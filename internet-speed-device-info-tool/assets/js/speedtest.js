class SpeedTest {
    constructor(options = {}) {
        this.ajaxUrl = options.ajaxUrl || '';
        this.nonce = options.nonce || '';
        this.config = options.config || {};

        this.downloadSize = parseFloat(this.config.downloadSize || 5);
        this.uploadSize = parseFloat(this.config.uploadSize || 3);
        this.testDuration = parseInt(this.config.testDuration || 10000, 10);
        this.iterations = Math.max(1, parseInt(this.config.speedIterations || 2, 10));
        this.ipLookupEndpoint = this.config.ipLookupEndpoint || '';

        this.downloadUrl = this.buildUrl('isdit_speed_test_download');
        this.uploadUrl = this.buildUrl('isdit_speed_test_upload');
        this.ipInfoUrl = this.buildUrl('isdit_get_ip_info');

        this.uploadPayload = this.generatePayload(this.uploadSize);
    }

    buildUrl(action) {
        if (!this.ajaxUrl) {
            return '';
        }
        const connector = this.ajaxUrl.indexOf('?') === -1 ? '?' : '&';
        return `${this.ajaxUrl}${connector}action=${action}&nonce=${encodeURIComponent(this.nonce)}`;
    }

    generatePayload(sizeMb) {
        const bytes = Math.max(1, Math.floor(sizeMb * 1024 * 1024));
        const payload = new Uint8Array(bytes);
        if (window.crypto && window.crypto.getRandomValues) {
            window.crypto.getRandomValues(payload);
        } else {
            for (let i = 0; i < payload.length; i++) {
                payload[i] = Math.floor(Math.random() * 256);
            }
        }
        return payload;
    }

    async testLatency(samples = 5) {
        const times = [];
        for (let i = 0; i < samples; i++) {
            const start = performance.now();
            try {
                await fetch(`${this.downloadUrl}&size=0.05&_=${Date.now()}_${i}`, { cache: 'no-store' });
                const end = performance.now();
                times.push(end - start);
            } catch (error) {
                console.error('Latency check failed', error);
            }
        }

        if (!times.length) {
            return { latency: 0, jitter: 0 };
        }

        const average = times.reduce((acc, time) => acc + time, 0) / times.length;
        let jitter = 0;
        if (times.length > 1) {
            const deltas = times.slice(1).map((value, index) => Math.abs(value - times[index]));
            jitter = deltas.reduce((acc, delta) => acc + delta, 0) / deltas.length;
        }

        return { latency: Math.round(average), jitter: Math.round(jitter) };
    }

    async testDownload(onProgress) {
        const results = [];
        const sizeParam = this.downloadSize;
        const bytesExpected = Math.max(1, Math.floor(sizeParam * 1024 * 1024));

        for (let i = 0; i < this.iterations; i++) {
            const start = performance.now();
            try {
                const response = await fetch(`${this.downloadUrl}&size=${encodeURIComponent(sizeParam)}&_=${Date.now()}_${i}`, {
                    cache: 'no-store'
                });
                const blob = await response.blob();
                const end = performance.now();

                const durationSeconds = (end - start) / 1000;
                const bytes = blob.size || bytesExpected;
                const speedMbps = durationSeconds > 0 ? (bytes * 8) / (durationSeconds * 1_000_000) : 0;
                results.push(speedMbps);
            } catch (error) {
                console.error('Download test error', error);
            }

            if (typeof onProgress === 'function') {
                onProgress(((i + 1) / this.iterations) * 100);
            }
        }

        return this.averageSpeed(results);
    }

    async testUpload(onProgress) {
        const results = [];
        const payload = this.uploadPayload;

        for (let i = 0; i < this.iterations; i++) {
            const start = performance.now();
            try {
                const response = await fetch(`${this.uploadUrl}&_=${Date.now()}_${i}`, {
                    method: 'POST',
                    body: payload,
                    cache: 'no-store',
                    headers: {
                        'Content-Type': 'application/octet-stream'
                    }
                });

                await response.json();
                const end = performance.now();

                const durationSeconds = (end - start) / 1000;
                const bytes = payload.byteLength;
                const speedMbps = durationSeconds > 0 ? (bytes * 8) / (durationSeconds * 1_000_000) : 0;
                results.push(speedMbps);
            } catch (error) {
                console.error('Upload test error', error);
            }

            if (typeof onProgress === 'function') {
                onProgress(((i + 1) / this.iterations) * 100);
            }
        }

        return this.averageSpeed(results);
    }

    averageSpeed(values) {
        const filtered = values.filter((value) => Number.isFinite(value) && value > 0);
        if (!filtered.length) {
            return 0;
        }
        const average = filtered.reduce((acc, value) => acc + value, 0) / filtered.length;
        return parseFloat(average.toFixed(2));
    }

    async getIPInfo() {
        try {
            const response = await fetch(this.ipInfoUrl, {
                method: 'POST',
                cache: 'no-store'
            });
            const data = await response.json();
            if (data && data.success) {
                return data.data;
            }
        } catch (error) {
            console.error('Server IP endpoint failed', error);
        }

        if (!this.ipLookupEndpoint) {
            return null;
        }

        try {
            const response = await fetch(this.ipLookupEndpoint, { cache: 'no-store' });
            if (!response.ok) {
                return null;
            }
            const data = await response.json();
            return {
                ip: data.ip || null,
                location: {
                    country: data.country_name || data.country || '',
                    region: data.region || data.region_name || '',
                    city: data.city || '',
                    latitude: data.latitude || data.lat || 0,
                    longitude: data.longitude || data.lon || 0,
                    timezone: data.timezone || '',
                    isp: data.org || data.isp || '',
                    organization: data.org || data.company || '',
                    postal: data.postal || data.zip || ''
                }
            };
        } catch (error) {
            console.error('Fallback IP lookup failed', error);
        }

        return null;
    }

    getDeviceInfo() {
        const info = {};
        const ua = navigator.userAgent || '';
        const uaData = navigator.userAgentData || null;

        if (uaData && uaData.mobile !== undefined) {
            info.type = uaData.mobile ? 'Mobile' : 'Desktop';
        } else if (/tablet/i.test(ua)) {
            info.type = 'Tablet';
        } else if (/mobile/i.test(ua)) {
            info.type = 'Mobile';
        } else {
            info.type = 'Desktop';
        }

        info.os = this.detectOS(ua);
        info.browser = this.detectBrowser(ua);
        info.screen = `${window.screen.width} x ${window.screen.height}`;
        info.viewport = `${window.innerWidth} x ${window.innerHeight}`;
        info.cpu = navigator.hardwareConcurrency || 'Unknown';
        info.memory = navigator.deviceMemory ? `${navigator.deviceMemory} GB` : 'Unknown';
        info.connection = this.detectConnection();
        info.userAgent = ua;

        if (uaData && Array.isArray(uaData.brands)) {
            info.browser = uaData.brands.map((brand) => `${brand.brand} ${brand.version}`).join(', ');
        }

        return info;
    }

    detectOS(ua) {
        if (ua.indexOf('Windows NT 10.0') !== -1) return 'Windows 10/11';
        if (ua.indexOf('Windows NT 6.3') !== -1) return 'Windows 8.1';
        if (ua.indexOf('Windows NT 6.2') !== -1) return 'Windows 8';
        if (ua.indexOf('Windows NT 6.1') !== -1) return 'Windows 7';
        if (/Mac OS X/.test(ua)) return 'macOS';
        if (/Android/.test(ua)) return 'Android';
        if (/iPhone/.test(ua)) return 'iOS (iPhone)';
        if (/iPad/.test(ua)) return 'iOS (iPad)';
        if (/Linux/.test(ua)) return 'Linux';
        return 'Unknown';
    }

    detectBrowser(ua) {
        if (/Edg\//.test(ua)) return 'Microsoft Edge';
        if (/OPR\//.test(ua)) return 'Opera';
        if (/Chrome\//.test(ua) && /Safari\//.test(ua)) return 'Google Chrome';
        if (/Safari\//.test(ua) && !/Chrome\//.test(ua)) return 'Safari';
        if (/Firefox\//.test(ua)) return 'Mozilla Firefox';
        if (/MSIE|Trident/.test(ua)) return 'Internet Explorer';
        return 'Unknown';
    }

    detectConnection() {
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (!connection) {
            return 'Unknown';
        }
        const parts = [];
        if (connection.effectiveType) {
            parts.push(connection.effectiveType.toUpperCase());
        }
        if (connection.downlink) {
            parts.push(`${connection.downlink} Mbps`);
        }
        if (connection.rtt) {
            parts.push(`${connection.rtt} ms RTT`);
        }
        return parts.length ? parts.join(' · ') : 'Unknown';
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = SpeedTest;
}

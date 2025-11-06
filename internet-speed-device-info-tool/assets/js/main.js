(function($) {
    'use strict';

    let testRunning = false;

    function initializeMap(container, lat, lng, config) {
        if (!container || typeof L === 'undefined') {
            console.warn('Leaflet is not loaded or container is missing.');
            return;
        }

        try {
            const mapId = container.id || 'isdit-map-' + Date.now();
            container.id = mapId;

            const mapInst = L.map(mapId, {
                center: [lat, lng],
                zoom: 12,
                zoomControl: true,
                scrollWheelZoom: false,
                attributionControl: true
            });

            const tileUrl = (config && config.mapTileUrl) || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const attribution = (config && config.mapAttribution) || '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors';

            L.tileLayer(tileUrl, {
                attribution: attribution,
                maxZoom: 19
            }).addTo(mapInst);

            L.marker([lat, lng]).addTo(mapInst);
        } catch (error) {
            console.error('Map initialization failed', error);
        }
    }

    function formatNumber(value, decimals = 2) {
        if (!value || isNaN(value)) {
            return '--';
        }
        return parseFloat(value).toFixed(decimals);
    }

    function updateText(element, text) {
        if (element) {
            element.textContent = text || '--';
        }
    }

    function updateProgressBar(bar, percent) {
        if (bar) {
            bar.style.width = Math.min(100, Math.max(0, percent)) + '%';
        }
    }

    function updateDeviceInfo(container, info) {
        if (!container || !info) return;
        updateText(container.querySelector('[data-device-type]'), info.type);
        updateText(container.querySelector('[data-os]'), info.os);
        updateText(container.querySelector('[data-browser]'), info.browser);
        updateText(container.querySelector('[data-screen]'), info.screen);
        updateText(container.querySelector('[data-viewport]'), info.viewport);
        updateText(container.querySelector('[data-cpu]'), info.cpu);
        updateText(container.querySelector('[data-memory]'), info.memory);
        updateText(container.querySelector('[data-connection]'), info.connection);
        const uaEl = container.querySelector('[data-useragent]');
        if (uaEl) {
            uaEl.textContent = info.userAgent || '--';
        }
    }

    function updateIPInfo(container, ipData) {
        if (!container || !ipData) return;
        updateText(container.querySelector('[data-ip-address]'), ipData.ip);
        if (ipData.location) {
            const loc = ipData.location;
            updateText(container.querySelector('[data-country]'), loc.country);
            updateText(container.querySelector('[data-region]'), loc.region);
            updateText(container.querySelector('[data-city]'), loc.city);
            updateText(container.querySelector('[data-isp]'), loc.isp);
            updateText(container.querySelector('[data-organization]'), loc.organization);
            updateText(container.querySelector('[data-timezone]'), loc.timezone);
        }
    }

    async function runSpeedTest(wrapper, speedTest, config) {
        if (testRunning) {
            return;
        }

        testRunning = true;

        const button = wrapper.querySelector('[data-speed-test-start]');
        const status = wrapper.querySelector('.isdit-status');
        const progressBar = wrapper.querySelector('[data-progress-bar]');

        const downloadEl = wrapper.querySelector('[data-download]');
        const uploadEl = wrapper.querySelector('[data-upload]');
        const latencyEl = wrapper.querySelector('[data-latency]');
        const jitterEl = wrapper.querySelector('[data-jitter]');

        if (button) button.disabled = true;
        updateText(status, 'Preparing test...');
        updateProgressBar(progressBar, 0);

        updateText(downloadEl, '--');
        updateText(uploadEl, '--');
        updateText(latencyEl, '--');
        if (jitterEl) updateText(jitterEl, '--');

        try {
            updateText(status, 'Measuring latency & jitter...');
            const latencyResult = await speedTest.testLatency(5);
            updateText(latencyEl, latencyResult.latency || 0);
            if (jitterEl) updateText(jitterEl, latencyResult.jitter || 0);
            updateProgressBar(progressBar, 10);

            updateText(status, 'Running download test...');
            const downloadSpeed = await speedTest.testDownload((progress) => {
                updateProgressBar(progressBar, 10 + (progress / 100) * 40);
            });
            updateText(downloadEl, formatNumber(downloadSpeed));
            updateProgressBar(progressBar, 50);

            updateText(status, 'Running upload test...');
            const uploadSpeed = await speedTest.testUpload((progress) => {
                updateProgressBar(progressBar, 50 + (progress / 100) * 40);
            });
            updateText(uploadEl, formatNumber(uploadSpeed));
            updateProgressBar(progressBar, 100);

            updateText(status, 'Test complete! All measurements are averaged for accuracy.');
        } catch (error) {
            console.error('Speed test error', error);
            updateText(status, 'An error occurred during the test. Please try again.');
        } finally {
            if (button) button.disabled = false;
            testRunning = false;
        }
    }

    function initializeWidget(wrapper, index) {
        const config = (typeof isdtAjax !== 'undefined' && isdtAjax.config) ? isdtAjax.config : {};
        const speedTest = new SpeedTest({
            ajaxUrl: (typeof isdtAjax !== 'undefined') ? isdtAjax.ajaxUrl : '',
            nonce: (typeof isdtAjax !== 'undefined') ? isdtAjax.nonce : '',
            config: config
        });

        const content = wrapper.querySelector('.isdit-content');
        const showInfo = content && content.dataset.showInfo === '1';
        const button = wrapper.querySelector('[data-speed-test-start]');

        if (button) {
            button.addEventListener('click', function() {
                runSpeedTest(wrapper, speedTest, config);
            });
        }

        if (showInfo) {
            const deviceCard = wrapper.querySelector('.device-info');
            if (deviceCard) {
                const deviceInfo = speedTest.getDeviceInfo();
                updateDeviceInfo(deviceCard, deviceInfo);
            }

            speedTest.getIPInfo().then((ipData) => {
                if (!ipData) {
                    console.warn('Could not retrieve IP information');
                    return;
                }

                const ipCard = wrapper.querySelector('.ip-info');
                if (ipCard) {
                    updateIPInfo(ipCard, ipData);
                }

                const location = ipData.location;
                if (location && location.latitude && location.longitude) {
                    const mapContainer = wrapper.querySelector('[data-map]');
                    if (mapContainer) {
                        initializeMap(mapContainer, location.latitude, location.longitude, config);
                    }
                }
            }).catch((error) => {
                console.error('Failed to retrieve IP info', error);
            });
        }
    }

    function initialize() {
        if (typeof SpeedTest === 'undefined') {
            console.error('SpeedTest class is not defined.');
            return;
        }

        const wrappers = document.querySelectorAll('.isdit-wrapper');
        if (!wrappers.length) {
            return;
        }

        wrappers.forEach((wrapper, index) => {
            initializeWidget(wrapper, index);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

})(jQuery);

/**
 * Cloud Gaming Speed Test - Frontend JavaScript
 * Integrates with LibreSpeed backends, renders animated UI, and
 * provides cloud gaming suitability assessments in real time.
 *
 * @package CloudGamingSpeedTest
 */

(function($) {
    'use strict';

    class CloudGamingSpeedTest {
        constructor() {
            this.servers = Array.isArray(cgstData.servers) ? cgstData.servers : [];
            this.thresholds = cgstData.thresholds || {};
            this.i18n = cgstData.i18n || {};
            this.tips = cgstData.tips || {};

            this.mode = 'auto';
            this.selectedServer = null;
            this.activeServer = null;
            this.testInProgress = false;

            this.results = {
                download: 0,
                upload: 0,
                ping: 0,
                jitter: 0,
                packetLoss: 0
            };

            this.latencySamples = [];
            this.latencyFailures = 0;

            // Cache selectors
            this.$serverSelect = $('#cgst-server-select');
            this.$startButton = $('#cgst-start-test');
            this.$progressContainer = $('#cgst-progress-container');
            this.$resultsContainer = $('#cgst-results-container');
            this.$status = $('#cgst-status-message');
            this.$testedServer = $('#cgst-tested-server');
            this.$tipsWrapper = $('#cgst-tips-wrapper');
            this.$tipsList = $('#cgst-tips-list');

            this.init();
        }

        init() {
            this.populateServerDropdown();
            this.bindEvents();
            this.showInitialState();
        }

        bindEvents() {
            $('.cgst-btn-auto').on('click', () => this.setMode('auto'));
            $('.cgst-btn-manual').on('click', () => this.setMode('manual'));

            this.$serverSelect.on('change', () => {
                const serverId = this.$serverSelect.val();
                this.selectedServer = this.servers.find((server) => server.id === serverId) || null;
            });

            this.$startButton.on('click', () => this.handleStart());
            $('#cgst-retest').on('click', () => this.resetTest());
        }

        setMode(mode) {
            this.mode = mode;
            if (mode === 'auto') {
                $('.cgst-btn-auto').addClass('active');
                $('.cgst-btn-manual').removeClass('active');
                $('.cgst-server-dropdown').slideUp();
            } else {
                $('.cgst-btn-manual').addClass('active');
                $('.cgst-btn-auto').removeClass('active');
                $('.cgst-server-dropdown').slideDown();
            }
        }

        populateServerDropdown() {
            const placeholder = this.i18n.selectServer || 'Select a server...';
            this.$serverSelect.empty().append(`<option value="">${placeholder}</option>`);

            this.servers.forEach((server) => {
                const optionText = `${server.name} - ${server.location}`;
                this.$serverSelect.append(`<option value="${server.id}">${optionText}</option>`);
            });
        }

        showInitialState() {
            this.$resultsContainer.hide();
            this.$progressContainer.hide();
            this.$startButton.show().prop('disabled', false);
            this.updateStatus(this.i18n.initializing || 'Initializing test...');
        }

        resetResults() {
            this.results = {
                download: 0,
                upload: 0,
                ping: 0,
                jitter: 0,
                packetLoss: 0
            };
            this.latencySamples = [];
            this.latencyFailures = 0;
        }

        resetProgressBars() {
            $('#cgst-progress-ping, #cgst-progress-download, #cgst-progress-upload').css('width', '0%');
        }

        updateProgress(stage, percent) {
            const clamped = Math.max(0, Math.min(percent, 100));
            if (stage === 'ping') {
                $('#cgst-progress-ping').css('width', `${clamped}%`);
            } else if (stage === 'download') {
                $('#cgst-progress-download').css('width', `${clamped}%`);
            } else if (stage === 'upload') {
                $('#cgst-progress-upload').css('width', `${clamped}%`);
            }
        }

        updateStatus(message) {
            if (!this.$status) {
                return;
            }
            this.$status.text(message);
        }

        async handleStart() {
            if (this.testInProgress) {
                return;
            }

            if (this.mode === 'manual' && !this.selectedServer) {
                window.alert(this.i18n.pleaseSelectServer || 'Please select a server first.');
                return;
            }

            try {
                this.testInProgress = true;
                this.resetResults();
                this.resetProgressBars();
                this.$tipsWrapper.hide();
                this.$testedServer.hide();

                this.$startButton.prop('disabled', true).fadeOut(200, () => {
                    this.$progressContainer.fadeIn(200);
                });

                const server = (this.mode === 'auto')
                    ? await this.findFastestServer()
                    : this.selectedServer;

                if (!server) {
                    throw new Error(this.i18n.errorNoServer || 'No server available for testing. Please configure server presets.');
                }

                this.activeServer = server;

                await this.testLatency(server);
                await this.testDownload(server);
                await this.testUpload(server);

                const ratingData = this.calculateRating();
                this.displayResults(server, ratingData);
                this.saveResults(server, ratingData);

                this.updateStatus(this.i18n.complete || 'Test complete!');
            } catch (error) {
                this.handleError(error.message || error.toString(), error);
            } finally {
                this.testInProgress = false;
                this.$startButton.prop('disabled', false);
            }
        }

        async findFastestServer() {
            if (!this.servers.length) {
                return null;
            }

            this.updateStatus(this.i18n.findingFastest || 'Finding the fastest server...');
            this.updateProgress('ping', 10);

            const pingResults = [];

            for (const server of this.servers) {
                const latencies = [];
                for (let i = 0; i < 3; i++) {
                    const response = await this.measurePing(server.pingUrl, 4000);
                    if (response.success) {
                        latencies.push(response.latency);
                    }
                }

                const avgLatency = latencies.length ? this.average(latencies) : Infinity;
                pingResults.push({ server, latency: avgLatency });
            }

            pingResults.sort((a, b) => a.latency - b.latency);
            this.updateProgress('ping', 100);

            return pingResults.length && pingResults[0].latency !== Infinity
                ? pingResults[0].server
                : null;
        }

        async testLatency(server) {
            this.updateStatus(this.i18n.testingLatency || 'Testing latency and jitter...');

            const attempts = 10;
            this.latencySamples = [];
            this.latencyFailures = 0;

            for (let i = 0; i < attempts; i++) {
                const response = await this.measurePing(server.pingUrl, 5000);
                if (response.success) {
                    this.latencySamples.push(response.latency);
                } else {
                    this.latencyFailures++;
                }

                this.updateProgress('ping', ((i + 1) / attempts) * 100);
                await this.wait(120);
            }

            if (this.latencySamples.length) {
                this.results.ping = this.average(this.latencySamples);
                this.results.jitter = this.calculateJitter(this.latencySamples);
            } else {
                this.results.ping = 0;
                this.results.jitter = 0;
            }

            this.results.packetLoss = 1 - (this.latencySamples.length / attempts);

            this.updateMetric('ping', this.results.ping);
            this.updateMetric('jitter', this.results.jitter);
            this.updateMetric('packet', this.results.packetLoss);
        }

        async measurePing(url, timeout) {
            try {
                const pingUrl = this.appendParams(url, {
                    cors: 'true',
                    ts: Date.now()
                });

                const start = performance.now();
                const response = await this.fetchWithTimeout(pingUrl, {
                    method: 'GET',
                    cache: 'no-store',
                    credentials: 'omit'
                }, timeout);

                if (!response.ok) {
                    throw new Error('Ping failed');
                }

                await response.text();
                const latency = performance.now() - start;
                return { success: true, latency };
            } catch (error) {
                return { success: false, latency: Infinity };
            }
        }

        async testDownload(server) {
            this.updateStatus(this.i18n.testingDownload || 'Testing download speed...');

            const attempts = 4;
            const chunkKB = 1024 * 4; // 4 MB per chunk
            let totalBytes = 0;
            const startTime = performance.now();

            for (let i = 0; i < attempts; i++) {
                try {
                    const downloadUrl = this.appendParams(server.downloadUrl, {
                        ckSize: chunkKB,
                        cors: 'true',
                        ts: `${Date.now()}-${i}`
                    });

                    const response = await this.fetchWithTimeout(downloadUrl, {
                        method: 'GET',
                        cache: 'no-store',
                        credentials: 'omit'
                    }, 10000);

                    if (!response.ok) {
                        throw new Error('Download chunk failed');
                    }

                    const buffer = await response.arrayBuffer();
                    totalBytes += buffer.byteLength;

                    const elapsedSeconds = Math.max(0.001, (performance.now() - startTime) / 1000);
                    this.results.download = (totalBytes * 8) / (elapsedSeconds * 1e6);
                    this.updateMetric('download', this.results.download);
                } catch (error) {
                    console.warn('Download attempt failed', error);
                }

                this.updateProgress('download', ((i + 1) / attempts) * 100);
            }
        }

        async testUpload(server) {
            this.updateStatus(this.i18n.testingUpload || 'Testing upload speed...');

            const attempts = 4;
            const chunkBytes = 1024 * 1024 * 2; // 2 MB per chunk
            let totalBytes = 0;
            const startTime = performance.now();

            for (let i = 0; i < attempts; i++) {
                try {
                    const uploadUrl = this.appendParams(server.uploadUrl, {
                        cors: 'true',
                        ts: `${Date.now()}-${i}`
                    });

                    const payload = this.generatePayload(chunkBytes);

                    const response = await this.fetchWithTimeout(uploadUrl, {
                        method: 'POST',
                        cache: 'no-store',
                        credentials: 'omit',
                        body: payload,
                        headers: {
                            'Content-Type': 'application/octet-stream'
                        }
                    }, 10000);

                    if (!response.ok) {
                        throw new Error('Upload chunk failed');
                    }

                    totalBytes += chunkBytes;
                    const elapsedSeconds = Math.max(0.001, (performance.now() - startTime) / 1000);
                    this.results.upload = (totalBytes * 8) / (elapsedSeconds * 1e6);
                    this.updateMetric('upload', this.results.upload);
                } catch (error) {
                    console.warn('Upload attempt failed', error);
                }

                this.updateProgress('upload', ((i + 1) / attempts) * 100);
            }
        }

        generatePayload(size) {
            const buffer = new Uint8Array(size);
            if (window.crypto && window.crypto.getRandomValues) {
                window.crypto.getRandomValues(buffer);
            } else {
                for (let i = 0; i < size; i++) {
                    buffer[i] = Math.floor(Math.random() * 256);
                }
            }
            return buffer;
        }

        appendParams(url, params) {
            const separator = url.indexOf('?') === -1 ? '?' : '&';
            const query = Object.keys(params)
                .map((key) => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
                .join('&');
            return `${url}${separator}${query}`;
        }

        async fetchWithTimeout(url, options, timeout = 8000) {
            return Promise.race([
                fetch(url, options),
                new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), timeout))
            ]);
        }

        average(values) {
            if (!values.length) {
                return 0;
            }
            const total = values.reduce((sum, value) => sum + value, 0);
            return total / values.length;
        }

        calculateJitter(samples) {
            if (samples.length < 2) {
                return 0;
            }
            const diffs = [];
            for (let i = 1; i < samples.length; i++) {
                diffs.push(Math.abs(samples[i] - samples[i - 1]));
            }
            return this.average(diffs);
        }

        updateMetric(metric, value) {
            if (Number.isNaN(value)) {
                return;
            }

            if (metric === 'download') {
                $('#cgst-result-download').text(value > 0 ? value.toFixed(2) : '--');
            } else if (metric === 'upload') {
                $('#cgst-result-upload').text(value > 0 ? value.toFixed(2) : '--');
            } else if (metric === 'ping') {
                $('#cgst-result-ping').text(value > 0 ? value.toFixed(0) : '--');
            } else if (metric === 'jitter') {
                $('#cgst-result-jitter').text(value > 0 ? value.toFixed(2) : '--');
            } else if (metric === 'packet') {
                $('#cgst-result-packet').text((value * 100).toFixed(2));
            }
        }

        calculateRating() {
            const defaults = {
                excellent: { download: 150, upload: 25, ping: 20, jitter: 5, packet_loss: 0.1 },
                good: { download: 90, upload: 15, ping: 35, jitter: 8, packet_loss: 0.3 },
                fair: { download: 45, upload: 8, ping: 55, jitter: 12, packet_loss: 0.8 }
            };

            const excellent = Object.assign({}, defaults.excellent, this.thresholds.excellent || {});
            const good = Object.assign({}, defaults.good, this.thresholds.good || {});
            const fair = Object.assign({}, defaults.fair, this.thresholds.fair || {});

            const download = this.results.download;
            const upload = this.results.upload;
            const ping = this.results.ping;
            const jitter = this.results.jitter;
            const packetLoss = this.results.packetLoss;

            let rating = 'Poor';
            let recommendation = '720p @ 60fps – lower bitrate and enable performance mode.';
            let icon = '❌';
            let className = 'rating-poor';

            if (
                download >= excellent.download &&
                upload >= excellent.upload &&
                ping <= excellent.ping &&
                jitter <= excellent.jitter &&
                packetLoss <= excellent.packet_loss
            ) {
                rating = 'Excellent';
                recommendation = '4K / 120fps – perfect for GeForce NOW Ultimate & Xbox Cloud Gaming Performance preset.';
                icon = '🏆';
                className = 'rating-excellent';
            } else if (
                download >= good.download &&
                upload >= good.upload &&
                ping <= good.ping &&
                jitter <= good.jitter &&
                packetLoss <= good.packet_loss
            ) {
                rating = 'Good';
                recommendation = '1440p / 60fps – enable VRR and keep bitrate under 45 Mbps.';
                icon = '✅';
                className = 'rating-good';
            } else if (
                download >= fair.download &&
                upload >= fair.upload &&
                ping <= fair.ping &&
                jitter <= fair.jitter &&
                packetLoss <= fair.packet_loss
            ) {
                rating = 'Fair';
                recommendation = '1080p / 60fps – reduce graphics streaming quality to balanced.';
                icon = '⚠️';
                className = 'rating-fair';
            }

            return { rating, recommendation, icon, className };
        }

        displayResults(server, ratingData) {
            this.$progressContainer.fadeOut(300, () => {
                $('#cgst-tested-server-name').text(`${server.name} (${server.location})`);
                this.$testedServer.fadeIn(200);

                this.updateMetric('download', this.results.download);
                this.updateMetric('upload', this.results.upload);
                this.updateMetric('ping', this.results.ping);
                this.updateMetric('jitter', this.results.jitter);
                this.updateMetric('packet', this.results.packetLoss);

                this.applyRating(ratingData);
                this.displayTips(ratingData.rating);

                this.$resultsContainer.fadeIn(300);
            });
        }

        applyRating(ratingData) {
            $('#cgst-rating-text').text(ratingData.rating);
            $('#cgst-rating-icon').text(ratingData.icon);
            $('#cgst-recommendation').text(ratingData.recommendation);

            const $badge = $('#cgst-rating-badge');
            $badge.removeClass('rating-excellent rating-good rating-fair rating-poor');
            $badge.addClass(ratingData.className);
        }

        displayTips(ratingLabel) {
            const tips = this.tips[ratingLabel] || [];
            if (!tips.length) {
                this.$tipsWrapper.hide();
                return;
            }

            this.$tipsList.empty();
            tips.forEach((tip) => {
                this.$tipsList.append(`<li>${tip}</li>`);
            });

            this.$tipsWrapper.fadeIn(200);
        }

        saveResults(server, ratingData) {
            $.ajax({
                url: cgstData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cgst_save_result',
                    nonce: cgstData.nonce,
                    server_id: server.id,
                    server_name: `${server.name} (${server.location})`,
                    download_mbps: this.results.download,
                    upload_mbps: this.results.upload,
                    ping_ms: this.results.ping,
                    jitter_ms: this.results.jitter,
                    packet_loss: this.results.packetLoss,
                    rating: ratingData.rating,
                    recommendation: ratingData.recommendation,
                    settings: {
                        icon: ratingData.icon,
                        cssClass: ratingData.className
                    }
                }
            });
        }

        resetTest() {
            this.resetResults();
            this.resetProgressBars();
            this.$resultsContainer.fadeOut(200, () => {
                this.$tipsWrapper.hide();
                this.$testedServer.hide();
                this.$startButton.fadeIn(200);
            });
        }

        handleError(message, error) {
            console.error('Cloud Gaming Speed Test error:', error);
            this.updateStatus(message);
            window.alert(message);
            this.resetTest();
        }

        wait(duration) {
            return new Promise((resolve) => setTimeout(resolve, duration));
        }
    }

    $(document).ready(() => {
        if ($('.cgst-wrapper').length) {
            new CloudGamingSpeedTest();
        }
    });

})(jQuery);

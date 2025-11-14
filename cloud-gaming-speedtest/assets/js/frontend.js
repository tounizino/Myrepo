(function($) {
    'use strict';

    class CloudGamingSpeedTest {
        constructor(container) {
            this.$container = $(container);
            this.theme = this.$container.data('theme');
            this.isRunning = false;
            this.results = {};
            this.sessionStartTime = null;
            
            this.initElements();
            this.attachEvents();
            this.getUserInfo();
        }

        initElements() {
            this.$startButton = this.$container.find('[data-cgst-action="start-test"]');
            this.$speedValue = this.$container.find('[data-cgst-speed-value]');
            this.$gaugeLabel = this.$container.find('[data-cgst-gauge-label]');
            this.$gaugeFill = this.$container.find('[data-cgst-gauge-fill]');
            this.$gaugePointer = this.$container.find('[data-cgst-gauge-pointer]');
            this.$progressFill = this.$container.find('[data-cgst-progress-fill]');
            this.$downloadValue = this.$container.find('[data-cgst-download]');
            this.$uploadValue = this.$container.find('[data-cgst-upload]');
            this.$latencyValue = this.$container.find('[data-cgst-latency]');
            this.$jitterValue = this.$container.find('[data-cgst-jitter]');
            this.$results = this.$container.find('[data-cgst-results]');
            this.$statusBanner = this.$container.find('[data-cgst-status-banner]');
            this.$statusIcon = this.$container.find('[data-cgst-status-icon]');
            this.$statusHeadline = this.$container.find('[data-cgst-status-headline]');
            this.$statusDescription = this.$container.find('[data-cgst-status-description]');
            this.$recommendations = this.$container.find('[data-cgst-recommendations]');
            this.$statusPill = this.$container.find('[data-cgst-status-pill]');
            this.$sessionTime = this.$container.find('[data-cgst-session-time]');
            this.$ip = this.$container.find('[data-cgst-ip]');
            this.$location = this.$container.find('[data-cgst-location]');
            this.$isp = this.$container.find('[data-cgst-isp]');
        }

        attachEvents() {
            this.$startButton.on('click', () => this.startTest());
        }

        async getUserInfo() {
            try {
                const response = await $.ajax({
                    url: cgstAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cgst_get_user_info',
                        nonce: cgstAjax.nonce
                    }
                });

                if (response.success) {
                    this.$ip.text(response.data.ip);
                    this.$location.text(response.data.city + ', ' + response.data.country);
                    this.$isp.text(response.data.isp);
                }
            } catch (error) {
                console.warn('Failed to get user info:', error);
            }
        }

        async startTest() {
            if (this.isRunning) return;

            this.isRunning = true;
            this.results = {};
            this.sessionStartTime = Date.now();
            this.$startButton.prop('disabled', true).addClass('cgst-loading');
            this.$startButton.find('.cgst-start-text').text('Running Tests...');
            this.$statusPill.text('Test in Progress');
            this.$results.removeAttr('hidden');
            this.$container.removeClass('cgst-status-excellent cgst-status-good cgst-status-fair cgst-status-poor cgst-status-bad');

            this.startSessionTimer();

            try {
                await this.runLatencyTest();
                await this.runDownloadTest();
                await this.runUploadTest();
                await this.runAnalysis();

                this.$statusPill.text('Test Completed');
                this.$startButton.find('.cgst-start-text').text('Run Test Again');
            } catch (error) {
                console.error('Speed test error:', error);
                alert(error && error.message ? error.message : 'Test failed. Please try again.');
                this.$statusPill.text('Test Failed');
                this.$startButton.find('.cgst-start-text').text('Retry Test');
            } finally {
                this.isRunning = false;
                this.$startButton.prop('disabled', false).removeClass('cgst-loading');
            }
        }

        startSessionTimer() {
            if (this.sessionInterval) {
                clearInterval(this.sessionInterval);
            }

            this.sessionInterval = setInterval(() => {
                const elapsed = Math.floor((Date.now() - this.sessionStartTime) / 1000);
                const mins = Math.floor(elapsed / 60);
                const secs = elapsed % 60;
                this.$sessionTime.text(`Session: ${mins}m ${secs}s`);
            }, 1000);
        }

        async runLatencyTest() {
            this.activateStep('latency');
            this.$gaugeLabel.text('Latency');
            this.$progressFill.css('width', '25%');

            const response = await $.ajax({
                url: cgstAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cgst_test_latency',
                    nonce: cgstAjax.nonce
                }
            });

            if (!response.success) {
                throw new Error(response.data && response.data.message ? response.data.message : 'Latency test failed.');
            }

            this.results.latency = response.data.latency;
            this.results.jitter = response.data.jitter;
            
            this.$latencyValue.text(Math.round(response.data.latency));
            this.$jitterValue.text(response.data.jitter.toFixed(1));
            
            const latencyScaled = Math.min(100, response.data.latency);
            await this.animateGauge(latencyScaled);
        }

        async runDownloadTest() {
            this.activateStep('download');
            this.$gaugeLabel.text('Download');
            this.$progressFill.css('width', '50%');

            const response = await $.ajax({
                url: cgstAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cgst_test_download',
                    nonce: cgstAjax.nonce
                }
            });

            if (!response.success) {
                throw new Error(response.data && response.data.message ? response.data.message : 'Download test failed.');
            }

            this.results.download = response.data.download;
            this.$downloadValue.text(response.data.download.toFixed(1));
            await this.animateGauge(Math.min(150, response.data.download));
        }

        async runUploadTest() {
            this.activateStep('upload');
            this.$gaugeLabel.text('Upload');
            this.$progressFill.css('width', '75%');

            const response = await $.ajax({
                url: cgstAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cgst_test_upload',
                    nonce: cgstAjax.nonce
                }
            });

            if (!response.success) {
                throw new Error(response.data && response.data.message ? response.data.message : 'Upload test failed.');
            }

            this.results.upload = response.data.upload;
            this.$uploadValue.text(response.data.upload.toFixed(1));
            await this.animateGauge(Math.min(100, response.data.upload));
        }

        async runAnalysis() {
            this.activateStep('analysis');
            this.$gaugeLabel.text('Analyzing');
            this.$progressFill.css('width', '100%');

            const response = await $.ajax({
                url: cgstAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cgst_analyze_results',
                    nonce: cgstAjax.nonce,
                    download: this.results.download,
                    upload: this.results.upload,
                    latency: this.results.latency,
                    jitter: this.results.jitter
                }
            });

            if (!response.success) {
                throw new Error(response.data && response.data.message ? response.data.message : 'Analysis failed.');
            }

            const analysis = response.data;
            
            this.$statusIcon.text(analysis.icon);
            this.$statusHeadline.text(analysis.headline);
            this.$statusDescription.text(analysis.label + ' performance tier detected');
            this.$container.addClass('cgst-status-' + analysis.class);
            
            this.$recommendations.empty();
            analysis.recommendations.forEach(rec => {
                this.$recommendations.append(`<li>${rec}</li>`);
            });
            
            if (analysis.warnings && analysis.warnings.length > 0) {
                analysis.warnings.forEach(warning => {
                    this.$recommendations.append(`<li>${warning}</li>`);
                });
            }
        }

        activateStep(step) {
            this.$container.find('.cgst-progress-steps span').removeClass('is-active');
            this.$container.find(`.cgst-progress-steps span[data-step="${step}"]`).addClass('is-active');
        }

        async animateGauge(value) {
            return new Promise(resolve => {
                const steps = 20;
                const increment = value / steps;
                const delay = 50;
                let current = 0;
                let step = 0;

                const interval = setInterval(() => {
                    current += increment;
                    step++;
                    
                    this.$speedValue.text(Math.round(current));
                    this.$gaugeFill.css('height', (Math.min(150, current) / 150 * 100) + '%');
                    this.$gaugePointer.css('transform', `translate(-50%, -100%) rotate(${Math.min(150, current) / 150 * 180}deg)`);

                    if (step >= steps) {
                        clearInterval(interval);
                        this.$speedValue.text(Math.round(value));
                        this.$gaugeFill.css('height', (Math.min(150, value) / 150 * 100) + '%');
                        this.$gaugePointer.css('transform', `translate(-50%, -100%) rotate(${Math.min(150, value) / 150 * 180}deg)`);
                        resolve();
                    }
                }, delay);
            });
        }
    }

    $(document).ready(function() {
        $('[data-cgst-component]').each(function() {
            new CloudGamingSpeedTest(this);
        });
    });

})(jQuery);

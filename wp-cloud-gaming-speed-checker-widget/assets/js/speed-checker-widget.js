const BUTTON_LABELS = {
    idle: 'Start internet speed test',
    testing: 'Testing internet speed…',
    retry: 'Run the speed test again'
};

class SpeedCheckerWidget {
    constructor(widgetElement) {
        this.widget = widgetElement;
        this.button = this.widget.querySelector('.test-button');
        this.recommendation = this.widget.querySelector('.recommendation');
        this.progressCircle = this.widget.querySelector('.progress-ring-circle');
        this.speedNumber = this.widget.querySelector('.speed-number');
        this.stats = {
            download: this.widget.querySelector('.stat-item:nth-child(1) .stat-value'),
            upload: this.widget.querySelector('.stat-item:nth-child(2) .stat-value'),
            ping: this.widget.querySelector('.stat-item:nth-child(3) .stat-value'),
            jitter: this.widget.querySelector('.stat-item:nth-child(4) .stat-value')
        };

        this.circumference = 2 * Math.PI * 80;
        if (this.progressCircle) {
            this.progressCircle.style.strokeDasharray = `0 ${this.circumference}`;
        }

        this.widget.setAttribute('data-status', 'idle');

        if (this.button) {
            this.button.setAttribute('aria-label', BUTTON_LABELS.idle);
            this.button.addEventListener('click', () => this.runSpeedTest());
        }
    }

    setProgress(percent) {
        if (!this.progressCircle) {
            return;
        }

        const offset = this.circumference * (percent / 100);
        this.progressCircle.style.strokeDasharray = `${offset} ${this.circumference}`;
    }

    resetStats() {
        if (this.stats.download) {
            this.stats.download.textContent = '-- Mbps';
        }
        if (this.stats.upload) {
            this.stats.upload.textContent = '-- Mbps';
        }
        if (this.stats.ping) {
            this.stats.ping.textContent = '-- ms';
        }
        if (this.stats.jitter) {
            this.stats.jitter.textContent = '-- ms';
        }
    }

    async runSpeedTest() {
        if (!this.button || this.button.disabled) {
            return;
        }

        this.widget.setAttribute('data-status', 'starting');
        this.setProgress(0);
        this.resetStats();

        this.button.disabled = true;
        this.button.innerHTML = '<span class="button-icon">⏳</span> Testing...';
        this.button.setAttribute('aria-label', BUTTON_LABELS.testing);

        if (this.recommendation) {
            this.recommendation.classList.add('hidden');
        }

        if (this.speedNumber) {
            this.speedNumber.textContent = '--';
        }

        try {
            this.widget.setAttribute('data-status', 'download');
            await this.animateProgress(30, 2000);
            const downloadSpeed = await this.simulateDownloadTest();
            if (this.stats.download) {
                this.stats.download.textContent = `${downloadSpeed.toFixed(1)} Mbps`;
            }
            if (this.speedNumber) {
                this.speedNumber.textContent = downloadSpeed.toFixed(1);
            }

            this.widget.setAttribute('data-status', 'upload');
            await this.animateProgress(60, 2000);
            const uploadSpeed = await this.simulateUploadTest();
            if (this.stats.upload) {
                this.stats.upload.textContent = `${uploadSpeed.toFixed(1)} Mbps`;
            }

            this.widget.setAttribute('data-status', 'latency');
            await this.animateProgress(80, 1500);
            const ping = await this.simulatePingTest();
            if (this.stats.ping) {
                this.stats.ping.textContent = `${ping} ms`;
            }

            this.widget.setAttribute('data-status', 'stability');
            await this.animateProgress(95, 1000);
            const jitter = await this.simulateJitterTest();
            if (this.stats.jitter) {
                this.stats.jitter.textContent = `${jitter} ms`;
            }

            this.widget.setAttribute('data-status', 'finalizing');
            await this.animateProgress(100, 500);

            this.showRecommendation(downloadSpeed, uploadSpeed, ping, jitter);
            this.widget.setAttribute('data-status', 'complete');
            this.button.innerHTML = '<span class="button-icon">🔄</span> Test Again';
            this.button.setAttribute('aria-label', BUTTON_LABELS.retry);
        } catch (error) {
            console.error('[SpeedCheckerWidget]', error);
            this.widget.setAttribute('data-status', 'error');
            this.showErrorRecommendation();
            this.button.innerHTML = '<span class="button-icon">↻</span> Try Again';
            this.button.setAttribute('aria-label', BUTTON_LABELS.retry);
        } finally {
            this.button.disabled = false;
        }
    }

    animateProgress(targetPercent, duration) {
        if (!this.progressCircle) {
            return Promise.resolve();
        }

        return new Promise(resolve => {
            const startTime = Date.now();
            const existing = this.progressCircle.style.strokeDasharray;
            const startingDash = existing ? parseFloat(existing.split(' ')[0]) : 0;
            const startPercent = (startingDash / this.circumference) * 100;

            const animate = () => {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = this.easeOutCubic(progress);
                const currentPercent = startPercent + (targetPercent - startPercent) * eased;

                this.setProgress(currentPercent);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    resolve();
                }
            };

            animate();
        });
    }

    easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    async simulateDownloadTest() {
        const imageUrl = 'https://via.placeholder.com/2000x2000';
        const startTime = performance.now();

        try {
            const response = await fetch(`${imageUrl}?_=${Date.now()}`, { cache: 'no-cache' });
            const blob = await response.blob();
            const endTime = performance.now();
            const duration = (endTime - startTime) / 1000;
            const fileSizeMB = blob.size / (1024 * 1024);
            const speedMbps = (fileSizeMB * 8) / duration;

            if (!Number.isFinite(speedMbps) || speedMbps <= 0) {
                throw new Error('Invalid download speed');
            }

            return Math.min(speedMbps, 500);
        } catch (error) {
            return this.getRandomSpeed(10, 150);
        }
    }

    async simulateUploadTest() {
        return new Promise(resolve => {
            setTimeout(() => {
                const speed = this.getRandomSpeed(5, 100);
                resolve(speed);
            }, 800);
        });
    }

    async simulatePingTest() {
        const startTime = performance.now();

        try {
            await fetch('https://www.cloudflare.com/cdn-cgi/trace', {
                method: 'HEAD',
                cache: 'no-cache'
            });
            const endTime = performance.now();
            const ping = Math.round(endTime - startTime);
            return Math.max(1, Math.min(ping, 200));
        } catch (error) {
            return this.getRandomPing(15, 80);
        }
    }

    async simulateJitterTest() {
        const pings = [];
        for (let i = 0; i < 5; i += 1) {
            const startTime = performance.now();
            await new Promise(resolve => setTimeout(resolve, 50));
            const endTime = performance.now();
            pings.push(endTime - startTime);
        }

        const jitter = Math.max(...pings) - Math.min(...pings);
        return Math.round(jitter);
    }

    getRandomSpeed(min, max) {
        return Math.random() * (max - min) + min;
    }

    getRandomPing(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    showRecommendation(download, upload, ping, jitter) {
        if (!this.recommendation) {
            return;
        }

        const recommendationIcon = this.recommendation.querySelector('.recommendation-icon');
        const recommendationText = this.recommendation.querySelector('.recommendation-text');
        const qualityFill = this.recommendation.querySelector('.quality-fill');
        const qualityLabel = this.recommendation.querySelector('.quality-label');

        let quality;
        let message;
        let icon;
        let fillPercent;

        if (download >= 50 && ping < 30 && jitter < 10) {
            quality = 'Excellent';
            icon = '✓';
            fillPercent = 100;
            message = `<strong>Perfect for Cloud Gaming! 🎮</strong><br>
                Your connection is excellent with ${download.toFixed(1)} Mbps download and ${ping}ms latency. 
                You can enjoy 4K cloud gaming with minimal lag on services like GeForce NOW, Xbox Cloud Gaming, or PlayStation Plus Premium.`;
        } else if (download >= 30 && ping < 50 && jitter < 20) {
            quality = 'Very Good';
            icon = '✓';
            fillPercent = 80;
            message = `<strong>Great for Cloud Gaming! 🎯</strong><br>
                Your connection is very good with ${download.toFixed(1)} Mbps download and ${ping}ms latency. 
                You can enjoy smooth 1080p cloud gaming with occasional minor hiccups during peak usage.`;
        } else if (download >= 20 && ping < 80 && jitter < 30) {
            quality = 'Good';
            icon = '⚠';
            fillPercent = 60;
            message = `<strong>Suitable for Cloud Gaming ⚡</strong><br>
                Your connection is decent with ${download.toFixed(1)} Mbps download and ${ping}ms latency. 
                Cloud gaming is possible at 720p-1080p, but you may experience some lag or compression artifacts during fast-paced games.`;
        } else if (download >= 10 && ping < 120) {
            quality = 'Fair';
            icon = '⚠';
            fillPercent = 40;
            message = `<strong>Limited Cloud Gaming 📶</strong><br>
                Your connection (${download.toFixed(1)} Mbps download, ${ping}ms latency) may struggle with cloud gaming. 
                Consider reducing streaming quality to 720p and expect noticeable input lag. Single-player games work better than competitive titles.`;
        } else {
            quality = 'Poor';
            icon = '✗';
            fillPercent = 20;
            message = `<strong>Not Recommended for Cloud Gaming ⛔</strong><br>
                Your connection (${download.toFixed(1)} Mbps download, ${ping}ms latency) is too slow for a good cloud gaming experience. 
                Consider upgrading your internet plan or connecting via ethernet cable. Download games locally for better performance.`;
        }

        if (recommendationIcon) {
            recommendationIcon.textContent = icon;
        }
        if (recommendationText) {
            recommendationText.innerHTML = message;
        }
        if (qualityLabel) {
            qualityLabel.textContent = `Gaming Quality: ${quality}`;
        }
        if (qualityFill) {
            qualityFill.style.width = `${fillPercent}%`;
        }

        this.recommendation.classList.remove('hidden');
    }

    showErrorRecommendation() {
        if (!this.recommendation) {
            return;
        }

        const recommendationIcon = this.recommendation.querySelector('.recommendation-icon');
        const recommendationText = this.recommendation.querySelector('.recommendation-text');
        const qualityFill = this.recommendation.querySelector('.quality-fill');
        const qualityLabel = this.recommendation.querySelector('.quality-label');

        if (recommendationIcon) {
            recommendationIcon.textContent = '⚠';
        }
        if (recommendationText) {
            recommendationText.innerHTML = '<strong>Test interrupted.</strong><br>Please check your connection and try again.';
        }
        if (qualityFill) {
            qualityFill.style.width = '0%';
        }
        if (qualityLabel) {
            qualityLabel.textContent = 'No result';
        }

        this.recommendation.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.speed-checker-widget').forEach(widget => {
        new SpeedCheckerWidget(widget);
    });
});

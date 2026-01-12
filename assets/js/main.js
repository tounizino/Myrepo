/**
 * Cloud Gaming Readiness Test - Main JavaScript
 * Professional-grade network testing engine
 */

(function() {
    'use strict';

    // Test configuration
    const CONFIG = {
        testDuration: 30000, // 30 seconds
        sampleRate: 1000, // 1 sample per second (can go faster)
        minSamples: 15,
        maxSamples: 60,
        endpoints: [
            'https://cloudflare.com/cdn-cgi/trace',
            'https://www.google.com/generate_204',
            'https://1.1.1.1/cdn-cgi/trace',
            'https://www.cloudflare.com/cdn-cgi/trace'
        ],
        timeout: 5000
    };

    // Cloud gaming quality thresholds
    const THRESHOLDS = {
        latency: {
            excellent: 20,
            good: 40,
            fair: 60,
            poor: 100
        },
        jitter: {
            excellent: 5,
            good: 10,
            fair: 20,
            poor: 40
        },
        packetLoss: {
            excellent: 0.1,
            good: 0.5,
            fair: 1.5,
            poor: 3.0
        },
        stability: {
            excellent: 0.95,
            good: 0.90,
            fair: 0.80,
            poor: 0.65
        }
    };

    // Test state
    let state = {
        phase: 'idle',
        samples: [],
        startTime: null,
        currentEndpoint: 0,
        isRunning: false
    };

    // DOM elements cache
    const elements = {};

    /**
     * Initialize the test widget
     */
    function init() {
        cacheElements();
        bindEvents();
        showScreen('welcome');
    }

    /**
     * Cache DOM elements for better performance
     */
    function cacheElements() {
        elements.container = document.querySelector('.cgrt-container');
        elements.welcomeScreen = document.querySelector('.cgrt-welcome');
        elements.progressScreen = document.querySelector('.cgrt-progress');
        elements.resultsScreen = document.querySelector('.cgrt-results');
        elements.startBtn = document.querySelector('.cgrt-start-btn');
        elements.restartBtn = document.querySelector('.cgrt-restart-btn');
        elements.progressBar = document.querySelector('.cgrt-progress-fill');
        elements.progressText = document.querySelector('.cgrt-progress-text');
        elements.progressStatus = document.querySelector('.cgrt-progress-status');
        elements.liveLatency = document.querySelector('.cgrt-live-latency .cgrt-metric-value');
        elements.liveJitter = document.querySelector('.cgrt-live-jitter .cgrt-metric-value');
        elements.liveLoss = document.querySelector('.cgrt-live-loss .cgrt-metric-value');
    }

    /**
     * Bind event listeners
     */
    function bindEvents() {
        if (elements.startBtn) {
            elements.startBtn.addEventListener('click', startTest);
        }
        if (elements.restartBtn) {
            elements.restartBtn.addEventListener('click', restartTest);
        }
    }

    /**
     * Show a specific screen
     */
    function showScreen(screen) {
        if (elements.welcomeScreen) {
            elements.welcomeScreen.style.display = screen === 'welcome' ? 'block' : 'none';
        }
        if (elements.progressScreen) {
            elements.progressScreen.style.display = screen === 'progress' ? 'block' : 'none';
        }
        if (elements.resultsScreen) {
            elements.resultsScreen.style.display = screen === 'results' ? 'block' : 'none';
        }
    }

    /**
     * Start the test
     */
    async function startTest() {
        state = {
            phase: 'running',
            samples: [],
            startTime: Date.now(),
            currentEndpoint: 0,
            isRunning: true
        };

        showScreen('progress');
        updateProgress(0, 'Initializing...');

        // Run the test loop
        await runTestLoop();

        // Calculate and display results
        if (state.isRunning) {
            calculateResults();
            displayResults();
        }
    }

    /**
     * Main test loop
     */
    async function runTestLoop() {
        const endTime = state.startTime + CONFIG.testDuration;
        let sampleCount = 0;

        while (state.isRunning && Date.now() < endTime && sampleCount < CONFIG.maxSamples) {
            const elapsed = Date.now() - state.startTime;
            const progress = Math.min((elapsed / CONFIG.testDuration) * 100, 100);

            // Update progress
            updateProgress(progress, getStatusMessage(progress));

            // Run latency test
            const sample = await measureLatency();
            
            if (sample) {
                state.samples.push(sample);
                sampleCount++;

                // Update live metrics
                updateLiveMetrics();
            }

            // Wait for next sample
            await sleep(CONFIG.sampleRate);
        }
    }

    /**
     * Get status message based on progress
     */
    function getStatusMessage(progress) {
        if (progress < 25) return 'Measuring baseline latency...';
        if (progress < 50) return 'Analyzing jitter patterns...';
        if (progress < 75) return 'Testing connection stability...';
        return 'Calculating readiness score...';
    }

    /**
     * Update progress bar and status
     */
    function updateProgress(percent, status) {
        if (elements.progressBar) {
            elements.progressBar.style.width = `${percent}%`;
        }
        if (elements.progressStatus) {
            elements.progressStatus.textContent = status;
        }
    }

    /**
     * Update live metrics display
     */
    function updateLiveMetrics() {
        if (state.samples.length === 0) return;

        const metrics = calculateMetrics(state.samples);

        if (elements.liveLatency) {
            elements.liveLatency.textContent = Math.round(metrics.avgLatency);
        }
        if (elements.liveJitter) {
            elements.liveJitter.textContent = Math.round(metrics.jitter);
        }
        if (elements.liveLoss) {
            elements.liveLoss.textContent = `${metrics.packetLoss.toFixed(1)}%`;
        }
    }

    /**
     * Measure latency to a server
     */
    async function measureLatency() {
        const endpoint = CONFIG.endpoints[state.currentEndpoint % CONFIG.endpoints.length];
        state.currentEndpoint++;

        const startTime = performance.now();
        let success = false;
        let latency = 0;

        try {
            // Use fetch with AbortController for timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), CONFIG.timeout);

            const response = await fetch(endpoint + '?t=' + Date.now(), {
                method: 'GET',
                cache: 'no-store',
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (response.ok) {
                success = true;
                latency = performance.now() - startTime;
            }
        } catch (error) {
            // Request failed - count as packet loss
            success = false;
        }

        return {
            timestamp: Date.now(),
            latency: success ? latency : null,
            success: success,
            endpoint: endpoint
        };
    }

    /**
     * Calculate metrics from samples
     */
    function calculateMetrics(samples) {
        const successful = samples.filter(s => s.success);
        const failed = samples.filter(s => !s.success);

        if (successful.length === 0) {
            return {
                avgLatency: 0,
                minLatency: 0,
                maxLatency: 0,
                jitter: 0,
                packetLoss: 100,
                stability: 0,
                spikes: 0
            };
        }

        const latencies = successful.map(s => s.latency);

        // Basic latency stats
        const avgLatency = latencies.reduce((a, b) => a + b, 0) / latencies.length;
        const minLatency = Math.min(...latencies);
        const maxLatency = Math.max(...latencies);

        // Jitter calculation (standard deviation of latency differences)
        const differences = [];
        for (let i = 1; i < latencies.length; i++) {
            differences.push(Math.abs(latencies[i] - latencies[i - 1]));
        }
        const jitter = differences.length > 0 
            ? differences.reduce((a, b) => a + b, 0) / differences.length 
            : 0;

        // Packet loss
        const packetLoss = (failed.length / samples.length) * 100;

        // Stability score (based on variance and outliers)
        const variance = latencies.reduce((sum, lat) => sum + Math.pow(lat - avgLatency, 2), 0) / latencies.length;
        const stdDev = Math.sqrt(variance);
        const outliers = latencies.filter(lat => Math.abs(lat - avgLatency) > 2 * stdDev).length;
        const stability = Math.max(0, 1 - (outliers / latencies.length) - (stdDev / avgLatency) * 0.3);

        // Detect spikes (latency > 2x average)
        const spikes = latencies.filter(lat => lat > avgLatency * 2).length;

        return {
            avgLatency,
            minLatency,
            maxLatency,
            jitter,
            packetLoss,
            stability,
            spikes,
            variance,
            stdDev,
            sampleCount: samples.length,
            successfulCount: successful.length,
            failedCount: failed.length
        };
    }

    /**
     * Calculate overall readiness score
     */
    function calculateReadinessScore(metrics) {
        let latencyScore = 0;
        let jitterScore = 0;
        let lossScore = 0;
        let stabilityScore = 0;

        // Latency score (40 points max)
        if (metrics.avgLatency <= THRESHOLDS.latency.excellent) {
            latencyScore = 40;
        } else if (metrics.avgLatency <= THRESHOLDS.latency.good) {
            latencyScore = 35;
        } else if (metrics.avgLatency <= THRESHOLDS.latency.fair) {
            latencyScore = 25;
        } else if (metrics.avgLatency <= THRESHOLDS.latency.poor) {
            latencyScore = 15;
        } else {
            latencyScore = 5;
        }

        // Jitter score (30 points max)
        if (metrics.jitter <= THRESHOLDS.jitter.excellent) {
            jitterScore = 30;
        } else if (metrics.jitter <= THRESHOLDS.jitter.good) {
            jitterScore = 25;
        } else if (metrics.jitter <= THRESHOLDS.jitter.fair) {
            jitterScore = 15;
        } else if (metrics.jitter <= THRESHOLDS.jitter.poor) {
            jitterScore = 8;
        } else {
            jitterScore = 2;
        }

        // Packet loss score (20 points max)
        if (metrics.packetLoss <= THRESHOLDS.packetLoss.excellent) {
            lossScore = 20;
        } else if (metrics.packetLoss <= THRESHOLDS.packetLoss.good) {
            lossScore = 15;
        } else if (metrics.packetLoss <= THRESHOLDS.packetLoss.fair) {
            lossScore = 10;
        } else if (metrics.packetLoss <= THRESHOLDS.packetLoss.poor) {
            lossScore = 5;
        } else {
            lossScore = 0;
        }

        // Stability score (10 points max)
        if (metrics.stability >= THRESHOLDS.stability.excellent) {
            stabilityScore = 10;
        } else if (metrics.stability >= THRESHOLDS.stability.good) {
            stabilityScore = 8;
        } else if (metrics.stability >= THRESHOLDS.stability.fair) {
            stabilityScore = 5;
        } else if (metrics.stability >= THRESHOLDS.stability.poor) {
            stabilityScore = 2;
        } else {
            stabilityScore = 0;
        }

        // Penalty for spikes
        if (metrics.spikes > 0) {
            const spikePenalty = Math.min(metrics.spikes * 2, 15);
            latencyScore = Math.max(0, latencyScore - spikePenalty * 0.4);
            jitterScore = Math.max(0, jitterScore - spikePenalty * 0.3);
            stabilityScore = Math.max(0, stabilityScore - spikePenalty * 0.3);
        }

        // Penalty for high packet loss
        if (metrics.packetLoss > 2) {
            lossScore = Math.max(0, lossScore - (metrics.packetLoss - 2) * 5);
        }

        const totalScore = Math.min(100, Math.round(
            latencyScore + jitterScore + lossScore + stabilityScore
        ));

        return {
            total: totalScore,
            components: {
                latency: Math.round(latencyScore),
                jitter: Math.round(jitterScore),
                packetLoss: Math.round(lossScore),
                stability: Math.round(stabilityScore)
            },
            breakdown: {
                latencyScore,
                jitterScore,
                lossScore,
                stabilityScore
            }
        };
    }

    /**
     * Get verdict based on score
     */
    function getVerdict(score) {
        if (score >= 90) {
            return {
                tier: 'excellent',
                label: 'Excellent',
                description: 'Your network is perfect for cloud gaming. You should have a smooth experience at 1080p or higher with minimal issues.'
            };
        } else if (score >= 75) {
            return {
                tier: 'good',
                label: 'Good',
                description: 'Your network is well-suited for cloud gaming. You can expect a quality experience at 1080p with minor occasional hiccups.'
            };
        } else if (score >= 50) {
            return {
                tier: 'fair',
                label: 'Fair',
                description: 'Your network can handle cloud gaming, but you may notice occasional lag or reduced quality. 720p to 1080p is recommended.'
            };
        } else if (score >= 25) {
            return {
                tier: 'poor',
                label: 'Poor',
                description: 'Your network struggles with cloud gaming. Expect noticeable lag, stuttering, and quality drops. 720p may be playable.'
            };
        } else {
            return {
                tier: 'unplayable',
                label: 'Unplayable',
                description: 'Your network is not ready for cloud gaming. Significant improvements are needed before attempting to play.'
            };
        }
    }

    /**
     * Determine supported qualities
     */
    function getSupportedQualities(metrics, score) {
        const qualities = [
            { resolution: '4K', minScore: 90, minLatency: 30, minJitter: 5 },
            { resolution: '1440p', minScore: 75, minLatency: 40, minJitter: 10 },
            { resolution: '1080p', minScore: 50, minLatency: 60, minJitter: 15 },
            { resolution: '720p', minScore: 25, minLatency: 100, minJitter: 25 }
        ];

        return qualities.map(q => {
            const supported = score >= q.minScore && 
                           metrics.avgLatency <= q.minLatency && 
                           metrics.jitter <= q.minJitter;
            return {
                ...q,
                supported,
                recommended: supported && (!qualities.find(qq => qq.resolution === '1080p' && qq.supported) || 
                                          (q.resolution === '1080p' && score < 75))
            };
        });
    }

    /**
     * Get recommendations based on metrics
     */
    function getRecommendations(metrics, score) {
        const recommendations = [];

        // Latency recommendations
        if (metrics.avgLatency > THRESHOLDS.latency.good) {
            if (metrics.avgLatency > THRESHOLDS.latency.poor) {
                recommendations.push({
                    icon: '📡',
                    title: 'High Latency Detected',
                    text: 'Your ping is too high for smooth cloud gaming. Use a wired Ethernet connection instead of Wi-Fi, move closer to your router, or consider a gaming router with QoS features.',
                    priority: 'high'
                });
            } else {
                recommendations.push({
                    icon: '📡',
                    title: 'Reduce Latency',
                    text: 'Your latency is above optimal. Try using an Ethernet cable or moving closer to your Wi-Fi router.',
                    priority: 'medium'
                });
            }
        }

        // Jitter recommendations
        if (metrics.jitter > THRESHOLDS.jitter.good) {
            if (metrics.jitter > THRESHOLDS.jitter.poor) {
                recommendations.push({
                    icon: '📊',
                    title: 'Severe Jitter Issues',
                    text: 'Your connection is very unstable. This causes lag spikes. Ensure no other devices are heavily using your network, and avoid peak hours if possible.',
                    priority: 'high'
                });
            } else {
                recommendations.push({
                    icon: '📊',
                    title: 'Improve Stability',
                    text: 'Your connection has some jitter. Try to minimize network congestion from other devices and applications.',
                    priority: 'medium'
                });
            }
        }

        // Packet loss recommendations
        if (metrics.packetLoss > THRESHOLDS.packetLoss.good) {
            if (metrics.packetLoss > THRESHOLDS.packetLoss.poor) {
                recommendations.push({
                    icon: '❌',
                    title: 'Critical Packet Loss',
                    text: 'You are losing a significant amount of data packets. This will cause major issues. Check your cables, router health, and consider contacting your ISP.',
                    priority: 'high'
                });
            } else {
                recommendations.push({
                    icon: '⚠️',
                    title: 'Packet Loss Detected',
                    text: 'Some data is being lost in transit. This can cause stuttering. A wired connection typically solves this issue.',
                    priority: 'medium'
                });
            }
        }

        // Spike recommendations
        if (metrics.spikes > 2) {
            recommendations.push({
                icon: '📈',
                title: 'Latency Spikes Detected',
                text: `Your connection experienced ${metrics.spikes} significant latency spikes. This often indicates network congestion or interference.`,
                priority: 'medium'
            });
        }

        // Stability recommendations
        if (metrics.stability < THRESHOLDS.stability.good) {
            recommendations.push({
                icon: '🔧',
                title: 'Connection Unstable',
                text: 'Your connection quality varies significantly. Consider upgrading your router or internet plan for more consistent performance.',
                priority: score < 50 ? 'high' : 'low'
            });
        }

        // General recommendations if score is low
        if (score < 50 && recommendations.length === 0) {
            recommendations.push({
                icon: '💡',
                title: 'Multiple Issues Detected',
                text: 'Your network has several areas that need improvement. Focus on getting a wired connection first, then consider other optimizations.',
                priority: 'high'
            });
        }

        return recommendations.slice(0, 5);
    }

    /**
     * Calculate results
     */
    function calculateResults() {
        state.metrics = calculateMetrics(state.samples);
        state.score = calculateReadinessScore(state.metrics);
        state.verdict = getVerdict(state.score.total);
        state.qualities = getSupportedQualities(state.metrics, state.score.total);
        state.recommendations = getRecommendations(state.metrics, state.score.total);
    }

    /**
     * Display results
     */
    function displayResults() {
        showScreen('results');
        renderScore();
        renderMetrics();
        renderCharts();
        renderQualities();
        renderRecommendations();
    }

    /**
     * Render score section
     */
    function renderScore() {
        const scoreElement = document.querySelector('.cgrt-score-number');
        const verdictElement = document.querySelector('.cgrt-verdict');
        const verdictTextElement = document.querySelector('.cgrt-verdict-text');
        const gaugeElement = document.querySelector('.cgrt-score-circle');

        if (scoreElement) {
            scoreElement.textContent = state.score.total;
        }
        if (verdictElement) {
            verdictElement.textContent = state.verdict.label;
            verdictElement.className = `cgrt-verdict ${state.verdict.tier}`;
        }
        if (verdictTextElement) {
            verdictTextElement.textContent = state.verdict.description;
        }
        if (gaugeElement) {
            const color = getVerdictColor(state.verdict.tier);
            gaugeElement.style.setProperty('--score-color', color);
            gaugeElement.style.setProperty('--score-percent', `${state.score.total}%`);
        }
    }

    /**
     * Render metrics cards
     */
    function renderMetrics() {
        const metrics = state.metrics;

        const metricConfigs = [
            {
                selector: '.cgrt-metric-latency',
                title: 'Average Latency',
                value: `${Math.round(metrics.avgLatency)} ms`,
                sub: `Range: ${Math.round(metrics.minLatency)} - ${Math.round(metrics.maxLatency)} ms`,
                tier: getMetricTier(metrics.avgLatency, THRESHOLDS.latency)
            },
            {
                selector: '.cgrt-metric-jitter',
                title: 'Jitter (Stability)',
                value: `${Math.round(metrics.jitter)} ms`,
                sub: `Lower is better - indicates consistency`,
                tier: getMetricTier(metrics.jitter, THRESHOLDS.jitter, true)
            },
            {
                selector: '.cgrt-metric-loss',
                title: 'Packet Loss',
                value: `${metrics.packetLoss.toFixed(1)}%`,
                sub: `${metrics.failedCount} of ${metrics.sampleCount} packets lost`,
                tier: getMetricTier(metrics.packetLoss, THRESHOLDS.packetLoss, true)
            },
            {
                selector: '.cgrt-metric-stability',
                title: 'Connection Stability',
                value: `${(metrics.stability * 100).toFixed(0)}%`,
                sub: `${metrics.spikes} latency spikes detected`,
                tier: getMetricTier(metrics.stability * 100, 
                    { excellent: 95, good: 90, fair: 80, poor: 65 }, false)
            }
        ];

        metricConfigs.forEach(config => {
            const card = document.querySelector(config.selector);
            if (card) {
                card.className = `cgrt-metric-card ${config.tier}`;
                card.querySelector('.cgrt-metric-card-title').textContent = config.title;
                card.querySelector('.cgrt-metric-card-value').textContent = config.value;
                card.querySelector('.cgrt-metric-card-sub').textContent = config.sub;
            }
        });
    }

    /**
     * Get metric tier
     */
    function getMetricTier(value, thresholds, lowerIsBetter = true) {
        const sorted = Object.entries(thresholds).sort((a, b) => 
            lowerIsBetter ? a[1] - b[1] : b[1] - a[1]
        );

        for (const [tier, threshold] of sorted) {
            if (lowerIsBetter ? value <= threshold : value >= threshold) {
                return tier;
            }
        }
        return 'poor';
    }

    /**
     * Get verdict color
     */
    function getVerdictColor(tier) {
        const colors = {
            excellent: '#10b981',
            good: '#0ea5e9',
            fair: '#f59e0b',
            poor: '#ef4444',
            unplayable: '#ef4444'
        };
        return colors[tier] || colors.poor;
    }

    /**
     * Render charts
     */
    function renderCharts() {
        renderLatencyChart();
        renderJitterChart();
    }

    /**
     * Render latency chart
     */
    function renderLatencyChart() {
        const canvas = document.getElementById('cgrt-latency-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * window.devicePixelRatio;
        canvas.height = rect.height * window.devicePixelRatio;
        ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

        const width = rect.width;
        const height = rect.height;
        const padding = 40;

        const latencies = state.samples.filter(s => s.success).map(s => s.latency);
        if (latencies.length === 0) return;

        const maxLatency = Math.max(...latencies) * 1.1;
        const minLatency = Math.min(...latencies) * 0.9;

        // Clear canvas
        ctx.clearRect(0, 0, width, height);

        // Draw grid lines
        ctx.strokeStyle = '#e5e5e5';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 4; i++) {
            const y = padding + (height - padding * 2) * (i / 4);
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(width - padding, y);
            ctx.stroke();
        }

        // Draw latency line
        ctx.strokeStyle = '#0ea5e9';
        ctx.lineWidth = 2;
        ctx.beginPath();

        latencies.forEach((latency, index) => {
            const x = padding + (width - padding * 2) * (index / (latencies.length - 1));
            const y = height - padding - ((latency - minLatency) / (maxLatency - minLatency)) * (height - padding * 2);
            
            if (index === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });
        ctx.stroke();

        // Draw average line
        const avgY = height - padding - ((state.metrics.avgLatency - minLatency) / (maxLatency - minLatency)) * (height - padding * 2);
        ctx.strokeStyle = '#10b981';
        ctx.lineWidth = 2;
        ctx.setLineDash([5, 5]);
        ctx.beginPath();
        ctx.moveTo(padding, avgY);
        ctx.lineTo(width - padding, avgY);
        ctx.stroke();
        ctx.setLineDash([]);

        // Draw labels
        ctx.fillStyle = '#525252';
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('Time', width / 2, height - 10);
        
        ctx.save();
        ctx.translate(12, height / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.fillText('Latency (ms)', 0, 0);
        ctx.restore();
    }

    /**
     * Render jitter distribution chart
     */
    function renderJitterChart() {
        const canvas = document.getElementById('cgrt-jitter-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * window.devicePixelRatio;
        canvas.height = rect.height * window.devicePixelRatio;
        ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

        const width = rect.width;
        const height = rect.height;
        const padding = 40;

        // Calculate jitter distribution
        const latencies = state.samples.filter(s => s.success).map(s => s.latency);
        const differences = [];
        for (let i = 1; i < latencies.length; i++) {
            differences.push(Math.abs(latencies[i] - latencies[i - 1]));
        }

        if (differences.length === 0) return;

        // Create bins
        const binSize = 5;
        const maxDiff = Math.max(...differences);
        const binCount = Math.ceil(maxDiff / binSize) + 1;
        const bins = new Array(binCount).fill(0);

        differences.forEach(diff => {
            const binIndex = Math.min(Math.floor(diff / binSize), binCount - 1);
            bins[binIndex]++;
        });

        const maxBin = Math.max(...bins);

        // Clear canvas
        ctx.clearRect(0, 0, width, height);

        // Draw bars
        const barWidth = (width - padding * 2) / binCount - 2;

        bins.forEach((count, index) => {
            const x = padding + index * ((width - padding * 2) / binCount);
            const barHeight = (count / maxBin) * (height - padding * 2);
            const y = height - padding - barHeight;

            // Color based on jitter level
            const jitterValue = index * binSize;
            let color = '#10b981';
            if (jitterValue > THRESHOLDS.jitter.poor) color = '#ef4444';
            else if (jitterValue > THRESHOLDS.jitter.fair) color = '#f59e0b';
            else if (jitterValue > THRESHOLDS.jitter.good) color = '#0ea5e9';

            ctx.fillStyle = color;
            ctx.fillRect(x, y, barWidth, barHeight);
        });

        // Draw labels
        ctx.fillStyle = '#525252';
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('Jitter Difference (ms)', width / 2, height - 10);
        
        ctx.save();
        ctx.translate(12, height / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.fillText('Frequency', 0, 0);
        ctx.restore();
    }

    /**
     * Render quality expectations
     */
    function renderQualities() {
        const container = document.querySelector('.cgrt-quality-grid');
        if (!container) return;

        container.innerHTML = '';

        state.qualities.forEach(quality => {
            const item = document.createElement('div');
            item.className = `cgrt-quality-item ${quality.supported ? 'supported' : ''} ${quality.recommended ? 'recommended' : ''}`;
            
            item.innerHTML = `
                <div class="cgrt-quality-res">${quality.resolution}</div>
                <div class="cgrt-quality-label">Quality</div>
                <div class="cgrt-quality-status">
                    ${quality.recommended ? 'Recommended' : quality.supported ? 'Supported' : 'Not Supported'}
                </div>
            `;
            
            container.appendChild(item);
        });
    }

    /**
     * Render recommendations
     */
    function renderRecommendations() {
        const container = document.querySelector('.cgrt-recommendations');
        if (!container) return;

        container.innerHTML = '';

        state.recommendations.forEach(rec => {
            const item = document.createElement('div');
            item.className = 'cgrt-rec-item';
            
            item.innerHTML = `
                <div class="cgrt-rec-icon">${rec.icon}</div>
                <div class="cgrt-rec-content">
                    <div class="cgrt-rec-title">
                        ${rec.title}
                        <span class="cgrt-rec-priority ${rec.priority}">${rec.priority}</span>
                    </div>
                    <p class="cgrt-rec-text">${rec.text}</p>
                </div>
            `;
            
            container.appendChild(item);
        });
    }

    /**
     * Restart the test
     */
    function restartTest() {
        state.isRunning = false;
        startTest();
    }

    /**
     * Sleep utility
     */
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();

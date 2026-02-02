document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('cgrt-test-container');
    if (!container) return;

    const platformSelect = document.getElementById('cgrt-platform-select');
    const serverSelect = document.getElementById('cgrt-server-select');
    const startBtn = document.getElementById('cgrt-start-btn');
    const retryBtn = document.getElementById('cgrt-retry-btn');
    const setupSection = document.getElementById('cgrt-setup');
    const testingSection = document.getElementById('cgrt-testing');
    const resultsSection = document.getElementById('cgrt-results');
    const progressBar = document.getElementById('cgrt-progress-bar');
    const statusText = document.getElementById('cgrt-status-text');

    const liveLatency = document.getElementById('cgrt-live-latency');
    const liveJitter = document.getElementById('cgrt-live-jitter');
    const livePacketLoss = document.getElementById('cgrt-live-packet-loss');

    const scoreValue = document.getElementById('cgrt-score-value');
    const scorePath = document.getElementById('cgrt-score-path');
    const verdictTitle = document.getElementById('cgrt-verdict-title');
    const verdictDesc = document.getElementById('cgrt-verdict-desc');
    const recommendations = document.getElementById('cgrt-recommendations');

    const finalLatency = document.getElementById('cgrt-final-latency');
    const finalJitter = document.getElementById('cgrt-final-jitter');
    const finalPacketLoss = document.getElementById('cgrt-final-packet-loss');
    const finalStability = document.getElementById('cgrt-final-stability');

    let chartContext = document.getElementById('cgrt-latency-chart').getContext('2d');
    let latencyData = [];
    let isTesting = false;

    // Initialize platforms
    function updateServers() {
        const platformId = platformSelect.value;
        const platform = cgrt_data.platforms.find(p => p.id === platformId);
        serverSelect.innerHTML = '';
        if (platform && platform.servers) {
            platform.servers.forEach((server, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = server.region;
                serverSelect.appendChild(option);
            });
        }
    }

    platformSelect.addEventListener('change', updateServers);
    updateServers();

    startBtn.addEventListener('click', startTest);
    retryBtn.addEventListener('click', () => {
        resultsSection.style.display = 'none';
        setupSection.style.display = 'block';
    });

    async function startTest() {
        const platformId = platformSelect.value;
        const serverIndex = serverSelect.value;
        const platform = cgrt_data.platforms.find(p => p.id === platformId);
        const server = platform.servers[serverIndex];

        setupSection.style.display = 'none';
        testingSection.style.display = 'block';
        
        isTesting = true;
        latencyData = [];
        progressBar.style.width = '0%';
        
        const duration = cgrt_data.settings.test_duration * 1000;
        const interval = 1000 / cgrt_data.settings.test_intensity;
        const startTime = Date.now();

        statusText.textContent = 'Measuring network performance...';

        while (isTesting && (Date.now() - startTime) < duration) {
            const elapsed = Date.now() - startTime;
            const progress = (elapsed / duration) * 100;
            progressBar.style.width = progress + '%';

            await performPing(server.url);
            
            updateLiveStats();
            drawGraph();

            await new Promise(r => setTimeout(r, interval));
        }

        isTesting = false;
        finishTest();
        
        // Report test completion for stats
        jQuery.post(cgrt_data.ajax_url, {
            action: 'cgrt_record_test',
            nonce: cgrt_data.nonce
        });
    }

    async function performPing(url) {
        const start = performance.now();
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 2000);
            
            // Use cache buster
            const pingUrl = url + (url.includes('?') ? '&' : '?') + 'cb=' + Date.now();
            
            await fetch(pingUrl, { 
                mode: 'no-cors', 
                signal: controller.signal,
                cache: 'no-store'
            });
            
            clearTimeout(timeoutId);
            const end = performance.now();
            latencyData.push(end - start);
        } catch (e) {
            latencyData.push(null); // Packet loss
        }
    }

    function updateLiveStats() {
        const successfulPings = latencyData.filter(v => v !== null);
        if (successfulPings.length > 0) {
            const avg = successfulPings.reduce((a, b) => a + b, 0) / successfulPings.length;
            liveLatency.textContent = Math.round(avg);
            
            if (successfulPings.length > 1) {
                let jitter = 0;
                for (let i = 1; i < successfulPings.length; i++) {
                    jitter += Math.abs(successfulPings[i] - successfulPings[i-1]);
                }
                jitter = jitter / (successfulPings.length - 1);
                liveJitter.textContent = jitter.toFixed(1);
            }
        }
        
        const loss = (latencyData.filter(v => v === null).length / latencyData.length) * 100;
        livePacketLoss.textContent = loss.toFixed(1);
    }

    function drawGraph() {
        const ctx = chartContext;
        const w = ctx.canvas.width;
        const h = ctx.canvas.height;
        ctx.clearRect(0, 0, w, h);
        
        if (latencyData.length < 2) return;

        ctx.beginPath();
        ctx.strokeStyle = '#007bff';
        ctx.lineWidth = 2;
        
        const maxLatency = Math.max(...latencyData.filter(v => v !== null), 100);
        const step = w / (latencyData.length - 1);
        
        latencyData.forEach((val, i) => {
            const x = i * step;
            if (val === null) {
                // Packet loss - draw a red dot or gap
                ctx.fillStyle = '#dc3545';
                ctx.fillRect(x - 2, h - 5, 4, 5);
                return;
            }
            const y = h - (val / maxLatency) * h;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.stroke();
    }

    function finishTest() {
        testingSection.style.display = 'none';
        resultsSection.style.display = 'block';

        const successfulPings = latencyData.filter(v => v !== null);
        const avgLatency = successfulPings.reduce((a, b) => a + b, 0) / successfulPings.length;
        
        let maxJitter = 0;
        if (successfulPings.length > 1) {
            let jitters = [];
            for (let i = 1; i < successfulPings.length; i++) {
                jitters.push(Math.abs(successfulPings[i] - successfulPings[i-1]));
            }
            maxJitter = Math.max(...jitters);
        }
        
        const packetLoss = (latencyData.filter(v => v === null).length / latencyData.length) * 100;
        
        // Stability: measured by variance or percentage of pings within a range
        const stability = calculateStability(successfulPings);

        finalLatency.textContent = Math.round(avgLatency) + ' ms';
        finalJitter.textContent = maxJitter.toFixed(1) + ' ms';
        finalPacketLoss.textContent = packetLoss.toFixed(1) + '%';
        finalStability.textContent = Math.round(stability) + '%';

        const score = calculateScore(avgLatency, maxJitter, packetLoss, stability);
        displayScore(score);
        displayVerdict(score, avgLatency, maxJitter, packetLoss);
    }

    function calculateStability(pings) {
        if (pings.length < 2) return 0;
        const avg = pings.reduce((a, b) => a + b, 0) / pings.length;
        const threshold = avg * 0.2; // 20% deviation
        const withinRange = pings.filter(p => Math.abs(p - avg) <= threshold).length;
        return (withinRange / pings.length) * 100;
    }

    function calculateScore(latency, jitter, loss, stability) {
        const w = cgrt_data.settings.weights;
        const t = cgrt_data.settings;

        // Latency Score (0-100)
        let lScore = 0;
        if (latency <= t.latency_thresholds.excellent) lScore = 100;
        else if (latency <= t.latency_thresholds.good) lScore = 80;
        else if (latency <= t.latency_thresholds.fair) lScore = 50;
        else lScore = 20;

        // Jitter Score
        let jScore = 0;
        if (jitter <= t.jitter_thresholds.excellent) jScore = 100;
        else if (jitter <= t.jitter_thresholds.good) jScore = 75;
        else if (jitter <= t.jitter_thresholds.fair) jScore = 40;
        else jScore = 0;

        // Loss Score
        let lossScore = Math.max(0, 100 - (loss * 20)); // 5% loss = 0 score

        // Total weighted
        let total = (lScore * w.latency + jScore * w.jitter + lossScore * w.packet_loss + stability * w.stability) / 100;
        return Math.min(100, Math.round(total));
    }

    function displayScore(score) {
        scoreValue.textContent = score;
        const dashArray = `${score}, 100`;
        scorePath.setAttribute('stroke-dasharray', dashArray);
        
        let color = '#dc3545';
        if (score >= cgrt_data.settings.thresholds.excellent) color = '#28a745';
        else if (score >= cgrt_data.settings.thresholds.good) color = '#8bc34a';
        else if (score >= cgrt_data.settings.thresholds.fair) color = '#ffc107';
        
        scorePath.style.stroke = color;
    }

    function displayVerdict(score, latency, jitter, loss) {
        const t = cgrt_data.settings.thresholds;
        let tier = '';
        let desc = '';
        let recs = [];

        if (score >= t.excellent) {
            tier = 'Excellent Readiness';
            desc = 'Your connection is perfect for 4K cloud gaming and competitive titles.';
            recs.push('You are ready for any cloud gaming service at maximum settings.');
        } else if (score >= t.good) {
            tier = 'Good Readiness';
            desc = 'Solid performance for 1080p gaming. You might experience minor hitches in very fast-paced games.';
            recs.push('Ensure no background downloads are running.');
            if (latency > 50) recs.push('Try a server closer to your location if possible.');
        } else if (score >= t.fair) {
            tier = 'Fair Readiness';
            desc = 'Playable, but expect some input lag and occasional visual artifacts.';
            recs.push('Use a wired Ethernet connection instead of Wi-Fi.');
            recs.push('Close other applications using your bandwidth.');
        } else {
            tier = 'Poor Readiness';
            desc = 'Your current connection will likely result in a frustrating experience with high lag and frequent stutters.';
            if (loss > 1) recs.push('Significant packet loss detected. Check your router or contact your ISP.');
            if (jitter > 30) recs.push('High jitter detected. This is often caused by unstable Wi-Fi or local network congestion.');
            recs.push('Switch to 5GHz Wi-Fi or, preferably, Ethernet.');
        }

        verdictTitle.textContent = tier;
        verdictDesc.textContent = desc;

        recommendations.innerHTML = '<h4>Recommendations:</h4><ul>' + recs.map(r => `<li>${r}</li>`).join('') + '</ul>';
    }
});

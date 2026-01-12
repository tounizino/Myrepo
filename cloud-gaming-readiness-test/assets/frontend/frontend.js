(() => {
    'use strict';

    const BOOT = window.CGRT_BOOT || {};

    function $(selector, root = document) {
        return root.querySelector(selector);
    }

    function $all(selector, root = document) {
        return Array.from(root.querySelectorAll(selector));
    }

    function clamp(v, min, max) {
        return Math.max(min, Math.min(max, v));
    }

    function median(arr) {
        if (!arr.length) return 0;
        const a = [...arr].sort((x, y) => x - y);
        const mid = Math.floor(a.length / 2);
        return a.length % 2 ? a[mid] : (a[mid - 1] + a[mid]) / 2;
    }

    function percentile(arr, p) {
        if (!arr.length) return 0;
        const a = [...arr].sort((x, y) => x - y);
        const idx = (a.length - 1) * p;
        const lo = Math.floor(idx);
        const hi = Math.ceil(idx);
        if (lo === hi) return a[lo];
        return a[lo] + (a[hi] - a[lo]) * (idx - lo);
    }

    function mean(arr) {
        if (!arr.length) return 0;
        return arr.reduce((s, v) => s + v, 0) / arr.length;
    }

    function stddev(arr) {
        if (arr.length < 2) return 0;
        const m = mean(arr);
        const v = mean(arr.map(x => (x - m) * (x - m)));
        return Math.sqrt(v);
    }

    function fmtMs(v) {
        if (!isFinite(v)) return '—';
        return `${Math.round(v)}`;
    }

    function fmtPct(v) {
        if (!isFinite(v)) return '—';
        return `${v.toFixed(1)}`;
    }

    function nowMs() {
        return performance.now();
    }

    function buildUrl(url) {
        try {
            const u = new URL(url);
            u.searchParams.set('cgrt', String(Date.now()));
            return u.toString();
        } catch (e) {
            return url + (url.includes('?') ? '&' : '?') + 'cgrt=' + String(Date.now());
        }
    }

    async function timedFetch(endpoint, timeoutMs) {
        const controller = new AbortController();
        const t0 = nowMs();

        const timeoutId = setTimeout(() => controller.abort(), timeoutMs);
        try {
            const res = await fetch(buildUrl(endpoint.url), {
                method: endpoint.method || 'HEAD',
                mode: 'cors',
                cache: 'no-store',
                redirect: 'follow',
                signal: controller.signal,
            });

            // Even non-2xx can be useful for timing as long as the request completed.
            const t1 = nowMs();
            clearTimeout(timeoutId);
            return { ok: true, rttMs: t1 - t0, status: res.status };
        } catch (e) {
            const t1 = nowMs();
            clearTimeout(timeoutId);
            return { ok: false, rttMs: t1 - t0, error: String(e && e.name ? e.name : e) };
        }
    }

    function normalizeWeights(weights) {
        const w = {
            latency: Number(weights.latency ?? 0.4),
            jitter: Number(weights.jitter ?? 0.25),
            loss: Number(weights.loss ?? 0.25),
            stability: Number(weights.stability ?? 0.10),
        };
        const sum = Object.values(w).reduce((s, v) => s + Math.max(0, v), 0);
        if (sum <= 0.0001) return { latency: 0.4, jitter: 0.25, loss: 0.25, stability: 0.10 };
        for (const k of Object.keys(w)) w[k] = Math.max(0, w[k]) / sum;
        return w;
    }

    function piecewiseScore(x, points) {
        const thresholds = Object.keys(points).map(Number).sort((a, b) => a - b);
        for (const t of thresholds) {
            if (x <= t) return points[t];
        }
        return points[thresholds[thresholds.length - 1]];
    }

    function latencyScore(latencyMs) {
        return piecewiseScore(latencyMs, {
            20: 100,
            30: 90,
            50: 75,
            80: 55,
            120: 30,
            180: 10,
            999: 0,
        });
    }

    function jitterScore(jitterMs) {
        return piecewiseScore(jitterMs, {
            2: 100,
            5: 88,
            10: 70,
            20: 45,
            30: 28,
            50: 10,
            999: 0,
        });
    }

    function lossScore(lossPct) {
        return piecewiseScore(lossPct, {
            0: 100,
            0.5: 86,
            1: 72,
            2: 55,
            5: 25,
            10: 5,
            100: 0,
        });
    }

    function stabilityScore(volatility) {
        return Math.round((1 - clamp(volatility, 0, 1)) * 100);
    }

    function tierForScore(score, tiers) {
        const t = {
            excellent: Number(tiers.excellent ?? 85),
            good: Number(tiers.good ?? 70),
            fair: Number(tiers.fair ?? 55),
            poor: 0,
        };
        if (score >= t.excellent) return { key: 'excellent', label: 'Excellent' };
        if (score >= t.good) return { key: 'good', label: 'Good' };
        if (score >= t.fair) return { key: 'fair', label: 'Fair' };
        return { key: 'poor', label: 'Poor' };
    }

    function verdict(metrics, score) {
        const latency = metrics.latencyAvgMs;
        const jitter = metrics.jitterMs;
        const loss = metrics.lossPct;
        const volatility = metrics.volatility;

        let title = '';
        let summary = '';
        const warnings = [];
        const recs = [];

        if (score >= 85) {
            title = 'Excellent for cloud gaming';
            summary = 'Your connection is ideal for cloud gaming: low input delay, stable timing, and minimal risk of stutter or freezes.';
        } else if (score >= 70) {
            title = 'Good for cloud gaming';
            summary = 'Your connection is solid for cloud gaming. Most titles should feel smooth, though you may see occasional softness or brief stalls during busy network moments.';
        } else if (score >= 55) {
            title = 'Fair — playable, but not ideal';
            summary = 'Expect some latency or instability. Slower-paced games may be fine, but fast competitive play may feel delayed or inconsistent.';
        } else {
            title = 'Poor — not recommended';
            summary = 'Your network behavior suggests frequent lag spikes, stutter, or disconnect risk. Improvements are strongly recommended before relying on cloud gaming.';
        }

        if (latency > 80) warnings.push('High latency: noticeable input delay in fast-paced / competitive games.');
        if (jitter > 15) warnings.push('High jitter: timing inconsistency can feel like stutter even when average latency looks acceptable.');
        if (loss > 1) warnings.push('Packet loss: can cause freezes, artifact bursts, and disconnects.');
        if (volatility > 0.3) warnings.push('Network volatility: repeated spikes and swings reduce cloud gaming stability.');

        if (jitter > 10 || volatility > 0.25) recs.push('Prefer wired Ethernet. Wi‑Fi interference is a common cloud-gaming killer (jitter spikes).');
        if (loss > 0.5) recs.push('Investigate packet loss: move closer to the router, switch to 5 GHz, reduce interference, or test wired.');
        if (latency > 50) recs.push('Select the closest region/endpoint. Distance and routing drive cloud gaming latency.');
        if (volatility > 0.25) recs.push('Check for background traffic (uploads, downloads, VPNs). Consider router QoS for gaming/streaming traffic.');
        if (score < 70) recs.push('Try again at a different time. ISP congestion often appears as sustained degradation and spike clusters.');
        if (score >= 85 && recs.length === 0) recs.push('Keep it consistent: use wired where possible and avoid heavy network usage during play sessions.');

        return { title, summary, warnings, recs };
    }

    function drawLatencyChart(canvas, samples, spikeMs) {
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const w = canvas.width;
        const h = canvas.height;

        ctx.clearRect(0, 0, w, h);

        const okSamples = samples.filter(s => s.ok).map(s => s.rttMs);
        if (!okSamples.length) {
            ctx.fillStyle = '#6b7280';
            ctx.font = '13px system-ui, -apple-system, Segoe UI, Roboto, Arial';
            ctx.fillText('No samples captured.', 12, 24);
            return;
        }

        const med = median(okSamples);
        const maxRtt = Math.max(...okSamples, med + spikeMs * 3);
        const minRtt = Math.min(...okSamples);

        const pad = 12;
        const plotW = w - pad * 2;
        const plotH = h - pad * 2;

        const xFor = i => pad + (i / Math.max(1, samples.length - 1)) * plotW;
        const yFor = v => pad + (1 - (v - minRtt) / Math.max(1, maxRtt - minRtt)) * plotH;

        // Grid
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 1;
        ctx.setLineDash([4, 4]);
        for (let i = 1; i <= 3; i++) {
            const y = pad + (plotH * i) / 4;
            ctx.beginPath();
            ctx.moveTo(pad, y);
            ctx.lineTo(w - pad, y);
            ctx.stroke();
        }
        ctx.setLineDash([]);

        // Spike threshold
        const spikeLine = med + spikeMs;
        const spikeY = yFor(spikeLine);
        ctx.strokeStyle = 'rgba(245, 158, 11, 0.55)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(pad, spikeY);
        ctx.lineTo(w - pad, spikeY);
        ctx.stroke();

        // Line
        ctx.strokeStyle = '#2563eb';
        ctx.lineWidth = 2;
        ctx.beginPath();
        let started = false;
        samples.forEach((s, i) => {
            if (!s.ok) return;
            const x = xFor(i);
            const y = yFor(s.rttMs);
            if (!started) {
                ctx.moveTo(x, y);
                started = true;
            } else {
                ctx.lineTo(x, y);
            }
        });
        ctx.stroke();

        // Dots for loss
        ctx.fillStyle = '#dc2626';
        samples.forEach((s, i) => {
            if (s.ok) return;
            const x = xFor(i);
            ctx.beginPath();
            ctx.arc(x, h - pad, 3, 0, Math.PI * 2);
            ctx.fill();
        });

        // Labels
        ctx.fillStyle = '#6b7280';
        ctx.font = '12px system-ui, -apple-system, Segoe UI, Roboto, Arial';
        ctx.fillText(`median ${Math.round(med)} ms`, pad, pad + 12);
    }

    function safeText(text) {
        const span = document.createElement('span');
        span.textContent = text;
        return span.textContent;
    }

    function renderError(root, msg) {
        const body = $('[data-cgrt-body]', root);
        body.innerHTML = `<div class="cgrt__error">${safeText(msg)}</div>`;
    }

    function renderStepShell(root) {
        const body = $('[data-cgrt-body]', root);
        body.innerHTML = `
            <div class="cgrt__step cgrt__step--active" data-step="setup">
                <div class="cgrt__formGroup">
                    <label class="cgrt__label">${safeText(BOOT.i18n?.choosePlatform || 'Choose platform')}</label>
                    <select class="cgrt__select" data-cgrt-platform></select>
                </div>
                <div class="cgrt__formGroup">
                    <label class="cgrt__label">${safeText(BOOT.i18n?.chooseRegion || 'Choose region')}</label>
                    <select class="cgrt__select" data-cgrt-region></select>
                    <div class="cgrt__loadingHint" style="margin-top:8px">${safeText(BOOT.i18n?.disclaimerBody || '')}</div>
                </div>
                <div class="cgrt__actions">
                    <button class="cgrt__button" data-cgrt-start>${safeText(BOOT.i18n?.runTest || 'Run readiness test')}</button>
                </div>
            </div>

            <div class="cgrt__step" data-step="running">
                <div class="cgrt__progress">
                    <div class="cgrt__progressBar"><div class="cgrt__progressFill" data-cgrt-progress-fill></div></div>
                    <div class="cgrt__progressLabel" data-cgrt-progress-label></div>
                </div>
                <div class="cgrt__metricsGrid">
                    <div class="cgrt__metricCard">
                        <div class="cgrt__metricLabel">Latency (avg)</div>
                        <div class="cgrt__metricValue" data-cgrt-live-latency>— <span class="cgrt__metricUnit">ms</span></div>
                    </div>
                    <div class="cgrt__metricCard">
                        <div class="cgrt__metricLabel">Jitter</div>
                        <div class="cgrt__metricValue" data-cgrt-live-jitter>— <span class="cgrt__metricUnit">ms</span></div>
                    </div>
                    <div class="cgrt__metricCard">
                        <div class="cgrt__metricLabel">Packet loss</div>
                        <div class="cgrt__metricValue" data-cgrt-live-loss>— <span class="cgrt__metricUnit">%</span></div>
                    </div>
                    <div class="cgrt__metricCard">
                        <div class="cgrt__metricLabel">Stability</div>
                        <div class="cgrt__metricValue" data-cgrt-live-stability>— <span class="cgrt__metricUnit">/100</span></div>
                    </div>
                </div>
                <canvas class="cgrt__canvas" width="744" height="180" data-cgrt-canvas></canvas>
                <div class="cgrt__loadingHint">${safeText(BOOT.i18n?.running || 'Running diagnostic…')}</div>
            </div>

            <div class="cgrt__step" data-step="results"></div>
        `;
    }

    function setStep(root, stepName) {
        $all('.cgrt__step', root).forEach(el => {
            el.classList.toggle('cgrt__step--active', el.getAttribute('data-step') === stepName);
        });
    }

    async function loadConfig() {
        const url = `${BOOT.restUrl}/config`;
        const res = await fetch(url, { credentials: 'omit' });
        if (!res.ok) throw new Error('config');
        return await res.json();
    }

    function pickDefaultPlatformSlug(root) {
        const slug = root.getAttribute('data-platform');
        return slug && slug !== '' ? slug : 'auto';
    }

    function buildSetup(root, cfg) {
        const platforms = cfg.platforms || [];
        if (!platforms.length) {
            renderError(root, BOOT.i18n?.noEndpoints || 'No endpoints available.');
            return;
        }

        renderStepShell(root);

        const platformSelect = $('[data-cgrt-platform]', root);
        const regionSelect = $('[data-cgrt-region]', root);
        const startBtn = $('[data-cgrt-start]', root);
        const status = $('[data-cgrt-status]', root);

        platformSelect.innerHTML = platforms
            .map(p => `<option value="${String(p.slug).replace(/"/g, '')}">${safeText(p.name)}</option>`)
            .join('');

        const desired = pickDefaultPlatformSlug(root);
        if (desired && desired !== 'auto') {
            const opt = Array.from(platformSelect.options).find(o => o.value === desired);
            if (opt) platformSelect.value = desired;
        }

        function updateRegions() {
            const p = platforms.find(x => x.slug === platformSelect.value) || platforms[0];
            const eps = p.endpoints || [];
            regionSelect.innerHTML = eps
                .map(e => `<option value="${e.id}">${safeText(e.region)}</option>`)
                .join('');
            status.textContent = `Ready. ${p.name}`;
        }

        platformSelect.addEventListener('change', updateRegions);
        updateRegions();

        startBtn.addEventListener('click', async () => {
            startBtn.disabled = true;
            try {
                await runTest(root, cfg, platformSelect.value, Number(regionSelect.value));
            } finally {
                startBtn.disabled = false;
            }
        });
    }

    async function chooseBestEndpoint(platform, settings) {
        // Server-side testing handles endpoint realism. If a fallback is needed,
        // prefer the first enabled endpoint (typically the curated service entry).
        const endpoints = platform.endpoints || [];
        return endpoints[0];
    }

    function computeVolatility(okRtts, spikeMs) {
        if (okRtts.length < 3) return 1;
        const med = median(okRtts);
        let spikes = 0;
        for (const r of okRtts) {
            if (r > med + spikeMs) spikes++;
        }
        const spikeRate = spikes / okRtts.length;
        const sd = stddev(okRtts);
        // volatility combines spike rate and normalized sd.
        const sdNorm = clamp(sd / Math.max(10, med), 0, 1);
        return clamp(spikeRate * 0.7 + sdNorm * 0.3, 0, 1);
    }

    function computeResults(samples, spikeMs) {
        const ok = samples.filter(s => s.ok);
        const okRtts = ok.map(s => s.rttMs);
        const lossPct = samples.length ? ((samples.length - ok.length) / samples.length) * 100 : 100;

        if (!okRtts.length) {
            return {
                samples,
                latencyMedianMs: Infinity,
                latencyAvgMs: Infinity,
                latencyP95Ms: Infinity,
                jitterMs: Infinity,
                lossPct,
                volatility: 1,
                degradationMs: 0,
            };
        }

        const med = median(okRtts);
        const avg = mean(okRtts);
        const p95 = percentile(okRtts, 0.95);
        const jit = stddev(okRtts);
        const volatility = computeVolatility(okRtts, spikeMs);

        // sustained vs burst: compare first 20% vs last 20%
        const n = okRtts.length;
        const headN = Math.max(3, Math.floor(n * 0.2));
        const tailN = Math.max(3, Math.floor(n * 0.2));
        const headAvg = mean(okRtts.slice(0, headN));
        const tailAvg = mean(okRtts.slice(n - tailN));
        const degradationMs = tailAvg - headAvg;

        return {
            samples,
            latencyMedianMs: med,
            latencyAvgMs: avg,
            latencyP95Ms: p95,
            jitterMs: jit,
            lossPct,
            volatility,
            degradationMs,
        };
    }

    function computeScore(metrics, weights, tiers) {
        const w = normalizeWeights(weights);
        const subs = {
            latency: latencyScore(metrics.latencyMedianMs),
            jitter: jitterScore(metrics.jitterMs),
            loss: lossScore(metrics.lossPct),
            stability: stabilityScore(metrics.volatility),
        };
        const score = Math.round(
            subs.latency * w.latency +
            subs.jitter * w.jitter +
            subs.loss * w.loss +
            subs.stability * w.stability
        );
        const tier = tierForScore(score, tiers || {});
        return { score: clamp(score, 0, 100), tier, subs, weights: w };
    }

    function renderResults(root, platform, endpoint, metrics, scorePack, cfg) {
        const step = $('[data-step="results"]', root);
        const v = verdict(metrics, scorePack.score);

        if (platform.hasDisclaimer) {
            v.warnings.unshift('Limited diagnostic: This platform does not expose full cloud gaming infrastructure. Measurements are approximate.');
        }

        v.recs.push('This test measures routing quality (DNS, TCP, HTTP) to cloud service entry points, not exact in-game latency.');

        const details = [
            ['Platform', platform.name],
            ['Region / endpoint', `${endpoint.region}`],
            ['Latency (avg)', `${Math.round(metrics.latencyAvgMs)} ms`],
            ['Latency (median)', `${Math.round(metrics.latencyMedianMs)} ms`],
            ['Latency (p95)', `${Math.round(metrics.latencyP95Ms)} ms`],
            ['Jitter (stddev)', `${Math.round(metrics.jitterMs)} ms`],
            ['Packet loss', `${metrics.lossPct.toFixed(1)} %`],
            ['Stability', `${Math.round(scorePack.subs.stability)} / 100`],
            ['Volatility index', metrics.volatility.toFixed(2)],
            ['Degradation (end vs start)', `${Math.round(metrics.degradationMs)} ms`],
        ];

        const warningsHtml = v.warnings.length
            ? `<div class="cgrt__warnings"><div class="cgrt__sectionTitle">Cloud gaming killers detected</div><ul>${v.warnings
                .map(x => `<li>${safeText(x)}</li>`)
                .join('')}</ul></div>`
            : '';

        const recHtml = `<div class="cgrt__recommendations"><div class="cgrt__sectionTitle">Recommendations</div><ul>${v.recs
            .map(x => `<li>${safeText(x)}</li>`)
            .join('')}</ul></div>`;

        const tierClass = `cgrt__scoreTier--${scorePack.tier.key}`;

        step.innerHTML = `
            <div class="cgrt__scoreDisplay">
                <div class="cgrt__scoreBig">${scorePack.score}</div>
                <div class="cgrt__scoreTier ${tierClass}">${safeText(scorePack.tier.label)}</div>
            </div>

            <div class="cgrt__verdict">
                <div class="cgrt__verdictTitle">${safeText(v.title)}</div>
                <div class="cgrt__verdictSummary">${safeText(v.summary)}</div>
            </div>

            ${warningsHtml}
            ${recHtml}

            <div class="cgrt__metricsGrid">
                <div class="cgrt__metricCard">
                    <div class="cgrt__metricLabel">Latency subscore</div>
                    <div class="cgrt__metricValue">${scorePack.subs.latency}<span class="cgrt__metricUnit">/100</span></div>
                </div>
                <div class="cgrt__metricCard">
                    <div class="cgrt__metricLabel">Jitter subscore</div>
                    <div class="cgrt__metricValue">${scorePack.subs.jitter}<span class="cgrt__metricUnit">/100</span></div>
                </div>
                <div class="cgrt__metricCard">
                    <div class="cgrt__metricLabel">Loss subscore</div>
                    <div class="cgrt__metricValue">${scorePack.subs.loss}<span class="cgrt__metricUnit">/100</span></div>
                </div>
                <div class="cgrt__metricCard">
                    <div class="cgrt__metricLabel">Stability subscore</div>
                    <div class="cgrt__metricValue">${scorePack.subs.stability}<span class="cgrt__metricUnit">/100</span></div>
                </div>
            </div>

            <canvas class="cgrt__canvas" width="744" height="180" data-cgrt-canvas-final></canvas>

            <details class="cgrt__details">
                <summary class="cgrt__detailsTitle">${safeText(BOOT.i18n?.advancedDetails || 'Advanced details')}</summary>
                <div style="margin-top:10px">
                    ${details
                        .map(
                            ([k, val]) =>
                                `<div class="cgrt__detailsRow"><div class="cgrt__detailsLabel">${safeText(
                                    k
                                )}</div><div class="cgrt__detailsValue">${safeText(String(val))}</div></div>`
                        )
                        .join('')}
                </div>
            </details>

            <div class="cgrt__actions">
                <button class="cgrt__button" data-cgrt-retry>${safeText(BOOT.i18n?.retry || 'Retry')}</button>
                <button class="cgrt__button cgrt__button--secondary" data-cgrt-copy>${safeText(BOOT.i18n?.copy || 'Copy results')}</button>
            </div>
        `;

        const c = $('[data-cgrt-canvas-final]', root);
        drawLatencyChart(c, metrics.samples, cfg.settings.spikeMs);

        $('[data-cgrt-retry]', root).addEventListener('click', () => {
            buildSetup(root, cfg);
        });

        $('[data-cgrt-copy]', root).addEventListener('click', async (e) => {
            const payload = {
                score: scorePack.score,
                tier: scorePack.tier.label,
                platform: platform.name,
                region: endpoint.region,
                latency_avg_ms: Math.round(metrics.latencyAvgMs),
                latency_p95_ms: Math.round(metrics.latencyP95Ms),
                jitter_ms: Math.round(metrics.jitterMs),
                loss_pct: Number(metrics.lossPct.toFixed(1)),
                stability: scorePack.subs.stability,
            };

            try {
                await navigator.clipboard.writeText(JSON.stringify(payload, null, 2));
                e.target.textContent = BOOT.i18n?.copied || 'Copied';
                setTimeout(() => (e.target.textContent = BOOT.i18n?.copy || 'Copy results'), 1200);
            } catch (err) {
                // Fallback
                const ta = document.createElement('textarea');
                ta.value = JSON.stringify(payload, null, 2);
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
            }
        });
    }

    async function runTest(root, cfg, platformSlug, endpointId) {
        const platforms = cfg.platforms || [];
        const platform = platforms.find(p => p.slug === platformSlug) || platforms[0];
        const settings = cfg.settings || {};

        const status = $('[data-cgrt-status]', root);
        status.textContent = 'Selecting best endpoint…';

        // If user selected a region explicitly, use it; otherwise pick best.
        let endpoint = platform.endpoints.find(e => e.id === endpointId);
        if (!endpoint) {
            endpoint = await chooseBestEndpoint(platform, settings);
        }

        if (!endpoint) {
            renderError(root, BOOT.i18n?.noEndpoints || 'No endpoints available.');
            return;
        }

        setStep(root, 'running');

        const fill = $('[data-cgrt-progress-fill]', root);
        const label = $('[data-cgrt-progress-label]', root);
        const liveLatency = $('[data-cgrt-live-latency]', root);
        const liveJitter = $('[data-cgrt-live-jitter]', root);
        const liveLoss = $('[data-cgrt-live-loss]', root);
        const liveStability = $('[data-cgrt-live-stability]', root);
        const canvas = $('[data-cgrt-canvas]', root);

        const durationSec = settings.testDurationSeconds || 18;
        const intervalMs = settings.sampleIntervalMs || 350;
        const spikeMs = settings.spikeMs || 35;

        status.textContent = `Testing ${platform.name} — ${endpoint.region}`;

        const totalSamples = Math.max(10, Math.floor((durationSec * 1000) / intervalMs));

        let serverSamples = [];
        try {
            const testResponse = await fetch(`${BOOT.restUrl}/test`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ platformId: platform.id, endpointId: endpoint.id }),
            });
            if (testResponse.ok) {
                const testData = await testResponse.json();
                serverSamples = Array.isArray(testData.samples) ? testData.samples : [];
            }
        } catch (e) {
            serverSamples = [];
        }

        const samples = [];

        for (let i = 0; i < totalSamples; i++) {
            const s = serverSamples[i] || {};
            samples.push({
                i,
                ok: !!s.ok,
                rttMs: Number.isFinite(Number(s.rttMs)) ? Number(s.rttMs) : 0,
                status: s.status || null,
                error: s.error || null,
                t: Date.now(),
            });

            const metrics = computeResults(samples, spikeMs);
            const stability = stabilityScore(metrics.volatility);

            liveLatency.textContent = fmtMs(metrics.latencyAvgMs);
            liveJitter.textContent = fmtMs(metrics.jitterMs);
            liveLoss.textContent = fmtPct(metrics.lossPct);
            liveStability.textContent = String(stability);

            const prog = (i + 1) / totalSamples;
            fill.style.width = `${Math.round(prog * 100)}%`;
            label.textContent = `Sample ${i + 1} of ${totalSamples}`;

            drawLatencyChart(canvas, samples, spikeMs);

            await new Promise(res => setTimeout(res, intervalMs));
        }

        const metrics = computeResults(samples, spikeMs);
        const scorePack = computeScore(
            metrics,
            platform.weights || BOOT.settings?.default_weights || { latency: 0.4, jitter: 0.25, loss: 0.25, stability: 0.10 },
            settings.tiers || BOOT.settings?.tiers || {}
        );

        // Attempt optional result submission (may be disabled server-side).
        try {
            await fetch(`${BOOT.restUrl}/submit`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    platformId: platform.id,
                    endpointId: endpoint.id,
                    score: scorePack.score,
                    tier: scorePack.tier.label,
                    metrics: {
                        latency_ms: metrics.latencyMedianMs,
                        jitter_ms: metrics.jitterMs,
                        loss_pct: metrics.lossPct,
                        volatility: metrics.volatility,
                    },
                }),
            });
        } catch (e) {
            // ignore
        }

        renderResults(root, platform, endpoint, metrics, scorePack, cfg);
        setStep(root, 'results');
        status.textContent = `Completed — ${platform.name}`;
    }

    async function bootContainer(root) {
        try {
            const cfg = await loadConfig();
            buildSetup(root, cfg);
        } catch (e) {
            renderError(root, BOOT.i18n?.configError || 'Unable to load test configuration.');
        }
    }

    function init() {
        const roots = $all('.cgrt');
        roots.forEach(root => bootContainer(root));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

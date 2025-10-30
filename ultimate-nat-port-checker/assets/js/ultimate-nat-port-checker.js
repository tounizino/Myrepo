/**
 * Ultimate NAT & Port Checker – Frontend Logic (2026 Edition)
 * Handles NAT intelligence, port diagnostics, device profiling, and interactive UI
 */

(function () {
  'use strict';

  const STORAGE_KEYS = {
    theme: 'UNPC_THEME',
  };

  const MAX_PORTS = 40;

  const App = {
    config: null,
    container: null,
    prefetchedIP: null,
    natResult: null,

    init() {
      if (typeof UNPC_DATA === 'undefined') {
        console.warn('UNPC: configuration missing.');
        return;
      }

      this.config = UNPC_DATA;
      this.container = document.querySelector('.unpc-container');

      if (!this.container) {
        console.warn('UNPC: container not found.');
        return;
      }

      if (!this.config.settings.enableAnimations) {
        this.container.classList.add('unpc-no-animations');
      }

      const storedTheme = this.getStoredTheme();
      const defaultTheme = storedTheme || this.container.dataset.defaultTheme || this.config.settings.defaultTheme || 'light';
      this.applyTheme(defaultTheme, false);

      this.attachEventListeners();
      this.populatePortPresets();
      this.prefetchClientIP();
      this.gatherDeviceInfo();
    },

    getStoredTheme() {
      try {
        const value = localStorage.getItem(STORAGE_KEYS.theme);
        return value && ['light', 'dark'].includes(value) ? value : null;
      } catch (error) {
        return null;
      }
    },

    applyTheme(theme, persist = true) {
      if (!['light', 'dark'].includes(theme)) {
        theme = 'light';
      }

      this.container.dataset.theme = theme;

      document.querySelectorAll('[data-theme-target]').forEach((btn) => {
        const isActive = btn.dataset.themeTarget === theme;
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
      });

      if (persist) {
        try {
          localStorage.setItem(STORAGE_KEYS.theme, theme);
        } catch (error) {
          // ignore persistence errors
        }
      }
    },

    attachEventListeners() {
      document.querySelectorAll('[data-theme-target]').forEach((btn) => {
        btn.addEventListener('click', () => this.applyTheme(btn.dataset.themeTarget));
      });

      const natButton = document.querySelector('[data-action="run-nat"]');
      if (natButton) {
        natButton.addEventListener('click', () => this.runNATCheck());
      }

      const portButton = document.querySelector('[data-action="run-port-scan"]');
      if (portButton) {
        portButton.addEventListener('click', () => this.runPortCheck());
      }

      const resetButton = document.querySelector('[data-action="reset-ports"]');
      if (resetButton) {
        resetButton.addEventListener('click', () => this.resetPortSection());
      }

      const myIpButton = document.querySelector('[data-action="use-my-ip"]');
      if (myIpButton) {
        myIpButton.addEventListener('click', () => this.fillMyIP());
      }

      window.addEventListener('resize', () => {
        this.updateDeviceResolution();
      });
    },

    setStatusMessage(area, message) {
      const el = document.querySelector(`[data-status="${area}"]`);
      if (el) {
        el.textContent = message;
      }
    },

    clearStatusMessage(area) {
      const el = document.querySelector(`[data-status="${area}"]`);
      if (el) {
        el.innerHTML = '&nbsp;';
      }
    },

    async prefetchClientIP() {
      try {
        const response = await fetch('https://ipapi.co/json/');
        if (!response.ok) {
          throw new Error('Request failed');
        }

        const data = await response.json();
        this.prefetchedIP = {
          ip: data.ip || '',
          location: data.city && data.country_name ? `${data.city}, ${data.country_name}` : '',
          isp: data.org || data.isp || '',
        };

        this.updateMetric('ip-address', this.prefetchedIP.ip);
        if (this.prefetchedIP.location) {
          this.updateMetric('location', this.prefetchedIP.location);
        }
        if (this.prefetchedIP.isp) {
          this.updateMetric('isp', this.prefetchedIP.isp);
        }
      } catch (error) {
        console.info('UNPC: Unable to prefetch client IP', error);
      }
    },

    updateMetric(field, value, force = false) {
      const el = document.querySelector(`[data-field="${field}"]`);
      if (!el) return;

      if (!force && el.textContent && el.textContent.trim() !== '—' && el.textContent.trim() !== '') {
        return;
      }

      el.textContent = value || '—';
    },

    async runNATCheck() {
      const button = document.querySelector('[data-action="run-nat"]');
      const statusCard = document.querySelector('.unpc-nat__status');
      const typeEl = statusCard ? statusCard.querySelector('.unpc-nat__type') : null;
      const labelEl = statusCard ? statusCard.querySelector('.unpc-nat__label') : null;
      const descEl = statusCard ? statusCard.querySelector('.unpc-nat__description') : null;

      this.setButtonBusy(button, true);

      if (statusCard) {
        statusCard.dataset.natState = 'checking';
        statusCard.dataset.natTier = 'idle';
      }
      if (typeEl) typeEl.textContent = '…';
      if (labelEl) labelEl.textContent = this.config.strings.checkingNat;
      if (descEl) descEl.textContent = '';

      this.setStatusMessage('nat', this.config.strings.checkingNat);

      try {
        const start = performance.now();
        const result = await this.detectNATViaWebRTC();
        const elapsed = Math.round(performance.now() - start);
        this.natResult = result;

        const tier = this.determineNATTier(result);

        if (statusCard) {
          statusCard.dataset.natState = 'complete';
          statusCard.dataset.natTier = tier;
        }
        if (typeEl) typeEl.textContent = this.getNATLabel(tier);
        if (labelEl) labelEl.textContent = this.getNATLabelString(tier);
        if (descEl) descEl.textContent = this.getNATDescription(tier);

        this.updateMetric('ip-address', result.publicIP, true);
        this.updateMetric('location', result.location, true);
        this.updateMetric('isp', result.isp, true);
        this.updateMetric('stun-latency', `${elapsed} ms`, true);

        this.populateNATTechnicalInfo(result);
        this.setStatusMessage('nat', this.config.strings.natCheckComplete);
        setTimeout(() => this.clearStatusMessage('nat'), 3500);
      } catch (error) {
        console.error('UNPC NAT check error', error);
        if (statusCard) {
          statusCard.dataset.natState = 'error';
          statusCard.dataset.natTier = 'error';
        }
        if (typeEl) typeEl.textContent = 'ERR';
        if (labelEl) labelEl.textContent = 'Analysis failed';
        if (descEl) descEl.textContent = error.message || this.config.strings.apiError;
        this.setStatusMessage('nat', error.message || this.config.strings.apiError);
      } finally {
        this.setButtonBusy(button, false);
      }
    },

    detectNATViaWebRTC() {
      return new Promise((resolve, reject) => {
        if (typeof RTCPeerConnection === 'undefined') {
          reject(new Error('WebRTC is not supported in this browser.'));
          return;
        }

        const rtc = new RTCPeerConnection({
          iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
          ],
        });

        const parsedCandidates = [];
        let resolved = false;
        const timeoutMs = Math.max((parseInt(this.config.settings.apiTimeout, 10) || 10) * 1000, 6000);

        const finish = (callback) => {
          if (resolved) {
            return;
          }
          resolved = true;
          rtc.onicecandidate = null;
          if (typeof rtc.close === 'function') {
            rtc.close();
          }
          callback();
        };

        rtc.onicecandidate = (event) => {
          if (event.candidate && event.candidate.candidate) {
            const parsed = this.parseCandidate(event.candidate.candidate);
            if (parsed) {
              parsedCandidates.push(parsed);
            }
          } else {
            finish(async () => {
              if (!parsedCandidates.length) {
                reject(new Error('No ICE candidates discovered. Check firewall or VPN restrictions.'));
                return;
              }

              const srflx = parsedCandidates.filter((c) => c.type === 'srflx');
              const relay = parsedCandidates.filter((c) => c.type === 'relay');
              const host = parsedCandidates.filter((c) => c.type === 'host');

              const portPreserved = srflx.some((remote) => host.some((local) => local.port === remote.port));
              const publicCandidate = srflx[0] || host.find((c) => this.isPublicIP(c.ip)) || srflx[0];
              const publicIP = publicCandidate ? publicCandidate.ip : (this.prefetchedIP ? this.prefetchedIP.ip : '');

              const mappingBehavior = this.evaluateMappingBehavior({ host, srflx, relay, portPreserved });
              const filteringBehavior = this.evaluateFilteringBehavior({ host, srflx, relay });

              try {
                const geo = publicIP && this.isPublicIP(publicIP)
                  ? await this.fetchGeoIP(publicIP)
                  : { location: this.prefetchedIP?.location || 'Unknown', isp: this.prefetchedIP?.isp || 'Unknown' };

                resolve({
                  publicIP: publicIP || this.prefetchedIP?.ip || 'Unknown',
                  location: geo.location || 'Unknown',
                  isp: geo.isp || 'Unknown',
                  candidates: parsedCandidates,
                  portPreserved,
                  hasRelay: relay.length > 0,
                  hasSrflx: srflx.length > 0,
                  hasHost: host.length > 0,
                  mappingBehavior,
                  filteringBehavior,
                });
              } catch (error) {
                console.info('UNPC: Geo lookup fallback', error);
                resolve({
                  publicIP: publicIP || this.prefetchedIP?.ip || 'Unknown',
                  location: this.prefetchedIP?.location || 'Unknown',
                  isp: this.prefetchedIP?.isp || 'Unknown',
                  candidates: parsedCandidates,
                  portPreserved,
                  hasRelay: relay.length > 0,
                  hasSrflx: srflx.length > 0,
                  hasHost: host.length > 0,
                  mappingBehavior,
                  filteringBehavior,
                });
              }
            });
          }
        };

        try {
          rtc.createDataChannel('unpc-check');
          rtc.createOffer()
            .then((offer) => rtc.setLocalDescription(offer))
            .catch((error) => finish(() => reject(error)));
        } catch (error) {
          finish(() => reject(error));
        }

        setTimeout(() => {
          finish(() => {
            if (!parsedCandidates.length) {
              reject(new Error('STUN request timed out. Network may be blocking UDP outbound traffic.'));
            }
          });
        }, timeoutMs);
      });
    },

    parseCandidate(candidateString) {
      if (!candidateString) {
        return null;
      }

      const parts = candidateString.trim().split(/\s+/);
      if (parts.length < 8) {
        return null;
      }

      const foundation = parts[0].split(':')[1];
      const component = parseInt(parts[1], 10);
      const protocol = parts[2];
      const priority = parseInt(parts[3], 10);
      const ip = parts[4];
      const port = parseInt(parts[5], 10);
      const typeIndex = parts.indexOf('typ');
      const type = typeIndex !== -1 ? parts[typeIndex + 1] : 'unknown';
      const raddrIndex = parts.indexOf('raddr');
      const raddr = raddrIndex !== -1 ? parts[raddrIndex + 1] : '';
      const rportIndex = parts.indexOf('rport');
      const rport = rportIndex !== -1 ? parseInt(parts[rportIndex + 1], 10) : null;

      return {
        foundation,
        component,
        protocol,
        priority,
        ip,
        port,
        type,
        raddr,
        rport,
        raw: candidateString,
      };
    },

    evaluateMappingBehavior({ host, srflx, relay, portPreserved }) {
      if (!srflx.length && relay.length) {
        return 'Symmetric or port-dependent mapping';
      }
      if (srflx.length && portPreserved && !relay.length) {
        return 'Endpoint-independent mapping (full cone)';
      }
      if (srflx.length && !portPreserved) {
        return 'Address-restricted or port-restricted mapping';
      }
      if (host.length && !srflx.length) {
        return 'Direct (no NAT detected)';
      }
      return 'Indeterminate mapping behaviour';
    },

    evaluateFilteringBehavior({ host, srflx, relay }) {
      if (relay.length) {
        return 'Port-dependent filtering (TURN relay required)';
      }
      if (srflx.length) {
        return 'Address or endpoint-independent filtering';
      }
      if (host.length && !srflx.length) {
        return 'Open filtering (no NAT)';
      }
      return 'Filtering behaviour unknown';
    },

    isPublicIP(ip) {
      if (!ip) return false;
      const privatePatterns = [/^10\./, /^172\.(1[6-9]|2[0-9]|3[01])\./, /^192\.168\./, /^127\./, /^169\.254\./, /^0\./];
      return !privatePatterns.some((pattern) => pattern.test(ip));
    },

    async fetchGeoIP(ip) {
      const response = await fetch(`https://ipapi.co/${ip}/json/`);
      if (!response.ok) {
        throw new Error('GeoIP lookup failed');
      }
      const data = await response.json();
      return {
        location: data.city && data.country_name ? `${data.city}, ${data.country_name}` : 'Unknown',
        isp: data.org || data.isp || 'Unknown',
      };
    },

    determineNATTier(info) {
      if (info.hasHost && !info.hasSrflx && !info.hasRelay) {
        return 'type1';
      }
      if (info.hasSrflx && info.portPreserved && !info.hasRelay) {
        return 'type1';
      }
      if (info.hasSrflx && !info.hasRelay) {
        return 'type2';
      }
      return 'type3';
    },

    getNATLabel(tier) {
      const labels = {
        type1: 'Type 1',
        type2: 'Type 2',
        type3: 'Type 3',
      };
      return labels[tier] || 'Unknown';
    },

    getNATLabelString(tier) {
      const labels = {
        type1: 'Open',
        type2: 'Moderate',
        type3: 'Strict',
      };
      return labels[tier] || 'Unknown';
    },

    getNATDescription(tier) {
      const descriptions = {
        type1: this.config.strings.natType1,
        type2: this.config.strings.natType2,
        type3: this.config.strings.natType3,
      };
      return descriptions[tier] || '';
    },

    populateNATTechnicalInfo(info) {
      const candidateList = document.querySelector('[data-field="ice-candidates"]');
      if (candidateList) {
        candidateList.innerHTML = '';
        info.candidates.slice(0, 10).forEach((candidate, index) => {
          const li = document.createElement('li');
          const mapped = candidate.raddr && candidate.rport ? ` ↔ ${candidate.raddr}:${candidate.rport}` : '';
          li.textContent = `${index + 1}. ${candidate.type.toUpperCase()} • ${candidate.ip}:${candidate.port} (${candidate.protocol.toUpperCase()})${mapped}`;
          candidateList.appendChild(li);
        });
        if (info.candidates.length > 10) {
          const more = document.createElement('li');
          more.textContent = `+${info.candidates.length - 10} additional candidates`; 
          candidateList.appendChild(more);
        }
      }

      const summaryList = document.querySelector('[data-field="nat-summary"]');
      if (summaryList) {
        const items = [
          `Mapping behaviour: ${info.mappingBehavior}`,
          `Filtering behaviour: ${info.filteringBehavior}`,
          info.portPreserved ? 'Port preservation confirmed (favourable for peer-to-peer).' : 'Port translation detected (may require forwarding).',
          info.hasRelay ? 'TURN relay was required for connectivity.' : 'No TURN relay required; direct traversal successful.',
        ];

        summaryList.innerHTML = items.map((item) => `<li>${item}</li>`).join('');
      }

      const recommendationsList = document.querySelector('[data-field="nat-recommendations"]');
      if (recommendationsList) {
        const tier = this.determineNATTier(info);
        let recommendations = [];

        if (tier === 'type1') {
          recommendations = [
            'Maintain current configuration – your network is fully optimised.',
            'Optionally enable QoS/DSCP to prioritise gaming traffic.',
          ];
        } else if (tier === 'type2') {
          recommendations = [
            'Enable UPnP or manually forward required cloud gaming ports.',
            'Assign a static IP address to your console or gaming PC.',
            'Check for double NAT (modem + router) and bridge extra hardware.',
          ];
        } else {
          recommendations = [
            'Place your gaming device in a DMZ or use dedicated port forwarding rules.',
            'Switch the router to bridge mode if cascading multiple routers.',
            'Enable “Full Cone NAT”, “Open NAT”, or NAT acceleration where available.',
            'Consider router firmware updates for improved NAT traversal.',
          ];
        }

        recommendationsList.innerHTML = recommendations.map((item) => `<li>${item}</li>`).join('');
      }
    },

    async runPortCheck() {
      const button = document.querySelector('[data-action="run-port-scan"]');
      const portsInput = document.querySelector('[data-field="port-list"]');
      const hostInput = document.querySelector('[data-field="port-host"]');

      if (!portsInput) {
        return;
      }

      const raw = portsInput.value.trim();
      if (!raw) {
        this.setStatusMessage('port', this.config.strings.noPortsProvided);
        return;
      }

      const ports = this.parsePortInput(raw);
      if (!ports.length) {
        this.setStatusMessage('port', 'No valid ports detected. Use formats like 3478, 3478-3480/udp.');
        return;
      }

      this.setButtonBusy(button, true);
      this.setStatusMessage('port', this.config.strings.portCheckRunning);

      const payload = {
        host: hostInput ? hostInput.value.trim() : '',
        ports,
        timeout: this.config.settings.apiTimeout,
      };

      try {
        const response = await fetch(this.config.restUrl + 'port-check', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': this.config.nonce,
          },
          body: JSON.stringify(payload),
        });

        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.message || this.config.strings.apiError);
        }

        const data = await response.json();
        this.displayPortResults(data);
        this.setStatusMessage('port', this.config.strings.portCheckComplete);
        setTimeout(() => this.clearStatusMessage('port'), 3500);
      } catch (error) {
        console.error('UNPC Port check error', error);
        this.setStatusMessage('port', error.message || this.config.strings.apiError);
      } finally {
        this.setButtonBusy(button, false);
      }
    },

    parsePortInput(input) {
      const tokens = input
        .split(/[,\n]/)
        .map((token) => token.trim())
        .filter(Boolean);

      const map = new Map();

      tokens.forEach((token) => {
        const protocolMatch = token.match(/\/(tcp|udp)$/i);
        const protocol = protocolMatch ? protocolMatch[1].toLowerCase() : 'tcp';
        const clean = token.replace(/\/(tcp|udp)$/i, '').trim();

        if (clean.includes('-')) {
          const [start, end] = clean.split('-').map((value) => parseInt(value, 10));
          if (!isNaN(start) && !isNaN(end) && start <= end) {
            for (let port = start; port <= end && port <= 65535; port += 1) {
              const key = `${port}/${protocol}`;
              if (!map.has(key)) {
                map.set(key, { port, protocol });
              }
              if (map.size >= MAX_PORTS) break;
            }
          }
        } else {
          const port = parseInt(clean, 10);
          if (!isNaN(port) && port >= 1 && port <= 65535) {
            const key = `${port}/${protocol}`;
            if (!map.has(key) && map.size < MAX_PORTS) {
              map.set(key, { port, protocol });
            }
          }
        }
      });

      return Array.from(map.values());
    },

    displayPortResults(data) {
      const tbody = document.querySelector('[data-field="port-results"]');
      const summaryEl = document.querySelector('[data-field="port-summary"]');

      if (!tbody) {
        return;
      }

      tbody.innerHTML = '';

      if (!data.results || !data.results.length) {
        tbody.innerHTML = '<tr class="unpc-table__empty"><td colspan="6">No results returned from the scan.</td></tr>';
        if (summaryEl) summaryEl.innerHTML = '';
        return;
      }

      const statusClass = {
        open: 'unpc-status-chip unpc-status-chip--open',
        closed: 'unpc-status-chip unpc-status-chip--closed',
        info: 'unpc-status-chip unpc-status-chip--info',
        indeterminate: 'unpc-status-chip unpc-status-chip--info',
      };

      const statusLabel = {
        open: 'Open',
        closed: 'Closed',
        info: 'Info',
        indeterminate: 'Pending',
      };

      data.results.forEach((result) => {
        const tr = document.createElement('tr');
        const label = statusLabel[result.status] || statusLabel.info;
        const cls = statusClass[result.status] || statusClass.info;
        const response = typeof result.response_time === 'number' ? `${result.response_time} ms` : '—';

        tr.innerHTML = `
          <td><strong>${result.port}</strong></td>
          <td>${result.protocol}</td>
          <td>${result.service}</td>
          <td><span class="${cls}">${label}</span></td>
          <td>${response}</td>
          <td>${result.message || '—'}</td>
        `;

        tbody.appendChild(tr);
      });

      const openCount = data.results.filter((item) => item.status === 'open').length;
      const closedCount = data.results.filter((item) => item.status === 'closed').length;
      const infoCount = data.results.length - openCount - closedCount;

      if (summaryEl) {
        summaryEl.innerHTML = `
          <strong>Summary:</strong> ${data.results.length} port(s) analysed on ${data.resolved_host || data.host}.
          <span class="unpc-summary-chip unpc-summary-chip--open">${openCount} open</span>
          <span class="unpc-summary-chip unpc-summary-chip--closed">${closedCount} closed</span>
          ${infoCount > 0 ? `<span class="unpc-summary-chip unpc-summary-chip--info">${infoCount} informational</span>` : ''}
        `;
      }
    },

    populatePortPresets() {
      const container = document.querySelector('[data-field="port-presets"]');
      if (!container || !Array.isArray(this.config.portPresets)) {
        return;
      }

      this.config.portPresets.forEach((preset) => {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'unpc-preset-chip';
        chip.textContent = preset.label;
        chip.title = preset.description;
        chip.addEventListener('click', () => {
          this.addPortsToInput(preset);
          chip.classList.add('is-applied');
          setTimeout(() => chip.classList.remove('is-applied'), 600);
        });

        container.appendChild(chip);
      });
    },

    addPortsToInput(preset) {
      const input = document.querySelector('[data-field="port-list"]');
      if (!input) {
        return;
      }

      const newTokens = preset.entries.map((entry) => {
        const suffix = entry.protocol.toLowerCase() === 'udp' ? '/udp' : '';
        return entry.ports
          .split(',')
          .map((token) => token.trim())
          .filter(Boolean)
          .map((token) => `${token}${suffix}`)
          .join(', ');
      });

      const addition = newTokens.filter(Boolean).join(', ');
      const current = input.value.trim();
      input.value = current ? `${current}, ${addition}` : addition;
    },

    resetPortSection() {
      const listEl = document.querySelector('[data-field="port-list"]');
      const hostEl = document.querySelector('[data-field="port-host"]');
      const tbody = document.querySelector('[data-field="port-results"]');
      const summaryEl = document.querySelector('[data-field="port-summary"]');

      if (listEl) listEl.value = '';
      if (hostEl) hostEl.value = '';
      if (tbody) {
        tbody.innerHTML = '<tr class="unpc-table__empty"><td colspan="6">Add ports and launch the scan to see results.</td></tr>';
      }
      if (summaryEl) summaryEl.innerHTML = '';

      this.setStatusMessage('port', 'Port diagnostics reset.');
      setTimeout(() => this.clearStatusMessage('port'), 2500);
    },

    fillMyIP() {
      const hostEl = document.querySelector('[data-field="port-host"]');
      if (!hostEl) return;

      const candidate = (this.natResult && this.isPublicIP(this.natResult.publicIP))
        ? this.natResult.publicIP
        : (this.prefetchedIP && this.prefetchedIP.ip ? this.prefetchedIP.ip : '');

      if (candidate) {
        hostEl.value = candidate;
        this.setStatusMessage('port', 'Your public IP has been inserted.');
      } else {
        this.setStatusMessage('port', 'Run the NAT analysis first to detect your public IP.');
      }

      setTimeout(() => this.clearStatusMessage('port'), 2500);
    },

    gatherDeviceInfo() {
      const platform = navigator.userAgentData && navigator.userAgentData.platform
        ? navigator.userAgentData.platform
        : (navigator.platform || 'Unknown');
      const browser = this.detectBrowser();
      const connection = this.detectConnectionType();
      const bandwidth = this.estimateBandwidth();
      const cores = navigator.hardwareConcurrency || 'Unknown';

      this.setDeviceField('platform', platform);
      this.setDeviceField('browser', browser);
      this.setDeviceField('connection', connection);
      this.setDeviceField('bandwidth', bandwidth);
      this.updateDeviceResolution();
      this.setDeviceField('cores', cores);
    },

    detectBrowser() {
      const ua = navigator.userAgent;
      if (/Edg\//.test(ua)) return 'Microsoft Edge';
      if (/OPR\//.test(ua) || /Opera/.test(ua)) return 'Opera';
      if (/Chrome\//.test(ua) && !/Edg\//.test(ua)) return 'Google Chrome';
      if (/Firefox\//.test(ua)) return 'Mozilla Firefox';
      if (/Safari\//.test(ua) && !/Chrome\//.test(ua)) return 'Safari';
      return 'Unknown Browser';
    },

    detectConnectionType() {
      if ('connection' in navigator && navigator.connection) {
        return navigator.connection.effectiveType || 'Unknown';
      }
      return 'Unknown';
    },

    estimateBandwidth() {
      if ('connection' in navigator && navigator.connection && navigator.connection.downlink) {
        return `~${navigator.connection.downlink} Mbps`;
      }
      return 'Unknown';
    },

    updateDeviceResolution() {
      const resolution = `${window.screen.width} × ${window.screen.height}`;
      this.setDeviceField('resolution', resolution);
    },

    setDeviceField(field, value) {
      const el = document.querySelector(`[data-device="${field}"]`);
      if (el) {
        el.textContent = value || 'Unknown';
      }
    },

    setButtonBusy(button, busy) {
      if (!button) return;
      const label = button.querySelector('.unpc-button__label');
      if (!label) return;

      if (!label.dataset.labelIdle) {
        label.dataset.labelIdle = label.textContent;
      }
      if (!label.dataset.labelBusy) {
        label.dataset.labelBusy = 'Working…';
      }

      button.disabled = !!busy;
      label.textContent = busy ? label.dataset.labelBusy : label.dataset.labelIdle;
    },
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => App.init());
  } else {
    App.init();
  }

  window.UNPC = App;
})();

/**
 * Ultimate NAT & Port Checker Frontend JavaScript
 */

(function($) {
    'use strict';

    const UNPC = {
        init: function() {
            if (typeof unpcData === 'undefined') {
                return;
            }
            this.applyCustomColors();
            this.applyFontSize();
            this.prefillNetworkMeta();
            this.setupThemeToggle();
            this.setupNATChecker();
            this.setupPortChecker();
            this.setupDeviceInfo();
            this.setupRouterSearch();
        },

        applyCustomColors: function() {
            if (!unpcData.settings) {
                return;
            }
            const settings = unpcData.settings;
            const root = document.documentElement;
            const map = {
                primary_color: '--unpc-primary',
                secondary_color: '--unpc-secondary',
                nat_type1_color: '--unpc-nat-type1',
                nat_type2_color: '--unpc-nat-type2',
                nat_type3_color: '--unpc-nat-type3',
                port_open_color: '--unpc-port-open',
                port_closed_color: '--unpc-port-closed'
            };
            Object.keys(map).forEach(function(key) {
                if (settings[key]) {
                    root.style.setProperty(map[key], settings[key]);
                }
            });
            if (settings.container_width) {
                root.style.setProperty('--unpc-container-width', settings.container_width + 'px');
            }
        },

        applyFontSize: function() {
            const preset = (unpcData.settings && unpcData.settings.font_size_preset) ? unpcData.settings.font_size_preset : 'small';
            $('.unpc-wrapper, .unpc-standalone').attr('data-font-size', preset);
        },

        prefillNetworkMeta: function() {
            const self = this;
            $.ajax({
                url: unpcData.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'unpc_get_ip_info',
                    nonce: unpcData.nonce
                }
            }).done(function(response) {
                if (response && response.success && response.data) {
                    const ip = response.data.ip || '';
                    if (ip) {
                        self.setHostInputs(ip);
                    }
                }
            });
        },

        setHostInputs: function(ip) {
            if (!ip) {
                return;
            }
            ['#port-host', '#port-host-standalone'].forEach(function(selector) {
                const $el = $(selector);
                if ($el.length && !$el.val()) {
                    $el.val(ip);
                }
            });
        },

        setupThemeToggle: function() {
            const wrappers = $('.unpc-wrapper, .unpc-standalone');
            const savedTheme = localStorage.getItem('unpc_theme');
            if (savedTheme) {
                wrappers.attr('data-theme', savedTheme);
            }
            $(document).on('click', '.unpc-theme-toggle', function() {
                const currentTheme = wrappers.first().attr('data-theme') || 'dark';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                wrappers.attr('data-theme', newTheme);
                localStorage.setItem('unpc_theme', newTheme);
            });
        },

        setupNATChecker: function() {
            const self = this;
            $(document).on('click', '#unpc-check-nat, #unpc-check-nat-standalone', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const suffix = $btn.is('#unpc-check-nat-standalone') ? '-standalone' : '';
                if ($btn.hasClass('loading')) {
                    return;
                }
                $btn.addClass('loading').prop('disabled', true);
                $.ajax({
                    url: unpcData.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'unpc_check_nat',
                        nonce: unpcData.nonce
                    }
                }).done(function(response) {
                    if (response && response.success && response.data) {
                        self.displayNATResults(response.data, suffix);
                        self.setHostInputs(response.data.ip || '');
                        self.runWebRTCNATAnalysis(response.data.ip || '', suffix);
                    } else {
                        self.showError('Unable to complete NAT analysis. Please retry.', suffix);
                    }
                }).fail(function() {
                    self.showError('Network error while checking NAT. Please verify your connection.', suffix);
                }).always(function() {
                    $btn.removeClass('loading').prop('disabled', false);
                });
            });
        },

        displayNATResults: function(data, suffix) {
            const $results = $('#unpc-nat-results' + suffix);
            const typeName = data.nat_type_name || 'Type 2 - Moderate';
            const typeNumber = data.nat_type || 2;
            const description = data.nat_description || 'Standard NAT configuration suitable for most online services';
            this.updateNATStatus(typeNumber, typeName, description, suffix);
            this.fillText('#nat-public-ip' + suffix, data.ip);
            this.fillText('#nat-isp' + suffix, data.isp);
            this.fillText('#nat-country' + suffix, data.country);
            this.fillText('#nat-region' + suffix, data.region);
            this.fillText('#nat-city' + suffix, data.city);
            this.fillText('#nat-timezone' + suffix, data.timezone);
            this.fillText('#nat-org' + suffix, data.org);
            this.fillText('#nat-asn' + suffix, data.as);
            this.fillText('#nat-connection-type' + suffix, data.connection_type);
            this.fillText('#nat-lat' + suffix, data.lat);
            this.fillText('#nat-lon' + suffix, data.lon);
            this.fillText('#nat-postal' + suffix, data.postal);
            this.fillText('#nat-symmetric' + suffix, 'Analysing…');
            $('#nat-ice-details' + suffix).text('Gathering peer-to-peer routing signatures…');
            $results.fadeIn(300);
        },

        updateNATStatus: function(typeNumber, typeName, description, suffix) {
            const advice = this.generateNATAdvice(typeNumber, false);
            this.fillText('#nat-type-value' + suffix, typeName);
            const $badge = $('#nat-status-badge' + suffix);
            $badge.text(description).attr('data-type', typeNumber);
            this.fillText('#nat-insights' + suffix, advice);
        },

        generateNATAdvice: function(typeNumber, symmetric) {
            if (typeNumber === 1) {
                return 'Excellent! Your NAT is open. Multiplayer, voice chat, and cloud streaming will perform at peak levels.';
            }
            if (typeNumber === 2) {
                return symmetric ? 'Moderate NAT detected with symmetric behavior. Enable UPnP or configure port forwarding to improve connectivity.' : 'Moderate NAT. Most services work fine. For full optimization enable UPnP, configure port forwarding, or place your console in DMZ.';
            }
            return 'Strict NAT detected. Forward the recommended gaming ports, enable UPnP, or contact your ISP for bridge mode to unlock full connectivity.';
        },

        runWebRTCNATAnalysis: function(publicIP, suffix) {
            const self = this;
            if (!window.RTCPeerConnection) {
                this.fillText('#nat-ice-details' + suffix, 'WebRTC not supported on this browser. NAT inference limited.');
                return;
            }
            const pc = new RTCPeerConnection({
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:stun1.l.google.com:19302' }
                ],
                iceCandidatePoolSize: 8
            });
            const state = { host: [], srflx: [], relay: [] };
            pc.createDataChannel('unpc-nat');
            pc.onicecandidate = function(event) {
                if (event.candidate && event.candidate.candidate) {
                    const parsed = self.parseIceCandidate(event.candidate.candidate);
                    if (parsed) {
                        state[parsed.type].push(parsed);
                        self.renderIceDetails(state, suffix);
                    }
                } else {
                    pc.close();
                    self.summarizeIceState(state, publicIP, suffix);
                }
            };
            pc.createOffer({ iceRestart: true }).then(function(offer) {
                return pc.setLocalDescription(offer);
            }).catch(function() {
                self.fillText('#nat-ice-details' + suffix, 'Unable to gather ICE candidates.');
            });
            setTimeout(function() {
                if (pc && pc.iceConnectionState !== 'closed') {
                    pc.close();
                    self.summarizeIceState(state, publicIP, suffix);
                }
            }, 4000);
        },

        parseIceCandidate: function(candidateString) {
            const parts = candidateString.trim().split(' ');
            if (parts.length < 8) {
                return null;
            }
            const details = {
                foundation: parts[0].split(':')[1] || '',
                component: parts[1] || '',
                protocol: parts[2] ? parts[2].toUpperCase() : 'UNKNOWN',
                priority: parts[3] || '',
                ip: parts[4] || '',
                port: parts[5] || '',
                type: parts[7] || 'host'
            };
            if (!details.type || !details.ip) {
                return null;
            }
            return details;
        },

        renderIceDetails: function(state, suffix) {
            const groups = [];
            const labels = {
                host: 'Host (LAN)',
                srflx: 'Server Reflexive (NAT)',
                relay: 'Relay (TURN)'
            };
            ['host', 'srflx', 'relay'].forEach(function(type) {
                if (state[type] && state[type].length) {
                    const rows = state[type].map(function(item) {
                        return '<div class="ice-candidate"><span>' + item.ip + '</span><span>' + item.protocol + ':' + item.port + '</span></div>';
                    }).join('');
                    groups.push('<div class="ice-group"><div class="ice-title">' + labels[type] + '</div>' + rows + '</div>');
                }
            });
            const html = groups.length ? groups.join('') : 'No ICE candidates available yet…';
            $('#nat-ice-details' + suffix).html(html);
        },

        summarizeIceState: function(state, publicIP, suffix) {
            const hasRelay = state.relay && state.relay.length > 0;
            const hasSrflx = state.srflx && state.srflx.length > 0;
            const hasHost = state.host && state.host.length > 0;
            let type = 2;
            let label = 'Type 2 - Moderate';
            let symmetric = false;
            if (hasRelay && !hasSrflx) {
                type = 3;
                label = 'Type 3 - Strict';
            } else if (hasSrflx && hasRelay) {
                type = 3;
                label = 'Type 3 - Strict';
            } else if (hasHost && this.hostMatchesPublic(state.host, publicIP)) {
                type = 1;
                label = 'Type 1 - Open';
            }
            if (hasSrflx) {
                symmetric = this.detectSymmetric(state.srflx);
            }
            const description = this.generateDescription(type, symmetric);
            this.updateNATStatus(type, label, description, suffix);
            const symText = symmetric ? 'Likely symmetric – port preservation blocked' : 'No symmetric pattern detected';
            this.fillText('#nat-symmetric' + suffix, symText);
        },

        hostMatchesPublic: function(hostCandidates, publicIP) {
            if (!publicIP) {
                return false;
            }
            return hostCandidates.some(function(item) {
                return item.ip === publicIP || UNPC.isPublicIP(item.ip);
            });
        },

        detectSymmetric: function(srflxCandidates) {
            if (!srflxCandidates || srflxCandidates.length < 2) {
                return false;
            }
            const ports = srflxCandidates.map(function(item) { return item.port; });
            return new Set(ports).size !== ports.length;
        },

        generateDescription: function(type, symmetric) {
            if (type === 1) {
                return 'Open routing path detected – optimal peer-to-peer and streaming performance expected.';
            }
            if (type === 2 && !symmetric) {
                return 'Moderate NAT discovered. Enable UPnP or smart port forwarding for the best multiplayer throughput.';
            }
            if (type === 2 && symmetric) {
                return 'Moderate NAT with symmetric traits. Configure manual port forwarding or request bridge mode from ISP.';
            }
            return 'Strict firewall or CGNAT detected. Use static IP, advanced router configurations, or ISP assistance to unlock ports.';
        },

        setupPortChecker: function() {
            const self = this;
            $(document).on('change', '#platform-preset, #platform-preset-standalone', function() {
                const $select = $(this);
                const ports = $select.find('option:selected').data('ports');
                const suffix = $select.is('#platform-preset-standalone') ? '-standalone' : '';
                if (ports) {
                    $('#custom-port' + suffix).val(ports);
                }
            });
            $(document).on('click', '#unpc-check-port, #unpc-check-port-standalone', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const suffix = $btn.is('#unpc-check-port-standalone') ? '-standalone' : '';
                if ($btn.hasClass('loading')) {
                    return;
                }
                const port = $('#custom-port' + suffix).val().trim();
                const host = $('#port-host' + suffix).val().trim();
                const protocol = ($('#platform-preset' + suffix).find('option:selected').data('protocol') || 'TCP').toString().toUpperCase();
                if (!port) {
                    alert('Please enter at least one port or range.');
                    return;
                }
                $btn.addClass('loading').prop('disabled', true);
                $.ajax({
                    url: unpcData.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'unpc_check_port',
                        nonce: unpcData.nonce,
                        port: port,
                        host: host,
                        protocol: protocol
                    }
                }).done(function(response) {
                    if (response && response.success && response.data) {
                        self.displayPortResults(response.data, suffix, port, protocol);
                    } else {
                        const message = response && response.data && response.data.message ? response.data.message : 'Port scan failed. Try again later.';
                        alert(message);
                    }
                }).fail(function() {
                    alert('Network error while scanning ports.');
                }).always(function() {
                    $btn.removeClass('loading').prop('disabled', false);
                });
            });
        },

        displayPortResults: function(data, suffix, inputPorts, protocol) {
            const $meta = $('#port-results-meta' + suffix);
            const $container = $('#port-results-container' + suffix);
            const $results = $('#unpc-port-results' + suffix);
            const timestamp = new Date().toLocaleString();
            $meta.html(
                '<div><strong>Target:</strong> ' + (data.host || 'Unknown') + '</div>' +
                '<div><strong>Ports:</strong> ' + inputPorts + '</div>' +
                '<div><strong>Protocol:</strong> ' + protocol + '</div>' +
                '<div><strong>Checked:</strong> ' + timestamp + '</div>'
            );
            $container.empty();
            if (!data.results || !data.results.length) {
                $container.html('<p>No port data returned.</p>');
                $results.fadeIn(300);
                return;
            }
            data.results.forEach(function(result) {
                const statusClass = result.status === 'open' ? 'open' : 'closed';
                const statusLabel = result.status === 'open' ? 'Open' : 'Closed';
                const latency = result.latency ? result.latency + ' ms' : 'N/A';
                const message = result.message || '';
                const item = `
                    <div class="port-result-item">
                        <div class="port-result-info">
                            <div class="port-result-port">Port ${result.port}</div>
                            <div class="port-result-protocol">${result.protocol}</div>
                            <div class="port-result-latency">Latency: ${latency}</div>
                            <div class="port-result-message">${message}</div>
                        </div>
                        <div class="port-result-status ${statusClass}">${statusLabel}</div>
                    </div>
                `;
                $container.append(item);
            });
            $results.fadeIn(300);
        },

        setupDeviceInfo: function() {
            const self = this;
            setTimeout(function() {
                self.detectDeviceInfo();
            }, 150);
        },

        detectDeviceInfo: function() {
            const nav = navigator;
            const screen = window.screen;
            const info = {
                os: this.getOS(),
                platform: nav.platform || 'Unknown',
                arch: nav.userAgentData && nav.userAgentData.platform ? nav.userAgentData.platform : nav.platform || 'Unknown',
                browser: this.getBrowser(),
                browserVersion: this.getBrowserVersion(),
                engine: this.getBrowserEngine(),
                screen: screen.width + '×' + screen.height,
                viewport: window.innerWidth + '×' + window.innerHeight,
                colorDepth: screen.colorDepth + '-bit',
                userAgent: nav.userAgent,
                language: nav.language || nav.userLanguage || 'Unknown',
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || 'Unknown',
                cores: nav.hardwareConcurrency || 'Unknown',
                memory: nav.deviceMemory ? nav.deviceMemory + ' GB' : 'Unknown',
                touch: ('ontouchstart' in window || nav.maxTouchPoints > 0) ? 'Yes' : 'No',
                webgl: this.detectWebGL(),
                cookies: nav.cookieEnabled ? 'Enabled' : 'Disabled'
            };
            this.fillText('#device-os', info.os);
            this.fillText('#device-platform', info.platform);
            this.fillText('#device-arch', info.arch);
            this.fillText('#device-browser', info.browser);
            this.fillText('#device-browser-version', info.browserVersion);
            this.fillText('#device-engine', info.engine);
            this.fillText('#device-screen', info.screen);
            this.fillText('#device-viewport', info.viewport);
            this.fillText('#device-color', info.colorDepth);
            this.fillText('#device-ua', info.userAgent);
            this.fillText('#device-lang', info.language);
            this.fillText('#device-tz', info.timezone);
            this.fillText('#device-cores', info.cores);
            this.fillText('#device-memory', info.memory);
            this.fillText('#device-touch', info.touch);
            this.fillText('#device-webgl', info.webgl);
            this.fillText('#device-cookies', info.cookies);
        },

        fillText: function(selector, value) {
            const $el = $(selector);
            if ($el.length) {
                $el.text(value && value !== '' ? value : '-');
            }
        },

        getOS: function() {
            const ua = navigator.userAgent;
            const platform = navigator.platform;
            if (/Win/i.test(platform)) {
                if (/Windows NT 10/.test(ua) || /Windows NT 11/.test(ua)) return 'Windows 10/11';
                if (/Windows NT 6.3/.test(ua)) return 'Windows 8.1';
                if (/Windows NT 6.2/.test(ua)) return 'Windows 8';
                if (/Windows NT 6.1/.test(ua)) return 'Windows 7';
                return 'Windows';
            }
            if (/Mac/i.test(platform)) {
                if (/iPhone|iPad|iPod/.test(ua)) return 'iOS';
                return 'macOS';
            }
            if (/Android/.test(ua)) {
                return 'Android';
            }
            if (/Linux/.test(platform)) {
                return 'Linux';
            }
            return 'Unknown';
        },

        getBrowser: function() {
            const ua = navigator.userAgent;
            if (ua.indexOf('Edg/') > -1) return 'Microsoft Edge';
            if (ua.indexOf('OPR/') > -1 || ua.indexOf('Opera') > -1) return 'Opera';
            if (ua.indexOf('Chrome') > -1 && ua.indexOf('Edg/') === -1) return 'Google Chrome';
            if (ua.indexOf('Firefox') > -1) return 'Mozilla Firefox';
            if (ua.indexOf('Safari') > -1 && ua.indexOf('Chrome') === -1) return 'Apple Safari';
            if (ua.indexOf('MSIE') > -1 || ua.indexOf('Trident/') > -1) return 'Internet Explorer';
            return 'Unknown';
        },

        getBrowserVersion: function() {
            const ua = navigator.userAgent;
            let match;
            if ((match = ua.match(/Edg\/(\d+)/))) return match[1];
            if ((match = ua.match(/OPR\/(\d+)/))) return match[1];
            if ((match = ua.match(/Chrome\/(\d+)/))) return match[1];
            if ((match = ua.match(/Firefox\/(\d+)/))) return match[1];
            if ((match = ua.match(/Version\/(\d+).*Safari/))) return match[1];
            return 'Unknown';
        },

        getBrowserEngine: function() {
            const ua = navigator.userAgent;
            if (ua.indexOf('Gecko/') > -1 && ua.indexOf('Firefox') > -1) return 'Gecko';
            if (ua.indexOf('AppleWebKit/') > -1) {
                if (ua.indexOf('Chrome') > -1 || ua.indexOf('Edg/') > -1) return 'Blink';
                return 'WebKit';
            }
            if (ua.indexOf('Trident/') > -1) return 'Trident';
            return 'Unknown';
        },

        detectWebGL: function() {
            try {
                const canvas = document.createElement('canvas');
                return !!(window.WebGLRenderingContext && (canvas.getContext('webgl') || canvas.getContext('experimental-webgl'))) ? 'Supported' : 'Not Supported';
            } catch (e) {
                return 'Not Supported';
            }
        },

        setupRouterSearch: function() {
            $(document).on('input', '#router-search', function() {
                const term = $(this).val().toLowerCase().trim();
                $('.unpc-router-item').each(function() {
                    const brand = $(this).data('brand');
                    if (!term || (brand && brand.indexOf(term) > -1)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        },

        isPublicIP: function(ip) {
            if (!ip) {
                return false;
            }
            const privateRanges = [/^10\./, /^172\.(1[6-9]|2\d|3[01])\./, /^192\.168\./, /^169\.254\./];
            return !privateRanges.some(function(regex) { return regex.test(ip); });
        },

        showError: function(message, suffix) {
            alert(message);
        }
    };

    $(function() {
        UNPC.init();
    });

})(jQuery);

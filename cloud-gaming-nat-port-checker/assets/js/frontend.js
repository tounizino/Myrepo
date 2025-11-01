(function($) {
    'use strict';

    const CGNPC = {
        autoRefreshTimer: null,
        autoRefreshDelay: 30000,
        isNatChecking: false,

        init() {
            this.bindEvents();
            this.loadThemePreference();
            this.loadFontPreference();
            this.restoreAutoRefresh();
            this.autoLoadDeviceInfo();
            this.triggerInitialNatCheck();
        },

        bindEvents() {
            $(document).on('click', '.cgnpc-theme-toggle', this.toggleTheme.bind(this));
            $(document).on('click', '.cgnpc-font-toggle', this.cycleFontSize.bind(this));
            $(document).on('click', '.cgnpc-check-nat-btn', () => this.checkNAT(false));
            $(document).on('change', '.cgnpc-auto-refresh-toggle', this.handleAutoToggle.bind(this));
            $(document).on('click', '.cgnpc-preset-btn', this.checkPresetPorts.bind(this));
            $(document).on('click', '.cgnpc-check-custom-port-btn', this.checkCustomPort.bind(this));
            $(document).on('click', '.cgnpc-refresh-device-btn', this.autoLoadDeviceInfo.bind(this));
            $(document).on('click', '.cgnpc-copy-btn', this.copyToClipboard.bind(this));
        },

        toggleTheme() {
            const $wrapper = $('.cgnpc-wrapper');
            const current = $wrapper.attr('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            $wrapper.attr('data-theme', next);
            localStorage.setItem('cgnpc_theme', next);
        },

        loadThemePreference() {
            const saved = localStorage.getItem('cgnpc_theme');
            if (saved) {
                $('.cgnpc-wrapper').attr('data-theme', saved);
            }
        },

        cycleFontSize() {
            const $wrapper = $('.cgnpc-wrapper');
            const current = $wrapper.attr('data-font-size') || cgnpcData.fonts.default || 'small';
            const sizes = ['small', 'medium', 'large'];
            const index = sizes.indexOf(current);
            const next = sizes[(index + 1) % sizes.length];
            $wrapper.attr('data-font-size', next);
            localStorage.setItem('cgnpc_font_size', next);
        },

        loadFontPreference() {
            const saved = localStorage.getItem('cgnpc_font_size');
            if (saved) {
                $('.cgnpc-wrapper').attr('data-font-size', saved);
            }
        },

        handleAutoToggle(e) {
            const enabled = $(e.currentTarget).is(':checked');
            this.setAutoRefresh(enabled);
        },

        setAutoRefresh(enabled) {
            if (this.autoRefreshTimer) {
                clearInterval(this.autoRefreshTimer);
                this.autoRefreshTimer = null;
            }
            if (enabled) {
                this.autoRefreshTimer = setInterval(() => this.checkNAT(true), this.autoRefreshDelay);
                this.checkNAT(true);
            }
            localStorage.setItem('cgnpc_auto_refresh', enabled ? '1' : '0');
            $('.cgnpc-auto-refresh-toggle').prop('checked', enabled);
        },

        restoreAutoRefresh() {
            const saved = localStorage.getItem('cgnpc_auto_refresh');
            if (saved === '1') {
                this.setAutoRefresh(true);
            }
        },

        triggerInitialNatCheck() {
            setTimeout(() => this.checkNAT(true), 600);
        },

        checkNAT(isAuto = false) {
            if (this.isNatChecking) {
                return;
            }
            this.isNatChecking = true;

            const $section = $('.cgnpc-nat-section');
            const $loading = $section.find('.cgnpc-nat-loading');
            const $result = $section.find('.cgnpc-nat-result');
            const $button = $section.find('.cgnpc-check-nat-btn');

            if (!isAuto) {
                $result.hide();
                $loading.show();
            }
            $button.toggleClass('cgnpc-btn-busy', true);

            $.ajax({
                url: cgnpcData.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'cgnpc_check_nat',
                    nonce: cgnpcData.nonce
                }
            }).done((response) => {
                if (response && response.success) {
                    this.renderNatResult(response.data);
                } else {
                    const message = response && response.data && response.data.message ? response.data.message : cgnpcData.i18n.error;
                    this.displayInlineError($section, message);
                }
            }).fail(() => {
                this.displayInlineError($section, cgnpcData.i18n.error);
            }).always(() => {
                this.isNatChecking = false;
                $button.toggleClass('cgnpc-btn-busy', false);
                $loading.hide();
            });
        },

        renderNatResult(data) {
            const $result = $('.cgnpc-nat-result');
            const $indicator = $result.find('.cgnpc-nat-indicator');
            const $label = $result.find('.cgnpc-nat-label');
            const $description = $result.find('.cgnpc-nat-description');
            const $ip = $result.find('.cgnpc-ip');
            const $location = $result.find('.cgnpc-location');
            const $isp = $result.find('.cgnpc-isp');
            const $advanced = $result.find('.cgnpc-advanced-content');

            $indicator.removeClass('green yellow red gray').addClass(data.nat_color || 'gray');
            $label.text(data.nat_status || cgnpcData.i18n.unknown);
            $description.text(data.nat_description || '');
            $ip.text(data.public_ip || '');
            $location.text(`${data.city || 'Unknown'}, ${data.region || 'Unknown'}, ${data.country || 'Unknown'}`);
            $isp.text(data.isp || '');

            let advanced = '';
            if (data.advanced_details) {
                advanced += '<dl class="cgnpc-advanced-list">';
                advanced += `<dt>${cgnpcData.i18n.method || 'Detection Method'}</dt><dd>${data.advanced_details.detection_method || 'Heuristic'}</dd>`;
                advanced += `<dt>${cgnpcData.i18n.reason || 'Reasoning'}</dt><dd>${data.advanced_details.reasoning || '—'}</dd>`;
                advanced += `<dt>${cgnpcData.i18n.confidence || 'Confidence'}</dt><dd>${data.confidence || '—'}</dd>`;
                if (data.advanced_details.stun && data.advanced_details.stun.tests && data.advanced_details.stun.tests.binding) {
                    const stun = data.advanced_details.stun;
                    advanced += `<dt>STUN Server</dt><dd>${stun.server ? `${stun.server.host}:${stun.server.port}` : '—'}</dd>`;
                    advanced += `<dt>STUN Inference</dt><dd>${stun.inference ? stun.inference.description : '—'}</dd>`;
                    advanced += `<dt>Public Mapping</dt><dd>${stun.tests.binding.mapped_ip || '—'} : ${stun.tests.binding.mapped_port || '—'}</dd>`;
                }
                if (data.advanced_details.port_mapping) {
                    advanced += `<dt>Port Mapping</dt><dd>${data.advanced_details.port_mapping.description}</dd>`;
                }
                advanced += '</dl>';
            }
            if (data.advice && data.advice.length) {
                advanced += '<div class="cgnpc-advanced-advice"><strong>Optimization Tips</strong><ul>';
                data.advice.forEach((tip) => {
                    advanced += `<li>${tip}</li>`;
                });
                advanced += '</ul></div>';
            }
            if (data.fallback) {
                advanced += `<div class="cgnpc-advanced-fallback"><strong>Fallback:</strong> ${data.fallback}</div>`;
            }
            $advanced.html(advanced);

            $result.fadeIn(160);
        },

        checkPresetPorts(e) {
            const key = $(e.currentTarget).data('preset');
            if (!cgnpcData.portPresets || !cgnpcData.portPresets[key]) {
                return;
            }
            const ports = cgnpcData.portPresets[key].ports || [];
            if (!ports.length) {
                return;
            }
            this.performPortChecks(ports);
        },

        checkCustomPort() {
            const port = parseInt($('.cgnpc-custom-port-input').val(), 10);
            const protocol = $('.cgnpc-protocol-select').val();
            if (!port || port < 1 || port > 65535) {
                alert('Please enter a valid port between 1 and 65535.');
                return;
            }
            this.performPortChecks([{ port, protocol, label: `Custom Port ${port}` }]);
        },

        performPortChecks(portItems) {
            const $results = $('.cgnpc-port-results');
            $results.html('<div class="cgnpc-spinner" style="margin:20px auto;"></div><div class="cgnpc-loading-text">' + cgnpcData.i18n.checking + '</div>');

            const requests = portItems.map((item) => this.requestPortCheck(item.port, item.protocol, item.label));
            Promise.all(requests).then((responses) => {
                this.renderPortResults(responses);
            });
        },

        requestPortCheck(port, protocol, label) {
            return new Promise((resolve) => {
                $.ajax({
                    url: cgnpcData.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'cgnpc_check_port',
                        nonce: cgnpcData.nonce,
                        port: port,
                        protocol: protocol
                    }
                }).done((response) => {
                    if (response && response.success) {
                        resolve({ ...response.data, label: label || `Port ${port}` });
                    } else {
                        const message = response && response.data && response.data.message ? response.data.message : cgnpcData.i18n.error;
                        resolve({
                            port,
                            protocol: protocol.toUpperCase(),
                            status: 'error',
                            color: 'red',
                            message,
                            details: '',
                            label: label || `Port ${port}`
                        });
                    }
                }).fail(() => {
                    resolve({
                        port,
                        protocol: protocol.toUpperCase(),
                        status: 'error',
                        color: 'red',
                        message: cgnpcData.i18n.error,
                        details: 'Network request failed.',
                        label: label || `Port ${port}`
                    });
                });
            });
        },

        renderPortResults(results) {
            const $results = $('.cgnpc-port-results');
            let html = '';
            results.forEach((res) => {
                html += `
                    <div class="cgnpc-port-result-item ${res.color}">
                        <div class="cgnpc-port-header">
                            <strong>${res.label || `Port ${res.port}`}</strong>
                            <span class="cgnpc-port-badge">${res.port} / ${res.protocol}</span>
                        </div>
                        <div class="cgnpc-port-status">${res.message}</div>
                        ${res.latency_ms !== null && res.latency_ms !== undefined ? `<div class="cgnpc-port-meta">⏱ Latency: ${res.latency_ms} ms</div>` : ''}
                        ${res.packet_loss !== null && res.packet_loss !== undefined ? `<div class="cgnpc-port-meta">📊 Packet Loss: ${res.packet_loss}%</div>` : ''}
                        ${res.details ? `<div class="cgnpc-port-details">${res.details}</div>` : ''}
                    </div>
                `;
            });
            $results.html(html);
        },

        autoLoadDeviceInfo() {
            const $grid = $('.cgnpc-device-grid');
            if (!$grid.length) {
                return;
            }
            $.ajax({
                url: cgnpcData.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'cgnpc_get_device_info',
                    nonce: cgnpcData.nonce
                }
            }).done((response) => {
                if (response && response.success) {
                    const combined = this.buildDeviceDataset(response.data);
                    this.renderDeviceDetails(combined);
                }
            });
        },

        buildDeviceDataset(serverInfo) {
            const client = this.collectClientInfo();
            const dataset = [
                { label: 'Browser', value: serverInfo.browser },
                { label: 'Operating System', value: serverInfo.os },
                { label: 'Device Type', value: serverInfo.device },
                { label: 'IP Address', value: serverInfo.ip },
                { label: 'User Agent', value: serverInfo.user_agent, multiline: true },
                { label: 'Language', value: serverInfo.accept_language }
            ];

            dataset.push({ label: 'Screen Resolution', value: client.resolution });
            dataset.push({ label: 'Pixel Density', value: client.pixelDensity });
            dataset.push({ label: 'CPU Threads', value: client.cpuCores });
            dataset.push({ label: 'Estimated RAM', value: client.ram });
            dataset.push({ label: 'GPU Renderer', value: client.gpu });
            dataset.push({ label: 'Connection Type', value: client.connectionType });
            dataset.push({ label: 'Downlink', value: client.downlink });
            dataset.push({ label: 'RTT', value: client.rtt });

            if (serverInfo.connection_hint && serverInfo.connection_hint.length) {
                dataset.push({ label: 'Server Hints', value: serverInfo.connection_hint.join('\n'), multiline: true });
            }

            return dataset;
        },

        renderDeviceDetails(items) {
            const $grid = $('.cgnpc-device-grid');
            let html = '';
            items.forEach((item) => {
                if (!item.value) {
                    return;
                }
                let value = item.value;
                if (item.multiline) {
                    value = value.replace(/\n/g, '<br>');
                }
                html += `
                    <div class="cgnpc-device-item">
                        <strong>${item.label}</strong>
                        <div class="cgnpc-device-value">${value}</div>
                    </div>
                `;
            });
            $grid.html(html);
        },

        collectClientInfo() {
            const info = {};
            info.resolution = `${window.screen.width || '?'} x ${window.screen.height || '?'}`;
            if (window.devicePixelRatio) {
                info.pixelDensity = `${window.devicePixelRatio.toFixed(2)} dppx`;
            }
            if (navigator.hardwareConcurrency) {
                info.cpuCores = `${navigator.hardwareConcurrency} cores`;
            }
            if (navigator.deviceMemory) {
                info.ram = `${navigator.deviceMemory} GB`; 
            }
            const connection = navigator.connection || navigator.webkitConnection || navigator.mozConnection; // Network Information API
            if (connection) {
                info.connectionType = connection.effectiveType || connection.type || '—';
                if (connection.downlink) {
                    info.downlink = `${connection.downlink} Mbps`;
                }
                if (connection.rtt) {
                    info.rtt = `${connection.rtt} ms`;
                }
            }
            info.gpu = this.detectGPU();
            return info;
        },

        detectGPU() {
            try {
                const canvas = document.createElement('canvas');
                const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
                if (!gl) {
                    return 'WebGL not supported';
                }
                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                if (debugInfo) {
                    const renderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                    return renderer || 'Unknown GPU';
                }
                return 'WebGL renderer hidden';
            } catch (e) {
                return 'GPU detection failed';
            }
        },

        copyToClipboard(e) {
            const target = $(e.currentTarget).data('target');
            let text = '';
            if (target === 'ip') {
                text = $('.cgnpc-ip').text();
            }
            if (!text) {
                return;
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => this.flashCopyState(e.currentTarget, true)).catch(() => this.flashCopyState(e.currentTarget, false));
            } else {
                const temp = $('<textarea>').val(text).appendTo('body');
                temp[0].select();
                try {
                    const success = document.execCommand('copy');
                    this.flashCopyState(e.currentTarget, success);
                } catch (err) {
                    this.flashCopyState(e.currentTarget, false);
                }
                temp.remove();
            }
        },

        flashCopyState(button, success) {
            const $btn = $(button);
            const original = $btn.text();
            $btn.text(success ? '✓ ' + cgnpcData.i18n.copied : cgnpcData.i18n.copyFailed);
            setTimeout(() => $btn.text(original), 1800);
        },

        displayInlineError($container, message) {
            const $existing = $container.find('.cgnpc-error-message');
            if ($existing.length) {
                $existing.remove();
            }
            const $error = $('<div class="cgnpc-error-message" role="alert"></div>').text(message);
            $container.append($error);
            setTimeout(() => {
                $error.fadeOut(250, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    $(document).ready(() => CGNPC.init());

})(jQuery);

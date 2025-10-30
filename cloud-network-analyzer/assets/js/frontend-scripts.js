(function($) {
    'use strict';

    const CNA = {
        cache: {},
        
        init: function() {
            this.bindEvents();
            this.initTooltips();
            this.initAccordion();
        },
        
        bindEvents: function() {
            $(document).on('click', '.cna-check-nat', this.checkNAT.bind(this));
            $(document).on('click', '.cna-check-port', this.checkPorts.bind(this));
            $(document).on('click', '.cna-btn-preset', this.presetPorts.bind(this));
            $(document).on('click', '.cna-get-device-info', this.getDeviceInfo.bind(this));
            $(document).on('click', '.cna-run-network-test', this.runNetworkTest.bind(this));
            $(document).on('click', '.cna-toggle-advanced', this.toggleAdvanced.bind(this));
        },
        
        initTooltips: function() {
            if (window.matchMedia && window.matchMedia('(max-width: 768px)').matches) {
                return;
            }
        },
        
        initAccordion: function() {
            $('.cna-accordion-header').on('click', function() {
                const $header = $(this);
                const $content = $header.next('.cna-accordion-content');
                const isActive = $header.hasClass('active');
                
                $('.cna-accordion-header').removeClass('active');
                $('.cna-accordion-content').removeClass('active');
                
                if (!isActive) {
                    $header.addClass('active');
                    $content.addClass('active');
                }
            });
        },
        
        checkNAT: function(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $section = $button.closest('.cna-nat-checker');
            const $loading = $section.find('.cna-loading');
            const $result = $section.find('.cna-nat-result');
            
            $button.prop('disabled', true);
            $result.hide();
            $loading.show();
            
            const $icon = $button.find('.cna-icon');
            $icon.addClass('loading');
            
            $.ajax({
                url: cnaAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cna_check_nat',
                    nonce: cnaAjax.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.displayNATResult(response.data, $section);
                    } else {
                        this.showError($section, 'Failed to check NAT type');
                    }
                },
                error: () => {
                    this.showError($section, 'Network error occurred');
                },
                complete: () => {
                    $button.prop('disabled', false);
                    $loading.hide();
                    $icon.removeClass('loading');
                }
            });
        },
        
        displayNATResult: function(data, $section) {
            const $result = $section.find('.cna-nat-result');
            const $circle = $result.find('.cna-nat-circle');
            const $label = $result.find('.cna-nat-type-label');
            const $explanation = $result.find('.cna-nat-explanation-text');
            
            $circle.removeClass('nat-open nat-moderate nat-strict');
            
            if (data.nat_type === 'Open') {
                $circle.addClass('nat-open');
                $label.text('🟢 Open NAT (Type 1)');
            } else if (data.nat_type === 'Moderate') {
                $circle.addClass('nat-moderate');
                $label.text('🟡 Moderate NAT (Type 2)');
            } else if (data.nat_type === 'Strict') {
                $circle.addClass('nat-strict');
                $label.text('🔴 Strict NAT (Type 3)');
            }
            
            $('#cna-public-ip').text(data.public_ip || '-');
            $('#cna-private-ip').text(data.private_ip || 'Not accessible from browser');
            $('#cna-isp').text(data.isp || '-');
            $('#cna-location').text(data.location || '-');
            $('#cna-ip-version').text(data.ip_version || '-');
            
            $('#cna-gateway-ip').text(data.gateway_ip || '-');
            $('#cna-subnet-mask').text(data.subnet_mask || '-');
            $('#cna-dns-servers').text(data.dns_servers || '-');
            $('#cna-upnp-status').text(data.upnp_status || '-');
            $('#cna-nat-mapping').text(data.nat_mapping || '-');
            
            $explanation.text(data.explanation || '');
            
            $result.fadeIn(400);
        },
        
        toggleAdvanced: function(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $advanced = $button.closest('.cna-nat-result').find('.cna-advanced-details');
            
            $button.toggleClass('active');
            $advanced.slideToggle(300);
        },
        
        checkPorts: function(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $section = $button.closest('.cna-port-checker');
            const $input = $section.find('#cna-port-input');
            const $protocol = $section.find('#cna-protocol-select');
            const portsInput = $input.val().trim();
            
            if (!portsInput) {
                alert('Please enter port numbers to check');
                return;
            }
            
            const ports = this.parsePortsInput(portsInput);
            
            if (ports.length === 0) {
                alert('Please enter valid port numbers (1-65535)');
                return;
            }
            
            if (ports.length > 50) {
                alert('Please limit to 50 ports at a time');
                return;
            }
            
            const protocol = $protocol.val();
            
            this.runPortTests($section, ports, protocol);
        },
        
        presetPorts: function(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const service = $button.data('service');
            const $section = $button.closest('.cna-port-checker');
            
            const presets = {
                'geforce-now': {
                    ports: [47989, 47990, 47998, 47999, 48000, 48010],
                    protocol: 'both'
                },
                'xbox-cloud': {
                    ports: [3074, 88, 500, 3544, 4500],
                    protocol: 'udp'
                },
                'playstation': {
                    ports: [80, 443, 3478, 3479, 3480],
                    protocol: 'tcp'
                },
                'boosteroid': {
                    ports: [443, 3478, 8443],
                    protocol: 'tcp'
                },
                'shadow': {
                    ports: [443, 8100, 9100],
                    protocol: 'tcp'
                }
            };
            
            const preset = presets[service];
            
            if (preset) {
                this.runPortTests($section, preset.ports, preset.protocol, service);
            }
        },
        
        runPortTests: function($section, ports, protocol, serviceName) {
            const $loading = $section.find('.cna-loading');
            const $results = $section.find('.cna-port-results');
            const $tbody = $section.find('#cna-port-results-body');
            
            $loading.show();
            $results.hide();
            $tbody.empty();
            
            const portTests = ports.map(port => {
                return $.ajax({
                    url: cnaAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cna_check_port',
                        nonce: cnaAjax.nonce,
                        port: port,
                        protocol: protocol
                    }
                });
            });
            
            Promise.all(portTests).then(responses => {
                responses.forEach((response, index) => {
                    if (response.success) {
                        this.addPortResult($tbody, response.data, serviceName);
                    }
                });
                
                $loading.hide();
                $results.fadeIn(300);
            }).catch(() => {
                this.showError($section, 'Error checking ports');
                $loading.hide();
            });
        },
        
        addPortResult: function($tbody, data, serviceName) {
            const statusClass = data.open ? 'status-open' : 'status-closed';
            const statusIcon = data.open ? '✅' : '❌';
            const statusText = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            
            const $row = $('<tr>').addClass('cna-fade-in');
            
            $row.append($('<td>').text(data.port));
            $row.append($('<td>').text(data.protocol));
            $row.append(
                $('<td>').html(
                    `<span class="cna-port-status ${statusClass}">${statusIcon} ${statusText}</span>`
                )
            );
            $row.append($('<td>').text(data.notes || '-'));
            
            $tbody.append($row);
        },
        
        parsePortsInput: function(input) {
            const parts = input.split(/[,\s]+/);
            const ports = [];
            
            parts.forEach(part => {
                if (part.includes('-')) {
                    const [start, end] = part.split('-').map(p => parseInt(p, 10));
                    if (start > 0 && end > start && end - start <= 100) {
                        for (let i = start; i <= end; i++) {
                            if (i > 0 && i <= 65535) {
                                ports.push(i);
                            }
                        }
                    }
                } else {
                    const port = parseInt(part, 10);
                    if (port > 0 && port <= 65535) {
                        ports.push(port);
                    }
                }
            });
            
            return [...new Set(ports)].sort((a, b) => a - b);
        },
        
        getDeviceInfo: function(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $section = $button.closest('.cna-device-info');
            const $loading = $section.find('.cna-loading');
            const $result = $section.find('.cna-device-result');
            
            $button.prop('disabled', true);
            $result.hide();
            $loading.show();
            
            const clientInfo = this.detectClientInfo();
            
            $('#cna-device-type').text(clientInfo.deviceType);
            $('#cna-browser').text(clientInfo.browser);
            $('#cna-os').text(clientInfo.os);
            $('#cna-connection-type').text(clientInfo.connectionType);
            
            $.ajax({
                url: cnaAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cna_get_device_info',
                    nonce: cnaAjax.nonce
                },
                success: (response) => {
                    if (response.success) {
                        $('#cna-device-type').text(response.data.device_type || clientInfo.deviceType);
                        $('#cna-browser').text(response.data.browser || clientInfo.browser);
                        $('#cna-os').text(response.data.os || clientInfo.os);
                    }
                },
                complete: () => {
                    $button.prop('disabled', false);
                    $loading.hide();
                    $result.fadeIn(300);
                }
            });
        },
        
        detectClientInfo: function() {
            const userAgent = navigator.userAgent;
            const info = {
                deviceType: 'Desktop',
                browser: 'Unknown',
                os: 'Unknown',
                connectionType: 'Unknown'
            };
            
            if (/mobile|android|iphone|ipod/i.test(userAgent)) {
                info.deviceType = 'Mobile';
            } else if (/ipad|tablet/i.test(userAgent)) {
                info.deviceType = 'Tablet';
            }
            
            if (/edge|edg/i.test(userAgent)) {
                info.browser = 'Microsoft Edge';
            } else if (/chrome/i.test(userAgent) && !/edge/i.test(userAgent)) {
                info.browser = 'Google Chrome';
            } else if (/safari/i.test(userAgent) && !/chrome/i.test(userAgent)) {
                info.browser = 'Safari';
            } else if (/firefox/i.test(userAgent)) {
                info.browser = 'Mozilla Firefox';
            } else if (/opera|opr/i.test(userAgent)) {
                info.browser = 'Opera';
            }
            
            if (/windows/i.test(userAgent)) {
                info.os = 'Windows';
            } else if (/mac/i.test(userAgent)) {
                info.os = 'macOS';
            } else if (/linux/i.test(userAgent)) {
                info.os = 'Linux';
            } else if (/android/i.test(userAgent)) {
                info.os = 'Android';
            } else if (/ios|iphone|ipad/i.test(userAgent)) {
                info.os = 'iOS';
            }
            
            if (navigator.connection) {
                const conn = navigator.connection;
                if (conn.effectiveType) {
                    const types = {
                        'slow-2g': 'Slow 2G',
                        '2g': '2G',
                        '3g': '3G',
                        '4g': '4G / LTE',
                        '5g': '5G'
                    };
                    info.connectionType = types[conn.effectiveType] || 'Cellular';
                } else if (conn.type) {
                    info.connectionType = conn.type;
                }
            }
            
            if (info.connectionType === 'Unknown') {
                info.connectionType = 'Wi-Fi / Ethernet';
            }
            
            return info;
        },
        
        runNetworkTest: function(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $section = $button.closest('.cna-device-info');
            const $results = $section.find('.cna-network-results');
            
            $button.prop('disabled', true).text('Testing...');
            
            this.measurePing().then(pingData => {
                $('#cna-ping').text(`${pingData.ping} ms`);
                $('#cna-jitter').text(`${pingData.jitter} ms`);
                $('#cna-packet-loss').text(`${pingData.packetLoss}%`);
                
                $results.fadeIn(300);
                $button.prop('disabled', false).text('Run Network Test');
            });
        },
        
        measurePing: function() {
            return new Promise((resolve) => {
                const iterations = 5;
                const pings = [];
                let completed = 0;
                let failures = 0;
                
                const testEndpoint = cnaAjax.ajaxurl;
                
                for (let i = 0; i < iterations; i++) {
                    setTimeout(() => {
                        const startTime = Date.now();
                        
                        $.ajax({
                            url: testEndpoint + '?action=cna_ping_test&t=' + Date.now(),
                            type: 'HEAD',
                            cache: false,
                            timeout: 5000,
                            success: () => {
                                const ping = Date.now() - startTime;
                                pings.push(ping);
                            },
                            error: () => {
                                failures++;
                            },
                            complete: () => {
                                completed++;
                                
                                if (completed === iterations) {
                                    const avgPing = pings.length > 0 
                                        ? Math.round(pings.reduce((a, b) => a + b, 0) / pings.length)
                                        : 0;
                                    
                                    let jitter = 0;
                                    if (pings.length > 1) {
                                        const differences = [];
                                        for (let j = 1; j < pings.length; j++) {
                                            differences.push(Math.abs(pings[j] - pings[j - 1]));
                                        }
                                        jitter = Math.round(
                                            differences.reduce((a, b) => a + b, 0) / differences.length
                                        );
                                    }
                                    
                                    const packetLoss = Math.round((failures / iterations) * 100);
                                    
                                    resolve({
                                        ping: avgPing,
                                        jitter: jitter,
                                        packetLoss: packetLoss
                                    });
                                }
                            }
                        });
                    }, i * 500);
                }
            });
        },
        
        showError: function($section, message) {
            const $error = $('<div>')
                .addClass('cna-error-message')
                .css({
                    padding: '15px',
                    background: '#fee',
                    border: '1px solid #fcc',
                    borderRadius: '6px',
                    color: '#c33',
                    marginTop: '15px'
                })
                .text(message);
            
            $section.find('.cna-error-message').remove();
            $section.append($error);
            
            setTimeout(() => {
                $error.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };
    
    $(document).ready(function() {
        CNA.init();
    });
    
})(jQuery);

(function($){
    const settings = window.unpcSettings || {};
    const stunServers = (settings.stunServers || ['stun:stun.l.google.com:19302']).map(server => {
        if (typeof server === 'string') {
            return { urls: server.trim() };
        }
        return server;
    });
    const natTimeout = parseInt(settings.natTimeout, 10) || 5000;
    const maxPorts = parseInt(settings.maxPorts, 10) || 50;

    function setTheme($wrapper, theme) {
        $wrapper.removeClass('theme-dark theme-light').addClass('theme-' + theme);
        const $toggle = $wrapper.find('.unpc-theme-toggle');
        if ($toggle.length) {
            $toggle.data('theme', theme);
            if (theme === 'dark') {
                $toggle.html('<i class="fas fa-moon"></i><span> ' + unpc_i18n('Dark Mode') + '</span>');
            } else {
                $toggle.html('<i class="fas fa-sun"></i><span> ' + unpc_i18n('Light Mode') + '</span>');
            }
        }
    }

    function unpc_i18n(text) {
        return text;
    }

    function analyzeNAT(candidates) {
        if (!candidates.length) {
            return {
                type: 'Type 3 (Strict)',
                level: 3,
                description: 'Symmetric NAT detected - may cause issues',
                log: 'No STUN candidates received.'
            };
        }

        let host = 0, srflx = 0, relay = 0;
        candidates.forEach(candidate => {
            if (candidate.includes('typ host')) {
                host++;
            } else if (candidate.includes('typ srflx')) {
                srflx++;
            } else if (candidate.includes('typ relay')) {
                relay++;
            }
        });

        let level = 3;
        let type = 'Type 3 (Strict)';
        let description = 'Symmetric NAT - may cause connectivity issues';

        if (srflx > 0 && relay === 0) {
            if (host > 0) {
                level = 1;
                type = 'Type 1 (Open)';
                description = 'Full Cone NAT - optimal for gaming';
            } else {
                level = 2;
                type = 'Type 2 (Moderate)';
                description = 'Address-restricted NAT - good for gaming';
            }
        }

        return {
            type,
            level,
            description,
            log: 'Candidates (' + candidates.length + '):\n' + candidates.join('\n')
        };
    }

    function checkNAT($wrapper) {
        const $button = $wrapper.find('.unpc-check-nat-btn');
        const $result = $wrapper.find('.unpc-nat-result');
        const $logWrapper = $wrapper.find('.unpc-technical-log-wrapper');
        const $log = $wrapper.find('.unpc-technical-log');
        const $toggleLog = $wrapper.find('.unpc-toggle-log');

        $button.prop('disabled', true).html('<span class="unpc-loading"></span> ' + unpc_i18n('Checking...'));
        $result.hide().removeClass('type-1 type-2 type-3');
        if ($logWrapper.length) {
            $logWrapper.hide();
            $log.hide();
            $toggleLog.removeClass('active').find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $toggleLog.find('span').text(unpc_i18n('Show Technical Details'));
        }

        const pc = new RTCPeerConnection({iceServers: stunServers});
        const candidates = [];
        let finished = false;

        const timeoutId = setTimeout(() => {
            if (!finished) {
                finished = true;
                pc.close();
                $result.text(unpc_i18n('NAT Check Failed: Timeout or browser restrictions.')).addClass('type-3').fadeIn();
                $button.prop('disabled', false).html('<i class="fas fa-redo"></i> ' + unpc_i18n('Check Again'));
            }
        }, natTimeout);

        pc.onicecandidate = event => {
            if (event.candidate) {
                candidates.push(event.candidate.candidate);
            } else if (!finished) {
                finished = true;
                clearTimeout(timeoutId);
                pc.close();
                const nat = analyzeNAT(candidates);
                $result.addClass('type-' + nat.level).html('<strong>' + nat.type + '</strong> – ' + nat.description).fadeIn();
                if ($logWrapper.length) {
                    $log.text('[NAT CHECK LOG]\n' + new Date().toISOString() + '\n\n' + nat.log);
                    $logWrapper.fadeIn();
                }
                $(document).trigger('unpc_nat_checked', [nat]);
                $button.prop('disabled', false).html('<i class="fas fa-redo"></i> ' + unpc_i18n('Check Again'));
            }
        };

        try {
            const channel = pc.createDataChannel('');
            pc.createOffer().then(offer => pc.setLocalDescription(offer)).catch(() => {
                clearTimeout(timeoutId);
                finished = true;
                $result.text(unpc_i18n('NAT Check Failed: Unable to create WebRTC offer.')).addClass('type-3').fadeIn();
                $button.prop('disabled', false).html('<i class="fas fa-redo"></i> ' + unpc_i18n('Check Again'));
            });
        } catch (error) {
            clearTimeout(timeoutId);
            finished = true;
            $result.text(unpc_i18n('NAT Check Failed: WebRTC not supported in this browser.')).addClass('type-3').fadeIn();
            $button.prop('disabled', false).html('<i class="fas fa-redo"></i> ' + unpc_i18n('Check Again'));
        }
    }

    function bindLogToggle($wrapper) {
        const $toggleLog = $wrapper.find('.unpc-toggle-log');
        const $log = $wrapper.find('.unpc-technical-log');

        $toggleLog.on('click', function(){
            const isOpen = $toggleLog.hasClass('active');
            if (isOpen) {
                $log.slideUp(200);
                $toggleLog.removeClass('active');
                $toggleLog.find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $toggleLog.find('span').text(unpc_i18n('Show Technical Details'));
            } else {
                $log.slideDown(200);
                $toggleLog.addClass('active');
                $toggleLog.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $toggleLog.find('span').text(unpc_i18n('Hide Technical Details'));
            }
        });
    }

    function parsePortsString(input) {
        const collected = [];
        if (!input) {
            return collected;
        }

        input.split(',').forEach(piece => {
            const segment = piece.trim();
            if (!segment) {
                return;
            }

            if (segment.includes('-')) {
                const rangeParts = segment.split('-');
                if (rangeParts.length === 2) {
                    const start = parseInt(rangeParts[0], 10);
                    const end = parseInt(rangeParts[1], 10);
                    if (!Number.isNaN(start) && !Number.isNaN(end) && start > 0 && end > 0 && start <= end && end <= 65535 && (end - start) <= 100) {
                        for (let i = start; i <= end; i += 1) {
                            collected.push(i);
                        }
                    }
                }
            } else {
                const port = parseInt(segment, 10);
                if (!Number.isNaN(port) && port > 0 && port <= 65535) {
                    collected.push(port);
                }
            }
        });

        return collected;
    }

    function checkPorts($wrapper, ports) {
        const $result = $wrapper.find('.unpc-port-result');
        const $details = $wrapper.find('.unpc-port-details');

        if (!ports) {
            $result.removeClass('open').addClass('closed').text(unpc_i18n('Please enter ports to check.')).fadeIn();
            $details.hide();
            return;
        }

        const parsedPorts = parsePortsString(ports);
        if (!parsedPorts.length) {
            $result.removeClass('open').addClass('closed').text(unpc_i18n('Invalid port format.')).fadeIn();
            $details.hide();
            return;
        }

        if (parsedPorts.length > maxPorts) {
            $result.removeClass('open').addClass('closed').text(unpc_i18n('Too many ports selected. Please reduce your range.')).fadeIn();
            $details.hide();
            return;
        }

        $result.removeClass('open closed').html('<span class="unpc-loading"></span> ' + unpc_i18n('Scanning ports...')).fadeIn();
        $details.hide();

        $.ajax({
            url: settings.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'unpc_check_port',
                nonce: settings.nonce,
                ports: ports
            }
        }).done(response => {
            if (!response || !response.success) {
                const message = response && response.data ? response.data.message : null;
                $result.removeClass('open').addClass('closed').text(message || unpc_i18n('Port check failed.')).fadeIn();
                return;
            }

            const data = response.data;
            if (data.open) {
                $result.removeClass('closed').addClass('open').html('<strong>' + unpc_i18n('Open Ports Detected') + ':</strong> ' + data.ports).fadeIn();
            } else {
                $result.removeClass('open').addClass('closed').html('<strong>' + unpc_i18n('Ports Closed or Filtered') + ':</strong> ' + data.ports).fadeIn();
            }

            if (data.results) {
                let detailHtml = '<div class="unpc-port-detail-grid">';
                Object.keys(data.results).forEach(port => {
                    const status = data.results[port];
                    detailHtml += '<div class="unpc-port-detail-item ' + (status ? 'open' : 'closed') + '">';
                    detailHtml += '<span class="port">' + port + '</span>';
                    detailHtml += '<span class="status">' + (status ? unpc_i18n('Open') : unpc_i18n('Closed')) + '</span>';
                    detailHtml += '</div>';
                });
                detailHtml += '</div>';
                detailHtml += '<div class="unpc-port-summary">' + unpc_i18n('Open: ') + data.open_count + ' | ' + unpc_i18n('Closed: ') + data.closed_count + ' | ' + unpc_i18n('Total: ') + data.total + '</div>';
                $details.html(detailHtml).fadeIn();
            }
            
            $(document).trigger('unpc_port_checked', [data]);
        }).fail(() => {
            $result.removeClass('open').addClass('closed').text(unpc_i18n('Network error. Please try again.')).fadeIn();
        });
    }

    function bindPresetButtons($wrapper) {
        const $input = $wrapper.find('.unpc-port-input-field');
        const $buttons = $wrapper.find('.unpc-preset-btn');
        const $checkButton = $wrapper.find('.unpc-check-port-btn');

        $buttons.on('click', function() {
            const ports = $(this).data('ports');
            if (ports === 'custom') {
                $input.val('').focus();
            } else {
                $input.val(ports);
                checkPorts($wrapper, ports);
            }
        });

        $checkButton.on('click', function(){
            const ports = $input.val().trim();
            checkPorts($wrapper, ports);
        });
    }

    function fetchDeviceInfo($wrapper) {
        const $values = $wrapper.find('[data-info]');
        if (!$values.length) {
            return;
        }

        const screenInfo = window.screen ? screen.width + 'x' + screen.height : 'Unknown';
        $wrapper.find('[data-info="screen"]').text(screenInfo);

        $.ajax({
            url: settings.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'unpc_get_device_info',
                nonce: settings.nonce
            }
        }).done(response => {
            if (!response || !response.success || !response.data) {
                $values.each(function(){
                    const $item = $(this);
                    const key = $item.data('info');
                    if (key !== 'screen') {
                        $item.text(unpc_i18n('Unavailable'));
                    }
                });
                return;
            }

            const data = response.data;
            Object.keys(data).forEach(key => {
                $wrapper.find('[data-info="' + key + '"]').text(data[key]);
            });
            $(document).trigger('unpc_device_loaded', [data]);
        }).fail(() => {
            $values.each(function(){
                const $item = $(this);
                const key = $item.data('info');
                if (key !== 'screen') {
                    $item.text(unpc_i18n('Unavailable'));
                }
            });
        });
    }

    $(function(){
        wrappers.each(function(){
            const $wrapper = $(this);
            const initialTheme = $wrapper.hasClass('theme-light') ? 'light' : 'dark';
            setTheme($wrapper, initialTheme);

            const $themeToggle = $wrapper.find('.unpc-theme-toggle');
            $themeToggle.on('click', function(){
                const current = $themeToggle.data('theme') || initialTheme;
                const next = current === 'dark' ? 'light' : 'dark';
                setTheme($wrapper, next);
            });

            const $natButton = $wrapper.find('.unpc-check-nat-btn');
            if ($natButton.length) {
                $natButton.on('click', function(){
                    checkNAT($wrapper);
                });
                if ($wrapper.find('.unpc-toggle-log').length) {
                    bindLogToggle($wrapper);
                }
            }

            const $portButton = $wrapper.find('.unpc-check-port-btn');
            if ($portButton.length) {
                bindPresetButtons($wrapper);
            }

            fetchDeviceInfo($wrapper);
        });
    });

})(jQuery);

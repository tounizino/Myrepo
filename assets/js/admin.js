/**
 * Cloud Gaming Tracker - Admin JavaScript
 * Version: 3.1.0
 */
(function($) {
    'use strict';

    var CGTAdmin = {
        availabilityState: {},

        init: function() {
            this.bindEvents();
            this.initColorPickers();
            this.initToggles();
            this.initModals();
            this.initThemePreview();
            this.initAvailabilityState();
        },

        bindEvents: function() {
            // Platform actions
            $(document).on('click', '.cgt-save-platform', function(e) {
                e.preventDefault();
                CGTAdmin.savePlatform();
            });

            $(document).on('click', '.cgt-delete-platform', function(e) {
                e.preventDefault();
                var platformId = $(this).data('platform-id');
                CGTAdmin.deletePlatform(platformId);
            });

            $(document).on('click', '.cgt-edit-platform', function(e) {
                e.preventDefault();
                var platformId = $(this).data('platform-id');
                CGTAdmin.openPlatformModal(platformId);
            });

            $(document).on('click', '.cgt-add-platform', function(e) {
                e.preventDefault();
                CGTAdmin.openPlatformModal();
            });

            // Availability toggles
            $(document).on('click', '.cgt-avail-btn', function(e) {
                e.preventDefault();
                CGTAdmin.toggleAvailabilityButton($(this));
            });

            $(document).on('click', '.cgt-save-availability', function(e) {
                e.preventDefault();
                CGTAdmin.saveAvailability($(this));
            });

            // Settings
            $(document).on('click', '.cgt-save-settings', function(e) {
                e.preventDefault();
                CGTAdmin.saveSettings();
            });

            // Game actions
            $(document).on('click', '.cgt-add-game', function(e) {
                e.preventDefault();
                CGTAdmin.addGame();
            });

            $(document).on('click', '.cgt-delete-game', function(e) {
                e.preventDefault();
                var gameId = $(this).data('game-id');
                CGTAdmin.deleteGame(gameId);
            });

            // Icon upload
            $(document).on('click', '.cgt-upload-icon', function(e) {
                e.preventDefault();
                CGTAdmin.uploadIcon($(this));
            });

            // Modal close
            $(document).on('click', '.cgt-modal-close, .cgt-modal-overlay', function(e) {
                if ($(e.target).hasClass('cgt-modal-overlay') || $(e.target).hasClass('cgt-modal-close')) {
                    CGTAdmin.closeModal();
                }
            });
        },

        initAvailabilityState: function() {
            $('.cgt-availability-grid').each(function() {
                var gameId = $(this).data('game-id');
                if (!CGTAdmin.availabilityState[gameId]) {
                    CGTAdmin.availabilityState[gameId] = {};
                }
                $(this).find('.cgt-availability-item').each(function() {
                    var platformId = $(this).data('platform-id');
                    var availActive = $(this).find('.cgt-avail-available.active').length > 0;
                    var includedActive = $(this).find('.cgt-avail-included.active').length > 0;
                    var requiredActive = $(this).find('.cgt-avail-required.active').length > 0;

                    CGTAdmin.availabilityState[gameId][platformId] = {
                        is_available: availActive ? 1 : 0,
                        game_included: includedActive ? 1 : (requiredActive ? 0 : -1)
                    };
                });
            });
        },

        savePlatform: function() {
            var $form = $('#cgt-platform-form');
            var data = {
                action: 'cgt_save_platform',
                nonce: cgtAdmin.nonce,
                platform_id: $form.find('[name="platform_id"]').val() || 0,
                name: $form.find('[name="name"]').val(),
                icon_url: $form.find('[name="icon_url"]').val(),
                game_included: $form.find('[name="game_included"]').is(':checked') ? 1 : 0,
                price: $form.find('[name="price"]').val(),
                currency: $form.find('[name="currency"]').val(),
                tier: $form.find('[name="tier"]').val(),
                unavailable_url: $form.find('[name="unavailable_url"]').val(),
                cta_text: $form.find('[name="cta_text"]').val(),
                cta_url: $form.find('[name="cta_url"]').val(),
                display_order: $form.find('[name="display_order"]').val(),
                is_active: $form.find('[name="is_active"]').is(':checked') ? 1 : 0
            };

            if (!data.name) {
                this.showNotice('Please enter a platform name', 'error');
                return;
            }

            this.showLoading($('.cgt-save-platform'), true);
            $.post(cgtAdmin.ajaxUrl, data, function(response) {
                CGTAdmin.showLoading($('.cgt-save-platform'), false);
                if (response.success) {
                    CGTAdmin.showNotice(response.data.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    CGTAdmin.showNotice(response.data.message || cgtAdmin.strings.error, 'error');
                }
            });
        },

        deletePlatform: function(platformId) {
            if (!confirm(cgtAdmin.strings.confirmDelete)) {
                return;
            }
            $.post(cgtAdmin.ajaxUrl, {
                action: 'cgt_delete_platform',
                nonce: cgtAdmin.nonce,
                platform_id: platformId
            }, function(response) {
                if (response.success) {
                    CGTAdmin.showNotice(response.data.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    CGTAdmin.showNotice(response.data.message || cgtAdmin.strings.error, 'error');
                }
            });
        },

        openPlatformModal: function(platformId) {
            platformId = platformId || 0;
            var $modal = $('.cgt-platform-modal');
            var $form = $modal.find('#cgt-platform-form');

            if (platformId > 0) {
                var $row = $('.cgt-platform-row[data-platform-id="' + platformId + '"]');
                if ($row.length === 0) {
                    this.showNotice('Platform not found', 'error');
                    return;
                }
                $form.find('[name="platform_id"]').val(platformId);
                $form.find('[name="name"]').val($row.data('name'));
                $form.find('[name="icon_url"]').val($row.data('icon'));
                $form.find('[name="game_included"]').prop('checked', $row.data('game-included') == 1).parent().toggleClass('active', $row.data('game-included') == 1);
                $form.find('[name="price"]').val($row.data('price'));
                $form.find('[name="currency"]').val($row.data('currency'));
                $form.find('[name="tier"]').val($row.data('tier'));
                $form.find('[name="unavailable_url"]').val($row.data('unavailable-url') || '');
                $form.find('[name="cta_text"]').val($row.data('cta-text'));
                $form.find('[name="cta_url"]').val($row.data('cta-url'));
                $form.find('[name="display_order"]').val($row.data('order'));
                $form.find('[name="is_active"]').prop('checked', $row.data('active') == 1).parent().toggleClass('active', $row.data('active') == 1);

                if ($row.data('icon')) {
                    $form.find('.cgt-icon-preview').html('<img src="' + $row.data('icon') + '" alt="Icon">');
                } else {
                    $form.find('.cgt-icon-preview').html('');
                }

                $modal.find('.cgt-modal-header h3').text('Edit Platform');
            } else {
                $form[0].reset();
                $form.find('[name="platform_id"]').val(0);
                $form.find('.cgt-icon-preview').html('');
                $form.find('[name="is_active"]').prop('checked', true).parent().addClass('active');
                $form.find('[name="game_included"]').prop('checked', false).parent().removeClass('active');
                $form.find('[name="cta_text"]').val('Play Now');
                $modal.find('.cgt-modal-header h3').text('Add Platform');
            }
            $modal.addClass('active');
        },

        closeModal: function() {
            $('.cgt-modal-overlay').removeClass('active');
        },

        toggleAvailabilityButton: function($btn) {
            var $item = $btn.closest('.cgt-availability-item');
            var platformId = $item.data('platform-id');
            var type = $btn.data('type');
            var status = $btn.data('status');
            var gameId = $item.closest('.cgt-availability-grid').data('game-id');

            $item.find('.cgt-avail-btn[data-type="' + type + '"]').removeClass('active');
            $btn.addClass('active');

            if (!CGTAdmin.availabilityState[gameId]) {
                CGTAdmin.availabilityState[gameId] = {};
            }
            if (!CGTAdmin.availabilityState[gameId][platformId]) {
                CGTAdmin.availabilityState[gameId][platformId] = {
                    is_available: 1,
                    game_included: -1
                };
            }

            if (type === 'availability') {
                CGTAdmin.availabilityState[gameId][platformId].is_available = parseInt(status);
            } else if (type === 'game_included') {
                CGTAdmin.availabilityState[gameId][platformId].game_included = parseInt(status);
            }
        },

        saveAvailability: function($btn) {
            var gameId = $btn.data('game-id');
            if (!gameId || gameId <= 0) {
                this.showNotice('Invalid game ID', 'error');
                return;
            }

            var $grid = $btn.closest('.cgt-admin-card').find('.cgt-availability-grid');
            var availability = {};

            $grid.find('.cgt-availability-item').each(function() {
                var platformId = $(this).data('platform-id');
                var isAvailable = $(this).find('.cgt-avail-available.active').length > 0 ? 1 : 0;
                var gameIncluded = $(this).find('.cgt-avail-included.active').length > 0 ? 1 : ($(this).find('.cgt-avail-required.active').length > 0 ? 0 : -1);

                availability[platformId] = {
                    is_available: isAvailable,
                    game_included: gameIncluded
                };
            });

            this.showLoading($btn, true);
            $.post(cgtAdmin.ajaxUrl, {
                action: 'cgt_save_availability',
                nonce: cgtAdmin.nonce,
                game_id: gameId,
                availability: availability
            }, function(response) {
                CGTAdmin.showLoading($btn, false);
                if (response.success) {
                    CGTAdmin.showNotice(response.data.message, 'success');
                } else {
                    CGTAdmin.showNotice(response.data.message || cgtAdmin.strings.error, 'error');
                }
            });
        },

        saveSettings: function() {
            var settingsForm = $('#cgt-settings-form');
            var displayForm = $('#cgt-display-form');
            var titlesForm = $('#cgt-titles-form');
            var unavailableForm = $('#cgt-unavailable-form');
            var data = {
                action: 'cgt_save_settings',
                nonce: cgtAdmin.nonce,
                theme: settingsForm.find('[name="theme"]').val(),
                primary_color: settingsForm.find('[name="primary_color"]').val(),
                success_color: settingsForm.find('[name="success_color"]').val(),
                danger_color: settingsForm.find('[name="danger_color"]').val(),
                card_radius: settingsForm.find('[name="card_radius"]').val(),
                button_radius: settingsForm.find('[name="button_radius"]').val(),
                spacing: settingsForm.find('[name="spacing"]').val(),
                glassmorphism: settingsForm.find('[name="glassmorphism"]').is(':checked') ? 1 : 0,
                show_price: displayForm.find('[name="show_price"]').is(':checked') ? 1 : 0,
                show_tier: displayForm.find('[name="show_tier"]').is(':checked') ? 1 : 0,
                group_platforms: displayForm.find('[name="group_platforms"]').is(':checked') ? 1 : 0,
                show_group_titles: titlesForm.find('[name="show_group_titles"]').val(),
                available_title: titlesForm.find('[name="available_title"]').val(),
                unavailable_title: titlesForm.find('[name="unavailable_title"]').val(),
                title_font_size: titlesForm.find('[name="title_font_size"]').val(),
                unavailable_button_text: unavailableForm.find('[name="unavailable_button_text"]').val()
            };
            this.showLoading($('.cgt-save-settings'), true);
            $.post(cgtAdmin.ajaxUrl, data, function(response) {
                CGTAdmin.showLoading($('.cgt-save-settings'), false);
                if (response.success) {
                    CGTAdmin.showNotice(response.data.message, 'success');
                } else {
                    CGTAdmin.showNotice(response.data.message || cgtAdmin.strings.error, 'error');
                }
            });
        },

        addGame: function() {
            var gameName = $('#cgt-new-game-name').val().trim();
            var gameTier = $('#cgt-new-game-tier').val();
            if (!gameName) {
                this.showNotice('Please enter a game name', 'error');
                return;
            }
            $.post(cgtAdmin.ajaxUrl, {
                action: 'cgt_add_game',
                nonce: cgtAdmin.nonce,
                game_name: gameName,
                game_tier: gameTier
            }, function(response) {
                if (response.success) {
                    CGTAdmin.showNotice(response.data.message, 'success');
                    $('#cgt-new-game-name').val('');
                    $('#cgt-new-game-tier').val('');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    CGTAdmin.showNotice(response.data.message || cgtAdmin.strings.error, 'error');
                }
            });
        },

        deleteGame: function(gameId) {
            if (!confirm(cgtAdmin.strings.confirmDelete)) {
                return;
            }
            $.post(cgtAdmin.ajaxUrl, {
                action: 'cgt_delete_game',
                nonce: cgtAdmin.nonce,
                game_id: gameId
            }, function(response) {
                if (response.success) {
                    CGTAdmin.showNotice(response.data.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    CGTAdmin.showNotice(response.data.message || cgtAdmin.strings.error, 'error');
                }
            });
        },

        uploadIcon: function($button) {
            var mediaUploader = wp.media({
                title: 'Choose Platform Icon',
                button: {
                    text: 'Use this icon'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                var $input = $button.siblings('[name="icon_url"]');
                $input.val(attachment.url);
                $button.siblings('.cgt-icon-preview').html('<img src="' + attachment.url + '" alt="Icon">');
            });

            mediaUploader.open();
        },

        initColorPickers: function() {
            if ($.fn.wpColorPicker) {
                $('.cgt-color-picker').wpColorPicker();
            }
        },

        initToggles: function() {
            // Bind toggle click events with delegation
            $(document).on('click', '.cgt-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $toggle = $(this);
                var isActive = $toggle.hasClass('active');
                
                // Toggle the active class
                $toggle.toggleClass('active');
                
                // Update the checkbox state
                var checkbox = $toggle.find('input[type="checkbox"]');
                if (checkbox.length) {
                    checkbox.prop('checked', !isActive);
                }
                
                // Trigger change event for any listeners
                checkbox.trigger('change');
            });
            
            // Ensure checkboxes inside toggles sync with toggle state
            $(document).on('change', '.cgt-toggle input[type="checkbox"]', function() {
                var $checkbox = $(this);
                var $toggle = $checkbox.closest('.cgt-toggle');
                if ($checkbox.is(':checked')) {
                    $toggle.addClass('active');
                } else {
                    $toggle.removeClass('active');
                }
            });
        },

        initModals: function() {
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    CGTAdmin.closeModal();
                }
            });
        },

        initThemePreview: function() {
            $('#cgt-theme-preview-toggle').on('click', function() {
                var currentTheme = $('#cgt-theme-select').val();
                var newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                if (currentTheme === 'auto') {
                    newTheme = 'dark';
                }
                document.documentElement.setAttribute('data-theme', newTheme);
            });
        },

        showLoading: function($element, isLoading) {
            if (isLoading) {
                $element.prop('disabled', true).html('<span class="cgt-spinner"></span> ' + cgtAdmin.strings.saving);
            } else {
                $element.prop('disabled', false).text($element.data('original-text') || 'Save');
            }
        },

        showNotice: function(message, type) {
            type = type || 'success';
            var noticeClass = 'cgt-notice-' + type;
            var $notice = $('<div class="cgt-notice ' + noticeClass + '">' + message + '</div>');
            $('.cgt-admin-wrap').prepend($notice);
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        CGTAdmin.init();
    });

    // Expose to global
    window.CGTAdmin = CGTAdmin;

})(jQuery);
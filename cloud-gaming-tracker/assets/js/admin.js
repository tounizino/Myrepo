(function($) {
    'use strict';

    const CGTAdmin = {
        init: function() { 
            this.bindEvents(); 
            this.initColorPickers(); 
            this.initToggles(); 
            this.initModals();
            this.initThemePreview();
        },
        
        bindEvents: function() {
            $(document).on('click', '.cgt-save-platform', (e) => { 
                e.preventDefault(); 
                this.savePlatform($(e.target).closest('form')); 
            });
            
            $(document).on('click', '.cgt-delete-platform', (e) => { 
                e.preventDefault(); 
                const platformId = $(e.target).data('platform-id'); 
                this.deletePlatform(platformId); 
            });
            
            $(document).on('click', '.cgt-edit-platform', (e) => { 
                e.preventDefault(); 
                const platformId = $(e.target).data('platform-id'); 
                this.openPlatformModal(platformId); 
            });
            
            $(document).on('click', '.cgt-add-platform', (e) => { 
                e.preventDefault(); 
                this.openPlatformModal(); 
            });
            
            $(document).on('click', '.cgt-avail-btn', (e) => { 
                e.preventDefault(); 
                this.toggleAvailabilityButton($(e.target)); 
            });
            
            $(document).on('click', '.cgt-save-availability', (e) => { 
                e.preventDefault(); 
                this.saveAvailability($(e.target)); 
            });
            
            $(document).on('click', '.cgt-save-game-settings', (e) => { 
                e.preventDefault(); 
                this.saveGameSettings($(e.target)); 
            });
            
            $(document).on('click', '.cgt-save-settings', (e) => { 
                e.preventDefault(); 
                this.saveSettings($(e.target).closest('form')); 
            });
            
            $(document).on('click', '.cgt-add-game', (e) => { 
                e.preventDefault(); 
                this.addGame(); 
            });
            
            $(document).on('click', '.cgt-delete-game', (e) => { 
                e.preventDefault(); 
                const gameId = $(e.target).data('game-id'); 
                this.deleteGame(gameId); 
            });
            
            $(document).on('click', '.cgt-upload-icon', (e) => { 
                e.preventDefault(); 
                this.uploadIcon($(e.target)); 
            });
            
            $(document).on('click', '.cgt-modal-close, .cgt-modal-overlay', (e) => { 
                if ($(e.target).hasClass('cgt-modal-overlay') || $(e.target).hasClass('cgt-modal-close')) {
                    this.closeModal(); 
                }
            });
        },
        
        savePlatform: function($form) {
            const data = { 
                action: 'cgt_save_platform', 
                nonce: cgtAdmin.nonce, 
                platform_id: $form.find('[name="platform_id"]').val() || 0, 
                name: $form.find('[name="name"]').val(), 
                icon_url: $form.find('[name="icon_url"]').val(), 
                game_included: $form.find('[name="game_included"]').is(':checked') ? 1 : 0, 
                price: $form.find('[name="price"]').val(), 
                currency: $form.find('[name="currency"]').val(), 
                tier: $form.find('[name="tier"]').val(), 
                cta_text: $form.find('[name="cta_text"]').val(), 
                cta_url: $form.find('[name="cta_url"]').val(), 
                display_order: $form.find('[name="display_order"]').val(), 
                is_active: $form.find('[name="is_active"]').is(':checked') ? 1 : 0 
            };
            
            if (!data.name) {
                this.showNotice('Please enter a platform name', 'error');
                return;
            }
            
            this.showLoading($form.find('.cgt-save-platform'), true);
            $.post(cgtAdmin.ajaxUrl, data, (response) => {
                this.showLoading($form.find('.cgt-save-platform'), false);
                if (response.success) { 
                    this.showNotice(response.data.message, 'success'); 
                    setTimeout(() => location.reload(), 1000); 
                } else { 
                    this.showNotice(response.data.message || cgtAdmin.strings.error, 'error'); 
                }
            }).fail(() => {
                this.showLoading($form.find('.cgt-save-platform'), false);
                this.showNotice('Connection error. Please try again.', 'error');
            });
        },
        
        deletePlatform: function(platformId) {
            if (!confirm(cgtAdmin.strings.confirmDelete)) return;
            $.post(cgtAdmin.ajaxUrl, { 
                action: 'cgt_delete_platform', 
                nonce: cgtAdmin.nonce, 
                platform_id: platformId 
            }, (response) => {
                if (response.success) { 
                    this.showNotice(response.data.message, 'success'); 
                    setTimeout(() => location.reload(), 1000); 
                } else { 
                    this.showNotice(response.data.message || cgtAdmin.strings.error, 'error'); 
                }
            }).fail(() => {
                this.showNotice('Connection error. Please try again.', 'error');
            });
        },
        
        openPlatformModal: function(platformId = 0) {
            const $modal = $('.cgt-platform-modal');
            const $form = $modal.find('form');
            
            if (platformId > 0) {
                const $row = $(`.cgt-platform-row[data-platform-id="${platformId}"]`);
                if ($row.length === 0) {
                    this.showNotice('Platform not found', 'error');
                    return;
                }
                
                $form.find('[name="platform_id"]').val(platformId);
                $form.find('[name="name"]').val($row.data('name'));
                $form.find('[name="icon_url"]').val($row.data('icon'));
                
                // FIX: Properly handle checkbox values
                const $gameIncludedCheckbox = $form.find('[name="game_included"]');
                $gameIncludedCheckbox.prop('checked', $row.data('game-included') == 1);
                
                // Update toggle visual state
                const $gameIncludedToggle = $gameIncludedCheckbox.closest('.cgt-toggle');
                if ($row.data('game-included') == 1) {
                    $gameIncludedToggle.addClass('active');
                } else {
                    $gameIncludedToggle.removeClass('active');
                }
                
                $form.find('[name="price"]').val($row.data('price'));
                $form.find('[name="currency"]').val($row.data('currency'));
                $form.find('[name="tier"]').val($row.data('tier'));
                $form.find('[name="cta_text"]').val($row.data('cta-text'));
                $form.find('[name="cta_url"]').val($row.data('cta-url'));
                $form.find('[name="display_order"]').val($row.data('order'));
                
                // FIX: Properly handle is_active checkbox
                const $activeCheckbox = $form.find('[name="is_active"]');
                $activeCheckbox.prop('checked', $row.data('active') == 1);
                
                // Update toggle visual state
                const $activeToggle = $activeCheckbox.closest('.cgt-toggle');
                if ($row.data('active') == 1) {
                    $activeToggle.addClass('active');
                } else {
                    $activeToggle.removeClass('active');
                }
                
                if ($row.data('icon')) {
                    $form.find('.cgt-icon-preview').html(`<img src="${$row.data('icon')}" alt="Icon">`);
                }
                
                $modal.find('.cgt-modal-header h3').text('Edit Platform');
            } else {
                $form[0].reset();
                $form.find('[name="platform_id"]').val(0);
                $form.find('.cgt-icon-preview').html('');
                
                // Set default values
                $form.find('[name="is_active"]').prop('checked', true);
                $form.find('[name="is_active"]').closest('.cgt-toggle').addClass('active');
                $form.find('[name="game_included"]').prop('checked', false);
                $form.find('[name="game_included"]').closest('.cgt-toggle').removeClass('active');
                $form.find('[name="cta_text"]').val('Play Now');
                
                $modal.find('.cgt-modal-header h3').text('Add Platform');
            }
            $modal.addClass('active');
        },
        
        closeModal: function() { 
            $('.cgt-modal-overlay').removeClass('active'); 
        },
        
        toggleAvailabilityButton: function($btn) {
            const $item = $btn.closest('.cgt-availability-item');
            $item.find('.cgt-avail-btn').removeClass('active');
            $btn.addClass('active');
        },
        
        saveAvailability: function($btn) {
            const $grid = $btn.closest('.cgt-admin-card-body').find('.cgt-availability-grid');
            const gameId = $grid.data('game-id');
            const availability = {};
            
            // FIX: Validate game ID
            if (!gameId || gameId <= 0) {
                this.showNotice('Invalid game ID. Please refresh the page.', 'error');
                return;
            }
            
            $grid.find('.cgt-availability-item').each(function() {
                const platformId = $(this).data('platform-id');
                const isActive = $(this).find('.cgt-avail-btn.active').data('status');
                availability[platformId] = isActive !== undefined ? isActive : 1;
            });
            
            this.showLoading($btn, true);
            $.post(cgtAdmin.ajaxUrl, { 
                action: 'cgt_save_availability', 
                nonce: cgtAdmin.nonce, 
                game_id: gameId,
                availability: availability
            }, (response) => {
                this.showLoading($btn, false);
                if (response.success) { 
                    this.showNotice(response.data.message, 'success'); 
                } else { 
                    this.showNotice(response.data.message || cgtAdmin.strings.error, 'error'); 
                }
            }).fail(() => {
                this.showLoading($btn, false);
                this.showNotice('Connection error. Please try again.', 'error');
            });
        },
        
        saveGameSettings: function($btn) {
            const $settings = $btn.closest('.cgt-game-settings');
            const gameId = $settings.data('game-id');
            const gameIncluded = $settings.find('[name="game_included"]').is(':checked') ? 1 : 0;
            const isActive = $settings.find('[name="is_active"]').is(':checked') ? 1 : 0;
            
            // Validate game ID
            if (!gameId || gameId <= 0) {
                this.showNotice('Invalid game ID. Please refresh the page.', 'error');
                return;
            }
            
            this.showLoading($btn, true);
            $.post(cgtAdmin.ajaxUrl, { 
                action: 'cgt_save_game_settings', 
                nonce: cgtAdmin.nonce, 
                game_id: gameId,
                game_included: gameIncluded,
                is_active: isActive
            }, (response) => {
                this.showLoading($btn, false);
                if (response.success) { 
                    this.showNotice(response.data.message, 'success'); 
                } else { 
                    this.showNotice(response.data.message || cgtAdmin.strings.error, 'error'); 
                }
            }).fail(() => {
                this.showLoading($btn, false);
                this.showNotice('Connection error. Please try again.', 'error');
            });
        },
        
        saveSettings: function($form) {
            const data = { 
                action: 'cgt_save_settings', 
                nonce: cgtAdmin.nonce, 
                theme: $form.find('[name="theme"]').val(), 
                primary_color: $form.find('[name="primary_color"]').val(), 
                success_color: $form.find('[name="success_color"]').val(), 
                danger_color: $form.find('[name="danger_color"]').val(), 
                card_radius: $form.find('[name="card_radius"]').val(), 
                button_radius: $form.find('[name="button_radius"]').val(), 
                spacing: $form.find('[name="spacing"]').val(), 
                show_price: $form.find('[name="show_price"]').is(':checked') ? 1 : 0, 
                show_tier: $form.find('[name="show_tier"]').is(':checked') ? 1 : 0, 
                group_platforms: $form.find('[name="group_platforms"]').is(':checked') ? 1 : 0, 
                glassmorphism: $form.find('[name="glassmorphism"]').is(':checked') ? 1 : 0 
            };
            this.showLoading($form.find('.cgt-save-settings'), true);
            $.post(cgtAdmin.ajaxUrl, data, (response) => {
                this.showLoading($form.find('.cgt-save-settings'), false);
                if (response.success) this.showNotice(response.data.message, 'success');
                else this.showNotice(response.data.message || cgtAdmin.strings.error, 'error');
            }).fail(() => {
                this.showLoading($form.find('.cgt-save-settings'), false);
                this.showNotice('Connection error. Please try again.', 'error');
            });
        },
        
        addGame: function() {
            const gameName = $('#cgt-new-game-name').val().trim();
            if (!gameName) { 
                this.showNotice('Please enter a game name', 'error'); 
                return; 
            }
            $.post(cgtAdmin.ajaxUrl, { 
                action: 'cgt_add_game', 
                nonce: cgtAdmin.nonce, 
                game_name: gameName 
            }, (response) => {
                if (response.success) { 
                    this.showNotice(response.data.message, 'success'); 
                    $('#cgt-new-game-name').val(''); 
                    setTimeout(() => location.reload(), 1000); 
                } else { 
                    this.showNotice(response.data.message || cgtAdmin.strings.error, 'error'); 
                }
            }).fail(() => {
                this.showNotice('Connection error. Please try again.', 'error');
            });
        },
        
        deleteGame: function(gameId) {
            if (!confirm(cgtAdmin.strings.confirmDelete)) return;
            $.post(cgtAdmin.ajaxUrl, { 
                action: 'cgt_delete_game', 
                nonce: cgtAdmin.nonce, 
                game_id: gameId 
            }, (response) => {
                if (response.success) { 
                    this.showNotice(response.data.message, 'success'); 
                    setTimeout(() => location.reload(), 1000); 
                } else { 
                    this.showNotice(response.data.message || cgtAdmin.strings.error, 'error'); 
                }
            }).fail(() => {
                this.showNotice('Connection error. Please try again.', 'error');
            });
        },
        
        uploadIcon: function($button) {
            const mediaUploader = wp.media({ 
                title: 'Choose Platform Icon', 
                button: { text: 'Use this icon' }, 
                library: { type: 'image' }, 
                multiple: false 
            });
            mediaUploader.on('select', () => {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                const $input = $button.siblings('[name="icon_url"]');
                $input.val(attachment.url);
                $button.siblings('.cgt-icon-preview').html(`<img src="${attachment.url}" alt="Icon">`);
            });
            mediaUploader.open();
        },
        
        initColorPickers: function() { 
            if ($.fn.wpColorPicker) {
                $('.cgt-color-picker').wpColorPicker();
            }
        },
        
        initToggles: function() { 
            $(document).on('click', '.cgt-toggle', function(e) {
                e.stopPropagation(); // Prevent event bubbling
                $(this).toggleClass('active'); 
                const checkbox = $(this).find('input[type="checkbox"]');
                if (checkbox.length) {
                    checkbox.prop('checked', $(this).hasClass('active'));
                }
            }); 
        },
        
        initModals: function() { 
            $(document).on('keydown', (e) => { 
                if (e.key === 'Escape') this.closeModal(); 
            }); 
        },
        
        initThemePreview: function() {
            $('#cgt-theme-preview-toggle').on('click', function() {
                const currentTheme = $('#cgt-theme-select').val();
                let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                if (currentTheme === 'auto') newTheme = 'dark';
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
        
        showNotice: function(message, type = 'success') {
            const noticeClass = `cgt-notice-${type}`;
            const $notice = $(`<div class="cgt-notice ${noticeClass}">${message}</div>`);
            $('.cgt-admin-wrap').prepend($notice);
            setTimeout(() => { 
                $notice.fadeOut(300, () => $notice.remove()); 
            }, 3000);
        }
    };

    $(document).ready(() => { 
        CGTAdmin.init(); 
    });
    
    window.CGTAdmin = CGTAdmin;

})(jQuery);

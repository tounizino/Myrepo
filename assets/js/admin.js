(function($) {
    'use strict';

    const CGA_Admin = {
        init: function() {
            this.bindEvents();
            this.initSettingsTabs();
        },

        bindEvents: function() {
            // Platform Modal
            $(document).on('click', '.cga-add-platform-btn', this.openPlatformModal.bind(this));
            $(document).on('click', '.cga-edit-platform', this.editPlatform.bind(this));
            $(document).on('click', '.cga-delete-platform', this.deletePlatform.bind(this));

            // Game Modal
            $(document).on('click', '.cga-add-game-btn', this.openGameModal.bind(this));
            $(document).on('click', '.cga-edit-game', this.editGame.bind(this));
            $(document).on('click', '.cga-delete-game', this.deleteGame.bind(this));
            $(document).on('click', '.cga-manage-availability', this.openAvailabilityModal.bind(this));

            // Modal Close
            $(document).on('click', '.cga-modal-close', this.closeModal.bind(this));
            $(document).on('click', '.cga-modal', function(e) {
                if ($(e.target).hasClass('cga-modal')) {
                    CGA_Admin.closeModal();
                }
            });

            // Save Actions
            $(document).on('click', '.cga-save-platform', this.savePlatform.bind(this));
            $(document).on('click', '.cga-save-game', this.saveGame.bind(this));
            $(document).on('click', '.cga-save-availability', this.saveAvailability.bind(this));
            $(document).on('submit', '#cga-settings-form', this.saveSettings.bind(this));

            // Keyboard
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    CGA_Admin.closeModal();
                }
            });
        },

        initSettingsTabs: function() {
            $('.cga-tab-btn').on('click', function() {
                const tabId = $(this).data('tab');
                $('.cga-tab-btn').removeClass('cga-tab-active');
                $('.cga-tab-panel').removeClass('cga-tab-active');
                $(this).addClass('cga-tab-active');
                $('#tab-' + tabId).addClass('cga-tab-active');
            });
        },

        openModal: function(modalId) {
            $('#' + modalId).addClass('active');
            $('body').css('overflow', 'hidden');
        },

        closeModal: function() {
            $('.cga-modal').removeClass('active');
            $('body').css('overflow', '');
        },

        /* Platform Functions */
        openPlatformModal: function() {
            $('#cga-platform-form')[0].reset();
            $('#cga-platform-id').val('');
            $('.cga-modal-header h2').text(cgaAdmin.strings.addPlatform || 'Add Platform');
            this.openModal('cga-platform-modal');
        },

        editPlatform: function(e) {
            e.preventDefault();
            const row = $(e.currentTarget).closest('tr');
            const platformId = row.data('platform-id');

            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'GET',
                data: {
                    action: 'cga_get_platform',
                    id: platformId,
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.data) {
                        const platform = response.data;
                        $('#cga-platform-id').val(platform.id);
                        $('#cga-platform-name').val(platform.name);
                        $('#cga-platform-icon').val(platform.icon_url || '');
                        $('#cga-platform-description').val(platform.description || '');
                        $('#cga-platform-price').val(platform.base_price || 0);
                        $('#cga-platform-currency').val(platform.price_currency || 'USD');
                        $('#cga-platform-period').val(platform.price_period || 'month');
                        $('#cga-platform-tier').val(platform.tier_name || '');
                        $('#cga-platform-cta-text').val(platform.cta_text || 'Play Now');
                        $('#cga-platform-cta-url').val(platform.cta_url || '');
                        $('#cga-platform-notes').val(platform.notes || '');
                        $('#cga-platform-status').val(platform.status || 'active');
                        $('#cga-platform-display-order').val(platform.display_order || 0);
                        
                        $('.cga-modal-header h2').text(cgaAdmin.strings.editPlatform || 'Edit Platform');
                        CGA_Admin.openModal('cga-platform-modal');
                    }
                }
            });
        },

        savePlatform: function() {
            const formData = $('#cga-platform-form').serialize();
            
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: formData + '&action=cga_save_platform&nonce=' + cgaAdmin.nonce,
                beforeSend: function() {
                    $('.cga-save-platform').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        CGA_Admin.closeModal();
                        location.reload();
                    } else {
                        alert(response.data.message || cgaAdmin.strings.error);
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error);
                },
                complete: function() {
                    $('.cga-save-platform').prop('disabled', false).text('Save Platform');
                }
            });
        },

        deletePlatform: function(e) {
            if (!confirm(cgaAdmin.strings.confirmDelete)) {
                return;
            }

            const row = $(e.currentTarget).closest('tr');
            const platformId = row.data('platform-id');

            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cga_delete_platform',
                    id: platformId,
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.data.message || cgaAdmin.strings.error);
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error);
                }
            });
        },

        /* Game Functions */
        openGameModal: function() {
            $('#cga-game-form')[0].reset();
            $('#cga-game-id').val('');
            $('.cga-modal-header h2').text('Add Game');
            this.openModal('cga-game-modal');
        },

        editGame: function(e) {
            e.preventDefault();
            const row = $(e.currentTarget).closest('tr');
            const gameId = row.data('game-id');

            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'GET',
                data: {
                    action: 'cga_get_game',
                    id: gameId,
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.data) {
                        const game = response.data;
                        $('#cga-game-id').val(game.id);
                        $('#cga-game-name').val(game.name);
                        $('#cga-game-description').val(game.description || '');
                        $('#cga-game-cover').val(game.cover_image || '');
                        $('#cga-game-developer').val(game.developer || '');
                        $('#cga-game-publisher').val(game.publisher || '');
                        $('#cga-game-release-date').val(game.release_date || '');
                        $('#cga-game-status').val(game.status || 'active');
                        
                        $('.cga-modal-header h2').text('Edit Game');
                        CGA_Admin.openModal('cga-game-modal');
                    }
                }
            });
        },

        saveGame: function() {
            const formData = $('#cga-game-form').serialize();
            
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: formData + '&action=cga_save_game&nonce=' + cgaAdmin.nonce,
                beforeSend: function() {
                    $('.cga-save-game').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        CGA_Admin.closeModal();
                        location.reload();
                    } else {
                        alert(response.data.message || cgaAdmin.strings.error);
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error);
                },
                complete: function() {
                    $('.cga-save-game').prop('disabled', false).text('Save Game');
                }
            });
        },

        deleteGame: function(e) {
            if (!confirm(cgaAdmin.strings.confirmDelete)) {
                return;
            }

            const row = $(e.currentTarget).closest('tr');
            const gameId = row.data('game-id');

            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cga_delete_game',
                    id: gameId,
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.data.message || cgaAdmin.strings.error);
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error);
                }
            });
        },

        openAvailabilityModal: function(e) {
            e.preventDefault();
            const gameId = $(e.currentTarget).attr('href').match(/game_id=(\d+)/)[1];
            $('#cga-availability-game-id').val(gameId);

            // Load platforms
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'GET',
                data: {
                    action: 'cga_get_availability',
                    game_id: gameId,
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.data) {
                        let html = '';
                        response.data.forEach(function(platform) {
                            html += `
                                <div class="cga-platform-item" data-platform-id="${platform.id}">
                                    <div class="cga-platform-item-icon">
                                        ${platform.icon_url ? `<img src="${platform.icon_url}" alt="${platform.name}">` : '<div class="dashicons dashicons-admin-site"></div>'}
                                    </div>
                                    <div class="cga-platform-item-info">
                                        <h4>${platform.name}</h4>
                                    </div>
                                    <div class="cga-platform-item-controls">
                                        <div class="cga-checkbox-group">
                                            <label>
                                                <input type="checkbox" name="platforms[${platform.id}][is_available]" value="1" ${platform.is_available ? 'checked' : ''}>
                                                Available
                                            </label>
                                        </div>
                                        <div class="cga-checkbox-group">
                                            <label>
                                                <input type="checkbox" name="platforms[${platform.id}][game_included]" value="1" ${platform.game_included ? 'checked' : ''}>
                                                Game Included
                                            </label>
                                        </div>
                                        <input type="number" name="platforms[${platform.id}][custom_price]" class="cga-small-input" placeholder="Price" value="${platform.custom_price || ''}" step="0.01">
                                        <input type="text" name="platforms[${platform.id}][availability_notes]" class="cga-small-input" placeholder="Notes" value="${platform.availability_notes || ''}">
                                    </div>
                                </div>
                            `;
                        });
                        $('#cga-platform-availability-list').html(html);
                        CGA_Admin.openModal('cga-availability-modal');
                    }
                }
            });
        },

        saveAvailability: function() {
            const formData = $('#cga-availability-form').serialize();
            
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: formData + '&action=cga_save_availability&nonce=' + cgaAdmin.nonce,
                beforeSend: function() {
                    $('.cga-save-availability').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        alert(cgaAdmin.strings.saveSuccess);
                        CGA_Admin.closeModal();
                    } else {
                        alert(response.data.message || cgaAdmin.strings.error);
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error);
                },
                complete: function() {
                    $('.cga-save-availability').prop('disabled', false).text('Save Changes');
                }
            });
        },

        saveSettings: function(e) {
            e.preventDefault();
            const formData = $(e.currentTarget).serialize();

            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: formData + '&action=cga_save_settings&nonce=' + cgaAdmin.nonce,
                beforeSend: function() {
                    $('#cga-settings-form').find('.button-primary').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        alert(cgaAdmin.strings.saveSuccess);
                    } else {
                        alert(response.data.message || cgaAdmin.strings.error);
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error);
                },
                complete: function() {
                    $('#cga-settings-form').find('.button-primary').prop('disabled', false);
                }
            });
        }
    };

    $(document).ready(function() {
        CGA_Admin.init();
    });

})(jQuery);

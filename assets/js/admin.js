(function($) {
    'use strict';

    const CGA_Admin = {
        currentGameId: 0,

        init: function() {
            // Get current game ID from URL
            const urlParams = new URLSearchParams(window.location.search);
            this.currentGameId = parseInt(urlParams.get('game_id')) || 0;
            
            this.bindEvents();
            this.initSettingsTabs();
            
            // Load game details if editing
            if (this.currentGameId > 0) {
                this.loadGameDetails(this.currentGameId);
                this.loadAvailability(this.currentGameId);
                this.loadPlatforms();
            }
        },

        bindEvents: function() {
            // List View - Platform Modal
            $(document).on('click', '.cga-add-platform-btn', this.openPlatformModal.bind(this));
            $(document).on('click', '.cga-edit-platform', this.editPlatform.bind(this));
            $(document).on('click', '.cga-delete-platform', this.deletePlatform.bind(this));

            // List View - Game Modal
            $(document).on('click', '.cga-add-game-btn', this.openGameModal.bind(this));
            $(document).on('click', '.cga-edit-game', this.editGame.bind(this));
            $(document).on('click', '.cga-delete-game', this.deleteGame.bind(this));

            // Single Game View
            $(document).on('click', '.cga-manage-game', this.manageGame.bind(this));
            $(document).on('click', '.cga-save-game-details', this.saveGameDetails.bind(this));
            $(document).on('click', '.cga-save-availability', this.saveAvailability.bind(this));
            $(document).on('click', '.cga-toggle-all-available', this.toggleAllAvailable.bind(this));
            $(document).on('click', '.cga-toggle-all-unavailable', this.toggleAllUnavailable.bind(this));
            $(document).on('click', '.cga-preset-btn', this.usePreset.bind(this));

            // Modal Close
            $(document).on('click', '.cga-modal-close', this.closeModal.bind(this));
            $(document).on('click', '.cga-modal', function(e) {
                if ($(e.target).hasClass('cga-modal')) {
                    CGA_Admin.closeModal();
                }
            });

            // Save Actions
            $(document).on('click', '.cga-save-platform', this.savePlatform.bind(this));
            $(document).on('click', '.cga-save-game-modal', this.saveGameModal.bind(this));
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
                type: 'POST',
                data: {
                    action: 'cga_get_platform',
                    id: platformId,
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
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
                    } else {
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error loading platform');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error loading platform');
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
                        CGA_Admin.loadPlatforms();
                        CGA_Admin.loadAvailability(CGA_Admin.currentGameId);
                    } else {
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error saving platform');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error saving platform');
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
                            CGA_Admin.loadPlatforms();
                            CGA_Admin.loadAvailability(CGA_Admin.currentGameId);
                        });
                    } else {
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error deleting platform');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error deleting platform');
                }
            });
        },

        loadPlatforms: function() {
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cga_get_platforms',
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        CGA_Admin.renderCustomPlatforms(response.data);
                    }
                }
            });
        },

        renderCustomPlatforms: function(platforms) {
            let html = '';
            platforms.forEach(function(platform) {
                html += `
                    <tr data-platform-id="${platform.id}">
                        <td>
                            ${platform.icon_url ? `<img src="${platform.icon_url}" alt="${platform.name}" style="width: 40px; height: 40px; object-fit: contain;">` : '<div class="dashicons dashicons-admin-site"></div>'}
                        </td>
                        <td>
                            <strong>${platform.name}</strong>
                        </td>
                        <td>
                            ${platform.base_price > 0 ? '$' + parseFloat(platform.base_price).toFixed(2) + '/' + platform.price_period : 'Free'}
                        </td>
                        <td>
                            <button type="button" class="button button-small cga-edit-platform">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button type="button" class="button button-small cga-delete-platform">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#cga-custom-platforms-list').html(html);
        },

        usePreset: function(e) {
            e.preventDefault();
            const btn = $(e.currentTarget);
            const name = btn.data('name');
            
            $('#cga-platform-name').val(name);
            $('#cga-platform-slug').val(btn.data('slug'));
            
            // Set some defaults for presets
            $('#cga-platform-description').val(`${name} cloud gaming platform`);
            
            this.openModal('cga-platform-modal');
        },

        /* Game Functions - List View */
        openGameModal: function() {
            $('#cga-game-modal-form')[0].reset();
            $('#cga-game-modal-id').val('');
            $('.cga-modal-header h2').text('Add Game');
            this.openModal('cga-game-modal');
        },

        editGame: function(e) {
            e.preventDefault();
            const row = $(e.currentTarget).closest('tr');
            const gameId = row.data('game-id');

            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cga_get_game',
                    id: gameId,
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const game = response.data;
                        $('#cga-game-modal-id').val(game.id);
                        $('#cga-game-modal-name').val(game.name);
                        $('#cga-game-modal-description').val(game.description || '');
                        $('#cga-game-modal-cover').val(game.cover_image || '');
                        $('#cga-game-modal-developer').val(game.developer || '');
                        $('#cga-game-modal-publisher').val(game.publisher || '');
                        $('#cga-game-modal-release-date').val(game.release_date || '');
                        $('#cga-game-modal-status').val(game.status || 'active');
                        
                        $('.cga-modal-header h2').text('Edit Game');
                        CGA_Admin.openModal('cga-game-modal');
                    } else {
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error loading game');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error loading game');
                }
            });
        },

        saveGameModal: function() {
            const formData = $('#cga-game-modal-form').serialize();
            
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: formData + '&action=cga_save_game&nonce=' + cgaAdmin.nonce,
                beforeSend: function() {
                    $('.cga-save-game-modal').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        CGA_Admin.closeModal();
                        location.reload();
                    } else {
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error saving game');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error saving game');
                },
                complete: function() {
                    $('.cga-save-game-modal').prop('disabled', false).text('Save Game');
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
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error deleting game');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error deleting game');
                }
            });
        },

        /* Game Functions - Single Game View */
        manageGame: function(e) {
            // Navigate to single game view (handled by link)
            // This is here for potential future enhancement
        },

        loadGameDetails: function(gameId) {
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cga_get_game',
                    id: gameId,
                    nonce: cgaAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const game = response.data;
                        $('#cga-game-id').val(game.id);
                        $('#cga-game-name').val(game.name);
                        $('#cga-game-description').val(game.description || '');
                        $('#cga-game-cover').val(game.cover_image || '');
                        $('#cga-game-developer').val(game.developer || '');
                        $('#cga-game-publisher').val(game.publisher || '');
                        $('#cga-game-release-date').val(game.release_date || '');
                        $('#cga-game-status').val(game.status || 'active');
                    }
                }
            });
        },

        saveGameDetails: function() {
            const formData = $('#cga-game-form').serialize();
            
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: formData + '&action=cga_save_game&nonce=' + cgaAdmin.nonce,
                beforeSend: function() {
                    $('.cga-save-game-details').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        alert(cgaAdmin.strings.saveSuccess || 'Saved successfully!');
                    } else {
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error saving game');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error saving game');
                },
                complete: function() {
                    $('.cga-save-game-details').prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> ' + (cgaAdmin.strings.saveGame || 'Save Game'));
                }
            });
        },

        /* Availability Functions */
        loadAvailability: function(gameId) {
            if (!gameId) return;
            
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cga_get_availability',
                    game_id: gameId,
                    nonce: cgaAdmin.nonce
                },
                beforeSend: function() {
                    $('#cga-platform-availability-list').html(`
                        <div class="cga-loading-spinner">
                            <span class="spinner is-active"></span>
                            <p>Loading platforms...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    if (response.success && response.data) {
                        CGA_Admin.renderAvailabilityList(response.data);
                    } else {
                        $('#cga-platform-availability-list').html('<p>Error loading platforms</p>');
                    }
                }
            });
        },

        renderAvailabilityList: function(platforms) {
            let html = '';
            platforms.forEach(function(platform) {
                html += `
                    <div class="cga-platform-item" data-platform-id="${platform.id}">
                        <div class="cga-platform-item-icon">
                            ${platform.icon_url ? `<img src="${platform.icon_url}" alt="${platform.name}">` : '<div class="dashicons dashicons-admin-site"></div>'}
                        </div>
                        <div class="cga-platform-item-info">
                            <h4>${platform.name}</h4>
                            <div class="cga-platform-meta">
                                ${platform.base_price > 0 ? `<span>$${parseFloat(platform.base_price).toFixed(2)}/${platform.price_period}</span>` : '<span>Free</span>'}
                            </div>
                        </div>
                        <div class="cga-platform-item-controls">
                            <label class="cga-switch">
                                <input type="checkbox" name="platforms[${platform.id}][is_available]" value="1" ${platform.is_available ? 'checked' : ''}>
                                <span class="cga-switch-slider"></span>
                                <span class="cga-switch-label">${platform.is_available ? 'Available' : 'Unavailable'}</span>
                            </label>
                            <div class="cga-checkbox-group">
                                <label>
                                    <input type="checkbox" name="platforms[${platform.id}][game_included]" value="1" ${platform.game_included ? 'checked' : ''}>
                                    Game Included
                                </label>
                            </div>
                            <input type="number" name="platforms[${platform.id}][custom_price]" class="cga-small-input" placeholder="Custom Price" value="${platform.custom_price || ''}" step="0.01">
                            <input type="text" name="platforms[${platform.id}][availability_notes]" class="cga-small-input" placeholder="Notes" value="${platform.availability_notes || ''}">
                        </div>
                    </div>
                `;
            });
            $('#cga-platform-availability-list').html(html);
            $('.cga-save-availability').show();
        },

        saveAvailability: function() {
            if (!this.currentGameId) {
                alert('No game selected');
                return;
            }

            const formData = $('#cga-availability-form').serialize();
            
            $.ajax({
                url: cgaAdmin.ajaxUrl,
                type: 'POST',
                data: formData + '&action=cga_save_availability&game_id=' + this.currentGameId + '&nonce=' + cgaAdmin.nonce,
                beforeSend: function() {
                    $('.cga-save-availability').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        alert(cgaAdmin.strings.saveSuccess || 'Saved successfully!');
                    } else {
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error saving availability');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error saving availability');
                },
                complete: function() {
                    $('.cga-save-availability').prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> ' + (cgaAdmin.strings.saveAvailability || 'Save Availability'));
                }
            });
        },

        toggleAllAvailable: function() {
            $('#cga-platform-availability-list input[name*="[is_available]"]').prop('checked', true);
            this.updateAvailabilityLabels();
        },

        toggleAllUnavailable: function() {
            $('#cga-platform-availability-list input[name*="[is_available]"]').prop('checked', false);
            this.updateAvailabilityLabels();
        },

        updateAvailabilityLabels: function() {
            $('#cga-platform-availability-list input[name*="[is_available]"]').each(function() {
                const label = $(this).siblings('.cga-switch-label');
                label.text($(this).prop('checked') ? 'Available' : 'Unavailable');
            });
        },

        /* Settings Functions */
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
                        alert(cgaAdmin.strings.saveSuccess || 'Saved successfully!');
                    } else {
                        alert(response.data?.message || cgaAdmin.strings.error || 'Error saving settings');
                    }
                },
                error: function() {
                    alert(cgaAdmin.strings.error || 'Error saving settings');
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

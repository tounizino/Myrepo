/**
 * Cloud Layout Builder Pro - Admin Scripts
 */
(function($) {
    'use strict';

    const CLBPAdmin = {
        init() {
            this.initSortable();
            this.initBlockLibrary();
            this.initBlockActions();
            this.handleSave();
            this.handleImport();
            this.handleExport();
            this.initColorPickers();
        },

        initBlockLibrary() {
            $('#clbp-add-block').on('click', function() {
                $('#clbp-block-library').attr('aria-hidden', 'false').fadeIn(200);
            });

            $('.clbp-library-close, #clbp-block-library').on('click', function(e) {
                if (e.target === this) {
                    $('#clbp-block-library').fadeOut(200, function() {
                        $(this).attr('aria-hidden', 'true');
                    });
                }
            });

            $('.clbp-library-item').on('click', function() {
                const blockType = $(this).data('block-type');
                const blockLabel = $(this).find('.clbp-library-item__title').text();
                CLBPAdmin.addBlock(blockType, blockLabel);
                $('#clbp-block-library').fadeOut(200).attr('aria-hidden', 'true');
            });
        },

        addBlock(type, label) {
            const blockHtml = `
                <div class="clbp-block-card clbp-block-item" data-settings='{"id":"block_${Date.now()}","type":"${type}","enabled":true,"settings":{}}'>
                    <div class="clbp-block-info">
                        <span class="dashicons dashicons-menu"></span>
                        <h3 class="clbp-block-title-label">${label}</h3>
                        <label class="clbp-toggle">
                            <input type="checkbox" class="clbp-toggle-enabled" checked>
                            <span>Enabled</span>
                        </label>
                    </div>
                    <div class="clbp-block-summary"></div>
                    <div class="clbp-block-actions">
                        <button type="button" class="clbp-btn-config" title="Configure">
                            <span class="dashicons dashicons-admin-generic"></span>
                        </button>
                        <button type="button" class="clbp-btn-delete" title="Delete">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
            `;
            $('.clbp-block-list').append(blockHtml);
            CLBPAdmin.showNotice('success', `${label} block added successfully!`);
        },

        initBlockActions() {
            $(document).on('click', '.clbp-btn-delete', function() {
                if (confirm('Are you sure you want to delete this block?')) {
                    $(this).closest('.clbp-block-item').fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });

            $(document).on('click', '.clbp-btn-config', function() {
                alert('Block configuration coming soon! For now, use the Customizer or edit settings via shortcode attributes.');
            });
        },

        initSortable() {
            const $list = $('.clbp-block-list');
            if (!$list.length) return;

            $list.sortable({
                handle: '.clbp-block-card',
                placeholder: 'clbp-block-placeholder',
                helper: 'clone',
                tolerance: 'pointer'
            });
        },

        handleSave() {
            const $form = $('#clbp-builder-form');
            if (!$form.length) return;

            $form.on('submit', (event) => {
                event.preventDefault();

                const blocks = [];
                $('.clbp-block-item').each(function(index) {
                    const $item = $(this);
                    const data = $item.data('settings') || {};
                    data.order = index + 1;
                    data.enabled = $item.find('.clbp-toggle-enabled').is(':checked');
                    blocks.push(data);
                });

                $.post(clbpAdmin.ajaxUrl, {
                    action: 'clbp_save_homepage_blocks',
                    nonce: clbpAdmin.nonce,
                    blocks: JSON.stringify(blocks)
                }).done((response) => {
                    if (response.success) {
                        CLBPAdmin.showNotice('success', response.data.message);
                    } else {
                        CLBPAdmin.showNotice('error', response.data.message || 'Failed to save blocks');
                    }
                });
            });
        },

        handleImport() {
            const $importForm = $('#clbp-import-form');
            if (!$importForm.length) return;

            $importForm.on('submit', function(event) {
                event.preventDefault();
                const json = $('#clbp-import-input').val();

                $.post(clbpAdmin.ajaxUrl, {
                    action: 'clbp_import_settings',
                    nonce: clbpAdmin.nonce,
                    data: json
                }).done((response) => {
                    if (response.success) {
                        CLBPAdmin.showNotice('success', response.data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        CLBPAdmin.showNotice('error', response.data.message || 'Import failed');
                    }
                });
            });
        },

        handleExport() {
            const $exportBtn = $('#clbp-export-button');
            if (!$exportBtn.length) return;

            $exportBtn.on('click', function() {
                $.post(clbpAdmin.ajaxUrl, {
                    action: 'clbp_export_settings',
                    nonce: clbpAdmin.nonce
                }).done((response) => {
                    if (response.success) {
                        const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(response.data.data, null, 2));
                        const downloadAnchor = document.createElement('a');
                        downloadAnchor.setAttribute('href', dataStr);
                        downloadAnchor.setAttribute('download', 'cloud-layout-builder-settings.json');
                        document.body.appendChild(downloadAnchor);
                        downloadAnchor.click();
                        downloadAnchor.remove();
                        CLBPAdmin.showNotice('success', 'Settings exported successfully');
                    } else {
                        CLBPAdmin.showNotice('error', response.data.message || 'Export failed');
                    }
                });
            });
        },

        initColorPickers() {
            $('.clbp-color-field').wpColorPicker();
        },

        showNotice(type, message) {
            const $notice = $('<div class="clbp-notice clbp-notice--' + type + '">' + message + '</div>');
            $('.clbp-admin-wrap').prepend($notice);
            setTimeout(() => $notice.fadeOut(300, () => $notice.remove()), 4000);
        }
    };

    $(function() {
        CLBPAdmin.init();
    });

})(jQuery);

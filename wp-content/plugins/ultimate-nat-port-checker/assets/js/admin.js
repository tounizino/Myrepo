(function($) {
    'use strict';

    const UNPCAdmin = {
        init: function() {
            this.initColorPickers();
            this.setupTabs();
            this.setupGuides();
        },

        initColorPickers: function() {
            if ($.fn.wpColorPicker) {
                $('.color-picker').wpColorPicker();
            }
        },

        setupTabs: function() {
            const $tabs = $('.unpc-admin-tabs .nav-tab');
            const $contents = $('.unpc-admin-tabs .tab-content');
            $tabs.on('click', function(e) {
                e.preventDefault();
                const target = $(this).attr('href');
                $tabs.removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $contents.removeClass('active');
                $(target).addClass('active');
            });
            // Activate first tab by default if none active
            if (!$tabs.filter('.nav-tab-active').length) {
                $tabs.first().addClass('nav-tab-active');
                $contents.first().addClass('active');
            }
        },

        setupGuides: function() {
            const container = $('#guides-container');
            $('#add-guide').on('click', function() {
                const index = container.find('.guide-row').length;
                const template = UNPCAdmin.buildGuideRow(index);
                container.append(template);
            });

            container.on('click', '.remove-guide', function() {
                if (container.find('.guide-row').length === 1) {
                    alert('At least one guide entry must remain.');
                    return;
                }
                $(this).closest('.guide-row').remove();
                UNPCAdmin.reindexGuides();
            });
        },

        buildGuideRow: function(index) {
            return `
                <div class="guide-row">
                    <div class="guide-fields">
                        <div class="guide-field">
                            <label>Title</label>
                            <input type="text" name="unpc_settings[guides][${index}][title]" placeholder="Guide Title" />
                        </div>
                        <div class="guide-field">
                            <label>URL</label>
                            <input type="url" name="unpc_settings[guides][${index}][url]" placeholder="https://" />
                        </div>
                        <div class="guide-field">
                            <label>Description</label>
                            <textarea name="unpc_settings[guides][${index}][description]" placeholder="Short summary"></textarea>
                        </div>
                    </div>
                    <button type="button" class="button button-secondary remove-guide">Remove</button>
                </div>
            `;
        },

        reindexGuides: function() {
            $('#guides-container .guide-row').each(function(i) {
                $(this).find('[name]').each(function() {
                    const name = $(this).attr('name');
                    const newName = name.replace(/guides\]\[\d+\]/, 'guides[' + i + ']');
                    $(this).attr('name', newName);
                });
            });
        }
    };

    $(function() {
        UNPCAdmin.init();
    });

})(jQuery);

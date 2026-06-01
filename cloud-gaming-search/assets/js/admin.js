(function($) {
    'use strict';

    $(document).ready(function() {
        // Save settings
        $('#cgs-settings-form').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();
            formData += '&action=cgs_save_settings';

            $.ajax({
                url: CGS_Admin.ajax_url,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('.cgs-admin .button-primary').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        $('.cgs-admin .button-primary').after(
                            '<span class="cgs-saved-notice" style="color: #00d68f; margin-left: 12px; font-weight: 600;">\u2713 Settings saved</span>'
                        );
                        setTimeout(function() {
                            $('.cgs-saved-notice').fadeOut(300, function() { $(this).remove(); });
                        }, 3000);
                    }
                },
                complete: function() {
                    $('.cgs-admin .button-primary').prop('disabled', false).text('Save Settings');
                }
            });
        });

        // Clear cache
        $('#cgs-clear-cache').on('click', function() {
            $.ajax({
                url: CGS_Admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'cgs_clear_cache',
                    cgs_nonce: CGS_Admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $(this).after(
                            '<span class="cgs-cache-notice" style="color: #00d68f; margin-left: 12px; font-weight: 600;">\u2713 Cache cleared</span>'
                        );
                        setTimeout(function() {
                            $('.cgs-cache-notice').fadeOut(300, function() { $(this).remove(); });
                        }, 3000);
                    }
                }
            });
        });
    });
})(jQuery);
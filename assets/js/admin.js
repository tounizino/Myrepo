/**
 * Ultimate Blocks for Cloud Gaming - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Initialize color pickers
        $('.ubcg-color-picker').wpColorPicker({
            change: function(event, ui) {
                var color = ui.color.toString();
                $(this).val(color).trigger('change');
            },
            clear: function() {
                $(this).trigger('change');
            }
        });
        
        // Settings form validation
        $('form[action="options.php"]').on('submit', function(e) {
            var postsPerPage = $('#ubcg_posts_per_page').val();
            
            if (postsPerPage < 1 || postsPerPage > 100) {
                e.preventDefault();
                alert('Posts per page must be between 1 and 100.');
                $('#ubcg_posts_per_page').focus();
                return false;
            }
        });
        
        // Add visual feedback for settings changes
        $('input[type="text"], input[type="number"], input[type="checkbox"]').on('change', function() {
            var $row = $(this).closest('tr');
            $row.css('background-color', '#eff6ff');
            setTimeout(function() {
                $row.css('background-color', '');
            }, 500);
        });
        
    });

})(jQuery);

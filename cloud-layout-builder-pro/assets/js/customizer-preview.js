/**
 * Cloud Layout Builder Pro - Customizer Live Preview
 */
(function($) {
    wp.customize('clbp_settings[primary_color]', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--clbp-primary', to);
        });
    });

    wp.customize('clbp_settings[secondary_color]', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--clbp-secondary', to);
        });
    });

    wp.customize('clbp_settings[font_family]', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--clbp-font-family', to);
        });
    });
})(jQuery);

(function($) {
    'use strict';

    function initColorPickers() {
        $('.aapd-color-picker').wpColorPicker({
            change: updatePreview,
            clear: updatePreview
        });
    }

    function applyThemePreset(preset) {
        if (!window.AAPDThemes || !window.AAPDThemes[preset]) {
            return;
        }

        const colors = window.AAPDThemes[preset];

        $('#button_gradient_start').wpColorPicker('color', colors.button_gradient_start);
        $('#button_gradient_end').wpColorPicker('color', colors.button_gradient_end);
        $('#accent_color').wpColorPicker('color', colors.accent_color);
        $('#hover_color').wpColorPicker('color', colors.hover_color);

        if (colors.dark_mode !== undefined) {
            $('input[name="aapd_settings[dark_mode]"]').prop('checked', !!colors.dark_mode);
        }

        updatePreview();
    }

    function toggleGoogleFontField() {
        const choice = $('#font_choice').val();
        if (choice === 'google') {
            $('.aapd-google-font-field').slideDown(150);
        } else {
            $('.aapd-google-font-field').slideUp(150);
        }
    }

    function updatePreview() {
        const $preview = $('.aapd-preview-layout');
        if ($preview.length === 0) {
            return;
        }

        const buttonStart = $('#button_gradient_start').val() || '#ff9900';
        const buttonEnd = $('#button_gradient_end').val() || '#ffcc00';
        const accent = $('#accent_color').val() || '#146eb4';
        const hover = $('#hover_color').val() || '#f37300';
        const darkMode = $('input[name="aapd_settings[dark_mode]"]').is(':checked');

        document.documentElement.style.setProperty('--aapd-button-start', buttonStart);
        document.documentElement.style.setProperty('--aapd-button-end', buttonEnd);
        document.documentElement.style.setProperty('--aapd-accent', accent);
        document.documentElement.style.setProperty('--aapd-hover', hover);

        $preview.toggleClass('aapd-dark', darkMode);
    }

    $(function() {
        initColorPickers();
        toggleGoogleFontField();
        updatePreview();

        $('#font_choice').on('change', toggleGoogleFontField);
        $('.aapd-theme-preset').on('change', function() {
            applyThemePreset($(this).val());
        });

        $('input[name="aapd_settings[dark_mode]"]').on('change', updatePreview);
    });

})(jQuery);

(function($) {
    'use strict';

    $(document).ready(function() {
        const $resourcesGrid = $('.cgst-resources-grid');
        const maxResources = 8;

        $('#cgst-add-resource').on('click', function(e) {
            e.preventDefault();

            const currentItems = $resourcesGrid.find('.cgst-resource-item').length;

            if (currentItems >= maxResources) {
                window.alert('Maximum number of resources reached.');
                return;
            }

            const index = currentItems;

            const template = `
                <div class="cgst-resource-item">
                    <label>
                        <span>${wp.i18n.__('Resource', 'cloud-gaming-speedtest')} ${index + 1} ${wp.i18n.__('Title', 'cloud-gaming-speedtest')}</span>
                        <input type="text" name="cgst_custom_resources[${index}][title]" value="" />
                    </label>
                    <label>
                        <span>${wp.i18n.__('Resource', 'cloud-gaming-speedtest')} ${index + 1} URL</span>
                        <input type="url" name="cgst_custom_resources[${index}][url]" value="" placeholder="https://" />
                    </label>
                </div>`;

            $resourcesGrid.append(template);
        });
    });
})(jQuery);

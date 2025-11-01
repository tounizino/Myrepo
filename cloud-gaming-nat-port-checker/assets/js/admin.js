(function($) {
    'use strict';
    
    const CGNPCAdmin = {
        init() {
            this.initColorPicker();
            this.bindEvents();
        },
        
        initColorPicker() {
            if ($.fn.wpColorPicker) {
                $('.cgnpc-color-field').wpColorPicker();
            }
        },
        
        bindEvents() {
            $(document).on('click', '#cgnpc-add-guide', this.addGuide.bind(this));
            $(document).on('click', '.cgnpc-remove-guide', this.removeGuide.bind(this));
            $(document).on('click', '.cgnpc-upload-image', this.uploadImage.bind(this));
        },
        
        addGuide(e) {
            e.preventDefault();
            const $container = $('#cgnpc-guides-container');
            const count = $container.find('.cgnpc-guide-item').length;
            
            const html = `
                <div class="cgnpc-guide-item" data-index="${count}">
                    <header>
                        <strong>Guide #${count + 1}</strong>
                        <button type="button" class="button button-link-delete cgnpc-remove-guide" aria-label="Remove guide">&times;</button>
                    </header>
                    <div class="cgnpc-guide-fields">
                        <label>
                            <span>Title</span>
                            <input type="text" name="cgnpc_settings[guides][${count}][title]" value="" class="regular-text" />
                        </label>
                        <label>
                            <span>Image URL</span>
                            <input type="text" name="cgnpc_settings[guides][${count}][image]" value="" class="regular-text cgnpc-image-url" />
                            <button type="button" class="button cgnpc-upload-image">Upload</button>
                        </label>
                        <label>
                            <span>Excerpt</span>
                            <textarea name="cgnpc_settings[guides][${count}][excerpt]" rows="3" class="large-text"></textarea>
                        </label>
                        <label>
                            <span>Link</span>
                            <input type="url" name="cgnpc_settings[guides][${count}][link]" value="" class="regular-text" />
                        </label>
                    </div>
                </div>
            `;
            
            $container.append(html);
        },
        
        removeGuide(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to remove this guide?')) {
                $(e.currentTarget).closest('.cgnpc-guide-item').fadeOut(function() {
                    $(this).remove();
                    CGNPCAdmin.reindexGuides();
                });
            }
        },
        
        reindexGuides() {
            $('#cgnpc-guides-container .cgnpc-guide-item').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('header strong').text('Guide #' + (index + 1));
                $(this).find('input, textarea').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/\[guides\]\[\d+\]/, '[guides][' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        },
        
        uploadImage(e) {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const $input = $button.siblings('.cgnpc-image-url');
            
            if (typeof wp !== 'undefined' && wp.media) {
                const frame = wp.media({
                    title: 'Select or Upload Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });
                
                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    $input.val(attachment.url);
                });
                
                frame.open();
            } else {
                alert('Media uploader not available. Please add the image URL manually.');
            }
        }
    };
    
    $(document).ready(() => {
        CGNPCAdmin.init();
    });
    
})(jQuery);

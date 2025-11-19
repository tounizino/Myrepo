(function($){
    function updateConditionalFields($container) {
        var source = $container.find('.sap-data-source').val();
        $container.find('.sap-conditional-wrapper').each(function(){
            var condition = $(this).data('condition');
            if (condition === source) {
                $(this).slideDown(120);
            } else {
                $(this).slideUp(120);
            }
        });
    }

    $(document).ready(function(){
        $('.sap-color-field').wpColorPicker();

        $('.sap-section-card').each(function(){
            var $card = $(this);
            updateConditionalFields($card);

            $card.find('.sap-data-source').on('change', function(){
                updateConditionalFields($card);
            });
        });

        $('.sap-section-shortcode').on('click', function(){
            var text = $(this).text();
            navigator.clipboard.writeText(text).then(function(){
                var original = $(this).text();
                $(this).text('Copied!');
                var self = this;
                setTimeout(function(){ $(self).text(original); }, 1500);
            }.bind(this));
        });
    });
})(jQuery);

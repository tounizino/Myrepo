(function($) {
    'use strict';

    function initCarousels() {
        $('.aapd-carousel').each(function() {
            const $carousel = $(this);
            const $track = $carousel.find('.aapd-carousel-track');
            const $prevBtn = $carousel.find('.aapd-carousel-prev');
            const $nextBtn = $carousel.find('.aapd-carousel-next');
            const $dotsContainer = $carousel.find('.aapd-carousel-dots');
            const slideCount = parseInt($carousel.attr('data-slides'), 10) || 0;

            if (slideCount === 0) {
                return;
            }

            for (let i = 0; i < slideCount; i++) {
                $dotsContainer.append('<button type="button" aria-label="Go to slide ' + (i + 1) + '"></button>');
            }

            const $dots = $dotsContainer.find('button');
            let currentIndex = 0;

            function updateDots() {
                $dots.removeClass('active').eq(currentIndex).addClass('active');
            }

            function scrollToSlide(index) {
                const slideWidth = $track.find('.aapd-carousel-slide').first().outerWidth(true);
                $track.scrollLeft(slideWidth * index);
                currentIndex = index;
                updateDots();
            }

            $prevBtn.on('click', function() {
                const newIndex = currentIndex > 0 ? currentIndex - 1 : slideCount - 1;
                scrollToSlide(newIndex);
            });

            $nextBtn.on('click', function() {
                const newIndex = currentIndex < slideCount - 1 ? currentIndex + 1 : 0;
                scrollToSlide(newIndex);
            });

            $dots.on('click', function() {
                const index = $(this).index();
                scrollToSlide(index);
            });

            updateDots();

            $track.on('scroll', function() {
                const slideWidth = $track.find('.aapd-carousel-slide').first().outerWidth(true);
                const scrollLeft = $track.scrollLeft();
                const calculatedIndex = Math.round(scrollLeft / slideWidth);
                if (calculatedIndex !== currentIndex) {
                    currentIndex = calculatedIndex;
                    updateDots();
                }
            });
        });
    }

    $(document).ready(function() {
        initCarousels();
    });

})(jQuery);

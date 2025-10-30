/**
 * Cloud Layout Builder Pro - Frontend Scripts
 */

(function($) {
    'use strict';

    const CLBP = {
        init: function() {
            this.initCarousels();
            this.initInfiniteScroll();
            this.initNewsletter();
            this.initLazyLoad();
        },

        initCarousels: function() {
            $('.clbp-carousel').each(function() {
                const $carousel = $(this).closest('[data-carousel="true"]');
                const $track = $(this).find('.clbp-carousel__track');
                const $slides = $track.find('.clbp-carousel__slide');
                const autoplay = $carousel.data('autoplay') === true;
                const speed = parseInt($carousel.data('speed')) || 5000;
                const itemsPerView = parseInt($carousel.data('items')) || 3;
                
                let currentIndex = 0;
                let autoplayInterval;

                // Navigation
                $carousel.find('.clbp-carousel__nav--prev').on('click', function() {
                    currentIndex = Math.max(0, currentIndex - 1);
                    updateCarousel();
                });

                $carousel.find('.clbp-carousel__nav--next').on('click', function() {
                    currentIndex = Math.min($slides.length - itemsPerView, currentIndex + 1);
                    updateCarousel();
                });

                function updateCarousel() {
                    const offset = currentIndex * ($slides.first().outerWidth(true));
                    $track.css('transform', `translateX(-${offset}px)`);
                }

                // Autoplay
                if (autoplay) {
                    autoplayInterval = setInterval(function() {
                        currentIndex++;
                        if (currentIndex > $slides.length - itemsPerView) {
                            currentIndex = 0;
                        }
                        updateCarousel();
                    }, speed);

                    $carousel.on('mouseenter', function() {
                        clearInterval(autoplayInterval);
                    });

                    $carousel.on('mouseleave', function() {
                        autoplayInterval = setInterval(function() {
                            currentIndex++;
                            if (currentIndex > $slides.length - itemsPerView) {
                                currentIndex = 0;
                            }
                            updateCarousel();
                        }, speed);
                    });
                }

                // Responsive handling
                function handleResize() {
                    const width = $(window).width();
                    if (width < 768) {
                        $track.css('transform', 'translateX(0)');
                    } else {
                        updateCarousel();
                    }
                }

                $(window).on('resize', handleResize);
            });
        },

        initInfiniteScroll: function() {
            const $loadMoreBtn = $('.clbp-load-more');

            if ($loadMoreBtn.length === 0) return;

            $loadMoreBtn.on('click', function(e) {
                e.preventDefault();

                const $btn = $(this);
                const $container = $btn.closest('.clbp-block');
                const $grid = $container.find('.clbp-articles-grid');
                const query = $btn.data('query');
                const currentPage = parseInt($container.attr('data-page')) || 1;

                $btn.text('Loading...').prop('disabled', true);

                $.ajax({
                    url: clbpData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'clbp_load_more_posts',
                        nonce: clbpData.nonce,
                        query: query,
                        page: currentPage + 1
                    },
                    success: function(response) {
                        if (response.success && response.data.html) {
                            $grid.append(response.data.html);
                            $container.attr('data-page', currentPage + 1);
                            $btn.text('Load more articles').prop('disabled', false);

                            if (!response.data.hasMore) {
                                $btn.fadeOut();
                            }
                        } else {
                            $btn.text('No more articles').prop('disabled', true);
                        }
                    },
                    error: function() {
                        $btn.text('Error loading posts').prop('disabled', false);
                    }
                });
            });
        },

        initNewsletter: function() {
            $('.clbp-newsletter__form').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $button = $form.find('.clbp-newsletter__button');
                const $message = $form.find('.clbp-newsletter__message');

                $button.text('Subscribing...').prop('disabled', true);
                $message.empty();

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $message.html('<p style="color: #10B981; margin-top: 12px;">✓ Successfully subscribed!</p>');
                            $form[0].reset();
                        } else {
                            $message.html('<p style="color: #EF4444; margin-top: 12px;">⚠ ' + (response.data?.message || 'Subscription failed') + '</p>');
                        }
                        $button.text('Subscribe Now').prop('disabled', false);
                    },
                    error: function() {
                        $message.html('<p style="color: #EF4444; margin-top: 12px;">⚠ An error occurred. Please try again.</p>');
                        $button.text('Subscribe Now').prop('disabled', false);
                    }
                });
            });
        },

        initLazyLoad: function() {
            if ('IntersectionObserver' in window) {
                const lazyImages = document.querySelectorAll('img[loading="lazy"]');
                
                const imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                            observer.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(function(img) {
                    imageObserver.observe(img);
                });
            }
        }
    };

    $(document).ready(function() {
        CLBP.init();
    });

})(jQuery);

/**
 * Ultimate Blocks for Cloud Gaming - Frontend JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Newsletter form submission
        $('.ubcg-newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $button = $form.find('.ubcg-submit-button');
            var originalText = $button.text();
            
            $button.prop('disabled', true).text('Subscribing...');
            
            $.ajax({
                url: ubcgData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ubcg_newsletter_signup',
                    nonce: ubcgData.nonce,
                    email: $form.find('input[name="email"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        $form.after('<div class="ubcg-success-message">' + response.data.message + '</div>');
                        $form.hide();
                    } else {
                        alert(response.data.message || 'An error occurred. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Lazy loading enhancement for images
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.classList.add('ubcg-loaded');
                        observer.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('.ubcg-post-thumbnail img').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
        
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 600);
            }
        });
        
        // Add animation when elements enter viewport
        if ('IntersectionObserver' in window) {
            var animationObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('ubcg-animate-in');
                    }
                });
            }, {
                threshold: 0.1
            });
            
            document.querySelectorAll('.ubcg-post-card, .ubcg-category-item, .ubcg-stat-item').forEach(function(el) {
                animationObserver.observe(el);
            });
        }
        
        // Responsive video embeds
        $('.ubcg-post-content iframe').wrap('<div class="ubcg-video-wrapper"></div>');
        
        // Handle focus states for accessibility
        $('.ubcg-post-card a, .ubcg-category-link, .ubcg-platform-item').on('focus', function() {
            $(this).closest('.ubcg-post-card, .ubcg-category-item, .ubcg-platform-item').addClass('ubcg-focus');
        }).on('blur', function() {
            $(this).closest('.ubcg-post-card, .ubcg-category-item, .ubcg-platform-item').removeClass('ubcg-focus');
        });
        
    });

})(jQuery);

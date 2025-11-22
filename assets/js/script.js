jQuery(document).ready(function($) {
    const urlInput = $('#svd-url-input');
    const downloadBtn = $('#svd-download-btn');
    const result = $('#svd-result');
    const error = $('#svd-error');
    const buttonText = $('.svd-button-text');
    const buttonLoader = $('.svd-button-loader');
    
    downloadBtn.on('click', function() {
        const url = urlInput.val().trim();
        
        if (!url) {
            showError('Please enter a valid URL');
            return;
        }
        
        if (!isValidUrl(url)) {
            showError('Please enter a valid video URL');
            return;
        }
        
        setLoading(true);
        error.hide();
        result.hide();
        
        $.ajax({
            url: svdAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'download_social_video',
                url: url,
                nonce: svdAjax.nonce
            },
            success: function(response) {
                setLoading(false);
                
                if (response.success) {
                    displayResult(response.data);
                } else {
                    showError(response.data.message || 'An error occurred. Please try again.');
                }
            },
            error: function() {
                setLoading(false);
                showError('Network error. Please check your connection and try again.');
            }
        });
    });
    
    urlInput.on('keypress', function(e) {
        if (e.which === 13) {
            downloadBtn.click();
        }
    });
    
    urlInput.on('input', function() {
        error.hide();
    });
    
    function isValidUrl(string) {
        try {
            const url = new URL(string);
            return url.protocol === 'http:' || url.protocol === 'https:';
        } catch (_) {
            return false;
        }
    }
    
    function setLoading(loading) {
        if (loading) {
            buttonText.hide();
            buttonLoader.show();
            downloadBtn.prop('disabled', true);
        } else {
            buttonText.show();
            buttonLoader.hide();
            downloadBtn.prop('disabled', false);
        }
    }
    
    function showError(message) {
        error.text(message).fadeIn();
        result.hide();
    }
    
    function displayResult(data) {
        $('#svd-video-title').text(data.title);
        $('#svd-video-platform').text('Platform: ' + data.platform.toUpperCase());
        
        if (data.thumbnail) {
            $('#svd-thumbnail').css('background-image', 'url(' + data.thumbnail + ')');
        }
        
        const downloadOptions = $('#svd-download-options');
        downloadOptions.empty();
        
        if (data.download_options && data.download_options.length > 0) {
            data.download_options.forEach(function(option) {
                const card = $('<div class="svd-download-card"></div>');
                
                const info = $('<div class="svd-download-info"></div>');
                info.append('<div class="svd-download-name">' + escapeHtml(option.name) + '</div>');
                info.append('<div class="svd-download-quality">Quality: ' + escapeHtml(option.quality) + '</div>');
                
                const button = $('<a class="svd-button svd-button-secondary" href="' + escapeHtml(option.url) + '" target="_blank" rel="noopener noreferrer">Download ↗</a>');
                
                card.append(info);
                card.append(button);
                downloadOptions.append(card);
            });
        } else {
            downloadOptions.append('<p>No download options available for this video.</p>');
        }
        
        result.fadeIn();
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});

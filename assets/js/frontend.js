/**
 * Cloud Loadout Latency Tester - Frontend JavaScript
 * Handles latency testing functionality for the frontend user interface
 */

(function($) {
    'use strict';
    
    var CloudLoadoutLatencyTester = {
        currentTest: null,
        testResults: [],
        isTesting: false,
        
        // Initialize the tester
        init: function() {
            this.bindEvents();
            this.loadSavedResults();
        },
        
        // Bind event handlers
        bindEvents: function() {
            // Start test button
            $(document).on('click', '.cloudloadout-start-test', this.startTest.bind(this));
            
            // Platform selection
            $(document).on('change', '.cloudloadout-platform-selector', this.onPlatformChange.bind(this));
            
            // Test type selection
            $(document).on('change', '.cloudloadout-test-type', this.onTestTypeChange.bind(this));
            
            // Export results
            $(document).on('click', '.cloudloadout-export-results', this.exportResults.bind(this));
            
            // Clear results
            $(document).on('click', '.cloudloadout-clear-results', this.clearResults.bind(this));
            
            // Retry test
            $(document).on('click', '.cloudloadout-retry-test', this.startTest.bind(this));
            
            // Show help
            $(document).on('click', '.cloudloadout-show-help', this.showHelp.bind(this));
            
            // Advanced settings toggle
            $(document).on('click', '.cloudloadout-advanced-toggle', this.toggleAdvancedSettings.bind(this));
        },
        
        // Handle platform selection change
        onPlatformChange: function(e) {
            var platform = $(e.target).val();
            var servers = this.getPlatformServers(platform);
            
            $('.cloudloadout-servers-list').html('');
            if (servers.length > 0) {
                this.displayServers(servers);
            }
        },
        
        // Handle test type change
        onTestTypeChange: function(e) {
            var testType = $(e.target).val();
            var options = $('.cloudloadout-test-options');
            
            options.hide();
            $('.cloudloadout-' + testType + '-options').show();
        },
        
        // Get servers for a specific platform
        getPlatformServers: function(platform) {
            var servers = {
                'xbox_cloud': [
                    { name: 'Xbox Test Server', url: 'https://test.xbox.com', location: 'US East' },
                    { name: 'Xbox.com', url: 'https://xbox.com', location: 'Global' },
                    { name: 'Game Pass Cloud', url: 'https://www.xbox.com/en-US/xbox-game-pass/cloud-gaming', location: 'US West' }
                ],
                'amazon_luna': [
                    { name: 'Luna Amazon', url: 'https://luna.amazon.com', location: 'US East' },
                    { name: 'Amazon Gaming', url: 'https://www.amazon.com/luna', location: 'US West' }
                ],
                'shadow': [
                    { name: 'Shadow Tech', url: 'https://shadow.tech', location: 'Europe' },
                    { name: 'Shadow Cloud', url: 'https://www.shadow.tech', location: 'US East' }
                ],
                'boosteroid': [
                    { name: 'Boosteroid', url: 'https://boosteroid.com', location: 'Europe' },
                    { name: 'Boosteroid US', url: 'https://www.boosteroid.com', location: 'US West' }
                ],
                'playstation_cloud': [
                    { name: 'PlayStation', url: 'https://playstation.com', location: 'US East' },
                    { name: 'PlayStation Store', url: 'https://store.playstation.com', location: 'US West' }
                ],
                'nvidia_geforce': [
                    { name: 'GeForce NOW', url: 'https://play.geforcenow.com', location: 'US East' },
                    { name: 'NVIDIA Gaming', url: 'https://www.nvidia.com/en-us/geforce-now', location: 'US West' }
                ],
                'microsoft_cloud': [
                    { name: 'Windows Microsoft', url: 'https://windows.microsoft.com', location: 'Global' },
                    { name: 'Azure Windows 365', url: 'https://docs.microsoft.com/en-us/azure/windows-365', location: 'US East' }
                ]
            };
            
            return servers[platform] || [];
        },
        
        // Display server list
        displayServers: function(servers) {
            var html = '';
            servers.forEach(function(server, index) {
                html += '<div class="cloudloadout-server-item" data-index="' + index + '">';
                html += '<label>';
                html += '<input type="radio" name="selected_server" value="' + server.url + '" ' + (index === 0 ? 'checked' : '') + '>';
                html += '<span class="server-name">' + server.name + '</span>';
                html += '<span class="server-location">' + server.location + '</span>';
                html += '<span class="server-url">' + server.url + '</span>';
                html += '</label>';
                html += '</div>';
            });
            $('.cloudloadout-servers-list').html(html);
        },
        
        // Start latency test
        startTest: function(e) {
            e.preventDefault();
            
            if (this.isTesting) {
                return;
            }
            
            var $button = $(e.target);
            var platform = $('.cloudloadout-platform-selector').val();
            var testType = $('.cloudloadout-test-type').val();
            var selectedServer = $('input[name="selected_server"]:checked').val();
            var testCount = parseInt($('.cloudloadout-test-count').val()) || 5;
            var timeout = parseInt($('.cloudloadout-test-timeout').val()) || 5000;
            
            if (!platform) {
                this.showNotification('Please select a platform to test.', 'error');
                return;
            }
            
            if (!selectedServer && testType === 'single') {
                this.showNotification('Please select a server to test.', 'error');
                return;
            }
            
            this.isTesting = true;
            this.updateTestStatus('testing');
            
            var testData = {
                action: 'cloudloadout_run_test',
                nonce: cloudloadout_ajax.nonce,
                platform: platform,
                server_url: selectedServer,
                test_count: testCount,
                timeout: timeout,
                test_type: testType
            };
            
            $.post(cloudloadout_ajax.ajax_url, testData, this.handleTestResponse.bind(this))
                .fail(this.handleTestError.bind(this));
        },
        
        // Handle test response
        handleTestResponse: function(response) {
            this.isTesting = false;
            
            if (response.success) {
                this.testResults = response.data.results;
                this.displayResults(response.data);
                this.saveResults();
                this.updateTestStatus('completed');
            } else {
                this.handleTestError(response);
            }
        },
        
        // Handle test error
        handleTestError: function(response) {
            this.isTesting = false;
            this.updateTestStatus('error');
            
            var errorMsg = 'Test failed. Please try again.';
            if (response.data && response.data.error) {
                errorMsg = response.data.error;
            }
            
            this.showNotification(errorMsg, 'error');
        },
        
        // Display test results
        displayResults: function(data) {
            var resultsHtml = '<div class="cloudloadout-results">';
            resultsHtml += '<h4>' + cloudloadout_ajax.strings.completed + '</h4>';
            
            var latencies = [];
            data.results.forEach(function(result) {
                if (result.success) {
                    latencies.push(result.latency);
                }
            });
            
            if (latencies.length > 0) {
                var average = latencies.reduce(function(a, b) { return a + b; }) / latencies.length;
                var min = Math.min.apply(null, latencies);
                var max = Math.max.apply(null, latencies);
                
                var qualityClass = this.getLatencyQuality(average);
                
                resultsHtml += '<div class="result-summary">';
                resultsHtml += '<div class="average-latency ' + qualityClass + '">';
                resultsHtml += '<span class="label">Average:</span> ';
                resultsHtml += '<span class="value">' + average.toFixed(1) + 'ms</span>';
                resultsHtml += '</div>';
                resultsHtml += '<div class="latency-stats">';
                resultsHtml += '<span class="stat">Min: ' + min.toFixed(1) + 'ms</span>';
                resultsHtml += '<span class="stat">Max: ' + max.toFixed(1) + 'ms</span>';
                resultsHtml += '<span class="stat">Tests: ' + latencies.length + '</span>';
                resultsHtml += '</div>';
                resultsHtml += '</div>';
                
                // Detailed results
                resultsHtml += '<div class="detailed-results">';
                resultsHtml += '<h5>Individual Test Results:</h5>';
                data.results.forEach(function(result, index) {
                    resultsHtml += '<div class="test-result-item ' + (result.success ? 'success' : 'error') + '">';
                    resultsHtml += '<span class="test-number">Test ' + (index + 1) + ':</span> ';
                    if (result.success) {
                        resultsHtml += '<span class="latency-value">' + result.latency.toFixed(1) + 'ms</span>';
                    } else {
                        resultsHtml += '<span class="error-message">Failed - ' + result.error + '</span>';
                    }
                    resultsHtml += '</div>';
                });
                resultsHtml += '</div>';
                
                // Quality assessment
                resultsHtml += '<div class="quality-assessment">';
                resultsHtml += '<h5>Gaming Quality Assessment:</h5>';
                resultsHtml += '<div class="quality-indicator ' + qualityClass + '">';
                resultsHtml += '<span class="quality-label">' + this.getQualityLabel(average) + '</span>';
                resultsHtml += '</div>';
                resultsHtml += '<p class="quality-description">' + this.getQualityDescription(average) + '</p>';
                resultsHtml += '</div>';
                
                // Recommendations
                resultsHtml += '<div class="recommendations">';
                resultsHtml += '<h5>Recommendations:</h5>';
                resultsHtml += '<ul>' + this.getRecommendations(average) + '</ul>';
                resultsHtml += '</div>';
                
            } else {
                resultsHtml += '<div class="test-failed">';
                resultsHtml += '<p>All tests failed. Please check your internet connection and try again.</p>';
                resultsHtml += '</div>';
            }
            
            resultsHtml += '</div>';
            
            $('.cloudloadout-results-container').html(resultsHtml).show();
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $('.cloudloadout-results-container').offset().top - 20
            }, 500);
        },
        
        // Get latency quality class
        getLatencyQuality: function(latency) {
            if (latency <= 50) return 'excellent';
            if (latency <= 100) return 'good';
            if (latency <= 150) return 'fair';
            return 'poor';
        },
        
        // Get quality label
        getQualityLabel: function(latency) {
            if (latency <= 50) return cloudloadout_ajax.strings.excellent;
            if (latency <= 100) return cloudloadout_ajax.strings.good;
            if (latency <= 150) return cloudloadout_ajax.strings.fair;
            return cloudloadout_ajax.strings.poor;
        },
        
        // Get quality description
        getQualityDescription: function(latency) {
            if (latency <= 50) {
                return 'Excellent latency for cloud gaming. You should experience minimal input lag and smooth gameplay.';
            } else if (latency <= 100) {
                return 'Good latency for cloud gaming. Most games should play well with minimal noticeable delay.';
            } else if (latency <= 150) {
                return 'Fair latency. You may notice some input lag in fast-paced games, but casual gaming should be acceptable.';
            } else {
                return 'High latency. Cloud gaming may be challenging with noticeable input lag and potential stuttering.';
            }
        },
        
        // Get recommendations based on latency
        getRecommendations: function(latency) {
            var recommendations = [];
            
            if (latency > 100) {
                recommendations.push('<li>Try using a wired Ethernet connection instead of WiFi</li>');
                recommendations.push('<li>Close bandwidth-intensive applications (streaming, downloads)</li>');
                recommendations.push('<li>Consider upgrading your internet plan for better speeds</li>');
            }
            
            if (latency > 150) {
                recommendations.push('<li>Test your connection at different times of day</li>');
                recommendations.push('<li>Contact your ISP to investigate high latency issues</li>');
                recommendations.push('<li>Consider a gaming VPN service that offers better routing</li>');
            }
            
            if (latency <= 50) {
                recommendations.push('<li>Your connection is excellent for cloud gaming!</li>');
                recommendations.push('<li>Try playing competitive multiplayer games</li>');
            } else {
                recommendations.push('<li>Focus on games that are less latency-sensitive</li>');
                recommendations.push('<li>Avoid real-time competitive games with this connection</li>');
            }
            
            return recommendations.join('');
        },
        
        // Update test status
        updateTestStatus: function(status) {
            var $status = $('.cloudloadout-test-status');
            var statusText = '';
            var statusClass = '';
            
            switch (status) {
                case 'testing':
                    statusText = cloudloadout_ajax.strings.testing;
                    statusClass = 'testing';
                    break;
                case 'completed':
                    statusText = cloudloadout_ajax.strings.completed;
                    statusClass = 'completed';
                    break;
                case 'error':
                    statusText = cloudloadout_ajax.strings.error;
                    statusClass = 'error';
                    break;
            }
            
            $status.removeClass('testing completed error').addClass(statusClass).text(statusText);
        },
        
        // Show notification
        showNotification: function(message, type) {
            var notificationHtml = '<div class="cloudloadout-notification ' + type + '">';
            notificationHtml += '<span class="message">' + message + '</span>';
            notificationHtml += '<button class="close-notification">×</button>';
            notificationHtml += '</div>';
            
            $('.cloudloadout-notifications').append(notificationHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('.cloudloadout-notification').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },
        
        // Show help modal
        showHelp: function(e) {
            e.preventDefault();
            var helpHtml = '<div class="cloudloadout-help-modal">';
            helpHtml += '<div class="help-content">';
            helpHtml += '<h3>How to Use the Latency Tester</h3>';
            helpHtml += '<div class="help-section">';
            helpHtml += '<h4>Understanding Latency</h4>';
            helpHtml += '<p>Latency (ping) measures the time it takes for data to travel from your device to the cloud gaming server and back. Lower latency means better responsiveness.</p>';
            helpHtml += '</div>';
            helpHtml += '<div class="help-section">';
            helpHtml += '<h4>Latency Guidelines</h4>';
            helpHtml += '<ul>';
            helpHtml += '<li><strong>0-50ms:</strong> Excellent for all types of cloud gaming</li>';
            helpHtml += '<li><strong>51-100ms:</strong> Good for most cloud gaming experiences</li>';
            helpHtml += '<li><strong>101-150ms:</strong> Fair, may notice some input lag</li>';
            helpHtml += '<li><strong>150ms+:</strong> Poor, gaming may be difficult</li>';
            helpHtml += '</ul>';
            helpHtml += '</div>';
            helpHtml += '<div class="help-section">';
            helpHtml += '<h4>Tips for Better Results</h4>';
            helpHtml += '<ul>';
            helpHtml += '<li>Use a wired Ethernet connection when possible</li>';
            helpHtml += '<li>Close other applications using internet bandwidth</li>';
            helpHtml += '<li>Run tests at different times of day</li>';
            helpHtml += '<li>Try multiple servers to find the best one</li>';
            helpHtml += '</ul>';
            helpHtml += '</div>';
            helpHtml += '<button class="close-help">Close</button>';
            helpHtml += '</div>';
            helpHtml += '</div>';
            
            $('body').append(helpHtml);
            $('.cloudloadout-help-modal').show();
        },
        
        // Toggle advanced settings
        toggleAdvancedSettings: function(e) {
            e.preventDefault();
            $('.cloudloadout-advanced-settings').toggle();
        },
        
        // Export results
        exportResults: function() {
            if (this.testResults.length === 0) {
                this.showNotification('No results to export', 'error');
                return;
            }
            
            var csv = 'Test Results\n';
            csv += 'Date,Platform,Server,Latency (ms),Status\n';
            
            this.testResults.forEach(function(result) {
                csv += new Date().toLocaleDateString() + ',' + 
                      $('.cloudloadout-platform-selector option:selected').text() + ',' +
                      (result.server || 'Unknown') + ',' +
                      (result.latency || 0) + ',' +
                      (result.success ? 'Success' : 'Failed') + '\n';
            });
            
            var blob = new Blob([csv], { type: 'text/csv' });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'cloudloadout-latency-results.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        },
        
        // Clear results
        clearResults: function() {
            this.testResults = [];
            $('.cloudloadout-results-container').hide();
            $('.cloudloadout-test-status').removeClass('testing completed error').text('');
        },
        
        // Save results to localStorage
        saveResults: function() {
            if (typeof(Storage) !== 'undefined') {
                localStorage.setItem('cloudloadout_latency_results', JSON.stringify({
                    results: this.testResults,
                    timestamp: new Date().getTime()
                }));
            }
        },
        
        // Load saved results from localStorage
        loadSavedResults: function() {
            if (typeof(Storage) !== 'undefined') {
                var saved = localStorage.getItem('cloudloadout_latency_results');
                if (saved) {
                    try {
                        var data = JSON.parse(saved);
                        // Only load results from the last 24 hours
                        if (new Date().getTime() - data.timestamp < 24 * 60 * 60 * 1000) {
                            this.testResults = data.results || [];
                        } else {
                            localStorage.removeItem('cloudloadout_latency_results');
                        }
                    } catch (e) {
                        localStorage.removeItem('cloudloadout_latency_results');
                    }
                }
            }
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        CloudLoadoutLatencyTester.init();
        
        // Close notifications
        $(document).on('click', '.close-notification', function() {
            $(this).parent().fadeOut(function() {
                $(this).remove();
            });
        });
        
        // Close help modal
        $(document).on('click', '.close-help, .cloudloadout-help-modal', function(e) {
            if (e.target === this) {
                $('.cloudloadout-help-modal').remove();
            }
        });
        
        // Handle Enter key in forms
        $(document).on('keypress', '.cloudloadout-test-form input', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('.cloudloadout-start-test').click();
            }
        });
    });
    
    // Make CloudLoadoutLatencyTester globally available
    window.CloudLoadoutLatencyTester = CloudLoadoutLatencyTester;
    
})(jQuery);
/**
 * Cloud Loadout Latency Tester - Admin JavaScript
 * Handles admin functionality for the WordPress plugin
 */

(function($) {
    'use strict';
    
    var CloudLoadoutAdmin = {
        
        init: function() {
            this.bindEvents();
            this.initializeCharts();
            this.loadAdminSettings();
        },
        
        // Bind event handlers
        bindEvents: function() {
            // Theme toggle
            $(document).on('click', '.admin-theme-toggle', this.toggleTheme.bind(this));
            
            // Export functionality
            $(document).on('click', '#export-admin-settings', this.exportSettings.bind(this));
            $(document).on('click', '#export-test-results', this.exportTestResults.bind(this));
            
            // Bulk operations
            $(document).on('click', '#bulk-delete-tests', this.bulkDeleteTests.bind(this));
            $(document).on('click', '#clear-all-tests', this.clearAllTests.bind(this));
            
            // Test operations
            $(document).on('click', '.run-test', this.runSingleTest.bind(this));
            $(document).on('click', '.test-all-platforms', this.testAllPlatforms.bind(this));
            
            // Platform management
            $(document).on('click', '.add-platform', this.addPlatform.bind(this));
            $(document).on('click', '.remove-platform', this.removePlatform.bind(this));
            $(document).on('click', '.toggle-platform', this.togglePlatform.bind(this));
            
            // Settings management
            $(document).on('click', '#reset-settings', this.resetSettings.bind(this));
            $(document).on('click', '#save-settings', this.saveSettings.bind(this));
            
            // Real-time updates
            $(document).on('click', '#enable-realtime', this.toggleRealtimeUpdates.bind(this));
            
            // Analytics
            $(document).on('click', '#refresh-analytics', this.refreshAnalytics.bind(this));
            $(document).on('change', '.analytics-filter', this.filterAnalytics.bind(this));
        },
        
        // Toggle admin theme
        toggleTheme: function(e) {
            e.preventDefault();
            
            var currentTheme = $('body').hasClass('dark-theme') ? 'dark' : 'light';
            var newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_update_admin_theme',
                theme: newTheme,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    $('body').removeClass(currentTheme + '-theme').addClass(newTheme + '-theme');
                    $('.admin-theme-toggle').text(newTheme === 'dark' ? 'Light Theme' : 'Dark Theme');
                }
            });
        },
        
        // Export plugin settings
        exportSettings: function() {
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_export_settings',
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    var settings = JSON.stringify(response.data.settings, null, 2);
                    var blob = new Blob([settings], { type: 'application/json' });
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'cloudloadout-settings.json';
                    a.click();
                    URL.revokeObjectURL(url);
                    
                    CloudLoadoutAdmin.showNotification('Settings exported successfully!', 'success');
                } else {
                    CloudLoadoutAdmin.showNotification('Failed to export settings', 'error');
                }
            });
        },
        
        // Export test results
        exportTestResults: function() {
            var filters = {
                platform: $('.analytics-filter').val() || '',
                date_from: $('.date-from').val() || '',
                date_to: $('.date-to').val() || ''
            };
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_export_test_results',
                filters: filters,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    var csv = CloudLoadoutAdmin.convertToCSV(response.data.results);
                    var blob = new Blob([csv], { type: 'text/csv' });
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'cloudloadout-test-results.csv';
                    a.click();
                    URL.revokeObjectURL(url);
                    
                    CloudLoadoutAdmin.showNotification('Test results exported successfully!', 'success');
                } else {
                    CloudLoadoutAdmin.showNotification('Failed to export test results', 'error');
                }
            });
        },
        
        // Convert data to CSV format
        convertToCSV: function(data) {
            if (!data.length) return '';
            
            var csv = 'Date,Platform,Average Latency,Min Latency,Max Latency,Tests,Success Rate,User IP,Location\n';
            
            data.forEach(function(row) {
                csv += '"' + row.test_date + '","' + 
                      (row.platform || 'Unknown') + '","' + 
                      (row.average_latency || 0) + '","' + 
                      (row.min_latency || 0) + '","' + 
                      (row.max_latency || 0) + '","' + 
                      (row.test_count || 0) + '","' + 
                      (row.success_rate || 0) + '","' + 
                      (row.user_ip || 'Unknown') + '","' + 
                      (row.server_location || 'Unknown') + '"\n';
            });
            
            return csv;
        },
        
        // Bulk delete tests
        bulkDeleteTests: function() {
            var selectedTests = $('.test-checkbox:checked');
            
            if (!selectedTests.length) {
                CloudLoadoutAdmin.showNotification('Please select tests to delete', 'warning');
                return;
            }
            
            if (!confirm('Are you sure you want to delete ' + selectedTests.length + ' test(s)? This cannot be undone.')) {
                return;
            }
            
            var testIds = selectedTests.map(function() {
                return $(this).val();
            }).get();
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_bulk_delete_tests',
                test_ids: testIds,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    selectedTests.closest('tr').fadeOut(function() {
                        $(this).remove();
                    });
                    CloudLoadoutAdmin.showNotification('Tests deleted successfully', 'success');
                    CloudLoadoutAdmin.updateStatistics();
                } else {
                    CloudLoadoutAdmin.showNotification('Failed to delete tests', 'error');
                }
            });
        },
        
        // Clear all tests
        clearAllTests: function() {
            if (!confirm('Are you sure you want to delete ALL test results? This cannot be undone!')) {
                return;
            }
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_clear_all_tests',
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    $('.test-results-table tbody').empty();
                    CloudLoadoutAdmin.showNotification('All test results cleared', 'success');
                    CloudLoadoutAdmin.updateStatistics();
                } else {
                    CloudLoadoutAdmin.showNotification('Failed to clear test results', 'error');
                }
            });
        },
        
        // Run single test
        runSingleTest: function(e) {
            e.preventDefault();
            
            var $button = $(e.target);
            var platform = $button.data('platform');
            var originalText = $button.text();
            
            $button.text('Testing...').prop('disabled', true);
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_run_single_test',
                platform: platform,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    CloudLoadoutAdmin.showNotification('Test completed for ' + platform, 'success');
                    // Update test results table
                    CloudLoadoutAdmin.refreshTestResults();
                } else {
                    CloudLoadoutAdmin.showNotification('Test failed: ' + (response.data || 'Unknown error'), 'error');
                }
            }).always(function() {
                $button.text(originalText).prop('disabled', false);
            });
        },
        
        // Test all platforms
        testAllPlatforms: function(e) {
            e.preventDefault();
            
            var $button = $(e.target);
            $button.text('Testing All Platforms...').prop('disabled', true);
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_test_all_platforms',
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    CloudLoadoutAdmin.showNotification('All platform tests completed', 'success');
                    CloudLoadoutAdmin.refreshTestResults();
                    CloudLoadoutAdmin.updateStatistics();
                } else {
                    CloudLoadoutAdmin.showNotification('Some tests failed', 'warning');
                }
            }).always(function() {
                $button.text('Test All Platforms').prop('disabled', false);
            });
        },
        
        // Add new platform
        addPlatform: function(e) {
            e.preventDefault();
            
            var name = prompt('Enter platform name:');
            if (!name) return;
            
            var servers = prompt('Enter server URLs (one per line):');
            if (!servers) return;
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_add_platform',
                name: name,
                servers: servers,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    CloudLoadoutAdmin.showNotification('Platform added successfully', 'success');
                    location.reload();
                } else {
                    CloudLoadoutAdmin.showNotification('Failed to add platform', 'error');
                }
            });
        },
        
        // Remove platform
        removePlatform: function(e) {
            e.preventDefault();
            
            var platform = $(e.target).data('platform');
            
            if (!confirm('Are you sure you want to remove ' + platform + '?')) {
                return;
            }
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_remove_platform',
                platform: platform,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    CloudLoadoutAdmin.showNotification('Platform removed successfully', 'success');
                    location.reload();
                } else {
                    CloudLoadoutAdmin.showNotification('Failed to remove platform', 'error');
                }
            });
        },
        
        // Toggle platform enabled/disabled
        togglePlatform: function(e) {
            e.preventDefault();
            
            var $checkbox = $(e.target);
            var platform = $checkbox.data('platform');
            var enabled = $checkbox.is(':checked');
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_toggle_platform',
                platform: platform,
                enabled: enabled,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    var status = enabled ? 'enabled' : 'disabled';
                    CloudLoadoutAdmin.showNotification('Platform ' + status + ' successfully', 'success');
                } else {
                    $checkbox.prop('checked', !enabled); // Revert
                    CloudLoadoutAdmin.showNotification('Failed to update platform', 'error');
                }
            });
        },
        
        // Reset settings to defaults
        resetSettings: function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to reset all settings to defaults? This cannot be undone!')) {
                return;
            }
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_reset_settings',
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    CloudLoadoutAdmin.showNotification('Settings reset to defaults', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    CloudLoadoutAdmin.showNotification('Failed to reset settings', 'error');
                }
            });
        },
        
        // Save settings
        saveSettings: function(e) {
            e.preventDefault();
            
            var settings = {
                test_timeout: parseInt($('[name="test_timeout"]').val()),
                test_count: parseInt($('[name="test_count"]').val()),
                enable_detailed_logs: $('[name="enable_detailed_logs"]').is(':checked'),
                show_results_chart: $('[name="show_results_chart"]').is(':checked'),
                enable_analytics: $('[name="enable_analytics"]').is(':checked'),
                admin_theme: $('[name="admin_theme"]').val()
            };
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_save_settings',
                settings: settings,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    CloudLoadoutAdmin.showNotification('Settings saved successfully!', 'success');
                } else {
                    CloudLoadoutAdmin.showNotification('Failed to save settings', 'error');
                }
            });
        },
        
        // Toggle real-time updates
        toggleRealtimeUpdates: function(e) {
            var enabled = $(e.target).is(':checked');
            
            if (enabled) {
                CloudLoadoutAdmin.startRealtimeUpdates();
            } else {
                CloudLoadoutAdmin.stopRealtimeUpdates();
            }
        },
        
        // Start real-time updates
        startRealtimeUpdates: function() {
            CloudLoadoutAdmin.realtimeInterval = setInterval(function() {
                CloudLoadoutAdmin.refreshAnalytics();
                CloudLoadoutAdmin.updateStatistics();
            }, 30000); // Update every 30 seconds
        },
        
        // Stop real-time updates
        stopRealtimeUpdates: function() {
            if (CloudLoadoutAdmin.realtimeInterval) {
                clearInterval(CloudLoadoutAdmin.realtimeInterval);
                CloudLoadoutAdmin.realtimeInterval = null;
            }
        },
        
        // Refresh analytics
        refreshAnalytics: function() {
            var filters = {
                platform: $('.analytics-filter').val() || '',
                date_from: $('.date-from').val() || '',
                date_to: $('.date-to').val() || ''
            };
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_get_analytics',
                filters: filters,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    CloudLoadoutAdmin.updateAnalytics(response.data);
                }
            });
        },
        
        // Filter analytics
        filterAnalytics: function(e) {
            // Debounce the filter to avoid too many requests
            clearTimeout(CloudLoadoutAdmin.filterTimeout);
            CloudLoadoutAdmin.filterTimeout = setTimeout(function() {
                CloudLoadoutAdmin.refreshAnalytics();
            }, 500);
        },
        
        // Initialize charts
        initializeCharts: function() {
            // Performance trends chart
            if ($('#performance-chart').length) {
                CloudLoadoutAdmin.initPerformanceChart();
            }
            
            // Platform comparison chart
            if ($('#platform-chart').length) {
                CloudLoadoutAdmin.initPlatformChart();
            }
            
            // Real-time status chart
            if ($('#realtime-chart').length) {
                CloudLoadoutAdmin.initRealtimeChart();
            }
        },
        
        // Initialize performance chart
        initPerformanceChart: function() {
            var ctx = document.getElementById('performance-chart').getContext('2d');
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_get_performance_data',
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    var data = response.data;
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Average Latency (ms)',
                                data: data.latency,
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                borderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Latency (ms)'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        },
        
        // Initialize platform comparison chart
        initPlatformChart: function() {
            var ctx = document.getElementById('platform-chart').getContext('2d');
            
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_get_platform_data',
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    var data = response.data;
                    
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Average Latency (ms)',
                                data: data.latency,
                                backgroundColor: [
                                    '#667eea', '#764ba2', '#f093fb', '#f5576c',
                                    '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Latency (ms)'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        },
        
        // Initialize real-time chart
        initRealtimeChart: function() {
            var ctx = document.getElementById('realtime-chart').getContext('2d');
            
            CloudLoadoutAdmin.realtimeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Current Latency (ms)',
                        data: [],
                        borderColor: '#27ae60',
                        backgroundColor: 'rgba(39, 174, 96, 0.1)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Latency (ms)'
                            }
                        }
                    }
                }
            });
            
            CloudLoadoutAdmin.startRealtimeUpdates();
        },
        
        // Update real-time chart
        updateRealtimeChart: function(latency) {
            if (!CloudLoadoutAdmin.realtimeChart) return;
            
            var chart = CloudLoadoutAdmin.realtimeChart;
            var now = new Date().toLocaleTimeString();
            
            chart.data.labels.push(now);
            chart.data.datasets[0].data.push(latency);
            
            // Keep only last 20 data points
            if (chart.data.labels.length > 20) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
            }
            
            chart.update('none');
        },
        
        // Update analytics display
        updateAnalytics: function(data) {
            // Update summary cards
            $('.total-tests .stat-number').text(data.total_tests || 0);
            $('.avg-latency .stat-number').text((data.avg_latency || 0).toFixed(1) + 'ms');
            $('.tests-today .stat-number').text(data.tests_today || 0);
            $('.success-rate .stat-number').text((data.success_rate || 0).toFixed(1) + '%');
            
            // Update platform performance table
            if (data.platform_performance) {
                var html = '';
                data.platform_performance.forEach(function(platform) {
                    html += '<tr>';
                    html += '<td>' + platform.name + '</td>';
                    html += '<td>' + platform.tests + '</td>';
                    html += '<td>' + platform.avg_latency.toFixed(1) + 'ms</td>';
                    html += '<td>' + platform.min_latency.toFixed(1) + 'ms</td>';
                    html += '<td>' + platform.max_latency.toFixed(1) + 'ms</td>';
                    html += '<td>' + platform.success_rate.toFixed(1) + '%</td>';
                    html += '</tr>';
                });
                $('.platform-performance-table tbody').html(html);
            }
        },
        
        // Update statistics
        updateStatistics: function() {
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_get_statistics',
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    $('.total-tests .stat-number').text(response.data.total_tests || 0);
                    $('.avg-latency .stat-number').text((response.data.avg_latency || 0).toFixed(1) + 'ms');
                    $('.tests-today .stat-number').text(response.data.tests_today || 0);
                }
            });
        },
        
        // Refresh test results table
        refreshTestResults: function() {
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_get_recent_tests',
                limit: 10,
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    var html = '';
                    response.data.forEach(function(test) {
                        html += '<tr>';
                        html += '<td>' + test.test_date + '</td>';
                        html += '<td>' + test.platform + '</td>';
                        html += '<td>' + (test.average_latency || 0).toFixed(1) + 'ms</td>';
                        html += '<td>' + test.server_location + '</td>';
                        html += '</tr>';
                    });
                    $('.recent-tests-table tbody').html(html);
                }
            });
        },
        
        // Load admin settings
        loadAdminSettings: function() {
            // Load theme preference
            var savedTheme = localStorage.getItem('cloudloadout_admin_theme') || 'light';
            if (savedTheme !== $('body').attr('class').includes('dark') ? 'dark' : 'light') {
                $('body').removeClass('light-theme dark-theme').addClass(savedTheme + '-theme');
            }
            
            // Load other preferences
            var autoRefresh = localStorage.getItem('cloudloadout_auto_refresh') === 'true';
            $('#enable-realtime').prop('checked', autoRefresh);
            
            if (autoRefresh) {
                CloudLoadoutAdmin.startRealtimeUpdates();
            }
        },
        
        // Show notification
        showNotification: function(message, type) {
            type = type || 'info';
            
            var notificationHtml = '<div class="cloudloadout-admin-notification ' + type + '">';
            notificationHtml += '<span class="message">' + message + '</span>';
            notificationHtml += '<button class="close-notification">×</button>';
            notificationHtml += '</div>';
            
            $('.cloudloadout-admin-notifications').append(notificationHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('.cloudloadout-admin-notification').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },
        
        // Utility function for debouncing
        debounce: function(func, wait) {
            var timeout;
            return function executedFunction() {
                var context = this;
                var args = arguments;
                var later = function() {
                    timeout = null;
                    func.apply(context, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };
    
    // Initialize admin functionality when document is ready
    $(document).ready(function() {
        CloudLoadoutAdmin.init();
        
        // Close notifications
        $(document).on('click', '.close-notification', function() {
            $(this).parent().fadeOut(function() {
                $(this).remove();
            });
        });
        
        // Save preferences
        $(document).on('change', '#enable-realtime', function() {
            localStorage.setItem('cloudloadout_auto_refresh', $(this).is(':checked'));
        });
        
        // Handle keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + S to save settings
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
                e.preventDefault();
                $('#save-settings').click();
            }
            
            // Ctrl/Cmd + E to export results
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 69) {
                e.preventDefault();
                $('#export-test-results').click();
            }
        });
    });
    
    // Make CloudLoadoutAdmin globally available
    window.CloudLoadoutAdmin = CloudLoadoutAdmin;
    
})(jQuery);
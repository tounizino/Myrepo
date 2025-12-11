<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('cloudloadout_latency_options', array());
global $wpdb;
$table_name = $wpdb->prefix . 'cloudloadout_latency_tests';

// Handle bulk actions
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['test_ids'])) {
    check_admin_referer('cloudloadout_bulk_action');
    $test_ids = array_map('intval', $_POST['test_ids']);
    $ids_string = implode(',', $test_ids);
    $wpdb->query("DELETE FROM $table_name WHERE id IN ($ids_string)");
    echo '<div class="notice notice-success"><p>' . __('Selected test results deleted.', 'cloud-loadout-latency-tester') . '</p></div>';
}

// Handle export
if (isset($_POST['export_csv'])) {
    check_admin_referer('cloudloadout_export_csv');
    $this->export_test_results();
}

// Pagination
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Filter by platform
$platform_filter = isset($_GET['platform']) ? sanitize_text_field($_GET['platform']) : '';
$where_clause = '';
if (!empty($platform_filter)) {
    $where_clause = $wpdb->prepare(" WHERE platform = %s", $platform_filter);
}

// Get total count
$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where_clause");
$total_pages = ceil($total_items / $per_page);

// Get test results
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_name $where_clause ORDER BY test_date DESC LIMIT %d OFFSET %d",
    $per_page,
    $offset
), ARRAY_A);

// Get platform statistics
$platform_stats = $wpdb->get_results(
    "SELECT platform, COUNT(*) as test_count, AVG(average_latency) as avg_latency 
     FROM $table_name 
     WHERE average_latency > 0 
     GROUP BY platform 
     ORDER BY avg_latency ASC",
    ARRAY_A
);
?>
<div class="wrap cloudloadout-admin results <?php echo esc_attr($options['admin_theme'] ?? 'light'); ?>-theme">
    <div class="cloudloadout-header">
        <h1><?php _e('Test Results', 'cloud-loadout-latency-tester'); ?></h1>
        <p class="subtitle"><?php _e('View and analyze latency test results', 'cloud-loadout-latency-tester'); ?></p>
    </div>
    
    <!-- Statistics Overview -->
    <div class="cloudloadout-dashboard-grid">
        <div class="cloudloadout-card">
            <h3><?php _e('Platform Performance Summary', 'cloud-loadout-latency-tester'); ?></h3>
            <div class="stats-container">
                <?php if ($platform_stats): ?>
                    <div class="platform-stats">
                        <?php foreach ($platform_stats as $stat): ?>
                        <div class="platform-stat-item">
                            <div class="stat-header">
                                <span class="platform-name"><?php echo esc_html($stat['platform']); ?></span>
                                <span class="test-count"><?php echo intval($stat['test_count']); ?> tests</span>
                            </div>
                            <div class="stat-metrics">
                                <div class="latency-bar">
                                    <div class="latency-fill" style="width: <?php echo min(100, ($stat['avg_latency'] / 200) * 100); ?>%"></div>
                                </div>
                                <span class="avg-latency"><?php echo round($stat['avg_latency'], 1); ?>ms avg</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p><?php _e('No test data available yet.', 'cloud-loadout-latency-tester'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Filters and Actions -->
    <div class="cloudloadout-card">
        <h3><?php _e('Filters & Actions', 'cloud-loadout-latency-tester'); ?></h3>
        <div class="filters-row">
            <form method="get" action="" class="filter-form">
                <input type="hidden" name="page" value="cloudloadout-latency-tester-results" />
                
                <label for="platform-filter"><?php _e('Filter by Platform:', 'cloud-loadout-latency-tester'); ?></label>
                <select name="platform" id="platform-filter">
                    <option value=""><?php _e('All Platforms', 'cloud-loadout-latency-tester'); ?></option>
                    <?php foreach ($options['supported_platforms'] as $key => $platform): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($platform_filter, $key); ?>>
                            <?php echo esc_html($platform['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="submit" class="button" value="<?php _e('Apply Filter', 'cloud-loadout-latency-tester'); ?>" />
                
                <?php if (!empty($platform_filter)): ?>
                    <a href="<?php echo admin_url('admin.php?page=cloudloadout-latency-tester-results'); ?>" class="button">
                        <?php _e('Clear Filter', 'cloud-loadout-latency-tester'); ?>
                    </a>
                <?php endif; ?>
            </form>
            
            <div class="action-buttons">
                <form method="post" style="display: inline;">
                    <?php wp_nonce_field('cloudloadout_export_csv'); ?>
                    <input type="submit" name="export_csv" class="button button-secondary" value="<?php _e('Export CSV', 'cloud-loadout-latency-tester'); ?>" />
                </form>
                
                <button type="button" id="bulk-delete" class="button button-secondary" disabled>
                    <?php _e('Delete Selected', 'cloud-loadout-latency-tester'); ?>
                </button>
                
                <button type="button" id="clear-all-results" class="button button-secondary">
                    <?php _e('Clear All Results', 'cloud-loadout-latency-tester'); ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Test Results Table -->
    <div class="cloudloadout-card">
        <h3><?php _e('Test History', 'cloud-loadout-latency-tester'); ?></h3>
        
        <form method="post" id="results-form">
            <?php wp_nonce_field('cloudloadout_bulk_action'); ?>
            
            <div class="tablenav top">
                <div class="alignleft actions">
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1"><?php _e('Bulk Actions', 'cloud-loadout-latency-tester'); ?></option>
                        <option value="delete"><?php _e('Delete', 'cloud-loadout-latency-tester'); ?></option>
                    </select>
                    <input type="submit" class="button action" value="<?php _e('Apply', 'cloud-loadout-latency-tester'); ?>" />
                </div>
                
                <div class="tablenav-pages">
                    <?php if ($total_pages > 1): ?>
                        <?php
                        $page_links = paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => __('«'),
                            'next_text' => __('»'),
                            'total' => $total_pages,
                            'current' => $current_page
                        ));
                        echo $page_links;
                        ?>
                    <?php endif; ?>
                    
                    <span class="displaying-num">
                        <?php printf(__('Showing %s-%s of %s', 'cloud-loadout-latency-tester'), $offset + 1, min($offset + $per_page, $total_items), $total_items); ?>
                    </span>
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" id="cb-select-all" />
                        </td>
                        <th class="manage-column"><?php _e('Date & Time', 'cloud-loadout-latency-tester'); ?></th>
                        <th class="manage-column"><?php _e('Platform', 'cloud-loadout-latency-tester'); ?></th>
                        <th class="manage-column"><?php _e('Average Latency', 'cloud-loadout-latency-tester'); ?></th>
                        <th class="manage-column"><?php _e('Location', 'cloud-loadout-latency-tester'); ?></th>
                        <th class="manage-column"><?php _e('User IP', 'cloud-loadout-latency-tester'); ?></th>
                        <th class="manage-column"><?php _e('Actions', 'cloud-loadout-latency-tester'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results): ?>
                        <?php foreach ($results as $result): ?>
                        <tr>
                            <th class="check-column">
                                <input type="checkbox" name="test_ids[]" value="<?php echo intval($result['id']); ?>" />
                            </th>
                            <td><?php echo date('M j, Y H:i:s', strtotime($result['test_date'])); ?></td>
                            <td>
                                <strong><?php echo esc_html($result['platform']); ?></strong>
                            </td>
                            <td>
                                <?php if ($result['average_latency'] > 0): ?>
                                    <span class="latency-value <?php echo cloudloadout_get_latency_class($result['average_latency']); ?>">
                                        <?php echo round($result['average_latency'], 1); ?>ms
                                    </span>
                                <?php else: ?>
                                    <span class="latency-error"><?php _e('Failed', 'cloud-loadout-latency-tester'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($result['server_location']); ?></td>
                            <td><?php echo esc_html($result['user_ip']); ?></td>
                            <td>
                                <button type="button" class="button button-small view-details" data-id="<?php echo intval($result['id']); ?>">
                                    <?php _e('View Details', 'cloud-loadout-latency-tester'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <p><?php _e('No test results found.', 'cloud-loadout-latency-tester'); ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="tablenav bottom">
                <div class="alignleft actions">
                    <select name="action" id="bulk-action-selector-bottom">
                        <option value="-1"><?php _e('Bulk Actions', 'cloud-loadout-latency-tester'); ?></option>
                        <option value="delete"><?php _e('Delete', 'cloud-loadout-latency-tester'); ?></option>
                    </select>
                    <input type="submit" class="button action" value="<?php _e('Apply', 'cloud-loadout-latency-tester'); ?>" />
                </div>
                
                <div class="tablenav-pages">
                    <?php if ($total_pages > 1): ?>
                        <?php echo $page_links; ?>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Test Details Modal -->
<div id="test-details-modal" class="cloudloadout-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php _e('Test Details', 'cloud-loadout-latency-tester'); ?></h3>
            <button type="button" class="modal-close">×</button>
        </div>
        <div class="modal-body" id="test-details-content">
            <!-- Content will be loaded via AJAX -->
        </div>
    </div>
</div>

<style>
.cloudloadout-dashboard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.stats-container {
    margin-top: 15px;
}

.platform-stats {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.platform-stat-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.dark-theme .platform-stat-item {
    background: #34495e;
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.platform-name {
    font-weight: 600;
    font-size: 1.1em;
}

.test-count {
    font-size: 0.9em;
    color: #666;
}

.dark-theme .test-count {
    color: #bdc3c7;
}

.stat-metrics {
    display: flex;
    align-items: center;
    gap: 15px;
}

.latency-bar {
    flex: 1;
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}

.latency-fill {
    height: 100%;
    background: linear-gradient(90deg, #27ae60, #f39c12, #e74c3c);
    transition: width 0.3s ease;
}

.avg-latency {
    font-weight: 600;
    min-width: 80px;
}

.filters-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.filter-form {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.latency-value.excellent { color: #27ae60; font-weight: bold; }
.latency-value.good { color: #f39c12; font-weight: bold; }
.latency-value.fair { color: #e67e22; font-weight: bold; }
.latency-value.poor { color: #e74c3c; font-weight: bold; }
.latency-error { color: #e74c3c; font-weight: bold; }

/* Modal Styles */
.cloudloadout-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 10px;
    max-width: 600px;
    max-height: 80%;
    width: 90%;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.dark-theme .modal-content {
    background: #2c3e50;
    color: #ecf0f1;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #ddd;
}

.dark-theme .modal-header {
    border-bottom-color: #34495e;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.modal-body {
    padding: 20px;
}

@media (max-width: 768px) {
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-form, .action-buttons {
        width: 100%;
    }
    
    .filter-form {
        justify-content: center;
    }
}
</style>

<?php
// Helper function to get latency class
function cloudloadout_get_latency_class($latency) {
    if ($latency <= 50) return 'excellent';
    if ($latency <= 100) return 'good';
    if ($latency <= 150) return 'fair';
    return 'poor';
}
?>

<script>
jQuery(document).ready(function($) {
    // Select all checkbox
    $('#cb-select-all').on('change', function() {
        $('input[name="test_ids[]"]').prop('checked', this.checked);
        updateBulkDeleteButton();
    });
    
    // Individual checkboxes
    $('input[name="test_ids[]"]').on('change', function() {
        updateBulkDeleteButton();
    });
    
    // Update bulk delete button state
    function updateBulkDeleteButton() {
        var checkedCount = $('input[name="test_ids[]"]:checked').length;
        $('#bulk-delete').prop('disabled', checkedCount === 0);
    }
    
    // Bulk delete
    $('#bulk-delete').on('click', function() {
        if (confirm('<?php _e('Are you sure you want to delete the selected test results?', 'cloud-loadout-latency-tester'); ?>')) {
            $('select[name="action"]').val('delete');
            $('#results-form').submit();
        }
    });
    
    // Clear all results
    $('#clear-all-results').on('click', function() {
        if (confirm('<?php _e('Are you sure you want to delete ALL test results? This cannot be undone!', 'cloud-loadout-latency-tester'); ?>')) {
            // Implement clear all functionality
            $.post(cloudloadout_admin.ajax_url, {
                action: 'cloudloadout_clear_all_results',
                nonce: cloudloadout_admin.nonce
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('<?php _e('Failed to clear results.', 'cloud-loadout-latency-tester'); ?>');
                }
            });
        }
    });
    
    // View test details
    $('.view-details').on('click', function() {
        var testId = $(this).data('id');
        
        $.post(cloudloadout_admin.ajax_url, {
            action: 'cloudloadout_get_test_details',
            test_id: testId,
            nonce: cloudloadout_admin.nonce
        }, function(response) {
            if (response.success) {
                $('#test-details-content').html(response.data.html);
                $('#test-details-modal').show();
            } else {
                alert('<?php _e('Failed to load test details.', 'cloud-loadout-latency-tester'); ?>');
            }
        });
    });
    
    // Close modal
    $('.modal-close, .cloudloadout-modal').on('click', function(e) {
        if (e.target === this) {
            $('#test-details-modal').hide();
        }
    });
    
    // Escape key to close modal
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) {
            $('#test-details-modal').hide();
        }
    });
});
</script>
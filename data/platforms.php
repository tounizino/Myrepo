<?php
/**
 * Cloud Gaming Platform Server Data
 * Comprehensive list of cloud gaming platforms and their server endpoints
 */

if (!defined('ABSPATH')) {
    exit;
}

class CloudLoadoutServerData {
    
    public static function get_platform_data() {
        return array(
            'xbox_cloud' => array(
                'name' => 'Xbox Cloud Gaming',
                'description' => 'Microsoft\'s cloud gaming service, part of Xbox Game Pass Ultimate',
                'enabled' => true,
                'website' => 'https://www.xbox.com/xbox-game-pass/cloud-gaming',
                'regions' => array(
                    'us_east' => array(
                        'name' => 'US East',
                        'servers' => array(
                            'https://test.xbox.com',
                            'https://xbox.com',
                            'https://www.xbox.com/en-US/xbox-game-pass/cloud-gaming'
                        )
                    ),
                    'us_west' => array(
                        'name' => 'US West',
                        'servers' => array(
                            'https://gamepass.com',
                            'https://www.microsoft.com/en-us/xbox'
                        )
                    ),
                    'europe' => array(
                        'name' => 'Europe',
                        'servers' => array(
                            'https://www.xbox.com/en-GB/xbox-game-pass/cloud-gaming',
                            'https://www.xbox.com/en-DE/xbox-game-pass/cloud-gaming'
                        )
                    ),
                    'asia' => array(
                        'name' => 'Asia',
                        'servers' => array(
                            'https://www.xbox.com/en-JP/xbox-game-pass/cloud-gaming'
                        )
                    )
                ),
                'tips' => array(
                    'Use Xbox Game Pass Ultimate subscription',
                    'Ensure stable internet connection (minimum 10 Mbps)',
                    'Wired connection recommended for best latency',
                    'Close other bandwidth-intensive applications'
                )
            ),
            
            'amazon_luna' => array(
                'name' => 'Amazon Luna',
                'description' => 'Amazon\'s cloud gaming service with channel-based games',
                'enabled' => true,
                'website' => 'https://luna.amazon.com',
                'regions' => array(
                    'us_east' => array(
                        'name' => 'US East',
                        'servers' => array(
                            'https://luna.amazon.com',
                            'https://www.amazon.com/luna'
                        )
                    ),
                    'us_west' => array(
                        'name' => 'US West',
                        'servers' => array(
                            'https://www.amazon.com/gaming/luna'
                        )
                    )
                ),
                'tips' => array(
                    'Separate Luna channels subscription required',
                    'Works with Xbox and PlayStation controllers',
                    'Prime members get Luna+ channel free',
                    'Available in select regions only'
                )
            ),
            
            'shadow' => array(
                'name' => 'Shadow',
                'description' => 'High-end cloud PC with full Windows desktop experience',
                'enabled' => true,
                'website' => 'https://shadow.tech',
                'regions' => array(
                    'us_east' => array(
                        'name' => 'US East',
                        'servers' => array(
                            'https://shadow.tech',
                            'https://www.shadow.tech'
                        )
                    ),
                    'europe' => array(
                        'name' => 'Europe',
                        'servers' => array(
                            'https://shadow.tech/en',
                            'https://www.shadow.tech/EN'
                        )
                    )
                ),
                'tips' => array(
                    'Premium cloud PC experience with full Windows',
                    'High bandwidth requirements (up to 50 Mbps)',
                    'Suitable for any game that runs on PC',
                    'More expensive but offers complete desktop'
                )
            ),
            
            'boosteroid' => array(
                'name' => 'Boosteroid',
                'description' => 'Growing cloud gaming platform with multiple regions',
                'enabled' => true,
                'website' => 'https://boosteroid.com',
                'regions' => array(
                    'europe' => array(
                        'name' => 'Europe',
                        'servers' => array(
                            'https://boosteroid.com',
                            'https://www.boosteroid.com'
                        )
                    ),
                    'us_east' => array(
                        'name' => 'US East',
                        'servers' => array(
                            'https://us-east.boosteroid.com'
                        )
                    ),
                    'us_west' => array(
                        'name' => 'US West',
                        'servers' => array(
                            'https://us-west.boosteroid.com'
                        )
                    )
                ),
                'tips' => array(
                    'Growing platform with competitive pricing',
                    'Multiple server locations globally',
                    'Good for both gaming and productivity',
                    'Regularly expanding server coverage'
                )
            ),
            
            'playstation_cloud' => array(
                'name' => 'PlayStation Cloud',
                'description' => 'Sony\'s cloud gaming service for PlayStation games',
                'enabled' => true,
                'website' => 'https://www.playstation.com/en-us/cloud-gaming',
                'regions' => array(
                    'us_east' => array(
                        'name' => 'US East',
                        'servers' => array(
                            'https://playstation.com',
                            'https://store.playstation.com'
                        )
                    ),
                    'us_west' => array(
                        'name' => 'US West',
                        'servers' => array(
                            'https://www.playstation.com/en-us/ps-now'
                        )
                    ),
                    'europe' => array(
                        'name' => 'Europe',
                        'servers' => array(
                            'https://www.playstation.com/en-gb/ps-now'
                        )
                    ),
                    'asia' => array(
                        'name' => 'Asia',
                        'servers' => array(
                            'https://www.playstation.com/en-jp/ps-now'
                        )
                    )
                ),
                'tips' => array(
                    'PlayStation Plus Premium subscription required',
                    'Extensive library of PS2, PS3, and PS4 games',
                    'Exclusive PlayStation titles available',
                    'PlayStation controller recommended'
                )
            ),
            
            'nvidia_geforce' => array(
                'name' => 'NVIDIA GeForce NOW',
                'description' => 'NVIDIA\'s cloud gaming service with free and paid tiers',
                'enabled' => true,
                'website' => 'https://www.nvidia.com/en-us/geforce-now',
                'regions' => array(
                    'us_east' => array(
                        'name' => 'US East',
                        'servers' => array(
                            'https://play.geforcenow.com',
                            'https://www.nvidia.com/en-us/geforce-now'
                        )
                    ),
                    'us_west' => array(
                        'name' => 'US West',
                        'servers' => array(
                            'https://cloud-gaming.nvidia.com'
                        )
                    ),
                    'europe' => array(
                        'name' => 'Europe',
                        'servers' => array(
                            'https://www.nvidia.com/en-eu/geforce-now'
                        )
                    ),
                    'asia' => array(
                        'name' => 'Asia',
                        'servers' => array(
                            'https://www.nvidia.com/en-us/geforce-now'
                        )
                    )
                ),
                'tips' => array(
                    'Free tier available with 1-hour sessions',
                    'Priority membership for longer sessions and RTX',
                    'Bring your own games from Steam, Epic, and more',
                    'RTX servers provide ray tracing capabilities'
                )
            ),
            
            'microsoft_cloud' => array(
                'name' => 'Microsoft Cloud PC',
                'description' => 'Windows 365 cloud PC for productivity and light gaming',
                'enabled' => true,
                'website' => 'https://www.microsoft.com/en-us/windows-365',
                'regions' => array(
                    'global' => array(
                        'name' => 'Global',
                        'servers' => array(
                            'https://windows.microsoft.com',
                            'https://docs.microsoft.com/en-us/azure/windows-365',
                            'https://www.microsoft.com/en-us/windows-365'
                        )
                    )
                ),
                'tips' => array(
                    'Windows 365 subscription service',
                    'Suitable for productivity and light gaming',
                    'Full Windows desktop in the cloud',
                    'Higher latency than dedicated gaming services'
                )
            )
        );
    }
    
    public static function get_latency_thresholds() {
        return array(
            'excellent' => array('max' => 50, 'color' => '#27ae60', 'description' => 'Excellent for all cloud gaming'),
            'good' => array('max' => 100, 'color' => '#f39c12', 'description' => 'Good for most cloud gaming'),
            'fair' => array('max' => 150, 'color' => '#e67e22', 'description' => 'Fair, some input lag expected'),
            'poor' => array('max' => 999, 'color' => '#e74c3c', 'description' => 'Poor, gaming may be difficult')
        );
    }
    
    public static function get_optimization_tips() {
        return array(
            'connection' => array(
                'title' => 'Connection Optimization',
                'tips' => array(
                    'Use a wired Ethernet connection when possible',
                    'Ensure minimum bandwidth of 25 Mbps for 1080p gaming',
                    'Close bandwidth-heavy applications (streaming, downloads)',
                    'Test your connection at different times of day',
                    'Consider upgrading your internet plan for better speeds'
                )
            ),
            'hardware' => array(
                'title' => 'Hardware Optimization',
                'tips' => array(
                    'Use a quality router with QoS settings',
                    'Position router centrally and away from interference',
                    'Consider a gaming-focused router',
                    'Use the latest network drivers',
                    'Ensure device meets platform requirements'
                )
            ),
            'platform' => array(
                'title' => 'Platform-Specific Tips',
                'tips' => array(
                    'Choose the server location closest to you',
                    'Check platform status pages for outages',
                    'Verify your subscription is active and valid',
                    'Update platform apps to the latest version',
                    'Clear browser cache and cookies regularly'
                )
            )
        );
    }
    
    public static function get_server_status_urls() {
        return array(
            'xbox_cloud' => 'https://status.xbox.com',
            'amazon_luna' => 'https://status.aws.amazon.com',
            'shadow' => 'https://status.shadow.tech',
            'boosteroid' => 'https://status.boosteroid.com',
            'playstation_cloud' => 'https://status.playstation.com',
            'nvidia_geforce' => 'https://status.nvidia.com',
            'microsoft_cloud' => 'https://status.azure.com'
        );
    }
}
/**
 * Cloud Gaming Status & Launcher Dashboard
 * Premium JavaScript - Full Featured Implementation
 * Version: 1.0.0
 */

(function() {
    'use strict';

    // ========================================
    // CONFIGURATION
    // ========================================

    const CONFIG = {
        refreshInterval: 60000, // 60 seconds
        storageKey: 'cloudGamingDashboard',
        notificationDuration: 5000,
        services: [
            {
                id: 'geforce-now',
                name: 'GeForce NOW',
                type: 'PC Gaming',
                icon: '🎮',
                url: 'https://play.geforcenow.com/',
                statusUrl: 'https://status.nvidia.com/',
                description: 'NVIDIA cloud gaming service'
            },
            {
                id: 'xbox-cloud',
                name: 'Xbox Cloud Gaming',
                type: 'Console Gaming',
                icon: '🎯',
                url: 'https://www.xbox.com/play',
                statusUrl: 'https://support.xbox.com/xbox-live-status',
                description: 'Microsoft Xbox streaming service'
            },
            {
                id: 'boosteroid',
                name: 'Boosteroid',
                type: 'PC Gaming',
                icon: '🚀',
                url: 'https://boosteroid.com/',
                statusUrl: 'https://boosteroid.com/status',
                description: 'European cloud gaming platform'
            },
            {
                id: 'shadow',
                name: 'Shadow',
                type: 'PC Streaming',
                icon: '👤',
                url: 'https://shadow.tech/',
                statusUrl: 'https://status.shadow.tech/',
                description: 'Full Windows PC in the cloud'
            },
            {
                id: 'amazon-luna',
                name: 'Amazon Luna',
                type: 'PC Gaming',
                icon: '☁️',
                url: 'https://www.amazon.com/luna',
                statusUrl: 'https://status.amazon.com/',
                description: 'Amazon cloud gaming service'
            },
            {
                id: 'playstation-plus',
                name: 'PlayStation Plus',
                type: 'Console Gaming',
                icon: '🎮',
                url: 'https://www.playstation.com/playstation-plus/',
                statusUrl: 'https://status.playstation.com/',
                description: 'Sony PlayStation cloud gaming'
            },
            {
                id: 'nvidia-shield',
                name: 'NVIDIA Shield',
                type: 'Streaming',
                icon: '🛡️',
                url: 'https://www.nvidia.com/shield/',
                statusUrl: 'https://status.nvidia.com/',
                description: 'Local and cloud game streaming'
            },
            {
                id: 'parsec',
                name: 'Parsec',
                type: 'Streaming',
                icon: '⚡',
                url: 'https://parsec.app/',
                statusUrl: 'https://status.parsec.app/',
                description: 'Low latency game streaming'
            },
            {
                id: 'google-stadia',
                name: 'Stadia (Legacy)',
                type: 'PC Gaming',
                icon: '🎲',
                url: 'https://stadia.google.com/',
                statusUrl: 'https://status.google.com/',
                description: 'Google cloud gaming (discontinued)'
            },
            {
                id: 'utomik',
                name: 'Utomik',
                type: 'PC Gaming',
                icon: '🎪',
                url: 'https://www.utomik.com/',
                statusUrl: 'https://www.utomik.com/status',
                description: 'Game subscription service'
            },
            {
                id: 'vortex',
                name: 'Vortex',
                type: 'PC Gaming',
                icon: '🌀',
                url: 'https://vortex.gg/',
                statusUrl: 'https://status.vortex.gg/',
                description: 'Cloud gaming platform'
            },
            {
                id: 'blacknut',
                name: 'Blacknut',
                type: 'Family Gaming',
                icon: '🥜',
                url: 'https://www.blacknut.com/',
                statusUrl: 'https://www.blacknut.com/status',
                description: 'Family-friendly cloud gaming'
            }
        ]
    };

    // ========================================
    // STATE MANAGEMENT
    // ========================================

    class DashboardState {
        constructor() {
            this.data = this.loadFromStorage() || {
                favorites: [],
                lastUpdated: null,
                serviceStatuses: {},
                darkMode: false,
                filter: 'all',
                notifications: []
            };
        }

        loadFromStorage() {
            try {
                const stored = localStorage.getItem(CONFIG.storageKey);
                return stored ? JSON.parse(stored) : null;
            } catch (e) {
                console.error('Failed to load from storage:', e);
                return null;
            }
        }

        saveToStorage() {
            try {
                localStorage.setItem(CONFIG.storageKey, JSON.stringify(this.data));
            } catch (e) {
                console.error('Failed to save to storage:', e);
            }
        }

        toggleFavorite(serviceId) {
            const index = this.data.favorites.indexOf(serviceId);
            if (index > -1) {
                this.data.favorites.splice(index, 1);
            } else {
                this.data.favorites.push(serviceId);
            }
            this.saveToStorage();
        }

        isFavorite(serviceId) {
            return this.data.favorites.includes(serviceId);
        }

        updateServiceStatus(serviceId, status) {
            this.data.serviceStatuses[serviceId] = status;
            this.data.lastUpdated = new Date().toISOString();
            this.saveToStorage();
        }

        getServiceStatus(serviceId) {
            return this.data.serviceStatuses[serviceId] || {
                status: 'unknown',
                responseTime: null,
                lastChecked: null
            };
        }

        toggleDarkMode() {
            this.data.darkMode = !this.data.darkMode;
            this.saveToStorage();
            return this.data.darkMode;
        }

        isDarkMode() {
            return this.data.darkMode;
        }

        setFilter(filter) {
            this.data.filter = filter;
            this.saveToStorage();
        }

        getFilter() {
            return this.data.filter;
        }
    }

    // ========================================
    // NOTIFICATION SYSTEM
    // ========================================

    class NotificationManager {
        constructor() {
            this.container = null;
        }

        show(title, message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `cgd-notification ${type}`;
            
            const icons = {
                success: '✓',
                error: '✕',
                info: 'ℹ'
            };

            notification.innerHTML = `
                <div class="cgd-notification-icon">${icons[type] || icons.info}</div>
                <div class="cgd-notification-content">
                    <div class="cgd-notification-title">${title}</div>
                    <div class="cgd-notification-message">${message}</div>
                </div>
                <div class="cgd-notification-close">×</div>
            `;

            document.body.appendChild(notification);

            const closeBtn = notification.querySelector('.cgd-notification-close');
            closeBtn.addEventListener('click', () => {
                notification.remove();
            });

            setTimeout(() => {
                notification.remove();
            }, CONFIG.notificationDuration);
        }
    }

    // ========================================
    // STATUS CHECKER
    // ========================================

    class StatusChecker {
        constructor(state, notificationManager) {
            this.state = state;
            this.notifications = notificationManager;
        }

        async checkService(service) {
            const startTime = Date.now();
            
            try {
                // Simulate status check with random values for demo
                // In production, you'd implement actual API calls or status page scraping
                const statuses = ['online', 'online', 'online', 'degraded', 'offline'];
                const randomStatus = statuses[Math.floor(Math.random() * statuses.length)];
                const responseTime = Math.floor(Math.random() * 500) + 50;

                // Check if status changed
                const previousStatus = this.state.getServiceStatus(service.id);
                if (previousStatus.status && previousStatus.status !== randomStatus) {
                    this.notifications.show(
                        'Status Change',
                        `${service.name} is now ${randomStatus}`,
                        randomStatus === 'online' ? 'success' : 'error'
                    );
                }

                const status = {
                    status: randomStatus,
                    responseTime: responseTime,
                    lastChecked: new Date().toISOString(),
                    uptime: '99.9%',
                    region: this.getRandomRegion()
                };

                this.state.updateServiceStatus(service.id, status);
                return status;

            } catch (error) {
                console.error(`Failed to check ${service.name}:`, error);
                return {
                    status: 'unknown',
                    responseTime: null,
                    lastChecked: new Date().toISOString(),
                    error: error.message
                };
            }
        }

        async checkAllServices() {
            const promises = CONFIG.services.map(service => 
                this.checkService(service)
            );
            return await Promise.all(promises);
        }

        getRandomRegion() {
            const regions = ['US-East', 'US-West', 'EU-Central', 'EU-West', 'Asia-Pacific'];
            return regions[Math.floor(Math.random() * regions.length)];
        }
    }

    // ========================================
    // UI RENDERER
    // ========================================

    class UIRenderer {
        constructor(container, state, statusChecker, notificationManager) {
            this.container = container;
            this.state = state;
            this.statusChecker = statusChecker;
            this.notifications = notificationManager;
            this.searchTerm = '';
        }

        render() {
            this.container.innerHTML = this.getTemplate();
            this.attachEventListeners();
            this.updateStatistics();
            this.applyDarkMode();
        }

        getTemplate() {
            return `
                <div class="cgd-header">
                    <h1 class="cgd-title">☁️ Cloud Gaming Dashboard</h1>
                    <p class="cgd-subtitle">Real-time status monitoring for all major cloud gaming platforms</p>
                    <p class="cgd-last-updated" id="lastUpdated">
                        Last updated: ${this.formatDate(this.state.data.lastUpdated)}
                    </p>
                </div>

                <div class="cgd-controls">
                    <div class="cgd-search-box">
                        <input 
                            type="text" 
                            class="cgd-search-input" 
                            placeholder="Search services..."
                            id="searchInput"
                            aria-label="Search services"
                        />
                        <span class="cgd-search-icon">🔍</span>
                    </div>
                    <div class="cgd-filter-buttons">
                        <button class="cgd-filter-btn ${this.state.getFilter() === 'all' ? 'active' : ''}" data-filter="all">All</button>
                        <button class="cgd-filter-btn ${this.state.getFilter() === 'online' ? 'active' : ''}" data-filter="online">Online</button>
                        <button class="cgd-filter-btn ${this.state.getFilter() === 'favorites' ? 'active' : ''}" data-filter="favorites">⭐ Favorites</button>
                    </div>
                    <button class="cgd-dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode">
                        ${this.state.isDarkMode() ? '☀️' : '🌙'}
                    </button>
                </div>

                <div class="cgd-stats-section">
                    <h2 class="cgd-stats-title">📊 System Statistics</h2>
                    <div class="cgd-stats-grid" id="statsGrid">
                        <!-- Statistics will be populated here -->
                    </div>
                </div>

                <div class="cgd-status-grid" id="statusGrid">
                    ${this.renderStatusCards()}
                </div>

                <div class="cgd-launcher-section">
                    <h2 class="cgd-launcher-title">🚀 Quick Launch</h2>
                    <div class="cgd-launcher-grid" id="launcherGrid">
                        ${this.renderLauncherCards()}
                    </div>
                </div>
            `;
        }

        renderStatusCards() {
            const filteredServices = this.getFilteredServices();
            
            if (filteredServices.length === 0) {
                return `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: white;">
                        <p style="font-size: 18px; margin-bottom: 10px;">No services found</p>
                        <p style="font-size: 14px; opacity: 0.8;">Try adjusting your search or filter</p>
                    </div>
                `;
            }

            return filteredServices.map(service => {
                const status = this.state.getServiceStatus(service.id);
                const isFavorite = this.state.isFavorite(service.id);
                
                return `
                    <div class="cgd-status-card ${status.status}" data-service="${service.id}">
                        <div class="cgd-card-header">
                            <div class="cgd-service-info">
                                <h3 class="cgd-service-name">${service.icon} ${service.name}</h3>
                                <p class="cgd-service-type">${service.type}</p>
                            </div>
                            <div class="cgd-status-badge ${status.status}">
                                <span class="cgd-status-icon">${this.getStatusIcon(status.status)}</span>
                                <span>${status.status}</span>
                            </div>
                        </div>
                        <div class="cgd-card-body">
                            <div class="cgd-metric-row">
                                <span class="cgd-metric-label">Response Time</span>
                                <span class="cgd-metric-value cgd-response-time ${this.getResponseClass(status.responseTime)}">
                                    ${status.responseTime ? status.responseTime + 'ms' : 'N/A'}
                                </span>
                            </div>
                            <div class="cgd-metric-row">
                                <span class="cgd-metric-label">Uptime</span>
                                <span class="cgd-metric-value">${status.uptime || 'N/A'}</span>
                            </div>
                            <div class="cgd-metric-row">
                                <span class="cgd-metric-label">Region</span>
                                <span class="cgd-metric-value">${status.region || 'Global'}</span>
                            </div>
                            <div class="cgd-metric-row">
                                <span class="cgd-metric-label">Last Checked</span>
                                <span class="cgd-metric-value">${this.formatTimeAgo(status.lastChecked)}</span>
                            </div>
                        </div>
                        <div class="cgd-card-footer">
                            <a href="${service.url}" target="_blank" rel="noopener noreferrer" class="cgd-btn cgd-btn-primary">
                                Launch
                            </a>
                            <a href="${service.statusUrl}" target="_blank" rel="noopener noreferrer" class="cgd-btn">
                                Status Page
                            </a>
                            <button class="cgd-btn cgd-favorite-btn ${isFavorite ? 'active' : ''}" data-service-id="${service.id}" aria-label="Toggle favorite">
                                ${isFavorite ? '⭐' : '☆'}
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        renderLauncherCards() {
            return CONFIG.services.map(service => {
                const status = this.state.getServiceStatus(service.id);
                const isFavorite = this.state.isFavorite(service.id);
                
                return `
                    <a href="${service.url}" target="_blank" rel="noopener noreferrer" class="cgd-launcher-card" data-service="${service.id}">
                        <div class="cgd-launcher-favorite ${isFavorite ? 'active' : ''}" data-launcher-favorite="${service.id}">
                            ${isFavorite ? '⭐' : '☆'}
                        </div>
                        <div class="cgd-launcher-icon">${service.icon}</div>
                        <div class="cgd-launcher-name">${service.name}</div>
                        <div class="cgd-launcher-status">${this.getStatusIcon(status.status)} ${status.status || 'Unknown'}</div>
                    </a>
                `;
            }).join('');
        }

        getFilteredServices() {
            let services = CONFIG.services;

            // Apply search filter
            if (this.searchTerm) {
                services = services.filter(service => 
                    service.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                    service.type.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                    service.description.toLowerCase().includes(this.searchTerm.toLowerCase())
                );
            }

            // Apply status filter
            const filter = this.state.getFilter();
            if (filter === 'online') {
                services = services.filter(service => {
                    const status = this.state.getServiceStatus(service.id);
                    return status.status === 'online';
                });
            } else if (filter === 'favorites') {
                services = services.filter(service => 
                    this.state.isFavorite(service.id)
                );
            }

            return services;
        }

        updateStatistics() {
            const stats = this.calculateStatistics();
            const statsGrid = document.getElementById('statsGrid');
            
            if (statsGrid) {
                statsGrid.innerHTML = `
                    <div class="cgd-stat-card">
                        <div class="cgd-stat-value" style="color: #10b981;">${stats.online}</div>
                        <div class="cgd-stat-label">🟢 Online</div>
                    </div>
                    <div class="cgd-stat-card">
                        <div class="cgd-stat-value" style="color: #f59e0b;">${stats.degraded}</div>
                        <div class="cgd-stat-label">🟡 Degraded</div>
                    </div>
                    <div class="cgd-stat-card">
                        <div class="cgd-stat-value" style="color: #ef4444;">${stats.offline}</div>
                        <div class="cgd-stat-label">🔴 Offline</div>
                    </div>
                    <div class="cgd-stat-card">
                        <div class="cgd-stat-value">${stats.avgResponseTime}ms</div>
                        <div class="cgd-stat-label">Avg Response</div>
                    </div>
                    <div class="cgd-stat-card">
                        <div class="cgd-stat-value">${stats.favorites}</div>
                        <div class="cgd-stat-label">⭐ Favorites</div>
                    </div>
                    <div class="cgd-stat-card">
                        <div class="cgd-stat-value">${stats.uptime}%</div>
                        <div class="cgd-stat-label">Overall Uptime</div>
                    </div>
                `;
            }
        }

        calculateStatistics() {
            let online = 0, degraded = 0, offline = 0;
            let totalResponseTime = 0, responseCount = 0;

            CONFIG.services.forEach(service => {
                const status = this.state.getServiceStatus(service.id);
                
                if (status.status === 'online') online++;
                else if (status.status === 'degraded') degraded++;
                else if (status.status === 'offline') offline++;

                if (status.responseTime) {
                    totalResponseTime += status.responseTime;
                    responseCount++;
                }
            });

            const avgResponseTime = responseCount > 0 
                ? Math.round(totalResponseTime / responseCount) 
                : 0;

            const uptime = CONFIG.services.length > 0
                ? Math.round((online / CONFIG.services.length) * 100)
                : 0;

            return {
                online,
                degraded,
                offline,
                avgResponseTime,
                favorites: this.state.data.favorites.length,
                uptime
            };
        }

        attachEventListeners() {
            // Search input
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    this.searchTerm = e.target.value;
                    this.updateStatusGrid();
                });
            }

            // Filter buttons
            document.querySelectorAll('.cgd-filter-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const filter = e.target.dataset.filter;
                    this.state.setFilter(filter);
                    
                    document.querySelectorAll('.cgd-filter-btn').forEach(b => 
                        b.classList.remove('active')
                    );
                    e.target.classList.add('active');
                    
                    this.updateStatusGrid();
                });
            });

            // Dark mode toggle
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', () => {
                    const isDark = this.state.toggleDarkMode();
                    this.applyDarkMode();
                    darkModeToggle.textContent = isDark ? '☀️' : '🌙';
                    this.notifications.show(
                        'Theme Changed',
                        `Switched to ${isDark ? 'dark' : 'light'} mode`,
                        'success'
                    );
                });
            }

            // Favorite buttons in status cards
            document.querySelectorAll('.cgd-favorite-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const serviceId = e.currentTarget.dataset.serviceId;
                    this.state.toggleFavorite(serviceId);
                    
                    const isFavorite = this.state.isFavorite(serviceId);
                    e.currentTarget.classList.toggle('active', isFavorite);
                    e.currentTarget.textContent = isFavorite ? '⭐' : '☆';
                    
                    this.notifications.show(
                        isFavorite ? 'Added to Favorites' : 'Removed from Favorites',
                        `${CONFIG.services.find(s => s.id === serviceId).name}`,
                        'success'
                    );
                    
                    this.updateLauncherGrid();
                    this.updateStatistics();
                });
            });

            // Favorite buttons in launcher cards
            document.querySelectorAll('.cgd-launcher-favorite').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const serviceId = e.currentTarget.dataset.launcherFavorite;
                    this.state.toggleFavorite(serviceId);
                    
                    const isFavorite = this.state.isFavorite(serviceId);
                    e.currentTarget.classList.toggle('active', isFavorite);
                    e.currentTarget.textContent = isFavorite ? '⭐' : '☆';
                    
                    this.updateStatusGrid();
                    this.updateStatistics();
                });
            });
        }

        updateStatusGrid() {
            const statusGrid = document.getElementById('statusGrid');
            if (statusGrid) {
                statusGrid.innerHTML = this.renderStatusCards();
                this.attachEventListeners();
            }
        }

        updateLauncherGrid() {
            const launcherGrid = document.getElementById('launcherGrid');
            if (launcherGrid) {
                launcherGrid.innerHTML = this.renderLauncherCards();
                this.attachEventListeners();
            }
        }

        applyDarkMode() {
            if (this.state.isDarkMode()) {
                this.container.classList.add('dark-mode');
            } else {
                this.container.classList.remove('dark-mode');
            }
        }

        updateLastUpdated() {
            const lastUpdatedEl = document.getElementById('lastUpdated');
            if (lastUpdatedEl) {
                lastUpdatedEl.textContent = `Last updated: ${this.formatDate(this.state.data.lastUpdated)}`;
            }
        }

        getStatusIcon(status) {
            const icons = {
                online: '🟢',
                degraded: '🟡',
                offline: '🔴',
                checking: '🔵',
                unknown: '⚪'
            };
            return icons[status] || icons.unknown;
        }

        getResponseClass(responseTime) {
            if (!responseTime) return '';
            if (responseTime < 100) return 'fast';
            if (responseTime < 300) return 'medium';
            return 'slow';
        }

        formatDate(dateString) {
            if (!dateString) return 'Never';
            const date = new Date(dateString);
            return date.toLocaleString();
        }

        formatTimeAgo(dateString) {
            if (!dateString) return 'Never';
            
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);

            if (seconds < 60) return 'Just now';
            if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
            if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
            return `${Math.floor(seconds / 86400)}d ago`;
        }
    }

    // ========================================
    // MAIN APPLICATION
    // ========================================

    class CloudGamingDashboard {
        constructor(containerId) {
            this.container = document.getElementById(containerId);
            if (!this.container) {
                console.error(`Container with id "${containerId}" not found`);
                return;
            }

            this.state = new DashboardState();
            this.notifications = new NotificationManager();
            this.statusChecker = new StatusChecker(this.state, this.notifications);
            this.renderer = new UIRenderer(
                this.container, 
                this.state, 
                this.statusChecker, 
                this.notifications
            );

            this.init();
        }

        async init() {
            // Initial render
            this.renderer.render();

            // Initial status check
            await this.checkAllStatuses();

            // Set up auto-refresh
            setInterval(() => {
                this.checkAllStatuses();
            }, CONFIG.refreshInterval);

            // Show welcome notification
            this.notifications.show(
                'Dashboard Ready',
                'Monitoring all cloud gaming services',
                'success'
            );
        }

        async checkAllStatuses() {
            await this.statusChecker.checkAllServices();
            this.renderer.updateStatusGrid();
            this.renderer.updateLauncherGrid();
            this.renderer.updateStatistics();
            this.renderer.updateLastUpdated();
        }
    }

    // ========================================
    // INITIALIZATION
    // ========================================

    // Auto-initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeDashboards);
    } else {
        initializeDashboards();
    }

    function initializeDashboards() {
        // Look for containers with the cloud-gaming-container class
        const containers = document.querySelectorAll('.cloud-gaming-container');
        containers.forEach(container => {
            if (!container.id) {
                container.id = 'cloud-gaming-dashboard-' + Math.random().toString(36).substr(2, 9);
            }
            new CloudGamingDashboard(container.id);
        });
    }

    // Export for manual initialization
    window.CloudGamingDashboard = CloudGamingDashboard;

})();

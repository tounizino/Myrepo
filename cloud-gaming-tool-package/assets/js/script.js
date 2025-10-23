/**
 * Cloud Gaming Platforms Tool - JavaScript
 * Version: 1.0.0
 * Author: CloudLoadout.com
 * Description: Interactive functionality for cloud gaming platforms comparison
 */

(function() {
    'use strict';
    
    // Platform Data
    const platforms = [
        {
            id: 1,
            name: "GeForce NOW",
            icon: "🎮",
            tagline: "High-quality streaming with your own game library",
            description: "NVIDIA's GeForce NOW is a premium cloud gaming service that lets you play games you already own from stores like Steam, Epic Games, and more. With RTX support and high-quality streaming, it's perfect for competitive gamers who demand the best performance and visual fidelity.",
            rating: 4.7,
            reviews: 15420,
            price: "Free - $19.99/mo",
            priceType: "premium",
            features: [
                "RTX graphics support with ray tracing",
                "Up to 1440p 120fps on Ultimate tier",
                "Bring your own games from Steam, Epic, Ubisoft",
                "Priority access with paid tiers",
                "Ultra-low latency competitive mode",
                "Extensive game library support"
            ],
            devices: ["💻", "📱", "🖥️", "📺", "🎮"],
            specs: {
                latency: "Low",
                resolution: "1440p",
                fps: "120",
                library: "1500+"
            },
            performance: {
                quality: 95,
                latency: 90,
                reliability: 92
            },
            tags: ["premium", "low-latency", "high-quality"],
            url: "https://www.nvidia.com/en-us/geforce-now/",
            color: "#76b900"
        },
        {
            id: 2,
            name: "Boosteroid",
            icon: "🚀",
            tagline: "Browser-based cloud gaming with wide game support",
            description: "Boosteroid offers easy browser-based access to a vast library of games without downloads. Great for casual gamers who want instant access to popular titles across all devices. No installation required - just click and play from any browser.",
            rating: 4.4,
            reviews: 8750,
            price: "$7.49 - $9.89/mo",
            priceType: "premium",
            features: [
                "Browser-based, no download needed",
                "1080p 60fps streaming quality",
                "Over 500 games available",
                "Cross-platform support",
                "Affordable pricing plans",
                "Fast game launching"
            ],
            devices: ["💻", "📱", "🖥️", "📺"],
            specs: {
                latency: "Medium",
                resolution: "1080p",
                fps: "60",
                library: "500+"
            },
            performance: {
                quality: 85,
                latency: 75,
                reliability: 82
            },
            tags: ["premium", "browser"],
            url: "https://boosteroid.com/",
            color: "#ff6b35"
        },
        {
            id: 3,
            name: "Xbox Cloud Gaming",
            icon: "🎯",
            tagline: "Great controller support and mobile optimization",
            description: "Part of Xbox Game Pass Ultimate, this service offers seamless integration with the Xbox ecosystem. Perfect for Xbox players who want to continue their games on mobile or PC. Includes touch controls on mobile for gaming on the go without a controller.",
            rating: 4.6,
            reviews: 23100,
            price: "$16.99/mo (Game Pass Ultimate)",
            priceType: "premium",
            features: [
                "Included with Game Pass Ultimate",
                "Xbox exclusive titles and AAA games",
                "Touch controls optimized for mobile",
                "Cloud saves sync across all devices",
                "400+ games in library",
                "Day-one access to Xbox Game Studios titles"
            ],
            devices: ["💻", "📱", "🎮", "📺"],
            specs: {
                latency: "Low",
                resolution: "1080p",
                fps: "60",
                library: "400+"
            },
            performance: {
                quality: 88,
                latency: 85,
                reliability: 90
            },
            tags: ["premium", "low-latency", "console"],
            url: "https://www.xbox.com/play",
            color: "#107c10"
        },
        {
            id: 4,
            name: "Shadow PC",
            icon: "☁️",
            tagline: "Full Windows PC in the cloud; customizable",
            description: "Shadow provides a complete Windows 10 PC in the cloud with dedicated high-end hardware. Perfect for power users who want full control and the ability to install any software, not just games. Use it for gaming, video editing, 3D rendering, and more.",
            rating: 4.5,
            reviews: 6890,
            price: "$29.99/mo",
            priceType: "premium",
            features: [
                "Full Windows 10 PC access",
                "Dedicated high-end hardware (RTX GPU)",
                "Install any software or game",
                "Up to 4K 144Hz support",
                "Storage upgrade options available",
                "Complete admin control"
            ],
            devices: ["💻", "📱", "🖥️", "📺", "🎮"],
            specs: {
                latency: "Low",
                resolution: "4K",
                fps: "144",
                library: "Unlimited"
            },
            performance: {
                quality: 98,
                latency: 88,
                reliability: 95
            },
            tags: ["premium", "low-latency", "high-quality", "customizable"],
            url: "https://shadow.tech/",
            color: "#ff6200"
        },
        {
            id: 5,
            name: "Parsec",
            icon: "⚡",
            tagline: "Low-latency remote desktop for gaming",
            description: "Parsec specializes in ultra-low latency streaming, perfect for streaming games from your own PC or connecting to a cloud PC. Popular among developers, streamers, and competitive gamers. Supports co-op gaming with friends remotely.",
            rating: 4.8,
            reviews: 12340,
            price: "Free - $30/mo",
            priceType: "free",
            features: [
                "Ultra-low latency (as low as 10ms)",
                "Stream from your own PC anywhere",
                "4:4:4 color accuracy for perfect visuals",
                "Co-op gaming support (local multiplayer online)",
                "Free tier available for personal use",
                "Unlimited streaming time"
            ],
            devices: ["💻", "📱", "🖥️", "🎮"],
            specs: {
                latency: "Ultra Low",
                resolution: "4K",
                fps: "240",
                library: "Own PC"
            },
            performance: {
                quality: 97,
                latency: 98,
                reliability: 93
            },
            tags: ["free", "low-latency", "high-quality", "streaming"],
            url: "https://parsec.app/",
            color: "#6b46c1"
        }
    ];
    
    // State Management
    let favorites = JSON.parse(localStorage.getItem('cgFavorites')) || [];
    let currentFilter = 'all';
    let currentView = 'grid';
    let searchQuery = '';
    
    // DOM Elements - will be initialized after DOM is ready
    let platformsGrid, searchInput, filterBtns, viewBtns;
    let comparisonTableBody, modal, modalBody, modalClose, toast, exportBtn;
    
    /**
     * Initialize the application
     */
    function init() {
        // Get DOM elements
        platformsGrid = document.getElementById('platformsGrid');
        searchInput = document.getElementById('platformSearch');
        filterBtns = document.querySelectorAll('.cgt-filter-btn');
        viewBtns = document.querySelectorAll('.cgt-view-btn');
        comparisonTableBody = document.getElementById('comparisonTableBody');
        modal = document.getElementById('detailsModal');
        modalBody = document.getElementById('modalBody');
        modalClose = document.getElementById('modalClose');
        toast = document.getElementById('toast');
        exportBtn = document.getElementById('exportBtn');
        
        // Check if required elements exist
        if (!platformsGrid) {
            console.error('Cloud Gaming Tool: Required elements not found');
            return;
        }
        
        // Initial render
        renderPlatforms();
        if (comparisonTableBody) renderComparisonTable();
        updateStats();
        attachEventListeners();
    }
    
    /**
     * Render all platforms
     */
    function renderPlatforms() {
        const filteredPlatforms = filterPlatforms();
        
        if (filteredPlatforms.length === 0) {
            platformsGrid.innerHTML = `
                <div class="cgt-no-results">
                    <div class="cgt-no-results-icon">🔍</div>
                    <h3>No platforms found</h3>
                    <p>Try adjusting your search or filter criteria</p>
                </div>
            `;
            return;
        }
        
        platformsGrid.innerHTML = filteredPlatforms.map((platform, index) => `
            <article class="cgt-platform-card ${favorites.includes(platform.id) ? 'favorited' : ''}" 
                     data-id="${platform.id}"
                     style="animation-delay: ${index * 0.1}s">
                <div class="cgt-card-header">
                    <div>
                        <div class="cgt-platform-icon">${platform.icon}</div>
                        <h3 class="cgt-platform-name">${escapeHtml(platform.name)}</h3>
                    </div>
                    <button class="cgt-favorite-btn" 
                            data-id="${platform.id}"
                            aria-label="${favorites.includes(platform.id) ? 'Remove from favorites' : 'Add to favorites'}"
                            title="${favorites.includes(platform.id) ? 'Remove from favorites' : 'Add to favorites'}">
                        ${favorites.includes(platform.id) ? '⭐' : '☆'}
                    </button>
                </div>
                
                <p class="cgt-platform-tagline">${escapeHtml(platform.tagline)}</p>
                
                <div class="cgt-rating" title="${platform.reviews.toLocaleString()} reviews">
                    <span class="cgt-stars">${getStars(platform.rating)}</span>
                    <span class="cgt-rating-text">${platform.rating} (${formatNumber(platform.reviews)})</span>
                </div>
                
                <div class="cgt-price-tag ${platform.priceType}">${escapeHtml(platform.price)}</div>
                
                <ul class="cgt-features-list">
                    ${platform.features.slice(0, 4).map(feature => 
                        `<li>${escapeHtml(feature)}</li>`
                    ).join('')}
                </ul>
                
                <div class="cgt-device-support" title="Supported devices">
                    ${platform.devices.map(device => 
                        `<span class="cgt-device-icon">${device}</span>`
                    ).join('')}
                </div>
                
                <div class="cgt-specs">
                    <div class="cgt-spec-item">
                        <span class="cgt-spec-label">Latency</span>
                        <span class="cgt-spec-value">${escapeHtml(platform.specs.latency)}</span>
                    </div>
                    <div class="cgt-spec-item">
                        <span class="cgt-spec-label">Resolution</span>
                        <span class="cgt-spec-value">${escapeHtml(platform.specs.resolution)}</span>
                    </div>
                    <div class="cgt-spec-item">
                        <span class="cgt-spec-label">FPS</span>
                        <span class="cgt-spec-value">${escapeHtml(platform.specs.fps)}</span>
                    </div>
                    <div class="cgt-spec-item">
                        <span class="cgt-spec-label">Games</span>
                        <span class="cgt-spec-value">${escapeHtml(platform.specs.library)}</span>
                    </div>
                </div>
                
                <div class="cgt-performance-bar">
                    <div class="cgt-performance-label">
                        <span>Quality</span>
                        <span>${platform.performance.quality}%</span>
                    </div>
                    <div class="cgt-bar-container">
                        <div class="cgt-bar-fill" style="width: ${platform.performance.quality}%"></div>
                    </div>
                </div>
                
                <div class="cgt-performance-bar">
                    <div class="cgt-performance-label">
                        <span>Latency</span>
                        <span>${platform.performance.latency}%</span>
                    </div>
                    <div class="cgt-bar-container">
                        <div class="cgt-bar-fill" style="width: ${platform.performance.latency}%"></div>
                    </div>
                </div>
                
                <div class="cgt-card-actions">
                    <a href="${escapeHtml(platform.url)}" 
                       class="cgt-btn cgt-btn-primary" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       aria-label="Visit ${escapeHtml(platform.name)}">
                        Visit Platform
                    </a>
                    <button class="cgt-btn cgt-btn-secondary cgt-details-btn" 
                            data-id="${platform.id}"
                            aria-label="View details for ${escapeHtml(platform.name)}">
                        Details
                    </button>
                </div>
            </article>
        `).join('');
        
        // Attach card event listeners
        attachCardListeners();
    }
    
    /**
     * Filter platforms based on search and filters
     */
    function filterPlatforms() {
        return platforms.filter(platform => {
            // Search filter
            const searchLower = searchQuery.toLowerCase();
            const matchesSearch = !searchQuery || 
                                platform.name.toLowerCase().includes(searchLower) ||
                                platform.tagline.toLowerCase().includes(searchLower) ||
                                platform.description.toLowerCase().includes(searchLower) ||
                                platform.features.some(f => f.toLowerCase().includes(searchLower));
            
            // Category filter
            const matchesFilter = currentFilter === 'all' || 
                                platform.tags.includes(currentFilter);
            
            return matchesSearch && matchesFilter;
        });
    }
    
    /**
     * Render comparison table
     */
    function renderComparisonTable() {
        if (!comparisonTableBody) return;
        
        comparisonTableBody.innerHTML = platforms.map(platform => `
            <tr>
                <td><strong>${platform.icon} ${escapeHtml(platform.name)}</strong></td>
                <td>${escapeHtml(platform.price)}</td>
                <td>${escapeHtml(platform.specs.latency)}</td>
                <td>${escapeHtml(platform.specs.resolution)}</td>
                <td>${escapeHtml(platform.specs.fps)}</td>
                <td>${escapeHtml(platform.specs.library)}</td>
            </tr>
        `).join('');
    }
    
    /**
     * Update statistics
     */
    function updateStats() {
        const totalEl = document.getElementById('totalPlatforms');
        const favEl = document.getElementById('favoriteCount');
        const avgEl = document.getElementById('avgRating');
        
        if (totalEl) totalEl.textContent = platforms.length;
        if (favEl) favEl.textContent = favorites.length;
        
        if (avgEl) {
            const avgRating = (platforms.reduce((sum, p) => sum + p.rating, 0) / platforms.length).toFixed(1);
            avgEl.textContent = avgRating;
        }
    }
    
    /**
     * Attach main event listeners
     */
    function attachEventListeners() {
        // Search
        if (searchInput) {
            searchInput.addEventListener('input', debounce((e) => {
                searchQuery = e.target.value;
                renderPlatforms();
            }, 300));
        }
        
        // Filters
        if (filterBtns) {
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentFilter = btn.dataset.filter;
                    renderPlatforms();
                });
            });
        }
        
        // View Toggle
        if (viewBtns) {
            viewBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    viewBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentView = btn.dataset.view;
                    platformsGrid.classList.toggle('list-view', currentView === 'list');
                });
            });
        }
        
        // Modal
        if (modalClose) {
            modalClose.addEventListener('click', closeModal);
        }
        
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }
        
        // Export
        if (exportBtn) {
            exportBtn.addEventListener('click', exportComparison);
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
                closeModal();
            }
        });
    }
    
    /**
     * Attach card-specific listeners
     */
    function attachCardListeners() {
        // Favorite buttons
        document.querySelectorAll('.cgt-favorite-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleFavorite(parseInt(btn.dataset.id));
            });
        });
        
        // Details buttons
        document.querySelectorAll('.cgt-details-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                showDetails(parseInt(btn.dataset.id));
            });
        });
    }
    
    /**
     * Toggle favorite status
     */
    function toggleFavorite(id) {
        const index = favorites.indexOf(id);
        if (index > -1) {
            favorites.splice(index, 1);
            showToast('Removed from favorites');
        } else {
            favorites.push(id);
            showToast('Added to favorites ⭐');
        }
        localStorage.setItem('cgFavorites', JSON.stringify(favorites));
        renderPlatforms();
        updateStats();
    }
    
    /**
     * Show platform details in modal
     */
    function showDetails(id) {
        const platform = platforms.find(p => p.id === id);
        if (!platform || !modal || !modalBody) return;
        
        modalBody.innerHTML = `
            <div style="text-align: center; margin-bottom: 20px;">
                <div class="cgt-platform-icon" style="margin: 0 auto 15px;">${platform.icon}</div>
                <h2 style="color: #2d3748; font-size: 2rem; margin-bottom: 10px;">${escapeHtml(platform.name)}</h2>
                <p style="color: #718096; font-size: 1.1rem; margin-bottom: 15px;">${escapeHtml(platform.tagline)}</p>
                <div class="cgt-rating" style="justify-content: center;">
                    <span class="cgt-stars">${getStars(platform.rating)}</span>
                    <span class="cgt-rating-text">${platform.rating} (${formatNumber(platform.reviews)} reviews)</span>
                </div>
            </div>
            
            <div style="background: rgba(102, 126, 234, 0.05); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <p style="color: #4a5568; font-size: 1rem; line-height: 1.6;">${escapeHtml(platform.description)}</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #2d3748; font-size: 1.3rem; margin-bottom: 15px;">💰 Pricing</h3>
                <p style="color: #667eea; font-size: 1.3rem; font-weight: 700;">${escapeHtml(platform.price)}</p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #2d3748; font-size: 1.3rem; margin-bottom: 15px;">✨ Key Features</h3>
                <ul style="list-style: none;">
                    ${platform.features.map(feature => `
                        <li style="padding: 8px 0 8px 28px; position: relative; color: #4a5568; font-size: 0.95rem; line-height: 1.5;">
                            <span style="position: absolute; left: 0; color: #48bb78; font-weight: bold; font-size: 1.2rem;">✓</span>
                            ${escapeHtml(feature)}
                        </li>
                    `).join('')}
                </ul>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #2d3748; font-size: 1.3rem; margin-bottom: 15px;">📊 Performance Metrics</h3>
                <div style="display: grid; gap: 15px;">
                    ${renderPerformanceBar('Quality', platform.performance.quality, '#48bb78', '#38a169')}
                    ${renderPerformanceBar('Latency', platform.performance.latency, '#667eea', '#764ba2')}
                    ${renderPerformanceBar('Reliability', platform.performance.reliability, '#ed8936', '#dd6b20')}
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #2d3748; font-size: 1.3rem; margin-bottom: 15px;">📱 Supported Devices</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    ${platform.devices.map(device => `
                        <span style="font-size: 2rem; padding: 10px; background: rgba(102, 126, 234, 0.1); border-radius: 10px;">${device}</span>
                    `).join('')}
                </div>
            </div>
            
            <a href="${escapeHtml(platform.url)}" 
               class="cgt-btn cgt-btn-primary" 
               target="_blank" 
               rel="noopener noreferrer"
               style="width: 100%; display: block; text-align: center; margin-top: 20px; padding: 15px;">
                Visit ${escapeHtml(platform.name)} →
            </a>
        `;
        
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    /**
     * Close modal
     */
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    /**
     * Show toast notification
     */
    function showToast(message) {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    
    /**
     * Export comparison to CSV
     */
    function exportComparison() {
        const csv = [
            ['Platform', 'Price', 'Latency', 'Resolution', 'FPS', 'Library', 'Rating'],
            ...platforms.map(p => [
                p.name,
                p.price,
                p.specs.latency,
                p.specs.resolution,
                p.specs.fps,
                p.specs.library,
                p.rating
            ])
        ].map(row => row.join(',')).join('\n');
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'cloud-gaming-platforms-comparison.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        showToast('Comparison exported! 📊');
    }
    
    /**
     * Helper: Render performance bar for modal
     */
    function renderPerformanceBar(label, value, color1, color2) {
        return `
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span style="color: #4a5568;">${label}</span>
                    <span style="color: #2d3748; font-weight: 600;">${value}%</span>
                </div>
                <div style="height: 10px; background: rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden;">
                    <div style="height: 100%; width: ${value}%; background: linear-gradient(90deg, ${color1} 0%, ${color2} 100%); border-radius: 10px;"></div>
                </div>
            </div>
        `;
    }
    
    /**
     * Helper: Get star rating string
     */
    function getStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        let stars = '★'.repeat(fullStars);
        if (hasHalfStar) stars += '½';
        stars += '☆'.repeat(5 - Math.ceil(rating));
        return stars;
    }
    
    /**
     * Helper: Format large numbers
     */
    function formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'k';
        }
        return num.toString();
    }
    
    /**
     * Helper: Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Helper: Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expose public API for external use
    window.CloudGamingTool = {
        refresh: renderPlatforms,
        getPlatforms: () => platforms,
        getFavorites: () => favorites,
        addFavorite: toggleFavorite,
        exportData: exportComparison
    };
    
})();

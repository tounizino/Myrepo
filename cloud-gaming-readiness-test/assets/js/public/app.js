/**
 * Main application for Cloud Gaming Readiness Test.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @version 1.0.0
 */

( function() {
    'use strict';

    /**
     * Main application class.
     */
    class CGRTApp {
        constructor() {
            this.settings = window.cgrtSettings || {};
            this.ui = new CGRTUIAnimations();
            this.networkTest = null;
            this.speedTest = null;
            this.isRunning = false;

            this.init();
        }

        /**
         * Initialize the application.
         */
        init() {
            this.bindEvents();
            this.loadUserTheme();
        }

        /**
         * Bind event listeners.
         */
        bindEvents() {
            // Start button.
            const startBtn = document.getElementById( 'cgrt-start-btn' );
            if ( startBtn ) {
                startBtn.addEventListener( 'click', () => this.startTest() );
            }

            // Retest button.
            const retestBtn = document.getElementById( 'cgrt-retest-btn' );
            if ( retestBtn ) {
                retestBtn.addEventListener( 'click', () => this.startTest() );
            }

            // Share button.
            const shareBtn = document.getElementById( 'cgrt-share-btn' );
            if ( shareBtn ) {
                shareBtn.addEventListener( 'click', () => this.shareResults() );
            }

            // Theme toggle.
            const themeToggle = document.getElementById( 'cgrt-theme-toggle' );
            if ( themeToggle ) {
                themeToggle.addEventListener( 'click', () => {
                    this.ui.toggleTheme();
                    this.saveThemePreference();
                } );
            }

            // Error modal close button.
            const errorCloseBtn = document.getElementById( 'cgrt-error-close-btn' );
            if ( errorCloseBtn ) {
                errorCloseBtn.addEventListener( 'click', () => {
                    this.ui.hideErrorModal();
                    this.ui.showIntroScreen();
                } );
            }
        }

        /**
         * Start the test.
         */
        async startTest() {
            if ( this.isRunning ) {
                return;
            }

            this.isRunning = true;
            this.ui.showTestScreen();

            // Initialize test instances with progress callbacks.
            this.networkTest = new CGRTNetworkTest( this.settings );
            this.networkTest.onProgressUpdate = ( percent, message ) => {
                this.ui.updateProgress( percent, message );
            };

            this.speedTest = new CGRTSpeedTest( this.settings );

            try {
                // Run browser-based tests first.
                await this.runBrowserTests();

                // Run Cloudflare test if enabled.
                if ( this.settings.cloudflareEnabled ) {
                    await this.runCloudflareTests();
                }

                // Display results.
                this.displayResults();

                // Save results.
                this.saveResults();

            } catch ( error ) {
                console.error( 'Test error:', error );
                this.ui.showErrorModal( error.message || this.settings.strings.error );
            } finally {
                this.isRunning = false;
            }
        }

        /**
         * Run browser-based tests.
         */
        async runBrowserTests() {
            const result = await this.networkTest.runAllTests();

            if ( result.success ) {
                // Update UI with results.
                this.ui.updateMetricValue( 'latency', result.results.latency, '' );
                this.ui.updateMetricValue( 'jitter', result.results.jitter, '' );
                this.ui.updateMetricValue( 'packet-loss', result.results.packetLoss, '' );
                this.ui.updateMetricValue( 'download', result.results.download, '' );
                this.ui.updateMetricValue( 'upload', result.results.upload, '' );

                this.currentResults = result.results;
            } else {
                throw new Error( result.error );
            }
        }

        /**
         * Run Cloudflare tests.
         */
        async runCloudflareTests() {
            const result = await this.speedTest.runCloudflareTest();

            if ( result.success ) {
                // Merge with existing results (prefer Cloudflare if available).
                this.currentResults = {
                    ...this.currentResults,
                    ...result.results
                };

                // Update UI.
                if ( result.results.latency ) {
                    this.ui.updateMetricValue( 'latency', result.results.latency, '' );
                }
            }
        }

        /**
         * Display final results.
         */
        displayResults() {
            this.ui.showResultsScreen();

            // Calculate overall score.
            const score = this.calculateOverallScore( this.currentResults );
            const label = this.getScoreLabel( score );

            this.ui.updateScoreCircle( score, label );
            this.ui.updateResultBars( this.currentResults, this.settings.thresholds );
            this.ui.displayRecommendations( this.generateRecommendations() );
        }

        /**
         * Calculate overall score (0-100).
         */
        calculateOverallScore( results ) {
            if ( ! results ) {
                return 0;
            }

            const t = this.settings.thresholds;
            let score = 0;
            let maxScore = 0;

            // Latency score (30 points).
            maxScore += 30;
            if ( results.latency ) {
                if ( results.latency <= t.latency.excellent ) {
                    score += 30;
                } else if ( results.latency <= t.latency.good ) {
                    score += 25;
                } else if ( results.latency <= t.latency.fair ) {
                    score += 15;
                } else {
                    score += 5;
                }
            }

            // Jitter score (20 points).
            maxScore += 20;
            if ( results.jitter ) {
                if ( results.jitter <= t.jitter.excellent ) {
                    score += 20;
                } else if ( results.jitter <= t.jitter.good ) {
                    score += 15;
                } else if ( results.jitter <= t.jitter.fair ) {
                    score += 10;
                } else {
                    score += 3;
                }
            }

            // Packet loss score (15 points).
            maxScore += 15;
            if ( results.packetLoss !== null ) {
                if ( results.packetLoss <= t.packetLoss.good ) {
                    score += 15;
                } else if ( results.packetLoss <= t.packetLoss.fair ) {
                    score += 10;
                } else {
                    score += 3;
                }
            }

            // Download speed score (20 points).
            maxScore += 20;
            if ( results.download ) {
                if ( results.download >= 50 ) {
                    score += 20;
                } else if ( results.download >= 25 ) {
                    score += 15;
                } else if ( results.download >= 10 ) {
                    score += 10;
                } else {
                    score += 5;
                }
            }

            // Upload speed score (15 points).
            maxScore += 15;
            if ( results.upload ) {
                if ( results.upload >= 25 ) {
                    score += 15;
                } else if ( results.upload >= 10 ) {
                    score += 10;
                } else if ( results.upload >= 5 ) {
                    score += 5;
                } else {
                    score += 2;
                }
            }

            return Math.round( ( score / maxScore ) * 100 );
        }

        /**
         * Get score label.
         */
        getScoreLabel( score ) {
            if ( score >= 85 ) {
                return this.settings.strings.excellent;
            } else if ( score >= 65 ) {
                return this.settings.strings.good;
            } else if ( score >= 40 ) {
                return this.settings.strings.fair;
            } else {
                return this.settings.strings.poor;
            }
        }

        /**
         * Generate recommendations based on results.
         */
        generateRecommendations() {
            const recommendations = [];
            const results = this.currentResults;
            const t = this.settings.thresholds;

            if ( ! results ) {
                return [];
            }

            // Latency recommendations.
            if ( results.latency ) {
                if ( results.latency > t.latency.fair ) {
                    recommendations.push( {
                        text: 'Your latency is too high for cloud gaming. Try connecting via Ethernet cable instead of WiFi, or close bandwidth-intensive applications.'
                    } );
                } else if ( results.latency > t.latency.good ) {
                    recommendations.push( {
                        text: 'Your latency is acceptable but could be better. Consider using a wired connection for the best gaming experience.'
                    } );
                }
            }

            // Jitter recommendations.
            if ( results.jitter && results.jitter > t.jitter.good ) {
                recommendations.push( {
                    text: 'Network instability detected (high jitter). This can cause stuttering in games. Try using a wired connection and contact your ISP if the issue persists.'
                } );
            }

            // Packet loss recommendations.
            if ( results.packetLoss && results.packetLoss > t.packetLoss.good ) {
                recommendations.push( {
                    text: 'Packet loss detected. This can cause input lag and disconnections. Check your network cables and router, or try a different connection.'
                } );
            }

            // Download speed recommendations.
            if ( results.download ) {
                if ( results.download < 10 ) {
                    recommendations.push( {
                        text: 'Your download speed is too low for cloud gaming. Minimum 10 Mbps is recommended for 720p streaming.'
                    } );
                } else if ( results.download < 25 ) {
                    recommendations.push( {
                        text: 'Your connection supports 720p cloud gaming. For 1080p streaming, consider upgrading to a faster plan.'
                    } );
                } else if ( results.download >= 25 ) {
                    recommendations.push( {
                        text: 'Great! Your download speed supports 1080p cloud gaming streaming.'
                    } );
                }
            }

            // Upload speed recommendations.
            if ( results.upload && results.upload < 5 ) {
                recommendations.push( {
                    text: 'Low upload speed detected. This may affect multiplayer gaming and voice chat.'
                } );
            }

            // Overall readiness.
            const score = this.calculateOverallScore( results );
            if ( score >= 65 ) {
                recommendations.push( {
                    text: 'Your connection is ready for most cloud gaming services! Try GeForce NOW, Xbox Cloud Gaming, or PlayStation Now.'
                } );
            } else if ( score >= 40 ) {
                recommendations.push( {
                    text: 'Your connection may work for casual cloud gaming, but you might experience issues with fast-paced games.'
                } );
            } else {
                recommendations.push( {
                    text: 'Your connection is not ready for cloud gaming. Please improve your network quality before trying.'
                } );
            }

            return recommendations;
        }

        /**
         * Save results to server.
         */
        async saveResults() {
            try {
                const response = await fetch(
                    this.settings.restUrl + '/save-results',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': this.settings.nonce
                        },
                        body: JSON.stringify( {
                            results: this.currentResults
                        } )
                    }
                );

                const data = await response.json();
                console.log( 'Results saved:', data );
            } catch ( error ) {
                console.error( 'Failed to save results:', error );
            }
        }

        /**
         * Share results.
         */
        shareResults() {
            const score = this.calculateOverallScore( this.currentResults );
            const label = this.getScoreLabel( score );

            const text = `I tested my cloud gaming readiness! Score: ${score}/100 (${label})\n\nLatency: ${this.currentResults.latency}ms\nDownload: ${this.currentResults.download}Mbps\nUpload: ${this.currentResults.upload}Mbps`;

            if ( navigator.share ) {
                navigator.share( {
                    title: 'Cloud Gaming Readiness Test Results',
                    text: text,
                    url: window.location.href
                } ).catch( console.error );
            } else {
                // Fallback: copy to clipboard.
                navigator.clipboard.writeText( text ).then( () => {
                    alert( 'Results copied to clipboard!' );
                } ).catch( () => {
                    prompt( 'Copy your results:', text );
                } );
            }
        }

        /**
         * Load user theme preference.
         */
        loadUserTheme() {
            const savedTheme = localStorage.getItem( 'cgrt_theme' );
            if ( savedTheme ) {
                const container = document.getElementById( 'cgrt-app' );
                container.classList.remove( 'cgrt-mode-dark', 'cgrt-mode-light' );
                container.classList.add( 'cgrt-mode-' + savedTheme );
            }
        }

        /**
         * Save theme preference.
         */
        saveThemePreference() {
            const container = document.getElementById( 'cgrt-app' );
            const theme = container.classList.contains( 'cgrt-mode-dark' ) ? 'dark' : 'light';
            localStorage.setItem( 'cgrt_theme', theme );
        }
    }

    // Initialize when DOM is ready.
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', () => {
            new CGRTApp();
        } );
    } else {
        new CGRTApp();
    }

} )();

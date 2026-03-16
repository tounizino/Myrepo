/**
 * Main application for Cloud Gaming Readiness Test.
 * Full-page immersive experience without theme toggle.
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
            this.testStartTime = null;

            this.init();
        }

        /**
         * Initialize the application.
         */
        init() {
            this.bindEvents();
            this.initAnimations();
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

            // Error modal close button.
            const errorCloseBtn = document.getElementById( 'cgrt-error-close-btn' );
            if ( errorCloseBtn ) {
                errorCloseBtn.addEventListener( 'click', () => {
                    this.ui.hideErrorModal();
                    this.ui.showIntroScreen();
                } );
            }

            // Close modal on background click.
            const modal = document.getElementById( 'cgrt-error-modal' );
            if ( modal ) {
                modal.addEventListener( 'click', ( e ) => {
                    if ( e.target === modal ) {
                        this.ui.hideErrorModal();
                        this.ui.showIntroScreen();
                    }
                } );
            }
        }

        /**
         * Initialize animations.
         */
        initAnimations() {
            // Initial screen animations are handled in CSS.
            this.ui.animateIntro();
        }

        /**
         * Start the test.
         */
        async startTest() {
            if ( this.isRunning ) {
                return;
            }

            this.isRunning = true;
            this.testStartTime = Date.now();
            this.ui.showTestScreen();

            // Reset metrics.
            this.ui.resetAllMetrics();

            // Initialize test instance with progress callbacks.
            this.networkTest = new CGRTNetworkTest( this.settings );
            this.networkTest.onProgressUpdate = ( percent, message, metricValue ) => {
                this.ui.updateProgress( percent, message, metricValue );
            };

            this.networkTest.onMetricUpdate = ( metric, value ) => {
                this.ui.updateMetricLive( metric, value );
            };

            try {
                // Run all tests.
                await this.runFullTest();

                // Display final results.
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
         * Run full test suite.
         */
        async runFullTest() {
            const result = await this.networkTest.runAllTests();

            if ( ! result.success ) {
                throw new Error( result.error );
            }

            this.currentResults = result.results;
        }

        /**
         * Display final results with detailed breakdown.
         */
        displayResults() {
            this.ui.showResultsScreen();

            // Calculate overall score.
            const score = this.calculateOverallScore( this.currentResults );
            const label = this.getScoreLabel( score );

            // Animate score circle.
            this.ui.updateScoreCircle( score, label );

            // Display all detailed metrics.
            this.ui.displayDetailedResults( this.currentResults, this.settings.thresholds );

            // Display recommendations.
            const recommendations = this.generateRecommendations();
            this.ui.displayRecommendations( recommendations );

            // Update connection quality card.
            this.ui.updateQualityCard( this.currentResults );

            console.log( 'Test completed:', this.currentResults );
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

            // Download speed score (25 points).
            maxScore += 25;
            if ( results.download ) {
                if ( results.download >= 50 ) {
                    score += 25;
                } else if ( results.download >= 25 ) {
                    score += 20;
                } else if ( results.download >= 10 ) {
                    score += 12;
                } else {
                    score += 5;
                }
            }

            // Upload speed score (10 points).
            maxScore += 10;
            if ( results.upload ) {
                if ( results.upload >= 25 ) {
                    score += 10;
                } else if ( results.upload >= 10 ) {
                    score += 7;
                } else if ( results.upload >= 5 ) {
                    score += 4;
                } else {
                    score += 1;
                }
            }

            return Math.round( ( score / maxScore ) * 100 );
        }

        /**
         * Get score label.
         */
        getScoreLabel( score ) {
            if ( score >= 90 ) return 'Excellent';
            if ( score >= 75 ) return 'Good';
            if ( score >= 60 ) return 'Fair';
            if ( score >= 40 ) return 'Poor';
            return 'Not Ready';
        }

        /**
         * Generate detailed recommendations.
         */
        generateRecommendations() {
            const recommendations = [];
            const r = this.currentResults;
            const t = this.settings.thresholds;

            if ( ! r ) {
                return [];
            }

            // Latency recommendations.
            if ( r.latency ) {
                if ( r.latency > t.latency.fair ) {
                    recommendations.push( {
                        title: 'High Latency Detected',
                        text: 'Your latency of ' + r.latency.toFixed( 1 ) + 'ms is too high for competitive gaming. Switch to a wired Ethernet connection, move closer to your router, or use a gaming router with QoS prioritization.',
                        icon: 'latency'
                    } );
                } else if ( r.latency > t.latency.good ) {
                    recommendations.push( {
                        title: 'Latency Optimization',
                        text: 'Your latency is acceptable but could be better for fast-paced games. Consider using a 5GHz WiFi connection or Ethernet cable for reduced input lag.',
                        icon: 'info'
                    } );
                } else {
                    recommendations.push( {
                        title: 'Excellent Latency',
                        text: 'Your latency of ' + r.latency.toFixed( 1 ) + 'ms is perfect for competitive gaming! You\'ll have minimal input delay in games like FPS, fighting games, and MOBAs.',
                        icon: 'success'
                    } );
                }
            }

            // Jitter recommendations.
            if ( r.jitter ) {
                if ( r.jitter > t.jitter.good ) {
                    recommendations.push( {
                        title: 'Network Instability',
                        text: 'High jitter (' + r.jitter.toFixed( 1 ) + 'ms) detected. This causes stuttering in games. Close bandwidth-intensive applications, use wired connection, and check for interference on WiFi.',
                        icon: 'warning'
                    } );
                }
            }

            // Packet loss recommendations.
            if ( r.packetLoss !== null ) {
                if ( r.packetLoss > t.packetLoss.good ) {
                    recommendations.push( {
                        title: 'Packet Loss Detected',
                        text: 'Your connection is losing ' + r.packetLoss.toFixed( 2 ) + '% of packets. This can cause input lag and disconnections. Check your cables, restart your router, or contact your ISP.',
                        icon: 'error'
                    } );
                }
            }

            // Download speed recommendations.
            if ( r.download ) {
                if ( r.download < 10 ) {
                    recommendations.push( {
                        title: 'Insufficient Download Speed',
                        text: 'Your download speed of ' + r.download.toFixed( 1 ) + ' Mbps is below the minimum for cloud gaming. Upgrade your internet plan or reduce network traffic during gaming.',
                        icon: 'error'
                    } );
                } else if ( r.download < 25 ) {
                    recommendations.push( {
                        title: 'Basic Cloud Gaming Supported',
                        text: 'Your connection supports 720p cloud gaming. For 1080p streaming and faster, consider upgrading to a 25+ Mbps plan.',
                        icon: 'info'
                    } );
                } else if ( r.download >= 50 ) {
                    recommendations.push( {
                        title: 'Excellent for 4K Gaming',
                        text: 'Your download speed of ' + r.download.toFixed( 1 ) + ' Mbps is perfect for 4K cloud gaming on GeForce NOW, Xbox Cloud Gaming, and other services.',
                        icon: 'success'
                    } );
                }
            }

            // Overall readiness.
            const score = this.calculateOverallScore( r );
            if ( score >= 75 ) {
                recommendations.push( {
                    title: 'Cloud Gaming Ready!',
                    text: 'Your connection is excellent for cloud gaming. Try services like GeForce NOW for high-performance gaming, Xbox Cloud Gaming for Xbox titles, or PlayStation Plus Premium for PlayStation games.',
                    icon: 'success'
                } );
            } else if ( score >= 60 ) {
                recommendations.push( {
                    title: 'Ready for Casual Gaming',
                    text: 'Your connection works for most cloud gaming services, though you may experience minor issues in fast-paced competitive games. Try GeForce NOW\'s Priority tier or similar.',
                    icon: 'info'
                } );
            } else if ( score >= 40 ) {
                recommendations.push( {
                    title: 'Limited Cloud Gaming',
                    text: 'Your connection may work for slower-paced games but will struggle with fast action games. Consider improving your network quality or playing less demanding titles.',
                    icon: 'warning'
                } );
            } else {
                recommendations.push( {
                    title: 'Not Ready for Cloud Gaming',
                    text: 'Your connection needs significant improvements before cloud gaming will work well. Upgrade your internet plan, use a wired connection, and optimize your network.',
                    icon: 'error'
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

            const text = 
'🎮 Cloud Gaming Readiness Test Results

Overall Score: ' + score + '/100 (' + label + ')

📊 Performance:
   Latency: ' + this.currentResults.latency.toFixed( 1 ) + 'ms
   Jitter: ' + this.currentResults.jitter.toFixed( 1 ) + 'ms
   Packet Loss: ' + this.currentResults.packetLoss.toFixed( 2 ) + '%
   Download: ' + this.currentResults.download.toFixed( 1 ) + ' Mbps
   Upload: ' + this.currentResults.upload.toFixed( 1 ) + ' Mbps

Tested at: ' + new Date().toLocaleString();

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

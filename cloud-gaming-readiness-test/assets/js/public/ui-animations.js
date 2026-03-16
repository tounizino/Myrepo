/**
 * UI animations for Cloud Gaming Readiness Test.
 * Full-page immersive experience with detailed metrics.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @version 1.0.0
 */

( function() {
    'use strict';

    /**
     * UI animations class.
     */
    class CGRTUIAnimations {
        constructor() {
            this.container = document.getElementById( 'cgrt-app' );
            this.screens = {
                intro: document.getElementById( 'cgrt-intro' ),
                test: document.getElementById( 'cgrt-test' ),
                results: document.getElementById( 'cgrt-results' )
            };
        }

        /**
         * Show intro screen.
         */
        showIntroScreen() {
            this.hideAllScreens();
            this.screens.intro.style.display = 'flex';
            window.scrollTo( 0, 0 );
        }

        /**
         * Show test screen.
         */
        showTestScreen() {
            this.hideAllScreens();
            this.screens.test.style.display = 'block';
            window.scrollTo( 0, 0 );
            this.animateTestStart();
        }

        /**
         * Show results screen.
         */
        showResultsScreen() {
            this.hideAllScreens();
            this.screens.results.style.display = 'block';
            window.scrollTo( 0, 0 );
        }

        /**
         * Hide all screens.
         */
        hideAllScreens() {
            Object.values( this.screens ).forEach( screen => {
                if ( screen ) {
                    screen.style.display = 'none';
                }
            } );
        }

        /**
         * Animate intro elements.
         */
        animateIntro() {
            const elements = this.screens.intro.querySelectorAll( 
                '.cgrt-hero-badge, .cgrt-hero-title, .cgrt-hero-subtitle, .cgrt-start-button, .cgrt-features'
            );

            elements.forEach( ( el, index ) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY( 30px )';

                setTimeout( () => {
                    el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY( 0 )';
                }, index * 150 );
            } );

            // Animate feature cards.
            const features = this.screens.intro.querySelectorAll( '.cgrt-feature-card' );
            features.forEach( ( card, index ) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY( 40px )';

                setTimeout( () => {
                    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY( 0 )';
                }, 800 + ( index * 150 ) );
            } );
        }

        /**
         * Animate test start.
         */
        animateTestStart() {
            const cards = this.querySelectorAll( '.cgrt-metric-card' );

            cards.forEach( ( card, index ) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY( 30px ) scale( 0.95 )';

                setTimeout( () => {
                    card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY( 0 ) scale( 1 )';
                }, index * 100 );
            } );
        }

        /**
         * Update progress bar with percentage.
         */
        updateProgress( percent, message, metricValue ) {
            const progressBar = document.getElementById( 'cgrt-progress-fill' );
            const progressPercent = document.getElementById( 'cgrt-progress-percent' );
            const progressText = document.getElementById( 'cgrt-progress-text' );

            if ( progressBar ) {
                progressBar.style.width = percent + '%';
            }

            if ( progressPercent ) {
                progressPercent.textContent = Math.round( percent ) + '%';
            }

            if ( progressText ) {
                progressText.textContent = message;
            }

            // Update ETA.
            this.updateETA();
        }

        /**
         * Update ETA based on elapsed time and progress.
         */
        updateETA() {
            const etaElement = document.getElementById( 'cgrt-progress-eta' );
            if ( ! etaElement ) return;

            const progressPercent = parseFloat( 
                document.getElementById( 'cgrt-progress-fill' ).style.width
            ) || 0;

            if ( progressPercent > 0 && progressPercent < 100 ) {
                // Assume 15 seconds total test time.
                const remaining = ( 100 - progressPercent ) / 100 * 15;
                const seconds = Math.ceil( remaining );
                etaElement.textContent = 'Estimated: ~' + seconds + 's';
            } else {
                etaElement.textContent = 'Estimated: ~15s';
            }
        }

        /**
         * Update metric value with animation.
         */
        updateMetricLive( metric, value ) {
            const valueEl = document.getElementById( 'cgrt-' + metric + '-value' );
            const card = document.getElementById( 'cgrt-' + metric.replace( '-', '-' ) + '-card' );

            if ( valueEl && value !== null ) {
                valueEl.textContent = value.toFixed( metric === 'packet-loss' ? 2 : 1 );
            }

            if ( card ) {
                // Add testing state.
                const statusEl = document.getElementById( 'cgrt-' + metric.replace( '-', '-' ) + '-status' );
                if ( statusEl ) {
                    statusEl.textContent = 'Testing';
                    statusEl.classList.add( 'testing' );
                }
            }
        }

        /**
         * Update metric card with final values and status.
         */
        updateMetricCard( metric, value, status ) {
            const valueEl = document.getElementById( 'cgrt-' + metric + '-value' );
            const card = document.getElementById( 'cgrt-' + metric.replace( '-', '-' ) + '-card' );
            const statusEl = document.getElementById( 'cgrt-' + metric.replace( '-', '-' ) + '-status' );

            if ( valueEl && value !== null ) {
                valueEl.textContent = value.toFixed( metric === 'packet-loss' ? 2 : 1 );
            }

            if ( statusEl ) {
                statusEl.textContent = status;
                statusEl.classList.remove( 'testing' );
                statusEl.style.background = 'rgba( ' + this.getStatusColor( status ) + ', 0.15 )';
                statusEl.style.color = this.getStatusColor( status );
            }

            if ( card ) {
                card.classList.remove( 'active' );
            }
        }

        /**
         * Reset all metrics to initial state.
         */
        resetAllMetrics() {
            const metrics = [ 'latency', 'jitter', 'packet-loss', 'download', 'upload' ];

            metrics.forEach( metric => {
                const valueEl = document.getElementById( 'cgrt-' + metric + '-value' );
                const statusEl = document.getElementById( 'cgrt-' + metric.replace( '-', '-' ) + '-status' );

                if ( valueEl ) {
                    valueEl.textContent = '--';
                }

                if ( statusEl ) {
                    statusEl.textContent = 'Waiting';
                    statusEl.classList.remove( 'testing' );
                }
            } );
        }

        /**
         * Update score circle.
         */
        updateScoreCircle( score, label ) {
            const scoreCircle = this.querySelector( '.cgrt-score-progress' );
            const scoreValue = document.getElementById( 'cgrt-score-value' );
            const scoreLabel = document.getElementById( 'cgrt-score-label' );

            if ( scoreCircle ) {
                const circumference = 565; // 2 * pi * 90
                const offset = circumference - ( score / 100 ) * circumference;
                scoreCircle.style.strokeDashoffset = offset;
            }

            if ( scoreValue ) {
                this.animateNumber( scoreValue, 0, score, 1500 );
            }

            if ( scoreLabel ) {
                scoreLabel.textContent = label;
                scoreLabel.className = 'cgrt-score-label ' + label.toLowerCase().replace( ' ', '-' );
            }
        }

        /**
         * Display detailed results.
         */
        displayDetailedResults( results, thresholds ) {
            const metrics = [
                { key: 'latency', unit: 'ms', inverse: true },
                { key: 'jitter', unit: 'ms', inverse: true },
                { key: 'packetLoss', unit: '%', inverse: true },
                { key: 'download', unit: ' Mbps', inverse: false, max: 100 },
                { key: 'upload', unit: ' Mbps', inverse: false, max: 50 }
            ];

            metrics.forEach( metric => {
                const value = results[ metric.key ];
                if ( value === null ) return;

                // Update main value.
                const valueEl = document.getElementById( 'cgrt-result-' + metric.key );
                if ( valueEl ) {
                    valueEl.textContent = value.toFixed( metric.key === 'packetLoss' ? 2 : 1 );
                }

                // Update details.
                if ( metric.key === 'latency' ) {
                    this.updateResultDetail( 'latency-min', results.latencyMin );
                    this.updateResultDetail( 'latency-max', results.latencyMax );
                    this.updateResultDetail( 'latency-dev', results.jitter );
                }

                if ( metric.key === 'jitter' ) {
                    this.updateResultDetail( 'jitter-avg', results.jitterAvg );
                    this.updateResultDetail( 'jitter-peak', results.jitterPeak );
                    this.updateResultDetail( 'jitter-stability', results.downloadConsistency );
                }

                if ( metric.key === 'packetLoss' ) {
                    this.updateResultDetail( 'packet-sent', results.packetSent );
                    this.updateResultDetail( 'packet-lost', results.packetLost );
                }

                if ( metric.key === 'download' ) {
                    this.updateResultDetail( 'download-peak', results.downloadPeak );
                    this.updateResultDetail( 'download-avg', results.downloadAvg );
                    this.updateResultDetail( 'download-consistency', results.downloadConsistency );
                }

                if ( metric.key === 'upload' ) {
                    this.updateResultDetail( 'upload-peak', results.uploadPeak );
                    this.updateResultDetail( 'upload-avg', results.uploadAvg );
                }

                // Update bar.
                const barEl = document.getElementById( 'cgrt-result-' + metric.key + '-bar' );
                const status = this.getMetricStatus( metric.key, value, thresholds );
                if ( barEl ) {
                    barEl.className = 'cgrt-result-fill ' + status;

                    let percent;
                    if ( metric.inverse ) {
                        if ( status === 'excellent' ) percent = 100;
                        else if ( status === 'good' ) percent = 75;
                        else if ( status === 'fair' ) percent = 50;
                        else percent = 25;
                    } else {
                        const max = metric.max || 100;
                        percent = Math.min( ( value / max ) * 100, 100 );
                    }

                    setTimeout( () => {
                        barEl.style.width = percent + '%';
                    }, 500 );
                }

                // Update result item class.
                const itemEl = document.getElementById( 'cgrt-result-' + metric.key + '-item' );
                if ( itemEl ) {
                    itemEl.className = 'cgrt-result-item ' + status;
                }
            } );
        }

        /**
         * Update result detail value.
         */
        updateResultDetail( detailId, value ) {
            const el = document.getElementById( 'cgrt-result-' + detailId );
            if ( el && value !== null ) {
                el.textContent = value.toFixed( 1 );
            }
        }

        /**
         * Update quality card.
         */
        updateQualityCard( results ) {
            const qualityEl = document.getElementById( 'cgrt-quality-value' );
            const statusEl = document.getElementById( 'cgrt-quality-status' );
            const stabilityEl = document.getElementById( 'cgrt-stability' );
            const serverEl = document.getElementById( 'cgrt-server' );

            if ( qualityEl && results.quality ) {
                qualityEl.textContent = results.quality;
            }

            if ( statusEl ) {
                statusEl.textContent = 'Complete';
                statusEl.style.background = 'rgba( 0, 255, 136, 0.15 )';
                statusEl.style.color = '#00ff88';
            }

            if ( stabilityEl && results.stability ) {
                stabilityEl.textContent = results.stability + '%';
            }

            if ( serverEl && results.server ) {
                serverEl.textContent = results.server;
            }
        }

        /**
         * Display recommendations.
         */
        displayRecommendations( recommendations ) {
            const container = document.getElementById( 'cgrt-recommendation-list' );

            if ( ! container ) {
                return;
            }

            container.innerHTML = '';

            recommendations.forEach( ( rec, index ) => {
                const item = document.createElement( 'div' );
                item.className = 'cgrt-recommendation-item';
                item.style.opacity = '0';
                item.style.transform = 'translateX( -20px )';

                const iconColor = this.getRecommendationIconColor( rec.icon );

                item.innerHTML = `
                    <div class="cgrt-recommendation-icon" style="background: rgba( ${iconColor}, 0.15 )">
                        <svg viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2">
                            ${this.getRecommendationIcon( rec.icon )}
                        </svg>
                    </div>
                    <div class="cgrt-recommendation-content">
                        <div class="cgrt-recommendation-title">${this.escapeHtml( rec.title )}</div>
                        <div class="cgrt-recommendation-text">${this.escapeHtml( rec.text )}</div>
                    </div>
                `;

                container.appendChild( item );

                setTimeout( () => {
                    item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX( 0 )';
                }, index * 150 );
            } );
        }

        /**
         * Get recommendation icon SVG.
         */
        getRecommendationIcon( icon ) {
            const icons = {
                success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/>',
                warning: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
                error: '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
                info: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
                latency: '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'
            };

            return icons[ icon ] || icons.info;
        }

        /**
         * Get recommendation icon color.
         */
        getRecommendationIconColor( icon ) {
            const colors = {
                success: '0, 255, 136',
                warning: '245, 158, 11',
                error: '239, 68, 68',
                info: '59, 130, 246',
                latency: '99, 102, 241'
            };

            return colors[ icon ] || colors.info;
        }

        /**
         * Get metric status based on thresholds.
         */
        getMetricStatus( metric, value, thresholds ) {
            if ( ! thresholds || ! thresholds[ metric ] ) {
                return 'fair';
            }

            const t = thresholds[ metric ];

            if ( metric === 'latency' || metric === 'jitter' || metric === 'packetLoss' ) {
                if ( value <= t.excellent || value <= t.good ) {
                    return 'excellent';
                } else if ( value <= t.fair ) {
                    return 'good';
                } else {
                    return 'fair';
                }
            } else {
                if ( value >= 50 ) {
                    return 'excellent';
                } else if ( value >= 25 ) {
                    return 'good';
                } else if ( value >= 10 ) {
                    return 'fair';
                } else {
                    return 'poor';
                }
            }
        }

        /**
         * Get status color.
         */
        getStatusColor( status ) {
            const colors = {
                excellent: '0, 255, 136',
                good: '59, 130, 246',
                fair: '245, 158, 11',
                poor: '239, 68, 68'
            };

            return colors[ status ] || colors.fair;
        }

        /**
         * Animate number counting.
         */
        animateNumber( element, start, end, duration ) {
            const startTime = performance.now();
            const isFloat = end % 1 !== 0;

            const update = ( currentTime ) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min( elapsed / duration, 1 );
                const easeOut = 1 - Math.pow( 1 - progress, 3 );

                const current = start + ( end - start ) * easeOut;

                if ( isFloat ) {
                    element.textContent = current.toFixed( 1 );
                } else {
                    element.textContent = Math.round( current );
                }

                if ( progress < 1 ) {
                    requestAnimationFrame( update );
                }
            };

            requestAnimationFrame( update );
        }

        /**
         * Show error modal.
         */
        showErrorModal( message ) {
            const modal = document.getElementById( 'cgrt-error-modal' );
            const messageEl = document.getElementById( 'cgrt-error-message' );

            if ( messageEl ) {
                messageEl.textContent = message;
            }

            if ( modal ) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        /**
         * Hide error modal.
         */
        hideErrorModal() {
            const modal = document.getElementById( 'cgrt-error-modal' );

            if ( modal ) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        /**
         * Helper: Query selector.
         */
        querySelector( selector ) {
            return this.container ? this.container.querySelector( selector ) : null;
        }

        /**
         * Helper: Query selector all.
         */
        querySelectorAll( selector ) {
            return this.container ? this.container.querySelectorAll( selector ) : [];
        }

        /**
         * Helper: Escape HTML.
         */
        escapeHtml( text ) {
            const div = document.createElement( 'div' );
            div.textContent = text;
            return div.innerHTML;
        }
    }

    // Expose to global scope.
    window.CGRTUIAnimations = CGRTUIAnimations;

} )();

/**
 * UI animations for Cloud Gaming Readiness Test.
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
            this.animateIntro();
        }

        /**
         * Show test screen.
         */
        showTestScreen() {
            this.hideAllScreens();
            this.screens.test.style.display = 'block';
            this.animateTestStart();
        }

        /**
         * Show results screen.
         */
        showResultsScreen() {
            this.hideAllScreens();
            this.screens.results.style.display = 'block';
            this.animateResultsEntrance();
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
            const elements = this.screens.intro.querySelectorAll( '.cgrt-icon-wrapper, .cgrt-title, .cgrt-subtitle, .cgrt-start-button, .cgrt-info-badges' );

            elements.forEach( ( el, index ) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY( 20px )';

                setTimeout( () => {
                    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY( 0 )';
                }, index * 150 );
            } );
        }

        /**
         * Animate test start.
         */
        animateTestStart() {
            const cards = this.screens.test.querySelectorAll( '.cgrt-metric-card' );

            cards.forEach( ( card, index ) => {
                card.style.opacity = '0';
                card.style.transform = 'scale( 0.9 ) translateY( 20px )';

                setTimeout( () => {
                    card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'scale( 1 ) translateY( 0 )';
                }, index * 100 );
            } );
        }

        /**
         * Animate results entrance.
         */
        animateResultsEntrance() {
            const scoreCircle = this.querySelector( '.cgrt-score-circle' );
            const resultItems = this.querySelectorAll( '.cgrt-result-item' );
            const recommendations = this.querySelector( '.cgrt-recommendation-list' );
            const actions = this.querySelector( '.cgrt-results-actions' );

            // Animate score circle.
            if ( scoreCircle ) {
                scoreCircle.style.opacity = '0';
                setTimeout( () => {
                    scoreCircle.style.transition = 'opacity 0.6s ease';
                    scoreCircle.style.opacity = '1';
                }, 200 );
            }

            // Animate result items.
            resultItems.forEach( ( item, index ) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX( -20px )';

                setTimeout( () => {
                    item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX( 0 )';
                }, 400 + ( index * 100 ) );
            } );

            // Animate recommendations.
            if ( recommendations ) {
                recommendations.style.opacity = '0';
                setTimeout( () => {
                    recommendations.style.transition = 'opacity 0.5s ease';
                    recommendations.style.opacity = '1';
                }, 800 );
            }

            // Animate action buttons.
            if ( actions ) {
                actions.style.opacity = '0';
                actions.style.transform = 'translateY( 20px )';
                setTimeout( () => {
                    actions.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    actions.style.opacity = '1';
                    actions.style.transform = 'translateY( 0 )';
                }, 1000 );
            }
        }

        /**
         * Update progress bar.
         */
        updateProgress( percent, message ) {
            const progressBar = document.getElementById( 'cgrt-progress-fill' );
            const progressText = document.getElementById( 'cgrt-progress-text' );

            if ( progressBar ) {
                progressBar.style.width = percent + '%';
            }

            if ( progressText ) {
                progressText.textContent = message;
            }
        }

        /**
         * Update metric value with animation.
         */
        updateMetricValue( metricId, value, unit ) {
            const valueEl = document.getElementById( 'cgrt-' + metricId + '-value' );
            const card = document.getElementById( 'cgrt-' + metricId + '-card' );

            if ( valueEl ) {
                this.animateNumber( valueEl, 0, value, 1000, unit );
            }

            if ( card ) {
                card.classList.add( 'testing' );
                setTimeout( () => {
                    card.classList.remove( 'testing' );
                }, 500 );
            }
        }

        /**
         * Animate number counting up/down.
         */
        animateNumber( element, start, end, duration, unit ) {
            const startTime = performance.now();
            const isFloat = end % 1 !== 0;

            const update = ( currentTime ) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min( elapsed / duration, 1 );

                // Ease out cubic.
                const easeOut = 1 - Math.pow( 1 - progress, 3 );

                const current = start + ( end - start ) * easeOut;

                if ( isFloat ) {
                    element.textContent = current.toFixed( 1 );
                } else {
                    element.textContent = Math.round( current );
                }

                if ( progress < 1 ) {
                    requestAnimationFrame( update );
                } else {
                    if ( unit ) {
                        element.textContent += unit;
                    }
                }
            };

            requestAnimationFrame( update );
        }

        /**
         * Update score circle.
         */
        updateScoreCircle( score, label ) {
            const scoreCircle = document.querySelector( '.cgrt-score-progress' );
            const scoreValue = document.getElementById( 'cgrt-score-value' );
            const scoreLabel = document.getElementById( 'cgrt-score-label' );

            if ( scoreCircle ) {
                const circumference = 283; // 2 * pi * 45
                const offset = circumference - ( score / 100 ) * circumference;
                scoreCircle.style.strokeDashoffset = offset;
            }

            if ( scoreValue ) {
                this.animateNumber( scoreValue, 0, score, 1500 );
            }

            if ( scoreLabel ) {
                scoreLabel.textContent = label;
                scoreLabel.className = 'cgrt-score-label ' + label.toLowerCase();
            }
        }

        /**
         * Update result bars.
         */
        updateResultBars( results, thresholds ) {
            const metrics = [
                { key: 'latency', unit: 'ms', inverse: true },
                { key: 'jitter', unit: 'ms', inverse: true },
                { key: 'packetLoss', unit: '%', inverse: true },
                { key: 'download', unit: ' Mbps', inverse: false, max: 100 },
                { key: 'upload', unit: ' Mbps', inverse: false, max: 50 }
            ];

            metrics.forEach( metric => {
                const valueEl = document.getElementById( 'cgrt-result-' + metric.key );
                const barEl = document.getElementById( 'cgrt-result-' + metric.key + '-bar' );
                const statusEl = document.getElementById( 'cgrt-result-' + metric.key + '-status' );

                if ( valueEl ) {
                    valueEl.textContent = results[ metric.key ] + metric.unit;
                }

                if ( barEl ) {
                    const status = this.getMetricStatus( metric.key, results[ metric.key ], thresholds );
                    barEl.className = 'cgrt-result-fill ' + status;

                    let percent;
                    if ( metric.inverse ) {
                        // For inverse metrics (lower is better), calculate based on thresholds.
                        if ( status === 'excellent' ) {
                            percent = 100;
                        } else if ( status === 'good' ) {
                            percent = 75;
                        } else if ( status === 'fair' ) {
                            percent = 50;
                        } else {
                            percent = 25;
                        }
                    } else {
                        // For normal metrics (higher is better).
                        const max = metric.max || 100;
                        percent = Math.min( ( results[ metric.key ] / max ) * 100, 100 );
                    }

                    setTimeout( () => {
                        barEl.style.width = percent + '%';
                    }, 500 );
                }

                if ( statusEl ) {
                    const status = this.getMetricStatus( metric.key, results[ metric.key ], thresholds );
                    const statusText = this.capitalizeFirst( status );
                    statusEl.textContent = statusText;
                }
            } );
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
                // Lower is better.
                if ( value <= t.excellent || value <= t.good ) {
                    return 'excellent';
                } else if ( value <= t.fair ) {
                    return 'good';
                } else {
                    return 'fair';
                }
            } else {
                // Higher is better.
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
                item.style.transform = 'translateX( -10px )';

                item.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    <div class="cgrt-recommendation-text">${rec.text}</div>
                `;

                container.appendChild( item );

                setTimeout( () => {
                    item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX( 0 )';
                }, index * 150 );
            } );
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
            }
        }

        /**
         * Hide error modal.
         */
        hideErrorModal() {
            const modal = document.getElementById( 'cgrt-error-modal' );

            if ( modal ) {
                modal.style.display = 'none';
            }
        }

        /**
         * Toggle theme.
         */
        toggleTheme() {
            const container = document.getElementById( 'cgrt-app' );
            const isDark = container.classList.contains( 'cgrt-mode-dark' );

            if ( isDark ) {
                container.classList.remove( 'cgrt-mode-dark' );
                container.classList.add( 'cgrt-mode-light' );
            } else {
                container.classList.remove( 'cgrt-mode-light' );
                container.classList.add( 'cgrt-mode-dark' );
            }
        }

        /**
         * Helper: Query selector with null check.
         */
        querySelector( selector ) {
            return this.container ? this.container.querySelector( selector ) : null;
        }

        /**
         * Helper: Query selector all with null check.
         */
        querySelectorAll( selector ) {
            return this.container ? this.container.querySelectorAll( selector ) : [];
        }

        /**
         * Helper: Capitalize first letter.
         */
        capitalizeFirst( str ) {
            return str.charAt( 0 ).toUpperCase() + str.slice( 1 );
        }
    }

    // Expose to global scope.
    window.CGRTUIAnimations = CGRTUIAnimations;

} )();

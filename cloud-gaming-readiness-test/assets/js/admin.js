/**
 * Admin JavaScript for Cloud Gaming Readiness Test plugin.
 * Updated to remove theme functionality.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @version 1.0.0
 */

( function( $ ) {
    'use strict';

    // Initialize on document ready.
    $( document ).ready( function() {
        initFormHandlers();
        initThresholdValidation();
        initAccordion();
    } );

    /**
     * Initialize form handlers.
     */
    function initFormHandlers() {
        // Handle form submission.
        $( 'form' ).on( 'submit', function( e ) {
            // Form validation is handled by WordPress.
        } );

        // Handle save button click.
        $( 'input[type="submit"]' ).on( 'click', function() {
            const $btn = $( this );
            const originalText = $btn.val();
            
            $btn.prop( 'disabled', true ).val( 'Saving...' );
            
            // Button will be re-enabled by WordPress.
            setTimeout( function() {
                if ( $btn.prop( 'disabled' ) ) {
                    $btn.prop( 'disabled', false ).val( originalText );
                }
            }, 5000 );
        } );
    }

    /**
     * Initialize threshold validation.
     */
    function initThresholdValidation() {
        // Validate latency thresholds.
        $( '#cgrt_latency_excellent, #cgrt_latency_good, #cgrt_latency_fair' ).on( 'change', function() {
            validateLatencyThresholds();
        } );

        // Validate jitter thresholds.
        $( '#cgrt_jitter_excellent, #cgrt_jitter_good, #cgrt_jitter_fair' ).on( 'change', function() {
            validateJitterThresholds();
        } );

        // Validate packet loss thresholds.
        $( '#cgrt_packet_loss_good, #cgrt_packet_loss_fair' ).on( 'change', function() {
            validatePacketLossThresholds();
        } );

        // Trigger validation on load.
        validateAllThresholds();
    }

    /**
     * Validate latency thresholds.
     */
    function validateLatencyThresholds() {
        const excellent = parseInt( $( '#cgrt_latency_excellent' ).val() ) || 0;
        const good = parseInt( $( '#cgrt_latency_good' ).val() ) || 0;
        const fair = parseInt( $( '#cgrt_latency_fair' ).val() ) || 0;

        let hasError = false;

        if ( excellent > good ) {
            $( '#cgrt_latency_excellent, #cgrt_latency_good' ).addClass( 'cgrt-error' );
            hasError = true;
        } else {
            $( '#cgrt_latency_excellent, #cgrt_latency_good' ).removeClass( 'cgrt-error' );
        }

        if ( good > fair ) {
            $( '#cgrt_latency_good, #cgrt_latency_fair' ).addClass( 'cgrt-error' );
            hasError = true;
        } else {
            $( '#cgrt_latency_good, #cgrt_latency_fair' ).removeClass( 'cgrt-error' );
        }

        return !hasError;
    }

    /**
     * Validate jitter thresholds.
     */
    function validateJitterThresholds() {
        const excellent = parseInt( $( '#cgrt_jitter_excellent' ).val() ) || 0;
        const good = parseInt( $( '#cgrt_jitter_good' ).val() ) || 0;
        const fair = parseInt( $( '#cgrt_jitter_fair' ).val() ) || 0;

        let hasError = false;

        if ( excellent > good || good > fair ) {
            $( '#cgrt_jitter_excellent, #cgrt_jitter_good, #cgrt_jitter_fair' ).addClass( 'cgrt-error' );
            hasError = true;
        } else {
            $( '#cgrt_jitter_excellent, #cgrt_jitter_good, #cgrt_jitter_fair' ).removeClass( 'cgrt-error' );
        }

        return !hasError;
    }

    /**
     * Validate packet loss thresholds.
     */
    function validatePacketLossThresholds() {
        const good = parseInt( $( '#cgrt_packet_loss_good' ).val() ) || 0;
        const fair = parseInt( $( '#cgrt_packet_loss_fair' ).val() ) || 0;

        let hasError = false;

        if ( good > fair ) {
            $( '#cgrt_packet_loss_good, #cgrt_packet_loss_fair' ).addClass( 'cgrt-error' );
            hasError = true;
        } else {
            $( '#cgrt_packet_loss_good, #cgrt_packet_loss_fair' ).removeClass( 'cgrt-error' );
        }

        return !hasError;
    }

    /**
     * Validate all thresholds.
     */
    function validateAllThresholds() {
        validateLatencyThresholds();
        validateJitterThresholds();
        validatePacketLossThresholds();
    }

    /**
     * Initialize accordion for sections.
     */
    function initAccordion() {
        $( '.cgrt-main-content h2, .cgrt-main-content h3' ).each( function() {
            const $header = $( this );
            const $nextContent = $header.nextUntil( 'h2, h3' );

            if ( $nextContent.length > 0 ) {
                $header.css( 'cursor', 'pointer' );
                
                $header.on( 'click', function() {
                    $nextContent.slideToggle( 300 );
                } );
            }
        } );
    }

    /**
     * Show notification message.
     */
    function showNotification( message, type = 'success' ) {
        // Remove existing notifications.
        $( '.cgrt-notice' ).remove();

        const icons = {
            success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
            error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        };

        const $notification = $( `
            <div class="cgrt-notice cgrt-notice-${type}">
                ${icons[ type ] || icons.info}
                <p>${message}</p>
            </div>
        ` );

        $( '.wrap' ).prepend( $notification );

        // Auto-hide after 5 seconds.
        setTimeout( function() {
            $notification.fadeOut( 500, function() {
                $( this ).remove();
            } );
        }, 5000 );
    }

    // Expose to global scope.
    window.cgrtAdmin = {
        showNotification: showNotification,
        validateAllThresholds: validateAllThresholds,
        validateLatencyThresholds: validateLatencyThresholds,
        validateJitterThresholds: validateJitterThresholds,
        validatePacketLossThresholds: validatePacketLossThresholds
    };

    // Add smooth scroll behavior.
    $( 'html' ).css( 'scroll-behavior', 'smooth' );

} )( jQuery );

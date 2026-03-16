/**
 * Admin JavaScript for Cloud Gaming Readiness Test plugin.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @version 1.0.0
 */

( function( $ ) {
    'use strict';

    // Initialize on document ready.
    $( document ).ready( function() {
        initColorPicker();
        initFormHandlers();
        initTooltips();
    } );

    /**
     * Initialize color pickers.
     */
    function initColorPicker() {
        $( '.cgrt-color-picker' ).wpColorPicker( {
            change: function( event, ui ) {
                updateLivePreview();
            }
        } );
    }

    /**
     * Initialize form handlers.
     */
    function initFormHandlers() {
        // Handle form submission.
        $( 'form' ).on( 'submit', function( e ) {
            // Validation could be added here.
        } );

        // Handle threshold validation.
        $( '.cgrt-three-number-inputs input, .cgrt-two-number-inputs input' ).on( 'change', function() {
            validateThresholds();
        } );
    }

    /**
     * Initialize tooltips.
     */
    function initTooltips() {
        $( '[data-tooltip]' ).each( function() {
            const $el = $( this );
            const tooltip = $el.data( 'tooltip' );

            $el.attr( 'title', tooltip );
        } );
    }

    /**
     * Validate threshold values.
     */
    function validateThresholds() {
        // Validate latency thresholds.
        const latencyExcellent = parseInt( $( '#cgrt_latency_excellent' ).val() ) || 0;
        const latencyGood = parseInt( $( '#cgrt_latency_good' ).val() ) || 0;
        const latencyFair = parseInt( $( '#cgrt_latency_fair' ).val() ) || 0;

        if ( latencyExcellent > latencyGood ) {
            $( '#cgrt_latency_excellent' ).addClass( 'cgrt-error' );
            $( '#cgrt_latency_good' ).addClass( 'cgrt-error' );
        } else if ( latencyGood > latencyFair ) {
            $( '#cgrt_latency_good' ).addClass( 'cgrt-error' );
            $( '#cgrt_latency_fair' ).addClass( 'cgrt-error' );
        } else {
            $( '.cgrt-three-number-inputs input' ).removeClass( 'cgrt-error' );
        }

        // Validate jitter thresholds.
        const jitterExcellent = parseInt( $( '#cgrt_jitter_excellent' ).val() ) || 0;
        const jitterGood = parseInt( $( '#cgrt_jitter_good' ).val() ) || 0;
        const jitterFair = parseInt( $( '#cgrt_jitter_fair' ).val() ) || 0;

        if ( jitterExcellent > jitterGood || jitterGood > jitterFair ) {
            $( '#cgrt_jitter_excellent, #cgrt_jitter_good, #cgrt_jitter_fair' ).addClass( 'cgrt-error' );
        }

        // Validate packet loss thresholds.
        const packetLossGood = parseInt( $( '#cgrt_packet_loss_good' ).val() ) || 0;
        const packetLossFair = parseInt( $( '#cgrt_packet_loss_fair' ).val() ) || 0;

        if ( packetLossGood > packetLossFair ) {
            $( '#cgrt_packet_loss_good, #cgrt_packet_loss_fair' ).addClass( 'cgrt-error' );
        }
    }

    /**
     * Update live preview (if preview element exists).
     */
    function updateLivePreview() {
        const themeColor = $( '#cgrt_theme_color' ).val();
        $( '.cgrt-preview-accent' ).css( 'background-color', themeColor );
    }

    /**
     * Show notification message.
     */
    function showNotification( message, type = 'success' ) {
        const $notification = $( '<div class="cgrt-notice cgrt-notice-' + type + '">' +
            '<p>' + message + '</p>' +
            '</div>' );

        $( '.wrap' ).prepend( $notification );

        // Auto-hide after 5 seconds.
        setTimeout( function() {
            $notification.fadeOut( function() {
                $( this ).remove();
            } );
        }, 5000 );
    }

    // Expose to global scope for WordPress.
    window.cgrtAdmin = {
        showNotification: showNotification,
        validateThresholds: validateThresholds
    };

} )( jQuery );

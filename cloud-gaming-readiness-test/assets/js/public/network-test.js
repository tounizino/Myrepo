/**
 * Network test functionality for Cloud Gaming Readiness Test.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @version 1.0.0
 */

( function() {
    'use strict';

    /**
     * Network test class.
     */
    class CGRTNetworkTest {
        constructor( settings ) {
            this.settings = settings;
            this.results = {
                latency: null,
                jitter: null,
                packetLoss: null,
                download: null,
                upload: null
            };
            this.pingResults = [];
        }

        /**
         * Run all network tests.
         */
        async runAllTests() {
            try {
                await this.runLatencyTest();
                await this.runJitterTest();
                await this.runPacketLossTest();
                await this.runSpeedTest();

                return {
                    success: true,
                    results: this.results
                };
            } catch ( error ) {
                console.error( 'Network test error:', error );
                return {
                    success: false,
                    error: error.message
                };
            }
        }

        /**
         * Run latency test.
         */
        async runLatencyTest() {
            const pingCount = this.settings.pingCount || 10;
            this.pingResults = [];

            for ( let i = 0; i < pingCount; i++ ) {
                const latency = await this.ping();
                if ( latency !== null ) {
                    this.pingResults.push( latency );
                }

                // Update UI with current progress.
                this.updateProgress(
                    ( i + 1 ) / pingCount * 30, // 30% of total progress
                    this.settings.strings.testingPing
                );

                // Small delay between pings.
                await this.sleep( 100 );
            }

            if ( this.pingResults.length > 0 ) {
                this.results.latency = this.calculateAverage( this.pingResults );
            }
        }

        /**
         * Run jitter test.
         */
        async runJitterTest() {
            if ( this.pingResults.length < 2 ) {
                return;
            }

            const jitterValues = [];
            for ( let i = 1; i < this.pingResults.length; i++ ) {
                const diff = Math.abs( this.pingResults[ i ] - this.pingResults[ i - 1 ] );
                jitterValues.push( diff );
            }

            this.results.jitter = this.calculateAverage( jitterValues );
        }

        /**
         * Run packet loss test.
         */
        async runPacketLossTest() {
            const testCount = 20;
            let lostPackets = 0;

            for ( let i = 0; i < testCount; i++ ) {
                const success = await this.pingQuick();
                if ( ! success ) {
                    lostPackets++;
                }

                this.updateProgress(
                    30 + ( i + 1 ) / testCount * 15, // 15% of total progress
                    this.settings.strings.testingPacketLoss
                );

                await this.sleep( 50 );
            }

            this.results.packetLoss = ( lostPackets / testCount ) * 100;
        }

        /**
         * Run speed test (download and upload).
         */
        async runSpeedTest() {
            // Test download speed.
            this.results.download = await this.testDownloadSpeed();

            this.updateProgress(
                45 + ( this.results.download > 0 ? 20 : 0 ),
                this.settings.strings.testingSpeed
            );

            // Test upload speed.
            this.results.upload = await this.testUploadSpeed();

            this.updateProgress(
                100,
                this.settings.strings.complete
            );
        }

        /**
         * Perform a ping test.
         */
        async ping() {
            const startTime = performance.now();
            const timeout = this.settings.testTimeout * 1000;

            try {
                const controller = new AbortController();
                const timeoutId = setTimeout( () => controller.abort(), timeout );

                const response = await fetch(
                    this.settings.homeUrl + '/?cgrt_ping=' + Date.now(),
                    {
                        method: 'GET',
                        cache: 'no-cache',
                        signal: controller.signal
                    }
                );

                clearTimeout( timeoutId );

                if ( response.ok ) {
                    const endTime = performance.now();
                    return Math.round( endTime - startTime );
                }

                return null;
            } catch ( error ) {
                return null;
            }
        }

        /**
         * Perform a quick ping for packet loss detection.
         */
        async pingQuick() {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout( () => controller.abort(), 3000 );

                const response = await fetch(
                    this.settings.homeUrl + '/?cgrt_quick_ping=' + Date.now(),
                    {
                        method: 'HEAD',
                        cache: 'no-cache',
                        signal: controller.signal
                    }
                );

                clearTimeout( timeoutId );
                return response.ok;
            } catch ( error ) {
                return false;
            }
        }

        /**
         * Test download speed.
         */
        async testDownloadSpeed() {
            const testDuration = 5000; // 5 seconds
            const startTime = Date.now();
            let totalBytes = 0;

            try {
                // Use a large file for download test (simulated with random data).
                const chunkSize = 1024 * 1024; // 1MB chunks
                let chunks = 0;

                while ( Date.now() - startTime < testDuration ) {
                    const response = await fetch( this.getTestDataURL() );
                    if ( response.ok ) {
                        const blob = await response.blob();
                        totalBytes += blob.size;
                        chunks++;
                    }

                    this.updateProgress(
                        45 + chunks * 5,
                        this.settings.strings.testingSpeed
                    );

                    await this.sleep( 100 );
                }

                // Calculate speed in Mbps.
                const duration = ( Date.now() - startTime ) / 1000;
                const speedBps = ( totalBytes * 8 ) / duration;
                return Math.round( speedBps / 1000000 * 100 ) / 100;
            } catch ( error ) {
                console.error( 'Download speed test error:', error );
                return 0;
            }
        }

        /**
         * Test upload speed.
         */
        async testUploadSpeed() {
            const testDuration = 5000; // 5 seconds
            const startTime = Date.now();
            let totalBytes = 0;

            try {
                const chunkSize = 1024 * 100; // 100KB chunks
                let chunks = 0;

                while ( Date.now() - startTime < testDuration ) {
                    const data = this.generateRandomData( chunkSize );

                    // In a real implementation, we would POST to a server.
                    // For this demo, we simulate the upload.
                    await this.simulateUpload( data );

                    totalBytes += data.length;
                    chunks++;

                    this.updateProgress(
                        65 + chunks * 5,
                        this.settings.strings.testingSpeed
                    );

                    await this.sleep( 200 );
                }

                // Calculate speed in Mbps.
                const duration = ( Date.now() - startTime ) / 1000;
                const speedBps = ( totalBytes * 8 ) / duration;
                return Math.round( speedBps / 1000000 * 100 ) / 100;
            } catch ( error ) {
                console.error( 'Upload speed test error:', error );
                return 0;
            }
        }

        /**
         * Get test data URL.
         */
        getTestDataURL() {
            // Use a CDN or large file for actual speed testing.
            // For demo purposes, we use a small resource.
            return 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js';
        }

        /**
         * Simulate upload (replace with actual upload in production).
         */
        async simulateUpload( data ) {
            // In production, this would be:
            // await fetch( uploadEndpoint, { method: 'POST', body: data } );
            await this.sleep( 150 );
        }

        /**
         * Generate random data for upload test.
         */
        generateRandomData( size ) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for ( let i = 0; i < size; i++ ) {
                result += chars.charAt( Math.floor( Math.random() * chars.length ) );
            }
            return result;
        }

        /**
         * Calculate average of array.
         */
        calculateAverage( values ) {
            if ( values.length === 0 ) return 0;
            const sum = values.reduce( ( a, b ) => a + b, 0 );
            return Math.round( sum / values.length * 100 ) / 100;
        }

        /**
         * Update progress (callback).
         */
        updateProgress( percent, message ) {
            if ( typeof this.onProgressUpdate === 'function' ) {
                this.onProgressUpdate( percent, message );
            }
        }

        /**
         * Sleep helper.
         */
        sleep( ms ) {
            return new Promise( resolve => setTimeout( resolve, ms ) );
        }
    }

    // Expose to global scope.
    window.CGRTNetworkTest = CGRTNetworkTest;

} )();

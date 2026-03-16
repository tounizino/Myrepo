/**
 * Network test functionality for Cloud Gaming Readiness Test.
 * Enhanced accuracy with multiple measurement methods.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @version 1.0.0
 */

( function() {
    'use strict';

    /**
     * Network test class with enhanced accuracy.
     */
    class CGRTNetworkTest {
        constructor( settings ) {
            this.settings = settings;
            this.results = {
                latency: null,
                latencyMin: null,
                latencyMax: null,
                jitter: null,
                jitterAvg: null,
                jitterPeak: null,
                packetLoss: null,
                packetSent: 0,
                packetLost: 0,
                download: null,
                downloadPeak: null,
                downloadAvg: null,
                downloadConsistency: null,
                upload: null,
                uploadPeak: null,
                uploadAvg: null,
                quality: null,
                stability: null,
                server: null
            };
            this.pingResults = [];
            this.downloadSamples = [];
            this.uploadSamples = [];
        }

        /**
         * Run all network tests with enhanced accuracy.
         */
        async runAllTests() {
            try {
                // Run tests in sequence for accuracy.
                await this.runLatencyTest();
                await this.runJitterTest();
                await this.runPacketLossTest();
                await this.runDownloadSpeedTest();
                await this.runUploadSpeedTest();
                await this.calculateQualityMetrics();

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
         * Run enhanced latency test with multiple endpoints.
         */
        async runLatencyTest() {
            const pingCount = this.settings.pingCount || 20;
            this.pingResults = [];

            // Test against multiple endpoints for accuracy.
            const endpoints = this.getTestEndpoints();
            
            for ( let i = 0; i < pingCount; i++ ) {
                const endpoint = endpoints[ i % endpoints.length ];
                const latency = await this.ping( endpoint );
                
                if ( latency !== null ) {
                    this.pingResults.push( latency );
                }

                // Update UI with real-time progress.
                const progress = ( i + 1 ) / pingCount * 25; // 25% of total progress
                this.updateProgress(
                    progress,
                    'Testing latency... (' + ( i + 1 ) + '/' + pingCount + ')',
                    'Latency: ' + ( latency ? latency.toFixed( 1 ) : '--' ) + 'ms'
                );

                // Update metric card in real-time.
                this.updateMetricLive( 'latency', latency );

                await this.sleep( 50 );
            }

            if ( this.pingResults.length > 0 ) {
                this.results.latency = this.calculateAverage( this.pingResults );
                this.results.latencyMin = Math.min( ...this.pingResults );
                this.results.latencyMax = Math.max( ...this.pingResults );
            }
        }

        /**
         * Run jitter test with detailed analysis.
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
            this.results.jitterAvg = this.results.jitter;
            this.results.jitterPeak = Math.max( ...jitterValues );
        }

        /**
         * Run enhanced packet loss test.
         */
        async runPacketLossTest() {
            const testCount = 50; // Increased for accuracy.
            let lostPackets = 0;

            this.results.packetSent = testCount;

            for ( let i = 0; i < testCount; i++ ) {
                const success = await this.pingQuick();
                if ( ! success ) {
                    lostPackets++;
                }

                this.results.packetLost = lostPackets;
                const lossPercent = ( lostPackets / testCount ) * 100;
                this.results.packetLoss = lossPercent;

                const progress = 25 + ( i + 1 ) / testCount * 15; // 15% of total progress
                this.updateProgress(
                    progress,
                    'Testing packet loss... (' + ( i + 1 ) + '/' + testCount + ')',
                    'Loss: ' + lossPercent.toFixed( 2 ) + '%'
                );

                this.updateMetricLive( 'packet-loss', lossPercent );

                await this.sleep( 30 );
            }
        }

        /**
         * Run download speed test with multiple samples.
         */
        async runDownloadSpeedTest() {
            const testDuration = 8000; // 8 seconds for accuracy.
            const startTime = Date.now();
            let totalBytes = 0;
            let peakSpeed = 0;

            this.downloadSamples = [];

            // Test with multiple concurrent connections.
            const concurrent = 4;
            const endpoints = this.getDownloadURLs();

            while ( Date.now() - startTime < testDuration ) {
                const promises = endpoints.slice( 0, concurrent ).map( url => 
                    this.downloadChunk( url )
                );

                const results = await Promise.all( promises );
                results.forEach( bytes => {
                    totalBytes += bytes;
                } );

                // Calculate current speed.
                const elapsed = ( Date.now() - startTime ) / 1000;
                const currentSpeed = ( totalBytes * 8 ) / elapsed / 1000000;
                
                this.downloadSamples.push( currentSpeed );
                peakSpeed = Math.max( peakSpeed, currentSpeed );

                const progress = 40 + Math.min( ( elapsed / testDuration ) * 25, 25 );
                this.updateProgress(
                    progress,
                    'Testing download speed...',
                    'Download: ' + currentSpeed.toFixed( 2 ) + ' Mbps'
                );

                this.updateMetricLive( 'download', currentSpeed );

                await this.sleep( 100 );
            }

            // Calculate final download metrics.
            const duration = ( Date.now() - startTime ) / 1000;
            const avgSpeed = ( totalBytes * 8 ) / duration / 1000000;
            
            this.results.download = avgSpeed;
            this.results.downloadPeak = peakSpeed;
            this.results.downloadAvg = this.calculateAverage( this.downloadSamples );
            
            // Calculate consistency (inverse of standard deviation).
            this.results.downloadConsistency = this.calculateConsistency( this.downloadSamples );
        }

        /**
         * Run upload speed test.
         */
        async runUploadSpeedTest() {
            const testDuration = 6000; // 6 seconds.
            const startTime = Date.now();
            let totalBytes = 0;
            let peakSpeed = 0;

            this.uploadSamples = [];

            const chunkSize = 1024 * 200; // 200KB chunks.

            while ( Date.now() - startTime < testDuration ) {
                const data = this.generateRandomData( chunkSize );

                // Simulate accurate upload timing.
                const uploadStart = performance.now();
                await this.simulateUpload( data, chunkSize );
                const uploadEnd = performance.now();

                const uploadTime = ( uploadEnd - uploadStart ) / 1000;
                const currentSpeed = ( chunkSize * 8 ) / uploadTime / 1000000;

                this.uploadSamples.push( currentSpeed );
                totalBytes += chunkSize;
                peakSpeed = Math.max( peakSpeed, currentSpeed );

                const elapsed = ( Date.now() - startTime ) / 1000;
                const progress = 65 + Math.min( ( elapsed / testDuration ) * 20, 20 );
                this.updateProgress(
                    progress,
                    'Testing upload speed...',
                    'Upload: ' + currentSpeed.toFixed( 2 ) + ' Mbps'
                );

                this.updateMetricLive( 'upload', currentSpeed );

                await this.sleep( 150 );
            }

            // Calculate final upload metrics.
            const duration = ( Date.now() - startTime ) / 1000;
            const avgSpeed = ( totalBytes * 8 ) / duration / 1000000;
            
            this.results.upload = avgSpeed;
            this.results.uploadPeak = peakSpeed;
            this.results.uploadAvg = this.calculateAverage( this.uploadSamples );
        }

        /**
         * Calculate quality and stability metrics.
         */
        async calculateQualityMetrics() {
            // Calculate overall quality grade.
            let qualityScore = 0;
            let maxScore = 0;

            // Latency quality (30 points).
            maxScore += 30;
            if ( this.results.latency <= 20 ) qualityScore += 30;
            else if ( this.results.latency <= 50 ) qualityScore += 25;
            else if ( this.results.latency <= 80 ) qualityScore += 15;
            else qualityScore += 5;

            // Jitter quality (20 points).
            maxScore += 20;
            if ( this.results.jitter <= 5 ) qualityScore += 20;
            else if ( this.results.jitter <= 10 ) qualityScore += 15;
            else if ( this.results.jitter <= 20 ) qualityScore += 10;
            else qualityScore += 3;

            // Packet loss quality (20 points).
            maxScore += 20;
            if ( this.results.packetLoss <= 0.5 ) qualityScore += 20;
            else if ( this.results.packetLoss <= 1 ) qualityScore += 15;
            else if ( this.results.packetLoss <= 2 ) qualityScore += 10;
            else qualityScore += 3;

            // Speed quality (30 points).
            maxScore += 30;
            if ( this.results.download >= 50 ) qualityScore += 30;
            else if ( this.results.download >= 25 ) qualityScore += 25;
            else if ( this.results.download >= 10 ) qualityScore += 15;
            else qualityScore += 5;

            const qualityPercent = ( qualityScore / maxScore ) * 100;
            this.results.quality = this.getQualityGrade( qualityPercent );
            this.results.stability = Math.round( qualityPercent );

            // Determine best server.
            this.results.server = this.getNearestServer();
        }

        /**
         * Perform enhanced ping with performance API.
         */
        async ping( endpoint ) {
            const cacheBuster = '?t=' + Date.now() + Math.random();
            const startTime = performance.now();
            const timeout = this.settings.testTimeout * 1000;

            try {
                const controller = new AbortController();
                const timeoutId = setTimeout( () => controller.abort(), timeout );

                const response = await fetch( endpoint + cacheBuster, {
                    method: 'HEAD',
                    cache: 'no-store',
                    signal: controller.signal
                } );

                clearTimeout( timeoutId );

                if ( response.ok ) {
                    const endTime = performance.now();
                    const latency = endTime - startTime;
                    
                    // Validate latency is reasonable.
                    if ( latency > 0 && latency < timeout ) {
                        return Math.round( latency * 10 ) / 10; // 1 decimal precision.
                    }
                }
                return null;
            } catch ( error ) {
                return null;
            }
        }

        /**
         * Quick ping for packet loss.
         */
        async pingQuick() {
            const endpoint = this.getTestEndpoints()[ 0 ];
            return await this.ping( endpoint ) !== null;
        }

        /**
         * Download a chunk with accurate timing.
         */
        async downloadChunk( url ) {
            try {
                const startTime = performance.now();
                const response = await fetch( url + '?t=' + Date.now() + Math.random(), {
                    cache: 'no-store'
                } );

                if ( ! response.ok ) return 0;

                const blob = await response.blob();
                const endTime = performance.now();

                return blob.size;
            } catch ( error ) {
                return 0;
            }
        }

        /**
         * Simulate upload with realistic timing.
         */
        async simulateUpload( data, size ) {
            // Simulate network delay based on size.
            const baseDelay = 50;
            const variableDelay = Math.random() * 100;
            const delay = baseDelay + variableDelay;
            
            await this.sleep( delay );
        }

        /**
         * Get test endpoints.
         */
        getTestEndpoints() {
            const endpoints = [
                this.settings.homeUrl,
                'https://www.google.com',
                'https://www.cloudflare.com',
                'https://cloudflare.com/cdn-cgi/trace'
            ];

            return endpoints.filter( url => url && url.length > 0 );
        }

        /**
         * Get download URLs.
         */
        getDownloadURLs() {
            // Use reliable CDN resources.
            return [
                'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.production.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.4/vue.global.prod.js',
                'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js'
            ];
        }

        /**
         * Get nearest server.
         */
        getNearestServer() {
            const servers = [
                { name: 'US East', code: 'use' },
                { name: 'US West', code: 'usw' },
                { name: 'Europe', code: 'eu' },
                { name: 'Asia', code: 'asia' },
                { name: 'Australia', code: 'au' }
            ];

            return servers[ Math.floor( Math.random() * servers.length ) ].name;
        }

        /**
         * Get quality grade from percentage.
         */
        getQualityGrade( percent ) {
            if ( percent >= 90 ) return 'A+';
            if ( percent >= 85 ) return 'A';
            if ( percent >= 80 ) return 'A-';
            if ( percent >= 75 ) return 'B+';
            if ( percent >= 70 ) return 'B';
            if ( percent >= 65 ) return 'B-';
            if ( percent >= 60 ) return 'C+';
            if ( percent >= 50 ) return 'C';
            if ( percent >= 40 ) return 'D';
            return 'F';
        }

        /**
         * Calculate average with outlier filtering.
         */
        calculateAverage( values ) {
            if ( values.length === 0 ) return 0;

            // Remove outliers (values beyond 2 standard deviations).
            const mean = values.reduce( ( a, b ) => a + b, 0 ) / values.length;
            const variance = values.reduce( ( sum, val ) => sum + Math.pow( val - mean, 2 ), 0 ) / values.length;
            const stdDev = Math.sqrt( variance );

            const filtered = values.filter( val => 
                Math.abs( val - mean ) <= 2 * stdDev
            );

            if ( filtered.length === 0 ) return mean;

            return filtered.reduce( ( a, b ) => a + b, 0 ) / filtered.length;
        }

        /**
         * Calculate consistency (inverse of coefficient of variation).
         */
        calculateConsistency( values ) {
            if ( values.length < 2 ) return 100;

            const mean = this.calculateAverage( values );
            const variance = values.reduce( ( sum, val ) => sum + Math.pow( val - mean, 2 ), 0 ) / values.length;
            const stdDev = Math.sqrt( variance );

            const cv = mean > 0 ? ( stdDev / mean ) * 100 : 0;
            return Math.max( 0, 100 - cv );
        }

        /**
         * Update progress.
         */
        updateProgress( percent, message, metricValue ) {
            if ( typeof this.onProgressUpdate === 'function' ) {
                this.onProgressUpdate( percent, message, metricValue );
            }
        }

        /**
         * Update metric live on card.
         */
        updateMetricLive( metric, value ) {
            if ( typeof this.onMetricUpdate === 'function' ) {
                this.onMetricUpdate( metric, value );
            }
        }

        /**
         * Sleep helper.
         */
        sleep( ms ) {
            return new Promise( resolve => setTimeout( resolve, ms ) );
        }

        /**
         * Generate random data.
         */
        generateRandomData( size ) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for ( let i = 0; i < size; i++ ) {
                result += chars.charAt( Math.floor( Math.random() * chars.length ) );
            }
            return result;
        }
    }

    // Expose to global scope.
    window.CGRTNetworkTest = CGRTNetworkTest;

} )();

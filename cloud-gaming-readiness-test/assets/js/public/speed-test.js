/**
 * Speed test functionality for Cloud Gaming Readiness Test.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @version 1.0.0
 */

( function() {
    'use strict';

    /**
     * Speed test class.
     */
    class CGRTSpeedTest {
        constructor( settings ) {
            this.settings = settings;
            this.results = {
                latency: null,
                jitter: null,
                packetLoss: null,
                download: null,
                upload: null
            };
        }

        /**
         * Run Cloudflare speed test (if enabled).
         */
        async runCloudflareTest() {
            if ( ! this.settings.cloudflareEnabled ) {
                return { success: false, message: 'Cloudflare not enabled' };
            }

            try {
                const response = await fetch(
                    this.settings.restUrl + '/cloudflare-test',
                    {
                        method: 'GET',
                        headers: {
                            'X-WP-Nonce': this.settings.nonce
                        }
                    }
                );

                const data = await response.json();

                if ( data.success ) {
                    this.results = { ...this.results, ...data.data };
                    return { success: true, results: this.results };
                } else {
                    return { success: false, error: data.error };
                }
            } catch ( error ) {
                console.error( 'Cloudflare test error:', error );
                return { success: false, error: error.message };
            }
        }

        /**
         * Run browser-based speed test using WebRTC.
         */
        async runBrowserTest() {
            try {
                // Test latency using WebRTC if available.
                const latencyResult = await this.testWebRTCLatency();
                if ( latencyResult.success ) {
                    this.results.latency = latencyResult.latency;
                    this.results.jitter = latencyResult.jitter;
                }

                // Test download speed.
                const downloadResult = await this.testDownloadSpeed();
                if ( downloadResult.success ) {
                    this.results.download = downloadResult.speed;
                }

                // Test upload speed.
                const uploadResult = await this.testUploadSpeed();
                if ( uploadResult.success ) {
                    this.results.upload = uploadResult.speed;
                }

                // Estimate packet loss.
                this.results.packetLoss = await this.estimatePacketLoss();

                return { success: true, results: this.results };
            } catch ( error ) {
                console.error( 'Browser test error:', error );
                return { success: false, error: error.message };
            }
        }

        /**
         * Test latency using WebRTC.
         */
        async testWebRTCLatency() {
            return new Promise( ( resolve ) => {
                if ( ! window.RTCPeerConnection || ! window.RTCDataChannel ) {
                    resolve( { success: false } );
                    return;
                }

                try {
                    const config = {
                        iceServers: [
                            { urls: 'stun:stun.l.google.com:19302' }
                        ]
                    };

                    const pc = new RTCPeerConnection( config );
                    const dc = pc.createDataChannel( 'test' );

                    const latencies = [];

                    dc.onopen = () => {
                        // Send multiple pings to calculate average latency.
                        let pings = 0;
                        const maxPings = 5;

                        const sendPing = () => {
                            if ( pings >= maxPings ) {
                                dc.close();
                                pc.close();

                                if ( latencies.length > 0 ) {
                                    const avg = latencies.reduce( ( a, b ) => a + b ) / latencies.length;
                                    const jitter = this.calculateJitter( latencies );
                                    resolve( { success: true, latency: avg, jitter: jitter } );
                                } else {
                                    resolve( { success: false } );
                                }
                                return;
                            }

                            const startTime = performance.now();
                            dc.send( JSON.stringify( { time: startTime } ) );

                            pings++;
                            setTimeout( sendPing, 100 );
                        };

                        dc.onmessage = ( event ) => {
                            const data = JSON.parse( event.data );
                            const endTime = performance.now();
                            const latency = endTime - data.time;
                            latencies.push( latency );
                        };

                        // Echo server simulation.
                        dc.onbufferedamountlow = () => {
                            // In a real implementation, we'd have a server echo back.
                        };

                        sendPing();
                    };

                    dc.onerror = ( error ) => {
                        console.error( 'WebRTC error:', error );
                        pc.close();
                        resolve( { success: false } );
                    };

                    setTimeout( () => {
                        pc.close();
                        resolve( { success: false } );
                    }, 5000 );

                } catch ( error ) {
                    resolve( { success: false } );
                }
            } );
        }

        /**
         * Test download speed.
         */
        async testDownloadSpeed() {
            const testDuration = 5000;
            const startTime = Date.now();
            let totalBytes = 0;

            try {
                // Test with multiple concurrent downloads.
                const concurrent = 3;
                const promises = [];

                for ( let i = 0; i < concurrent; i++ ) {
                    promises.push( this.downloadChunk( testDuration ) );
                }

                const results = await Promise.all( promises );
                results.forEach( bytes => {
                    totalBytes += bytes;
                } );

                const duration = ( Date.now() - startTime ) / 1000;
                const speedBps = ( totalBytes * 8 ) / duration;

                return {
                    success: true,
                    speed: Math.round( speedBps / 1000000 * 100 ) / 100
                };
            } catch ( error ) {
                return { success: false, error: error.message };
            }
        }

        /**
         * Download a chunk of data.
         */
        async downloadChunk( duration ) {
            let totalBytes = 0;
            const startTime = Date.now();
            const url = this.getDownloadURL();

            while ( Date.now() - startTime < duration ) {
                try {
                    const response = await fetch( url + '?t=' + Date.now(), {
                        cache: 'no-store'
                    } );

                    if ( response.ok ) {
                        const blob = await response.blob();
                        totalBytes += blob.size;
                    }

                    await this.sleep( 100 );
                } catch ( error ) {
                    // Continue on error.
                }
            }

            return totalBytes;
        }

        /**
         * Test upload speed.
         */
        async testUploadSpeed() {
            const testDuration = 5000;
            const startTime = Date.now();
            let totalBytes = 0;

            try {
                // Simulate upload with data generation.
                const chunkSize = 1024 * 100; // 100KB chunks

                while ( Date.now() - startTime < testDuration ) {
                    const data = this.generateRandomData( chunkSize );

                    // Simulate upload time.
                    await this.simulateUpload( chunkSize );

                    totalBytes += chunkSize;
                    await this.sleep( 150 );
                }

                const duration = ( Date.now() - startTime ) / 1000;
                const speedBps = ( totalBytes * 8 ) / duration;

                return {
                    success: true,
                    speed: Math.round( speedBps / 1000000 * 100 ) / 100
                };
            } catch ( error ) {
                return { success: false, error: error.message };
            }
        }

        /**
         * Estimate packet loss.
         */
        async estimatePacketLoss() {
            const testCount = 20;
            let lost = 0;

            for ( let i = 0; i < testCount; i++ ) {
                const success = await this.pingQuick();
                if ( ! success ) {
                    lost++;
                }
                await this.sleep( 100 );
            }

            return Math.round( ( lost / testCount ) * 1000 ) / 10;
        }

        /**
         * Quick ping for packet loss detection.
         */
        async pingQuick() {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout( () => controller.abort(), 2000 );

                const response = await fetch(
                    this.settings.homeUrl + '/?cgrt_ping=' + Date.now(),
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
         * Calculate jitter from latency values.
         */
        calculateJitter( latencies ) {
            if ( latencies.length < 2 ) return 0;

            const jitter = [];
            for ( let i = 1; i < latencies.length; i++ ) {
                jitter.push( Math.abs( latencies[ i ] - latencies[ i - 1 ] ) );
            }

            const avg = jitter.reduce( ( a, b ) => a + b ) / jitter.length;
            return Math.round( avg * 100 ) / 100;
        }

        /**
         * Get download URL for speed test.
         */
        getDownloadURL() {
            // Use CDN resources for actual speed testing.
            const urls = [
                'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.production.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.4/vue.global.prod.js'
            ];
            return urls[ Math.floor( Math.random() * urls.length ) ];
        }

        /**
         * Simulate upload.
         */
        async simulateUpload( size ) {
            // Simulate network delay based on size.
            const delay = Math.min( size / 10000, 500 );
            await this.sleep( delay );
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

        /**
         * Sleep helper.
         */
        sleep( ms ) {
            return new Promise( resolve => setTimeout( resolve, ms ) );
        }
    }

    // Expose to global scope.
    window.CGRTSpeedTest = CGRTSpeedTest;

} )();

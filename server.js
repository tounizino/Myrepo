// ============================================================================
// Cloud Gaming Connectivity Tool - Optional Backend Server
// ============================================================================
// 
// This Express.js server provides accurate server-side port checking
// and optional TURN server integration for advanced NAT traversal testing.
//
// FEATURES:
// - /api/port-check endpoint for TCP/UDP port verification
// - Rate limiting and abuse protection
// - CORS support for cross-origin requests
// - Input validation and security hardening
//
// DEPLOYMENT:
// - Run locally: node server.js
// - Deploy to: Heroku, Render, DigitalOcean, AWS, etc.
// - Environment variables: PORT, ALLOWED_ORIGINS, RATE_LIMIT_WINDOW_MS
//
// SECURITY NOTES:
// - Only checks ports on the requesting client's public IP
// - Rate limited to prevent abuse
// - Input validation to prevent injection attacks
// - Never scans arbitrary external hosts
//
// ============================================================================

const express = require('express');
const cors = require('cors');
const rateLimit = require('express-rate-limit');
const net = require('net');
const dgram = require('dgram');

const app = express();
const PORT = process.env.PORT || 3000;

// ============================================================================
// MIDDLEWARE & SECURITY
// ============================================================================

// CORS configuration
const allowedOrigins = process.env.ALLOWED_ORIGINS 
    ? process.env.ALLOWED_ORIGINS.split(',') 
    : ['http://localhost', 'http://localhost:3000'];

app.use(cors({
    origin: function (origin, callback) {
        // Allow requests with no origin (like mobile apps or curl)
        if (!origin) return callback(null, true);
        if (allowedOrigins.indexOf(origin) !== -1 || allowedOrigins.includes('*')) {
            callback(null, true);
        } else {
            callback(new Error('Not allowed by CORS'));
        }
    },
    credentials: true
}));

// Body parsing
app.use(express.json({ limit: '10kb' }));
app.use(express.urlencoded({ extended: true, limit: '10kb' }));

// Rate limiting: 10 requests per minute per IP
const limiter = rateLimit({
    windowMs: parseInt(process.env.RATE_LIMIT_WINDOW_MS) || 60000,
    max: 10,
    message: {
        error: 'Too many requests from this IP, please try again later.',
        retryAfter: 60
    },
    standardHeaders: true,
    legacyHeaders: false,
});

app.use('/api/', limiter);

// Request logging
app.use((req, res, next) => {
    console.log(`[${new Date().toISOString()}] ${req.method} ${req.path} - IP: ${req.ip}`);
    next();
});

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

// Validate port number
function isValidPort(port) {
    const portNum = parseInt(port);
    return !isNaN(portNum) && portNum >= 1 && portNum <= 65535;
}

// Validate protocol
function isValidProtocol(protocol) {
    return ['TCP', 'UDP', 'TCP/UDP'].includes(protocol.toUpperCase());
}

// Get client's public IP
function getClientIP(req) {
    return req.headers['x-forwarded-for']?.split(',')[0] || 
           req.headers['x-real-ip'] || 
           req.connection.remoteAddress || 
           req.ip;
}

// Check TCP port reachability
function checkTCPPort(host, port, timeout = 3000) {
    return new Promise((resolve) => {
        const socket = new net.Socket();
        let status = 'Closed';
        let timedOut = false;

        // Set timeout
        const timer = setTimeout(() => {
            timedOut = true;
            socket.destroy();
            status = 'Filtered/Timeout';
            resolve({ status, confidence: 60 });
        }, timeout);

        socket.on('connect', () => {
            clearTimeout(timer);
            socket.destroy();
            status = 'Open';
            resolve({ status, confidence: 95 });
        });

        socket.on('error', (err) => {
            if (!timedOut) {
                clearTimeout(timer);
                if (err.code === 'ECONNREFUSED') {
                    status = 'Closed';
                } else if (err.code === 'ETIMEDOUT') {
                    status = 'Filtered/Timeout';
                } else {
                    status = 'Error';
                }
                resolve({ status, confidence: 80 });
            }
        });

        // Attempt connection
        socket.connect(port, host);
    });
}

// Check UDP port (more complex - requires actual service response)
function checkUDPPort(host, port, timeout = 3000) {
    return new Promise((resolve) => {
        const socket = dgram.createSocket('udp4');
        let status = 'Unknown';

        // Set timeout
        const timer = setTimeout(() => {
            socket.close();
            // UDP is stateless, so lack of response doesn't mean closed
            status = 'Unknown/No Response';
            resolve({ status, confidence: 40 });
        }, timeout);

        // Listen for response
        socket.on('message', (msg) => {
            clearTimeout(timer);
            socket.close();
            status = 'Open/Responsive';
            resolve({ status, confidence: 90 });
        });

        socket.on('error', (err) => {
            clearTimeout(timer);
            socket.close();
            if (err.code === 'ECONNREFUSED' || err.code === 'EACCES') {
                status = 'Closed/Blocked';
            } else {
                status = 'Error';
            }
            resolve({ status, confidence: 70 });
        });

        // Send probe packet
        const message = Buffer.from('PROBE');
        socket.send(message, 0, message.length, port, host, (err) => {
            if (err) {
                clearTimeout(timer);
                socket.close();
                resolve({ status: 'Error', confidence: 50 });
            }
        });
    });
}

// ============================================================================
// API ENDPOINTS
// ============================================================================

// Health check
app.get('/api/health', (req, res) => {
    res.json({ 
        status: 'ok', 
        service: 'Cloud Gaming Connectivity Backend',
        version: '1.0.0',
        timestamp: new Date().toISOString()
    });
});

// Port check endpoint
app.post('/api/port-check', async (req, res) => {
    try {
        const { port, protocol } = req.body;

        // Validate input
        if (!port || !protocol) {
            return res.status(400).json({ 
                error: 'Missing required fields: port and protocol' 
            });
        }

        if (!isValidPort(port)) {
            return res.status(400).json({ 
                error: 'Invalid port number. Must be between 1 and 65535.' 
            });
        }

        if (!isValidProtocol(protocol)) {
            return res.status(400).json({ 
                error: 'Invalid protocol. Must be TCP, UDP, or TCP/UDP.' 
            });
        }

        // Get client's public IP
        const clientIP = getClientIP(req);
        console.log(`Port check request: ${clientIP}:${port} (${protocol})`);

        // Security: Only check the requesting client's IP
        // This prevents the service from being used to scan arbitrary hosts
        const targetHost = clientIP;

        let result = {
            port: parseInt(port),
            protocol: protocol.toUpperCase(),
            clientIP,
            timestamp: new Date().toISOString()
        };

        // Perform check based on protocol
        if (protocol.toUpperCase() === 'TCP') {
            const tcpResult = await checkTCPPort(targetHost, port);
            result = { ...result, ...tcpResult };
            result.notes = 'TCP connection test from server';
        } else if (protocol.toUpperCase() === 'UDP') {
            const udpResult = await checkUDPPort(targetHost, port);
            result = { ...result, ...udpResult };
            result.notes = 'UDP probe test (requires service response for accuracy)';
        } else if (protocol.toUpperCase() === 'TCP/UDP') {
            // Check both
            const [tcpResult, udpResult] = await Promise.all([
                checkTCPPort(targetHost, port),
                checkUDPPort(targetHost, port)
            ]);
            
            result.tcp = tcpResult;
            result.udp = udpResult;
            result.status = `TCP: ${tcpResult.status}, UDP: ${udpResult.status}`;
            result.confidence = Math.round((tcpResult.confidence + udpResult.confidence) / 2);
            result.notes = 'Tested both TCP and UDP protocols';
        }

        res.json(result);
    } catch (error) {
        console.error('Port check error:', error);
        res.status(500).json({ 
            error: 'Internal server error during port check',
            message: error.message 
        });
    }
});

// TURN server configuration endpoint (optional)
app.get('/api/turn-config', (req, res) => {
    // This endpoint can provide TURN server credentials for advanced NAT testing
    // In production, generate temporary credentials with time-limited validity
    
    // Example configuration for coturn server
    const turnConfig = {
        iceServers: [
            {
                urls: process.env.TURN_URLS || 'turn:turn.example.com:3478',
                username: process.env.TURN_USERNAME || 'testuser',
                credential: process.env.TURN_CREDENTIAL || 'testpass'
            },
            // Always include STUN as fallback
            { urls: 'stun:stun.l.google.com:19302' }
        ]
    };

    res.json(turnConfig);
});

// Batch port check endpoint
app.post('/api/port-check-batch', async (req, res) => {
    try {
        const { ports, protocol } = req.body;

        if (!ports || !Array.isArray(ports) || ports.length === 0) {
            return res.status(400).json({ 
                error: 'Missing or invalid ports array' 
            });
        }

        if (ports.length > 10) {
            return res.status(400).json({ 
                error: 'Maximum 10 ports per batch request' 
            });
        }

        if (!isValidProtocol(protocol)) {
            return res.status(400).json({ 
                error: 'Invalid protocol' 
            });
        }

        const clientIP = getClientIP(req);
        console.log(`Batch port check: ${clientIP} - ${ports.length} ports`);

        // Check all ports in parallel
        const results = await Promise.all(
            ports.map(async (port) => {
                if (!isValidPort(port)) {
                    return { port, error: 'Invalid port number' };
                }

                if (protocol.toUpperCase() === 'TCP') {
                    const result = await checkTCPPort(clientIP, port);
                    return { port: parseInt(port), protocol: 'TCP', ...result };
                } else if (protocol.toUpperCase() === 'UDP') {
                    const result = await checkUDPPort(clientIP, port);
                    return { port: parseInt(port), protocol: 'UDP', ...result };
                }
            })
        );

        res.json({
            clientIP,
            protocol: protocol.toUpperCase(),
            timestamp: new Date().toISOString(),
            results
        });
    } catch (error) {
        console.error('Batch port check error:', error);
        res.status(500).json({ 
            error: 'Internal server error',
            message: error.message 
        });
    }
});

// ============================================================================
// ERROR HANDLING
// ============================================================================

// 404 handler
app.use((req, res) => {
    res.status(404).json({ 
        error: 'Endpoint not found',
        availableEndpoints: [
            'GET /api/health',
            'POST /api/port-check',
            'POST /api/port-check-batch',
            'GET /api/turn-config'
        ]
    });
});

// Global error handler
app.use((err, req, res, next) => {
    console.error('Server error:', err);
    res.status(500).json({ 
        error: 'Internal server error',
        message: process.env.NODE_ENV === 'development' ? err.message : 'Something went wrong'
    });
});

// ============================================================================
// START SERVER
// ============================================================================

app.listen(PORT, () => {
    console.log('='.repeat(70));
    console.log('  Cloud Gaming Connectivity Backend Server');
    console.log('='.repeat(70));
    console.log(`  Server running on port ${PORT}`);
    console.log(`  Environment: ${process.env.NODE_ENV || 'development'}`);
    console.log(`  Rate limit: 10 requests/minute per IP`);
    console.log('');
    console.log('  Available endpoints:');
    console.log('    GET  /api/health');
    console.log('    POST /api/port-check');
    console.log('    POST /api/port-check-batch');
    console.log('    GET  /api/turn-config');
    console.log('='.repeat(70));
});

// ============================================================================
// TURN SERVER SETUP NOTES
// ============================================================================
//
// To set up a TURN server using coturn:
//
// 1. Install coturn:
//    Ubuntu/Debian: sudo apt-get install coturn
//    CentOS/RHEL: sudo yum install coturn
//
// 2. Configure /etc/turnserver.conf:
//    listening-port=3478
//    fingerprint
//    lt-cred-mech
//    use-auth-secret
//    static-auth-secret=YOUR_SECRET_KEY
//    realm=yourdomain.com
//    total-quota=100
//    stale-nonce=600
//    no-tcp-relay
//    no-multicast-peers
//
// 3. Start coturn:
//    sudo systemctl start coturn
//    sudo systemctl enable coturn
//
// 4. Update environment variables:
//    TURN_URLS=turn:your-server.com:3478
//    TURN_USERNAME=generated_username
//    TURN_CREDENTIAL=generated_credential
//
// 5. Generate temporary credentials (for production):
//    const crypto = require('crypto');
//    const timestamp = Math.floor(Date.now() / 1000) + 3600; // Valid for 1 hour
//    const username = timestamp + ':user';
//    const hmac = crypto.createHmac('sha1', 'YOUR_SECRET_KEY');
//    hmac.update(username);
//    const credential = hmac.digest('base64');
//
// ============================================================================

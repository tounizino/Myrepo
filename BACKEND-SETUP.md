# Backend Server Setup Guide

This guide covers deploying the optional Node.js backend for accurate port checking and TURN server support.

---

## Why Use the Backend?

**Frontend-only limitations:**
- UDP port testing is not possible in browsers
- TCP port checks are limited by CORS/browser security
- Results show "Unknown" for most tests

**Backend advantages:**
- Accurate TCP and UDP port testing
- Server-side verification from external network
- TURN server support for advanced NAT testing
- No browser security restrictions

---

## Quick Start (Local Development)

### 1. Install Dependencies

```bash
cd /path/to/your/project
npm install
```

### 2. Start Server

```bash
npm start
```

Server will run on `http://localhost:3000`

### 3. Update Frontend Configuration

In `cloud-gaming-connectivity-tool.html` (line 1280):

```javascript
const BACKEND_PORT_CHECK_URL = 'http://localhost:3000/api/port-check';
```

### 4. Test

1. Open the HTML tool in browser
2. Try checking a port (e.g., 3074)
3. Check terminal for backend logs

---

## Environment Variables

Create `.env` file in project root:

```bash
# Server Configuration
PORT=3000
NODE_ENV=production

# CORS Configuration
ALLOWED_ORIGINS=https://yourdomain.com,https://www.yourdomain.com

# Rate Limiting
RATE_LIMIT_WINDOW_MS=60000  # 1 minute

# TURN Server (Optional)
TURN_URLS=turn:turn.yourdomain.com:3478
TURN_USERNAME=testuser
TURN_CREDENTIAL=testpass
```

---

## Deployment Options

### Option 1: Heroku (Easiest)

**Step 1: Install Heroku CLI**
```bash
# macOS
brew install heroku/brew/heroku

# Windows
# Download from https://devcenter.heroku.com/articles/heroku-cli
```

**Step 2: Login and Create App**
```bash
heroku login
heroku create your-gaming-tool-backend
```

**Step 3: Set Environment Variables**
```bash
heroku config:set NODE_ENV=production
heroku config:set ALLOWED_ORIGINS=https://yourdomain.com
```

**Step 4: Deploy**
```bash
git push heroku main
```

**Step 5: Update Frontend**
```javascript
const BACKEND_PORT_CHECK_URL = 'https://your-gaming-tool-backend.herokuapp.com/api/port-check';
```

**Cost:** Free tier available (dyno sleeps after 30 min inactivity)

---

### Option 2: Render (Recommended)

**Step 1: Connect GitHub Repository**
1. Go to https://render.com
2. Sign up / Login
3. Click "New +" → "Web Service"
4. Connect your GitHub repository

**Step 2: Configure Service**
```yaml
Name: cloud-gaming-backend
Environment: Node
Build Command: npm install
Start Command: npm start
```

**Step 3: Environment Variables**
Add in Render dashboard:
- `NODE_ENV` = `production`
- `ALLOWED_ORIGINS` = `https://yourdomain.com`

**Step 4: Deploy**
Render auto-deploys on git push

**Step 5: Update Frontend**
```javascript
const BACKEND_PORT_CHECK_URL = 'https://cloud-gaming-backend.onrender.com/api/port-check';
```

**Cost:** Free tier available (spins down after inactivity, 15s cold start)

---

### Option 3: DigitalOcean App Platform

**Step 1: Create Account**
https://cloud.digitalocean.com/

**Step 2: Create App**
1. Click "Create" → "Apps"
2. Connect GitHub repo
3. Choose branch

**Step 3: Configure**
```yaml
Name: gaming-connectivity-backend
Type: Web Service
HTTP Port: 3000
Environment Variables:
  - NODE_ENV=production
  - ALLOWED_ORIGINS=https://yourdomain.com
```

**Step 4: Deploy**
DigitalOcean builds and deploys automatically

**Cost:** $5/month minimum (no cold starts)

---

### Option 4: AWS Elastic Beanstalk

**Step 1: Install EB CLI**
```bash
pip install awsebcli
```

**Step 2: Initialize**
```bash
eb init -p node.js gaming-backend
```

**Step 3: Create Environment**
```bash
eb create gaming-backend-prod
```

**Step 4: Set Environment Variables**
```bash
eb setenv NODE_ENV=production ALLOWED_ORIGINS=https://yourdomain.com
```

**Step 5: Deploy**
```bash
eb deploy
```

**Cost:** ~$10-20/month (t2.micro instance)

---

### Option 5: VPS (DigitalOcean Droplet, Linode, etc.)

**Step 1: Create Droplet**
- Ubuntu 22.04 LTS
- 1GB RAM minimum
- $5-6/month

**Step 2: SSH and Install Node.js**
```bash
ssh root@your-server-ip

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install PM2 (process manager)
npm install -g pm2
```

**Step 3: Upload Code**
```bash
# On your local machine
scp -r . root@your-server-ip:/var/www/gaming-backend
```

**Step 4: Setup and Start**
```bash
cd /var/www/gaming-backend
npm install
pm2 start server.js --name gaming-backend
pm2 startup
pm2 save
```

**Step 5: Configure Nginx (Reverse Proxy)**
```bash
sudo apt-get install nginx

# Create config
sudo nano /etc/nginx/sites-available/gaming-backend
```

Add configuration:
```nginx
server {
    listen 80;
    server_name api.yourdomain.com;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/gaming-backend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

**Step 6: SSL with Let's Encrypt**
```bash
sudo apt-get install certbot python3-certbot-nginx
sudo certbot --nginx -d api.yourdomain.com
```

**Cost:** $5-6/month (most control, best performance)

---

## TURN Server Setup (Advanced)

TURN servers enable accurate NAT traversal testing.

### Install coturn

**Ubuntu/Debian:**
```bash
sudo apt-get install coturn
```

**CentOS/RHEL:**
```bash
sudo yum install coturn
```

### Configure coturn

Edit `/etc/turnserver.conf`:

```ini
# Basic configuration
listening-port=3478
fingerprint
lt-cred-mech

# Authentication
use-auth-secret
static-auth-secret=YOUR_SECRET_KEY_CHANGE_THIS
realm=yourdomain.com

# Limits
total-quota=100
stale-nonce=600

# Performance
no-tcp-relay
no-multicast-peers

# Logging
log-file=/var/log/turnserver.log
verbose
```

### Start TURN Server

```bash
sudo systemctl start coturn
sudo systemctl enable coturn

# Check status
sudo systemctl status coturn
```

### Open Firewall Ports

```bash
# UFW (Ubuntu)
sudo ufw allow 3478/tcp
sudo ufw allow 3478/udp
sudo ufw allow 49152:65535/udp  # Media ports

# iptables
sudo iptables -A INPUT -p tcp --dport 3478 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 3478 -j ACCEPT
sudo iptables -A INPUT -p udp --dport 49152:65535 -j ACCEPT
```

### Generate TURN Credentials

Add to your backend `server.js` (around line 121):

```javascript
const crypto = require('crypto');

app.get('/api/turn-config', (req, res) => {
    const secret = process.env.TURN_SECRET || 'YOUR_SECRET_KEY_CHANGE_THIS';
    const username = Math.floor(Date.now() / 1000) + 86400; // Valid for 24h
    
    const hmac = crypto.createHmac('sha1', secret);
    hmac.update(username.toString());
    const credential = hmac.digest('base64');
    
    res.json({
        iceServers: [
            {
                urls: process.env.TURN_URLS || 'turn:turn.yourdomain.com:3478',
                username: username.toString(),
                credential: credential
            },
            { urls: 'stun:stun.l.google.com:19302' }
        ]
    });
});
```

### Update Frontend to Use TURN

In `cloud-gaming-connectivity-tool.html` (around line 1273):

```javascript
// Fetch TURN config from backend
async function loadTurnConfig() {
    try {
        const response = await fetch('https://your-backend.com/api/turn-config');
        const config = await response.json();
        return config.iceServers;
    } catch (error) {
        console.error('Failed to load TURN config, using STUN only');
        return STUN_SERVERS.map(url => ({ urls: url }));
    }
}

// Update NAT detection to use TURN
class NATTypeDetector {
    async detect() {
        const iceServers = await loadTurnConfig();
        const configuration = { iceServers };
        // ... rest of detection code
    }
}
```

---

## Security Best Practices

### 1. Rate Limiting (Already Included)

The backend has rate limiting built-in. Adjust in `server.js` (line 47):

```javascript
const limiter = rateLimit({
    windowMs: 60000,  // 1 minute
    max: 10,          // 10 requests per IP
    message: { error: 'Too many requests' }
});
```

### 2. CORS Configuration

Only allow your domain(s) in `server.js` (line 28):

```javascript
const allowedOrigins = [
    'https://yourdomain.com',
    'https://www.yourdomain.com'
];
```

### 3. Input Validation

Already implemented for port numbers and protocols.

### 4. HTTPS Only

Always use HTTPS in production:
- Heroku: Automatic HTTPS
- Render: Automatic HTTPS
- VPS: Use Let's Encrypt (see above)

### 5. Environment Variables

Never commit `.env` files. Use platform-specific secrets:
- Heroku: `heroku config:set`
- Render: Environment variables in dashboard
- AWS: Parameter Store
- VPS: `.env` file with `chmod 600`

### 6. Firewall Rules

Only open necessary ports:
- 80/443 (HTTP/HTTPS)
- 3478 (TURN)
- 49152-65535 (TURN media - UDP only)

### 7. Keep Dependencies Updated

```bash
npm audit
npm audit fix
npm update
```

---

## Monitoring & Logging

### PM2 (VPS Deployment)

```bash
# View logs
pm2 logs gaming-backend

# Monitor resources
pm2 monit

# Restart
pm2 restart gaming-backend

# View status
pm2 status
```

### Application Monitoring

Consider adding:
- **Sentry** for error tracking
- **New Relic** for performance monitoring
- **Loggly** or **Papertrail** for log management

Example Sentry integration:

```bash
npm install @sentry/node
```

In `server.js`:

```javascript
const Sentry = require('@sentry/node');

Sentry.init({
    dsn: process.env.SENTRY_DSN,
    environment: process.env.NODE_ENV
});

app.use(Sentry.Handlers.requestHandler());
app.use(Sentry.Handlers.errorHandler());
```

---

## Testing the Backend

### Manual Testing

**Health check:**
```bash
curl https://your-backend.com/api/health
```

**Port check:**
```bash
curl -X POST https://your-backend.com/api/port-check \
  -H "Content-Type: application/json" \
  -d '{"port": 3074, "protocol": "UDP"}'
```

**TURN config:**
```bash
curl https://your-backend.com/api/turn-config
```

### Automated Testing

Create `test.js`:

```javascript
const assert = require('assert');
const http = require('http');

describe('Backend API Tests', () => {
    const baseURL = 'http://localhost:3000';
    
    it('Health check should return OK', (done) => {
        http.get(`${baseURL}/api/health`, (res) => {
            assert.strictEqual(res.statusCode, 200);
            done();
        });
    });
    
    it('Port check should validate input', (done) => {
        const data = JSON.stringify({
            port: 99999,  // Invalid
            protocol: 'TCP'
        });
        
        const options = {
            hostname: 'localhost',
            port: 3000,
            path: '/api/port-check',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Content-Length': data.length
            }
        };
        
        const req = http.request(options, (res) => {
            assert.strictEqual(res.statusCode, 400);
            done();
        });
        
        req.write(data);
        req.end();
    });
});
```

Run tests:
```bash
npm test
```

---

## Troubleshooting

### Issue: CORS errors in browser

**Solution:**
- Ensure `ALLOWED_ORIGINS` includes your frontend domain
- Check browser console for specific CORS error
- Verify backend is responding with proper headers

### Issue: Port check always returns "Unknown"

**Solution:**
- Verify backend is accessible from internet
- Check if user's firewall is blocking incoming connections
- Test with a known open port first (80, 443)

### Issue: High memory usage

**Solution:**
- Limit concurrent connections in server.js
- Implement connection pooling
- Increase server resources

### Issue: Slow response times

**Solution:**
- Enable caching for TURN credentials
- Use CDN for static assets
- Optimize database queries (if added later)

### Issue: TURN server not working

**Solution:**
- Verify coturn is running: `sudo systemctl status coturn`
- Check firewall allows UDP 3478 and 49152-65535
- Test with TURN tester: https://webrtc.github.io/samples/src/content/peerconnection/trickle-ice/
- Verify credentials generation matches coturn config

---

## Cost Comparison

| Platform | Cost/Month | Cold Starts | SSL | Pros | Cons |
|----------|------------|-------------|-----|------|------|
| Heroku | Free-$7 | Yes | Free | Easy setup | Sleeps after 30min |
| Render | Free-$7 | Yes (15s) | Free | Auto-deploy | Cold start delay |
| DigitalOcean | $5+ | No | Manual | Always on | More setup |
| AWS EB | $10-20 | No | Manual | Scalable | Complex |
| VPS | $5-6 | No | Manual | Full control | Most work |

**Recommendation:**
- **Hobby/Blog:** Render (free tier)
- **Small Business:** DigitalOcean App Platform ($5)
- **High Traffic:** DigitalOcean Droplet with load balancer
- **Enterprise:** AWS with auto-scaling

---

## Scaling Considerations

### Horizontal Scaling

If you get high traffic:

1. **Load Balancer Setup**
   - Multiple backend instances
   - Nginx or HAProxy for load balancing
   - Session stickiness not needed (stateless API)

2. **Database for Caching**
   - Redis for rate limit tracking across instances
   - Cache TURN credentials

3. **Containerization**
   - Docker for consistent deployments
   - Kubernetes for orchestration

Example Docker setup:

**Dockerfile:**
```dockerfile
FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY . .
EXPOSE 3000
CMD ["node", "server.js"]
```

**Build and run:**
```bash
docker build -t gaming-backend .
docker run -p 3000:3000 -e NODE_ENV=production gaming-backend
```

---

## Maintenance Checklist

**Weekly:**
- [ ] Check error logs
- [ ] Monitor response times
- [ ] Review rate limit violations

**Monthly:**
- [ ] Update dependencies (`npm update`)
- [ ] Check SSL certificate expiry
- [ ] Review and rotate TURN secrets

**Quarterly:**
- [ ] Audit security vulnerabilities
- [ ] Review and optimize code
- [ ] Test disaster recovery

---

## Support & Resources

- Node.js Docs: https://nodejs.org/docs
- Express.js Guide: https://expressjs.com/
- coturn Wiki: https://github.com/coturn/coturn/wiki
- WebRTC Samples: https://webrtc.github.io/samples/

---

## Next Steps

1. Choose deployment platform
2. Set up backend server
3. Configure TURN server (optional)
4. Update frontend BACKEND_PORT_CHECK_URL
5. Test thoroughly
6. Monitor and optimize

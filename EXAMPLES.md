# Network Tool Pro - Usage Examples

This document provides real-world examples of how to use Network Tool Pro for various networking tasks.

## Table of Contents
- [Port Scanner Examples](#port-scanner-examples)
- [IP Information Examples](#ip-information-examples)
- [DNS Configuration Examples](#dns-configuration-examples)
- [Advanced Scenarios](#advanced-scenarios)

---

## Port Scanner Examples

### Example 1: Check Web Server Status
**Goal**: Verify if a web server is running and accepting connections

```
Target Host: example.com (or your server IP)
Ports: 80,443
Timeout: 2000ms
Threads: 10
```

**Expected Results**:
- Port 80 (HTTP): Open ✓
- Port 443 (HTTPS): Open ✓

**Troubleshooting**:
- If both closed: Server may be down or firewall blocking
- If only 80 open: HTTPS not configured
- If only 443 open: Server redirects HTTP to HTTPS

---

### Example 2: Scan Local Network for Active Services
**Goal**: Find what services are running on your local machine

```
Target Host: localhost
Ports: 20-1000
Timeout: 200ms
Threads: 200
```

**Common Findings**:
- Port 22: SSH server running
- Port 80: Local web server (Apache/Nginx/IIS)
- Port 443: HTTPS server
- Port 3306: MySQL database
- Port 5432: PostgreSQL database
- Port 8080: Development server

---

### Example 3: Check Database Server
**Goal**: Verify database server is accessible

```
Target Host: 192.168.1.100 (database server IP)
Ports: 3306,5432,1433,27017
Timeout: 3000ms
Threads: 10
```

**Port Meanings**:
- 3306: MySQL/MariaDB
- 5432: PostgreSQL
- 1433: Microsoft SQL Server
- 27017: MongoDB

---

### Example 4: Security Audit - Find Open Ports
**Goal**: Check what ports are exposed on a server

```
Target Host: 192.168.1.50
Ports: 1-1024 (well-known ports)
Timeout: 1000ms
Threads: 100
```

**Security Concerns** (if found open):
- Port 23 (Telnet): Insecure, should be disabled
- Port 3389 (RDP): Should only be open to trusted IPs
- Port 445 (SMB): Common target for attacks
- Port 21 (FTP): Unencrypted, prefer SFTP

---

### Example 5: Game Server Check
**Goal**: Verify game server is online

```
Target Host: gameserver.example.com
Ports: Depends on game
```

**Common Game Ports**:
- Minecraft: 25565
- TeamSpeak: 9987
- Discord Voice: 50000-50100
- Steam: 27015
- ARK: 7777,27015
- Rust: 28015

Example for Minecraft:
```
Target: play.minecraft-server.com
Ports: 25565
```

---

## IP Information Examples

### Example 1: Verify VPN Connection
**Goal**: Confirm VPN is active and routing traffic

**Steps**:
1. Open IP Information tab
2. Note your Public IP
3. Connect to VPN
4. Click "Refresh"
5. Public IP should change to VPN provider's IP

**What to Check**:
- Public IP changed? ✓ VPN is working
- DNS servers changed? ✓ Using VPN DNS
- New adapters listed? ✓ VPN adapter active

---

### Example 2: Find Your Router's IP
**Goal**: Locate your router's admin interface

**Steps**:
1. Open IP Information tab
2. Look at "Gateway" column
3. Common router IPs:
   - 192.168.1.1
   - 192.168.0.1
   - 10.0.0.1
   - 192.168.1.254

**Usage**:
- Copy Gateway IP
- Paste into web browser
- Access router admin panel

---

### Example 3: Troubleshoot "No Internet" Issue
**Goal**: Diagnose network connectivity problem

**Check List**:
```
✓ IPv4 Address present? (not 169.254.x.x)
✓ Gateway listed?
✓ DNS servers present?
✓ Public IP retrievable?
```

**Common Issues**:
- **169.254.x.x IP**: DHCP failure, router not responding
- **No Gateway**: Not connected to router
- **No DNS**: Can't resolve domain names
- **Public IP fails**: No internet connectivity

---

### Example 4: Document Network Configuration
**Goal**: Record network settings for support/documentation

**Steps**:
1. Open IP Information tab
2. Take screenshot (Windows Key + Shift + S)
3. Save with descriptive name
4. Include in documentation

**Useful for**:
- Helpdesk tickets
- Network diagrams
- Before/after comparisons
- Compliance documentation

---

## DNS Configuration Examples

### Example 1: Switch to Cloudflare for Speed
**Goal**: Use fastest DNS for better performance

**Steps**:
1. Run as Administrator
2. Open DNS Configuration tab
3. Select your active adapter (usually "Ethernet" or "Wi-Fi")
4. Click "Cloudflare" preset button
5. Click "Apply DNS Settings"

**Result**:
- Primary DNS: 1.1.1.1
- Secondary DNS: 1.0.0.1
- Expected benefit: Faster website loading

---

### Example 2: Family-Safe Internet with OpenDNS
**Goal**: Block adult content and malicious sites

**Steps**:
1. Run as Administrator
2. Select your network adapter
3. Click "OpenDNS" preset
4. Apply settings

**Configuration**:
- Primary: 208.67.222.222
- Secondary: 208.67.220.220

**Benefits**:
- Blocks phishing sites
- Optional parental controls (requires OpenDNS account)
- Typo correction

---

### Example 3: Privacy-Focused with Quad9
**Goal**: Block malicious domains without logging

**Steps**:
1. Run as Administrator
2. Select adapter
3. Click "Quad9" preset
4. Apply

**Configuration**:
- Primary: 9.9.9.9
- Secondary: 149.112.112.112

**Benefits**:
- Blocks known malicious domains
- No personal data logging
- Security-focused

---

### Example 4: Block Ads with AdGuard
**Goal**: Network-wide ad blocking

**Steps**:
1. Run as Administrator
2. Select adapter
3. Click "AdGuard" preset
4. Apply settings

**Configuration**:
- Primary: 94.140.14.14
- Secondary: 94.140.15.15

**Benefits**:
- Blocks ads at DNS level
- Blocks trackers
- Works in all browsers and apps

---

### Example 5: Custom DNS Setup
**Goal**: Use specific DNS servers (e.g., company DNS)

**Steps**:
1. Run as Administrator
2. Select adapter
3. Manually enter:
   - Primary: 10.0.0.1 (example)
   - Secondary: 10.0.0.2 (example)
4. Apply

**Use Cases**:
- Corporate DNS servers
- Pi-hole DNS server
- Custom DNS filtering
- Local DNS server

---

### Example 6: Revert to ISP DNS
**Goal**: Return to automatic/default DNS

**Steps**:
1. Run as Administrator
2. Select adapter
3. Click "Reset to DHCP"
4. Confirm

**When to Use**:
- DNS changes caused issues
- Want to use ISP's DNS
- Troubleshooting connectivity
- Undoing previous changes

---

## Advanced Scenarios

### Scenario 1: Diagnosing Slow Website Loading

**Problem**: Websites take long to start loading, but download fast once started.

**Diagnosis Steps**:

1. **Check Current DNS**:
   - IP Information tab → note DNS servers
   - If showing ISP DNS (varies), may be slow

2. **Test Port Connectivity**:
   ```
   Target: 8.8.8.8
   Port: 53
   ```
   If closed, DNS queries may be blocked

3. **Switch DNS**:
   - Try Cloudflare (1.1.1.1) - fastest
   - Or Google (8.8.8.8) - reliable

4. **Test Results**:
   - Visit various websites
   - Should load faster now

---

### Scenario 2: Can't Access Certain Websites

**Problem**: Some websites don't load, others work fine.

**Diagnosis**:

1. **Port Scanner Test**:
   ```
   Target: problematic-site.com
   Ports: 80,443
   ```
   - If closed: Site is down or blocked
   - If open: DNS or routing issue

2. **Check DNS**:
   - IP Information tab
   - Note current DNS servers
   - May be censoring/filtering

3. **Try Alternative DNS**:
   - Switch to Cloudflare or Google
   - Bypasses ISP filtering

4. **Verify**:
   - Try accessing site again
   - May work with different DNS

---

### Scenario 3: Setting Up Home Server

**Goal**: Make home server accessible and secure

**Steps**:

1. **Find Server's Local IP**:
   - IP Information tab on server computer
   - Note IPv4 address (e.g., 192.168.1.150)

2. **Verify Services Running**:
   ```
   Target: localhost
   Ports: 80,443 (or your service ports)
   ```
   Should show as Open

3. **Test from Another Computer**:
   ```
   Target: 192.168.1.150
   Ports: 80,443
   ```
   If open: Firewall allows local access

4. **Port Security Check**:
   ```
   Target: localhost
   Ports: 1-1000
   ```
   Only intended services should be open

---

### Scenario 4: Public Wi-Fi Security Check

**Goal**: Verify safety of public Wi-Fi network

**Steps**:

1. **Connect to Wi-Fi**

2. **Check Your Public IP**:
   - IP Information tab
   - Note Public IP

3. **Verify Gateway**:
   - Should match Wi-Fi router
   - If unexpected: potential rogue AP

4. **Scan Your Own Ports** (from another device):
   ```
   Target: [your IP from step 2]
   Ports: Common ports
   ```
   All should be closed/filtered

5. **Check DNS**:
   - Verify DNS isn't hijacked
   - Should be legitimate servers

**Red Flags**:
- Unknown gateway IP
- Suspicious DNS servers
- Ports open that shouldn't be

---

### Scenario 5: Optimize Gaming Connection

**Goal**: Reduce lag and improve gaming performance

**Steps**:

1. **Test Game Server Ports**:
   ```
   Target: game-server.com
   Ports: [game-specific ports]
   Timeout: 100ms
   ```
   Note response times

2. **Check Your Network**:
   - IP Information tab
   - Verify DHCP enabled
   - Note current DNS

3. **Optimize DNS for Gaming**:
   - Try Cloudflare (lowest latency)
   - Or Google DNS (very stable)

4. **Verify Changes**:
   - Retest server ports
   - Response times should improve

5. **Port Forwarding Check**:
   - Scan your public IP (requires external tool)
   - Verify required ports are forwarded

---

### Scenario 6: Corporate Network Compliance Check

**Goal**: Verify workstation meets security policy

**Checklist**:

1. **Port Security Scan**:
   ```
   Target: localhost
   Ports: 1-1024
   ```
   Document all open ports

2. **Verify DNS Configuration**:
   - DNS tab
   - Should use corporate DNS
   - Document current settings

3. **Network Adapter Audit**:
   - IP Information tab
   - List all adapters
   - Verify no unauthorized VPN/adapters

4. **Gateway Verification**:
   - Confirm using approved gateway
   - No rogue router connections

5. **Generate Report**:
   - Screenshot all findings
   - Document in compliance checklist

---

## Testing and Verification

### Verify DNS Change Took Effect

**Method 1 - Command Line**:
```batch
nslookup google.com
```
Should show your new DNS server as "Server"

**Method 2 - Network Tool Pro**:
1. DNS Configuration tab
2. Current DNS list should show new servers
3. IP Information tab → Refresh
4. DNS column should show new servers

---

### Verify Port Scanner Accuracy

**Test with Known Services**:
```
Target: google.com
Ports: 80,443
Result: Should show both as OPEN

Target: google.com  
Ports: 81,444
Result: Should show as CLOSED or timeout
```

---

## Tips for Effective Use

### Port Scanning Tips
1. Always test with `localhost` first to verify tool works
2. Use higher thread counts for large scans (300-500)
3. Adjust timeout based on network:
   - LAN: 200-500ms
   - Internet: 1000-3000ms
4. Start with small ranges before full scans

### DNS Configuration Tips
1. Always document original DNS before changing
2. Test with multiple websites after change
3. If problems occur, Reset to DHCP immediately
4. Use secondary DNS for redundancy
5. Clear browser cache after DNS change (Ctrl+F5)

### Network Troubleshooting Tips
1. Check physical connections first
2. Use IP Information to verify DHCP is working
3. Ping gateway before testing internet
4. Try different DNS if websites won't load
5. Screenshot everything for documentation

---

**Need more examples?** Check the USER_GUIDE.md for additional scenarios and troubleshooting tips.

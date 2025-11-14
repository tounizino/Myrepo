# 📘 Network Tool Pro - User Guide

## Table of Contents
1. [Getting Started](#getting-started)
2. [Port Scanner](#port-scanner)
3. [IP Information](#ip-information)
4. [DNS Configuration](#dns-configuration)
5. [Common Use Cases](#common-use-cases)
6. [Tips & Tricks](#tips--tricks)
7. [FAQ](#faq)

---

## Getting Started

### First Launch
1. **Download** `NetworkToolPro.exe` from the releases or build it from source
2. **Run the application**:
   - For Port Scanner and IP Info: Just double-click
   - For DNS Configuration: Right-click → "Run as Administrator"

### Application Overview
Network Tool Pro has three main sections accessible via tabs:
- **Port Scanner** - Test connectivity to specific ports
- **IP Information** - View your network configuration
- **DNS Configuration** - Manage DNS servers

---

## Port Scanner

### Basic Port Scanning

#### Scan a Single Port
1. Enter target in "Target Host/IP" field:
   - Examples: `localhost`, `192.168.1.1`, `google.com`, `github.com`
2. Enter port number in "Ports" field:
   - Example: `80`
3. Click **Start Scan**

#### Scan Multiple Ports
```
Format: 80,443,8080
Result: Scans ports 80, 443, and 8080
```

#### Scan a Range of Ports
```
Format: 20-100
Result: Scans all ports from 20 to 100
```

#### Combine Ranges and Individual Ports
```
Format: 20-100,443,8080,8443
Result: Scans ports 20-100, plus 443, 8080, and 8443
```

### Quick Presets

Click **Common Ports** to automatically fill in frequently scanned ports:
- FTP (21)
- SSH (22)
- Telnet (23)
- SMTP (25)
- DNS (53)
- HTTP (80)
- POP3 (110)
- NetBIOS (139)
- IMAP (143)
- HTTPS (443)
- SMB (445)
- MySQL (3306)
- RDP (3389)
- PostgreSQL (5432)
- VNC (5900)
- HTTP Proxy (8080)
- HTTPS Alt (8443)

### Advanced Options

#### Timeout Setting
- **Default**: 1000ms (1 second)
- **Range**: 100-10,000ms
- **When to adjust**:
  - Lower timeout (100-500ms): Faster scans on local network
  - Higher timeout (2000-5000ms): More reliable on slow networks or internet hosts

#### Threads (Concurrency)
- **Default**: 100 threads
- **Range**: 1-500 threads
- **When to adjust**:
  - More threads (200-500): Faster scans of many ports
  - Fewer threads (1-50): Avoid overwhelming slow systems

### Reading Results

#### Result Columns
- **Port**: Port number scanned
- **Status**: Open, Closed, or Unknown
- **Response Time**: How long the connection attempt took
- **Service**: Common service that uses this port
- **Notes**: Additional information or error messages

#### Color Coding
- **Green Background**: Port is OPEN
- **Gray Text**: Port is CLOSED
- **Black Text**: Unknown status or error

### Example Scenarios

#### Check if Web Server is Running
```
Target: localhost (or your server IP)
Ports: 80,443
```
If port 80 or 443 shows "Open", your web server is running.

#### Scan Home Router
```
Target: 192.168.1.1 (your router IP)
Ports: 20-100
```
See what services your router exposes.

#### Check if Remote Desktop is Available
```
Target: 192.168.1.100 (remote computer IP)
Ports: 3389
```
Port 3389 open means RDP is available.

---

## IP Information

### What You'll See

#### Top Section
- **Host**: Your computer's network name
- **Public IP**: Your IP address as seen from the internet

#### Adapter List
Each row shows a network adapter with:
- **Adapter Name**: Interface name (e.g., "Ethernet", "Wi-Fi")
- **Description**: Full hardware description
- **IPv4**: Your local IPv4 address(es)
- **IPv6**: Your local IPv6 address(es)
- **Gateway**: Your router's IP address
- **DNS**: Current DNS servers
- **DHCP**: Whether using automatic IP configuration

### Using the Information

#### Find Your Local IP
Look in the IPv4 column for addresses like:
- `192.168.x.x`
- `10.x.x.x`
- `172.16-31.x.x`

#### Identify Active Adapters
Active adapters will have:
- IP addresses assigned
- Gateway address listed
- Speed information

#### Check Current DNS
The DNS column shows which DNS servers you're currently using.

### Refresh Button
Click **Refresh** to:
- Update all network information
- Detect new adapters
- Reload public IP
- See DNS changes after modifying settings

---

## DNS Configuration

### Why Change DNS?

Different DNS providers offer different benefits:

#### Google DNS (8.8.8.8, 8.8.4.4)
- **Best for**: General use, reliability
- **Features**: Fast, reliable, global coverage
- **Privacy**: Logs queries for 24-48 hours

#### Cloudflare (1.1.1.1, 1.0.0.1)
- **Best for**: Privacy-conscious users
- **Features**: Very fast, privacy-focused
- **Privacy**: Minimal logging, focus on privacy

#### OpenDNS (208.67.222.222, 208.67.220.220)
- **Best for**: Families, security
- **Features**: Parental controls, phishing protection
- **Privacy**: Basic logging for security

#### Quad9 (9.9.9.9, 149.112.112.112)
- **Best for**: Security-focused users
- **Features**: Blocks malicious domains
- **Privacy**: No personal data logging

#### AdGuard (94.140.14.14, 94.140.15.15)
- **Best for**: Ad blocking
- **Features**: Blocks ads and tracking domains
- **Privacy**: Privacy-focused

### How to Change DNS

#### Step-by-Step
1. **Run as Administrator** (Required!)
   - Right-click NetworkToolPro.exe
   - Select "Run as Administrator"

2. **Go to DNS Configuration Tab**

3. **Select Your Network Adapter**
   - Use the dropdown to choose your active network connection
   - Usually "Ethernet" or "Wi-Fi"

4. **View Current DNS**
   - Current DNS servers are shown in the list

5. **Choose DNS Method**:

   **Option A - Use Preset (Easiest)**
   - Click one of the preset buttons (Google, Cloudflare, etc.)
   - Click **Apply DNS Settings**

   **Option B - Enter Manually**
   - Enter Primary DNS (required)
   - Enter Secondary DNS (optional)
   - Click **Apply DNS Settings**

6. **Verify**
   - Success message will appear
   - Current DNS list will update
   - Go to IP Information tab and click Refresh to confirm

### Reset to Automatic DNS

If you want to go back to your ISP's DNS or automatic configuration:
1. Select your adapter
2. Click **Reset to DHCP**
3. Confirm the action

Your adapter will now get DNS servers automatically from your router/ISP.

### Troubleshooting DNS Changes

#### "Access Denied" Error
**Problem**: Not running as Administrator
**Solution**: Close app, right-click .exe, "Run as Administrator"

#### DNS Changes Don't Apply
**Possible issues**:
1. Wrong adapter selected - choose your active connection
2. Invalid DNS IP address - check for typos
3. Network adapter is disabled - enable it in Windows settings

#### Test DNS is Working
After changing DNS:
1. Open Command Prompt
2. Run: `nslookup google.com`
3. You should see your new DNS server listed as "Server"

---

## Common Use Cases

### 1. Check if Website is Up
**Scenario**: Website won't load in browser
```
Tab: Port Scanner
Target: example.com
Ports: 80,443
```
If ports are closed, server might be down.

### 2. Troubleshoot Network Connection
**Scenario**: Internet is slow or not working
```
Tab: IP Information
1. Check if you have an IPv4 address
2. Check if Gateway is listed
3. Check if DNS servers are present
```

### 3. Speed Up Internet with Better DNS
**Scenario**: Websites take long to load
```
Tab: DNS Configuration
1. Try Cloudflare (1.1.1.1) - fastest
2. Or Google DNS (8.8.8.8) - very reliable
3. Test speed at fast.com
```

### 4. Block Ads Network-Wide
**Scenario**: Too many ads
```
Tab: DNS Configuration
1. Select your adapter
2. Click "AdGuard" preset
3. Apply settings
Ads will be blocked for all devices using this computer as gateway
```

### 5. Find Devices on Local Network
**Scenario**: Find your network printer or NAS
```
Tab: Port Scanner
Target: 192.168.1.1-254 (scan range)
Ports: 80,139,445
```
Open ports will reveal active devices.

### 6. Verify VPN is Working
**Scenario**: Check if VPN is active
```
Tab: IP Information
1. Note your Public IP
2. Connect to VPN
3. Click Refresh
4. Public IP should change to VPN's IP
```

### 7. Check if Firewall is Blocking Port
**Scenario**: Server software won't accept connections
```
Tab: Port Scanner
Target: localhost
Ports: [your server's port]
```
If closed from localhost, firewall is likely blocking.

---

## Tips & Tricks

### Port Scanner Tips

1. **Scan Localhost First**
   - Before scanning remote hosts, test with localhost
   - Helps verify the tool is working correctly

2. **Start with Small Ranges**
   - Don't scan 1-65535 immediately
   - Start with common ports or small ranges

3. **Increase Threads for Large Scans**
   - Scanning 1000+ ports? Use 300-500 threads
   - Much faster, especially on fast connections

4. **Lower Timeout on LANs**
   - Local network? Use 100-200ms timeout
   - Speeds up scans significantly

### IP Information Tips

1. **Use for Documentation**
   - Take screenshots for network documentation
   - Helpful when troubleshooting with support

2. **Monitor for Changes**
   - Check periodically if IP changes (DHCP)
   - Verify VPN connections

3. **Identify the Right Adapter**
   - Multiple adapters? Look at Gateway and DNS
   - Active adapter will have these populated

### DNS Configuration Tips

1. **Test Different DNS Providers**
   - Try Google, then Cloudflare
   - Use speedtest to see which is faster for you

2. **Use Secondary DNS**
   - Always set a secondary DNS
   - Provides backup if primary is down

3. **Document Original DNS**
   - Before changing, screenshot current DNS
   - Makes it easy to revert if needed

4. **Clear Browser Cache After DNS Change**
   - Some websites may be cached
   - Ctrl+F5 in browser to hard refresh

5. **Restart Browser After DNS Change**
   - Browser may cache DNS results
   - Fresh start ensures new DNS is used

---

## FAQ

### General Questions

**Q: Do I need to install anything?**
A: No, it's a standalone executable. Just run it.

**Q: Does it work on Windows 11?**
A: Yes, fully compatible with Windows 10 and 11.

**Q: Is it safe to use?**
A: Yes, the source code is available for review. It only performs standard network operations.

### Port Scanner Questions

**Q: Is port scanning legal?**
A: Scanning your own systems is legal. Scanning others without permission may be illegal.

**Q: Why are all ports showing as closed?**
A: Could be firewall, wrong IP, or host is down. Try scanning localhost first.

**Q: How long does a full port scan (1-65535) take?**
A: With 500 threads and 1000ms timeout: 2-5 minutes on fast networks.

**Q: Can it detect filtered ports?**
A: Filtered ports will typically show as "Closed" or timeout.

### IP Information Questions

**Q: Why is Public IP showing "Unable to retrieve"?**
A: Check internet connection. The tool tries multiple services, so if all fail, likely no internet.

**Q: What's the difference between IPv4 and IPv6?**
A: IPv4 (192.168.x.x) is older and more common. IPv6 (2001:...) is newer with more addresses.

**Q: Why do I have multiple IP addresses?**
A: Multiple adapters (Ethernet, Wi-Fi, VPN) each have their own IP.

### DNS Configuration Questions

**Q: Will changing DNS make internet faster?**
A: It can speed up website loading (DNS lookups), but won't increase download speeds.

**Q: Can changing DNS break my internet?**
A: No, you can always reset to DHCP. Worst case: restart router.

**Q: Do I need to change DNS on every device?**
A: This changes DNS for this computer only. Change router DNS to affect all devices.

**Q: Which DNS is fastest?**
A: Usually Cloudflare (1.1.1.1) or Google (8.8.8.8), but varies by location.

**Q: Will my ISP know if I change DNS?**
A: ISP can see DNS queries, but most don't care. For privacy, use DNS over HTTPS in browser.

---

## Need More Help?

### Resources
- Check the main README.md for technical details
- Review BUILD_INSTRUCTIONS.md to build from source
- Open an issue on GitHub for bugs or questions

### Before Reporting Issues
1. Try running as Administrator
2. Disable firewall temporarily to test
3. Try scanning localhost to verify tool works
4. Check Windows Event Viewer for errors

---

**Happy networking! 🌐**

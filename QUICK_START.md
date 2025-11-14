# 🚀 Quick Start Guide - Network Tool Pro

Get up and running in 5 minutes!

## Step 1: Get the Application

### Option A: Download (Easiest)
1. Download `NetworkToolPro.exe` from releases
2. No installation needed!

### Option B: Build from Source
```batch
dotnet restore
dotnet build -c Release
dotnet publish -c Release -r win-x64 --self-contained
```
Executable will be in: `bin/Release/net6.0-windows/win-x64/publish/`

---

## Step 2: Run the Application

### For Port Scanner & IP Info:
- Just double-click `NetworkToolPro.exe`
- Works immediately, no admin rights needed

### For DNS Configuration:
- **Right-click** `NetworkToolPro.exe`
- Select **"Run as Administrator"**
- Required for changing network settings

---

## Step 3: Try It Out!

### 🔍 Port Scanner (No admin needed)
1. Click **Port Scanner** tab
2. Enter `localhost` in Target field
3. Click **Common Ports** button
4. Click **🔍 Start Scan**
5. Watch results appear in real-time!

**What you'll see:**
- Green rows = Ports that are OPEN
- Gray text = Ports that are CLOSED
- Response times in milliseconds

---

### 📊 IP Information (No admin needed)
1. Click **IP Information** tab
2. Information loads automatically
3. View your:
   - Computer name
   - Public IP address
   - All network adapters
   - Current DNS servers

**Useful for:**
- Finding your local IP
- Finding your router's IP (Gateway column)
- Checking if VPN is active (Public IP changes)

---

### ⚙️ DNS Configuration (Requires admin)
1. **Must run as Administrator!**
2. Click **DNS Configuration** tab
3. Select your network adapter from dropdown
4. Click a preset button (try **Cloudflare**)
5. Click **Apply DNS Settings**
6. Done! You're now using Cloudflare DNS

**Benefits:**
- Faster website loading
- More privacy
- Access to blocked sites
- Ad blocking (with AdGuard DNS)

**To undo:** Click **Reset to DHCP**

---

## Common First Tasks

### "Is my web server running?"
```
Tab: Port Scanner
Target: localhost
Ports: 80,443
Expected: Both should show as OPEN
```

### "What's my router's IP?"
```
Tab: IP Information
Look at: Gateway column
Common: 192.168.1.1 or 192.168.0.1
```

### "Speed up my internet"
```
Tab: DNS Configuration (Run as Admin!)
Click: Cloudflare button
Click: Apply DNS Settings
Result: Faster DNS lookups
```

### "Check if remote computer is accessible"
```
Tab: Port Scanner
Target: 192.168.1.100 (other computer's IP)
Ports: 3389 (for Remote Desktop)
Expected: Should show OPEN if RDP enabled
```

---

## Keyboard Shortcuts

- **Tab**: Move between fields
- **Enter** in Target/Ports: Start scan
- **Escape**: Cancel current scan
- **Ctrl+A**: Select all in results

---

## Troubleshooting

### "Access Denied" when changing DNS
**Fix:** Right-click exe → "Run as Administrator"

### Port scanner shows everything closed
**Reasons:**
- Target is wrong (check IP/hostname)
- Firewall blocking (test with localhost first)
- Host is offline (ping it first)

### Can't retrieve public IP
**Reasons:**
- No internet connection
- Firewall blocking outbound HTTP
- Just wait a moment and click Refresh

### DNS changes don't work
**Checklist:**
- [ ] Running as Administrator?
- [ ] Correct adapter selected?
- [ ] Valid DNS IP entered?
- [ ] Adapter is enabled?

---

## Pro Tips

### Port Scanner
- Start with `localhost` to test the tool works
- Use **Common Ports** for quick standard scan
- Increase **Threads** to 300+ for faster large scans
- Lower **Timeout** to 200ms for local network scans

### IP Information
- Click **Refresh** after connecting to VPN to verify
- Take screenshots for network documentation
- Gateway IP is your router's admin interface

### DNS Configuration
- **Cloudflare (1.1.1.1)**: Fastest
- **Google (8.8.8.8)**: Most reliable
- **AdGuard (94.140.14.14)**: Blocks ads
- **Always set Secondary DNS** for redundancy
- **Test after change**: Visit multiple websites

---

## What's Next?

### Learn More:
- **README.md** - Full feature documentation
- **USER_GUIDE.md** - Detailed usage instructions
- **EXAMPLES.md** - Real-world scenarios
- **BUILD_INSTRUCTIONS.md** - How to compile

### Common Use Cases:
1. Check if services are running (port scan localhost)
2. Find devices on network (scan 192.168.1.1-254)
3. Speed up browsing (change to Cloudflare DNS)
4. Block ads (change to AdGuard DNS)
5. Troubleshoot connectivity (check IP info)
6. Verify VPN (watch Public IP change)

---

## Need Help?

### Self-Help:
1. Check the FAQ in USER_GUIDE.md
2. Review EXAMPLES.md for your scenario
3. Try with `localhost` first to verify tool works

### Still Stuck?
- Open an issue on GitHub
- Include:
  - What you're trying to do
  - What happens instead
  - Screenshot if possible

---

## Legal Notice

⚠️ **Important:**
- Only scan systems you own or have permission to scan
- Unauthorized port scanning may be illegal
- This tool is for legitimate network administration
- Use responsibly and ethically

---

## Quick Reference Card

### DNS Presets
| Provider | Primary | Secondary | Best For |
|----------|---------|-----------|----------|
| Google | 8.8.8.8 | 8.8.4.4 | Reliability |
| Cloudflare | 1.1.1.1 | 1.0.0.1 | Speed & Privacy |
| OpenDNS | 208.67.222.222 | 208.67.220.220 | Family Safety |
| Quad9 | 9.9.9.9 | 149.112.112.112 | Security |
| AdGuard | 94.140.14.14 | 94.140.15.15 | Ad Blocking |

### Common Ports
| Port | Service | Description |
|------|---------|-------------|
| 21 | FTP | File Transfer |
| 22 | SSH | Secure Shell |
| 80 | HTTP | Web Server |
| 443 | HTTPS | Secure Web |
| 3306 | MySQL | Database |
| 3389 | RDP | Remote Desktop |
| 8080 | HTTP-Alt | Web Proxy |

---

**🎉 You're all set! Enjoy Network Tool Pro!**

For detailed information, see the full **README.md** and **USER_GUIDE.md**.

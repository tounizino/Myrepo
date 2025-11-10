# 🌐 Network Tool Pro

**The Ultimate Windows Network Diagnostic & Configuration Tool**

Network Tool Pro is a comprehensive, professional-grade networking utility for Windows that combines port scanning, IP information gathering, and DNS configuration management into one powerful application.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![.NET](https://img.shields.io/badge/.NET-6.0--windows-purple)
![Platform](https://img.shields.io/badge/platform-Windows-lightgrey)

## ✨ Features

### 🔍 Port Scanner
- **Fast & Efficient**: Multi-threaded port scanning with customizable concurrency
- **Flexible Port Selection**: Scan individual ports, ranges (e.g., 20-100), or multiple ports at once
- **Common Ports Preset**: Quick access to frequently scanned ports
- **Real-time Progress**: Live progress updates with visual feedback
- **Service Detection**: Automatically identifies common services running on open ports
- **Response Time Tracking**: Measures connection response times in milliseconds
- **Customizable Timeout**: Adjust timeout settings from 100ms to 10 seconds
- **Export Ready**: Color-coded results for easy identification

### 📊 IP Information
- **Local Network Details**: View all active network adapters
- **Public IP Detection**: Automatically discovers your public IP address
- **Comprehensive Adapter Info**: 
  - IPv4 and IPv6 addresses
  - Gateway information
  - DNS servers
  - MAC addresses
  - Connection speed
  - DHCP status
  - Subnet masks
- **Auto-Refresh**: Keep information up-to-date with one click

### ⚙️ DNS Configuration Manager
- **Easy DNS Management**: Change DNS servers with a simple interface
- **Multiple Adapter Support**: Configure any network adapter
- **Quick Presets**: One-click configuration for popular DNS providers:
  - 🌍 **Google DNS** (8.8.8.8, 8.8.4.4)
  - ☁️ **Cloudflare** (1.1.1.1, 1.0.0.1)
  - 🔒 **OpenDNS** (208.67.222.222, 208.67.220.220)
  - 🛡️ **Quad9** (9.9.9.9, 149.112.112.112)
  - 🚫 **AdGuard** (94.140.14.14, 94.140.15.15)
- **DHCP Reset**: Easily restore automatic DNS settings
- **Current Settings Display**: Always see what DNS servers are currently in use

## 🚀 Quick Start

### Prerequisites
- Windows 10 or Windows 11
- .NET 6.0 Runtime (included with Windows 11, or download from Microsoft)
- Administrator privileges (required for DNS configuration only)

### Download & Run
1. Download the latest `NetworkToolPro-x64.exe` from [GitHub Releases](https://github.com/your-org/your-repo/releases) *(or build locally using `build.bat` if no release is available yet)*
2. Double-click to run
3. For DNS configuration features, right-click and select "Run as Administrator"

## 🛠️ Building from Source

### Requirements
- .NET 6.0 SDK or later
- Windows 10/11
- Visual Studio 2022 or JetBrains Rider (optional)

### Build Instructions

#### Using Command Line:
```bash
# Clone or download the repository
cd NetworkToolPro

# Restore dependencies
dotnet restore

# Build the project
dotnet build -c Release

# Publish as self-contained executable
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true
```

#### Using Visual Studio:
1. Open `NetworkToolPro.csproj` in Visual Studio 2022
2. Select **Release** configuration
3. Right-click project → **Publish**
4. Choose **Folder** profile
5. Click **Publish**

#### Using the Build Script:
```bash
# Make the script executable (if on Unix-based system with WSL)
chmod +x build.sh

# Run the build script
./build.sh

# Or on Windows:
build.bat
```

The compiled executable will be in:
- `bin/Release/net6.0-windows/win-x64/publish/NetworkToolPro.exe`

## 📖 Usage Guide

### Port Scanner
1. Navigate to the **Port Scanner** tab
2. Enter target host/IP (e.g., `localhost`, `192.168.1.1`, `google.com`)
3. Specify ports to scan:
   - Individual: `80,443,8080`
   - Ranges: `20-100`
   - Combined: `20-100,443,8080`
   - Or click **Common Ports** for preset
4. Adjust timeout and thread count if needed
5. Click **🔍 Start Scan**
6. View results in real-time with color-coded status

**Tips:**
- Use higher thread counts (100-500) for faster scanning of many ports
- Reduce timeout for faster scans of likely-closed ports
- Green = Open, Gray = Closed

### IP Information
1. Navigate to the **IP Information** tab
2. Information loads automatically
3. View your:
   - Hostname
   - Public IP address
   - All network adapters with full details
4. Click **Refresh** to update information

### DNS Configuration
1. Navigate to the **DNS Configuration** tab
2. **Important**: Run as Administrator for this feature
3. Select your network adapter from dropdown
4. View current DNS servers
5. To change DNS:
   - Either click a preset button (Google, Cloudflare, etc.)
   - Or manually enter Primary and Secondary DNS
6. Click **Apply DNS Settings**
7. To restore automatic DNS: Click **Reset to DHCP**

**Popular DNS Options:**
- **Google DNS**: Fast, reliable, global coverage
- **Cloudflare**: Privacy-focused, very fast
- **OpenDNS**: Family filtering, security features
- **Quad9**: Blocks malicious domains
- **AdGuard**: Blocks ads and trackers

## ⚠️ Important Notes

### Administrator Privileges
- Port scanning and IP information: **No admin required**
- DNS configuration: **Administrator required**
  - Right-click → "Run as Administrator"

### Firewall Considerations
- Port scanning may trigger firewall alerts
- Some firewalls may block port scanning
- Scanning external hosts may be restricted by ISP or network policies

### Legal & Ethical Use
- Only scan systems you own or have permission to scan
- Unauthorized port scanning may be illegal in your jurisdiction
- This tool is for legitimate network diagnostics and administration

## 🔧 Technical Details

### Architecture
- **Framework**: .NET 6.0 Windows Forms
- **Language**: C# 10.0
- **Threading**: async/await with configurable concurrency
- **WMI**: System.Management for DNS configuration
- **Networking**: System.Net, System.Net.Sockets, System.Net.NetworkInformation

### Key Components
- `PortScannerService`: High-performance async port scanning
- `IPInfoService`: Network interface information gathering
- `DnsConfigurator`: WMI-based DNS configuration management
- Modern UI with color-coded results and real-time updates

## 🐛 Troubleshooting

### "Access Denied" when changing DNS
**Solution**: Run the application as Administrator

### Port scanner shows all ports as closed
**Possible causes**:
- Firewall blocking connections
- Incorrect host/IP address
- Network connectivity issues
- Target host is down

### Public IP shows "Unable to retrieve"
**Possible causes**:
- No internet connection
- Firewall blocking HTTP requests
- External IP services are down (app tries multiple services)

### DNS changes don't apply
**Check**:
- Running as Administrator
- Correct adapter selected
- Valid DNS IP addresses entered
- Network adapter is active

## 📝 License

This project is provided as-is for educational and administrative purposes.

## 🤝 Contributing

Contributions are welcome! Feel free to:
- Report bugs
- Suggest new features
- Submit pull requests
- Improve documentation

## 📧 Support

For issues, questions, or suggestions, please create an issue in the repository.

## 🎯 Roadmap

Future features under consideration:
- [ ] Traceroute functionality
- [ ] Ping monitoring
- [ ] Network speed testing
- [ ] Export results to CSV/JSON
- [ ] Dark mode theme
- [ ] Scheduled scans
- [ ] Email notifications
- [ ] Custom service detection

---

**Made with ❤️ for network administrators and IT professionals**

*Network Tool Pro - Your All-in-One Networking Toolkit for Windows*

# Deployment Guide for Network Tool Pro

This guide explains how to build and deploy Network Tool Pro for end users.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Building the Application](#building-the-application)
- [Deployment Options](#deployment-options)
- [Testing the Build](#testing-the-build)
- [Distribution](#distribution)
- [Troubleshooting Build Issues](#troubleshooting-build-issues)

---

## Prerequisites

### Development Machine Requirements
- Windows 10 or Windows 11
- .NET 6.0 SDK or later ([Download](https://dotnet.microsoft.com/download/dotnet/6.0))
- 2GB free disk space (for build artifacts)

### Verify Installation
```batch
dotnet --version
```
Should output: 6.0.x or higher

---

## Building the Application

### Option 1: Quick Build (Recommended)

Simply run the included build script:
```batch
build.bat
```

This will:
1. Restore all NuGet packages
2. Build in Release configuration
3. Publish as self-contained single-file executable

**Output Location:**
```
bin\Release\net6.0-windows\win-x64\publish\NetworkToolPro.exe
```

### Option 2: Manual Build

#### Step 1: Restore Dependencies
```batch
dotnet restore
```

#### Step 2: Build
```batch
dotnet build -c Release
```

#### Step 3: Publish
```batch
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true
```

### Option 3: Visual Studio

1. Open `NetworkToolPro.csproj` in Visual Studio 2022
2. Select **Release** configuration
3. Build → Publish → Folder
4. Configure settings:
   - Target Framework: net6.0-windows
   - Deployment Mode: Self-contained
   - Target Runtime: win-x64
   - File publish options: Produce single file
5. Click **Publish**

---

## Deployment Options

### A. Self-Contained Single File (Recommended for Distribution)

**Pros:**
- ✅ Single .exe file
- ✅ No .NET runtime required on target machine
- ✅ Easy to distribute
- ✅ Works on any Windows 10/11 machine

**Cons:**
- ❌ Larger file size (~60-80 MB)

**Command:**
```batch
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true
```

**Best For:** Public distribution, external clients

---

### B. Framework-Dependent

**Pros:**
- ✅ Small file size (~500 KB)
- ✅ Faster updates

**Cons:**
- ❌ Requires .NET 6.0 runtime on target machine

**Command:**
```batch
dotnet publish -c Release -r win-x64 --self-contained false -p:PublishSingleFile=true
```

**Best For:** Internal deployment where .NET runtime is pre-installed

---

### C. Trimmed Self-Contained (Smallest Size)

**Pros:**
- ✅ Smaller than standard self-contained (~40-50 MB)
- ✅ No runtime required

**Cons:**
- ❌ Requires thorough testing
- ❌ May have compatibility issues

**Command:**
```batch
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:PublishTrimmed=true -p:TrimMode=link
```

**Best For:** Size-constrained environments (tested deployments)

---

### D. ReadyToRun (Faster Startup)

**Pros:**
- ✅ Faster application startup
- ✅ Better performance

**Cons:**
- ❌ Slightly larger file size

**Command:**
```batch
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:PublishReadyToRun=true
```

**Best For:** Enterprise deployment where performance is critical

---

## Target Runtimes

### Windows 64-bit (Most Common)
```batch
-r win-x64
```

### Windows 32-bit
```batch
-r win-x86
```

### Windows ARM64 (Surface Pro X, etc.)
```batch
-r win-arm64
```

---

## Testing the Build

### Pre-Deployment Testing

#### 1. Basic Functionality Test
```
✓ Application launches without errors
✓ All three tabs are accessible
✓ No exceptions in Event Viewer
```

#### 2. Port Scanner Test
```
Target: localhost
Ports: 80,443,8080
Expected: Scan completes without errors
```

#### 3. IP Information Test
```
Expected:
- Public IP is retrieved
- Local adapters are listed
- No exceptions
```

#### 4. DNS Configuration Test (As Administrator)
```
Steps:
1. Select adapter
2. Click Cloudflare preset
3. Apply settings
Expected: Success message, DNS updated
```

#### 5. Error Handling Test
```
Test invalid inputs:
- Port scanner with invalid host
- DNS with invalid IP
Expected: Friendly error messages, no crashes
```

---

## Distribution

### Option 1: Simple ZIP Distribution

1. **Create deployment folder:**
   ```batch
   mkdir NetworkToolPro-v1.0.0
   ```

2. **Copy files:**
   ```
   NetworkToolPro-v1.0.0/
   ├── NetworkToolPro.exe
   ├── README.md
   ├── QUICK_START.md
   └── USER_GUIDE.md
   ```

3. **Create ZIP:**
   ```batch
   powershell Compress-Archive -Path NetworkToolPro-v1.0.0 -DestinationPath NetworkToolPro-v1.0.0-win-x64.zip
   ```

4. **Distribute:**
   - Upload to GitHub Releases
   - Share via file hosting
   - Internal file server

---

### Option 2: Installer (Advanced)

For enterprise deployment, consider creating an installer using:

#### Using Inno Setup (Free)
1. Download [Inno Setup](https://jrsoftware.org/isinfo.php)
2. Create installer script
3. Include application and documentation
4. Generate .exe installer

#### Using WiX Toolset (Advanced)
1. Install WiX Toolset
2. Create WiX project
3. Add application files
4. Build MSI package

---

### Option 3: ClickOnce Deployment

For automatic updates:

```batch
dotnet publish -c Release -p:PublishProfile=ClickOnceProfile
```

Configure in Visual Studio:
- Publish → ClickOnce
- Set installation URL
- Enable automatic updates

---

## File Size Comparison

| Build Type | Size | Runtime Required |
|------------|------|------------------|
| Self-contained | ~70 MB | No |
| Self-contained + Trimmed | ~45 MB | No |
| Self-contained + R2R | ~75 MB | No |
| Framework-dependent | ~500 KB | Yes (.NET 6.0) |

---

## Troubleshooting Build Issues

### Error: "The SDK 'Microsoft.NET.Sdk' could not be found"

**Cause:** .NET SDK not installed

**Solution:**
```batch
# Download and install .NET 6.0 SDK
https://dotnet.microsoft.com/download/dotnet/6.0
```

---

### Error: "System.Management package not found"

**Cause:** NuGet packages not restored

**Solution:**
```batch
dotnet restore
```

---

### Error: "Windows Forms not supported on this platform"

**Cause:** Trying to build on non-Windows platform

**Solution:**
- Build on Windows machine
- Or use Windows build agent in CI/CD

---

### Error: "Unable to extract native libraries"

**Cause:** Incorrect publish settings

**Solution:**
Add this parameter:
```batch
-p:IncludeNativeLibrariesForSelfExtract=true
```

---

### Build succeeds but .exe doesn't run

**Possible Causes:**

1. **Missing Visual C++ Redistributable**
   - Download and install: [VC++ Redist](https://aka.ms/vs/17/release/vc_redist.x64.exe)

2. **Antivirus blocking**
   - Add exception for the .exe
   - Sign the executable (see below)

3. **Wrong runtime target**
   - Verify you're using `win-x64` for 64-bit Windows
   - Use `win-x86` for 32-bit Windows

---

### Large file size issue

**Solutions:**

1. **Use trimming:**
   ```batch
   -p:PublishTrimmed=true
   ```

2. **Framework-dependent:**
   ```batch
   --self-contained false
   ```

3. **Compression:**
   - Use UPX (not recommended for production)
   - Distribute as ZIP

---

## Code Signing (Optional but Recommended)

### Why Sign?
- Prevents "Unknown Publisher" warnings
- Builds trust with users
- Required for some enterprise deployments

### How to Sign:

1. **Obtain Code Signing Certificate**
   - Purchase from: DigiCert, Sectigo, GlobalSign
   - Or use self-signed (for internal use only)

2. **Sign the executable:**
   ```batch
   signtool sign /f certificate.pfx /p password /t http://timestamp.digicert.com NetworkToolPro.exe
   ```

3. **Verify signature:**
   ```batch
   signtool verify /pa NetworkToolPro.exe
   ```

---

## Continuous Integration / Continuous Deployment

### GitHub Actions Example

```yaml
name: Build and Release

on:
  push:
    tags:
      - 'v*'

jobs:
  build:
    runs-on: windows-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup .NET
      uses: actions/setup-dotnet@v3
      with:
        dotnet-version: 6.0.x
    
    - name: Restore dependencies
      run: dotnet restore
    
    - name: Build
      run: dotnet build -c Release --no-restore
    
    - name: Publish
      run: dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true
    
    - name: Upload artifact
      uses: actions/upload-artifact@v3
      with:
        name: NetworkToolPro
        path: bin/Release/net6.0-windows/win-x64/publish/NetworkToolPro.exe
```

---

## Deployment Checklist

Before distributing:

- [ ] Build in Release configuration
- [ ] Test all features on clean Windows machine
- [ ] Include documentation (README, QUICK_START)
- [ ] Verify file size is reasonable
- [ ] Check for antivirus false positives
- [ ] Sign executable (if possible)
- [ ] Create version number/tag
- [ ] Write release notes
- [ ] Test on Windows 10 and Windows 11
- [ ] Verify administrator rights prompts work
- [ ] Check all error messages are user-friendly

---

## Support and Updates

### Version Numbering
Use Semantic Versioning:
- MAJOR.MINOR.PATCH (e.g., 1.0.0)
- Update in `NetworkToolPro.csproj`:
  ```xml
  <Version>1.0.0</Version>
  ```

### Release Notes Template
```markdown
## Version 1.0.0 - 2024-01-15

### Features
- Port scanning with multi-threading
- IP information display
- DNS configuration management

### Bug Fixes
- None (initial release)

### Known Issues
- None

### System Requirements
- Windows 10 or later
- Administrator rights for DNS configuration
```

---

## Additional Resources

- [.NET Publishing Documentation](https://docs.microsoft.com/en-us/dotnet/core/deploying/)
- [Windows Forms Deployment](https://docs.microsoft.com/en-us/dotnet/desktop/winforms/deployment/)
- [Code Signing Best Practices](https://docs.microsoft.com/en-us/windows/win32/seccrypto/cryptography-tools)

---

**Ready to deploy!** Follow the appropriate option above based on your distribution needs.

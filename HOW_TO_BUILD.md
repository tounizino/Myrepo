# 🔨 How to Build Your Executable

## Important Note
**This repository doesn't include pre-built .exe files in the source code.** This is a best practice because:
- Binary files shouldn't be in git repositories
- Each user should build from source or download from releases
- GitHub Actions automatically builds releases when tags are pushed

## Three Ways to Get the Executable

### Option 1: Download from GitHub Releases (Easiest)
Once you push a tag to GitHub, the Actions workflow will automatically build the executable.

**Steps:**
1. Push your code to GitHub
2. Create and push a tag: `git tag v1.0.0 && git push origin v1.0.0`
3. GitHub Actions will automatically build the .exe
4. Download from the Releases page

### Option 2: Build Locally on Windows (Recommended)
If you have a Windows machine with .NET 6.0 SDK:

**Quick Method:**
```batch
# Simply double-click this file:
build.bat

# The .exe will be in:
# bin\Release\net6.0-windows\win-x64\publish\NetworkToolPro.exe
```

**Manual Method:**
```batch
# Open PowerShell or Command Prompt
cd path\to\project

# Restore packages
dotnet restore

# Build and publish
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true

# Find your .exe at:
# bin\Release\net6.0-windows\win-x64\publish\NetworkToolPro.exe
```

### Option 3: Use GitHub Actions to Build
You can trigger a build without creating a release:

1. Go to your GitHub repository
2. Click "Actions" tab
3. Select "Build and Release" workflow
4. Click "Run workflow"
5. Download the artifact when complete

## Prerequisites for Local Building

### Install .NET 6.0 SDK
1. Download from: https://dotnet.microsoft.com/download/dotnet/6.0
2. Install the SDK (not just runtime)
3. Verify installation:
   ```batch
   dotnet --version
   ```
   Should show: 6.0.x or higher

### Verify Your Environment
```batch
# Check if dotnet is installed
dotnet --version

# Check if you can build
dotnet build

# If successful, proceed with build.bat
```

## Troubleshooting

### "dotnet command not found"
**Solution:** Install .NET 6.0 SDK from Microsoft's website

### "System.Management package not found"
**Solution:** Run `dotnet restore` first

### "The project targets .NET 6.0-windows"
**Solution:** This is expected. Build on Windows or use GitHub Actions

### Build succeeds but no .exe found
**Solution:** Check the exact path:
```
bin\Release\net6.0-windows\win-x64\publish\NetworkToolPro.exe
```

## Quick Start Script

Save this as `quick-build.bat`:

```batch
@echo off
echo Checking .NET installation...
dotnet --version
if %errorlevel% neq 0 (
    echo ERROR: .NET 6.0 SDK not found!
    echo Download from: https://dotnet.microsoft.com/download/dotnet/6.0
    pause
    exit /b 1
)

echo.
echo Building Network Tool Pro...
echo.

dotnet restore
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo SUCCESS! Executable created at:
    echo bin\Release\net6.0-windows\win-x64\publish\NetworkToolPro.exe
    echo ========================================
    echo.
    explorer bin\Release\net6.0-windows\win-x64\publish
) else (
    echo.
    echo ========================================
    echo BUILD FAILED! Check errors above.
    echo ========================================
)

pause
```

## Creating a Release on GitHub

### Step 1: Commit and Push
```batch
git add .
git commit -m "feat: Network Tool Pro v1.0.0"
git push origin main
```

### Step 2: Create and Push Tag
```batch
git tag v1.0.0
git push origin v1.0.0
```

### Step 3: Wait for GitHub Actions
- Go to your repository's "Actions" tab
- Watch the build process (takes 3-5 minutes)
- When complete, check the "Releases" page
- Download NetworkToolPro-x64.exe

## File Sizes to Expect

After building, your executable will be:
- **Self-contained (win-x64):** ~70-80 MB
- **Self-contained (win-x86):** ~65-75 MB
- **Framework-dependent:** ~500 KB (requires .NET runtime)

The larger size is because it includes the entire .NET runtime.

## Distribution

Once built, you can:
1. Share the .exe directly (no installation needed)
2. Create a ZIP with exe + documentation
3. Upload to file sharing services
4. Distribute via GitHub Releases (recommended)

## Next Steps

1. **Build the executable** using one of the methods above
2. **Test it thoroughly** on a clean Windows machine
3. **Create a release** on GitHub
4. **Share with users** via the releases page

---

**Need help?** Check BUILD_INSTRUCTIONS.md for more details or open an issue on GitHub.

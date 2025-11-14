# Build Instructions for Network Tool Pro

## Quick Build (Windows)

### Option 1: Using the Build Script
Simply double-click `build.bat` or run it from command prompt:
```batch
build.bat
```

### Option 2: Manual Build Commands
```batch
dotnet restore
dotnet build -c Release
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true
```

## Output Location
After building, the executable will be located at:
```
bin\Release\net6.0-windows\win-x64\publish\NetworkToolPro.exe
```

## Build Configurations

### Self-Contained (Recommended)
This creates a single .exe file that includes the .NET runtime. Users don't need to install .NET separately.
```batch
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true
```

### Framework-Dependent (Smaller File)
This requires users to have .NET 6.0 runtime installed on their system.
```batch
dotnet publish -c Release -r win-x64 --self-contained false -p:PublishSingleFile=true
```

## Target Runtimes

### Windows 64-bit (Most Common)
```batch
-r win-x64
```

### Windows 32-bit
```batch
-r win-x86
```

### Windows ARM64
```batch
-r win-arm64
```

## Additional Build Options

### With Trimming (Smaller Size)
```batch
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:PublishTrimmed=true
```

### With ReadyToRun (Faster Startup)
```batch
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:PublishReadyToRun=true
```

## Troubleshooting

### Error: "The SDK 'Microsoft.NET.Sdk' could not be found"
**Solution**: Install the .NET 6.0 SDK from https://dotnet.microsoft.com/download

### Error: "System.Management package not found"
**Solution**: Run `dotnet restore` first

### Build succeeds but .exe doesn't run
**Solution**: 
- Check if you have .NET 6.0 runtime installed (for framework-dependent builds)
- Try building as self-contained
- Run from command line to see error messages

## Testing the Build

After building, test the application:
1. Navigate to the output folder
2. Right-click `NetworkToolPro.exe` → "Run as Administrator"
3. Test all three tabs:
   - Port Scanner (scan localhost:80,443)
   - IP Information (should load automatically)
   - DNS Configuration (requires admin rights)

## Distribution

To distribute the application:
1. Use the self-contained build
2. Include the README.md for user instructions
3. Optional: Create a ZIP file with the .exe and README
4. Users can run the .exe directly without installation

## File Size Expectations

- **Self-contained, single file**: ~60-80 MB
- **Self-contained, trimmed**: ~40-50 MB
- **Framework-dependent**: ~500 KB

The larger size for self-contained builds is because it includes the entire .NET runtime.

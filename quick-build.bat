@echo off
setlocal enabledelayedexpansion

echo ===============================================
echo    Network Tool Pro - Quick Build Script
echo ===============================================
echo.

REM Check if .NET SDK is installed
echo [1/5] Checking .NET SDK installation...
dotnet --version >nul 2>&1
if %errorlevel% neq 0 (
    echo.
    echo [ERROR] .NET 6.0 SDK is not installed!
    echo.
    echo Please install it from:
    echo https://dotnet.microsoft.com/download/dotnet/6.0
    echo.
    echo Make sure to install the SDK, not just the runtime.
    echo.
    pause
    exit /b 1
)

for /f "tokens=*" %%i in ('dotnet --version') do set DOTNET_VERSION=%%i
echo Found .NET SDK version: %DOTNET_VERSION%
echo.

REM Restore dependencies
echo [2/5] Restoring NuGet packages...
dotnet restore --verbosity quiet
if %errorlevel% neq 0 (
    echo [ERROR] Failed to restore packages!
    pause
    exit /b 1
)
echo Packages restored successfully.
echo.

REM Build the project
echo [3/5] Building in Release mode...
dotnet build -c Release --no-restore --verbosity quiet
if %errorlevel% neq 0 (
    echo [ERROR] Build failed!
    pause
    exit /b 1
)
echo Build completed successfully.
echo.

REM Publish self-contained executable
echo [4/5] Publishing self-contained executable...
echo This may take a minute...
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true --verbosity quiet
if %errorlevel% neq 0 (
    echo [ERROR] Publish failed!
    pause
    exit /b 1
)
echo Published successfully.
echo.

REM Check if file exists
set OUTPUT_PATH=bin\Release\net6.0-windows\win-x64\publish\NetworkToolPro.exe
if exist "%OUTPUT_PATH%" (
    echo [5/5] Getting file information...
    for %%A in ("%OUTPUT_PATH%") do set FILE_SIZE=%%~zA
    
    REM Convert to MB
    set /a FILE_SIZE_MB=!FILE_SIZE! / 1048576
    
    echo.
    echo ===============================================
    echo            BUILD SUCCESSFUL!
    echo ===============================================
    echo.
    echo Executable Location:
    echo %OUTPUT_PATH%
    echo.
    echo File Size: ~!FILE_SIZE_MB! MB
    echo.
    echo What's next?
    echo 1. Test the application by running the .exe
    echo 2. Right-click and "Run as Administrator" for DNS features
    echo 3. See USER_GUIDE.md for usage instructions
    echo.
    echo Opening folder in Explorer...
    explorer "bin\Release\net6.0-windows\win-x64\publish"
) else (
    echo [ERROR] Build completed but executable not found!
    echo Expected location: %OUTPUT_PATH%
)

echo.
pause

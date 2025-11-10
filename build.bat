@echo off
echo ========================================
echo Network Tool Pro - Build Script
echo ========================================
echo.

echo Restoring dependencies...
dotnet restore
if %errorlevel% neq 0 exit /b %errorlevel%

echo.
echo Building project...
dotnet build -c Release
if %errorlevel% neq 0 exit /b %errorlevel%

echo.
echo Publishing self-contained executable...
dotnet publish -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true
if %errorlevel% neq 0 exit /b %errorlevel%

echo.
echo ========================================
echo Build completed successfully!
echo ========================================
echo.
echo Executable location:
echo bin\Release\net6.0-windows\win-x64\publish\NetworkToolPro.exe
echo.
pause

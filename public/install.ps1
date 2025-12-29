# Portex Installer for Windows

$ErrorActionPreference = 'Stop'

$Version = "latest"
$BaseUrl = "https://portex.space/bin"
$BinaryName = "portex-windows-amd64.exe"
$TargetName = "portex.exe"

# Install directory: C:\Users\<User>\.portex\bin
$InstallDir = Join-Path $env:USERPROFILE ".portex\bin"

Write-Host "Installing Portex..." -ForegroundColor Cyan

# Create directory
if (!(Test-Path $InstallDir)) {
    New-Item -ItemType Directory -Force -Path $InstallDir | Out-Null
}

# Download
$DownloadUrl = "$BaseUrl/$BinaryName"
$OutputFile = Join-Path $InstallDir $TargetName

Write-Host "Downloading from $DownloadUrl..."
Invoke-WebRequest -Uri $DownloadUrl -OutFile $OutputFile

if (!(Test-Path $OutputFile)) {
    Write-Error "Download failed."
}

Write-Host "Download successful." -ForegroundColor Green

# Add to PATH
$UserPath = [Environment]::GetEnvironmentVariable("Path", [EnvironmentVariableTarget]::User)

if ($UserPath -notlike "*$InstallDir*") {
    Write-Host "Adding $InstallDir to PATH..."
    [Environment]::SetEnvironmentVariable("Path", "$UserPath;$InstallDir", [EnvironmentVariableTarget]::User)
    $env:Path += ";$InstallDir"
    Write-Host "Path updated." -ForegroundColor Green
    Write-Host "Please restart your terminal to use 'portex' command." -ForegroundColor Yellow
} else {
    Write-Host "Path already configured." -ForegroundColor Green
}

Write-Host "Installation complete! Run 'portex --help' to get started." -ForegroundColor Cyan

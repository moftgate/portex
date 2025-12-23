#!/bin/bash

# Portex Installer

set -e

# Detect OS
OS="$(uname -s)"
case "${OS}" in
    Linux*)     OS=linux;;
    Darwin*)    OS=darwin;;
    CYGWIN*)    OS=windows;;
    MINGW*)     OS=windows;;
    *)          echo "Unsupported operating system: ${OS}"; exit 1;;
esac

# Detect Architecture
ARCH="$(uname -m)"
case "${ARCH}" in
    x86_64)    ARCH=amd64;;
    aarch64)   ARCH=arm64;;
    arm64)     ARCH=arm64;;
    *)         echo "Unsupported architecture: ${ARCH}"; exit 1;;
esac

# Determine binary name
BINARY_NAME="portex-${OS}-${ARCH}"
if [ "$OS" = "windows" ]; then
    BINARY_NAME="${BINARY_NAME}.exe"
fi

# Download URL
DOWNLOAD_URL="https://portex.space/bin/${BINARY_NAME}"
INSTALL_DIR="/usr/local/bin"
TARGET_PATH="${INSTALL_DIR}/portex"

echo "Downloading Portex for ${OS}/${ARCH}..."

if [ "$OS" = "windows" ]; then
    echo "Automatic installation on Windows via bash is not fully supported yet."
    echo "Please download the binary manually from: ${DOWNLOAD_URL}"
    exit 1
fi

# Check for sudo/root permissions for installation
if [ ! -w "$INSTALL_DIR" ]; then
    echo "Sudo permissions required to install to ${INSTALL_DIR}"
    sudo curl -fsSL "$DOWNLOAD_URL" -o "$TARGET_PATH"
    sudo chmod +x "$TARGET_PATH"
else
    curl -fsSL "$DOWNLOAD_URL" -o "$TARGET_PATH"
    chmod +x "$TARGET_PATH"
fi

echo "Successfully installed Portex to ${TARGET_PATH}"
echo "Run 'portex --help' to get started."

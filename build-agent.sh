#!/bin/bash

# Portex Agent Build Script
# Builds the agent for all supported platforms and copies them to the public distribution folder.

set -e

# Versions
VERSION="0.7.1"
DIST_DIR="dist"
PUBLIC_BIN_DIR="public/bin"

echo "🚀 Building Portex Agent v${VERSION}..."

# Ensure directories exist
mkdir -p $DIST_DIR
mkdir -p $PUBLIC_BIN_DIR

# 1. Linux amd64
echo "📦 Building for Linux (amd64)..."
(cd agent && GOOS=linux GOARCH=amd64 go build -ldflags "-s -w" -o "../${DIST_DIR}/portex-linux-amd64" cmd/agent/main.go)

# 2. macOS amd64 (Intel)
echo "🍎 Building for macOS (Intel)..."
(cd agent && GOOS=darwin GOARCH=amd64 go build -ldflags "-s -w" -o "../${DIST_DIR}/portex-darwin-amd64" cmd/agent/main.go)

# 3. macOS arm64 (Apple Silicon)
echo "🍎 Building for macOS (Apple Silicon)..."
(cd agent && GOOS=darwin GOARCH=arm64 go build -ldflags "-s -w" -o "../${DIST_DIR}/portex-darwin-arm64" cmd/agent/main.go)

# 4. Windows amd64
echo "🪟 Building for Windows (x64)..."
(cd agent && GOOS=windows GOARCH=amd64 go build -ldflags "-s -w" -o "../${DIST_DIR}/portex-windows-amd64.exe" cmd/agent/main.go)

# Copy to public folder
echo "🚚 Copying binaries to ${PUBLIC_BIN_DIR}..."
mv "${DIST_DIR}"/* "${PUBLIC_BIN_DIR}/"

echo "✅ Build complete! Binaries are ready in '${PUBLIC_BIN_DIR}'"
ls -lh "${PUBLIC_BIN_DIR}"

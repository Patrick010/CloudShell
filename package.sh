#!/bin/bash
set -e # Exit immediately if a command exits with a non-zero status.

APP_NAME="nextshell"
VERSION="1.0.0"
ARCHIVE_NAME="${APP_NAME}-${VERSION}.zip"

echo "--- NextShell Packaging Script ---"

# 1. Check for required commands
if ! command -v composer &> /dev/null
then
    echo "ERROR: 'composer' command not found. Please install Composer and ensure it's in your PATH."
    exit 1
fi

if ! command -v zip &> /dev/null
then
    echo "ERROR: 'zip' command not found. Please install zip."
    exit 1
fi

# Ensure we are in the script's directory
cd "$(dirname "$0")"

if [ ! -d "$APP_NAME" ]; then
    echo "ERROR: Directory './${APP_NAME}' not found. Make sure you are running this script from the project root."
    exit 1
fi

echo "[1/3] Installing PHP dependencies with Composer..."
(cd "./${APP_NAME}" && composer install --no-dev --optimize-autoloader)

echo "[2/3] Preparing for packaging..."
# The final zip should contain the contents of the app directory at the root of the zip.

echo "[3/3] Creating the final archive: ${ARCHIVE_NAME}..."
(cd "./${APP_NAME}" && zip -r "../${ARCHIVE_NAME}" . -x ".git/*" ".DS_Store" "*/.DS_Store" "../package.sh")

echo ""
echo "---"
echo "SUCCESS: Package created at '${ARCHIVE_NAME}'"
echo "You can now upload this file to your Nextcloud instance."
echo "---"

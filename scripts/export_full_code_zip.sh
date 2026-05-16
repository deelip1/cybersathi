#!/usr/bin/env bash
set -euo pipefail
ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
OUT_NAME="CyberSathi-FullCode.zip"
OUT_PATH="${ROOT_DIR}/${OUT_NAME}"

cd "$ROOT_DIR"
rm -f "$OUT_PATH"

zip -r "$OUT_PATH" . \
  -x ".git/*" \
  -x "*.zip" \
  -x "node_modules/*" \
  -x "mobile-app/node_modules/*" \
  -x "vendor/*" \
  -x "uploads/*" \
  -x "database/*.sqlite*" \
  -x "__pycache__/*"

echo "Created: $OUT_PATH"

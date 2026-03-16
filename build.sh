#!/bin/bash
# =============================================================================
#  build.sh  –  Creates a ready-to-upload ZIP for shared hosting (Hostinger)
#  Usage: bash build.sh
#  Output: restaurant-saas-<date>.zip
# =============================================================================

set -e

PACKAGE_NAME="restaurant-saas-$(date +%Y%m%d).zip"
EXCLUDE_PATTERNS=(
    ".git"
    ".git/*"
    "*.zip"
    "build.sh"
    "deploy.sh"
    "install.sh"
    ".env"
    "database/database.sqlite"
    "storage/logs/*.log"
    "storage/framework/cache/data/*"
    "storage/framework/sessions/*"
    "storage/framework/views/*"
    "bootstrap/cache/*.php"
    "node_modules"
    "node_modules/*"
    ".DS_Store"
    "Thumbs.db"
)

echo "============================================"
echo "  Restaurant SaaS – Build Package"
echo "============================================"
echo ""

# ── 1. Install composer dependencies ──────────────────────────────────────────
echo "[1/3] Installing composer dependencies (no-dev, optimized)..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "      Done."
echo ""

# ── 2. Clear cached files that should not ship ────────────────────────────────
echo "[2/3] Clearing cached bootstrap files..."
php artisan config:clear  2>/dev/null || true
php artisan cache:clear   2>/dev/null || true
php artisan view:clear    2>/dev/null || true
php artisan route:clear   2>/dev/null || true
echo "      Done."
echo ""

# ── 3. Build ZIP ──────────────────────────────────────────────────────────────
echo "[3/3] Creating $PACKAGE_NAME ..."

# Build exclude flags for zip
EXCLUDE_FLAGS=()
for p in "${EXCLUDE_PATTERNS[@]}"; do
    EXCLUDE_FLAGS+=("--exclude=./$p")
done

zip -r "$PACKAGE_NAME" . "${EXCLUDE_FLAGS[@]}" -q

echo "      Done."
echo ""
echo "============================================"
echo "  Package ready:  $PACKAGE_NAME"
echo "  Size:           $(du -sh "$PACKAGE_NAME" | cut -f1)"
echo "============================================"
echo ""
echo "  Upload instructions:"
echo "  ─────────────────────────────────────────"
echo "  1. Log in to Hostinger hPanel"
echo "  2. Go to File Manager → your domain folder"
echo "  3. Upload $PACKAGE_NAME"
echo "  4. Extract it (right-click → Extract)"
echo "  5. Make sure public/ contents are in public_html/"
echo "     OR point the domain's Document Root to: <upload-folder>/public"
echo "  6. Visit https://yourdomain.com/  → installer starts automatically"
echo "  ─────────────────────────────────────────"
echo ""

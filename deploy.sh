#!/usr/bin/env bash
# =============================================================================
# deploy.sh — سكريبت النشر الكامل لمنصة Restaurant SaaS
# الاستخدام: bash deploy.sh [--fresh]
#   --fresh  : يعيد تشغيل الـ migrations من الصفر (تحذير: يمسح البيانات)
# =============================================================================

set -e  # أوقف السكريبت فوراً عند أي خطأ

APP_DIR="/var/www/restaurant"
PHP="php8.3"
ARTISAN="$PHP $APP_DIR/artisan"
COMPOSER="composer"
FRESH=false

# ── معالجة الخيارات ──────────────────────────────────────────────────────────
for arg in "$@"; do
    [[ "$arg" == "--fresh" ]] && FRESH=true
done

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " Restaurant SaaS — Deployment Script"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

cd "$APP_DIR"

# ── 1. وضع الصيانة ───────────────────────────────────────────────────────────
echo "▶ [1/10] Enabling maintenance mode..."
$ARTISAN down --refresh=15 --retry=10

# ── 2. سحب آخر التغييرات ─────────────────────────────────────────────────────
echo "▶ [2/10] Pulling latest code..."
git pull origin main --ff-only

# ── 3. تثبيت الـ dependencies ────────────────────────────────────────────────
echo "▶ [3/10] Installing Composer dependencies..."
$COMPOSER install \
    --no-interaction \
    --no-dev \
    --optimize-autoloader \
    --prefer-dist

# ── 4. توليد مفتاح التطبيق (إذا لم يكن موجوداً) ──────────────────────────────
if grep -q "APP_KEY=$" .env 2>/dev/null || ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "▶ [4/10] Generating application key..."
    $ARTISAN key:generate --force
else
    echo "▶ [4/10] App key already exists, skipping."
fi

# ── 5. تشغيل الـ Migrations ───────────────────────────────────────────────────
echo "▶ [5/10] Running database migrations..."
if [[ "$FRESH" == "true" ]]; then
    echo "   WARNING: Running --migrate:fresh (all data will be lost!)"
    $ARTISAN migrate:fresh --seed --force
else
    $ARTISAN migrate --force
fi

# ── 6. ربط Storage ────────────────────────────────────────────────────────────
echo "▶ [6/10] Linking storage..."
$ARTISAN storage:link --force 2>/dev/null || true

# ── 7. تحسين الأداء (Cache) ───────────────────────────────────────────────────
echo "▶ [7/10] Optimizing application..."
$ARTISAN config:cache
$ARTISAN route:cache
$ARTISAN view:cache
$ARTISAN event:cache

# ── 8. ضبط صلاحيات الملفات ───────────────────────────────────────────────────
echo "▶ [8/10] Setting file permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── 9. إعادة تشغيل Queue Worker ──────────────────────────────────────────────
echo "▶ [9/10] Restarting queue workers..."
$ARTISAN queue:restart

# ── 10. رفع وضع الصيانة ──────────────────────────────────────────────────────
echo "▶ [10/10] Disabling maintenance mode..."
$ARTISAN up

echo ""
echo "✔ Deployment complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

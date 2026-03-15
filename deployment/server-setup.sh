#!/usr/bin/env bash
# =============================================================================
# server-setup.sh — إعداد السيرفر من الصفر (Ubuntu 22.04 / 24.04)
# شغّل هذا مرة واحدة فقط على السيرفر الجديد
# الاستخدام: sudo bash server-setup.sh
# =============================================================================

set -e

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " Server Setup — Restaurant SaaS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# ── 1. تحديث النظام ──────────────────────────────────────────────────────────
echo "▶ [1] Updating system packages..."
apt-get update -qq && apt-get upgrade -y -qq

# ── 2. تثبيت PHP 8.3 + Extensions ────────────────────────────────────────────
echo "▶ [2] Installing PHP 8.3..."
apt-get install -y -qq software-properties-common
add-apt-repository ppa:ondrej/php -y
apt-get update -qq
apt-get install -y -qq \
    php8.3-fpm \
    php8.3-cli \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-bcmath \
    php8.3-curl \
    php8.3-gd \
    php8.3-zip \
    php8.3-redis \
    php8.3-intl \
    php8.3-tokenizer

# ── 3. تثبيت Nginx ────────────────────────────────────────────────────────────
echo "▶ [3] Installing Nginx..."
apt-get install -y -qq nginx

# ── 4. تثبيت MySQL 8 ─────────────────────────────────────────────────────────
echo "▶ [4] Installing MySQL 8..."
apt-get install -y -qq mysql-server

# ── 5. تثبيت Redis ───────────────────────────────────────────────────────────
echo "▶ [5] Installing Redis..."
apt-get install -y -qq redis-server
systemctl enable redis-server

# ── 6. تثبيت Composer ────────────────────────────────────────────────────────
echo "▶ [6] Installing Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ── 7. تثبيت Supervisor ──────────────────────────────────────────────────────
echo "▶ [7] Installing Supervisor..."
apt-get install -y -qq supervisor
systemctl enable supervisor

# ── 8. تثبيت Certbot (SSL) ────────────────────────────────────────────────────
echo "▶ [8] Installing Certbot..."
apt-get install -y -qq certbot python3-certbot-nginx

# ── 9. إنشاء مجلد التطبيق ────────────────────────────────────────────────────
echo "▶ [9] Creating application directory..."
mkdir -p /var/www/restaurant
chown www-data:www-data /var/www/restaurant

# ── 10. ضبط PHP-FPM ──────────────────────────────────────────────────────────
echo "▶ [10] Configuring PHP..."
# رفع حجم الرفع
sed -i "s/upload_max_filesize = .*/upload_max_filesize = 10M/" /etc/php/8.3/fpm/php.ini
sed -i "s/post_max_size = .*/post_max_size = 12M/"             /etc/php/8.3/fpm/php.ini
sed -i "s/memory_limit = .*/memory_limit = 256M/"              /etc/php/8.3/fpm/php.ini
sed -i "s/max_execution_time = .*/max_execution_time = 120/"   /etc/php/8.3/fpm/php.ini

systemctl restart php8.3-fpm

echo ""
echo "✔ Server setup complete!"
echo ""
echo "Next steps:"
echo "  1. Clone repo: git clone <repo-url> /var/www/restaurant"
echo "  2. Copy config: cp deployment/.env.production.example /var/www/restaurant/.env"
echo "  3. Edit .env with your values"
echo "  4. Run: bash /var/www/restaurant/deploy.sh --fresh"
echo "  5. Copy Nginx config: cp deployment/nginx.conf /etc/nginx/sites-available/restaurant"
echo "  6. Enable site: ln -s /etc/nginx/sites-available/restaurant /etc/nginx/sites-enabled/"
echo "  7. Get SSL cert: certbot --nginx -d yourdomain.com"
echo "  8. Copy Supervisor: cp deployment/supervisor.conf /etc/supervisor/conf.d/restaurant-worker.conf"
echo "  9. Reload supervisor: supervisorctl reread && supervisorctl update"
echo " 10. Add cron: * * * * * www-data php /var/www/restaurant/artisan schedule:run"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

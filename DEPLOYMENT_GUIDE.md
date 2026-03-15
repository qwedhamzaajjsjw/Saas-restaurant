# دليل النشر الشامل - Restaurant SaaS
## من الصفر إلى الإنتاج خطوة بخطوة

---

## الفهرس
1. [المتطلبات الأساسية](#المتطلبات-الأساسية)
2. [الاستضافة المشتركة - Hostinger](#الاستضافة-المشتركة---hostinger)
3. [VPS - Ubuntu/Debian](#vps---ubuntudebian)
4. [مشاكل شائعة وحلولها](#مشاكل-شائعة-وحلولها)

---

## المتطلبات الأساسية

| المتطلب | الحد الأدنى |
|---------|------------|
| PHP | 8.2 أو أحدث |
| MySQL | 5.7 أو MariaDB 10.3+ |
| Composer | 2.x |
| امتدادات PHP | pdo, pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, zip |
| مساحة الديسك | 500MB على الأقل |
| الذاكرة | 256MB PHP memory_limit |

---

## الاستضافة المشتركة - Hostinger

### الخطوة 0: تجهيز المشروع محلياً (على جهازك)

قبل رفع أي شيء، ابنِ المشروع محلياً:

```bash
# داخل مجلد المشروع على جهازك
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

هذا يُنشئ:
- مجلد `vendor/` (مكتبات PHP)
- مجلد `public/build/` (ملفات CSS/JS المُجمَّعة)

### الخطوة 1: رفع الملفات

**الطريقة أ: عبر File Manager في hPanel**

1. اذهب إلى hPanel → **Files** → **File Manager**
2. انتقل إلى `/home/u149018080/domains/مجالك.com/`
3. أنشئ مجلداً جديداً اسمه `restaurant`
4. ارفع جميع ملفات المشروع داخل `restaurant/` **ماعدا** هذه المجلدات (ثقيلة ولا داعي لها):
   - `.git/`
   - `node_modules/`
5. تأكد أن `vendor/` و `public/build/` موجودان (رفعتهما في الخطوة 0)

**الطريقة ب: عبر SSH (أسرع وأفضل)**

```bash
# على جهازك المحلي - ضغط المشروع
cd /path/to/your/project
tar --exclude='.git' --exclude='node_modules' --exclude='*.zip' \
    -czf restaurant.tar.gz .

# رفع الملف المضغوط إلى السيرفر
scp restaurant.tar.gz u149018080@fr-int-web934.hostinger.com:/home/u149018080/domains/مجالك.com/

# الاتصال بالسيرفر
ssh u149018080@fr-int-web934.hostinger.com

# فك الضغط
mkdir -p /home/u149018080/domains/مجالك.com/restaurant
cd /home/u149018080/domains/مجالك.com/restaurant
tar -xzf ../restaurant.tar.gz
```

### الخطوة 2: ربط public_html بـ Laravel

Laravel يضع ملفاته العامة في مجلد `public/`. الاستضافة المشتركة تقرأ من `public_html/`.
الحل: نجعل `public_html/index.php` يُشير إلى `restaurant/public/`.

**أولاً: انسخ ملفات public إلى public_html:**

```bash
# على SSH في السيرفر
DOMAIN_PATH="/home/u149018080/domains/مجالك.com"

# انسخ .htaccess و index.php من public/
cp $DOMAIN_PATH/restaurant/public/.htaccess $DOMAIN_PATH/public_html/.htaccess
cp $DOMAIN_PATH/restaurant/public/index.php  $DOMAIN_PATH/public_html/index.php
cp $DOMAIN_PATH/restaurant/public/favicon.ico $DOMAIN_PATH/public_html/favicon.ico 2>/dev/null || true
cp $DOMAIN_PATH/restaurant/public/robots.txt  $DOMAIN_PATH/public_html/robots.txt 2>/dev/null || true

# انسخ مجلد build (CSS/JS)
cp -r $DOMAIN_PATH/restaurant/public/build $DOMAIN_PATH/public_html/build
```

**ثانياً: عدّل `public_html/index.php` ليُشير للمسار الصحيح:**

افتح `public_html/index.php` وعدّل السطرين:

```php
// قبل التعديل:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// بعد التعديل (أضف restaurant في المسار):
require __DIR__.'/../restaurant/vendor/autoload.php';
$app = require_once __DIR__.'/../restaurant/bootstrap/app.php';
```

**أو باستخدام sed مباشرة:**

```bash
DOMAIN_PATH="/home/u149018080/domains/مجالك.com"

sed -i "s|__DIR__.'/../vendor/autoload.php'|__DIR__.'/../restaurant/vendor/autoload.php'|" \
    $DOMAIN_PATH/public_html/index.php

sed -i "s|__DIR__.'/../bootstrap/app.php'|__DIR__.'/../restaurant/bootstrap/app.php'|" \
    $DOMAIN_PATH/public_html/index.php
```

### الخطوة 3: إنشاء قاعدة البيانات

1. في hPanel → **Databases** → **MySQL Databases**
2. أنشئ قاعدة بيانات جديدة (سجّل الاسم)
3. أنشئ مستخدم جديد بكلمة مرور قوية (سجّلها)
4. اربط المستخدم بقاعدة البيانات وأعطه **جميع الصلاحيات**

> **مهم:** في Hostinger الاستضافة المشتركة، اسم قاعدة البيانات والمستخدم يكون بصيغة:
> `u149018080_اسم` (يضيف prefix تلقائياً)

### الخطوة 4: إعداد ملف .env

```bash
cd /home/u149018080/domains/مجالك.com/restaurant

# انسخ من المثال
cp .env.example .env

# اضبط الـ drivers لتعمل بدون DB أثناء التثبيت
sed -i 's/SESSION_DRIVER=database/SESSION_DRIVER=file/' .env
sed -i 's/CACHE_STORE=database/CACHE_STORE=file/' .env
sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/' .env

# ولّد مفتاح التطبيق
/opt/alt/php84/usr/bin/php artisan key:generate
```

### الخطوة 5: ضبط الصلاحيات

```bash
cd /home/u149018080/domains/مجالك.com/restaurant

chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 644 .env

# تأكد أن مجلدات sessions و cache موجودة
mkdir -p storage/framework/sessions
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/views
chmod -R 775 storage/framework
```

### الخطوة 6: ضبط PHP في Hostinger

في Hostinger، بعض الإعدادات تحتاج تُضبط يدوياً:

**أ) تغيير إصدار PHP:**
- hPanel → **Advanced** → **PHP Configuration** → اختر **PHP 8.2** أو **8.3**

**ب) إنشاء ملف `.htaccess` في `public_html/`** (إذا لم يكن موجوداً أو غير عامل):

```bash
cat > /home/u149018080/domains/مجالك.com/public_html/.htaccess << 'HTACCESS'
Options -MultiViews -Indexes
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Handle Laravel routing
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
HTACCESS
```

### الخطوة 7: تشغيل المثبّت

افتح المتصفح وانتقل إلى: `https://مجالك.com`

سيتحول تلقائياً إلى `/install`. اتبع الخطوات:

1. **Requirements** - فحص المتطلبات (كلها يجب أن تكون ✅)
2. **Database** - أدخل بيانات MySQL التي أنشأتها في الخطوة 3:
   - Host: `127.0.0.1`
   - Port: `3306`
   - Database: `u149018080_اسم_قاعدتك`
   - Username: `u149018080_اسم_المستخدم`
   - Password: كلمة المرور
   - App URL: `https://مجالك.com`
3. **Migrate** - تشغيل جداول قاعدة البيانات
4. **Admin** - إنشاء حساب السوبر أدمن
5. **Complete** - اكتمل!

### الخطوة 8: التحقق النهائي

```bash
cd /home/u149018080/domains/مجالك.com/restaurant

# تحقق أن ملف القفل موجود
ls storage/installed.lock && echo "✅ التثبيت مكتمل" || echo "❌ لم يكتمل التثبيت"

# تحقق من .env
grep "DB_DATABASE" .env
grep "APP_URL" .env

# امسح الكاش
/opt/alt/php84/usr/bin/php artisan config:clear
/opt/alt/php84/usr/bin/php artisan cache:clear
/opt/alt/php84/usr/bin/php artisan view:clear
/opt/alt/php84/usr/bin/php artisan route:clear
```

---

## VPS - Ubuntu/Debian

### الخطوة 1: تجهيز السيرفر (Ubuntu 22.04)

```bash
# سجّل دخول كـ root
ssh root@IP_السيرفر

# تحديث النظام
apt update && apt upgrade -y

# تثبيت المتطلبات الأساسية
apt install -y curl wget git unzip zip software-properties-common
```

### الخطوة 2: تثبيت PHP 8.2

```bash
# إضافة مستودع PHP
add-apt-repository ppa:ondrej/php -y
apt update

# تثبيت PHP والامتدادات
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
    php8.2-bcmath php8.2-curl php8.2-zip php8.2-tokenizer php8.2-fileinfo \
    php8.2-json php8.2-openssl php8.2-ctype php8.2-pdo php8.2-gd \
    php8.2-intl php8.2-redis

# تحقق من الإصدار
php -v
```

### الخطوة 3: تثبيت Nginx

```bash
apt install -y nginx

# تشغيل وتفعيل عند الإقلاع
systemctl start nginx
systemctl enable nginx
```

### الخطوة 4: تثبيت MySQL

```bash
apt install -y mysql-server

# تأمين التثبيت
mysql_secure_installation

# إنشاء قاعدة البيانات والمستخدم
mysql -u root -p << 'SQL'
CREATE DATABASE restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'restaurant_user'@'localhost' IDENTIFIED BY 'كلمة_مرور_قوية_هنا';
GRANT ALL PRIVILEGES ON restaurant_db.* TO 'restaurant_user'@'localhost';
FLUSH PRIVILEGES;
SQL
```

### الخطوة 5: تثبيت Composer

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer --version
```

### الخطوة 6: رفع المشروع

```bash
# إنشاء المستخدم للتطبيق (أفضل من root)
adduser www-laravel
usermod -aG www-data www-laravel

# إنشاء مجلد المشروع
mkdir -p /var/www/restaurant
chown www-laravel:www-data /var/www/restaurant

# التبديل للمستخدم الجديد
su - www-laravel

# رفع الملفات (اختر طريقة):
# الطريقة أ: من جهازك المحلي عبر scp
# scp -r /path/to/project/* www-laravel@IP_السيرفر:/var/www/restaurant/

# الطريقة ب: من GitHub
cd /var/www/restaurant
git clone https://github.com/USERNAME/REPO.git .

# الطريقة ج: رفع ملف مضغوط
# scp restaurant.tar.gz www-laravel@IP:/var/www/
# tar -xzf /var/www/restaurant.tar.gz -C /var/www/restaurant/
```

### الخطوة 7: تثبيت مكتبات PHP

```bash
cd /var/www/restaurant

# تثبيت المكتبات (production mode)
composer install --no-dev --optimize-autoloader
```

### الخطوة 8: إعداد .env

```bash
cd /var/www/restaurant

cp .env.example .env

# عدّل القيم
nano .env
```

**القيم المهمة في .env:**
```env
APP_NAME="Restaurant SaaS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://مجالك.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurant_db
DB_USERNAME=restaurant_user
DB_PASSWORD=كلمة_مرور_قوية_هنا

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

```bash
# ولّد مفتاح التطبيق
php artisan key:generate
```

### الخطوة 9: الصلاحيات

```bash
cd /var/www/restaurant

# الصلاحيات الأساسية
chown -R www-laravel:www-data .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

# إنشاء مجلدات مهمة
mkdir -p storage/framework/{sessions,cache/data,views}
mkdir -p storage/logs
chmod -R 775 storage
```

### الخطوة 10: إعداد Nginx

```bash
# إنشاء ملف الإعدادات
nano /etc/nginx/sites-available/restaurant
```

**المحتوى:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name مجالك.com www.مجالك.com;

    # سيتحول لـ HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name مجالك.com www.مجالك.com;

    root /var/www/restaurant/public;
    index index.php;

    # SSL - سيضيفها Certbot تلقائياً
    # ssl_certificate /etc/letsencrypt/live/مجالك.com/fullchain.pem;
    # ssl_certificate_key /etc/letsencrypt/live/مجالك.com/privkey.pem;

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # منع الوصول لملفات حساسة
    location ~ /\.(?!well-known).* {
        deny all;
    }
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # ضغط الملفات الثابتة
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # حجم رفع الملفات
    client_max_body_size 50M;

    # Logs
    error_log  /var/log/nginx/restaurant-error.log;
    access_log /var/log/nginx/restaurant-access.log;
}
```

```bash
# تفعيل الموقع
ln -s /etc/nginx/sites-available/restaurant /etc/nginx/sites-enabled/

# اختبار الإعدادات
nginx -t

# إعادة تشغيل Nginx
systemctl reload nginx
```

### الخطوة 11: شهادة SSL مجانية

```bash
# تثبيت Certbot
apt install -y certbot python3-certbot-nginx

# الحصول على الشهادة
certbot --nginx -d مجالك.com -d www.مجالك.com

# التجديد التلقائي (يضيفه Certbot تلقائياً، للتحقق)
crontab -l
```

### الخطوة 12: تشغيل المثبّت

```bash
cd /var/www/restaurant

# تشغيل المثبّت يدوياً
php artisan migrate --force
php artisan db:seed --class=PlanSeeder --force

# إنشاء السوبر أدمن
php artisan tinker
```

في Tinker:
```php
App\Models\User::create([
    'name' => 'Super Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('كلمة_مرور_قوية'),
    'role' => 'super_admin',
    'is_active' => true,
    'email_verified_at' => now(),
]);
exit
```

```bash
# إنشاء ملف القفل
echo "$(date)" > storage/installed.lock

# أو شغّل المثبّت من المتصفح: https://مجالك.com/install
```

### الخطوة 13: Queue Worker (لمعالجة المهام في الخلفية)

```bash
# تثبيت Supervisor
apt install -y supervisor

# إنشاء ملف الإعداد
nano /etc/supervisor/conf.d/restaurant-worker.conf
```

**المحتوى:**
```ini
[program:restaurant-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/restaurant/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-laravel
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/restaurant-worker.log
stopwaitsecs=3600
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start restaurant-queue:*
```

### الخطوة 14: Cron Job (للمهام المجدولة)

```bash
crontab -e -u www-laravel
```

أضف:
```cron
* * * * * cd /var/www/restaurant && php artisan schedule:run >> /dev/null 2>&1
```

### الخطوة 15: تحسينات الأداء

```bash
cd /var/www/restaurant

# تحسينات Laravel للإنتاج
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

---

## مشاكل شائعة وحلولها

### خطأ 500 - Internal Server Error

```bash
# 1. تحقق من الـ log
tail -50 storage/logs/laravel.log

# 2. تحقق من وجود vendor/
ls vendor/ | head -5

# 3. تحقق من .env
cat .env | grep APP_KEY
cat .env | grep DB_

# 4. تحقق من الصلاحيات
ls -la storage/
ls -la bootstrap/cache/
```

**أسباب شائعة:**
- `vendor/` غير موجود → شغّل `composer install`
- `APP_KEY` فارغة → شغّل `php artisan key:generate`
- صلاحيات storage → شغّل `chmod -R 775 storage bootstrap/cache`

### خطأ 403 - Forbidden

```bash
# تحقق من index.php في public_html
cat public_html/index.php | head -5

# تحقق من .htaccess
cat public_html/.htaccess
```

### خطأ في قاعدة البيانات

```bash
# اختبار الاتصال
/opt/alt/php84/usr/bin/php -r "
new PDO('mysql:host=127.0.0.1;port=3306;dbname=DB_NAME', 'USER', 'PASS');
echo 'Connection OK';
"
```

### مشكلة المسارات (404 على الصفحات الداخلية)

تأكد من وجود `RewriteEngine On` في `.htaccess`:
```bash
cat public_html/.htaccess
```

إذا كان `.htaccess` لا يعمل، تأكد من تفعيل `mod_rewrite` في PHP settings.

### مشكلة build/ أو CSS/JS لا يظهر

```bash
# تحقق من وجود build
ls public/build/ | head -5

# إذا غير موجود، ابنِ محلياً ثم ارفع
npm install
npm run build
# ثم ارفع مجلد public/build/ للسيرفر
```

### لا تظهر صفحة المثبّت وبدلاً منها 404

```bash
# تحقق من middleware
cat app/Http/Middleware/CheckInstalled.php

# تحقق أن installed.lock غير موجود
ls storage/installed.lock 2>/dev/null && echo "INSTALLED" || echo "NOT INSTALLED"

# إذا أردت إعادة التثبيت
rm storage/installed.lock
```

---

## سكريبت النشر التلقائي (للـ VPS)

احفظ هذا الملف كـ `deploy.sh` وشغّله عند التحديث:

```bash
#!/bin/bash
set -e

APP_DIR="/var/www/restaurant"
PHP="php8.2"

echo "🚀 بدء النشر..."

cd $APP_DIR

echo "📦 تحديث الكود..."
git pull origin main

echo "📚 تثبيت المكتبات..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🗄️ تشغيل الـ migrations..."
$PHP artisan migrate --force

echo "🔄 مسح الكاش..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

echo "⚙️ إعادة تشغيل Queue..."
$PHP artisan queue:restart

echo "✅ اكتمل النشر!"
```

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## ملخص سريع - Hostinger

```
1. ارفع الملفات → /domains/مجالك.com/restaurant/
2. عدّل public_html/index.php (المسارات)
3. أنشئ قاعدة بيانات في hPanel
4. cp .env.example .env
5. صحح SESSION_DRIVER=file وغيره
6. php artisan key:generate
7. chmod -R 775 storage bootstrap/cache
8. افتح المتصفح → مجالك.com → اتبع المثبّت
```

## ملخص سريع - VPS

```
1. apt install nginx php8.2-fpm mysql-server composer
2. انشئ قاعدة البيانات
3. ارفع الملفات → /var/www/restaurant/
4. composer install --no-dev
5. cp .env.example .env && عدّل القيم
6. php artisan key:generate
7. chmod -R 775 storage bootstrap/cache
8. إعداد Nginx config
9. certbot --nginx
10. افتح المتصفح → اتبع المثبّت
```

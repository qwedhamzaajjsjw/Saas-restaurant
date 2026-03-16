#!/bin/bash

# ============================================================
#  Restaurant SaaS - Interactive Installer for Hostinger
# ============================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

print_header() {
    clear
    echo -e "${CYAN}${BOLD}"
    echo "  ╔══════════════════════════════════════════════╗"
    echo "  ║       Restaurant SaaS - Installer            ║"
    echo "  ╚══════════════════════════════════════════════╝"
    echo -e "${NC}"
}

print_step() {
    echo -e "\n${BLUE}${BOLD}[ $1 ]${NC} $2"
}

print_success() {
    echo -e "  ${GREEN}✔${NC} $1"
}

print_error() {
    echo -e "  ${RED}✘ خطأ:${NC} $1"
}

print_warning() {
    echo -e "  ${YELLOW}⚠${NC} $1"
}

ask() {
    local prompt="$1"
    local default="$2"
    local var_name="$3"
    local is_secret="$4"

    if [ -n "$default" ]; then
        echo -ne "  ${BOLD}${prompt}${NC} ${YELLOW}[${default}]${NC}: "
    else
        echo -ne "  ${BOLD}${prompt}${NC}: "
    fi

    if [ "$is_secret" = "true" ]; then
        read -s input
        echo
    else
        read input
    fi

    if [ -z "$input" ] && [ -n "$default" ]; then
        eval "$var_name='$default'"
    else
        eval "$var_name='$input'"
    fi
}

# ─── تحديد المسارات ─────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HOME_DIR="$(eval echo ~$(whoami))"
INSTALL_DIR="${HOME_DIR}/restaurant"
PUBLIC_HTML=""

# محاولة إيجاد public_html تلقائياً
if [ -d "${HOME_DIR}/public_html" ]; then
    # Hostinger shared hosting (public_html مباشرة في home)
    PUBLIC_HTML="${HOME_DIR}/public_html"
elif [ -d "${HOME_DIR}/domains" ]; then
    # Hostinger VPS أو نمط domains/
    FIRST_DOMAIN=$(ls "${HOME_DIR}/domains" | head -1)
    if [ -n "$FIRST_DOMAIN" ]; then
        PUBLIC_HTML="${HOME_DIR}/domains/${FIRST_DOMAIN}/public_html"
    fi
fi

# ─── الشاشة الترحيبية ───────────────────────────────────────
print_header
echo -e "  ${BOLD}مرحباً! هذا المثبت سيقوم بـ:${NC}"
echo "  • نسخ ملفات المشروع إلى السيرفر"
echo "  • إعداد قاعدة البيانات"
echo "  • ضبط إعدادات الموقع"
echo "  • تشغيل migrations تلقائياً"
echo ""
echo -e "  ${YELLOW}اضغط Enter للمتابعة...${NC}"
read

# ─── الخطوة 1: مسارات التثبيت ───────────────────────────────
print_header
print_step "1/5" "مسارات التثبيت"
echo ""

ask "مجلد تثبيت المشروع" "$INSTALL_DIR" INSTALL_DIR
ask "مسار public_html" "$PUBLIC_HTML" PUBLIC_HTML

if [ -z "$PUBLIC_HTML" ]; then
    print_error "يجب تحديد مسار public_html"
    exit 1
fi

# ─── الخطوة 2: إعدادات الموقع ───────────────────────────────
print_header
print_step "2/5" "إعدادات الموقع"
echo ""

# محاولة استخراج اسم الدومين من المسار
DETECTED_DOMAIN=$(echo "$PUBLIC_HTML" | grep -oP '(?<=/domains/)[^/]+' || echo "")

ask "اسم الموقع" "Restaurant SaaS" APP_NAME
ask "رابط الموقع (مثال: https://maktabipro.de)" "https://${DETECTED_DOMAIN}" APP_URL
ask "بيئة التشغيل (production/local)" "production" APP_ENV

# ─── الخطوة 3: قاعدة البيانات ───────────────────────────────
print_header
print_step "3/5" "إعدادات قاعدة البيانات"
echo ""
print_warning "هذه المعلومات من لوحة تحكم Hostinger → Databases"
echo ""

ask "نوع قاعدة البيانات (mysql/sqlite)" "mysql" DB_CONNECTION
ask "عنوان السيرفر" "localhost" DB_HOST
ask "رقم البورت" "3306" DB_PORT
ask "اسم قاعدة البيانات" "" DB_DATABASE
ask "اسم المستخدم" "" DB_USERNAME
ask "كلمة المرور" "" DB_PASSWORD "true"

if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    print_error "يجب إدخال اسم قاعدة البيانات واسم المستخدم"
    exit 1
fi

# ─── الخطوة 4: البريد الإلكتروني (اختياري) ──────────────────
print_header
print_step "4/5" "إعدادات البريد الإلكتروني (اختياري)"
echo ""
print_warning "اضغط Enter لتخطي هذه الخطوة"
echo ""

ask "سيرفر البريد (SMTP)" "smtp.gmail.com" MAIL_HOST
ask "بورت البريد" "587" MAIL_PORT
ask "اسم مستخدم البريد" "" MAIL_USERNAME
ask "كلمة مرور البريد" "" MAIL_PASSWORD "true"
ask "عنوان الإرسال" "noreply@${DETECTED_DOMAIN}" MAIL_FROM

# ─── الخطوة 5: تأكيد وتثبيت ─────────────────────────────────
print_header
print_step "5/5" "ملخص الإعدادات"
echo ""
echo -e "  ${BOLD}الموقع:${NC}          ${APP_URL}"
echo -e "  ${BOLD}مجلد التثبيت:${NC}    ${INSTALL_DIR}"
echo -e "  ${BOLD}public_html:${NC}     ${PUBLIC_HTML}"
echo -e "  ${BOLD}قاعدة البيانات:${NC}  ${DB_DATABASE}@${DB_HOST}"
echo -e "  ${BOLD}المستخدم:${NC}        ${DB_USERNAME}"
echo ""
echo -ne "  ${YELLOW}${BOLD}هل تريد البدء بالتثبيت؟ (y/n):${NC} "
read CONFIRM

if [ "$CONFIRM" != "y" ] && [ "$CONFIRM" != "Y" ]; then
    echo ""
    print_warning "تم إلغاء التثبيت"
    exit 0
fi

# ════════════════════════════════════════
# بدء التثبيت الفعلي
# ════════════════════════════════════════
echo ""
echo -e "${CYAN}${BOLD}═══ بدء التثبيت ═══${NC}"
echo ""

# نسخ ملفات المشروع
print_step "◆" "نسخ ملفات المشروع..."
if [ "$SCRIPT_DIR" != "$INSTALL_DIR" ]; then
    mkdir -p "$INSTALL_DIR"
    cp -r "${SCRIPT_DIR}/." "${INSTALL_DIR}/"
    print_success "تم نسخ الملفات إلى ${INSTALL_DIR}"
else
    print_success "المشروع موجود بالفعل في ${INSTALL_DIR}"
fi

cd "$INSTALL_DIR" || { print_error "لا يمكن الدخول إلى ${INSTALL_DIR}"; exit 1; }

# تثبيت Composer
print_step "◆" "تثبيت dependencies..."
if command -v composer &>/dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -3
    print_success "تم تثبيت Composer packages"
else
    print_error "Composer غير موجود! ثبّته أولاً"
    exit 1
fi

# إنشاء .env
print_step "◆" "إنشاء ملف .env..."
cat > "${INSTALL_DIR}/.env" << EOF
APP_NAME="${APP_NAME}"
APP_ENV=${APP_ENV}
APP_KEY=
APP_DEBUG=false
APP_URL=${APP_URL}

APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=${DB_CONNECTION}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=${MAIL_HOST}
MAIL_PORT=${MAIL_PORT}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_FROM_ADDRESS="${MAIL_FROM}"
MAIL_FROM_NAME="\${APP_NAME}"
EOF
print_success "تم إنشاء .env"

# توليد APP_KEY
print_step "◆" "توليد APP_KEY..."
php artisan key:generate --force
print_success "تم توليد APP_KEY"

# إعداد public_html
print_step "◆" "إعداد public_html..."
mkdir -p "$PUBLIC_HTML"
cp -r "${INSTALL_DIR}/public/." "${PUBLIC_HTML}/"

# تعديل index.php
cat > "${PUBLIC_HTML}/index.php" << PHPEOF
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// مسار المشروع
\$laravelPath = '${INSTALL_DIR}';

if (file_exists(\$laravelPath.'/vendor/autoload.php')) {
    require \$laravelPath.'/vendor/autoload.php';
}

\$app = require_once \$laravelPath.'/bootstrap/app.php';

\$app->handleRequest(Request::capture());
PHPEOF
print_success "تم إعداد public_html"

# إنشاء storage link
print_step "◆" "إنشاء storage link..."
php artisan storage:link --force 2>/dev/null
# نسخ storage بديلاً عن symlink إذا فشل
if [ ! -L "${PUBLIC_HTML}/storage" ]; then
    ln -sf "${INSTALL_DIR}/storage/app/public" "${PUBLIC_HTML}/storage" 2>/dev/null || \
    print_warning "لم يتم إنشاء storage link - افعل ذلك يدوياً"
else
    print_success "تم إنشاء storage link"
fi

# ضبط الصلاحيات
print_step "◆" "ضبط صلاحيات المجلدات..."
chmod -R 755 "${INSTALL_DIR}/storage"
chmod -R 755 "${INSTALL_DIR}/bootstrap/cache"
print_success "تم ضبط الصلاحيات"

# تشغيل migrations
print_step "◆" "تشغيل Database Migrations..."
php artisan migrate --force 2>&1
if [ $? -eq 0 ]; then
    print_success "تم تشغيل Migrations بنجاح"
else
    print_warning "فشل Migrations - تحقق من بيانات قاعدة البيانات"
fi

# تحسين الأداء
print_step "◆" "تحسين الأداء..."
php artisan config:cache 2>/dev/null && print_success "Config cached"
php artisan route:cache  2>/dev/null && print_success "Routes cached"
php artisan view:cache   2>/dev/null && print_success "Views cached"

# ─── النتيجة النهائية ────────────────────────────────────────
echo ""
echo -e "${GREEN}${BOLD}"
echo "  ╔══════════════════════════════════════════════╗"
echo "  ║       ✔  اكتمل التثبيت بنجاح!               ║"
echo "  ╚══════════════════════════════════════════════╝"
echo -e "${NC}"
echo -e "  ${BOLD}الموقع جاهز على:${NC} ${CYAN}${APP_URL}${NC}"
echo ""
echo -e "  ${YELLOW}تذكّر:${NC}"
echo "  • احذف ملف install.sh من السيرفر بعد الانتهاء"
echo "  • تأكد من إعداد SSL في لوحة Hostinger"
echo ""

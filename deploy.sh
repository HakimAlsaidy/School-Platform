#!/usr/bin/env bash
set -e

# تشغيل الميجرشن
php artisan migrate --force

# تشغيل جميع السيدرات المدمجة
php artisan db:seed --force

# تشغيل Apache في المقدمة باستخدام المسار الكامل
exec /usr/local/bin/apache2-foreground

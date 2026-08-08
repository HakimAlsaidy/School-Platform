FROM php:8.2-apache

# تثبيت الحزم والإضافات اللازمة لـ Laravel بما فيها مكتبات الـ PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# تثبيت إضافات PHP وتفعيل pdo_pgsql و pgsql للاتصال بقاعدة بيانات Render
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# تفعيل Apache Rewrite لمسارات Laravel وتحويل المجلد الرئيسي إلى public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# السماح لـ Composer بالعمل كمسؤول داخل الحاوية وتثبيت الحزم
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# ضبط الصلاحيات الكاملة لمجلدات الكاش والتخزين لمنع خطأ 500
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["apache2-foreground"]
CMD bash -c "php artisan migrate --force && apache2-foreground"

CMD bash -c "php artisan migrate --force"
CMD bash -c "php seed_accounts.php"
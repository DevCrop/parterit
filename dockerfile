ARG PHP_VERSION=8.4
FROM php:${PHP_VERSION}-apache

# 필요한 패키지 설치 및 PHP 확장 설치
RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    curl \
    gnupg \
    ca-certificates \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j4 mysqli pdo_mysql zip gd mbstring exif \
    && rm -rf /var/lib/apt/lists/*

# Apache rewrite 모듈 활성화
RUN a2enmod rewrite headers

# 내 설정으로 Apache 설정 덮어쓰기
COPY apache.conf /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# 퍼미션 정리
# RUN chown -R www-data:www-data /home/illu0423order/www/
RUN chown -h www-data:www-data /var/www/html

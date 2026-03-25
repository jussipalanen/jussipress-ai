# =============================================================
# JussiPress AI — Dockerfile
# =============================================================
ARG THEME='jussipress-theme'
ARG NODE_VERSION=22
ARG PHP_VERSION=8.4
ARG COMPOSER_VERSION=2

# =============================================================
# Stage 1 — Theme assets (Node 22)
# =============================================================
FROM node:${NODE_VERSION}-alpine AS assets
ARG THEME

WORKDIR /build

COPY web/app/themes/${THEME}/package*.json ./
# Use ci for reproducible installs when lockfile exists, install otherwise
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

COPY web/app/themes/${THEME}/ ./
RUN npm run build


# =============================================================
# Stage 2 — Root PHP vendor (Composer)
# =============================================================
FROM composer:${COMPOSER_VERSION} AS vendor

WORKDIR /app

# Separate layer: only manifests for better cache reuse
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs \
    --prefer-dist \
    --no-interaction

# Copy source and generate optimised autoloader
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative

# =============================================================
# Stage 3 — Theme PHP vendor (Composer)
# =============================================================
FROM composer:${COMPOSER_VERSION} AS theme-vendor
ARG THEME

WORKDIR /theme

COPY web/app/themes/${THEME}/composer.json \
    web/app/themes/${THEME}/composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs \
    --prefer-dist \
    --no-interaction

COPY web/app/themes/${THEME}/ ./
RUN composer dump-autoload --no-dev --classmap-authoritative

# =============================================================
# Stage 4 — Production runtime (PHP + nginx + supervisor)
# =============================================================
FROM php:${PHP_VERSION}-fpm-alpine AS production
ARG THEME

# ---- Permanent runtime libraries ----
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    curl \
    freetype \
    libjpeg-turbo \
    libpng \
    libzip \
    libxml2 \
    icu \
    imagemagick \
    oniguruma

# ---- Build-time deps (removed after compile) ----
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    icu-dev \
    imagemagick-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    gd \
    mysqli \
    pdo_mysql \
    mbstring \
    zip \
    xml \
    intl \
    exif \
    bcmath \
    opcache \
    && pecl install redis imagick \
    && docker-php-ext-enable redis imagick \
    && apk del .build-deps \
    && rm -rf /tmp/pear /var/cache/apk/*

# ---- WP-CLI ----
RUN curl -sS -o /usr/local/bin/wp \
    https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

# ---- Config files ----
COPY docker/php.ini     $PHP_INI_DIR/conf.d/wp.ini
COPY docker/nginx.conf  /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# ---- Application ----
WORKDIR /var/www/html

# Root project (vendor + WordPress core from Composer)
COPY --from=vendor /app ./

# Theme PHP vendor (override gitignored vendor)
COPY --from=theme-vendor /theme/vendor \
    ./web/app/themes/${THEME}/vendor

# Theme built assets (Vite output)
COPY --from=assets /build/public \
    ./web/app/themes/${THEME}/public

# Ensure uploads directory exists and is writable
RUN mkdir -p web/app/uploads \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} + \
    && chmod -R 775 web/app/uploads

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

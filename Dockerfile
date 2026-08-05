# ---- PHP-FPM application server ----
FROM php:8.2-fpm-alpine
WORKDIR /var/www/html

# Install OS deps + PHP extensions needed for the app
RUN apk add --no-cache \
      oniguruma-dev \
      libzip-dev \
    && docker-php-ext-install \
      pdo pdo_mysql \
      mbstring \
      zip \
      fileinfo

# Copy application source
COPY . .

# Expose FPM port (behind nginx)
EXPOSE 9000

CMD ["php-fpm"]

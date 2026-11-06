FROM php:8.2-cli

# PHP nativo MVC: extensiones para PDO/MySQL y validación UTF-8.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /lab

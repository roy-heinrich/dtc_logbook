# Pinned to the release patching CVE-2026-109618 / CVE-2026-109619; bump on PHP security releases.
FROM php:8.2.35-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    python3 \
    python3-pip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev

# Node 20 is EOL and is not patched for the Oct 2026 CVEs; 24.x is the current LTS.
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - && \
    apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Python packages used by the Excel export generator
RUN python3 -m pip install --no-cache-dir --break-system-packages openpyxl pillow

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy package files
COPY package*.json ./

# Install Node dependencies (include dev deps required for build tools like Vite)
RUN npm ci

# Copy application files
COPY . .

# Build assets
RUN npm run build

# Remove dev dependencies after build
RUN npm prune --omit=dev

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port
EXPOSE 8080

# Start application (environment variables from Render)
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

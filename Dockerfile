FROM php:8.3-apache

# Enable Apache modules and install dependencies
RUN a2enmod rewrite headers && apt-get update && apt-get install -y unzip && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
COPY server/packages /srv/frogtab-packages
WORKDIR /srv/frogtab-packages
RUN composer install --no-dev --optimize-autoloader

# Copy app files into web root
COPY app/ /var/www/html/

# Download QR libraries
RUN curl -sL -o /var/www/html/qrcode.min.js "https://unpkg.com/qrcode-generator@1.4.4/qrcode.js" \
    && curl -sL -o /var/www/html/jsqr.min.js "https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"

# Copy PHP endpoints into web root
COPY server/public/ /var/www/html/

# Create data directory for SQLite database and settings
RUN mkdir -p /data && chown www-data:www-data /data

# Configure Apache: set env vars, allow .htaccess overrides
RUN echo '\
<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>\n\
SetEnv DIR_PACKAGES "/srv/frogtab-packages"\n\
SetEnv FILE_SQLITEDB "/data/frogtab.db"\n\
SetEnv FILE_SETTINGS "/data/settings.toml"\n\
AddType text/javascript .mjs\n' > /etc/apache2/conf-available/frogtab.conf \
    && a2enconf frogtab

# Generate the root .htaccess for URL rewriting
RUN echo '\
RewriteEngine On\n\
RewriteRule ^key_([0-9a-f-]{36})\\.asc$ get-public-key.php?user_id=$1 [L]\n\
RewriteCond %{REQUEST_FILENAME}.php -f\n\
RewriteRule ^([^\\.]+)$ $1.php [L]\n\
RewriteCond %{REQUEST_FILENAME}.html -f\n\
RewriteRule ^([^\\.]+)$ $1.html [L]\n' > /var/www/html/.htaccess

# Patch HTML files for self-hosted mode
RUN sed -i 's/data-server-base="https:\/\/frogtab\.com\/"/data-server-base=""/' \
        /var/www/html/index.html \
        /var/www/html/icon-normal.html \
        /var/www/html/icon-notify.html \
        /var/www/html/help.html \
    && sed -i 's/data-location="local"/data-location="server"/' \
        /var/www/html/send.html

VOLUME /data
EXPOSE 80

WORKDIR /var/www/html

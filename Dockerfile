FROM php:8.2-cli

RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        libpng-dev \
        zip \
        unzip \
        curl \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        gd \
        zip \
        mysqli \
        pdo \
        pdo_mysql && \
    docker-php-ext-enable pdo_mysql && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install runtime dependencies
# Dependencies for PDF rendering
RUN apt-get update && apt-get install -y \
    xfonts-75dpi xfonts-base fontconfig \
    libxrender1 libfreetype6 libxext6 libx11-6 \
    libjpeg62-turbo libpng16-16 wget curl \
  && rm -rf /var/lib/apt/lists/*

# Install wkhtmltopdf Bookworm package
RUN wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox_0.12.6.1-3.bookworm_amd64.deb \
  && apt-get update \
  && apt-get install -y ./wkhtmltox_0.12.6.1-3.bookworm_amd64.deb \
  && rm -f wkhtmltox_0.12.6.1-3.bookworm_amd64.deb \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install

CMD php -S 0.0.0.0:8080 -t public

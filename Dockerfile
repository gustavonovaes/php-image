FROM php:7.4-alpine

RUN apk add --no-cache \
  zlib-dev \
  jpeg-dev \
  libpng-dev 

RUN docker-php-ext-configure gd --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd

RUN wget -O- https://getcomposer.org/installer | php -- --install-dir=bin --filename=composer
ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-fpm

# set main params
ENV APP_HOME /var/www/html
ARG UID=1000
ARG GID=1000
ENV USERNAME=www-data
ENV COMPOSER_DIR=/usr/bin/composer


### Fix permissions
RUN mkdir -p /home/$USERNAME \
    && chown $USERNAME:$USERNAME /home/$USERNAME \
    && usermod -u $UID $USERNAME -d /home/$USERNAME \
    && groupmod -g $GID $USERNAME

# install all the dependencies and enable PHP modules
RUN set -e; \
  apt-get update && apt-get upgrade -y \
    && apt-get install -y \
      procps \
      nano \
      curl \
      git \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      libzip-dev \
      locales \
      locales-all \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
      pdo_mysql \
      intl \
      opcache \
      zip \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# put php config
COPY ./php.ini /usr/local/etc/php/php.ini
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf

# install Xdebug
RUN pecl install xdebug
COPY ./xdebug.ini /usr/local/etc/php/conf.d/


# install composer
COPY --from=composer:2.3 $COMPOSER_DIR $COMPOSER_DIR
RUN chmod +x $COMPOSER_DIR
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN chown -R $USERNAME:$USERNAME $COMPOSER_DIR

# set working directory
WORKDIR $APP_HOME
USER ${USERNAME}

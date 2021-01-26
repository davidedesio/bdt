FROM php:7.4-fpm AS dev_phpfpm
LABEL MAINTAINER="Davide De Sio"
WORKDIR /var/www/html


# update the system
RUN apt update \
    && apt autoremove -y


# add local user
RUN useradd -u 1000 local


# install Symfony 4.4.x requirements
RUN apt update \
    && apt install -y software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt install -y git libzip-dev libpcre3 libpcre3-dev libxml2-dev libxslt-dev libpng-dev vim \
    && docker-php-ext-install pdo_mysql xsl zip gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer


# install xDebug
COPY ./phpfpm/xdebug/xdebug-2.9.6.tgz /usr/src/php/ext/xdebug.tgz
RUN tar -xf /usr/src/php/ext/xdebug.tgz -C /usr/src/php/ext/ && \
    rm /usr/src/php/ext/xdebug.tgz && \
    docker-php-ext-install xdebug-2.9.6
COPY ./phpfpm/xdebug/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini


# install wkhtmltopdf
RUN apt -y install wget fontconfig libfreetype6 libjpeg62-turbo-dev libxrender1 xfonts-75dpi xfonts-base \
    && wget https://sourceforge.net/projects/libpng/files/libpng15/older-releases/1.5.15/libpng-1.5.15.tar.gz/download \
    && wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.bionic_amd64.deb \
    && wget http://mirrors.edge.kernel.org/ubuntu/pool/main/libj/libjpeg-turbo/libjpeg-turbo8_2.0.3-0ubuntu1_amd64.deb -P download \
    && dpkg -i ./download/libjpeg-turbo8_2.0.3-0ubuntu1_amd64.deb \
    && mv download libpng-1.5.15.tar.gz \
    && mv libpng-1.5.15.tar.gz  /tmp && apt install \
    && dpkg -i wkhtmltox_0.12.5-1.bionic_amd64.deb && apt -f install


# load PHP configurations
COPY ./phpfpm/config/php.ini /usr/local/etc/php/php.ini

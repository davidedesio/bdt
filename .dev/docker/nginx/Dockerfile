FROM nginx:1.18 AS dev_nginx
LABEL MAINTAINER="Davide De Sio"
WORKDIR /var/www/html


# update the system
RUN apt update \
    && apt autoremove -y


# adds a local user for compatibility
RUN useradd -u 1000 local


# install vim
RUN apt install vim -y


# copy the project configuration
COPY ./nginx/config/nginx.conf /etc/nginx/
COPY ./nginx/config/default.conf /etc/nginx/conf.d/


# copy SSL certs
COPY ./nginx/ssl/certs/server.crt /etc/ssl/certs/
COPY ./nginx/ssl/certs/server.key /etc/ssl/certs/

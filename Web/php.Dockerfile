FROM debian:stable

RUN apt update && apt install -y \
    php-fpm \
    php-pdo \
    php-pgsql \
    php-common \
    php-cli

WORKDIR /var/www/html

RUN sed -i 's/listen = \/run\/php\/php8.4-fpm.sock/listen = 9000/' /etc/php/8.4/fpm/pool.d/www.conf
RUN echo "clear_env = no" >> /etc/php/8.4/fpm/pool.d/www.conf

EXPOSE 9000

CMD ["/usr/sbin/php-fpm8.4", "-F"]

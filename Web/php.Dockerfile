# Use the official Debian image as the base
FROM debian:stable

# Install PHP-FPM, necessary dependencies, and PostgreSQL driver
RUN apt update && apt install -y \
    php-fpm \
    php-pdo \
    php-pgsql \
    php-common \
    php-cli

# Set the working directory (where website files will be mounted)
WORKDIR /var/www/html

# Adjust PHP-FPM configuration to listen on TCP port 9000 (standard for Docker)
# This is necessary because the default Debian/Ubuntu config often uses a Unix socket.
RUN sed -i 's/listen = \/run\/php\/php8.4-fpm.sock/listen = 9000/' /etc/php/8.4/fpm/pool.d/www.conf
RUN echo "clear_env = no" >> /etc/php/8.4/fpm/pool.d/www.conf

# Expose the default PHP-FPM port
EXPOSE 9000

# The command to run when the container starts
CMD ["/usr/sbin/php-fpm8.4", "-F"]

# Use the official Debian image as the base
FROM debian:stable

# Update the system and install Nginx, and clean up the cache afterward
RUN apt update \
    && apt install -y nginx

# Remove the default Nginx configuration
RUN rm -rf /etc/nginx/sites-enabled/default

# Copy custom Nginx configuration file (defined in Step 3)
COPY Web/conf/nginx.conf /etc/nginx/conf.d/default.conf

# Copy the static HTML/PHP content into the directory Nginx will serve
# The destination must match the 'root' directive in nginx.conf
COPY Web/ /var/www/html/

# Expose port 80 to the outside world
EXPOSE 80

# The command to run when the container starts (Nginx in the foreground)
CMD ["nginx", "-g", "daemon off;"]

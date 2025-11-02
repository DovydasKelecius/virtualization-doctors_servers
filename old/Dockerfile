# Use the official Debian image as the base
FROM debian:stable

# Update the system and install Nginx, and clean up the cache afterward
RUN apt update \
    && apt install -y nginx

# Copy the custom Nginx configuration file into the container
# This config will define our single server block.
#COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copy the static HTML content into the directory Nginx will serve
# We will use the standard /usr/share/nginx/html for our files.
COPY . /var/www/html

# Expose port 80 to the outside world
EXPOSE 80

# The command to run when the container starts.
# This starts Nginx in the foreground (daemon off), which is required for Docker containers.
CMD ["nginx", "-g", "daemon off;"]

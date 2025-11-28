FROM debian:stable

RUN apt update \
    && apt install -y nginx

RUN rm -rf /etc/nginx/sites-enabled/default

COPY Web/conf/nginx.conf /etc/nginx/conf.d/default.conf

COPY Web/ /var/www/html/

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

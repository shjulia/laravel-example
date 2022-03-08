FROM nginx:1.10

COPY ./nginx/ssl /etc/nginx/ssl
ADD ./nginx/default.conf /etc/nginx/conf.d/default.conf
WORKDIR /var/www

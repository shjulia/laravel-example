FROM node:12.7-alpine AS node-builder

WORKDIR /var/www

COPY ./package.json ./package-lock.json ./

RUN npm install && npm rebuild node-sass

COPY ./ ./

RUN npm run prod

######################

FROM nginx:1.15-alpine

COPY ./docker/production/nginx/ssl /etc/nginx/ssl
COPY ./docker/production/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www

COPY ./public ./public
COPY --from=node-builder /var/www/public/build ./public/build

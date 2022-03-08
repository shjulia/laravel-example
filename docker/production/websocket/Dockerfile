FROM node:10.11-jessie

WORKDIR /var/www/websocket

COPY ./websocket/package.json ./

RUN npm install
RUN npm install pm2@latest -g

COPY ./websocket ./

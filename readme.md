**Environment**
- php 7.3
- mysql 5.7
- redis 3.0
- nodejs 10.11
- laravel 5.7

**For local deploy:**
- docker-compose up -d
- make web-composer
- make web-migrate
- make npm-install
- make npm-watch
- docker-compose exec php-cli php artisan db:seed

**Seeders for tests:**
- docker-compose exec php-cli php artisan db:seed --class=TestUsersSeeder
- docker-compose exec php-cli php artisan db:seed --class=ForMatchingTestSeeder
 ---
**To fill cities and zip codes table:**
- php artisan db:seed --class=CitySeeder  
- php artisan db:seed --class=AddAreaToUsers
---
**Queues and websockets for local deploy**
- QUEUE_CONNECTION=redis
- BROADCAST_DRIVER=redis
- MIX_SOCKET_SERVER_URL=http://127.0.0.1:8084
- make websocket-install
- make queue
- make websocket-start
---
**Deploy to server**
- make deploy-dev
- make deploy-live 
---
 **Restart supervisor:**  
 `sudo supervisorctl update`  
 `sudo supervisorctl restart laravel-queue-worker:*`
 `/etc/init.d/cron stop`
 `sudo supervisorctl restart cron-worker:*`
 `sudo supervisorctl restart horizon:*`  
 **Restart pm2:**  
 `pm2 restart /var/www/dev/websocket/server.js `

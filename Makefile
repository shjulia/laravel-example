docker-up:
	docker-compose up -d

web-composer:
	docker-compose exec dental-php-cli composer install

web-migrate:
	docker-compose exec dental-php-cli php artisan migrate

web-migrate-rollback:
	docker-compose exec dental-php-cli php artisan migrate:rollback

npm-install:
	docker-compose exec dental-nodejs npm install

npm-watch:
	docker-compose exec dental-nodejs npm run watch-poll

npm-prod:
	docker-compose exec dental-nodejs npm run production

perm:
	sudo chmod 777 -R bootstrap/cache && sudo chmod 777 -R storage

api-doc:
	php artisan api:doc

dump-autoload:
	docker-compose exec dental-php-cli composer dump-autoload

websocket-start:
	docker-compose exec dental-websocket-nodejs node server.js

websocket-install:
	docker-compose exec dental-websocket-nodejs npm install

queue:
	docker-compose exec dental-php-cli php artisan queue:work --tries=1

finished-shifts:
	docker-compose exec dental-php-cli php artisan shifts:finished

unit:
	docker-compose exec dental-php-cli vendor/bin/phpunit --testsuite=Unit

integration:
	docker-compose exec dental-php-cli vendor/bin/phpunit --testsuite=Integration

unit-cover:
	docker-compose exec dental-php-cli vendor/bin/phpunit --coverage-html /var/www/public/tests

restart-queues:
	php artisan cache:clear && php artisan config:clear && php artisan queue:restart && sudo supervisorctl restart laravel-queue-worker:* && /etc/init.d/redis-server restart && sudo supervisorctl restart laravel-queue-worker:*

deploy-dev:
	vendor/bin/dep deploy dev

deploy-dev-no-assets:
	vendor/bin/dep deploy-no-assets dev

deploy-live:
	vendor/bin/dep deploy live

deploy-live-no-assets:
	vendor/bin/dep deploy-no-assets live

check: code-check psalm

code-check:
	./vendor/bin/phpcs app/

code-fix:
	./vendor/bin/phpcbf app/

psalm:
	docker-compose exec dental-php-cli ./vendor/bin/psalm

build-production:
	docker build --pull --file=docker/production/nginx/Dockerfile --tag ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG} ./
	docker build --pull --file=docker/production/php/fpm/Dockerfile --tag ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG} ./
	docker build --pull --file=docker/production/php/cli/Dockerfile --tag ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG} ./
	docker build --pull --file=docker/production/websocket/Dockerfile --tag ${REGISTRY_ADDRESS}/websocket:${IMAGE_TAG} ./

push-production:
	docker push ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/websocket:${IMAGE_TAG}

docs:
	docker-compose exec dental-php-cli php phpDocumentor.phar

try-local-deploy:
	REGISTRY_ADDRESS=localhost IMAGE_TAG=0 make build-production




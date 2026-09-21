up:
	docker-compose up -d web
build:
	docker-compose up -d --build web
stop:
	docker stop $(docker ps -q) && docker rm $(docker ps -aq)
down:
	docker-compose down
reset:
	docker-compose down -v --remove-orphans
bash:
	docker-compose exec -it web bash
node:
	docker-compose exec -it node bash
npm:
	docker-compose exec -it node bash -c "npm install"
composer:
	docker-compose exec -it web bash -c "composer dump-autoload && composer install"
new:
	php scripts/project-init.php
# laravel-test
PHP Developer Test

### Prerequisites
- Docker (>= 20.x) and Docker Compose (v2) installed on your machine.
- Git.

### Quick start (development)

1. Clone the repository:
   `git clone https://github.com/Arsen-sbase/laravel-test.git`
   `cd laravel-test`

2. Prepare environment file:
   `cp src/.env.example src/.env`

*Note: this project mounts the whole `src` folder into the container (`./src:/var/www/html`), so the file `src/.env` will be present inside the container as `/var/www/html/.env`. Edit `src/.env` on your host (or /var/www/html/.env inside the container) if you need to change DB credentials or APP_URL.*

3. Build and start containers:
   `docker-compose up --build -d`

*Wait for containers to start, then verify .env is present in the app container:
docker-compose exec app ls -la /var/www/html/.env*

4. Install PHP dependencies and prepare the app (run inside app container):
open a shell in the app container
   `docker-compose exec app bash`

# inside container:
   `composer install --no-interaction --prefer-dist`
   `php artisan key:generate`
   `php artisan config:clear`
   `php artisan cache:clear`

# set permissions
   `chown -R www-data:www-data storage bootstrap/cache`
   `chmod -R 775 storage bootstrap/cache`

5. Create DB schema and seed (if migrations/seeders exist):
   `php artisan migrate --force`
   `php artisan db:seed --force`

*(Make sure the CSV file is mounted - docker-compose.yml mounts ./property-data.csv to container.)*

6. Open the app in browser: `http://localhost:8000`

### Useful commands
- View container logs:
	`docker-compose logs -f app`
	`docker-compose logs -f db`

- Stop containers:
	`docker-compose down`

- Rebuild after Dockerfile or composer changes:
	`docker-compose build --no-cache app`
	`docker-compose up -d`

- Run artisan commands without shell:
	`docker-compose exec app php artisan migrate`

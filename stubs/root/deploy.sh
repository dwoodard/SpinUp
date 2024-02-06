#!/usr/bin/env bash
# Example useage: [root of project] ./deploy.sh

# docker-compose commands

if [ ${1:-1} == 'down' ]; then
  cd laradock || exit
  docker-compose down
  cd ..
  exit 0
fi

# ----------------------------------

export COMPOSER_ALLOW_SUPERUSER=1


# DEPENDENCIES CHECKS
echo "--- php: $(php -r 'echo PHP_VERSION;') ---"
echo "--- composer: $(composer -V | awk '{ print $3 }') ---"
echo "--- npm: $(npm -v || echo 'run: npm install') ---"
echo "--- node: $(node -v || echo 'run: node install') ---"
echo "--- docker: $(docker -v | awk '{print $3}'  ) ---"
echo "--- docker-compose: $(docker-compose -v) ---"

# check if .env exist
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo '.env Added '
fi

# Check if Composer packages are installed
if [ ! -d "vendor" ]; then
  docker-compose exec -it laradock-workspace-1 sh -c 'composer -n install;'
else
  echo 'Composer packages are installed.'
fi

# check permissions of storage, if not correct, fix them
if [ ! -d "storage" ]; then
  docker-compose exec -it laradock-workspace-1 sh -c 'chmod -R 755 storage'
fi

# Check if the Laravel application's .env file exists. If not, generate an application key.
if [ ! -f ".env" ]; then
  docker-compose exec -it laradock-workspace-1 sh -c "php artisan key:generate"
fi

# Check if npm packages are installed
if [ ! -d "node_modules" ]; then
  echo 'No npm dependencies installed. Trying to run npm install.'
  docker-compose exec -it laradock-workspace-1 sh -c "npm install"
else
  echo 'npm packages are installed.'
fi

# Run Laravel migrations based on the provided argument
if [ "${1:-}" == 'fresh' ]; then
  docker-compose exec -it laradock-workspace-1 sh -c "php artisan migrate:fresh"
else
  docker-compose exec -it laradock-workspace-1 sh -c "php artisan migrate"
fi


if [ ${1:-1} == 'build' ]; then
    cd laradock || exit
    docker-compose build \
      nginx \
      mysql \
      php-worker \
      laravel-horizon \
      mailhog \
      phpmyadmin
  else
    cd laradock || exit
    docker-compose up -d \
      nginx \
      mysql \
      php-worker \
      laravel-horizon \
      mailhog \
      phpmyadmin
    cd ..

    echo '--------------------------------------------------------'
    echo '|  Website:    http://localhost                        |'
    echo '|  Phpmyadmin: http://localhost:8081 root:root         |'
    echo '|  Redis:      http://localhost:9987 laradock:laradock |'
    echo '|  Mailhog:    http://localhost:8025/                  |'
    echo '--------------------------------------------------------'
  fi


#check composer packages are installed
if [ ! -d "vendor" ]; then
  docker exec -it laradock-workspace-1 sh -c 'composer -n install;'
else
  echo 'composer was installed'
fi

# check if .env set
if [ ! -f ".env" ]; then
  docker exec -it laradock-workspace-1 sh -c "php artisan key:generate"
fi

#check npm packages are installed
if [ ! -d "node_modules" ]; then
  echo 'No dependencies installed. Trying to run npm install.'
  docker exec -it laradock-workspace-1 sh -c "npm install"
else
  echo 'npm was installed'
fi



# When all of the above checks are done we can start the application, but we need to wait for mysql to start

# check if mysql is running
wait_time=0
while ! docker exec -it laradock-mysql-1 sh -c 'mysqladmin ping --silent'; do
    tput cuu1 && tput el
    echo 'waiting for mysql to start...' $wait_time
    ((wait_time++))
    sleep 1
done

DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2-)
wait_time=0
while ! docker exec -it laradock-mysql-1 sh -c "mysql -u root -proot -e \"use $DB_DATABASE;\" > /dev/null 2>&1"; do
    tput cuu1 && tput el
    echo 'waiting for mysql migration to complete...' $wait_time
    ((wait_time++))
    sleep 1
done

# fresh
if [ ${1:-1} == 'fresh' ]; then
  docker exec -it laradock-workspace-1 sh -c "php artisan migrate:fresh"
fi

# fresh --seed
if [ ${1:-1} == 'seed' ]; then
  docker exec -it laradock-workspace-1 sh -c "php artisan migrate:fresh --seed"
fi




docker exec -it laradock-workspace-1 sh -c "php artisan migrate"

$SHELL # Open a new shell to run the application

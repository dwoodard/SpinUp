#!/usr/bin/env bash
# Example useage: [root of project] ./deploy.sh

export COMPOSER_ALLOW_SUPERUSER=1


# CHECKS

echo "--- php: $(php -r 'echo PHP_VERSION;') ---"
echo "--- composer: $(composer -V | awk '{ print $3 }') ---"
echo "--- npm: $(npm -v || echo 'run: npm install') ---"
echo "--- node: $(node -v || echo 'run: node install') ---"


# check if both .env exist
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


    echo 'Website: http://localhost'
    echo 'Phpmyadmin: http://localhost:8081 root:root'
    echo 'Redis: http://localhost:9987 laradock:laradock'
  fi


$SHELL # Open a new shell to run the application

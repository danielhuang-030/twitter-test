#!/bin/sh

# container name
container=twitter-test-app
docker_command="docker exec -i $container"

# shutdown the laravel app
$docker_command php artisan down

# update PHP dependencies
$docker_command composer install  --no-interaction --no-dev --prefer-dist
# --no-interaction Do not ask any interactive question
# --no-dev  Disables installation of require-dev packages.
# --prefer-dist  Forces installation from package dist even for dev versions.

# update database
$docker_command php artisan migrate --force
# --force  Required to run when in production.

# cache boost configuration and routes
$docker_command php artisan cache:clear
$docker_command php artisan config:cache
$docker_command php artisan route:cache

# horizon
$docker_command php artisan horizon:terminate

# octane
$docker_command php artisan octane:stop

# rise from the ashes
$docker_command php artisan up

# # npm install
# $docker_command npm install

# # npm run production
# $docker_command npm run production

echo 'Deploy finished.'

# exit
exit 0

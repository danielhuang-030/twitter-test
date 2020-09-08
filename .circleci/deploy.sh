#!/bin/sh

# container name
container=twitter-test
docker_command="docker exec -it $container"

# update source code
git pull origin master

# update PHP dependencies
$docker_command composer install  --no-interaction --no-dev --prefer-dist
# --no-interaction Do not ask any interactive question
# --no-dev  Disables installation of require-dev packages.
# --prefer-dist  Forces installation from package dist even for dev versions.

# update database
$docker_command php artisan migrate --force
# --force  Required to run when in production.

# horizon
$docker_command php artisan horizon:purge
$docker_command php artisan horizon:terminate
$docker_command php artisan queue:restart

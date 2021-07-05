### Introduction
Docker for PHP

### Including
 - [PHP 7.3 with FPM](https://hub.docker.com/_/php)
 - [MySQL 5.7](https://hub.docker.com/_/mysql)
 - [Redis](https://hub.docker.com/_/redis)
 - [phpMyAdmin](https://hub.docker.com/r/phpmyadmin/phpmyadmin)
 - [phpRedisAdmin](https://hub.docker.com/r/erikdubbelboer/phpredisadmin)
 - [Laravel Echo Server](https://github.com/tlaverdure/laravel-echo-server)
 - [Nginx](https://hub.docker.com/_/nginx)

### Usage

```shell
# start docker
docker-compose --env-file="./dockerize/.env.docker" up -d

# stop docker
docker-compose down

# docker logs
docker-compose logs -f
```

```shell
# twitter-test cli
docker exec -it twitter-test bash
```
refer to the project [README.md](https://github.com/danielhuang-030/twitter-test/blob/master/README.md) installation


### Port(default)
| service  | port-inside | port-outside  | description |
|---|---|---|---|
| web-server  | 12001 | 12001 | [twitter-test](http://localhost:12001/api), [Laravel Echo Server(WebSocket)](http://localhost:12001/ws/) |
| app-redis | 6379 | 12009 | Redis |
| app-db | 3306, 33060 | 12006 | MySQL |
| laravel-echo-server | 6001 | - | Laravel Echo Server(WebSocket) |
| twitter-test | 9000 | - | [twitter-test](https://github.com/danielhuang-030/twitter-test) |
| app-pma | 80 | 12010 | [phpMyAdmin](http://localhost:12010) |
| app-pra | 80 | 12011 | [phpRedisAdmin](http://localhost:12011) |

### Password(default)
| Service  | Username | Password  |
|---|---|---|
| app-db | root | root |

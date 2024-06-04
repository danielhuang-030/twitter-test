### Introduction
Docker for twitter-test

### Including
 - [PHP 8.2 with FPM](https://hub.docker.com/_/php)
 - [MySQL 5.7](https://hub.docker.com/_/mysql)
 - [Redis](https://hub.docker.com/_/redis)
 - [phpMyAdmin](https://hub.docker.com/r/phpmyadmin/phpmyadmin)
 - [phpRedisAdmin](https://hub.docker.com/r/erikdubbelboer/phpredisadmin)
 - [soketi](https://github.com/soketi/soketi)
 - [Nginx](https://hub.docker.com/_/nginx)

### Usage

```shell
# copy .env and setting
cp .env.example .env
vi .env

# start docker
docker-compose up -d

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
| twitter-test-web-server  | 80 | 12000 | [twitter-test](http://localhost:12000/api), [soketi(WebSocket)](http://localhost:12001), [Supervisor status](http://localhost:12008) |
| twitter-test-redis | 6379 | 12009 | Redis |
| twitter-test-db | 3306, 33060 | 12006 | MySQL |
| twitter-test-soketi | 6001 | 12001 | soketi(WebSocket) |
| twitter-test-app | 9000 | - | [twitter-test](https://github.com/danielhuang-030/twitter-test) |
| twitter-test-ri | 80 | 12010 | [RedisInsight](http://localhost:12010) |
| twitter-test-pra | 80 | 12011 | [phpRedisAdmin](http://localhost:12011) |

### Password(default)
| Service  | Username | Password  |
|---|---|---|
| twitter-test-db | root | root |
| Supervisor status | root | root |

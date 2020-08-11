FROM node:alpine

# Create app directory
RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

# Install app dependencies
COPY ./dockerize/conf/laravel-echo-server/package.json /usr/src/app/

RUN apk add --update \
    python \
    python-dev \
    py-pip \
    build-base

RUN npm install

# Bundle app source
COPY ./dockerize/conf/laravel-echo-server/laravel-echo-server.json /usr/src/app/laravel-echo-server.json

EXPOSE 6001
CMD [ "npm", "start", "--force" ]

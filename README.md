# INSHOP CRM / ERP / ECOMMERCE

Inshop CRM / ERP is powerful framework which allows to build systems for business with different workflows.
It has on board multi language support, clients management, projects & tasks, documents, simple accounting, inventory management, 
orders & invoice management, possibilities to integrate with third party software, REST API, and many other features.

https://inshopcrm.com/

![alt text](https://inshopcrm.com/static/screenshots/inshop-crm-login.png "Inshop CRM login page")

![alt text](https://inshopcrm.com/static/screenshots/inshop-crm-dashboard.png "Inshop CRM login dashboard with charts")

## Live demo
Fill free to check out our demo CRM instance

Username: demo

Password: demo

https://demo.inshopcrm.com/signin


## Main Features

 * Multi language support
 * Clients management
 * Projects & tasks
 * Calendar with events & reminders
 * Google calendar integration
 * Documents & templates
 * Multi currency support
 * Products & categories management
 * Prices and availability management
 * Possibilities for fulfillment
 * Invoice management

## Technologies

### Backend
 - PHP 7.2
 - Symfony 4
 - API Platform
 - Postgres
 - Elasticsearch
 
### CRM / ERP / ecommerce
 - VueJs, Vuex, Nuxt
 - Bootstrap
 - Docker
 - GIT


# Installation

## Using docker-compose for local testing

.env
```dotenv
PORT_API=8888
PORT_CLIENT=8080
PORT_ECOMMERCE=8081

DATABASE_NAME=api
DATABASE_USER=api
DATABASE_PASSWORD=!ChangeMe!

JWT_PASSPHRASE=!ChangeMe!
COMPOSE_PROJECT_NAME=inshop-crm
```

docker-compose.yml

```
version: '3.2'

services:
  ecommerce:
    restart: always
    image: inshopgroup/inshop-crm-ecommerce
    user: node
    working_dir: /var/www
    environment:
      NODE_ENV: production
      HOST: 0.0.0.0
    ports:
      - ${PORT_ECOMMERCE}:3000
    command: "npm start"

  client:
    restart: always
    image: inshopgroup/inshop-crm-client
    ports:
      - ${PORT_CLIENT}:80

  php:
    restart: always
    image: inshopgroup/inshop-crm-api-php-fpm
    depends_on:
      - db
    volumes:
      - files-data:/var/www/data
      - images-data:/var/www/public/images
    networks:
      - api

  nginx:
    restart: always
    image: inshopgroup/inshop-crm-api-nginx
    depends_on:
      - php
    ports:
      - ${PORT_API}:80
    volumes:
      - images-data:/var/www/images
    networks:
      - api

  db:
    restart: always
    image: postgres:9.5-alpine
    environment:
      - POSTGRES_DB=${DATABASE_NAME}
      - POSTGRES_USER=${DATABASE_USER}
      - POSTGRES_PASSWORD=${DATABASE_PASSWORD}
    volumes:
      - db-data:/var/lib/postgresql/data:rw
    networks:
      - api

  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:6.3.1
    environment:
      - cluster.name=docker-cluster
      - bootstrap.memory_lock=true
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    ulimits:
      memlock:
        soft: -1
        hard: -1
    volumes:
      - es-data:/usr/share/elasticsearch/data
    networks:
      - api
      - esnet

volumes:
  es-data: {}
  db-data: {}
  files-data: {}
  images-data: {}

networks:
    api:
    esnet:

```

## For developers

```bash
mkdir inshop-crm
cd inshop-crm

# api
git clone git@github.com:inshopgroup/inshop-crm-api.git
cd inshop-crm-api
cp .env.dist .env
docker-compose up -d
cd ..

# client
git clone git@github.com:inshopgroup/inshop-crm-client.git
cd inshop-crm-client
cp .env.dist .env
yarn install
yarn run dev
cd ..

# ecommerce
git clone git@github.com:inshopgroup/inshop-crm-ecommerce.git
cd inshop-crm-ecommerce
cp .env.dist .env
yarn install
yarn run dev
cd ..
```

## Setup database & fixtures

```bash
docker-compose exec --user=www-data php sh ./setup.sh
```

Enter pass phrase for config/jwt/private.pem: **!ChangeMe!**  

**NOTE!** described setup is only for local use!

Enjoy, after run, API will be available under [http://localhost:8888/docs](http://localhost:8888/docs)

Client - [http://localhost:8080](http://localhost:8080)
Ecommerce [http://localhost:8081](http://localhost:8081)

```
username: demo
password: demo
```

# Elastic search settings on host machine

```bash
sudo sysctl -w vm.max_map_count=262144
echo "vm.max_map_count=262144" | sudo tee -a /etc/sysctl.conf
```

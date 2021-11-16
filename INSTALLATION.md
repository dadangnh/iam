# IAM Installation Method

####Since 1.15.0, our default compose file contains two web server (caddy or nginx). As the consequences, you need to choose which one to use on deployment command

To install/ deploy this service, we support the following method:

1. [Fully Dockerized for Development](#1-fully-dockerized-for-development)
2. [Fully Dockerized for Deployment and Production](#2-fully-dockerized-for-deployment-and-production)
3. [Use Symfony console](#3-use-symfony-console)
4. [Fully use native OS services](#4-fully-use-native-os-services)

## 1. Fully Dockerized For Development
### Requirement
This method only require you to have [Docker Engine](https://docker.com) installed on the host.

### Installation
First, clone this repository:

```bash
$ git clone git@gitlab.com:dadangnh/iam.git some_dir
$ cd some_dir
```

Then, create your environment by editing `.env` and save as `.env.local` or you can use OS's environment variable or use [Symfony Secrets](https://symfony.com/doc/current/configuration/secrets.html). Create your JWT passphrase on the JWT_PASSPHRASE key.
Make sure to adjust the credentials on the environment for the Docker. You can find inside docker-compose.yaml file

Create the docker environment with caddy:
```bash
$ docker-compose up -d database redis php caddy
```

Create the docker environment with nginx:
```bash
$ docker-compose up -d database redis php nginx
```

Generate Private and public key for JWT Token (or you can use your own key and place it to config/jwt folder):

#### On Linux:

```bash
$ docker-compose exec php sh -c '
    set -e
    apk add openssl
    mkdir -p config/jwt
    jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}
    echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
'
```

#### On Windows:

```bash
> docker-compose exec php /bin/sh
```

You will enter docker shell, then run (line by line, do not paste it as a whole):

```bash
set -e
apk add openssl
mkdir -p config/jwt
export jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}
echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 --pass stdin
echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout --passin stdin
setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
exit
```

#### Install dependency

```bash
$ docker-compose exec php composer install
```

#### Migration
run the migration:
```bash
$  docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
```

#### Add default data (optional on non production)
run the following to add dummy data:
```bash
$  docker-compose exec php bin/console doctrine:fixtures:load --no-interaction
```

Now your app are ready to use:

Landing page: [https://localhost/](https://localhost/)

API Endpoint and Docs: [https://localhost/api](https://localhost/api)

Admin page: [https://localhost/admin](https://localhost/admin)

default credentials:
```bash
root:toor
admin:admin
upk_pusat:upk_pusat
```

#### Test

Unit testing also available with the following command:

```bash
$ docker-compose exec php bin/phpunit
```

## 2. Fully Dockerized for Deployment and Production
### Requirement
This method only require you to have [Docker Engine](https://docker.com) installed on the host.

### Installation
Copy your project on the server using `git clone`, `scp` or any other tool that may fit your need.
If you use GitHub, you may want to use [a deploy key](https://docs.github.com/en/free-pro-team@latest/developers/overview/managing-deploy-keys#deploy-keys).
Deploy keys are also [supported by GitLab](https://docs.gitlab.com/ee/user/project/deploy_keys/).

Example with Git:

```bash
$ git clone git@gitlab.com:dadangnh/iam.git
```

Go into the directory containing your project (`<project-name>`), and start the app in production mode (caddy):

```bash
$ SERVER_NAME=your-domain-name.example.com docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d database redis php caddy
```


Be sure to replace `your-domain-name.example.com` by your actual domain name.

Your server is up and running, and a Let's Encrypt HTTPS certificate has been automatically generated for you.
Go to `https://your-domain-name.example.com` and enjoy!

### Disabling HTTPS on Caddy

Alternatively, if you don't want to expose an HTTPS server but only an HTTP one, run the following command:

```bash
$ SERVER_NAME=:80 docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d database redis php caddy
```

### Disabling HTTPS on nginx

By default, the nginx image run both on http and https protocol, there is no redirection yet, so it can be run with:

```bash
$ SERVER_NAME=:80 docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d database redis php nginx
```

## 3. Use Symfony console
### Requirement
This method only require you to have the following tools:

1. [Docker Engine](https://docker.com) installed on the host.

2. [PHP Engine version 8.0.1 or newer](https://www.php.net) 

3. [Symfony console](https://symfony.com/download)

### Installation
First, clone this repository:

```bash
$ git clone git@gitlab.com:dadangnh/iam.git some_dir
$ cd some_dir
```

Then, create your environment by editing `.env` and save as `.env.local` or you can use OS's environment variable or use [Symfony Secrets](https://symfony.com/doc/current/configuration/secrets.html). Create your JWT passphrase on the JWT_PASSPHRASE key.
Make sure to adjust the credentials on the environment for the Docker. You can find inside docker-compose.yaml file

Create the docker environment for the database and redis:
```bash
$ docker-compose up -d database redis
```

#### Create public and private key
```bash
$ mkdir -p config/jwt
$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```


#### Install dependency

```bash
$ symfony composer install
```

#### Migration
run the migration:
```bash
$  docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
```

#### Add default data (optional on non production)
run the following to add dummy data:
```bash
$  docker-compose exec php bin/console doctrine:fixtures:load --no-interaction
```

Now your app are ready to use:

Landing page: [https://localhost:8080/](https://localhost:8080/)

API Endpoint and Docs: [https://localhost:8080/api](https://localhost:8080/api)

Admin page: [https://localhost:8080/admin](https://localhost:8080/admin)

default credentials:
```bash
root:toor
admin:admin
upk_pusat:upk_pusat
```

#### Test

Unit testing also available with the following command:

```bash
$ php bin/phpunit
```

## 4. Fully use native OS services

### Requirement
This method only require you to have the following tools:

1. [PHP Engine version 8.0.1 or newer](https://www.php.net)

2. [Symfony console](https://symfony.com/download)

3. [Postgre SQL version 13 or newer](https://www.postgresql.org/download/)

4. [Redis](https://redis.io/download)

### Installation
First, clone this repository:

```bash
$ git clone git@gitlab.com:dadangnh/iam.git some_dir
$ cd some_dir
```

Then, create your environment by editing `.env` and save as `.env.local` or you can use OS's environment variable or use [Symfony Secrets](https://symfony.com/doc/current/configuration/secrets.html). Create your JWT passphrase on the JWT_PASSPHRASE key.

#### Create public and private key
```bash
$ mkdir -p config/jwt
$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

#### Install dependency

```bash
$ symfony composer install
```

#### Migration
run the migration:
```bash
$  php bin/console doctrine:migrations:migrate --no-interaction
```

#### Add default data (optional on non production)
run the following to add dummy data:
```bash
$  php bin/console doctrine:fixtures:load --no-interaction
```

Now your app are ready to use:

Landing page: [https://localhost:8080/](https://localhost:8080/)

API Endpoint and Docs: [https://localhost:8080/api](https://localhost:8080/api)

Admin page: [https://localhost:8080/admin](https://localhost:8080/admin)

default credentials:
```bash
root:toor
admin:admin
upk_pusat:upk_pusat
```

#### Test

Unit testing also available with the following command:

```bash
$ php bin/phpunit
```

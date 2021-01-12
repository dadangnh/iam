# DJP IAM (Back-end for DJPConnect)

Source code service Identity and Access Management DJP.

## Canonical source

The canonical source of DJP IAM where all development takes place is [hosted on GitLab.com](https://gitlab.com/dadangnh/djp-iam).

## Requirements

To use this tool, you need:
*  [Docker](https://docker.com)

## Installation

First, clone this repository:

```bash
$ git clone git@gitlab.com:dadangnh/djp-iam.git some_dir
$ cd some_dir
```

Generate Private and public key for JWT Token, make sure you remember your passphrase:
```bash
$ mkdir -p config/jwt
$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

Then, create your local environment by editing `.env` and save as `.env.local` or you can use OS's environment variable or use [Symfony Secrets](https://symfony.com/doc/current/configuration/secrets.html). Put your passphrase on the JWT_PASSPHRASE key

Create the docker environment:
```bash
$ docker-compose up -d
```

```bash
$ docker-compose exec php composer install
```

Then, run (if database haven't created yet):
```bash
$  docker-compose exec php bin/console doctrine:schema:create
```

Prepopulate the database with default content:
```bash
$  docker-compose exec php bin/console doctrine:fixtures:load --no-interaction
```

(Optional) if you have installed this before, you can make migration from previous release:
```bash
$  docker-compose exec php bin/console make:migration
```

(Optional) Lastly, run the migration:
```bash
$  docker-compose exec php bin/console doctrine:migrations:migrate
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

## Test

Unit testing also available with the following command:

```bash
$ docker-compose exec php bin/phpunit
```


## Contributing

This is an open source project, and we are very happy to accept community contributions.

# License

This code is published under [GPLv3 License](LICENSE).

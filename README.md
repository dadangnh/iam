# DJP IAM (Back-end for DJPConnect)

Source code service Identity and Access Management DJP.

## Canonical source

The canonical source of DJP IAM where all development takes place is [hosted on GitLab.com](https://gitlab.com/dadangnh/djp-iam).

## Requirements

To use this tool, you need:
*  PHP Runtime | version 7.4.3 or newer is recommended.
*  Database server | we use [PostgreSQL 13](https://www.postgresql.org/), but you can use any databases supported by [Doctrine Project](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/introduction.html).
*  [Composer](https://getcomposer.org/download/).
*  [Symfony CLI](https://symfony.com/download) (Optional but recommended)
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

Turn on the database and redis (we use docker, skip if you use your own):
```bash
$ docker-compose up -d
```

After that, from inside your project directory, install the required package:
> Note: if you didn't install [Symfony CLI](https://symfony.com/download), simply change the `symfony console` to `php bin/console`

```bash
$ symfony composer install
```

Then, run (if database haven't created yet):
```bash
$ symfony console doctrine:schema:create
```

Prepopulate the database with default content:
```bash
$ symfony console doctrine;fixtures:load --no-interaction
```

(Optional) if you have installed this before, you can make migration from previous release:
```bash
$ symfony console make:migration
```

(Optional) Lastly, run the migration:
```bash
$ symfony console doctrine:migrations:migrate
```

Now your app are ready to use:
```bash
$ symfony serve -d
```

## Test

Your Application should be available on https://127.0.0.1:8000/

API Endpoint are available at https://127.0.0.1:8000/api

Admin Area are available at https://127.0.0.1:8000/admin

Unit testing also available with the following command:

```bash
$ symfony php bin/phpunit
```


## Contributing

This is an open source project, and we are very happy to accept community contributions.

# License

This code is published under [GPLv3 License](LICENSE).

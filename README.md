# DJP IAM

Source code service Identity and Access Management DJP.

## Canonical source

The canonical source of DJP IAM where all development takes place is [hosted on GitLab.com](https://gitlab.com/dadangnh/djp-iam).

## Requirements

To use this tool, you need:
*  PHP Runtime | version 7.4.3 or newer is recommended.
*  Database server | we use [PostgreSQL 12](https://www.postgresql.org/), but you can use any databases supported by [Doctrine Project](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/introduction.html).
*  [Composer](https://getcomposer.org/download/).
*  [Symfony CLI](https://symfony.com/download) (Optional but recommended)

## Installation

First, clone this repository:

```bash
$ git@gitlab.com:dadangnh/djp-iam.git
```

Then, create your local environment by editing `.env` and save as `.env.local` or you can use OS's environment variable or use [Symfony Secrets](https://symfony.com/doc/current/configuration/secrets.html)

After that, from inside your project directory, install the required package:

```bash
$ composer install
```

Turn on the database (we use docker, skip if you use your own):
```bash
$ docker-compose up -d
```


Then, run (if database haven't created yet):
```bash
$ symfony console doctrine:database:create
```
> Note: if you didn't install [Symfony CLI](https://symfony.com/download), simply change the `symfony console` to `php bin/console`


If you use PostgreSQL, you can skip this, if your database is not PostgreSQL, run the following code:
```bash
$ symfony console make:migration
```

Lastly, run the migration:
```bash
$ symfony console doctrine:migrations:migrate
```

Now your app are ready to use:
```bash
$ symfony serve -d
```

## Contributing

This is an open source project, and we are very happy to accept community contributions.

# License

This code is published under [GPLv3 License](LICENSE).

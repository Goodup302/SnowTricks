# SnowTricks
[![Maintainability](https://api.codeclimate.com/v1/badges/d74c5fb42d669da3fd0e/maintainability)](https://codeclimate.com/github/Goodup302/SnowTricks/maintainability)

Snow Tricks is a collaborative website to introduce snowboarding to the general public.

This site is created on the base of Symfony4 framework.

- [Install Producion]
```bash
git clone https://github.com/Goodup302/SnowTricks.git
composer install --no-dev --optimize-autoloader
php bin/console doctrine:database:create -n
php bin/console doctrine:schema:create -n
php bin/console cache:clear -e prod -n --no-debug
```
After, configuring environment variables:
For apache:
```ini
<VirtualHost *:80>
    # ...
    SetEnv APP_ENV "prod"
    SetEnv MAILER_URL "gmail://email:password@localhost"
    SetEnv DATABASE_URL "mysql://login:password@127.0.0.1:3306/snowtricks"
</VirtualHost>
```
For ngnix:
```ini
fastcgi_param DATABASE_URL "mysql://db_user:db_password@127.0.0.1:3306/db_name";
- Install Project : dev
```
Doc:
https://symfony.com/doc/current/deployment.html
https://symfony.com/doc/current/configuration/environment_variables.html



- [Install Development]
```bash
git clone https://github.com/Goodup302/SnowTricks.git
composer install
php bin/console doctrine:database:drop --force -n
php bin/console doctrine:database:create -n
php bin/console make:mig -n
php bin/console doctrine:schema:create -n
php bin/console doctrine:fix:load -n
```
After, configuring environment variables in file .env.local:
```bash
APP_ENV=prod
DATABASE_URL=mysql://login:password@127.0.0.1:3306/snowtricks
MAILER_URL=gmail://email:password@localhost
```


**The specification and diagrams are in the "document" folder**
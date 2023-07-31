# Geonames v2

This repository contains the Geonames Controller, built on a Symfony 6.3.1 skeleton.

## Deployment

Download/Installation

```bash
  git clone https://github.com/MathieuGTR/geonames_symfony.git
```
```bash
  composer install
```
Mettre à jour les infos DB : 
```doctrine
  DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=11&charset=utf8"
```
puis 
```
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

Main route :

```bash
  https://localhost:8000/geonames
```
## Usage/Examples
The Geoname controller has a few functions :

```php
___TODO____
```

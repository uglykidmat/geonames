# Geonames v2

"Reverse/Geocoding Webservices" : this repository contains the Geonames Controller, built on a Symfony 6.3.1 skeleton.

## Deployment

Download/Installation

```bash
  git clone https://github.com/GTRDevTeam/geonames_rd
```
```bash
  composer install
```
Update database configuration 
```doctrine
  DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=11&charset=utf8"
```
then 
```
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

Main route :

```bash
  https://localhost:8000/geonames
```
## Usage/Examples (04/08/2023)
The Geoname controller has a few functions :

### Subdivisions

Global search within database : 
```php
/geonames/search/{string $geoquery}-{string $featureCode}
```
Search for a keyword $geoquery associated with a featureCode (ADM1,ADM1H,ADM2,ADM2,ADM3,ADM3,ADM4,ADM4,ADM5,ADM5,ADMD,ADMD,LTER,PC,PCLD,PCLF,PCLH,PCLI,PCLI,PCLS,PRSH,TERR,Z,ZNB). See https://www.geonames.org/export/codes.html for more information.

Add a geonames entry to the local database :
```php
/geonames/globalgetjson/{int $geonamesId}
```
Information on a specific Geonames Id :
```php
/geonames/geonamesid/{int $geonamesId}
```
Search by postal code :
```php
/geonames/postalcodesearch/{int $postalcode}
```
Search by nearby postal code :
```php
/geonames/nearbypostalcode/{string $countrycode}-{int postalcode}
```
Search by Latitude and Longitude :
```php
/geonames/{int $lat}-{int $lng}
```
### Countries

Add all country information
```php
/geonames/country/all
```
Search for a country :
```php
/geonames/country/{string $countryCode}
```



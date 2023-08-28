# Geonames v2

"Reverse/Geocoding Webservices" : this repository contains the Geonames Controller, built on a Symfony 6.3.1 skeleton.

## Deployment

Download/Installation

```bash
  git clone https://github.com/GTRDevTeam_rd
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

Homepage :

```bash
  /
```
## Usage/Examples (04/08/2023)
The Geoname controller has a few functions :

### Subdivisions

Global search within database : 
```php
/search/{string $geoquery}-{string $featureCode}
```
Search for a keyword $geoquery associated with a featureCode (ADM1,ADM1H,ADM2,ADM2,ADM3,ADM3,ADM4,ADM4,ADM5,ADM5,ADMD,ADMD,LTER,PC,PCLD,PCLF,PCLH,PCLI,PCLI,PCLS,PRSH,TERR,Z,ZNB). See https://www.geonames.org/export/codes.html for more information.

Add a geonames entry to the local database :
```php
/globalgetjson/{int $geonamesId}
```
Information on a specific Geonames Id :
```php
/geonamesid/{int $geonamesId}
```
Search by postal code :
```php
/postalcodesearch/{int $postalcode}
```
Search by nearby postal code :
```php
/nearbypostalcode/{string $countrycode}-{int postalcode}
```
Search by Latitude and Longitude :
```php
/latLng/{int $lat}-{int $lng}
```
### Countries

Add all country information
```php
/country/all
```
Search for a country :
```php
/country/{string $countryCode}
```
Update country level (be sure you have 'geonames_country_level.json' in your 'base_data' folder) :
```php
/country/level/update
```
Information about all country-levels:
```php
/country/level/get
```
Information about a specific country (countryCode being a 2-letter string) :
```php
/country/level/get/{string $countryCode}
```

### API Search

To get information from the Geonames API, the endpoints will be under /geonamesapi/ : 

```php
/geonamesapi/postalcodesearch/{string postalcode}
```
returns a list of (10 by default) postal codes and places for the placename/postalcode query

```php
/geonamesapi/postalcodelookup/{string postalcode}-{string countrycode}
```
returns a list of (10 by default) places for the given postalcode in JSON format, sorted by postalcode,placename


### Commands

Terminal commands : 
```bash
php bin/console Latlngsearch <lat> <lng>
```
returns a json string of the Geonames location closest the latitude and longitude provided.
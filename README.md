# Geonames v2.1.6

"Reverse/Geocoding Webservices" : this repository contains the Geonames Controller, built on a Symfony 6.3.1 skeleton.

The general information can be found here : https://www.notion.so/gtrsuite/Geonames-5561d74e241c4fee8dcf0ee39c4a1221

## Deployment

#### 1. Download/Installation

```bash
  git clone https://github.com/Gatoreviews/geonames.git
```
```bash
  composer install
```
#### 2. Update database configuration in your `.env` files.
```doctrine
  DATABASE_URL="postgresql://{{db_user}}:{{db_password}}@{{127.0.0.1:5432}}/{{db_name}}?serverVersion=15&charset=utf8"
```
#### 3. then run these commands to create the database :
```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
the database should then be ready for hydration.

#### 4. Run these commands in order :

`symfony server:start -d` to start the server.

## Countries hydration
#### 1. update the countries
- basic information ( ⚠️ make sure you have the file "allCountries.json" in your /all_countries_data/ folder). This performs a purge of the "geonames_country" table and fills it up with fresh information from Geonames. As of september 2023, there were 250 entries.

```bash
php bin/console app:cu
```
#### 2. update the countries' locales (translated names)
- Countries names translated into different languages. The table "country_locale" must first be updated with geonames information :
```bash
php bin/console app:clu
```
ℹ️ This command runs a few subprocesses which manage the different files containing the Ids, since loading them all at once caused a timeout error.
Importing these files should result in about 43000 new entries in the database.

To update all the names ("locales") for a specific country, you need its geonamesId (find it here https://www.geonames.org/countries/, click on the desired country name, then on the name again, the ID will be in the URL). Then run :

```bash
php bin/console app:clsu 3017382
```
to get all of France's names, for example.

#### 3. Update country level

⚠️ make sure you have 'geonames_country_level.json' in your 'base_data' root folder :
```php
/country/level/update
```
or
```bash
php bin/console app:clvu
```

#### 4. Update GeoJson data

⚠️ Make sure the file 'geonames_geojson.json' is in the 'base_data' root folder :
```php
/geojson/update
```
or
```bash
php bin/console app:cgu
```

Will update the database entries (countries or administrative divisions) if their geonameID is found.

#### 5. Country Barycenters

⚠️ Make sure the file 'geonames_country_barycenters.json' is in the 'base_data' root folder.
Then visit : `/country/barycenters/update` and the country table will be updated accordingly.

To update a specific country's barycenter :
```php
/country/barycenter/{string countryCode}
```
or
```bash
php bin/console app:cbu
```
This script will compute the approximate barycenter of the country and update it in the database.

## Subdivisions hydration
- Update your `GEONAMES_TOKEN` variable in the correct `.env`file.
- Import/Update : 
The command line arguments are countryCode, featureCode ("ADM1","ADM2","ADM3") and "startRow" which sets the start of the geonames Response content. Example :
```bash
php bin/console app:adu FR ADM1 1
```
will yield the first level Administrative Divisions of France, starting from row 1. Updates are done by batch of 1000 entries, so the next logical steps would be to run the same command with the second argument increased by 1000, like :

```bash
php bin/console app:adu DE ADM1 1000
php bin/console app:adu IT ADM3 2000
php bin/console app:adu JP ADM2 1
php bin/console app:adu ES ADM2 1000
```
Multiple countries' ADM can be imported at once :
```bash
php bin/console app:adu DE,ES,FI,CH ADM1 1
```

Some administrative divisions have an alternative admincode.
To import them, ⚠️ make sure the file 'geonames_alternative_divisions.json' is in the 'base_data' folder, then visit
```php
/administrativedivisions/alternatives/update
```
or
```bash
php bin/console app:adaltu
```

Quick command to import multiple geonameIds :
```bash
php bin/console app:adbu {geoid},{geoid},{geoid},...
```
the Ids must be separated by a comma.

Locales update : 
```bash
php bin/console app:adlu {CountryCode}
```
This command can take a moment to execute, depending on the country's depth.

## Security information
- `/status` is publicly available.
- `/geonames/search` is accessible via a Bearer Token.
- `/geonames/api` is accessible via a Bearer Token.
- `/*` any other page is accessible via basic_auth.

## Api Documentation
`/api/doc.json` for a json output
`/api/doc` for the Nelmio API documentation
`/api` for the standard API documentation

## Usage/Examples
The Geoname controller has a few functions :

### POST endpoint
```bash
  /geonames/search
```
Handles POST requests : the content must be a JSON string following this structure (for a single entry ; if more are needed, just separate the `{}` with a `,`) :
```
[
  {
    "elt_id": "4M SP04801", #unique element id
    "country_code": "FR",   #2-letter country code. If not found in a list, returns an empty content.
    "zip_code": "30900",    #zipcode respecting the country's format
    "lat": 43.818134,       #latitude
    "lng": 4.347509         #longitude
  },
  {
    ...
  }
]
```
The search will be on the coordinates, and use the postalcode/countrycode couple to find the subdivisions.
This URL is protected by a token, if not provided you will encounter a 401 error.

### GET endpoints
Hydrate the database first, for every country code needed. Example for Japan : 
```bash
/administrativedivisions/locales/update/JP
```
then call the endpoint : 
```bash
  /administrativedivisions/api/{lang}/{countryCode}
```
The inline command for this API endpoint (JSON generation) is :
```bash
  php bin/console app:adapi {lang} {countryCode}
```

Country list : 
```bash
  /country/list/{lang}
```
A GET request on this URL will return the country list with the following information : countryCode, geonameId, name.
The name will be translated in {lang}, for example /fr, /de, etc

Example : /administrativedivisions/api/it/FR
Handles GET requests for getting all the subdivision names of a country in a specific language.

### EXPORT endpoint
```bash
  /administrativedivisions/export/{locale}/{level}
```

```bash
php bin/console app:ade {locale} {level}
```

Example :  `/administrativedivisions/export/fr/0`, `/administrativedivisions/export/it/1`
Handles GET requests for getting all the subdivisions in a specific language, filtered by level. 0 represents the countries, 1,2,3 the subdivisions.
The result page contains JSON information, and a file with the same content will be created in the `/var/geonames_export_data/` folder.

### Translations

ℹ️ "Translations" act as locales overrides. The following endpoints are used to manage these translations.
Use different HTTP methods on the translation API endpoint `/translation`:

* GET : paginated list
* POST : bulk creation
* PATCH : bulk update
* DELETE : bulk deletion

The expected content should follow this syntax, `geonameId` and `locale` are required :
```
[{
	"geonameId": "123456",
	"name": "name_in_locale_language",
	"countryCode": "FR",
	"fcode": "ADM1",
	"locale": "fr"
},
 {
	"geonameId": "123457",
	"name": "name_in_locale_language",
	"countryCode": "UK",
	"fcode": "COUNTRY",
	"locale": "de"
}]
```

Calling POST with it will create new entries, PATCH will modify existing entries (if found), and DELETE will delete them.
For an initial database hydration, just copy the JSON content from 'geonames_translation.json' in your 'base_data' folder, and use it as request on the POST endpoint.

The `/export` URL will serve a .csv file with all the current translation entries.
```php
/translation/export
```

### Subdivisions

1. To clean database entries, use `app:adp` followed by the featureCode.
```bash
php bin/console app:adp ADM1
```

2. Translations list
- Calling the endpoint `/alternatives/{locale}` (locale being a 2-letter code) will return a list of every subdivions names in the selected locale.

Global search in Symfony database : 
```php
/search/{string geoquery}-{string featureCode}
```
Search for a keyword geoquery (eg. "New York", "Chambéry", etc) associated with a featureCode (ADM1,ADM1H,ADM2,ADM2,ADM3,ADM3,ADM4,ADM4,ADM5,ADM5,ADMD,ADMD,LTER,PC,PCLD,PCLF,PCLH,PCLI,PCLI,PCLS,PRSH,TERR,Z,ZNB). See https://www.geonames.org/export/codes.html for more information.

Add a geonames entry to the local database :
```php
/globalgetjson/{int geonamesId}
```
Information on a specific Geonames Id :
```php
/geonamesid/{int geonamesId}
```
Search by postal code :
```php
/postalcodesearch/{int postalcode}
```
Search by nearby postal code :
```php
/nearbypostalcode/{string countrycode}-{int postalcode}
```
Search by Latitude and Longitude :
```php
/latLng/{int lat}-{int lng}
```
### Countries

1. Translations list
- Calling the endpoint `/countrynames/{locale}` (locale being a 2-letter code) will return a list of every country names in the selected locale.

2. Other

Search for some information on a country :
```php
/country/{string countryCode}
```
Information about all country-levels :
```php
/country/level/get
```
Information about a specific country (countryCode being a 2-letter string) :
```php
/country/level/get/{string countryCode}
```

### GeoJson information

```php
/geojson/get
```
will return the information from every entry in the database.
```php
/geojson/get/{int geonameId}
```
will return the information for a specific geonameID, if found in the database.

### API Search

To get information directly from the Geonames API, the endpoints will be under /geonamesapi/ : 

```php
/geonamesapi/postalcodesearch/{string postalcode}
```
returns a list of (10 by default) postal codes and places for the placename/postalcode query

```php
/geonamesapi/postalcodelookup/{string postalcode}-{string countrycode}
```
returns a list of places for the given postalcode in JSON format, sorted by postalcode,placename


### Commands

Terminal commands : 
```bash
php bin/console Latlngsearch <lat> <lng>
```
returns a json string of the Geonames location closest the latitude and longitude provided.

Running the tests :
```bash
php vendor/bin/phpunit --testdox
```

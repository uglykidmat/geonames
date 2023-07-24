<?php
//______________________________DB CONFIGURATION & ERROR CONFIGURATION

function createPDOforSubdiv($pdoname, $operation){
    $pdoname = new PDO('mysql:host=' . _DB_PROD_SERVER_ . ';dbname=' . _DB_PROD_NAME_. ';charset=utf8', _DB_PROD_USER_, _DB_PROD_PASSWD_);
    $pdoname->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdoname;
}

function queryPDOforSubdivDeletion($pdoname, $geonamesId){
    $pdoname = new PDO('mysql:host=' . _DB_PROD_SERVER_ . ';dbname=' . _DB_PROD_NAME_ . ';charset=utf8', _DB_PROD_USER_, _DB_PROD_PASSWD_);
    $pdoname->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdoname->query('DELETE FROM geonames_administrative_division WHERE `geonameId` =' .$geonamesId);
    echo 'DELETED ' . $geonamesId . ' FROM geonames_administrative_division<br/>';

    return $pdoname;
}

function queryPDOforSubdivInsertion($pdoname, $geonamesId, $data){
    $pdoname = new PDO('mysql:host=' . _DB_PROD_SERVER_ . ';dbname=' . _DB_PROD_NAME_ . ';charset=utf8', _DB_PROD_USER_, _DB_PROD_PASSWD_);
    $pdoname->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdoname->query('INSERT INTO geonames_administrative_division (geonameId,
    name,
    asciiName,
    toponymName,
    continentCode,
    countryCode,
    adminName1,
    adminName2,
    adminName3,
    adminName4,
    adminName5,
    adminId1,
    adminCode1,
    lat,
    lng,
    population,
    timezone_gmtOffset,
    timezone_timeZoneId,
    timezone_dstOffset,
    adminTypeName,
    fcode) VALUES  ("' .
    $data["geonameId"] . '", "' .
    $data["name"] . '", "' .
    $data["asciiName"] . '", "' .
    $data["toponymName"] . '", "' .
    $data["continentCode"] . '", "' .
    $data["countryCode"] . '", "' .
    $data["adminName1"] . '", "' .
    $data["adminName2"] . '", "' .
    $data["adminName3"] . '", "' .
    $data["adminName4"] . '", "' .
    $data["adminName5"] . '", "' .
    $data["adminId1"] . '", "' .
    $data["adminCode1"] . '", "' .
    $data["lat"] . '", "' .
    $data["lng"] . '", "' .
    $data["population"] . '", "' .
    $data["timezone"]["gmtOffset"] . '", "' .
    $data["timezone"]["timeZoneId"] . '", "' .
    $data["timezone"]["dstOffset"] . '", "' .
    $data["adminTypeName"] . '", "' .
    $data["fcode"] .'")');

    echo 'INSERTED ' . $geonamesId . ' INTO geonames_administrative_division<br/>';
}

function createPDOandQuery($pdoname, $querytype){
    //______________________________New PDO connection
    $pdoname = new PDO('mysql:host=' . _DB_PROD_SERVER_ . ';dbname=tools', _DB_PROD_USER_,_DB_PROD_PASSWD_);
    $pdoname->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

    switch ($querytype) {
        case 'geojson':
            //______________________________geojson backup
            $pdovalues = $pdoname->query('SELECT geonameId,geojson FROM geonames_administrative_division WHERE (geojson IS NOT NULL AND geojson LIKE "%geojson")')->fetchAll(PDO::FETCH_ASSOC);

            //______________________________geojson table creation
            $pdoname->query(
                'CREATE TABLE IF NOT EXISTS `countries_geonamesid_geojson` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `geonameId` int NOT NULL,
                    `geojson` varchar(255) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
            );

            //______________________________geojson table insertion
            foreach ($pdovalues as $key => $value) {
                $pdoname->query(
                    'INSERT INTO `countries_geonamesid_geojson` (`geonameId`, `geojson`) VALUES ("' .
                    $value['geonameId'] . "','" .
                    $value['geojson'] . "')"
                );
            }
            return $pdovalues;
        break;

        case 'countrylevels':
            //______________________________fetch country levels
            $pdovalues = $pdoname->query('SELECT countrycode,used_level FROM geonames_country_level')->fetchAll(PDO::FETCH_ASSOC);

            return $pdovalues;
        break;

        default:
            break;
    }

    return array('Woops! Something went wrong with the querytype');
}

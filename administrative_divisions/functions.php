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

    $insertionrequest = $pdoname->prepare('INSERT INTO geonames_administrative_division (geonameId,
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
    fcode)
    VALUES
    (
    :geonameId,
    :name,
    :asciiName,
    :toponymName,
    :continentCode,
    :countryCode,
    :adminName1,
    :adminName2,
    :adminName3,
    :adminName4,
    :adminName5,
    :adminId1,
    :adminCode1,
    :lat,
    :lng,
    :population,
    :timezone_gmtOffset,
    :timezone_timeZoneId,
    :timezone_dstOffset,
    :adminTypeName,
    :fcode
    )');

$insertionrequest->execute(
	[
	':geonameId' => $data['geonameId'],
	':name' => $data['name'],
	':asciiName' => $data['asciiName'],
	':toponymName' => $data['toponymName'],
	':continentCode' => $data['continentCode'],
	':countryCode' => $data['countryCode'],
	':adminName1' => $data['adminName1'],
	':adminName2' => $data['adminName2'],
	':adminName3' => $data['adminName3'],
	':adminName4' => $data['adminName4'],
	':adminName5' => $data['adminName5'],
	':adminId1' => $data['adminId1'],
	':adminCode1' => $data['adminCode1'],
	':lat' => strval($data['lat']),
	':lng' => strval($data['lng']),
	':population' => $data['population'],
	':timezone_gmtOffset' => $data['timezone']['gmtOffset'],
	':timezone_timeZoneId' => $data['timezone']['timeZoneId'],
	':timezone_dstOffset' => $data['timezone']['dstOffset'],
	':adminTypeName' => $data['adminTypeName'],
	':fcode' => $data['fcode']
	]);
}

function createPDOandQuery($pdoname, $querytype){
    //______________________________New PDO connection
    $pdoname = new PDO('mysql:host=' . _DB_PROD_SERVER_ . ';dbname=tools', _DB_PROD_USER_,_DB_PROD_PASSWD_);
    $pdoname->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

    switch ($querytype) {
        case 'geojson':
            
            //______________________________geojson table creation
            $pdoname->query(
                'CREATE TABLE IF NOT EXISTS `countries_geonamesid_geojson` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `geonameId` int NOT NULL,
                    `geojson` varchar(255) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
            );

            //______________________________geojson backup FROM geonames_administrative_division
            $pdovalues = $pdoname->query('SELECT geonameId,geojson FROM geonames_administrative_division WHERE (geojson IS NOT NULL AND geojson LIKE "%geojson")')->fetchAll(PDO::FETCH_ASSOC);

            //______________________________geojson table insertion
            $pdoreq = $pdoname->prepare('INSERT INTO `countries_geonamesid_geojson` (`geonameId`,`geojson`) VALUES (:geonameId,:geojson)');
            foreach ($pdovalues as $key => $value) {
                
                $pdoreq->bindParam(':geonameId', $value["geonameId"], PDO::PARAM_INT);
                $pdoreq->bindParam(':geojson', $value["geojson"], PDO::PARAM_STR);
                $pdoreq->execute();
            }

            //______________________________geojson backup FROM geonames_country
            $pdocountryvalues = $pdoname->query('SELECT geonameId,geojson FROM geonames_country WHERE (geojson IS NOT NULL AND geojson LIKE "%geojson")')->fetchAll(PDO::FETCH_ASSOC);

            //______________________________geojson table insertion FROM geonames_country
            $pdoreqcountry = $pdoname->prepare('INSERT INTO `countries_geonamesid_geojson` (`geonameId`,`geojson`) VALUES (:geonameId,:geojson)');
            foreach ($pdocountryvalues as $key => $countryvalue) {
                
                $pdoreqcountry->bindParam(':geonameId', $countryvalue["geonameId"], PDO::PARAM_INT);
                $pdoreqcountry->bindParam(':geojson', $countryvalue["geojson"], PDO::PARAM_STR);
                $pdoreqcountry->execute();
            }

            // //______________________________geojson backup FROM geonames_feature
            // $pdofeaturevalues = $pdoname->query('SELECT geonameId,geojson FROM geonames_feature WHERE (geojson IS NOT NULL AND geojson LIKE "%geojson")')->fetchAll(PDO::FETCH_ASSOC);

            // //______________________________geojson table insertion FROM geonames_feature

            // foreach ($pdocountryvalues as $key => $countryvalue) {
            //     $pdoname->query(
            //         'INSERT INTO `countries_geonamesid_geojson` (`geonameId`, `geojson`) VALUES ("' .
            //         $value["geonameId"] . '","' .
            //         $value["geojson"] . '")"'
            //     );
            // }

            //______________________________need more on return ?
            return "okay !";
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

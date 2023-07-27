<?php
require (__DIR__ . '/config/init.php');

use GuzzleHttp;

//______________________________Variables
$geonames_username = 'mathieugtr';
$maxrows = 1000;

$geonames_url_adm1 = 'http://api.geonames.org/searchJSON?maxRows=' . $maxrows . '&username=' . $geonames_username . '&featureCode=ADM1&style=FULL';
$geonames_url_adm2 = 'http://api.geonames.org/searchJSON?maxRows=' . $maxrows . '&username=' . $geonames_username . '&featureCode=ADM2&style=FULL';
$geonames_url_adm3 = 'http://api.geonames.org/searchJSON?maxRows=' . $maxrows . '&username=' . $geonames_username . '&featureCode=ADM3&style=FULL';

$geonames_url_pcli = 'http://api.geonames.org/searchJSON?maxRows=' . $maxrows . '&username=' . $geonames_username . '&featureCode=PCLI&style=FULL';
$geonames_url_cont = 'http://api.geonames.org/searchJSON?maxRows=' . $maxrows . '&username=' . $geonames_username . '&featureCode=CONT&style=FULL';

//______________________________Geonames Request

$client = new GuzzleHttp\Client();
$res = $client->request('GET', $geonames_url_adm1);
echo '<h1>Geonames Update :</h1>';
echo 'fetching ' . $geonames_url_adm1 . ' ...<br/>';

//______________________________Geonames Response
$responseArray = json_decode($res->getBody(), true);
$geonames_data = $responseArray['geonames'];

$geonamesIdsDone = 0;
//______________________________Database Insertions
foreach ($geonames_data as $key => $data) {
    echo '<h3>Current GeonameID : ' . $data["geonameId"] . '</h3>';
    echo 'queryDELETION :<br/>';
    queryPDOforSubdivDeletion('deletionPDO', $data["geonameId"]);
    echo 'queryINSERTION :<br/>';
    queryPDOforSubdivInsertion('insertionPDO', $data["geonameId"], $data);
    $geonamesIdsDone ++;
}

echo "<br/>GeonamesIdsDone : ".$geonamesIdsDone."<br/>";

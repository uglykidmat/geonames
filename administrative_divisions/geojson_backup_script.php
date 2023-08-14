<?php
//______________________________Configuration + initialization
require (__DIR__ . '/config/init.php');

//______________________________geojson backup
echo 'Countries_id_and_geojson:<br/>';

$geojsonPDO = createPDOandQuery('geojsonPDO','geojson');
print_r ($geojsonPDO);
echo '<br>';

//______________________________Database Request
$countries_level_1 = array();
$countries_level_2 = array();
$countries_level_3 = array();

$countrylevelsPDO = createPDOandQuery('countrylevelsPDO','countrylevels');

echo '<br/><br/><h2>Countrylevels</h2><br/>';
foreach ($countrylevelsPDO as $countrylevel) {

    switch ($countrylevel['used_level']) {
        case 1:
            $countries_level_1[] = $countrylevel;
            break;
        case 2:
            $countries_level_2[] = $countrylevel;
            break;
        case 3:
            $countries_level_3[] = $countrylevel;
            break;
        }
}

//______________________________Echos juste pour savoir si j'ai pas fait n'importe quoi
echo '<br/><br/><h3>Countries level 1 : </h3><br/>';
print_r($countries_level_1);
echo '<br/><br/><h3>Countries level 2 : </h3><br/>';
print_r($countries_level_2);
echo '<br/><br/><h3>Countries level 3 : </h3><br/>';
print_r($countries_level_3);

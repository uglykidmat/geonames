<?php

//_____________________Links
$testCountriesTxtUrl = pathinfo('base_data/testCountries.txt');
$testCountriesTxtLink = "./".$testCountriesTxtUrl["dirname"]."/".$testCountriesTxtUrl["basename"];

//_____________________Echodumps
echo "<pre>";
var_dump ($testCountriesTxtUrl);
echo "</pre>";
echo "testCountriesTxtLink : ".$testCountriesTxtLink."<br/><br/>";

//_____________________Country code list
$countryCodes = [];

//_____________________Open file
$testCountriesTxt = fopen("testCountries.txt", "r") or die("Impossible d'ouvrir le fichier.");

if(file_exists("testCountries.txt") && filesize("testCountries.txt") > 0){
    //_____________________Read file
    $data = fread($testCountriesTxt,filesize("testCountries.txt"));
    echo $data;
    echo "<br/><br/><br/>";

    //_____________________Try write into array
    $testCountriesArray = preg_split('/\s+/',file_get_contents("testCountries.txt",FILE_IGNORE_NEW_LINES));
        echo "<pre>";
        var_dump ($testCountriesArray);
        echo "</pre>";
    echo "<br/>".gettype($testCountriesArray)."<br/><br/>";

    //_____________________Iterate array
    foreach($testCountriesArray as $key=>$value)
    {
        $conents_arr[$key]  = rtrim($value, "\r");
    }

    }
fclose($testCountriesTxt);

?>
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
$countryCodes = ["AD","AE","AF","AG","AI","AL","AM","AO","AQ","AR","AS","AT","AU","AW","AX","AZ","BA","BB","BD","BE","BF","BG","BH","BI","BJ","BL","BM","BN","BO","BQ","BR","BS","BT","BV","BW","BY","BZ","CA","CC","CD","CF","CG","CH","CI","CK","CL","CM","CN","CO","CR","CU","CV","CW","CX","CY","CZ","DE","DJ","DK","DM","DO","DZ","EC","EE","EG","EH","ER","ES","ET","FI","FJ","FK","FM","FO","FR","GA","GB","GD","GE","GF","GG","GH","GI","GL","GM","GN","GP","GQ","GR","GS","GT","GU","GW","GY","HK","HM","HN","HR","HT","HU","ID","IE","IL","IM","IN","IO","IQ","IR","IS","IT","JE","JM","JO","JP","KE","KG","KH","KI","KM","KN","KP","KR","KW","KY","KZ","LA","LB","LC","LI","LK","LR","LS","LT","LU","LV","LY","MA","MC","MD","ME","MF","MG","MH","MK","ML","MM","MN","MO","MP","MQ","MR","MS","MT","MU","MV","MW","MX","MY","MZ","NA","NC","NE","NF","NG","NI","NL","NO","NP","NR","NU","NZ","OM","PA","PE","PF","PG","PH","PK","PL","PM","PN","PR","PS","PT","PW","PY","QA","RE","RO","RS","RU","RW","SA","SB","SC","SD","SE","SG","SH","SI","SJ","SK","SL","SM","SN","SO","SR","SS","ST","SV","SX","SY","SZ","TC","TD","TF","TG","TH","TJ","TK","TL","TM","TN","TO","TR","TT","TV","TW","TZ","UA","UG","UM","US","UY","UZ","VA","VC","VE","VG","VI","VN","VU","WF","WS","XK","YE","YT","ZA","ZM","ZW"];

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
    // foreach($testCountriesArray as $key=>$value)
    // {
    //     $conents_arr[$key]  = rtrim($value, "\r");
    // }

    }
fclose($testCountriesTxt);

?>
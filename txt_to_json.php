<?php

//_____________________Country code list
$countryCodes = ["AD" => 0,"AE" => 0,"AF" => 0,"AG" => 0,"AI" => 0,"AL" => 0,"AM" => 0,"AO" => 0,"AQ" => 0,"AR" => 0,"AS" => 0,"AT" => 0,"AU" => 0,"AW" => 0,"AX" => 0,"AZ" => 0,"BA" => 0,"BB" => 0,"BD" => 0,"BE" => 0,"BF" => 0,"BG" => 0,"BH" => 0,"BI" => 0,"BJ" => 0,"BL" => 0,"BM" => 0,"BN" => 0,"BO" => 0,"BQ" => 0,"BR" => 0,"BS" => 0,"BT" => 0,"BV" => 0,"BW" => 0,"BY" => 0,"BZ" => 0,"CA" => 0,"CC" => 0,"CD" => 0,"CF" => 0,"CG" => 0,"CH" => 0,"CI" => 0,"CK" => 0,"CL" => 0,"CM" => 0,"CN" => 0,"CO" => 0,"CR" => 0,"CU" => 0,"CV" => 0,"CW" => 0,"CX" => 0,"CY" => 0,"CZ" => 0,"DE" => 0,"DJ" => 0,"DK" => 0,"DM" => 0,"DO" => 0,"DZ" => 0,"EC" => 0,"EE" => 0,"EG" => 0,"EH" => 0,"ER" => 0,"ES" => 0,"ET" => 0,"FI" => 0,"FJ" => 0,"FK" => 0,"FM" => 0,"FO" => 0,"FR" => 0,"GA" => 0,"GB" => 0,"GD" => 0,"GE" => 0,"GF" => 0,"GG" => 0,"GH" => 0,"GI" => 0,"GL" => 0,"GM" => 0,"GN" => 0,"GP" => 0,"GQ" => 0,"GR" => 0,"GS" => 0,"GT" => 0,"GU" => 0,"GW" => 0,"GY" => 0,"HK" => 0,"HM" => 0,"HN" => 0,"HR" => 0,"HT" => 0,"HU" => 0,"ID" => 0,"IE" => 0,"IL" => 0,"IM" => 0,"IN" => 0,"IO" => 0,"IQ" => 0,"IR" => 0,"IS" => 0,"IT" => 0,"JE" => 0,"JM" => 0,"JO" => 0,"JP" => 0,"KE" => 0,"KG" => 0,"KH" => 0,"KI" => 0,"KM" => 0,"KN" => 0,"KP" => 0,"KR" => 0,"KW" => 0,"KY" => 0,"KZ" => 0,"LA" => 0,"LB" => 0,"LC" => 0,"LI" => 0,"LK" => 0,"LR" => 0,"LS" => 0,"LT" => 0,"LU" => 0,"LV" => 0,"LY" => 0,"MA" => 0,"MC" => 0,"MD" => 0,"ME" => 0,"MF" => 0,"MG" => 0,"MH" => 0,"MK" => 0,"ML" => 0,"MM" => 0,"MN" => 0,"MO" => 0,"MP" => 0,"MQ" => 0,"MR" => 0,"MS" => 0,"MT" => 0,"MU" => 0,"MV" => 0,"MW" => 0,"MX" => 0,"MY" => 0,"MZ" => 0,"NA" => 0,"NC" => 0,"NE" => 0,"NF" => 0,"NG" => 0,"NI" => 0,"NL" => 0,"NO" => 0,"NP" => 0,"NR" => 0,"NU" => 0,"NZ" => 0,"OM" => 0,"PA" => 0,"PE" => 0,"PF" => 0,"PG" => 0,"PH" => 0,"PK" => 0,"PL" => 0,"PM" => 0,"PN" => 0,"PR" => 0,"PS" => 0,"PT" => 0,"PW" => 0,"PY" => 0,"QA" => 0,"RE" => 0,"RO" => 0,"RS" => 0,"RU" => 0,"RW" => 0,"SA" => 0,"SB" => 0,"SC" => 0,"SD" => 0,"SE" => 0,"SG" => 0,"SH" => 0,"SI" => 0,"SJ" => 0,"SK" => 0,"SL" => 0,"SM" => 0,"SN" => 0,"SO" => 0,"SR" => 0,"SS" => 0,"ST" => 0,"SV" => 0,"SX" => 0,"SY" => 0,"SZ" => 0,"TC" => 0,"TD" => 0,"TF" => 0,"TG" => 0,"TH" => 0,"TJ" => 0,"TK" => 0,"TL" => 0,"TM" => 0,"TN" => 0,"TO" => 0,"TR" => 0,"TT" => 0,"TV" => 0,"TW" => 0,"TZ" => 0,"UA" => 0,"UG" => 0,"UM" => 0,"US" => 0,"UY" => 0,"UZ" => 0,"VA" => 0,"VC" => 0,"VE" => 0,"VG" => 0,"VI" => 0,"VN" => 0,"VU" => 0,"WF" => 0,"WS" => 0,"XK" => 0,"YE" => 0,"YT" => 0,"ZA" => 0,"ZM" => 0,"ZW" => 0];

//_____________________Open file
$allCountriesTxt = fopen("allCountries.txt", "r") or die("Impossible d'ouvrir le fichier.");

if(file_exists("allCountries.txt") && filesize("allCountries.txt") > 0){
    //_____________________Read file
    $data = fread($allCountriesTxt,filesize("allCountries.txt"));

    //_____________________Explode CSV into PHP array
    $allCountriesArray = explode(PHP_EOL,file_get_contents("allCountries.txt",FILE_IGNORE_NEW_LINES));

    //_____________________Loop through array
    for ($i=0; $i < count($allCountriesArray) ; $i++) { 
        //_____________________Only get the first 2 characters of each line, which are the country code
        $allCountriesArray[$i] = substr($allCountriesArray[$i], 0, 2);
        //_____________________If this country code is met in the general $countryCodes array, increment value
        if (in_array($allCountriesArray[$i],array_keys($countryCodes))){
            $countryCodes[$allCountriesArray[$i]]++;
        }
    }

    //_____________________Echoes to see what is happening
    echo "Occurrences des codeCountry :";
    echo "<pre>";
    print_r($countryCodes);
    echo "</pre>";
}

//_____________________Close file
fclose($allCountriesTxt);

//_____________________Create JSON file from the countries Array
    //_____________________Encode to JSON
    $allCountries = json_encode($countryCodes);

        echo "allCountries - JSON <br/><pre>";
        print_r($allCountries);
        echo "</pre>";

    //_____________________Write to file allCountries.json
    file_put_contents("allCountries.json",$allCountries);

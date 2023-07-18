<?php

$zipcodes = file_get_contents("usZipcodes.txt");

$zipcodesArray = explode(PHP_EOL,$zipcodes);

$tableau = [];
$tableauvalues = [];
$num=[];
$tableaukeys = [];

foreach ($zipcodesArray as $key => $value) {
    //$value = preg_split("/\t+/", $value);
    $tableau[] = preg_split("/\t+/", $value);
}

$cpAlreadyDone = [];

foreach ($tableau as $key => $value) {
    // Je récupère mes valeurs intéréssantes

        $cp = substr($tableau[$key][1],0,2);
        if (!in_array($cp,$cpAlreadyDone))
        {
            $cpAlreadyDone [] = $cp;    
            $adminCode1 = $tableau[$key][4];
            echo '"'.$cp.'":{"adminCode1":"'.$adminCode1.'"},<br/>';
        }
    
}
?>
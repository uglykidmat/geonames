<?php

$zipcodes = file_get_contents("usZipcodes.txt");

$zipcodesArray = explode(PHP_EOL,$zipcodes);

$tableau = [];
$tableauvalues = [];
$num=[];
$tableaukeys = [];

foreach ($zipcodesArray as $key => $value) {
    $tableau[] = preg_split("/\t+/", $value);
}

foreach ($tableau as $key => $value) {
    // Je récupère mes valeurs intéréssantes
    $cp = substr($tableau[$key][1],0,2);
    //echo $cp;
    $n = $tableau[$key][4];
    //Si la clé CP existe
    if (in_array($cp,array_keys($tableauvalues))){
        if (!in_array($n, $tableauvalues[$cp])){
            $tableauvalues[$cp][]= $n;
        }
    }else{
        $tableauvalues["$cp"] = array();
        $tableauvalues[$cp][] = $n;
    }
}

$tableaufinal = [];

foreach ($tableauvalues as $key => $value) {
    if (count($tableauvalues[$key])>1){
        $tableaufinal[] = $key.'":{"adminCode1":"'. implode(", ", $tableauvalues[$key]).'"},<br/>';
    }
}
print_r ($tableaufinal);

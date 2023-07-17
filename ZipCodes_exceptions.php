<?php

$zipcodes = file_get_contents("mxZipcodes.txt");

$zipcodesArray = explode(PHP_EOL,$zipcodes);

$tableau = [];
$tableauvalues = [];
$num=[];
$tableaukeys = [];

foreach ($zipcodesArray as $key => $value) {
    //$value = preg_split("/\t+/", $value);
    $tableau[] = preg_split("/\t+/", $value);
}

foreach ($tableau as $key => $value) {
    // Je récupère mes valeurs intéréssantes
    $cp = substr($tableau[$key][1],0,3);
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
    // echo $cp."=>".$n;
}

$tableaufinal = [];

foreach ($tableauvalues as $key => $value) {
    if (count($tableauvalues[$key])>1){
        print_r($key." => ". implode(", ", $tableauvalues[$key])."\n");
        //echo $cp.'":{"adminCode1":"'.$adminCode1.'"},<br/>';
        $tableaufinal[] = $key.'":{"adminCode1":"'. implode(", ", $tableauvalues[$key]).'"},<br/>';
    }
}

print_r ($tableaufinal);
// $jsonfinalpourgwendelapartdemila = json_encode($tableaufinal);
// file_put_contents("jsonfinalpourgwendelapartdemila.json",$jsonfinalpourgwendelapartdemila);

?>
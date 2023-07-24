<?php
$json_to_sort = '{"AD":7,"AE":0,"AF":0,"AG":0,"AI":0,"AL":0,"AM":0,"AO":0,"AQ":0,"AR":20260,"AS":1,"AT":18957,"AU":16873,"AW":0,"AX":37,"AZ":1186,"BA":0,"BB":0,"BD":1349,"BE":2781,"BF":0,"BG":5304,"BH":0,"BI":0,"BJ":0,"BL":0,"BM":112,"BN":0,"BO":0,"BQ":0,"BR":5525,"BS":0,"BT":0,"BV":0,"BW":0,"BY":3133,"BZ":0,"CA":1655,"CC":0,"CD":0,"CF":0,"CG":0,"CH":4521,"CI":0,"CK":0,"CL":346,"CM":0,"CN":0,"CO":3681,"CR":473,"CU":0,"CV":0,"CW":0,"CX":0,"CY":1127,"CZ":15507,"DE":16478,"DJ":0,"DK":1159,"DM":0,"DO":544,"DZ":15951,"EC":0,"EE":5398,"EG":0,"EH":0,"ER":0,"ES":37867,"ET":0,"FI":3576,"FJ":0,"FK":0,"FM":4,"FO":130,"FR":51670,"GA":0,"GB":27430,"GD":0,"GE":0,"GF":77,"GG":14,"GH":0,"GI":0,"GL":33,"GM":0,"GN":0,"GP":105,"GQ":0,"GR":0,"GS":0,"GT":548,"GU":21,"GW":0,"GY":0,"HK":0,"HM":0,"HN":0,"HR":6774,"HT":236,"HU":3571,"ID":0,"IE":139,"IL":0,"IM":87,"IN":155570,"IO":0,"IQ":0,"IR":0,"IS":147,"IT":18416,"JE":4,"JM":0,"JO":0,"JP":146916,"KE":0,"KG":0,"KH":0,"KI":0,"KM":0,"KN":0,"KP":0,"KR":35583,"KW":0,"KY":0,"KZ":0,"LA":0,"LB":0,"LC":0,"LI":13,"LK":1837,"LR":0,"LS":0,"LT":21870,"LU":4483,"LV":6101,"LY":0,"MA":1325,"MC":29,"MD":1753,"ME":0,"MF":0,"MG":0,"MH":2,"MK":220,"ML":0,"MM":0,"MN":0,"MO":0,"MP":3,"MQ":100,"MR":0,"MS":0,"MT":73,"MU":0,"MV":0,"MW":491,"MX":144655,"MY":2818,"MZ":0,"NA":0,"NC":52,"NE":0,"NF":0,"NG":0,"NI":0,"NL":4182,"NO":5079,"NP":0,"NR":0,"NU":0,"NZ":1738,"OM":0,"PA":0,"PE":96943,"PF":0,"PG":0,"PH":2231,"PK":2563,"PL":72900,"PM":2,"PN":0,"PR":177,"PS":0,"PT":206942,"PW":1,"PY":0,"QA":0,"RE":152,"RO":37915,"RS":1155,"RU":43538,"RW":0,"SA":0,"SB":0,"SC":0,"SD":0,"SE":17159,"SG":121154,"SH":0,"SI":556,"SJ":8,"SK":4231,"SL":0,"SM":26,"SN":0,"SO":0,"SR":0,"SS":0,"ST":0,"SV":0,"SX":0,"SY":0,"SZ":0,"TC":0,"TD":0,"TF":0,"TG":0,"TH":903,"TJ":0,"TK":0,"TL":0,"TM":0,"TN":0,"TO":0,"TR":36307,"TT":0,"TV":0,"TW":0,"TZ":0,"UA":29571,"UG":0,"UM":0,"US":41483,"UY":1964,"UZ":0,"VA":1,"VC":0,"VE":0,"VG":0,"VI":16,"VN":0,"VU":0,"WF":3,"WS":0,"XK":0,"YE":0,"YT":21,"ZA":3920,"ZM":0,"ZW":0}';
echo "<br><br>";
print_r (gettype($json_to_sort));
echo "<br><br>";

$json_decoded = json_decode($json_to_sort, true);

print_r (gettype($json_decoded));

var_dump ($json_decoded);
echo "<br><br>";

arsort($json_decoded);

var_dump ($json_decoded);
// $json_final = json_encode($json_decoded);

file_put_contents('json_sorted_countries.json', json_encode($json_decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

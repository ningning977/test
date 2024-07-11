<?php
include('../../../core/config.core.php');
include('../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');


$resultArray = array();
$arrCol = array();
$server = 
    array(
        '192.168.1.9',
        '192.168.1.11',
        '192.168.3.5',
        '192.168.4.5'
    );
$svname =
    array(
        "EUROX FORCE <kbd>[Main]</kbd>",
        "SAP 10 & HRMI+ESS",
        "SITE GATEWAY <kbd>[KSY]</kbd>",
        "SITE GATEWAY <kbd>[KSM]</kbd>"
    );
$count  = count($server);
for($i=0; $i < $count; $i++) {
    $arrCol['SRV'.$i.'_Status'] = 0;
    exec("ping -n 2 $server[$i]", $output, $status);
    $arrCol['SRV'.$i.'_SVIPAd'] = $server[$i];
    $arrCol['SRV'.$i.'_SVName'] = $svname[$i];
    if($status == 0) {
        $arrCol['SRV'.$i.'_Status'] = 1;
    } else {
        $arrCol['SRV'.$i.'_Status'] = 0;
    }

}

$arrCol['Rows'] = $count;


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
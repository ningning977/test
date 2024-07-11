<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";

function ($CardCode,$Price,$Qty){
	$NewPrice = "<span>".number_format($Price)."</span>";
	return $NewPrice;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
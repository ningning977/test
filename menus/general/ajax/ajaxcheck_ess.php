<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == 'GetDataCheckESS') {
	$SQL = "SELECT LocationName,Latitude,Longtitude FROM emOrgConfigTimeStampOutside WHERE  IsDeleted = 'FALSE'";
	$QRY = HRMISelect($SQL);
	$r = 0;
	while($RST = odbc_fetch_array($QRY)) {
		$arrCol[$r]['No'] = $r+1;
		$arrCol[$r]['LocationName'] = conutf8($RST['LocationName']);
		$arrCol[$r]['Location'] = "<a href='https://www.google.com/maps/place/".$RST['Latitude'].",".$RST['Longtitude']."' target='_blank'><i class='fas fa-map-marker-alt'></i></a>";
		$r++;
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
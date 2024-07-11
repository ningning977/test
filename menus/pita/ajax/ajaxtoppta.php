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

if($_GET['a'] == 'GetSlpCode'){
	$SQL = "
		SELECT 
			T0.ukey, CONCAT(T1.uName,' ',T1.uLastName) AS 'FullName', T1.uNickName AS 'NickName',
			GROUP_CONCAT(DISTINCT(T2.SlpCode)) AS 'SlpCode', T3.DeptCode, T1.LvCode, T0.DocStatus 
		FROM saletarget T0
		LEFT JOIN users T1 ON T0.Ukey = T1.uKey
		LEFT JOIN oslp_pita T2 ON T0.Ukey = T2.Ukey
		LEFT JOIN positions T3 ON T1.LvCode = T3.LvCode 
		WHERE T0.DocStatus != 'I' AND T1.uName IS NOT NULL AND T2.MainTeam = 'PITA'
		GROUP BY T0.Ukey, T1.uName
		ORDER BY T3.uClass";
	$QRY = MySQLSelectX($SQL);
	$i = 0;
	while($RST = mysqli_fetch_array($QRY)) {
		
		if($RST['NickName'] == "" || $RST['NickName'] == NULL || $RST['NickName'] == "Online") {
			if($RST['NickName'] == "Online") {
				$SlpName = str_replace(" Online","",$RST['FullName']);
			} else {
				$SlpName = $RST['FullName'];
			}
		} else {
			$SlpName = $RST['FullName']." (".$RST['NickName'].")";
		}

		$arrCol[$i]['SlpCode']  = $RST['SlpCode'];
		$arrCol[$i]['SlpName']  = $SlpName;
		$arrCol[$i]['DeptCode'] = $RST['DeptCode'];
		$i++;
	}
	$arrCol['Rows'] = $i;
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
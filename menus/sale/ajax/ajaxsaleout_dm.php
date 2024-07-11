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

if($_GET['a'] == 'GetDM') {
	switch($_SESSION['DeptCode']) {
		case "DP005": $LvCode = "'LV092'"; break;
		case "DP006": $LvCode = "'LV042','LV043'"; break;
		case "DP007": $LvCode = "'LV048','LV049'"; break;
		case "DP008": $LvCode = "'LV109'"; break;
		default: $LvCode = "'LV042','LV043','LV048','LV049','LV092','LV109'"; break;
	}
	$SQL = 
		"SELECT T0.uKey, CONCAT(T0.uName,' ',T0.uLastName) AS 'FullName', T0.uNickName 
		FROM users T0 
		WHERE T0.UserStatus = 'A' AND T0.LvCode IN ($LvCode) 
		ORDER BY T0.uName, T0.uLastName";
	$QRY = MySQLSelectX($SQL);
	$Data = "";
	while($RST = mysqli_fetch_array($QRY)) {
		$Data .= "<option value='".$RST['uKey']."'>".$RST['FullName']." ".(($RST['uNickName'] != "") ? "(".$RST['uNickName'].")" : "")."</option>";
	}
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'GetTarget') {
	$uKey = $_POST['uKey'];
	$SQL = "SELECT SaleTarget, SaleActual FROM demontarget WHERE uKey = '$uKey' AND Status = 'A' AND YEAR(CreateDate) = ".date("Y")." AND MONTH(CreateDate) = ".date("m")." ORDER BY CreateDate DESC LIMIT 1";
	$RST = MySQLSelect($SQL);
	$arrCol['SaleTarget'] = (isset($RST['SaleTarget'])) ? $RST['SaleTarget'] : "0";
	$arrCol['SaleActual'] = (isset($RST['SaleActual'])) ? $RST['SaleActual'] : "0";
}

if($_GET['a'] == 'AddSaleOut') {
	$uKey = $_POST['uKey'];
	$Target = $_POST['Target'];
	$Sale = $_POST['Sale'];

	$SQL_CHK = "SELECT SaleTarget FROM demontarget WHERE uKey = '$uKey' AND Status = 'A' AND YEAR(CreateDate) = ".date("Y")." AND MONTH(CreateDate) = ".date("m")." ORDER BY CreateDate DESC LIMIT 1";
	if(CHKRowDB($SQL_CHK) != 0) {
		$UPDATE = "UPDATE demontarget SET Status = 'I', UpdateUkey = '".$_SESSION['ukey']."' WHERE uKey = '$uKey' AND Status = 'A' AND YEAR(CreateDate) = ".date("Y")." AND MONTH(CreateDate) = ".date("m")."";
		MySQLUpdate($UPDATE);
	}

	$INSERT = 
		"INSERT INTO demontarget 
		SET	uKey = '$uKey',
			SaleTarget = '$Target',
			SaleActual = '$Sale',
			CreateUkey = '".$_SESSION['ukey']."',
			CreateDate = NOW()";
	MySQLInsert($INSERT);
}

if($_GET['a'] == 'ListSaleOut') {
	switch($_SESSION['DeptCode']) {
		case "DP005": $LvCode = "'LV092'"; break;
		case "DP006": $LvCode = "'LV042','LV043'"; break;
		case "DP007": $LvCode = "'LV048','LV049'"; break;
		case "DP008": $LvCode = "'LV109'"; break;
		default: $LvCode = "'LV042','LV043','LV048','LV049','LV092','LV109'"; break;
	}

	$SQL = 
		"SELECT CONCAT(T1.uName,' ',T1.uLastName) AS 'FullName', T1.uNickName, T0.SaleTarget, T0.SaleActual, T0.CreateDate
		FROM demontarget T0
		LEFT JOIN users T1 ON T1.uKey = T0.uKey 
		WHERE T0.Status = 'A' AND T1.UserStatus = 'A' AND T1.LvCode IN ($LvCode) AND YEAR(T0.CreateDate) = ".date("Y")." AND MONTH(T0.CreateDate) = ".date("m")."";
	$QRY = MySQLSelectX($SQL);
	$Tbody = ""; $No = 0;
	while($RST = mysqli_fetch_array($QRY)) {
		$No++;
		$Tbody .= "
			<tr>
				<td class='text-center'>$No</td>
				<td>".$RST['FullName']." ".(($RST['uNickName'] != "") ? "(".$RST['uNickName'].")" : "")."</td>
				<td class='text-right'>".number_format($RST['SaleTarget'],2)."</td>
				<td class='text-right'>".number_format($RST['SaleActual'],2)."</td>
				<td class='text-right'>".number_format((($RST['SaleTarget'] != 0) ? ($RST['SaleActual']/$RST['SaleTarget'])*100 : 0),2)."%</td>
				<td class='text-center'>".date("d/m/Y", strtotime($RST['CreateDate']))." เวลา ".date("H:i", strtotime($RST['CreateDate']))." น.</td>
			</tr>
		";
	}
	$arrCol['Tbody'] = $Tbody;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
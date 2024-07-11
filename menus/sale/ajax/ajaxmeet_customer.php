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

if($_GET['a'] == 'CallData') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	$SQL1 = "
		SELECT CardCode, Q1, Q2, Q3, Q4, Q5, Q6, Q7, CreateDate
		FROM route_survey
		WHERE plan_month = $Month AND plan_year = $Year AND DocStatus = 'A' 
		ORDER BY CreateDate";
	$QRY1 = MySQLSelectX($SQL1);
	$r = 0;
	while($RST1 = mysqli_fetch_array($QRY1)) {
		$SQL2 = "
			SELECT T0.CardCode, T0.CardName, T2.SlpName
			FROM OCRD T0
			LEFT JOIN OCRG T1 ON T0.GroupCode = T1.GroupCode
			LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
			WHERE T0.CardCode = '".$RST1['CardCode']."'";
		$QRY2 = SAPSelect($SQL2);
		$RST2 = odbc_fetch_array($QRY2);

		for($q = 1; $q <= 7; $q++) {
			switch($RST1['Q'.$q]) {
				case 'Y': ${"Q{$q}"} = "<div title='เรียบร้อย'><i class='fas fa-check-circle fa-fw text-success'></i></div>"; break;
				case 'N': ${"Q{$q}"} = "<div title='ไม่เรียบร้อย'><i class='fas fa-times-circle fa-fw text-danger'></i></div>"; break;
				default: ${"Q{$q}"} = "<div title='ยังไม่ลงข้อมูล'><i class='fas fa-question-circle fa-fw text-secondary'></i></div>"; break;
			}
		}

		$SQL3 = "
			SELECT DetailPlan, DetailActual, CreateDate
			FROM route_action 
			WHERE CardCode = '".$RST1['CardCode']."' AND plan_month = '$Month' AND plan_year = '$Year' AND DocStatus = 'A'
			ORDER BY CreateDate DESC LIMIT 1";
		$RST3 = MySQLSelect($SQL3);
		$DetailPlan = "";
		if(isset($RST3['DetailPlan'])) {
			$DetailPlan = $RST3['DetailPlan'];
		}
		$DetailActual = "";
		if(isset($RST3['DetailActual'])) {
			$DetailActual = $RST3['DetailActual'];
		}
		$CreateDate = "";
		if(isset($RST1['CreateDate'])) {
			$CreateDate = date("d/m/Y",strtotime($RST1['CreateDate']));
		}
		$arrCol[$r]['CardCode'] = $RST2['CardCode'];
		$arrCol[$r]['CardName'] = conutf8($RST2['CardName']);
		$arrCol[$r]['SlpName'] = conutf8($RST2['SlpName']);
		$arrCol[$r]['Q1'] = $Q1;
		$arrCol[$r]['Q2'] = $Q2;
		$arrCol[$r]['Q3'] = $Q3;
		$arrCol[$r]['Q4'] = $Q4;
		$arrCol[$r]['Q5'] = $Q5;
		$arrCol[$r]['Q6'] = $Q6;
		$arrCol[$r]['Q7'] = $Q7;
		$arrCol[$r]['DetailPlan'] = $DetailPlan;
		$arrCol[$r]['DetailActual'] = $DetailActual;
		$arrCol[$r]['DataDate'] = $CreateDate;
		$r++;
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}


if($_GET['p'] == "GetSlp") {
	$SQL1 = "SELECT T0.SlpCode, T0.SlpName FROM OSLP T0 ORDER BY T0.SlpName";
	$QRY1 = SAPSelect($SQL1);
	$i = 0;
	while($RST1 = odbc_fetch_array($QRY1)) {
		$arrCol[$i]['SlpName'] = conutf8($RST1['SlpName']);
		$i++;
	}
	$arrCol['Rows'] = $i;
}

if($_GET['p'] == "GetData") {
	$user_1 = SapTHSearch($_POST['u1']);
	$user_2 = SapTHSearch($_POST['u2']);
	$date_1 = $_POST['d1'];
	$date_2 = $_POST['d2'];

	$DocYear = date("Y",strtotime($date_2));

	if($DocYear <= 2022) {
		$DBPrefix = "KBI_DB2022.dbo.";
	} else {
		$DBPrefix = null;
	}

	$SQL1 =
		"SELECT
			A0.SlpName, SUM(A0.DocTotal) AS 'DocTotal'
		FROM (
			SELECT
				T1.SlpName, T0.DocTotal-T0.VatSum AS 'DocTotal'
			FROM ".$DBPrefix."OINV T0
			LEFT JOIN ".$DBPrefix."OSLP T1 ON T0.SlpCode = T1.SlpCode
			WHERE (T0.DocDate BETWEEN '$date_1' AND '$date_2') AND (T1.SlpName >= N'$user_1' AND T1.SlpName <= N'$user_2') AND T0.CANCELED = 'N'
			UNION ALL
			SELECT
				T1.SlpName, -(T0.DocTotal-T0.VatSum) AS 'DocTotal'
			FROM ".$DBPrefix."ORIN T0
			LEFT JOIN ".$DBPrefix."OSLP T1 ON T0.SlpCode = T1.SlpCode
			WHERE (T0.DocDate BETWEEN '$date_1' AND '$date_2') AND (T1.SlpName >= N'$user_1' AND T1.SlpName <= N'$user_2') AND T0.CANCELED = 'N'
		) A0
		GROUP BY A0.SlpName";
	$Rows = ChkRowSAP($SQL1);
	if($Rows > 0) {
		$QRY1 = SAPSelect($SQL1);
		$i = 0;
		$ALLTotal = 0;
		while($RST1 = odbc_fetch_array($QRY1)) {
			$ALLTotal = $ALLTotal+$RST1['DocTotal'];
			$arrCol[$i]['SlpName']  = conutf8($RST1['SlpName']);
			$arrCol[$i]['DocTotal'] = number_format($RST1['DocTotal'],2);
			$i++;
		}
	}
	$arrCol['SumTotal'] = number_format($ALLTotal,2);
	$arrCol['Rows'] = $Rows;

}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if($_GET['p'] == "ItemKPI") {
	$filt_year  = $_POST['y'];
	$filt_month = $_POST['m'];
	$filt_team  = $_POST['t'];

	if($filt_team != "ALL") {
		$SQLWhr = " AND T3.U_Dim1 = '$filt_team'";
	} else {
		$SQLWhr = NULL;
	}

	$GetSQL =
		"SELECT
			A0.ItemStatus, SUM(A0.Cost) AS 'Cost', SUM(A0.LineTotal) AS 'LineTotal', SUM(A0.GrssProfit) AS 'GrssProfit',
			CASE WHEN SUM(A0.GrssProfit) > 0 THEN (SUM(A0.GrssProfit) / SUM(A0.LineTotal)) * 100 ELSE 0 END AS 'GrssPercent'
		FROM (
			SELECT
				T0.ItemCode, T2.U_ProductStatus AS 'ItemStatus', (T0.LineTotal-T0.GrssProfit) AS 'Cost', T0.LineTotal, T0.GrssProfit 
			FROM INV1 T0
			LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN OITM T2 ON T0.ItemCode = T2.ItemCode
			LEFT JOIN OSLP T3 ON T1.SlpCode = T3.SlpCode
			WHERE YEAR(T1.DocDate) = $filt_year AND MONTH(T1.DocDate) = $filt_month AND T1.CANCELED = 'N' $SQLWhr
			UNION ALL
			SELECT
				T0.ItemCode, T2.U_ProductStatus AS 'ItemStatus', -(T0.LineTotal-T0.GrssProfit) AS 'Cost', -T0.LineTotal, -T0.GrssProfit 
			FROM RIN1 T0
			LEFT JOIN ORIN T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN OITM T2 ON T0.ItemCode = T2.ItemCode
			LEFT JOIN OSLP T3 ON T1.SlpCode = T3.SlpCode
			WHERE YEAR(T1.DocDate) = $filt_year AND MONTH(T1.DocDate) = $filt_month AND T1.CANCELED = 'N' $SQLWhr
		) A0
		WHERE A0.ItemStatus IS NOT NULL AND A0.ItemStatus != 'K'
		GROUP BY A0.ItemStatus
		ORDER BY
		CASE
			WHEN A0.ItemStatus LIKE 'D%' THEN 1
			WHEN A0.ItemStatus = 'R' THEN 2
			WHEN A0.ItemStatus = 'A' THEN 3
			WHEN A0.ItemStatus = 'W' THEN 4
			WHEN A0.ItemStatus = 'N' THEN 5
			WHEN A0.ItemStatus = 'M' THEN 6
			ELSE 7
		END";
	if($filt_year <= 2022) {
		$GetQRY = conSAP8($GetSQL);
	} else {
		$GetQRY = SAPSelect($GetSQL);
	}
	$r = 0;
	while($GetRST = odbc_fetch_array($GetQRY)) {
		$arrCol[$r]['Status']   = $GetRST['ItemStatus'];
		$arrCol[$r]['COST']     = $GetRST['Cost'];
		$arrCol[$r]['SALE']     = $GetRST['LineTotal'];
		$arrCol[$r]['PRFT']     = $GetRST['GrssProfit'];
		$arrCol[$r]['PCNTPRFT'] = $GetRST['GrssPercent'];
		$r++;
		// echo $GetRST['ItemStatus']."\n";
	}
	$arrCol['Rows'] = $r;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
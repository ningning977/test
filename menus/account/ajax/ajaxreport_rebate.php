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

if($_GET['a'] == 'GetHang') {
	$Year = $_POST['Year'];
	$sql = "SELECT DISTINCT T0.GroupCode, T0.GroupName FROM report_rebate T0 WHERE T0.YEAR = '$Year'";
	$QRY = MySQLSelectX($sql);
	$Data = "<option value='' selected disabled>เลือกห้าง</option>";
	while($result = mysqli_fetch_array($QRY)) {
		$Data .= "<option value='".$result['GroupCode']."'>".$result['GroupName']."</option>";
	}
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'CallData') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	$Hang  = $_POST['Hang'];

	$GETNAME = "SELECT T0.GroupName FROM report_rebate T0 WHERE T0.GroupCode = '$Hang' LIMIT 1";
	$NAME = MySQLSelect($GETNAME);
	$arrCol['Name'] = "ยอดขาย ".$NAME['GroupName']." ปี ".($Year+543);

	$SQL1 = "SELECT T0.CardCode FROM OCRD T0 WHERE T0.QryGroup$Hang = 'Y'";
	$QRY1 = SAPSelect($SQL1);
	$CardCode = "";
	while($result1 = odbc_fetch_array($QRY1)) {
		$CardCode .= "'".$result1['CardCode']."'".",";
	}
	$CardCode = substr($CardCode,0,-1);

	$SQL2 = "
		SELECT 
			B0.CardCode, B1.CardName,
			SUM(B0.M_1) AS 'M_1', SUM(B0.M_2) AS 'M_2', SUM(B0.M_3) AS 'M_3',
			SUM(B0.M_4) AS 'M_4', SUM(B0.M_5) AS 'M_5', SUM(B0.M_6) AS 'M_6',
			SUM(B0.M_7) AS 'M_7', SUM(B0.M_8) AS 'M_8', SUM(B0.M_9) AS 'M_9',
			SUM(B0.M_10) AS 'M_10', SUM(B0.M_11) AS 'M_11', SUM(B0.M_12) AS 'M_12'
		FROM(
			SELECT
				A0.CardCode,
				CASE WHEN A0.Month = 1 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_1',
				CASE WHEN A0.Month = 2 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_2',
				CASE WHEN A0.Month = 3 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_3',
				CASE WHEN A0.Month = 4 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_4',
				CASE WHEN A0.Month = 5 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_5',
				CASE WHEN A0.Month = 6 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_6',
				CASE WHEN A0.Month = 7 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_7',
				CASE WHEN A0.Month = 8 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_8',
				CASE WHEN A0.Month = 9 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_9',
				CASE WHEN A0.Month = 10 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_10',
				CASE WHEN A0.Month = 11 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_11',
				CASE WHEN A0.Month = 12 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_12'
			FROM(
				SELECT T0.CardCode, MONTH(T0.DocDate) AS 'Month', SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal'
				FROM OINV T0
				WHERE (YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) <= $Month) AND T0.CardCode IN ($CardCode) AND T0.CANCELED = 'N'
				GROUP BY T0.CardCode, MONTH(T0.DocDate)
				UNION ALL
				SELECT T0.CardCode, MONTH(T0.DocDate) AS 'Month', -SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal'
				FROM ORIN T0
				LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
				WHERE (YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) <= $Month) AND (T1.BeginStr LIKE 'S1-%' OR T1.BeginStr LIKE 'SR-%') AND T0.CardCode IN ($CardCode) AND T0.CANCELED = 'N'
				GROUP BY T0.CardCode, MONTH(T0.DocDate)
				) A0
			GROUP BY A0.CardCode, A0.Month
		) B0
		LEFT JOIN OCRD B1 ON B0.CardCode = B1.CardCode
		GROUP BY B0.CardCode, B1.CardName 
		ORDER BY B0.CardCode";
	if($Year <= 2022) {
		$QRY2 = conSAP8($SQL2);
	}else{
		$QRY2 = SAPSelect($SQL2);
	}
	$Data = array();
	for($m = 1; $m <= 12; $m++) { 
		$Data['AllM_'.$m] = 0; 
		$Data['SumTotalAllM_'.$m] = 0; 
		$Data['PerM_'.$m] = 0; 
		$Data['Rebate'.$m] = 0; 
	}
	$Data['SumAllM']     = 0;
	$Data['SumTotalAll'] = 0;
	$Data['TotolPer']     = 0;
	$Data['SumRebate']   = 0; 
	$Data['LastTotalRE']   = 0; 
	$i = 0;
	while($result2 = odbc_fetch_array($QRY2)) {
		$i++;
		//รายการทั้งหมด
		$Data['CardCode'][$i] = conutf8($result2['CardCode']);
		$Data['CardName'][$i] = conutf8($result2['CardName']);
		$Data['Sum'][$i] = 0;
		for($m = 1; $m <= 12; $m++) {
			$Data['M_'.$m][$i] = 0;
			if($result2['M_'.$m] != 0) {
				$Data['M_'.$m][$i] = $result2['M_'.$m];
				$Data['Sum'][$i]   = $Data['Sum'][$i]+$result2['M_'.$m];

		//รวมทุกรายการ
				$Data['AllM_'.$m]  = $Data['AllM_'.$m]+$result2['M_'.$m];
				$Data['SumAllM']   = $Data['SumAllM']+$result2['M_'.$m];
			}
		}
	}

	for($m = 1; $m <= $Month; $m++) { 
		//ยอดขายสะสม
		if($m != 1) { $mT = $m-1; }else{ $mT = $m; }
		$Data['SumTotalAllM_'.$m] = $Data['SumTotalAllM_'.$mT]+$Data['AllM_'.$m];

		$SQL3 = "SELECT Min, Max, percent FROM report_rebate WHERE Year = $Year AND GroupCode = $Hang";
		$QRY3 = MySQLSelectX($SQL3);
		while($result3 = mysqli_fetch_array($QRY3)) {
			//ส่วนลด
			if($Data['SumTotalAllM_'.$m] <= $result3['Max'] && $Data['SumTotalAllM_'.$m] >= $result3['Min']) {
				$Data['PerM_'.$m]  = $result3['percent'];
				$Data['TotolPer']  = $result3['percent'];
			}
		}
		
		//ยอด Rebate สะสม
		$Data['Rebate'.$m] = ($Data['PerM_'.$m]*$Data['SumTotalAllM_'.$m])/100;
		$Data['SumRebate'] = $Data['Rebate'.$m];

	}
	
	//Table2
	$SQL4 = "SELECT Min, Max, percent, percent_mkt, percent_dc FROM report_rebate WHERE Year = $Year AND GroupCode = $Hang";
	// echo $SQL4;
	$QRY4 = MySQLSelectX($SQL4);
	$i2 = 0;
	while($result4 = mysqli_fetch_array($QRY4)) {
		$i2++;
		$Data['Percent_mkt'] = $result4['percent_mkt'];
		$Data['Percent_dc']  = $result4['percent_dc'];

		//เงื่อนไข 
		if($result4['Min'] == 1 && $result4['Max'] == 999999999) {
			$Data['Cdt_'.$i2] = "ตั้งแต่บาทแรก";
		}elseif($result4['Max'] == 999999999) {
			$Data['Cdt_'.$i2] = "ตั้งแต่ ".number_format($result4['Min'],0)." บาท ขึ้นไป";
		}else{
			$Data['Cdt_'.$i2] = "ตั้งแต่ ".number_format($result4['Min'],0)." บาท ถึง ".number_format($result4['Max'],0)." บาท";
		}

		//ส่วนลด (%)
		$Data['Percent_'.$i2]   = $result4['percent'];

		//ยอด Rebate (บาท)
		if($Data['SumAllM'] <= $result4['Max'] && $Data['SumAllM'] >= $result4['Min']) {
			$Data['LastTotal_'.$i2] = ($Data['SumAllM']*$Data['Percent_'.$i2])/100;
			$Data['Color_'.$i2]     = "text-success";
		}else{
			$Data['LastTotal_'.$i2] = 0;
			$Data['Color_'.$i2]     = "";
		}
		// รวม Rebate
		$Data['LastTotalRE'] = $Data['LastTotalRE']+$Data['LastTotal_'.$i2];
	}

	//Table3
	$Data['Totalmkt'] = ($Data['SumAllM']*$Data['Percent_mkt'])/100;

	//Table4
	$Data['Totaldc'] = ($Data['SumAllM']*$Data['Percent_dc'])/100;

	$arrCol['Row']  = $i;
	$arrCol['Row2'] = $i2;
	$arrCol['Data'] = $Data;
} 

if($_GET['a'] == 'GetHang2') {
	$SQL = "SELECT T0.GroupCode, T0.GroupName FROM OCQG T0 WHERE T0.GroupName NOT LIKE 'Business%' ORDER BY T0.GroupName ASC";
	$QRY = SAPSelect($SQL);
	$Data = "<option value='' selected disabled>เลือกห้าง</option>";
	while($result = odbc_fetch_array($QRY)) {
		$Data .= "<option value='".$result['GroupCode']."'>".conutf8($result['GroupName'])."</option>";
	}
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'SaveData') {
	$No   = intval($_POST['No']);
	$Hang = intval($_POST['Hang']);
	$Year = intval($_POST['Year']);

	$SQL = "SELECT T0.GroupName FROM OCQG T0 WHERE T0.GroupCode = $Hang AND T0.GroupName NOT LIKE 'Business%' ORDER BY T0.GroupName ASC";
	$QRY = SAPSelect($SQL);
	$result = odbc_fetch_array($QRY);

	// echo $No;

	for($i = 1; $i <= $No; $i++) {
		$Min          = (float)str_replace(",","",$_POST['Min'.$i]);
		$Max          = (float)str_replace(",","",$_POST['Max'.$i]);
		$Discount     = (float)str_replace(",","",$_POST['Discount'.$i]);
		$MarketingFee = (float)str_replace(",","",$_POST['MarketingFee'.$i]);
		$DCFee        = (float)str_replace(",","",$_POST['DCFee'.$i]);
		$SQL_INSERT = "INSERT INTO report_rebate 
					   SET GroupCode = $Hang, GroupName = '".conutf8($result['GroupName'])."', Year = $Year, NoCdt = $i, Min = $Min, Max = $Max,
					  	   Percent = $Discount, percent_mkt = $MarketingFee, percent_dc = $DCFee, ukey_inst = '".$_SESSION['ukey']."', dateCreate = NOW()";
		MySQLInsert($SQL_INSERT);
	}
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
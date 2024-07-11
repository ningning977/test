<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
$resultArray = array();
$arrCol = array();
$output = "";

switch($_SESSION['DeptCode']) {
	case "DP001":
	case "DP002":
	case "DP004":
	case "DP009":
		$VisCost = true;
		// $VisCost = false;
	break;
	case "DP003":
		switch($_SESSION['LvCode']) {
			case "LV010":
			case "LV011":
			case "LV012":
			case "LV013":
				$VisCost = true;
			break;
			default: $VisCost = false;
		}
	break;
	default: $VisCost = false; break;
}

if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == 'GetSlpCode') {
	$Year = date("Y");
	$SQL =
		"SELECT 
			T0.ukey, CONCAT(T1.uName,' ',T1.uLastName) AS 'FullName', T1.uNickName AS 'NickName',
			GROUP_CONCAT(DISTINCT(T2.SlpCode)) AS 'SlpCode', T3.DeptCode, T1.LvCode, T0.DocStatus 
		FROM saletarget T0
		LEFT JOIN users T1 ON T0.Ukey = T1.uKey
		LEFT JOIN oslp T2 ON T0.Ukey = T2.Ukey
		LEFT JOIN positions T3 ON T1.LvCode = T3.LvCode 
		WHERE T0.DocYear = $Year AND T0.DocStatus != 'I' AND T1.uName IS NOT NULL
		GROUP BY T0.Ukey, T1.uName
		ORDER BY
			CASE
				WHEN T3.DeptCode IN ('DP006') THEN 1
				WHEN T3.DeptCode IN ('DP007') THEN 2
				WHEN T3.DeptCode IN ('DP005') THEN 3
				WHEN T3.DeptCode IN ('DP008') THEN 4
				WHEN T3.DeptCode IN ('DP003') THEN 5
			ELSE 6 END, T1.LvCode, T3.uClass";
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

		$arrCol[$i]['SlpCode'] = $RST['SlpCode'];
		$arrCol[$i]['SlpName'] = $SlpName;
		$i++;
	}
	$arrCol['Rows'] = $i;
}

if($_GET['a'] == 'CallData') {
	$Year = $_POST['Year'];
	if($_POST['Product'] == 'ALL') {
		$Product = "";
	}else{
		$Product = "AND P3.U_ProductStatus = '".$_POST['Product']."'";
	}

	if($_POST['SlpCode'] == "ALL") {
		$SlpCode = "";
	} else {
		$SlpCode = "AND T0.SlpCode IN (".$_POST['SlpCode'].")";
	}

	if($_POST['CardCode'] == "ALL") {
		$CardCode = "";
	} else {
		$CardCode = "AND T0.CardCode = '".$_POST['CardCode']."'";
	}
	
	$SQL = "SELECT P1.ItemCode, P2.ItemName, P2.U_ProductStatus, SUM(P4.OnHand)AS 'OnHand', P2.BuyUnitMsr,";
		if($Year >= 2023) {
			$SQL .="(CASE WHEN P2.LastPurDat = '2022-12-31' THEN ISNULL(P3.LastPurPrc,P2.LastPurPrc) ELSE P2.LastPurPrc END)*1.07 AS 'LastPurc',
					(CASE WHEN P2.LastPurDat = '2022-12-31' THEN ISNULL(P3.LastPurDat,P2.LastPurDat) ELSE P2.LastPurDat END) AS 'LastPurDat',";
		}else{
			$SQL .= "(P2.LastPurPrc)*1.07 AS 'LastPurc', (P2.LastPurDat) AS 'LastPurDat',";
		}
		    $SQL .= "P1.M01, P1.M02, P1.M03, P1.M04, P1.M05, P1.M06, P1.M07, P1.M08, P1.M09, P1.M10, P1.M11, P1.M12
			FROM (
				SELECT
				W1.[ItemCode],
				SUM(W1.[M01]) AS M01, SUM(W1.[M02]) AS M02, SUM(W1.[M03]) AS M03,
				SUM(W1.[M04]) AS M04, SUM(W1.[M05]) AS M05, SUM(W1.[M06]) AS M06,
				SUM(W1.[M07]) AS M07, SUM(W1.[M08]) AS M08, SUM(W1.[M09]) AS M09,
				SUM(W1.[M10]) AS M10, SUM(W1.[M11]) AS M11, SUM(W1.[M12]) AS M12,
				SUM(W1.[M01])+SUM(W1.[M02])+SUM(W1.[M03])+SUM(W1.[M04])+SUM(W1.[M05])+SUM(W1.[M06])+SUM(W1.[M07])+SUM(W1.[M08])+SUM(W1.[M09])+SUM(W1.[M10])+SUM(W1.[M11])+SUM(W1.[M12]) AS DocTotal
				FROM (
				SELECT
				T1.[ItemCode],
				CASE WHEN MONTH(T0.[DocDate]) = 1 THEN T1.[Quantity] ELSE 0 END AS M01,
				CASE WHEN MONTH(T0.[DocDate]) = 2 THEN T1.[Quantity] ELSE 0 END AS M02,
				CASE WHEN MONTH(T0.[DocDate]) = 3 THEN T1.[Quantity] ELSE 0 END AS M03,
				CASE WHEN MONTH(T0.[DocDate]) = 4 THEN T1.[Quantity] ELSE 0 END AS M04,
				CASE WHEN MONTH(T0.[DocDate]) = 5 THEN T1.[Quantity] ELSE 0 END AS M05,
				CASE WHEN MONTH(T0.[DocDate]) = 6 THEN T1.[Quantity] ELSE 0 END AS M06,
				CASE WHEN MONTH(T0.[DocDate]) = 7 THEN T1.[Quantity] ELSE 0 END AS M07,
				CASE WHEN MONTH(T0.[DocDate]) = 8 THEN T1.[Quantity] ELSE 0 END AS M08,
				CASE WHEN MONTH(T0.[DocDate]) = 9 THEN T1.[Quantity] ELSE 0 END AS M09,
				CASE WHEN MONTH(T0.[DocDate]) = 10 THEN T1.[Quantity] ELSE 0 END AS M10,
				CASE WHEN MONTH(T0.[DocDate]) = 11 THEN T1.[Quantity] ELSE 0 END AS M11,
				CASE WHEN MONTH(T0.[DocDate]) = 12 THEN T1.[Quantity] ELSE 0 END AS M12
				FROM OINV T0
				JOIN INV1 T1 ON T0.[DocEntry] = T1.[DocEntry]
				WHERE YEAR(T0.[DocDate]) = '$Year' AND T1.[ItemCode] IS NOT NULL AND T0.[CANCELED] = 'N' $SlpCode $CardCode
				UNION ALL
				SELECT
				T1.[ItemCode],
				CASE WHEN MONTH(T0.[DocDate]) = 1 THEN -T1.[Quantity] ELSE 0 END AS M01,
				CASE WHEN MONTH(T0.[DocDate]) = 2 THEN -T1.[Quantity] ELSE 0 END AS M02,
				CASE WHEN MONTH(T0.[DocDate]) = 3 THEN -T1.[Quantity] ELSE 0 END AS M03,
				CASE WHEN MONTH(T0.[DocDate]) = 4 THEN -T1.[Quantity] ELSE 0 END AS M04,
				CASE WHEN MONTH(T0.[DocDate]) = 5 THEN -T1.[Quantity] ELSE 0 END AS M05,
				CASE WHEN MONTH(T0.[DocDate]) = 6 THEN -T1.[Quantity] ELSE 0 END AS M06,
				CASE WHEN MONTH(T0.[DocDate]) = 7 THEN -T1.[Quantity] ELSE 0 END AS M07,
				CASE WHEN MONTH(T0.[DocDate]) = 8 THEN -T1.[Quantity] ELSE 0 END AS M08,
				CASE WHEN MONTH(T0.[DocDate]) = 9 THEN -T1.[Quantity] ELSE 0 END AS M09,
				CASE WHEN MONTH(T0.[DocDate]) = 10 THEN -T1.[Quantity] ELSE 0 END AS M10,
				CASE WHEN MONTH(T0.[DocDate]) = 11 THEN -T1.[Quantity] ELSE 0 END AS M11,
				CASE WHEN MONTH(T0.[DocDate]) = 12 THEN -T1.[Quantity] ELSE 0 END AS M12
				FROM ORIN T0
				JOIN RIN1 T1 ON T0.[DocEntry] = T1.[DocEntry]
				WHERE YEAR(T0.[DocDate]) = '$Year' AND T1.[ItemCode] IS NOT NULL AND T0.[CANCELED] = 'N' $SlpCode $CardCode
				) W1 
				GROUP BY W1.[ItemCode]
			) P1
			LEFT JOIN OITM P2 ON P1.[ItemCode] = P2.[ItemCode]";
	if($Year >= 2023) {
		$SQL.="LEFT JOIN KBI_DB2022.dbo.OITM P3 ON P1.[Itemcode] = P3.[ItemCode]";
	}
	if($Year >= 2023) {
		$SQL .="INNER JOIN OITW P4 ON P1.[ItemCode] = P4.[ItemCode] AND P4.[OnHand] != 0
				INNER JOIN OWHS P5 ON P4.[WhsCode]  = P5.[WhsCode] AND P5.[Location] IN (1,3)";
	}else{
		$SQL .="INNER JOIN SBO_KBI2023.dbo.OITW P4 ON P1.[ItemCode] = P4.[ItemCode] AND P4.[OnHand] != 0
				INNER JOIN SBO_KBI2023.dbo.OWHS P5 ON P4.[WhsCode]  = P5.[WhsCode] AND P5.[Location] IN (1,3)";
	}
	$SQL .="WHERE P2.[InvntItem] = 'Y' $Product
			GROUP BY
				P1.ItemCode, P2.ItemName, P2.U_ProductStatus, P2.BuyUnitMsr,";
		if($Year >= 2023) {
			$SQL .="(CASE WHEN P2.LastPurDat = '2022-12-31' THEN ISNULL(P3.LastPurPrc,P2.LastPurPrc) ELSE P2.LastPurPrc END)*1.07,
					(CASE WHEN P2.LastPurDat = '2022-12-31' THEN ISNULL(P3.LastPurDat,P2.LastPurDat) ELSE P2.LastPurDat END),";
		}else{
			$SQL .="(P2.LastPurPrc)*1.07, (P2.LastPurDat),";
		}
		$SQL .="P1.M01, P1.M02, P1.M03, P1.M04, P1.M05, P1.M06, P1.M07, P1.M08, P1.M09, P1.M10, P1.M11, P1.M12
			ORDER BY P1.ItemCode";
	if($Year >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	// echo $SQL;
	$r = 0; $No = 1;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['No']            = $No;
		$arrCol[$r]['ItemCode']      = "<a href='javascript:void(0);' onclick=\"ViewData('".$result['ItemCode']."')\">".$result['ItemCode']."</a>";
		$arrCol[$r]['ItemName']      = conutf8($result['ItemName']);
		$arrCol[$r]['ProductStatus'] = "[".$result['U_ProductStatus']."]";
		$arrCol[$r]['OnHand']        = number_format($result['OnHand'],0);
		$arrCol[$r]['UnitMsr']       = conutf8($result['BuyUnitMsr']);
		if($VisCost == true) {
			$arrCol[$r]['LastPurc']      = number_format($result['LastPurc'],2);
			$arrCol[$r]['LastDocDate']   = date("d/m/Y",strtotime($result['LastPurDat']));
		}
		for($m = 1; $m <= 12; $m++) {
			if($m < 10) {
				if($result['M0'.$m] != 0 && $result['M0'.$m] != "") { 
					$arrCol[$r]['M'.$m]   = number_format($result['M0'.$m],0); 
				}else{
					$arrCol[$r]['M'.$m]   = "-"; 
				}
			}else{
				if($result['M'.$m] != 0 && $result['M'.$m] != "") {
					$arrCol[$r]['M'.$m]   = number_format($result['M'.$m],0);
				}else{
					$arrCol[$r]['M'.$m]   = "-"; 
				}
			}
		}
		$r++; $No++;
	}
}

if($_GET['a'] == 'ViewData') {
	$ItemCode = $_POST['ItemCode'];
	$Year = $_POST['Year'];
	$SQL = "SELECT T0.ItemCode, T1.ItemName, T2.CardCode, T2.CardName, T1.BuyUnitMsr, T0.DocDate,
				T0.Quantity AS Qty, T0.PriceAfVAT AS Price, (T0.Quantity*T0.PriceAfVAT) AS LotalLine
			FROM PDN1 T0
			JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
			JOIN OPOR T2 ON T0.BaseEntry = T2.DocEntry
			WHERE T0.ItemCode = '$ItemCode' AND YEAR(T0.DocDate) = $Year
			ORDER BY T0.DocDate DESC";
	if($Year >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	$Data = array(); $r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$r++;
		$Data['No'][$r]       = $r;
		$Data['ItemCode'][$r] = $result['ItemCode'];
		$Data['ItemName'][$r] = conutf8($result['ItemName']);
		$Data['CardName'][$r] = $result['CardCode']." - ".conutf8($result['CardName']);
		$Data['UnitMsr'][$r]  = conutf8($result['BuyUnitMsr']);
		$Data['DocDate'][$r]  = date("d/m/Y",strtotime($result['DocDate']));
		$Data['Qty'][$r]      = number_format($result['Qty'],0);
		$Data['Price'][$r]    = number_format($result['Price'],2);
		$Data['LotalLine'][$r]= number_format($result['LotalLine'],2);
	}
	$arrCol['Row'] = $r;
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'Excel') {
	$Year = $_POST['Year'];
	if($_POST['Product'] == 'ALL') {
		$Product = "";
	}else{
		$Product = "AND P3.U_ProductStatus = '".$_POST['Product']."'";
	}

	if($_POST['SlpCode'] == "ALL") {
		$SlpCode = "";
	} else {
		$SlpCode = "AND T0.SlpCode IN (".$_POST['SlpCode'].")";
	}

	if($_POST['CardCode'] == "ALL") {
		$CardCode = "";
	} else {
		$CardCode = "AND T0.CardCode = '".$_POST['CardCode']."'";
	}
	$SQL = "SELECT P1.ItemCode, P2.ItemName, P2.U_ProductStatus, SUM(P4.OnHand)AS 'OnHand', P2.BuyUnitMsr,";
		if($Year >= 2023) {
			$SQL .="(CASE WHEN P2.LastPurDat = '2022-12-31' THEN ISNULL(P3.LastPurPrc,P2.LastPurPrc) ELSE P2.LastPurPrc END)*1.07 AS 'LastPurc',
					(CASE WHEN P2.LastPurDat = '2022-12-31' THEN ISNULL(P3.LastPurDat,P2.LastPurDat) ELSE P2.LastPurDat END) AS 'LastPurDat',";
		}else{
			$SQL .= "(P2.LastPurPrc)*1.07 AS 'LastPurc', (P2.LastPurDat) AS 'LastPurDat',";
		}
		    $SQL .= "P1.M01, P1.M02, P1.M03, P1.M04, P1.M05, P1.M06, P1.M07, P1.M08, P1.M09, P1.M10, P1.M11, P1.M12
			FROM (
				SELECT
				W1.[ItemCode],
				SUM(W1.[M01]) AS M01, SUM(W1.[M02]) AS M02, SUM(W1.[M03]) AS M03,
				SUM(W1.[M04]) AS M04, SUM(W1.[M05]) AS M05, SUM(W1.[M06]) AS M06,
				SUM(W1.[M07]) AS M07, SUM(W1.[M08]) AS M08, SUM(W1.[M09]) AS M09,
				SUM(W1.[M10]) AS M10, SUM(W1.[M11]) AS M11, SUM(W1.[M12]) AS M12,
				SUM(W1.[M01])+SUM(W1.[M02])+SUM(W1.[M03])+SUM(W1.[M04])+SUM(W1.[M05])+SUM(W1.[M06])+SUM(W1.[M07])+SUM(W1.[M08])+SUM(W1.[M09])+SUM(W1.[M10])+SUM(W1.[M11])+SUM(W1.[M12]) AS DocTotal
				FROM (
				SELECT
				T1.[ItemCode],
				CASE WHEN MONTH(T0.[DocDate]) = 1 THEN T1.[Quantity] ELSE 0 END AS M01,
				CASE WHEN MONTH(T0.[DocDate]) = 2 THEN T1.[Quantity] ELSE 0 END AS M02,
				CASE WHEN MONTH(T0.[DocDate]) = 3 THEN T1.[Quantity] ELSE 0 END AS M03,
				CASE WHEN MONTH(T0.[DocDate]) = 4 THEN T1.[Quantity] ELSE 0 END AS M04,
				CASE WHEN MONTH(T0.[DocDate]) = 5 THEN T1.[Quantity] ELSE 0 END AS M05,
				CASE WHEN MONTH(T0.[DocDate]) = 6 THEN T1.[Quantity] ELSE 0 END AS M06,
				CASE WHEN MONTH(T0.[DocDate]) = 7 THEN T1.[Quantity] ELSE 0 END AS M07,
				CASE WHEN MONTH(T0.[DocDate]) = 8 THEN T1.[Quantity] ELSE 0 END AS M08,
				CASE WHEN MONTH(T0.[DocDate]) = 9 THEN T1.[Quantity] ELSE 0 END AS M09,
				CASE WHEN MONTH(T0.[DocDate]) = 10 THEN T1.[Quantity] ELSE 0 END AS M10,
				CASE WHEN MONTH(T0.[DocDate]) = 11 THEN T1.[Quantity] ELSE 0 END AS M11,
				CASE WHEN MONTH(T0.[DocDate]) = 12 THEN T1.[Quantity] ELSE 0 END AS M12
				FROM OINV T0
				JOIN INV1 T1 ON T0.[DocEntry] = T1.[DocEntry]
				WHERE YEAR(T0.[DocDate]) = '$Year' AND T1.[ItemCode] IS NOT NULL AND T0.[CANCELED] = 'N' $SlpCode $CardCode
				UNION ALL
				SELECT
				T1.[ItemCode],
				CASE WHEN MONTH(T0.[DocDate]) = 1 THEN -T1.[Quantity] ELSE 0 END AS M01,
				CASE WHEN MONTH(T0.[DocDate]) = 2 THEN -T1.[Quantity] ELSE 0 END AS M02,
				CASE WHEN MONTH(T0.[DocDate]) = 3 THEN -T1.[Quantity] ELSE 0 END AS M03,
				CASE WHEN MONTH(T0.[DocDate]) = 4 THEN -T1.[Quantity] ELSE 0 END AS M04,
				CASE WHEN MONTH(T0.[DocDate]) = 5 THEN -T1.[Quantity] ELSE 0 END AS M05,
				CASE WHEN MONTH(T0.[DocDate]) = 6 THEN -T1.[Quantity] ELSE 0 END AS M06,
				CASE WHEN MONTH(T0.[DocDate]) = 7 THEN -T1.[Quantity] ELSE 0 END AS M07,
				CASE WHEN MONTH(T0.[DocDate]) = 8 THEN -T1.[Quantity] ELSE 0 END AS M08,
				CASE WHEN MONTH(T0.[DocDate]) = 9 THEN -T1.[Quantity] ELSE 0 END AS M09,
				CASE WHEN MONTH(T0.[DocDate]) = 10 THEN -T1.[Quantity] ELSE 0 END AS M10,
				CASE WHEN MONTH(T0.[DocDate]) = 11 THEN -T1.[Quantity] ELSE 0 END AS M11,
				CASE WHEN MONTH(T0.[DocDate]) = 12 THEN -T1.[Quantity] ELSE 0 END AS M12
				FROM ORIN T0
				JOIN RIN1 T1 ON T0.[DocEntry] = T1.[DocEntry]
				WHERE YEAR(T0.[DocDate]) = '$Year' AND T1.[ItemCode] IS NOT NULL AND T0.[CANCELED] = 'N' $SlpCode $CardCode
				) W1 
				GROUP BY W1.[ItemCode]
			) P1
			LEFT JOIN OITM P2 ON P1.[ItemCode] = P2.[ItemCode]";
	if($Year >= 2023) {
		$SQL.="LEFT JOIN KBI_DB2022.dbo.OITM P3 ON P1.[Itemcode] = P3.[ItemCode]";
	}
	if($Year >= 2023) {
		$SQL .="INNER JOIN OITW P4 ON P1.[ItemCode] = P4.[ItemCode] AND P4.[OnHand] != 0
				INNER JOIN OWHS P5 ON P4.[WhsCode]  = P5.[WhsCode] AND P5.[Location] IN (1,3)";
	}else{
		$SQL .="INNER JOIN SBO_KBI2023.dbo.OITW P4 ON P1.[ItemCode] = P4.[ItemCode] AND P4.[OnHand] != 0
				INNER JOIN SBO_KBI2023.dbo.OWHS P5 ON P4.[WhsCode]  = P5.[WhsCode] AND P5.[Location] IN (1,3)";
	}
	$SQL .="WHERE P2.[InvntItem] = 'Y' $Product
			GROUP BY
				P1.ItemCode, P2.ItemName, P2.U_ProductStatus, P2.BuyUnitMsr,";
		if($Year >= 2023) {
			$SQL .="(CASE WHEN P2.LastPurDat = '2022-12-31' THEN ISNULL(P3.LastPurPrc,P2.LastPurPrc) ELSE P2.LastPurPrc END)*1.07,
					(CASE WHEN P2.LastPurDat = '2022-12-31' THEN ISNULL(P3.LastPurDat,P2.LastPurDat) ELSE P2.LastPurDat END),";
		}else{
			$SQL .="(P2.LastPurPrc)*1.07, (P2.LastPurDat),";
		}
		$SQL .="P1.M01, P1.M02, P1.M03, P1.M04, P1.M05, P1.M06, P1.M07, P1.M08, P1.M09, P1.M10, P1.M11, P1.M12
			ORDER BY P1.ItemCode";
	if($Year >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานประวัติการขายสินค้า บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานประวัติการขายสินค้า บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// Header
	$sheet->setCellValue('A1',"ลำดับ");
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"รหัสสินค้า");
	$spreadsheet->getActiveSheet()->mergeCells('B1:B2');
	$sheet->setCellValue('C1',"ชือสินค้า");
	$spreadsheet->getActiveSheet()->mergeCells('C1:C2');
	$sheet->setCellValue('D1',"สถานะสินค้า");
	$spreadsheet->getActiveSheet()->mergeCells('D1:D2');
	$sheet->setCellValue('E1',"สินค้าคงคลัง");
	$spreadsheet->getActiveSheet()->mergeCells('E1:E2');
	$sheet->setCellValue('F1',"หน่วย");
	$spreadsheet->getActiveSheet()->mergeCells('F1:F2');
	$sheet->setCellValue('G1',"ต้นทุน");
	$spreadsheet->getActiveSheet()->mergeCells('G1:G2');
	$sheet->setCellValue('H1',"วันที่รับเข้าล่าสุด");
	$spreadsheet->getActiveSheet()->mergeCells('H1:H2');
	$sheet->setCellValue('I1',"ยอดขายปี ".$Year);
	$spreadsheet->getActiveSheet()->mergeCells('I1:T1');
	$mCell = ['0','I','J','K','L','M','N','O','P','Q','R','S','T'];
	for($m = 1; $m <= 12; $m++) {
		$sheet->setCellValue($mCell[$m]."2",FullMonth($m));
	}
	// Add Style Header
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->getStyle('A1:I1')->applyFromArray($PageHeader);
	$sheet->getStyle('I2:T2')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(54);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	for($m = 1; $m <= 12; $m++) {
		$spreadsheet->getActiveSheet()->getColumnDimension($mCell[$m])->setWidth(13);
	}
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);

	// Style Body
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$Row = 2; $No = 0;
	while($result = odbc_fetch_array($QRY)) {
		$Row++; $No++;
		// ลำดับ
		$sheet->setCellValue('A'.$Row,$No);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		// รหัสสินค้า
		$sheet->setCellValue('B'.$Row,$result['ItemCode']);
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

		// ชื่อสินค้า
		$sheet->setCellValue('C'.$Row,conutf8($result['ItemName']));

		// สถานะ
		$sheet->setCellValue('D'.$Row,$result['U_ProductStatus']);
		$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);

		// สถานะคงคลัง
		$sheet->setCellValue('E'.$Row,$result['OnHand']);
		$sheet->getStyle('E'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('E'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// หน่วย
		$sheet->setCellValue('F'.$Row,conutf8($result['BuyUnitMsr']));
		$sheet->getStyle('F'.$Row)->applyFromArray($TextCenter);

		if($VisCost == true) {
			// ต้นทุนล่าสุด
			$sheet->setCellValue('G'.$Row,$result['LastPurc']);
			$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);
			$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");

			// วันที่เข้าล่าสุด
			$sheet->setCellValue('H'.$Row,date("d/m/Y",strtotime($result['LastPurDat'])));
			$sheet->getStyle('H'.$Row)->applyFromArray($TextCenter);
		}

		// เดือน ม.ค. - ธ.ค.
		for($m = 1; $m <= 12; $m++){
			if($m < 10) {
				if($result['M0'.$m] != 0) {
					$sheet->setCellValue($mCell[$m].$Row,$result['M0'.$m]);
					$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
					$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0");
				}else{
					$sheet->setCellValue($mCell[$m].$Row,"-");
					$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
				}
			}else{
				if($result['M'.$m] != 0) {
					$sheet->setCellValue($mCell[$m].$Row,$result['M'.$m]);
					$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
					$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0");
				}else{
					$sheet->setCellValue($mCell[$m].$Row,"-");
					$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
				}
			}
		}
	}
	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานประวัติการขายสินค้า - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/HisItem/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'HisItem', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

// $arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
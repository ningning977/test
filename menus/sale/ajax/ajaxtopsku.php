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
if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}


if($_GET['p'] == 'GetSlpCode') {
	$SQL =
		"SELECT 
			T0.ukey, CONCAT(T1.uName,' ',T1.uLastName) AS 'FullName', T1.uNickName AS 'NickName',
			GROUP_CONCAT(DISTINCT(T2.SlpCode)) AS 'SlpCode', T3.DeptCode, T1.LvCode, T0.DocStatus 
		FROM saletarget T0
		LEFT JOIN users T1 ON T0.Ukey = T1.uKey
		LEFT JOIN oslp T2 ON T0.Ukey = T2.Ukey
		LEFT JOIN positions T3 ON T1.LvCode = T3.LvCode 
		WHERE T0.DocYear = 2023 AND T0.DocStatus != 'I' AND T1.uName IS NOT NULL
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

		$arrCol[$i]['SlpCode']  = $RST['SlpCode'];
		$arrCol[$i]['SlpName']  = $SlpName;
		$arrCol[$i]['DeptCode'] = $RST['DeptCode'];
		$i++;
	}
	$arrCol['Rows'] = $i;
}

if($_GET['p'] == "SearchData") {
	$filt_top = $_POST['filt_top'];
	$filt_y   = $_POST['filt_year'];
	$filt_m1  = $_POST['filt_month1'];
	$filt_m2  = $_POST['filt_month2'];
	$filt_t   = $_POST['filt_TeamCode'];
	$filt_s   = $_POST['filt_SlpCode'];
	$filt_c   = $_POST['filt_CardCode'];

	$crnt_y   = $filt_y;
	$prev_y   = $crnt_y-1;
	if($filt_top == "0" || $filt_top == 0) {
		$TOP_SAP = "";
	} else {
		$TOP_SAP = "TOP ".$filt_top;
	}

	if($filt_t != "ALL") {
		switch($filt_t) {
			case "MT1": $TeamCode = "('MT1','EXP')"; break;
			case "MT2": $TeamCode = "('MT2')"; break;
			case "TT2": $TeamCode = "('TT2')"; break;
			case "TT1": $TeamCode = "('TT1','OUL')"; break;
			case "ONL": $TeamCode = "('ONL')"; break;
			case "PTA": $TeamCode = "('PITA')"; break;
		}
		$T_SAP = "AND T2.U_Dim1 IN ".$TeamCode;
	} else {
		$T_SAP = "";
	}

	if($filt_s != "ALL") {
		$S_SAP = "AND T0.SlpCode IN ($filt_s)";
	} else {
		$S_SAP = "";
	}

	if($filt_c != "ALL") {
		$C_SAP = "AND T0.CardCode = '$filt_c'";
	} else {
		$C_SAP = "";
	}

	$SortType = explode("::",$_POST['SortType']);
	switch($SortType[0]) {
		case "NULL":
			$OrderSQL = "B0.ItemCode ".$SortType[2];
		break;
		case "PREV":
			switch($SortType[1]) {
				case "QTY": $FieldSort = "Qty"; break;
				case "SAL": $FieldSort = "LineTotal"; break;
				//case "GP" : $FieldSort = "LineGP"; break;
			}
			$OrderSQL = "SUM(Prev_$FieldSort) ".$SortType[2];
		break;
		case "CRNT":
			switch($SortType[1]) {
				case "QTY": $FieldSort = "Qty"; break;
				case "SAL": $FieldSort = "LineTotal"; break;
				//case "GP" : $FieldSort = "LineGP"; break;
			}
			$OrderSQL = "SUM(Crnt_$FieldSort) ".$SortType[2];
		break;
	}

	if($crnt_y == 2023 && $prev_y == 2022) {
		$U_SAP = 
			"UNION ALL
			SELECT
				T1.ItemCode, YEAR(T0.DocDate) AS 'Year', SUM(T1.Quantity) AS 'Qty', SUM(T1.LineTotal) AS 'LineTotal', SUM(T1.GrssProfit) AS 'LineGP'
			FROM KBI_DB2022.dbo.OINV T0
			LEFT JOIN KBI_DB2022.dbo.INV1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN KBI_DB2022.dbo.OSLP T2 ON T0.SlpCode  = T2.SlpCode
			WHERE ((YEAR(T0.DocDate) BETWEEN $prev_y AND $prev_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%' $T_SAP $S_SAP $C_SAP
			GROUP BY T1.ItemCode, YEAR(T0.DocDate)
			UNION ALL
			SELECT
				T1.ItemCode, YEAR(T0.DocDate) AS 'Year', -SUM(T1.Quantity) AS 'Qty', -SUM(T1.LineTotal) AS 'LineTotal', -SUM(T1.GrssProfit) AS 'LineGP'
			FROM KBI_DB2022.dbo.ORIN T0
			LEFT JOIN KBI_DB2022.dbo.RIN1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN KBI_DB2022.dbo.OSLP T2 ON T0.SlpCode  = T2.SlpCode
			WHERE ((YEAR(T0.DocDate) BETWEEN $prev_y AND $prev_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%' $T_SAP $S_SAP $C_SAP
			GROUP BY T1.ItemCode, YEAR(T0.DocDate)";
	} else {
		$U_SAP = "";
	}

	$SQL1 =
		"SELECT $TOP_SAP
			B0.ItemCode, B1.ItemName, B1.U_ProductStatus, B1.SalUnitMsr,
			SUM(B0.Prev_Qty) AS 'Prev_Qty', SUM(B0.Prev_LineTotal) AS 'Prev_LineTotal',
			CASE WHEN SUM(B0.Prev_LineTotal) = 0 THEN 0 ELSE (SUM(B0.Prev_LineGP)/SUM(B0.Prev_LineTotal))*100 END AS 'Prev_GP',
			SUM(B0.Crnt_Qty) AS 'Crnt_Qty', SUM(B0.Crnt_LineTotal) AS 'Crnt_LineTotal',
			CASE WHEN SUM(B0.Crnt_LineTotal) = 0 THEN 0 ELSE (SUM(B0.Crnt_LineGP)/SUM(B0.Crnt_LineTotal))*100 END AS 'Crnt_GP',
			CASE WHEN SUM(B0.Crnt_Qty) > 0 THEN ((SUM(B0.Crnt_Qty)-SUM(B0.Prev_Qty))/SUM(B0.Crnt_Qty))*100 ELSE 0 END AS 'Pcnt'
		FROM (
			SELECT
				A0.ItemCode,
				CASE WHEN A0.Year = $prev_y THEN SUM(A0.Qty) ELSE 0 END AS 'Prev_Qty',
				CASE WHEN A0.Year = $prev_y THEN SUM(A0.LineTotal) ELSE 0 END AS 'Prev_LineTotal',
				CASE WHEN A0.Year = $prev_y THEN SUM(A0.LineGP) ELSE 0 END AS 'Prev_LineGP',
				CASE WHEN A0.Year = $crnt_y THEN SUM(A0.Qty) ELSE 0 END AS 'Crnt_Qty',
				CASE WHEN A0.Year = $crnt_y THEN SUM(A0.LineTotal) ELSE 0 END AS 'Crnt_LineTotal',
				CASE WHEN A0.Year = $crnt_y THEN SUM(A0.LineGP) ELSE 0 END AS 'Crnt_LineGP'
			FROM (
				SELECT
					T1.ItemCode, YEAR(T0.DocDate) AS 'Year', SUM(T1.Quantity) AS 'Qty', SUM(T1.LineTotal) AS 'LineTotal', SUM(T1.GrssProfit) AS 'LineGP'
				FROM OINV T0
				LEFT JOIN INV1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T0.SlpCode  = T2.SlpCode
				WHERE ((YEAR(T0.DocDate) BETWEEN $prev_y AND $crnt_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%' $T_SAP $S_SAP $C_SAP
				GROUP BY T1.ItemCode, YEAR(T0.DocDate)
				UNION ALL
				SELECT
					T1.ItemCode, YEAR(T0.DocDate) AS 'Year', -SUM(T1.Quantity) AS 'Qty', -SUM(T1.LineTotal) AS 'LineTotal', -SUM(T1.GrssProfit) AS 'LineGP'
				FROM ORIN T0
				LEFT JOIN RIN1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T0.SlpCode  = T2.SlpCode
				WHERE ((YEAR(T0.DocDate) BETWEEN $prev_y AND $crnt_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%' $T_SAP $S_SAP $C_SAP
				GROUP BY T1.ItemCode, YEAR(T0.DocDate)
				$U_SAP
			) A0
			GROUP BY A0.ItemCode, A0.YEAR
		) B0
		LEFT JOIN OITM B1 ON B0.ItemCode = B1.ItemCode
		GROUP BY B0.ItemCode, B1.ItemName, B1.U_ProductStatus, B1.SalUnitMsr
		ORDER BY $OrderSQL";
	// echo $SQL1;
	if($filt_t == 'PTA') {
		$Rows = ChkRowPITA($SQL1);
	}else{
		if($filt_y >= 2023) {
			$Rows = ChkRowSAP($SQL1);
		} else {
			$Rows = ChkRowSAP8($SQL1);
		}
	}
	
	if($Rows == 0) {
		$arrCol['Rows'] = 0;
	} else {
		if($filt_t == 'PTA') {
			$QRY1 = PITASelect($SQL1);
		}else{
			if($filt_y >= 2023) {
				$QRY1 = SAPSelect($SQL1);
			} else {
				$QRY1 = conSAP8($SQL1);
			}
		}

		$SQL_SKU =  
			"SELECT GROUP_CONCAT(T0.ItemCode) AS ItemCode FROM skubook_header T0";
		$RST_SKU = MySQLSelect($SQL_SKU);
		$SKU = explode(',', $RST_SKU['ItemCode']);

		$i = 0;
		while($RST1 = odbc_fetch_array($QRY1)) {
			if(array_search($RST1['ItemCode'],$SKU) !== false) {
				$arrCol[$i]['ItemCode'] = "<a class='fw-bolder' href='?p=sku_book&ItemCode=".$RST1['ItemCode']."' target='_blank'>".$RST1['ItemCode']."</a>";
			}else{
				$arrCol[$i]['ItemCode'] = $RST1['ItemCode'];
			}
			$arrCol[$i]['ItemName']        = conutf8($RST1['ItemName']);
			$arrCol[$i]['U_ProductStatus'] = conutf8($RST1['U_ProductStatus']);
			$arrCol[$i]['SalUnitMsr']      = conutf8($RST1['SalUnitMsr']);
			$arrCol[$i]['Prev_Qty']        = number_format($RST1['Prev_Qty'],0);
			$arrCol[$i]['Prev_LineTotal']  = number_format($RST1['Prev_LineTotal'],2);
			$arrCol[$i]['Prev_GP']         = number_format($RST1['Prev_GP'],2);
			$arrCol[$i]['Crnt_Qty']        = number_format($RST1['Crnt_Qty'],0);
			$arrCol[$i]['Crnt_LineTotal']  = number_format($RST1['Crnt_LineTotal'],2);
			$arrCol[$i]['Crnt_GP']         = number_format($RST1['Crnt_GP'],2);
			if($RST1['Pcnt'] < 0) {
				$arrCol[$i]['PcntCls']     = " table-danger text-danger";
			} else {
				$arrCol[$i]['PcntCls']     = " table-success text-success";
			}
			$arrCol[$i]['Pcnt']            = number_format($RST1['Pcnt'],2);
			$i++;
		}
		$arrCol['Rows'] = $i;
	}

}

if($_GET['p'] == 'ExportData') {
	$filt_top = $_POST['filt_top'];
	$filt_y   = $_POST['filt_year'];
	$filt_m1  = $_POST['filt_month1'];
	$filt_m2  = $_POST['filt_month2'];
	$filt_t   = $_POST['filt_TeamCode'];
	$filt_s   = $_POST['filt_SlpCode'];
	$filt_c   = $_POST['filt_CardCode'];
	$crnt_y   = $filt_y;
	$prev_y   = $crnt_y-1;

	if($filt_top == "0" || $filt_top == 0) {
		$TOP_SAP = "";
	} else {
		$TOP_SAP = "TOP ".$filt_top;
	}

	if($filt_t != "ALL") {
		switch($filt_t) {
			case "MT1": $TeamCode = "('MT1','EXP')"; break;
			case "MT2": $TeamCode = "('MT2')"; break;
			case "TT2": $TeamCode = "('TT2')"; break;
			case "TT1": $TeamCode = "('TT1','OUL')"; break;
			case "ONL": $TeamCode = "('ONL')"; break;
			case "PTA": $TeamCode = "('PITA')"; break;
		}
		$T_SAP = "AND T2.U_Dim1 IN ".$TeamCode;
	} else {
		$T_SAP = "";
	}

	if($filt_s != "ALL") {
		$S_SAP = "AND T0.SlpCode IN ($filt_s)";
	} else {
		$S_SAP = "";
	}

	if($filt_c != "ALL") {
		$C_SAP = "AND T0.CardCode = '$filt_c'";
	} else {
		$C_SAP = "";
	}

	$SortType = explode("::",$_POST['SortType']);
	switch($SortType[0]) {
		case "NULL":
			$OrderSQL = "B0.ItemCode ".$SortType[2];
		break;
		case "PREV":
			switch($SortType[1]) {
				case "QTY": $FieldSort = "Qty"; break;
				case "SAL": $FieldSort = "LineTotal"; break;
			}
			$OrderSQL = "SUM(Prev_$FieldSort) ".$SortType[2];
		break;
		case "CRNT":
			switch($SortType[1]) {
				case "QTY": $FieldSort = "Qty"; break;
				case "SAL": $FieldSort = "LineTotal"; break;
			}
			$OrderSQL = "SUM(Crnt_$FieldSort) ".$SortType[2];
		break;
	}

	if($crnt_y == 2023 && $prev_y == 2022) {
		$U_SAP = 
			"UNION ALL
			SELECT
				T1.ItemCode, YEAR(T0.DocDate) AS 'Year', SUM(T1.Quantity) AS 'Qty', SUM(T1.LineTotal) AS 'LineTotal', SUM(T1.GrssProfit) AS 'LineGP'
			FROM KBI_DB2022.dbo.OINV T0
			LEFT JOIN KBI_DB2022.dbo.INV1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN KBI_DB2022.dbo.OSLP T2 ON T0.SlpCode  = T2.SlpCode
			WHERE ((YEAR(T0.DocDate) BETWEEN $prev_y AND $prev_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%' $T_SAP $S_SAP $C_SAP
			GROUP BY T1.ItemCode, YEAR(T0.DocDate)
			UNION ALL
			SELECT
				T1.ItemCode, YEAR(T0.DocDate) AS 'Year', -SUM(T1.Quantity) AS 'Qty', -SUM(T1.LineTotal) AS 'LineTotal', -SUM(T1.GrssProfit) AS 'LineGP'
			FROM KBI_DB2022.dbo.ORIN T0
			LEFT JOIN KBI_DB2022.dbo.RIN1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN KBI_DB2022.dbo.OSLP T2 ON T0.SlpCode  = T2.SlpCode
			WHERE ((YEAR(T0.DocDate) BETWEEN $prev_y AND $prev_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%' $T_SAP $S_SAP $C_SAP
			GROUP BY T1.ItemCode, YEAR(T0.DocDate)";
	} else {
		$U_SAP = "";
	}

	$SQL1 =
		"SELECT $TOP_SAP
			B0.ItemCode, B1.ItemName, B1.U_ProductStatus, B1.SalUnitMsr,
			SUM(B0.Prev_Qty) AS 'Prev_Qty', SUM(B0.Prev_LineTotal) AS 'Prev_LineTotal',
			CASE WHEN SUM(B0.Prev_LineTotal) = 0 THEN 0 ELSE (SUM(B0.Prev_LineGP)/SUM(B0.Prev_LineTotal)) END AS 'Prev_GP',
			SUM(B0.Crnt_Qty) AS 'Crnt_Qty', SUM(B0.Crnt_LineTotal) AS 'Crnt_LineTotal',
			CASE WHEN SUM(B0.Crnt_LineTotal) = 0 THEN 0 ELSE (SUM(B0.Crnt_LineGP)/SUM(B0.Crnt_LineTotal)) END AS 'Crnt_GP',
			CASE WHEN SUM(B0.Crnt_Qty) > 0 THEN ((SUM(B0.Crnt_Qty)-SUM(B0.Prev_Qty))/SUM(B0.Crnt_Qty)) ELSE 0 END AS 'Pcnt'
		FROM (
			SELECT
				A0.ItemCode,
				CASE WHEN A0.Year = $prev_y THEN SUM(A0.Qty) ELSE 0 END AS 'Prev_Qty',
				CASE WHEN A0.Year = $prev_y THEN SUM(A0.LineTotal) ELSE 0 END AS 'Prev_LineTotal',
				CASE WHEN A0.Year = $prev_y THEN SUM(A0.LineGP) ELSE 0 END AS 'Prev_LineGP',
				CASE WHEN A0.Year = $crnt_y THEN SUM(A0.Qty) ELSE 0 END AS 'Crnt_Qty',
				CASE WHEN A0.Year = $crnt_y THEN SUM(A0.LineTotal) ELSE 0 END AS 'Crnt_LineTotal',
				CASE WHEN A0.Year = $crnt_y THEN SUM(A0.LineGP) ELSE 0 END AS 'Crnt_LineGP'
			FROM (
				SELECT
					T1.ItemCode, YEAR(T0.DocDate) AS 'Year', SUM(T1.Quantity) AS 'Qty', SUM(T1.LineTotal) AS 'LineTotal', SUM(T1.GrssProfit) AS 'LineGP'
				FROM OINV T0
				LEFT JOIN INV1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T0.SlpCode  = T2.SlpCode
				WHERE ((YEAR(T0.DocDate) BETWEEN $prev_y AND $crnt_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%' $T_SAP $S_SAP $C_SAP
				GROUP BY T1.ItemCode, YEAR(T0.DocDate)
				UNION ALL
				SELECT
					T1.ItemCode, YEAR(T0.DocDate) AS 'Year', -SUM(T1.Quantity) AS 'Qty', -SUM(T1.LineTotal) AS 'LineTotal', -SUM(T1.GrssProfit) AS 'LineGP'
				FROM ORIN T0
				LEFT JOIN RIN1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T0.SlpCode  = T2.SlpCode
				WHERE ((YEAR(T0.DocDate) BETWEEN $prev_y AND $crnt_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%' $T_SAP $S_SAP $C_SAP
				GROUP BY T1.ItemCode, YEAR(T0.DocDate)
				$U_SAP
			) A0
			GROUP BY A0.ItemCode, A0.YEAR
		) B0
		LEFT JOIN OITM B1 ON B0.ItemCode = B1.ItemCode
		GROUP BY B0.ItemCode, B1.ItemName, B1.U_ProductStatus, B1.SalUnitMsr
		ORDER BY $OrderSQL";

	if($filt_t == 'PTA') {
		$Rows = ChkRowPITA($SQL1);
	}else{
		if($filt_y >= 2023) {
			$Rows = ChkRowSAP($SQL1);
		} else {
			$Rows = ChkRowSAP8($SQL1);
		}
	}

	if($Rows == 0) {
		$arrCol['Rows'] = 0;
	} else {
		if($filt_t == 'PTA') {
			$TextPITA = " (PITA)";
		}else{
			$TextPITA = "";
		}
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$spreadsheet->getProperties()
			->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
			->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
			->setTitle("รายงานสินค้าขายดี".$TextPITA." บจ.คิงบางกอก อินเตอร์เทรด")
			->setSubject("รายงานสินค้าขายดี".$TextPITA." บจ.คิงบางกอก อินเตอร์เทรด");
		$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
		$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
		$spreadsheet->setActiveSheetIndex(0);

		// Header
		$sheet->setCellValue('A1',"No.");
		$spreadsheet->getActiveSheet()->mergeCells('A1:A3');
		$sheet->setCellValue('B1',"รหัสสินค้า");
		$spreadsheet->getActiveSheet()->mergeCells('B1:B3');
		$sheet->setCellValue('C1',"ชื่อสินค้า");
		$spreadsheet->getActiveSheet()->mergeCells('C1:C3');
		$sheet->setCellValue('D1',"สถานะสินค้า");
		$spreadsheet->getActiveSheet()->mergeCells('D1:D3');
		$sheet->setCellValue('E1',"หน่วยขาย");
		$spreadsheet->getActiveSheet()->mergeCells('E1:E3');
		$sheet->setCellValue('F1',"ยอดขาย");
		$spreadsheet->getActiveSheet()->mergeCells('F1:K1');
		$sheet->setCellValue('F2',FullMonth($filt_m1)." - ".FullMonth($filt_m2)." ปี ".$prev_y);
		$spreadsheet->getActiveSheet()->mergeCells('F2:H2');
		$sheet->setCellValue('I2',FullMonth($filt_m1)." - ".FullMonth($filt_m2)." ปี ".$crnt_y);
		$spreadsheet->getActiveSheet()->mergeCells('I2:K2');
		$sheet->setCellValue('L1',"% การเติบโต (จำนวน)");
		$spreadsheet->getActiveSheet()->mergeCells('L1:L3');

		$sheet->setCellValue('F3',"จำนวน (หน่วย)");
		$sheet->setCellValue('G3',"มูลค่า (บาท)");
		$sheet->setCellValue('H3',"% GP");
		$sheet->setCellValue('I3',"จำนวน (หน่วย)");
		$sheet->setCellValue('J3',"มูลค่า (บาท)");
		$sheet->setCellValue('K3',"% GP");
		$PageHeader = [
			'font' => [ 'bold' => true, 'size' => 9.1 ],
			'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
		];
		$sheet->getStyle('A1:L1')->applyFromArray($PageHeader);
		$sheet->getStyle('A2:L2')->applyFromArray($PageHeader);
		$sheet->getStyle('A3:L3')->applyFromArray($PageHeader);
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(7.8); 
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(51);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14.5);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(25);
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(25);
		$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);

		if($filt_t == 'PTA') {
			$QRY1 = PITASelect($SQL1);
		}else{
			if($filt_y >= 2023) {
				$QRY1 = SAPSelect($SQL1);
			} else {
				$QRY1 = conSAP8($SQL1);
			}
		}

		$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
		$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
		$TextBold  = ['font' => [ 'bold' => true ]];

		$Row = 3; $No = 0;
		while($RST1 = odbc_fetch_array($QRY1)) {
			$Row++; $No++;
			// No
			$sheet->setCellValue('A'.$Row,$No);
			$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

			// รหัสสินค้า
			$sheet->setCellValue('B'.$Row,$RST1['ItemCode']);
			$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

			// ชื่อสินค้า
			$sheet->setCellValue('C'.$Row,conutf8($RST1['ItemName']));

			// สถานะสินค้า
			$sheet->setCellValue('D'.$Row,conutf8($RST1['U_ProductStatus']));
			$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);

			// หน่วยขาย
			$sheet->setCellValue('E'.$Row,conutf8($RST1['SalUnitMsr']));

			// จำนวน (หน่วย) pYear
			$sheet->setCellValue('F'.$Row,$RST1['Prev_Qty']);
			$spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0");

			// มูลค่า (บาท) pYear
			$sheet->setCellValue('G'.$Row,$RST1['Prev_LineTotal']);
			$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");

			// % GP
			$sheet->setCellValue('H'.$Row,$RST1['Prev_GP']);
			$sheet->getStyle('H'.$Row)->applyFromArray($TextCenter);
			$spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

			// จำนวน (หน่วย) cYear
			$sheet->setCellValue('I'.$Row,$RST1['Crnt_Qty']);
			$spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");

			// มูลค่า (บาท) cYear
			$sheet->setCellValue('J'.$Row,$RST1['Crnt_LineTotal']);
			$spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");

			// % GP
			$sheet->setCellValue('K'.$Row,$RST1['Crnt_GP']);
			$sheet->getStyle('K'.$Row)->applyFromArray($TextCenter);
			$spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

			// % การเติบโต (จำนวน)
			$sheet->setCellValue('L'.$Row,$RST1['Pcnt']);
			$sheet->getStyle('L'.$Row)->applyFromArray($TextCenter);
			$spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
		}
		$writer = new Xlsx($spreadsheet);
		$FileName = "รายงานสินค้าขายดี".$TextPITA." - ".date("YmdHis").".xlsx";
		if($filt_t == 'PTA') {
			$writer->save("../../../../FileExport/TopPTA/".$FileName);
			$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'TopPTA', logFile = '$FileName', DateCreate = NOW()";
		}else{
			$writer->save("../../../../FileExport/TopSku/".$FileName);
			$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'TopSku', logFile = '$FileName', DateCreate = NOW()";
		}
		MySQLInsert($InsertSQL);
		$arrCol['FileName'] = $FileName;
		$arrCol['Rows'] = $Row;
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
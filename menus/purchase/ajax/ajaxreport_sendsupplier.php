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
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == 'GetDataSup') {
	$Year = $_POST['Year'];
	$Taimas = $_POST['Taimas'];
	$Typesub = $_POST['Typesub'];

	switch ($Taimas) {
        case '1': $start = 1; $end = 3; break;
        case '2': $start = 4; $end = 6; break;
        case '3': $start = 7; $end = 9; break;
        case '4': $start = 10; $end = 12; break;
    }
	$ShowSQL = "";
	$i = $start;
	while ($i <= $end) {
		$m = ($i < 10) ? "0".$i : $i;
		$ShowSQL .= 
			"COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = ".$m." THEN 1 ELSE 0 END),0) AS [M".$m."_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = ".$m." AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M".$m."_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = ".$m." AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M".$m."_OVDUE]";
		$ShowSQL .= ($i != $end) ? "," : "";
		$i++;
	}
	$GroupCode = ($Typesub != 'ALL') ? "WHERE A1.GroupCode IN ($Typesub)" : "";

	$SQL = 
		"SELECT
			A0.CardCode, A1.CardName,
			COALESCE(SUM(CASE WHEN YEAR(A0.PODueDate) = $Year THEN 1 ELSE 0 END),0) AS [ALL_PODUE],
			COALESCE(SUM(CASE WHEN ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [ALL_INDUE],
			COALESCE(SUM(CASE WHEN ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [ALL_OVDUE],
			$ShowSQL
		FROM (
			SELECT DISTINCT
			T0.CardCode, CASE WHEN T3.GroupCode IN (101,127) THEN 'DMT' WHEN T3.GroupCode IN (126) THEN 'OVS' ELSE 'OTH' END 'SupGroup',
			T0.DocDueDate AS 'PODueDate', T2.DocDate AS 'GRPODate',
			CASE WHEN T2.DocDate IS NOT NULL THEN DATEDIFF(DAY, T0.DocDueDate, T2.DocDate) ELSE NULL END AS 'DIFF'
			FROM OPOR T0
			LEFT JOIN POR1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN OPDN T2 ON T1.TrgetEntry = T2.DocEntry AND T1.TargetType = 20
			LEFT JOIN OCRD T3 ON T0.CardCode = T3.CardCode
			WHERE YEAR(T0.DocDueDate) = $Year AND T0.CANCELED = 'N'
		) A0
		LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
		$GroupCode
		GROUP BY A0.CardCode, A1.CardName
		ORDER BY A1.CardName";
	// echo $SQL;
	$QRY = SAPSelect($SQL);
	$r = 0;
	while($RST = odbc_fetch_array($QRY)) {
		$arrCol[$r]['CardCode'] = "<a href='javascript:void(0);' onclick='ViewData(\"".$RST['CardCode']."\")'>".$RST['CardCode']."</a>";
		$arrCol[$r]['CardName'] = conutf8($RST['CardName']);

		$arrCol[$r]['ALLPodue'] = $RST['ALL_PODUE'];
		$arrCol[$r]['ALLIndue'] = $RST['ALL_INDUE'];
		$arrCol[$r]['ALLOvdue'] = $RST['ALL_OVDUE'];
		$arrCol[$r]['ALLPercent'] = ($RST['ALL_PODUE'] != 0) ? number_format(($RST['ALL_INDUE']/$RST['ALL_PODUE'])*100,2)."%" : "0.00%";

		$NumMonth = 1;
		for($month = $start; $month <= $end; $month++) {
			$m = ($month < 10) ? "0".$month : $month;
			$arrCol[$r]['M'.$NumMonth.'Podue'] = $RST['M'.$m.'_PODUE'];
			$arrCol[$r]['M'.$NumMonth.'Indue'] = $RST['M'.$m.'_INDUE'];
			$arrCol[$r]['M'.$NumMonth.'Ovdue'] = $RST['M'.$m.'_OVDUE'];
			$arrCol[$r]['M'.$NumMonth.'Percent'] = ($RST['M'.$m.'_PODUE'] != 0) ? number_format(($RST['M'.$m.'_INDUE']/$RST['M'.$m.'_PODUE'])*100,2)."%" : "0.00%";
			$NumMonth++;
		}
		$r++;
	}
}

if($_GET['a'] == 'ViewData') {
	$CardCode = $_POST['CardCode'];

	$SQL = 
		"SELECT
			T2.BeginStr+CAST(T0.DocNum AS VARCHAR) AS 'DocNum', T0.U_PONo AS 'RefDoc', T0.CardCode,
			T1.VisOrder+1 AS 'VisOrder', T1.ItemCode, T1.Dscription, T1.Quantity, T1.unitMsr,
			T0.DocDueDate, CASE WHEN GETDATE() >= T0.DocDueDate THEN DATEDIFF(DAY,T0.DocDueDate,GETDATE()) ELSE NULL END AS 'DIFF'
		FROM OPOR T0
		LEFT JOIN POR1 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
		WHERE (T0.DocDate >= '2023-01-01' AND T0.CANCELED = 'N' AND T0.DocStatus = 'O' AND T1.LineStatus = 'O') AND T0.CardCode = '$CardCode'
		ORDER BY T0.DocDueDate, T0.DocEntry, T1.VisOrder";
	$QRY = SAPSelect($SQL);
	$r = 0;
	while($RST = odbc_fetch_array($QRY)) {
		$arrCol[$r]['DocNum'] = $RST['DocNum'];
		$arrCol[$r]['RefDoc'] = $RST['RefDoc'];
		$arrCol[$r]['VisOrder'] = $RST['VisOrder'];
		$arrCol[$r]['ItemCode'] = $RST['ItemCode'];
		$arrCol[$r]['ItemName'] = conutf8($RST['Dscription']);
		$arrCol[$r]['Quantity'] = number_format($RST['Quantity'],0);
		$arrCol[$r]['unitMsr'] = conutf8($RST['unitMsr']);
		$arrCol[$r]['DocDueDate'] = date("d/m/Y",strtotime($RST['DocDueDate']));
		$arrCol[$r]['DIFF'] = ($RST['DIFF'] > 0) ? $RST['DIFF']." วัน" : "";
		
		$r++;
	}
}

function StrCell($c) {
	$StrCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
	return $StrCell;
}

if($_GET['a'] == 'Export') {
	$Year = $_POST['Year'];
	$Taimas = $_POST['Taimas'];
	$Typesub = $_POST['Typesub'];
	$GroupCode = ($Typesub != 'ALL') ? "WHERE A1.GroupCode IN ($Typesub)" : "";
	$SQL = 
		"SELECT
			A0.CardCode, A1.CardName,
			COALESCE(SUM(CASE WHEN YEAR(A0.PODueDate) = $Year THEN 1 ELSE 0 END),0) AS [ALL_PODUE],
			COALESCE(SUM(CASE WHEN ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [ALL_INDUE],
			COALESCE(SUM(CASE WHEN ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [ALL_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 01 THEN 1 ELSE 0 END),0) AS [M01_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 01 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M01_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 01 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M01_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 02 THEN 1 ELSE 0 END),0) AS [M02_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 02 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M02_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 02 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M02_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 03 THEN 1 ELSE 0 END),0) AS [M03_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 03 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M03_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 03 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M03_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 04 THEN 1 ELSE 0 END),0) AS [M04_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 04 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M04_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 04 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M04_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 05 THEN 1 ELSE 0 END),0) AS [M05_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 05 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M05_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 05 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M05_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 06 THEN 1 ELSE 0 END),0) AS [M06_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 06 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M06_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 06 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M06_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 07 THEN 1 ELSE 0 END),0) AS [M07_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 07 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M07_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 07 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M07_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 08 THEN 1 ELSE 0 END),0) AS [M08_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 08 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M08_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 08 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M08_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 09 THEN 1 ELSE 0 END),0) AS [M09_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 09 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M09_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 09 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M09_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 10 THEN 1 ELSE 0 END),0) AS [M10_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 10 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M10_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 10 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M10_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 11 THEN 1 ELSE 0 END),0) AS [M11_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 11 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M11_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 11 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M11_OVDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 12 THEN 1 ELSE 0 END),0) AS [M12_PODUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 12 AND ((A0.SupGroup = 'DMT' AND A0.DIFF <= 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF <= 15)) THEN 1 ELSE 0 END),0) AS [M12_INDUE],
			COALESCE(SUM(CASE WHEN MONTH(A0.PODueDate) = 12 AND ((A0.SupGroup = 'DMT' AND A0.DIFF > 1) OR (A0.SupGroup = 'OVS' AND A0.DIFF > 15)) THEN 1 ELSE 0 END),0)   AS [M12_OVDUE]
		FROM (
			SELECT DISTINCT
			T0.CardCode, CASE WHEN T3.GroupCode IN (101,127) THEN 'DMT' WHEN T3.GroupCode IN (126) THEN 'OVS' ELSE 'OTH' END 'SupGroup',
			T0.DocDueDate AS 'PODueDate', T2.DocDate AS 'GRPODate',
			CASE WHEN T2.DocDate IS NOT NULL THEN DATEDIFF(DAY, T0.DocDueDate, T2.DocDate) ELSE NULL END AS 'DIFF'
			FROM OPOR T0
			LEFT JOIN POR1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN OPDN T2 ON T1.TrgetEntry = T2.DocEntry AND T1.TargetType = 20
			LEFT JOIN OCRD T3 ON T0.CardCode = T3.CardCode
			WHERE YEAR(T0.DocDueDate) = $Year AND T0.CANCELED = 'N'
		) A0
		LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
		$GroupCode
		GROUP BY A0.CardCode, A1.CardName
		ORDER BY A1.CardName";
	$QRY = SAPSelect($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
	->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
	->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
	->setTitle("รายงานการส่งซัพพลายเออร์ บจ.คิงบางกอก อินเตอร์เทรด")
	->setSubject("รายงานการส่งซัพพลายเออร์ บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	$PageHeader = [ 'font' => [ 'bold' => true, 'size' => 9.1 ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenterBold = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRightBold  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$Row = 1; $Col = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "รหัสซัพฯ");
	$spreadsheet->getActiveSheet()->mergeCells(StrCell($Col).'1:'.StrCell($Col).'2');
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ชื่อซัพพลายเออร์");
	$spreadsheet->getActiveSheet()->mergeCells(StrCell($Col).'1:'.StrCell($Col).'2');
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(45);
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ภาพรวมปี ".$Year);
	$spreadsheet->getActiveSheet()->mergeCells(StrCell($Col).'1:'.StrCell($Col+3).'1');
	$Col = $Col+4;

	$i = 1;
	while ($i <= 12) {
		$m = $i;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "เดือน ".FullMonth($m)." ปี ".$Year);
		$spreadsheet->getActiveSheet()->mergeCells(StrCell($Col).'1:'.StrCell($Col+3).'1');
		$Col = $Col+4;
		$i++;
	}

	$Row++; $Col = 3;
	for($i = 1; $i <= 13; $i++) {
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "P/O ทั้งหมด");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
		$Col++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "P/O ตรงกำหนด");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
		$Col++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "P/O เกินกำหนด");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
		$Col++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "% ตรงกำหนด");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
		$Col++;
	}

	$sheet->getStyle('A1:'.StrCell($Col-1).'1')->applyFromArray($PageHeader);
	$sheet->getStyle('A2:'.StrCell($Col-1).'2')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);
	$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(18);

	$spreadsheet->SetActiveSheetIndex(0)->freezePane('C3');
	
	$Row = 3; $Col = 1;
	while($RST = odbc_fetch_array($QRY)) {
		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['CardCode']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;
		
		$sheet->setCellValueByColumnAndRow($Col, $Row, conutf8($RST['CardName']));
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['ALL_PODUE']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['ALL_INDUE']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['ALL_OVDUE']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, (($RST['ALL_PODUE'] != 0) ? ($RST['ALL_INDUE']/$RST['ALL_PODUE']) : 0));
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
		$Col++;

		for($month = 1; $month <= 12; $month++) {
			$m = ($month < 10) ? "0".$month : $month;
			$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['M'.$m.'_PODUE']);
			$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
			$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
			$Col++;

			$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['M'.$m.'_INDUE']);
			$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
			$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
			$Col++;

			$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['M'.$m.'_OVDUE']);
			$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
			$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
			$Col++;

			$sheet->setCellValueByColumnAndRow($Col, $Row, (($RST['M'.$m.'_PODUE'] != 0) ? ($RST['M'.$m.'_INDUE']/$RST['M'.$m.'_PODUE']) : 0));
			$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
			$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
			$Col++;
		}

		$Row++;
		$Col = 1;
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานการส่งซัพพลายเออร์ - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/SendSupplier/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'SendSupplier', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
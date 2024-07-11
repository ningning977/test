<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

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
	$Year = $_POST['Year'];
	$Month = $_POST['Month'];

	$SQL = 
		"SELECT
			T0.DocEntry, CONCAT(T1.loginame,' ',T1.logilastname) AS 'LogiName' , T0.DocNum,
			CASE WHEN T0.BillType LIKE 'OWA%' THEN (SELECT P0.DocNum FROM owas P0 WHERE P0.DocEntry = T0.BillEntry) ELSE T2.DocNum END AS 'BillDocNum', 
			CASE WHEN T0.BillType LIKE 'OWA%' THEN (SELECT P0.CusCode FROM owas P0 WHERE P0.DocEntry = T0.BillEntry) ELSE T2.CardCode END AS 'CardCode', 
			CASE WHEN T0.BillType LIKE 'OWA%' THEN (SELECT P0.CusName FROM owas P0 WHERE P0.DocEntry = T0.BillEntry) ELSE T3.CardName END AS 'CardName', T0.ShippingName, T0.ReceiveDate,
			CASE WHEN T0.TeamCode IN ('MT1', 'EXP') THEN T0.ShipCost ELSE 0 END AS 'COST_MT1',
			CASE WHEN T0.TeamCode IN ('MT2') THEN T0.ShipCost ELSE 0 END AS 'COST_MT2',
			CASE WHEN T0.TeamCode IN ('TT1') THEN T0.ShipCost ELSE 0 END AS 'COST_TT1',
			CASE WHEN T0.TeamCode IN ('TT2') THEN T0.ShipCost ELSE 0 END AS 'COST_TT2',
			CASE WHEN T0.TeamCode IN ('OUL') THEN T0.ShipCost ELSE 0 END AS 'COST_OUL',
			CASE WHEN T0.TeamCode IN ('ONL') THEN T0.ShipCost ELSE 0 END AS 'COST_ONL',
			CASE WHEN T0.TeamCode NOT IN ('MT1', 'EXP', 'MT2', 'TT1', 'TT2', 'OUL', 'ONL') THEN T0.ShipCost ELSE 0 END AS 'COST_KBI',
			CASE WHEN T0.WithdrawDate IS NOT NULL THEN 'Y' ELSE 'N' END AS 'Withdraw'
		FROM ship_header T0
		LEFT JOIN logistic T1 ON T0.logi_ukey = T1.logiID
		LEFT JOIN pack_header T2 ON T0.BillEntry = T2.BillEntry AND T0.BillType = T2.BillType
		LEFT JOIN OCRD T3 ON T2.CardCode = T3.CardCode
		WHERE YEAR(T0.ReceiveDate) = $Year AND MONTH(T0.ReceiveDate) = $Month AND T0.ShipStatus = 'A' AND T0.ShipCost > 0
		ORDER BY CASE WHEN T0.WithdrawDate IS NULL THEN 1 ELSE 2 END, T0.BillType, T0.DocEntry";
	$QRY = MySQLSelectX($SQL);
	$r = 0;
	while($RST = mysqli_fetch_array($QRY)) {
		$arrCol[$r]['CheckBox'] = "<input type='checkbox' class='form-check-input Chk_Doc' name='Chk_".$RST['DocEntry']."' id='Chk_".$RST['DocEntry']."' value='".$RST['DocEntry']."'>";
		$arrCol[$r]['LogiName'] = $RST['LogiName'];
		$arrCol[$r]['BillDocNum'] = $RST['BillDocNum'];
		$arrCol[$r]['CardCode'] = $RST['CardCode'];
		$arrCol[$r]['CardName'] = $RST['CardName'];
		$arrCol[$r]['ShippingName'] = $RST['ShippingName'];
		$arrCol[$r]['ReceiveDate'] = date("d/m/Y",strtotime($RST['ReceiveDate']));
		$arrCol[$r]['COST_MT1'] = ($RST['COST_MT1'] > 0) ? number_format($RST['COST_MT1']) : "-";
		$arrCol[$r]['COST_MT2'] = ($RST['COST_MT2'] > 0) ? number_format($RST['COST_MT2']) : "-";
		$arrCol[$r]['COST_TT1'] = ($RST['COST_TT1'] > 0) ? number_format($RST['COST_TT1']) : "-";
		$arrCol[$r]['COST_TT2'] = ($RST['COST_TT2'] > 0) ? number_format($RST['COST_TT2']) : "-";
		$arrCol[$r]['COST_OUL'] = ($RST['COST_OUL'] > 0) ? number_format($RST['COST_OUL']) : "-";
		$arrCol[$r]['COST_ONL'] = ($RST['COST_ONL'] > 0) ? number_format($RST['COST_ONL']) : "-";
		$arrCol[$r]['COST_KBI'] = ($RST['COST_KBI'] > 0) ? number_format($RST['COST_KBI']) : "-";
		$arrCol[$r]['Withdraw'] = $RST['Withdraw'];
		$r++;
	}
}

if($_GET['a'] == 'Export') {
	$DocEntry = $_POST['ID'];
	$Year = $_POST['Year'];
	$Month = $_POST['Month'];

	$SQL = 
		"SELECT
			T0.DocEntry, CONCAT(T1.loginame,' ',T1.logilastname) AS 'LogiName' , T0.DocNum,
			CASE WHEN T0.BillType LIKE 'OWA%' THEN (SELECT P0.DocNum FROM owas P0 WHERE P0.DocEntry = T0.BillEntry) ELSE T2.DocNum END AS 'BillDocNum', 
			CASE WHEN T0.BillType LIKE 'OWA%' THEN (SELECT P0.CusCode FROM owas P0 WHERE P0.DocEntry = T0.BillEntry) ELSE T2.CardCode END AS 'CardCode', 
			CASE WHEN T0.BillType LIKE 'OWA%' THEN (SELECT P0.CusName FROM owas P0 WHERE P0.DocEntry = T0.BillEntry) ELSE T3.CardName END AS 'CardName', T0.ShippingName, T0.ReceiveDate,
			CASE WHEN T0.TeamCode IN ('MT1', 'EXP') THEN T0.ShipCost ELSE 0 END AS 'COST_MT1',
			CASE WHEN T0.TeamCode IN ('MT2') THEN T0.ShipCost ELSE 0 END AS 'COST_MT2',
			CASE WHEN T0.TeamCode IN ('TT1') THEN T0.ShipCost ELSE 0 END AS 'COST_TT1',
			CASE WHEN T0.TeamCode IN ('TT2') THEN T0.ShipCost ELSE 0 END AS 'COST_TT2',
			CASE WHEN T0.TeamCode IN ('OUL') THEN T0.ShipCost ELSE 0 END AS 'COST_OUL',
			CASE WHEN T0.TeamCode IN ('ONL') THEN T0.ShipCost ELSE 0 END AS 'COST_ONL',
			CASE WHEN T0.TeamCode NOT IN ('MT1', 'EXP', 'MT2', 'TT1', 'TT2', 'OUL', 'ONL') THEN T0.ShipCost ELSE 0 END AS 'COST_KBI'
		FROM ship_header T0
		LEFT JOIN logistic T1 ON T0.logi_ukey = T1.logiID
		LEFT JOIN pack_header T2 ON T0.BillEntry = T2.BillEntry AND T0.BillType = T2.BillType
		LEFT JOIN OCRD T3 ON T2.CardCode = T3.CardCode
		WHERE YEAR(T0.ReceiveDate) = $Year AND MONTH(T0.ReceiveDate) = $Month AND T0.ShipStatus = 'A' AND T0.ShipCost > 0 AND T0.DocEntry IN ($DocEntry)";
	$QRY = MySQLSelectX($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("เบิกค่าขนส่ง บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("เบิกค่าขนส่ง บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$sheet->setCellValue('A1',"พนักงานขนส่ง");
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"เลขที่บิล");
	$spreadsheet->getActiveSheet()->mergeCells('B1:B2');
	$sheet->setCellValue('C1',"รหัสลูกค้า");
	$spreadsheet->getActiveSheet()->mergeCells('C1:C2');
	$sheet->setCellValue('D1',"ชื่อลูกค้า");
	$spreadsheet->getActiveSheet()->mergeCells('D1:D2');
	$sheet->setCellValue('E1',"ชื่อขนส่ง");
	$spreadsheet->getActiveSheet()->mergeCells('E1:E2');
	$sheet->setCellValue('F1',"วันที่ส่ง");
	$spreadsheet->getActiveSheet()->mergeCells('F1:F2');
	$sheet->setCellValue('G1',"ค่าขนส่ง");
	$spreadsheet->getActiveSheet()->mergeCells('G1:M1');
	$sheet->setCellValue('G2',"MT1");
	$sheet->setCellValue('H2',"MT2");
	$sheet->setCellValue('I2',"TT1");
	$sheet->setCellValue('J2',"TT2");
	$sheet->setCellValue('K2',"หน้าร้าน");
	$sheet->setCellValue('L2',"ออนไลน์");
	$sheet->setCellValue('M2',"ส่วนกลาง");
	$sheet->getStyle('A1:M1')->applyFromArray($PageHeader);
	$sheet->getStyle('A2:M2')->applyFromArray($PageHeader);

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(55);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);

	$Row = 2;
	while($RST = mysqli_fetch_array($QRY)) {

		$SQL2 = "UPDATE ship_header SET WithdrawDate = NOW(),WithdrawUkey = '".$_SESSION['ukey']."' WHERE DocEntry = ".$RST['DocEntry'];
		$QRY2 = MySQLUpdate($SQL2);
		$Row++;

		// พนักงานขนส่ง
		$sheet->setCellValue('A'.$Row,$RST['LogiName']);

		// เลขที่บิล
		$sheet->setCellValue('B'.$Row,$RST['BillDocNum']);
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

		// รหัสลูกค้า
		$sheet->setCellValue('C'.$Row,$RST['CardCode']);
		$sheet->getStyle('C'.$Row)->applyFromArray($TextCenter);

		// ชื่อลูกค้า
		$sheet->setCellValue('D'.$Row,$RST['CardName']);

		// ชื่อขนส่ง
		$sheet->setCellValue('E'.$Row,$RST['ShippingName']);

		// วันที่ส่ง
		$sheet->setCellValue('F'.$Row,date("d/m/Y",strtotime($RST['ReceiveDate'])));
		$sheet->getStyle('F'.$Row)->applyFromArray($TextCenter);

		// ค่าขนส่ง
		if($RST['COST_MT1'] > 0) {
			$sheet->setCellValue('G'.$Row,$RST['COST_MT1']);
			$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('G'.$Row,"-");
		}
		$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);
		
		if($RST['COST_MT2'] > 0) {
			$sheet->setCellValue('H'.$Row,$RST['COST_MT2']);
			$spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('H'.$Row,"-");
		}
		$sheet->getStyle('H'.$Row)->applyFromArray($TextRight);

		if($RST['COST_TT1'] > 0) {
			$sheet->setCellValue('I'.$Row,$RST['COST_TT1']);
			$spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('I'.$Row,"-");
		}
		$sheet->getStyle('I'.$Row)->applyFromArray($TextRight);

		if($RST['COST_TT2'] > 0) {
			$sheet->setCellValue('J'.$Row,$RST['COST_TT2']);
			$spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('J'.$Row,"-");
		}
		$sheet->getStyle('J'.$Row)->applyFromArray($TextRight);

		if($RST['COST_OUL'] > 0) {
			$sheet->setCellValue('K'.$Row,$RST['COST_OUL']);
			$spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('K'.$Row,"-");
		}
		$sheet->getStyle('K'.$Row)->applyFromArray($TextRight);

		if($RST['COST_ONL'] > 0) {
			$sheet->setCellValue('L'.$Row,$RST['COST_ONL']);
			$spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('L'.$Row,"-");
		}
		$sheet->getStyle('L'.$Row)->applyFromArray($TextRight);

		if($RST['COST_KBI'] > 0) {
			$sheet->setCellValue('M'.$Row,$RST['COST_KBI']);
			$spreadsheet->getActiveSheet()->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('M'.$Row,"-");
		}
		$sheet->getStyle('M'.$Row)->applyFromArray($TextRight);
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "เบิกค่าขนส่ง - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/WithdrawShipcost/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'WithdrawShipcost', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
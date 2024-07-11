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

if($_GET['a'] == 'CallData') {
	$ItemCode    = $_POST['ItemCode'];
	if($_POST['ItemStatus'] == 'ALL') {
		$ItemStatus = NULL;
	}else{
		$ItemStatus = "AND A3.U_ProductStatus LIKE '".$_POST['ItemStatus']."%'";
	}
	if($_POST['TeamSelect'] == 'ALL') {
		$TeamSelect = NULL;
	}else{
		$TeamSelect = "AND A2.U_Dim1 = '".$_POST['TeamSelect']."'";
	}
	$YearSelect  = $_POST['YearSelect'];
	if($_POST['MonthSelect'] == 'ALL') {
		$MonthSelect_W = NULL;
		$MonthSelect_T = NULL;
	}else{
		$MonthSelect_W = "AND MONTH(W1.DocDueDate) = ".$_POST['MonthSelect']."";
		$MonthSelect_T = "AND MONTH(T1.DocDueDate) = ".$_POST['MonthSelect']."";
	}
	$Chk = 0;
	switch($_SESSION['DeptCode']){
		case 'DP001': 
		case 'DP002': 
		case 'DP004': 
			$Chk = 1;
			break;
		case 'DP003': 
			if($_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4) {
				$Chk = 1;
			}
			break;
		case 'DP005': 
		case 'DP006': 
		case 'DP007': 
		case 'DP008': 
			if($_SESSION['uClass'] == 18) {
				$Chk = 1;
			}
			break;
		case 'DP009': 
			$Chk = 1;
			break;
		default: break;
	}
	$SQL = "
		SELECT A0.CardCode, A1.CardName, A2.U_Dim1, A0.ItemCode, A3.ItemName, A3.U_ProductStatus, A0.WhsCode,
			(
				SELECT TOP 1 W0.OpenQty 
				FROM RDR1 W0
				LEFT JOIN ORDR W1 ON W0.DocEntry = W1.DocEntry 
				WHERE (YEAR(W1.DocDueDate) = $YearSelect $MonthSelect_W) AND W1.DocStatus = 'O'  AND W0.LineStatus = 'O' 
					AND W0.ItemCode = A0.ItemCode AND W1.CardCode = A0.CardCode AND W0.WhsCode = A0.WhsCode
				ORDER BY W0.OpenQty DESC
			) AS OpenQty,
			(
				SELECT TOP 1 W0.LineTotal 
				FROM RDR1 W0
				LEFT JOIN ORDR W1 ON W0.DocEntry = W1.DocEntry 
				WHERE (YEAR(W1.DocDueDate) = $YearSelect $MonthSelect_W) AND W1.DocStatus = 'O'  AND W0.LineStatus = 'O' 
					AND W0.ItemCode = A0.ItemCode AND W1.CardCode = A0.CardCode AND W0.WhsCode = A0.WhsCode
				ORDER BY W0.OpenQty DESC
			) AS LineTotal,  
			A3.LastPurPrc, A3.LastPurDat, A3.CardCode AS VCode, A4.CardName AS VName, A2.SlpName 
		FROM(
			SELECT DISTINCT T1.CardCode,T0.ItemCode, T0.WhsCode
			FROM RDR1 T0
			LEFT JOIN ORDR T1 ON T0.DocEntry = T1.DocEntry
			WHERE (YEAR(T1.DocDueDate) = $YearSelect $MonthSelect_T) AND T1.DocStatus = 'O'  AND T0.LineStatus = 'O'
		) A0
		LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
		LEFT JOIN OSLP A2 ON A1.SlpCode = A2.SlpCode
		LEFT JOIN OITM A3 ON A0.ItemCode = A3.ItemCode
		LEFT JOIN OCRD A4 ON A3.CardCode = A4.CardCode
		WHERE A0.ItemCode NOT LIKE '00-%' $ItemStatus $TeamSelect";
	$QRY = SAPSelect($SQL);
	// echo $SQL;
	$r = 0; $No = 1;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['No'] = $No;
		$arrCol[$r]['Team'] = $result['U_Dim1'];
		$arrCol[$r]['CardName'] = $result['CardCode']." - ".conutf8($result['CardName']);
		$arrCol[$r]['ItemCode'] = $result['ItemCode'];
		$arrCol[$r]['ItemName'] = conutf8($result['ItemName']);
		$arrCol[$r]['U_ProductStatus'] = $result['U_ProductStatus'];
		$arrCol[$r]['WhsCode'] = conutf8($result['WhsCode']);
		$arrCol[$r]['OpenQty'] = number_format($result['OpenQty'],0);
		$arrCol[$r]['LineTotal'] = number_format($result['LineTotal'],2);
		if($Chk == 1) {
			$arrCol[$r]['LastPurPrc'] = number_format($result['LastPurPrc'],2);
			$arrCol[$r]['LastPurDat'] = date("d/m/Y",strtotime($result['LastPurDat']));
		}
		if($_SESSION['DeptCode'] == 'DP004' || $_SESSION['DeptCode'] == 'DP002') {
			$arrCol[$r]['VName'] = $result['VCode']." - ".conutf8($result['VName']);
		}else{
			$arrCol[$r]['VName'] = conutf8($result['SlpName']);
		}
		$r++; $No++;
	}
}

if($_GET['a'] == 'Excel') {
	$ItemCode    = $_POST['ItemCode'];
	if($_POST['ItemStatus'] == 'ALL') {
		$ItemStatus = NULL;
	}else{
		$ItemStatus = "AND A3.U_ProductStatus LIKE '".$_POST['ItemStatus']."%'";
	}
	if($_POST['TeamSelect'] == 'ALL') {
		$TeamSelect = NULL;
	}else{
		$TeamSelect = "AND A2.U_Dim1 = '".$_POST['TeamSelect']."'";
	}
	$YearSelect  = $_POST['YearSelect'];
	if($_POST['MonthSelect'] == 'ALL') {
		$MonthSelect_W = NULL;
		$MonthSelect_T = NULL;
	}else{
		$MonthSelect_W = "AND MONTH(W1.DocDueDate) = ".$_POST['MonthSelect']."";
		$MonthSelect_T = "AND MONTH(T1.DocDueDate) = ".$_POST['MonthSelect']."";
	}
	$Chk = 0;
	switch($_SESSION['DeptCode']){
		case 'DP001': 
		case 'DP002': 
		case 'DP004': 
			$Chk = 1;
			break;
		case 'DP003': 
			if($_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4) {
				$Chk = 1;
			}
			break;
		case 'DP005': 
		case 'DP006': 
		case 'DP007': 
		case 'DP008': 
			if($_SESSION['uClass'] == 18) {
				$Chk = 1;
			}
			break;
		case 'DP009': 
			$Chk = 1;
			break;
		default: break;
	}
	$SQL = "
		SELECT A0.CardCode, A1.CardName, A2.U_Dim1, A0.ItemCode, A3.ItemName, A3.U_ProductStatus, A0.WhsCode,
			(
				SELECT TOP 1 W0.OpenQty 
				FROM RDR1 W0
				LEFT JOIN ORDR W1 ON W0.DocEntry = W1.DocEntry 
				WHERE (YEAR(W1.DocDueDate) = $YearSelect $MonthSelect_W) AND W1.DocStatus = 'O'  AND W0.LineStatus = 'O' 
					AND W0.ItemCode = A0.ItemCode AND W1.CardCode = A0.CardCode AND W0.WhsCode = A0.WhsCode
				ORDER BY W0.OpenQty DESC
			) AS OpenQty,
			(
				SELECT TOP 1 W0.LineTotal 
				FROM RDR1 W0
				LEFT JOIN ORDR W1 ON W0.DocEntry = W1.DocEntry 
				WHERE (YEAR(W1.DocDueDate) = $YearSelect $MonthSelect_W) AND W1.DocStatus = 'O'  AND W0.LineStatus = 'O' 
					AND W0.ItemCode = A0.ItemCode AND W1.CardCode = A0.CardCode AND W0.WhsCode = A0.WhsCode
				ORDER BY W0.OpenQty DESC
			) AS LineTotal,  
			A3.LastPurPrc, A3.LastPurDat, A3.CardCode AS VCode, A4.CardName AS VName, A2.SlpName 
			FROM(
				SELECT DISTINCT T1.CardCode,T0.ItemCode, T0.WhsCode
				FROM RDR1 T0
				LEFT JOIN ORDR T1 ON T0.DocEntry = T1.DocEntry
				WHERE (YEAR(T1.DocDueDate) = $YearSelect $MonthSelect_T) AND T1.DocStatus = 'O'  AND T0.LineStatus = 'O'
			) A0
		LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
		LEFT JOIN OSLP A2 ON A1.SlpCode = A2.SlpCode
		LEFT JOIN OITM A3 ON A0.ItemCode = A3.ItemCode
		LEFT JOIN OCRD A4 ON A3.CardCode = A4.CardCode
		WHERE A0.ItemCode NOT LIKE '00-%' $ItemStatus $TeamSelect";
	// echo $SQL;
	$QRY = SAPSelect($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงาน Back Order บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงาน Back Order บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// Header
	/*
		G -> H
		H -> I
		I -> J
		J -> K
		K -> L
	*/
	$sheet->setCellValue('A1',"ลำดับ");
	$sheet->setCellValue('B1',"ทีม");
	$sheet->setCellValue('C1',"ชื่อลูกค้า");
	$sheet->setCellValue('D1',"รหัสสินค้า");
	$sheet->setCellValue('E1',"ชื่อสินค้า");
	$sheet->setCellValue('F1',"สถานะ");
	$sheet->setCellValue('G1',"คลังสินค้า");
	$sheet->setCellValue('H1',"จำนวน");
	$sheet->setCellValue('I1',"มูลค่า");
	if($Chk == 1) {	
		$sheet->setCellValue('J1',"ต้นทุน");
		$sheet->setCellValue('K1',"วันที่เข้าล่าสุด");
		if($_SESSION['DeptCode'] == 'DP004' || $_SESSION['DeptCode'] == 'DP002') {
			$sheet->setCellValue('L1',"ซัพพลายเออร์");
		}else{
			$sheet->setCellValue('L1',"พนักงานขาย");
		}
	}else{
		if($_SESSION['DeptCode'] == 'DP004' || $_SESSION['DeptCode'] == 'DP002') {
			$sheet->setCellValue('J1',"ซัพพลายเออร์");
		}else{
			$sheet->setCellValue('J1',"พนักงานขาย");
		}
	}

	// Add Style Header
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	if($Chk == 1) {	
		$sheet->getStyle('A1:L1')->applyFromArray($PageHeader);
	}else{
		$sheet->getStyle('A1:L1')->applyFromArray($PageHeader);
	}
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(8);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(50);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(53);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(8);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(9);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	if($Chk == 1) {	
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(40);
	}else{
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(40);
	}
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);

	// Style Body
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$Row = 1; $No = 0;
	while($result = odbc_fetch_array($QRY)) {
		$Row++; $No++;
		// ลำดับ
		$sheet->setCellValue('A'.$Row,$No);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		// ทีม
		$sheet->setCellValue('B'.$Row,$result['U_Dim1']);
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

		// ชื่อลูกค้า
		$sheet->setCellValue('C'.$Row,$result['CardCode']." - ".conutf8($result['CardName']));
		
		// รหัสสินค้า
		$sheet->setCellValue('D'.$Row,$result['ItemCode']);
		$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);

		// ชื่อสินค้า
		$sheet->setCellValue('E'.$Row,conutf8($result['ItemName']));

		// สถานะ
		$sheet->setCellValue('F'.$Row,$result['U_ProductStatus']);
		$sheet->getStyle('F'.$Row)->applyFromArray($TextCenter);

		// คลังสินค้า
		$sheet->setCellValue('G'.$Row,conutf8($result['WhsCode']));
		$sheet->getStyle('G'.$Row)->applyFromArray($TextCenter);

		// จำนวน
		$sheet->setCellValue('H'.$Row,$result['OpenQty']);
		$sheet->getStyle('H'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// มูลค่า
		$sheet->setCellValue('I'.$Row,$result['LineTotal']);
		$sheet->getStyle('I'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");

		if($Chk == 1) {
			// ต้นทุน
			$sheet->setCellValue('J'.$Row,$result['LastPurPrc']);
			$sheet->getStyle('J'.$Row)->applyFromArray($TextRight);
			$spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");

			// สถานะ
			$sheet->setCellValue('K'.$Row,date("d/m/Y",strtotime($result['LastPurDat'])));
			$sheet->getStyle('K'.$Row)->applyFromArray($TextCenter);

			if($_SESSION['DeptCode'] == 'DP004' || $_SESSION['DeptCode'] == 'DP002') {
				// ซัพพลายเออร์
				$sheet->setCellValue('L'.$Row,$result['VCode']." - ".conutf8($result['VName']));
			}else{
				// พนักงานขาย
				$sheet->setCellValue('L'.$Row,conutf8($result['SlpName']));
			}
		}else{
			if($_SESSION['DeptCode'] == 'DP004' || $_SESSION['DeptCode'] == 'DP002') {
				// ซัพพลายเออร์
				$sheet->setCellValue('L'.$Row,$result['VCode']." - ".conutf8($result['VName']));
			}else{
				// พนักงานขาย
				$sheet->setCellValue('L'.$Row,conutf8($result['SlpName']));
			}
		}
	}
	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงาน Back Order - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/BackOrder/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'BackOrder', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

// $arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
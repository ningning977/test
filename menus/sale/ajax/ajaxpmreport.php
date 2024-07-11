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

if($_GET['a'] == 'GetCusCode') {
	$sqlQRY = MySQLSelectX("SELECT CardCode, CardName FROM OCRD WHERE CardCode != '' ORDER BY CardCode");
	$option = "";
	$Row = 0;
	while($result = mysqli_fetch_array($sqlQRY)) {
		++$Row;
		$arrCol['Row_'.$Row]['CardCode'] = $result['CardCode'];
		$arrCol['Row_'.$Row]['CardName'] = $result['CardName'];
	}
	$arrCol['Row'] = $Row;
}

if($_GET['a'] == 'CallData') {
	$year = $_POST['Year'];
	$sql = "SELECT (ISNULL(T2.BeginStr,'IV-') +CAST(T1.DocNum AS varchar)) AS DocNum,T1.DocDate,T0.ItemCode,T0.Dscription AS ItemName,T0.Quantity,(T1.DocTotal-T1.VatSum) AS DocTotal,T0.UnitMsr 
			FROM INV1 T0 
				LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry 
				LEFT JOIN NNM1 T2 ON T1.Series = T2.Series 
			WHERE T1.CardCode = '".$_POST['CardCode']."' AND T0.ItemCode LIKE '99-%' AND YEAR(T1.DocDate) >= '$year'
			UNION ALL
			SELECT (T2.BeginStr+CAST(T1.DocNum AS varchar)) AS DocNum,T1.DocDate,T0.ItemCode,T0.Dscription AS ItemName,T0.Quantity,(T1.DocTotal-T1.VatSum) AS DocTotal,T0.UnitMsr
			FROM DLN1 T0 
				LEFT JOIN ODLN T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN NNM1 T2 ON T1.Series = T2.Series 
			WHERE T1.CardCode = '".$_POST['CardCode']."' AND T0.ItemCode LIKE '99-%' AND YEAR(T1.DocDate) >= '$year'";
	if($year <= 2022) {
		$sqlQRY = conSAP8($sql);
	} else {
		$sqlQRY = SAPSelect($sql);
	}
	
	$Row = 0;
	$output = "";
	while($result = odbc_fetch_array($sqlQRY)) {
		++$Row;
		$arrCol['Row_'.$Row]['DocNum'] = $result['DocNum'];
		$arrCol['Row_'.$Row]['DocDate'] = date("d/m/Y",strtotime($result['DocDate']));
		$arrCol['Row_'.$Row]['ItemList'] = $result['ItemCode']."-".conutf8($result['ItemName']);
		if($result['Quantity'] == 0) {
			$arrCol['Row_'.$Row]['Quantity'] = "-";
		}else{
			$arrCol['Row_'.$Row]['Quantity'] = number_format($result['Quantity'],0);
		}
		$arrCol['Row_'.$Row]['UnitMsr'] = conutf8($result['UnitMsr']);
		if($result['DocTotal'] == 0) {
			$arrCol['Row_'.$Row]['DocTotal'] = "-";
		}else{
			$arrCol['Row_'.$Row]['DocTotal'] = number_format($result['DocTotal'],0);
		}
	}
	$arrCol['Row'] = $Row;
}

if($_GET['a'] == 'Export') {
	$sql = "SELECT (ISNULL(T2.BeginStr,'IV-') +CAST(T1.DocNum AS varchar)) AS DocNum,T1.DocDate,T0.ItemCode,T0.Dscription AS ItemName,T0.Quantity,(T1.DocTotal-T1.VatSum) AS DocTotal,T0.UnitMsr 
			FROM INV1 T0 
				LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry 
				LEFT JOIN NNM1 T2 ON T1.Series = T2.Series 
			WHERE T1.CardCode = '".$_POST['CardCode']."' AND T0.ItemCode LIKE '99-%' AND YEAR(T1.DocDate) >= '".$_POST['Year']."'
			UNION ALL
			SELECT (T2.BeginStr+CAST(T1.DocNum AS varchar)) AS DocNum,T1.DocDate,T0.ItemCode,T0.Dscription AS ItemName,T0.Quantity,(T1.DocTotal-T1.VatSum) AS DocTotal,T0.UnitMsr
			FROM DLN1 T0 
				LEFT JOIN ODLN T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN NNM1 T2 ON T1.Series = T2.Series 
			WHERE T1.CardCode = '".$_POST['CardCode']."' AND T0.ItemCode LIKE '99-%' AND YEAR(T1.DocDate) >= '".$_POST['Year']."'";
	$sqlQRY = SAPSelect($sql);
	$resultCus = MySQLSelect("SELECT CardCode, CardName FROM OCRD WHERE CardCode = '".$_POST['CardCode']."'");

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานสินค้าพรีเมี่ยม บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานสินค้าพรีเมี่ยม บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(30); // Value x 6 = pixel in excel
	$spreadsheet->setActiveSheetIndex(0);

	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->setCellValue('A1',"รายงานสินค้าพรีเมี่ยม - ".$resultCus['CardCode']." ".$resultCus['CardName']." - ปี ".$_POST['Year']."");
	$sheet->getStyle('A1')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->mergeCells('A1:G1');

	$sheet->setCellValue('A2',"No.");
    $sheet->setCellValue('B2',"เลขที่เอกสาร");
    $sheet->setCellValue('C2',"วันที่เอกสาร");
    $sheet->setCellValue('D2',"รายการสินค้า");
    $sheet->setCellValue('E2',"จำนวน");
    $sheet->setCellValue('F2',"หน่วย");
    $sheet->setCellValue('G2',"ยอดรวมท้ายบิล");
	$sheet->getStyle('A2:G2')->applyFromArray($PageHeader);

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(44);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);

	$Row = 2;
	$Num = 0;
	while($result = odbc_fetch_array($sqlQRY)) {
		++$Row;
		++$Num;
		$sheet->setCellValue('A'.$Row,$Num);
		$sheet->setCellValue('B'.$Row,$result['DocNum']);
		$sheet->setCellValue('C'.$Row,date("d/m/Y",strtotime($result['DocDate'])));
		$sheet->setCellValue('D'.$Row,$result['ItemCode']."-".conutf8($result['ItemName']));
		if($result['Quantity'] == 0) {
			$sheet->setCellValue('E'.$Row,"-");
		}else{
			$sheet->setCellValue('E'.$Row,$result['Quantity']);
			$spreadsheet->getActiveSheet()->getStyle('E'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
		}
		$sheet->setCellValue('F'.$Row,conutf8($result['UnitMsr']));
		if($result['DocTotal'] == 0) {
			$sheet->setCellValue('G'.$Row,"-");
		}else{
			$sheet->setCellValue('G'.$Row,$result['DocTotal']);
			$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
		}

		$sheet->getStyle('A'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
		$sheet->getStyle('B'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
		$sheet->getStyle('C'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
		$sheet->getStyle('E'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
		$sheet->getStyle('F'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
		$sheet->getStyle('G'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
	}

	$writer = new Xlsx($spreadsheet);
	if(isset($resultCus['CardCode'])) {
		$FileName = "รายงานสินค้าพรีเมี่ยม - ".$resultCus['CardCode']." ".$resultCus['CardName']." [".$_POST['Year']."] - ".date("YmdHis").".xlsx";
	}else{
		$FileName = "รายงานสินค้าพรีเมี่ยม - ".date("YmdHis").".xlsx";
	}
	$writer->save("../../../../FileExport/PremiumReport/".$FileName);

    $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'PremiumReport', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
	$arrCol['ExportStatus'] = "SUCCESS";
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
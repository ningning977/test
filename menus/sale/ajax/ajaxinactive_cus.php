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
	$Active = $_POST['Active'];
	$SQL = "
		SELECT
			B0.CardCode, B1.CardName, B2.U_Dim1, B2.SlpName, B0.PastDate
		FROM (
			SELECT A0.CardCode, SUM(A0.DocTotal) AS 'DocTotal',
				(
					SELECT SUM(P0.DocTotal-P0.VatSum) AS 'PastTotal' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' AND YEAR(P0.DocDate) = YEAR(GETDATE()) GROUP BY P0.CardCode
				) AS 'CurYSale',
				(
					SELECT SUM(P0.DocTotal-P0.VatSum) AS 'PastTotal' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' AND P0.DocDate >= DATEADD(MONTH, -$Active, GETDATE()) GROUP BY P0.CardCode
				) AS 'PastSale',
				(
					CASE WHEN 
					(SELECT TOP 1 P0.DocDate AS 'LastDate' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' ORDER BY P0.DocDate DESC) = '2022-12-31' OR 
					(SELECT TOP 1 P0.DocDate AS 'LastDate' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' ORDER BY P0.DocDate DESC) IS NULL
					THEN (SELECT TOP 1 P0.DocDate AS 'LastDate' FROM KBI_DB2022.dbo.OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' ORDER BY P0.DocDate DESC)
					ELSE (SELECT TOP 1 P0.DocDate AS 'LastDate' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' ORDER BY P0.DocDate DESC) END
				)AS 'PastDate'
			FROM (
			SELECT T0.CardCode, SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal'
			FROM OINV T0
			WHERE (T0.CardCode LIKE 'C%' OR T0.CardCode LIKE 'M%') AND T0.CANCELED = 'N'
			GROUP BY T0.CardCode
			UNION ALL
			SELECT T0.CardCode, -SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal'
			FROM ORIN T0
			WHERE (T0.CardCode LIKE 'C%' OR T0.CardCode LIKE 'M%') AND T0.CANCELED = 'N'
			GROUP BY T0.CardCode
		) A0
		GROUP BY A0.CardCode
		) B0
		LEFT JOIN OCRD B1 ON B0.CardCode = B1.CardCode
		LEFT JOIN OSLP B2 ON B1.SlpCode  = B2.SlpCode
		WHERE (B0.PastSale IS NULL) AND (B2.U_Dim1 IS NOT NULL AND B2.U_Dim1 != 'KBI')
		ORDER BY B2.U_Dim1, B2.SlpName, B0.CardCode";
	$QRY = SAPSelect($SQL);
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['CardCode'] = "<a href='?p=reportcus&CardCode=".$result['CardCode']."' target='_blank'>".$result['CardCode']."</a>";
		$arrCol[$r]['CardName'] = conutf8($result['CardName']);
		switch ($result['U_Dim1']) {
			case 'MT1': $TeamName = "โมเดิร์นเทรด 1"; break;
			case 'EXP': $TeamName = "โมเดิร์นเทรด 1 (ต่างประเทศ)"; break;
			case 'MT2': $TeamName = "โมเดิร์นเทรด 2"; break;
			case 'TT1': $TeamName = "เขตกรุงเทพฯ (TT1)"; break;
			case 'TT2': $TeamName = "ต่างจังหวัด (TT2)"; break;
			case 'OUL': $TeamName = "หน้าร้าน"; break;
			case 'ONL': $TeamName = "ออนไลน์"; break;
		}
		$arrCol[$r]['TeamName'] = $TeamName;
		$arrCol[$r]['SlpName']  = conutf8($result['SlpName']);
		$arrCol[$r]['PastDate'] = date("d/m/Y", strtotime($result['PastDate']));
		$r++;
	}
}

if($_GET['a'] == 'Export') {
	$Active = $_POST['Active'];
	$SQL = "
		SELECT
			B0.CardCode, B1.CardName, B2.U_Dim1, B2.SlpName, B0.PastDate
		FROM (
			SELECT A0.CardCode, SUM(A0.DocTotal) AS 'DocTotal',
				(
					SELECT SUM(P0.DocTotal-P0.VatSum) AS 'PastTotal' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' AND YEAR(P0.DocDate) = YEAR(GETDATE()) GROUP BY P0.CardCode
				) AS 'CurYSale',
				(
					SELECT SUM(P0.DocTotal-P0.VatSum) AS 'PastTotal' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' AND P0.DocDate >= DATEADD(MONTH, -$Active, GETDATE()) GROUP BY P0.CardCode
				) AS 'PastSale',
				(
					CASE WHEN 
					(SELECT TOP 1 P0.DocDate AS 'LastDate' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' ORDER BY P0.DocDate DESC) = '2022-12-31' OR 
					(SELECT TOP 1 P0.DocDate AS 'LastDate' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' ORDER BY P0.DocDate DESC) IS NULL
					THEN (SELECT TOP 1 P0.DocDate AS 'LastDate' FROM KBI_DB2022.dbo.OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' ORDER BY P0.DocDate DESC)
					ELSE (SELECT TOP 1 P0.DocDate AS 'LastDate' FROM OINV P0 WHERE A0.CardCode = P0.CardCode AND P0.CANCELED = 'N' ORDER BY P0.DocDate DESC) END
				)AS 'PastDate'
			FROM (
			SELECT T0.CardCode, SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal'
			FROM OINV T0
			WHERE (T0.CardCode LIKE 'C%' OR T0.CardCode LIKE 'M%') AND T0.CANCELED = 'N'
			GROUP BY T0.CardCode
			UNION ALL
			SELECT T0.CardCode, -SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal'
			FROM ORIN T0
			WHERE (T0.CardCode LIKE 'C%' OR T0.CardCode LIKE 'M%') AND T0.CANCELED = 'N'
			GROUP BY T0.CardCode
		) A0
		GROUP BY A0.CardCode
		) B0
		LEFT JOIN OCRD B1 ON B0.CardCode = B1.CardCode
		LEFT JOIN OSLP B2 ON B1.SlpCode  = B2.SlpCode
		WHERE (B0.PastSale IS NULL) AND (B2.U_Dim1 IS NOT NULL AND B2.U_Dim1 != 'KBI')
		ORDER BY B2.U_Dim1, B2.SlpName, B0.CardCode";
	$QRY = SAPSelect($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานลูกค้าไม่เคลื่อนไหว บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานลูกค้าไม่เคลื่อนไหว บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// Style
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$sheet->setCellValue('A1',"รหัสลูกค้า");
	$sheet->setCellValue('B1',"ชื่อลูกค้า");
	$sheet->setCellValue('C1',"ทีมขายล่าสุด");
	$sheet->setCellValue('D1',"พนักงานขายล่าสุด");
	$sheet->setCellValue('E1',"วันที่เปิดบิลล่าสุด");

	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);
	$sheet->getStyle('A1:E1')->applyFromArray($PageHeader);

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(50);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(40);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(17);

	$Row = 1;
	while($result = odbc_fetch_array($QRY)) {
		$Row++;
		// รหัสลูกค้า
		$sheet->setCellValue('A'.$Row,$result['CardCode']);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);
		// ชื่อลูกค้า
		$sheet->setCellValue('B'.$Row,conutf8($result['CardName']));
		// ทีมขายล่าสุด
		switch ($result['U_Dim1']) {
			case 'MT1': $TeamName = "โมเดิร์นเทรด 1"; break;
			case 'EXP': $TeamName = "โมเดิร์นเทรด 1 (ต่างประเทศ)"; break;
			case 'MT2': $TeamName = "โมเดิร์นเทรด 2"; break;
			case 'TT1': $TeamName = "เขตกรุงเทพฯ (TT1)"; break;
			case 'TT2': $TeamName = "ต่างจังหวัด (TT2)"; break;
			case 'OUL': $TeamName = "หน้าร้าน"; break;
			case 'ONL': $TeamName = "ออนไลน์"; break;
		}
		$sheet->setCellValue('C'.$Row,$TeamName);
		// พนักงานขายล่าสุด
		$sheet->setCellValue('D'.$Row,conutf8($result['SlpName']));
		// วันที่เปิดบิลล่าสุด
		$sheet->setCellValue('E'.$Row,date("d/m/Y", strtotime($result['PastDate'])));
		$sheet->getStyle('E'.$Row)->applyFromArray($TextCenter);
	}
	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานลูกค้าไม่เคลื่อนไหว - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/InActiveCus/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'InActiveCus', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
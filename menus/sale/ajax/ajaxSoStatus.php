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
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	switch($_SESSION['DeptCode']) {
		case "DP005": $SqlWhr = " AND (T2.U_Dim1 IN ('TT2') OR T7.U_Dim1 IN ('TT2'))"; break;
		case "DP006": $SqlWhr = " AND (T2.U_Dim1 IN ('MT1','EXP') OR T7.U_Dim1 IN ('MT1','EXP'))"; break;
		case "DP007": $SqlWhr = " AND (T2.U_Dim1 IN ('MT2') OR T7.U_Dim1 IN ('MT2'))"; break;
		case "DP008": $SqlWhr = " AND (T2.U_Dim1 IN ('TT1','OUL') OR T7.U_Dim1 IN ('TT1','OUL'))"; break;
		case "DP003":
			switch($_SESSION['LvCode']) {
				case "LV104":
				case "LV105":
				case "LV106":
					$SqlWhr = " AND (T2.U_Dim1 IN ('ONL') OR T7.U_Dim1 = IN ('ONL'))";
				break;
				default: $SqlWhr = NULL; break;
			}
		break;
		default: $SqlWhr = NULL; break;
	}

	$SQL = "SELECT
				A0.*
			FROM (
				SELECT DISTINCT T1.BeginStr AS 'SoPrefix', T0.DocEntry AS 'SoDocEntry',
					CASE WHEN T0.U_PONo = '' OR T0.U_PONo IS NULL THEN (ISNULL(T1.BeginStr,'SO-')+CAST(T0.DocNum AS VARCHAR)) ELSE T0.U_PONo END AS 'U_PoNo',
					T0.CardCode, T0.CardName, T2.SlpName,
					(ISNULL(T1.BeginStr,'SO-')+CAST(T0.DocNum AS VARCHAR)) AS 'SoDocNum', T0.DocDate AS 'SoDocDate', T0.DocDueDate AS 'SoDueDate', (T0.DocTotal - T0.VatSum) AS 'SoDocTotal',
					(ISNULL(T5.BeginStr,'IV-')+CAST(T4.DocNum AS VARCHAR)) AS 'IvDocNum', T4.DocDate AS 'IvDocDate', T4.DocDueDate AS 'IvDueDate', (T4.DocTotal - T4.VatSum) AS 'IvDocTotal'
				FROM ORDR T0
				LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
				LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
				LEFT JOIN RDR1 T3 ON T0.DocEntry = T3.DocEntry
				LEFT JOIN OINV T4 ON T3.TrgetEntry = T4.DocEntry AND T3.TargetType = 13
				LEFT JOIN NNM1 T5 ON T4.Series = T5.Series
				LEFT JOIN OCRD T6 ON T0.CardCode = T6.CardCode
				LEFT JOIN OSLP T7 ON T6.SlpCode = T7.SlpCode
				WHERE
					(YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) = $Month) AND (T3.TrgetEntry IS NOT NULL AND T3.TargetType != -1) AND (T1.BeginStr IN ('SO-','SN-'))
					$SqlWhr
				
				UNION ALL 
				
				SELECT DISTINCT T1.BeginStr AS 'SoPrefix', T0.DocEntry AS 'SoDocEntry',
					CASE WHEN T0.U_PONo = '' OR T0.U_PONo IS NULL THEN (ISNULL(T1.BeginStr,'SO-')+CAST(T0.DocNum AS VARCHAR)) ELSE T0.U_PONo END AS 'U_PoNo',
					T0.CardCode, T0.CardName, T2.SlpName,
					(ISNULL(T1.BeginStr,'SA-')+CAST(T0.DocNum AS VARCHAR)) AS 'SoDocNum', T0.DocDate AS 'SoDocDate', T0.DocDueDate AS 'SoDueDate', (T0.DocTotal - T0.VatSum) AS 'SoDocTotal',
					(ISNULL(T5.BeginStr,'PA-')+CAST(T4.DocNum AS VARCHAR)) AS 'IvDocNum', T4.DocDate AS 'IvDocDate', T4.DocDueDate AS 'IvDueDate', (T4.DocTotal - T4.VatSum) AS 'IvDocTotal'
				FROM ORDR T0
				LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
				LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
				LEFT JOIN RDR1 T3 ON T0.DocEntry = T3.DocEntry
				LEFT JOIN ODLN T4 ON T3.TrgetEntry = T4.DocEntry AND T3.TargetType = 15
				LEFT JOIN NNM1 T5 ON T4.Series = T5.Series
				LEFT JOIN OCRD T6 ON T0.CardCode = T6.CardCode
				LEFT JOIN OSLP T7 ON T6.SlpCode = T7.SlpCode
				WHERE
				(YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) = $Month) AND (T3.TrgetEntry IS NOT NULL AND T3.TargetType != -1) AND (T1.BeginStr IN ('SA-','SB-'))
				$SqlWhr
			) A0
			ORDER BY
				CASE
				WHEN A0.SoPrefix = 'SO-' THEN 1
				WHEN A0.SoPrefix = 'SN-' THEN 2
				WHEN A0.SoPrefix = 'SA-' THEN 3
				ELSE 4 END, A0.SoDocNum";
	if($Year >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['U_PoNo'] = conutf8($result['U_PoNo']);
		if($result['SoDocTotal'] != $result['IvDocTotal']) {
			$arrCol[$r]['CardName']  = $result['CardCode']." ".conutf8($result['CardName'])."*";
		}else{
			$arrCol[$r]['CardName']  = $result['CardCode']." ".conutf8($result['CardName']);
		}
		$arrCol[$r]['SlpName']   = conutf8($result['SlpName']);
		$arrCol[$r]['SoDocNum']  = $result['SoDocNum'];
		$arrCol[$r]['SoDocDate'] = date("d/m/Y",strtotime($result['SoDocDate']));
		$arrCol[$r]['SoDueDate'] = date("d/m/Y",strtotime($result['SoDueDate']));
		$arrCol[$r]['SoDocTotal']= number_format($result['SoDocTotal'],2);
		$arrCol[$r]['IvDocNum']  = $result['IvDocNum'];
		$arrCol[$r]['IvDocDate'] = date("d/m/Y",strtotime($result['IvDocDate']));
		$arrCol[$r]['IvDueDate'] = date("d/m/Y",strtotime($result['IvDueDate']));
		$arrCol[$r]['IvDocTotal']= number_format($result['IvDocTotal'],2);
		$arrCol[$r]['ViewData']  = "<a href='#' onclick='ViewData(".$result['SoDocEntry'].");'><i class='fas fa-search-plus'></i></a>";
		$r++;
	}
}

if($_GET['a'] == 'ViewData') {
	$SoDocEntry = $_POST['SoDocEntry'];
	$GetSQL =
		"SELECT
			T0.DocEntry, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'SoDocNum', T0.CardCode, T0.CardName,
			T3.SlpName, T0.DocDate, T0.DocDueDate, T0.Comments, T0.U_PONo,
			T1.ItemCode, CASE WHEN (T1.SubCatNum = '' OR T1.SubCatNum IS NULL) THEN T1.CodeBars ELSE T1.SubCatNum END AS 'CodeBars', T1.Dscription, T1.WhsCode, T1.UnitMsr, T1.Quantity AS 'SoQty',
			CASE
				WHEN T1.TargetType = 13 THEN (SELECT P0.Quantity FROM INV1 P0 WHERE P0.DocEntry = T1.TrgetEntry AND P0.BaseLine = T1.LineNum)
				WHEN T1.TargetType = 15 THEN (SELECT P0.Quantity FROM DLN1 P0 WHERE P0.DocEntry = T1.TrgetEntry AND P0.BaseLine = T1.LineNum)
				WHEN T1.TargetType = -1 THEN 0
			END AS 'IvQty'
		FROM ORDR T0
		LEFT JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
		LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
		WHERE T0.DocEntry = $SoDocEntry";
	$GetQRY = SAPSelect($GetSQL);
	$r = 0;
	while($GetRST  = odbc_fetch_array($GetQRY)) {
		$r++; 
		if($r == 1) {
			$arrCol['CardCode']   = conutf8($GetRST['CardCode']." | ".$GetRST['CardName']);
			$arrCol['SoDocNum']   = $GetRST['SoDocNum'];
			$arrCol['SlpName']    = conutf8($GetRST['SlpName']);
			$arrCol['DocDate']    = date("Y-m-d",strtotime($GetRST['DocDate']));
			$arrCol['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
			$arrCol['Comments']   = conutf8($GetRST['Comments']);
		}
		$arrCol[$r]['ItemCode']   = $GetRST['ItemCode'];
		$arrCol[$r]['CodeBars']   = $GetRST['CodeBars'];
		$arrCol[$r]['Dscription'] = conutf8($GetRST['Dscription']);
		$arrCol[$r]['WhsCode']    = conutf8($GetRST['WhsCode']);
		$arrCol[$r]['SoQty']      = number_format($GetRST['SoQty'],0);
		$arrCol[$r]['IvQty']      = number_format($GetRST['IvQty'],0);
		$arrCol[$r]['UnitMsr']    = conutf8($GetRST['UnitMsr']);
	}
	$arrCol['Row'] = $r;
}

if($_GET['a'] == 'Export') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];

	switch($_SESSION['DeptCode']) {
		case "DP005": $SqlWhr = " AND (T2.U_Dim1 IN ('TT2') OR T7.U_Dim1 IN ('TT2'))"; break;
		case "DP006": $SqlWhr = " AND (T2.U_Dim1 IN ('MT1','EXP') OR T7.U_Dim1 IN ('MT1','EXP'))"; break;
		case "DP007": $SqlWhr = " AND (T2.U_Dim1 IN ('MT2') OR T7.U_Dim1 IN ('MT2'))"; break;
		case "DP008": $SqlWhr = " AND (T2.U_Dim1 IN ('TT1','OUL') OR T7.U_Dim1 IN ('TT1','OUL'))"; break;
		case "DP003":
			switch($_SESSION['LvCode']) {
				case "LV104":
				case "LV105":
				case "LV106":
					$SqlWhr = " AND (T2.U_Dim1 IN ('ONL') OR T7.U_Dim1 = IN ('ONL'))";
				break;
				default: $SqlWhr = NULL; break;
			}
		break;
		default: $SqlWhr = NULL; break;
	}

	$SQL = "SELECT
				A0.*
			FROM (
				SELECT DISTINCT T1.BeginStr AS 'SoPrefix', T0.DocEntry AS 'SoDocEntry',
					CASE WHEN T0.U_PONo = '' OR T0.U_PONo IS NULL THEN (ISNULL(T1.BeginStr,'SO-')+CAST(T0.DocNum AS VARCHAR)) ELSE T0.U_PONo END AS 'U_PoNo',
					T0.CardCode, T0.CardName, T2.SlpName,
					(ISNULL(T1.BeginStr,'SO-')+CAST(T0.DocNum AS VARCHAR)) AS 'SoDocNum', T0.DocDate AS 'SoDocDate', T0.DocDueDate AS 'SoDueDate', (T0.DocTotal - T0.VatSum) AS 'SoDocTotal',
					(ISNULL(T5.BeginStr,'IV-')+CAST(T4.DocNum AS VARCHAR)) AS 'IvDocNum', T4.DocDate AS 'IvDocDate', T4.DocDueDate AS 'IvDueDate', (T4.DocTotal - T4.VatSum) AS 'IvDocTotal'
				FROM ORDR T0
				LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
				LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
				LEFT JOIN RDR1 T3 ON T0.DocEntry = T3.DocEntry
				LEFT JOIN OINV T4 ON T3.TrgetEntry = T4.DocEntry AND T3.TargetType = 13
				LEFT JOIN NNM1 T5 ON T4.Series = T5.Series
				LEFT JOIN OCRD T6 ON T0.CardCode = T6.CardCode
				LEFT JOIN OSLP T7 ON T6.SlpCode = T7.SlpCode
				WHERE
					(YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) = $Month) AND (T3.TrgetEntry IS NOT NULL AND T3.TargetType != -1) AND (T1.BeginStr IN ('SO-','SN-'))
					$SqlWhr
				
				UNION ALL 
				
				SELECT DISTINCT T1.BeginStr AS 'SoPrefix', T0.DocEntry AS 'SoDocEntry',
					CASE WHEN T0.U_PONo = '' OR T0.U_PONo IS NULL THEN (ISNULL(T1.BeginStr,'SO-')+CAST(T0.DocNum AS VARCHAR)) ELSE T0.U_PONo END AS 'U_PoNo',
					T0.CardCode, T0.CardName, T2.SlpName,
					(ISNULL(T1.BeginStr,'SA-')+CAST(T0.DocNum AS VARCHAR)) AS 'SoDocNum', T0.DocDate AS 'SoDocDate', T0.DocDueDate AS 'SoDueDate', (T0.DocTotal - T0.VatSum) AS 'SoDocTotal',
					(ISNULL(T5.BeginStr,'PA-')+CAST(T4.DocNum AS VARCHAR)) AS 'IvDocNum', T4.DocDate AS 'IvDocDate', T4.DocDueDate AS 'IvDueDate', (T4.DocTotal - T4.VatSum) AS 'IvDocTotal'
				FROM ORDR T0
				LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
				LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
				LEFT JOIN RDR1 T3 ON T0.DocEntry = T3.DocEntry
				LEFT JOIN ODLN T4 ON T3.TrgetEntry = T4.DocEntry AND T3.TargetType = 15
				LEFT JOIN NNM1 T5 ON T4.Series = T5.Series
				LEFT JOIN OCRD T6 ON T0.CardCode = T6.CardCode
				LEFT JOIN OSLP T7 ON T6.SlpCode = T7.SlpCode
				WHERE
				(YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) = $Month) AND (T3.TrgetEntry IS NOT NULL AND T3.TargetType != -1) AND (T1.BeginStr IN ('SA-','SB-'))
				$SqlWhr
			) A0
			ORDER BY
				CASE
				WHEN A0.SoPrefix = 'SO-' THEN 1
				WHEN A0.SoPrefix = 'SN-' THEN 2
				WHEN A0.SoPrefix = 'SA-' THEN 3
				ELSE 4 END, A0.SoDocNum";
	if($Year >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}

	// START EXCEL
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานการเปิด S/O บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานการเปิด S/O บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// SET HEADER
	$sheet->setCellValue('A1',"เลขที่ P/O");
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"ชื่อลูกค้า");
	$spreadsheet->getActiveSheet()->mergeCells('B1:B2');
	$sheet->setCellValue('C1',"พนักงานขาย");
	$spreadsheet->getActiveSheet()->mergeCells('C1:C2');
	$sheet->setCellValue('D1',"ใบสั่งขาย (S/O)");
	$spreadsheet->getActiveSheet()->mergeCells('D1:G1');
	$sheet->setCellValue('H1',"ใบกำกับภาษี (Invoice)");
	$spreadsheet->getActiveSheet()->mergeCells('H1:K1');

	$sheet->setCellValue('D2',"เลขที่เอกสาร");
	$sheet->setCellValue('E2',"วันที่เอกสาร");
	$sheet->setCellValue('F2',"วันที่กำหนดส่ง");
	$sheet->setCellValue('G2',"มูลค่า (บาท)");
	$sheet->setCellValue('H2',"เลขที่เอกสาร");
	$sheet->setCellValue('I2',"วันที่เปิดบิล");
	$sheet->setCellValue('J2',"วันที่กำหนดชำระ");
	$sheet->setCellValue('K2',"มูลค่า (บาท)");

	// ADD STYLE HEADER
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->getStyle('A1:K1')->applyFromArray($PageHeader);
	$sheet->getStyle('D2:K2')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(50);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(18);
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);
	$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(18);

	// ADD STYLE BODY
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold   = ['font' => [ 'bold' => true ]];

	$Row = 2;
	while($result = odbc_fetch_array($QRY)) {
		$Row++;

		$sheet->setCellValue('A'.$Row,conutf8($result['U_PoNo']));
		if($result['SoDocTotal'] != $result['IvDocTotal']) {
			$spreadsheet->getActiveSheet()->getStyle('A'.$Row)->getFont()->getColor()->setARGB('ff9a1118');
		}
		
		$sheet->setCellValue('B'.$Row,$result['CardCode']." ".conutf8($result['CardName']));

		$sheet->setCellValue('C'.$Row,conutf8($result['SlpName']));

		$sheet->setCellValue('D'.$Row,$result['SoDocNum']);
		$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('E'.$Row,date("d/m/Y",strtotime($result['SoDocDate'])));
		$sheet->getStyle('E'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('F'.$Row,date("d/m/Y",strtotime($result['SoDueDate'])));
		$sheet->getStyle('F'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('G'.$Row,$result['SoDocTotal']);
		$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);

		$sheet->setCellValue('H'.$Row,$result['IvDocNum']);
		$sheet->getStyle('H'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('I'.$Row,date("d/m/Y",strtotime($result['IvDocDate'])));
		$sheet->getStyle('I'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('J'.$Row,date("d/m/Y",strtotime($result['IvDueDate'])));
		$sheet->getStyle('J'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('K'.$Row,$result['IvDocTotal']);
		$spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		$sheet->getStyle('K'.$Row)->applyFromArray($TextRight);
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานการเปิด SO - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/SoStatus/".$FileName);
	// $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'SoStatus', logFile = '$FileName', DateCreate = NOW()";
	// MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if($_GET['a'] == 'GetWareHouse') {
	$SQL = "SELECT
				T1.Location, T0.WhsCode, T0.WhsName 
			FROM OWHS T0
			LEFT JOIN OLCT T1 ON T0.Location = T1.Code 
			WHERE T0.WhsCode NOT IN ('0','ACC','W','01')
			ORDER BY T0.Location, T0.WhsCode";
	$QRY = SAPSelect($SQL);
	$output = ""; $Location = "";
	while($result = odbc_fetch_array($QRY)) {
		if($Location != $result['Location']) {
			if($Location != "") {
				$output .= "</optgroup>";
			}
			$Location = $result['Location'];
			$output .= "<optgroup label='".conutf8($result['Location'])."'>";
			$output .= "<option value='".conutf8($result['WhsCode'])."'>".conutf8($result['WhsCode'])." - ".conutf8($result['WhsName'])."</option>";
		}else{
			$output .= "<option value='".conutf8($result['WhsCode'])."'>".conutf8($result['WhsCode'])." - ".conutf8($result['WhsName'])."</option>";
		}
	}
	$arrCol['output'] = $output;
}

if($_GET['a'] == 'CallData') {
	$SelectYear = $_POST['SelectYear'];
	$WareHouse  = $_POST['WareHouse'];
	$SQL = "
		SELECT A0.ItemCode,A1.ItemName,A0.OnHand,A2.ReturnType,A2.Prefix,A2.DocNum,A2.U_RefNoCust,A2.Quantity,A2.Cost AS CostTotal,A2.Sapaw,A2.U_Investigate_Sales,
			A2.U_Investigate_QC,A2.ReturnDetail,A2.U_Result_Return, A2.DocDate AS 'DocDate'															
		FROM OITW A0
			INNER JOIN OITM A1 ON A0.ItemCode = A1.ItemCode
			LEFT JOIN (
				SELECT  T0.[DocDate] AS 'DocDate',T0.[DocDate] AS 'วันที่เอกสาร QC',
					T3.[Name] AS 'ReturnType',T1.[BeginStr] AS 'Prefix',T0.[DocNum], T0.[U_RefNoCust],
					T0.[NumAtCard] AS 'เลขที่ใบลดหนี้', T7.[DocDate] AS 'วันที่ลดหนี้', T0.[U_RefInv] AS 'เลขที่บิล',
					T5.[SlpName] AS 'ผู้แทนขาย',
					T0.[CardCode] AS 'รหัสลูกค้า',T0.[CardName] AS 'ชื่อลูกค้า',T2.[ItemCode],T2.[Dscription] AS 'ชื่อสินค้า',T9.CardCode AS 'รหัสซัพพลายเออร์' ,T9.CardName AS 'ชื่อซัพพลายเออร์',T2.[Quantity],T2.[UnitMsr] AS 'หน่วย',
					(T2.[GrossBuyPr]*1.07) AS 'ต้นทุน (VAT)',((T2.[GrossBuyPr]*1.07)*T2.[Quantity]) AS Cost,
					T2.[WhsCode] AS 'คลังสินค้า',T2.[Price] AS 'ราคาขาย (NO VAT)', (T2.[Price]*T2.[Quantity]) AS 'ราคาขายรวม (NO VAT)',
					T4.[Name] AS 'Sapaw',T2.[U_Investigate_Sales],T2.[U_Investigate_QC],T6.[Name] AS 'ReturnDetail',T0.[U_Result_Return]
				FROM ORDN T0
				LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
				LEFT JOIN RDN1 T2 ON T0.[DocEntry] = T2.[DocEntry]
				LEFT JOIN [@RETURN_TYPE] T3 ON T0.[U_Return_type] = T3.[Code]
				LEFT JOIN [@GRADE_ITEM] T4 ON T2.[U_Grade_Item] = T4.[Code]
				LEFT JOIN OSLP T5 ON T0.[SlpCode] = T5.[SlpCode]
				LEFT JOIN [@CNREASON] T6 ON T0.[U_CNReason2] = T6.[Code]
				LEFT JOIN ORIN T7 ON T0.[U_RefNoCust] = T7.[U_RefNoCust]
				LEFT JOIN OITM T8 ON T2.ItemCode = T8.ItemCode
				LEFT JOIN OCRD T9 ON T8.CardCode = T9.CardCode
				WHERE (YEAR(T0.[DocDate]) >= '$SelectYear') AND
					(T1.[SeriesName] LIKE '%ST%' OR T1.[SeriesName] LIKE '%RN%' OR T1.[SeriesName] LIKE '%PN%' OR T1.[SeriesName] LIKE '%PE%' OR T1.[SeriesName] LIKE '%IN%' OR T1.[SeriesName] LIKE '%IM%' OR T1.[SeriesName] LIKE '%SI%') AND 
					(T0.[CANCELED] = 'N')    AND  T2.WhsCode IN ('$WareHouse') 
				UNION ALL
				SELECT T0.[DocDate] AS 'DocDate', T0.[DocDate] AS 'วันที่เอกสาร QC',
					T3.[Name] AS ReturnType, T1.[BeginStr] AS 'Prefix', T0.[DocNum], T0.[U_RefNoCust],
					T0.[NumAtCard] AS 'เลขที่ใบลดหนี้', NULL AS 'วันที่ลดหนี้', T0.[U_RefInv] AS 'เลขที่บิล',
					T5.[SlpName] AS 'ผู้แทนขาย',
					T0.[CardCode] AS 'รหัสลูกค้า',T0.[CardName] AS 'ชื่อลูกค้า',T2.[ItemCode],T2.[Dscription] AS 'ชื่อสินค้า',T9.CardCode AS 'รหัสซัพพลายเออร์' ,T9.CardName AS 'ชื่อซัพพลายเออร์',T2.[Quantity],T2.[UnitMsr] AS 'หน่วย',
					(T2.[GrossBuyPr]*1.07) AS 'ต้นทุน (VAT)',((T2.[GrossBuyPr]*1.07)*T2.[Quantity]) AS Cost,
					T2.[WhsCode] AS 'คลังสินค้า',T2.[Price] AS 'ราคาขาย (NO VAT)', (T2.[Price]*T2.[Quantity]) AS 'ราคาขายรวม (NO VAT)',
					T4.[Name] AS 'Sapaw',T2.[U_Investigate_Sales],T2.[U_Investigate_QC],T6.[Name] AS 'ReturnDetail',T0.[U_Result_Return]
				FROM ORIN T0
				LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
				LEFT JOIN RIN1 T2 ON T0.[DocEntry] = T2.[DocEntry]
				LEFT JOIN [@RETURN_TYPE] T3 ON T0.[U_Return_type] = T3.[Code]
				LEFT JOIN [@GRADE_ITEM] T4 ON T2.[U_Grade_Item] = T4.[Code]
				LEFT JOIN OSLP T5 ON T0.[SlpCode] = T5.[SlpCode]
				LEFT JOIN [@CNREASON] T6 ON T0.[U_CNReason2] = T6.[Code]
				LEFT JOIN OITM T8 ON T2.ItemCode = T8.ItemCode
				LEFT JOIN OCRD T9 ON T8.CardCode = T9.CardCode
				WHERE (YEAR(T0.[DocDate]) >= '$SelectYear') AND (T1.[SeriesName] LIKE '%SR%' AND T0.[U_RefNoCust] LIKE 'RE%') AND 
					(T0.[CANCELED] = 'N') AND  T2.WhsCode IN ('$WareHouse')
			) A2 ON A0.ItemCode = A2.ItemCode
		WHERE A0.WhsCode IN ('$WareHouse') AND A0.OnHand > 0
		ORDER BY A0.ItemCode";
	if($SelectYear >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['ItemCode']         = $result['ItemCode'];
		$arrCol[$r]['ItemName']         = conutf8($result['ItemName']);
		$arrCol[$r]['OnHand']           = number_format($result['OnHand'],0);
		$arrCol[$r]['ReturnType']       = conutf8($result['ReturnType']);
		if($result['DocDate'] == NULL) {
			$arrCol[$r]['DocDate']      = "";
		} else {
			$arrCol[$r]['DocDate']      = date("d/m/Y", strtotime($result['DocDate']));
		}
		
		$arrCol[$r]['DocNum']           = $result['Prefix'].$result['DocNum'];
		$arrCol[$r]['RefNoCust']        = conutf8($result['U_RefNoCust']);
		if($result['Quantity'] != 0) {
			$arrCol[$r]['Quantity']     = number_format($result['Quantity'],0);
		}else{
			$arrCol[$r]['Quantity']     = "-";
		}
		if($result['CostTotal'] != 0) {
			$arrCol[$r]['CostTotal']    = number_format($result['CostTotal'],2);
		}else{
			$arrCol[$r]['CostTotal']    = "-";
		}
		
		$arrCol[$r]['Sapaw']            = conutf8($result['Sapaw']);
		$arrCol[$r]['InvestigateSales'] = conutf8($result['U_Investigate_Sales']);
		$arrCol[$r]['InvestigateQC']    = conutf8($result['U_Investigate_QC']);
		$arrCol[$r]['ReturnDetail']     = conutf8($result['ReturnDetail']);
		$arrCol[$r]['ResultReturn']     = conutf8($result['U_Result_Return']);
		$r++;
	}
	if($r == 0) {
		$arrCol[0]['ItemCode'] = "ไม่มีข้อมูล :(";
		$arrCol[0]['ItemName'] = "";
		$arrCol[0]['OnHand'] = "";
		$arrCol[0]['ReturnType'] = "";
		$arrCol[0]['DocDate'] = "";
		$arrCol[0]['DocNum'] = "";
		$arrCol[0]['RefNoCust'] = "";
		$arrCol[0]['Quantity'] = "";
		$arrCol[0]['CostTotal'] = "";
		$arrCol[0]['Sapaw'] = "";
		$arrCol[0]['InvestigateSales'] = "";
		$arrCol[0]['InvestigateQC'] = "";
		$arrCol[0]['ReturnDetail'] = "";
		$arrCol[0]['ResultReturn'] = "";
	}
	$arrCol[0]['Row'] = $r;
}

if($_GET['a'] == 'Export') {
	$SelectYear = $_POST['SelectYear'];
	$WareHouse  = $_POST['WareHouse'];
	$SQL = "
		SELECT A0.ItemCode,A1.ItemName,A0.OnHand,A2.ReturnType,A2.Prefix,A2.DocNum,A2.U_RefNoCust,A2.Quantity,A2.Cost AS CostTotal,A2.Sapaw,A2.U_Investigate_Sales,
			A2.U_Investigate_QC,A2.ReturnDetail,A2.U_Result_Return, A2.DocDate																
		FROM OITW A0
			INNER JOIN OITM A1 ON A0.ItemCode = A1.ItemCode
			LEFT JOIN (
				SELECT  T0.[DocDate] AS 'DocDate',T0.[DocDate] AS 'วันที่เอกสาร QC',
					T3.[Name] AS 'ReturnType',T1.[BeginStr] AS 'Prefix',T0.[DocNum], T0.[U_RefNoCust],
					T0.[NumAtCard] AS 'เลขที่ใบลดหนี้', T7.[DocDate] AS 'วันที่ลดหนี้', T0.[U_RefInv] AS 'เลขที่บิล',
					T5.[SlpName] AS 'ผู้แทนขาย',
					T0.[CardCode] AS 'รหัสลูกค้า',T0.[CardName] AS 'ชื่อลูกค้า',T2.[ItemCode],T2.[Dscription] AS 'ชื่อสินค้า',T9.CardCode AS 'รหัสซัพพลายเออร์' ,T9.CardName AS 'ชื่อซัพพลายเออร์',T2.[Quantity],T2.[UnitMsr] AS 'หน่วย',
					(T2.[GrossBuyPr]*1.07) AS 'ต้นทุน (VAT)',((T2.[GrossBuyPr]*1.07)*T2.[Quantity]) AS Cost,
					T2.[WhsCode] AS 'คลังสินค้า',T2.[Price] AS 'ราคาขาย (NO VAT)', (T2.[Price]*T2.[Quantity]) AS 'ราคาขายรวม (NO VAT)',
					T4.[Name] AS 'Sapaw',T2.[U_Investigate_Sales],T2.[U_Investigate_QC],T6.[Name] AS 'ReturnDetail',T0.[U_Result_Return]
				FROM ORDN T0
				LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
				LEFT JOIN RDN1 T2 ON T0.[DocEntry] = T2.[DocEntry]
				LEFT JOIN [@RETURN_TYPE] T3 ON T0.[U_Return_type] = T3.[Code]
				LEFT JOIN [@GRADE_ITEM] T4 ON T2.[U_Grade_Item] = T4.[Code]
				LEFT JOIN OSLP T5 ON T0.[SlpCode] = T5.[SlpCode]
				LEFT JOIN [@CNREASON] T6 ON T0.[U_CNReason2] = T6.[Code]
				LEFT JOIN ORIN T7 ON T0.[U_RefNoCust] = T7.[U_RefNoCust]
				LEFT JOIN OITM T8 ON T2.ItemCode = T8.ItemCode
				LEFT JOIN OCRD T9 ON T8.CardCode = T9.CardCode
				WHERE (YEAR(T0.[DocDate]) >= '$SelectYear') AND
					(T1.[SeriesName] LIKE '%ST%' OR T1.[SeriesName] LIKE '%RN%' OR T1.[SeriesName] LIKE '%PN%' OR T1.[SeriesName] LIKE '%PE%' OR T1.[SeriesName] LIKE '%IN%' OR T1.[SeriesName] LIKE '%IM%' OR T1.[SeriesName] LIKE '%SI%') AND 
					(T0.[CANCELED] = 'N')    AND  T2.WhsCode IN ('$WareHouse') 
				UNION ALL
				SELECT T0.[DocDate] AS 'DocDate', T0.[DocDate] AS 'วันที่เอกสาร QC',
					T3.[Name] AS ReturnType, T1.[BeginStr] AS 'Prefix', T0.[DocNum], T0.[U_RefNoCust],
					T0.[NumAtCard] AS 'เลขที่ใบลดหนี้', NULL AS 'วันที่ลดหนี้', T0.[U_RefInv] AS 'เลขที่บิล',
					T5.[SlpName] AS 'ผู้แทนขาย',
					T0.[CardCode] AS 'รหัสลูกค้า',T0.[CardName] AS 'ชื่อลูกค้า',T2.[ItemCode],T2.[Dscription] AS 'ชื่อสินค้า',T9.CardCode AS 'รหัสซัพพลายเออร์' ,T9.CardName AS 'ชื่อซัพพลายเออร์',T2.[Quantity],T2.[UnitMsr] AS 'หน่วย',
					(T2.[GrossBuyPr]*1.07) AS 'ต้นทุน (VAT)',((T2.[GrossBuyPr]*1.07)*T2.[Quantity]) AS Cost,
					T2.[WhsCode] AS 'คลังสินค้า',T2.[Price] AS 'ราคาขาย (NO VAT)', (T2.[Price]*T2.[Quantity]) AS 'ราคาขายรวม (NO VAT)',
					T4.[Name] AS 'Sapaw',T2.[U_Investigate_Sales],T2.[U_Investigate_QC],T6.[Name] AS 'ReturnDetail',T0.[U_Result_Return]
				FROM ORIN T0
				LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
				LEFT JOIN RIN1 T2 ON T0.[DocEntry] = T2.[DocEntry]
				LEFT JOIN [@RETURN_TYPE] T3 ON T0.[U_Return_type] = T3.[Code]
				LEFT JOIN [@GRADE_ITEM] T4 ON T2.[U_Grade_Item] = T4.[Code]
				LEFT JOIN OSLP T5 ON T0.[SlpCode] = T5.[SlpCode]
				LEFT JOIN [@CNREASON] T6 ON T0.[U_CNReason2] = T6.[Code]
				LEFT JOIN OITM T8 ON T2.ItemCode = T8.ItemCode
				LEFT JOIN OCRD T9 ON T8.CardCode = T9.CardCode
				WHERE (YEAR(T0.[DocDate]) >= '$SelectYear') AND (T1.[SeriesName] LIKE '%SR%' AND T0.[U_RefNoCust] LIKE 'RE%') AND 
					(T0.[CANCELED] = 'N') AND  T2.WhsCode IN ('$WareHouse')
			) A2 ON A0.ItemCode = A2.ItemCode
		WHERE A0.WhsCode IN ('$WareHouse') AND A0.OnHand > 0
		ORDER BY A0.ItemCode";
	if($SelectYear >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานการรับคืน บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานการรับคืน บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// Header
	$sheet->setCellValue('A1',"รหัสสินค้า");
	$sheet->setCellValue('B1',"ชื่อสินค้า");
	$sheet->setCellValue('C1',"จำนวนคงเหลือ");
	$sheet->setCellValue('D1',"ประเภทการคืน");
	$sheet->setCellValue('E1',"วันที่เอกสาร");
	$sheet->setCellValue('F1',"เลขที่เอกสาร");
	$sheet->setCellValue('G1',"เลขที่เอกสาร QC");
	$sheet->setCellValue('H1',"จำนวนคืน");
	$sheet->setCellValue('I1',"ต้นทุนรวม");
	$sheet->setCellValue('J1',"สภาพสินค้า");
	$sheet->setCellValue('K1',"รายละเอียดเซลส์");
	$sheet->setCellValue('L1',"รายละเอียด QC");
	$sheet->setCellValue('M1',"รายละเอียดการคืน");
	$sheet->setCellValue('N1',"ผลตรวจสอบ QC");

	// Add Style Header
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->getStyle('A1:N1')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(14.5);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(37);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14.5);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(17);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(19);
	$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(22);
	$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(22);
	$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(22);
	$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(60);
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);

	// Style Body
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$Row = 1;
	while($result = odbc_fetch_array($QRY)) {
		$Row++;
		// รหัสสินค้า
		$sheet->setCellValue('A'.$Row,$result['ItemCode']);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		// ชื่อสินค้า
		$sheet->setCellValue('B'.$Row,conutf8($result['ItemName']));

		// จำนวนคงเหลือ
		$sheet->setCellValue('C'.$Row,$result['OnHand']);
		$sheet->getStyle('C'.$Row)->applyFromArray($TextRight);

		// ประเภทการคืน
		$sheet->setCellValue('D'.$Row,conutf8($result['ReturnType']));

		// วันที่เอกสาร
		if($result['DocDate'] == NULL) {
			$sheet->setCellValue('E'.$Row,"");
		} else {
			$sheet->setCellValue('E'.$Row,date("d/m/Y", strtotime($result['DocDate'])));
		}
		$sheet->getStyle('E'.$Row)->applyFromArray($TextCenter);

		// เลขที่เอกสาร
		$sheet->setCellValue('F'.$Row,$result['Prefix'].$result['DocNum']);
		$sheet->getStyle('F'.$Row)->applyFromArray($TextCenter);

		// เลขที่เอกสาร QC
		$sheet->setCellValue('G'.$Row,conutf8($result['U_RefNoCust']));
		$sheet->getStyle('G'.$Row)->applyFromArray($TextCenter);

		// จำนวนคืน
		if($result['Quantity'] != 0) {
			$sheet->setCellValue('H'.$Row,$result['Quantity']);
			$spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('H'.$Row,"-");
		}
		$sheet->getStyle('H'.$Row)->applyFromArray($TextRight);

		// ต้นทุกรวม
		if($result['CostTotal'] != 0) {
			$sheet->setCellValue('I'.$Row,$result['CostTotal']);
			$spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		}else{
			$sheet->setCellValue('I'.$Row,"-");
		}
		$sheet->getStyle('I'.$Row)->applyFromArray($TextRight);

		// สภาพสินค้า
		$sheet->setCellValue('J'.$Row,conutf8($result['Sapaw']));

		// รายละเอียดเซลส์
		$sheet->setCellValue('K'.$Row,conutf8($result['U_Investigate_Sales']));

		// รายละเอียด QC
		$sheet->setCellValue('L'.$Row,conutf8($result['U_Investigate_QC']));

		// รายละเอียดการคืน
		$sheet->setCellValue('M'.$Row,conutf8($result['ReturnDetail']));

		// ผลตรวจสอบ QC
		$sheet->setCellValue('N'.$Row,conutf8($result['U_Result_Return']));
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานการรับคืน - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/ReportReturn/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'ReportReturn', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['Row'] = $Row;
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
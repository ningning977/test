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

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

if($_GET['p'] == "SearchData") {
	$filt_y   = $_POST['filt_year'];
	$filt_m1  = $_POST['filt_month1'];
	$filt_m2  = $_POST['filt_month2'];
	$SortType = explode("::",$_POST['SortType']);

	$OrderSQL = "";
	switch($SortType[0]) {
		case "ITEM": $OrderSQL .= " A0.ItemCode"; break;
		default:     $OrderSQL .= " SUM(A0.Qty_".$SortType[0].")"; break;
	}

	$OrderSQL = $OrderSQL." ".$SortType[1];
	if($filt_y >= 2023) {
		$tbpf = NULL;
	} else {
		$tbpf = "KBI_DB2022.dbo.";
	}

	$SQL1 =
		"SELECT
			A0.ItemCode, A1.ItemName, A1.U_ProductStatus, A1.SalUnitMsr,
			SUM(A0.Qty_MT1 + A0.Qty_MT2 + A0.Qty_TT1 + A0.Qty_TT2 + A0.Qty_OUL + A0.Qty_ONL + A0.Qty_EXP + A0.Qty_KBI) AS 'Qty_All',
			SUM(A0.Qty_MT1) AS 'Qty_MT1',
			SUM(A0.Qty_MT2) AS 'Qty_MT2',
			SUM(A0.Qty_TT1) AS 'Qty_TT1',
			SUM(A0.Qty_TT2) AS 'Qty_TT2',
			SUM(A0.Qty_OUL) AS 'Qty_OUL',
			SUM(A0.Qty_ONL) AS 'Qty_ONL',
			SUM(A0.Qty_EXP) AS 'Qty_EXP',
			SUM(A0.Qty_KBI) AS 'Qty_KBI'
		FROM (
			SELECT
				T1.ItemCode,
				CASE WHEN T2.U_Dim1 IN ('MT1') THEN T1.Quantity ELSE 0 END AS 'Qty_MT1',
				CASE WHEN T2.U_Dim1 IN ('MT2') THEN T1.Quantity ELSE 0 END AS 'Qty_MT2',
				CASE WHEN T2.U_Dim1 IN ('TT1') THEN T1.Quantity ELSE 0 END AS 'Qty_TT1',
				CASE WHEN T2.U_Dim1 IN ('TT2') THEN T1.Quantity ELSE 0 END AS 'Qty_TT2',
				CASE WHEN T2.U_Dim1 IN ('OUL') THEN T1.Quantity ELSE 0 END AS 'Qty_OUL',
				CASE WHEN T2.U_Dim1 IN ('ONL') THEN T1.Quantity ELSE 0 END AS 'Qty_ONL',
				CASE WHEN T2.U_Dim1 IN ('EXP') THEN T1.Quantity ELSE 0 END AS 'Qty_EXP',
				CASE WHEN T2.U_Dim1 NOT IN ('MT1','MT2','TT1','TT2','OUL','ONL','EXP') THEN T1.Quantity ELSE 0 END AS 'Qty_KBI'
			FROM ".$tbpf."OINV T0
			LEFT JOIN ".$tbpf."INV1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN ".$tbpf."OSLP T2 ON T0.SlpCode  = T2.SlpCode
			WHERE ((YEAR(T0.DocDate) BETWEEN $filt_y AND $filt_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%'
			UNION ALL
			SELECT
				T1.ItemCode,
				CASE WHEN T2.U_Dim1 IN ('MT1') THEN -T1.Quantity ELSE 0 END AS 'Qty_MT1',
				CASE WHEN T2.U_Dim1 IN ('MT2') THEN -T1.Quantity ELSE 0 END AS 'Qty_MT2',
				CASE WHEN T2.U_Dim1 IN ('TT1') THEN -T1.Quantity ELSE 0 END AS 'Qty_TT1',
				CASE WHEN T2.U_Dim1 IN ('TT2') THEN -T1.Quantity ELSE 0 END AS 'Qty_TT2',
				CASE WHEN T2.U_Dim1 IN ('OUL') THEN -T1.Quantity ELSE 0 END AS 'Qty_OUL',
				CASE WHEN T2.U_Dim1 IN ('ONL') THEN -T1.Quantity ELSE 0 END AS 'Qty_ONL',
				CASE WHEN T2.U_Dim1 IN ('EXP') THEN -T1.Quantity ELSE 0 END AS 'Qty_EXP',
				CASE WHEN T2.U_Dim1 NOT IN ('MT1','MT2','TT1','TT2','OUL','ONL','EXP') THEN -T1.Quantity ELSE 0 END AS 'Qty_KBI'
			FROM ".$tbpf."ORIN T0
			LEFT JOIN ".$tbpf."RIN1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN ".$tbpf."OSLP T2 ON T0.SlpCode  = T2.SlpCode
			WHERE ((YEAR(T0.DocDate) BETWEEN $filt_y AND $filt_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%'
		) A0
		LEFT JOIN OITM A1 ON A0.ItemCode = A1.ItemCode
		GROUP BY A0.ItemCode, A1.ItemName, A1.U_ProductStatus, A1.SalUnitMsr
		ORDER BY $OrderSQL";
	$i = 0;
	if(ChkRowSAP($SQL1) > 0) {
		$arrCol['Rows'] = ChkRowSAP($SQL1);
		$QRY1 = SAPSelect($SQL1);
		while($RST1 = odbc_fetch_array($QRY1)) {
			$arrCol[$i]['ItemCode']        = $RST1['ItemCode'];
			$arrCol[$i]['ItemName']        = conutf8($RST1['ItemName']);
			$arrCol[$i]['U_ProductStatus'] = conutf8($RST1['U_ProductStatus']);
			$arrCol[$i]['SalUnitMsr']      = conutf8($RST1['SalUnitMsr']);
			if($RST1['Qty_MT1'] == 0) { $arrCol[$i]['Qty_MT1'] = "-"; } else { $arrCol[$i]['Qty_MT1'] = number_format($RST1['Qty_MT1'],0); }
			if($RST1['Qty_MT2'] == 0) { $arrCol[$i]['Qty_MT2'] = "-"; } else { $arrCol[$i]['Qty_MT2'] = number_format($RST1['Qty_MT2'],0); }
			if($RST1['Qty_TT1'] == 0) { $arrCol[$i]['Qty_TT1'] = "-"; } else { $arrCol[$i]['Qty_TT1'] = number_format($RST1['Qty_TT1'],0); }
			if($RST1['Qty_TT2'] == 0) { $arrCol[$i]['Qty_TT2'] = "-"; } else { $arrCol[$i]['Qty_TT2'] = number_format($RST1['Qty_TT2'],0); }
			if($RST1['Qty_OUL'] == 0) { $arrCol[$i]['Qty_OUL'] = "-"; } else { $arrCol[$i]['Qty_OUL'] = number_format($RST1['Qty_OUL'],0); }
			if($RST1['Qty_ONL'] == 0) { $arrCol[$i]['Qty_ONL'] = "-"; } else { $arrCol[$i]['Qty_ONL'] = number_format($RST1['Qty_ONL'],0); }
			if($RST1['Qty_EXP'] == 0) { $arrCol[$i]['Qty_EXP'] = "-"; } else { $arrCol[$i]['Qty_EXP'] = number_format($RST1['Qty_EXP'],0); }
			if($RST1['Qty_KBI'] == 0) { $arrCol[$i]['Qty_KBI'] = "-"; } else { $arrCol[$i]['Qty_KBI'] = number_format($RST1['Qty_KBI'],0); }
			if($RST1['Qty_All'] == 0) { $arrCol[$i]['Qty_All'] = "-"; } else { $arrCol[$i]['Qty_All'] = number_format($RST1['Qty_All'],0); }

			$i++;
		}
	} else {
		$arrCol['Rows'] = 0;
	}
}

if($_GET['p'] == "ExportData") {
	$filt_y   = $_POST['filt_year'];
	$filt_m1  = $_POST['filt_month1'];
	$filt_m2  = $_POST['filt_month2'];
	$SortType = explode("::",$_POST['SortType']);

	$OrderSQL = "";
	switch($SortType[0]) {
		case "ITEM": $OrderSQL .= " A0.ItemCode"; break;
		default:     $OrderSQL .= " SUM(A0.Qty_".$SortType[0].")"; break;
	}

	$OrderSQL = $OrderSQL." ".$SortType[1];
	if($filt_y >= 2023) {
		$tbpf = NULL;
	} else {
		$tbpf = "KBI_DB2022.dbo.";
	}

	$SQL1 =
		"SELECT
			A0.ItemCode, A1.ItemName, A1.U_ProductStatus, A1.SalUnitMsr,
			SUM(A0.Qty_MT1 + A0.Qty_MT2 + A0.Qty_TT1 + A0.Qty_TT2 + A0.Qty_OUL + A0.Qty_ONL + A0.Qty_EXP + A0.Qty_KBI) AS 'Qty_All',
			SUM(A0.Qty_MT1) AS 'Qty_MT1',
			SUM(A0.Qty_MT2) AS 'Qty_MT2',
			SUM(A0.Qty_TT1) AS 'Qty_TT1',
			SUM(A0.Qty_TT2) AS 'Qty_TT2',
			SUM(A0.Qty_OUL) AS 'Qty_OUL',
			SUM(A0.Qty_ONL) AS 'Qty_ONL',
			SUM(A0.Qty_EXP) AS 'Qty_EXP',
			SUM(A0.Qty_KBI) AS 'Qty_KBI'
		FROM (
			SELECT
				T1.ItemCode,
				CASE WHEN T2.U_Dim1 IN ('MT1') THEN T1.Quantity ELSE 0 END AS 'Qty_MT1',
				CASE WHEN T2.U_Dim1 IN ('MT2') THEN T1.Quantity ELSE 0 END AS 'Qty_MT2',
				CASE WHEN T2.U_Dim1 IN ('TT1') THEN T1.Quantity ELSE 0 END AS 'Qty_TT1',
				CASE WHEN T2.U_Dim1 IN ('TT2') THEN T1.Quantity ELSE 0 END AS 'Qty_TT2',
				CASE WHEN T2.U_Dim1 IN ('OUL') THEN T1.Quantity ELSE 0 END AS 'Qty_OUL',
				CASE WHEN T2.U_Dim1 IN ('ONL') THEN T1.Quantity ELSE 0 END AS 'Qty_ONL',
				CASE WHEN T2.U_Dim1 IN ('EXP') THEN T1.Quantity ELSE 0 END AS 'Qty_EXP',
				CASE WHEN T2.U_Dim1 NOT IN ('MT1','MT2','TT1','TT2','OUL','ONL','EXP') THEN T1.Quantity ELSE 0 END AS 'Qty_KBI'
			FROM ".$tbpf."OINV T0
			LEFT JOIN ".$tbpf."INV1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN ".$tbpf."OSLP T2 ON T0.SlpCode  = T2.SlpCode
			WHERE ((YEAR(T0.DocDate) BETWEEN $filt_y AND $filt_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%'
			UNION ALL
			SELECT
				T1.ItemCode,
				CASE WHEN T2.U_Dim1 IN ('MT1') THEN -T1.Quantity ELSE 0 END AS 'Qty_MT1',
				CASE WHEN T2.U_Dim1 IN ('MT2') THEN -T1.Quantity ELSE 0 END AS 'Qty_MT2',
				CASE WHEN T2.U_Dim1 IN ('TT1') THEN -T1.Quantity ELSE 0 END AS 'Qty_TT1',
				CASE WHEN T2.U_Dim1 IN ('TT2') THEN -T1.Quantity ELSE 0 END AS 'Qty_TT2',
				CASE WHEN T2.U_Dim1 IN ('OUL') THEN -T1.Quantity ELSE 0 END AS 'Qty_OUL',
				CASE WHEN T2.U_Dim1 IN ('ONL') THEN -T1.Quantity ELSE 0 END AS 'Qty_ONL',
				CASE WHEN T2.U_Dim1 IN ('EXP') THEN -T1.Quantity ELSE 0 END AS 'Qty_EXP',
				CASE WHEN T2.U_Dim1 NOT IN ('MT1','MT2','TT1','TT2','OUL','ONL','EXP') THEN -T1.Quantity ELSE 0 END AS 'Qty_KBI'
			FROM ".$tbpf."ORIN T0
			LEFT JOIN ".$tbpf."RIN1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN ".$tbpf."OSLP T2 ON T0.SlpCode  = T2.SlpCode
			WHERE ((YEAR(T0.DocDate) BETWEEN $filt_y AND $filt_y) AND (MONTH(T0.DocDate) BETWEEN $filt_m1 AND $filt_m2)) AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL AND T1.ItemCode NOT LIKE '00-000-%'
		) A0
		LEFT JOIN OITM A1 ON A0.ItemCode = A1.ItemCode
		GROUP BY A0.ItemCode, A1.ItemName, A1.U_ProductStatus, A1.SalUnitMsr
		ORDER BY $OrderSQL";

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานยอดขายสินค้ารายทีม บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานยอดขายสินค้ารายทีม บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	$sheet->setCellValue('A1',"ลำดับ");
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"รหัสสินค้า");
	$spreadsheet->getActiveSheet()->mergeCells('B1:B2');
	$sheet->setCellValue('C1',"ชื่อสินค้า");
	$spreadsheet->getActiveSheet()->mergeCells('C1:C2');
	$sheet->setCellValue('D1',"สถานะสินค้า");
	$spreadsheet->getActiveSheet()->mergeCells('D1:D2');
	$sheet->setCellValue('E1',"หน่วยงาน");
	$spreadsheet->getActiveSheet()->mergeCells('E1:E2');
	$sheet->setCellValue('F1',"ยอดขาย (หน่วย)");
	$spreadsheet->getActiveSheet()->mergeCells('F1:M1');
	$sheet->setCellValue('F2',"MT1");
	$sheet->setCellValue('G2',"MT2");
	$sheet->setCellValue('H2',"TT กทม.");
	$sheet->setCellValue('I2',"TT ตจว.");
	$sheet->setCellValue('J2',"หน้าร้าน");
	$sheet->setCellValue('K2',"ออนไลน์");
	$sheet->setCellValue('L2',"ต่างประเทศ");
	$sheet->setCellValue('M2',"ส่วนกลาง");
	$sheet->setCellValue('N1',"รวมทั้งหมด (หน่วย)");
	$spreadsheet->getActiveSheet()->mergeCells('N1:N2');

	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$sheet->getStyle('A1:N1')->applyFromArray($PageHeader);
	$sheet->getStyle('A2:N2')->applyFromArray($PageHeader);

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(50);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(17);

	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(14);
	$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(18);

	if(ChkRowSAP($SQL1) > 0) {
		$arrCol['Rows'] = ChkRowSAP($SQL1);
		$QRY1 = SAPSelect($SQL1);
		$Row = 2; $No = 0;
		while($RST1 = odbc_fetch_array($QRY1)) {
			$Row++; $No++;
			// ลำดับ
			$sheet->setCellValue('A'.$Row,$No);
			$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

			// รหัสสินค้า
			$sheet->setCellValue('B'.$Row,$RST1['ItemCode']);
			$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

			// ชื่อสินค้า
			$sheet->setCellValue('C'.$Row,conutf8($RST1['ItemName']));

			// สถานะ
			$sheet->setCellValue('D'.$Row,$RST1['U_ProductStatus']);
			$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);

			// หน่วย
			$sheet->setCellValue('E'.$Row,conutf8($RST1['SalUnitMsr']));
			$sheet->getStyle('E'.$Row)->applyFromArray($TextCenter);

			if($RST1['Qty_MT1'] == 0) { $sheet->setCellValue('F'.$Row,"-"); } else { $sheet->setCellValue('F'.$Row,$RST1['Qty_MT1']); $spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('F'.$Row)->applyFromArray($TextRight);

			if($RST1['Qty_MT2'] == 0) { $sheet->setCellValue('G'.$Row,"-"); } else { $sheet->setCellValue('G'.$Row,$RST1['Qty_MT2']); $spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);

			if($RST1['Qty_TT1'] == 0) { $sheet->setCellValue('H'.$Row,"-"); } else { $sheet->setCellValue('H'.$Row,$RST1['Qty_TT1']); $spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('H'.$Row)->applyFromArray($TextRight);

			if($RST1['Qty_TT2'] == 0) { $sheet->setCellValue('I'.$Row,"-"); } else { $sheet->setCellValue('I'.$Row,$RST1['Qty_TT2']); $spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('I'.$Row)->applyFromArray($TextRight);

			if($RST1['Qty_OUL'] == 0) { $sheet->setCellValue('J'.$Row,"-"); } else { $sheet->setCellValue('J'.$Row,$RST1['Qty_OUL']); $spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('J'.$Row)->applyFromArray($TextRight);

			if($RST1['Qty_ONL'] == 0) { $sheet->setCellValue('K'.$Row,"-"); } else { $sheet->setCellValue('K'.$Row,$RST1['Qty_ONL']); $spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('K'.$Row)->applyFromArray($TextRight);

			if($RST1['Qty_EXP'] == 0) { $sheet->setCellValue('L'.$Row,"-"); } else { $sheet->setCellValue('L'.$Row,$RST1['Qty_EXP']); $spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('L'.$Row)->applyFromArray($TextRight);

			if($RST1['Qty_KBI'] == 0) { $sheet->setCellValue('M'.$Row,"-"); } else { $sheet->setCellValue('M'.$Row,$RST1['Qty_KBI']); $spreadsheet->setActiveSheetIndex(0)->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('M'.$Row)->applyFromArray($TextRight);

			if($RST1['Qty_All'] == 0) { $sheet->setCellValue('N'.$Row,"-"); } else { $sheet->setCellValue('N'.$Row,$RST1['Qty_All']); $spreadsheet->setActiveSheetIndex(0)->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0"); }
			$sheet->getStyle('N'.$Row)->applyFromArray($TextRight);
			$sheet->getStyle('N'.$Row)->applyFromArray($TextBold);
		}
	}else{
		$arrCol['Rows'] = 0;
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานยอดขายสินค้ารายทีม - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/ItemByTeam/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'ItemByTeam', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
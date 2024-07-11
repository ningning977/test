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

if($_GET['a'] == 'Search') {
	if($_POST['aging'] == "true") {
		$SQLPurDat = "ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = B0.ItemCode ORDER BY P0.DocEntry DESC),B1.LastPurDat)";
		$SQLAging  = "DATEDIFF(m, ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = B0.ItemCode ORDER BY P0.DocEntry DESC),B1.LastPurDat),GETDATE())";
	}else{ 
		$SQLPurDat = "''"; 
		$SQLAging  = "'0'"; 
	}

	$SQL = "
		SELECT DISTINCT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', 
			B0.ItemCode, B1.CodeBars, B1.ItemName, ISNULL(B1.U_ProductStatus,'K') AS 'Status', B1.InvntryUom, SUM(B0.OnHand) AS 'OnHand',
			(B1.LastPurPrc*1.07) AS 'LastPurPrc', 
			$SQLPurDat AS 'LastPurDat', 
			$SQLAging AS 'Aging'
		FROM (
			SELECT
				T0.ItemCode, T1.WhsCode, T1.OnHand
			FROM OITM T0
			LEFT JOIN OITW T1 ON T0.ItemCode = T1.ItemCode
			LEFT JOIN OWHS T2 ON T1.WhsCode = T2.WhsCode
			WHERE (T0.InvntItem != 'N')
		) B0 
		LEFT JOIN OITM B1 ON B0.ItemCode = B1.ItemCode 
		GROUP BY B0.ItemCode, B1.CodeBars, B1.ItemName, B1.U_ProductStatus, B1.LastPurPrc, B1.LastPurDat, B1.InvntryUom 
		ORDER BY B0.ItemCode";
	$ListQRY = PITASelect($SQL);

	$Thead ="
		<tr class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>
			<th width='10%' class='text-center'>รหัสสินค้า</th>
			<th width='10%' class='text-center'>บาร์โค้ด</th>
			<th class='text-center'>ชื่อสินค้า</th> 
			<th width='5%' class='text-center'>สถานะ</th> 
			<th width='5%' class='text-center'>หน่วย</th>
			<th width='7%' class='text-center'>มูลค่า (บาท)</th>
			<th width='6%' class='text-center'>จำนวน (หน่วย)</th>
			<th width='7%' class='text-center' style='background-color: #f5f5f5;'>AGING (เดือน)</th>
			<th width='10%' class='text-center'>มูลค่ารวม (บาท)</th>
		</tr>";
	$ALLSUM = 0;
	$Tbody = "";
	while($ListRST = odbc_fetch_array($ListQRY)) { 
		if($ListRST['Aging'] >= 25) {
			$color = "text-danger table-danger";
		}elseif($ListRST['Aging'] >= 7 && $ListRST['Aging'] <= 24) {
			$color = "text-warning table-warning";
		}else {
			$color = "text-success table-success";
		}
		$Tbody .= "
			<tr class='fw-bold'>
				<td class='text-center'><a href='javascript:void(0);' class='Data-Item' data-item='".$ListRST['ItemCode']."'><i class='fas fa-search-plus'></i></a> ".$ListRST['ItemCode']."</td>
				<td class='text-center'>".$ListRST['CodeBars']."</td>
				<td>".conutf8($ListRST['ItemName'])."</td>
				<td class='text-center'>".$ListRST['Status']."</td>
				<td class='text-center'>".conutf8($ListRST['InvntryUom'])."</td>
				<td class='text-right'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ListRST['LastPurPrc'],2))." ฿</td>
				<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['OnHand'],0))."</td>
				<td class='text-center ".$color."' >".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['Aging'],0))."</td>
				<td class='text-right fw-bolder'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ListRST['OnHand']*$ListRST['LastPurPrc'],2))." ฿</td>
			</tr>";
		$ALLSUM = ($ALLSUM + ($ListRST['OnHand']*$ListRST['LastPurPrc']));
	}
	$Tfoot = "
		<tr class='fw-bold'>
			<td colspan='7' class='text-right fw-bolder text-primary'>มูลค่ารวมทั้งหมด (บาท)</td>
			<td colspan='2' class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ALLSUM,2))." ฿</td>
		</tr>";
	$arrCol['Thead'] = $Thead;
	$arrCol['Tbody'] = $Tbody;
	$arrCol['Tfoot'] = $Tfoot;
}

if($_GET['a'] == 'DataDetail'){
	$arrCol['ItemCode'] = $_POST['ItemCode'];
	$ItemSQL = "
		SELECT TOP 1  T0.[ItemCode], T0.[CodeBars], T0.[ItemName], T0.[InvntryUom], 
			(T0.LastPurPrc*1.07) AS 'LastPurPrc', 
			ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC),T0.LastPurDat) AS 'LastPurDat', 
			DATEDIFF(m,ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC),T0.LastPurDat),GETDATE()) AS 'Aging'
		FROM OITM T0
		WHERE T0.[ItemCode] = '".$_POST['ItemCode']."'";
	$ItemQRY = PITASelect($ItemSQL);
	$ItemRST = odbc_fetch_array($ItemQRY);
	$output1 = "
		<tr>
			<th width='15%'>รหัสสินค้า</th>
			<td width='35%'>".$ItemRST['ItemCode']."</td>
			<th width='15%'>บาร์โค้ด</th>
			<td width='35%'>".$ItemRST['CodeBars']."</td>
		</tr>
		<tr>
			<th>ชื่อสินค้า</th>
			<td colspan='3'>".conutf8($ItemRST['ItemName'])."</td>
		</tr>
		<tr>
			<th>ยี่ห้อ</th>
			<td></td>
			<th>กลุ่มสินค้า</th>
			<td></td>
		</tr>
		<tr>
			<th>หน่วย</th>
			<td>".conutf8($ItemRST['InvntryUom'])."</td>
			<th>สถานะ</th>
			<td></td>
		</tr>
		<tr>
			<th>ต้นทุนล่าสุด</th>
			<td>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ItemRST['LastPurPrc'],2))." ฿</td>
			<th>วันที่เข้าล่าสุด (Aging)</th>
			<td>".date("d/m/Y", strtotime($ItemRST['LastPurDat']))." (".number_format($ItemRST['Aging'],0)." เดือน)</td>
		</tr>";
	/* OUT PUT 1 => ข้อมูลสินค้า */
	$arrCol['output1'] = $output1;

	/* จำนวนสินค้าคงคลัง SAP */
	$WhseSQL = "
		SELECT T0.[ItemCode], T0.[WhsCode], T1.[WhsName],'PATA' AS 'WhsGroup', T0.[OnHand], T0.[OnOrder]
		FROM OITW T0
		LEFT JOIN OWHS T1 ON T0.[WhsCode] = T1.[WhsCode]
		WHERE T0.[ItemCode] = '".$_POST['ItemCode']."' AND (T0.[OnHand] !=0 OR T0.[OnOrder] != 0)
		ORDER BY 'WhsGroup', T0.[WhsCode]";
	$WhseQRY = PITASelect($WhseSQL);
	$output2 = "
		<table class='table table-sm table-bordered rounded rounded-3 overflow-hidden'>
			<thead style='font-size: 13px;'>
				<tr class='text-center'>
					<th rowspan='2'>ชื่อคลัง</th>
					<th colspan='5'>จำนวน (หน่วย)</th>
					<th width='15%' rowspan='2'>มูลค่ารวม</th>
				</tr>
				<tr class='text-center'>
					<th width='12.5%'>คงคลัง</th>
					<th width='12.5%'>รอเบิก</th>
					<th width='12.5%'>เบิกแล้ว</th>
					<th width='12.5%'>คงเหลือ</th>
					<th width='12.5%'>กำลังสั่งซื้อ</th>
				</tr>
			</thead>
			<tbody style='font-size: 12px;'>";
				$tempGroup = "";
				while($WhseRST = odbc_fetch_array($WhseQRY)) {
					if($tempGroup != $WhseRST['WhsGroup']) {
						$tempGroup = $WhseRST['WhsGroup'];
						$output2 .= "
							<tr>
								<td colspan='7' class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'>คลัง $tempGroup</td>
							</tr>
							<tr>
								<td>".conutf8($WhseRST['WhsName'])."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnHand'],0))."</td>
								<td class='text-right'>0</td>
								<td class='text-right'>0</td>
								<td class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnHand'],0))."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnOrder'],0))."</td>
								<td class='text-right fw-bolder'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format(($WhseRST['OnHand']*$ItemRST['LastPurPrc']),2))." ฿</td>
							</tr>";
					}
				}
			$output2 .= "</tbody>
		</table>";
	/* OUT PUT 2 => จำนวนสินค้าคงคลัง SAP */
	$arrCol['output2'] = $output2;
}

if($_GET['a'] == 'Export') {
	if($_POST['aging'] == "true") {
		$SQLPurDat = "ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = B0.ItemCode ORDER BY P0.DocEntry DESC),B1.LastPurDat)";
		$SQLAging  = "DATEDIFF(m, ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = B0.ItemCode ORDER BY P0.DocEntry DESC),B1.LastPurDat),GETDATE())";
	}else{ 
		$SQLPurDat = "''"; 
		$SQLAging  = "'0'"; 
	}

	$SQL = "
		SELECT DISTINCT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', 
			B0.ItemCode, B1.CodeBars, B1.ItemName, ISNULL(B1.U_ProductStatus,'K') AS 'Status', B1.InvntryUom, SUM(B0.OnHand) AS 'OnHand',
			(B1.LastPurPrc*1.07) AS 'LastPurPrc', 
			$SQLPurDat AS 'LastPurDat', 
			$SQLAging AS 'Aging'
		FROM (
			SELECT
				T0.ItemCode, T1.WhsCode, T1.OnHand
			FROM OITM T0
			LEFT JOIN OITW T1 ON T0.ItemCode = T1.ItemCode
			LEFT JOIN OWHS T2 ON T1.WhsCode = T2.WhsCode
			WHERE (T0.InvntItem != 'N')
		) B0 
		LEFT JOIN OITM B1 ON B0.ItemCode = B1.ItemCode 
		GROUP BY B0.ItemCode, B1.CodeBars, B1.ItemName, B1.U_ProductStatus, B1.LastPurPrc, B1.LastPurDat, B1.InvntryUom 
		ORDER BY B0.ItemCode";
	$ListQRY = PITASelect($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานสินค้าคงคลัง (PITA) บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานสินค้าคงคลัง (PITA) บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(12); // Value x 6 = pixel in excel
	$spreadsheet->setActiveSheetIndex(0);

	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$ColorSAP = [
		'font' => [ 'bold' => true, 'size' => 9 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ],
		'fill' => [ 'fillType' => \PHPOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'startColor' => ['argb' => 'ffd9edf7' ],
				  ]
	];
	$ColorDF = [
		'font' => [ 'bold' => true, 'size' => 9 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];

	// HEADER //
	$sheet->setCellValue('A1',"รหัสสินค้า");
	$sheet->setCellValue('B1',"บาร์โค้ด");
	$sheet->setCellValue('C1',"ชื่อสินค้า");
	$sheet->setCellValue('D1',"สถานะ");
	$sheet->setCellValue('E1',"หน่วย");
	$sheet->setCellValue('F1',"มูลค่า (บาท)");
	$sheet->setCellValue('G1',"จำนวน (หน่วย)");
	$sheet->setCellValue('H1',"AGING (เดือน)");
	$sheet->setCellValue('I1',"มูลค่ารวม (บาท)");
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13.5);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(50);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->getStyle('A1:I1')->applyFromArray($PageHeader);

	// Style
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	// BODY //
	$Row = 1;
	while($ListRST = odbc_fetch_array($ListQRY)) {
		$Row++;
		$sheet->setCellValue('A'.$Row,$ListRST['ItemCode']); 
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('B'.$Row,$ListRST['CodeBars']); 
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('C'.$Row,conutf8($ListRST['ItemName'])); 

		$sheet->setCellValue('D'.$Row,$ListRST['Status']); 
		$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);
		
		$sheet->setCellValue('E'.$Row,conutf8($ListRST['InvntryUom'])); 
		$sheet->getStyle('E'.$Row)->applyFromArray($TextCenter);

		if($ListRST['LastPurPrc'] != 0 || $ListRST['LastPurPrc'] != '') {
			$sheet->setCellValue('F'.$Row,$ListRST['LastPurPrc']); 
			$spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		}else{
			$sheet->setCellValue('F'.$Row,'-'); 
		}
		$sheet->getStyle('F'.$Row)->applyFromArray($TextRight);

		if($ListRST['OnHand'] != 0 || $ListRST['OnHand'] != '') {
			$sheet->setCellValue('G'.$Row,$ListRST['OnHand']); 
			$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('G'.$Row,'-'); 
		}
		$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);

		if($ListRST['Aging'] != 0 || $ListRST['Aging'] != '') {
			$sheet->setCellValue('H'.$Row,$ListRST['Aging']); 
			$spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}else{
			$sheet->setCellValue('H'.$Row,'-'); 
		}
		$sheet->getStyle('H'.$Row)->applyFromArray($TextCenter);

		if(($ListRST['OnHand']*$ListRST['LastPurPrc']) != 0 || $ListRST['OnHand'] != '' || $ListRST['LastPurPrc'] != '') {
			$sheet->setCellValue('I'.$Row,$ListRST['OnHand']); 
			$spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		}else{
			$sheet->setCellValue('I'.$Row,'-'); 
		}
		$sheet->getStyle('I'.$Row)->applyFromArray($TextRight);
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานสินค้าคงคลัง (PITA) -".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/InStockPTA/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'InStockPTA', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
	$arrCol['ExportStatus'] = "SUCCESS";
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
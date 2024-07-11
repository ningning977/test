<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";

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

if($_GET['a'] == 'GetCardCode') {
	$SQL = "SELECT T0.CardCode, CardName FROM OCRD T0 WHERE T0.CardCode LIKE 'V%'";
	$QRY = SAPSelect($SQL);
	$Data = "";
	while($result = odbc_fetch_array($QRY)) {
		$Data .= "<option value='".$result['CardCode']."'>".$result['CardCode']." | ".conutf8($result['CardName'])."</option>";
	}
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'CallData') {
	$Year = $_POST['Year'];
	if($_POST['Month'] == 'ALL') {
		$Month = "";
	}else{
		$Month = "AND MONTH(T0.DocDate) = ".$_POST['Month']."";
	}
	if($_POST['CardCode'] == 'ALL') {
		$CardCode  = "";
	}else{
		$CardCode = "AND T0.CardCode = '".$_POST['CardCode']."'";
	}
	if($_POST['DocStatus'] == 'ALL') { 
		$DocStatus = "AND T0.DocStatus IN ('O','C')"; 
	}else{ 
		$DocStatus = "AND T0.DocStatus IN ('".$_POST['DocStatus']."')";
	}
	if($_POST['GroupCode'] == 'ALL') { 
		$GroupCode = ""; 
	}else{ 
		if($Year >= 2023) {
			if($_POST['GroupCode'] == 'OUT') {
				$GroupCode = "AND T2.GroupCode = '126'"; 
			}else{
				$GroupCode = "AND T2.GroupCode != '126'"; 
			}
		}else{
			if($_POST['GroupCode'] == 'OUT') {
				$GroupCode = "AND T2.GroupCode = '108'"; 
			}else{
				$GroupCode = "AND T2.GroupCode != '108'"; 
			}
		}
	}
	$SQL = "
		SELECT T0.DocEntry, T0.CardCode, T0.CardName, (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS DocNum, T0.U_PurchaseFor, T0.DocDate, T0.DocDueDate, T0.Comments, T0.DocType, T0.DocTotal
		FROM OPOR T0  
		LEFT JOIN NNM1 T1 ON T0.Series = T1.Series 
		LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode 
		WHERE T0.CANCELED = 'N' AND YEAR(T0.DocDate) = $Year $CardCode $Month $DocStatus $GroupCode";
	if($Year >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['DocNum']        = "<a href='javascript:void(0);' onclick='GetDetail(".$result['DocEntry'].",$Year);'>".$result['DocNum']."</a>";
		$arrCol[$r]['CardCode']      = $result['CardCode'];
		$arrCol[$r]['CardName']      = conutf8($result['CardName']);
		$arrCol[$r]['U_PurchaseFor'] = $result['U_PurchaseFor'];
		$arrCol[$r]['DocType']       = $result['DocType'];
		$arrCol[$r]['DocDate']       = date("d/m/Y",strtotime($result['DocDate']));
		$arrCol[$r]['DocDueDate']    = date("d/m/Y",strtotime($result['DocDueDate']));
		$arrCol[$r]['DocTotal']    = number_format($result['DocTotal'],2);
		$arrCol[$r]['Comments']      = conutf8($result['Comments']);
		$r++;
	}
}

if($_GET['a'] == 'GetDetail') {
	$DocEntry = $_POST['DocEntry'];
	$Year = $_POST['Year'];
	$SQL1 = "
		SELECT TOP 1 T1.CardCode, T1.CardName, (T2.BeginStr+CAST(T1.DocNum AS VARCHAR)) AS DocNum, T1.DocDate, T1.DocDueDate, T1.U_PurchaseFor, T1.Address, T3.PymntGroup  
		FROM POR1 T0
		LEFT JOIN OPOR T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T1.Series = T2.Series 
		LEFT JOIN OCTG T3 ON T1.GroupNum = T3.GroupNum 
		WHERE T1.DocEntry = $DocEntry";
	if($Year >= 2023) {
		$QRY1 = SAPSelect($SQL1);
	}else{
		$QRY1 = conSAP8($SQL1);
	}
	$resultH = odbc_fetch_array($QRY1);
	$Head = 
		"<tr>
			<th>ผู้จำหน่าย</th>
			<td>".$resultH['CardCode']."</td>
			<th>เลขที่ใบสั่งซื้อ</th>
			<td>".$resultH['DocNum']."</td>
		</tr>
		<tr>
			<th>ชื่อร้าน</th>
			<td>".conutf8($resultH['CardName'])."</td>
			<th>เลขที่ PR</th>
			<td>".$resultH['U_PurchaseFor']."</td>
		</tr>
		<tr>
			<th>ที่อยู่</th>
			<td>".conutf8($resultH['Address'])."</td>
			<th>วันที่สั่งซื้อ</th>
			<td>".date("d/m/Y",strtotime($resultH['DocDate']))."</td>
		</tr>
		<tr>
			<th>เครดิต</th>
			<td>".conutf8($resultH['PymntGroup'])."</td>
			<th>วันที่กำหนดส่ง</th>
			<td>".date("d/m/Y",strtotime($resultH['DocDueDate']))."</td>
		</tr>";
	$arrCol['Head'] = $Head;

	$SQL = "
		SELECT T0.ItemCode, T0.Dscription AS ItemName, T0.Quantity, T0.unitMsr, (T0.LineTotal/T0.Quantity) AS Price, T0.LineTotal, T1.VatSum, T1.DocTotal, T0.DiscPrcnt, T1.DiscSum, T1.Comments
		FROM POR1 T0
		LEFT JOIN OPOR T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T1.Series = T2.Series 
		WHERE T1.DocEntry = $DocEntry";
	if($Year >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	$Body = ""; $No = 1; $Tfoot = "";
	while($result = odbc_fetch_array($QRY)) {
		$Body .= "
			<tr>
				<td class='text-center'>".$No."</td>
				<td class='text-center'>".$result['ItemCode']."</td>
				<td>".conutf8($result['ItemName'])."</td>
				<td class='text-right'>".number_format($result['Quantity'],0)."</td>
				<td>".conutf8($result['unitMsr'])."</td>
				<td class='text-right'>".number_format($result['Price'],2)."</td>";
				if($result['DiscPrcnt'] == 0) {
					$Body .= "<td></td>";
				}else{
					$Body .= "<td class='text-right'>".number_format($result['DiscPrcnt'],2)."</td>";
				}
		$Body.="<td class='text-right'>".number_format($result['LineTotal'],2)."</td>
			</tr>";
		
		if($No == 1) {
			$Tfoot .= "
				<tr>
					<td rowspan='4' colspan='2' class='align-top'>หมายเหตุ</td>
					<td rowspan='4' colspan='3' class='align-top'>".conutf8($result['Comments'])."</td>
					<td colspan='2'>รวมเป็นเงิน</td>
					<td class='text-right'>".number_format(($result['DocTotal'] - $result['VatSum']) + $result['DiscSum'],2)."</td>
				</tr>
				<tr>
					<td colspan='2'>หักส่วนลด</td>
					<td class='text-right'>".number_format($result['DiscSum'],2)."</td>
				</tr>
				<tr>
					<td colspan='2'>จำนวนเงินหลังหักส่วนลด</td>
					<td class='text-right'>".number_format((($result['DocTotal'] - $result['VatSum']) - $result['DiscSum']),2)."</td>
				</tr>
				<tr>
					<td colspan='2'>จำนวนภาษีมูลค่าเพิ่ม 7.00 %</td>
					<td class='text-right'>".number_format($result['VatSum'],2)."</td>
				</tr>
				<tr>
					<td colspan='2'>ตัวอักษร</td>
					<td colspan='3'><i>".numText(number_format($result['DocTotal'],2))."</i></td>
					<td colspan='2' class='fw-bolder'>จำนวนเงินรวมทั้งหมด</td>
					<td class='fw-bolder text-right'>".number_format($result['DocTotal'],2)."</td>
				</tr>";
		}

		$No++;
	}
	
	$arrCol['Body'] = $Body;
	$arrCol['Tfoot'] = $Tfoot;
}

if($_GET['a'] == 'Excel') {
	$Year = $_POST['Year'];
	if($_POST['Month'] == 'ALL') {
		$Month = "";
	}else{
		$Month = "AND MONTH(T0.DocDate) = ".$_POST['Month']."";
	}
	if($_POST['CardCode'] == 'ALL') {
		$CardCode  = "";
	}else{
		$CardCode = "AND T0.CardCode = '".$_POST['CardCode']."'";
	}
	if($_POST['DocStatus'] == 'ALL') { 
		$DocStatus = "AND T0.DocStatus IN ('O','C')"; 
	}else{ 
		$DocStatus = "AND T0.DocStatus IN ('".$_POST['DocStatus']."')";
	}
	if($_POST['GroupCode'] == 'ALL') { 
		$GroupCode = ""; 
	}else{ 
		if($Year >= 2023) {
			if($_POST['GroupCode'] == 'OUT') {
				$GroupCode = "AND T2.GroupCode = '126'"; 
			}else{
				$GroupCode = "AND T2.GroupCode != '126'"; 
			}
		}else{
			if($_POST['GroupCode'] == 'OUT') {
				$GroupCode = "AND T2.GroupCode = '108'"; 
			}else{
				$GroupCode = "AND T2.GroupCode != '108'"; 
			}
		}
	}
	$SQL = "
		SELECT T0.DocEntry, T0.CardCode, T0.CardName, (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS DocNum, T0.U_PurchaseFor, T0.DocDate, T0.DocDueDate, T0.Comments, T0.DocType, T0.DocTotal
		FROM OPOR T0  
		LEFT JOIN NNM1 T1 ON T0.Series = T1.Series 
		LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode 
		WHERE T0.CANCELED = 'N' AND YEAR(T0.DocDate) = $Year $CardCode $Month $DocStatus $GroupCode";
	if($Year >= 2023) {
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานรายงานใบสั่งซื้อสินค้าง บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานรายงานใบสั่งซื้อสินค้าง บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	$PageHeader = [ 'font' => [ 'bold' => true, 'size' => 9.1 ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold   = ['font' => [ 'bold' => true ]];

	$sheet->setCellValue('A1',"เลขที่ใบสั่งซื้อ");
	$sheet->setCellValue('B1',"รหัสร้านค้า");
	$sheet->setCellValue('C1',"ชื่อร้านค้า");
	$sheet->setCellValue('D1',"เลขที่ PR");
	$sheet->setCellValue('E1',"ประเภทเอกสาร");
	$sheet->setCellValue('F1',"วันที่สั่งซื้อ");
	$sheet->setCellValue('G1',"วันที่กำหนดส่ง");
	$sheet->setCellValue('H1',"ยอดสั่งซื้อ (THB)");
	$sheet->setCellValue('I1',"หมายเหตุ");
	$sheet->getStyle('A1:I1')->applyFromArray($PageHeader);

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(16);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(55);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(90);

	$Row = 1;
	while($result = odbc_fetch_array($QRY)) {
		$Row++;
		$sheet->setCellValue('A'.$Row,$result['DocNum']);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('B'.$Row,$result['CardCode']);
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('C'.$Row,conutf8($result['CardName']));

		$sheet->setCellValue('D'.$Row,$result['U_PurchaseFor']);
		$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);
	
		$sheet->setCellValue('E'.$Row,$result['DocType']);
		$sheet->getStyle('E'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('F'.$Row,date("d/m/Y",strtotime($result['DocDate'])));
		$sheet->getStyle('F'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('G'.$Row,date("d/m/Y",strtotime($result['DocDueDate'])));
		$sheet->getStyle('G'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('H'.$Row,$result['DocTotal']);
		$sheet->getStyle('H'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");

		$sheet->setCellValue('I'.$Row,conutf8($result['Comments']));
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานรายงานใบสั่งซื้อสินค้าง - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/POrder/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'POrder', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

// $arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if($_GET['a'] == 'CallData') {
	$TeamSale = $_POST['TeamSale'];
	$SQL = "
		SELECT T0.DocEntry, CONCAT(T2.Beginstr,T1.DocNum) AS DocNum,T1.DocDate,T1.DocDueDate,T1.CardCode,T1.CardName,T1.U_PONo,
			T0.ItemCode,T0.CodeBars,T0.Dscription AS ItemName,T4.U_ProductStatus AS PST,T0.OpenQty,T0.Price,T0.LineTotal AS Total,
			T3.SlpName,(SELECT SUM(A0.OnHand) FROM OITW A0 WHERE A0.ItemCode = T0.ItemCode AND A0.WhsCode IN ('KSM','KSY','KB4')) AS OnHand,
			ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC),'2023-01-01') AS DateIn  
		FROM RDR1 T0
		LEFT JOIN ORDR T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
		LEFT JOIN OSLP T3 ON T1.SlpCode = T3.SlpCode
		LEFT JOIN OITM T4 ON T4.ItemCode = T0.ItemCode
		WHERE T1.DocStatus = 'O' AND T1.CANCELED = 'N' AND T0.LineStatus = 'O' AND T0.OpenQty > 0 AND T3.U_Dim1 IN ($TeamSale) 
		ORDER BY T0.DocDate";
	$QRY = SAPSelect($SQL);
	$r = 0;
	$Data = "";
	while($result = odbc_fetch_array($QRY)) {
		if ($result['OnHand'] == 0){
			$OnHand = "<span style='color:#8B0000;font-weight: bold;'>".number_format($result['OnHand'],0)."</span>";
		}else{
			$OnHand = number_format($result['OnHand'],0);
		}
		if ($result['OnHand'] >= $result['OpenQty']) {
			$OpenQty = "<span style='color:#006400;font-weight: bold;'>".number_format($result['OpenQty'],0)."</span>";
		}else{
			$OpenQty = number_format($result['OpenQty'],0);
		}

		$Get_SaleDate = MySQLSelect("SELECT SaleDate FROM skuplan WHERE ItemCode = '".$result['ItemCode']."' AND StatusDoc = 1 LIMIT 1");
		$SaleDate = "";
		$ColorSaleDate = "";
		if(isset($Get_SaleDate['SaleDate'])) {
			if($Get_SaleDate['SaleDate'] != '0000-00-00') {
				$SaleDate = date("d/m/Y",strtotime($Get_SaleDate['SaleDate']));
				$ColorSaleDate = "text-danger";
			}else{
				if(date("Y-m-d",strtotime($result['DateIn'])) != '2023-01-01') {
					$SaleDate = date("d/m/Y",strtotime($result['DateIn']));
					$ColorSaleDate = "text-success";
				}else{
					$SaleDate = "ของเก่าคงค้าง";
				}
			}
		}else{
			if(date("Y-m-d",strtotime($result['DateIn'])) != '2023-01-01') {
				$SaleDate = date("d/m/Y",strtotime($result['DateIn']));
				$ColorSaleDate = "text-success";
			}else{
				$SaleDate = "ของเก่าคงค้าง";
			}
		}

		$Get_Remark = MySQLSelect("SELECT Remark FROM backorder_sales WHERE DocEntry = ".$result['DocEntry']." AND ItemCode = '".$result['ItemCode']."' AND Status = 'A' LIMIT 1");
		$Remark = "";
		if(isset($Get_Remark['Remark'])) {
			$Remark = $Get_Remark['Remark'];
		}

		switch ($_SESSION['DeptCode']) {
			case 'DP005':
			case 'DP006':
			case 'DP007':
			case 'DP008':
				$Disabled = "";
				break;
			case 'DP003':
				switch ($_SESSION['LvCode']) {
					case 'LV018':
					case 'LV019':
					case 'LV104':
					case 'LV105':
					case 'LV106':
						$Disabled = "";
						break;
					default: $Disabled = "disabled"; break;
				}
				break;
			default: $Disabled = "disabled"; break;
		}
			

		$Data .= "
		<tr>
			<td class='text-center'>".$result['DocNum']."</td>
			<td class='text-center'>".date("d/m/Y",strtotime($result['DocDate']))."</td>
			<td class='text-center'>".date("d/m/Y",strtotime($result['DocDueDate']))."</td>
			<td>".$result['CardCode']." | ".conutf8($result['CardName'])."</td>
			<td>".$result['U_PONo']."</td>
			<td>".$result['ItemCode']." | ".conutf8($result['ItemName'])." [".$result['CodeBars']."]</td>
			<td class='text-center'>[".$result['PST']."]</td>
			<td class='text-right'>".$OnHand."</td>
			<td class='text-right'>".$OpenQty."</td>
			<td class='text-right'>".number_format($result['Price'],2)."</td>
			<td class='text-right'>".number_format($result['Total'],2)."</td>
			<td>".conutf8($result['SlpName'])."</td>
			<td class='text-center $ColorSaleDate'>".$SaleDate."</td>
			<td><textarea $Disabled class='remark' cols='20' style='border: 1px solid #dce7f1 !important; outline: none;' name='Remark' id='Remark' dataRemark='".$result['DocEntry']."::".$result['ItemCode']."'>$Remark</textarea></td>
		</tr>";
	}
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'SaveRemark') {
	$DocEntry = $_POST['DocEntry'];
	$ItemCode = $_POST['ItemCode'];
	$Remark   = $_POST['Remark'];
	$Chk = "SELECT * FROM backorder_sales WHERE DocEntry = $DocEntry AND ItemCode = '$ItemCode' AND Status = 'A'";
	if(CHKRowDB($Chk) != 0) {
		$SQL_UPDATE = "UPDATE backorder_sales SET Status = 'I' WHERE DocEntry = $DocEntry AND ItemCode = '$ItemCode' AND Status = 'A'";
		MySQLUpdate($SQL_UPDATE);
	}
	$SQL_INSERT = "INSERT INTO backorder_sales SET DocEntry = $DocEntry, ItemCode = '$ItemCode', Remark = '$Remark', CreateUkey = '".$_SESSION['ukey']."', CreateDate = NOW()";
	MySQLInsert($SQL_INSERT);
}

if($_GET['a'] == 'Excel') {
	$TeamSale = $_POST['TeamSale'];
	$SQL = "
		SELECT T0.DocEntry, CONCAT(T2.Beginstr,T1.DocNum) AS DocNum,T1.DocDate,T1.DocDueDate,T1.CardCode,T1.CardName,T1.U_PONo,
			T0.ItemCode,T0.CodeBars,T0.Dscription AS ItemName,T4.U_ProductStatus AS PST,T0.OpenQty,T0.Price,T0.LineTotal AS Total,
			T3.SlpName,(SELECT SUM(A0.OnHand) FROM OITW A0 WHERE A0.ItemCode = T0.ItemCode AND A0.WhsCode IN ('KSM','KSY','KB4')) AS OnHand,
			ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC),'2023-01-01') AS DateIn  
		FROM RDR1 T0
		LEFT JOIN ORDR T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
		LEFT JOIN OSLP T3 ON T1.SlpCode = T3.SlpCode
		LEFT JOIN OITM T4 ON T4.ItemCode = T0.ItemCode
		WHERE T1.DocStatus = 'O' AND T1.CANCELED = 'N' AND T0.LineStatus = 'O' AND T0.OpenQty > 0 AND T3.U_Dim1 IN ($TeamSale) 
		ORDER BY T0.DocDate";
	$QRY = SAPSelect($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงาน Back Order Sales บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงาน Back Order Sales บจ.คิงบางกอก อินเตอร์เทรด");
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

	$sheet->setCellValue('A1',"เลขที่เอกสาร");
	$sheet->setCellValue('B1',"วันที่เอกสาร");
	$sheet->setCellValue('C1',"กำหนดส่ง");
	$sheet->setCellValue('D1',"รหัสลูกค้า");
	$sheet->setCellValue('E1',"ชื่อลูกค้า");
	$sheet->setCellValue('F1',"เลขที่ PO");
	$sheet->setCellValue('G1',"รหัสสินค้า");
	$sheet->setCellValue('H1',"บาร์โค้ด");
	$sheet->setCellValue('I1',"ชื่อสินค้า");
	$sheet->setCellValue('J1',"สถานะสินค้า");
	$sheet->setCellValue('K1',"คงคลัง");
	$sheet->setCellValue('L1',"ค้างส่ง (หน่วย)");
	$sheet->setCellValue('M1',"ราคา / หน่วย");
	$sheet->setCellValue('N1',"ราคารวม (บาท)");
	$sheet->setCellValue('O1',"พนักงานขาย");
	$sheet->setCellValue('P1',"วันที่พร้อมขาย/เข้าล่าสุด");
	$sheet->setCellValue('Q1',"หมายเหตุ");

	$sheet->getStyle('A1:Q1')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(40);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(50);
	$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(35);
	$spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(21);
	$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);

	$Row = 1;
	while($result = odbc_fetch_array($QRY)) {
		$Row++;
		// เลขที่เอกสาร
		$sheet->setCellValue('A'.$Row,$result['DocNum']);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);
		// วันที่เอกสาร
		$sheet->setCellValue('B'.$Row,date("d/m/Y",strtotime($result['DocDate'])));
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);
		// กำหนดส่ง
		$sheet->setCellValue('C'.$Row,date("d/m/Y",strtotime($result['DocDueDate'])));
		$sheet->getStyle('C'.$Row)->applyFromArray($TextCenter);
		// รหัสลูกค้า
		$sheet->setCellValue('D'.$Row,$result['CardCode']);
		$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);
		// ชื่อลูกค้า
		$sheet->setCellValue('E'.$Row,conutf8($result['CardName']));
		// เลขที่ PO
		$sheet->setCellValue('F'.$Row,$result['U_PONo']);
		// รหัสสินค้า
		$sheet->setCellValue('G'.$Row,$result['ItemCode']);
		$sheet->getStyle('G'.$Row)->applyFromArray($TextCenter);
		// บาร์โค้ด
		$sheet->setCellValue('H'.$Row,$result['CodeBars']);
		$sheet->getStyle('H'.$Row)->applyFromArray($TextCenter);
		// ชื่อสินค้า
		$sheet->setCellValue('I'.$Row,conutf8($result['ItemName']));
		// สถานะสินค้า
		$sheet->setCellValue('J'.$Row,conutf8($result['PST']));
		// คงคลัง
		$sheet->setCellValue('K'.$Row,$result['OnHand']);
		$sheet->getStyle('K'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		// ค้างส่ง
		$sheet->setCellValue('L'.$Row,$result['OpenQty']);
		$sheet->getStyle('L'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		// ราคา / หน่วย
		$sheet->setCellValue('M'.$Row,$result['Price']);
		$sheet->getStyle('M'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		// ราคารวม (บาท)
		$sheet->setCellValue('N'.$Row,$result['Total']);
		$sheet->getStyle('N'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		// พนักงานขาย
		$sheet->setCellValue('O'.$Row,conutf8($result['SlpName']));
		// วันที่พร้อมขาย/เข้าล่าสุด
		$Get_SaleDate = MySQLSelect("SELECT SaleDate FROM skuplan WHERE ItemCode = '".$result['ItemCode']."' AND StatusDoc = 1 AND typeCode = 'A02' LIMIT 1");
		$SaleDate = "";
		$ColorSaleDate = "";
		if(isset($Get_SaleDate['SaleDate'])) {
			$SaleDate = date("d/m/Y",strtotime($Get_SaleDate['SaleDate']));
			$ColorSaleDate = "text-danger";
		}else{
			if(date("Y-m-d",strtotime($result['DateIn'])) != '2023-01-01') {
				$SaleDate = date("d/m/Y",strtotime($result['DateIn']));
				$ColorSaleDate = "text-success";
			}else{
				$SaleDate = "ของเก่าคงค้าง";
			}
		}
		$sheet->setCellValue('P'.$Row,$SaleDate);
		$sheet->getStyle('P'.$Row)->applyFromArray($TextCenter);
		if(isset($Get_SaleDate['SaleDate'])) {
			$ColorSaleDate = "text-danger";
			$spreadsheet->getActiveSheet()->getStyle('P'.$Row)->getFont()->getColor()->setARGB('ffdc3545');
		}else{
			if(date("Y-m-d",strtotime($result['DateIn'])) != '2023-01-01') {
				$spreadsheet->getActiveSheet()->getStyle('P'.$Row)->getFont()->getColor()->setARGB('ff198754');
			}
		}
		// หมายเหตุ
		$Get_Remark = MySQLSelect("SELECT Remark FROM backorder_sales WHERE DocEntry = ".$result['DocEntry']." AND ItemCode = '".$result['ItemCode']."' AND Status = 'A' LIMIT 1");
		$Remark = "";
		if(isset($Get_Remark['Remark'])) {
			$Remark = $Get_Remark['Remark'];
		}
		$sheet->setCellValue('Q'.$Row,$Remark);
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงาน Back Order Sales - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/BackOrderSales/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'BackOrderSales', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
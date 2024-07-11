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


if($_GET['p'] == "GetData") {
	$ItemType = $_POST['ItemType'];
	$SortType = $_POST['SortType'];

	switch($SortType) {
		case "0":
			$OrderSQL = "D0.ItemCode ASC";
		break;
		default:
			$Sort = explode("#",$SortType);
			$OrderSQL = "D0.".$Sort[0]." ".$Sort[1];
		break;
	}

	// echo $OrderSQL;

	switch($ItemType) {
		case "1": $SQLWhr = "WHERE A1.ItmsGrpCod NOT IN (102,126)"; break;
		case "2": $SQLWhr = "WHERE A1.ItmsGrpCod IN (123,124,125,127)"; break;
		case "3": $SQLWhr = "WHERE A1.ItmsGrpCod NOT IN (102,123,124,125,126,127)"; break;
		default : $SQLWhr = "WHERE A1.ItmsGrpCod NOT IN (102,123,124,125,126,127)"; break;
	}

	$GetDataSQL =
		"SELECT D0.*
		FROM (
			SELECT
				C0.ItemCode, C0.ItemName, C0.U_ProductStatus, C0.SalUnitMsr, C0.Qty, C0.AvgQty, C0.OnHand, C0.OnOrder, C0.NewOrder, C0.TOV,
				CASE
					WHEN C0.TOV > 0 THEN DATEADD(DAY,ROUND(C0.TOV*30,0), GETDATE())
					ELSE NULL
				END AS 'EmptyDate',
				(SELECT R0.CardCode FROM OITM R0 WHERE R0.ItemCode = C0.ItemCode) AS 'VendorCode',
				(SELECT R1.CardName FROM OITM R0 LEFT JOIN OCRD R1 ON R0.CardCode = R1.CardCode WHERE R0.ItemCode = C0.ItemCode) AS 'VendorName',
				CASE
					WHEN (C0.U_ProductStatus NOT LIKE 'D%' OR C0.U_ProductStatus NOT IN ('K','M','R')) AND (C0.AvgQty >= 0) AND (C0.TOV <= C0.LeadTime+C0.SafetyStock)
					THEN
						CASE
							WHEN ROUND((((C0.LeadTime-C0.TOV+C0.SafetyStock)*C0.AvgQty)/1)*1,0)-C0.OnOrder < 0 THEN 0
							ELSE ROUND((((C0.LeadTime-C0.TOV+C0.SafetyStock)*C0.AvgQty)/1)*1,0)-C0.OnOrder
						END
					ELSE 0
				END AS 'ROP',
				CASE
					WHEN C0.U_ProductStatus NOT IN ('K','M') AND C0.NewOrder > 0 THEN ROUND(((C0.OnHand+C0.OnOrder+C0.NewOrder)/C0.AvgQty)-C0.LeadTime,2)
					WHEN C0.U_ProductStatus NOT IN ('K','M') AND C0.NewOrder = 0 THEN ROUND(((C0.OnHand+C0.OnOrder+(ROUND((((C0.LeadTime-C0.TOV+C0.SafetyStock)*C0.AvgQty)/1)*1,0)-C0.OnOrder))/C0.AvgQty)-C0.LeadTime,2)
					ELSE 0
				END AS 'EstTOV'
			FROM (
				SELECT
					B0.ItemCode, B0.ItemName, B0.SalUnitMsr, B0.U_ProductStatus, B0.Qty, B0.AvgQty, B0.OnHand, B0.OnOrder,
					ROUND(B0.OnHand/NULLIF(B0.AvgQty,0),2) AS 'TOV', B0.SafetyStock, B0.LeadTime,
					COALESCE(
						(
							SELECT
								SUM(Q1.Quantity)
							FROM ODRF Q0
							LEFT JOIN DRF1 Q1 ON Q0.DocEntry = Q1.DocEntry
							WHERE Q0.ObjType = '22' AND Q0.DocStatus = 'O' AND Q1.ItemCode = B0.ItemCode
						), 0
					) AS 'NewOrder'
				FROM (
					SELECT
						A0.ItemCode, A1.ItemName, A1.SalUnitMsr, A1.U_ProductStatus, SUM(A0.Qty) AS 'Qty',
						CASE WHEN ROUND(SUM(A0.Qty)/12,0) != 0 THEN ROUND(SUM(A0.Qty)/12,0) ELSE 1 END AS 'AvgQty',
						(SELECT SUM(P0.OnHand) FROM OITW P0 LEFT JOIN OWHS P1 ON P0.WhsCode = P1.WhsCode WHERE P0.ItemCode = A0.ItemCode AND (P1.Location IN ('1','2') AND P0.WhsCode NOT IN ('KB1.1')) GROUP BY P0.ItemCode) AS 'OnHand',
						A1.OnOrder, (A1.ToleranDay/30) AS 'SafetyStock', (A1.LeadTime/30) AS 'LeadTime'
					FROM (
						SELECT T0.ItemCode, SUM(T0.Quantity) AS 'Qty' FROM INV1 T0 LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry WHERE T0.DocDate > DATEADD(m,-12,GETDATE()) AND T1.CANCELED = 'N' GROUP BY T0.ItemCode
						UNION ALL
						SELECT T2.ItemCode, SUM(T2.Quantity) AS 'Qty' FROM KBI_DB2022.dbo.INV1 T2 LEFT JOIN KBI_DB2022.dbo.OINV T3 ON T2.DocEntry = T3.DocEntry  WHERE T2.DocDate > DATEADD(m,-12,GETDATE()) AND T3.CANCELED = 'N' GROUP BY T2.ItemCode
						UNION ALL
						SELECT T4.ItemCode, -SUM(T4.Quantity) AS 'Qty' FROM RIN1 T4 LEFT JOIN ORIN T5 ON T4.DocEntry = T5.DocEntry WHERE T4.DocDate > DATEADD(m,-12,GETDATE()) AND T5.CANCELED = 'N' GROUP BY T4.ItemCode
						UNION ALL
						SELECT T6.ItemCode, -SUM(T6.Quantity) AS 'Qty' FROM KBI_DB2022.dbo.RIN1 T6 LEFT JOIN KBI_DB2022.dbo.ORIN T7 ON T6.DocEntry = T7.DocEntry WHERE T6.DocDate > DATEADD(m,-12,GETDATE()) AND T7.CANCELED = 'N' GROUP BY T6.ItemCode
					) A0
					LEFT JOIN OITM A1 ON A0.ItemCode = A1.ItemCode $SQLWhr
					GROUP BY A0.ItemCode, A1.ItemName, A1.SalUnitMsr, A1.U_ProductStatus, A1.OnOrder, A1.ToleranDay, A1.LeadTime
				) B0
				WHERE (B0.ItemCode NOT BETWEEN '99-991-001' AND '99-999-999' AND B0.ItemCode NOT LIKE '00-000-%')
				GROUP BY B0.ItemCode, B0.ItemName, B0.SalUnitMsr, B0.U_ProductStatus, B0.Qty, B0.AvgQty, B0.OnHand, B0.OnOrder, B0.SafetyStock, B0.LeadTime
			) C0
		) D0
		ORDER BY $OrderSQL";
	// echo $GetDataSQL;
	$GetDataQRY = SAPSelect($GetDataSQL);
	$Rows = 0;
	$no = 1;
	$i = 0;
	while($GetDataRST = odbc_fetch_array($GetDataQRY)) {
		if($GetDataRST['ROP'] > 0) {
			$arrCol[$i]['RowStyle'] = "color: #004D99; background-color: #CCE6FF;";
		} else {
			$arrCol[$i]['RowStyle'] = "color: #000000;";
		}

		if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP010") {
			$arrCol[$i]['Vendor']	  = $GetDataRST['VendorCode']." - ".conutf8($GetDataRST['VendorName']);
		}

		if($GetDataRST['TOV'] < 0) {
			$arrCol[$i]['TOVStyle'] = "color: #990000; font-weight: bold; text-align: center;";
			$arrCol[$i]['TOVText']  = "DEAD";
		} else {
			if($GetDataRST['TOV'] <= 4) {
				$arrCol[$i]['TOVStyle'] = "color: #996600; background-color: #FFEECC; font-weight: bold; text-align: right";
			} else {
				if($GetDataRST['TOV'] <= 6) {
					$arrCol[$i]['TOVStyle'] = "color: #006600; background-color: #D5F5D6; font-weight: bold; text-align: right";
				} else {
					$arrCol[$i]['TOVStyle'] = "color: #990000; background-color: #FFCCCC; font-weight: bold; text-align: right";
				}
			}
			$arrCol[$i]['TOVText']  = number_format($GetDataRST['TOV'],2);
		}

		if($GetDataRST['EstTOV'] < 0) {
			$arrCol[$i]['EstTOVStyle'] = "color: #990000; font-weight: bold; text-align: center;";
			$arrCol[$i]['EstTOVText']  = "DEAD";
		} else {
			if($GetDataRST['EstTOV'] <= 4) {
				$arrCol[$i]['EstTOVStyle'] = "color: #996600; background-color: #FFEECC; font-weight: bold; text-align: right";
			} else {
				if($GetDataRST['EstTOV'] <= 6) {
					$arrCol[$i]['EstTOVStyle'] = "color: #006600; background-color: #D5F5D6; font-weight: bold; text-align: right";
				} else {
					$arrCol[$i]['EstTOVStyle'] = "color: #990000; background-color: #FFCCCC; font-weight: bold; text-align: right";
				}
			}
			$arrCol[$i]['EstTOVText']  = number_format($GetDataRST['EstTOV'],2);
		}

		if($GetDataRST['EmptyDate'] != NULL) {
			$arrCol[$i]['EmptyDate'] = date("d/m/Y",strtotime($GetDataRST['EmptyDate']));
		} else {
			$arrCol[$i]['EmptyDate'] = "";
		}

		$arrCol[$i]['No']			  = number_format($no);
		$arrCol[$i]['ItemDscription'] = $GetDataRST['ItemCode']." - ".conutf8($GetDataRST['ItemName']);
		$arrCol[$i]['SaleUnitMsr']	  = conutf8($GetDataRST['SalUnitMsr']);
		$arrCol[$i]['ProductStatus']  = conutf8($GetDataRST['U_ProductStatus']);
		$arrCol[$i]['Qty']			  = number_format($GetDataRST['Qty'],0);
		$arrCol[$i]['AvgQty']		  = number_format($GetDataRST['AvgQty'],0);
		$arrCol[$i]['OnHand']		  = number_format($GetDataRST['OnHand'],0);
		$arrCol[$i]['OnOrder']		  = number_format($GetDataRST['OnOrder'],0);
		$arrCol[$i]['NewOrder']		  = number_format($GetDataRST['NewOrder'],0);
		$arrCol[$i]['ROP']		  	  = number_format($GetDataRST['ROP'],0);


		$no++;
		$i++;
	}

	$arrCol['Rows'] = $i;
}

if($_GET['p'] == 'ExportData') {
	$ItemType = $_POST['ItemType'];
	$SortType = $_POST['SortType'];
	switch($SortType) {
		case "0":
			$OrderSQL = "D0.ItemCode ASC";
		break;
		default:
			$Sort = explode("#",$SortType);
			$OrderSQL = "D0.".$Sort[0]." ".$Sort[1];
		break;
	}

	switch($ItemType) {
		case "1": $SQLWhr = "WHERE A1.ItmsGrpCod NOT IN (102,126)"; break;
		case "2": $SQLWhr = "WHERE A1.ItmsGrpCod IN (123,124,125,127)"; break;
		case "3": $SQLWhr = "WHERE A1.ItmsGrpCod NOT IN (102,123,124,125,126,127)"; break;
		default : $SQLWhr = "WHERE A1.ItmsGrpCod NOT IN (102,123,124,125,126,127)"; break;
	}

	$GetDataSQL =
		"SELECT D0.*
		FROM (
			SELECT
				C0.ItemCode, C0.ItemName, C0.U_ProductStatus, C0.SalUnitMsr, C0.Qty, C0.AvgQty, C0.OnHand, C0.OnOrder, C0.NewOrder, C0.TOV,
				CASE
					WHEN C0.TOV > 0 THEN DATEADD(DAY,ROUND(C0.TOV*30,0), GETDATE())
					ELSE NULL
				END AS 'EmptyDate',
				(SELECT R0.CardCode FROM OITM R0 WHERE R0.ItemCode = C0.ItemCode) AS 'VendorCode',
				(SELECT R1.CardName FROM OITM R0 LEFT JOIN OCRD R1 ON R0.CardCode = R1.CardCode WHERE R0.ItemCode = C0.ItemCode) AS 'VendorName',
				CASE
					WHEN (C0.U_ProductStatus NOT LIKE 'D%' OR C0.U_ProductStatus NOT IN ('K','M','R')) AND (C0.AvgQty >= 0) AND (C0.TOV <= C0.LeadTime+C0.SafetyStock)
					THEN
						CASE
							WHEN ROUND((((C0.LeadTime-C0.TOV+C0.SafetyStock)*C0.AvgQty)/1)*1,0)-C0.OnOrder < 0 THEN 0
							ELSE ROUND((((C0.LeadTime-C0.TOV+C0.SafetyStock)*C0.AvgQty)/1)*1,0)-C0.OnOrder
						END
					ELSE 0
				END AS 'ROP',
				CASE
					WHEN C0.U_ProductStatus NOT IN ('K','M') AND C0.NewOrder > 0 THEN ROUND(((C0.OnHand+C0.OnOrder+C0.NewOrder)/C0.AvgQty)-C0.LeadTime,2)
					WHEN C0.U_ProductStatus NOT IN ('K','M') AND C0.NewOrder = 0 THEN ROUND(((C0.OnHand+C0.OnOrder+(ROUND((((C0.LeadTime-C0.TOV+C0.SafetyStock)*C0.AvgQty)/1)*1,0)-C0.OnOrder))/C0.AvgQty)-C0.LeadTime,2)
					ELSE 0
				END AS 'EstTOV'
			FROM (
				SELECT
					B0.ItemCode, B0.ItemName, B0.SalUnitMsr, B0.U_ProductStatus, B0.Qty, B0.AvgQty, B0.OnHand, B0.OnOrder,
					ROUND(B0.OnHand/NULLIF(B0.AvgQty,0),2) AS 'TOV', B0.SafetyStock, B0.LeadTime,
					COALESCE(
						(
							SELECT
								SUM(Q1.Quantity)
							FROM ODRF Q0
							LEFT JOIN DRF1 Q1 ON Q0.DocEntry = Q1.DocEntry
							WHERE Q0.ObjType = '22' AND Q0.DocStatus = 'O' AND Q1.ItemCode = B0.ItemCode
						), 0
					) AS 'NewOrder'
				FROM (
					SELECT
						A0.ItemCode, A1.ItemName, A1.SalUnitMsr, A1.U_ProductStatus, SUM(A0.Qty) AS 'Qty',
						CASE WHEN ROUND(SUM(A0.Qty)/12,0) != 0 THEN ROUND(SUM(A0.Qty)/12,0) ELSE 1 END AS 'AvgQty',
						(SELECT SUM(P0.OnHand) FROM OITW P0 LEFT JOIN OWHS P1 ON P0.WhsCode = P1.WhsCode WHERE P0.ItemCode = A0.ItemCode AND (P1.Location IN ('1','2') AND P0.WhsCode NOT IN ('KB1.1')) GROUP BY P0.ItemCode) AS 'OnHand',
						A1.OnOrder, (A1.ToleranDay/30) AS 'SafetyStock', (A1.LeadTime/30) AS 'LeadTime'
					FROM (
						SELECT T0.ItemCode, SUM(T0.Quantity) AS 'Qty' FROM INV1 T0 LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry WHERE T0.DocDate > DATEADD(m,-12,GETDATE()) AND T1.CANCELED = 'N' GROUP BY T0.ItemCode
						UNION ALL
						SELECT T2.ItemCode, SUM(T2.Quantity) AS 'Qty' FROM KBI_DB2022.dbo.INV1 T2 LEFT JOIN KBI_DB2022.dbo.OINV T3 ON T2.DocEntry = T3.DocEntry  WHERE T2.DocDate > DATEADD(m,-12,GETDATE()) AND T3.CANCELED = 'N' GROUP BY T2.ItemCode
						UNION ALL
						SELECT T4.ItemCode, -SUM(T4.Quantity) AS 'Qty' FROM RIN1 T4 LEFT JOIN ORIN T5 ON T4.DocEntry = T5.DocEntry WHERE T4.DocDate > DATEADD(m,-12,GETDATE()) AND T5.CANCELED = 'N' GROUP BY T4.ItemCode
						UNION ALL
						SELECT T6.ItemCode, -SUM(T6.Quantity) AS 'Qty' FROM KBI_DB2022.dbo.RIN1 T6 LEFT JOIN KBI_DB2022.dbo.ORIN T7 ON T6.DocEntry = T7.DocEntry WHERE T6.DocDate > DATEADD(m,-12,GETDATE()) AND T7.CANCELED = 'N' GROUP BY T6.ItemCode
					) A0
					LEFT JOIN OITM A1 ON A0.ItemCode = A1.ItemCode $SQLWhr
					GROUP BY A0.ItemCode, A1.ItemName, A1.SalUnitMsr, A1.U_ProductStatus, A1.OnOrder, A1.ToleranDay, A1.LeadTime
				) B0
				WHERE (B0.ItemCode NOT BETWEEN '99-991-001' AND '99-999-999' AND B0.ItemCode NOT LIKE '00-000-%')
				GROUP BY B0.ItemCode, B0.ItemName, B0.SalUnitMsr, B0.U_ProductStatus, B0.Qty, B0.AvgQty, B0.OnHand, B0.OnOrder, B0.SafetyStock, B0.LeadTime
			) C0
		) D0
		ORDER BY $OrderSQL";
	$GetDataQRY = SAPSelect($GetDataSQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานสินค้า Turn Over บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานสินค้า Turn Over บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// Header
	$sheet->setCellValue('A1',"No.");
	$sheet->setCellValue('B1',"รายการสินค้า **เฉพาะรายการสินค้าที่มีความเคลื่อนไหวภายใน 12 เดือนล่าสุด**");
	$sheet->setCellValue('D1',"ตัวแทนจำหน่าย");
	$sheet->setCellValue('E1',"หน่วย");
	$sheet->setCellValue('F1',"สถานะสินค้า");
	$sheet->setCellValue('G1',"ยอดขาย 12 เดือนย้อนหลัง");
	$sheet->setCellValue('H1',"ยอดขายเฉลี่ยต่อเดือน");
	$sheet->setCellValue('I1',"จำนวนสินค้าคงเหลือ");
	$sheet->setCellValue('J1',"T/O ปัจจุบัน");
	$sheet->setCellValue('K1',"วันที่คาดว่าสินค้าหมด");
	$sheet->setCellValue('L1',"จำนวนสั่งซื้อในระบบ");
	$sheet->setCellValue('M1',"จำนวนสั่งซื้อที่ต้องการ");
	$sheet->setCellValue('N1',"Re Order Point");
	$sheet->setCellValue('O1',"T/O เมื่อของเข้า");

	$spreadsheet->getActiveSheet()->mergeCells('B1:C1');

	// Add Style Header
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->getStyle('A1:O1')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10); 
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(55);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(17);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(9);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(28);
	$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(24);
	$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(22);
	$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
	$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(22);
	$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(21);
	$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(23);
	$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(19);
	$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(18);
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18); // Set Height Row

	// Style Body
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$No = 0; $Row = 1;
	while($result = odbc_fetch_array($GetDataQRY)) {
		$No++; $Row++;
		// No.
		$sheet->setCellValue('A'.$Row,$No);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		// รายการสินค้า 
		$sheet->setCellValue('B'.$Row,$result['ItemCode']);
		$sheet->setCellValue('C'.$Row,conutf8($result['ItemName']));
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

		// ตัวแทนจำหน่าย
		$sheet->setCellValue('D'.$Row,$result['VendorCode']." - ".conutf8($result['VendorName']));

		// หน่วย
		$sheet->setCellValue('E'.$Row,conutf8($result['SalUnitMsr']));

		// สถานะสินค้า
		$sheet->setCellValue('F'.$Row,conutf8($result['U_ProductStatus']));
		$sheet->getStyle('F'.$Row)->applyFromArray($TextCenter);

		// ยอดขาย 12 เดือนย้อนหลัง
		$sheet->setCellValue('G'.$Row,$result['Qty']);
		$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// ยอดขายเฉลี่ยต่อเดือน
		$sheet->setCellValue('H'.$Row,$result['AvgQty']);
		$sheet->getStyle('H'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// จำนวนสินค้าคงเหลือ
		$sheet->setCellValue('I'.$Row,$result['OnHand']);
		$sheet->getStyle('I'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// T/O ปัจจุบัน
		if($result['TOV'] < 0) {
			$sheet->setCellValue('J'.$Row,"DEAD");
		}else{
			$sheet->setCellValue('J'.$Row,number_format($result['TOV'],2));
		}
		$sheet->getStyle('J'.$Row)->applyFromArray($TextRight);
		$sheet->getStyle('J'.$Row)->applyFromArray($TextBold);
		$spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");

		// วันที่คาดว่าสินค้าหมด
		if($result['EmptyDate'] != NULL) {
			$sheet->setCellValue('K'.$Row,date("d/m/Y",strtotime($result['EmptyDate'])));
		} else {
			$sheet->setCellValue('K'.$Row,"");
		}
		$sheet->getStyle('K'.$Row)->applyFromArray($TextCenter);

		// จำนวนสั่งซื้อในระบบ
		$sheet->setCellValue('L'.$Row,$result['OnOrder']);
		$sheet->getStyle('L'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// จำนวนสั่งซื้อที่ต้องการ
		$sheet->setCellValue('M'.$Row,$result['NewOrder']);
		$sheet->getStyle('M'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// Re Order Point
		$sheet->setCellValue('N'.$Row,$result['ROP']);
		$sheet->getStyle('N'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// T/O เมื่อของเข้า
		if($result['EstTOV'] < 0) {
			$sheet->setCellValue('O'.$Row,"DEAD");
		}else{
			$sheet->setCellValue('O'.$Row,$result['EstTOV']);
		}
		$sheet->getStyle('O'.$Row)->applyFromArray($TextRight);
		$sheet->getStyle('O'.$Row)->applyFromArray($TextBold);
		$spreadsheet->getActiveSheet()->getStyle('O'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานสินค้า Turn Over - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/TOV/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'TOV', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
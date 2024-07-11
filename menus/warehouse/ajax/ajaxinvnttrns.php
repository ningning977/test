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
	$cYear     = $_POST['Year'];
	$pYear     = $_POST['Year']-1;
	$WareHouse = $_POST['WareHouse'];
	$Type      = $_POST['Type'];
	$HideZero  = NULL;
	if($_POST['HideZero'] == 'true') {
		$HideZero = "LEFT JOIN OITW A3 ON A0.ItemCode = A3.ItemCode WHERE A3.WhsCode = '$WareHouse' AND A3.OnHand > 0";
	}
	
	if($cYear < 2023) {
		$tbpf = "KBI_DB2022.dbo.";
	} else {
		$tbpf = "";
	}

	$SQL = 
		"SELECT
			A0.ItemCode, A1.ItemName, A1.U_ProductStatus, A1.SalUnitMsr,
			ISNULL(
			    ISNULL(
				   (SELECT DISTINCT TOP 1 P0.DocDate FROM OPCH P0 LEFT JOIN PCH1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC),
				   (SELECT DISTINCT TOP 1 P0.DocDate FROM KBI_DB2022.dbo.OPCH P0 LEFT JOIN KBI_DB2022.dbo.PCH1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC)
				),
				CASE
					WHEN (SELECT DISTINCT TOP 1 P0.DocDate FROM OIGN P0 LEFT JOIN IGN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC) = '2022-12-31' OR (SELECT DISTINCT TOP 1 P0.DocDate FROM OIGN P0 LEFT JOIN IGN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC) IS NULL
					THEN (SELECT DISTINCT TOP 1 P0.DocDate FROM KBI_DB2022.dbo.OIGN P0 LEFT JOIN KBI_DB2022.dbo.IGN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC)
					ELSE (SELECT DISTINCT TOP 1 P0.DocDate FROM OIGN P0 LEFT JOIN IGN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC) END
			) AS 'LastImpDate',
			(CASE WHEN A1.LastPurDat = '2022-12-31' OR A1.LastPurDat IS NULL THEN ISNULL(A2.LastPurPrc,A1.LastPurPrc) ELSE A2.LastPurPrc END) * 1.07 AS 'Cost',
			SUM(A0.M_00) AS 'M_0',
			SUM(A0.M_00 + A0.M_01) AS 'M_1',
			SUM(A0.M_00 + A0.M_01 + A0.M_02) AS 'M_2',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03) AS 'M_3',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04) AS 'M_4',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05) AS 'M_5',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06) AS 'M_6',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07) AS 'M_7',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08) AS 'M_8',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08 + A0.M_09) AS 'M_9',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08 + A0.M_09 + A0.M_10) AS 'M_10',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08 + A0.M_09 + A0.M_10 + A0.M_11) AS 'M_11',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08 + A0.M_09 + A0.M_10 + A0.M_11 + A0.M_12) AS 'M_12'
		FROM (
			SELECT
				T0.ItemCode,
				(CASE WHEN T0.CreateDate = '2023-01-01'  OR (YEAR(T0.CreateDate) <= $pYear) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_00',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 1) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_01',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 2) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_02',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 3) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_03',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 4) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_04',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 5) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_05',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 6) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_06',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 7) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_07',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 8) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_08',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 9) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_09',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 10) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_10',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 11) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_11',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 12) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_12'
				FROM OINM T0
				WHERE T0.Warehouse = '$WareHouse'
				GROUP BY T0.ItemCode, T0.CreateDate
			) A0
		LEFT JOIN ".$tbpf."OITM A1 ON A0.ItemCode = A1.ItemCode
		LEFT JOIN KBI_DB2022.dbo.OITM A2 ON A1.ItemCode = A2.ItemCode
		$HideZero
		GROUP BY A0.ItemCode, A1.ItemName, A1.SalUnitMsr, A1.U_ProductStatus, A1.LastPurDat, A1.LastPurPrc, A2.LastPurPrc
		ORDER BY A0.ItemCode";
	// echo $SQL;
	$QRY = SAPSelect($SQL);
	$r = 0; $No = 0;
	while($result = odbc_fetch_array($QRY)) {
		$No++;
		$arrCol[$r]['No']     = $No;
		$arrCol[$r]['Year']     = $cYear;
		$arrCol[$r]['ItemCode'] = $result['ItemCode'];
		$arrCol[$r]['ItemName'] = conutf8($result['ItemName']);
		$arrCol[$r]['Status']   = $result['U_ProductStatus'];
		$arrCol[$r]['Unit']     = conutf8($result['SalUnitMsr']);
		$arrCol[$r]['LastDate'] = date("d/m/Y",strtotime($result['LastImpDate']));
		if($cYear == date("Y")) {
			for($m = 0; $m <= 12; $m++){
				if($m <= date("m")) {
					if($Type == "Q") {
						$arrCol[$r]['M_'.$m] = number_format($result['M_'.$m],0);
					} else {
						$arrCol[$r]['M_'.$m] = number_format($result['M_'.$m] * $result['Cost'],0);
					}
				}else{
					$arrCol[$r]['M_'.$m] = "-";
				}
			}
		}else{
			for($m = 0; $m <= 12; $m++){
				if($Type == "Q") {
					$arrCol[$r]['M_'.$m] = number_format($result['M_'.$m],0);
				} else {
					$arrCol[$r]['M_'.$m] = number_format($result['M_'.$m] * $result['Cost'],0);
				}
			}
		}
		$r++;
	}
}

if($_GET['a'] == 'Export') {
	$cYear     = $_POST['Year'];
	$pYear     = $_POST['Year']-1;
	$WareHouse = $_POST['WareHouse'];
	$Type      = $_POST['Type'];
	$HideZero  = NULL;
	if($_POST['HideZero'] == 'true') {
		$HideZero = "LEFT JOIN OITW A2 ON A0.ItemCode = A2.ItemCode WHERE A2.WhsCode = '$WareHouse' AND A2.OnHand > 0";
	}

	if($cYear < 2023) {
		$tbpf = "KBI_DB2022.dbo.";
	} else {
		$tbpf = "";
	}
	$SQL = 
		"SELECT
			A0.ItemCode, A1.ItemName, A1.U_ProductStatus, A1.SalUnitMsr,
			ISNULL(
			    ISNULL(
				   (SELECT DISTINCT TOP 1 P0.DocDate FROM OPCH P0 LEFT JOIN PCH1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC),
				   (SELECT DISTINCT TOP 1 P0.DocDate FROM KBI_DB2022.dbo.OPCH P0 LEFT JOIN KBI_DB2022.dbo.PCH1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC)
				),
				CASE
					WHEN (SELECT DISTINCT TOP 1 P0.DocDate FROM OIGN P0 LEFT JOIN IGN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC) = '2022-12-31' OR (SELECT DISTINCT TOP 1 P0.DocDate FROM OIGN P0 LEFT JOIN IGN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC) IS NULL
					THEN (SELECT DISTINCT TOP 1 P0.DocDate FROM KBI_DB2022.dbo.OIGN P0 LEFT JOIN KBI_DB2022.dbo.IGN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC)
					ELSE (SELECT DISTINCT TOP 1 P0.DocDate FROM OIGN P0 LEFT JOIN IGN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = A0.ItemCode AND P1.WhsCode = '$WareHouse' ORDER BY P0.DocDate DESC) END
			) AS 'LastImpDate',
			(CASE WHEN A1.LastPurDat = '2022-12-31' OR A1.LastPurDat IS NULL THEN ISNULL(A2.LastPurPrc,A1.LastPurPrc) ELSE A2.LastPurPrc END) * 1.07 AS 'Cost',
			SUM(A0.M_00) AS 'M_0',
			SUM(A0.M_00 + A0.M_01) AS 'M_1',
			SUM(A0.M_00 + A0.M_01 + A0.M_02) AS 'M_2',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03) AS 'M_3',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04) AS 'M_4',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05) AS 'M_5',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06) AS 'M_6',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07) AS 'M_7',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08) AS 'M_8',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08 + A0.M_09) AS 'M_9',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08 + A0.M_09 + A0.M_10) AS 'M_10',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08 + A0.M_09 + A0.M_10 + A0.M_11) AS 'M_11',
			SUM(A0.M_00 + A0.M_01 + A0.M_02 + A0.M_03 + A0.M_04 + A0.M_05 + A0.M_06 + A0.M_07 + A0.M_08 + A0.M_09 + A0.M_10 + A0.M_11 + A0.M_12) AS 'M_12'
		FROM (
			SELECT
				T0.ItemCode,
				(CASE WHEN T0.CreateDate = '2023-01-01'  OR (YEAR(T0.CreateDate) <= $pYear) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_00',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 1) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_01',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 2) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_02',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 3) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_03',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 4) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_04',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 5) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_05',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 6) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_06',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 7) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_07',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 8) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_08',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 9) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_09',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 10) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_10',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 11) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_11',
				(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = 12) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_12'
				FROM ".$tbpf."OINM T0
				WHERE T0.Warehouse = '$WareHouse'
				GROUP BY T0.ItemCode, T0.CreateDate
			) A0
		LEFT JOIN OITM A1 ON A0.ItemCode = A1.ItemCode
		LEFT JOIN KBI_DB2022.dbo.OITM A2 ON A1.ItemCode = A2.ItemCode
		$HideZero
		GROUP BY A0.ItemCode, A1.ItemName, A1.SalUnitMsr, A1.U_ProductStatus, A1.LastPurDat, A1.LastPurPrc, A2.LastPurPrc
		ORDER BY A0.ItemCode";
	$QRY = SAPSelect($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานความเคลื่อนไหวสินค้าคงคลัง บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานความเคลื่อนไหวสินค้าคงคลัง บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// Header
	$sheet->setCellValue('A1',"ลำดับ");
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"รหัสสินค้า");
	$spreadsheet->getActiveSheet()->mergeCells('B1:B2');
	$sheet->setCellValue('C1',"ชือสินค้า");
	$spreadsheet->getActiveSheet()->mergeCells('C1:C2');
	$sheet->setCellValue('D1',"สถานะ");
	$spreadsheet->getActiveSheet()->mergeCells('D1:D2');
	$sheet->setCellValue('E1',"หน่วย");
	$spreadsheet->getActiveSheet()->mergeCells('E1:E2');
	$sheet->setCellValue('F1',"วันที่รับเข้าล่าสุด");
	$spreadsheet->getActiveSheet()->mergeCells('F1:F2');
	$sheet->setCellValue('G1',"จำนวนคงคลัง ณ สิ้นเดือน ของปี ".$cYear);
	$spreadsheet->getActiveSheet()->mergeCells('G1:S1');
	$sheet->setCellValue('G2',"ตั้งต้น");
	$mCell = ['0','H','I','J','K','L','M','N','O','P','Q','R','S'];
	for($m = 1; $m <= 12; $m++) {
		$sheet->setCellValue($mCell[$m]."2",txtMonth($m));
	}
	// Add Style Header
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->getStyle('A1:G1')->applyFromArray($PageHeader);
	$sheet->getStyle('G2:S2')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(54);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	for($m = 1; $m <= 12; $m++) {
		$spreadsheet->getActiveSheet()->getColumnDimension($mCell[$m])->setWidth(13);
	}
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);
	
	// Style Body
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$Row = 2; $No = 0;
	while($result = odbc_fetch_array($QRY)) {
		$Row++; $No++;
		// ลำดับ
		$sheet->setCellValue('A'.$Row,$No);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		// รหัสสินค้า
		$sheet->setCellValue('B'.$Row,$result['ItemCode']);
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

		// ชื่อสินค้า
		$sheet->setCellValue('C'.$Row,conutf8($result['ItemName']));

		// สถานะ
		$sheet->setCellValue('D'.$Row,$result['U_ProductStatus']);
		$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);

		// หน่วย
		$sheet->setCellValue('E'.$Row,conutf8($result['SalUnitMsr']));
		$sheet->getStyle('E'.$Row)->applyFromArray($TextCenter);

		// วันที่รับเข้าล่าสุด
		$sheet->setCellValue('F'.$Row,date("d/m/Y",strtotime($result['LastImpDate'])));
		$sheet->getStyle('F'.$Row)->applyFromArray($TextCenter);

		// ตั้งต้น
		$sheet->setCellValue('G'.$Row,$result['M_0']);
		$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");

		// เดือน ม.ค. - ธ.ค.
		if($cYear == date("Y")) {
			for($m = 1; $m <= 12; $m++){
				if($m <= date("m")) {
					if($Type == "Q") {
						$sheet->setCellValue($mCell[$m].$Row,$result['M_'.$m]);
					} else {
						$sheet->setCellValue($mCell[$m].$Row,$result['M_'.$m] * $result['Cost']);
					}
					$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
					$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0");
				}else{
					$sheet->setCellValue($mCell[$m].$Row,"-");
					$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
				}
			}
		}else{
			for($m = 1; $m <= 12; $m++){
				if($Type == "Q") {
					$sheet->setCellValue($mCell[$m].$Row,$result['M_'.$m]);
				} else {
					$sheet->setCellValue($mCell[$m].$Row,$result['M_'.$m] * $result['Cost']);
				}
				$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
				$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0");
			}
		}
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานความเคลื่อนไหวสินค้าคงคลัง - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/Invnttrns/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'Invnttrns', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

if($_GET['a'] == 'CallData2'){
	$ItemCode  = $_POST['ItemCode'];
	$WareHouse = $_POST['WareHouse'];
	$StartDate = $_POST['StartDate'];
	$EndDate   = $_POST['EndDate'];
	$Year      = $_POST['Year'];

	$SQL = "
		SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', X2.U_Dim1 AS SaleTeam,X0.*
		FROM (
			SELECT CASE WHEN P0.TransType IN (13,15) THEN 'A' ELSE 'B' END AS 'ORDR',P0.TransNum, P0.DocDate,P0.CreateDate,P0.TransType,
				CASE WHEN P0.TransType = 13 THEN (SELECT DISTINCT ISNULL('IV-',W1.BeginStr) FROM OINV W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W1.BeginStr FROM ORIN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W1.BeginStr FROM ODLN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W1.BeginStr FROM ORDN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W1.BeginStr FROM OPDN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W1.BeginStr FROM ORPD W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W1.BeginStr FROM OIGN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W1.BeginStr FROM OIGE W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W1.BeginStr FROM OWTR W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
				END AS BeginStr,
				P0.DocNum,P0.SAPtb,
				CASE WHEN P0.TransType = 13 THEN (SELECT DISTINCT W0.DocEntry FROM OINV W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W0.DocEntry FROM ORIN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W0.DocEntry FROM ODLN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W0.DocEntry FROM ORDN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W0.DocEntry FROM OPDN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W0.DocEntry FROM ORPD W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W0.DocEntry FROM OIGN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W0.DocEntry FROM OIGE W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W0.DocEntry FROM OWTR W0 WHERE W0.DocNum = P0.DocNum)
				END AS DocEntry,
				CASE WHEN P0.TransType = 13 THEN (SELECT DISTINCT W0.BaseEntry FROM INV1 W0 LEFT JOIN OINV W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W0.BaseEntry FROM DLN1 W0 LEFT JOIN ODLN W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W0.BaseEntry FROM PDN1 W0 LEFT JOIN OPDN W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum) 
					ELSE NULL 
				END AS SODocEntry,P0.CardCode,P0.CardName,
				CASE WHEN P0.TransType = 13 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName FROM OINV W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORIN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ODLN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORDN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OPDN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORPD W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OIGN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OIGE W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OWTR W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
				END AS Owner,
				P0.ItemCode, P0.ItemName,P0.WhsCode,P0.InQty,P0.OutQty
			FROM (
				SELECT MAX(T0.TransNum) AS TransNum,T0.[DocDate] AS DocDate, T0.[CreateDate] AS 'CreateDate', T0.TransType,
					CASE WHEN T0.TransType = 13 THEN 'OINV'
						WHEN T0.TransType = 14 THEN 'ORIN'
						WHEN T0.TransType = 15 THEN 'ODLN'
						WHEN T0.TransType = 16 THEN 'ORDN'
						WHEN T0.TransType = 20 THEN 'OPDN'
						WHEN T0.TransType = 21 THEN 'ORPD'
						WHEN T0.TransType = 59 THEN 'OIGN'
						WHEN T0.TransType = 60 THEN 'OIGE'
						WHEN T0.TransType = 67 THEN 'OWTR'
					END AS SAPtb,T0.[BASE_REF] AS 'DocNum',
					T0.[ItemCode] AS ItemCode, T0.[Dscription] AS ItemName,T0.[WareHouse] AS WhsCode,SUM(T0.[InQty]) AS InQty,SUM(T0.[OutQty]) AS OutQty,T0.CardCode,T0.CardName
				FROM OINM T0 
				WHERE (T0.[CreateDate] BETWEEN '$StartDate' AND '$EndDate') AND T0.[WareHouse] = '$WareHouse' AND T0.ItemCode = '$ItemCode' AND ((T0.[InQty] + T0.[OutQty]) != 0) 
				GROUP BY T0.[DocDate], T0.[CreateDate], T0.TransType,T0.[BASE_REF],T0.[ItemCode],T0.[Dscription],T0.[WareHouse],T0.CardCode,T0.CardName 
			) P0
		) X0
		LEFT JOIN ORDR X1 ON X0.SODocEntry = X1.DocEntry
		LEFT JOIN OSLP X2 ON X1.SlpCode = X2.SlpCode
		ORDER BY X0.TransNum";
	$SQL2 = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', SUM(T0.[InQty]-T0.[OutQty]) AS OnHand FROM OINM T0 WHERE T0.[ItemCode] = '$ItemCode' AND T0.[Warehouse] = '$WareHouse' AND T0.[CreateDate] < '$StartDate'";
	if($Year >= 2023){
		$QRY2 = SAPSelect($SQL2);
	}else{
		$QRY2 = conSAP8($SQL2);
	}
	$OnHand = odbc_fetch_array($QRY2);
	$QtyOld = 0;
	if(isset($OnHand['OnHand'])) { $QtyOld = $OnHand['OnHand']; }
	$Data = array();
	$Data['No'][0]         = "&nbsp;";
	$Data['CreateDate'][0] = "&nbsp;";
	$Data['DocDate'][0]    = "&nbsp;";
	$Data['DocNum'][0]     = "&nbsp;";
	$Data['DocType'][0]    = "ยอดยกมา";
	$Data['ReceivePay'][0] = "Opening Balance";
	$Data['Team'][0]       = "&nbsp;";
	$Data['WhsCode'][0]    = "&nbsp;";
	$Data['Location'][0]   = "&nbsp;";
	$Data['InQty'][0]      = "&nbsp;";
	$Data['OutQty'][0]     = "&nbsp;";
	$Data['QtyShow'][0]    = number_format($QtyOld,0);
	$Data['Owner'][0]      = "&nbsp;";
	if($Year >= 2023){
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	$r = 0; $TotalIn = 0; $TotalOut = 0;
	while($result = odbc_fetch_array($QRY)) {
		$r++;
		$ORDR[$r]       = $result['ORDR'];
        $CreateDate[$r] = $result['CreateDate'];
        $DocDate[$r]    = $result['DocDate'];
        $TransType[$r]  = $result['TransType'];
        $BeginStr[$r]   = $result['BeginStr'];
        $DocNum[$r]     = $BeginStr[$r].$result['DocNum'];
		if($result['SaleTeam'] != null) {
			$SaleTeam[$r]   = $result['SaleTeam'];
		}else{
			$SaleTeam[$r]   = "&nbsp;";
		}
        $SAPtb[$r]      = $result['SAPtb'];
        $DocEntry[$r]   = $result['DocEntry'];
        $SODocEntry[$r] = $result['SODocEntry'];
        $Owner[$r]      = conutf8($result['Owner']);
        $CardCode[$r]   = $result['CardCode'];
        $CardName[$r]   = conutf8($result['CardName']);
        $WhsCode[$r]    = $result['WhsCode'];
		if($result['InQty'] != 0) {
			$InQty[$r]  = $result['InQty'];
		}else{
			$InQty[$r]  = "-";
		}
        if($result['OutQty'] != 0) {
			$OutQty[$r] = $result['OutQty'];
		}else{
			$OutQty[$r] = "-";
		}
	}
	
	$QtyShow = $QtyOld; 
	for($i = 1; $i <= $r; $i++) {
		if($InQty[$i] == "-")  { $In = 0;}  else { $In = $InQty[$i];   $InQty[$i]  = number_format($InQty[$i],0); }
		if($OutQty[$i] == "-") { $Out = 0;} else { $Out = $OutQty[$i]; $OutQty[$i] = number_format($OutQty[$i],0); }
		$QtyShow = $QtyShow + (1*$In) + (-1*$Out);
		// echo $QtyShow." + (1*".$In.") + (-1*".$Out.")\n";
		switch($TransType[$i]) {
			case '13': 
				$textType = "เบิกสินค้าเพื่อขาย";
				$Owner[$i] = "";
				$Location = "&nbsp;";
				if($SODocEntry[$i] != null || $SODocEntry[$i] != "") {
					$SQLCase  = "
						SELECT T0.DocNum, T1.uName, T1.uLastName, T2.LocationRack
						FROM picker_soheader T0
						LEFT JOIN users T1 ON T0.UkeyPicker = T1.uKey 
						LEFT JOIN transecdata T2 ON T0.SODocEntry = T2.trnCode AND T2.ItemCode = '$ItemCode'  
						WHERE T0.SODocEntry = ".$SODocEntry[$i]." AND T0.DocType = 'ORDR'";
					if(CHKRowDB($SQLCase) > 0) {
						$result    = MySQLSelect($SQLCase);
						$Owner[$i] = $result['uName']." ".$result['uLastName'];
						if($result['LocationRack'] != null) {
							$Location = $result['LocationRack'];
						}else{
							$Location = "&nbsp;";
						}
					}
				}
			break;
			case '14': 
				$textType = "รับคืนสินค้าขาย";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '15': 
				$textType = "เบิกยืมสินค้า";
				$Owner[$i] = "";
				$Location = "&nbsp;";
				if($SODocEntry[$i] != null || $SODocEntry[$i] != "") {
					$SQLCase  = "
						SELECT T0.DocNum, T1.uName, T1.uLastName, T2.LocationRack
						FROM picker_soheader T0
						LEFT JOIN users T1 ON T0.UkeyPicker = T1.uKey 
						LEFT JOIN transecdata T2 ON T0.SODocEntry = T2.trnCode AND T2.ItemCode = '$ItemCode'  
						WHERE T0.SODocEntry = ".$SODocEntry[$i]." AND T0.DocType = 'ORDR'";
					if(CHKRowDB($SQLCase) > 0) {
						$result    = MySQLSelect($SQLCase);
						$Owner[$i] = $result['uName']." ".$result['uLastName'];
						if($result['LocationRack'] != null) {
							$Location = $result['LocationRack'];
						}else{
							$Location = "&nbsp;";
						}
					}
				}
			break;
			case '16': 
				$textType = "รับคืนสินค้ายืม";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '20': 
				$textType = "รับสินค้าเข้า";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '21': 
				$textType = "คืนสินค้าซัพพลายเออร์";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '60': 
			case '59': 
				$textType = "ปรับสต๊อคภายใน";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '67': 
				$textType = "โอนย้ายระหว่างคลัง";
                $Location = $WhsCode[$i]."-Recive";
			break;
		}

		$Data['No'][$i]         = $i;
		$Data['CreateDate'][$i] = date("d/m/Y",strtotime($CreateDate[$i]));
		$Data['DocDate'][$i]    = date("d/m/Y",strtotime($DocDate[$i]));
		$Data['DocNum'][$i]     = $DocNum[$i];
		$Data['DocType'][$i]    = $textType;
		$Data['ReceivePay'][$i] = $CardCode[$i]." ".$CardName[$i];
		$Data['Team'][$i]       = $SaleTeam[$i];
		$Data['WhsCode'][$i]    = $WhsCode[$i];
		$Data['Location'][$i]   = $Location;
		$Data['InQty'][$i]      = $InQty[$i];
		$Data['OutQty'][$i]     = $OutQty[$i];
		$Data['QtyShow'][$i]    = number_format($QtyShow,0);
		$Data['Owner'][$i]      = $Owner[$i];
	}

	$LastRow = $r+1;
	$Data['No'][$LastRow]         = "&nbsp;";
	$Data['CreateDate'][$LastRow] = "&nbsp;";
	$Data['DocDate'][$LastRow]    = "&nbsp;";
	$Data['DocNum'][$LastRow]     = "&nbsp;";
	$Data['DocType'][$LastRow]    = "ยอดสุดท้าย";
	$Data['ReceivePay'][$LastRow] = "Closed Balance";
	$Data['Team'][$LastRow]       = "&nbsp;";
	$Data['WhsCode'][$LastRow]    = "&nbsp;";
	$Data['Location'][$LastRow]   = "&nbsp;";
	$Data['InQty'][$LastRow]      = "&nbsp;";
	$Data['OutQty'][$LastRow]     = "&nbsp;";
	$Data['QtyShow'][$LastRow]    = number_format($QtyShow,0);
	$Data['Owner'][$LastRow]      = "&nbsp;";

	$arrCol['Row'] = $LastRow;
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'CallData3') {
	
	if(isset($_POST['WSG'])) {
		$SQL_WSG = "SELECT T0.WhseCode FROM whsemd T0 WHERE T0.WhseSubGrpCode = '".$_POST['WSG']."'";
		$RST_WSG = MySQLSelect($SQL_WSG);
		$WhrSQL = "T0.Warehouse IN (".$RST_WSG['WhseCode'].")";
		$Year  = date("Y");
		$PYear = $Year-1;
		$IF_Month = date("m");
	}else{
		$WareH = $_POST['WareH'];
		$Year  = $_POST['Year'];
		$PYear = $Year-1;
		$IF_Month = ($Year == date("Y")) ? date("m") : 12;
		switch($WareH) {
			case "KBI": $WhrSQL = "(T1.WhsCode = 'KB2') OR (T1.Location IN (9) AND T1.StreetNo IS NULL)"; break;
			case "MT1": 
			case "MT2": 
			case "TT2":
			case "OUL": $WhrSQL = "T1.Location IN (6) AND T1.StreetNo = '$WareH'"; break;
			case "WPP": $WhrSQL = "T1.WhsCode IN ('WP','WP1','WP2','WP2.2','RD4')"; break;
		}
	}

	$SQL = "
		SELECT
			A0.Warehouse, A0.WhsName,
			SUM(A0.M_00 * A0.Cost) AS 'M_00',";
			for($m = 1; $m <= 12; $m++) {
				if($m < 10) {
					$SQL .= "
					SUM(A0.M_0".$m."_I * A0.Cost) AS 'M_0".$m."_I', SUM(A0.M_0".$m."_O * A0.Cost) AS 'M_0".$m."_O', SUM(A0.M_0".$m."_O1 * A0.Cost) AS 'M_0".$m."_O1', SUM(A0.M_0".$m."_O2 * A0.Cost) AS 'M_0".$m."_O2', SUM(A0.M_0".$m."_O3 * A0.Cost) AS 'M_0".$m."_O3'";
				}else{
					$SQL .= "
					SUM(A0.M_".$m."_I * A0.Cost) AS 'M_".$m."_I', SUM(A0.M_".$m."_O * A0.Cost) AS 'M_".$m."_O', SUM(A0.M_".$m."_O1 * A0.Cost) AS 'M_".$m."_O1', SUM(A0.M_".$m."_O2 * A0.Cost) AS 'M_".$m."_O2', SUM(A0.M_".$m."_O3 * A0.Cost) AS 'M_".$m."_O3'";
				}
				$SQL .= ($m < 12) ? "," : "";
			}
		$SQL .= "
		FROM (
			SELECT T0.Warehouse, T1.WhsName, T0.ItemCode,
			(CASE WHEN T2.LastPurDat = '2022-12-31' OR T2.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc,T2.LastPurPrc) ELSE T2.LastPurPrc END) * 1.07 AS 'Cost',
			(CASE WHEN T0.CreateDate = '2023-01-01'  OR (YEAR(T0.CreateDate) <= $PYear) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_00',";
			for($m = 1; $m <= 12; $m++) {
				if($m < 10) {
					$SQL .= "
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.InQty > 0) THEN SUM(T0.InQty) ELSE 0 END) AS 'M_0".$m."_I',
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O',
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 13) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O1',
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 15) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O2',
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType IN (60,67)) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O3'";
				}else{
					$SQL .= "
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.InQty > 0) THEN SUM(T0.InQty) ELSE 0 END) AS 'M_".$m."_I',
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O',
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 13) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O1',
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 15) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O2',
					(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType IN (60,67)) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O3'";
				}
				$SQL .= ($m < 12) ? "," : "";
			}
			$SQL .= "
			FROM OINM T0
			LEFT JOIN OWHS T1 ON T0.Warehouse = T1.WhsCode
			LEFT JOIN OITM T2 ON T0.ItemCode = T2.ItemCode
			LEFT JOIN KBI_DB2022.dbo.OITM T3 ON T0.ItemCode = T3.ItemCode
			WHERE $WhrSQL
			GROUP BY T0.TransType, T0.Warehouse, T1.WhsName, T0.CreateDate, T0.ItemCode, T0.InQty, T0.OutQty, T2.LastPurDat, T3.LastPurDat, T2.LastPurPrc, T3.LastPurPrc
		) A0
		GROUP BY A0.Warehouse, A0.WhsName
		ORDER BY A0.Warehouse";
	// echo $SQL;
	$QRY = SAPSelect($SQL);
	$r = 0; $tmp = 0;
	while($result = odbc_fetch_array($QRY)) {
		$r++;
		$WhseID[$r]   = $result['Warehouse'];
		$WhseName[$r] = conutf8($result['WhsName']);
		for($m = 1; $m <= 12; $m++) {
			if($m == 1) {
				$Data[$r][$m]['r1'] = $result['M_00'];
			}else{
				$Data[$r][$m]['r1'] = $tmp;
			}

			if($m < 10) {
				$Data[$r][$m]['r2'] = $result['M_0'.$m.'_I'];
				$Data[$r][$m]['r3'] = $result['M_0'.$m.'_O'];
			}else{
				$Data[$r][$m]['r2'] = $result['M_'.$m.'_I'];
				$Data[$r][$m]['r3'] = $result['M_'.$m.'_O'];
			}
		
			$Data[$r][$m]['r4'] = $Data[$r][$m]['r1']+($Data[$r][$m]['r2']+$Data[$r][$m]['r3']);
			$tmp = $Data[$r][$m]['r4'];

			if($m < 10) {
				$Data[$r][$m]['r5'] = $result['M_0'.$m.'_O1'];
				$Data[$r][$m]['r6'] = $result['M_0'.$m.'_O2'];
				$Data[$r][$m]['r7'] = $result['M_0'.$m.'_O3'];
			}else{
				$Data[$r][$m]['r5'] = $result['M_'.$m.'_O1'];
				$Data[$r][$m]['r6'] = $result['M_'.$m.'_O2'];
				$Data[$r][$m]['r7'] = $result['M_'.$m.'_O3'];
			}
		}
	}

	$Tbody = "";
	if($r != 0) {
		for($i = 1; $i <= $r; $i++) {
			$Tbody .= "<tr style='border-top: 4px double #9A1118;'>
				<th rowspan='7'>";
					if(isset($_POST['WSG'])) {
						$Tbody .= "<a href='javascript:void(0);' class='WSG-ws text-dark' data-ws='".$WhseID[$i]."'><span class='fw-bolder'>".$WhseID[$i]."</span><br>".$WhseName[$i]."</a>";
					}else{
						$Tbody .= "<span class='fw-bolder'>".$WhseID[$i]."</span><br>".$WhseName[$i];
					}
				$Tbody .= "
				</th>
				<th class='table-warning text-warning'>ต้นทุนคลัง ณ ต้นเดือน</th>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= $IF_Month){
						$Tbody .= "<th class='text-right table-warning text-warning'>".number_format($Data[$i][$m]['r1'],0)."</th>";
					}else{
						$Tbody .= "<th class='text-right table-warning text-warning'>-</th>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr>
				<th class='table-success text-success'>ต้นทุนรับเข้ารวม</th>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= $IF_Month){
						$Tbody .= "<th class='text-right table-success text-success'>".number_format($Data[$i][$m]['r2'],0)."</th>";
					}else{
						$Tbody .= "<th class='text-right table-success text-success'>-</th>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr>
				<th class='table-danger text-primary'>ต้นทุนออกรวม</th>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= $IF_Month){
						$Tbody .= "<th class='text-right table-danger text-primary'>".number_format($Data[$i][$m]['r3'],0)."</th>";
					}else{
						$Tbody .= "<th class='text-right table-danger text-primary'>-</th>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr>
				<td class='text-primary table-light'>ต้นทุนออก (ขาย)</td>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= $IF_Month){
						$Tbody .= "<td class='text-primary table-light text-right'>".number_format($Data[$i][$m]['r5'],0)."</td>";
					}else{
						$Tbody .= "<td class='text-primary table-light text-right'>-</td>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr>
				<td class='text-primary table-light'>ต้นทุนออก (เบิก/แถม)</td>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= $IF_Month){
						$Tbody .= "<td class='text-primary table-light text-right'>".number_format($Data[$i][$m]['r6'],0)."</td>";
					}else{
						$Tbody .= "<td class='text-primary table-light text-right'>-</td>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr>
				<td class='text-primary table-light'>ต้นทุนออก (JU/โอน)</td>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= $IF_Month){
						$Tbody .= "<td class='text-primary table-light text-right'>".number_format($Data[$i][$m]['r7'],0)."</td>";
					}else{
						$Tbody .= "<td class='text-primary table-light text-right'>-</td>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr class='fw-bolder table-active'>
				<td>ต้นทุนคลัง ณ สิ้นเดือน</td>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= $IF_Month){
						$Tbody .= "
						<td class='text-right'>";
							if(isset($_POST['WSG'])) {
								$Tbody .= "<span class='v-detail'>".number_format($Data[$i][$m]['r4'],0)."</span>";
							}else{
								$Tbody .= "<span class='v-detail' style='cursor: pointer;' onclick='Detail(".$Year.",".$m.",\"".$WhseID[$i]."\");'>".number_format($Data[$i][$m]['r4'],0)."</span>";
							}
						$Tbody .= "
						</td>";
					}else{
						$Tbody .= "<td class='text-right'>-</td>";
					}
				}
			$Tbody .= "</tr>";
		}
	}else{
		$Tbody .= "<tr><></tr>";
	}
	$arrCol['Tbody'] = $Tbody;
}

if($_GET['a'] == 'Detail') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	$WareH = $_POST['WareH'];

	$SQL = "
		SELECT X2.U_Dim1 AS SaleTeam, X0.*
		FROM (
			SELECT 
				CASE WHEN P0.TransType IN (13,15) THEN 'A' ELSE 'B' END AS 'ORDR',
				P0.TransNum, P0.DocDate,P0.CreateDate,P0.TransType,
				CASE 
					WHEN P0.TransType = 13 THEN (SELECT DISTINCT ISNULL('IV-',W1.BeginStr) FROM OINV W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W1.BeginStr FROM ORIN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W1.BeginStr FROM ODLN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W1.BeginStr FROM ORDN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W1.BeginStr FROM OPDN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W1.BeginStr FROM ORPD W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W1.BeginStr FROM OIGN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W1.BeginStr FROM OIGE W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W1.BeginStr FROM OWTR W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
				END AS BeginStr,
				P0.DocNum,P0.SAPtb,
				CASE 
					WHEN P0.TransType = 13 THEN (SELECT DISTINCT W0.DocEntry FROM OINV W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W0.DocEntry FROM ORIN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W0.DocEntry FROM ODLN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W0.DocEntry FROM ORDN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W0.DocEntry FROM OPDN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W0.DocEntry FROM ORPD W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W0.DocEntry FROM OIGN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W0.DocEntry FROM OIGE W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W0.DocEntry FROM OWTR W0 WHERE W0.DocNum = P0.DocNum)
				END AS DocEntry,
				CASE 
					WHEN P0.TransType = 13 THEN (SELECT DISTINCT W0.BaseEntry FROM INV1 W0 LEFT JOIN OINV W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W0.BaseEntry FROM DLN1 W0 LEFT JOIN ODLN W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W0.BaseEntry FROM PDN1 W0 LEFT JOIN OPDN W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum) 
					ELSE NULL 
				END AS SODocEntry,P0.CardCode,P0.CardName,
				CASE 
					WHEN P0.TransType = 13 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName FROM OINV W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORIN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ODLN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORDN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OPDN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORPD W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OIGN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OIGE W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OWTR W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
				END AS Owner,
				P0.ItemCode, P0.ItemName,P0.WhsCode,P0.InQty,P0.OutQty, P0.TransValue
			FROM (
				SELECT 
					MAX(T0.TransNum) AS TransNum,T0.[DocDate] AS DocDate, T0.[CreateDate] AS 'CreateDate', T0.TransType,
					CASE 
						WHEN T0.TransType = 13 THEN 'OINV'
						WHEN T0.TransType = 14 THEN 'ORIN'
						WHEN T0.TransType = 15 THEN 'ODLN'
						WHEN T0.TransType = 16 THEN 'ORDN'
						WHEN T0.TransType = 20 THEN 'OPDN'
						WHEN T0.TransType = 21 THEN 'ORPD'
						WHEN T0.TransType = 59 THEN 'OIGN'
						WHEN T0.TransType = 60 THEN 'OIGE'
						WHEN T0.TransType = 67 THEN 'OWTR'
					END AS SAPtb,T0.[BASE_REF] AS 'DocNum',
					T0.[ItemCode] AS ItemCode, T0.[Dscription] AS ItemName,T0.[WareHouse] AS WhsCode, SUM(T0.[InQty]) AS InQty, SUM(T0.[OutQty]) AS OutQty,T0.CardCode,T0.CardName,T0.[TransValue]
				FROM OINM T0 
				WHERE (YEAR(T0.[CreateDate]) = $Year AND MONTH(T0.[CreateDate]) = $Month) AND T0.[WareHouse] = '$WareH' AND ((T0.[InQty] + T0.[OutQty]) != 0) 
				GROUP BY T0.[DocDate], T0.[CreateDate], T0.TransType,T0.[BASE_REF],T0.[ItemCode],T0.[Dscription],T0.[WareHouse],T0.CardCode,T0.CardName, T0.[TransValue]
			) P0
		) X0
		LEFT JOIN ORDR X1 ON X0.SODocEntry = X1.DocEntry
		LEFT JOIN OSLP X2 ON X1.SlpCode = X2.SlpCode
		ORDER BY X0.ItemCode, X0.TransNum";
	$QRY = SAPSelect($SQL);
	$Data = ""; $No = 0; $tmpItemCode = "";
	while($result = odbc_fetch_array($QRY)){
		$No++;
		$Chk = ""; $OutQty = "-"; $InQty = "-";

		if($result['OutQty'] != 0) {
			$OutQty = number_format($result['OutQty'],0);
		}
		if($result['InQty'] != 0) {
			$InQty = number_format($result['InQty'],0);
		}

		if($tmpItemCode != $result['ItemCode']) {
			$tmpItemCode = $result['ItemCode'];
			$Chk = "border-top: 1px solid #9A1118 !important;";
			/* INSERT NEW ROW */
			switch($Month) {
				case "1": $PMonth = 12; $PYear = $Year-1; break;
				default : $PMonth = $Month - 1; $PYear = $Year; break;
			}

			$lastdate 	 = cal_days_in_month(CAL_GREGORIAN, $PMonth, $PYear);
			$GetOpenDate = date("Y-m-d",strtotime($PYear."-".$PMonth."-".$lastdate));

			$SQL2 = 
				"SELECT TOP 1
					T0.ItemCode, T1.ItemName,
					ISNULL((SELECT SUM(P0.InQty-P0.OutQty) FROM OINM P0 WHERE P0.ItemCode = T0.ItemCode AND P0.Warehouse = T0.WhsCode AND (P0.CreateDate <= '$GetOpenDate') GROUP BY P0.ItemCode),0) AS 'OpenQty',
					ISNULL((SELECT SUM(P0.TransValue) FROM OINM P0 WHERE P0.ItemCode = T0.ItemCode AND P0.Warehouse = T0.WhsCode AND (P0.CreateDate <= '$GetOpenDate') GROUP BY P0.ItemCode),0) AS 'OpenValue'
				FROM OITW T0
				LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
				WHERE T0.WhsCode = '$WareH' AND T0.ItemCode = '$tmpItemCode'";
			$QRY2 = SAPSelect($SQL2);
			$RST2 = odbc_fetch_array($QRY2);

			$OpenQty   = $RST2['OpenQty'];
			$OpenValue = $RST2['OpenValue'];
			$CloseQty   = $OpenQty;
			$CloseValue = $OpenValue;
		}
		
		$CloseQty   = $CloseQty + ($result['InQty'] - $result['OutQty']);
		$CloseValue = $CloseValue + ($result['TransValue']);
		
		$Data .= "
			<tr>
				<td style='".$Chk."' class='text-center'>".$No."</td>
				<td style='".$Chk."' class='text-center'>".date("d/m/Y",strtotime($result['DocDate']))."</td>
				<td style='".$Chk."' class='text-center'>".$result['BeginStr'].$result['DocNum']."</td>
				<td style='".$Chk."'>".$result['CardCode']." ".conutf8($result['CardName'])."</td>
				<td style='".$Chk."' class='text-center'>".$result['ItemCode']."</td>
				<td style='".$Chk."'>".conutf8($result['ItemName'])."</td>
				<td style='".$Chk."' class='text-center'>".$result['WhsCode']."</td>
				<td style='".$Chk."' class='text-right fw-bolder'>".number_format($OpenQty,0)."</td>
				<td style='".$Chk."' class='text-right text-success'>".$InQty."</td>
				<td style='".$Chk."' class='text-right text-primary'>".$OutQty."</td>
				<td style='".$Chk."' class='text-right fw-bolder'>".$CloseQty."</td>
				<td style='".$Chk."' class='text-right fw-bolder'>".number_format($CloseValue,2)."</td>
			</tr>";
		$OpenQty = $CloseQty;
	}
	if($No == 0) {
		$Data .= "
			<tr>
				<td colspan='12' class='text-center'>ไม่มีข้อมูล :)</td>
			</tr>";
	}
	$arrCol['Data'] = $Data;
	$arrCol['H'] = FullMonth($Month)." ".$Year;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
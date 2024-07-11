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
	$C_YEAR = $_POST['Year'];
	$P_YEAR = $C_YEAR-1;
	$GroupCode = $_POST['GroupCode'];

	$sql3 = "SELECT T0.GroupCode,T0.GroupName FROM OCQG T0 WHERE T0.GroupCode = '$GroupCode'";
	$sapfqryName = SAPSelect($sql3);
	$GroupName = odbc_fetch_array($sapfqryName);
	$NameCus = conutf8($GroupName['GroupName']);

	$C_SAPSQL =
		"SELECT
			B0.CardCode, B1.CardName,
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 1 THEN B0.DocTotal  END),0) AS 'M_01_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 1 THEN B0.DocProfit END),0) AS 'M_01_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 2 THEN B0.DocTotal  END),0) AS 'M_02_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 2 THEN B0.DocProfit END),0) AS 'M_02_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 3 THEN B0.DocTotal  END),0) AS 'M_03_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 3 THEN B0.DocProfit END),0) AS 'M_03_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 4 THEN B0.DocTotal  END),0) AS 'M_04_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 4 THEN B0.DocProfit END),0) AS 'M_04_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 5 THEN B0.DocTotal  END),0) AS 'M_05_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 5 THEN B0.DocProfit END),0) AS 'M_05_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 6 THEN B0.DocTotal  END),0) AS 'M_06_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 6 THEN B0.DocProfit END),0) AS 'M_06_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 7 THEN B0.DocTotal  END),0) AS 'M_07_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 7 THEN B0.DocProfit END),0) AS 'M_07_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 8 THEN B0.DocTotal  END),0) AS 'M_08_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 8 THEN B0.DocProfit END),0) AS 'M_08_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 9 THEN B0.DocTotal  END),0) AS 'M_09_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 9 THEN B0.DocProfit END),0) AS 'M_09_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 10 THEN B0.DocTotal  END),0) AS 'M_10_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 10 THEN B0.DocProfit END),0) AS 'M_10_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 11 THEN B0.DocTotal  END),0) AS 'M_11_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 11 THEN B0.DocProfit END),0) AS 'M_11_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 12 THEN B0.DocTotal  END),0) AS 'M_12_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 12 THEN B0.DocProfit END),0) AS 'M_12_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) IN (1,2,3,4,5,6,7,8,9,10,11,12) THEN B0.DocTotal  END),0) AS 'ALL_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) IN (1,2,3,4,5,6,7,8,9,10,11,12) THEN B0.DocProfit END),0) AS 'ALL_PRFT'
		FROM (
			SELECT
				A0.CardCode, A0.DocDate, SUM(A0.DocTotal-A0.VatSum) AS 'DocTotal', SUM(A0.GrosProfit) AS 'DocProfit'
			FROM OINV A0
			LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
			WHERE YEAR(A0.DocDate) = $C_YEAR AND A0.CANCELED = 'N'
			GROUP BY A0.CardCode, A0.DocDate
			UNION 
			SELECT
				A0.CardCode, A0.DocDate, -SUM(A0.DocTotal-A0.VatSum) AS 'DocTotal', -SUM(A0.GrosProfit) AS 'DocProfit'
			FROM ORIN A0
			LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
			WHERE YEAR(A0.DocDate) = $C_YEAR AND A0.CANCELED = 'N'
			GROUP BY A0.CardCode, A0.DocDate
		) B0
		LEFT JOIN OCRD B1 ON B0.CardCode = B1.CardCode
		WHERE B1.QryGroup$GroupCode = 'Y'
		GROUP BY YEAR(B0.DocDate), B0.CardCode, B1.CardName";
	$C_SAPSQL .= ($_SESSION['DeptCode'] == "DP006") ? " ORDER BY B0.CardCode" : " ORDER BY ALL_SALE DESC" ;
	$C_SAPQRY = ($C_YEAR <= 2022) ? conSAP8($C_SAPSQL) : SAPSelect($C_SAPSQL) ;

	$P_SAPSQL =
		"SELECT
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 1 THEN B0.DocTotal  END),0) AS 'M_01_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 1 THEN B0.DocProfit END),0) AS 'M_01_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 2 THEN B0.DocTotal  END),0) AS 'M_02_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 2 THEN B0.DocProfit END),0) AS 'M_02_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 3 THEN B0.DocTotal  END),0) AS 'M_03_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 3 THEN B0.DocProfit END),0) AS 'M_03_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 4 THEN B0.DocTotal  END),0) AS 'M_04_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 4 THEN B0.DocProfit END),0) AS 'M_04_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 5 THEN B0.DocTotal  END),0) AS 'M_05_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 5 THEN B0.DocProfit END),0) AS 'M_05_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 6 THEN B0.DocTotal  END),0) AS 'M_06_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 6 THEN B0.DocProfit END),0) AS 'M_06_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 7 THEN B0.DocTotal  END),0) AS 'M_07_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 7 THEN B0.DocProfit END),0) AS 'M_07_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 8 THEN B0.DocTotal  END),0) AS 'M_08_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 8 THEN B0.DocProfit END),0) AS 'M_08_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 9 THEN B0.DocTotal  END),0) AS 'M_09_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 9 THEN B0.DocProfit END),0) AS 'M_09_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 10 THEN B0.DocTotal  END),0) AS 'M_10_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 10 THEN B0.DocProfit END),0) AS 'M_10_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 11 THEN B0.DocTotal  END),0) AS 'M_11_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 11 THEN B0.DocProfit END),0) AS 'M_11_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 12 THEN B0.DocTotal  END),0) AS 'M_12_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 12 THEN B0.DocProfit END),0) AS 'M_12_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) IN (1,2,3,4,5,6,7,8,9,10,11,12) THEN B0.DocTotal  END),0) AS 'ALL_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) IN (1,2,3,4,5,6,7,8,9,10,11,12) THEN B0.DocProfit END),0) AS 'ALL_PRFT'
		FROM (
			SELECT
				A0.CardCode, A0.DocDate, SUM(A0.DocTotal-A0.VatSum) AS 'DocTotal', SUM(A0.GrosProfit) AS 'DocProfit'
			FROM OINV A0
			LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
			WHERE YEAR(A0.DocDate) = $P_YEAR AND A0.CANCELED = 'N'
			GROUP BY A0.CardCode, A0.DocDate
			UNION 
			SELECT
				A0.CardCode, A0.DocDate, -SUM(A0.DocTotal-A0.VatSum) AS 'DocTotal', -SUM(A0.GrosProfit) AS 'DocProfit'
			FROM ORIN A0
			LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
			WHERE YEAR(A0.DocDate) = $P_YEAR AND A0.CANCELED = 'N'
			GROUP BY A0.CardCode, A0.DocDate
		) B0
		LEFT JOIN OCRD B1 ON B0.CardCode = B1.CardCode
		WHERE B1.QryGroup$GroupCode = 'Y'
		GROUP BY YEAR(B0.DocDate)";
	$P_SAPQRY = ($P_YEAR <= 2022) ? conSAP8($P_SAPSQL) : SAPSelect($P_SAPSQL) ;

	$TBODY = "";
	$TFOOT = "";

	for($m = 1; $m <= 12; $m++) {
		${"SUM_SALE_M".$m} = 0;
		${"SUM_PRFT_M".$m} = 0;
		${"P_SUM_SALE_M".$m} = 0;
		${"P_SUM_PRFT_M".$m} = 0;
	}

	$SUM_SALE_ALL = 0;
	$SUM_PRFT_ALL = 0;
	$P_SUM_SALE_ALL = 0;
	$P_SUM_PRFT_ALL = 0;

	while($C_RST = odbc_fetch_array($C_SAPQRY)) {
		$TBODY .= "<tr>".
			"<td class='fw-bold'>".$C_RST['CardCode']." - ".conutf8($C_RST['CardName'])."</td>";
			for($m = 1; $m <= 12; $m++) {
				if($m < 10) {
					$SALE = $C_RST['M_0'.$m.'_SALE'];
					$PRFT = $C_RST['M_0'.$m.'_PRFT'];
				} else {
					$SALE = $C_RST['M_'.$m.'_SALE'];
					$PRFT = $C_RST['M_'.$m.'_PRFT'];
				}

				$TBODY .= ($SALE == 0) ? "<td class='text-right fw-bold'>-</td>" : "<td class='text-right fw-bold'>".number_format($SALE,0)."</td>" ;

				${"SUM_SALE_M".$m} = ${"SUM_SALE_M".$m} + $SALE;
				${"SUM_PRFT_M".$m} = ${"SUM_PRFT_M".$m} + $PRFT;
			}

			$ALL_SALE = $C_RST['ALL_SALE'];
			$ALL_PRFT = $C_RST['ALL_PRFT'];

			$ALL_PCNT = ($ALL_SALE <> 0) ? number_format(($ALL_PRFT/$ALL_SALE)*100,2) : 0.00 ;

			$TBODY .= ($ALL_SALE == 0) ? "<th class='text-right fw-bolder'>-</th>" : "<th class='text-right fw-bolder'>".number_format($ALL_SALE,0)."</th>" ;
			$TBODY .= "<th class='text-center'>$ALL_PCNT%</th>";

			$SUM_SALE_ALL = $SUM_SALE_ALL + $ALL_SALE;
			$SUM_PRFT_ALL = $SUM_PRFT_ALL + $ALL_PRFT;

		$TBODY .= "</tr>";
	}
	// ยอดขายปี ปัจจุบัน
	$TFOOT .= "<tr class='table-success'>".
		"<th>รวมยอดขายปี $C_YEAR</th>";
		for($m = 1; $m <= 12; $m++) {
			$TFOOT .= (${"SUM_SALE_M".$m} == 0) ? "<th class='text-right'>-</th>" : "<th class='text-right'>".number_format(${"SUM_SALE_M".$m},0)."</th>";
		}
		$TFOOT .= ($SUM_SALE_ALL == 0) ? "<th class='text-right'>-</th>" : "<th class='text-right'>".number_format($SUM_SALE_ALL,0)."</th>";
		$TFOOT .= "<th>&nbsp;</th>";
	$TFOOT .= "</tr>";

	// %GP ปัจจุบัน
	$TFOOT .= "<tr class='table-success'>".
		"<td>% กำไรปี $C_YEAR</td>";
		for($m = 1; $m <= 12; $m++) {
			$PCNT_PROFIT = (${"SUM_SALE_M".$m} <> 0) ? (${"SUM_PRFT_M".$m}/${"SUM_SALE_M".$m})*100 : 0 ;
			$TFOOT .= ($PCNT_PROFIT == 0) ? "<td class='text-right'>0.00%</td>" : "<td class='text-right'>".number_format($PCNT_PROFIT,2)."%</td>";
		}
		$PCNT_PROFIT = ($SUM_SALE_ALL <> 0) ? ($SUM_PRFT_ALL/$SUM_SALE_ALL)*100 : 0 ;
		$TFOOT .= ($SUM_SALE_ALL == 0) ? "<td class='text-right'>-</td>" : "<td class='text-right'>".number_format($PCNT_PROFIT,2)."%</td>";
		$TFOOT .= "<td>&nbsp;</td>";
	$TFOOT .= "</tr>";

	while($P_RST = odbc_fetch_array($P_SAPQRY)) {
		for($m = 1; $m <= 12; $m++) {
			if($m < 10) {
				$SALE = $P_RST['M_0'.$m.'_SALE'];
				$PRFT = $P_RST['M_0'.$m.'_PRFT'];
			} else {
				$SALE = $P_RST['M_'.$m.'_SALE'];
				$PRFT = $P_RST['M_'.$m.'_PRFT'];
			}
			${"P_SUM_SALE_M".$m} = $SALE;
			${"P_SUM_PRFT_M".$m} = $PRFT;
		}
		$P_SUM_SALE_ALL = $P_RST['ALL_SALE'];
		$P_SUM_PRFT_ALL = $P_RST['ALL_PRFT'];
	}

	// ยอดขายปี ย้อนหลัง
	$TFOOT .= "<tr class='table-warning'>".
		"<th>รวมยอดขายปี $P_YEAR</th>";
		for($m = 1; $m <= 12; $m++) {
			$TFOOT .= (${"P_SUM_SALE_M".$m} == 0) ? "<th class='text-right'>-</th>" : "<th class='text-right'>".number_format(${"P_SUM_SALE_M".$m},0)."</th>";
		}
		$TFOOT .= ($P_SUM_SALE_ALL == 0) ? "<th class='text-right'>-</th>" : "<th class='text-right'>".number_format($P_SUM_SALE_ALL,0)."</th>";
		$TFOOT .= "<th>&nbsp;</th>";
	$TFOOT .= "</tr>";

	// %GP ย้อนหลัง
	$TFOOT .= "<tr class='table-warning'>".
		"<td>% กำไรปี $P_YEAR</td>";
		for($m = 1; $m <= 12; $m++) {
			$PCNT_PROFIT = (${"P_SUM_SALE_M".$m} <> 0) ? (${"P_SUM_PRFT_M".$m}/${"P_SUM_SALE_M".$m})*100 : 0 ;
			$TFOOT .= ($PCNT_PROFIT == 0) ? "<td class='text-right'>0.00%</td>" : "<td class='text-right'>".number_format($PCNT_PROFIT,2)."%</td>";
		}
		$PCNT_PROFIT = ($P_SUM_SALE_ALL <> 0) ? ($P_SUM_PRFT_ALL/$P_SUM_SALE_ALL)*100 : 0 ;
		$TFOOT .= ($P_SUM_SALE_ALL == 0) ? "<td class='text-right'>-</td>" : "<td class='text-right'>".number_format($PCNT_PROFIT,2)."%</td>";
		$TFOOT .= "<td>&nbsp;</td>";
	$TFOOT .= "</tr>";


	$arrCol['NameCus'] = $NameCus;
	$arrCol['Tbody'] = $TBODY;
	$arrCol['Tfoot'] = $TFOOT;

	$arrCol['SAPsql']  = $C_SAPSQL;
	$arrCol['SAPsql2'] = $P_SAPSQL;
	$arrCol['cYear'] = $C_YEAR;
	$arrCol['pYear'] = $P_YEAR;
}

function StrCell($c) {
	$StrCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
	return $StrCell;
}

if($_GET['a'] == 'Export') {
	$C_YEAR = $_POST['cYear'];
	$P_YEAR = $C_YEAR-1;
	if($_POST['cYear'] <= 2022) {
		$sqlSAP1 = conSAP8($_POST['SQL1']);
	} else {
		$sqlSAP1 = SAPSelect($_POST['SQL1']);
	}
	
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานยอดขายรายห้าง บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานยอดขายรายห้าง บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13); // Value x 6 = pixel in excel
	$spreadsheet->setActiveSheetIndex(0);

	$PageHeader = [ 'font' => [ 'bold' => true, 'size' => 9.1 ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenterBold = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRightBold  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	// HEADER //
	$Row = 2; $Col = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ชื่อลูกค้า");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(70);
	$Col++;

	for($m = 1; $m <= 12; $m++) {
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, FullMonth($m));
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
		$Col++;
	}

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "รวมทั้งหมด");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "% GP");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$sheet->getStyle('A2:'.StrCell($Col).'2')->applyFromArray($PageHeader);

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "รายงานยอดขายรายห้าง : ".$_POST['NameCus']."");
	$sheet->getStyle('A1:'.StrCell($Col).'1')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->mergeCells('A1:'.StrCell($Col).'1');

	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);
	$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(18);

	// // BODY //
	$Row = 3; 
	for($m = 1; $m <= 12; $m++) {
		${"SUM_SALE_M".$m} = 0;
		${"SUM_PRFT_M".$m} = 0;
		${"P_SUM_SALE_M".$m} = 0;
		${"P_SUM_PRFT_M".$m} = 0;
	}
	$SUM_SALE_ALL = 0;
	$SUM_PRFT_ALL = 0;
	$P_SUM_SALE_ALL = 0;
	$P_SUM_PRFT_ALL = 0;
	while($C_RST = odbc_fetch_array($sqlSAP1)) {
		$Col = 1;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $C_RST['CardCode']." - ".conutf8($C_RST['CardName']));
		$Col++;

		for($m = 1; $m <= 12; $m++) {
			if($m < 10) {
				$SALE = $C_RST['M_0'.$m.'_SALE'];
				$PRFT = $C_RST['M_0'.$m.'_PRFT'];
			} else {
				$SALE = $C_RST['M_'.$m.'_SALE'];
				$PRFT = $C_RST['M_'.$m.'_PRFT'];
			}

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, (($SALE == 0) ? "-" : $SALE));
			$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
			$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
			$Col++;

			${"SUM_SALE_M".$m} = ${"SUM_SALE_M".$m} + $SALE;
			${"SUM_PRFT_M".$m} = ${"SUM_PRFT_M".$m} + $PRFT;
		}

		$ALL_SALE = $C_RST['ALL_SALE'];
		$ALL_PRFT = $C_RST['ALL_PRFT'];
		$ALL_PCNT = ($ALL_SALE <> 0) ? (($ALL_PRFT/$ALL_SALE)) : 0;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, (($ALL_SALE == 0) ? "-" : $ALL_SALE));
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $ALL_PCNT);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenterBold);
		$Col++;

		$SUM_SALE_ALL = $SUM_SALE_ALL + $ALL_SALE;
		$SUM_PRFT_ALL = $SUM_PRFT_ALL + $ALL_PRFT;

		$Row++;
	}

	// // FOOTER //
	// ยอดขายปี ปัจจุบัน
	$Col = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "รวมยอดขายปี $C_YEAR");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
	$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextBold);
	$Col++;

	for($m = 1; $m <= 12; $m++) {
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, ((${"SUM_SALE_M".$m} == 0) ? "-" : ${"SUM_SALE_M".$m}));
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
		$Col++;
	}

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, (($SUM_SALE_ALL == 0) ? "-" : $SUM_SALE_ALL));
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
	$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
	$Col++;

	$Row++;
	//End ยอดขายปี ปัจจุบัน

	// % กำไรปี ปัจจุบัน
	$Col = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "% กำไรปี $C_YEAR");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
	$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextBold);
	$Col++;

	for($m = 1; $m <= 12; $m++) {
		$PCNT_PROFIT = (${"SUM_SALE_M".$m} <> 0) ? (${"SUM_PRFT_M".$m}/${"SUM_SALE_M".$m}) : 0 ;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, (($PCNT_PROFIT == 0) ? 0 : $PCNT_PROFIT));
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
		$Col++;
	}

	$PCNT_PROFIT = ($SUM_SALE_ALL <> 0) ? ($SUM_PRFT_ALL/$SUM_SALE_ALL) : 0 ;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, (($SUM_SALE_ALL == 0) ? 0 : $PCNT_PROFIT));
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
	$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
	$Col++;

	$Row++;
	//End % กำไรปี ปัจจุบัน

	if($_POST['pYear'] <= 2022) {
		$sqlSAP2 = conSAP8($_POST['SQL2']);
	} else {
		$sqlSAP2 = SAPSelect($_POST['SQL2']);
	}
	while($P_RST = odbc_fetch_array($sqlSAP2)) {
		for($m = 1; $m <= 12; $m++) {
			if($m < 10) {
				$SALE = $P_RST['M_0'.$m.'_SALE'];
				$PRFT = $P_RST['M_0'.$m.'_PRFT'];
			} else {
				$SALE = $P_RST['M_'.$m.'_SALE'];
				$PRFT = $P_RST['M_'.$m.'_PRFT'];
			}
			${"P_SUM_SALE_M".$m} = $SALE;
			${"P_SUM_PRFT_M".$m} = $PRFT;
		}
		$P_SUM_SALE_ALL = $P_RST['ALL_SALE'];
		$P_SUM_PRFT_ALL = $P_RST['ALL_PRFT'];
	}

	// ยอดขายปี ย้อนหลัง
	$Col = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "รวมยอดขายปี $P_YEAR");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
	$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextBold);
	$Col++;

	for($m = 1; $m <= 12; $m++) {
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, ((${"P_SUM_SALE_M".$m} == 0) ? "-" : ${"P_SUM_SALE_M".$m}));
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
		$Col++;
	}

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, (($P_SUM_SALE_ALL == 0) ? 0 : $P_SUM_SALE_ALL));
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode("#,##0");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
	$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
	$Col++;

	$Row++;
	//End ยอดขายปี ย้อนหลัง

	// % กำไรปี ย้อนหลัง
	$Col = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "% กำไรปี $P_YEAR");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
	$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextBold);
	$Col++;

	for($m = 1; $m <= 12; $m++) {
		$PCNT_PROFIT = (${"P_SUM_SALE_M".$m} <> 0) ? (${"P_SUM_PRFT_M".$m}/${"P_SUM_SALE_M".$m}) : 0 ;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, (($PCNT_PROFIT == 0) ? 0 : $PCNT_PROFIT));
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
		$Col++;
	}

	$PCNT_PROFIT = ($P_SUM_SALE_ALL <> 0) ? ($P_SUM_PRFT_ALL/$P_SUM_SALE_ALL) : 0 ;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, (($P_SUM_SALE_ALL == 0) ? 0 : $PCNT_PROFIT));
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
	$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRightBold);
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "");
	$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
	$Col++;

	$Row++;
	//End % กำไรปี ย้อนหลัง

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานยอดขายรายห้าง - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/SaleByStore/".$FileName);

	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'SaleByStore', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
	$arrCol['ExportStatus'] = "SUCCESS";
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == 'GetSO04') {
	if(intval($_POST['Month'])-1 == 0) {
		$Year = intval($_POST['Year'])-1;
		$pMonth = 12;
	}else{
		$Year = $_POST['Year'];
		$pMonth = intval($_POST['Month'])-1;
	}
	$StartDate = $Year."-".$pMonth."-26";
	$EndDate = $_POST['Year']."-".$_POST['Month']."-25";
	$SQL = 
		"SELECT
			T0.DocType, T0.DocDate, T0.DocNum, CONCAT(T0.BillCardCode,' ',T0.BillCardName) AS 'CardName',
			CONCAT(T1.uName,' ',T1.uLastName) AS 'CoName' , T0.BillSlpName,T3.MainTeam,
			CASE
			WHEN T0.DocType = 'A' THEN (SELECT P0.BillDocNum FROM sa04_detaila P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			WHEN T0.DocType = 'B' THEN (SELECT P0.BillDocNum FROM sa04_detailb P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			WHEN T0.DocType = 'C' THEN (SELECT GROUP_CONCAT(P0.BillDocNum) FROM sa04_detailc P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			ELSE 0 END AS 'BillDocNum',
			CASE
			WHEN T0.DocType = 'A' THEN (SELECT P0.BillDocDate FROM sa04_detaila P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			WHEN T0.DocType = 'B' THEN (SELECT P0.BillDocDate FROM sa04_detailb P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			WHEN T0.DocType = 'C' THEN NULL
			ELSE 0 END AS 'BillDocDate',
			CASE
			WHEN T0.DocRemark = 1 THEN 'เซลส์เสนอราคาผิด'
			WHEN T0.DocRemark = 2 THEN 'ลูกค้าขอราคาเดิม'
			WHEN T0.DocRemark = 3 THEN 'คู่แข่งขายถูกกว่า'
			ELSE T0.DocRemarkText END AS 'DocRemark',
			T0.FineSA, T0.FineCO, CASE WHEN T0.FineSA = 'N' AND T0.FineCO = 'N' THEN 'Y' ELSE 'N' END AS 'NoFine'
		FROM sa04_header T0
		LEFT JOIN users T1 ON T0.CreateUkey = T1.uKey
		LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
		LEFT JOIN oslp T3 ON T0.BillSlpName = T3.SlpName
		WHERE T0.CANCELED = 'N' AND DATE(T0.CreateDate) BETWEEN '$StartDate' AND '$EndDate'";
	$QRY = MySQLSelectX($SQL);
	$r = 0;
	$Data = "";
	while($RST = mysqli_fetch_array($QRY)) {
		$r++;
		$Data .= 
		"<tr>
			<td class='text-center align-baseline'>$r</td>
			<td class='text-center align-baseline'>".date("d/m/Y", strtotime($RST['DocDate']))."</td>
			<td class='text-center align-baseline'>".$RST['DocNum']."</td>
			<td class='align-baseline'>".$RST['CardName']."</td>
			<td class='align-baseline'>".$RST['CoName']."</td>
			<td class='align-baseline'>".$RST['BillSlpName']."</td>
			<td class='text-center align-baseline'>".str_replace(",",",<br>",$RST['BillDocNum'])."</td>
			<td class='text-center align-baseline'>".$RST['MainTeam']."</td>
			<td class='text-center align-baseline'>".date("d/m/Y", strtotime($RST['BillDocDate']))."</td>
			<td class='align-baseline'>".$RST['DocRemark']."</td>
			<td class='text-center align-baseline'>".$RST['FineSA']."</td>
			<td class='text-center align-baseline'>".$RST['FineCO']."</td>
			<td class='text-center align-baseline'>".$RST['NoFine']."</td>
		</tr>";
	}
	$arrCol['Row'] = $r;
	$arrCol['Data'] = $Data;
}

function StrCell($c) {
	$StrCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
	return $StrCell;
}

if($_GET['a'] == 'ExcelSO04') {
	if(intval($_POST['Month'])-1 == 0) {
		$Year = intval($_POST['Year'])-1;
		$pMonth = 12;
	}else{
		$Year = $_POST['Year'];
		$pMonth = intval($_POST['Month'])-1;
	}
	$StartDate = $Year."-".$pMonth."-26";
	$EndDate = $_POST['Year']."-".$_POST['Month']."-25";
	$SQL = 
		"SELECT
			T0.DocType, T0.DocDate, T0.DocNum, CONCAT(T0.BillCardCode,' ',T0.BillCardName) AS 'CardName',
			CONCAT(T1.uName,' ',T1.uLastName) AS 'CoName' , T0.BillSlpName,T3.MainTeam,
			CASE
			WHEN T0.DocType = 'A' THEN (SELECT P0.BillDocNum FROM sa04_detaila P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			WHEN T0.DocType = 'B' THEN (SELECT P0.BillDocNum FROM sa04_detailb P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			WHEN T0.DocType = 'C' THEN (SELECT GROUP_CONCAT(P0.BillDocNum) FROM sa04_detailc P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			ELSE 0 END AS 'BillDocNum',
			CASE
			WHEN T0.DocType = 'A' THEN (SELECT P0.BillDocDate FROM sa04_detaila P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			WHEN T0.DocType = 'B' THEN (SELECT P0.BillDocDate FROM sa04_detailb P0 WHERE P0.DocEntry = T0.DocEntry ORDER BY P0.TransID LIMIT 1)
			WHEN T0.DocType = 'C' THEN NULL
			ELSE 0 END AS 'BillDocDate',
			CASE
			WHEN T0.DocRemark = 1 THEN 'เซลส์เสนอราคาผิด'
			WHEN T0.DocRemark = 2 THEN 'ลูกค้าขอราคาเดิม'
			WHEN T0.DocRemark = 3 THEN 'คู่แข่งขายถูกกว่า'
			ELSE T0.DocRemarkText END AS 'DocRemark',
			T0.FineSA, T0.FineCO, CASE WHEN T0.FineSA = 'N' AND T0.FineCO = 'N' THEN 'Y' ELSE 'N' END AS 'NoFine'
		FROM sa04_header T0
		LEFT JOIN users T1 ON T0.CreateUkey = T1.uKey
		LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
		LEFT JOIN oslp T3 ON T0.BillSlpName = T3.SlpName
		WHERE T0.CANCELED = 'N' AND DATE(T0.CreateDate) BETWEEN '$StartDate' AND '$EndDate'";
	$QRY = MySQLSelectX($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานสรุป SA04 บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานสรุป SA04 บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	$PageHeader = [ 'font' => [ 'bold' => true, 'size' => 9.1 ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];

	$Row = 1; $Col = 1;

	$ArrHeader = ['No.', 'วันที่เอกสาร', 'เลขที่เอกสาร (SA-04)', 'ชื่อร้าน', 'Sales', 'Co-Sales', 'เลขที่บิล', 'ช่องทางการขาย', 'วันที่บิล', 'รายละเอียด', 'จำนวนที่ปรับ'];
	for($d = 0; $d < count($ArrHeader); $d++) {
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $ArrHeader[$d]);
		if($d != count($ArrHeader)-1) {
			$spreadsheet->getActiveSheet()->mergeCells(StrCell($Col).$Row.':'.StrCell($Col).($Row+1)); 
			$Col++;
		}else{
			$spreadsheet->getActiveSheet()->mergeCells(StrCell($Col).$Row.':'.StrCell(($Col+2)).$Row);
		}
	}
	$Row++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "Sales(100 บาท)");
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "Co-Sales(20 บาท)");
	$Col++;

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ไม่มีค่าปรับ");
	$Col = 1;

	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(6); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(20); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(50); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(25); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(30); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(18); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(20); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(40); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(17); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(17); $Col++;
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(17); $Col++;

	$sheet->getStyle('A1:M1')->applyFromArray($PageHeader);
	$sheet->getStyle('A2:M2')->applyFromArray($PageHeader);

	$r = 0;
	while($RST = mysqli_fetch_array($QRY)) {
		$Row++;
		$Col = 1;
		$r++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $r);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, date("d/m/Y", strtotime($RST['DocDate'])));
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['DocNum']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['CardName']);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['CoName']);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['BillSlpName']);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['BillDocNum']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['MainTeam']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, date("d/m/Y", strtotime($RST['BillDocDate'])));
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['DocRemark']);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['FineSA']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['FineCO']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['NoFine']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานสรุป SA04 - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/SA04/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'SA04', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
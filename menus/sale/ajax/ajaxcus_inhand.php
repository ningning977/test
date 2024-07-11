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

if($_GET['a'] == 'GetEmp') {
	switch($_SESSION['DeptCode']) {
		case "DP003":
		case "DP005":
		case "DP006":
		case "DP007":
		case "DP008": $EmpWhr = " AND ( (T1.DeptCode = '".$_SESSION['DeptCode']."' AND T1.UClass IN(18,19,20)) OR T0.uKey = '60336f75f5e6549c26d88d72745f67d0')"; break;
		default: $EmpWhr = " AND ( (T1.DeptCode IN ('DP003','DP005','DP006','DP007','DP008') AND T1.UClass IN (18,19,20))) OR T0.uKey = '60336f75f5e6549c26d88d72745f67d0' "; break;
	}

	$SQL = "
		SELECT
			T0.uKey, CONCAT(T0.uName,' ',T0.uLastName) AS 'EmpName', T0.uNickName, T0.LvCode, T1.UClass, T1.DeptCode, T2.DeptName
		FROM users T0
		LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
		LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
		WHERE T0.UserStatus = 'A' $EmpWhr
		ORDER BY T1.DeptCode ASC, T1.uClass ASC, T0.uName ASC";
	$QRY = MySQLSelectX($SQL);

	$Data = "";
	$TmpDeptCode = "";
	$TmpDeptName = "";
	while($result = mysqli_fetch_array($QRY)) {
		if($_SESSION['uClass'] == 20 && $result['uKey'] != $_SESSION['ukey']){ $disb = " disabled"; }else{ $disb = NULL; }
		if($result['uNickName'] == ""){ $nickname = NULL; }else{ $nickname = " (".$result['uNickName'].")";}
		if($_SESSION['ukey'] == $result['uKey']){ $slct = " selected"; }else{ $slct = NULL; }

		if($TmpDeptCode == "" || $TmpDeptCode != $result['DeptCode']) {
			if($TmpDeptCode != "") {
				$Data .= "</optgroup>";
			}
			$Data .= "<optgroup label='".$result['DeptName']."'>";
				$Data .= "<option value='".$result['uKey']."'$disb$slct>".$result['EmpName'].$nickname."</option>";
			$TmpDeptCode = $result['DeptCode'];
		} else {
				$Data .= "<option value='".$result['uKey']."'$disb$slct>".$result['EmpName'].$nickname."</option>";
		}
	}
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'CallData') {
	$Emp = $_POST['Emp'];
	$Year = $_POST['Year'];
	if(stripos($_POST['Cus'], 'ALL') !== false) {
		$Cus = "";
	}else{
		$Cus = "AND T1.GroupCode IN (".$_POST['Cus'].")";
	}
	$data = slpCodeData(null,$Emp);
	array_pop($data);
	$SlpCode = join("",$data);

	$SQL = "
		SELECT T0.CardCode,T0.CardName,T0.GroupCode,T1.GroupName,
			(SELECT SUM(A0.DocTotal-A0.VatSum) FROM OINV A0 WHERE A0.CardCode = T0.CardCode AND YEAR(A0.DocDate) = $Year) AS OINV2023,
			(SELECT SUM(X0.DocTotal-X0.VatSum) FROM ORIN X0 WHERE X0.CardCode = T0.CardCode AND YEAR(X0.DocDate) = $Year) AS ORIN2023,
			(SELECT SUM(B0.DocTotal-B0.VatSum) FROM [KBI_DB2022].[dbo].OINV B0 WHERE B0.CardCode = T0.CardCode AND YEAR(B0.DocDate) = ($Year-1)) AS OINV2022,
			(SELECT SUM(Y0.DocTotal-Y0.VatSum) FROM [KBI_DB2022].[dbo].ORIN Y0 WHERE Y0.CardCode = T0.CardCode AND YEAR(Y0.DocDate) = ($Year-1)) AS ORIN2022,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 1)) AS IV1,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 1)) AS RN1,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 2)) AS IV2,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 2)) AS RN2,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 3)) AS IV3,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 3)) AS RN3,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 4)) AS IV4,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 4)) AS RN4,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 5)) AS IV5,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 5)) AS RN5,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 6)) AS IV6,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 6)) AS RN6,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 7)) AS IV7,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 7)) AS RN7,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 8)) AS IV8,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 8)) AS RN8,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 9)) AS IV9,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 9)) AS RN9,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 10)) AS IV10,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 10)) AS RN10,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 11)) AS IV11,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 11)) AS RN11,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 12)) AS IV12,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 12)) AS RN12
		FROM OCRD T0
		LEFT JOIN OCRG T1 ON T0.GroupCode = T1.GroupCode 
		WHERE T0.SlpCode IN $SlpCode $Cus
		ORDER BY (SELECT SUM(A0.DocTotal-A0.VatSum) FROM OINV A0 WHERE A0.CardCode = T0.CardCode AND YEAR(A0.DocDate) = $Year) DESC";
	$QRY = SAPSelect($SQL);
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['CardCode'] = $result['CardCode'];
		$arrCol[$r]['CardName'] = conutf8($result['CardName']);
		$arrCol[$r]['GroupName']= conutf8($result['GroupName']);
		$SQL2 = "SELECT CardCode,CusTarget FROM custarget WHERE TgrStatus = 'A' AND DocYear = ".$Year."  AND CardCode IN ('".$result['CardCode']."') LIMIT 1";
		$Tgr = MySQLSelect($SQL2);
		$CusTarget = 0;
		if(isset($Tgr['CusTarget'])) {
			$CusTarget = $Tgr['CusTarget'];
			$arrCol[$r]['TarYear']  = number_format($Tgr['CusTarget'],0);
			$arrCol[$r]['TarMonth'] = number_format(($Tgr['CusTarget']/12),0);
		}else{
			$arrCol[$r]['TarYear']  = '-';
			$arrCol[$r]['TarMonth'] = '-';
		}
		
		if(($result['OINV2023'] - $result['ORIN2023']) != 0) {
			if(($result['OINV2023'] - $result['ORIN2023']) > $CusTarget && $CusTarget != 0) {
				$arrCol[$r]['cTotal'] = "<span class='text-success'>".number_format(($result['OINV2023'] - $result['ORIN2023']),0)."</span>";
			}else{
				$arrCol[$r]['cTotal'] = number_format(($result['OINV2023'] - $result['ORIN2023']),0);
			}
		}else{
			$arrCol[$r]['cTotal'] = "-";
		}
		if(($result['OINV2022'] - $result['ORIN2022']) != 0) {
			$arrCol[$r]['pTotal'] = number_format(($result['OINV2022'] - $result['ORIN2022']),0);
		}else{
			$arrCol[$r]['pTotal'] = "-";
		}
		for($m = 1; $m <= 12; $m++) {
			if(($result['IV'.$m] - $result['RN'.$m]) != 0) {
				$arrCol[$r]['M'.$m] = number_format(($result['IV'.$m] - $result['RN'.$m]),0);
			}else{
				$arrCol[$r]['M'.$m] = "-";
			}
		}
		$r++;
	}
}

if($_GET['a'] == 'Export') {
	$Year = $_POST['Year'];
	$Emp = $_POST['Emp'];
	if(stripos($_POST['Cus'], 'ALL') !== false) {
		$Cus = "";
	}else{
		$Cus = "AND T1.GroupCode IN (".$_POST['Cus'].")";
	}
	$data = slpCodeData(null,$Emp);
	array_pop($data);
	$SlpCode = join("",$data);

	$SQL = "
		SELECT T0.CardCode,T0.CardName,T0.GroupCode,T1.GroupName,
			(SELECT SUM(A0.DocTotal-A0.VatSum) FROM OINV A0 WHERE A0.CardCode = T0.CardCode AND YEAR(A0.DocDate) = $Year) AS OINV2023,
			(SELECT SUM(X0.DocTotal-X0.VatSum) FROM ORIN X0 WHERE X0.CardCode = T0.CardCode AND YEAR(X0.DocDate) = $Year) AS ORIN2023,
			(SELECT SUM(B0.DocTotal-B0.VatSum) FROM [KBI_DB2022].[dbo].OINV B0 WHERE B0.CardCode = T0.CardCode AND YEAR(B0.DocDate) = ($Year-1)) AS OINV2022,
			(SELECT SUM(Y0.DocTotal-Y0.VatSum) FROM [KBI_DB2022].[dbo].ORIN Y0 WHERE Y0.CardCode = T0.CardCode AND YEAR(Y0.DocDate) = ($Year-1)) AS ORIN2022,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 1)) AS IV1,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 1)) AS RN1,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 2)) AS IV2,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 2)) AS RN2,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 3)) AS IV3,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 3)) AS RN3,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 4)) AS IV4,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 4)) AS RN4,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 5)) AS IV5,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 5)) AS RN5,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 6)) AS IV6,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 6)) AS RN6,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 7)) AS IV7,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 7)) AS RN7,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 8)) AS IV8,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 8)) AS RN8,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 9)) AS IV9,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 9)) AS RN9,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 10)) AS IV10,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 10)) AS RN10,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 11)) AS IV11,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 11)) AS RN11,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM OINV C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 12)) AS IV12,
			(SELECT SUM(C0.DocTotal-C0.VatSum) FROM ORIN C0 WHERE C0.CardCode = T0.CardCode AND (YEAR(C0.DocDate) = $Year AND MONTH(C0.DocDate) = 12)) AS RN12
		FROM OCRD T0
		LEFT JOIN OCRG T1 ON T0.GroupCode = T1.GroupCode 
		WHERE T0.SlpCode IN $SlpCode $Cus
		ORDER BY (SELECT SUM(A0.DocTotal-A0.VatSum) FROM OINV A0 WHERE A0.CardCode = T0.CardCode AND YEAR(A0.DocDate) = $Year) DESC";
	$QRY = SAPSelect($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานลูกค้าในมือ บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานลูกค้าในมือ บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// Header
	$sheet->setCellValue('A1',"รหัสลูกค้า");
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"ชื่อลูกค้า");
	$spreadsheet->getActiveSheet()->mergeCells('B1:B2');
	$sheet->setCellValue('C1',"กลุ่มลูกค้า");
	$spreadsheet->getActiveSheet()->mergeCells('C1:C2');
	$sheet->setCellValue('D1',"เป้าขายต่อปี");
	$spreadsheet->getActiveSheet()->mergeCells('D1:D2');
	$sheet->setCellValue('E1',"เป้าขายต่อเดือน");
	$spreadsheet->getActiveSheet()->mergeCells('E1:E2');
	$sheet->setCellValue('F1',"ยอดรวมปัจจุบัน ".$Year);
	$spreadsheet->getActiveSheet()->mergeCells('F1:F2');
	$sheet->setCellValue('G1',"ยอดรวมปีที่แล้ว ".($Year-1));
	$spreadsheet->getActiveSheet()->mergeCells('G1:G2');
	$sheet->setCellValue('H1',"ยอดขายปี ".$Year);
	$spreadsheet->getActiveSheet()->mergeCells('H1:S1');
	$mCell = ['0','H','I','J','K','L','N','M','O','P','Q','R','S'];
	for($m = 1; $m <= 12; $m++) {
		$sheet->setCellValue($mCell[$m].'2',FullMonth($m));
	}
	// Add Style Header
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->getStyle('A1:S1')->applyFromArray($PageHeader);
	$sheet->getStyle('H2:S2')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(13);
	for($m = 1; $m <= 12; $m++) {
		$spreadsheet->getActiveSheet()->getColumnDimension($mCell[$m])->setWidth(13);
	}
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);

	// Style Body
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$Row = 2;
	while($result = odbc_fetch_array($QRY)) {
		$Row++;
		// รหัสลูกค้า
		$sheet->setCellValue('A'.$Row,$result['CardCode']);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);
		// ชื่อลูกค้า
		$sheet->setCellValue('B'.$Row,conutf8($result['CardName']));
		// กลุ่มลูกค้า
		$sheet->setCellValue('C'.$Row,conutf8($result['GroupName']));
		$SQL2 = "SELECT CardCode,CusTarget FROM custarget WHERE TgrStatus = 'A' AND DocYear = ".$Year."  AND CardCode IN ('".$result['CardCode']."') LIMIT 1";
		$Tgr = MySQLSelect($SQL2);
		// เป้าขายต่อปี, เป้าขายต่อเดือน
		if(isset($Tgr['CusTarget'])) {
			$sheet->setCellValue('D'.$Row,$Tgr['CusTarget']);
			$sheet->setCellValue('E'.$Row,($Tgr['CusTarget']/12));
			$spreadsheet->getActiveSheet()->getStyle('D'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
			$spreadsheet->getActiveSheet()->getStyle('E'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		}else{
			$sheet->setCellValue('D'.$Row,'-');
			$sheet->setCellValue('E'.$Row,'-');
		}
		$sheet->getStyle('D'.$Row)->applyFromArray($TextRight);
		$sheet->getStyle('E'.$Row)->applyFromArray($TextRight);
		// ยอดรวมปัจจุบัน
		if(($result['OINV2023'] - $result['ORIN2023']) != 0) {
			$sheet->setCellValue('F'.$Row,($result['OINV2023'] - $result['ORIN2023']));
			$spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		}else{
			$sheet->setCellValue('F'.$Row,'-');
		}
		$sheet->getStyle('F'.$Row)->applyFromArray($TextRight);
		// ยอดรวมปีที่แล้ว
		if(($result['OINV2022'] - $result['ORIN2022']) != 0) {
			$sheet->setCellValue('G'.$Row,($result['OINV2022'] - $result['ORIN2022']));
			$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		}else{
			$sheet->setCellValue('G'.$Row,'-');
		}
		$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);
		// ยอดเดือน 1 - 12
		for($m = 1; $m <= 12; $m++) {
			if(($result['IV'.$m] - $result['RN'.$m]) != 0) {
				$sheet->setCellValue($mCell[$m].$Row,($result['IV'.$m] - $result['RN'.$m]));
				$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
			}else{
				$sheet->setCellValue($mCell[$m].$Row,'-');
			}
			$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
		}
	}
	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานลูกค้าในมือ - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/CusInHand/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'CusInHand', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

// $arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if($_GET['a'] == "GetSaleName") {
	$this_year = $_POST['Year'];
	$SQL = "SELECT LogCode FROM saletarget WHERE DocYear = $this_year AND DocStatus != 'I' AND Ukey = '".$_SESSION['ukey']."'";
	$LogCode = MySQLSelect($SQL);
	$onl = 0;
	switch($_SESSION['DeptCode']){
		case 'DP005' :
			$dept = " ('TT2') ";
			break;
		case 'DP006' :
			$dept = " ('MT1') ";
			break;
		case 'DP007' :
			$dept = " ('MT2') ";
			break;
		case 'DP008' :
			$dept = " ('OUL') ";
			break;
		case 'DP001' :
		case 'DP002' :
		case 'DP003' :
		case 'DP009' :
			$dept = " ('MT1','MT2','TT2','OUL') ";
			$onl = 1;
			break;
	}
	$SlpSQL = "SELECT DISTINCT
					T0.LogCode, T1.MainTeam, T0.TeamCode, T0.Ukey, T2.uName, T2.uLastName, T2.uNickName,
					CASE
						WHEN T0.TeamCode LIKE 'EXP%' THEN 'ต่างประเทศ'
						WHEN T0.TeamCode = 'MT201' THEN 'โฮมโปร (ฝากขาย)'
						WHEN T0.TeamCode = 'MT202' THEN 'เมกาโฮม (ฝากขาย)'
						WHEN T0.TeamCode = 'MT203' THEN 'ไทวัสดุ (ฝากขาย)'
						WHEN T0.TeamCode = 'TT203' THEN 'ประเทศลาว' 
					ELSE NULL END AS 'Locate', 
					CASE
                        WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo01' THEN 'โฮมโปร - ฝากขาย'
                        WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo02' THEN 'ไทวัสดุ - ฝากขาย'
                        WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo03' THEN 'เมกาโฮม - ฝากขาย'
                        WHEN T0.Ukey = 'a82726eeff10f11797ed9fde004e701a' THEN 'จีรศักดิ์ (ซ่อมหน้าร้าน)'
                    ELSE CONCAT(T2.uName,' (',T2.uNickName,')') END AS 'SlpName',
					T0.DocStatus
				FROM saletarget T0
				LEFT JOIN teamcode T1 ON T0.TeamCode = T1.TeamCode
				LEFT JOIN users T2 ON T0.Ukey = T2.uKey
				WHERE T0.DocYear = $this_year AND T1.MainTeam IN $dept AND T0.DocStatus != 'I'
				ORDER BY
				CASE
					WHEN T0.TeamCode LIKE 'MT1%' THEN 1
					WHEN T0.TeamCode LIKE 'EXP%' THEN 2
					WHEN T0.TeamCode LIKE 'MT2%' THEN 3
					WHEN T0.TeamCode LIKE 'TT2%' THEN 4
					WHEN T0.TeamCode LIKE 'TT1%' THEN 5
					WHEN T0.TeamCode LIKE 'OUL%' THEN 6
				ELSE 7 END, T0.TeamCode, T2.uName";
	$SlpQRY = MySQLSelectX($SlpSQL);
	$tempteam = "";
	$Log = "N";
	$output .= "<option value='NULL' selected disabled>กรุณาเลือกทีมหรือพนักงานขาย</option>";
	while($SlpRST = mysqli_fetch_array($SlpQRY)) {
		if($SlpRST['uName'] == "" && $SlpRST['uLastName'] == "" && $SlpRST['uNickName'] == "") {
			$fullname = $SlpRST['SlpName'];
		}else{
			$fullname = $SlpRST['uName']." ".$SlpRST['uLastName'];
			if($SlpRST['uNickName'] != "") {
				$fullname = $fullname." (".$SlpRST['uNickName'].")";
			}
	
			if($SlpRST['Locate'] != "") {
				$fullname = $fullname." (".$SlpRST['Locate'].")";
			}
		}


		if($tempteam != $SlpRST['MainTeam']) {
			if($tempteam != "") {
				$output .= "</optgroup>";
			}
			$output .= "<optgroup label='".SATeamName($SlpRST['MainTeam'])."'>";
				$output .= "<option value='T-".$SlpRST['MainTeam']."'>รวม".SATeamName($SlpRST['MainTeam'])." ทั้งหมด</option>";
				$output .= "<option value='".$SlpRST['LogCode']."'>".$fullname."</option>";
			$tempteam = $SlpRST['MainTeam'];
		} else {
			$output .= "<option value='".$SlpRST['LogCode']."'>".$fullname."</option>";
		}

		if(isset($LogCode['LogCode'])) {
			if($LogCode['LogCode'] == $SlpRST['LogCode']) {
				$Log = $LogCode['LogCode'];
			}
		}
	}
	if ($onl == 1){
		$output .= "</optgroup>";
		$output .= "<optgroup label='".SATeamName("ONL")."'>";
			$output .= "<option value='T-ONL'>รวม".SATeamName("ONL")." ทั้งหมด</option>";
		$output .= "</optgroup>";
	}

	$arrCol['output'] = $output;
	$arrCol['LogCode'] = $Log;
}

if($_GET['a'] == 'CallData') {
	$SelectSlpCode = $_POST['SlpCode'];
	$cYear = $_POST['Year'];
	$cMonth = $_POST['Month'];

	if (substr($SelectSlpCode,0,1) == 'T') {
		$Team = substr($SelectSlpCode,2);
		$sqlTeam = "";
		if($Team == 'MT1') {
			$sqlTeam = "(TeamCode LIKE '$Team%' OR TeamCode LIKE 'EXP%')";
		}else{
			$sqlTeam = "TeamCode LIKE '$Team%'";
		}
		$SQL1 = "SELECT * FROM saletarget WHERE DocYear = '$cYear' AND DocStatus != 'I' AND $sqlTeam";
		$QRY1 = MySQLSelectX($SQL1);
		$u = 0;
		while($result1 = mysqli_fetch_array($QRY1)) {
			$u++;
			$Ukey[$u] = $result1['Ukey'];
		}
		$SlpCode = "";
		for($r = 1; $r <= $u; $r++) {
			$SlpC = slpCodeData(NULL,$Ukey[$r]);
			$SlpCode .= substr($SlpC['SlpCode'],1,-1).",";
		}
		$SlpCode = substr($SlpCode,0,-1);
	}else{
		$SQL1 = "SELECT * FROM saletarget WHERE DocYear = '$cYear' AND DocStatus != 'I' AND LogCode = '$SelectSlpCode'";
		$QRY1 = MySQLSelectX($SQL1);
		$u = 0;
		while($result1 = mysqli_fetch_array($QRY1)) {
			$u++;
			$Ukey[$u] = $result1['Ukey'];
		}
		$SlpCode = "";
		for($r = 1; $r <= $u; $r++) {
			$SlpC = slpCodeData(NULL,$Ukey[$r]);
			$SlpCode .= substr($SlpC['SlpCode'],1,-1).",";
		}
		$SlpCode = substr($SlpCode,0,-1);
	}

	$SQL = "
		SELECT P0.*,P1.U_PONo 
		FROM ( 
			SELECT 
				T0.DocDate,T0.NumAtCard,T0.CardCode,T0.CardName,(T0.DocTotal-T0.VatSum) AS DocTotal,T1.SlpName,
				(SELECT TOP 1 A0.DocEntry FROM RDR1 A0 WHERE A0.TrgetEntry = T0.DocEntry AND A0.TrgetEntry IS NOT NULL ) AS SO   
			FROM OINV T0
			LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode 
			WHERE (MONTH(T0.DocDate)= $cMonth AND YEAR(T0.DocDate) = $cYear) AND T1.SlpCode IN ($SlpCode)
			UNION ALL
			SELECT 
				T0.DocDate,T0.NumAtCard,T0.CardCode,T0.CardName,-1*(T0.DocTotal-T0.VatSum) AS DocTotal,T1.SlpName,'' AS SO 
			FROM ORIN T0
			LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode 
			WHERE (MONTH(T0.DocDate)= $cMonth AND YEAR(T0.DocDate) = $cYear) AND T1.SlpCode IN ($SlpCode)
		) P0
		LEFT JOIN ORDR P1 ON P0.SO = P1.DocEntry
		ORDER BY P0.DocDate,P0.NumAtCard";
	$QRY = SAPSelect($SQL);
	// echo $SQL;
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['DocNum']   = $result['NumAtCard'];
		$arrCol[$r]['DocDate']  = date("d/m/Y",strtotime($result['DocDate']));
		$arrCol[$r]['CardCode'] = $result['CardCode'];
		$arrCol[$r]['CardName'] = conutf8($result['CardName']);
		$arrCol[$r]['SlpName']  = conutf8($result['SlpName']);
		$arrCol[$r]['DocTotal'] = number_format($result['DocTotal'],2);
		$arrCol[$r]['PO']       = conutf8($result['U_PONo']);
		$r++;
	}
}

if($_GET['a'] == 'Excel') {
	$SelectSlpCode = $_POST['SlpCode'];
	$cYear = $_POST['Year'];
	$cMonth = $_POST['Month'];

	if (substr($SelectSlpCode,0,1) == 'T') {
		$Team = substr($SelectSlpCode,2);
		$sqlTeam = "";
		if($Team == 'MT1') {
			$sqlTeam = "(TeamCode LIKE '$Team%' OR TeamCode LIKE 'EXP%')";
		}else{
			$sqlTeam = "TeamCode LIKE '$Team%'";
		}
		$SQL1 = "SELECT * FROM saletarget WHERE DocYear = '$cYear' AND DocStatus != 'I' AND $sqlTeam";
		$QRY1 = MySQLSelectX($SQL1);
		$u = 0;
		while($result1 = mysqli_fetch_array($QRY1)) {
			$u++;
			$Ukey[$u] = $result1['Ukey'];
		}
		$SlpCode = "";
		for($r = 1; $r <= $u; $r++) {
			$SlpC = slpCodeData(NULL,$Ukey[$r]);
			$SlpCode .= substr($SlpC['SlpCode'],1,-1).",";
		}
		$SlpCode = substr($SlpCode,0,-1);
	}else{
		$SQL1 = "SELECT * FROM saletarget WHERE DocYear = '$cYear' AND DocStatus != 'I' AND LogCode = '$SelectSlpCode'";
		$QRY1 = MySQLSelectX($SQL1);
		$u = 0;
		while($result1 = mysqli_fetch_array($QRY1)) {
			$u++;
			$Ukey[$u] = $result1['Ukey'];
		}
		$SlpCode = "";
		for($r = 1; $r <= $u; $r++) {
			$SlpC = slpCodeData(NULL,$Ukey[$r]);
			$SlpCode .= substr($SlpC['SlpCode'],1,-1).",";
		}
		$SlpCode = substr($SlpCode,0,-1);
	}
	$SQL = "
		SELECT P0.*,P1.U_PONo 
		FROM ( 
			SELECT 
				T0.DocDate,T0.NumAtCard,T0.CardCode,T0.CardName,(T0.DocTotal-T0.VatSum) AS DocTotal,T1.SlpName,
				(SELECT TOP 1 A0.DocEntry FROM RDR1 A0 WHERE A0.TrgetEntry = T0.DocEntry AND A0.TrgetEntry IS NOT NULL ) AS SO   
			FROM OINV T0
			LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode 
			WHERE (MONTH(T0.DocDate)= $cMonth AND YEAR(T0.DocDate) = $cYear) AND T1.SlpCode IN ($SlpCode)
			UNION ALL
			SELECT 
				T0.DocDate,T0.NumAtCard,T0.CardCode,T0.CardName,-1*(T0.DocTotal-T0.VatSum) AS DocTotal,T1.SlpName,'' AS SO 
			FROM ORIN T0
			LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode 
			WHERE (MONTH(T0.DocDate)= $cMonth AND YEAR(T0.DocDate) = $cYear) AND T1.SlpCode IN ($SlpCode)
		) P0
		LEFT JOIN ORDR P1 ON P0.SO = P1.DocEntry
		ORDER BY P0.DocDate,P0.NumAtCard";
	$QRY = SAPSelect($SQL);

	// HEADER 
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานการขาย บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานการขาย บจ.คิงบางกอก อินเตอร์เทรด");
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
	$sheet->setCellValue('B1',"วันที่");
	$sheet->setCellValue('C1',"รหัสลูกค้า");
	$sheet->setCellValue('D1',"ชื่อลูกค้า");
	$sheet->setCellValue('E1',"พนักงานขาย");
	$sheet->setCellValue('F1',"ยอดขาย (บาท)");
	$sheet->setCellValue('G1',"เลขที่ PO");

	$sheet->getStyle('A1:G1')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(11);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);

	$Row = 1;
	while($result = odbc_fetch_array($QRY)) {
		$Row++;
		// เลขที่เอกสาร
		$sheet->setCellValue('A'.$Row,$result['NumAtCard']);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);
		// วันที่
		$sheet->setCellValue('B'.$Row,date("d/m/Y",strtotime($result['DocDate'])));
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);
		// รหัสลูกค้า
		$sheet->setCellValue('C'.$Row,$result['CardCode']);
		$sheet->getStyle('C'.$Row)->applyFromArray($TextCenter);
		// ชื่อลูกค้า
		$sheet->setCellValue('D'.$Row,conutf8($result['CardName']));
		// พนักงานขาย
		$sheet->setCellValue('E'.$Row,conutf8($result['SlpName']));
		// ยอดขาย (บาท)
		$sheet->setCellValue('F'.$Row,$result['DocTotal']);
		$sheet->getStyle('F'.$Row)->applyFromArray($TextRight);
		$spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		// เลขที่ PO
		$sheet->setCellValue('G'.$Row,conutf8($result['U_PONo']));
	}
	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานการขาย - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/OINVList/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'OINVList', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
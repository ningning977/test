<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();

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
	$Year = $_POST['Year'];
	$Team = $_POST['Team'];
	$ItemCode = $_POST['ItemCode'];

	switch($Team) {
		case "OUL": $TeamSQL = "T2.U_Dim1 IN ('OUL','TT1')"; break;
		default:    $TeamSQL = "T2.U_Dim1 = '$Team'"; break;
	}

	$SQL = "
		SELECT
			A0.ItemCode, A0.Memo,
			SUM(A0.M_01) AS 'M_1', SUM(A0.M_02) AS 'M_2', SUM(A0.M_03) AS 'M_3',
			SUM(A0.M_04) AS 'M_4', SUM(A0.M_05) AS 'M_5', SUM(A0.M_06) AS 'M_6',
			SUM(A0.M_07) AS 'M_7', SUM(A0.M_08) AS 'M_8', SUM(A0.M_09) AS 'M_9',
			SUM(A0.M_10) AS 'M_10', SUM(A0.M_11) AS 'M_11', SUM(A0.M_12) AS 'M_12'
		FROM (
			SELECT
				T0.ItemCode, T2.Memo,
				CASE WHEN MONTH(T1.Docdate) = 1 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_01',
				CASE WHEN MONTH(T1.Docdate) = 2 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_02',
				CASE WHEN MONTH(T1.Docdate) = 3 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_03',
				CASE WHEN MONTH(T1.Docdate) = 4 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_04',
				CASE WHEN MONTH(T1.Docdate) = 5 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_05',
				CASE WHEN MONTH(T1.Docdate) = 6 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_06',
				CASE WHEN MONTH(T1.Docdate) = 7 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_07',
				CASE WHEN MONTH(T1.Docdate) = 8 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_08',
				CASE WHEN MONTH(T1.Docdate) = 9 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_09',
				CASE WHEN MONTH(T1.Docdate) = 10 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_10',
				CASE WHEN MONTH(T1.Docdate) = 11 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_11',
				CASE WHEN MONTH(T1.Docdate) = 12 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_12'
			FROM INV1 T0
			LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
			WHERE (T1.CANCELED = 'N' AND YEAR(T1.DocDate) = $Year AND $TeamSQL) AND T0.ItemCode = '$ItemCode'
			GROUP BY T0.ItemCode, T1.DocDate, T2.Memo 
			UNION ALL 
			SELECT
				T0.ItemCode, T2.Memo,
				CASE WHEN MONTH(T1.Docdate) = 1 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_01',
				CASE WHEN MONTH(T1.Docdate) = 2 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_02',
				CASE WHEN MONTH(T1.Docdate) = 3 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_03',
				CASE WHEN MONTH(T1.Docdate) = 4 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_04',
				CASE WHEN MONTH(T1.Docdate) = 5 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_05',
				CASE WHEN MONTH(T1.Docdate) = 6 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_06',
				CASE WHEN MONTH(T1.Docdate) = 7 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_07',
				CASE WHEN MONTH(T1.Docdate) = 8 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_08',
				CASE WHEN MONTH(T1.Docdate) = 9 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_09',
				CASE WHEN MONTH(T1.Docdate) = 10 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_10',
				CASE WHEN MONTH(T1.Docdate) = 11 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_11',
				CASE WHEN MONTH(T1.Docdate) = 12 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_12'
			FROM RIN1 T0
			LEFT JOIN ORIN T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
			WHERE (T1.CANCELED = 'N' AND YEAR(T1.DocDate) = $Year AND $TeamSQL) AND T0.ItemCode = '$ItemCode'
			GROUP BY T0.ItemCode, T1.DocDate, T2.Memo 
		) A0
		GROUP BY A0.ItemCode, A0.Memo";
	$QRY = SAPSelect($SQL);
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$SlpName = MySQLSelect("SELECT CONCAT(uName, ' ',uLastName, ' (', uNickName,')') AS FullName FROM users WHERE uKey = '".$result['Memo']."'");
		$arrCol[$r]['SlpName'] = $SlpName['FullName'];
		$Sum = 0;
		for($m = 1; $m <= 12; $m++) {
			if($m <= date("m")) {
				$arrCol[$r]['M_'.$m] = number_format($result['M_'.$m],0);
			}else{
				$arrCol[$r]['M_'.$m] = "-";
			}
			$Sum = $Sum+$result['M_'.$m];
		}
		$arrCol[$r]['Sum'] = number_format($Sum,0)." ตัว";
		$r++;
	}
}


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
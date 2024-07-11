<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
	exit;
}

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
$resultArray = array();
$arrCol = array();
$output = "";

if($_GET['a'] == 'Updatestatus'){
	$year = date("Y");
	$month = date("m");
	$last = cal_days_in_month(CAL_GREGORIAN,$month,$year);
	$sql = "SELECT
				A0.*,
				CONCAT('UPDATE picker_soheader SET StatusDoc = ',CASE WHEN A0.Loaded > 0 THEN 12 ELSE 11 END,' WHERE ID = ',A0.IDPick,';') AS 'NewStatus'
			FROM (
				SELECT
				T0.ID, T0.IDPick, T0.BillEntry, T0.BillType, T0.DocNum, T0.CardCode, T2.CardName, T0.DateCreate, T0.DateFinish, T1.DocDueDate, T1.StatusDoc,
				(SELECT DISTINCT COUNT(P0.LogiNum) FROM logi_detail P0 WHERE P0.BillEntry = T0.BillEntry AND P0.BillType = T0.BillType) AS 'Loaded'
				FROM pack_header T0
				LEFT JOIN picker_soheader T1 ON T0.IDPick = T1.ID
				LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode 
				WHERE (DATE(T0.DateCreate) BETWEEN '$year-$month-01' AND '$year-$month-$last' AND DATE(T0.DateFinish) != '0000-00-00') AND (T1.StatusDoc = 10 AND T0.Status = 'Y')
			) A0";

	$qry = MySQLSelectX($sql);
	$data =""; $r = 0;
	while($RST = mysqli_fetch_array($qry)){
		$r++;
		MySQLUpdate($RST['NewStatus']);
		$data .=
		"<tr>
			<td class = 'text-center'>$r</td>
			<td class = 'text-center'>".$RST['DocNum']."</td>
			<td>".$RST['CardCode']."".$RST['CardName']."</td>
			<td class = 'text-center'>".date("d/m/Y",strtotime($RST['DateCreate']))."</td>
			<td class = 'text-center'>".date("d/m/Y",strtotime($RST['DateFinish']))."</td>
			<td class = 'text-center'>".date("d/m/Y",strtotime($RST['DocDueDate']))."</td>
			<td class = 'text-center'>".$RST['StatusDoc']."</td>
			<td class = 'text-center'>11</td>
		</tr>";
	}
	$arrCol['status'] = 'อัปเดตสำเร็จแล้ว';
	$arrCol['data'] = $data;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
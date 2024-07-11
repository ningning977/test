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
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == 'CallData') {
	$SQL = "
		SELECT T0.DocEntry, CONCAT(T3.BeginStr, T0.DocNum) AS DocNum, T0.DocDate, T0.CardCode, T0.CardName, T1.SlpName, (T0.DocTotal - T0.VatSum) AS 'Total', CONCAT(T2.lastName, ' ',T2.firstName) AS ShipName, T0.Comments
		FROM ODLN T0
		LEFT JOIN OSLP T1 ON T1.SlpCode = T0.SlpCode
		LEFT JOIN OHEM T2 ON T2.empID = T0.OwnerCode  
		LEFT JOIN NNM1 T3 ON T3.Series = T0.Series 
		LEFT JOIN OUSR T4 ON T4.USERID = T0.UserSign 
		WHERE T0.DocStatus = 'O' AND T3.BeginStr IN ('PA-','PB-','PC-','PD-','PF-')";
	$QRY = SAPSelect($SQL);
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['DocNum']   = "<a href='javascript:void(0);' onclick='Detail(".$result['DocEntry'].",\"".$result['DocNum']."\")'>".$result['DocNum']."</a>";
		$arrCol[$r]['DocDate']  = date("d/m/Y",strtotime($result['DocDate']));
		$arrCol[$r]['CardCode'] = $result['CardCode'];
		$arrCol[$r]['CardName'] = conutf8($result['CardName']);
		$arrCol[$r]['SlpName']  = conutf8($result['SlpName']);
		$arrCol[$r]['Total']    = number_format($result['Total'],2);
		$arrCol[$r]['ShipName'] = conutf8($result['ShipName']);
		$arrCol[$r]['Comments'] = conutf8($result['Comments']);
		$r++;
	}
}

if($_GET['a'] == 'Detail') {
	$DocEntry = $_POST['DocEntry'];
	$SQL = "SELECT T0.ItemCode, T0.CodeBars, T0.Dscription, T0.WhsCode, T0.Quantity, T0.unitMsr FROM DLN1 T0 WHERE T0.DocEntry = $DocEntry";
	$QRY = SAPSelect($SQL);
	$Data = ""; $No = 0;
	while($result = odbc_fetch_array($QRY)) {
		$No++;
		$Data .="<tr>
					<td class='text-center'>".$No."</td>
					<td class='text-center'>".$result['ItemCode']."</td>
					<td class='text-center'>".$result['CodeBars']."</td>
					<td>".conutf8($result['Dscription'])."</td>
					<td class='text-center'>".$result['WhsCode']."</td>
					<td class='text-right'>".number_format($result['Quantity'],0)."</td>
					<td>".conutf8($result['unitMsr'])."</td>
				</tr>";
	}
	$arrCol['Data'] = $Data;
}

// $arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if($_GET['a'] == 'GetItemData') {
	$ItemCode = $_POST['ItemCode'];
	$SQL = "SELECT ItemCode, ItemName FROM oitm WHERE ItemCode LIKE '$ItemCode%' ORDER BY ItemCode";
	$QRY = MySQLSelectX($SQL);
	$Data = ""; $r = 0;
	while($RST = mysqli_fetch_array($QRY)) {
		$Data .= 
		"<tr>
			<td class='text-center'>".$RST['ItemCode']."</td>
			<td>".$RST['ItemName']."</td>
			<td></td>
		</tr>";
		$r++;
	}

	$Data .= 
	"<tr>
		<td><input type='text' class='form-control form-control-sm text-center' name='NewItemCode' id='NewItemCode' value='$ItemCode-$r' readonly></td>
		<td><input type='text' class='form-control form-control-sm' name='NewItemName' id='NewItemName'></td>
		<td><button class='btn btn-sm btn-success' onclick='AddItemCode()'><i class='fas fa-plus'></i></button></td>
	</tr>";

	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'AddItemCode') {
	$ItemCode = $_POST['ItemCode'];
	$NewItemCode = $_POST['NewItemCode'];
	$NewItemName = $_POST['NewItemName'];
	$BomGroup = "";
	if(intval(substr($NewItemCode,-1)) == 1) {
		$SQL1 = "SELECT (T0.BomGroup)+1 AS 'BomGroup' FROM bomgroup T0 ORDER BY T0.BomGroup DESC LIMIT 1 OFFSET 0";
		$RST1 = MySQLSelect($SQL1);
		$BomGroup = $RST1['BomGroup'];
		$UPDATE1 = 
			"UPDATE oitm 
			SET IsBom = 0, 
				BomGroup = $BomGroup, 
				ItemMaster = '$ItemCode', 
				uKeyUpdate = '".$_SESSION['ukey']."', 
				DateUpdate = NOW()
			WHERE ItemCode = '$ItemCode'";
		MySQLUpdate($UPDATE1);
		$INSERT1 = 
			"INSERT INTO bomgroup 
			SET BomGroup = $BomGroup, 
				ItemCode = '$NewItemCode', 
				Qty = 1, 
				ItemStatus = 'A', 
				uKeyCreate = '".$_SESSION['ukey']."', 
				DateCreate = NOW()";
		MySQLInsert($INSERT1);
	}

	$SQL2 = "SELECT ProductStatus, DftWhsCode, bomGroup FROM oitm WHERE ItemCode = '$ItemCode'";
	$RST2 = MySQLSelect($SQL2);
	$ProductStatus = $RST2['ProductStatus'];
	$DftWhsCode = $RST2['DftWhsCode'];
	$BomGroup = ($BomGroup == "") ? $RST2['bomGroup'] : $BomGroup;

	$INSERT2 = 
		"INSERT INTO oitm 
		SET ItemCode = '$NewItemCode', 
			BarCode = '$NewItemCode', 
			ItemName = '$NewItemName',  
			ProductStatus = '$ProductStatus',  
			DftWhsCode = '$DftWhsCode',
			IsBom = 1, 
			bomGroup = $BomGroup, 
			ItemMaster = '$ItemCode', 
			uKeyCreate = '".$_SESSION['ukey']."', 
			DateCreate = NOW()";
	MySQLInsert($INSERT2);
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
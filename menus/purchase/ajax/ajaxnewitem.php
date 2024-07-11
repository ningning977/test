<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName']==NULL ){
	echo '<script>window.location="../../../../"</script>';
}
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if ($_GET['a'] == 'GetItemCode') {
	$sql = "SELECT TOP 1
			     T0.ItemCode, T0.CodeBars AS BarCode , T0.ItemName, T0.SalUnitMsr AS MgrUnit, T0.U_ProductStatus AS ProductStatus, T0.DfltWH AS DftWhsCode
			FROM OITM T0 
			WHERE ItemCode = '".$_POST['ItemCode']."'";
	$sqlQRY = SAPSelect($sql);
	$result = odbc_fetch_array($sqlQRY);
	if (!isset($result['ItemCode'])){
		$row = 0;
	}else{
		$BarCode = $result['BarCode'];
		$ItemName = conutf8($result['ItemName']);
		$MgrUnit = conutf8($result['MgrUnit']);
		$DftWhsCode = $result['DftWhsCode'];
		$ProductStatusSAP = $result['ProductStatus'];
		$row = 1;

		$sql2 = "SELECT T0.StatusCode, T0.StatusName FROM productstatus T0 WHERE T0.CodeActive = 'A'";
		$sqlQRY2 = MySQLSelectX($sql2);
		$ProductStatus = "";
		while ($result2 = mysqli_fetch_array($sqlQRY2)) {
			$ProductStatus .= "<option value='".$result2['StatusCode']."'>".$result2['StatusCode']." - ".$result2['StatusName']."</option>";
		}

		$arrCol['BarCode'] = $BarCode;
		$arrCol['ItemName'] = $ItemName;
		$arrCol['MgrUnit'] = $MgrUnit;
		$arrCol['ProductStatus'] = $ProductStatus;
		$arrCol['DftWhsCode'] = $DftWhsCode;
		$arrCol['ProductStatusSAP'] = $ProductStatusSAP;

		$sql3 = "SELECT T0.ItemCode FROM OITM T0 WHERE ItemCode = '".$_POST['ItemCode']."' LIMIT 1";
		$Chkrow = CHKRowDB($sql3);
		if($Chkrow == 0) {
			$arrCol['Chkrow'] = $Chkrow;
		}else{
			$arrCol['Chkrow'] = $Chkrow;
		}
	}
	$arrCol['row'] = $row;
}

if ($_GET['a'] == 'AddItemCode') {
	$sql = "SELECT T0.ItemCode FROM OITM T0 WHERE ItemCode = '".$_POST['ItemCode']."' LIMIT 1";
	$row = CHKRowDB($sql);
	if ($row == 0) {
		$insert = "INSERT INTO OITM 
				   SET ItemCode = '".$_POST['ItemCode']."',
				   	   BarCode = '".$_POST['BarCode']."',
					   BarCode2 = '".$_POST['BarCode2']."',
					   BarCode3 = '".$_POST['BarCode3']."',
				       ItemName = '".$_POST['ItemName']."',
					   ItemName2 = '".$_POST['ItemName2']."',
					   MgrUnit = '".$_POST['MgrUnit']."',
					   ProductStatus = '".$_POST['ProductStatus']."',
					   DftWhsCode = '".$_POST['DftWhsCode']."',
					   IsBom = '0' ,
					   BomGroup = '0',
					   ItemMaster = '',
					   UkeyCreate = '".$_SESSION['ukey']."',
					   DateCreate = NOW(),
					   UkeyUpdate = '',
					   DateUpdate = '',
					   ItemStatus = 'A'";
		$sqlQRYinsert = MySQLInsert($insert);
		$note = "เพิ่มข้อมูลสินค้าใน Eurox Force เสร็จสิ้น";
	}else{
		$update = "UPDATE OITM 
				   SET BarCode = '".$_POST['BarCode']."',
				   	   BarCode2 = '".$_POST['BarCode2']."',
					   BarCode3 = '".$_POST['BarCode3']."',
					   ItemName = '".$_POST['ItemName']."',
				       ItemName2 = '".$_POST['ItemName2']."',
					   MgrUnit = '".$_POST['MgrUnit']."',
					   ProductStatus = '".$_POST['ProductStatus']."',
					   DftWhsCode = '".$_POST['DftWhsCode']."',
					   UkeyUpdate = '".$_SESSION['ukey']."',
					   DateUpdate = NOW()
				   WHERE ItemCode = '".$_POST['ItemCode']."'";
		$sqlQRYupdate = MySQLUpdate($update);
		$note = "อับเดตข้อมูลสินค้าใน Eurox Force เสร็จสิ้น";
	}
	$arrCol['note'] = $note;
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
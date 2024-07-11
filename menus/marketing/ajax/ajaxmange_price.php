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

if ($_GET['a'] == 'GetPriceCode') {
	$sql = "SELECT ItemCode, ItemName, BarCode FROM OITM WHERE ItemCode != '' AND ItemCode NOT LIKE '%เก่า%' AND ItemCode NOT LIKE '%ZZ%' AND ProductStatus != ''";
	$sqlQRY = MySQLSelectX($sql);
	$ItemCode = "<option value='' selected disabled>กรุณากรอกรหัสสินค้า</option>";
	while ($result = mysqli_fetch_array($sqlQRY)) {
		$ItemCode .= "<option value='".$result['ItemCode']."'>".$result['ItemCode']." | ".$result['ItemName']." - ".$result['BarCode']."</option>";
	}
	$arrCol['ItemCode'] = $ItemCode;
}

if ($_GET['a'] == 'GetPriceList') {
	$sql = "SELECT T0.ItemCode, T0.P0, T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice, T1.ItemName, T1.BarCode 
			FROM pricelist T0
			LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
			WHERE T0.PriceType = '".$_POST['PriceType']."' AND T1.ProductStatus != '' AND T0.PriceStatus = 'A' 
				  AND T0.ItemCode NOT LIKE '%เก่า%' AND T0.ItemCode NOT LIKE '%ZZ%' AND T1.ItemName != ''
			ORDER BY T0.ItemCode";
	$sqlQRY = MySQLSelectX($sql);
	$ItemRow = "";
	$uClass = $_SESSION['uClass'];
	if ($uClass == 18 || $uClass == 0 || $uClass == 1 || $uClass == 2 || $uClass == 3 || $uClass == 4) { $ViewStatus = 1; }else{ $ViewStatus = 0; }
	$r = 0;
	if ($ViewStatus == 1) {
		
		while ($result = mysqli_fetch_array($sqlQRY)) {
			$arrCol[$r]['ItemCode'] = "<a href='javascript:void(0)' onclick=\"GetItemCode('".$result['ItemCode']."')\">".$result['ItemCode']."</a>";
			$arrCol[$r]['ItemName'] = $result['ItemName'];
			$arrCol[$r]['BarCode']  = $result['BarCode'];

			$sqlSAP =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc'
						FROM OITM T0 LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode WHERE T0.ItemCode = '".$result['ItemCode']."'";   
			$qrySAP = SAPSelect($sqlSAP);
			$resultSAP = odbc_fetch_array($qrySAP);
			$arrCol[$r]['LastPurPrc'] = number_format(0,3);
			$LastPurPrc = 0;
			if(isset($resultSAP['LastPurPrc'])) {
				if($resultSAP['LastPurPrc'] != null) {
					$arrCol[$r]['LastPurPrc'] = number_format($resultSAP['LastPurPrc'],3);
					$LastPurPrc = $resultSAP['LastPurPrc'];
				}
			}
			$arrCol[$r]['P0'] = number_format($result['P0'],2);
			$arrCol[$r]['P1'] = number_format($result['P1'],2);
			$arrCol[$r]['P2'] = number_format($result['P2'],2);

			if($result['P2'] != 0) {
				$GP_P2 = (($result['P2'] - $LastPurPrc) / $result['P2']) * 100;
			}else{
				$GP_P2 = 0;
			}
			$arrCol[$r]['GP_P2'] = number_format($GP_P2,2)."%";
			$arrCol[$r]['S1P'] = number_format($result['S1P'],2);

			if($result['S1P'] != 0) {
				$GP_S1P = (($result['S1P'] - $LastPurPrc) / $result['S1P']) * 100;
			}else{
				$GP_S1P = 0;
			}
			$arrCol[$r]['GP_S1P'] = number_format($GP_S1P,2)."%";
			$arrCol[$r]['S1Q'] = number_format($result['S1Q'],0);
			$arrCol[$r]['S2P'] = number_format($result['S2P'],2);

			if($result['S2P'] != 0) {
				$GP_S2P = (($result['S2P'] - $LastPurPrc) / $result['S2P']) * 100;
			}else{
				$GP_S2P = 0;
			}
			$arrCol[$r]['GP_S2P'] = number_format($GP_S2P,2)."%";
			$arrCol[$r]['S2Q'] = number_format($result['S2Q'],0);
			$arrCol[$r]['S3P'] = number_format($result['S3P'],2);

			if($result['S3P'] != 0) {
				$GP_S3P = (($result['S3P'] - $LastPurPrc) / $result['S3P']) * 100;
			}else{
				$GP_S3P = 0;
			}
			$arrCol[$r]['GP_S3P'] = number_format($GP_S3P,2)."%";
			$arrCol[$r]['S3Q'] = number_format($result['S3Q'],0);
			$arrCol[$r]['MgrPrice'] = number_format($result['MgrPrice'],2);

			if($result['MgrPrice'] != 0) {
				$GP_MgrPrice = (($result['MgrPrice'] - $LastPurPrc) / $result['MgrPrice']) * 100;
			}else{
				$GP_MgrPrice = 0;
			}
			$arrCol[$r]['GP_MgrPrice'] = number_format($GP_MgrPrice,2)."%";
			$arrCol[$r]['MTPrice'] = number_format($result['MTPrice'],2);

			$r++;
		}
	}else{
		while ($result = mysqli_fetch_array($sqlQRY)) {
			$arrCol[$r]['ItemCode'] = "<a href='javascript:void(0)' onclick=\"GetItemCode('".$result['ItemCode']."')\">".$result['ItemCode']."</a>";
			$arrCol[$r]['ItemName'] = $result['ItemName'];
			$arrCol[$r]['BarCode'] = $result['BarCode'];
			$sqlSAP =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc'
						FROM OITM T0 LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode WHERE T0.ItemCode = '".$result['ItemCode']."'";   
			$qrySAP = SAPSelect($sqlSAP);
			$resultSAP = odbc_fetch_array($qrySAP);
			$arrCol[$r]['LastPurPrc'] = number_format(0,3);
			if(isset($resultSAP['LastPurPrc'])) {
				if($resultSAP['LastPurPrc'] != null) {
					$arrCol[$r]['LastPurPrc'] = number_format($resultSAP['LastPurPrc'],3);
				}
			}
			$arrCol[$r]['P0'] = number_format($result['P0'],2);
			$arrCol[$r]['P1'] = number_format($result['P1'],2);
			$arrCol[$r]['P2'] = number_format($result['P2'],2);
			$arrCol[$r]['S1P'] = number_format($result['S1P'],2);
			$arrCol[$r]['S1Q'] = number_format($result['S1Q'],0);
			$arrCol[$r]['S2P'] = number_format($result['S2P'],2);
			$arrCol[$r]['S2Q'] = number_format($result['S2Q'],0);
			$arrCol[$r]['S3P'] = number_format($result['S3P'],2);
			$arrCol[$r]['S3Q'] = number_format($result['S3Q'],0);

			$r++;
		}
	}
}

if ($_GET['a'] == 'GetItemCode') {
	$sqlOITM = "SELECT ItemName, BarCode, ProductStatus FROM OITM WHERE ItemCode = '".$_POST['ItemCode']."' LIMIT 1";
	$rowOITM = CHKRowDB($sqlOITM);
	if ($rowOITM == 1) {
		$sqlPL = "SELECT T0.ItemCode, T0.P0,T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice,T0.MTPrice2, T1.ItemName, T1.ProductStatus, T1.BarCode, T0.StartDate, T0.EndDate 
			FROM pricelist T0
			LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
			WHERE T0.ItemCode = '".$_POST['ItemCode']."' AND T0.PriceType = '".$_POST['PriceType']."' AND T0.PriceStatus = 'A' LIMIT 1";
		$rowPL = CHKRowDB($sqlPL);

		$sqlSAP =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc' 
					FROM OITM T0 LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode WHERE T0.ItemCode = '".$_POST['ItemCode']."'";   
		$qrySAP = SAPSelect($sqlSAP);
		$resultSAP = odbc_fetch_array($qrySAP);
		$LastPurPrc = 0;
		if(isset($resultSAP['LastPurPrc'])) {
			if($resultSAP['LastPurPrc'] != null) {
				$LastPurPrc = $resultSAP['LastPurPrc'];
			}
		}
		if ($rowPL == 1) {
			$result = MySQLSelect($sqlPL);
			$arrCol['P0'] = $result['P0'];
			$arrCol['P1'] = $result['P1'];
			$arrCol['P2'] = $result['P2'];
			$arrCol['S1Q'] = $result['S1Q'];
			$arrCol['S1P'] = $result['S1P'];
			$arrCol['S2Q'] = $result['S2Q'];
			$arrCol['S2P'] = $result['S2P'];
			$arrCol['S3Q'] = $result['S3Q'];
			$arrCol['S3P'] = $result['S3P'];
			$arrCol['MgrPrice'] = $result['MgrPrice'];
			$arrCol['MTPrice'] = $result['MTPrice'];
			$arrCol['MTPrice2'] = $result['MTPrice2'];
			$arrCol['ItemName'] = $result['ItemName'];
			$arrCol['BarCode'] = $result['BarCode'];
			$arrCol['StartDate'] = $result['StartDate'];
			$arrCol['EndDate'] = $result['EndDate'];
			$arrCol['ProductStatus'] = $result['ProductStatus'];
			
			$arrCol['LstEvlPric'] = $LastPurPrc;
			
			if($result['S1P'] != 0) {
				$GP_S1 = (($result['S1P'] - $LastPurPrc) / $result['S1P']) * 100;
			}else{
				$GP_S1 = 0;
			}
			$arrCol['GP_S1'] = $GP_S1;
			if($result['S2P'] != 0) {
				$GP_S2 = (($result['S2P'] - $LastPurPrc) / $result['S2P']) * 100;
			}else{
				$GP_S2 = 0;
			}
			$arrCol['GP_S2'] = $GP_S2;
			if($result['S3P'] != 0) {
				$GP_S3 = (($result['S3P'] - $LastPurPrc) / $result['S3P']) * 100;
			}else{
				$GP_S3 = 0;
			}
			$arrCol['GP_S3'] = $GP_S3;
		}else{
			$result2 = MySQLSelect($sqlOITM);
			$arrCol['ItemName'] = $result2['ItemName'];
			$arrCol['BarCode'] = $result2['BarCode'];
			$arrCol['ProductStatus'] = $result2['ProductStatus'];
			$arrCol['LstEvlPric'] = $LastPurPrc;
		}
		$arrCol['rowOITM'] = $rowOITM;
		$arrCol['rowPL'] = $rowPL;
	}else{
		$NoData = "ไม่มีรหัสสินค้านี้";
		$arrCol['NoData'] = $NoData;
		$arrCol['rowOITM'] = $rowOITM;
	}
}

if ($_GET['a'] == 'ActionPrice') {
	if ($_POST['submit'] == "AddPrice") {
		$insert = "INSERT INTO pricelist 
				   SET ItemCode = '".$_POST['ItemCode']."',
					   P0 = '".$_POST['P0']."',
					   P1 = '".$_POST['P1']."',
					   P2 = '".$_POST['P2']."',
					   S1Q = '".$_POST['S1Q']."',
					   S1P = '".$_POST['S1P']."',
				       S2Q = '".$_POST['S2Q']."',
					   S2P = '".$_POST['S2P']."',
					   S3Q = '".$_POST['S3Q']."',
					   S3P = '".$_POST['S3P']."',
				       MgrPrice = '".$_POST['MgrPrice']."',
					   MTPrice = '".$_POST['MTPrice']."',
					   MTPrice2 = '".$_POST['MTPrice2']."',
					   PriceType = '".$_POST['PriceType']."',
					   PriceStatus = 'A',
				       DateCreate = NOW(),
					   UkeyCreate = '".$_SESSION['ukey']."'";
		if($_POST['PriceType'] == 'PRO') {
			$insert.=",StartDate = '".$_POST['StartDate']."', 
					   EndDate = '".$_POST['EndDate']."'";
		}
		$sqlQRYinsert = MySQLInsert($insert);
		$note = "เพิ่มข้อมูลราคาเสร็จสิ้น";
	}else{
		$update = "UPDATE pricelist 
				   SET P0 = '".$_POST['P0']."',
				       P1 = '".$_POST['P1']."',
				       P2 = '".$_POST['P2']."',
					   S1Q = '".$_POST['S1Q']."',
					   S1P = '".$_POST['S1P']."',
				       S2Q = '".$_POST['S2Q']."',
					   S2P = '".$_POST['S2P']."',
					   S3Q = '".$_POST['S3Q']."',
					   S3P = '".$_POST['S3P']."',
					   MgrPrice = '".$_POST['MgrPrice']."',
					   MTPrice = '".$_POST['MTPrice']."',
					   MTPrice2 = '".$_POST['MTPrice2']."',
					   DateUpdate = NOW(),
					   UkeyUpdate = '".$_SESSION['ukey']."'";
		if($_POST['PriceType'] == 'PRO') {
			$update.=",StartDate = '".$_POST['StartDate']."', 
					   EndDate = '".$_POST['EndDate']."'";
		}
		$update .= "WHERE ItemCode = '".$_POST['ItemCode']."' AND PriceType = '".$_POST['PriceType']."'";
		$sqlQRYupdate = MySQLUpdate($update);
		$note = "อับเดตข้อมูลราคาเสร็จสิ้น";
	}
	$arrCol['note'] = $note;
}

if ($_GET['a'] == 'GetCardCode') {
	$slqCardCode = "SELECT T0.CardCode, T0.CardName FROM ocrd T0";
	$qryCardCode = MySQLSelectX($slqCardCode);
	$CardCode = "<option value='' selected disabled>กรุณาเลือกร้านค้า</option>";
	while ($resultCus = mysqli_fetch_array($qryCardCode)) {
		$CardCode .= "<option value='".$resultCus['CardCode']."'>".$resultCus['CardCode']." | ".$resultCus['CardName']."</option>";
	}
	$arrCol['CardCode'] = $CardCode;
}

if ($_GET['a'] == 'GetMTGroup') {
	$sqlMTG = "SELECT T0.ID, T0.GroupName FROM mtgroup T0";
	$qryMTG = MySQLSelectX($sqlMTG);
	$MTGroup = "<option value='' selected disabled>กรุณาเลือกกลุ่มลูกค้า</option>";
	while ($resultMTG = mysqli_fetch_array($qryMTG)) {
		$MTGroup .= "<option value='".$resultMTG['ID']."'>".$resultMTG['ID'].". ".$resultMTG['GroupName']."</option>";
	}
	$arrCol['MTGroup']  = $MTGroup;
}

// $sql = "SELECT  DISTINCT SUBSTRING(GroupCode,6,3) AS DocNum FROM groupprice WHERE DocStatus = 'A' AND SUBSTRING(GroupCode,4,2)  = SUBSTRING((YEAR(NOW())+543),3,2) ORDER BY GroupCode DESC LIMIT 1";
if ($_GET['a'] == 'GetIDPriceType') {
	$sql = "SELECT  DISTINCT GroupCode FROM groupprice WHERE DocStatus = 'A' ORDER BY GroupCode DESC LIMIT 1";
	$result = MySQLSelect($sql);
	$YearCurrent = intval(substr($result['GroupCode'],3,2));
	$YearNow = intval(substr((date("Y")+543),2,2));
	$GRPY = 'GRP'.$YearNow; // GRP65
	if ($YearCurrent == $YearNow) {
		$resultNumber = intval(substr($result['GroupCode'],5,3))+1;
		if ($resultNumber <= 9){
			$newResultNB = '00'.$resultNumber;
		}else{
			if ($resultNumber <= 99){
				$newResultNB = '0'.$resultNumber;
			}else{
				$newResultNB = $resultNumber;
			}
		}
		$newGroupCode = $GRPY.$newResultNB;
	}else{
		$newResultNB = '001';
		$newGroupCode = $GRPY.$newResultNB; // GRP65001
	}

	if (isset($newGroupCode)) {
		$sqlGroupPrice = "SELECT GroupCode FROM groupprice WHERE DocStatus = 'A' GROUP BY GroupCode";
		$qryGroupPrice = MySQLSelectX($sqlGroupPrice);
		$GroupCode = "<option value='".$newGroupCode."' selected>เพิ่มรหัสใหม่ (".$newGroupCode.")</option>";
		while ($resultGP = mysqli_fetch_array($qryGroupPrice)) {
			$GroupCode .= "<option value='".$resultGP['GroupCode']."'>".$resultGP['GroupCode']."</option>";
		}
	}

	
	$arrCol['GroupCode']  = $GroupCode;
}

if ($_GET['a'] == 'AddPriceType') {
	$insert = "INSERT INTO groupprice
			   SET GroupCode = '".$_POST['GroupCode']."',
			   	   CardCode = '".$_POST['CardCode']."',
				   MTGroup = '".$_POST['MTGroup']."',
			       DocStatus = 'A', DateCreate = NOW(),
				   UkeyCreate = '".$_SESSION['ukey']."'";
	$sqlQRYinsert = MySQLInsert($insert);
	$note = "เพิ่มข้อมูลประเภทราคาเสร็จสิ้น";

	$arrCol['note']  = $note;
}
if ($_GET['a']=='AddItem'){
	$sql1 = "SELECT ItemCode FROM pricelist WHERE ItemCode = '".$_POST['ItemCode']."' AND PriceType = '".$_POST['PriceType']."'";
	//echo $sql1;
	if (CHKRowDB($sql1) == 0){
		$GetData = new DateTime(); 
		$GetLastDa = new DateTime($GetData->format('Y')."-12-01"); 
		$StartDate = $GetData->format('Y')."-01-01";
		$EndDate = $GetLastDa->format('Y-m-t');

		$sqlInsert = 
			"INSERT INTO pricelist 
			SET ItemCode = '".$_POST['ItemCode']."',
				PriceType = '".$_POST['PriceType']."',
				StartDate = '$StartDate',
				EndDate = '$EndDate',
				UkeyCreate = '".$_SESSION['ukey']."'";
		MySQLInsert($sqlInsert);
		// echo $sqlInsert;
		$output = "Y";
	}else{
		$output = "N";
	}
	$arrCol['output'] = $output;
}


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
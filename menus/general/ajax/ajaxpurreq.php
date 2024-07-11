<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

if($_GET['p'] == "GetItemList") {
	$ItemSQL = "SELECT T0.ItemCode, T0.BarCode, T0.ItemName FROM OITM T0 WHERE T0.IsBom = 0 AND T0.ItemCode NOT IN ('','00-000-002','00-000-003','00-000-004','00-000-005','00-000-006')";
	$ItemQRY = MySQLSelectX($ItemSQL);
	$output = "";
	while($ItemRST = mysqli_fetch_array($ItemQRY)) {
		$output .= "<option value='".$ItemRST['ItemCode']." | ".$ItemRST['ItemName']."'>";
	}
	$arrCol['output'] = $output;
}

if($_GET['p'] == "GetItemDetail") {
	$ItemSQL = "SELECT T0.ItemCode, T0.MgrUnit FROM OITM T0 WHERE T0.ItemCode = '".$_POST['ItemCode']."' LIMIT 1";
	$ItemRST = MySQLSelect($ItemSQL);
	$arrCol['output'] = $ItemRST['MgrUnit'];

	$PriceSQL = "
		SELECT
			CASE
				WHEN T0.MgrPrice != 0 THEN T0.MgrPrice/1.07
				WHEN T0.P0 != 0 AND T0.P0 < T0.P1 AND T0.P0 < T0.P2 AND T0.P0 < T0.S1P AND T0.P0 < T0.S2P AND T0.P0 < T0.S3P THEN T0.P0/1.07
				WHEN T0.P1 != 0 AND T0.P1 < T0.P2 AND T0.P1 < T0.S1P AND T0.P1 < T0.S2P AND T0.P1 < T0.S3P THEN T0.P1/1.07
				WHEN T0.P2 != 0 AND T0.P2 < T0.S1P AND T0.P2 < T0.S2P AND T0.P2 < T0.S3P THEN T0.P2/1.07
				WHEN T0.S1P != 0 AND T0.S1P < T0.S2P AND T0.S1P < T0.S3P THEN T0.S1P/1.07
				WHEN T0.S2P != 0 AND T0.S2P < T0.S3P THEN T0.S2P/1.07
				WHEN T0.S3P != 0 THEN T0.S3P/1.07
			ELSE 0 END AS 'SalePriceTHB'
		FROM pricelist T0
		WHERE T0.ItemCode = '".$_POST['ItemCode']."' AND T0.PriceType = 'STD' AND T0.PriceStatus = 'A' LIMIT 1";
	$PriceQRY = MySQLSelect($PriceSQL);
	$arrCol['SalePriceTHB'] = $PriceQRY['SalePriceTHB'];
}

if($_GET['p'] == "SearchDoc") {
	$DocSubStr = explode("-",$_POST['kwd']);
	// echo $DocSubStr[0];
	if(strlen($DocSubStr[0]) == 6) {
		/* HOOK FROM EUROX FORCE */
		$DocType = substr($DocSubStr[0],0,2);
		$DocNum = substr($_POST['kwd'],2);
		$GetItemSQL = 
			"SELECT
				T0.DocEntry, T1.VisOrder, T1.ItemCode, T1.ItemName, T1.Qty, T1.UnitMsr,
				T1.UnitPrice, T1.UnitCur, T1.UnitRate, T1.UnitPriceTHB, T1.LineTotal, T1.LineTotalTHB ,T1.SalePriceTHB
			FROM purreq_header T0
			LEFT JOIN purreq_detail T1 ON T0.DocEntry = T1.DocEntry
			WHERE T0.DocType = '$DocType' AND T0.DocNum = '$DocNum'
			ORDER BY T1.VisOrder";
		$Rows = CHKRowDB($GetItemSQL);
		if($Rows == 0) {
			$arrCol['Rows'] = 0;
		} else {
			$GetItemQRY = MySQLSelectX($GetItemSQL);
			while($GetItemRST = mysqli_fetch_array($GetItemQRY)) {
				$arrCol[$GetItemRST['VisOrder']]['ItemCode']     = $GetItemRST['ItemCode'];
				$arrCol[$GetItemRST['VisOrder']]['ItemName']     = $GetItemRST['ItemName'];
				$arrCol[$GetItemRST['VisOrder']]['Qty']          = $GetItemRST['Qty'];
				$arrCol[$GetItemRST['VisOrder']]['UnitMsr']      = $GetItemRST['UnitMsr'];
				$arrCol[$GetItemRST['VisOrder']]['UnitPrice']    = $GetItemRST['UnitPrice'];
				$arrCol[$GetItemRST['VisOrder']]['UnitCur']      = $GetItemRST['UnitCur'];
				$arrCol[$GetItemRST['VisOrder']]['UnitRate']     = $GetItemRST['UnitRate'];
				$arrCol[$GetItemRST['VisOrder']]['UnitPriceTHB'] = $GetItemRST['UnitPriceTHB'];
				$arrCol[$GetItemRST['VisOrder']]['LineTotal']    = $GetItemRST['LineTotal'];
				$arrCol[$GetItemRST['VisOrder']]['LineTotalTHB'] = $GetItemRST['LineTotalTHB'];
				$arrCol[$GetItemRST['VisOrder']]['SalePriceTHB'] = $GetItemRST['SalePriceTHB'];
				$arrCol[$GetItemRST['VisOrder']]['GrossPrft']    = 0;
			}
			$arrCol['Rows'] = $Rows;
		}
	} else {
		/* HOOK FROM SAP */
	}
}

if($_GET['p'] == "SavePurReq") {
	$PurReqEntry = $_POST['PurReqEntry'];
	
	switch($_POST['DocType']) {
		case "LC": $DocType = 'LC'; $Suffix = "-0"; break;
		default:   $DocType = 'IM'; $Suffix = "-1"; break;
	}

	$YDocNum = substr(date("Y")+543,-2);
	$MDocNum = date("m");
	$DocPrefix = $YDocNum.$MDocNum;
	// echo $DocPrefix.$Suffix;
	$GetDocSQL = "SELECT T0.DocNum FROM purreq_header T0 WHERE T0.DocType = '$DocType' AND T0.DocNum LIKE '".$DocPrefix.$Suffix."%' ORDER BY T0.DocEntry DESC LIMIT 1";
	$GetDocRST = MySQLSelect($GetDocSQL);

	if(!isset($GetDocRST['DocNum'])) {
		$NewDocNum = $DocPrefix.$Suffix."001";
	} else {
		$LastDocNum = intval(substr($GetDocRST['DocNum'],-3));
		$NextDocNum = $LastDocNum+1;
		if($NextDocNum <= 9) {
			$NewSuffix = "00".$NextDocNum;
		} elseif($NextDocNum >= 10 && $NextDocNum <= 99) {
			$NewSuffix = "0".$NextDocNum;
		} else {
			$NewSuffix = $NextDocNum;
		}
		$NewDocNum = $DocPrefix.$Suffix.$NewSuffix;
	}
	/* LCYYMM-0XXX / IMYYMM-1XXX */

	if($_POST['SaveType'] == 1) {
		$DraftStatus = 'N';
	} else {
		$DraftStatus = 'Y';
	}
	$ItemQuotaTeam = NULL;
	$Quota = $_POST['ItemQuota'];
	$Count = count($Quota);
	if($Count > 0) {
		for($q=0; $q<=$Count-1; $q++) {
			$ItemQuotaTeam .= $Quota[$q];
			if($q != $Count-1) {
				$ItemQuotaTeam .= ", ";
			}
		}
	}

	$DocNum          = $NewDocNum;
	$DocType         = $DocType;
	$DraftStatus     = $DraftStatus;
	$DocDate         = $_POST['DocDate'];
	$DocDueDate      = $_POST['DocDueDate'];
	$ProductType     = $_POST['ItemType'];
	$ItemQuotaTeam   = $ItemQuotaTeam;
	$ShiptoType      = $_POST['ShiptoType'];
	$ShiptoAddress   = $_POST['ShiptoAddress'];
	$ShiptoWhse      = $_POST['ShiptoWhse'];
	$PackageRemark   = $_POST['RemarkPackage'];
	$PackageFilePath = $_POST['PackageFilePath'];
	$DocRemark       = $_POST['PurchaseReasons'];
	$Comments        = $_POST['Comments'];
	$CreateUkey      = $_SESSION['ukey'];

	/* INSERT HEADER */
	$HeaderSQL =
		"INSERT INTO purreq_header SET
			DocNum = '$DocNum',
			DraftStatus = '$DraftStatus',
			DocType = '$DocType',
			DocDate = '$DocDate',
			DocDueDate = '$DocDueDate',
			ProductType = '$ProductType',
			ItemQuotaTeam = '$ItemQuotaTeam',
			ShiptoType = '$ShiptoType',
			ShiptoAddress = '$ShiptoAddress',
			ShiptoWhse = '$ShiptoWhse',
			PackageRemark = '$PackageRemark',
			PackageFilePath = '$PackageFilePath',
			DocRemark = '$DocRemark',
			Comments = '$Comments',
			CreateUkey = '$CreateUkey'
		";
	// echo $HeaderSQL;
	$DocEntry = MySQLInsert($HeaderSQL);

	/* INSERT DETAIL */
	$TotalRow = $_POST['TotalRow'];
	$No = 0;
	for($r = 1; $r <= $TotalRow; $r++) {
		if($_POST['ItemCode_'.$r] != "" || $_POST['ItemName_'.$r] != "") {
			$ItemCode = $_POST['ItemCode_'.$r];
			$ItemName = $_POST['ItemName_'.$r];
			if($_POST['ItemCode_'.$r] != "") {
				$ItemSQL = "SELECT T0.ItemName, T0.ProductStatus FROM OITM T0 WHERE ItemCode = '$ItemCode' LIMIT 1";
				$ItemRST = MySQLSelect($ItemSQL);
				$ItemStatus = $ItemRST['ProductStatus'];
			} else {
				$ItemStatus = "";
			}
			$WhsCode = $ShiptoWhse;
			$Qty = str_replace(",","",$_POST['Quantity_'.$r]);
			if($ProductType == "B2") {
				$OpenQty = 0;
			} else {
				$OpenQty = $Qty;
			}
			$UnitMsr = $_POST['Unit_'.$r];
			$UnitCur = $_POST['Currency_'.$r];
			$UnitRate = $_POST['UnitRate_'.$r];
			$UnitPrice = str_replace(",","",$_POST['UnitPrice_'.$r]);
			$UnitPriceTHB = str_replace(",","",$_POST['UnitPriceTHB_'.$r]);
			$LineTotal = str_replace(",","",$_POST['LineTotal_'.$r]);
			$LineTotalTHB = str_replace(",","",$_POST['LineTotalTHB_'.$r]);
			$SalePriceTHB = str_replace(",","",$_POST['SalePrice_'.$r]);
			
			$DetailSQL =
				"INSERT INTO purreq_detail SET
					DocEntry = $DocEntry,
					VisOrder = $No,
					ItemCode = '$ItemCode',
					ItemName = '$ItemName',
					ItemStatus = '$ItemStatus',
					WhsCode = '$WhsCode',
					Qty = $Qty,
					OpenQty = $OpenQty,
					UnitMsr = '$UnitMsr',
					UnitCur = '$UnitCur',
					UnitRate = '$UnitRate',
					UnitPrice = $UnitPrice,
					UnitPriceTHB = $UnitPriceTHB,
					LineTotal = $LineTotal,
					LineTotalTHB = $LineTotalTHB,
					SalePriceTHB = $SalePriceTHB,
					CreateUkey = '$CreateUkey'
				;";
			// echo $DetailSQL;
			MySQLInsert($DetailSQL);
			$No++;
		}
	}

	/* INSERT ATTACHMENT */
	if(isset($_FILES['FileAttach']['name'])) {
		$Totals = count($_FILES['FileAttach']['name'])-1;
		// echo $Totals;
		for($i = 0; $i <= $Totals; $i++) {
			$FileProcess = explode(".",basename($_FILES['FileAttach']['name'][$i]));
			$countProcess = count($FileProcess);
			if($countProcess == 2){
				$FileOriName = $FileProcess[0]; 
				$FileExt = $FileProcess[1];
			} else {
				$FileOriName = "";
				$FileExt = $FileProcess[$countProcess-1];
				for($n = 0; $n <= $countProcess-2; $n++) {
					$FileOriName .= $FileProcess[$n].".";
				}
				$FileOriName = substr($FileOriName,0,-1);
			}

			$tmpFilePath = $_FILES['FileAttach']['tmp_name'][$i];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/PR/".$DocType.$DocNum."-".$i.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				// $DocEntry = 2;

				$AttachSQL = "INSERT INTO purreq_attach SET
					DocEntry = $DocEntry,
					VisOrder = $i,
					FileOriName = '$FileOriName',
					FileDirName = '".$DocType.$DocNum."-".$i."',
					FileExt = '$FileExt',
					UploadUkey = '$CreateUkey'
				;";
				// echo $AttachSQL;
				MySQLInsert($AttachSQL);
			}
		}
	}

	if($_POST['SaveType'] == "1") {
		// require('ajaxApprove.php');
	}
}



array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
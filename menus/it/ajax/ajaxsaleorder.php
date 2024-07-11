<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
	exit;
}

$resultArray = array();
$arrCol = array();
$output = "";

if ($_GET['p'] == 'head'){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}


if ($_GET['p'] == "GetCardCode") {
	$CustSQL = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE T0.CardType = 'C' AND (T0.CardCode != '' OR T0.CardName != '') AND T0.CardStatus = 'A' ORDER BY T0.CardCode";
	$CustQRY = MySQLSelectX($CustSQL);
	$output .= "<option value='' selected disabled>กรุณาเลือกลูกค้า</option>";
	while($CustRST = mysqli_fetch_array($CustQRY)) {
		$output .= "<option value='".$CustRST['CardCode']."'>".$CustRST['CardCode']." | ".$CustRST['CardName']."</option>";
	}
	$arrCol['output'] = $output;
}

if ($_GET['p'] == "GetAddress") {
	
	$sql = "SELECT 
				T0.CardCode, T0.AdresType AS 'AddressType', T0.Address AS 'AddressID', (T0.Street+' '+T0.Block+' '+T0.City+' '+CAST(T0.ZipCode AS VARCHAR)) AS 'FullAddress',
				CASE WHEN T0.AdresType = 'B' THEN T1.BilltoDef ELSE T1.ShiptoDef END AS 'AddressDef', T1.SlpCode, T1.LicTradNum, ISNULL(T1.U_ShippingType,814) AS 'U_ShippingType'
			FROM CRD1 T0
			LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode
			WHERE T0.CardCode = '".$_POST['CardCode']."'
			ORDER BY T0.AdresType, T0.LineNum";
			// echo $sql;
	$CusQRY = SAPSelect($sql); // PERz
	$outputBD = "";
	$outputSD = "";
	$outputB = "";
	$outputS = "";
	$outputSlp = "";
	$outputTaxID = "";
	$outputShipping = "";
	while ($result = odbc_fetch_array($CusQRY)) {
		if($result['AddressType'] == "B" && $result['AddressID'] != '00000') {
			$BillDefault = conutf8($result['AddressDef']);
			if(conutf8($result['AddressID']) == $BillDefault) {
				$class = " class='default'";
				$text = " (ค่าเริ่มต้น)";
			} else {
				$class = null;
				$text = null;
			}
			$outputB .= "<option value='".conutf8($result['AddressID'])."'".$class.">".conutf8($result['AddressID'])." ".conutf8($result['FullAddress']).$text."</option>";
			$outputBD = conutf8($result['AddressDef']);
		} else {
			$ShipDefault = conutf8($result['AddressDef']);
			if(conutf8($result['AddressID']) == $ShipDefault) {
				$class = " class='default'";
				$text = " (ค่าเริ่มต้น)";
			} else {
				$class = null;
				$text = null;
			}
			$outputS .= "<option value='".conutf8($result['AddressID'])."'".$class.">".conutf8($result['AddressID'])." ".conutf8($result['FullAddress']).$text."</option>";
			$outputSD = conutf8($result['AddressDef']);
		}
		$outoutSlp = $result['SlpCode'];
		$outputTaxID = $result['LicTradNum'];
		$outputShipping = conutf8($result['U_ShippingType']);
	}
	$arrCol['outputB'] = $outputB;
	$arrCol['outputS'] = $outputS;
	$arrCol['outputBD'] = $outputBD;
	$arrCol['outputSD'] = $outputSD;
	$arrCol['outputSaleCode'] = $outoutSlp;
	$arrCol['outputTaxID'] = $outputTaxID;
	$arrCol['outputShipping'] = $outputShipping;
}

if ($_GET['p'] == "GetSlpCode") {
	// $sql = "SELECT T0.SlpCode, T0.SlpName, T0.U_Dim1 FROM OSLP T0 WHERE T0.SlpCode > 0
	// 		ORDER BY
	// 		CASE
	// 			WHEN T0.U_Dim1 = 'MT1' THEN 1
	// 			WHEN T0.U_Dim1 = 'MT2' THEN 2
	// 			WHEN T0.U_Dim1 = 'TT2' THEN 3
	// 			WHEN T0.U_Dim1 = 'TT1' THEN 4
	// 			WHEN T0.U_Dim1 = 'OUL' THEN 5
	// 			WHEN T0.U_Dim1 = 'ONL' THEN 6
	// 		ELSE 7 END, T0.U_Name_end ASC";
	// $SlpQRY = SAPSelect($sql); // PERz
	// $outputSlp = "<option value='' selected disabled>กรุณาเลือกพนักงานขาย</option>";
	// $tempTeam = "";
	// while ($result = odbc_fetch_array($SlpQRY)) {
	// 	if($tempTeam != $result['U_Dim1']) {
	// 		if($tempTeam != "") {
	// 			$outputSlp .= "</optgroup>";
	// 		}
	// 		$outputSlp .= "<optgroup label='".$result['U_Dim1']."'>";
	// 	}
	// 	$outputSlp .= "<option value='".$result['SlpCode']."'>".conutf8($result['SlpName'])."</option>";
	// 	$tempTeam = $result['U_Dim1'];
	// }
	// $outputSlp .= "</optgroup>";
	$SlpSQL = "SELECT T0.SlpCode, T0.SlpName, T0.MainTeam FROM OSLP T0 WHERE T0.CodeStatus = 'A'
				ORDER BY
				CASE
					WHEN T0.MainTeam = 'MT1' THEN 1
					WHEN T0.MainTeam = 'EXP' THEN 2
					WHEN T0.MainTeam = 'MT2' THEN 3
					WHEN T0.MainTeam = 'TT2' THEN 4
					WHEN T0.MainTeam = 'TT1' THEN 5
					WHEN T0.MainTeam = 'OUL' THEN 6
					WHEN T0.MainTeam = 'ONL' THEN 7
					WHEN T0.MainTeam = 'EI1' THEN 8
					WHEN T0.MainTeam = 'MKT' THEN 9
					WHEN T0.MainTeam = 'DMN' THEN 10
				ELSE 11 END, T0.SlpName ASC";
	$SlpQRY = MySQLSelectX($SlpSQL);
	$outputSlp = "<option value='' selected disabled>กรุณาเลือกพนักงานขาย</option>";
	$tempTeam = "";
	while($SlpRST = mysqli_fetch_array($SlpQRY)) {
		if($tempTeam != $SlpRST['MainTeam']) {
			if($tempTeam != "") {
				$outputSlp .= "</optgroup>";
			}
			$outputSlp .= "<optgroup label='".SATeamName($SlpRST['MainTeam'])."'>";
		}
		$outputSlp .= "<option value='".$SlpRST['SlpCode']."'>".$SlpRST['SlpName']."</option>";
		$tempTeam = $SlpRST['MainTeam'];
	}
	$outputSlp .= "</optgroup>";
	$arrCol['outputSlp'] = $outputSlp;
}

if ($_GET['p'] == "GetShipping") {
	$ShipSQL = "SELECT T0.Code, T0.Name, T0.U_Address FROM [dbo].[@SHIPPINGTYPE] T0 ORDER BY T0.Name";
	$ShipQRY = SAPSelect($ShipSQL); // PERz
	$output = "<option value='' selected disabled>กรุณาเลือกชื่อขนส่ง</option>";
	while($ShipRST = odbc_fetch_array($ShipQRY)) {
		$output .= "<option value='".conutf8($ShipRST['Code'])."'>".conutf8($ShipRST['Name'])." | ".conutf8($ShipRST['U_Address'])."</option>";
	}
	$arrCol['output'] = $output;
}

if ($_GET['p'] == "GetItemProduct") {
	$ItemSQL = "SELECT T0.ItemCode, T0.BarCode, T0.ItemName, CASE WHEN T0.ProductStatus = '' THEN 'K' ELSE T0.ProductStatus END AS 'ProductStatus', T0.MgrUnit FROM OITM T0 WHERE T0.IsBom = 0 AND T0.ItemCode NOT IN ('','00-000-002','00-000-003','00-000-004','00-000-005','00-000-006')";
	$ItemQRY = MySQLSelectX($ItemSQL);
	$outputPro = "<option value='' selected disabled>กรุณาเลือกรหัสสินค้า</option>";
	while($ItemRST = mysqli_fetch_array($ItemQRY)) {
		if($ItemRST['ItemCode'] != $ItemRST['BarCode']) {
			if($ItemRST['BarCode'] != "") {
				$BarCode = " | ".$ItemRST['BarCode'];
			} else {
				$BarCode = null;
			}
		} else {
			$BarCode = null;
		}

		if($ItemRST['ProductStatus'] == "") {
			$Status = null;
		} else {
			$Status = " | [".$ItemRST['ProductStatus']."]";
		}
		$outputPro .= "<option value='".$ItemRST['ItemCode']."' data-ItemName = '".$ItemRST['ItemName']."' data-BarCode='".$ItemRST['BarCode']."' data-ItemStatus='".$ItemRST['ProductStatus']."' data-UnitMsr='".$ItemRST['MgrUnit']."'>".$ItemRST['ItemCode']." | ".$ItemRST['ItemName'].$BarCode.$Status."</option>";
	}
	$arrCol['outputPro'] = $outputPro;
}

if ($_GET['p'] == "GetItemDetail") {
	// ดึงประวัติการขาย 5 บิลล่าสุด
	$History   = "";
	$HisSQL = "SELECT TOP 3 T1.DocDate, T0.Quantity, T0.UnitMsr, T0.PriceBefDi, T0.U_DiscP1, T0.U_DiscP2, T0.U_DiscP3, T0.U_DiscP4, T0.U_DiscP5, T0.Price, T0.VatSum FROM INV1 T0 
			   LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry 
			   WHERE T0.ItemCode = '".$_POST['ItemCode']."' AND T1.CardCode = '".$_POST['CardCode']."' AND T0.PriceAfVat > 0
	           ORDER BY T1.DocEntry DESC";
	// $HisQRY = SAPSelect($HisSQL); // PERz ผ่านไปสองเดือนค่อยแก้คืนนะจ้ะ
	$HisQRY   = conSAP8($HisSQL); // PERz
	$History .= "<div class='table-responsive'>";
	$History .= "<table class='table table-bordered table-hover' style='font-size: 14px;'>";
	$History .= "<thead class='text-center table-group-divider'>";
	$History .= "<tr>";
	$History .= "<th width='12.5%'>วันที่สั่งซื้อ</th>";
	$History .= "<th width='10%'>จำนวน</th>";
	$History .= "<th width='12.5%'>ราคาขาย<br/>(ก่อน VAT)</th>";
	$History .= "<th width='20%'>ส่วนลด (%)</th>";
	$History .= "<th width='12.5%'>ราคาสุทธิ<br/>(ก่อน VAT)</th>";
	$History .= "<th width='10%'>ภาษี<br/>(VAT)</th>";
	$History .= "</tr>";
	$History .= "</thead>";
	$History .= "<tbody>";
	while ($result = odbc_fetch_array($HisQRY)) {
		$Discount = 0;
		if ($result['U_DiscP5'] != NULL and $result['U_DiscP5'] != "" and $result['U_DiscP5'] != 0.00) {
			$Discount = number_format($result['U_DiscP1'], 2) . "%+" . number_format($result['U_DiscP2'], 2) . "%+" . number_format($result['U_DiscP3'], 2) . "%+" . number_format($result['U_DiscP4'], 2) . "%+" . number_format($result['U_DiscP5'], 2) . "%";
		} elseif ($result['U_DiscP4'] != NULL and $result['U_DiscP4'] != "" and $result['U_DiscP4'] != 0.00) {
			$Discount = number_format($result['U_DiscP1'], 2) . "%+" . number_format($result['U_DiscP2'], 2) . "%+" . number_format($result['U_DiscP3'], 2) . "%+" . number_format($result['U_DiscP4'], 2) . "%";
		} elseif ($result['U_DiscP3'] != NULL and $result['U_DiscP3'] != "" and $result['U_DiscP3'] != 0.00) {
			$Discount = number_format($result['U_DiscP1'], 2) . "%+" . number_format($result['U_DiscP2'], 2) . "%+" . number_format($result['U_DiscP3'], 2) . "%";
		} elseif ($result['U_DiscP2'] != NULL and $result['U_DiscP2'] != "" and $result['U_DiscP2'] != 0.00) {
			$Discount = number_format($result['U_DiscP1'], 2) . "%+" . number_format($result['U_DiscP2'], 2) . "%";
		} elseif ($result['U_DiscP1'] != NULL and $result['U_DiscP1'] != "" and $result['U_DiscP1'] != 0.00) {
			$Discount = number_format($result['U_DiscP1'], 2) . "%";
		} else {
			$Discount = NULL;
		}
		
		$History .= "<tr>
						<td class='text-center'>".date("d/m/Y",strtotime($result['DocDate']))."</td>
						<td class='text-right'>".number_format($result['Quantity'],0)." ".conutf8($result['UnitMsr'])."</td>
						<td class='text-right'>".number_format($result['PriceBefDi'],2)."</td>
						<td class='text-center'>".$Discount."</td>
						<td class='text-right'>".number_format($result['Price'],2)."</td>
						<td class='text-right'>".number_format($result['VatSum']/$result['Quantity'],2)."</td>
					</tr>";
	}
	$History .= "</tbody>";
	$History .= "</table>";
	$History .= "</div>";
	$arrCol['History'] = $History;
	
	// ดึงสินค้าคงคลัง
	$Warehouse = "<option value='' selected disabled>กรุณาเลือกคลังสินค้า</option>";
	$WhsSQL = "SELECT T0.WhsCode, T1.[WhsName], T0.OnHand, T1.Location, T3.Location AS 'LocationName', ISNULL(T2.DfltWH, 'KSY') AS 'DfltWH', T2.SalUnitMsr, T0.Locked FROM OITW T0
      		   LEFT JOIN OWHS T1 ON T0.WhsCode = T1.WhsCode
			   LEFT JOIN OITM T2 ON T0.ItemCode = T2.ItemCode
			   LEFT JOIN OLCT T3 ON T1.Location = T3.Code
			   WHERE (T0.ItemCode = '".$_POST['ItemCode']."')
			   ORDER BY T1.Location, T0.WhsCode";
	$WhsQRY = SAPSelect($WhsSQL); // PERz
	$DefWhse = "";
	$LoGroup = "";
	while ($result = odbc_fetch_array($WhsQRY)) {
		if ($LoGroup != $result['LocationName']) {
			if ($LoGroup != "") {
				$Warehouse .= "</optgroup>";
			}
			$Warehouse .= "<optgroup label='".conutf8($result['LocationName'])."'>";
		}
		$DefWhse = conutf8($result['DfltWH']);
		if(conutf8($result['WhsCode']) == $DefWhse) {
			$OptionClass = " class='default'";
			$OptionText = " (ค่าเริ่มต้น)";
		} else {
			$OptionClass = null;
			$OptionText = null;
		}

		if($result['Locked'] == "Y") {
			$OptionDis = " disabled";
		} else {
			$OptionDis = null;
		}
		$Warehouse .= "<option".$OptionClass." value='".conutf8($result['WhsCode'])."' $OptionDis>".conutf8($result['WhsCode'])." - ".conutf8($result['WhsName'])." (คงเหลือ ".number_format($result['OnHand'],0)." ".conutf8($result['SalUnitMsr']).")".$OptionText."</option>";
		
		$LoGroup = $result['LocationName'];
	}
	$Warehouse .= "</optgroup>";
	$arrCol['Warehouse'] = $Warehouse;
	$arrCol['DefWhse'] = $DefWhse;

	/* เช็คราคา */
	$DP = GetPriceList($_POST['CardCode'],$_POST['ItemCode'],$_POST['Quantity']);
	$arrCol['DefaultPrice'] = AddDecimal($DP,3);

	/* ดึงต้นทุน 
	$CostSQL = "SELECT TOP 1 (T0.PriceAfVAT*1.07) AS 'PriceAfVAT' FROM PDN1 T0 WHERE T0.ItemCode = '".$_POST['ItemCode']."' AND T0.PriceAfVAT > 0 ORDER BY T0.DocEntry DESC";
	$CostQRY = SAPSelect($CostSQL);
	$CostRST = odbc_fetch_array($CostQRY);
	*/
	/// ใช้ตรงนี้ 
	/*
	$CostSQL = "SELECT TOP 1 CASE WHEN T0.LstEvlPric > 0 THEN T0.LstEvlPric WHEN T0.LastPurPrc = 0 THEN T0.LastPurPrc ELSE 0 END AS 'PriceAfVAT' FROM OITM T0 WHERE T0.ItemCode = '".$_POST['ItemCode']."'";
	$CostQRY = SAPSelect($CostSQL);
	$CostRST = odbc_fetch_array($CostQRY);
	
	$arrCol['CXST'] = conData("A".$CostRST['PriceAfVAT']);
	
	*/
	$CostSQL = "SELECT TOP 1 CASE WHEN T0.LstEvlPric > 0 THEN T0.LstEvlPric WHEN T0.LastPurPrc > 0 THEN T0.LastPurPrc ELSE 0 END AS 'PriceAfVAT' FROM OITM T0 WHERE T0.ItemCode = '".$_POST['ItemCode']."'";
	$CostQRY = conSAP8($CostSQL);
	$CostRST = odbc_fetch_array($CostQRY);
	
	$arrCol['CXST'] = conData("A".$CostRST['PriceAfVAT']);

}

if($_GET['p'] == "SearchDoc") {
	$DocSubstr = explode("-",$_POST['kwd']);
	if(strlen($DocSubstr[0]) == 3) {
		/* HOOK FROM EUROX FORCE */
		$DocType = substr($DocSubstr[0],0,2);
		$DocNum  = $DocSubstr[1];
		$GetItemSQL = 
			"SELECT
				T0.DocEntry, T1.VisOrder,
				T1.ItemCode, T1.CodeBars, T2.ItemName, T2.ProductStatus, IFNULL(T2.DftWhsCode,'KSY') AS 'DftWhsCode', T1.Quantity, T1.UnitMsr,
				T1.GrandPrice, T1.Line_Disc1, T1.Line_Disc2, T1.Line_Disc3, T1.Line_Disc4, T1.UnitPrice
			FROM order_header T0
			LEFT JOIN order_detail T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN OITM T2 ON T1.ItemCode = T2.ItemCode
			WHERE T0.DocType = '$DocType' AND T0.DocNum = '$DocNum'
			ORDER BY T1.VisOrder";
		// echo $GetItemSQL;
		$Rows = CHKRowDB($GetItemSQL);
		if($Rows == 0) {
			$arrCol['Rows'] = 0;
		} else {
			$GetItemQRY = MySQLSelectX($GetItemSQL);
			while($GetItemRST = mysqli_fetch_array($GetItemQRY)) {
				
				$Discount = 0;
				if ($GetItemRST['Line_Disc4'] != NULL && $GetItemRST['Line_Disc4'] != "" && $GetItemRST['Line_Disc4'] != 0.00) {
					$Discount = number_format($GetItemRST['Line_Disc1'], 2) . "-" . number_format($GetItemRST['Line_Disc2'], 2) . "-" . number_format($GetItemRST['Line_Disc3'], 2) . "-" . number_format($GetItemRST['Line_Disc4'], 2);
				} elseif ($GetItemRST['Line_Disc3'] != NULL && $GetItemRST['Line_Disc3'] != "" && $GetItemRST['Line_Disc3'] != 0.00) {
					$Discount = number_format($GetItemRST['Line_Disc1'], 2) . "-" . number_format($GetItemRST['Line_Disc2'], 2) . "-" . number_format($GetItemRST['Line_Disc3'], 2);
				} elseif ($GetItemRST['Line_Disc2'] != NULL && $GetItemRST['Line_Disc2'] != "" && $GetItemRST['Line_Disc2'] != 0.00) {
					$Discount = number_format($GetItemRST['Line_Disc1'], 2) . "-" . number_format($GetItemRST['Line_Disc2'], 2);
				} elseif ($GetItemRST['Line_Disc1'] != NULL && $GetItemRST['Line_Disc1'] != "" && $GetItemRST['Line_Disc1'] != 0.00) {
					$Discount = number_format($GetItemRST['Line_Disc1'], 2);
				} else {
					$Discount = NULL;
				}

				$CostSQL = "SELECT TOP 1 ISNULL(T0.LstEvlPric, T0.LastPurPrc) AS 'PriceAfVAT' FROM OITM T0 WHERE T0.ItemCode = '".$GetItemRST['ItemCode']."'";
				$CostQRY = SAPSelect($CostSQL);
				$CostRST = odbc_fetch_array($CostQRY);

				$DP = GetPriceList($_POST['CardCode'],$GetItemRST['ItemCode'],$GetItemRST['Quantity']);
				$DefaultPrice = AddDecimal($DP,3);

				if(AddDecimal($GetItemRST['UnitPrice'],3) < AddDecimal($DefaultPrice/1.07,3) || $DefaultPrice == 0) {
					$SPPrice = "Y";
				} else {
					$SPPrice = "N";
				}
				if(!isset($CostRST['PriceAfVAT'])) {
					$CXSTTotal = 0;
				} else {
					$CXSTTotal = $CostRST['PriceAfVAT']*$GetItemRST['Quantity'];
				}
				$arrCol[$GetItemRST['VisOrder']]['ItemRow']      = $GetItemRST['ItemCode'];
				$arrCol[$GetItemRST['VisOrder']]['ItemBarCode']  = $GetItemRST['CodeBars'];
				$arrCol[$GetItemRST['VisOrder']]['ItemStatus']   = $GetItemRST['ProductStatus'];
				$arrCol[$GetItemRST['VisOrder']]['ItemName']     = $GetItemRST['ItemName'];
				$arrCol[$GetItemRST['VisOrder']]['ItemWhse']     = $GetItemRST['DftWhsCode'];
				$arrCol[$GetItemRST['VisOrder']]['ItemQuantity'] = $GetItemRST['Quantity'];
				$arrCol[$GetItemRST['VisOrder']]['ItemUnit']     = $GetItemRST['UnitMsr'];
				$arrCol[$GetItemRST['VisOrder']]['GrandPrice']   = $GetItemRST['GrandPrice'];
				$arrCol[$GetItemRST['VisOrder']]['Discount']     = $Discount;
				$arrCol[$GetItemRST['VisOrder']]['PriceAfDisc']  = $GetItemRST['UnitPrice'];
				$arrCol[$GetItemRST['VisOrder']]['LineTotal']    = $GetItemRST['UnitPrice']*$GetItemRST['Quantity'];
				$arrCol[$GetItemRST['VisOrder']]['CXSTTotal']    = $CXSTTotal;
				$arrCol[$GetItemRST['VisOrder']]['SPPrice']      = $SPPrice;
			}
			$arrCol['Rows'] = $Rows;
		}
	} else {
		/* HOOK FROM SAP */
		/* Do Something */
	}

}

if($_GET['p'] == "SaveDraft") {
	$OrderEntry = $_POST['OrderEntry'];
	
	switch($_POST['TaxType']) {
		case "SNV": $DocType = 'SN'; $Suffix = 3; $YDocNum = substr(date("Y"),-2); break;
		default:
			switch($_POST['DocType']) {
				case "SO":
					$DocType = 'SO'; $Suffix = 0; $YDocNum = substr(date("Y")+543,-2);
					break;
				case "SA":
					$DocType = 'SA'; $Suffix = 1; $YDocNum = substr(date("Y"),-2);
					break;
				case "SB":
					$DocType = 'SB'; $Suffix = 2; $YDocNum = substr(date("Y"),-2);
					break;
			}
		break;
	}
	$MDocNum = date("m");
	$DocPrefix = $YDocNum.$MDocNum;
	$GetNameSQL = "SELECT TOP 1 T0.CardName FROM OCRD T0 WHERE T0.CardCode = '".$_POST['CardCode']."'";
	$GetNameQRY = SAPSelect($GetNameSQL);
	$GetNameRST = odbc_fetch_array($GetNameQRY);
	$GetDocSQL = "SELECT T0.DocNum FROM order_header T0 WHERE T0.DocType = '$DocType' AND T0.DocNum LIKE '".$DocPrefix.$Suffix."%' ORDER BY T0.DocEntry DESC LIMIT 1";
	$GetDocRST = MySQLSelect($GetDocSQL);

	if(!isset($GetDocRST['DocNum'])) {
		$NewDocNum = $DocPrefix.$Suffix."0001";
	} else {
		$LastDocNum = intval(substr($GetDocRST['DocNum'],-4));
		$NextDocNum = $LastDocNum+1;
		if($NextDocNum <= 9) {
			$NewSuffix = "000".$NextDocNum;
		} elseif($NextDocNum >= 10 && $NextDocNum <= 99) {
			$NewSuffix = "00".$NextDocNum;
		} elseif($NextDocNum >= 100 && $NextDocNum <= 999) {
			$NewSuffix = "0".$NextDocNum;
		} else {
			$NewSuffix = $NextDocNum;
		}
		$NewDocNum = $DocPrefix.$Suffix.$NewSuffix;
	}

	/* Field In Database */
	/* SOV-YYMM0XXXX / SAV-YYMM1XXXX / SBV-YYMM2XXXX / SNV-YYMM3XXXX */
	if(!isset($_POST['ShippingType'])) {
		$ShippingType = NULL;
	} else {
		$ShippingType  = $_POST['ShippingType'];
	}
	if($_POST['SaveType'] == "1") {
		$DraftStatus = 'N';
	} else {
		$DraftStatus = 'Y';
	}

	if($_POST['ShipComment'] == "") {
		$ShipComment = "NULL";
	} else {
		$ShipComment = "'".$_POST['ShipComment']."'";
	}
	$DocNum        = $NewDocNum;
	$DocType       = $DocType;
	$DraftStatus   = $DraftStatus;
	$DocDate       = $_POST['DocDate'];
	$DocDueDate    = $_POST['DocDueDate'];
	$CardCode      = $_POST['CardCode'];
	$CardName      = conutf8($GetNameRST['CardName']);
	$LicTradeNum   = $_POST['LicTradeNum'];
	$SlpCode       = $_POST['SlpCode'];
	$Payment_Cond  = $_POST['PaymentTerm'];
	$TaxType       = $_POST['TaxType'];
	$BilltoCode    = $_POST['AddressBillTo'];
	$ShiptoCode    = $_POST['AddressShipto'];
	$AddressBillto = str_replace(' (ค่าเริ่มต้น)','',$_POST['AddressBillTo_text']);
	$AddressShipto = str_replace(' (ค่าเริ่มต้น)','',$_POST['AddressShipto_text']);
	$DiscTotal     = $_POST['DiscountSum'];
	$DocTotal      = str_replace(",","",$_POST['DocTotal']);
	$VatSum        = str_replace(",","",$_POST['VatSum']);
	$GrossProfit   = str_replace(",","",$_POST['ProfitTotal']);
	$U_PONo        = $_POST['U_PONo'];
	$ShippingType  = $ShippingType;
	$ShipCostType  = $_POST['ShipCostType'];
	$ShipComment   = $ShipComment;
	$Comments      = $_POST['DocRemark'];
	$CreateUkey    = $_SESSION['ukey'];

	if($OrderEntry == "") {

		/* INSERT HEADER */
		$HeaderSQL =  
			"INSERT INTO order_header SET
				DocNum = '$DocNum',
				DraftStatus = '$DraftStatus',
				DocType = '$DocType',
				DocDate = '$DocDate',
				DocDueDate = '$DocDueDate',
				CardCode = '$CardCode',
				CardName = '$CardName',
				LicTradeNum = '$LicTradeNum',
				SlpCode = '$SlpCode',
				Payment_Cond = '$Payment_Cond',
				TaxType = '$TaxType',
				BilltoCode = '$BilltoCode',
				ShiptoCode = '$ShiptoCode',
				AddressBillto = '$AddressBillto',
				AddressShipto = '$AddressShipto',
				DiscTotal = '$DiscTotal',
				DocTotal = '$DocTotal',
				VatSum = '$VatSum',
				GrossProfit = '$GrossProfit',
				U_PONo = '$U_PONo',
				ShippingType = '$ShippingType',
				ShipCostType = '$ShipCostType',
				ShipComment = $ShipComment,
				Comments = '$Comments',
				CreateUkey = '$CreateUkey'
			";
		$DocEntry = MySQLInsert($HeaderSQL);

		/* INSERT DETAIL */

		$TotalRow = $_POST['TotalRow'];
		$No = 0;
		for($r = 1; $r <= $TotalRow; $r++) {
			if(isset($_POST['ItemRow_'.$r])) {
				$GetName = explode("|",$_POST['ItemName_'.$r]);
				
				$ItemCode = $_POST['ItemRow_'.$r];
				$CodeBars = $_POST['ItemBarCode_'.$r];
				$ItemSQL = "SELECT T0.ItemName, CASE WHEN T0.ProductStatus = '' THEN 'K' ELSE T0.ProductStatus END AS 'ProductStatus' FROM OITM T0 WHERE ItemCode = '$ItemCode' LIMIT 1";
				$ItemRST = MySQLSelect($ItemSQL);
				$ItemName = $GetName[1];
				$ItemStatus = $ItemRST['ProductStatus'];
				$WhsCode  = $_POST['ItemWhse_'.$r];
				$Quantity = str_replace(",","",$_POST['ItemQuantity_'.$r]);
				$UnitMsr = $_POST['ItemUnit_'.$r];

				/* คำนวณราคาก่อนเข้า DB */
				if($TaxType != "S07") {
					$GrandPrice = str_replace(",","",$_POST['GrandPrice_'.$r])/1.07;
					$UnitPrice  = str_replace(",","",$_POST['PriceAfDisc_'.$r])/1.07;
				} else {
					$GrandPrice = str_replace(",","",$_POST['GrandPrice_'.$r]);
					$UnitPrice  = str_replace(",","",$_POST['PriceAfDisc_'.$r]);
				}
				$UnitVat    = $UnitPrice*0.07;
				$Line_Disc0 = "NULL";
				$Line_Disc1 = "NULL";
				$Line_Disc2 = "NULL";
				$Line_Disc3 = "NULL";
				$Line_Disc4 = "NULL";

				if($_POST['Discount_'.$r] != "") {
					$chk_disc = substr($_POST['Discount_'.$r],0,1);
					if($chk_disc != "*") {
						$Discount = explode("-",$_POST['Discount_'.$r]);
						$Line = 1;
						for($d = 0; $d <= sizeof($Discount)-1; $d++) {
							if($Discount[$d] != 0 || $Discount[$d] != "") {
								${"Line_Disc".$Line} = $Discount[$d];
								$Line++;
							}
						}
					} else {
						$Line_Disc0 = substr($_POST['Discount_'.$r],1);
					}
				}

				$LineTotal  = $UnitPrice*$Quantity;
				$LineVatSum = $UnitVat*$Quantity;
				$LineProfit = ($LineTotal)-$_POST['CXSTTotal_'.$r];
				$Line_SP = $_POST['input_spprice_'.$r];
				$Line_CV = $_POST['input_convert_'.$r];
				$Line_BK = $_POST['input_backorder_'.$r];

				$DetailSQL = 
				"INSERT INTO order_detail SET
					DocEntry = $DocEntry,
					VisOrder = $No,
					ItemCode = '$ItemCode',
					CodeBars = '$CodeBars',
					ItemName = '$ItemName',
					ItemStatus = '$ItemStatus',
					WhsCode = '$WhsCode',
					Quantity = $Quantity,
					UnitMsr = '$UnitMsr',
					GrandPrice = $GrandPrice,
					Line_Disc0 = $Line_Disc0,
					Line_Disc1 = $Line_Disc1,
					Line_Disc2 = $Line_Disc2,
					Line_Disc3 = $Line_Disc3,
					Line_Disc4 = $Line_Disc4,
					UnitPrice = $UnitPrice,
					UnitVat = $UnitVat,
					LineTotal = $LineTotal,
					LineVatSum = $LineVatSum,
					LineProfit = $LineProfit,
					Line_SP = '$Line_SP',
					Line_CV = '$Line_CV',
					Line_BK = '$Line_BK',
					CreateUkey = '$CreateUkey'
				;";
				MySQLInsert($DetailSQL);
				$No++;
			}
		}

		/* INSERT ATTACHMENT */
		if(isset($_FILES['OrderAttach']['name'])) {
			$Totals = count($_FILES['OrderAttach']['name'])-1;
			// echo $Totals;
			for($i = 0; $i <= $Totals; $i++) {
				$FileProcess = explode(".",basename($_FILES['OrderAttach']['name'][$i]));
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

				$tmpFilePath = $_FILES['OrderAttach']['tmp_name'][$i];
				if($tmpFilePath != "") {
					$NewFilePath = "../../../../FileAttach/SO/".$DocType."V-".$DocNum."-".$i.".".$FileExt;
					move_uploaded_file($tmpFilePath,$NewFilePath);
					// $DocEntry = 2;

					$AttachSQL = "INSERT INTO order_attach SET
						DocEntry = $DocEntry,
						VisOrder = $i,
						FileOriName = '$FileOriName',
						FileDirName = '".$DocType."V-".$DocNum."-".$i."',
						FileExt = '$FileExt',
						UploadUkey = '$CreateUkey'
					;";
					// echo $AttachSQL;
					MySQLInsert($AttachSQL);
				}
			}
		}
	} else {
		/* UPDATE HEADER */
		$HeaderSQL = 
		"UPDATE order_header SET
			DraftStatus = '$DraftStatus',
			DocDate = '$DocDate',
			DocDueDate = '$DocDueDate',
			LicTradeNum = '$LicTradeNum',
			SlpCode = '$SlpCode',
			BilltoCode = '$BilltoCode',
			ShiptoCode = '$ShiptoCode',
			AddressBillto = '$AddressBillto',
			AddressShipto = '$AddressShipto',
			DiscTotal = '$DiscTotal',
			DocTotal = '$DocTotal',
			VatSum = '$VatSum',
			GrossProfit = '$GrossProfit',
			U_PONo = '$U_PONo',
			ShippingType = '$ShippingType',
			ShipCostType = '$ShipCostType',
			ShipComment = $ShipComment,
			Comments = '$Comments',
			UpdateUkey = '$CreateUkey'
		WHERE DocEntry = $OrderEntry
		;";

		MySQLUpdate($HeaderSQL);

		/* UPDATE DETAIL */
		$DeleteSQL = "DELETE FROM order_detail WHERE DocEntry = $OrderEntry";
		MySQLDelete($DeleteSQL);

		$TotalRow = $_POST['TotalRow'];
		$No = 0;
		for($r = 1; $r <= $TotalRow; $r++) {
			if($_POST['ItemRow_'.$r] != "") {
				$GetName = explode("|",$_POST['ItemName_'.$r]);
				
				$ItemCode = $_POST['ItemRow_'.$r];
				$CodeBars = $_POST['ItemBarCode_'.$r];
				$ItemSQL = "SELECT T0.ItemName, CASE WHEN T0.ProductStatus = '' THEN 'K' ELSE T0.ProductStatus END AS 'ProductStatus' FROM OITM T0 WHERE ItemCode = '$ItemCode' LIMIT 1";
				$ItemRST = MySQLSelect($ItemSQL);
				$ItemName = $GetName[1];
				$ItemStatus = $ItemRST['ProductStatus'];
				$WhsCode  = $_POST['ItemWhse_'.$r];
				$Quantity = str_replace(",","",$_POST['ItemQuantity_'.$r]);
				$UnitMsr = $_POST['ItemUnit_'.$r];

				/* คำนวณราคาก่อนเข้า DB */
				if($TaxType != "S07") {
					$GrandPrice = str_replace(",","",$_POST['GrandPrice_'.$r])/1.07;
					$UnitPrice  = str_replace(",","",$_POST['PriceAfDisc_'.$r])/1.07;
				} else {
					$GrandPrice = str_replace(",","",$_POST['GrandPrice_'.$r]);
					$UnitPrice  = str_replace(",","",$_POST['PriceAfDisc_'.$r]);
				}
				$UnitVat    = $UnitPrice*0.07;
				$Line_Disc0 = "NULL";
				$Line_Disc1 = "NULL";
				$Line_Disc2 = "NULL";
				$Line_Disc3 = "NULL";
				$Line_Disc4 = "NULL";

				if($_POST['Discount_'.$r] != "") {
					$chk_disc = substr($_POST['Discount_'.$r],0,1);
					if($chk_disc != "*") {
						$Discount = explode("-",$_POST['Discount_'.$r]);
						$Line = 1;
						for($d = 0; $d <= sizeof($Discount)-1; $d++) {
							if($Discount[$d] != 0 || $Discount[$d] != "") {
								${"Line_Disc".$Line} = $Discount[$d];
								$Line++;
							}
						}
					} else {
						$Line_Disc0 = substr($_POST['Discount_'.$r],1);
					}
				}

				$LineTotal  = $UnitPrice*$Quantity;
				$LineVatSum = $UnitVat*$Quantity;
				$LineProfit = ($LineTotal-$LineVatSum)-$_POST['CXSTTotal_'.$r];
				$Line_SP = $_POST['input_spprice_'.$r];
				$Line_CV = $_POST['input_convert_'.$r];
				$Line_BK = $_POST['input_backorder_'.$r];

				$DetailSQL = 
				"INSERT INTO order_detail SET
					DocEntry = $OrderEntry,
					VisOrder = $No,
					ItemCode = '$ItemCode',
					CodeBars = '$CodeBars',
					ItemName = '$ItemName',
					ItemStatus = '$ItemStatus',
					WhsCode = '$WhsCode',
					Quantity = $Quantity,
					UnitMsr = '$UnitMsr',
					GrandPrice = $GrandPrice,
					Line_Disc0 = $Line_Disc0,
					Line_Disc1 = $Line_Disc1,
					Line_Disc2 = $Line_Disc2,
					Line_Disc3 = $Line_Disc3,
					Line_Disc4 = $Line_Disc4,
					UnitPrice = $UnitPrice,
					UnitVat = $UnitVat,
					LineTotal = $LineTotal,
					LineVatSum = $LineVatSum,
					LineProfit = $LineProfit,
					Line_SP = '$Line_SP',
					Line_CV = '$Line_CV',
					Line_BK = '$Line_BK',
					CreateUkey = '$CreateUkey'
				;";
				// echo $DetailSQL."<br/>";
				MySQLInsert($DetailSQL);
				$No++;
			}
		}

		/* INSERT NEW ATTACHMENT */
		if(isset($_FILES['OrderAttach']['name'])) {
			$Totals = count($_FILES['OrderAttach']['name'])-1;
			// echo $Totals;
			$NextROWSQL = "SELECT IFNULL(MAX(T0.VisOrder)+1,0) AS 'NextRow', T1.DocType, T1.DocNum FROM order_attach T0 LEFT JOIN order_header T1 ON T0.DocEntry = T1.DocEntry WHERE T1.DocEntry = $OrderEntry";
			$NextROWRST = MySQLSelect($NextROWSQL);
			$row     = $NextROWRST['NextRow'];
			$DocType = $NextROWRST['DocType'];
			$DocNum  = $NextROWRST['DocNum'];
			for($i = 0; $i <= $Totals; $i++) {
				$FileProcess = explode(".",basename($_FILES['OrderAttach']['name'][$i]));
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

				$tmpFilePath = $_FILES['OrderAttach']['tmp_name'][$i];
				if($tmpFilePath != "") {
					$NewFilePath = "../../../../FileAttach/SO/".$DocType."V-".$DocNum."-".$row.".".$FileExt;
					move_uploaded_file($tmpFilePath,$NewFilePath);
					// $DocEntry = 2;

					$AttachSQL = "INSERT INTO order_attach SET
						DocEntry = $OrderEntry,
						VisOrder = $row,
						FileOriName = '$FileOriName',
						FileDirName = '".$DocType."V-".$DocNum."-".$row."',
						FileExt = '$FileExt',
						UploadUkey = '$CreateUkey'
					;";
					// echo $AttachSQL;
					MySQLInsert($AttachSQL);
					$row++;
				}
			}
		}
		$DocEntry = $OrderEntry;
	}
	if($_POST['SaveType'] == "1") {
		require('ajaxApprove.php');
	}else{
		$arrCol['Status'] = 'D';
		$arrCol['errMsg'] = 'บันทึกร่างสำเร็จ';
	}
}

if($_GET['p'] == "EditSO") {
	$DocEntry = $_POST['DocEntry'];
	/* Header SO */
	$HeaderSQL = "SELECT CONCAT(T0.DocType,'V-',T0.DocNum) AS 'DocNum', T0.DocDate, T0.DocDueDate, T0.CardCode, T0.LicTradeNum, T0.SlpCode, T0.Payment_Cond, T0.TaxType, T0.BilltoCode, T0.ShiptoCode, T0.U_PONo, T0.ShippingType, T0.ShipCostType, T0.ShipComment, T0.Comments FROM order_header T0 WHERE T0.DocEntry = $DocEntry LIMIT 1";
	$HeaderRST = MySQLSelect($HeaderSQL);
	$arrCol['DocNum']       = $HeaderRST['DocNum'];
	$arrCol['DocDate']      = date("Y-m-d",strtotime($HeaderRST['DocDate']));
	$arrCol['DocDueDate']   = date("Y-m-d",strtotime($HeaderRST['DocDueDate']));
	$arrCol['CardCode']     = $HeaderRST['CardCode'];
	$arrCol['LicTradeNum']  = $HeaderRST['LicTradeNum'];
	$arrCol['SlpCode']      = $HeaderRST['SlpCode'];
	$arrCol['Payment_Cond'] = $HeaderRST['Payment_Cond'];
	$arrCol['TaxType']      = $HeaderRST['TaxType'];
	$arrCol['BilltoCode']   = $HeaderRST['BilltoCode'];
	$arrCol['ShiptoCode']   = $HeaderRST['ShiptoCode'];
	$arrCol['U_PONo']       = $HeaderRST['U_PONo'];
	$arrCol['ShippingType'] = $HeaderRST['ShippingType'];
	$arrCol['ShipCostType'] = $HeaderRST['ShipCostType'];
	$arrCol['ShipComment']  = $HeaderRST['ShipComment'];
	$arrCol['Comments']     = $HeaderRST['Comments'];

	/* Attachment */
	$AttachSQL = "SELECT T0.AttachID, T0.FileOriName, T0.FileDirName, T0.FileExt FROM order_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileStatus = 'A'";
	$AttachROW = ChkRowDB($AttachSQL);
	if($AttachROW == 0) {
		$AttLine = "<tr><td colspan='3' class='text-center'>ไม่มีเอกสารแนบ :(</td></tr>";
	} else {
		$AttachQRY = MySQLSelectX($AttachSQL);
		$AttLine = "";
		$no = 1;
		while($AttachRST = mysqli_fetch_array($AttachQRY)) {
			$AttLine .= "<tr>";
				$AttLine .= "<td class='text-right'>".number_format($no,0)."</td>";
				$AttLine .= "<td>".$AttachRST['FileOriName'].".".$AttachRST['FileExt']."</td>";
				$AttLine .= "<td class='text-center'><a href='javascript:void(0);' class='btn btn-danger btn-sm' onclick='DeleteAttach($DocEntry,".$AttachRST['AttachID'].");'><i class='fas fa-trash fa-fw fa-1x'></i></a></td>";
			$AttLine .= "</tr>";
			$no++;
		}
	}
	$arrCol['AttachList'] = $AttLine;
}

if($_GET['p'] == "GetAttach") {
	$DocEntry = $_POST['DocEntry'];
	/* Attachment */
	$AttachSQL = "SELECT T0.AttachID, T0.FileOriName, T0.FileDirName, T0.FileExt FROM order_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileStatus = 'A'";
	$AttachROW = ChkRowDB($AttachSQL);
	if($AttachROW == 0) {
		$AttLine = "<tr><td colspan='3' class='text-center'>ไม่มีเอกสารแนบ :(</td></tr>";
	} else {
		$AttachQRY = MySQLSelectX($AttachSQL);
		$AttLine = "";
		$no = 1;
		while($AttachRST = mysqli_fetch_array($AttachQRY)) {
			$AttLine .= "<tr>";
				$AttLine .= "<td class='text-right'>".number_format($no,0)."</td>";
				$AttLine .= "<td>".$AttachRST['FileOriName'].".".$AttachRST['FileExt']."</td>";
				$AttLine .= "<td class='text-center'><a href='javascript:void(0);' class='btn btn-danger btn-sm' onclick='DeleteAttach($DocEntry,".$AttachRST['AttachID'].");'><i class='fas fa-trash fa-fw fa-1x'></i></a></td>";
			$AttLine .= "</tr>";
			$no++;
		}
	}
	$arrCol['AttachList'] = $AttLine;
}

if($_GET['p'] == "DelAttach") {
	$AttachID = $_POST['AttachID'];
	$DelAttSQL = "UPDATE order_attach SET FileStatus = 'I' WHERE AttachID = $AttachID";
	$DelAttQRY = MySQLUpdate($DelAttSQL);
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
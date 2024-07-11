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

function DocTypeName($MainType) {
	switch($MainType) {
		case "R": $TypeName = "ฝากรับสินค้า"; break;
		case "S": $TypeName = "ฝากส่งสินค้า"; break;
		case "B": $TypeName = "ฝากเบิกสินค้า"; break;
	}
	return $TypeName;
}

function SubTypeName($SubType) {
	switch($SubType) {
		case "RP": $TypeName = "ฝากรับสินค้าที่ฝากซื้อ"; break;
		case "RD": $TypeName = "รับสินค้าคืนที่ MT / ขนส่ง / ไปรษณีย์"; break;
		case "RR": $TypeName = "ฝากรับสินค้าซ่อม"; break;
		case "SP": $TypeName = "ฝากส่งสินค้าให้ลูกค้า"; break;
		case "SQ": $TypeName = "ฝากส่งสินค้าที่ไม่รับคืน เคลม เปลี่ยน (จาก QC)"; break;
		default  : $TypeName = NULL;
	}
	return $TypeName;
}

function ShipmentType($ShipType) {
	switch($ShipType) {
		case 1:  $TypeName = "บริษัทฯ เป็นผู้จ่ายค่าขนส่ง"; break;
		case 2:  $TypeName = "ปลายทางเป็นผู้จ่ายค่าขนส่ง"; break;
		default: $TypeName = "ไม่มีค่าใช้จ่าย"; break;
	}
	return $TypeName;
}

if($_GET['p'] == "GetShipName") {
	$GetSQL = "SELECT T0.Code, T0.Name, T0.U_TelNo, T0.U_Address FROM [dbo].[@SHIPPINGTYPE] T0 ORDER BY T0.Name ASC";
	$Rows   = ChkRowSAP($GetSQL);
	if($Rows > 0) {
		$GetQRY = SAPSelect($GetSQL);
		while($GetRST = odbc_fetch_array($GetQRY)) {
			if($GetRST['Code'] == "814") {
				$slct = " selected";
			} else {
				$slct = NULL;
			}
			$output .= "<option value='".conutf8($GetRST['Code'])."' data-TelNo='".conutf8($GetRST['U_TelNo'])."' data-Address='".conutf8($GetRST['U_Address'])."' $slct>".conutf8($GetRST['Name'])."</option>";
		}
		$arrCol['ShipName'] = $output;
	}
}

if($_GET['p'] == "GetCardName") {
	$GetSQL = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE (T0.CardCode != '' OR T0.CardName != '') AND T0.CardStatus = 'A' ORDER BY T0.CardCode";
	$Rows   = ChkRowDB($GetSQL);
	if($Rows > 0) {
		$GetQRY = MySQLSelectX($GetSQL);
		while($GetRST = mysqli_fetch_array($GetQRY)){
			$CardName = $GetRST['CardCode']." | ".$GetRST['CardName'];
			$output .= "<option value='$CardName' />";
		}
		$arrCol['CardName'] = $output;
	}
}

if($_GET['p'] == "GetItemList") {
	$GetSQL = "SELECT T0.ItemCode, T0.ItemName FROM OITM T0 WHERE (T0.ItemCode != '' AND T0.ItemCode != '0' AND T0.ItemCode NOT LIKE '00-%') ORDER BY T0.ItemCode ASC";
	$Rows   = ChkRowDB($GetSQL);
	if($Rows > 0) {
		$GetQRY = MySQLSelectX($GetSQL);
		while($GetRST = mysqli_fetch_array($GetQRY)) {
			$ItemName = $GetRST['ItemCode']." | ".$GetRST['ItemName'];
			$output .= "<option value='$ItemName' />";
		}
		$arrCol['ItemName'] = $output;
	}
}

if($_GET['p'] == "GetCardDetail") {
	$CardCode = $_POST['CardCode'];
	$GetSQL = "SELECT TOP 1 T0.ShipToDef, T0.Address, T0.Block, T0.City, T0.ZipCode, T0.Phone1, T0.U_ShippingType FROM OCRD T0 WHERE T0.CardCode = '$CardCode'";
	$Rows   = ChkRowSAP($GetSQL);
	if($Rows > 0) {
		$GetQRY = SAPSelect($GetSQL);
		$GetRST = odbc_fetch_array($GetQRY);

		$arrCol['CardAddress'] = conutf8($GetRST['ShipToDef']."\n".$GetRST['Address']."\n".$GetRST['Block']."\n".$GetRST['City']."\n".$GetRST['ZipCode']);
		$arrCol['CardTelNo']   = conutf8($GetRST['Phone1']);
		$arrCol['CardShip']    = conutf8($GetRST['U_ShippingType']);
		$arrCol['GetStatus']   = "SUCCESS";
	} else {
		$arrCol['GetStatus']   = "ERR";
	}
}

if($_GET['p'] == "SaveDoc") {
	$YDocNum   = substr(date("Y")+543,-2);
	$MDocNum   = date("m");
	$Prefix    = "WO-".$YDocNum.$MDocNum;
	$GetDocSQL = "SELECT SUBSTRING(T0.DocNum,8,4)+1 AS 'DocNum' FROM OWAS T0 WHERE T0.DocNum LIKE '$Prefix%' ORDER BY T0.DocEntry DESC LIMIT 1";
	$Rows      = ChkRowDB($GetDocSQL);
	if($Rows > 0) {
		$GetDocRST = MySQLSelect($GetDocSQL);
		$NextDocNum = $GetDocRST['DocNum'];
		if($NextDocNum <= 9) {
			$NewSuffix = "000".$NextDocNum;
		} elseif($NextDocNum >= 10 && $NextDocNum <= 99) {
			$NewSuffix = "00".$NextDocNum;
		} elseif($NextDocNum >= 100 && $NextDocNum <= 999) {
			$NewSuffix = "0".$NextDocNum;
		} else {
			$NewSuffix = $NextDocNum;
		}
	} else {
		$NewSuffix = "0001";
	}

	$CardArr     = explode(" | ",$_POST['ContactName']);
	if(sizeof($CardArr) == 2) {
		$CardCode = "'".$CardArr[0]."'";
		$CardName = $CardArr[1];
	} else {
		$CardCode = "NULL";
		$CardName = $_POST['ContactName'];
	}
	if($_POST['ShippingName'] != "") {
		$ShippingName = SapTHSearch($_POST['ShippingName']);
		$GetSQL   = "SELECT TOP 1 T0.U_Name FROM [dbo].[@SHIPPINGTYPE] T0 WHERE T0.Code LIKE N'%".$ShippingName."%'";
		// echo $GetSQL;
		$GetQRY   = SAPSelect($GetSQL);
		$GetRST   = odbc_fetch_array($GetQRY);
		$ShipName = "'".conutf8($GetRST['U_Name'])."'";
	} else {
		$ShipName = "NULL";
	}
	$DeptCode = $_SESSION['DeptCode'];
	switch($DeptCode) {
		case "DP005": $CodeTeam = "TT2"; break;
		case "DP006": $CodeTeam = "MT1"; break;
		case "DP007": $CodeTeam = "MT2"; break;
		case "DP008": $CodeTeam = "OUL"; break;
		default:
			$LvCode = $_SESSION['LvCode'];
			switch($LvCode) {
				case "LV018":
				case "LV019":
					$CodeTeam = "ONL";
					break;
				default: $CodeTeam = "KBI"; break;
			}
		break;
	}

	if(isset($_POST['SubDocType'])) {
		$TypeDetail = $_POST['SubDocType'];
	} else {
		$TypeDetail = "";
	}

	$DocNum         = $Prefix.$NewSuffix;
	$CreateUkey     = $_SESSION['ukey'];
	$MainDocType    = $_POST['MainDocType'];
	
	$TotalBox       = $_POST['TotalBox'];
	$CusCode        = $CardCode;
	$CusName        = $CardName;
	$CusAddress     = $_POST['ContactAddress'];
	$PaidTotal      = $_POST['ShippingCost'];
	$PaidType       = $_POST['ShippingType'];
	$LogiName       = $ShipName;
	$Remark         = $_POST['DocDetail'];
	$TeamCode       = $CodeTeam;
	$NameContrac    = $_POST['ContactPerson'];
	$Phone          = $_POST['ContactTel'];
	$LogiPhone      = $_POST['ShippingTel'];
	$TimeContrac    = $_POST['DocDueDate'];
	$AddressContrac = $_POST['ShippingAddress'];
	/* StatusDoc
		0 = ยกเลิก
		1 = เอกสารใหม่
		2 = เอกสารสมบูรณ์
		3 = รออนุมัติ
	*/
	if($PaidTotal >= 500 && ($DeptCode == "DP005" || $DeptCode == "DP010")) {
		/* Approve */
		$StatusDoc = 3;
	} else {
		$StatusDoc = 2;
	}
	// echo $StatusDoc;

	/* INSERT INTO OWAS (HEADER) */
	$InsertSQL = 
		"INSERT INTO OWAS SET
			DocNum = '$DocNum',
			DateCreate = NOW(),
			UserCreate = '$CreateUkey',
			TypeOrder = '$MainDocType',
			TypeDetail = '$TypeDetail',
			Attatach = 'N',
			AttOpt1 = 'N',
			AttOpt2 = 'N',
			AttOpt3 = 'N',
			AttOpt3Remark = '',
			TotalBox = $TotalBox,
			CusCode = $CusCode,
			CusName = '$CusName',
			CusAddress = '$CusAddress',
			PaidTotal = $PaidTotal,
			PaidType = $PaidType,
			LogiName = $LogiName,
			Remark = '$Remark',
			TeamCode = '$TeamCode',
			NameContrac = '$NameContrac',
			Phone = '$Phone',
			LogiPhone = '$LogiPhone',
			TimeContrac = '".date("Y-m-d",strtotime($TimeContrac))."',
			AddressContrac = '$AddressContrac',
			StatusDoc = $StatusDoc";
	// echo $InsertSQL;
	$DocEntry = MySQLInsert($InsertSQL);
	// $DocEntry = 2738;

	/* INSERT INTO WAS1 (DETAIL) */
	$TotalRow = $_POST['TotalRow'];

	$ItemSuffix = 1;
	$row = 1;
	for($i=1;$i<=$TotalRow;$i++) {
		if(isset($_POST['ItemCode_'.$i]) || isset($_POST['ItemCode_'.$i])) {
			if($_POST['ItemCode_'.$i] != "") {
				/* CreateNewItemCode */
				$GetBarSQL = "SELECT T0.BarCode FROM OITM T0 WHERE T0.ItemCode = '".$_POST['ItemCode_'.$i]."' LIMIT 1";
				$GetBarRST = MySQLSelect($GetBarSQL);

				$ItemCode = $_POST['ItemCode_'.$i];
				$BarCode  = $GetBarRST['BarCode'];
			} else {
				if($ItemSuffix <= 9) {
					$Suffix = "00".$ItemSuffix;
				} elseif($ItemSuffix >= 10 && $ItemSuffix <= 99) {
					$Suffix = "0".$ItemSuffix;
				} else {
					$Suffix = $ItemSuffix;
				}

				$ItemCode = "PW-000-".$Suffix;
				$BarCode  = $ItemCode;
				$ItemSuffix++;
			}
			$ItemName = $_POST['ItemName_'.$i];
			$UnitMgr  = $_POST['ItemUnitMsr_'.$i];
			$WhsCode  = $_POST['ItemWhsCode_'.$i];
			$Qty      = $_POST['ItemQuantity_'.$i];
			$Remark   = $_POST['Remark_'.$i];
			$InsertSQL = 
				"INSERT INTO WAS1 SET
					DocEntry = $DocEntry,
					lnNum = $row,
					ItemCode = '$ItemCode',
					BarCode = '$BarCode',
					ItemName = '$ItemName',
					UnitMgr = '$UnitMgr',
					WhsCode = '$WhsCode',
					Qty = $Qty,
					Remark = '$Remark',
					DateCreate = NOW(),
					UkeyCreate = '$CreateUkey',
					StatusDoc = 1";
			// echo $InsertSQL;
			MySQLInsert($InsertSQL);
			$row++;
		}
	}

	/* INSERT INTO WAS2 (ATTACHMENT) FileAttach */
	if(isset($_FILES['FileAttach']['name'])) {
		$Totals = count($_FILES['FileAttach']['name'])-1;
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
				$NewFilePath = "../../../../FileAttach/WHORDER/".$DocNum."-".$i.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				// $DocEntry = 2;
				$FileName = $DocNum."-".$i.".".$FileExt;
				$NameShow = $FileOriName.".".$FileExt;
				$AttachSQL =
					"INSERT INTO WAS2 SET
						DocEntry = $DocEntry,
						FileName = '$FileName',
						NameShow = '$NameShow',
						StatusLine = 1,
						lnNum = $i";
				// echo $AttachSQL;
				MySQLInsert($AttachSQL);
			}
		}
	}
}

if($_GET['p'] == "GetOrderList") {
	$filt_year  = $_POST['y'];
	$filt_month = $_POST['m'];
	$filt_team  = $_POST['t'];

	if($filt_team == "ALL") {
		$OrderWhr = "";
	} else {
		$OrderWhr = " AND T3.DeptCode = '$filt_team' ";
	}

	$GetSQL = 
		"SELECT
			T0.DocEntry, DATE(T0.DateCreate) AS 'DocDate', DATE(T0.TimeContrac) AS 'DocDueDate', T0.DocNum, T0.TypeOrder,
			T0.CusCode, T0.CusName, T0.Remark, T4.DeptCode, T4.DeptName, T0.StatusDoc, T1.StatusApp,
			CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName'
		FROM OWAS T0
		LEFT JOIN WAS3 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN users T2 ON T0.UserCreate = T2.ukey
		LEFT JOIN positions T3 ON T2.LvCode = T3.LvCode
		LEFT JOIN departments T4 ON T3.DeptCode = T4.DeptCode
		WHERE (YEAR(T0.DateCreate) = $filt_year AND MONTH(T0.DateCreate) = $filt_month) $OrderWhr ORDER BY T0.DocEntry";
	$Rows = ChkRowDB($GetSQL);
	$i = 1;
	if($Rows == 0) {
		$output .= "<tr><td colspan='8' class='text-center'>ไม่มีผลลัพธ์</td></tr>";
	} else {
		$GetQRY = MySQLSelectX($GetSQL);
		/*
			int_status
			0 = ยกเลิก      StatusDoc = 0
			1 = แบบร่าง
			2 = รออนุมัติ     StatusDoc = 3 AND StatusApp = NULL
			3 = อนุมัติ       (StatusDoc = 2 AND StatusApp = 'Y'
			4 = ไม่อนุมัติ     StatusDoc = 2 AND StatusApp = 'N'
			5 = เสร็จสมบูรณ์  StatusDoc = 5 AND StatusApp = 'Y'
		*/
		while($GetRST = mysqli_fetch_array($GetQRY)) {
			if($GetRST['DocDueDate'] == "" || $GetRST['DocDueDate'] == NULL) {
				$DocDueDate = NULL;
			} else {
				$DocDueDate = date("d/m/Y",strtotime($GetRST['DocDueDate']));
			}
			if($GetRST['CusCode'] == "" || $GetRST['CusCode'] == NULL) {
				$ShowCard = $GetRST['CusName'];
			} else {
				$ShowCard = $GetRST['CusCode']." | ".$GetRST['CusName'];
			}

			if($GetRST['StatusDoc'] == 0) {
				$int_status = 0;
			}
			if($GetRST['StatusDoc'] == 3 && $GetRST['StatusApp'] == NULL) {
				$int_status = 2;
			}
			if($GetRST['StatusDoc'] == 2) {
				if($GetRST['StatusApp'] == "Y" || $GetRST['StatusApp'] == NULL) {
					$int_status = 3;
				} else {
					$int_status = 4;
				}
			}
			if($GetRST['StatusDoc'] == 5 || $GetRST['StatusDoc'] == 14) {
				$int_status = 5;
			}
			//echo $i."/".$int_status."<br/>";
			$i++;

			$RowCls = NULL;
			$dis_prnt = NULL;
			$dis_impt = NULL;

			switch($int_status) {
				case 0:
					$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
					$RowCls = " class='table-secondary text-muted'";
					break;
				case 1:
					$txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
					$dis_prnt = " disabled";
					$dis_impt = " disabled";
					break;
				case 1.5:
					$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
					$dis_prnt = " disabled";
					$dis_impt = " disabled";
					break;
				case 2:
					$txt_status = "<span class='badge bg-warning w-100'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>";
					$dis_prnt = " disabled";
					$dis_impt = " disabled";
					break;
				case 3:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
					break;
				case 4:
					$txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
					$dis_prnt = " disabled";
					$dis_impt = " disabled";
					break;
				case 5:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
					$RowCls = " class='table-success text-success'";
					break;
			}

			if($int_status != 0) {
				$txt_opt = "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
					$txt_opt.= "<i class='fas fa-cog fa-fw fa-1x'></i>";
				$txt_opt.= "</button>";
				$txt_opt.= "<ul class='dropdown-menu' style='font-size: 13px;'>";
					$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item' onclick='PreviewDoc(".$GetRST['DocEntry'].",$int_status)'><i class='fas fa-info fa-fw fa-1x'></i> รายละเอียด</a></li>";
					$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item$dis_prnt' onclick='PrintDoc(".$GetRST['DocEntry'].",\"".$GetRST['TypeOrder']."\")'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์เอกสาร</a></li>";
					$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item$dis_impt' onclick='ExportDoc(".$GetRST['DocEntry'].")'><i class='fas fa-share-square fa-fw fa-1x'></i> ส่งฝ่ายคลัง</a></li>";
					$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item' onclick='CancelDoc(".$GetRST['DocEntry'].")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกเอกสาร</a></li>";
				$txt_opt.= "</ul>";
		} else {
			$txt_opt = "";
			$row_cls = " class='table-active text-secondary'";
		}
			$output .= "<tr$RowCls>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($GetRST['DocDate']))."</td>";
				$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='PreviewDoc(".$GetRST['DocEntry'].",$int_status);'>".$GetRST['DocNum']."</a></td>";
				$output .= "<td class='text-center'>$DocDueDate</td>";
				$output .= "<td>".DocTypeName($GetRST['TypeOrder'])."</td>";
				$output .= "<td><strong>$ShowCard</strong><br/><small>รายละเอียด: ".iconv_substr($GetRST['Remark'],0,96,'UTF-8')."</small></td>";
				$output .= "<td>".$GetRST['DeptName']."<br/><small>ผู้จัดทำ: ".$GetRST['CreateName']."</small></td>";
				$output .= "<td>$txt_status</td>";
				$output .= "<td class='text-center'>$txt_opt</td>";
			$output .= "</tr>";
		}
	}
	$arrCol['OrderList'] = $output;
}

if($_GET['p'] == "CancelDoc") {
	$DocEntry = $_POST['DocEntry'];

	$CancelSQL = "UPDATE OWAS SET StatusDoc = 0, ukeyUpdate = '".$_SESSION['ukey']."', LastUpdate = NOW() WHERE DocEntry = $DocEntry";
	$CancelQRY = MySQLUpdate($CancelSQL);
	if(!isset($CancelQRY)) {
		echo "ERROR";
	} else {
		echo "SUCCESS";
	}
}

if($_GET['p'] == "PreviewDoc") {
	$DocEntry = $_POST['DocEntry'];
	$HeaderSQL = 
	"SELECT
		T0.DocEntry, T0.DocNum, DATE(T0.DateCreate) AS 'DocDate', DATE(T0.TimeContrac) AS 'DocDueDate',
		CONCAT(T1.uName,' ',T1.uLastName) AS 'CreateName', T3.DeptName, T0.TypeOrder, T0.TypeDetail,
		T0.CusCode, T0.CusName, T0.CusAddress, T0.NameContrac, T0.Phone, T0.AddressContrac, T0.Remark,
		T0.LogiName, T0.LogiPhone, T0.AddressContrac, T0.PaidType, T0.PaidTotal, T0.TotalBox 
	FROM OWAS T0
	LEFT JOIN users T1 ON T0.UserCreate = T1.ukey
	LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
	LEFT JOIN departments T3 ON T2.DeptCode = T3.DeptCode
	WHERE T0.DocEntry = $DocEntry LIMIT 1";
	$HeaderRST = MySQLSelect($HeaderSQL);

	$arrCol['WOEntry']    = $HeaderRST['DocEntry'];
	$arrCol['TypeOrder']    = $HeaderRST['TypeOrder'];

	$arrCol['DocNum']     = $HeaderRST['DocNum'];
	$arrCol['DocDate']    = date("d/m/Y",strtotime($HeaderRST['DocDate']));
	if($HeaderRST['DocDueDate'] == NULL) {
		$arrCol['DocDueDate'] = NULL;
	} else {
		$arrCol['DocDueDate'] = date("d/m/Y",strtotime($HeaderRST['DocDueDate']));
	}
	$arrCol['CreateName'] = $HeaderRST['CreateName'];
	$arrCol['DeptName']   = $HeaderRST['DeptName'];
	switch($HeaderRST['TypeOrder']) {
		case "B": $arrCol['DocType'] = DocTypeName($HeaderRST['TypeOrder']); break;
		default:  $arrCol['DocType'] = DocTypeName($HeaderRST['TypeOrder'])." &gt; ".SubTypeName($HeaderRST['TypeDetail']);
	}
	if($HeaderRST['CusCode'] == NULL) {
		$arrCol['CusCode'] = $HeaderRST['CusName'];
	} else {
		$arrCol['CusCode'] = $HeaderRST['CusCode']." | ".$HeaderRST['CusName'];
	}
	$arrCol['ContactName'] = $HeaderRST['NameContrac'];
	$arrCol['ContactTel']  = $HeaderRST['Phone'];
	$arrCol['CusAddress']  = str_replace("\n"," ",$HeaderRST['CusAddress']);
	$arrCol['DocDetail']   = $HeaderRST['Remark'];

	$DetailSQL = "SELECT T0.ItemCode, T0.ItemName, T0.WhsCode, T0.Qty, T0.UnitMgr, T0.Remark FROM WAS1 T0 WHERE T0.DocEntry = $DocEntry ORDER BY T0.lnNum ASC";
	$Rows = ChkRowDB($DetailSQL);
	if($Rows == 0) {
		$output .= "<tr><td colspan='7' class='text-center'>ไม่มีข้อมูลสินค้าฝากรับ/ส่ง</td></tr>";
	} else {
		$DetailQRY = MySQLSelectX($DetailSQL);
		$row = 0;
		while($DetailRST = mysqli_fetch_array($DetailQRY)) {
			$row++;
			$output .= "<tr>";
				$output .= "<td class='text-right'>".number_format($row,0)."</td>";
				$output .= "<td class='text-center'>".$DetailRST['ItemCode']."</td>";
				$output .= "<td>".$DetailRST['ItemName']."</td>";
				$output .= "<td class='text-center'>".$DetailRST['WhsCode']."</td>";
				$output .= "<td class='text-right'>".number_format($DetailRST['Qty'],0)."</td>";
				$output .= "<td>".$DetailRST['UnitMgr']."</td>";
				$output .= "<td>".$DetailRST['Remark']."</td>";
			$output .= "</tr>";
		}
	}
	$arrCol['ItemList']    = $output;

	$arrCol['LogiName']    = $HeaderRST['LogiName'];
	$arrCol['LogiPhone']   = $HeaderRST['LogiPhone'];
	$arrCol['LogiAddress'] = $HeaderRST['AddressContrac'];
	$arrCol['LogiCost']    = ShipmentType($HeaderRST['PaidType'])." (<strong>".number_format($HeaderRST['PaidTotal'],2)."</strong> บาท)";
	$arrCol['TotalBox']    = $HeaderRST['TotalBox'];

	$AttSQL = "SELECT * FROM WAS2 T0 WHERE T0.DocEntry = $DocEntry AND T0.StatusLine = 1 ORDER BY T0.lnNum ASC";
	// echo $AttSQL;
	$Rows   = ChkRowDB($AttSQL);
	if($Rows == 0) {
		$Att = "<span class='text-muted'>ไม่มีเอกสารแนบ</span>";
	} else {
		$AttQRY = MySQLSelectX($AttSQL);
		$Att  = "<ul class='fa-ul'>";
		while($AttRST = mysqli_fetch_array($AttQRY)) {
			$Att .= "<li><a href='../FileAttach/WHORDER/".$AttRST['FileName']."' target='_blank'><span class='fa-li'><i class='fas fa-paperclip fa-fw fa-1x'></i></span> ".$AttRST['NameShow']."</a></li>";
		}
		$Att .= "</ul>";
	}
	$arrCol['AttList'] = $Att;

	if(isset($_GET['App']) == "Y") {
		$App = "";
		$App .= "<hr/><div class='row'><div class='table-responsive'>";
		$App .= "<table class='table table-bordered table-sm' style='font-size: 12px;'>";
			$App .= "<thead class='text-center'>";
				$App .= "<th width='7.5%'>ลำดับที่</th>";
				$App .= "<th width='15%'>ผู้อนุมัติ</th>";
				$App .= "<th width='15%'>ผลการ<br/>พิจารณา</th>";
				$App .= "<th>หมายเหตุ</th>";
				$App .= "<th width='7.5%'>บันทึก</th>";
			$App .= "</thead>";
			$App .= "<tbody>";
				$App .= "<tr>";
					$App .= "<td class='text-right'>1</td>";
					$App .= "<td>".$_SESSION['uName']." ".$_SESSION['uLastName']."</td>";
					$App .= "<td class='text-center'><select class='form-select form-select-sm' name='AppState_' id='AppState_'><option value='1' selected>รอพิจารณา</option><option value='Y'>อนุมัติ</option><option value='N'>ไม่อนุมัติ</option></select></td>";
					$App .= "<td><input class='form-control form-control-sm' name='Remark_' id='Remark_' placeholder='ระบุเหตุผลการพิจารณา' /></td>";
					$App .= "<td class='text-center'><button type='button' class='btn btn-success btn-save btn-sm btn-block' onclick='AppDoc(".$DocEntry.")'><i class='fas fa-save fa-fw fa-1x'></i></button></td>";
				$App .= "</tr>";
			$App .= "</tbody>";
		$App .= "</table>";
		$App .= "</div></div>";
		$arrCol['AppName'] = $App;
	}
}

if($_GET['p'] == "ExportDoc") {
	$DocEntry = $_POST['DocEntry'];

	$GetDocSQL = "SELECT T0.DocEntry, T0.DocNum, T0.UserCreate, DATE(T0.DateCreate) AS 'DocDate', DATE(T0.TimeContrac) AS 'DocDueDate', T0.CusCode, T0.CusName FROM OWAS T0 WHERE T0.DocEntry = $DocEntry LIMIT 1";
	$Rows     = ChkRowDB($GetDocSQL);
	if($Rows == 0) {
		$arrCol['AddStatus'] = "ERR::NO_RESULT";
	} else {
		$GetDocRST = MySQLSelect($GetDocSQL);
		$WOEntry = $GetDocRST['DocEntry'];
		$DocNum  = $GetDocRST['DocNum'];
		$DocDate = date("Y-m-d",strtotime($GetDocRST['DocDate']));
		if($GetDocRST['CusCode'] == "") {
			$CusCode = "NULL";
		} else {
			$CusCode = "'".$GetDocRST['CusCode']."'";
		}
		$CusName = $GetDocRST['CusName'];
		$CreateUkey = $GetDocRST['UserCreate'];
		// $DocDueDate = date("Y-m-d-",strtotime($GetDocRST['DocDueDate']));
		/* Check Duplicate */
		$ChkSQL = "SELECT T0.DocEntry FROM docwho_header T0 WHERE T0.DocNum = '$DocNum' AND T0.WOEntry = $WOEntry AND T0.RecipientStatus IN ('1','Y') AND T0.DocStatus = 'O'";
		$Rows   = ChkRowDB($ChkSQL);
		if($Rows > 0) {
			$arrCol['AddStatus'] = "ERR::DUPLICATE";
		} else {
			$InsertSQL = "INSERT INTO docwho_header SET DocNum = '$DocNum', WOEntry = $WOEntry, DocDate = '$DocDate', CardCode = $CusCode, CardName = '$CusName', SenderUkey = '$CreateUkey'";
			// echo $InsertSQL;
			$InsertID  = MySQLInsert($InsertSQL);
			if($InsertID > 0) {
				$arrCol['AddStatus'] = "SUCCESS";
			} else {
				$arrCol['AddStatus'] = "ERR::CANNOT_INSERT";
			}
		}
	}
}



array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
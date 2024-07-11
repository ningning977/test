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

function utf8_strlen($s) {
    $c = strlen($s); $l = 0;
    for ($i = 0; $i < $c; ++$i) if ((ord($s[$i]) & 0xC0) != 0x80) ++$l;
    return $l;
}

if($_GET['a'] == 'GetTeam') {
	switch($_SESSION['DeptCode']) {
		case "DP005": $SqlWhr = "<option value='TT2' selected>ทีม TT2</option>"; break; // TT2
		case "DP006": $SqlWhr = "<option value='MT1' selected>ทีม MT1</option>"; break; // MT1
		case "DP007": $SqlWhr = "<option value='MT2' selected>ทีม MT2</option>"; break; // MT2
		case "DP008": $SqlWhr = "<option value='OUL+TT1' selected>ทีม OUL + TT1</option>"; break; // OUL
		case "DP003":
			switch($_SESSION['LvCode']) {
				case "LV104":
				case "LV105":
				case "LV106":
					$SqlWhr = "<option value='ONL' selected>ทีม ONL</option>";
				break;
				default: $SqlWhr = ""; break;
			}
		break; // ONL
		default: $SqlWhr = ""; break; // ALL
	}
	if($SqlWhr == "") {
		$SqlWhr = "
		<option value='ALL' selected>เลือกทุกทีม</option>
		<option value=\"'MT1'\">ทีม MT1</option>
		<option value=\"'MT2'\">ทีม MT2</option>
		<option value=\"'TT2'\">ทีม TT2</option>
		<option value=\"'OUL','TT1'\">ทีม OUL + TT1</option>
		<option value=\"'ONL'\">ทีม ONL</option>"; 
	}
	$arrCol['option'] = $SqlWhr;
}

if($_GET['a'] == 'GetOrderTracking') {
	$Yaer = $_POST['Year'];
	$Month = $_POST['Month'];
	$Team = ($_POST['Team'] == 'ALL') ? "" : "AND T3.U_Dim1 IN (".$_POST['Team'].")";

	$SQL1 = "
		SELECT DISTINCT
			T0.CardCode, T0.CardName, T3.SlpName, T3.U_Dim1,
			T0.DocEntry AS 'SoEntry', 'ORDR' AS 'SoType', (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'SoDocNum', T0.DocDate AS 'SODocDate', (T0.DocTotal-T0.VatSum) AS 'DocTotal', T0.CANCELED AS 'SOCancel', T0.DocStatus AS 'SoStatus',
			CASE WHEN T1.TargetType = 13 THEN T4.DocEntry WHEN T1.TargetType = 15 THEN T6.DocEntry ELSE NULL END AS 'BillEntry', CASE WHEN T1.TargetType = 13 THEN 'OINV' WHEN T1.TargetType = 15 THEN 'ODLN' ELSE NULL END AS 'BillType',
			CASE WHEN T1.TargetType = 13 THEN (ISNULL(T5.BeginStr,'IV-')+CAST(T4.DocNum AS VARCHAR)) WHEN T1.TargetType = 15 THEN (T7.BeginStr+CAST(T6.DocNum AS VARCHAR)) ELSE NULL END AS 'BillDocNum',
			CASE WHEN T1.TargetType = 13 THEN T4.DocDate WHEN T1.TargetType = 15 THEN T6.DocDate ELSE NULL END AS 'BillDocDate', CASE WHEN T1.TargetType = 13 THEN T4.DocDueDate WHEN T1.TargetType = 15 THEN T6.DocDueDate ELSE NULL END AS 'BillDocDueDate',
			CASE WHEN T1.TargetType = 13 THEN (T4.DocTotal-T4.VatSum) WHEN T1.TargetType = 15 THEN (T6.DocTotal-T6.VatSum) ELSE NULL END AS 'BillTotal',
			CASE WHEN T1.TargetType = 13 THEN (T4.PaidToDate-T4.VatPaid) WHEN T1.TargetType = 15 THEN (T6.PaidToDate-T6.VatPaid) ELSE 0 END AS 'BillPaid',
			CASE WHEN T1.TargetType = 13 THEN T4.CANCELED WHEN T1.TargetType = 15 THEN T6.CANCELED ELSE NULL END AS 'BillCancel',
			T0.Comments
		FROM ORDR T0
		LEFT JOIN RDR1 T1 ON T0.DocEntry   = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T0.Series     = T2.Series
		LEFT JOIN OSLP T3 ON T0.SlpCode    = T3.SlpCode
		LEFT JOIN OINV T4 ON T1.TrgetEntry = T4.DocEntry AND T1.TargetType = 13
		LEFT JOIN NNM1 T5 ON T4.Series     = T5.Series
		LEFT JOIN ODLN T6 ON T1.TrgetEntry = T6.DocEntry AND T1.TargetType = 15
		LEFT JOIN NNM1 T7 ON T6.Series     = T7.Series
		WHERE YEAR(T0.DocDate) = $Yaer AND MONTH(T0.DocDate) = $Month AND T1.TargetType > 0 $Team
		ORDER BY T3.U_Dim1, T3.SlpName, T0.CardName, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR))";
	$QRY1 = SAPSelect($SQL1);
	$r = 0; $SoEntry = ""; $OINVBillEntry = ""; $ODLNBillEntry = "";
	while($RST1 = odbc_fetch_array($QRY1)) {
		$SoEntry .= $RST1['SoEntry'].",";
		$arrCol[$r]['SoEntry'] = $RST1['SoEntry'];
		if($RST1['BillType'] == 'OINV') {
			$arrCol[$r]['OINVBillEntry'] = $RST1['BillEntry'];
			$OINVBillEntry .= $RST1['BillEntry'].",";
		}else{
			$arrCol[$r]['ODLNBillEntry'] = $RST1['BillEntry'];
			$ODLNBillEntry .= $RST1['BillEntry'].",";
		}

		$arrCol[$r]['Cancel'] = ($RST1['SOCancel'] == 'Y' || $RST1['BillCancel'] == 'Y') ? "Y" : "N";

		$arrCol[$r]['No'] = ($r+1);
		$arrCol[$r]['DocNum'] = "-";
		$arrCol[$r]['DocDate'] = "-";
		$arrCol[$r]['CardName'] = "<div style='white-space: nowrap;'>".$RST1['CardCode']." | ".conutf8($RST1['CardName'])."</div><div style='white-space: nowrap;'><small>พนักงานขาย: ".conutf8($RST1['SlpName'])."</small></div>";
		$arrCol[$r]['U_PONo'] = "-";
		$arrCol[$r]['DocTotal'] = "-";

		$arrCol[$r]['SoDocNum'] = "<a href='javascript:void(0);' onclick='ViewDoc(\"SO\",".$RST1['SoEntry'].",\"".$RST1['SoType']."\")'>".$RST1['SoDocNum']."</a>";
		$arrCol[$r]['SoDocDate'] = date("d/m/Y", strtotime($RST1['SODocDate']));
		$arrCol[$r]['SoDocTotal'] = number_format($RST1['DocTotal'],2);

		$arrCol[$r]['BillDocNum'] = "<a href='javascript:void(0);' onclick='ViewDoc(\"Bill\",".$RST1['BillEntry'].",\"".$RST1['BillType']."\")'>".$RST1['BillDocNum']."</a>";
		$arrCol[$r]['BillDocDate'] = date("d/m/Y", strtotime($RST1['BillDocDate']));
		$arrCol[$r]['BillDocDueDate'] = date("d/m/Y", strtotime($RST1['BillDocDueDate']));
		$arrCol[$r]['BillDocTotal'] = number_format($RST1['BillTotal'],2);
		$arrCol[$r]['BillPaid'] = number_format($RST1['BillPaid'],2);
		
		$Comments = "";
		if($RST1['Comments'] != "") { 
			$LoopComments = ceil(utf8_strlen(conutf8($RST1['Comments']))/65);
			$tmpComments = 0;
			for($i = 1; $i <= $LoopComments; $i++) {
				$Comments .= "<div style='white-space: nowrap;'>".mb_substr(conutf8($RST1['Comments']), $tmpComments, 65, 'UTF-8')."</div>";
				$tmpComments = $tmpComments+65;
			}
		}
		$arrCol[$r]['Comments'] = $Comments;
		$arrCol[$r]['Ship'] = "-";

		$r++;
	}

	$SQL2 = 
		"SELECT T0.SODocEntry, T0.DocType, CONCAT(T1.DocType,'V-',T1.DocNum) AS 'ERFDocNum', T1.DocDate, T1.U_PONo, (T1.DocTotal-T1.VatSum) AS DocTotal, T1.DocEntry AS 'ERFEntry'
		FROM picker_soheader T0
		LEFT JOIN order_header T1 ON T0.SODocEntry = T1.ImportEntry
		WHERE T0.DocType = 'ORDR' AND T0.SODocEntry IN (".substr($SoEntry,0,-1).")";
	$QRY2 = MySQLSelectX($SQL2);
	while($RST2 = mysqli_fetch_array($QRY2)) {
		$key = array_search($RST2['SODocEntry'], array_column($arrCol,'SoEntry'));
		$arrCol[$key]['DocNum'] = "<a href='javascript:void(0);' onclick='ViewDoc(\"APP\",".$RST2['ERFEntry'].",\"".$RST2['DocType']."\")'>".$RST2['ERFDocNum']."</a>";
		$arrCol[$key]['DocDate'] = date("d/m/Y", strtotime($RST2['DocDate']));
		$arrCol[$key]['U_PONo'] = $RST2['U_PONo'];
	}

	$SQL3 = "SELECT DocNum, BillEntry, BillType FROM ship_header WHERE BillEntry IN (".substr($OINVBillEntry,0,-1).") AND BillType = 'OINV' AND ShipStatus = 'A'";
	$QRY3 = MySQLSelectX($SQL3);
	while($RST3 = mysqli_fetch_array($QRY3)) {
		$key = array_search($RST3['BillEntry'], array_column($arrCol,'OINVBillEntry'));
		$arrCol[$key]['Ship'] = "<a href='javascript:void(0);' onclick='ViewShip(".$RST3['BillEntry'].", \"".$RST3['BillType']."\")'><i class='far fa-file-alt fa-fw'></i></a>";
	}

	$SQL4 = "SELECT DocNum, BillEntry, BillType FROM ship_header WHERE BillEntry IN (".substr($ODLNBillEntry,0,-1).") AND BillType = 'ODLN' AND ShipStatus = 'A'";
	$QRY4 = MySQLSelectX($SQL4);
	while($RST4 = mysqli_fetch_array($QRY4)) {
		$key = array_search($RST4['BillEntry'], array_column($arrCol,'ODLNBillEntry'));
		$arrCol[$key]['Ship'] = "<a href='javascript:void(0);' onclick='ViewShip(".$RST4['BillEntry'].", \"".$RST4['BillType']."\")'><i class='far fa-file-alt fa-fw'></i></a>";
	}
}

if($_GET['a'] == 'ViewDoc') {
	$Type = $_POST['Type'];
	$DocEntry = $_POST['DocEntry'];
	$DocType = $_POST['DocType'];

	if($Type == 'APP') {
		/* HEADER */
		$HeaderSQL = "SELECT T0.DocEntry, T0.CANCELED, T0.DraftStatus, T0.DocStatus, T0.AppStatus,
						CONCAT(T0.DocType,'V-',T0.DocNum) AS 'DocNum',
						CONCAT(T0.CardCode,' | ', T0.CardName) AS 'CardCode',
						T0.LicTradeNum, T0.DocDate, T0.DocDueDate,
						T0.TaxType, T0.Payment_Cond, T1.SlpName,
						T0.BilltoCode, T0.AddressBillto, T0.ShiptoCode, T0.AddressShipto,
						T1.SlpName, T0.ShippingType, T0.ShipCostType, T0.ShipComment, T0.DocTotal, T0.DiscTotal, T0.Comments, T3.DeptCode, T4.Description, T0.ImportEntry
					FROM order_header T0
					LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
					LEFT JOIN users T2 ON T0.CreateUkey = T2.uKey
					LEFT JOIN positions T3 ON T2.LvCode = T3.LvCode
					LEFT JOIN order_cancelreasons T4 ON T0.CancelReason = T4.CancelID
					WHERE DocEntry = $DocEntry LIMIT 1";
		$HeaderRST = MySQLSelect($HeaderSQL);
		$SAPEntry = $HeaderRST['ImportEntry'];
		$int_status = 0;
		if($HeaderRST['CANCELED'] == "Y") {
			$int_status = 0; /* ยกเลิก */
		}

		if($HeaderRST['CANCELED'] == "N" && $HeaderRST['DraftStatus'] == "Y" && $HeaderRST['DocStatus'] == "O") {
			$int_status = 1; /* บันทึกร่าง */
		}

		if($HeaderRST['CANCELED'] == "N" && $HeaderRST['DraftStatus'] == "N" && $HeaderRST['DocStatus'] == "P" && $HeaderRST['AppStatus'] == "P") {
			$int_status = 2; /* เอกสารรอตรวจสอบ */
		}

		if($HeaderRST['CANCELED'] == "N" && $HeaderRST['DraftStatus'] == "N" && $HeaderRST['DocStatus'] == "P" && $HeaderRST['AppStatus'] == "Y") {
			$int_status = 3; /* เอกสารผ่านการอนุมัติ */
		}
		
		if($HeaderRST['CANCELED'] == "N" && $HeaderRST['DraftStatus'] == "N" && $HeaderRST['DocStatus'] == "C" && $HeaderRST['AppStatus'] == "N") {
			$int_status = 4; /* เอกสารไม่ผ่านการอนุมัติ */
		}

		if($HeaderRST['CANCELED'] == "N" && $HeaderRST['DraftStatus'] == "N" && $HeaderRST['DocStatus'] == "C" && $HeaderRST['AppStatus'] == "Y") {
			$int_status = 5; /* เอกสารเสร็จสมบูรณ์ */
		}

		if($SAPEntry != "" && $int_status == 5) {
			$GetSAPSQL = "SELECT TOP 1 (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum' FROM ORDR T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE T0.DocEntry = $SAPEntry";
			$GetSAPQRY = SAPSelect($GetSAPSQL);
			$GetSAPRST = odbc_fetch_array($GetSAPQRY);
			$SAPDocNum = " [SAP S/O No.: <strong class='text-success'>".$GetSAPRST['DocNum']."</strong>]";
		} else {
			if($int_status == 5) {
				$SAPDocNum = " <strong class='text-danger'>Import Error!</span>";
			} else {
				$SAPDocNum = NULL;
			}
		}

		if($_SESSION['DeptCode'] != $HeaderRST['DeptCode'] && $_SESSION['DeptCode'] != "DP002") {
			$btn_edit   = null;
			$btn_print  = null;
			$btn_cancel = null;
			$btn_import = null;

			switch($int_status) {
				case 0:   $txt_status = "<span class='badge bg-secondary'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span> เนื่องจาก: ".$HeaderRST['Description']; break;
				case 1:   $txt_status = "<span class='badge bg-info'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>"; break;
				case 1.5: $txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>"; break;
				case 2:   $txt_status = "<span class='badge bg-warning'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>"; break;
				case 3:   $txt_status = "<span class='badge bg-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
				case 4:   $txt_status = "<span class='badge bg-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
				case 5:   $txt_status = "<span class='badge bg-success'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>$SAPDocNum"; break;
			}
			
		} else {
			switch($int_status) {
				case 0:
					$txt_status = "<span class='badge bg-secondary'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span> เนื่องจาก: ".$HeaderRST['Description'];
					$btn_edit   = null;
					$btn_print  = null;
					$btn_cancel = null;
					$btn_import = null;
					break;
				case 1:
					$txt_status = "<span class='badge bg-info'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
					$btn_edit   = "<a href='javascript:void(0);' class='btn btn-primary btn-sm' onclick='EditSO(".$DocEntry.")'><i class='fas fa-edit fa-fw fa-1x'></i> แก้ไขใบสั่งขาย</a>";
					$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintSO(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
					$btn_cancel  = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelSO(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
					$btn_import = null;
					break;
				case 1.5:
					$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
					$btn_edit   = null;
					$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintSO(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
					$btn_cancel  = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelSO(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
					$btn_import = null;
					break;
				case 2:
					$txt_status = "<span class='badge bg-warning'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>";
					$btn_edit   = null;
					$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintSO(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
					$btn_cancel = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelSO(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
					$btn_import = null;
					break;
				case 3:
					if($_SESSION['uClass'] == 0) {
						$Dis = null;
					} else {
						$Dis = "disabled";
					}
					$txt_status = "<span class='badge bg-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
					$btn_edit   = null;
					$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintSO(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
					$btn_cancel = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelSO(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
					$btn_import = "<a href='javascript:void(0);' class='btn btn-info btn-sm $Dis' onclick='ExportSO(".$DocEntry.")'><i class='fas fa-share-square fa-fw fa-1x'></i> ส่งออก</a>";
					break;
				case 4:
					$txt_status = "<span class='badge bg-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
					$btn_edit   = null;
					$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintSO(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
					$btn_cancel = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelSO(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
					$btn_import = null;
					break;
				case 5:
					$txt_status = "<span class='badge bg-success'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>$SAPDocNum";
					$btn_edit   = null;
					$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintSO(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
					$btn_cancel = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelSO(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
					$btn_import = null;
					break;
			}
		}

		switch($HeaderRST['TaxType']) {
			case "S07": $TaxType = "VAT นอก"; break;
			case "S00": $TaxType = "VAT ใน"; break;
			case "SNV": $TaxType = "ไม่มี VAT"; break;
			default   : $TaxType = "ไม่ระบุ"; break;
		}
		switch($HeaderRST['Payment_Cond']) {
			case "CR": $PaymentTerm = "เครดิต"; break;
			case "CS": $PaymentTerm = "เงินสด"; break;
			default  : $PaymentTerm = "ไม่ระบุ"; break;
		}
		switch($HeaderRST['ShipCostType']) {
			case "PRE": $ShipCostType = "เก็บเงินค่าขนส่งต้นทาง"; break;
			case "PST": $ShipCostType = "เก็บเงินค่าขนส่งปลายทาง"; break;
			case "COD": $ShipCostType = "เก็บเงินค่าสินค้าปลายทาง"; break;
			case "FREE": $ShipCostType = "ไม่มีค่าขนส่ง"; break;
			default   : $ShipCostType = "ไม่ระบุ"; break;
		}
		$arrCol['DocEntry']          = $HeaderRST['DocEntry'];
		$arrCol['DocStatus']         = $int_status;
		$arrCol['view_DocNum']       = $HeaderRST['DocNum'];
		$arrCol['view_CardCode']     = $HeaderRST['CardCode'];
		$arrCol['view_LicTradeNum']  = $HeaderRST['LicTradeNum'];
		$arrCol['view_DocDate']      = date("d/m/Y",strtotime($HeaderRST['DocDate']));
		$arrCol['view_DocDueDate']   = date("d/m/Y",strtotime($HeaderRST['DocDueDate']));
		$arrCol['view_TaxType']      = $TaxType;
		$arrCol['view_Payment_Cond'] = $PaymentTerm;
		$arrCol['view_SlpCode']      = $HeaderRST['SlpName'];
		$arrCol['view_DocStatus']    = $txt_status;

		/* ITEM LIST */
		$ItemListSQL = "SELECT
							T0.ItemCode, T0.CodeBars, T0.ItemStatus, T0.ItemName, T0.WhsCode, T0.Quantity, T0.UnitMsr,
							T0.GrandPrice, T0.Line_Disc1, T0.Line_Disc2, T0.Line_Disc3, T0.Line_Disc4, T0.UnitPrice, T0.UnitVat, T0.LineTotal, T0.LineVatSum
						FROM order_detail T0
						WHERE T0.DocEntry = $DocEntry AND T0.LineStatus != 'I'";
		$ItemListQRY = MySQLSelectX($ItemListSQL);
		$ItemList = "";
		$no = 1;
		$SUMLineTotal = 0;
		while($ItemListRST = mysqli_fetch_array($ItemListQRY)) {
			$NameLen = mb_strlen($ItemListRST['ItemName'],'UTF-8');
			if($NameLen <= 32) {
				$ItemName = $ItemListRST['ItemName'];
			} else {
				$ItemName = iconv_substr($ItemListRST['ItemName'],0,32,'UTF-8')."...";
			}

			if($HeaderRST['TaxType'] != "S07") {
				$GrandPrice = $ItemListRST['GrandPrice']*1.07;
				$LineTotal = $ItemListRST['LineTotal']+$ItemListRST['LineVatSum'];
			} else {
				$GrandPrice = $ItemListRST['GrandPrice'];
				$LineTotal = $ItemListRST['LineTotal'];
			}

			if($ItemListRST['Line_Disc4'] != NULL AND $ItemListRST['Line_Disc4'] != "" AND $ItemListRST['Line_Disc4'] != 0.00) {
				$Discount = number_format($ItemListRST['Line_Disc1'],1)."%+".number_format($ItemListRST['Line_Disc2'],1)."%+".number_format($ItemListRST['Line_Disc3'],1)."%+".number_format($ItemListRST['Line_Disc4'],1)."%";
			} elseif($ItemListRST['Line_Disc3'] != NULL AND $ItemListRST['Line_Disc3'] != "" AND $ItemListRST['Line_Disc3'] != 0.00) {
				$Discount = number_format($ItemListRST['Line_Disc1'],1)."%+".number_format($ItemListRST['Line_Disc2'],1)."%+".number_format($ItemListRST['Line_Disc3'],1)."%";
			} elseif($ItemListRST['Line_Disc2'] != NULL AND $ItemListRST['Line_Disc2'] != "" AND $ItemListRST['Line_Disc2'] != 0.00) {
				$Discount = number_format($ItemListRST['Line_Disc1'],1)."%+".number_format($ItemListRST['Line_Disc2'],1)."%";
			} elseif($ItemListRST['Line_Disc1'] != NULL AND $ItemListRST['Line_Disc1'] != "" AND $ItemListRST['Line_Disc1'] != 0.00) {
				$Discount = number_format($ItemListRST['Line_Disc1'],1)."%";
			} else {
				$Discount = NULL;
			}

			$SUMLineTotal = $SUMLineTotal+$LineTotal;

			$ItemList .= "<tr>";
				$ItemList .= "<td class='text-right'>".number_format($no,0)."</td>";
				$ItemList .= "<td>".$ItemListRST['ItemCode']." | ".$ItemName."</td>";
				$ItemList .= "<td class='text-center'>".$ItemListRST['WhsCode']."</td>";
				$ItemList .= "<td width='7.5%' class='text-right'>".number_format($ItemListRST['Quantity'],0)."</td>";
				$ItemList .= "<td width='6.25%'>".$ItemListRST['UnitMsr']."</td>";
				$ItemList .= "<td class='text-right'>".number_format($GrandPrice,3)."</td>";
				$ItemList .= "<td class='text-center'>".$Discount."</td>";
				$ItemList .= "<td class='text-right'>".number_format($LineTotal,2)."</td>";
			$ItemList .= "</tr>";
			$no++;
		}
		switch($HeaderRST['TaxType']) {
			case "S07":
				$txt_pricebefvat = $SUMLineTotal-$HeaderRST['DiscTotal'];
				$txt_tax         = $txt_pricebefvat*0.07;
				$txt_doctotal    = $txt_pricebefvat+$txt_tax;
				break;
			case "S00":
				$txt_pricebefvat = ($SUMLineTotal-$HeaderRST['DiscTotal'])/1.07;
				$txt_tax         = $txt_pricebefvat*0.07;
				$txt_doctotal    = $txt_pricebefvat+$txt_tax;
			break;
			case "SNV":
				$txt_pricebefvat = $SUMLineTotal-$HeaderRST['DiscTotal'];
				$txt_tax         = 0;
				$txt_doctotal    = $txt_pricebefvat+$txt_tax;
			break;
		}
		$ItemList .= "<tr>";
			$ItemList .= "<td colspan='5' rowspan='5' class='align-top'><span class='font-weight'>หมายเหตุ:</span><br/>".$HeaderRST['Comments']."</td>";
			$ItemList .= "<td colspan='2' class='text-right font-weight'>ยอดรวมทุกรายการ:</td>";
			$ItemList .= "<td class='text-right font-weight'>".number_format($SUMLineTotal,2)."</td>";
		$ItemList .= "</tr>";
		$ItemList .= "<tr>";
			$ItemList .= "<td colspan='2' class='font-weight text-right'>ส่วนลดท้ายบิล:</td>";
			$ItemList .= "<td class='text-right'>".number_format($HeaderRST['DiscTotal'],2)."</td>";
		$ItemList .= "</tr>";
		$ItemList .= "<tr>";
			$ItemList .= "<td colspan='2' class='font-weight text-right'>ยอดสินค้าหลังหักส่วนลด:</td>";
			$ItemList .= "<td class='text-right'>".number_format($txt_pricebefvat,2)."</td>";
		$ItemList .= "</tr>";
		$ItemList .= "<tr>";
			$ItemList .= "<td colspan='2' class='font-weight text-right'>ภาษีมูลค่าเพิ่ม:</td>";
			$ItemList .= "<td class='text-right'>".number_format($txt_tax,2)."</td>";
		$ItemList .= "</tr>";
		$ItemList .= "<tr>";
			$ItemList .= "<td colspan='2' class='font-weight text-right font-weight'>จำนวนเงินรวมสุทธิ:</td>";
			$ItemList .= "<td class='text-right font-weight'>".number_format($HeaderRST['DocTotal'],2)."</td>";
		$ItemList .= "</tr>";

		$arrCol['view_ItemList'] = $ItemList;
	}else{
		$SQL1 = 
            "SELECT
				ISNULL(T3.[BeginStr],'IV-')+CAST(T0.[DocNum] AS VARCHAR) AS [DocNum],
                CONCAT(T0.[CardCode], ' | ', T0.[CardName]) AS CardName, T0.[LicTradNum], T0.[DocDate], T0.[DocDueDate], T1.[PymntGroup],
                CONCAT(T0.[PayToCode], ' ', T0.[Address]) AS [PaytoAddr],  CONCAT(T0.[ShipToCode], ' ', T0.[Address2]) AS [ShiptoAddr],
                T2.[SlpName], T0.[NumAtCard], T0.[Comments], T0.[CANCELED], T0.[DocTotal], T0.[DiscPrcnt], T0.[DiscSum], T0.[VatSum],
                T4.[VisOrder], T4.[ItemCode], T4.[CodeBars], T4.[Dscription], T4.[WhsCode], T4.[Quantity], T4.[unitMsr],
                T4.[PriceBefDi], T4.[U_DiscP1], T4.[U_DiscP2], T4.[U_DiscP3], T4.[U_DiscP4], T4.[U_DiscP5], T4.[PriceAfVAT], T4.[LineTotal], T4.[LineStatus]
            FROM $DocType T0
            LEFT JOIN OCTG T1 ON T0.[GroupNum] = T1.[GroupNum]
            LEFT JOIN OSLP T2 ON T0.[SlpCode] = T2.[SlpCode]
            LEFT JOIN NNM1 T3 ON T0.[Series] = T3.[Series]
            LEFT JOIN ".substr($DocType, 1)."1 T4 ON T0.[DocEntry] = T4.[DocEntry]
            WHERE T0.DocEntry = $DocEntry";
		$QRY1 = SAPSelect($SQL1);
		$r = 0; $Data = array();
		while($RST1 = odbc_fetch_array($QRY1)) {
			if($r == 0) {
				$arrCol['DataHead'] = 
					[
						$RST1['DocNum'], (($RST1['CANCELED'] == 'N') ? "<span class='badge bg-success p-1'>ปกติ</span>" : "<span class='badge bg-secondary p-1'>ยกเลิก</span>"),
						conutf8($RST1['CardName']), $RST1['LicTradNum'],
						date("d/m/Y", strtotime($RST1['DocDate'])), date("d/m/Y", strtotime($RST1['DocDueDate'])),
						conutf8($RST1['PymntGroup']), "",
						conutf8($RST1['PaytoAddr']), conutf8($RST1['ShiptoAddr']),
						conutf8($RST1['SlpName']), $RST1['NumAtCard']
					];
			}
			$Data[$r]['LineTotal'] = $RST1['LineTotal'];
			$Data[$r]['U_DiscP1'] = $RST1['U_DiscP1'];
			$Data[$r]['U_DiscP2'] = $RST1['U_DiscP2'];
			$Data[$r]['U_DiscP3'] = $RST1['U_DiscP3'];
			$Data[$r]['U_DiscP4'] = $RST1['U_DiscP4'];
			$Data[$r]['LineStatus'] = $RST1['LineStatus'];
			$Data[$r]['ItemCode'] = $RST1['ItemCode'];
			$Data[$r]['CodeBars'] = $RST1['CodeBars'];
			$Data[$r]['Dscription'] = conutf8($RST1['Dscription']);
			$Data[$r]['WhsCode'] = $RST1['WhsCode'];
			$Data[$r]['Quantity'] = $RST1['Quantity'];
			$Data[$r]['unitMsr'] = conutf8($RST1['unitMsr']);
			$Data[$r]['PriceBefDi'] = $RST1['PriceBefDi'];
			$Data[$r]['PriceAfVAT'] = $RST1['PriceAfVAT'];
			$Data[$r]['Comments'] = conutf8($RST1['Comments']);
			$Data[$r]['DiscPrcnt'] = $RST1['DiscPrcnt'];
			$Data[$r]['DocTotal'] = $RST1['DocTotal'];
			$Data[$r]['VatSum'] = $RST1['VatSum'];
			$r++;
		}
		$arrCol['DataView'] = $Data;
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
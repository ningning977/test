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

if($_GET['p'] == "CallDropDown") {
	/* CO-LOGI */
	$SQL1 =
		"SELECT
			T0.uKey, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'FullName', T1.DeptCode, T2.DeptName
		FROM users T0
		LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
		LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
		WHERE T1.LvCode IN ('LV082','LV083','LV084')
		ORDER BY T1.DeptCode, T1.uClass, T0.uName";
	$QRY1 = MySQLSelectX($SQL1);
	$ROW1 = 0;
	while($RST1 = mysqli_fetch_array($QRY1)) {
		$arrCol['COLO'][$ROW1]['VAL'] = $RST1['uKey'];
		$arrCol['COLO'][$ROW1]['TXT'] = $RST1['FullName'];
		$arrCol['COLO'][$ROW1]['DPC'] = $RST1['DeptCode'];
		$arrCol['COLO'][$ROW1]['DPN'] = $RST1['DeptName'];
		$ROW1++;
	}
	$arrCol['COLO']['ROW'] = $ROW1;

	/* ALL USERS */
	$SQL2 =
		"SELECT
			T0.uKey, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'FullName', T1.DeptCode, T2.DeptName
		FROM users T0
		LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
		LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
		WHERE (T1.DeptCode IN ('DP003','DP005','DP006','DP007','DP008','DP010','DP011') AND T1.uClass NOT IN (24,25,26))
		ORDER BY T1.DeptCode, T1.uClass, T0.uName";
	$QRY2 = MySQLSelectX($SQL2);
	$ROW2 = 0;
	while($RST2 = mysqli_fetch_array($QRY2)) {	
		$arrCol['USER'][$ROW2]['VAL'] = $RST2['uKey'];
		$arrCol['USER'][$ROW2]['TXT'] = $RST2['FullName'];
		$arrCol['USER'][$ROW2]['DPC'] = $RST2['DeptCode'];
		$arrCol['USER'][$ROW2]['DPN'] = $RST2['DeptName'];
		$ROW2++;
	}
	$arrCol['USER']['ROW'] = $ROW2;

	/* CO-SALES */
	$SQL3 =
		"SELECT
			T0.uKey, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'FullName', T1.DeptCode, T2.DeptName
		FROM users T0
		LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
		LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
		WHERE T1.uClass IN (18,19,20,21,22,51,52,53)
		ORDER BY T1.DeptCode, T1.uClass, T0.uName";
	$QRY3 = MySQLSelectX($SQL3);
	$ROW3 = 0;
	while($RST3 = mysqli_fetch_array($QRY3)) {
		$arrCol['COSA'][$ROW3]['VAL'] = $RST3['uKey'];
		$arrCol['COSA'][$ROW3]['TXT'] = $RST3['FullName'];
		$arrCol['COSA'][$ROW3]['DPC'] = $RST3['DeptCode'];
		$arrCol['COSA'][$ROW3]['DPN'] = $RST3['DeptName'];
		$ROW3++;
	}
	$arrCol['COSA']['ROW'] = $ROW3;

	/* SALE */
	$SQL4 =
		"SELECT
			T0.uKey, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'FullName', T1.DeptCode, T2.DeptName
		FROM users T0
		LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
		LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
		WHERE (T1.uClass IN (18,19,20)) OR (T1.uClass IN (22) AND T1.DeptCode = 'DP003')
		ORDER BY T1.DeptCode, T1.uClass, T0.uName";
	$QRY4 = MySQLSelectX($SQL4);
	$ROW4 = 0;
	while($RST4 = mysqli_fetch_array($QRY4)) {
		$arrCol['SALE'][$ROW4]['VAL'] = $RST4['uKey'];
		$arrCol['SALE'][$ROW4]['TXT'] = $RST4['FullName'];
		$arrCol['SALE'][$ROW4]['DPC'] = $RST4['DeptCode'];
		$arrCol['SALE'][$ROW4]['DPN'] = $RST4['DeptName'];
		$ROW4++;
	}
	$arrCol['SALE']['ROW'] = $ROW4;
}

if($_GET['p'] == "GetDocList") {
	if(!isset($_POST['filt_y'])) { $y = date("Y"); } else { $y = $_POST['filt_y']; }
	if(!isset($_POST['filt_m'])) { $m = date("m"); } else { $m = $_POST['filt_m']; }
	if(!isset($_POST['filt_t'])) { $t = "ALL"; } else { $t = $_POST['filt_t']; }

	if($t == "ALL") {
		$ListWhr = "";
	} else {
		switch($t) {
			case "MT1":
			case "EXP":
				$DeptCode = 'DP006';
			break;
			case "MT2":
				$DeptCode = 'DP007';
			break;
			case "TT2":
				$DeptCode = 'DP005';
			break;
			case "TT1":
			case "OUL":
				$DeptCode = 'DP008';
			break;
			case "ONL":
				$DeptCode = 'DP003';
			break;
		}
		$ListWhr = " AND (T2.DeptCode = '$DeptCode' OR T0.BillTeamCode = '$t')";
	}

	$SQL1 = 
		"SELECT
			T0.DocEntry, T0.DocDate, T0.DocType, T0.DraftStatus, T0.CANCELED, T0.DocStatus, T0.AppStatus, T0.Printed,
			T0.DocNum, T0.BillCardCode, T0.BillCardName, T0.RefDoc1, T0.BillDocNum, CONCAT(T1.uName,' ',T1.uLastName) AS 'SlpName', T2.Deptcode, T3.DeptName,
			(SELECT COUNT(P0.ApproveID) FROM rtqc_approve P0 WHERE P0.DocEntry = T0.DocEntry AND P0.AppState NOT IN ('0','1')) AS 'Approved',
			(SELECT COUNT(P0.ApproveID) FROM rtqc_approve P0 WHERE P0.DocEntry = T0.DocEntry) AS 'Approve',
			CONCAT(T4.uName,' ',T4.uLastName) AS 'CreateName'
		FROM rtqc_header T0
		LEFT JOIN users T1 ON T0.BillSlpUkey = T1.uKey
		LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
		LEFT JOIN departments T3 ON T2.DeptCode = T3.DeptCode
		LEFT JOIN users T4 ON T0.CreateUkey = T4.uKey
		WHERE (YEAR(T0.CreateDate) = $y AND MONTH(T0.CreateDate) = $m) $ListWhr
		ORDER BY 
			CASE
				WHEN (T0.DocType = 'D') THEN 1
				WHEN (T0.DocType = 'L') THEN 2
				WHEN (T0.DocType = 'AC') THEN 3
				WHEN (T0.DocType = 'X') THEN 4
			ELSE 99 END,
			T0.DocEntry DESC";
	// echo $SQL1;
	$ROW1 = CHKRowDB($SQL1);
	// echo $ROW1;
	if($ROW1 == 0) {
		$output = "<tr class='table-active text-muted'><td colspan='9' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$no = 1;
		$i  = 0;

		$QRY1 = MySQLSelectX($SQL1);
		while($RST1 = mysqli_fetch_array($QRY1)) {
			$int_status = 0;
			if($RST1['CANCELED'] == "Y") {
				$int_status = 0; /* ยกเลิก */
			}
			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "Y" && $RST1['DocStatus'] == "O") {
				$int_status = 1; /* บันทึกร่าง */
			}

			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "N" && $RST1['DocStatus'] == "P" && $RST1['AppStatus'] == "P") {
				$int_status = 2; /* เอกสารรอตรวจสอบ */
			}

			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "N" && $RST1['DocStatus'] == "P" && $RST1['AppStatus'] == "Y") {
				$int_status = 3; /* เอกสารผ่านการอนุมัติ */
			}
			
			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "N" && $RST1['DocStatus'] == "C" && $RST1['AppStatus'] == "N") {
				$int_status = 4; /* เอกสารไม่ผ่านการอนุมัติ */
			}

			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "N" && $RST1['DocStatus'] == "C" && $RST1['AppStatus'] == "Y") {
				$int_status = 5; /* เอกสารเสร็จสมบูรณ์ */
			}

			/*
				int_status หมายถึงสถานะภายในสำหรับการประมวลผลคำสั่งขาย
				+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
				| int_status | CANCELED | DraftStatus | DocStatus | AppStatus || CAN EDIT? | CAN PRINT? | CAN IMPORT? |
				+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
				| 0          | Y        | ANY         | ANY       | ANY       || NO        | NO         | NO          | -> เอกสารยกเลิก
				| 1          | N        | Y           | O         | B         || YES       | YES        | NO          | -> เอกสารแบบร่าง
				| 1.5        | N        | P           | O         | B         || NO        | YES        | NO          | -> เอกสารรอตรวจสอบ (รอ Co-Sales ตรวจ)
				| 2          | N        | N           | P         | P         || NO        | YES        | NO          | -> เอกสารรออนุมัติ
				| 3          | N        | N           | P         | Y         || NO        | YES        | YES         | -> เอกสารผ่านการอนุมัติ
				| 4          | N        | N           | C         | N         || NO        | NO         | NO          | -> เอกสารไม่อนุมัติ
				| 5          | N        | N           | C         | Y         || YES       | YES        | NO          | -> เอกสารเสร็จสมบูรณ์ (Import เข้า SAP เรียบร้อย)
				+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
			*/

			$dis_prnt   = NULL;
			$dis_cncl   = NULL;

			if($_SESSION['DeptCode'] != "DP002") {
				$dis_prnt   = " disabled";
				$dis_import = " disabled";
				$dis_cncl   = "";
				switch($int_status) {
					case 0:
						$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
						$dis_cncl   = " disabled";
					break;
					case 1:   $txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>"; break;
					case 1.5: $txt_status = "<span class='badge bg-primary w-100'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>"; break;
					case 2:   $txt_status = "<span class='badge w-100' style='background-color: #C79910; color: #FFF;'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ <b>[".$RST1['Approved']."/".$RST1['Approve']."]</b></span>"; break;
					case 3:
						$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
						$dis_prnt   = "";
						$dis_import = "";
					break;
					case 4:
						$txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
						$dis_prnt   = "";
					break;
					case 5:
						$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
						$dis_cncl   = " disabled";
					break;
				}
				
			} else {
				$dis_prnt   = " disabled";
				$dis_import = " disabled";
				$dis_cncl   = "";
				switch($int_status) {
					case 0:   $txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>"; break;
					case 1:   $txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>"; break;
					case 1.5: $txt_status = "<span class='badge bg-primary w-100'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>"; break;
					case 2:   $txt_status = "<span class='badge w-100' style='background-color: #C79910; color: #FFF;'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ <b>[".$RST1['Approved']."/".$RST1['Approve']."]</b></span>"; break;
					case 3:
						$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
						$dis_prnt   = "";
						$dis_import = "";
					break;
					case 4:
						$txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
						$dis_prnt   = "";
					break;
					case 5:
						$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
						$dis_cncl   = " disabled";
					break;
				}
			}

			if($int_status != 0) {
				$txt_opt = "<div calss='dropdown'>";
					$txt_opt.= "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
						$txt_opt.= "<i class='fas fa-cog fa-fw fa-1x'></i>";
					$txt_opt.= "</button>";
					$txt_opt.= "<ul class='dropdown-menu' style='font-size: 13px;'>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-view' onclick='PreviewDoc(".$RST1['DocEntry'].",$int_status)'><i class='fas fa-info fa-fw fa-1x'></i> รายละเอียด</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-prnt$dis_prnt' onclick='PrintDoc(".$RST1['DocEntry'].");'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-impt$dis_import' onclick='SendDoc(".$RST1['DocEntry'].")'><i class='fas fa-share-square fa-fw fa-1x'></i> ส่งฝ่าย QC ตรวจสอบ</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-cncl$dis_cncl' onclick='CancelDoc(".$RST1['DocEntry'].")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a></li>";
					$txt_opt.= "</ul>";
				$txt_opt.= "</div>";
				switch($int_status) {
					case "1.5":
					case "2":
						$row_cls = " class='table-warning text-warning'";
					break;
					case "3":
					case "5":
						$row_cls = " class='table-success text-success'";
					break;
					case "4":
						$row_cls = " class='table-danger text-danger'";
					break;
					default: $row_cls = null; break;
				}
			} else {
				$txt_opt = "";
				$row_cls = " class='table-active text-secondary'";
			}

			switch($RST1['DocType']) {
				case "D":  $TypeName = "คืนเพื่อลดหนี้"; break;
				case "L":  $TypeName = "คืนจากการยืม"; break;
				case "AC": $TypeName = "คืนแบบไม่มีสินค้า (คืนลอย)"; break;
				default:   $TypeName = "คืนจากการที่คลังส่งของผิด ส่งเกิน"; break;
			}

			$arrCol[$i]['no']           = number_format($no);
			$arrCol[$i]['DocDate']      = date("d/m/Y",strtotime($RST1['DocDate']));
			$arrCol[$i]['DocType']      = $TypeName;
			$arrCol[$i]['DocNum']       = "<a href='javascript:void(0);' onclick='PreviewDoc(".$RST1['DocEntry'].",$int_status);'>".$RST1['DocNum']."</a>";
			$arrCol[$i]['BillCardCode'] = $RST1['BillCardCode']." | ".$RST1['BillCardName']."<br/><small>ผู้จัดทำ: ".$RST1['CreateName']."</small>";
			$arrCol[$i]['RefDocNum']    = $RST1['RefDoc1'];
			$arrCol[$i]['BillDocNum']   = $RST1['BillDocNum'];
			$arrCol[$i]['BillSlpName']  = "<span class='badge bg-dark'>".$RST1['DeptName']."</span><br/>".$RST1['SlpName'];
			$arrCol[$i]['txt_status']   = $txt_status;
			$arrCol[$i]['txt_opt']      = $txt_opt;
			$arrCol[$i]['int_status']   = $int_status;

			$no++;
			$i++;

		}
	}

}

if($_GET['p'] == "SearchBill") {
	$TextBox  = $_POST['TextBox'];
	$BillType = $_POST['BillType'];

	switch($BillType) {
		case "OINV": $tbprefix = array("OINV","INV1"); break;
		case "ODLN": $tbprefix = array("ODLN","DLN1"); break;
	}

	$SQL1 =
		"SELECT
			T0.DocEntry, T0.DocDate, T0.DocDueDate,
			(SUBSTRING(ISNULL(T4.BeginStr,'IV-'),1,2)+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.NumAtCard,
			T0.CardCode, T0.CardName, T0.SlpCode, T2.Memo AS 'SlpUkey',
			CASE WHEN T0.SlpCode IN (291,296,20,123,124,125,126) THEN T7.U_Dim1 ELSE T2.U_Dim1 END AS 'TeamCode',
			ISNULL((SELECT P1.ExtEmpNo FROM ORDR P0 LEFT JOIN OHEM P1 ON P0.OwnerCode = P1.empID WHERE P0.DocEntry = T1.BaseEntry), T3.ExtEmpNo) AS 'OwnEmpCode',
			T1.VisOrder, T1.ItemCode, ISNULL(T1.CodeBars, '') AS 'CodeBars', T1.Dscription AS 'ItemName', ISNULL(T5.U_ProductStatus, '') AS 'ItemStatus',
			T1.PriceBefDi AS 'GrandPrice', T1.Price AS 'UnitPrice', CAST(T1.Quantity AS DECIMAL(20,0)) AS 'Quantity', T1.unitMsr, T1.WhsCode,
			T1.U_DiscP1, T1.U_DiscP2, T1.U_DiscP3, T1.U_DiscP4, T1.U_DiscP5
		FROM $tbprefix[0] T0
		LEFT JOIN $tbprefix[1] T1 ON T0.DocEntry  = T1.DocEntry
		LEFT JOIN OSLP T2 ON T0.SlpCode   = T2.SlpCode
		LEFT JOIN OHEM T3 ON T0.OwnerCode = T3.empID
		LEFT JOIN NNM1 T4 ON T0.Series    = T4.Series
		LEFT JOIN OITM T5 ON T1.ItemCode  = T5.ItemCode
		LEFT JOIN OCRD T6 ON T0.CardCode  = T6.CardCode
		LEFT JOIN OSLP T7 ON T6.SlpCode  = T7.SlpCode
		WHERE 
			(T0.NumAtCard LIKE '%$TextBox%' OR (SUBSTRING(ISNULL(T4.BeginStr,'IV-'),1,2)+CAST(T0.DocNum AS VARCHAR)) LIKE '%$TextBox%') AND 
			(T0.CANCELED = 'N' AND T0.CreateDate NOT BETWEEN '2022-12-31' AND '2023-01-01')";
	$arrCol['HEAD']['ROW'] = 0;

	$SAPVer = 0;

	if(ChkRowSAP($SQL1) > 0) {
		/* SAP 10 */
		$arrCol['HEAD']['ROW'] = ChkRowSAP($SQL1);
		$QRY1 = SAPSelect($SQL1);
		$SAPVer = 10;
	} elseif(ChkRowSAP8($SQL1) > 0) {
		/* SAP 8 */
		$arrCol['HEAD']['ROW'] = ChkRowSAP8($SQL1);
		$QRY1 = conSAP8($SQL1);
		$SAPVer = 8;
	}

	if($arrCol['HEAD']['ROW'] > 0) {
		while($RST1 = odbc_fetch_array($QRY1)) {

			$DiscPcnt = array($RST1['U_DiscP1'],$RST1['U_DiscP2'],$RST1['U_DiscP3'],$RST1['U_DiscP4'],$RST1['U_DiscP5']);
			$Discount = "";
			for($d = 0; $d < count($DiscPcnt); $d++) {
				if($DiscPcnt[$d] > 0) {
					if($d != 0) {
						$Discount .= "+";
					}
					$Discount .= number_format($DiscPcnt[$d],2)."%";
				}
			}


			$OwnEmpCode = $RST1['OwnEmpCode'];
			$VisOrder   = $RST1['VisOrder'];

			/* DOCUMENT HEADER */
			$arrCol['HEAD']['DocEntry']   = $RST1['DocEntry'];
			$arrCol['HEAD']['DocDate']    = date("Y-m-d",strtotime($RST1['DocDate']));
			$arrCol['HEAD']['DocDueDate'] = date("Y-m-d",strtotime($RST1['DocDueDate']));
			$arrCol['HEAD']['DocNum']     = conutf8($RST1['DocNum']);
			$arrCol['HEAD']['NumAtCard']  = conutf8($RST1['NumAtCard']);
			$arrCol['HEAD']['CardCode']   = conutf8($RST1['CardCode']." | ".$RST1['CardName']);
			$arrCol['HEAD']['SlpUkey']    = $RST1['SlpUkey'];
			$arrCol['HEAD']['TeamCode']   = $RST1['TeamCode'];
			$arrCol['HEAD']['BillType']   = $BillType;

			/* DOCUMENT DETAIL */
			$arrCol['BODY'][$VisOrder]['VisOrder']   = $VisOrder;
			$arrCol['BODY'][$VisOrder]['ItemCode']   = $RST1['ItemCode'];
			$arrCol['BODY'][$VisOrder]['CodeBars']   = $RST1['CodeBars'];
			$arrCol['BODY'][$VisOrder]['ItemName']   = conutf8($RST1['ItemName']);
			$arrCol['BODY'][$VisOrder]['ItemStatus'] = $RST1['ItemStatus'];
			$arrCol['BODY'][$VisOrder]['Quantity']   = $RST1['Quantity'];
			$arrCol['BODY'][$VisOrder]['GrandPrice'] = number_format($RST1['GrandPrice'],3);
			$arrCol['BODY'][$VisOrder]['Discount']   = $Discount;
			$arrCol['BODY'][$VisOrder]['UnitPrice']  = number_format($RST1['UnitPrice'],3);
			$arrCol['BODY'][$VisOrder]['UnitMsr']    = conutf8($RST1['unitMsr']);
			$arrCol['BODY'][$VisOrder]['WhsCode']    = conutf8($RST1['WhsCode']);


			$arrCol['BODY'][$VisOrder]['OptValue'] =
				/* 0 */ $VisOrder."::".
				/* 1 */ $RST1['ItemCode']."::".
				/* 2 */ $RST1['CodeBars']."::".
				/* 3 */ conutf8($RST1['ItemName'])."::".
				/* 4 */ $RST1['ItemStatus']."::".
				/* 5 */ $RST1['Quantity']."::".
				/* 6 */ conutf8($RST1['unitMsr'])."::".
				/* 7 */ conutf8($RST1['WhsCode'])."::".
				/* 8 */ number_format($RST1['UnitPrice'],3)."::".
				/* 9 */ number_format($RST1['GrandPrice'],3)."::".
				/*10 */ $Discount;

			if($SAPVer == 8) {
				$SlpCode = $RST1['SlpCode'];
				$SQL2 = "SELECT T0.Ukey FROM OSLP T0 WHERE T0.SlpCode8 = $SlpCode LIMIT 1";
				$RST2 = MySQLSelect($SQL2);
				$arrCol['HEAD']['SlpUkey'] = $RST2['Ukey'];
			}
		}

		$arrCol['HEAD']['SAPVer'] = $SAPVer;

		if($OwnEmpCode != "NULL" || $OwnEmpCode != "NULL") {
			$SQL2 = "SELECT T0.uKey AS 'OwnUkey' FROM users T0 WHERE T0.EmpCode = '$OwnEmpCode'";
			$QRY2 = MySQLSelectX($SQL2);
			$RST2 = mysqli_fetch_array($QRY2);
			$arrCol['HEAD']['OwnUkey'] = $RST2['OwnUkey'];
		}
	}
}

if($_GET['p'] == "ChkWhs") {
	$ItemCode = $_POST['ItemCode'];
	$WhsCode  = $_POST['WhsCode'];

	$SQL1 = "SELECT T0.WhsCode FROM OWHS T0 WHERE T0.WhsCode = '$WhsCode'";
	$ROW1 = ChkRowSAP($SQL1);

	if($ROW1 > 0) {
		$SQL2 = "SELECT T0.WhsCode, T0.ItemCode FROM OITW T0 WHERE T0.ItemCode = '$ItemCode' AND T0.WhsCode = '$WhsCode'";
		$ROW2 = ChkRowSAP($SQL2);
		if($ROW2 > 0) {
			$arrCol['Status'] = "SUCCESS";
		} else {
			$arrCol['Status'] = "ERR::NOINVT";
		}
	} else {
		$arrCol['Status'] = "ERR::NOWHSE";
	}
}

if($_GET['p'] == "SaveDoc") {
	if(isset($_POST['txt_DocDate'])) { $txt_DocDate = "'".date("Y-m-d",strtotime($_POST['txt_DocDate']))."'"; } else { $txt_DocDate = "NULL"; }
	if(isset($_POST['txt_RefDoc1'])) { $txt_RefDoc1 = "'".$_POST['txt_RefDoc1']."'"; } else { $txt_RefDoc1 = "NULL"; }
	if(isset($_POST['txt_DocType'])) { $txt_DocType = "'".$_POST['txt_DocType']."'"; } else { $txt_DocType = "NULL"; }
	if(isset($_POST['txt_BillDocNum'])) { $txt_BillDocNum = "'".$_POST['txt_BillDocNum']."'"; } else { $txt_BillDocNum = "NULL"; }
	if(isset($_POST['txt_BillDocType'])) { $txt_BillDocType = "'".$_POST['txt_BillDocType']."'"; $BillDocType = $_POST['txt_BillDocType']; } else { $txt_BillDocType = "NULL"; $BillDocType = NULL; }
	if(isset($_POST['txt_BillDocEntry'])) { $txt_BillDocEntry = "'".$_POST['txt_BillDocEntry']."'"; } else { $txt_BillDocEntry = "NULL"; }
	if(isset($_POST['txt_BillSAPVer'])) { $txt_BillSAPVer = "'".$_POST['txt_BillSAPVer']."'"; } else { $txt_BillSAPVer = "NULL"; }
	if(isset($_POST['txt_BillTeamCode'])) { $txt_BillTeamCode = "'".$_POST['txt_BillTeamCode']."'"; } else { $txt_BillTeamCode = "NULL"; }
	if(isset($_POST['txt_RefDoc2'])) { $txt_RefDoc2 = "'".$_POST['txt_RefDoc2']."'"; } else { $txt_RefDoc2 = "NULL"; }
	if(isset($_POST['txt_BillDocNum2'])) { $txt_BillDocNum2 = "'".$_POST['txt_Billการจ่ายค่า IncentiveDocNum2']."'"; } else { $txt_BillDocNum2 = "NULL"; }
	if(isset($_POST['txt_SendType'])) { $txt_SendType = "'".$_POST['txt_SendType']."'"; } else { $txt_SendType = "NULL"; }
	if(isset($_POST['txt_ShippingName'])) { $txt_ShippingName = "'".$_POST['txt_ShippingName']."'"; } else { $txt_ShippingName = "NULL"; }
	if(isset($_POST['txt_CoLogiName'])) { $txt_CoLogiName = "'".$_POST['txt_CoLogiName']."'"; } else { $txt_CoLogiName = "NULL"; }
	if(isset($_POST['txt_ShipCost'])) { $txt_ShipCost = "'".$_POST['txt_ShipCost']."'"; } else { $txt_ShipCost = "NULL"; }
	if(isset($_POST['txt_ShipCostBaht'])) { $txt_ShipCostBaht = "'".$_POST['txt_ShipCostBaht']."'"; } else { $txt_ShipCostBaht = "NULL"; }
	if(isset($_POST['txt_ShipCostName'])) { $txt_ShipCostName = "'".$_POST['txt_ShipCostName']."'"; } else { $txt_ShipCostName = "NULL"; }
	if(isset($_POST['txt_Att1'])) {
		if($_POST['txt_Att1'] == "true") {
			$txt_Att1 = "'Y'";
		} else {
			$txt_Att1 = "'N'";
		}
	} else {
		$txt_Att1 = "NULL";
	}
	if(isset($_POST['txt_Att2'])) {
		if($_POST['txt_Att2'] == "true") {
			$txt_Att2 = "'Y'";
		} else {
			$txt_Att2 = "'N'";
		}
	} else {
		$txt_Att2 = "NULL";
	}
	if(isset($_POST['txt_Att3'])) {
		if($_POST['txt_Att3'] == "true") {
			$txt_Att3 = "'Y'";
		} else {
			$txt_Att3 = "'N'";
		}
	} else {
		$txt_Att3 = "NULL";
	}
	if(isset($_POST['txt_BillCardCode'])) {
		$CardCode = explode(" | ",$_POST['txt_BillCardCode']);
		$txt_BillCardCode = "'".$CardCode[0]."'";
		$txt_BillCardName = "'".$CardCode[1]."'";
	} else {
		$txt_BillCardCode = "NULL";
		$txt_BillCardName = "NULL";
	}
	if(isset($_POST['txt_BillSlpCode'])) { $txt_BillSlpUkey = "'".$_POST['txt_BillSlpCode']."'"; } else { $txt_BillSlpUkey = "NULL"; }
	if(isset($_POST['txt_BillOwnerCode'])) { $txt_BillOwnerCode = "'".$_POST['txt_BillOwnerCode']."'"; } else { $txt_BillOwnerCode = "NULL"; }
	if(isset($_POST['txt_BillDate'])) { $txt_BillDate = "'".date("Y-m-d",strtotime($_POST['txt_BillDate']))."'"; } else { $txt_BillDate = "NULL"; }
	if(isset($_POST['txt_BillDueDate'])) { $txt_BillDueDate = "'".date("Y-m-d",strtotime($_POST['txt_BillDueDate']))."'"; } else { $txt_BillDueDate = "NULL"; }
	if(isset($_POST['txt_ReturnReason'])) { $txt_ReturnReason = "'".$_POST['txt_ReturnReason']."'"; } else { $txt_ReturnReason = "NULL"; }
	if(isset($_POST['txt_DeadStockType'])) { $txt_DeadStockType = "'".$_POST['txt_DeadStockType']."'"; } else { $txt_DeadStockType = "NULL"; }
	if(isset($_POST['txt_FreeBie'])) { $txt_FreeBie = "'".$_POST['txt_FreeBie']."'"; } else { $txt_FreeBie = "NULL"; }
	if(isset($_POST['txt_Incentive'])) { $txt_Incentive = "'".$_POST['txt_Incentive']."'"; } else { $txt_Incentive = "NULL"; }
	if(isset($_POST['txt_Incentivebaht'])) { $txt_Incentivebaht = "'".$_POST['txt_Incentivebaht']."'"; } else { $txt_Incentivebaht = "NULL"; }
	if(isset($_POST['txt_COSA_FineType'])) { $txt_COSA_FineType = "'".$_POST['txt_COSA_FineType']."'"; } else { $txt_COSA_FineType = "NULL"; }
	if(isset($_POST['txt_COSA_FineName'])) { $txt_COSA_FineName = "'".$_POST['txt_COSA_FineName']."'"; } else { $txt_COSA_FineName = "NULL"; }
	if(isset($_POST['txt_RefDoc3'])) { $txt_RefDoc3 = "'".$_POST['txt_RefDoc3']."'"; } else { $txt_RefDoc3 = "NULL"; }
	if(isset($_POST['txt_RefDoc3No'])) { $txt_RefDoc3No = "'".$_POST['txt_RefDoc3No']."'"; } else { $txt_RefDoc3No = "NULL"; }
	if(isset($_POST['txt_SALE_FineType'])) { $txt_SALE_FineType = "'".$_POST['txt_SALE_FineType']."'"; } else { $txt_SALE_FineType = "NULL"; }
	if(isset($_POST['txt_SALE_FineName'])) { $txt_SALE_FineName = "'".$_POST['txt_SALE_FineName']."'"; } else { $txt_SALE_FineName = "NULL"; }
	if(isset($_POST['txt_RefDoc4'])) { $txt_RefDoc4 = "'".$_POST['txt_RefDoc4']."'"; } else { $txt_RefDoc4 = "NULL"; }
	if(isset($_POST['txt_RefDoc4No'])) { $txt_RefDoc4No = "'".$_POST['txt_RefDoc4No']."'"; } else { $txt_RefDoc4No = "NULL"; }
	if(isset($_POST['txt_COSA_Remark'])) { $txt_COSA_Remark = "'".$_POST['txt_COSA_Remark']."'"; } else { $txt_COSA_Remark = "NULL"; }

	$CreateUkey = $_SESSION['ukey'];

	/* Get DocNum */
	$thisyear = substr(date("Y")+543,2,2);
	$Prefix   = $_POST['txt_DocType'];
	$SQL1 = "SELECT T0.DocNum FROM rtqc_header T0 WHERE T0.DocType = '$Prefix' AND T0.DocNum LIKE '%-$thisyear' ORDER BY T0.DocNum DESC LIMIT 1";
	$ROW1 = ChkRowDB($SQL1);
	
	if($ROW1 == 0) {
		$DocNum = $Prefix."0001-".$thisyear;
	} else {
		$RST1 = MySQLSelect($SQL1);
	
		$LastDocNum = $RST1['DocNum'];

		switch($Prefix) {
			case "AC": $start = 2; break;
			default:   $start = 1; break;
		}
		$OldDocNum = intval(substr($LastDocNum,$start,4));
		$NewDocNum = $OldDocNum+1;

		if($OldDocNum < 10) {
			$DocNum = $Prefix."000".$NewDocNum."-".$thisyear;
		} elseif($OldDocNum < 100) {
			$DocNum = $Prefix."00".$NewDocNum."-".$thisyear;
		} elseif($OldDocNum < 1000) {
			$DocNum = $Prefix."0".$NewDocNum."-".$thisyear;
		} else {
			$DocNum = $Prefix.$NewDocNum."-".$thisyear;
		}
	}

	if($BillDocType != NULL) {
		$SQL0 = "SELECT TOP 1 T0.SlpCode FROM $BillDocType T0 WHERE T0.DocEntry = $txt_BillDocEntry";
		// echo $txt_BillSAPVer;
		if($txt_BillSAPVer == "'8'") {
			$QRY0 = conSAP8($SQL0);
		} else {
			$QRY0 = SAPSelect($SQL0);
		}
		while($RST0 = odbc_fetch_array($QRY0)) {
			$txt_BillSlpCode = "'".$RST0['SlpCode']."'";
		}
	} else {
		$txt_BillSlpCode = "NULL";
	}

	/* Add Header */
	$SQL2 =
		"INSERT INTO rtqc_header SET
			DocNum = '$DocNum',
			DocType = $txt_DocType,
			DocStatus = 'P',
			AppStatus = 'P',
			DocDate = $txt_DocDate,
			BillDocNum = $txt_BillDocNum,
			BillSAPVer = $txt_BillSAPVer,
			BillEntry = $txt_BillDocEntry,
			BillType = $txt_BillDocType,
			BillCardCode = $txt_BillCardCode,
			BillCardName = $txt_BillCardName,
			BillSlpCode = $txt_BillSlpCode,
			BillSlpUkey = $txt_BillSlpUkey,
			BillTeamCode = $txt_BillTeamCode,
			BillDate = $txt_BillDate,
			BillDueDate = $txt_BillDueDate,
			BillOwnerCode = $txt_BillOwnerCode,
			BillOwnerName = NULL,
			BillCard2 = NULL,
			BillDate2 = NULL,
			BillDocNum2 = $txt_BillDocNum2,
			RefDoc1 = $txt_RefDoc1,
			RefDoc2 = $txt_RefDoc2,
			RefDoc3 = $txt_RefDoc3,
			RefDoc4 = $txt_RefDoc4,
			RefDoc3No = $txt_RefDoc3No,
			RefDoc4No = $txt_RefDoc4No,
			Att_1 = $txt_Att1,
			Att_2 = $txt_Att2,
			Att_3 = $txt_Att3,
			SendType = $txt_SendType,
			ShippingName = $txt_ShipCostName,
			CoLogiName = $txt_CoLogiName,
			ShipCost = $txt_ShipCost,
			ShipCostBaht = $txt_ShipCostBaht,
			ShipCostName = $txt_ShipCostName,
			ShipAccUkey = NULL,
			ReturnReason = $txt_ReturnReason,
			DeadStockType = $txt_DeadStockType,
			COSA_FineType = $txt_COSA_FineType,
			COSA_FineName = $txt_COSA_FineName,
			SALE_FineType = $txt_SALE_FineType,
			SALE_FineName = $txt_SALE_FineName,
			Incentive = $txt_Incentive,
			Incentivebaht = $txt_Incentivebaht,
			FreeBie = $txt_FreeBie,
			COSA_Remark = $txt_COSA_Remark,
			CreateUkey = '$CreateUkey'";
	// echo $SQL2;
	$DocEntry = MySQLInsert($SQL2);
	// $DocEntry = 1;

	if(isset($_POST['DataRow'])) { $DataRow = explode(",",$_POST['DataRow']); } else { $DataRow = "NULL"; }

	/* Loop DataRow */
	/*
		ItemRow Position
		Pos 0 = VisOrder
		Pos 1 = ItemCode
		Pos 2 = CodeBars
		Pos 3 = ItemName
		Pos 4 = ItemStatus
		Pos 5 = Quantity
		Pos 6 = UnitMsr
		Pos 7 = WhsCode
		Pos 8 = UnitPrice
		Pos 9 = GrandPrice
		Pos 10= Discount
	*/
	if($DataRow != "NULL") {
		for($i = 0; $i < count($DataRow); $i++) {
			$RowID    = "ItemRow_".$DataRow[$i];
			${$RowID} = explode("::",$_POST[$RowID]);

			$SQL3 =
				"INSERT INTO rtqc_detail SET
					DocEntry = $DocEntry,
					BillEntry = $txt_BillDocEntry,
					BillType = $txt_BillDocType,
					BillSAPVer = $txt_BillSAPVer,
					VisOrder = $i,
					BillOrder = '".${$RowID}[0]."',
					ItemCode = '".${$RowID}[1]."',
					CodeBars = '".${$RowID}[2]."',
					ItemName = '".str_replace("'","\'",${$RowID}[3])."',
					ItemStatus = '".${$RowID}[4]."',
					WhsCode = '".${$RowID}[7]."',
					GrandPrice = '".floatval(preg_replace('/[^\d.]/', '',${$RowID}[9]))."',
					Discount = '".${$RowID}[10]."',
					UnitPrice = '".floatval(preg_replace('/[^\d.]/', '',${$RowID}[8]))."',
					Quantity = '".floatval(preg_replace('/[^\d.]/', '',${$RowID}[5]))."',
					UnitMsr = '".${$RowID}[6]."',
					SA_Grade = '".${$RowID}[8]."',
					SA_WhsCode = '".${$RowID}[9]."',
					CreateUkey = '".$CreateUkey."'
				";
			MySQLInsert($SQL3);
		}
	}

	/* Loop Attachment */
	if(isset($_FILES['DocAttach']['name'])) {
		$Totals = count($_FILES['DocAttach']['name']);
		for($i = 0; $i < $Totals; $i++) {
			$FileProcess  = explode(".",basename($_FILES['DocAttach']['name'][$i]));
			$countProcess = count($FileProcess);
			if($countProcess == 2) {
				$FileOriName = $FileProcess[0];
				$FileExt     = $FileProcess[1];
			} else {
				$FileOriName = "";
				$FileExt     = $FileProcess[$countProcess-1];
				for($n = 0; $n <= $countProcess-2; $n++) {
					$FileOriName .= $FileProcess[$n].".";
				}
				$FileOriName = substr($FileOriName,0,-1);
			}
			$tmpFilePath = $_FILES['DocAttach']['tmp_name'][$i];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/RTQC/".$DocNum."-".$i.".".$FileExt;
				move_uploaded_file($tmpFilePath, $NewFilePath);
				$SQL4 = 
					"INSERT INTO rtqc_attach SET
						DocEntry = $DocEntry,
						VisOrder = $i,
						FileOriName = '$FileOriName',
						FileDirName = '".$DocNum."-".$i."',
						FileExt = '$FileExt',
						UploadUkey = '$CreateUkey'";
				MySQLInsert($SQL4);
			}
		}
	}

	/* Approval */
	$TeamCode = $_POST['txt_BillTeamCode'];
	switch($TeamCode) {
		case "MT1":
		case "EXP":
		case "MT2":
		case "TT2":
			$uClass = "18,21";
			switch($TeamCode) {
				case "MT1":
				case "EXP":
					$DeptCode = "DP006";
					break;
				case "MT2":
					$DeptCode = "DP007";
					break;
				case "TT2":
					$DeptCode = "DP005";
					break;
			}
			break;
		case "TT1":
		case "OUL":
			$DeptCode = "DP008"; $uClass = "18,19";
			break;
		case "ONL":
			$DeptCode = "DP003"; $uClass = "3,19";
			break;
		default:break;
	}

	$SQL5 = "SELECT T0.uKey FROM users T0 LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode WHERE T1.DeptCode = '$DeptCode' AND T1.uClass IN ($uClass) AND T0.UserStatus = 'A' ORDER BY T1.uClass DESC";
	$QRY5 = MySQLSelectX($SQL5);
	$AppOrder = 0;
	while($RST5 = mysqli_fetch_array($QRY5)) {
		$AppUkeyReq = $RST5['uKey'];
		$SQL6 =
			"INSERT INTO rtqc_approve SET
				DocEntry = $DocEntry,
				VisOrder = $AppOrder,
				AppUkeyReq = '$AppUkeyReq',
				CreateUkey = '$CreateUkey'";
		MySQLInsert($SQL6);
		$AppOrder++;
	}

	$arrCol['Status'] = "SUCCESS";
}

if($_GET['p'] == "PreviewDoc") {
	$DocEntry   = $_POST['DocEntry'];
	$int_status = $_POST['int_status'];
	$SQL1 =
		"SELECT
			T0.DocEntry, T0.DocNum, T0.RefDoc1, T0.RefDoc2, T0.DocDate, T0.DocType, T0.BillDocNum, T0.BillDocNum2,
			T0.BillCardCode, T0.BillCardName, T0.BillDate, T0.BillDueDate,
			CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')') AS 'SlpName', CONCAT(T3.uName,' ',T3.uLastName,' (',T3.uNickName,')') AS 'OwnName',
			T0.Att_1, T0.Att_2, T0.Att_3,
			T0.SendType, T0.ShippingName, CONCAT(T4.uName,' ',T4.uLastName,' (',T4.uNickName,')') AS 'CoLogiName',
			T0.ShipCost, T0.ShipCostBaht, CONCAT(T5.uName,' ',T5.uLastName,' (',T5.uNickName,')') AS 'ShipCostName',
			T0.ReturnReason, T0.DeadStockType, T0.Incentive, T0.Incentivebaht, T0.FreeBie, T0.COSA_Remark,
			T0.COSA_FineType, CONCAT(T6.uName,' ',T6.uLastName,' (',T6.uNickName,')') AS 'FineCOSAName', T0.RefDoc3, T0.RefDoc3No,
			T0.SALE_FineType, CONCAT(T7.uName,' ',T7.uLastName,' (',T7.uNickName,')') AS 'FineSALEName', T0.RefDoc4, T0.RefDoc4No,
			T1.ItemCode, T1.ItemName, T1.ItemStatus, T1.WhsCode, T1.GrandPrice, T1.Discount, T1.UnitPrice, T1.Quantity, T1.UnitMsr
		FROM rtqc_header T0
		LEFT JOIN rtqc_detail T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN users T2 ON T0.BillSlpUkey    = T2.uKey
		LEFT JOIN users T3 ON T0.BillOwnerCode  = T3.uKey
		LEFT JOIN users T4 ON T0.CoLogiName     = T4.uKey
		LEFT JOIN users T5 ON T0.ShipCostName   = T5.uKey
		LEFT JOIN users T6 ON T0.COSA_FineName  = T6.uKey
		LEFT JOIN users T7 ON T0.SALE_FineName  = T7.uKey
		WHERE T0.DocEntry = $DocEntry
		ORDER BY T1.VisOrder ASC";

	$ROW1 = CHKRowDB($SQL1);
	if($ROW1 == 0) {
		$arrCol['HEAD']['Row'] = 0;
		$arrCol['FOOT'] = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal'>ตกลง</button>";
	} else {
		$arrCol['HEAD']['Row'] = $ROW1;
		$QRY1 = MySQLSelectX($SQL1);
		$i = 0;
		$DocEntry = NULL;
		while($RST1 = mysqli_fetch_array($QRY1)) {
			if($DocEntry == NULL) {
				$DocEntry = $RST1['DocEntry'];
				switch($RST1['DocType']) {
					case "D":
						$TypeName = "คืนเพื่อลดหนี้";
						$BillDocNum = "ใบกำกับภาษีเลขที่ <b class='text-danger'>".$RST1['BillDocNum']."</b>";
					break;
					case "L":
						$TypeName = "คืนจากการยืม";
						$BillDocNum = "ใบยืมสินค้าเลขที่ <b class='text-danger'>".$RST1['BillDocNum']."</b>";
					break;
					case "AC":
						$TypeName = "คืนแบบไม่มีสินค้า (คืนลอย)";
						$BillDocNum = "เอกสารเลขที่ <b class='text-danger'>".$RST1['BillDocNum']."</b> / เปิดใบใหม่เลขที่ <b class='text-danger'>".$RST1['BillDocNum2']."</b>";
					break;
					default:
						$TypeName = "คืนจากการที่คลังส่งของผิด ส่งเกิน";
						$BillDocNum = "เอกสารเลขที่ <b class='text-danger'>".$RST1['BillDocNum']."</b> / เอกสาร FM-WH-17 เลขที่ <b class='text-danger'>".$RST1['RefDoc2']."</b> / เอกสาร PC เลขที่ <b class='text-danger'>".$RST1['BillDocNum2']."</b>";
					break;
				}
				if($RST1['Att_1'] == "Y") { $Att1 = "<i class='far fa-check-square fa-fw fa-1x'></i>"; } else { $Att1 = "<i class='far fa-square fa-fw fa-1x'></i>"; }
				if($RST1['Att_2'] == "Y") { $Att2 = "<i class='far fa-check-square fa-fw fa-1x'></i>"; } else { $Att2 = "<i class='far fa-square fa-fw fa-1x'></i>"; }
				if($RST1['Att_3'] == "Y") { $Att3 = "<i class='far fa-check-square fa-fw fa-1x'></i>"; } else { $Att3 = "<i class='far fa-square fa-fw fa-1x'></i>"; }

				if($RST1['SendType'] == "1") {
					$SendType = "ลูกค้าฝากส่งคืน ผ่านขนส่งชื่อ ".$RST1['ShippingName']." (ธุรการขนส่ง คุณ".$RST1['CoLogiName']."เป็นผู้รับสินค้า)";
				} else {
					$SendType = "เซลส์รับกลับมาคืน";
				}

				if($RST1['ShipCost'] == "Y") {
					$ShipCost = "มีค่าขนส่ง ".number_format($RST1['ShipCostBaht'],2)." บาท ให้คุณ".$RST1['ShipCostName']."รับผิดชอบค่าขนส่ง";
				} else {
					$ShipCost = "ไม่มีค่าขนส่ง";
				}

				if($RST1['Incentive'] == "Y") { $Incentive = "ได้รับค่า Incentive แล้ว (".number_format($RST1['Incentivebaht'],0)."บาท)"; } else { $Incentive = "ยังไม่ได้รับค่า Incentive"; }
				if($RST1['FreeBie'] == "Y") { $FreeBie = "ใช่"; } else { $FreeBie = "ไม่ใช่"; }
				if($RST1['COSA_FineType'] == "Y") {
					$COSA_FineType = "มีค่าปรับธุรการเซลส์ 20 บาท โดยปรับจากคุณ<span style'font-weight: bold;'>".$RST1['FineCOSAName']."</span> (อ้างอิงใบวินัยธุรการเซลส์เลขที่: <span style'font-weight: bold;' class='text-danger'>".$RST1['RefDoc3']."</span> ข้อที่: <span style'font-weight: bold;' class='text-danger'>".$RST1['RefDoc3No']."</span>)";
				} else {
					$COSA_FineType = "ไม่มีค่าปรับ";
				}
				if($RST1['SALE_FineType'] == "Y") {
					$SALE_FineType = "มีค่าปรับเซลส์ 50 บาท โดยปรับจากคุณ<span style'font-weight: bold;'>".$RST1['FineSALEName']."</span> (อ้างอิงใบวินัยเซลส์เลขที่: <span style'font-weight: bold;' class='text-danger'>".$RST1['RefDoc4']."</span> ข้อที่: <span style'font-weight: bold;' class='text-danger'>".$RST1['RefDoc4No']."</span>)";
				} else {
					$SALE_FineType = "ไม่มีค่าปรับ";
				}

				switch($RST1['ReturnReason']) {
					case "1.1": $ReturnReason = "1.1 ลูกค้าสั่งผิด"; break;
					case "1.2": $ReturnReason = "1.2 คู่แข่งตัดราคา"; break;
					case "1.3": $ReturnReason = "1.3 ลูกค้ามีปัญหาด้านการเงิน"; break;
					case "1.4": $ReturnReason = "1.4 LAZADA"; break;
					case "1.5":
						switch($RST1['DeadStockType']) {
							case "1": $DeadType = "0 - 6 เดือน (100% ของราคาขาย)"; break;
							case "2": $DeadType = "7 - 12 เดือน (80% ของราคาขาย)"; break;
							case "3": $DeadType = "13 - 24 เดือน (50% ของราคาขาย)"; break;
							case "4": $DeadType = "25 เดือนขึ้นไป (30% ของราคาขาย)"; break;
						}
						$ReturnReason = "1.5 สินค้า Dead Stock ($DeadType)";
					break;
					case "1.6": $ReturnReason = "1.6 ลูกค้าไม่มั่นใจคุณภาพสินค้า"; break;
					case "2.1": $ReturnReason = "2.1 เซลส์แจ้งผิด"; break;
					case "2.2": $ReturnReason = "2.2 ธุรการเซลส์เปิดบิลผิด"; break;
					case "2.3": $ReturnReason = "2.3 คลัง/ขนส่งผิด"; break;
					case "2.4": $ReturnReason = "2.4 ยืมออกตลาด"; break;
					case "2.5": $ReturnReason = "2.5 สินค้าฝากขาย (Consign)"; break;
					case "2.6": $ReturnReason = "2.6 ยืมออกบูธ"; break;
					case "2.7": $ReturnReason = "2.7 ยืมไปทดลอง/ใช้งาน"; break;
					case "2.8": $ReturnReason = "2.8 ยืมไปเปลี่ยนสินค้าชำรุด"; break;
					case "3.1": $ReturnReason = "3.1 อุปกรณ์ไม่ครบ"; break;
					case "3.2": $ReturnReason = "3.2 ชำรุดจากโรงงาน"; break;
					case "3.3": $ReturnReason = "3.3 ชำรุดจากขนส่ง"; break;
					case "3.4": $ReturnReason = "3.4 ชำรุดจากลูกค้า"; break;
					case "4.1": $ReturnReason = "4.1 เหตุการณ์ภายในประเทศ"; break;
				}

				$Attach =
					$Att1." ฟอร์มการคืนสินค้า (ต้นฉบับ)<br/>".
					$Att2." สำเนาใบกำกับภาษี<br/>".
					$Att3." สำเนาใบยืม (PA) / ส่งสินค้าผิด (PC)<br/>".
					"<i class='far fa-check-square fa-fw fa-1x'></i> ภาพถ่ายสินค้า <a href='javascript:void(0);' onclick='ViewAttach($DocEntry);'><i class='fas fa-search-plus fa-fw fa-1x'></i> คลิกเพื่อดู</a>";

				$arrCol['HEAD']['DocNum']         = $RST1['DocNum'];
				$arrCol['HEAD']['RefDoc1']        = $RST1['RefDoc1'];
				$arrCol['HEAD']['DocDate']        = date("d/m/Y",strtotime($RST1['DocDate']));
				$arrCol['HEAD']['DocType']        = $TypeName;
				$arrCol['HEAD']['BillDocNum']     = $BillDocNum;
				$arrCol['HEAD']['BillCardCode']   = $RST1['BillCardCode']." | ".$RST1['BillCardName'];
				$arrCol['HEAD']['BillDocDate']    = date("d/m/Y",strtotime($RST1['BillDate']));
				$arrCol['HEAD']['BillDocDueDate'] = date("d/m/Y",strtotime($RST1['BillDueDate']));
				$arrCol['HEAD']['BillSlpName']    = $RST1['SlpName'];
				$arrCol['HEAD']['BillOwnName']    = $RST1['OwnName'];
				$arrCol['HEAD']['Attach']         = $Attach;
				$arrCol['HEAD']['ReturnReason']   = $ReturnReason;
				$arrCol['HEAD']['Incentive']      = $Incentive;
				$arrCol['HEAD']['Freebie']        = $FreeBie;
				$arrCol['HEAD']['Remark']         = $RST1['COSA_Remark'];
				$arrCol['HEAD']['SendType']       = $SendType;
				$arrCol['HEAD']['ShipCost']       = $ShipCost;
				$arrCol['HEAD']['FineCOSA']       = $COSA_FineType;
				$arrCol['HEAD']['FineSALE']       = $SALE_FineType;

				
			}

			$arrCol['BODY'][$i]['ItemCode']   = $RST1['ItemCode'];
			$arrCol['BODY'][$i]['ItemName']   = $RST1['ItemName'];
			$arrCol['BODY'][$i]['ItemStatus'] = $RST1['ItemStatus'];
			$arrCol['BODY'][$i]['WhsCode']    = $RST1['WhsCode'];
			$arrCol['BODY'][$i]['GrandPrice'] = number_format($RST1['GrandPrice'],3);
			$arrCol['BODY'][$i]['Discount']   = $RST1['Discount'];
			$arrCol['BODY'][$i]['UnitPrice']  = number_format($RST1['UnitPrice'],3);
			$arrCol['BODY'][$i]['Quantity']   = number_format($RST1['Quantity'],0);
			$arrCol['BODY'][$i]['UnitMsr']    = $RST1['UnitMsr'];
			
			$i++;
		}

		$SQL2 = "SELECT T0.AttachID, T0.FileOriName, T0.FileDirName, T0.FileExt FROM rtqc_attach T0 WHERE T0.DocEntry = $DocEntry";
		$QRY2 = MySQLSelectX($SQL2);
		$ROW2 = ChkRowDB($SQL2);
		$arrCol['ATTACH']['ROW'] = $ROW2;
		$i = 0;
		while($RST2 = mysqli_fetch_array($QRY2)) {
			$arrCol['ATTACH'][$i]['AttachID'] = $RST2['AttachID'];
			$arrCol['ATTACH'][$i]['FileOriName'] = $RST2['FileOriName'].".".$RST2['FileExt'];
			$arrCol['ATTACH'][$i]['FileDirName'] = $RST2['FileDirName'].".".$RST2['FileExt'];
			$i++;
		}

		$SQL3 = 
			"SELECT
				T0.ApproveID, T0.VisOrder, T0.AppUkeyReq, CONCAT(T1.uName,' ',T1.uLastName,' (',T1.uNickName,')') AS 'NameReq',
				T0.AppState, IFNULL(T0.AppRemark,'') AS 'AppRemark', IFNULL(CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')'),'') AS 'NameAct',
				T0.AppDate
			FROM rtqc_approve T0
			LEFT JOIN users T1 ON T0.AppUkeyReq = T1.uKey
			LEFT JOIN users T2 ON T0.AppUkeyAct = T2.uKey
			WHERE T0.DocEntry = $DocEntry";
		$QRY3 = MySQLSelectX($SQL3);
		$ROW3 = ChkRowDB($SQL3);
		$arrCol['APPROVE']['ROW'] = $ROW3;
		$i = 0;
		$prev_state = "Y";
		while($RST3 = mysqli_fetch_array($QRY3)) {
			$arrCol['APPROVE'][$i]['ApproveID'] = $RST3['ApproveID'];
			$arrCol['APPROVE'][$i]['VisOrder']  = $RST3['VisOrder']+1;
			$arrCol['APPROVE'][$i]['NameReq']   = $RST3['NameReq'];
			$arrCol['APPROVE'][$i]['AppState']  = $RST3['AppState'];
			$arrCol['APPROVE'][$i]['AppRemark'] = $RST3['AppRemark'];
			$arrCol['APPROVE'][$i]['NameAct']   = $RST3['NameAct'];
			if($RST3['AppDate'] == NULL || $RST3['AppDate'] == "NULL") {
				$arrCol['APPROVE'][$i]['AppDate'] = "";
			} else {
				$arrCol['APPROVE'][$i]['AppDate'] = date("d/m/Y",strtotime($RST3['AppDate']))." ".date("H:i",strtotime($RST3['AppDate']))." น.";
			}

			if($prev_state == "Y" && $RST3['AppState'] == "1" && $RST3['AppUkeyReq'] == $_SESSION['ukey']) {
				$arrCol['APPROVE'][$i]['APP'] = "Y";
			} else {
				$arrCol['APPROVE'][$i]['APP'] = "N";
			}

			$prev_state = $RST3['AppState'];
			$i++;
		}

		$footer = "";
		switch($int_status) {
			case "1":
			case 1:
				$footer .= "<button type='button' class='btn btn-outline-danger btn-sm' onclick='CancelDoc($DocEntry);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกเอกสาร</button>";
			break;
			case "2":
			case 2:
				$footer .= "<button type='button' class='btn btn-outline-danger btn-sm' onclick='CancelDoc($DocEntry);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกเอกสาร</button>";
			break;
			case "3":
			case 3:
				$footer .= "<button type='button' class='btn btn-outline-danger btn-sm' onclick='CancelDoc($DocEntry);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกเอกสาร</button>";
			break;
			case "4":
			case 4:
				$footer .= "<button type='button' class='btn btn-outline-danger btn-sm' onclick='CancelDoc($DocEntry);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกเอกสาร</button>";
				$footer .= "<button type='button' class='btn btn-outline-info btn-sm' onclick='PrintDoc($DocEntry);'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button>";
			break;
			case "5":
			case 5:
				$footer .= "<button type='button' class='btn btn-outline-info btn-sm' onclick='PrintDoc($DocEntry);'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button>";
			break;
			default: $footer .= ""; break;
		}
		$footer .= "<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'><i class='fas fa-times fa-fw fa-1x'></i> ปิดหน้าต่าง</button>";
		$arrCol['FOOT'] = $footer;

	}
}

if($_GET['p'] == "CancelDoc") {
	$DocEntry = $_POST['DocEntry'];
	$Ukey = $_SESSION['ukey'];

	$SQL1 = "UPDATE rtqc_header SET CANCELED = 'Y', DocStatus = 'C', UpdateUkey = '$Ukey', CancelUkey = '$Ukey', UpdateDate = NOW(), CancelDate = NOW() WHERE DocEntry = $DocEntry";
	MySQLUpdate($SQL1);

	$arrCol['Status'] = "SUCCESS";
}

if($_GET['p'] == "AppList") {
	$ukey = $_SESSION['ukey'];

	$Limit = NULL;
	if(isset($_GET['tab'])) {
		if($_GET['tab'] == "Y") {
			$Limit = " LIMIT 5";
		}
	}

	$SQL1 =
		"SELECT A0.* FROM
			(SELECT
				CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName', T4.DeptName,
				T0.*, CONCAT(T5.uName,' ',T5.uLastName) AS 'SlpName', T1.VisOrder AS 'NowState', T1.AppState AS 'NowApprove',
				CASE WHEN T1.VisOrder > 0 THEN T1.VisOrder - 1 ELSE NULL END AS 'PrevState',
				CASE WHEN T1.VisOrder > 0 THEN (SELECT P0.AppState FROM memo_approve P0 WHERE P0.DocEntry = T0.DocEntry AND P0.VisOrder = T1.VisOrder - 1 LIMIT 1) ELSE NULL END AS 'PrevApprove'
			FROM rtqc_header T0
			LEFT JOIN rtqc_approve T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN users T2 ON T0.CreateUkey = T2.uKey
			LEFT JOIN positions T3 ON T2.LvCode = T3.LvCode
			LEFT JOIN departments T4 ON T3.DeptCode = T4.DeptCode
			LEFT JOIN users T5 ON T0.BillSlpUkey = T5.ukey
			WHERE ((T1.AppUkeyReq = '$ukey') OR (T0.CreateUkey = '$ukey')) AND (T0.CANCELED = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P' AND T1.AppState = '1')
		) A0
		WHERE CASE WHEN (A0.PrevState IS NOT NULL) THEN A0.PrevApprove = 'Y' ELSE A0.PrevApprove IS NULL END
		ORDER BY A0.DocEntry DESC";
	$ROW1 = ChkRowDB($SQL1);
	
	if($ROW1 == 0) {
		$arrCol['ROW'] = 0;
	} else {
		$arrCol['ROW'] = $ROW1;
		$QRY1 = MySQLSelectX($SQL1);
		$i = 0;
		$no = 1;

		while($RST1 = mysqli_fetch_array($QRY1)) {
			$int_status = 0;
			if($RST1['CANCELED'] == "Y") {
				$int_status = 0; /* ยกเลิก */
			}
			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "Y" && $RST1['DocStatus'] == "O") {
				$int_status = 1; /* บันทึกร่าง */
			}

			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "N" && $RST1['DocStatus'] == "P" && $RST1['AppStatus'] == "P") {
				$int_status = 2; /* เอกสารรอตรวจสอบ */
			}

			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "N" && $RST1['DocStatus'] == "P" && $RST1['AppStatus'] == "Y") {
				$int_status = 3; /* เอกสารผ่านการอนุมัติ */
			}
			
			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "N" && $RST1['DocStatus'] == "C" && $RST1['AppStatus'] == "N") {
				$int_status = 4; /* เอกสารไม่ผ่านการอนุมัติ */
			}

			if($RST1['CANCELED'] == "N" && $RST1['DraftStatus'] == "N" && $RST1['DocStatus'] == "C" && $RST1['AppStatus'] == "Y") {
				$int_status = 5; /* เอกสารเสร็จสมบูรณ์ */
			}
			switch($int_status) {
				case 0:   $txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>"; break;
				case 1:   $txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>"; break;
				case 1.5: $txt_status = "<span class='badge bg-primary w-100'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>"; break;
				case 2:   $txt_status = "<span class='badge w-100' style='background-color: #C79910; color: #FFF;'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>"; break;
				case 3:   $txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
				case 4:   $txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
				case 5:   $txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>"; break;
			}
			switch($RST1['DocType']) {
				case "D":  $TypeName = "คืนเพื่อลดหนี้"; break;
				case "L":  $TypeName = "คืนจากการยืม"; break;
				case "AC": $TypeName = "คืนแบบไม่มีสินค้า (คืนลอย)"; break;
				default:   $TypeName = "คืนจากการที่คลังส่งของผิด ส่งเกิน"; break;
			}
			$arrCol['BODY'][$i]['no']           = number_format($no);
			$arrCol['BODY'][$i]['DocDate']      = date("d/m/Y",strtotime($RST1['DocDate']));
			$arrCol['BODY'][$i]['DocType']      = $TypeName;
			$arrCol['BODY'][$i]['DocNum']       = "<a href='javascript:void(0);' onclick='PreviewDoc(".$RST1['DocEntry'].",$int_status);'>".$RST1['DocNum']."</a>";
			$arrCol['BODY'][$i]['BillCardCode'] = $RST1['BillCardCode']." | ".$RST1['BillCardName']."<br/><small>ผู้จัดทำ: ".$RST1['CreateName']."</small>";
			$arrCol['BODY'][$i]['RefDocNum']    = $RST1['RefDoc1'];
			$arrCol['BODY'][$i]['BillDocNum']   = $RST1['BillDocNum'];
			$arrCol['BODY'][$i]['BillSlpName']  = $RST1['SlpName'];
			$arrCol['BODY'][$i]['txt_status']   = $txt_status;
			$arrCol['BODY'][$i]['int_status']   = $int_status;
			
			$no++;
			$i++;
		}
	}

}

if($_GET['p'] == "AppSave") {
	$AppState  = $_POST['AppState'];
	$ApproveID = $_POST['ApproveID'];
	$ukey      = $_SESSION['ukey'];

	$SQL0 = "SELECT T0.DocEntry FROM rtqc_approve T0 WHERE T0.ApproveID = $ApproveID LIMIT 1";
	// echo $SQL0;
	$RST0 = MySQLSelect($SQL0);
	$DocEntry = $RST0['DocEntry'];

	if($_POST['AppRemark'] == "") {
		$AppRemark = "NULL";
	} else {
		$AppRemark = "'".$_POST['AppRemark']."'";
	}

	$SQL1 = "UPDATE rtqc_approve SET AppState = '$AppState', AppRemark = $AppRemark, AppUkeyAct = '$ukey', AppDate = NOW() WHERE ApproveID = $ApproveID";
	MySQLUpdate($SQL1);

	if($AppState == "N") {
		$SQL2 = "UPDATE rtqc_header SET AppStatus = 'N', DocStatus = 'C' UpdateUkey = '$ukey', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
		// echo $SQL2;
		MySQLUpdate($SQL2);
	} else {
		/* CHECK NEXT STATE IF ROW = 0 APPROVE IN HEADER */
		$SQL3 = "SELECT T0.ApproveID, T0.AppUkeyReq FROM rtqc_approve T0 WHERE T0.DocEntry = $DocEntry AND T0.ApproveID > $ApproveID LIMIT 1";
		$ROW3 = ChkRowDB($SQL3);
		if($ROW3 == 0) {
			$SQL4 = "UPDATE rtqc_header SET AppStatus = 'Y', Printed = 'Y', UpdateUkey = '$ukey', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
			MySQLUpdate($SQL4);
		}
	}
}

if($_GET['p'] == "SendDoc") {
	$DocEntry = $_POST['DocEntry'];

	$SQL1 = "SELECT T0.DocEntry, T0.DocNum, T0.DocDate, T0.BillCardCode AS 'CardCode', T0.BillCardName AS 'CardName', T0.BillDocNum AS 'RefDocNum' FROM rtqc_header T0 WHERE T0.DocEntry = $DocEntry LIMIT 1";
	$ROW1 = ChkRowDB($SQL1);
	if($ROW1 == 0) {
		$arrCol['ROW'] = 0;
	} else {
		$arrCol['ROW'] = $ROW1;
		$RST1 = MySQLSelect($SQL1);

		$uKey      = $_SESSION['ukey'];
		$RtQcEntry = $RST1['DocEntry'];
		$DocNum    = $RST1['DocNum'];
		$DocDate   = date("Y-m-d",strtotime($RST1['DocDate']));
		$CardCode  = $RST1['CardCode'];
		$CardName  = $RST1['CardName'];
		$RefDocNum = $RST1['RefDocNum'];

		$SQL2 =
			"INSERT INTO docrtqc_header SET
				RtqcEntry = $RtQcEntry,
				DocNum    = '$DocNum',
				DocStatus = 'O',
				DocDate   = '$DocDate',
				CardCode  = '$CardCode',
				CardName  = '$CardName',
				RefDocNum = '$RefDocNum',
				SenderUkey = '$uKey'";
		$DocEntry = MySQLInsert($SQL2);

		if($DocEntry > 0) {
			$arrCol['Status'] = "SUCCESS";
			$SQL3 = "UPDATE rtqc_header SET DocStatus = 'C', UpdateUkey = '$uKey', UpdateDate = NOW() WHERE DocEntry = $RtQcEntry";
			$QRY3 = MySQLUpdate($SQL3);
		} else {
			$arrCol['Status'] = "ERROR";
		}

	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
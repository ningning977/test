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

if($_GET['p'] == "GetOrderList") {
	if(!isset($_POST['filt_year'])) { $y = date("Y"); } else { $y = $_POST['filt_year']; }
	if(!isset($_POST['filt_month'])) { $m = date("m"); } else { $m = $_POST['filt_month']; }
/*
	switch ($_SESSION['DeptCode']){
		case 'DP003':
			$OrderWhr = " AND (T3.MainTeam IN ('KBI,'ONL','MKT' ) ";
		break;
		case 'DP010':
			$OrderWhr = " AND T0.SlpCode = 98 ";
		break;
		case 'DP005':
			$OrderWhr = " AND (T3.MainTeam IN ('TT2') OR T0.SlpCode IN (291,296,60)) ";
		break;
		case 'DP006':
			$OrderWhr = " AND (T3.MainTeam IN ('MT1','EXP') OR T0.SlpCode IN (291,296,60)) ";
		break;
		case 'DP007':
			$OrderWhr = " AND (T3.MainTeam IN ('MT2') OR T0.SlpCode IN (291,296,60)) ";
		break;
		case 'DP008':
			$OrderWhr = " AND (T3.MainTeam IN ('OUL','TT1') OR T0.SlpCode IN (291,296,60)) ";
		break;
		default :

		break;
	}
*/

	if($_POST['filt_team'] == "ALL") {
		$OrderWhr = "";
	} elseif($_SESSION['DeptCode'] != "DP008" && $_SESSION['DeptCode'] != "DP009") {
		switch($_SESSION['uClass']) {
			case 20: $OrderWhr = " AND T3.MainTeam = '".$_POST['filt_team']."' AND T0.CreateUkey = '".$_SESSION['ukey']."' "; break;
			default: $OrderWhr = " AND T3.MainTeam = '".$_POST['filt_team']."' "; break;
		}
	} else {
		$OrderWhr = " AND T3.MainTeam = '".$_POST['filt_team']."' ";
	}

	$OrderSQL = "SELECT
		T0.DocEntry, T0.DocType, T0.DocNum, T0.CANCELED, T0.DraftStatus, T0.AppStatus, T0.DocStatus, T0.Printed, T0.CreateDate,
		CONCAT(T0.DocType,'V-',T0.DocNum) AS 'DocumentNo', T0.DocDate, T0.DocDueDate, T0.CardCode, T0.CardName,
		T0.SlpCode, T3.SlpName, T3.MainTeam, T0.DocTotal, T0.U_PONo, T0.CreateUkey, CONCAT(T1.uName,' ',T1.uLastName) AS 'CreateName', T1.LvCode, T2.DeptCode, T0.ImportEntry,
		(SELECT COUNT(P0.ID) FROM apporder P0 WHERE P0.DocEntry = T0.DocEntry AND P0.ResultApp != '0') AS 'Approved',
		(SELECT COUNT(P0.ID) FROM apporder P0 WHERE P0.DocEntry = T0.DocEntry) AS 'Approve'
	FROM order_header T0
	LEFT JOIN users T1     ON T0.CreateUkey = T1.Ukey
	LEFT JOIN positions T2 ON T1.LvCode     = T2.LvCode
	LEFT JOIN OSLP T3      ON T0.SlpCode    = T3.SlpCode 
	WHERE (YEAR(T0.CreateDate) = $y AND MONTH(T0.CreateDate) = $m) $OrderWhr 
	ORDER BY
        CASE
			WHEN (T0.DocStatus = 'O' AND T0.CANCELED = 'N') THEN 1
			WHEN (T0.DocStatus = 'P' AND T0.AppStatus = 'P' AND T0.CANCELED = 'N') THEN 2
			WHEN (T0.DocStatus = 'P' AND T0.AppStatus = 'N' AND T0.CANCELED = 'N') THEN 3
			WHEN (T0.DocStatus = 'C' AND T0.CANCELED = 'N') THEN 4
			WHEN (T0.CANCELED = 'Y') THEN 5
		ELSE 99 END,
        CASE
			WHEN (T0.DocType = 'SO') THEN 1
			WHEN (T0.DocType = 'SN') THEN 2
			WHEN (T0.DocType = 'SA') THEN 3
			WHEN (T0.DocType = 'SB') THEN 4
        ELSE 5 END,
        T0.DocEntry DESC";
	$Rows = CHKRowDB($OrderSQL);
	if($Rows == 0) {
		$output .= "<tr class='table-active text-muted'><td colspan='10' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$no = 1;
		$OrderQRY = MySQLSelectX($OrderSQL);
		while($OrderRST = mysqli_fetch_array($OrderQRY)) {

			if($OrderRST['CANCELED'] == "Y") {
				$int_status = 0;
			} elseif($OrderRST['DraftStatus'] == "Y") {
				$int_status = 1;
			} elseif($OrderRST['DocStatus'] == "P") {
				switch($OrderRST['AppStatus']) {
					case "Y": $int_status = 3; break;
					case "N": $int_status = 4; break;
					default:  $int_status = 2; break;
				}
			} elseif($OrderRST['DocStatus'] == "C") {
				$int_status = 5;
			} else {
				$int_status = 3;
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
				| 4          | N        | N           | P         | N         || NO        | NO         | NO          | -> เอกสารไม่อนุมัติ
				| 5          | N        | N           | C         | Y         || YES       | YES        | NO          | -> เอกสารเสร็จสมบูรณ์ (Import เข้า SAP เรียบร้อย)
				+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
			*/
			$txt_print  = "<a class='btn btn-secondary btn-sm' href='javascript:void(0)'><i class='fas fa-print fa-fw fa-1x'></i></a>";
			$dis_edit   = NULL;
			$dis_prnt   = NULL;
			$dis_import = NULL;
			$dis_cncl   = NULL;

			if($OrderRST['ImportEntry'] != "" && $int_status == 5) {
				$GetSAPSQL = "SELECT TOP 1 (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum' FROM ORDR T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE T0.DocEntry = ".$OrderRST['ImportEntry'];
				if (ChkRowSAP($GetSAPSQL) > 0){
					$GetSAPQRY = SAPSelect($GetSAPSQL);
					$GetSAPRST = odbc_fetch_array($GetSAPQRY);
					//echo $GetSAPSQL."<br>";
					$SAPDocNum = $GetSAPRST['DocNum'];
				}
			} else {
				if($int_status == 5) {
					$SAPDocNum = "<strong class='text-danger'>Import Error!</strong>";
				} else {
					$SAPDocNum = NULL;
				}
				
			}

			if($_SESSION['DeptCode'] != $OrderRST['DeptCode'] && $_SESSION['DeptCode'] != "DP002") {
				switch($int_status) {
					case 0:   $txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>"; break;
					case 1:   $txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>"; break;
					case 1.5: $txt_status = "<span class='badge bg-primary w-100'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>"; break;
					case 2:   $txt_status = "<span class='badge w-100' style='background-color: #C79910; color: #FFF;'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ <b>[".$OrderRST['Approved']."/".$OrderRST['Approve']."]</b></span>";
					case 3:   $txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
					case 4:   $txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
					case 5:   $txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>"; break;
				}
				$dis_edit   = " disabled";
				$dis_prnt   = " disabled";
				$dis_import = " disabled";
				$dis_cncl   = " disabled";
			} else {
				switch($int_status) {
					case 0:
						$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
						break;
					case 1:
						$txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
						// $dis_import = " disabled";
						break;
					case 1.5:
						$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
						$dis_edit   = " disabled";
						$dis_import = " disabled";
						break;
					case 2:
						$txt_status = "<span class='badge' style='background-color: #C79910; color: #FFF;'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ <b>[".$OrderRST['Approved']."/".$OrderRST['Approve']."]</b></span>";
						$dis_edit   = " disabled";
						// $dis_import = " disabled";
						break;
					case 3:
						$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
						$dis_edit   = " disabled";
						break;
					case 4:
						$txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
						$dis_edit   = " disabled";
						break;
					case 5:
						$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
						$dis_edit   = " disabled";
						$dis_cncl   = " disabled";
						// $dis_import = " disabled";
						
						break;
				}
			}
			if($int_status != 0) {
				$txt_opt = "<div calss='dropdown'>";
					$txt_opt.= "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
						$txt_opt.= "<i class='fas fa-cog fa-fw fa-1x'></i>";
					$txt_opt.= "</button>";
					$txt_opt.= "<ul class='dropdown-menu' style='font-size: 13px;'>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item ordr-view' onclick='PreviewSO(".$OrderRST['DocEntry'].",$int_status)'><i class='fas fa-info fa-fw fa-1x'></i> รายละเอียด</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item ordr-edit$dis_edit' onclick='EditSO(".$OrderRST['DocEntry'].")'><i class='fas fa-edit fa-fw fa-1x'></i> แก้ไขใบสั่งขาย</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item ordr-impt$dis_import' onclick='ExportSO(".$OrderRST['DocEntry'].")'><i class='fas fa-share-square fa-fw fa-1x'></i> Import to SAP</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item ordr-prnt$dis_prnt' onclick='PrintSO(".$OrderRST['DocEntry'].",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์ใบสั่งขาย/ใบเสนอราคา</a></li>";
						//$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item ordr-impt$dis_import' data-docentry='".$OrderRST['DocEntry']."'><i class='fas fa-file fa-fw fa-1x'></i> ส่งออกเป็นใบสั่งขาย</li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item ordr-cncl$dis_cncl' onclick='CancelSO(".$OrderRST['DocEntry'].")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกใบสั่งขาย</a></li>";
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

			$output .= "<tr$row_cls>";
				$output .= "<td class='text-right'>".number_format($no)."</td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($OrderRST['DocDate']))."</td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($OrderRST['DocDueDate']))."</td>";
				$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='PreviewSO(".$OrderRST['DocEntry'].",$int_status)'>".$OrderRST['DocumentNo']."</a></td>";
				$output .= "<td>".$OrderRST['CardCode']." ".$OrderRST['CardName']."<br/><small>ผู้จัดทำ: ".$OrderRST['CreateName']."</small></td>";
				$output .= "<td>".$OrderRST['U_PONo']."</td>";
				$output .= "<td class='text-right'>".number_format($OrderRST['DocTotal'],2)."</td>";
				$output .= "<td><span class='badge bg-dark'>".$OrderRST['MainTeam']."</span>".$OrderRST['SlpName']."</td>";
				$output .= "<td class='text-center'>".$txt_status."</td>";
				$output .= "<td class='text-center'>$SAPDocNum</td>";
				$output .= "<td>$txt_opt</td>";
			$output .= "</tr>";
			$no++;
		}
	}
	$arrCol['output'] = $output;
}

if($_GET['p'] == "SOPreview") {
	$DocEntry   = $_POST['DocEntry'];
	$int_status = $_POST['int_status'];

	/* HEADER */
	$HeaderSQL = "SELECT T0.DocEntry,
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
		$ItemList .= "<td colspan='4' rowspan='5' class='align-top'><span class='font-weight'>หมายเหตุ:</span><br/>".$HeaderRST['Comments']."</td>";
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
		$ItemList .= "<td class='text-right font-weight'>".number_format($txt_doctotal,2)."</td>";
	$ItemList .= "</tr>";

	$arrCol['view_ItemList'] = $ItemList;


	/* ADDRESS */
	$arrCol['view_BilltoAddress'] = "<span class='font-weight'>".$HeaderRST['BilltoCode']."</span><br/>".str_replace($HeaderRST['BilltoCode'],"",$HeaderRST['AddressBillto']);
	$arrCol['view_ShiptoAddress'] = "<span class='font-weight'>".$HeaderRST['ShiptoCode']."</span><br/>".str_replace($HeaderRST['ShiptoCode'],"",$HeaderRST['AddressShipto']);
	$ShippingCode = $HeaderRST['ShippingType'];
	$ShippingSQL = "SELECT TOP 1 T0.Code, T0.Name, T0.U_Address FROM [dbo].[@SHIPPINGTYPE] T0 WHERE T0.Code = N'".SapTHSearch($ShippingCode)."'";
	$ShippingQRY = SAPSelect($ShippingSQL);
	$ShippingType = "";
	while($ShippingRST = odbc_fetch_array($ShippingQRY)) {
		$ShippingType =  "<span class='font-weight'>".conutf8($ShippingRST['Name'])." (".$ShipCostType.")</span><br/>".conutf8($ShippingRST['U_Address']);
	}
	$arrCol['view_ShippingType'] = $ShippingType;
	$arrCol['view_ShipComment'] = $HeaderRST['ShipComment'];

	/* ATTACH */
	$AttSQL = "SELECT T0.AttachID, T0.VisOrder, T0.FileOriName, T0.FileDirName, T0.FileExt, T0.UploadDate FROM order_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileStatus = 'A' ORDER BY T0.VisOrder";
	$AttRow = CHKRowDB($AttSQL);
	if($AttRow == 0) {
		$AttachList = "<tr><td class='text-center' colspan='4'>ไม่มีเอกสารแนบ :(</td></tr>";
	} else {
		$AttQRY = MySQLSelectX($AttSQL);
		$AttachList = "";
		$no = 1;
		while($AttRST = mysqli_fetch_array($AttQRY)) {
			$AttachList .= "<tr>";
				$AttachList .= "<td class='text-right'>".number_format($no,0)."</td>";
				$AttachList .= "<td>".$AttRST['FileOriName'].".".$AttRST['FileExt']."</td>";
				$AttachList .= "<td class='text-center'>".date("d/m/Y",strtotime($AttRST['UploadDate']))." เวลา ".date("H:i",strtotime($AttRST['UploadDate']))." น.</td>";
				$AttachList .= "<td class='text-center'><a class='btn btn-success btn-sm' href='../FileAttach/SO/".$AttRST['FileDirName'].".".$AttRST['FileExt']."' target='_blank'><i class='fas fa-file-download fa-fw fa-1x'></i></a></td>";
			$AttachList .= "</tr>";
			$no++;
		}
	}

	$arrCol['view_attachlist'] = $AttachList;


	/* APPROVE */
	$AppSQL = "SELECT
				T0.ID, T0.LvApp, T0.StepApprove, T0.AppSO, T0.AppCR, T0.AppGP,T0.ResultApp,
				IFNULL(CONCAT(T1.uName,' ',T1.uLastName),T3.PositionName) AS 'ApproveName', T1.uNickName AS 'ApproveNick',
				T0.ApproveDate, T0.Remark, CONCAT(T4.uName,' ',T4.uLastName) AS 'ApprovedName', T4.uNickName AS 'ApprovedNick'
			FROM apporder T0
			LEFT JOIN users T1 ON T0.UkeyReq = T1.Ukey
			LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
			LEFT JOIN positions T3 ON T0.UkeyReq = T3.LvCode
			LEFT JOIN users T4 ON T0.UkeyApprove = T4.Ukey
			WHERE T0.DocEntry = $DocEntry 
			ORDER BY T0.StepApprove,T0.LvApp,  T2.LvCode DESC";
	$Approve = "";
	
	switch($int_status) {
		case 2:
		case 3:
		case 4:
		case 5:
			$Row = ChkRowDB($AppSQL);
			if($Row > 0) {
				$AppQRY = MySQLSelectX($AppSQL);
				$no = 1;
				while($AppRST = mysqli_fetch_array($AppQRY)) {
					$Text_App = null;
					$Text_Remark = $AppRST['Remark'];

					if($AppRST['ApproveDate'] == null) {
						$AppDate = null;
					} else {
						$AppDate = date("d/m/Y",strtotime($AppRST['ApproveDate']))." เวลา ".date("H:i",strtotime($AppRST['ApproveDate']))." น.";
					}
					if($AppRST['AppSO'] == "0") {
						$Icon_AppSO = "<i class='fas fa-minus fa-fw fa-1x'></i>";
					} else {
						$Icon_AppSO = "<i class='fas fa-check fa-fw fa-1x'></i>";
					}
					if($AppRST['AppCR'] == "0") {
						$Icon_AppCR = "<i class='fas fa-minus fa-fw fa-1x'></i>";
					} else {
						$Icon_AppCR = "<i class='fas fa-check fa-fw fa-1x'></i>";
					}
					if($AppRST['AppGP'] == "0") {
						$Icon_AppGP = "<i class='fas fa-minus fa-fw fa-1x'></i>";
					} else {
						$Icon_AppGP = "<i class='fas fa-check fa-fw fa-1x'></i>";
					}

					/* รอพิจารณา 001 010 011 100 110 111 */
					/* อนุมัติ     Y00 YY0 YYY Y0Y 00Y 0YY */
					/* ไม่อนุมัติ   N00 NN0 NNN N0N 00N 0NN */
					/* 000 ยกเว้นการพิจารณาเนื่องจากผ่านเงื่อนไข */
					$AppState = $AppRST['AppSO'].$AppRST['AppCR'].$AppRST['AppGP'];
					switch($AppState) {
						case "001":
						case "010":
						case "011":
						case "100":
						case "110":
						case "111":
						case "Y00":
						case "YY0":
						case "YYY":
						case "Y0Y":
						case "00Y":
						case "0YY":
						case "N00":
						case "NN0":
						case "NNN":
						case "N0N":
						case "00N":
						case "0NN":  break;
						default:    $Text_App = ""; $Text_Remark = "ยกเว้นการพิจารณาเนื่องจากเอกสารผ่านเงื่อนไข"; break;
					}

					switch($AppRST['ResultApp']) {
						case "Y":
							$Text_App = "<span class='text-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
						break;
						case "N":
							$Text_App = "<span class='text-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
						break;
						case "0":
						default:
							$Text_App = "<span class='text-muted'><i class='far fa-clock fa-fw fa-lg'></i> รอพิจารณา</span>";
						break;
					}

					if($AppRST['ApproveNick'] == NULL || $AppRST['ApproveNick'] == "") {
						$NickName = "";
					} else {
						$NickName = " (".$AppRST['ApproveNick'].")";
					}

					if($AppRST['ApprovedNick'] == NULL || $AppRST['ApprovedNick'] == "") {
						$NickName2 = "";
					} else {
						$NickName2 = " (".$AppRST['ApprovedNick'].")";
					}
					$Approve .= "<tr>";
						$Approve .= "<td class='text-right'>$no</td>";
						$Approve .= "<td>".$AppRST['ApproveName'].$NickName."</td>";
						$Approve .= "<td class='text-center'>$Icon_AppSO</td>";
						$Approve .= "<td class='text-center'>$Icon_AppCR</td>";
						$Approve .= "<td class='text-center'>$Icon_AppGP</td>";
						$Approve .= "<td>$Text_App</td>";
						$Approve .= "<td>$Text_Remark</td>";
						$Approve .= "<td>".$AppRST['ApprovedName'].$NickName2."</td>";
						$Approve .= "<td class='text-center'>$AppDate</td>";
					$Approve .= "</tr>";
					$no++;
				}
			} else {
				$Approve = "<tr><td class='text-center' colspan='9'>ไม่มีข้อมูลการขออนุมัติ :)</td></tr>";
			}
		break;
		default:
			$Approve = "<tr><td class='text-center' colspan='9'>ไม่มีข้อมูลการขออนุมัติ :)</td></tr>";
		break;
	}
	$arrCol['view_approvelist'] = $Approve;
	$arrCol['footer'] = $btn_cancel." ".$btn_edit." ".$btn_print." ".$btn_import;
}

if($_GET['p'] == "CancelSO") {
	$DocEntry = $_POST['DocEntry'];
	$Reasons  = $_POST['Reasons'];

	$CancelSQL = "UPDATE order_header SET CANCELED = 'Y', DocStatus = 'C', CancelDate = NOW(), CancelUkey = '".$_SESSION['ukey']."', CancelReason = '$Reasons' WHERE DocEntry = $DocEntry";
	$CancelQRY = MySQLUpdate($CancelSQL);
	if(!isset($CancelQRY)) {
		echo "ERROR";
	} else {
		echo "SUCCESS";
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
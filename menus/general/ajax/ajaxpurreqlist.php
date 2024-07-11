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

if($_GET['p'] == "GetPurReqList") {
	if(!isset($_POST['filt_year'])) { $y = date("Y"); } else { $y = $_POST['filt_year']; }
	if(!isset($_POST['filt_month'])) { $m = date("m"); } else { $m = $_POST['filt_month']; }

	/* WHERE TEAM PURCAHSE */
	if($_SESSION['DeptCode'] == "DP004") {
		switch($_SESSION['LvCode']) {
			case "LV023":
			case "LV024":
				$WhrPU = " AND (T0.DocType = 'LC')";
			break;
			case "LV025":
			case "LV026":
				$WhrPU = " AND (T0.DocType = 'IM')";
			break;
			default: $WhrPU = "";
		}
	} else {
		$WhrPU = "";
	}
	
	/* WHERE VIA FILTER */
	if($_POST['filt_team'] == "ALL") {
		$PurWhr = "";
	} else {
		$PurWhr = " AND (T2.DeptCode = '".$_POST['filt_team']."')";
	}

	$PurReqSQL = "SELECT
			T0.DocEntry, T0.DocType, T0.DocNum, T0.CANCELED, T0.DraftStatus, T0.AppStatus, T0.DocStatus, T0.Printed, T0.CreateDate,
			CONCAT(T0.DocType,T0.DocNum) AS 'DocumentNo', T0.DocDate, T0.DocDueDate, T0.ProductType, T4.TypeName, T0.CreateUkey,
			CONCAT(T1.uName,' ',T1.uLastName) AS 'CreateName', T1.LvCode, T2.DeptCode, T3.DeptName, T0.DocRemark
		FROM purreq_header T0
		LEFT JOIN users T1 ON T0.CreateUkey = T1.Ukey
		LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
		LEFT JOIN departments T3 ON T2.DeptCode = T3.DeptCode
		LEFT JOIN purreq_ItemType T4 ON T0.ProductType = T4.TypeCode
		WHERE (YEAR(T0.CreateDate) = $y AND MONTH(T0.CreateDate) = $m) $WhrPU $PurWhr
		ORDER BY
			CASE WHEN (T0.CANCELED = 'N') THEN 1 ELSE 2 END,
			CASE WHEN (T0.DocType = 'IM') THEN 1 ELSE 2 END,
			T0.DocEntry DESC";
	$Rows = CHKRowDB($PurReqSQL);
	if($Rows == 0) {
		$output .= "<tr class='table-active text-muted'><td colspan='10' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$no = 1;
		$PurReqQRY = MySQLSelectX($PurReqSQL);
		while($PurReqRST = mysqli_fetch_array($PurReqQRY)) {
			if($PurReqRST['CANCELED'] == "Y") {
				$int_status = 0;
			} elseif($PurReqRST['DraftStatus'] == "Y") {
				$int_status = 1;
			} elseif($PurReqRST['DocStatus'] == "P") {
				switch($PurReqRST['AppStatus']) {
					case "Y": $int_status = 3; break;
					case "N": $int_status = 4; break;
					default:  $int_status = 2; break;
				}
			} elseif($PurReqRST['DocStatus'] == "C") {
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

			switch($int_status) {
				case 0:
					$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
					break;
				case 1:
					$txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
					$dis_import = " disabled";
					break;
				case 1.5:
					$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
					$dis_edit   = " disabled";
					$dis_import = " disabled";
					break;
				case 2:
					$txt_status = "<span class='badge bg-warning w-100'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>";
					$dis_edit   = " disabled";
					$dis_import = " disabled";
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
					$dis_import = " disabled";
					break;
			}

			if($int_status != 0) {
				$txt_opt = "<div calss='dropdown'>";
					$txt_opt.= "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
						$txt_opt.= "<i class='fas fa-cog fa-fw fa-1x'></i>";
					$txt_opt.= "</button>";
					$txt_opt.= "<ul class='dropdown-menu' style='font-size: 13px;'>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-view' onclick='PreviewPR(".$PurReqRST['DocEntry'].",$int_status)'><i class='fas fa-info fa-fw fa-1x'></i> รายละเอียด</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-impt$dis_import' onclick='ExportPR(".$PurReqRST['DocEntry'].")'><i class='fas fa-share-square fa-fw fa-1x'></i> ส่งออก EDI</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-prnt$dis_prnt' onclick='PrintPR(".$PurReqRST['DocEntry'].",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์ใบขอซื้อ</a></li>";
						//$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item ordr-impt$dis_import' data-docentry='".$PurReqRST['DocEntry']."'><i class='fas fa-file fa-fw fa-1x'></i> ส่งออกเป็นใบสั่งขาย</li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-cncl$dis_cncl' onclick='CancelPR(".$PurReqRST['DocEntry'].")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกใบขอซื้อ</a></li>";
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
				$output .= "<td class='text-right'>$no</td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($PurReqRST['DocDate']))."</td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($PurReqRST['DocDueDate']))."</td>";
				$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='PreviewPR(".$PurReqRST['DocEntry'].",$int_status)'>".$PurReqRST['DocumentNo']."</a></td>";
				$output .= "<td>".$PurReqRST['TypeName']."</td>";
				$output .= "<td>".$PurReqRST['DocRemark']."</td>";
				$output .= "<td>".$PurReqRST['DeptName']."<br/><small>ผู้จัดทำ: ".$PurReqRST['CreateName']."</small></td>";
				$output .= "<td class='text-center'>".$txt_status."</td>";
				$output .= "<td>$txt_opt</td>";
			$output .= "</tr>";
			$no++;
		}
	}
	$arrCol['output'] = $output;
}

if($_GET['p'] == "PRPreview") {
	$DocEntry   = $_POST['DocEntry'];
	$int_status = $_POST['int_status'];

	/* HEADER */
	$HeaderSQL = "SELECT
			CONCAT(T0.DocType,T0.DocNum) AS 'DocNum',
			CONCAT(T1.uName,' ',T1.uLastName) AS 'CreateName', T3.DeptName,
			T0.DocDate, T0.DocDueDate, T0.DocType, T4.TypeName, T0.ShiptoType, T0.ShiptoAddress, T0.ItemQuotaTeam,
			(SELECT MAX(P0.UnitCur) FROM purreq_detail P0 WHERE P0.DocEntry = T0.DocEntry) AS 'DocCur',
			(SELECT MAX(P0.UnitRate) FROM purreq_detail P0 WHERE P0.DocEntry = T0.DocEntry) AS 'DocRate',
			T0.DocRemark
		FROM purreq_header T0
		LEFT JOIN users T1 ON T0.CreateUkey = T1.Ukey
		LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
		LEFT JOIN departments T3 ON T2.DeptCode = T3.DeptCode
		LEFT JOIN purreq_itemtype T4 ON T0.ProductType = T4.TypeCode
		WHERE T0.DocEntry = $DocEntry LIMIT 1";
	$HeaderRST = MySQLSelect($HeaderSQL);

	switch($int_status) {
		case 0:
			$txt_status = "<span class='badge bg-secondary'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span> เนื่องจาก: ".$HeaderRST['Description'];
			$btn_print  = null;
			$btn_cancel = null;
			$btn_import = null;
			break;
		case 1:
			$txt_status = "<span class='badge bg-info'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
			$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintPR(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
			$btn_cancel  = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelPR(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
			$btn_import = null;
			break;
		case 1.5:
			$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
			$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintPR(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
			$btn_cancel  = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelPR(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
			$btn_import = null;
			break;
		case 2:
			$txt_status = "<span class='badge bg-warning'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>";
			$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintPR(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
			$btn_cancel = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelPR(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
			$btn_import = null;
			break;
		case 3:
			$txt_status = "<span class='badge bg-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
			$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintPR(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
			$btn_cancel = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelPR(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
			$btn_import = "<a href='javascript:void(0);' class='btn btn-info btn-sm' onclick='ExportSO(".$DocEntry.")'><i class='fas fa-share-square fa-fw fa-1x'></i> ส่งออก</a>";
			break;
		case 4:
			$txt_status = "<span class='badge bg-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
			$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintPR(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
			$btn_cancel = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelPR(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
			$btn_import = null;
			break;
		case 5:
			$txt_status = "<span class='badge bg-success'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
			$btn_print  = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm' onclick='PrintSO(".$DocEntry.",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a>";
			$btn_cancel = "<a href='javascript:void(0);' class='btn btn-outline-danger btn-sm' onclick='CancelSO(".$DocEntry.")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</a>";
			$btn_import = null;
			break;
	}

	switch($HeaderRST['DocType']) {
		case "LC": $txt_DocType = "สั่งซื้อสินค้าในประเทศ"; break;
		case "IM": $txt_DocType = "สั่งซื้อสินค้าต่างประเทศ"; break;
		default: $txt_DocType = "ไม่ระบุ"; break;
	}
	switch($HeaderRST['ShiptoType']) {
		case "KBI": $txt_ShiptoType = "สำนักงานใหญ่ (KBI)"; break;
		case "KSY": $txt_ShiptoType = "คลังสินค้าลาดสวาย (KSY / KSM)"; break;
		case "OTR": $txt_ShiptoType = $HeaderRST['ShiptoAddress']; break;
		default: $txt_ShiptoType = "ไม่ระบุ"; break;
	}
	switch($HeaderRST['DocCur']) {
		case "THB": $txt_cur = "THB"; break;
		default: $txt_cur = $HeaderRST['DocCur']." (1 ".$HeaderRST['DocCur']." = ".$HeaderRST['DocRate'].")"; break;
	}

	$arrCol['view_DocNum']     = $HeaderRST['DocNum'];
	$arrCol['view_CreateName'] = $HeaderRST['CreateName'];
	$arrCol['view_DeptName']   = $HeaderRST['DeptName'];
	$arrCol['view_DocDate']    = date("d/m/Y",strtotime($HeaderRST['DocDate']));
	$arrCol['view_DocDueDate'] = date("d/m/Y",strtotime($HeaderRST['DocDueDate']));
	$arrCol['view_DocType']    = $txt_DocType;
	$arrCol['view_TypeName']   = $HeaderRST['TypeName'];
	$arrCol['view_ShiptoType'] = $txt_ShiptoType;
	$arrCol['view_DocCurSign'] = $HeaderRST['DocCur'];
	$arrCol['view_DocCur']     = $txt_cur;
	$arrCol['view_ItemQuota']  = $HeaderRST['ItemQuotaTeam'];
	$arrCol['view_DocRate']    = $HeaderRST['DocRate'];
	$arrCol['view_DocRemark']  = $HeaderRST['DocRemark'];

	/* ITEM LIST */
	$ItemListSQL = "SELECT
			T0.ItemCode, T0.ItemName, T0.ItemStatus, T0.WhsCode, T0.Qty, T0.OpenQty, T0.UnitMsr,
			T0.UnitPrice, T0.UnitPriceTHB, T0.LineTotal, T0.LineTotalTHB, T0.SalePriceTHB
		FROM purreq_detail T0
		WHERE T0.DocEntry = $DocEntry AND T0.LineStatus != 'I'";
	$ItemListQRY = MySQLSelectX($ItemListSQL);
	$ItemList = "";
	$no = 1;
	while($ItemListRST = mysqli_fetch_array($ItemListQRY)) {
		$NameLen = mb_strlen($ItemListRST['ItemName'],'UTF-8');
		if($NameLen <= 32) {
			$ItemName = $ItemListRST['ItemName'];
		} else {
			$ItemName = iconv_substr($ItemListRST['ItemName'],0,32,'UTF-8')."...";
		}

		if($ItemListRST['UnitPrice'] == 0) {
			$UnitPrice = "-";
		} else {
			$UnitPrice = number_format($ItemListRST['UnitPrice'],3);
		}
		if($ItemListRST['LineTotal'] == 0) {
			$LineTotal = "-";
		} else {
			$LineTotal = number_format($ItemListRST['LineTotal'],3);
		}
		if($ItemListRST['SalePriceTHB'] == 0) {
			$SalePriceTHB = "-";
			$GP = "-";
		} else {
			$SalePriceTHB = number_format($ItemListRST['SalePriceTHB'],3);
			$GP = number_format((($ItemListRST['SalePriceTHB']-$ItemListRST['UnitPriceTHB'])/$ItemListRST['SalePriceTHB'])*100,2);
		}

		$ItemList .= "<tr>";
			$ItemList .= "<td class='text-right'>".number_format($no,0)."</td>";
			$ItemList .= "<td class='text-center'>".$ItemListRST['ItemCode']."</td>";
			$ItemList .= "<td>".$ItemName."</td>";
			$ItemList .= "<td width='7.5%' class='text-right'>".number_format($ItemListRST['Qty'],0)."</td>";
			$ItemList .= "<td width='6.25%'>".$ItemListRST['UnitMsr']."</td>";
			$ItemList .= "<td class='text-right'>".$UnitPrice."</td>";
			$ItemList .= "<td class='text-right' style='font-weight: bold;'>".$LineTotal."</td>";
			$ItemList .= "<td class='text-right'>".$SalePriceTHB."</td>";
			$ItemList .= "<td class='text-center'>".$GP."</td>";
		$ItemList .= "</tr>";
		$no++;
	}

	$arrCol['view_ItemList'] = $ItemList;

	/* ATTACH */
	$AttSQL = "SELECT T0.AttachID, T0.VisOrder, T0.FileOriName, T0.FileDirName, T0.FileExt, T0.UploadDate FROM purreq_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileStatus = 'A' ORDER BY T0.VisOrder";
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

	$arrCol['footer'] = $btn_cancel." ".$btn_print." ".$btn_import;

}

if($_GET['p'] == "CancelPR") {
	$DocEntry = $_POST['DocEntry'];

	$CancelSQL = "UPDATE purreq_header SET CANCELED = 'Y', DocStatus = 'C', CancelDate = NOW(), CancelUkey = '".$_SESSION['ukey']."' WHERE DocEntry = $DocEntry";
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
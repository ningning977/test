<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName']== NULL) {
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
		case "RP": $TypeName = "ฝากรับสินค้าที่ฝากซื้อ"; break;
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


if($_GET['p'] == "GetLogList") {
	$year     = $_POST['y'];
	$month    = $_POST['m'];
	$DeptCode = $_POST['t'];
	if($DeptCode == "ALL") {
		$ListWhr = NULL;
	} else {
		$ListWhr = " AND T3.DeptCode = '$DeptCode'";
	}

	$GetListSQL = 
		"SELECT
			T0.DocEntry, T0.WOEntry, T0.DocNum, T5.TypeOrder, T0.CANCELED, T0.DocDate, T0.CardCode, T0.CardName, T3.DeptCode, T0.DocTitle,
			T1.uName AS 'SendName', T1.uNickName AS 'SendNickName', T0.SenderDate, T0.RecipientStatus,
			T2.uName AS 'RecipientName', T0.RecipientDate, T4.RecipientRemark,
			DATE(T5.TimeContrac) AS 'DocDueDate', T6.DeptName
		FROM docwho_header T0
		LEFT JOIN users T1 ON T0.SenderUkey = T1.ukey
		LEFT JOIN users T2 ON T0.RecipientUkey = T2.ukey
		LEFT JOIN positions T3 ON T1.LvCode = T3.LvCode
		LEFT JOIN docwho_remark T4 ON T0.DocEntry = T4.DocEntry AND T4.RemarkStatus = 'A'
		LEFT JOIN OWAS T5 ON T0.WOEntry = T5.DocEntry
		LEFT JOIN departments T6 ON T3.DeptCode = T6.DeptCode
		WHERE (YEAR(T0.SenderDate) = $year AND MONTH(T0.SenderDate) = $month $ListWhr) OR (T0.RecipientStatus = '1' AND T0.CANCELED = 'N' $ListWhr) 
		ORDER BY CASE WHEN T0.CANCELED = 'N' THEN 1 ELSE 2 END, T0.DocNum, T0.DocEntry";
		// echo $GetListSQL;
	$Rows = ChkRowDB($GetListSQL);
	if($Rows > 0) {
		$GetListQRY = MySQLSelectX($GetListSQL);
		$output = "";
		while($GetListRST = mysqli_fetch_array($GetListQRY)) {
			if($GetListRST['SendNickName'] == "") {
				$NickName = NULL;
			} else {
				$NickName = " (".$GetListRST['SendNickName'].")";
			}

			if($GetListRST['RecipientName'] == "") {
				$Recipient = "ยังไม่รับเอกสาร";
			} else {
				$Recipient = $GetListRST['RecipientName']." (".date("d/m/Y",strtotime($GetListRST['RecipientDate'])).")";
			}

			if($GetListRST['CANCELED'] == "Y") {
				$trcls = " class='table-active'";
				$chckY = " disabled";
				$chckN = " disabled";
				$cnclD = " disabled";
				$remkD = " disabled";
			} else {
				$trcls = NULL; /* table classes */
				$chckY = NULL; /* checked Accept */
				$chckN = NULL; /* checked Reject */
				$cnclD = NULL; /* Cancel sent doc */
				$remkD = NULL; /* Remark Account */

				$DeptCode = $_SESSION['DeptCode'];
				$DeptCond = array("DP001","DP002","DP011");
				$DeptChck = array_search($DeptCode,$DeptCond);
				if(!$DeptChck) {
					/* ไม่ใช่ฝ่ายบริหาร / IT / และ คลัง */
					$chckY = " disabled";
					$chckN = " disabled";
					$remkD = " disabled";
					
					switch($GetListRST['RecipientStatus']) {
						case "Y": $chckY = " checked disabled"; $chckN = " disabled"; $cnclD = " disabled"; break;
						case "N": $chckN = " checked disabled"; $chckY = " disabled"; $cnclD = " disabled"; $trcls = " class='table-danger text-danger'"; break;
					}
				} else {
					/* ใช่ */
					switch($GetListRST['RecipientStatus']) {
						case "Y": $chckY = " checked disabled"; $chckN = " disabled"; $cnclD = " disabled"; break;
						case "N": $chckN = " checked disabled"; $chckY = " disabled"; $cnclD = " disabled"; $trcls = " class='table-danger text-danger'"; break;
					}
				}
			}


			if($GetListRST['CardCode'] == "") {
				$ShowTitle = $GetListRST['CardName'];
			} else {
				$ShowTitle = $GetListRST['CardCode']." | ".$GetListRST['CardName'];
			}
				$DocNum    = $GetListRST['DocNum'];

			$output .= "<tr$trcls>";
				$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='PreviewDoc(".$GetListRST['WOEntry'].");'>$DocNum</a></td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($GetListRST['DocDate']))."</td>";
				$output .= "<td><strong>$ShowTitle</strong><br/><small>เอกสาร: ".DocTypeName($GetListRST['TypeOrder'])."</small></td>";
				$output .= "<td>".$GetListRST['DeptName']."<br/><small>ผู้จัดทำ: ".$GetListRST['SendName'].$NickName."</small></td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($GetListRST['SenderDate']))."</td>";
				$output .= "<td class='text-center'><input class='form-check-input' type='radio' name='Accept_".$GetListRST['DocEntry']."' onclick='ReceiveDoc(".$GetListRST['DocEntry'].",\"Y\")' $chckY /></td>";
				$output .= "<td class='text-center'><input class='form-check-input' type='radio' name='Accept_".$GetListRST['DocEntry']."' onclick='ReceiveDoc(".$GetListRST['DocEntry'].",\"N\")' $chckN /></td>";
				$output .= "<td>$Recipient</td>";
				$output .= "<td><input type='text' class='form-control form-control-sm' name='Remark_".$GetListRST['DocEntry']."' id='Remark_".$GetListRST['DocEntry']."' value='".$GetListRST['RecipientRemark']."' data-DocEntry='".$GetListRST['DocEntry']."' placeholder='ระบุข้อความ (ถ้ามี)' $remkD />";
				$output .= "<td>";
					$output .= "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
						$output .= "<i class='fas fa-cog fa-fw fa-1x'></i>";
					$output .= "</button>";
					$output .= "<ul class='dropdown-menu' style='font-size: 13px;'>";
						$output .= "<li><a href='javascript:void(0);' class='dropdown-item' onclick='HistoryDoc(".$GetListRST['DocEntry'].");'><i class='fas fa-history fa-fw fa-1x'></i> ประวัติ</a></li>";
						$output .= "<li><a href='javascript:void(0);' class='dropdown-item$cnclD' onclick='CancelDoc(".$GetListRST['DocEntry'].")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</li>";
					$output .= "</ul>";
				$output .= "</td>";
			$output .= "</tr>";
		}
	} else {
		$output .= "<tr><td class='text-center' colspan='10'>ไม่มีข้อมูล :(</td></tr>";
	}
	$arrCol['DocList'] = $output;
}

if($_GET['p'] == "ReceiveDoc") {
	$DocEntry = $_POST['DocEntry'];
	$Status   = $_POST['Status'];
	$Receiver = $_SESSION['ukey'];
	$ChkSQL   = "SELECT T0.DocEntry, T0.WOEntry, T1.TypeOrder FROM docwho_header T0 LEFT JOIN OWAS T1 ON T0.WOEntry = T1.DocEntry WHERE T0.DocEntry = $DocEntry";
	$Rows     = ChkRowDB($ChkSQL);
	if($Rows == 0) {
		$arrCol['AddStatus'] = "ERR:NO_RESULT";
	} else {
		$ChkRST = MySQLSelect($ChkSQL);
		$WOEntry = $ChkRST['WOEntry'];
		$DocType = $ChkRST['TypeOrder'];

		$UpdateSQL = "UPDATE docwho_header SET DocStatus = 'C', RecipientDate = NOW(), RecipientStatus = '$Status', RecipientUkey = '$Receiver' WHERE DocEntry = $DocEntry";
		MySQLUpdate($UpdateSQL);

		$arrCol['AddStatus'] = "REJECTED";

		if($Status == "Y") {
			$UpdateSQL = "UPDATE OWAS SET StatusDoc = 5, LastUpdate = NOW(), ukeyUpdate = '".$_SESSION['ukey']."' WHERE DocEntry = $WOEntry";
			// echo $UpdateSQL;
			MySQLUpdate($UpdateSQL);
			$arrCol['WOEntry']   = $WOEntry;
			$arrCol['DocType']   = $DocType;
			$arrCol['AddStatus'] = "SUCCESS";
		}
		
	}
}

if($_GET['p'] == "SaveRemark") {
	$DocEntry = $_POST['DocEntry'];
	$DocText  = $_POST['Content'];

	$ChkSQL = "SELECT T0.RemarkID FROM docwho_remark T0 WHERE T0.DocEntry = $DocEntry  ORDER BY T0.RemarkID DESC LIMIT 1";
	$Rows   = ChkRowDB($ChkSQL);
	if($Rows == 0) {
		if($DocText != "" || $DocText != NULL) {
			$InsertSQL = "INSERT INTO docwho_remark SET DocEntry = $DocEntry, RecipientRemark = '$DocText', RecipientUkey = '".$_SESSION['ukey']."'";
			MySQLInsert($InsertSQL);
		}
	} else {
		$LastIDSQL = "SELECT T0.RemarkID, T0.RecipientRemark FROM docwho_remark T0 WHERE T0.DocEntry = '$DocEntry' AND T0.RemarkStatus = 'A' ORDER BY T0.RemarkID DESC LIMIT 1";
		$LastIDRST = MySQLSelect($LastIDSQL);
		if($LastIDRST['RecipientRemark'] != $DocText) {
			if($DocText != '') {
				$Comment = "'$DocText'";
			} else {
				$Comment = "NULL";
			}
			$UpdateSQL = "UPDATE docwho_remark SET RemarkStatus = 'I' WHERE RemarkID = ".$LastIDRST['RemarkID'];
			MySQLUpdate($UpdateSQL);

			$InsertSQL = "INSERT INTO docwho_remark SET DocEntry = '$DocEntry', RecipientRemark = $Comment, RecipientUkey = '".$_SESSION['ukey']."'";
			MySQLInsert($InsertSQL);
		}
	}
}

if($_GET['p'] == "HistoryDoc") {
	$DocEntry = $_POST['DocEntry'];
	$HistorySQL = "SELECT
			T0.RemarkID, T0.DocEntry, T0.RecipientRemark, T0.RecipientDate, CONCAT(T1.uName,' ',T1.uLastName) AS 'SavedName', T0.RemarkStatus
		FROM docwho_remark T0
		LEFT JOIN users T1 ON T0.RecipientUkey = T1.ukey
		WHERE T0.DocEntry = $DocEntry
		ORDER BY RemarkID ASC";
	$Rows = ChkRowDB($HistorySQL);
	if($Rows == 0) {
		$output .= "<tr><td class='text-center' colspan='4'>ไม่พบประวัติ :(</td></tr>";
	} else {
		$HistoryQRY = MySQLSelectX($HistorySQL);
		$no = 0;
		while($HistoryRST = mysqli_fetch_array($HistoryQRY)) {
			$no++;
			if($HistoryRST['RemarkStatus'] == "A") { $rowcls = " class='table-success text-success'"; } else { $rowcls = NULL; }
			$output .= "<tr$rowcls>";
				$output .= "<td class='text-right'>".number_format($no,0)."</td>";
				$output .= "<td>".$HistoryRST['RecipientRemark']."</td>";
				$output .= "<td>".$HistoryRST['SavedName']."</td>";
				$output .= "<td class='text-center'>".date("d/m/Y", strtotime($HistoryRST['RecipientDate']))." ".date("H:i", strtotime($HistoryRST['RecipientDate']))." น.</td>";
			$output .= "</tr>";
		}
	}
	$arrCol['output'] = $output;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
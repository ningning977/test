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

function DocTypeName($DocType) {
	switch($DocType) {
		case "C"    : $TypeName = "[C] เอกสารใบเคลมเปลี่ยน"; break;
		case "L"    : $TypeName = "[L] เอกสารคืนจากการยืม/คืนใบยืมที่ผ่าน QC แล้ว"; break;
		case "D"    : $TypeName = "[D] เอกสารลดหนี้ที่ผ่าน QC แล้ว"; break;
		case "RE"   : $TypeName = "[RE] เอกสารใบไม่รับคืนเคลมเปลี่ยน"; break;
		case "SA-04": $TypeName = "[SA-04] เอกสารทำลดหนี้ส่วนลดจ่าย"; break;
		case "SA-08": $TypeName = "[SA-08] เอกสารแก้ไขบิล/เปลี่ยนที่อยู่บิล/แก้ไขบิล"; break;
		case "AC"   : $TypeName = "[AC] เอกสารคืนลอย/คืนลอยเพื่อเปิดบิล"; break;
		case "MM"   : $TypeName = "[MM] เอกสาร MEMO"; break;
		case "MP"   : $TypeName = "[MP] เอกสาร MEMO จ่ายเงิน"; break;
	}
	return $TypeName;
}

if ($_GET['p'] == "GetCardCode") {
	$CustSQL = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE (T0.CardCode != '' OR T0.CardName != '') AND T0.CardStatus = 'A' ORDER BY T0.CardCode";
	$CustQRY = MySQLSelectX($CustSQL);
	$output .= "<option value='' selected disabled>กรุณาเลือกลูกค้า</option>";
	while($CustRST = mysqli_fetch_array($CustQRY)) {
		$output .= "<option value='".$CustRST['CardCode']."'>".$CustRST['CardCode']." | ".$CustRST['CardName']."</option>";
	}
	$arrCol['output'] = $output;
}

if ($_GET['p'] == "AddDoc") {
	/* Check Duplicate */
	$DocType    = $_POST['DocType'];
	$DocNum     = $_POST['DocNum'];
	$DocDate    = $_POST['DocDate'];
	$CardCode   = $_POST['CardCode'];
	$RefDocNum  = $_POST['RefDocNum'];
	$CreateUkey = $_SESSION['ukey'];
	$ChkSQL = "SELECT T0.DocEntry FROM docacc_header T0 WHERE T0.DocNum = '$DocNum' AND T0.DocType = '$DocType' AND T0.RecipientStatus IN ('1','Y') AND T0.DocStatus = 'O'";
	$Rows   = ChkRowDB($ChkSQL);
	if($Rows > 0) {
		/* มีข้อมูลมากกว่า 1 */
		$arrCol['AddStatus'] = "ERR::DUPLICATE";
	} else {
		$CardCodeSQL = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE T0.CardCode = '$CardCode' LIMIT 1";
		$CardCodeRST = MySQLSelect($CardCodeSQL);
		$CardName = $CardCodeRST['CardName'];
		if($RefDocNum == "") {
			$RefDocNum = "NULL";
		} else {
			$RefDocNum = "'".$RefDocNum."'";
		}
		$InsertSQL = "INSERT INTO docacc_header SET DocNum = '$DocNum', DocType = '$DocType', DocDate = '$DocDate', CardCode = '$CardCode', CardName = '$CardName', RefDocNum = $RefDocNum, SenderUkey = '$CreateUkey'";
		$InsertID = MySQLInsert($InsertSQL);
		if($InsertID > 0) {
			$arrCol['AddStatus'] = "SUCCESS";
		} else {
			$arrCol['AddStatus'] = "ERR::CANNOT_INSERT";
		}
	}
}

if($_GET['p'] == "GetSA08DocNum") {
	$YDocNum = substr(date("Y")+543,-2);
	$GetDocSQL = "SELECT SUBSTRING(T0.DocNum,6,4)+1 AS 'DocNum' FROM docacc_header T0 WHERE T0.DocNum LIKE '%-$YDocNum' AND T0.DocType = 'SA-08' ORDER BY T0.DocNum DESC LIMIT 1";
	$Rows = ChkRowDB($GetDocSQL);
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
	$arrCol['DocNum'] = "SA08-".$NewSuffix."-".$YDocNum;
}

if($_GET['p'] == "GetACDocNum") {
	$YDocNum = substr(date("Y")+543,-2);
	$GetDocSQL = "SELECT SUBSTRING(T0.DocNum,4,4)+1 AS 'DocNum' FROM docacc_header T0 WHERE T0.DocNum LIKE '%-$YDocNum' AND T0.DocType = 'AC' ORDER BY T0.DocNum DESC LIMIT 1";
	$Rows = ChkRowDB($GetDocSQL);
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
	$arrCol['DocNum'] = "AC".$NewSuffix."-".$YDocNum;
}

if($_GET['p'] == "GetLogList") {
	$year     = $_POST['y'];
	$month    = $_POST['m'];
	$DeptCode = $_POST['t'];
	$DocType  = $_POST['d'];
	if($DeptCode == "ALL") {
		$ListWhr = NULL;
	} else {
		$ListWhr = " AND T3.DeptCode = '$DeptCode'";
	}
	if($DocType == "ALL") {
		$ListWhr.= NULL;
	} else {
		$ListWhr.= " AND T0.DocType = '$DocType'";
	}

	$GetListSQL = 
		"SELECT
			T0.DocEntry, T0.DocNum, T0.CANCELED, T0.DocType, T0.DocDate, T0.CardCode, T0.CardName, T3.DeptCode, T0.DocTitle,
			T1.uName AS 'SendName', T1.uNickName AS 'SendNickName', T0.SenderDate, T0.RecipientStatus,
			T2.uName AS 'RecipientName', T0.RecipientDate, T4.RecipientRemark
		FROM docacc_header T0
		LEFT JOIN users T1 ON T0.SenderUkey = T1.ukey
		LEFT JOIN users T2 ON T0.RecipientUkey = T2.ukey
		LEFT JOIN positions T3 ON T1.LvCode = T3.LvCode
		LEFT JOIN docacc_remark T4 ON T0.DocEntry = T4.DocEntry AND T4.RemarkStatus = 'A'
		WHERE (YEAR(T0.SenderDate) = $year AND MONTH(T0.SenderDate) = $month $ListWhr) OR (T0.RecipientStatus = '1' AND T0.CANCELED = 'N' $ListWhr) 
		ORDER BY CASE WHEN T0.CANCELED = 'N' THEN 1 ELSE 2 END, T0.DocType, T0.DocNum, T0.DocEntry";

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
				$Recipient = NULL;
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
				$DeptCond = array("DP001","DP002","DP009");
				$DeptChck = array_search($DeptCode,$DeptCond);
				if(!$DeptChck) {
					/* ไม่ใช่ฝ่ายบริหาร / IT / และ บัญชี */
					$chckY = " disabled";
					$chckN = " disabled";
					$remkD = " disabled";
					
					switch($GetListRST['RecipientStatus']) {
						case "Y": $chckY = " checked"; $chckN = ""; $cnclD = ""; break;
						case "N": $chckN = " checked"; $chckY = ""; $cnclD = ""; $trcls = " class='table-danger text-danger'"; break;
					}
				} else {
					/* ใช่ */
					switch($GetListRST['RecipientStatus']) {
						case "Y": $chckY = " checked"; $chckN = ""; $cnclD = " disabled"; break;
						case "N": $chckN = " checked"; $chckY = ""; $cnclD = " disabled"; $trcls = " class='table-danger text-danger'"; break;
					}
				}
			}
			if($GetListRST['DocType'] == "MM" || $GetListRST['DocType'] == "MP") {
				$ShowTitle = $GetListRST['DocTitle'];
				$DocNum    = "<a href='javascript:void(0);' onclick='PreviewMM(\"".$GetListRST['DocNum']."\",2);'>".$GetListRST['DocNum']."</a>";
			} else {
				$ShowTitle = $GetListRST['CardCode']." | ".$GetListRST['CardName'];
				$DocNum    = $GetListRST['DocNum'];
			}
			$output .= "<tr$trcls>";
				$output .= "<td class='text-center'>$DocNum</td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($GetListRST['DocDate']))."</td>";
				$output .= "<td><strong>$ShowTitle</strong><br/><small>เอกสาร: ".DocTypeName($GetListRST['DocType'])."</small></td>";
				$output .= "<td>".$GetListRST['SendName'].$NickName."</td>";
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
	$ChkSQL = "SELECT T0.DocEntry FROM docacc_header T0 WHERE T0.DocEntry = $DocEntry LIMIT 1";
	$Rows   = ChkRowDB($ChkSQL);
	if($Rows == 0) {
		$arrCol['AddStatus'] = "ERR::NO_RESULT";
	} else {
		$UpdateSQL = "UPDATE docacc_header SET DocStatus = 'C', RecipientDate = NOW(), RecipientStatus = '$Status', RecipientUkey = '$Receiver' WHERE DocEntry = $DocEntry";
		MySQLUpdate($UpdateSQL);
		$arrCol['AddStatus'] = "SUCCESS";
	}
}

if($_GET['p'] == "SaveRemark") {
	$DocEntry = $_POST['DocEntry'];
	$DocText  = $_POST['Content'];

	$ChkSQL = "SELECT T0.RemarkID FROM docacc_remark T0 WHERE T0.DocEntry = $DocEntry  ORDER BY T0.RemarkID DESC LIMIT 1";
	$Rows   = ChkRowDB($ChkSQL);
	if($Rows == 0) {
		if($DocText != "" || $DocText != NULL) {
			$InsertSQL = "INSERT INTO docacc_remark SET DocEntry = $DocEntry, RecipientRemark = '$DocText', RecipientUkey = '".$_SESSION['ukey']."'";
			MySQLInsert($InsertSQL);
		}
	} else {
		$LastIDSQL = "SELECT T0.RemarkID, T0.RecipientRemark FROM docacc_remark T0 WHERE T0.DocEntry = '$DocEntry' AND T0.RemarkStatus = 'A' ORDER BY T0.RemarkID DESC LIMIT 1";
		$LastIDRST = MySQLSelect($LastIDSQL);
		if($LastIDRST['RecipientRemark'] != $DocText) {
			if($DocText != '') {
				$Comment = "'$DocText'";
			} else {
				$Comment = "NULL";
			}
			$UpdateSQL = "UPDATE docacc_remark SET RemarkStatus = 'I' WHERE RemarkID = ".$LastIDRST['RemarkID'];
			MySQLUpdate($UpdateSQL);

			$InsertSQL = "INSERT INTO docacc_remark SET DocEntry = '$DocEntry', RecipientRemark = $Comment, RecipientUkey = '".$_SESSION['ukey']."'";
			MySQLInsert($InsertSQL);
		}
	}
}

if($_GET['p'] == "HistoryDoc") {
	$DocEntry = $_POST['DocEntry'];
	$HistorySQL = "SELECT
			T0.RemarkID, T0.DocEntry, T0.RecipientRemark, T0.RecipientDate, CONCAT(T1.uName,' ',T1.uLastName) AS 'SavedName', T0.RemarkStatus
		FROM docacc_remark T0
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

if($_GET['p'] == "CancelDoc") {
	$DocEntry = $_POST['DocEntry'];
	$CancelSQL = "UPDATE docacc_header SET CANCELED = 'Y', DocStatus = 'C', CancelDate = NOW(), CancelUkey = '".$_SESSION['ukey']."' WHERE DocEntry = $DocEntry";
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
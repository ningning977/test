<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";

function DocTypeName($DocType) {
	switch($DocType) {
		case "A": $text = "ลดหนี้/ลดจ่ายทั้งบิล"; break;
		case "B": $text = "ลดหนี้/ลดจ่ายเฉพาะรายการ"; break;
		case "C": $text = "ลดหนี้/ลดจ่ายค่าขนส่ง"; break;
	}
	return $text;
}

function DocRemarkName($DocRemark) {
	switch($DocRemark) {
		case 1: $text = "เซลส์เสนอราคาผิด"; break;
		case 2: $text = "ลูกค้าขอราคาเดิม"; break;
		case 3: $text = "คู่แข่งขายถูกกว่า"; break;
		case 4: $text = "อื่น ๆ"; break;
	}
	return $text;
}

if($_SESSION['UserName']== NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

if($_GET['p'] == "DocList") {
	$DeptCode = $_SESSION['DeptCode'];
	if($DeptCode == "DP009") {
		$AppUkey = "(T1.AppUkeyReq = 'DP009') AND (T0.CANCELED = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'Y')";
	} else if($DeptCode == "DP003") {
		$AppUkey = "((T1.AppUkeyReq = 'DP003' OR T1.AppUkeyReq = '".$_SESSION['ukey']."') AND T1.AppState NOT IN ('Y','N') ) AND (T0.CANCELED = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P')";
 	} else {
		$AppUkey = "(T1.AppUkeyReq = '".$_SESSION['ukey']."' AND T1.AppState NOT IN ('Y','N') ) AND (T0.CANCELED = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P')";
	}
	$DocSQL =
		"SELECT A0.* FROM
			(SELECT
				CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName', T4.DeptName,
				T0.*, T1.VisOrder AS 'NowState', T1.AppState AS 'NowApprove',
				CASE WHEN T1.VisOrder > 0 THEN (SELECT P0.VisOrder FROM SA04_Approve P0 WHERE P0.DocEntry = T0.DocEntry AND P0.VisOrder < T1.VisOrder ORDER BY P0.ApproveID DESC LIMIT 1) ELSE NULL END AS 'PrevState',
				CASE WHEN T1.VisOrder > 0 THEN (SELECT P0.AppState FROM SA04_Approve P0 WHERE P0.DocEntry = T0.DocEntry AND P0.VisOrder < T1.VisOrder ORDER BY P0.ApproveID DESC LIMIT 1) ELSE NULL END AS 'PrevApprove'
			FROM SA04_Header T0
			LEFT JOIN SA04_Approve T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN users T2 ON T0.CreateUkey = T2.uKey
			LEFT JOIN positions T3 ON T2.LvCode = T3.LvCode
			LEFT JOIN departments T4 ON T3.DeptCode = T4.DeptCode
			WHERE $AppUkey
		) A0
		WHERE CASE WHEN (A0.PrevState IS NOT NULL) THEN A0.PrevApprove = 'Y' ELSE A0.PrevApprove IS NULL END
		ORDER BY A0.DocEntry DESC";
	 //echo $DocSQL;
	$Rows = ChkRowDB($DocSQL);

	$ChkRow = "N";
	if(isset($_GET['tab'])) { 
		if($_GET['tab'] == "ChkRow") {
			$arrCol['Rows'] = $Rows;
			$ChkRow = "Y";
		}
	}

	if($Rows == 0 && $ChkRow == "Y") {
		$output .= "<tr><td colspan='9' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$DocQRY = MySQLSelectX($DocSQL);
		$no = 0;
		/*
			int_status หมายถึงสถานะภายในสำหรับการประมวลผลคำสั่งขาย
			+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
			| int_status | CANCELED | DraftStatus | DocStatus | AppStatus || CAN EDIT? | CAN PRINT? | CAN IMPORT? |
			+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
			| 0          | Y        | ANY         | ANY       | ANY       || NO        | NO         | NO          | -> เอกสารยกเลิก
			| 1          | N        | Y           | O         | B         || YES       | YES        | NO          | -> เอกสารแบบร่าง
			| 2          | N        | N           | P         | P         || NO        | YES        | NO          | -> เอกสารรออนุมัติ
			| 3          | N        | N           | P         | Y         || NO        | YES        | YES         | -> เอกสารผ่านการอนุมัติ
			| 4          | N        | N           | P         | N         || NO        | NO         | NO          | -> เอกสารไม่อนุมัติ
			| 5          | N        | N           | C         | Y         || YES       | YES        | NO          | -> เอกสารเสร็จสมบูรณ์ (Import เข้า SAP เรียบร้อย)
			+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
		*/
		while($DocRST = mysqli_fetch_array($DocQRY)) {
			if($DocRST['CANCELED'] == "Y") {
				$int_status = 0;
			}  elseif($DocRST['DocStatus'] == "P") {
				switch($DocRST['AppStatus']) {
					case "Y": $int_status = 3; break;
					case "N": $int_status = 4; break;
					default:  $int_status = 2; break;
				}
			} elseif($DocRST['DocStatus'] == "C") {
				$int_status = 5;
			} else {
				$int_status = 3;
			}

			switch($int_status) {
				case 0:
					$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
					$dis_prnt = " disabled";
					if($_SESSION['ukey'] != $MemoRST['CreateUkey']) {
						$dis_edit   = " disabled";
					}
					break;
				case 1:
					$txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
					break;
				case 1.5:
					$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
					break;
				case 2:
					$txt_status = "<span class='badge bg-warning w-100'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>";
					break;
				case 3:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
					break;
				case 4:
					$txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
					break;
				case 5:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
					break;
			}

			if($int_status != 0) {
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

			$no++;
			
			$output .= "<tr$row_cls>";
				$output .= "<td class='text-right'>".number_format($no,0)."</td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($DocRST['DocDate']))."</td>";
				if (!isset($_GET['m'])){
					$pageLine = 'wai';
				}else{
					$pageLine = 'main';
				}
				if ($pageLine == 'main'){
					$output .= "<td class='text-center'><a href='?p=app_sa04'>".$DocRST['DocNum']."</a></td>";
					
				}else{
					$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='PreviewSA04(".$DocRST['DocEntry'].",$int_status)'>".$DocRST['DocNum']."</a></td>";//?p=app_sa04
				}
				$output .= "<td><strong>".$DocRST['BillCardCode']." | ".$DocRST['BillCardName']."</strong><br/><small>ประเภทเอกสาร: ".DocTypeName($DocRST['DocType'])."</small></td>";
				$output .= "<td>".$DocRST['BillSlpName']."</td>";
				$output .= "<td>".$DocRST['CreateName']."</td>";
				$output .= "<td class='text-center'>".$DocRST['BillDocNum']."</td>";
				$output .= "<td class='text-right text-danger' style='font-weight: bold;'>".number_format($DocRST['BillCNTotal'],2)."</td>";
				$output .= "<td class='text-center'>".$txt_status."</td>";
			$output .= "</tr>";
		}
	}
	$arrCol['DocList'] = $output;
}


if($_GET['p'] == "AppDoc") {
	$ApproveID = $_POST['aid'];
	$DocEntry  = $_POST['d'];
	$AppState  = $_POST['a'];
	$Remark    = $_POST['r'];

	$UpdateSQL = "UPDATE SA04_Approve SET AppState = '$AppState', AppRemark = '$Remark', AppUkeyAct = '".$_SESSION['ukey']."', AppDate = NOW() WHERE ApproveID = $ApproveID";
	// echo $UpdateSQL."<br/>";
	MySQLUpdate($UpdateSQL);

	if($AppState == "N") {
		$UpdateSQL = "UPDATE SA04_Header SET AppStatus = 'N', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
		MySQLUpdate($UpdateSQL);
	} else {
		if($_SESSION['DeptCode'] != "DP009" || $_SESSION['DeptCode'] == "DP003") {
			/* Co-Sale And Manager */
			if(isset($_POST['FineN']) || isset($_POST['FineS']) || isset($_POST['FineC'])) {
				$NoFine    = isset($_POST['FineN']);
				$SAFine    = isset($_POST['FineS']);
				$CoFine    = isset($_POST['FineC']);
				$FineSA    = NULL;
				$FineCO    = NULL;
				if($NoFine == "Y") {
					$FineSA = "N";
					$FineCO = "N";
				} else {
					// if($SAFine == 1) { $FineSA = "Y"; } else { $FineSA = "N"; }
					// if($CoFine == 1) { $FineCO = "Y"; } else { $FineCO = "N"; }
					if($SAFine == "Y") { $FineSA = "Y"; } else { $FineSA = "N"; }
					if($CoFine == "Y") { $FineCO = "Y"; } else { $FineCO = "N"; }
				}

				$UpdateSQL = "UPDATE SA04_Header SET FineSA = '$FineSA', FineCO = '$FineCO' WHERE DocEntry = $DocEntry";
				// echo $UpdateSQL."<br/>";
				MySQLUpdate($UpdateSQL);
			}
		}

		if($_SESSION['DeptCode'] == "DP003") {
			$UpdateSQL = "UPDATE SA04_Header SET AppStatus = 'Y', Printed = 'Y', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
			MySQLUpdate($UpdateSQL);
		}

		if($_SESSION['DeptCode'] == "DP009") {
			/* Accounting Manager */
			/* FindDocNum */
			$GetNumSQL = "SELECT T0.DocEntry, T0.DocNum FROM SA04_Header T0 WHERE T0.DocEntry = $DocEntry LIMIT 1";
			$GetNumRST    = MySQLSelect($GetNumSQL);
			$GetDocNum    = $GetNumRST['DocNum'];
			$GetDocEntry  = $GetNumRST['DocEntry'];

			/* Closed SA04 Doc */
			$UpdateSQL = "UPDATE SA04_Header SET DocStatus = 'C' WHERE DocEntry = $GetDocEntry";
			// echo $UpdateSQL."<br/>";
			MySQLUpdate($UpdateSQL);

			/* Find Log Send to Acc */
			$GetLogSQL = "SELECT T0.DocEntry, T0.DocNum FROM docacc_header T0 WHERE T0.DocNum = '$GetDocNum' AND T0.DocType = 'SA-04' AND T0.DocStatus = 'O' AND T0.RecipientStatus = '1' ORDER BY T0.DocEntry DESC LIMIT 1 ";
			// echo $GetLogSQL."<br/>";
			$GetLogRST = MySQLSelect($GetLogSQL);
			$LogEntry  = $GetLogRST['DocEntry'];
			
			/* UPDATE RECEIVE STATUS */
			$UpdateSQL = "UPDATE docacc_header SET RecipientStatus = '$AppState', RecipientDate = NOW(), RecipientUkey = '".$_SESSION['ukey']."' WHERE DocEntry = $LogEntry";
			// echo $UpdateSQL."<br/>";
			MySQLUpdate($UpdateSQL);

			/* UPDATE RECEIVE REMARK STATUS */
			$UpdateSQL = "UPDATE docacc_remark SET RemarkStatus = 'I' WHERE DocEntry = $LogEntry";
			// echo $UpdateSQL."<br/>";
			MySQLUpdate($UpdateSQL);
			$UpdateSQL = "INSERT INTO docacc_remark SET DocEntry = $LogEntry, RecipientRemark = '$Remark', RecipientUkey = '".$_SESSION['ukey']."', RecipientDate = NOW(), RemarkStatus = 'A'";
			// echo $UpdateSQL."<br/>";
			MySQLUpdate($UpdateSQL);
		}

		$Chck = "SELECT T0.ApproveID, T0.AppUkeyReq FROM SA04_Approve T0 WHERE T0.DocEntry = $DocEntry AND T0.ApproveID > $ApproveID LIMIT 1";
		$Rows = ChkRowDB($Chck);

		if($Rows == 0) {
			$UpdateSQL = "UPDATE SA04_Header SET AppStatus = 'Y', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
			MySQLUpdate($UpdateSQL);
		}
	}
	

	// if($_SESSION['DeptCode'] != "DP009") {
	// 	/* CHECK APPSTATE = N NOT APPROVE IN HEADER */
	// 	if($AppState == "N") {
	// 		$UpdateSQL = "UPDATE SA04_Header SET AppStatus = 'N', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
	// 		// echo $UpdateSQL."<br/>";
	// 		MySQLUpdate($UpdateSQL);
	// 	} else {
	// 		/* CHECK NEXT STATE IF ROW = 0 APPROVE IN HEADER */
	// 		$Chck = "SELECT T0.ApproveID, T0.AppUkeyReq FROM SA04_Approve T0 WHERE DocEntry = $DocEntry AND ApproveID > $ApproveID AND AppUkeyReq != 'DP009' LIMIT 1";
	// 		$Rows = ChkRowDB($Chck);
	// 		// echo $Rows."<br/>";
	// 		if($Rows == 0) {
	// 			$UpdateSQL = "UPDATE SA04_Header SET AppStatus = 'Y', Printed = 'Y', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
	// 			// echo $UpdateSQL."<br/>";
	// 			MySQLUpdate($UpdateSQL);
	// 		}
	// 	}

	// 	if(isset($_POST['FineN']) || isset($_POST['FineS']) || isset($_POST['FineC'])) {
	// 		$NoFine    = isset($_POST['FineN']);
	// 		$SAFine    = isset($_POST['FineS']);
	// 		$CoFine    = isset($_POST['FineC']);
	// 		$FineSA    = NULL;
	// 		$FineCO    = NULL;
	// 		if($NoFine == "Y") {
	// 			$FineSA = "N";
	// 			$FineCO = "N";
	// 		} else {
	// 			if($SAFine == 1) { $FineSA = "Y"; } else { $FineSA = "N"; }
	// 			if($CoFine == 1) { $FineCO = "Y"; } else { $FineCO = "N"; }
	// 		}

	// 		$UpdateSQL = "UPDATE SA04_Header SET FineSA = '$FineSA', FineCO = '$FineCO' WHERE DocEntry = $DocEntry";
	// 		// echo $UpdateSQL."<br/>";
	// 		MySQLUpdate($UpdateSQL);
	// 	}
	// } else {
	// 	/* FindDocNum */
	// 	$GetNumSQL = "SELECT T0.DocEntry, T0.DocNum FROM SA04_Header T0 WHERE T0.DocEntry = $DocEntry LIMIT 1";
	// 	$GetNumRST    = MySQLSelect($GetNumSQL);
	// 	$GetDocNum    = $GetNumRST['DocNum'];
	// 	$GetDocEntry  = $GetNumRST['DocEntry'];

	// 	/* Closed SA04 Doc */
	// 	$UpdateSQL = "UPDATE SA04_Header SET DocStatus = 'C' WHERE DocEntry = $GetDocEntry";
	// 	// echo $UpdateSQL."<br/>";
	// 	MySQLUpdate($UpdateSQL);

	// 	/* Find Log Send to Acc */
	// 	$GetLogSQL = "SELECT T0.DocEntry, T0.DocNum FROM docacc_header T0 WHERE T0.DocNum = '$GetDocNum' AND T0.DocType = 'SA-04' AND T0.DocStatus = 'O' AND T0.RecipientStatus = '1' ORDER BY T0.DocEntry DESC LIMIT 1 ";
	// 	// echo $GetLogSQL."<br/>";
	// 	$GetLogRST = MySQLSelect($GetLogSQL);
	// 	$LogEntry  = $GetLogRST['DocEntry'];
		
	// 	/* UPDATE RECEIVE STATUS */
	// 	$UpdateSQL = "UPDATE docacc_header SET RecipientStatus = '$AppState', RecipientDate = NOW(), RecipientUkey = '".$_SESSION['ukey']."' WHERE DocEntry = $LogEntry";
	// 	// echo $UpdateSQL."<br/>";
	// 	MySQLUpdate($UpdateSQL);

	// 	/* UPDATE RECEIVE REMARK STATUS */
	// 	$UpdateSQL = "UPDATE docacc_remark SET RemarkStatus = 'I' WHERE DocEntry = $LogEntry";
	// 	// echo $UpdateSQL."<br/>";
	// 	MySQLUpdate($UpdateSQL);
	// 	$UpdateSQL = "INSERT INTO docacc_remark SET DocEntry = $LogEntry, RecipientRemark = '$Remark', RecipientUkey = '".$_SESSION['ukey']."', RecipientDate = NOW(), RemarkStatus = 'A'";
	// 	// echo $UpdateSQL."<br/>";
	// 	MySQLUpdate($UpdateSQL);
	// }
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
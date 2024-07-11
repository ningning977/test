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

if($_GET['p'] == "MemoList") {
	$Limit = NULL;
	if(isset($_GET['tab'])) {
		if($_GET['tab'] == "Y") {
			$Limit = " LIMIT 5";
		}
	}
	$MemoSQL = 
		"SELECT A0.* FROM
			(SELECT
				CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName', T4.DeptName,
				T0.*, T1.VisOrder AS 'NowState', T1.AppState AS 'NowApprove',
				CASE WHEN T1.VisOrder > 0 THEN T1.VisOrder - 1 ELSE NULL END AS 'PrevState',
				CASE WHEN T1.VisOrder > 0 THEN (SELECT P0.AppState FROM memo_approve P0 WHERE P0.DocEntry = T0.DocEntry AND P0.VisOrder = T1.VisOrder - 1 LIMIT 1) ELSE NULL END AS 'PrevApprove'
			FROM memo_header T0
			LEFT JOIN memo_approve T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN users T2 ON T0.CreateUkey = T2.uKey
			LEFT JOIN positions T3 ON T2.LvCode = T3.LvCode
			LEFT JOIN departments T4 ON T3.DeptCode = T4.DeptCode
			WHERE ((T1.AppUkeyReq = '".$_SESSION['ukey']."') OR (T0.CreateUkey = '".$_SESSION['ukey']."' AND T1.AppUkeyReq = '42b4e5ab67feb54da8216a5439fd6dcb')) AND (T0.CANCELED = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P' AND T1.AppState = '1')
		) A0
		WHERE CASE WHEN (A0.PrevState IS NOT NULL) THEN A0.PrevApprove = 'Y' ELSE A0.PrevApprove IS NULL END
		ORDER BY A0.DocEntry DESC$Limit";
	// echo $MemoSQL;
	$Rows = ChkRowDB($MemoSQL);

	$ChkRow = "N";
	if(isset($_GET['tab'])) { 
		if($_GET['tab'] == "ChkRow") {
			$arrCol['Rows'] = $Rows;
			$ChkRow = "Y";
		}
	}

	if($Rows == 0 && $ChkRow == "Y") {
		$output = "<tr><td colspan='6' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$MemoQRY = MySQLSelectX($MemoSQL);
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
		while($MemoRST = mysqli_fetch_array($MemoQRY)) {
			
			if($MemoRST['CANCELED'] == "Y") {
				$int_status = 0;
			} elseif($MemoRST['DraftStatus'] == "Y") {
				$int_status = 1;
			} elseif($MemoRST['DocStatus'] == "P") {
				switch($MemoRST['AppStatus']) {
					case "Y": $int_status = 3; break;
					case "N": $int_status = 4; break;
					default:  $int_status = 2; break;
				}
			} elseif($MemoRST['DocStatus'] == "C") {
				$int_status = 5;
			} else {
				$int_status = 3;
			}
			if($MemoRST['DocSecret'] == "Y") {
				$DocScret = "<span class='badge bg-danger'>ลับ</span> ";
			} else {
				$DocScret = NULL;
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
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($MemoRST['DocDate']))."</td>";
				$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='PreviewMM(".$MemoRST['DocEntry'].",$int_status)'>".$MemoRST['DocNum']."</a></td>";
				$output .= "<td><strong>".$DocScret.$MemoRST['DocTitle']."</strong><br/><small>".iconv_substr(strip_tags($MemoRST['DocDetail']),0,128,'UTF-8')."...</small></td>";
				$output .= "<td>".$MemoRST['DeptName']."<br/><small>ผู้จัดทำ: ".$MemoRST['CreateName']."</small></td>";
				$output .= "<td class='text-center'>".$txt_status."</td>";
			$output .= "</tr>";
		}
	}
	$arrCol['MemoList'] = $output;
}

if($_GET['p'] == "AppMemo") {
	$ApproveID = $_POST['aid'];
	$DocEntry  = $_POST['d'];
	$AppState  = $_POST['a'];
	$Remark    = $_POST['r'];

	$UpdateSQL = "UPDATE memo_approve SET AppState = '$AppState', AppRemark = '$Remark', AppUkeyAct = '".$_SESSION['ukey']."', AppDate = NOW() WHERE ApproveID = $ApproveID";
	// echo $UpdateSQL;
	MySQLUpdate($UpdateSQL);

	/* CHECK APPSTATE = N NOT APPROVE IN HEADER */
	if($AppState == "N") {
		$UpdateSQL = "UPDATE memo_header SET AppStatus = 'N', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
		// echo $UpdateSQL;
		MySQLUpdate($UpdateSQL);
	} else {
		/* CHECK NEXT STATE IF ROW = 0 APPROVE IN HEADER */
		$Chck = "SELECT T0.ApproveID, T0.AppUkeyReq FROM memo_approve T0 WHERE DocEntry = $DocEntry AND ApproveID > $ApproveID LIMIT 1";
		$Rows = ChkRowDB($Chck);
		if($Rows == 0) {
			$UpdateSQL = "UPDATE memo_header SET AppStatus = 'Y', Printed = 'Y', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
			// echo $UpdateSQL;
			MySQLUpdate($UpdateSQL);
		} else {
			$ChckRST = MySQLSelect($Chck);
			if($ChckRST['AppUkeyReq'] == "42b4e5ab67feb54da8216a5439fd6dcb") {
				$UpdateSQL = "UPDATE memo_header SET Printed = 'Y', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
				MySQLUpdate($UpdateSQL);
			}
		}
	}

	
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
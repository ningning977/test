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

if($_GET['p'] == "GetEmpName") {

	$EmpSQL = "SELECT
				T0.uKey, CONCAT(T0.uName,' ',T0.uLastName) AS 'EmpName', T0.uNickName, T0.LvCode, T1.UClass, T1.DeptCode, T2.DeptName
			FROM users T0
			LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
			LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
			WHERE T0.UserStatus = 'A' AND T0.uNickName != 'Online'
			ORDER BY T1.DeptCode ASC, T0.LvCode ASC";
	$EmpQRY = MySQLSelectX($EmpSQL);
	$TmpDeptCode = "";
	$TmpDeptName = "";
	$output .= "<option value='NULL'>กรุณาเลือก</option>";
	while($EmpRST = mysqli_fetch_array($EmpQRY)) {

		if($EmpRST['uNickName'] == "") {
			$nickname = NULL;
		} else {
			$nickname = " (".$EmpRST['uNickName'].")";
		}
		// if($TmpDeptCode == "" || $TmpDeptCode != $EmpRST['DeptCode']) {
		// 	if($TmpDeptCode != "") {
		// 		$output .= "</optgroup>";
		// 	}
		// 	$output .= "<optgroup label='".$EmpRST['DeptName']."'>";
		// 		$output .= "<option value='".$EmpRST['uKey']."'>คุณ".$EmpRST['EmpName'].$nickname."</option>";
		// 	$TmpDeptCode = $EmpRST['DeptCode'];
		// } else {
		// 		$output .= "<option value='".$EmpRST['uKey']."'>คุณ".$EmpRST['EmpName'].$nickname."</option>";
		// }
		$output .= "<option value='".$EmpRST['uKey']."'>คุณ".$EmpRST['EmpName'].$nickname."</option>";
	}

	$arrCol['view_user'] = $output;
}

if($_GET['p'] == "SaveMemo") {
	$DocEntry = $_POST['DocEntry'];
	/* GETLastDocNum */
	$YDocNum = substr(date("Y")+543,-2);
	$DocType = $_POST['DocType'];
	$GetDocSQL = "SELECT SUBSTRING(T0.DocNum,3,4)+1 AS 'DocNum' FROM memo_header T0 WHERE T0.DocNum LIKE '%-$YDocNum' AND T0.DocType = '$DocType' ORDER BY T0.DocEntry DESC LIMIT 1";
	// echo $GetDocSQL;
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

	if($_POST['SaveType'] == 0) {
		$DraftStatus = "Y";
		$DocStatus   = "O";
		$AppStatus   = "B";
	} else {
		$DraftStatus = "N";
		$DocStatus   = "P";
		$AppStatus   = "P";
	}

	$DocMention = "";
	$MntnCount = count($_POST['DocMention']);
	if($MntnCount > 0) {
		for($i = 0; $i < $MntnCount; $i++) {
			$DocMention .= $_POST['DocMention'][$i].",";
		}
		$DocMention = "'".substr($DocMention,0,-1)."'";
	} else {
		$DocMention = "NULL";
	}

	if($_POST['DocCopyTo'] == "") {
		$DocCopyTo = "NULL";
	} else {
		$DocCopyTo = "'".$_POST['DocCopyTo']."'";
	}

	$DocNum      = $DocType.$NewSuffix."-".$YDocNum;
	$DocType     = $_POST['DocType'];
	$DraftStatus = $DraftStatus;
	$DocStatus   = $DocStatus;
	$AppStatus   = $AppStatus;
	$DocDate     = $_POST['DocDate'];
	$DocSecret   = $_POST['DocSecret'];
	$DocTitle    = $_POST['DocTitle'];
	$DocMention  = $DocMention;
	$DocCopyTo   = $DocCopyTo;
	$DocDetail   = addslashes(str_replace("<table>","<table class=\"table table-bordered\">",$_POST['TmpDocDetail']));
	$DocSignOff  = $_POST['DocSignOff'];
	$CreateUkey  = $_SESSION['ukey'];

	if($DocEntry == 0) {
		/* INSERT HEADER */
		$InsertSQL =
			"INSERT INTO memo_header SET
				DocNum = '$DocNum',
				DocType = '$DocType',
				DraftStatus = '$DraftStatus',
				DocStatus = '$DocStatus',
				AppStatus = '$AppStatus',
				DocDate = '$DocDate',
				DocSecret = '$DocSecret',
				DocTitle = '$DocTitle',
				DocDetail = '$DocDetail',
				DocMention = $DocMention,
				DocCopyTo = $DocCopyTo,
				DocSignOff = '$DocSignOff',
				CreateUkey = '$CreateUkey'
			";
		// echo $InsertSQL;
		$DocEntry = MySQLInsert($InsertSQL);

		/* INSERT APPOVE STAGE */
		$Approve = array();
		$DocApprove_1 = $_POST['DocApprove_1'];
		$DocApprove_2 = $_POST['DocApprove_2'];
		$DocApprove_3 = $_POST['DocApprove_3'];
		$DocApprove_4 = $_POST['DocApprove_4'];
		if($DocApprove_1 != "NULL") { array_push($Approve,$DocApprove_1); }
		if($DocApprove_2 != "NULL") { array_push($Approve,$DocApprove_2); }
		if($DocApprove_3 != "NULL") { array_push($Approve,$DocApprove_3); }
		if($DocApprove_4 != "NULL") { array_push($Approve,$DocApprove_4); }
		$countApp = count($Approve);

		if($countApp > 0) {
			for($i = 0; $i < $countApp; $i++) {
				$AppSQL = "INSERT INTO memo_approve SET DocEntry = '$DocEntry', VisOrder = '$i', AppUkeyReq = '".$Approve[$i]."', CreateUkey = '$CreateUkey'";
				// echo $AppSQL."</br>";
				MySQLInsert($AppSQL);
			}
		} else {
			/* Check LV CODE */
			$LvCode = $_SESSION['LvCode'];
			$LvCondition = array("LV001","LV002","LV003","LV004","LV005","LV006","LV010","LV011","LV022","LV027","LV038","LV045","LV051","LV057","LV062","LV072","LV085","LV098");
			/* ถ้าไม่ใช่ ผจก. ให้ไปหา ผจก. แต่ละแผนกมาค้นหาแล้ว INSERT */
			$LvChk = array_search($LvCode, $LvCondition);
			if(!$LvChk) {
				/* ไม่ใช่ ผจก. */
				/* หา LvCode ผจก. */
				switch($_SESSION['DeptCode']) {
					case "DP001": $AppLv = "LV001"; break;
					case "DP002":
					case "DP009": $AppLv = "LV057"; break;
					case "DP003": $AppLv = "LV011"; break;
					case "DP004": $AppLv = "LV004"; break;
					case "DP005": $AppLv = "LV027"; break;
					case "DP006": $AppLv = "LV038"; break;
					case "DP007": $AppLv = "LV045"; break;
					case "DP008": $AppLv = "LV051"; break;
					case "DP010": $AppLv = "LV062"; break;
					case "DP011": $AppLv = "LV072"; break;
					case "DP012": $AppLv = "LV085"; break;
					case "DP013": $AppLv = "LV093"; break;
				}
				$MgrSQL  = "SELECT T0.uKey FROM users T0 WHERE T0.LvCode = '$AppLv' AND T0.UserStatus = 'A' LIMIT 1";
				$MgrRST  = MySQLSelect($MgrSQL);
				$Approve = $MgrRST['uKey'];
				$AppSQL  = "INSERT INTO memo_approve SET DocEntry = '$DocEntry', VisOrder = '0', AppUkeyReq = '$Approve', CreateUkey = '$CreateUkey'";
				// echo $AppSQL;
				MySQLInsert($AppSQL);
			} else {
				/* ใช่ ผจก. */
				if($_POST['SaveType'] == 1) {
					$UpdateSQL = "UPDATE memo_header SET DraftStatus = 'N', DocStatus = 'C', AppStatus = 'Y', UpdateUkey = '$CreateUkey' WHERE DocEntry = '$DocEntry'";
					// echo $UpdateSQL;
					MySQLUpdate($UpdateSQL);
				}
			}
		}

		/* INSERT ATTACHMENT */
		if(isset($_FILES['DocAttach']['name'])) {
			$Totals = count($_FILES['DocAttach']['name'])-1;
			// echo $Totals;
			for($i = 0; $i <= $Totals; $i++) {
				$FileProcess = explode(".",basename($_FILES['DocAttach']['name'][$i]));
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

				$tmpFilePath = $_FILES['DocAttach']['tmp_name'][$i];
				if($tmpFilePath != "") {
					$NewFilePath = "../../../../FileAttach/MEMO/".$DocNum."-".$i.".".$FileExt;
					move_uploaded_file($tmpFilePath,$NewFilePath);
					// $DocEntry = 2;

					$AttachSQL = "INSERT INTO memo_attach SET
						DocEntry = $DocEntry,
						VisOrder = $i,
						FileOriName = '$FileOriName',
						FileDirName = '".$DocNum."-".$i."',
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
		$UpdateSQL = 
			"UPDATE memo_header SET
				DraftStatus = '$DraftStatus',
				DocStatus = '$DocStatus',
				AppStatus = '$AppStatus',
				DocDate = '$DocDate',
				DocSecret = '$DocSecret',
				DocTitle = '$DocTitle',
				DocDetail = '$DocDetail',
				DocMention = $DocMention,
				DocCopyTo = $DocCopyTo,
				DocSignOff = '$DocSignOff',
				UpdateUkey = '$CreateUkey'
			WHERE DocEntry = $DocEntry";
		MySQLUpdate($UpdateSQL);

		/* UPDATE APPROVE */
		$DeleteSQL = "DELETE FROM memo_approve WHERE DocEntry = $DocEntry";
		MySQLDelete($DeleteSQL);

		/* INSERT APPOVE STAGE */
		$Approve = array();
		$DocApprove_1 = $_POST['DocApprove_1'];
		$DocApprove_2 = $_POST['DocApprove_2'];
		$DocApprove_3 = $_POST['DocApprove_3'];
		$DocApprove_4 = $_POST['DocApprove_4'];
		if($DocApprove_1 != "NULL") { array_push($Approve,$DocApprove_1); }
		if($DocApprove_2 != "NULL") { array_push($Approve,$DocApprove_2); }
		if($DocApprove_3 != "NULL") { array_push($Approve,$DocApprove_3); }
		if($DocApprove_4 != "NULL") { array_push($Approve,$DocApprove_4); }
		$countApp = count($Approve);

		if($countApp > 0) {
			for($i = 0; $i < $countApp; $i++) {
				$AppSQL = "INSERT INTO memo_approve SET DocEntry = '$DocEntry', VisOrder = '$i', AppUkeyReq = '".$Approve[$i]."', CreateUkey = '$CreateUkey'";
				// echo $AppSQL."</br>";
				MySQLInsert($AppSQL);
			}
		} else {
			/* Check LV CODE */
			$LvCode = $_SESSION['LvCode'];
			$LvCondition = array("LV001","LV002","LV003","LV004","LV005","LV006","LV010","LV011","LV022","LV027","LV038","LV045","LV051","LV057","LV062","LV072","LV085","LV098");
			/* ถ้าไม่ใช่ ผจก. ให้ไปหา ผจก. แต่ละแผนกมาค้นหาแล้ว INSERT */
			$LvChk = array_search($LvCode, $LvCondition);
			if(!$LvChk) {
				/* ไม่ใช่ ผจก. */
				/* หา LvCode ผจก. */
				switch($_SESSION['DeptCode']) {
					case "DP001": $AppLv = "LV001"; break;
					case "DP002":
					case "DP009": $AppLv = "LV057"; break;
					case "DP003": $AppLv = "LV011"; break;
					case "DP004": $AppLv = "LV004"; break;
					case "DP005": $AppLv = "LV027"; break;
					case "DP006": $AppLv = "LV038"; break;
					case "DP007": $AppLv = "LV045"; break;
					case "DP008": $AppLv = "LV051"; break;
					case "DP010": $AppLv = "LV062"; break;
					case "DP011": $AppLv = "LV072"; break;
					case "DP012": $AppLv = "LV085"; break;
					case "DP013": $AppLv = "LV093"; break;
				}
				$MgrSQL  = "SELECT T0.uKey FROM users T0 WHERE T0.LvCode = '$AppLv' AND T0.UserStatus = 'A' LIMIT 1";
				$MgrRST  = MySQLSelect($MgrSQL);
				$Approve = $MgrRST['uKey'];
				$AppSQL  = "INSERT INTO memo_approve SET DocEntry = '$DocEntry', VisOrder = '0', AppUkeyReq = '$Approve', CreateUkey = '$CreateUkey'";
				// echo $AppSQL;
				MySQLInsert($AppSQL);
			} else {
				/* ใช่ ผจก. */
				if($_POST['SaveType'] == 1) {
					$UpdateSQL = "UPDATE memo_header SET DraftStatus = 'N', DocStatus = 'C', AppStatus = 'Y', UpdateUkey = '$CreateUkey' WHERE DocEntry = '$DocEntry'";
					// echo $UpdateSQL;
					MySQLUpdate($UpdateSQL);
				}
			}
		}

		/* INSERT ATTACHMENT */
		if(isset($_FILES['DocAttach']['name'])) {
			$Totals = count($_FILES['DocAttach']['name'])-1;
			// echo $Totals;
			$NextROWSQL = "SELECT IFNULL(MAX(T0.VisOrder)+1,0) AS 'NextRow', T1.DocType, T1.DocNum FROM memo_attach T0 LEFT JOIN memo_header T1 ON T0.DocEntry = T1.DocEntry WHERE T0.DocEntry = $DocEntry";
			$NextROWRST = MySQLSelect($NextROWSQL);
			$row     = $NextROWRST['NextRow'];
			$DocType = $NextROWRST['DocType'];
			$DocNum  = $NextROWRST['DocNum'];
			for($i = 0; $i <= $Totals; $i++) {
				$FileProcess = explode(".",basename($_FILES['DocAttach']['name'][$i]));
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

				$tmpFilePath = $_FILES['DocAttach']['tmp_name'][$i];
				if($tmpFilePath != "") {
					$NewFilePath = "../../../../FileAttach/MEMO/".$DocNum."-".$row.".".$FileExt;
					move_uploaded_file($tmpFilePath,$NewFilePath);
					// $DocEntry = 2;

					$AttachSQL = "INSERT INTO memo_attach SET
						DocEntry = $DocEntry,
						VisOrder = $row,
						FileOriName = '$FileOriName',
						FileDirName = '".$DocNum."-".$row."',
						FileExt = '$FileExt',
						UploadUkey = '$CreateUkey'
					;";
					// echo $AttachSQL;
					MySQLInsert($AttachSQL);
					$row++;
				}
			}
		}

	}
}

if($_GET['p'] == "MemoList") {
	$year  = $_POST['y'];
	$month = $_POST['m'];
	$team  = $_POST['t'];
	$WhrSQL = "";
	
	switch($team) {
		case "ALL":
			$WhrSQL = " AND T0.DocSecret = 'N' ";
			break;
		default:
			$WhrSQL = " AND (";
			if($team == $_SESSION['DeptCode']) {
				$WhrSQL .= " (T0.CreateUkey = '".$_SESSION['ukey']."' AND T0.DocSecret = 'Y') OR ";
			}
			$WhrSQL .= " (T2.DeptCode = '$team' AND T0.DocSecret = 'N')) ";
		break;
	}
	$MemoSQL = 
	"SELECT
		T0.DocEntry, T0.DocDate, T0.DocNum, T0.DocType, T0.CANCELED, T0.DraftStatus, T0.DocStatus, T0.AppStatus, T0.Printed,
		T0.DocTitle, T0.DocDetail, T0.DocSecret, T0.CreateUkey, CONCAT(T1.uName,' ',T1.uLastname) AS 'CreateName', T3.DeptName,
		(SELECT COUNT(P0.ApproveID) FROM memo_approve P0 WHERE P0.DocEntry = T0.DocEntry AND P0.AppState = 'Y') AS 'Approved',
        (SELECT COUNT(P0.ApproveID) FROM memo_approve P0 WHERE P0.DocEntry = T0.DocEntry) AS 'MaxApporve'
	FROM memo_header T0
	LEFT JOIN users T1 ON T0.CreateUkey = T1.uKey
	LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
	LEFT JOIN departments T3 ON T2.DeptCode = T3.DeptCode
	WHERE (YEAR(T0.DocDate) = $year AND MONTH(T0.DocDate) = $month) $WhrSQL 
	ORDER BY
		CASE WHEN (T0.CANCELED = 'N') THEN 1 ELSE 2 END,
		CASE WHEN (T0.DocType = 'MM') THEN 1 ELSE 2 END";
	 //
	 //echo $MemoSQL;
	$Rows = ChkRowDB($MemoSQL);
	if($Rows == 0) {
		$output = "<tr><td colspan='7' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$MemoQRY = MySQLSelectX($MemoSQL);
		$no = 0;
		while($MemoRST = mysqli_fetch_array($MemoQRY)) {
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
			$txt_print  = "<a class='btn btn-secondary btn-sm' href='javascript:void(0)'><i class='fas fa-print fa-fw fa-1x'></i></a>";
			$dis_edit   = NULL;
			
			$dis_import = NULL;
			$dis_cncl   = NULL;
			if($MemoRST['Printed'] == "Y") {
				$dis_prnt   = NULL;
			} else {
				$dis_prnt   = " disabled";
			}
			switch($int_status) {
				case 0:
					$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
					break;
				case 1:
					$txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
					if($_SESSION['ukey'] != $MemoRST['CreateUkey']) {
						$dis_edit   = " disabled";
					}
					$dis_import = " disabled";
					break;
				case 1.5:
					$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
					$dis_edit   = " disabled";
					$dis_import = " disabled";
					break;
				case 2:
					$txt_status = "<span class='badge bg-warning w-100'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ [".$MemoRST['Approved']."/".$MemoRST['MaxApporve']."]</span>";
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
					$dis_import = " disabled";
					break;
				case 5:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
					$dis_edit   = " disabled";
					$dis_import = " disabled";
					break;
			}
			if ($_SESSION['uClass'] == 29){
				$dis_prnt =  NULL ;
			}
			
			if($int_status != 0) {
					$txt_opt = "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
						$txt_opt.= "<i class='fas fa-cog fa-fw fa-1x'></i>";
					$txt_opt.= "</button>";
					$txt_opt.= "<ul class='dropdown-menu' style='font-size: 13px;'>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-view' onclick='PreviewMM(".$MemoRST['DocEntry'].",$int_status)'><i class='fas fa-info fa-fw fa-1x'></i> รายละเอียด</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-edit$dis_edit' onclick='EditMM(".$MemoRST['DocEntry'].")'><i class='fas fa-edit fa-fw fa-1x'></i> แก้ไขเอกสาร</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-prnt$dis_prnt' onclick='PrintMM(".$MemoRST['DocEntry'].",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์เอกสาร</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-impt$dis_import' onclick='ExportMM(".$MemoRST['DocEntry'].")'><i class='fas fa-share-square fa-fw fa-1x'></i> ส่งฝ่ายบัญชี</a></li>";
						//$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item ordr-impt$dis_import' data-docentry='".$PurReqRST['DocEntry']."'><i class='fas fa-file fa-fw fa-1x'></i> ส่งออกเป็นใบสั่งขาย</li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item pcrq-cncl$dis_cncl' onclick='CancelMM(".$MemoRST['DocEntry'].")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกเอกสาร</a></li>";
					$txt_opt.= "</ul>";
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
				$output .= "<td>$txt_opt</td>";
			$output .= "</tr>";
		}
	}
	$arrCol['MemoList'] = $output;
}

if($_GET['p'] == "PreviewMM") {
	$DocEntry   = $_POST['DocEntry'];
	$int_status = $_POST['int_status'];

	$HeaderSQL = "
		SELECT T0.DocEntry, T0.DocNum, T0.DocDate, T0.DocTitle, T0.DocMention, T0.DocCopyTo, T0.DocDetail,
			CONCAT(T1.uName, ' ', T1.uLastName, ' (', T1.uNickName, ')') AS CreateName
		FROM memo_header T0 
		LEFT JOIN users T1 ON T0.CreateUkey = T1.uKey 
		WHERE T0.DocEntry = '$DocEntry' OR T0.DocNum = '$DocEntry' LIMIT 1";
	// echo $HeaderSQL;
	$HeaderRST = MySQLSelect($HeaderSQL);
	$DocEntry = $HeaderRST['DocEntry'];

	$arrCol['view_DocNum'] = $HeaderRST['DocNum'];
	$arrCol['view_DocDate'] = date("d/m/Y",strtotime($HeaderRST['DocDate']));
	$arrCol['view_DocTitle'] = $HeaderRST['DocTitle'];
	$arrCol['view_CreateName'] = $HeaderRST['CreateName'];

	$DocMention = explode(",",$HeaderRST['DocMention']);
	$MentionName = "";
	for($i = 0; $i < count($DocMention); $i++) {
		$GetNameSQL = "SELECT CONCAT(T0.uName,' ',T0.uLastName) AS 'MentionName' FROM users T0 WHERE T0.uKey = '".$DocMention[$i]."' LIMIT 1";
		$GetNameRST = MySQLSelect($GetNameSQL);
		$MentionName .= "คุณ".$GetNameRST['MentionName'];
		if($i != count($DocMention)-1) {
			$MentionName .= "<br/>";
		}
	}
	$arrCol['view_MentionName'] = $MentionName;
	$arrCol['view_DocCopyTo'] = $HeaderRST['DocCopyTo'];
	$arrCol['view_DocDetail'] = $HeaderRST['DocDetail'];

	/* ATTACHMENT */
	$AttSQL = "SELECT T0.AttachID, T0.VisOrder, T0.FileOriName, T0.FileDirName, T0.FileExt, T0.UploadDate FROM memo_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileStatus = 'A' ORDER BY T0.VisOrder";
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
				$AttachList .= "<td class='text-center'><a class='btn btn-success btn-sm' href='../FileAttach/MEMO/".$AttRST['FileDirName'].".".$AttRST['FileExt']."' target='_blank'><i class='fas fa-file-download fa-fw fa-1x'></i></a></td>";
			$AttachList .= "</tr>";
			$no++;
		}
	}

	$arrCol['view_attachlist'] = $AttachList;

	/* APPROVE */
	$AppSQL = "SELECT
		T0.ApproveID, T0.DocEntry, T0.VisOrder, T0.AppUkeyReq, T0.AppState, CONCAT(T1.uName,' ',T1.uLastName) AS 'ApproveName', T1.uNickName AS 'ApproveNick', T0.AppRemark, T0.AppDate AS 'ApproveDate'
	FROM memo_approve T0
	LEFT JOIN users T1 ON T0.AppUkeyReq = T1.uKey
	WHERE T0.DocEntry = $DocEntry
	ORDER BY T0.VisOrder ASC";
	$Rows = ChkRowDB($AppSQL);
	$Approve = "";

	if(!isset($_GET['App'])) {
		if($Rows == 0) {
			$Approve .= "<tr><td colspan='5' class='text-center'>ไม่มีข้อมูลการอนุมัติ</td></tr>";
		} else {
			$AppQRY = MySQLSelectX($AppSQL);
			$no = 0;
			while($AppRST = mysqli_fetch_array($AppQRY)) {
				$no++;
				$Text_App = null;
				$Text_Remark = $AppRST['AppRemark'];

				if($AppRST['ApproveDate'] == null) {
					$AppDate = null;
				} else {
					$AppDate = date("d/m/Y",strtotime($AppRST['ApproveDate']))." เวลา ".date("H:i",strtotime($AppRST['ApproveDate']))." น.";
				}

				switch($AppRST['AppState']) {
					case "0": $Text_App = "<i class='fas fa-minus fa-fw fa-1x'></i>"; break;
					case "1": $Text_App = "<span class='text-muted'><i class='far fa-clock fa-fw fa-lg'></i> รอพิจารณา</span>"; break;
					case "Y": $Text_App = "<span class='text-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
					case "N": $Text_App = "<span class='text-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
				}

				if($AppRST['ApproveNick'] == NULL || $AppRST['ApproveNick'] == "") {
					$NickName = "";
				} else {
					$NickName = " (".$AppRST['ApproveNick'].")";
				}

				$Approve .= "<tr>";
					$Approve .= "<td class='text-right'>".number_format($no,0)."</td>";
					$Approve .= "<td>".$AppRST['ApproveName'].$NickName."</td>";
					$Approve .= "<td class='text-center'>$Text_App</td>";
					$Approve .= "<td>$Text_Remark</td>";
					$Approve .= "<td class='text-center'>$AppDate</td>";
				$Approve .= "</tr>";
			}
		}
	} else {
		/* ฝั่งอนุมัติ */
		if($Rows == 0) {
			$Approve .= "<tr><td colspan='6' class='text-center'>ไม่มีข้อมูลการอนุมัติ</td></tr>";
		} else {
			$AppQRY = MySQLSelectX($AppSQL);
			$no = 0;
			while($AppRST = mysqli_fetch_array($AppQRY)) {
				$no++;
				$Text_App = null;
				$Text_Remark = $AppRST['AppRemark'];

				if($AppRST['ApproveDate'] == null) {
					$AppDate = null;
				} else {
					$AppDate = date("d/m/Y",strtotime($AppRST['ApproveDate']))." เวลา ".date("H:i",strtotime($AppRST['ApproveDate']))." น.";
				}

				switch($AppRST['AppState']) {
					case "0": $Text_App = "<i class='fas fa-minus fa-fw fa-1x'></i>"; break;
					case "1": $Text_App = "<span class='text-muted'><i class='far fa-clock fa-fw fa-lg'></i> รอพิจารณา</span>"; break;
					case "Y": $Text_App = "<span class='text-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
					case "N": $Text_App = "<span class='text-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
				}

				if($AppRST['ApproveNick'] == NULL || $AppRST['ApproveNick'] == "") {
					$NickName = "";
				} else {
					$NickName = " (".$AppRST['ApproveNick'].")";
				}
				if($AppRST['AppUkeyReq'] == $_SESSION['ukey'] || $AppRST['AppUkeyReq'] == "42b4e5ab67feb54da8216a5439fd6dcb") {
					$Approve .= "<tr>";
						$Approve .= "<td class='text-right'>".number_format($no,0)."</td>";
						$Approve .= "<td>".$AppRST['ApproveName'].$NickName."</td>";
						$Approve .= "<td class='text-center'><select class='form-select form-select-sm' name='AppState_".$AppRST['ApproveID']."' id='AppState_".$AppRST['ApproveID']."'><option value='1' selected>รอพิจารณา</option><option value='Y'>อนุมัติ</option><option value='N'>ไม่อนุมัติ</option></select></td>";
						$Approve .= "<td><input class='form-control form-control-sm' name='Remark_".$AppRST['ApproveID']."' id='Remark_".$AppRST['ApproveID']."' placeholder='ระบุเหตุผลการพิจารณา' /></td>";
						$Approve .= "<td class='text-center'>$AppDate</td>";
						$Approve .= "<td class='text-center'><button type='button' class='btn btn-success btn-save btn-sm btn-block' onclick='AppMemo(".$AppRST['ApproveID'].",".$AppRST['DocEntry'].")'><i class='fas fa-save fa-fw fa-1x'></i></button></td>";
					$Approve .= "</tr>";
				} else {
					$Approve .= "<tr>";
						$Approve .= "<td class='text-right'>".number_format($no,0)."</td>";
						$Approve .= "<td>".$AppRST['ApproveName'].$NickName."</td>";
						$Approve .= "<td class='text-center'>$Text_App</td>";
						$Approve .= "<td>$Text_Remark</td>";
						$Approve .= "<td class='text-center'>$AppDate</td>";
						$Approve .= "<td class='text-center'>&nbsp;</td>";
					$Approve .= "</tr>";
				}
			}
		}
	}
	$arrCol['view_approvelist'] = $Approve;
}

if($_GET['p'] == "EditMM") {
	$DocEntry = $_POST['DocEntry'];
	/* Header Memo */
	$HeaderSQL = "SELECT T0.DocEntry, T0.DocDate, T0.DocType, T0.DocSecret, T0.DocTitle, T0.DocMention, T0.DocCopyTo, T0.DocDetail, T0.DocSignOff, GROUP_CONCAT(T1.AppUkeyReq) AS 'UkeyReq' FROM memo_header T0 LEFT JOIN memo_approve T1 ON T0.DocEntry = T1.DocEntry WHERE T0.DocEntry = $DocEntry LIMIT 1";
	$Rows = ChkRowDB($HeaderSQL);
	if($Rows > 0) {
		$HeaderRST = MySQLSelect($HeaderSQL);
		$arrCol['DocEntry']   = $HeaderRST['DocEntry'];
		$arrCol['DocDate']    = date("Y-m-d",strtotime($HeaderRST['DocDate']));
		$arrCol['DocType']    = $HeaderRST['DocType'];
		$arrCol['DocSecret']  = $HeaderRST['DocSecret'];
		$arrCol['DocTitle']   = $HeaderRST['DocTitle'];
		$arrCol['DocMention'] = $HeaderRST['DocMention'];
		$arrCol['DocCopyTo']  = $HeaderRST['DocCopyTo'];
		$arrCol['DocDetail']  = $HeaderRST['DocDetail'];
		$arrCol['DocSignOff'] = $HeaderRST['DocSignOff'];
		$arrCol['UkeyReq']    = $HeaderRST['UkeyReq'];
		/* Attachment */
		$AttachSQL = "SELECT T0.AttachID, T0.FileOriName, T0.FileDirName, T0.FileExt FROM memo_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileStatus = 'A'";
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
}

if($_GET['p'] == "GetAttach") {
	$DocEntry = $_POST['DocEntry'];
	/* Attachment */
	$AttachSQL = "SELECT T0.AttachID, T0.FileOriName, T0.FileDirName, T0.FileExt FROM memo_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileStatus = 'A'";
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
	$DelAttSQL = "UPDATE memo_attach SET FileStatus = 'I' WHERE AttachID = $AttachID";
	$DelAttQRY = MySQLUpdate($DelAttSQL);
}

if($_GET['p'] == "CancelMM") {
	$DocEntry = $_POST['DocEntry'];

	$CancelSQL = "UPDATE memo_header SET CANCELED = 'Y', DocStatus = 'C', CancelDate = NOW(), CancelUkey = '".$_SESSION['ukey']."' WHERE DocEntry = $DocEntry";
	$CancelQRY = MySQLUpdate($CancelSQL);
	if(!isset($CancelQRY)) {
		echo "ERROR";
	} else {
		echo "SUCCESS";
	}
}

if($_GET['p'] == "ExportMM") {
	$DocEntry = $_POST['DocEntry'];

	$GetMMSQL = "SELECT T0.DocEntry, T0.DocType, T0.DocNum, T0.DocDate, T0.DocTitle FROM memo_header T0 WHERE T0.DocEntry = $DocEntry AND T0.AppStatus = 'Y' LIMIT 1";
	$Rows     = ChkRowDB($GetMMSQL);
	if($Rows == 0) {
		$arrCol['AddStatus'] = "ERR::NO_RESULT";
	} else {
		$GetMMRST   = MySQLSelect($GetMMSQL);
		$DocType    = $GetMMRST['DocType'];
		$DocNum     = $GetMMRST['DocNum'];
		$DocDate    = date("Y-m-d",strtotime($GetMMRST['DocDate']));
		$DocTitle   = $GetMMRST['DocTitle'];
		$CreateUkey = $_SESSION['ukey'];
		/* Check Duplicate */
		$ChkSQL = "SELECT T0.DocEntry FROM docacc_header T0 WHERE T0.DocNum = '$DocNum' AND T0.DocType = '$DocType' AND T0.RecipientStatus IN ('1','Y') AND T0.DocStatus = 'O'";
		$Rows   = ChkRowDB($ChkSQL);
		if($Rows > 0) {
			$arrCol['AddStatus'] = "ERR::DUPLICATE";
		} else {
			$InsertSQL = "INSERT INTO docacc_header SET DocNum = '$DocNum', DocType = '$DocType', DocDate = '$DocDate', DocTitle = '$DocTitle', SenderUkey = '$CreateUkey'";
			$InsertID  = MySQLInsert($InsertSQL);
			if($InsertID > 0) {
				$arrCol['AddStatus'] = "SUCCESS";
			} else {
				$arrCol['AddStatus'] = "ERR::CANNOT_INSERT";
			}
		}
	}

}

if($_GET['p'] == 'GetItemMemo') {
	$Year = $_POST['Year'];
	$Ukey = $_SESSION['ukey'];

	$SQL = 
		"SELECT
			T0.DocEntry, T0.DocDate, T0.DocNum, T0.DocType, T0.CANCELED, T0.DraftStatus, T0.DocStatus, T0.AppStatus, T0.Printed,
			T0.DocTitle, T0.DocDetail, T0.DocSecret, T0.CreateUkey, CONCAT(T1.uName,' ',T1.uLastname) AS 'CreateName', T3.DeptName,
			(SELECT COUNT(P0.ApproveID) FROM memo_approve P0 WHERE P0.DocEntry = T0.DocEntry AND P0.AppState = 'Y') AS 'Approved',
			(SELECT COUNT(P0.ApproveID) FROM memo_approve P0 WHERE P0.DocEntry = T0.DocEntry) AS 'MaxApporve'
		FROM memo_header T0
		LEFT JOIN users T1 ON T0.CreateUkey = T1.uKey
		LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
		LEFT JOIN departments T3 ON T2.DeptCode = T3.DeptCode  
		LEFT JOIN memo_approve T4 ON T0.DocEntry = T4.DocEntry
		WHERE (T0.CreateUkey  = '$Ukey' OR T4.AppUkeyAct = '$Ukey' OR T4.AppUkeyReq = '$Ukey') AND YEAR(T0.DocDate) = '$Year' AND T4.AppState IN ('Y','N')
		ORDER BY T0.CreateDate DESC";
	$QRY = MySQLSelectX($SQL);
	$output = ""; 
	$no = 0;
	while($MemoRST = mysqli_fetch_array($QRY)) {
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
		$txt_print  = "<a class='btn btn-secondary btn-sm' href='javascript:void(0)'><i class='fas fa-print fa-fw fa-1x'></i></a>";
		$dis_edit   = NULL;
		
		$dis_import = NULL;
		$dis_cncl   = NULL;
		if($MemoRST['Printed'] == "Y") {
			$dis_prnt   = NULL;
		} else {
			$dis_prnt   = " disabled";
		}
		switch($int_status) {
			case 0:
				$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
				break;
			case 1:
				$txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
				if($_SESSION['ukey'] != $MemoRST['CreateUkey']) {
					$dis_edit   = " disabled";
				}
				$dis_import = " disabled";
				break;
			case 1.5:
				$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
				$dis_edit   = " disabled";
				$dis_import = " disabled";
				break;
			case 2:
				$txt_status = "<span class='badge bg-warning w-100'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ [".$MemoRST['Approved']."/".$MemoRST['MaxApporve']."]</span>";
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
				$dis_import = " disabled";
				break;
			case 5:
				$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
				$dis_edit   = " disabled";
				$dis_import = " disabled";
				break;
		}
		if ($_SESSION['uClass'] == 29){
			$dis_prnt =  NULL ;
		}
		
		if($int_status != 0) {
				$txt_opt = "<a href='javascript:void(0);' class='dropdown-item pcrq-prnt$dis_prnt' onclick='PrintMM(".$MemoRST['DocEntry'].",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์เอกสาร</a>";
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
			$output .= "<td>$txt_opt</td>";
		$output .= "</tr>";
	}
	$arrCol['MemoList'] = $output;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
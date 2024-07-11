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
		$ListWhr = " AND (T3.DeptCode = '$DeptCode' AND T1.BillTeamCode = '$t')";
	}

	$SQL1 =
		"SELECT
			T0.DocEntry,
			T1.DocEntry AS 'RtqcEntry', T1.DocType, T1.DraftStatus, T1.CANCELED, T1.DocStatus, T1.AppStatus, T1.Printed, T1.DocDate,
			T1.DocNum, T1.BillEntry, T1.BillCardCode, T1.BillCardName, T1.RefDoc1, T1.BillDocNum, T1.BillTeamCode, CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')') AS 'SlpName',
			T3.DeptCode, T4.DeptName, CONCAT(T5.uName,' ',T5.uLastName) AS 'CreateName', T0.RecipientStatus
		FROM docrtqc_header T0
		LEFT JOIN rtqc_header T1 ON T0.RtqcEntry = T1.DocEntry
		LEFT JOIN users T2 ON T1.BillSlpUkey = T2.uKey
		LEFT JOIN positions T3 ON T2.LvCode = T3.LvCode
		LEFT JOIN departments T4 ON T3.DeptCode = T4.DeptCode
		LEFT JOIN users T5 ON T1.CreateUkey = T5.uKey
		WHERE
			(YEAR(T0.SenderDate) = $y AND MONTH(T0.SenderDate) = $m) $ListWhr AND T0.RecipientStatus = '1'
		ORDER BY
			CASE
				WHEN (T1.DocType = 'D')  THEN 1
				WHEN (T1.DocType = 'L')  THEN 2
				WHEN (T1.DocType = 'AC') THEN 3
				WHEN (T1.DocType = 'X')  THEN 4
				ELSE 99
			END, T1.DocEntry DESC";
	$ROW1 = CHKRowDB($SQL1);
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

			$dis_prnt   = "";
			$dis_import = "";
			$dis_cncl   = "";

			if($RST1['RecipientStatus'] == "1") {
				$txt_status = "<span class='badge bg-warning w-100'><i class='far fa-clock fa-fw fa-lg'></i> รอดำเนินการ</span>";
			} else {
				$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> กำลังดำเนินการ</span>";
			}
			

			if($int_status != 0) {
				$txt_opt = "<div calss='dropdown'>";
					$txt_opt.= "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
						$txt_opt.= "<i class='fas fa-cog fa-fw fa-1x'></i>";
					$txt_opt.= "</button>";
					$txt_opt.= "<ul class='dropdown-menu' style='font-size: 13px;'>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-view' onclick='PreviewDoc(".$RST1['RtqcEntry'].",$int_status)'><i class='fas fa-info fa-fw fa-1x'></i> รายละเอียด</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-prnt$dis_prnt' onclick='PrintDoc(".$RST1['RtqcEntry'].");'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</a></li>";
					$txt_opt.= "</ul>";
				$txt_opt.= "</div>";
			} else {
				$txt_opt = "";
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
			$arrCol[$i]['DocNum']       = "<a href='javascript:void(0);' onclick='PreviewDoc(".$RST1['RtqcEntry'].",$int_status);'>".$RST1['DocNum']."</a>";
			$arrCol[$i]['BillCardCode'] = $RST1['BillCardCode']." | ".$RST1['BillCardName']."<br/><small>ผู้จัดทำ: ".$RST1['CreateName']."</small>";
			$arrCol[$i]['RefDocNum']    = $RST1['RefDoc1'];
			$arrCol[$i]['BillDocNum']   = $RST1['BillDocNum'];
			$arrCol[$i]['BillSlpName']  = "<span class='badge bg-dark'>".$RST1['DeptName']."</span><br/>".$RST1['SlpName'];
			$arrCol[$i]['txt_status']   = $txt_status;
			$arrCol[$i]['txt_opt']      = $txt_opt;
			$arrCol[$i]['int_status']   = $int_status;
			$arrCol[$i]['RecptStatus']  = $RST1['RecipientStatus'];

			$no++;
			$i++;

		}
	}
}


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
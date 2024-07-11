<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
$output = "";
if($_SESSION['UserName'] == NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

function GetDocTotal($year,$month,$CardCode) {
	$GetTotalSQL = 
	"SELECT TOP 1 SUM(A0.DocTotal) AS 'DocTotal' FROM (
		SELECT SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal' FROM OINV T0 WHERE YEAR(T0.DocDate) = $year AND MONTH(T0.DocDate) = $month AND T0.CardCode = '".$CardCode."'
		UNION ALL
		SELECT -SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal' FROM ORIN T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE YEAR(T0.DocDate) = $year AND MONTH(T0.DocDate) = $month AND T0.CardCode = '".$CardCode."' AND T1.BeginStr IN ('S1-','SR-')
	) A0";
	$GetTotalQRY = SAPSelect($GetTotalSQL);
	$GetTotalRST = odbc_fetch_array($GetTotalQRY);
	if($GetTotalRST['DocTotal'] < 0) {
		$class = " class='text-danger'";
	} elseif($GetTotalRST['DocTotal'] > 0) {
		$class = " class='text-success'";
	} else {
		$class = NULL;
	}

	return "<span$class>".number_format($GetTotalRST['DocTotal'],2)."</span>";
}

function GetDocTotalInt($year,$month,$CardCode) {
	$GetTotalSQL = 
	"SELECT TOP 1 SUM(A0.DocTotal) AS 'DocTotal' FROM (
		SELECT SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal' FROM OINV T0 WHERE YEAR(T0.DocDate) = $year AND MONTH(T0.DocDate) = $month AND T0.CardCode = '".$CardCode."'
		UNION ALL
		SELECT -SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal' FROM ORIN T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE YEAR(T0.DocDate) = $year AND MONTH(T0.DocDate) = $month AND T0.CardCode = '".$CardCode."' AND T1.BeginStr IN ('S1-','SR-')
	) A0";
	$GetTotalQRY = SAPSelect($GetTotalSQL);
	$GetTotalRST = odbc_fetch_array($GetTotalQRY);
	return $GetTotalRST['DocTotal'];
}

function GetCardDetail($CardCode) {
	$GetDetailSQL = 
		"SELECT TOP 1
			A0.CardCode, A2.Name, SUM(A0.DocTotal) AS 'DocTotal'
		FROM (
			SELECT T0.CardCode, SUM(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal' FROM OINV T0 WHERE T0.DocStatus = 'O' AND T0.CardCode = '$CardCode' GROUP BY T0.CardCode
			UNION ALL
			SELECT T0.CardCode, -SUM(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal' FROM ORIN T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE T0.DocStatus = 'O' AND T0.CardCode = '$CardCode' AND T1.BeginStr IN ('S1-','SR-') GROUP BY T0.CardCode
		) A0
		LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
		LEFT JOIN dbo.[@TERITORY] A2 ON A1.U_Teritory = A2.Code
		GROUP BY A0.CardCode, A2.Name";
	$GetDetailQRY = SAPSelect($GetDetailSQL);
	$GetDetailRST = odbc_fetch_array($GetDetailQRY);

	if(ChkRowSAP($GetDetailSQL) > 0) {
		$Teritory = conutf8($GetDetailRST['Name']);
		$DocTotal = number_format($GetDetailRST['DocTotal'],2);
	} else {
		$PvSQL = "SELECT T1.Name FROM OCRD T0 LEFT JOIN dbo.[@TERITORY] T1 ON T0.U_Teritory = T1.Code WHERE T0.CardCode = '$CardCode'";
		$PvQRY = SAPSelect($PvSQL);
		$PvRST = odbc_fetch_array($PvQRY);
		$Teritory = conutf8($PvRST['Name']);
		$DocTotal = "-";
	}

	return array($Teritory, $DocTotal);
}

function GetCardDetailTRUE($CardCode) {
	$GetDetailSQL = 
		"SELECT TOP 1
			A0.CardCode, (A1.[MailAddres]+' '+A1.[MailZipCod]+' '+A1.MailBlock+' '+A1.MailCity) AS 'Name', SUM(A0.DocTotal) AS 'DocTotal'
		FROM (
			SELECT T0.CardCode, SUM(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal' FROM OINV T0 WHERE T0.DocStatus = 'O' AND T0.CardCode = '$CardCode' GROUP BY T0.CardCode
			UNION ALL
			SELECT T0.CardCode, -SUM(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal' FROM ORIN T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE T0.DocStatus = 'O' AND T0.CardCode = '$CardCode' AND T1.BeginStr IN ('S1-','SR-') GROUP BY T0.CardCode
		) A0
		LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
		GROUP BY A0.CardCode, A1.[MailAddres], A1.[MailZipCod], A1.MailBlock, A1.MailCity";
	$GetDetailQRY = SAPSelect($GetDetailSQL);
	$GetDetailRST = odbc_fetch_array($GetDetailQRY);

	if(ChkRowSAP($GetDetailSQL) > 0) {
		$Teritory = conutf8($GetDetailRST['Name']);
		$DocTotal = number_format($GetDetailRST['DocTotal'],2);
		$DocTotalN = $GetDetailRST['DocTotal'];
	} else {
		$PvSQL = "SELECT (T0.[MailAddres]+' '+T0.[MailZipCod]+' '+T0.MailBlock+' '+T0.MailCity) AS 'Name' FROM OCRD T0 WHERE T0.CardCode = '$CardCode'";
		$PvQRY = SAPSelect($PvSQL);
		$PvRST = odbc_fetch_array($PvQRY);
		$Teritory = conutf8($PvRST['Name']);
		$DocTotal = "-";
		$DocTotalN = 0;
	}

	return array($Teritory, $DocTotal,$DocTotalN);
}

function GetCardDetailTRUEInt($CardCode) {
	$GetDetailSQL = 
		"SELECT TOP 1
			A0.CardCode, (A1.[MailAddres]+' '+A1.[MailZipCod]+' '+A1.MailBlock+' '+A1.MailCity) AS 'Name', SUM(A0.DocTotal) AS 'DocTotal'
		FROM (
			SELECT T0.CardCode, SUM(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal' FROM OINV T0 WHERE T0.DocStatus = 'O' AND T0.CardCode = '$CardCode' GROUP BY T0.CardCode
			UNION ALL
			SELECT T0.CardCode, -SUM(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal' FROM ORIN T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE T0.DocStatus = 'O' AND T0.CardCode = '$CardCode' AND T1.BeginStr IN ('S1-','SR-') GROUP BY T0.CardCode
		) A0
		LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
		GROUP BY A0.CardCode, A1.[MailAddres], A1.[MailZipCod], A1.MailBlock, A1.MailCity";
	$GetDetailQRY = SAPSelect($GetDetailSQL);
	$GetDetailRST = odbc_fetch_array($GetDetailQRY);

	if(ChkRowSAP($GetDetailSQL) > 0) {
		$DocTotal = $GetDetailRST['DocTotal'];
	} else {
		$DocTotal = 0;
	}

	return $DocTotal;
}


if($_GET['p'] == "GetEmpName") {
	switch($_SESSION['DeptCode']) {
		case "DP005":
		case "DP008":
		case "DP007":
			$EmpWhr = " AND (T1.DeptCode = '".$_SESSION['DeptCode']."' AND T1.UClass IN (18,19,20,23,24,25,26)) "; break;
		case "DP006": $EmpWhr = " AND ((T1.DeptCode IN ('DP005','DP006','DP007','DP008')) AND T1.UClass IN (18,19,20,23,24,25,26)) "; break;
		case "DP009": $EmpWhr = " AND (T1.DeptCode = 'DP009' AND T1.UClass IN (29,30,31)) "; break;
		case "DP010": $EmpWhr = " AND (T1.DeptCode = 'DP010') "; break;
		default: $EmpWhr = " AND (T1.DeptCode IN ('DP002','DP003','DP005','DP006','DP007','DP008','DP009','DP010') AND T1.UClass IN (0,2,3,18,19,20,23,24,25,26,29,30,31)) "; break;
	}
	$EmpSQL = "SELECT
				T0.uKey, CONCAT(T0.uName,' ',T0.uLastName) AS 'EmpName', T0.uNickName, T0.LvCode, T1.UClass, T1.DeptCode, T2.DeptName
			FROM users T0
			LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
			LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
			WHERE T0.UserStatus = 'A' AND T0.uNickName != 'Online' $EmpWhr
			ORDER BY T1.DeptCode ASC, T1.uClass ASC";
			//echo $EmpSQL;
	$EmpQRY = MySQLSelectX($EmpSQL);
	$TmpDeptCode = "";
	$TmpDeptName = "";
	$output .= "";
	while($EmpRST = mysqli_fetch_array($EmpQRY)) {
		if($_SESSION['uClass'] == 20 && $EmpRST['uKey'] != $_SESSION['ukey']) {
			$disb = " disabled";
		} else {
			$disb = NULL;
		}
		if($EmpRST['uNickName'] == "") {
			$nickname = NULL;
		} else {
			$nickname = " (".$EmpRST['uNickName'].")";
		}
		if($_SESSION['ukey'] == $EmpRST['uKey']) {
			$slct = " selected";
		} else {
			$slct = NULL;
		}
		if($TmpDeptCode == "" || $TmpDeptCode != $EmpRST['DeptCode']) {
			if($TmpDeptCode != "") {
				$output .= "</optgroup>";
			}
			$output .= "<optgroup label='".$EmpRST['DeptName']."'>";
				$output .= "<option value='".$EmpRST['uKey']."'$disb$slct>".$EmpRST['EmpName'].$nickname."</option>";
			$TmpDeptCode = $EmpRST['DeptCode'];
		} else {
				$output .= "<option value='".$EmpRST['uKey']."'$disb$slct>".$EmpRST['EmpName'].$nickname."</option>";
		}
	}

	$arrCol['view_user'] = $output;
}

if($_GET['p'] == "GetCardCode") {
	$CardSQL = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE (T0.CardCode NOT LIKE 'A-%' AND T0.CardCode NOT LIKE 'B-%' AND T0.CardCode NOT LIKE 'P-%' AND T0.CardCode NOT LIKE 'R-%' AND T0.CardCode NOT LIKE 'V-%' AND T0.CardCode NOT LIKE 'W-%') ORDER BY T0.CardCode ASC";
	$CardQRY = MySQLSelectX($CardSQL);
	// echo $CardSQL;
	$output = "";
	while($CardRST = mysqli_fetch_array($CardQRY)) {
		$output .= "<option value='".$CardRST['CardCode']."'>".$CardRST['CardCode']." | ".$CardRST['CardName']."</option>";
	}
	$arrCol['view_CardCode'] = $output;
}

if($_GET['p'] == "GetWorkTrip") {
	$year  = $_POST['y'];
	$month = $_POST['m'];
	$view  = $_POST['v'];
	$user  = $_POST['u'];
	$today = date("Y-m-d");

	/* GET MEETSTATUS COUNT */
	$MeetSQL = "SELECT
					T0.CreateUkey,
					IFNULL(COUNT(CASE WHEN T0.MeetType = 0 THEN 1 END),0) AS 'Meet0',
                    IFNULL(COUNT(CASE WHEN T0.MeetType IN (1,3) THEN 1 END),0) AS 'Meet1',
					IFNULL(COUNT(CASE WHEN T0.MeetType IN (2,4) THEN 1 END),0) AS 'Meet2',
					IFNULL(COUNT(CASE WHEN T0.MeetType = 5 THEN 1 END),0) AS 'Meet5',
					IFNULL(COUNT(CASE WHEN T0.MeetType = 6 THEN 1 END),0) AS 'Meet6',
					IFNULL(COUNT(CASE WHEN T0.CardCode IS NULL THEN 1 END),0) AS 'Meet7'
				FROM route_planner T0
				WHERE (YEAR(T0.PlanDate) = $year AND MONTH(T0.PlanDate) = $month) AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A' 
				GROUP BY T0.CreateUkey";
	// echo $MeetSQL;
	$Rows = ChkRowDB($MeetSQL);
	if($Rows > 0) {
		$MeetRST = MySQLSelect($MeetSQL);
		$st_Meet0 = $MeetRST['Meet0'];
		$st_Meet1 = $MeetRST['Meet1'];
		$st_Meet2 = $MeetRST['Meet2'];
		$st_Meet5 = $MeetRST['Meet5'];
		$st_Meet6 = $MeetRST['Meet6'];
		$st_Meet7 = $MeetRST['Meet7'];
	} else {
		$st_Meet0 = 0;
		$st_Meet1 = 0;
		$st_Meet2 = 0;
		$st_Meet5 = 0;
		$st_Meet6 = 0;
		$st_Meet7 = 0;
	}

	$output = "";
	$output .= "<div class='row'>";
		$output .= "<div class='col-lg-2 col-md-4 col-6 mb-2'>";
			$output .= "<span class='badge w-100 p-2 text-white MeetType0'><i class='fas fa-clock fa-fw fa-lg'></i> รอเข้าพบ $st_Meet0 ราย</span>";
		$output .= "</div>";
		$output .= "<div class='col-lg-2 col-md-4 col-6 mb-2'>";
			$output .= "<span class='badge w-100 p-2 text-white MeetType1'><i class='fas fa-street-view fa-fw fa-lg'></i> เข้าพบ (ในพื้นที่) $st_Meet1 ราย</span>";
		$output .= "</div>";
		$output .= "<div class='col-lg-2 col-md-4 col-6 mb-2'>";
			$output .= "<span class='badge w-100 p-2 text-white MeetType2'><i class='fas fa-male fa-fw fa-lg'></i> เข้าพบ (นอกพื้นที่) $st_Meet2 ราย</span>";
		$output .= "</div>";
		$output .= "<div class='col-lg-2 col-md-4 col-6 mb-2'>";
			$output .= "<span class='badge w-100 p-2 text-white MeetType5'><i class='fas fa-exclamation-circle fa-fw fa-lg'></i> เข้าพบ (ไม่มีพิกัด) $st_Meet5 ราย</span>";
		$output .= "</div>";
		$output .= "<div class='col-lg-2 col-md-4 col-6 mb-2'>";
			$output .= "<span class='badge w-100 p-2 text-white MeetType6'><i class='fas fa-phone-volume fa-fw fa-lg'></i> โทร / ไลน์ $st_Meet6 ราย</span>";
		$output .= "</div>";
		$output .= "<div class='col-lg-2 col-md-4 col-6 mb-2'>";
			$output .= "<span class='badge w-100 p-2 text-white MeetType7'><i class='far fa-star fa-fw fa-lg'></i> ลูกค้ามุ่งหวัง $st_Meet7 ราย</span>";
		$output .= "</div>";
	$output .= "</div>";

	/* LOOP INSIDE THE MONTH */
	$loopday = cal_days_in_month(CAL_GREGORIAN, $month, $year);

	/* GET MEET TOTAL BY DATE */
	$MeetTotalSQL = "SELECT
						DAY(T0.PlanDate) AS 'Day', COUNT(T0.RouteEntry) AS 'MeetTotal'
					FROM route_planner T0
					WHERE (YEAR(T0.PlanDate) = $year AND MONTH(T0.PlanDate) = $month) AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A'
					GROUP BY T0.PlanDate";
	$MeetTotalQRY = MySQLSelectX($MeetTotalSQL);
	while($MeetTotalRST = mysqli_fetch_array($MeetTotalQRY)) {
		${$MeetTotalRST['Day']."_loop"} = $MeetTotalRST['MeetTotal'];
	}

	if($view == "GRID") {
		$output .= "<div class='calendar mt-2'>";
			$output .= "<ol class='day-names list-unstyled'>";
				$output .= "<li class='font-weight-bold text-center text-danger'>อาทิตย์</li>";
				$output .= "<li class='font-weight-bold text-center'>จันทร์</li>";
				$output .= "<li class='font-weight-bold text-center'>อังคาร</li>";
				$output .= "<li class='font-weight-bold text-center'>พุธ</li>";
				$output .= "<li class='font-weight-bold text-center'>พฤหัสบดี</li>";
				$output .= "<li class='font-weight-bold text-center'>ศุกร์</li>";
				$output .= "<li class='font-weight-bold text-center'>เสาร์</li>";
			$output .= "</ol>";

			$Get1stDay = date("w",strtotime($year."-".$month."-01"));
			$output .= "<ol class='days list-unstyled'>";
			/* LOOP OUTSIDE THE MONTH BEGINS */
			for($o=1; $o <= $Get1stDay; $o++) {
				$output .= "<li class='outside'>&nbsp;</li>";
			}
			

			for($d=1; $d<=$loopday; $d++) {
				$LoopDate = date("Y-m-d",strtotime($year."-".$month."-".$d));
				if(isset(${$d."_loop"}) != 0) {
					$GetTripSQL = "SELECT T0.RouteEntry, T0.CardCode, T0.CardName, CASE WHEN T0.MeetType IN (1,3) THEN 1 WHEN T0.MeetType IN (2,4) THEN 2 ELSE T0.MeetType END AS 'MeetType', T0.CreateUkey FROM route_planner T0 WHERE T0.PlanDate = '$LoopDate' AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A' ORDER BY T0.RouteEntry LIMIT 2";
					$GetTripQRY = MySQLSelectX($GetTripSQL);
					$looptrip = "";
					while($GetTripRST = mysqli_fetch_array($GetTripQRY)) {
						if($GetTripRST['CardCode'] == NULL) {
							$ShowCard = "<i class='far fa-star fa-fw fa-1x'></i> ".$GetTripRST['CardName'];
						} else {
							$ShowCard = $GetTripRST['CardCode']." | ".$GetTripRST['CardName'];
						}
						if(($GetTripRST['MeetType'] == 0 || $GetTripRST['MeetType'] == 7) && ($GetTripRST['CreateUkey'] == $_SESSION['ukey'])) {
							$onclick = "onclick='CheckIn(\"".$GetTripRST['CardCode']."\",".$GetTripRST['RouteEntry'].");'";
						} else {
							$onclick = NULL;
						}
						$looptrip .= "<div class='event MeetType".$GetTripRST['MeetType']."' $onclick>".$ShowCard."</div>";
					}
					$nummore = ${$d."_loop"}-2;
					if($nummore > 0) {
						$looptrip .= "<div class='event event-more text-center' onclick='TripDate(\"".$LoopDate."\",\"".$user."\")'>+".$nummore." รายการ</div>";
					}
				} else {
					$looptrip = NULL;
				}
				if(date("w",strtotime($LoopDate)) == 0) { $clsSunday = " text-danger"; } else { $clsSunday = NULL; }
				if($LoopDate == $today) { $clstoday = " class='today'"; } else { $clstoday = NULL; }
				$output .= "<li$clstoday>";
					$output .= "<div class='date text-right$clsSunday' onclick='TripDate(\"".$LoopDate."\",\"".$user."\")'>".$d."</div>";
					$output .= $looptrip;
				$output .= "</li>";
			}
			/* LOOP OUTSIDE THE MONTH ENDS */
			$GetLastDay = date("w",strtotime($year."-".$month."-".$loopday));
			for($o=1; $o <= 6-$GetLastDay; $o++) {
				$output .= "<li class='outside'>&nbsp;</li>";
			}
			$output .= "</ol>";
		$output .= "</div>";

	} else if($view == "LIST") {
		$output .= "<div class='table-responsive mt-2'>";
			$output .= "<table class='table table-bordered table-sm' style='font-size: 12px;'>";
				$output .= "<thead class='text-center'>";
					$output .= "<tr>";
						$output .= "<th width='7.5%'>วันที่</th>";
						$output .= "<th width='5%'>จุดที่</th>";
						$output .= "<th width='25%'>ชื่อร้านค้า</th>";
						$output .= "<th width='7.5%'>จังหวัด</th>";
						$output .= "<th>รายละเอียดแผนงาน</th>";
						$output .= "<th width='7.5%'>ประมาณการยอดขาย<br/>(บาท)</th>";
						$output .= "<th width='7.5%'>ยอดขาย<br/>(บาท)</th>";
						$output .= "<th width='7.5%'>บิลรอเรียกเก็บ<br/>(บาท)</th>";
						$output .= "<th width='10%'>สถานะ</th>";
						$output .= "<th width='5%'><i class='fas fa-cog fa-fw fa-1x'></i></th>";
					$output .= "</tr>";
				$output .= "</thead>";
				$output .= "<tbody id='WorkList'>";
				for($d=1; $d<=$loopday; $d++) {
					$LoopDate = date("Y-m-d",strtotime($year."-".$month."-".$d));
					if(date("w",strtotime($LoopDate)) == 0) { $clsSunday = " text-danger"; } else { $clsSunday = NULL; }
					if($LoopDate == $today) { $clstoday = " class='today'"; } else { $clstoday = NULL; }
					$output .= "<tr$clstoday>";
						if(isset(${$d."_loop"}) != 0) {
							$GetTripSQL = "SELECT 
											T0.RouteEntry, T0.EditStatus, T0.CardCode, T0.CardName, T0.Comments, T0.PlanSale,
											CASE WHEN T0.MeetType IN (1,3) THEN 1 WHEN T0.MeetType IN (2,4) THEN 2 ELSE T0.MeetType END AS 'MeetType',
											T1.Lon, T1.Lat 
										FROM route_planner T0
										LEFT JOIN ocrd T1 ON T0.CardCode = T1.CardCode AND T0.CardCode IS NOT NULL
										WHERE T0.PlanDate = '$LoopDate' AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A' ORDER BY T0.RouteEntry";
							$GetTripQRY = MySQLSelectX($GetTripSQL);
							$r=0;

							$rowspan = " rowspan='".${$d."_loop"}."'";
							$output .= "<td $rowspan class='text-center lead$clsSunday' onclick='TripDate(\"".$LoopDate."\",\"".$user."\")'>$d</td>";
							while($GetTripRST = mysqli_fetch_array($GetTripQRY)) {
								$r++;
								$dis_view = NULL;
								$dis_chck = " disabled";
								switch($GetTripRST['MeetType']) {
									case 0: $MeetType = "<span class='badge w-100 p-2 text-white MeetType0'><i class='fas fa-clock fa-fw fa-1x'></i> รอเข้าพบ</span>";  $dis_view = " disabled"; $dis_chck = NULL; break;
									case 1: $MeetType = "<span class='badge w-100 p-2 text-white MeetType1'><i class='fas fa-street-view fa-fw fa-1x'></i> เข้าพบ (ในพื้นที่)</span>"; break;
									case 2: $MeetType = "<span class='badge w-100 p-2 text-white MeetType2'><i class='fas fa-male fa-fw fa-1x'></i> เข้าพบ (นอกพื้นที่)</span>"; break;
									case 5: $MeetType = "<span class='badge w-100 p-2 text-white MeetType5'><i class='fas fa-exclamation-circle fa-fw fa-1x'></i> เข้าพบ (ไม่มีพิกัด)</span>"; break;
									case 6: $MeetType = "<span class='badge w-100 p-2 text-white MeetType6'><i class='fas fa-phone-volume fa-fw fa-1x'></i>โทร / ไลน์</span>"; break;
									case 7: $MeetType = "<span class='badge w-100 p-2 text-white MeetType7'><i class='far fa-star fa-fw fa-1x'></i> ลูกค้ามุ่งหวัง</span>"; $dis_chck = NULL; break;
									case 8: $MeetType = "<span class='badge w-100 p-2 text-white MeetType8'><i class='fas fa-check-circle fa-fw fa-1x'></i> เข้าพบลูกค้ามุ่งหวัง</span>"; break;
								}
								if($GetTripRST['Lon'] == null && $GetTripRST['Lat'] == null) {
									$dis_navi = " disabled";
								} else {
									$dis_navi = NULL;
								}
								if($GetTripRST['EditStatus'] == "N") {
									$dis_edit = " disabled";
								} else {
									$dis_edit = NULL;
								}
								if($GetTripRST['CardCode'] == NULL || $GetTripRST['CardCode'] == "NULL") {
									$ShowCard = "<i class='far fa-star fa-fw fa-1x'></i> ".$GetTripRST['CardName'];
									$Teritory = "-";
									$PlanSale = "-";
									$DocTotal = "-";
									$OpenIV   = "-";
								} else {
									$CardDetail = GetCardDetail($GetTripRST['CardCode']);
									$ShowCard = $GetTripRST['CardCode']." ".$GetTripRST['CardName'];
									$Teritory = $CardDetail[0];
									$OpenIV   = "<a href='javascript:void(0);' onclick='GetOpenIV(\"".$GetTripRST['CardCode']."\");'>".$CardDetail[1]."</a>";
									$DocTotal = GetDocTotal($year,$month,$GetTripRST['CardCode']);
									$PlanSale = number_format($GetTripRST['PlanSale'],2);
									
								}
								$btn  = "<div class='dropdown'>";
									$btn .= "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
										$btn .= "<i class='fas fa-cog fa-fw fa-1x'></i>";
									$btn .= "</button>";
									$btn .= "<ul class='dropdown-menu dropdown-menu-end' style='font-size: 13px;'>";
										$btn .= "<li><a class='dropdown-item$dis_edit' href='javascript:void(0);' onclick='EditTrip(".$GetTripRST['RouteEntry'].")'><i class='fas fa-edit fa-fw fa-lg'></i> แก้ไขแผนงาน</a></li>";
										$btn .= "<li><a class='dropdown-item$dis_navi' href='https://maps.google.com/?q=".$GetTripRST['Lat'].",".$GetTripRST['Lon']."' target='_blank'><i class='fas fa-directions fa-fw fa-lg text-success'></i> นำทาง</a></li>";
										$btn .= "<li><a class='dropdown-item$dis_chck' href='javascript:void(0);' onclick='CheckIn(\"".$GetTripRST['CardCode']."\",".$GetTripRST['RouteEntry'].")'><i class='fas fa-map-marker-alt fa-fw fa-lg text-primary'></i> เช็คอิน</a></li>";
										$btn .= "<li><a class='dropdown-item$dis_view' href='javascript:void(0);' onclick='CheckInReport(".$GetTripRST['RouteEntry'].")'><i class='fas fa-file-alt fa-fw fa-lg'></i> รายงานการเข้าพบ</a></li>";
									$btn .= "</ul>";
								$btn .= "</div>";
								if($r == 1) {
									$output .= "<td class='text-center'>$r</td>";
									$output .= "<td>$ShowCard</td>";
									$output .= "<td>$Teritory</td>";
									$output .= "<td>".$GetTripRST['Comments']."</td>";
									$output .= "<td class='text-right'>".$PlanSale."</td>";
									$output .= "<td class='text-right'>".$DocTotal."</td>";
									$output .= "<td class='text-right'>".$OpenIV."</td>";
									$output .= "<td class='text-center'>$MeetType</td>";
									$output .= "<td class='text-center'>$btn</td>";
									$output .= "</tr>";
								} else {
									$output .= "<tr$clstoday>";
									$output .= "<td class='text-center'>$r</td>";
									$output .= "<td>$ShowCard</td>";
									$output .= "<td>$Teritory</td>";
									$output .= "<td>".$GetTripRST['Comments']."</td>";
									$output .= "<td class='text-right'>".$PlanSale."</td>";
									$output .= "<td class='text-right'>".$DocTotal."</td>";
									$output .= "<td class='text-right'>".$OpenIV."</td>";
									$output .= "<td class='text-center'>$MeetType</td>";
									$output .= "<td class='text-center'>$btn</td>";
									$output .= "</tr>";
								}
							}
						} else {
							$rowspan = NULL;
							$output .= "<td class='text-center lead$clsSunday'>$d</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
						}
					$output .= "</tr>";
				}
				$output .= "</tbody>";
			$output .= "</table>";
		$output .= "</div>";

	} else {
		$AllPlanSale = 0;
		$AllDocTotal = 0; 
		$AllOpenIV = 0;
		for($d = 1; $d < date("d"); $d++) {
			${$d."_loop"} = 0;
		}
		$MeetTotalSQL = 
			"SELECT
				DAY(T0.CreateDate) AS 'Day', COUNT(T0.CreateDate) AS 'MeetTotal'
			FROM route_checkin T0
			LEFT JOIN route_planner T1 ON T1.RouteEntry = T0.RouteEntry 
			WHERE (YEAR(T0.CreateDate) = $year AND MONTH(T0.CreateDate) = $month AND DAY(T0.CreateDate) < ".date("d").") AND T0.CreateUkey = '$user' AND T1.DocStatus = 'A'
			GROUP BY DAY(T0.CreateDate)";
		$MeetTotalQRY = MySQLSelectX($MeetTotalSQL);
		while($MeetTotalRST = mysqli_fetch_array($MeetTotalQRY)) {
			${$MeetTotalRST['Day']."_loop"} = $MeetTotalRST['MeetTotal'];
		}
		
		$output .= "<div class='table-responsive mt-2'>";
			$output .= "<table class='table table-bordered table-sm' style='font-size: 12px;'>";
				$output .= "<thead class='text-center'>";
					$output .= "<tr>";
						$output .= "<th width='7.5%'>วันที่</th>";
						$output .= "<th width='5%'>เวลาเช็คอิน</th>";
						$output .= "<th width='20%'>ชื่อร้านค้า</th>";
						$output .= "<th>ที่อยู่</th>";
						$output .= "<th width='7.5%'>ประมาณการยอดขาย<br/>(บาท)</th>";
						$output .= "<th width='7.5%'>ยอดขาย<br/>(บาท)</th>";
						$output .= "<th width='7.5%'>บิลรอเรียกเก็บ<br/>(บาท)</th>";
						$output .= "<th width='10%'>ผลการเข้าพบ</th>";
						$output .= "<th width='10%'>สถานะ</th>";
						$output .= "<th width='5%'><i class='fas fa-cog fa-fw fa-1x'></i></th>";
					$output .= "</tr>";
				$output .= "</thead>";
				$output .= "<tbody id='WorkList'>";
				for($d=1; $d<=$loopday; $d++) {
					$LoopDate = date("Y-m-d",strtotime($year."-".$month."-".$d));
					if(date("w",strtotime($LoopDate)) == 0) { $clsSunday = " text-danger"; } else { $clsSunday = NULL; }
					if($LoopDate == $today) { $clstoday = " class='today'"; } else { $clstoday = NULL; }
					$output .= "<tr$clstoday>";
						if(isset(${$d."_loop"})) {
							if(${$d."_loop"} != 0) {
								if($d < intval(date("d"))) {
									$GetTripSQL = 
										"SELECT 
											T0.RouteEntry, T1.EditStatus, T1.CardCode, T1.CardName, T1.Comments, T1.PlanSale, 
											CASE WHEN T1.MeetType IN (1,3) THEN 1 WHEN T1.MeetType IN (2,4) THEN 2 ELSE T1.MeetType END AS 'MeetType',
											T0.plan_lon AS 'Lon', T0.plan_lat AS 'Lat', T0.CreateDate, T0.ChkReport
										FROM route_checkin T0
										LEFT JOIN route_planner T1 ON T1.RouteEntry = T0.RouteEntry 
										WHERE DATE(T0.CreateDate) = '$LoopDate' AND T0.CreateUkey = '$user' AND T1.DocStatus = 'A'
										ORDER BY T0.CreateDate";
								}else{
									$GetTripSQL = 
										"SELECT 
											T0.RouteEntry, T0.EditStatus, T0.CardCode, T0.CardName, T0.Comments, T0.PlanSale,
											CASE WHEN T0.MeetType IN (1,3) THEN 1 WHEN T0.MeetType IN (2,4) THEN 2 ELSE T0.MeetType END AS 'MeetType',
											T1.Lon, T1.Lat, '' AS 'CreateDate', '' AS 'ChkReport'
										FROM route_planner T0
										LEFT JOIN ocrd T1 ON T0.CardCode = T1.CardCode AND T0.CardCode IS NOT NULL
										WHERE T0.PlanDate = '$LoopDate' AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A' ORDER BY T0.RouteEntry";
								}
								$GetTripQRY = MySQLSelectX($GetTripSQL);
								$r=0;
	
								$rowspan = " rowspan='".${$d."_loop"}."'";
								$output .= "<td $rowspan class='text-center lead$clsSunday' onclick='TripDate(\"".$LoopDate."\",\"".$user."\")'>$d</td>";
								while($GetTripRST = mysqli_fetch_array($GetTripQRY)) {
									$r++;
									$TimeChk = ($GetTripRST['CreateDate'] != '') ? date("H:i", strtotime($GetTripRST['CreateDate']))." น." : "";
									$dis_view = NULL;
									$dis_chck = " disabled";
									switch($GetTripRST['MeetType']) {
										case 0: $MeetType = "<span class='badge w-100 p-2 text-white MeetType0'><i class='fas fa-clock fa-fw fa-1x'></i> รอเข้าพบ</span>";  $dis_view = " disabled"; $dis_chck = NULL; break;
										case 1: $MeetType = "<span class='badge w-100 p-2 text-white MeetType1'><i class='fas fa-street-view fa-fw fa-1x'></i> เข้าพบ (ในพื้นที่)</span>"; break;
										case 2: $MeetType = "<span class='badge w-100 p-2 text-white MeetType2'><i class='fas fa-male fa-fw fa-1x'></i> เข้าพบ (นอกพื้นที่)</span>"; break;
										case 5: $MeetType = "<span class='badge w-100 p-2 text-white MeetType5'><i class='fas fa-exclamation-circle fa-fw fa-1x'></i> เข้าพบ (ไม่มีพิกัด)</span>"; break;
										case 6: $MeetType = "<span class='badge w-100 p-2 text-white MeetType6'><i class='fas fa-phone-volume fa-fw fa-1x'></i>โทร / ไลน์</span>"; break;
										case 7: $MeetType = "<span class='badge w-100 p-2 text-white MeetType7'><i class='far fa-star fa-fw fa-1x'></i> ลูกค้ามุ่งหวัง</span>"; $dis_chck = NULL; break;
										case 8: $MeetType = "<span class='badge w-100 p-2 text-white MeetType8'><i class='fas fa-check-circle fa-fw fa-1x'></i> เข้าพบลูกค้ามุ่งหวัง</span>"; break;
									}
									if($GetTripRST['Lon'] == null && $GetTripRST['Lat'] == null) {
										$dis_navi = " disabled";
									} else {
										$dis_navi = NULL;
									}
									if($GetTripRST['EditStatus'] == "N") {
										$dis_edit = " disabled";
									} else {
										$dis_edit = NULL;
									}
									if($GetTripRST['CardCode'] == NULL || $GetTripRST['CardCode'] == "NULL") {
										$ShowCard = "<i class='far fa-star fa-fw fa-1x'></i> ".$GetTripRST['CardName'];
										$Teritory = "-";
										$DocTotal = "-";
										$OpenIV   = "-";
										$PlanSale = "-";
									} else {
										$CardDetail = GetCardDetailTRUE($GetTripRST['CardCode']);
										$ShowCard = $GetTripRST['CardCode']." ".$GetTripRST['CardName'];
										$Teritory = $CardDetail[0];
										$OpenIV   = "<a href='javascript:void(0);' onclick='GetOpenIV(\"".$GetTripRST['CardCode']."\");'>".$CardDetail[1]."</a>";
										$DocTotal = GetDocTotal($year,$month,$GetTripRST['CardCode']);
										$PlanSale = number_format($GetTripRST['PlanSale'],2);
										$AllPlanSale = $AllPlanSale+$GetTripRST['PlanSale'];
										$AllDocTotal = $AllDocTotal+floatval(GetDocTotalInt($year,$month,$GetTripRST['CardCode'])); 
										$AllOpenIV = $AllOpenIV+floatval(GetCardDetailTRUEInt($GetTripRST['CardCode']));
									}
									$btn  = "<div class='dropdown'>";
										$btn .= "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
											$btn .= "<i class='fas fa-cog fa-fw fa-1x'></i>";
										$btn .= "</button>";
										$btn .= "<ul class='dropdown-menu dropdown-menu-end' style='font-size: 13px;'>";
											$btn .= "<li><a class='dropdown-item$dis_edit' href='javascript:void(0);' onclick='EditTrip(".$GetTripRST['RouteEntry'].")'><i class='fas fa-edit fa-fw fa-lg'></i> แก้ไขแผนงาน</a></li>";
											$btn .= "<li><a class='dropdown-item$dis_navi' href='https://maps.google.com/?q=".$GetTripRST['Lat'].",".$GetTripRST['Lon']."' target='_blank'><i class='fas fa-directions fa-fw fa-lg text-success'></i> นำทาง</a></li>";
											$btn .= "<li><a class='dropdown-item$dis_chck' href='javascript:void(0);' onclick='CheckIn(\"".$GetTripRST['CardCode']."\",".$GetTripRST['RouteEntry'].")'><i class='fas fa-map-marker-alt fa-fw fa-lg text-primary'></i> เช็คอิน</a></li>";
											$btn .= "<li><a class='dropdown-item$dis_view' href='javascript:void(0);' onclick='CheckInReport(".$GetTripRST['RouteEntry'].")'><i class='fas fa-file-alt fa-fw fa-lg'></i> รายงานการเข้าพบ</a></li>";
										$btn .= "</ul>";
									$btn .= "</div>";
									if($r == 1) {
										$output .= "<td class='text-center'>$TimeChk</td>";
										$output .= "<td>$ShowCard</td>";
										$output .= "<td>$Teritory</td>";
										$output .= "<td class='text-right'>".$PlanSale."</td>";
										$output .= "<td class='text-right'>".$DocTotal."</td>";
										$output .= "<td class='text-right'>".$OpenIV."</td>";
										$output .= "<td>".$GetTripRST['ChkReport']."</td>";
										$output .= "<td class='text-center'>$MeetType</td>";
										$output .= "<td class='text-center'>$btn</td>";
										$output .= "</tr>";
										
									} else {
										$output .= "<tr$clstoday>";
										$output .= "<td class='text-center'>$TimeChk</td>";
										$output .= "<td>$ShowCard</td>";
										$output .= "<td>$Teritory</td>";
										$output .= "<td class='text-right'>".$PlanSale."</td>";
										$output .= "<td class='text-right'>".$DocTotal."</td>";
										$output .= "<td class='text-right'>".$OpenIV."</td>";
										$output .= "<td>".$GetTripRST['ChkReport']."</td>";
										$output .= "<td class='text-center'>$MeetType</td>";
										$output .= "<td class='text-center'>$btn</td>";
										$output .= "</tr>";
										
									}
								}
							}else{
								$rowspan = NULL;
								$output .= "<td class='text-center lead$clsSunday'>$d</td>";
								$output .= "<td>&nbsp;</td>";
								$output .= "<td>&nbsp;</td>";
								$output .= "<td>&nbsp;</td>";
								$output .= "<td>&nbsp;</td>";
								$output .= "<td>&nbsp;</td>";
								$output .= "<td>&nbsp;</td>";
								$output .= "<td>&nbsp;</td>";
								$output .= "<td>&nbsp;</td>";
								$output .= "<td>&nbsp;</td>";
							}
						} else {
							$rowspan = NULL;
							$output .= "<td class='text-center lead$clsSunday'>$d</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
							$output .= "<td>&nbsp;</td>";
						}
					$output .= "</tr>";
		

				}
				$output .= "<tr>";
				$output .= "<th colspan='4' class='text-right'>รวม</th>";
				$output .= "<th class='text-right'>".number_format($AllPlanSale,2)."</th>";
				$output .= "<th class='text-right text-success'>".number_format($AllDocTotal,2)."</th>";
				$output .= "<th class='text-right text-danger'>".number_format($AllOpenIV,2)."</th>";
				$output .= "<td colspan='4'>&nbsp;</td>";
				$output .= "</tr>";
	}

	$arrCol['view_worktrip'] = $output;
}

if($_GET['p'] == "GetGPS") {
	$CardCode = $_POST['CardCode'];
	/* GET GPS */
	$GPSSQL = "SELECT T0.CardCode, T0.CardName, T0.Lon, T0.Lat FROM OCRD T0 WHERE T0.CardCode = '$CardCode' LIMIT 1";
	$GPSROW = ChkRowDB($GPSSQL);
	if($GPSROW == 0) {
		$arrCol['geo_lon']  = null;
		$arrCol['geo_lat']  = null;
		$arrCol['geo_cardcode'] = null;
		$arrCol['geo_cardname'] = null;
	} else {
		$GPSRST = MySQLSelect($GPSSQL);
		$arrCol['geo_lon']  = $GPSRST['Lon'];
		$arrCol['geo_lat']  = $GPSRST['Lat'];
		$arrCol['geo_cardcode'] = $GPSRST['CardCode'];
		$arrCol['geo_cardname'] = $GPSRST['CardName'];
	}
	/* GET MONTHLY TARGET */
	$Year  = date("Y",strtotime($_POST['PlanDate']));
	$Month = date("m",strtotime($_POST['PlanDate']));
	$TARSQL = "SELECT T0.CardCode, T0.CusTarget FROM custarget T0 WHERE T0.CardCode = '$CardCode' AND T0.TgrStatus = 'A' AND T0.DocYear = '$Year' LIMIT 1";
	$TARROW = ChkRowDB($TARSQL);
	if($TARROW == 0) {
		$Target = 0;
	} else {
		$TARRST = MySQLSelect($TARSQL);
		$Target = (round($TARRST['CusTarget'],0)/12);
	}
	$arrCol['cus_target'] = $Target;
	/* GET MONTHLY PLAN */
	$PLNSQL = "SELECT T0.DetailPlan FROM route_action T0 WHERE (T0.CardCode = '$CardCode' AND T0.DocStatus = 'A' AND (T0.plan_year = $Year AND T0.plan_month = $Month)) ORDER BY T0.SurveyID DESC LIMIT 1";
	$PLNROW = ChkRowDB($PLNSQL);
	if($PLNROW == 0) {
		$PlanDetail = null;
	} else {
		$PLNRST = MySQLSelect($PLNSQL);
		$PlanDetail = $PLNRST['DetailPlan'];
	}
	$arrCol['cus_Plan'] = $PlanDetail;
}

if($_GET['p'] == "AddTrip") {
	$PlanDate    = $_POST['PlanDate'];
	$PlanRemark  = $_POST['PlanRemark'];
	$PlanSale    = $_POST['PlanSale'];
	$CreateUkey  = $_SESSION['ukey'];
	/* ถ้าไม่มี CardCode => CardCode = NULL ส่วน CardName = NewCardCode แต่ถ้ามี CardCode = CardCode และ CardName = CardName */
	if($_POST['RouteEntry'] == 0) {
		if(!isset($_POST['CardCode'])) {
			$CardCode = "NULL";
			$CardName = "'".$_POST['NewCardCode']."'";
			$MeetType = 7;
		} else {
			$CardCode = "'".$_POST['CardCode']."'";
			$CardNameSQL = "SELECT T0.CardName FROM OCRD T0 WHERE T0.CardCode = '".$_POST['CardCode']."' LIMIT 1";
			// echo $CardNameSQL;
			$CardNameRST = MySQLSelect($CardNameSQL);
			$CardName = "'".$CardNameRST['CardName']."'";
			$MeetType = 0;
		}
	}

	if($_POST['RouteEntry'] == 0) {
		$InsertSQL = "INSERT INTO route_planner SET PlanDate = '$PlanDate', PlanSale = '$PlanSale', CardCode = $CardCode, CardName = $CardName, Comments = '$PlanRemark', MeetType = $MeetType, CreateUkey = '$CreateUkey';";
		// echo $InsertSQL;
		MySQLInsert($InsertSQL);
	} else {
		/* UPDATE */
		$UpdateSQL = "UPDATE route_planner SET EditStatus = 'N', PlanDate = '$PlanDate', PlanSale = '$PlanSale', Comments = '$PlanRemark', MeetType = 0, UpdateUkey = '$CreateUkey' WHERE RouteEntry = '".$_POST['RouteEntry']."';";
		// echo $UpdateSQL;
		MySQLUpdate($UpdateSQL);
	}

	// ChkActual
	if(isset($_POST['CardCode'])) {
		$y = date("Y",strtotime($PlanDate));
		$m = date("m",strtotime($PlanDate));
		$SurSQL = "SELECT T0.SurveyID, T0.DetailPlan, T0.DetailActual FROM route_action T0 WHERE T0.CardCode = '$CardCode' AND (T0.plan_year = $y AND T0.plan_month = $m) AND T0.DocStatus = 'A' LIMIT 1";
		$SurROW = ChkRowDB($SurSQL);
		if($SurROW > 0) {
			/* UPDATE AND INSERT */
			$SurRST       = MySQLSelect($SurSQL);
			$SurveyID     = $SurRST['SurveyID'];
			$DetailActual = $SurRST['DetailActual'];

			$UpdateSQL = "UPDATE route_action SET DocStatus = 'I' WHERE SurveyID = $SurveyID";
			MySQLUpdate($UpdateSQL);
			$InsertSQL = "INSERT INTO route_action SET CardCode = '$CardCode', plan_year = $y, plan_month = $m, DetailPlan = '$PlanRemark', DetailActual = '$DetailActual', CreateUkey = '$CreateUkey', CreateDate = NOW(), DocStatus = 'A'";
			MySQLInsert($InsertSQL);

		} else {
			$InsertSQL = "INSERT INTO route_action SET CardCode = '$CardCode', plan_year = $y, plan_month = $m, DetailPlan = '$PlanRemark', CreateUkey = '$CreateUkey', CreateDate = NOW(), DocStatus = 'A'";
			MySQLInsert($InsertSQL);
		}
	}
}

if($_GET['p'] == "GetAgenda") {
	$user = $_POST['u'];
	$date = $_POST['d'];
	$month = date("m",strtotime($_POST['d']));
	$year = date("Y",strtotime($_POST['d']));
	$output = "";
	$AgendaSQL = "SELECT
					T0.RouteEntry, T0.PlanDate, T0.CardCode, T0.CardName, T0.Comments, T0.CreateUkey, T0.PlanSale,
					T1.Lon, T1.Lat,
					CASE WHEN T0.MeetType IN (1,3) THEN 1 WHEN T0.MeetType IN (2,4) THEN 2 ELSE T0.MeetType END AS 'MeetType',
					T0.EditStatus, T0.CreateUkey, DATEDIFF(T0.PlanDate, NOW()) AS DIFF
				FROM route_planner T0
				LEFT JOIN ocrd T1 ON T0.CardCode = T1.CardCode AND T0.CardCode IS NOT NULL
				WHERE T0.PlanDate = '$date' AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A' ORDER BY T0.RouteEntry";
	$Rows = ChkRowDB($AgendaSQL);
	if($Rows == 0) {
		$output .= "<tr><td class='text-center text-muted' colspan='7'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$AgendaQRY = MySQLSelectX($AgendaSQL);
		$no = 0;
		while($AgendaRST = mysqli_fetch_array($AgendaQRY)) {
			$no++;
			$dis_view = NULL;
			$dis_chck = " disabled";
			switch($AgendaRST['MeetType']) {
				case 0: $MeetType = "<span class='badge w-100 p-2 text-white text-center MeetType0'><i class='fas fa-clock fa-fw fa-1x'></i></span>"; $dis_view = " disabled"; if($AgendaRST['CreateUkey'] == $_SESSION['ukey']) { $dis_chck = NULL; } break;
				case 1: $MeetType = "<span class='badge w-100 p-2 text-white text-center MeetType1'><i class='fas fa-street-view fa-fw fa-1x'></i></span>"; break;
				case 2: $MeetType = "<span class='badge w-100 p-2 text-white text-center MeetType2'><i class='fas fa-male fa-fw fa-1x'></i></span>"; break;
				case 5: $MeetType = "<span class='badge w-100 p-2 text-white text-center MeetType5'><i class='fas fa-exclamation-circle fa-fw fa-1x'></i></span>"; break;
				case 6: $MeetType = "<span class='badge w-100 p-2 text-white text-center MeetType6'><i class='fas fa-phone-volume fa-fw fa-1x'></i></span>"; break;
				case 7: $MeetType = "<span class='badge w-100 p-2 text-white text-center MeetType7'><i class='far fa-star fa-fw fa-1x'></i></span>"; if($AgendaRST['CreateUkey'] == $_SESSION['ukey']) { $dis_chck = NULL; } break;
				case 8: $MeetType = "<span class='badge w-100 p-2 text-white text-center MeetType8'><i class='fas fa-check-circle fa-fw fa-1x'></i></span>"; break;
			}
			if($AgendaRST['Lon'] == null && $AgendaRST['Lat'] == null) {
				$dis_navi = " disabled";
			} else {
				$dis_navi = NULL;
			}
			if($AgendaRST['EditStatus'] == "N") {
				$dis_edit = " disabled";
			} else {
				$dis_edit = NULL;
			}

			if($AgendaRST['DIFF'] > 0 && $AgendaRST['CreateUkey'] == $_SESSION['ukey']) {
				$btn_del = "<li><a class='dropdown-item' href='javascript:void(0);' onclick='DeleteTrip(".$AgendaRST['RouteEntry'].",\"".$date."\",\"".$user."\")'><i class='fas fa-trash fa-fw fa-lg text-danger'></i> ลบรายการเข้าพบ</a></li>";
			}else{
				$btn_del = "";
			}
			
			if($AgendaRST['CardCode'] == NULL) {
				$ShowCard = "<i class='far fa-star fa-fw fa-1x'></i> ".$AgendaRST['CardName'];
				$OpenIV   = "-";
				$DocTotal = "-";
			} else {
				$CardDetail = GetCardDetail($AgendaRST['CardCode']);
				$ShowCard = $AgendaRST['CardCode']." ".$AgendaRST['CardName'];
				$OpenIV   = "<a href='javascript:void(0);' onclick='GetOpenIV(\"".$AgendaRST['CardCode']."\");'>".$CardDetail[1]."</a>";
				$DocTotal = GetDocTotal($year,$month,$AgendaRST['CardCode']);
				
			}
			$output .= "<tr>";
				$output .= "<td class='text-right'>$no</td>";
				$output .= "<td>".$ShowCard."</td>";
				$output .= "<td>".$AgendaRST['Comments']."</td>";
				$output .= "<td class='text-right'>".number_format($AgendaRST['PlanSale'],0)."</td>";
				$output .= "<td class='text-right'>$DocTotal</td>";
				$output .= "<td class='text-right'>$OpenIV</td>";
				$output .= "<td>$MeetType</td>";
				// $output .= "<td class='text-center'>".$GPS."</td>";
				// $output .= "<td class='text-center'><a class='btn btn-primary btn-sm w-100' href='javascript:void(0);' onclick='CheckIn(".$AgendaRST['RouteEntry'].");'><i class='fas fa-map-marker-alt fa-fw fa-1x'></i></a></td>";
				$output .= "<td class='text-center'>";
					$output .= "<div class='dropdown'>";
						$output .= "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
							$output .= "<i class='fas fa-cog fa-fw fa-1x'></i>";
						$output .= "</button>";
						$output .= "<ul class='dropdown-menu dropdown-menu-end' style='font-size: 13px;'>";
							$output .= "<li><a class='dropdown-item$dis_edit' href='javascript:void(0);' onclick='EditTrip(".$AgendaRST['RouteEntry'].")'><i class='fas fa-edit fa-fw fa-lg'></i> แก้ไขแผนงาน</a></li>";
							$output .= "<li><a class='dropdown-item$dis_navi' href='https://maps.google.com/?q=".$AgendaRST['Lat'].",".$AgendaRST['Lon']."' target='_blank'><i class='fas fa-directions fa-fw fa-lg text-success'></i> นำทาง</a></li>";
							$output .= "<li><a class='dropdown-item$dis_chck' href='javascript:void(0);' onclick='CheckIn(\"".$AgendaRST['CardCode']."\",".$AgendaRST['RouteEntry'].")'><i class='fas fa-map-marker-alt fa-fw fa-lg text-primary'></i> เช็คอิน</a></li>";
							$output .= "<li><a class='dropdown-item$dis_view' href='javascript:void(0);' onclick='CheckInReport(".$AgendaRST['RouteEntry'].")'><i class='fas fa-file-alt fa-fw fa-lg'></i> รายงานการเข้าพบ</a></li>";
							$output .= $btn_del;
						$output .= "</ul>";
					$output .= "</div>";
				$output .= "</td>";
			$output .= "</tr>";
			
		}
	}
	$arrCol['view_agenda'] = $output;
	$arrCol['view_date']   = date("d/m/Y",strtotime($date));
}

if($_GET['p'] == 'DeleteTrip') {
	$RouteEntry = $_POST['RouteEntry'];
	$UpdateSQL = "UPDATE route_planner SET DocStatus = 'I' WHERE RouteEntry = $RouteEntry";
	MySQLUpdate($UpdateSQL);
}

if($_GET['p'] == "CopyTrip") {
	$from_m = $_POST['copy_from_m'];
	$from_y = $_POST['copy_from_y'];
	$to_m   = $_POST['copy_to_m'];
	$to_y   = $_POST['copy_to_y'];
	$user   = $_POST['u'];

	$ChkSQL = "SELECT T0.RouteEntry 
				FROM route_planner T0 
				WHERE (YEAR(T0.PlanDate) = $to_y AND MONTH(T0.PlanDate) = $to_m) AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A'
				ORDER BY T0.RouteEntry";
	$Rows = ChkRowDB($ChkSQL);
	if($Rows == 0) {
		$OriginSQL = 
				"SELECT
					T0.RouteEntry, T0.PlanDate, T0.CardCode, T0.CardName, T0.Comments,
					IFNULL((T1.CusTarget/12), IFNULL(T0.PlanSale,0)) AS 'PlanSale'
				FROM route_planner T0
				LEFT JOIN custarget T1 ON T0.CardCode = T1.CardCode AND T1.DocYear = $from_y AND T1.TgrStatus = 'A'
				WHERE (YEAR(T0.PlanDate) = $from_y AND MONTH(T0.PlanDate) = $from_m) AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A'
				ORDER BY T0.RouteEntry";
		$OriginQRY = MySQLSelectX($OriginSQL);
		// echo $OriginSQL;
		while($OriginRST = mysqli_fetch_array($OriginQRY)) {
			$getOriDay = date("d",strtotime($OriginRST['PlanDate']));
			$NewDate   = date("Y-m-d",strtotime($to_y."-".$to_m."-".$getOriDay));
			if($OriginRST['CardCode'] == NULL) {
				$CardCode = "NULL";
				$MeetType = 7;
			} else {
				$CardCode = "'".$OriginRST['CardCode']."'";
				$MeetType = 0;
			}
			if($OriginRST['Comments'] == NULL) {
				$Comments = "NULL";
			} else {
				$Comments = $OriginRST['Comments'];
			}
			if($OriginRST['PlanSale'] == NULL) {
				$PlanSale = "NULL";
			} else {
				$PlanSale = "'".$OriginRST['PlanSale']."'";
			}
			$InsertSQL = "INSERT INTO route_planner SET EditStatus = 'Y', PlanDate = '$NewDate', PlanSale = $PlanSale, CardCode = $CardCode, CardName = '".$OriginRST['CardName']."', Comments = '$Comments', CreateUkey = '$user', MeetType = $MeetType;";
			//echo $InsertSQL."<br/>";
			MySQLInsert($InsertSQL);
		}
		$arrCol['copy_status'] = "SUCCESS";
	} else {
		$arrCol['copy_status'] = "ERROR";
	}
}

if($_GET['p'] == "HookTrip") {
	$HookSQL = "SELECT
					T0.RouteEntry, T0.CardCode, T0.CardName, T0.PlanDate, T0.Comments, T1.Lon, T1.Lat
				FROM route_planner T0
				LEFT JOIN ocrd T1 ON T0.CardCode = T1.CardCode AND T0.CardCode IS NOT NULL
				WHERE T0.RouteEntry = '".$_POST['RID']."' LIMIT 1;";
	$HookRST = MySQLSelect($HookSQL);

	if($HookRST['CardCode'] == NULL) {
		$CardCode = NULL;
		$CardName = $HookRST['CardName'];
	} else {
		$CardCode = $HookRST['CardCode'];
		$CardName = NULL;
	}
	if($HookRST['Lon'] != NULL && $HookRST['Lat'] != NULL) {
		$arrCol['geo_lon']  = $HookRST['Lon'];
		$arrCol['geo_lat']  = $HookRST['Lat'];
	} else {
		$arrCol['geo_lon']  = NULL;
		$arrCol['geo_lat']  = NULL;
	}

	$arrCol['RouteEntry'] = $HookRST['RouteEntry'];
	$arrCol['CardCode']   = $CardCode;
	$arrCol['CardName']   = $CardName;
	$arrCol['PlanDate']   = date("Y-m-d",strtotime($HookRST['PlanDate']));
	$arrCol['Comments']   = $HookRST['Comments'];
}

if($_GET['p'] == "GetIVList") {
	$GetListSQL =
	"SELECT
		'IV' AS 'DocType', (ISNULL(T1.BeginStr,'IV-')+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum',
		T0.DocDate, T0.DocDueDate, DATEDIFF(day, T0.DocDueDate, GETDATE()) AS 'Aging',
		(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal', T0.CardCode, T2.CardName
	FROM OINV T0
	LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
	LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode
	WHERE T0.DocStatus = 'O' AND T0.CardCode = '".$_POST['CardCode']."' AND T0.CANCELED = 'N'
	UNION ALL
	SELECT
		'RE' AS 'DocType', (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum',
		T0.DocDate, T0.DocDueDate, DATEDIFF(day, T0.DocDueDate, GETDATE()) AS 'Aging',
		-(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal', T0.CardCode, T2.CardName
	FROM ORIN T0
	LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
	LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode
	WHERE T0.DocStatus = 'O' AND T1.BeginStr IN ('S1-','SR-') AND T0.CardCode = '".$_POST['CardCode']."' AND T0.CANCELED = 'N'

	ORDER BY 'DocType' ASC, 'Aging' DESC";
	$Rows = ChkRowSAP($GetListSQL);
	if($Rows > 0) {
		$GetListQRY = SAPSelect($GetListSQL);
		$openbill = "";
		$cardcode = "";
		$row = 0;
		while($GetListRST = odbc_fetch_array($GetListQRY)) {
			$cardcode = $GetListRST['CardCode']." ".conutf8($GetListRST['CardName']);
			$row++;
			if($GetListRST['Aging'] < 0) {
				$trclass = NULL;
				$Agingtext = "<span class='text-muted'>ยังไม่ถึงกำหนด</span>";
			} elseif($GetListRST['Aging'] == 0) {
				$trclass = " class='table-success text-success'";
				$Agingtext = "ถึงกำหนดชำระ";
			} elseif($GetListRST['Aging'] >= 1 && $GetListRST['Aging'] <= 30) {
				$trclass = " class='table-warning text-warning'";
				$Agingtext = "+".$GetListRST['Aging'];
			} else {
				$trclass = " class='table-danger text-danger'";
				$Agingtext = "+".$GetListRST['Aging'];
			}
			$openbill .= "<tr$trclass>";
				$openbill .= "<td class='text-center'>$row</td>";
				$openbill .= "<td class='text-center'>".$GetListRST['DocNum']."</td>";
				$openbill .= "<td class='text-center'>".date("d/m/Y",strtotime($GetListRST['DocDate']))."</td>";
				$openbill .= "<td class='text-center'>".date("d/m/Y",strtotime($GetListRST['DocDueDate']))."</td>";
				$openbill .= "<td class='text-right' style='font-weight: bold;'>".number_format($GetListRST['DocTotal'],2)."</td>";
				$openbill .= "<td class='text-center'>".$Agingtext."</td>";
			$openbill .= "</tr>";
		}
	} else {
		$openbill = "<tr><td colspan='6' class='text-center'>ไม่มีข้อมูลบิลรอเรียกเก็บ :)</td></tr>";
		$CardCodeSQL = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE T0.CardCode = '".$_POST['CardCode']."' LIMIT 1";
		$CardCodeRST = MySQLSelect($CardCodeSQL);
		$cardcode = $CardCodeRST['CardCode']." ".$CardCodeRST['CardName'];
	}
	$arrCol['open_cardcode'] = $cardcode;
	$arrCol['open_list']     = $openbill;

}

if($_GET['p'] == "history") {
	$year  = $_POST['y'];
	$month = $_POST['m'];
	$user  = $_POST['u'];
	$today = date("Y-m-d");
	$output = "";
	$HistorySQL = "SELECT 
						T0.RouteEntry, T1.CardCode, T1.CardName, T1.PlanDate, CASE WHEN T1.MeetType IN (1,3) THEN 1 WHEN T1.MeetType IN (2,4) THEN 2 ELSE T1.MeetType END AS 'MeetType', T1.Comments, T1.CreateUkey,
						T0.plan_lon, T0.plan_lat, T0.chk_lon, T0.chk_lat, T0.ChkDistance,
						T0.CreateDate
					FROM route_checkin T0
					LEFT JOIN route_planner T1 ON T0.RouteEntry = T1.RouteEntry
					WHERE YEAR(T0.CreateDate) = $year AND MONTH(T0.CreateDate) = $month AND T0.CreateUkey = '$user' AND T1.DocStatus = 'A'
					ORDER BY T0.CreateDate ASC";
	$Rows = ChkRowDB($HistorySQL);
	if($Rows == 0) {
		$output .= "<tr><td colspan='5' class='text-center'>ไม่มีข้อมูลการเช็คอิน :(</td></tr>";
	} else {

		$HistoryQRY = MySQLSelectX($HistorySQL);
		while($HistoryRST = mysqli_fetch_array($HistoryQRY)) {
			if($HistoryRST['CardCode'] == NULL) {
				$ShowCard = $HistoryRST['CardName'];
			} else {
				$ShowCard = $HistoryRST['CardCode']." ".$HistoryRST['CardName'];
			}
			if($today == date("Y-m-d",strtotime($HistoryRST['CreateDate']))) {
				$trclass = " class='today'";
			} else {
				$trclass = NULL;
			}
			switch($HistoryRST['MeetType']) {
				case 0: $MeetType = "<span class='badge w-100 p-2 text-white MeetType0'><i class='fas fa-clock fa-fw fa-1x'></i> รอเข้าพบ</span>"; $dis_view = " disabled"; break;
				case 1: $MeetType = "<span class='badge w-100 p-2 text-white MeetType1'><i class='fas fa-street-view fa-fw fa-1x'></i> เข้าพบ (ในพื้นที่)</span>"; break;
				case 2: $MeetType = "<span class='badge w-100 p-2 text-white MeetType2'><i class='fas fa-male fa-fw fa-1x'></i> เข้าพบ (นอกพื้นที่)</span>"; break;
				case 5: $MeetType = "<span class='badge w-100 p-2 text-white MeetType5'><i class='fas fa-exclamation-circle fa-fw fa-1x'></i> เข้าพบ (ไม่มีพิกัด)</span>"; break;
				case 6: $MeetType = "<span class='badge w-100 p-2 text-white MeetType6'><i class='fas fa-phone-volume fa-fw fa-1x'></i> โทร / ไลน์</span>"; break;
				case 7: $MeetType = "<span class='badge w-100 p-2 text-white MeetType7'><i class='far fa-star fa-fw fa-1x'></i> ลูกค้ามุ่งหวัง</span>"; break;
				case 8: $MeetType = "<span class='badge w-100 p-2 text-white MeetType8'><i class='fas fa-check-circle fa-fw fa-1x'></i> เข้าพบลูกค้ามุ่งหวัง</span>"; break;
			}
			$output .= "<tr$trclass>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($HistoryRST['CreateDate']))."<br/>เวลา ".date("H:i",strtotime($HistoryRST['CreateDate']))." น.</td>";
				$output .= "<td>$ShowCard</td>";
				$output .= "<td>".$HistoryRST['Comments']."</td>";
				$output .= "<td class='text-center'>$MeetType</td>";
				$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CheckInReport(".$HistoryRST['RouteEntry'].")'><i class='fas fa-file-alt fa-fw fa-1x'></i></a></td>";
			$output .= "</tr>";
		}
	}

	$arrCol['view_history'] = $output;
}

if($_GET['p'] == "RouteReport") {
	$RouteEntry = $_POST['RouteEntry'];
	$ReportSQL = "SELECT
					T1.CardCode, T1.CardName, T1.PlanDate, T0.CreateDate, T1.Comments,
					T0.plan_lon, T0.plan_lat, T0.chk_lon, T0.chk_lat, T0.ChkDistance,T0.ChkReport,
					CASE WHEN T1.MeetType IN (1,3) THEN 1 WHEN T1.MeetType IN (2,4) THEN 2 ELSE T1.MeetType END AS 'MeetType', T1.MeetType AS 'TrueMeetType'
				FROM route_checkin T0
				LEFT JOIN route_planner T1 ON T0.RouteEntry = T1.RouteEntry
				WHERE T0.RouteEntry = $RouteEntry ORDER BY T0.CheckID DESC LIMIT 1";
	$ReportRST = MySQLSelect($ReportSQL);
	
	if($ReportRST['CardCode'] == NULL) {
		$ShowCard = $ReportRST['CardName'];
	} else {
		$ShowCard = $ReportRST['CardCode']." ".$ReportRST['CardName'];
	}
	if((date("Y-m-d",strtotime($ReportRST['PlanDate'])) == date("Y-m-d",strtotime($ReportRST['CreateDate']))) && ($ReportRST['MeetType'] == 3 || $ReportRST['MeetType'] == 4)) {
		$PlanResult = "ตามแผน";
	} elseif((date("Y-m-d",strtotime($ReportRST['PlanDate'])) == date("Y-m-d",strtotime($ReportRST['CreateDate'])))) {
		$PlanResult = "เพิ่มเนื่องจากไม่อยู่ในแผน";
	} else {
		$PlanResult = "นอกแผน";
	}

	if($ReportRST['TrueMeetType'] == 5 || $ReportRST['TrueMeetType'] == 8) {
		$Location = "ไม่มีพิกัด";
	} else {
		if($ReportRST['ChkDistance'] <= 5.00) {
			$Location = "เช็คอินจากในพื้นที่ (ห่างจากร้านค้า ".number_format($ReportRST['ChkDistance'],2)." กม.)";
		} else {
			if($ReportRST['TrueMeetType'] == 6) {
				$Location = "เช็คอินจากนอกพื้นที่ด้วยการโทร / ไลน์";
			} else {
				$Location = "เช็คอินจากนอกพื้นที่ (ห่างจากร้านค้า ".number_format($ReportRST['ChkDistance'],2)." กม.)";
			}
			
		}
	}

	$arrCol['CardName']    = $ShowCard;
	$arrCol['PlanDate']    = date("d/m/Y",strtotime($ReportRST['PlanDate']));
	$arrCol['ChckDate']    = date("d/m/Y",strtotime($ReportRST['CreateDate']))." เวลา ".date("H:i",strtotime($ReportRST['CreateDate']))." น. (".$PlanResult.")";
	$arrCol['Comments']    = $ReportRST['Comments'];
	$arrCol['plan_lon']    = $ReportRST['plan_lon'];
	$arrCol['plan_lat']    = $ReportRST['plan_lat'];
	$arrCol['chk_lon']     = $ReportRST['chk_lon'];
	$arrCol['chk_lat']     = $ReportRST['chk_lat'];
	$arrCol['ChkDistance'] = $Location;
	$arrCol['ReportCHK'] = $ReportRST['ChkReport'];
	$arrCol['CHKEntry'] = $RouteEntry;
	$arrCol['Disabled_ReportCHK'] = (date("Y-m",strtotime($ReportRST['PlanDate'])) == date("Y-m")) ? "Y" : "N";
}
if($_GET['p'] == "Addtreport") {
	$RouteEntry = $_POST['RouteEntry'];
	$ReportCHK = $_POST['ReportCHK'];
	$SQL1 = "UPDATE route_checkin SET ChkReport = '$ReportCHK' WHERE RouteEntry = $RouteEntry";
	//echo $SQL1;
	MySQLUpdate($SQL1);


}
/*-------------------- CHECK IN --------------------*/
if($_GET['p'] == "GetLocation") {
	$RouteEntry = $_POST['RouteEntry'];
	$CardCode   = $_POST['CardCode'];

	if(($CardCode == NULL || $CardCode == "") && ($RouteEntry != NULL || $RouteEntry != "")) {
		$GetLocSQL = "SELECT T0.CardCode, T0.CardName, T1.Lon, T1.Lat FROM route_planner T0 LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode AND T0.CardCode IS NOT NULL WHERE RouteEntry = $RouteEntry LIMIT 1";
	} else {
		$GetLocSQL = "SELECT T0.CardCode, T0.CardName, T0.Lon, T0.Lat FROM OCRD T0 WHERE T0.CardCode = '$CardCode' LIMIT 1";
	}
	$GetLocRST = MySQLSelect($GetLocSQL);
	if($GetLocRST['CardCode'] == NULL || $GetLocRST['CardCode'] == "NULL") {
		$ShowCard = $GetLocRST['CardName'];
	} else {
		$ShowCard = $GetLocRST['CardCode']." ".$GetLocRST['CardName'];
	}
	$arrCol['plan_customer'] = $ShowCard;
	$arrCol['plan_cardcode'] = $GetLocRST['CardCode'];
	$arrCol['plan_lon'] = $GetLocRST['Lon'];
	$arrCol['plan_lat'] = $GetLocRST['Lat'];
}

if($_GET['p'] == "AddCheckIn") {
	$RouteEntry  = $_POST['RouteEntry'];
	$ChkCardCode = $_POST['ChkCardCode'];
	$ChkLon      = $_POST['ChkLon'];
	$ChkLat      = $_POST['ChkLat'];
	$PlanLon     = $_POST['PlanLon'];
	$PlanLat     = $_POST['PlanLat'];
	$ChkDistance = $_POST['ChkDistance'];
	$CheckType   = $_POST['CheckType'];
	$CreateUkey  = $_SESSION['ukey'];

	/* MeetType
		0 = รอเช็คอิน
		1 = เช็คอิน ในแผน ในพื้นที่
		2 = เช็คอิน ในแผน นอกพื้นที่
		3 = เช็คอิน นอกแผน ในพื้นที่
		4 = เช็คอิน นอกแผน นอกพื้นที่
		5 = เช็คอิน ไม่มีพิกัด
		6 = เช็คอิน (โทร/ไลน์)
		7 = ลูกค้ามุ่งหวัง
		8 = ลูกค้ามุ่งหวัง (เช็คอิน)
	*/

	if(($PlanLon == "" && $PlanLat == "") || ($PlanLon == "0" && $PlanLat == "0")) {
		$CusLon = "NULL";
		$CusLat = "NULL";
		$Distance = "NULL";
	} else {
		$CusLon = "'$PlanLon'";
		$CusLat = "'$PlanLat'";
		$Distance = "$ChkDistance";
	}

	if($RouteEntry != null || $RouteEntry != "") {
		$PlannerSQL = "SELECT T0.RouteEntry, T0.PlanDate, T0.CardCode, T0.CardName FROM route_planner T0 WHERE T0.RouteEntry = $RouteEntry AND T0.DocStatus = 'A' LIMIT 1";
		$PlannerRST = MySQLSelect($PlannerSQL);
		$PlanDate   = date("Y-m-d",strtotime($PlannerRST['PlanDate']));
		$ChckDate   = date("Y-m-d");

		if($ChkDistance <= 5.00) {
			if($PlanDate == $ChckDate) {
				$MeetType = 1;
			} else {
				$MeetType = 3;
			}
		} else {
			if($PlanDate == $ChckDate) {
				$MeetType = 2;
			} else {
				$MeetType = 4;
			}
		}
		if($PlanLon == "" && $PlanLat == "") {
			$MeetType = 5;
		} 
		if($CheckType == 1) {
			$MeetType = 6;
		}
		if($PlannerRST['CardCode'] == NULL) {
			$MeetType = 8;
		}
		$UpdateSQL = "UPDATE route_planner SET MeetType = $MeetType, UpdateUkey = '$CreateUkey' WHERE RouteEntry = $RouteEntry;";
		MySQLUpdate($UpdateSQL);

		$InsertSQL = "INSERT INTO route_checkin SET
						RouteEntry = $RouteEntry,
						plan_lon = $CusLon,
						plan_lat = $CusLat,
						chk_lon = '$ChkLon',
						chk_lat = '$ChkLat',
						ChkDistance = $Distance,
						CreateUkey = '$CreateUkey';";
		MySQLInsert($InsertSQL);
	} else {
		if($ChkDistance <= 5.00) {
			$MeetType = 3;
		} else {
			$MeetType = 4;
		}
		if($PlanLon == "" && $PlanLat == "") {
			$MeetType = 5;
		} 
		if($CheckType == 1) {
			$MeetType = 6;
		}
		
		$GetNameSQL = "SELECT T0.CardName FROM OCRD T0 WHERE T0.CardCode = '$ChkCardCode' LIMIT 1";
		$GetNameRST = MySQLSelect($GetNameSQL);
		/* Insert Planner */
		$InsPlanSQL = "INSERT INTO route_planner SET PlanDate = NOW(), CardCode = '$ChkCardCode', CardName = '".$GetNameRST['CardName']."', CreateUkey = '$CreateUkey', MeetType = $MeetType";
		$RouteID    = MySQLInsert($InsPlanSQL);
		/* Insert CheckIn */

		$InsertSQL = "INSERT INTO route_checkin SET
						RouteEntry = $RouteID,
						plan_lon = $CusLon,
						plan_lat = $CusLat,
						chk_lon = '$ChkLon',
						chk_lat = '$ChkLat',
						ChkDistance = $Distance,
						CreateUkey = '$CreateUkey';";
		MySQLInsert($InsertSQL);
	}
	$arrCol['chk_status'] = "SUCCESS";

}

function StrCell($c) {
	$StrCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
	return $StrCell;
}

if($_GET['p'] == 'Excel') {
	$year  = $_POST["y"];
    $month = $_POST["m"];
    $user  = $_POST["u"];
    $view  = $_POST["v"];

	$MeetTotalSQL = "SELECT
						DAY(T0.PlanDate) AS 'Day', COUNT(T0.RouteEntry) AS 'MeetTotal'
					FROM route_planner T0
					WHERE (YEAR(T0.PlanDate) = $year AND MONTH(T0.PlanDate) = $month) AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A'
					GROUP BY T0.PlanDate";
	$MeetTotalQRY = MySQLSelectX($MeetTotalSQL);
	while($MeetTotalRST = mysqli_fetch_array($MeetTotalQRY)) {
		${$MeetTotalRST['Day']."_loop"} = $MeetTotalRST['MeetTotal'];
	}

	/* LOOP INSIDE THE MONTH */
	$loopday = cal_days_in_month(CAL_GREGORIAN, $month, $year);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

	if($view == "LISTTRUE") {
		$AllPlanSale = 0;
		$AllDocTotal = 0; 
		$AllOpenIV = 0;
		for($d = 1; $d < date("d"); $d++) {
			${$d."_loop"} = 0;
		}
		$MeetTotalSQL = 
			"SELECT
				DAY(T0.CreateDate) AS 'Day', COUNT(T0.CreateDate) AS 'MeetTotal'
			FROM route_checkin T0
			LEFT JOIN route_planner T1 ON T1.RouteEntry = T0.RouteEntry 
			WHERE (YEAR(T0.CreateDate) = $year AND MONTH(T0.CreateDate) = $month AND DAY(T0.CreateDate) < ".date("d").") AND T0.CreateUkey = '$user' AND T1.DocStatus = 'A'
			GROUP BY DAY(T0.CreateDate)";
		$MeetTotalQRY = MySQLSelectX($MeetTotalSQL);
		while($MeetTotalRST = mysqli_fetch_array($MeetTotalQRY)) {
			${$MeetTotalRST['Day']."_loop"} = $MeetTotalRST['MeetTotal'];
		}
		
		$spreadsheet->getProperties()
			->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
			->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
			->setTitle("รายงานปฎิบัติการจริง บจ.คิงบางกอก อินเตอร์เทรด")
			->setSubject("รายงานปฎิบัติการจริง บจ.คิงบางกอก อินเตอร์เทรด");
		$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
		$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
		$spreadsheet->setActiveSheetIndex(0);

		// Style
		$PageHeader = [ 'font' => [ 'bold' => true, 'size' => 9.1 ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
		$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
		$TextCenterBold = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
		$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
		$TextRightBold  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
		$TextBold  = ['font' => [ 'bold' => true ]];

		$Row = 1; $Col = 1;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "วันที่");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(16);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "เวลาเช็คอิน");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(13);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ชื่อร้านค้า");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(40);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ที่อยู่");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(80);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ประมาณการยอดขาย (บาท)");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(25);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ยอดขาย (บาท)");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(20);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "บิลรอเรียกเก็บ (บาท)");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(20);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ผลการเข้าพบ");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(30);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "สถานะ (บาท)");
		$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(16);

		$spreadsheet->getActiveSheet()->getRowDimension($Row)->setRowHeight(18);
		$sheet->getStyle('A'.$Row.':'.StrCell($Col).$Row)->applyFromArray($PageHeader);

		$Row++;
		for($d = 1; $d <= $loopday; $d++) {
			$Col = 1;
			$LoopDate = date("Y-m-d",strtotime($year."-".$month."-".$d));
			$sheet->setCellValueByColumnAndRow($Col, $Row, $d);
			$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
			if(isset(${$d."_loop"}) && ${$d."_loop"} != 0 && ${$d."_loop"} != 1) {
				$spreadsheet->getActiveSheet()->mergeCells(StrCell($Col).$Row.':'.StrCell($Col).($Row+(${$d."_loop"}-1)));
			}
			if(isset(${$d."_loop"}) && ${$d."_loop"} != 0) {
				if($d < intval(date("d"))) {
					$GetTripSQL = 
						"SELECT 
							T0.RouteEntry, T1.EditStatus, T1.CardCode, T1.CardName, T1.Comments, T1.PlanSale, 
							CASE WHEN T1.MeetType IN (1,3) THEN 1 WHEN T1.MeetType IN (2,4) THEN 2 ELSE T1.MeetType END AS 'MeetType',
							T0.plan_lon AS 'Lon', T0.plan_lat AS 'Lat', T0.CreateDate, T0.ChkReport
						FROM route_checkin T0
						LEFT JOIN route_planner T1 ON T1.RouteEntry = T0.RouteEntry 
						WHERE DATE(T0.CreateDate) = '$LoopDate' AND T0.CreateUkey = '$user' AND T1.DocStatus = 'A'
						ORDER BY T0.CreateDate";
				}else{
					$GetTripSQL = 
						"SELECT 
							T0.RouteEntry, T0.EditStatus, T0.CardCode, T0.CardName, T0.Comments, T0.PlanSale,
							CASE WHEN T0.MeetType IN (1,3) THEN 1 WHEN T0.MeetType IN (2,4) THEN 2 ELSE T0.MeetType END AS 'MeetType',
							T1.Lon, T1.Lat, '' AS 'CreateDate', '' AS 'ChkReport'
						FROM route_planner T0
						LEFT JOIN ocrd T1 ON T0.CardCode = T1.CardCode AND T0.CardCode IS NOT NULL
						WHERE T0.PlanDate = '$LoopDate' AND T0.CreateUkey = '$user' AND T0.DocStatus = 'A' ORDER BY T0.RouteEntry";
				}
				$GetTripQRY = MySQLSelectX($GetTripSQL);
				while($GetTripRST = mysqli_fetch_array($GetTripQRY)) {
					$Col = 2;
					$TimeChk = ($GetTripRST['CreateDate'] != '') ? date("H:i", strtotime($GetTripRST['CreateDate']))." น." : "";
					$dis_view = NULL;
					$dis_chck = " disabled";
					switch($GetTripRST['MeetType']) {
						case 0: $MeetType = "รอเข้าพบ";  $dis_view = " disabled"; $dis_chck = NULL; break;
						case 1: $MeetType = "เข้าพบ"; break;
						case 2: $MeetType = "เข้าพบ (นอกพื้นที่)"; break;
						case 5: $MeetType = "เข้าพบ (ไม่มีพิกัด)"; break;
						case 6: $MeetType = "โทร / ไลน์"; break;
						case 7: $MeetType = "ลูกค้ามุ่งหวัง"; $dis_chck = NULL; break;
						case 8: $MeetType = "เข้าพบลูกค้ามุ่งหวัง"; break;
					}
					if($GetTripRST['CardCode'] == NULL || $GetTripRST['CardCode'] == "NULL") {
						$ShowCard = $GetTripRST['CardName'];
						$Teritory = "-";
						$DocTotal = "-";
						$OpenIV   = "-";
						$PlanSale = "-";
					} else {
						$CardDetail = GetCardDetailTRUE($GetTripRST['CardCode']);
						$ShowCard = $GetTripRST['CardCode']." ".$GetTripRST['CardName'];
						$Teritory = $CardDetail[0];
						$OpenIV   = GetCardDetailTRUEInt($GetTripRST['CardCode']);
						$DocTotal = GetDocTotalInt($year,$month,$GetTripRST['CardCode']);
						$PlanSale = number_format($GetTripRST['PlanSale'],2);
					}

					$sheet->setCellValueByColumnAndRow($Col, $Row, $TimeChk);
					$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
					$Col++;

					$sheet->setCellValueByColumnAndRow($Col, $Row, $ShowCard);
					$Col++;

					$sheet->setCellValueByColumnAndRow($Col, $Row, $Teritory);
					$Col++;

					$sheet->setCellValueByColumnAndRow($Col, $Row, $PlanSale);
					$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
					$Col++;

					$sheet->setCellValueByColumnAndRow($Col, $Row, $DocTotal);
					$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
					$Col++;

					$sheet->setCellValueByColumnAndRow($Col, $Row, $OpenIV);
					$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextRight);
					$Col++;

					$sheet->setCellValueByColumnAndRow($Col, $Row, $GetTripRST['ChkReport']);
					$Col++;

					$sheet->setCellValueByColumnAndRow($Col, $Row, $MeetType);
					$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
					$Col++;

					$Row++;
				}
			}else{
				$Row++;
			}
		}

		$writer = new Xlsx($spreadsheet);
		$FileName = "รายงานปฎิบัติการจริง - ".date("YmdHis").".xlsx";
		$writer->save("../../../../FileExport/Routetrip/".$FileName);
		$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'Routetrip', logFile = '$FileName', DateCreate = NOW()";
		MySQLInsert($InsertSQL);
		$arrCol['FileName'] = $FileName;
	}
}


/*-------------------- MEET SURVEY --------------------*/


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
	exit;
}

function CalTime($timeCal){
	switch ($timeCal){
		case '450':
			return "07:30";
		break;
		case '480':
			return "08:00";
		break;
		case '490':
			return "08:10";
		break;
		case '510':
			return "08:30";
		break;
		case '540':
			return "09:00";
		break;
		case '600':
			return "10:00";
		break;
		case '730':
			return "12:10";
		break;
		case '750':
			return "12:30";
		break;
		case '790':
			return "13:10";
		break;
		case '810':
			return "13:30";
		break;
		case '990':
			return "16:00";
		break;
		case '1020':
			return "17:00";
		break;
		case '1030':
			return "17:10";
		break;
		case '1050':
			return "17:30";
		break;
		case '1080':
			return "18:00";
		break;
		case '1140':
			return "19:00";
		break;
	}
}

function ChkFreeDay($EmpCode, $DocDate){
	$SQL1 = 
		"SELECT TOP 1
			T0.StartDate,T0.EndDate
		FROM hrTimeAbstainTimeStampDT T0
		LEFT JOIN  hrTimeAbstainTimeStamp T1 ON T0.AbStainTimeStampID = T1.AbStainTimeStampID
		LEFT JOIN emEmployee T2 ON T1.EmpID = T2.EmpID
		WHERE
			T1.ApproveStatus = 'Y' AND T1.IsCancel != 'TRUE' AND T1.IsDeleted != 'TRUE' AND 
			T2.EmpCode = '$EmpCode' AND T0.StartDate >= '$DocDate' AND T0.EndDate <= '$DocDate'
		ORDER BY T0.CreatedDate DESC";
	$QRY1 = HRMISelect($SQL1);
	$ax = 0;
	while ($RST1 = odbc_fetch_array($QRY1)) { $ax++; }
	
	if ($ax == 0){
		$SQL2 =
			"SELECT TOP 1
				T3.ShiftCode,T3.ShiftName,T4.ShiftCode,T4.ShiftName
			FROM  hrTimeChangeWorkDetail T0
			LEFT JOIN hrTimeChangeWork T1 ON T1.ChangeWorkID = T0.ChangeWorkID 
			LEFT JOIN emEmployee T2 ON T2.EmpID = T1.EmpID
			LEFT JOIN hrTimeShift T3 ON T0.ShiftID1 = T3.ShiftID
			LEFT JOIN hrTimeShift T4 ON T0.ShiftID2 = T4.ShiftID    
			WHERE
				T2.EmpCode = '$EmpCode' AND T1.ApproveStatus = 'Y' AND T1.IsDeleted != 'TRUE' AND T1.IsCancel != 'TRUE' AND T0.IsDeleted != 'TRUE' AND 
				(T0.Date1 >='$DocDate' AND T0.Date2 <= '$DocDate') AND (T3.WorkTime NOT IN ('0010','0011','0012','0014')  AND T4.WorkTime NOT IN ('0010','0011','0012','0014'))
			ORDER BY T0.ModifiedDate DESC";
		$QRY2 = HRMISelect($SQL2);
		$OFF = 0;
		while ($RST2 = odbc_fetch_array($QRY2)){ $OFF++; }
		if ($OFF == 0){
			return $ax;
		}else{
			return -1;
		}
	}else{
		return $ax;
	}
}

function ChkLeave($EmpCode, $DocDate) {
	$DateNo = date("N",strtotime($DocDate));

	if($DateNo == 7 || date("Y-m-d") <= date("Y-m-d",strtotime($DocDate))) {
		$SQL1 = "SELECT T0.Holiday_name FROM annual_holiday T0 WHERE T0.Holiday_Date = '$DocDate'";
		if(ChkRowDB($SQL1) > 0) {
			$RST1 = MySQLSelect($SQL1);
			return "<div class='event holiday'>".$RST1['Holiday_name']."</div>";
		} else {
			return "";
		}
	} else {
		$SQL2 = "SELECT T0.Holiday_name FROM annual_holiday T0 WHERE T0.Holiday_Date = '$DocDate'";
		if(ChkRowDB($SQL2) > 0) {
			$RST2 = MySQLSelect($SQL2);
			return "<div class='event holiday'>".$RST2['Holiday_name']."</div>";
		} else {
			$SQL3 =
				"SELECT TOP 1
					D1.LeaveTypeName, D1.LeaveTypeCode
				FROM hrTimeLeaveRecord D0
				LEFT JOIN hrLeaveType D1 ON D0.LeaveTypeID = D1.LeaveTypeID
				LEFT JOIN hrTimeLeaveRecordDetail D2 ON D0.LeaveID = D2.LeaveID
				LEFT JOIN emEmployee D3 ON D0.EmpID = D3.EmpID
				WHERE
					D3.EmpCode = '$EmpCode' AND D0.IsCancel != 'TRUE' AND D0.ApproveStatus = 'Y' AND D0.IsDeleted != 'TRUE' AND 
					DATEADD(dd,0,DATEDIFF(dd,0,D2.StartDate)) >= '$DocDate' AND DATEADD(dd,0,DATEDIFF(dd,0,D2.EndDate)) <= '$DocDate'
				ORDER BY D0.CreatedDate DESC";
			$QRY3 = HRMISelect($SQL3);
			$ax = 0;
			while($RST3 = odbc_fetch_array($QRY3)) {
				$ax++;
				if($RST3['LeaveTypeCode'] == "L-008") {
					$LeaveData = "<div class='event workformhome'>".conutf8($RST3['LeaveTypeName'])."</div>";
				} else {
					$LeaveData = "<div class='event leave'>".conutf8($RST3['LeaveTypeName'])."</div>";
				}
			}

			if($ax > 0) {
				return $LeaveData;
			} else {
				$SQL4 =
					"SELECT TOP 1
						T3.ShiftCode, T3.ShiftName, T4.ShiftCode, T4.ShiftName
					FROM hrTimeChangeWorkDetail T0
					LEFT JOIN hrTimeChangeWork T1 ON T1.ChangeWorkID = T0.ChangeWorkID
					LEFT JOIN emEmployee T2 ON T2.EmpID = T1.EmpID
					LEFT JOIN hrTimeShift T3 ON T0.ShiftID1 = T3.ShiftID
					LEFT JOIN hrTimeShift T4 ON T0.ShiftID2 = T4.ShiftID
					WHERE
						T2.EmpCode = '$EmpCode' AND T1.ApproveStatus = 'Y' AND T1.IsDeleted != 'TRUE' AND T1.IsCancel != 'TRUE' AND T0.IsDeleted != 'TRUE' AND 
						(T0.Date1 >='$DocDate' AND T0.Date2 <= '$DocDate')
					ORDER BY T0.ModifiedDate DESC";
				$QRY4 = HRMISelect($SQL4);
				$OFF  = 0;
				while($RST4 = odbc_fetch_array($QRY4)) {
					$OFF++;
					$LeaveData = "<div class='event offdate'>OFF</div>";
				}

				if($OFF > 0) {
					return $LeaveData;
				} else {
					$SQL5 =
						"SELECT TOP 1
							T0.ReMark
						FROM hrTimeWorkOutSideHD T0
						LEFT JOIN emEmployee T1 ON T0.EmpID = T1.EmpID
						WHERE
							T1.EmpCode = '$EmpCode' AND T0.ApproveStatus = 'Y' AND T0.IsDeleted != 'TRUE' AND T0.IsCancel != 'TRUE' AND T0.IsDeleted != 'TRUE' AND 
							(T0.OutSideDate >='$DocDate' AND T0.OutSideEndDate <= '$DocDate')
						ORDER BY T0.ModifiedDate DESC";
					$QRY5 = HRMISelect($SQL5);
					$OUT  = 0;
					while($RST5 = odbc_fetch_array($QRY5)) {
						$OUT++;
						$LeaveData = "<div class='event workoutside'>".conutf8($RST5['ReMark'])."</div>";
					}
					if($OUT > 0) {
						return $LeaveData;
					} else {
						if(($EmpCode == "4811002" || $EmpCode == "5705005" || $EmpCode == "6104006") && $DateNo == 6) {
							return "";
						} else {
							return "<div class='event workalert'>ขาดงาน</div>";
						}
					}
				}
			}
		}
	}
}

function ChkOFF($EmpCode, $DocDate, $TimeStamp) {
	$SQL1 =
		"SELECT TOP 1
			T3.ShiftCode, T3.ShiftName, T4.ShiftCode AS NewShiftCode, T4.ShiftName AS NewShift
		FROM hrTimeChangeWorkDetail T0
		LEFT JOIN hrTimeChangeWork T1 ON T1.ChangeWorkID = T0.ChangeWorkID
		LEFT JOIN emEmployee T2 ON T2.EmpID = T1.EmpID
		LEFT JOIN hrTimeShift T3 ON T0.ShiftID1 = T3.ShiftID
		LEFT JOIN hrTimeShift T4 ON T0.ShiftID2 = T4.ShiftID
		WHERE
			T2.EmpCode = '$EmpCode' AND T1.ApproveStatus = 'Y' AND T1.IsDeleted != 'TRUE' AND T1.IsCancel != 'TRUE' AND T0.IsDeleted != 'TRUE' AND
			(T0.Date1 >='$DocDate' AND T0.Date2 <= '$DocDate')
		ORDER BY T0.ModifiedDate DESC";
	// echo $SQL1;
	$OffData = 1;
	if(ChkRowHRMI($SQL1) > 0) {
		$QRY1 = HRMISelect($SQL1);
		$RST1 = odbc_fetch_array($QRY1);
		switch($RST1['NewShiftCode']) {
			case '0010':
				/* OFF เช้า */
				if(date("H:i",strtotime($TimeStamp)) > date("H:i",strtotime("13:10"))) {
					$OffData = 2;
				} else {
					$OffData = 3;
				}
			break;
			case '0012':
				/* OFF เช้า กะพิเศษ */
				if(date("H:i",strtotime($TimeStamp)) > date("H:i",strtotime("13:30"))) {
					$OffData = 2;
				} else {
					$OffData = 3;
				}
			break;
			case '0011':
				/* OFF บ่าย */
				if(date("H:i",strtotime($TimeStamp)) < date("H:i",strtotime("12:10"))) {
					$OffData = 4;
				} else {
					$OffData = 5;
				}
			break;
			case '0014':
				/* OFF บ่าย กะพิเศษ */
				if(date("H:i",strtotime($TimeStamp)) < date("H:i",strtotime("12:30"))) {
					$OffData = 4;
				} else {
					$OffData = 5;
				}
			break;
			default:
				/* รอเช็ค */
				$SQL2 =
					"SELECT TOP 1
						T2.LocationName  AS Remark
					FROM hrTimeWorkOutSideDT T2
					LEFT JOIN  hrTimeWorkOutSideHD T0 ON T0.WorkOutSideHDID = T2.WorkOutSideHDID
					LEFT JOIN emEmployee T1 ON T0.EmpID = T1.EmpID
					WHERE
						T1.EmpCode = '$EmpCode' AND T0.ApproveStatus = 'Y' AND T0.IsDeleted != 'TRUE' AND T0.IsCancel != 'TRUE' AND T0.IsDeleted != 'TRUE' AND
						(T0.OutSideDate >='$DocDate' AND T0.OutSideEndDate <= '$DocDate')
					ORDER BY T0.ModifiedDate DESC";
				$QRY2 = HRMISelect($SQL2);
				$WO   = 0;
				while($RST2 = odbc_fetch_array($QRY2)) {
					$Rmk_OutSite = conutf8($RST2['Remark']);
					$WO++;
				}

				if($WO > 0) {
					$OffData = "6-".$Rmk_OutSite;
				} else {
					$ChkLeave = ChkLeave($EmpCode,$DocDate);
					if($ChkLeave == "" || $ChkLeave == "<div class='event workalert'>ขาดงาน</div>") {
						$OffData = 1;
					} else {
						$OffData = "7-".$ChkLeave;
					}
				}
			break;
		}
	}
	return $OffData;
}

function CallTime($EmpCode, $DocDate, $ChkInTime, $ChkOutTime) {
	$FreeDay = ChkFreeDay($EmpCode, $DocDate);
	switch($FreeDay) {
		case 0:
		case-1:
			$SQL1 = 
				"SELECT
					T0.DateTimeStamp
				FROM hrTimeTempImport T0
				JOIN emEmployee T1 ON T0.EmpID = T1.EmpID
				WHERE T1.EmpCode = '$EmpCode' AND DATEADD(dd,0,DATEDIFF(dd,0,T0.DateTimeStamp)) ='$DocDate'
				ORDER BY T0.DateTimeStamp";
			$QRY1 = HRMISelect($SQL1);
			$i = 0;

			while($RST1 = odbc_fetch_array($QRY1)) {
				$i++;
				$TimeStamp[$i] = $RST1['DateTimeStamp'];
				$DayOFFIn  = 0;
				$DayOFFOut = 0;

				
			}

			if($i > 0) {
				$TimeStampIn  = date("H:i",strtotime($TimeStamp[1]));
				$TimeStampOut = date("H:i",strtotime($TimeStamp[$i]));
				if(date("H:i",strtotime($TimeStamp[1])) > $ChkInTime) {
					$DayOFFIn = ChkOFF($EmpCode, $DocDate, $TimeStamp[1]);
				}
				if(date("H:i",strtotime($TimeStamp[$i])) < $ChkOutTime) {
					$DayOFFOut = ChkOFF($EmpCode, $DocDate, $TimeStamp[$i]);
				}
			}

			

			/*Return Value
				0 = ไม่สาย ไม่ OFF
				1 = สาย
				2 = สาย + OFF เช้า
				3 = ไม่สาย + OFF เช้า
				4 = สาย + OFF บ่าย
				5 = ไม่สาย + OFF บ่าย
			*/

			if($i > 1) {
				$TimeDiv = "";
				if($DayOFFIn == 0 || $DayOFFIn == 3) {
					$TimeDiv = "<div class='event workdate'>".date("H:i",strtotime($TimeStamp[1]))." น. - ";
				} else {
					$TimeDiv = "<div class='event workdate'><span class='text-danger'>".date("H:i",strtotime($TimeStamp[1]))." น.</span> - ";
				}
				if($DayOFFOut == 0 || $DayOFFOut == 5) {
					$TimeDiv .= date("H:i",strtotime($TimeStamp[$i]))." น.</span></div>";
				}  else {
					$TimeDiv .= "<span class='text-danger'>".date("H:i",strtotime($TimeStamp[$i]))." น.</span></div>";
				}

				if($DayOFFIn == 2 || $DayOFFIn == 3) {
					$TimeDiv .= "<div class='event offdate'>OFF เช้า</div>";
				}

				if($DayOFFOut == 4 || $DayOFFOut == 5) {
					$TimeDiv .= "<div class='event offdate'>OFF บ่าย</div>";
				}

				if(substr($DayOFFIn,0,1) == "6") {
					$TimeDiv .= "<div class='event workoutside'>".substr($DayOFFIn,2)."</div>";
				}
				if(substr($DayOFFOut,0,1) == "6") {
					$TimeDiv .= "<div class='event workoutside'>".substr($DayOFFOut,2)."</div>";
				}

				if(substr($DayOFFIn,0,1) == "7") {
					$TimeDiv .= substr($DayOFFIn,2);
				}
				if(substr($DayOFFOut,0,1) == "7") {
					$TimeDiv .= substr($DayOFFOut,2);
				}
			} else {
				if($i == 1) {
					$TimeDiv ="<div class='event workdate 2'>".date("H:i",strtotime($TimeStamp[1]))." น.</div>";
				} else {
					$TimeDiv = ChkLeave($EmpCode, $DocDate);
				}
			}
			return $TimeDiv;
		break;
		default:
			return "<div class='event noscan'>ยกเว้นสแกนนิ้ว</div>";
		break;
	}
}


if($_GET['p'] == "GetTimeStamp") {
	$DocDate  = $_POST['DocDate'];
	$TeamCode = $_POST['TeamCode'];

	if($_SESSION['uClass'] == 29) {
		$SqlWhr = " IN ('DP002','DP009')";
	} else {
		
		if ($TeamCode == 'DP000'){
			$SqlWhr = " IN ('DP002','DP003','DP004','DP005','DP006','DP007','DP008','DP009','DP010','DP011','DP012')";
		}else{
			$SqlWhr = " = '$TeamCode'";
		} 

		
	}

	$GetTimeSQL =
	"SELECT
		W0.*, W3.DateTimeStamp AS TimeID, W1.OrgUnitName AS 'DivName', W2.OrgUnitName AS 'DeptName',
		CASE
			WHEN W3.RecordType = 'Import' THEN 'SCAN' 
			WHEN W3.RecordType = 'ESS' THEN W3.LocationName
		ELSE '' END AS CHKName,
		CASE WHEN W0.DivCode IN ('DV011','DV014','DV017','DV022','DV032','DV033','DV034','DV035','DV036','DV037','DV038','DV039','DV040','DV041','DV042','DV043','DV044','DV045','DV046','DV047','DV048','DV050') THEN 1 ELSE 0 END AS Office,
		CASE
			WHEN W0.PosLevel = 'M2' THEN 0 
			WHEN W0.PosLevel = 'M1' THEN 1
			WHEN W0.PosLevel = 'L2' THEN 2
			WHEN W0.PosLevel = 'L1' THEN 3
			WHEN W0.PosLevel = 'O3' THEN 4
			WHEN W0.PosLevel = 'O2' THEN 5
			WHEN W0.PosLevel = 'O1' THEN 6
		ELSE 7 END AS LevelRun
	FROM (
		SELECT
			P0.PersonID,P0.EmpID,P0.WorkProFileID,P0.EmpCode,P0.MemberCardExcept,P0.FirstName,P0.LastName,P0.NickName,P0.WorkingStatus,P2.PositionCode,
			SUBSTRING(P2.PositionCode,0,3) AS PosLevel,P2.PositionName,
			CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DV' THEN P3.OrgUnitCode ELSE NULL END AS 'DivCode',
			CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DP' THEN P3.OrgUnitCode ELSE P4.OrgUnitCode END AS 'DeptCode',
		(SELECT TOP 1 (X0.TempImportID) FROM hrTimeTempImport X0 WHERE X0.EmpID = P0.EmpID AND DATEADD(dd,0,DATEDIFF(dd,0,X0.DateTimeStamp)) = '$DocDate' ORDER BY X0.DateTimeStamp ) AS TimeTmpID,
		CASE
			WHEN P5.TimeIn1 = 490 THEN '810AM' 
			WHEN P5.TimeIn1 = 510 THEN '830AM'
			WHEN P5.TimeIn1 = 540 THEN '900AM'
		ELSE 'PM' END AS TimeIN, 
		(SELECT TOP 1 D1.LeaveTypeName
		FROM hrTimeLeaveRecord D0 
		LEFT JOIN hrLeaveType D1 ON D0.LeaveTypeID = D1.LeaveTypeID
		LEFT JOIN hrTimeLeaveRecordDetail D2 ON D0.LeaveID = D2.LeaveID
		WHERE D0.EmpID = P0.EmpID AND D0.IsCancel != 'TRUE' AND D0.ApproveStatus = 'Y' AND D0.IsDeleted != 'TRUE' AND DATEADD(dd,0,DATEDIFF(dd,0,D2.StartDate)) >= '$DocDate' AND DATEADD(dd,0,DATEDIFF(dd,0,D2.EndDate)) <= '$DocDate' ORDER BY D0.CreatedDate DESC) AS  LeaveCode,
		(SELECT TOP 1 N0.ApproveStatus 
		FROM  hrTimeAbstainTimeStamp N0
		JOIN hrTimeAbstainTimeStampDT N1 ON N1.AbstainTimeStampID =   N0.AbstainTimeStampID
		WHERE N0.EmpID = P0.EmpID AND N0.ApproveStatus = 'Y' AND  N0.IsCancel != 'TRUE'  AND N0.IsDeleted != 'TRUE'  AND DATEADD(dd,0,DATEDIFF(dd,0,N1.StartDate)) >= '$DocDate' AND DATEADD(dd,0,DATEDIFF(dd,0,N1.EndDate)) <= '$DocDate') AS OutSite                        
		FROM  (
			SELECT
				T0.PersonID,T1.EmpID,T1.EmpCode,T1.MemberCardExcept,T0.FirstName,T0.LastName,T0.NickName,T1.WorkingStatus,T1.ShiftID,
				(SELECT TOP 1 D1.WorkProfileID FROM hrEmpWorkProfile D1 WHERE T1.EmpID = D1.EmpID  AND D1.EndDate IS NULL AND  D1.IsDeleted != 'TRUE' ORDER BY D1.ModifiedDate DESC) AS WorkProFileID               
			FROM emPerson T0
			LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
			WHERE T1.WorkingStatus = 'Working' AND  T0.IsDeleted != 'TRUE' AND T1.EmpCode NOT LIKE 'B%'
		) P0
		LEFT JOIN hrEmpWorkProfile P1 ON  P1.WorkProfileID = P0.WorkProfileID 
		LEFT JOIN emPosition P2 ON P1.PositionID =  P2.PositionID
		LEFT JOIN emOrgUnit P3 ON P1.OrgUnitID = P3.OrgUnitID
		LEFT JOIN emOrgUnit P4 ON P3.ParentOrgUnit = P4.OrgUnitID
		LEFT JOIN hrTimeShift P5 ON P0.ShiftID = P5.ShiftID 
		WHERE P1.OrgID = '3F3BF3AD-B4C9-4D44-A56F-AB55C4E4FB01' AND P2.PositionCode  NOT LIKE 'C%'
	)  W0
	LEFT JOIN  emOrgUnit W1 ON W0.DivCode =  W1.OrgUnitCode
	LEFT JOIN  emOrgUnit W2 ON W0.DeptCode =  W2.OrgUnitCode
	LEFT JOIN hrTimeTempImport  W3 ON W0.TimeTmpID = W3.TempImportID
	WHERE W0.DeptCode $SqlWhr
	ORDER BY Office,W0.DeptCode,LevelRun,W0.DivCode";
	// echo $GetTimeSQL;
	$GetTimeQRY = HRMISelect($GetTimeSQL);
	$ax = 0;
	$bx = 0;
	$i  = 0;
	while($GetTimeRST = odbc_fetch_array($GetTimeQRY)) {
		$Office = $GetTimeRST['Office'];
		/* 0 = Office use $ax / 1 = PC & DEMON use $bx */

		if($Office == 0) {
			$i = $ax;
		} else {
			$i = $bx;
		}

		if($GetTimeRST['NickName'] != "" || $GetTimeRST['NickName'] != NULL) {
			$NickName = " (".$GetTimeRST['NickName'].")";
		} else {
			$NickName = null;
		}

		$Leave   = "";
		$Outsite = "";
		$NoStamp = "";

		if($GetTimeRST['MemberCardExcept'] != "0") {
			$TimeStamp = "<span class='text-muted'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเว้น</span>";
		} else {
			if($GetTimeRST['TimeID'] == NULL OR $GetTimeRST['TimeID'] == "") {
				$TimeStamp = "";
				if($GetTimeRST['LeaveCode'] != "") {
					$Leave = conutf8($GetTimeRST['LeaveCode']);
				} else {
					if($GetTimeRST['OutSite'] == "Y") {
						$Outsite = "<span class='text-success'><i class='fas fa-check fa-fw fa-1x'></i></span>";
					} else {
						$NoStamp = "<span class='text-danger'><i class='fas fa-times fa-fw fa-1x'></i></span>";
						$NoStamp = "<span class='text-danger'>ไม่ลงเวลา</span>";
					}
				}
			} else {
				if($GetTimeRST['Office'] == 0) {
					switch($GetTimeRST['TimeIN']) {
						case '810AM': $time_1 = "8:10:00"; break;
						case '830AM': $time_1 = "8:30:00"; break;
						case '900AM': $time_1 = "9:00:00"; break;
						default: $time_1 = "8:10:00"; break;
					}
					$time_2 = date("h:i:s",strtotime($GetTimeRST['TimeID']));
					$diff   = date_diff(date_create($time_2), date_create($time_1));

					if(date_create($time_2) > date_create($time_1)) {
						$x = 1;
					} else {
						$x = -1;
					}

					$timediff = $diff->h*60;
					$timediff = $x * ($timediff + $diff->i);

					if($timediff > 0) {
						$TimeStamp = "<span class='text-danger' style='font-weight: bold;'>".date('h:i',strtotime($GetTimeRST['TimeID']))."</span><br/><small class='text-muted'>".conutf8($GetTimeRST['CHKName'])."</small>";
					} else {
						$TimeStamp = "<span class='text-success'>".date('h:i',strtotime($GetTimeRST['TimeID']))."</span><br/><small class='text-muted'>".conutf8($GetTimeRST['CHKName'])."</small>";
					}

				} else {
					$TimeStamp = "<span>".date('h:i',strtotime($GetTimeRST['TimeID']))."</span><br/><small class='text-muted'>".conutf8($GetTimeRST['CHKName'])."</small>";
				}
			}
		}
		$arrCol[$Office][$i]['EmpCode']          = $GetTimeRST['EmpCode'];
		$arrCol[$Office][$i]['MemberCardExcept'] = $GetTimeRST['MemberCardExcept'];
		$arrCol[$Office][$i]['FullName']         = conutf8($GetTimeRST['FirstName']." ".$GetTimeRST['LastName'].$NickName);
		$arrCol[$Office][$i]['PositionName']     = conutf8($GetTimeRST['PositionName']);
		$arrCol[$Office][$i]['DeptName']     = conutf8($GetTimeRST['DeptName']);
		$arrCol[$Office][$i]['TimeStamp']        = $TimeStamp;
		$arrCol[$Office][$i]['Leave']            = $Leave;
		$arrCol[$Office][$i]['Outsite']          = $Outsite;
		$arrCol[$Office][$i]['NoStamp']          = $NoStamp;

		if($Office == 0) {
			$ax++;
		} else {
			$bx++;
		}	
	}
	$arrCol['ax'] = $ax;
	$arrCol['bx'] = $bx;
}

if($_GET['p'] == "GetData") {
	$DocDate = $_POST['DocDate'];
	$EmpCode = $_POST['EmpCode'];

	/* Explode Year Month Day into variable */

	$DocY  = date("Y",strtotime($DocDate));
	$DocM  = date("m",strtotime($DocDate));
	$DocD  = date("d",strtotime($DocDate));
	$today = date("Y-m-d");

	// echo $DocD." / ".$DocM." / ".$DocY;

	if($DocD >= 26 && $DocD <= 31) {
		/* Y1-M1-26 to Y2-M2-25 */
		switch($DocM) {
			case 12:
				$SetY1 = $DocY;
				$SetM1 = "12";
				$SetY2 = $DocY+1;
				$SetM2 = "01";
			break;
			default:
				$SetY1 = $DocY;
				$SetM1 = $DocM;
				$SetY2 = $DocY;
				$SetM2 = $DocM+1;
			break;
		}
	} else {
		switch($DocM) {
			case 1:
				$SetY1 = $DocY-1;
				$SetM1 = "12";
				$SetY2 = $DocY;
				$SetM2 = "01";
			break;
			default:
				$SetY1 = $DocY;
				$SetM1 = $DocM-1;
				$SetY2 = $DocY;
				$SetM2 = $DocM;
			break;
		}
	}
	$FullDate_1 = date("Y-m",strtotime($SetY1."-".$SetM1."-26"));
	$FullDate_2 = date("Y-m",strtotime($SetY2."-".$SetM2."-25"));

	$S1_FirstDay = date("w",strtotime($FullDate_1."-26"));
	$S1_LastDate  = cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($SetY1."-".$SetM1."-26")), date("Y",strtotime($SetY1."-".$SetM1."-26")));
	$S2_LastDate  = date("w",strtotime($FullDate_2."-25"));

	/* Get Check In and Check Out Time */
	$TimeSQL = "SELECT T1.TimeIn1,T1.TimeOut1 FROM emEmployee T0 LEFT JOIN hrTimeShift T1 ON T0.ShiftID = T1.ShiftID WHERE EmpCode = '$EmpCode'";
	$TimeQRY = HRMISelect($TimeSQL);
	$TimeRST = odbc_fetch_array($TimeQRY);

	$ChkInTime  = CalTime($TimeRST['TimeIn1']);
	$ChkOutTime = CalTime($TimeRST['TimeOut1']);

	/* LOOP OUTSIDE FIRST SECTIONS */
	for($o=1; $o <= $S1_FirstDay; $o++) {
		$output .= "<li class='outside'>&nbsp;</li>";
	}

	/* LOOP SECTION 1 */

	for($d = 26; $d <= $S1_LastDate; $d++) {
		$LoopDate = date("Y-m-d",strtotime($FullDate_1."-".$d));
		if(date("w",strtotime($LoopDate)) == 0) { $clsSunday = " text-danger"; } else { $clsSunday = NULL; }
		if($LoopDate == $today) { $clstoday = " class='today'"; } else { $clstoday = NULL; }
		$output .= "<li $clstoday>";
			$output .= "<div class='date text-right $clsSunday'>".$d."</div>";
			$output .= CallTime($EmpCode, $LoopDate, $ChkInTime, $ChkOutTime);
		$output .= "</li>";
	}

	/* LOOP SECTION 2 */
	for($d = 1; $d <= 25; $d++) {
		$LoopDate = date("Y-m-d",strtotime($FullDate_2."-".$d));
		if(date("w",strtotime($LoopDate)) == 0) { $clsSunday = " text-danger"; } else { $clsSunday = NULL; }
		if($LoopDate == $today) { $clstoday = " class='today'"; } else { $clstoday = NULL; }
		$output .= "<li $clstoday>";
			$output .= "<div class='date text-right $clsSunday'>".$d."</div>";
			$output .= CallTime($EmpCode, $LoopDate, $ChkInTime, $ChkOutTime);
		$output .= "</li>";
	}

	/* LOOP OUTSIDE 2ND SECTIONS */
	for($o=1; $o <= 6-$S2_LastDate; $o++) {
		$output .= "<li class='outside'>&nbsp;</li>";
	}

	$arrCol['output'] = $output;

}


function StrCell($c) {
	$StrCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
	return $StrCell;
}

if($_GET['p'] == 'Export') {
	$DocDate = $_POST['DocDate'];

	$SQL = 
		"SELECT
			W0.*, W3.DateTimeStamp AS TimeID, W4.DateTimeStamp AS TimeOutID, W1.OrgUnitName AS 'DivName', W2.OrgUnitName AS 'DeptName',
			CASE
				WHEN W3.RecordType = 'Import' THEN 'SCAN' 
				WHEN W3.RecordType = 'ESS' THEN W3.LocationName
			ELSE '' END AS CHKName,
			CASE WHEN W0.DivCode IN ('DV011','DV014','DV017','DV022','DV032','DV033','DV034','DV035','DV036','DV037','DV038','DV039','DV040','DV041','DV042','DV043','DV044','DV045','DV046','DV047','DV048','DV050') THEN 1 ELSE 0 END AS Office,
			CASE
				WHEN W0.PosLevel = 'M2' THEN 0 
				WHEN W0.PosLevel = 'M1' THEN 1
				WHEN W0.PosLevel = 'L2' THEN 2
				WHEN W0.PosLevel = 'L1' THEN 3
				WHEN W0.PosLevel = 'O3' THEN 4
				WHEN W0.PosLevel = 'O2' THEN 5
				WHEN W0.PosLevel = 'O1' THEN 6
			ELSE 7 END AS LevelRun
		FROM (
			SELECT
				P0.PersonID,P0.EmpID,P0.WorkProFileID,P0.EmpCode,P0.MemberCardExcept,P0.FirstName,P0.LastName,P0.NickName,P0.WorkingStatus,P2.PositionCode,
				SUBSTRING(P2.PositionCode,0,3) AS PosLevel,P2.PositionName,
				CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DV' THEN P3.OrgUnitCode ELSE NULL END AS 'DivCode',
				CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DP' THEN P3.OrgUnitCode ELSE P4.OrgUnitCode END AS 'DeptCode',
				(SELECT TOP 1 (X0.TempImportID) FROM hrTimeTempImport X0 WHERE X0.EmpID = P0.EmpID AND DATEADD(dd,0,DATEDIFF(dd,0,X0.DateTimeStamp)) = '$DocDate' ORDER BY X0.DateTimeStamp ) AS TimeTmpID,
				(SELECT TOP 1 (X0.TempImportID) FROM hrTimeTempImport X0 WHERE X0.EmpID = P0.EmpID AND DATEADD(dd,0,DATEDIFF(dd,0,X0.DateTimeStamp)) = '$DocDate' ORDER BY X0.DateTimeStamp DESC) AS TimeTmpOutID,
				CASE
					WHEN P5.TimeIn1 = 490 THEN '810AM' 
					WHEN P5.TimeIn1 = 510 THEN '830AM'
					WHEN P5.TimeIn1 = 540 THEN '900AM'
				ELSE 'AM' END AS TimeIN, 
				CASE
					WHEN P5.TimeOut1 = 1030 THEN '510PM' 
					WHEN P5.TimeOut1 = 1050 THEN '530PM'
					WHEN P5.TimeOut1 = 1080 THEN '600PM'
				ELSE 'PM' END AS TimeOut,
				CASE
					WHEN P5.ShiftCode IN ('0008','0003','0002') THEN 'กะปกติ 08:10-17:10'
					WHEN P5.ShiftCode = '0009' THEN 'กะพิเศษ 08:30-17:30'
					WHEN P5.ShiftCode = '0010' THEN  'OFF เช้า 13:10-17:10'
					WHEN P5.ShiftCode = '0011' THEN  'OFF บ่าย 08:10-12:10'
					WHEN P5.ShiftCode = '0012' THEN 'OFF เช้า 13:30-17:30'
					WHEN P5.ShiftCode = '0014' THEN 'OFF บ่าย 08:30-12:30'
				ELSE 'กะงานพิเศษ PC' END AS ShiftName,
				(
					SELECT TOP 1 D1.LeaveTypeName
					FROM hrTimeLeaveRecord D0 
					LEFT JOIN hrLeaveType D1 ON D0.LeaveTypeID = D1.LeaveTypeID
					LEFT JOIN hrTimeLeaveRecordDetail D2 ON D0.LeaveID = D2.LeaveID
					WHERE D0.EmpID = P0.EmpID AND D0.IsCancel != 'TRUE' AND D0.ApproveStatus = 'Y' AND D0.IsDeleted != 'TRUE' AND DATEADD(dd,0,DATEDIFF(dd,0,D2.StartDate)) >= '$DocDate' AND DATEADD(dd,0,DATEDIFF(dd,0,D2.EndDate)) <= '$DocDate' ORDER BY D0.CreatedDate DESC
				) AS  LeaveCode,
				(
					SELECT TOP 1 N0.ApproveStatus 
					FROM  hrTimeAbstainTimeStamp N0
					JOIN hrTimeAbstainTimeStampDT N1 ON N1.AbstainTimeStampID =   N0.AbstainTimeStampID
					WHERE N0.EmpID = P0.EmpID AND N0.ApproveStatus = 'Y' AND  N0.IsCancel != 'TRUE'  AND N0.IsDeleted != 'TRUE'  AND DATEADD(dd,0,DATEDIFF(dd,0,N1.StartDate)) >= '$DocDate' AND DATEADD(dd,0,DATEDIFF(dd,0,N1.EndDate)) <= '$DocDate'
				) AS OutSite                        
			FROM(
				SELECT
					T0.PersonID,T1.EmpID,T1.EmpCode,T1.MemberCardExcept,T0.FirstName,T0.LastName,T0.NickName,T1.WorkingStatus,T1.ShiftID,
					(SELECT TOP 1 D1.WorkProfileID FROM hrEmpWorkProfile D1 WHERE T1.EmpID = D1.EmpID  AND D1.EndDate IS NULL AND  D1.IsDeleted != 'TRUE' ORDER BY D1.ModifiedDate DESC) AS WorkProFileID               
				FROM emPerson T0
				LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
				WHERE T1.WorkingStatus = 'Working' AND  T0.IsDeleted != 'TRUE' AND T1.EmpCode NOT LIKE 'B%'
			) P0
			LEFT JOIN hrEmpWorkProfile P1 ON  P1.WorkProfileID = P0.WorkProfileID 
			LEFT JOIN emPosition P2 ON P1.PositionID =  P2.PositionID
			LEFT JOIN emOrgUnit P3 ON P1.OrgUnitID = P3.OrgUnitID
			LEFT JOIN emOrgUnit P4 ON P3.ParentOrgUnit = P4.OrgUnitID
			LEFT JOIN hrTimeShift P5 ON P0.ShiftID = P5.ShiftID 
			WHERE P1.OrgID = '3F3BF3AD-B4C9-4D44-A56F-AB55C4E4FB01' AND P2.PositionCode  NOT LIKE 'C%'
		) W0
		LEFT JOIN  emOrgUnit W1 ON W0.DivCode =  W1.OrgUnitCode
		LEFT JOIN  emOrgUnit W2 ON W0.DeptCode =  W2.OrgUnitCode
		LEFT JOIN hrTimeTempImport  W3 ON W0.TimeTmpID = W3.TempImportID
		LEFT JOIN hrTimeTempImport  W4 ON W0.TimeTmpOutID = W4.TempImportID
		WHERE W0.DeptCode IN ('DP002','DP003','DP004','DP005','DP006','DP007','DP008','DP009','DP010','DP011','DP012')
		ORDER BY Office,W0.DeptCode,LevelRun,W0.DivCode";
	$QRY = HRMISelect($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานสถิติการมาทำงาน บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานสถิติการมาทำงาน บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

	$PageHeader = [ 'font' => [ 'bold' => true, 'size' => 9.1 ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	$Row = 1;
	
	$Col = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "รหัสพนักงาน");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ชื่อพนักงาน");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(30);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ตำแหน่ง");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(35);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ฝ่าย");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(25);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ประเภทกะงาน");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(23);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "เวลาเข้างาน");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "เวลาออกงาน");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "สถานที่");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(20);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ลา");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(12);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ยังไม่ลงเวลาขาดงาน");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(20);
	$Col++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, "ทำงานนอกสถานที่");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(20);

	$sheet->getStyle('A'.$Row.':'.StrCell($Col).$Row)->applyFromArray($PageHeader);

	while($RST = odbc_fetch_array($QRY)) {
		$Row++;
		$Col = 1;
		$TimeStamp = "";
		$ColorTimeStamp = "";
		$TimeOutStamp = "";
		$ColorTimeOutStamp = "";
		$Leave = "";
		$NoStamp = "";
		$Outsite = "";
		$ChkName = "";
		if($RST['MemberCardExcept'] != "0") {
			$TimeStamp = "ยกเว้น";
			$TimeOutStamp = "ยกเว้น";
		}else{
			if($RST['TimeID'] == NULL OR $RST['TimeID'] == "") {
				if($RST['LeaveCode'] != "") {
					$Leave = conutf8($RST['LeaveCode']);
				} else {
					if($RST['OutSite'] == "Y") {
						$Outsite = "ลงเวลานอกสถานที่";
					} else {
						$NoStamp = "ไม่ลงเวลา";
					}
				}
			}else{
				if($RST['Office'] == 0) {
					$ChkName = conutf8($RST['CHKName']);

					// Check In
					switch($RST['TimeIN']) {
						case '810AM': $time_1 = strtotime("8:10:00"); break;
						case '830AM': $time_1 = strtotime("8:30:00"); break;
						case '900AM': $time_1 = strtotime("9:00:00"); break;
					}
					$time_2 = strtotime(date("h:i:s",strtotime($RST['TimeID'])));
					$timediff = ($time_2 - $time_1) / 60;
					$TimeStamp = date('h:i',strtotime($RST['TimeID']));
					// if($timediff > 0) {
					// 	$ColorTimeStamp = "ffDC3545"; // สีแดง
					// }else{
					// 	$ColorTimeStamp = "ff198754"; // สีเขียว
					// }

					// Check Out
					switch($RST['TimeOutID']) {
						case '510PM': $time_1 = strtotime("17:10:00"); break;
						case '530PM': $time_1 = strtotime("17:30:00"); break;
						case '600PM': $time_1 = strtotime("18:00:00"); break;
					}
					$time_2 = strtotime(date("h:i:s",strtotime($RST['TimeOutID'])));
					$timediff = ($time_2 - $time_1) / 60;
					$TimeOutStamp = date('h:i',strtotime($RST['TimeOutID']));
					// if($timediff < 0) {
					// 	$ColorTimeOutStamp = "ffDC3545"; // สีแดง
					// }else{
					// 	$ColorTimeOutStamp = "ff198754"; // สีเขียว
					// }
				}else{
					$ChkName = conutf8($RST['CHKName']);

					$TimeStamp = date('h:i',strtotime($RST['TimeID']));
					$TimeOutStamp = date('h:i',strtotime($RST['TimeOutID']));
				}
			}
		}

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['EmpCode']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$NickName = ($RST['NickName'] != "" || $RST['NickName'] != NULL) ? "(".$RST['NickName'].")" : "";
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, conutf8($RST['FirstName']." ".$RST['LastName']." ".$NickName));
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, conutf8($RST['PositionName']));
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, conutf8($RST['DeptName']));
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $RST['ShiftName']);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $TimeStamp);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFont()->getColor()->setARGB('ff198754');
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $TimeOutStamp);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$spreadsheet->getActiveSheet()->getStyle(StrCell($Col).$Row)->getFont()->getColor()->setARGB('ffDC3545');
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $ChkName);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $Leave);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;
		
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $NoStamp);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($Col, $Row, $Outsite);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานสถิติการมาทำงาน - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/EmpTimeStamp/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'EmpTimeStamp', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
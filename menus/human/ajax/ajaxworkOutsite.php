<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName'] == NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
	exit();
}

$filt_year = $_POST['y'];
$filt_month = $_POST['m'];

switch($filt_month) {
	case 1:
		$year_1  = $filt_year - 1;
		$month_1 = 12;
		$year_2  = $filt_year;
		$month_2 = $filt_month;
	break;
	default:
		$year_1  = $filt_year;
		$month_1 = $filt_month - 1;
		$year_2  = $filt_year;
		$month_2 = $filt_month;
	break;
}

$Date_1 = $year_1."-".$month_1."-26";
$Date_2 = $year_2."-".$month_2."-25";

if($_GET['p'] == "WorkData") {
	

	$SQL1 = 
		"SELECT DISTINCT
			K0.DeptCode, K0.PosLevel, K0.PositionCode, K0.EmpCode, K0.FirstName, K0.LastName, K0.NickName, K0.PositionName, K3.OrgUnitName
		FROM (
			SELECT
				P0.*, P2.PositionCode,
				SUBSTRING(P2.PositionCode,0,3) AS PosLevel, P2.PositionName,
				CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DV' THEN P3.OrgUnitCode ELSE NULL END AS 'DivCode',
				CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DP' THEN P3.OrgUnitCode ELSE P4.OrgUnitCode END AS 'DeptCode'
			FROM (
				SELECT
					T0.PersonID, T1.EmpID, T1.EmpCode, T1.MemberCardExcept, T0.FirstName, T0.LastName, T0.NickName, T1.WorkingStatus, T1.ShiftID,
					(SELECT TOP 1 D1.WorkProfileID FROM hrEmpWorkProfile D1 WHERE T1.EmpID = D1.EmpID  AND D1.EndDate IS NULL AND  D1.IsDeleted != 'TRUE' ORDER BY D1.ModifiedDate DESC) AS WorkProFileID               
				FROM emPerson T0
				LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
				WHERE T1.WorkingStatus = 'Working' AND MemberCardExCept != 'TRUE' AND  T0.IsDeleted != 'TRUE' AND T1.EmpCode NOT LIKE 'B%' AND T1.EmpGroupID = 'a8e6e883-1391-4473-a544-6406668dd43d'
			) P0
			LEFT JOIN hrEmpWorkProfile P1 ON  P1.WorkProfileID = P0.WorkProfileID 
			LEFT JOIN emPosition P2 ON P1.PositionID =  P2.PositionID
			LEFT JOIN emOrgUnit P3 ON P1.OrgUnitID = P3.OrgUnitID
			LEFT JOIN emOrgUnit P4 ON P3.ParentOrgUnit = P4.OrgUnitID
			LEFT JOIN hrTimeShift P5 ON P0.ShiftID = P5.ShiftID
		) K0
		JOIN hrTimeTempImport K1 ON K0.EmpID = K1.EmpID AND (DATEADD(dd, 0, DATEDIFF(dd, 0, K1.DateTimeStamp)) BETWEEN '$Date_1' AND '$Date_2')
		JOIN  emOrgUnit K3 ON K0.DeptCode = K3.OrgUnitCode 
		WHERE K1.LocationName IN ('KSY','KSM','KSO')
		ORDER BY K0.EmpCode";
	$Rows = ChkRowHRMI($SQL1);
	if($Rows > 0) {
		$QRY1 = HRMISelect($SQL1);
		$i = 0;
		while($RST1 = odbc_fetch_array($QRY1)) {
			$arrCol[$i]['EmpCode']    = $RST1['EmpCode'];
			$arrCol[$i]['FullName']   = conutf8($RST1['FirstName']." ".$RST1['LastName']." (".$RST1['NickName'].")");
			$arrCol[$i]['Position']   = conutf8($RST1['PositionName']);
			$arrCol[$i]['Department'] = conutf8($RST1['OrgUnitName']);
			$i++;
		}
	}

	$arrCol['Rows'] = $Rows;
}

if($_GET['p'] == "GetDetail") {
	$EmpCode = $_POST['e'];
	$SQL1 = 
		"SELECT
			K0.*, K3.OrgUnitName, K1.DateTimeStamp, K1.LocationName
		FROM (
			SELECT
				P0.*, P2.PositionCode,
				SUBSTRING(P2.PositionCode,0,3) AS PosLevel, P2.PositionName,
				CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DV' THEN P3.OrgUnitCode ELSE NULL END AS 'DivCode',
				CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DP' THEN P3.OrgUnitCode ELSE P4.OrgUnitCode END AS 'DeptCode'
			FROM (
				SELECT
					T0.PersonID, T1.EmpID, T1.EmpCode, T1.MemberCardExcept, T0.FirstName, T0.LastName, T0.NickName, T1.WorkingStatus, T1.ShiftID,
					(SELECT TOP 1 D1.WorkProfileID FROM hrEmpWorkProfile D1 WHERE T1.EmpID = D1.EmpID  AND D1.EndDate IS NULL AND  D1.IsDeleted != 'TRUE' ORDER BY D1.ModifiedDate DESC) AS WorkProFileID               
				FROM emPerson T0
				LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
				WHERE T1.WorkingStatus = 'Working' AND MemberCardExCept != 'TRUE' AND  T0.IsDeleted != 'TRUE' AND T1.EmpCode NOT LIKE 'B%' AND T1.EmpGroupID = 'a8e6e883-1391-4473-a544-6406668dd43d'
			) P0
			LEFT JOIN hrEmpWorkProfile P1 ON  P1.WorkProfileID = P0.WorkProfileID 
			LEFT JOIN emPosition P2 ON P1.PositionID =  P2.PositionID
			LEFT JOIN emOrgUnit P3 ON P1.OrgUnitID = P3.OrgUnitID
			LEFT JOIN emOrgUnit P4 ON P3.ParentOrgUnit = P4.OrgUnitID
			LEFT JOIN hrTimeShift P5 ON P0.ShiftID = P5.ShiftID
		) K0
		JOIN hrTimeTempImport K1 ON K0.EmpID = K1.EmpID AND (DATEADD(dd, 0, DATEDIFF(dd, 0, K1.DateTimeStamp)) BETWEEN '$Date_1' AND '$Date_2')
		JOIN  emOrgUnit K3 ON K0.DeptCode = K3.OrgUnitCode 
		WHERE K1.LocationName IN ('KSY','KSM','KSO') AND K0.EmpCode = '$EmpCode'
		ORDER BY K0.DeptCode, K0.PosLevel, K0.PositionCode, K0.EmpCode, K1.DateTimeStamp";
	$Rows = ChkRowHRMI($SQL1);
	if($Rows > 0) {
		$QRY1 = HRMISelect($SQL1);
		$i = 0;
		$tmpEmpCode = ""; $tmpDate = ""; $tmpTimeStamp = "";
		while($RST1 = odbc_fetch_array($QRY1)) {
			if($tmpEmpCode == "") {
				$tmpEmpCode = $RST1['EmpCode'];
				$arrCol['HEAD']['EmpCode']    = $RST1['EmpCode'];
				$arrCol['HEAD']['FullName']   = conutf8($RST1['FirstName']." ".$RST1['LastName']." (".$RST1['NickName'].")");
				$arrCol['HEAD']['Position']   = conutf8($RST1['PositionName']);
				$arrCol['HEAD']['Department'] = conutf8($RST1['OrgUnitName']);
			}

			if($tmpDate != date("d/m/Y",strtotime($RST1['DateTimeStamp']))) {
				$tmpDate = date("d/m/Y",strtotime($RST1['DateTimeStamp']));
				// $tmpTimeStamp = date("H",strtotime($RST1['DateTimeStamp']));
				$arrCol[$i]['DateStamp'] = date("d/m/Y",strtotime($RST1['DateTimeStamp']));
				$arrCol[$i]['TimeStamp'] = date("H:i",strtotime($RST1['DateTimeStamp']))." น.";
				$arrCol[$i]['Location']  = conutf8($RST1['LocationName']);
				$i++;
			}else{
				// if($tmpTimeStamp != date("H",strtotime($RST1['DateTimeStamp']))) {
				// 	$tmpTimeStamp = date("H",strtotime($RST1['DateTimeStamp']));
				// 	$arrCol[($i-1)]['TimeStamp'] .= " - ".date("H:i",strtotime($RST1['DateTimeStamp']))." น.";
				// }
			}
		}
	}
	$arrCol['Rows'] = $i;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
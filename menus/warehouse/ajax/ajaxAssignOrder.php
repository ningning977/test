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

if($_GET['p'] == "GetSO") {
	$u = $_POST['u'];
	$t = $_POST['t'];

	$WHR1 = "";

	switch($t) {
		case "TT_CrntSo": $WHR1 = "AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND DATE(P0.DatePick) <= DATE(NOW()) AND P0.StatusDoc BETWEEN 1 AND 2"; break;
		case "TT_NextSo": $WHR1 = "AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND DATE(P0.DatePick) > DATE(NOW()) AND P0.StatusDoc BETWEEN 1 AND 2"; break;
		case "TT_PickSo": $WHR1 = "AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc BETWEEN 3 AND 6"; break;
		case "MT_CrntSo": $WHR1 = "AND P0.TeamCode LIKE 'MT%' AND DATE(P0.DatePick) <= DATE(NOW()) AND P0.StatusDoc BETWEEN 1 AND 2"; break;
		case "MT_NextSo": $WHR1 = "AND P0.TeamCode LIKE 'MT%' AND DATE(P0.DatePick) > DATE(NOW()) AND P0.StatusDoc BETWEEN 1 AND 2"; break;
		case "MT_PickSo": $WHR1 = "AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc BETWEEN 3 AND 6"; break;
		case "TT_CrntIV": $WHR1 = "AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DocDueDate) = YEAR(NOW()) AND MONTH(P0.DocDueDate) = MONTH(NOW())) AND P0.StatusDoc = 9"; break;
		case "TT_PackIV": $WHR1 = "AND P1.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc = 10"; break;
		case "MT_CrntIV": $WHR1 = "AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.DocDueDate) = YEAR(NOW()) AND MONTH(P0.DocDueDate) = MONTH(NOW())) AND P0.StatusDoc = 9"; break;
		case "MT_PackIV": $WHR1 = "AND P1.TeamCode LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc = 10"; break;
	}

	switch($t) {
		case "TT_CrntSo": 
		case "TT_NextSo":
		case "TT_PickSo":
		case "MT_CrntSo":
		case "MT_NextSo":
		case "MT_PickSo":
			$SQL1 = 
				"SELECT
					P0.DocNum, P0.DocDate, P0.DocDueDate, P0.CardCode, P0.CardName, P0.ItemCount, P1.SlpName, P0.StatusDoc
				FROM picker_soheader P0
				LEFT JOIN OSLP P1 ON P0.SlpCode = P1.SlpCode
				WHERE P0.ukeypicker = '$u' $WHR1
				ORDER BY P0.DocDueDate";
		break;
		case "TT_CrntIV":
		case "MT_CrntIV":
			$SQL1 = 
				"SELECT
					P0.DocNum, P0.DocDate, P0.DocDueDate, P0.CardCode, P0.CardName, P0.ItemCount, P1.SlpName, P0.StatusDoc
				FROM picker_soheader P0
				LEFT JOIN OSLP P1 ON P0.SlpCode = P1.SlpCode
				WHERE P0.TablePacking = '$u' $WHR1
				ORDER BY P0.DocDueDate";
		break;
		case "TT_PackIV":
		case "MT_PackIV":
			$SQL1 =
				"SELECT
					P0.DocNum, P1.DocDate, P1.DocDueDate, P1.CardCode, P1.CardName, P1.ItemCount, P2.SlpName, P1.StatusDoc
				FROM pack_header P0
				LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID
				LEFT JOIN OSLP P2 ON P1.SlpCode = P2.SlpCode
				WHERE P0.TablePack = '$u' $WHR1
				ORDER BY P1.DocDueDate";
		break;
	}
	// echo $SQL1;
	$ROW1 = ChkRowDB($SQL1);
	if($ROW1 > 0) {
		$QRY1 = MySQLSelectX($SQL1);
		$i = 0;
		while($RST1 = mysqli_fetch_array($QRY1)) {
			if($RST1['CardCode'] == "" || $RST1['CardCode'] == NULL) {
				$arrCol[$i]['CusName'] = $RST1['CardName'];
			} else {
				$arrCol[$i]['CusName'] = $RST1['CardCode']." - ".$RST1['CardName'];
			}

			if($RST1['SlpName'] == "" || $RST1['SlpName'] == NULL) {
				$arrCol[$i]['SlpName'] = "";
			} else {
				$arrCol[$i]['SlpName'] = $RST1['SlpName'];
			}

			$txt_status = "";
			switch($RST1['StatusDoc']) {
				case 2: $txt_status = "รอหยิบสินค้า"; break;
				case 3: $txt_status = "กำลังหยิบสินค้า"; break;
				case 4: $txt_status = "รอตัดสินค้า"; break;
				case 5: $txt_status = "ตัดสินค้าเรียบร้อย"; break;
				case 6: $txt_status = "รอ/แปลงสินค้า"; break;
				case 9: $txt_status = "รอแพ็กสินค้า"; break;
				case 10: $txt_status = "กำลังแพ็กสินค้า"; break;
			}

			$arrCol[$i]['DocNum']     = $RST1['DocNum'];
			$arrCol[$i]['DocDate']    = date("d/m/Y",strtotime($RST1['DocDate']));
			$arrCol[$i]['DocDueDate'] = date("d/m/Y",strtotime($RST1['DocDueDate']));
			$arrCol[$i]['ItemCount']  = $RST1['ItemCount'];
			$arrCol[$i]['StatusDoc']  = $RST1['StatusDoc'];
			$arrCol[$i]['ItemCount']  = number_format($RST1['ItemCount']);
			$arrCol[$i]['StatusTxt']  = $txt_status;
			$i++;
		}
	} 

	$arrCol['Row'] = $ROW1;
}


if($_GET['p'] == "GetData") {
	$SQL1 =
		"SELECT
			T0.ukey, T0.EmpCode, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'FullName',
			/* TT SECTION */
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND DATE(P0.DatePick) <= DATE(NOW()) AND P0.StatusDoc BETWEEN 1 AND 2) AS 'TT_CrntSo',
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND DATE(P0.DatePick) > DATE(NOW()) AND P0.StatusDoc BETWEEN 1 AND 2) AS 'TT_NextSo',
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc BETWEEN 3 AND 6) AS 'TT_PickSo',
			(SELECT IFNULL(SUM(P0.ItemCount),0) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc BETWEEN 3 AND 6) AS 'TT_PickSku',
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc >= 7) AS 'TT_FnshSo',
			(SELECT IFNULL(SUM(P0.ItemCount),0) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc >= 7) AS 'TT_FnshSku',
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW()))) AS 'TT_WorkSo',
			(SELECT IFNULL(SUM(P0.ItemCount),0) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW()))) AS 'TT_WorkSku',
			/* MT SECTION */
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode LIKE 'MT%' AND DATE(P0.DatePick) <= DATE(NOW()) AND P0.StatusDoc BETWEEN 1 AND 2) AS 'MT_CrntSo',
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode LIKE 'MT%' AND DATE(P0.DatePick) > DATE(NOW()) AND P0.StatusDoc BETWEEN 1 AND 2) AS 'MT_NextSo',
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc BETWEEN 3 AND 6) AS 'MT_PickSo',
			(SELECT IFNULL(SUM(P0.ItemCount),0) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc BETWEEN 3 AND 6) AS 'MT_PickSku',
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc >= 7) AS 'MT_FnshSo',
			(SELECT IFNULL(SUM(P0.ItemCount),0) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW())) AND P0.StatusDoc >= 7) AS 'MT_FnshSku',
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW()))) AS 'MT_WorkSo',
			(SELECT IFNULL(SUM(P0.ItemCount),0) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = MONTH(NOW()))) AS 'MT_WorkSku'
		FROM users T0
		WHERE T0.LvCode IN ('LV077') AND (T0.UserStatus = 'A' OR (YEAR(T0.ResignDate) = YEAR(NOW()) AND MONTH(T0.ResignDate) = MONTH(NOW())))";
	$ROW1 = ChkRowDB($SQL1);

	if($ROW1 > 0) {
		$QRY1 = MySQLSelectX($SQL1);
		$i = 0;
		while($RST1 = mysqli_fetch_array($QRY1)) {

			$SQL2 = 
				"SELECT TOP 1
					T0.EmpCode, T1.DateTimeStamp
				FROM emEmployee T0
				JOIN hrTimeTempImport T1 ON T0.EmpID = T1.EmpID
				WHERE T0.WorkingStatus = 'Working' AND 
				DATEADD(dd,0,DATEDIFF(dd,0,T1.DateTimeStamp)) =  DATEADD(dd,0,DATEDIFF(dd,0,GETDATE())) AND 
				T0.EmpCode = '".$RST1['EmpCode']."'";
			$ROW2 = ChkRowHRMI($SQL2);
			if($ROW2 > 0) {
				$arrCol['PICKEMP'][$i]['Online'] = "Y";
			} else {
				$arrCol['PICKEMP'][$i]['Online'] = "N";
			}

			$arrCol['PICKEMP'][$i]['uKey']       = $RST1['ukey'];
			$arrCol['PICKEMP'][$i]['FullName']   = $RST1['FullName'];

			$arrCol['PICKEMP'][$i]['TT_CrntSo']  = $RST1['TT_CrntSo'];
			$arrCol['PICKEMP'][$i]['TT_NextSo']  = $RST1['TT_NextSo'];
			$arrCol['PICKEMP'][$i]['TT_PickSo']  = $RST1['TT_PickSo'];
			$arrCol['PICKEMP'][$i]['TT_PickSku'] = $RST1['TT_PickSku'];
			$arrCol['PICKEMP'][$i]['TT_FnshSo']  = $RST1['TT_FnshSo'];
			$arrCol['PICKEMP'][$i]['TT_FnshSku'] = $RST1['TT_FnshSku'];
			$arrCol['PICKEMP'][$i]['TT_WorkSo']  = $RST1['TT_WorkSo'];
			$arrCol['PICKEMP'][$i]['TT_WorkSku'] = $RST1['TT_WorkSku'];

			$arrCol['PICKEMP'][$i]['MT_CrntSo']  = $RST1['MT_CrntSo'];
			$arrCol['PICKEMP'][$i]['MT_NextSo']  = $RST1['MT_NextSo'];
			$arrCol['PICKEMP'][$i]['MT_PickSo']  = $RST1['MT_PickSo'];
			$arrCol['PICKEMP'][$i]['MT_PickSku'] = $RST1['MT_PickSku'];
			$arrCol['PICKEMP'][$i]['MT_FnshSo']  = $RST1['MT_FnshSo'];
			$arrCol['PICKEMP'][$i]['MT_FnshSku'] = $RST1['MT_FnshSku'];
			$arrCol['PICKEMP'][$i]['MT_WorkSo']  = $RST1['MT_WorkSo'];
			$arrCol['PICKEMP'][$i]['MT_WorkSku'] = $RST1['MT_WorkSku'];

			$arrCol['PICKEMP'][$i]['AL_WorkSo']  = $RST1['TT_WorkSo'] + $RST1['MT_WorkSo'];
			$arrCol['PICKEMP'][$i]['AL_WorkSku'] = $RST1['TT_WorkSku'] + $RST1['MT_WorkSku'];
			$i++;
		}
	}
	$arrCol['PICKEMP']['Row'] = $ROW1;

	$SQL2 =
		"SELECT
			T0.ukey, T0.EmpCode, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'FullName',";
			for($m = 1; $m <= 12; $m++) {
				if($m < 10) {
					$SQL2 .= "(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = ".$m.")) AS 'M0".$m."_WorkSo',
							  (SELECT SUM(P0.ItemCount) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = ".$m.")) AS 'M0".$m."_WorkSku',";
				}else{
					$SQL2 .= "(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = ".$m.")) AS 'M".$m."_WorkSo',
							  (SELECT SUM(P0.ItemCount) FROM picker_soheader P0 WHERE P0.ukeypicker = T0.ukey AND (YEAR(P0.StartPick) = YEAR(NOW()) AND MONTH(P0.StartPick) = ".$m.")) AS 'M".$m."_WorkSku'";
					if($m != 12) {
						$SQL2 .= ",";
					}
				}
			}
	$SQL2 .= "
		FROM users T0
		WHERE T0.LvCode IN ('LV077') AND (T0.UserStatus = 'A' OR (YEAR(T0.ResignDate) = YEAR(NOW())))";
	$ROW2 = ChkRowDB($SQL2);
	if($ROW2 > 0) {
		$QRY2 = MySQLSelectX($SQL2);

		$i = 0;
		while($RST2 = mysqli_fetch_array($QRY2)) {

			$arrCol['PICKMTH'][$i]['uKey']       = $RST2['ukey'];
			$arrCol['PICKMTH'][$i]['FullName']   = $RST2['FullName'];

			for($m = 1; $m <= 12; $m++) {
				if($m < 10) {
					$arrCol['PICKMTH'][$i]['M0'.$m.'_WorkSo']  = $RST2['M0'.$m.'_WorkSo'];
					$arrCol['PICKMTH'][$i]['M0'.$m.'_WorkSku'] = $RST2['M0'.$m.'_WorkSku'];
				} else {
					$arrCol['PICKMTH'][$i]['M'.$m.'_WorkSo']   = $RST2['M'.$m.'_WorkSo'];
					$arrCol['PICKMTH'][$i]['M'.$m.'_WorkSku']  = $RST2['M'.$m.'_WorkSku'];
				}
			}
			$i++;
		}
	}
	$arrCol['PICKMTH']['Row'] = $ROW1;

	$SQL3 =
		"SELECT
			T0.TableID,
			/* TT SECTION */
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.TablePacking = T0.TableID AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DocDueDate) = YEAR(NOW()) AND MONTH(P0.DocDueDate) = MONTH(NOW())) AND P0.StatusDoc = 9) AS 'TT_CrntIV',
			(SELECT IFNULL(SUM(P0.ItemCount),0) FROM picker_soheader P0 WHERE P0.TablePacking = T0.TableID AND P0.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DocDueDate) = YEAR(NOW()) AND MONTH(P0.DocDueDate) = MONTH(NOW())) AND P0.StatusDoc = 9) AS 'TT_CrntSku',
			(SELECT COUNT(P0.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID WHERE P0.TablePack = T0.TableID AND P1.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc = 10) AS 'TT_PackIV',
			(SELECT COUNT(P2.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID LEFT JOIN pack_boxlist P2 ON P0.BillEntry = P2.BillEntry AND P0.BillType = P2.BillType WHERE P0.TablePack = T0.TableID AND P1.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc = 10) AS 'TT_PackBox',
			(SELECT COUNT(P0.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID WHERE P0.TablePack = T0.TableID AND P1.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc > 10) AS 'TT_FnshIV',
			(SELECT COUNT(P2.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID LEFT JOIN pack_boxlist P2 ON P0.BillEntry = P2.BillEntry AND P0.BillType = P2.BillType WHERE P0.TablePack = T0.TableID AND P1.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc > 10) AS 'TT_FnshBox',
			(SELECT COUNT(P0.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID WHERE P0.TablePack = T0.TableID AND P1.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW()))) AS 'TT_WorkIV',
			(SELECT COUNT(P2.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID LEFT JOIN pack_boxlist P2 ON P0.BillEntry = P2.BillEntry AND P0.BillType = P2.BillType WHERE P0.TablePack = T0.TableID AND P1.TeamCode NOT LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW()))) AS 'TT_WorkBox',
			/* MT SECTION */
			(SELECT COUNT(P0.ID) FROM picker_soheader P0 WHERE P0.TablePacking = T0.TableID AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.DocDueDate) = YEAR(NOW()) AND MONTH(P0.DocDueDate) = MONTH(NOW())) AND P0.StatusDoc = 9) AS 'MT_CrntIV',
			(SELECT IFNULL(SUM(P0.ItemCount),0) FROM picker_soheader P0 WHERE P0.TablePacking = T0.TableID AND P0.TeamCode LIKE 'MT%' AND (YEAR(P0.DocDueDate) = YEAR(NOW()) AND MONTH(P0.DocDueDate) = MONTH(NOW())) AND P0.StatusDoc = 9) AS 'MT_CrntSku',
			(SELECT COUNT(P0.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID WHERE P0.TablePack = T0.TableID AND P1.TeamCode LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc = 10) AS 'MT_PackIV',
			(SELECT COUNT(P2.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID LEFT JOIN pack_boxlist P2 ON P0.BillEntry = P2.BillEntry AND P0.BillType = P2.BillType WHERE P0.TablePack = T0.TableID AND P1.TeamCode LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc = 10) AS 'MT_PackBox',
			(SELECT COUNT(P0.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID WHERE P0.TablePack = T0.TableID AND P1.TeamCode LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc > 10) AS 'MT_FnshIV',
			(SELECT COUNT(P2.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID LEFT JOIN pack_boxlist P2 ON P0.BillEntry = P2.BillEntry AND P0.BillType = P2.BillType WHERE P0.TablePack = T0.TableID AND P1.TeamCode LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW())) AND P1.StatusDoc > 10) AS 'MT_FnshBox',
			(SELECT COUNT(P0.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID WHERE P0.TablePack = T0.TableID AND P1.TeamCode LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW()))) AS 'MT_WorkIV',
			(SELECT COUNT(P2.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID LEFT JOIN pack_boxlist P2 ON P0.BillEntry = P2.BillEntry AND P0.BillType = P2.BillType WHERE P0.TablePack = T0.TableID AND P1.TeamCode LIKE 'MT%' AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = MONTH(NOW()))) AS 'MT_WorkBox'
		FROM checkertable T0";
	$ROW3 = ChkRowDB($SQL3);
	if($ROW3 > 0) {
		$QRY3 = MySQLSelectX($SQL3);
		$i = 0;
		while($RST3 = mysqli_fetch_array($QRY3)) {
			$arrCol['PACKEMP'][$i]['TableID']     = $RST3['TableID'];
			$arrCol['PACKEMP'][$i]['FullName']    = "โต๊ะที่ ".$RST3['TableID'];

			$arrCol['PACKEMP'][$i]['TT_CrntIV']   = $RST3['TT_CrntIV'];
			$arrCol['PACKEMP'][$i]['TT_CrntSku']  = $RST3['TT_CrntSku'];
			$arrCol['PACKEMP'][$i]['TT_PackIV']   = $RST3['TT_PackIV'];
			$arrCol['PACKEMP'][$i]['TT_PackBox']  = $RST3['TT_PackBox'];
			$arrCol['PACKEMP'][$i]['TT_FnshIV']   = $RST3['TT_FnshIV'];
			$arrCol['PACKEMP'][$i]['TT_FnshBox']  = $RST3['TT_FnshBox'];
			$arrCol['PACKEMP'][$i]['TT_WorkIV']   = $RST3['TT_WorkIV'];
			$arrCol['PACKEMP'][$i]['TT_WorkBox']  = $RST3['TT_WorkBox'];

			$arrCol['PACKEMP'][$i]['MT_CrntIV']   = $RST3['MT_CrntIV'];
			$arrCol['PACKEMP'][$i]['MT_CrntSku']  = $RST3['MT_CrntSku'];
			$arrCol['PACKEMP'][$i]['MT_PackIV']   = $RST3['MT_PackIV'];
			$arrCol['PACKEMP'][$i]['MT_PackBox']  = $RST3['MT_PackBox'];
			$arrCol['PACKEMP'][$i]['MT_FnshIV']   = $RST3['MT_FnshIV'];
			$arrCol['PACKEMP'][$i]['MT_FnshBox']  = $RST3['MT_FnshBox'];
			$arrCol['PACKEMP'][$i]['MT_WorkIV']   = $RST3['MT_WorkIV'];
			$arrCol['PACKEMP'][$i]['MT_WorkBox']  = $RST3['MT_WorkBox'];

			$arrCol['PACKEMP'][$i]['AL_WorkIV']   = $RST3['TT_WorkIV'] + $RST3['MT_WorkIV'];
			$arrCol['PACKEMP'][$i]['AL_WorkBox']  = $RST3['TT_WorkBox'] + $RST3['MT_WorkBox'];

			$i++;
		}
	}
	$arrCol['PACKEMP']['Row'] = $ROW3;

	$SQL4 = "SELECT T0.TableID,";
		for($m = 1; $m <= 12; $m++) {
			if($m < 10) {
				$SQL4 .= "(SELECT COUNT(P0.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID WHERE P0.TablePack = T0.TableID AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = ".$m.")) AS 'M0".$m."_WorkIV',
						  (SELECT COUNT(P2.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID LEFT JOIN pack_boxlist P2 ON P0.BillEntry = P2.BillEntry AND P0.BillType = P2.BillType WHERE P0.TablePack = T0.TableID AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = ".$m.")) AS 'M0".$m."_WorkSku',";
			}else{
				$SQL4 .= "(SELECT COUNT(P0.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID WHERE P0.TablePack = T0.TableID AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = ".$m.")) AS 'M".$m."_WorkIV',
						  (SELECT COUNT(P2.ID) FROM pack_header P0 LEFT JOIN picker_soheader P1 ON P0.IDPick = P1.ID LEFT JOIN pack_boxlist P2 ON P0.BillEntry = P2.BillEntry AND P0.BillType = P2.BillType WHERE P0.TablePack = T0.TableID AND (YEAR(P0.DateCreate) = YEAR(NOW()) AND MONTH(P0.DateCreate) = ".$m.")) AS 'M".$m."_WorkSku'";
				if($m != 12) {
					$SQL4 .= ",";
				}
			}
		}
	$SQL4 .= " FROM checkertable T0";
	$ROW4 = ChkRowDB($SQL4);
	if($ROW4 > 0) {
		$QRY4 = MySQLSelectX($SQL4);

		$i = 0;
		while($RST4 = mysqli_fetch_array($QRY4)) {

			$arrCol['PACKMTH'][$i]['uKey']       = $RST4['TableID'];
			$arrCol['PACKMTH'][$i]['FullName']   = "โต๊ะที่ ".$RST4['TableID'];

			for($m = 1; $m <= 12; $m++) {
				if($m < 10) {
					if($RST4['M0'.$m.'_WorkIV'] != 0) {
						$arrCol['PACKMTH'][$i]['M0'.$m.'_WorkIV']  = number_format($RST4['M0'.$m.'_WorkIV'],0);
					}else{
						$arrCol['PACKMTH'][$i]['M0'.$m.'_WorkIV']  = "-";
					}
					if($RST4['M0'.$m.'_WorkSku'] != 0) {
						$arrCol['PACKMTH'][$i]['M0'.$m.'_WorkSku'] = number_format($RST4['M0'.$m.'_WorkSku'],0);
					}else{
						$arrCol['PACKMTH'][$i]['M0'.$m.'_WorkSku'] = "-";
					}
				} else {
					if($RST4['M'.$m.'_WorkIV'] != 0) {
						$arrCol['PACKMTH'][$i]['M'.$m.'_WorkIV']  = number_format($RST4['M'.$m.'_WorkIV'],0);
					}else{
						$arrCol['PACKMTH'][$i]['M'.$m.'_WorkIV']  = "-";
					}
					if($RST4['M'.$m.'_WorkSku'] != 0) {
						$arrCol['PACKMTH'][$i]['M'.$m.'_WorkSku'] = number_format($RST4['M'.$m.'_WorkSku'],0);
					}else{
						$arrCol['PACKMTH'][$i]['M'.$m.'_WorkSku'] = "-";
					}
				}
			}
			$i++;
		}
	}
	$arrCol['PACKMTH']['Row'] = $ROW4;
}


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
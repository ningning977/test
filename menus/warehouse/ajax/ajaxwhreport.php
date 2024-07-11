<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
// $output = "";
if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$sql1 = "INSERT INTO uselog SET uKey = '".$uKey."',MenuKey='".$MenuKey."'";
	echo $sql1;
	$addLog = MySQLInsert($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == "FiltUser") {
	$Tab = $_POST['Tab'];
	if($Tab != 4) {
		switch($Tab) {
			case "0": $SQL1 = "SELECT T0.ukey, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'Name' FROM users T0 WHERE T0.LvCode = 'LV077' AND T0.UserStatus = 'A' ORDER BY T0.uName"; break;
			case "1": $SQL1 = "SELECT T0.ukey, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'Name' FROM users T0 WHERE T0.LvCode = 'LV076' AND T0.UserStatus = 'A' ORDER BY T0.uName"; break;
			case "2": $SQL1 = "SELECT T0.ukey, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'Name' FROM users T0 WHERE T0.LvCode = 'LV081' AND T0.UserStatus = 'A' ORDER BY T0.uName"; break;
		}

		$ROW1 = ChkRowDB($SQL1);
		if($ROW1 > 0) {
			$QRY1 = MySQLSelectX($SQL1);
			$i = 0;
			while($RST1 = mysqli_fetch_array($QRY1)) {
				$arrCol[$i]['ukey'] = $RST1['ukey'];
				$arrCol[$i]['Name'] = $RST1['Name'];
				$i++;
			}
		}
	} else {
		$no = 1;
		for($i = 0; $i <= 5; $i++) {
			$arrCol[$i]['ukey'] = $no;
			$arrCol[$i]['Name'] = "โต๊ะที่ ".$no;
			$no++;
		}
		$ROW1 = 5;
	}

	$arrCol['Rows'] = $ROW1;
}

if($_GET['a'] == "GetData") {
	$Tab = $_POST['o'];
	$y   = $_POST['y'];
	$m   = $_POST['m'];

	if(isset($_POST['u'])) {
		$u = $_POST['u'];
	}
	if(isset($_POST['t'])) {
		$t = $_POST['t'];
	}

	

	$loopday = cal_days_in_month(CAL_GREGORIAN, $m, $y);
	$arrCol['LoopDay'] = $loopday;

	$WeekName = array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");

	switch($Tab) {
		case 0:
			$SQL1WHR1 = "";
			$SQL1WHR2 = "";

			$arrCol['SUM_TargetSO']    = 0;
			$arrCol['SUM_TargetSKU']   = 0;
			$arrCol['SUM_ONTIME_SO']   = 0;
			$arrCol['SUM_ONTIME_SKU']  = 0;
			$arrCol['SUM_AFTER_SO']    = 0;
			$arrCol['SUM_AFTER_SKU']   = 0;
			$arrCol['SUM_CANCELED_SO'] = 0;

			for($i = 1; $i <= $loopday; $i++) {
				$WeekDate = date("w",strtotime($y."-".$m."-".$i));
				$arrCol[$i]['Date']        = $i;
				$arrCol[$i]['WeekDate']    = $WeekDate;
				$arrCol[$i]['WeekName']    = $WeekName[$WeekDate];
				$arrCol[$i]['TargetSO']    = null;
				$arrCol[$i]['TargetSKU']   = null;
				$arrCol[$i]['ONTIME_SO']   = null;
				$arrCol[$i]['ONTIME_SKU']  = null;
				$arrCol[$i]['AFTER_SO']    = null;
				$arrCol[$i]['AFTER_SKU']   = null;
				$arrCol[$i]['CANCELED_SO'] = null;
			}

			if($u != "ALL") {
				$SQL1WHR1 = " AND T0.UkeyPicker = '$u'";
			}

			switch($t) {
				case "MT":
				case "TT":
					$SQL1WHR2 = " WHERE A0.TeamCode = '$t'";
				break;
			}

			$SQL1 =
				"SELECT
					B0.PlanDay,
					SUM(B0.TargetSO) AS 'TargetSO', SUM(B0.TargetSKU) AS 'TargetSKU',
					SUM(B0.ONTIME_SO) AS 'ONTIME_SO', SUM(B0.ONTIME_SKU) AS 'ONTIME_SKU',
					SUM(B0.AFTER_SO) AS 'AFTER_SO', SUM(B0.AFTER_SKU) AS 'AFTER_SKU',
					SUM(B0.CANCELED_SO) AS 'CANCELED_SO'
				FROM (
					SELECT
						DAY(A0.DatePick) AS 'PlanDay',
						COUNT(A0.ID) AS 'TargetSO',	SUM(A0.ItemCount) AS 'TargetSKU',
						CASE WHEN A0.PickType = 'ONTIME' THEN COUNT(A0.ID) ELSE 0 END AS 'ONTIME_SO',
						CASE WHEN A0.PickType = 'ONTIME' THEN SUM(A0.ItemCount) ELSE 0 END AS 'ONTIME_SKU',
						CASE WHEN A0.PickType = 'AFTER' THEN COUNT(A0.ID) ELSE 0 END AS 'AFTER_SO',
						CASE WHEN A0.PickType = 'AFTER' THEN SUM(A0.ItemCount) ELSE 0 END AS 'AFTER_SKU',
						CASE WHEN A0.CANCELED = 'Y' THEN COUNT(A0.ID) ELSE 0 END AS 'CANCELED_SO'
					FROM (
						SELECT
							T0.DatePick, IFNULL(DATE(T0.StartPick), DATE(T0.PickedDate)) AS 'StartPick', T0.ID, T0.DocType,
							CASE
								WHEN (T0.DatePick = IFNULL(DATE(T0.StartPick), DATE(T0.PickedDate)) OR T0.DatePick > IFNULL(DATE(T0.StartPick), DATE(T0.PickedDate))) THEN 'ONTIME'
								WHEN (T0.DatePick < IFNULL(DATE(T0.StartPick), DATE(T0.PickedDate)) AND IFNULL(DATE(T0.StartPick), DATE(T0.PickedDate)) IS NOT NULL) THEN 'AFTER'
							ELSE 'NOPICK' END AS 'PickType',
							CASE WHEN T0.TeamCode LIKE 'MT%' THEN 'MT' ELSE 'TT' END AS 'TeamCode',
							T0.ItemCount, T0.StatusDoc, CASE WHEN T0.StatusDoc = 0 THEN 'Y' ELSE 'N' END AS 'CANCELED'
						FROM picker_soheader T0
						LEFT JOIN users T1 ON T0.UkeyPicker = T1.ukey
						WHERE (YEAR(T0.DatePick) = $y AND MONTH(T0.DatePick) = $m) $SQL1WHR1
					) A0
					$SQL1WHR2
					GROUP BY A0.DatePick, A0.PickType
				) B0
				GROUP BY B0.PlanDay
				ORDER BY B0.PlanDay";

			$QRY1 = MySQLSelectX($SQL1);
			while($RST1 = mysqli_fetch_array($QRY1)) {
				$arrCol[$RST1['PlanDay']]['TargetSO']    = number_format($RST1['TargetSO'],0);
				$arrCol[$RST1['PlanDay']]['TargetSKU']   = number_format($RST1['TargetSKU'],0);
				$arrCol[$RST1['PlanDay']]['ONTIME_SO']   = number_format($RST1['ONTIME_SO'],0);
				$arrCol[$RST1['PlanDay']]['ONTIME_SKU']  = number_format($RST1['ONTIME_SKU'],0);
				$arrCol[$RST1['PlanDay']]['AFTER_SO']    = number_format($RST1['AFTER_SO'],0);
				$arrCol[$RST1['PlanDay']]['AFTER_SKU']   = number_format($RST1['AFTER_SKU'],0);
				$arrCol[$RST1['PlanDay']]['CANCELED_SO'] = number_format($RST1['CANCELED_SO'],0);


				$arrCol['SUM_TargetSO']    = $arrCol['SUM_TargetSO'] + $RST1['TargetSO'];
				$arrCol['SUM_TargetSKU']   = $arrCol['SUM_TargetSKU'] + $RST1['TargetSKU'];
				$arrCol['SUM_ONTIME_SO']   = $arrCol['SUM_ONTIME_SO'] + $RST1['ONTIME_SO'];
				$arrCol['SUM_ONTIME_SKU']  = $arrCol['SUM_ONTIME_SKU'] + $RST1['ONTIME_SKU'];
				$arrCol['SUM_AFTER_SO']    = $arrCol['SUM_AFTER_SO'] + $RST1['AFTER_SO'];
				$arrCol['SUM_AFTER_SKU']   = $arrCol['SUM_AFTER_SKU'] + $RST1['AFTER_SKU'];
				$arrCol['SUM_CANCELED_SO'] = $arrCol['SUM_CANCELED_SO'] + $RST1['CANCELED_SO'];
			}
		break;
		case 1:
			$SQL1WHR1 = "";

			$arrCol['SUM_Refill']      = null;
            $arrCol['SUM_Transfer']    = null;

			for($i = 1; $i <= $loopday; $i++) {
				$WeekDate = date("w",strtotime($y."-".$m."-".$i));
				$arrCol[$i]['Date']        = $i;
				$arrCol[$i]['WeekDate']    = $WeekDate;
				$arrCol[$i]['WeekName']    = $WeekName[$WeekDate];
				$arrCol[$i]['Refill']      = null;
            	$arrCol[$i]['Transfer']    = null;
			}

			if($u != "ALL") {
				$SQL1WHR1 = " AND T0.ukeyUpdate = '$u'";
			}

			$SQL1 =
				"SELECT
					A0.Date, SUM(A0.CountRefill) AS 'CountRefill', SUM(A0.CountTransfer) AS 'CountTransfer'
				FROM (
					SELECT
						DAY(T0.DateCreate) AS 'Date',
						CASE WHEN (T0.QtyIn > 0 AND T0.LocationRack LIKE 'A%' OR T0.LocationRack LIKE 'B%' OR T0.LocationRack LIKE 'C6%' OR T0.LocationRack LIKE 'C7%' OR T0.LocationRack LIKE 'C8%') THEN 1 ELSE 0 END AS 'CountRefill',
						CASE WHEN (T0.QtyOut > 0 AND trnCode LIKE 'WH%') THEN 1 ELSE 0 END AS 'CountTransfer'
					FROM transecdata T0
					LEFT JOIN users T1 ON T0.ukeyUpdate = T1.uKey
					WHERE (T1.LvCode = 'LV076' AND T0.StatusTran = 1) AND (YEAR(T0.DateCreate) = $y AND MONTH(T0.DateCreate) = $m) $SQL1WHR1
				) A0
				GROUP BY A0.Date
				ORDER BY A0.Date";
			$QRY1 = MySQLSelectX($SQL1);
			while($RST1 = mysqli_fetch_array($QRY1)) {
				$arrCol[$RST1['Date']]['Refill']    = number_format($RST1['CountRefill'],0);
				$arrCol[$RST1['Date']]['Transfer']  = number_format($RST1['CountTransfer'],0);

				$arrCol['SUM_Refill']      = $arrCol['SUM_Refill'] + $RST1['CountRefill'];
            	$arrCol['SUM_Transfer']    = $arrCol['SUM_Transfer'] + $RST1['CountTransfer'];
			}
		break;
		case 2:
			$SQL1WHR1 = "";

			$arrCol['SUM_IV_MT']       = null;
			$arrCol['SUM_IV_TT']       = null;
			$arrCol['SUM_IV_ALL']      = null;

			for($i = 1; $i <= $loopday; $i++) {
				$WeekDate = date("w",strtotime($y."-".$m."-".$i));
				$arrCol[$i]['Date']        = $i;
				$arrCol[$i]['WeekDate']    = $WeekDate;
				$arrCol[$i]['WeekName']    = $WeekName[$WeekDate];
				$arrCol[$i]['IV_MT']       = null;
            	$arrCol[$i]['IV_TT']       = null;
            	$arrCol[$i]['IV_ALL']      = null;
			}

			if($u != "ALL") {
				$SQL1WHR1 = " AND T0.Ukey = '$u'";
			}

			$SQL1 = "SELECT GROUP_CONCAT(DISTINCT T0.OwnerCode) AS 'OwnerCode' FROM users T0 WHERE T0.LvCode = 'LV081' AND (T0.OwnerCode IS NOT NULL AND T0.OwnerCode != -1) $SQL1WHR1";
			$RST1 = MySQLSelect($SQL1);
			$OwnerCode = $RST1['OwnerCode'];

			$SQL2 =
				"SELECT
					B0.Date, SUM(B0.MT) AS 'MT', SUM(B0.TT) AS 'TT', SUM(B0.MT+B0.TT) AS 'ALL'
				FROM (
					SELECT
						A0.Date,
						CASE WHEN A0.TeamCode = 'MT' THEN COUNT(A0.DocEntry) ELSE 0 END AS 'MT',
						CASE WHEN A0.TeamCode = 'TT' THEN COUNT(A0.DocEntry) ELSE 0 END AS 'TT'
					FROM (
						SELECT
							DAY(T0.CreateDate) AS 'Date', T0.DocEntry,
							CASE WHEN T1.U_Dim1 LIKE 'MT%' THEN 'MT' ELSE 'TT' END AS 'TeamCode'
						FROM OINV T0
						LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
						WHERE T0.OwnerCode IN ($OwnerCode) AND (YEAR(T0.CreateDate) = $y AND MONTH(T0.CreateDate) = $m) AND T0.CANCELED = 'N'
					) A0
					GROUP BY A0.Date, A0.TeamCode
				) B0
				GROUP BY B0.Date
				ORDER BY B0.Date";
			$QRY2 = SAPSelect($SQL2);
			while($RST2 = odbc_fetch_array($QRY2)) {
				$arrCol[$RST2['Date']]['IV_MT']  = number_format($RST2['MT'],0);
				$arrCol[$RST2['Date']]['IV_TT']  = number_format($RST2['TT'],0);
				$arrCol[$RST2['Date']]['IV_ALL'] = number_format($RST2['ALL'],0);

				$arrCol['SUM_IV_MT']  = $arrCol['SUM_IV_MT'] + $RST2['MT'];
				$arrCol['SUM_IV_TT']  = $arrCol['SUM_IV_TT'] + $RST2['TT'];
				$arrCol['SUM_IV_ALL'] = $arrCol['SUM_IV_ALL'] + $RST2['ALL'];
			}
		break;
		case 3:

			for($i = 1; $i <= $loopday; $i++) {
				$WeekDate = date("w",strtotime($y."-".$m."-".$i));
				$arrCol[$i]['Date']        = $i;
				$arrCol[$i]['WeekDate']    = $WeekDate;
				$arrCol[$i]['WeekName']    = $WeekName[$WeekDate];
				$arrCol[$i]['Cnt_PA']      = null;
            	$arrCol[$i]['Cnt_TF']      = null;
            	$arrCol[$i]['Cnt_PD']      = null;
            	$arrCol[$i]['Cnt_SP']      = null;
			}

			$SQL1 = "SELECT GROUP_CONCAT(DISTINCT T0.OwnerCode) AS 'OwnerCode' FROM users T0 WHERE T0.LvCode = 'LV080' AND (T0.OwnerCode IS NOT NULL AND T0.OwnerCode != -1)";
			$RST1 = MySQLSelect($SQL1);
			$OwnerCode = $RST1['OwnerCode'];


			/* PA */
			$SQL2 =
				"SELECT
					DAY(T0.DocDate) AS 'Date', COUNT(T0.DocEntry) AS 'Cnt_PA'
				FROM ODLN T0
				WHERE (YEAR(T0.DocDate) = $y AND MONTH(T0.DocDate) = $m) AND T0.CANCELED = 'N' AND T0.OwnerCode IN ($OwnerCode)
				GROUP BY DAY(T0.DocDate)";
			$QRY2 = SAPSelect($SQL2);
			while($RST2 = odbc_fetch_array($QRY2)) {
				$arrCol[$RST2['Date']]['Cnt_PA'] = $RST2['Cnt_PA'];
			}

			/* TRANSFER */
			$SQL3 =
				"SELECT
					DAY(T0.DocDate) AS 'Date', COUNT(T0.DocEntry) AS 'Cnt_TR'
				FROM OWTR T0
				LEFT JOIN OUSR T1 ON T0.UserSign = T1.USERID
				WHERE (YEAR(T0.DocDate) = $y AND MONTH(T0.DocDate) = $m) AND T0.CANCELED = 'N' AND T1.USER_CODE LIKE 'WH%'
				GROUP BY DAY(T0.DocDate)";
			$QRY3 = SAPSelect($SQL3);
			while($RST3 = odbc_fetch_array($QRY3)) {
				$arrCol[$RST3['Date']]['Cnt_TR'] = $RST3['Cnt_TR'];
			}

			/* PRODUCTION OR CRAFT */
			$KWD1 = SapTHSearch("แปลง");
			$KWD2 = SapTHSearch("ผลิต");
			$KWD3 = SapTHSearch("ถอด");
			$SQL4 =
				"SELECT
					A0.Date, SUM(A0.Cnt_PD) AS 'Cnt_PD', SUM(A0.Cnt_SP) AS 'Cnt_SP'
				FROM (
				SELECT
					DAY(T0.DocDate) AS 'Date',
					CASE WHEN (T1.BeginStr IN ('JA-','JU-') AND (T0.Comments LIKE N'%".$KWD1."%' OR T0.Comments LIKE N'%".$KWD2."%')) THEN 1 ELSE 0 END AS 'Cnt_PD',
					CASE WHEN (T1.BeginStr IN ('JU-') AND (T0.Comments LIKE N'%".$KWD3."%')) THEN 1 ELSE 0 END AS 'Cnt_SP'
				FROM OIGN T0
				LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
				LEFT JOIN OUSR T2 ON T0.UserSign = T2.USERID
				WHERE (YEAR(T0.DocDate) = $y AND MONTH(T0.DocDate) = $m) AND T0.CANCELED = 'N' AND T2.USER_CODE LIKE 'WH%'
				) A0
				GROUP BY A0.Date";
			$QRY4 = SAPSelect($SQL4);
			while($RST4 = odbc_fetch_array($QRY4)) {
				$arrCol[$RST4['Date']]['Cnt_PD'] = $RST4['Cnt_PD'];
				$arrCol[$RST4['Date']]['Cnt_SP'] = $RST4['Cnt_SP'];
			}
		break;
		case 4:
			$SQL1WHR1 = "1,2,3,4,5";
			$arrCol['SUM_MT_Bills']  = 0;
			$arrCol['SUM_MT_Boxes']  = 0;
			$arrCol['SUM_TT_Bills']  = 0;
			$arrCol['SUM_TT_Boxes']  = 0;
			$arrCol['SUM_ALL_Bills'] = 0;
			$arrCol['SUM_ALL_Boxes'] = 0;

			for($i = 1; $i <= $loopday; $i++) {
				$WeekDate = date("w",strtotime($y."-".$m."-".$i));
				$arrCol[$i]['Date']        = $i;
				$arrCol[$i]['WeekDate']    = $WeekDate;
				$arrCol[$i]['WeekName']    = $WeekName[$WeekDate];
				$arrCol[$i]['MT_Bills']    = null;
            	$arrCol[$i]['MT_Boxes']    = null;
				$arrCol[$i]['TT_Bills']    = null;
            	$arrCol[$i]['TT_Boxes']    = null;
				$arrCol[$i]['ALL_Bills']   = null;
            	$arrCol[$i]['ALL_Boxes']   = null;
			}

			if($u != "ALL") {
				$SQL1WHR1 = $u;
			}

			$SQL1 =
				"SELECT
					B0.Date,
					SUM(B0.MT_Bills) AS 'MT_Bills', SUM(B0.MT_Boxes) AS 'MT_Boxes',
					SUM(B0.TT_Bills) AS 'TT_Bills', SUM(B0.TT_Boxes) AS 'TT_Boxes',
					SUM(B0.MT_Bills + B0.TT_Bills) AS 'ALL_Bills', SUM(B0.MT_Boxes + B0.TT_Boxes) AS 'ALL_Boxes'
				FROM (
					SELECT
						A0.Date,
						CASE WHEN A0.TeamCode = 'MT' THEN COUNT(A0.Bills) ELSE 0 END AS 'MT_Bills',
						CASE WHEN A0.TeamCode = 'MT' THEN SUM(A0.Boxes) ELSE 0 END AS 'MT_Boxes',
						CASE WHEN A0.TeamCode = 'TT' THEN COUNT(A0.Bills) ELSE 0 END AS 'TT_Bills',
						CASE WHEN A0.TeamCode = 'TT' THEN SUM(A0.Boxes) ELSE 0 END AS 'TT_Boxes'
					FROM (
					SELECT
						DAY(T0.DateCreate) AS 'Date',
						CASE WHEN T1.TeamCode LIKE 'MT%' THEN 'MT' ELSE 'TT' END AS 'TeamCode',
						T0.ID AS 'Bills',
						(SELECT COUNT(P0.ID) FROM pack_boxlist P0 WHERE P0.BillEntry = T0.BillEntry AND P0.BillType = P0.BillType) AS 'Boxes'
					FROM pack_header T0
					LEFT JOIN picker_soheader T1 ON T0.IDPick = T1.ID
					WHERE T0.Status = 'Y' AND T0.TablePack IN ($SQL1WHR1) AND (YEAR(T0.DateCreate) = $y AND MONTH(T0.DateCreate) = $m)
					) A0
					GROUP BY A0.Date, A0.TeamCode
				) B0
				GROUP BY B0.Date
				ORDER BY B0.Date";
			$QRY1 = MySQLSelectX($SQL1);
			while($RST1 = mysqli_fetch_array($QRY1)) {
				$arrCol[$RST1['Date']]['MT_Bills']  = number_format($RST1['MT_Bills'],0);
				$arrCol[$RST1['Date']]['MT_Boxes']  = number_format($RST1['MT_Boxes'],0);
				$arrCol[$RST1['Date']]['TT_Bills']  = number_format($RST1['TT_Bills'],0);
				$arrCol[$RST1['Date']]['TT_Boxes']  = number_format($RST1['TT_Boxes'],0);
				$arrCol[$RST1['Date']]['ALL_Bills'] = number_format($RST1['ALL_Bills'],0);
				$arrCol[$RST1['Date']]['ALL_Boxes'] = number_format($RST1['ALL_Boxes'],0);

				$arrCol['SUM_MT_Bills']  = $arrCol['SUM_MT_Bills'] + $RST1['MT_Bills'];
				$arrCol['SUM_MT_Boxes']  = $arrCol['SUM_MT_Boxes'] + $RST1['MT_Boxes'];
				$arrCol['SUM_TT_Bills']  = $arrCol['SUM_TT_Bills'] + $RST1['TT_Bills'];
				$arrCol['SUM_TT_Boxes']  = $arrCol['SUM_TT_Boxes'] + $RST1['TT_Boxes'];
				$arrCol['SUM_ALL_Bills'] = $arrCol['SUM_ALL_Bills'] + $RST1['ALL_Bills'];
				$arrCol['SUM_ALL_Boxes'] = $arrCol['SUM_ALL_Boxes'] + $RST1['ALL_Boxes'];
			}
		break;
		case 5:
			for($i = 1; $i <= $loopday; $i++) {
				$WeekDate = date("w",strtotime($y."-".$m."-".$i));
				$arrCol[$i]['Date']     = $i;
				$arrCol[$i]['WeekDate'] = $WeekDate;
				$arrCol[$i]['WeekName'] = $WeekName[$WeekDate];
				$arrCol[$i]['Cars']     = null;
            	$arrCol[$i]['Bills']    = null;
            	$arrCol[$i]['Boxes']    = null;
			}
			$SQL1 =
				"SELECT
					A0.Date, SUM(A0.Car) AS 'Car', SUM(A0.Bill) AS 'Bill', SUM(A0.Box) AS 'Box'
				FROM (
					SELECT
						DAY(T1.LoadDate) AS 'Date',
						COUNT(DISTINCT T0.LogiNum) AS 'Car',
						COUNT(DISTINCT T0.BillEntry) AS 'Bill',
						COUNT(T0.BoxCode) AS 'Box'
					FROM logi_detail T0
					LEFT JOIN logi_head T1 ON T0.LogiNum = T1.LogiNum
					WHERE (YEAR(T1.LoadDate) = $y AND MONTH(T1.LoadDate) = $m) AND (T1.Status = 3 AND T0.Status = 2)
					GROUP BY T1.LoadDate
				) A0
				GROUP BY A0.Date";
			$QRY1 = MySQLSelectX($SQL1);
			while($RST1 = mysqli_fetch_array($QRY1)) {
				$arrCol[$RST1['Date']]['Cars']  = $RST1['Car'];
            	$arrCol[$RST1['Date']]['Bills'] = $RST1['Bill'];
            	$arrCol[$RST1['Date']]['Boxes'] = $RST1['Box'];
			}

		break;
	}
}
/*
if ($_GET['a'] == 'CallData') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];

	// เบิก (PIK)
		$GenSoQty = array(); $GenSoSku = array();
		$PikSoQty = array(); $PikSoSku = array();
		$CutSoQty = array();
		$CanSoQty = array();
		for($i = 1; $i <= GetLastDate($Year, $Month); $i++) {
			$GenSoQty[$i] = 0; $GenSoSku[$i] = 0;
			$PikSoQty[$i] = 0; $PikSoSku[$i] = 0;
			$CutSoQty[$i] = 0;
			$CanSoQty[$i] = 0;
		}
		$SQLPIK1 = "SELECT A0.Date, COUNT(A0.SoDocEntry) AS 'SoQty', SUM(A0.ItemCount) AS 'SoSKU'
					FROM(
						SELECT DISTINCT DAY(T0.DateCreate) AS 'Date', T0.SODocEntry, T0.ItemCount
						FROM picker_soheader T0
						WHERE YEAR(T0.DateCreate) = $Year AND MONTH(T0.DateCreate) = $Month
						) A0 
					GROUP BY A0.Date";
		$QRYPIK1 = MySQLSelectX($SQLPIK1);
		while($resultPIK1 = mysqli_fetch_array($QRYPIK1)) {
			$GenSoQty[$resultPIK1['Date']] = $resultPIK1['SoQty'];
			$GenSoSku[$resultPIK1['Date']] = $resultPIK1['SoSKU'];
		}

		$SQLPIK2 = "SELECT A0.Date, COUNT(A0.trnCode) AS 'SoQty', SUM(A1.ItemCount) AS 'SoSKU'
					FROM(
						SELECT DISTINCT DAY(T0.DateCreate) AS 'Date', T0.trnCode
						FROM transecdata T0
						WHERE YEAR(T0.DateCreate) = $Year AND MONTH(T0.DateCreate) = $Month AND T0.QtyOut > 0 AND (T0.trnCode IS NOT NULL AND T0.trnCode NOT LIKE 'WH-%')
						) A0
					LEFT JOIN picker_soheader A1 ON A0.trnCode = A1.SoDocEntry
					GROUP BY A0.Date";
		$QRYPIK2 = MySQLSelectX($SQLPIK2);
		while($resultPIK2 = mysqli_fetch_array($QRYPIK2)) {
			$PikSoQty[$resultPIK2['Date']] = $resultPIK2['SoQty'];
			$PikSoSku[$resultPIK2['Date']] = $resultPIK2['SoSKU'];
		}

		$SQLPIK3 = "SELECT A0.Date, COUNT(A0.SoDocEntry) AS 'SoQty'
					FROM(
						SELECT DAY(T0.TimeCut1) AS 'Date', T0.SODocEntry
						FROM picker_soheader T0
						WHERE YEAR(T0.DateCreate) = $Year AND MONTH(T0.DateCreate) = $Month AND T0.TimeCut1 IS NOT NULL
						GROUP BY T0.DateCreate
						) A0 
					GROUP BY A0.Date";
		$QRYPIK3 = MySQLSelectX($SQLPIK3);
		while($resultPIK3 = mysqli_fetch_array($QRYPIK3)) {
			$CutSoQty[$resultPIK3['Date']] = $resultPIK3['SoQty'];
		}

		$SQLPIK4 = "SELECT A0.Date, COUNT(A0.SoDocEntry) AS 'SoQty'
					FROM(
						SELECT DAY(T0.LastUpDate) AS 'Date', T0.SODocEntry
						FROM picker_soheader T0
						WHERE YEAR(T0.DateCreate) = $Year AND MONTH(T0.DateCreate) = $Month AND T0.TimeCut1 IS NOT NULL AND T0.StatusDoc = 0
						GROUP BY T0.DateCreate
						) A0 
					GROUP BY A0.Date";
		$QRYPIK4 = MySQLSelectX($SQLPIK4);
		while($resultPIK4 = mysqli_fetch_array($QRYPIK4)) {
			$CanSoQty[$resultPIK4['Date']] = $resultPIK4['SoQty'];
		}

		$TbodyPIK = "";
		$TfootPIK = "";
		$SUM_GenSoQty = 0; $SUM_GenSoSku = 0; $SUM_PikSoQty = 0; $SUM_PikSoSku = 0; $SUM_CutSoQty = 0; $SUM_CanSoQty = 0;
		$thai_day_arr = array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
		for($d = 1; $d <= GetLastDate($Year, $Month); $d++) {
			$thai_date = thai_date(strtotime($Year."-".$Month."-".$d));
			if($thai_date == "อาทิตย์") { $class = " class='text-danger table-danger'"; } else { $class = NULL; }
			$TbodyPIK.="<tr".$class.">
						<td class='text-left'>".$thai_date."</td>
						<td class='text-center'>".$d."</td>";
						if($GenSoQty[$d] != 0) { $TbodyPIK .= "<td class='text-right'>".number_format($GenSoQty[$d],0)."</td>"; }else{ $TbodyPIK .= "<td class='text-right'>-</td>"; }
						if($GenSoSku[$d] != 0) { $TbodyPIK .= "<td class='text-right'>".number_format($GenSoSku[$d],0)."</td>"; }else{ $TbodyPIK .= "<td class='text-right'>-</td>"; }
						if($PikSoQty[$d] != 0) { $TbodyPIK .= "<td class='text-right'>".number_format($PikSoQty[$d],0)."</td>"; }else{ $TbodyPIK .= "<td class='text-right'>-</td>"; }
						if($PikSoSku[$d] != 0) { $TbodyPIK .= "<td class='text-right'>".number_format($PikSoSku[$d],0)."</td>"; }else{ $TbodyPIK .= "<td class='text-right'>-</td>"; }
						if($CutSoQty[$d] != 0) { $TbodyPIK .= "<td class='text-right'>".number_format($CutSoQty[$d],0)."</td>"; }else{ $TbodyPIK .= "<td class='text-right'>-</td>"; }
						if($CanSoQty[$d] != 0) { $TbodyPIK .= "<td class='text-right'>".number_format($CanSoQty[$d],0)."</td>"; }else{ $TbodyPIK .= "<td class='text-right'>-</td>"; }
			$TbodyPIK.="</tr>";
			$SUM_GenSoQty = $SUM_GenSoQty + $GenSoQty[$d];
			$SUM_GenSoSku = $SUM_GenSoSku + $GenSoSku[$d];
			$SUM_PikSoQty = $SUM_PikSoQty + $PikSoQty[$d];
			$SUM_PikSoSku = $SUM_PikSoSku + $PikSoSku[$d];
			$SUM_CutSoQty = $SUM_CutSoQty + $CutSoQty[$d];
			$SUM_CanSoQty = $SUM_CanSoQty + $CanSoQty[$d];
		}
		$TfootPIK.="<tr class='fw-bolder'>
					<td colspan='2' class='text-center'>รวมทั้งหมด</td>";
					if($SUM_GenSoQty != 0) { $TfootPIK .= "<td class='text-right'>".number_format($SUM_GenSoQty,0)."</td>"; }else{ $TfootPIK .= "<td class='text-right'>-</td>"; }
					if($SUM_GenSoSku != 0) { $TfootPIK .= "<td class='text-right'>".number_format($SUM_GenSoSku,0)."</td>"; }else{ $TfootPIK .= "<td class='text-right'>-</td>"; }
					if($SUM_PikSoQty != 0) { $TfootPIK .= "<td class='text-right'>".number_format($SUM_PikSoQty,0)."</td>"; }else{ $TfootPIK .= "<td class='text-right'>-</td>"; }
					if($SUM_PikSoSku != 0) { $TfootPIK .= "<td class='text-right'>".number_format($SUM_PikSoSku,0)."</td>"; }else{ $TfootPIK .= "<td class='text-right'>-</td>"; }
					if($SUM_CutSoQty != 0) { $TfootPIK .= "<td class='text-right'>".number_format($SUM_CutSoQty,0)."</td>"; }else{ $TfootPIK .= "<td class='text-right'>-</td>"; }
					if($SUM_CanSoQty != 0) { $TfootPIK .= "<td class='text-right'>".number_format($SUM_CanSoQty,0)."</td>"; }else{ $TfootPIK .= "<td class='text-right'>-</td>"; }
		$TfootPIK.="</tr>";

		$arrCol['TbodyPIK'] = $TbodyPIK;
		$arrCol['TfootPIK'] = $TfootPIK;

	// เติม (RFL)
		$SQLRFL1 = "SELECT DISTINCT T0.ukeyUpdate 
					FROM transecdata T0 
					LEFT JOIN users T1 ON T0.ukeyUpdate = T1.uKey  
					WHERE T1.LvCode = 'LV076' AND (YEAR(T0.DateCreate) = $Year AND MONTH(T0.DateCreate) = $Month) ORDER BY T1.uName";
		$QRYRFL1 = MySQLSelectX($SQLRFL1);
		$loopuser = array();
		while ($resultRFL = mysqli_fetch_array($QRYRFL1)) {
			array_push($loopuser, $resultRFL['ukeyUpdate']);
		}

		$nameRFL = array();
		$refillRFL = array();
		$transfRFL = array();
		foreach($loopuser as $users) { 
			for($i = 1; $i <= GetLastDate($Year, $Month); $i++) {
				$refillRFL[$users][$i] = 0;
				$transfRFL[$users][$i] = 0;
			}
		}
		foreach($loopuser as $users) {
			$SQLRFL2 = "SELECT DISTINCT A0.Date, A0.ukeyUpdate, CONCAT(A1.uName ,' ',A1.uLastName ,' (',A1.uNickName ,')') AS 'FullName', SUM(A0.CountRefill) AS 'CountRefill', SUM(A0.CountTransfer) AS 'CountTransfer'
						FROM (
								SELECT DAY(T0.DateCreate) AS 'Date', T0.ukeyUpdate,
									CASE WHEN (T0.QtyIn > 0 AND T0.LocationRack LIKE 'A%' OR T0.LocationRack LIKE 'B%' OR T0.LocationRack LIKE 'C6%' OR T0.LocationRack LIKE 'C7%' OR T0.LocationRack LIKE 'C8%') THEN 1 ELSE 0 END AS 'CountRefill',
									CASE WHEN (T0.QtyOut > 0 AND trnCode LIKE 'WH%') THEN 1 ELSE 0 END AS 'CountTransfer'
								FROM transecdata T0
								LEFT JOIN users T1 ON T0.ukeyUpdate = T1.uKey 
								WHERE T1.LvCode  = 'LV076' AND T0.ukeyUpdate = '$users' AND (YEAR(T0.DateCreate) = $Year AND MONTH(T0.DateCreate) = $Month)
						) A0
						LEFT JOIN users A1 ON A0.ukeyUpdate = A1.uKey 
						GROUP BY A0.Date, A0.ukeyUpdate, CONCAT(A1.uName ,' ',A1.uLastName ,' (',A1.uNickName ,')')
						ORDER BY A0.ukeyUpdate";
			$QRYRFL2 = MySQLSelectX($SQLRFL2);
			while($resultRFL2 = mysqli_fetch_array($QRYRFL2)) {
				$nameRFL[$resultRFL2['ukeyUpdate']] = $resultRFL2['FullName'];
				$refillRFL[$resultRFL2['ukeyUpdate']][$resultRFL2['Date']] = $resultRFL2['CountRefill'];
				$transfRFL[$resultRFL2['ukeyUpdate']][$resultRFL2['Date']] = $resultRFL2['CountTransfer'];
			}
		}
		$TheadRFL = ""; $TbodyRFL = ""; $TfootRFL = "";
		$TheadRFL.="<tr class='text-center'>
						<th width='7.5%' rowspan='2' class='align-bottom'>วัน</th>
						<th width='7.5%' rowspan='2' class='align-bottom'>วันที่</th>";
						foreach($loopuser as $users) { $TheadRFL.="<th width='' colspan='2'>".$nameRFL[$users]."</th>";}
						$TheadRFL.="<th width='' colspan='2'>รวมทั้งหมด</th>";
		$TheadRFL.="</tr>";
		$TheadRFL.="<tr class='text-center'>";
						foreach($loopuser as $users) { 
							$TheadRFL.="<th width=''>เติม</th>";
							$TheadRFL.="<th width=''>โอนย้าย</th>";
						}
						$TheadRFL.="<th width=''>เติม</th>";
						$TheadRFL.="<th width=''>โอนย้าย</th>";
		$TheadRFL.="</tr>";

		$SUMrefill = array(); $SUMtransf = array();
		foreach($loopuser as $users) { 
			$SUMrefill[$users] = 0;
			$SUMtransf[$users] = 0;
		}
		$TotalRefill = 0; $TotalTransf = 0;
		$ALLRefill = 0; $ALLTransf = 0;
		for($d = 1; $d <= GetLastDate($Year, $Month); $d++) {
			$thai_date = thai_date(strtotime($Year."-".$Month."-".$d));
			if($thai_date == "อาทิตย์") { $class = " class='text-danger table-danger'"; } else { $class = NULL; }
			$TbodyRFL.="<tr".$class.">
							<td>".$thai_date."</td>
							<td class='text-center'>".$d."</td>";
							foreach($loopuser as $users) { 
								if($refillRFL[$users][$d] != 0) { $TbodyRFL.="<td class='text-right'>".number_format($refillRFL[$users][$d],0)."</td>"; }else{ $TbodyRFL.="<td class='text-right'>-</td>"; }
								if($transfRFL[$users][$d] != 0) { $TbodyRFL.="<td class='text-right'>".number_format($transfRFL[$users][$d],0)."</td>"; }else{ $TbodyRFL.="<td class='text-right'>-</td>"; }
								
								$SUMrefill[$users] = $SUMrefill[$users]+$refillRFL[$users][$d];
								$SUMtransf[$users] = $SUMtransf[$users]+$transfRFL[$users][$d];

								$TotalRefill = $TotalRefill+$refillRFL[$users][$d];
								$TotalTransf = $TotalTransf+$transfRFL[$users][$d];
							}
							if($TotalRefill != 0) { $TbodyRFL.="<td class='text-right'>".number_format($TotalRefill,0)."</td>"; }else{ $TbodyRFL.="<td class='text-right'>-</td>"; }
							if($TotalTransf != 0) { $TbodyRFL.="<td class='text-right'>".number_format($TotalTransf,0)."</td>"; }else{ $TbodyRFL.="<td class='text-right'>-</td>"; }

							$ALLRefill = $ALLRefill + $TotalRefill;
                    		$ALLTransf = $ALLTransf + $TotalTransf;
							$TotalRefill = 0; $TotalTransf = 0;
			$TbodyRFL.="</tr>";
		}
		$TfootRFL.="<tr class='fw-bolder'>
						<td class='text-center' colspan='2'>รวมทั้งหมด</td>";
						foreach($loopuser as $users) {
							if($SUMrefill[$users] != 0) { $TfootRFL.="<td class='text-right'>".number_format($SUMrefill[$users],0)."</td>"; }else{ $TfootRFL.="<td class='text-right'>-</td>"; }
							if($SUMtransf[$users] != 0) { $TfootRFL.="<td class='text-right'>".number_format($SUMtransf[$users],0)."</td>"; }else{ $TfootRFL.="<td class='text-right'>-</td>"; }
						}
						if($ALLRefill != 0) { $TfootRFL.="<td class='text-right'>".number_format($ALLRefill,0)."</td>"; }else{ $TfootRFL.="<td class='text-right'>-</td>"; }
						if($ALLTransf != 0) { $TfootRFL.="<td class='text-right'>".number_format($ALLTransf,0)."</td>"; }else{ $TfootRFL.="<td class='text-right'>-</td>"; }
		$TfootRFL.="</tr>";

		$arrCol['TheadRFL'] = $TheadRFL;
		$arrCol['TbodyRFL'] = $TbodyRFL;
		$arrCol['TfootRFL'] = $TfootRFL;

	// เปิดบิล (INV)
		$loopuser = array(51,92);
		$BillMT = array(); $BillTT = array();

		$Name[$loopuser[0]] = ""; $Name[$loopuser[1]] = "";
		for($i = 1; $i <= GetLastDate($Year, $Month); $i++) {
			$BillMT[$loopuser[0]][$i] = ""; $BillTT[$loopuser[0]][$i] = "";
			$BillMT[$loopuser[1]][$i] = ""; $BillTT[$loopuser[1]][$i] = "";
		}

		foreach($loopuser as $users){
			$SQLINV =  "SELECT DISTINCT A0.Date, A0.OwnerCode, A0.FullName, SUM(A0.BillMT) AS 'BillMT', SUM(A0.BillTT) AS 'BillTT'
				FROM (
					SELECT DAY(T0.CreateDate) AS 'Date', T0.OwnerCode,(T1.lastname+' '+T1.firstname) AS 'FullName',
						CASE WHEN T2.U_Dim1 LIKE 'MT%' THEN COUNT(T0.DocEntry) ELSE 0 END AS 'BillMT',
						CASE WHEN T2.U_Dim1 NOT LIKE 'MT%' THEN COUNT(T0.DocEntry) ELSE 0 END AS 'BillTT'
					FROM OINV T0
					LEFT JOIN OHEM T1 ON T0.OwnerCode = T1.empID
					LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
					WHERE T0.OwnerCode = '$users' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = $Month)
					GROUP BY T0.CreateDate, T0.OwnerCode, T1.lastname, T1.firstname, T2.U_Dim1
				) A0
				GROUP BY A0.Date, A0.OwnerCode, A0.FullName
				ORDER BY A0.Date ASC";
			$QRYINV = SAPSelect($SQLINV);
			while($resultINV = odbc_fetch_array($QRYINV)) {
				$Name[$resultINV['OwnerCode']] = conutf8($resultINV['FullName']);
				$BillMT[$resultINV['OwnerCode']][$resultINV['Date']] = $resultINV['BillMT'];
				$BillTT[$resultINV['OwnerCode']][$resultINV['Date']] = $resultINV['BillTT'];
			}

		}
		$TheadINV = "<tr class='text-center'>
						<th width='7.5%' rowspan='2' class='align-bottom'>วัน</th>
						<th width='7.5%' rowspan='2' class='align-bottom'>วันที่</th>";
						foreach($loopuser as $users) {
							$TheadINV .= "<th colspan='3'>".$Name[$users]."</th>";
						}
						$TheadINV .="<th  colspan='3'>รวมทั้งหมด</th>
					</tr>
					<tr class='text-center'>";
						foreach($loopuser as $users) {
							$TheadINV.="<th>MT</th>
										<th>TT</th>
										<th class='active'>รวม</th>";
						}
						$TheadINV .= "<th>MT</th>
						<th>TT</th>
						<th class='active'>รวม</th>
					</tr>";
					
		$TbodyINV = ""; $TfootINV = "";
		$TotalMT = 0; $TotalTT = 0;
		$ALL = array(); 
		$ALL['BillMT'][$loopuser[0]] = 0; $ALL['BillMT'][$loopuser[1]] = 0; 
		$ALL['BillTT'][$loopuser[0]] = 0; $ALL['BillTT'][$loopuser[1]] = 0; 
		$ALL['TOTAL'][$loopuser[0]] = 0;  $ALL['TOTAL'][$loopuser[1]] = 0;
		$ALLMT = 0; $ALLTT = 0;
		for($d = 1; $d <= GetLastDate($Year, $Month); $d++) {
			$thai_date = thai_date(strtotime($Year."-".$Month."-".$d));
			if($thai_date == "อาทิตย์") { $class = " class='text-danger table-danger'"; } else { $class = NULL; }
				$TbodyINV .="<tr".$class.">
								<td>".$thai_date."</td>
								<td class='text-center'>".$d."</td>";
								foreach($loopuser as $users) {
									if($BillMT[$users][$d] == '' || $BillMT[$users][$d] == 0) {
										$BillMT[$users][$d] = 0;
										$TbodyINV .= "<td class='text-right'>-</td>";
									}else{
										$TbodyINV .= "<td class='text-right'>".number_format($BillMT[$users][$d],0)."</td>";
									}
									if($BillTT[$users][$d] == '' || $BillTT[$users][$d] == 0) {
										$BillTT[$users][$d] = 0;
										$TbodyINV .= "<td class='text-right'>-</td>";
									}else{
										$TbodyINV .= "<td class='text-right'>".number_format($BillTT[$users][$d],0)."</td>";
									}
									if(($BillMT[$users][$d] + $BillTT[$users][$d]) != 0) { $TbodyINV .= "<td class='text-right fw-bolder'>".number_format(($BillMT[$users][$d] + $BillTT[$users][$d]),0)."</td>"; }else{ $TbodyINV .= "<td class='text-right'>-</td>"; }

									$TotalMT = $TotalMT + $BillMT[$users][$d];
									$TotalTT = $TotalTT + $BillTT[$users][$d];

									$ALL['BillMT'][$users] = $ALL['BillMT'][$users] + $BillMT[$users][$d];
									$ALL['BillTT'][$users] = $ALL['BillTT'][$users] + $BillTT[$users][$d];
									$ALL['TOTAL'][$users]  = $ALL['TOTAL'][$users] + ($BillMT[$users][$d] + $BillTT[$users][$d]);
								}
								if($TotalMT != 0) { $TbodyINV .="<td class='text-right'>".number_format($TotalMT)."</td>"; }else{ $TbodyINV .="<td class='text-right'>-</td>"; }
								if($TotalTT != 0) { $TbodyINV .="<td class='text-right'>".number_format($TotalTT)."</td>"; }else{ $TbodyINV .="<td class='text-right'>-</td>"; }
								if(($TotalMT+$TotalTT) != 0) { $TbodyINV .="<td class='text-right fw-bolder'>".number_format(($TotalMT+$TotalTT))."</td>"; }else{ $TbodyINV .="<td class='text-right'>-</td>"; }
				$TbodyINV .= "</tr>";
			$ALLMT = $ALLMT + $TotalMT;
			$ALLTT = $ALLTT + $TotalTT;
			$TotalMT = 0; $TotalTT = 0;
		}
		$TfootINV .="<tr>
						<td class='text-center' colspan='2'>รวมทั้งหมด</td>";
						foreach($loopuser as $users) {
							if($ALL['BillMT'][$users] != 0) { $TfootINV .="<td class='text-right'>".number_format($ALL['BillMT'][$users],0)."</td>"; }else{ $TfootINV .="<td class='text-right'>-</td>"; }
							if($ALL['BillTT'][$users] != 0) { $TfootINV .="<td class='text-right'>".number_format($ALL['BillTT'][$users],0)."</td>"; }else{ $TfootINV .="<td class='text-right'>-</td>"; }
							if($ALL['TOTAL'][$users] != 0)  { $TfootINV .="<td class='text-right'>".number_format($ALL['TOTAL'][$users],0)."</td>"; }else{ $TfootINV .="<td class='text-right'>-</td>"; }
						}
						if($ALLMT != 0) { $TfootINV .="<td class='text-right'>".number_format($ALLMT,0)."</td>"; }else{ $TfootINV .="<td class='text-right'>-</td>"; }
						if($ALLTT != 0) { $TfootINV .="<td class='text-right'>".number_format($ALLTT,0)."</td>"; }else{ $TfootINV .="<td class='text-right'>-</td>"; }
						if(($ALLMT+$ALLTT) != 0) { $TfootINV .="<td class='text-right'>".number_format(($ALLMT+$ALLTT),0)."</td>"; }else{ $TfootINV .="<td class='text-right'>-</td>"; }
		$TfootINV .="</tr>";

		$arrCol['TheadINV'] = $TheadINV;
		$arrCol['TbodyINV'] = $TbodyINV;
		$arrCol['TfootINV'] = $TfootINV;
	// แพ็ค (PAK)
		$loopuser = array(1,2,3,4,5);
		$namePAK = array();
		$pMTBILL = array();
		$pMTBOX = array();
		$pTTBILL = array();
		$pTTBOX = array();
			foreach($loopuser as $users) { 
				for($i = 1; $i <= GetLastDate($Year, $Month); $i++) {
					$pMTBILL[$users][$i] = 0;
					$pMTBOX[$users][$i] = 0;
					$pTTBILL[$users][$i] = 0;
					$pTTBOX[$users][$i] = 0;
				}
				$namePAK[$users] = "โต๊ะที่: ".$users;
			}
		foreach($loopuser as $users) {
			$SQLPAK1 = "SELECT B0.Date, B0.TablePack, 
								SUM(B0.MT_Bill) AS 'MT_Bill', SUM(B0.MT_Box) AS 'MT_Box',
								SUM(B0.TT_Bill) AS 'TT_Bill', SUM(B0.TT_Box) AS 'TT_Box'
						FROM (
								SELECT A0.Date, A0.TablePack,
									CASE WHEN A0.TeamCode = 'MT' THEN COUNT(A0.BillEntry) ELSE 0 END AS 'MT_Bill',
									CASE WHEN A0.TeamCode = 'MT' THEN SUM(A0.CountBox) ELSE 0 END AS 'MT_Box',
									CASE WHEN A0.TeamCode = 'TT' THEN COUNT(A0.BillEntry) ELSE 0 END AS 'TT_Bill',
									CASE WHEN A0.TeamCode = 'TT' THEN SUM(A0.CountBox) ELSE 0 END AS 'TT_Box'
								FROM (
										SELECT DAY(T0.DateCreate) AS 'Date', T0.TablePack, 
											CASE WHEN T1.TeamCode LIKE 'MT%' THEN 'MT' ELSE 'TT' END AS 'TeamCode', T0.BillEntry, T0.TablePack AS 'CountBox'
										FROM pack_header T0
										LEFT JOIN picker_soheader T1 ON T0.IDPick = T1.ID
										WHERE T0.TablePack = ".$users." AND (YEAR(T0.DateCreate) = ".$Year." AND MONTH(T0.DateCreate) = ".$Month.")
										GROUP BY T0.DateCreate, T0.TablePack, T1.TeamCode
									) A0
										GROUP BY A0.Date, A0.TablePack, A0.TeamCode
						) B0 
						GROUP BY B0.Date, B0.TablePack";
			$QRYPAK1 = MySQLSelectX($SQLPAK1);
			
			while ($resultPAK = mysqli_fetch_array($QRYPAK1)) {
				$namePAK[$users] = "โต๊ะที่: ".$resultPAK['TablePack'];
				$pMTBILL[$resultPAK["TablePack"]][$resultPAK["Date"]] = $resultPAK["MT_Bill"];
				$pMTBOX[$resultPAK["TablePack"]][$resultPAK["Date"]] = $resultPAK["MT_Box"];
				$pTTBILL[$resultPAK["TablePack"]][$resultPAK["Date"]] = $resultPAK["TT_Bill"];
				$pTTBOX[$resultPAK["TablePack"]][$resultPAK["Date"]] = $resultPAK["TT_Box"];
			}
		}
		$TheadPAK = ""; 
		$TbodyPAK = ""; 
		$TfootPAK = "";

		$TheadPAK .="<tr class='text-center'>
						<th width='5%' rowspan='3' class='align-bottom'>วัน</th>
						<th width='2.5%' rowspan='3' class='align-bottom'>วันที่</th>";
						foreach($loopuser as $users) {
							$TheadPAK .="<th width='' colspan='6'>".$namePAK[$users]."</th>";
						}
						$TheadPAK .="<th width='' colspan='6'>รวมทั้งหมด</th>
					</tr>
					<tr class='text-center'>";
						foreach($loopuser as $users) {
							$TheadPAK .= "<th colspan='2'>MT</th>
							<th colspan='2'>TT</th>
							<th class='active' colspan='2'>รวม</th>";
						}
						$TheadPAK .="<th colspan='2'>MT</th>
						<th colspan='2'>TT</th>
						<th class='active' colspan='2'>รวม</th>
					</tr>
					<tr class='text-center'>";
						foreach($loopuser as $users) {
							$TheadPAK .= "<th>บิล</th>
							<th>ลัง</th>
							<th>บิล</th>
							<th>ลัง</th>
							<th>บิล</th>
							<th>ลัง</th>";
						}
						$TheadPAK .="<th>บิล</th>
									<th>ลัง</th>
									<th>บิล</th>
									<th>ลัง</th>
									<th>บิล</th>
									<th>ลัง</th>
					</tr>";	

		// $TotalMT = 0; $TotalTT = 0;
		$All_PAK = array(); 

		foreach($loopuser as $users) { 
			$All_PAK['BillMT_PAK'][$users] = 0; 
			$All_PAK['BillTT_PAK'][$users] = 0; 
			$All_PAK['BoxMT_PAK'][$users] = 0; 
			$All_PAK['BoxTT_PAK'][$users] = 0; 
			$All_PAK_BillTotal['BillTotal_PAK'][$users] = 0; 
			$All_PAK_BoxTotal['BoxTotal_PAK'][$users] = 0;
		}
		$TotalMTBill_PAK = 0;
		$TotalMTBox_PAK = 0;
		$TotalTTBill_PAK = 0;
		$TotalTTBox_PAK = 0;
		
		
		for($d = 1; $d <= GetLastDate($Year,$Month); $d++) {
			$thai_date = thai_date(strtotime($Year."-".$Month."-".$d));
			if($thai_date == "อาทิตย์") { $class = " class='text-danger table-danger'"; } else { $class = NULL; }	
			$TbodyPAK .= "<tr".$class.">
							<td>".$thai_date."</td>
							<td class='text-center'>".$d."</td>";
							foreach($loopuser as $users) {
								if($pMTBILL[$users][$d] != 0) { $TbodyPAK.="<td class='text-right'>".number_format($pMTBILL[$users][$d],0)."</td>"; }else{ $TbodyPAK.="<td class='text-right'>-</td>"; }
								if($pMTBOX[$users][$d] != 0) { $TbodyPAK.="<td class='text-right'>".number_format($pMTBOX[$users][$d],0)."</td>"; }else{ $TbodyPAK.="<td class='text-right'>-</td>"; }
								if($pTTBILL[$users][$d] != 0) { $TbodyPAK.="<td class='text-right'>".number_format($pTTBILL[$users][$d],0)."</td>"; }else{ $TbodyPAK.="<td class='text-right'>-</td>"; }
								if($pTTBOX[$users][$d] != 0) { $TbodyPAK.="<td class='text-right'>".number_format($pTTBOX[$users][$d],0)."</td>"; }else{ $TbodyPAK.="<td class='text-right'>-</td>"; }

								if(($pMTBILL[$users][$d] + $pTTBILL[$users][$d]) != 0) { $TbodyPAK.="<td style='font-weight: bold;' class='active text-right'>".number_format($pMTBILL[$users][$d] + $pTTBILL[$users][$d],0)."</td>"; }
									else{ $TbodyPAK.="<td style='font-weight: bold;' class='active text-right'>-</td>"; }
								if(($pMTBOX[$users][$d] + $pTTBOX[$users][$d]) != 0) { $TbodyPAK.="<td style='font-weight: bold;' class='active text-right'>".number_format($pMTBOX[$users][$d] + $pTTBOX[$users][$d],0)."</td>"; }
									else{ $TbodyPAK.="<td style='font-weight: bold;' class='active text-right'>-</td>"; }

								$All_PAK['BillMT_PAK'][$users] = $All_PAK['BillMT_PAK'][$users] + $pMTBILL[$users][$d];
								$All_PAK['BillTT_PAK'][$users] = $All_PAK['BillTT_PAK'][$users] + $pTTBILL[$users][$d];
								$All_PAK['BoxMT_PAK'][$users] = $All_PAK['BoxMT_PAK'][$users] + $pMTBOX[$users][$d];
								$All_PAK['BoxTT_PAK'][$users] = $All_PAK['BoxTT_PAK'][$users] + $pTTBOX[$users][$d];

								$All_PAK_BillTotal['BillTotal_PAK'][$users] =  $All_PAK_BillTotal['BillTotal_PAK'][$users] + $pMTBILL[$users][$d] + $pTTBILL[$users][$d];
								$All_PAK_BoxTotal['BoxTotal_PAK'][$users] =  $All_PAK_BoxTotal['BoxTotal_PAK'][$users] + $pMTBOX[$users][$d] + $pTTBOX[$users][$d];
								
								$TotalMTBill_PAK = $TotalMTBill_PAK + $pMTBILL[$users][$d];
								$TotalMTBox_PAK = $TotalMTBox_PAK + $pMTBOX[$users][$d];
								$TotalTTBill_PAK = $TotalTTBill_PAK + $pTTBILL[$users][$d];
								$TotalTTBox_PAK = $TotalTTBox_PAK + $pTTBOX[$users][$d];
							}

							if($TotalMTBill_PAK != 0) { $TbodyPAK.="<td class='text-right'>".number_format($TotalMTBill_PAK,0)."</td>"; }else{ $TbodyPAK.="<td class='text-right'>-</td>"; }
							if($TotalMTBox_PAK != 0) { $TbodyPAK.="<td class='text-right'>".number_format($TotalMTBox_PAK,0)."</td>"; }else{ $TbodyPAK.="<td class='text-right'>-</td>"; }
							if($TotalTTBill_PAK != 0) { $TbodyPAK.="<td class='text-right'>".number_format($TotalTTBill_PAK,0)."</td>"; }else{ $TbodyPAK.="<td class='text-right'>-</td>"; }
							if($TotalTTBox_PAK != 0) { $TbodyPAK.="<td class='text-right'>".number_format($TotalTTBox_PAK,0)."</td>"; }else{ $TbodyPAK.="<td class='text-right'>-</td>"; }

							if($TotalMTBill_PAK + $TotalTTBill_PAK != 0) { $TbodyPAK.="<td style='font-weight: bold;' class='active text-right'>".number_format($TotalMTBill_PAK + $TotalTTBill_PAK,0)."</td>"; }
								else{ $TbodyPAK.="<td style='font-weight: bold;' class='active text-right'>-</td>"; }
							if($TotalMTBox_PAK + $TotalTTBox_PAK != 0) { $TbodyPAK.="<td style='font-weight: bold;' class='active text-right'>".number_format($TotalMTBox_PAK + $TotalTTBox_PAK,0)."</td>"; }
								else{ $TbodyPAK.="<td style='font-weight: bold;' class='active text-right'>-</td>"; }
			$TbodyPAK .="</tr>";
			$TotalMTBill_PAK = 0;
			$TotalMTBox_PAK = 0;
			$TotalTTBill_PAK = 0;
			$TotalTTBox_PAK = 0;
		}
		$arrCol['TheadPAK'] = $TheadPAK;
		$arrCol['TbodyPAK'] = $TbodyPAK;

	// จัดส่ง(LGT) 

		$SQLLGT = "SELECT A0.Date, SUM(A0.Car) AS 'Car', SUM(A0.Bill) AS 'Bill', SUM(A0.Box) AS 'Box'
					FROM (
					SELECT
						DAY(T1.LoadDate) AS 'Date',
						COUNT(DISTINCT T0.LogiNum) AS 'Car',
						COUNT(DISTINCT T0.BillEntry) AS 'Bill',
						COUNT(T0.BoxCode) AS 'Box'
					FROM logi_detail T0
					LEFT JOIN logi_head T1 ON T0.LogiNum = T1.LogiNum
					WHERE YEAR(T1.LoadDate) = ".$Year." AND MONTH(T1.LoadDate) = ".$Month." AND (T1.Status = 3 AND T0.Status = 2)
					GROUP BY T1.LoadDate
					) A0 GROUP BY A0.Date";
		$QRYLGT = MySQLSelectX($SQLLGT);

		$LGT_Car = array();
		$LGT_Bill = array();
		$LGT_Box = array();

		for($i = 1; $i <= GetLastDate($Year, $Month); $i++) {
			$LGT_Car[$i] = 0;
			$LGT_Bill[$i] = 0;
			$LGT_Box[$i] = 0;
		}

		while ($resultLGT = mysqli_fetch_array($QRYLGT)) {
			$LGT_Car[$resultLGT["Date"]] = $resultLGT['Car'];
			$LGT_Bill[$resultLGT["Date"]] = $resultLGT['Bill'];
			$LGT_Box[$resultLGT["Date"]] = $resultLGT['Box'];
		}
		$TheadLGT = "";
		$TbodyLGT = "";
		$TfootLGT = "";
		$TotalCar = 0;
		$TotalBill = 0;
		$TotalBox = 0;

		$TheadLGT .="<tr class='text-center'>
						<th width='7.5%'>วัน</th>
						<th width='7.5%'>วันที่</th>
						<th>จำนวนรถที่เข้าโหลด (คัน)</th>
						<th>จำนวนบิลที่โหลด (ใบ)</th>
						<th>จำนวนลังที่โหลด (ลัง)</th>
					<tr>";
		for($d = 1; $d <= GetLastDate($Year,$Month); $d++) {
			$thai_date = thai_date(strtotime($Year."-".$Month."-".$d));
			if($thai_date == "อาทิตย์") { $class = " class='text-danger table-danger'"; } else { $class = NULL; }
				$TbodyLGT .="<tr".$class.">
								<td>".$thai_date."</td>
								<td class='text-center'>".$d."</td>";
								if($LGT_Car[$d] != 0) { $TbodyLGT .= "<td class='text-right'>".number_format($LGT_Car[$d],0)."</td>"; }else{ $TbodyLGT .= "<td class='text-right'>-</td>"; }
								if($LGT_Bill[$d] != 0) { $TbodyLGT .= "<td class='text-right'>".number_format($LGT_Bill[$d],0)."</td>"; }else{ $TbodyLGT .= "<td class='text-right'>-</td>"; }
								if($LGT_Box[$d] != 0) { $TbodyLGT .= "<td class='text-right'>".number_format($LGT_Box[$d],0)."</td>"; }else{ $TbodyLGT .= "<td class='text-right'>-</td>"; }
				$TbodyLGT .="</tr>";
				$TotalCar = $TotalCar + $LGT_Car[$d];
				$TotalBill = $TotalBill + $LGT_Bill[$d];
				$TotalBox = $TotalBox +  $LGT_Box[$d];
		} 
		$TfootLGT .="<tr class='active'>
                     <th colspan='2'>รวมทั้งหมด</th>";
					 if($TotalCar != 0) { $TfootLGT .= "<td class='text-right'>".number_format($TotalCar,0)."</td>"; }else{ $TfootLGT .= "<td class='text-right'>-</td>"; }
					 if($TotalBill != 0) { $TfootLGT .= "<td class='text-right'>".number_format($TotalBill,0)."</td>"; }else{ $TfootLGT .= "<td class='text-right'>-</td>"; }
					 if($TotalBox != 0) { $TfootLGT .= "<td class='text-right'>".number_format($TotalBox,0)."</td>"; }else{ $TfootLGT .= "<td class='text-right'>-</td>"; }
		$TfootLGT .="</tr>";

		$arrCol["TheadLGT"] = $TheadLGT;
		$arrCol["TbodyLGT"] = $TbodyLGT;
		$arrCol["TfootLGT"] = $TfootLGT;
}


$arrCol['output'] = $output;
*/

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName']==NULL ){
	echo '<script>window.location="../../../../"</script>';
}

if($_GET['p'] == "GetSaleName") {
	$this_year = $_POST['Year'];
	$SQL = "SELECT LogCode FROM saletarget WHERE DocYear = $this_year AND DocStatus != 'I' AND Ukey = '".$_SESSION['ukey']."'";
	$LogCode = MySQLSelect($SQL);
	$onl = 0;
	switch($_SESSION['DeptCode']){
		case 'DP005' :
			$dept = " ('TT2') ";
			break;
		case 'DP006' :
			$dept = ($_SESSION['LvCode'] == 'LV038') ? " ('MT1','MT2','TT2','OUL') " : " ('MT1') ";
			break;
		case 'DP007' :
			$dept = " ('MT2') ";
			break;
		case 'DP008' :
			$dept = " ('OUL') ";
			break;
		case 'DP001' :
		case 'DP002' :
		case 'DP003' :
		case 'DP009' :
			$dept = " ('MT1','MT2','TT2','OUL') ";
			$onl = 1;
			break;
	}
	$SlpSQL = "SELECT DISTINCT
					T0.LogCode, T1.MainTeam, T0.TeamCode, T0.Ukey, T2.uName, T2.uLastName, T2.uNickName,
					CASE
						WHEN T0.TeamCode LIKE 'EXP%' THEN 'ต่างประเทศ'
						WHEN T0.TeamCode = 'MT201' THEN 'โฮมโปร (ฝากขาย)'
						WHEN T0.TeamCode = 'MT202' THEN 'เมกาโฮม (ฝากขาย)'
						WHEN T0.TeamCode = 'MT203' THEN 'ไทวัสดุ (ฝากขาย)'
						WHEN T0.TeamCode = 'TT203' THEN 'ประเทศลาว' 
					ELSE NULL END AS 'Locate', 
					CASE
                        WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo01' THEN 'โฮมโปร - ฝากขาย'
                        WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo02' THEN 'ไทวัสดุ - ฝากขาย'
                        WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo03' THEN 'เมกาโฮม - ฝากขาย'
                        WHEN T0.Ukey = 'a82726eeff10f11797ed9fde004e701a' THEN 'จีรศักดิ์ (ซ่อมหน้าร้าน)'
                    ELSE CONCAT(T2.uName,' (',T2.uNickName,')') END AS 'SlpName',
					T0.DocStatus
				FROM saletarget T0
				LEFT JOIN teamcode T1 ON T0.TeamCode = T1.TeamCode
				LEFT JOIN users T2 ON T0.Ukey = T2.uKey
				WHERE T0.DocYear = $this_year AND T1.MainTeam IN $dept AND T0.DocStatus != 'I'
				ORDER BY
				CASE
					WHEN T0.TeamCode LIKE 'MT1%' THEN 1
					WHEN T0.TeamCode LIKE 'EXP%' THEN 2
					WHEN T0.TeamCode LIKE 'MT2%' THEN 3
					WHEN T0.TeamCode LIKE 'TT2%' THEN 4
					WHEN T0.TeamCode LIKE 'TT1%' THEN 5
					WHEN T0.TeamCode LIKE 'OUL%' THEN 6
				ELSE 7 END, T0.TeamCode, T2.uName";
	$SlpQRY = MySQLSelectX($SlpSQL);
	$tempteam = "";
	$Log = "N";
	$output .= "<option value='NULL' selected disabled>กรุณาเลือกทีมหรือพนักงานขาย</option>";
	while($SlpRST = mysqli_fetch_array($SlpQRY)) {
		if($SlpRST['uName'] == "" && $SlpRST['uLastName'] == "" && $SlpRST['uNickName'] == "") {
			$fullname = $SlpRST['SlpName'];
		}else{
			$fullname = $SlpRST['uName']." ".$SlpRST['uLastName'];
			if($SlpRST['uNickName'] != "") {
				$fullname = $fullname." (".$SlpRST['uNickName'].")";
			}
	
			if($SlpRST['Locate'] != "") {
				$fullname = $fullname." (".$SlpRST['Locate'].")";
			}
		}


		if($tempteam != $SlpRST['MainTeam']) {
			if($tempteam != "") {
				$output .= "</optgroup>";
			}
			$output .= "<optgroup label='".SATeamName($SlpRST['MainTeam'])."'>";
				$output .= "<option value='T-".$SlpRST['MainTeam']."'>รวม".SATeamName($SlpRST['MainTeam'])." ทั้งหมด</option>";
				$output .= "<option value='".$SlpRST['LogCode']."'>".$fullname."</option>";
			$tempteam = $SlpRST['MainTeam'];
		} else {
			$output .= "<option value='".$SlpRST['LogCode']."'>".$fullname."</option>";
		}

		if(isset($LogCode['LogCode'])) {
			if($LogCode['LogCode'] == $SlpRST['LogCode']) {
				$Log = $LogCode['LogCode'];
			}
		}
	}
	if ($onl == 1){
		$output .= "</optgroup>";
		$output .= "<optgroup label='".SATeamName("ONL")."'>";
			$output .= "<option value='T-ONL'>รวม".SATeamName("ONL")." ทั้งหมด</option>";
		$output .= "</optgroup>";
	}

	$arrCol['output'] = $output;
	$arrCol['LogCode'] = $Log;
}

if($_GET['p'] == "SelectSlpCode") {
	$SelectSlpCode = $_POST['SelectSlpCode'];
	$cMonth = ($_POST['Year'] == date("Y")) ? date("m") : 12;
	$cYear  = $_POST['Year'];
	$pYear  = $cYear-1;

	// (1)>
	// เป้าขาย -------------------------------------------------------------------------------------------------------------------------------------------------------- //
		if (substr($SelectSlpCode,0,1) == 'T') { // ทีม
			$Team = substr($SelectSlpCode,2);
			$sqlTeam = "";
			if($Team == 'MT1') {
				$sqlTeam = "(TeamCode LIKE '$Team%' OR TeamCode LIKE 'EXP%')";
			}elseif($Team == 'OUL'){
				$sqlTeam = "(TeamCode LIKE '$Team%' OR TeamCode LIKE 'TT1%')";
			}else{
				$sqlTeam = "TeamCode LIKE '$Team%'";
			}
			$SQL1 = "SELECT * FROM saletarget WHERE DocYear = '$cYear' AND DocStatus != 'I' AND $sqlTeam";
			$QRY1 = MySQLSelectX($SQL1);
			$trg = array(); /* -> */ for($t = 1; $t <= 12; $t++) { $trg[$t] = 0; } /* -> */ $trg['All'] = 0; // สร้างตัวแปร Array
			$Ukey = array();
			$u = 1;
			while($result1 = mysqli_fetch_array($QRY1)) {
				$Ukey[$u] = $result1['Ukey'];
				for($m = 1; $m <= 12; $m++) { 
					if($m < 10) {
						$trg[$m] = $trg[$m]+$result1['M0'.$m];
					}else{
						$trg[$m] = $trg[$m]+$result1['M'.$m];
					}
				}
				$u++;
			}
			$SQLTTarget =  "SELECT * FROM teamtarget T0 WHERE $sqlTeam AND T0.DocYear = $cYear AND T0.DocStatus = 'A'";
			$QRYTTarget = MySQLSelectX($SQLTTarget);
			while($resultTTarget = mysqli_fetch_array($QRYTTarget)) {
				$trg['All'] = $trg['All']+$resultTTarget['TrgAmount'];
			}
			$trg['All'] = ($trg['All']/12)*12;
			$SlpCode = "(";
			for($r = 1; $r < $u; $r++) {
				$SlpC = slpCodeData(NULL,$Ukey[$r]);
				$SlpCode .= substr($SlpC['SlpCode'],1,-1).",";
			}
			$SlpCode = substr($SlpCode,0,-1).")";
		}else{ // รายบุคคล
			$SQL1 = "SELECT * FROM saletarget WHERE DocYear = '$cYear' AND DocStatus != 'I' AND LogCode = '$SelectSlpCode'";
			$QRY1 = MySQLSelectX($SQL1);
			$trg = array(); /* -> */ for($t = 1; $t <= 12; $t++) { $trg[$t] = 0; } /* -> */ $trg['All'] = 0; // สร้างตัวแปร Array
			$Ukey = array();
			$u = 1;
			while($result1 = mysqli_fetch_array($QRY1)) {
				$Ukey[$u] = $result1['Ukey'];
				for($m = 1; $m <= 12; $m++) { 
					if($m < 10) {
						$trg[$m] = $trg[$m]+$result1['M0'.$m];
					}else{
						$trg[$m] = $trg[$m]+$result1['M'.$m];
					}
				}
				$u++;
			}
			for($m = 1; $m <= 12; $m++) {
				$trg['All'] = $trg['All']+$trg[$m];
			}
			$trg['All'] = ($trg['All']/12)*12;
			$SlpCode = "(";
			for($r = 1; $r < $u; $r++) {
				$SlpC = slpCodeData(NULL,$Ukey[$r]);
				$SlpCode .= substr($SlpC['SlpCode'],1,-1).",";
			}
			$SlpCode = substr($SlpCode,0,-1).")";
		}


	// ยอดขาย ------------------------------------------------------------------------------------------------------------------------------------------------------- //
		$SQL2 ="SELECT P0.BillMonth,SUM(P0.DocTotal) AS DocTotal
				FROM (
					SELECT MONTH(DocDate) AS BillMonth,(T0.DocTotal-T0.VatSum) AS DocTotal
					FROM OINV T0
					WHERE T0.CANCELED = 'N' AND YEAR(T0.DocDate) = '$cYear' AND SlpCode IN $SlpCode
					UNION ALL
					SELECT MONTH(DocDate) AS BillMonth,-1*(T0.DocTotal-T0.VatSum) AS DocTotal
					FROM ORIN T0
					WHERE T0.CANCELED = 'N' AND YEAR(T0.DocDate) = '$cYear' AND SlpCode IN $SlpCode
					) P0
				GROUP BY P0.BillMonth
				ORDER BY BillMonth";
		// echo $SQL2;
		$QRY2 = SAPSelect($SQL2);
		$SALES = array(); $SALES['All'] = 0;
		for($m = 1; $m <= 12; $m++) { $SALES[$m] = 0; }
		while($result2 = odbc_fetch_array($QRY2)) {
			$SALES[$result2['BillMonth']] = $result2['DocTotal'];
			$SALES['All'] = ($SALES['All']+$result2['DocTotal']); //preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W100'],0))
		}

	// สัดส่วนยอดขายต่อเป้าขาย (%) -------------------------------------------------------------------------------------------------------------------------------------- //
		$TrgPer = array(); $TrgPer['All'] = 0;
		for($m = 1; $m <= 12; $m++) { $TrgPer[$m] = 0;
			if($SALES[$m] != 0 && $trg[$m] != 0) {
				$TrgPer[$m] = ($SALES[$m]/$trg[$m])*100;
			}
		}
		if($SALES['All'] != 0 && $trg['All'] != 0) {
			$TrgPer['All'] = ($SALES['All']/$trg['All'])*100;
		}

	// GP ----------------------------------------------------------------------------------------------------------------------------------------------------------- //
		$GP = array(); $GP['All'] = 0;$GPAll=$GPDocTotal=0;
		for($m = 1; $m <= 12; $m++) { 
			$GP[$m] = 0;
			$SQLGP = "SELECT SUM(W0.DocTotal) AS DocTotal,SUM(W0.GrosProfit) AS GrosProfit 
					  FROM (
			  		  SELECT SUM(T0.DocTotal-VatSum) AS DocTotal, SUM(T0.GrosProfit) AS GrosProfit FROM OINV T0 WHERE YEAR(T0.DocDate) = '$cYear' AND MONTH(T0.DocDate) = $m AND T0.SlpCode IN $SlpCode
			          UNION ALL 
					  SELECT -1*(SUM(T0.DocTotal-VatSum)) AS DocTotal, -1*(SUM(T0.GrosProfit)) AS GrosProfit FROM ORIN T0 WHERE YEAR(T0.DocDate) = '$cYear' AND MONTH(T0.DocDate) = $m AND T0.SlpCode IN $SlpCode
					  ) W0";
					  //echo 	$SQLGP;
			$QRYP2 = SAPSelect($SQLGP);
			while($RSTP2 = odbc_fetch_array($QRYP2)) {
				$GP[$m] = ($RSTP2['DocTotal'] <> 0) ? number_format(($RSTP2['GrosProfit']/$RSTP2['DocTotal'])*100,2) : "0.00" ;
				$GPDocTotal = $GPDocTotal + $RSTP2['DocTotal'];
				$GPAll = $GPAll + $RSTP2['GrosProfit'];
			}
			$GP['All'] = ($GPDocTotal  <> 0) ? number_format(($GPAll/$GPDocTotal )*100,2) : "0.00" ;
		}

	// (2)>
	// จำนวนลูกค้าเก่า ทั้งหมด ------------------------------------------------------------------------------------------------------------------------------------------- //
		$SQL3 = "SELECT T0.CardCode FROM OCRD T0 WHERE T0.SlpCode IN $SlpCode";
		//echo $SQL3;
		$QRY3 = SAPSelect($SQL3);
		$CardCus = "(";
		$wai =0;
		while($result3 = odbc_fetch_array($QRY3)) {
			$CardCus .= "'".$result3['CardCode']."',";
			$wai++;
		}
		if ($wai > 0){
			$CardCus = substr($CardCus,0,-1).")";
			$CusTar = array();
			$CusTar['All'] = 0;
			for($m = 1; $m <= 12; $m++) { $CusTar[$m] = 0;
				if($m <= $cMonth) {
					$SQL4 = "SELECT Count(CardCode) AS CardCount FROM custarget WHERE CusTarget > 50000 AND YEAR(DateCreate) = $cYear AND MONTH(DateCreate) <= $m AND CardCode IN $CardCus LIMIT 1";
					$result4 = MySQLSelect($SQL4);
					if($result4) {
						$CusTar[$m] = $result4['CardCount'];
					} else {
						$CusTar[$m] = 0;
					}
					$CusTar['All'] = $CusTar['All']+$CusTar[$m];
				}
			}
		}else{
			$CusTar['All'] = 0;
			$CusTar[1]=$CusTar[2]=$CusTar[3]=$CusTar[4]=$CusTar[5]=$CusTar[6]=$CusTar[7]=$CusTar[8]=$CusTar[9]=$CusTar[10]=$CusTar[11]=$CusTar[12]=0;
		}
	
	// จำนวนลูกค้าผู้มุ่งหวัง (ราย) ---------------------------------------------------------------------------------------------------------------------------------------- //
		$getUkey = "(";
		for($i = 1; $i <= count($Ukey); $i++) {
			$getUkey .= "'".$Ukey[$i]."',";
		}
		$getUkey = substr($getUkey,0,-1).")";
		$SQL5 ="SELECT T0.MonthPlan,SUM(T0.CusCount) AS CusCount
				FROM (SELECT CreateUkey, MONTH(PlanDate) AS MonthPlan, 1 AS CusCount 
					FROM route_planner
					WHERE CardCode IS NULL AND YEAR(PlanDate) = $cYear AND DocStatus = 'A' AND CreateUkey IN $getUkey) T0
				GROUP BY T0.MonthPlan";
		$QRY5 = MySQLSelectX($SQL5);
		$NewCus = array(); for($i = 1; $i <= 12; $i++) { $NewCus[$i] = 0; }
		$NewCus['All'] = 0;
		while($result5 = mysqli_fetch_array($QRY5)) {
			$NewCus[$result5['MonthPlan']] = $result5['CusCount'];
			$NewCus['All'] = $NewCus['All']+$result5['CusCount'];
		}

	// (3 - 4)>
	// เป้าหมายลูกค้าเก่าที่ต้องเข้าพบ (ราย) <ถึง> สัดส่วนการเข้าพบจริง (%) ------------------------------------------------------------------------------------------------------- //
		$SQL6 ="SELECT P0.CusType, P0.MonthPlan, SUM(P0.MeetCount) AS Meet, SUM(P0.AllCus) AS AllCus 
				FROM (SELECT CASE WHEN T0.CardCode IS NULL THEN 'N' ELSE 'C' END AS CusType,
							CASE WHEN T0.MeetType != 0 THEN 1 ELSE 0 END AS MeetCount,
							MONTH(T0.PlanDate) AS MonthPlan, 1 AS AllCus
					FROM route_planner T0
					WHERE (T0.DocStatus = 1 OR T0.MeetType != 0) AND YEAR(T0.PlanDate) = $cYear AND T0.CreateUkey IN $getUkey) P0
				GROUP BY P0.CusType, P0.MonthPlan";
		$QRY6 = MySQLSelectX($SQL6);
		$O_MeetCus = array(); for($i = 1; $i <= 12; $i++) { $O_MeetCus[$i] = 0; } $O_MeetCus['All'] = 0;
		$O_OldCus = array();  for($i = 1; $i <= 12; $i++) { $O_OldCus[$i] = 0; }  $O_OldCus['All']  = 0;
		$N_MeetCus = array(); for($i = 1; $i <= 12; $i++) { $N_MeetCus[$i] = 0; } $N_MeetCus['All'] = 0;
		$N_OldCus = array();  for($i = 1; $i <= 12; $i++) { $N_OldCus[$i] = 0; }  $N_OldCus['All']  = 0;

		$O_Per = array();  for($i = 1; $i <= 12; $i++) { $O_Per[$i] = 0; }  $O_Per['All']  = 0;
		$N_Per = array();  for($i = 1; $i <= 12; $i++) { $N_Per[$i] = 0; }  $N_Per['All']  = 0;
		while($result6 = mysqli_fetch_array($QRY6)) {
			if($result6['CusType'] == 'C') {
				$O_OldCus[$result6['MonthPlan']]  = $result6['AllCus'];  // เป้าหมายลูกค้าเก่าที่ต้องเข้าพบ (ราย)
				$O_OldCus['All']  = $O_OldCus['All']+$result6['AllCus']; // รวม เป้าหมายลูกค้าเก่าที่ต้องเข้าพบ (ราย)

				$O_MeetCus[$result6['MonthPlan']] = $result6['Meet'];    // จำนวนลูกค้าเก่าที่เข้าพบ (ราย)
				$O_MeetCus['All'] = $O_MeetCus['All']+$result6['Meet'];  // รวม จำนวนลูกค้าเก่าที่เข้าพบ (ราย)

				$O_Per[$result6['MonthPlan']] = ($result6['Meet']/$result6['AllCus'])*100; // สัดส่วนการเข้าพบจริง (%) 1
			}else{
				$N_OldCus[$result6['MonthPlan']]  = $result6['AllCus'];  // เป้าหมายลูกค้ามุ่งหวังที่ตั้งใจเข้าพบ (ราย)
				$N_OldCus['All']  = $N_OldCus['All']+$result6['AllCus']; // รวม เป้าหมายลูกค้ามุ่งหวังที่ตั้งใจเข้าพบ (ราย)

				$N_MeetCus[$result6['MonthPlan']] = $result6['Meet'];    // จำนวนลูกค้ามุ่งหวังที่เข้าพบ (ราย)
				$N_MeetCus['All'] = $N_MeetCus['All']+$result6['Meet'];  // รวม จำนวนลูกค้ามุ่งหวังที่เข้าพบ (ราย)

				$N_Per[$result6['MonthPlan']] = ($result6['Meet']/$result6['AllCus'])*100; // สัดส่วนการเข้าพบจริง (%) 2
			}
		}
		if($O_MeetCus['All'] != 0 && $O_OldCus['All'] != 0) {
			$O_Per['All'] = ($O_MeetCus['All']/$O_OldCus['All'])*100; // รวมทั้งหมด สัดส่วนการเข้าพบจริง (%) 1
		}
		if($N_MeetCus['All'] != 0 && $N_OldCus['All'] != 0) {
			$N_Per['All'] = ($N_MeetCus['All']/$N_OldCus['All'])*100; // รวมทั้งหมด สัดส่วนการเข้าพบจริง (%) 2
		}

	// (5)>
	// จำนวนร้านค้าที่เปิดใหม่ (ราย) --------------------------------------------------------------------------------------------------------------------------------------- //
		$SQL7 ="SELECT P0.MCard, SUM(P0.NewCount) AS NewCount
				FROM (SELECT DISTINCT MONTH(T0.CreateDate) AS MCard,T0.CardCode,1 AS NewCount
					FROM OCRD T0
					WHERE YEAR(T0.CreateDate) = $cYear AND T0.SlpCode IN $SlpCode) P0
				GROUP BY P0.MCard";
		$QRY7 = SAPSelect($SQL7);
		$nOpenCus = array(); for($i = 1; $i <= 12; $i++) { $nOpenCus[$i] = 0; } $nOpenCus['All'] = 0;
		while($result7 = odbc_fetch_array($QRY7)) {
			$nOpenCus[$result7['MCard']] = $result7['NewCount'];
			$nOpenCus['All'] = $nOpenCus['All']+$result7['NewCount'];
		}

	// จำนวนร้านค้าที่เปิดบิล (ราย) ---------------------------------------------------------------------------------------------------------------------------------------- //
		$SQL8 ="SELECT P0.MBill, SUM(P0.CardCount) AS CardCount 
				FROM (SELECT DISTINCT MONTH(T0.DocDate) AS MBill, T0.CardCode, 1 AS CardCount
					FROM OINV T0
					WHERE YEAR(T0.DocDate) = $cYear AND T0.CANCELED = 'N' AND T0.SlpCode IN $SlpCode) P0
				GROUP BY P0.MBill";
		$QRY8 = SAPSelect($SQL8);
		$LOpenCus = array(); for($i = 1; $i <= 12; $i++) { $LOpenCus[$i] = 0; } $LOpenCus['All'] = 0;
		while($result8 = odbc_fetch_array($QRY8)) {
			$LOpenCus[$result8['MBill']] = $result8['CardCount'];
			$LOpenCus['All'] = $LOpenCus['All']+$result8['CardCount'];
		}

	// จำนวนบิลที่เปิด (บิล) --------------------------------------------------------------------------------------------------------------------------------------------- //
		$SQL9 ="SELECT P0.MBill, SUM(P0.BillCount) AS BillCount
				FROM (SELECT MONTH(T0.DocDate) AS MBill, 1 AS BillCount  
					FROM OINV T0
					WHERE YEAR(T0.DocDate) = $cYear AND T0.CANCELED = 'N' AND T0.SlpCode IN $SlpCode) P0
				GROUP BY P0.MBill";
		$QRY9 = SAPSelect($SQL9);
		$bOpenCus = array(); for($i = 1; $i <= 12; $i++) { $bOpenCus[$i] = 0; } $bOpenCus['All'] = 0;
		while($result9 = odbc_fetch_array($QRY9)) {
			$bOpenCus[$result9['MBill']] = $result9['BillCount'];
			$bOpenCus['All'] = $bOpenCus['All']+$result9['BillCount'];
		}

	// (6)>
	// รายงานประจำวัน (> 80 ร้านค้า/เดือน) -------------------------------------------------------------------------------------------------------------------------------- //
		$SQL10="SELECT SUM(A0.M01) AS M01, SUM(A0.M02) AS M02, SUM(A0.M03) AS M03, SUM(A0.M04) AS M04, SUM(A0.M05) AS M05, SUM(A0.M06) AS M06,
					   SUM(A0.M07) AS M07, SUM(A0.M08) AS M08, SUM(A0.M09) AS M09, SUM(A0.M10) AS M10, SUM(A0.M11) AS M11, SUM(A0.M12) AS M12
				FROM (SELECT DISTINCT T0.CardCode, T0.plan_month,
					CASE WHEN T0.plan_month = 1  THEN 1 ELSE 0 END AS M01,
					CASE WHEN T0.plan_month = 2  THEN 1 ELSE 0 END AS M02,
					CASE WHEN T0.plan_month = 3  THEN 1 ELSE 0 END AS M03,
					CASE WHEN T0.plan_month = 4  THEN 1 ELSE 0 END AS M04,
					CASE WHEN T0.plan_month = 5  THEN 1 ELSE 0 END AS M05,
					CASE WHEN T0.plan_month = 6  THEN 1 ELSE 0 END AS M06,
					CASE WHEN T0.plan_month = 7  THEN 1 ELSE 0 END AS M07,
					CASE WHEN T0.plan_month = 8  THEN 1 ELSE 0 END AS M08,
					CASE WHEN T0.plan_month = 9  THEN 1 ELSE 0 END AS M09,
					CASE WHEN T0.plan_month = 10 THEN 1 ELSE 0 END AS M10,
					CASE WHEN T0.plan_month = 11 THEN 1 ELSE 0 END AS M11,
					CASE WHEN T0.plan_month = 12 THEN 1 ELSE 0 END AS M12 
					FROM  route_survey T0
					WHERE T0.DocStatus != 'I' AND T0.plan_year = $cYear AND (T0.Q1 != '0' AND T0.Q2!='0' AND T0.Q3 !='0' AND  T0.Q4!= '0' AND T0.Q5 != '0' AND T0.Q6 != '0') AND 
						T0.CreateUkey IN $getUkey
				) A0";
		$PLAN80 = array(); for($i = 1; $i <= 12; $i++) { $PLAN80[$i] = 0; } $PLAN80['All'] = 0;
		$QRY10 = MySQLSelectX($SQL10);
		while($result10 = mysqli_fetch_array($QRY10)) {
			for($i = 1; $i <= 12; $i++) { 
				if($i < 10) {
					$PLAN80[$i]    = $result10['M0'.$i]; 
					$PLAN80['All'] = $PLAN80['All']+$result10['M0'.$i];
				}else{
					$PLAN80[$i]    = $result10['M'.$i]; 
					$PLAN80['All'] = $PLAN80['All']+$result10['M'.$i];
				}
			} 
		}

	// รายงานประจำเดือน (> 1 ครั้ง/เดือน) --------------------------------------------------------------------------------------------------------------------------------- //
		$SQL11="
			SELECT  SUM(A0.M01) AS M01, SUM(A0.M02) AS M02, SUM(A0.M03) AS M03, SUM(A0.M04) AS M04, SUM(A0.M05) AS M05, SUM(A0.M06) AS M06,
						SUM(A0.M07) AS M07, SUM(A0.M08) AS M08, SUM(A0.M09) AS M09, SUM(A0.M10) AS M10, SUM(A0.M11) AS M11, SUM(A0.M12) AS M12
				FROM (SELECT DISTINCT T0.CardCode, T0.plan_month,
					CASE WHEN T0.plan_month = 1  THEN 1 ELSE 0 END AS M01,
					CASE WHEN T0.plan_month = 2  THEN 1 ELSE 0 END AS M02,
					CASE WHEN T0.plan_month = 3  THEN 1 ELSE 0 END AS M03,
					CASE WHEN T0.plan_month = 4  THEN 1 ELSE 0 END AS M04,
					CASE WHEN T0.plan_month = 5  THEN 1 ELSE 0 END AS M05,
					CASE WHEN T0.plan_month = 6  THEN 1 ELSE 0 END AS M06,
					CASE WHEN T0.plan_month = 7  THEN 1 ELSE 0 END AS M07,
					CASE WHEN T0.plan_month = 8  THEN 1 ELSE 0 END AS M08,
					CASE WHEN T0.plan_month = 9  THEN 1 ELSE 0 END AS M09,
					CASE WHEN T0.plan_month = 10 THEN 1 ELSE 0 END AS M10,
					CASE WHEN T0.plan_month = 11 THEN 1 ELSE 0 END AS M11,
					CASE WHEN T0.plan_month = 12 THEN 1 ELSE 0 END AS M12 
					FROM  route_survey T0
					WHERE T0.DocStatus != 'I' AND T0.plan_year = $cYear AND T0.CreateUkey IN $getUkey
				) A0";
		$SQL11 = "
			SELECT SUM(A0.M01) AS M01,
					SUM(A0.M02) AS M02,
					SUM(A0.M03) AS M03,
					SUM(A0.M04) AS M04,
					SUM(A0.M05) AS M05,
					SUM(A0.M06) AS M06,
					SUM(A0.M07) AS M07,
					SUM(A0.M08) AS M08,
					SUM(A0.M09) AS M09,
					SUM(A0.M10) AS M10,
					SUM(A0.M11) AS M11,
					SUM(A0.M12) AS M12
			FROM (
			SELECT DISTINCT T0.CardCode,
					CASE WHEN T0.plan_month  = 1 THEN 1 ELSE 0 END AS M01,
					CASE WHEN T0.plan_month  = 2 THEN 1 ELSE 0 END AS M02,
					CASE WHEN T0.plan_month  = 3 THEN 1 ELSE 0 END AS M03,
					CASE WHEN T0.plan_month  = 4 THEN 1 ELSE 0 END AS M04,
					CASE WHEN T0.plan_month  = 5 THEN 1 ELSE 0 END AS M05,
					CASE WHEN T0.plan_month  = 6 THEN 1 ELSE 0 END AS M06,
					CASE WHEN T0.plan_month  = 7 THEN 1 ELSE 0 END AS M07,
					CASE WHEN T0.plan_month  = 8 THEN 1 ELSE 0 END AS M08,
					CASE WHEN T0.plan_month  = 9 THEN 1 ELSE 0 END AS M09,
					CASE WHEN T0.plan_month  = 10 THEN 1 ELSE 0 END AS M10,
					CASE WHEN T0.plan_month  = 11 THEN 1 ELSE 0 END AS M11,
					CASE WHEN T0.plan_month  = 12 THEN 1 ELSE 0 END AS M12   
			FROM route_action T0
			WHERE T0.CreateUkey IN $getUkey  AND T0.Plan_Year = $cYear) A0";
		// echo $SQL11;
		$QRY11 = MySQLSelectX($SQL11);
		$PLAN1 = array(); for($i = 1; $i <= 12; $i++) { $PLAN1[$i] = 0; } $PLAN1['All'] = 0;
		while($result11 = mysqli_fetch_array($QRY11)) {
			for($i = 1; $i <= 12; $i++) { 
				if($i < 10) {
					$PLAN1[$i]    = $result11['M0'.$i]; 
					$PLAN1['All'] = $PLAN1['All']+$result11['M0'.$i];
				}else{
					$PLAN1[$i]    = $result11['M'.$i]; 
					$PLAN1['All'] = $PLAN1['All']+$result11['M'.$i];
				}
			} 
		}

	// การสำรวจราคาร้านค้า (ส่งรูปอัลบั้มไลน์) ------------------------------------------------------------------------------------------------------------------------------- //
		$SQL12="SELECT  SUM(A0.M01) AS M01, SUM(A0.M02) AS M02, SUM(A0.M03) AS M03, SUM(A0.M04) AS M04, SUM(A0.M05) AS M05, SUM(A0.M06) AS M06,
						SUM(A0.M07) AS M07, SUM(A0.M08) AS M08, SUM(A0.M09) AS M09, SUM(A0.M10) AS M10, SUM(A0.M11) AS M11, SUM(A0.M12) AS M12
				FROM (SELECT DISTINCT T0.CardCode, T0.plan_month,
					CASE WHEN T0.plan_month = 1  THEN 1 ELSE 0 END AS M01,
					CASE WHEN T0.plan_month = 2  THEN 1 ELSE 0 END AS M02,
					CASE WHEN T0.plan_month = 3  THEN 1 ELSE 0 END AS M03,
					CASE WHEN T0.plan_month = 4  THEN 1 ELSE 0 END AS M04,
					CASE WHEN T0.plan_month = 5  THEN 1 ELSE 0 END AS M05,
					CASE WHEN T0.plan_month = 6  THEN 1 ELSE 0 END AS M06,
					CASE WHEN T0.plan_month = 7  THEN 1 ELSE 0 END AS M07,
					CASE WHEN T0.plan_month = 8  THEN 1 ELSE 0 END AS M08,
					CASE WHEN T0.plan_month = 9  THEN 1 ELSE 0 END AS M09,
					CASE WHEN T0.plan_month = 10 THEN 1 ELSE 0 END AS M10,
					CASE WHEN T0.plan_month = 11 THEN 1 ELSE 0 END AS M11,
					CASE WHEN T0.plan_month = 12 THEN 1 ELSE 0 END AS M12 
					FROM  route_survey T0
					WHERE T0.DocStatus != 'I' AND T0.plan_year = $cYear AND T0.Q7 != '0' AND 
						T0.CreateUkey IN $getUkey
				) A0";
		$QRY12 = MySQLSelectX($SQL12);
		$Album = array(); for($i = 1; $i <= 12; $i++) { $Album[$i] = 0; } $Album['All'] = 0;
		while($result12 = mysqli_fetch_array($QRY12)) {
			for($i = 1; $i <= 12; $i++) { 
				if($i < 10) {
					$Album[$i]    = $result12['M0'.$i]; 
					$Album['All'] = $Album['All']+$result12['M0'.$i];
				}else{
					$Album[$i]    = $result12['M'.$i]; 
					$Album['All'] = $Album['All']+$result12['M'.$i];
				}
			} 
		}

	// (7)>
	// หนี้เกินกำหนด < 30 วัน (บาท), หนี้เกินกำหนด 31-90 วัน (บาท), หนี้เกินกำหนด > 90 วัน (บาท) ------------------------------------------------------------------------------ //
		switch($cMonth) {
			case "1":  $m1 = 12; $y1 = $pYear; $y2 = $pYear; break;
			case "2":  $m1 = 1;  $y1 = $cYear; $y2 = $cYear; break;
			case "3":  $m1 = 2;  $y1 = $cYear; $y2 = $cYear; break;
			case "4":  $m1 = 3;  $y1 = $cYear; $y2 = $cYear; break;
			case "5":  $m1 = 4;  $y1 = $cYear; $y2 = $cYear; break;
			case "6":  $m1 = 5;  $y1 = $cYear; $y2 = $cYear; break;
			case "7":  $m1 = 6;  $y1 = $cYear; $y2 = $cYear; break;
			case "8":  $m1 = 7;  $y1 = $cYear; $y2 = $cYear; break;
			case "9":  $m1 = 8;  $y1 = $cYear; $y2 = $cYear; break;
			case "10": $m1 = 9;  $y1 = $cYear; $y2 = $cYear; break;
			case "11": $m1 = 10; $y1 = $cYear; $y2 = $cYear; break;
			case "12": $m1 = 11; $y1 = $cYear; $y2 = $cYear; break;
		}
		$SQL13="SELECT A0.[U_Dim1], A0.[Group], SUM(A0.[Amount]) AS 'Amount'
				FROM (SELECT T2.[U_Dim1],T2.[SlpCode], 
							CASE WHEN CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) <= 30 THEN 'B30D'
								WHEN CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) >= 31 AND CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) <= 60 THEN 'B60D'
								WHEN CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) >= 61 AND CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) <= 90 THEN 'B90D'
							ELSE 'A90D' END AS 'Group',
							SUM((T0.Doctotal-T0.PaidToDate)) AS 'Amount'
					FROM OINV T0 
					LEFT JOIN NNM1 T1 On T0.Series = T1.Series 
					LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
					WHERE ((MONTH(T0.DocDueDate) <= $m1 AND YEAR(T0.DocDueDate)= $y1) OR  YEAR(T0.DocDueDate) < $y2) AND T0.DocStatus ='O' AND T0.CANCELED = 'N'
							AND (T1.SeriesName LIKE 'IV%' or T1.SeriesName LIKE 'HA%' OR T1.SeriesName IS NULL)
							AND (T2.[U_Dim1] IN ('MT1','MT2','TT1','TT2','OUL','ONL','EI1','EXP')) AND (T0.[SlpCode] NOT IN (23,24,158))
					GROUP BY T2.[U_Dim1], T2.[SlpCode], T0.[DocDueDate]
					UNION ALL
					SELECT T2.[U_Dim1],T2.[SlpCode], 
							CASE WHEN CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) <= 30 THEN 'B30D'
								WHEN CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) >= 31 AND CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) <= 60 THEN 'B60D'
								WHEN CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) >= 61 AND CAST((DATEDIFF(DAY,T0.[DocDueDate],GETDATE()))-30 AS INT) <= 90 THEN 'B90D'
							ELSE 'A90D' END AS 'Group',
							SUM(-(T0.Doctotal-T0.PaidToDate)) AS 'Amount' 
					FROM ORIN T0 
					LEFT JOIN NNM1 T1 On T0.Series = T1.Series 
					LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode                
					WHERE ((MONTH(T0.DocDueDate) <= $m1 AND YEAR(T0.DocDueDate)= $y1) OR  YEAR(T0.DocDueDate) < $y2) AND T0.DocStatus ='O' AND T0.CANCELED = 'N'
							AND (T1.SeriesName LIKE 'SR%' or T1.SeriesName LIKE 'S1%' OR T1.SeriesName IS NULL)
							AND (T2.[U_Dim1] IN ('MT1','MT2','TT1','TT2','OUL','ONL','EI1','EXP')) AND (T0.[SlpCode] NOT IN (23,24,158))
					GROUP BY T2.[U_Dim1], T2.[SlpCode], T0.[DocDueDate]
				) A0
				WHERE A0.SlpCode IN $SlpCode
				GROUP BY A0.[U_Dim1], A0.[Group]
				ORDER BY CASE WHEN A0.[Group] = 'B30D' THEN 1
							WHEN A0.[Group] = 'B60D' THEN 2
							WHEN A0.[Group] = 'B90D' THEN 3
						ELSE 4 END";
		// echo $SQL13;
		if($y1 == 2022) {
			$QRY13 = conSAP8($SQL13);
		}else{
			$QRY13 = SAPSelect($SQL13);
		}
		$Nee = array(); 
		for($i = 1; $i <= 12; $i++) {
			$Nee['B60D'][$i] = 0; $Nee['B90D'][$i] = 0; 
			$Nee['B30D'][$i] = 0;     // หนี้เกินกำหนด < 30 วัน (บาท)
			$Nee['B31D-B91D'][$i] = 0;// หนี้เกินกำหนด 31-90 วัน (บาท)
			$Nee['A90D'][$i] = 0;     // หนี้เกินกำหนด > 90 วัน (บาท)
		} 
		if(date("m") < 10) { $NeeMonth = substr(date("m"),1); }else{ $NeeMonth = date("m"); }
		// echo $NeeMonth;
		$Nee['B30D']['All'] = 0; $Nee['B60D']['All'] = 0; $Nee['B90D']['All'] = 0; $Nee['A90D']['All'] = 0;
		while($result13 = odbc_fetch_array($QRY13)) {
			$Nee[$result13['Group']][$NeeMonth] = $Nee[$result13['Group']][$NeeMonth]+$result13['Amount'];
			// echo $result13['Group']."/".$NeeMonth."/".$Nee[$result13['Group']][$NeeMonth]."/".$result13['Amount']."<br/>";
		}
		$Nee['B31D-B91D'][$NeeMonth] = $Nee['B60D'][$NeeMonth]+$Nee['B90D'][$NeeMonth];
		$Nee['B30D']['All']          = $Nee['B30D'][$NeeMonth];
		$Nee['B31D-B91D']['All']     = $Nee['B31D-B91D'][$NeeMonth];
		$Nee['A90D']['All']          = $Nee['A90D'][$NeeMonth];

	// (8)>
	// จำนวนเช็คเด้ง (ใบ) ---------------------------------------------------------------------------------------------------------------------------------------------- //
		$SQL14="SELECT P0.MChq, SUM(P0.CountX) AS ChqCount
				FROM (SELECT MONTH(T0.CHQ_SaleReceive) AS MChq, 1 AS CountX 
					  FROM chq_return T0
					  WHERE T0.Status IN (0,1) AND YEAR(T0.CHQ_SaleReceive) = $cYear AND T0.SaleUkey IN $getUkey
					 ) P0
				GROUP BY P0.MChq";
		$QRY14 = MySQLSelectX($SQL14);
		$ChkD = array(); for($i = 1; $i <= 12; $i++) { $ChkD[$i] = 0; } $ChkD['All'] = 0;
		while($result14 = mysqli_fetch_array($QRY14)) {
			$ChkD[$result14['MChq']] = $result14['ChqCount'];
			$ChkD['All'] = $ChkD['All']+$result14['ChqCount'];
		}

	// จำนวนเช็คเกินกำหนด > 30 วัน (บิล) -------------------------------------------------------------------------------------------------------------------------------- //
		$SQL15="SELECT P0.MChq, SUM(P0.CountCHQ) AS ContCHQ
				FROM (SELECT T2.CardCode, T2.DocNum, MONTH(T4.DocDate) AS MChq, 
							T2.DocDate, T2.DocDueDate, T0.DueDate AS CheckDueDate,
							T0.CheckNum, DATEDIFF(DAY,T2.DocDueDate,T0.DueDate) AS Diff, 1 AS CountCHQ    
					  FROM RCT1 T0 
					  LEFT JOIN RCT2 T1 ON T0.DocNum = T1.DocEntry
					  LEFT JOIN OINV T2 ON T1.DocEntry = T2.DocEntry AND T1.InvType = 13
					  LEFT JOIN ORCT T4 ON T0.DocNum = T4.DocEntry
					  WHERE YEAR(T2.DocDate) = $cYear AND DATEDIFF(DAY,T2.DocDueDate,T0.DueDate) > 30 AND T2.SlpCode IN $SlpCode
					) P0
				GROUP BY P0.MChq";
		$QRY15 = SAPSelect($SQL15);
		$ChkDue = array(); for($i = 1; $i <= 12; $i++) { $ChkDue[$i] = 0; } $ChkDue['All'] = 0;
		while($result15 = odbc_fetch_array($QRY15)) {
			$ChkDue[$result15['MChq']] = $result15['ContCHQ'];
			$ChkDue['All'] = $ChkDue['All']+$result15['ContCHQ'];
		}

	// (9)>
	// มูลค่ารวมการคืนสินค้า (บาท), ต้นทุนรวมเซลส์รับผิดชอบที่รับสินค้าคืน (บาท) -------------------------------------------------------------------------------------------------- //
		$SQL16="SELECT
					T2.U_Dim1, MONTH(T0.DocDate) AS 'Month', 
					CASE WHEN T1.WhsCode IN ('MT','MT2','TT-C','OUL','KB1','WP01','WM1','WM2','TT','PM-TT','WA26.1','KB7','WM1.1','WM2.1','TT2.1') OR (T1.WhsCode LIKE 'W%' AND T1.WhsCode NOT LIKE 'WP%') THEN 'SA' ELSE 'KBI' END AS 'Owner', 
					SUM(T1.PriceAfVat*T1.Quantity) AS 'LineTotal'
				FROM ORDN T0
				LEFT JOIN RDN1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
				WHERE YEAR(T0.DocDate) = $cYear AND T0.SlpCode IN $SlpCode AND T0.CANCELED = 'N'
				GROUP BY T2.U_Dim1, MONTH(T0.DocDate), CASE WHEN T1.WhsCode IN ('MT','MT2','TT-C','OUL','KB1','WP01','WM1','WM2','TT','PM-TT','WA26.1','KB7','WM1.1','WM2.1','TT2.1') OR (T1.WhsCode LIKE 'W%' AND T1.WhsCode NOT LIKE 'WP%') THEN 'SA' ELSE 'KBI' END
				ORDER BY T2.U_Dim1, MONTH(T0.DocDate), 'Owner'";
		// echo $SQL16;
		$QRY16 = SAPSelect($SQL16);
		$KPro = array();
		for($i = 1; $i <= 12; $i++) { $KPro['KBI'][$i] = 0; $KPro['SA'][$i] = 0; } 
		$KPro['KBIAll'] = 0; $KPro['SAAll'] = 0;
		while($result16 = odbc_fetch_array($QRY16)) {
			$KPro[$result16['Owner']][$result16['Month']] = $result16['LineTotal'];
			$KPro[$result16['Owner']."All"] = $KPro[$result16['Owner']."All"]+$result16['LineTotal'];
		}

	// (10)>
	// มูลค่าการยืมสินค้าที่ยังไม่คืน (บาท), มูลค่าการยืมสินค้าที่ยังไม่คืน > 6 เดือน (บาท) -------------------------------------------------------------------------------------------- //
		$SQL17 ="
			SELECT SUM((T1.PriceAfVAT*T1.OpenQty)) AS Total
			FROM ODLN T0
			LEFT JOIN DLN1 T1 ON T1.DocEntry = T0.DocEntry 
			LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
			WHERE T2.BeginStr IN ('PA-','PC-','PD-') AND T0.DocStatus = 'O' AND T1.LineStatus = 'O' AND T0.SlpCode IN $SlpCode AND T0.CANCELED = 'N'";
		$QRY17 = SAPSelect($SQL17);
		$result17 = odbc_fetch_array($QRY17);
		$borrowed = array();
		for($i = 1; $i <= 12; $i++) {
			if($i == intval($cMonth)) {
				if(isset($result17['Total'])) {
					$borrowed[$i] = $result17['Total'];
					$borrowed['All'] = $result17['Total'];
				}else{
					$borrowed[$i] = 0;
					$borrowed['All'] = 0;
				}
			}else{
				$borrowed[$i] = 0;
			}
		}
		// echo $SlpCode;
		$SQL18 = "
			SELECT SUM((T1.PriceAfVAT*T1.OpenQty)) AS TotalDif6
			FROM ODLN T0
			LEFT JOIN DLN1 T1 ON T1.DocEntry = T0.DocEntry 
			LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
			WHERE T2.BeginStr IN ('PA-','PC-','PD-') AND T0.DocStatus = 'O' AND T1.LineStatus = 'O' AND T0.SlpCode IN $SlpCode AND T0.CANCELED = 'N'
				AND DATEDIFF(MONTH,T0.DocDate,GETDATE()) > 6";
		$QRY18 = SAPSelect($SQL18);
		$result18 = odbc_fetch_array($QRY18);
		$borrowedDif = array();
		for($i = 1; $i <= 12; $i++) {
			if($i == intval($cMonth)) {
				if(isset($result18['TotalDif6'])) {
					$borrowedDif[$i] = $result18['TotalDif6'];
					$borrowedDif['All'] = $result18['TotalDif6'];
				}else{
					$borrowedDif[$i] = 0;
					$borrowedDif['All'] = 0;
				}
			}else{
				$borrowedDif[$i] = 0;
			}
		}

	// (11)>
	// ต้นทุนคลังเซลส์มือหนึง (บาท)
		if (substr($SelectSlpCode,0,1) != 'T') {
			$GETTeam = MySQLSelect("SELECT MainTeam FROM oslp WHERE Ukey = '".$Ukey[1]."'");
			$Team = $GETTeam['MainTeam'];
		}
		
		$SQL = "
			SELECT
				A0.Warehouse, A0.WhsName,
				SUM(A0.M_00 * A0.Cost) AS 'M_00',";
				for($m = 1; $m <= 12; $m++) {
					if($m < 10) {
						$SQL .= "
						SUM(A0.M_0".$m."_I * A0.Cost) AS 'M_0".$m."_I', SUM(A0.M_0".$m."_O * A0.Cost) AS 'M_0".$m."_O', SUM(A0.M_0".$m."_O1 * A0.Cost) AS 'M_0".$m."_O1', SUM(A0.M_0".$m."_O2 * A0.Cost) AS 'M_0".$m."_O2', SUM(A0.M_0".$m."_O3 * A0.Cost) AS 'M_0".$m."_O3'";
					}else{
						$SQL .= "
						SUM(A0.M_".$m."_I * A0.Cost) AS 'M_".$m."_I', SUM(A0.M_".$m."_O * A0.Cost) AS 'M_".$m."_O', SUM(A0.M_".$m."_O1 * A0.Cost) AS 'M_".$m."_O1', SUM(A0.M_".$m."_O2 * A0.Cost) AS 'M_".$m."_O2', SUM(A0.M_".$m."_O3 * A0.Cost) AS 'M_".$m."_O3'";
					}
					$SQL .= ($m < 12) ? "," : "";
				}
			$SQL .= "
			FROM (
				SELECT T0.Warehouse, T1.WhsName, T0.ItemCode,
				(CASE WHEN T2.LastPurDat = '2022-12-31' OR T2.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc,T2.LastPurPrc) ELSE T2.LastPurPrc END) * 1.07 AS 'Cost',
				(CASE WHEN T0.CreateDate = '2023-01-01'  OR (YEAR(T0.CreateDate) <= $pYear) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_00',";
				for($m = 1; $m <= 12; $m++) {
					if($m < 10) {
						$SQL .= "
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.InQty > 0) THEN SUM(T0.InQty) ELSE 0 END) AS 'M_0".$m."_I',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 13) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O1',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 15) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O2',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType IN (60,67)) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O3'";
					}else{
						$SQL .= "
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.InQty > 0) THEN SUM(T0.InQty) ELSE 0 END) AS 'M_".$m."_I',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 13) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O1',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 15) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O2',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType IN (60,67)) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O3'";
					}
					$SQL .= ($m < 12) ? "," : "";
				}
				$SQL .= "
				FROM OINM T0
				LEFT JOIN OWHS T1 ON T0.Warehouse = T1.WhsCode
				LEFT JOIN OITM T2 ON T0.ItemCode = T2.ItemCode
				LEFT JOIN KBI_DB2022.dbo.OITM T3 ON T0.ItemCode = T3.ItemCode
				WHERE T1.Location IN (1) AND T1.StreetNo = '$Team'
				GROUP BY T0.TransType, T0.Warehouse, T1.WhsName, T0.CreateDate, T0.ItemCode, T0.InQty, T0.OutQty, T2.LastPurDat, T3.LastPurDat, T2.LastPurPrc, T3.LastPurPrc
			) A0
			GROUP BY A0.Warehouse, A0.WhsName
			ORDER BY A0.Warehouse";

		for($m = 1; $m <= 12; $m++) {
			$WhseSaleTeam1H[1][$m]['r1'] = 0;
			$WhseSaleTeam1H[1][$m]['r2'] = 0;
			$WhseSaleTeam1H[1][$m]['r3'] = 0;
			$WhseSaleTeam1H[1][$m]['r4'] = 0;
			$WhseSaleTeam1H[1][$m]['r5'] = 0;
			$WhseSaleTeam1H[1][$m]['r6'] = 0;
			$WhseSaleTeam1H[1][$m]['r7'] = 0;
		}
		if(ChkRowSAP($SQL) != 0) {
			$QRY = SAPSelect($SQL);
			$r = 0; $tmp = 0;
			while($result = odbc_fetch_array($QRY)) {
				$r++;
				for($m = 1; $m <= 12; $m++) {
					if($m == 1) {
						$WhseSale1H[$r][$m]['r1'] = $result['M_00'];
					}else{
						$WhseSale1H[$r][$m]['r1'] = $tmp;
					}

					if($m < 10) {
						$WhseSale1H[$r][$m]['r2'] = $result['M_0'.$m.'_I'];
						$WhseSale1H[$r][$m]['r3'] = $result['M_0'.$m.'_O'];
					}else{
						$WhseSale1H[$r][$m]['r2'] = $result['M_'.$m.'_I'];
						$WhseSale1H[$r][$m]['r3'] = $result['M_'.$m.'_O'];
					}
				
					$WhseSale1H[$r][$m]['r4'] = $WhseSale1H[$r][$m]['r1']+($WhseSale1H[$r][$m]['r2']+$WhseSale1H[$r][$m]['r3']);
					$tmp = $WhseSale1H[$r][$m]['r4'];

					if($m < 10) {
						$WhseSale1H[$r][$m]['r5'] = $result['M_0'.$m.'_O1'];
						$WhseSale1H[$r][$m]['r6'] = $result['M_0'.$m.'_O2'];
						$WhseSale1H[$r][$m]['r7'] = $result['M_0'.$m.'_O3'];
					}else{
						$WhseSale1H[$r][$m]['r5'] = $result['M_'.$m.'_O1'];
						$WhseSale1H[$r][$m]['r6'] = $result['M_'.$m.'_O2'];
						$WhseSale1H[$r][$m]['r7'] = $result['M_'.$m.'_O3'];
					}
				}
			}

			for($m = 1; $m <= 12; $m++) { 
				${"R1m".$m} = 0; ${"R2m".$m} = 0; ${"R3m".$m} = 0; ${"R4m".$m} = 0; ${"R5m".$m} = 0; ${"R6m".$m} = 0; ${"R7m".$m} = 0; 
			}

			if(substr($SelectSlpCode,0,1) == 'T') {
				for($m = 1; $m <= 12; $m++) {
					for($i = 1; $i <= $r; $i++) {
						${"R1m".$m} = ${"R1m".$m}+$WhseSale1H[$i][$m]['r1'];
						${"R2m".$m} = ${"R2m".$m}+$WhseSale1H[$i][$m]['r2'];
						${"R3m".$m} = ${"R3m".$m}+$WhseSale1H[$i][$m]['r3'];
						${"R4m".$m} = ${"R4m".$m}+$WhseSale1H[$i][$m]['r4'];
						${"R5m".$m} = ${"R5m".$m}+$WhseSale1H[$i][$m]['r5'];
						${"R6m".$m} = ${"R6m".$m}+$WhseSale1H[$i][$m]['r6'];
						${"R7m".$m} = ${"R7m".$m}+$WhseSale1H[$i][$m]['r7'];
					}
					$WhseSaleTeam1H[1][$m]['r1'] = ${"R1m".$m};
					$WhseSaleTeam1H[1][$m]['r2'] = ${"R2m".$m};
					$WhseSaleTeam1H[1][$m]['r3'] = ${"R3m".$m};
					$WhseSaleTeam1H[1][$m]['r4'] = ${"R4m".$m};
					$WhseSaleTeam1H[1][$m]['r5'] = ${"R5m".$m};
					$WhseSaleTeam1H[1][$m]['r6'] = ${"R6m".$m};
					$WhseSaleTeam1H[1][$m]['r7'] = ${"R7m".$m};
				}
			}
		}
	// (13)>
	// ต้นทุนคลังเซลส์มือสอง (บาท)
		$WhrSQL = (substr($SelectSlpCode,0,1) == 'T') ? "T1.Location IN (6) AND T1.StreetNo = '$Team'" : "T1.Location IN (6) AND T1.Block = '".$Ukey[1]."'";

		$SQL = "
			SELECT
				A0.Warehouse, A0.WhsName,
				SUM(A0.M_00 * A0.Cost) AS 'M_00',";
				for($m = 1; $m <= 12; $m++) {
					if($m < 10) {
						$SQL .= "
						SUM(A0.M_0".$m."_I * A0.Cost) AS 'M_0".$m."_I', SUM(A0.M_0".$m."_O * A0.Cost) AS 'M_0".$m."_O', SUM(A0.M_0".$m."_O1 * A0.Cost) AS 'M_0".$m."_O1', SUM(A0.M_0".$m."_O2 * A0.Cost) AS 'M_0".$m."_O2', SUM(A0.M_0".$m."_O3 * A0.Cost) AS 'M_0".$m."_O3'";
					}else{
						$SQL .= "
						SUM(A0.M_".$m."_I * A0.Cost) AS 'M_".$m."_I', SUM(A0.M_".$m."_O * A0.Cost) AS 'M_".$m."_O', SUM(A0.M_".$m."_O1 * A0.Cost) AS 'M_".$m."_O1', SUM(A0.M_".$m."_O2 * A0.Cost) AS 'M_".$m."_O2', SUM(A0.M_".$m."_O3 * A0.Cost) AS 'M_".$m."_O3'";
					}
					$SQL .= ($m < 12) ? "," : "";
				}
			$SQL .= "
			FROM (
				SELECT T0.Warehouse, T1.WhsName, T0.ItemCode,
				(CASE WHEN T2.LastPurDat = '2022-12-31' OR T2.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc,T2.LastPurPrc) ELSE T2.LastPurPrc END) * 1.07 AS 'Cost',
				(CASE WHEN T0.CreateDate = '2023-01-01'  OR (YEAR(T0.CreateDate) <= $pYear) THEN SUM(T0.InQty-T0.OutQty) ELSE 0 END) AS 'M_00',";
				for($m = 1; $m <= 12; $m++) {
					if($m < 10) {
						$SQL .= "
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.InQty > 0) THEN SUM(T0.InQty) ELSE 0 END) AS 'M_0".$m."_I',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 13) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O1',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 15) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O2',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType IN (60,67)) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_0".$m."_O3'";
					}else{
						$SQL .= "
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.InQty > 0) THEN SUM(T0.InQty) ELSE 0 END) AS 'M_".$m."_I',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 13) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O1',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType = 15) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O2',
						(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $cYear AND MONTH(T0.CreateDate) = ".$m." AND T0.OutQty > 0 AND T0.TransType IN (60,67)) THEN -SUM(T0.OutQty) ELSE 0 END) AS 'M_".$m."_O3'";
					}
					$SQL .= ($m < 12) ? "," : "";
				}
				$SQL .= "
				FROM OINM T0
				LEFT JOIN OWHS T1 ON T0.Warehouse = T1.WhsCode
				LEFT JOIN OITM T2 ON T0.ItemCode = T2.ItemCode
				LEFT JOIN KBI_DB2022.dbo.OITM T3 ON T0.ItemCode = T3.ItemCode
				WHERE $WhrSQL
				GROUP BY T0.TransType, T0.Warehouse, T1.WhsName, T0.CreateDate, T0.ItemCode, T0.InQty, T0.OutQty, T2.LastPurDat, T3.LastPurDat, T2.LastPurPrc, T3.LastPurPrc
			) A0
			GROUP BY A0.Warehouse, A0.WhsName
			ORDER BY A0.Warehouse";
		// echo $SQL;
		if(ChkRowSAP($SQL) != 0) {
			$QRY = SAPSelect($SQL);
			$r = 0; $tmp = 0;
			while($result = odbc_fetch_array($QRY)) {
				$r++;
				for($m = 1; $m <= 12; $m++) {
					if($m == 1) {
						$WhseSale[$r][$m]['r1'] = $result['M_00'];
					}else{
						$WhseSale[$r][$m]['r1'] = $tmp;
					}
	
					if($m < 10) {
						$WhseSale[$r][$m]['r2'] = $result['M_0'.$m.'_I'];
						$WhseSale[$r][$m]['r3'] = $result['M_0'.$m.'_O'];
					}else{
						$WhseSale[$r][$m]['r2'] = $result['M_'.$m.'_I'];
						$WhseSale[$r][$m]['r3'] = $result['M_'.$m.'_O'];
					}
				
					$WhseSale[$r][$m]['r4'] = $WhseSale[$r][$m]['r1']+($WhseSale[$r][$m]['r2']+$WhseSale[$r][$m]['r3']);
					$tmp = $WhseSale[$r][$m]['r4'];
	
					if($m < 10) {
						$WhseSale[$r][$m]['r5'] = $result['M_0'.$m.'_O1'];
						$WhseSale[$r][$m]['r6'] = $result['M_0'.$m.'_O2'];
						$WhseSale[$r][$m]['r7'] = $result['M_0'.$m.'_O3'];
					}else{
						$WhseSale[$r][$m]['r5'] = $result['M_'.$m.'_O1'];
						$WhseSale[$r][$m]['r6'] = $result['M_'.$m.'_O2'];
						$WhseSale[$r][$m]['r7'] = $result['M_'.$m.'_O3'];
					}
				}
			}

			for($m = 1; $m <= 12; $m++) { 
				${"R1m".$m} = 0; ${"R2m".$m} = 0; ${"R3m".$m} = 0; ${"R4m".$m} = 0; ${"R5m".$m} = 0; ${"R6m".$m} = 0; ${"R7m".$m} = 0; 
			}

			if(substr($SelectSlpCode,0,1) == 'T') {
				for($m = 1; $m <= 12; $m++) {
					for($i = 1; $i <= $r; $i++) {
						${"R1m".$m} = ${"R1m".$m}+$WhseSale[$i][$m]['r1'];
						${"R2m".$m} = ${"R2m".$m}+$WhseSale[$i][$m]['r2'];
						${"R3m".$m} = ${"R3m".$m}+$WhseSale[$i][$m]['r3'];
						${"R4m".$m} = ${"R4m".$m}+$WhseSale[$i][$m]['r4'];
						${"R5m".$m} = ${"R5m".$m}+$WhseSale[$i][$m]['r5'];
						${"R6m".$m} = ${"R6m".$m}+$WhseSale[$i][$m]['r6'];
						${"R7m".$m} = ${"R7m".$m}+$WhseSale[$i][$m]['r7'];
					}
					$WhseSaleTeam[1][$m]['r1'] = ${"R1m".$m};
					$WhseSaleTeam[1][$m]['r2'] = ${"R2m".$m};
					$WhseSaleTeam[1][$m]['r3'] = ${"R3m".$m};
					$WhseSaleTeam[1][$m]['r4'] = ${"R4m".$m};
					$WhseSaleTeam[1][$m]['r5'] = ${"R5m".$m};
					$WhseSaleTeam[1][$m]['r6'] = ${"R6m".$m};
					$WhseSaleTeam[1][$m]['r7'] = ${"R7m".$m};
				}
			}
		}
	// (14)>
	// ต้นทุนคลังสินค้าจอง
		$DocYear = $cYear;
		$ArrCPType  = ['Q', 'F'];
		if (substr($SelectSlpCode,0,1) != 'T') {
			$GETTeam = MySQLSelect("SELECT MainTeam FROM oslp WHERE Ukey = '".$Ukey[1]."'");
			$Team = $GETTeam['MainTeam'];
		}

		$SQL0 = "SELECT T0.ItemCode, ISNULL(DATEDIFF(m,(CASE WHEN T0.LastPurDat = '2022-12-31' OR T0.LastPurDat IS NULL THEN T1.LastPurDat ELSE ISNULL(T0.LastPurDat, T1.LastPurDat) END), GETDATE()),9999) AS 'Aging' FROM OITM T0 LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode ORDER BY T0.ItemCode";
		$QRY0 = SAPSelect($SQL0);
		while($RST0 = odbc_fetch_array($QRY0)) {
			$Aging[$RST0['ItemCode']] = $RST0['Aging'];
		}

		for($c = 0; $c < count($ArrCPType); $c++) {
			$CPType = $ArrCPType[$c];
			switch($CPType) {
				case "Q":
					$TextArray = array("","ยกมา ณ ต้นเดือน","ตั้งเป้าเพิ่มเติม","ต้นทุนออก (ขาย)","ต้นทุนออก (แถม)","ต้นทุนออก (ทั้งหมด)","คงเหลือ ณ สิ้นเดือน","คงเหลือ (Aging 0 - 3 เดือน)","คงเหลือ (Aging 4 - 6 เดือน)","คงเหลือ (Aging 7 - 12 เดือน)","คงเหลือ (Aging มากกว่า 12 เดือน)","T/O (เดือน)","ต้นทุนเข้าสะสม (เป้าหมาย)","ต้นทุนออกสะสม","% ความสำเร็จ (&#8805; 70% ของเป้า)");
				break;
				case "F":
					$TextArray = array("","ยกมา ณ ต้นเดือน","ตั้งเป้าเพิ่มเติม","ต้นทุนออก (ขาย)","ต้นทุนออก (แถม)","ต้นทุนออก (ทั้งหมด)","คงเหลือ ณ สิ้นเดือน","คงเหลือ (Aging 0 - 3 เดือน)","คงเหลือ (Aging 4 - 6 เดือน)","คงเหลือ (Aging 7 - 12 เดือน)","คงเหลือ (Aging มากกว่า 12 เดือน)","T/O (เดือน)","ต้นทุนเข้าสะสม (เป้าหมาย)","ต้นทุนออกสะสม","% ความสำเร็จ (&#8805; 70% ของเป้าไตรมาส)");
				break;
			}

			for($m = 1; $m <= 12; $m++) {
				$Data[$CPType]['R1'][$m] = 0; // ยกมา
				$Data[$CPType]['R2'][$m] = 0; // ตั้งเป้าเพิ่มเติม
				$Data[$CPType]['R3'][$m] = 0; // ต้นทุนออก (ขาย)
				$Data[$CPType]['R4'][$m] = 0; // ต้นทุนออก (แถม)
				$Data[$CPType]['R5'][$m] = 0; // ต้นทุนออก (ทั้งหมด)
				$Data[$CPType]['R6'][$m] = 0; // คงเหลือ
				$Data[$CPType]['R7'][$m] = 0; // คงเหลือ
				$Data[$CPType]['R8'][$m] = 0; // คงเหลือ
				$Data[$CPType]['R9'][$m] = 0; // คงเหลือ
				$Data[$CPType]['R10'][$m] = 0; // คงเหลือ
				$Data[$CPType]['R11'][$m] = 0; // T/O (7)
				$Data[$CPType]['R12'][$m] = 0; // $SumIn (8)
				$Data[$CPType]['R13'][$m] = 0; // $SumOut (9)
				$Data[$CPType]['R14'][$m] = 0; // % of Success (10)
				
			}

			$SQL1 = "
				SELECT
					B0.TeamCode, B0.CPType,
					SUM(B0.M_01) AS 'M_01',
					SUM(B0.M_02) AS 'M_02',
					SUM(B0.M_03) AS 'M_03',
					SUM(B0.M_04) AS 'M_04',
					SUM(B0.M_05) AS 'M_05',
					SUM(B0.M_06) AS 'M_06',
					SUM(B0.M_07) AS 'M_07',
					SUM(B0.M_08) AS 'M_08',
					SUM(B0.M_09) AS 'M_09',
					SUM(B0.M_10) AS 'M_10',
					SUM(B0.M_11) AS 'M_11',
					SUM(B0.M_12) AS 'M_12'
				FROM
				(
					SELECT
						A0.TeamCode, A0.CPType,
						CASE WHEN A0.DocMonth = 1 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_01',
						CASE WHEN A0.DocMonth = 2 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_02',
						CASE WHEN A0.DocMonth = 3 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_03',
						CASE WHEN A0.DocMonth = 4 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_04',
						CASE WHEN A0.DocMonth = 5 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_05',
						CASE WHEN A0.DocMonth = 6 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_06',
						CASE WHEN A0.DocMonth = 7 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_07',
						CASE WHEN A0.DocMonth = 8 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_08',
						CASE WHEN A0.DocMonth = 9 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_09',
						CASE WHEN A0.DocMonth = 10 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_10',
						CASE WHEN A0.DocMonth = 11 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_11',
						CASE WHEN A0.DocMonth = 12 THEN SUM(A0.TargetValue) ELSE 0 END AS 'M_12'
					FROM (
						SELECT
							T0.TeamCode, T0.CPType, MONTH(T0.StartDate) AS 'DocMonth', SUM(T1.TargetTotal * T1.Cxst) AS 'TargetValue'
						FROM tarsku_header T0
						LEFT JOIN tarsku_itemlist T1 ON T0.CPEntry = T1.CPEntry
						WHERE YEAR(T0.StartDate) = $DocYear AND T0.CPType = '$CPType' AND T0.CANCELED = 'N' AND T0.DocStatus = 'C' AND T0.TeamCode = '$Team'
						GROUP BY T0.DocNum, T0.CPType, T0.StartDate
					) A0
					GROUP BY A0.TeamCode, A0.CPType, A0.DocMonth
				) B0
				GROUP BY B0.TeamCode, B0.CPType
				ORDER BY
					CASE
						WHEN B0.TeamCode = 'MT1' THEN 1
						WHEN B0.TeamCode = 'MT2' THEN 2
						WHEN B0.TeamCode = 'TT2' THEN 3
						WHEN B0.TeamCode = 'OUL' THEN 4
						WHEN B0.TeamCode = 'ONL' THEN 5
						ELSE 6
					END";

			$QRY1 = MySQLSelectX($SQL1);
			while($RST1 = mysqli_fetch_array($QRY1)) {
				for($m = 1; $m <= 12; $m++) {
					if($m < 10) {
						$Input = $RST1['M_0'.$m];
					} else {
						$Input = $RST1['M_'.$m];
					}
					$Data[$CPType]['R2'][$m] = $Input;
				}
			}

			$Start = 0;
			$Ended = 0;
			$SumIn = 0;
			$SumOut =0;

			for($m = 1; $m <= 12; $m++) {
				$L1_In[$m] = 0;
				$L2_In[$m] = 0;
				$L3_In[$m] = 0;
				$L4_In[$m] = 0;
				$L1_Out[$m] = 0;
				$L2_Out[$m] = 0;
				$L3_Out[$m] = 0;
				$L4_Out[$m] = 0;
			}
	
			$Start_L1 = 0;
			$Start_L2 = 0;
			$Start_L3 = 0;
			$Start_L4 = 0;
			$Ended_L1 = 0;
			$Ended_L2 = 0;
			$Ended_L3 = 0;
			$Ended_L4 = 0;

			for($m = 1; $m <= 12; $m++) {
				if($CPType == "F" && ( ($DocYear == 2023 && ($m == 1 || $m == 4 || $m == 6 || $m == 10)) || ($DocYear > 2023 && ($m == 1 || $m == 4 || $m == 7 || $m == 10)))) {
					$Start = 0;
					$Ended = 0;
	
					$L1_In[$m] = 0;
					$L2_In[$m] = 0;
					$L3_In[$m] = 0;
					$L4_In[$m] = 0;
					$L1_Out[$m] = 0;
					$L2_Out[$m] = 0;
					$L3_Out[$m] = 0;
					$L4_Out[$m] = 0;
				
					$Start_L1 = 0;
					$Start_L2 = 0;
					$Start_L3 = 0;
					$Start_L4 = 0;
					$Ended_L1 = 0;
					$Ended_L2 = 0;
					$Ended_L3 = 0;
					$Ended_L4 = 0;

					$SumIn = 0;
					$SumOut =0;
				}
				
				$SQL2 = 
					"SELECT DISTINCT T1.ItemCode, T1.Cxst
					FROM tarsku_header T0
					LEFT JOIN tarsku_itemlist T1 ON T0.CPEntry = T1.CPEntry
					WHERE (T0.TeamCode = '$Team' AND T0.CPType = '$CPType' AND T0.CANCELED = 'N' AND T0.DocStatus = 'C') AND (YEAR(T0.StartDate) = $DocYear AND (MONTH(T0.StartDate) <= $m AND MONTH(T0.EndDate) >= $m))";
				
				/* ROW 3 4 5 10 */
				if(ChkRowDB($SQL2) > 0) {
					$ItemSet = array();
					$QRY2 = MySQLSelectX($SQL2);
					$ItemSQL = "";
					$r = 1;
					while($RST2 = mysqli_fetch_array($QRY2)) {
						$ItemSet[$RST2['ItemCode']]['Code']      = $RST2['ItemCode'];
						$ItemSet[$RST2['ItemCode']]['Cost']      = $RST2['Cxst'];
						$ItemSet[$RST2['ItemCode']]['Aging']     = $Aging[$RST2['ItemCode']];
						$ItemSQL .= "'".$RST2['ItemCode']."'";
						if($r != ChkRowDB($SQL2)) { $ItemSQL .= ", "; }
						$r++;
					}
					if($Team == "OUL") {
						$TeamSQL = "T2.U_Dim1 IN ('TT1','OUL')";
					} else {
						$TeamSQL = "T2.U_Dim1 IN ('".$Team."')";
					}

					$SQL2_5 =
						"SELECT
							T0.TeamCode, T0.CPType, MONTH(T0.StartDate) AS 'DocMonth', T1.ItemCode, SUM(T1.TargetTotal * T1.Cxst) AS 'Cxst'
						FROM tarsku_header T0
						LEFT JOIN tarsku_itemlist T1 ON T0.CPEntry = T1.CPEntry
						WHERE (T0.TeamCode = '".$Team."' AND T0.CPType = '$CPType' AND T0.CANCELED = 'N' AND T0.DocStatus = 'C') AND (YEAR(T0.StartDate) = $DocYear AND (MONTH(T0.StartDate) <= $m AND MONTH(T0.EndDate) >= $m))
						GROUP BY T0.DocNum, T0.CPType, T0.StartDate, T1.ItemCode";

					$QRY2_5 = MySQLSelectX($SQL2_5);
					while($RST2_5 = mysqli_fetch_array($QRY2_5)) {
						if($ItemSet[$RST2_5['ItemCode']]['Aging'] >= 0 && $ItemSet[$RST2_5['ItemCode']]['Aging'] <= 3) {
							$L1_In[$RST2_5['DocMonth']] = $L1_In[$RST2_5['DocMonth']] + $RST2_5['Cxst'];
						} elseif($ItemSet[$RST2_5['ItemCode']]['Aging'] >= 4 && $ItemSet[$RST2_5['ItemCode']]['Aging'] <= 6) {
							$L2_In[$RST2_5['DocMonth']] = $L2_In[$RST2_5['DocMonth']] + $RST2_5['Cxst'];
						} elseif($ItemSet[$RST2_5['ItemCode']]['Aging'] >= 7 && $ItemSet[$RST2_5['ItemCode']]['Aging'] <= 12) {
							$L3_In[$RST2_5['DocMonth']] = $L3_In[$RST2_5['DocMonth']] + $RST2_5['Cxst'];
						} else {
							$L4_In[$RST2_5['DocMonth']] = $L4_In[$RST2_5['DocMonth']] + $RST2_5['Cxst'];
						}
					}

					/* GET SALE QUANTITY EXPECT SlpCode != 20,123,124,125,126,291,296,290,23,24,77,158 */
					$SQL3 =
						"SELECT
							A0.ItemCode, A0.SaleType, SUM(A0.Quantity) AS 'Quantity', SUM(A0.LineTotal) AS 'LineTotal'
						FROM (
						SELECT
							T0.ItemCode,
							CASE WHEN T0.PriceAfVat = 0 THEN 'FREE' ELSE 'SALE' END AS 'SaleType'
							, SUM(T0.Quantity) AS 'Quantity', SUM(T0.Quantity * T0.PriceAfVat) AS 'LineTotal'
						FROM INV1 T0
						LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
						LEFT JOIN OSLP T2 ON T0.SlpCode  = T2.SlpCode
						WHERE T0.ItemCode IN ($ItemSQL) AND T1.CANCELED = 'N' AND $TeamSQL AND (YEAR(T0.DocDate) = $DocYear AND MONTH(T0.DocDate) = $m) AND T0.SlpCode NOT IN (20,123,124,125,126,291,296)
						GROUP BY T0.ItemCode, CASE WHEN T0.PriceAfVat = 0 THEN 'FREE' ELSE 'SALE' END
						UNION ALL
						SELECT
							T0.ItemCode,
							CASE WHEN T0.PriceAfVat = 0 THEN 'FREE' ELSE 'SALE' END AS 'SaleType'
							, -SUM(T0.Quantity) AS 'Quantity', -SUM(T0.Quantity * T0.PriceAfVat) AS 'LineTotal'
						FROM RIN1 T0
						LEFT JOIN ORIN T1 ON T0.DocEntry = T1.DocEntry
						LEFT JOIN OSLP T2 ON T0.SlpCode  = T2.SlpCode
						WHERE T0.ItemCode IN ($ItemSQL) AND T1.CANCELED = 'N' AND $TeamSQL AND (YEAR(T0.DocDate) = $DocYear AND MONTH(T0.DocDate) = $m) AND T0.SlpCode NOT IN (20,123,124,125,126,291,296)
						GROUP BY T0.ItemCode, CASE WHEN T0.PriceAfVat = 0 THEN 'FREE' ELSE 'SALE' END
						) A0
						GROUP BY A0.ItemCode, A0.SaleType";
					if(ChkRowSAP($SQL3) > 0) {
						$QRY3 = SAPSelect($SQL3);
						$FreeCost = 0;
						$SaleCost = 0;
						$LineTotal = 0;
						while($RST3 = odbc_fetch_array($QRY3)) {
							if($RST3['SaleType'] == "FREE") {
								$FreeCost = $FreeCost + ($RST3['Quantity'] * $ItemSet[$RST3['ItemCode']]['Cost']);
							} else {
								$SaleCost = $SaleCost + ($RST3['Quantity'] * $ItemSet[$RST3['ItemCode']]['Cost']);
							}
							$LineTotal = $LineTotal + $RST3['LineTotal'];
	
							if($ItemSet[$RST3['ItemCode']]['Aging'] >= 0 && $ItemSet[$RST3['ItemCode']]['Aging'] <= 3) {
								$L1_Out[$m] = $L1_Out[$m] + ($RST3['Quantity'] * $ItemSet[$RST3['ItemCode']]['Cost']);
							} elseif($ItemSet[$RST3['ItemCode']]['Aging'] >= 4 && $ItemSet[$RST3['ItemCode']]['Aging'] <= 6) {
								$L2_Out[$m] = $L2_Out[$m] + ($RST3['Quantity'] * $ItemSet[$RST3['ItemCode']]['Cost']);
							} elseif($ItemSet[$RST3['ItemCode']]['Aging'] >= 7 && $ItemSet[$RST3['ItemCode']]['Aging'] <= 12) {
								$L3_Out[$m] = $L3_Out[$m] + ($RST3['Quantity'] * $ItemSet[$RST3['ItemCode']]['Cost']);
							} else {
								$L4_Out[$m] = $L4_Out[$m] + ($RST3['Quantity'] * $ItemSet[$RST3['ItemCode']]['Cost']);
							}
						}
						$Data[$CPType]['R3'][$m] = $SaleCost;
						$Data[$CPType]['R4'][$m] = $FreeCost;
						$Data[$CPType]['R5'][$m] = $FreeCost + $SaleCost;
						$SumIn  = $SumIn + $Data[$CPType]['R2'][$m];
						$SumOut = $SumOut + $Data[$CPType]['R5'][$m];
	
						$Data[$CPType]['R12'][$m] = $SumIn;
						$Data[$CPType]['R13'][$m] = $SumOut;
	
						if($SumIn > 0) {
							$PoS = ($SumOut / $SumIn) * 100;
						} else {
							$PoS = 0;
						}
						$Data[$CPType]['R14'][$m] = $PoS;
					}
				}

				/* ROW 1 6 11 */
				if($m <= date("m")) {
					$Start = $Ended;
				} else {
					$Start = 0;
				}
				if($m == 1) {
					$Data[$CPType]['R1'][$m] = 0;
				} else {
					$Data[$CPType]['R1'][$m] = $Start;
				}
				$Start = $Data[$CPType]['R1'][$m];
				$Ended = $Start + ($Data[$CPType]['R2'][$m] - $Data[$CPType]['R5'][$m]);
				$Data[$CPType]['R6'][$m] = $Ended;
				if($Data[$CPType]['R5'][$m] != 0) {
					$Data[$CPType]['R11'][$m] = $Data[$CPType]['R6'][$m] / $Data[$CPType]['R5'][$m];
				}

				/* ROW 7 8 9 10 */
				if($m <= date("m")) {
					$Start_L1 = $Ended_L1;
					$Start_L2 = $Ended_L2;
					$Start_L3 = $Ended_L3;
					$Start_L4 = $Ended_L4;
				} else {
					$Start_L1 = 0;
					$Start_L2 = 0;
					$Start_L3 = 0;
					$Start_L4 = 0;
				}
	
				if($m == 1) {
					$SUM_L1[$m] = 0;
					$SUM_L2[$m] = 0;
					$SUM_L3[$m] = 0;
					$SUM_L4[$m] = 0;
				} else {
					$SUM_L1[$m] = $Start_L1;
					$SUM_L2[$m] = $Start_L2;
					$SUM_L3[$m] = $Start_L3;
					$SUM_L4[$m] = $Start_L4;
				}

				$Start_L1 = $SUM_L1[$m];
				$Ended_L1 = $Start_L1 + ($L1_In[$m] - $L1_Out[$m]);
				$Data[$CPType]['R7'][$m] = $Ended_L1;

				$Start_L2 = $SUM_L2[$m];
				$Ended_L2 = $Start_L2 + ($L2_In[$m] - $L2_Out[$m]);
				$Data[$CPType]['R8'][$m] = $Ended_L2;
				
				$Start_L3 = $SUM_L3[$m];
				$Ended_L3 = $Start_L3 + ($L3_In[$m] - $L3_Out[$m]);
				$Data[$CPType]['R9'][$m] = $Ended_L3;

				$Start_L4 = $SUM_L4[$m];
				$Ended_L4 = $Start_L4 + ($L4_In[$m] - $L4_Out[$m]);
				$Data[$CPType]['R10'][$m] = $Ended_L4;
			}
		}
		
	//> PUSH ALL DATA <//
	for($m = 1; $m <= 12; $m++) {
	// (1)>
		if($trg[$m]    == 0)    { $trg[$m]       = "-";    }else{ $trg[$m]       = number_format($trg[$m],0);       } // ยอดขาย
		if($SALES[$m]  == 0)    { $SALES[$m]     = "-";    }else{ $SALES[$m]     = number_format($SALES[$m],0);     } // ยอดขาย
		if($TrgPer[$m] == 0)    { $TrgPer[$m]    = "0.00"; }else{ $TrgPer[$m]    = number_format($TrgPer[$m],2);    } // สัดส่วนยอดขายต่อเป้าขาย (%)
		if($GP[$m] == 0)        { $GP[$m]        = "0.00"; }else{ $GP[$m]        = number_format($GP[$m],2);        } // สัดส่วนยอดขายต่อเป้าขาย (%)
	// (2)>
		if($CusTar[$m] == 0)    { $CusTar[$m]    = "-";    }else{ $CusTar[$m]    = number_format($CusTar[$m],0);    } // จำนวนลูกค้าเก่า ทั้งหมด
		if($NewCus[$m] == 0)    { $NewCus[$m]    = "-";    }else{ $NewCus[$m]    = number_format($NewCus[$m],0);    } // จำนวนลูกค้าผู้มุ่งหวัง (ราย)
	// (3)>
		if($O_OldCus[$m]  == 0) { $O_OldCus[$m]  = "-";    }else{ $O_OldCus[$m]  = number_format($O_OldCus[$m],0);  } // เป้าหมายลูกค้าเก่าที่ต้องเข้าพบ (ราย)
		if($O_MeetCus[$m] == 0) { $O_MeetCus[$m] = "-";    }else{ $O_MeetCus[$m] = number_format($O_MeetCus[$m],0); } // จำนวนลูกค้าเก่าที่เข้าพบ (ราย)
		if($O_Per[$m]     == 0) { $O_Per[$m]     = "0.00"; }else{ $O_Per[$m]     = number_format($O_Per[$m],0);     } // สัดส่วนการเข้าพบจริง (%) 1
	// (4)>
		if($N_OldCus[$m]  == 0) { $N_OldCus[$m]  = "-";    }else{ $N_OldCus[$m]  = number_format($N_OldCus[$m],0);  } // เป้าหมายลูกค้ามุ่งหวังที่ตั้งใจเข้าพบ (ราย)
		if($N_MeetCus[$m] == 0) { $N_MeetCus[$m] = "-";    }else{ $N_MeetCus[$m] = number_format($N_MeetCus[$m],0); } // จำนวนลูกค้ามุ่งหวังที่เข้าพบ (ราย)
		if($N_Per[$m]     == 0) { $N_Per[$m]     = "0.00"; }else{ $N_Per[$m]     = number_format($N_Per[$m],0);     } // สัดส่วนการเข้าพบจริง (%) 2
	// (5)>
		if($nOpenCus[$m]  == 0) { $nOpenCus[$m]  = "-";    }else{ $nOpenCus[$m]  = number_format($nOpenCus[$m],0);  } // จำนวนร้านค้าที่เปิดใหม่ (ราย)
		if($LOpenCus[$m]  == 0) { $LOpenCus[$m]  = "-";    }else{ $LOpenCus[$m]  = number_format($LOpenCus[$m],0);  } // จำนวนร้านค้าที่เปิดบิล (ราย)
		if($bOpenCus[$m]  == 0) { $bOpenCus[$m]  = "-";    }else{ $bOpenCus[$m]  = number_format($bOpenCus[$m],0);  } // จำนวนบิลที่เปิด (บิล)
	// (6)>
		if($PLAN80[$m]  == 0)   { $PLAN80[$m]    = "-";    }else{ $PLAN80[$m]    = number_format($PLAN80[$m],0);    } // รายงานประจำวัน (> 80 ร้านค้า/เดือน)
		if($PLAN1[$m]  == 0)    { $PLAN1[$m]     = "-";    }else{ $PLAN1[$m]     = number_format($PLAN1[$m],0);     } // รายงานประจำเดือน (> 1 ครั้ง/เดือน)
		if($Album[$m]  == 0)    { $Album[$m]     = "-";    }else{ $Album[$m]     = number_format($Album[$m],0);     } // การสำรวจราคาร้านค้า (ส่งรูปอัลบั้มไลน์)
	// (7)>
		if(date("m") < 10) { $Nee_m = substr(date("m"),1); }else{ $Nee_m = date("m"); } 
		if($Nee['B30D'][$m] == 0){                                                                                    // หนี้เกินกำหนด < 30 วัน (บาท)
			if($Nee_m == $m) { $Nee['B30D'][$m] = "0"; }else{ $Nee['B30D'][$m] = ""; }
		}else{ 
			$Nee['B30D'][$m] = number_format($Nee['B30D'][$m],0); 
		} 
		if($Nee['B31D-B91D'][$m] == 0){                                                                               // หนี้เกินกำหนด 31-90 วัน (บาท)
			if($Nee_m == $m) { $Nee['B31D-B91D'][$m] = "0"; }else{ $Nee['B31D-B91D'][$m] = ""; }
		}else{ 
			$Nee['B31D-B91D'][$m] = number_format($Nee['B31D-B91D'][$m],0); 
		} 
		if($Nee['A90D'][$m] == 0){                                                                                    // หนี้เกินกำหนด > 90 วัน (บาท)
			if($Nee_m == $m) { $Nee['A90D'][$m] = "0"; }else{ $Nee['A90D'][$m] = ""; }
		}else{ 
			$Nee['A90D'][$m] = number_format($Nee['A90D'][$m],0); 
		} 
	// (8)>
		if($ChkD[$m]  == 0)     { $ChkD[$m]     = "-";     }else{ $ChkD[$m]       = number_format($ChkD[$m],0);     } // จำนวนเช็คเด้ง (ใบ)
		if($ChkDue[$m]  == 0)   { $ChkDue[$m]   = "-";     }else{ $ChkDue[$m]     = number_format($ChkDue[$m],0);   } // จำนวนเช็คเกินกำหนด > 30 วัน (บิล)
	// (9)>
		if($KPro['KBI'][$m]== 0){ $KPro['KBI'][$m] = "-";  }else{ $KPro['KBI'][$m] = number_format($KPro['KBI'][$m],0); } // มูลค่ารวมการคืนสินค้า (บาท)
		if($KPro['SA'][$m]== 0) { $KPro['SA'][$m] = "-";   }else{ $KPro['SA'][$m]  = number_format($KPro['SA'][$m],0);  } // ต้นทุนรวมเซลส์รับผิดชอบที่รับสินค้าคืน (บาท)
	// (10)>
		if($borrowed[$m]  == 0) {      	                                                                              // มูลค่าการยืมสินค้าที่ยังไม่คืน (บาท)
			if($m == intval($cMonth)) {	$borrowed[$m] = "0"; }else{ $borrowed[$m] = ""; };                            
			      
		}else{ 
			$borrowed[$m]    = number_format($borrowed[$m],0);     
		} 
		if($borrowedDif[$m] == 0 || $borrowedDif[$m] == '') { 														  // มูลค่าการยืมสินค้าที่ยังไม่คืน > 6 เดือน (บาท)
			if($m == intval($cMonth)) {	$borrowedDif[$m] = "0"; }else{ $borrowedDif[$m] = ""; };
		}else{ 
			$borrowedDif[$m] = number_format($borrowedDif[$m],0);
		} 
	// (11)> // ต้นทุน
		
	}
	$arrCol['trg']         = $trg;       // เป้าขาย
	$arrCol['SALES']       = $SALES;     // ยอดขาย
	$arrCol['TrgPer']      = $TrgPer;    // สัดส่วนยอดขายต่อเป้าขาย (%)
	$arrCol['GP']          = $GP;        // GP
	$arrCol['CusTar']      = $CusTar;    // จำนวนลูกค้าเก่า ทั้งหมด
	$arrCol['NewCus']      = $NewCus;    // จำนวนลูกค้าผู้มุ่งหวัง (ราย)
	$arrCol['O_OldCus']    = $O_OldCus;  // เป้าหมายลูกค้าเก่าที่ต้องเข้าพบ (ราย)
	$arrCol['O_MeetCus']   = $O_MeetCus; // จำนวนลูกค้าเก่าที่เข้าพบ (ราย)
	$arrCol['O_Per']       = $O_Per;     // สัดส่วนการเข้าพบจริง (%) 1
	$arrCol['N_OldCus']    = $N_OldCus;  // เป้าหมายลูกค้ามุ่งหวังที่ตั้งใจเข้าพบ (ราย)
	$arrCol['N_MeetCus']   = $N_MeetCus; // จำนวนลูกค้ามุ่งหวังที่เข้าพบ (ราย)
	$arrCol['N_Per']       = $N_Per;     // สัดส่วนการเข้าพบจริง (%) 2
	$arrCol['nOpenCus']    = $nOpenCus;  // จำนวนร้านค้าที่เปิดใหม่ (ราย)
	$arrCol['LOpenCus']    = $LOpenCus;  // จำนวนร้านค้าที่เปิดบิล (ราย)
	$arrCol['bOpenCus']    = $bOpenCus;  // จำนวนบิลที่เปิด (บิล)
	$arrCol['PLAN80']      = $PLAN80;    // รายงานประจำวัน (> 80 ร้านค้า/เดือน)
	$arrCol['PLAN1']       = $PLAN1;     // รายงานประจำเดือน (> 1 ครั้ง/เดือน)
	$arrCol['Album']       = $Album;     // การสำรวจราคาร้านค้า (ส่งรูปอัลบั้มไลน์)
	$arrCol['Nee']         = $Nee;       // หนี้เกินกำหนด < 30 วัน (บาท), หนี้เกินกำหนด 31-90 วัน (บาท), หนี้เกินกำหนด > 90 วัน (บาท)
	$arrCol['ChkD']        = $ChkD;      // จำนวนเช็คเด้ง (ใบ)
	$arrCol['ChkDue']      = $ChkDue;    // จำนวนเช็คเกินกำหนด > 30 วัน (บิล)
	$arrCol['KPro']        = $KPro;      // มูลค่ารวมการคืนสินค้า (บาท), ต้นทุนรวมเซลส์รับผิดชอบที่รับสินค้าคืน (บาท)
	$arrCol['borrowed']    = $borrowed;  // มูลค่าการยืมสินค้าที่ยังไม่คืน (บาท)
	$arrCol['borrowedDif'] = $borrowedDif;  // มูลค่าการยืมสินค้าที่ยังไม่คืน > 6 เดือน (บาท)
	if(ChkRowSAP($SQL) != 0) {
		if(substr($SelectSlpCode,0,1) == 'T') { // ความเคลื่อนไหวคลังเซลส์มือหนึ่ง
			$arrCol['WhseSale1H'] = $WhseSaleTeam1H[1]; 
		}else{
			$arrCol['WhseSale1H'] = $WhseSale1H[1]; 
		}
	}else{
		$arrCol['WhseSale1H'] = '-'; 
	}
	if(ChkRowSAP($SQL) != 0) {
		if(substr($SelectSlpCode,0,1) == 'T') { // ความเคลื่อนไหวคลังเซลส์มือสอง
			$arrCol['WhseSale'] = $WhseSaleTeam[1]; 
		}else{
			$arrCol['WhseSale'] = $WhseSale[1]; 
		}
	}else{
		$arrCol['WhseSale'] = '-'; 
	}
	$arrCol['TargetSkuQ'] = $Data['Q']; // ต้นทุนคลังสินค้าจอง
	$arrCol['TargetSkuF'] = $Data['F']; // ต้นทุนสินค้าต้องขาย
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
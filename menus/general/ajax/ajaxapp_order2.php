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
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if ($_GET['a']=='read'){
	if(isset($_GET['tab']) == "Y") {
		$Limit = " LIMIT 5";
	} else {
		$Limit = NULL;
	}
	/*
	$UserShow = "";

	$notShow = 0;
	 switch ($_SESSION['DeptCode']){
		 case 'DP002' :
			 $UserShow = "";
			 break;
		 case 'DP005' :
			 $UserShow = " AND T1.TeamCode LIKE  'TT2%' ";
			 break;
		 case 'DP006' :
			 $UserShow = " AND (T1.TeamCode LIKE  'MT1%' OR T1.TeamCode LIKE  'EXP%') ";
			 break;
		 case 'DP007' :
			 $UserShow = " AND T1.TeamCode LIKE  'MT2%' ";
			 break;
		 case 'DP008' :
			 $UserShow = " AND (T1.TeamCode LIKE  'TT1%' OR T1.TeamCode LIKE 'OUL') ";
			 break;
		 case 'DP003' :
			switch ($_SESSION['LvCode']){
				case 'LV010' :
				case 'LV011' :
				case 'LV012' :
					$UserShow = " AND (T2.UkeyReq = '4b23a9096e0cbb9714875032094bb466' OR T1.MainTeam = 'ONL')";
					$notShow = 1;
					break;
				default :
					$UserShow = " AND T1.MainTeam = 'ONL' ";
					break;
			}
	 	 break;
	
		default :
			switch ($_SESSION['LvCode']){
				case 'LV005' :
					$UserShow = " AND T2.UkeyReq = '9b4a0b12f93d49f7987726999df3b0a2' ";
					$notShow = 1;
					break;
				case 'LV003' :
					$UserShow = " AND T2.UkeyReq = '189eb6fd650338d13092fb694d337b61' ";
					$notShow = 1;
					break;
				
				case 'LV057' :
					$UserShow = " AND T2.UkeyReq = '8811b4c2a29275b3b74e209c34bbb5f8' ";
					$notShow = 1;
					break;
				default :
					$UserShow = " AND T1.TeamCode = 'WAIWAI' ";
				break;
			}
			break;
	 }
	 //CASE WHEN (SELECT COUNT(W1.ResultApp) FROM apporder W1 WHERE W1.DocEntry = T0.DocEntry AND (W1.UkeyReq = '".$_SESSION['ukey']."' OR W1.UkeyReq = '".$_SESSION['LvCode']."') AND ResultApp = '0' LIMIT 1 ) > 1 THEN 1 ELSE 0 END AS ChkApp
	$sql1 = "SELECT T0.DocEntry,T0.DocDate,T0.DocDueDate,CONCAT(T0.DocType,'V-',T0.DocNum) AS DocNum,T0.CardCode,T0.CardName,T0.DocTotal,T0.SlpCode,T1.SlpName,T1.MainTeam,
					T2.LvApp,T2.StepApprove AS StepApp,T2.AppSO,T2.AppCR,T2.AppGP,T2.UkeyReq,CONCAT(T3.uName,' ',T3.uLastName,' (',T3.uNickName,')') AS uName,
					T2.TypeApp,T2.ConditionOption AS ConOpt,
					CASE WHEN (SELECT Count(X0.ID) FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.AppSO != 0) THEN '1' ELSE '0' END AS 'ChkSO',
					CASE WHEN (SELECT Count(X0.ID) FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.AppCR != 0) THEN '1' ELSE '0' END AS 'ChkCR',
					CASE WHEN (SELECT Count(X0.ID) FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.AppGP != 0) THEN '1' ELSE '0' END AS 'ChkGP'
			 FROM order_header T0
			  	  LEFT JOIN oslp T1 ON T0.slpCode = T1.SlpCode
				  LEFT JOIN apporder T2 ON T0.DocEntry = T2.DocEntry
				  LEFT JOIN users T3 ON T2.UkeyReq = T3.uKey
				  LEFT JOIN positions T4 ON T3.LvCode = T4.LvCode
				  LEFT JOIN users T5 ON T0.CreateUkey = T5.uKey
				  LEFT JOIN positions T6 ON T5.LvCode = T6.LvCode
			 WHERE T0.DraftStatus = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P' AND T0.CANCELED = 'N' AND T2.ResultApp ='0' ".$UserShow." OR (T0.SlpCode IN (251, 291, 296) AND T6.DeptCode = '".$_SESSION['DeptCode']."')
			 ORDER BY T0.DocEntry DESC,T2.LvApp,T2.StepApprove,T3.LvCode DESC $Limit";
	*/

	$DeptCode = $_SESSION['DeptCode'];
	$LvCode   = $_SESSION['LvCode'];
	$UserShow = NULL;
	$TeamShow = NULL;

	switch($LvCode) {
		case "DP002":
			$UserShow = NULL;
		break;
		case "LV036":
		//case "LV052":
			$UserShow = " AND T2.UkeyReq = '$LvCode' ";
		break;
		default:
			$UserShow = " AND T2.UkeyReq = '".$_SESSION['ukey']."' ";
		break;
	}

	switch($DeptCode) {
		case "DP003":
			switch($LvCode) {
				case "LV104":
				case "LV105":
				case "LV106":
					$TeamShow = " AND (T1.TeamCode LIKE 'ONL%') ";
				break;
				default:
					$TeamShow = NULL;
			}
		break;
		case "DP005": $TeamShow = " AND (T1.TeamCode LIKE 'TT2%') "; break;
		case "DP006": $TeamShow = " AND (T1.TeamCode LIKE 'MT1%' OR T1.TeamCode LIKE 'EXP%') "; break;
		case "DP007": $TeamShow = " AND (T1.TeamCode LIKE 'MT2%') ";break;
		case "DP008": $TeamShow = " AND (T1.TeamCode LIKE 'TT1%' OR T1.TeamCode LIKE 'OUL%') "; break;
		default: $TeamShow = NULL; break;
	}

	$SQLWhr = $UserShow.$TeamShow;

	$sql1 = 
		"SELECT B0.* FROM (
			SELECT A0.*,
				CASE
					WHEN
						(A0.Prev_TypeApp IN ('A','B','C','D','E') AND A0.Prev_ResultApp IN ('Y','N')) OR
						(A0.Prev_ID = 0)
					THEN 'A'
				ELSE 'I' END AS 'Shown' 
				FROM (
					SELECT
						T2.ID, T0.DocEntry, T0.DocDate, T0.DocDueDate, CONCAT(T0.DocType,'V-',T0.DocNum) AS DocNum, T0.CardCode, T0.CardName, T0.DocTotal, T0.SlpCode, T1.SlpName, T1.MainTeam,
						T2.LvApp, T2.StepApprove AS 'StepApp', T2.AppSO, T2.AppCR, T2.AppGP, T2.TypeApp, T2.ConditionOption AS 'ConOpt', T2.ResultApp,
						CASE WHEN (SELECT Count(X0.ID) FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.AppSO != 0) THEN '1' ELSE '0' END AS 'ChkSO',
						CASE WHEN (SELECT Count(X0.ID) FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.AppCR != 0) THEN '1' ELSE '0' END AS 'ChkCR',
						CASE WHEN (SELECT Count(X0.ID) FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.AppGP != 0) THEN '1' ELSE '0' END AS 'ChkGP',
						/* PREV ROW DATA */
						IFNULL((SELECT X0.ID FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.StepApprove < T2.StepApprove ORDER BY X0.StepApprove DESC LIMIT 1),0) AS 'Prev_ID',
						CASE
							WHEN IFNULL((SELECT X0.ID FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.StepApprove < T2.StepApprove ORDER BY X0.StepApprove DESC LIMIT 1),0) != 0
							THEN (SELECT X0.TypeApp FROM apporder X0 WHERE X0.ID = IFNULL((SELECT X0.ID FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.StepApprove < T2.StepApprove ORDER BY X0.StepApprove DESC LIMIT 1),0))
						ELSE NULL END AS 'Prev_TypeApp',
						CASE
							WHEN IFNULL((SELECT X0.ID FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.StepApprove < T2.StepApprove ORDER BY X0.StepApprove DESC LIMIT 1),0) != 0
							THEN (SELECT X0.ConditionOption FROM apporder X0 WHERE X0.ID = IFNULL((SELECT X0.ID FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.StepApprove < T2.StepApprove ORDER BY X0.StepApprove DESC LIMIT 1),0))
						ELSE NULL END AS 'Prev_CondOpt',
						CASE
							WHEN IFNULL((SELECT X0.ID FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.StepApprove < T2.StepApprove ORDER BY X0.StepApprove DESC LIMIT 1),0) != 0
							THEN (SELECT X0.ResultApp FROM apporder X0 WHERE X0.ID = IFNULL((SELECT X0.ID FROM apporder X0 WHERE X0.DocEntry = T0.DocEntry AND X0.StepApprove < T2.StepApprove ORDER BY X0.StepApprove DESC LIMIT 1),0))
						ELSE NULL END AS 'Prev_ResultApp'
					FROM order_header T0
					LEFT JOIN oslp T1 ON T0.slpCode = T1.SlpCode
					LEFT JOIN apporder T2 ON T0.DocEntry = T2.DocEntry
					LEFT JOIN users T3 ON T2.UkeyReq = T3.uKey
					LEFT JOIN positions T4 ON T3.LvCode = T4.LvCode
					LEFT JOIN users T5 ON T0.CreateUkey = T5.uKey
					LEFT JOIN positions T6 ON T5.LvCode = T6.LvCode
					WHERE
						(T0.DraftStatus = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P' AND T0.CANCELED = 'N' AND T2.ResultApp ='0' $SQLWhr) OR 
						(T0.DraftStatus = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P' AND T0.CANCELED = 'N' AND (T0.SlpCode IN (251, 291, 296) OR T6.DeptCode = '$DeptCode') AND T2.ResultApp ='0' $SQLWhr)
				) A0
		) B0
		WHERE B0.Shown = 'A'
		ORDER BY B0.DocEntry DESC $Limit";

	//  echo $sql1;	
	$Rows = CHKRowDB($sql1);
	if($Rows > 0) {
		$getList = MySQLSelectX($sql1);
		$DocEntry=$ax=0;
		
		while ($ShowData = mysqli_fetch_array($getList)){
			/*
			if ($notShow == 1){
				if ($ShowData['ChkApp'] == 1){
					$show = "N";
				}else{
					$show = 'Y';
				}
			}else{
				$show = 'Y';
			}
			*/
			if ($DocEntry != $ShowData['DocEntry'] ){
				$ax++;
				$DrafList['DocEntry'][$ax] = $ShowData['DocEntry'];
				$DrafList['DocDate'][$DrafList['DocEntry'][$ax]] = $ShowData['DocDate'];
				$DrafList['DocDueDate'][$DrafList['DocEntry'][$ax]] = $ShowData['DocDueDate'];
				$DrafList['DocNum'][$DrafList['DocEntry'][$ax]] = $ShowData['DocNum'];
				$DrafList['CardCode'][$DrafList['DocEntry'][$ax]] = $ShowData['CardCode'];
				$DrafList['CardName'][$DrafList['DocEntry'][$ax]] = $ShowData['CardName'];
				$DrafList['DocTotal'][$DrafList['DocEntry'][$ax]] = $ShowData['DocTotal'];
				$DrafList['SlpName'][$DrafList['DocEntry'][$ax]] = $ShowData['SlpName'];
				$DrafList['Status'][$DrafList['DocEntry'][$ax]]  = $ShowData['ChkSO'].$ShowData['ChkGP'].$ShowData['ChkCR'];
				$DocEntry = $ShowData['DocEntry'];
			}
			
		
		} 
		for ($i=1;$i<=$ax;$i++){
			switch ($DrafList['Status'][$DrafList['DocEntry'][$i]] ) {
				case '100':
					$StatusDoc = "อนุมัติ SO";
					break;
				case '010':
					$StatusDoc = "อนุมัติ GP";
					break;
				case '001':
					$StatusDoc = "อนุมัติวงเงิน";
					break;
				case '011':
					$StatusDoc = "อนุมัติ GP/อนุมัติวงเงิน";
					break;
				case '101':
					$StatusDoc = "อนุมัติ SO/อนุมัติวงเงิน";
					break;
				case '110':
					$StatusDoc = "อนุมัติ SO/อนุมัติ GP";
					break;
				case '111':
					$StatusDoc = "อนุมัติ SO/อนุมัติ GP/อนุมัติวงเงิน";
					break;
			}
			$output.=  "<tr>
							<td class='text-center'>".date("d/m/Y",strtotime($DrafList['DocDate'][$DrafList['DocEntry'][$i]]))."</td>
							<td class='text-center'>".date("d/m/Y",strtotime($DrafList['DocDueDate'][$DrafList['DocEntry'][$i]]))."</td>
							<td class='text-center'><a href='javascript:void(0)' onclick=\"SOAppOrder('".$DrafList['Status'][$DrafList['DocEntry'][$i]]."',".$DrafList['DocEntry'][$i].")\">".$DrafList['DocNum'][$DrafList['DocEntry'][$i]]."</a></td>
							<td class='text-left'>".$DrafList['CardCode'][$DrafList['DocEntry'][$i]]." ".$DrafList['CardName'][$DrafList['DocEntry'][$i]]."</td>
							<td class='text-right'>".chk0($DrafList['DocTotal'][$DrafList['DocEntry'][$i]],2)."</td>
							<td class='text-left'>".$DrafList['SlpName'][$DrafList['DocEntry'][$i]]."</td>
							<td>".$StatusDoc."</td>
						</tr>";
		}
	} else {
		$output .= "<tr><td class='text-center' colspan='7'>ไม่มีเอกสารรอนุมัติ :)</td></tr>";
	}

}

if ($_GET['a'] == 'approv'){
	$dataApp = 0;
	$sql1 = "SELECT  T0.DocEntry,CONCAT(T0.DocType,'V-',T0.DocNum) AS DocNum,T0.CardCode,T0.CardName,T0.U_PONo,T0.SlpCode,T1.SlpName,T0.Comments,T0.DocDate,
					 T2.FirstBillDate,T2.Paid22,T2.BillCount,T2.PaidAll
			 FROM order_header T0
		 		  LEFT JOIN oslp T1 ON T0.SlpCode = T1.SlpCode 
				  LEFT JOIN ocrd T2 ON T0.CardCode = T2.CardCode 
			 WHERE T0.DocEntry = ".$_POST['DocEntry'];
			 //echo $sql1;
	$DataCus = MySQLSelect($sql1);	

	$CardCode = GroupCard($DataCus['CardCode']);
	/*
	$sql1 = "SELECT TOP 1 DocDate FROM OINV WHERE CardCode = '".$DataCus['CardCode']."' AND CANCELED = 'N' AND DocTotal > 0 ORDER BY DocDate";
	$getBill = conSAP8($sql1);
	$FristBill = odbc_fetch_array($getBill);
	*/
	$sql1 = "SELECT SUM(X0.DocTotal) AS DocTotal 
			 FROM (SELECT SUM(T0.PaidToDate) AS DocTotal 
				   FROM OINV T0
				   WHERE T0.CANCELED = 'N' AND T0.CardCode IN ".$CardCode." 
				   UNION ALL
				   SELECT -1*(SUM(T0.PaidToDate)) AS DocTotal 
				   FROM ORIN T0
				   WHERE T0.CANCELED = 'N' AND T0.CardCode IN ".$CardCode." 
				   ) X0 ";
	//echo $sql1;

	$getAllPay = SAPSelect($sql1);
	$AllPay = odbc_fetch_array($getAllPay);

	$sql1 = "SELECT COUNT(DocEntry) AS CBill FROM OINV WHERE CANCELED = 'N' AND DocTotal > 0 AND CardCode IN ".$CardCode;
	$getCountBill =	SAPSelect($sql1);
	$CountBill = odbc_fetch_array($getCountBill);

	$sql1 = "SELECT T1.Descr AS BillAddTxt,T2.Descr AS SaveTxt,T3.PymntGroup AS TextCR
			 FROM OCRD T0
			 	  LEFT JOIN UFD1 T1 ON T1.TableID = 'OCRD' AND T1.FieldID = 25 AND T1.FldValue = T0.U_BillAdd
                  LEFT JOIN UFD1 T2 ON T2.TableID = 'OCRD' AND T2.FieldID = 26 AND T2.FldValue = T0.U_SaveMoney
				  LEFT JOIN OCTG T3 ON T0.[GroupNum] = T3.[GroupNum]
			 	  
			 WHERE T0.CardCode IN ".$CardCode;
	//echo $sql1;
	$getSAPCus =	SAPSelect($sql1);
	$DataSAP = odbc_fetch_array($getSAPCus);



	$UserShow = "";
	$sql1 = "SELECT T2.ID,T1.MainTeam,T2.LvApp,T2.StepApprove AS StepApp,T2.AppSO,T2.AppCR,T2.AppGP,T2.UkeyReq,
	         CASE WHEN LENGTH(T2.UkeyReq) <= 4 THEN (SELECT X0.PositionName FROM positions X0 WHERE X0.uClass = T2.UkeyReq LIMIT 1)
			      WHEN SUBSTRING(T2.UkeyReq,1,2) = 'LV' THEN (SELECT X1.PositionName FROM positions X1 WHERE X1.LvCode = T2.UkeyReq LIMIT 1)
				  ELSE (SELECT CONCAT(X2.uName,' ',X2.uLastName,' (',X2.uNickName,')') FROM users X2 WHERE X2.uKey = T2.UkeyReq LIMIT 1) END AS uName,
					T2.TypeApp,T2.ConditionOption AS ConOpt,T2.ResultApp,T2.Remark 
			 FROM order_header T0
				  LEFT JOIN oslp T1 ON T0.slpCode = T1.SlpCode
			  	  LEFT JOIN apporder T2 ON T0.DocEntry = T2.DocEntry
				  LEFT JOIN users T3 ON T3.uKey = T2.UkeyReq	
				  LEFT JOIN positions T4 ON T3.LvCode = T4.LvCode
 			 WHERE T0.DraftStatus = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P' AND T0.CANCELED = 'N'    ".$UserShow." AND T0.DocEntry = '".$_POST['DocEntry']."'
			 ORDER BY T2.StepApprove,T2.LvApp";
	//echo $sql1;
	$getStepApp = MySQLSelectX($sql1);
	$i=0;
	$IDApp = 0;
	while ($rowApp = mysqli_fetch_array($getStepApp)) {
		$i++;
		$ListApp = $rowApp['AppSO'].$rowApp['AppGP'].$rowApp['AppCR'];
		switch ($ListApp) {
			case '100':
			case 'Y00':
			case 'N00':
				$StatusDoc = "<span class='text-success'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติ SO</span>";
				break;
			case '010':
				$StatusDoc = "<span class='text-warning'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติ GP</span>";
				break;
			case '001':
				$StatusDoc = "<span style='color: #0d6efd;'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติวงเงิน";
				break;
			case '011':
				$StatusDoc = "<span class='text-warning'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติ GP</span><br><span style='color: #0d6efd;'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติวงเงิน</span>";
				break;
			case '101':
				$StatusDoc = "<span class='text-success'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติ SO</span><br><span style='color: #0d6efd;'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติวงเงิน</span>";
				break;
			case '110':
				$StatusDoc = "<span class='text-success'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติ SO</span><br><span class='text-warning'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติ GP</span>";
				break;
			case '111':
				$StatusDoc = "<span class='text-success'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติ SO</span><br><span class='text-warning'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติ GP</span><br><span style='color: #0d6efd;'><i class='fas fa-file-signature fa-fw fa-1x'></i> อนุมัติวงเงิน</span>";
				break;
		}
		/*
		$ShowData['ChkSO'].$ShowData['ChkCR'].$ShowData['ChkGP'];
		case '100':
			$StatusDoc = "อนุมัติ SO";
			break;
		case '010':
			$StatusDoc = "อนุมัติ GP";
			break;
		case '001':
			$StatusDoc = "อนุมัติวงเงิน";
			break;
		case '011':
			$StatusDoc = "อนุมัติ GP/อนุมัติวงเงิน";
			break;
		case '101':
			$StatusDoc = "อนุมัติ SO/อนุมัติวงเงิน";
			break;
		case '110':
			$StatusDoc = "อนุมัติ SO/อนุมัติ GP";
			break;
		case '111':
			$StatusDoc = "อนุมัติ SO/อนุมัติ GP/อนุมัติวงเงิน";
			break;
		**/
		if (substr($rowApp['UkeyReq'],0,2) == 'LV'){
			if ($rowApp['UkeyReq'] == $_SESSION['LvCode']){
				$userApp = 'Y';
			}else{
				$userApp = 'N';
			}
		}else{
			if ($rowApp['UkeyReq'] == $_SESSION['ukey']){
				$userApp = 'Y';
			}else{
				$userApp = 'N';
			}
		}
		
		if ($userApp == 'Y' AND $rowApp['ResultApp'] == '0'){
			$permitApp = " ";
			$dataApp = 1;
			$IDApp = $rowApp['ID'];
		}else{
			$permitApp = " disabled ";
		}
		switch ($rowApp['ResultApp']){
			case 'Y' :
				$se0 = "  ";
				$seY = " selected ";
				$seN = "  ";
			break;
			case 'N' :
				$se0 = "  ";
				$seY = "  ";
				$seN = " selected ";
			break;
			default :
				$se0 = " selected ";
				$seY = "  ";
				$seN = "  ";
			break;
		}

		if($_POST['Mobile'] == "true") {
			switch ($rowApp['ResultApp']){
				case 'Y':
					$bg = "#dff0d8";
					$icon = "<span class='text-success'><i class='far fa-check-circle fa-fw fa-1x'></i> อนุมัติ</span>";
				break;
				case 'N':
					$bg = "#f2dede";
					$icon = "<span class='text-danger'><i class='far fa-times-circle fa-fw fa-1x'></i> ไม่อนุมัติ</span>";
				break;
				default:
					if($_SESSION['ukey'] == $rowApp['UkeyReq']) {
						$bg = "#d9edf7";
					} else {
						$bg = "#fff";
					}
					
					$icon = "<span><i class='far fa-clock fa-fw fa-1x'></i> รอพิจารณา</span>";
				break;
			}
			$output .=
			"<tr>
				<td class='pb-0'>
					<div class='ps-2 pe-2 pt-2 border border-1' style='border-radius: 10px 10px 0px 0px; box-shadow: 1px 1px $bg; background-color: $bg;'>
						<div class='d-flex w-100'>
							<div style='width: 70%'>
								<i class='fas fa-user fa-fw fa-1x'></i> ".$rowApp['uName']."
							</div>
							<div style='width: 30%'> $icon </div>
						</div>
					</div>
					<div class='p-2 border border-1' style='border-radius: 0px 0px 10px 10px; box-shadow: 1px 1px $bg;'>";
					if($_SESSION['ukey'] != $rowApp['UkeyReq']) {
			$output .= "<div class='d-flex'>
							<div style='width: 100%'>
								<span class='fw-bolder'>การดำเนินการ</span>
								<span>".str_replace("<br>"," | ",$StatusDoc)."</span>
							</div>
						</div>
						<div class='d-flex mt-2'>
							<div style='width: 100%'>
								<span class='fw-bolder'>เหตุผลการพิจารณา<br/></span>
								<span>".$rowApp['Remark']."</span>
							</div>
						</div>";
					} else {
			$output .= "<div class='d-flex'>
							<div style='width: 100%'>
								<span class='fw-bolder'>การดำเนินการ</span>
								<span>".str_replace("<br>"," | ",$StatusDoc)."</span>
							</div>
						</div>
						<div class='d-flex mt-2'>
							<div style='width: 100%'>
								<span class='fw-bolder' for='Remark_".$rowApp['ID']."'>เหตุผลการพิจารณา</span></br>
								<textarea id='Remark_".$rowApp['ID']."' name='Remark_".$rowApp['ID']."' rows='2' class='pt-2 form-control' ".$permitApp.">".$rowApp['Remark']."</textarea>
							</div>
						</div>
						<div class='d-flex mt-2'>
							<div style='width: 100%'>
								<span class='fw-bolder' for='App_".$rowApp['ID']."'>ผลการพิจารณา</span></br>
								<select class='form-select' id='App_".$rowApp['ID']."' name='App_".$rowApp['ID']."' ".$permitApp." >
									<option value='0' ".$se0.">รอพิจารณา</option>
									<option value='Y' ".$seY.">อนุมัติ</option>
									<option value='N' ".$seN.">ไม่อนุมัติ</option>
								</select>
							</div>
						</div>";

					}
		$output .= "</div>
				</td>
			</tr>";

		} else {
			$output .= 
			"<tr>
				<td class='text-center'>".$i."</td>
				<td>".$rowApp['uName']."</td>
				<td class='text-left'>".$StatusDoc."</td>
				<td class='text-left'><textarea id='Remark_".$rowApp['ID']."' name='Remark_".$rowApp['ID']."' rows='2' class='form-control' ".$permitApp.">".$rowApp['Remark']."</textarea></td>
				<td class='text-center'>
					<select class='form-select' id='App_".$rowApp['ID']."' name='App_".$rowApp['ID']."' ".$permitApp." >
						<option value='0' ".$se0.">รอพิจารณา</option>
						<option value='Y' ".$seY.">อนุมัติ</option>
						<option value='N' ".$seN.">ไม่อนุมัติ</option>
					</select>
				</td>
			</tr>";
		}

		
	}

	switch ($_POST['ChkApp']) {
		case '100':
			$StatusDoc = "อนุมัติ SO";
			break;
		case '010':
			$StatusDoc = "อนุมัติ GP";
			break;
		case '001':
			$StatusDoc = "อนุมัติวงเงิน";
			break;
		case '011':
			$StatusDoc = "อนุมัติ GP/อนุมัติวงเงิน";
			break;
		case '101':
			$StatusDoc = "อนุมัติ SO/อนุมัติวงเงิน";
			break;
		case '110':
			$StatusDoc = "อนุมัติ SO/อนุมัติ GP";
			break;
		case '111':
			$StatusDoc = "อนุมัติ SO/อนุมัติ GP/อนุมัติวงเงิน";
			break;
	}

	$arrCol['StatusApp'] = $StatusDoc;
	$arrCol['CardCode'] = $DataCus['CardCode'];
	$arrCol['CardName'] = $DataCus['CardCode']." ".$DataCus['CardName'] ;
	$arrCol['DocDate'] = $DataCus['DocDate'];
	$arrCol['SlpName'] = $DataCus['SlpName'];
	$arrCol['DocNum'] = $DataCus['DocNum'];

	//1.Descr AS BillAddTxt,T2.Descr AS SaveTxt,T3.PymntGroup AS TextCR
	$arrCol['TextCR'] = conutf8($DataSAP['TextCR']);
	$arrCol['TextBill'] = conutf8($DataSAP['BillAddTxt']);
	$arrCol['SaveTxt'] = conutf8($DataSAP['SaveTxt']);

	$arrCol['OrderRemark'] = $DataCus['Comments'];
	$arrCol['FristBill'] = FullDate($DataCus['FirstBillDate']);
	$arrCol['TotalPay'] = chk0($AllPay['DocTotal']+$DataCus['Paid22'],2);
	$arrCol['CountBill'] = $CountBill['CBill']+$DataCus['BillCount'];
	$arrCol['AppPermit'] = $dataApp;
	$arrCol['IDApp'] = $IDApp;
	/*
	
	
	
	$arrCol[''] = $DataCus[''];
	$arrCol[''] = $DataCus[''];
	$arrCol[''] = $DataCus[''];
	$arrCol[''] = $DataCus[''];
	$arrCol[''] = $DataCus[''];
	$arrCol[''] = $DataCus[''];
	$arrCol[''] = $DataCus[''];
	$arrCol[''] = $DataCus[''];
	$arrCol[''] = $DataCus[''];
	*/
}


if ($_GET['a'] == 'crtab'){
	$cYear = date("Y");
	$pYear = $cYear-1;
	$CardCode = $_POST['CardCode'];
	$DocNum = $_POST['DocNum'];
	$DocEntry = $_POST['DocEntry'];
	 
	// $AllCard = substr($AllCard,0,($AllCard-2)).")"; => ('C-12442')
	$sqlFa = "SELECT T0.CardCode,T0.FatherCard
          FROM OCRD T0
          WHERE   T0.FatherCard = '".$CardCode."' OR T0.CardCode = '".$CardCode."'
          ORDER BY T0.CardCode DESC";
	$sqlFaQRY = SAPSelect($sqlFa);
	$MainCard = $CardCode;
	while($resultFa = odbc_fetch_array($sqlFaQRY)) {
		if ($resultFa['FatherCard'] != null){
			$MainCard = $resultFa['FatherCard']; 
		}
	}
	$sqlAllc = "SELECT T0.CardCode,T0.CardName,T0.CreditLine
				FROM OCRD T0
				WHERE (T0.CardCode = '".$CardCode."' OR T0.CardCode = '".$MainCard."') OR T0.FatherCard = '".$MainCard."' ORDER BY T0.CardCode";
	$sqlAllcQRY = SAPSelect($sqlAllc);
	$AllCard = "('";
	$MainCR = 0;
	while($resultAllc = odbc_fetch_array($sqlAllcQRY)) {
		$AllCard .= $resultAllc['CardCode']."','";
		if ($resultAllc['CardCode'] == $MainCard){
			$MainCR = $resultAllc['CreditLine'];
		}
		if ($CardCode == $MainCard){
			if ($CardCode != $resultAllc['CardCode']){
				$CardCode2 = $resultAllc['CardCode'];
				$CardName2 = conutf8($resultAllc['CardName']);
			}else{
				$CardCode2 ="";
				$CardName2 = "";
			}
		}else{
			if ($MainCard == $resultAllc['CardCode']){
				$CardCode2 = $resultAllc['CardCode'];
				$CardName2 = conutf8($resultAllc['CardName']);
			}
		}
	}
	$AllCard = substr($AllCard,0,-2).")";

	$arrCol['AllCard'] = $AllCard;

	// บิลที่ยังไม่เรียกเก็บ / ใบยืมสินค้าที่ยังไม่คืน / ใบสั่งขายที่ยังไม่ส่งสินค้า
	$sqlPAIV = "SELECT  SUM(W1.IVxDue) AS IVxDue,
						SUM(W1.IVDue) AS IVDue,
						SUM(W1.IVxDue90) AS IVxDue90,
						SUM(W1.IVDue90) AS IVDue90,
						SUM(W1.IVxDue60) AS IVxDue60,
						SUM(W1.IVDue60) AS IVDue60,
						SUM(W1.IVxDue30) AS IVxDue30,
						SUM(W1.IVDue30) AS IVDue30,
						SUM(W1.IVxDue0) AS IVxDue0,
						SUM(W1.IVDue0) AS IVDue0,
						SUM(W1.IVxDue+W1.IVxDue90+W1.IVxDue60+W1.IVxDue30+W1.IVxDue0) AS xAllIV,
						SUM(W1.IVDue+W1.IVDue90+W1.IVDue60+W1.IVDue30+W1.IVDue0) AS AllIV,
						SUM(W1.PAxDue) AS PAxDue,
						SUM(W1.PADue) AS PADue,
						SUM(W1.PAxDue90) AS PAxDue90,
						SUM(W1.PADue90) AS PADue90,
						SUM(W1.PAxDue60) AS PAxDue60,
						SUM(W1.PADue60) AS PADue60,
						SUM(W1.PAxDue30) AS PAxDue30,
						SUM(W1.PADue30) AS PADue30,
						SUM(W1.PAxDue0) AS PAxDue0,
						SUM(W1.PADue0) AS PADue0,
						SUM(W1.PAxDue+W1.PAxDue90+W1.PAxDue60+W1.PAxDue30+W1.PAxDue0) AS xAllPA,
						SUM(W1.PADue+W1.PADue90+W1.PADue60+W1.PADue30+W1.PADue0) AS AllPA
				FROM (
					SELECT 0 AS IVxDue,0 AS IVDue,0 AS IVxDue90,0 AS IVDue90,0 AS IVxDue60,0 AS IVDue60,0 AS IVxDue30,0 AS IVDue30,0 AS IVxDue0,0 AS IVDue0, 
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) >= 0 THEN 1 ELSE 0 END AS PAxDue,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) >= 0 THEN P1.Balance ELSE 0 END AS PADue,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) < -90 THEN 1 ELSE 0 END AS PAxDue90,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) < -90 THEN P1.Balance ELSE 0 END AS PADue90,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) < -60 AND DATEDIFF(DAY,GETDATE(),P1.DocDueDate) > -90 THEN 1 ELSE 0 END AS PAxDue60,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) < -60 AND DATEDIFF(DAY,GETDATE(),P1.DocDueDate) > -90 THEN P1.Balance ELSE 0 END AS PADue60,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) < -30 AND DATEDIFF(DAY,GETDATE(),P1.DocDueDate) > -60 THEN 1 ELSE 0 END AS PAxDue30,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) < -30 AND DATEDIFF(DAY,GETDATE(),P1.DocDueDate) > -60 THEN P1.Balance ELSE 0 END AS PADue30,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) < 0   AND DATEDIFF(DAY,GETDATE(),P1.DocDueDate) > -30 THEN 1 ELSE 0 END AS PAxDue0,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P1.DocDueDate) < 0   AND DATEDIFF(DAY,GETDATE(),P1.DocDueDate) > -30 THEN P1.Balance ELSE 0 END AS PADue0
					FROM ( 
							SELECT T0.[NumAtCard], T0.[DocDate], T0.CardCode,T0.CardName, T0.[DocDueDate], T0.[DocTotal],(T0.[DocTotal] - T0.[PaidToDate]) AS Balance, T0.[DocNum], T1.[Beginstr],DATEDIFF(DAY,GETDATE(),T0.DocDueDate) AS Diff 
							FROM ODLN T0 
									INNER JOIN NNM1 T1 ON T0.Series = T1.Series 
							WHERE T0.[CardCode] IN ".$AllCard." AND T0.[DocStatus] = 'O' AND T1.[SeriesName] LIKE 'PA%'
							) P1
					UNION ALL
					SELECT CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) >= 0 THEN 1 ELSE 0 END AS IVxDue,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) >= 0 THEN P2.Balance ELSE 0 END AS IVDue,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) < -90 THEN 1 ELSE 0 END AS IVxDue90,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) < -90 THEN P2.Balance ELSE 0 END AS IVDue90,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) < -60 AND DATEDIFF(DAY,GETDATE(),P2.DocDueDate) >= -90 THEN 1 ELSE 0 END AS IVxDue60,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) < -60 AND DATEDIFF(DAY,GETDATE(),P2.DocDueDate) >= -90 THEN P2.Balance ELSE 0 END AS IVDue60,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) < -30 AND DATEDIFF(DAY,GETDATE(),P2.DocDueDate) >= -60 THEN 1 ELSE 0 END AS IVxDue30,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) < -30 AND DATEDIFF(DAY,GETDATE(),P2.DocDueDate) >= -60 THEN P2.Balance ELSE 0 END AS IVDue30,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) < 0   AND DATEDIFF(DAY,GETDATE(),P2.DocDueDate) >= -30 THEN 1 ELSE 0 END AS IVxDue0,
							CASE WHEN DATEDIFF(DAY,GETDATE(),P2.DocDueDate) < 0   AND DATEDIFF(DAY,GETDATE(),P2.DocDueDate) >= -30 THEN P2.Balance ELSE 0 END AS IVDue0,
							0 AS PAxDue,0 AS PADue,0 AS PAxDue90,0 AS PADue90,0 AS PAxDue60,0 AS PADue60,0 AS PAxDue30,0 AS PADue30,0 AS PAxDue0,0 AS PADue0
						FROM ( 
							SELECT T0.[NumAtCard], T0.[DocDate], T0.CardCode,T0.CardName, T0.[DocDueDate], T0.[DocTotal],(T0.[DocTotal] - T0.[PaidToDate]) AS Balance, T0.[DocNum], T1.[Beginstr],DATEDIFF(DAY,GETDATE(),T0.DocDueDate) AS Diff 
							FROM OINV T0 
									LEFT JOIN NNM1 T1 ON T0.Series = T1.Series 
							WHERE T0.[CardCode] IN ".$AllCard." AND T0.[DocStatus] = 'O' 
							UNION ALL
							SELECT T0.[NumAtCard], T0.[DocDate], T0.CardCode,T0.CardName, T0.[DocDueDate], T0.[DocTotal],-1*((T0.[DocTotal] - T0.[PaidToDate])) AS Balance, T0.[DocNum], T1.[Beginstr],DATEDIFF(DAY,GETDATE(),T0.DocDueDate) AS Diff 
							FROM ORIN T0 
									LEFT JOIN NNM1 T1 ON T0.Series = T1.Series 
							WHERE T0.[CardCode] IN ".$AllCard." AND T0.[DocStatus] = 'O'
							) P2 ) W1";
	$sqlPAIVQRY = SAPSelect($sqlPAIV);	
	$resultPAIV = odbc_fetch_array($sqlPAIVQRY);
	
	// ADD DATA บิลที่ยังไม่เรียกเก็บ
	$T1 = 	"<tr>";
		$T1 .= "<td>ยังไม่เกินกำหนด</td>";
		if($resultPAIV['IVxDue'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVxDue'],0)."</td>"; }else{ $T1 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['IVDue'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVDue'],2)."</td>"; }else{ $T1 .= "<td class='text-right'>0.00</td>"; }
	$T1 .= "</tr>";
	$T1 .= "<tr>";
		$T1 .= "<td>น้อยกว่า 30 วัน</td>";
		if($resultPAIV['IVxDue0'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVxDue0'],0)."</td>"; }else{ $T1 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['IVDue0'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVDue0'],2)."</td>"; }else{ $T1 .= "<td class='text-right'>0.00</td>"; }
	$T1 .= "</tr>";
	$T1 .= "<tr>";
		$T1 .= "<td>31 - 60 วัน</td>";
		if($resultPAIV['IVxDue30'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVxDue30'],0)."</td>"; }else{ $T1 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['IVDue30'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVDue30'],2)."</td>"; }else{ $T1 .= "<td class='text-right'>0.00</td>"; }
	$T1 .= "</tr>";
	$T1 .= "<tr>";
		$T1 .= "<td>61 - 90 วัน</td>";
		if($resultPAIV['IVxDue60'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVxDue60'],0)."</td>"; }else{ $T1 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['IVDue60'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVDue60'],2)."</td>"; }else{ $T1 .= "<td class='text-right'>0.00</td>"; }
	$T1 .= "</tr>";
	$T1 .= "<tr>";
		$T1 .= "<td>91 วันขึ้นไป</td>";
		if($resultPAIV['IVxDue90'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVxDue90'],0)."</td>"; }else{ $T1 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['IVDue90'] > 0){ $T1 .= "<td class='text-right'>".number_format($resultPAIV['IVDue90'],2)."</td>"; }else{ $T1 .= "<td class='text-right'>0.00</td>"; }
	$T1 .= "</tr>";
	$T1 .= "<tr>";
		$T1 .= "<td class='text-primary'>รวมทั้งหมด</td>";
		if($resultPAIV['xAllIV'] > 0){ $T1 .= "<td class='text-primary text-right'>".number_format($resultPAIV['xAllIV'],0)."</td>"; }else{ $T1 .= "<td class='text-primary text-right'>0</td>"; }
		if($resultPAIV['AllIV'] > 0){ $T1 .= "<td class='text-primary text-right'>".number_format($resultPAIV['AllIV'],2)."</td>"; }else{ $T1 .= "<td class='text-primary text-right'>0.00</td>"; }
	$T1 .= "</tr>";
	$T1 .= "<tr>";
		$T1 .= "<td colspan='3' class='text-center'><a href='javascript:void(0)' onclick=\"CallModal(1)\">ดูรายละเอียดเพิ่มเติม</a></td>";
	$T1 .= "</tr>";

	$arrCol['CR_T1'] = $T1;
						
	// ADD DATA ใบยืมสินค้าที่ยังไม่คืน
	$T2 = "<tr>";
		$T2 .= "<td>ยังไม่เกินกำหนด</td>";
		if($resultPAIV['PAxDue'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PAxDue'],0)."</td>"; }else{ $T2 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['PADue'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PADue'],2)."</td>"; }else{ $T2 .= "<td class='text-right'>0.00</td>"; }
	$T2 .= "</tr>";
	$T2 .= "<tr>";
		$T2 .= "<td>น้อยกว่า 30 วัน</td>";
		if($resultPAIV['PAxDue0'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PAxDue0'],0)."</td>"; }else{ $T2 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['PADue0'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PADue0'],2)."</td>"; }else{ $T2 .= "<td class='text-right'>0.00</td>"; }
	$T2 .= "</tr>";
	$T2 .= "<tr>";
		$T2 .= "<td>31 - 60 วัน</td>";
		if($resultPAIV['PAxDue30'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PAxDue30'],0)."</td>"; }else{ $T2 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['PADue30'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PADue30'],2)."</td>"; }else{ $T2 .= "<td class='text-right'>0.00</td>"; }
	$T2 .= "</tr>";
	$T2 .= "<tr>";
		$T2 .= "<td>61 - 90 วัน</td>";
		if($resultPAIV['PAxDue60'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PAxDue60'],0)."</td>"; }else{ $T2 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['PADue60'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PADue60'],2)."</td>"; }else{ $T2 .= "<td class='text-right'>0.00</td>"; }
	$T2 .= "</tr>";
	$T2 .= "<tr>";
		$T2 .= "<td>91 วันขึ้นไป</td>";
		if($resultPAIV['PAxDue90'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PAxDue90'],0)."</td>"; }else{ $T2 .= "<td class='text-right'>0</td>"; }
		if($resultPAIV['PADue90'] > 0){ $T2 .= "<td class='text-right'>".number_format($resultPAIV['PADue90'],2)."</td>"; }else{ $T2 .= "<td class='text-right'>0.00</td>"; }
	$T2 .= "</tr>";
	$T2 .= "<tr>";
		$T2 .= "<td class='text-primary'>รวมทั้งหมด</td>";
		if($resultPAIV['xAllPA'] > 0){ $T2 .= "<td class='text-primary text-right'>".number_format($resultPAIV['xAllPA'],0)."</td>"; }else{ $T2 .= "<td class='text-primary text-right'>0</td>"; }
		if($resultPAIV['AllPA'] > 0){ $T2 .= "<td class='text-primary text-right'>".number_format($resultPAIV['AllPA'],2)."</td>"; }else{ $T2 .= "<td class='text-primary text-right'>0.00</td>"; }
	$T2 .= "</tr>";
	$T2 .= "<tr>";
		$T2 .= "<td colspan='3' class='text-center'><a href='javascript:void(0)' onclick=\"CallModal(2)\">ดูรายละเอียดเพิ่มเติม</a></td>";
	$T2 .= "</tr>";

	$arrCol['CR_T2'] = $T2;

	// ใบสั่งขายที่ยังไม่ส่งสินค้า
	$sqlDataCus = "SELECT SUM(X1.BackOrder) AS BackOrder,
							SUM(X1.xBackOrder) AS xBackOrder,
							SUM(X1.ReturnBill) AS ReturnBill,
							SUM(X1.xReturn) AS xReturn,
							SUM(X1.CHQ) AS CHQ,
							SUM(X1.xCHQ) AS xCHQ
					FROM (
							SELECT CASE WHEN P1.GroupData = 'BackOrder' THEN P1.DocTotal ELSE 0 END AS BackOrder,
									CASE WHEN P1.GroupData = 'BackOrder' THEN P1.cx ELSE 0 END AS xBackOrder,
									CASE WHEN P1.GroupData = 'ReturnBill' THEN P1.DocTotal ELSE 0 END AS ReturnBill,
									CASE WHEN P1.GroupData = 'ReturnBill' THEN P1.cx ELSE 0 END AS xReturn,
									CASE WHEN P1.GroupData = 'CHQ' THEN P1.DocTotal ELSE 0 END AS CHQ,
									CASE WHEN P1.GroupData = 'CHQ' THEN P1.cx ELSE 0 END AS xCHQ
							FROM (
								SELECT SUM(W1.cX) AS cx,SUM(W1.DocTotal) AS DocTotal,W1.GroupData
								FROM 
									(
										SELECT 1 AS cX, (T1.[OpenQty]*T1.[Price]) AS DocTotal, 'BackOrder' AS GroupData  
										FROM ORDR T0 
											INNER JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry 
										WHERE T0.[CardCode] IN  ".$AllCard." AND  T1.[LineStatus] = 'O'
										UNION ALL
										SELECT 1 AS cX,T0.DocTotal AS DocTotalm, 'ReturnBill' AS GroupData
										FROM ORIN T0
											LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
										WHERE T0.CardCode IN  ".$AllCard." AND (T1.BeginStr LIKE 'S1%' OR T1.BeginStr LIKE 'SR%') AND T0.DocStatus = 'C'
										UNION ALL
										SELECT  1 AS cX, T0.[CheckSum] AS DocTotal, 'CHQ' AS GroupData 
										FROM OCHH  T0 
										WHERE T0.[CardCode] IN  ".$AllCard." AND T0.[Deposited] = 'N' AND T0.[Canceled] = 'N' AND T0.[Converted] = 'N' 
									) W1
							GROUP BY W1.GroupData ) P1
						) X1";
	$sqlDataCusVQRY = SAPSelect($sqlDataCus);
	$resultDataCus = odbc_fetch_array($sqlDataCusVQRY);

	$sqlCHQ =  "SELECT SUM(W1.Cx) AS TotalCHQ,SUM(W1.CHQ_Amount) AS TotalAmount
				FROM (SELECT T0.CardCode,T0.Status,T0.DocNum,1 AS Cx,T0.CHQ_Amount FROM chq_return T0 WHERE T0.CardCode IN ".$AllCard." AND Status !=2) W1";
	$resultCHQ = MySQLSelect($sqlCHQ);

	$HeadTD1 = ["-", "จำนวนรายการ", "จำนวนบิลที่คืน", "จำนวนเช็ค", "จำนวนเช็คที่เด้ง"];
	$DataTD1 = [0, $resultDataCus['xBackOrder'], $resultDataCus['xReturn'], $resultDataCus['xCHQ'], $resultCHQ['TotalCHQ']];
	$FootTD1 = ["-", "รายการ", "ใบ", "ใบ", "ใบ"];
	$HeadTD2 = ["-", "มูลค่า", "มูลค่า", "มูลค่า", "มูลค่าเช็คคืนทั้งหมด"];
	$DataTD2 = [0, $resultDataCus['BackOrder'], $resultDataCus['ReturnBill'], $resultDataCus['CHQ'], $resultCHQ['TotalAmount']];
	$CallModal = 3;
	for($t = 1; $t <= 4; $t++){
		${"TB{$t}"} = "<tr>";
			${"TB{$t}"} .= "<td width='30%'>".$HeadTD1[$t]."</td>";
			if($t != 4){
				if($DataTD1[$t] > 0){ ${"TB{$t}"} .= "<td width='50%' class='text-right'>".number_format($DataTD1[$t],0)."</td>"; }else{ ${"TB{$t}"} .= "<td width='50%' class='text-right'>0</td>"; }
			}else{
				if($DataTD1[$t] > 0){ ${"TB{$t}"} .= "<td width='50%' class='text-right'>".number_format($DataTD1[$t],2)."</td>"; }else{ ${"TB{$t}"} .= "<td width='50%' class='text-right'>0.00</td>"; }
			}
			${"TB{$t}"} .= "<td width='20%'>".$FootTD1[$t]."</td>";
		${"TB{$t}"} .= "</tr>";
		${"TB{$t}"} .= "<tr>";
			${"TB{$t}"} .= "<td>".$HeadTD2[$t]."</td>";
			if($DataTD2[$t] > 0){ ${"TB{$t}"} .= "<td class='text-right'>".number_format($DataTD2[$t],2)."</td>"; }else{ ${"TB{$t}"} .= "<td class='text-right'>0.00</td>"; }
			${"TB{$t}"} .= "<td>บาท</td>";
		${"TB{$t}"} .= "</tr>";
		${"TB{$t}"} .= "<tr>";
			${"TB{$t}"} .= "<td colspan='3' class='text-center'><a href='javascript:void(0)' onclick=\"CallModal(".$CallModal.")\">ดูรายละเอียดเพิ่มเติม</a></td>";
		${"TB{$t}"} .= "</tr>";

		$CallModal++;

		$arrCol["TB{$t}"] = ${"TB{$t}"};
	}

	// หมายเหตุ
	// echo substr($AllCard,2,-2);
	$sqlNote = "SELECT T0.CardCode, T0.Free_Text FROM OCRD T0 WHERE (T0.CardCode = '".$CardCode."')";
	//echo $sqlNote;
	$sqlNoteQRY = SAPSelect($sqlNote);
	$resultNote = odbc_fetch_array($sqlNoteQRY);
	//echo conutf8($resultNote['Free_Text']);
	$TBNote = 	"<tr>
					<td><textarea rows='6' class='form-control' style='font-size: 13px;' disabled>".conutf8($resultNote['Free_Text'])."</textarea></td>
				</tr>";
	$arrCol["TBNote"] = $TBNote;
	$sqlLine = "SELECT NewOrder,OldCR,CRLimit,TypeCR
	            FROM crapp 
				WHERE DocEntry = ".$DocEntry;
	if (CHKRowDB($sqlLine) > 0) {
		$DataLine = MySQLSelect($sqlLine);
		switch ($DataLine['TypeCR']) {
			case 'A' : 
				$TxtCR = " <strong class='badge bg-warning'>เกินวงเงินไม่ถึง 1.5 เท่า</strong>";
				break;
			case  'B' : 
				$TxtCR = " <strong class='badge bg-danger'>เกินวงเงิน 1.5 เท่า แต่ไม่ถึง 200,000 บาท</strong>";
				break;
			case 'C'  :
				$TxtCR = " <strong class='badge bg-danger'>เกินวงเงิน 1.5 เท่าเกิน 200,000 บาท</strong>";
				break;
			case 'D' :
				$TxtCR = " <strong class='badge bg-danger'>มีบิลเกินดิว</strong>";
				break;
			case 'E' :
				$TxtCR = " <strong class='badge bg-danger'>ขายเงินสด มีบิลค้างจ่าย</strong>'";
				break;
		}
		$arrCol["L1"] = number_format($DataLine['NewOrder'],2);
		$arrCol["L2"] = number_format($DataLine['OldCR'],2);
		$arrCol["L3"] = number_format(($DataLine['NewOrder']+$DataLine['OldCR']),2);
		$arrCol["L4"] = number_format(($DataLine['NewOrder']+$DataLine['OldCR'])-$DataLine['CRLimit'],2);
		$arrCol['CRType'] = $TxtCR;
	}else{
		$arrCol["L1"] = "-";
		$arrCol["L2"] = "-";
		$arrCol["L3"] = "-";
		$arrCol["L4"] = "-";
		$arrCol['CRType'] = " <strong class='badge bg-success'>ไม่เกินวงเงิน</strong>";
	}

	

	// ---- ขอนุมัติวงเงินเฉพาะบิล ---- //
	// ...
	
}

if($_GET['a'] == 'CallModal') {
	$AllCard = $_POST['AllCard'];
	$TbModal = $_POST['TbModal'];
	if ($AllCard != ''){
		switch ($TbModal) {
			case 1: 
				$sql = "SELECT P1.*  
						FROM
						(
							SELECT T0.NumAtCard, T0.DocDate, T0.DocDueDate,T0.CardCode,T0.CardName,
									DATEDIFF(DAY,GETDATE(),T0.DocDueDate) AS Diff,
									T0.DocTotal, (T0.DocTotal - T0.PaidToDate) AS Balance, T0.DocNum, T1.Beginstr,0 AS runLn 
							FROM OINV T0 
									LEFT JOIN NNM1 T1 On T0.Series = T1.Series 
							WHERE T0.CardCode IN ".$AllCard." AND T0.DocStatus ='O' 
							UNION ALL
							SELECT T0.NumAtCard, T0.DocDate, T0.DocDueDate,T0.CardCode,T0.CardName,
									DATEDIFF(DAY,GETDATE(),T0.DocDueDate) AS Diff,
									-1*T0.Doctotal AS DocTotal, ((-1*T0.Doctotal) + T0.PaidToDate) AS Balance, T0.DocNum, T1.Beginstr,1 AS runln 
							FROM ORIN T0 
									LEFT JOIN NNM1 T1 On T0.Series = T1.Series 
							WHERE T0.CardCode IN ".$AllCard." AND T0.DocStatus ='O' AND (T1.SeriesName LIKE 'S1%' or T1.SeriesName Like 'SR%')
						) P1 ORDER BY runLn,Diff";
						//echo
				$sqlQRY = SAPSelect($sql);
				$Tbody ="<div class='table-responsive'>".
							"<table class='table table-bordered rounded rounded-3 overflow-hidden'>".
								"<thead style='background-color: rgba(155, 0, 0, 0.04); font-size: 13.5px;'>".
									"<tr>".
										"<th class='text-center'>เลขที่เอกสาร</th>".
										"<th class='text-center'>วันที่</th>".
										"<th class='text-center'>วันที่ครบกำหนด</th>".
										"<th class='text-center'>จำนวนวัน<br>เกินกำหนด</th>".
										"<th class='text-center'>ชื่อลูกค้า</th>".
										"<th class='text-center'>จำนวนเงิน</th>".
										"<th class='text-center'>จำนวนเงินคงเหลือ</th>".
									"</tr>".
								"</thead>".
								"<tbody style='font-size: 12px;'>";
				$Total = 0;		
				$CardCode = "";
				$CardName = "";		
				while($result = odbc_fetch_array($sqlQRY)) {
					$Total = $Total + $result['Balance'];
					if ($result['NumAtCard'] != '') {
						$DocNumShow = $result['NumAtCard'];
					}else{
						if ($result['Beginstr'] == ""){
							$DocNumShow = "IV-".$result['DocNum'];
						}else{
							$DocNumShow = $result['Beginstr'].$result['DocNum'];
						}
					}
					$Tbody .= 	"<tr>".
									"<td class='text-center fw-bold'>".$DocNumShow."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['DocDate']))."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['DocDueDate']))."</td>";
									if ($result['Diff'] <= 0) {
										$Tbody .= "<td class='text-center'>".number_format(-1*$result['Diff'],0)."</td>";
									}else{
										$Tbody .= "<td class='text-center'></td>";
									}
						$Tbody .=   "<td>".$result['CardCode']." ".conutf8($result['CardName'])."</td>".
									"<td class='text-right'>".number_format($result['DocTotal'],2)."</td>".
									"<td class='text-right'>".number_format($result['Balance'],2)."</td>".
								"</tr>";
					$CardCode = $result['CardCode'];
                	$CardName = conutf8($result['CardName']);
				}
				$Tbody .= 		"</tbody>".
								"<tfoot style='font-size: 13px; background-color: rgba(0, 0, 0, 0.04);'>".
									"<tr>".
										"<th colspan='6' class='text-right text-primary'>รวมมูลค่าบิลที่ยังไม่เรียกเก็บ</th>".
										"<th class='text-right text-primary'>".number_format($Total,2)."</th>".
									"</tr>".
								"</tfoot>".
							"</table>".
						"</div>";
				$HeadModal = "บิลที่ยังไม่เรียกเก็บของ ".$CardCode." ".$CardName;
				break;
			case 2: 
				$sql = "SELECT T0.[NumAtCard], T0.[DocDate], T0.CardCode,T0.CardName, T0.[DocDueDate], T0.[DocTotal],(T0.[DocTotal] - T0.[PaidToDate]) AS Balance, T0.[DocNum], T1.[Beginstr],DATEDIFF(DAY,GETDATE(),T0.DocDueDate) AS Diff 
						FROM ODLN T0 
							LEFT JOIN NNM1 T1 ON T0.Series = T1.Series 
						WHERE T0.[CardCode] IN ".$AllCard." AND T0.[DocStatus] = 'O' AND T1.[SeriesName] LIKE 'PA%'";
				$sqlQRY = SAPSelect($sql);
				$Tbody ="<div class='table-responsive'>".
							"<table class='table table-bordered rounded rounded-3 overflow-hidden'>".
								"<thead style='background-color: rgba(155, 0, 0, 0.04); font-size: 13.5px;'>".
									"<tr>".
										"<th class='text-center'>เลขที่เอกสาร</th>".
										"<th class='text-center'>วันที่</th>".
										"<th class='text-center'>วันที่ครบกำหนด</th>".
										"<th class='text-center'>จำนวนวัน<br>เกินกำหนด</th>".
										"<th class='text-center'>ชื่อลูกค้า</th>".
										"<th class='text-center'>จำนวนเงิน</th>".
										"<th class='text-center'>จำนวนเงินคงเหลือ</th>".
									"</tr>".
								"</thead>".
								"<tbody style='font-size: 12px;'>";
				$Total = 0;	
				$CardCode = "";
				$CardName = "";
				while($result = odbc_fetch_array($sqlQRY)) {
					$Total = $Total + $result['Balance'];
					if ($result['NumAtCard'] != '') {
						$DocNumShow = $result['NumAtCard'];
					}else{
						$DocNumShow = $result['Beginstr'].$result['DocNum'];
					}
					$Tbody .= 	"<tr>".
									"<td class='text-center fw-bold'>".$DocNumShow."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['DocDate']))."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['DocDueDate']))."</td>";
									if ($result['Diff'] <= 0) {
										$Tbody .= "<td class='text-center'>".number_format(-1*$result['Diff'],0)."</td>";
									}else{
										$Tbody .= "<td class='text-center'></td>";
									}
						$Tbody .=   "<td>".$result['CardCode']." ".conutf8($result['CardName'])."</td>".
									"<td class='text-right'>".number_format($result['DocTotal'],2)."</td>".
									"<td class='text-right'>".number_format($result['Balance'],2)."</td>".
								"</tr>";
					$CardCode = $result['CardCode'];
                	$CardName = conutf8($result['CardName']);
				}
				$Tbody .= 		"</tbody>".
								"<tfoot style='font-size: 13px; background-color: rgba(0, 0, 0, 0.04);'>".
									"<tr>".
										"<th colspan='6' class='text-right text-primary'>รวมมูลค่าใบยืมที่ยังไม่คืน</th>".
										"<th class='text-right text-primary'>".number_format($Total,2)."</th>".
									"</tr>".
								"</tfoot>".
							"</table>".
						"</div>";
				$HeadModal = "ใบยืมสินค้าที่ยังไม่คืนของ ".$CardCode." ".$CardName;
				break;
			case 3: 
				$sql = "SELECT T0.[DocNum], T0.[DocDate], T0.[DocDueDate], T0.[CardCode], T0.[CardName], T0.[DocTotal], T3.[Beginstr], T1.[ItemCode], T1.[Dscription],(T1.[OpenQty]*T1.[Price]) AS Price, T1.[OpenQty], T1.[Quantity] 
						FROM ORDR T0 
							INNER JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry 
							INNER JOIN OITM T2 ON T1.ItemCode = T2.ItemCode 
							LEFT  JOIN NNM1 T3 ON T0.Series = T3.Series 
						WHERE T0.[CardCode] IN ".$AllCard." AND  T1.[LineStatus] = 'O'";
				$sqlQRY = SAPSelect($sql);
				$Tbody ="<div class='table-responsive'>".
							"<table class='table table-bordered rounded rounded-3 overflow-hidden'>".
								"<thead style='background-color: rgba(155, 0, 0, 0.04); font-size: 13.5px;'>".
									"<tr>".
										"<th class='text-center'>เลขที่เอกสาร</th>".
										"<th class='text-center'>วันที่</th>".
										"<th class='text-center'>วันที่จะส่ง</th>".
										"<th class='text-center'>ชื่อสินค้า</th>".
										"<th class='text-center'>จำนวนที่สั่ง</th>".
										"<th class='text-center'>จำนวนที่ส่ง</th>".
										"<th class='text-center'>มูลค่าค้างส่ง</th>".
									"</tr>".
								"</thead>".
								"<tbody style='font-size: 12px;'>";
				$Total = 0;	
				$CardCode = "";
				$CardName = "";
				while($result = odbc_fetch_array($sqlQRY)) {
					$Total = $Total + $result['Price'];
					$Tbody .= 	"<tr>".
									"<td class='text-center fw-bold'>".$result['Beginstr'].$result['DocNum']."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['DocDate']))."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['DocDueDate']))."</td>".
									"<td>".$result['ItemCode']." ".conutf8($result['Dscription'])."</td>".
									"<td class='text-right'>".number_format($result['Quantity'],0)."</td>".
									"<td class='text-right'>".number_format($result['OpenQty'],2)."</td>".
									"<td class='text-right'>".number_format($result['Price'],2)."</td>".
								"</tr>";
					$CardCode = $result['CardCode'];
                	$CardName = conutf8($result['CardName']);
				}
				$Tbody .= 		"</tbody>".
								"<tfoot style='font-size: 13px; background-color: rgba(0, 0, 0, 0.04);'>".
									"<tr>".
										"<th colspan='6' class='text-right text-primary'>รวมมูลค่าใบสั่งขายค้างส่ง</th>".
										"<th class='text-right text-primary'>".number_format($Total,2)."</th>".
									"</tr>".
								"</tfoot>".
							"</table>".
						"</div>";
				$HeadModal = "ใบสั่งขายที่ยังไม่ส่งสินค้าของ ".$CardCode." ".$CardName;
				break;
			case 4: 
				$sql = "SELECT T0.DocDate,T0.NumAtCard,T1.[BeginStr], T0.[DocNum], T0.[CardCode], T0.[CardName], T0.[DocTotal],T0.U_CNReason
						FROM ORIN T0
							LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
						WHERE T0.[CardCode] IN ".$AllCard." AND (T1.[BeginStr] LIKE 'S1%' OR T1.[BeginStr] LIKE 'SR%') AND  (T0.[DocStatus] = 'C') 
						ORDER BY T0.DocDate  DESC";
				$sqlQRY = SAPSelect($sql);
				$Tbody ="<div class='table-responsive'>".
							"<table class='table table-bordered rounded rounded-3 overflow-hidden'>".
								"<thead style='background-color: rgba(155, 0, 0, 0.04); font-size: 13.5px;'>".
									"<tr>".
										"<th class='text-center'>เลขที่เอกสาร</th>".
										"<th class='text-center'>วันที่</th>".
										"<th class='text-center'>ชื่อร้านค้า</th>".
										"<th class='text-center'>สาเหตุการคืน</th>".
										"<th class='text-center'>มูลค่าการคืน</th>".
									"</tr>".
								"</thead>".
								"<tbody style='font-size: 12px;'>";
				$Total = 0;	
				$CardCode = "";
				$CardName = "";
				while($result = odbc_fetch_array($sqlQRY)) {
					$Total = $Total + $result['DocTotal'];
					if ($result['NumAtCard'] != '') {
						$DocNumShow = $result['NumAtCard'];
					}else{
						$DocNumShow = $result['BeginStr'].$result['DocNum'];
					}
					$Tbody .= 	"<tr>".
									"<td class='text-center fw-bold'>".$DocNumShow."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['DocDate']))."</td>".
									"<td>".$result['CardCode']." ".conutf8($result['CardName'])."</td>".
									"<td class=''>".conutf8($result['U_CNReason'])."</td>".
									"<td class='text-right'>".number_format($result['DocTotal'],2)."</td>".
								"</tr>";
					$CardCode = $result['CardCode'];
                	$CardName = conutf8($result['CardName']);
				}
				$Tbody .= 		"</tbody>".
								"<tfoot style='font-size: 13px; background-color: rgba(0, 0, 0, 0.04);'>".
									"<tr>".
										"<th colspan='4' class='text-right text-primary'>รวมมูลค่าการคืนสินค้า</th>".
										"<th class='text-right text-primary'>".number_format($Total,2)."</th>".
									"</tr>".
								"</tfoot>".
							"</table>".
						"</div>";
				$HeadModal = "ประวัติการคืนสินค้าของ ".$CardCode." ".$CardName;
				break;
			case 5:
				$sql = "SELECT  T0.[CheckNum] , T0.[CheckDate], T0.[CheckSum], T0.[BankCode],T0.[RcptDate], T0.[Branch], T0.[CardCode], T1.[CardName] 
						FROM OCHH  T0 
							JOIN OCRD T1 ON T0.CardCode = T1.CardCode 
						WHERE T0.[Deposited] = 'N' AND T0.[Canceled] = 'N' AND T0.[Converted] = 'N' AND T0.[CardCode] IN ".$AllCard;
				$sqlQRY = SAPSelect($sql);
				$Tbody ="<div class='table-responsive'>".
							"<table class='table table-bordered rounded rounded-3 overflow-hidden'>".
								"<thead style='background-color: rgba(155, 0, 0, 0.04); font-size: 13.5px;'>".
									"<tr>".
										"<th class='text-center'>วันที่รับเช็ค</th>".
										"<th class='text-center'>เลขที่เช็ค</th>".
										"<th class='text-center'>วันที่ในเช็ค</th>".
										"<th class='text-center'>ธนาคาร</th>".
										"<th class='text-center'>ชื่อลูกค้า</th>".
										"<th class='text-center'>จำนวนเงิน</th>".
									"</tr>".
								"</thead>".
								"<tbody style='font-size: 12px;'>";
				$Total = 0;	
				$CardCode = "";
				$CardName = "";
				while($result = odbc_fetch_array($sqlQRY)) {
					$Total = $Total + $result['CheckSum'];
					$Tbody .= 	"<tr>
									 <td class='text-center fw-bold'>".$result['CheckNum']."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['RcptDate']))."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['CheckDate']))."</td>".
									"<td>".conutf8($result['BankCode']." ".$result['Branch'])."</td>".
									"<td>".conutf8($result['CardCode']." ".$result['CardName'])."</td>".
									"<td class='text-right'>".number_format($result['CheckSum'],2)."</td>".
								"</tr>";
					$CardCode = $result['CardCode'];
                	$CardName = conutf8($result['CardName']);
				}
				$Tbody .= 		"</tbody>".
								"<tfoot style='font-size: 13px; background-color: rgba(0, 0, 0, 0.04);'>".
									"<tr>".
										"<th colspan='5' class='text-right text-primary'>รวมมูลค่าเช็ครอขึ้นเงิน</th>".
										"<th class='text-right text-primary'>".number_format($Total,2)."</th>".
									"</tr>".
								"</tfoot>".
							"</table>".
						"</div>";
				$HeadModal = "ใบสั่งขายที่ยังไม่ส่งสินค้าของ ".$CardCode." ".$CardName; 
				break;
			case 6: 
				$sql = "SELECT  T0.CHQ_DateReturn,T0.CHQ_No,T0.CHQ_Amount,
							(SELECT SUM(X1.Amount) FROM chq_detail X1 WHERE X1.ChqDocNum = T0.DocNum ) AS toPaid,
							(SELECT X2.DatePaid FROM chq_detail X2 WHERE X2.ChqDocNum = T0.DocNum ORDER BY DatePaid DESC LIMIT 1) AS LastDate,
							(SELECT X3.Remark FROM chq_detail X3 WHERE X3.ChqDocNum = T0.DocNum ORDER BY DatePaid DESC LIMIT 1) AS Remark
						FROM chq_return T0
						WHERE T0.CardCode IN ".$AllCard." AND T0.Status != 2";
				$sqlQRY = MySQLSelectX($sql);
				$Tbody ="<div class='table-responsive'>".
							"<table class='table table-bordered rounded rounded-3 overflow-hidden'>".
								"<thead style='background-color: rgba(155, 0, 0, 0.04); font-size: 13.5px;'>".
									"<tr>".
										"<th class='text-center'>No.</th>".
										"<th class='text-center'>วันที่เช็คคืน</th>".
										"<th class='text-center'>เลขที่เช็ค</th>".
										"<th class='text-center'>จำนวนเงิน</th>".
										"<th class='text-center'>ยอดคงเหลือ</th>".
										"<th class='text-center'>วันที่ปิดบัญชี</th>".
										"<th class='text-center'>หมายเหตุ</th>".
									"</tr>".
								"</thead>".
								"<tbody style='font-size: 12px;'>";
				$Total = 0;
				$No = 1;
				while($result = mysqli_fetch_array($sqlQRY)) {
					$Total = $Total + $result['toPaid'];
					$Balance = $result['CHQ_Amount'] - $result['toPaid'];
					$Tbody .= 	"<tr>".
									"<td class='text-center'>".$No."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['CHQ_DateReturn']))."</td>".
									"<td class='text-center'>".$result['CHQ_No']."</td>".
									"<td class='text-right'>".number_format($result['CHQ_Amount'],0)."</td>".
									"<td class='text-right'>".number_format($result['Balance'],2)."</td>".
									"<td class='text-center'>".date('d/m/Y',strtotime($result['LastDate']))."</td>".
									"<td class='text-right'>".$result['Remark']."</td>".
								"</tr>";
					$No++;
				}
				$Tbody .= 		"</tbody>".
								"<tfoot style='font-size: 13px; background-color: rgba(0, 0, 0, 0.04);'>".
									"<tr>".
										"<th colspan='6' class='text-right text-primary'>รวมมูลค่าเช็คเด้ง</th>".
										"<th class='text-right text-primary'>".number_format($Total,2)."</th>".
									"</tr>".
								"</tfoot>".
							"</table>".
						"</div>";
				$HeadModal = "ประวัติเช็คเด้ง"; 
				break;
		}
		$arrCol['HeadModal'] = $HeadModal;
		$arrCol['Tbody'] = $Tbody;
	}
}
if ($_GET['a'] == 'atttab'){
	$AttSQL = "SELECT T0.AttachID, T0.VisOrder, T0.FileOriName, T0.FileDirName, T0.FileExt, T0.UploadDate 
	           FROM order_attach T0
			   WHERE T0.DocEntry = '".$_POST['DocEntry']."' AND T0.FileStatus = 'A' ORDER BY T0.VisOrder";
	$AttRow = CHKRowDB($AttSQL);
	if($AttRow == 0) {
		$output = "<tr><td class='text-center' colspan='4'>ไม่มีเอกสารแนบ :(</td></tr>";
	} else {
		$AttQRY = MySQLSelectX($AttSQL);
		$output = "";
		$no = 1;
		while($AttRST = mysqli_fetch_array($AttQRY)) {
			$output .= "<tr>";
				$output .= "<td class='text-right'>".number_format($no,0)."</td>";
				$output .= "<td >".$AttRST['FileOriName'].".".$AttRST['FileExt']."</td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($AttRST['UploadDate']))." เวลา ".date("H:i",strtotime($AttRST['UploadDate']))." น.</td>";
				$output .= "<td class='text-center'><a class='btn btn-success btn-sm' href='../FileAttach/SO/".$AttRST['FileDirName'].".".$AttRST['FileExt']."' target='_blank'><i class='fas fa-file-download fa-fw fa-1x'></i></a></td>";
			$output .= "</tr>";
			$no++;
		}
	}
	$sql1 = "SELECT T0.SlpCode,T1.MainTeam, 
					CASE WHEN T1.MainTeam LIKE 'TT2%' THEN 'DP005'
						 WHEN T1.MainTeam IN ('MT1','EXP') THEN 'DP006'
						 WHEN T1.MainTeam LIKE 'MT2%' THEN 'DP007'
						 WHEN T1.MainTeam IN ('TT1','OUL') THEN 'DP008'
						 ELSE 'DP003' END AS dept
			 FROM order_header T0
			 	  LEFT JOIN oslp T1 ON T0.slpCode = T1.SlpCode 
			 WHERE T0.DocEntry = ".$_POST['DocEntry'];
	$teamCHK = MySQLSelect($sql1);
	if ($teamCHK['dept'] == $_SESSION['DeptCode']){
		$arrCol['dpt'] = 'Y';
	}else{
		$arrCol['dpt'] = 'N';
	}


	

	

}

if ($_GET['a'] == 'gptab'){
	$sql1 = "SELECT T1.TaxType,T0.Line_SP,T0.ItemCode,T0.ItemName,T0.ItemStatus,T0.WhsCode,T0.Quantity,T0.UnitMsr,T0.GrandPrice,
					CASE WHEN T1.TaxType = 'S07' THEN T0.UnitPrice*1.07 ELSE T0.UnitPrice END AS UnitPrice,
					T0.LineProfit,T0.LineTotal,T0.LineVatSum,
					T2.MTPrice2 AS MT_In,T2.MTPrice AS MT_Out
			 FROM order_detail T0
				  LEFT JOIN order_header T1 ON T0.DocEntry = T1.DocEntry
				  LEFT JOIN pricelist T2 ON T0.ItemCode = T2.ItemCode AND T2.PriceType = 'STD'
			 WHERE T0.DocEntry = ".$_POST['DocEntry'];


	$sqlQRY = MySQLSelectX($sql1);
	$TotalPrice = 0;
	$TotalProfit = 0;
	while($result = mysqli_fetch_array($sqlQRY)) {
		if ($result['UnitPrice']*$result['Quantity'] == 0){
			$GP = 0;
		}else{
			$GP = ($result['LineProfit']/($result['LineTotal']+$result['LineVatSum']))*100;
		}
		if ($result['Line_SP'] == 'Y'){
			$SP = " checked='checked' ";
		}else{
			$SP = " ";
		}
		$TotalPrice = $TotalPrice+($result['LineTotal']+$result['LineVatSum']);
		$TotalProfit = $TotalProfit+$result['LineProfit'];
		$output .= "<tr>
						<td><input type='checkbox' class='form-check-input' ".$SP." disabled/></td>
						<td>".$result['ItemCode']." - ".$result['ItemName']." [".$result['ItemStatus']."]</td>
						<td class='text-center'>".$result['WhsCode']."</td>
						<td class='text-right'>".number_format($result['Quantity'])."</td>
						<td class='text-center'>".$result['UnitMsr']."</td>
						<td class='text-right'>".number_format(((($result['LineTotal']+$result['LineVatSum'])-$result['LineProfit'])/$result['Quantity']),2)."</td>
						<td class='text-right'>".number_format($result['UnitPrice'],2)."</td>
						<td class='text-right'>".number_format(($result['UnitPrice']*$result['Quantity']),2)."</td>
						<td class='text-right'>".number_format($result['LineProfit'],2)."</td>
						<td class='text-center'>".number_format($GP,2)."</td>
						<td class='text-right'>".number_format($result['MT_In'],2)."</td>
						<td class='text-right'>".number_format($result['MT_Out'],2)."</td>
					</tr>";

	}
	if ($TotalPrice == 0){
		$TotalGP = "-";
		
	}else{
		$TotalGP = number_format((($TotalProfit/$TotalPrice)*100),2);
		
	}
	$footer = " <tr class='text-primary'>
					<td colspan='7' class='text-center'>รวมทั้งหมด</td>
					<td class='text-right'>".number_format($TotalPrice,2)."</td>
					<td class='text-right'>".number_format($TotalProfit,2)."</td>
					<td class='text-center'>".$TotalGP."</td>
					<td class='text-right'></td>
					<td class='text-right'></td>
				</tr>";
	$arrCol['tb2'] = $footer;
}
if ($_GET['a'] == 'save'){
	$remark = $_POST['Remark'];
	$ID = $_POST['ID'];
	$App = $_POST['App'];
	$DocEntry = $_POST['DocEntry'];
	if ($App == 'Y' OR $App == 'N'){
		$sql1 = "SELECT T2.ID,T1.MainTeam,T2.LvApp,T2.StepApprove AS StepApp,T2.AppSO,T2.AppCR,T2.AppGP,
					T2.TypeApp,T2.ConditionOption AS ConOpt,T2.ResultApp
				FROM order_header T0
					LEFT JOIN oslp T1 ON T0.slpCode = T1.SlpCode
					LEFT JOIN apporder T2 ON T0.DocEntry = T2.DocEntry
					LEFT JOIN users T3 ON T3.uKey = T2.UkeyReq	
					LEFT JOIN positions T4 ON T3.LvCode = T4.LvCode
				WHERE T0.DraftStatus = 'N' AND T0.DocStatus = 'P' AND T0.AppStatus = 'P' AND T0.CANCELED = 'N'  AND T2.ResultApp ='0' AND T0.DocEntry = '".$DocEntry."'
				ORDER BY T2.StepApprove,T2.LvApp";
		$cx=0;
		$NexStep=0;
		$sqlQRY = MySQLSelectX($sql1);
		$NextID =0;
		$Approve['Next'] = '000';
		$Approve['Now'] = '000';
		while($result = mysqli_fetch_array($sqlQRY)) {
			$cx++;
			if ($NexStep==1){
				$AppSO['Next'] = $result['AppSO'];
				$AppCR['Next'] = $result['AppCR'];
				$AppGP['Next'] = $result['AppGP'];
				$NextID = $result['ID'];
				$NexStep++;
			}
			if ($result['ID'] == $ID){
				$sql1 = "UPDATE apporder SET Remark= '".$remark."',ResultApp='".$App."',UkeyApprove='".$_SESSION['ukey']."',ApproveDate=NOW() WHERE ID = ".$ID;
				MySQLUpdate($sql1);
				$NexStep++;
				$AppSO['Now'] = $result['AppSO'];
				$AppCR['Now'] = $result['AppCR'];
				$AppGP['Now'] = $result['AppGP'];
				$TypeApp['Now'] = $result['TypeApp'];
			}

		}
		//echo $cx." - ".$NextID;
		if ($NextID == 0){
			switch ($App){
				case 'Y' :
					$sqlApp = "UPDATE order_header SET CANCELED = 'N',
													DraftStatus = 'N',
													DocStatus = 'C',
													AppStatus = 'Y'
							WHERE DocEntry = '".$DocEntry."'";
					MySQLUpdate($sqlApp);
					$output =  "WAI";
					$arrCol['DocEntry'] = $DocEntry;
					break;
				case 'N ' :
					$output = 'เอกสารไม่อนุมัติ';
					$sqlApp = "UPDATE order_header SET CANCELED = 'N',
														DraftStatus = 'N',
														DocStatus = 'P',
														AppStatus = 'N'
									
					WHERE DocEntry = '".$DocEntry."'";
					MySQLUpdate($sqlApp);
					break;

			}
		}else{
			switch($App){
				case 'N' :
					switch ($TypeApp['Now']){
						case 'A' ://จบเลย
						case 'D' :
							$output = 'เอกสารไม่อนุมัติ';
							$sqlApp = "UPDATE order_header SET CANCELED = 'N',
																DraftStatus = 'N',
																DocStatus = 'P',
																AppStatus = 'N'
											
							WHERE DocEntry = '".$DocEntry."'";
							MySQLUpdate($sqlApp);
							break;
						case 'B' ://ไปต่อ	
						case 'C' :
							$output = "พิจารณาเอกสารเรียบร้อย";
							break;
						case 'E' :	
							$sql1 = "SELECT TypeCR FROM crapp WHERE DocEntry = ".$DocEntry;
							$CHKtype = MySQLSelect($sql1);
							if ($CHKtype['TypeCR'] == 'D'){
								$output = 'เอกสารไม่อนุมัติ';
								$sqlApp = "UPDATE order_header SET CANCELED = 'N',
																	DraftStatus = 'N',
																	DocStatus = 'P',
																	AppStatus = 'N'
												
								WHERE DocEntry = '".$DocEntry."'";
								MySQLUpdate($sqlApp);
								
							}else{
								$output = "พิจารณาเอกสารเรียบร้อย";
							}
							break;
					}
					break;
				case 'Y' :
					$ConOpt = 0;
					switch ($TypeApp['Now']){
						case 'A'://ไปต่อ
						case 'B':
							$output = "พิจารณาเอกสารเรียบร้อย";
							break;
						case 'E' :
							$sql1 = "SELECT TypeCR FROM crapp WHERE DocEntry = ".$DocEntry;
							$CHKtype = MySQLSelect($sql1);
							if ($CHKtype['TypeCR'] == 'C'){
								$output = "พิจารณาเอกสารเรียบร้อย";
							}else{
								$ConOpt = 1;
							}
							break;
						default ://เช็คเงือนไข
							$ConOpt = 1;
							break;
					}
					if ($ConOpt == 1){//CDE
						switch ($TypeApp['Now']){
							case 'C' :
								if ($AppCR['Next'] == '0'){
									$sqlApp = "UPDATE order_header SET CANCELED = 'N',
																	   DraftStatus = 'N',
																	   DocStatus = 'C',
																	   AppStatus = 'Y'
											   WHERE DocEntry = '".$DocEntry."'";
									MySQLUpdate($sqlApp);
									$output =  "WAI";
									$arrCol['DocEntry'] = $DocEntry;
								}else{
									$sql1 = "SELECT TypeCR FROM crapp WHERE DocEntry = ".$DocEntry;
									$CHKtype = MySQLSelect($sql1);
									if ($CHKtype['TypeCR'] == 'C'){
										$output = "พิจารณาเอกสารเรียบร้อย";
									}else{
										$sqlApp = "UPDATE order_header SET CANCELED = 'N',
																		   DraftStatus = 'N',
																		   DocStatus = 'C',
																		   AppStatus = 'Y'
												   WHERE DocEntry = '".$DocEntry."'";
										MySQLUpdate($sqlApp);
										$output =  "WAI";
										$arrCol['DocEntry'] = $DocEntry;
									}
								}
								break;
							case 'E' :
								if ($AppGP['Next'] == '0'){
									$sqlApp = "UPDATE order_header SET CANCELED = 'N',
																		DraftStatus = 'N',
																		DocStatus = 'C',
																		AppStatus = 'Y'
												WHERE DocEntry = '".$DocEntry."'";
									MySQLUpdate($sqlApp);
									$output =  "WAI";
									$arrCol['DocEntry'] = $DocEntry;

								}else{
									$output = "พิจารณาเอกสารเรียบร้อย";
								}
								break;
							case 'D' :
								if ($_SESSION['LvCode'] == 'LV003'){
									$sqlApp = "UPDATE order_header SET CANCELED = 'N',
																		DraftStatus = 'N',
																		DocStatus = 'C',
																		AppStatus = 'Y'
												WHERE DocEntry = '".$DocEntry."'";
									MySQLUpdate($sqlApp);
									$output =  "WAI";
									$arrCol['DocEntry'] = $DocEntry;
								}else{
									$output = "พิจารณาเอกสารเรียบร้อย";
								}
							break;
						}
					}
					break;
			}
		}
	}else{
			$output = 'กรุณาพิจารณาเอกสาร';
	}

	/*
	if ($AppSO == 1 && $App == 'Y'){
		if ($NexTID == 0){
			$sqlApp = "UPDATE order_header SET CANCELED = 'N',
				  							   DraftStatus = 'N',
											   DocStatus = 'C',
											   AppStatus = 'Y'
					   WHERE DocEntry = '".$DocEntry."'";
			MySQLUpdate($sqlApp);
			//require("SAPInsert.php?Doc=".$DocEntry);
			$output =  "WAI";
			$arrCol['DocEntry'] = $DocEntry;

		}else{
			$NexStep = 2;
		}
	}else{
		if ($AppSO == 1){
			if ($App == 'N'){
				$output = 'เอกสารไม่อนุมัติ';
				$sqlApp = "UPDATE order_header SET CANCELED = 'N',
													DraftStatus = 'N',
													DocStatus = 'P',
													AppStatus = 'N'
								
				WHERE DocEntry = '".$DocEntry."'";
				MySQLUpdate($sqlApp);
			}else{
				$output = 'กรุณาพิจารณาเอกสาร';
			}
		}else{
			$NexStep = 1;
		}	
	}


	if ($NexStep == 1 && $App != '0' ){
		
		if ($App == 'N' && ($TypeApp == 'D' || $TypeApp == 'A')){// เคสไม่อนุมัติละจบเลย
			$output = 'เอกสารไม่อนุมัติ';
			$sqlApp = "UPDATE order_header SET CANCELED = 'N',
												DraftStatus = 'N',
												DocStatus = 'P',
												AppStatus = 'N'
							
			WHERE DocEntry = '".$DocEntry."'";
			MySQLUpdate($sqlApp);
		}
		if (($App == 'N' && $TypeApp == 'C') || $TypeApp == 'B' || ($App == 'Y' && $TypeApp == 'A') || ($TypeApp == 'E' && $ConOpt == 'Y' )){//NextStep
			$NexStep = 2;
		}

		if (($TypeApp == 'C' && $App == 'Y') || ($TypeApp == 'D' && $App == 'Y') || ($TypeApp =='E' && $ConOpt == 'N')){
			$sqlApp = "UPDATE order_header SET CANCELED = 'N',
												DraftStatus = 'N',
												DocStatus = 'C',
												AppStatus = 'Y'
					   WHERE DocEntry = '".$DocEntry."'";
			MySQLUpdate($sqlApp);
			//require("SAPInsert.php?Doc=".$DocEntry);
			$output =  "WAI";
			$arrCol['DocEntry'] = $DocEntry;
		} 
	}else{
		if ($output != 'WAI'){
			$output = 'กรุณาพิจารณาเอกสาร';
		}
		
	}
	
	if ($NexStep == 2){

		
	}
*/
}

if($_GET['a'] == 'UploadsFile') {
	$DocEntry = $_POST['DocEntry'];
	
	$sqlHead = "SELECT CONCAT(T0.DocType,'V-',T0.DocNum) AS 'DocNum' FROM order_header T0 WHERE T0.DocEntry = $DocEntry LIMIT 1";
	$result_Head = MySQLSelect($sqlHead);
	$FileDirName = $result_Head['DocNum'];

	$Chk = CHKRowDB("SELECT * FROM order_attach T0 WHERE T0.DocEntry = $DocEntry");
	if($Chk == 0) {
		$newVisOrder = 0;
	}else{
		$sql_order = "SELECT MAX(T0.VisOrder)+1 AS 'VisOrder' FROM order_attach T0 WHERE T0.DocEntry = $DocEntry";
		$result_order = MySQLSelect($sql_order);
		$newVisOrder = $result_order['VisOrder'];
	}

	/* INSERT ATTACHMENT */
	if(isset($_FILES['AttachOrder']['name'])) {
		$Totals = count($_FILES['AttachOrder']['name'])-1;
		for($i = 0; $i <= $Totals; $i++) {
			$FileProcess = explode(".",basename($_FILES['AttachOrder']['name'][$i]));
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
			$tmpFilePath = $_FILES['AttachOrder']['tmp_name'][$i];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/SO/".$FileDirName."-".$newVisOrder.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				// $DocEntry = 2;

				$AttachSQL = "INSERT INTO order_attach SET
					DocEntry = $DocEntry,
					VisOrder = $newVisOrder,
					FileOriName = '$FileOriName',
					FileDirName = '".$FileDirName."-".$newVisOrder."',
					FileExt = '$FileExt',
					UploadUkey = '".$_SESSION['ukey']."'";
				// echo $AttachSQL;
				MySQLInsert($AttachSQL);
			}
			$newVisOrder++;
		}
	}
}


$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
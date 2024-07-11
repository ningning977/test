<?php
/*include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
*/

$output = "";
$App['SO'] = '0';
$App['CR'] = '0';
$App['GP'] = '0';
$App['CV'] = '0';
$CHKcr = 'N';
$sms1 = "";

$sql1 = "SELECT T0.DocEntry,T0.DocNum,DATE(T0.DocDate) AS DocDate,T0.DocType,T0.ObjType,T0.Payment_Cond,T0.DocStatus,T0.CardCode,T2.CardName,T0.SlpCode,T0.DocTotal,
                 ((T0.GrossProfit/(T0.DocTotal))*100) AS GP,
				T1.TeamCode, T1.MainTeam, T2.MTGroup
		 FROM order_header T0
			   LEFT JOIN oslp T1 ON T0.SlpCode = T1.SlpCode
			   LEFT JOIN ocrd T2 ON T2.CardCode = T0.CardCode
		 WHERE T0.DocEntry = '".$DocEntry."'";
//echo $sql1;		 
$DataCus = MySQLSelect($sql1);
$CardCode = $DataCus['CardCode']; 
$SlpCode  = $DataCus['SlpCode'];
$sqlFa = "SELECT T0.CardCode,T0.FatherCard
          FROM OCRD T0
          WHERE   T0.FatherCard = '".$CardCode."' OR T0.CardCode = '".$CardCode."'
		  ORDER BY T0.CardCode DESC";
$MainCR = 0;
$getCardList = SAPSelect($sqlFa);
$CardList = "('";
while ($FaCard = odbc_fetch_array($getCardList)){
	$CardList .= $FaCard['CardCode']."','";
    if ($FaCard['FatherCard'] != null){
        $CardCode = $FaCard['FatherCard']; 
	}
}

$CardList = substr($CardList,0,-2).")"; 
/* เช็คสถานะเกินวงเงิน */
$TypeCR = "N";
if ($_SESSION['DeptCode'] == 'DP003' OR $_SESSION['DeptCode'] == 'DP005' OR $_SESSION['DeptCode'] == 'DP008'){
	$AllCredit = 0;
	$sql1 = "SELECT SUM(P0.Balance) AS Balance
			FROM (SELECT (T0.[DocTotal] - T0.[PaidToDate]) AS Balance
				FROM OINV T0 
				WHERE T0.[CardCode] IN ".$CardList." AND T0.[DocStatus] = 'O' 
				UNION ALL
				SELECT -1*(T0.[DocTotal] - T0.[PaidToDate]) AS Balance
				FROM ORIN T0 
				WHERE T0.[CardCode] IN ".$CardList." AND T0.[DocStatus] = 'O') P0";
	$getAllBill = SAPSelect($sql1);
	while ($CusAllBill = odbc_fetch_array($getAllBill)){
		$AllCredit = $CusAllBill['Balance'];
	}
	$sql2 = "SELECT SUM(T0.CreditLine) AS CR
			FROM OCRD T0
			WHERE T0.CardCode IN ".$CardList;
	$getCR = SAPSelect($sql2);
	$CusCredit = odbc_fetch_array($getCR);


	$sql3 = "SELECT 'SO' AS Tbtype,SUM(P1.LineTotal) AS  Amount
		 	 FROM ORDR P0
			   	  INNER JOIN RDR1 P1 ON P0.DocEntry = P1.DocEntry 
			 WHERE P0.CardCode IN   ".$CardList."   AND P1.[LineStatus] = 'O'  
			 UNION ALL
			 SELECT 'AP' AS TbType,SUM(A0.DocTotal-A0.PaidToDate) AS  Amount
			 FROM ODLN A0
			  	  LEFT JOIN NNM1 A1 ON A0.Series = A1.Series
			 WHERE A0.CardCode IN   ".$CardList."   AND A0.[DocStatus] = 'O' AND A1.[SeriesName] LIKE 'PA%'
			 UNION ALL
			 SELECT 'CQ' AS TbType,SUM(T0.CheckSum) AS Amount 
			 FROM OCHH T0
			 WHERE T0.CardCode IN ".$CardList."  AND T0.[Deposited] = 'N' AND T0.[Canceled] = 'N' AND T0.[Converted] = 'N'";
	$getCHK = SAPSelect($sql3);
	while ($CusCHKCredit = odbc_fetch_array($getCHK)){
		switch ($CusCHKCredit['Tbtype']){
			case 'SO' :
				if ($CusCHKCredit['Amount'] == NULL OR $CusCHKCredit['Amount'] == ''){
					$Credit['SO'] = 0;
				}else{
					$Credit['SO'] = $CusCHKCredit['Amount'];
				}
				break;
			case 'AP' :
				if ($CusCHKCredit['Amount'] == NULL OR $CusCHKCredit['Amount'] == ''){
					$Credit['AP'] = 0;
				}else{
					$Credit['AP'] = $CusCHKCredit['Amount'];
				}
				break;
			case 'CQ' :
				if ($CusCHKCredit['Amount'] == NULL OR $CusCHKCredit['Amount'] == ''){
					$Credit['CQ'] = 0;
				}else{
					$Credit['CQ'] = $CusCHKCredit['Amount'];
				}
				break;
		}
	}

	$CR = ($AllCredit  + $Credit['AP'] + $Credit['CQ'] +$Credit['SO']+$DataCus['DocTotal'])-$CusCredit["CR"];
	$AppLV = " ";
	if ($CR > 0 ){ // เกินวงเงิน
		$App["CR"] = '1'; 
		$TypeCR = 'A';
		if (($CusCredit["CR"]*1.5) > ($AllCredit  + $Credit['AP'] + $Credit['CQ'] +$Credit['SO']+$DataCus['DocTotal'])){
			$AppLV = " AND T0.UserApp = '18' ";
		}else{
			$AppLV = " AND (T0.UserApp = '18' OR T0.UserApp = 'LV057') ";
			$TypeCR = 'B';
			if ($CR>200000){
				$CHKcr = 'Y';
				$TypeCR = 'C';
				$AppLV = "  ";

			}
		}
	}else{
		$sql1 = "SELECT COUNT(DocEntry) AS OV FROM OINV WHERE DocDueDate < '".date("Y-m-d",strtotime("-60 day",strtotime($DataCus['DocDate'])))."' AND DocStatus = 'O' AND CardCode IN ".$CardList;
		//echo $sql1;
		$getOverDue = SAPSelect($sql1);
		$OverDue = odbc_fetch_array($getOverDue);
		if ($OverDue['OV'] > 0){//เกินดิว
			$App["CR"] = '1'; 
			$TypeCR = 'D';
			$AppLV = " AND (T0.UserApp = '18' OR T0.UserApp = 'LV057') ";
		}else{
			$App["CR"] = '0'; 
		}
	}
	if ($DataCus['Payment_Cond'] == 'CS'){
		$sql1 = "SELECT 'OINV' AS DocType,T0.DocEntry FROM OINV T0 WHERE T0.CardCode = '".$DataCus['CardCode']."' AND T0.DocStatus = 'O'
				 UNION ALL
				 SELECT 'ODLN' AS DocType,T1.DocEntry FROM ODLN T1 WHERE T1.CardCode = '".$DataCus['CardCode']."' AND T1.DocStatus = 'O'";
		if (ChkRowSAP($sql1) == 0){
			$App["CR"] = '0'; 
		}else{
			$App["CR"] = '1'; 
			$TypeCR = 'E';
			$AppLV = " AND (T0.UserApp = '18' OR T0.UserApp = 'LV057') ";
		}

	}

	if ($App['CR'] == '1'){
		$sqlCR = "INSERT INTO crapp SET DocEntry = ".$DocEntry.",
										NewOrder = ".$DataCus['DocTotal'].",
										OldCR = ".intval($AllCredit+$Credit['AP']+$Credit['CQ']+$Credit['SO']).",
										CRLimit = ".$CusCredit["CR"].",
										TypeCR = '".$TypeCR."'";
		MySQLInsert($sqlCR);
	}
}else{
	$App['CR'] = '0';
}


/* เช็คขอราคาพิเศษ 
$sql1 = "SELECT TransID FROM order_detail WHERE DocEntry = '".$DocEntry."' AND Line_SP = 'Y'";
if ($_SESSION['DeptCode'] == 'DP006' || $_SESSION['DeptCode'] == 'DP007'){
	$GPchk = 0;
}else{
	$GPchk = 1;
}
if (CHKRowDB($sql1) > 0 && $GPchk == 1 ){
	if ($DataCus['MTGroup'] == 0) {
		$sql1 = "SELECT DISTINCT GroupCode FROM groupprice WHERE CardCode IN ".$CardList;
	}else{
		$sql1 = "SELECT DISTINCT GroupCode FROM groupprice WHERE CardCode IN $CardList OR MTGroup = '".$DataCus['MTGroup']."'";
	}
	if (CHKRowDB($sql1) == 0){
		$GPrice = " ('STD') ";
	}else{
		$getGPrice = MySQLSelect($sql1);
		$GPrice = " ('STD','".$getGPrice['GroupCode']."') ";
	}
	
	$sql1 = "SELECT T0.ItemCode,T0.UnitPrice,T0.Quantity,T0.LineProfit,T0.LineTotal,T0.LineVatSum
	         FROM order_detail T0
			 WHERE DocEntry = '".$DocEntry."' AND Line_SP = 'Y' 
			 ORDER BY T0.UnitPrice";
	$getCHkPrice = MySQLSelectX($sql1);
	while ($CHkPrice = mysqli_fetch_array($getCHkPrice)){
		if ($App['GP'] != '1'){
			$sql2 = "SELECT S1P,S2P,S3P,MgrPrice FROM pricelist T0 WHERE T0.PriceStatus = 'A' AND PriceType IN $GPrice AND ItemCode = '".$CHkPrice['ItemCode']."'";
			$getAppPrice = MySQLSelectX($sql2);		 
			$a=0;
			while ($AllPrice = mysqli_fetch_array($getAppPrice)){
				if ($AllPrice['S1P'] > 0){
					$PriceList[$a]=$AllPrice['S1P'];
					$a++;
				}
				if ($AllPrice['S2P'] > 0){
					$PriceList[$a]=$AllPrice['S2P'];
					$a++;
				}
				if ($AllPrice['S3P'] > 0){
					$PriceList[$a]=$AllPrice['S3P'];
					$a++;
				}
				if ($AllPrice['MgrPrice'] > 0){
					$PriceList[$a]=$AllPrice['MgrPrice'];
					$a++;
				}
			}
			$MgrPrice = min($PriceList);
			// $sql2 = 
			// 	"SELECT
			// 		CASE
			// 			WHEN T0.MgrPrice != 0 THEN T0.MgrPrice/1.07
			// 			WHEN T0.P0 != 0 AND T0.P0 < T0.P1 AND T0.P0 < T0.P2 AND T0.P0 < T0.S1P AND T0.P0 < T0.S2P AND T0.P0 < T0.S3P THEN T0.P0/1.07
			// 			WHEN T0.P1 != 0 AND T0.P1 < T0.P2 AND T0.P1 < T0.S1P AND T0.P1 < T0.S2P AND T0.P1 < T0.S3P THEN T0.P1/1.07
			// 			WHEN T0.P2 != 0 AND T0.P2 < T0.S1P AND T0.P2 < T0.S2P AND T0.P2 < T0.S3P THEN T0.P2/1.07
			// 			WHEN T0.S1P != 0 AND T0.S1P < T0.S2P AND T0.S1P < T0.S3P THEN T0.S1P/1.07
			// 			WHEN T0.S2P != 0 AND T0.S2P < T0.S3P THEN T0.S2P/1.07
			// 			WHEN T0.S3P != 0 THEN T0.S3P/1.07
			// 		ELSE 0 END AS 'Minimum'
			// 	FROM pricelist T0
			// 	WHERE T0.ItemCode = '".$CHkPrice['ItemCode']."' AND T0.PriceType IN $GPrice AND T0.PriceStatus = 'A'";
			// $getAppPrice = MySQLSelect($sql2);
			// $MgrPrice = $getAppPrice['Minimum'];

			if ($CHkPrice['UnitPrice'] >= $MgrPrice && $App['GP'] == '0'){
				$App['GP'] = 'Mgr';
			}else{
				$GP = ($CHkPrice['LineProfit']/($CHkPrice['LineTotal']+$CHkPrice['LineVatSum']))*100;
				if ($GP < 30 ){
					$App['GP'] = '1';
				}else{
					$App['GP'] = 'MK';
				}
			}
		}	
	}
}else{
	if ($DataCus['GP'] < 30){
		$App['GP'] = '1';
	}else{
		$App['GP'] = '0';
	}
}
*/

if ($DataCus['GP'] < 25){
	$App['GP'] = '1';
}else{
	$App['GP'] = '0';
	if ($_SESSION['DeptCode'] == 'DP006' || $_SESSION['DeptCode'] == 'DP007'){
		$GPchk = 0;
		$App['GP'] = '0';
	}else{
		$GPchk = 1;
		$sql1 = "SELECT T0.UnitPrice*1.07 AS  UnitPrice,T0.ItemCode,
		T0.LineProfit,T0.LineTotal,T0.LineVatSum,T2.MTPrice2 AS MT_In,T2.MTPrice AS MT_Out,T0.Line_SP
			FROM order_detail T0
			LEFT JOIN order_header T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN pricelist T2 ON T0.ItemCode = T2.ItemCode AND T2.PriceType = 'STD'
			WHERE T0.DocEntry = ".$DocEntry;
		$sqlQRY = MySQLSelectX($sql1);
		$GP=0;
		while($result = mysqli_fetch_array($sqlQRY)) {
			if ($App['GP'] == '0'){
				$GP = ($result['LineProfit']/($result['LineTotal']+$result['LineVatSum']))*100; 
				if ($result['UnitPrice'] > 0 && $GP < 25) {
					if ($result['Line_SP'] == 'Y'){
						if ($DataCus['MTGroup'] == 0) {
							$sql1 = "SELECT DISTINCT GroupCode FROM groupprice WHERE CardCode IN ".$CardList;
						}else{
							$sql1 = "SELECT DISTINCT GroupCode FROM groupprice WHERE CardCode IN $CardList OR MTGroup = '".$DataCus['MTGroup']."'";
						}
						if (CHKRowDB($sql1) == 0){
							$GPrice = " ('STD') ";
						}else{
							$getGPrice = MySQLSelect($sql1);
							$GPrice = " ('STD','".$getGPrice['GroupCode']."') ";
						}
						$sql2 = "SELECT S1P, S2P, S3P, MgrPrice FROM pricelist T0 WHERE T0.PriceStatus = 'A' AND PriceType IN $GPrice AND ItemCode = '".$result['ItemCode']."'";
						$getAppPrice = MySQLSelectX($sql2);		 
						$a=0;
						while ($AllPrice = mysqli_fetch_array($getAppPrice)){
							if ($AllPrice['S1P'] > 0){
								$PriceList[$a]=$AllPrice['S1P'];
								$a++;
							}
							if ($AllPrice['S2P'] > 0){
								$PriceList[$a]=$AllPrice['S2P'];
								$a++;
							}
							if ($AllPrice['S3P'] > 0){
								$PriceList[$a]=$AllPrice['S3P'];
								$a++;
							}
							if ($AllPrice['MgrPrice'] > 0){
								$PriceList[$a]=$AllPrice['MgrPrice'];
								$a++;
							}
						}
						$MgrPrice = min($PriceList);
						if ($result['UnitPrice'] >= $MgrPrice){
							$App['GP'] = 'Mgr';
						}else{
							if ($result['ItemCode'] != '00-000-010'){
								$App['GP'] = '1';
							}
						}
					}else{
						$App['GP'] = '0';
					}
				}
			}
		}
	}
}




$SlpNoGP = array(123,124,125,126,251,291,296);
$SlpChk  = array_search($SlpCode, $SlpNoGP);

if($SlpChk) {
	/* BY PASS ไม่ต้องขออนุมัติ GP */
	$App['GP'] = '0';
}

if ($DataCus['DocType'] == 'SA' && $App['GP'] == '1'){
	$App['GP'] = 'Mgr';
}


/*เช็คสิทธิ์ขอ Approve SO*/
$sql1 = "SELECT DocType FROM approvecenter WHERE DocType = 'SO-App' AND TeamCode = '".$DataCus['TeamCode']."' AND StatusDoc = 'A' AND ConditionOption = 'N'";
if (CHKRowDB($sql1) != 0){
	$App['SO'] = '1';
}
/*เช็คสิทธิ์ขอ Approve SB*/
//$sql1 = "SELECT DocType FROM approvecenter WHERE DocType = 'SO-App' AND TeamCode = '".$DataCus['TeamCode']."' AND StatusDoc = 'A'";
$MKapp = 0;
if ($DataCus['DocType'] == 'SB'){
	$App['SO'] = '1';
	$App['GP'] = 'Mgr';
	//$App['CR'] = '0';
	$sql1 = "SELECT ItemCode FROM order_detail WHERE ItemCode LIKE '99%' AND DocEntry = ".$DocEntry;
	if (CHKRowDB($sql1) == 0){
		$MKapp=1;
	}
}


/**  เช็คแปลงสินค้า **/
/*
$sql1 = "SELECT TransID FROM order_detail WHERE DocEntry = '".$DocEntry."' AND Line_CV = 'Y'";
if (CHKRowDB($sql1) > 0){
	$App['SO'] = '1';
}else{
	//$App['SO'] = '0';
}
*/

/*******ดำเนินการ บันทึก || ส่งเอกสาร ****************/
// echo "SO :".$App['SO']."<br> SP : ". $App['GP']."<br> CR : ".$App['CR']."<br>";






if ($App['SO'] == '0' && $App['GP'] == '0' && $App['CR'] == '0' ){
	
	$sqlApp = "UPDATE order_header SET CANCELED = 'N',
									   DraftStatus = 'N',
									   DocStatus = 'C',
									   AppStatus = 'Y'
										
				WHERE DocEntry = '".$DocEntry."'";
	MySQLUpdate($sqlApp);
	// echo "Insert SAP";
	/*
	$arrCol['Status'] = 'B';
	$arrCol['errMsg'] = '';
	*/
	// header("../../../../core/ORDR.php?x=".$DocEntry);
	// $arrCol['errMsg'] = "<a href='../../../../core/ORDR.php?x=".$DocEntry."' target='_blank'>Link</a>";
	//require($url."/core/ORDR.php?x=".$DocEntry);
	$arrCol['Status'] = 'F';
	$arrCol['errMsg'] = $DocEntry;

	
}else{
	$arrCol['Status'] = 'A';
	$arrCol['errMsg'] = 'เอกสารรออนุมัติ';
/*
	$WH['SO'] = $WH['GP'] = $WH['CR'] = " ";
	if ($App['SO'] == '1'){
		$WH['SO'] = " ApproveCode = 'AppSO' ";
	}

	if ($App['GP'] == '1'){
		$WH['GP'] = " ApproveCode = 'AppGP' ";
	}else{
		if ($App['GP'] == 'Mgr'){
			$WH['GP'] = " ApproveCode = 'MgrGP' ";	
		}
	}
	if ($App['CR'] == '1'){
		$WH['CR'] = " ApproveCode = 'AppCR' ";
	}

	$WH['Finish'] = "(".$WH['SO']." OR ".$WH['GP']." OR ".$WH['CR'].") AND (T0.teamCode = '0' OR T0.TeamCode = '".$DataCus['TeamCode']."') ";
	
*/ 
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
	$sqlApp = "UPDATE order_header SET CANCELED = 'N',
									   DraftStatus = 'N',
									   DocStatus = 'P',
									   AppStatus = 'P'
									   
			   WHERE DocEntry = '".$DocEntry."'";
	MySQLUpdate($sqlApp);
	
	if ($App['SO'] != '0'){ //อนุมัติ SO
		if ($DataCus['DocType'] == 'SB'){
			$WPhus = " OR T0.ConditionOption = 'SB' ";
		}else{
			$WPhus = "  ";
		}
		$sms1 = "";
		$sms1 .= "\nมีการบันทึกข้อมูลเอกสาร เลขที่ : `".$DataCus['DocType']."V-".$DataCus['DocNum']."`\n";
		$sms1 .= "ข้อมูลลูกค้า : `".$DataCus['CardCode']."-".$DataCus['CardName']."` \n"; 
		switch ($_SESSION['DeptCode']){
			case 'DP008' :
				LineNoti('OULSO',$sms1);
				break;
			case 'DP005' :
				LineNoti('TTSO',$sms1);
				break;
		} 


		$sql1 = "SELECT T0.DocType,T0.LvApp,T0.ApproveCode AS AppCode,
		                T0.UserApp,
						T0.StepApprove,T0.TypeApp,T0.ConditionOption,T0.TeamCode,
						CASE WHEN T1.MainTeam = 'TT2' THEN 'DP005'
			  				 WHEN T1.MainTeam = 'MT1' THEN 'DP006'
			  				 WHEN T1.MainTeam = 'MT2' THEN 'DP007'
			 				 WHEN T1.MainTeam = 'OUL' THEN 'DP008'
			 		    ELSE 'DP003' END AS 'DeptCode'
 				 FROM approvecenter T0
					  LEFT JOIN teamcode T1 ON T0.TeamCode = T1.TeamCode
				 WHERE T0.ApproveCode = 'AppSO' AND (T0.TeamCode = '".$DataCus['TeamCode']."' OR T0.TeamCode = '".$DataCus['MainTeam']."') AND StatusDoc = 'A' ".$WPhus;
		$sql1 .= " ORDER BY T0.LvApp,T0.StepApprove ";
        //echo $sql1;
		$AppSO = MySQLSelect($sql1);
		if ($AppSO['UserApp'] == '18'){
			switch($_SESSION['DeptCode']){
				case 'DP005':
				case 'DP006':
				case 'DP007':
				case 'DP008':
					$AppSO['UserApp'] = '18';
					break;
				default :
					$AppSO['UserApp'] = 'LV011';
					break;
			}
		}
		if ($AppSO['UserApp'] == 'LV038' AND $_SESSION ['DeptCode'] == 'DP008'){
			$AppSO['UserApp'] = 'LV051';
		}
		if ($AppSO['UserApp'] == 'LV038' AND $_SESSION ['DeptCode'] == 'DP003'){
			$AppSO['UserApp'] = 'LV011';
		}
		if (substr($AppSO['UserApp'],0,2) == 'LV'){
			$sql2 = "SELECT uKey,LineToken FROM users WHERE LvCode = '".$AppSO['UserApp']."' AND UserStatus = 'A'";
		}else{
			if (strlen($AppSO['UserApp']) == 32){
				$sql2 = "SELECT uKey,LineToken FROM users WHERE  uKey= '".$AppSO['UserApp']."' AND UserStatus = 'A'";
			}else{
				$sql2 = "SELECT T0.uKey,T0.LineToken 
						 FROM users T0
						 	  LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
						 WHERE  T1.uClass = '".$AppSO['UserApp']."' AND T1.DeptCode = '".$_SESSION['DeptCode']."' AND UserStatus = 'A'";
			}

		}
		if (CHKRowDB($sql2) > 1){
			$KeyApp = $AppSO['UserApp'];
		}else{
			$LineUser = MySQLSelect($sql2);
			$KeyApp = $LineUser['uKey'];
			$LineToken = $LineUser['LineToken'];
		}
		if ($MKapp == 1){
			$sqlIN = "INSERT apporder SET DocEntry = ".$DataCus['DocEntry']." ,
			LvApp = 1,
			StepApprove = 4,
			AppSO = 1,
			UkeyReq = '4b23a9096e0cbb9714875032094bb466',
			TypeApp = 'D',
			ConditionOption = 'SB'";
			MySQLInsert($sqlIN);
		}
		$sql3 = "SELECT ID FROM apporder WHERE UkeyReq = '".$KeyApp."' AND DocEntry = '".$DataCus['DocEntry']."'";
		if (CHKRowDB($sql3) == 0){
			$sqlIN = "INSERT apporder SET DocEntry = ".$DataCus['DocEntry']." ,
										LvApp = 1,
										StepApprove = ".$AppSO['StepApprove'].",
										AppSO = 1,
										UkeyReq = '".$KeyApp."',
										TypeApp = '".$AppSO['TypeApp']."',
										ConditionOption = '".$AppSO['ConditionOption']."'";
			MySQLInsert($sqlIN);
		}

	}
	if ($App['GP'] != '0'){//GP
		if ($App['GP'] == 'Mgr'){
			$CodeApp = 'MgrGP';
		}else{
			$CodeApp = 'AppGP';
		}
		$sms1 = "";
		$sms1 .= "\nมีการขอราคาพิเศษ เลขที่ : `".$DataCus['DocType']."V-".$DataCus['DocNum']."`\n";
		$sms1 .= "ข้อมูลลูกค้า : `".$DataCus['CardCode']."-".$DataCus['CardName']."` \n"; 
		LineNoti('GPApp',$sms1);

		$sql1 = "SELECT T0.DocType,T0.LvApp,T0.ApproveCode AS AppCode,
						T0.UserApp,
						T0.StepApprove,T0.TypeApp,T0.ConditionOption,T0.TeamCode,
						CASE WHEN T1.MainTeam = 'TT2' THEN 'DP005'
							WHEN T1.MainTeam = 'MT1' THEN 'DP006'
							WHEN T1.MainTeam = 'MT2' THEN 'DP007'
							WHEN T1.MainTeam = 'OUL' THEN 'DP008'
						ELSE 'DP003' END AS 'DeptCode'
				FROM approvecenter T0
					LEFT JOIN teamcode T1 ON T0.TeamCode = T1.TeamCode
				WHERE T0.ApproveCode = '".$CodeApp."' AND (T0.TeamCode = '".$DataCus['TeamCode']."' OR T0.TeamCode = '0') AND StatusDoc = 'A' 
				ORDER BY T0.StepApprove" ;
		$getAppGP = MySQLSelectX($sql1);
		$ax=0;
		while ($AppGP = mysqli_fetch_array($getAppGP)){	
			$ax++;
			if ($AppGP['UserApp'] == '18'){
				switch($_SESSION['DeptCode']){
					case 'DP005':
					case 'DP006':
					case 'DP007':
					case 'DP008':
						$AppGP['UserApp'] = '18';
						break;
					default :
						$AppGP['UserApp'] = 'LV011';
						break;
				}
			}
			if ($AppGP['UserApp'] == 'LV038' AND $_SESSION ['DeptCode'] == 'DP008'){
				$AppGP['UserApp'] = 'LV051';
			}
			if ($App['GP'] == 'MK' && ($AppGP['UserApp']  == 'LV003' OR $AppGP['UserApp'] == 'LVOO5')){
				$aomaom = 'waiwai';
			}else{
				if (substr($AppGP['UserApp'],0,2) == 'LV'){
					$sql2 = "SELECT uKey,LineToken FROM users WHERE LvCode = '".$AppGP['UserApp']."' AND UserStatus = 'A'";
				}else{
					if (strlen($AppGP['UserApp']) == 32){
						$sql2 = "SELECT uKey,LineToken FROM users WHERE  uKey= '".$AppGP['UserApp']."' AND UserStatus = 'A'";
					}else{
						$sql2 = "SELECT T0.uKey,T0.LineToken 
								 FROM users T0
									   LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
								 WHERE  T1.uClass = '".$AppGP['UserApp']."' AND T1.DeptCode = '".$_SESSION['DeptCode']."' AND UserStatus = 'A'";
					}
		
				}
				if (CHKRowDB($sql2) > 1){
					$KeyApp = $AppGP['UserApp'];
				}else{
					$LineUser = MySQLSelect($sql2);
					$KeyApp = $LineUser['uKey'];
					$LineToken = $LineUser['LineToken'];
				}
				$sql3 = "SELECT ID FROM apporder WHERE UkeyReq = '".$KeyApp."' AND DocEntry = '".$DataCus['DocEntry']."'";
				if (CHKRowDB($sql3) == 0){
					$sqlIN = "INSERT apporder SET DocEntry = ".$DataCus['DocEntry']." ,
											 LvApp = 2,
											 StepApprove = ".$AppGP['StepApprove'].",
											 AppGP = 1,
											 UkeyReq = '".$KeyApp."',
											 TypeApp = '".$AppGP['TypeApp']."',
											 ConditionOption = '".$AppGP['ConditionOption']."'";
					MySQLInsert($sqlIN);
				}else{
					$AppID = MySQLSelect($sql3);
					$sqlUpdate = "UPDATE apporder SET AppGP = 1 WHERE ID = ".$AppID['ID'];
					MySQLUpdate($sqlUpdate);
				}

			}
		}
	}
	if ($App['CR'] != '0'){
		$sms1 = "";
		$sms1 .= "\nมีการขอเกินวงเงิน เลขที่ : `".$DataCus['DocType']."V-".$DataCus['DocNum']."`\n";
		$sms1 .= "ข้อมูลลูกค้า : `".$DataCus['CardCode']."-".$DataCus['CardName']."` \n"; 
		LineNoti('CRApp',$sms1);
		$sql1 = "SELECT T0.DocType,T0.LvApp,T0.ApproveCode AS AppCode,
						T0.UserApp,
						T0.StepApprove,T0.TypeApp,T0.ConditionOption,T0.TeamCode,
						CASE WHEN T1.MainTeam = 'TT2' THEN 'DP005'
							WHEN T1.MainTeam = 'MT1' THEN 'DP006'
							WHEN T1.MainTeam = 'MT2' THEN 'DP007'
							WHEN T1.MainTeam = 'OUL' THEN 'DP008'
						ELSE 'DP003' END AS 'DeptCode'
				 FROM approvecenter T0
				  	  LEFT JOIN teamcode T1 ON T0.TeamCode = T1.TeamCode
				 WHERE T0.ApproveCode = 'AppCR' AND (T0.TeamCode = '".$DataCus['TeamCode']."' OR T0.TeamCode = '0') AND StatusDoc = 'A' ".$AppLV."
				 ORDER BY T0.StepApprove" ;
		$getAppCR = MySQLSelectX($sql1);

		$ax=0;
		while ($AppCR = mysqli_fetch_array($getAppCR)){	
			$ax++;
			if ($AppCR['UserApp'] == '18'){
				switch($_SESSION['DeptCode']){
					case 'DP005':
					case 'DP006':
					case 'DP007':
					case 'DP008':
						$AppCR['UserApp'] = '18';
						break;
					default :
						$AppCR['UserApp'] = 'LV011';
						break;
				}
			}
			if ($AppCR['UserApp'] == 'LV038' AND $_SESSION ['DeptCode'] == 'DP008'){
				$AppCR['UserApp'] = 'LV051';
			}
			if (substr($AppCR['UserApp'],0,2) == 'LV'){
				$sql2 = "SELECT uKey,LineToken FROM users WHERE LvCode = '".$AppCR['UserApp']."' AND UserStatus = 'A'";
			}else{
				if (strlen($AppCR['UserApp']) == 32){
					$sql2 = "SELECT uKey,LineToken FROM users WHERE  uKey= '".$AppCR['UserApp']."' AND UserStatus = 'A'";
				}else{
					$sql2 = "SELECT T0.uKey,T0.LineToken 
							 FROM users T0
								   LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
							 WHERE  T1.uClass = '".$AppCR['UserApp']."' AND T1.DeptCode = '".$_SESSION['DeptCode']."' AND UserStatus = 'A'";
				}
	
			}
			if (CHKRowDB($sql2) > 1){
				$KeyApp = $AppCR['UserApp'];
			}else{
				$LineUser = MySQLSelect($sql2);
				//echo $sql2;
				$KeyApp = $LineUser['uKey'];
				$LineToken = $LineUser['LineToken'];
			}
			$sql3 = "SELECT ID FROM apporder WHERE UkeyReq = '".$KeyApp."' AND DocEntry = '".$DataCus['DocEntry']."'";
			if (CHKRowDB($sql3) == 0){
				if ($AppCR['TypeApp'] == 'E' && $CHKcr == 'Y'){
					$ConOpt = 'Y';
				}else{
					$ConOpt = 'N';
				}
				$sqlIN = "INSERT apporder SET DocEntry = ".$DataCus['DocEntry']." ,
										 LvApp = 3,
										 StepApprove = ".$AppCR['StepApprove'].",
										 AppCR = 1,
										 UkeyReq = '".$KeyApp."',
										 TypeApp = '".$AppCR['TypeApp']."',
										 ConditionOption = '".$ConOpt."'";
				MySQLInsert($sqlIN);
			}else{
				$AppID = MySQLSelect($sql3);
				$sqlUpdate = "UPDATE apporder SET AppCR = 1 WHERE ID = ".$AppID['ID'];
				MySQLUpdate($sqlUpdate);
			}
		}
	}

/*
เริมตรงนี้
	$chk = 0;
	if ($App['SO'] == '1'){
		$WH['Finish'] = "( T0.ApproveCode = 'AppSO' ";
		$chk = 1;
	}else{
		$WH['Finish'] = "(";
	}

	if ($App['GP'] == '1'){
		if ($chk == 1){
			$WH['Finish'] .= " OR T0.ApproveCode = 'AppGP' ";
		}else{
			$WH['Finish'] .= " T0.ApproveCode = 'AppGP' ";	
			$chk = 1;
		}
	}else{
		if ($App['GP'] == 'Mgr'){
			if ($chk == 1){
				$WH['Finish'] .= " OR T0.ApproveCode = 'MgrGP' ";	
			}else{
				$WH['Finish'] .= " T0.ApproveCode = 'MgrGP' ";	
				$chk = 1;
			}
		}
	}

	if ($App['CR'] == '1'){
		if ($chk == 1){
			$WH['Finish'] .= " OR T0.ApproveCode = 'AppCR' )";		
		}else{
			$WH['Finish'] .= " T0.ApproveCode = 'AppCR' )";		
		}

	}else{
		$WH['Finish'] .= ")";	
	}


	$sql1 = "SELECT T0.DocType,T0.LvApp,T0.ApproveCode AS AppCode,T0.UserApp,
					T0.StepApprove,T0.TypeApp,T0.ConditionOption,T0.TeamCode,
					CASE WHEN T1.MainTeam = 'TT2' THEN 'DP005'
			 			 WHEN T1.MainTeam = 'MT1' THEN 'DP006'
			 			 WHEN T1.MainTeam = 'MT2' THEN 'DP007'
						 WHEN T1.MainTeam = 'OUL' THEN 'DP008'
						 END AS 'DeptCode'
			 FROM approvecenter T0
  				  LEFT JOIN teamcode T1 ON T0.TeamCode = T1.TeamCode 
			 WHERE ".$WH['Finish'] ." 
			 ORDER BY T0.LvApp,T0.ApproveCode,T0.StepApprove   ";
	//echo "\r".$sql1;
	$getApprove = MySQLSelectX($sql1);
	$LineToken = "0";
	while ($AppList = mysqli_fetch_array($getApprove)){	
		if (substr($AppList['UserApp'],0,2) == 'LV'){
			$sql2 = "SELECT uKey,LineToken FROM users WHERE LvCode = '".$AppList['UserApp']."' AND UserStatus = 'A'";
		}else{
			if (strlen($AppList['UserApp']) == 32){
				$sql2 = "SELECT uKey,LineToken FROM users WHERE  uKey= '".$AppList['UserApp']."' AND UserStatus = 'A'";
			}else{
				$sql2 = "SELECT T0.uKey,T0.LineToken 
						 FROM users T0
						 	  LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
						 WHERE  T1.uClass = '".$AppList['UserApp']."' AND T1.DeptCode = '".$_SESSION['DeptCode']."' AND UserStatus = 'A'";
			}

		}
		if (CHKRowDB($sql2) > 1){
			$KeyApp = $AppList['UserApp'];
		}else{
			$LineUser = MySQLSelect($sql2);
			$KeyApp = $LineUser['uKey'];
			$LineToken = $LineUser['LineToken'];
		}

		$sql1 = "SELECT * FROM apporder WHERE DocEntry = '".$DataCus['DocEntry']."' AND UkeyReq = '".$KeyApp."' ";
		if (CHKRowDB($sql1) == 0){
			switch ($AppList['AppCode']){
				case 'AppSO' :
					$appSO = '1';
					$appGP = '0';
					$appCR = '0';
					break;
				case 'AppGP' :
				case 'MgrGP' :
					$appSO = '0';
					$appGP = '1';
					$appCR = '0';
					break;
				case 'AppCR' :
					$appSO = '0';
					$appGP = '0';
					$appCR = '1';
					break;
			}
			$sqlIN = "INSERT INTO apporder SET DocEntry = '".$DataCus['DocEntry']."',
			 								   LvApp = '".$AppList['LvApp']."',
											   AppSO = '".$appSO."',
											   AppCR = '".$appCR."',
											   AppGP = '".$appGP."',
											   UkeyReq = '".$KeyApp."',
											   TypeApp='".$AppList['TypeApp']."',
											   StepApprove = '".$AppList['StepApprove']."',
											   ConditionOption='".$AppList['ConditionOption']."'";
			MySQLInsert($sqlIN);
		}else{
			$sql3 = "SELECT ID FROM apporder WHERE DocEntry = '".$DataCus['DocEntry']."' AND UkeyReq = '".$KeyApp."' ";
			$UpdateApp = MySQLSelect($sql3);

			switch ($AppList['AppCode']){
				case 'AppSO' :
					MySQLUpdate("UPDATE apporder SET AppSO = '1' WHERE ID = ".$UpdateApp['ID']);
					break;
				case 'AppGP' :
				case 'MgrGP' :
					MySQLUpdate("UPDATE apporder SET AppGP = '1' WHERE ID = ".$UpdateApp['ID']);
					break;
				case 'AppCR' :
					MySQLUpdate("UPDATE apporder SET AppCR = '1' WHERE ID = ".$UpdateApp['ID']);
					break;
			}
		}
	}	

สุดสุดตรงนี้ **/  
	$sql4 = "SELECT T0.DocEntry,T3.DocNum,T6.CardCode,T6.CardName,T1.uKey,T1.LineToken,T2.DeptCode,T0.StepApprove,T0.LvApp,T0.AppSO,T0.AppGP,T0.AppCR,
					T3.SlpCode,CONCAT(T5.uName,' ',T5.uLastName,' (',T5.uNickName,')') AS SaleName 
			 FROM apporder T0
			      LEFT JOIN users T1 ON T0.UkeyReq = T1.uKey
				  LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode  	
				  LEFT JOIN order_header T3 ON T0.DocEntry = T3.DocEntry 
				  LEFT JOIN oslp T4 ON T3.SlpCode = T4.SlpCode
				  LEFT JOIN users T5 ON T5.uKey = T4.UKey
				  LEFT JOIN ocrd T6 ON T3.CardCode = T6.CardCode 
			 WHERE T0.DocEntry = '".$DataCus['DocEntry']."' AND (T0.AppSO = '1' OR T0.AppGP = '1' OR T0.AppCR = '1' ) AND  T0.StepApprove = 1 
			 ORDER BY  T0.LvApp,T0.StepApprove";	

	$getAppList = MySQLSelectX($sql4);	 
	//echo $sql4."\r";
	while ($LineList = mysqli_fetch_array($getAppList)){
		#LineUser($DeptCode,$user,$message);
		$message  = "อนุมัติใบสั่งขายเลขที่ : ".$LineList['DocNum']."\r";
		$message .= "ข้ออมูลลูกค้า : ".$LineList['CardCode']." ".$LineList['CardName']."\r";
		$message .= "พนักงานขาย : ".$LineList['SaleName']."\r";
		LineUser($LineList['DeptCode'],$LineList['LineToken'],$message);
		// LineNoti('Order',$message);

	}	
}

/* เขียนข้อมูลลง DataBase */
/*
A = ไม่อนุมัติ Finish / อนุมัติ Next Step 
B = อนุมัติ/ไม่อนุมัติ Next Step 
C = อนุมัติ Finish / ไม่อนุมัติ Next Step 
D = Finish
*/





?>
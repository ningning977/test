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

function ShowCHK($x){
	switch ($x){
		case 'Y' :
			return "<i class='fas fa-check fa-fw'></i>";
		break;
		case 'N' :
			return "<i class='fas fa-times fa-fw'></i>";
		break;
		default :
			return "-";
		break;
	}
}

// SelectPicker ข้อมูลลูกค้า
if($_GET['a'] == 'SeleteCardCode') {
	$sql = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE T0.CardType = 'C' AND (T0.CardCode != '' OR T0.CardName != '') AND T0.CardStatus = 'A' ORDER BY T0.CardCode";
	$sqlQRY = MySQLSelectX($sql);
	$option = "<option value='' selected disabled>ค้นหา...</option>";
	while($result = mysqli_fetch_array($sqlQRY)) {
		$option .= "<option value='".$result['CardCode']."'>".$result['CardCode']." - ".$result['CardName']."</option>";
	}
	$arrCol['option'] = $option;
}

// GET ข้อมูลลูกค้า
if($_GET['a'] == 'ChangeCardCode') {
	# PART 1 ข้อมูลลูกค้า
		$sqlP1 ="SELECT T0.[CardCode],T0.[CardName],T1.[GroupName],T0.[LicTradNum],
					T2.[SlpName], (T0.[MailAddres]+' '+T0.[MailZipCod]+' '+T0.MailBlock+' '+T0.MailCity) AS 'Block',T0.[Phone1],T0.[Phone2],T0.[Cellular],
					T4.[PymntGroup],T0.[CreditLine],T0.[Balance],T0.[U_ChqCond],T0.U_BillAdd,T5.Descr AS BillAddTxt,T0.U_SaveMoney,T6.Descr AS SaveTxt
				FROM OCRD T0
				LEFT JOIN OCRG T1 ON T0.[GroupCode] = T1.[GroupCode]
				LEFT JOIN OSLP T2 ON T0.[SlpCode] = T2.[SlpCode]
				LEFT JOIN OCTG T4 ON T0.[GroupNum] = T4.[GroupNum]
				LEFT JOIN UFD1 T5 ON T5.TableID = 'OCRD' AND T5.FieldID = 25 AND T5.FldValue = T0.U_BillAdd AND T0.U_BillAdd IS NOT NULL
				LEFT JOIN UFD1 T6 ON T6.TableID = 'OCRD' AND T6.FieldID = 26 AND T6.FldValue = T0.U_SaveMoney AND T0.U_BillAdd IS NOT NULL
				WHERE T0.[CardCode] = '".$_POST['CardCode']."'";
		$sqlP1QRY = SAPSelect($sqlP1);
		$resultP1 = odbc_fetch_array($sqlP1QRY);
		$TheadP1 ="<tr>";
			$TheadP1 .="<th class='pt-3'>รหัสลูกค้า</th>";
			$TheadP1 .="<td class='fw-bold pt-3'>".$resultP1['CardCode']."</td>";
			$TheadP1 .="<th class='pt-3'>ผู้แทนขาย</th>";
			if(isset($resultP1['SlpName'])) { 
				$TheadP1 .="<td class='fw-bold pt-3'>".conutf8($resultP1['SlpName'])."</td>"; 
			} else { 
				$TheadP1 .="<td class='fw-bold pt-3'>&nbsp;</td>"; 
			}
			$TheadP1 .="<th class='pt-3'>เครดิต</th>";
			if(isset($resultP1['PymntGroup'])) { 
				$TheadP1 .="<td class='fw-bold pt-3'>".conutf8($resultP1['PymntGroup'])."</td>"; 
			} else { 
				$TheadP1 .="<td class='fw-bold pt-3'>&nbsp;</td>"; 
			}
		$TheadP1 .="<tr>";
		$TheadP1 .="<tr>";
			$TheadP1 .="<th>ชื่อลูกค้า</th>";
			if(isset($resultP1['CardName'])) { 
				$TheadP1 .="<td class='fw-bold'>".conutf8($resultP1['CardName'])."</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
			$TheadP1 .="<th>รหัสประจำตัวผู้เสียภาษี</th>";
			if(isset($resultP1['LicTradNum'])) { 
				$TheadP1 .="<td class='fw-bold'>".$resultP1['LicTradNum']."</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
			$TheadP1 .="<th>วงเงินเครดิต</th>";
			if(isset($resultP1['CreditLine'])) { 
				$TheadP1 .="<td class='fw-bold'>".number_format($resultP1['CreditLine'],0)."</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
		$TheadP1 .="<tr>";
		$TheadP1 .="<tr>";
			$TheadP1 .="<th>กลุ่มลูกค้า</th>";
			if(isset($resultP1['GroupName'])) { 
				$TheadP1 .="<td class='fw-bold'>".conutf8($resultP1['GroupName'])."</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
			$TheadP1 .="<th>เงื่อนไขการชำระเงิน <a href='javascript:void(0);' onclick=\"ContentModal('Condition')\"><i class='fas fa-search-plus'></i></a></th>";
			if(isset($resultP1['U_ChqCond'])) { 
				$TheadP1 .="<td class='fw-bold'>".conutf8($resultP1['U_ChqCond'])."</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
			$TheadP1 .="<th>ยอดหนี้คงค้าง <a href='javascript:void(0);' onclick=\"ContentModal('".$resultP1['CardCode']."')\"><i class='fas fa-search-plus'></i></a></th>";
			if(isset($resultP1['Balance'])) { 
				$TheadP1 .="<td class='fw-bold'>".number_format($resultP1['Balance'],0)." บาท</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
		$TheadP1 .="<tr>";
		$TheadP1 .="<tr>";
			$TheadP1 .="<th>เบอร์โทรศัพท์</th>";
			if(isset($resultP1['Phone1']) || isset($resultP1['Phone2']) || isset($resultP1['Cellular'])) { 
				$Phone = "";
				if($resultP1['Phone1'] != "") { 
					$Phone .= conutf8($resultP1['Phone1']); $P1 = 1; 
				} else { 
					$P1 = 0; 
				}
				if($resultP1['Phone2'] != "") { 
					if ($P1 == 1) { 
						$Phone .= ", ".conutf8($resultP1['Phone2']); $P2 = 1; 
					}else{ 
						$Phone .= conutf8($resultP1['Phone2']); $P2 = 1; 
					} 
				} else { 
					$P2 = 0; 
				}
				if($resultP1['Cellular'] != "") { 
					if ($P1 == 1 || $P2 == 1) { 
						$Phone .= ", ".conutf8($resultP1['Cellular']); 
					}else{ 
						$Phone .= conutf8($resultP1['Cellular']); 
					} 
				}
				$TheadP1 .="<td class='fw-bold'>".$Phone."</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
			$sqlMap = "SELECT lat, lon, CardCode FROM OCRD WHERE CardCode = '".$_POST['CardCode']."' LIMIT 1";
			$resultMap = MySQLSelect($sqlMap);
			if ($resultMap['lat'] != "" && $resultMap['lon'] != ""){
				$TheadP1 .= "<th>ที่อยู่ <a href='https://www.google.com/maps/place/".$resultMap['lat'].",".$resultMap['lon']."' target='_blank'><i class='fas fa-map-marker-alt'></i></a></th>";
			}else{
				$TheadP1 .= "<th>ที่อยู่ <a href='javascript:void(0);' onclick=\"MapCardCode()\"><i class='fas fa-map-marker-alt'></i></a></th>";
			}
			if(isset($resultP1['Block'])) { 
				$TheadP1 .="<td colspan='2' class='fw-bold'>".conutf8($resultP1['Block'])."</td>"; 
			}else{ 
				$TheadP1 .="<td colspan='2' class='fw-bold'>&nbsp;</td>"; 
			}
		$TheadP1 .="<tr>";
		$TheadP1 .="<tr>";
			$TheadP1 .="<th>วิธีการวางบิล</th>";
			if(isset($resultP1['BillAddTxt'])) { 
				$TheadP1 .="<td class='fw-bold'>".conutf8($resultP1['BillAddTxt'])."</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
			$TheadP1 .="<th>วิธีการเก็บเงิน</th>";
			if(isset($resultP1['SaveTxt'])) { 
				$TheadP1 .="<td class='fw-bold'>".conutf8($resultP1['SaveTxt'])."</td>"; 
			}else{ 
				$TheadP1 .="<td class='fw-bold'>&nbsp;</td>"; 
			}
		$TheadP1 .="<tr>";
		$arrCol['TheadP1'] = $TheadP1;	
	# TAB 1 การเข้าพบลูกค้า
		$sqlT1 = "SELECT Q1, Q2, Q3, Q4, Q5, Q6, Q7 
			FROM route_survey
			WHERE CardCode = '".$_POST['CardCode']."' AND plan_month = MONTH(NOW()) AND plan_year = YEAR(NOW()) AND DocStatus = 'A' ORDER BY CreateDate";
		$resultT1 = MySQLSelect($sqlT1);
		if($resultT1 != null) {
			for($q = 1; $q <= 7; $q++) {
				switch($resultT1["Q{$q}"]) {
					case "Y": ${"QY{$q}"} = "true"; ${"QN{$q}"} = "false"; break;
					case "N": ${"QN{$q}"} = "true"; ${"QY{$q}"} = "false"; break; break;
					default: ${"QY{$q}"} = "false"; ${"QN{$q}"} = "false"; break;
				}
				$arrCol["QY{$q}"] = ${"QY{$q}"};	
				$arrCol["QN{$q}"] = ${"QN{$q}"};	
			}
		}
	# TAB 2 ยอดขายของร้านค้า
	$SQLP2 =
		"SELECT
			YEAR(B0.DocDate) AS 'DocYear', B0.CardCode, B1.CardName,
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 1 THEN B0.DocTotal  END),0) AS 'M_01_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 1 THEN B0.DocProfit END),0) AS 'M_01_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 2 THEN B0.DocTotal  END),0) AS 'M_02_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 2 THEN B0.DocProfit END),0) AS 'M_02_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 3 THEN B0.DocTotal  END),0) AS 'M_03_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 3 THEN B0.DocProfit END),0) AS 'M_03_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 4 THEN B0.DocTotal  END),0) AS 'M_04_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 4 THEN B0.DocProfit END),0) AS 'M_04_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 5 THEN B0.DocTotal  END),0) AS 'M_05_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 5 THEN B0.DocProfit END),0) AS 'M_05_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 6 THEN B0.DocTotal  END),0) AS 'M_06_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 6 THEN B0.DocProfit END),0) AS 'M_06_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 7 THEN B0.DocTotal  END),0) AS 'M_07_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 7 THEN B0.DocProfit END),0) AS 'M_07_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 8 THEN B0.DocTotal  END),0) AS 'M_08_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 8 THEN B0.DocProfit END),0) AS 'M_08_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 9 THEN B0.DocTotal  END),0) AS 'M_09_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 9 THEN B0.DocProfit END),0) AS 'M_09_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 10 THEN B0.DocTotal  END),0) AS 'M_10_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 10 THEN B0.DocProfit END),0) AS 'M_10_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 11 THEN B0.DocTotal  END),0) AS 'M_11_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 11 THEN B0.DocProfit END),0) AS 'M_11_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 12 THEN B0.DocTotal  END),0) AS 'M_12_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) = 12 THEN B0.DocProfit END),0) AS 'M_12_PRFT',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) IN (1,2,3,4,5,6,7,8,9,10,11,12) THEN B0.DocTotal  END),0) AS 'ALL_SALE',
			COALESCE(SUM(CASE WHEN MONTH(B0.DocDate) IN (1,2,3,4,5,6,7,8,9,10,11,12) THEN B0.DocProfit END),0) AS 'ALL_PRFT'
		FROM (
			SELECT
				A0.CardCode, A0.DocDate, SUM(A0.DocTotal-A0.VatSum) AS 'DocTotal', SUM(A0.GrosProfit) AS 'DocProfit'
			FROM OINV A0
			LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
			WHERE YEAR(A0.DocDate) BETWEEN YEAR(GETDATE())-1 AND YEAR(GETDATE()) AND A0.CANCELED = 'N'
			GROUP BY A0.CardCode, A0.DocDate
			UNION 
			SELECT
				A0.CardCode, A0.DocDate, -SUM(A0.DocTotal-A0.VatSum) AS 'DocTotal', -SUM(A0.GrosProfit) AS 'DocProfit'
			FROM ORIN A0
			LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
			WHERE YEAR(A0.DocDate) BETWEEN YEAR(GETDATE())-1 AND YEAR(GETDATE()) AND A0.CANCELED = 'N'
			GROUP BY A0.CardCode, A0.DocDate
		) B0
		LEFT JOIN OCRD B1 ON B0.CardCode = B1.CardCode
		WHERE B0.CardCode = '".$_POST['CardCode']."'
		GROUP BY YEAR(B0.DocDate), B0.CardCode, B1.CardName
		ORDER BY B0.CardCode";
	$QRYP2 = SAPSelect($SQLP2);

	$C_YEAR = date("Y");
	$P_YEAR = $C_YEAR-1;

	for($m = 1; $m <= 12; $m++) {
		$DATA[$C_YEAR][$m]['SALE'] = 0;
		$DATA[$C_YEAR][$m]['PRFT'] = 0;
		$DATA[$P_YEAR][$m]['SALE'] = 0;
		$DATA[$P_YEAR][$m]['PRFT'] = 0;
	}
	$DATA[$C_YEAR]['TRGT'] = 0;
	$DATA[$P_YEAR]['TRGT'] = 0;

	while($RSTP2 = odbc_fetch_array($QRYP2)) {
		for($m = 1; $m <= 12; $m++) {
			if($m < 10) {
				$SALE = $RSTP2['M_0'.$m.'_SALE'];
				$PRFT = $RSTP2['M_0'.$m.'_PRFT'];
			} else {
				$SALE = $RSTP2['M_'.$m.'_SALE'];
				$PRFT = $RSTP2['M_'.$m.'_PRFT'];
			}

			$DATA[$RSTP2['DocYear']][$m]['SALE'] = $SALE;
			$DATA[$RSTP2['DocYear']][$m]['PRFT'] = $PRFT;
		}
	}

	

	$TBODYP2 = "";
	$TFOOTP2 = "";

	$C_SUM_SALE = 0;
	$C_SUM_PRFT = 0;
	$P_SUM_SALE = 0;
	$P_SUM_PRFT = 0;

	for($m = 1; $m <= 12; $m++) {
		$C_PCNT = ($DATA[$C_YEAR][$m]['SALE'] <> 0) ? number_format(($DATA[$C_YEAR][$m]['PRFT']/$DATA[$C_YEAR][$m]['SALE'])*100,2) : "0.00" ;
		$P_PCNT = ($DATA[$P_YEAR][$m]['SALE'] <> 0) ? number_format(($DATA[$P_YEAR][$m]['PRFT']/$DATA[$P_YEAR][$m]['SALE'])*100,2) : "0.00" ;
		$TBODYP2 .= "<tr>";
			$TBODYP2 .= "<td>".FullMonth($m)."</td>";
			$TBODYP2 .= "<td class='text-right'>".number_format($DATA[$C_YEAR][$m]['SALE'],0)."</td>";
			$TBODYP2 .= "<td class='text-center'>$C_PCNT%</td>";
			$TBODYP2 .= "<td class='text-right'>".number_format($DATA[$P_YEAR][$m]['SALE'],0)."</td>";
			$TBODYP2 .= "<td class='text-center'>$P_PCNT%</td>";
		$TBODYP2 .= "</tr>";

		$C_SUM_SALE = $C_SUM_SALE + $DATA[$C_YEAR][$m]['SALE'];
		$C_SUM_PRFT = $C_SUM_PRFT + $DATA[$C_YEAR][$m]['PRFT'];

		$P_SUM_SALE = $P_SUM_SALE + $DATA[$P_YEAR][$m]['SALE'];
		$P_SUM_PRFT = $P_SUM_PRFT + $DATA[$P_YEAR][$m]['PRFT'];
	}

	/* ยอดรวมทั้งหมด */
	$C_PCNT_PRFT = ($C_SUM_SALE <> 0) ? ($C_SUM_PRFT / $C_SUM_SALE) * 100 : 0.00 ;
	$P_PCNT_PRFT = ($P_SUM_SALE <> 0) ? ($P_SUM_PRFT / $P_SUM_SALE) * 100 : 0.00 ;
	$TFOOTP2 .= "<tr class='table-active'>";
		$TFOOTP2 .= "<th>รวมทั้งหมด (<a href='javascript:void(0);' onclick='YearSales()'><i class='fas fa-search-plus'></i></a> ยอดขายย้อนหลัง 3 ปี)</th>";
		$TFOOTP2 .= "<th class='text-right'>".number_format($C_SUM_SALE,0)."</th>";
		$TFOOTP2 .= "<th class='text-center'>".number_format($C_PCNT_PRFT,2)."%</th>";
		$TFOOTP2 .= "<th class='text-right'>".number_format($P_SUM_SALE,0)."</th>";
		$TFOOTP2 .= "<th class='text-center'>".number_format($P_PCNT_PRFT,2)."%</th>";
	$TFOOTP2 .= "</tr>";

	/* เป้าขายร้านค้า */
	$SQLP2TAR = "SELECT T0.DocYear, T0.CusTarget FROM custarget T0 WHERE T0.CardCode = '".$_POST['CardCode']."' AND T0.DocYear BETWEEN YEAR(NOW())-1 AND YEAR(NOW()) AND T0.TgrStatus = 'A'";
	$QRYP2TAR = MySQLSelectX($SQLP2TAR);
	if($QRYP2TAR) {
		while($RSTP2TAR = mysqli_fetch_array($QRYP2TAR)) {
			$DATA[$RSTP2TAR['DocYear']]['TRGT'] = $RSTP2TAR['CusTarget'];
		}
	}
	$TFOOTP2 .= "<tr>";
		$TFOOTP2 .= "<th>เป้าขายร้านค้า</th>";
		$TFOOTP2 .= "<th class='text-right'>".number_format($DATA[$C_YEAR]['TRGT'],0)."</th>";
		$TFOOTP2 .= "<th class='text-center'>&nbsp;</th>";
		$TFOOTP2 .= "<th class='text-right'>".number_format($DATA[$P_YEAR]['TRGT'],0)."</th>";
		$TFOOTP2 .= "<th class='text-center'>&nbsp;</th>";
	$TFOOTP2 .= "</tr>";

	/* % ยอดขายต่อเป้าขาย */
	$C_PCNT_TRGT = ($DATA[$C_YEAR]['TRGT'] <> 0) ? ($C_SUM_SALE / $DATA[$C_YEAR]['TRGT']) * 100 : 0 ;
	$P_PCNT_TRGT = ($DATA[$P_YEAR]['TRGT'] <> 0) ? ($P_SUM_SALE / $DATA[$P_YEAR]['TRGT']) * 100 : 0 ;
	$TFOOTP2 .= "<tr class='table-success'>";
		$TFOOTP2 .= "<th>% ยอดขายต่อเป้าขาย</th>";
		$TFOOTP2 .= "<th class='text-center'>".number_format($C_PCNT_TRGT,2)."%</th>";
		$TFOOTP2 .= "<th class='text-center'>&nbsp;</th>";
		$TFOOTP2 .= "<th class='text-center'>".number_format($P_PCNT_TRGT,2)."%</th>";
		$TFOOTP2 .= "<th class='text-center'>&nbsp;</th>";
	$TFOOTP2 .= "</tr>";


	$arrCol['TbodyP2'] = $TBODYP2;
	$arrCol['TfootP2'] = $TFOOTP2;
	
	
}

// TAB 1
if($_GET['a'] == 'AddQ') {
	$CardCode = $_POST['CardCode'];
	$Qtion = $_POST['Qtion'];
	$QtionValue = $_POST['QtionValue'];
	$sql = "SELECT * FROM route_survey WHERE CardCode = '".$CardCode."' AND plan_month = MONTH(NOW()) AND plan_year = YEAR(NOW()) AND DocStatus = 'A'";
	$sqlQRY = MySQLSelectX($sql);
	for($q = 1; $q <= 7; $q++) { 
		${"Q{$q}"} = null; 
	}
	$q = 0;
	while($result = mysqli_fetch_array($sqlQRY)) {
		$ID = $result["SurveyID"];
		for($q = 1; $q <= 7; $q++) { 
			${"Q{$q}"} = $result["Q{$q}"]; 
		}
		$q++;
	}
	switch ($Qtion){
		case 'Q1' : $Q1 = $QtionValue; break;
		case 'Q2' : $Q2 = $QtionValue; break;
		case 'Q3' : $Q3 = $QtionValue; break;
		case 'Q4' : $Q4 = $QtionValue; break;
		case 'Q5' : $Q5 = $QtionValue; break;
		case 'Q6' : $Q6 = $QtionValue; break;
		case 'Q7' : $Q7 = $QtionValue; break;
   	}

   	if ($q != 0){
		MySQLUpdate("UPDATE route_survey SET DocStatus = 'I', UpdateUkey = '".$_SESSION['ukey']."' WHERE SurveyID = '".$ID."'");
	}
	$Insert = "INSERT INTO route_survey 
					SET CardCode = '".$CardCode."',
						Q1 = '".$Q1."',
						Q2 = '".$Q2."',
						Q3 = '".$Q3."',
						Q4 = '".$Q4."',
						Q5 = '".$Q5."',
						Q6 = '".$Q6."',
						Q7 = '".$Q7."',
						plan_year = YEAR(NOW()),
						plan_month = MONTH(NOW()),
						CreateUkey = '".$_SESSION['ukey']."',
						CreateDate = NOW()";
	MySQLInsert($Insert);

}
if($_GET['a'] == 'Debt'){
	$sql = "SELECT X0.* 
			 FROM (SELECT T0.[NumAtCard], T0.[DocDate], T0.CardCode, T0.[DocDueDate], T0.[DocTotal],(T0.[DocTotal] - T0.[PaidToDate]) AS Balance, T0.[DocNum], T1.[Beginstr],T0.[DocStatus],T0.ReceiptNum,T2.DocDate AS PaidDate,
						  CASE WHEN T0.ReceiptNum IS NULL OR T0.ReceiptNum = '' THEN DATEDIFF(DAY,GETDATE(),T0.DocDueDate) ELSE  DATEDIFF(DAY,T2.DocDate,T0.DocDueDate) END AS Diff
				   FROM OINV T0 
						LEFT JOIN NNM1 T1 ON T0.Series = T1.Series 
						LEFT JOIN ORCT T2 ON T0.ReceiptNum = T2.DocNum 
				   WHERE (T0.CardCode = '".$_POST['CardCode']."' ) AND T0.[CANCELED] = 'N' AND T0.DocStatus = 'O' ) X0
			 ORDER BY X0.Diff,X0.DocDueDate,X0.DocDate";
 	$sqlQRY = SAPSelect($sql);
	$Tbody = "";
	while ($result = odbc_fetch_array($sqlQRY)){
		if ($result['Diff'] < -30) {
			$class = " class='table-danger' ";
			$result['Diff'] = $result['Diff'] -1;
		}else{
			$class = "";
		}
		$Tbody .="<tr ".$class.">
					<td class='text-center'>".$result['NumAtCard']."</td>
					<td class='text-center'>".date("d/m/Y",strtotime($result['DocDate']))."</td>
					<td class='text-center'>".date("d/m/Y",strtotime($result['DocDueDate']))."</td>
					<td class='text-right'>".number_format($result['DocTotal'],2)."</td>
					<td class='text-right'>".number_format($result['Balance'],2)."</td>
					<td class='text-right'>".number_format($result['Diff'])."</td>
				</tr>";
	}
	$arrCol['Tbody'] = $Tbody;
}

if($_GET['a'] == 'YearSales') {
	$pYear = date("Y")-3;
	$sql = "SELECT P0.YearData,SUM(P0.DocTotal) AS DocTotal
			FROM (SELECT YEAR(T0.DocDate)AS YearData,(T0.DocTotal-T0.VatSum) AS DocTotal  
				FROM OINV T0
				WHERE YEAR(T0.DocDate) >= ".$pYear."  AND T0.CardCode = '".$_POST['CardCode']."' AND T0.CANCELED = 'N'
				UNION ALL
				SELECT YEAR(T0.DocDate)AS YearData,-1*(T0.DocTotal-T0.VatSum) AS DocTotal  
				FROM ORIN T0
				WHERE YEAR(T0.DocDate) >= ".$pYear." AND T0.CardCode = '".$_POST['CardCode']."' AND T0.CANCELED = 'N') P0
			GROUP BY   P0.YearData
			ORDER BY P0.YearData DESC";
	$sqlQRY = SAPSelect($sql); /* EDIT Y */
	$TextBox = "";
	if($pYear <= 2022) {
		while ($result = odbc_fetch_array($sqlQRY)){
			if($result['YearData'] >= 2023) {
				$TextBox .= "ปี ".$result['YearData']." ยอดขาย ".number_format($result['DocTotal'],0)." บาท<br>";
			}
		}

		$sqlQRY_conSAP8 = conSAP8($sql);
		while ($result_conSAP8 = odbc_fetch_array($sqlQRY_conSAP8)){
			if($result_conSAP8['YearData'] <= 2022) {
				$TextBox .= "ปี ".$result_conSAP8['YearData']." ยอดขาย ".number_format($result_conSAP8['DocTotal'],0)." บาท<br>";
			}
		}
	}else{
		while ($result = odbc_fetch_array($sqlQRY)){
			$TextBox .= "ปี ".$result['YearData']." ยอดขาย ".number_format($result['DocTotal'],0)." บาท<br>";
		}
	}
	$arrCol['TextBox'] = $TextBox;
}
if($_GET['a'] == 'MeetingPlan') {
	if ($_POST['Year'] < date("Y")){
		$readonly = "readonly";
	}else{
		$readonly = "";
	}
	$Tbody = "";
	switch($_POST['Ti']) {
		case 'Ti1':
			for($m = 1; $m <= 4; $m++) {
				$sqlTi1 =  "SELECT DetailPlan, DetailActual
							FROM route_action 
							WHERE CardCode = '".$_POST['CardCode']."' AND plan_month = '".$m."' AND plan_year = '".$_POST['Year']."' ORDER BY CreateDate DESC LIMIT 1";
				$resultTi1 = MySQLSelect($sqlTi1);		
				$DetailPlan = "";
				$DetailActual = "";
				if(isset($resultTi1['DetailPlan']))   { $DetailPlan = $resultTi1['DetailPlan']; }
				if(isset($resultTi1['DetailActual'])) { $DetailActual = $resultTi1['DetailActual']; }
				$Tbody .= "<tr>".
								"<td class='text-center fw-bold text-primary'><input type='hidden' name='Mp_m".$m."' id='Mp_m".$m."' value='".$m."'>".FullMonth($m)."</td>".
								"<td>".
									"<div class='form-floating'>".
										"<textarea class='form-control' placeholder='เขียนแผนการทำงานที่นี่' id='MpP_m".$m."' style='height: 100px' onfocusout=\"AddPlan('MpP_m','".$m."')\" ".$readonly.">".$DetailPlan."</textarea>".
										"<label for='MpP_m".$m."'>แผนการดำเนินงาน</label>".
									"</div>".
								"</td>".
								"<td>".
									"<div class='form-floating'>".
										"<textarea class='form-control' placeholder='เขียนแผนการทำงานที่นี่' id='MpR_m".$m."' style='height: 100px' onfocusout=\"AddPlan('MpR_m','".$m."')\" ".$readonly.">".$DetailActual."</textarea>".
										"<label for='MpR_m".$m."'>ผลการดำเนินงาน</label>".
									"</div>".
								"</td>".
						  "</tr>";
			}
			break;
		case 'Ti2':
			for($m = 5; $m <= 8; $m++) {
				$sqlTi1 =  "SELECT DetailPlan, DetailActual
							FROM route_action 
							WHERE CardCode = '".$_POST['CardCode']."' AND plan_month = '".$m."' AND plan_year = '".$_POST['Year']."' ORDER BY CreateDate DESC LIMIT 1";
				$resultTi1 = MySQLSelect($sqlTi1);		
				$DetailPlan = "";
				$DetailActual = "";
				if(isset($resultTi1['DetailPlan']))   { $DetailPlan = $resultTi1['DetailPlan']; }
				if(isset($resultTi1['DetailActual'])) { $DetailActual = $resultTi1['DetailActual']; }
				$Tbody .= "<tr>".
								"<td class='text-center fw-bold text-primary'><input type='hidden' name='Mp_m".$m."' id='Mp_m".$m."' value='".$m."'>".FullMonth($m)."</td>".
								"<td>".
									"<div class='form-floating'>".
										"<textarea class='form-control' placeholder='เขียนแผนการทำงานที่นี่' id='MpP_m".$m."' style='height: 100px' onfocusout=\"AddPlan('MpP_m','".$m."')\" ".$readonly.">".$DetailPlan."</textarea>".
										"<label for='MpP_m".$m."'>แผนการดำเนินงาน</label>".
									"</div>".
								"</td>".
								"<td>".
									"<div class='form-floating'>".
										"<textarea class='form-control' placeholder='เขียนแผนการทำงานที่นี่' id='MpR_m".$m."' style='height: 100px' onfocusout=\"AddPlan('MpR_m','".$m."')\" ".$readonly.">".$DetailActual."</textarea>".
										"<label for='MpR_m".$m."'>ผลการดำเนินงาน</label>".
									"</div>".
								"</td>".
						  "</tr>";
			}
			break;
		case 'Ti3':
			for($m = 9; $m <= 12; $m++) {
				$sqlTi1 =  "SELECT DetailPlan, DetailActual
							FROM route_action 
							WHERE CardCode = '".$_POST['CardCode']."' AND plan_month = '".$m."' AND plan_year = '".$_POST['Year']."' ORDER BY CreateDate DESC LIMIT 1";
				$resultTi1 = MySQLSelect($sqlTi1);		
				$DetailPlan = "";
				$DetailActual = "";
				if(isset($resultTi1['DetailPlan']))   { $DetailPlan = $resultTi1['DetailPlan']; }
				if(isset($resultTi1['DetailActual'])) { $DetailActual = $resultTi1['DetailActual']; }
				$Tbody .= "<tr>".
								"<td class='text-center fw-bold text-primary'><input type='hidden' name='Mp_m".$m."' id='Mp_m".$m."' value='".$m."'>".FullMonth($m)."</td>".
								"<td>".
									"<div class='form-floating'>".
										"<textarea class='form-control' placeholder='เขียนแผนการทำงานที่นี่' id='MpP_m".$m."' style='height: 100px' onfocusout=\"AddPlan('MpP_m','".$m."')\" ".$readonly.">".$DetailPlan."</textarea>".
										"<label for='MpP_m".$m."'>แผนการดำเนินงาน</label>".
									"</div>".
								"</td>".
								"<td>".
									"<div class='form-floating'>".
										"<textarea class='form-control' placeholder='เขียนแผนการทำงานที่นี่' id='MpR_m".$m."' style='height: 100px' onfocusout=\"AddPlan('MpR_m','".$m."')\" ".$readonly.">".$DetailActual."</textarea>".
										"<label for='MpR_m".$m."'>ผลการดำเนินงาน</label>".
									"</div>".
								"</td>".
						  "</tr>";
			}
			break;
	}
	$arrCol['Tbody'] = $Tbody;
}
if($_GET['a'] == 'AddPlan') {
	$DetailPlan = "";
	$DetailActual = "";
	if($_POST['Mp'] == 'MpP_m') {
		$DetailPlan = $_POST['Comments'];
	}else{
		$DetailActual = $_POST['Comments'];
	}
	$sqlCk = "SELECT * FROM route_action WHERE CardCode = '".$_POST['CardCode']."' AND plan_month = '".$_POST['Month']."' AND plan_year = '".$_POST['Year']."'";
	$Ck = CHKRowDB($sqlCk);
	if($Ck == 0) {
		$Insert = "INSERT INTO route_action 
						SET CardCode = '".$_POST['CardCode']."',
							DetailPlan = '".$DetailPlan."',
							DetailActual = '".$DetailActual."',
							plan_year = '".$_POST['Year']."',
							plan_month = '".$_POST['Month']."',
							CreateUkey = '".$_SESSION['ukey']."',
							CreateDate = NOW()";
		$Insert = MySQLInsert($Insert);
	}else{
		$sql = "SELECT * FROM route_action WHERE CardCode = '".$_POST['CardCode']."' AND plan_month = '".$_POST['Month']."' AND plan_year = '".$_POST['Year']."' AND DocStatus = 'A' ORDER BY CreateDate DESC";
		$result = MySQLSelect($sql);
		MySQLUpdate("UPDATE route_action SET DocStatus = 'I', UpdateUkey = '".$_SESSION['ukey']."' WHERE SurveyID = '".$result['SurveyID']."'");
		if($_POST['Mp'] == 'MpP_m') {
			$DetailPlan = $_POST['Comments'];
			$DetailActual = $result['DetailActual'];
		}else{
			$DetailPlan = $result['DetailPlan'];
			$DetailActual = $_POST['Comments'];
		}
		$Insert = "INSERT INTO route_action 
						SET CardCode = '".$_POST['CardCode']."',
							DetailPlan = '".$DetailPlan."',
							DetailActual = '".$DetailActual."',
							plan_year = '".$_POST['Year']."',
							plan_month = '".$_POST['Month']."',
							CreateUkey = '".$_SESSION['ukey']."',
							CreateDate = NOW()";
		MySQLInsert($Insert);
	}
}

if($_GET['a'] == 'CheckList') {
	$CardCode = $_POST['CardCode'];
	$CLYear = $_POST['CLYear'];

	$H[1] = "1. สินค้าถูกโชว์เรียง และสะอาดสวยงาม";
	$H[2] = "2. มี Shelf Talker หรือป้ายราคาเพื่อทำ Sales";
	$H[3] = "3. มี Shelf หรือ Display";
	$H[4] = "4. มี PC หรือ มือปืน";
	$H[5] = "5. ได้สอบถาม PC ในเรื่องปัญหาสินค้าภายในร้านค้าแล้วหรือไม่?";
	$H[6] = "6. นับสต๊อคเพื่อเติมสินค้าที่ขาด";
	$H[7] = "7. ส่งสำรวจราคาคู่แข่งใน LINE กลุ่ม";

	$sql = "SELECT plan_month, Q1, Q2, Q3, Q4, Q5, Q6, Q7 
			FROM route_survey
			WHERE CardCode = '".$CardCode."' AND plan_year = '".$CLYear."' AND DocStatus = 'A' ORDER BY plan_month, CreateDate";
	$sqlQRY = MySQLSelectX($sql);
	for($Q=1; $Q<=7; $Q++){
		for($m=1; $m<=12; $m++){
			$CHK[$Q][$m] = "-";
		}
   }
	while($result = mysqli_fetch_array($sqlQRY)) {
		$CHK[1][$result['plan_month']] = $result['Q1'];
		$CHK[2][$result['plan_month']] = $result['Q2'];
		$CHK[3][$result['plan_month']] = $result['Q3'];
		$CHK[4][$result['plan_month']] = $result['Q4'];
		$CHK[5][$result['plan_month']] = $result['Q5'];
		$CHK[6][$result['plan_month']] = $result['Q6'];
		$CHK[7][$result['plan_month']] = $result['Q7'];
	}
	$Tbody = "";
	for($i = 1; $i <= 7; $i++) {
		$Tbody .="<tr>
						<td>".$H[$i]."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][1])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][2])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][3])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][4])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][5])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][6])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][7])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][8])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][9])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][10])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][11])."</td>
						<td class='text-center'>".ShowCHK($CHK[$i][12])."</td>
				</tr>";
	} 
	$arrCol['Tbody'] = $Tbody;
}


if($_GET['a'] == 'SelectItemCode') {
	$sql = "SELECT DIStinct(T0.[ItemCode]),T1.[ItemName],T1.[CodeBars],T1.[U_ProductStatus]  
			FROM OITW T0 LEFT JOIN OITM T1 ON T0.[ItemCode] = T1.[ItemCode] 
			WHERE T1.[InvntItem] = 'Y' AND T1.ValidFor = 'Y'ORDER BY T0.[ItemCode]";
	$sqlQRY = SAPSelect($sql);
	$option = "<option value='' selected disabled>กรุณาเลือกชื่อสินค้า</option>";
	while($result = odbc_fetch_array($sqlQRY)) {
		$option .= "<option value='".$result['ItemCode']."'>".$result['ItemCode']." - ".conutf8($result['ItemName'])."</option>";
	}
	$arrCol['option'] = $option;
}
if($_GET['a'] == 'HisProduct'){
	# TAB 3 ประวัติสินค้า
	$sqlT3 =" SELECT A0.*
	FROM (
	SELECT TOP 10 ISNULL(T2.BeginStr,'IV-')+CAST(T1.DocNum AS VARCHAR) AS 'DocNum', T0.DocDate,T0.ITemCode,T0.Dscription,T0.WhsCode,T0.Quantity,T0.Price,T0.LineTotal,T0.VatSum
	FROM INV1  T0
		LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
	WHERE ItemCode = '".$_POST['ItemCode']."' AND T1.CardCode = '".$_POST['CardCode']."'
	UNION ALL 
	SELECT TOP 10 ISNULL(T2.BeginStr,'')+CAST(T1.DocNum AS VARCHAR) AS 'DocNum', T0.DocDate,T0.ITemCode,T0.Dscription,T0.WhsCode,T0.Quantity,T0.Price,T0.LineTotal,T0.VatSum
	FROM DLN1  T0
		LEFT JOIN ODLN T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
	WHERE ItemCode = '".$_POST['ItemCode']."' AND T1.CardCode = '".$_POST['CardCode']."') A0
	ORDER BY A0.DocDate
	";
	//echo $sqlT3;

	$sqlT3QRY = conSAP8($sqlT3); /* ใส่หมายเหตุด้านล่างตารางว่า "ข้อมูลตั้งแต่ปี 2023" */
	$TbodyT3 = "";
	$RowT3 = 0;
	while ($resultT3 = odbc_fetch_array($sqlT3QRY)) {
		$TbodyT3 .= "<tr>
						<td class='text-center'>".$resultT3['DocNum']."</td>
						<td class='text-center'>".date("d/m/Y",strtotime($resultT3['DocDate']))."</td>
						<td class='text-center'>".$resultT3['ITemCode']."</td>
						<td>".conutf8($resultT3['Dscription'])."</td>
						<td class='text-center'>".$resultT3['WhsCode']."</td>
						<td class='text-center'>".number_format($resultT3['Quantity'])."</td>
						<td class='text-center'>".number_format($resultT3['Price'],2)."</td>
						<td class='text-center'>".number_format($resultT3['VatSum'],2)."</td>
						<td class='text-center fw-bolder'>".number_format($resultT3['LineTotal']+$resultT3['VatSum'],2)."</td>
					</tr>";
		$RowT3++;
	}
	$rrT3 = 10-$RowT3;
	for($r = 1; $r <= $rrT3; $r++) {
	$TbodyT3 .=	"<tr>
					<td class='text-center'>&nbsp;</td>
					<td class='text-center'>&nbsp;</td>
					<td class='text-center'>&nbsp;</td>
					<td>&nbsp;</td>
					<td class='text-center'>&nbsp;</td>
					<td class='text-center'>&nbsp;</td>
					<td class='text-center'>&nbsp;</td>
					<td class='text-center'>&nbsp;</td>
					<td class='text-center fw-bolder'>&nbsp;</td>
				</tr>";
	}
	$arrCol['TbodyT3'] = $TbodyT3;
}
if($_GET['a'] == 'CallStock') {
	switch ($_SESSION['DeptCode']) {
		case 'DP005':
			 $Dept = " OR T0.WhsCode IN ('TT')  OR T0.WhsCode LIKE 'WB%' OR T0.WhsCode LIKE 'WC%' OR T0.WhsCode LIKE 'WD%' OR T0.WhsCode LIKE 'WK%' ";
			 break;
		case 'DP006':
			 $Dept = " OR T0.WhsCode IN ('WM1') ";
			 break;
		case 'DP007':
			 $Dept = " OR T0.WhsCode IN ('WM2') ";
			 break;
		case 'DP008':
			 $Dept = " OR T0.WhsCode IN ('KB7','WA26.1') ";
			 break;
		default:
			 $Dept = "  ";
			 break;
   }
   $sql = "SELECT P0.NewWhs AS WhsCode,SUM(P0.OnHand) AS OnHand,
					CASE WHEN P0.NewWhs = 'KSY' THEN 1
						WHEN P0.NewWhs IN ('MT','MT2','TT-C','OUL','KB1') THEN 2
						WHEN P0.NewWhs = 'KBI-2' THEN 3
						WHEN P0.NewWhs IN ('WM1','WM2','TT-2','OUL-2') THEN 4
						ELSE 5 END AS LineNum 
			FROM (SELECT T0.WhsCode,T0.OnHand,
						CASE WHEN T0.WhsCode IN ('KB5','KB5.1','KB6','KB6.1') THEN 'KBI-2'
							WHEN T0.WhsCode IN ('KB7','WA26.1') THEN 'OUL-2'
							WHEN T0.WhsCode = 'TT' OR T0.WhsCode LIKE 'WB%' OR T0.WhsCode LIKE 'WC%' OR T0.WhsCode LIKE 'WD%' OR T0.WhsCode LIKE 'WK%' THEN 'TT-2'
							WHEN T0.WhsCode IN ('KSM','KSY') THEN 'KSY'
							WHEN T0.WhsCode IN ('AGT','JSI','KN','KS','OSP','RTR','IMAX','TC','PU','VRK','YEE','NST','PLA','SY') THEN 'SUB' 
							ELSE T0.WhsCode END AS 'NewWhs' 
				FROM OITW T0
				WHERE T0.ItemCode = '".$_POST['ItemCode']."' AND T0.OnHand > 0 AND (T0.WhsCode IN ('KSM','KSY','MT','MT2','TT-C','OUL','KB1') ".$Dept."   OR T0.WhsCode IN ('AGT','JSI','KN','KS','OSP','RTR','IMAX','TC','PU','VRK','YEE','NST','PLA','SY'))
				) P0
			GROUP BY P0.NewWhs
			ORDER BY LineNum";
	$sqlQRY = SAPSelect($sql);
	$Tbody = "";
	while ($result = odbc_fetch_array($sqlQRY)) {
		switch ($result['WhsCode']){
			case 'KSY' : $result['WhsCode'] = "KSY"; break;
			case 'KBI-2' : $result['WhsCode'] = "คลังมือ 2 ส่วนกลาง"; break;
			case 'WM1' :
			case 'WM2' :
			case 'TT-2' :
			case 'OUL-2' : $result['WhsCode'] = "คลังมือ 2 "; break;
			case 'SUB' : $result['WhsCode'] = "คลังซัพฯ"; break;
			default; $result['WhsCode'] = $result['WhsCode']; break;
	   }
	   $Tbody .="<tr> ".
	   				"<td class='text-center'>".$result['WhsCode']."</td>".
					"<td class='text-right'>".number_format($result['OnHand'])."</td>".
				"</tr>";
	}
	$arrCol['Tbody'] = $Tbody;
}
if($_GET['a'] == 'HisItem') {
	$sql = "
		SELECT P0.*
		FROM (
			SELECT TOP 10 OINV.[DocDate],ISNULL(NNM1.[BeginStr],'IV-')+CAST(OINV.[DocNum] AS VARCHAR) AS 'DocNum', 
				OINV.[DocEntry],OSLP.[SlpName],(OINV.[DocTotal]-OINV.[VatSum]) AS 'DOC_TOTAL', OINV.CANCELED 
			FROM OINV
			LEFT JOIN NNM1 ON OINV.[Series] = NNM1.[Series]
			JOIN OSLP ON OINV.[SlpCode] = OSLP.[SlpCode]
			WHERE OINV.[CardCode] = '".$_POST['CardCode']."'
			UNION ALL
			SELECT TOP 10 ORIN.[DocDate],ISNULL(NNM1.[BeginStr],'IV-')+CAST(ORIN.[DocNum] AS VARCHAR) AS 'DocNum', 
				ORIN.[DocEntry],OSLP.[SlpName],-1*(ORIN.[DocTotal]-ORIN.[VatSum]) AS 'DOC_TOTAL', ORIN.CANCELED 
			FROM ORIN
			LEFT JOIN NNM1 ON ORIN.[Series] = NNM1.[Series]
			JOIN OSLP ON ORIN.[SlpCode] = OSLP.[SlpCode]
			WHERE ORIN.[CardCode] = '".$_POST['CardCode']."'
		) P0
		ORDER BY P0.DocDate DESC";
	$sqlQRY = SAPSelect($sql);
	$Tbody = "";
	$Row = 0;
	while ($result = odbc_fetch_array($sqlQRY)) {
		$Tbody .= 	"<tr>".
						"<td class='text-center'><a href='javascript:void(0);' class='DataHisItem' data-hisitem='".$result['DocEntry']."'>".$result['DocNum']."</a></td>".
						"<td class='text-center'>".date("d/m/Y",strtotime($result['DocDate']))."</td>".
						"<td>".conutf8($result['SlpName'])."</td>".
						"<td class='text-right'>".number_format($result['DOC_TOTAL'],2)."</td>".
					"</tr>";
		$Row++;
	}
	$rr = 10-$Row;
	for($r = 1; $r <= $rr; $r++) {
	$Tbody .=	"<tr>".
					"<td class='text-center'><a href='javascript:void(0);'>&nbsp;</a></td>".
					"<td class='text-center'>&nbsp;</td>".
					"<td>&nbsp;</td>".
					"<td class='text-right'>&nbsp;</td>".
				"</tr>";
	}
	$arrCol['Tbody'] = $Tbody;
}
if($_GET['a'] == 'DataHisItem') {
	/* HEADER */
	$HeaderSQL ="SELECT TOP 1
					(ISNULL(T1.BeginStr,'IV-')+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum',
					(T0.CardCode+' | '+T0.CardName) AS 'CardCode',
					T0.LicTradNum, T0.DocDate, T0.DocDueDate, T2.SlpName,
					T0.PaytoCode AS 'BilltoCode', T0.Address AS 'AddressBillto', T0.ShiptoCode, T0.Address2 AS 'AddressShipto',
					T0.U_ShippingType AS 'ShippingType', T0.Comments
				FROM OINV T0
				LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
				LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
				WHERE T0.DocEntry = ".$_POST['DocEntry']."";
	$qryHeaderSQL = SAPSelect($HeaderSQL);
	$HeaderRST = odbc_fetch_array($qryHeaderSQL);
	if(isset($HeaderRST['LicTradNum'])) { 
		$LicTradNum = $HeaderRST['LicTradNum']; 
	} else { 
		$LicTradNum = ""; 
	}
	if(isset($HeaderRST['DocDate'])) { 
		$DocDate = date("d/m/Y",strtotime($HeaderRST['DocDate'])); 
	} else { 
		$DocDate = ""; 
	}
	if(isset($HeaderRST['DocDueDate'])) { 
		$DocDueDate = date("d/m/Y",strtotime($HeaderRST['DocDueDate'])); 
	} else { 
		$DocDueDate = ""; 
	}
	if(isset($HeaderRST['SlpName'])) { 
		$SlpName = conutf8($HeaderRST['SlpName']); 
	} else { 
		$SlpName = ""; 
	}

	$arrCol['view_DocNum']       = $HeaderRST['DocNum'];
	$arrCol['view_CardCode']     = conutf8($HeaderRST['CardCode']);
	$arrCol['view_LicTradeNum']  = $LicTradNum;
	$arrCol['view_DocDate']      = $DocDate;
	$arrCol['view_DocDueDate']   = $DocDueDate;
	$arrCol['view_SlpCode']      = $SlpName;

	/* ITEM */
	$ItemListSQL = "SELECT
						T0.ItemCode, T0.CodeBars, T0.Dscription AS 'ItemName', T0.WhsCode, T0.Quantity, T0.UnitMsr, T0.PriceBefDi AS 'GrandPrice', 
						T0.U_DiscP1 AS 'Line_Disc1', T0.U_DiscP2 AS 'Line_Disc2', T0.U_DiscP3 AS 'Line_Disc3', T0.U_DiscP4 AS 'Line_Disc4',
						T0.Price AS 'UnitPrice', (T0.PriceAfVat-T0.Price) AS 'UnitVat', T0.LineTotal, T0.VatSum AS 'LineVatSum'
					FROM INV1 T0
					WHERE T0.DocEntry = ".$_POST['DocEntry']."";
	$ItemList = "";
	$no = 1;
	$Discount = null;
	$SUMLineTotal = 0;
	$qryItemListSQL = SAPSelect($ItemListSQL);
	while($ItemListRST = odbc_fetch_array($qryItemListSQL)) {
		if(isset($ItemListRST['ItemName'])) {
			$NameLen = mb_strlen(conutf8($ItemListRST['ItemName']),'UTF-8');
			if($NameLen <= 32) {
				$ItemName = conutf8($ItemListRST['ItemName']);
			} else {
				$ItemName = iconv_substr(conutf8($ItemListRST['ItemName']),0,32,'UTF-8')."...";
			}
		}
		
		if(isset($ItemListRST['Line_Disc4'])) {
			if($ItemListRST['Line_Disc4'] != NULL AND $ItemListRST['Line_Disc4'] != "" AND $ItemListRST['Line_Disc4'] != 0.00) {
				$Discount = number_format($ItemListRST['Line_Disc1'],1)."%+".number_format($ItemListRST['Line_Disc2'],1)."%+".number_format($ItemListRST['Line_Disc3'],1)."%+".number_format($ItemListRST['Line_Disc4'],1)."%";
			} 
		}	
		if(isset($ItemListRST['Line_Disc3'])) {
			if($ItemListRST['Line_Disc3'] != NULL AND $ItemListRST['Line_Disc3'] != "" AND $ItemListRST['Line_Disc3'] != 0.00) {
				$Discount = number_format($ItemListRST['Line_Disc1'],1)."%+".number_format($ItemListRST['Line_Disc2'],1)."%+".number_format($ItemListRST['Line_Disc3'],1)."%";
			} 
		}	
		if(isset($ItemListRST['Line_Disc2'])) {
			if($ItemListRST['Line_Disc2'] != NULL AND $ItemListRST['Line_Disc2'] != "" AND $ItemListRST['Line_Disc2'] != 0.00) {
				$Discount = number_format($ItemListRST['Line_Disc1'],1)."%+".number_format($ItemListRST['Line_Disc2'],1)."%";
			} 
		}	
		if(isset($ItemListRST['Line_Disc1'])) {
			if($ItemListRST['Line_Disc1'] != NULL AND $ItemListRST['Line_Disc1'] != "" AND $ItemListRST['Line_Disc1'] != 0.00) {
				$Discount = number_format($ItemListRST['Line_Disc1'],1)."%";
			}
		}

		if(isset($ItemListRST['LineTotal'])) {
			$SUMLineTotal = $SUMLineTotal+$ItemListRST['LineTotal'];
		}

		$ItemList .= "<tr>";
			$ItemList .= "<td class='text-right'>".number_format($no,0)."</td>";
			if(isset($ItemListRST['ItemName'])) { 
				$ItemList .= "<td>".$ItemListRST['ItemCode']." | ".$ItemName."</td>"; 
			} else { 
				$ItemList .= "<td>&nbsp;</td>"; 
			}
			if(isset($ItemListRST['Quantity'])) { 
				$ItemList .= "<td width='7.5%' class='text-right'>".number_format($ItemListRST['Quantity'],0)."</td>"; 
			} else { 
				$ItemList .= "<td width='7.5%' class='text-right'>&nbsp;</td>"; 
			}
			if(isset($ItemListRST['UnitMsr'])) { 
				$ItemList .= "<td width='6.25%'>".conutf8($ItemListRST['UnitMsr'])."</td>"; 
			} else { 
				$ItemList .= "<td width='6.25%'>&nbsp;</td>"; 
			}
			if(isset($ItemListRST['GrandPrice'])) { 
				$ItemList .= "<td class='text-right'>".number_format($ItemListRST['GrandPrice'],3)."</td>"; 
			} else { 
				$ItemList .= "<td class='text-right'>&nbsp;</td>"; 
			}
			$ItemList .= "<td class='text-center'>".$Discount."</td>";
			if(isset($ItemListRST['LineTotal'])) { 
				$ItemList .= "<td class='text-right'>".number_format($ItemListRST['LineTotal'],2)."</td>"; 
			} else { 
				$ItemList .= "<td class='text-right'>&nbsp;</td>"; 
			}
		$ItemList .= "</tr>";
		$no++;
	}
	$txt_pricebefvat = $SUMLineTotal;
	$txt_tax         = $txt_pricebefvat*0.07;
	$txt_doctotal    = $txt_pricebefvat+$txt_tax;
	$ItemList .= "<tr>";
		if(isset($HeaderRST['Comments'])) { 
			$ItemList .= "<td colspan='4' rowspan='5' class='align-top'><span class='fw-bolder'>หมายเหตุ:</span><br/>".conutf8($HeaderRST['Comments'])."</td>"; 
		} else { 
			$ItemList .= "<td colspan='4' rowspan='5' class='align-top'><span class='fw-bolder'>หมายเหตุ:</span><br/></td>"; 
		}
		$ItemList .= "<td colspan='2' class='text-right fw-bolder'>ยอดรวมทุกรายการ:</td>";
		$ItemList .= "<td class='text-right fw-bolder'>".number_format($SUMLineTotal,2)."</td>";
	$ItemList .= "</tr>";
	$ItemList .= "<tr>";
		$ItemList .= "<td colspan='2' class='fw-bolder text-right'>ส่วนลดท้ายบิล:</td>";
		$ItemList .= "<td class='text-right'>".number_format(0,0)."</td>";
	$ItemList .= "</tr>";
	$ItemList .= "<tr>";
		$ItemList .= "<td colspan='2' class='fw-bolder text-right'>ยอดสินค้าหลังหักส่วนลด:</td>";
		$ItemList .= "<td class='text-right'>".number_format($txt_pricebefvat,2)."</td>";
	$ItemList .= "</tr>";
	$ItemList .= "<tr>";
		$ItemList .= "<td colspan='2' class='fw-bolder text-right'>ภาษีมูลค่าเพิ่ม:</td>";
		$ItemList .= "<td class='text-right'>".number_format($txt_tax,2)."</td>";
	$ItemList .= "</tr>";
	$ItemList .= "<tr>";
		$ItemList .= "<td colspan='2' class='fw-bolder text-right fw-bolder'>จำนวนเงินรวมสุทธิ:</td>";
		$ItemList .= "<td class='text-right fw-bolder'>".number_format($txt_doctotal,2)."</td>";
	$ItemList .= "</tr>";
	$arrCol['view_ItemList'] = $ItemList;

	/* ADDRESS */
	$arrCol['view_BilltoAddress'] = "<span class='fw-bolder'>".conutf8($HeaderRST['BilltoCode'])."</span><br/>".str_replace(conutf8($HeaderRST['BilltoCode']),"",conutf8($HeaderRST['AddressBillto']));
	$arrCol['view_ShiptoAddress'] = "<span class='fw-bolder'>".conutf8($HeaderRST['ShiptoCode'])."</span><br/>".str_replace(conutf8($HeaderRST['ShiptoCode']),"",conutf8($HeaderRST['AddressShipto']));
	$ShippingCode = $HeaderRST['ShippingType'];
	$ShippingSQL = "SELECT TOP 1 T0.Code, T0.Name, T0.U_Address FROM [dbo].[@SHIPPINGTYPE] T0 WHERE T0.Code = N'".SapTHSearch($ShippingCode)."'";
	$ShippingQRY = SAPSelect($ShippingSQL);
	$ShippingType = "";
	while($ShippingRST = odbc_fetch_array($ShippingQRY)) {
		$ShippingType =  "<span class='fw-bolder'>".conutf8($ShippingRST['Name'])."</span><br/>".conutf8($ShippingRST['U_Address']);
	}
	$arrCol['view_ShippingType'] = $ShippingType;
}

// TAB 4
if($_GET['a'] == 'CheckT4') {
	$cYear = date("Y");
	$CardCode = $_POST['CardCode'];
	switch($_POST['UCode']) {
		case "C": $UCode = "T1.[Quantity]"; $x=0; break;
		case "U": $UCode = "T1.[Quantity]*T1.[Price]"; $x=2; break;
	}
	$sql = "SELECT TOP 10 W1.[ItemCode],W1.[Dscription],W1.[UnitMsr],
				SUM(W1.[M01]) AS M1, SUM(W1.[M02]) AS M2, SUM(W1.[M03]) AS M3,
				SUM(W1.[M04]) AS M4, SUM(W1.[M05]) AS M5, SUM(W1.[M06]) AS M6,
				SUM(W1.[M07]) AS M7, SUM(W1.[M08]) AS M8, SUM(W1.[M09]) AS M9,
				SUM(W1.[M10]) AS M10, SUM(W1.[M11]) AS M11, SUM(W1.[M12]) AS M12,
				SUM(W1.[M01])+SUM(W1.[M02])+SUM(W1.[M03])+SUM(W1.[M04])+SUM(W1.[M05])+SUM(W1.[M06])+SUM(W1.[M07])+SUM(W1.[M08])+SUM(W1.[M09])+SUM(W1.[M10])+SUM(W1.[M11])+SUM(W1.[M12]) AS DocTotal
			FROM                                     
				(SELECT
				T1.[ItemCode],T1.[Dscription],T1.[UnitMsr],
				CASE WHEN MONTH(T0.[DocDate]) = 1 THEN ".$UCode." ELSE 0 END AS M01,
				CASE WHEN MONTH(T0.[DocDate]) = 2 THEN ".$UCode." ELSE 0 END AS M02,
				CASE WHEN MONTH(T0.[DocDate]) = 3 THEN ".$UCode." ELSE 0 END AS M03,
				CASE WHEN MONTH(T0.[DocDate]) = 4 THEN ".$UCode." ELSE 0 END AS M04,
				CASE WHEN MONTH(T0.[DocDate]) = 5 THEN ".$UCode." ELSE 0 END AS M05,
				CASE WHEN MONTH(T0.[DocDate]) = 6 THEN ".$UCode." ELSE 0 END AS M06,
				CASE WHEN MONTH(T0.[DocDate]) = 7 THEN ".$UCode." ELSE 0 END AS M07,
				CASE WHEN MONTH(T0.[DocDate]) = 8 THEN ".$UCode." ELSE 0 END AS M08,
				CASE WHEN MONTH(T0.[DocDate]) = 9 THEN ".$UCode." ELSE 0 END AS M09,
				CASE WHEN MONTH(T0.[DocDate]) = 10 THEN ".$UCode." ELSE 0 END AS M10,
				CASE WHEN MONTH(T0.[DocDate]) = 11 THEN ".$UCode." ELSE 0 END AS M11,
				CASE WHEN MONTH(T0.[DocDate]) = 12 THEN ".$UCode." ELSE 0 END AS M12
				FROM OINV T0
				JOIN INV1 T1 ON T0.[DocEntry] = T1.[DocEntry]
				WHERE YEAR(T0.[DocDate]) = '".$cYear."' AND T0.[CardCode] = '".$CardCode."' AND T1.[ItemCode] IS NOT NULL
				UNION ALL
				SELECT
				T1.[ItemCode],T1.[Dscription],T1.[UnitMsr],
				CASE WHEN MONTH(T0.[DocDate]) = 1 THEN -(".$UCode.") ELSE 0 END AS M01,
				CASE WHEN MONTH(T0.[DocDate]) = 2 THEN -(".$UCode.") ELSE 0 END AS M02,
				CASE WHEN MONTH(T0.[DocDate]) = 3 THEN -(".$UCode.") ELSE 0 END AS M03,
				CASE WHEN MONTH(T0.[DocDate]) = 4 THEN -(".$UCode.") ELSE 0 END AS M04,
				CASE WHEN MONTH(T0.[DocDate]) = 5 THEN -(".$UCode.") ELSE 0 END AS M05,
				CASE WHEN MONTH(T0.[DocDate]) = 6 THEN -(".$UCode.") ELSE 0 END AS M06,
				CASE WHEN MONTH(T0.[DocDate]) = 7 THEN -(".$UCode.") ELSE 0 END AS M07,
				CASE WHEN MONTH(T0.[DocDate]) = 8 THEN -(".$UCode.") ELSE 0 END AS M08,
				CASE WHEN MONTH(T0.[DocDate]) = 9 THEN -(".$UCode.") ELSE 0 END AS M09,
				CASE WHEN MONTH(T0.[DocDate]) = 10 THEN -(".$UCode.") ELSE 0 END AS M10,
				CASE WHEN MONTH(T0.[DocDate]) = 11 THEN -(".$UCode.") ELSE 0 END AS M11,
				CASE WHEN MONTH(T0.[DocDate]) = 12 THEN -(".$UCode.") ELSE 0 END AS M12
				FROM ORIN T0
				JOIN RIN1 T1 ON T0.[DocEntry] = T1.[DocEntry]
				WHERE YEAR(T0.[DocDate]) = '".$cYear."' AND T0.[CardCode] = '".$CardCode."' AND T1.[ItemCode] IS NOT NULL) W1
			GROUP BY W1.[ItemCode],W1.[Dscription],W1.[UnitMsr]
			ORDER BY DocTotal DESC";
	$sqlQRY = SAPSelect($sql);
	$Tbody = "";
	$Row = 0;
	while($result = odbc_fetch_array($sqlQRY)) {
		$Tbody .= 	"<tr>".
						"<td class='text-center'>".$result['ItemCode']."</td>".
						"<td>".conutf8($result['Dscription'])."</td>".
						"<td class='text-center'>".conutf8($result['UnitMsr'])."</td>".
						"<td class='text-right'>".chk0($result['M1'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M2'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M3'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M4'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M5'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M6'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M7'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M8'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M9'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M10'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M11'],$x)."</td>".
						"<td class='text-right'>".chk0($result['M12'],$x)."</td>".
						"<td class='text-right fw-bolder'>".chk0($result['DocTotal'],$x)."</td>".
					"</tr>";
		$Row++;
	}
	$rr = 10-$Row;
	for($r = 1; $r <= $rr; $r++) {
		$Tbody .=	"<tr>".
						"<td class='text-center'>&nbsp;</td>".
						"<td>&nbsp;</td>".
						"<td class='text-center'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right'>&nbsp;</td>".
						"<td class='text-right fw-bolder'>&nbsp;</td>".
					"</tr>";
	}
	$arrCol['Tbody'] = $Tbody;
}

if($_GET['a'] == 'HisMeet') {
	$CardCode = $_POST['CardCode'];
	$SQL = "
		SELECT
			T0.CreateDate, T1.RouteEntry,
			T1.Comments,
			(SELECT P0.DetailPlan FROM route_action P0 WHERE (P0.DocStatus = 'A' AND P0.CardCode = T1.CardCode AND (P0.plan_year  = YEAR(T0.CreateDate) AND P0.plan_month = MONTH(T0.CreateDate))) ORDER BY P0.SurveyID DESC) AS 'Remark',
			T0.plan_lon, T0.plan_lat, T0.chk_lon, T0.chk_lat, T0.ChkDistance, CONCAT(T2.uName,' (',T2.uNickName,')') AS 'ChkName'
		FROM route_checkin T0
		LEFT JOIN route_planner T1 ON T0.RouteEntry = T1.RouteEntry
		LEFT JOIN users T2 ON T1.CreateUkey = T2.uKey
		WHERE T1.CardCode = '$CardCode'
		ORDER BY T0.CheckID DESC
		LIMIT 10";
	$QRY = MySQLSelectX($SQL);
	$r = 0;
	while($result = mysqli_fetch_array($QRY)) {
		$r++;
		$arrCol[$r]['CreateDate'] = date("d/m/Y",strtotime($result['CreateDate']))." เวลา ".date("H:i",strtotime($result['CreateDate']))." น.";
		if($result['Comments'] != null) {
			$arrCol[$r]['Comments']   = $result['Comments'];
		}else{
			$arrCol[$r]['Comments']   = "";
		}
		if($result['Remark'] != null) {
			$arrCol[$r]['Remark']   = $result['Remark'];
		}else{
			$arrCol[$r]['Remark']   = "";
		}
		$arrCol[$r]['ChkName']    = $result['ChkName'];
		$arrCol[$r]['RouteEntry']    = "<a href='javascript:void(0);' onclick='HisMeetRoute(".$result['RouteEntry'].")' ><i class='fas fa-search-location fa-fw'></i></a>";
	}
	$arrCol['Row'] = $r;
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
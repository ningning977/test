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
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == 'SelectData') {
	$CardCode  = $_POST['CardCode'];
	$StartDate = $_POST['StartDate'];
	$EndDate   = $_POST['EndDate'];
	$arrCol['H_TB'] = "ระหว่างวันที่ ".substr($StartDate,8,2)." ".FullMonth(intval(substr($StartDate,5,2)))." ".substr($StartDate,0,4)." ถึงวันที่ ".substr($EndDate,8,2)." ".FullMonth(intval(substr($EndDate,5,2)))." ".substr($EndDate,0,4);
	// HEADER
	$SQL_HEAD ="SELECT T0.CardCode, T0.CardName, T1.GroupName, T0.LicTradNum, T2.SlpName, (T3.Street+' '+T3.Block+' '+T3.City+' '+T3.ZipCode) AS 'Block', T3.City, T0.Phone1,
					T0.Phone2, T0.Cellular, T4.PymntGroup, T0.CreditLine, T0.Balance, T0.U_ChqCond
				FROM OCRD T0
				LEFT JOIN OCRG T1 ON T0.GroupCode = T1.GroupCode
				LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
				LEFT JOIN CRD1 T3 ON T0.CardCode = T3.CardCode
				LEFT JOIN OCTG T4 ON T0.GroupNum = T4.GroupNum
				WHERE T0.CardCode = '$CardCode' AND T3.AdresType = 'B'";
	$QRY_HEAD = SAPSelect($SQL_HEAD);
	$result_HEAD = odbc_fetch_array($QRY_HEAD);
	if($result_HEAD['Phone1'] == "") {
		if($result_HEAD['Phone2'] == "") {
			if($result_HEAD['Cellular'] == "") {
				$Phone = "";
			}else{ $Phone = conutf8($result_HEAD['Cellular']); }
		}else{ $Phone = conutf8($result_HEAD['Phone2']); }
	}else{ $Phone = conutf8($result_HEAD['Phone1']); }

	$Head = ["รหัสลูกค้า",   "ผู้แทนขาย",           "เครดิต",
			"ชื่อลูกค้า",    "รหัสประจำตัวผู้เสียภาษี", "วงเงินเครดิต",
			"กลุ่มลูกค้า",   "เงื่อนไข",            "วงเงินคงเหลือ",
			"เบอร์โทรศัพท์","ที่อยู่"]; 

	$D_Head =[ 	$result_HEAD['CardCode'], 			conutf8($result_HEAD['SlpName']),   conutf8($result_HEAD['PymntGroup']),
				conutf8($result_HEAD['CardName']),  $result_HEAD['LicTradNum'], 	    number_format($result_HEAD['CreditLine'],0)." บาท",
				conutf8($result_HEAD['GroupName']), conutf8($result_HEAD['U_ChqCond']), number_format($result_HEAD['Balance'],0)." บาท",
				$Phone,                             conutf8($result_HEAD['Block']) ]; 
	$n = 0;
	$c = "";
	$Thead = "";
	for($r = 1; $r <= 4; $r++) {
		if($r <= 3){
			if($r == 1){ $c = "pt-3"; }else{ $c = ""; }
			$Thead .= "<tr>";
				$Thead .= "<th class='".$c."'>".$Head[$n]."</th>"; 
				$Thead .= "<td class='".$c."'>".$D_Head[$n]."</td>"; $n++;
				$Thead .= "<th class='".$c."'>".$Head[$n]."</th>"; 
				$Thead .= "<td class='".$c."'>".$D_Head[$n]."</td>"; $n++;
				$Thead .= "<th class='".$c."'>".$Head[$n]."</th>"; 
				$Thead .= "<td class='".$c."'>".$D_Head[$n]."</td>"; $n++;
			$Thead .= "</tr>";
		}else{
			$Thead .= "<tr>";
				$Thead .= "<th>".$Head[$n]."</th>"; 
				$Thead .= "<td>".$D_Head[$n]."</td>"; $n++;
				$Thead .= "<th>".$Head[$n]."</th>"; 
				$Thead .= "<td colspan='3'>".$D_Head[$n]."</td>"; $n++;
			$Thead .= "</tr>";
		}
	}
	$arrCol['Thead'] = $Thead;

	// ยอดขายของร้านค้า
	$SQL2 ="SELECT MAX(P1.[MONTH]) AS 'MONTH',SUM(P1.[PSTYEAR_SALE]) AS 'PASTYEAR', SUM(P1.[CURYEAR_SALE]) AS 'CURRYEAR'
				FROM
					(SELECT MONTH(OINV.[DocDate]) AS 'MONTH',
                    CASE
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 1) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 2) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 3) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 4) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 5) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 6) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 7) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 8) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 9) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 10) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 11) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 12) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                    END AS 'PSTYEAR_SALE',
                    CASE
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 1) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 2) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 3) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 4) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 5) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 6) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 7) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 8) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 9) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 10) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 11) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 12) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                    END AS 'CURYEAR_SALE'  																					
                FROM OINV
                WHERE YEAR(OINV.[DocDate]) >= YEAR(GETDATE())-1 AND OINV.[CardCode] = '$CardCode' AND OINV.CANCELED = 'N'
                GROUP BY YEAR(OINV.[DocDate]),MONTH(OINV.[DocDate])
                UNION ALL
                SELECT MONTH(ORIN.[DocDate]) AS 'MONTH',
                    CASE
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 1) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 2) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 3) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 4) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 5) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 6) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 7) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 8) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 9) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 10) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 11) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 12) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                    END AS 'PSTYEAR_SALE',
                    CASE
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 1) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 2) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 3) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 4) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 5) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 6) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 7) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 8) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 9) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 10) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 11) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 12) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                    END AS 'CURYEAR_SALE'    																					
                FROM ORIN
                WHERE YEAR(ORIN.[DocDate]) >= YEAR(GETDATE())-1 AND ORIN.[CardCode] = '$CardCode' AND ORIN.CANCELED = 'N'
                GROUP BY YEAR(ORIN.[DocDate]),MONTH(ORIN.[DocDate])
            ) P1
            GROUP BY P1.[Month]
            ORDER BY P1.[Month]";

	$sqlP2QRY = SAPSelect($SQL2); /* EDIT Y */
	$cYEAR = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
	$pYEAR = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
	if(date("Y") == 2023) {
		$sqlP2QRY_conSAP8 = conSAP8($SQL2);
		while ($resultP2 = odbc_fetch_array($sqlP2QRY)) {
			$cYEAR[$resultP2['MONTH']] = $resultP2['CURRYEAR'];
		}
		while ($resultP2_conSAP8 = odbc_fetch_array($sqlP2QRY_conSAP8)) {
			$pYEAR[$resultP2_conSAP8['MONTH']] = $resultP2_conSAP8['PASTYEAR'];
		}
	}else{
		while ($resultP2 = odbc_fetch_array($sqlP2QRY)) {
			$cYEAR[$resultP2['MONTH']] = $resultP2['CURRYEAR'];
			$pYEAR[$resultP2['MONTH']] = $resultP2['PASTYEAR'];
		}
	}
	$AllcYEAR = 0;
	$TbodyP2 = "<tr class='text-right'>";
		$TbodyP2 .= "<td class='fw-bolder text-center'>ยอดขาย ".date("Y")."</td>";
		for($i = 1; $i <= 12; $i++) {
			$TbodyP2 .= "<td>".number_format($cYEAR[$i],0)."</td>";
			$AllcYEAR = $AllcYEAR+$cYEAR[$i];
		}
		$TbodyP2 .= "<td class='fw-bolder text-primary'>".number_format($AllcYEAR,0)."</td>";
		$cYearAVG = $AllcYEAR/date("m");
		$TbodyP2 .= "<td class='fw-bolder text-primary'>".number_format($cYearAVG,0)."</td>";
	$TbodyP2 .= "</tr>";

	$AllpYEAR = 0;
	$TbodyP2 .= "<tr class='text-right'>";
		$TbodyP2 .= "<td class='fw-bolder text-center'>ยอดขาย ".(date("Y")-1)."</td>";
		for($i = 1; $i <= 12; $i++) {
			$TbodyP2 .= "<td>".number_format($pYEAR[$i],0)."</td>";
			$AllpYEAR = $AllpYEAR+$pYEAR[$i];
		}
		$TbodyP2 .= "<td class='fw-bolder text-primary'>".number_format($AllpYEAR,0)."</td>";
		$pYearAVG = $AllpYEAR/12;
		$TbodyP2 .= "<td class='fw-bolder text-primary'>".number_format($pYearAVG,0)."</td>";
	$TbodyP2 .= "</tr>";
	$arrCol['Tbody2'] = $TbodyP2;


}

if($_GET['a'] == 'DataTB') {
	$CardCode  = $_POST['CardCode'];
	$StartDate = $_POST['StartDate'];
	$EndDate   = $_POST['EndDate'];
	$ItemCode  = $_POST['ItemCode'];

	// รายการการขายสินค้า
	if (strlen($ItemCode) == 0){
		$SQL_DETAIL =  "SELECT T0.NumAtCard, T3.Beginstr, T0.DocNum, T0.DocDate, T0.DocDueDate, T4.SlpName, T0.U_PONo, T0.DocEntry
					FROM OINV T0 
					LEFT JOIN NNM1 T3 ON T0.Series = T3.Series 
					LEFT JOIN OSLP T4 ON T0.SlpCode = T4.SlpCode 
					WHERE T0.CardCode = '$CardCode' AND T0.DocDate BETWEEN '$StartDate' AND '$EndDate' 
					ORDER BY T0.DocDate DESC,T0.DocNum DESC,T0.CardCode";
	}else{
		$SQL_DETAIL =  "SELECT DISTINCT T0.NumAtCard, T3.Beginstr, T0.DocNum, T0.DocDate, T0.DocDueDate, T4.SlpName, T0.U_PONo, T0.DocEntry, T0.CardCode
					FROM OINV T0 
					LEFT JOIN INV1 T1 ON T0.DocEntry = T1.DocEntry
					LEFT JOIN NNM1 T3 ON T0.Series = T3.Series 
					LEFT JOIN OSLP T4 ON T0.SlpCode = T4.SlpCode 

					WHERE T0.CardCode = '$CardCode' AND T0.DocDate BETWEEN '$StartDate' AND '$EndDate'  AND T1.ItemCode = '$ItemCode' 
					ORDER BY T0.DocDate DESC,T0.DocNum DESC,T0.CardCode";

	}

	if(intval(substr($EndDate,0,4)) <= 2022) {
		$QRY_DETAIL = conSAP8($SQL_DETAIL);
	}else{
		$QRY_DETAIL = SAPSelect($SQL_DETAIL);
	}
	$Tbody_Detail = array();
	$r = 0;
	while($result = odbc_fetch_array($QRY_DETAIL)) {
		if ($result['NumAtCard'] != '' && $result['Beginstr'] == null){
			$arrCol[$r]['NumAtCard'] = "<a href='javascript:void(0);' data-id='".$result['DocEntry']."' onclick='Detail(".$result['DocEntry'].");'>".$result['NumAtCard']."</a>";
		}else{
			$arrCol[$r]['NumAtCard'] = "<a href='javascript:void(0);' data-id='".$result['DocEntry']."' onclick='Detail(".$result['DocEntry'].");'>".$result['Beginstr'].$result['DocNum']."</a>";
		}
		$arrCol[$r]['DocDate']    = date("d/m/Y",strtotime($result['DocDate']));
		$arrCol[$r]['DocDueDate'] = date("d/m/Y",strtotime($result['DocDueDate']));
		$arrCol[$r]['SlpName']    = conutf8($result['SlpName']);
		$arrCol[$r]['U_PONo']     = conutf8($result['U_PONo']);
		$r++;
	}
}

if($_GET['a'] == 'Detail') {
	$DocEntry  = $_POST['DocEntry'];
	$CardCode  = $_POST['CardCode'];
	$StartDate = $_POST['StartDate'];
	$EndDate   = $_POST['EndDate'];
	$SQL = "SELECT T0.NumAtCard, T3.Beginstr, T0.DocNum, T0.DocDate, T0.DocDueDate, T4.SlpName, T0.U_PONo,
				T1.ItemCode, T2.U_ProductStatus, T1.Dscription, T1.Quantity, T1.unitMsr, T1.PriceBefDi, T1.LineTotal, 
				T1.U_DiscP1, T1.U_DiscP2, T1.U_DiscP3, T1.U_DiscP4, T1.U_DiscP5,
				T0.DocTotal, T0.VatSum, T0.DocEntry
			FROM OINV T0 
			LEFT JOIN INV1 T1 ON T0.DocEntry = T1.DocEntry 
			LEFT JOIN OITM T2 ON T1.ItemCode = T2.ItemCode 
			LEFT JOIN NNM1 T3 ON T0.Series = T3.Series 
			LEFT JOIN OSLP T4 ON T0.SlpCode = T4.SlpCode 
			WHERE T0.CardCode = '$CardCode' AND T0.DocDate BETWEEN '$StartDate' AND '$EndDate' AND T0.DocEntry = '$DocEntry'
			ORDER BY T0.DocDate DESC,T0.DocNum DESC,T0.CardCode";
	if(intval(substr($EndDate,0,4)) <= 2022) {
		$QRY = conSAP8($SQL);
	}else{
		$QRY = SAPSelect($SQL);
	}
	$Tbody = array();
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		if($r == 0) {
			$Tbody['SumNoVat'][$r] = number_format(($result['DocTotal']-$result['VatSum']),2);
			$Tbody['VatSum'][$r]   = number_format($result['VatSum'],2);
			$Tbody['DocTotal'][$r] = number_format($result['DocTotal'],2);
		}
		$Tbody['ItemCode'][$r]   = $result['ItemCode'];
		$Tbody['Status'][$r]     = $result['U_ProductStatus'];
		$Tbody['Dscription'][$r] = conutf8($result['Dscription']);
		$Tbody['Quantity'][$r]   = number_format($result['Quantity'],0);
		$Tbody['unitMsr'][$r]    = conutf8($result['unitMsr']);
		$Tbody['PriceBefDi'][$r] = number_format($result['PriceBefDi'],2);
		if(0 < $result['U_DiscP5']){
			$Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'],2)."%+".number_format($result['U_DiscP2'],2)."%+".number_format($result['U_DiscP3'],2)."%+".number_format($result['U_DiscP4'])."%+".number_format($result['U_DiscP5'])."%";
		}else{
			if(0 < $result['U_DiscP4']){
				$Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'])."%+".number_format($result['U_DiscP2'])."%+".number_format($result['U_DiscP3'])."%+".number_format($result['U_DiscP4'])."%";
			}else{
				if(0 < $result['U_DiscP3']) {
					$Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'],2)."%+".number_format($result['U_DiscP2'],2)."%+".number_format($result['U_DiscP3'],2)."%";
				}else{
					if(0 < $result['U_DiscP2']) {
						$Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'],2)."%+".number_format($result['U_DiscP2'],2)."%";
					}else{
						if(0 < $result['U_DiscP1']) {
							$Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'],2)."%";
						}else{
							$Tbody['U_Disc'][$r] = "0.00%";
						}
					}
				}
			}
		}
		$Tbody['LineTotal'][$r] = number_format($result['LineTotal'],2);
		$r++;
	}
	$arrCol['Tbody'] = $Tbody;
	$arrCol['Row'] = $r;
} 

// $arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
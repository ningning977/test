<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName'] == NULL) {
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

if($_GET['a'] == "GetDocData") {
	$box = $_POST['box'];
	$SQL1 =
		"SELECT
			T0.DocEntry, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.U_RefNoCust, T0.CardCode, T0.CardName, T0.DocDate, T0.Comments, T3.SlpName,
			T1.LineNum, T1.VisOrder, T1.ItemCode, T1.Dscription, T1.WhsCode, T1.Quantity, T1.unitMsr,
			CASE
				WHEN T3.U_Dim1 IN ('TT1','OUL') THEN 'OUL'
				WHEN T3.U_Dim1 IN ('TT2') THEN 'TTC'
				ELSE T3.U_Dim1
			END AS 'CH'
		FROM ORDN T0
		LEFT JOIN RDN1 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
		LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
		WHERE ((T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) LIKE '%$box%' OR T0.U_RefNoCust LIKE '%$box%') AND T1.WhsCode IN ('KSY','KSM','KB4','MT','MT2','TT-C','OUL')
		ORDER BY T1.VisOrder ASC";
	// echo $SQL1;
	$ROW1 = ChkRowSAP($SQL1);
	if($ROW1 > 0) {
		$QRY1 = SAPSelect($SQL1);
		$tmpDocEntry = "";
		$i = 0;
		while($RST1 = odbc_fetch_array($QRY1)) {
			if($tmpDocEntry == "") {
				$tmpDocEntry = $RST1['DocEntry'];

				$arrCol['HD']['DocEntry']  = $RST1['DocEntry'];
				$arrCol['HD']['DocNum']    = $RST1['DocNum'];
				$arrCol['HD']['RefDocNum'] = conutf8($RST1['U_RefNoCust']);
				$arrCol['HD']['CardCode']  = $RST1['CardCode'];
				$arrCol['HD']['CardName']  = conutf8($RST1['CardName']);
				$arrCol['HD']['DocDate']   = date("d/m/Y",strtotime($RST1['DocDate']));
				$arrCol['HD']['SlpName']   = conutf8($RST1['SlpName']);
				$arrCol['HD']['Comments']   = conutf8($RST1['Comments']);
			}

			$SQL2 = "SELECT T0.TransID FROM returnquota T0 WHERE T0.DocEntry = '".$RST1['DocEntry']."' AND T0.LineNum = '".$RST1['LineNum']."' AND T0.ItemCode = '".$RST1['ItemCode']."'";
			// echo $SQL2;
			$ROW2 = CHKRowDB($SQL2);

			if($ROW2 > 0) {
				$arrCol['BD'][$i]['DONE'] = "Y";
			} else {
				$arrCol['BD'][$i]['DONE'] = "N";
			}

			$arrCol['BD'][$i]['LineNum']    = $RST1['LineNum'];
			$arrCol['BD'][$i]['ItemCode']   = $RST1['ItemCode'];
			$arrCol['BD'][$i]['Dscription'] = conutf8($RST1['Dscription']);
			$arrCol['BD'][$i]['WhsCode']    = conutf8($RST1['WhsCode']);
			$arrCol['BD'][$i]['Quantity']   = $RST1['Quantity'];
			$arrCol['BD'][$i]['unitMsr']    = conutf8($RST1['unitMsr']);
			$arrCol['BD'][$i]['CH']         = $RST1['CH'];
			$i++;
		}
		
	}

	$arrCol['Row'] = $ROW1;
}

if($_GET['a'] == "AddQuota") {
	$DocEntry = $_POST['DocEntry'];
	$LineNum  = $_POST['LineNum'];
	$CH       = $_POST['CH'];
	$SSID     = session_id();
	$ukey     = $_SESSION['ukey'];

	$SQL1 = 
		"SELECT TOP 1
			T0.DocEntry, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.U_RefNoCust, T3.U_Dim1,
			T1.LineNum, T1.ItemCode, T1.WhsCode, T1.Quantity
		FROM ORDN T0
		LEFT JOIN RDN1 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
		LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
		WHERE T0.DocEntry = $DocEntry AND T1.LineNum = $LineNum";
	// echo $SQL1;
	$ROW1 = ChkRowSAP($SQL1);
	if($ROW1 == 0) {
		$arrCol['Status'] = "ERR::NORESULT";
	} else {
		$QRY1 = SAPSelect($SQL1);
		$RST1 = odbc_fetch_array($QRY1);

		$DocNum    = $RST1['DocNum'];
		$RefDocNum = conutf8($RST1['U_RefNoCust']);
		$LineNum   = $RST1['LineNum'];
		$ItemCode  = $RST1['ItemCode'];
		$WhsCode   = conutf8($RST1['WhsCode']);
		$Quantity  = $RST1['Quantity'];
		$TeamCode  = $RST1['U_Dim1'];

		$SQL2 =
			"INSERT INTO returnquota SET
				SSID = '$SSID',
				DocEntry = $DocEntry,
				DocNum = '$DocNum',
				RefDocNum = '$RefDocNum',
				LineNum = $LineNum,
				ItemCode = '$ItemCode',
				WhsCode = '$WhsCode',
				Quantity = $Quantity,
				TeamCode = '$TeamCode',
				CreateUkey = '$ukey',
				CreateDate = NOW(),
				LineStatus = 'A'";
		// echo $SQL2;
		
		$TransID = MySQLInsert($SQL2);

		if($TransID > 0) {
			/* ADD WHSQUOTA */
			$SQL3 =
				"INSERT INTO whsquota_trn SET
					trnType = 'R',
					WhsTarget = '$CH',
					WhsSource = '$WhsCode',
					trnDate = NOW(),
					ItemCode = '$ItemCode',
					QtyIn = $Quantity,
					QtyOut = 0,
					StatusDoc = '1',
					DocNum = '$DocNum'
					ReturnX = 'N'";
			$IDTrans = MySQLInsert($SQL3);

			/* CHECK WHSQUOTA */
			$SQL4 = "SELECT T0.ID, T0.OnHand FROM whsquota T0 WHERE T0.ItemCode = '$ItemCode' AND T0.CH = '$CH' AND T0.WhsCode = '$WhsCode'";
			$ROW4 = ChkRowDB($SQL4);
			if($ROW4 == 0) {
				/* INSERT */
				$SQL5 =
					"INSERT INTO whsquota SET
						WhsCode = '$WhsCode',
						ItemCode = '$ItemCode',
						CH = '$CH',
						OnHand = $Quantity,
						LastUpdate = NOW(),
						LastUkey = '$ukey',
						LastIDTran = $IDTrans";
				MySQLInsert($SQL5);
			} else {
				/* UPDATE */
				$RST4 = MySQLSelect($SQL4);
				$QTID = $RST4['ID'];
				$OnHand = $RST4['OnHand'] + $Quantity;

				$SQL6 = "UPDATE whsquota SET OnHand = $OnHand, LastUpdate = NOW(), LastUkey = '$ukey', LastIDTran = $IDTrans WHERE ID = $QTID";
				MySQLUpdate($SQL6);
			}
			$arrCol['Status'] = "SUCCESS";
		} else {
			$arrCol['Status'] = "ERR::CANNOTINSERT";
		}
	}

}

if($_GET['a'] == 'CallData') {
	$WhseSQL = "
		SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
			T0.[ItemCode], T0.[WhsCode], T1.[WhsName],
			CASE
				WHEN T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') THEN 'W100'
				WHEN T0.WhsCode IN ('MT') THEN 'W101'
				WHEN T0.WhsCode IN ('MT2') THEN 'W102'
				WHEN T0.WhsCode IN ('TT-C') THEN 'W103'
				WHEN T0.WhsCode IN ('OUL') THEN 'W104'
				WHEN T0.WhsCode IN ('KB1','KB1.1') THEN 'W200'
				WHEN T1.Location IN (2) THEN 'W300'
				WHEN T1.Location IN (6,7,9) THEN 'W400'
			ELSE 'W500' END AS 'WhsGroup', T0.[OnHand], T0.[OnOrder]
		FROM OITW T0
		LEFT JOIN OWHS T1 ON T0.[WhsCode] = T1.[WhsCode]
		WHERE T0.[ItemCode] = '".$_POST['ItemCode']."' AND (T0.[OnHand] !=0 OR T0.[OnOrder] != 0)
		ORDER BY 'WhsGroup', T0.[WhsCode]";
	$WhseQRY = SAPSelect($WhseSQL);
	$PickSQL = "
		SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', 
			T0.ItemCode, T0.WhsCode, SUM(T0.OpenQty) AS 'OpenQty', SUM(T0.Qty) AS 'Qty'
		FROM picker_sodetail T0
		LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry
		WHERE (T1.DocType = 'ORDR' AND (T1.StatusDoc BETWEEN 2 AND 8)) AND T0.ItemCode = '".$_POST['ItemCode']."'
		GROUP BY T0.ItemCode, T0.WhsCode";
	$PickQRY = MySQLSelectX($PickSQL);
	while($PickRST = mysqli_fetch_array($PickQRY)) {
		${$PickRST['ItemCode']."_".$PickRST['WhsCode']."_Qty"} = $PickRST['Qty'];
        ${$PickRST['ItemCode']."_".$PickRST['WhsCode']."_OpenQty"} = $PickRST['OpenQty'];
	}
	$ItemSQL = "SELECT TOP 1  '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
                    T0.[ItemCode], T0.[CodeBars], T0.[ItemName], T0.[InvntryUom], 
                    CASE
                        WHEN T0.[U_ProductStatus] = 'D' THEN 'D - Delete Item'
                        WHEN T0.[U_ProductStatus] = 'D21' THEN 'D21 - Delete Item (2021)'
                        WHEN T0.[U_ProductStatus] = 'D22' THEN 'D22 - Delete Item (2022)'
						WHEN T0.[U_ProductStatus] = 'D23' THEN 'D23 - Delete Item (2023)'
                        WHEN T0.[U_ProductStatus] = 'R' THEN 'R - Replace Item'
                        WHEN T0.[U_ProductStatus] = 'A' THEN 'A - Active Item'
                        WHEN T0.[U_ProductStatus] = 'W' THEN 'W - Watchout / Warning Item'
                        WHEN T0.[U_ProductStatus] = 'N' THEN 'N - New Item'
                        WHEN T0.[U_ProductStatus] = 'M' THEN 'M - Made to order Item'
                    ELSE 'NULL' END AS 'U_ProductStatus',                         
                    (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T4.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc', 
                    ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC),T0.LastPurDat) AS 'LastPurDat', DATEDIFF(m,ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC),T0.LastPurDat),GETDATE()) AS 'Aging',
                    T1.Name AS 'Brand', T2.Name AS 'MainGroup', T3.Name AS 'SupGroup'
                FROM OITM T0
                LEFT JOIN [dbo].[@BRAND2]     T1 ON T0.[U_Brand2] = T1.[Code]
                LEFT JOIN [dbo].[@ITEMGROUP1] T2 ON T0.[U_Group1] = T2.[Code]
                LEFT JOIN [dbo].[@ITEMGROUP2] T3 ON T0.[U_Group2] = T3.[Code]
				LEFT JOIN KBI_DB2022.dbo.OITM T4 ON T0.ItemCode = T4.ItemCode
                WHERE T0.[ItemCode] = '".$_POST['ItemCode']."'";
	// echo $ItemSQL;
	$ItemQRY = SAPSelect($ItemSQL);
	$ItemRST = odbc_fetch_array($ItemQRY);
	$output1 = "
		<table class='table table-sm table-bordered rounded rounded-3 overflow-hidden table-hover'>
			<thead style='font-size: 13px;'>
				<tr class='text-center'>
					<th rowspan='2' class='align-middle'>ชื่อคลัง</th>
					<th colspan='5'>จำนวน (หน่วย)</th>
				</tr>
				<tr class='text-center'>
					<th width='12.5%'>คงคลัง</th>
					<th width='12.5%'>รอเบิก</th>
					<th width='12.5%'>เบิกแล้ว</th>
					<th width='12.5%'>คงเหลือ</th>
					<th width='12.5%'>กำลังสั่งซื้อ</th>
				</tr>
			</thead>
			<tbody style='font-size: 12px;'>";
				$tempGroup = "";
				$rowdata1 = 0;
				$Chk_KB4 = "N";
				while($WhseRST = odbc_fetch_array($WhseQRY)) {
					$rowdata1++;
					if($tempGroup != $WhseRST['WhsGroup']) {
						$tempGroup = $WhseRST['WhsGroup'];
						$output1 .= "<tr><td colspan='7' class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'>".WhsGroupName($tempGroup)."</td></tr>";
					}
					if(isset(${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_Qty"})) {
						$DT1 = ${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_Qty"}-${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_OpenQty"};
						$DT2 = ${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_OpenQty"};
						$DT3 = $WhseRST['OnHand']-${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_OpenQty"};
						$DT4 = ($WhseRST['OnHand']-${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_OpenQty"})*$ItemRST['LastPurPrc'];
					}else{
						$DT1 = 0;
						$DT2 = 0;
						$DT3 = $WhseRST['OnHand'];
						$DT4 = ($WhseRST['OnHand']*$ItemRST['LastPurPrc']);
					}
					$output1 .= "
						<tr>
							<td>".conutf8($WhseRST['WhsCode'])." - ".conutf8($WhseRST['WhsName'])."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnHand'],0))."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT1,0))."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT2,0))."</td>
							<td class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT3,0))."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnOrder'],0))."</td>
						</tr>";
					if($WhseRST['WhsCode'] == 'KB4') {
						$Chk_KB4 = "Y";
					}
				}
				if($rowdata1 == 0) {
					$output1 .= "
						<tr>
							<td colspan='6' class='text-center'>ไม่มีข้อมูล :(</td>
						</tr>";
				}
			$output1 .= "
			</tbody>
		</table>";
	/* OUT PUT 1 => จำนวนสินค้าคงคลัง SAP */
	$arrCol['output1'] = $output1;

	/* โอนย้ายสินค้าคลังจอง */
	$sql1 = "SELECT ISNULL(SUM(OnHandIN),0) AS OnHandIN
             FROM (SELECT T1.ItemCode,T1.ItemName,T1.CodeBars,T0.WhsCode,
                          CASE WHEN T0.WhsCode IN ('KSY','KSM','KB4','MT','MT2') THEN T0.OnHand ELSE 0 END AS OnHandIN,
                          CASE WHEN T2.Location = 2 THEN T0.OnHand ELSE 0 END AS OnHandOUT
                   FROM OITW T0
					LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
					LEFT JOIN OWHS T2 ON T2.WhsCode = T0.WhsCode
                   WHERE (T0.WhsCode IN ('KSY','KSM','KB4','MT','MT2') OR T2.Location = 2) AND T0.ItemCode = '".$_POST['ItemCode']."'
            ) P0";
	$getHead = SAPSelect($sql1);
	$DataHead = odbc_fetch_array($getHead);
	$OnUse = $DataHead['OnHandIN'];

	$sql2 = "SELECT ItemCode,CH,SUM(OnHand) AS OnHand FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' GROUP BY ItemCode,CH";
	$getDetail = MySQLSelectX($sql2);
    $sql3 = "SELECT T0.*,
				CONCAT(T1.uName,' ',T1.uLastName,' (',T1.uNickName,')') AS MKT_Name,
				CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')') AS TTC_Name,
				CONCAT(T3.uName,' ',T3.uLastName,' (',T3.uNickName,')') AS MT1_Name,
				CONCAT(T4.uName,' ',T4.uLastName,' (',T4.uNickName,')') AS MT2_Name,
				CONCAT(T5.uName,' ',T5.uLastName,' (',T5.uNickName,')') AS OUL_Name,
				CONCAT(T6.uName,' ',T6.uLastName,' (',T6.uNickName,')') AS ONL_Name
			FROM whsequota_header T0 
			LEFT JOIN users T1 ON T1.uKey = T0.MKT_Ukey
			LEFT JOIN users T2 ON T2.uKey = T0.TTC_Ukey
			LEFT JOIN users T3 ON T3.uKey = T0.MT1_Ukey
			LEFT JOIN users T4 ON T4.uKey = T0.MT2_Ukey
			LEFT JOIN users T5 ON T5.uKey = T0.OUL_Ukey
			LEFT JOIN users T6 ON T6.uKey = T0.ONL_Ukey
			WHERE ItemCode = '".$_POST['ItemCode']."' AND StatusDoc = 1";
	$DataMove = MySQLSelect($sql3);
	if(CHKRowDB($sql3) != 0) {
		$DataResIn['TTC'] = $DataMove['TTC_In'];
		$DataResOut['TTC'] = $DataMove['TTC_Out'];
		$DataResIn['MT1'] = $DataMove['MT1_In'];
		$DataResOut['MT1'] = $DataMove['MT1_Out'];
		$DataResIn['MT2'] = $DataMove['MT2_In'];
		$DataResOut['MT2'] = $DataMove['MT2_Out'];
		$DataResIn['OUL'] = $DataMove['OUL_In'];
		$DataResOut['OUL'] = $DataMove['OUL_Out'];
		$DataResIn['ONL'] = $DataMove['ONL_In'];
		$DataResOut['ONL'] = $DataMove['ONL_Out'];
		$DocOn = $DataMove['StatusDoc'];
	}else{
		$DataResIn['TTC'] = 0;
		$DataResOut['TTC'] = 0;
		$DataResIn['MT1'] = 0;
		$DataResOut['MT1'] = 0;
		$DataResIn['MT2'] = 0;
		$DataResOut['MT2'] = 0;
		$DataResIn['OUL'] = 0;
		$DataResOut['OUL'] = 0;
		$DataResIn['ONL'] = 0;
		$DataResOut['ONL'] = 0;
		$DocOn = "";

		$DataMove['All_In'] = 0;
		$DataMove['All_Out'] = 0;
	}
	if ($DocOn == 1){
        $HaveData = "disabled";
    }else{
        $HaveData = "";
    }
	$CH = array("TTC","MT1","MT2","OUL","ONL");
	$Ck_TTC = 0; $Ck_MT1 = 0; $Ck_MT2 = 0; $Ck_OUL = 0; $Ck_ONL = 0; 
	if(CHKRowDB($sql2) != 0) {
		while ($DataDetail = mysqli_fetch_array($getDetail)){
			$DataOnHand = 0;
			if($DataDetail['CH'] == "TTC") {
				$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
				$DataOnHand = $DataDetail['OnHand'];
				$Ck_TTC = 1;
			}
			if($DataDetail['CH'] == "MT1") {
				if($DataDetail['OnHand'] > 0) {
					$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
					$DataOnHand = $DataDetail['OnHand'];
				}else{
					$SQL = "
					SELECT
						T0.ItemCode, T1.WhsCode,T1.OnHand
					FROM OITM T0
					LEFT JOIN OITW T1 ON T0.ItemCode = T1.ItemCode
					WHERE T0.InvntItem != 'N' AND T0.ItemCode = '".$_POST['ItemCode']."' AND T1.WhsCode = '".$DataDetail['CH']."'";
					$QRY = SAPSelect($SQL);
					$RST = odbc_fetch_array($QRY);
					if(isset($RST['OnHand'])) {
						$OnHand[$DataDetail['CH']] = $RST['OnHand'];
						$DataOnHand = $RST['OnHand'];
					}else{
						$OnHand[$DataDetail['CH']] = 0;
						$DataOnHand =0;
					}
				}
				$Ck_MT1 = 1;
			}
			if($DataDetail['CH'] == "MT2") {
				if($DataDetail['OnHand'] > 0) {
					$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
					$DataOnHand = $DataDetail['OnHand'];
				}else{
					$SQL = "
					SELECT
						T0.ItemCode, T1.WhsCode,T1.OnHand
					FROM OITM T0
					LEFT JOIN OITW T1 ON T0.ItemCode = T1.ItemCode
					WHERE T0.InvntItem != 'N' AND T0.ItemCode = '".$_POST['ItemCode']."' AND T1.WhsCode = '".$DataDetail['CH']."'";
					$QRY = SAPSelect($SQL);
					$RST = odbc_fetch_array($QRY);
					if(isset($RST['OnHand'])) {
						$OnHand[$DataDetail['CH']] = $RST['OnHand'];
						$DataOnHand = $RST['OnHand'];
					}else{
						$OnHand[$DataDetail['CH']] = 0;
						$DataOnHand =0;
					}
				}
				$Ck_MT2 = 1;
			}
			if($DataDetail['CH'] == "OUL") {
				$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
				$DataOnHand = $DataDetail['OnHand'];
				$Ck_OUL = 1;
			}
			if($DataDetail['CH'] == "ONL") {
				$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
				$DataOnHand = $DataDetail['OnHand'];
				$Ck_ONL = 1;
			}
			$OnUse = $OnUse - $DataOnHand;
		}
		if($Ck_TTC == 0 ) { $OnHand["TTC"] = 0; }
		if($Ck_MT1 == 0 ) { $OnHand["MT1"] = 0; }
		if($Ck_MT2 == 0 ) { $OnHand["MT2"] = 0; }
		if($Ck_OUL == 0 ) { $OnHand["OUL"] = 0; }
		if($Ck_ONL == 0 ) { $OnHand["ONL"] = 0; }
	}else{
		for($i = 0; $i <= 4; $i++){
			$OnHand[$CH[$i]] = 0;
		}
		$OnUse = $OnUse - 0;
	}
	$NewDataShow = $OnUse + $DataMove['All_In'] - $DataMove['All_Out']; 
	$Dis = " ";

    if($Chk_KB4 == 'Y') {
		$Dis = " disabled ";
        $SQL_kb4 = "
        SELECT T0.[ItemCode], T0.[WhsCode], T1.[WhsName],
            CASE
                WHEN T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') THEN 'W100'
                WHEN T0.WhsCode IN ('MT') THEN 'W101'
                WHEN T0.WhsCode IN ('MT2') THEN 'W102'
                WHEN T0.WhsCode IN ('TT-C') THEN 'W103'
                WHEN T0.WhsCode IN ('OUL') THEN 'W104'
                WHEN T0.WhsCode IN ('KB1','KB1.1') THEN 'W200'
                WHEN T1.Location IN (2) THEN 'W300'
                WHEN T1.Location IN (6,7,9) THEN 'W400'
            ELSE 'W500' END AS 'WhsGroup', T0.[OnHand], T0.[OnOrder]
        FROM OITW T0
        LEFT JOIN OWHS T1 ON T0.[WhsCode] = T1.[WhsCode]
        WHERE T0.[ItemCode] = '".$_POST['ItemCode']."' AND (T0.[OnHand] !=0 OR T0.[OnOrder] != 0)
        ORDER BY T1.[WhsName]";
        $QRY_kb4 = SAPSelect($SQL_kb4);
        $output3_kb4 = "
            <div>โอนย้ายสินค้าคลังจอง</div>
            <div class='d-flex align-items-center text-primary'>";
				if($_SESSION['DeptCode'] == 'DP003') {
					$output3_kb4 .= "
					ระยะเวลาขาย&nbsp;
					<select class='form-select form-select-sm' style='width: 150px;' name='SaleTime' id='SaleTime'>
						<option value='0' selected>ไม่จำกัดเวลา</option>
						<option value='3'>3 เดือน</option>
						<option value='4'>4 เดือน</option>
						<option value='5'>5 เดือน</option>
						<option value='6'>6 เดือน</option>
					</select>
					&nbsp;&nbsp;";
				}
				$output3_kb4 .= "
                เลือกคลัง&nbsp;
                <select class='form-select form-select-sm' style='width: 200px;' name='WhsCaseKB4' id='WhsCaseKB4' onchange='WhsCaseKB4(\"".$_POST['ItemCode']."\");'>";
					$output3_kb4 .= "<option value='ALL' selected>คลังทั้งหมด</option>";
					while($RST_kb4 = odbc_fetch_array($QRY_kb4)) {
						if($RST_kb4['WhsCode'] == 'KSY' || $RST_kb4['WhsCode'] == 'KB4'){
							$output3_kb4 .= "<option value='".$RST_kb4['WhsCode']."'>".$RST_kb4['WhsCode']." - ".conutf8($RST_kb4['WhsName'])."</option>";
						}
					}
        		$output3_kb4 .= "
				</select>
            </div>";
    }else{
        $output3_kb4 = "
			<div>โอนย้ายสินค้าคลังจอง</div>
			<div class='d-flex align-items-center text-primary'>";
				if($_SESSION['DeptCode'] == 'DP003') {
					$output3_kb4 .= "
					ระยะเวลาขาย&nbsp;
					<select class='form-select form-select-sm' style='width: 150px;' name='SaleTime' id='SaleTime'>
						<option value='0' selected>ไม่จำกัดเวลา</option>
						<option value='3'>3 เดือน</option>
						<option value='4'>4 เดือน</option>
						<option value='5'>5 เดือน</option>
						<option value='6'>6 เดือน</option>
					</select>
					&nbsp;&nbsp;";
				}
		$output3_kb4 .= "</div>";
    }

	$output3 = "<tr>
					<td class='text-center'>คลังกลาง</td>
					<td><input class='text-right form-control form-control-sm' type='text' name='Now_All' id='Now_All' value='".number_format($OnUse)."' disabled></td>
					<td><input class='text-right form-control form-control-sm' type='text' name='Add_All' id='Add_All' value='".number_format($DataMove['All_In'])."' onfocusout=\"CHKdata('Add','All')\" ".$HaveData." ".$Dis."></td>
					<td><input class='text-right form-control form-control-sm' type='text' name='Red_All' id='Red_All' value='".number_format($DataMove['All_Out'])."' onfocusout=\"CHKdata('Red','All')\" ".$HaveData." ".$Dis."></td>
					<td><input class='text-right form-control form-control-sm' type='text' name='New_All' id='New_All' value='".number_format($NewDataShow)."' disabled></td>
				</tr>";
	$AllOnHand = $OnUse;
	for($i = 0; $i <= 4; $i++){ 
		$NewDataShow = $OnHand[$CH[$i]] + $DataResIn[$CH[$i]] - $DataResOut[$CH[$i]];
		$output3 .= "<tr>
						<td class='text-center'>".$CH[$i]."</td>
						<td><input class='text-right form-control form-control-sm' type='text' name='Now_".$CH[$i]."' id='Now_".$CH[$i]."' value='".number_format($OnHand[$CH[$i]])."' disabled></td>
						<td><input class='text-right form-control form-control-sm' type='text' name='Add_".$CH[$i]."' id='Add_".$CH[$i]."' value='".number_format($DataResIn[$CH[$i]])."' onfocusout=\"CHKdata('Add','".$CH[$i]."')\" ".$HaveData." ".$Dis."></td>
						<td><input class='text-right form-control form-control-sm' type='text' name='Red_".$CH[$i]."' id='Red_".$CH[$i]."' value='".number_format($DataResOut[$CH[$i]])."' onfocusout=\"CHKdata('Red','".$CH[$i]."')\" ".$HaveData." ".$Dis."></td>
						<td><input class='text-right form-control form-control-sm' type='text' name='New_".$CH[$i]."' id='New_".$CH[$i]."' value='".number_format($NewDataShow)."' disabled></td>
					</tr>";
		$AllOnHand = $AllOnHand + $OnHand[$CH[$i]];
	}
	$output3 .= "<tr class='fw-bolder text-primary'>
					<td class='text-center'>รวม</td>
					<td class='text-right' id='Final_Now'>".number_format($AllOnHand)."</td>
					<td class='text-right' id='Final_Add'>0</td>
					<td class='text-right' id='Final_Red'>0</td>
					<td class='text-right' id='Final_New'>0</td>
				</tr>";
	/* OUT PUT 3 => โอนย้ายสินค้าคลังจอง*/
	$arrCol['output3'] = $output3;
	$arrCol['output3_kb4'] = $output3_kb4;

	$output4 = "
		<thead style='font-size: 13px;'>
			<tr class='text-center'>
				<th width='20%' style='color: blue;'></th>
				<th width='15%'>ผลการดำเนินการ</th>
				<th width='42%'>เหตุผล</th>
				<th width='18%'>ชื่อ</th>
				<th width='5%'></th>
			</tr>
		</thead>
		<tbody style='font-size: 12px;'>";
		$Dis = "disabled";
		if($_SESSION['DeptCode'] == 'DP002' || $_SESSION['DeptCode'] == 'DP011') {
			$Dis = "";
		}
		$output4 .= "
			<tr>
				<td class='fw-bolder text-primary'>ผู้ดำเนินการ</td>
				<td>
					<select class='form-select form-select-sm' id='MgrMktApp'>      
						<option value='R' selected>ผู้ขอโอนย้าย</option>    
					</select>
				</td>
				<td><input class='form-control form-control-sm' type='text' name='MgrMktRemark' id='MgrMktRemark' value='' $Dis></td>
				<td><input class='form-control form-control-sm' type='text' name='MgrMktName' id='MgrMktName' value='".$_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")' disabled></td>
				<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('Mkt')\" $Dis>บันทึก</button></td>
			</tr>
		</tbody>";
	$arrCol['output4'] = $output4;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if($_GET['a'] == 'CallData') {
	$Year  = $_POST['Year'];
	$PYear = $Year-1;
	$SQL = "
		SELECT
			A0.Warehouse, A0.WhsName,
			SUM(A0.M_00) AS 'M_00',
			SUM(A0.M_01_I) AS 'M_01_I', SUM(A0.M_01_O) AS 'M_01_O',
			SUM(A0.M_02_I) AS 'M_02_I', SUM(A0.M_02_O) AS 'M_02_O',
			SUM(A0.M_03_I) AS 'M_03_I', SUM(A0.M_03_O) AS 'M_03_O',
			SUM(A0.M_04_I) AS 'M_04_I', SUM(A0.M_04_O) AS 'M_04_O',
			SUM(A0.M_05_I) AS 'M_05_I', SUM(A0.M_05_O) AS 'M_05_O',
			SUM(A0.M_06_I) AS 'M_06_I', SUM(A0.M_06_O) AS 'M_06_O',
			SUM(A0.M_07_I) AS 'M_07_I', SUM(A0.M_07_O) AS 'M_07_O',
			SUM(A0.M_08_I) AS 'M_08_I', SUM(A0.M_08_O) AS 'M_08_O',
			SUM(A0.M_09_I) AS 'M_09_I', SUM(A0.M_09_O) AS 'M_09_O',
			SUM(A0.M_10_I) AS 'M_10_I', SUM(A0.M_10_O) AS 'M_10_O',
			SUM(A0.M_11_I) AS 'M_11_I', SUM(A0.M_11_O) AS 'M_11_O',
			SUM(A0.M_12_I) AS 'M_12_I', SUM(A0.M_12_O) AS 'M_12_O'
		FROM (
			SELECT T0.Warehouse, T1.WhsName,
			(CASE WHEN T0.CreateDate = '2023-01-01'  OR (YEAR(T0.CreateDate) <= $PYear) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_00',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 1 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_01_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 1 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_01_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 2 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_02_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 2 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_02_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 3 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_03_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 3 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_03_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 4 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_04_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 4 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_04_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 5 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_05_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 5 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_05_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 6 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_06_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 6 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_06_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 7 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_07_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 7 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_07_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 8 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_08_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 8 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_08_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 9 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_09_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 9 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_09_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 10 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_10_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 10 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_10_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 11 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_11_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 11 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_11_O',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 12 AND T0.InQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_12_I',
			(CASE WHEN T0.CreateDate != '2023-01-01' AND (YEAR(T0.CreateDate) = $Year AND MONTH(T0.CreateDate) = 12 AND T0.OutQty > 0) THEN SUM(T0.TransValue) ELSE 0 END) AS 'M_12_O'
			FROM OINM T0
			LEFT JOIN OWHS T1 ON T0.Warehouse = T1.WhsCode
			WHERE T0.Warehouse IN ('WP4', 'WP5')
			GROUP BY T0.Warehouse, T1.WhsName, T0.CreateDate, T0.InQty, T0.OutQty
		) A0
		GROUP BY A0.Warehouse, A0.WhsName
		ORDER BY A0.Warehouse";
	$QRY = SAPSelect($SQL);
	$r = 0; $tmp = 0;
	while($result = odbc_fetch_array($QRY)) {
		$r++;
		$WhseID[$r]   = $result['Warehouse'];
		$WhseName[$r] = conutf8($result['WhsName']);
		for($m = 1; $m <= 12; $m++) {
			if($m == 1) {
				$Data[$r][$m]['r1'] = $result['M_00'];
			}else{
				$Data[$r][$m]['r1'] = $tmp;
			}

			if($m < 10) {
				$Data[$r][$m]['r2'] = $result['M_0'.$m.'_I'];
				$Data[$r][$m]['r3'] = $result['M_0'.$m.'_O'];
			}else{
				$Data[$r][$m]['r2'] = $result['M_'.$m.'_I'];
				$Data[$r][$m]['r3'] = $result['M_'.$m.'_O'];
			}
		
			$Data[$r][$m]['r4'] = $Data[$r][$m]['r1']+($Data[$r][$m]['r2']+$Data[$r][$m]['r3']);
			$tmp = $Data[$r][$m]['r4'];
		}
	}

	$Tbody = "";
	if($r != 0) {
		for($i = 1; $i <= $r; $i++) {
			$Tbody .= "<tr>
				<td rowspan='4'><span class='fw-bolder'>".$WhseID[$i]."</span><br>".$WhseName[$i]."</td>
				<td>ต้นทุนคลัง ณ ต้นเดือน</td>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= date("m")){
						$Tbody .= "<td class='text-right'>".number_format($Data[$i][$m]['r1'],2)."</td>";
					}else{
						$Tbody .= "<td class='text-right'>-</td>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr>
				<td>ต้นทุนรับเข้ารวม</td>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= date("m")){
						$Tbody .= "<td class='text-right text-success'>".number_format($Data[$i][$m]['r2'],2)."</td>";
					}else{
						$Tbody .= "<td class='text-right text-success'>-</td>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr>
				<td>ต้นทุนออกรวม</td>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= date("m")){
						$Tbody .= "<td class='text-right text-primary'>".number_format($Data[$i][$m]['r3'],2)."</td>";
					}else{
						$Tbody .= "<td class='text-right text-primary'>-</td>";
					}
				}
			$Tbody .= "</tr>";

			$Tbody .= "<tr class='fw-bolder table-active'>
				<td>ต้นทุนคลัง ณ สิ้นเดือน</td>";
				for($m = 1; $m <= 12; $m++) {
					if($m <= date("m")){
						$Tbody .= "<td class='text-right'><span class='v-detail' style='cursor: pointer;' onclick='Detail(".$Year.",".$m.",\"".$WhseID[$i]."\");'>".number_format($Data[$i][$m]['r4'],2)."</span></td>";
					}else{
						$Tbody .= "<td class='text-right'>-</td>";
					}
				}
			$Tbody .= "</tr>";
		}
	}else{
		$Tbody .= "<tr><td colspan='14'>ไม่มีข้อมูล<td></tr>";
	}
	$arrCol['Tbody'] = $Tbody;
}

if($_GET['a'] == 'Detail') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	$WareH = $_POST['WareH'];

	$SQL = "
		SELECT X2.U_Dim1 AS SaleTeam, X0.*
		FROM (
			SELECT 
				CASE WHEN P0.TransType IN (13,15) THEN 'A' ELSE 'B' END AS 'ORDR',
				P0.TransNum, P0.DocDate,P0.CreateDate,P0.TransType,
				CASE 
					WHEN P0.TransType = 13 THEN (SELECT DISTINCT ISNULL('IV-',W1.BeginStr) FROM OINV W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W1.BeginStr FROM ORIN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W1.BeginStr FROM ODLN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W1.BeginStr FROM ORDN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W1.BeginStr FROM OPDN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W1.BeginStr FROM ORPD W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W1.BeginStr FROM OIGN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W1.BeginStr FROM OIGE W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W1.BeginStr FROM OWTR W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
				END AS BeginStr,
				P0.DocNum,P0.SAPtb,
				CASE 
					WHEN P0.TransType = 13 THEN (SELECT DISTINCT W0.DocEntry FROM OINV W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W0.DocEntry FROM ORIN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W0.DocEntry FROM ODLN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W0.DocEntry FROM ORDN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W0.DocEntry FROM OPDN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W0.DocEntry FROM ORPD W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W0.DocEntry FROM OIGN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W0.DocEntry FROM OIGE W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W0.DocEntry FROM OWTR W0 WHERE W0.DocNum = P0.DocNum)
				END AS DocEntry,
				CASE 
					WHEN P0.TransType = 13 THEN (SELECT DISTINCT W0.BaseEntry FROM INV1 W0 LEFT JOIN OINV W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W0.BaseEntry FROM DLN1 W0 LEFT JOIN ODLN W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W0.BaseEntry FROM PDN1 W0 LEFT JOIN OPDN W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum) 
					ELSE NULL 
				END AS SODocEntry,P0.CardCode,P0.CardName,
				CASE 
					WHEN P0.TransType = 13 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName FROM OINV W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORIN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ODLN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORDN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OPDN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORPD W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OIGN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OIGE W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OWTR W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
				END AS Owner,
				P0.ItemCode, P0.ItemName,P0.WhsCode,P0.InQty,P0.OutQty, P0.TransValue
			FROM (
				SELECT 
					MAX(T0.TransNum) AS TransNum,T0.[DocDate] AS DocDate, T0.[CreateDate] AS 'CreateDate', T0.TransType,
					CASE 
						WHEN T0.TransType = 13 THEN 'OINV'
						WHEN T0.TransType = 14 THEN 'ORIN'
						WHEN T0.TransType = 15 THEN 'ODLN'
						WHEN T0.TransType = 16 THEN 'ORDN'
						WHEN T0.TransType = 20 THEN 'OPDN'
						WHEN T0.TransType = 21 THEN 'ORPD'
						WHEN T0.TransType = 59 THEN 'OIGN'
						WHEN T0.TransType = 60 THEN 'OIGE'
						WHEN T0.TransType = 67 THEN 'OWTR'
					END AS SAPtb,T0.[BASE_REF] AS 'DocNum',
					T0.[ItemCode] AS ItemCode, T0.[Dscription] AS ItemName,T0.[WareHouse] AS WhsCode, SUM(T0.[InQty]) AS InQty, SUM(T0.[OutQty]) AS OutQty,T0.CardCode,T0.CardName,T0.[TransValue]
				FROM OINM T0 
				WHERE (YEAR(T0.[CreateDate]) = $Year AND MONTH(T0.[CreateDate]) = $Month) AND T0.[WareHouse] = '$WareH' AND ((T0.[InQty] + T0.[OutQty]) != 0) 
				GROUP BY T0.[DocDate], T0.[CreateDate], T0.TransType,T0.[BASE_REF],T0.[ItemCode],T0.[Dscription],T0.[WareHouse],T0.CardCode,T0.CardName, T0.[TransValue]
			) P0
		) X0
		LEFT JOIN ORDR X1 ON X0.SODocEntry = X1.DocEntry
		LEFT JOIN OSLP X2 ON X1.SlpCode = X2.SlpCode
		ORDER BY X0.ItemCode, X0.TransNum";
	$QRY = SAPSelect($SQL);
	$Data = ""; $No = 0; $tmpItemCode = "";
	while($result = odbc_fetch_array($QRY)){
		$No++;
		$Chk = ""; $OutQty = "-"; $InQty = "-";

		if($result['OutQty'] != 0) {
			$OutQty = number_format($result['OutQty'],0);
		}
		if($result['InQty'] != 0) {
			$InQty = number_format($result['InQty'],0);
		}

		if($tmpItemCode != $result['ItemCode']) {
			$tmpItemCode = $result['ItemCode'];
			$Chk = "border-top: 1px solid #9A1118 !important;";
			/* INSERT NEW ROW */
			switch($Month) {
				case "1": $PMonth = 12; $PYear = $Year-1; break;
				default : $PMonth = $Month - 1; $PYear = $Year; break;
			}

			$lastdate 	 = cal_days_in_month(CAL_GREGORIAN, $PMonth, $PYear);
			$GetOpenDate = date("Y-m-d",strtotime($PYear."-".$PMonth."-".$lastdate));

			$SQL2 = 
				"SELECT TOP 1
					T0.ItemCode, T1.ItemName,
					ISNULL((SELECT SUM(P0.InQty-P0.OutQty) FROM OINM P0 WHERE P0.ItemCode = T0.ItemCode AND P0.Warehouse = T0.WhsCode AND (P0.CreateDate <= '$GetOpenDate') GROUP BY P0.ItemCode),0) AS 'OpenQty',
					ISNULL((SELECT SUM(P0.TransValue) FROM OINM P0 WHERE P0.ItemCode = T0.ItemCode AND P0.Warehouse = T0.WhsCode AND (P0.CreateDate <= '$GetOpenDate') GROUP BY P0.ItemCode),0) AS 'OpenValue'
				FROM OITW T0
				LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
				WHERE T0.WhsCode = '$WareH' AND T0.ItemCode = '$tmpItemCode'";
			$QRY2 = SAPSelect($SQL2);
			$RST2 = odbc_fetch_array($QRY2);

			$OpenQty   = $RST2['OpenQty'];
			$OpenValue = $RST2['OpenValue'];
			$CloseQty   = $OpenQty;
			$CloseValue = $OpenValue;
		}
		
		$CloseQty   = $CloseQty + ($result['InQty'] - $result['OutQty']);
		$CloseValue = $CloseValue + ($result['TransValue']);
		
		$Data .= "
			<tr>
				<td style='".$Chk."' class='text-center'>".$No."</td>
				<td style='".$Chk."' class='text-center'>".date("d/m/Y",strtotime($result['DocDate']))."</td>
				<td style='".$Chk."' class='text-center'>".$result['BeginStr'].$result['DocNum']."</td>
				<td style='".$Chk."'>".$result['CardCode']." ".conutf8($result['CardName'])."</td>
				<td style='".$Chk."' class='text-center'>".$result['ItemCode']."</td>
				<td style='".$Chk."'>".conutf8($result['ItemName'])."</td>
				<td style='".$Chk."' class='text-center'>".$result['WhsCode']."</td>
				<td style='".$Chk."' class='text-right fw-bolder'>".number_format($OpenQty,0)."</td>
				<td style='".$Chk."' class='text-right text-success'>".$InQty."</td>
				<td style='".$Chk."' class='text-right text-primary'>".$OutQty."</td>
				<td style='".$Chk."' class='text-right fw-bolder'>".$CloseQty."</td>
				<td style='".$Chk."' class='text-right fw-bolder'>".number_format($CloseValue,2)."</td>
			</tr>";
		$OpenQty = $CloseQty;
	}
	if($No == 0) {
		$Data .= "
			<tr>
				<td colspan='12' class='text-center'>ไม่มีข้อมูล :)</td>
			</tr>";
	}
	$arrCol['Data'] = $Data;
	$arrCol['H'] = FullMonth($Month)." ".$Year;
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
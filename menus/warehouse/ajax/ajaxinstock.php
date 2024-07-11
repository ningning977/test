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

if($_GET['a'] == 'Search') {
	/* Filter Management */
	$Tfoot = "";
	if($_POST['status'] != "SALL") {
		if($_POST['status'] == "D") {
			$WhrStatus1 = " AND T0.U_ProductStatus LIKE 'D%'";
			$WhrStatus2 = " AND T1.U_ProductStatus LIKE 'D%'";
		} else {
			$WhrStatus1 = " AND T0.U_ProductStatus LIKE '".$_POST['status']."%'";
			$WhrStatus2 = " AND T1.U_ProductStatus LIKE '".$_POST['status']."%'";
		}
	}else{ 
		$WhrStatus1 = ""; 
		$WhrStatus2 = ""; 
	}

	if($_POST['zero'] == "true") { 
		$WhrZero1 = ""; $WhrZero2 = ""; 
	}else{ $WhrZero1 = " AND (T1.[OnHand] > 0)"; $WhrZero2 = " AND (T0.[OnHand] > 0)"; }

	if($_POST['aging'] == "true") {
		$SQLPurDat = "ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = B0.ItemCode ORDER BY P0.DocEntry DESC),(CASE WHEN B1.LastPurDat = '2022-12-31' OR B1.LastPurDat IS NULL THEN ISNULL(B2.LastPurDat, B1.LastPurDat) ELSE B1.LastPurDat END))";
		$SQLAging  = "DATEDIFF(m,ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = B0.ItemCode ORDER BY P0.DocEntry DESC),(CASE WHEN B1.LastPurDat = '2022-12-31' OR B1.LastPurDat IS NULL THEN ISNULL(B2.LastPurDat, B1.LastPurDat) ELSE B1.LastPurDat END)),GETDATE())";
		// $SQLAging  = "DATEDIFF(m, (CASE WHEN B1.LastPurDat = '2022-12-31' OR B1.LastPurDat IS NULL THEN ISNULL(B2.LastPurDat, B1.LastPurDat) ELSE B1.LastPurDat END),GETDATE())";
	}else{ $SQLPurDat = "''"; $SQLAging  = "'0'"; }

	if($_POST['whsgroup'] == "WALL" || $_POST['whsgroup'] == "WALL") {
		$ListSQL = "SELECT DISTINCT
						B0.ItemCode, B1.CodeBars, B1.ItemName, ISNULL(B1.U_ProductStatus,'K') AS 'Status', B1.InvntryUom,
						SUM(B0.W100) AS 'W100', SUM(B0.W101) AS 'W101', SUM(B0.W102) AS 'W102', SUM(B0.W103) AS 'W103', SUM(B0.W104) AS 'W104',
						SUM(B0.W200) AS 'W200', SUM(B0.W300) AS 'W300', SUM(B0.W400) AS 'W400', SUM(B0.W500) AS 'W500', B1.OnOrder,
						(CASE WHEN B1.LastPurDat = '2022-12-31' OR B1.LastPurDat IS NULL THEN ISNULL(B2.LastPurPrc, B1.LastPurPrc) ELSE B1.LastPurPrc END *1.07) AS 'LastPurPrc', $SQLPurDat AS 'LastPurDat', $SQLAging AS 'Aging'
					FROM (
						SELECT DISTINCT
							A0.ItemCode,
							CASE WHEN A0.WhsGroup IN ('W100') THEN SUM(A0.OnHand) ELSE 0 END AS 'W100',
							CASE WHEN A0.WhsGroup IN ('W101') THEN SUM(A0.OnHand) ELSE 0 END AS 'W101',
							CASE WHEN A0.WhsGroup IN ('W102') THEN SUM(A0.OnHand) ELSE 0 END AS 'W102',
							CASE WHEN A0.WhsGroup IN ('W103') THEN SUM(A0.OnHand) ELSE 0 END AS 'W103',
							CASE WHEN A0.WhsGroup IN ('W104') THEN SUM(A0.OnHand) ELSE 0 END AS 'W104',
							CASE WHEN A0.WhsGroup IN ('W200') THEN SUM(A0.OnHand) ELSE 0 END AS 'W200',
							CASE WHEN A0.WhsGroup IN ('W300') THEN SUM(A0.OnHand) ELSE 0 END AS 'W300',
							CASE WHEN A0.WhsGroup IN ('W400') THEN SUM(A0.OnHand) ELSE 0 END AS 'W400',
							CASE WHEN A0.WhsGroup IN ('W500') THEN SUM(A0.OnHand) ELSE 0 END AS 'W500'
						FROM (
							SELECT
								T0.ItemCode, T1.WhsCode, T2.Location,
								CASE
									WHEN T1.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') THEN 'W100'
									WHEN T1.WhsCode IN ('MT') THEN 'W101'
									WHEN T1.WhsCode IN ('MT2') THEN 'W102'
									WHEN T1.WhsCode IN ('TT-C') THEN 'W103'
									WHEN T1.WhsCode IN ('OUL') THEN 'W104'
									WHEN T1.WhsCode IN ('KB1','KB1.1') THEN 'W200'
									WHEN T2.Location IN (2) THEN 'W300'
									WHEN T2.Location IN (6,7,9) THEN 'W400'
								ELSE 'W500' END AS 'WhsGroup',
								T1.OnHand
							FROM OITM T0
							LEFT JOIN OITW T1 ON T0.ItemCode = T1.ItemCode
							LEFT JOIN OWHS T2 ON T1.WhsCode = T2.WhsCode
							WHERE (T0.InvntItem != 'N' AND T0.ItemCode != '00-000-003') ".$WhrZero1.$WhrStatus1."
						) A0 
						GROUP BY A0.ItemCode, A0.WhsGroup
					) B0 
					LEFT JOIN OITM B1 ON B0.ItemCode = B1.ItemCode 
					LEFT JOIN KBI_DB2022.dbo.OITM B2 ON B0.ItemCode = B2.ItemCode
					GROUP BY B0.ItemCode, B1.CodeBars, B1.ItemName, B1.U_ProductStatus, B1.LastPurPrc, B2.LastPurPrc, B1.LastPurDat, B2.LastPurDat, B1.InvntryUom, B1.OnOrder
					ORDER BY B0.ItemCode";
					// echo $ListSQL;
					// 99-999-602
		$ListQRY = SAPSelect($ListSQL);
		$QuotaSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
						B0.ItemCode,
						SUM(B0.W101) AS 'W101', SUM(B0.W102) AS 'W102', SUM(B0.W103) AS 'W103', SUM(B0.W104) AS 'W104', SUM(B0.W105) AS 'W105'
					FROM (
						SELECT
							A0.ItemCode,
							CASE WHEN A0.WhsGroup IN ('W101') THEN A0.OnHand ELSE 0 END AS 'W101',
							CASE WHEN A0.WhsGroup IN ('W102') THEN A0.OnHand ELSE 0 END AS 'W102',
							CASE WHEN A0.WhsGroup IN ('W103') THEN A0.OnHand ELSE 0 END AS 'W103',
							CASE WHEN A0.WhsGroup IN ('W104') THEN A0.OnHand ELSE 0 END AS 'W104',
							CASE WHEN A0.WhsGroup IN ('W105') THEN A0.OnHand ELSE 0 END AS 'W105'
						FROM (
							SELECT
								T0.ItemCode, T0.CH AS 'WhsCode',
								CASE
									WHEN T0.CH IN ('MT1') THEN 'W101'
									WHEN T0.CH IN ('MT2') THEN 'W102'
									WHEN T0.CH IN ('TTC') THEN 'W103'
									WHEN T0.CH IN ('OUL') THEN 'W104'
									WHEN T0.CH IN ('ONL') THEN 'W105'
								ELSE 'W500' END AS 'WhsGroup',
								T0.OnHand
							FROM whsquota T0
						) A0
					) B0 GROUP BY B0.ItemCode ORDER BY B0.ItemCode";
		// echo $QuotaSQL;
		$QuotaQRY = MySQLSelectX($QuotaSQL);
		while($QuotaRST = mysqli_fetch_array($QuotaQRY)) {
			// $QuotaRST_ItemCode = str_replace("-", "", $QuotaRST['ItemCode']);
			${$QuotaRST['ItemCode']."_Q101"} = $QuotaRST['W101'];
			${$QuotaRST['ItemCode']."_Q102"} = $QuotaRST['W102'];
			${$QuotaRST['ItemCode']."_Q103"} = $QuotaRST['W103'];
			${$QuotaRST['ItemCode']."_Q104"} = $QuotaRST['W104'];
			${$QuotaRST['ItemCode']."_Q105"} = $QuotaRST['W105'];
			
			${"ItemCode_".$QuotaRST['ItemCode']} = $QuotaRST['ItemCode'];
		}

		$Thead = "<tr class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>
						<th width='7%' rowspan='3' class='text-center'>รหัสสินค้า</th>
						<th width='7%' rowspan='3' class='text-center'>บาร์โค้ด</th>
						<th rowspan='3' class='text-center'>ชื่อสินค้า</th> 
						<th width='3%' rowspan='3' class='text-center'>สถานะ</th> 
						<th width='3%' rowspan='3' class='text-center'>หน่วย</th>";
						if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
							$Thead .= "<th width='7%' rowspan='3' class='text-center'>มูลค่า (บาท)<br>(รวม VAT)</th>";
						}
			 $Thead .= "<th colspan='12' class='text-center'>จำนวน (หน่วย)</th>
						<th width='5%' rowspan='3' class='text-center' style='background-color: #f5f5f5;'>AGING (เดือน)</th>";
						if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
							$Thead .= "<th width='7%' rowspan='3' class='text-center'>มูลค่ารวม (บาท)</th>";
						}
		$Thead .= "</tr>
					<tr class='text-center'>
						<th colspan='6' class='text-center' style='background-color: #d9edf7;'>จำนวนคงคลังใน SAP</th>
						<th colspan='6' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>จำนวนสินค้าที่สามารถเบิกได้</th>
					</tr>
					<tr class='text-center'>
						<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>พร้อมขาย KSY/KSM</th>
						<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>พร้อมขาย KBI</th>
						<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>พร้อมขาย SUPPLIER</th>
						<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>มือสอง</th>
						<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>อื่น ๆ</th>
						<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>กำลัง<br>สั่งซื้อ</th>

						<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>ส่วนกลาง</th>
						<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า MT1</th>
						<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า MT2</th>
						<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า TT</th>
						<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า หน้าร้าน</th>
						<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า ออนไลน์</th>
					</tr>";
			
					$ALLSUM = 0;
					$Tbody = "";
					while($ListRST = odbc_fetch_array($ListQRY)) {
						$WALL = ($ListRST['W100'])+($ListRST['W101'])+($ListRST['W102'])+($ListRST['W103'])+($ListRST['W104'])+
								($ListRST['W200'])+($ListRST['W300'])+($ListRST['W400'])+($ListRST['W500']);
						if($ListRST['Aging'] >= 25) {
							$color = "text-danger table-danger";
						}elseif($ListRST['Aging'] >= 7 && $ListRST['Aging'] <= 24) {
							$color = "text-warning table-warning";
						}else {
							$color = "text-success table-success";
						}
						$Tbody .= "<tr class='fw-bold'>
										<td><a href='javascript:void(0);' class='Data-Item' data-item='".$ListRST['ItemCode']."'><i class='fas fa-search-plus'></i></a> ".$ListRST['ItemCode']."</td>
										<td class='text-center'>".$ListRST['CodeBars']."</td>
										<td>".conutf8($ListRST['ItemName'])."</td>
										<td class='text-center'>".$ListRST['Status']."</td>
										<td class='text-center'>".conutf8($ListRST['InvntryUom'])."</td>";
										if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
											$Tbody .= "<td class='text-right'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ListRST['LastPurPrc'],2))." ฿</td>";
										}
										if(isset(${"ItemCode_".$ListRST['ItemCode']})){
											$QuotaW100 = ($ListRST['W100'] - (${$ListRST['ItemCode']."_Q101"}+${$ListRST['ItemCode']."_Q102"}+${$ListRST['ItemCode']."_Q103"}+${$ListRST['ItemCode']."_Q104"}+${$ListRST['ItemCode']."_Q105"}));
											if(${$ListRST['ItemCode']."_Q101"} > 0) {
												$QuotaW101 = ${$ListRST['ItemCode']."_Q101"};
											}else{
												$QuotaW101 = $ListRST['W101'];
											}
											if(${$ListRST['ItemCode']."_Q102"} > 0) {
												$QuotaW102 = ${$ListRST['ItemCode']."_Q102"};
											}else{
												$QuotaW102 = $ListRST['W102'];
											}
											$QuotaW103 = ${$ListRST['ItemCode']."_Q103"};
											$QuotaW104 = ${$ListRST['ItemCode']."_Q104"};
											$QuotaW105 = ${$ListRST['ItemCode']."_Q105"};
										}else{
											$QuotaW100 = $ListRST['W100'];
											$QuotaW101 = 0;
											$QuotaW102 = 0;
											$QuotaW103 = 0;
											$QuotaW104 = 0;
											$QuotaW105 = 0;
										}
										$AUM = $ListRST['W500']+$ListRST['W101']+$ListRST['W102']+$ListRST['W103']+$ListRST['W104'];
										$Tbody .= "<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W100'],0))."</td>
													<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W200'],0))."</td>
													<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W300'],0))."</td>
													<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W400'],0))."</td>
													<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($AUM,0))."</td>
													<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['OnOrder'],0))."</td>
													<td class='text-right text-success'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW100,0))."</td>
													<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW101,0))."</td>
													<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW102,0))."</td>
													<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW103,0))."</td>
													<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW104,0))."</td>
													<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW105,0))."</td>
													<td class='text-center ".$color."' >".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['Aging'],0))."</td>";
													if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
														$Tbody .= "<td class='text-right fw-bolder'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($WALL*$ListRST['LastPurPrc'],2))." ฿</td>";
														$ALLSUM = ($ALLSUM + ($WALL*$ListRST['LastPurPrc']));
													}
						$Tbody .= "</tr>";
					}
					if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
						$Tfoot = "<tr class='fw-bold'>
										<td colspan='18' class='text-right fw-bolder text-primary'>มูลค่ารวมทั้งหมด (บาท)</td>
										<td colspan='2' class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ALLSUM,2))." ฿</td>
									</tr>";
					}
	}elseif($_POST['whsgroup'] == "CMT1" || $_POST['whsgroup'] == "CMT2" || $_POST['whsgroup'] == "CTT2" || $_POST['whsgroup'] == "COUL" || $_POST['whsgroup'] == "CONL") {
		switch($_POST['whsgroup']) {
			case "CMT1": $CH = "MT1"; break;
			case "CMT2": $CH = "MT2"; break;
			case "CTT2": $CH = "TTC"; break;
			case "COUL": $CH = "OUL"; break;
			case "CONL": $CH = "ONL"; break;
		}

		$QtaListQRY = MySQLSelectX("SELECT T0.ItemCode FROM whsquota T0 WHERE T0.CH = '".$CH."'");
		$WhereQuota = "";
		while($QtaListRST = mysqli_fetch_array($QtaListQRY)) {
			$WhereQuota .= "'".$QtaListRST['ItemCode']."',";
		}
		$WhereQuota = substr($WhereQuota,0,-1);

		$ListSQL = "SELECT DISTINCT
						B0.ItemCode, B1.CodeBars, B1.ItemName, ISNULL(B1.U_ProductStatus,'K') AS 'Status', B1.InvntryUom,
						SUM(B0.W100) AS 'W100', SUM(B0.W101) AS 'W101', SUM(B0.W102) AS 'W102', SUM(B0.W103) AS 'W103', SUM(B0.W104) AS 'W104',
						SUM(B0.W200) AS 'W200', SUM(B0.W300) AS 'W300', SUM(B0.W400) AS 'W400', SUM(B0.W500) AS 'W500', B1.OnOrder,
						(CASE WHEN B1.LastPurDat = '2022-12-31' OR B1.LastPurDat IS NULL THEN ISNULL(B2.LastPurPrc, B1.LastPurPrc) ELSE B1.LastPurPrc END *1.07) AS 'LastPurPrc', $SQLPurDat AS 'LastPurDat', $SQLAging AS 'Aging'
					FROM (
						SELECT DISTINCT
							A0.ItemCode,
							CASE WHEN A0.WhsGroup IN ('W100') THEN SUM(A0.OnHand) ELSE 0 END AS 'W100',
							CASE WHEN A0.WhsGroup IN ('W101') THEN SUM(A0.OnHand) ELSE 0 END AS 'W101',
							CASE WHEN A0.WhsGroup IN ('W102') THEN SUM(A0.OnHand) ELSE 0 END AS 'W102',
							CASE WHEN A0.WhsGroup IN ('W103') THEN SUM(A0.OnHand) ELSE 0 END AS 'W103',
							CASE WHEN A0.WhsGroup IN ('W104') THEN SUM(A0.OnHand) ELSE 0 END AS 'W104',
							CASE WHEN A0.WhsGroup IN ('W200') THEN SUM(A0.OnHand) ELSE 0 END AS 'W200',
							CASE WHEN A0.WhsGroup IN ('W300') THEN SUM(A0.OnHand) ELSE 0 END AS 'W300',
							CASE WHEN A0.WhsGroup IN ('W400') THEN SUM(A0.OnHand) ELSE 0 END AS 'W400',
							CASE WHEN A0.WhsGroup IN ('W500') THEN SUM(A0.OnHand) ELSE 0 END AS 'W500'
						FROM (
							SELECT
								T0.ItemCode, T1.WhsCode, T2.Location,
								CASE
									WHEN T1.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') THEN 'W100'
									WHEN T1.WhsCode IN ('MT') THEN 'W101'
									WHEN T1.WhsCode IN ('MT2') THEN 'W102'
									WHEN T1.WhsCode IN ('TT-C') THEN 'W103'
									WHEN T1.WhsCode IN ('OUL') THEN 'W104'
									WHEN T1.WhsCode IN ('KB1','KB1.1') THEN 'W200'
									WHEN T2.Location IN (2) THEN 'W300'
									WHEN T2.Location IN (6,7,9) THEN 'W400'
								ELSE 'W500' END AS 'WhsGroup',
								T1.OnHand
							FROM OITM T0
							LEFT JOIN OITW T1 ON T0.ItemCode = T1.ItemCode
							LEFT JOIN OWHS T2 ON T1.WhsCode = T2.WhsCode
							WHERE T0.ItemCode IN (".$WhereQuota.") $WhrStatus1
						) A0 
						GROUP BY A0.ItemCode, A0.WhsGroup
					) B0 
					LEFT JOIN OITM B1 ON B0.ItemCode = B1.ItemCode 
					LEFT JOIN KBI_DB2022.dbo.OITM B2 ON B0.ItemCode = B2.ItemCode
					GROUP BY B0.ItemCode, B1.CodeBars, B1.ItemName, B1.U_ProductStatus, B1.LastPurPrc, B2.LastPurPrc, B1.LastPurDat, B2.LastPurDat, B1.InvntryUom, B1.OnOrder
					ORDER BY B0.ItemCode";
		$ListQRY = SAPSelect($ListSQL);

		$QuotaSQL = "SELECT 
						B0.ItemCode,
						SUM(B0.W101) AS 'W101', SUM(B0.W102) AS 'W102', SUM(B0.W103) AS 'W103', SUM(B0.W104) AS 'W104', SUM(B0.W105) AS 'W105'
					FROM (
						SELECT
							A0.ItemCode,
							CASE WHEN A0.WhsGroup IN ('W101') THEN A0.OnHand ELSE 0 END AS 'W101',
							CASE WHEN A0.WhsGroup IN ('W102') THEN A0.OnHand ELSE 0 END AS 'W102',
							CASE WHEN A0.WhsGroup IN ('W103') THEN A0.OnHand ELSE 0 END AS 'W103',
							CASE WHEN A0.WhsGroup IN ('W104') THEN A0.OnHand ELSE 0 END AS 'W104',
							CASE WHEN A0.WhsGroup IN ('W105') THEN A0.OnHand ELSE 0 END AS 'W105'
						FROM (
							SELECT
								T0.ItemCode, T0.CH AS 'WhsCode',
								CASE
									WHEN T0.CH IN ('MT1') THEN 'W101'
									WHEN T0.CH IN ('MT2') THEN 'W102'
									WHEN T0.CH IN ('TTC') THEN 'W103'
									WHEN T0.CH IN ('OUL') THEN 'W104'
									WHEN T0.CH IN ('ONL') THEN 'W105'
								ELSE 'W500' END AS 'WhsGroup',
								T0.OnHand
							FROM whsquota T0
						) A0
					) B0 GROUP BY B0.ItemCode ORDER BY B0.ItemCode";
		$QuotaQRY = MySQLSelectX($QuotaSQL);
		while($QuotaRST = mysqli_fetch_array($QuotaQRY)) {
			${$QuotaRST['ItemCode']."_Q101"} = $QuotaRST['W101'];
			${$QuotaRST['ItemCode']."_Q102"} = $QuotaRST['W102'];
			${$QuotaRST['ItemCode']."_Q103"} = $QuotaRST['W103'];
			${$QuotaRST['ItemCode']."_Q104"} = $QuotaRST['W104'];
			${$QuotaRST['ItemCode']."_Q105"} = $QuotaRST['W105'];

			${"ItemCode_".$QuotaRST['ItemCode']} = $QuotaRST['ItemCode'];
		}
		$Thead ="<tr class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>
					<th width='7%' rowspan='3' class='text-center'>รหัสสินค้า</th>
					<th width='7%' rowspan='3' class='text-center'>บาร์โค้ด</th>
					<th rowspan='3' class='text-center'>ชื่อสินค้า</th> 
					<th width='3%' rowspan='3' class='text-center'>สถานะ</th> 
					<th width='3%' rowspan='3' class='text-center'>หน่วย</th>";
					if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
						$Thead .= "<th width='7%' rowspan='3' class='text-center'>มูลค่า (บาท)</th>";
					}
		$Thead .= "<th colspan='12' class='text-center'>จำนวน (หน่วย)</th>
					<th width='5%' rowspan='3' class='text-center' style='background-color: #f5f5f5;'>AGING (เดือน)</th>";
					if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
						$Thead .= "<th width='7%' rowspan='3' class='text-center'>มูลค่ารวม (บาท)</th>";
					}
		$Thead .= "</tr>
					<tr class='text-center'>
						<th colspan='6' class='text-center' style='background-color: #d9edf7;'>จำนวนคงคลังใน SAP</th>
						<th colspan='6' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>จำนวนสินค้าที่สามารถเบิกได้</th>
					</tr>
					<tr class='text-center'>
							<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>พร้อมขาย KSY/KSM</th>
							<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>พร้อมขาย KBI</th>
							<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>พร้อมขาย SUPPLIER</th>
							<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>มือสอง</th>
							<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>อื่น ๆ</th>
							<th width='4.2%' class='text-center' style='background-color: #d9edf7;'>กำลัง<br>สั่งซื้อ</th>

							<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>ส่วนกลาง</th>
							<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า MT1</th>
							<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า MT2</th>
							<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า TT</th>
							<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า หน้าร้าน</th>
							<th width='4.2%' class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>โควต้า ออนไลน์</th>
						</tr>";
		$ALLSUM = 0;
		$Tbody = "";
		while($ListRST = odbc_fetch_array($ListQRY)) {
			// $ListRST['OnOrder']
			$WALL = ($ListRST['W100'])+($ListRST['W101'])+($ListRST['W102'])+($ListRST['W103'])+($ListRST['W104'])+
					($ListRST['W200'])+($ListRST['W300'])+($ListRST['W400'])+($ListRST['W500']);
			if($ListRST['Aging'] >= 25) {
				$color = "text-danger table-danger";
			}elseif($ListRST['Aging'] >= 7 && $ListRST['Aging'] <= 24) {
				$color = "text-warning table-warning";
			}else {
				$color = "text-success table-success";
			}
			$Tbody .= "<tr class='fw-bold'>
				<td><a href='javascript:void(0);' class='Data-Item' data-item='".$ListRST['ItemCode']."'><i class='fas fa-search-plus'></i></a> ".$ListRST['ItemCode']."</td>
				<td class='text-center'>".$ListRST['CodeBars']."</td>
				<td>".conutf8($ListRST['ItemName'])."</td>
				<td class='text-center'>".$ListRST['Status']."</td>
				<td class='text-center'>".conutf8($ListRST['InvntryUom'])."</td>";
				if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
					$Tbody .= "<td class='text-right'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ListRST['LastPurPrc'],2))." ฿</td>";
				}
				if(isset(${"ItemCode_".$ListRST['ItemCode']})){
					$QuotaW100 = ($ListRST['W100'] - (${$ListRST['ItemCode']."_Q101"}+${$ListRST['ItemCode']."_Q102"}+${$ListRST['ItemCode']."_Q103"}+${$ListRST['ItemCode']."_Q104"}+${$ListRST['ItemCode']."_Q105"}));
					if(${$ListRST['ItemCode']."_Q101"} > 0) {
						$QuotaW101 = ${$ListRST['ItemCode']."_Q101"};
					}else{
						$QuotaW101 = $ListRST['W101'];
					}
					if(${$ListRST['ItemCode']."_Q102"} > 0) {
						$QuotaW102 = ${$ListRST['ItemCode']."_Q102"};
					}else{
						$QuotaW102 = $ListRST['W102'];
					}
					$QuotaW103 = ${$ListRST['ItemCode']."_Q103"};
					$QuotaW104 = ${$ListRST['ItemCode']."_Q104"};
					$QuotaW105 = ${$ListRST['ItemCode']."_Q105"};
				}else{
					$QuotaW100 = $ListRST['W100'];
					$QuotaW101 = 0;
					$QuotaW102 = 0;
					$QuotaW103 = 0;
					$QuotaW104 = 0;
					$QuotaW105 = 0;
				}
				$AUM = $ListRST['W500']+$ListRST['W101']+$ListRST['W102']+$ListRST['W103']+$ListRST['W104'];
				
				$Tbody .= "<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W100'],0))."</td>
							<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W200'],0))."</td>
							<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W300'],0))."</td>
							<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['W400'],0))."</td>
							<td class='text-right' style='background-color: #d9edf7;'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($AUM,0))."</td>
							<td class='text-right' style='background-color: #d9edf7;'>".number_format($ListRST['OnOrder'],0)."</td>
							<td class='text-right text-success'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW100,0))."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW101,0))."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW102,0))."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW103,0))."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW104,0))."</td>
							<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($QuotaW105,0))."</td>
							<td class='text-center ".$color."' >".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['Aging'],0))."</td>";
							if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
								$Tbody .= "<td class='text-right fw-bolder'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($WALL*$ListRST['LastPurPrc'],2))." ฿</td>";
								$ALLSUM = ($ALLSUM + ($WALL*$ListRST['LastPurPrc']));
							}
			$Tbody .= "</tr>";
		}
		if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
			$Tfoot .= "<tr class='fw-bold'>
							<td colspan='18' class='text-right fw-bolder text-primary'>มูลค่ารวมทั้งหมด (บาท)</td>
							<td colspan='2' class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ALLSUM,2))." ฿</td>
						</tr>";
		}
	}elseif(($_POST['whsgroup'] != "WALL" && $_POST['status'] == "SALL") || ($_POST['whsgroup'] != "WALL" && $_POST['status'] != "SALL")) {
		if($_POST['status'] != "SALL") {
			if($_POST['status'] == "D") {
				$WhrStatus = " AND T1.U_ProductStatus LIKE 'D%'";
			} else {
				$WhrStatus = " AND T1.U_ProductStatus = '".$_POST['status']."'";
			}
		} else {
			$WhrStatus = "";
		}
		switch($_POST['whsgroup']) {
			case "GW100":
				$SAPWhre = " AND T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4')";
				$ERFWhre = " AND T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4')";
			break;
	
			case "GW200":
				$SAPWhre = " AND T0.WhsCode IN ('KB1','KB1.1')";
				$ERFWhre = " AND T0.WhsCode IN ('KB1','KB1.1')";
			break;
	
			case "GW300":
				$SAPWhre = " AND T2.Location IN (2)";
				$ERFWhre = " AND T0.WhsCode IN ('AGT','IMAX','JSI','KN','KTW','NST','PLA','PU','RST','SY','TC','VRK','YEE','YMT')";
			break;
	
			case "GW400":
				$SAPWhre = " AND T2.Location IN (6,7,9)";
				$ERFWhre = " AND (T0.WhsCode LIKE 'B%' OR T0.WhsCode LIKE 'K%' OR T0.WhsCode LIKE 'M%' OR T0.WhsCode = 'MK01' OR T0.WhsCode = 'SALE' OR T0.WhsCode = 'TT' OR T0.WhsCode LIKE 'WA%' OR T0.WhsCode LIKE 'WB%' OR T0.WhsCode LIKE 'WC%' OR T0.WhsCode LIKE 'WD%' OR T0.WhsCode LIKE 'WK%' OR T0.WhsCode LIKE 'WM%' OR T0.WhsCode = 'WP01' OR T0.WhsCode LIKE 'RD%' OR T0.WhsCode LIKE 'KB5%' OR T0.WhsCode LIKE 'KB6%' OR T0.WhsCode = 'KB7')";
			break;
	
			case "GW500":
				$SAPWhre = " AND (T0.WhsCode NOT IN ('KB2','KSY','KSM','KBM','KB4','MT','MT2','TT-C','OUL','KB1','KB1.1') AND T2.Location NOT IN (2,6,7,9))";
				$ERFWhre = " AND (T0.WhsCode NOT IN ('KB2','KSY','KSM','KBM','KB4','MT','MT2','TT-C','OUL','KB1','KB1.1','AGT','IMAX','JSI','KN','KTW','NST','PLA','PU','RST','SY','TC','VRK','YEE','YMT') AND 
							(T0.WhsCode NOT LIKE 'B%' OR T0.WhsCode NOT LIKE 'K%' OR T0.WhsCode NOT LIKE 'M%' OR T0.WhsCode != 'MK01' OR T0.WhsCode != 'SALE' OR T0.WhsCode != 'TT' OR T0.WhsCode NOT LIKE 'WA%' OR T0.WhsCode NOT LIKE 'WB%' OR T0.WhsCode NOT LIKE 'WC%' OR T0.WhsCode NOT LIKE 'WD%' OR T0.WhsCode NOT LIKE 'WK%' OR T0.WhsCode NOT LIKE 'WM%' OR T0.WhsCode != 'WP01' OR T0.WhsCode NOT LIKE 'RD%' OR T0.WhsCode NOT LIKE 'KB5%' OR T0.WhsCode NOT LIKE 'KB6%' OR T0.WhsCode != 'KB7'))";
			break;
	
			default:
				$SAPWhre = " AND T0.WhsCode = '".$_POST['whsgroup']."'";
				$ERFWhre = " AND T0.WhsCode = '".$_POST['whsgroup']."'";
			break;
		}

		$ListSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
						T0.ItemCode, T1.CodeBars, T1.ItemName, T1.U_ProductStatus AS 'Status', T1.InvntryUom,
						(CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07) AS 'LastPurPrc', ".str_replace(array("B0","B1","B2"),array("T0","T1","T3"),$SQLAging)." AS 'Aging',
						T0.OnHand, T0.OnOrder, T0.WhsCode, T2.WhsName
					FROM OITW T0
					LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
					LEFT JOIN OWHS T2 ON T0.WhsCode = T2.WhsCode
					LEFT JOIN KBI_DB2022.dbo.OITM T3 ON T0.ItemCode = T3.ItemCode
					WHERE (T1.InvntItem != 'N' AND T0.ItemCode != '00-000-003') ".$WhrZero2.$SAPWhre.$WhrStatus2."
					ORDER BY T0.WhsCode, T0.ItemCode";
					// echo $ListSQL;
		$ListQRY = SAPSelect($ListSQL);

		$PickSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
						T0.ItemCode, T0.WhsCode, SUM(T0.Qty) AS 'Qty',
						SUM(T0.OpenQty) AS 'OpenQty'
					FROM picker_sodetail T0
					LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry
					WHERE (T1.DocType IN ('ORDR', 'OWAS', 'OWAB') AND (T1.StatusDoc BETWEEN 2 AND 8)) ".$ERFWhre."
					GROUP BY T0.ItemCode, T0.WhsCode";
					// echo $PickSQL;
		$PickQRY = MySQLSelectX($PickSQL);
		while($PickRST = mysqli_fetch_array($PickQRY)) {
			${$PickRST['ItemCode']."_".$PickRST['WhsCode']."_Qty"} = $PickRST['Qty'];
        	${$PickRST['ItemCode']."_".$PickRST['WhsCode']."_OpenQty"} = $PickRST['OpenQty'];
		}

		$colspan = 12;
		$Thead = "<tr class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>
					<th width='7%' rowspan='2' class='text-center'>รหัสสินค้า</th>
					<th width='7%' rowspan='2' class='text-center'>บาร์โค้ด</th>
					<th rowspan='2'>ชื่อสินค้า</th> 
					<th width='3%' rowspan='2' class='text-center'>สถานะ</th> 
					<th width='3%' rowspan='2' class='text-center'>หน่วย</th>";
					if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
						$Thead .= "<th width='5%' rowspan='2' class='text-center'>มูลค่า (บาท)</th>";
						$colspan = 14;
					}
		$Thead .= "<th width='5%' rowspan='2'>คลังสินค้า</th>
					<th colspan='5' class='text-center'>จำนวน (หน่วย)</th>
					<th width='5%' rowspan='2' class='text-center' style='background-color: #f5f5f5;'>AGING (เดือน)</th>";
					if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
						$Thead .= "<th width='7%' rowspan='2' class='text-center'>มูลค่ารวม (บาท)</th>";
					}
		$Thead .= "</tr>";
		$Thead .= "<tr class='text-center' style='background-color: rgba(245, 245, 245, 0.43);'>
						<th width='7.5%' class='text-center'>คงคลัง</th>
						<th width='7.5%' class='text-center'>รอเบิก</th>
						<th width='7.5%' class='text-center'>เบิกแล้ว</th>
						<th width='7.5%' class='text-center'>คงเหลือ</th>
						<th width='7.5%' class='text-center'>กำลังสั่งซื้อ</th>
					</tr>";
		$tempWhs = "";	
		$ALLSUM = 0;
		$Tbody = "";
		while($ListRST = odbc_fetch_array($ListQRY)) {
			// $ListRST['OnOrder']
			if($tempWhs != $ListRST['WhsCode']) {
				$tempWhs = $ListRST['WhsCode'];
				$Tbody.="<tr class='active'>
							<th class='fw-bolder text-primary text-center' style='background-color: rgba(189, 189, 189, 0.15);'><i class='fas fa-warehouse'></i></th>
							<th class='fw-bolder text-primary text-center' style='background-color: rgba(189, 189, 189, 0.15);'>".conutf8($ListRST['WhsCode'])."</th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'>".conutf8($ListRST['WhsName'])."</th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
							<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>";
							if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
								$Tbody.="<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>
										<th class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'></th>";
							}
				$Tbody.="</tr>";   
			}

			if($ListRST['Aging'] >= 25) {
				$color = "text-danger table-danger";
			} elseif($ListRST['Aging'] >= 7 && $ListRST['Aging'] <= 24) {
				$color = "text-warning table-warning";
			} else {
				$color = "text-success table-success";
			}
			if(isset(${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_Qty"})){
				$DT1 = ${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_Qty"}-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
				$DT2 = ${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
				$DT3 = $ListRST['OnHand']-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
				$SUM = ($ListRST['OnHand']-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"})*$ListRST['LastPurPrc'];
			}else{
				$DT1 = 0;
				$DT2 = 0;
				$DT3 = $ListRST['OnHand'];
				$SUM = ($ListRST['OnHand']*$ListRST['LastPurPrc']);
			}
			$Tbody .= "<tr class='fw-bold'>
							<td><a href='javascript:void(0);' class='Data-Item' data-item='".$ListRST['ItemCode']."'><i class='fas fa-search-plus'></i></a> ".$ListRST['ItemCode']."</td>
							<td class='text-center'>".$ListRST['CodeBars']."</td>
							<td>".conutf8($ListRST['ItemName'])."</td>
							<td class='text-center'>".$ListRST['Status']."</td>
							<td class='text-center'>".conutf8($ListRST['InvntryUom'])."</td>";
							if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
								$Tbody .= "<td class='text-right'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ListRST['LastPurPrc'],2))." ฿</td>";
							}
							$Tbody .= "<td class='text-center'>".conutf8($ListRST['WhsCode'])."</td>
										<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['OnHand'],0))."</td>
										<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT1,0))."</td>
										<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT2,0))."</td>
										<td class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT3,0))."</td>
										<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['OnOrder'],0))."</td>
										<td class='text-center ".$color."' >".preg_replace('/\b'.'0'.'\b/i',"-",number_format($ListRST['Aging'],0))."</td>";
										if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
											$Tbody .= "<td class='text-right fw-bolder'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($SUM,2))." ฿</td>";
											$ALLSUM = ($ALLSUM + $SUM);
										}
			$Tbody .= "</tr>";
		}
		if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
			$Tfoot .= "<tr class='fw-bold'>
							<td colspan='12' class='text-right fw-bolder text-primary'>มูลค่ารวมทั้งหมด (บาท)</td>
							<td colspan='2' class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ALLSUM,2))." ฿</td>
						</tr>";
		}
	}

	$arrCol['Thead'] = $Thead;
	$arrCol['Tbody'] = $Tbody;
	$arrCol['Tfoot'] = $Tfoot;
	// $arrCol['output'] = $output;
}

if($_GET['a'] == 'DataDetail'){
	// echo $_POST['ItemCode'];
	/* ข้อมูลสินค้า */
	$arrCol['ItemCode'] = $_POST['ItemCode'];
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
                    (CASE WHEN T0.LastPurDat = '2022-12-31' OR T0.LastPurDat IS NULL THEN ISNULL(T4.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc', 
                    ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC), CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T4.LastPurDat, T0.LastPurDat) ELSE T0.LastPurDat END) AS 'LastPurDat',
					DATEDIFF(m,ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC), CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T4.LastPurDat, T0.LastPurDat) ELSE T0.LastPurDat END),GETDATE()) AS 'Aging',
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
	$output1 = "<tr>
					<th width='15%'>รหัสสินค้า</th>
					<td width='35%'>".$ItemRST['ItemCode']."</td>
					<th width='15%'>บาร์โค้ด</th>
					<td width='35%'>".$ItemRST['CodeBars']."</td>
				</tr>
				<tr>
					<th>ชื่อสินค้า</th>
					<td colspan='3'>".conutf8($ItemRST['ItemName'])."</td>
				</tr>
				<tr>
					<th>ยี่ห้อ</th>
					<td>".conutf8($ItemRST['Brand'])."</td>
					<th>กลุ่มสินค้า</th>
					<td>".conutf8($ItemRST['MainGroup'])." > ".conutf8($ItemRST['SupGroup'])."</td>
				</tr>
				<tr>
					<th>หน่วย</th>
					<td>".conutf8($ItemRST['InvntryUom'])."</td>
					<th>สถานะ</th>
					<td>".$ItemRST['U_ProductStatus']."</td>
				</tr>";
				if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052' OR $_SESSION['LvCode'] == 'LV107') {
					$output1 .= "<tr>
									<th>ต้นทุนล่าสุด (รวม VAT)</th>
									<td>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($ItemRST['LastPurPrc'],2))." ฿</td>
									<th>วันที่เข้าล่าสุด (Aging)</th>
									<td>".date("d/m/Y", strtotime($ItemRST['LastPurDat']))." (".number_format($ItemRST['Aging'],0)." เดือน)</td>
								</tr>";
				}else{
					$output1 .= "<tr>
									<th>วันที่เข้าล่าสุด (Aging)</th>
									<td colspan='3'>".date("d/m/Y", strtotime($ItemRST['LastPurDat']))." (".number_format($ItemRST['Aging'],0)." เดือน)</td>
								</tr>";
				}
	/* OUT PUT 1 => ข้อมูลสินค้า */
	$arrCol['output1'] = $output1;

	/* จำนวนสินค้าคงคลัง SAP */
    $WhseSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
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
	$tempGroup = "";
	$PickSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', 
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

	$output2 = "<table class='table table-sm table-bordered rounded rounded-3 overflow-hidden'>
					<thead style='font-size: 13px;'>
						<tr class='text-center'>
							<th rowspan='2'>ชื่อคลัง</th>
							<th colspan='5'>จำนวน (หน่วย)</th>";
							if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
								$output2 .= "<th width='15%' rowspan='2'>มูลค่ารวม</th>";
							}
			$output2 .= "</tr>
						<tr class='text-center'>
							<th width='12.5%'>คงคลัง</th>
							<th width='12.5%'>รอเบิก</th>
							<th width='12.5%'>เบิกแล้ว</th>
							<th width='12.5%'>คงเหลือ</th>
							<th width='12.5%'>กำลังสั่งซื้อ</th>
						</tr>
					</thead>
					<tbody style='font-size: 12px;'>";
            $Chk_KB4 = "N";
			while($WhseRST = odbc_fetch_array($WhseQRY)) {
				if($tempGroup != $WhseRST['WhsGroup']) {
					$tempGroup = $WhseRST['WhsGroup'];
					$output2 .= "<tr><td colspan='7' class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'>".WhsGroupName($tempGroup)."</td></tr>";
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
				$output2 .= "<tr>
								<td>".conutf8($WhseRST['WhsCode'])." - ".conutf8($WhseRST['WhsName'])."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnHand'],0))."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT1,0))."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT2,0))."</td>
								<td class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT3,0))."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnOrder'],0))."</td>";
								if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 19 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
									$output2 .= "<td class='text-right fw-bolder'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($DT4,2))." ฿</td>";
								}
				$output2 .= "</tr>";

				// ถ้ามีคลัง KB4 ($WhseRST['WhsCode'])
                if($WhseRST['WhsCode'] == 'KB4') {
                    $Chk_KB4 = "Y";
                }
			}
		$output2 .= "</tbody>
				</table>";
	/* OUT PUT 2 => จำนวนสินค้าคงคลัง SAP */
	$arrCol['output2'] = $output2;

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
	if ($_SESSION['DeptCode'] == 'DP003' || $_SESSION['DeptCode'] == 'DP005' || $_SESSION['DeptCode'] == 'DP006' || $_SESSION['DeptCode'] == 'DP007' || $_SESSION['DeptCode'] == 'DP008') {
		$Dis = " ";
	}else{
		$Dis = " disabled ";
	}

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
    

	/* ------4------ */
	$DisMkt = "disabled";

    $DisTTC = "disabled";
    $DisMT1 = "disabled";
    $DisMT2 = "disabled";
    $DisOUL = "disabled";
    $DisONL = "disabled";
    $MKtNameShow = "";
    $TTCNameShow = "";
    $MT1NameShow = "";
    $MT2NameShow = "";
    $OULNameShow = "";
    $ONLNameShow = "";

	$MKT_O = ""; $MKT_R = ""; $MKT_Y = ""; $MKT_N = ""; $MKT_C = "";
	$TCC_O = ""; $TTC_R = ""; $TTC_Y = ""; $TTC_N = ""; $TTC_C = "";
	$MT1_O = ""; $MT1_R = ""; $MT1_Y = ""; $MT1_N = ""; $MT1_C = "";
	$MT2_O = ""; $MT2_R = ""; $MT2_Y = ""; $MT2_N = ""; $MT2_C = "";
	$OUL_O = ""; $OUL_R = ""; $OUL_Y = ""; $OUL_N = ""; $OUL_C = "";
	$ONL_O = ""; $ONL_R = ""; $ONL_Y = ""; $ONL_N = ""; $ONL_C = "";


	if(CHKRowDB($sql3) == 0) {//ไม่เจอเอกสารใหม่
		switch ($_SESSION['LvCode']){
			case 'LV010' :
			case 'LV011' : 
			case 'LV012' : 
				$DisMkt = "";
				$MKT_R = "selected";
				$MKtNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
				
				break;
			case 'LV027' :
			case 'LV036' :
			case 'LV034' :
				$DisTTC = "";
				$TTC_R = "selected";
				$TTCNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
				break;
			case 'LV038' :
			case 'LV040' :
			case 'LV041' :
				$DisMT1 = "";
				$MT1_R = "selected";
				$MT1NameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
				break;
			case 'LV045' :
			case 'LV046' :
			case 'LV047' :
			case 'LV107' :	
				$DisMT2 = "";
				$MT2_R = "selected";
				$MT2NameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
				break;
			case 'LV051' :
			case 'LV052' :
			case 'LV053' :
			case 'LV054' :
				$DisOUL = "";
				$OUL_R = "selected";
				$OULNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
				break;
			case 'LV104' :
				$DisONL = "";
				$ONL_R = "selected";
				$ONLNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
				break;

		}
		/* ไม่มีข้อมูล */
		$output4 = "<thead style='font-size: 13px;'>
							<tr class='text-center'>
								<th width='20%' style='color: blue;'></th>
								<th width='15%'>ผลการดำเนินการ</th>
								<th width='42%'>เหตุผล</th>
								<th width='18%'>ชื่อ</th>
								<th width='5%'></th>
							</tr>
						</thead>
						<tbody style='font-size: 12px;'>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการการตลาด</td>
								<td>
									<select class='form-select form-select-sm' id='MgrMktApp' ".$DisMkt.">
										<option value=''></option>
										<option value='O' ".$MKT_O.">รอพิจารณา</option>        
										<option value='R' ".$MKT_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$MKT_Y.">อนุมัติ</option>
										<option value='N' ".$MKT_N.">ไม่อนุมัติ</option>
										<option value='C' ".$MKT_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMktRemark' id='MgrMktRemark' value='' ".$DisMkt."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMktName' id='MgrMktName' value='".$MKtNameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('Mkt')\" ".$DisMkt.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการขาย TT</td>
								<td>
									<select class='form-select form-select-sm' id='MgrTTCApp' ".$DisTTC.">
										<option value=''></option>
										<option value='O' ".$TCC_O.">รอพิจารณา</option>            
										<option value='R' ".$TTC_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$TTC_Y.">อนุมัติ</option>
										<option value='N' ".$TTC_N.">ไม่อนุมัติ</option>
										<option value='C' ".$TTC_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrTTCRemark' id='MgrTTCRemark' value='' ".$DisTTC."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrTTCName' id='MgrTTCName' value='".$TTCNameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('TTC')\" ".$DisTTC.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการขาย MT1</td>
								<td>
									<select class='form-select form-select-sm' id='MgrMT1App' ".$DisMT1.">
										<option value=''></option>
										<option value='O' ".$MT1_O.">รอพิจารณา</option>            
										<option value='R' ".$MT1_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$MT1_Y.">อนุมัติ</option>
										<option value='N' ".$MT1_N.">ไม่อนุมัติ</option>
										<option value='C' ".$MT1_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMT1Remark' id='MgrMT1Remark' value='' ".$DisMT1."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMT1name' id='MgrMT1name' value='".$MT1NameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('MT1')\" ".$DisMT1.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการขาย MT2</td>
								<td>
									<select class='form-select form-select-sm' id='MgrMT2App' ".$DisMT2.">
										<option value=''></option>
										<option value='O' ".$MT2_O.">รอพิจารณา</option>           
										<option value='R' ".$MT2_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$MT2_Y.">อนุมัติ</option>
										<option value='N' ".$MT2_N.">ไม่อนุมัติ</option>
										<option value='C' ".$MT2_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMT2Remark' id='MgrMT2Remark' value='' ".$DisMT2."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMT2Name' id='MgrMT2Name' value='".$MT2NameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('MT2')\" ".$DisMT2.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการการขาย หน้าร้าน / กทม.</td>
								<td>
									<select class='form-select form-select-sm' id='MgrOULApp' ".$DisOUL.">
										<option value=''></option>
										<option value='O' ".$OUL_O.">รอพิจารณา</option>            
										<option value='R' ".$OUL_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$OUL_Y.">อนุมัติ</option>
										<option value='N' ".$OUL_N.">ไม่อนุมัติ</option>
										<option value='C' ".$OUL_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrOULRemark' id='MgrOULRemark' value='' ".$DisOUL."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrOULName' id='MgrOULName' value='".$OULNameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('OUL')\" ".$DisOUL.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ขาย Online</td>
								<td>
									<select class='form-select form-select-sm' id='MgrONLApp' ".$DisONL.">
										<option value=''></option>
										<option value='O' ".$ONL_O.">รอพิจารณา</option>            
										<option value='R' ".$ONL_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$ONL_Y.">อนุมัติ</option>
										<option value='N' ".$ONL_N.">ไม่อนุมัติ</option>
										<option value='C' ".$ONL_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrONLRemark' id='MgrONLRemark' value='' ".$DisONL."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrONLName' id='MgrONLName' value='".$ONLNameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('ONL')\" ".$DisONL.">บันทึก</button></td>
							</tr>
						</tbody>";
	}else{ //เจอเอกสาร
		$AppState = str_split($DataMove['AppState']);
		if(count($AppState) == 5) {
			/* ฝ่ายการตลาด */
			if (($AppState[0] == 'A' OR $AppState[0] == 'Y' OR $AppState[0] == 'N' OR $AppState[0] == 'R' OR $AppState[0] == 'C')){
				switch ($AppState[0]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV010' OR $_SESSION['LvCode'] == 'LV011'){
							$DisMkt = "";
							$MKT_Y = "selected";
							$MKtNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$MKT_O = "selected";
						}
						break;
					case 'R' :
						$MKT_R = "selected";
						$MKtNameShow = $DataMove['MKT_Name'];
						break;
					case 'Y' :
						$MKT_Y = "selected";
						$MKtNameShow = $DataMove['MKT_Name'];
						break;
					case 'N' :
						$MKT_N = "selected";
						$MKtNameShow = $DataMove['MKT_Name'];
						break;
					case 'C' :
						$MKT_C = "selected";
						$MKtNameShow = $DataMove['MKT_Name'];
						break;
				}
			}

			/* ฝ่าย TTC */
			if (($AppState[1] == 'A' OR $AppState[1] == 'Y' OR $AppState[1] == 'N' OR $AppState[1] == 'R' OR $AppState[1] == 'C') ){
				switch ($AppState[1]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV027' OR $_SESSION['LvCode'] == 'LV034'){
							$DisTTC = "";
							if ($AppState[0] == 'R'){
								$TTC_C = "selected";
								$TTC_R = "disabled";  
								$TTC_Y = "disabled";
								$TTC_N = "disabled";
							}else{
								$TTC_Y = "selected";
							}
							
							$TTCNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$TTC_O = "selected";
						}
						break;
					case 'R' :
						$TTC_R = "selected";
						$TTCNameShow = $DataMove['TTC_Name'];
						break;
					case 'Y' :
						$TTC_Y = "selected";
						$TTCNameShow = $DataMove['TTC_Name'];
						break;
					case 'N' :
						$TTC_N = "selected";
						$TTCNameShow = $DataMove['TTC_Name'];
						break;
					case 'C' :
						$TTC_C = "selected";
						$TTCNameShow = $DataMove['TTC_Name'];
						break;
				}
			}

			/* ฝ่าย MT1 */
			if (($AppState[2] == 'A' OR $AppState[2] == 'Y' OR $AppState[2] == 'N' OR $AppState[2] == 'R' OR $AppState[2] == 'C')){
				switch ($AppState[2]){
					case 'A' :
						// echo $_SESSION['LvCode'];
						if ($_SESSION['LvCode'] == 'LV038' OR $_SESSION['LvCode'] == 'LV040'){
							$DisMT1 = "";
							if ($AppState[0] == 'R'){
								$MT1_C = "selected"; 
								$MT1_R = "disabled";  
								$MT1_Y = "disabled";
								$MT1_N = "disabled";
							}else{
								$MT1_Y = " selected "; 
							}
							$MT1NameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$MT1_O = "selected";
						}
						break;
					case 'R' :
						$MT1_R = "selected";
						$MT1NameShow = $DataMove['MT1_Name'];
						break;
					case 'Y' :
						$MT1_Y = "selected";
						$MT1NameShow = $DataMove['MT1_Name'];
						break;
					case 'N' :
						$MT1_N = "selected";
						$MT1NameShow = $DataMove['MT1_Name'];
						break;
					case 'C' :
						$MT1_C = "selected";
						$MT1NameShow = $DataMove['MT1_Name'];
						break;
				}
			}

			/* ฝ่าย MT2 */
			if (($AppState[3] == 'A' OR $AppState[3] == 'Y' OR $AppState[3] == 'N' OR $AppState[3] == 'R' OR $AppState[3] == 'C')){
				switch ($AppState[3]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV045' OR $_SESSION['LvCode'] == 'LV046' OR $_SESSION['LvCode'] == 'LV107'){
							$DisMT2 = "";
							if ($AppState[0] == 'R'){
								$MT2_C = "selected";
								$MT2_R = "disabled";  
								$MT2_Y = "disabled";
								$MT2_N = "disabled";
							}else{
								$MT2_Y = "selected"; 
							}
							$MT2NameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$MT2_O = "selected";
						}
						break;
					case 'R' :
						$MT2_R = "selected";
						$MT2NameShow = $DataMove['MT2_Name'];
						break;
					case 'Y' :
						$MT2_Y = "selected";
						$MT2NameShow = $DataMove['MT2_Name'];
						break;
					case 'N' :
						$MT2_N = "selected";
						$MT2NameShow = $DataMove['MT2_Name'];
						break;
					case 'C' :
						$MT2_C = "selected";
						$MT2NameShow = $DataMove['MT2_Name'];
						break;
				}
			}

			/* ฝ่าย OUL */
			if (($AppState[4] == 'A' OR $AppState[4] == 'Y' OR $AppState[4] == 'N' OR $AppState[4] == 'R' OR $AppState[4] == 'C')){
				switch ($AppState[4]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV051' OR $_SESSION['LvCode'] == 'LV052'){
							$DisOUL = "";
							if ($AppState[0] == 'R'){
								$OUL_C = "selected";
								$OUL_R = "disabled";  
								$OUL_Y = "disabled";
								$OUL_N = "disabled";
							}else{
								$OUL_Y = "selected";   
							}
							$OULNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$OUL_O = "selected";
						}
						break;
					case 'R' :
						$OUL_R = "selected";
						$OULNameShow = $DataMove['OUL_Name'];
						break;
					case 'Y' :
						$OUL_Y = "selected";
						$OULNameShow = $DataMove['OUL_Name'];
						break;
					case 'N' :
						$OUL_N = "selected";
						$OULNameShow = $DataMove['OUL_Name'];
						break;
					case 'C' :
						$OUL_C = "selected";
						$OULNameShow = $DataMove['OUL_Name'];
						break;
				}
			}
			/* Online */
			if (($AppState[5] == 'A' OR $AppState[5] == 'Y' OR $AppState[5] == 'N' OR $AppState[5] == 'R' OR $AppState[5] == 'C')){
				switch ($AppState[4]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV104'){
							$DisONL = "";
							if ($AppState[0] == 'R'){
								$ONL_C = "selected";
								$ONL_R = "disabled";  
								$ONL_Y = "disabled";
								$ONL_N = "disabled";
							}else{
								$ONL_Y = "selected";   
							}
							$ONLNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$ONL_O = "selected";
						}
						break;
					case 'R' :
						$ONL_R = "selected";
						$ONLNameShow = $DataMove['ONL_Name'];
						break;
					case 'Y' :
						$ONL_Y = "selected";
						$ONLNameShow = $DataMove['ONL_Name'];
						break;
					case 'N' :
						$ONL_N = "selected";
						$ONLNameShow = $DataMove['ONL_Name'];
						break;
					case 'C' :
						$ONL_C = "selected";
						$ONLNameShow = $DataMove['ONL_Name'];
						break;
				}
			}

		}else{
			if (($AppState[0] == 'A' OR $AppState[0] == 'Y' OR $AppState[0] == 'N' OR $AppState[0] == 'R' OR $AppState[0] == 'C')){
				switch ($AppState[0]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV010' OR $_SESSION['LvCode'] == 'LV011'){
							$DisMkt = "";
							$MKT_Y = "selected";
							$MKtNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$MKT_O = "selected";
						}
						break;
					case 'R' :
						$MKT_R = "selected";
						$MKtNameShow = $DataMove['MKT_Name'];
						break;
					case 'Y' :
						$MKT_Y = "selected";
						$MKtNameShow = $DataMove['MKT_Name'];
						break;
					case 'N' :
						$MKT_N = "selected";
						$MKtNameShow = $DataMove['MKT_Name'];
						break;
					case 'C' :
						$MKT_C = "selected";
						$MKtNameShow = $DataMove['MKT_Name'];
						break;
				}
			}

			/* ฝ่าย TTC */
			if (($AppState[1] == 'A' OR $AppState[1] == 'Y' OR $AppState[1] == 'N' OR $AppState[1] == 'R' OR $AppState[1] == 'C') ){
				switch ($AppState[1]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV027' OR $_SESSION['LvCode'] == 'LV034'){
							$DisTTC = "";
							if ($AppState[0] == 'R'){
								$TTC_C = "selected";
								$TTC_R = "disabled";  
								$TTC_Y = "disabled";
								$TTC_N = "disabled";
							}else{
								$TTC_Y = "selected";
							}
							
							$TTCNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$TTC_O = "selected";
						}
						break;
					case 'R' :
						$TTC_R = "selected";
						$TTCNameShow = $DataMove['TTC_Name'];
						break;
					case 'Y' :
						$TTC_Y = "selected";
						$TTCNameShow = $DataMove['TTC_Name'];
						break;
					case 'N' :
						$TTC_N = "selected";
						$TTCNameShow = $DataMove['TTC_Name'];
						break;
					case 'C' :
						$TTC_C = "selected";
						$TTCNameShow = $DataMove['TTC_Name'];
						break;
				}
			}

			/* ฝ่าย MT1 */
			if (($AppState[2] == 'A' OR $AppState[2] == 'Y' OR $AppState[2] == 'N' OR $AppState[2] == 'R' OR $AppState[2] == 'C')){
				switch ($AppState[2]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV038' OR $_SESSION['LvCode'] == 'LV040'){
							$DisMT1 = "";
							if ($AppState[0] == 'R'){
								$MT1_C = "selected"; 
								$MT1_R = "disabled";  
								$MT1_Y = "disabled";
								$MT1_N = "disabled";
							}else{
								$MT1_Y = " selected "; 
							}
							$MT1NameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$MT1_O = "selected";
						}
						break;
					case 'R' :
						$MT1_R = "selected";
						$MT1NameShow = $DataMove['MT1_Name'];
						break;
					case 'Y' :
						$MT1_Y = "selected";
						$MT1NameShow = $DataMove['MT1_Name'];
						break;
					case 'N' :
						$MT1_N = "selected";
						$MT1NameShow = $DataMove['MT1_Name'];
						break;
					case 'C' :
						$MT1_C = "selected";
						$MT1NameShow = $DataMove['MT1_Name'];
						break;
				}
			}

			/* ฝ่าย MT2 */
			if (($AppState[3] == 'A' OR $AppState[3] == 'Y' OR $AppState[3] == 'N' OR $AppState[3] == 'R' OR $AppState[3] == 'C')){
				switch ($AppState[3]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV045' OR $_SESSION['LvCode'] == 'LV046' OR $_SESSION['LvCode'] == 'LV107'){
							$DisMT2 = "";
							if ($AppState[0] == 'R'){
								$MT2_C = "selected";
								$MT2_R = "disabled";  
								$MT2_Y = "disabled";
								$MT2_N = "disabled";
							}else{
								$MT2_Y = "selected"; 
							}
							$MT2NameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$MT2_O = "selected";
						}
						break;
					case 'R' :
						$MT2_R = "selected";
						$MT2NameShow = $DataMove['MT2_Name'];
						break;
					case 'Y' :
						$MT2_Y = "selected";
						$MT2NameShow = $DataMove['MT2_Name'];
						break;
					case 'N' :
						$MT2_N = "selected";
						$MT2NameShow = $DataMove['MT2_Name'];
						break;
					case 'C' :
						$MT2_C = "selected";
						$MT2NameShow = $DataMove['MT2_Name'];
						break;
				}
			}

			/* ฝ่าย OUL */
			if (($AppState[4] == 'A' OR $AppState[4] == 'Y' OR $AppState[4] == 'N' OR $AppState[4] == 'R' OR $AppState[4] == 'C')){
				switch ($AppState[4]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV051' OR $_SESSION['LvCode'] == 'LV052'){
							$DisOUL = "";
							if ($AppState[0] == 'R'){
								$OUL_C = "selected";
								$OUL_R = "disabled";  
								$OUL_Y = "disabled";
								$OUL_N = "disabled";
							}else{
								$OUL_Y = "selected";   
							}
							$OULNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$OUL_O = "selected";
						}
						break;
					case 'R' :
						$OUL_R = "selected";
						$OULNameShow = $DataMove['OUL_Name'];
						break;
					case 'Y' :
						$OUL_Y = "selected";
						$OULNameShow = $DataMove['OUL_Name'];
						break;
					case 'N' :
						$OUL_N = "selected";
						$OULNameShow = $DataMove['OUL_Name'];
						break;
					case 'C' :
						$OUL_C = "selected";
						$OULNameShow = $DataMove['OUL_Name'];
						break;
				}
			}
			/* ฝ่าย ONL */
			if (($AppState[5] == 'A' OR $AppState[5] == 'Y' OR $AppState[5] == 'N' OR $AppState[5] == 'R' OR $AppState[5] == 'C')){
				
				switch ($AppState[5]){
					case 'A' :
						if ($_SESSION['LvCode'] == 'LV010' || $_SESSION['LvCode'] == 'LV011' || $_SESSION['LvCode'] == 'LV104' ){
							$DisONL = " ";
							if ($AppState[0] == 'R'){
								$ONL_C = "selected";
								$ONL_R = "disabled";  
								$ONL_Y = "disabled";
								$ONL_N = "disabled";
							}else{
								$ONL_Y = "selected"; 
							}
							$ONLNameShow = $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")";
						}else{
							$ONL_O = "selected";
						}
						break;
					case 'R' :
						$ONL_R = "selected";
						$ONLNameShow = $DataMove['ONL_Name'];
						break;
					case 'Y' :
						$ONL_Y = "selected";
						$ONLNameShow = $DataMove['ONL_Name'];
						break;
					case 'N' :
						$ONL_N = "selected";
						$ONLNameShow = $DataMove['ONL_Name'];
						break;
					case 'C' :
						$ONL_C = "selected";
						$ONLNameShow = $DataMove['ONL_Name'];
						break;
				}
			}
		}

		$cancel = ($_SESSION['DeptCode'] == 'DP002') ? "<button class='btn btn-sm btn-outline-danger' style='font-size: 12px;' id='btn-Cancel' onclick=\"Cancel('".$DataMove['DocNum']."')\">ยกเลิก</button>" : "";
		$output4 = "<thead style='font-size: 13px;'>
							<tr class='text-center'>
								<th width='20%' style='color: blue;'>".$DataMove['DocNum']."</th>
								<th width='15%'>ผลการดำเนินการ</th>
								<th width='42%'>เหตุผล</th>
								<th width='18%'>ชื่อ</th>
								<th width='5%'>$cancel</th>
							</tr>
						</thead>
						<tbody style='font-size: 12px;'>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการการตลาด</td>
								<td>
									<select class='form-select form-select-sm' id='MgrMktApp' ".$DisMkt.">
										<option value=''></option>
										<option value='O' ".$MKT_O.">รอพิจารณา</option>        
										<option value='R' ".$MKT_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$MKT_Y.">อนุมัติ</option>
										<option value='N' ".$MKT_N.">ไม่อนุมัติ</option>
										<option value='C' ".$MKT_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMktRemark' id='MgrMktRemark' value='".$DataMove['MKT_Remark']."' ".$DisMkt."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMktName' id='MgrMktName' value='".$MKtNameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('Mkt')\" ".$DisMkt.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการขาย TT</td>
								<td>
									<select class='form-select form-select-sm' id='MgrTTCApp' ".$DisTTC.">
										<option value=''></option>
										<option value='O' ".$TCC_O.">รอพิจารณา</option>            
										<option value='R' ".$TTC_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$TTC_Y.">อนุมัติ</option>
										<option value='N' ".$TTC_N.">ไม่อนุมัติ</option>
										<option value='C' ".$TTC_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrTTCRemark' id='MgrTTCRemark' value='".$DataMove['TTC_Remark']."' ".$DisTTC."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrTTCName' id='MgrTTCName' value='".$TTCNameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('TTC')\" ".$DisTTC.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการขาย MT1</td>
								<td>
									<select class='form-select form-select-sm' id='MgrMT1App' ".$DisMT1.">
										<option value=''></option>
										<option value='O' ".$MT1_O.">รอพิจารณา</option>            
										<option value='R' ".$MT1_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$MT1_Y.">อนุมัติ</option>
										<option value='N' ".$MT1_N.">ไม่อนุมัติ</option>
										<option value='C' ".$MT1_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMT1Remark' id='MgrMT1Remark' value='".$DataMove['MT1_Remark']."' ".$DisMT1."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMT1name' id='MgrMT1name' value='".$MT1NameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('MT1')\" ".$DisMT1.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการขาย MT2</td>
								<td>
									<select class='form-select form-select-sm' id='MgrMT2App' ".$DisMT2.">
										<option value=''></option>
										<option value='O' ".$MT2_O.">รอพิจารณา</option>           
										<option value='R' ".$MT2_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$MT2_Y.">อนุมัติ</option>
										<option value='N' ".$MT2_N.">ไม่อนุมัติ</option>
										<option value='C' ".$MT2_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMT2Remark' id='MgrMT2Remark' value='".$DataMove['MT2_Remark']."' ".$DisMT2."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrMT2Name' id='MgrMT2Name' value='".$MT2NameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('MT2')\" ".$DisMT2.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ผู้จัดการการขาย หน้าร้าน / กทม.</td>
								<td>
									<select class='form-select form-select-sm' id='MgrOULApp' ".$DisOUL.">
										<option value=''></option>
										<option value='O' ".$OUL_O.">รอพิจารณา</option>            
										<option value='R' ".$OUL_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$OUL_Y.">อนุมัติ</option>
										<option value='N' ".$OUL_N.">ไม่อนุมัติ</option>
										<option value='C' ".$OUL_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrOULRemark' id='MgrOULRemark' value='".$DataMove['OUL_Remark']."' ".$DisOUL."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrOULName' id='MgrOULName' value='".$OULNameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('OUL')\" ".$DisOUL.">บันทึก</button></td>
							</tr>
							<tr>
								<td class='fw-bolder text-primary'>ขาย Online</td>
								<td>
									<select class='form-select form-select-sm' id='MgrONLApp' ".$DisONL.">
										<option value=''></option>
										<option value='O' ".$ONL_O.">รอพิจารณา</option>            
										<option value='R' ".$ONL_R.">ผู้ขอโอนย้าย</option>    
										<option value='Y' ".$ONL_Y.">อนุมัติ</option>
										<option value='N' ".$ONL_N.">ไม่อนุมัติ</option>
										<option value='C' ".$ONL_C.">รับทราบ</option>
									</select>
								</td>
								<td><input class='form-control form-control-sm' type='text' name='MgrONLRemark' id='MgrONLRemark' value='".$DataMove['ONL_Remark']."' ".$DisONL."></td>
								<td><input class='form-control form-control-sm' type='text' name='MgrONLName' id='MgrONLName' value='".$ONLNameShow."' disabled></td>
								<td class='text-center'><button class='btn btn-sm btn-primary' style='font-size: 12px;' onclick=\"SaveApp('ONL')\" ".$DisONL.">บันทึก</button></td>
							</tr>
						</tbody>";
	}
	$arrCol['output4'] = $output4;
} 

if($_GET['a'] == 'CHKdata') {
	$New_ALL = ConToInt($_POST['New_ALL']);
	$New_TTC = ConToInt($_POST['New_TTC']);
	$New_MT1 = ConToInt($_POST['New_MT1']);
	$New_MT2 = ConToInt($_POST['New_MT2']);
	$New_OUL = ConToInt($_POST['New_OUL']);
	$New_ONL = ConToInt($_POST['New_ONL']);

	switch ($_POST['CH']) {
		case 'All':
			$New_ALL = ConToInt($_POST['Now_ALL']) + ConToInt($_POST['Add_ALL']) - ConToInt($_POST['Red_ALL']);
            $arrCol["New"] = number_format($New_ALL);
            $arrCol["Add"] = number_format(ConToInt($_POST['Add_ALL']));
            $arrCol["Red"] = number_format(ConToInt($_POST['Red_ALL']));
            $arrCol['CH'] =  $_POST['CH'];
			break;
		case 'TTC':
			$New_TTC = ConToInt($_POST['Now_TTC']) + ConToInt($_POST['Add_TTC']) - ConToInt($_POST['Red_TTC']);
			$arrCol["New"] = number_format($New_TTC);
			$arrCol["Add"] = number_format(ConToInt($_POST['Add_TTC']));
			$arrCol["Red"] = number_format(ConToInt($_POST['Red_TTC']));
			$arrCol['CH'] =  $_POST['CH'];
			break;
		case 'MT1':
			$New_MT1 = ConToInt($_POST['Now_MT1']) + ConToInt($_POST['Add_MT1']) - ConToInt($_POST['Red_MT1']);
			$arrCol["New"] = number_format($New_MT1);
			$arrCol["Add"] = number_format(ConToInt($_POST['Add_MT1']));
			$arrCol["Red"] = number_format(ConToInt($_POST['Red_MT1']));
			$arrCol['CH'] =  $_POST['CH'];
			break;
		case 'MT2':
			$New_MT2 = ConToInt($_POST['Now_MT2']) + ConToInt($_POST['Add_MT2']) - ConToInt($_POST['Red_MT2']);
			$arrCol["New"] = number_format($New_MT2);
			$arrCol["Add"] = number_format(ConToInt($_POST['Add_MT2']));
			$arrCol["Red"] = number_format(ConToInt($_POST['Red_MT2']));
			$arrCol['CH'] =  $_POST['CH'];
			break;
		case 'OUL':
			$New_OUL = ConToInt($_POST['Now_OUL']) + ConToInt($_POST['Add_OUL']) - ConToInt($_POST['Red_OUL']);
			$arrCol["New"] = number_format($New_OUL);
			$arrCol["Add"] = number_format(ConToInt($_POST['Add_OUL']));
			$arrCol["Red"] = number_format(ConToInt($_POST['Red_OUL']));
			$arrCol['CH'] =  $_POST['CH'];
			break;
		case 'ONL':
			$New_ONL = ConToInt($_POST['Now_ONL']) + ConToInt($_POST['Add_ONL']) - ConToInt($_POST['Red_ONL']);
			$arrCol["New"] = number_format($New_ONL);
			$arrCol["Add"] = number_format(ConToInt($_POST['Add_ONL']));
			$arrCol["Red"] = number_format(ConToInt($_POST['Red_ONL']));
			$arrCol['CH'] =  $_POST['CH'];
			break;
	}
	$arrCol['TotalAdd'] = number_format(ConToInt($_POST['Add_ALL']) + ConToInt($_POST['Add_TTC']) + ConToInt($_POST['Add_MT1']) + ConToInt($_POST['Add_MT2']) + ConToInt($_POST['Add_OUL']) + ConToInt($_POST['Add_ONL']));
    $arrCol['TotalRed'] = number_format(ConToInt($_POST['Red_ALL']) + ConToInt($_POST['Red_TTC']) + ConToInt($_POST['Red_MT1']) + ConToInt($_POST['Red_MT2']) + ConToInt($_POST['Red_OUL']) + ConToInt($_POST['Red_ONL']));
	// echo $New_ALL." | ".$New_TTC." | ".$New_MT1." | ".$New_MT2." | ".$New_OUL." | ".$New_ONL;
    $arrCol['TotalNew'] = number_format($New_ALL + $New_TTC + $New_MT1 + $New_MT2 + $New_OUL + $New_ONL);
} 

if( $_GET['a'] == 'SaveApp') {
	$TotalAdd = ConToInt($_POST['Add_ALL']) + ConToInt($_POST['Add_TTC']) + ConToInt($_POST['Add_MT1']) + ConToInt($_POST['Add_MT2']) + ConToInt($_POST['Add_OUL']) + ConToInt($_POST['Add_ONL']);
    $TotalRed = ConToInt($_POST['Red_ALL']) + ConToInt($_POST['Red_TTC']) + ConToInt($_POST['Red_MT1']) + ConToInt($_POST['Red_MT2']) + ConToInt($_POST['Red_OUL']) + ConToInt($_POST['Red_ONL']);

	if ($TotalAdd != $TotalRed || $TotalAdd == 0 || $TotalRed == 0){
		$Halert = "<i class='fas fa-exclamation-circle text-primary' style='font-size: 75px;'></i>";
		$alert = "ข้อมูล เพิ่ม/ลด การจองสินค้าไม่เท่ากันหรือเท่ากับ 0 กรุณาตรวจสอบ";
		$arrCol['Halert'] = $Halert;
        $arrCol['alert'] = $alert;
        $RunSave = "N";
    }else{
        $RunSave = "Y";
    }

    $Chk_WhsCode = "";
	$WhsCode = "NULL";
    if(isset($_POST['WhsCode'])) {
        $Chk_WhsCode = "AND WhsCode = '".$_POST['WhsCode']."'";
		$WhsCode = $_POST['WhsCode'];
    }

	if ($RunSave == "Y"){
		$chk = CHKRowDB("SELECT * FROM whsequota_header WHERE ItemCode = '".$_POST['ItemCode']."' AND StatusDoc = '1'");
		if($chk == 0) { //ยังไม่เอกสาร
			if($_POST['App'] != 'R') {
				$Halert = "<i class='fas fa-exclamation-circle text-primary' style='font-size: 75px;'></i>";
				$alert = "กรุณาเลือก ผู้ขอจองสินค้า";
				$arrCol['Halert'] = $Halert;
				$arrCol['alert'] = $alert;
			}else{
				$thisMonth = date("m");
                $thisYear = date("Y");
				$sql1 = "SELECT DocNum FROM whsequota_header WHERE MONTH(DocDate) = MONTH(NOW()) AND YEAR(DocDate) = YEAR(NOW()) ORDER BY DocNum DESC LIMIT 1";
				$DataLast = MySQLSelect($sql1);
				if (date("Y") <= 2500){
                    $yearAdd = (date("Y")+543);
                    $yearAdd = substr($yearAdd,2).$thisMonth;
                }else{
                    $yearAdd = date("y").$thisMonth;
                }
				$runNum = intval(substr($DataLast['DocNum'],7))+1;
				if ($runNum <= 9){
                    $docNum = "MR-".$yearAdd."000".$runNum;
                }else{
                    if ($runNum <= 99){
                      $docNum = "MR-".$yearAdd."00".$runNum;
                    }else{
                      if ($runNum <= 999){
                        $docNum = "MR-".$yearAdd."0".$runNum;
                      }else{
                        $docNum = "MR-".$yearAdd.$runNum;
                      }
                    }
                }
				$NewDoc = $docNum;
                if ($_POST['Pos'] == 'Mkt'){
                    $dataApp = 3;
                }else{
                    $dataApp = 1;
                }
				$InsertData = "DocNum = '".$NewDoc."',
                               DocDate = NOW(),
                               ItemCode = '".$_POST['ItemCode']."',
                               All_In = ".intval(ConToInt($_POST['Add_ALL'])).",
                               All_Out = ".intval(ConToInt($_POST['Red_ALL'])).",
                               TTC_In = ".intval(ConToInt($_POST['Add_TTC'])).",
                               TTC_Out = ".intval(ConToInt($_POST['Red_TTC'])).",
                               MT1_In = ".intval(ConToInt($_POST['Add_MT1'])).",
                               MT1_Out = ".intval(ConToInt($_POST['Red_MT1'])).",
                               MT2_In = ".intval(ConToInt($_POST['Add_MT2'])).",
                               MT2_Out = ".intval(ConToInt($_POST['Red_MT2'])).",
                               OUL_In = ".intval(ConToInt($_POST['Add_OUL'])).",
                               OUL_Out = ".intval(ConToInt($_POST['Red_OUL'])).",
                               ONL_In = ".intval(ConToInt($_POST['Add_ONL'])).",
                               ONL_Out = ".intval(ConToInt($_POST['Red_ONL'])).",
                               UkeyCreate = '".$_SESSION['ukey']."',
                               LastUpdate = NOW(),
                               StatusDoc = ".$dataApp.",
                               LastUkey = '".$_SESSION['ukey']."',";
				if($WhsCode != "NULL") {
					$InsertData .= "WhsCode = '".$WhsCode."',";
				}
				if (ConToInt($_POST['Add_ALL']) != 0 OR ConToInt($_POST['Red_ALL']) != 0){
					$AppState[0] = 'A';
				}else{
					$AppState[0] = 'O';
				}
				if (ConToInt($_POST['Add_TTC']) != 0 OR ConToInt($_POST['Red_TTC']) != 0){
					$AppState[1] = 'A';
				}else{
					$AppState[1] = 'O';
				}
				if (ConToInt($_POST['Add_MT1']) != 0 OR ConToInt($_POST['Red_MT1']) != 0){
					$AppState[2] = 'A';
				}else{
					$AppState[2] = 'O';
				}
				if (ConToInt($_POST['Add_MT2']) != 0 OR ConToInt($_POST['Red_MT2']) != 0){
					$AppState[3] = 'A';
				}else{
					$AppState[3] = 'O';
				}
				if (ConToInt($_POST['Add_OUL']) != 0 OR ConToInt($_POST['Red_OUL']) != 0){
					$AppState[4] = 'A';
				}else{
					$AppState[4] = 'O';
				}
				if (ConToInt($_POST['Add_ONL']) != 0 OR ConToInt($_POST['Red_ONL']) != 0){
					$AppState[5] = 'A';
				}else{
					$AppState[5] = 'O';
				}

				if(!isset($_POST['SaleTime'])) {
					$_POST['SaleTime'] = 0;
				}

				switch ($_POST['Pos']){
                    case 'Mkt':
                        $InsertData .= "MKT_Ukey = '".$_SESSION['ukey']."',
                                        MKT_App = '".$_POST['App']."',
                                        MKT_Remark = '".$_POST['Remark']."',
                                        MKT_Date = NOW(),
										SaleTime = ".$_POST['SaleTime'].",";
                        $AppState[0] = 'R';
                        break;
                    case 'TTC':
                        $InsertData .= "TTC_Ukey = '".$_SESSION['ukey']."',
                                        TTC_App = '".$_POST['App']."',
                                        TTC_Remark = '".$_POST['Remark']."',
                                        TTC_Date = NOW(),";
                        $AppState[1] = 'R';
                        break;
                    case 'MT1':
                        $InsertData .= "MT1_Ukey = '".$_SESSION['ukey']."',
                                        MT1_App = '".$_POST['App']."',
                                        MT1_Remark = '".$_POST['Remark']."',
                                        MT1_Date = NOW(),";
                        $AppState[2] = 'R';
                        break;
                    case 'MT2':
                        $InsertData .= "MT2_Ukey = '".$_SESSION['ukey']."',
                                        MT2_App = '".$_POST['App']."',
                                        MT2_Remark = '".$_POST['Remark']."',
                                        MT2_Date = NOW(),";
                        $AppState[3] = 'R';
                        break;
                    case 'OUL':
                        $InsertData .= "OUL_Ukey = '".$_SESSION['ukey']."',
                                        OUL_App = '".$_POST['App']."',
                                        OUL_Remark = '".$_POST['Remark']."',
                                        OUL_Date = NOW(),";
                        $AppState[4] = 'R';
                        break;
                    case 'ONL':
                        $InsertData .= "ONL_Ukey = '".$_SESSION['ukey']."',
                                        ONL_App = '".$_POST['App']."',
                                        ONL_Remark = '".$_POST['Remark']."',
                                        ONL_Date = NOW(),";
                        $AppState[5] = 'R';
                        break;
                }
				$FinalState = $AppState[0].$AppState[1].$AppState[2].$AppState[3].$AppState[4].$AppState[5];
                $InsertData .= "AppState = '".$FinalState."'";
				MySQLInsert("INSERT INTO whsequota_header SET ".$InsertData."");
				$Halert = "<i class='fas fa-check-circle text-success' style='font-size: 75px;'></i>";
				$alert = "สร้างเอกสาร จองสินค้าเรียบร้อยแล้ว";
                $arrCol['Halert'] = $Halert;
                $arrCol['alert'] = $alert;
			}
		}else{ // เจอเอกสาร
			$sql1 = "SELECT DocNum, AppState, WhsCode FROM whsequota_header WHERE ItemCode = '".$_POST['ItemCode']."' AND StatusDoc = '1'";
			$DataLast = MySQLSelect($sql1);
			$AppState = str_split($DataLast['AppState']);
			$Chk_WhsCode = "";
			$WhsCode = "NULL";
			if(isset($DataLast['WhsCode'])) {
				$Chk_WhsCode = ($DataLast['WhsCode'] != "" && $DataLast['WhsCode'] != null) ? "AND WhsCode = '".$DataLast['WhsCode']."'" : "";
				$WhsCode = ($DataLast['WhsCode'] != "" && $DataLast['WhsCode'] != null) ? $DataLast['WhsCode'] : "NULL";
			}
			$UpdateData = "";
			switch($_POST['Pos']) {
				case 'Mkt' :
                    $UpdateData .= "MKT_Ukey = '".$_SESSION['ukey']."',
                                    MKT_App = '".$_POST['App']."',
                                    MKT_Remark = '".$_POST['Remark']."',
                                    MKT_Date = NOW(),
									SaleTime = ".$_POST['SaleTime'].",";
                    $AppState[0] = $_POST['App'];
                    break;
                case 'TTC' :
                    $UpdateData .= "TTC_Ukey = '".$_SESSION['ukey']."',
                                    TTC_App = '".$_POST['App']."',
                                    TTC_Remark = '".$_POST['Remark']."',
                                    TTC_Date = NOW(),";
                    $AppState[1] = $_POST['App'];
                    break;
                case 'MT1' :
                    $UpdateData .= "MT1_Ukey = '".$_SESSION['ukey']."',
                                    MT1_App = '".$_POST['App']."',
                                    MT1_Remark = '".$_POST['Remark']."',
                                    MT1_Date = NOW(),";
                    $AppState[2] = $_POST['App'];
                    break;
                case 'MT2' :
                    $UpdateData .= "MT2_Ukey = '".$_SESSION['ukey']."',
                                    MT2_App = '".$_POST['App']."',
                                    MT2_Remark = '".$_POST['Remark']."',
                                    MT2_Date = NOW(),";
                    $AppState[3] = $_POST['App'];
                    break;
                case 'OUL' :
                    $UpdateData .= "OUL_Ukey = '".$_SESSION['ukey']."',
                                    OUL_App = '".$_POST['App']."',
                                    OUL_Remark = '".$_POST['Remark']."',
                                    OUL_Date = NOW(),";
                    $AppState[4] = $_POST['App'];
					break;
                case 'ONL' :
                    $UpdateData .= "ONL_Ukey = '".$_SESSION['ukey']."',
                                    ONL_App = '".$_POST['App']."',
                                    ONL_Remark = '".$_POST['Remark']."',
                                    ONL_Date = NOW(),";
                    $AppState[5] = $_POST['App'];
                    break;
			}
			$FinalState = $AppState[0].$AppState[1].$AppState[2].$AppState[3].$AppState[4].$AppState[5];
            $UpdateData .= "AppState = '".$FinalState."',";

			if ($AppState[0] == 'A' OR $AppState[1] == 'A' OR $AppState[2] == 'A' OR $AppState[3] == 'A' OR $AppState[4] == 'A' OR $AppState[5] == 'A'){
                $UpdateData .= "LastUpdate = NOW(),
                                LastUkey = '".$_SESSION['ukey']."'";
            }else{
                $UpdateData .= "LastUpdate = NOW(),
                                LastUkey = '".$_SESSION['ukey']."',
                                StatusDoc = 3";
            }
			$NewDoc = $DataLast['DocNum'];
			MySQLUpdate("UPDATE whsequota_header SET ".$UpdateData." WHERE DocNum = '".$NewDoc."'");
			$Halert = "<i class='fas fa-check-circle text-success' style='font-size: 75px;'></i>";
			$alert = "บันทึก จองสินค้าเรียบร้อยแล้ว";
            $arrCol['Halert'] = $Halert;
            $arrCol['alert'] = $alert;
		}

		// ส่วนปรับยอด
		$NewQty = ['New_TTC','New_MT1','New_MT2','New_OUL','New_ONL'];
		$OldQty = ['Now_TTC','Now_MT1','Now_MT2','Now_OUL','Now_ONL'];
		$Team =   ['TTC',    'MT1',    'MT2',    'OUL',    'ONL',];
		$QtyIn =  ['Add_TTC','Add_MT1','Add_MT2','Add_OUL','Add_ONL'];
		$QtyOut = ['Red_TTC','Red_MT1','Red_MT2','Red_OUL','Red_ONL'];
		$a = 0;
		for ($u=0;$u<=4;$u++){
			if ($Team[$u] == $_POST['Pos']){
				$a=$u;    
			}
		}

		switch($_POST['App']) {
			case 'R':
				if ($_POST['Pos']  == 'Mkt'){
					//เพิ่ม+ลด สินค้าเลย
					for($i = 0; $i <= 4; $i++) {
						$NewQtyTeam = ConToInt($_POST[$NewQty[$i]]);
						$OldQtyTeam = ConToInt($_POST[$OldQty[$i]]);
						if ($NewQtyTeam != $OldQtyTeam){
							$AddTrn = "trnType= 'M',
										WhsTarget = '".$Team[$i]."',
										WhsSource = 'KSY',
										trnDate = NOW(),
										ItemCode = '".$_POST['ItemCode']."',
										QtyIn = ".$_POST[$QtyIn[$i]].",
										QtyOut = ".$_POST[$QtyOut[$i]].",
										DocNum = '".$NewDoc."'";
							$NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
							$chkQuota = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$i]."' $Chk_WhsCode");
							if($chkQuota == 0) {
								$AddQuota = "ItemCode = '".$_POST['ItemCode']."',
						                    CH = '".$Team[$i]."',
						                    OnHand = ".$NewQtyTeam.",
						                    LastUpdate = NOW(),
						                    LastUkey = '".$_SESSION['ukey']."',
						                    LastIDTran = ".$NewTrn;
                                if($WhsCode != "NULL") {
                                    $AddQuota .= ", WhsCode = '".$WhsCode."'";
                                }
								MySQLInsert("INSERT INTO whsquota SET ".$AddQuota."");
							}else{
								MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyTeam." WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$i]."' $Chk_WhsCode");
							}
						}
					}
					$alert = "จัดสรรคการจองสินค้าเรียบร้อยแล้ว";
					$Halert = "<i class='fas fa-check-circle text-success' style='font-size: 75px;'></i>";
					$arrCol['Halert'] = $Halert;
					$arrCol['alert'] = $alert;
				}

 			break;
			case 'Y':
				switch($_POST['Pos']) {
					case 'Mkt':
						//เพิ่มลดสินค้าทั้งหมด
						for($i = 0; $i <= 4; $i++) {
							if($_POST[$QtyIn[$i]] != 0 OR $_POST[$QtyOut[$i]] != 0) {
								$AddTrn = "trnType= 'M',
											WhsTarget = '".$Team[$i]."',
											WhsSource = 'KSY',
											trnDate = NOW(),
											ItemCode = '".$_POST['ItemCode']."',
											QtyIn = ".$_POST[$QtyIn[$i]].",
											QtyOut = ".$_POST[$QtyOut[$i]].",
											DocNum = '".$NewDoc."'";
								$NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
								$chkQuota = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$i]."' $Chk_WhsCode");
								if($chkQuota == 0) {
									$AddQuota = "ItemCode = '".$_POST['ItemCode']."',
												CH = '".$Team[$i]."',
												OnHand = ".$_POST[$NewQty[$i]].",
												LastUpdate = NOW(),
												LastUkey = '".$_SESSION['ukey']."',
												LastIDTran = ".$NewTrn;
                                    if($WhsCode != "NULL") {
                                        $AddQuota .= ", WhsCode = '".$WhsCode."'";
                                    }
									MySQLInsert("INSERT INTO whsquota SET ".$AddQuota."");
								}else{
									MySQLUpdate("UPDATE whsquota SET OnHand = ".$_POST[$NewQty[$i]]." WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$i]."' $Chk_WhsCode");
								}
							}
						}
						break;
					default :
							$DataAppState = MySQLSelect("SELECT RIGHT(AppState,5) AS AppState FROM whsequota_header WHERE DocNum = '".$NewDoc."'");
							$AppState = str_split($DataAppState['AppState']);
							for ($r = 0; $r < count($AppState); $r++) {
								if($AppState[$r] == 'R') { // เจอ CH ขอ
									$AddTrn = "trnType= 'D',
														WhsTarget = '".$Team[$r]."',
														WhsSource = '".$Team[$a]."',
														trnDate = NOW(),
														ItemCode = '".$_POST['ItemCode']."',
														QtyIn = 0,
														QtyOut = ".$_POST[$QtyOut[$a]].",
														DocNum = '".$NewDoc."'";
									$NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
									//echo "INSERT INTO whsquota_trn SET ".$AddTrn."<br>";
									$chkQA = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$a]."' $Chk_WhsCode");
									if($chkQA == 0) {
                                        $AddQuota = "
                                            INSERT INTO whsquota 
                                            SET ItemCode = '".$_POST['ItemCode']."', 
                                                CH = '".$Team[$a]."', 
                                                OnHand = ".$_POST[$NewQty[$a]].", 
                                                LastUpdate = NOW(), 
                                                LastUkey = '".$_SESSION['ukey']."', 
                                                LastIDTran = ".$NewTrn."";
                                        if($WhsCode != "NULL") {
                                            $AddQuota .= ", WhsCode = '".$WhsCode."'";
                                        }
										MySQLInsert($AddQuota);
									}else{
										MySQLUpdate("UPDATE whsquota SET OnHand = ".$_POST[$NewQty[$a]].", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = '".$Team[$a]."' AND ItemCode = '".$_POST['ItemCode']."' $Chk_WhsCode");
									}

									$AddTrn = "trnType= 'A',
												WhsTarget = '".$Team[$r]."',
												WhsSource = '".$Team[$a]."',
												trnDate = NOW(),
												ItemCode = '".$_POST['ItemCode']."',
												QtyIn = ".$_POST[$QtyOut[$a]].",
												QtyOut = 0,
												DocNum = '".$NewDoc."'";
									$NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
									//echo "INSERT INTO whsquota_trn SET ".$AddTrn."<br>";
									$chkQR = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$r]."' $Chk_WhsCode");
									if ($chkQR == 0){
                                        $AddQuota = "
                                            INSERT INTO whsquota 
                                            SET ItemCode = '".$_POST['ItemCode']."', 
                                                CH = '".$Team[$r]."', 
                                                OnHand = ".$_POST[$NewQty[$r]].", 
                                                LastUpdate = NOW(), 
                                                LastUkey = '".$_SESSION['ukey']."', 
                                                LastIDTran = ".$NewTrn."";
                                        if($WhsCode != "NULL") {
                                            $AddQuota .= ", WhsCode = '".$WhsCode."'";
                                        }
										MySQLInsert($AddQuota);
									}else{
										MySQLUpdate("UPDATE whsquota SET OnHand = ".$_POST[$NewQty[$r]].", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = '".$Team[$r]."' AND ItemCode = '".$_POST['ItemCode']."' $Chk_WhsCode");
									}

								}
							}
							break;

					/*
                        case 'TTC':
                            //เพิ่มลดสินค้า TTC
                            $DataAppState = MySQLSelect("SELECT AppState FROM whsequota_header WHERE DocNum = '".$NewDoc."'");
                            $AppState = str_split($DataAppState['AppState']);
                            for ($i = 0; $i < count($AppState); $i++) {
                                if($AppState[$i] == 'R') {
                                    if($i != 0) {
                                        if($i <= 5) {
                                            $ax= $i-1;
                                            $AddTrn = "trnType= 'A',
                                                            WhsTarget = '".$Team[$ax]."',
                                                            WhsSource = 'TTC',
                                                            trnDate = NOW(),
                                                            ItemCode = '".$_POST['ItemCode']."',
                                                            QtyIn = ".$_POST['Red_TTC'].",
                                                            QtyOut = 0,
                                                            DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyTeam = ConToInt($_POST[$OldQty[$ax]]) + ConToInt($_POST['Red_TTC']);
                                            $chkQuota = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$ax]."'");
                                            if($chkQuota == 0) {
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = '".$Team[$ax]."', OnHand = ".$NewQtyTeam.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyTeam.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = '".$Team[$ax]."' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                            $AddTrn = "trnType= 'D',
                                                        WhsTarget = '".$Team[$ax]."',
                                                        WhsSource = 'TTC',
                                                        trnDate = NOW(),
                                                        ItemCode = '".$_POST['ItemCode']."',
                                                        QtyIn = 0,
                                                        QtyOut = ".$_POST['Red_TTC'].",
                                                        DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyTTC = ConToInt($_POST['Now_TTC']) - ConToInt($_POST['Red_TTC']);
                                            $chkTTC = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = 'TTC'");
                                            if ($chkTTC == 0){
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = 'TTC', OnHand = ".$NewQtyTTC.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyTTC.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = 'TTC' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                        }else{
                                            $Halert = "<i class='fas fa-exclamation-circle text-primary' style='font-size: 75px;'></i>";
                                            $alert = "AppState มากกว่า 5 โปรดแจ้งแผนก IT";
                                            $arrCol['Halert'] = $Halert;
                                            $arrCol['alert'] = $alert;
                                        }
                                    }
                                }
                            }
                            break;
                        case 'MT1':
                            // เพิ่มลดสินค้า MT1
                            $DataAppState = MySQLSelect("SELECT AppState FROM whsequota_header WHERE DocNum = '".$NewDoc."'");
                            $AppState = str_split($DataAppState['AppState']);
                            for ($i = 0; $i < count($AppState); $i++) {
                                if($AppState[$i] == 'R') {
                                    if($i != 1) {
                                        if($i <= 4) {
                                            $ax = $i-1;
                                            $AddTrn = "trnType= 'A',
                                                            WhsTarget = '".$Team[$ax]."',
                                                            WhsSource = 'MT1',
                                                            trnDate = NOW(),
                                                            ItemCode = '".$_POST['ItemCode']."',
                                                            QtyIn = ".$_POST['Red_MT1'].",
                                                            QtyOut = 0,
                                                            DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyTeam = ConToInt($_POST[$OldQty[$ax]]) + ConToInt($_POST['Red_MT1']);
                                            $chkQuota = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$ax]."'");
                                            if($chkQuota == 0) {
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = '".$Team[$ax]."', OnHand = ".$NewQtyTeam.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyTeam.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = '".$Team[$ax]."' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                            $AddTrn = "trnType= 'D',
                                                        WhsTarget = '".$Team[$ax]."',
                                                        WhsSource = 'MT1',
                                                        trnDate = NOW(),
                                                        ItemCode = '".$_POST['ItemCode']."',
                                                        QtyIn = 0,
                                                        QtyOut = ".$_POST['Red_MT1'].",
                                                        DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyMT1 = ConToInt($_POST['Now_MT1']) - ConToInt($_POST['Red_MT1']);
                                            $chkMT1 = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = 'MT1'");
                                            if ($chkMT1 == 0){
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = 'MT1', OnHand = ".$NewQtyMT1.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyMT1.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = 'MT1' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                        }else{
                                            $Halert = "<i class='fas fa-exclamation-circle text-primary' style='font-size: 75px;'></i>";
                                            $alert = "AppState มากกว่า 5 โปรดแจ้งแผนก IT";
                                            $arrCol['Halert'] = $Halert;
                                            $arrCol['alert'] = $alert;
                                        }
                                    }
                                }
                            }
                            break;
                        case 'MT2':
                            // เพิ่มลดสินค้า MT2
                            $DataAppState = MySQLSelect("SELECT AppState FROM whsequota_header WHERE DocNum = '".$NewDoc."'");
                            $AppState = str_split($DataAppState['AppState']);
                            for ($i = 0; $i < count($AppState); $i++) {
                                if($AppState[$i] == 'R') {
                                    //if($i != 2) {
                                        //if($i <= 4) {
                                            //$ax = $i-1;
                                            echo "wai".$i;
                                            $ax=$i-1;
                                            $AddTrn = "trnType= 'D',
                                                            WhsTarget = 'MT2',
                                                            WhsSource = '".$Team[$ax]."',
                                                            trnDate = NOW(),
                                                            ItemCode = '".$_POST['ItemCode']."',
                                                            QtyIn = 0,
                                                            QtyOut = ".$_POST['Red_MT2'].",
                                                            DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            //$NewQtyTeam = ConToInt;//ConToInt($_POST[$OldQty[$i]]) + ConToInt($_POST['Red_MT2']);
                                            $chkQuota = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$i]."'");
                                            if($chkQuota == 0) {
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = '".$Team[$i]."', OnHand = ".$_POST[$NewQty[$i]].", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$_POST[$NewQty[$i]].", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = '".$Team[$i]."' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                            $AddTrn = "trnType= 'A',
                                                        WhsTarget = '".$Team[$ax]."',
                                                        WhsSource = 'MT2',
                                                        trnDate = NOW(),
                                                        ItemCode = '".$_POST['ItemCode']."',
                                                        QtyIn = ".$_POST['Red_MT2'].",
                                                        QtyOut = 0,
                                                        DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyMT2 = ConToInt($_POST['Now_MT2']) - ConToInt($_POST['Red_MT2']);
                                            $chkMT2 = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = 'MT2'");
                                            if ($chkMT2 == 0){
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = 'MT2', OnHand = ".$NewQtyMT2.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyMT2.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = 'MT2' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                *}else{
                                            $Halert = "<i class='fas fa-exclamation-circle text-primary' style='font-size: 75px;'></i>";
                                            $alert = "AppState มากกว่า 6 โปรดแจ้งแผนก IT";
                                            $arrCol['Halert'] = $Halert;
                                            $arrCol['alert'] = $alert;
                                        }
                                    }
                                    
                                }
                            }
                            break;
                        case 'OUL':	
                            // เพิ่มลดสินค้า OUL
                            $DataAppState = MySQLSelect("SELECT AppState FROM whsequota_header WHERE DocNum = '".$NewDoc."'");
                            $AppState = str_split($DataAppState['AppState']);
                            for ($i = 0; $i < count($AppState); $i++) {
                                if($AppState[$i] == 'R') {
                                    if($i != 3) {
                                        if($i <= 4) {
                                            $ax = $i-1;
                                            $AddTrn = "trnType= 'A',
                                                            WhsTarget = '".$Team[$ax]."',
                                                            WhsSource = 'OUL',
                                                            trnDate = NOW(),
                                                            ItemCode = '".$_POST['ItemCode']."',
                                                            QtyIn = ".$_POST['Red_OUL'].",
                                                            QtyOut = 0,
                                                            DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyTeam = ConToInt($_POST[$OldQty[$ax]]) + ConToInt($_POST['Red_OUL']);
                                            $chkQuota = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$ax]."'");
                                            if($chkQuota == 0) {
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = '".$Team[$ax]."', OnHand = ".$NewQtyTeam.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyTeam.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = '".$Team[$ax]."' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                            $AddTrn = "trnType= 'D',
                                                        WhsTarget = '".$Team[$ax]."',
                                                        WhsSource = 'OUL',
                                                        trnDate = NOW(),
                                                        ItemCode = '".$_POST['ItemCode']."',
                                                        QtyIn = 0,
                                                        QtyOut = ".$_POST['Red_OUL'].",
                                                        DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyOUL = ConToInt($_POST['Now_OUL']) - ConToInt($_POST['Red_OUL']);
                                            $chkOUL = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = 'OUL'");
                                            if ($chkOUL == 0){
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = 'OUL', OnHand = ".$NewQtyOUL.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyOUL.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = 'OUL' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                        }else{
                                            $Halert = "<i class='fas fa-exclamation-circle text-primary' style='font-size: 75px;'></i>";
                                            $alert = "AppState มากกว่า 5 โปรดแจ้งแผนก IT";
                                            $arrCol['Halert'] = $Halert;
                                            $arrCol['alert'] = $alert;
                                        }
                                    }
                                }
                            }
                            break;
                        case 'ONL':
                            // เพิ่มลดสินค้า ONL
                            $DataAppState = MySQLSelect("SELECT AppState FROM whsequota_header WHERE DocNum = '".$NewDoc."'");
                            $AppState = str_split($DataAppState['AppState']);
                            for ($i = 0; $i < count($AppState); $i++) {
                                if($AppState[$i] == 'R') {
                                    if($i != 4) {
                                        if($i <= 4) {
                                            $ax = $i-1;
                                            $AddTrn = "trnType= 'A',
                                                            WhsTarget = '".$Team[$ax]."',
                                                            WhsSource = 'ONL',
                                                            trnDate = NOW(),
                                                            ItemCode = '".$_POST['ItemCode']."',
                                                            QtyIn = ".$_POST['Red_ONL'].",
                                                            QtyOut = 0,
                                                            DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyTeam = ConToInt($_POST[$OldQty[$ax]]) + ConToInt($_POST['Red_ONL']);
                                            $chkQuota = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = '".$Team[$ax]."'");
                                            if($chkQuota == 0) {
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = '".$Team[$ax]."', OnHand = ".$NewQtyTeam.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyTeam.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = '".$Team[$ax]."' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                            $AddTrn = "trnType= 'D',
                                                        WhsTarget = '".$Team[$ax]."',
                                                        WhsSource = 'ONL',
                                                        trnDate = NOW(),
                                                        ItemCode = '".$_POST['ItemCode']."',
                                                        QtyIn = 0,
                                                        QtyOut = ".$_POST['Red_ONL'].",
                                                        DocNum = '".$NewDoc."'";
                                            $NewTrn = MySQLInsert("INSERT INTO whsquota_trn SET ".$AddTrn."");
                                            $NewQtyONL = ConToInt($_POST['Now_ONL']) - ConToInt($_POST['Red_ONL']);
                                            $chkONL = CHKRowDB("SELECT * FROM whsquota WHERE ItemCode = '".$_POST['ItemCode']."' AND CH = 'ONL'");
                                            if ($chkONL == 0){
                                                MySQLInsert("INSERT INTO whsquota SET ItemCode = '".$_POST['ItemCode']."', CH = 'ONL', OnHand = ".$NewQtyONL.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn."");
                                            }else{
                                                MySQLUpdate("UPDATE whsquota SET OnHand = ".$NewQtyONL.", LastUpdate = NOW(), LastUkey = '".$_SESSION['ukey']."', LastIDTran = ".$NewTrn." WHERE CH = 'ONL' AND ItemCode = '".$_POST['ItemCode']."'");
                                            }
                                        }else{
                                            $Halert = "<i class='fas fa-exclamation-circle text-primary' style='font-size: 75px;'></i>";
                                            $alert = "AppState มากกว่า 5 โปรดแจ้งแผนก IT";
                                            $arrCol['Halert'] = $Halert;
                                            $arrCol['alert'] = $alert;
                                        }
                                    }
                                }
                            }
                        break;
                    */
				}
				$alert = "บันทึกเอกสาร จองสินค้าเรียบร้อยแล้ว";
				$Halert = "<i class='fas fa-check-circle text-success' style='font-size: 75px;'></i>";
				$arrCol['Halert'] = $Halert;
				$arrCol['alert'] = $alert;
				break;
		}
	}
}

if($_GET['a'] == 'WhsCaseKB4') {
	$WhsCode = "";
	if($_POST['WhsCode'] != 'ALL') {
		$WhsCode = "AND T0.WhsCode = '".$_POST['WhsCode']."'";
	}

    $sql1 = "SELECT SUM(OnHandIN) AS OnHandIN
             FROM (SELECT T1.ItemCode,T1.ItemName,T1.CodeBars,T0.WhsCode,
                          CASE WHEN T0.WhsCode IN ('KSY','KSM','KB4','MT','MT2') THEN T0.OnHand ELSE 0 END AS OnHandIN,
                          CASE WHEN T2.Location = 2 THEN T0.OnHand ELSE 0 END AS OnHandOUT
                   FROM OITW T0
					LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
					LEFT JOIN OWHS T2 ON T2.WhsCode = T0.WhsCode
                   WHERE (T0.WhsCode IN ('KSY','KSM','KB4','MT','MT2') OR T2.Location = 2) AND T0.ItemCode = '".$_POST['ItemCode']."' $WhsCode
                  ) P0
             GROUP BY P0.ItemCode,P0.ItemName,P0.CodeBars";
	$getHead = SAPSelect($sql1);
	$DataHead = odbc_fetch_array($getHead);
	$OnUse = $DataHead['OnHandIN'];

	$sql2 = "SELECT T0.ItemCode, T0.CH, SUM(T0.OnHand) AS OnHand FROM whsquota T0 WHERE T0.ItemCode = '".$_POST['ItemCode']."' $WhsCode GROUP BY T0.ItemCode, T0.CH";
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
			if($DataDetail['CH'] == "TTC") {
				$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
				$Ck_TTC = 1;
			}
			if($DataDetail['CH'] == "MT1") {
				$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
				$Ck_MT1 = 1;
			}
			if($DataDetail['CH'] == "MT2") {
				$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
				$Ck_MT2 = 1;
			}
			if($DataDetail['CH'] == "OUL") {
				$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
				$Ck_OUL = 1;
			}
			if($DataDetail['CH'] == "ONL") {
				$OnHand[$DataDetail['CH']] = $DataDetail['OnHand'];
				$Ck_ONL = 1;
			}
			$OnUse = $OnUse - $DataDetail['OnHand'];
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
	if ($_SESSION['DeptCode'] == 'DP003' || $_SESSION['DeptCode'] == 'DP005' || $_SESSION['DeptCode'] == 'DP006' || $_SESSION['DeptCode'] == 'DP007' || $_SESSION['DeptCode'] == 'DP008') {
		$Dis = " ";
	}else{
		$Dis = " disabled ";
	}

	if($_POST['WhsCode'] == 'ALL') {
		$Dis = " disabled ";
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
}

if($_GET['a'] == 'Cancel') {
	$DocNum = $_POST['DocNum'];
	$UPDATE = "UPDATE whsequota_header SET StatusDoc = 0 WHERE DocNum = '$DocNum'";
	MySQLUpdate($UPDATE);
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
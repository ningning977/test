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

if($_GET['p'] == "GetSaleEmp") {
	$TeamCode = $_POST['t'];
	switch($TeamCode) {
		case "OUL": $TeamSQL = "T0.TeamCode LIKE 'OUL%' OR T0.TeamCode LIKE 'TT1%'"; break;
		default:    $TeamSQL = "T0.TeamCode LIKE '$TeamCode%'"; break;
	}
	$SQL1 = 
		"SELECT DISTINCT
			T0.Ukey, CONCAT(T1.uName,' ',T1.uLastName) AS 'FullName', T1.uNickName
		FROM saletarget T0
		LEFT JOIN users T1 ON T0.Ukey = T1.uKey
		WHERE T0.DocYear = YEAR(NOW()) AND ($TeamSQL) AND T0.DocStatus = 'A'
		ORDER BY T0.TeamCode, T1.uName ASC";
	$ROW1 = ChkRowDB($SQL1);
	if($ROW1 > 0) {
		$QRY1 = MySQLSelectX($SQL1);
		$i = 0;
		while($RST1 = mysqli_fetch_array($QRY1)) {
			$arrCol[$i]['ukey']     = $RST1['Ukey'];
			$arrCol[$i]['SaleName'] = $RST1['FullName']." (".$RST1['uNickName'].")";
			$i++;
		}
	}
	$arrCol['Rows'] = $ROW1;
}

if($_GET['p'] == "AddHeader") {
	$CPEntry       = $_POST['CPEntry'];
	$CPTitle       = $_POST['CPTitle'];
	$TeamCode      = $_POST['TeamCode'];
	$CPType        = $_POST['CPType'];
	$StartDate     = $_POST['StartDate']."-01";
	$EndDate       = $_POST['EndDate']."-".cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($_POST['EndDate'])), date("Y",strtotime($_POST['EndDate'])));
	$CreateUkey    = $_SESSION['ukey'];

	$arrCol['TeamCode'] = $TeamCode;

	if($_POST['CPDescription'] == "") {
		$CPDescription = "NULL";
	} else {
		$CPDescription = "'".$_POST['CPDescription']."'";
	}

	$MngTypeArr    = array();
	foreach($_POST['MngType'] as $data) { 
		array_push($MngTypeArr,$data);
	}

	if(count($MngTypeArr) == 1 && $MngTypeArr[0] == "T") {
		$MngType  = "T";
		$SaleUkey = "NULL";
	} else {
		$MngType  = "P";
		$SaleUkey = "'".implode(",",$MngTypeArr)."'";
	}

	/* GetDocNum */
	$DocPrefix = "TS-".date("y").date("m");
	$SQL1 = "SELECT T0.DocNum FROM tarsku_header T0 WHERE T0.DocNum LIKE '$DocPrefix%' AND T0.CANCELED = 'N' ORDER BY T0.DocNum DESC LIMIT 1";
	$ROW1 = ChkRowDB($SQL1);
	if($ROW1 == 0) {
		$DocNum = $DocPrefix."001";
	} else {
		$RST1 = MySQLSelect($SQL1);
		$OldDocNum = substr($RST1['DocNum'],7,3);
		$NxtDocNum = $OldDocNum + 1;
		if($NxtDocNum < 10) {
			$DocNum = $DocPrefix."00".$NxtDocNum;
		} elseif($NxtDocNum < 100) {
			$DocNum = $DocPrefix."0".$NxtDocNum;
		} else {
			$DocNum = $DocPrefix.$NxtDocNum;
		}
	}

	if($CPEntry != "0" || $CPEntry == 0) {
		$SQL2 = 
			"INSERT INTO tarsku_header SET
				DocNum = '$DocNum',
				TeamCode = '$TeamCode',
				MngType = '$MngType',
				CPType = '$CPType',
				CPTitle = '$CPTitle',
				CPDescription = $CPDescription,
				SaleUkey = $SaleUkey,
				StartDate = '$StartDate',
				EndDate = '$EndDate',
				CreateUkey = '$CreateUkey'";
		$PID2 = MySQLInsert($SQL2);
		// FOR TESTING
		// $PID1 = 1;
		// $DocNum = "TS-2305001";

		if($PID2 == 0) {
			$arrCol['Status']  = "ERR::CANNOTINSERT";
			$arrCol['CPEntry'] = 0;
			$arrCol['DocNum']  = null;
		} else {
			$arrCol['Status']  = "SUCCESS";
			$arrCol['CPEntry'] = $PID2;
			$arrCol['DocNum']  = $DocNum;
		}
	}
}

if($_GET['p'] == 'GetSales') {
	$DocNum = $_POST['CPDocNum'];
	switch ($_POST['TeamCode']) {
		case 'MT1': $TeamCode = "โมเดิร์นเทรด 1"; break;
		case 'MT2': $TeamCode = "โมเดิร์นเทรด 2"; break;
		case 'TT2': $TeamCode = "ร้านค้าเขตต่างจังหวัด"; break;
		case 'OUL': $TeamCode = "หน้าร้าน + ร้านค้าเขตกรุงเทพฯ"; break;
		case 'ONL': $TeamCode = "ออนไลน์"; break;
	}

	$Data = "";
	if($_POST['MngType'] == "T") {
		$Data .= "<tr>
					<td>".$TeamCode."</td>";
					for($m = 1; $m <= 12; $m++) {
						$Data .= "<td class='text-right'>-</td>";
					}
		$Data .= "</tr>";
	}else{
		$SQL = "
			SELECT
				A0.ukey, CONCAT(A1.uName,' ',A1.uLastName) AS 'FullName', A1.uNickName, A1.LvCode
			FROM (
				SELECT
				SUBSTRING_INDEX(SUBSTRING_INDEX(T1.SaleUkey, ',', T0.N), ',', -1) AS 'Ukey'
				FROM
				(
				SELECT 1 N UNION ALL
				SELECT 2 UNION ALL
				SELECT 3 UNION ALL
				SELECT 4 UNION ALL
				SELECT 5 UNION ALL
				SELECT 6 UNION ALL
				SELECT 7 UNION ALL
				SELECT 8 UNION ALL
				SELECT 9 UNION ALL
				SELECT 10 UNION ALL
				SELECT 11 UNION ALL
				SELECT 12 UNION ALL
				SELECT 13 UNION ALL
				SELECT 14 UNION ALL
				SELECT 15 UNION ALL
				SELECT 16 UNION ALL
				SELECT 17 UNION ALL
				SELECT 18 UNION ALL
				SELECT 19 UNION ALL
				SELECT 20
				) T0
				INNER JOIN tarsku_header T1 ON char_length(T1.SaleUkey) - character_length(REPLACE(T1.SaleUkey,',','')) > T0.N-2
				WHERE T1.DocNum = '$DocNum' AND T1.CANCELED = 'N'
			) A0
			LEFT JOIN users A1 ON A0.Ukey = A1.uKey
			ORDER BY A1.LvCode, A1.uName";
		$QRY = MySQLSelectX($SQL);
		while($result = mysqli_fetch_array($QRY)) {
			$Data .= "<tr>
					<td>".$result['FullName']." (".$result['uNickName'].")</td>";
					for($m = 1; $m <= 12; $m++) {
						$Data .= "<td class='text-right'>-</td>";
					}
			$Data .= "</tr>";
		}
	}
	$arrCol['Data'] = $Data;
}

if($_GET['p'] == 'ChkStock') {
	$ItemCode = $_POST['ItemCode'];
	$CPType = $_POST['CPType'];

	if($CPType != "2") {
		$WhsCode = "'KSY','KSM','KB4','MT','MT2','TT-C','OUL','KBM'";
	} else {
		$WhsCode = "'KB5','KB5.1','KB6','KB6.1'";
	}
	$SQL2 = 
		"SELECT TOP 1
			T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr,
			(CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc,T0.LastPurPrc) ELSE T0.LastPurPrc END) * 1.07  AS 'PriceAfVAT',
			SUM(T2.OnHand) AS 'OpenStock', SUM(T2.OnOrder) AS 'OnOrder'
		FROM OITM T0
		LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode
		LEFT JOIN OITW T2 ON T0.ItemCode = T2.ItemCode
		WHERE T0.ItemCode = '$ItemCode' AND T2.WhsCode IN ($WhsCode)
		GROUP BY T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr, (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc,T0.LastPurPrc) ELSE T0.LastPurPrc END)";
	$QRY2 = SAPSelect($SQL2);
	$RST2 = odbc_fetch_array($QRY2);
	$arrCol['OpenStock'] = "( คงเหลือ : ".number_format($RST2['OpenStock'],0)." + สั่งซื้อเพิ่มเติม : ".number_format($RST2['OnOrder'],0)." ) รวมทั้งหมด ".number_format($RST2['OpenStock']+$RST2['OnOrder'],0)." ".conutf8($RST2['SalUnitMsr']);
}

if($_GET['p'] == 'CalData') {
	$ItemCode = $_POST['ItemCode'];
	$DocNum   = $_POST['CPDocNum'];
	switch ($_POST['TeamCode']) {
		case 'MT1': $TeamCode = "โมเดิร์นเทรด 1"; break;
		case 'MT2': $TeamCode = "โมเดิร์นเทรด 2"; break;
		case 'TT2': $TeamCode = "ร้านค้าเขตต่างจังหวัด"; break;
		case 'OUL': $TeamCode = "หน้าร้าน + ร้านค้าเขตกรุงเทพฯ"; break;
		case 'ONL': $TeamCode = "ออนไลน์"; break;
	}
	$TarCam = $_POST['TarCam'];
	$CPType = $_POST['CPType'];

	if($CPType != "2") {
		$WhsCode = "'KSY','KSM','KB4','MT','MT2','TT-C','OUL','KBM'";
	} else {
		$WhsCode = "'KB5','KB5.1','KB6','KB6.1'";
	}
	$SQL2 = 
		"SELECT TOP 1
			T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr,
			(CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc,T0.LastPurPrc) ELSE T0.LastPurPrc END) * 1.07  AS 'PriceAfVAT',
			SUM(T2.OnHand) AS 'OpenStock', SUM(T2.OnOrder) AS 'OnOrder'
		FROM OITM T0
		LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode
		LEFT JOIN OITW T2 ON T0.ItemCode = T2.ItemCode
		WHERE T0.ItemCode = '$ItemCode' AND T2.WhsCode IN ($WhsCode)
		GROUP BY T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr, (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc,T0.LastPurPrc) ELSE T0.LastPurPrc END)";
	$QRY2 = SAPSelect($SQL2);
	$RST2 = odbc_fetch_array($QRY2);

	// $CheckStock = $RST2['OpenStock']+$RST2['OnOrder'];
	$CheckStock = 999999999;


	if($TarCam <= $CheckStock) {
		$SQL1 = "SELECT T0.MngType, T0.StartDate, T0.EndDate, T0.TeamCode FROM tarsku_header T0 WHERE T0.DocNum = '$DocNum' AND T0.CANCELED = 'N' LIMIT 1";
		$result1 = MySQLSelect($SQL1);

		$NumMonth = 0;
		for($m = 1; $m <= 12; $m++) {
			if($m >= intval(date("m",strtotime($result1['StartDate']))) && $m <= intval(date("m",strtotime($result1['EndDate'])))) {
				$NumMonth++;
			}
		}

		$Data = ""; 
		$Row = 1;
		if($result1['MngType'] == "T") {
			$TarAll = 0;
			$Tar = ceil(($TarCam/$NumMonth));
			$Data .= "<tr>
						<td>".$TeamCode."</td>";
						for($m = 1; $m <= 12; $m++) {
							if($m >= intval(date("m",strtotime($result1['StartDate']))) && $m <= intval(date("m",strtotime($result1['EndDate'])))) {
								if($m == intval(date("m",strtotime($result1['EndDate'])))) {
									$TarValue = ceil(($TarCam/$Row)) - $TarAll;
									if($TarValue <= 0) {
										$TarValue = 1;
									}
								} else {
									$TarValue = $Tar;
								}
								$Data .= "<td class='table-danger'><input type='number' class='form-control form-control-sm text-right' name='TarM".$m."_".$Row."' id='TarM".$m."_".$Row."' value='".$TarValue."' /></td>";
								$TarAll = $TarAll+$Tar;
							}else{
								$Data .= "<td class='text-right table-active'><input type='hidden' class='form-control form-control-sm text-right' name='TarM".$m."_".$Row."' id='TarM".$m."_".$Row."' value='' /></td>";
							}
						}
			$Data .= "</tr>";
		}else{
			$SQL2 = "
				SELECT
					A0.Ukey, CONCAT(A1.uName,' ',A1.uLastName) AS 'FullName', A1.uNickName, A1.LvCode
				FROM (
					SELECT
					SUBSTRING_INDEX(SUBSTRING_INDEX(T1.SaleUkey, ',', T0.N), ',', -1) AS 'Ukey'
					FROM
					(
					SELECT 1 N UNION ALL
					SELECT 2 UNION ALL
					SELECT 3 UNION ALL
					SELECT 4 UNION ALL
					SELECT 5 UNION ALL
					SELECT 6 UNION ALL
					SELECT 7 UNION ALL
					SELECT 8 UNION ALL
					SELECT 9 UNION ALL
					SELECT 10 UNION ALL
					SELECT 11 UNION ALL
					SELECT 12 UNION ALL
					SELECT 13 UNION ALL
					SELECT 14 UNION ALL
					SELECT 15 UNION ALL
					SELECT 16 UNION ALL
					SELECT 17 UNION ALL
					SELECT 18 UNION ALL
					SELECT 19 UNION ALL
					SELECT 20
					) T0
					INNER JOIN tarsku_header T1 ON char_length(T1.SaleUkey) - character_length(REPLACE(T1.SaleUkey,',','')) > T0.N-2
					WHERE T1.DocNum = '$DocNum' AND T1.CANCELED = 'N'
				) A0
				LEFT JOIN users A1 ON A0.Ukey = A1.ukey
				ORDER BY A1.LvCode, A1.uName";
			// echo $SQL2;
			$QRY2 = MySQLSelectX($SQL2); 
			while($result2 = mysqli_fetch_array($QRY2)) {
				$GetName['Name'][$Row] = $result2['FullName']." (".$result2['uNickName'].")";
				$GetName['Ukey'][$Row] = $result2['Ukey'];
				$Row++;
			}

			$Tar = ceil(($TarCam/$NumMonth)/($Row-1));
			// echo "TarCam ".$TarCam." |NumMonth ".$NumMonth." |Row ".$Row." |ceil ".$Tar;
			for($r = 1; $r < $Row; $r++) {
				$TarAll = 0;
				$Data .= "<tr>
							<td>".
								$GetName['Name'][$r].
								"<input type='hidden' value='".$GetName['Ukey'][$r]."' name='ukey_$r' id='ukey_$r'>".
							"</td>";
							for($m = 1; $m <= 12; $m++) {
								if($m >= intval(date("m",strtotime($result1['StartDate']))) && $m <= intval(date("m",strtotime($result1['EndDate'])))) {
									if($m == intval(date("m",strtotime($result1['EndDate'])))) {
										$TarValue = ceil(($TarCam/($Row-1))) - $TarAll;
										if($TarValue <= 0) {
											$TarValue = 1;
										}
									} else {
										$TarValue = $Tar;
									}
									$Data .= "<td class='table-danger'><input type='number' class='form-control form-control-sm text-right' name='TarM".$m."_$r' id='TarM".$m."_$r' value='".$TarValue."' /></td>";
									$TarAll = $TarAll+$Tar;
								}else{
									$Data .= "<td class='text-right table-active'><input type='hidden' class='form-control form-control-sm text-right' name='TarM".$m."_$r' id='TarM".$m."_$r' value='' /></td>";
								}
							}
				$Data .= "</tr>";
			}
		}

		$arrCol['Row']  = ($Row-1);
		$arrCol['Data'] = $Data;
		$arrCol['ChkStock'] = "Y";
	}else{
		$arrCol['ChkStock'] = "N";
	}
}

if($_GET['p'] == 'SaveList') {
	$ItemCode = $_POST['ItemSelect'];
	$Row      = $_POST['tmpRow'];
	$TarCam   = $_POST['TarCampaign'];
	$CPEntry  = $_POST['CPEntry'];
	$DocNum   = $_POST['CPDocNum'];
	$CPType   = $_POST['CPType'];
	$RowID    = $_POST['RowID'];
	$CreateUkey = $_SESSION['ukey'];

	/* GET ITEMNAME, COST, OpenStock AND PRODUCT STATUS */
	
	if($CPType != "2") {
		$WhsCode = "'KSY','KSM','KB4','MT','MT2','TT-C','OUL','KBM','PLA'";
	} else {
		$WhsCode = "'KB5','KB5.1','KB6','KB6.1'";
	}
	$SQL2 = 
		"SELECT TOP 1
			T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr,
			(CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc,T0.LastPurPrc) ELSE T0.LastPurPrc END) * 1.07  AS 'PriceAfVAT',
			SUM(T2.OnHand) AS 'OpenStock', SUM(T2.OnOrder) AS 'OnOrder' 
		FROM OITM T0
		LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode
		LEFT JOIN OITW T2 ON T0.ItemCode = T2.ItemCode
		WHERE T0.ItemCode = '$ItemCode' AND T2.WhsCode IN ($WhsCode)
		GROUP BY T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr, (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc,T0.LastPurPrc) ELSE T0.LastPurPrc END)";
	$QRY2 = SAPSelect($SQL2);
	$RST2 = odbc_fetch_array($QRY2);

	// $CheckStock = $RST2['OpenStock']+$RST2['OnOrder'];
	$CheckStock = 999999999;


	if($TarCam <= $CheckStock) {
		$SQL1 = "SELECT T0.MngType FROM tarsku_header T0 WHERE T0.DocNum = '$DocNum' AND T0.CANCELED = 'N' LIMIT 1";
		$result1 = MySQLSelect($SQL1);
		
		$ChkRow = CHKRowDB("SELECT T0.* FROM tarsku_itemlist T0 WHERE T0.DocNum = '$DocNum' AND T0.ItemCode = '$ItemCode' AND T0.RowStatus = 'A' AND T0.RowId = $RowID");
		if($ChkRow == 0) {
			/* ADD NEW VISORDER */
			$SQL1 = "SELECT IFNULL(MAX(T0.VisOrder),-1)+1 AS 'VisOrder' FROM tarsku_itemlist T0 WHERE T0.DocNum = '$DocNum' AND T0.RowStatus = 'A' LIMIT 1";
			$RST1 = MySQLSelect($SQL1);
			$VisOrder = $RST1['VisOrder'];
		
			$ItemName      = conutf8($RST2['ItemName']);
			$ItemCode      = $RST2['ItemCode'];
			$ProductStatus = $RST2['U_ProductStatus'];
			$Cxst          = $RST2['PriceAfVAT'];
			$OpenStock     = $RST2['OpenStock'];
			$UnitMsr       = conutf8($RST2['SalUnitMsr']);
		
		
			/* INSERT INTO tarsku_itemlist */
			$INSERT_LIST = "
				INSERT INTO tarsku_itemlist 
				SET CPEntry  = $CPEntry,
					DocNum   = '$DocNum',
					VisOrder = '$VisOrder',
					ItemCode = '$ItemCode',
					ProductStatus = '$ProductStatus',
					OpenStock = $OpenStock,
					UnitMsr  = '$UnitMsr',
					Cxst     = $Cxst,
					TargetTotal = $TarCam,
					CreateUkey = '$CreateUkey'";
			$RowID = MySQLInsert($INSERT_LIST);
			
			if($RowID != "") {
				/* INSERT INTO tarsku_detail */
				if($result1['MngType'] == 'T') {
					$INSERT_DETAIL = "
						INSERT INTO tarsku_detail 
						SET CPEntry  = $CPEntry,
							RowID    = $RowID,
							ItemCode = '$ItemCode',
							SaleUkey = NULL,";
							for($m = 1; $m <= 12; $m++) {
								if($_POST['TarM'.$m.'_1'] == "") {
									$TarM = "NULL";
								}else{
									$TarM = $_POST['TarM'.$m.'_1'];
								}
								if($m < 10) {
									$INSERT_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
								}else{
									$INSERT_DETAIL .= "Tar_M".$m." = ".$TarM.",";
								}
							}
							$INSERT_DETAIL .= "CreateUkey = '$CreateUkey'";
					MySQLInsert($INSERT_DETAIL);
				}else{
					for($r = 1; $r <= $Row; $r++) {
						$UkeySale = $_POST['ukey_'.$r];
						$INSERT_DETAIL = "
						INSERT INTO tarsku_detail 
						SET CPEntry  = $CPEntry,
							RowID    = $RowID,
							ItemCode = '$ItemCode',
							SaleUkey = '$UkeySale',";
							for($m = 1; $m <= 12; $m++) {
								if($_POST['TarM'.$m.'_'.$r] == "") {
									$TarM = "NULL";
								}else{
									$TarM = $_POST['TarM'.$m.'_'.$r];
								}
								if($m < 10) {
									$INSERT_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
								}else{
									$INSERT_DETAIL .= "Tar_M".$m." = ".$TarM.",";
								}
							}
							$INSERT_DETAIL .= "CreateUkey = '$CreateUkey'";
						MySQLInsert($INSERT_DETAIL);
					}
				}
				$arrCol['SUCCESS'] = "Y";
			}else{
				$arrCol['SUCCESS'] = "N";
			}
		}else{
			$UPDATE_LIST = "
				UPDATE tarsku_itemlist
				SET TargetTotal = $TarCam,
					UpdateUkey = '$CreateUkey',
					UpdateDate = NOW()
				WHERE DocNum = '$DocNum' AND RowStatus = 'A' AND RowID = $RowID";
			MySQLUpdate($UPDATE_LIST);

			if($result1['MngType'] == 'T') {
				$UPDATE_DETAIL = "
					UPDATE tarsku_detail
					SET ";
						for($m = 1; $m <= 12; $m++) {
							if($_POST['TarM'.$m.'_1'] == "") {
								$TarM = "NULL";
							}else{
								$TarM = $_POST['TarM'.$m.'_1'];
							}
							if($m < 10) {
								$UPDATE_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
							}else{
								$UPDATE_DETAIL .= "Tar_M".$m." = ".$TarM.",";
							}
						}
						$UPDATE_DETAIL .= "
						UpdateUkey = '$CreateUkey',
						UpdateDate = NOW()
					WHERE RowID = $RowID AND ItemCode = '$ItemCode' AND TargetStatus = 'A'";
				MySQLUpdate($UPDATE_DETAIL);
			}else{
				for($r = 1; $r <= $Row; $r++) {
					$UkeySale = $_POST['ukey_'.$r];
					$UPDATE_DETAIL = "
						UPDATE tarsku_detail
						SET ";
							for($m = 1; $m <= 12; $m++) {
								if($_POST['TarM'.$m.'_'.$r] == "") {
									$TarM = "NULL";
								}else{
									$TarM = $_POST['TarM'.$m.'_'.$r];
								}
								if($m < 10) {
									$UPDATE_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
								}else{
									$UPDATE_DETAIL .= "Tar_M".$m." = ".$TarM.",";
								}
							}
							$UPDATE_DETAIL .= "
							UpdateUkey = '$CreateUkey',
							UpdateDate = NOW()
						WHERE RowID = $RowID AND ItemCode = '$ItemCode' AND TargetStatus = 'A' AND SaleUkey = '$UkeySale'";
					MySQLUpdate($UPDATE_DETAIL);
				}
			}
			$arrCol['SUCCESS'] = "Y";
		}
		$arrCol['ChkStock'] = "Y";
	}else{
		$arrCol['ChkStock'] = "N";
	}

}

if($_GET['p'] == 'ShowList') {
	$DocNum = $_POST['CPDocNum'];
	$SQL = "
		SELECT T0.RowID, T0.ItemCode, T1.ItemName, T0.ProductStatus, T0.Cxst, T0.OpenStock, T0.TargetTotal, T2.DocStatus, T0.UnitMsr
		FROM tarsku_itemlist T0 
		LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
		LEFT JOIN tarsku_header T2 ON T2.DocNum = T0.DocNum
		WHERE T0.DocNum = '$DocNum' AND T0.RowStatus = 'A' AND T2.CANCELED = 'N'
		ORDER BY T0.VisOrder";
	$QRY = MySQLSelectX($SQL);
	$Data = ""; $No = 0;
	while($result = mysqli_fetch_array($QRY)){
		$No++;
		$Data .= "
			<tr>
				<td class='text-center'>".$No."</td>
				<td class='text-center'>".$result['ItemCode']."</td>
				<td>".$result['ItemName']."</td>
				<td class='text-center'>".$result['ProductStatus']."</td>
				<td class='text-right'>".number_format($result['Cxst'],2)."</td>
				<td class='text-right'>".number_format($result['OpenStock'],0)."</td>
				<td>".$result['UnitMsr']."</td>
				<td class='text-right'>".number_format($result['TargetTotal'],0)."</td>
				<td class='text-center'>
					<div calss='dropdown'>
						<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>
							<i class='fas fa-cog fa-fw fa-1x'></i>
						</button>
						<ul class='dropdown-menu' style='font-size: 13px;'>
							<li><a href='javascript:void(0);' class='dropdown-item' onclick='EditList(\"".$DocNum."\",".$result['RowID'].");'><i class='fas fa-edit fa-fw text-warning'></i> แก้ไข</a></li>
							<li><a href='javascript:void(0);' class='dropdown-item' onclick='DeleteList(".$result['RowID'].",\"".$result['DocStatus']."\");'><i class='fas fa-trash-alt fa-fw text-danger'></i> ลบ</a></li>
						</ul>
					</div>
				</td>
			</tr>"; 
	}
	if($No == 0) {
		$Data .= "<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td></tr>";
	}
	$arrCol['Data'] = $Data;
}

if($_GET['p'] == 'EditList') {
	$DocNum = $_POST['DocNum'];
	$RowID  = $_POST['RowID'];

	$SQL1 = "SELECT T0.MngType, T0.StartDate, T0.EndDate, T0.TeamCode, T0.SaleUkey FROM tarsku_header T0 WHERE T0.DocNum = '$DocNum' AND T0.CANCELED = 'N' LIMIT 1";
	$RST_HEAD = MySQLSelect($SQL1);

	$ArrSaleUkey = explode(",",$RST_HEAD['SaleUkey']);

	switch ($RST_HEAD['TeamCode']) {
		case 'MT1': $TeamCode = "โมเดิร์นเทรด 1"; break;
		case 'MT2': $TeamCode = "โมเดิร์นเทรด 2"; break;
		case 'TT2': $TeamCode = "ร้านค้าเขตต่างจังหวัด"; break;
		case 'OUL': $TeamCode = "หน้าร้าน + ร้านค้าเขตกรุงเทพฯ"; break;
		case 'ONL': $TeamCode = "ออนไลน์"; break;
	}

	$SQL = "
		SELECT T0.ItemCode, T0.TargetTotal,
			T1.SaleUkey, CONCAT(T2.uName,' ',T2.uLastName) AS 'FullName', T2.uNickName, T2.LvCode, T0.DocNum,
			T1.Tar_M01, T1.Tar_M02, T1.Tar_M03, T1.Tar_M04, T1.Tar_M05, T1.Tar_M06,
			T1.Tar_M07, T1.Tar_M08, T1.Tar_M09, T1.Tar_M10, T1.Tar_M11, T1.Tar_M12
		FROM tarsku_itemlist T0
		LEFT JOIN tarsku_detail T1 ON T1.RowID = T0.RowID
		LEFT JOIN users T2 ON T2.ukey = T1.SaleUkey
		WHERE T0.RowID = $RowID AND T0.RowStatus = 'A' AND T1.TargetStatus = 'A'
		ORDER BY T2.uName, T2.LvCode";

	$Data = ""; $ItemCode = 0; $TarCam = 0;
	$Row = 1;
	if($RST_HEAD['MngType'] == "T") {
		$SQL .= " LIMIT 1";
		$result = MySQLSelect($SQL);
;		$Data .="<tr>
					<td>"
						.$TeamCode.
					"</td>";
					for($m = 1; $m <= 12; $m++) {
						if($m < 10) {
							if($result['Tar_M0'.$m] != "") {
								$Data .= "<td class='table-danger'><input type='number' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='".$result['Tar_M0'.$m]."' /></td>";
							}else{
								$Data .= "<td class='text-right table-active'><input type='hidden' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='' /></td>";
							}
						}else{
							if($result['Tar_M'.$m] != "") {
								$Data .= "<td class='table-danger'><input type='number' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='".$result['Tar_M'.$m]."' /></td>";
							}else{
								$Data .= "<td class='text-right table-active'><input type='hidden' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='' /></td>";
							}
						}
					}
		$Data .="</tr>";
		$ItemCode = $result['ItemCode'];
		$TarCam   = $result['TargetTotal'];
	}else{
		$QRY = MySQLSelectX($SQL);
		while($result = mysqli_fetch_array($QRY)) {
			$Data .="<tr>
						<td>"
							.$result['FullName']." (".$result['uNickName'].")".
							"<input type='hidden' value='".$result['SaleUkey']."' name='ukey_$Row' id='ukey_$Row'>".
						"</td>";
						for($m = 1; $m <= 12; $m++) {
							$ChkM[$m] = 0;
							if($m < 10) {
								if($result['Tar_M0'.$m] != "") {
									$ChkM[$m] = 1;
									$Data .= "<td class='table-danger'><input type='number' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='".$result['Tar_M0'.$m]."' /></td>";
								}else{
									$Data .= "<td class='text-right table-active'><input type='hidden' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='' /></td>";
								}
							}else{
								if($result['Tar_M'.$m] != "") {
									$ChkM[$m] = 1;
									$Data .= "<td class='table-danger'><input type='number' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='".$result['Tar_M'.$m]."' /></td>";
								}else{
									$Data .= "<td class='text-right table-active'><input type='hidden' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='' /></td>";
								}
							}
						}
			$Data .="</tr>";
			$temSale['uKey'][$Row] = $result['SaleUkey'];

			$Row++;

			$ItemCode = $result['ItemCode'];
			$TarCam   = $result['TargetTotal'];
		}

		$n = 0; $GetNewSale = array();
		for($r = 0; $r < count($ArrSaleUkey); $r++) {
			$tmpArr = 0;
			for($i = 1; $i <= count($temSale['uKey']); $i++) {
				if($ArrSaleUkey[$r] == $temSale['uKey'][$i]) {
					$tmpArr++;
				}
			}
			if($tmpArr == 0) {
				$n++;
				array_push($GetNewSale, $ArrSaleUkey[$r]);
			}
		}

		for($r = 0; $r < count($GetNewSale); $r++) {
			$Chk = "SELECT CONCAT(T0.uName,' ',T0.uLastName) AS 'FullName', T0.uNickName FROM users T0 WHERE T0.uKey = '".$GetNewSale[$r]."' LIMIT 1";
			$RST_Ck = MySQLSelect($Chk);
			$Data .="<tr>
						<td>"
							.$RST_Ck['FullName']." (".$RST_Ck['uNickName'].")".
							"<input type='hidden' value='".$GetNewSale[$r]."' name='ukey_$Row' id='ukey_$Row'>".
						"</td>";
						for($m = 1; $m <= 12; $m++) {
							if($m < 10) {
								if($ChkM[$m] != 0) {
									$Data .= "<td class='table-danger'><input type='number' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='0' /></td>";
								}else{
									$Data .= "<td class='text-right table-active'><input type='hidden' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='' /></td>";
								}
							}else{
								if($ChkM[$m] != 0) {
									$Data .= "<td class='table-danger'><input type='number' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='0' /></td>";
								}else{
									$Data .= "<td class='text-right table-active'><input type='hidden' class='form-control form-control-sm text-right' name='TarM".$m."_$Row' id='TarM".$m."_$Row' value='' /></td>";
								}
							}
						}
			$Data .="</tr>";
			$Row++;
		}
	}
	$arrCol['Row']  = ($Row-1);
	$arrCol['Data'] = $Data;
	$arrCol['ItemCode'] = $ItemCode;
	$arrCol['TarCam'] = $TarCam;
}

if($_GET['p'] == 'DeleteList') {
	$RowID     = $_POST['RowID'];
	$DocStatus = $_POST['DocStatus'];

	if($DocStatus == 'C') {
		$UPDATE_ITEM = "UPDATE tarsku_itemlist SET RowStatus = 'I' WHERE RowID = $RowID AND RowStatus = 'A'";
		MySQLUpdate($UPDATE_ITEM);
	
		$UPDATE_DETAIL = "UPDATE tarsku_detail SET TargetStatus = 'I' WHERE RowID = $RowID AND TargetStatus = 'A'";
		MySQLUpdate($UPDATE_DETAIL);
	}else{
		$DELETE_ITEM = "DELETE FROM tarsku_itemlist WHERE RowID = $RowID AND RowStatus = 'A'";
		MySQLDelete($DELETE_ITEM);
	
		$DELETE_DETAIL = "DELETE FROM tarsku_detail WHERE RowID = $RowID AND TargetStatus = 'A'";
		MySQLDelete($DELETE_DETAIL);
	}
}

if($_GET['p'] == 'SaveStatus') {
	$DocNum = $_POST['CPDocNum'];
	$SQL = "UPDATE tarsku_header SET DocStatus = 'C' WHERE DocNum = '$DocNum' AND CANCELED = 'N'";
	MySQLUpdate($SQL);
}

//  --------------------------------------------------------------------------------- TAB 1 ------------------------------------------------------------------------------------------------ //

if($_GET['p'] == 'ShowTar') {
	$StartDate = date('Y-m-d');

	$SQL = "
		SELECT 
			T0.CPEntry, T0.DocNum, T0.CPTitle, T0.TeamCode, T0.MngType, T0.CPType, T0.StartDate, T0.EndDate, T0.CPDescription, T0.DocStatus,
			DATEDIFF(T0.EndDate, '$StartDate') AS DiffDate
		FROM tarsku_header T0 
		WHERE T0.CANCELED = 'N'";
	$QRY = MySQLSelectX($SQL);
	$r = 0; $No = 0;
	while($result = mysqli_fetch_array($QRY)) {
		switch ($result['TeamCode']) {
			case 'MT1': $TeamCode = "โมเดิร์นเทรด 1"; break;
			case 'MT2': $TeamCode = "โมเดิร์นเทรด 2"; break;
			case 'TT2': $TeamCode = "ต่างจังหวัด"; break;
			case 'OUL': $TeamCode = "หน้าร้าน + เขตกรุงเทพฯ"; break;
			case 'ONL': $TeamCode = "ออนไลน์"; break;
		}

		switch ($result['MngType']) {
			case 'T': $MngType = 'รายทีม'; break;
			case 'P': $MngType  = 'รายบุคคล'; break;
		}

		switch ($result['CPType']) {
			case 'Q': $CPType = 'สินค้าจอง (Quota)'; break;
			case 'F': $CPType = 'สินค้าต้องขาย (Focus)'; break;
			case 'P': $CPType = 'สินค้าโปรโมชั่น (Promotion)'; break;
			case '2': $CPType = 'สินค้ามือสอง (2nd Hand)'; break;
			case 'O': $CPType = 'อื่น ๆ'; break;
			case 'SD': $CPType = 'สถานะ D'; break;
			case 'SR': $CPType = 'สถานะ R'; break;
			case 'SAW': $CPType = 'สถานะ A / W'; break;
			case 'SM': $CPType = 'สถานะ M'; break;
			case 'SN': $CPType = 'สถานะ N'; break;
			case 'SP': $CPType = 'สถานะ P'; break;
			case 'SE': $CPType = 'สถานะ E'; break;
		}

		$No++;
		$arrCol[$r]['DocNum']   = "<a href='javascript:void(0);' onclick='ViewDoc(\"".$result['DocNum']."\",\"".$result['DocStatus']."\");'>".$result['DocNum']."</a>";
		$arrCol[$r]['CPTitle']  = $result['CPTitle'];
		$arrCol[$r]['TeamCode'] = $TeamCode;
		$arrCol[$r]['MngType']  = $MngType;
		$arrCol[$r]['CPType']   = $CPType;
		$arrCol[$r]['CamDate']  = date("d/m/Y",strtotime($result['StartDate']))." ถึง ".date("d/m/Y",strtotime($result['EndDate']));
		if($result['DocStatus'] == 'O') {
			$arrCol[$r]['DocStatus']   = "<span class='badge bg-secondary w-100'><i class='far fa-save fa-fw'></i> บันทึกร่าง</span>";
		}else{
			if(date("Y-m-d") < $result['StartDate']) {
				$arrCol[$r]['DocStatus'] = "<span class='badge bg-info text-dark w-100'><i class='fas fa-hourglass-half fa-fw'></i> รอเวลาแคมเปญ</span>";
			}elseif(date("Y-m-d") >= $result['StartDate'] && date("Y-m-d") <= $result['EndDate']) {
				$arrCol[$r]['DocStatus'] = "
					<span class='badge bg-success w-100'>
						<i class='far fa-clock fa-fw'></i>&nbsp;
						<span class='headtext text1 position-absolute'>กำลังดำเนินการ</span>
						<span class='headtext2 text2'>คงเหลือ ".$result['DiffDate']." วัน</span>
					</span>";
			}else{
				$arrCol[$r]['DocStatus'] = "<span class='badge bg-danger w-100'><i class='fas fa-ban fa-fw'></i> หมดเวลาแคมเปญ</span>";
			}
		}
		$arrCol[$r]['Detail']   = $result['CPDescription'];
		$arrCol[$r]['Manage']   = "<div calss='dropdown'>
										<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>
											<i class='fas fa-cog fa-fw fa-1x'></i>
										</button>
										<ul class='dropdown-menu' style='font-size: 13px;'>
											<li><a href='javascript:void(0);' class='dropdown-item' onclick='Edit(\"".$result['DocNum']."\",\"".$result['DocStatus']."\");'><i class='fas fa-edit fa-fw text-warning'></i> แก้ไข</a></li>
											<li><a href='javascript:void(0);' class='dropdown-item' onclick='DeleteHeader(\"".$result['DocNum']."\",\"".$result['DocStatus']."\");'><i class='fas fa-trash-alt fa-fw text-danger'></i> ลบ</a></li>
										</ul>
									</div>";
		$r++;
	}
}

if($_GET['p'] == 'Edit') {
	$DocNum    = $_POST['DocNum'];
	$DocStatus = $_POST['DocStatus'];
	$SQL1 = "SELECT T0.CPEntry, T0.DocNum, T0.CPTitle, T0.TeamCode, T0.MngType, T0.CPType, T0.StartDate, T0.EndDate, T0.CPDescription, T0.SaleUkey FROM tarsku_header T0 WHERE T0.DocNum = '$DocNum' AND T0.DocStatus = '$DocStatus' AND T0.CANCELED = 'N' LIMIT 1";
	$RST1 = MySQLSelect($SQL1);
	
	switch ($RST1['TeamCode']) {
		case 'MT1': $TeamCode = "โมเดิร์นเทรด 1"; break;
		case 'MT2': $TeamCode = "โมเดิร์นเทรด 2"; break;
		case 'TT2': $TeamCode = "ต่างจังหวัด"; break;
		case 'OUL': $TeamCode = "หน้าร้าน + เขตกรุงเทพฯ"; break;
		case 'ONL': $TeamCode = "ออนไลน์"; break;
	}

	switch ($RST1['MngType']) {
		case 'T': $MngType = 'รายทีม'; break;
		case 'P': $MngType  = 'รายบุคคล'; break;
	}

	switch ($RST1['CPType']) {
		case 'Q': $CPType = 'สินค้าจอง (Quota)'; break;
		case 'F': $CPType = 'สินค้าต้องขาย (Focus)'; break;
		case 'P': $CPType = 'สินค้าโปรโมชั่น (Promotion)'; break;
		case '2': $CPType = 'สินค้ามือสอง (2nd Hand)'; break;
		case 'O': $CPType = 'อื่น ๆ'; break;
		case 'SD': $CPType = 'สถานะ D'; break;
		case 'SR': $CPType = 'สถานะ R'; break;
		case 'SAW': $CPType = 'สถานะ A / W'; break;
		case 'SM': $CPType = 'สถานะ M'; break;
		case 'SN': $CPType = 'สถานะ N'; break;
		case 'SP': $CPType = 'สถานะ P'; break;
		case 'SE': $CPType = 'สถานะ E'; break;
	}

	$DataHeader = "
		<tr>
			<th width='15%'>ชื่อเป้าขายสินค้า</th>
			<td width='25%'>".$RST1['CPTitle']."</td>
			<th width='10%'>วันที่เริ่มต้น</th>
			<td width='20%'>".date("d/m/Y",strtotime($RST1['StartDate']))."</td>
			<th width='15%'>วันที่สิ้นสุด</th>
			<td width='15%'>".date("d/m/Y",strtotime($RST1['EndDate']))."</td>
		</tr>
		<tr>
			<th>ทีมขาย</th>
			<td>".$TeamCode."</td>
			<th>รูปแบบวัดผล</th>
			<td>".$MngType."</td>
			<th>ประเภทเป้าขายสินค้า</th>
			<td>".$CPType."</td>
		</tr>";

	if($RST1['MngType'] == 'T') {
		$DataHeader .= "
			<tr>
				<th class='pb-3'>รายละเอียดเป้าขายสินค้า</th>
				<td class='pb-3' colspan='5'>".$RST1['CPDescription']."</td>
			</tr>";
	}else{
		$DataHeader .= "
			<tr>
				<th class='pb-3'>รายละเอียดเป้าขายสินค้า</th>
				<td class='pb-3'>".$RST1['CPDescription']."</td>
				<td class='pb-3 text-primary'>เพิ่มพนักงานขาย</td>
				<td class='pb-3' colspan='4'>
					<button class='btn btn-sm btn-primary ' onclick='ModalAddSale();'><i class='fas fa-user-plus fa-fw'></i></button>
				</td>
			</tr>";
	}
		
	$arrCol['TeamCode']   = $RST1['TeamCode'];
	$arrCol['CPType']     = $RST1['CPType'];
	$arrCol['DataHeader'] = $DataHeader;

	$SQL = "
		SELECT T0.RowID, T0.ItemCode, T1.ItemName, T0.ProductStatus, T0.Cxst, T0.OpenStock, T0.TargetTotal, T2.DocStatus, T0.UnitMsr
		FROM tarsku_itemlist T0 
		LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
		LEFT JOIN tarsku_header T2 ON T2.DocNum = T0.DocNum
		WHERE T0.DocNum = '$DocNum' AND T0.RowStatus = 'A' AND T2.CANCELED = 'N'
		ORDER BY T0.VisOrder";
	$QRY = MySQLSelectX($SQL);
	$Data = ""; $No = 0;
	while($result = mysqli_fetch_array($QRY)){
		$No++;
		$Data .= "
			<tr>
				<td class='text-center'>".$No."</td>
				<td class='text-center'>".$result['ItemCode']."</td>
				<td>".$result['ItemName']."</td>
				<td class='text-center'>".$result['ProductStatus']."</td>
				<td class='text-right'>".number_format($result['Cxst'],2)."</td>
				<td class='text-right'>".number_format($result['OpenStock'],0)."</td>
				<td>".$result['UnitMsr']."</td>
				<td class='text-right'>".number_format($result['TargetTotal'],0)."</td>
				<td class='text-center'>
					<div calss='dropdown'>
						<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>
							<i class='fas fa-cog fa-fw fa-1x'></i>
						</button>
						<ul class='dropdown-menu' style='font-size: 13px;'>
							<li><a href='javascript:void(0);' class='dropdown-item' onclick='EditList2(\"".$DocNum."\",".$result['RowID'].");'><i class='fas fa-edit fa-fw text-warning'></i> แก้ไข</a></li>
							<li><a href='javascript:void(0);' class='dropdown-item' onclick='DeleteList2(".$result['RowID'].",\"".$result['DocStatus']."\");'><i class='fas fa-trash-alt fa-fw text-danger'></i> ลบ</a></li>
						</ul>
					</div>
				</td>
			</tr>"; 
	}
	if($No == 0) {
		$Data .= "<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td></tr>";
	}
	$arrCol['Data']     = $Data;
	$arrCol['CPEntry']  = $RST1['CPEntry'];
	$arrCol['CPType']   = $RST1['CPType'];
	$arrCol['TeamCode'] = $RST1['TeamCode'];
	$arrCol['ChkMngType'] = $RST1['MngType'];
	if($RST1['MngType'] == 'P') {
		$ArrSaleUkey = explode(",",$RST1['SaleUkey']);
		$arrCol['MngType']  = $ArrSaleUkey[0];
	}else{
		$arrCol['MngType'] = "T";
	}
}

if($_GET['p'] == 'GetSaleEdit') {
	$DocNum    = $_POST['DocNum'];
	$DocStatus = $_POST['DocStatus'];
	$SQL1 = "SELECT T0.TeamCode, T0.SaleUkey, T0.MngType FROM tarsku_header T0 WHERE T0.DocNum = '$DocNum' AND T0.DocStatus = '$DocStatus' AND T0.CANCELED = 'N' LIMIT 1";
	$RST1 = MySQLSelect($SQL1);
	$ArrSaleUkey = explode(",",$RST1['SaleUkey']);
	$SaleUkey = "";
	for($u = 0; $u <= count($ArrSaleUkey)-1; $u++) {
		$SaleUkey .= "'".$ArrSaleUkey[$u]."'";
		if($u != count($ArrSaleUkey)-1) {
			$SaleUkey .= ",";
		}
	}
	$SQL_GET_SALE = "
			SELECT DISTINCT
			T0.Ukey, CONCAT(T1.uName,' ',T1.uLastName) AS 'FullName', T1.uNickName
		FROM saletarget T0
		LEFT JOIN users T1 ON T0.Ukey = T1.uKey
		WHERE T0.DocYear = YEAR(NOW()) AND T0.TeamCode LIKE '".$RST1['TeamCode']."%' AND T0.DocStatus = 'A' AND T0.Ukey NOT IN ($SaleUkey)
		ORDER BY T0.TeamCode, T1.uName ASC";
	$QRY_GET_SALE = MySQLSelectX($SQL_GET_SALE);
	$option = "";
	$r = 0;
	while($RST_GS = mysqli_fetch_array($QRY_GET_SALE)) {
		$r++;
		$option .= "<option value='".$RST_GS['Ukey']."'>".$RST_GS['FullName']." (".$RST_GS['uNickName'].")</option>";
	}
	$arrCol['option'] = $option;
	$arrCol['Row'] = $r;
}

if($_GET['p'] == 'ConDeleteHeader') {
	$DocNum     = $_POST['DocNum'];
	$UpdateUkey = $_SESSION['ukey'];

	if($_POST['DocStatus'] == 'C') {
		$UPDATE_HEADER = "UPDATE tarsku_header SET CANCELED = 'Y', UpdateUkey = '$UpdateUkey', UpdateDate = NOW() WHERE DocNum = '$DocNum' AND CANCELED = 'N'";
		MySQLUpdate($UPDATE_HEADER);
	
		$UPDATE_ITEM = "UPDATE tarsku_itemlist SET RowStatus = 'I', UpdateUkey = '$UpdateUkey', UpdateDate = NOW() WHERE DocNum = '$DocNum' AND RowStatus = 'A'";
		MySQLUpdate($UPDATE_ITEM);

		$SQL = "SELECT RowID FROM tarsku_itemlist WHERE DocNum = '$DocNum'";
		$QRY = MySQLSelectX($SQL);
		while($RST = mysqli_fetch_array($QRY)) {
			$UPDATE_DETAIL = "UPDATE tarsku_detail SET TargetStatus = 'I', UpdateUkey = '$UpdateUkey', UpdateDate = NOW() WHERE RowID = ".$RST['RowID']." AND TargetStatus ='A'";
			MySQLUpdate($UPDATE_DETAIL);
		}
	}else{
		$DELETE_HEADER = "DELETE FROM tarsku_header WHERE DocNum = '$DocNum'";
		MySQLDelete($DELETE_HEADER);
	
		$DELETE_ITEM = "DELETE FROM tarsku_itemlist WHERE DocNum = '$DocNum'";
		MySQLDelete($DELETE_ITEM);
	
		$SQL = "SELECT RowID FROM tarsku_itemlist WHERE DocNum = '$DocNum'";
		$QRY = MySQLSelectX($SQL);
		while($RST = mysqli_fetch_array($QRY)) {
			$DELETE_DETAIL = "DELETE FROM tarsku_detail WHERE RowID = ".$RST['RowID']."";
			MySQLDelete($DELETE_DETAIL);
		}
	}
}

if($_GET['p'] == 'AddSale') {
	$DocNum     = $_POST['DocNum'];
	$UpdateUkey = $_SESSION['ukey'];
	$MngTypeArr = array();
	foreach($_POST['EditMngType'] as $data) { 
		array_push($MngTypeArr,$data);
	}
	$AllSaleUkey = implode(",",$MngTypeArr);
	
	$SQL1 = "SELECT T0.SaleUkey, T0.DocStatus FROM tarsku_header T0 WHERE T0.DocNum = '$DocNum' AND T0.CANCELED = 'N' LIMIT 1";
	$RST1 = MySQLSelect($SQL1);

	$SaleUkey = $RST1['SaleUkey'].",".$AllSaleUkey;

	if($RST1['DocStatus'] == 'C') {
		$UPDATE = "UPDATE tarsku_header SET CANCELED = 'Y', UpdateUkey = '$UpdateUkey', UpdateDate = NOW() WHERE DocNum = '$DocNum' AND CANCELED = 'N'";
		MySQLUpdate($UPDATE);
		$DocStatus = $RST1['DocStatus'];
		$INSERT = "
			INSERT INTO tarsku_header 
				(DocNum, TeamCode, MngType, CPType,	DocStatus, CPTitle, CPDescription, SaleUkey, StartDate, EndDate, CreateUkey) 
			SELECT 
				DocNum, TeamCode, MngType, CPType, '$DocStatus', CPTitle, CPDescription, '$SaleUkey', StartDate, EndDate, '$UpdateUkey'
			FROM tarsku_header 
			WHERE DocNum = '$DocNum' AND CANCELED = 'Y'
			ORDER BY CreateDate DESC";
		MySQLInsert($INSERT);
	}else{
		$UPDATE = "UPDATE tarsku_header SET SaleUkey = '$SaleUkey', UpdateUkey = '$UpdateUkey', UpdateDate = NOW() WHERE DocNum = '$DocNum' AND CANCELED = 'N'";
		MySQLUpdate($UPDATE);
	}
}


if($_GET['p'] == 'ShowList2') {
	$DocNum = $_POST['CPDocNum'];
	$SQL = "
		SELECT T0.RowID, T0.ItemCode, T1.ItemName, T0.ProductStatus, T0.Cxst, T0.OpenStock, T0.TargetTotal, T2.DocStatus, T0.UnitMsr
		FROM tarsku_itemlist T0 
		LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
		LEFT JOIN tarsku_header T2 ON T2.DocNum = T0.DocNum
		WHERE T0.DocNum = '$DocNum' AND T0.RowStatus = 'A' AND T2.CANCELED = 'N'
		ORDER BY T0.VisOrder";
	$QRY = MySQLSelectX($SQL);
	$Data = ""; $No = 0;
	while($result = mysqli_fetch_array($QRY)){
		$No++;
		$Data .= "
			<tr>
				<td class='text-center'>".$No."</td>
				<td class='text-center'>".$result['ItemCode']."</td>
				<td>".$result['ItemName']."</td>
				<td class='text-center'>".$result['ProductStatus']."</td>
				<td class='text-right'>".number_format($result['Cxst'],2)."</td>
				<td class='text-right'>".number_format($result['OpenStock'],0)."</td>
				<td>".$result['UnitMsr']."</td>
				<td class='text-right'>".number_format($result['TargetTotal'],0)."</td>
				<td class='text-center'>
					<div calss='dropdown'>
						<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>
							<i class='fas fa-cog fa-fw fa-1x'></i>
						</button>
						<ul class='dropdown-menu' style='font-size: 13px;'>
							<li><a href='javascript:void(0);' class='dropdown-item' onclick='EditList2(\"".$DocNum."\",".$result['RowID'].");'><i class='fas fa-edit fa-fw text-warning'></i> แก้ไข</a></li>
							<li><a href='javascript:void(0);' class='dropdown-item' onclick='DeleteList2(".$result['RowID'].",\"".$result['DocStatus']."\");'><i class='fas fa-trash-alt fa-fw text-danger'></i> ลบ</a></li>
						</ul>
					</div>
				</td>
			</tr>"; 
	}
	if($No == 0) {
		$Data .= "<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td></tr>";
	}
	$arrCol['Data'] = $Data;
}

if($_GET['p'] == 'SaveList2') {
	$ItemCode = $_POST['ItemSelect'];
	$Row      = $_POST['tmpRow'];
	$TarCam   = $_POST['TarCampaign'];
	$CPEntry  = $_POST['CPEntry'];
	$DocNum   = $_POST['CPDocNum'];
	$CPType   = $_POST['CPType'];
	$RowID   = $_POST['RowID'];
	$DocStatus  = $_POST['DocStatus'];
	$CreateUkey = $_SESSION['ukey'];
	
	if($CPType != "2") {
		$WhsCode = "'KSY','KSM','KB4','MT','MT2','TT-C','OUL','KBM','PLA'";
	} else {
		$WhsCode = "'KB5','KB5.1','KB6','KB6.1'";
	}
	$SQL2 = 
		"SELECT TOP 1
			T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr,
			(CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc,T0.LastPurPrc) ELSE T0.LastPurPrc END) * 1.07  AS 'PriceAfVAT',
			SUM(T2.OnHand) AS 'OpenStock', SUM(T2.OnOrder) AS 'OnOrder' 
		FROM OITM T0
		LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode
		LEFT JOIN OITW T2 ON T0.ItemCode = T2.ItemCode
		WHERE T0.ItemCode = '$ItemCode' AND T2.WhsCode IN ($WhsCode)
		GROUP BY T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr, (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc,T0.LastPurPrc) ELSE T0.LastPurPrc END)";
	$QRY2 = SAPSelect($SQL2);
	$RST2 = odbc_fetch_array($QRY2);

	// $CheckStock = $RST2['OpenStock']+$RST2['OnOrder'];
	$CheckStock = 999999999;

	if($TarCam <= $CheckStock) {
		$SQL1 = "SELECT T0.MngType FROM tarsku_header T0 WHERE T0.DocNum = '$DocNum' AND T0.CANCELED = 'N' LIMIT 1";
		$result1 = MySQLSelect($SQL1);

		$ChkRow = CHKRowDB("SELECT T0.* FROM tarsku_itemlist T0 WHERE T0.DocNum = '$DocNum' AND T0.ItemCode = '$ItemCode' AND T0.RowStatus = 'A' AND T0.RowId = $RowID");
		if($ChkRow != 0) {
			if($DocStatus == 'O') {
				$UPDATE_LIST = "
					UPDATE tarsku_itemlist
					SET TargetTotal = $TarCam,
						UpdateUkey = '$CreateUkey',
						UpdateDate = NOW()
					WHERE DocNum = '$DocNum' AND RowStatus = 'A' AND RowID = $RowID";
				MySQLUpdate($UPDATE_LIST);
				
				if($result1['MngType'] == 'T') {
					$ChkDetail = CHKRowDB("SELECT * FROM tarsku_detail WHERE RowID = $RowID");
					if($ChkDetail != 0) {
						$UPDATE_DETAIL = "
							UPDATE tarsku_detail
							SET ";
								for($m = 1; $m <= 12; $m++) {
									if($_POST['TarM'.$m.'_1'] == "") {
										$TarM = "NULL";
									}else{
										$TarM = $_POST['TarM'.$m.'_1'];
									}
									if($m < 10) {
										$UPDATE_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
									}else{
										$UPDATE_DETAIL .= "Tar_M".$m." = ".$TarM.",";
									}
								}
								$UPDATE_DETAIL .= "
								UpdateUkey = '$CreateUkey',
								UpdateDate = NOW()
							WHERE RowID = $RowID AND ItemCode = '$ItemCode' AND TargetStatus = 'A'";
						MySQLUpdate($UPDATE_DETAIL);
					}else{
						$INSERT_DETAIL = "
						INSERT INTO tarsku_detail 
						SET CPEntry  = $CPEntry,
							RowID    = $RowID,
							ItemCode = '$ItemCode',
							SaleUkey = NULL,";
							for($m = 1; $m <= 12; $m++) {
								if($_POST['TarM'.$m.'_1'] == "") {
									$TarM = "NULL";
								}else{
									$TarM = $_POST['TarM'.$m.'_1'];
								}
								if($m < 10) {
									$INSERT_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
								}else{
									$INSERT_DETAIL .= "Tar_M".$m." = ".$TarM.",";
								}
							}
							$INSERT_DETAIL .= "CreateUkey = '$CreateUkey'"; 
						MySQLInsert($INSERT_DETAIL);
					}
				}else{
					for($r = 1; $r <= $Row; $r++) {
						$UkeySale = $_POST['ukey_'.$r];
						$ChkDetail = CHKRowDB("SELECT * FROM tarsku_detail WHERE RowID = $RowID AND SaleUkey = '$UkeySale'");
						if($ChkDetail != 0) {
							$UPDATE_DETAIL = "
								UPDATE tarsku_detail
								SET ";
									for($m = 1; $m <= 12; $m++) {
										if($_POST['TarM'.$m.'_'.$r] == "") {
											$TarM = "NULL";
										}else{
											$TarM = $_POST['TarM'.$m.'_'.$r];
										}
										if($m < 10) {
											$UPDATE_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
										}else{
											$UPDATE_DETAIL .= "Tar_M".$m." = ".$TarM.",";
										}
									}
									$UPDATE_DETAIL .= "
									UpdateUkey = '$CreateUkey',
									UpdateDate = NOW()
								WHERE RowID = $RowID AND ItemCode = '$ItemCode' AND TargetStatus = 'A' AND SaleUkey = '$UkeySale'";
							MySQLUpdate($UPDATE_DETAIL);
						}else{
							$INSERT_DETAIL = "
							INSERT INTO tarsku_detail 
							SET CPEntry  = $CPEntry,
								RowID    = $RowID,
								ItemCode = '$ItemCode',
								SaleUkey = '$UkeySale',";
								for($m = 1; $m <= 12; $m++) {
									if($_POST['TarM'.$m.'_'.$r] == "") {
										$TarM = "NULL";
									}else{
										$TarM = $_POST['TarM'.$m.'_'.$r];
									}
									if($m < 10) {
										$INSERT_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
									}else{
										$INSERT_DETAIL .= "Tar_M".$m." = ".$TarM.",";
									}
								}
								$INSERT_DETAIL .= "CreateUkey = '$CreateUkey'"; 
							MySQLInsert($INSERT_DETAIL);
						}
					}
				}
				$arrCol['SUCCESS'] = "Y";
			}else{
				$UPDATE_LIST = "UPDATE tarsku_itemlist SET RowStatus = 'I', UpdateUkey = '$CreateUkey', UpdateDate = NOW() WHERE RowID = $RowID AND DocNum = '$DocNum'";
				MySQLUpdate($UPDATE_LIST);

				$INSERT_LIST = "
					INSERT INTO tarsku_itemlist 
						(CPEntry, DocNum, VisOrder, ItemCode, ProductStatus, OpenStock, UnitMsr, Cxst, TargetTotal, CreateUkey) 
					SELECT 
						CPEntry, DocNum, VisOrder, ItemCode, ProductStatus, OpenStock, UnitMsr, Cxst, $TarCam, '$CreateUkey'
					FROM tarsku_itemlist WHERE RowID = $RowID AND DocNum = '$DocNum' AND RowStatus = 'I'
					ORDER BY CreateDate DESC";
				$GetID = MySQLInsert($INSERT_LIST);

				if($result1['MngType'] == 'T') {
					$ChkDetail = CHKRowDB("SELECT * FROM tarsku_detail WHERE RowID = $RowID AND TargetStatus = 'A'");
					if($ChkDetail == 0) {
						$INSERT_DETAIL = "
						INSERT INTO tarsku_detail 
						SET CPEntry  = $CPEntry,
							RowID    = $GetID,
							ItemCode = '$ItemCode',
							SaleUkey = NULL,";
							for($m = 1; $m <= 12; $m++) {
								if($_POST['TarM'.$m.'_1'] == "") {
									$TarM = "NULL";
								}else{
									$TarM = $_POST['TarM'.$m.'_1'];
								}
								if($m < 10) {
									$INSERT_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
								}else{
									$INSERT_DETAIL .= "Tar_M".$m." = ".$TarM.",";
								}
							}
							$INSERT_DETAIL .= "CreateUkey = '$CreateUkey'";
						MySQLInsert($INSERT_DETAIL);
					}else{
						$UPDATE_DETAIL = "UPDATE tarsku_detail SET TargetStatus = 'I', UpdateUkey = '$CreateUkey', UpdateDate = NOW() WHERE RowID = $RowID AND TargetStatus = 'A'";
						MySQLUpdate($UPDATE_DETAIL);

						$INSERT_DETAIL = "
							INSERT INTO tarsku_detail 
								(CPEntry, RowID, ItemCode, SaleUkey, Tar_M01, Tar_M02, Tar_M03, Tar_M04, Tar_M05, Tar_M06, Tar_M07, Tar_M08, Tar_M09, Tar_M10, Tar_M11, Tar_M12, CreateUkey) 
							SELECT 
								CPEntry, $GetID, ItemCode, SaleUkey, ";
								for($m = 1; $m <= 12; $m++) {
									if($m < 10) {
										if($_POST['TarM'.$m.'_1'] != "") {
											$INSERT_DETAIL .= $_POST['TarM'.$m.'_1'].", ";
										}else{
											$INSERT_DETAIL .= "Tar_M0".$m.", ";
										}
									}else{
										if($_POST['TarM'.$m.'_1'] != "") {
											$INSERT_DETAIL .= $_POST['TarM'.$m.'_1'].", ";
										}else{
											$INSERT_DETAIL .= "Tar_M".$m.", ";
										}
									}
								}
								$INSERT_DETAIL .= "
								'$CreateUkey'
							FROM tarsku_detail WHERE RowID = $RowID AND TargetStatus = 'I'
							ORDER BY CreateDate DESC";
						MySQLInsert($INSERT_DETAIL);
					}
				}else{
					for($r = 1; $r <= $Row; $r++) {
						$UkeySale = $_POST['ukey_'.$r];
						$ChkDetail = CHKRowDB("SELECT * FROM tarsku_detail WHERE RowID = $RowID AND SaleUkey = '$UkeySale' AND TargetStatus = 'A'");
						if($ChkDetail == 0) {
							$INSERT_DETAIL = "
							INSERT INTO tarsku_detail 
							SET CPEntry  = $CPEntry,
								RowID    = $GetID,
								ItemCode = '$ItemCode',
								SaleUkey = '$UkeySale',";
								for($m = 1; $m <= 12; $m++) {
									if($_POST['TarM'.$m.'_'.$r] == "") {
										$TarM = "NULL";
									}else{
										$TarM = $_POST['TarM'.$m.'_'.$r];
									}
									if($m < 10) {
										$INSERT_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
									}else{
										$INSERT_DETAIL .= "Tar_M".$m." = ".$TarM.",";
									}
								}
								$INSERT_DETAIL .= "CreateUkey = '$CreateUkey'";
							MySQLInsert($INSERT_DETAIL);
						}else{
							// $Chk = "
							// 	SELECT T0.Tar_M01 AS 'Tar_M1', T0.Tar_M02 AS 'Tar_M2', T0.Tar_M03 AS 'Tar_M3', T0.Tar_M04 AS 'Tar_M4', T0.Tar_M05 AS 'Tar_M5', T0.Tar_M06 AS 'Tar_M6', T0.Tar_M07 AS 'Tar_M7', T0.Tar_M08 AS 'Tar_M8', T0.Tar_M09 AS 'Tar_M9', T0.Tar_M10, T0.Tar_M11, T0.Tar_M12
							// 	FROM tarsku_detail T0 
							// 	WHERE T0.RowID = $RowID AND T0.SaleUkey = '$UkeySale' AND T0.TargetStatus = 'A'";
							// $RST_Chk = MySQLSelect($Chk);
							// $ChkUpdate = 0;
							// for($m = 1; $m <= 12; $m++) {
							// 	if($RST_Chk['Tar_M'.$m] != $_POST['TarM'.$m.'_'.$r]) {
							// 		$ChkUpdate++;
							// 	}
							// }
	
							// if($ChkUpdate != 0) {
								$UPDATE_DETAIL = "UPDATE tarsku_detail SET TargetStatus = 'I', UpdateUkey = '$CreateUkey', UpdateDate = NOW() WHERE RowID = $RowID AND SaleUkey = '$UkeySale' AND TargetStatus = 'A'";
								MySQLUpdate($UPDATE_DETAIL);
		
								$INSERT_DETAIL = "
									INSERT INTO tarsku_detail 
										(CPEntry, RowID, ItemCode, SaleUkey, Tar_M01, Tar_M02, Tar_M03, Tar_M04, Tar_M05, Tar_M06, Tar_M07, Tar_M08, Tar_M09, Tar_M10, Tar_M11, Tar_M12, CreateUkey) 
									SELECT 
										CPEntry, $GetID, ItemCode, SaleUkey, ";
										for($m = 1; $m <= 12; $m++) {
											if($m < 10) {
												if($_POST['TarM'.$m.'_'.$r] != "") {
													$INSERT_DETAIL .= $_POST['TarM'.$m.'_'.$r].", ";
												}else{
													$INSERT_DETAIL .= "Tar_M0".$m.", ";
												}
											}else{
												if($_POST['TarM'.$m.'_'.$r] != "") {
													$INSERT_DETAIL .= $_POST['TarM'.$m.'_'.$r].", ";
												}else{
													$INSERT_DETAIL .= "Tar_M".$m.", ";
												}
											}
										}
										$INSERT_DETAIL .= "
										'$CreateUkey'
									FROM tarsku_detail WHERE RowID = $RowID AND  SaleUkey = '$UkeySale' AND TargetStatus = 'I'
									ORDER BY CreateDate DESC";
								MySQLInsert($INSERT_DETAIL);
							// }
						}
					}
				}
				$arrCol['SUCCESS'] = "Y";
			}
		}else{
			/* ADD NEW VISORDER */
			$SQL1 = "SELECT IFNULL(MAX(T0.VisOrder),-1)+1 AS 'VisOrder' FROM tarsku_itemlist T0 WHERE T0.DocNum = '$DocNum' AND T0.RowStatus = 'A' LIMIT 1";
			$RST1 = MySQLSelect($SQL1);
			$VisOrder = $RST1['VisOrder'];

			$ItemName      = conutf8($RST2['ItemName']);
			$ItemCode      = $RST2['ItemCode'];
			$ProductStatus = $RST2['U_ProductStatus'];
			$Cxst          = $RST2['PriceAfVAT'];
			$OpenStock     = $RST2['OpenStock'];
			$UnitMsr       = conutf8($RST2['SalUnitMsr']);


			/* INSERT INTO tarsku_itemlist */
			$INSERT_LIST = "
				INSERT INTO tarsku_itemlist 
				SET CPEntry  = $CPEntry,
					DocNum   = '$DocNum',
					VisOrder = '$VisOrder',
					ItemCode = '$ItemCode',
					ProductStatus = '$ProductStatus',
					OpenStock = $OpenStock,
					UnitMsr  = '$UnitMsr',
					Cxst     = $Cxst,
					TargetTotal = $TarCam,
					CreateUkey = '$CreateUkey'";
			$RowID = MySQLInsert($INSERT_LIST);

			if($RowID != "") {
				/* INSERT INTO tarsku_detail */
				if($result1['MngType'] == 'T') {
					$INSERT_DETAIL = "
						INSERT INTO tarsku_detail 
						SET CPEntry  = $CPEntry,
							RowID    = $RowID,
							ItemCode = '$ItemCode',
							SaleUkey = NULL,";
							for($m = 1; $m <= 12; $m++) {
								if($_POST['TarM'.$m.'_1'] == "") {
									$TarM = "NULL";
								}else{
									$TarM = $_POST['TarM'.$m.'_1'];
								}
								if($m < 10) {
									$INSERT_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
								}else{
									$INSERT_DETAIL .= "Tar_M".$m." = ".$TarM.",";
								}
							}
							$INSERT_DETAIL .= "CreateUkey = '$CreateUkey'";
					MySQLInsert($INSERT_DETAIL);
				}else{
					for($r = 1; $r <= $Row; $r++) {
						$UkeySale = $_POST['ukey_'.$r];
						$INSERT_DETAIL = "
						INSERT INTO tarsku_detail 
						SET CPEntry  = $CPEntry,
							RowID    = $RowID,
							ItemCode = '$ItemCode',
							SaleUkey = '$UkeySale',";
							for($m = 1; $m <= 12; $m++) {
								if($_POST['TarM'.$m.'_'.$r] == "") {
									$TarM = "NULL";
								}else{
									$TarM = $_POST['TarM'.$m.'_'.$r];
								}
								if($m < 10) {
									$INSERT_DETAIL .= "Tar_M0".$m." = ".$TarM.",";
								}else{
									$INSERT_DETAIL .= "Tar_M".$m." = ".$TarM.",";
								}
							}
							$INSERT_DETAIL .= "CreateUkey = '$CreateUkey'";
						MySQLInsert($INSERT_DETAIL);
					}
				}
				$arrCol['SUCCESS'] = "Y";
			}else{
				$arrCol['SUCCESS'] = "N";
			}
		}
		$arrCol['ChkStock'] = "Y";
	}else{
		$arrCol['ChkStock'] = "N";
	}
}

if($_GET['p'] == 'ViewDoc') {
	$DocNum = $_POST['DocNum'];
	$DocStatus = $_POST['DocStatus'];

	$SQL0 = "SELECT T0.ItemCode, ISNULL(DATEDIFF(m,(CASE WHEN T0.LastPurDat = '2022-12-31' OR T0.LastPurDat IS NULL THEN T1.LastPurDat ELSE ISNULL(T0.LastPurDat, T1.LastPurDat) END), GETDATE()),9999) AS 'Aging' FROM OITM T0 LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode ORDER BY T0.ItemCode";
	$QRY0 = SAPSelect($SQL0);
	while($RST0 = odbc_fetch_array($QRY0)) {
		$Aging[$RST0['ItemCode']] = $RST0['Aging'];
	}

	$SQL1 = "
		SELECT T0.CPEntry, T0.DocNum, T0.CPTitle, T0.TeamCode, T0.MngType, T0.CPType, T0.StartDate, T0.EndDate, T0.CPDescription, T0.SaleUkey 
		FROM tarsku_header T0 
		WHERE T0.DocNum = '$DocNum' AND T0.DocStatus = '$DocStatus' AND T0.CANCELED = 'N' LIMIT 1";
	$RST1 = MySQLSelect($SQL1);
	
	switch ($RST1['TeamCode']) {
		case 'MT1': $TeamCode = "โมเดิร์นเทรด 1"; break;
		case 'MT2': $TeamCode = "โมเดิร์นเทรด 2"; break;
		case 'TT2': $TeamCode = "ต่างจังหวัด"; break;
		case 'OUL': $TeamCode = "หน้าร้าน + เขตกรุงเทพฯ"; break;
		case 'ONL': $TeamCode = "ออนไลน์"; break;
	}

	switch ($RST1['MngType']) {
		case 'T': $MngType = 'รายทีม'; break;
		case 'P': $MngType  = 'รายบุคคล'; break;
	}

	switch ($RST1['CPType']) {
		case 'Q': $CPType = 'สินค้าจอง (Quota)'; break;
		case 'F': $CPType = 'สินค้าต้องขาย (Focus)'; break;
		case 'P': $CPType = 'สินค้าโปรโมชั่น (Promotion)'; break;
		case '2': $CPType = 'สินค้ามือสอง (2nd Hand)'; break;
		case 'O': $CPType = 'อื่น ๆ'; break;
		case 'SD': $CPType = 'สถานะ D'; break;
		case 'SR': $CPType = 'สถานะ R'; break;
		case 'SAW': $CPType = 'สถานะ A / W'; break;
		case 'SM': $CPType = 'สถานะ M'; break;
		case 'SN': $CPType = 'สถานะ N'; break;
		case 'SP': $CPType = 'สถานะ P'; break;
		case 'SE': $CPType = 'สถานะ E'; break;
	}

	$DataHeader = "
		<tr>
			<th width='15%'>ชื่อเป้าขายสินค้า</th>
			<td width='25%'>".$RST1['CPTitle']."</td>
			<th width='10%'>วันที่เริ่มต้น</th>
			<td width='20%'>".date("d/m/Y",strtotime($RST1['StartDate']))."</td>
			<th width='15%'>วันที่สิ้นสุด</th>
			<td width='15%'>".date("d/m/Y",strtotime($RST1['EndDate']))."</td>
		</tr>
		<tr>
			<th>ทีมขาย</th>
			<td>".$TeamCode."</td>
			<th>รูปแบบวัดผล</th>
			<td>".$MngType."</td>
			<th>ประเภทเป้าขายสินค้า</th>
			<td>".$CPType."</td>
		</tr>
		<tr>
			<th class='pb-3'>รายละเอียดเป้าขายสินค้า</th>
			<td class='pb-3' colspan='5'>".$RST1['CPDescription']."</td>
		</tr>";

	$SQL = "
		SELECT T0.RowID, T0.ItemCode, T1.ItemName, T0.ProductStatus, T0.Cxst, T0.OpenStock, T0.TargetTotal, T2.DocStatus, T0.UnitMsr,
			T2.CPType, T2.StartDate, T2.EndDate, T2.TeamCode, T2.MngType, T2.SaleUkey
		FROM tarsku_itemlist T0 
		LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
		LEFT JOIN tarsku_header T2 ON T2.DocNum = T0.DocNum
		WHERE T0.DocNum = '$DocNum' AND T0.RowStatus = 'A' AND T2.CANCELED = 'N'
		ORDER BY (T0.TargetTotal * T0.Cxst) DESC";
	$QRY = MySQLSelectX($SQL);

	$DocY = date("Y",strtotime($RST1['StartDate']));

	if($_SESSION['DeptCode'] == "DP005" || $_SESSION['DeptCode'] == "DP008") {
		if($_SESSION['DeptCode'] == "DP008") {
			$SQL_Team = "T0.TeamCode IN ('OUL','TT101') AND T0.Ukey NOT IN ('a82726eeff10f11797ed9fde004e701a')";
		} else {
			$SQL_Team = "T0.TeamCode LIKE 'TT2%'";
		}
		$SQL_User = "SELECT DISTINCT COUNT(Ukey) AS 'Count' FROM saletarget T0 WHERE $SQL_Team AND T0.DocYear = $DocY AND T0.DocStatus = 'A'";
		$RST_User = MySQLSelect($SQL_User);

		$SaleQty = $RST_User['Count'];
	} else {
		$SaleQty = 1;
	}


	$Data = ""; $No = 0;
	while($result = mysqli_fetch_array($QRY)){
		$No++;
		if($result['CPType'] != "2") {
			$WhsCode = "'KSY','KSM','KB4','MT','MT2','TT-C','OUL','KBM','PLA'";
		} else {
			$WhsCode = "'KB5','KB5.1','KB6','KB6.1'";
		}

		if($result['MngType'] == "T") {
			if($_SESSION['uClass'] == 20 && ($_SESSION['DeptCode'] == "DP005" || $_SESSION['DeptCode'] == "DP008")) {
				$TeamSQL = "T2.Memo IN ('".$_SESSION['ukey']."')";
			} else {
				switch($result['TeamCode']) {
					case "OUL": $TeamSQL = "T2.U_Dim1 IN ('OUL','TT1')"; break;
					default:    $TeamSQL = "T2.U_Dim1 = '".$result['TeamCode']."'"; break;
				}
			}
			
		} else {
			$arrUkey = explode(",",$result['SaleUkey']);
			$i = 0;
			$count = count($arrUkey)-1;
			$Memo = "";
			foreach($arrUkey as $u) {
				$Memo .= "'$u'";
				if($i != $count) {
					$Memo .= ",";
				}
				$i++;
			}
			$TeamSQL = "T2.Memo IN ($Memo)";
		}

		$SQL_Add = "
			SELECT
				A0.ItemCode, SUM(A0.Quantity) AS 'Quantity'
			FROM (
				SELECT
				T0.ItemCode, SUM(T0.Quantity) AS 'Quantity'
				FROM INV1 T0
				LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
				WHERE (T1.CANCELED = 'N' AND T1.DocDate BETWEEN '".$result['StartDate']."' AND '".$result['EndDate']."' AND $TeamSQL) AND T0.ItemCode = '".$result['ItemCode']."'
				GROUP BY T0.ItemCode
				UNION ALL
				SELECT
				T0.ItemCode, -SUM(T0.Quantity) AS 'Quantity'
				FROM RIN1 T0
				LEFT JOIN ORIN T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
				WHERE (T1.CANCELED = 'N' AND T1.DocDate BETWEEN '".$result['StartDate']."' AND '".$result['EndDate']."' AND $TeamSQL) AND T0.ItemCode = '".$result['ItemCode']."'
				GROUP BY T0.ItemCode
			) A0
			WHERE A0.ItemCode IS NOT NULL
			GROUP BY A0.ItemCode
			ORDER BY A0.ItemCode";
			// echo $SQL_Add;
		$QRY_Add = SAPSelect($SQL_Add);
		$RST_Add = odbc_fetch_array($QRY_Add);

		$SQL_OnHand = "SELECT SUM(P0.OnHand) AS OnHand, SUM(P0.OnOrder) AS 'OnOrder' FROM OITW P0 WHERE P0.ItemCode = '".$result['ItemCode']."' AND P0.WhsCode IN ($WhsCode)";
		$QRY_OnHand = SAPSelect($SQL_OnHand);
		$RST_OnHand = odbc_fetch_array($QRY_OnHand);

		$Quantity = 0;
		$OnHand   = 0;
		$QPO = 0;
		if(isset($RST_Add['Quantity'])) {
			if($RST_Add['Quantity'] != 0) {
				if($RST_Add['Quantity'] >= $result['TargetTotal']) {
					$Quantity = $result['TargetTotal'];
				} else {
					$Quantity = $RST_Add['Quantity'];
				}
				if($_SESSION['uClass'] == 20 && ($_SESSION['DeptCode'] == "DP005" || $_SESSION['DeptCode'] == "DP006")) {
					$QPO = ($Quantity/(ceil($result['TargetTotal']/$SaleQty)))*100;
				} else {
					$QPO = ($Quantity/$result['TargetTotal'])*100;
				}
				
			}
		}
		$OnHand = $RST_OnHand['OnHand'];
		$ColorOnHand = "";
		if($OnHand == 0) {
			$ColorOnHand = "table-active";
		}

		if($Quantity != 0) {
			$TxtDetail = "<a href='javascript:void(0);' onclick='ViewDetail(\"".$result['StartDate']."\",\"".$result['EndDate']."\",\"".$result['TeamCode']."\",\"".$result['ItemCode']."\",\"".$DocNum."\",".$result['RowID'].",\"".$result['MngType']."\",\"".$RST1['SaleUkey']."\");'><i class='fas fa-search-plus fa-fw'></i></a>";
		} else {
			$TxtDetail = "";
		}
		
		$Color = "";
		if($QPO >= 100) {
			$Color = "table-success text-success";
		}
		if($_SESSION['uClass'] == 20 && ($_SESSION['DeptCode'] == "DP005" || $_SESSION['DeptCode'] == "DP008")) {
			$QpU = number_format(ceil($result['TargetTotal']/$SaleQty),0);
		} else {
			$QpU = "-";
		}
		$Data .= "
			<tr class='$Color $ColorOnHand'>
				<td class='text-center'>".$No."</td>
				<td class='text-center'><a href='?p=item_masterdata&Sku=".$result['ItemCode']."' target='_blank' class='text-primary'>".$result['ItemCode']."</a></td>
				<td>".$result['ItemName']."</td>
				<td class='text-center'>".$result['ProductStatus']."</td>
				<td class='text-center'>".$Aging[$result['ItemCode']]."</td>
				<td class='text-right'>".number_format($result['OpenStock'],0)."</td>
				<td>".$result['UnitMsr']."</td>
				<td class='text-right fw-bolder'>".number_format($result['TargetTotal'],0)."</td>
				<td class='text-right fw-bolder'>$QpU</td>
				<td class='text-right fw-bolder text-success'>".number_format($Quantity,0)."</td>
				<td class='text-right'>".number_format($QPO,2)."%</td>
				<td class='text-right text-info'>".number_format($OnHand,0)."</td>
				<td class='text-right'>".number_format($RST_OnHand['OnOrder'],0)."</td>
				<td class='text-center'>$TxtDetail</td>
			</tr>"; 
	}
	if($No == 0) {
		$Data .= "<tr><td colspan='11' class='text-center'>ไม่มีข้อมูล :)</td></tr>";
	}
	$arrCol['DataHeader'] = $DataHeader;
	$arrCol['Data'] = $Data;
}

if($_GET['p'] == 'ViewDetail') {
	$StartDate = $_POST['StartDate'];
	$EndDate   = $_POST['EndDate'];
	$TeamCode  = $_POST['TeamCode'];
	$ItemCode  = $_POST['ItemCode'];
	$DocNum    = $_POST['DocNum'];
	$RowID     = $_POST['RowID'];
	$MngType   = $_POST['MngType'];

	$ArrSaleUkey = explode(",",$_POST['SaleUkey']);
	$SaleUkey = "";
	for($u = 0; $u <= count($ArrSaleUkey)-1; $u++) {
		$SaleUkey .= "'".$ArrSaleUkey[$u]."'";
		if($u != count($ArrSaleUkey)-1) {
			$SaleUkey .= ",";
		}
	}

	$SQL1 = "
		SELECT T0.ItemCode, T1.ItemName, T0.TargetTotal, T1.ProductStatus
		FROM tarsku_itemlist T0
		LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
		WHERE T0.DocNum = '$DocNum' AND T0.RowID = $RowID AND T0.RowStatus = 'A'";
	$RST1 = MySQLSelect($SQL1);
	$arrCol['ItemName'] = $RST1['ItemCode']." | ".$RST1['ItemName']." [".$RST1['ProductStatus']."]";
	$arrCol['Target']   = $RST1['TargetTotal'];

	$Data = "";
	if($MngType == 'T') {
		switch ($TeamCode) {
			case 'MT1': $TeamName = "โมเดิร์นเทรด 1"; break;
			case 'MT2': $TeamName = "โมเดิร์นเทรด 2"; break;
			case 'TT2': $TeamName = "ต่างจังหวัด"; break;
			case 'OUL': $TeamName = "หน้าร้าน + เขตกรุงเทพฯ"; break;
			case 'ONL': $TeamName = "ออนไลน์"; break;
		}
		switch($TeamCode) {
			case "OUL": $TeamSQL = "T2.U_Dim1 IN ('OUL','TT1')"; break;
			default:    $TeamSQL = "T2.U_Dim1 = '$TeamCode'"; break;
		}

		$SQL2 = "
			SELECT
				A0.ItemCode,
				SUM(A0.M_01) AS 'M_1', SUM(A0.M_02) AS 'M_2', SUM(A0.M_03) AS 'M_3',
				SUM(A0.M_04) AS 'M_4', SUM(A0.M_05) AS 'M_5', SUM(A0.M_06) AS 'M_6',
				SUM(A0.M_07) AS 'M_7', SUM(A0.M_08) AS 'M_8', SUM(A0.M_09) AS 'M_9',
				SUM(A0.M_10) AS 'M_10', SUM(A0.M_11) AS 'M_11', SUM(A0.M_12) AS 'M_12'
			FROM (
				SELECT
				T0.ItemCode,
				CASE WHEN MONTH(T1.Docdate) = 1 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_01',
				CASE WHEN MONTH(T1.Docdate) = 2 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_02',
				CASE WHEN MONTH(T1.Docdate) = 3 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_03',
				CASE WHEN MONTH(T1.Docdate) = 4 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_04',
				CASE WHEN MONTH(T1.Docdate) = 5 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_05',
				CASE WHEN MONTH(T1.Docdate) = 6 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_06',
				CASE WHEN MONTH(T1.Docdate) = 7 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_07',
				CASE WHEN MONTH(T1.Docdate) = 8 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_08',
				CASE WHEN MONTH(T1.Docdate) = 9 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_09',
				CASE WHEN MONTH(T1.Docdate) = 10 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_10',
				CASE WHEN MONTH(T1.Docdate) = 11 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_11',
				CASE WHEN MONTH(T1.Docdate) = 12 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_12'
				FROM INV1 T0
				LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
				WHERE (T1.CANCELED = 'N' AND T1.DocDate BETWEEN '$StartDate' AND '$EndDate' AND $TeamSQL) AND T0.ItemCode = '$ItemCode'
				GROUP BY T0.ItemCode, T1.DocDate
				UNION ALL 
				SELECT
				T0.ItemCode,
				CASE WHEN MONTH(T1.Docdate) = 1 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_01',
				CASE WHEN MONTH(T1.Docdate) = 2 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_02',
				CASE WHEN MONTH(T1.Docdate) = 3 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_03',
				CASE WHEN MONTH(T1.Docdate) = 4 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_04',
				CASE WHEN MONTH(T1.Docdate) = 5 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_05',
				CASE WHEN MONTH(T1.Docdate) = 6 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_06',
				CASE WHEN MONTH(T1.Docdate) = 7 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_07',
				CASE WHEN MONTH(T1.Docdate) = 8 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_08',
				CASE WHEN MONTH(T1.Docdate) = 9 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_09',
				CASE WHEN MONTH(T1.Docdate) = 10 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_10',
				CASE WHEN MONTH(T1.Docdate) = 11 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_11',
				CASE WHEN MONTH(T1.Docdate) = 12 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_12'
				FROM RIN1 T0
				LEFT JOIN ORIN T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
				WHERE (T1.CANCELED = 'N' AND T1.DocDate BETWEEN '$StartDate' AND '$EndDate' AND $TeamSQL) AND T0.ItemCode = '$ItemCode'
				GROUP BY T0.ItemCode, T1.DocDate
			) A0
			GROUP BY A0.ItemCode";
		$QRY2 = SAPSelect($SQL2);
		$RST2 = odbc_fetch_array($QRY2);

		$SQL3 = "
			SELECT 
				SUM(IFNULL(T0.Tar_M01,0)+IFNULL(T0.Tar_M02,0)+IFNULL(T0.Tar_M03,0)+IFNULL(T0.Tar_M04,0)+IFNULL(T0.Tar_M05,0)+IFNULL(T0.Tar_M06,0)+IFNULL(T0.Tar_M07,0)+IFNULL(T0.Tar_M08,0)+IFNULL(T0.Tar_M09,0)+IFNULL(T0.Tar_M10,0)+IFNULL(T0.Tar_M11,0)+IFNULL(T0.Tar_M12,0)) AS TarTotal
			FROM tarsku_detail T0
			WHERE T0.RowID = $RowID AND T0.ItemCode = '$ItemCode'";
		$RST3 = MySQLSelect($SQL3);
		$Data .="<tr>
					<td>".$TeamName."</td>";
					$sTotal = 0;
					for($m = 1; $m <= 12; $m++) {
						if($m >= intval(date("m",strtotime($StartDate))) && $m <= intval(date("m",strtotime($EndDate)))) {
							$Data .="<td class='text-right'>".number_format($RST2['M_'.$m],0)."</td>";
							$sTotal = $sTotal+$RST2['M_'.$m];
						}else{
							$Data .="<td class='text-right table-active'></td>";
						}
					}

					if($RST1['TargetTotal'] > 0) {
						$PoS = ($sTotal/$RST1['TargetTotal']) * 100;
					} else {
						$PoS = 0;
					}
					$Data .="<td class='text-right fw-bolder'>".number_format($RST1['TargetTotal'],0)."</td>";
					$Data .="<td class='text-right fw-bolder text-success'>".number_format($sTotal,0)."</td>";
					$Data .="<td class='text-right'>".number_format($PoS,2)."%</td>";
		$Data.="</tr>";

		if($TeamCode == "OUL" || $TeamCode == "TT2") {
			$SQL4 = "
				SELECT
					A0.ItemCode, A0.Memo,
					SUM(A0.M_01) AS 'M_1', SUM(A0.M_02) AS 'M_2', SUM(A0.M_03) AS 'M_3',
					SUM(A0.M_04) AS 'M_4', SUM(A0.M_05) AS 'M_5', SUM(A0.M_06) AS 'M_6',
					SUM(A0.M_07) AS 'M_7', SUM(A0.M_08) AS 'M_8', SUM(A0.M_09) AS 'M_9',
					SUM(A0.M_10) AS 'M_10', SUM(A0.M_11) AS 'M_11', SUM(A0.M_12) AS 'M_12'
				FROM (
					SELECT
					T0.ItemCode, T2.Memo,
					CASE WHEN MONTH(T1.Docdate) = 1 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_01',
					CASE WHEN MONTH(T1.Docdate) = 2 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_02',
					CASE WHEN MONTH(T1.Docdate) = 3 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_03',
					CASE WHEN MONTH(T1.Docdate) = 4 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_04',
					CASE WHEN MONTH(T1.Docdate) = 5 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_05',
					CASE WHEN MONTH(T1.Docdate) = 6 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_06',
					CASE WHEN MONTH(T1.Docdate) = 7 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_07',
					CASE WHEN MONTH(T1.Docdate) = 8 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_08',
					CASE WHEN MONTH(T1.Docdate) = 9 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_09',
					CASE WHEN MONTH(T1.Docdate) = 10 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_10',
					CASE WHEN MONTH(T1.Docdate) = 11 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_11',
					CASE WHEN MONTH(T1.Docdate) = 12 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_12'
					FROM INV1 T0
					LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
					LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
					WHERE (T1.CANCELED = 'N' AND T1.DocDate BETWEEN '$StartDate' AND '$EndDate' AND $TeamSQL) AND T0.ItemCode = '$ItemCode'
					GROUP BY T0.ItemCode, T1.DocDate, T2.Memo
					UNION ALL 
					SELECT
					T0.ItemCode, T2.Memo,
					CASE WHEN MONTH(T1.Docdate) = 1 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_01',
					CASE WHEN MONTH(T1.Docdate) = 2 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_02',
					CASE WHEN MONTH(T1.Docdate) = 3 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_03',
					CASE WHEN MONTH(T1.Docdate) = 4 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_04',
					CASE WHEN MONTH(T1.Docdate) = 5 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_05',
					CASE WHEN MONTH(T1.Docdate) = 6 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_06',
					CASE WHEN MONTH(T1.Docdate) = 7 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_07',
					CASE WHEN MONTH(T1.Docdate) = 8 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_08',
					CASE WHEN MONTH(T1.Docdate) = 9 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_09',
					CASE WHEN MONTH(T1.Docdate) = 10 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_10',
					CASE WHEN MONTH(T1.Docdate) = 11 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_11',
					CASE WHEN MONTH(T1.Docdate) = 12 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_12'
					FROM RIN1 T0
					LEFT JOIN ORIN T1 ON T0.DocEntry = T1.DocEntry
					LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
					WHERE (T1.CANCELED = 'N' AND T1.DocDate BETWEEN '$StartDate' AND '$EndDate' AND $TeamSQL) AND T0.ItemCode = '$ItemCode'
					GROUP BY T0.ItemCode, T1.DocDate, T2.Memo
				) A0
				GROUP BY A0.ItemCode, A0.Memo";
			// echo $SQL4;
			$QRY4 = SAPSelect($SQL4);
			while($RST4 = odbc_fetch_array($QRY4)) {
				$SQL5 = "SELECT CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'SaleName' FROM users T0 WHERE T0.ukey = '".$RST4['Memo']."'";
				$RST5 = MySQLSelect($SQL5);
				if($_SESSION['ukey'] == $RST4['Memo']) {
					$Data .= "<tr class='table-warning'>";
				} else {
					$Data .= "<tr>";
				}
				$Data .="
					<td>".$RST5['SaleName']."</td>";
							$sTotal = 0;
							for($m = 1; $m <= 12; $m++) {
								if($m >= intval(date("m",strtotime($StartDate))) && $m <= intval(date("m",strtotime($EndDate)))) {
									$Data .="<td class='text-right'>".number_format($RST4['M_'.$m],0)."</td>";
									$sTotal = $sTotal+$RST4['M_'.$m];
								}else{
									$Data .="<td class='text-right table-active'></td>";
								}
							}
							$Data .="<td class='text-right fw-bolder'>&nbsp;</td>";
							$Data .="<td class='text-right fw-bolder text-success'>".number_format($sTotal,0)."</td>";
							$Data .="<td class='text-right'>&nbsp;</td>";
				$Data.="</tr>";
			}
		}

	}else{
		$TeamSQL = "T2.Memo IN ($SaleUkey)";
		$SQL2 = "
			SELECT
				A0.ItemCode, A0.Memo,
				SUM(A0.M_01) AS 'M_1', SUM(A0.M_02) AS 'M_2', SUM(A0.M_03) AS 'M_3',
				SUM(A0.M_04) AS 'M_4', SUM(A0.M_05) AS 'M_5', SUM(A0.M_06) AS 'M_6',
				SUM(A0.M_07) AS 'M_7', SUM(A0.M_08) AS 'M_8', SUM(A0.M_09) AS 'M_9',
				SUM(A0.M_10) AS 'M_10', SUM(A0.M_11) AS 'M_11', SUM(A0.M_12) AS 'M_12'
			FROM (
				SELECT
				T0.ItemCode, T2.Memo,
				CASE WHEN MONTH(T1.Docdate) = 1 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_01',
				CASE WHEN MONTH(T1.Docdate) = 2 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_02',
				CASE WHEN MONTH(T1.Docdate) = 3 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_03',
				CASE WHEN MONTH(T1.Docdate) = 4 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_04',
				CASE WHEN MONTH(T1.Docdate) = 5 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_05',
				CASE WHEN MONTH(T1.Docdate) = 6 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_06',
				CASE WHEN MONTH(T1.Docdate) = 7 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_07',
				CASE WHEN MONTH(T1.Docdate) = 8 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_08',
				CASE WHEN MONTH(T1.Docdate) = 9 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_09',
				CASE WHEN MONTH(T1.Docdate) = 10 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_10',
				CASE WHEN MONTH(T1.Docdate) = 11 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_11',
				CASE WHEN MONTH(T1.Docdate) = 12 THEN SUM(T0.Quantity) ELSE 0 END AS 'M_12'
				FROM INV1 T0
				LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
				WHERE (T1.CANCELED = 'N' AND T1.DocDate BETWEEN '$StartDate' AND '$EndDate' AND $TeamSQL) AND T0.ItemCode = '$ItemCode' AND T2.Memo IN ($SaleUkey)
				GROUP BY T0.ItemCode, T1.DocDate, T2.Memo
				UNION ALL 
				SELECT
				T0.ItemCode, T2.Memo,
				CASE WHEN MONTH(T1.Docdate) = 1 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_01',
				CASE WHEN MONTH(T1.Docdate) = 2 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_02',
				CASE WHEN MONTH(T1.Docdate) = 3 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_03',
				CASE WHEN MONTH(T1.Docdate) = 4 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_04',
				CASE WHEN MONTH(T1.Docdate) = 5 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_05',
				CASE WHEN MONTH(T1.Docdate) = 6 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_06',
				CASE WHEN MONTH(T1.Docdate) = 7 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_07',
				CASE WHEN MONTH(T1.Docdate) = 8 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_08',
				CASE WHEN MONTH(T1.Docdate) = 9 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_09',
				CASE WHEN MONTH(T1.Docdate) = 10 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_10',
				CASE WHEN MONTH(T1.Docdate) = 11 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_11',
				CASE WHEN MONTH(T1.Docdate) = 12 THEN -SUM(T0.Quantity) ELSE 0 END AS 'M_12'
				FROM RIN1 T0
				LEFT JOIN ORIN T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN OSLP T2 ON T1.SlpCode  = T2.SlpCode
				WHERE (T1.CANCELED = 'N' AND T1.DocDate BETWEEN '$StartDate' AND '$EndDate' AND $TeamSQL) AND T0.ItemCode = '$ItemCode' AND T2.Memo IN ($SaleUkey)
				GROUP BY T0.ItemCode, T1.DocDate, T2.Memo
			) A0
			GROUP BY A0.ItemCode, A0.Memo";
		$QRY2 = SAPSelect($SQL2); $GetSlp = array();
		while($RST2 = odbc_fetch_array($QRY2)) {
			array_push($GetSlp,$RST2['Memo']);

			$SRT_NAME = MySQLSelect("SELECT CONCAT(uName, ' ',uLastName, ' (', uNickName, ')') AS SaleName FROM users WHERE uKey = '".$RST2['Memo']."' LIMIT 1");
			$SQL3 = "
				SELECT 
					SUM(IFNULL(T0.Tar_M01,0)+IFNULL(T0.Tar_M02,0)+IFNULL(T0.Tar_M03,0)+IFNULL(T0.Tar_M04,0)+IFNULL(T0.Tar_M05,0)+IFNULL(T0.Tar_M06,0)+IFNULL(T0.Tar_M07,0)+IFNULL(T0.Tar_M08,0)+IFNULL(T0.Tar_M09,0)+IFNULL(T0.Tar_M10,0)+IFNULL(T0.Tar_M11,0)+IFNULL(T0.Tar_M12,0)) AS TarTotal
				FROM tarsku_detail T0
				WHERE T0.RowID = $RowID AND T0.ItemCode = '$ItemCode' AND T0.SaleUkey = '".$RST2['Memo']."'";
			$RST3 = MySQLSelect($SQL3);
			$Data .="<tr>
						<td>".$SRT_NAME['SaleName']."</td>";
						
						$sTotal = 0;
						for($m = 1; $m <= 12; $m++) {
							if($m >= intval(date("m",strtotime($StartDate))) && $m <= intval(date("m",strtotime($EndDate)))) {
								$Data .="<td class='text-right'>".number_format($RST2['M_'.$m],0)."</td>";
								$sTotal = $sTotal+$RST2['M_'.$m];
							}else{
								$Data .="<td class='text-right table-active'></td>";
							}
						}
						$Data .="<td class='text-right fw-bolder'>".number_format($RST3['TarTotal'],0)."</td>";
						$Data .="<td class='text-right fw-bolder text-success'>".number_format($sTotal,0)."</td>";
						$Data .="<td class='text-right'>".number_format(($sTotal/$RST3['TarTotal'])*100,2)."%</td>";
			$Data.="</tr>";
		}
		
		for($i = 0; $i < count($ArrSaleUkey); $i++) {
			if(in_array($ArrSaleUkey[$i],$GetSlp) == null) {
				$SRT_NAME = MySQLSelect("SELECT CONCAT(uName, ' ',uLastName, ' (', uNickName, ')') AS SaleName FROM users WHERE uKey = '".$ArrSaleUkey[$i]."' LIMIT 1");
				$SQL4 = "
					SELECT 
						SUM(IFNULL(T0.Tar_M01,0)+IFNULL(T0.Tar_M02,0)+IFNULL(T0.Tar_M03,0)+IFNULL(T0.Tar_M04,0)+IFNULL(T0.Tar_M05,0)+IFNULL(T0.Tar_M06,0)+IFNULL(T0.Tar_M07,0)+IFNULL(T0.Tar_M08,0)+IFNULL(T0.Tar_M09,0)+IFNULL(T0.Tar_M10,0)+IFNULL(T0.Tar_M11,0)+IFNULL(T0.Tar_M12,0)) AS TarTotal
					FROM tarsku_detail T0
					WHERE T0.RowID = $RowID AND T0.ItemCode = '$ItemCode' AND T0.SaleUkey = '".$ArrSaleUkey[$i]."'";
				$RST4 = MySQLSelect($SQL4);
				$Data .="<tr>
							<td>".$SRT_NAME['SaleName']."</td>";
							
							for($m = 1; $m <= 12; $m++) {
								if($m >= intval(date("m",strtotime($StartDate))) && $m <= intval(date("m",strtotime($EndDate)))) {
									$Data .="<td class='text-right'>0</td>";
								}else{
									$Data .="<td class='text-right table-active'></td>";
								}
							}
							$Data .="<td class='text-right fw-bolder'>".number_format($RST4['TarTotal'],0)."</td>";
							$Data .="<td class='text-right fw-bolder text-success'>0</td>";
							$Data .="<td class='text-right'>0.00%</td>";
				$Data.="</tr>";
			}
		}
	}

	$arrCol['Data'] = $Data;
}

if($_GET['p'] == "TarSummary") {
	$DocYear = $_POST['y'];
	$CPType  = $_POST['t'];

	$TBODY = "";

	$SQL0 = "SELECT T0.ItemCode, ISNULL(DATEDIFF(m,(CASE WHEN T0.LastPurDat = '2022-12-31' OR T0.LastPurDat IS NULL THEN T1.LastPurDat ELSE ISNULL(T0.LastPurDat, T1.LastPurDat) END), GETDATE()),9999) AS 'Aging' FROM OITM T0 LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode ORDER BY T0.ItemCode";
	$QRY0 = SAPSelect($SQL0);
	while($RST0 = odbc_fetch_array($QRY0)) {
		$Aging[$RST0['ItemCode']]  = $RST0['Aging'];
		$Actual[$RST0['ItemCode']] = 0;
	}

	switch($_SESSION['DeptCode']) {
		case "DP005": $TeamArray = array("TT2"); break;  
		case "DP006": $TeamArray = array("MT1"); break;  
		case "DP007": $TeamArray = array("MT2"); break;  
		case "DP008": $TeamArray = array("OUL"); break;  
		default:      $TeamArray = array("MT1","MT2","TT2","OUL","ONL"); break;
	}
	switch($CPType) {
		case "Q":
			$TextArray = array("","ยกมา ณ ต้นเดือน","ตั้งเป้าเพิ่มเติม","ต้นทุนออก (ขาย)","ต้นทุนออก (แถม)","ต้นทุนออก (ทั้งหมด)","คงเหลือ ณ สิ้นเดือน","คงเหลือ (Aging 0 - 3 เดือน)","คงเหลือ (Aging 4 - 6 เดือน)","คงเหลือ (Aging 7 - 12 เดือน)","คงเหลือ (Aging มากกว่า 12 เดือน)","T/O (เดือน)","ต้นทุนเข้าสะสม (เป้าหมาย)","ต้นทุนออกสะสม","% ความสำเร็จ (&#8805; 70% ของเป้า)");
			break;
		case "F":
		case "SD":
		case "SR":
		case "SAW":
		case "SM":
		case "SN":
		case "SP":
		case "SE":
			$TextArray = array("","ยกมา ณ ต้นเดือน","ตั้งเป้าเพิ่มเติม","ต้นทุนออก (ขาย)","ต้นทุนออก (แถม)","ต้นทุนออก (ทั้งหมด)","คงเหลือ ณ สิ้นเดือน","คงเหลือ (Aging 0 - 3 เดือน)","คงเหลือ (Aging 4 - 6 เดือน)","คงเหลือ (Aging 7 - 12 เดือน)","คงเหลือ (Aging มากกว่า 12 เดือน)","T/O (เดือน)","ต้นทุนเข้าสะสม (เป้าหมาย)","ต้นทุนออกสะสม","% ความสำเร็จ (&#8805; 70% ของเป้าไตรมาส)");
			break;
		case "P":
		case "2":
		case "O":
			$TextArray = array("","ยกมา ณ ต้นเดือน","ตั้งเป้าเพิ่มเติม","ต้นทุนออก (ขาย)","ต้นทุนออก (แถม)","ต้นทุนออก (ทั้งหมด)","คงเหลือ ณ สิ้นเดือน","คงเหลือ (Aging 0 - 3 เดือน)","คงเหลือ (Aging 4 - 6 เดือน)","คงเหลือ (Aging 7 - 12 เดือน)","คงเหลือ (Aging มากกว่า 12 เดือน)","T/O (เดือน)","ต้นทุนเข้าสะสม (เป้าหมาย)","ต้นทุนออกสะสม","% ความสำเร็จ");
			break;
	}
	

	for($i = 0; $i < count($TeamArray); $i++) {
		$Data[$TeamArray[$i]]['R0'] = $TeamArray[$i];
		for($m = 1; $m <= 12; $m++) {
			
			$Data[$TeamArray[$i]]['R1'][$m] = 0; // ยกมา
			$Data[$TeamArray[$i]]['R2'][$m] = 0; // ตั้งเป้าเพิ่มเติม
			$Data[$TeamArray[$i]]['R3'][$m] = 0; // ต้นทุนออก (ขาย)
			$Data[$TeamArray[$i]]['R4'][$m] = 0; // ต้นทุนออก (แถม)
			$Data[$TeamArray[$i]]['R5'][$m] = 0; // ต้นทุนออก (ทั้งหมด)
			$Data[$TeamArray[$i]]['R6'][$m] = 0; // คงเหลือ
			$Data[$TeamArray[$i]]['R7'][$m] = 0; // คงเหลือ
			$Data[$TeamArray[$i]]['R8'][$m] = 0; // คงเหลือ
			$Data[$TeamArray[$i]]['R9'][$m] = 0; // คงเหลือ
			$Data[$TeamArray[$i]]['R10'][$m] = 0; // คงเหลือ
			$Data[$TeamArray[$i]]['R11'][$m] = 0; // T/O (7)
			$Data[$TeamArray[$i]]['R12'][$m] = 0; // $SumIn (8)
			$Data[$TeamArray[$i]]['R13'][$m] = 0; // $SumOut (9)
			$Data[$TeamArray[$i]]['R14'][$m] = 0; // % of Success (10)
			
		}
	}

	/* ROW 2 ADD TARGET by MONTH */
	$SQL1 = 
		"SELECT
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
				WHERE YEAR(T0.StartDate) = $DocYear AND T0.CPType = '$CPType' AND T0.CANCELED = 'N' AND T0.DocStatus = 'C'
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
			$Data[$RST1['TeamCode']]['R2'][$m] = $Input;
		}
	}

	/* ROW 1 3 4 5 6 11 14 COST OF GOOD IN ITEM */
	for($i = 0; $i < count($TeamArray); $i++) {
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

		foreach($Actual as $key => $ItemCode) {
			$Actual[$key] = 0;
		}

		for($m = 1; $m <= 12; $m++) {
			if(($CPType == "F" || $CPType == "SD" || $CPType == "SR" || $CPType == "SAW" || $CPType == "SM" || $CPType == "SN" || $CPType == "SP" || $CPType == "SE") && ( ($DocYear == 2023 && ($m == 1 || $m == 4 || $m == 6 || $m == 10)) || ($DocYear > 2023 && ($m == 1 || $m == 4 || $m == 7 || $m == 10)))) {
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

				$SumIn  = 0;
				$SumOut = 0;

				foreach($Actual as $key => $ItemCode) {
					$Actual[$key] = 0;
				}
			}
			$SQL2 =
				"SELECT DISTINCT T1.ItemCode, T1.Cxst, T1.TargetTotal
				FROM tarsku_header T0
				LEFT JOIN tarsku_itemlist T1 ON T0.CPEntry = T1.CPEntry
				WHERE (T0.TeamCode = '".$Data[$TeamArray[$i]]['R0']."' AND T0.CPType = '$CPType' AND T0.CANCELED = 'N' AND T0.DocStatus = 'C') AND (YEAR(T0.StartDate) = $DocYear AND (MONTH(T0.StartDate) <= $m AND MONTH(T0.EndDate) >= $m))";
			$ROW2 = ChkRowDB($SQL2);
			/* ROW 3 4 5 10 */
			if($ROW2 > 0) {

				$ItemSet = array();
				$QRY2 = MySQLSelectX($SQL2);
				$ItemSQL = "";
				$r = 1;
				while($RST2 = mysqli_fetch_array($QRY2)) {
					$ItemSet[$RST2['ItemCode']]['Code']      = $RST2['ItemCode'];
					$ItemSet[$RST2['ItemCode']]['Cost']      = $RST2['Cxst'];
					$ItemSet[$RST2['ItemCode']]['Target']    = $RST2['TargetTotal'];
					$ItemSet[$RST2['ItemCode']]['Aging']     = $Aging[$RST2['ItemCode']];
					// array_push($ItemSet[$RST2['ItemCode']],$RST2['ItemCode']);
					// array_push($ItemSet[$RST2['ItemCode']],,$RST2['Cxst']);
					$ItemSQL .= "'".$RST2['ItemCode']."'";

					${"SUM_".$RST2['ItemCode']} = 0;  
					if($r != $ROW2) { $ItemSQL .= ", "; }
					$r++;
				}
				if($Data[$TeamArray[$i]]['R0'] == "OUL") {
					$TeamSQL = "T2.U_Dim1 IN ('TT1','OUL')";
				} else {
					$TeamSQL = "T2.U_Dim1 IN ('".$Data[$TeamArray[$i]]['R0']."')";
				}



				$SQL2_5 =
					"SELECT
						T0.TeamCode, T0.CPType, MONTH(T0.StartDate) AS 'DocMonth', T1.ItemCode, SUM(T1.TargetTotal * T1.Cxst) AS 'Cxst'
					FROM tarsku_header T0
					LEFT JOIN tarsku_itemlist T1 ON T0.CPEntry = T1.CPEntry
					WHERE (T0.TeamCode = '".$Data[$TeamArray[$i]]['R0']."' AND T0.CPType = '$CPType' AND T0.CANCELED = 'N' AND T0.DocStatus = 'C') AND (YEAR(T0.StartDate) = $DocYear AND (MONTH(T0.StartDate) <= $m AND MONTH(T0.EndDate) >= $m))
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
				$ROW3 = ChkRowSAP($SQL3);
				
				if($ROW3 > 0) {
					$QRY3 = SAPSelect($SQL3);
					$FreeCost = 0;
					$SaleCost = 0;
					$LineTotal = 0;
					while($RST3 = odbc_fetch_array($QRY3)) {

						// echo "Team: ".$Data[$TeamArray[$i]]['R0']." | ItemCode: ".$RST3['ItemCode']." | Month: ".$m." | Aging: ".$ItemSet[$RST3['ItemCode']]['Aging']." | Target: ".$ItemSet[$RST3['ItemCode']]['Target']." | Old Actual: ".$Actual[$RST3['ItemCode']]." | ";

						if($Actual[$RST3['ItemCode']] < $ItemSet[$RST3['ItemCode']]['Target']) {
							$NewActual = $Actual[$RST3['ItemCode']] + $RST3['Quantity'];
							if($NewActual < $ItemSet[$RST3['ItemCode']]['Target']) {
								$Quantity = $RST3['Quantity'];
							} else {
								if($Actual[$RST3['ItemCode']] == 0) {
									$Quantity = $ItemSet[$RST3['ItemCode']]['Target'];
								} else{
									$Quantity = $NewActual - $ItemSet[$RST3['ItemCode']]['Target'];
								}
							}
						} else {
							$Quantity = 0;
						}

						$Actual[$RST3['ItemCode']] = $Actual[$RST3['ItemCode']] + $Quantity;

						// echo "IV Qty: ".$RST3['Quantity']." | New Actual: $NewActual | New Qty: $Quantity | New Actual: ".$Actual[$RST3['ItemCode']]."<br/>";

						// if($RST3['ItemCode'] == "02-065-010") {
						// }
						


						// if($RST3['Quantity'] >= $ItemSet[$RST3['ItemCode']]['Target']) {
						// 	$Quantity = $ItemSet[$RST3['ItemCode']]['Target'];
						// } else {
						// 	$Quantity = $RST3['Quantity'];
						// }

						// $Quantity = $RST3['Quantity'];

						if($RST3['SaleType'] == "FREE") {
							$FreeCost = $FreeCost + ($Quantity * $ItemSet[$RST3['ItemCode']]['Cost']);
						} else {
							$SaleCost = $SaleCost + ($Quantity * $ItemSet[$RST3['ItemCode']]['Cost']);
						}
						// $LineTotal = $LineTotal + $RST3['LineTotal'];

						if($ItemSet[$RST3['ItemCode']]['Aging'] >= 0 && $ItemSet[$RST3['ItemCode']]['Aging'] <= 3) {
							$L1_Out[$m] = $L1_Out[$m] + ($Quantity * $ItemSet[$RST3['ItemCode']]['Cost']);
						} elseif($ItemSet[$RST3['ItemCode']]['Aging'] >= 4 && $ItemSet[$RST3['ItemCode']]['Aging'] <= 6) {
							$L2_Out[$m] = $L2_Out[$m] + ($Quantity * $ItemSet[$RST3['ItemCode']]['Cost']);
						} elseif($ItemSet[$RST3['ItemCode']]['Aging'] >= 7 && $ItemSet[$RST3['ItemCode']]['Aging'] <= 12) {
							$L3_Out[$m] = $L3_Out[$m] + ($Quantity * $ItemSet[$RST3['ItemCode']]['Cost']);
						} else {
							$L4_Out[$m] = $L4_Out[$m] + ($Quantity * $ItemSet[$RST3['ItemCode']]['Cost']);
						}
					}
					$Data[$TeamArray[$i]]['R3'][$m] = $SaleCost;
					$Data[$TeamArray[$i]]['R4'][$m] = $FreeCost;
					$Data[$TeamArray[$i]]['R5'][$m] = $FreeCost + $SaleCost;
					$SumIn  = $SumIn + $Data[$TeamArray[$i]]['R2'][$m];
					$SumOut = $SumOut + $Data[$TeamArray[$i]]['R5'][$m];

					$Data[$TeamArray[$i]]['R12'][$m] = $SumIn;
					$Data[$TeamArray[$i]]['R13'][$m] = $SumOut;

					if($SumIn > 0) {
						$PoS = ($SumOut / $SumIn) * 100;
					} else {
						$PoS = 0;
					}

					$Data[$TeamArray[$i]]['R14'][$m] = $PoS;
				}
			}
			/* ROW 1 6 11 */
			if($m <= date("m")) {
				$Start = $Ended;
			} else {
				$Start = 0;
			}
			if($m == 1) {
				$Data[$TeamArray[$i]]['R1'][$m] = 0;
			} else {
				$Data[$TeamArray[$i]]['R1'][$m] = $Start;
			}
			$Start = $Data[$TeamArray[$i]]['R1'][$m];
			$Ended = $Start + ($Data[$TeamArray[$i]]['R2'][$m] - $Data[$TeamArray[$i]]['R5'][$m]);
			$Data[$TeamArray[$i]]['R6'][$m] = $Ended;
			if($Data[$TeamArray[$i]]['R5'][$m] != 0) {
				$Data[$TeamArray[$i]]['R11'][$m] = $Data[$TeamArray[$i]]['R6'][$m] / $Data[$TeamArray[$i]]['R5'][$m];
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
			$Data[$TeamArray[$i]]['R7'][$m] = $Ended_L1;

			$Start_L2 = $SUM_L2[$m];
			$Ended_L2 = $Start_L2 + ($L2_In[$m] - $L2_Out[$m]);
			$Data[$TeamArray[$i]]['R8'][$m] = $Ended_L2;
			
			$Start_L3 = $SUM_L3[$m];
			$Ended_L3 = $Start_L3 + ($L3_In[$m] - $L3_Out[$m]);
			$Data[$TeamArray[$i]]['R9'][$m] = $Ended_L3;

			$Start_L4 = $SUM_L4[$m];
			$Ended_L4 = $Start_L4 + ($L4_In[$m] - $L4_Out[$m]);
			$Data[$TeamArray[$i]]['R10'][$m] = $Ended_L4;

		}
	}

	for($i = 0; $i < count($TeamArray); $i++) {
		/* ROW 1 */
		switch ($Data[$TeamArray[$i]]['R0']) {
			case 'MT1': $TeamName = "โมเดิร์นเทรด 1"; break;
			case 'MT2': $TeamName = "โมเดิร์นเทรด 2"; break;
			case 'TT2': $TeamName = "ต่างจังหวัด"; break;
			case 'OUL': $TeamName = "หน้าร้าน + เขตกรุงเทพฯ"; break;
			case 'ONL': $TeamName = "ออนไลน์"; break;
		}
		for($r = 1; $r <= 14; $r++) {
			$rowCls = "";
			switch($r) {
				case 1: $rowCls = "fw-bolder table-active"; break;
				case 2:
				case 12:
					$rowCls = "text-danger";
					break;
				case 3:
				case 4:
				case 13:
					$rowCls = "text-success";
					break;
				case 5: $rowCls = "fw-bolder text-success"; break;
				case 6:
				case 14:
					$rowCls = "fw-bolder table-active";
				break;
				case 11: $rowCls = "table-warning"; break;
			}
			
			if($r == 1) {
				$TBODY .= "<tr><td rowspan='14' style='border-top: 3px double #9A1118;'>$TeamName</td><td class='$rowCls' style='border-top: 3px double #9A1118;'>".$TextArray[$r]."</td>";
				for($m = 1; $m <= 12; $m++) {
					if($Data[$TeamArray[$i]]['R'.$r][$m] == 0) {
						$txt_show = "-";
					} else {
						$txt_show = number_format($Data[$TeamArray[$i]]['R'.$r][$m],0);
					}
					$TBODY .= "<td class='text-right $rowCls' style='border-top: 3px double #9A1118;'>$txt_show</td>";
				}
				$TBODY .="</tr>";
			} else {
				$TBODY .= "<tr><td class='$rowCls'>".$TextArray[$r]."</td>";
				for($m = 1; $m <= 12; $m++) {
					switch($r) {
						case 2:
						case 12:
							$tdCls = "text-right text-danger";
							break;
						case 3:
						case 4:
						case 13:
							$tdCls = "text-right text-success";
							break;
						case 5:  $tdCls = "text-right text-success fw-bolder"; break;
						case 6:  $tdCls = "text-right table-active fw-bolder"; break;
						case 11:
							$tdCls = "text-center fw-bolder";
							if($Data[$TeamArray[$i]]['R'.$r][$m] == "0") {
								$tdCls .= "text-warning table-warning";
							} else {
								if($Data[$TeamArray[$i]]['R'.$r][$m] <= 4) {
									$tdCls .= " text-warning table-warning";
								} elseif($Data[$TeamArray[$i]]['R'.$r][$m] <= 6) {
									$tdCls .= " text-success table-success";
								} else {
									$tdCls .= " text-danger table-danger";
								}
							}
						break;
						case 14:
							$tdCls = "text-center fw-bolder table-active";
							if($Data[$TeamArray[$i]]['R'.$r][$m] < 70) {
								$tdCls .= " text-danger";
							} elseif($Data[$TeamArray[$i]]['R'.$r][$m] >= 70) {
								$tdCls .= " text-success";
							}
						break;
						default: $tdCls = "text-right ";  break;
					}
					if($Data[$TeamArray[$i]]['R'.$r][$m] == 0) {
						$txt_show = "-";
					} else {
						switch($r) {
							case 11:  $txt_show = number_format($Data[$TeamArray[$i]]['R'.$r][$m],2); break;
							case 14:  $txt_show = number_format($Data[$TeamArray[$i]]['R'.$r][$m],2)."%"; break;
							default: $txt_show = number_format($Data[$TeamArray[$i]]['R'.$r][$m],0); break;
							// default: $txt_show = $Data[$TeamArray[$i]]['R'.$r][$m]; break;
						}
					}
					$TBODY .= "<td class='$tdCls'>$txt_show</td>";
				}
				$TBODY .="</tr>";
			}
		}
	}

	$arrCol['TBODY'] = $TBODY;
}
	
array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
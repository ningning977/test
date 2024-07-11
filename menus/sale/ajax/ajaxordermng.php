<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
	exit;
}

function sectoTime($second) {
    $s = $second%60;
    if($s < 10) { $s = "0".$s; } else { $s; }
    $h = floor(($second%86400)/3600);
    if($h < 10) { $h = $h; } else { $h; }
    $m = floor(($second%3600)/60);
    if($m < 10) { $m = $m; } else { $m; }
    $d = floor(($second%2592000)/86400);
    if($d > 0) {
        return "$d วัน $h ชั่วโมงที่ผ่านมา";
    } else {
        if($h > 0) {
            return "$h ชั่วโมงที่ผ่านมา";
        } else {
            return "$m นาทีที่ผ่านมา";
        }
    }
}

if($_GET['p'] == "GetPickerName") {
	$PickerSQL = "SELECT T0.uKey, CONCAT(T0.uName,' ',T0.uLastName,' (',T0.uNickName,')') AS 'PickerName' FROM users T0 WHERE T0.LvCode = 'LV077' AND T0.UserStatus = 'A' ORDER BY T0.uName";
	$PickerQRY = MySQLSelectX($PickerSQL);
	$i = 0;
	while($PickerRST = mysqli_fetch_array($PickerQRY)) {
		$arrCol[$i]['PickUkey'] = $PickerRST['uKey'];
		$arrCol[$i]['PickName'] = $PickerRST['PickerName'];
		$i++;
	}
	$Rows = $i;
	$arrCol['Rows'] = $Rows;
}

if($_GET['p'] == "GetOrder") {
	$tabno      = $_POST['tabno'];
	$filt_year  = $_POST['y'];
	$filt_month = $_POST['m'];
	
	if(isset($_GET['tab'])) {
		$Rows = 0;
	}
	
	$QryName    = $_SESSION['uName']." ".$_SESSION['uLastName'];
	$IPAddr     = $_SERVER['REMOTE_ADDR'];

	/* FILTER TO PICKER SOHEADER */
	$WOWhr = " AND (T2.DeptCode = '".$_SESSION['DeptCode']."')";
	switch($_SESSION['DeptCode']) {
		case "DP001":
		case "DP002":
		case "DP003":
		case "DP009":
		case "DP011":
		case 'DP012' :
			 $SOWhr = NULL; $WOWhr = NULL; break; /* MANAGEMENT & IT & MK & AC & WH */

		case "DP005":
		case "DP006":
		case "DP007":
			if($filt_year >= 2024) {
				$SOWhr = " AND T0.TeamCode IN ('MT1','EXP','MT2','TT2','DMN','KBI')";
			} else{
				switch($_SESSION['DeptCode']) {
					case "DP005": $SOWhr = " AND T0.TeamCode IN ('TT2','DMN','KBI')"; break;
					case "DP006": $SOWhr = " AND T0.TeamCode IN ('MT1','EXP','DMN','KBI')"; break;
					case "DP007": $SOWhr = " AND T0.TeamCode IN ('MT2','DMN','KBI')"; break;
				}
			}
		break;
		case "DP008":
			if($filt_year >= 2022) {
				$SOWhr = " AND T0.TeamCode IN ('TT1','OUL','KBI')";
			} else {
				$SOWhr = " AND T0.TeamCode IN ('OUL','ONL','KBI')";
			}
			break; /* OUL & TT1 & ONL */
		default: $SOWhr = " AND T0.TeamCode IN ('KBI')"; break; /* OTHERS */
	}
	switch($tabno) {
		case 2:  $WhrPicker = " AND T0.StatusDoc IN (2,3)";     break;
		case 3:  $WhrPicker = " AND T0.StatusDoc IN (4,5,6)";   break;
		case 4:  $WhrPicker = " AND T0.StatusDoc IN (7,8)";     break;
		case 5:  $WhrPicker = " AND T0.StatusDoc IN (9,10,11)"; break;
		case 6:  $WhrPicker = " AND T0.StatusDoc IN (11,12,13)";   break;
		case 7:  $WhrPicker = " AND T0.StatusDoc IN (14)";   break;
		case 8:  $WhrPicker = " AND T0.StatusDoc IN (0)";       break;
		default: $WhrPicker = NULL;                             break;
	}

	$arrSOEntry = NULL;
	$arrWOEntry = NULL;

	/* GET DATA FROM SAP */
	
	switch($tabno) {
		case 1:
			$SAPWhr   = " AND YEAR(T1.DocDate) = '".$filt_year."' AND MONTH(T1.DocDate) = '".$filt_month."' ";
			// $SAPWhr  .= " AND T0.[WhsCode] IN ('KSY','KB4','MT','MT2','TT-C','OUL','OSP','NST','PM','PM-KSY','PMTT-KSY','KB1')";
			$TBPrefix = "T0";
			$WhrOrSAP = NULL;
			break;
		default: 
			$SAPWhr   = NULL;
			$TBPrefix = "T3";
			$WhrOrSAP = NULL;
			break;
	}

	switch($tabno) {
		case 3:
		case 4:
			switch($filt_month) {
				case 1:
					$filt_Mprev = 12;
					$filt_Yprev = $filt_year-1;
					break;
				default:
					$filt_Mprev = $filt_month-1;
					$filt_Yprev = $filt_year;
					break;
			}
			break;
		default:
			$filt_Mprev = $filt_month;
			$filt_Yprev = $filt_year;
			break;
	}
	
	switch($tabno) {
		case 6:
		case 7:
			if($tabno == 6) {
				$WoSt = 5;
			} else {
				$WoSt = 14;
			}
			$UnionSQL = 
			"UNION ALL
			SELECT
				'OWAS' AS 'DocType', GROUP_CONCAT(T0.DocEntry) AS 'arrDocEntry'
			FROM OWAS T0
			LEFT JOIN users T1 ON T0.UserCreate = T1.Ukey
			LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
			WHERE
				(
					(((YEAR(T0.DateCreate) = $filt_year AND MONTH(T0.DateCreate) = $filt_month) OR (YEAR(T0.DateCreate) = $filt_Yprev AND MONTH(T0.DateCreate) = $filt_Mprev)) $WOWhr )
				) AND T0.TypeOrder = 'R' AND T0.StatusDoc = $WoSt
			GROUP BY 'DocType'";
			break;
		default:
			$UnionSQL = NULL;
			break;
	}
	if($tabno == 1) {
		$GetSQL =
			"SELECT
				CASE WHEN T0.DocType = 'ORDR' THEN 'ORDR' ELSE 'OWAS' END AS 'DocType', GROUP_CONCAT(T0.SODocEntry) AS 'arrDocEntry'
			FROM picker_soheader T0
			WHERE (T0.UkeyPicker = '' OR T0.TablePacking = 0) AND T0.StatusDoc > 0
			GROUP BY CASE WHEN T0.DocType = 'ORDR' THEN 'ORDR' ELSE 'OWAS' END";
	} else {
		/* GET SODocEntry IN PICKER_HEADER GROUP BY DOCTYPE [ORDR / OWA%] */
		$GetSQL =
			"SELECT
				A0.DocType, GROUP_CONCAT(A0.arrDocEntry) AS 'arrDocEntry'
			FROM (
				SELECT
					CASE WHEN T0.DocType = 'ORDR' THEN 'ORDR' ELSE 'OWAS' END AS 'DocType', GROUP_CONCAT(T0.SODocEntry) AS 'arrDocEntry'
				FROM picker_soheader T0
				WHERE
					(
						(((YEAR(T0.DocDate) = $filt_year AND MONTH(T0.DocDate) = $filt_month) OR (YEAR(T0.DocDate) = $filt_Yprev AND MONTH(T0.Docdate) = $filt_Mprev)) $SOWhr ) OR
						(((YEAR(T0.DocDate) = $filt_year AND MONTH(T0.DocDate) = $filt_month) OR (YEAR(T0.DocDate) = $filt_Yprev AND MONTH(T0.Docdate) = $filt_Mprev)) AND T0.Slpcode IN (20,125,251))
					) $WhrPicker AND (T0.UkeyPicker != '' AND T0.TablePacking != 0)
				GROUP BY CASE WHEN T0.DocType = 'ORDR' THEN 'ORDR' ELSE 'OWAS' END

				$UnionSQL
			) A0
			GROUP BY A0.DocType";
	}
	//echo $GetSQL;
	$GetQRY = MySQLSelectX($GetSQL);
	$RowSO  = 0;
	$RowWO  = 0;
	while($GetRST = mysqli_fetch_array($GetQRY)) {
		if($GetRST['DocType'] == "ORDR") {
			$arrSOEntry = $GetRST['arrDocEntry'];
			$RowSO++;
		} else {
			$arrWOEntry = $GetRST['arrDocEntry'];
			$RowWO++;
		}
	}
	// echo $arrWOEntry;

	$SOSQL = 
		"SELECT
			'$QryName' AS 'Query Name', '$IPAddr' AS 'Query IP', A0.*
		FROM (
			SELECT DISTINCT
				T1.[DocEntry], T6.[U_Dim1], T1.[DocDate] AS 'OrderDate',T1.DocDueDate, T2.[BeginStr] AS 'OrderPrefix', T1.[DocNum] As 'OrderNo', T1.[CardCode], T1.[CardName], T1.[U_PONo] AS 'PO', (T1.[DocTotal]) AS 'OrderTotal',
				T4.DocEntry AS BillDocEntry, T4.[DocDate] AS 'BillDate', T5.[BeginStr] AS 'BillPrefix', T4.[DocNum] AS 'BillNo', T4.[NumAtCard] AS 'RefBill', (T4.[DocTotal]) AS 'BillTotal', T6.SlpName, 1 AS lnRun, T3.TrgetEntry,
				CASE
					WHEN T1.DocStatus = 'O' AND T4.DocEntry IS NULL THEN 1
					WHEN T1.DocStatus = 'O' AND T4.DocEntry IS NOT NULL THEN 9
					WHEN T1.DocStatus = 'C' AND T4.DocEntry IS NOT NULL THEN 9
					WHEN T1.DocStatus = 'C' AND T4.DocEntry IS NULL THEN 0
					WHEN T3.TrgetEntry != 0 THEN 0
				ELSE 2 END AS 'DocStatus',
				CASE WHEN (T1.DocStatus = 'O' AND T4.DocEntry IS NOT NULL) OR (T1.DocStatus = 'C' AND T4.DocEntry IS NOT NULL) THEN 'OINV' ELSE NULL END AS 'DocType',
				CASE WHEN UPPER(SUBSTRING(T1.Comments,1,2)) = 'QQ' OR UPPER(SUBSTRING(T1.Comments,1,3)) = '*QQ' THEN 1 ELSE 0 END AS 'BillQQ'
			FROM RDR1 T0
			LEFT JOIN ORDR T1 ON T0.[DocEntry] = T1.[DocEntry]
			LEFT JOIN NNM1 T2 ON T1.[Series] = T2.[Series]
			LEFT JOIN INV1 T3 ON T0.[TrgetEntry] = T3.[DocEntry]
			LEFT JOIN OINV T4 ON T3.[DocEntry] = T4.[DocEntry]
			LEFT JOIN NNM1 T5 ON T4.[Series] = T5.[Series]
			LEFT JOIN OSLP T6 ON T1.[SlpCode] = T6.[SlpCode]
			WHERE
				T1.[CANCELED] = 'N' AND (T2.[BeginStr] LIKE 'SO-%' OR T2.[BeginStr] LIKE 'SN-%') AND 
				T1.[DocEntry] != '' AND T0.DocEntry $WhrOrSAP IN ($arrSOEntry) $SAPWhr
			UNION ALL
			SELECT DISTINCT
				T1.[DocEntry], T6.[U_Dim1], T1.[DocDate] AS 'OrderDate',T1.DocDueDate, T2.[BeginStr] AS 'OrderPrefix', T1.[DocNum] As 'OrderNo', T1.[CardCode], T1.[CardName], T1.[U_PONo] AS 'PO', (T1.[DocTotal]) AS 'OrderTotal',
				T4.DocEntry AS BillDocEntry, T4.[DocDate] AS 'BillDate', T5.[BeginStr] AS 'BillPrefix', T4.[DocNum] AS 'BillNo', T4.[NumAtCard] AS 'RefBill', (T4.[DocTotal]) AS 'BillTotal', T6.SlpName, 1 AS lnRun, T3.TrgetEntry,
				CASE
					WHEN T1.DocStatus = 'O' AND T4.DocEntry IS NULL THEN 1
					WHEN T1.DocStatus = 'O' AND T4.DocEntry IS NOT NULL THEN 9
					WHEN T1.DocStatus = 'C' AND T4.DocEntry IS NOT NULL THEN 9
					WHEN T1.DocStatus = 'C' AND T4.DocEntry IS NULL THEN 0
					WHEN T3.TrgetEntry != 0 THEN 0
				ELSE 2 END AS 'DocStatus',
				CASE WHEN (T1.DocStatus = 'O' AND T4.DocEntry IS NOT NULL) OR (T1.DocStatus = 'C' AND T4.DocEntry IS NOT NULL) THEN 'ODLN' ELSE NULL END AS 'DocType',
				CASE WHEN UPPER(SUBSTRING(T1.Comments,1,2)) = 'QQ' OR UPPER(SUBSTRING(T1.Comments,1,3)) = '*QQ' THEN 1 ELSE 0 END AS 'BillQQ'
			FROM RDR1 T0
			LEFT JOIN ORDR T1 ON T0.[DocEntry] = T1.[DocEntry]
			LEFT JOIN NNM1 T2 ON T1.[Series] = T2.[Series]
			LEFT JOIN DLN1 T3 ON T0.[TrgetEntry] = T3.[DocEntry]
			LEFT JOIN ODLN T4 ON T3.[DocEntry] = T4.[DocEntry]
			LEFT JOIN NNM1 T5 ON T4.[Series] = T5.[Series]
			LEFT JOIN OSLP T6 ON T1.[SlpCode] = T6.[SlpCode]
			WHERE
				(
					T1.[CANCELED] = 'N' AND (T2.[BeginStr] LIKE 'SA-%' OR T2.[BeginStr] LIKE 'SB-%') AND 
					T1.[DocEntry] != '' AND ($TBPrefix.TrgetEntry = 0 OR $TBPrefix.TrgetEntry IS NULL) AND
					T0.DocEntry $WhrOrSAP IN ($arrSOEntry) $SAPWhr
				) --OR (T0.DocEntry IN (17881))
		) A0
		ORDER BY A0.OrderPrefix DESC, A0.U_Dim1, A0.lnRun, A0.OrderDate DESC, A0.DocEntry, A0.BillTotal DESC
		";
		//echo $SOSQL;

	/* RENDER S/O */
	if($RowSO == 0) {
		$output .= "<tr><td colspan='13' class='text-center'>ไม่พบข้อมูล S/O :(</td></tr>";
	} else {
		/* 1. GET ORDER INFO FROM SAP */
		if($filt_year <= 2022) {
			$SOQRY = conSAP8($SOSQL);
		} else {
			$SOQRY = SAPSelect($SOSQL);
		}
		//echo $SOSQL;
		$tmpDocEntry = NULL;
		$i = 0;
		$DocEtyArr = array();
		$SAPEntry  = NULL;
		while($SORST = odbc_fetch_array($SOQRY)) {
			$DocEntry  = $SORST['DocEntry'];
			$BillTotal = $SORST['BillTotal'];
			if($tmpDocEntry == $DocEntry && $BillTotal == 0) {
				/* DO NOTHING */
			} else {
				array_push($DocEtyArr, $SORST['DocEntry']);
				$ORDocEntry[$DocEntry]   = $SORST['DocEntry'];
				$ORDocDate[$DocEntry]    = date("d/m/Y",strtotime($SORST['OrderDate']));
				$ORDocDueDate[$DocEntry] = date("d/m/Y",strtotime($SORST['DocDueDate']));
				$ORPrefix[$DocEntry]     = $SORST['OrderPrefix'];
				$ORDocNum[$DocEntry]     = $SORST['OrderNo'];
				$ORCardCode[$DocEntry]   = $SORST['CardCode'];
				$ORCardName[$DocEntry]   = conutf8($SORST['CardName']);
				$ORSlpName[$DocEntry]    = conutf8($SORST['SlpName']);
				$ORTeamCode[$DocEntry]   = $SORST['U_Dim1'];
				$ORPONum[$DocEntry]      = conutf8($SORST['PO']);
				$ORDocTotal[$DocEntry]   = number_format($SORST['OrderTotal'],2);

				if($SORST['BillDate'] != "" || $SORST['BillDate'] != NULL) {
					$IVDocDate[$DocEntry] = date("d/m/Y",strtotime($SORST['BillDate']));
				} else {
					$IVDocDate[$DocEntry] = null;
				}

				$IVDocEntry[$DocEntry]    = $SORST['BillDocEntry'];

				if(($SORST['BillPrefix'] == '' || $SORST['BillPrefix'] == NULL) && ($BillTotal != "" || $BillTotal != NULL)) {
					$IVPrefix[$DocEntry] = "IV-";
				} else {
					$IVPrefix[$DocEntry] = $SORST['BillPrefix'];
				}

				$IVDocNum[$DocEntry]     = $SORST['BillNo'];
				$IVRefBill[$DocEntry]    = $SORST['RefBill'];
				$IVDocTotal[$DocEntry]   = $SORST['BillTotal'];

				$ORStatusDoc[$DocEntry]  = $SORST['DocStatus'];
				$IVDocType[$DocEntry]    = $SORST['DocType'];
				$QQSt[$DocEntry]         = $SORST['BillQQ'];

				$SAPEntry .= $DocEntry.",";
				
				$i++;
			}
			$tmpDocEntry = $DocEntry;
		}
		/* 2. GET PICKING INFO FROM EUROX FORCE */
		$SAPEntry = substr($SAPEntry,0,-1);

		// if($i == 0) {
		// 	$PKWhr = " T0.SODocEntry = ''";
		// } else {
		// 	$PKWhr = " T0.SODocEntry IN ($SAPEntry)";
		// }
		if($i > 0) {
			$PKSQL = 
				"SELECT
					'$QryName' AS 'Query Name', '$IPAddr' AS 'Query IP',
					T0.ID, T0.SODocEntry, T0.StatusDoc, T0.DocType, T0.TablePacking, T0.QQst,
					T1.uName, T1.uNickName, T2.uName AS 'OBname', T2.uNickName AS 'OBnickname',
					COUNT(DISTINCT T4.BoxCode) AS 'TotalPack', T3.TablePack, 
					TIMESTAMPDIFF(SECOND, T0.TimeCUT1, NOW()) AS 'TimeDiff',
					TIMESTAMPDIFF(SECOND, IFNULL(T0.TimeCUT2, IFNULL(T0.StartPick, T0.LastUpdate)), NOW()) AS 'CutDiff', T0.ItemCount,T0.Printed
				FROM picker_soheader T0
				LEFT JOIN users T1 ON T0.UkeyPicker = T1.ukey
				LEFT JOIN users T2 ON T0.UkeyOpen   = T2.ukey
				LEFT JOIN pack_header T3 ON T0.ID   = T3.IDPick
				LEFT JOIN pack_boxlist T4 ON T3.BillEntry = T4.BillEntry AND T3.BillType = T4.BillType AND T4.Status = 'C'
				WHERE T0.SODocEntry IN ($SAPEntry) AND T0.DocType = 'ORDR'
				GROUP BY
					T0.ID, T0.SODocEntry, T0.StatusDoc, T0.DocType, T0.TablePacking, 
					T1.uName, T1.uNickName, T2.uName, T2.uNickName, T3.TablePack";
			$PKQRY = MySQLSelectX($PKSQL);
			while($PKRST = mysqli_fetch_array($PKQRY)) {

				$PickID[$PKRST['SODocEntry']]     = $PKRST['ID'];
				$QQSt[$PKRST['SODocEntry']]       = $PKRST['QQst'];
				$TBPacking[$PKRST['SODocEntry']]  = $PKRST['TablePacking'];
				$PickerName[$PKRST['SODocEntry']] = $PKRST['uName']." (".$PKRST['uNickName'].")";
				$ItemCount[$PKRST['SODocEntry']]  = $PKRST['ItemCount'];
				$ORStatusDoc[$PKRST['SODocEntry']] = $PKRST['StatusDoc'];

				if($ORStatusDoc[$PKRST['SODocEntry']] == 0 && $PKRST['StatusDoc'] != 0) {
					$CancelSO = "UPDATE picker_soheader SET StatusDoc = 0, LastUkey = '".$_SESSION['ukey']."', LastUpdate = NOW() WHERE SODocEntry = ".$PKRST['SODocEntry']." AND DocType = '".$PKRST['DocType']."'";
					// MySQLUpdate($CancelSO);
				} else {
					if(($PKRST['StatusDoc'] == 7 || $PKRST['StatusDoc'] == 8) && $ORStatusDoc[$PKRST['SODocEntry']] == 9) {
						$ORDocType[$PKRST['SODocEntry']]   = $PKRST['DocType'];
						$ORStatusDoc[$PKRST['SODocEntry']] = $PKRST['StatusDoc'];
						$BilledSO = "UPDATE picker_soheader SET StatusDoc = 9, LastUkey = '".$_SESSION['ukey']."', LastUpdate = NOW() WHERE SODocEntry = ".$PKRST['SODocEntry']." AND DocType = '".$PKRST['DocType']."'";
						MySQLUpdate($BilledSO);
					} else {
						$ORDocType[$PKRST['SODocEntry']]   = $PKRST['DocType'];
						$ORStatusDoc[$PKRST['SODocEntry']] = $PKRST['StatusDoc'];
					}
				}
				if ($PKRST['Printed'] == 'Y'){
					$PrintSO[$PKRST['SODocEntry']] = " style='color:#A52A2A;font-weight: bold;' ";
				}else{
					$PrintSO[$PKRST['SODocEntry']] = " ";
				}
				
				if($PKRST['StatusDoc'] >= 9) {
					$BilledName[$PKRST['SODocEntry']] = $PKRST['OBname']." (".$PKRST['OBnickname'].")";
				} else {
					$BilledName[$PKRST['SODocEntry']] = NULL;
				}

				$TotalPack[$PKRST['SODocEntry']]  = $PKRST['TotalPack'];

				if($PKRST['TablePack'] != "") {
					$TablePack[$PKRST['SODocEntry']] = $PKRST['TablePack'];
				} else {
					$TablePack[$PKRST['SODocEntry']] = $PKRST['TablePacking'];
				}

				$TimeDiff[$PKRST['SODocEntry']]   = $PKRST['TimeDiff'];
				$CutDiff[$PKRST['SODocEntry']]    = $PKRST['CutDiff'];
			}
		}	
		
		
		for($r = 0; $r < $i; $r++) {
			$txt_status = "";
			if($QQSt[$DocEtyArr[$r]] == "Y") {
				$BillQQ = " <strong class='badge bg-danger'>บิลด่วน</strong>";
			} else {
				$BillQQ = NULL;
			}
			switch($tabno) {
				/* SO TAB 1 & 8 */
				case 1:
				case 8:
					switch($ORStatusDoc[$DocEtyArr[$r]]) {
						case "0":
							$output .= "<tr class='table-active text-muted'>";
							break;
						default:
							$output .= "<tr>";
							break;
					}
					if($tabno == 8) {
						$dis = " disabled";
					} else {
						$dis = NULL;
					}
						$output .= "<td class='text-center'><input type='checkbox' class='addso' name='addso[]' value='".$PickID[$DocEtyArr[$r]]."' $dis /></td>";
						$output .= "<td class='text-center'>".$ORDocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORDocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallSO(\"".$DocEtyArr[$r]."\")'>".$ORPrefix[$DocEtyArr[$r]].$ORDocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$ORCardCode[$DocEtyArr[$r]]." | ".$ORCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td>".$ORPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-right'>".$ORDocTotal[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$ORSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-right'>".$ItemCount[$DocEtyArr[$r]]."</td>";
					$output .= "</tr>";
				break;

				/* SO TAB 2 & 3 */
				case 2:
				case 3:
					switch($ORStatusDoc[$DocEtyArr[$r]]) {
						case "2":
							$txt_status = "<span ".$PrintSO[$DocEtyArr[$r]].">รอหยิบสินค้า</span>";
							$output .= "<tr>";
							break;
						case "3":
							$txt_status = "<span ".$PrintSO[$DocEtyArr[$r]].">กำลังหยิบสินค้า</span>";
							$output .= "<tr class='table-info'>";
							break;
						case "4":
							
							if($TimeDiff[$DocEtyArr[$r]] <= 1800) {
								$output .= "<tr class='table-warning'>";
							} elseif($TimeDiff[$DocEtyArr[$r]] <= 7200) {
								$output .= "<tr class='table-warning' style='color: #9A1118; font-weight: bold;'>";
							} else {
								$output .= "<tr style='background-color: #FF8888; color: #9A1118; font-weight: bold;'>";
							}
							$txt_status = "<a href='javascript:void(0);' onclick='CallCut(\"".$PickID[$DocEtyArr[$r]]."\");'>รอตัดสินค้า</a> <small>(".sectoTime($TimeDiff[$DocEtyArr[$r]]).")</small>";
							break;
						case "5":
							$txt_status = "<a href='javascript:void(0);' onclick='CallWait(\"".$PickID[$DocEtyArr[$r]]."\");' class='text-success'>ยืนยันตัดสินค้า</a> <small>(".sectoTime($CutDiff[$DocEtyArr[$r]]).")</small>";
							$output .= "<tr class='table-success'>";
							break;
						case "6":
							if($CutDiff[$DocEtyArr[$r]] != "" || $CutDiff[$DocEtyArr[$r]] == 0 || $CutDiff[$DocEtyArr[$r]] == "NULL" || $CutDiff[$DocEtyArr[$r]] == NULL) {
								$Diff = " <small>(".sectoTime($CutDiff[$DocEtyArr[$r]]).")</small>";
							} else {
								$Diff = NULL;
							}
							$txt_status = "<a href='javascript:void(0);' onclick='CallWait(\"".$PickID[$DocEtyArr[$r]]."\");'>รอ/แปลง สินค้า</a>$Diff";
							$output .= "<tr class='table-info'>";
							break;
					}
						$output .= "<td class='text-center'>".$ORDocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORDocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center' data-DocEntry='".$DocEtyArr[$r]."' data-PickID='".$PickID[$DocEtyArr[$r]]."'><a href='javascript:void(0);' onclick='CallSO(\"".$DocEtyArr[$r]."\")'>".$ORPrefix[$DocEtyArr[$r]].$ORDocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$ORCardCode[$DocEtyArr[$r]]." | ".$ORCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td class='text-center'>".$ORPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-right'>".$ORDocTotal[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$ORSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$PickerName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$TBPacking[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$txt_status."</td>";
					$output .= "</tr>";
					if(isset($_GET['tab'])) {
						$Rows++;
					}
				break;

				/* SO TAB 4 */
				case 4:
					switch($ORStatusDoc[$DocEtyArr[$r]]) {
						case "7":
							$txt_status = "<a href='javascript:void(0);' onclick='AddBill(".$PickID[$DocEtyArr[$r]].");'>รอเปิดบิล</a>";
							$output .= "<tr>";
							break;
						case "8":
							$txt_status = "รอแก้ไขบิล";
							$output .= "<tr class='table-danger text-danger'>";
							break;
						case "9":
							$txt_status = "<a href='javascript:void(0);' onclick='DoneBill(".$PickID[$DocEtyArr[$r]].");' class='text-success' style='font-weight: bold;'>เปิดบิลเรียบร้อย</a>";
							$output .= "<tr class='table-success'>";
							if($_SESSION['LvCode'] == "LV008" || $_SESSION['LvCode'] == "LV009" || $_SESSION['LvCode'] == "LV079" || $_SESSION['LvCode'] == "LV080" || $_SESSION['LvCode'] == "LV081") {
								$Chk9SQL = "SELECT T0.OpenBill FROM picker_soheader T0 WHERE T0.SODocEntry = ".$DocEtyArr[$r]." AND T0.DocType = '".$ORDocType[$DocEtyArr[$r]]."' LIMIT 1";
								$Chk9RST = MySQLSelect($Chk9SQL);
								if($Chk9RST['OpenBill'] == NULL) {
									$xSQL = "SELECT TOP 1 T0.DocTime, T0.UpdateDate FROM ".$ORDocType[$DocEtyArr[$r]]." T0 WHERE T0.DocEntry = ".$DocEtyArr[$r];
									$xQRY = SAPSelect($xSQL);
									$xRST = odbc_fetch_array($xQRY);
									$Time = $xRST['DocTime'];

									if(strlen($time) != 4) {
										$Time = "0".$Time;
									}

									$NwTime = substr($Time, 0, 2).":".substr($Time, 2, 2).":00";
									$OpenTime = date("Y-m-d",strtotime($xRST['UpdateDate']))." ".$NwTime;
									$OpenIV = "UPDATE picker_soheader SET OpenBill = '$OpenTime', WHERE SODocEntry = ".$DocEtyArr[$r]." AND DocType = '".$ORDocType[$DocEtyArr[$r]]."'";
									// MySQLUpdate($OpenIV);
								}
							}
							break;
					}

					if(strlen($ORPONum[$DocEtyArr[$r]]) > 16) {
						$PONum = "<span title='".$ORPONum[$DocEtyArr[$r]]."'>".substr($ORPONum[$DocEtyArr[$r]],0,16)."...</span>";
					} else {
						$PONum = $ORPONum[$DocEtyArr[$r]];
					}
						$output .= "<td class='text-center'>".$ORDocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORDocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallSO(\"".$DocEtyArr[$r]."\")'>".$ORPrefix[$DocEtyArr[$r]].$ORDocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$ORCardCode[$DocEtyArr[$r]]." | ".$ORCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td class='text-center'>".$ORTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$ORSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$PickerName[$DocEtyArr[$r]]."</td>";
						$output .= "<td style='word-wrap: break-word; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>$PONum</td>";
						//$output .= "<td></td>";
						$output .= "<td class='text-right'>".$ORDocTotal[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallIV(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\")'>".$IVPrefix[$DocEtyArr[$r]].$IVDocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$BilledName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$TBPacking[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$txt_status."</td>";
					$output .= "</tr>";
				break;

				/* SO TAB 5 & 6 */
				case 5:
				case 6:
					switch($ORStatusDoc[$DocEtyArr[$r]]) {
						case "9":
							$txt_status = "รอแพ็กสินค้า";
							$output .= "<tr>";
							break;
						case "10":
							$txt_status = "กำลังแพ็กสินค้า";
							$output .= "<tr class='table-info'>";
							break;
						case "11":
							if($tabno == 5) {
								$txt_status = "<span class='text-success'>แพ็กสินค้าเรียบร้อย</span>";
								$output .= "<tr class='table-success'>";
							} else {
								$txt_status = "<a href='javascript:void(0);' onclick='Send(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\");'>สินค้ารอส่ง</a>";
								$output .= "<tr>";
							}
							break;
						case "12":
						case "13":
							$txt_status = "<a href='javascript:void(0);' onclick='Send(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\");'>กำลังจัดส่ง</a>";
							$output .= "<tr class='table-info'>";
							break;
					}
						$output .= "<td class='text-center'>".$ORDocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORDocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallSO(\"".$DocEtyArr[$r]."\")'>".$ORPrefix[$DocEtyArr[$r]].$ORDocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$ORCardCode[$DocEtyArr[$r]]." | ".$ORCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td>".$ORPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$ORSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallIV(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\")'>".$IVPrefix[$DocEtyArr[$r]].$IVDocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$BilledName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$TablePack[$DocEtyArr[$r]]."</td>";
						if($TotalPack[$DocEtyArr[$r]] == 0) {
							$output .= "<td class='text-right'>0</td>";
						} else {
							$output .= "<td class='text-right'><a href='javascript:void(0);' onclick='CallBox(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\");'>".$TotalPack[$DocEtyArr[$r]]."</a></td>";
						}
						
						$output .= "<td class='text-center'>".$txt_status."</td>";
					$output .= "</tr>";
				break;

				/* SO TAB 7 */
				case 7:
					switch($ORStatusDoc[$DocEtyArr[$r]]) {
						case "13":
							$txt_status = "<a href='javascript:void(0);' onclick='Send(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\");'>รอใบขนส่ง</a>";
							$output .= "<tr>";
							break;
						case "14":
							$txt_status = "<strong class='text-success'>เสร็จสิ้น</strong>";
							$output .= "<tr class='table-success'>";
							break;
					}
					if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP011") {
						$btnClass = "";
					} else {
						$btnClass = "class='disabled'";
					}
						$output .= "<td class='text-center'>".$ORDocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORDocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallSO(\"".$DocEtyArr[$r]."\")'>".$ORPrefix[$DocEtyArr[$r]].$ORDocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$ORCardCode[$DocEtyArr[$r]]." | ".$ORCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td>".$ORPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$ORTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$ORSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallIV(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\")'>".$IVPrefix[$DocEtyArr[$r]].$IVDocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td class='text-right'><a href='javascript:void(0);' onclick='CallBox(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\");'>".$TotalPack[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td class='text-center'>".$txt_status."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='ShipTrack(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\");'><i class='far fa-file-alt fa-fw fa-1x'></i></a></td>";
						$output .= "<td class='text-center'><a $btnClass href='javascript:void(0);' onclick='Send(\"".$IVDocEntry[$DocEtyArr[$r]]."\",\"".$IVDocType[$DocEtyArr[$r]]."\");'><i class='fas fa-paperclip fa-fw fa-1x'></i></a></td>";
					$output .= "</tr>";
				break;
			}
		}
	}

	/* RENDER W/O */
	if($RowWO == 0) {
		$output .= "<tr><td colspan='13' class='text-center'>ไม่พบข้อมูล W/O :(</td></tr>";
	} else {
		if(isset($tabno)) {

			if($tabno == 6 || $tabno == 7) {
				if($tabno == 7) {
					$Status = " AND T0.StatusDoc IN (14)";
				} else {
					$Status = " AND T0.StatusDoc IN (5)";
				}

				$OWASWhere = " OR ((YEAR(T0.DateCreate) = $filt_year AND MONTH(T0.DateCreate) = $filt_month) AND T0.TypeOrder = 'R'$Status)";
			} else {
				$OWASWhere = NULL;
			}
			$WOSQL =
				"SELECT
					T0.DocEntry, CONCAT('OWA',T0.TypeOrder) AS 'TypeOrder', T2.DeptCode,
					DATE(T0.DateCreate) AS 'DocDate', DATE(T0.TimeContrac) AS 'DocDueDate',
					'' AS 'OrderPrefix', T0.DocNum AS 'OrderNo',
					CASE
						WHEN T2.DeptCode = 'DP006' THEN 'MT1'
						WHEN T2.DeptCode = 'DP007' THEN 'MT2'
						WHEN T2.DeptCode = 'DP005' THEN 'TT2'
						WHEN T2.DeptCode = 'DP008' THEN 'OUL'
						WHEN T2.DeptCode = 'DP004' THEN 'PUR'
					ELSE 'KBI' END AS 'U_Dim1',
					T0.CusCode AS 'CardCode', T0.CusName AS 'CardName', '' AS 'PO', '' AS 'OrderTotal',
					CONCAT(T1.uName,' ',T1.uLastName,' (',T1.uNickName,')') AS 'SlpName', T0.StatusDoc
				FROM OWAS T0
				LEFT JOIN users T1 ON T0.UserCreate = T1.uKey
				LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
				WHERE ((YEAR(T0.DateCreate) = $filt_year AND MONTH(T0.DateCreate) = $filt_month) AND (T0.DocEntry  $WhrOrSAP IN ($arrWOEntry)))";
			// echo $WOSQL;
			$WOQRY = MySQLSelectX($WOSQL);
			$tmpDocEntry = NULL;
			$i = 0;
			$DocEtyArr = array();
			$WHOEntry  = NULL;
			while($WORST = mysqli_fetch_array($WOQRY)) {
				$DocEntry = $WORST['DocEntry'];
				array_push($DocEtyArr,$WORST['DocEntry']);
				$WODocEntry[$DocEntry]   = $WORST['DocEntry'];
				$WODocDate[$DocEntry]    = date("d/m/Y",strtotime($WORST['DocDate']));
				if($WORST['DocDueDate'] == NULL) {
					$WODocDueDate[$DocEntry] = NULL;
				} else {
					$WODocDueDate[$DocEntry] = date("d/m/Y",strtotime($WORST['DocDueDate']));
				}
				
				$WOPrefix[$DocEntry]     = $WORST['OrderPrefix'];
				$WODocNum[$DocEntry]     = $WORST['OrderNo'];
				$WOCardCode[$DocEntry]   = $WORST['CardCode'];
				$WOCardName[$DocEntry]   = $WORST['CardName'];
				$WOSlpName[$DocEntry]    = $WORST['SlpName'];
				$WOTeamCode[$DocEntry]   = $WORST['U_Dim1'];
				$WOPONum[$DocEntry]      = $WORST['PO'];
				$WODocTotal[$DocEntry]   = $WORST['OrderTotal'];
				$WHOEntry .= $DocEntry.",";
				$i++;
			}
			$WHOEntry = substr($WHOEntry,0,-1);
			if($i > 0) {
				$PKSQL =
					"SELECT
						'$QryName' AS 'Query Name', '$IPAddr' AS 'Query IP',
						T0.ID, T0.SODocEntry, T0.StatusDoc, T0.DocType, T0.TablePacking, T0.QQst,
						T1.uName, T1.uNickName, T2.uName AS 'OBname', T2.uNickName AS 'OBnickname',
						COUNT(T4.BoxCode) AS 'TotalPack', T3.TablePack,
						TIMESTAMPDIFF(SECOND, T0.TimeCUT1, NOW()) AS 'TimeDiff',
						TIMESTAMPDIFF(SECOND, IFNULL(T0.TimeCUT2, IFNULL(T0.StartPick, T0.LastUpdate)), NOW()) AS 'CutDiff', T0.ItemCount
					FROM picker_soheader T0
					LEFT JOIN users T1 ON T0.UkeyPicker = T1.ukey
					LEFT JOIN users T2 ON T0.UkeyOpen   = T2.ukey
					LEFT JOIN pack_header T3 ON T0.ID   = T3.IDPick
					LEFT JOIN pack_boxlist T4 ON T3.BillEntry = T4.BillEntry AND T3.BillType = T4.BillType AND T4.Status = 'C'
					WHERE T0.SODocEntry IN ($WHOEntry) AND T0.DocType LIKE 'OWA%'
					GROUP BY
						T0.ID, T0.SODocEntry, T0.StatusDoc, T0.DocType, T0.TablePacking,
						T1.uName, T1.uNickName, T2.uName, T2.uNickName, T3.TablePack
					UNION ALL
					SELECT
						'$QryName' AS 'Query Name', '$IPAddr' AS 'Query IP',
						T0.DocEntry AS 'ID', T0.DocEntry AS 'SODocEntry', T0.StatusDoc AS 'StatusDoc', CONCAT('OWA',T0.TypeOrder) AS 'DocType', NULL AS 'TablePacking', CASE WHEN UPPER(T0.Remark) LIKE '%QQ%' THEN 'Y' ELSE 'N' END AS 'QQst',
						NULL AS 'uName', NULL AS 'uNickName', NULL AS 'OBname', NULL AS 'OBnickname',
						NULL AS 'TotalPack', NULL AS 'TablePack',
						NULL AS 'TimeDiff', NULL AS 'CutDiff', NULL AS 'ItemCount'
					FROM OWAS T0
					WHERE YEAR(T0.DateCreate) = $filt_year AND MONTH(T0.DateCreate) = $filt_month AND T0.TypeOrder = 'R'";
				// echo $PKSQL;
				$PKQRY = MySQLSelectX($PKSQL);
				while($PKRST = mysqli_fetch_array($PKQRY)) {
					$WODocType[$PKRST['SODocEntry']]   = $PKRST['DocType'];
					$WOStatusDoc[$PKRST['SODocEntry']] = $PKRST['StatusDoc'];
					$PickID[$PKRST['SODocEntry']]      = $PKRST['ID'];
					$QQSt[$PKRST['SODocEntry']]        = $PKRST['QQst'];
					$TBPacking[$PKRST['SODocEntry']]   = $PKRST['TablePacking'];
					$PickerName[$PKRST['SODocEntry']]  = $PKRST['uName']." (".$PKRST['uNickName'].")";
					$ItemCount[$PKRST['SODocEntry']]    = $PKRST['ItemCount'];

					if($PKRST['StatusDoc'] >= 9) {
						$BilledName[$PKRST['SODocEntry']] = $PKRST['OBname']." (".$PKRST['OBnickname'].")";
					} else {
						$BilledName[$PKRST['SODocEntry']] = NULL;
					}

					$TotalPack[$PKRST['SODocEntry']]   = $PKRST['TotalPack'];
					
					if($PKRST['TablePack'] != "") {
						$TablePack[$PKRST['SODocEntry']] = $PKRST['TablePack'];
					} else {
						$TablePack[$PKRST['SODocEntry']] = $PKRST['TablePacking'];
					}
					$TimeDiff[$PKRST['SODocEntry']]   = $PKRST['TimeDiff'];
					$CutDiff[$PKRST['SODocEntry']]    = $PKRST['CutDiff'];
				}
			}
		}

		for($r = 0; $r < $i; $r++) {
			if($QQSt[$DocEtyArr[$r]] == "Y") {
				$BillQQ = " <strong class='badge bg-danger'>บิลด่วน</strong>";
			} else {
				$BillQQ = NULL;
			}
			switch($tabno) {
				/* WO TAB 1 & 8 */
				case 1:
				case 8:
					switch($WOStatusDoc[$DocEtyArr[$r]]) {
						case "0":
							$output .= "<tr class='table-active text-muted'>";
							break;
						default:
							$output .= "<tr>";
							break;
					}
					if($tabno == 8) {
						$dis = " disabled";
					} else {
						$dis = NULL;
					}
						$output .= "<td class='text-center'><input type='checkbox' class='addso' name='addso[]' value='".$PickID[$DocEtyArr[$r]]."' $dis /></td>";
						$output .= "<td class='text-center'>".$WODocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$WODocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallWO(\"".$DocEtyArr[$r]."\")'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$WOCardCode[$DocEtyArr[$r]]." | ".$WOCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td>".$WOPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-right'>".$WODocTotal[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$WOTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$WOSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-right'>".$ItemCount[$DocEtyArr[$r]]."</td>";
					$output .= "</tr>";
				break;

				/* WO TAB 2 & 3 */
				case 2:
				case 3:
					switch($WOStatusDoc[$DocEtyArr[$r]]) {
						case "2":
							$txt_status = "รอหยิบสินค้า";
							$output .= "<tr>";
							break;
						case "3":
							$txt_status = "กำลังหยิบสินค้า";
							$output .= "<tr class='table-info'>";
							break;
						case "4":
							$txt_status = "<a href='javascript:void(0);' onclick='CallCut(\"".$PickID[$DocEtyArr[$r]]."\");'>รอตัดสินค้า</a> <small>(".sectoTime($TimeDiff[$DocEtyArr[$r]]).")</small>";
							if($TimeDiff[$DocEtyArr[$r]] <= 1800) {
								$output .= "<tr class='table-warning'>";
							} else {
								$output .= "<tr class='table-warning' style='color: #9A1118; font-weight: bold;'>";
							}
							break;
						case "5":
							$txt_status = "<a href='javascript:void(0);' onclick='CallWait(\"".$PickID[$DocEtyArr[$r]]."\");' class='text-success'>ยืนยันตัดสินค้า</a> <small>(".sectoTime($CutDiff[$DocEtyArr[$r]]).")</small>";
							$output .= "<tr class='table-success'>";

							break;
						case "6":
							if($CutDiff[$DocEtyArr[$r]] != "" || $CutDiff[$DocEtyArr[$r]] == 0 || $CutDiff[$DocEtyArr[$r]] == "NULL" || $CutDiff[$DocEtyArr[$r]] == NULL) {
								$Diff = " <small>(".sectoTime($CutDiff[$DocEtyArr[$r]]).")</small>";
							} else {
								$Diff = NULL;
							}
							$txt_status = "<a href='javascript:void(0);' onclick='CallWait(\"".$PickID[$DocEtyArr[$r]]."\");'>รอ/แปลง สินค้า</a>$Diff";
							$output .= "<tr class='table-info'>";
							break;
					}
						$output .= "<td class='text-center'>".$WODocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$WODocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallWO(\"".$DocEtyArr[$r]."\")'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$WOCardCode[$DocEtyArr[$r]]." | ".$WOCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td>".$WOPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>&nbsp;</td>";
						$output .= "<td class='text-center'>".$WOTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$WOSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$PickerName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$TBPacking[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$txt_status."</td>";
					$output .= "</tr>";
					if(isset($_GET['tab'])) {
						$Rows++;
					}
				break;

				/* WO TAB 4 */
				case 4:
					switch($WOStatusDoc[$DocEtyArr[$r]]) {
						case "7":
							$txt_status = "<a href='javascript:void(0);' onclick='AddBill(".$PickID[$DocEtyArr[$r]].");'>รอเปิดบิล</a>";
							$output .= "<tr>";
							break;
						case "8":
							$txt_status = "รอแก้ไขบิล";
							$output .= "<tr class='table-danger text-danger'>";
							break;
						case "9":
							$txt_status = "<a href='javascript:void(0);' onclick='DoneBill(".$PickID[$DocEtyArr[$r]].");' class='text-success' style='font-weight: bold;'>เปิดบิลเรียบร้อย</a>";
							$output .= "<tr class='table-success'>";
							break;
					}
						$output .= "<td class='text-center'>".$WODocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$WODocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallWO(\"".$DocEtyArr[$r]."\")'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$WOCardCode[$DocEtyArr[$r]]." | ".$WOCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td class='text-center'>".$WOTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$WOSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$PickerName[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$WOPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-right'>".$WODocTotal[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallWait(\"".$PickID[$DocEtyArr[$r]]."\");'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$BilledName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$TBPacking[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$txt_status."</td>";
					$output .= "</tr>";	
				break;

				/* WO TAB 5 & 6 */
				case 5:
				case 6:
					if($WODocType[$DocEtyArr[$r]] == "OWAR") {
						$CallBill   = "<a href='javascript:void(0);' onclick='CallWO(\"".$DocEtyArr[$r]."\")'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a>";
					} else {
						$CallBill   = "<a href='javascript:void(0);' onclick='CallWait(\"".$PickID[$DocEtyArr[$r]]."\");'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a>";
					}
					switch($WOStatusDoc[$DocEtyArr[$r]]) {
						case "9":
							$txt_status = "รอแพ็กสินค้า";
							$output .= "<tr>";
							break;
						case "10":
							$txt_status = "กำลังแพ็กสินค้า";
							$output .= "<tr class='table-info'>";
							break;
						case "11":
							if($tabno == 5) {
								$txt_status = "<span class='text-success'>แพ็กสินค้าเรียบร้อย</span>";
								$output .= "<tr class='table-success'>";
							} else {
								$txt_status = "<a href='javascript:void(0);' onclick='Send(\"".$WODocEntry[$DocEtyArr[$r]]."\",\"".$WODocType[$DocEtyArr[$r]]."\");'>สินค้ารอส่ง</a>";
								$output .= "<tr>";
							}
							
							break;
						case "5":
						case "12":
						case "13":
							if($WODocType[$DocEtyArr[$r]] == "OWAR") {
								$txt_status = "<a href='javascript:void(0);' onclick='Send(\"".$WODocEntry[$DocEtyArr[$r]]."\",\"".$WODocType[$DocEtyArr[$r]]."\");'>รอรับสินค้า</a>";
							} else {
								$txt_status = "<a href='javascript:void(0);' onclick='Send(\"".$WODocEntry[$DocEtyArr[$r]]."\",\"".$WODocType[$DocEtyArr[$r]]."\");'>กำลังจัดส่ง</a>";
							}
							$output .= "<tr class='table-info'>";
							break;
					}
						$output .= "<td class='text-center'>".$WODocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$WODocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallWO(\"".$DocEtyArr[$r]."\")'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$WOCardCode[$DocEtyArr[$r]]." | ".$WOCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td>".$WOPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$WOTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$WOSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>$CallBill</td>";
						$output .= "<td>".$BilledName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$TablePack[$DocEtyArr[$r]]."</td>";
						if($TotalPack[$DocEtyArr[$r]] == 0) {
							$output .= "<td class='text-right'>0</td>";
						} else {
							$output .= "<td class='text-right'><a href='javascript:void(0);' onclick='CallBox(\"".$WODocEntry[$DocEtyArr[$r]]."\",\"".$WODocType[$DocEtyArr[$r]]."\");'>".$TotalPack[$DocEtyArr[$r]]."</a></td>";
						}
						
						$output .= "<td class='text-center'>".$txt_status."</td>";
					$output .= "</tr>";
				break;

				/* WO TAB 7 */
				case 7:
					if($WODocType[$DocEtyArr[$r]] == "OWAR") {
						$CallBill   = "<a href='javascript:void(0);' onclick='CallWO(\"".$DocEtyArr[$r]."\")'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a>";
					} else {
						$CallBill   = "<a href='javascript:void(0);' onclick='CallWait(\"".$PickID[$DocEtyArr[$r]]."\");'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a>";
					}
					switch($WOStatusDoc[$DocEtyArr[$r]]) {
						case "13":
							$txt_status = "รอใบขนส่ง";
							$output .= "<tr>";
							break;
						case "14":
							$txt_status = "<strong class='text-success'>เสร็จสิ้น</strong>";
							$output .= "<tr class='table-success'>";
							break;
					}
					if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP011") {
						$btnClass = "";
					} else {
						$btnClass = "class='disabled'";
					}
						$output .= "<td class='text-center'>".$WODocDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$WODocDueDate[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallWO(\"".$DocEtyArr[$r]."\")'>".$WOPrefix[$DocEtyArr[$r]].$WODocNum[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td>".$WOCardCode[$DocEtyArr[$r]]." | ".$WOCardName[$DocEtyArr[$r]]."$BillQQ</td>";
						$output .= "<td>".$WOPONum[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>".$WOTeamCode[$DocEtyArr[$r]]."</td>";
						$output .= "<td>".$WOSlpName[$DocEtyArr[$r]]."</td>";
						$output .= "<td class='text-center'>$CallBill</td>";
						$output .= "<td class='text-right'><a href='javascript:void(0);' onclick='CallBox(\"".$WODocEntry[$DocEtyArr[$r]]."\",\"".$WODocType[$DocEtyArr[$r]]."\");'>".$TotalPack[$DocEtyArr[$r]]."</a></td>";
						$output .= "<td class='text-center'>".$txt_status."</td>";
						$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='ShipTrack(\"".$WODocEntry[$DocEtyArr[$r]]."\",\"".$WODocType[$DocEtyArr[$r]]."\");'><i class='far fa-file-alt fa-fw fa-1x'></i></a></td>";
						$output .= "<td class='text-center'><a $btnClass href='javascript:void(0);' onclick='Send(\"".$WODocEntry[$DocEtyArr[$r]]."\",\"".$WODocType[$DocEtyArr[$r]]."\");'><i class='fas fa-paperclip fa-fw fa-1x'></i></a></td>";
					$output .= "</tr>";
				break;
			}
		}
	}

	$arrCol['OrderList'] = $output;
	
	if(isset($_GET['tab'])) {
		$arrCol['Rows'] = $Rows;
	}
	
}

if($_GET['p'] == "CallSO") {
	$DocEntry = $_POST['DocEntry'];
	$GetSQL =
		"SELECT
			/* SO HEADER */
			T0.DocEntry, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'SODocNum', T0.CardCode, T0.CardName,
			T3.SlpName, T0.DocDate, T0.DocDueDate, T0.Comments, T0.U_PONo, T0.AtcEntry,
			/* SO DETAIL */
			T1.VisOrder,T1.ItemCode, CASE WHEN (T1.SubCatNum = '' OR T1.SubCatNum IS NULL) THEN T1.CodeBars ELSE T1.SubCatNum END AS 'CodeBars', T1.Dscription, T1.WhsCode, T1.Quantity, T1.UnitMsr, 
			T1.PriceBefDi, 
			T1.DiscPrcnt, T1.U_DiscP1, T1.U_DiscP2, T1.U_DiscP3, T1.U_DiscP4, T1.U_DiscP5,
			T1.LineTotal,
			/* SO FOOTER */
			T0.DocTotal, T0.VatSum, (T4.lastName+' '+T4.firstName) AS 'OwnerName'
		FROM ORDR T0
		LEFT JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
		LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
		LEFT JOIN OHEM T4 ON T0.OwnerCode = T4.empID
		WHERE T0.DocEntry = $DocEntry
		ORDER BY T1.VisOrder ASC";
	$GetQRY = SAPSelect($GetSQL);

	/* GET PICKED NAME AND TABLE */
	$PKSQL = "SELECT T0.ID, T0.DateCreate, T0.UKeyPicker, T0.TablePacking FROM picker_soheader T0 WHERE T0.SODocEntry = $DocEntry AND T0.DocType = 'ORDR' LIMIT 1";
	$Rows  = ChkRowDB($PKSQL);

	if($Rows > 0) {
		$PKRST = MySQLSelect($PKSQL);
		$arrCol['HD']['PickID']     = $PKRST['ID'];
		$arrCol['HD']['PickUkey']   = $PKRST['UKeyPicker'];
		$arrCol['HD']['TablePack']  = $PKRST['TablePacking'];
		$arrCol['HD']['DateCreate'] = date("Y-m-d\TH:i",strtotime($PKRST['DateCreate']));
	} else {
		$arrCol['HD']['PickID']     = NULL;
		$arrCol['HD']['PickUkey']   = NULL;
		$arrCol['HD']['TablePack']  = NULL;
		$arrCol['HD']['DateCreate'] = NULL;
	}
	
	$arrCol['HD']['DocEntry'] = NULL;

	$i = 0;
	while($GetRST = odbc_fetch_array($GetQRY)) {
		if($arrCol['HD']['DocEntry'] == NULL) {
			$SODocEntry = $GetRST['DocEntry'];
			$arrCol['HD']['DocEntry']   = $GetRST['DocEntry'];
			$arrCol['HD']['SODocNum']   = $GetRST['SODocNum'];
			$arrCol['HD']['CardCode']   = conutf8($GetRST['CardCode']." | ".$GetRST['CardName']);
			$arrCol['HD']['DocDate']    = date("Y-m-d",strtotime($GetRST['DocDate']));
			$arrCol['HD']['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
			$arrCol['HD']['SlpName']    = conutf8($GetRST['SlpName']);
			$arrCol['HD']['Comments']   = conutf8($GetRST['Comments']);
			$arrCol['HD']['U_PONo']     = conutf8($GetRST['U_PONo']);
			$arrCol['FT']['DocTotal']   = $GetRST['DocTotal'];
			$arrCol['FT']['VatSum']     = $GetRST['VatSum'];
			$arrCol['FT']['OwnerName']  = conutf8($GetRST['OwnerName']);
			if($GetRST['AtcEntry'] != NULL) {
				$AtcEntry = $GetRST['AtcEntry'];
			}
		}
		$arrCol['BD_'.$i]['VisOrder']   = $GetRST['VisOrder'];
		$arrCol['BD_'.$i]['ItemCode']   = $GetRST['ItemCode'];
		$arrCol['BD_'.$i]['CodeBars']   = $GetRST['CodeBars'];
		$arrCol['BD_'.$i]['Dscription'] = conutf8($GetRST['Dscription']);
		$arrCol['BD_'.$i]['WhsCode']    = conutf8($GetRST['WhsCode']);
		$arrCol['BD_'.$i]['Quantity']   = $GetRST['Quantity'];
		$arrCol['BD_'.$i]['UnitMsr']    = conutf8($GetRST['UnitMsr']);
		$arrCol['BD_'.$i]['PriceBefDi'] = $GetRST['PriceBefDi'];
		$arrCol['BD_'.$i]['LineTotal']  = $GetRST['LineTotal'];

		if ($GetRST['U_DiscP5'] != NULL and $GetRST['U_DiscP5'] != "" and $GetRST['U_DiscP5'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%+" . number_format($GetRST['U_DiscP4'], 2) . "%+" . number_format($GetRST['U_DiscP5'], 2) . "%";
        } elseif ($GetRST['U_DiscP4'] != NULL and $GetRST['U_DiscP4'] != "" and $GetRST['U_DiscP4'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%+" . number_format($GetRST['U_DiscP4'], 2) . "%";
        } elseif ($GetRST['U_DiscP3'] != NULL and $GetRST['U_DiscP3'] != "" and $GetRST['U_DiscP3'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%";
        } elseif ($GetRST['U_DiscP2'] != NULL and $GetRST['U_DiscP2'] != "" and $GetRST['U_DiscP2'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%";
        } elseif ($GetRST['U_DiscP1'] != NULL and $GetRST['U_DiscP1'] != "" and $GetRST['U_DiscP1'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%";
        } else {
            $Discount = NULL;
        }
		$arrCol['BD_'.$i]['Discount']   = $Discount;
		$i++;
	}
	$Rows = $i;
	$arrCol['Rows'] = $Rows;

	/* ATTACHMENT */

	if(isset($AtcEntry)) {
		$AttSQL  = "SELECT T0.trgtPath, T0.FileName,T0.FileExt FROM ATC1 T0 WHERE T0.AbsEntry = $AtcEntry ORDER BY T0.Line ASC";
		$AttRows = ChkRowSAP($AttSQL);
		if($AttRows == 0) {
			$arrCol['AttRows'] = 0;
		} else {
			$arrCol['AttRows'] = $AttRows;

			$AttQRY = SAPSelect($AttSQL);
			$i = 0;
			while($AttRST = odbc_fetch_array($AttQRY)) {
				$arrCol['AT_'.$i]['FileName'] = conutf8($AttRST['FileName'].".".$AttRST['FileExt']);
				$arrCol['AT_'.$i]['FilePath'] = "file:".str_replace(" ","%20",str_replace("\\","/",$AttRST['trgtPath']))."/".conutf8(str_replace(" ","%20",$AttRST['FileName']).".".$AttRST['FileExt']);
				$i++;
			}
		}
	} else {
		$AttSQL =
			"SELECT
				T0.VisOrder, T0.FileOriName, T0.FileDirName, T0.FileExt
			FROM order_attach T0
			LEFT JOIN order_header T1 ON T0.DocEntry = T1.DocEntry
			WHERE T1.ImportEntry = $SODocEntry AND T0.FileStatus = 'A'";
		$AttRows = ChkRowDB($AttSQL);
		if($AttRows == 0) {
			$arrCol['AttRows'] = 0;
		} else {
			$arrCol['AttRows'] = $AttRows;
			
			$AttQRY = MySQLSelectX($AttSQL);
			$i = 0;
			while($AttRST = mysqli_fetch_array($AttQRY)) {
				$arrCol['AT_'.$i]['FileName'] = $AttRST['FileOriName'].".".$AttRST['FileExt'];
				$arrCol['AT_'.$i]['FilePath'] = "../FileAttach/SO/".$AttRST['FileDirName'].".".$AttRST['FileExt'];
				$i++;
			}
		}
	}

	
}

if($_GET['p'] == "CallCut") {
	$PickID = $_POST['pid'];
	$PickSQL = "SELECT T0.ID AS 'PickID', T0.DocType AS 'SOType', T1.* FROM picker_soheader T0 LEFT JOIN picker_sodetail T1 ON T0.SODocEntry = T1.DocEntry AND T0.DocType = T1.DocType LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode WHERE T0.ID = $PickID AND T1.BomItem = 0 ORDER BY T1.VisOrder ASC";
	$PickQRY = MySQLSelectX($PickSQL);

	$DocEntry = NULL;
	$i = 0;

	while($PickRST = mysqli_fetch_array($PickQRY)) {
		if($DocEntry == NULL) {
			$DocEntry = $PickRST['DocEntry'];
			$DocType  = $PickRST['SOType'];
			$arrCol['HD']['PickID']  = $PickRST['PickID'];
			$arrCol['HD']['DocType'] = $DocType;
		}
		$arrCol['BD_'.$i]['TransID'] = $PickRST['ID'];
		$arrCol['BD_'.$i]['OpenQty'] = $PickRST['OpenQty'];
		$arrCol['BD_'.$i]['Remark']  = $PickRST['Remark'];
		$i++;
	}
	
	switch($DocType) {
		case "ORDR":
			$GetSQL =
				"SELECT
					/* SO HEADER */
					T0.DocEntry, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'SODocNum', T0.CardCode, T0.CardName,
					T3.SlpName, T0.DocDate, T0.DocDueDate, T0.Comments, T0.U_PONo, T0.AtcEntry,
					/* SO DETAIL */
					T1.VisOrder,T1.ItemCode, CASE WHEN (T1.SubCatNum = '' OR T1.SubCatNum IS NULL) THEN T1.CodeBars ELSE T1.SubCatNum END AS 'CodeBars', T1.Dscription, T1.WhsCode, T1.Quantity, T1.UnitMsr, 
					/* SO FOOTER */
					(T4.lastName+' '+T4.firstName) AS 'OwnerName'
				FROM ORDR T0
				LEFT JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
				LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
				LEFT JOIN OHEM T4 ON T0.OwnerCode = T4.empID
				WHERE T0.DocEntry = $DocEntry
				ORDER BY T1.VisOrder ASC";
			$GetQRY = SAPSelect($GetSQL);
			
			$arrCol['HD']['DocEntry'] = NULL;
			$i = 0;

			while($GetRST = odbc_fetch_array($GetQRY)) {
				/* GET ONHAND IN SAP */
				$SAPSQL  = "SELECT TOP 1 SUM(P0.OnHand) AS 'SAPOnHand' FROM OITW P0 WHERE P0.ItemCode = '".$GetRST['ItemCode']."' AND P0.WhsCode = N'".conutf8($GetRST['WhsCode'])."'";
				$SAPQRY  = SAPSelect($SAPSQL);
				$SAPRST  = odbc_fetch_array($SAPQRY);

				/* GET PICKED IN EUROX FORCE */
				$PkSQL   = "SELECT SUM(T0.OpenQty) AS 'OpenQty' FROM picker_sodetail T0 LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry AND T0.DocType = T1.DocType WHERE T0.ItemCode = '".$GetRST['ItemCode']."' AND T0.WhsCode = '".conutf8($GetRST['WhsCode'])."' AND T1.StatusDoc BETWEEN 2 AND 8 LIMIT 1";
				$PkRST   = MySQLSelect($PkSQL);
				$OpenQty = $PkRST['OpenQty'];

				if($arrCol['HD']['DocEntry'] == NULL) {
					$arrCol['HD']['DocEntry']   = $GetRST['DocEntry'];
					$arrCol['HD']['SODocNum']   = $GetRST['SODocNum'];
					$arrCol['HD']['CardCode']   = conutf8($GetRST['CardCode']." | ".$GetRST['CardName']);
					$arrCol['HD']['DocDate']    = date("Y-m-d",strtotime($GetRST['DocDate']));
					$arrCol['HD']['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
					$arrCol['HD']['SlpName']    = conutf8($GetRST['SlpName']);
					$arrCol['HD']['Comments']   = conutf8($GetRST['Comments']);
					$arrCol['FT']['OwnerName']  = conutf8($GetRST['OwnerName']);
				}
				$arrCol['BD_'.$i]['ItemCode']   = $GetRST['ItemCode'];
				$arrCol['BD_'.$i]['CodeBars']   = $GetRST['CodeBars'];
				$arrCol['BD_'.$i]['Dscription'] = conutf8($GetRST['Dscription']);
				$arrCol['BD_'.$i]['WhsCode']    = conutf8($GetRST['WhsCode']);
				$arrCol['BD_'.$i]['SAPOnHand']  = $SAPRST['SAPOnHand']-$OpenQty;
				$arrCol['BD_'.$i]['Quantity']   = $GetRST['Quantity'];
				$arrCol['BD_'.$i]['UnitMsr']    = conutf8($GetRST['UnitMsr']);

				$i++;
			}
			$Rows = $i;
			$arrCol['Rows'] = $Rows;

		break;
		case "OWAS":
		case "OWAB":
			$GetSQL =
				"SELECT
					/* WO HEADER */
					T0.DocEntry, T0.DocNum AS 'SODocNum', T0.CusCode AS 'CardCode', T0.CusName AS 'CardName',
					CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')') AS 'SlpName', DATE(T0.DateCreate) AS 'DocDate',
					DATE(T0.TimeContrac) AS 'DocDueDate', T0.Remark AS 'Comments', NULL AS 'U_PONo', NULL AS 'ActEntry',
					/* WO DETAIL */
					T1.lnNum AS 'VisOrder', T1.ItemCode, T1.BarCode AS 'CodeBars', T1.ItemName AS 'Dscription', T1.WhsCode, T1.Qty AS 'Quantity', T1.UnitMgr AS 'UnitMsr',
					CONCAT(T2.uName,' ',T2.uLastName) AS 'OwnerName'
				FROM OWAS T0
				LEFT JOIN WAS1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN users T2 ON T0.UserCreate = T2.uKey
				WHERE T0.DocEntry = $DocEntry
				ORDER BY T1.lnNum ASC";
			$GetQRY = MySQLSelectX($GetSQL);
			$arrCol['HD']['DocEntry'] = NULL;
			$i = 0;
			while($GetRST = mysqli_fetch_array($GetQRY)) {
				$SAPOnHand = 0;
				
				if($DocType == "OWAB") {
					/* GET ON HAND IN SAP (CASE OWAB) */
					$SAPSQL  = "SELECT TOP 1 SUM(P0.OnHand) AS 'SAPOnHand' FROM OITW P0 WHERE P0.ItemCode = '".$GetRST['ItemCode']."' AND P0.WhsCode = N'".conutf8($GetRST['WhsCode'])."'";
					$SAPQRY  = SAPSelect($SAPSQL);
					$SAPRST  = odbc_fetch_array($SAPQRY);

					/* GET PICKED IN EUROX FORCE */
					$PkSQL   = "SELECT SUM(T0.OpenQty) AS 'OpenQty' FROM picker_sodetail T0 LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry AND T0.DocType = T1.DocType WHERE T0.ItemCode = '".$GetRST['ItemCode']."' AND T0.WhsCode = '".conutf8($GetRST['WhsCode'])."' AND T1.StatusDoc BETWEEN 2 AND 8 LIMIT 1";
					$PkRST   = MySQLSelect($PkSQL);
					$OpenQty = $PkRST['OpenQty'];
					$SAPOnHand = $SAPRST['SAPOnHand'] - $OpenQty;
				} else {
					$SAPOnHand = 0;
					$OpenQty = 0;
				}

				if($arrCol['HD']['DocEntry'] == NULL) {
					$arrCol['HD']['DocEntry']   = $GetRST['DocEntry'];
					$arrCol['HD']['SODocNum']   = $GetRST['SODocNum'];
					$arrCol['HD']['CardCode']   = $GetRST['CardCode']." | ".$GetRST['CardName'];
					$arrCol['HD']['DocDate']    = date("Y-m-d",strtotime($GetRST['DocDate']));
					$arrCol['HD']['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
					$arrCol['HD']['SlpName']    = $GetRST['SlpName'];
					$arrCol['HD']['Comments']   = $GetRST['Comments'];
					$arrCol['FT']['OwnerName']  = $GetRST['OwnerName'];
				}
				$arrCol['BD_'.$i]['ItemCode']   = $GetRST['ItemCode'];
				$arrCol['BD_'.$i]['CodeBars']   = $GetRST['CodeBars'];
				$arrCol['BD_'.$i]['Dscription'] = $GetRST['Dscription'];
				$arrCol['BD_'.$i]['WhsCode']    = $GetRST['WhsCode'];
				$arrCol['BD_'.$i]['SAPOnHand']  = $SAPOnHand-$OpenQty;
				$arrCol['BD_'.$i]['Quantity']   = $GetRST['Quantity'];
				$arrCol['BD_'.$i]['UnitMsr']    = $GetRST['UnitMsr'];

				$i++;
			}
			$Rows = $i;
			$arrCol['Rows'] = $Rows;
		break;
	}
}

if($_GET['p'] == "CallWait") {
	$PickID = $_POST['pid'];
	$PickSQL = "SELECT T0.ID AS 'PickID', T1.* FROM picker_soheader T0 LEFT JOIN picker_sodetail T1 ON T0.SODocEntry = T1.DocEntry AND T0.DocType = T1.DocType LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode WHERE T0.ID = $PickID AND T1.BomItem = 0 ORDER BY T1.VisOrder ASC";
	$PickQRY = MySQLSelectX($PickSQL);

	$DocEntry = NULL;
	$i = 0;

	while($PickRST = mysqli_fetch_array($PickQRY)) {
		if($DocEntry == NULL) {
			$DocEntry = $PickRST['DocEntry'];
			$DocType  = $PickRST['DocType'];
			$arrCol['HD']['PickID']  = $PickRST['PickID'];
			$arrCol['HD']['DocType'] = $DocType;
		}

		if($PickRST['Status'] == 4) {
			$arrCol['BD_'.$i]['RowStatus'] = $PickRST['Status'].$PickRST['WaitOP'];
		} else {
			$arrCol['BD_'.$i]['RowStatus'] = $PickRST['Status'];
		}
		$arrCol['BD_'.$i]['TransID'] = $PickRST['ID'];
		$arrCol['BD_'.$i]['OpenQty'] = $PickRST['OpenQty'];
		
		$arrCol['BD_'.$i]['Remark']  = $PickRST['Remark'];
		$i++;
	}
	
	switch($DocType) {
		case "ORDR":
			$GetSQL =
				"SELECT
					/* SO HEADER */
					T0.DocEntry, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'SODocNum', T0.CardCode, T0.CardName,
					T3.SlpName, T0.DocDate, T0.DocDueDate, T0.Comments, T0.U_PONo, T0.AtcEntry,
					/* SO DETAIL */
					T1.VisOrder,T1.ItemCode, CASE WHEN (T1.SubCatNum = '' OR T1.SubCatNum IS NULL) THEN T1.CodeBars ELSE T1.SubCatNum END AS 'CodeBars', T1.Dscription, T1.WhsCode, T1.Quantity, T1.UnitMsr, 
					/* SO FOOTER */
					(T4.lastName+' '+T4.firstName) AS 'OwnerName'
				FROM ORDR T0
				LEFT JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
				LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
				LEFT JOIN OHEM T4 ON T0.OwnerCode = T4.empID
				WHERE T0.DocEntry = $DocEntry
				ORDER BY T1.VisOrder ASC";
			$GetQRY = SAPSelect($GetSQL);
			
			$arrCol['HD']['DocEntry'] = NULL;
			$i = 0;

			while($GetRST = odbc_fetch_array($GetQRY)) {
				/* GET ONHAND IN SAP */
				$SAPSQL  = "SELECT TOP 1 SUM(P0.OnHand) AS 'SAPOnHand' FROM OITW P0 WHERE P0.ItemCode = '".$GetRST['ItemCode']."' AND P0.WhsCode = N'".conutf8($GetRST['WhsCode'])."'";
				$SAPQRY  = SAPSelect($SAPSQL);
				$SAPRST  = odbc_fetch_array($SAPQRY);

				/* GET PICKED IN EUROX FORCE */
				$PkSQL   = "SELECT SUM(T0.OpenQty) AS 'OpenQty' FROM picker_sodetail T0 LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry AND T0.DocType = T1.DocType WHERE T0.ItemCode = '".$GetRST['ItemCode']."' AND T0.WhsCode = '".conutf8($GetRST['WhsCode'])."' AND T1.StatusDoc BETWEEN 2 AND 8 LIMIT 1";
				$PkRST   = MySQLSelect($PkSQL);
				$OpenQty = $PkRST['OpenQty'];
				if($arrCol['HD']['DocEntry'] == NULL) {
					$arrCol['HD']['DocEntry']   = $GetRST['DocEntry'];
					$arrCol['HD']['SODocNum']   = $GetRST['SODocNum'];
					$arrCol['HD']['CardCode']   = conutf8($GetRST['CardCode']." | ".$GetRST['CardName']);
					$arrCol['HD']['DocDate']    = date("Y-m-d",strtotime($GetRST['DocDate']));
					$arrCol['HD']['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
					$arrCol['HD']['SlpName']    = conutf8($GetRST['SlpName']);
					$arrCol['HD']['Comments']   = conutf8($GetRST['Comments']);
					$arrCol['FT']['OwnerName']  = conutf8($GetRST['OwnerName']);
				}
				$arrCol['BD_'.$i]['ItemCode']   = $GetRST['ItemCode'];
				$arrCol['BD_'.$i]['CodeBars']   = $GetRST['CodeBars'];
				$arrCol['BD_'.$i]['Dscription'] = conutf8($GetRST['Dscription']);
				$arrCol['BD_'.$i]['WhsCode']    = conutf8($GetRST['WhsCode']);
				$arrCol['BD_'.$i]['SAPOnHand']  = $SAPRST['SAPOnHand']-$OpenQty;
				$arrCol['BD_'.$i]['Quantity']   = $GetRST['Quantity'];
				$arrCol['BD_'.$i]['UnitMsr']    = conutf8($GetRST['UnitMsr']);

				$i++;
			}
			$Rows = $i;
			$arrCol['Rows'] = $Rows;
		break;
		case "OWAS":
		case "OWAB":
			$GetSQL =
				"SELECT
					/* WO HEADER */
					T0.DocEntry, T0.DocNum AS 'SODocNum', T0.CusCode AS 'CardCode', T0.CusName AS 'CardName',
					CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')') AS 'SlpName', DATE(T0.DateCreate) AS 'DocDate',
					DATE(T0.TimeContrac) AS 'DocDueDate', T0.Remark AS 'Comments', NULL AS 'U_PONo', NULL AS 'ActEntry',
					/* WO DETAIL */
					T1.lnNum AS 'VisOrder', T1.ItemCode, T1.BarCode AS 'CodeBars', T1.ItemName AS 'Dscription', T1.WhsCode, T1.Qty AS 'Quantity', T1.UnitMgr AS 'UnitMsr',
					CONCAT(T2.uName,' ',T2.uLastName) AS 'OwnerName'
				FROM OWAS T0
				LEFT JOIN WAS1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN users T2 ON T0.UserCreate = T2.uKey
				WHERE T0.DocEntry = $DocEntry
				ORDER BY T1.lnNum ASC";
			$GetQRY = MySQLSelectX($GetSQL);
			$arrCol['HD']['DocEntry'] = NULL;
			$i = 0;
			while($GetRST = mysqli_fetch_array($GetQRY)) {
				
				if($DocType == "OWAB") {
					/* GET ON HAND IN SAP (CASE OWAB) */
					$SAPSQL  = "SELECT TOP 1 SUM(P0.OnHand) AS 'SAPOnHand' FROM OITW P0 WHERE P0.ItemCode = '".$GetRST['ItemCode']."' AND P0.WhsCode = N'".conutf8($GetRST['WhsCode'])."'";
					$SAPQRY  = SAPSelect($SAPSQL);
					$SAPRST  = odbc_fetch_array($SAPQRY);

					/* GET PICKED IN EUROX FORCE */
					$PkSQL   = "SELECT SUM(T0.OpenQty) AS 'OpenQty' FROM picker_sodetail T0 LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry AND T0.DocType = T1.DocType WHERE T0.ItemCode = '".$GetRST['ItemCode']."' AND T0.WhsCode = '".conutf8($GetRST['WhsCode'])."' AND T1.StatusDoc BETWEEN 2 AND 8 LIMIT 1";
					$PkRST   = MySQLSelect($PkSQL);
					$OpenQty = $PkRST['OpenQty'];
					$SAPOnHand = $SAPRST['SAPOnHand'] - $OpenQty;
				} else {
					$SAPOnHand = 0;
				}

				if($arrCol['HD']['DocEntry'] == NULL) {
					$arrCol['HD']['DocEntry']   = $GetRST['DocEntry'];
					$arrCol['HD']['SODocNum']   = $GetRST['SODocNum'];
					$arrCol['HD']['CardCode']   = $GetRST['CardCode']." | ".$GetRST['CardName'];
					$arrCol['HD']['DocDate']    = date("Y-m-d",strtotime($GetRST['DocDate']));
					$arrCol['HD']['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
					$arrCol['HD']['SlpName']    = $GetRST['SlpName'];
					$arrCol['HD']['Comments']   = $GetRST['Comments'];
					$arrCol['FT']['OwnerName']  = $GetRST['OwnerName'];
				}
				$arrCol['BD_'.$i]['ItemCode']   = $GetRST['ItemCode'];
				$arrCol['BD_'.$i]['CodeBars']   = $GetRST['CodeBars'];
				$arrCol['BD_'.$i]['Dscription'] = $GetRST['Dscription'];
				$arrCol['BD_'.$i]['WhsCode']    = $GetRST['WhsCode'];
				$arrCol['BD_'.$i]['SAPOnHand']  = $SAPOnHand;
				$arrCol['BD_'.$i]['Quantity']   = $GetRST['Quantity'];
				$arrCol['BD_'.$i]['UnitMsr']    = $GetRST['UnitMsr'];

				$i++;
			}
			$Rows = $i;
			$arrCol['Rows'] = $Rows;
		break;
	}
}

if($_GET['p'] == "AddBill") {
	$PickID = $_POST['pid'];

	$DocSQL = "SELECT T0.SODocEntry, T0.DocType FROM picker_soheader T0 WHERE T0.ID = $PickID LIMIT 1";
	$DocRST = MySQLSelect($DocSQL);
	$DocEntry = $DocRST['SODocEntry'];
	$DocType  = $DocRST['DocType'];

	switch($DocType) {
		case "ORDR":
			/* HEADER */
			$SOHDSQL = 
				"SELECT TOP 1
					T0.DocEntry, T0.DocDate, T0.DocDueDate, (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.CardCode, T0.CardName, T0.Address, T0.Address2, T0.LicTradNum, T0.U_PONo,
					T5.U_ChqCond, T0.VatSum, T0.DocTotal, T0.U_SumInThai, T0.OwnerCode, T0.Comments, T4.U_Dim1,
					T2.LastName, T2.FirstName, T3.USER_CODE, T4.SlpName, T6.Name, T7.PymntGroup, T5.U_BillCond, T8.Name AS 'CntctName', T9.U_Name, T9.U_Address,
					T5.MailStrNo
				FROM ORDR T0
				LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
				LEFT JOIN OHEM T2 ON T0.OwnerCode = T2.empID
				LEFT JOIN OUSR T3 ON T0.UserSign = T3.USERID
				LEFT JOIN OSLP T4 ON T0.SlpCode = T4.SlpCode
				LEFT JOIN OCRD T5 ON T0.CardCode = T5.CardCode
				LEFT JOIN [dbo].[@TERITORY] T6 ON T5.U_Teritory = T6.Code
				LEFT JOIN OCTG T7 ON T0.GroupNum = T7.GroupNum
				LEFT JOIN OCPR T8 ON T0.CntctCode = T8.CntctCode
				LEFT JOIN [dbo].[@SHIPPINGTYPE] T9 ON T0.U_ShippingType = T9.Code
				WHERE T0.DocEntry = $DocEntry";
			$SOHDQRY = SAPSelect($SOHDSQL);
			$SOHDRST = odbc_fetch_array($SOHDQRY);
			$arrCol['HD']['PickID']      = $PickID;
			$arrCol['HD']['DocEntry']    = $SOHDRST['DocEntry'];
			$arrCol['HD']['DocType']     = "ORDR";
			$arrCol['HD']['CardCode']    = conutf8($SOHDRST['CardCode']." ".$SOHDRST['CardName']);
			$arrCol['HD']['DocNum']      = $SOHDRST['DocNum'];
			$arrCol['HD']['ShipAddress'] = conutf8($SOHDRST['Address2']);
			$arrCol['HD']['BillAddress'] = conutf8($SOHDRST['Address']);
			$arrCol['HD']['DocDate']     = date("d/m/Y",strtotime($SOHDRST['DocDate']));
			$arrCol['HD']['DocDueDate']  = date("d/m/Y",strtotime($SOHDRST['DocDueDate']));
			$arrCol['HD']['TaxID']       = $SOHDRST['LicTradNum'];
			$arrCol['HD']['SlpCode']     = conutf8($SOHDRST['SlpName']);
			$arrCol['HD']['UPONo']       = conutf8($SOHDRST['U_PONo']);
			$arrCol['HD']['UTeritory']   = conutf8($SOHDRST['Name']);
			$arrCol['HD']['PaymentType'] = conutf8($SOHDRST['U_ChqCond']);
			$arrCol['HD']['CreditGroup'] = conutf8($SOHDRST['PymntGroup']);
			$arrCol['HD']['LGType']      = conutf8($SOHDRST['U_Name']);
			$arrCol['HD']['LGAddress']   = conutf8($SOHDRST['U_Address']);
			$arrCol['HD']['Contact']     = conutf8($SOHDRST['CntctName']);
			$arrCol['HD']['BillCond']    = conutf8($SOHDRST['U_BillCond']);
			/* DETAIL */
			$SOBDSQL = "SELECT T0.* FROM RDR1 T0 WHERE T0.DocEntry = $DocEntry ORDER BY T0.VisOrder ASC";
			$SOBDQRY = SAPSelect($SOBDSQL);
			$i = 0;
			while($SOBDRST = odbc_fetch_array($SOBDQRY)) {
				if($SOBDRST['SubCatNum'] == "") { $CodeBars = $SOBDRST['CodeBars']; } else { $CodeBars = $SOBDRST['SubCatNum']; }
				if($SOBDRST['U_DiscP5'] != NULL AND $SOBDRST['U_DiscP5'] != "" AND $SOBDRST['U_DiscP5'] != 0.00) {
					$Discount = number_format($SOBDRST['U_DiscP1'],2)."%+".number_format($SOBDRST['U_DiscP2'],2)."%+".number_format($SOBDRST['U_DiscP3'],2)."%+".number_format($SOBDRST['U_DiscP4'],2)."%+".number_format($SOBDRST['U_DiscP5'],2)."%";
				} elseif($SOBDRST['U_DiscP4'] != NULL AND $SOBDRST['U_DiscP4'] != "" AND $SOBDRST['U_DiscP4'] != 0.00) {
					$Discount = number_format($SOBDRST['U_DiscP1'],2)."%+".number_format($SOBDRST['U_DiscP2'],2)."%+".number_format($SOBDRST['U_DiscP3'],2)."%+".number_format($SOBDRST['U_DiscP4'],2)."%";
				} elseif($SOBDRST['U_DiscP3'] != NULL AND $SOBDRST['U_DiscP3'] != "" AND $SOBDRST['U_DiscP3'] != 0.00) {
					$Discount = number_format($SOBDRST['U_DiscP1'],2)."%+".number_format($SOBDRST['U_DiscP2'],2)."%+".number_format($SOBDRST['U_DiscP3'],2)."%";
				} elseif($SOBDRST['U_DiscP2'] != NULL AND $SOBDRST['U_DiscP2'] != "" AND $SOBDRST['U_DiscP2'] != 0.00) {
					$Discount = number_format($SOBDRST['U_DiscP1'],2)."%+".number_format($SOBDRST['U_DiscP2'],2)."%";
				} elseif($SOBDRST['U_DiscP1'] != NULL AND $SOBDRST['U_DiscP1'] != "" AND $SOBDRST['U_DiscP1'] != 0.00) {
					$Discount = number_format($SOBDRST['U_DiscP1'],2)."%";
				} else {
					$Discount = NULL;
				}

				$arrCol['BD_'.$i]['VisOrder']   = $SOBDRST['VisOrder']+1;
				$arrCol['BD_'.$i]['ItemName']   = $SOBDRST['ItemCode']." ".$CodeBars." ".conutf8($SOBDRST['WhsCode'])." ".conutf8($SOBDRST['Dscription']);
				$arrCol['BD_'.$i]['unitMsr']    = conutf8($SOBDRST['unitMsr']);
				$arrCol['BD_'.$i]['PriceBefDi'] = $SOBDRST['PriceBefDi'];
				$arrCol['BD_'.$i]['Price']      = $SOBDRST['Price'];
				$arrCol['BD_'.$i]['Discount'] = $Discount;
				$i++;
			}
			$arrCol['Rows'] = $i;

			/* PICKED DETAIL */
			$PKSOSQL = "SELECT T0.OpenQty, T0.Status, T0.Remark, T1.TablePacking, CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')') AS 'PickerName' FROM picker_sodetail T0 LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry AND T0.DocType = T1.DocType LEFT JOIN users T2 ON T1.UkeyPicker = T2.uKey WHERE T0.DocEntry = $DocEntry AND T0.DocType = '$DocType' AND T0.BomItem = 0 ORDER BY T0.VisOrder";
			// echo $PKSOSQL;
			$PKSOQRY = MySQLSelectX($PKSOSQL);
			$i = 0;
			while($PKSORST = mysqli_fetch_array($PKSOQRY)) {
				$arrCol['BD_'.$i]['Quantity']  = $PKSORST['OpenQty'];
				$arrCol['BD_'.$i]['RowStatus'] = $PKSORST['Status'];
				$arrCol['BD_'.$i]['Remark']    = $PKSORST['Remark'];
				$arrCol['HD']['PickerName']    = $PKSORST['PickerName'];
				$arrCol['HD']['PackerName']    = $PKSORST['TablePacking'];
				$i++;
			}

			$arrCol['FT']['Comments'] = conutf8($SOHDRST['Comments']);
			$arrCol['FT']['VatSum']   = $SOHDRST['VatSum'];

			/* ATTACHMENT */
			$ChkAtcSQL = "SELECT TOP 1 T0.DocEntry, T0.AtcEntry FROM ORDR T0 WHERE T0.DocEntry = $DocEntry AND T0.AtcEntry IS NOT NULL";
			if(ChkRowSAP($ChkAtcSQL) > 0) {
				/* GET SAP */
				$ChkAtcQRY = SAPSelect($ChkAtcSQL);
				$ChkAtcRST = odbc_fetch_array($ChkAtcQRY);
				$AtcEntry  = $ChkAtcRST['AtcEntry'];
		
				$AttSQL  = "SELECT T0.trgtPath, T0.FileName,T0.FileExt FROM ATC1 T0 WHERE T0.AbsEntry = $AtcEntry ORDER BY T0.Line ASC";
				$AttRows = ChkRowSAP($AttSQL);
				if($AttRows == 0) {
					$arrCol['AttRows'] = 0;
				} else {
					$arrCol['AttRows'] = $AttRows;
		
					$AttQRY = SAPSelect($AttSQL);
					$i = 0;
					while($AttRST = odbc_fetch_array($AttQRY)) {
						$arrCol['AT_'.$i]['FileName'] = conutf8($AttRST['FileName'].".".$AttRST['FileExt']);
						$arrCol['AT_'.$i]['FilePath'] = "file:".str_replace(" ","%20",str_replace("\\","/",$AttRST['trgtPath']))."/".conutf8(str_replace(" ","%20",$AttRST['FileName']).".".$AttRST['FileExt']);
						$i++;
					}
				}
			} else {
				/* GET EUROX FORCE */
				$AttSQL =
					"SELECT
						T0.VisOrder, T0.FileOriName, T0.FileDirName, T0.FileExt
					FROM order_attach T0
					LEFT JOIN order_header T1 ON T0.DocEntry = T1.DocEntry
					WHERE T1.ImportEntry = $DocEntry AND T0.FileStatus = 'A'";
				$AttRows = ChkRowDB($AttSQL);
				if($AttRows == 0) {
					$arrCol['AttRows'] = 0;
				} else {
					$arrCol['AttRows'] = $AttRows;
					
					$AttQRY = MySQLSelectX($AttSQL);
					$i = 0;
					while($AttRST = mysqli_fetch_array($AttQRY)) {
						$arrCol['AT_'.$i]['FileName'] = $AttRST['FileOriName'].".".$AttRST['FileExt'];
						$arrCol['AT_'.$i]['FilePath'] = "../FileAttach/SO/".$AttRST['FileDirName'].".".$AttRST['FileExt'];
						$i++;
					}
				}
			}



		break;
		case "OWAS":
		case "OWAB":
			$WOHDSQL = 
			"SELECT
				T0.DocEntry, DATE(T0.DateCreate) AS 'DocDate', DATE(T0.TimeContrac) AS 'DocDueDate', T0.DocNum AS 'DocNum', T0.CusCode AS 'CardCode', T0.CusName AS 'CardName',
				T0.CusAddress AS 'Address', T0.CusAddress AS 'Address2', NULL AS 'LicTradNum', NULL AS 'U_PONo', T0.TypeOrder,
				NULL AS 'U_ChqCond', 0 AS 'VatSum', 0 AS 'DocTotal', NULL AS 'U_SumInThai', 0 AS 'OwnerCode', T0.Remark AS 'Comments', T0.TeamCode AS 'U_Dim1',
				CONCAT(T2.uName,' ',T2.uLastName) AS 'SlpName', NULL AS 'Name', NULL AS 'PymntGroup', NULL AS 'U_BillCond', NULL AS 'CntctName', T0.LogiName AS 'U_Name', NULL AS 'U_Address'
			FROM OWAS T0
			LEFT JOIN WAS1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN users T2 ON T0.UserCreate = T2.uKey
			WHERE T0.DocEntry = $DocEntry";
			$WOHDRST = MySQLSelect($WOHDSQL);
			$DocType = "OWA".$WOHDRST['TypeOrder'];
			$arrCol['HD']['PickID']      = $PickID;
			$arrCol['HD']['DocEntry']    = $WOHDRST['DocEntry'];
			$arrCol['HD']['DocType']     = $DocType;
			$arrCol['HD']['CardCode']    = $WOHDRST['CardCode']." ".$WOHDRST['CardName'];
			$arrCol['HD']['DocNum']      = $WOHDRST['DocNum'];
			$arrCol['HD']['ShipAddress'] = $WOHDRST['Address2'];
			$arrCol['HD']['BillAddress'] = $WOHDRST['Address'];
			$arrCol['HD']['DocDate']     = date("d/m/Y",strtotime($WOHDRST['DocDate']));
			$arrCol['HD']['DocDueDate']  = date("d/m/Y",strtotime($WOHDRST['DocDueDate']));
			$arrCol['HD']['TaxID']       = $WOHDRST['LicTradNum'];
			$arrCol['HD']['SlpCode']     = $WOHDRST['SlpName'];
			$arrCol['HD']['UPONo']       = $WOHDRST['U_PONo'];
			$arrCol['HD']['UTeritory']   = $WOHDRST['Name'];
			$arrCol['HD']['PaymentType'] = $WOHDRST['U_ChqCond'];
			$arrCol['HD']['CreditGroup'] = $WOHDRST['PymntGroup'];
			$arrCol['HD']['LGType']      = $WOHDRST['U_Name'];
			$arrCol['HD']['LGAddress']   = $WOHDRST['U_Address'];
			$arrCol['HD']['Contact']     = $WOHDRST['CntctName'];
			$arrCol['HD']['BillCond']    = $WOHDRST['U_BillCond'];

			$WOBDSQL = "SELECT T0.* FROM WAS1 T0 WHERE T0.DocEntry = $DocEntry ORDER BY T0.lnNum ASC";
			$WOBDQRY = MySQLSelectX($WOBDSQL);
			$i = 0;
			while($WOBDRST = mysqli_fetch_array($WOBDQRY)) {
				$arrCol['BD_'.$i]['VisOrder']   = $WOBDRST['lnNum'];
				$arrCol['BD_'.$i]['ItemName']   = $WOBDRST['ItemCode']." ".$WOBDRST['BarCode']." ".$WOBDRST['WhsCode']." ".$WOBDRST['ItemName'];
				$arrCol['BD_'.$i]['unitMsr']    = $WOBDRST['UnitMgr'];
				$arrCol['BD_'.$i]['PriceBefDi'] = 0;
				$arrCol['BD_'.$i]['Price']      = 0;
				$i++;
			}
			$arrCol['Rows'] = $i;

			/* PICKED DETAIL */
			$PKSOSQL = "SELECT T0.OpenQty, T0.Status, T0.Remark, T1.TablePacking, CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')') AS 'PickerName' FROM picker_sodetail T0 LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry AND T0.DocType = T1.DocType LEFT JOIN users T2 ON T1.UkeyPicker = T2.uKey WHERE T0.DocEntry = $DocEntry AND T0.DocType LIKE 'OWA%' AND T0.BomItem = 0 ORDER BY T0.VisOrder";
			// echo $PKSOSQL;
			$PKSOQRY = MySQLSelectX($PKSOSQL);
			$i = 0;
			while($PKSORST = mysqli_fetch_array($PKSOQRY)) {
				$arrCol['BD_'.$i]['Quantity']  = $PKSORST['OpenQty'];
				$arrCol['BD_'.$i]['RowStatus'] = $PKSORST['Status'];
				$arrCol['BD_'.$i]['Remark']    = $PKSORST['Remark'];
				$arrCol['HD']['PickerName']    = $PKSORST['PickerName'];
				$arrCol['HD']['PackerName']    = $PKSORST['TablePacking'];
				$i++;
			}
			$arrCol['FT']['Comments'] = $WOHDRST['Comments'];
			$arrCol['FT']['VatSum']   = $WOHDRST['VatSum'];

			/* ATTACHMENT */



		break;
	}
}

if($_GET['p'] == "UpdateSO") {
	$PickID     = $_POST['pid'];
	$DocDueDate = $_POST['ddd'];
	$PickerName = $_POST['pkn'];
	$TablePack  = $_POST['tpk'];
	$UpdateUkey = $_SESSION['ukey'];
	$UpdateSQL = "UPDATE picker_soheader SET DocDueDate = '$DocDueDate', UkeyPicker = '$PickerName', TablePacking = $TablePack, LastUkey = '$UpdateUkey', LastUpdate = NOW() WHERE ID = $PickID";
	MySQLUpdate($UpdateSQL);
}

if($_GET['p'] == "UpdateWO") {
	$PickID     = $_POST['pid'];
	$DocDueDate = $_POST['ddd'];
	$PickerName = $_POST['pkn'];
	$TablePack  = $_POST['tpk'];
	$UpdateUkey = $_SESSION['ukey'];
	$UpdateSQL = "UPDATE picker_soheader SET DocDueDate = '$DocDueDate', UkeyPicker = '$PickerName', TablePacking = $TablePack, LastUkey = '$UpdateUkey', LastUpdate = NOW() WHERE ID = $PickID";
	MySQLUpdate($UpdateSQL);
}

if($_GET['p'] == "UpdateStatus") {
	if($_POST['pid'] != "") {
		$DocEntry = $_POST['pid'];
	} elseif($_POST['wid'] != "") {
		$DocEntry = $_POST['wid'];
	} else {
		$DocEntry = $_POST['cid'];
	}
	$StatusDoc  = $_POST['std'];
	$UpdateUkey = $_SESSION['ukey'];
	
	$UpdateSQL = "UPDATE picker_soheader SET StatusDoc = $StatusDoc, LastUkey = '$UpdateUkey', LastUpdate = NOW() WHERE ID = $DocEntry";
	MySQLUpdate($UpdateSQL);
}

if($_GET['p'] == "CallWO") {
	$DocEntry = $_POST['DocEntry'];
	$GetSQL =
		"SELECT
			T0.DocEntry, T0.DocNum AS 'WODocNum', T0.CusCode AS 'CardCode', T0.CusName AS 'CardName', T0.TypeOrder AS 'DocType',
			CONCAT(T1.uName,' ',T1.uLastName,' (',T1.uNickName,')') AS 'SlpName', DATE(T0.DateCreate) AS 'DocDate', DATE(T0.TimeContrac) AS 'DocDueDate',
			T0.Remark AS 'Comments',
			T2.ItemCode, T2.BarCode, T2.ItemName, T2.UnitMgr, T2.WhsCode, T2.Qty, T2.Remark
		FROM OWAS T0
		LEFT JOIN users T1 ON T0.UserCreate = T1.uKey
		LEFT JOIN WAS1 T2 ON T0.DocEntry = T2.DocEntry
		WHERE T0.DocEntry = $DocEntry";
	$GetQRY = MySQLSelectX($GetSQL);

	$arrCol['HD']['DocEntry'] = NULL;
	
	$i = 0;
	while($GetRST = mysqli_fetch_array($GetQRY)) {
		if($arrCol['HD']['DocEntry'] == NULL) {
			$arrCol['HD']['DocEntry'] = $GetRST['DocEntry'];
			$arrCol['HD']['WODocNum'] = $GetRST['WODocNum'];
			$arrCol['HD']['CardCode'] = $GetRST['CardCode']." | ".$GetRST['CardName'];
			$arrCol['HD']['SlpName']  = $GetRST['SlpName'];
			$arrCol['HD']['DocDate']  = date("Y-m-d",strtotime($GetRST['DocDate']));
			if($GetRST['DocDueDate'] == NULL) {
				$arrCol['HD']['DocDueDate'] = date("Y-m-d");
			} else {
				$arrCol['HD']['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
			}
			$arrCol['HD']['Comments'] = $GetRST['Comments'];
			$DocType = $GetRST['DocType'];
			$arrCol['HD']['DocType'] = $DocType;
		}

		$arrCol['BD_'.$i]['ItemCode'] = $GetRST['ItemCode'];
		$arrCol['BD_'.$i]['CodeBars'] = $GetRST['BarCode'];
		$arrCol['BD_'.$i]['ItemName'] = $GetRST['ItemName'];
		$arrCol['BD_'.$i]['UnitMsr']  = $GetRST['UnitMgr'];
		$arrCol['BD_'.$i]['WhsCode']  = $GetRST['WhsCode'];
		$arrCol['BD_'.$i]['Quantity'] = $GetRST['Qty'];
		$arrCol['BD_'.$i]['Remark']   = $GetRST['Remark'];
		$i++;

	}
	$Rows = $i;
	$arrCol['Rows'] = $i;

	/* GET PICKED NAME AND TABLE */
	if($DocType != "R") {
		$PKSQL = "SELECT T0.ID, T0.UKeyPicker, T0.TablePacking FROM picker_soheader T0 WHERE T0.SODocEntry = $DocEntry AND T0.DocType LIKE 'OWA%' LIMIT 1";
		$PKRST = MySQLSelect($PKSQL);
		$arrCol['HD']['PickID']    = $PKRST['ID'];
		$arrCol['HD']['PickUkey']  = $PKRST['UKeyPicker'];
		$arrCol['HD']['TablePack'] = $PKRST['TablePacking'];
	} else {
		$arrCol['HD']['PickID']    = NULL;
		$arrCol['HD']['PickUkey']  = NULL;
		$arrCol['HD']['TablePack'] = NULL;
	}

	/* ATTACHMENT */
	$AttSQL = "SELECT T0.* FROM WAS2 T0 WHERE T0.DocEntry = $DocEntry";
	$Rows   = ChkRowDB($AttSQL);
	if($Rows == 0) {
		$arrCol['AttRows'] = 0;
	} else {
		$AttQRY = MySQLSelectX($AttSQL);
		$i = 0;
		while($AttRST = mysqli_fetch_array($AttQRY)) {
			$arrCol['AT_'.$i]['FileName'] = $AttRST['FileName'];
			$arrCol['AT_'.$i]['NameShow'] = $AttRST['NameShow'];
			$i++;
		}
		$AttRows = $i;
		$arrCol['AttRows'] = $AttRows;
	}
}

if($_GET['p'] == "CallIV") {
	$DocEntry = $_POST['DocEntry'];
	$DocType  = $_POST['DocType'];

	switch($DocType) {
		case "OINV":
			$TBNAME = array("OINV","INV1");
		break;
		case "ODLN":
			$TBNAME = array("ODLN","DLN1");
			break;
		case 'ORIN' :
			$TBNAME = array("ORIN","RIN1");
		break;
	}

	$GetSQL =
		"SELECT
			/* IV HEADER */
			T0.DocEntry, (ISNULL(T2.BeginStr,'IV-')+CAST(T0.DocNum AS VARCHAR)) AS 'IVDocNum', T0.CardCode, T0.CardName,
			T3.SlpName, T0.DocDate, T0.DocDueDate, T0.Comments, T0.U_PONo,
			/* IV DETAIL */
			T1.BaseEntry, T1.BaseType, T1.VisOrder, T1.ItemCode, CASE WHEN (T1.SubCatNum = '' OR T1.SubCatNum IS NULL) THEN T1.CodeBars ELSE T1.SubCatNum END AS 'CodeBars', T1.Dscription, T1.WhsCode, T1.Quantity, T1.UnitMsr,
			T1.PriceBefDi,
			T1.DiscPrcnt, T1.U_DiscP1, T1.U_DiscP2, T1.U_DiscP3, T1.U_DiscP4, T1.U_DiscP5,
			T1.LineTotal,
			/* IV FOOTER */
			T0.DocTotal, T0.VatSum, (T4.lastname+' '+T4.firstname) AS 'OwnerName'
		FROM $TBNAME[0] T0
		LEFT JOIN $TBNAME[1] T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
		LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
		LEFT JOIN OHEM T4 ON T0.OwnerCode = T4.empID
		WHERE T0.DocEntry = $DocEntry
		ORDER BY T1.VisOrder ASC";
	$GetQRY = SAPSelect($GetSQL);
	// echo $GetSQL;

	$arrCol['HD']['DocEntry'] = NULL;

	$i = 0;
	while($GetRST = odbc_fetch_array($GetQRY)) {
		if($arrCol['HD']['DocEntry'] == NULL) {
			$arrCol['HD']['DocEntry']   = $DocEntry;
			$arrCol['HD']['DocType']    = $DocType;
			$arrCol['HD']['IVDocNum']   = $GetRST['IVDocNum'];
			$arrCol['HD']['CardCode']   = conutf8($GetRST['CardCode']." | ".$GetRST['CardName']);
			$arrCol['HD']['DocDate']    = date("Y-m-d",strtotime($GetRST['DocDate']));
			$arrCol['HD']['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
			$arrCol['HD']['SlpName']    = conutf8($GetRST['SlpName']);
			$arrCol['HD']['Comments']   = conutf8($GetRST['Comments']);
			$arrCol['HD']['U_PONo']     = conutf8($GetRST['U_PONo']);
			$arrCol['FT']['DocTotal']   = $GetRST['DocTotal'];
			$arrCol['FT']['VatSum']     = $GetRST['VatSum'];
			$arrCol['FT']['OwnerName']  = conutf8($GetRST['OwnerName']);

			$BaseEntry = $GetRST['BaseEntry'];
			$BaseType  = $GetRST['BaseType'];
		}
		$arrCol['BD_'.$i]['VisOrder']   = $GetRST['VisOrder'];
		$arrCol['BD_'.$i]['ItemCode']   = $GetRST['ItemCode'];
		$arrCol['BD_'.$i]['CodeBars']   = $GetRST['CodeBars'];
		$arrCol['BD_'.$i]['Dscription'] = conutf8($GetRST['Dscription']);
		$arrCol['BD_'.$i]['WhsCode']    = conutf8($GetRST['WhsCode']);
		$arrCol['BD_'.$i]['Quantity']   = $GetRST['Quantity'];
		$arrCol['BD_'.$i]['UnitMsr']    = conutf8($GetRST['UnitMsr']);
		$arrCol['BD_'.$i]['PriceBefDi'] = $GetRST['PriceBefDi'];
		$arrCol['BD_'.$i]['LineTotal']  = $GetRST['LineTotal'];

		if ($GetRST['U_DiscP5'] != NULL and $GetRST['U_DiscP5'] != "" and $GetRST['U_DiscP5'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%+" . number_format($GetRST['U_DiscP4'], 2) . "%+" . number_format($GetRST['U_DiscP5'], 2) . "%";
        } elseif ($GetRST['U_DiscP4'] != NULL and $GetRST['U_DiscP4'] != "" and $GetRST['U_DiscP4'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%+" . number_format($GetRST['U_DiscP4'], 2) . "%";
        } elseif ($GetRST['U_DiscP3'] != NULL and $GetRST['U_DiscP3'] != "" and $GetRST['U_DiscP3'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%";
        } elseif ($GetRST['U_DiscP2'] != NULL and $GetRST['U_DiscP2'] != "" and $GetRST['U_DiscP2'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%";
        } elseif ($GetRST['U_DiscP1'] != NULL and $GetRST['U_DiscP1'] != "" and $GetRST['U_DiscP1'] != 0.00) {
            $Discount = number_format($GetRST['U_DiscP1'], 2) . "%";
        } else {
            $Discount = NULL;
        }
		$arrCol['BD_'.$i]['Discount']   = $Discount;
		$i++;
	}
	$Rows = $i;
	$arrCol['Rows'] = $Rows;

	/* ATTACHMENT */
	if ($DocType != 'ORIN'){
	$ChkAtcSQL = "SELECT TOP 1 T0.DocEntry, T0.AtcEntry FROM ORDR T0 WHERE T0.DocEntry = $BaseEntry AND T0.AtcEntry IS NOT NULL";
	
		if(ChkRowSAP($ChkAtcSQL) > 0) {
			/* GET SAP */
			$ChkAtcQRY = SAPSelect($ChkAtcSQL);
			$ChkAtcRST = odbc_fetch_array($ChkAtcQRY);
			$AtcEntry  = $ChkAtcRST['AtcEntry'];

			$AttSQL  = "SELECT T0.trgtPath, T0.FileName,T0.FileExt FROM ATC1 T0 WHERE T0.AbsEntry = $AtcEntry ORDER BY T0.Line ASC";
			$AttRows = ChkRowSAP($AttSQL);
			
			if($AttRows == 0) {
				$arrCol['AttRows'] = 0;
			} else {
				$arrCol['AttRows'] = $AttRows;

				$AttQRY = SAPSelect($AttSQL);
				$i = 0;
				while($AttRST = odbc_fetch_array($AttQRY)) {
					$arrCol['AT_'.$i]['FileName'] = conutf8($AttRST['FileName'].".".$AttRST['FileExt']);
					$arrCol['AT_'.$i]['FilePath'] = "file:".str_replace(" ","%20",str_replace("\\","/",$AttRST['trgtPath']))."/".conutf8(str_replace(" ","%20",$AttRST['FileName']).".".$AttRST['FileExt']);
					$i++;
				}
			}
		} else {
			/* GET EUROX FORCE */
			$AttSQL =
				"SELECT
					T0.VisOrder, T0.FileOriName, T0.FileDirName, T0.FileExt
				FROM order_attach T0
				LEFT JOIN order_header T1 ON T0.DocEntry = T1.DocEntry
				WHERE T1.ImportEntry = $BaseEntry AND T0.FileStatus = 'A'";
			// echo $AttSQL;
			$AttRows = ChkRowDB($AttSQL);
			if($AttRows == 0) {
				$arrCol['AttRows'] = 0;
			} else {
				$arrCol['AttRows'] = $AttRows;
				
				$AttQRY = MySQLSelectX($AttSQL);
				$i = 0;
				while($AttRST = mysqli_fetch_array($AttQRY)) {
					$arrCol['AT_'.$i]['FileName'] = $AttRST['FileOriName'].".".$AttRST['FileExt'];
					$arrCol['AT_'.$i]['FilePath'] = "../FileAttach/SO/".$AttRST['FileDirName'].".".$AttRST['FileExt'];
					$i++;
				}
			}
		}
	}

}

if($_GET['p'] == "ChkOnHand") {
	$ItemCode = $_POST['ItemCode'];
	$WhsCode  = $_POST['WhsCode'];
	$SapSQL   = "SELECT TOP 1 T0.ItemCode, T1.ItemName, T0.OnHand AS 'OnHand', T1.SalUnitMsr, T1.InvntryUom FROM OITW T0 LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode WHERE (T0.ItemCode = '$ItemCode' AND T0.WhsCode = '$WhsCode')";
	$Rows     = ChkRowSAP($SapSQL);
	if($Rows > 0) {
		$SapQRY   = SAPSelect($SapSQL);
		$SapRST   = odbc_fetch_array($SapQRY);
		/* GET SAP ONHAND */

		$arrCol['HD']['ItemCode']   = $SapRST['ItemCode'];
		$arrCol['HD']['ItemName']   = conutf8($SapRST['ItemName']);
		$arrCol['HD']['UnitMsr']    = conutf8($SapRST['SalUnitMsr']);
		$arrCol['HD']['SAPOnHand']  = $SapRST['OnHand'];
		$arrCol['HD']['InvntryUoM'] = conutf8($SapRST['InvntryUom']);
	}

	/* Get Picked S/O */
	$PckSQL = 
		"SELECT
			T0.DocNum, T0.DocDate, T0.CardCode, T0.CardName, SUM(T1.OpenQty) AS 'OpenQty'
		FROM picker_soheader T0
		LEFT JOIN picker_sodetail T1 ON T0.SODocEntry = T1.DocEntry AND T0.DocType = T1.DocType
		WHERE T1.ItemCode = '$ItemCode' AND T1.WhsCode = '$WhsCode' AND T0.StatusDoc BETWEEN 2 AND 8
		GROUP BY T0.DocNum, T0.DocDate, T0.CardCode, T0.CardName
		ORDER BY T0.DocNum";
	$PckROW = ChkRowDB($PckSQL);
	if($PckROW == 0) {
		$arrCol['PickRow'] = 0;
		$arrCol['FT']['SumQty'] = 0;
		$arrCol['FT']['SumTotal'] = 0;
	} else {
		$PckQRY = MySQLSelectX($PckSQL);
		$row    = 0;
		$SumQty = 0;

		while($PckRST = mysqli_fetch_array($PckQRY)) {
			$arrCol['BD_'.$row]['DocNum']   = $PckRST['DocNum'];
			$arrCol['BD_'.$row]['DocDate']  = date("d/m/Y",strtotime($PckRST['DocDate']));
			$arrCol['BD_'.$row]['CardCode'] = $PckRST['CardCode']." | ".$PckRST['CardName'];
			$arrCol['BD_'.$row]['OpenQty']  = $PckRST['OpenQty'];
			$SumQty = $SumQty + $PckRST['OpenQty'];
			$row++;
		}

		$arrCol['PickRow'] = $row;
		$arrCol['FT']['SumQty'] = $SumQty;
		$arrCol['FT']['SumTotal'] = $SapRST['OnHand'] - $SumQty;
	}

	/* GET ALL SAP ONHAND */
	$AllSQL = "SELECT T0.WhsCode, T1.WhsName, T0.OnHand FROM OITW T0 LEFT JOIN OWHS T1 ON T0.WhsCode = T1.WhsCode WHERE T0.ItemCode = '$ItemCode' AND T0.OnHand > 0";
	$AllRow = ChkRowSAP($AllSQL);
	if($AllRow == 0) {
		$WhsAll  = 0;
		$arrCol['SAPRow'] = 0;
	} else {
		$row = 0;
		$AllQRY = SAPSelect($AllSQL);
		$WhsAll  = 0;
		$WhsChk  = array('KB2','KSY','KSM','KBM','KB4');
		while($AllRST = odbc_fetch_array($AllQRY)) {
			$arrCol['SAP_'.$row]['WhsCode'] = conutf8($AllRST['WhsCode']);
			$arrCol['SAP_'.$row]['WhsName'] = conutf8($AllRST['WhsName']);
			$arrCol['SAP_'.$row]['OnHand']  = $AllRST['OnHand'];
			$row++;
			if(in_array(conutf8($AllRST['WhsCode']), $WhsChk, TRUE)) {
				$WhsAll = $WhsAll + $AllRST['OnHand'];
			}
		}
		$arrCol['SAPRow'] = $row;
	}

	$arrCol['Qta']['SAP'] = "-";
	$arrCol['Qta']['CEN'] = "-";
	$arrCol['Qta']['MT1'] = "-";
	$arrCol['Qta']['MT2'] = "-";
	$arrCol['Qta']['TTC'] = "-";
	$arrCol['Qta']['OUL'] = "-";
	$arrCol['Qta']['ONL'] = "-";

	if($WhsAll > 0) {
		$arrCol['Qta']['SAP'] = number_format($WhsAll,0);
		$arrCol['Qta']['CEN'] = "-";
		$arrCol['Qta']['MT1'] = "-";
		$arrCol['Qta']['MT2'] = "-";
		$arrCol['Qta']['TTC'] = "-";
		$arrCol['Qta']['OUL'] = "-";
		$arrCol['Qta']['ONL'] = "-";

		$QtaSQL = 
			"SELECT
				T0.ItemCode,
				SUM(CASE WHEN T0.CH = 'MT1' THEN T0.OnHand ELSE 0 END) AS 'MT1',
				SUM(CASE WHEN T0.CH = 'MT2' THEN T0.OnHand ELSE 0 END) AS 'MT2',
				SUM(CASE WHEN T0.CH = 'TTC' THEN T0.OnHand ELSE 0 END) AS 'TTC',
				SUM(CASE WHEN T0.CH = 'OUL' THEN T0.OnHand ELSE 0 END) AS 'OUL',
				SUM(CASE WHEN T0.CH = 'ONL' THEN T0.OnHand ELSE 0 END) AS 'ONL'
			FROM whsquota T0
			WHERE (T0.ItemCode = '$ItemCode')
			GROUP BY T0.ItemCode";
		if(ChkRowDB($QtaSQL) > 0) {
			$QtaRST = MySQLSelect($QtaSQL);
			
			$QtaSAP = $WhsAll;
			$QtaCEN = $QtaSAP - ($QtaRST['MT1'] + $QtaRST['MT2'] + $QtaRST['TTC'] + $QtaRST['OUL'] + $QtaRST['ONL']);

			if($QtaCEN != 0) { $arrCol['Qta']['CEN'] = number_format($QtaCEN,0); }

			if($QtaRST['MT1'] != 0) { $arrCol['Qta']['MT1'] = number_format($QtaRST['MT1'],0); }
			if($QtaRST['MT2'] != 0) { $arrCol['Qta']['MT2'] = number_format($QtaRST['MT2'],0); }
			if($QtaRST['TTC'] != 0) { $arrCol['Qta']['TTC'] = number_format($QtaRST['TTC'],0); }
			if($QtaRST['OUL'] != 0) { $arrCol['Qta']['OUL'] = number_format($QtaRST['OUL'],0); }
			if($QtaRST['ONL'] != 0) { $arrCol['Qta']['ONL'] = number_format($QtaRST['ONL'],0); }

			
		}
	}
}

if($_GET['p'] == "CutSO") {
	$PickID       = $_POST['PickID'];
	$Rows         = $_POST['TotalRow'];
	$UpdateUkey   = $_SESSION['ukey'];

	$HD_StatusDoc = 5;

	for($i = 0; $i < $Rows; $i++) {
		$CutOpt  = $_POST['RowStatus_'.$i];
		$Remark  = $_POST['Remark_'.$i];
		$TransID = $_POST['TransID_'.$i];
		switch($CutOpt) {
			case "41":
			case "42":
			case "43":
			case "44":
			case "45":
			case "46":
				$HD_StatusDoc = 6;
				$RowStatus    = 4;
				$WaitOP       = substr($CutOpt,1,1);
			break;
			default:
				$RowStatus    = $CutOpt;
				$WaitOP       = 0;
			break;
		}
		$UpdateSQL = "UPDATE picker_sodetail SET Remark = '$Remark', Status = $RowStatus, WaitOP = $WaitOP WHERE ID = $TransID";
		MySQLUpdate($UpdateSQL);
	}

	$UpdateSQL = "UPDATE picker_soheader SET StatusDoc = $HD_StatusDoc, UkeyCUT2 = '$UpdateUkey', TimeCUT2 = NOW(), LastUkey = '$UpdateUkey', LastUpdate = NOW() WHERE ID = $PickID";
	MySQLUpdate($UpdateSQL);

}

if($_GET['p'] == "CancelSO") {
	$PickID = $_POST['pid'];
	$InfoSQL = "SELECT T0.ID, T0.SODocEntry, T0.DocType FROM picker_soheader T0 WHERE T0.ID = $PickID LIMIT 1";
	$InfoRST = MySQLSelect($InfoSQL);

	$DocEntry = $InfoRST['SODocEntry'];
	$DocType  = $InfoRST['DocType'];

	$UpdateUkey = $_SESSION['ukey'];
	
	$UpdateSQL = ["UPDATE picker_soheader SET StatusDoc = 0, LastUkey = '$UpdateUkey', LastUpdate = NOW() WHERE ID = $PickID","UPDATE picker_sodetail SET Status = 3 WHERE DocEntry = $DocEntry AND DocType = '$DocType'"];
	for($i = 0; $i < count($UpdateSQL); $i++) {
		MySQLUpdate($UpdateSQL[$i]);
	}
}

if($_GET['p'] == "ReturnPick") {
	$PickID = $_POST['pid'];
	$DeptCode   = $_SESSION['DeptCode'];
	if($DeptCode == "DP011" || $DeptCode == "DP002") {
		$arrCol['ChkStatus'] = "SUCCESS";
		$UpdateUkey = $_SESSION['ukey'];
		$UpdateSQL = "UPDATE picker_soheader SET StatusDoc = 6, UkeyOpen = '$UpdateUkey', DateOpen = NOW(), LastUkey = '$UpdateUkey', LastUpdate = NOW() WHERE ID = $PickID";
		MySQLUpdate($UpdateSQL);
	} else {
		$arrCol['ChkStatus'] = "ERR::NOPERMISSION";
	}
}

if($_GET['p'] == "ConfirmBill") {
	$DeptCode   = $_SESSION['DeptCode'];
	if($DeptCode == "DP011" || $DeptCode == "DP002") {
		$PickID     = $_POST['PickID'];
		$SODocEntry = $_POST['DocEntry'];
		$SODocType  = $_POST['DocType'];
		
		switch($SODocType) {
			case "ORDR":
				$ChkBillSQL = "SELECT DISTINCT TOP 1 T1.TrgetEntry, T1.TargetType FROM ORDR T0 LEFT JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry WHERE T0.DocEntry = $SODocEntry AND (T1.TargetType != -1)";
				$SAPRow     = ChkRowSAP($ChkBillSQL);
				if($SAPRow == 0) {
					/* ยังไม่เปิดบิล */
					$arrCol['ChkStatus'] = "ERR::NOINVOICE";
				} else {
					$OpenUkey = $_SESSION['ukey'];
					/* เปิดบิลแล้ว */
					$UpdateSQL = "UPDATE picker_soheader SET StatusDoc = 9, UkeyOpen = '$OpenUkey', DateOpen = NOW(), LastUkey = '$OpenUkey', LastUpdate = NOW() WHERE ID = $PickID";
					MySQLUpdate($UpdateSQL);
					$arrCol['ChkStatus'] = "SUCCESS";
					$arrCol['OpenName'] = $_SESSION['uName']." ".$_SESSION['uLastName']." ".date("d/m/Y")." เวลา ".date("H:i")." น.";
				}
			break;
			default:
				$OpenUkey = $_SESSION['ukey'];
				/* เปิดบิลแล้ว */
				$UpdateSQL = "UPDATE picker_soheader SET StatusDoc = 9, UkeyOpen = '$OpenUkey', DateOpen = NOW(), LastUkey = '$OpenUkey', LastUpdate = NOW() WHERE ID = $PickID";
				MySQLUpdate($UpdateSQL);
				$arrCol['ChkStatus'] = "SUCCESS";
				$arrCol['OpenName'] = $_SESSION['uName']." ".$_SESSION['uLastName']." ".date("d/m/Y")." เวลา ".date("H:i")." น.";
			break;
		}
	} else {
		$arrCol['ChkStatus'] = "ERR::NOPERMISSION";
	}
}

if($_GET['p'] == "BillLoy") {
	$PickID = $_POST['PickID'];
	$UpdateUkey = $_SESSION['ukey'];
	$UpdateSQL = "UPDATE picker_soheader SET StatusDoc = 12, BillLoy = 1, LastUkey = '$UpdateUkey', LastUpdate = NOW() WHERE ID = $PickID";
	MySQLUpdate($UpdateSQL);
}

if($_GET['p'] == 'CallBox') {
	// echo $_POST['DocEntry']." | ".$_POST['DocType'];
	$sqlCall = "SELECT
					T0.ID, T0.BillEntry, T0.BillType, T0.BoxCode, T0.BoxNo, T0.TableCreate, T0.TotalItem, T0.BillInBoxc, T4.OutTime, T4.Status,
					T1.DocNum, T1.DateCreate, T1.DateFinish,
					CONCAT(T2.uName,' ',T2.uLastName) AS 'Checker_1',
					CONCAT(T3.uName,' ',T3.uLastName) AS 'Checker_2'
				FROM pack_boxlist T0
				LEFT JOIN pack_header T1 ON T0.BillEntry = T1.BillEntry AND T0.BillType = T1.BillType
				LEFT JOIN users T2 ON T1.uKeyCreate1 = T2.uKey
				LEFT JOIN users T3 ON T1.uKeyCreate2 = T3.uKey
				LEFT JOIN logi_detail T4 ON T0.BoxCode = T4.BoxCode AND T0.BillEntry = T4.BillEntry AND T0.BillType = T4.BillType
				WHERE T0.BillEntry = ".$_POST['DocEntry']." AND T0.BillType = '".$_POST['DocType']."' AND T0.Status = 'C'";
	// echo $sqlCall;
	$sqlQRYCall = MySQLSelectX($sqlCall);
	$Row = 0;
	while ($resultCall = mysqli_fetch_array($sqlQRYCall)) {
		++$Row;
		if($Row == 1) {
			$arrCol['DocNum'] = $resultCall['DocNum'];
			$arrCol['Checker'] = $resultCall['Checker_1'].", ".$resultCall['Checker_2']." (โต๊ะ: ".$resultCall['TableCreate'].")";
			$arrCol['DateCreate'] = date("d/m/Y",strtotime($resultCall['DateCreate']))." เวลา ".date("H:i",strtotime($resultCall['DateCreate']))." น.";
		}
		$arrCol['Row_'.$Row]['ID']          = $resultCall['ID'];
		$arrCol['Row_'.$Row]['BoxCode']     = $resultCall['BoxCode'];
		$arrCol['Row_'.$Row]['BillInBoxc']  = $resultCall['BillInBoxc'];
		$arrCol['Row_'.$Row]['TotalItem']   = $resultCall['TotalItem'];
		if($resultCall['Status'] != 0) {
			$arrCol['Row_'.$Row]['OutTime']  = date("d/m/Y",strtotime($resultCall['OutTime']))." เวลา ".date("H:i",strtotime($resultCall['OutTime']))." น.";
		}else{
			$arrCol['Row_'.$Row]['OutTime']  = '';
		}
		// $arrCol['Row_'.$Row]['BillEntry']   = $resultCall['BillEntry'];
		// $arrCol['Row_'.$Row]['BillType']    = $resultCall['BillType'];
		// $arrCol['Row_'.$Row]['BoxNo']       = $resultCall['BoxNo'];
	}
	$arrCol['Row'] = $Row;
}

if($_GET['p'] == 'CallBoxDetail') {
	// echo $_POST['ID'];
	$sql = "SELECT
				T0.ItemCode, T0.BarCode, T1.ItemName, T0.Qty, T0.DateCreate
			FROM pack_tran T0
			LEFT JOIN oitm T1 ON T0.ItemCode = T1.ItemCode
			LEFT JOIN pack_boxlist T2 ON T0.BoxCode = T2.BoxCode 
			WHERE T2.ID = ".$_POST['ID']."";
	$sqlQRY = MySQLSelectX($sql);
	$Row = 0;
	while($result = mysqli_fetch_array($sqlQRY)) {
		++$Row;
		$arrCol['Row_'.$Row]['ItemCode']   = $result['ItemCode'];
		$arrCol['Row_'.$Row]['BarCode']    = $result['BarCode'];
		$arrCol['Row_'.$Row]['ItemName']   = $result['ItemName'];
		$arrCol['Row_'.$Row]['Qty']        = $result['Qty'];
		$arrCol['Row_'.$Row]['DateCreate'] = date("d/m/Y",strtotime($result['DateCreate']))." เวลา ".date("H:i",strtotime($result['DateCreate']))." น.";
	}
	$arrCol['Row'] = $Row;
}

if($_GET['p'] == 'SaveSend') {
	$This_Year = intval(substr(date("Y")+543,2,2));
	$This_Month = date("m");
	$This_Day = date("d");
	// echo $This_Day."/".$This_Month."/".$This_Year."\n";
	$sqlGET_DocNum = "SELECT DocNum FROM ship_header ORDER BY DocEntry DESC LIMIT 1";
	$GET_DocNum = MySQLSelect($sqlGET_DocNum);
	if(isset($GET_DocNum['DocNum'])) {
		$YearNow = intval(substr($GET_DocNum['DocNum'],4,2));
		$nNumber = intval(substr($GET_DocNum['DocNum'],10))+1;
	}else{
		$YearNow = $This_Year;
		$nNumber = 1;
	}
	
	if ($This_Year == $YearNow) {
		if ($nNumber <= 9){
			$newNum = '000'.$nNumber;
		}else{
			if ($nNumber <= 99){
				$newNum = '00'.$nNumber;
			}else{
				if ($nNumber <= 999){
					$newNum = '0'.$nNumber;
				}else{
					$newNum = $nNumber;
				}
			}
		}
		$newDocNum = "SHP-".$This_Year.$This_Month.$This_Day.$newNum;
	}else{
		$newDocNum = "SHP-".$This_Year.$This_Month.$This_Day."0001"; //SHP-6512130001
	}

	// Get LogiUkey
	if($_POST['SendDocType'] == "OINV" || $_POST['SendDocType'] == "ODLN") {
		switch($_POST['SendDocType']) {
			case "OINV": $SQLLGUK = "SELECT T0.logi_ukey FROM billsr T0 WHERE T0.DocEntry = ".$_POST['SendDocEntry']." LIMIT 1"; break;
			case "ODLN": $SQLLGUK = "SELECT T0.logi_ukey FROM billpa T0 WHERE T0.DocEntry = ".$_POST['SendDocEntry']." LIMIT 1"; break;
		}
		$RSTLGUK = MySQLSelect($SQLLGUK);
		if(!$RSTLGUK) {
			$LogiUkey = "";
		} else {
			$LogiUkey = $RSTLGUK['logi_ukey'];
		}
	} else {
		$LogiUkey = "";
	}
	$DocEntry  = -1;
	$INSERT_HEADER = 
		"INSERT INTO ship_header SET 
			DocNum = '".$newDocNum."', 
			BillEntry = '".$_POST['SendDocEntry']."', 
			BillType = '".$_POST['SendDocType']."', 
			ShippingName = '".$_POST['Ship_Name']."',
			Received = '".$_POST['Satus_Receive']."', 
			ReceiveName = '".$_POST['Name_Receive']."', 
			ReceiveDate = '".$_POST['Date_Receive']."', 
			logi_ukey = '$LogiUkey',
			ShipCost = '".$_POST['ShipCost']."',
			TeamCode = '".$_POST['ShipTeam']."',
			CreateUkey = '".$_SESSION['ukey']."',
			CreateDate = NOW()";
	$DocEntry = MySQLInsert($INSERT_HEADER);
	$DocType = $_POST['SendDocType'];
	// echo $INSERT_HEADER;

	if(isset($_FILES['file_upload']['name'])) {
		$INSERT_SUCCESS = "";
		for($i = 0; $i < count($_FILES['file_upload']['name']); $i++) {
			$L_File = substr($_FILES['file_upload']['name'][$i],-3);
			$NewFileName = $newDocNum."-".date("Y").date("m").date("d").$newNum."-".$i.".".$L_File;
			
			$tmpFilePath = $_FILES['file_upload']['tmp_name'][$i];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/SHIPPING/".$NewFileName;
				move_uploaded_file($tmpFilePath,$NewFilePath);

				$INSERT_DETAIL = "INSERT INTO ship_detail SET DocEntry = '".$DocEntry."', FileName = '".$NewFileName."', CreateUkey = '".$_SESSION['ukey']."', CreateDate = NOW()";
				$INSERT_SUCCESS = MySQLInsert($INSERT_DETAIL);
			}
		}
	}

	if($DocEntry != -1) {
		switch($_POST['SendDocType']) {
			case "ODLN":
			case "OINV":
				if($_POST['SendDocType'] == "ODLN") {
					$TBName = "DLN1";
				} else {
					$TBName = "INV1";
				}
				$SQL = "SELECT DISTINCT T0.BaseEntry FROM $TBName T0 WHERE T0.DocEntry = ".$_POST['SendDocEntry'];
				$SQLQRY = SAPSelect($SQL);
				$result = odbc_fetch_array($SQLQRY);
				$OREntry = $result['BaseEntry'];
				$ORType  = "ORDR";
			break;
			case "OWAB":
			case "OWAS":
			case "OWAR":
				$UpdateSQL = "UPDATE OWAS SET StatusDoc = 14, LastUpdate = NOW(), ukeyUpdate = '".$_SESSION['ukey']."' WHERE DocEntry = ".$_POST['SendDocEntry'];
				MySQLUpdate($UpdateSQL);
				$OREntry = $_POST['SendDocEntry'];
				$ORType  = "OWA";
				break;
		}
		$UpdatePickerSOHeader = "UPDATE picker_soheader SET StatusDoc = 14, LastUkey = '".$_SESSION['ukey']."', LastUpdate = NOW() WHERE SODocEntry = $OREntry AND DocType LIKE '$ORType%'";
		MySQLUpdate($UpdatePickerSOHeader);

		$arrCol['output'] = "SUCCESS";
	}else{
		$arrCol['output'] = "";
	}
}

if($_GET['p'] == 'ShipTrack') {
	$arrCol['DocEntry'] = $_POST['DocEntry'];
	$arrCol['DocType'] = $_POST['DocType'];
	if($_POST['DocType'] == 'OWAS' || $_POST['DocType'] == 'OWAR' || $_POST['DocType'] == 'OWAB'){
        $getDocn = "SELECT T0.DocEntry, T0.DocNum, T0.ShippingName, T0.Received, T0.ReceiveName, T0.ReceiveDate,
		                   T1.DocEntry AS SOEntry,
						   CASE WHEN (T2.loginame = '' OR T2.loginame IS NULL) THEN '-' ELSE T2.loginame END AS 'loginame',
						   CASE	WHEN (T2.logilastname = '' OR T2.logilastname IS NULL) THEN '-' ELSE T2.logilastname END AS 'logilastname',
						   CASE	WHEN (T2.loginickname = '' OR T2.loginickname IS NULL) THEN '-' ELSE T2.loginickname END AS 'loginickname'
					FROM ship_header T0
					LEFT JOIN billsr T1 ON T1.DocEntry = T0.BillEntry
					LEFT JOIN logistic T2 ON T2.logiID = T1.logi_ukey 
					WHERE T0.BillEntry = '".$_POST['DocEntry']."' AND T0.BillType = '".$_POST['DocType']."' AND T0.ShipStatus = 'A'";
    }else if($_POST['DocType'] == 'OINV'){
		// echo "OINV\n";
        $getDocn = "SELECT T0.DocEntry, T0.DocNum, T0.ShippingName, T0.Received, T0.ReceiveName, T0.ReceiveDate, T0.BillEntry, T0.BillType,
						   T1.DocEntry AS SOEntry, 
						   CASE WHEN (T2.loginame = '' OR T2.loginame IS NULL) THEN '-' ELSE T2.loginame END AS 'loginame',
						   CASE	WHEN (T2.logilastname = '' OR T2.logilastname IS NULL) THEN '-' ELSE T2.logilastname END AS 'logilastname',
						   CASE	WHEN (T2.loginickname = '' OR T2.loginickname IS NULL) THEN '-' ELSE T2.loginickname END AS 'loginickname'
					FROM ship_header T0
					LEFT JOIN billsr T1 ON T1.DocEntry = T0.BillEntry
					LEFT JOIN logistic T2 ON T2.logiID = T1.logi_ukey 
					WHERE T0.BillEntry = '".$_POST['DocEntry']."' AND T0.BillType = 'OINV' AND T0.ShipStatus = 'A'";
    }else{
        $getDocn = "SELECT T0.DocEntry, T0.DocNum, T0.ShippingName, T0.Received, T0.ReceiveName, T0.ReceiveDate, T0.BillEntry, T0.BillType,
						   T1.DocEntry AS SOEntry, 
						   CASE WHEN (T2.loginame = '' OR T2.loginame IS NULL) THEN '-' ELSE T2.loginame END AS 'loginame',
						   CASE	WHEN (T2.logilastname = '' OR T2.logilastname IS NULL) THEN '-' ELSE T2.logilastname END AS 'logilastname',
						   CASE	WHEN (T2.loginickname = '' OR T2.loginickname IS NULL) THEN '-' ELSE T2.loginickname END AS 'loginickname'
                    FROM ship_header T0
                    LEFT JOIN billpa T1 ON T1.DocEntry = T0.BillEntry
                    LEFT JOIN logistic T2 ON T2.logiID = T1.logi_ukey 
                    WHERE T0.BillEntry = '".$_POST['DocEntry']."' AND T0.BillType = 'ODLN' AND T0.ShipStatus = 'A'";
    }
	// echo $getDocn;

	if(ChkRowDB($getDocn) > 0) {
		$QryDocn = MySQLSelectX($getDocn);
		$DocArr  = array();
		while($result = mysqli_fetch_array($QryDocn)) {
			if($result['loginame'] == '-') {
				$arrCol['ShipTrack']['Name'] = "ไม่มีข้อมูลชื่อผู้ส่ง";
			}else{
				$arrCol['ShipTrack']['Name'] = $result['loginame']." ".$result['logilastname']." (".$result['loginickname'].")";
			}
			$arrCol['ShipTrack']['ReceiveName'] = $result['ReceiveName'];
			$arrCol['ShipTrack']['ReceiveDate'] = date('d/m/Y',strtotime($result['ReceiveDate']))." เวลา ".date("H:i",strtotime($result['ReceiveDate']))." น.";
			$arrCol['Chk'] = "1";
			array_push($DocArr,$result['DocEntry']);
		}
		// IMAGE
		$DocEty = implode(",",$DocArr);
		$getAtt = "SELECT T0.TransID, T0.FileName FROM ship_detail T0 WHERE T0.DocEntry IN ($DocEty) AND T0.RowStatus = 'A'";
		$qryAtt = MySQLSelectX($getAtt);
		$ChkRow = 0;
		while($resultAtt = mysqli_fetch_array($qryAtt)) {
			++$ChkRow;
			$arrCol[$ChkRow]['TransID'] = $resultAtt['TransID'];
			$arrCol[$ChkRow]['FileName'] = $resultAtt['FileName'];
		}
		if($ChkRow != 0) {
			$arrCol['ChkRow'] = $ChkRow;
		}else{
			$arrCol['ChkRow'] = $ChkRow;
		}
	} else {
		$arrCol['Chk'] = "0";
	}
}

// if($_GET['p'] == 'DelectShipTrack') {
// 	MySQLUpdate("UPDATE ship_detail SET RowStatus = 'I' WHERE TransID = '".$_POST['ID_Att']."'");
// 	$arrCol['Chk'] = "SUCCESS";
// }

if($_GET['p'] == 'ConCancelSend') {
	$DocEntry = $_POST['DocEntry'];
	$DocType = $_POST['DocType'];
	$UserKey = $_SESSION['ukey'];

	/* Change Status A > I in ship_header && ship_detail */
	$GetShipSQL = "SELECT T0.DocEntry FROM ship_header T0 WHERE T0.BillEntry = $DocEntry AND T0.BillType = '$DocType' AND T0.ShipStatus = 'A' LIMIT 1";
	$GetShipRST = MySQLSelect($GetShipSQL);
	$ShipEntry  = $GetShipRST['DocEntry'];

	$UpdateShipHeader = "UPDATE ship_header SET ShipStatus = 'I', UpdateUkey = '$UserKey', UpdateDate = NOW() WHERE DocEntry = $ShipEntry";
	MySQLUpdate($UpdateShipHeader);
	$UpdateShipDetail = "UPDATE ship_detail SET RowStatus  = 'I' WHERE DocEntry = $DocEntry";
	MySQLUpdate($UpdateShipDetail);

	switch($DocType) {
		case "OINV":
		case "ODLN":
			if($DocType == "OINV") {
				$TBName = "INV1";
			} else {
				$TBName = "DLN1";
			}
			$GetORSql = "SELECT DISTINCT T0.BaseEntry  FROM $TBName T0 WHERE T0.DocEntry = $DocEntry";
			$GetORQRY = SAPSelect($GetORSql);
			$GetORRST = odbc_fetch_array($GetORQRY);
			
			$SODocEntry = $GetORRST['BaseEntry'];
			$SODocType  = "ORDR";
		break;
		default:
			$SODocEntry = $DocEntry;
			$SODocType  = $DocType;
		break;
	}

	if($DocType == "OWAR") {
		$UpdateSQL = "UPDATE OWAS SET StatusDoc = 5, ukeyUpdate = '$UserKey', LastUpdate = NOW() WHERE DocEntry = $DocEntry";
	} else {
		$UpdateSQL = "UPDATE picker_soheader SET StatusDoc = 13, LastUkey = '$UserKey', LastUpdate = NOW() WHERE SODocEntry = $SODocEntry AND DocType = '$SODocType'";
	}
	MySQLUpdate($UpdateSQL);

}

if($_GET['p'] == "PrintDoc") {
	$DocNum = $_POST['DocNum'];
	$Prefix = substr($DocNum,0,3);
	switch($Prefix) {
		case "SO-":
		case "SN-":
		case "SA-":
		case "SB-":
			$SapSQL = "SELECT TOP 1 T0.DocEntry FROM ORDR T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) = '$DocNum'";
			$ChkRow = ChkRowSAP($SapSQL);
			if($ChkRow > 0) {
				$SapQRY = SAPSelect($SapSQL);
				$SapRST = odbc_fetch_array($SapQRY);
				$arrCol['GetStatus'] = "SUCCESS";
				$arrCol['DocType'] = "ORDR";
				$arrCol['DocEntry'] = $SapRST['DocEntry'];
			} else {
				$arrCol['GetStatus'] = "ERR::NORESULT";
			}
		break;
		default:
			$WhoSQL = "SELECT T0.DocEntry, CONCAT('OWA',T0.TypeOrder) AS 'DocType' FROM OWAS T0 WHERE T0.DocNum = '$DocNum' LIMIT 1";
			$ChkRow = ChkRowDB($WhoSQL);
			if($ChkRow > 0) {
				$WhoRST = MySQLSelect($WhoSQL);
				$arrCol['GetStatus'] = "SUCCESS";
				$arrCol['DocType'] = $WhoRST['DocType'];
				$arrCol['DocEntry'] = $WhoRST['DocEntry'];
			} else {
				$arrCol['GetStatus'] = "ERR::NORESULT";
			}
		break;
	}
	
}

if($_GET['p'] == "SearchBox") {
	$txtbox = $_POST['txtbox'];
	$method = $_POST['method'];

	switch($method) {
		case "ORDR":
			$GetSQL =
				"SELECT
					T0.ID, T0.SODocEntry, T0.DocType, T0.DocNum, T0.DocDate, T0.DocDueDate, T1.SlpName, T0.CardCode, T0.CardName, T0.StatusDoc
				FROM picker_soheader T0
				LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
				WHERE T0.DocNum LIKE '%$txtbox%' OR T0.CardCode LIKE '%$txtbox%' OR T0.CardName LIKE '%$txtbox%' ORDER BY T0.ID DESC";
			if(ChkRowDB($GetSQL) > 0) {
				$GetQRY = MySQLSelectX($GetSQL);
				$r = 0;
				while($GetRST = mysqli_fetch_array($GetQRY)) {
					$arrCol[$r]['ID']         = $GetRST['ID'];
					$arrCol[$r]['SODocEntry'] = $GetRST['SODocEntry'];
					$arrCol[$r]['SODocType']  = $GetRST['DocType'];
					$arrCol[$r]['DocNum']     = $GetRST['DocNum'];
					$arrCol[$r]['DocDate']    = date("d/m/Y",strtotime($GetRST['DocDate']));
					$arrCol[$r]['DocDueDate'] = date("d/m/Y",strtotime($GetRST['DocDueDate']));
					$arrCol[$r]['SlpName']    = $GetRST['SlpName'];
					$arrCol[$r]['CardName']   = $GetRST['CardCode']." | ".$GetRST['CardName'];
					$arrCol[$r]['StatusDoc']  = $GetRST['StatusDoc'];

					switch($GetRST['StatusDoc']) {
						case 0:  $StatusTxt = "<i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก"; $Tab = 8; break;
						case 1:  $StatusTxt = "<i class='fas fa-file-alt fa-fw fa-1x'></i> เอกสารใหม่"; $Tab = 1; break;
						case 2:  $StatusTxt = "<i class='fas fa-shopping-basket fa-fw fa-1x'></i> รอหยิบสินค้า"; $Tab = 2; break;
						case 3:  $StatusTxt = "<i class='fas fa-shopping-basket fa-fw fa-1x'></i> กำลังหยิบสินค้า"; $Tab = 2; break;
						case 4:  $StatusTxt = "<i class='fas fa-exclamation-triangle fa-fw fa-1x'></i> รอตัดสินค้า";  $Tab = 3; break;
						case 5:  $StatusTxt = "<i class='fas fa-exclamation-triangle fa-fw fa-1x'></i> ยืนยันตัดสินค้า"; $Tab = 3; break;
						case 6:  $StatusTxt = "<i class='fas fa-exclamation-triangle fa-fw fa-1x'></i> รอ/แปลง สินค้า"; $Tab = 3; break;
						case 7:  $StatusTxt = "<i class='fas fa-file-invoice fa-fw fa-1x'></i> รอเปิดบิล"; $Tab = 4; break;
						case 8:  $StatusTxt = "<i class='fas fa-file-invoice fa-fw fa-1x'></i> รอแก้ไขบิล"; $Tab = 4; break;
						case 9:  $StatusTxt = "<i class='fas fa-box-open fa-fw fa-1x'></i> รอแพ็กสินค้า"; $Tab = 5; break;
						case 10: $StatusTxt = "<i class='fas fa-box-open fa-fw fa-1x'></i> กำลังแพ็กสินค้า"; $Tab = 5; break;
						case 11: $StatusTxt = "<i class='fas fa-truck-loading fa-fw fa-1x'></i> สินค้ารอส่ง"; $Tab = 6; break;
						case 12: $StatusTxt = "<i class='fas fa-truck fa-fw fa-1x'></i> กำลังจัดส่ง"; $Tab = 6; break;
						case 13: $StatusTxt = "<i class='fas fa-truck fa-fw fa-1x'></i> รอใบขนส่ง"; $Tab = 6; break;
						case 14: $StatusTxt = "<i class='fas fa-check fa-fw fa-1x'></i> เสร็จสมบูรณ์"; $Tab = 7; break;
					}
					$arrCol[$r]['CallTab'] = $Tab;
					$arrCol[$r]['StatusTxt'] = $StatusTxt;
					if($GetRST['StatusDoc'] == 14) {
						switch($GetRST['DocType']) {
							case "ORDR":
								$SapSQL = "SELECT DISTINCT T0.TrgetEntry, CASE WHEN T0.TargetType = 13 THEN 'OINV' WHEN T0.TargetType = 15 THEN 'ODLN' END AS 'BillType' FROM RDR1 T0 WHERE T0.TrgetEntry IS NOT NULL AND T0.DocEntry = '".$GetRST['SODocEntry']."'";
								$SapQRY = SAPSelect($SapSQL);
								while($SapRST = odbc_fetch_array($SapQRY)) {
									$BillEntry = $SapRST['TrgetEntry'];
									$BillType  = $SapRST['BillType'];
								}
								break;
							default:
								$BillEntry = $GetRST['SODocEntry'];
								$BillType  = $GetRST['DocType'];
								break;
						}
						$arrCol[$r]['BillEntry'] = $BillEntry;
						$arrCol[$r]['BillType']  = $BillType;
					}
					$r++;
				}
			}
			$arrCol['Rows'] = ChkRowDB($GetSQL);
		break;
	}
}

if($_GET['p'] == "GetOrderBacklog") {
	$SQL = 
		"SELECT
			A2.ID AS 'IDPick', A2.SoDocEntry, A2.DocType, A0.BillEntry, A0.BillType, A2.TeamCode, A3.SlpName, A2.DocNum AS 'SODocNum', A2.CardCode, A2.CardName, A1.DocNum AS 'IVDocNum', A0.AllBox, A0.LoadBox
		FROM (
			SELECT DISTINCT
				T0.BillEntry, T0.BillType,
				(SELECT COUNT(P0.BoxCode) FROM pack_boxlist P0 WHERE P0.BillEntry = T0.BillEntry AND P0.BillType = T0.BillType AND P0.Status = 'C') AS 'AllBox',
				COUNT(T0.BoxCode) AS 'LoadBox'
			FROM logi_detail T0
			WHERE (T0.Status = 2 AND (T0.BoxCode NOT LIKE 'BX-65%' AND T0.BoxCode NOT LIKE 'BX-22%'))
			GROUP BY T0.BillEntry, T0.BillType
		) A0
		LEFT JOIN pack_header A1 ON A1.BillEntry = A0.BillEntry AND A1.BillType = A0.BillType
		LEFT JOIN picker_soheader A2 ON A1.IDPick = A2.ID
		LEFT JOIN OSLP A3 ON A2.SlpCode = A3.SlpCode
		WHERE (A0.AllBox > A0.LoadBox AND A1.DocNum IS NOT NULL AND A2.DocType = 'ORDR')
		/* COMMENT เมื่อไม่ใช้ */
		-- UNION ALL
		-- SELECT
		-- 	A0.ID AS 'IDPick', A0.SODocEntry, A0.DocType, A1.BillEntry, A1.BillType, A0.TeamCode, A2.SlpName, A0.DocNum AS 'SODocNum', A0.CardCode, A0.CardName, A1.DocNum AS 'IVDocNum',
		-- 	(SELECT COUNT(P0.BoxCode) FROM pack_boxlist P0 WHERE P0.BillEntry = A1.BillEntry AND P0.BillType = A1.BillType) AS 'A0.AllBox',
		-- 	(SELECT COUNT(P0.BoxCode) FROM logi_detail P0 WHERE P0.BillEntry = A1.BillEntry AND P0.BillType = A1.BillType) AS 'A0.LoadBox'
		-- FROM picker_soheader A0
		-- LEFT JOIN pack_header A1 ON A0.ID = A1.IDPick
		-- LEFT JOIN OSLP A2 ON A0.SlpCode = A2.SlpCode
		-- WHERE A0.DocNum IN ('SO-661201017')
		/* COMMENT ถึงตรงนี้เมื่อไม่ใช้ */
		ORDER BY 'IDPick'";
	$QRY = MySQLSelectX($SQL);
	$Data = ""; $row = 0;
	while($result = mysqli_fetch_array($QRY)) {
		$row++;
		$Data .= "
			<tr>
				<td class='text-center'><a href='javascript:void(0);' onclick='CallSO(\"".$result['SoDocEntry']."\");'>".$result['SODocNum']."</a></td>
				<td>".$result['CardCode']." | ".$result['CardName']."</td>
				<td class='text-center'>".$result['TeamCode']."</td>
				<td>".$result['SlpName']."</td>
				<td class='text-center'><a href='javascript:void(0);' onclick='CallIV(\"".$result['BillEntry']."\",\"".$result['BillType']."\")'>".$result['IVDocNum']."</a></td>
				<td class='text-right'><a href='javascript:void(0);' onclick='CallBox(\"".$result['BillEntry']."\",\"".$result['BillType']."\")'>".number_format($result['AllBox'],0)."</a></td>
				<td class='text-right'>".number_format($result['LoadBox'],0)."</td>
				<td class='text-right'>".number_format($result['AllBox']-$result['LoadBox'],0)."</td>
				<td class='text-center'><a href='javascript:void(0);' onclick='CallLoad(\"".$result['BillEntry']."\",\"".$result['BillType']."\")'><i class='far fa-file-alt fa-fw fa-1x'></i></a></td>
			</tr>";
	}
	if($row == 0) {
		$Data .= "
			<tr>
				<td class='text-center' colspan='9'>ไม่มีรายการสินค้าค้างส่ง :)</td>
			</tr>";
	}
	$arrCol['OrderList'] = $Data;
}

if($_GET['p'] == 'CallLoad') {
	$BillEntry = $_POST['BillEntry'];
	$BillType  = $_POST['BillType'];

	$SQL = "
		SELECT T0.BoxCode, T1.Status, T1.OutTime 
		FROM pack_boxlist T0 
		LEFT JOIN logi_detail T1 ON T1.BillEntry = T0.BillEntry AND T1.BillType = T0.BillType AND T1.BoxCode = T0.BoxCode
		WHERE T0.BillEntry = $BillEntry AND T0.BillType = '$BillType' AND T0.Status = 'C'";
	$QRY = MySQLSelectX($SQL);
	$Data = "";
	while($result = mysqli_fetch_array($QRY)) {
		$Chk = "";
		$Time = "";
		$Color = "table-danger text-danger";
		if($result['Status'] == 2) {
			$Chk = "<i class='fas fa-check fa-fw'></i>";
			$Time = date("d/m/Y", strtotime($result['OutTime']))." เวลา ".date("H:s", strtotime($result['OutTime']))." น.";
			$Color = "";
		}
		$Data .= "
			<tr class='".$Color."'>
				<td class='text-center'>".$result['BoxCode']."</td>
				<td class='text-center'>".$Chk."</td>
				<td class='text-center'>".$Time."</td>
			</tr>";
	}
	$arrCol['Data'] = $Data;
}

if($_GET['p'] == "GetSender") {
	$DocEntry = $_POST['DocEntry'];
	$DocType  = $_POST['DocType'];

	switch($DocType) {
		case "OINV": $SQL1 = "SELECT T0.Load_Date AS 'LoadDate' FROM billsr T0 LEFT JOIN logistic T1 ON T0.logi_ukey = T1.logiID WHERE T0.DocEntry = $DocEntry LIMIT 1"; break;
		case "ODLN": $SQL1 = "SELECT T0.Load_Date AS 'LoadDate' FROM billpa T0 LEFT JOIN logistic T1 ON T0.logi_ukey = T1.logiID WHERE T0.DocEntry = $DocEntry LIMIT 1"; break;
	}
	if($DocType == "OINV" || $DocType == "ODLN") {
		$RST1 = MySQLSelect($SQL1);
		if($RST1) {
			$arrCol['LoadDate'] = $RST1['LoadDate'];
		} else {
			$arrCol['LoadDate'] = "";
		}
		
		$SQL2 = 
			"SELECT TOP 1
				ISNULL(T1.Name,'') AS 'LogiName',
				CASE WHEN T0.SlpCode IN (20,123,124,125,126,251,291,296) THEN T4.U_Dim1 ELSE T2.U_Dim1 END AS 'TeamCode'
			FROM $DocType T0
			LEFT JOIN dbo.[@SHIPPINGTYPE] T1 ON T0.U_ShippingType = T1.Code
			LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
			LEFT JOIN OCRD T3 ON T0.CardCode = T3.CardCode
			LEFT JOIN OSLP T4 ON T3.SlpCode = T4.SlpCode
			WHERE T0.DocEntry = $DocEntry";
		$QRY2 = SAPSelect($SQL2);
		$RST2 = odbc_fetch_array($QRY2);
		if($RST2) {
			$arrCol['LogiName'] = conutf8($RST2['LogiName']);
			$arrCol['TeamCode'] = conutf8($RST2['TeamCode']);
		} else {
			$arrCol['LogiName'] = "";
			$arrCol['TeamCode'] = "";
		}

		$arrCol['Status'] = "OK";
	} else {
		$SQL1 =
			"SELECT
				T0.CusCode,
				CASE
					WHEN T2.DeptCode = 'DP006' THEN 'MT1'
					WHEN T2.DeptCode = 'DP007' THEN 'MT2'
					WHEN T2.DeptCode = 'DP005' THEN 'TT2'
					WHEN T2.DeptCode = 'DP008' THEN 'OUL'
					WHEN T2.DeptCode = 'DP003' AND T2.LvCode IN ('LV019','LV104','LV105','LV106') THEN 'ONL'
				ELSE 'KBI' END AS 'TeamCode',
				NULLIF(T0.LogiName,'') AS 'LogiName'
			FROM OWAS T0
			LEFT JOIN users T1 ON T0.UserCreate = T1.uKey
			LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
			WHERE T0.DocEntry = $DocEntry LIMIT 1";
		$RST1 = MySQLSelect($SQL1);
		$arrCol['LogiName'] = $RST1['LogiName'];
		$arrCol['TeamCode'] = $RST1['TeamCode'];
		$arrCol['Status'] = "OK";
	}

}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
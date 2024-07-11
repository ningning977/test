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
}

if($_GET['p'] == "GetEmpName") {
	switch($_SESSION['DeptCode']) {
		case "DP003":
		case "DP005":
		case "DP006":
		case "DP007":
		case "DP008": $EmpWhr = " AND (T1.DeptCode = '".$_SESSION['DeptCode']."' AND T1.UClass IN(18,19,20)) "; break;
		default: $EmpWhr = " AND (T1.DeptCode IN ('DP003','DP005','DP006','DP007','DP008') AND T1.UClass IN (18,19,20)) "; break;
	}
	if ($_SESSION['LvCode'] == 'LV038'){
		$EmpWhr = " AND (T1.DeptCode IN ('DP003','DP005','DP006','DP007','DP008') AND T1.UClass IN (18,19,20)) "; 
	}
	$EmpSQL = "SELECT
					T0.uKey, CONCAT(T0.uName,' ',T0.uLastName) AS 'EmpName', T0.uNickName, T0.LvCode, T1.UClass, T1.DeptCode, T2.DeptName
				FROM users T0
				LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode
				LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
				WHERE
					((T0.UserStatus = 'A' OR ((T0.UserStatus = 'I' AND YEAR(T0.ResignDate) = YEAR(NOW())))) $EmpWhr) OR 
					T0.ukey IN ('e01ac4a337aef61adfdb5f88158b1cd0','f9f757a10d46ddad199ece6b7a8a3166','d6938351e624baf0518c4fc243d5a1ce','c44d565cccf48e7ffc998fadcdd6b521','7be381f86b4ca28c12c279874e95fab2')
				ORDER BY T1.DeptCode ASC, T1.uClass ASC, T0.uName ASC";
	$EmpQRY = MySQLSelectX($EmpSQL);

	$TmpDeptCode = "";
	$TmpDeptName = "";
	$output .= "";

	while($EmpRST = mysqli_fetch_array($EmpQRY)) {
		if($_SESSION['uClass'] == 20 && $EmpRST['uKey'] != $_SESSION['ukey']) {
			$disb = " disabled";
		} else {
			$disb = NULL;
		}
		if($EmpRST['uNickName'] == "") {
			$nickname = NULL;
		} else {
			$nickname = " (".$EmpRST['uNickName'].")";
		}
		if($_SESSION['ukey'] == $EmpRST['uKey']) {
			$slct = " selected";
		} else {
			$slct = NULL;
		}
		if($TmpDeptCode == "" || $TmpDeptCode != $EmpRST['DeptCode']) {
			if($TmpDeptCode != "") {
				$output .= "</optgroup>";
			}
			$output .= "<optgroup label='".$EmpRST['DeptName']."'>";
				$output .= "<option value='".$EmpRST['uKey']."'$disb$slct>".$EmpRST['EmpName'].$nickname."</option>";
			$TmpDeptCode = $EmpRST['DeptCode'];
		} else {
				$output .= "<option value='".$EmpRST['uKey']."'$disb$slct>".$EmpRST['EmpName'].$nickname."</option>";
		}
	}
	$output .= "</optgroup>";
	$output .= "<optgroup label='อื่น ๆ'>";
		$output .= "<option value='B11'>11 - สำนักงาน</option>";
		$output .= "<option value='B60'>60 - ซ่อมสินค้าหน้าร้าน</option>";
		$output .= "<option value='B98'>B98 - ซ่อมภายนอก (QC)</option>";
		$output .= "<option value='B99'>B99 - ซ่อมภายใน</option>";
	$output .= "</optgroup>";
	$arrCol['filt_user'] = $output;
}

if($_GET['p'] == "GetInvoice") {
	$user = $_POST['u'];
	$type = $_POST['t'];
	$pta  = FALSE;
	$notSale = "";
	if(isset($_POST['PITA'])) {
		$pta = TRUE;
	}
	switch($type) {
		case "ALL":   $BONUS = FALSE; $OV30D = FALSE; break;
		case "BONUS": $BONUS = TRUE;  $OV30D = FALSE; $notSale = " T1.SlpName NOT LIKE '%.3%' AND "; break;
		case "OVDUE": $BONUS = FALSE; $OV30D = TRUE; $notSale = " T1.SlpName NOT LIKE '%.3%' AND "; break;
		case "Faxkay": $BONUS = FALSE; $OV30D = FALSE; $notSale = " T1.SlpName LIKE '%.3%' AND ";  break;
	}

	switch($user) {
		case "B11": $IVWhr = "(T0.SlpCode = 1)"; break;
		case "B60": $IVWhr = "(T0.SlpCode = 251)"; break;
		case "B98": $IVWhr = "(T0.SlpCode = 296)"; break;
		case "B99": $IVWhr = "(T0.SlpCode = 291)"; break;
		case 'PITA' : $IVWhr = "(T0.SlpCode  != 999)"; break;
		default:    $IVWhr = "(T1.Memo = '$user' OR (T0.SlpCode IN (251,291,296) AND T6.Memo = '$user'))"; break;

	}
	
	$IVSQL = "SELECT
				'OINV' AS 'DocType', T0.DocEntry, (ISNULL(T2.BeginStr,'IV-')+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.NumAtCard,
				CASE WHEN T1.SlpName LIKE '%.3%' THEN 0 ELSE 1 END AS FK,
				CASE WHEN T0.DocDate = '2022-12-31' THEN T7.DocDate ELSE T0.DocDate END AS 'DocDate', T0.DocDueDate, DATEDIFF(day,T0.DocDueDate,GETDATE()-30) AS 'DateDiff',
				CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) <= 30 THEN 'B30'
					WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 31 AND 60 THEN 'B60'
					WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 61 AND 90 THEN 'B90'
				ELSE 'A90' END AS 'DueType',
				T0.CardCode, T0.CardName, T0.DocTotal, (T0.DocTotal-T0.PaidToDate) AS 'NoPaid', T0.SlpCode, T1.SlpName, T1.U_Dim1,
				T0.U_Remak_Oveerdue, T3.U_BillAdd, T3.U_SaveMoney, T4.Descr AS AddBill , T5.Descr AS SaveMoney
			FROM OINV T0
			LEFT JOIN KBI_DB2022.dbo.OINV T7 ON T0.NumAtCard = T7.NumAtCard
			LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
			LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
			LEFT JOIN OCRD T3 ON T0.CardCode = T3.CardCode
			LEFT JOIN UFD1 T4 ON T3.U_BillAdd = T4.FldValue AND T4.TableID = 'OCRD' AND T4.FieldID = 25
			LEFT JOIN UFD1 T5 ON T3.U_SaveMoney = T5.FldValue AND T5.TableID = 'OCRD' AND T5.FieldID = 26
			LEFT JOIN OSLP T6 ON T3.SlpCode = T6.SlpCode
			WHERE T0.SlpCode NOT IN (23,24,158,290) AND $notSale
				((MONTH(T0.DocDueDate) < MONTH(GETDATE()) AND YEAR(T0.DocDueDate) = YEAR(GETDATE())) OR (YEAR(T0.DocDueDate) < YEAR(GETDATE()))) AND 
				(T0.CANCELED = 'N' AND T0.DocStatus = 'O' AND (T0.DocTotal-T0.PaidToDate) > 0) AND 
				$IVWhr
			UNION ALL
			SELECT
				'ORIN' AS 'DocType', T0.DocEntry, (ISNULL(T2.BeginStr,'IV-')+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.NumAtCard, 
				0 AS FK,
				CASE WHEN T0.DocDate = '2022-12-31' THEN T7.DocDate ELSE T0.DocDate END AS 'DocDate', T0.DocDueDate, DATEDIFF(day,T0.DocDueDate,GETDATE()-30) AS 'DateDiff',
				CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) <= 30 THEN 'B30'
					WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 31 AND 60 THEN 'B60'
					WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 61 AND 90 THEN 'B90'
				ELSE 'A90' END AS 'DueType',
				T0.CardCode, T0.CardName, -T0.DocTotal AS 'DocTotal', -(T0.DocTotal-T0.PaidToDate) AS 'NoPaid', T0.SlpCode, T1.SlpName, T1.U_Dim1,
				T0.U_Remak_Oveerdue, T3.U_BillAdd, T3.U_SaveMoney, T4.Descr AS AddBill , T5.Descr AS SaveMoney
			FROM ORIN T0
			LEFT JOIN KBI_DB2022.dbo.ORIN T7 ON T0.NumAtCard = T7.NumAtCard
			LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
			LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
			LEFT JOIN OCRD T3 ON T0.CardCode = T3.CardCode
			LEFT JOIN UFD1 T4 ON T3.U_BillAdd = T4.FldValue AND T4.TableID = 'OCRD' AND T4.FieldID = 25
			LEFT JOIN UFD1 T5 ON T3.U_SaveMoney = T5.FldValue AND T5.TableID = 'OCRD' AND T5.FieldID = 26
			LEFT JOIN OSLP T6 ON T3.SlpCode = T6.SlpCode
			WHERE T0.SlpCode NOT IN (23,24,158,290) AND $notSale 
				((MONTH(T0.DocDueDate) < MONTH(GETDATE()) AND YEAR(T0.DocDueDate) = YEAR(GETDATE())) OR (YEAR(T0.DocDueDate) < YEAR(GETDATE()))) AND 
				(T0.CANCELED = 'N' AND T0.DocStatus = 'O' AND (T0.DocTotal-T0.PaidToDate) > 0 AND T2.BeginStr IN ('S1-','SR-')) AND 
				$IVWhr
			ORDER BY 'DocType' ASC, T0.CardCode ASC, T0.DocDueDate ASC";
	 //echo $IVSQL;
	if($pta == TRUE){
		$Rows = ChkRowPITA($IVSQL);
	}else{
		$Rows = ChkRowSAP($IVSQL);
	}
	$output = "";
	$SUMALL = 0;
	$SUMB30 = 0;
	$SUMB60 = 0;
	$SUMB90 = 0;
	$SUMA90 = 0;

	$SUMSAL = 0;
	$SUMSUP = 0;
	$SUMMGR = 0;

	if($Rows == 0) {
		$output .= "<tr><td class='text-center' colspan='14'>ไม่มีข้อมูล :(</td></tr>";
		$overdue = null;
		$finedue = null;
		$bonusdue = null;
	} else {
		if($pta == TRUE){
			$IVQRY = PITASelect($IVSQL);
		}else{
			$IVQRY = SAPSelect($IVSQL);
		}
		while($IVRST = odbc_fetch_array($IVQRY)) {
			if($IVRST['DocType'] == "OINV") {
				$ChkBonus = CheckBonus($IVRST['DocDate']);
			} else {
				$ChkBonus = FALSE;
			}
			if($type == "ALL" || $type == "Faxkay" ||($type == "BONUS" && $ChkBonus == TRUE) || ($type == "OVDUE" && $IVRST['DueType'] != "B30")) {
				$trclass = NULL;
				//
				switch($IVRST['DueType']) {
					case "B30":
						if($IVRST['DocType'] == 'OINV') { $trclass = NULL; } else { $trclass = " class='table-active'"; }
						$FineRate = 0; 
						break;
					case "B60": 
						if($IVRST['DocType'] == 'OINV') { $trclass = " class='table-success text-success'"; } else { $trclass = " class='table-active'"; }
						$FineRate = 0.005;
						break;
					case "B90": 
						if($IVRST['DocType'] == 'OINV') { $trclass = " class='table-warning text-warning'"; } else { $trclass = " class='table-active'"; }
						$FineRate = 0.01;
						break;
					default:    
						if($IVRST['DocType'] == 'OINV') { $trclass = " class='table-danger text-danger'"; } else { $trclass = " class='table-active'"; }
						$FineRate = 0.03;
						break;
				}
				$showBonus = NULL;
				
				if($user == "B60") {
					if($IVRST['DocType'] == "OINV") {
						if($ChkBonus == TRUE && $IVRST['DocType'] == "OINV") { $showBonus = "<i class='fas fa-check fa-fw fa-lg text-success'></i>"; }

						$FineMGR = ($IVRST['NoPaid']*$FineRate)*0.5;
						$FineSUP = ($IVRST['NoPaid']*$FineRate)*0.5;
						$FineSAL = ($IVRST['NoPaid']*$FineRate)*0;

						if($FineSAL > 0) { $showSAL = number_format($FineSAL,2); } else { $showSAL = NULL; }
						if($FineSUP > 0) { $showSUP = number_format($FineSUP,2); } else { $showSUP = NULL; }
						if($FineMGR > 0) { $showMGR = number_format($FineMGR,2); } else { $showMGR = NULL; }
						
					} else {
						$FineMGR = NULL;
						$FineSUP = NULL;
						$FineSAL = NULL;
						$showSAL = NULL;
						$showSUP = NULL;
						$showMGR = NULL;
					}
				} else {
					switch($IVRST['SlpCode']) {
						case 251:
						case 291:
						case 296: $Fixed = TRUE; break;
						default:  $Fixed = FALSE; break;
					}
					switch($IVRST['U_Dim1']) {
						case "TT1":
						case "TT2":
						case "OUL":
						case "ONL": $CallFine = TRUE; break;
						default:    $CallFine = FALSE; break;
					}
					$CallFine = TRUE;
					//echo $ChkBonus;
					//echo $IVRST['DocType']." - ".$Fixed." - ". $CallFine." - ".$IVRST['SlpCode']." - ".$IVRST['FK'] ."<br>";
					if($IVRST['DocType'] == "OINV" && $Fixed == FALSE && $CallFine == TRUE && $IVRST['SlpCode'] != 1 && $IVRST['FK'] == 1) {
						//echo "waiw3";
						if($ChkBonus == TRUE && $IVRST['DocType'] == "OINV") { if($IVRST['U_Dim1'] != "ONL") { $showBonus = "<i class='fas fa-check fa-fw fa-lg text-success'></i>"; } }
						//echo "waiw";
						$FineMGR = ($IVRST['NoPaid']*$FineRate)*0.1;
						$FineSUP = ($IVRST['NoPaid']*$FineRate)*0.2;
						$FineSAL = ($IVRST['NoPaid']*$FineRate)*0.7;

						if($FineSAL > 0) { $showSAL = number_format($FineSAL,2); } else { $showSAL = NULL; }
						if($FineSUP > 0) { $showSUP = number_format($FineSUP,2); } else { $showSUP = NULL; }
						if($FineMGR > 0) { $showMGR = number_format($FineMGR,2); } else { $showMGR = NULL; }
					} else {
						$FineMGR = NULL;
						$FineSUP = NULL;
						$FineSAL = NULL;
						$showSAL = NULL;
						$showSUP = NULL;
						$showMGR = NULL;
					}
				}

				switch($_SESSION['DeptCode']) {
					case "DP002":
					case "DP003":
					case "DP005":
					case "DP006":
					case "DP007":
					case "DP008": 
						if($_SESSION['uClass'] == 0 || $_SESSION['uClass'] == 18 || $_SESSION['uClass'] == 19 || $_SESSION['uClass'] == 20) { $readonly = NULL; } else { $readonly = " readonly"; }
						break;
					case "DP009": $readonly = NULL; break;
					default: $readonly = " readonly"; break;
				}
				if($pta == TRUE){
					$table_remark = "collect_remark_pita";
				}else{
					$table_remark = "collect_remark";
				}
				$RemarkSQL = "SELECT T0.Comments FROM $table_remark T0 WHERE T0.DocType = '".$IVRST['DocType']."' AND T0.DocEntry = '".$IVRST['DocEntry']."' AND T0.DocStatus = 'A' LIMIT 1";
				$Rows = ChkRowDB($RemarkSQL);
				if($Rows != 0) {
					$RemarkRST = MySQLSelect($RemarkSQL);
					if($RemarkRST['Comments'] == NULL || $RemarkRST['Comments'] == "") {
						$Comment = NULL;
					} else {
						$Comment = $RemarkRST['Comments'];
					}
				} else {
					$Comment = NULL;
				}
				if($IVRST['DateDiff'] < 0) {
					$DateDiff = "<span class='text-muted'>-</span>";
				} else {
					$DateDiff = "<strong>+".$IVRST['DateDiff']."</strong>";
				}
				$output .= "<tr$trclass data-type='$type' data-ChkBonus='$ChkBonus' data-DueType='".$IVRST['DueType']."'>";
					$output .= "<td class='align-top text-center'>".$IVRST['NumAtCard']."</td>";
					$output .= "<td class='align-top text-center'>".date("d/m/Y",strtotime($IVRST['DocDate']))."</td>";
					$output .= "<td class='align-top text-center text-danger'>".date("d/m/Y",strtotime($IVRST['DocDueDate']))."</td>";
					$output .= "<td class='align-top text-right'>$DateDiff</td>";
					$output .= "<td class='align-top'>".conutf8($IVRST['CardCode']." | ".$IVRST['CardName'])."<br/><small>พนักงานขาย: ".conutf8($IVRST['SlpName'])."</small></td>";
					$output .= "<td class='align-top text-right'>".number_format($IVRST['DocTotal'],2)."</td>";
					$output .= "<td class='align-top text-right text-danger'>".number_format($IVRST['NoPaid'],2)."</td>";
					$output .= "<td class='text-center'><textarea class='form-control form-control-sm CollectRemark' style='font-size: 13px;' rows='2' data-DocType='".$IVRST['DocType']."' data-DocEntry='".$IVRST['DocEntry']."'$readonly>$Comment</textarea></td>";
					$output .= "<td class='align-top text-right text-danger'>$showSAL</td>";
					$output .= "<td class='align-top text-right text-danger'>$showSUP</td>";
					$output .= "<td class='align-top text-right text-danger'>$showMGR</td>";
					if($pta == TRUE){
						$output .= "<td class='text-center'>0</td>";
					}else{
						$output .= "<td class='text-center'>$showBonus</td>";
					}
					$output .= "<td class='align-top'>".conutf8($IVRST['AddBill'])."</td>";
					$output .= "<td class='align-top'>".conutf8($IVRST['SaveMoney'])."</td>";
				$output .= "</tr>";

				$SUMALL = $SUMALL+$IVRST['NoPaid'];
				$SUMSAL = $SUMSAL+$FineSAL;
				$SUMSUP = $SUMSUP+$FineSUP;
				$SUMMGR = $SUMMGR+$FineMGR;
				${"SUM".$IVRST['DueType']} = ${"SUM".$IVRST['DueType']}+$IVRST['NoPaid'];
			}
		}

		$overdue = "<table class='table table-bordered' style='font-size: 12px;'>";
			$overdue .= "<thead class='text-center'>";
				$overdue .= "<tr><th colspan='2'>ยอดหนี้เกินกำหนด</th></tr>";
				$overdue .= "<tr>";
					$overdue .= "<th width='50%'>รายละเอียด</th>";
					$overdue .= "<th>ยอดค้างชำระ (บาท)</th>";
				$overdue .= "</tr>";
			$overdue .= "</thead>";
			$overdue .= "<tbody>";
				$overdue .= "<tr>";
					$overdue .= "<td>เกินกำหนด <strong>< 30 วัน</strong></td>";
					$overdue .= "<td class='text-right'><strong>".number_format($SUMB30,2)."</strong></td>";
				$overdue .= "</tr>";
				$overdue .= "<tr>";
					$overdue .= "<td>เกินกำหนด <strong>31 - 60 วัน</strong></td>";
					$overdue .= "<td class='text-right'><strong class='text-success'>".number_format($SUMB60,2)."</strong></td>";
				$overdue .= "</tr>";
				$overdue .= "<tr>";
					$overdue .= "<td>เกินกำหนด <strong>61 - 90 วัน</strong></td>";
					$overdue .= "<td class='text-right'><strong class='text-warning'>".number_format($SUMB90,2)."</strong></td>";
				$overdue .= "</tr>";
				$overdue .= "<tr>";
					$overdue .= "<td>เกินกำหนด <strong> 90 วันขึ้นไป</strong></td>";
					$overdue .= "<td class='text-right'><strong class='text-danger'>".number_format($SUMA90,2)."</strong></td>";
				$overdue .= "</tr>";
				$overdue .= "<tr class='table-danger text-danger'>";
					$overdue .= "<td><strong>รวมเกินกำหนดทั้งหมดเกิน 30 วัน</strong> <a href='javascript:void(0);' onclick='$(\"#filt_type\").val(\"OVDUE\").change();'><i class='fas fa-search fa-fw fa-1x'></i></a></td>";
					$overdue .= "<td class='text-right'><strong>".number_format($SUMB60+$SUMB90+$SUMA90,2)."</strong></td>";
				$overdue .= "</tr>";
				$overdue .= "<tr class='table-active text-muted'>";
					$overdue .= "<td><strong>รวมเกินกำหนดทั้งหมด</strong></td>";
					$overdue .= "<td class='text-right'><strong>".number_format($SUMALL,2)."</strong></td>";
				$overdue .= "</tr>";
			$overdue .= "</tbody>";
		$overdue.= "</table>";

		$finedue = "<table class='table table-bordered' style='font-size: 12px;'>";
			$finedue .= "<thead class='text-center'>";
				$finedue .= "<tr><th colspan='2'>ค่าปรับ <a href='javascript:void(0)' onclick='OpenFineCond();'><i class='fas fa-info-circle fa-fw fa-1x'></i></a></th></tr>";
				$finedue .= "<tr>";
					$finedue .= "<th width='50%'>รายละเอียด</th>";
					$finedue .= "<th>ค่าปรับ (บาท)</th>";
				$finedue .= "</tr>";
			$finedue .= "</thead>";
			$finedue .= "<tbody>";
				$finedue .= "<tr>";
					$finedue .= "<td>ค่าปรับ <strong>พนักงานขาย</strong></td>";
					$finedue .= "<td class='text-right'><strong>".number_format($SUMSAL,2)."</strong></td>";
				$finedue .= "</tr>";
				$finedue .= "<tr>";
					$finedue .= "<td>ค่าปรับ <strong>หัวหน้าทีมขาย</strong></td>";
					$finedue .= "<td class='text-right'><strong>".number_format($SUMSUP,2)."</strong></td>";
				$finedue .= "</tr>";
				$finedue .= "<tr>";
					$finedue .= "<td>ค่าปรับ <strong>ผู้จัดการทีมขาย</strong></td>";
					$finedue .= "<td class='text-right'><strong>".number_format($SUMMGR,2)."</strong></td>";
				$finedue .= "</tr>";
				$finedue .= "<tr class='table-danger text-danger'>";
					$finedue .= "<td><strong>รวมค่าปรับท้ังหมด</strong></td>";
					$finedue .= "<td class='text-right'><strong>".number_format($SUMSAL+$SUMSUP+$SUMMGR,2)."</strong></td>";
				$finedue .= "</tr>";
			$finedue .= "</tbody>";
		$finedue.= "</table>";
		/*
		if ($pta AND $user == "PITA" ){

		}else{
			
		}
		*/
		$GetBonus = CalculateBonus($user);
		
		$bonusdue = "<table class='table table-bordered' style='font-size: 12px;'>";
			$bonusdue .= "<thead class='text-center'>";
				$bonusdue .= "<tr><th colspan='2'>โบนัส <a href='javascript:void(0)' onclick='OpenBonusCond();'><i class='fas fa-info-circle fa-fw fa-1x'></i></a></th></tr>";
				$bonusdue .= "<tr>";
					$bonusdue .= "<th width='50%'>หัวข้อ</th>";
					$bonusdue .= "<th>รายละเอียด</th>";
				$bonusdue .= "</tr>";
			$bonusdue .= "</thead>";
			$bonusdue .= "<tbody>";
				$bonusdue .= "<tr>";
					if($pta == TRUE){
						$bonusdue .= "<td>บิลที่ต้องเก็บเงินให้ได้ (ใบ) <a href='javascript:void(0);' ><i class='fas fa-search fa-fw fa-1x'></i></a></td>";
						$bonusdue .= "<td class='text-right'><a href='javascript:void(0);'><strong>0</strong></td>";
					}else{
						$bonusdue .= "<td>บิลที่ต้องเก็บเงินให้ได้ (ใบ) <a href='javascript:void(0);' onclick='$(\"#filt_type\").val(\"BONUS\").change();'><i class='fas fa-search fa-fw fa-1x'></i></a></td>";
						$bonusdue .= "<td class='text-right'><a href='javascript:void(0);'><strong>".number_format($GetBonus[0],0)."</strong></td>";
					}
				$bonusdue .= "</tr>";
				$bonusdue .= "<tr>";
					$bonusdue .= "<td>มูลค่าที่ต้องเก็บเงินให้ได้ (บาท)</td>";
					if($pta == TRUE){
						$bonusdue .= "<td class='text-right'><strong>0.00</strong></td>";
					}else{
						$bonusdue .= "<td class='text-right'><strong>".number_format($GetBonus[1],2)."</strong></td>";
					}
				$bonusdue .= "</tr>";
				$bonusdue .= "<tr>";
					$bonusdue .= "<td>ยอดขายในเดือน ".$GetBonus[4]." (บาท)</td>";
					if($pta == TRUE){
						$bonusdue .= "<td class='text-right'><strong>0.00</strong></td>";
					}else{
						$bonusdue .= "<td class='text-right'><strong>".number_format($GetBonus[2],2)."</strong></td>";
					}
				$bonusdue .= "</tr>";
				$bonusdue .= "<tr class='table-success text-success'>";
					$bonusdue .= "<td><strong>โบนัสที่จะได้รับ (บาท)</strong></td>";
					if($pta == TRUE){
						$bonusdue .= "<td class='text-right'><strong>0.00</strong></td>";
					}else{
						$bonusdue .= "<td class='text-right'><strong>".number_format($GetBonus[3],2)."</strong></td>";
					}
				$bonusdue .= "</tr>";
			$bonusdue .= "</tbody>";
		$bonusdue.= "</table>";
	}

	$arrCol['view_collectlist'] = $output;
	$arrCol['view_overdue'] = $overdue;
	$arrCol['view_finedue'] = $finedue;
	$arrCol['view_bonusdue'] = $bonusdue;
}

if($_GET['p'] == "SaveRemark") {
	$DocType    = $_POST['DocType'];
	$DocEntry   = $_POST['DocEntry'];
	$DocText    = $_POST['DocText'];
	$CreateUkey = $_SESSION['ukey'];
	if(isset($_POST['PITA'])) {
		$table = "collect_remark_pita";
	}else{
		$table = "collect_remark";
	}

	$ChkRow = "SELECT T0.RemarkID FROM $table T0 WHERE T0.DocType = '$DocType' AND T0.DocEntry = '$DocEntry' ORDER BY T0.RemarkID DESC LIMIT 1";
	$Rows   = ChkRowDB($ChkRow);
	if($Rows == 0) {
		if($DocText != "" || $DocText != NULL) {
			$InsertSQL = "INSERT INTO $table SET DocType = '$DocType', DocEntry = '$DocEntry', Comments = '$DocText', CreateUkey = '$CreateUkey'";
			MySQLInsert($InsertSQL);
		}
	} else {
		$LastIDSQL = "SELECT T0.RemarkID, T0.Comments FROM $table T0 WHERE T0.DocType = '$DocType' AND T0.DocEntry = '$DocEntry' AND T0.DocStatus = 'A' ORDER BY T0.RemarkID DESC LIMIT 1";
		$LastIDRST = MySQLSelect($LastIDSQL);

		if($LastIDRST['Comments'] != $DocText) {
			if($DocText != '') {
				$Comment = "'$DocText'";
			} else {
				$Comment = "NULL";
			}
			$UpdateSQL = "UPDATE $table SET DocStatus = 'I' WHERE RemarkID = ".$LastIDRST['RemarkID'];
			MySQLUpdate($UpdateSQL);

			$InsertSQL = "INSERT INTO $table SET DocType = '$DocType', DocEntry = '$DocEntry', Comments = $Comment, CreateUkey = '$CreateUkey'";
			MySQLInsert($InsertSQL);
		}
	}
}

if($_GET['p'] == 'GetChkReturn') {

	$ListSQL = 
		"SELECT
			T0.CHQ_ID, T0.DocNum, T0.CHQ_No, T0.CHQ_Amount, T0.CHQ_DateReturn, T0.CHQ_SaleReceive, 
			DATEDIFF(NOW(),T0.CHQ_DateReturn) AS 'DateDiff', T0.CardCode, IFNULL(T0.CardName,T5.CardName) AS 'CardName', 
			CONCAT(T3.uName,' ',T3.uLastName) AS 'SalesName', T4.DeptCode, CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName',
			CONCAT(T1.ReturnCode,' | ',T1.ReturnName) AS 'CauseReturn',
			IFNULL((SELECT SUM(P1.Amount) FROM chq_detail P1 WHERE P1.DocNum = T0.DocNum),0) AS 'Paid',
			IFNULL((SELECT P2.Remark FROM chq_remark P2 WHERE P2.DocNum = T0.DocNum AND Status = 1 ORDER BY P2.CreateDate DESC LIMIT 1),'') AS 'Remark'
		FROM chq_return T0
		LEFT JOIN chq_causereturn T1 ON T0.CauseReturn = T1.TransID
		LEFT JOIN users T2 ON T0.CreateUkey = T2.uKey
		LEFT JOIN users T3 ON T0.SaleUkey = T3.uKey
		LEFT JOIN positions T4 ON T3.LvCode = T4.LvCode
		LEFT JOIN OCRD T5 ON T0.CardCode = T5.CardCode
		WHERE T0.Status = 0 AND T0.SaleUkey = '".$_POST['uKey']."'
		ORDER BY T0.CHQ_DateReturn, T0.DocNum";
	
	$Rows = ChkRowDB($ListSQL);
	if($Rows > 0) {
		$ListQRY = MySQLSelectX($ListSQL);
		$SUM_CheckSUM = 0;
		$SUM_Applied  = 0;
		$SUM_FineALL  = 0;
		$SUM_FineSAL  = 0;
		$SUM_FineSUP  = 0;
		$SUM_FineMGR  = 0;
		$i = 0;

		while($ListRST = mysqli_fetch_array($ListQRY)) {
			$Balance = $ListRST['CHQ_Amount'] - $ListRST['Paid'];
			$FineALL = 0;
			$FineSAL = 0;
			$FineSUP = 0;
			$FineMGR = 0;

			$arrCol['BD_'.$i]['CHQ_ID']          = $ListRST['CHQ_ID'];
			$arrCol['BD_'.$i]['DocNum']          = $ListRST['DocNum'];
			$arrCol['BD_'.$i]['CHQ_SaleReceive'] = date("d/m/Y",strtotime($ListRST['CHQ_SaleReceive']));
			$arrCol['BD_'.$i]['CardCode']        = $ListRST['CardCode']." | ".$ListRST['CardName'];
			$arrCol['BD_'.$i]['CHQ_DateReturn']  = date("d/m/Y",strtotime($ListRST['CHQ_DateReturn']));
			$arrCol['BD_'.$i]['DateDiff']        = "+".number_format($ListRST['DateDiff'],0);
			$arrCol['BD_'.$i]['SalesName']       = $ListRST['SalesName'];
			$arrCol['BD_'.$i]['CauseReturn']     = $ListRST['CauseReturn'];
			$arrCol['BD_'.$i]['CHQ_No']          = $ListRST['CHQ_No'];
			$arrCol['BD_'.$i]['CHQ_Amount']      = number_format($ListRST['CHQ_Amount'],2);
			$arrCol['BD_'.$i]['Balance']         = number_format($Balance,2);
			$arrCol['BD_'.$i]['Remark']          = $ListRST['Remark'];

			$DateDiff = intval($ListRST['DateDiff']);
			if($DateDiff > 90) {
				$FineALL = $Balance * 0.03;
			} elseif($DateDiff > 60) {
				$FineALL = $Balance * 0.01;
			} elseif($DateDiff > 30) {
				$FineALL = $Balance * 0.005;
			} else {
				$FineALL = 0;
			}

			$FineSAL = $FineALL * 0.7;
			$FineSUP = $FineALL * 0.2;
			$FineMGR = $FineALL * 0.1;

			$arrCol['BD_'.$i]['FineALL'] = number_format($FineALL,0);
			$arrCol['BD_'.$i]['FineSAL'] = number_format($FineSAL,0);
			$arrCol['BD_'.$i]['FineSUP'] = number_format($FineSUP,0);
			$arrCol['BD_'.$i]['FineMGR'] = number_format($FineMGR,0);

			$SUM_CheckSUM = $SUM_CheckSUM + $ListRST['CHQ_Amount'];
			$SUM_Applied  = $SUM_Applied  + $Balance;

			$SUM_FineALL = $SUM_FineALL + $FineALL;
			$SUM_FineSAL = $SUM_FineSAL + $FineSAL;
			$SUM_FineSUP = $SUM_FineSUP + $FineSUP;
			$SUM_FineMGR = $SUM_FineMGR + $FineMGR;

			$i++;
		}

		$arrCol['FT']['SUM_CheckSUM'] = number_format($SUM_CheckSUM,2);
		$arrCol['FT']['SUM_Applied']  = number_format($SUM_Applied,2);
		$arrCol['FT']['SUM_FineALL']  = number_format($SUM_FineALL,0);
		$arrCol['FT']['SUM_FineSAL']  = number_format($SUM_FineSAL,0);
		$arrCol['FT']['SUM_FineSUP']  = number_format($SUM_FineSUP,0);
		$arrCol['FT']['SUM_FineMGR']  = number_format($SUM_FineMGR,0);
	}

	$arrCol['Rows'] = $Rows;
}


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
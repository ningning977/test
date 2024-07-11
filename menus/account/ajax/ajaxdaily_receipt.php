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

function GetLastDate($year,$month) {
    $last_date = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    return $last_date;
}
function thai_date($time){
    global $thai_day_arr;
    $thai_date_return = $thai_day_arr[date("w",$time)];
    return $thai_date_return;
}

if($_GET['a'] == 'CallData') {
	$thai_day_arr = array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
	$count = CHKRowDB("SELECT * FROM log_acreceipt T0 WHERE (YEAR(T0.AC_ReceiptDate) = ".$_POST['Year']." AND MONTH(T0.AC_ReceiptDate) = ".$_POST['Month'].")");
	if($count == 0) {
		$Insert = "INSERT INTO log_acreceipt (AC_ReceiptDate) VALUES ";
		for($d = 1; $d <= GetLastDate($_POST['Year'],$_POST['Month']); $d++) {
			$Insert.= "('".date("Y-m-d",strtotime($_POST['Year']."-".$_POST['Month']."-".$d))."'), ";
		}
		$InsertSQL = substr($Insert,0,-2);
		MySQLInsert($InsertSQL);
	}

	$count = CHKRowDB("SELECT * FROM log_actarget T0 WHERE T0.TG_PeriodYear = ".$_POST['Year']." AND T0.TG_PeriodMonth = ".$_POST['Month']."");
	if($count == 0) {
		$InsertSQL = "INSERT INTO log_actarget (TG_PeriodYear, TG_PeriodMonth) VALUES ('".$_POST['Year']."','".$_POST['Month']."');";
    	MySQLInsert($InsertSQL);
	}

	$genACSQL = "SELECT
                A1.[Date],
                SUM(A1.[OULCASH]) AS 'OULCASH', SUM(A1.[OULTRFR]) AS 'OULTRFR', SUM(A1.[ONL]) AS 'ONL',
                SUM(A1.[TT1]) AS 'TT1', SUM(A1.[TT2]) AS 'TT2', SUM(A1.[MT1]) AS 'MT1', SUM(A1.[MT2]) AS 'MT2'
                FROM (
                    SELECT
                        DAY(A0.[DocDate]) AS 'Date',
                        CASE WHEN (A0.[U_Dim1] = 'OUL' AND A0.[Type] = 'AA-') THEN SUM(A0.[SumApplied]) ELSE 0 END AS 'OULCASH',
                        CASE WHEN (A0.[U_Dim1] = 'OUL' AND A0.[Type] != 'AA-') THEN SUM(A0.[SumApplied]) ELSE 0 END AS 'OULTRFR',
                        CASE WHEN (A0.[U_Dim1] = 'ONL') THEN SUM(A0.[SumApplied]) ELSE 0 END AS 'ONL',
                        CASE WHEN (A0.[U_Dim1] = 'TT1') THEN SUM(A0.[SumApplied]) ELSE 0 END AS 'TT1',
                        CASE WHEN (A0.[U_Dim1] = 'TT2') THEN SUM(A0.[SumApplied]) ELSE 0 END AS 'TT2',
                        CASE WHEN (A0.[U_Dim1] = 'MT1') THEN SUM(A0.[SumApplied]) ELSE 0 END AS 'MT1',
                        CASE WHEN (A0.[U_Dim1] = 'MT2') THEN SUM(A0.[SumApplied]) ELSE 0 END AS 'MT2'
                    FROM (
                        SELECT T0.[DocDate], T1.[SumApplied], T3.[U_Dim1], T4.[BeginStr] AS 'Type'
                        FROM ORCT T0
                        INNER JOIN RCT2 T1 ON T0.[DocEntry] = T1.[DocNum]
                        INNER JOIN OINV T2 ON T1.[DocEntry] = T2.[DocEntry] and T1.[InvType] = 13
                        INNER JOIN OSLP T3 ON T2.[SlpCode] = T3.[SlpCode]
                        LEFT JOIN NNM1 T4 ON T2.[Series] = T4.[Series]
                        WHERE (YEAR(T0.[DocDate]) = ".$_POST['Year']." AND MONTH(T0.[DocDate]) = ".$_POST['Month'].") AND T0.[Canceled] = 'N' AND T3.[U_Dim1] IN ('MT1','MT2','TT1','TT2','ONL')

                        UNION ALL

                        SELECT T0.[DocDate], T1.[SumApplied], T3.[U_Dim1], T4.[BeginStr] AS 'Type'
                        FROM ORCT T0
                        INNER JOIN RCT2 T1 ON T0.[DocEntry] = T1.[DocNum]
                        INNER JOIN OINV T2 ON T1.[DocEntry] = T2.[DocEntry] and T1.[InvType] = 13
                        INNER JOIN OSLP T3 ON T2.[SlpCode] = T3.[SlpCode]
                        LEFT JOIN NNM1 T4 ON T2.[Series] = T4.[Series]
                        WHERE (YEAR(T0.[DocDate]) = ".$_POST['Year']." AND MONTH(T0.[DocDate]) = ".$_POST['Month'].") AND T0.[Canceled] = 'N' AND T3.[U_Dim1] IN ('OUL') AND T4.[BeginStr] != 'AA-'

                        UNION ALL

                        SELECT T0.[DocDate], T0.[DocTotal] AS 'SumApplied', T1.[U_Dim1], T2.[BeginStr] AS 'Type'
                        FROM OINV T0
                        LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
                        LEFT JOIN NNM1 T2 ON T0.[Series] = T2.[Series]
                        WHERE (YEAR(T0.[DocDate]) = ".$_POST['Year']." AND MONTH(T0.[DocDate]) = ".$_POST['Month'].") AND T0.[Canceled] = 'N' AND T1.[U_Dim1] = 'OUL' AND T2.[BeginStr] = 'AA-'

                        UNION ALL

                        SELECT T0.[DocDate], -T1.[SumApplied] AS 'SumApplied', T3.[U_Dim1], T4.[BeginStr] AS 'Type'
                        FROM ORCT T0
                        INNER JOIN RCT2 T1 ON T0.[DocEntry] = T1.[DocNum]
                        INNER JOIN ORIN T2 ON T1.[DocEntry] = T2.[DocEntry] and T1.[InvType] = 14
                        INNER JOIN OSLP T3 ON T2.[SlpCode] = T3.[SlpCode]
                        LEFT JOIN NNM1 T4 ON T2.[Series] = T4.[Series]
                        WHERE (YEAR(T0.[DocDate]) = ".$_POST['Year']." AND MONTH(T0.[DocDate]) = ".$_POST['Month'].") AND T0.[Canceled] = 'N' AND T3.[U_Dim1] IN ('MT1','MT2','TT1','TT2','OUL','ONL')
                    ) A0
                    GROUP BY A0.[DocDate], A0.[U_Dim1], A0.[Type]
                ) A1 GROUP BY A1.[Date] ORDER BY A1.[Date]";
	if($_POST['Year'] <= 2022) {
		$genACQRY = conSAP8($genACSQL);	
	} else {
		$genACQRY = SAPSelect($genACSQL);	
	}
			
	$SUM_OULCASH = 0;
    $SUM_OULTRFR = 0;
    $SUM_ONL = 0;
    $SUM_TT1 = 0;
    $SUM_TT2 = 0;
    $SUM_MT1 = 0;
    $SUM_MT2 = 0;
    $SUM_ALL = 0;		
	while($genACRST = odbc_fetch_array($genACQRY)) {
		if($genACRST['OULCASH'] != 0) { ${"D".$genACRST['Date']."OULCASH"} = number_format($genACRST['OULCASH'],2); } else { ${"D".$genACRST['Date']."OULCASH"} = ""; }
		if($genACRST['OULTRFR'] != 0) { ${"D".$genACRST['Date']."OULTRFR"} = number_format($genACRST['OULTRFR'],2); } else { ${"D".$genACRST['Date']."OULTRFR"} = ""; }
		if($genACRST['ONL'] != 0)     { ${"D".$genACRST['Date']."ONL"}     = number_format($genACRST['ONL'],2);     } else { ${"D".$genACRST['Date']."ONL"} = ""; }
		if($genACRST['TT1'] != 0)     { ${"D".$genACRST['Date']."TT1"}     = number_format($genACRST['TT1'],2);     } else { ${"D".$genACRST['Date']."TT1"} = ""; }
		if($genACRST['TT2'] != 0)     { ${"D".$genACRST['Date']."TT2"}     = number_format($genACRST['TT2'],2);     } else { ${"D".$genACRST['Date']."TT2"} = ""; }
		if($genACRST['MT1'] != 0)     { ${"D".$genACRST['Date']."MT1"}     = number_format($genACRST['MT1'],2);     } else { ${"D".$genACRST['Date']."MT1"} = ""; }
		if($genACRST['MT2'] != 0)     { ${"D".$genACRST['Date']."MT2"}     = number_format($genACRST['MT2'],2);     } else { ${"D".$genACRST['Date']."MT2"} = ""; }
		${"D".$genACRST['Date']."ALL"} = number_format($genACRST['OULCASH']+$genACRST['OULTRFR']+$genACRST['ONL']+$genACRST['TT1']+$genACRST['TT2']+$genACRST['MT1']+$genACRST['MT2'],2);
		$SUM_OULCASH = $SUM_OULCASH+$genACRST['OULCASH'];
    	$SUM_OULTRFR = $SUM_OULTRFR+$genACRST['OULTRFR'];
		$SUM_ONL = $SUM_ONL+$genACRST['ONL'];
		$SUM_TT1 = $SUM_TT1+$genACRST['TT1'];
		$SUM_TT2 = $SUM_TT2+$genACRST['TT2'];
		$SUM_MT1 = $SUM_MT1+$genACRST['MT1'];
		$SUM_MT2 = $SUM_MT2+$genACRST['MT2'];
		$SUM_ALL = $SUM_ALL+$genACRST['OULCASH']+$genACRST['OULTRFR']+$genACRST['ONL']+$genACRST['TT1']+$genACRST['TT2']+$genACRST['MT1']+$genACRST['MT2'];
	}

	$SUM_KBICOST = 0;
	$KBISQL ="SELECT DAY(T1.RefDate) AS 'Date', SUM(T0.Credit) AS 'Credit'
			FROM JDT1 T0
			LEFT JOIN OJDT T1 ON T0.[TransId] = T1.[TransId] 
			WHERE (YEAR(T1.[RefDate]) = ".$_POST['Year']." AND MONTH(T1.[RefDate]) = ".$_POST['Month'].") AND T0.[Credit] > 0 AND
				  (T0.[Account] IN ('1112-05','1113-04','1113-02','2122-01')) AND
				  (T0.[TransType] = 46 OR (T0.[TransType] = 30 AND T0.[BaseRef] LIKE '2%'))
			GROUP BY T1.RefDate ORDER BY DAY(T1.RefDate) ASC";
	if($_POST['Year'] <= 2022) {
		$KBIQRY = conSAP8($KBISQL);	
	} else {
		$KBIQRY = SAPSelect($KBISQL);	
	}
		
	while($kbidata = odbc_fetch_array($KBIQRY)) {
		if($kbidata['Credit'] != 0) { ${"D".$kbidata['Date']."KBICOST"} = number_format($kbidata['Credit'],2); } else { ${"D".$kbidata['Date']."KBICOST"} = ""; }
    	$SUM_KBICOST = $SUM_KBICOST+$kbidata['Credit'];
	}


	$SUM_PTASALE = 0;
    $SUM_PTARECEIPT = 0;
    $SUM_PTACOST = 0;

	/* PITA SALE INVOICE */
	$PTASA_SQL = 
		"SELECT
			DAY(A0.[DocDate]) AS 'Date',
			SUM(A0.[DocTotal]) AS 'AC_PTASale'
		FROM (
			SELECT
				T0.[DocDate], (T0.[DocTotal]-T0.[VatSum]) AS 'DocTotal'
			FROM OINV T0
			WHERE (YEAR(T0.[DocDate]) = ".$_POST['Year']." AND MONTH(T0.[DocDate]) = ".$_POST['Month'].") AND T0.[CANCELED] = 'N'
			UNION ALL
			SELECT
				T0.[DocDate], -(T0.[DocTotal]-T0.[VatSum]) AS 'DocTotal'
			FROM ORIN T0
			WHERE (YEAR(T0.[DocDate]) = ".$_POST['Year']." AND MONTH(T0.[DocDate]) = ".$_POST['Month'].") AND T0.[CANCELED] = 'N'
		) A0
		GROUP BY A0.[DocDate]";
	$PTASA_QRY = PITASelect($PTASA_SQL);
	while($PTASA_RST = odbc_fetch_array($PTASA_QRY)) {
		if($PTASA_RST['AC_PTASale'] != 0)    { ${"D".$PTASA_RST['Date']."PTASALE"}    = number_format($PTASA_RST['AC_PTASale'],2);    } else { ${"D".$PTASA_RST['Date']."PTASALE"} = ""; }
		$SUM_PTASALE = $SUM_PTASALE + $PTASA_RST['AC_PTASale'];
	}
	/* PITA RECEIPT */
	$PTARC_SQL =
		"SELECT
			A1.[Date],
			SUM(A1.[AC_PTAReceipt]) AS 'AC_PTAReceipt'
		FROM (
			SELECT
				DAY(A0.[DocDate]) AS 'Date',
				SUM(A0.[SumApplied]) AS 'AC_PTAReceipt'
			FROM (
				SELECT
					T0.[DocDate], T1.[SumApplied], T3.[U_Dim1], T4.[BeginStr] AS 'Type'
				FROM ORCT T0
				INNER JOIN RCT2 T1 ON T0.[DocEntry] = T1.[DocNum]
				INNER JOIN OINV T2 ON T1.[DocEntry] = T2.[DocEntry] and T1.[InvType] = 13
				INNER JOIN OSLP T3 ON T2.[SlpCode] = T3.[SlpCode]
				LEFT JOIN NNM1 T4 ON T2.[Series] = T4.[Series]
				WHERE (YEAR(T0.[DocDate]) = ".$_POST['Year']." AND MONTH(T0.[DocDate]) = ".$_POST['Month'].") AND T0.[Canceled] = 'N'
				UNION ALL
				SELECT
					T0.[DocDate], -T1.[SumApplied] AS 'SumApplied', T3.[U_Dim1], T4.[BeginStr] AS 'Type'
				FROM ORCT T0
				INNER JOIN RCT2 T1 ON T0.[DocEntry] = T1.[DocNum]
				INNER JOIN ORIN T2 ON T1.[DocEntry] = T2.[DocEntry] and T1.[InvType] = 14
				INNER JOIN OSLP T3 ON T2.[SlpCode] = T3.[SlpCode]
				LEFT JOIN NNM1 T4 ON T2.[Series] = T4.[Series]
				WHERE (YEAR(T0.[DocDate]) = ".$_POST['Year']." AND MONTH(T0.[DocDate]) = ".$_POST['Month'].") AND T0.[Canceled] = 'N'
			) A0
			GROUP BY A0.[DocDate]
		) A1
		GROUP BY A1.[Date]
		ORDER BY A1.[Date]";
	
	$PTARC_QRY = PITASelect($PTARC_SQL);
	while($PTARC_RST = odbc_fetch_array($PTARC_QRY)) {
		if($PTARC_RST['AC_PTAReceipt'] != 0) { ${"D".$PTARC_RST['Date']."PTARECEIPT"} = number_format($PTARC_RST['AC_PTAReceipt'],2); } else { ${"D".$PTARC_RST['Date']."PTARECEIPT"} = ""; }
		$SUM_PTARECEIPT = $SUM_PTARECEIPT + $PTARC_RST['AC_PTAReceipt'];
	}

	/* PITA COST */
	$PTACS_SQL =
		"SELECT
			DAY(T1.RefDate) AS 'Date', SUM(T0.Credit) AS 'AC_PTACost'
		FROM JDT1 T0
		LEFT JOIN OJDT T1 ON T0.[TransId] = T1.[TransId] 
		WHERE (YEAR(T1.[RefDate]) = ".$_POST['Year']." AND MONTH(T1.[RefDate]) = ".$_POST['Month'].") AND T0.[Credit] > 0 AND
			(T0.[Account] IN ('1112-01','1113-01','2120-01')) AND
			(T0.[TransType] = 46 OR (T0.[TransType] = 30 AND T0.[BaseRef] LIKE '2%'))
		GROUP BY T1.RefDate ORDER BY DAY(T1.RefDate) ASC";
	$PTACS_QRY = PITASelect($PTACS_SQL);
	while($PTACS_RST = odbc_fetch_array($PTACS_QRY)) {
		if($PTACS_RST['AC_PTACost'] != 0)    { ${"D".$PTACS_RST['Date']."PTACOST"}    = number_format($PTACS_RST['AC_PTACost'],2);    } else { ${"D".$PTACS_RST['Date']."PTACOST"} = ""; }
		$SUM_PTACOST = $SUM_PTACOST + $PTACS_RST['AC_PTACost'];
	}

	$TARSQL = "SELECT T0.* FROM log_actarget T0 WHERE T0.TG_PeriodYear = '".$_POST['Year']."' AND T0.TG_PeriodMonth = '".$_POST['Month']."' LIMIT 1";
	$TARQRY = MySQLSelectX($TARSQL);
	while($TARRST = mysqli_fetch_array($TARQRY)) {
		if($TARRST['TG_OULAA'] != 0) { $TAR_OULAA = $TARRST['TG_OULAA']; } else { $TAR_OULAA = "0"; }
		if($TARRST['TG_OUL'] != 0)   { $TAR_OUL   = $TARRST['TG_OUL'];   } else { $TAR_OUL = "0"; }
		if($TARRST['TG_ONL'] != 0)   { $TAR_ONL   = $TARRST['TG_ONL'];   } else { $TAR_ONL = "0"; }
		if($TARRST['TG_TT1'] != 0)   { $TAR_TT1   = $TARRST['TG_TT1'];   } else { $TAR_TT1 = "0"; }
		if($TARRST['TG_TT2'] != 0)   { $TAR_TT2   = $TARRST['TG_TT2'];   } else { $TAR_TT2 = "0"; }
		if($TARRST['TG_MT1'] != 0)   { $TAR_MT1   = $TARRST['TG_MT1'];   } else { $TAR_MT1 = "0"; }
		if($TARRST['TG_MT2'] != 0)   { $TAR_MT2   = $TARRST['TG_MT2'];   } else { $TAR_MT2 = "0"; }
		$TAR_ALL = ($TARRST['TG_OULAA'] + $TARRST['TG_OUL'] + $TARRST['TG_ONL'] + $TARRST['TG_TT1'] + $TARRST['TG_TT2'] + $TARRST['TG_MT1'] + $TARRST['TG_MT2']);
	}

	$Tbody = "";
	for($d = 1; $d <= GetLastDate($_POST['Year'],$_POST['Month']); $d++) {
		$thai_date = thai_date(strtotime($_POST['Year']."-".$_POST['Month']."-".$d));
		if($thai_date == "อาทิตย์") { $class = "bg-light-danger text-danger"; } else { $class = ""; }
		$Tbody .=  "<tr class='".$class." fw-bold'>
						<td>".$thai_date."</td>
						<td class='text-center'>".$d."</td>";
						if(isset(${"D".$d."OULCASH"}))    { $Tbody .= "<td class='text-right'>".${"D".$d."OULCASH"}."</td>"; }else{ $Tbody .= "<td></td>"; }
						if(isset(${"D".$d."OULTRFR"}))    { $Tbody .= "<td class='text-right'>".${"D".$d."OULTRFR"}."</td>"; }else{ $Tbody .= "<td></td>"; }
						if(isset(${"D".$d."ONL"}))        { $Tbody .= "<td class='text-right'>".${"D".$d."ONL"}."</td>"; }else{ $Tbody .= "<td></td>"; }
						if(isset(${"D".$d."TT1"}))        { $Tbody .= "<td class='text-right'>".${"D".$d."TT1"}."</td>"; }else{ $Tbody .= "<td></td>"; }
						if(isset(${"D".$d."TT2"}))        { $Tbody .= "<td class='text-right'>".${"D".$d."TT2"}."</td>"; }else{ $Tbody .= "<td></td>"; }
						if(isset(${"D".$d."MT1"}))        { $Tbody .= "<td class='text-right'>".${"D".$d."MT1"}."</td>"; }else{ $Tbody .= "<td></td>"; }
						if(isset(${"D".$d."MT2"}))        { $Tbody .= "<td class='text-right'>".${"D".$d."MT2"}."</td>"; }else{ $Tbody .= "<td></td>"; }
						if(isset(${"D".$d."ALL"}))        { $Tbody .= "<td class='text-right text-success fw-bolder'>".${"D".$d."ALL"}."</td>"; }else{ $Tbody .= "<td class='text-success'></td>"; }
						if(isset(${"D".$d."KBICOST"}))    { $Tbody .= "<td class='text-right text-primary fw-bolder'>".${"D".$d."KBICOST"}."</td>"; }else{ $Tbody .= "<td class='text-primary'></td>"; }
						if(isset(${"D".$d."PTASALE"}))    { $Tbody .= "<td class='text-right'>".${"D".$d."PTASALE"}."</td>"; }else{ $Tbody .= "<td></td>"; }
						if(isset(${"D".$d."PTARECEIPT"})) { $Tbody .= "<td class='text-right text-success'>".${"D".$d."PTARECEIPT"}."</td>"; }else{ $Tbody .= "<td class='text-success'></td>"; }
						if(isset(${"D".$d."PTACOST"}))    { $Tbody .= "<td class='text-right text-primary'>".${"D".$d."PTACOST"}."</td>"; }else{ $Tbody .= "<td class='text-primary'></td>"; }
						// if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP009") {
						// 	$Tbody .= "<td class='text-center'><a href='javascript:void(0);' class='adddata' data-date='".date("Y-m-d",strtotime($_POST['Year']."-".$_POST['Month']."-".$d))."'><i class='far fa-edit'></i></td>";
						// } 
						$Tbody .= "<td>&nbsp;</td>";
		$Tbody .= "</tr>";
	}

	$Tfoot = "<tr class='bg-light fw-bolder text-right'>
				<td class='text-start' colspan='2'>เป้าการเก็บเงิน</td>
				<td>".number_format($TAR_OULAA,2)."</td>
				<td>".number_format($TAR_OUL,2)."</td>
				<td>".number_format($TAR_ONL,2)."</td>
				<td>".number_format($TAR_TT1,2)."</td>
				<td>".number_format($TAR_TT2,2)."</td>
				<td>".number_format($TAR_MT1,2)."</td>
				<td>".number_format($TAR_MT2,2)."</td>
				<td class='text-success'>".number_format($TAR_ALL,2)."</td>
				<td colspan='4'></td>";
				if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP009") {
					$Tfoot .= "<td class='text-center'><a href='javascript:void(0);' class='addtarget' data-year='".$_POST['Year']."' data-month='".$_POST['Month']."'><i class='far fa-edit'></i></td>";
				}
    $Tfoot .="</tr>
			  <tr class='bg-light-success fw-bolder text-right'>
				<td class='text-start' colspan='2'>ยอดเก็บเงินทั้งหมด</td>
				<td>".number_format($SUM_OULCASH,2)."</td>
				<td>".number_format($SUM_OULTRFR,2)."</td>
				<td>".number_format($SUM_ONL,2)."</td>
				<td>".number_format($SUM_TT1,2)."</td>
				<td>".number_format($SUM_TT2,2)."</td>
				<td>".number_format($SUM_MT1,2)."</td>
				<td>".number_format($SUM_MT2,2)."</td>
				<td class='text-success'>".number_format($SUM_ALL,2)."</td>
				<td class='text-primary'>".number_format($SUM_KBICOST,2)."</td>
				<td>".number_format($SUM_PTASALE,2)."</td>
				<td class='text-success'>".number_format($SUM_PTARECEIPT,2)."</td>
				<td class='text-primary'>".number_format($SUM_PTACOST,2)."</td>";
			if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP009") {
				$Tfoot .="<td>&nbsp;</td>";
			}
	$Tfoot .="</tr>";

	if($TAR_OULAA != 0) { $PCNT_OULAA = number_format(($SUM_OULCASH/$TAR_OULAA)*100,2)."%"; } else { $PCNT_OULAA = "-"; }
	if($TAR_OUL != 0)   { $PCNT_OUL   = number_format(($SUM_OULTRFR/$TAR_OUL)*100,2)."%";   } else { $PCNT_OUL = "-"; }
	if($TAR_ONL != 0)   { $PCNT_ONL   = number_format(($SUM_ONL/$TAR_ONL)*100,2)."%";       } else { $PCNT_ONL = "-"; }
	if($TAR_TT1 != 0)   { $PCNT_TT1   = number_format(($SUM_TT1/$TAR_TT1)*100,2)."%";       } else { $PCNT_TT1 = "-"; }
	if($TAR_TT2 != 0)   { $PCNT_TT2   = number_format(($SUM_TT2/$TAR_TT2)*100,2)."%";       } else { $PCNT_TT2 = "-"; }
	if($TAR_MT1 != 0)   { $PCNT_MT1   = number_format(($SUM_MT1/$TAR_MT1)*100,2)."%";       } else { $PCNT_MT1 = "-"; }
	if($TAR_MT2 != 0)   { $PCNT_MT2   = number_format(($SUM_MT2/$TAR_MT2)*100,2)."%";       } else { $PCNT_MT2 = "-"; }
	if($TAR_ALL != 0)   { $PCNT_ALL   = number_format(($SUM_ALL/$TAR_ALL)*100,2)."%";       } else { $PCNT_ALL = "-"; }		  
	$Tfoot .= "<tr class='bg-light fw-bolder text-right'>
				<td class='text-start' colspan='2'>% การเก็บเงินสำเร็จ</td>
				<td>".$PCNT_OULAA."</td>
				<td>".$PCNT_OUL."</td>
				<td>".$PCNT_ONL."</td>
				<td>".$PCNT_TT1."</td>
				<td>".$PCNT_TT2."</td>
				<td>".$PCNT_MT1."</td>
				<td>".$PCNT_MT2."</td>
				<td>".$PCNT_ALL."</td>
				<td colspan='5'>&nbsp;</td>
			  </tr>";

	$arrCol['Tbody'] = $Tbody;
	$arrCol['Tfoot'] = $Tfoot;
}

if($_GET['a'] == 'PickData') {
	$sql = "SELECT T0.AC_PTASale, T0.AC_PTAReceipt, T0.AC_PTACost FROM log_acreceipt T0 WHERE T0.AC_ReceiptDate = '".$_POST['ReceiptDate']."' LIMIT 1";
	$result = MySQLSelect($sql);
	if($result['AC_PTASale'] == 0) { $AC_PTASale = null; } else { $AC_PTASale = $result['AC_PTASale']; }
	if($result['AC_PTAReceipt'] == 0) { $AC_PTAReceipt = null; } else { $AC_PTAReceipt = $result['AC_PTAReceipt']; }
	if($result['AC_PTACost'] == 0) { $AC_PTACost = null; } else { $AC_PTACost = $result['AC_PTACost']; }
	$arrCol['AC_PTASale'] = $AC_PTASale;
    $arrCol['AC_PTAReceipt'] = $AC_PTAReceipt;
    $arrCol['AC_PTACost'] = $AC_PTACost;
}

if($_GET['a'] == 'UpdateData') {
	$AC_PTASale_Value = (float)str_replace(",","",$_POST['AC_PTASale']);
	$AC_PTAReceipt_Value = (float)str_replace(",","",$_POST['AC_PTAReceipt']);
	$AC_PTACost_Value = (float)str_replace(",","",$_POST['AC_PTACost']);
	if($AC_PTASale_Value == "")    { $AC_PTASale = 0; }    else { $AC_PTASale = $AC_PTASale_Value; }
	if($AC_PTAReceipt_Value == "") { $AC_PTAReceipt = 0; } else { $AC_PTAReceipt = $AC_PTAReceipt_Value; }
	if($AC_PTACost_Value == "")    { $AC_PTACost = 0; }    else { $AC_PTACost = $AC_PTACost_Value; }
	// echo $_POST['ReceiptDate']." | ".$AC_PTASale_Value." | ".$AC_PTAReceipt_Value." | ".$AC_PTACost_Value;

	$UpdateSQL = "UPDATE log_acreceipt 
				  SET AC_PTASale = '".$AC_PTASale."', AC_PTAReceipt = ".$AC_PTAReceipt.", AC_PTACost = ".$AC_PTACost.", 
				      UpdateDate = NOW(), UpdateUKey = '".$_SESSION['ukey']."' WHERE AC_ReceiptDate = '".$_POST['ReceiptDate']."'";
	MySQLUpdate($UpdateSQL);
}

if($_GET['a'] == 'PickTarget') {
	$sql = "SELECT T0.TG_OUL, T0.TG_OULAA, T0.TG_ONL, T0.TG_TT1, T0.TG_TT2, T0.TG_MT1, T0.TG_MT2 FROM log_actarget T0 WHERE T0.TG_PeriodYear = '".$_POST['Year']."' AND T0.TG_PeriodMonth = '".$_POST['Month']."' LIMIT 1";
	$result = MySQLSelect($sql);

	$arrCol['TAR_OULAA'] = $result['TG_OULAA'];
    $arrCol['TAR_OUL'] = $result['TG_OUL'];
    $arrCol['TAR_ONL'] = $result['TG_ONL'];
    $arrCol['TAR_TT1'] = $result['TG_TT1'];
    $arrCol['TAR_TT2'] = $result['TG_TT2'];
    $arrCol['TAR_MT1'] = $result['TG_MT1'];
    $arrCol['TAR_MT2'] = $result['TG_MT2'];
}

if($_GET['a'] == 'UpdateTarget') {
	$TAR_OUL_Value = (float)str_replace(",","",$_POST['TAR_OUL']);
	$TAR_OULAA_Value = (float)str_replace(",","",$_POST['TAR_OULAA']);
	$TAR_ONL_Value = (float)str_replace(",","",$_POST['TAR_ONL']);
	$TAR_TT1_Value = (float)str_replace(",","",$_POST['TAR_TT1']);
	$TAR_TT2_Value = (float)str_replace(",","",$_POST['TAR_TT2']);
	$TAR_MT1_Value = (float)str_replace(",","",$_POST['TAR_MT1']);
	$TAR_MT2_Value = (float)str_replace(",","",$_POST['TAR_MT2']);

	if($TAR_OUL_Value == "")    { $TAR_OUL = 0; }    else { $TAR_OUL = $TAR_OUL_Value; }
	if($TAR_OULAA_Value == "")    { $TAR_OULAA = 0; }else { $TAR_OULAA = $TAR_OULAA_Value; }
	if($TAR_ONL_Value == "")    { $TAR_ONL = 0; }    else { $TAR_ONL = $TAR_ONL_Value; }
	if($TAR_TT1_Value == "")    { $TAR_TT1 = 0; }    else { $TAR_TT1 = $TAR_TT1_Value; }
	if($TAR_TT2_Value == "")    { $TAR_TT2 = 0; }    else { $TAR_TT2 = $TAR_TT2_Value; }
	if($TAR_MT1_Value == "")    { $TAR_MT1 = 0; }    else { $TAR_MT1 = $TAR_MT1_Value; }
	if($TAR_MT2_Value == "")    { $TAR_MT2 = 0; }    else { $TAR_MT2 = $TAR_MT2_Value; }

	$UpdateSQL ="UPDATE log_actarget 
				SET TG_OUL = '".$TAR_OUL."', TG_OULAA = '".$TAR_OULAA."', TG_ONL = '".$TAR_ONL."', TG_TT1 = '".$TAR_TT1."', 
				    TG_TT2 = '".$TAR_TT2."', TG_MT1 = '".$TAR_MT1."', TG_MT2 = '".$TAR_MT2."', UpdateDate = NOW(), 
					UKey_Update = '".$_SESSION['ukey']."' WHERE TG_PeriodYear = '".$_POST['Year']."' AND TG_PeriodMonth = '".$_POST['Month']."'";
	MySQLUpdate($UpdateSQL);
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
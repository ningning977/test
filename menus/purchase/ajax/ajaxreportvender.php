<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
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

if($_GET['a'] == 'SLvender') {
	$cYear = $_POST['Year'];
	$pYear = ($cYear-1);
	$arrCol['cYear'] = $cYear;
	$arrCol['pYear'] = $pYear;
	if($cYear >= 2023) {
		switch($_POST['DataSL']) {
			case 1: $wherePart = " "; break;
			case 2: $wherePart = " AND T1.[GroupCode] IN (101,127)"; break;
			case 3: $wherePart = " AND  T1.[GroupCode] IN (126)"; break;
		}
		if($pYear <= 2022) {
			$LastYSQL = "(SELECT SUM(P0.[DocTotal] - P0.[VatSum]) FROM KBI_DB2022.dbo.OPOR P0 WHERE YEAR(P0.[DocDate]) = $pYear AND P0.[CardCode] = T0.[CardCode])";
		} else {
			$LastYSQL = "(SELECT SUM(P0.[DocTotal] - P0.[VatSum]) FROM OPOR P0 WHERE YEAR(P0.[DocDate]) = $pYear AND P0.[CardCode] = T0.[CardCode])";
		}
	}else{
		switch($_POST['DataSL']) {
			case 1: $wherePart = " "; break;
			case 2: $wherePart = " AND T1.[GroupCode] IN (101,109)"; break;
			case 3: $wherePart = " AND  T1.[GroupCode] IN (108)"; break;
		}
		$LastYSQL = "(SELECT SUM(P0.[DocTotal] - P0.[VatSum]) FROM OPOR P0 WHERE YEAR(P0.[DocDate]) = $pYear AND P0.[CardCode] = T0.[CardCode])";
	}

	$sqlDATA = "SELECT W1.CardCode, W1.CardName, W1.GroupCode, W1.LastYear AS LastYear, sum(W1.ThisYear) AS ThisYear,
                        sum(W1.M01) AS M01,sum(W1.M02) AS M02,sum(W1.M03) AS M03,sum(W1.M04) AS M04,sum(W1.M05) AS M05,sum(W1.M06) AS M06,
                        sum(W1.M07) AS M07,sum(W1.M08) AS M08,sum(W1.M09) AS M09,sum(W1.M10) AS M10,sum(W1.M11) AS M11,sum(W1.M12) AS M12
                FROM(
					SELECT
                        T0.[DocDate], T0.[CardCode], T1.[CardName], T1.[GroupCode],  
                        $LastYSQL AS LastYear,
                        CASE WHEN Year(T0.DocDate) = '".$cYear."' THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS ThisYear,
                        CASE WHEN (month(T0.DocDate) = 1 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M01,
                        CASE WHEN (month(T0.DocDate) = 2 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M02,
                        CASE WHEN (month(T0.DocDate) = 3 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M03,
                        CASE WHEN (month(T0.DocDate) = 4 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M04,
                        CASE WHEN (month(T0.DocDate) = 5 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M05,
                        CASE WHEN (month(T0.DocDate) = 6 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M06,
                        CASE WHEN (month(T0.DocDate) = 7 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M07,
                        CASE WHEN (month(T0.DocDate) = 8 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M08,
                        CASE WHEN (month(T0.DocDate) = 9 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M09,
                        CASE WHEN (month(T0.DocDate) = 10 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M10,
                        CASE WHEN (month(T0.DocDate) = 11 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M11,
                        CASE WHEN (month(T0.DocDate) = 12 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M12
                    FROM OPOR T0
                        JOIN OCRD T1 ON T0.[CardCode] = T1.[CardCode]
                    WHERE T0.[CardCode] LIKE 'V-%' AND T0.[DocDate] >= '".$pYear."-01-01' AND T0.[DocDate] <= '".$cYear."-12-31' ".$wherePart." 
				) W1
                GROUP BY W1.CardCode, W1.CardName, W1.GroupCode, W1.LastYear
                ORDER BY GroupCode, CardCode";
	// echo $sqlDATA;
	if($cYear >= 2023) {
		$sapfqry = SAPSelect($sqlDATA);
	} else {
		$sapfqry = conSAP8($sqlDATA);
	}
	
	$Tbody = "";
	$no = 0;
	while($result = odbc_fetch_array($sapfqry)) {
		++$no;
		$Tbody .= "<tr class='fw-bold'>
					<td class='text-center'>".$no."</td>
					<td class=''><a href='javascript:void(0);' onclick=\"DataDetail('".$result['CardCode']."')\"><i class='fas fa-search-plus text-primary'></i></a> ".$result['CardCode']." - ".conutf8($result['CardName'])."</td>";
		// if($pYear <= 2022) {

		// }else{
			if($result['LastYear'] == 0) {
				$Tbody .= "<td class='text-right'>-</td>";
			}else{
				$Tbody .= "<td class='text-right fw-bolder'>".number_format($result['LastYear'],0)."</td>";
			}
		// }

		if($result['ThisYear'] == 0) {
			$Tbody .= "<td class='text-right'>-</td>";
		}else{
			$Tbody .= "<td class='text-right fw-bolder'>".number_format($result['ThisYear'],0)."</td>";
		}

		if ($result['ThisYear'] != 0){
			$gross = number_format((($result['ThisYear']-$result['LastYear'])/$result['ThisYear'])*100,0);
		}else{
			$gross = 0;
		}
		if($gross < 0) { $TextColorgross = "text-danger"; }else{ $TextColorgross = "text-success"; }
		$Tbody .= "<td class='text-right fw-bolder ".$TextColorgross."'>".$gross." %</td>";

		$TextColor = "";
		for($m = 1; $m <= 12; $m++) {
			if($m < 10) {
				// if($result['M0'.$m] < 0) { $TextColor = "text-danger"; }else{ $TextColor = "text-success"; }
				if($result['M0'.$m] == 0) {
					$Tbody .= "<td class='text-right ".$TextColor."'>-</td>";
				}else{
					$Tbody .= "<td class='text-right ".$TextColor."'>".number_format($result['M0'.$m],0)."</td>";
				}
			}else{
				// if($result['M'.$m] < 0) { $TextColor = "text-danger"; }else{ $TextColor = "text-success"; }
				if($result['M'.$m] == 0) {
					$Tbody .= "<td class='text-right ".$TextColor."'>-</td>";
				}else{
					$Tbody .= "<td class='text-right ".$TextColor."'>".number_format($result['M'.$m],0)."</td>";
				}
			}
		}
		$Tbody .= "</tr>";
	}

	if($no == 0) {
		$Tbody = "<tr><td colspan='17'>ไม่มีข้อมูล :(</td></tr>";
	}
	$arrCol['Tbody'] = $Tbody;
} 

if($_GET['a'] == 'DataDetail') {
	$cYear = $_POST['Year'];
	$pYear = ($_POST['Year']-1);
	$sqlThis = "SELECT W1.CardCode, W1.CardName, W1.GroupCode, sum(W1.ThisYear) AS ThisYear,
					sum(W1.M01) AS M01,sum(W1.M02) AS M02,sum(W1.M03) AS M03,sum(W1.M04) AS M04,sum(W1.M05) AS M05,sum(W1.M06) AS M06,
					sum(W1.M07) AS M07,sum(W1.M08) AS M08,sum(W1.M09) AS M09,sum(W1.M10) AS M10,sum(W1.M11) AS M11,sum(W1.M12) AS M12
				FROM 
				(SELECT
						T0.[DocDate], T0.[CardCode], T1.[CardName], T1.[GroupCode],  
						CASE WHEN Year(T0.DocDate) = '".$cYear."' THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS ThisYear,
						CASE WHEN (month(T0.DocDate) = 1 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M01,
						CASE WHEN (month(T0.DocDate) = 2 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M02,
						CASE WHEN (month(T0.DocDate) = 3 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M03,
						CASE WHEN (month(T0.DocDate) = 4 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M04,
						CASE WHEN (month(T0.DocDate) = 5 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M05,
						CASE WHEN (month(T0.DocDate) = 6 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M06,
						CASE WHEN (month(T0.DocDate) = 7 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M07,
						CASE WHEN (month(T0.DocDate) = 8 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M08,
						CASE WHEN (month(T0.DocDate) = 9 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M09,
						CASE WHEN (month(T0.DocDate) = 10 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M10,
						CASE WHEN (month(T0.DocDate) = 11 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M11,
						CASE WHEN (month(T0.DocDate) = 12 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M12
					FROM OPOR T0
						JOIN OCRD T1 ON T0.[CardCode] = T1.[CardCode]
					WHERE T0.[DocDate] >= '".$cYear."-01-01' AND T0.[DocDate] <= '".$cYear."-12-31' AND (T1.[GroupCode] = 108 OR T1.[GroupCode] = 101) AND T0.CardCode = '".$_POST['CardCode']."') W1
				GROUP BY W1.CardCode,W1.CardName,W1.GroupCode
				ORDER BY GroupCode,CardCode";
	if($cYear <= 2022) {
		$QRYThis = conSAP8($sqlThis);
	} else {
		$QRYThis = SAPSelect($sqlThis);
	}
	
	$resultThis = odbc_fetch_array($QRYThis);
	
	$sqlLast = "SELECT W1.CardCode, W1.CardName, W1.GroupCode, sum(W1.LastYear) AS LastYear,
					sum(W1.M01) AS M01,sum(W1.M02) AS M02,sum(W1.M03) AS M03,sum(W1.M04) AS M04,sum(W1.M05) AS M05,sum(W1.M06) AS M06,
					sum(W1.M07) AS M07,sum(W1.M08) AS M08,sum(W1.M09) AS M09,sum(W1.M10) AS M10,sum(W1.M11) AS M11,sum(W1.M12) AS M12
				FROM 
				(SELECT
						T0.[DocDate], T0.[CardCode], T1.[CardName], T1.[GroupCode],  
						CASE WHEN Year(T0.DocDate) = '".$pYear."' THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS LastYear,
						CASE WHEN (month(T0.DocDate) = 1 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M01,
						CASE WHEN (month(T0.DocDate) = 2 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M02,
						CASE WHEN (month(T0.DocDate) = 3 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M03,
						CASE WHEN (month(T0.DocDate) = 4 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M04,
						CASE WHEN (month(T0.DocDate) = 5 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M05,
						CASE WHEN (month(T0.DocDate) = 6 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M06,
						CASE WHEN (month(T0.DocDate) = 7 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M07,
						CASE WHEN (month(T0.DocDate) = 8 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M08,
						CASE WHEN (month(T0.DocDate) = 9 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M09,
						CASE WHEN (month(T0.DocDate) = 10 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M10,
						CASE WHEN (month(T0.DocDate) = 11 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M11,
						CASE WHEN (month(T0.DocDate) = 12 AND YEAR(T0.DocDate) = '".$pYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M12
					FROM OPOR T0
						JOIN OCRD T1 ON T0.[CardCode] = T1.[CardCode]
					WHERE T0.[DocDate] >= '".$pYear."-01-01' AND T0.[DocDate] <= '".$pYear."-12-31' AND (T1.[GroupCode] = 108 OR T1.[GroupCode] = 101) AND T0.CardCode = '".$_POST['CardCode']."') W1
				GROUP BY W1.CardCode,W1.CardName,W1.GroupCode
				ORDER BY GroupCode,CardCode";
	if($pYear <= 2022) {
		$QRYLast = conSAP8($sqlLast);
	} else {
		$QRYLast = SAPSelect($sqlLast);
	}
	
	$resultLast = odbc_fetch_array($QRYLast);

	if(isset($resultThis['CardCode'])) {
		$Tbody1 = "<tr>
					<td class='text-center fw-bolder'>ปี ".$cYear."</td>";
					for($m = 1; $m <= 12; $m++) {
						if($m < 10) {
							if($resultThis['M0'.$m] == 0) {
								$Tbody1 .= "<td class='text-right'>-</td>";
							}else{
								$Tbody1 .= "<td class='text-right'>".number_format($resultThis['M0'.$m],0)."</td>";
							}
						}else{
							if($resultThis['M'.$m] == 0) {
								$Tbody1 .= "<td class='text-right'>-</td>";
							}else{
								$Tbody1 .= "<td class='text-right'>".number_format($resultThis['M'.$m],0)."</td>";
							}
						}
					}
			if($resultThis['ThisYear'] == 0) {
				$Tbody1 .= "<td class='text-right fw-bolder'>-</td>";
			}else{
				$Tbody1 .= "<td class='text-right fw-bolder'>".number_format($resultThis['ThisYear'],0)."</td>";
			}
		$Tbody1 .= "</tr>";
	}else{
		$Tbody1 = "<tr>
					<td class='text-center fw-bolder'>ปี ".$cYear."</td>";
					for($m = 1; $m <= 12; $m++) {
						$Tbody1 .= "<td class='text-right'>-</td>";
					}
				$Tbody1 .= "<td class='text-right fw-bolder'>-</td>";
		$Tbody1 .= "</tr>";
	}

	if(isset($resultLast['CardCode'])) {
		$Tbody1 .= "<tr>
					<td class='text-center fw-bolder'>ปี ".$pYear."</td>";
					for($m = 1; $m <= 12; $m++) {
						if($m < 10) {
							if($resultLast['M0'.$m] == 0) {
								$Tbody1 .= "<td class='text-right'>-</td>";
							}else{
								$Tbody1 .= "<td class='text-right'>".number_format($resultLast['M0'.$m],0)."</td>";
							}
						}else{
							if($resultLast['M'.$m] == 0) {
								$Tbody1 .= "<td class='text-right'>-</td>";
							}else{
								$Tbody1 .= "<td class='text-right'>".number_format($resultLast['M'.$m],0)."</td>";
							}
						}
					}
			$Tbody1 .= "<td class='text-right fw-bolder'>".number_format($resultLast['LastYear'],0)."</td>";
		$Tbody1 .= "</tr>";
	}else{
		$Tbody1 .= "<tr>
					<td class='text-center fw-bolder'>ปี ".$pYear."</td>";
					for($m = 1; $m <= 12; $m++) {
						$Tbody1 .= "<td class='text-right'>-</td>";
					}
			$Tbody1 .= "<td class='text-right fw-bolder'>-</td>";
		$Tbody1 .= "</tr>";
	}

	if(date("m") == 1) {
		$CalYear = $pYear;
	}else{
		$CalYear = $cYear;
	}
	$sql = "SELECT T2.Beginstr, T1.DocNum,T1.NumAtCard, T1.DocDate,T1.CardCode,T0.ItemCode, T0.Dscription, T0.LineNum, T0.Quantity, T0.Price,T0.Currency,T0.LineTotal
			FROM POR1 T0
				JOIN OPOR T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN NNM1 T2 ON T1.Series = T2.Series 
			WHERE  T1.CardCode = '".$_POST['CardCode']."' AND YEAR(T0.DocDate) = '".$CalYear."' 
			ORDER BY T1.DocDate DESC,T0.DocEntry DESC,T0.LineNum";
	if($CalYear <= 2022) {
		$QRY = conSAP8($sql);
	} else {
		$QRY = SAPSelect($sql);
	}
	
	$Tbody2 = "";
	while($result = odbc_fetch_array($QRY)) {
		$Tbody2 .= "<tr>
						<td class='text-center'>".$result['Beginstr'].$result['DocNum']."</td>
						<td class='text-center'>".date("d/m/Y",strtotime($result['DocDate']))."</td>
						<td class='text-center'>".$result['ItemCode']."</td>
						<td class=''>".conutf8($result['Dscription'])."</td>
						<td class='text-right'>".number_format($result['Price'],2)."</td>
						<td class='text-right'>".number_format($result['Quantity'],0)."</td>
						<td class='text-right'>".number_format($result['LineTotal'],2)."</td>
						<td class='text-center'>".$result['Currency']."</td>
					</tr>";
	}

	if(isset($resultThis['CardCode'])) {
		$arrCol['SupCus'] = $resultThis['CardCode']." - ".conutf8($resultThis['CardName']);
	}
	if(isset($resultLast['CardCode'])) {
		$arrCol['SupCus'] = $resultLast['CardCode']." - ".conutf8($resultLast['CardName']);
	}
	$arrCol['Tbody1'] = $Tbody1;
	$arrCol['Tbody2'] = $Tbody2;
	
}

if($_GET['a'] == 'Export') {
	$cYear = $_POST['Year'];
	$pYear = ($cYear-1);

	if($cYear >= 2023) {
		if($pYear <= 2022) {
			$LastYSQL = "(SELECT SUM(P0.[DocTotal] - P0.[VatSum]) FROM KBI_DB2022.dbo.OPOR P0 WHERE YEAR(P0.[DocDate]) = $pYear AND P0.[CardCode] = T0.[CardCode])";
		} else {
			$LastYSQL = "(SELECT SUM(P0.[DocTotal] - P0.[VatSum]) FROM OPOR P0 WHERE YEAR(P0.[DocDate]) = $pYear AND P0.[CardCode] = T0.[CardCode])";
		}
	}else{
		$LastYSQL = "(SELECT SUM(P0.[DocTotal] - P0.[VatSum]) FROM OPOR P0 WHERE YEAR(P0.[DocDate]) = $pYear AND P0.[CardCode] = T0.[CardCode])";
	}

	$sqlDATA = 
		"SELECT W1.CardCode, W1.CardName, W1.GroupCode, W1.LastYear AS LastYear, sum(W1.ThisYear) AS ThisYear,
				sum(W1.M01) AS M01,sum(W1.M02) AS M02,sum(W1.M03) AS M03,sum(W1.M04) AS M04,sum(W1.M05) AS M05,sum(W1.M06) AS M06,
				sum(W1.M07) AS M07,sum(W1.M08) AS M08,sum(W1.M09) AS M09,sum(W1.M10) AS M10,sum(W1.M11) AS M11,sum(W1.M12) AS M12
		FROM(
			SELECT
				T0.[DocDate], T0.[CardCode], T1.[CardName], T1.[GroupCode],  
				$LastYSQL AS LastYear,
				CASE WHEN Year(T0.DocDate) = '".$cYear."' THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS ThisYear,
				CASE WHEN (month(T0.DocDate) = 1 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M01,
				CASE WHEN (month(T0.DocDate) = 2 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M02,
				CASE WHEN (month(T0.DocDate) = 3 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M03,
				CASE WHEN (month(T0.DocDate) = 4 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M04,
				CASE WHEN (month(T0.DocDate) = 5 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M05,
				CASE WHEN (month(T0.DocDate) = 6 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M06,
				CASE WHEN (month(T0.DocDate) = 7 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M07,
				CASE WHEN (month(T0.DocDate) = 8 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M08,
				CASE WHEN (month(T0.DocDate) = 9 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M09,
				CASE WHEN (month(T0.DocDate) = 10 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M10,
				CASE WHEN (month(T0.DocDate) = 11 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M11,
				CASE WHEN (month(T0.DocDate) = 12 AND YEAR(T0.DocDate) = '".$cYear."') THEN (T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS M12
			FROM OPOR T0
				JOIN OCRD T1 ON T0.[CardCode] = T1.[CardCode]
			WHERE T0.[CardCode] LIKE 'V-%' AND T0.[DocDate] >= '".$pYear."-01-01' AND T0.[DocDate] <= '".$cYear."-12-31'
		) W1
		GROUP BY W1.CardCode, W1.CardName, W1.GroupCode, W1.LastYear
		ORDER BY GroupCode, CardCode";
	if($cYear <= 2022) {
		$sapfqry = conSAP8($sqlDATA);
	} else {
		$sapfqry = SAPSelect($sqlDATA);
	}

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานข้อมูลซัพฯ บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานข้อมูลซัพฯ บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	// Header
	$sheet->setCellValue('A1',"ลำดับ");
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"รหัสซัพฯ");
	$spreadsheet->getActiveSheet()->mergeCells('B1:B2');
	$sheet->setCellValue('C1',"ชื่อซัพพลายเออร์");
	$spreadsheet->getActiveSheet()->mergeCells('C1:C2');
	$sheet->setCellValue('D1',"ประเภทซัพฯ");
	$spreadsheet->getActiveSheet()->mergeCells('D1:D2');
	$sheet->setCellValue('E1',"ยอดสั่งซื้อ ".$pYear." (บาท)");
	$spreadsheet->getActiveSheet()->mergeCells('E1:E2');
	$sheet->setCellValue('F1',"ยอดสั่งซื้อ ".$cYear." (บาท)");
	$spreadsheet->getActiveSheet()->mergeCells('F1:F2');
	$sheet->setCellValue('G1',"% การเติบโต");
	$spreadsheet->getActiveSheet()->mergeCells('G1:G2');
	$sheet->setCellValue('H1',"ยอดสั่งซื้อรายเดือน ปี ".$cYear." (บาท)");
	$spreadsheet->getActiveSheet()->mergeCells('H1:S1');
	$mCell = ['0','H','I','J','K','L','M','N','O','P','Q','R','S'];
	for($m = 1; $m <= 12; $m++) {
		$sheet->setCellValue($mCell[$m]."2",txtMonth($m));
	}

	// Add Style Header
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$sheet->getStyle('A1:H1')->applyFromArray($PageHeader);
	$sheet->getStyle('H2:S2')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(54);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(13);
	for($m = 1; $m <= 12; $m++) {
		$spreadsheet->getActiveSheet()->getColumnDimension($mCell[$m])->setWidth(13);
	}
	$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);
	$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(18);

	// Style Body
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$Row = 2; $No = 0;
	while($result = odbc_fetch_array($sapfqry)) {
		$Row++; $No++;

		// ลำดับ
		$sheet->setCellValue('A'.$Row,$No);
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		// รหัสซัพฯ
		$sheet->setCellValue('B'.$Row,$result['CardCode']);
		$sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);

		// รายชื่อซัพพลายเออร์
		$sheet->setCellValue('C'.$Row,conutf8($result['CardName']));

		// ประเภทซัพฯ
		if($cYear >= 2023) {
			switch($result['GroupCode']) {
				case 101:
				case 127: $sheet->setCellValue('D'.$Row,'ในประเทศ');  break;
				case 126: $sheet->setCellValue('D'.$Row,'ต่างประเทศ'); break;
			}
		}else{
			switch($result['GroupCode']) {
				case 101:
				case 109: $sheet->setCellValue('D'.$Row,'ในประเทศ');  break;
				case 108: $sheet->setCellValue('D'.$Row,'ต่างประเทศ'); break;
			}
		}
		$sheet->getStyle('D'.$Row)->applyFromArray($TextCenter);

		// ยอดสั่งซื้อ pYear (บาท)
		if($result['LastYear'] == 0) {
			$sheet->setCellValue('E'.$Row,'-');
		}else{
			$sheet->setCellValue('E'.$Row,$result['LastYear']);
			$spreadsheet->getActiveSheet()->getStyle('E'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}
		$sheet->getStyle('E'.$Row)->applyFromArray($TextRight);

		// ยอดสั่งซื้อ cYear (บาท)
		if($result['ThisYear'] == 0) {
			$sheet->setCellValue('F'.$Row,'-');
		}else{
			$sheet->setCellValue('F'.$Row,$result['ThisYear']);
			$spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}
		$sheet->getStyle('F'.$Row)->applyFromArray($TextRight);

		// %
		if ($result['ThisYear'] != 0){
			$gross = (($result['ThisYear']-$result['LastYear'])/$result['ThisYear']);
		}else{
			$gross = 0;
		}
		$sheet->setCellValue('G'.$Row,$gross);
		$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);
		if($gross < 0) { 
			$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getFont()->getColor()->setARGB('ff9a1118');
		}else{ 
			$spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getFont()->getColor()->setARGB('ff1f8f38');
		}
		$sheet->getStyle('G'.$Row)->applyFromArray($TextRight);
		
		// ยอดสั่งซื้อรายเดือน ปี cYear (บาท) เดือน 1-12 
		for($m = 1; $m <= 12; $m++){
			if($m < 10) {
				if($result['M0'.$m] == 0) {
					$sheet->setCellValue($mCell[$m].$Row,'-');
				}else{
					$sheet->setCellValue($mCell[$m].$Row,$result['M0'.$m]);
					$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0");
				}
			}else{
				if($result['M'.$m] == 0) {
					$sheet->setCellValue($mCell[$m].$Row,'-');
				}else{
					$sheet->setCellValue($mCell[$m].$Row,$result['M'.$m]);
					$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0");
				}
			}
			$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
		}
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานข้อมูลซัพฯ - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/SupData/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'SupData', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
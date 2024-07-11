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

// รายงานการขาย
	if ($_GET['a'] == 'SelectYearAll'){
		$TeamSelect = $_POST['Team'];
		$YearSelect = $_POST['Year'];
		$YearPrev = $YearSelect-1;
		// ------------------------------------------------------------------------ ALL --------------------------------------------------------------------------
		if ($TeamSelect == "all") {
			// ======= ข้อมูลยอดขายปีที่เลือก ======= //
			$sqlSelect ="SELECT 	A1.[TEAM],
								SUM(A1.[M_01]) AS 'M_01', SUM(A1.[M_02]) AS 'M_02', SUM(A1.[M_03]) AS 'M_03', SUM(A1.[M_04]) AS 'M_04',
								SUM(A1.[M_05]) AS 'M_05', SUM(A1.[M_06]) AS 'M_06', SUM(A1.[M_07]) AS 'M_07', SUM(A1.[M_08]) AS 'M_08',
								SUM(A1.[M_09]) AS 'M_09', SUM(A1.[M_10]) AS 'M_10', SUM(A1.[M_11]) AS 'M_11', SUM(A1.[M_12]) AS 'M_12'
						FROM (
								SELECT
										T1.[U_Dim1] AS 'TEAM',
										CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
										CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
										CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
										CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
										CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
										CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
										CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
										CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
										CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
										CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
										CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
										CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12'
								FROM OINV T0
								LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
								WHERE YEAR(T0.[DocDate]) = '".$YearSelect."' AND T0.CANCELED = 'N'
								GROUP BY T1.[U_Dim1], MONTH(T0.[DocDate])
								UNION ALL
								SELECT
										T1.[U_Dim1] AS 'TEAM',
										CASE WHEN MONTH(T0.[DocDate]) = 1 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
										CASE WHEN MONTH(T0.[DocDate]) = 2 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
										CASE WHEN MONTH(T0.[DocDate]) = 3 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
										CASE WHEN MONTH(T0.[DocDate]) = 4 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
										CASE WHEN MONTH(T0.[DocDate]) = 5 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
										CASE WHEN MONTH(T0.[DocDate]) = 6 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
										CASE WHEN MONTH(T0.[DocDate]) = 7 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
										CASE WHEN MONTH(T0.[DocDate]) = 8 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
										CASE WHEN MONTH(T0.[DocDate]) = 9 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
										CASE WHEN MONTH(T0.[DocDate]) = 10 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
										CASE WHEN MONTH(T0.[DocDate]) = 11 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
										CASE WHEN MONTH(T0.[DocDate]) = 12 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12'
								FROM ORIN T0
								LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
								LEFT JOIN NNM1 T2 ON T0.[Series] = T2.[Series]
								WHERE YEAR(T0.[DocDate]) = '".$YearSelect."' AND T0.CANCELED = 'N'
								GROUP BY T1.[U_Dim1], MONTH(T0.[DocDate])
						) A1
						GROUP BY A1.[Team]
						ORDER BY CASE
								WHEN A1.[Team] = 'MT1' THEN 1
								WHEN A1.[Team] = 'MT2' THEN 2
								WHEN A1.[Team] = 'TT1' THEN 3
								WHEN A1.[Team] = 'TT2' THEN 4
								WHEN A1.[Team] = 'OUL' THEN 5
								WHEN A1.[Team] = 'ONL' THEN 6
								WHEN A1.[Team] = 'EI1' THEN 7
								WHEN A1.[Team] = 'EXP' THEN 8
								WHEN A1.[Team] = 'MKT' THEN 9
								WHEN A1.[Team] = 'DMN' THEN 10
								ELSE 11
						END";
			if($YearSelect <= 2022) {
				$sqlSelectQRY = conSAP8($sqlSelect);
			}else{
				$sqlSelectQRY = SAPSelect($sqlSelect);
			}
			$TEAM=""; $M_1=""; $M_2=""; $M_3=""; $M_4=""; $M_5=""; $M_6=""; $M_7=""; $M_8=""; $M_9=""; $M_10=""; $M_11=""; $M_12="";
			while ($result = odbc_fetch_array($sqlSelectQRY)) {
				$TEAM .= SATeamName($result['TEAM']).'|';
				$M_1 .= $result['M_01'].'|';
				$M_2 .= $result['M_02'].'|';
				$M_3 .= $result['M_03'].'|';
				$M_4 .= $result['M_04'].'|';
				$M_5 .= $result['M_05'].'|';
				$M_6 .= $result['M_06'].'|';
				$M_7 .= $result['M_07'].'|';
				$M_8 .= $result['M_08'].'|';
				$M_9 .= $result['M_09'].'|';
				$M_10 .= $result['M_10'].'|';
				$M_11 .= $result['M_11'].'|';
				$M_12 .= $result['M_12'].'|';
			}
			$arrCol['TeamSelect'] = $TeamSelect;
			$arrCol['YearSelect'] = $YearSelect;
			$arrCol['TEAM'] = $TEAM;
			$arrCol['M_1'] = $M_1;
			$arrCol['M_2'] = $M_2;
			$arrCol['M_3'] = $M_3;
			$arrCol['M_4'] = $M_4;
			$arrCol['M_5'] = $M_5;
			$arrCol['M_6'] = $M_6;
			$arrCol['M_7'] = $M_7;
			$arrCol['M_8'] = $M_8;
			$arrCol['M_9'] = $M_9;
			$arrCol['M_10'] = $M_10;
			$arrCol['M_11'] = $M_11;
			$arrCol['M_12'] = $M_12;

			// ======= ข้อมูลยอดขายปีที่เลือก -1 ======= //
			if ($YearSelect != 2015) {
				$sqlYearPrev ="SELECT  A1.[TEAM],
										SUM(A1.[M_01]) AS 'M_01', SUM(A1.[M_02]) AS 'M_02', SUM(A1.[M_03]) AS 'M_03', SUM(A1.[M_04]) AS 'M_04',
										SUM(A1.[M_05]) AS 'M_05', SUM(A1.[M_06]) AS 'M_06', SUM(A1.[M_07]) AS 'M_07', SUM(A1.[M_08]) AS 'M_08',
										SUM(A1.[M_09]) AS 'M_09', SUM(A1.[M_10]) AS 'M_10', SUM(A1.[M_11]) AS 'M_11', SUM(A1.[M_12]) AS 'M_12'
								FROM (
										SELECT 
											T1.[U_Dim1] AS 'TEAM',
											CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
											CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
											CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
											CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
											CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
											CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
											CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
											CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
											CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
											CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
											CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
											CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
										FROM OINV T0
										LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode] 
										WHERE YEAR(T0.[DocDate]) = '$YearPrev' AND T0.CANCELED = 'N'
										GROUP BY T1.[U_Dim1], MONTH(T0.[DocDate])
										UNION ALL 
										SELECT 
											T1.[U_Dim1] AS 'TEAM',
											CASE WHEN MONTH(T0.[DocDate]) = 1 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
											CASE WHEN MONTH(T0.[DocDate]) = 2 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
											CASE WHEN MONTH(T0.[DocDate]) = 3 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
											CASE WHEN MONTH(T0.[DocDate]) = 4 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
											CASE WHEN MONTH(T0.[DocDate]) = 5 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
											CASE WHEN MONTH(T0.[DocDate]) = 6 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
											CASE WHEN MONTH(T0.[DocDate]) = 7 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
											CASE WHEN MONTH(T0.[DocDate]) = 8 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
											CASE WHEN MONTH(T0.[DocDate]) = 9 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
											CASE WHEN MONTH(T0.[DocDate]) = 10 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
											CASE WHEN MONTH(T0.[DocDate]) = 11 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
											CASE WHEN MONTH(T0.[DocDate]) = 12 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
										FROM ORIN T0
										LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
										LEFT JOIN NNM1 T2 ON T0.[Series] = T2.[Series]
										WHERE YEAR(T0.[DocDate]) = '$YearPrev' AND T0.CANCELED = 'N'
										GROUP BY T1.[U_Dim1], MONTH(T0.[DocDate])
								) A1
								GROUP BY A1.[Team]
								ORDER BY CASE
										WHEN A1.[Team] = 'MT1' THEN 1
										WHEN A1.[Team] = 'MT2' THEN 2
										WHEN A1.[Team] = 'TT1' THEN 3
										WHEN A1.[Team] = 'TT2' THEN 4
										WHEN A1.[Team] = 'OUL' THEN 5
										WHEN A1.[Team] = 'ONL' THEN 6
										WHEN A1.[Team] = 'EI1' THEN 7
										WHEN A1.[Team] = 'EXP' THEN 8
										WHEN A1.[Team] = 'MKT' THEN 9
										WHEN A1.[Team] = 'DMN' THEN 10
										ELSE 11
								END";
				if($YearPrev <= 2022) {
					$sqlYearPrevQRY = conSAP8($sqlYearPrev);
				}else{
					$sqlYearPrevQRY = SAPSelect($sqlYearPrev);
				}
				
				$SM_1=0; $SM_2=0; $SM_3=0; $SM_4=0; $SM_5=0; $SM_6=0; $SM_7=0; $SM_8=0; $SM_9=0; $SM_10=0; $SM_11=0; $SM_12=0;
				while ($result = odbc_fetch_array($sqlYearPrevQRY)) {
					$SM_1 += $result['M_01'];
					$SM_2 += $result['M_02'];
					$SM_3 += $result['M_03'];
					$SM_4 += $result['M_04'];
					$SM_5 += $result['M_05'];
					$SM_6 += $result['M_06'];
					$SM_7 += $result['M_07'];
					$SM_8 += $result['M_08'];
					$SM_9 += $result['M_09'];
					$SM_10 += $result['M_10'];
					$SM_11 += $result['M_11'];
					$SM_12 += $result['M_12'];
				}
				$arrCol['YearPrev'] = $YearPrev;
				$arrCol['SM_1'] = $SM_1;
				$arrCol['SM_2'] = $SM_2;
				$arrCol['SM_3'] = $SM_3;
				$arrCol['SM_4'] = $SM_4;
				$arrCol['SM_5'] = $SM_5;
				$arrCol['SM_6'] = $SM_6;
				$arrCol['SM_7'] = $SM_7;
				$arrCol['SM_8'] = $SM_8;
				$arrCol['SM_9'] = $SM_9;
				$arrCol['SM_10'] = $SM_10;
				$arrCol['SM_11'] = $SM_11;
				$arrCol['SM_12'] = $SM_12;
			}
		}

		// ------------------------------------------------------------------------ Team -------------------------------------------------------------------------
		if($TeamSelect != "all") {
			// ======= ข้อมูลยอดขายปีที่เลือก ======= //
			if($YearSelect <= 2022) {
				$GroupCodeSQL = 
					"CASE
						WHEN T2.[GroupCode] = '106' THEN 'G1'
						WHEN T2.[GroupCode] = '107' THEN 'G2'
						WHEN T2.[GroupCode] = '102' THEN 'G3'
						WHEN T2.[GroupCode] = '103' THEN 'G4'
						WHEN T2.[GroupCode] = '127' THEN 'G5'
						WHEN T2.[GroupCode] IN ('104','116','117', '118','122','129') THEN 'G6'
						WHEN T2.[GroupCode] = '100' THEN 'G7'
						WHEN T2.[GroupCode] = '125' THEN 'G8'
						WHEN T2.[GroupCode] = '128' THEN 'G9'
					ELSE 'G10' END";
			} else {
				$GroupCodeSQL = 
					"CASE
						WHEN T2.[GroupCode] = '106' THEN 'G1'
						WHEN T2.[GroupCode] = '107' THEN 'G2'
						WHEN T2.[GroupCode] = '102' THEN 'G3'
						WHEN T2.[GroupCode] = '103' THEN 'G4'
						WHEN T2.[GroupCode] = '122' THEN 'G5'
						WHEN T2.[GroupCode] IN ('104','111','112', '113','117','124') THEN 'G6'
						WHEN T2.[GroupCode] = '100' THEN 'G7'
						WHEN T2.[GroupCode] = '120' THEN 'G8'
						WHEN T2.[GroupCode] = '123' THEN 'G9'
					ELSE 'G10' END";
			}
			$sqlSelect = "SELECT
								A1.[Team], A1.[CusGroup],
								SUM(A1.[M_01]) AS 'M_01', SUM(A1.[M_02]) AS 'M_02', SUM(A1.[M_03]) AS 'M_03', SUM(A1.[M_04]) AS 'M_04',
								SUM(A1.[M_05]) AS 'M_05', SUM(A1.[M_06]) AS 'M_06', SUM(A1.[M_07]) AS 'M_07', SUM(A1.[M_08]) AS 'M_08',
								SUM(A1.[M_09]) AS 'M_09', SUM(A1.[M_10]) AS 'M_10', SUM(A1.[M_11]) AS 'M_11', SUM(A1.[M_12]) AS 'M_12'
						FROM (
								SELECT
									T1.[U_Dim1] AS 'Team', 
									$GroupCodeSQL AS 'CusGroup',
									CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
									CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
									CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
									CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
									CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
									CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
									CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
									CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
									CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
									CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
									CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
									CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
								FROM OINV T0
								LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
								LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode] 
								WHERE YEAR(T0.[DocDate]) = '".$YearSelect."' AND T0.CANCELED = 'N'
								GROUP BY T1.[U_Dim1], T2.[GroupCode], MONTH(T0.[DocDate])
								UNION ALL 
								SELECT
									T1.[U_Dim1] AS 'Team', 
									$GroupCodeSQL AS 'CusGroup',
									CASE WHEN MONTH(T0.[DocDate]) = 1 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
									CASE WHEN MONTH(T0.[DocDate]) = 2 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
									CASE WHEN MONTH(T0.[DocDate]) = 3 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
									CASE WHEN MONTH(T0.[DocDate]) = 4 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
									CASE WHEN MONTH(T0.[DocDate]) = 5 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
									CASE WHEN MONTH(T0.[DocDate]) = 6 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
									CASE WHEN MONTH(T0.[DocDate]) = 7 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
									CASE WHEN MONTH(T0.[DocDate]) = 8 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
									CASE WHEN MONTH(T0.[DocDate]) = 9 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
									CASE WHEN MONTH(T0.[DocDate]) = 10 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
									CASE WHEN MONTH(T0.[DocDate]) = 11 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
									CASE WHEN MONTH(T0.[DocDate]) = 12 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
								FROM ORIN T0
								LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
								LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode]
								LEFT JOIN NNM1 T3 ON T0.[Series] = T3.[Series]
								WHERE YEAR(T0.[DocDate]) = '".$YearSelect."' AND T0.CANCELED = 'N'
								GROUP BY T1.[U_Dim1], T2.[GroupCode], MONTH(T0.[DocDate])
						) A1
						WHERE A1.[Team] = '".$TeamSelect."'
						GROUP BY A1.[Team], A1.[CusGroup]
						ORDER BY
						CASE
							WHEN A1.[CusGroup] = 'G1' THEN 1
							WHEN A1.[CusGroup] = 'G2' THEN 2
							WHEN A1.[CusGroup] = 'G3' THEN 3
							WHEN A1.[CusGroup] = 'G4' THEN 4
							WHEN A1.[CusGroup] = 'G5' THEN 5
							WHEN A1.[CusGroup] = 'G6' THEN 6
							WHEN A1.[CusGroup] = 'G7' THEN 7
							WHEN A1.[CusGroup] = 'G8' THEN 8
							WHEN A1.[CusGroup] = 'G9' THEN 9
						ELSE 10 END";
			if($YearSelect <= 2022) {
				$sqlSelectQRY = conSAP8($sqlSelect);
			}else{
				$sqlSelectQRY = SAPSelect($sqlSelect);
			}
			$TEAM=""; $M_1=""; $M_2=""; $M_3=""; $M_4=""; $M_5=""; $M_6=""; $M_7=""; $M_8=""; $M_9=""; $M_10=""; $M_11=""; $M_12="";
			while ($result = odbc_fetch_array($sqlSelectQRY)) {
				$TEAM .= GroupName_Th($result['CusGroup']).'|';
				$M_1 .= $result['M_01'].'|';
				$M_2 .= $result['M_02'].'|';
				$M_3 .= $result['M_03'].'|';
				$M_4 .= $result['M_04'].'|';
				$M_5 .= $result['M_05'].'|';
				$M_6 .= $result['M_06'].'|';
				$M_7 .= $result['M_07'].'|';
				$M_8 .= $result['M_08'].'|';
				$M_9 .= $result['M_09'].'|';
				$M_10 .= $result['M_10'].'|';
				$M_11 .= $result['M_11'].'|';
				$M_12 .= $result['M_12'].'|';
			}
			$arrCol['TeamSelect'] = $TeamSelect;
			$arrCol['YearSelect'] = $YearSelect;
			$arrCol['TEAM'] = $TEAM;
			$arrCol['M_1'] = $M_1;
			$arrCol['M_2'] = $M_2;
			$arrCol['M_3'] = $M_3;
			$arrCol['M_4'] = $M_4;
			$arrCol['M_5'] = $M_5;
			$arrCol['M_6'] = $M_6;
			$arrCol['M_7'] = $M_7;
			$arrCol['M_8'] = $M_8;
			$arrCol['M_9'] = $M_9;
			$arrCol['M_10'] = $M_10;
			$arrCol['M_11'] = $M_11;
			$arrCol['M_12'] = $M_12;

			// ======= ข้อมูลยอดขายปีที่เลือก -1 ======= //
			if ($YearSelect != 2015) {
				$sqlYearPrev ="SELECT
										A1.[Team], A1.[CusGroup],
										SUM(A1.[M_01]) AS 'M_01', SUM(A1.[M_02]) AS 'M_02', SUM(A1.[M_03]) AS 'M_03', SUM(A1.[M_04]) AS 'M_04',
										SUM(A1.[M_05]) AS 'M_05', SUM(A1.[M_06]) AS 'M_06', SUM(A1.[M_07]) AS 'M_07', SUM(A1.[M_08]) AS 'M_08',
										SUM(A1.[M_09]) AS 'M_09', SUM(A1.[M_10]) AS 'M_10', SUM(A1.[M_11]) AS 'M_11', SUM(A1.[M_12]) AS 'M_12'
								FROM (
										SELECT
											T1.[U_Dim1] AS 'Team', 
											$GroupCodeSQL AS 'CusGroup',
											CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
											CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
											CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
											CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
											CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
											CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
											CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
											CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
											CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
											CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
											CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
											CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
										FROM OINV T0
										LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
										LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode] 
										WHERE YEAR(T0.[DocDate]) = '".$YearPrev."' AND T0.CANCELED = 'N'
										GROUP BY T1.[U_Dim1], T2.[GroupCode], MONTH(T0.[DocDate])
										UNION ALL 
										SELECT
											T1.[U_Dim1] AS 'Team', 
											$GroupCodeSQL AS 'CusGroup',
											CASE WHEN MONTH(T0.[DocDate]) = 1 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
											CASE WHEN MONTH(T0.[DocDate]) = 2 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
											CASE WHEN MONTH(T0.[DocDate]) = 3 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
											CASE WHEN MONTH(T0.[DocDate]) = 4 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
											CASE WHEN MONTH(T0.[DocDate]) = 5 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
											CASE WHEN MONTH(T0.[DocDate]) = 6 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
											CASE WHEN MONTH(T0.[DocDate]) = 7 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
											CASE WHEN MONTH(T0.[DocDate]) = 8 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
											CASE WHEN MONTH(T0.[DocDate]) = 9 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
											CASE WHEN MONTH(T0.[DocDate]) = 10 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
											CASE WHEN MONTH(T0.[DocDate]) = 11 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
											CASE WHEN MONTH(T0.[DocDate]) = 12 THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
										FROM ORIN T0
										LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
										LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode]
										LEFT JOIN NNM1 T3 ON T0.[Series] = T3.[Series]
										WHERE YEAR(T0.[DocDate]) = '".$YearPrev."' AND T0.CANCELED = 'N'
										GROUP BY T1.[U_Dim1], T2.[GroupCode], MONTH(T0.[DocDate])
								) A1
								WHERE A1.[Team] = '".$TeamSelect."'
								GROUP BY A1.[Team], A1.[CusGroup]
								ORDER BY
								CASE
									WHEN A1.[CusGroup] = 'G1' THEN 1
									WHEN A1.[CusGroup] = 'G2' THEN 2
									WHEN A1.[CusGroup] = 'G3' THEN 3
									WHEN A1.[CusGroup] = 'G4' THEN 4
									WHEN A1.[CusGroup] = 'G5' THEN 5
									WHEN A1.[CusGroup] = 'G6' THEN 6
									WHEN A1.[CusGroup] = 'G7' THEN 7
									WHEN A1.[CusGroup] = 'G8' THEN 8
									WHEN A1.[CusGroup] = 'G9' THEN 9
								ELSE 10 END";
				if($YearPrev <= 2022) {
					$sqlYearPrevQRY = conSAP8($sqlYearPrev);
				}else{
					$sqlYearPrevQRY = SAPSelect($sqlYearPrev);
				}
				$SM_1=0; $SM_2=0; $SM_3=0; $SM_4=0; $SM_5=0; $SM_6=0; $SM_7=0; $SM_8=0; $SM_9=0; $SM_10=0; $SM_11=0; $SM_12=0;
				while ($result = odbc_fetch_array($sqlYearPrevQRY)) {
				$SM_1 += $result['M_01'];
				$SM_2 += $result['M_02'];
				$SM_3 += $result['M_03'];
				$SM_4 += $result['M_04'];
				$SM_5 += $result['M_05'];
				$SM_6 += $result['M_06'];
				$SM_7 += $result['M_07'];
				$SM_8 += $result['M_08'];
				$SM_9 += $result['M_09'];
				$SM_10 += $result['M_10'];
				$SM_11 += $result['M_11'];
				$SM_12 += $result['M_12'];
				}
				$arrCol['YearPrev'] = $YearPrev;
				$arrCol['SM_1'] = $SM_1;
				$arrCol['SM_2'] = $SM_2;
				$arrCol['SM_3'] = $SM_3;
				$arrCol['SM_4'] = $SM_4;
				$arrCol['SM_5'] = $SM_5;
				$arrCol['SM_6'] = $SM_6;
				$arrCol['SM_7'] = $SM_7;
				$arrCol['SM_8'] = $SM_8;
				$arrCol['SM_9'] = $SM_9;
				$arrCol['SM_10'] = $SM_10;
				$arrCol['SM_11'] = $SM_11;
				$arrCol['SM_12'] = $SM_12;
			}
		}
	}
		// ------------------------------------------------------------------------ Modal -------------------------------------------------------------------------
	if ($_GET['a'] == 'SelectGroup'){
		$TeamSelect = $_POST['Team'];
		$GroupSelect = $_POST['Group'];
		$YearCurrent = $_POST['Year'];
		$YearPrev = $YearCurrent-1;
		$sqlSelect ="SELECT
						A1.[CardCode], A1.[CardName], A3.[SlpName], SUM(A1.[Prev_Total]) AS 'Prev_Total', SUM(A1.[Curr_Total]) AS 'Curr_Total',
						SUM(A1.[Curr_M01]) AS 'Curr_M01',SUM(A1.[Curr_M02]) AS 'Curr_M02',SUM(A1.[Curr_M03]) AS 'Curr_M03',
						SUM(A1.[Curr_M04]) AS 'Curr_M04',SUM(A1.[Curr_M05]) AS 'Curr_M05',SUM(A1.[Curr_M06]) AS 'Curr_M06',
						SUM(A1.[Curr_M07]) AS 'Curr_M07',SUM(A1.[Curr_M08]) AS 'Curr_M08',SUM(A1.[Curr_M09]) AS 'Curr_M09',
						SUM(A1.[Curr_M10]) AS 'Curr_M10',SUM(A1.[Curr_M11]) AS 'Curr_M11',SUM(A1.[Curr_M12]) AS 'Curr_M12',
						SUM(A1.[Curr_PFT]) AS 'Curr_PFT'
					FROM (
					SELECT
						T0.[CardCode], T2.[CardName],
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearPrev."' AND MONTH(T0.[DocDate]) <= MONTH(GETDATE()) THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Prev_Total',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_Total',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '1' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M01',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '2' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M02',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '3' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M03',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '4' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M04',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '5' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M05',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '6' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M06',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '7' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M07',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '8' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M08',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '9' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M09',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '10' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M10',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '11' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M11',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '12' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M12',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' THEN SUM(T0.[GrosProfit]) ELSE 0 END AS 'Curr_PFT'
					FROM OINV T0
					LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
					LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode]
					WHERE T0.[CANCELED] = 'N' AND (YEAR(T0.[DocDate]) BETWEEN '".$YearPrev."' AND '".$YearCurrent."') AND T1.[U_Dim1] = '".$TeamSelect."' AND ".GroupCodeReturn($GroupSelect,$YearCurrent)."
					GROUP BY YEAR(T0.[DocDate]), MONTH(T0.[DocDate]), T0.[CardCode], T2.[CardName]
					UNION ALL
					SELECT
						T0.[CardCode], T2.[CardName],
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearPrev."' AND MONTH(T0.[DocDate]) <= MONTH(GETDATE()) THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Prev_Total',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_Total',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '1' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M01',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '2' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M02',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '3' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M03',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '4' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M04',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '5' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M05',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '6' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M06',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '7' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M07',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '8' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M08',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '9' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M09',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '10' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M10',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '11' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M11',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' AND MONTH(T0.[DocDate]) = '12' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_M12',
						CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' THEN -SUM(T0.[GrosProfit]) ELSE 0 END AS 'Curr_PFT'
					FROM ORIN T0
					LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
					LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode]
					LEFT JOIN NNM1 T3 ON T0.[Series] = T3.[Series]
					WHERE T0.[CANCELED] = 'N' AND (YEAR(T0.[DocDate]) BETWEEN '".$YearPrev."' AND '".$YearCurrent."') AND T1.[U_Dim1] = '".$TeamSelect."' AND ".GroupCodeReturn($GroupSelect,$YearCurrent)."
					GROUP BY YEAR(T0.[DocDate]), MONTH(T0.[DocDate]), T0.[CardCode], T2.[CardName]
					) A1
					LEFT JOIN OCRD A2 ON A1.CardCode = A2.CardCode
					LEFT JOIN OSLP A3 ON A2.SlpCode  = A3.SlpCode
					GROUP BY A1.[CardCode], A1.[CardName], A3.[SlpName]
					ORDER BY Curr_Total DESC, Prev_Total DESC";
		if($YearCurrent <= 2022) {
			$sqlSelectQRY = conSAP8($sqlSelect);
		}else{
			$sqlSelectQRY = SAPSelect($sqlSelect);
		}

		if($YearPrev <= 2022) {
			$sql_prev ="SELECT A1.[CardCode], SUM(A1.[Prev_Total]) AS 'Prev_Total', SUM(A1.[Curr_Total]) AS 'Curr_Total'
						FROM (
								SELECT
									T0.[CardCode], T2.[CardName],
									CASE WHEN YEAR(T0.[DocDate]) = '".$YearPrev."' AND MONTH(T0.[DocDate]) <= MONTH(GETDATE()) THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Prev_Total',
									CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_Total'
								FROM OINV T0
								LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
								LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode]
								WHERE T0.[CANCELED] = 'N' AND (YEAR(T0.[DocDate]) BETWEEN '".$YearPrev."' AND '".$YearCurrent."') AND T1.[U_Dim1] = '".$TeamSelect."' AND ".GroupCodeReturn($GroupSelect,$YearPrev)."
								GROUP BY YEAR(T0.[DocDate]), MONTH(T0.[DocDate]), T0.[CardCode], T2.[CardName]
								UNION ALL
								SELECT
									T0.[CardCode], T2.[CardName], 
									CASE WHEN YEAR(T0.[DocDate]) = '".$YearPrev."' AND MONTH(T0.[DocDate]) <= MONTH(GETDATE()) THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Prev_Total',
									CASE WHEN YEAR(T0.[DocDate]) = '".$YearCurrent."' THEN -SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'Curr_Total'
								FROM ORIN T0
								LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
								LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode]
								LEFT JOIN NNM1 T3 ON T0.[Series] = T3.[Series]
								WHERE T0.[CANCELED] = 'N' AND (YEAR(T0.[DocDate]) BETWEEN '".$YearPrev."' AND '".$YearCurrent."') AND T1.[U_Dim1] = '".$TeamSelect."' AND ".GroupCodeReturn($GroupSelect,$YearPrev)."
								GROUP BY YEAR(T0.[DocDate]), MONTH(T0.[DocDate]), T0.[CardCode], T2.[CardName]
						) A1
						GROUP BY A1.[CardCode], A1.[CardName]
						ORDER BY CardCode";
			$sql_prevQRY = conSAP8($sql_prev);
			while($result_prev = odbc_fetch_array($sql_prevQRY)) {
				$PrevTotal_array[$result_prev['CardCode']] = $result_prev['Prev_Total'];
			}
		}

		$ModalTbody = "";
		$No = 1;
		$Sum_Prev_Total = 0;
		$Sum_Curr_Total = 0;
		$Sum_Curr_PFT = 0;
		$Sum_Q1 = 0; $Sum_Q2 = 0; $Sum_Q3 = 0; $Sum_Q4 = 0;
		$PrevTotal = 0;
		$rr = 0;
		$SUM_GROWTH = 0;
		$SGP = 0;
		while ($result = odbc_fetch_array($sqlSelectQRY)) {
			++$rr;
			// #tbody
			if($YearPrev <= 2022) {
				if(isset($PrevTotal_array[$result['CardCode']])) {
					$PrevTotal = $PrevTotal_array[$result['CardCode']];
				}else{
					$PrevTotal = 0;
				}
			}else{
				$PrevTotal = $result['Prev_Total'];
			}
			// หา % การเติบโต
				if($result['Curr_Total'] > 0 && $PrevTotal == 0) { 
					$GROWTH = 100; 
				} elseif($result['Curr_Total'] < 0 && $PrevTotal == 0) { 
					$GROWTH = -100; 
				} elseif($result['Curr_Total'] == 0 && $PrevTotal == 0) { 
					$GROWTH = 0; 
				} else { 
					$GROWTH = (($result['Curr_Total']-$PrevTotal)/abs($PrevTotal))*100; 
				}

			// หากำไรปีปัจจุบัน
				if($result['Curr_Total'] > 0) { 
					$GP = ($result['Curr_PFT']/$result['Curr_Total'])*100; 
				} else { 
					$GP = 0; 
				}
			// เงื่อนไขเอาข้อมูลไปโชว์ (% การเติบโต)
				if($GROWTH != 0) { 
					if($GROWTH < 0) { 
						$GROWTH_TD = "<td class='text-primary'>".number_format($GROWTH,2)."%</td>"; 
					} else { 
						$GROWTH_TD = "<td class='text-right text-green'>".number_format($GROWTH,2)."%</td>"; 
					} 
				} else { 
					$GROWTH_TD = "<td class='text-right'>0.00%</td>"; 
				}
			
			// เงื่อนไขเอาข้อมูลไปโชว์ (กำไรปีปัจจุบัน)
				if($GP != 0) {
					if($GP > 45) {
						$GP_TD = "<td class='text-green'>".number_format($GP,2)."%</td>";
					} else if($GP > 20) {
						$GP_TD = "<td class='text-warning'>".number_format($GP,2)."%</td>";
					} else {
						$GP_TD = "<td class='text-primary'>".number_format($GP,2)."%</td>";
					}
				} else {
					$GP_TD = "<td class='text-primary'>0.00%</td>";
				}
			
			// ผลรวมไตรมาส
				$Data_Q1 = $result['Curr_M01']+$result['Curr_M02']+$result['Curr_M03'];
				$Data_Q2 = $result['Curr_M04']+$result['Curr_M05']+$result['Curr_M06'];
				$Data_Q3 = $result['Curr_M07']+$result['Curr_M08']+$result['Curr_M09'];
				$Data_Q4 = $result['Curr_M10']+$result['Curr_M11']+$result['Curr_M12'];
				if($Data_Q1 != 0) { 
					if($Data_Q1 < 0) { 
						$Data_Q1_TD = "<td class='text-primary'>".number_format($Data_Q1,2)."</td>"; 
					} else { 
						$Data_Q1_TD = "<td class='text-right'>".number_format($Data_Q1,2)."</td>"; 
					} 
				} else { 
					$Data_Q1_TD = "<td class='text-right'>0.00</td>"; 
				}
				if($Data_Q2 != 0) { 
					if($Data_Q2 < 0) { 
						$Data_Q2_TD = "<td class='text-right text-primary'>".number_format($Data_Q2,2)."</td>"; 
					} else { 
						$Data_Q2_TD = "<td class='text-right'>".number_format($Data_Q2,2)."</td>"; 
					} 
				} else { 
					$Data_Q2_TD = "<td class='text-right'>0.00</td>"; 
				}
				if($Data_Q3 != 0) { 
					if($Data_Q3 < 0) { 
						$Data_Q3_TD = "<td class='text-right text-primary'>".number_format($Data_Q3,2)."</td>"; 
					} else { 
						$Data_Q3_TD = "<td class='text-right'>".number_format($Data_Q3,2)."</td>"; 
					} 
				} else { 
					$Data_Q3_TD = "<td class='text-right'>0.00</td>"; 
				}
				if($Data_Q4 != 0) { 
					if($Data_Q4 < 0) { 
						$Data_Q4_TD = "<td class='text-right text-primary'>".number_format($Data_Q4,2)."</td>"; 
					} else { 
						$Data_Q4_TD = "<td class='text-right'>".number_format($Data_Q4,2)."</td>"; 
					} 
				} else { 
					$Data_Q4_TD = "<td class='text-right'>0.00</td>"; 
				}
			
			$ModalTbody .= 	"<tr class='text-right'>".
								"<td class='text-center'>".$No++."</td>".
								"<td class='text-start'>".$result['CardCode']." ".conutf8($result['CardName'])."</td>".
								"<td class='text-start'>".conutf8($result['SlpName'])."</td>".
								"<td>".number_format($PrevTotal,2)."</td>".
								"<td class='fw-bolder'>".number_format($result['Curr_Total'],2)."</td>".
								$GROWTH_TD.
								$GP_TD.
								$Data_Q1_TD.
								$Data_Q2_TD.
								$Data_Q3_TD.
								$Data_Q4_TD.                              
							"</tr>";

			// #tfoot
			// ผลรวมยอดขายปีที่แล้ว
				$Sum_Prev_Total = $Sum_Prev_Total+$PrevTotal;

			// ผลรวมยอดขายปีปัจจุบัน
				$Sum_Curr_Total = $Sum_Curr_Total+$result['Curr_Total'];
			
			// ผลรวม % การเติมโต	
				if($Sum_Curr_Total > 0 && $Sum_Prev_Total == 0) { 
					$SUM_GROWTH = 100; 
				} elseif($Sum_Curr_Total < 0 && $Sum_Prev_Total == 0) { 
					$SUM_GROWTH = -100; 
				} elseif($Sum_Curr_Total == 0 && $Sum_Prev_Total == 0) { 
					$SUM_GROWTH = 0; 
				} else { 
					$SUM_GROWTH = (($Sum_Curr_Total-$Sum_Prev_Total)/abs($Sum_Prev_Total))*100; 
				}

			// ผลรวมกำไรปีปัจจุบัน
				$Sum_Curr_PFT = $Sum_Curr_PFT+$result['Curr_PFT'];
				if($Sum_Curr_Total > 0) { 
					$SGP = ($Sum_Curr_PFT/$Sum_Curr_Total)*100; 
				} else { 
					$SGP = 0; 
				}

			// ผลรวมไตรมาส
				$Sum_Q1 = $Sum_Q1+$Data_Q1;
				$Sum_Q2 = $Sum_Q2+$Data_Q2;
				$Sum_Q3 = $Sum_Q3+$Data_Q3;
				$Sum_Q4 = $Sum_Q4+$Data_Q4;
		}

		// ผลรวมยอดขายปีที่แล้ว Tfoot
			if($Sum_Prev_Total != 0) { 
				$Sum_PT_TD = "<td class='text-right fw-bolder text-green'>".number_format($Sum_Prev_Total,2)."</td>"; 
			} else { 
				$Sum_PT_TD = "<td class='text-right fw-bolder'>0.00</td>"; 
			}

		// ผลรวมยอดขายปีปัจจุบัน Tfoot
			if($Sum_Curr_Total != 0) { 
				$Sum_CT_TD = "<td class='text-right fw-bolder text-green'>".number_format($Sum_Curr_Total,2)."</td>"; 
			} else { 
				$Sum_CT_TD = "<td class='text-right fw-bolder'>0.00</td>"; 
			}

		// ผลรวม % การเติมโต Tfoot
			if($SUM_GROWTH != 0) { 
				if($SUM_GROWTH < 0) { 
					$SUM_GROWTH_TD = "<td class='text-right fw-bolder text-primary'>".number_format($SUM_GROWTH,2)."%</td>"; 
				} else { 
					$SUM_GROWTH_TD = "<td class='text-right fw-bolder text-green'>".number_format($SUM_GROWTH,2)."%</td>"; 
				} 
			} else { 
				$SUM_GROWTH_TD = "<td class='text-right fw-bolder'>0.00%</td>"; 
			}

		// ผลรวมกำไรปีปัจจุบัน Tfoot
			if($SGP != 0) {
				if($SGP > 45) {
					$SGP_TD = "<td class='text-right fw-bolder text-green'>".number_format($SGP,2)."%</td>";
				} else if($SGP > 20) {
					$SGP_TD = "<td class='text-right fw-bolder text-warning'>".number_format($SGP,2)."%</td>";
				} else {
					$SGP_TD = "<td class='text-right fw-bolder text-primary'>".number_format($SGP,2)."%</td>";
				}
			} else {
				$SGP_TD = "<td class='text-right fw-bolder text-primary'>0.00%</td>";
			}

		// ผลรวมไตรมาส Tfoot
			if($Sum_Q1 != 0) { 
				$Sum_Q1_TD = "<td class='text-right fw-bolder text-green'>".number_format($Sum_Q1,2)."</td>"; 
			} else { 
				$Sum_Q1_TD = "<td class='text-right fw-bolder'>0.00</td>"; 
			}
			if($Sum_Q2 != 0) { 
				$Sum_Q2_TD = "<td class='text-right fw-bolder text-green'>".number_format($Sum_Q2,2)."</td>"; 
			} else { 
				$Sum_Q2_TD = "<td class='text-right fw-bolder'>0.00</td>"; 
			}
			if($Sum_Q3 != 0) { 
				$Sum_Q3_TD = "<td class='text-right fw-bolder text-green'>".number_format($Sum_Q3,2)."</td>"; 
			} else { 
				$Sum_Q3_TD = "<td class='text-right fw-bolder'>0.00</td>"; 
			}
			if($Sum_Q4 != 0) { 
				$Sum_Q4_TD = "<td class='text-right fw-bolder text-green'>".number_format($Sum_Q4,2)."</td>"; 
			} else { 
				$Sum_Q4_TD = "<td class='text-right fw-bolder'>0.00</td>"; 
			}

		$ModalTfoot = 	"<tr style='background-color: rgba(0, 0, 0, 0.04);'>".
								"<td></td>".  
								"<td></td>".  
								"<td class='text-start fw-bolder'>รวมทั้งหมด</td>".
								$Sum_PT_TD.
								$Sum_CT_TD.
								$SUM_GROWTH_TD.
								$SGP_TD.
								$Sum_Q1_TD.
								$Sum_Q2_TD.
								$Sum_Q3_TD.
								$Sum_Q4_TD.
							"</tr>";

		$arrCol['TeamSelect'] = $TeamSelect;
		$arrCol['GroupSelect'] = $GroupSelect;
		$arrCol['YearCurrent'] = $YearCurrent;
		$arrCol['YearPrev'] = $YearPrev;

		$arrCol['ModalTbody'] = $ModalTbody;
		$arrCol['ModalTfoot'] = $ModalTfoot;
	}
// END รายงานการขาย

// รายงานการคืน
		// ---------------------------------------------------------------------[ คืนลดหนี้ ]-------------------------------------------------------------------------
	if ($_GET['a'] == 'SelectYearRT') {
		// ------------------------------------------------------------------------ ALL --------------------------------------------------------------------------
		if ($_POST['Team'] == 'all') {
			$TeamSelect = $_POST['Team'];
			$YearSelect = $_POST['Year'];
			// SQL Select
			$sqlMONTH = "";
			if($YearSelect == date("Y")) { 
				$sqlMONTH = "AND MONTH(T0.[DocDate]) <= MONTH(GETDATE())"; 
			}
			$sqlSelect ="SELECT A1.[TEAM],
							SUM(A1.[M_01]) AS 'M_01', SUM(A1.[M_02]) AS 'M_02', SUM(A1.[M_03]) AS 'M_03', SUM(A1.[M_04]) AS 'M_04',
							SUM(A1.[M_05]) AS 'M_05', SUM(A1.[M_06]) AS 'M_06', SUM(A1.[M_07]) AS 'M_07', SUM(A1.[M_08]) AS 'M_08',
							SUM(A1.[M_09]) AS 'M_09', SUM(A1.[M_10]) AS 'M_10', SUM(A1.[M_11]) AS 'M_11', SUM(A1.[M_12]) AS 'M_12',
							(SELECT
						SUM(B1.SaTotal) AS 'SaTotal'
							FROM (
							SELECT
							T1.[U_Dim1] AS 'Team', SUM(T0.[DocTotal]-T0.[VatSum]) AS 'SaTotal' 
							FROM OINV T0
							LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
							LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode] 
							WHERE T0.CANCELED = 'N' AND YEAR(T0.[DocDate]) = '$YearSelect' $sqlMONTH
							GROUP BY T1.[U_Dim1]
							UNION ALL
							SELECT
							T1.[U_Dim1] AS 'Team', -SUM(T0.[DocTotal]-T0.[VatSum]) AS 'SaTotal' 
							FROM ORIN T0
							LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
							LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode] 
							WHERE T0.CANCELED = 'N' AND YEAR(T0.[DocDate]) = '$YearSelect' $sqlMONTH AND T0.[U_CNReason2] IN ('5.1')
							GROUP BY T1.[U_Dim1]
							UNION ALL
							SELECT
							T1.[U_Dim1] AS 'Team', SUM(T0.[DocTotal]-T0.[VatSum]) AS 'SaTotal' 
							FROM ORIN T0
							LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
							LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode] 
							WHERE T0.CANCELED = 'N' AND YEAR(T0.[DocDate]) = '$YearSelect' $sqlMONTH AND T0.[U_CNReason2] IN ('3.2','3.3','4.1')
							GROUP BY T1.[U_Dim1]
							) B1
							WHERE B1.[Team] = A1.[TEAM]) AS 'SaTotal'
						FROM (
							SELECT 
							T1.[U_Dim1] AS 'TEAM',
							CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
							FROM ORIN T0
							LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
							LEFT JOIN NNM1 T2 ON T0.[Series] = T2.[Series] 
							WHERE YEAR(T0.[DocDate]) = '$YearSelect' AND (T2.[BeginStr] LIKE 'S1-%' OR T2.[BeginStr] LIKE 'SR-%') AND (T0.[U_CNReason2] NOT IN ('5.1','3.2','3.3','4.1') OR T0.[U_CNReason2] IS NULL) AND T0.CANCELED = 'N'
							GROUP BY T1.[U_Dim1], MONTH(T0.[DocDate])
							) A1
						GROUP BY
							A1.[Team]
						ORDER BY CASE
							WHEN A1.[Team] = 'MT1' THEN 1
							WHEN A1.[Team] = 'MT2' THEN 2
							WHEN A1.[Team] = 'TT1' THEN 3
							WHEN A1.[Team] = 'TT2' THEN 4
							WHEN A1.[Team] = 'OUL' THEN 5
							WHEN A1.[Team] = 'ONL' THEN 6
							WHEN A1.[Team] = 'EI1' THEN 7
							WHEN A1.[Team] = 'EXP' THEN 8
							WHEN A1.[Team] = 'MKT' THEN 9
							WHEN A1.[Team] = 'DMN' THEN 10
							ELSE 11
						END";
			if($YearSelect <= 2022) {
				$sqlSelectQRY = conSAP8($sqlSelect);
			}else{
				$sqlSelectQRY = SAPSelect($sqlSelect);
			}
			$TbodyRT = "";
			$Sum_M1 = 0; $Sum_M2 = 0; $Sum_M3 = 0; $Sum_M4 = 0; $Sum_M5 = 0; $Sum_M6 = 0; $Sum_M7 = 0; $Sum_M8 = 0; $Sum_M9 = 0; $Sum_M10 = 0; $Sum_M11 = 0; $Sum_M12 = 0;
			$M_1 = ""; $M_2 = ""; $M_3 = ""; $M_4 = ""; $M_5 = ""; $M_6 = ""; $M_7 = ""; $M_8 = ""; $M_9 = ""; $M_10 = ""; $M_11 = ""; $M_12 = "";
			$TEAM = "";
			while ($result = odbc_fetch_array($sqlSelectQRY)) {
				$AllM_Team = ($result['M_01']+$result['M_02']+$result['M_03']+$result['M_04']+$result['M_05']+$result['M_06']+$result['M_07']+$result['M_08']+$result['M_09']+$result['M_10']+$result['M_11']+$result['M_12']);
				// Class TD Detail 
					if($result['M_01'] < 0) { $TDClass1 = "<td class='text-right text-primary'>"; } else { $TDClass1 = "<td class='text-right'>"; }
					if($result['M_02'] < 0) { $TDClass2 = "<td class='text-right text-primary'>"; } else { $TDClass2 = "<td class='text-right'>"; }
					if($result['M_03'] < 0) { $TDClass3 = "<td class='text-right text-primary'>"; } else { $TDClass3 = "<td class='text-right'>"; }
					if($result['M_04'] < 0) { $TDClass4 = "<td class='text-right text-primary'>"; } else { $TDClass4 = "<td class='text-right'>"; }
					if($result['M_05'] < 0) { $TDClass5 = "<td class='text-right text-primary'>"; } else { $TDClass5 = "<td class='text-right'>"; }
					if($result['M_06'] < 0) { $TDClass6 = "<td class='text-right text-primary'>"; } else { $TDClass6 = "<td class='text-right'>"; }
					if($result['M_07'] < 0) { $TDClass7 = "<td class='text-right text-primary'>"; } else { $TDClass7 = "<td class='text-right'>"; }
					if($result['M_08'] < 0) { $TDClass8 = "<td class='text-right text-primary'>"; } else { $TDClass8 = "<td class='text-right'>"; }
					if($result['M_09'] < 0) { $TDClass9 = "<td class='text-right text-primary'>"; } else { $TDClass9 = "<td class='text-right'>"; }
					if($result['M_10'] < 0) { $TDClass10 = "<td class='text-right text-primary'>"; } else { $TDClass10 = "<td class='text-right'>"; }
					if($result['M_11'] < 0) { $TDClass11 = "<td class='text-right text-primary'>"; } else { $TDClass11 = "<td class='text-right'>"; }
					if($result['M_12'] < 0) { $TDClass12 = "<td class='text-right text-primary'>"; } else { $TDClass12 = "<td class='text-right'>"; }
					if($AllM_Team < 0) { $TDAllM = "<td class='text-right text-primary fw-bolder'>"; } else { $TDAllM = "<td class='text-right fw-bolder'>"; }
					if((($AllM_Team/$result['SaTotal'])*100) >= 2) { $PERALL = "<td class='text-center text-primary fw-bolder'>"; } else { $PERALL = "<td class='text-center text-success fw-bolder'>"; }
				
				$TbodyRT .= "<tr class='text-right'>".
								"<td class='text-start text-primary fw-bolder btn-team' data-team='".$result['TEAM']."'><a href='javascript:void(0);'>".SATeamName($result['TEAM'])."</a></td>".
								$TDClass1.number_format($result['M_01'],2)."</td>".
								$TDClass2.number_format($result['M_02'],2)."</td>".
								$TDClass3.number_format($result['M_03'],2)."</td>".
								$TDClass4.number_format($result['M_04'],2)."</td>".
								$TDClass5.number_format($result['M_05'],2)."</td>".
								$TDClass6.number_format($result['M_06'],2)."</td>".
								$TDClass7.number_format($result['M_07'],2)."</td>".
								$TDClass8.number_format($result['M_08'],2)."</td>".
								$TDClass9.number_format($result['M_09'],2)."</td>".
								$TDClass10.number_format($result['M_10'],2)."</td>".
								$TDClass11.number_format($result['M_11'],2)."</td>".
								$TDClass12.number_format($result['M_12'],2)."</td>".
								$TDAllM.number_format($AllM_Team,2)."</td>".
								"<td>".number_format($result['SaTotal'],2)."</td>".
								$PERALL.number_format((($AllM_Team/$result['SaTotal'])*100),2)."%</td>".
							"</tr>";
				// Sum Months
					$Sum_M1 = $Sum_M1+$result['M_01'];
					$Sum_M2 = $Sum_M2+$result['M_02'];
					$Sum_M3 = $Sum_M3+$result['M_03'];
					$Sum_M4 = $Sum_M4+$result['M_04'];
					$Sum_M5 = $Sum_M5+$result['M_05'];
					$Sum_M6 = $Sum_M6+$result['M_06'];
					$Sum_M7 = $Sum_M7+$result['M_07'];
					$Sum_M8 = $Sum_M8+$result['M_08'];
					$Sum_M9 = $Sum_M9+$result['M_09'];
					$Sum_M10 = $Sum_M10+$result['M_10'];
					$Sum_M11 = $Sum_M11+$result['M_11'];
					$Sum_M12 = $Sum_M12+$result['M_12'];
					
				// ข้อมูลของ Charts
					$TEAM .= SATeamName($result['TEAM'])."|";
					$M_1 .= $result['M_01']." ";
					$M_2 .= $result['M_02']." ";
					$M_3 .= $result['M_03']." ";
					$M_4 .= $result['M_04']." ";
					$M_5 .= $result['M_05']." ";
					$M_6 .= $result['M_06']." ";
					$M_7 .= $result['M_07']." ";
					$M_8 .= $result['M_08']." ";
					$M_9 .= $result['M_09']." ";
					$M_10 .= $result['M_10']." ";
					$M_11 .= $result['M_11']." ";
					$M_12 .= $result['M_12']." ";
			}
			// Class TD Sum
				if($Sum_M1 < 0 || $Sum_M1 > 0){ $SMC1 = "text-right text-primary"; }else{ $SMC1 = "text-right"; }
				if($Sum_M2 < 0 || $Sum_M2 > 0){ $SMC2 = "text-right text-primary"; }else{ $SMC2 = "text-right"; }
				if($Sum_M3 < 0 || $Sum_M3 > 0){ $SMC3 = "text-right text-primary"; }else{ $SMC3 = "text-right"; }
				if($Sum_M4 < 0 || $Sum_M4 > 0){ $SMC4 = "text-right text-primary"; }else{ $SMC4 = "text-right"; }
				if($Sum_M5 < 0 || $Sum_M5 > 0){ $SMC5 = "text-right text-primary"; }else{ $SMC5 = "text-right"; }
				if($Sum_M6 < 0 || $Sum_M6 > 0){ $SMC6 = "text-right text-primary"; }else{ $SMC6 = "text-right"; }
				if($Sum_M7 < 0 || $Sum_M7 > 0){ $SMC7 = "text-right text-primary"; }else{ $SMC7 = "text-right"; }
				if($Sum_M8 < 0 || $Sum_M8 > 0){ $SMC8 = "text-right text-primary"; }else{ $SMC8 = "text-right"; }
				if($Sum_M9 < 0 || $Sum_M9 > 0){ $SMC9 = "text-right text-primary"; }else{ $SMC9 = "text-right"; }
				if($Sum_M10 < 0 || $Sum_M10 > 0){ $SMC10 = "text-right text-primary"; }else{ $SMC10 = "text-right"; }
				if($Sum_M11 < 0 || $Sum_M11 > 0){ $SMC11 = "text-right text-primary"; }else{ $SMC11 = "text-right"; }
				if($Sum_M12 < 0 || $Sum_M12 > 0){ $SMC12 = "text-right text-primary"; }else{ $SMC12 = "text-right"; }
			$Sum_AllM = ($Sum_M1+$Sum_M2+$Sum_M3+$Sum_M4+$Sum_M5+$Sum_M6+$Sum_M7+$Sum_M8+$Sum_M9+$Sum_M10+$Sum_M11+$Sum_M12);
			if($Sum_AllM < 0 || $Sum_AllM > 0){ $SAC = "text-right text-primary"; }else{ $SAC = "text-right"; }
			$TbodyRT .= "<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".
							"<td class='text-start fw-bolder'>รวมทุกทีม</td>".
							"<td class='fw-bolder ".$SMC1."'>".number_format($Sum_M1,2)."</td>".
							"<td class='fw-bolder ".$SMC2."'>".number_format($Sum_M2,2)."</td>".
							"<td class='fw-bolder ".$SMC3."'>".number_format($Sum_M3,2)."</td>".
							"<td class='fw-bolder ".$SMC4."'>".number_format($Sum_M4,2)."</td>".
							"<td class='fw-bolder ".$SMC5."'>".number_format($Sum_M5,2)."</td>".
							"<td class='fw-bolder ".$SMC6."'>".number_format($Sum_M6,2)."</td>".
							"<td class='fw-bolder ".$SMC7."'>".number_format($Sum_M7,2)."</td>".
							"<td class='fw-bolder ".$SMC8."'>".number_format($Sum_M8,2)."</td>".
							"<td class='fw-bolder ".$SMC9."'>".number_format($Sum_M9,2)."</td>".
							"<td class='fw-bolder ".$SMC10."'>".number_format($Sum_M10,2)."</td>".
							"<td class='fw-bolder ".$SMC11."'>".number_format($Sum_M11,2)."</td>".
							"<td class='fw-bolder ".$SMC12."'>".number_format($Sum_M12,2)."</td>".
							"<td class='fw-bolder ".$SAC."'>".number_format($Sum_AllM,2)."</td>".
							"<td>&nbsp;</td>".
							"<td>&nbsp;</td>".
						"</tr>";
			// SQL Sales
			$sqlSal ="SELECT
						SUM(A1.[M_01]) AS 'M_01', SUM(A1.[M_02]) AS 'M_02', SUM(A1.[M_03]) AS 'M_03', SUM(A1.[M_04]) AS 'M_04',
						SUM(A1.[M_05]) AS 'M_05', SUM(A1.[M_06]) AS 'M_06', SUM(A1.[M_07]) AS 'M_07', SUM(A1.[M_08]) AS 'M_08',
						SUM(A1.[M_09]) AS 'M_09', SUM(A1.[M_10]) AS 'M_10', SUM(A1.[M_11]) AS 'M_11', SUM(A1.[M_12]) AS 'M_12'
					FROM (
						SELECT
							T1.[U_Dim1] AS 'Team', 
							CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
						FROM OINV T0
						LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
						WHERE T0.CANCELED = 'N' AND YEAR(T0.[DocDate]) = '".$YearSelect."'";
					if($YearSelect == date("Y")) { $sqlSal .= " AND MONTH(T0.[DocDate]) <= MONTH(GETDATE()) "; }
					$sqlSal .= " GROUP BY T1.[U_Dim1], MONTH(T0.[DocDate])
						UNION ALL
						SELECT
							T1.[U_Dim1] AS 'Team', 
							CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
						FROM ORIN T0
						LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
						LEFT JOIN NNM1 T3 ON T0.[Series] = T3.[Series]
						WHERE T0.CANCELED = 'N' AND YEAR(T0.[DocDate]) = '".$YearSelect."' AND (T3.[BeginStr] IN ('S1-','SR-')) AND T0.[U_CNReason2] IN ('5.1')";
					if($YearSelect == date("Y")) { $sqlSal .= " AND MONTH(T0.[DocDate]) <= MONTH(GETDATE()) "; }
					$sqlSal .= " GROUP BY T1.[U_Dim1], MONTH(T0.[DocDate])
					) A1 ";
			if($YearSelect <= 2022) {
				$sqlSalQRY = conSAP8($sqlSal);
			}else{
				$sqlSalQRY = SAPSelect($sqlSal);
			}
			$resultSal = odbc_fetch_array($sqlSalQRY);

			// ยอดขายปี ...
			$PYTotal = 0;
			$TbodyRT .= "<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".
							"<td class='text-start fw-bolder'>ยอดขายปี ".$YearSelect."</td>";
							for($m=1;$m<=12;$m++) {
								if($m<10) {
									if($resultSal['M_0'.$m] < 0) { $TbodyRT .= "<td class='text-right text-primary'>"; } else { $TbodyRT .= "<td class='text-right'>"; }
									$TbodyRT .= number_format($resultSal['M_0'.$m],2);
									$TbodyRT .= "</td>";
									$PYTotal = $PYTotal+$resultSal['M_0'.$m];
								} else {
									if($resultSal['M_'.$m] < 0) { $TbodyRT .= "<td class='text-right text-primary'>"; } else { $TbodyRT .= "<td class='text-right'>"; }
									$TbodyRT .= number_format($resultSal['M_'.$m],2);
									$TbodyRT .= "</td>";
									$PYTotal = $PYTotal+$resultSal['M_'.$m];
								}
							}
							if($PYTotal < 0) { $TbodyRT .= "<td class='text-right fw-bolder text-primary'>".number_format($PYTotal,2)."</td>"; } else { $TbodyRT .= "<td class='text-right fw-bolder'>".number_format($PYTotal,2)."</td>"; }
				$TbodyRT .= "<td>&nbsp;</td>".
							"<td>&nbsp;</td>".
						"</tr>";

			// % การคืน
				for($m=1;$m<=12;$m++) {
					if($m<10) {
						if(${"Sum_M".$m} > 0 && $resultSal['M_0'.$m] == 0) { 
							${"GROWTH_M".$m} = 100; 
						} elseif(${"Sum_M".$m} < 0 && $resultSal['M_0'.$m] == 0) { 
							${"GROWTH_M".$m} = -100; 
						} elseif(${"Sum_M".$m} == 0 && $resultSal['M_0'.$m] == 0) { 
							${"GROWTH_M".$m} = 0; 
						} else { 
							${"GROWTH_M".$m} = ((${"Sum_M".$m})/$resultSal['M_0'.$m])*100; 
						}
					} else {
						if(${"Sum_M".$m} > 0 && $resultSal['M_'.$m] == 0) { 
							${"GROWTH_M".$m} = 100; 
						} elseif(${"Sum_M".$m} < 0 && $resultSal['M_'.$m] == 0) { 
							${"GROWTH_M".$m} = -100; 
						} elseif(${"Sum_M".$m} == 0 && $resultSal['M_'.$m] == 0) { 
							${"GROWTH_M".$m} = 0; 
						} else { 
							${"GROWTH_M".$m} = ((${"Sum_M".$m})/$resultSal['M_'.$m])*100; 
						}
					}
				}
				if($Sum_AllM > 0 && $PYTotal == 0) { 
					$GROWTH_Total = 100; 
				} elseif($Sum_AllM < 0 && $PYTotal == 0) { 
					$GROWTH_Total = -100; 
				} elseif($Sum_AllM == 0 && $PYTotal == 0) { 
					$GROWTH_Total = 0; 
				} else { 
					$GROWTH_Total = (($Sum_AllM)/abs($PYTotal))*100; 
				}
			$TbodyRT .= "<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".
							"<td class='text-start fw-bolder'>% การคืน</td>";
							for($m=1;$m<=12;$m++) {
								if(${"GROWTH_M".$m} > 2) { $TbodyRT .= "<td class='text-right fw-bolder text-primary'>"; } else { $TbodyRT .= "<td class='text-right fw-bolder text-green'>"; }
								$TbodyRT .= number_format(${"GROWTH_M".$m},2);
								$TbodyRT .= "%</td>";
							}
							if($GROWTH_Total > 2) { 
								$TbodyRT .= "<td class='text-right fw-bolder text-primary'>".number_format($GROWTH_Total,2)."%</td>"; 
							} else { 
								$TbodyRT .= "<td class='text-right fw-bolder text-green'>".number_format($GROWTH_Total,2)."%</td>"; 
							}
				$TbodyRT .= "<td>&nbsp;</td>".
							"<td>&nbsp;</td>".
						"</tr>";

			$arrCol['TeamSelect'] = $TeamSelect;
			$arrCol['YearSelect'] = $YearSelect;

			$arrCol['TbodyRT'] = $TbodyRT;

			$arrCol['TEAM'] = $TEAM;
			$arrCol['M_1'] = $M_1;
			$arrCol['M_2'] = $M_2;
			$arrCol['M_3'] = $M_3;
			$arrCol['M_4'] = $M_4;
			$arrCol['M_5'] = $M_5;
			$arrCol['M_6'] = $M_6;
			$arrCol['M_7'] = $M_7;
			$arrCol['M_8'] = $M_8;
			$arrCol['M_9'] = $M_9;
			$arrCol['M_10'] = $M_10;
			$arrCol['M_11'] = $M_11;
			$arrCol['M_12'] = $M_12;
		}

		// ------------------------------------------------------------------------ Team -------------------------------------------------------------------------
		if($_POST['Team'] != 'all') {
			$TeamSelect = $_POST['Team'];
			$YearSelect = $_POST['Year'];
			$sql = "SELECT A1.[TEAM], A1.[ReturnGroup],
						SUM(A1.[M_01]) AS 'M_01', SUM(A1.[M_02]) AS 'M_02', SUM(A1.[M_03]) AS 'M_03', SUM(A1.[M_04]) AS 'M_04',
						SUM(A1.[M_05]) AS 'M_05', SUM(A1.[M_06]) AS 'M_06', SUM(A1.[M_07]) AS 'M_07', SUM(A1.[M_08]) AS 'M_08',
						SUM(A1.[M_09]) AS 'M_09', SUM(A1.[M_10]) AS 'M_10', SUM(A1.[M_11]) AS 'M_11', SUM(A1.[M_12]) AS 'M_12'
					FROM (
						SELECT 
							T1.[U_Dim1] AS 'TEAM',
							CASE
								WHEN T0.[U_CNReason2] IN ('2.1') THEN 'G1'
								WHEN T0.[U_CNReason2] IN ('1.1','1.2','1.3','1.4','4.5','4.2') THEN 'G2'
								WHEN T0.[U_CNReason2] IN ('3.2','3.3','4.1') THEN 'G3'
								WHEN T0.[U_CNReason2] IN ('3.1') THEN 'G4'
								WHEN T0.[U_CNReason2] IN ('2.5') THEN 'G5'
								WHEN T0.[U_CNReason2] IN ('2.3','4.3') THEN 'G6'
								WHEN T0.[U_CNReason2] IN ('2.2') THEN 'G7'
								WHEN T0.[U_CNReason2] IN ('5.1') THEN 'G9'
								ELSE 'G8' END AS 'ReturnGroup',
							CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
						FROM ORIN T0 
						LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
						LEFT JOIN NNM1 T2 ON T0.[Series] = T2.[Series] 
						WHERE YEAR(T0.[DocDate]) = '".$YearSelect."' AND (T2.[BeginStr] LIKE 'S1-%' OR T2.[BeginStr] LIKE 'SR-%') AND T0.CANCELED = 'N' 
						GROUP BY T1.[U_Dim1], MONTH(T0.[DocDate]), T0.[U_CNReason2]
						) A1
					WHERE A1.[Team] = '".$TeamSelect."'
					GROUP BY
						A1.[Team], A1.[ReturnGroup]
					ORDER BY A1.[ReturnGroup]";
			if($YearSelect <= 2022) {
				$sqlQRY = conSAP8($sql);
			}else{
				$sqlQRY = SAPSelect($sql);
			}
			$TbodyRT = "";
			$Set_G1 = array(); $Set_G2 = array(); $Set_G3 = array(); $Set_G4 = array(); $Set_G5 = array(); $Set_G6 = array(); $Set_G7 = array(); $Set_G8 = array(); $Set_G9 = array();
			$Sum_M1 = 0; $Sum_M2 = 0; $Sum_M3 = 0; $Sum_M4 = 0; $Sum_M5 = 0; $Sum_M6 = 0; $Sum_M7 = 0; $Sum_M8 = 0; $Sum_M9 = 0; $Sum_M10 = 0; $Sum_M11 = 0; $Sum_M12 = 0;
			$G3 = array(0); $G9 = array(0);
			$M_1 = ""; $M_2 = ""; $M_3 = ""; $M_4 = ""; $M_5 = ""; $M_6 = ""; $M_7 = ""; $M_8 = ""; $M_9 = ""; $M_10 = ""; $M_11 = ""; $M_12 = "";
			$TEAM = "";
			while ($result = odbc_fetch_array($sqlQRY)) {
				$AllM_Team = ($result['M_01']+$result['M_02']+$result['M_03']+$result['M_04']+$result['M_05']+$result['M_06']+$result['M_07']+$result['M_08']+$result['M_09']+$result['M_10']+$result['M_11']+$result['M_12']);
				// All G
					if ( $result['ReturnGroup'] == 'G1' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G1, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G1, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G1, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G1, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

					if ( $result['ReturnGroup'] == 'G2' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G2, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G2, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G2, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G2, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

					if ( $result['ReturnGroup'] == 'G3' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G3, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G3, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G3, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G3, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

					if ( $result['ReturnGroup'] == 'G4' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G4, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G4, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G4, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G4, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

					if ( $result['ReturnGroup'] == 'G5' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G5, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G5, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G5, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G5, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

					if ( $result['ReturnGroup'] == 'G6' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G6, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G6, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G6, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G6, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

					if ( $result['ReturnGroup'] == 'G7' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G7, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G7, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G7, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G7, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

					if ( $result['ReturnGroup'] == 'G8' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G8, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G8, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G8, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G8, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

					if ( $result['ReturnGroup'] == 'G9' ) {
						for ($i = 0; $i <=13; $i++) {
							if ( $i == 0) { array_push($Set_G9, ReturnName($result['ReturnGroup'])); }
							if ( $i != 0 && $i < 10) { array_push($Set_G9, number_format($result['M_0'.$i],2)); }
							if ( $i >= 10 && $i < 13 ) { array_push($Set_G9, number_format($result['M_'.$i],2)); }
							if ( $i == 13) { array_push($Set_G9, number_format($AllM_Team,2)); }
						}
						// ข้อมูลของ Charts
							$TEAM .= ReturnName($result['ReturnGroup'])."|";
							$M_1 .= $result['M_01']." ";
							$M_2 .= $result['M_02']." ";
							$M_3 .= $result['M_03']." ";
							$M_4 .= $result['M_04']." ";
							$M_5 .= $result['M_05']." ";
							$M_6 .= $result['M_06']." ";
							$M_7 .= $result['M_07']." ";
							$M_8 .= $result['M_08']." ";
							$M_9 .= $result['M_09']." ";
							$M_10 .= $result['M_10']." ";
							$M_11 .= $result['M_11']." ";
							$M_12 .= $result['M_12']." ";
					}

				// Sum Months (ยกเว้น 3,9) || เก็บค่า G3, G9
					if ( $result['ReturnGroup'] != 'G3' && $result['ReturnGroup'] != 'G9') {
						$Sum_M1 = $Sum_M1+$result['M_01'];
						$Sum_M2 = $Sum_M2+$result['M_02'];
						$Sum_M3 = $Sum_M3+$result['M_03'];
						$Sum_M4 = $Sum_M4+$result['M_04'];
						$Sum_M5 = $Sum_M5+$result['M_05'];
						$Sum_M6 = $Sum_M6+$result['M_06'];
						$Sum_M7 = $Sum_M7+$result['M_07'];
						$Sum_M8 = $Sum_M8+$result['M_08'];
						$Sum_M9 = $Sum_M9+$result['M_09'];
						$Sum_M10 = $Sum_M10+$result['M_10'];
						$Sum_M11 = $Sum_M11+$result['M_11'];
						$Sum_M12 = $Sum_M12+$result['M_12'];
					}else{
						if($result['ReturnGroup'] == 'G3'){ 
							for ($i = 1; $i <= 12; $i++) { 
								if($i<10) {
									array_push($G3,$result['M_0'.$i]); 
								}else{ 
									array_push($G3,$result['M_'.$i]); 
								}
							}
						}
						if($result['ReturnGroup'] == 'G9'){ 
							for ($i = 1; $i <= 12; $i++) { 
								if($i<10) {
									array_push($G9,$result['M_0'.$i]); 
								}else{ 
									array_push($G9,$result['M_'.$i]); 
								}
							}
						}
					}

				
			}

			// Data Tbody
			$v = 0;
			for ($G = 1; $G <= 9; $G++) {
				if (isset(${"Set_G".$G}[$v])) {
					$TbodyRT .= "<tr class='text-right'>".
							"<td class='text-start fw-bolder'>".${"Set_G".$G}[0]."</td>".
							"<td>".${"Set_G".$G}[1]."</td>".
							"<td>".${"Set_G".$G}[2]."</td>".
							"<td>".${"Set_G".$G}[3]."</td>".
							"<td>".${"Set_G".$G}[4]."</td>".
							"<td>".${"Set_G".$G}[5]."</td>".
							"<td>".${"Set_G".$G}[6]."</td>".
							"<td>".${"Set_G".$G}[7]."</td>".
							"<td>".${"Set_G".$G}[8]."</td>".
							"<td>".${"Set_G".$G}[9]."</td>".
							"<td>".${"Set_G".$G}[10]."</td>".
							"<td>".${"Set_G".$G}[11]."</td>".
							"<td>".${"Set_G".$G}[12]."</td>".
							"<td class='fw-bolder'>".${"Set_G".$G}[13]."</td>".
						"</tr>";
					$v++;
				}else{
					$Set_Name = array("1. เซลส์", "2. ลูกค้า", "3. สินค้า", "4. สินค้าไม่เคลื่อนไหว", "5. MT/Consign", "6. ธุรการคลังสินค้า", "7. ธุรการขาย", "8. อื่น ๆ", "9. แก้ไขบิลภายใน");
					$TbodyRT .= "<tr class='text-right'>".
							"<td class='text-start fw-bolder'>".$Set_Name[$v]."</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td>0.00</td>".
							"<td class='fw-bolder'>0.00</td>".
						"</tr>";
						if ($Set_Name[$v] == "3. สินค้า") {
							for ($i = 1; $i <= 12; $i++) { 
								if($i<10) {
									array_push($G3,0); 
								}else{ 
									array_push($G3,0); 
								}
							}
						}
						if ($Set_Name[$v] == "9. แก้ไขบิลภายใน") {
							for ($i = 1; $i <= 12; $i++) { 
								if($i<10) {
									array_push($G9,0); 
								}else{ 
									array_push($G9,0); 
								}
							}
						}
					$v++;
				}
			}

			$Sum_AllM = ($Sum_M1+$Sum_M2+$Sum_M3+$Sum_M4+$Sum_M5+$Sum_M6+$Sum_M7+$Sum_M8+$Sum_M9+$Sum_M10+$Sum_M11+$Sum_M12);
			// รวมทั้งหมด (ยกเว้น 3,9)
			$TbodyRT .= "<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".
								"<td class='text-start fw-bolder'>รวมทั้งหมด (ยกเว้น 3, 9)</td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='1'>".number_format($Sum_M1,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='2''>".number_format($Sum_M2,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='3''>".number_format($Sum_M3,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='4''>".number_format($Sum_M4,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='5''>".number_format($Sum_M5,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='6''>".number_format($Sum_M6,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='7''>".number_format($Sum_M7,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='8''>".number_format($Sum_M8,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='9''>".number_format($Sum_M9,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='10''>".number_format($Sum_M10,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='11''>".number_format($Sum_M11,2)."</a></td>".
								"<td class='text-primary'><a href='javascript:void(0);' class='btn-group' data-team='".$TeamSelect."' data-year='".$YearSelect."' data-month='12''>".number_format($Sum_M12,2)."</a></td>".
								"<td class='text-primary fw-bolder'>".number_format($Sum_AllM,2)."</td>".
							"</tr>";

			// SQL หายอดขายปี ...
			$sqlSal ="SELECT
						SUM(A1.[M_01]) AS 'M_01', SUM(A1.[M_02]) AS 'M_02', SUM(A1.[M_03]) AS 'M_03', SUM(A1.[M_04]) AS 'M_04',
						SUM(A1.[M_05]) AS 'M_05', SUM(A1.[M_06]) AS 'M_06', SUM(A1.[M_07]) AS 'M_07', SUM(A1.[M_08]) AS 'M_08',
						SUM(A1.[M_09]) AS 'M_09', SUM(A1.[M_10]) AS 'M_10', SUM(A1.[M_11]) AS 'M_11', SUM(A1.[M_12]) AS 'M_12'
					FROM (
						SELECT
							T1.[U_Dim1] AS 'Team', 
							CASE WHEN MONTH(T0.[DocDate]) = 1 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(T0.[DocDate]) = 2 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(T0.[DocDate]) = 3 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(T0.[DocDate]) = 4 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(T0.[DocDate]) = 5 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(T0.[DocDate]) = 6 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(T0.[DocDate]) = 7 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(T0.[DocDate]) = 8 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(T0.[DocDate]) = 9 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(T0.[DocDate]) = 10 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(T0.[DocDate]) = 11 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(T0.[DocDate]) = 12 THEN SUM(T0.[DocTotal]-T0.[VatSum]) ELSE 0 END AS 'M_12' 
						FROM OINV T0
						LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
						LEFT JOIN OCRD T2 ON T0.[CardCode] = T2.[CardCode] 
						WHERE T0.CANCELED = 'N' AND YEAR(T0.[DocDate]) = '".$YearSelect."'";
						if($YearSelect == date("Y")) { $sqlSal .= " AND MONTH(T0.[DocDate]) <= MONTH(GETDATE()) "; }
					$sqlSal .= "AND T1.[U_Dim1] = '".$TeamSelect."'
						GROUP BY T1.[U_Dim1], T2.[GroupCode], MONTH(T0.[DocDate])
						) A1 ";

			if($YearSelect <= 2022) {
				$sqlSalQRY = conSAP8($sqlSal);
			}else{
				$sqlSalQRY = SAPSelect($sqlSal);
			}
			$resultSal = odbc_fetch_array($sqlSalQRY);

			// echo $resultSal['M_01']."-".$G9[1]."+".$G3[1];
			// ยอดขายปี ...
			$PYTotal = 0;
			$TbodyRT .= "<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".
							"<td class='text-start fw-bolder'>ยอดขายปี ".$YearSelect."</td>";
							for($m=1;$m<=12;$m++) {
								if($m<10) {
									if($resultSal['M_0'.$m] < 0) { $TbodyRT .= "<td class='text-right text-primary'>"; } else { $TbodyRT .= "<td class='text-right'>"; }
									$TbodyRT .= number_format($resultSal['M_0'.$m]-$G9[$m]+$G3[$m],2);
									$TbodyRT .= "</td>";
									$PYTotal = $PYTotal+($resultSal['M_0'.$m]-$G9[$m]+$G3[$m]);
								} else {
									if($resultSal['M_'.$m] < 0) { $TbodyRT .= "<td class='text-right text-primary'>"; } else { $TbodyRT .= "<td class='text-right'>"; }
									$TbodyRT .= number_format($resultSal['M_'.$m]-$G9[$m]+$G3[$m],2);
									$TbodyRT .= "</td>";
									$PYTotal = $PYTotal+($resultSal['M_'.$m]-$G9[$m]+$G3[$m]);
								}
							}
							if($PYTotal < 0) { 
								$TbodyRT .= "<td class='text-right fw-bolder text-primary'>".number_format($PYTotal,2)."</td>"; 
							} else { 
								$TbodyRT .= "<td class='text-right fw-bolder'>".number_format($PYTotal,2)."</td>"; 
							}
			$TbodyRT .= "</tr>";

			// % การคืน
				for($m=1;$m<=12;$m++) {
					if($m<10) {
						if(${"Sum_M".$m} > 0 && $resultSal['M_0'.$m] == 0) { 
							${"GROWTH_M".$m} = 100; 
						} elseif(${"Sum_M".$m} < 0 && $resultSal['M_0'.$m] == 0) { 
							${"GROWTH_M".$m} = -100; 
						} elseif(${"Sum_M".$m} == 0 && $resultSal['M_0'.$m] == 0) { 
							${"GROWTH_M".$m} = 0; 
						} else { 
							${"GROWTH_M".$m} = ((${"Sum_M".$m})/$resultSal['M_0'.$m])*100; 
						}
					} else {
						if(${"Sum_M".$m} > 0 && $resultSal['M_'.$m] == 0) { 
							${"GROWTH_M".$m} = 100; 
						} elseif(${"Sum_M".$m} < 0 && $resultSal['M_'.$m] == 0) { 
							${"GROWTH_M".$m} = -100; 
						} elseif(${"Sum_M".$m} == 0 && $resultSal['M_'.$m] == 0) { 
							${"GROWTH_M".$m} = 0; 
						} else { 
							${"GROWTH_M".$m} = ((${"Sum_M".$m})/$resultSal['M_'.$m])*100; 
						}
					}
				}

				// $Sum_AllM = ($Sum_M1+$Sum_M2+$Sum_M3+$Sum_M4+$Sum_M5+$Sum_M6+$Sum_M7+$Sum_M8+$Sum_M9+$Sum_M10+$Sum_M11+$Sum_M12);
				if($Sum_AllM > 0 && $PYTotal == 0) { 
					$GROWTH_Total = 100; 
				} elseif($Sum_AllM < 0 && $PYTotal == 0) { 
					$GROWTH_Total = -100; 
				} elseif($Sum_AllM == 0 && $PYTotal == 0) { 
					$GROWTH_Total = 0; 
				} else { 
					$GROWTH_Total = (($Sum_AllM)/abs($PYTotal))*100; 
				}
			$TbodyRT .= "<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".
							"<td class='text-start fw-bolder'>% การคืน</td>";
							for($m=1;$m<=12;$m++) {
								if(${"GROWTH_M".$m} > 2) { 
									$TbodyRT .= "<td class='text-right fw-bolder text-primary'>"; 
								} else { 
									$TbodyRT .= "<td class='text-right fw-bolder text-green'>"; 
								}
								$TbodyRT .= number_format(${"GROWTH_M".$m},2);
								$TbodyRT .= "%</td>";
							}
							if($GROWTH_Total != 0) { 
								if($GROWTH_Total > 2) { 
								$TbodyRT .= "<td class='text-right fw-bolder text-primary'>".number_format($GROWTH_Total,2)."%</td>"; 
								} else { 
									$TbodyRT .= "<td class='text-right fw-bolder text-green'>".number_format($GROWTH_Total,2)."%</td>"; 
								} 
							} else { 
								$TbodyRT .= "<td class='text-right fw-bolder'>0.00%</td>"; }
			$TbodyRT .= "</tr>";
			
			$arrCol['TeamSelect'] = $TeamSelect;
			$arrCol['YearSelect'] = $YearSelect;

			$arrCol['TbodyRT'] = $TbodyRT;

			$arrCol['TEAM'] = $TEAM;
			$arrCol['M_1'] = $M_1;
			$arrCol['M_2'] = $M_2;
			$arrCol['M_3'] = $M_3;
			$arrCol['M_4'] = $M_4;
			$arrCol['M_5'] = $M_5;
			$arrCol['M_6'] = $M_6;
			$arrCol['M_7'] = $M_7;
			$arrCol['M_8'] = $M_8;
			$arrCol['M_9'] = $M_9;
			$arrCol['M_10'] = $M_10;
			$arrCol['M_11'] = $M_11;
			$arrCol['M_12'] = $M_12;
		}
	}
		// ------------------------------------------------------------------------ Modal -------------------------------------------------------------------------
	if ($_GET['a'] == 'SelectYearRTgroup') {
		$TeamSelect = $_POST['Team'];
		$YearSelect = $_POST['Year'];
		$MonthSelect = $_POST['Month'];
		$sql = "SELECT CASE
					WHEN T0.[U_CNReason2] IN ('2.1') THEN 'G1'
					WHEN T0.[U_CNReason2] IN ('1.1','1.2','1.3','1.4','4.5','4.2') THEN 'G2'
					WHEN T0.[U_CNReason2] IN ('3.2','3.3','4.1') THEN 'G3'
					WHEN T0.[U_CNReason2] IN ('3.1') THEN 'G4'
					WHEN T0.[U_CNReason2] IN ('2.5') THEN 'G5'
					WHEN T0.[U_CNReason2] IN ('2.3','4.3') THEN 'G6'
					WHEN T0.[U_CNReason2] IN ('2.2') THEN 'G7'
					WHEN T0.[U_CNReason2] IN ('5.1') THEN 'G9'
					ELSE 'G8' END AS 'ReturnGroup', T4.[Name], T0.[DocDate],
					(T2.[BeginStr]+CAST(T0.[DocNum] AS VARCHAR)) AS 'DocNum', T0.[U_RefInv], T1.[SlpName], T0.[CardCode], T3.[CardName], (T0.[DocTotal]-T0.[VatSum]) AS 'DocTotal', T0.[DocEntry]
				FROM ORIN T0
				LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode]
				LEFT JOIN NNM1 T2 ON T0.[Series] = T2.[Series]
				LEFT JOIN OCRD T3 ON T0.[CardCode] = T3.[CardCode]
				LEFT JOIN [dbo].[@CNREASON] T4 ON T0.[U_CNReason2] = T4.[Code]
				WHERE YEAR(T0.[DocDate]) = ".$YearSelect." AND MONTH(T0.[DocDate]) = ".$MonthSelect." AND (T2.[BeginStr] LIKE 'S1-%' OR T2.[BeginStr] LIKE 'SR-%') AND T1.[U_Dim1] = '".$TeamSelect."' AND T0.CANCELED = 'N' 
				ORDER BY 'ReturnGroup', T0.[U_CNReason2], T0.[SlpCode], T0.[DocEntry]";
		if($YearSelect <= 2022) {
			$sqlQRY = conSAP8($sql);
		}else{
			$sqlQRY = SAPSelect($sql);
		}
		$Tbody = "";
		$number = 1;
		for ($c = 1; $c <= 8; $c++) { ${"column".$c} = ""; } 
		$DocEntry = "";
		while ($result = odbc_fetch_array($sqlQRY)) { 
			$DocDate = date_format(date_create($result['DocDate']),"d/m/Y");
			$column1 .= $number."|";
			$column2 .= ReturnName($result['ReturnGroup'])." <i class='fas fa-angle-right'></i> ".conutf8($result['Name'])."|";
			$column3 .= conutf8($result['SlpName'])."|";
			$column4 .= $DocDate."|";
			$column5 .= $result['DocNum']."|";
			$column6 .= conutf8($result['U_RefInv'])."|";
			$column7 .= $result['CardCode']." - ".conutf8($result['CardName'])."|";
			$column8 .= number_format($result['DocTotal'],2)."|";

			$DocEntry .= $result['DocEntry']."|";
			$number++;
		}
		$arrCol['TeamSelect'] = $TeamSelect;
		$arrCol['MonthSelect'] = FullMonth($MonthSelect);
		$arrCol['YearSelect'] = $YearSelect;

		$arrCol['column1'] = $column1;
		$arrCol['column2'] = $column2;
		$arrCol['column3'] = $column3;
		$arrCol['column4'] = $column4;
		$arrCol['column5'] = $column5;
		$arrCol['column6'] = $column6;
		$arrCol['column7'] = $column7;
		$arrCol['column8'] = $column8;
		$arrCol['DocEntry'] = $DocEntry;
	}
		// -------------------------------------------------------------------- Modal Detail -------------------------------------------------------------------------
	if ($_GET['a'] == 'RTSelectSR') {
		$DocEntry = $_POST['DocEntry'];
		$Year = $_POST['Year'];
		$sql = "SELECT TOP 1
					T0.[DocDate], (T2.[BeginStr]+CAST(T0.[DocNum] AS VARCHAR)) AS 'DocNum', T1.[SlpName], T0.[CardCode], T0.[CardName], T0.[U_RefInv], T0.[Comments],T0.[DocTotal],T0.[VatSum],
					CASE
						WHEN T0.[U_CNReason2] IN ('2.1') THEN 'G1'
						WHEN T0.[U_CNReason2] IN ('1.1','1.2','1.3','1.4','4.5','4.2') THEN 'G2'
						WHEN T0.[U_CNReason2] IN ('3.2','3.3','4.1') THEN 'G3'
						WHEN T0.[U_CNReason2] IN ('3.1') THEN 'G4'
						WHEN T0.[U_CNReason2] IN ('2.5') THEN 'G5'
						WHEN T0.[U_CNReason2] IN ('2.3','4.3') THEN 'G6'
						WHEN T0.[U_CNReason2] IN ('2.2') THEN 'G7'
						WHEN T0.[U_CNReason2] IN ('5.1') THEN 'G9'
						ELSE 'G8' END AS 'ReturnGroup', T3.[Name]
				FROM ORIN T0 
				LEFT JOIN OSLP T1 ON T0.[SlpCode] = T1.[SlpCode] 
				LEFT JOIN NNM1 T2 ON T0.[Series] = T2.[Series]
				LEFT JOIN [dbo].[@CNREASON] T3 ON T0.[U_CNReason2] = T3.[Code]
				WHERE T0.[DocEntry] = ".$DocEntry."";
				// echo $sql;

		if($Year <= 2022) {
			$sqlQRY = conSAP8($sql);
		} else {
			$sqlQRY = SAPSelect($sql);
		}
		
		$result = odbc_fetch_array($sqlQRY);
		if ($result['Name'] != "") { $Name = conutf8($result['Name']);} else { $Name = "ไม่ระบุ"; }
		$TheadMaster =	"<tr>".
							"<th class='fw-bolder'>เลขที่ใบลดหนี้</th>".
							"<th class='text-primary'>".$result['DocNum']."</th>".
							"<th class='fw-bolder'>วันที่ลดหนี้</th>".
							"<th class='text-primary'>".date_format(date_create($result['DocDate']),"d/m/Y")."</th>".
						"</tr>".
						"<tr>".
							"<th class='fw-bolder'>ชื่อลูกค้า</th>".
							"<th class='text-primary'>".$result['CardCode']." - ".conutf8($result['CardName'])."</th>".
							"<th class='fw-bolder'>ผู้แทนขาย</th>".
							"<th class='text-primary'>".conutf8($result['SlpName'])."</th>".
						"</tr>".
						"<tr>".
							"<th class='fw-bolder'>เอกสารอ้างอิง</th>".
							"<th class='text-primary'>".conutf8($result['U_RefInv'])."</th>".
							"<th class='fw-bolder'>สาเหตุการคืน</th>".
							"<th class='text-primary'>".ReturnName($result['ReturnGroup'])." <i class='fas fa-angle-right'></i> ".$Name."</th>".
						"</tr>".
						"<tr>".
							"<th class='fw-bolder'>หมายเหตุ</th>".
							"<th colspan='3' class='text-primary'>".conutf8($result['Comments'])."</th>".
						"</tr>";

		$sqlRTB = "SELECT T0.[ItemCode], T0.[Dscription], T0.[WhsCode], T0.[Price], T0.[Quantity], T0.[UnitMsr], T0.[LineTotal] FROM RIN1 T0 WHERE T0.[DocEntry] = ".$DocEntry." ORDER BY T0.[VisOrder] ASC";
		if($Year <= 2022) {
			$sqlRTBQRY = conSAP8($sqlRTB);
		} else {
			$sqlRTBQRY = SAPSelect($sqlRTB);
		}
		$row = 1;
		$Tbody = "";
		while ($resultRTB = odbc_fetch_array($sqlRTBQRY)) {
			$Tbody .= 	"<tr>".
							"<td class='text-center'>".$row."</td>".
							"<td class='text-center'>".$resultRTB['ItemCode']."</td>".
							"<td>".conutf8($resultRTB['Dscription'])."</td>".
							"<td class='text-center'>".$resultRTB['WhsCode']."</td>".
							"<td class='text-right'>".number_format($resultRTB['Price'],2)."</td>".
							"<td class='text-center'>".number_format($resultRTB['Quantity'],0)." ".conutf8($resultRTB['UnitMsr'])."</td>".
							"<td class='text-right text-primary'>".number_format($resultRTB['LineTotal'],2)."</td>".
						"</tr>";
			$row++;
		}
		$Tfooter = "<tr>
						<td colspan='6' class='text-right fw-bolder border-end'>รวมทั้งหมด</td>
						<td class='text-right text-primary fw-bolder'>".number_format(($result['DocTotal']-$result['VatSum']),2)."</td>
					</tr>
					<tr>
						<td colspan='6' class='text-right fw-bolder border-end'>ภาษีมูลค่าเพิ่ม	</td>
						<td class='text-right text-primary fw-bolder'>".number_format($result['VatSum'],2)."</td>
					</tr>
					<tr>
						<td colspan='6' class='text-right fw-bolder border-end'>ราคาสุทธิ</td>
						<td class='text-right text-primary fw-bolder'>".number_format($result['DocTotal'],2)."</td>
					</tr>";

		$arrCol['TheadMaster'] = $TheadMaster;
		$arrCol['Tbody'] = $Tbody;
		$arrCol['Tfooter'] = $Tfooter;
	}

		// ----------------------------------------------------------------------[ คืน QC ]-------------------------------------------------------------------------
	if ($_GET['a'] == 'QC') {
		$TeamSelect = $_POST['Team'];
		$YearSelect = $_POST['Year'];
		// ------------------------------------------------------------------------ ALL --------------------------------------------------------------------------
		if ($TeamSelect == 'all') {
			$sql = "SELECT
							A2.[Team], A2.[RType],
							SUM(A2.[M_01]) AS 'M_01', SUM(A2.[M_02]) AS 'M_02', SUM(A2.[M_03]) AS 'M_03',
							SUM(A2.[M_04]) AS 'M_04', SUM(A2.[M_05]) AS 'M_05', SUM(A2.[M_06]) AS 'M_06',
							SUM(A2.[M_07]) AS 'M_07', SUM(A2.[M_08]) AS 'M_08', SUM(A2.[M_09]) AS 'M_09',
							SUM(A2.[M_10]) AS 'M_10', SUM(A2.[M_11]) AS 'M_11', SUM(A2.[M_12]) AS 'M_12'
					FROM (
					SELECT
							A1.[Team],
							CASE WHEN A1.[RCode] IN ('K01','K02','K03','R01','P01') THEN 'KBI' WHEN A1.[RCode] IN ('S01','S02','S03') THEN 'SAL' ELSE 'SAL' END AS 'RType',
							CASE WHEN A1.[MONTH] = 1 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_01',
							CASE WHEN A1.[MONTH] = 2 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_02',
							CASE WHEN A1.[MONTH] = 3 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_03',
							CASE WHEN A1.[MONTH] = 4 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_04',
							CASE WHEN A1.[MONTH] = 5 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_05',
							CASE WHEN A1.[MONTH] = 6 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_06',
							CASE WHEN A1.[MONTH] = 7 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_07',
							CASE WHEN A1.[MONTH] = 8 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_08',
							CASE WHEN A1.[MONTH] = 9 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_09',
							CASE WHEN A1.[MONTH] = 10 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_10',
							CASE WHEN A1.[MONTH] = 11 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_11',
							CASE WHEN A1.[MONTH] = 12 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_12'
					FROM (
					SELECT
					T0.[DocDate] AS 'QCDocDate', YEAR(T0.[DocDate]) AS 'YEAR', MONTH(T0.[DocDate]) AS 'MONTH',
					T3.[Name] AS 'ReturnType',(T1.[BeginStr]+CAST(T0.[DocNum] AS VARCHAR)) AS 'DocNum', T0.[U_RefNoCust] AS 'RefNum',
					T5.[SlpName], T5.[U_Dim1] AS 'Team', T5.[Memo] AS 'SlpCode',
					T0.[CardCode],T0.[CardName],T2.[ItemCode],T2.[Dscription],T2.[Quantity],T2.[UnitMsr],
					(T2.[GrossBuyPr]*1.07) AS 'Cost(VAT)',((T2.[GrossBuyPr]*1.07)*T2.[Quantity]) AS 'CostSum(VAT)',
					T2.[WhsCode], T4.[Name] AS 'PName',T6.[Name] AS 'RName',
					CASE
							WHEN T2.[WhsCode] IN ('KSY','KSM','KB4','KBM','PM','PM-HR','PM-KBI','PM-KSY') THEN 'K01'
							WHEN T2.[WhsCode] IN ('KB5','KB5.1','KB6','KB6.1') THEN 'K02'
							WHEN T2.[WhsCode] IN ('WP','WP1','WP2','WP2.2','WP3','WP4','WP5','WP6','WP6-AGT','WP6-JSI','WP6-KN','WP6-KS','WP7') THEN 'K03'
							WHEN T2.[WhsCode] IN ('MT','MT2','TT-C','OUL','KB1','WP01') THEN 'S01'
							WHEN T2.[WhsCode] IN ('WM1','WM2','TT','PM-TT','KB7','WA26.1') THEN 'S02'
							WHEN T2.[WhsCode] IN ('WM1.1','WM2.1','TT2.1') THEN 'S03'
							WHEN T2.[WhsCode] IN ('RD','RD2','RD3','RD4') THEN 'R01'
							WHEN T2.[WhsCode] IN ('P01') THEN 'P01'
					ELSE 'XXX' END AS 'RCode',
					CASE
							WHEN T0.[U_CNReason2] IN ('3.1') THEN 'T02'
							WHEN T0.[U_CNReason2] IN ('2.4','2.6','2.7','3.6') THEN 'T03'
					ELSE 'T01' END AS 'TCode'
					FROM ORDN T0
					LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
					LEFT JOIN RDN1 T2 ON T0.[DocEntry] = T2.[DocEntry]
					LEFT JOIN [@RETURN_TYPE] T3 ON T0.[U_Return_type] = T3.[Code]
					LEFT JOIN [@GRADE_ITEM] T4 ON T2.[U_Grade_Item] = T4.[Code]
					LEFT JOIN OSLP T5 ON T0.[SlpCode] = T5.[SlpCode]
					LEFT JOIN [@CNREASON] T6 ON T0.[U_CNReason2] = T6.[Code]
					LEFT JOIN ORIN T7 ON T0.[U_RefNoCust] = T7.[U_RefNoCust]
					WHERE 
					YEAR(T0.[DocDate]) = '".$YearSelect."' AND T0.[DocStatus] = 'C' AND T0.CANCELED = 'N'
					AND (T1.[SeriesName] LIKE '%ST%' OR T1.[SeriesName] LIKE '%RN%' OR T1.[SeriesName] LIKE '%PN%' OR T1.[SeriesName] LIKE '%PE%' OR T1.[SeriesName] LIKE '%IN%' OR T1.[SeriesName] LIKE '%IM%' OR T1.[SeriesName] LIKE '%SI%') AND (T0.[CANCELED] = 'N')
					) A1
					GROUP BY A1.[Team], A1.[RCode], A1.[MONTH], A1.[ReturnType]
					) A2 GROUP BY A2.[Team], A2.[RType]
					ORDER BY
					CASE
							WHEN A2.[Team] = 'MT1' THEN 1
							WHEN A2.[Team] = 'MT2' THEN 2
							WHEN A2.[Team] = 'TT1' THEN 3
							WHEN A2.[Team] = 'TT2' THEN 4
							WHEN A2.[Team] = 'OUL' THEN 5
							WHEN A2.[Team] = 'ONL' THEN 6
							WHEN A2.[Team] = 'EI1' THEN 7
							WHEN A2.[Team] = 'DMN' THEN 8
					ELSE 9 END,
					CASE WHEN A2.[RType] = 'KBI' THEN 1 ELSE 2 END";
					// echo $sql;
			$sqlQRY = SAPSelect($sql);
			$TeamM = ['MT1', 'MT2', 'TT1', 'TT2', 'OUL', 'ONL', 'DMN', 'KBI'];
			$CompanyM = array();
			$SaleM = array();
			$SumM = array(); 
			for($t = 0; $t <= count($TeamM)-1; $t++) { 
				for($i = 1; $i <= 12; $i++) { 
					$CompanyM[$TeamM[$t]][$i] = 0;
					$SaleM[$TeamM[$t]][$i]    = 0; 
					$SumM[$TeamM[$t]][$i]     = 0; 
				} 
				$CompanyM[$TeamM[$t]]['All'] = 0;
				$SaleM[$TeamM[$t]]['All']    = 0; 
				$SumM[$TeamM[$t]]['All']     = 0; 
			}

			$SumM_1 = 0; $SumM_2 = 0; $SumM_3 = 0; $SumM_4 = 0; $SumM_5 = 0; $SumM_6 = 0; $SumM_7 = 0; $SumM_8 = 0; $SumM_9 = 0; $SumM_10 = 0; $SumM_11 = 0; $SumM_12 = 0;
			$Sum_Row = 0;
			$Tbody = "";
			$ChkTeam = "";
			while ($result = odbc_fetch_array($sqlQRY)) { 
				switch ($result['RType']) {
					case 'KBI':
						$Sum_Row = ($result['M_01']+$result['M_02']+$result['M_03']+$result['M_04']+$result['M_05']+$result['M_06']+$result['M_07']+$result['M_08']+$result['M_09']+$result['M_10']+$result['M_11']+$result['M_12']);
						for ($m = 1; $m <=12; $m++) {
							if($m < 10) {
								$CompanyM[$result['Team']][$m] = $result['M_0'.$m];
							}else{
								$CompanyM[$result['Team']][$m] = $result['M_'.$m];
							}
						}
						$CompanyM[$result['Team']]['All'] = $Sum_Row;
						break;
					case 'SAL':
						$Sum_Row = ($result['M_01']+$result['M_02']+$result['M_03']+$result['M_04']+$result['M_05']+$result['M_06']+$result['M_07']+$result['M_08']+$result['M_09']+$result['M_10']+$result['M_11']+$result['M_12']);
						for ($m = 1; $m <=12; $m++) {
							if($m < 10) {
								$SaleM[$result['Team']][$m] = $result['M_0'.$m];
							}else{
								$SaleM[$result['Team']][$m] = $result['M_'.$m];
							}
						}
						$SaleM[$result['Team']]['All'] = $Sum_Row;
						break;
					default: break;
				}
			}

			for($t = 0; $t <= count($TeamM)-1; $t++) { 
				for($r = 1; $r <= 3; $r++) {
					if($r == 1) {
						$Tbody.="<tr class='text-right'>".
							"<td class='text-center text-primary fw-bolder '></td>".
							"<td class='text-start'>บริษัทรับผิดชอบ</td>";
							for($i = 1; $i <= 12; $i++) { 
								$Tbody.="<td>".number_format($CompanyM[$TeamM[$t]][$i],2)."</td>";
							} 
							$Tbody.="<td class='fw-bolder'>".number_format($CompanyM[$TeamM[$t]]['All'],2)."</td>".
						"</tr>";
					}elseif($r == 2){
						$Tbody.="<tr class='text-right'>".
							"<td class='text-center text-primary fw-bolder '>".TeamName_Th($TeamM[$t])."</td>".
							"<td class='text-start'>Sale รับผิดชอบ</td>";
							for($i = 1; $i <= 12; $i++) { 
								$Tbody.="<td>".number_format($SaleM[$TeamM[$t]][$i],2)."</td>";
							} 
							$Tbody.="<td class='fw-bolder'>".number_format($SaleM[$TeamM[$t]]['All'],2)."</td>".
						"</tr>";
					}else{
						$Tbody.="<tr class='text-right fw-bolder'>".
							"<td class='text-center text-primary fw-bolder border-bottom'></td>".
							"<td class='text-start' style='background-color: rgba(0, 0, 0, 0.04);'>รวมทั้งหมด</td>";
							for($i = 1; $i <= 12; $i++) { 
								$SumM[$TeamM[$t]][$i] = ($CompanyM[$TeamM[$t]][$i]+$SaleM[$TeamM[$t]][$i]);
								$SumM[$TeamM[$t]]['All'] = ($SumM[$TeamM[$t]]['All']+$SumM[$TeamM[$t]][$i]);
								$Tbody.="<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumM[$TeamM[$t]][$i],2)."</td>";
							} 
							$Tbody.="<td class='fw-bolder' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumM[$TeamM[$t]]['All'],2)."</td>".
						"</tr>";
					}
				}
			}
			

			$arrCol['TeamSelect'] = $TeamSelect;
			$arrCol['Tbody'] = $Tbody;
		} 

		// ------------------------------------------------------------------------ Team -------------------------------------------------------------------------
		if ($TeamSelect != 'all') {
			$sql = "SELECT
							A2.[Team], A2.[RType], A2.[TCode],
							SUM(A2.[M_01]) AS 'M_01', SUM(A2.[M_02]) AS 'M_02', SUM(A2.[M_03]) AS 'M_03',
							SUM(A2.[M_04]) AS 'M_04', SUM(A2.[M_05]) AS 'M_05', SUM(A2.[M_06]) AS 'M_06',
							SUM(A2.[M_07]) AS 'M_07', SUM(A2.[M_08]) AS 'M_08', SUM(A2.[M_09]) AS 'M_09',
							SUM(A2.[M_10]) AS 'M_10', SUM(A2.[M_11]) AS 'M_11', SUM(A2.[M_12]) AS 'M_12'
					FROM (
					SELECT
							A1.[Team],
							CASE WHEN A1.[RCode] IN ('K01','K02','K03','R01','P01') THEN 'KBI' WHEN A1.[RCode] IN ('S01','S02','S03') THEN 'SAL' ELSE 'SAL' END AS 'RType',
							A1.[TCode],
							CASE WHEN A1.[MONTH] = 1 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_01',
							CASE WHEN A1.[MONTH] = 2 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_02',
							CASE WHEN A1.[MONTH] = 3 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_03',
							CASE WHEN A1.[MONTH] = 4 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_04',
							CASE WHEN A1.[MONTH] = 5 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_05',
							CASE WHEN A1.[MONTH] = 6 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_06',
							CASE WHEN A1.[MONTH] = 7 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_07',
							CASE WHEN A1.[MONTH] = 8 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_08',
							CASE WHEN A1.[MONTH] = 9 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_09',
							CASE WHEN A1.[MONTH] = 10 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_10',
							CASE WHEN A1.[MONTH] = 11 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_11',
							CASE WHEN A1.[MONTH] = 12 AND A1.[ReturnType] IS NOT NULL THEN SUM(A1.[CostSum(VAT)]) ELSE 0 END AS 'M_12'
					FROM (
					SELECT
					T0.[DocDate] AS 'QCDocDate', YEAR(T0.[DocDate]) AS 'YEAR', MONTH(T0.[DocDate]) AS 'MONTH',
					T3.[Name] AS 'ReturnType',(T1.[BeginStr]+CAST(T0.[DocNum] AS VARCHAR)) AS 'DocNum', T0.[U_RefNoCust] AS 'RefNum',
					T5.[SlpName], T5.[U_Dim1] AS 'Team', T5.[Memo] AS 'SlpCode',
					T0.[CardCode],T0.[CardName],T2.[ItemCode],T2.[Dscription],T2.[Quantity],T2.[UnitMsr],
					(T2.[GrossBuyPr]*1.07) AS 'Cost(VAT)',((T2.[GrossBuyPr]*1.07)*T2.[Quantity]) AS 'CostSum(VAT)',
					T2.[WhsCode], T4.[Name] AS 'PName',T6.[Name] AS 'RName',
					CASE
							WHEN T2.[WhsCode] IN ('KSY','KSM','KB4','KBM','PM','PM-HR','PM-KBI','PM-KSY') THEN 'K01'
							WHEN T2.[WhsCode] IN ('KB5','KB5.1','KB6','KB6.1') THEN 'K02'
							WHEN T2.[WhsCode] IN ('WP','WP1','WP2','WP2.2','WP3','WP4','WP5','WP6','WP6-AGT','WP6-JSI','WP6-KN','WP6-KS','WP7') THEN 'K03'
							WHEN T2.[WhsCode] IN ('MT','MT2','TT-C','OUL','KB1','WP01') THEN 'S01'
							WHEN T2.[WhsCode] IN ('WM1','WM2','TT','PM-TT','KB7') THEN 'S02'
							WHEN T2.[WhsCode] IN ('WM1.1','WM2.1','TT2.1') THEN 'S03'
							WHEN T2.[WhsCode] IN ('RD','RD2','RD3','RD4') THEN 'R01'
							WHEN T2.[WhsCode] IN ('P01') THEN 'P01'
					ELSE 'XXX' END AS 'RCode',
					CASE
							WHEN T0.[U_CNReason2] IN ('3.1') THEN 'T02'
							WHEN T0.[U_CNReason2] IN ('2.4','2.6','2.7','3.6') THEN 'T03'
					ELSE 'T01' END AS 'TCode'
					FROM ORDN T0
					LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
					LEFT JOIN RDN1 T2 ON T0.[DocEntry] = T2.[DocEntry]
					LEFT JOIN [@RETURN_TYPE] T3 ON T0.[U_Return_type] = T3.[Code]
					LEFT JOIN [@GRADE_ITEM] T4 ON T2.[U_Grade_Item] = T4.[Code]
					LEFT JOIN OSLP T5 ON T0.[SlpCode] = T5.[SlpCode]
					LEFT JOIN [@CNREASON] T6 ON T0.[U_CNReason2] = T6.[Code]
					LEFT JOIN ORIN T7 ON T0.[U_RefNoCust] = T7.[U_RefNoCust]
					WHERE 
					YEAR(T0.[DocDate]) = '".$YearSelect."' AND T0.[DocStatus] = 'C' AND T5.[U_Dim1] = '".$TeamSelect."' AND T0.CANCELED = 'N'
					AND (T1.[SeriesName] LIKE '%ST%' OR T1.[SeriesName] LIKE '%RN%' OR T1.[SeriesName] LIKE '%PN%' OR T1.[SeriesName] LIKE '%PE%' OR T1.[SeriesName] LIKE '%IN%' OR T1.[SeriesName] LIKE '%IM%' OR T1.[SeriesName] LIKE '%SI%') AND (T0.[CANCELED] = 'N')
					) A1
					GROUP BY A1.[Team], A1.[RCode], A1.[TCode], A1.[MONTH], A1.[ReturnType]
					) A2 GROUP BY A2.[Team], A2.[RType], A2.[TCode]
					ORDER BY
					CASE
							WHEN A2.[Team] = 'MT1' THEN 1
							WHEN A2.[Team] = 'MT2' THEN 2
							WHEN A2.[Team] = 'TT1' THEN 3
							WHEN A2.[Team] = 'TT2' THEN 4
							WHEN A2.[Team] = 'OUL' THEN 5
							WHEN A2.[Team] = 'ONL' THEN 6
							WHEN A2.[Team] = 'EI1' THEN 7
							WHEN A2.[Team] = 'DMN' THEN 8
					ELSE 9 END,
					CASE WHEN A2.[RType] = 'KBI' THEN 1 ELSE 2 END, A2.[TCode] ASC";
			$sqlQRY = SAPSelect($sql);
			$Tbody = "";
			$KBI1M_1 = 0; $KBI1M_2 = 0; $KBI1M_3 = 0; $KBI1M_4 = 0; $KBI1M_5 = 0; $KBI1M_6 = 0; $KBI1M_7 = 0; $KBI1M_8 = 0; $KBI1M_9 = 0; $KBI1M_10 = 0; $KBI1M_11 = 0; $KBI1M_12 = 0;
			$KBI2M_1 = 0; $KBI2M_2 = 0; $KBI2M_3 = 0; $KBI2M_4 = 0; $KBI2M_5 = 0; $KBI2M_6 = 0; $KBI2M_7 = 0; $KBI2M_8 = 0; $KBI2M_9 = 0; $KBI2M_10 = 0; $KBI2M_11 = 0; $KBI2M_12 = 0;
			$SumKBI_Row1 = 0; $SumKBI_Row2 = 0; $SumKBIAll_Row = 0;
			$SumKBIM_1 = 0; $SumKBIM_2 = 0; $SumKBIM_3 = 0; $SumKBIM_4 = 0; $SumKBIM_5 = 0; $SumKBIM_6 = 0; $SumKBIM_7 = 0; $SumKBIM_8 = 0; $SumKBIM_9 = 0; $SumKBIM_10 = 0; $SumKBIM_11 = 0; $SumKBIM_12 = 0;

			$SAL1M_1 = 0; $SAL1M_2 = 0; $SAL1M_3 = 0; $SAL1M_4 = 0; $SAL1M_5 = 0; $SAL1M_6 = 0; $SAL1M_7 = 0; $SAL1M_8 = 0; $SAL1M_9 = 0; $SAL1M_10 = 0; $SAL1M_11 = 0; $SAL1M_12 = 0;
			$SAL2M_1 = 0; $SAL2M_2 = 0; $SAL2M_3 = 0; $SAL2M_4 = 0; $SAL2M_5 = 0; $SAL2M_6 = 0; $SAL2M_7 = 0; $SAL2M_8 = 0; $SAL2M_9 = 0; $SAL2M_10 = 0; $SAL2M_11 = 0; $SAL2M_12 = 0;
			$SumSAL_Row1 = 0; $SumSAL_Row2 = 0; $SumSALAll_Row = 0;
			$SumSALM_1 = 0; $SumSALM_2 = 0; $SumSALM_3 = 0; $SumSALM_4 = 0; $SumSALM_5 = 0; $SumSALM_6 = 0; $SumSALM_7 = 0; $SumSALM_8 = 0; $SumSALM_9 = 0; $SumSALM_10 = 0; $SumSALM_11 = 0; $SumSALM_12 = 0;
			
			while ($result = odbc_fetch_array($sqlQRY)) {
				if ($result['RType'] == 'KBI') {
					switch ($result['TCode']) {
						case 'T01':
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"SumKBIM_".$m} = ${"SumKBIM_".$m}+$result['M_0'.$m];
								}else{
									${"SumKBIM_".$m} = ${"SumKBIM_".$m}+$result['M_'.$m];
								}
							}
							break; 
						case 'T02':
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"KBI1M_".$m} = ${"KBI1M_".$m}+$result['M_0'.$m];
								}else{
									${"KBI1M_".$m} = ${"KBI1M_".$m}+$result['M_'.$m];
								}
							}
							$SumKBI_Row1 = $SumKBI_Row1+($result['M_01']+$result['M_02']+$result['M_03']+$result['M_04']+$result['M_05']+$result['M_06']+$result['M_07']+$result['M_08']+$result['M_09']+$result['M_10']+$result['M_11']+$result['M_12']);
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"SumKBIM_".$m} = ${"SumKBIM_".$m}+$result['M_0'.$m];
								}else{
									${"SumKBIM_".$m} = ${"SumKBIM_".$m}+$result['M_'.$m];
								}
							}
							break;
						case 'T03':
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"KBI2M_".$m} = ${"KBI2M_".$m}+$result['M_0'.$m];
								}else{
									${"KBI2M_".$m} = ${"KBI2M_".$m}+$result['M_'.$m];
								}
							}
							$SumKBI_Row2 = $SumKBI_Row2+($result['M_01']+$result['M_02']+$result['M_03']+$result['M_04']+$result['M_05']+$result['M_06']+$result['M_07']+$result['M_08']+$result['M_09']+$result['M_10']+$result['M_11']+$result['M_12']);
							
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"SumKBIM_".$m} = ${"SumKBIM_".$m}+$result['M_0'.$m];
								}else{
									${"SumKBIM_".$m} = ${"SumKBIM_".$m}+$result['M_'.$m];
								}
							}
							break;
						default: break;
					}
				}

				if ($result['RType'] == 'SAL') {
					switch ($result['TCode']) {
						case 'T01':
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"SumSALM_".$m} = ${"SumSALM_".$m}+$result['M_0'.$m];
								}else{
									${"SumSALM_".$m} = ${"SumSALM_".$m}+$result['M_'.$m];
								}
							}
							break; 
						case 'T02':
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"SAL1M_".$m} = ${"SAL1M_".$m}+$result['M_0'.$m];
								}else{
									${"SAL1M_".$m} = ${"SAL1M_".$m}+$result['M_'.$m];
								}
							}
							$SumSAL_Row1 = $SumSAL_Row1+($result['M_01']+$result['M_02']+$result['M_03']+$result['M_04']+$result['M_05']+$result['M_06']+$result['M_07']+$result['M_08']+$result['M_09']+$result['M_10']+$result['M_11']+$result['M_12']);
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"SumSALM_".$m} = ${"SumSALM_".$m}+$result['M_0'.$m];
								}else{
									${"SumSALM_".$m} = ${"SumSALM_".$m}+$result['M_'.$m];
								}
							}
							break;
						case 'T03':
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"SAL2M_".$m} = ${"SAL2M_".$m}+$result['M_0'.$m];
								}else{
									${"SAL2M_".$m} = ${"SAL2M_".$m}+$result['M_'.$m];
								}
							}
							$SumSAL_Row2 = $SumSAL_Row2+($result['M_01']+$result['M_02']+$result['M_03']+$result['M_04']+$result['M_05']+$result['M_06']+$result['M_07']+$result['M_08']+$result['M_09']+$result['M_10']+$result['M_11']+$result['M_12']);
							
							for ($m = 1; $m <=12; $m++) {
								if($m < 10) {
									${"SumSALM_".$m} = ${"SumSALM_".$m}+$result['M_0'.$m];
								}else{
									${"SumSALM_".$m} = ${"SumSALM_".$m}+$result['M_'.$m];
								}
							}
							break;
						default: break;
					}
				}
			}
			$SumKBIAll_Row = $SumKBIAll_Row+($SumKBIM_1+$SumKBIM_2+$SumKBIM_3+$SumKBIM_4+$SumKBIM_5+$SumKBIM_6+$SumKBIM_7+$SumKBIM_8+$SumKBIM_9+$SumKBIM_10+$SumKBIM_11+$SumKBIM_12);
			$SumSALAll_Row = $SumSALAll_Row+($SumSALM_1+$SumSALM_2+$SumSALM_3+$SumSALM_4+$SumSALM_5+$SumSALM_6+$SumSALM_7+$SumSALM_8+$SumSALM_9+$SumSALM_10+$SumSALM_11+$SumSALM_12);
			$Tbody .="<tr class='text-right'>".
								"<td class='text-center text-primary fw-bolder'></td>".
								"<td class='text-start'>สินค้าไม่เคลื่อนไหว</td>".
								"<td>".number_format($KBI1M_1,2)."</td>".
								"<td>".number_format($KBI1M_2,2)."</td>".
								"<td>".number_format($KBI1M_3,2)."</td>".
								"<td>".number_format($KBI1M_4,2)."</td>".
								"<td>".number_format($KBI1M_5,2)."</td>".
								"<td>".number_format($KBI1M_6,2)."</td>".
								"<td>".number_format($KBI1M_7,2)."</td>".
								"<td>".number_format($KBI1M_8,2)."</td>".
								"<td>".number_format($KBI1M_9,2)."</td>".
								"<td>".number_format($KBI1M_10,2)."</td>".
								"<td>".number_format($KBI1M_11,2)."</td>".
								"<td>".number_format($KBI1M_12,2)."</td>".
								"<td class='fw-bolder'>".number_format($SumKBI_Row1,2)."</td>".
							"</tr>";

			$Tbody .="<tr class='text-right'>".
						"<td class='text-center text-primary fw-bolder'>บริษัทรับผิดชอบ</td>".
						"<td class='text-start'>ยืมออกบูธ</td>".
						"<td>".number_format($KBI2M_1,2)."</td>".
						"<td>".number_format($KBI2M_2,2)."</td>".
						"<td>".number_format($KBI2M_3,2)."</td>".
						"<td>".number_format($KBI2M_4,2)."</td>".
						"<td>".number_format($KBI2M_5,2)."</td>".
						"<td>".number_format($KBI2M_6,2)."</td>".
						"<td>".number_format($KBI2M_7,2)."</td>".
						"<td>".number_format($KBI2M_8,2)."</td>".
						"<td>".number_format($KBI2M_9,2)."</td>".
						"<td>".number_format($KBI2M_10,2)."</td>".
						"<td>".number_format($KBI2M_11,2)."</td>".
						"<td>".number_format($KBI2M_12,2)."</td>".
						"<td class='fw-bolder'>".number_format($SumKBI_Row2,2)."</td>".
					"</tr>";

			$Tbody .="<tr class='text-right fw-bolder'>".
					"<td class='text-center text-primary fw-bolder border-bottom'></td>".
					"<td class='text-start' style='background-color: rgba(0, 0, 0, 0.04);'>รวมทั้งหมด</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_1,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_2,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_3,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_4,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_5,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_6,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_7,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_8,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_9,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_10,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_11,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIM_12,2)."</td>".
					"<td class='fw-bolder' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumKBIAll_Row,2)."</td>".
				"tr";
			$Tbody .="<tr class='text-right'>".
								"<td class='text-center text-primary fw-bolder'></td>".
								"<td class='text-start'>สินค้าไม่เคลื่อนไหว</td>".
								"<td>".number_format($SAL1M_1,2)."</td>".
								"<td>".number_format($SAL1M_2,2)."</td>".
								"<td>".number_format($SAL1M_3,2)."</td>".
								"<td>".number_format($SAL1M_4,2)."</td>".
								"<td>".number_format($SAL1M_5,2)."</td>".
								"<td>".number_format($SAL1M_6,2)."</td>".
								"<td>".number_format($SAL1M_7,2)."</td>".
								"<td>".number_format($SAL1M_8,2)."</td>".
								"<td>".number_format($SAL1M_9,2)."</td>".
								"<td>".number_format($SAL1M_10,2)."</td>".
								"<td>".number_format($SAL1M_11,2)."</td>".
								"<td>".number_format($SAL1M_12,2)."</td>".
								"<td class='fw-bolder'>".number_format($SumSAL_Row1,2)."</td>".
							"</tr>";

			$Tbody .="<tr class='text-right'>".
						"<td class='text-center text-primary fw-bolder'>Sale รับผิดชอบ</td>".
						"<td class='text-start'>ยืมออกบูธ</td>".
						"<td>".number_format($SAL2M_1,2)."</td>".
						"<td>".number_format($SAL2M_2,2)."</td>".
						"<td>".number_format($SAL2M_3,2)."</td>".
						"<td>".number_format($SAL2M_4,2)."</td>".
						"<td>".number_format($SAL2M_5,2)."</td>".
						"<td>".number_format($SAL2M_6,2)."</td>".
						"<td>".number_format($SAL2M_7,2)."</td>".
						"<td>".number_format($SAL2M_8,2)."</td>".
						"<td>".number_format($SAL2M_9,2)."</td>".
						"<td>".number_format($SAL2M_10,2)."</td>".
						"<td>".number_format($SAL2M_11,2)."</td>".
						"<td>".number_format($SAL2M_12,2)."</td>".
						"<td class='fw-bolder'>".number_format($SumSAL_Row2,2)."</td>".
					"</tr>";

			$Tbody .="<tr class='text-right fw-bolder'>".
					"<td class='text-center text-primary fw-bolder border-bottom'></td>".
					"<td class='text-start' style='background-color: rgba(0, 0, 0, 0.04);'>รวมทั้งหมด</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_1,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_2,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_3,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_4,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_5,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_6,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_7,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_8,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_9,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_10,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_11,2)."</td>".
					"<td style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALM_12,2)."</td>".
					"<td class='fw-bolder' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumSALAll_Row,2)."</td>".
				"tr";
			$arrCol['TeamSelect'] = $TeamSelect;
			$arrCol['Tbody'] = $Tbody;
		}
	}
// END รายงานการคืน

// รายงานคลังสินค้า
	// LastPurPrc => LstEvlPric
	if ($_GET['a'] == 'Warehouse') {
		$checkWG = 12;
		for($i = 1; $i <= $checkWG; $i++) { ${"WG{$i}"} = ""; }
		$json_WG = array();
		$WG_colm = array();
		for($c = 1; $c <= $checkWG; $c++) {
			if ($c <= 9) {
				${"sql_WG{$c}"} = "SELECT T0.WhseCode FROM whsemd T0 WHERE T0.WhseMainGrpCode = 'WG0".$c."' AND T0.WhseCode IS NOT NULL";
				${"sqlQRY_WG{$c}"} = MySQLSelectX(${"sql_WG{$c}"});
				while (${"result_WG{$c}"} = mysqli_fetch_array(${"sqlQRY_WG{$c}"})) {
					${"WG{$c}"} .= ${"result_WG{$c}"}['WhseCode'].",";
				}
			}else{
				${"sql_WG{$c}"} = "SELECT T0.WhseCode FROM whsemd T0 WHERE T0.WhseMainGrpCode = 'WG".$c."' AND T0.WhseCode IS NOT NULL";
				${"sqlQRY_WG{$c}"} = MySQLSelectX(${"sql_WG{$c}"});
				while (${"result_WG{$c}"} = mysqli_fetch_array(${"sqlQRY_WG{$c}"})) {
					${"WG{$c}"} .= ${"result_WG{$c}"}['WhseCode'].",";
				}
			}
			// echo substr($WG10,0,-1);
			${"sql_Invt{$c}"}           = "SELECT ISNULL(SUM((CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T2.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07)*T0.OnHand),0) AS 'InvtCost' FROM OITW T0 LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode LEFT JOIN KBI_DB2022.dbo.OITM T2 ON T0.ItemCode = T2.ItemCode WHERE T0.WhsCode IN (".substr(${"WG{$c}"},0,-1).")";
			// echo ${"sql_Invt{$c}"}."\n";
			${"sqlQRY_Invt{$c}"}        = SAPSelect(${"sql_Invt{$c}"});
			${"result_Invt{$c}"}        = odbc_fetch_array(${"sqlQRY_Invt{$c}"});
			${"Data_WG{$c}"}            = ${"result_Invt{$c}"}['InvtCost'];
			${"WG_colm['WG0{$c}_txt']"} = NumToUnit(${"Data_WG{$c}"},2);
			${"WG_colm['WG{$c}_int']"}  = ${"Data_WG{$c}"};
			// echo $c.". ".${"WG_colm['WG0{$c}_txt']"}."\n";
			// echo $c.". ".${"WG_colm['WG{$c}_int']"}."\n";
			array_push($json_WG,${"WG_colm['WG0{$c}_txt']"});
			array_push($json_WG,${"WG_colm['WG{$c}_int']"});
		}
		// echo $json_WG;
		$Data = "";
		for ($i = 0; $i <= count($json_WG)-1; $i++) {
			$Data .= $json_WG[$i]."|";
		}
		// echo $Data;
		$arrCol['Data'] = substr($Data,0,-1);
	}

	if ($_GET['a'] == 'WG') {
		$WG = $_POST['WG'];
		$sqlNamwWG = "SELECT T0.WhseGrpName FROM whsegrpname T0 WHERE T0.WhseGrpCode = '".$WG."' AND T0.WhseMainGrpCode IS NULL LIMIT 1";
		$resultNameWG = MySQLSelect($sqlNamwWG);
		$Name = $resultNameWG['WhseGrpName'];

		$sqlWhsemd = "SELECT T0.WhseSubGrpCode, T1.WhseGrpName, T0.WhseCode FROM whsemd T0 LEFT JOIN whsegrpname T1 ON T0.WhseSubGrpCode = T1.WhseGrpCode WHERE T0.WhseMainGrpCode = '".$WG."' AND T0.WhseCode IS NOT NULL";
		$sqlQRYWhsemd = MySQLSelectX($sqlWhsemd);
		$Data = "";
		while ($resultWhsemd = mysqli_fetch_array($sqlQRYWhsemd)) {
			$sqlInvt = "SELECT ISNULL(SUM((CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T2.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07)*T0.OnHand),0) AS 'InvtCost' FROM OITW T0 LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode LEFT JOIN KBI_DB2022.dbo.OITM T2 ON T0.ItemCode = T2.ItemCode WHERE T0.WhsCode IN (".$resultWhsemd['WhseCode'].")";
			$sqlQRYInvt = SAPSelect($sqlInvt);
			$resultInvt = odbc_fetch_array($sqlQRYInvt);
			$Data .="<div class='col-lg-4'>".
						"<div class='table-responsive'>".
							"<table class='table table-bordered rounded rounded-3 overflow-hidden' style='width:100%'>".
								"<thead style='background-color: rgba(155, 0, 0, 0.04);'>".
									"<tr>".
										"<th class='text-primary'><i class='fas fa-warehouse'></i> ".$resultWhsemd['WhseGrpName']."</th>".
									"</tr>".
								"</thead>".
								"<tbody>".
									"<tr>".
										"<td class='text-right'>".
											"<p class='m-0 pt-4 pb-4'>".NumToUnit($resultInvt['InvtCost'],2)." ล้านบาท</p>".
											"<p class='m-0' style='font-size: 14px;'><a href='javascript:void(0);' class='WSG' data-wsg='".$resultWhsemd['WhseSubGrpCode']."' data-ws-name='".$resultWhsemd['WhseGrpName']."'>ข้อมูลสินค้าคงคลัง</a></p>".
										"</td>".
									"</tr>".
								"</tbody>".
							"</table>".
						"</div>".
					"</div>";
		}
		$arrCol['Data'] = $Data;
		$arrCol['Name'] = $Name;
	}
	if ($_GET['a'] == 'WSG') {
		$WSG = $_POST['WSG'];
		$sqlWSG = "SELECT T0.WhseCode FROM whsemd T0 WHERE T0.WhseSubGrpCode = '".$WSG."'";
		$resultWSG = MySQLSelect($sqlWSG);
		$sql = "SELECT
					A1.[WhsCode], A1.[WhsName], SUM(A1.[InvtCost_D]) AS 'InvtCost_D', SUM(A1.[InvtCost_A]) AS 'InvtCost_A', SUM(A1.[InvtCost_W]) AS 'InvtCost_W', SUM(A1.[InvtCost_N]) AS 'InvtCost_N', SUM(A1.[InvtCost_M]) AS 'InvtCost_M', SUM(A1.[InvtCost_O]) AS 'InvtCost_O'
				FROM (
					SELECT
						T0.[WhsCode], T2.[WhsName], 
						CASE WHEN T1.[U_ProductStatus] LIKE 'D%' THEN SUM((CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07)*T0.[OnHand]) ELSE 0 END AS 'InvtCost_D',
						CASE WHEN T1.[U_ProductStatus] = 'A' THEN SUM((CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07)*T0.[OnHand]) ELSE 0 END AS 'InvtCost_A',
						CASE WHEN T1.[U_ProductStatus] = 'W' THEN SUM((CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07)*T0.[OnHand]) ELSE 0 END AS 'InvtCost_W',
						CASE WHEN T1.[U_ProductStatus] = 'N' THEN SUM((CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07)*T0.[OnHand]) ELSE 0 END AS 'InvtCost_N',
						CASE WHEN T1.[U_ProductStatus] = 'M' THEN SUM((CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07)*T0.[OnHand]) ELSE 0 END AS 'InvtCost_M',
						CASE WHEN T1.[U_ProductStatus] NOT IN ('D','D21','D22','D23','A','W','N','M') THEN SUM((CASE WHEN T1.LastPurDat = '2022-12-31' THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07)*T0.[OnHand]) ELSE 0 END AS 'InvtCost_O'
					FROM OITW T0
					LEFT JOIN OITM T1 ON T0.[ItemCode] = T1.[ItemCode]
					LEFT JOIN OWHS T2 ON T0.[WhsCode] = T2.[WhsCode]
					LEFT JOIN KBI_DB2022.dbo.OITM T3 ON T0.ItemCode = T3.ItemCode
					WHERE T0.[WhsCode] IN (".$resultWSG['WhseCode'].")
					GROUP BY T0.[WhsCode],T2.[WhsName],T1.[U_ProductStatus]
				) A1 GROUP BY A1.[WhsCode], A1.[WhsName] ORDER BY A1.[WhsCode]";
		// echo $sql;
		$sqlQRY = SAPSelect($sql);
		$Tbody = "";
		$SumD = 0; $SumA = 0; $SumW = 0; $SumN = 0; $SumM = 0; $SumO = 0; 
		$SumAll = 0;
		while ($result = odbc_fetch_array($sqlQRY)) {
			$WhseTotal = $result['InvtCost_D']+$result['InvtCost_A']+$result['InvtCost_W']+$result['InvtCost_N']+$result['InvtCost_M']+$result['InvtCost_O'];
			$SumD = $SumD+$result['InvtCost_D'];
			$SumA = $SumA+$result['InvtCost_A'];
			$SumW = $SumW+$result['InvtCost_W'];
			$SumN = $SumN+$result['InvtCost_N'];
			$SumM = $SumM+$result['InvtCost_M'];
			$SumO = $SumO+$result['InvtCost_O'];
			$SumAll = $SumAll+$WhseTotal;
			$Tbody .= "<tr>";
			if($WhseTotal != 0) {
				if($result['WhsCode'] == "KSY" || $result['WhsCode'] == "KSM" || $result['WhsCode'] == "KB3" || $result['WhsCode'] == "KBM" || $result['WhsCode'] == "AGT" || $result['WhsCode'] == "JSI" || $result['WhsCode'] == "KN" || $result['WhsCode'] == "KS" || $result['WhsCode'] == "TC" || $result['WhsCode'] == "PU" || $result['WhsCode'] == "VRK" || $result['WhsCode'] == "YEE" || $result['WhsCode'] == "PLA" || $result['WhsCode'] == "SY" || $result['WhsCode'] == "KB4") {
					$Tbody .= "<td><a href='javascript:void(0);' class='WSG-ws' data-ws='".$result['WhsCode']."'>".$result['WhsCode']." - ".conutf8($result['WhsName'])."</a></td>";
				}else{
					$Tbody .= "<td><a href='javascript:void(0);' class='WSG-ws' data-ws='".$result['WhsCode']."'>".$result['WhsCode']." - ".conutf8($result['WhsName'])." ( ใช้เวลาดึง 1-4 นาที <i class='fas fa-exclamation'></i> )</a></td>";
				}
			}else{ $Tbody .= "<td class='text-secondary'>".$result['WhsCode']." - ".conutf8($result['WhsName'])."</td>"; }

			if($result['InvtCost_D'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['InvtCost_D'],2)."<br><small class='text-secondary'>(".number_format(($result['InvtCost_D']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['InvtCost_D'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			if($result['InvtCost_A'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['InvtCost_A'],2)."<br><small class='text-secondary'>(".number_format(($result['InvtCost_A']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['InvtCost_A'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			if($result['InvtCost_W'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['InvtCost_W'],2)."<br><small class='text-secondary'>(".number_format(($result['InvtCost_W']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['InvtCost_W'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			if($result['InvtCost_N'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['InvtCost_N'],2)."<br><small class='text-secondary'>(".number_format(($result['InvtCost_N']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['InvtCost_N'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			if($result['InvtCost_M'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['InvtCost_M'],2)."<br><small class='text-secondary'>(".number_format(($result['InvtCost_M']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['InvtCost_M'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }
						
			if($result['InvtCost_O'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['InvtCost_O'],2)."<br><small class='text-secondary'>(".number_format(($result['InvtCost_O']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['InvtCost_O'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			$Tbody .= 	"<td class='fw-bolder text-right'>".number_format($WhseTotal,2)."</td>".
					"</tr>";
		}
		if($SumAll != 0) {
			$Tfoot ="<tr>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>รวมทั้งหมด</td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumD,2)."<br><small class='text-secondary'>(".number_format(($SumD/$SumAll)*100,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumA,2)."<br><small class='text-secondary'>(".number_format(($SumA/$SumAll)*100,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumW,2)."<br><small class='text-secondary'>(".number_format(($SumW/$SumAll)*100,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumN,2)."<br><small class='text-secondary'>(".number_format(($SumN/$SumAll)*100,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumM,2)."<br><small class='text-secondary'>(".number_format(($SumM/$SumAll)*100,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumO,2)."<br><small class='text-secondary'>(".number_format(($SumO/$SumAll)*100,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumAll,2)."</td>".
					"</tr>";
		}else{
			$Tfoot ="<tr>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>รวมทั้งหมด</td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format(0,2)."<br><small class='text-secondary'>(".number_format(0,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format(0,2)."<br><small class='text-secondary'>(".number_format(0,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format(0,2)."<br><small class='text-secondary'>(".number_format(0,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format(0,2)."<br><small class='text-secondary'>(".number_format(0,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format(0,2)."<br><small class='text-secondary'>(".number_format(0,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format(0,2)."<br><small class='text-secondary'>(".number_format(0,2)."%)</small></td>".
						"<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format(0,2)."</td>".
					"</tr>";
		}
		$arrCol['Tbody'] = $Tbody;
		$arrCol['Tfoot'] = $Tfoot;
	}
	if ($_GET['a'] == 'WSG_aging') {
		$WSG = $_POST['WSG'];
		$sqlWSG = "SELECT T0.WhseCode FROM whsemd T0 WHERE T0.WhseSubGrpCode = '".$WSG."'";
		$resultWSG = MySQLSelect($sqlWSG);
		$sql = "SELECT A2.[WhsCode], A2.[WhsName],
					SUM(A2.[AG3M]) AS 'AG3M',SUM(A2.[AG6M]) AS 'AG6M',SUM(A2.[AG12M]) AS 'AG12M',SUM(A2.[AG24M]) AS 'AG24M',SUM(A2.[AG99M]) AS 'AG99M'
				FROM (
					SELECT A1.[WhsCode], A1.[WhsName],
						CASE WHEN A1.[AgingM] BETWEEN 0 AND 3 THEN SUM(A1.[LastPurPrc]) ELSE 0 END AS 'AG3M',
						CASE WHEN A1.[AgingM] BETWEEN 4 AND 6 THEN SUM(A1.[LastPurPrc]) ELSE 0 END AS 'AG6M',
						CASE WHEN A1.[AgingM] BETWEEN 7 AND 12 THEN SUM(A1.[LastPurPrc]) ELSE 0 END AS 'AG12M',
						CASE WHEN A1.[AgingM] BETWEEN 13 AND 24 THEN SUM(A1.[LastPurPrc]) ELSE 0 END AS 'AG24M',
						CASE WHEN A1.[AgingM] > 24 THEN SUM(A1.[LastPurPrc]) ELSE 0 END AS 'AG99M'
					FROM (
						SELECT
							T0.[WhsCode], T2.[WhsName],
							CASE
								WHEN (T0.[WhsCode] = 'KSY' OR T0.[WhsCode] = 'KSM' OR T0.[WhsCode] = 'KB3' OR T0.[WhsCode] = 'KBM' OR T0.[WhsCode] = 'AGT' OR T0.[WhsCode] = 'JSI' OR T0.[WhsCode] = 'KN' OR T0.[WhsCode] = 'KS' OR T0.[WhsCode] = 'TC' OR T0.[WhsCode] = 'PU' OR T0.[WhsCode] = 'VRK' OR T0.[WhsCode] = 'YEE')
									THEN ISNULL((DATEDIFF(day,CASE WHEN T1.LastPurDat = '2022-12-31' THEN ISNULL(T3.LastPurDat,T1.LastPurDat) ELSE T1.LastPurDat END,GETDATE()))/30,999)
								ELSE ISNULL((DATEDIFF(day,
									CASE WHEN T1.LastPurDat = '2022-12-31' THEN
										ISNULL(
											(SELECT TOP 1 U1.DocDate FROM KBI_DB2022.dbo.OINM U1 WHERE U1.ItemCode = T0.[ItemCode] AND U1.Warehouse = T0.[WhsCode] AND U1.InQty != 0 ORDER BY U1.DocDate DESC),
											(SELECT TOP 1 U1.DocDate FROM OINM U1 WHERE U1.ItemCode = T0.[ItemCode] AND U1.Warehouse = T0.[WhsCode] AND U1.InQty != 0 ORDER BY U1.DocDate DESC)
										)
									ELSE (SELECT TOP 1 U1.DocDate FROM OINM U1 WHERE U1.ItemCode = T0.[ItemCode] AND U1.Warehouse = T0.[WhsCode] AND U1.InQty != 0 ORDER BY U1.DocDate DESC) END
								,GETDATE()))/30,999) END AS 'AgingM',
							(CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T3.LastPurPrc,T1.LastPurPrc) ELSE T1.LastPurPrc END * 1.07) * T0.[OnHand] AS 'LastPurPrc'
						FROM OITW T0
						LEFT JOIN OITM T1 ON T0.[ItemCode] = T1.[ItemCode]
						LEFT JOIN OWHS T2 ON T0.[WhsCode] = T2.[WhsCode]
						LEFT JOIN KBI_DB2022.dbo.OITM T3 ON T0.[ItemCode] = T3.[ItemCode]
						WHERE T0.[WhsCode] IN (".$resultWSG['WhseCode'].")
					) A1
					GROUP BY A1.[WhsCode], A1.[WhsName], A1.[AgingM]
				) A2
				GROUP BY A2.[WhsCode], A2.[WhsName]
				ORDER BY A2.[WhsCode]";
		$sqlQRY = SAPSelect($sql);
		$Tbody = "";
		$Sum3M = 0; $Sum6M = 0; $Sum12M = 0; $Sum24M = 0; $Sum99M = 0;
		$SumAll = 0;
		while ($result = odbc_fetch_array($sqlQRY)) {
			$WhseTotal = $result['AG3M']+$result['AG6M']+$result['AG12M']+$result['AG24M']+$result['AG99M'];
			$Sum3M = $Sum3M+$result['AG3M'];
			$Sum6M = $Sum6M+$result['AG6M'];
			$Sum12M = $Sum12M+$result['AG12M'];
			$Sum24M = $Sum24M+$result['AG24M'];
			$Sum99M = $Sum99M+$result['AG99M'];
			$SumAll = $SumAll+$WhseTotal;
			$Tbody .= "<tr>";
			if($WhseTotal != 0) {
				$Tbody .= "<td><a href='javascript:void(0);' class='WSG-ws' data-ws='".$result['WhsCode']."'>".$result['WhsCode']." - ".conutf8($result['WhsName'])."</a></td>";
			}else{ $Tbody .= "<td class='text-secondary'>".$result['WhsCode']." - ".conutf8($result['WhsName'])."</td>"; }

			if($result['AG3M'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['AG3M'],2)."<br><small class='text-secondary'>(".number_format(($result['AG3M']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['AG3M'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			if($result['AG6M'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['AG6M'],2)."<br><small class='text-secondary'>(".number_format(($result['AG6M']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['AG6M'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			if($result['AG12M'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['AG12M'],2)."<br><small class='text-secondary'>(".number_format(($result['AG12M']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['AG12M'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			if($result['AG24M'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['AG24M'],2)."<br><small class='text-secondary'>(".number_format(($result['AG24M']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['AG24M'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }

			if($result['AG99M'] > 0) {
				$Tbody .= "<td class='text-right'>".number_format($result['AG99M'],2)."<br><small class='text-secondary'>(".number_format(($result['AG99M']/$WhseTotal)*100,2)."%)</small></td>";
			}else{ $Tbody .= "<td class='text-right'>".number_format($result['AG99M'],2)."<br><small class='text-secondary'>(".number_format("0",2)."%)</small></td>"; }
						
			$Tbody .= 	"<td class='fw-bolder text-right'>".number_format($WhseTotal,2)."</td>".
					"</tr>";
		}
		$Tfoot ="<tr></tr>
					<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>รวมทั้งหมด</td>
					<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($Sum3M,2)."<br><small class='text-secondary'>(".number_format(($Sum3M/$SumAll)*100,2)."%)</small></td>
					<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($Sum6M,2)."<br><small class='text-secondary'>(".number_format(($Sum6M/$SumAll)*100,2)."%)</small></td>
					<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($Sum12M,2)."<br><small class='text-secondary'>(".number_format(($Sum12M/$SumAll)*100,2)."%)</small></td>
					<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($Sum24M,2)."<br><small class='text-secondary'>(".number_format(($Sum24M/$SumAll)*100,2)."%)</small></td>
					<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($Sum99M,2)."<br><small class='text-secondary'>(".number_format(($Sum99M/$SumAll)*100,2)."%)</small></td>
					<td class='fw-bolder text-right' style='background-color: rgba(0, 0, 0, 0.04);'>".number_format($SumAll,2)."</td>
				</tr>";
		$arrCol['Tbody'] = $Tbody;
		$arrCol['Tfoot'] = $Tfoot;
	}
	if ($_GET['a'] == 'WSGws') {
		$WSGws = $_POST['WSGws'];
		$sqlHeadName = "SELECT T0.[WhsCode],T0.[WhsName] FROM OWHS T0 WHERE T0.[WhsCode] = '".$WSGws."'";
		$sqlQRYHeadName = SAPSelect($sqlHeadName);
		$resultHeadName = odbc_fetch_array($sqlQRYHeadName);
		$sql = "SELECT T0.[ItemCode], T1.[ItemName], T1.[U_ProductStatus],";

		$WhsChk = array("KSY","KSM","KB3","KBM","AGT","JSI","KN","KS","TC","PU","VRK","YEE","PLA","SY","KB4");
		if(in_array($WSGws, $WhsChk, TRUE)) {
			$sql .= "ISNULL(CASE WHEN T1.LastPurDat = '2022-12-31' THEN ISNULL(T2.LastPurDat,T1.LastPurDat) ELSE T1.LastPurDat END,'2014-01-01') AS 'LastImpDate',";
		} else {
			$sql .= "
				ISNULL(
					CASE
						WHEN T1.LastPurDat = '2022-12-31' THEN 
							ISNULL(
								(SELECT TOP 1 U1.DocDate FROM KBI_DB2022.dbo.OINM U1 WHERE U1.ItemCode = T0.ItemCode AND U1.Warehouse = T0.WhsCode AND U1.InQty != 0 ORDER BY U1.DocDate DESC),
								(SELECT TOP 1 U1.DocDate FROM OINM U1 WHERE U1.ItemCode = T0.ItemCode AND U1.Warehouse = T0.WhsCode AND U1.InQty != 0 ORDER BY U1.DocDate DESC)
							)
						ELSE (SELECT TOP 1 U1.DocDate FROM OINM U1 WHERE U1.ItemCode = T0.ItemCode AND U1.Warehouse = T0.WhsCode AND U1.InQty != 0 ORDER BY U1.DocDate DESC) END
				,'2014-01-01'
				) AS 'LastImpDate',";
		}
		// if($WSGws == "KSY" || $WSGws == "KSM" || $WSGws == "KB3" || $WSGws == "KBM" || $WSGws == "AGT" || $WSGws == "JSI" || $WSGws == "KN" || $WSGws == "KS" || $WSGws == "TC" || $WSGws == "PU" || $WSGws == "VRK" || $WSGws == "YEE" || $WSGws == "PLA" || $WSGws == "SY" || $WSGws == "KB4") {
		// 	$sql .= "ISNULL(T1.[LastPurDat],'2014-01-01') AS 'LastImpDate',";
		// } else {
		// 	$sql .= "ISNULL((SELECT TOP 1 U1.DocDate FROM OINM U1 WHERE U1.ItemCode = T0.[ItemCode] AND U1.Warehouse = T0.[WhsCode] AND U1.InQty != 0 ORDER BY U1.DocDate DESC),'2014-01-01') AS 'LastImpDate',";
		// }
		$sql .= 
			"T1.[InvntryUom], T0.[OnHand], T0.[IsCommited], T0.[OnOrder], (CASE WHEN T1.LastPurDat = '2022-12-31' OR T1.LastPurDat IS NULL THEN ISNULL(T2.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END * 1.07)*T0.[OnHand] AS 'StockValue'
		FROM OITW T0
		LEFT JOIN OITM T1 ON T0.[ItemCode] = T1.[ItemCode]
		LEFT JOIN KBI_DB2022.dbo.OITM T2 ON T0.[ItemCode] = T2.[ItemCode]
		WHERE T0.[WhsCode] = '".$WSGws."'";
		$sql .= " AND (T0.[OnHand] != 0 OR T0.[IsCommited] != 0 OR T0.[OnOrder] != 0) ";

		// if($WSGws != "KSY" || $WSGws != "KSM" || $WSGws != "KB3" || $WSGws != "KBM" || $WSGws != "AGT" || $WSGws != "JSI" || $WSGws != "KN" || $WSGws != "KS" || $WSGws != "TC" || $WSGws != "PU" || $WSGws != "VRK" || $WSGws != "YEE" || $WSGws != "PLA" || $WSGws != "SY" || $WSGws != "KB4") {
		// 	$sql .= " AND (T0.[OnHand] != 0 OR T0.[IsCommited] != 0 OR T0.[OnOrder] != 0) ";
		// }

		// echo $sql;
		$sqlQRY = SAPSelect($sql);
		$i = 0;
		$no = ""; $ItemName = ""; $Status = ""; $Invntry = ""; $OnHand = ""; $IsCommited = ""; $OnOrder = ""; $StockValue = ""; $LastImpDate = ""; $Aging = "";
		$StockValueSum = 0;
		while ($result = odbc_fetch_array($sqlQRY)) {
			$i++;
			$no .= $i."|";
			$ItemName .= $result['ItemCode']." - ".conutf8($result['ItemName'])."|";
			$Status .= $result['U_ProductStatus']."|";
			$Invntry .= conutf8($result['InvntryUom'])."|";
			if($result['OnHand'] > 0) { $OnHand .= number_format($result['OnHand'],0)."|"; } else { $OnHand .= "-"."|"; }
			if($result['IsCommited'] > 0) { $IsCommited .= number_format($result['IsCommited'],0)."|"; } else { $IsCommited .= "-"."|"; }
			if($result['OnOrder'] > 0) { $OnOrder .= number_format($result['OnOrder'],0)."|"; } else { $OnOrder .= "-"."|"; }
			$StockValue .= number_format($result['StockValue'],2)."|";
			if($result['LastImpDate'] != "") {
				$LastImpDate .= date_format(date_create($result['LastImpDate']),"d/m/Y")."|";
				$Diff = date_diff(date_create($result['LastImpDate']),date_create(date("Y-m-d")));
				$ResultAging = number_format(($Diff->format("%a")/30),0);
			} else {
				$LastImpDate .= "-"."|";
				$ResultAging = "";
			}
			if($ResultAging > 24) {
				$Aging .= "<td class='text-right text-primary'>".$ResultAging."</td>"."|";
			} elseif($ResultAging > 12) {
				$Aging .= "<td class='text-right' style='color: #fd7e14'>".$ResultAging."</td>"."|";
			} elseif($ResultAging > 6) {
				$Aging .= "<td class='text-right text-warning'>".$ResultAging."</td>"."|";
			} elseif($ResultAging != "") {
				$Aging .= "<td class='text-right text-success'>".$ResultAging."</td>"."|";
			} else {
				$Aging .= "<td class='text-right text-primary'>999</td>"."|";
			}
			$StockValueSum = $StockValueSum+$result['StockValue'];
		}
		$arrCol['HeadName'] = $resultHeadName['WhsCode']." - ".conutf8($resultHeadName['WhsName']);
		$arrCol['no'] = substr($no,0,-1);
		$arrCol['ItemName'] = substr($ItemName,0,-1);
		$arrCol['Status'] = substr($Status,0,-1);
		$arrCol['Invntry'] = substr($Invntry,0,-1);
		$arrCol['OnHand'] = substr($OnHand,0,-1);
		$arrCol['IsCommited'] = substr($IsCommited,0,-1);
		$arrCol['OnOrder'] = substr($OnOrder,0,-1);
		$arrCol['StockValue'] = substr($StockValue,0,-1);
		$arrCol['LastImpDate'] = substr($LastImpDate,0,-1);
		$arrCol['Aging'] = substr($Aging,0,-1);
		$arrCol['StockValueSum'] = number_format($StockValueSum,2);

		$arrCol['Row'] = $i;
	}
// END รายงานคลังสินค้า

// รายงานการเก็บเงิน
	if ($_GET['a'] == 'CollectingMoney') {
		$ThisMonth = date("m");
		$ThisYear  = date("Y");
		$PrevYear  = $ThisYear-1;
		switch($ThisMonth) {
			case "1": $m = 12; $y1 = $PrevYear; $y2 = $PrevYear; break;
			case "2": $m = 1;  $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "3": $m = 2;  $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "4": $m = 3;  $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "5": $m = 4;  $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "6": $m = 5;  $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "7": $m = 6;  $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "8": $m = 7;  $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "9": $m = 8;  $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "10": $m = 9; $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "11": $m = 10; $y1 = $ThisYear; $y2 = $ThisYear; break;
			case "12": $m = 11; $y1 = $ThisYear; $y2 = $ThisYear; break;
		}
		// ------------------------------------------------------------------------ ALL --------------------------------------------------------------------------
		if ($_POST['Team'] == 'all') {
			$sql = "SELECT
						A0.[U_Dim1], A0.[Group], SUM(A0.[Amount]) AS 'Amount'
					FROM (
						SELECT 
							T2.[U_Dim1], 
							CASE
								WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 30 THEN 'B30D'
								WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) >= 31 AND CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 60 THEN 'B60D'
								WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) >= 61 AND CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 90 THEN 'B90D'
							ELSE 'A90D' END AS 'Group',
							SUM((T0.Doctotal-T0.PaidToDate)) AS 'Amount'
						FROM OINV T0 
						LEFT JOIN NNM1 T1 On T0.Series = T1.Series 
						LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
						WHERE ((month(T0.DocDueDate) <= '".$m."' AND year(T0.DocDueDate)= '".$y1."') OR  year(T0.DocDueDate) < '".$y2."') 
								and T0.DocStatus ='O' 
								and (T1.SeriesName LIKE 'IV%' or T1.SeriesName Like 'HA%' OR T1.SeriesName IS NULL)
								and (T2.[U_Dim1] IN ('MT1','MT2','TT1','TT2','OUL')) and (T0.[SlpCode] NOT IN (23,24,158,290)) AND T0.CANCELED = 'N'
						GROUP BY T2.[U_Dim1], T0.[DocDueDate]
						UNION ALL
						SELECT 
							T2.[U_Dim1], 
							CASE
								WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 30 THEN 'B30D'
								WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) >= 31 AND CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 60 THEN 'B60D'
								WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) >= 61 AND CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 90 THEN 'B90D'
							ELSE 'A90D' END AS 'Group',
							SUM(-(T0.Doctotal-T0.PaidToDate)) AS 'Amount' 
						FROM ORIN T0 
						LEFT JOIN NNM1 T1 On T0.Series = T1.Series 
						LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode                
						WHERE ((month(T0.DocDueDate) <= '".$m."' AND year(T0.DocDueDate)= '".$y1."') OR  year(T0.DocDueDate) < '".$y2."') 
						and T0.DocStatus ='O' 
						and (T1.SeriesName LIKE 'SR%' or T1.SeriesName Like 'S1%' OR T1.SeriesName IS NULL)
						and (T2.[U_Dim1] IN ('MT1','MT2','TT1','TT2','OUL','ONL','EI1')) and (T0.[SlpCode] NOT IN (23,24,158,290)) AND T0.CANCELED = 'N'
						GROUP BY T2.[U_Dim1], T0.[DocDueDate]
					) A0 GROUP BY A0.[U_Dim1], A0.[Group]
					ORDER BY
					CASE
						WHEN A0.[U_Dim1] = 'MT1' THEN 1
						WHEN A0.[U_Dim1] = 'MT2' THEN 2
						WHEN A0.[U_Dim1] = 'TT1' THEN 3
						WHEN A0.[U_Dim1] = 'TT2' THEN 4
						WHEN A0.[U_Dim1] = 'OUL' THEN 5
						WHEN A0.[U_Dim1] = 'ONL' THEN 6
						WHEN A0.[U_Dim1] = 'EI1' THEN 7
						ELSE 8 END,
					CASE
						WHEN A0.[Group] = 'B30D' THEN 1
						WHEN A0.[Group] = 'B60D' THEN 2
						WHEN A0.[Group] = 'B90D' THEN 3
						ELSE 4 END";
			$sqlQRY = SAPSelect($sql);
			$Tbody = "";
			$TeamName = "";
			$TeamID = 0;
			for ($i = 1; $i <= 15; $i++) {
				${"TeamName_".$i."_B30D"} = 0;
				${"TeamName_".$i."_B60D"} = 0;
				${"TeamName_".$i."_B90D"} = 0;
				${"TeamName_".$i."_A90D"} = 0;
			}

			while ($result = odbc_fetch_array($sqlQRY)) {
				if ($TeamName != $result['U_Dim1']) {
					$TeamID++;
					$TeamName = $result['U_Dim1'];
					${"TeamName_".$TeamID} = TeamName_Th($result['U_Dim1']);
					${"TeamName_".$TeamID."_".$result['Group']} = ${"TeamName_".$TeamID."_".$result['Group']}+$result['Amount'];
				}else{
					${"TeamName_".$TeamID."_".$result['Group']} = ${"TeamName_".$TeamID."_".$result['Group']}+$result['Amount'];
				}
			}

			for ($i = 1; $i <= $TeamID; $i++) {
				$Tbody .="<tr class='text-right'>".
							"<th class='text-center text-primary fw-bolder' style='background-color: rgba(155, 0, 0, 0.14);'>".${"TeamName_".$i}."</th>".
							"<td class='text-start'>หนี้เกินกำหนดน้อยกว่า 30 วัน</td>".
							"<td>".number_format(${"TeamName_".$i."_B30D"},2)."</td>".
						"</tr>".
						"<tr class='text-right'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start text-primary'>หนี้เกินกำหนดน้อยกว่า 60 วัน</td>".
							"<td>".number_format(${"TeamName_".$i."_B60D"},2)."</td>".
						"</tr>".
						"<tr class='text-right'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start text-primary'>หนี้เกินกำหนดน้อยกว่า 90 วัน</td>".
							"<td>".number_format(${"TeamName_".$i."_B90D"},2)."</td>".
						"</tr>".
						"<tr class='text-right'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start text-primary'>หนี้เกินกำหนดมากกว่า 90 วัน</td>".
							"<td>".number_format(${"TeamName_".$i."_A90D"},2)."</td>".
						"</tr>".
						"<tr class='text-right fw-bolder' style='background-color: rgba(0, 0, 0, 0.04);'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start'>รวมยอดหนี้เกินกำหนดมากกว่า 30 วัน</td>".
							"<td>".number_format((${"TeamName_".$i."_B60D"}+${"TeamName_".$i."_B90D"}+${"TeamName_".$i."_A90D"}),2)."</td>".
						"</tr>".
						"<tr class='text-right text-primary fw-bolder' style='background-color: rgba(0, 0, 0, 0.10);'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start'>รวมยอดหนี้เกินกำหนดทั้งหมด</td>".
							"<td>".number_format(((${"TeamName_".$i."_B60D"}+${"TeamName_".$i."_B90D"}+${"TeamName_".$i."_A90D"})+(${"TeamName_".$i."_B30D"})),2)."</td>".
						"</tr>";

				if($i != $TeamID) {
					$Tbody .= "<tr>
							<td> </td>
							<td> </td>
							<td> </td>
						</tr>";
				}
			}
			$arrCol['Team'] = $_POST['Team'];
			$arrCol['Month'] = FullMonth($m);
			$arrCol['Year'] = $y1;
			$arrCol['Tbody'] = $Tbody;
		}
		// ------------------------------------------------------------------------ Team -------------------------------------------------------------------------
		if ($_POST['Team'] != 'all') {
			$sql = "SELECT
						A0.[U_Dim1], A0.[Memo], A0.[Group], SUM(A0.[Amount]) AS 'Amount'
					FROM (
					SELECT 
						T2.[U_Dim1],T2.[Memo], 
						CASE
							WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 30 THEN 'B30D'
							WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) >= 31 AND CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 60 THEN 'B60D'
							WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) >= 61 AND CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 90 THEN 'B90D'
						ELSE 'A90D' END AS 'Group',
						SUM((T0.Doctotal-T0.PaidToDate)) AS 'Amount'
					FROM OINV T0 
					LEFT JOIN NNM1 T1 On T0.Series = T1.Series 
					LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
					WHERE ((month(T0.DocDueDate) <= '".$m."' AND year(T0.DocDueDate)= '".$y1."') OR  year(T0.DocDueDate) < '".$y2."')
					and T0.DocStatus ='O' 
					and (T1.SeriesName LIKE 'IV%' or T1.SeriesName Like 'HA%' OR T1.SeriesName IS NULL)
					and (T2.[U_Dim1] IN ('MT1','MT2','TT1','TT2','OUL','ONL','EI1','EXP')) and (T0.[SlpCode] NOT IN (23,24,158,290)) AND T0.CANCELED = 'N'
					GROUP BY T2.[U_Dim1], T2.[Memo], T0.[DocDueDate]
					UNION ALL
					SELECT 
						T2.[U_Dim1],T2.[Memo], 
						CASE
							WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 30 THEN 'B30D'
							WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) >= 31 AND CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 60 THEN 'B60D'
							WHEN CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) >= 61 AND CAST((DATEDIFF(\"day\",T0.[DocDueDate],GETDATE()))-30 AS INT) <= 90 THEN 'B90D'
						ELSE 'A90D' END AS 'Group',
						SUM(-(T0.Doctotal-T0.PaidToDate)) AS 'Amount' 
					FROM ORIN T0 
					LEFT JOIN NNM1 T1 On T0.Series = T1.Series 
					LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode                
					WHERE ((month(T0.DocDueDate) <= '".$m."' AND year(T0.DocDueDate)= '".$y1."') OR  year(T0.DocDueDate) < '".$y2."') 
					and T0.DocStatus ='O' 
					and (T1.SeriesName LIKE 'SR%' or T1.SeriesName Like 'S1%' OR T1.SeriesName IS NULL)
					and (T2.[U_Dim1] IN ('MT1','MT2','TT1','TT2','OUL','ONL','EI1','EXP')) and (T0.[SlpCode] NOT IN (23,24,158,290)) AND T0.CANCELED = 'N'
					GROUP BY T2.[U_Dim1], T2.[Memo], T0.[DocDueDate]
					) A0
					WHERE A0.[U_Dim1] = '".$_POST['Team']."'
					GROUP BY A0.[U_Dim1], A0.[Memo], A0.[Group]
					ORDER BY
					A0.[Memo],
					CASE
						WHEN A0.[Group] = 'B30D' THEN 1
						WHEN A0.[Group] = 'B60D' THEN 2
						WHEN A0.[Group] = 'B90D' THEN 3
						ELSE 4 END";
			$sqlQRY = SAPSelect($sql);
			$Tbody = "";
			$TeamName = "";
			$TeamID = 0;
			for ($i = 0; $i <= 15; $i++) {
				${"TeamName_".$i."_B30D"} = 0;
				${"TeamName_".$i."_B60D"} = 0;
				${"TeamName_".$i."_B90D"} = 0;
				${"TeamName_".$i."_A90D"} = 0;
			}

			while ($result = odbc_fetch_array($sqlQRY)) {
				if ($TeamName != $result['Memo']) {
					$TeamID++;
					$TeamName = $result['Memo'];
					$sqlukey = "SELECT CONCAT(uName,' ', ulastName) AS 'FullName' FROM users WHERE uKey = '".$TeamName."'";
					$resultukey = MySQLSelect($sqlukey);
					${"TeamName_".$TeamID} = $resultukey['FullName'];
					${"TeamName_".$TeamID."_".$result['Group']} = ${"TeamName_".$TeamID."_".$result['Group']}+$result['Amount'];
				}else{
					${"TeamName_".$TeamID."_".$result['Group']} = ${"TeamName_".$TeamID."_".$result['Group']}+$result['Amount'];
				}
			}

			for ($i = 1; $i <= $TeamID; $i++) {
				// $Tbody .="<tr class='text-primary fw-bolder' style='background-color: rgba(155, 0, 0, 0.14);'>".
					// 			"<th>".${"TeamName_".$i}."</th>".
					// 			"<th></th>".
					// 		"</tr>".
					// 		"<tr class='text-right'>".
					// 			"<td class='text-start'>หนี้เกินกำหนดน้อยกว่า 30 วัน</td>".
					// 			"<td>".number_format(${"TeamName_".$i."_B30D"},2)."</td>".
					// 		"</tr>".
					// 		"<tr class='text-right'>".
					// 			"<td class='text-start text-primary'>หนี้เกินกำหนดน้อยกว่า 60 วัน</td>".
					// 			"<td>".number_format(${"TeamName_".$i."_B60D"},2)."</td>".
					// 		"</tr>".
					// 		"<tr class='text-right'>".
					// 			"<td class='text-start text-primary'>หนี้เกินกำหนดน้อยกว่า 90 วัน</td>".
					// 			"<td>".number_format(${"TeamName_".$i."_B90D"},2)."</td>".
					// 		"</tr>".
					// 		"<tr class='text-right'>".
					// 			"<td class='text-start text-primary'>หนี้เกินกำหนดมากกว่า 90 วัน</td>".
					// 			"<td>".number_format(${"TeamName_".$i."_A90D"},2)."</td>".
					// 		"</tr>".
					// 		"<tr class='text-right text-primary fw-bolder' style='background-color: rgba(0, 0, 0, 0.04);'>".
					// 			"<td class='text-start'>รวมยอดหนี้เกินกำหนดมากกว่า 30 วัน</td>".
					// 			"<td>".number_format((${"TeamName_".$i."_B60D"}+${"TeamName_".$i."_B90D"}+${"TeamName_".$i."_A90D"}),2)."</td>".
					// 		"</tr>".
					// 		"<tr class='text-right fw-bolder' style='background-color: rgba(0, 0, 0, 0.04);'>".
					// 			"<td class='text-start'>รวมยอดหนี้เกินกำหนดทั้งหมด</td>".
					// 			"<td>".number_format(((${"TeamName_".$i."_B60D"}+${"TeamName_".$i."_B90D"}+${"TeamName_".$i."_A90D"})+(${"TeamName_".$i."_B30D"})),2)."</td>".
					// 		"</tr>";

				$Tbody .="<tr class='text-right'>".
							"<th class='text-center text-primary fw-bolder' style='background-color: rgba(155, 0, 0, 0.14);'>".${"TeamName_".$i}."</th>".
							"<td class='text-start'>หนี้เกินกำหนดน้อยกว่า 30 วัน</td>".
							"<td>".number_format(${"TeamName_".$i."_B30D"},2)."</td>".
						"</tr>".
						"<tr class='text-right'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start text-primary'>หนี้เกินกำหนดน้อยกว่า 60 วัน</td>".
							"<td>".number_format(${"TeamName_".$i."_B60D"},2)."</td>".
						"</tr>".
						"<tr class='text-right'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start text-primary'>หนี้เกินกำหนดน้อยกว่า 90 วัน</td>".
							"<td>".number_format(${"TeamName_".$i."_B90D"},2)."</td>".
						"</tr>".
						"<tr class='text-right'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start text-primary'>หนี้เกินกำหนดมากกว่า 90 วัน</td>".
							"<td>".number_format(${"TeamName_".$i."_A90D"},2)."</td>".
						"</tr>".
						"<tr class='text-right fw-bolder' style='background-color: rgba(0, 0, 0, 0.04);'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start'>รวมยอดหนี้เกินกำหนดมากกว่า 30 วัน</td>".
							"<td>".number_format((${"TeamName_".$i."_B60D"}+${"TeamName_".$i."_B90D"}+${"TeamName_".$i."_A90D"}),2)."</td>".
						"</tr>".
						"<tr class='text-right text-primary fw-bolder' style='background-color: rgba(0, 0, 0, 0.10);'>".
							"<th style='background-color: rgba(155, 0, 0, 0.14);'></th>".
							"<td class='text-start'>รวมยอดหนี้เกินกำหนดทั้งหมด</td>".
							"<td>".number_format(((${"TeamName_".$i."_B60D"}+${"TeamName_".$i."_B90D"}+${"TeamName_".$i."_A90D"})+(${"TeamName_".$i."_B30D"})),2)."</td>".
						"</tr>";

				if($i != $TeamID) {
					$Tbody .= "<tr>
							<td> </td>
							<td> </td>
							<td> </td>
						</tr>";
				}
			}
			$arrCol['Team'] = $_POST['Team'];
			$arrCol['TeamName'] = TeamName_Th($_POST['Team']);
			$arrCol['Month'] = FullMonth($m);
			$arrCol['Year'] = $y1;
			$arrCol['Tbody'] = $Tbody;
		}
	} 
// END รายงานการเก็บเงิน

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
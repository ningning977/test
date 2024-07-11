<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName']== NULL) {
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

if($_GET['p'] == "GetList") {
	$filt_year  = $_POST['y'];
	$filt_month = $_POST['m'];

	if($_SESSION['uClass'] == 29) {
		$GetArrWhr = "<";
	} else {
		$GetArrWhr = NULL;
	}

	$GetArr = 
		"SELECT
			GROUP_CONCAT(T0.DocEntry) AS 'arrDocEntry'
		FROM billpa T0
		WHERE
			(YEAR(T0.DocDate) = $filt_year AND MONTH(T0.DocDate) $GetArrWhr= $filt_month) AND 
			((T0.Whs_App = 1 AND T0.Acc_App = 1) OR (CURDATE() <= DATE_ADD(T0.Acc_Date,interval 2 day) AND T0.Acc_App = 1))";
	$GetArrRST = MySQLSelect($GetArr);
	if($GetArrRST['arrDocEntry'] != NULL) {
		$getSAPWhr = "AND T0.DocEntry NOT IN (".$GetArrRST['arrDocEntry'].")";
	} else {
		$getSAPWhr = NULL;
	}
	// echo $GetArr;
	
	$SAPSQL =
		"SELECT
			T0.DocEntry, T0.NumAtCard, (ISNULL(T1.BeginStr,'IV-')+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.DocDate,
			DATEDIFF(day, T0.DocDate, GETDATE()) AS 'DateDiff',
			T0.CardCode, T0.CardName, T0.DocTotal, T0.GroupNum, T2.PymntGroup, T3.SlpName,
			T0.OwnerCode, (T4.lastname) AS 'OwnerName', T0.Comments, T0.U_PONo
		FROM ODLN T0
		LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
		LEFT JOIN OCTG T2 ON T0.GroupNum = T2.GroupNum
		LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
		LEFT JOIN OHEM T4 ON T0.OwnerCode = T4.empID
		WHERE (YEAR(T0.DocDate) = $filt_year AND MONTH(T0.DocDate) = $filt_month) $getSAPWhr
		ORDER BY T1.BeginStr, T0.DocDate, T0.DocEntry";
	// echo $SAPSQL;
	if($filt_year <= 2022) {
		$SapROW = ChkRowSAP8($SAPSQL);
	} else {
		$SapROW = ChkRowSAP($SAPSQL);
	}
	
	if($SapROW == 0) {
		$arrCol['Rows'] = 0;
	} else {
		if($filt_year <= 2022) {
			$SAPQRY = conSAP8($SAPSQL);
		} else {
			$SAPQRY = SAPSelect($SAPSQL);
		}
		
		$arrCol['Rows'] = $SapROW;
		$i = 0;
		while($SAPRST = odbc_fetch_array($SAPQRY)) {
			$DocEntry  = $SAPRST['DocEntry'];

			$arrCol['BD_'.$i]['DocEntry']   = $DocEntry;
			$arrCol['BD_'.$i]['DateDiff']   = $SAPRST['DateDiff'];
			$arrCol['BD_'.$i]['GroupNum']   = $SAPRST['GroupNum'];
			$arrCol['BD_'.$i]['NumAtCard']  = $SAPRST['DocNum'];
			$arrCol['BD_'.$i]['DocNum']     = $SAPRST['DocNum'];
			$arrCol['BD_'.$i]['DocDate']    = date("d/m/Y",strtotime($SAPRST['DocDate']));
			$arrCol['BD_'.$i]['CardCode']   = conutf8($SAPRST['CardCode']." | ".$SAPRST['CardName']);
			$arrCol['BD_'.$i]['PymntGroup'] = conutf8($SAPRST['PymntGroup']);
			$arrCol['BD_'.$i]['SlpName']    = conutf8($SAPRST['SlpName']);
			$arrCol['BD_'.$i]['Comments']   = conutf8($SAPRST['Comments']);
			$arrCol['BD_'.$i]['OwnerName']  = "คุณ".conutf8($SAPRST['OwnerName']);
			$arrCol['BD_'.$i]['DocTotal']   = number_format($SAPRST['DocTotal'],2);

			$arrCol['BD_'.$i]['LoadDate']   = NULL;
			$arrCol['BD_'.$i]['LogiDate']   = NULL;
			$arrCol['BD_'.$i]['LogiName']   = NULL;
			$arrCol['BD_'.$i]['WhseDate']   = NULL;
			$arrCol['BD_'.$i]['AcntDate']   = NULL;
			$arrCol['BD_'.$i]['ChkLoad']    = NULL;
			$arrCol['BD_'.$i]['ChkLogi']    = NULL;
			$arrCol['BD_'.$i]['ChkWhse']    = NULL;
			$arrCol['BD_'.$i]['ChkAcnt']    = NULL;
			$arrCol['BD_'.$i]['RmkWhse']    = "";
			$arrCol['BD_'.$i]['RmkAcnt']    = "";

			$StatusSQL = "SELECT T0.*, T1.* FROM billpa T0 LEFT JOIN logistic T1 ON T1.logiID = T0.logi_ukey WHERE T0.DocEntry = $DocEntry";
			$StRow     = ChkRowDB($StatusSQL);
			if($StRow > 0) {
				$StatusRST = MySQLSelect($StatusSQL);
				if($StatusRST['Load_Date'] == "" || $StatusRST['Load_Date'] == NULL) {
					$LoadDate = NULL;
					$ChkLoad  = NULL;
				} else {
					$LoadDate = date("d/m/y",strtotime($StatusRST['Load_Date']));
					$ChkLoad  = 1;
				}

				if($StatusRST['logi_Date'] == "" || $StatusRST['logi_Date'] == NULL) {
					$LogiDate = NULL;
				} else {
					$LogiDate = date("d/m/y",strtotime($StatusRST['logi_Date']));
				}

				if($StatusRST['Whs_Date'] == "" || $StatusRST['Whs_Date'] == NULL) {
					$WhseDate = NULL;
				} else {
					$WhseDate = date("d/m/y",strtotime($StatusRST['Whs_Date']));
				}

				if($StatusRST['Acc_Date'] == "" || $StatusRST['Acc_Date'] == NULL) {
					$AcntDate = NULL;
				} else {
					$AcntDate = date("d/m/y",strtotime($StatusRST['Acc_Date']));
				}

				if($StatusRST['loginickname'] == "" || $StatusRST['loginickname'] == NULL) {
					$LogiName = $StatusRST['loginame'];
				} else {
					$LogiName = $StatusRST['loginame']." (".$StatusRST['loginickname'].")";
				}

				if($StatusRST['wsmark'] == "" || $StatusRST['wsmark'] == NULL) {
					$RmkWhse = "";
				} else {
					$RmkWhse = $StatusRST['wsmark'];
				}

				if($StatusRST['remark'] == "" || $StatusRST['remark'] == NULL) {
					$RmkAcnt = "";
				} else {
					$RmkAcnt = $StatusRST['remark'];
				}

				$arrCol['BD_'.$i]['LogiName']   = $LogiName;
				$arrCol['BD_'.$i]['LoadDate']   = $LoadDate;
				$arrCol['BD_'.$i]['LogiDate']   = $LogiDate;
				$arrCol['BD_'.$i]['WhseDate']   = $WhseDate;
				$arrCol['BD_'.$i]['AcntDate']   = $AcntDate;
				$arrCol['BD_'.$i]['ChkLoad']    = $ChkLoad;
				$arrCol['BD_'.$i]['ChkLogi']    = $StatusRST['logi_App'];
				$arrCol['BD_'.$i]['ChkWhse']    = $StatusRST['Whs_App'];
				$arrCol['BD_'.$i]['ChkAcnt']    = $StatusRST['Acc_App'];
				$arrCol['BD_'.$i]['RmkWhse']    = $RmkWhse;
				$arrCol['BD_'.$i]['RmkAcnt']    = $RmkAcnt;
			} else {
				$LoadSQL = "SELECT DISTINCT DATE(T0.OutTime) AS 'OutTime' FROM logi_detail T0 WHERE T0.BillEntry = $DocEntry AND T0.BillType = 'ODLN' ORDER BY T0.OutTime DESC LIMIT 1";
				$Rows    = ChkRowDB($LoadSQL);
				if($Rows > 0) {
					$LoadRST = MySQLSelect($LoadSQL);
					$arrCol['BD_'.$i]['ChkLoad']    = 1;
					$arrCol['BD_'.$i]['LoadDate']   = date("d/m/y",strtotime($LoadRST['OutTime']));
				}
			}
			$i++;
		}
	}
}

if($_GET['p'] == "Approve") {
	$SaveType   = $_POST['Type'];
	$DocEntry   = $_POST['DocEntry'];
	$Approve    = $_POST['App'];
	$UpdateUkey = $_SESSION['ukey'];

	$ChkSQL = "SELECT T0.DocEntry FROM billpa T0 WHERE T0.DocEntry = $DocEntry";
	$Rows   = ChkRowDB($ChkSQL);
	if($Rows == 0) {
		$LoadSQL = "SELECT DISTINCT DATE(T0.OutTime) AS 'OutTime' FROM logi_detail T0 WHERE T0.BillEntry = $DocEntry AND T0.BillType = 'ODLN' ORDER BY T0.OutTime DESC LIMIT 1";
		$LoadRST = MySQLSelect($LoadSQL);
		$LoadDate = $LoadRST['OutTime'];
		$DocDateSQL = "SELECT TOP 1 T0.DocDate FROM ODLN T0 WHERE T0.DocEntry = $DocEntry";
		$DocDateQRY = SAPSelect($DocDateSQL);
		$DocDateRST = odbc_fetch_array($DocDateQRY);
		$DocDate    = $DocDateRST['DocDate'];
		$InsertSQL = "INSERT INTO billpa SET DocEntry = $DocEntry, DocDate = '$DocDate', Load_Date = '$LoadDate'";
		// echo $InsertSQL;
		MySQLInsert($InsertSQL);
	}

	$UpdateSQL  = "UPDATE billpa SET ";
	switch($SaveType) {
		case "ChkLogi": $UpdateSQL .= "logi_App = $Approve, logi_ukey = '$UpdateUkey', logi_Date = NOW()"; break;
		case "ChkWhse": $UpdateSQL .= "Whs_App  = $Approve, Whs_ukey  = '$UpdateUkey', Whs_Date  = NOW()"; break;
		case "ChkAcnt": $UpdateSQL .= "Acc_App  = $Approve, Acc_ukey  = '$UpdateUkey', Acc_Date  = NOW()"; break;
	}
	$UpdateSQL .= " WHERE DocEntry = $DocEntry";
	echo $UpdateSQL;
	MySQLUpdate($UpdateSQL);
}

if($_GET['p'] == "Remark") {
	$SaveType  = $_POST['Type'];
	$DocEntry  = $_POST['DocEntry'];
	$Content   = $_POST['Content'];

	$ChkSQL = "SELECT T0.DocEntry FROM billpa T0 WHERE T0.DocEntry = $DocEntry";
	$Rows   = ChkRowDB($ChkSQL);
	if($Rows == 0) {
		$LoadSQL = "SELECT DISTINCT DATE(T0.OutTime) AS 'OutTime' FROM logi_detail T0 WHERE T0.BillEntry = $DocEntry AND T0.BillType = 'ODLN' ORDER BY T0.OutTime DESC LIMIT 1";
		$LoadRST = MySQLSelect($LoadSQL);
		$LoadDate = $LoadRST['OutTime'];
		$DocDateSQL = "SELECT TOP 1 T0.DocDate FROM ODLN T0 WHERE T0.DocEntry = $DocEntry";
		$DocDateQRY = SAPSelect($DocDateSQL);
		$DocDateRST = odbc_fetch_array($DocDateQRY);
		$DocDate    = $DocDateRST['DocDate'];
		$InsertSQL = "INSERT INTO billpa SET DocEntry = $DocEntry, DocDate = '$DocDate', Load_Date = '$LoadDate'";
		MySQLInsert($InsertSQL);
	}

	$UpdateSQL = "UPDATE billpa SET ";
	switch($SaveType) {
		case "RmkWhse": $UpdateSQL .= "wsmark = '$Content'"; break;
		case "RmkAcnt": $UpdateSQL .= "remark = '$Content'"; break;
	}
	$UpdateSQL .= " WHERE DocEntry = $DocEntry";
	// echo $UpdateSQL;
	MySQLUpdate($UpdateSQL);
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
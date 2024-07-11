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

if($_GET['a'] == 'SelectDate') {
	$StartDate = $_POST['StartDate'];
	$EndDate   = $_POST['EndDate'];
	$SQL = "SELECT T0.DocDate, (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocumentNo', (T0.CardCode+' '+T0.CardName) AS 'CustomerName', T4.InvoiceId,
				CASE WHEN T4.InvType = 13 THEN (SELECT TOP 1 ISNULL(P0.NumAtCard,(P1.BeginStr+CAST(P0.DocNum AS VARCHAR))) FROM OINV P0 LEFT JOIN NNM1 P1 ON P0.Series = P1.Series WHERE P0.DocEntry = T4.DocEntry)
					WHEN T4.InvType = 14 THEN (SELECT TOP 1 ISNULL(P0.NumAtCard,(P1.BeginStr+CAST(P0.DocNum AS VARCHAR))) FROM ORIN P0 LEFT JOIN NNM1 P1 ON P0.Series = P1.Series WHERE P0.DocEntry = T4.DocEntry)
					WHEN T4.InvType IN (24,30) THEN (SELECT TOP 1 CAST(P0.TransId AS VARCHAR) FROM OJDT P0 WHERE P0.TransId = T4.DocEntry)
				ELSE NULL END AS 'ReferenceNo',
				CASE WHEN T4.InvType = 13 THEN (SELECT TOP 1 P0.DocDate FROM OINV P0 WHERE P0.DocEntry = T4.DocEntry)
					WHEN T4.InvType = 14 THEN (SELECT TOP 1 P0.DocDate FROM ORIN P0 WHERE P0.DocEntry = T4.DocEntry)
					WHEN T4.InvType IN (24,30) THEN (SELECT TOP 1 P0.RefDate FROM OJDT P0 WHERE P0.TransId = T4.DocEntry)
				ELSE NULL END AS 'InvoiceDate',
				CASE WHEN T6.SlpCode = -1 THEN T0.Comments ELSE T6.SlpName END AS 'SlpName',
				CASE WHEN T4.InvType = 13 THEN T4.SumApplied
					WHEN T4.InvType = 14 THEN -T4.SumApplied
				ELSE T4.SumApplied END AS 'SumApplied',
				CASE WHEN T0.CashAcct = '4113-10' THEN 0 ELSE T0.CashSum END AS 'CashSum',
				CASE WHEN T0.TrsfrAcct = '4113-10' THEN 0 ELSE T0.TrsfrSum END AS 'TransferSum',
				CASE WHEN T0.CheckAcct = '4113-10' THEN 0 ELSE T0.CheckSum END AS 'CheckSum',
				CASE WHEN T0.CashAcct = '4113-10' THEN T0.CashSum
				ELSE CASE WHEN T0.TrsfrAcct = '4113-10' THEN T0.TrsfrSum 
					ELSE CASE WHEN T0.CheckAcct = '4113-10' THEN T0.CheckSum
						ELSE 0 END
					END 
				END AS 'Discount',
				T2.CheckNum, T2.DueDate, T3.BankName
			FROM ORCT T0
			LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
			LEFT JOIN RCT1 T2 ON T0.DocNum = T2.DocNum
			LEFT JOIN ODSC T3 ON T2.BankCode = T3.BankCode
			LEFT JOIN RCT2 T4 ON T0.DocNum = T4.DocNum
			LEFT JOIN OCRD T5 ON T0.CardCode = T5.CardCode
			LEFT JOIN OSLP T6 ON T5.SlpCode = T6.SlpCode
			WHERE T0.DocDate BETWEEN '$StartDate' AND '$EndDate' AND T1.BeginStr LIKE 'RE-%'
			ORDER BY (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)), T4.InvoiceId ASC";
	if(intval(substr($EndDate,0,4)) <= 2022) {
		$QRY = conSAP8($SQL);
	}else{
		$QRY = SAPSelect($SQL);
	}
	$r = 0;
	while($result = odbc_fetch_array($QRY)) {
		if($result['InvoiceId'] == "")   { $arrCol[$r]['InvoiceId'] = "";} else { $arrCol[$r]['InvoiceId'] = $result['InvoiceId']; }

		$arrCol[$r]['DocNo']    = $result['DocumentNo']; 
		$arrCol[$r]['DocDate']  = date('d/m/Y', strtotime($result['DocDate'])); 
		$arrCol[$r]['CusName']  = conutf8($result['CustomerName']); 
		$arrCol[$r]['SlpName']  = conutf8($result['SlpName']); 
		if($result['CashSum'] == 0)     { $arrCol[$r]['CashSum'] = "";  } else { $arrCol[$r]['CashSum']  = number_format($result['CashSum'],2); }
		if($result['TransferSum'] == 0) { $arrCol[$r]['TransSum'] = ""; } else { $arrCol[$r]['TransSum'] = number_format($result['TransferSum'],2); }
		if($result['CheckSum'] == 0)    { $arrCol[$r]['CheckSum'] = ""; } else { $arrCol[$r]['CheckSum'] = number_format($result['CheckSum'],2); }
		if($result['Discount'] == 0)    { $arrCol[$r]['Discount'] = ""; } else { $arrCol[$r]['Discount'] = number_format($result['Discount'],2); }
		if($result['CheckNum'] == "")   { $arrCol[$r]['CheckNum'] = ""; } else { $arrCol[$r]['CheckNum'] = $result['CheckNum'];}
		if($result['DueDate'] == "")    { $arrCol[$r]['DueDate'] = "";  } else { $arrCol[$r]['DueDate']  = date('d/m/Y', strtotime($result['DueDate'])); }
		if($result['BankName'] == "")   { $arrCol[$r]['BankName'] = ""; } else { $arrCol[$r]['BankName'] = conutf8($result['BankName']); }

		if($result['ReferenceNo'] == "") { $arrCol[$r]['ReferenceNo'] = ""; }else{ $arrCol[$r]['ReferenceNo'] = $result['ReferenceNo']; }
		$arrCol[$r]['InvoiceDate'] = date('d/m/Y', strtotime($result['InvoiceDate'])); 
		$arrCol[$r]['SumApplied']  = number_format($result['SumApplied'],2); 
		$r++;
	}
	$arrCol['Row'] = ($r-1);
}

// $arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
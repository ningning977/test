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

if($_GET['p'] == "GetEmpList") {
	switch($_SESSION['DeptCode']) {
		case "DP005":
		case "DP006":
		case "DP007":
		case "DP008":
			$SOWhr = " AND T2.DeptCode = '".$_SESSION['DeptCode']."'";
		break;
		default:
			$SOWhr = NULL;
		break;
	}
	$ERF_EmpSQL =
		"SELECT DISTINCT
			T0.Ukey,
			CASE
				WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo01' THEN 'โฮมโปร - ฝากขาย'
				WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo02' THEN 'ไทวัสดุ - ฝากขาย'
				WHEN T0.Ukey = '569ed0bfade926ca16c8fd42b15eNo03' THEN 'เมกาโฮม - ฝากขาย'
				WHEN T0.Ukey = 'a82726eeff10f11797ed9fde004e701a' THEN '60 | จีรศักดิ์ (ซ่อมหน้าร้าน)'
			ELSE CONCAT(T3.SaleEmpCode,' | ',T1.uName,' ',T1.uLastName,' (',T1.uNickName,')') END AS 'SlpName',
			IFNULL(T2.DeptCode,'DP007') AS 'DeptCode'
		FROM saletarget T0
		LEFT JOIN users T1 ON T0.ukey = T1.ukey
		LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
		LEFT JOIN OSLP T3 ON T0.Ukey = T3.Ukey
		WHERE T0.DocStatus != 'I' $SOWhr
		ORDER BY 
			CASE
				WHEN IFNULL(T2.DeptCode,'DP007') = 'DP006' THEN 1
				WHEN IFNULL(T2.DeptCode,'DP007') = 'DP007' THEN 2
				WHEN IFNULL(T2.DeptCode,'DP007') = 'DP005' THEN 3
				WHEN IFNULL(T2.DeptCode,'DP007') = 'DP008' THEN 4
			ELSE 99 END, T2.uClass, T0.Ukey";
	$ERF_EmpQRY = MySQLSelectX($ERF_EmpSQL);
	$r = 0;
	while($ERF_EmpRST = mysqli_fetch_array($ERF_EmpQRY)) {
		$arrCol[$r]['OptVal'] = $ERF_EmpRST['Ukey'];
		$arrCol[$r]['OptTxt'] = $ERF_EmpRST['SlpName'];
		$r++;
	}

	// $arrCol[$r]['OptVal'] = '24b6de0a16ece9b8a83d5e0afbc45473';
	// $arrCol[$r]['OptTxt'] = "พงษ์ศักดิ์ ขาวสุข (โอ๋)";
	// $r++;

	$SAP_EmpSQL = "SELECT T0.SlpCode, T0.SlpName FROM OSLP T0 WHERE T0.SlpName LIKE 'D%' AND T0.Active = 'Y' ORDER BY T0.SlpName";
	$SAP_EmpQRY = SAPSelect($SAP_EmpSQL);
	while($SAP_EmpRST = odbc_fetch_array($SAP_EmpQRY)) {
		$arrCol[$r]['OptVal'] = "DD-".conutf8($SAP_EmpRST['SlpCode']);
		$arrCol[$r]['OptTxt'] = str_replace("-"," | ",conutf8($SAP_EmpRST['SlpName']));
		$r++;
	}
	$arrCol['Rows'] = $r;
}

if ($_GET['p'] == 'Detail'){
	$sql1 = "SELECT (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS DocNum,T0.CardCode,T0.CardName,T0.SlpCode,T2.SlpName,T0.DocDate,T0.DocDueDate,T0.Comments,T0.U_PONo
			 FROM ODLN T0
		 		  LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
		 		  LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
			 WHERE DocEntry = ".$_POST['DocEntry'];
	$getHeader= SAPSelect($sql1);
	$DataHeader = odbc_fetch_array($getHeader);
	$arrCol['CardName'] = $DataHeader['CardCode']." ".conutf8($DataHeader['CardName']);
	$arrCol['DocNum'] = $DataHeader['DocNum'];
	$arrCol['SaleName'] = conutf8($DataHeader['SlpName']);
	$arrCol['Remark'] = conutf8($DataHeader['Comments']);
	$arrCol['DocDate'] = date("Y-m-d",strtotime($DataHeader['DocDate']));
	$arrCol['DocDuDate'] = date("Y-m-d",strtotime($DataHeader['DocDueDate']));


	

	$arrCol['Data'] = "";
}
if($_GET['p'] == "GetPAList") {
	$r = 0;
	$no = 1;

	// switch($_SESSION['DeptCode']) {
	// 	case "DP005": $SQLWhr = " AND T3.U_Dim1 IN ('TT2','DMN')"; break;
	// 	case "DP006": $SQLWhr = " AND T3.U_Dim1 IN ('MT1','EXP')"; break;
	// 	case "DP007": $SQLWhr = " AND T3.U_Dim1 IN ('MT2')"; break;
	// 	case "DP008": $SQLWhr = " AND T3.U_Dim1 IN ('TT1','OUL')"; break;
	// 	case "DP003": $SQLWhr = " AND T3.U_Dim1 IN ('KBI','ONL')"; break;
	// 	default: $SQLWhr = NULL; break;
	// }

	$filt_sale = $_POST['filt_sale'];
	$Prefix    = explode("-",$filt_sale);
	if($Prefix[0] == "DD") {
		$SQLWhr = " AND T0.SlpCode = '".$Prefix[1]."'";
	} else {
		$SQLWhr = " AND T3.Memo = '$filt_sale'";
	}


	$GetPASQL = "SELECT
	    T0.DocEntry,
		(T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.DocDate, T0.CardCode, T0.CardName, T0.ShipToCode,
		T1.ItemCode, T1.Dscription, T1.WhsCode, T1.UnitMsr, T1.Quantity, (T1.Quantity-T1.OpenQty) AS 'Returned', T1.OpenQty,
		T1.PriceAfVAT, (T1.PriceAfVAT*T1.OpenQty) AS 'LineTotal', T0.Comments, DATEDIFF(month, T0.DocDate, GETDATE()) AS 'DateDiff'
	FROM
	ODLN T0
	LEFT JOIN DLN1 T1 ON T0.DocEntry = T1.DocEntry
	LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
	LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
	WHERE T0.CANCELED = 'N' AND T0.DocStatus = 'O' AND T1.LineStatus = 'O' AND T2.BeginStr IN ('PA-','PC-','PD-') $SQLWhr
	ORDER BY T0.DocEntry, T1.VisOrder ASC";

	if(ChkRowSAP($GetPASQL) > 0) {
		$GetPAQRY = SAPSelect($GetPASQL);
		while($GetPARST = odbc_fetch_array($GetPAQRY)) {
			$arrCol[$r]['no']         = $no;
			$arrCol[$r]['DocEntry']    = $GetPARST['DocEntry'];
			$arrCol[$r]['DocNum']     = "<a href='javascript:void(0);' onclick='Detail(".$GetPARST['DocEntry'].")'>".$GetPARST['DocNum']."</a>";
			$arrCol[$r]['DocDate']    = date("d/m/Y",strtotime($GetPARST['DocDate']));
			$arrCol[$r]['CardCode']   = $GetPARST['CardCode'];
			$arrCol[$r]['CardName']   = conutf8($GetPARST['CardName']);
			$arrCol[$r]['Customer']   = $arrCol[$r]['CardCode']." | ".$arrCol[$r]['CardName'];
			$arrCol[$r]['ShipToCode'] = conutf8($GetPARST['ShipToCode']);
			$arrCol[$r]['ItemCode']   = $GetPARST['ItemCode'];
			$arrCol[$r]['Dscription'] = conutf8($GetPARST['Dscription']);
			$arrCol[$r]['ItemDetail'] = $arrCol[$r]['ItemCode']." | ".$arrCol[$r]['Dscription'];
			$arrCol[$r]['WhsCode']    = conutf8($GetPARST['WhsCode']);
			$arrCol[$r]['UnitMsr']    = conutf8($GetPARST['UnitMsr']);
			$arrCol[$r]['Quantity']   = $GetPARST['Quantity'];
			$arrCol[$r]['Returned']   = $GetPARST['Returned'];
			$arrCol[$r]['OpenQty']    = $GetPARST['OpenQty'];
			$arrCol[$r]['PriceAfVAT'] = $GetPARST['PriceAfVAT'];
			$arrCol[$r]['LineTotal']  = $GetPARST['LineTotal'];
			$arrCol[$r]['Comments']   = conutf8($GetPARST['Comments']);
			$arrCol[$r]['DateDiff']   = $GetPARST['DateDiff'];
			$no++;
			$r++;
		}
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
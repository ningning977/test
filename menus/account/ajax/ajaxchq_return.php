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

if($_GET['p'] == "GetCustomer") {
	$GetSQL = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 ORDER BY T0.CardCode ASC";
	$GetQRY = MySQLSelectX($GetSQL);
	$i = 0;

	while($GetRST = mysqli_fetch_array($GetQRY)) {
		$arrCol[$i]['CardCode'] = $GetRST['CardCode'];
		$arrCol[$i]['CardName'] = $GetRST['CardName'];
		$i++;
	}

	$arrCol['Rows'] = $i;
}

if($_GET['p'] == "GetSlpName") {
	$GetSQL = "SELECT T0.uKey, CONCAT(T0.uName,' ',T0.uLastName) AS 'SlpName' FROM users T0 LEFT JOIN positions T1 ON T0.LvCode = T1.LvCode WHERE T1.uClass IN (18,19,20) AND T0.uLastName != 'Online'";
	$GetQRY = MySQLSelectX($GetSQL);
	$i = 0;

	while($GetRST = mysqli_fetch_array($GetQRY)) {
		$arrCol[$i]['SlpCode'] = $GetRST['uKey'];
		$arrCol[$i]['SlpName'] = $GetRST['SlpName'];
		$i++;
	}

	$arrCol['Rows'] = $i;
}

if($_GET['p'] == "GetChqCause") {
	$GetSQL = "SELECT T0.TransID, T0.ReturnName FROM chq_causereturn T0 WHERE T0.TransStatus = 'A' ORDER BY T0.ReturnCode";
	$GetQRY = MySQLSelectX($GetSQL);
	$i = 0;

	while($GetRST = mysqli_fetch_array($GetQRY)) {
		$arrCol[$i]['ReturnCode'] = $GetRST['TransID'];
		$arrCol[$i]['ReturnName'] = $GetRST['ReturnName'];
		$i++;
	}

	$arrCol['Rows'] = $i;
}

if($_GET['p'] == "SearchChq") {
	$CheckNum = $_POST['ChqDoc'];
	$GetSQL = 
		"SELECT TOP 1
			T0.CheckNum,T0.CheckDate,T0.CheckSum,T3.CardCode,T3.CardName,T4.SlpCode,T4.SlpName,T0.RcptNum,T4.Memo
		FROM  OCHH T0
		LEFT JOIN RCT1 T1 ON T0.RcptNum = T1.DocNum
		LEFT JOIN RCT2 T2 ON T1.DocNum = T2.DocNum
		LEFT JOIN OINV T3 ON T2.DocEntry = T3.DocEntry  
		LEFT JOIN OSLP T4 ON T3.SlpCode = T4.SlpCode
		WHERE T0.CheckNum LIKE '%$CheckNum%' AND (T2.InvType = 13 OR T2.InvType = 30)";
	$Rows = ChkRowSAP($GetSQL);
	if($Rows == 0) {
		$arrCol['Rows'] = 0;
	} else {
		$GetQRY = SAPSelect($GetSQL);
		$r = 0;
		while($GetRST = odbc_fetch_array($GetQRY)) {
			$arrCol['CardCode']  = $GetRST['CardCode'];
			$arrCol['CheckSum']  = $GetRST['CheckSum'];
			$arrCol['CheckDate'] = date("Y-m-d",strtotime($GetRST['CheckDate']));
			$arrCol['SlpCode']   = $GetRST['Memo'];
			$r++;
		}
		$arrCol['Rows'] = $r;
	}
}

if($_GET['p'] == "SearchDoc") {
	$txt_search = $_POST['SearchBar'];
	$Prefix     = substr($txt_search,0,4);

	$ChkRows = "SELECT T0.CHQ_ID FROM chq_return T0 LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode WHERE (T0.DocNum LIKE '%$txt_search%') OR (T0.CardCode LIKE '%$txt_search%') OR (T1.CardName LIKE '%$txt_search%') OR (T0.CHQ_No LIKE '%$txt_search%')";
	$Rows    = ChkRowDB($ChkRows);

	switch($Rows) {
		case 0:
			$arrCol['Rows'] = 0;
			break;
		case 1:
			$arrCol['Rows'] = 1;
			$ChkID = MySQLSelect($ChkRows);
			$CHQ_ID = $ChkID['CHQ_ID'];
			$SUM_CheckSUM = 0;
			$SUM_Applied  = 0;
			$SUM_Balance  = 0;
			$ResultSQL =
				"SELECT
					T1.DocNum, T1.CHQ_DateReturn, T1.CardCode, IFNULL(T1.CardName, T2.CardName) AS 'CardName', T1.CHQ_No, T1.CHQ_Amount,
					CONCAT(T3.ReturnCode,' | ',T3.ReturnName) AS 'CauseReturn', T0.DatePaid, T0.Remark, T0.Amount
				FROM chq_detail T0
				LEFT JOIN chq_return T1 ON T0.DocNum = T1.DocNum
				LEFT JOIN OCRD T2 ON T1.CardCode = T2.CardCode
				LEFT JOIN chq_causereturn T3 ON T1.CauseReturn = T3.TransID
				WHERE T1.CHQ_ID = $CHQ_ID
				ORDER BY T0.DatePaid ASC";
			$ChkRows = ChkRowDB($ResultSQL);
			if($ChkRows > 0) {
				$ResultQRY = MySQLSelectX($ResultSQL);
				$DocNum = null;
				
				$r = 0;
				while($Result = mysqli_fetch_array($ResultQRY)) {
					if($DocNum == null) {
						$arrCol['HD']['DocNum']         = $Result['DocNum'];
						$arrCol['HD']['CardCode']       = $Result['CardCode']." | ".$Result['CardName'];
						$arrCol['HD']['CHQ_DateReturn'] = date("d/m/Y",strtotime($Result['CHQ_DateReturn']));
						$arrCol['HD']['CHQ_No']         = $Result['CHQ_No'];
						$arrCol['HD']['CHQ_Amount']     = number_format($Result['CHQ_Amount'],2);
						$arrCol['HD']['CauseReturn']    = $Result['CauseReturn'];

						$SUM_CheckSUM = $Result['CHQ_Amount'];
					}

					$arrCol['BD_'.$r]['DatePaid'] = date("d/m/Y",strtotime($Result['DatePaid']));
					$arrCol['BD_'.$r]['Remark']   = $Result['Remark'];
					$arrCol['BD_'.$r]['Amount']   = number_format($Result['Amount'],2);

					$SUM_Applied = $SUM_Applied + $Result['Amount'];
					$r++;
				}
			} else {
				$ResultSQL = 
					"SELECT
						T0.DocNum, T0.CHQ_DateReturn, T0.CardCode, IFNULL(T0.CardName,T1.CardName) AS 'CardName', T0.CHQ_No, T0.CHQ_Amount,
						CONCAT(T2.ReturnCode,' | ',T2.ReturnName) AS 'CauseReturn'
					FROM chq_return T0
					LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode
					LEFT JOIN chq_causereturn T2 ON T0.CauseReturn = T2.TransID
					WHERE T0.CHQ_ID = $CHQ_ID LIMIT 1";
				$Result = MySQLSelect($ResultSQL);
				$arrCol['HD']['DocNum']         = $Result['DocNum'];
				$arrCol['HD']['CardCode']       = $Result['CardCode']." | ".$Result['CardName'];
				$arrCol['HD']['CHQ_DateReturn'] = date("d/m/Y",strtotime($Result['CHQ_DateReturn']));
				$arrCol['HD']['CHQ_No']         = $Result['CHQ_No'];
				$arrCol['HD']['CHQ_Amount']     = number_format($Result['CHQ_Amount'],2);
				$arrCol['HD']['CauseReturn']    = $Result['CauseReturn'];
				$SUM_CheckSUM = $Result['CHQ_Amount'];
				$r = 0;

			}
			$arrCol['LOOP'] = $r;
			$arrCol['FT']['SUM_CheckSUM'] = number_format($SUM_CheckSUM,2);
			$arrCol['FT']['SUM_Applied']  = number_format($SUM_Applied,2);
			$arrCol['FT']['SUM_Balance']  = number_format($SUM_CheckSUM - $SUM_Applied,2);


			break;
		default:
			$arrCol['Rows'] = $Rows;
			$ResultSQL = 
				"SELECT
					T0.DocNum, T0.CHQ_DateReturn, T0.CardCode, IFNULL(T0.CardName, T1.CardName) AS 'CardName', T0.CHQ_No, T0.CHQ_Amount,
					IFNULL((SELECT SUM(P1.Amount) FROM chq_detail P1 WHERE P1.DocNum = T0.DocNum),0) AS 'Paid'
				FROM chq_return T0 
				LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode 
				WHERE (T0.DocNum LIKE '%$txt_search%') OR (T0.CardCode LIKE '%$txt_search%') OR (T1.CardName LIKE '%$txt_search%') OR (T0.CHQ_No LIKE '%$txt_search%') 
				ORDER BY T0.CHQ_DateReturn ASC";
			$ResultQRY = MySQLSelectX($ResultSQL);
			$r = 0;
			while($Result = mysqli_fetch_array($ResultQRY)) {
				$arrCol['BD_'.$r]['DocNum']         = $Result['DocNum'];
				$arrCol['BD_'.$r]['CHQ_DateReturn'] = date("d/m/Y",strtotime($Result['CHQ_DateReturn']));
				$arrCol['BD_'.$r]['CardCode']       = $Result['CardCode']." | ".$Result['CardName'];
				$arrCol['BD_'.$r]['CHQ_No']         = $Result['CHQ_No'];
				$arrCol['BD_'.$r]['CHQ_Amount']     = number_format($Result['CHQ_Amount'],2);
				$arrCol['BD_'.$r]['CHQ_Applied']    = number_format($Result['Paid'],2);
				$arrCol['BD_'.$r]['CHQ_Balance']    = number_format($Result['CHQ_Amount'] - $Result['Paid'],2);
				$r++;
			}
			break;
	}
}

if($_GET['p'] == "SaveChq") {
	$DocYear   = substr(date("Y")+543,2);
	$DocMonth  = date("m");
	$Prefix    = "QRT-".$DocYear.$DocMonth;
	$DocNumSQL = "SELECT CONVERT(SUBSTRING(T0.DocNum,9),DECIMAL(3,0))+1 AS 'DocNum' FROM chq_return T0 WHERE T0.DocNum LIKE '$Prefix%' ORDER BY T0.DocNum DESC LIMIT 1";
	$Rows      = ChkRowDB($DocNumSQL);
	if($Rows == 0 ){
		$ChqDocNum = $Prefix."001";
	} else {
		$DocNumRST = MySQLSelect($DocNumSQL);
		$Subfix    = $DocNumRST['DocNum'];
		if($Subfix <= 9) {
			$ChqDocNum = $Prefix."00".$Subfix;
		} elseif($Subfix <= 99) {
			$ChqDocNum = $Prefix."0".$Subfix;
		} else {
			$ChqDocNum = $Prefix.$Subfix;
		}
	}

	$CardNameSQL = "SELECT T0.CardName FROM OCRD T0 WHERE T0.CardCode = '".$_POST['CardCode']."' LIMIT 1";
	$CardNameRST = MySQLSelect($CardNameSQL);

	$DocNum          = $ChqDocNum;
	$CHQ_No          = $_POST['ChqDocNum'];
	$CHQ_Amount      = $_POST['ChqAmount'];
	$CHQ_DateReturn  = $_POST['ChqReturnDate'];
	$CHQ_SaleReceive = $_POST['SaleReceiveDate'];
	$CardCode        = $_POST['CardCode'];
	$CardName        = $CardNameRST['CardName'];
	$SaleUkey        = $_POST['SlpCode'];
	$CreateUkey      = $_SESSION['ukey'];
	$Remark          = $_POST['ChqRemark'];
	$CauseReturn     = $_POST['ChqCauseReturn'];

	$HeaderSQL = 
		"INSERT INTO chq_return SET
			DocNum = '$DocNum',
			CHQ_No = '$CHQ_No',
			CHQ_Amount = '$CHQ_Amount',
			CHQ_DateReturn = '$CHQ_DateReturn',
			CHQ_SaleReceive = '$CHQ_SaleReceive',
			CardCode = '$CardCode',
			CardName = '$CardName',
			SaleUkey = '$SaleUkey',
			CreateUkey = '$CreateUkey',
			Remark = '$Remark',
			CauseReturn = '$CauseReturn'";
	echo $HeaderSQL;
	MySQLInsert($HeaderSQL);

	if($Remark != "") {
		$RemarkSQL = "INSERT INTO chq_remark SET DocNum = '$DocNum', Remark = '$Remark', CreateUkey = '$CreateUkey'";
		MySQLInsert($RemarkSQL);
	}

	$arrCol['SaveStatus'] = "SUCCESS";
}

if($_GET['p'] == "GetList") {
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
		WHERE T0.Status = 0
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

if($_GET['p'] == "GetDetail") {
	$CHQ_ID = $_POST['cid'];
	$r = 0;
	$SumCheckSUM = 0;
	$SumApplied  = 0;
	$GetSQL =
		"SELECT
				T1.CHQ_ID, T1.DocNum, T0.DatePaid AS 'DatePaid', T0.Amount, T0.Remark, T1.CHQ_Amount,
				IFNULL((SELECT COUNT(P1.ID) FROM chq_detail P1 WHERE P1.DocNum = T1.DocNum),0) AS 'CountRow'
				FROM chq_detail T0
				LEFT JOIN chq_return T1 ON T0.DocNum = T1.DocNum
		WHERE T1.CHQ_ID = $CHQ_ID
		ORDER BY T0.DatePaid";
	$Rows = ChkRowDB($GetSQL);
	if($Rows > 0) {
		$GetQRY = MySQLSelectX($GetSQL);
		while($GetRST = mysqli_fetch_array($GetQRY)) {
			if($GetRST['CountRow'] == 0) {
				$r = 0;
			} else {
				$arrCol['BD_'.$r]['DatePaid'] = date("d/m/Y",strtotime($GetRST['DatePaid']));
				$arrCol['BD_'.$r]['Remark']   = $GetRST['Remark'];
				$arrCol['BD_'.$r]['Applied']  = number_format($GetRST['Amount'],2);
				
				$SumApplied = $SumApplied + $GetRST['Amount'];
				$r++;
			}
			$SumCheckSUM = $GetRST['CHQ_Amount'];
			$arrCol['HD']['DocNum']     = $GetRST['DocNum'];
			$arrCol['HD']['CHQ_ID']     = $GetRST['CHQ_ID'];
		}
		$SumBalance = $SumCheckSUM - $SumApplied;
	} else {
		$DocNumSQL = "SELECT T0.CHQ_ID, T0.DocNum, T0.CHQ_Amount FROM chq_return T0 WHERE T0.CHQ_ID = $CHQ_ID LIMIT 1";
		$DocNumRST = MySQLSelect($DocNumSQL);
		$arrCol['HD']['DocNum']     = $DocNumRST['DocNum'];
		$arrCol['HD']['CHQ_ID']     = $DocNumRST['CHQ_ID'];
		$SumCheckSUM                = $DocNumRST['CHQ_Amount'];
		$SumBalance                 = $SumCheckSUM;
	}
	$arrCol['Rows'] = $r;
	$arrCol['FT']['SumCheckSUM'] = number_format($SumCheckSUM,2);
	$arrCol['FT']['SumApplied']  = number_format($SumApplied,2);
	$arrCol['FT']['SumBalance']  = number_format($SumBalance,2);
	
}

if($_GET['p'] == "SaveDetail") {
	$Applied = $_POST['add_applied'];
	$CHQ_ID  = $_POST['add_chqid'];
	$Remark  = $_POST['add_remark'];
	$UpdateUkey = $_SESSION['ukey'];

	if(isset($_POST['add_closed'])) {
		$UpdateSQL = "UPDATE chq_return SET Status = 1 WHERE CHQ_ID = $CHQ_ID";
		MySQLUpdate($UpdateSQL);
		$arrCol['txtMsg'] = "ปิดบัญชีเช็ค ด้วยจำนวนเงิน ".number_format($Applied,2)." เรียบร้อยแล้ว";
	} else {
		$arrCol['txtMsg'] = "รับชำระเพิ่มเติม จำนวนเงิน ".number_format($Applied,2)." เรียบร้อยแล้ว";
	}

	$DocNumSQL = "SELECT T0.DocNum FROM chq_return T0 WHERE T0.CHQ_ID = $CHQ_ID LIMIT 1";
	$DocNumRST = MySQLSelect($DocNumSQL);
	$DocNum    = $DocNumRST['DocNum'];

	$InsertSQL = "INSERT INTO chq_detail SET DocNum = '$DocNum', DatePaid = NOW(), UpdateUkey = '$UpdateUkey', UpdateDate = NOW(), Amount = $Applied, Remark = '$Remark'";
	MySQLInsert($InsertSQL);
	$arrCol['SaveStatus'] = "SUCCESS";
	
}

if($_GET['p'] == "SaveRemark") {
	$CHQ_ID  = $_POST['cid'];
	$Content = $_POST['content'];
	
	$DocNumSQL = "SELECT T0.DocNum FROM chq_return T0 WHERE T0.CHQ_ID = $CHQ_ID LIMIT 1";
	$DocNumRST = MySQLSelect($DocNumSQL);
	$DocNum    = $DocNumRST['DocNum'];

	$ContentSQL = "SELECT T0.Remark FROM chq_remark T0 WHERE T0.DocNum = '$DocNum' ORDER BY T0.ID DESC LIMIT 1";
	$ContentRST = MySQLSelect($ContentSQL);
	if($Content != $ContentRST['Remark']) {
		$InsertSQL = "INSERT INTO chq_remark SET DocNum = '$DocNum', Remark = '$Content', CreateDate = NOW(), CreateUkey = '$CreateUkey', Status = 1";
		MySQLInsert($InsertSQL);
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
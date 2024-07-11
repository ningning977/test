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

if($_GET['a'] == 'CallData') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	$CH    = $_POST['CH'];
	$GP    = $_POST['GP'];
	if($GP != '') {
		$GP = "AND (CASE WHEN T0.Price != 0 THEN  (((T0.Price - T0.GrossBuyPr) / T0.Price) * 100) ELSE 0 END <= $GP )";
	}

	$SQL = "
		SELECT T1.DocDate,T0.BaseEntry, T1.DocEntry,(ISNULL(T2.BeginStr,'IV-')+CAST(T1.DocNum AS VARCHAR)) AS DocNum,T1.CardCode,T1.CardName,T3.SlpName,T3.U_Dim1 AS CH,
			T0.ItemCode,T0.Dscription AS ItemName ,T0.Quantity,T0.Price,
			CASE WHEN T0.Price != 0 THEN  (((T0.Price - T0.GrossBuyPr) / T0.Price) * 100) ELSE 0 END AS GP,
			(SELECT CASE WHEN SUM(A0.Price*A0.Quantity) = 0 THEN 0 ELSE ((SUM(A0.Price*A0.Quantity)-SUM(A0.GrossBuyPr*A0.Quantity))/ SUM(A0.Price*A0.Quantity))*100 END  FROM INV1 A0 WHERE A0.DocEntry = T0.DocEntry) AS TotalGP,
			'N' AS GPApp
		FROM INV1 T0
		LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
		LEFT JOIN OSLP T3 ON T3.SlpCode = T1.SlpCode
		WHERE YEAR(T1.DocDate) = $Year AND MONTH(T1.DocDate) = $Month AND T3.U_Dim1 IN ($CH) $GP";
	
	$QRY = SAPSelect($SQL);
	$r = 0;
	while($RST = odbc_fetch_array($QRY)) {
		$arrCol[$r]['DocNum'] = $RST['DocNum'];
		$arrCol[$r]['CardName'] = $RST['CardCode']." | ".conutf8($RST['CardName']);
		$arrCol[$r]['SlpName'] = conutf8($RST['SlpName']);
		$arrCol[$r]['CH'] = $RST['CH'];
		$arrCol[$r]['ItemName'] = $RST['ItemCode']." | ".conutf8($RST['ItemName']);
		$arrCol[$r]['Quantity'] = number_format($RST['Quantity'],0);
		$arrCol[$r]['Price'] = number_format($RST['Price'],2);
		$arrCol[$r]['GP'] = number_format($RST['GP'],3);
		$arrCol[$r]['TotalGP'] = number_format($RST['TotalGP'],3);
		$chk = 0;
		if($RST['BaseEntry'] != '') {
			$SQL = "SELECT ResultApp FROM apporder WHERE DocEntry = ".$RST['BaseEntry']." AND AppGP = 1 ";
			if(CHKRowDB($SQL) >= 1){
				$getResult = MySQLSelectX($SQL);
				while ($Result = mysqli_fetch_array($getResult)){
					if ($Result['ResultApp'] == 'N' AND $chk == 0){
						//ไม่อนุมัติ
						$GPApp = "[N]";
						$chk = 1;
					}else{
						if ($Result['ResultApp'] == 'Y' && $chk == 0){
							//อนุมัติ
							$GPApp = "[Y]";
							$chk = 1;
						}else{
							// ยังไม่พิจารณา
							$GPApp = "[0]";
						}
					}
				}

			}else{
				//ไม่ผ่านการอนุมัติ
				$GPApp = "[-]";
			}
		}else{
			//เข้าตรง
			$GPApp = "[D]";
		}
		$arrCol[$r]['GPApp'] = $GPApp;
		$r++;
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
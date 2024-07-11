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

if($_GET['a'] == 'SelectData') {
	$CardCode = $_POST['CardCode'];
	$ItemCode = $_POST['ItemCode'];
	$SQL = "SELECT T2.BeginStr, T1.DocNum, T1.NumAtCard, T1.DocEntry, T1.DocDate, T1.CardCode, T1.CardName, T0.ItemCode, T0.Dscription, 
				T0.Quantity, T3.SalUnitMsr, T0.PriceBefDi, T0.U_DiscP1, T0.U_DiscP2, T0.U_DiscP3, T0.U_DiscP4, T0.LineTotal
			FROM INV1 T0
			LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
			LEFT JOIN OITM T3 ON T3.ItemCode = T0.ItemCode
			WHERE T1.CardCode = '$CardCode' AND T0.ItemCode = '$ItemCode'
			ORDER BY T1.DocDate DESC";
	// echo $SQL;
	$no = 1;
	$r  = 0;
	$U_Disc = "";
	if(ChkRowSAP($SQL) > 0) {
		$QRY = SAPSelect($SQL);
		while($result = odbc_fetch_array($QRY)) {
			$arrCol[$r]['no'] = $no;                                                              // ลำดับ
			$arrCol[$r]['DocDate'] = date("d/m/Y", strtotime($result['DocDate']));                // วันที่
			if ($result['NumAtCard'] == '') {                                                     // เลขที่เวลา
				$arrCol[$r]['DocNum'] = $result['BeginStr'].$result['DocNum'];
			}else{
				$arrCol[$r]['DocNum'] = $result['NumAtCard'];
			}
			$arrCol[$r]['CardName'] = $result['CardCode']." - ".conutf8($result['CardName']);     // ชื่อร้านค้า
			$arrCol[$r]['ItemCode'] = $result['ItemCode']." - ".conutf8($result['Dscription']);   // รายการสินค้า
			$arrCol[$r]['Quantity'] = number_format($result['Quantity'],0);                       // จำนวนสินค้า
			$arrCol[$r]['Unit']     = conutf8($result['SalUnitMsr']);							  // หน่วย					
			$arrCol[$r]['Price']    = number_format($result['PriceBefDi'],2);					  // ราคา (ก่อน VAT)
			if($result['U_DiscP1'] > 0 && $result['U_DiscP1'] != "") {                            
				$U_Disc .= number_format($result['U_DiscP1'],0)."+";
			}elseif($result['U_DiscP2'] > 0 && $result['U_DiscP2'] != ""){
				$U_Disc .= number_format($result['U_DiscP2'],0)."+";
			}elseif($result['U_DiscP3'] > 0 && $result['U_DiscP3'] != ""){
				$U_Disc .= number_format($result['U_DiscP3'],0)."+";
			}elseif($result['U_DiscP4'] > 0 && $result['U_DiscP4'] != ""){
				$U_Disc .= number_format($result['U_DiscP4'],0)."+";
			}else{
				$U_Disc .= "0+";
			}
			$arrCol[$r]['U_Disc'] = substr($U_Disc,0,-1)."%";									  // ส่วนลด
			$arrCol[$r]['LineTotal']  = number_format($result['LineTotal'],2);                    // รวม
			$no++;
			$r++;
			$U_Disc = "";
		}
	}

	if(ChkRowSAP8($SQL) > 0) {
		$QRY = conSAP8($SQL);
		while($result = odbc_fetch_array($QRY)) {
			$arrCol[$r]['no'] = $no;                                                              // ลำดับ
			$arrCol[$r]['DocDate'] = date("d/m/Y", strtotime($result['DocDate']));                // วันที่
			if ($result['NumAtCard'] == '') {                                                     // เลขที่เวลา
				$arrCol[$r]['DocNum'] = $result['BeginStr'].$result['DocNum'];
			}else{
				$arrCol[$r]['DocNum'] = $result['NumAtCard'];
			}
			$arrCol[$r]['CardName'] = $result['CardCode']." - ".conutf8($result['CardName']);     // ชื่อร้านค้า
			$arrCol[$r]['ItemCode'] = $result['ItemCode']." - ".conutf8($result['Dscription']);   // รายการสินค้า
			$arrCol[$r]['Quantity'] = number_format($result['Quantity'],0);                       // จำนวนสินค้า
			$arrCol[$r]['Unit']     = conutf8($result['SalUnitMsr']);							  // หน่วย		
			$arrCol[$r]['Price']    = number_format($result['PriceBefDi'],2);					  // ราคา (ก่อน VAT)
			if($result['U_DiscP1'] > 0 && $result['U_DiscP1'] != "") {
				$U_Disc .= number_format($result['U_DiscP1'],0)."+";
			}elseif($result['U_DiscP2'] > 0 && $result['U_DiscP2'] != ""){
				$U_Disc .= number_format($result['U_DiscP2'],0)."+";
			}elseif($result['U_DiscP3'] > 0 && $result['U_DiscP3'] != ""){
				$U_Disc .= number_format($result['U_DiscP3'],0)."+";
			}elseif($result['U_DiscP4'] > 0 && $result['U_DiscP4'] != ""){
				$U_Disc .= number_format($result['U_DiscP4'],0)."+";
			}else{
				$U_Disc .= "0+";
			}
			$arrCol[$r]['U_Disc'] = substr($U_Disc,0,-1)."%";									  // ส่วนลด
			$arrCol[$r]['LineTotal']  = number_format($result['LineTotal'],2);                    // รวม
			$no++;
			$r++;
			$U_Disc = "";
		}
	}

	if($r == 0) {
		$arrCol[$r]['no']       = "";   
		$arrCol[$r]['DocDate']  = "ไม่มีข้อมูล";    
		$arrCol[$r]['DocNum']   = "";
		$arrCol[$r]['CardName'] = "";
		$arrCol[$r]['ItemCode'] = "";
		$arrCol[$r]['Quantity'] = "";
		$arrCol[$r]['Unit']     =  "";
		$arrCol[$r]['Price']    = "";
		$arrCol[$r]['U_Disc']   = "";
		$arrCol[$r]['LineTotal'] = "";
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

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

if($_GET['a'] == 'FileImport') {
	move_uploaded_file($_FILES["FileImport"]["tmp_name"],"../../../../FileImport/PUImport.xlsx");
	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
	$reader->setReadDataOnly(true);
	$spreadsheet = $reader->load("../../../../FileImport/PUImport.xlsx");
	$sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
	$data = $sheet->toArray();
	$getData = array(); $r = 0; $v = 0;
	foreach ($data as $cell) {
		if($cell[0] != "") {
			$r++; $v = 1;
			foreach ($cell as $value) {
				$Col = $sheet->getCellByColumnAndRow($v, $r)->getParent()->getCurrentCoordinate(); $v++;
				$getData[$r][substr($Col,0,1)] = $value;
			}
		}
	}
	
	$ChkA = 0; $ChkB = 0;
	for($i = 1; $i <= $r; $i++) {
		$SQL = "INSERT INTO skuplan SET
				typeCode = '".$getData[$i]['A']."',
                typeName = '".$getData[$i]['B']."',
                lnnum = '".$getData[$i]['C']."',
                ItemCode = '".$getData[$i]['D']."',
                StatusItem = '".$getData[$i]['F']."',";
				if($getData[$i]['G'] != "" && $getData[$i]['G'] != '-') { 
					$exEndDate = explode(".",$getData[$i]['G']);
					if(count($exEndDate) == 3) {
						$SQL .= "EndDate = '".$exEndDate[2]."-".$exEndDate[1]."-".$exEndDate[0]."',"; 
					}else{
						$SQL .= "EndDate  = '".date("Y-m-d",(($getData[$i]['G']-25569)*86400))."',"; 
					}
				}
				$SQL.= "StockQty = '".$getData[$i]['H']."',";
				if($getData[$i]['I'] != "" && $getData[$i]['I'] != '-') { 
					$exPODate = explode(".",$getData[$i]['I']);
					if(count($exPODate) == 3) {
						$SQL .= "PODate = '".$exPODate[2]."-".$exPODate[1]."-".$exPODate[0]."',"; 
					}else{
						$SQL .= "PODate = '".date("Y-m-d",(($getData[$i]['I']-25569)*86400))."',"; 
					}
				}
				if($getData[$i]['J'] != "" && $getData[$i]['J'] != '-') { 
					$exDL1 = explode(".",$getData[$i]['J']);
					if(count($exDL1) == 3) {
						$SQL .= "DL1 = '".$exDL1[2]."-".$exDL1[1]."-".$exDL1[0]."',"; 
					}else{
						$SQL .= "DL1 = '".date("Y-m-d",(($getData[$i]['J']-25569)*86400))."',"; 
					}
				}
				if($getData[$i]['K'] != "" && $getData[$i]['K'] != '-') { 
					$exDL2 = explode(".",$getData[$i]['K']);
					if(count($exDL2) == 3) {
						$SQL .= "DL2 = '".$exDL2[2]."-".$exDL2[1]."-".$exDL2[0]."',"; 
					}else{
						$SQL .= "DL2 = '".date("Y-m-d",(($getData[$i]['K']-25569)*86400))."',"; 
					}
				}
				if($getData[$i]['L'] != "" && $getData[$i]['L'] != '-') { 
					$exDL3 = explode(".",$getData[$i]['L']);
					if(count($exDL3) == 3) {
						$SQL .= "DL3 = '".$exDL3[2]."-".$exDL3[1]."-".$exDL3[0]."',"; 
					}else{
						$SQL .= "DL3 = '".date("Y-m-d",(($getData[$i]['L']-25569)*86400))."',"; 
					}
				}
				if($getData[$i]['M'] != "" && $getData[$i]['M'] != '-') { 
					$exKBIRecive = explode(".",$getData[$i]['M']);
					if(count($exKBIRecive) == 3) {
						$SQL .= "KBIRecive = '".$exKBIRecive[2]."-".$exKBIRecive[1]."-".$exKBIRecive[0]."',"; 
					}else{
						$SQL .= "KBIRecive = '".date("Y-m-d",(($getData[$i]['M']-25569)*86400))."',"; 
					}
				}
				if($getData[$i]['N'] != "" && $getData[$i]['N'] != '-') { 
					$exSaleDate = explode(".",$getData[$i]['N']);
					if(count($exSaleDate) == 3) {
						$SQL .= "SaleDate = '".$exSaleDate[2]."-".$exSaleDate[1]."-".$exSaleDate[0]."',"; 
					}else{
						$SQL .= "SaleDate = '".date("Y-m-d",(($getData[$i]['N']-25569)*86400))."',"; 
					}
				}
				$SQL.= "InQty  = '".$getData[$i]['O']."',
				TotalQty  = '".$getData[$i]['P']."',
				Remark  = '".$getData[$i]['Q']."',
				DateCreate  = NOW(),
				ukeyCreate  = '".$_SESSION['ukey']."',
				StatusDoc  = '1'";
		if (substr(strtoupper($getData[$i]['A']),0,1) == 'A' AND $ChkA == 0){
			MySQLDelete("DELETE FROM skuplan WHERE typeCode LIKE 'A%' AND StatusDoc = 0");
			MySQLUpdate("UPDATE skuplan SET StatusDoc = 0 WHERE typeCode LIKE 'A%'");
			$ChkA++;
		} 
		if (substr(strtoupper($getData[$i]['A']),0,1) == 'B' AND $ChkB == 0){
			MySQLDelete("DELETE FROM skuplan WHERE typeCode LIKE 'B%' AND StatusDoc = 0");
			MySQLUpdate("UPDATE skuplan SET StatusDoc = 0 WHERE typeCode LIKE 'B%'"); 
			$ChkB++;
		} 
		MySQLInsert($SQL);
	}
}

if($_GET['a'] == 'CallData1') {
	$Year = $_POST['txtYear'];
	$Month = $_POST['txtMonth'];
	$SQL = "SELECT
				T0.DocDate, T0.CreateDate, T1.ItemCode, T1.CodeBars, T1.Dscription, T1.Quantity, T1.UnitMsr, T1.WhsCode,
				(T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', ISNULL(T3.U_ProductStatus,'K') AS 'U_ProductStatus',
				'' AS 'Q_MT1', '' AS 'Q_MT2', '' AS 'Q_TT', '' AS 'Q_OUL', '' AS 'Q_ONL', '' AS 'Q_MKT'
			FROM OPDN T0
			LEFT JOIN PDN1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN NNM1 T2 ON T0.Series   = T2.Series
			LEFT JOIN OITM T3 ON T1.ItemCode = T3.ItemCode
			WHERE (T1.ItemCode IS NOT NULL AND T1.ItemCode != '00-999-999') AND 
				(YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) = $Month)
			ORDER BY T0.DocDate DESC, T0.DocEntry ASC, T1.VisOrder ASC";
	$QRY = SAPSelect($SQL);
	$r = 0; $No = 1;
	while($result = odbc_fetch_array($QRY)) {
		$arrCol[$r]['No']         = $No;
		$arrCol[$r]['DocDate']    = date("d/m/Y",strtotime($result['DocDate']));
		$arrCol[$r]['ItemCode']   = $result['ItemCode'];
		$arrCol[$r]['CodeBars']   = $result['CodeBars'];
		$arrCol[$r]['Dscription'] = conutf8($result['Dscription']);
		$arrCol[$r]['ProductStatus'] = $result['U_ProductStatus'];
		$arrCol[$r]['Quantity']   = number_format($result['Quantity'],0);
		$arrCol[$r]['UnitMsr']    = conutf8($result['UnitMsr']);
		$arrCol[$r]['WhsCode']    = conutf8($result['WhsCode']);
		$arrCol[$r]['DocNum']     = conutf8($result['DocNum']);
		$arrCol[$r]['CreateDate'] = date("d/m/Y",strtotime($result['CreateDate']));
		$arrCol[$r]['Q_MT1']      = $result['Q_MT1'];
		$arrCol[$r]['Q_MT2']      = $result['Q_MT2'];
		$arrCol[$r]['Q_TT']       = $result['Q_TT'];
		$arrCol[$r]['Q_OUL']      = $result['Q_OUL'];
		$arrCol[$r]['Q_ONL']      = $result['Q_ONL'];
		$arrCol[$r]['Q_MKT']      = $result['Q_MKT'];
		$arrCol[$r]['Quota']      = "";
		$r++; $No++;
	}
}

if($_GET['a'] == 'GetDateUpdate2') {
	$SQL = "SELECT DISTINCT SUBSTRING(typeCode,1,1) AS TypeCode, DateCreate FROM skuplan WHERE StatusDoc = 1 AND SUBSTRING(TypeCode,1,1) = 'A'";
	$result = MySQLSelect($SQL);
	$arrCol['DateCreate'] = "** วันที่อัพเดท: ".date("d/m/Y",strtotime($result['DateCreate']))." เวลา ".date("H:i",strtotime($result['DateCreate']))." น. **";
}

if($_GET['a'] == 'CallData2') {
	$SQL1 ="SELECT
				T0.ID, T0.typeCode, T0.typeName, T0.lnNum, T0.ItemCode, T1.ItemName, T0.StatusItem, T0.StockQty, T0.InQty, T0.TotalQty, T0.Remark, T0.DateCreate, 
				CASE WHEN T0.EndDate = '0000-00-00' OR T0.EndDate IS NULL THEN '-' ELSE T0.EndDate END AS 'EndDate',
				CASE WHEN T0.PODate = '0000-00-00' OR T0.PODate IS NULL THEN '-' ELSE T0.PODate END AS 'PODate',
				CASE WHEN T0.DL1 = '0000-00-00' OR T0.DL1 IS NULL THEN '-' ELSE T0.DL1 END AS 'DL1',
				CASE WHEN T0.DL2 = '0000-00-00' OR T0.DL2 IS NULL THEN '-' ELSE T0.DL2 END AS 'DL2',
				CASE WHEN T0.DL3 = '0000-00-00' OR T0.DL3 IS NULL THEN '-' ELSE T0.DL3 END AS 'DL3',
				CASE WHEN T0.KBIRecive = '0000-00-00' OR T0.KBIRecive IS NULL THEN '-' ELSE T0.KBIRecive END AS 'KBIRecive',
				CASE WHEN T0.SaleDate = '0000-00-00' OR T0.SaleDate IS NULL THEN '-' ELSE T0.SaleDate END AS 'SaleDate'
			FROM skuplan T0
			LEFT JOIN oitm  T1 ON T1.ItemCode = T0.ItemCode
			WHERE StatusDoc = 1 AND typeCode LIKE 'A%' AND typeName != '' AND typeCode != 'A00'
			ORDER BY T0.typeCode, T0.lnnum";
	$QRY1 = MySQLSelectX($SQL1);
	$Data = array(); $r = 0; $ItemList = "";
	while($result1 = mysqli_fetch_array($QRY1)) {
		if($result1['ItemCode'] != "") { $ItemList .= "'".$result1['ItemCode']."',"; }
		$Data[$r]['ItemCode']   = $result1['ItemCode'];    // รหัสสินค้า
		$Data[$r]['typeCode']   = $result1['typeCode'];    // ประเภท
		$Data[$r]['typeName']   = $result1['typeName'];    // ชื่อประเภท
		$Data[$r]['lnNum']      = $result1['lnNum'];       // ลำดับ
		$Data[$r]['ItemName']   = $result1['ItemName'];    // ชื่อสินค้า
		$Data[$r]['StatusItem'] = $result1['StatusItem'];  // สถานะสินค้า
		if($result1['EndDate'] != '-') { $EndDate = date("d/m/Y",strtotime($result1['EndDate'])); }else{ $EndDate = $result1['EndDate']; }
		$Data[$r]['EndDate']    = $EndDate;                // วันที่สินค้าคาดว่าจะหมด
		$Data[$result1['ItemCode']]['StockQty']   = number_format($result1['StockQty'],0); // จำนวนสินค้าคงเหลือ PCs.
		if($result1['PODate'] != '-') { $PODate = date("d/m/Y",strtotime($result1['PODate'])); }else{ $PODate = $result1['PODate']; }
		$Data[$r]['PODate']     = $PODate;                 // วันที่เปิด PO
		if($result1['DL1'] != '-') { $DL1 = date("d/m/Y",strtotime($result1['DL1'])); }else{ $DL1 = $result1['DL1']; }
		$Data[$r]['DL1']        = $DL1;                    // กำหนดส่ง
		if($result1['DL2'] != '-') { $DL2 = date("d/m/Y",strtotime($result1['DL2'])); }else{ $DL2 = $result1['DL2']; }
		$Data[$r]['DL2']        = $DL2;                    // เลื่อนส่งครั้งที่ 1
		if($result1['DL3'] != '-') { $DL3 = date("d/m/Y",strtotime($result1['DL3'])); }else{ $DL3 = $result1['DL3']; }
		$Data[$r]['DL3']        = $DL3;                    // เลื่อนส่งครั้งที่ 2
		if($result1['KBIRecive'] != '-') { $KBIRecive = date("d/m/Y",strtotime($result1['KBIRecive'])); }else{ $KBIRecive = $result1['KBIRecive']; }
		$Data[$r]['KBIRecive']  = $KBIRecive;              // ประมาณการสินค้าถึง KBI
		if($result1['SaleDate'] != '-') { $SaleDate = date("d/m/Y",strtotime($result1['SaleDate'])); }else{ $SaleDate = $result1['SaleDate']; }
		$Data[$r]['SaleDate']   = $SaleDate;               // วันที่สินค้าพร้อมขาย
		$Data[$r]['InQty']      = number_format($result1['InQty'],0);    // จำนวนที่เข้าในล็อตถัดไป(pcs)
		$Data[$r]['TotalQty']   = number_format($result1['TotalQty'],0); // จำนวนสั่งซื้อทั้งหมด (pcs)
		$Data[$r]['Remark']     = $result1['Remark'];      // หมายเหตุ
		$r++;
	}
	if($r != 0) {
		$ItemList = "ItemCode IN (".substr($ItemList,0,-1).") AND";
		$SQL2 = "SELECT ItemCode,SUM(OnHand) AS OnHand FROM OITW WHERE $ItemList WhsCode IN ('KSY','KSM','KB4','PLA') GROUP BY ItemCode";
		$QRY2 = SAPSelect($SQL2);
		while($result2 = odbc_fetch_array($QRY2)) {
			$Data[$result2['ItemCode']]['StockQty'] = number_format($result2['OnHand'],0);
		}

		$tmpName = ""; $Row = 0;
		for($i = 0; $i < $r; $i++) {
			if($tmpName != $Data[$i]['typeName']){
				$arrCol[$Row]['lnNum']      = ""; // ลำดับ
				$arrCol[$Row]['ItemCode']   = "H"; // รหัสสินค้า
				$arrCol[$Row]['ItemName']   = $Data[$i]['typeName']; // ชื่อสินค้า
				$arrCol[$Row]['StatusItem'] = ""; // สถานะสินค้า
				$arrCol[$Row]['EndDate']    = ""; // วันที่สินค้าคาดว่าจะหมด
				$arrCol[$Row]['StockQty']   = ""; // จำนวนสินค้าคงเหลือ PCs.
				$arrCol[$Row]['PODate']     = ""; // วันที่เปิด PO
				$arrCol[$Row]['DL1']        = ""; // กำหนดส่ง
				$arrCol[$Row]['DL2']        = ""; // เลื่อนส่งครั้งที่ 1
				$arrCol[$Row]['DL3']        = ""; // เลื่อนส่งครั้งที่ 2
				$arrCol[$Row]['KBIRecive']  = ""; // ประมาณการสินค้าถึง KBI
				$arrCol[$Row]['SaleDate']   = ""; // วันที่สินค้าพร้อมขาย
				$arrCol[$Row]['InQty']      = ""; // จำนวนที่เข้าในล็อตถัดไป(pcs)
				$arrCol[$Row]['TotalQty']   = ""; // จำนวนสั่งซื้อทั้งหมด (pcs)
				$arrCol[$Row]['Remark']     = ""; // หมายเหตุ

				$Row++;
				$arrCol[$Row]['lnNum']      = $Data[$i]['lnNum'];      // ลำดับ
				if($_SESSION['DeptCode']  == 'DP002' OR  $_SESSION['DeptCode']  == 'DP004'){
					$arrCol[$Row]['ItemCode']   = "<a href='javascript:void(0);' onclick=\"ViewData('".$Data[$i]['ItemCode']."')\">".$Data[$i]['ItemCode']."</a>"; // รหัสสินค้า
				}else{
					$arrCol[$Row]['ItemCode']   = $Data[$i]['ItemCode']; // รหัสสินค้า
				}
				$arrCol[$Row]['ItemName']   = $Data[$i]['ItemName'];   // ชื่อสินค้า
				$arrCol[$Row]['StatusItem'] = $Data[$i]['StatusItem']; // สถานะสินค้า
				$arrCol[$Row]['EndDate']    = $Data[$i]['EndDate'];    // วันที่สินค้าคาดว่าจะหมด
				$arrCol[$Row]['StockQty']   = $Data[$Data[$i]['ItemCode']]['StockQty']; // จำนวนสินค้าคงเหลือ PCs.
				$arrCol[$Row]['PODate']     = $Data[$i]['PODate'];     // วันที่เปิด PO
				$arrCol[$Row]['DL1']        = $Data[$i]['DL1'];        // กำหนดส่ง
				$arrCol[$Row]['DL2']        = $Data[$i]['DL2'];        // เลื่อนส่งครั้งที่ 1
				$arrCol[$Row]['DL3']        = $Data[$i]['DL3'];        // เลื่อนส่งครั้งที่ 2
				$arrCol[$Row]['KBIRecive']  = $Data[$i]['KBIRecive'];  // ประมาณการสินค้าถึง KBI
				$arrCol[$Row]['SaleDate']   = $Data[$i]['SaleDate'];   // วันที่สินค้าพร้อมขาย
				$arrCol[$Row]['InQty']      = $Data[$i]['InQty'];      // จำนวนที่เข้าในล็อตถัดไป(pcs)
				$arrCol[$Row]['TotalQty']   = $Data[$i]['TotalQty'];   // จำนวนสั่งซื้อทั้งหมด (pcs)
				$arrCol[$Row]['Remark']     = $Data[$i]['Remark'];     // หมายเหตุ
			}else{
				$arrCol[$Row]['lnNum']      = $Data[$i]['lnNum'];      // ลำดับ
				if($_SESSION['DeptCode']  == 'DP002' OR  $_SESSION['DeptCode']  == 'DP004'){
					$arrCol[$Row]['ItemCode']   = "<a href='javascript:void(0);' onclick=\"ViewData('".$Data[$i]['ItemCode']."')\">".$Data[$i]['ItemCode']."</a>"; // รหัสสินค้า
				}else{
					$arrCol[$Row]['ItemCode']   = $Data[$i]['ItemCode']; // รหัสสินค้า
				}
				$arrCol[$Row]['ItemName']   = $Data[$i]['ItemName'];   // ชื่อสินค้า
				$arrCol[$Row]['StatusItem'] = $Data[$i]['StatusItem']; // สถานะสินค้า
				$arrCol[$Row]['EndDate']    = $Data[$i]['EndDate'];    // วันที่สินค้าคาดว่าจะหมด
				$arrCol[$Row]['StockQty']   = $Data[$Data[$i]['ItemCode']]['StockQty']; // จำนวนสินค้าคงเหลือ PCs.
				$arrCol[$Row]['PODate']     = $Data[$i]['PODate'];     // วันที่เปิด PO
				$arrCol[$Row]['DL1']        = $Data[$i]['DL1'];        // กำหนดส่ง
				$arrCol[$Row]['DL2']        = $Data[$i]['DL2'];        // เลื่อนส่งครั้งที่ 1
				$arrCol[$Row]['DL3']        = $Data[$i]['DL3'];        // เลื่อนส่งครั้งที่ 2
				$arrCol[$Row]['KBIRecive']  = $Data[$i]['KBIRecive'];  // ประมาณการสินค้าถึง KBI
				$arrCol[$Row]['SaleDate']   = $Data[$i]['SaleDate'];   // วันที่สินค้าพร้อมขาย
				$arrCol[$Row]['InQty']      = $Data[$i]['InQty'];      // จำนวนที่เข้าในล็อตถัดไป(pcs)
				$arrCol[$Row]['TotalQty']   = $Data[$i]['TotalQty'];   // จำนวนสั่งซื้อทั้งหมด (pcs)
				$arrCol[$Row]['Remark']     = $Data[$i]['Remark'];     // หมายเหตุ
			}
			$tmpName = $Data[$i]['typeName']; $Row++;
		} 
	}else{
		$arrCol[0]['lnNum']      = "NoData";
		$arrCol[0]['ItemCode']   = ""; // รหัสสินค้า
		$arrCol[0]['ItemName']   = "ไม่มีข้อมูล :(";
		$arrCol[0]['StatusItem'] = ""; // สถานะสินค้า
		$arrCol[0]['EndDate']    = ""; // วันที่สินค้าคาดว่าจะหมด
		$arrCol[0]['StockQty']   = ""; // จำนวนสินค้าคงเหลือ PCs.
		$arrCol[0]['PODate']     = ""; // วันที่เปิด PO
		$arrCol[0]['DL1']        = ""; // กำหนดส่ง
		$arrCol[0]['DL2']        = ""; // เลื่อนส่งครั้งที่ 1
		$arrCol[0]['DL3']        = ""; // เลื่อนส่งครั้งที่ 2
		$arrCol[0]['KBIRecive']  = ""; // ประมาณการสินค้าถึง KBI
		$arrCol[0]['SaleDate']   = ""; // วันที่สินค้าพร้อมขาย
		$arrCol[0]['InQty']      = ""; // จำนวนที่เข้าในล็อตถัดไป(pcs)
		$arrCol[0]['TotalQty']   = ""; // จำนวนสั่งซื้อทั้งหมด (pcs)
		$arrCol[0]['Remark']     = ""; // หมายเหตุ
	}
}

if($_GET['a'] == 'GetDateUpdate3') {
	$SQL = "SELECT DISTINCT SUBSTRING(typeCode,1,1) AS TypeCode, DateCreate FROM skuplan WHERE StatusDoc = 1 AND SUBSTRING(TypeCode,1,1) = 'B'";
	$result = MySQLSelect($SQL);
	$arrCol['DateCreate'] = "** วันที่อัพเดท: ".date("d/m/Y",strtotime($result['DateCreate']))." เวลา ".date("H:i",strtotime($result['DateCreate']))." น. **";
}

if($_GET['a'] == 'CallData3') {
	$SQL1 ="SELECT
				T0.ID, T0.typeCode, T0.typeName, T0.lnNum, T0.ItemCode, T1.ItemName, T0.StatusItem, T0.StockQty, T0.InQty, T0.TotalQty, T0.Remark, T0.DateCreate, 
				CASE WHEN T0.EndDate = '0000-00-00' OR T0.EndDate IS NULL THEN '-' ELSE T0.EndDate END AS 'EndDate',
				CASE WHEN T0.PODate = '0000-00-00' OR T0.PODate IS NULL THEN '-' ELSE T0.PODate END AS 'PODate',
				CASE WHEN T0.DL1 = '0000-00-00' OR T0.DL1 IS NULL THEN '-' ELSE T0.DL1 END AS 'DL1',
				CASE WHEN T0.DL2 = '0000-00-00' OR T0.DL2 IS NULL THEN '-' ELSE T0.DL2 END AS 'DL2',
				CASE WHEN T0.DL3 = '0000-00-00' OR T0.DL3 IS NULL THEN '-' ELSE T0.DL3 END AS 'DL3',
				CASE WHEN T0.KBIRecive = '0000-00-00' OR T0.KBIRecive IS NULL THEN '-' ELSE T0.KBIRecive END AS 'KBIRecive',
				CASE WHEN T0.SaleDate = '0000-00-00' OR T0.SaleDate IS NULL THEN '-' ELSE T0.SaleDate END AS 'SaleDate'
			FROM skuplan T0
			LEFT JOIN oitm  T1 ON T1.ItemCode = T0.ItemCode
			WHERE StatusDoc = 1 AND typeCode LIKE 'B%' AND typeName != '' AND typeCode != 'B00'
			ORDER BY T0.typeCode, T0.lnnum";
	$QRY1 = MySQLSelectX($SQL1);
	$Data = array(); $r = 0; $ItemList = "";
	while($result1 = mysqli_fetch_array($QRY1)) {
		if($result1['ItemCode'] != "") { $ItemList .= "'".$result1['ItemCode']."',"; }
		$Data[$r]['ItemCode']   = $result1['ItemCode'];    // รหัสสินค้า
		$Data[$r]['typeCode']   = $result1['typeCode'];    // ประเภท
		$Data[$r]['typeName']   = $result1['typeName'];    // ชื่อประเภท
		$Data[$r]['lnNum']      = $result1['lnNum'];       // ลำดับ
		$Data[$r]['ItemName']   = $result1['ItemName'];    // ชื่อสินค้า
		$Data[$r]['StatusItem'] = $result1['StatusItem'];  // สถานะสินค้า
		if($result1['EndDate'] != '-') { $EndDate = date("d/m/Y",strtotime($result1['EndDate'])); }else{ $EndDate = $result1['EndDate']; }
		$Data[$r]['EndDate']    = $EndDate;                // วันที่สินค้าคาดว่าจะหมด
		$Data[$result1['ItemCode']]['StockQty']   = number_format($result1['StockQty'],0); // จำนวนสินค้าคงเหลือ PCs.
		if($result1['PODate'] != '-') { $PODate = date("d/m/Y",strtotime($result1['PODate'])); }else{ $PODate = $result1['PODate']; }
		$Data[$r]['PODate']     = $PODate;                 // วันที่เปิด PO
		if($result1['DL1'] != '-') { $DL1 = date("d/m/Y",strtotime($result1['DL1'])); }else{ $DL1 = $result1['DL1']; }
		$Data[$r]['DL1']        = $DL1;                    // กำหนดส่ง
		if($result1['DL2'] != '-') { $DL2 = date("d/m/Y",strtotime($result1['DL2'])); }else{ $DL2 = $result1['DL2']; }
		$Data[$r]['DL2']        = $DL2;                    // เลื่อนส่งครั้งที่ 1
		if($result1['DL3'] != '-') { $DL3 = date("d/m/Y",strtotime($result1['DL3'])); }else{ $DL3 = $result1['DL3']; }
		$Data[$r]['DL3']        = $DL3;                    // เลื่อนส่งครั้งที่ 2
		if($result1['KBIRecive'] != '-') { $KBIRecive = date("d/m/Y",strtotime($result1['KBIRecive'])); }else{ $KBIRecive = $result1['KBIRecive']; }
		$Data[$r]['KBIRecive']  = $KBIRecive;              // ประมาณการสินค้าถึง KBI
		if($result1['SaleDate'] != '-') { $SaleDate = date("d/m/Y",strtotime($result1['SaleDate'])); }else{ $SaleDate = $result1['SaleDate']; }
		$Data[$r]['SaleDate']   = $SaleDate;               // วันที่สินค้าพร้อมขาย
		$Data[$r]['InQty']      = number_format($result1['InQty'],0);    // จำนวนที่เข้าในล็อตถัดไป(pcs)
		$Data[$r]['TotalQty']   = number_format($result1['TotalQty'],0); // จำนวนสั่งซื้อทั้งหมด (pcs)
		$Data[$r]['Remark']     = $result1['Remark'];      // หมายเหตุ
		$r++;
	}
	
	if($r != 0) {
		$ItemList = "ItemCode IN (".substr($ItemList,0,-1).") AND";
		$SQL2 = "SELECT ItemCode,SUM(OnHand) AS OnHand FROM OITW WHERE $ItemList WhsCode IN ('KSY','KSM','KB4','PLA') GROUP BY ItemCode";
		$QRY2 = SAPSelect($SQL2);
		while($result2 = odbc_fetch_array($QRY2)) {
			$Data[$result2['ItemCode']]['StockQty'] = number_format($result2['OnHand'],0);
		}

		$tmpName = ""; $Row = 0;
		for($i = 0; $i < $r; $i++) {
			if($tmpName != $Data[$i]['typeName']){
				$arrCol[$Row]['lnNum']      = ""; // ลำดับ
				$arrCol[$Row]['ItemCode']   = "H"; // รหัสสินค้า
				$arrCol[$Row]['ItemName']   = $Data[$i]['typeName']; // ชื่อสินค้า
				$arrCol[$Row]['StatusItem'] = ""; // สถานะสินค้า
				$arrCol[$Row]['EndDate']    = ""; // วันที่สินค้าคาดว่าจะหมด
				$arrCol[$Row]['StockQty']   = ""; // จำนวนสินค้าคงเหลือ PCs.
				$arrCol[$Row]['PODate']     = ""; // วันที่เปิด PO
				$arrCol[$Row]['DL1']        = ""; // กำหนดส่ง
				$arrCol[$Row]['DL2']        = ""; // เลื่อนส่งครั้งที่ 1
				$arrCol[$Row]['DL3']        = ""; // เลื่อนส่งครั้งที่ 2
				$arrCol[$Row]['KBIRecive']  = ""; // ประมาณการสินค้าถึง KBI
				$arrCol[$Row]['SaleDate']   = ""; // วันที่สินค้าพร้อมขาย
				$arrCol[$Row]['InQty']      = ""; // จำนวนที่เข้าในล็อตถัดไป(pcs)
				$arrCol[$Row]['TotalQty']   = ""; // จำนวนสั่งซื้อทั้งหมด (pcs)
				$arrCol[$Row]['Remark']     = ""; // หมายเหตุ

				$Row++;
				$arrCol[$Row]['lnNum']      = $Data[$i]['lnNum'];      // ลำดับ
				if($_SESSION['DeptCode']  == 'DP002' OR  $_SESSION['DeptCode']  == 'DP004'){
					$arrCol[$Row]['ItemCode']   = "<a href='javascript:void(0);' onclick=\"ViewData('".$Data[$i]['ItemCode']."')\">".$Data[$i]['ItemCode']."</a>"; // รหัสสินค้า
				}else{
					$arrCol[$Row]['ItemCode']   = $Data[$i]['ItemCode']; // รหัสสินค้า
				}
				$arrCol[$Row]['ItemName']   = $Data[$i]['ItemName'];   // ชื่อสินค้า
				$arrCol[$Row]['StatusItem'] = $Data[$i]['StatusItem']; // สถานะสินค้า
				$arrCol[$Row]['EndDate']    = $Data[$i]['EndDate'];    // วันที่สินค้าคาดว่าจะหมด
				$arrCol[$Row]['StockQty']   = $Data[$Data[$i]['ItemCode']]['StockQty']; // จำนวนสินค้าคงเหลือ PCs.
				$arrCol[$Row]['PODate']     = $Data[$i]['PODate'];     // วันที่เปิด PO
				$arrCol[$Row]['DL1']        = $Data[$i]['DL1'];        // กำหนดส่ง
				$arrCol[$Row]['DL2']        = $Data[$i]['DL2'];        // เลื่อนส่งครั้งที่ 1
				$arrCol[$Row]['DL3']        = $Data[$i]['DL3'];        // เลื่อนส่งครั้งที่ 2
				$arrCol[$Row]['KBIRecive']  = $Data[$i]['KBIRecive'];  // ประมาณการสินค้าถึง KBI
				$arrCol[$Row]['SaleDate']   = $Data[$i]['SaleDate'];   // วันที่สินค้าพร้อมขาย
				$arrCol[$Row]['InQty']      = $Data[$i]['InQty'];      // จำนวนที่เข้าในล็อตถัดไป(pcs)
				$arrCol[$Row]['TotalQty']   = $Data[$i]['TotalQty'];   // จำนวนสั่งซื้อทั้งหมด (pcs)
				$arrCol[$Row]['Remark']     = $Data[$i]['Remark'];     // หมายเหตุ
			}else{
				$arrCol[$Row]['lnNum']      = $Data[$i]['lnNum'];      // ลำดับ
				if($_SESSION['DeptCode']  == 'DP002' OR  $_SESSION['DeptCode']  == 'DP004'){
					$arrCol[$Row]['ItemCode']   = "<a href='javascript:void(0);' onclick=\"ViewData('".$Data[$i]['ItemCode']."')\">".$Data[$i]['ItemCode']."</a>"; // รหัสสินค้า
				}else{
					$arrCol[$Row]['ItemCode']   = $Data[$i]['ItemCode']; // รหัสสินค้า
				}
				$arrCol[$Row]['ItemName']   = $Data[$i]['ItemName'];   // ชื่อสินค้า
				$arrCol[$Row]['StatusItem'] = $Data[$i]['StatusItem']; // สถานะสินค้า
				$arrCol[$Row]['EndDate']    = $Data[$i]['EndDate'];    // วันที่สินค้าคาดว่าจะหมด
				$arrCol[$Row]['StockQty']   = $Data[$Data[$i]['ItemCode']]['StockQty']; // จำนวนสินค้าคงเหลือ PCs.
				$arrCol[$Row]['PODate']     = $Data[$i]['PODate'];     // วันที่เปิด PO
				$arrCol[$Row]['DL1']        = $Data[$i]['DL1'];        // กำหนดส่ง
				$arrCol[$Row]['DL2']        = $Data[$i]['DL2'];        // เลื่อนส่งครั้งที่ 1
				$arrCol[$Row]['DL3']        = $Data[$i]['DL3'];        // เลื่อนส่งครั้งที่ 2
				$arrCol[$Row]['KBIRecive']  = $Data[$i]['KBIRecive'];  // ประมาณการสินค้าถึง KBI
				$arrCol[$Row]['SaleDate']   = $Data[$i]['SaleDate'];   // วันที่สินค้าพร้อมขาย
				$arrCol[$Row]['InQty']      = $Data[$i]['InQty'];      // จำนวนที่เข้าในล็อตถัดไป(pcs)
				$arrCol[$Row]['TotalQty']   = $Data[$i]['TotalQty'];   // จำนวนสั่งซื้อทั้งหมด (pcs)
				$arrCol[$Row]['Remark']     = $Data[$i]['Remark'];     // หมายเหตุ
			}
			$tmpName = $Data[$i]['typeName']; $Row++;
		}
	}else{
		$arrCol[0]['lnNum']      = "NoData";
		$arrCol[0]['ItemCode']   = ""; // รหัสสินค้า
		$arrCol[0]['ItemName']   = "ไม่มีข้อมูล :(";
		$arrCol[0]['StatusItem'] = ""; // สถานะสินค้า
		$arrCol[0]['EndDate']    = ""; // วันที่สินค้าคาดว่าจะหมด
		$arrCol[0]['StockQty']   = ""; // จำนวนสินค้าคงเหลือ PCs.
		$arrCol[0]['PODate']     = ""; // วันที่เปิด PO
		$arrCol[0]['DL1']        = ""; // กำหนดส่ง
		$arrCol[0]['DL2']        = ""; // เลื่อนส่งครั้งที่ 1
		$arrCol[0]['DL3']        = ""; // เลื่อนส่งครั้งที่ 2
		$arrCol[0]['KBIRecive']  = ""; // ประมาณการสินค้าถึง KBI
		$arrCol[0]['SaleDate']   = ""; // วันที่สินค้าพร้อมขาย
		$arrCol[0]['InQty']      = ""; // จำนวนที่เข้าในล็อตถัดไป(pcs)
		$arrCol[0]['TotalQty']   = ""; // จำนวนสั่งซื้อทั้งหมด (pcs)
		$arrCol[0]['Remark']     = ""; // หมายเหตุ
	}
}

if($_GET['a'] == 'ViewData') {
	$ItemCode = $_POST['ItemCode'];
	$SQL = "SELECT ItemCode, ItemName, CodeBars, SalUnitMsr, DfltWH FROM OITM WHERE ItemCode = '$ItemCode'";
	$QRY = SAPSelect($SQL);
	$result = odbc_fetch_array($QRY);
	$arrCol['ItemCode'] = $result['ItemCode'];
	$arrCol['ItemName'] = conutf8($result['ItemName']);
	$arrCol['CodeBars1'] = $result['CodeBars'];
	$arrCol['CodeBars2'] = "";
	$arrCol['CodeBars3'] = "";
	$arrCol['SalUnitMsr'] = conutf8($result['SalUnitMsr']);
	$arrCol['DfltWH'] = $result['DfltWH'];
	$ChkRow = CHKRowDB("SELECT * FROM oitm WHERE ItemCode = '$ItemCode'");
	if($ChkRow == 0) {
		$arrCol['AddItem'] = "<a href='javascript:void(0);' onclick=\"AddItem('".$ItemCode."')\"><i class='fa fa-upload'></i> เพิ่มข้อมูล</a>";
	}else{
		$arrCol['AddItem'] = "<i class='fas fa-check fa-fw fa-1x'></i>";
	}
}

if($_GET['a'] == "CallData4") {
	$SQL = 
		"SELECT
			T0.DocEntry,
			T1.ItemCode, T1.Dscription, T3.U_ProductStatus, T0.DocDate, T0.DocDueDate, T2.BeginStr+CAST(T0.DocNum AS VARCHAR) AS 'DocNum',
			T1.Quantity, T1.unitMsr, T1.WhsCode, T1.U_MT1, T1.U_MT2, T1.U_TT2, T1.U_OUL, T1.U_ONL
		FROM OPOR T0
		LEFT JOIN POR1 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
		LEFT JOIN OITM T3 ON T1.ItemCode = T3.ItemCode
		WHERE T0.DocStatus = 'O' AND T1.LineStatus = 'O' AND T0.CANCELED = 'N' AND T1.ItemCode IS NOT NULL
		ORDER BY T1.ItemCode, T0.DocDueDate ASC";
	$QRY = SAPSelect($SQL);
	$r = 0;
	while($RST = odbc_fetch_array($QRY)) {
		$arrCol[$r]['No'] = $r+1;
		$arrCol[$r]['ItemCode'] = $RST['ItemCode'];
		$arrCol[$r]['Dscription'] = conutf8($RST['Dscription']);
		$arrCol[$r]['U_ProductStatus'] = $RST['U_ProductStatus'];
		$arrCol[$r]['DocDate'] = ($RST['DocDate'] == '') ? "-" : date("d/m/Y", strtotime($RST['DocDate']));
		$arrCol[$r]['DocDueDate'] = ($RST['DocDueDate'] == '') ? "-" : date("d/m/Y", strtotime($RST['DocDueDate']));
		$arrCol[$r]['DocNum'] = $RST['DocNum'];
		$arrCol[$r]['Quantity'] = number_format($RST['Quantity'],0);
		$arrCol[$r]['unitMsr'] = conutf8($RST['unitMsr']);
		$arrCol[$r]['WhsCode'] = $RST['WhsCode'];
		$arrCol[$r]['U_MT1'] = number_format($RST['U_MT1'],0);
		$arrCol[$r]['U_MT2'] = number_format($RST['U_MT2'],0);
		$arrCol[$r]['U_TT2'] = number_format($RST['U_TT2'],0);
		$arrCol[$r]['U_OUL'] = number_format($RST['U_OUL'],0);
		$arrCol[$r]['U_ONL'] = number_format($RST['U_ONL'],0);
		$r++;
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
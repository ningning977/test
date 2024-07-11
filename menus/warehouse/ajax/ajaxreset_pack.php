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

if($_GET['a'] == 'GetListItem') {
	$DocNum = $_POST['DocNum'];
	$Status = "No Data";

	$SQL1 = "SELECT T0.ID, T0.IDPick, T0.BillEntry, T0.BillType FROM pack_header T0 WHERE T0.DocNum = '$DocNum'";
	if(CHKRowDB($SQL1) != 0) {
		$Status = "Success";
		$RST1 = MySQLSelect($SQL1);
		$HeadID = $RST1['ID'];
		$IDPick = $RST1['IDPick'];
		$BillEntry = $RST1['BillEntry'];
		$BillType = $RST1['BillType'];

		$DataOld = "";
		$DataNew = "";
		$SQL2 = "SELECT * FROM pack_list WHERE HeadID = $HeadID";
		if(CHKRowDB($SQL2) != 0) {
			$QRY2 = MySQLSelectX($SQL2);
			$ArrItemCode = array();
			$r = 0;
			while($RST2 = mysqli_fetch_array($QRY2)) {
				$r++;
				$DataOld .= 
				"<tr>
					<td class='text-center'>$r</td>
					<td class='text-center'>".$RST2['ItemCode']."</td>
					<td>".$RST2['ItemName']."</td>
					<td class='text-center'>".$RST2['WhsCode']."</td>
					<td class='text-right'>".$RST2['Qty']."</td>
					<td class='text-right table-danger'>".$RST2['OpenQty']."</td>
				</tr>";

				$DataNew .= 
				"<tr>
					<td class='text-center'>$r</td>
					<td class='text-center'>".$RST2['ItemCode']."</td>
					<td>".$RST2['ItemName']."</td>
					<td class='text-center'>".$RST2['WhsCode']."</td>
					<td class='text-right'>".$RST2['Qty']."</td>
					<td class='text-right table-success'>0</td>
				</tr>";
				array_push($ArrItemCode, $RST2['ItemCode']);
			}

			$SQL3 = 
				"SELECT (T1.VisOrder-1) AS VisOrder, T1.ItemCode, T1.BarCode, T1.ItemName, T1.WhsCode, T1.OpenQty AS Qty, T1.Remark AS Comments 
				FROM picker_soheader T0 
				LEFT JOIN picker_sodetail T1 ON T0.SODocEntry = T1.DocEntry AND T0.DocType = T1.DocType 
				WHERE T0.ID = $IDPick AND T1.Status NOT IN (3)";
			$QRY3 = MySQLSelectX($SQL3);
			while($RST3 = mysqli_fetch_array($QRY3)) {
				if(array_search($RST3['ItemCode'],$ArrItemCode) === false) {
					$r++;
					$DataNew .= 
					"<tr class='table-success'>
						<td class='text-center'>$r</td>
						<td class='text-center'>".$RST3['ItemCode']."</td>
						<td>".$RST3['ItemName']."</td>
						<td class='text-center'>".$RST3['WhsCode']."</td>
						<td class='text-right'>".$RST3['Qty']."</td>
						<td class='text-right'>0</td>
					</tr>";
				}
			}
		}else{
			$DataOld = "<tr><td colspan='6' class='text-center'>ยังไม่มีรายการ Pack กรุณาเข้าระบบ Pack ก่อน :(</td></tr>";
			$DataNew = "<tr><td colspan='6' class='text-center'>ยังไม่มีรายการ Pack กรุณาเข้าระบบ Pack ก่อน :(</td></tr>";
		}
		
		$arrCol['DataOld'] = $DataOld;
		$arrCol['DataNew'] = $DataNew;
	}
	$arrCol['Status'] = $Status;
}

if($_GET['a'] == 'RePack') {
	$DocNum = $_POST['DocNum'];
	$Status = "No Success";

	$SQL1 = "SELECT T0.ID, T0.IDPick, T0.BillEntry, T0.BillType FROM pack_header T0 WHERE T0.DocNum = '$DocNum'";
	if(CHKRowDB($SQL1) != 0) {
		$Status = "Success";
		$RST1 = MySQLSelect($SQL1);
		$HeadID = $RST1['ID'];
		$IDPick = $RST1['IDPick'];
		$BillEntry = $RST1['BillEntry'];
		$BillType = $RST1['BillType'];

		MySQLDelete("DELETE FROM pack_list WHERE HeadID = $HeadID");
		MySQLDelete("DELETE FROM pack_boxlist WHERE BillEntry = $BillEntry AND BillType = '$BillType'");
		MySQLDelete("DELETE FROM pack_tran WHERE BillEntry = $BillEntry AND BillType = '$BillType'");

		if($BillType == 'OWAS' || $BillType == 'OWAB') {
			$SQL2 = 
				"SELECT (T1.VisOrder-1) AS VisOrder, T1.ItemCode, T1.BarCode, T1.ItemName, T1.WhsCode, T2.UnitMgr AS UnitMsr, T1.OpenQty AS Qty, T1.Remark AS Comments 
				FROM picker_soheader T0 
				LEFT JOIN picker_sodetail T1 ON T0.SODocEntry = T1.DocEntry AND T0.DocType = T1.DocType 
				LEFT JOIN was1 T2 ON T0.SoDocEntry = T2.DocEntry AND T1.VisOrder = T2.lnNum 
				WHERE T0.ID = $IDPick AND T1.Status NOT IN (3)";
			$QRY2 = MySQLSelectX($SQL2);
			$LineNum = 0;
			while($RST2 = mysqli_fetch_array($QRY2)) {
				$LineNum++;
				$VisOrder = $RST2['VisOrder'];
				$ItemCode = $RST2['ItemCode'];
				$BarCode = $RST2['BarCode'];
				$ItemName = $RST2['ItemName'];
				$WhsCode = $RST2['WhsCode'];
				$Unit = $RST2['UnitMsr'];
				$Qty = $RST2['Qty'];
				$Comments = $RST2['Comments'];

				$InsertPackList = 
					"INSERT INTO pack_list 
					SET HeadID = $HeadID, 
						VisOrder = '$VisOrder',
						LineNum = '$LineNum', 
						ItemCode = '$ItemCode', 
						BarCode = '$BarCode', 
						ItemName = '$ItemName', 
						WhsCode = '$WhsCode', 
						Unit = '$Unit', 
						Qty = $Qty, 
						OpenQty = 0, 
						Comments = '$Comments'";
				MySQLInsert($InsertPackList);

				$ChkItemBoom = 
					"SELECT T1.ItemCode, T2.BarCode, T2.ItemName, IFNULL(T2.MgrUnit, 'EA') AS UnitMsr 
					FROM oitm T0 
					LEFT JOIN bomgroup T1 ON T0.BomGroup = T1.BomGroup AND T0.ItemCode = T1.ItemCode 
					LEFT JOIN oitm T2 ON T1.ItemCode = T2.ItemCode 
					WHERE T0.ItemCode LIKE '$ItemCode-%' AND T1.ItemStatus = 'A'";
				$QRY3 = MySQLSelectX($ChkItemBoom);
				while($RST3 = mysqli_fetch_array($QRY3)) {
					$LineNum++;
					$BomItemCode = $RST3['ItemCode'];
					$BomBarCode = $RST3['BarCode'];
					$BomItemName = $RST3['ItemName'];
					$BomUnit = $RST3['UnitMsr'];

					$InsertPackList2 = 
						"INSERT INTO pack_list 
						SET HeadID = $HeadID, 
							VisOrder = '$VisOrder', 
							LineNum = '$LineNum', 
							ItemCode = '$BomItemCode', 
							BarCode = '$BomBarCode', 
							ItemName = '$BomItemName', 
							WhsCode = '$WhsCode', 
							Unit = '$BomUnit', 
							Qty = '$Qty', 
							OpenQty = 0, 
							Comments = ''";
					MySQLInsert($InsertPackList2);
				}
			}
		}else{
			$BillTable = substr($BillType,1,3).'1';
			$SQL4 = "SELECT T0.VisOrder, T0.ItemCode, T0.Dscription AS 'ItemName', T0.CodeBars, T0.Dscription, T0.WhsCode, T0.unitMsr, T0.Quantity FROM $BillTable T0 WHERE T0.DocEntry = '$BillEntry'";
			$QRY4 = SAPSelect($SQL4);
			$LineNum = 0;
			while($RST4 = odbc_fetch_array($QRY4)) {
				$LineNum++;
				$VisOrder = $RST4['VisOrder'];
				$ItemCode = $RST4['ItemCode'];
				$BarCode = $RST4['CodeBars'];
				$ItemName = conutf8($RST4['ItemName']);
				$WhsCode = $RST4['WhsCode'];
				$Unit = $RST4['unitMsr'];
				$Qty = $RST4['Quantity'];

				$SQLRemart = "SELECT T0.Remark FROM picker_sodetail T0 LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry AND T0.DocType = T1.DocType WHERE T1.ID = $IDPick AND T0.VisOrder = $VisOrder";
				$GetRemart = MySQLSelect($SQLRemart);
				$Comments = $GetRemart['Remark'];

				$InsertPackList3 = 
					"INSERT INTO pack_list 
					SET HeadID = $HeadID, 
						VisOrder = '$VisOrder',
						LineNum = '$LineNum', 
						ItemCode = '$ItemCode', 
						BarCode = '$BarCode', 
						ItemName = '$ItemName', 
						WhsCode = '$WhsCode', 
						Unit = '$Unit', 
						Qty = $Qty, 
						OpenQty = 0, 
						Comments = '$Comments'";
				MySQLInsert($InsertPackList3);

				$ChkItemBoom2 = 
					"SELECT T1.ItemCode, T2.BarCode, T2.ItemName, IFNULL(T2.MgrUnit, 'EA') AS UnitMsr 
					FROM oitm T0 
					LEFT JOIN bomgroup T1 ON T0.BomGroup = T1.BomGroup AND T0.ItemCode = T1.ItemCode 
					LEFT JOIN oitm T2 ON T1.ItemCode = T2.ItemCode 
					WHERE T0.ItemCode LIKE '$ItemCode-%' AND T1.ItemStatus = 'A'";
				$QRY5 = MySQLSelectX($ChkItemBoom2);
				while($RST5 = mysqli_fetch_array($QRY5)) {
					$LineNum++;
					$BomItemCode = $RST5['ItemCode'];
					$BomBarCode = $RST5['BarCode'];
					$BomItemName = $RST5['ItemName'];
					$BomUnit = $RST5['UnitMsr'];

					$InsertPackList4 = 
						"INSERT INTO pack_list 
						SET HeadID = $HeadID, 
							VisOrder = '$VisOrder', 
							LineNum = '$LineNum', 
							ItemCode = '$BomItemCode', 
							BarCode = '$BomBarCode', 
							ItemName = '$BomItemName', 
							WhsCode = '$WhsCode', 
							Unit = '$BomUnit', 
							Qty = '$Qty', 
							OpenQty = 0, 
							Comments = ''";
					MySQLInsert($InsertPackList4);
				}
			}
		}
	}

	$arrCol['Status'] = $Status;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
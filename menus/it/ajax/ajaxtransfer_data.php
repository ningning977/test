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

if($_GET['a'] == 'GetDataSO') {
	$OldSO = $_POST['OldSO'];
	$NewSO = $_POST['NewSO'];
	$Error = 0;
	$wai=0;
	$aum=0;

	$SQL_Old = MySQLSelect("SELECT SODocEntry AS DocEntry FROM picker_soheader WHERE DocNum = '$OldSO' AND DocType = 'ORDR'");
	$Old_DocEntry = (isset($SQL_Old['DocEntry'])) ? $SQL_Old['DocEntry'] : "";

	$SQL_New = MySQLSelect("SELECT SODocEntry AS DocEntry FROM picker_soheader WHERE DocNum = '$NewSO' AND DocType = 'ORDR'");
	$New_DocEntry = (isset($SQL_New['DocEntry'])) ? $SQL_New['DocEntry'] : "";

	if($Old_DocEntry != "" && $New_DocEntry != "") {
		$DeleteDetail_New = "DELETE FROM picker_sodetail WHERE DocEntry = '$New_DocEntry' AND DocType = 'ORDR'";
		MySQLDelete($DeleteDetail_New);

		$SQL1 = "SELECT * FROM picker_sodetail WHERE DocEntry = '$Old_DocEntry' AND DocType = 'ORDR'";
		$QRY1 = MySQLSelectX($SQL1);
		$ArrInsert = array();
		$TbodyOld = "";
		while ($RST1 = mysqli_fetch_array($QRY1)){
			$SQL2 = "SELECT * FROM RDR1 WHERE DocEntry = ".$New_DocEntry." AND VisOrder = ".$RST1['VisOrder']." AND ItemCode = '".$RST1['ItemCode']."' AND Quantity = ".$RST1['Qty'];
			$QRY2 = SAPSelect($SQL2);
			$RST2 = odbc_fetch_array($QRY2);
			if(isset($RST2['DocEntry'])) {
				$Color = "table-success";
				$Insert_New = "
					INSERT INTO picker_sodetail 
						(DocEntry, DocType, VisOrder, ItemCode, BarCode, ItemName, WhsCode, Qty, OpenQty, Remark, Status, WaitOP, BomItem, LastRead) 
					SELECT 
						$New_DocEntry, DocType, VisOrder, ItemCode, BarCode, ItemName, WhsCode, Qty, OpenQty, Remark, Status, WaitOP, BomItem, LastRead
					FROM picker_sodetail WHERE ID = ".$RST1['ID']."";
				array_push($ArrInsert, $Insert_New);
			}else{
				$Color = "table-danger";
				$Error++; $Msg = "ข้อมูลไม่ถูกต้อง ไม่สามารถนำเข้าได้";
			}
	
			$TbodyOld .= "
			<tr class='$Color'>
				<td class='text-center'>".$RST1['DocEntry']."</td>
				<td class='text-center'>".$RST1['VisOrder']."</td>
				<td class='text-center'>".$RST1['ItemCode']."</td>
				<td>".$RST1['ItemName']."</td>
				<td class='text-right'>".$RST1['Qty']."</td>
			</tr>";
		}
	
		$SQL3 = "SELECT * FROM picker_sodetail WHERE DocEntry = '$New_DocEntry' AND DocType = 'ORDR'";
		$QRY3 = MySQLSelectX($SQL3);
		$TbodyNew = "";
		while ($RST3 = mysqli_fetch_array($QRY3)){
			$TbodyNew .= "
			<tr class=''>
				<td class='text-center'>".$RST3['DocEntry']."</td>
				<td class='text-center'>".$RST3['VisOrder']."</td>
				<td class='text-center'>".$RST3['ItemCode']."</td>
				<td>".$RST3['ItemName']."</td>
				<td class='text-right'>".$RST3['Qty']."</td>
			</tr>";
		}
	}else{
		$TbodyOld = "<tr><td colspan='5' class='text-center'>SO ไม่มีข้อมูล :(</td></tr>";
		$TbodyNew = "<tr><td colspan='5' class='text-center'>SO ไม่มีข้อมูล :(</td></tr>";
		$Error++; $Msg = "ไม่มีข้อมูล SO";
	}

	if($Error == 0){
		foreach($ArrInsert as $QRY) {
			MySQLInsert($QRY);
		}
		$Msg = "นำเข้าข้อมูลสำเร็จ";
	}

	$arrCol['TbodyOld'] = $TbodyOld;
	$arrCol['TbodyNew'] = $TbodyNew;
	$arrCol['errMsg'] = $Msg;
	$arrCol['errCode'] = $Error;
}

if($_GET['a'] == 'GetDataPack') {
	$Old = $_POST['Old'];
	$New = $_POST['New'];
	$DocType = $_POST['DocType'];
	$TbodyOld = "";
	$TbodyNew = "";
	$Error = 0; $Msg = "";
	$SQL1 = 
		"SELECT T0.[BaseEntry] AS [SoEntry], T0.[BaseDocNum] AS [SoDocNum], T1.[DocEntry] AS [BillEntry], T1.[DocNum] AS [BillDocNum]
		FROM ".substr($DocType, 1)."1 T0
		LEFT JOIN $DocType T1 ON T0.[DocEntry] = T1.[DocEntry]
		WHERE T1.[DocNum] IN (".substr($Old, 3).", ".substr($New, 3).") AND T0.[BaseEntry] IS NOT NULL
		ORDER BY T1.[DocEntry], T0.[BaseEntry] ASC";
	switch (ChkRowSAP($SQL1)) {
		case 0: $Error++; $Msg = "ไม่สามารถโอนย้ายได้ : ไม่มีข้อมูล IV นี้"; break;
		case 1: $Error++; $Msg = "ไม่สามารถโอนย้ายได้ : เจอข้อมูล IV แค่ 1 ตัว"; break;
		default:
			$Msg = "นำเข้าข้อมูลสำเร็จ";
			$QRY1 = SAPSelect($SQL1);
			$tmpRow = 0;
			while($RST1 = odbc_fetch_array($QRY1)) {
				$tmpRow++;
				if($tmpRow == 1) {
					$OLD_SOEntry = $RST1['SoEntry'];
					$OLD_SODocNum = $RST1['SoDocNum'];
					$OLD_BillEntry = $RST1['BillEntry'];
					$OLD_BIllDocNum = $RST1['BillDocNum'];
				}else{
					$NEW_SOEntry = $RST1['SoEntry'];
					$NEW_SODocNum = $RST1['SoDocNum'];
					$NEW_BillEntry = $RST1['BillEntry'];
					$NEW_BIllDovNum = $RST1['BillDocNum'];
				}
			}
			$SQL_Old = 
				"SELECT T2.BillEntry, T0.VisOrder, T0.VisOrder, T0.ItemCode, T0.ItemName, T0.Qty
					FROM picker_sodetail T0
					LEFT JOIN picker_soheader T1 ON T1.SODocEntry = T0.DocEntry
					LEFT JOIN pack_header T2 ON T2.IDPick = T1.ID 
					WHERE T2.BillEntry = $OLD_BillEntry AND T2.BillType = '$DocType'";
			$QRY_Old = MySQLSelectX($SQL_Old);
			while($RST_Old = mysqli_fetch_array($QRY_Old)) {
				$TbodyOld .= "
				<tr class=''>
					<td class='text-center'>".$RST_Old['BillEntry']."</td>
					<td class='text-center'>".$RST_Old['VisOrder']."</td>
					<td class='text-center'>".$RST_Old['ItemCode']."</td>
					<td>".$RST_Old['ItemName']."</td>
					<td class='text-right'>".$RST_Old['Qty']."</td>
				</tr>";
			}
			if($OLD_SOEntry == $NEW_SOEntry) {
				$SQL2 = "SELECT T0.ID FROM pack_header T0 WHERE T0.BillEntry = '$OLD_BillEntry' AND T0.BillType = '$DocType'";
				if(CHKRowDB($SQL2) == 0) {
					$Error++; $Msg = "ไม่สามารถโอนย้ายได้ : ไม่พบงานแพ็คของเลขที่บิลเก่า";
				}else{
					$UPDATE1 = "UPDATE pack_header SET BillEntry = '$NEW_BillEntry', DocNum = '$New', Status = 0 WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
					MySQLUpdate($UPDATE1);
					$UPDATE2 = "UPDATE pack_boxlist SET BillEntry = '$NEW_BillEntry', Status = 'O' WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
					MySQLUpdate($UPDATE2);
					$UPDATE3 = "UPDATE pack_tran SET BillEntry = '$NEW_BillEntry', Status = '1' WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
					MySQLUpdate($UPDATE3);
					$UPDATE4 = "UPDATE picker_soheader SET StatusDoc = 10, LastUkey = '".$_SESSION['ukey']."', LastUpdate = NOW() WHERE DocNum LIKE '%".$OLD_SODocNum."' AND DocType = 'ORDR'";
					MySQLUpdate($UPDATE4);
				}
			}else{
				$SQL4 = 
					"SELECT T0.IDPick, T0.CardCode, T0.Comment, T0.TotalPack, T0.TablePack, T0.uKeyCreate1, T0.uKeyCreate2 
					FROM pack_header T0 
					WHERE T0.BillEntry = '$OLD_BillEntry' AND T0.BillType = '$DocType'";
				if(CHKRowDB($SQL4) == 0) {
					$Error++; $Msg = "ไม่สามารถโอนย้ายได้ : ไม่พบงานแพ็คของเลขที่บิลเก่า";
				}else{
					$QRY4 = SAPSelect($SQL4);
					$RST4 = odbc_fetch_array($QRY4);
					$OLD_IDPick = $RST4['IDPick'];
					$HD_CardCode = $RST4['CardCode'];
					$HD_Comment = $RST4['Comment'];
					$HD_TotalPack = $RST4['TotalPack'];
					$HD_TablePack = $RST4['TablePack'];
					$HD_uKey1 = $RST4['uKeyCreate1'];
					$HD_uKey2 = $RST4['uKeyCreate2'];

					$SQL5 = "SELECT T0.ID FROM picker_soheader T0 WHERE T0.SODocEntry = '$NEW_SOEntry' AND T0.DocType = 'ORDR'";
					if(CHKRowDB($SQL5) == 0) { 
						$Error++; $Msg = "ไม่สามารถโอนย้ายได้<br>ไม่พบงานเบิกสินค้าของ S/O ใหม่ ให้จัดสรรคำสั่งขายเลขที่ $NEW_SODocNum เข้าระบบ และดำเนินการโอนย้าย SO";
					}else{
						$QRY5 = SAPSelect($SQL5);
						$RST5 = odbc_fetch_array($QRY5);
						$NEW_IDPick = $RST5['ID'];

						// ยกเลิกการแพ็คกับบิลเก่า
						$UPDATE5 = "UPDATE pack_header SET Status = 'Y', DateFinish = NOW() WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
						// MySQLUpdate($UPDATE5);
						// เพิ่มข้อมูลการแพ็กใหม่ที่ pack_header
						$INSERT1 = 
							"INSERT INTO pack_header 
							SET IDPick = $NEW_IDPick, 
								BillEntry = '$NEW_BillEntry', 
								BillType = '$DocType', 
								DocNum = '$New', 
								Comment = '$HD_Comment', 
								TotalPack = $HD_TotalPack, 
								TablePack = $HD_TablePack, 
								uKeyCreate1 = '$HD_uKey1', 
								uKeyCreate2 = '$HD_uKey2' 
								DateCreate = NOW(), 
								Status = '0', 
								Logi = 'N'";
						// MySQLInsert($INSERT1);
						$UPDATE6 = "UPDATE pack_boxlist SET BillEntry = '$NEW_BillEntry', Status = 'O' WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
						// MySQLUpdate($UPDATE6);
						$UPDATE7 = "UPDATE pack_tran SET BillEntry = '$NEW_BillEntry', Status = '1' WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
						// MySQLUpdate($UPDATE7);
						$UPDATE8 = "UPDATE picker_soheader SET StatusDoc = 0 LastUkey = '".$_SESSION['ukey']."', LastUpdate = NOW() WHERE ID = '$OLD_IDPick'";
						// MySQLUpdate($UPDATE8);
						$UPDATE9 = "UPDATE picker_soheader SET StatusDoc = 10, LastUkey = '".$_SESSION['ukey']."', LastUpdate = NOW() WHERE ID = '$NEW_IDPick'";
						// MySQLUpdate($UPDATE9);
					}
				}
			}
		break;
	}

	if($Error == 0) {
		$SQL_New = 
			"SELECT T2.BillEntry, T0.VisOrder, T0.VisOrder, T0.ItemCode, T0.ItemName, T0.Qty
			FROM picker_sodetail T0
			LEFT JOIN picker_soheader T1 ON T1.SODocEntry = T0.DocEntry
			LEFT JOIN pack_header T2 ON T2.IDPick = T1.ID 
			WHERE T2.BillEntry = $NEW_BillEntry AND T2.BillType = '$DocType'";
		$QRY_New = MySQLSelectX($SQL_New);
		while($RST_New = mysqli_fetch_array($QRY_New)) {
			$TbodyNew .= "
			<tr class=''>
				<td class='text-center'>".$RST_New['BillEntry']."</td>
				<td class='text-center'>".$RST_New['VisOrder']."</td>
				<td class='text-center'>".$RST_New['ItemCode']."</td>
				<td>".$RST_New['ItemName']."</td>
				<td class='text-right'>".$RST_New['Qty']."</td>
			</tr>";
		}
	}else{
		$TbodyOld = ($TbodyOld == '') ? "<tr><td colspan='5' class='text-center'>IV ไม่มีข้อมูล :(</td></tr>" : $TbodyOld;
		$TbodyNew = "<tr><td colspan='5' class='text-center'>IV ไม่มีข้อมูล :(</td></tr>";
	}

	$arrCol['TbodyOld'] = $TbodyOld;
	$arrCol['TbodyNew'] = $TbodyNew;
	$arrCol['errMsg'] = $Msg;
	$arrCol['errCode'] = $Error;
}

if($_GET['a'] == 'CheckDataIV') {
	$Old = $_POST['Old'];
	$New = $_POST['New'];
	$DocType = $_POST['DocType'];
	$Error = 0; $Msg = "";
	$TbodyPackHeaderOld = ""; $TbodyPackHeaderNew = "";
	$TbodyPackBoxlistOld =""; $TbodyPackBoxlistNew ="";
	$TbodyPackTranOld =""; $TbodyPackTranNew ="";
	$TbodyPickerSoheaderOld = ""; $TbodyPickerSoheaderNew = "";
	$SQL1 = 
		"SELECT T0.[BaseEntry] AS [SoEntry], T0.[BaseDocNum] AS [SoDocNum], T1.[DocEntry] AS [BillEntry], T1.[DocNum] AS [BillDocNum]
		FROM ".substr($DocType, 1)."1 T0
		LEFT JOIN $DocType T1 ON T0.[DocEntry] = T1.[DocEntry]
		WHERE T1.[DocNum] IN (".substr($Old, 3).", ".substr($New, 3).") AND T0.[BaseEntry] IS NOT NULL
		ORDER BY T1.[DocEntry], T0.[BaseEntry] ASC";
	switch (ChkRowSAP($SQL1)) {
		case 0: $Error++; $Msg = "ไม่มีข้อมูล IV นี้"; break;
		case 1: $Error++; $Msg = "เจอข้อมูล IV แค่ 1 ตัว"; break;
		default: 
			$QRY1 = SAPSelect($SQL1);
			$tmpRow = 0;
			while($RST1 = odbc_fetch_array($QRY1)) {
				$tmpRow++;
				if($tmpRow == 1) {
					$OLD_SOEntry = $RST1['SoEntry'];
					$OLD_SODocNum = $RST1['SoDocNum'];
					$OLD_BillEntry = $RST1['BillEntry'];
					$OLD_BIllDocNum = $RST1['BillDocNum'];
				}else{
					$NEW_SOEntry = $RST1['SoEntry'];
					$NEW_SODocNum = $RST1['SoDocNum'];
					$NEW_BillEntry = $RST1['BillEntry'];
					$NEW_BIllDovNum = $RST1['BillDocNum'];
				}
			}

			if($OLD_SOEntry == $NEW_SOEntry) {
				$SQL2 = "SELECT T0.ID FROM pack_header T0 WHERE T0.BillEntry = '$OLD_BillEntry' AND T0.BillType = '$DocType'";
				if(CHKRowDB($SQL2) == 0) {
					$Error++; $Msg = "ไม่พบงานแพ็คของเลขที่บิลเก่า";
				}else{
					// pack_header
						$SQL1 = "SELECT * FROM pack_header WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
						$QRY1 = MySQLSelectX($SQL1);
						while($RST1 = mysqli_fetch_array($QRY1)) {
							$TbodyPackHeaderOld .= 
							"<tr>
								<td class='text-right'>".$RST1['ID']."</td>
								<td class='text-right'>".$RST1['IDPick']."</td>
								<td class='text-right'>".$RST1['BillEntry']."</td>
								<td class='text-center'>".$RST1['BillType']."</td>
								<td class='text-center'>".$RST1['DocNum']."</td>
								<td class='text-center'>".$RST1['CardCode']."</td>
							</tr>";
						}
						$SQL1 = "SELECT * FROM pack_header WHERE BillEntry = '$NEW_BillEntry' AND BillType = '$DocType'";
						$QRY1 = MySQLSelectX($SQL1);
						while($RST1 = mysqli_fetch_array($QRY1)) {
							$TbodyPackHeaderNew .= 
							"<tr>
								<td class='text-right'>".$RST1['ID']."</td>
								<td class='text-right'>".$RST1['IDPick']."</td>
								<td class='text-right'>".$RST1['BillEntry']."</td>
								<td class='text-center'>".$RST1['BillType']."</td>
								<td class='text-center'>".$RST1['DocNum']."</td>
								<td class='text-center'>".$RST1['CardCode']."</td>
							</tr>";
						}

					// pack_boxlist
						$SQL2 = "SELECT * FROM pack_boxlist WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
						$QRY2 = MySQLSelectX($SQL2);
						while($RST2 = mysqli_fetch_array($QRY2)) {
							$TbodyPackBoxlistOld .= 
							"<tr>
								<td class='text-right'>".$RST2['ID']."</td>
								<td>".$RST2['Retails']."</td>
								<td class='text-right'>".$RST2['BillEntry']."</td>
								<td class='text-right'>".$RST2['BillType']."</td>
								<td>".$RST2['BoxCode']."</td>
								<td class='text-right'>".$RST2['BoxNo']."</td>
							</tr>";
						}
						$SQL2 = "SELECT * FROM pack_boxlist WHERE BillEntry = '$NEW_BillEntry' AND BillType = '$DocType'";
						$QRY2 = MySQLSelectX($SQL2);
						while($RST2 = mysqli_fetch_array($QRY2)) {
							$TbodyPackBoxlistNew .= 
							"<tr>
								<td class='text-right'>".$RST2['ID']."</td>
								<td>".$RST2['Retails']."</td>
								<td class='text-right'>".$RST2['BillEntry']."</td>
								<td class='text-right'>".$RST2['BillType']."</td>
								<td>".$RST2['BoxCode']."</td>
								<td class='text-right'>".$RST2['BoxNo']."</td>
							</tr>";
						}

					// pack_tran
						$SQL3 = "SELECT * FROM pack_tran WHERE BillEntry = '$OLD_BillEntry' AND BillType = '$DocType'";
						$QRY3 = MySQLSelectX($SQL3);
						while($RST3 = mysqli_fetch_array($QRY3)) {
							$TbodyPackTranOld .= 
							"<tr>
								<td class='text-right'>".$RST3['ID']."</td>
								<td class='text-right'>".$RST3['BillEntry']."</td>
								<td class='text-center'>".$RST3['BillType']."</td>
								<td class='text-center'>".$RST3['BoxCode']."</td>
								<td class='text-right'>".$RST3['BoxNo']."</td>
								<td class='text-center'>".$RST3['ItemCode']."</td>
							</tr>";
						}
						$SQL3 = "SELECT * FROM pack_tran WHERE BillEntry = '$NEW_BillEntry' AND BillType = '$DocType'";
						$QRY3 = MySQLSelectX($SQL3);
						while($RST3 = mysqli_fetch_array($QRY3)) {
							$TbodyPackTranNew .= 
							"<tr>
								<td class='text-right'>".$RST3['ID']."</td>
								<td class='text-right'>".$RST3['BillEntry']."</td>
								<td class='text-center'>".$RST3['BillType']."</td>
								<td class='text-center'>".$RST3['BoxCode']."</td>
								<td class='text-right'>".$RST3['BoxNo']."</td>
								<td class='text-center'>".$RST3['ItemCode']."</td>
							</tr>";
						}

					// picker_soheader
						$SQL4 = "SELECT * FROM picker_soheader WHERE DocNum LIKE '%".$OLD_SODocNum."' AND DocType = 'ORDR'";
						$RST4 = MySQLSelect($SQL4);
						$TbodyPickerSoheaderOld .=
						"<tr>
							<td>".$RST4['ID']."</td>
							<td>".$RST4['SODocEntry']."</td>
							<td>".$RST4['DocNum']."</td>
							<td>".$RST4['DocType']."</td>
							<td>".$RST4['CardCode']." | ".$RST4['CardName']."</td>
							<td>".$RST4['StatusDoc']."</td>
						</tr>";

						$SQL4 = "SELECT * FROM picker_soheader WHERE DocNum LIKE '%".$NEW_SODocNum."' AND DocType = 'ORDR'";
						$RST4 = MySQLSelect($SQL4);
						$TbodyPickerSoheaderNew .=
						"<tr>
							<td>".$RST4['ID']."</td>
							<td>".$RST4['SODocEntry']."</td>
							<td>".$RST4['DocNum']."</td>
							<td>".$RST4['DocType']."</td>
							<td>".$RST4['CardCode']." | ".$RST4['CardName']."</td>
							<td>".$RST4['StatusDoc']."</td>
						</tr>";
				}
			}else{

			}
		break;
	}
	$arrCol['TbodyPackHeaderOld'] = $TbodyPackHeaderOld;
	$arrCol['TbodyPackHeaderNew'] = $TbodyPackHeaderNew;
	$arrCol['TbodyPackBoxlistOld'] = $TbodyPackBoxlistOld;
	$arrCol['TbodyPackBoxlistNew'] = $TbodyPackBoxlistNew;
	$arrCol['TbodyPackTranOld'] = $TbodyPackTranOld;
	$arrCol['TbodyPackTranNew'] = $TbodyPackTranNew;
	$arrCol['TbodyPickerSoheaderOld'] = $TbodyPickerSoheaderOld;
	$arrCol['TbodyPickerSoheaderNew'] = $TbodyPickerSoheaderNew;
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
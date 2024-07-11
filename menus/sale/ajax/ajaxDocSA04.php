<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";

function DocTypeName($DocType) {
	switch($DocType) {
		case "A": $text = "ลดหนี้/ลดจ่ายทั้งบิล"; break;
		case "B": $text = "ลดหนี้/ลดจ่ายเฉพาะรายการ"; break;
		case "C": $text = "ลดหนี้/ลดจ่ายค่าขนส่ง"; break;
	}
	return $text;
}

function DocRemarkName($DocRemark) {
	switch($DocRemark) {
		case 1: $text = "เซลส์เสนอราคาผิด"; break;
		case 2: $text = "ลูกค้าขอราคาเดิม"; break;
		case 3: $text = "คู่แข่งขายถูกกว่า"; break;
		case 4: $text = "อื่น ๆ"; break;
	}
	return $text;
}

if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

if($_GET['p'] == "GetCardCode") {

	$CustSQL = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE T0.CardType = 'C' AND T0.CardStatus = 'A'";
	$CustQRY = MySQLSelectX($CustSQL);
	$output .= "<option value='' selected disabled>กรุณาเลือกลูกค้า</option>";
	while($CustRST = mysqli_fetch_array($CustQRY)) {
		$output .= "<option value='".$CustRST['CardCode']."'>".$CustRST['CardCode']." | ".$CustRST['CardName']."</option>";
	}
	$arrCol['output'] = $output;
}

if($_GET['p'] == "GetRefDoc") {
	$CardCode  = $_POST['c'];
	$DocType   = $_POST['t'];
	$Searchbox = $_POST['s'];

	if($_POST['v'] == "true") {
		$BillVer = 8;
	} else {
		$BillVer = 10;
	}
	/* IV6512.... // IV6601.... */

	$IVSQL = 
		"SELECT DISTINCT TOP 1
			T0.DocEntry, T0.NumAtCard AS 'DocNum',T0.DocDate, T0.DocDueDate, T0.SlpCode, T2.SlpName, (T0.DocTotal-T0.VatSum) AS 'DocTotal',
			T4.OwnerCode, (T5.lastName+' '+T5.firstname) AS 'CoName'
		FROM OINV T0
		LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
		LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
		LEFT JOIN INV1 T3 ON T0.DocEntry = T3.DocEntry
		LEFT JOIN ORDR T4 ON T3.BaseEntry = T4.DocEntry
		LEFT JOIN OHEM T5 ON T4.OwnerCode = T5.empID
		WHERE (T0.DocNum LIKE '%$Searchbox%' OR T0.NumAtCard LIKE '%$Searchbox%') AND T0.CardCode = '$CardCode'";
	if($BillVer == 8) {
		$Rows = ChkRowSAP8($IVSQL);
	} else {
		$Rows = ChkRowSAP($IVSQL);
	}
	
	if($Rows > 0) {
		if($BillVer == 8) {
			$IVQRY = conSAP8($IVSQL);
		} else {
			$IVQRY = SAPSelect($IVSQL);
		}
		
		
		$IVRST = odbc_fetch_array($IVQRY);

		$arrCol['IVEntry']    = $IVRST['DocEntry'];
		$arrCol['DocNum']     = $IVRST['DocNum'];
		$arrCol['DocDate']    = date("Y-m-d",strtotime($IVRST['DocDate']));
		$arrCol['DocDueDate'] = date("Y-m-d",strtotime($IVRST['DocDueDate']));
		$arrCol['SlpCode']    = $IVRST['SlpCode'];
		$arrCol['SlpName']    = conutf8($IVRST['SlpName']);
		$arrCol['CoCode']     = $IVRST['OwnerCode'];
		$arrCol['CoName']     = conutf8($IVRST['CoName']);
		$arrCol['GetStatus']  = "SUCCESS";

		switch($DocType) {
			case "A":
				$arrCol['DocTotal'] = $IVRST['DocTotal'];
			break;
			case "B":
				$DTSQL = 
					"SELECT
						ISNULL(T2.BeginStr,'IV-') AS 'BeginStr', T1.VisOrder, T1.ItemCode, T1.Dscription, 
						T1.Price, T1.PriceAfVAT, CASE WHEN ISNULL(T2.BeginStr,'IV-') IN ('IV-','IC-') THEN T1.Price ELSE T1.PriceAfVAT END AS 'DocPrice',
						T1.Quantity, T1.UnitMsr
					FROM OINV T0
					LEFT JOIN INV1 T1 ON T0.DocEntry = T1.DocEntry
					LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
					WHERE (T0.DocNum LIKE '%$Searchbox%' OR T0.NumAtCard LIKE '%$Searchbox%') AND T0.CardCode = '$CardCode'
					ORDER BY T1.VisOrder";
				if($BillVer == 8) {
					$Rows  = ChkRowSAP8($DTSQL);
				} else {
					$Rows  = ChkRowSAP($DTSQL);
				}
				
				
				if($Rows > 0) {
					$arrCol['Rows'] = $Rows;
					if($BillVer == 8) {
						$DTQRY = conSAP8($DTSQL);
					} else {
						$DTQRY = SAPSelect($DTSQL);
					}
					
					$l = 0;
					while($DTRST = odbc_fetch_array($DTQRY)) {
						$arrCol['ItemRow_'.$l] = array(
							"VisOrder" => $DTRST['VisOrder'],
							"ItemCode" => $DTRST['ItemCode'],
							"ItemName" => conutf8($DTRST['Dscription']),
							"DocPrice" => $DTRST['DocPrice'],
							"Quantity" => $DTRST['Quantity'],
							"UnitMsr"  => conutf8($DTRST['UnitMsr'])
						);
						$l++;
					}
				} else {
					$arrCol['GetStatus'] = "ERR::NO_RESULT";
				}
			break;
		}
	} else {
		$arrCol['GetStatus'] = "ERR::NO_RESULT";
	}
}

if($_GET['p'] == "GetShipBill") {
	$filt_year  = $_POST['y'];
	$filt_month = $_POST['m'];
	$CardCode   = $_POST['c'];

	$IVSQL = 
		"SELECT
			T0.DocEntry, T0.NumAtCard AS 'DocNum', T0.DocDate, T0.DocDueDate, (T0.DocTotal-T0.VatSum) AS 'DocTotal', T0.SlpCode, T2.SlpName
		FROM OINV T0
		LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
		LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
		WHERE (YEAR(T0.DocDate) = $filt_year AND MONTH(T0.DocDate) = $filt_month) AND T0.CardCode = '$CardCode' AND T0.CANCELED = 'N'";
	if($filt_year <= 2022) {
		$Rows = ChkRowSAP8($IVSQL);
	} else {
		$Rows = ChkRowSAP($IVSQL);
	}
	
	
	if($Rows > 0) {
		$arrCol['GetStatus'] = "SUCCESS";
		$arrCol['Rows'] = $Rows;
		if($filt_year <= 2022) {
			$IVQRY = conSAP8($IVSQL);
		} else {
			$IVQRY = SAPSelect($IVSQL);
		}
		
		$l = 0;
		while($IVRST = odbc_fetch_array($IVQRY)) {
			$arrCol['BillRow_'.$l] = array(
				"DocEntry"   => $IVRST['DocEntry'],
				"DocNum"     => $IVRST['DocNum'],
				"DocDate"    => date("d/m/Y",strtotime($IVRST['DocDate'])),
				"DocDueDate" => date("d/m/Y",strtotime($IVRST['DocDueDate'])),
				"DocTotal"   => $IVRST['DocTotal']
			);
			$l++;
			$SlpCode = $IVRST['SlpCode'];
			$SlpName = conutf8($IVRST['SlpName']);
		}
		$arrCol['SlpCode'] = $SlpCode;
		$arrCol['SlpName'] = $SlpName;
	} else {
		$arrCol['GetStatus'] = "ERR::NO_RESULT";
	}
}

if($_GET['p'] == "SaveDoc") {

	$YDocNum = substr(date("Y")+543,-2);
	$GetDocSQL = "SELECT SUBSTRING(T0.DocNum,8,4)+1 AS 'DocNum' FROM SA04_Header T0 WHERE T0.DocNum LIKE 'SA04-$YDocNum%' ORDER BY T0.DocNum DESC";
	$Rows = ChkRowDB($GetDocSQL);
	if($Rows > 0) {
		$GetDocRST = MySQLSelect($GetDocSQL);
		$NextDocNum = $GetDocRST['DocNum'];
		if($NextDocNum <= 9) {
			$NewSuffix = "000".$NextDocNum;
		} elseif($NextDocNum >= 10 && $NextDocNum <= 99) {
			$NewSuffix = "00".$NextDocNum;
		} elseif($NextDocNum >= 100 && $NextDocNum <= 999) {
			$NewSuffix = "0".$NextDocNum;
		} else {
			$NewSuffix = $NextDocNum;
		}
	} else {
		$NewSuffix = "0001";
	}

	if(isset($_POST['DocRemarkText']) != "") {
		$DocRemarkText = "'".$_POST['DocRemarkText']."'";
	} else {
		$DocRemarkText = "NULL";
	}

	if(isset($_POST['Attach_1']) == "Y") { $Attach_1 = "Y"; } else { $Attach_1 = "N"; }
	if(isset($_POST['Attach_2']) == "Y") { $Attach_2 = "Y"; } else { $Attach_2 = "N"; }
	if(isset($_POST['Attach_3']) == "Y") { $Attach_3 = "Y"; } else { $Attach_3 = "N"; }
	if(isset($_POST['Attach_Remark']) == "") {
		$Attach_Remark = "NULL";
	} else {
		$Attach_Remark = "'".$_POST['Attach_Remark']."'";
	}
	$DocNum        = "SA04-".$YDocNum.$NewSuffix;
	$CardCode      = $_POST['CardCode'];
	$DocType       = $_POST['DocType'];
	$DocDate       = $_POST['DocDate'];
	$DocRemark     = $_POST['DocRemark'];
	$DocRemarkText = $DocRemarkText;
	$Attach_1      = $Attach_1;
	$Attach_2      = $Attach_2;
	$Attach_3      = $Attach_3;
	$Attach_Remark = $Attach_Remark;
	$CreateUkey    = $_SESSION['ukey'];

	$CardNameSQL = "SELECT TOP 1 T0.CardName FROM OCRD T0 WHERE T0.CardCode = '$CardCode'";
	$CardNameQRY = SAPSelect($CardNameSQL);
	$CardNameRST = odbc_fetch_array($CardNameQRY);
	
	$CardName      = conutf8($CardNameRST['CardName']);

	/* INSERT HEADER AND DETAIL */

	switch($DocType) {
		case "A":
			if(isset($_POST['A_BillVer']) == "Y") {
				$BillVer = 8;
			} else {
				$BillVer = 10;
			}
			$BillEntry       = $_POST['A_ViewDocEntry'];
			$BillDocNum      = $_POST['A_ViewDocNum'];
			$BillDocDate     = $_POST['A_ViewDocDate'];
			$BillDocDueDate  = $_POST['A_ViewDocDueDate'];
			$BillSlpCode     = $_POST['A_ViewSlpCode'];
			$BillSlpName     = $_POST['A_ViewSlpName'];
			$BillCoCode      = $_POST['A_ViewCoCode'];
			$BillCoName      = $_POST['A_ViewCoName'];

			$BillDocTotal    = str_replace(",","",$_POST['A_ViewDocTotal']);
			$BillDiscount    = str_replace(",","",$_POST['A_ViewDiscount']);
			$BillDiscUnit    = $_POST['A_ViewDiscUnit'];
			$BillCNTotal     = str_replace(",","",$_POST['A_ViewSumDiscount']);

			$HeaderSQL = "INSERT INTO SA04_Header SET
				DocNum = '$DocNum',
				DocType = '$DocType',
				DocDate = '$DocDate',
				BillVer = '$BillVer',
				BillEntry = $BillEntry,
				BillDocNum = '$BillDocNum',
				BillCardCode = '$CardCode',
				BillCardName = '$CardName',
				BillSlpCode = '$BillSlpCode',
				BillSlpName = '$BillSlpName',
				BillCoCode = '$BillCoCode',
				BillCoName = '$BillCoName',
				BillCNTotal = $BillCNTotal,
				DocRemark = '$DocRemark',
				DocRemarkText = $DocRemarkText,
				Attach_1 = '$Attach_1',
				Attach_2 = '$Attach_2',
				Attach_3 = '$Attach_3',
				Attach_Remark = $Attach_Remark,
				CreateUkey = '$CreateUkey'";
			$DocEntry = MySQLInsert($HeaderSQL);
			// echo $HeaderSQL;

			$DetailSQL = "INSERT INTO SA04_DetailA SET
				DocEntry = $DocEntry,
				BillEntry = $BillEntry,
				BillDocNum = '$BillDocNum',
				BillDocDate = '$BillDocDate',
				BillDocDueDate = '$BillDocDueDate',
				BillDocTotal = $BillDocTotal,
				BillDiscount = $BillDiscount,
				BillDiscUnit = '$BillDiscUnit',
				BillCNTotal = $BillCNTotal,
				CreateUkey = '$CreateUkey'
				";
			MySQLInsert($DetailSQL);
			// echo $DetailSQL;
			
			break;
		case "B":
			if(isset($_POST['A_BillVer']) == "Y") {
				$BillVer = 8;
			} else {
				$BillVer = 10;
			}
			$BillEntry       = $_POST['B_ViewDocEntry'];
			$BillDocNum      = $_POST['B_ViewDocNum'];
			$BillDocDate     = $_POST['B_ViewDocDate'];
			$BillDocDueDate  = $_POST['B_ViewDocDueDate'];
			$BillSlpCode     = $_POST['B_ViewSlpCode'];
			$BillSlpName     = $_POST['B_ViewSlpName'];
			$BillCoCode      = $_POST['B_ViewCoCode'];
			$BillCoName      = $_POST['B_ViewCoName'];

			$SumTotal        = str_replace(",","",$_POST['SumTotal']);
			$VatTotal        = str_replace(",","",$_POST['VatTotal']);
			$CNTotal         = str_replace(",","",$_POST['CNTotal']);
			$TotalRow        = intval($_POST['TotalRow']);

			$HeaderSQL = "INSERT INTO SA04_Header SET
				DocNum = '$DocNum',
				DocType = '$DocType',
				DocDate = '$DocDate',
				BillVer = '$BillVer',
				BillEntry = $BillEntry,
				BillDocNum = '$BillDocNum',
				BillCardCode = '$CardCode',
				BillCardName = '$CardName',
				BillSlpCode = '$BillSlpCode',
				BillSlpName = '$BillSlpName',
				BillCoCode = '$BillCoCode',
				BillCoName = '$BillCoName',
				BillCNVatSum = $VatTotal,
				BillCNTotal = $CNTotal,
				DocRemark = '$DocRemark',
				DocRemarkText = $DocRemarkText,
				Attach_1 = '$Attach_1',
				Attach_2 = '$Attach_2',
				Attach_3 = '$Attach_3',
				Attach_Remark = $Attach_Remark,
				CreateUkey = '$CreateUkey'";
			$DocEntry = MySQLInsert($HeaderSQL);
			// echo $HeaderSQL."<br/>";

			for($i = 0; $i < $TotalRow; $i++) {
				if(isset($_POST['ItemCheck_'.$i])) {
					$ItemCode = $_POST['ItemCode_'.$i];
					$ItemName = $_POST['ItemName_'.$i];
					$OldPrice = str_replace(",","",$_POST['OldPrice_'.$i]);
					$NewPrice = str_replace(",","",$_POST['NewPrice_'.$i]);
					$DifPrice = str_replace(",","",$_POST['DifPrice_'.$i]);
					$Quantity = str_replace(",","",$_POST['Quantity_'.$i]);
					$UnitMsr  = $_POST['UnitMsr_'.$i];
					$DifTotal = str_replace(",","",$_POST['DifTotal_'.$i]);
					if($_POST['Remark_'.$i] != "") {
						$Remark   = "'".$_POST['Remark_'.$i]."'";
					} else {
						$Remark   = "NULL";
					}
					$DetailSQL = "INSERT INTO SA04_DetailB SET
						DocEntry = $DocEntry,
						BillEntry = $BillEntry,
						BillDocNum = '$BillDocNum',
						BillDocDate = '$BillDocDate',
						BillDocDueDate = '$BillDocDueDate',
						BillVisOrder = $i,
						BillItemCode = '$ItemCode',
						BillDscription = '$ItemName',
						BillOldPrice = $OldPrice,
						BillNewPrice = $NewPrice,
						BillDifPrice = $DifPrice,
						BillQuantity = $Quantity,
						BillUnitMsr = '$UnitMsr',
						BillDifTotal = $DifTotal,
						BillRemark = $Remark,
						CreateUkey = '$CreateUkey'
						";
					MySQLInsert($DetailSQL);
					// echo $DetailSQL."<br/";
				}
			}

			break;
		case "C":
			$CNTotal  = str_replace(",","",$_POST['C_ShipCostTotal']);
			$TotalRow = intval($_POST['TotalRow']);
			$BillSlpCode     = $_POST['C_ViewSlpCode'];
			$BillSlpName     = $_POST['C_ViewSlpName'];
			if($_POST['C_DocYear'] <= 2022) {
				$BillVer = 8;
			} else {
				$BillVer = 10;
			}

			$HeaderSQL = "INSERT INTO SA04_Header SET
				DocNum = '$DocNum',
				DocType = '$DocType',
				DocDate = '$DocDate',
				BillVer = $BillVer,
				BillCardCode = '$CardCode',
				BillCardName = '$CardName',
				BillSlpCode = $BillSlpCode,
				BillSlpName = '$BillSlpName',
				BillCNTotal = $CNTotal,
				DocRemark = '$DocRemark',
				DocRemarkText = $DocRemarkText,
				Attach_1 = '$Attach_1',
				Attach_2 = '$Attach_2',
				Attach_3 = '$Attach_3',
				Attach_Remark = $Attach_Remark,
				CreateUkey = '$CreateUkey'";
			$DocEntry = MySQLInsert($HeaderSQL);
			// echo $HeaderSQL."<br/>";
			for($i = 0; $i < $TotalRow; $i++) {
				if(isset($_POST['BillCheck_'.$i])) {
					$BillEntry = $_POST['BillCheck_'.$i];
					$BillDocNum = $_POST['BillDocNum_'.$i];

					$DetailSQL = "INSERT INTO SA04_DetailC SET
						DocEntry = $DocEntry,
						BillEntry = $BillEntry,
						BillDocNum = '$BillDocNum',
						CreateUkey = '$CreateUkey'";
					MySQLInsert($DetailSQL);
				}
			}
			break;
	}

	/* INSERT APPROVE */
	$DeptCode = $_SESSION['DeptCode'];
	/*
		TT2		= DP005 LV034 > LV027
		TT1/OUL	= DP008 LV052 > LV051
		ONL		= DP003 LV018 > (LV010,LV011)
		MT1		= DP006 LV038
		MT2		= DP007 LV045
		DEFAULT =       LV057
	*/
	switch($DeptCode) {
		case "DP005": $LvApp = "'LV034','LV038'"; break;
		case "DP008": $LvApp = "'LV052','LV051'"; break;
		case "DP003": $LvApp = "'LV018','LV010','LV011'"; break;
		case "DP006": $LvApp = "'LV038'"; break;
		case "DP007": $LvApp = "'LV038'"; break;
		default:      $LvApp = "'LV057'"; break;
	}
	$AppUkeySQL = "SELECT T0.uKey FROM users T0 WHERE T0.LvCode IN ($LvApp) AND T0.UserStatus = 'A' ORDER BY T0.LvCode DESC";
	$AppUkeyQRY = MySQLSelectX($AppUkeySQL);
	$i = 0;
	while($AppUkeyRST = mysqli_fetch_array($AppUkeyQRY)) {
		$AppSQL = "INSERT INTO SA04_Approve SET
			DocEntry = $DocEntry,
			VisOrder = $i,
			AppUkeyReq = '".$AppUkeyRST['uKey']."',
			AppState = '1',
			CreateUkey = '$CreateUkey'";
		MySQLInsert($AppSQL);
		$i++;
	}

	$MktSQL = "INSERT INTO SA04_Approve SET DocEntry = $DocEntry, VisOrder = 98, AppUkeyReq = 'DP003', AppState = '0', CreateUkey = '$CreateUkey'";
	MySQLInsert($MktSQL);

	/* Account Approve */
	$AccSQL = "INSERT INTO SA04_Approve SET DocEntry = $DocEntry, VisOrder = 99, AppUkeyReq = 'DP009', AppState = '0', CreateUkey = '$CreateUkey'";
	MySQLInsert($AccSQL);
}

if($_GET['p'] == "GetDocList") {
	$DeptCode = $_POST['filt_team'];
	$year = $_POST['filt_year'];
	$month = $_POST['filt_month'];
	switch($DeptCode) {
		case "DP001":
		case "DP002":
		case "ALL":
			$WhrSQL = NULL;
			break;
		default: $WhrSQL = " AND T2.DeptCode = '$DeptCode'";
			break;
	}

	$DocListSQL = "SELECT
		T0.DocEntry, T0.DocNum, T0.DocType, T0.DocDate, T0.CANCELED, T0.DocStatus, T0.AppStatus, T0.Printed, T0.CreateDate,
		T0.BillCardCode AS 'CardCode', T0.BillCardName AS 'CardName', T0.BillSlpName As 'SlpName', T0.BillDocNum,
		T0.BillCNTotal AS 'CNTotal', CONCAT(T1.uName,' ',T1.uLastName) AS 'CreateName', T2.DeptCode
	FROM SA04_Header T0
	LEFT JOIN users T1 ON T0.CreateUkey = T1.ukey
	LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
	WHERE (YEAR(T0.CreateDate) = $year AND MONTH(T0.CreateDate) = $month) $WhrSQL
	ORDER BY CASE WHEN T0.CANCELED = 'N' THEN 1 ELSE 2 END, T0.DocNum";
	$Rows = ChkRowDB($DocListSQL);
	if($Rows == 0) {
		$output = "<tr><td colspan='10' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$today = date("d");
		$DocListQRY = MySQLSelectX($DocListSQL);
		$output = "";
		$no = 0;
		while($DocListRST = mysqli_fetch_array($DocListQRY)) {
			/*
				int_status หมายถึงสถานะภายในสำหรับการประมวลผลคำสั่งขาย
				+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
				| int_status | CANCELED | DraftStatus | DocStatus | AppStatus || CAN EDIT? | CAN PRINT? | CAN IMPORT? |
				+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
				| 0          | Y        | ANY         | ANY       | ANY       || NO        | NO         | NO          | -> เอกสารยกเลิก
				| 1          | N        | Y           | O         | B         || YES       | YES        | NO          | -> เอกสารแบบร่าง
				| 2          | N        | N           | P         | P         || NO        | YES        | NO          | -> เอกสารรออนุมัติ
				| 3          | N        | N           | P         | Y         || NO        | YES        | YES         | -> เอกสารผ่านการอนุมัติ
				| 4          | N        | N           | P         | N         || NO        | NO         | NO          | -> เอกสารไม่อนุมัติ
				| 5          | N        | N           | C         | Y         || YES       | YES        | NO          | -> เอกสารเสร็จสมบูรณ์ (Import เข้า SAP เรียบร้อย)
				+------------+----------+-------------+-----------+-----------++-----------+------------+-------------+
			*/
			if($DocListRST['CANCELED'] == "Y") {
				$int_status = 0;
			}  elseif($DocListRST['DocStatus'] == "P") {
				switch($DocListRST['AppStatus']) {
					case "Y": $int_status = 3; break;
					case "N": $int_status = 4; break;
					default:  $int_status = 2; break;
				}
			} elseif($DocListRST['DocStatus'] == "C") {
				$int_status = 5;
			} else {
				$int_status = 3;
			}

			$dis_import = NULL;
			$dis_cncl   = NULL;

			if($DocListRST['Printed'] == "Y") {
				$dis_prnt   = NULL;
				// $dis_import = " disabled";
				//$dis_cncl   = " disabled";
			} else {
				$dis_prnt   = " disabled";
			}

			switch($int_status) {
				case 0:
					$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
					break;
				case 1:
					$txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
					break;
				case 1.5:
					$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
					$dis_import = " disabled";
					break;
				case 2:
					$txt_status = "<span class='badge bg-warning w-100'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>";
					$dis_import = " disabled";
					break;
				case 3:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
					if($today >= 28 || $today <= 4) {
						$dis_import = " disabled";
					}
					break;
				case 4:
					$txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
					$dis_import = " disabled";
					break;
				case 5:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
					$dis_import = " disabled";
					$dis_cncl   = " disabled";
					break;
			}

			if($int_status != 0) {
					$txt_opt = "<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>";
						$txt_opt.= "<i class='fas fa-cog fa-fw fa-1x'></i>";
					$txt_opt.= "</button>";
					$txt_opt.= "<ul class='dropdown-menu' style='font-size: 13px;'>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-view' onclick='PreviewDoc(".$DocListRST['DocEntry'].",$int_status)'><i class='fas fa-info fa-fw fa-1x'></i> รายละเอียด</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-prnt$dis_prnt' onclick='PrintDoc(".$DocListRST['DocEntry'].",$int_status)'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์เอกสาร</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-impt$dis_import' onclick='ExportDoc(".$DocListRST['DocEntry'].")'><i class='fas fa-share-square fa-fw fa-1x'></i> ส่งฝ่ายบัญชี</a></li>";
						$txt_opt.= "<li><a href='javascript:void(0);' class='dropdown-item doc-cncl$dis_cncl' onclick='CancelDoc(".$DocListRST['DocEntry'].")'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกเอกสาร</a></li>";
					$txt_opt.= "</ul>";
				switch($int_status) {
					case "1.5":
					case "2":
						$row_cls = " class='table-warning text-warning'";
					break;
					case "3":
					case "5":
						$row_cls = " class='table-success text-success'";
					break;
					case "4":
						$row_cls = " class='table-danger text-danger'";
					break;
					default: $row_cls = null; break;
				}
			} else {
				$txt_opt = "";
				$row_cls = " class='table-active text-secondary'";
			}

			$no++;
			$output .= "<tr$row_cls>";
				$output .= "<td class='text-right'>".number_format($no,0)."</td>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($DocListRST['DocDate']))."</td>";
				$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='PreviewDoc(".$DocListRST['DocEntry'].");'>".$DocListRST['DocNum']."</a></td>";
				$output .= "<td><strong>".$DocListRST['CardCode']." | ".$DocListRST['CardName']."</strong><br/><small>ประเภทเอกสาร: ".DocTypeName($DocListRST['DocType'])."</small></td>";
				$output .= "<td>".$DocListRST['SlpName']."</td>";
				$output .= "<td>".$DocListRST['CreateName']."</td>";
				$output .= "<td class='text-center'>".$DocListRST['BillDocNum']."</td>";
				$output .= "<td class='text-right text-danger' style='font-weight: bold;'>".number_format($DocListRST['CNTotal'],2)."</td>";
				$output .= "<td class='text-center'>".$txt_status."</td>";
				$output .= "<td>$txt_opt</td>";
			$output .= "</tr>";
		}
	}
	$arrCol['DocList'] = $output;
}

if($_GET['p'] == "PreviewDoc") {
	$DocEntry = $_POST['DocEntry'];
	// $intStatus = $_POST['int_status'];

	$HeaderSQL = "SELECT
			T0.DocEntry, T0.DocNum, T0.DocDate, T0.CreateDate, T0.DocType, T0.BillVer,
			T0.BillCardCode, T0.BillCardName, T0.DocRemark, T0.DocRemarkText,
			CONCAT(T1.uName,' ',T1.uLastName) AS 'CoName'
		FROM SA04_Header T0
		LEFT JOIN users T1 ON T0.CreateUkey = T1.uKey
		WHERE T0.DocEntry = $DocEntry
		LIMIT 1";
	$HeaderRST = MySQLSelect($HeaderSQL);
	if($HeaderRST['DocRemark'] == 4) {
		$DocRemark = $HeaderRST['DocRemarkText'];
	} else {
		$DocRemark = DocRemarkName($HeaderRST['DocRemark']);
	}
	$arrCol['ViewDocNum']     = $HeaderRST['DocNum'];
	$arrCol['ViewDocDate']    = date("d/m/Y",strtotime($HeaderRST['DocDate']));
	$arrCol['ViewCardCode']   = $HeaderRST['BillCardCode']." | ".$HeaderRST['BillCardName'];
	$arrCol['ViewDocRemark']  = $DocRemark;
	$arrCol['ViewDocType']    = DocTypeName($HeaderRST['DocType']);
	$arrCol['ViewCreateName'] = $HeaderRST['CoName'];

	switch($HeaderRST['DocType']) {
		case "A":
			$DetailSQL = "SELECT
					T0.BillDocNum, T0.BillDocDate, T0.BillDocDueDate, T1.BillSlpName, T1.BillCoName, 
					T0.BillDocTotal, T0.BillDiscount, T0.BillDiscount, T0.BillDiscUnit, T0.BillCNTotal 
				FROM SA04_DetailA T0
				LEFT JOIN SA04_Header T1 ON T0.DocEntry = T1.DocEntry
				WHERE T0.DocEntry = $DocEntry LIMIT 1";
			$DetailRST = MySQLSelect($DetailSQL);
			if($DetailRST['BillDiscUnit'] == "P") {
				$DiscUnit = "%";
			} else {
				$DiscUnit = "บาท";
			}
			$output .= "<div class='row mt-4'>";
				$output .= "<div class='col-12'>";
					$output .= "<table class='table table-borderless' style='font-size: 12px;'>";
						$output .= "<tr>";
							$output .= "<td width='20%' style='font-weight: bold;'>เลขที่บิล</td>";
							$output .= "<td colspan='3'>".$DetailRST['BillDocNum']."</td>";
						$output .= "</tr>";
						$output .= "<tr>";
							$output .= "<td width='20%' style='font-weight: bold;'>วันที่เปิดบิล</td>";
							$output .= "<td width='30%'>".date("d/m/Y",strtotime($DetailRST['BillDocDate']))."</td>";
							$output .= "<td width='20%' style='font-weight: bold;'>วันที่กำหนดชำระ</td>";
							$output .= "<td width='30%'>".date("d/m/Y",strtotime($DetailRST['BillDocDueDate']))."</td>";
						$output .= "</tr>";
						$output .= "<tr>";
							$output .= "<td style='font-weight: bold;'>พนักงานขาย</td>";
							$output .= "<td>".$DetailRST['BillSlpName']."</td>";
							$output .= "<td style='font-weight: bold;'>ธุรการขาย</td>";
							$output .= "<td>".$DetailRST['BillCoName']."</td>";
						$output .= "</tr>";
					$output .= "</table>";
				$output .= "</div>";
			$output .= "</div>";
			$output .= "<div class='row'>";
				$output .= "<div class='col-12'>";
					$output .= "<table class='table table-bordered' style='font-size: 12px;'>";
						$output .= "<tr>";
							$output .= "<td width='70%' class='text-right'>จำนวนทั้งหมด</td>";
							$output .= "<td width='20%' class='text-right text-danger' style='font-weight: bold;'>".number_format($DetailRST['BillDocTotal'],3)."</td>";
							$output .= "<td width='10%'>บาท</td>";
						$output .= "</tr>";
						$output .= "<tr>";
							$output .= "<td class='text-right'>ส่วนลด</td>";
							$output .= "<td class='text-right'>".number_format($DetailRST['BillDiscount'],3)."</td>";
							$output .= "<td>$DiscUnit</td>";
						$output .= "</tr>";
						$output .= "<tr style='font-weight: bold;'>";
							$output .= "<td class='text-right'>ส่วนลดหนี้/ลดจ่ายสุทธิ</td>";
							$output .= "<td class='text-right text-success'>".number_format($DetailRST['BillCNTotal'],3)."</td>";
							$output .= "<td>บาท</td>";
						$output .= "</tr>";
					$output .= "</table>";
				$output .= "</div>";
			$output .= "</div>";
			break;
		case "B":
			$DetailSQL = "SELECT
					T0.BillDocNum, T0.BillDocDate, T0.BillDocDueDate, T1.BillSlpName, T1.BillCoName,
					T0.BillItemCode, T0.BillDscription, T0.BillOldPrice, T0.BillNewPrice, T0.BillDifPrice,
					T0.BillQuantity, T0.BillUnitMsr, T0.BillDifTotal, T0.BillRemark
				FROM SA04_DetailB T0
				LEFT JOIN SA04_Header T1 ON T0.DocEntry = T1.DocEntry
				WHERE T0.DocEntry = $DocEntry
				ORDER BY T0.BillVisOrder ASC";
			$Rows = ChkRowDB($DetailSQL);
			$DetailQRY = MySQLSelectX($DetailSQL);
			$no = 0;
			$SumTotal = 0;
			while($DetailRST = mysqli_fetch_array($DetailQRY)) {
				$BillDocNum     = $DetailRST['BillDocNum'];
				$BillDocDate    = $DetailRST['BillDocDate'];
				$BillDocDueDate = $DetailRST['BillDocDueDate'];
				$BillSlpName    = $DetailRST['BillSlpName'];
				$BillCoName     = $DetailRST['BillCoName'];

				${"BillItemCode_".$no}   = $DetailRST['BillItemCode'];
				${"BillDscription_".$no} = $DetailRST['BillDscription'];
				${"BillOldPrice_".$no}   = $DetailRST['BillOldPrice'];
				${"BillNewPrice_".$no}   = $DetailRST['BillNewPrice'];
				${"BillDifPrice_".$no}   = $DetailRST['BillDifPrice'];
				${"BillQuantity_".$no}   = $DetailRST['BillQuantity'];
				${"BillUnitMsr_".$no}    = $DetailRST['BillUnitMsr'];
				${"BillDifTotal_".$no}   = $DetailRST['BillDifTotal'];
				${"BillRemark_".$no}     = $DetailRST['BillRemark'];

				$SumTotal = $SumTotal + $DetailRST['BillDifTotal'];
				$no++;
			}
			$VatTotal = ($SumTotal*7)/100;
			$CNTotal  = $SumTotal+$VatTotal;
			$output .= "<div class='row mt-4'>";
				$output .= "<div class='col-12'>";
					$output .= "<table class='table table-borderless' style='font-size: 12px;'>";
						$output .= "<tr>";
							$output .= "<td width='20%' style='font-weight: bold;'>เลขที่บิล</td>";
							$output .= "<td colspan='3'>".$BillDocNum."</td>";
						$output .= "</tr>";
						$output .= "<tr>";
							$output .= "<td width='20%' style='font-weight: bold;'>วันที่เปิดบิล</td>";
							$output .= "<td width='30%'>".date("d/m/Y",strtotime($BillDocDate))."</td>";
							$output .= "<td width='20%' style='font-weight: bold;'>วันที่กำหนดชำระ</td>";
							$output .= "<td width='30%'>".date("d/m/Y",strtotime($BillDocDueDate))."</td>";
						$output .= "</tr>";
						$output .= "<tr>";
							$output .= "<td style='font-weight: bold;'>พนักงานขาย</td>";
							$output .= "<td>".$BillSlpName."</td>";
							$output .= "<td style='font-weight: bold;'>ธุรการขาย</td>";
							$output .= "<td>".$BillCoName."</td>";
						$output .= "</tr>";
					$output .= "</table>";
				$output .= "</div>";
			$output .= "</div>";
			$output .= "<div class='row mt-4'>";
				$output .= "<div class='col-12'>";
					$output .= "<table class='table table-bordered' style='font-size: 12px;'>";
						$output .= "<thead class='text-center'>";
							$output .= "<tr>";
								$output .= "<th width='7.5%' rowspan='2'>รหัสสินค้า</th>";
								$output .= "<th rowspan='2'>ชื่อสินค้า</th>";
								$output .= "<th colspan='3'>ราคา/หน่วย (VAT)</th>";
								$output .= "<th width='10%' rowspan='2' colspan='2'>จำนวน</th>";
								$output .= "<th width='10%' rowspan='2'>ลดหนี้/ลดจ่ายรวม<br/>(ก่อน VAT)</th>";
								$output .= "<th width='25%' rowspan='2'>หมายเหตุ</th>";
							$output .= "</tr>";
							$output .= "<tr>";
								$output .= "<th width='7%'>เก่า</th>";
								$output .= "<th width='7%'>ใหม่</th>";
								$output .= "<th width='7%'>ส่วนต่าง</th>";
							$output .= "</tr>";
						$output .= "</thead>";
						$output .= "<tbody>";
						for($i=0;$i<$Rows;$i++) {
							$output .= "<tr>";
								$output .= "<td class='text-center'>".${"BillItemCode_".$i}."</td>";
								$output .= "<td>".${"BillDscription_".$i}."</td>";
								$output .= "<td class='text-right'>".number_format(${"BillOldPrice_".$i},3)."</td>";
								$output .= "<td class='text-right'>".number_format(${"BillNewPrice_".$i},3)."</td>";
								$output .= "<td class='text-right text-danger'>".number_format(${"BillDifPrice_".$i},3)."</td>";
								$output .= "<td width='5%' class='text-right'>".number_format(${"BillQuantity_".$i},0)."</td>";
								$output .= "<td width='5%'>".${"BillUnitMsr_".$i}."</td>";
								$output .= "<td class='text-right text-danger' style='font-weight: bold;'>".number_format(${"BillDifTotal_".$i},3)."</td>";
								$output .= "<td>".${"BillRemark_".$i}."</td>";
							$output .= "</tr>";
						}
						$output .= "</tbody>";
						$output .= "<tfoot style='font-weight: bold;'>";
							$output .= "<tr>";
								$output .= "<td colspan='7' class='text-right'>รวมทุกรายการ</td>";
								$output .= "<td class='text-right text-danger'>".number_format($SumTotal,3)."</td>";
								$output .= "<td>บาท</td>";
							$output .= "</tr>";
							$output .= "<tr>";
								$output .= "<td colspan='7' class='text-right'>ภาษีมูลค่าเพิ่ม</td>";
								$output .= "<td class='text-right text-danger'>".number_format($VatTotal,3)."</td>";
								$output .= "<td>บาท</td>";
							$output .= "</tr>";
							$output .= "<tr>";
								$output .= "<td colspan='7' class='text-right'>ส่วนลดหนี้/ลดจ่ายสุทธิ</td>";
								$output .= "<td class='text-right text-danger'>".number_format($CNTotal,3)."</td>";
								$output .= "<td>บาท</td>";
							$output .= "</tr>";
						$output .= "</tfoot>";
					$output .= "</table>";
				$output .= "</div>";
			$output .= "</div>";
			break;
		case "C":
			$DetailSQL = "SELECT
					T0.BillDocNum, T1.BillCNTotal
				FROM SA04_DetailC T0
				LEFT JOIN SA04_Header T1 ON T0.DocEntry = T1.DocEntry
				WHERE T0.DocEntry = $DocEntry";
			$Rows = ChkRowDB($DetailSQL);
			$DetailQRY = MySQLSelectX($DetailSQL);
			$no = 0;
			while($DetailRST = mysqli_fetch_array($DetailQRY)) {
				${"BillDocNum_".$no} = $DetailRST['BillDocNum'];
				$CNTotal = $DetailRST['BillCNTotal'];
				$no++;
			}
			$output .= "<div class='row mt-4'>";
				$output .= "<div class='col-12'>";
					$output .= "<p>ส่วนลดหนี้ / ลดจ่ายค่าขนส่งสุทธิ <span class='text-danger' style='font-weight:bold;'>".number_format($CNTotal,3)."</span> บาท โดยมีรายการบิลดังนี้</p>";
					$output .= "<ul>";
					for($i=0;$i<$Rows;$i++) {
						$output .= "<li>".${"BillDocNum_".$i}."</li>";
					}
				$output .= "</ul>";
				$output .= "</div>";
			$output .= "</div>";
			break;
	}
	$arrCol['DocDetail'] = $output;
	/* APPROVAL */
	$AppSQL = "SELECT
		T0.ApproveID, T0.DocEntry, T0.VisOrder, T0.AppUkeyReq, T0.AppState, CONCAT(T1.uName,' ',T1.uLastName) AS 'ApproveName', T1.uNickName AS 'ApproveNick', T0.AppRemark, T0.AppDate AS 'ApproveDate', T2.FineSA, T2.FineCO
	FROM SA04_Approve T0
	LEFT JOIN users T1 ON T0.AppUkeyReq = T1.uKey
	LEFT JOIN SA04_Header T2 ON T0.DocEntry = T2.DocEntry
	WHERE T0.DocEntry = $DocEntry
	ORDER BY T0.VisOrder ASC";
	$Rows = ChkRowDB($AppSQL);
	$Approve = "";
	if(!isset($_GET['App'])) {
		if($Rows == 0) {
			$Approve .= "<tr><td colspan='5' class='text-center'>ไม่มีข้อมูลการอนุมัติ</td></tr>";
		} else {
			$AppQRY = MySQLSelectX($AppSQL);
			$no = 0;
			while($AppRST = mysqli_fetch_array($AppQRY)) {
				$no++;
				$Text_App = null;
				$Text_Remark = $AppRST['AppRemark'];

				$FineNO = NULL;
				$FineSA = NULL;
				$FineCO = NULL;

				if($AppRST['VisOrder'] == 0 && ($AppRST['AppState'] != "0" && $AppRST['AppState'] != "1")) {
					if($AppRST['FineSA'] == "N" && $AppRST['FineCO'] == "N") {
						$FineNO = "<i class='fas fa-check fa-fw fa-1x'></i>";
					}

					if($AppRST['FineSA'] == "Y") {
						$FineSA = "<i class='fas fa-check fa-fw fa-1x'></i>";
					}

					if($AppRST['FineCO'] == "Y") {
						$FineCO = "<i class='fas fa-check fa-fw fa-1x'></i>";
					}
				}

				if($AppRST['ApproveDate'] == null) {
					$AppDate = null;
				} else {
					$AppDate = date("d/m/Y",strtotime($AppRST['ApproveDate']))." เวลา ".date("H:i",strtotime($AppRST['ApproveDate']))." น.";
				}


				switch($AppRST['AppState']) {
					case "0": $Text_App = "<i class='fas fa-minus fa-fw fa-1x'></i>"; break;
					case "1": $Text_App = "<span class='text-muted'><i class='far fa-clock fa-fw fa-lg'></i> รอพิจารณา</span>"; break;
					case "Y": $Text_App = "<span class='text-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
					case "N": $Text_App = "<span class='text-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
				}

				if($AppRST['ApproveNick'] == NULL || $AppRST['ApproveNick'] == "") {
					$NickName = "";
				} else {
					$NickName = " (".$AppRST['ApproveNick'].")";
				}

				switch($AppRST['AppUkeyReq']) {
					case "DP003": $ApproveName = "ฝ่ายการตลาด"; break;
					case "DP009": $ApproveName = "ฝ่ายบัญชี"; break;
					default: $ApproveName = $AppRST['ApproveName']; break;
				}


				$Approve .= "<tr>";
					$Approve .= "<td class='text-right'>".number_format($no,0)."</td>";
					$Approve .= "<td>".$ApproveName.$NickName."</td>";
					$Approve .= "<td class='text-center'>$Text_App</td>";
					$Approve .= "<td class='text-center'>$FineNO</td>";
					$Approve .= "<td class='text-center'>$FineSA</td>";
					$Approve .= "<td class='text-center'>$FineCO</td>";
					$Approve .= "<td>$Text_Remark</td>";
					$Approve .= "<td class='text-center'>$AppDate</td>";
				$Approve .= "</tr>";
			}
		}
	} else {
		/* ฝั่งอนุมัติ */
		if($Rows == 0) {
			$Approve .= "<tr><td colspan='6' class='text-center'>ไม่มีข้อมูลการอนุมัติ</td></tr>";
		} else {
			$AppQRY = MySQLSelectX($AppSQL);
			$no = 0;
			while($AppRST = mysqli_fetch_array($AppQRY)) {
				$no++;
				$Text_App = null;
				$Text_Remark = $AppRST['AppRemark'];

				$FineNO = NULL;
				$FineSA = NULL;
				$FineCO = NULL;

				if($AppRST['ApproveDate'] == null) {
					$AppDate = null;
				} else {
					$AppDate = date("d/m/Y",strtotime($AppRST['ApproveDate']))." เวลา ".date("H:i",strtotime($AppRST['ApproveDate']))." น.";
				}

				switch($AppRST['AppState']) {
					case "0": $Text_App = "<i class='fas fa-minus fa-fw fa-1x'></i>"; break;
					case "1": $Text_App = "<span class='text-muted'><i class='far fa-clock fa-fw fa-lg'></i> รอพิจารณา</span>"; break;
					case "Y": $Text_App = "<span class='text-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
					case "N": $Text_App = "<span class='text-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
				}

				if($AppRST['ApproveNick'] == NULL || $AppRST['ApproveNick'] == "") {
					$NickName = "";
				} else {
					$NickName = " (".$AppRST['ApproveNick'].")";
				}

				switch($AppRST['AppUkeyReq']) {
					case "DP003": $ApproveName = "ฝ่ายการตลาด"; break;
					case "DP009": $ApproveName = "ฝ่ายบัญชี"; break;
					default: $ApproveName = $AppRST['ApproveName']; break;
				}

				if(($AppRST['AppUkeyReq'] == $_SESSION['ukey']) || (($AppRST['VisOrder'] == 98 || $AppRST['VisOrder'] == 99) && $AppRST['AppUkeyReq'] == $_SESSION['DeptCode'])) {
					if($AppRST['VisOrder'] == 0) {
						$FineNO = "<input type='checkbox' name='NoFine_".$AppRST['ApproveID']."' id='NoFine_".$AppRST['ApproveID']."' value='Y' checked />";
						$FineSA = "<input type='checkbox' name='SAFine_".$AppRST['ApproveID']."' id='SAFine_".$AppRST['ApproveID']."' value='Y' />";
						$FineCO = "<input type='checkbox' name='CoFine_".$AppRST['ApproveID']."' id='CoFine_".$AppRST['ApproveID']."' value='Y' />";
					}
					$Approve .= "<tr>";
						$Approve .= "<td class='text-right'>".number_format($no,0)."</td>";
						$Approve .= "<td>".$ApproveName.$NickName."</td>";
						$Approve .= "<td class='text-center'><select class='form-select form-select-sm' name='AppState_".$AppRST['ApproveID']."' id='AppState_".$AppRST['ApproveID']."'><option value='1' selected>รอพิจารณา</option><option value='Y'>อนุมัติ</option><option value='N'>ไม่อนุมัติ</option></select></td>";
						$Approve .= "<td class='text-center'>$FineNO</td>";
						$Approve .= "<td class='text-center'>$FineSA</td>";
						$Approve .= "<td class='text-center'>$FineCO</td>";
						$Approve .= "<td><input class='form-control form-control-sm' name='Remark_".$AppRST['ApproveID']."' id='Remark_".$AppRST['ApproveID']."' placeholder='ระบุเหตุผลการพิจารณา' /></td>";
						$Approve .= "<td class='text-center'>$AppDate</td>";
						$Approve .= "<td class='text-center'><button type='button' class='btn btn-success btn-save btn-sm btn-block' onclick='AppDoc(".$AppRST['ApproveID'].",".$AppRST['DocEntry'].")'><i class='fas fa-save fa-fw fa-1x'></i></button></td>";
					$Approve .= "</tr>";
				} else {
					if($AppRST['VisOrder'] == 0) {
						if($AppRST['FineSA'] == "N" && $AppRST['FineCO'] == "N") {
							$FineNO = "<i class='fas fa-check fa-fw fa-1x'></i>";
						}

						if($AppRST['FineSA'] == "Y") {
							$FineSA = "<i class='fas fa-check fa-fw fa-1x'></i>";
						}

						if($AppRST['FineCO'] == "Y") {
							$FineCO = "<i class='fas fa-check fa-fw fa-1x'></i>";
						}
					}
					$Approve .= "<tr>";
						$Approve .= "<td class='text-right'>".number_format($no,0)."</td>";
						$Approve .= "<td>".$ApproveName.$NickName."</td>";
						$Approve .= "<td class='text-center'>$Text_App</td>";
						$Approve .= "<td class='text-center'>$FineNO</td>";
						$Approve .= "<td class='text-center'>$FineSA</td>";
						$Approve .= "<td class='text-center'>$FineCO</td>";
						$Approve .= "<td>$Text_Remark</td>";
						$Approve .= "<td class='text-center'>$AppDate</td>";
						$Approve .= "<td class='text-center'>&nbsp;</td>";
					$Approve .= "</tr>";
				}
			}
		}
	}
	$arrCol['view_approvelist'] = $Approve;
}

if($_GET['p'] == "CancelDoc") {
	$DocEntry = $_POST['DocEntry'];

	$CancelSQL = "UPDATE SA04_Header SET CANCELED = 'Y', DocStatus = 'C', CancelDate = NOW(), CancelUkey = '".$_SESSION['ukey']."' WHERE DocEntry = $DocEntry";
	$CancelQRY = MySQLUpdate($CancelSQL);
	if(!isset($CancelQRY)) {
		echo "ERROR";
	} else {
		echo "SUCCESS";
	}
}

if($_GET['p'] == "ExportDoc") {
	$DocEntry = $_POST['DocEntry'];

	$GetDocSQL = "SELECT T0.DocEntry, T0.DocNum, T0.DocDate, T0.BillCardCode, T0.BillCardName FROM SA04_Header T0 WHERE T0.DocEntry = $DocEntry AND T0.AppStatus = 'Y' LIMIT 1";
	$Rows     = ChkRowDB($GetDocSQL);
	if($Rows == 0) {
		$arrCol['AddStatus'] = "ERR::NO_RESULT";
	} else {
		$GetDocRST = MySQLSelect($GetDocSQL);
		$DocEntry = $GetDocRST['DocEntry'];
		$DocType  = "SA-04";
		$DocNum   = $GetDocRST['DocNum'];
		$DocDate  = date("Y-m-d",strtotime($GetDocRST['DocDate']));
		$CardCode = $GetDocRST['BillCardCode'];
		$CardName = $GetDocRST['BillCardName'];
		$CreateUkey = $_SESSION['ukey'];
		/* Check Duplicate */
		$ChkSQL = "SELECT T0.DocEntry FROM docacc_header T0 WHERE T0.DocNum = '$DocNum' AND T0.DocType = '$DocType' AND T0.RecipientStatus IN ('1','Y') AND T0.DocStatus = 'O'";
		$Rows   = ChkRowDB($ChkSQL);
		if($Rows > 0) {
			$arrCol['AddStatus'] = "ERR::DUPLICATE";
		} else {
			$InsertSQL = "INSERT INTO docacc_header SET DocNum = '$DocNum', DocType = '$DocType', DocDate = '$DocDate', CardCode = '$CardCode', CardName = '$CardName', SenderUkey = '$CreateUkey'";
			$InsertID  = MySQLInsert($InsertSQL);
			if($InsertID > 0) {
				$arrCol['AddStatus'] = "SUCCESS";
				$UpdateSQL = "UPDATE SA04_Approve SET AppState = '1' WHERE VisOrder = 99 AND AppUkeyReq = 'DP009' AND DocEntry = $DocEntry";
				$UpdateSQL2 = "UPDATE SA04_Header SET Printed = 'Y', UpdateUkey = '".$_SESSION['ukey']."', UpdateDate = NOW() WHERE DocEntry = $DocEntry";
				MySQLUpdate($UpdateSQL2);
				MySQLUpdate($UpdateSQL);
			} else {
				$arrCol['AddStatus'] = "ERR::CANNOT_INSERT";
			}
		}
	}
}


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
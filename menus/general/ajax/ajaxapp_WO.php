<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

function DocTypeName($MainType) {
	switch($MainType) {
		case "R": $TypeName = "ฝากรับสินค้า"; break;
		case "S": $TypeName = "ฝากส่งสินค้า"; break;
		case "B": $TypeName = "ฝากเบิกสินค้า"; break;
	}
	return $TypeName;
}

function SubTypeName($SubType) {
	switch($SubType) {
		case "RP": $TypeName = "ฝากรับสินค้าที่ฝากซื้อ"; break;
		case "RP": $TypeName = "ฝากรับสินค้าที่ฝากซื้อ"; break;
		case "RR": $TypeName = "ฝากรับสินค้าซ่อม"; break;
		case "SP": $TypeName = "ฝากส่งสินค้าให้ลูกค้า"; break;
		case "SQ": $TypeName = "ฝากส่งสินค้าที่ไม่รับคืน เคลม เปลี่ยน (จาก QC)"; break;
		default  : $TypeName = NULL;
	}
	return $TypeName;
}

function ShipmentType($ShipType) {
	switch($ShipType) {
		case 1:  $TypeName = "บริษัทฯ เป็นผู้จ่ายค่าขนส่ง"; break;
		case 2:  $TypeName = "ปลายทางเป็นผู้จ่ายค่าขนส่ง"; break;
		default: $TypeName = "ไม่มีค่าใช้จ่าย"; break;
	}
	return $TypeName;
}

if($_GET['p'] == "AppList") {
	$Limit = NULL;
	if(isset($_GET['tab'])) {
		if($_GET['tab'] == "Y") {
			$Limit = " LIMIT 5";
		}
	}

	$GetSQL = 
		"SELECT
			T0.DocEntry, DATE(T0.DateCreate) AS 'DocDate', DATE(T0.TimeContrac) AS 'DocDueDate', T0.DocNum, T0.TypeOrder,
			T0.CusCode, T0.CusName, T0.Remark, T4.DeptCode, T4.DeptName, T0.StatusDoc, T1.StatusApp,
			CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName'
		FROM OWAS T0
		LEFT JOIN WAS3 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN users T2 ON T0.UserCreate = T2.ukey
		LEFT JOIN positions T3 ON T2.LvCode = T3.LvCode
		LEFT JOIN departments T4 ON T3.DeptCode = T4.DeptCode
		WHERE T3.DeptCode = '".$_SESSION['DeptCode']."' AND T0.StatusDoc = 3
		ORDER BY T0.DocEntry DESC$Limit";
	$Rows = ChkRowDB($GetSQL);
	
	$ChkRow = "N";
	if(isset($_GET['tab'])) { 
		if($_GET['tab'] == "ChkRow") {
			$arrCol['Rows'] = $Rows;
			$ChkRow = "Y";
		}
	}

	if($Rows == 0 && $ChkRow == "Y") {
		$output .= "<tr><td class='text-center' colspan='7'>ไม่มีข้อมูล :(</td></tr>";
	} else {
		$GetQRY = MySQLSelectX($GetSQL);
		while($GetRST = mysqli_fetch_array($GetQRY)) {
			if($GetRST['DocDueDate'] == "" || $GetRST['DocDueDate'] == NULL) {
				$DocDueDate = NULL;
			} else {
				$DocDueDate = date("d/m/Y",strtotime($GetRST['DocDueDate']));
			}
			if($GetRST['CusCode'] == "" || $GetRST['CusCode'] == NULL) {
				$ShowCard = $GetRST['CusName'];
			} else {
				$ShowCard = $GetRST['CusCode']." | ".$GetRST['CusName'];
			}

			if($GetRST['StatusDoc'] == 0) {
				$int_status = 0;
			}
			if($GetRST['StatusDoc'] == 3 && $GetRST['StatusApp'] == NULL) {
				$int_status = 2;
			}
			if($GetRST['StatusDoc'] == 2) {
				if($GetRST['StatusApp'] == "Y" || $GetRST['StatusApp'] == NULL) {
					$int_status = 3;
				} else {
					$int_status = 4;
				}
			}
			if($GetRST['StatusDoc'] == 5 || $GetRST['StatusDoc'] == 14) {
				$int_status = 5;
			}
			switch($int_status) {
				case 0:
					$txt_status = "<span class='badge bg-secondary w-100'><i class='fas fa-ban fa-fw fa-lg'></i> ยกเลิก</span>";
					$RowCls = " class='table-secondary text-muted'";
					break;
				case 1:
					$txt_status = "<span class='badge bg-info w-100'><i class='far fa-save fa-fw fa-lg'></i> บันทึกร่าง</span>";
					break;
				case 1.5:
					$txt_status = "<span class='badge bg-primary'><i class='far fa-clock fa-fw fa-lg'></i> รอตรวจสอบ</span>";
					break;
				case 2:
					$txt_status = "<span class='badge bg-warning w-100'><i class='far fa-clock fa-fw fa-lg'></i> รออนุมัติ</span>";
					break;
				case 3:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>";
					break;
				case 4:
					$txt_status = "<span class='badge bg-danger w-100'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>";
					break;
				case 5:
					$txt_status = "<span class='badge bg-success w-100'><i class='far fa-check-circle fa-fw fa-lg'></i> เสร็จสมบูรณ์</span>";
					break;
			}
			$output .= "<tr>";
				$output .= "<td class='text-center'>".date("d/m/Y",strtotime($GetRST['DocDate']))."</td>";
				$output .= "<td class='text-center'><a href='javascript:void(0);' onclick='PreviewDoc(".$GetRST['DocEntry'].",$int_status);'>".$GetRST['DocNum']."</a></td>";
				$output .= "<td class='text-center'>$DocDueDate</td>";
				$output .= "<td>".DocTypeName($GetRST['TypeOrder'])."</td>";
				$output .= "<td><strong>$ShowCard</strong><br/><small>รายละเอียด: ".iconv_substr($GetRST['Remark'],0,96,'UTF-8')."</small></td>";
				$output .= "<td>".$GetRST['DeptName']."<br/><small>ผู้จัดทำ: ".$GetRST['CreateName']."</small></td>";
				$output .= "<td>$txt_status</td>";
			$output .= "</tr>";
		}
	}
	$arrCol['OrderList'] = $output;
}

if($_GET['p'] == "AppDoc") {
	$DocEntry = $_POST['d'];
	$AppState = $_POST['a'];
	$Remark   = $_POST['r'];

	$InsertSQL = "INSERT INTO WAS3 SET DocEntry = '$DocEntry', StatusApp = '$AppState', Remark = '$Remark', DateApprove = NOW(), UkeyApprove = '".$_SESSION['ukey']."'";
	$InsertID  = MySQLInsert($InsertSQL);
	if($InsertID > 0) {
		$UpdateSQL = "UPDATE OWAS SET StatusDoc = 2, LastUpdate = NOW(), uKeyUpdate = '".$_SESSION['ukey']."' WHERE DocEntry = $DocEntry";
		MySQLUpdate($UpdateSQL);
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if($_GET['a'] == 'GetTar') {
	switch($_SESSION['DeptCode']) {
		case "DP005":
			if($_SESSION['uClass'] == 20) {
				$TeamSQL = " AND (T0.MngType = 'P' AND T0.TeamCode = 'TT2' AND T0.SaleUkey LIKE ('%".$_SESSION['ukey']."%')) OR (T0.MngType = 'T' AND T0.TeamCode = 'TT2')";
			} else {
				$TeamSQL = " AND T0.TeamCode = 'TT2'";
			}
		break;
		case "DP006": $TeamSQL = " AND T0.TeamCode = 'MT1'"; break;
		case "DP007": $TeamSQL = " AND T0.TeamCode = 'MT2'"; break;
		case "DP008":
			if($_SESSION['uClass'] == 20) {
				$TeamSQL = " AND (T0.MngType = 'P' AND T0.TeamCode = 'OUL' AND T0.SaleUkey LIKE ('%".$_SESSION['ukey']."%')) OR (T0.MngType = 'T' AND T0.TeamCode = 'OUL')";
			} else {
				$TeamSQL = " AND T0.TeamCode = 'OUL'";
			}
		break;
		default:
			$TeamSQL = "";
		break;
	}

	$StartDate = date('Y-m-d');
	$SQL = "
		SELECT T0.CPEntry, T0.DocNum, T0.CPTitle, T0.TeamCode, T0.MngType, T0.CPType, T0.StartDate, T0.EndDate, T0.CPDescription, T0.DocStatus, DATEDIFF(T0.EndDate, '$StartDate') AS DiffDate 
		FROM tarsku_header T0 
		WHERE T0.CANCELED = 'N' $TeamSQL
		ORDER BY
			CASE 
				WHEN DATE(T0.StartDate) <= NOW() AND DATE(T0.EndDate) >= NOW() THEN 1
				WHEN DATE(T0.StartDate) > DATE(NOW()) THEN 2
				ELSE 3
			END";
	// echo $SQL;
	$QRY = MySQLSelectX($SQL);
	$r = 0; $No = 0;
	while($result = mysqli_fetch_array($QRY)) {
		switch ($result['TeamCode']) {
			case 'MT1': $TeamCode = "โมเดิร์นเทรด 1"; break;
			case 'MT2': $TeamCode = "โมเดิร์นเทรด 2"; break;
			case 'TT2': $TeamCode = "ต่างจังหวัด"; break;
			case 'OUL': $TeamCode = "หน้าร้าน + เขตกรุงเทพฯ"; break;
			case 'ONL': $TeamCode = "ออนไลน์"; break;
		}

		switch ($result['MngType']) {
			case 'T': $MngType = 'รายทีม'; break;
			case 'P': $MngType  = 'รายบุคคล'; break;
		}

		switch ($result['CPType']) {
			case 'Q': $CPType = 'สินค้าจอง (Quota)'; break;
			case 'F': $CPType = 'สินค้าต้องขาย (Focus)'; break;
			case 'P': $CPType = 'สินค้าโปรโมชั่น (Promotion)'; break;
			case '2': $CPType = 'สินค้ามือสอง (2nd Hand)'; break;
			case 'O': $CPType = 'อื่น ๆ'; break;
			case 'SD': $CPType = 'สถานะ D'; break;
			case 'SR': $CPType = 'สถานะ R'; break;
			case 'SAW': $CPType = 'สถานะ A / W'; break;
			case 'SM': $CPType = 'สถานะ M'; break;
			case 'SN': $CPType = 'สถานะ N'; break;
			case 'SP': $CPType = 'สถานะ P'; break;
			case 'SE': $CPType = 'สถานะ E'; break;
		}

		$No++;
		$arrCol[$r]['DocNum']   = "<a href='javascript:void(0);' onclick='ViewDoc(\"".$result['DocNum']."\",\"".$result['DocStatus']."\");'>".$result['DocNum']."</a>";
		$arrCol[$r]['CPTitle']  = $result['CPTitle'];
		$arrCol[$r]['TeamCode'] = $TeamCode;
		$arrCol[$r]['MngType']  = $MngType;
		$arrCol[$r]['CPType']   = $CPType;
		$arrCol[$r]['CamDate']  = date("d/m/Y",strtotime($result['StartDate']))." ถึง ".date("d/m/Y",strtotime($result['EndDate']));
		if($result['DocStatus'] == 'O') {
			$arrCol[$r]['DocStatus']   = "<span class='badge bg-secondary w-100'><i class='far fa-save fa-fw'></i> บันทึกร่าง</span>";
		}else{
			if(date("Y-m-d") < $result['StartDate']) {
				$arrCol[$r]['DocStatus'] = "<span class='badge bg-info text-dark w-100'><i class='fas fa-hourglass-half fa-fw'></i> รอเวลาแคมเปญ</span>";
			}elseif(date("Y-m-d") >= $result['StartDate'] && date("Y-m-d") <= $result['EndDate']) {
				$arrCol[$r]['DocStatus'] = "
					<span class='badge bg-success w-100'>
						<i class='far fa-clock fa-fw'></i>&nbsp;
						<span class='headtext text1 position-absolute'>กำลังดำเนินการ</span>
						<span class='headtext2 text2'>คงเหลือ ".$result['DiffDate']." วัน</span>
					</span>";
			}else{
				$arrCol[$r]['DocStatus'] = "<span class='badge bg-danger w-100'><i class='fas fa-ban fa-fw'></i> หมดเวลาแคมเปญ</span>";
			}
		}
		$arrCol[$r]['Detail']   = $result['CPDescription'];
		$r++;
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
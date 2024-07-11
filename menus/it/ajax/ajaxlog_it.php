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

function CategoryName($Category){
	switch($Category) {
		case 1:  $result = "ปัญหาเครื่องคอมพิวเตอร์"; break;
		case 2:  $result = "ปัญหาเครือข่ายอินเตอร์เน็ต (ภายใน)"; break;
		case 3:  $result = "ปัญหาอุปกรณ์ IT"; break;
		case 4:  $result = "ปัญหาระบบ EUROX FORCE"; break;
		case 5:  $result = "ปัญหาระบบ WMS"; break;
		case 6:  $result = "ปัญหาระบบ SAP"; break;
		case 7:  $result = "ปัญหาระบบ HRMI"; break;
		case 8:  $result = "ปัญหาระบบ ESS"; break;
		case 9:  $result = "ขอเพิ่มระบบงานใน EUROX FORCE"; break;
		case 10: $result = "ขอเพิ่มรายงาน Excel"; break;
		case 12: $result = "เพิ่มข้อมูลใน EUROX FORCE"; break;
		case 13: $result = "Project MA"; break;
		case 14: $result = "Project IT"; break;
		case 15: $result = "ประชุม"; break;
		case 16: $result = "CCTV"; break;
		case 17: $result = "Solar Cell"; break;
		case 18: $result = "โทรศัพท์ภายใน"; break;
		case 19: $result = "โอนย้ายทรัพย์สิน"; break;
		case 20: $result = "ปัญหาระบบ CRM"; break;
		case 21: $result = "ขอแก้ไขข้อมูลใน EUROX FORCE"; break;
		case 22: $result = "ขอแก้ไขข้อมูลใน WMS"; break;
		case 11: $result = "อื่น ๆ"; break;
	}
	return $result;
}

function CompMethodName($CompMethod){
	switch($CompMethod) {
		case 'TEL': $result = "โทรศัพท์"; break;
		case 'LNG': $result = "LINE กลุ่ม"; break;
		case 'LNP': $result = "LINE ส่วนตัว"; break;
		case 'SPK': $result = "แจ้งปากเปล่า"; break;
	}
	return $result;
}

if($_GET['a'] == 'GetDeptCode') {
	$SQL = "SELECT DeptCode, DeptName FROM departments";
	$QRY = MySQLSelectX($SQL);
	$option = "";
	while($RST = mysqli_fetch_array($QRY)) {
		$option .= "<option value='".$RST['DeptCode']."'>".$RST['DeptName']."</option>";
	}
	$arrCol['option'] = $option;
}

if($_GET['a'] == 'CallData') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	$Where = "";
	if($_SESSION['LvCode'] != 'LV006' && $_SESSION['LvCode'] != 'LV008') {
		$Where = ($_SESSION['DeptCode'] == 'DP001') ? "" : "AND (T0.UkeyCreate = '".$_SESSION['ukey']."' OR T0.UkeySolution = '".$_SESSION['ukey']."')";
	}
	$Dis = ($_SESSION['DeptCode'] == 'DP001') ? "disabled" : "";
	
	$SQL = "
		SELECT T0.LogEntry, T0.LogNum, T0.Category, T0.LogTitle, T0.CompDate, T0.StatusDoc, 
			CASE 
				WHEN T1.uNickName != '' 
				THEN CONCAT(T1.uName, ' ', T1.uLastName, ' (', T1.uNickName, ')') 
				ELSE CONCAT(T1.uName, ' ', T1.uLastName) 
			END AS 'Name'
		FROM logit_header T0
		LEFT JOIN users T1 ON T0.UkeyCreate = T1.uKey
		WHERE YEAR(T0.CompDate) = '$Year' AND MONTH(T0.CompDate) = '$Month' AND T0.Canceled = 'N' $Where
		ORDER BY T0.StatusDoc, T0.LogEntry DESC";
	// echo $SQL;
	$QRY = MySQLSelectX($SQL);
	$Data = array();
	$r = 0;
	while($result = mysqli_fetch_array($QRY)) {
		$r++;
		$Data['LogNum'][$r]    = "<a href='javascript:void(0);' onclick='View(".$result['LogEntry'].");'>".$result['LogNum']."</a>";
		$Data['Category'][$r]  = CategoryName($result['Category']);
		$Data['LogTitle'][$r]  = $result['LogTitle'];
		$Data['Name'][$r]  = $result['Name'];
		$Data['CompDate'][$r]  = date("d/m/Y", strtotime($result['CompDate']))." เวลา ".date("H:i", strtotime($result['CompDate']))." น.";
		switch($result['StatusDoc']) {
			case "O": 
				$Data['StatusDoc'][$r] = "<span class='badge rounded-pill bg-warning text-dark'><i class='far fa-clock'></i> กำลังดำเนินการ</span>"; 
				$Data['Setting'][$r]   = "<button class='btn btn-outline-secondary btn-sm dropdown-toggle $Dis' style='padding: 2px 3px 2px 3px;' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>
											<i class='fas fa-cog fa-fw fa-1x' aria-hidden='true'></i>
										  </button>
										  <ul class='dropdown-menu' style='font-size: 12px;'>
											<li>
												<a href='javascript:void(0);' class='dropdown-item text-warning' onclick='Edit(".$result['LogEntry'].");'><i class='fas fa-edit'></i> แก้ไข</a>
											</li>
											<li>
												<a href='javascript:void(0);' class='dropdown-item text-primary' onclick='Delete(".$result['LogEntry'].");'><i class='fas fa-trash-alt'></i> ลบ</a>
											</li>
										  </ul>";
			break;
			case "C": 
				$Data['StatusDoc'][$r] = "<span class='badge rounded-pill bg-success'><i class='far fa-check-circle'></i> เสร็จสมบูรณ์</span>"; 
				$Data['Setting'][$r]   = "<button class='btn btn-outline-secondary btn-sm dropdown-toggle disabled' style='padding: 2px 3px 2px 3px;' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>
											<i class='fas fa-cog fa-fw fa-1x' aria-hidden='true'></i>
										  </button>";
			break;
		}
		
	}
	$arrCol['Row'] = $r;
	$arrCol['Data'] = $Data;
	
}

if($_GET['a'] == 'CallData2') {
	switch($_SESSION['LvCode']) {
		case "LV006": $LvCode = "'LV009','LV008','LV007'"; break;
		case "LV008": $LvCode = "'LV009'"; break;
	}

	$SQL1 = "SELECT uKey FROM users WHERE LvCode IN ($LvCode)";
	$QRY1 = MySQLSelectX($SQL1);
	$uKey = "";
	while($result1 = mysqli_fetch_array($QRY1)){
		$uKey .= "'".$result1['uKey']."',";
	}
	$uKey = substr($uKey,0,-1);

	$SQL2 ="SELECT T0.LogEntry, T0.LogNum ,T0.Category, T0.LogTitle, T0.CompDate, T0.StatusDoc,
				CASE 
					WHEN T1.uNickName != '' 
					THEN CONCAT(T1.uName, ' ', T1.uLastName, ' (', T1.uNickName, ')') 
					ELSE CONCAT(T1.uName, ' ', T1.uLastName) 
				END AS 'Name'
			FROM logit_header T0
			LEFT JOIN users T1 ON T0.UkeyCreate = T1.uKey
			WHERE (T0.UkeyCreate IN ($uKey) OR T0.UkeySolution IN ($uKey)) AND StatusDoc = 'O'
			ORDER BY LogEntry DESC";
	$QRY2 = MySQLSelectX($SQL2);
	$Data = array(); $r = 0;
	while($result2 = mysqli_fetch_array($QRY2)) {
		$r++;
		$Data['LogNum'][$r]    = "<a href='javascript:void(0);' onclick='View(\"".$result2['LogEntry']."\");'>".$result2['LogNum']."</a>";
		$Data['Category'][$r]  = CategoryName($result2['Category']);
		$Data['LogTitle'][$r]  = $result2['LogTitle'];
		$Data['Name'][$r]  = $result2['Name'];
		$Data['CompDate'][$r]  = date("d/m/Y", strtotime($result2['CompDate']))." เวลา ".date("H:i", strtotime($result2['CompDate']))." น.";
		switch($result2['StatusDoc']) {
			case "O": $Data['StatusDoc'][$r] = "<span class='badge rounded-pill bg-warning text-dark'><i class='far fa-clock'></i> กำลังดำเนินการ</span>"; break;
		}
	}
	$arrCol['Row'] = $r;
	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'AddLog') {
	$Category       = $_POST['Category'];     // หมวดหมู่
	if($_POST['CategoryRemark'] != "") { $CategoryRemark = $_POST['CategoryRemark']; }else{ $CategoryRemark = ""; } // หมวดหมู่
    $CompMethod     = $_POST['CompMethod'];   // ช่องทางการแจ้ง 
    $CompUser       = $_POST['CompUser'];     // ผู้แจ้ง
    $CompDate       = $_POST['CompDate'];     // วันที่แจ้ง
    $LogTitle       = $_POST['LogTitle'];     // หัวข้อที่แจ้ง
    $LogDetail      = $_POST['LogDetail'];    // รายละเอียดที่แจ้ง
    $DeptCode       = $_POST['DeptCode'];     // แผนกผู้แจ้ง

	if(isset($_POST['UkeySolution'])) { $UkeySolution = $_POST['UkeySolution']; }else{ $UkeySolution = ""; } // ผู้แก้ปัญหา
	if($_POST['DateSolution'] != "") { $DateSolution = $_POST['DateSolution']; }else{ $DateSolution = ""; }  // วันที่แก้ปัญหา
	if($_POST['LogSolution'] != "") { $LogSolution = $_POST['LogSolution']; }else{ $LogSolution = ""; }      // วิธีการแก้ปัญหา

	$resultLog = MySQLSelect("SELECT SUBSTRING(LogNum,5,2) AS Year, SUBSTRING(LogNum,9,4) AS Num FROM logit_header ORDER BY LogEntry  DESC LIMIT 1");
	if(!isset($resultLog['Num'])) {
		$NewLogNum = "0001";
	} else {
		if(intval($resultLog['Year']) == intval(date("y"))) {
			$LNum = intval($resultLog['Num'])+1;
			if($LNum <= 9) {
				$NewLogNum = "000".$LNum;
			} elseif($LNum <= 99) {
				$NewLogNum = "00".$LNum;
			} elseif($LNum <= 999) {
				$NewLogNum = "0".$LNum;
			} else {
				$NewLogNum = $LNum;
			}
		}else{
			$NewLogNum = "0001";
		}
	}
	$NewLogNum = "ITL-".date('ym').$NewLogNum;

	$SQL_HEADER = "
		INSERT INTO logit_header 
		SET	Category = $Category, LogNum = '$NewLogNum', CategoryRemark = '$CategoryRemark', CompMethod = '$CompMethod', CompUser = '$CompUser', DeptCode = '$DeptCode',
			CompDate = '$CompDate', LogTitle = '$LogTitle', LogDetail = '$LogDetail', UkeySolution = '$UkeySolution',
			DateSolution = '$DateSolution', LogSolution = '$LogSolution', UkeyCreate = '".$_SESSION['ukey']."', DateCreate = NOW()
		";
	if($_SESSION['LvCode'] == 'LV006') {
		$SQL_HEADER .= ", StatusDoc = 'C'";
	}
	$LogEntry = MySQLInsert($SQL_HEADER);

	if(isset($_FILES['FileAttach']['name'])) {
		$Totals = count($_FILES['FileAttach']['name'])-1;
		for($i = 0; $i <= $Totals; $i++) {
			$FileProcess = explode(".",basename($_FILES['FileAttach']['name'][$i]));
			$countProcess = count($FileProcess);
			if($countProcess == 2){
				$FileOriName = $FileProcess[0]; 
				$FileExt = $FileProcess[1];
			} else {
				$FileOriName = "";
				$FileExt = $FileProcess[$countProcess-1];
				for($n = 0; $n <= $countProcess-2; $n++) {
					$FileOriName .= $FileProcess[$n].".";
				}
				$FileOriName = substr($FileOriName,0,-1);
			}
	
			$tmpFilePath = $_FILES['FileAttach']['tmp_name'][$i];
			if($tmpFilePath != "") {
				$fileDirectory = "ITL-".date('YmdHis')."-".$i.".".$FileExt;
				$NewFilePath = "../../../../FileAttach/LOGIT/".$fileDirectory;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				$INSERT2 = "INSERT INTO logit_attach 
							SET DocEntry = $LogEntry, VisOrder = $i, FileOriName = '$FileOriName', FileDirName = '$fileDirectory' , FileExt = '$FileExt', 
								UploadUkey = '".$_SESSION['ukey']."', UploadDate = NOW()";
				MySQLInsert($INSERT2);
			}
		}
	}
}

if($_GET['a'] == 'View') {
	$LogEntry = $_POST['LogEntry'];
	$SQL = "SELECT T0.LogNum ,T0.Category, T0.CategoryRemark, T0.CompMethod, T0.CompUser, T3.DeptName,
				CASE WHEN T0.CompDate = '0000-00-00' THEN NULL ELSE T0.CompDate END AS 'CompDate',
				T0.LogTitle, T0.LogDetail, CONCAT(T1.uName, ' ', T1.uLastName, ' (', T1.uNickName, ')') AS UkeySolution,
				CASE WHEN T0.DateSolution = '0000-00-00' THEN NULL ELSE T0.DateSolution END AS 'DateSolution', T0.LogSolution,  
				CONCAT(T2.uName, ' ', T2.uLastName, ' (', T2.uNickName, ')') AS UkeyApp,
				CASE WHEN T0.DateApp = '0000-00-00' THEN NULL ELSE T0.DateApp END AS 'DateApp', T0.StatusDoc
			FROM logit_header T0
			LEFT JOIN users T1 ON T0.UkeySolution = T1.uKey
			LEFT JOIN users T2 ON T0.UkeyApp = T2.uKey
			LEFT JOIN departments T3 ON T0.DeptCode = T3.DeptCode
			WHERE T0.LogEntry = $LogEntry";
	$result = MySQLSelect($SQL);
	if($result['Category'] == 11) {
		$Category = CategoryName($result['Category'])." (".$result['CategoryRemark'].")";
	}else{
		$Category = CategoryName($result['Category']);
	}

	if($result['CompDate'] != '' || $result['CompDate'] != null) {
		$CompDate = date("d/m/Y", strtotime($result['CompDate']))." เวลา ".date("H:i", strtotime($result['CompDate']))." น.";
	}else{
		$CompDate = "-";
	}

	if($result['DateSolution'] != '' || $result['DateSolution'] != null) {
		$DateSolution = date("d/m/Y", strtotime($result['DateSolution']))." เวลา ".date("H:i", strtotime($result['DateSolution']))." น.";
	}else{
		$DateSolution = "-";
	}

	if($result['DateApp'] != '' || $result['DateApp'] != null) {
		$DateApp = date("d/m/Y", strtotime($result['DateApp']))." เวลา ".date("H:i", strtotime($result['DateApp']))." น.";
	}else{
		$DateApp = "-";
	}

	if(isset($result['UkeyApp'])) {
		$UkeyApp = $result['UkeyApp'];
	}else{
		$UkeyApp = "-";
	}

	switch($result['StatusDoc']) {
		case 'O': $StatusDoc = "<span class='badge rounded-pill bg-warning text-dark'><i class='far fa-clock'></i> กำลังดำเนินการ</span>"; break;
		case 'C': $StatusDoc = "<span class='badge rounded-pill bg-success'><i class='far fa-check-circle'></i> เสร็จสมบูรณ์</span>";      break;
	}

	$SQL2 = "SELECT T0.* FROM logit_attach T0 WHERE T0.DocEntry = $LogEntry AND T0.FileStatus = 'A'";
	$QRY2 = MySQLSelectX($SQL2);
	$FileImg = array(); $FileImgName = array(); $rImg = 0; 
	$FileDoc = array(); $FileDocName = array(); $rDoc = 0;
	while($result2 = mysqli_fetch_array($QRY2)) {
		if($result2['FileExt'] == 'jpg' || $result2['FileExt'] == 'png' || $result2['FileExt'] == 'gif' || $result2['FileExt'] == 'jpeg') {
			$FileImg[$rImg] = $result2['FileDirName'];
			$FileImgName[$rImg] = $result2['FileOriName'].".".$result2['FileExt'];
			$rImg++;
		}else{
			$FileDoc[$rDoc] = $result2['FileDirName'];
			if($result2['FileExt'] == 'pdf') {
				$FileDocName[$rDoc] = "<i class='fas fa-file-pdf text-danger'></i> ".$result2['FileOriName'].".".$result2['FileExt'];
			}elseif($result2['FileExt'] == 'xlsx') {
				$FileDocName[$rDoc] = "<i class='fas fa-file-excel text-success'></i> ".$result2['FileOriName'].".".$result2['FileExt'];
			}elseif($result2['FileExt'] == 'docx'){
				$FileDocName[$rDoc] = "<i class='fas fa-file-word' style='color: #0d6efd;'></i> ".$result2['FileOriName'].".".$result2['FileExt'];
			}else{
				$FileDocName[$rDoc] = $result2['FileOriName'].".".$result2['FileExt'];
			}
			$rDoc++;
		}
	}

	$DataImg = "";
	if($rImg == 0) {
		$DataImg .= "<div class='d-flex justify-content-center' style='font-size: 12px;'>ไม่มีไฟล์รูปภาพ :(</div>";
	}else{
		for($i = 0; $i < $rImg; $i++) {
			if($i == 0) {
				$DataImg .= "<div class='carousel-item active text-center p-2'>
								<a href='../../../../FileAttach/LOGIT/".$FileImg[$i]."' target='_blank'>
									<img src='../../../../FileAttach/LOGIT/".$FileImg[$i]."' style='width: 100%;' />
								</a>
								<div style='font-size: 12px;'><a href='../../../../FileAttach/LOGIT/".$FileImg[$i]."' download>".$FileImgName[$i]."</a></div>
							</div>";
			}else{
				$DataImg .= "<div class='carousel-item text-center p-2'>
								<a href='../../../../FileAttach/LOGIT/".$FileImg[$i]."' target='_blank'>
									<img src='../../../../FileAttach/LOGIT/".$FileImg[$i]."' style='width: 100%;' />
								</a>
								<div style='font-size: 12px;'><a href='../../../../FileAttach/LOGIT/".$FileImg[$i]."' download>".$FileImgName[$i]."</a></div>
							</div>";
			}
		}
		$DataImg = "<div id='viewDataImg' class='carousel slide' data-bs-touch='false' data-bs-interval='false'>
						<div class='carousel-inner'>".$DataImg."</div>
						<button class='carousel-control-prev' type='button' data-bs-target='#viewDataImg' data-bs-slide='prev'>
							<span class='carousel-control-prev-icon rounded bg-dark' aria-hidden='true'></span>
							<span class='visually-hidden'>Previous</span>
						</button>
						<button class='carousel-control-next' type='button' data-bs-target='#viewDataImg' data-bs-slide='next'>
							<span class='carousel-control-next-icon rounded bg-dark' aria-hidden='true'></span>
							<span class='visually-hidden'>Next</span>
						</button>
					</div>";
	}

	$DataDoc = "";
	if($rDoc == 0) {
		$DataDoc .= "<div class='d-flex justify-content-center' style='font-size: 12px;'>ไม่มีไฟล์เอกสาร :(</div>";
	}else{
		for($i = 0; $i < $rDoc; $i++) {
			$DataDoc .= "<a href='../../../../FileAttach/LOGIT/".$FileDoc[$i]."' download>".$FileDocName[$i]."</a><br>";
		}
	}

	$arrCol['Category']   = $Category;
	$arrCol['CompMethod'] = CompMethodName($result['CompMethod']);
	$arrCol['CompUser']   = $result['CompUser'];
	$arrCol['DeptCode']   = $result['DeptName'];
	$arrCol['CompDate']   = $CompDate;
	$arrCol['LogTitle']   = $result['LogTitle'];
	$arrCol['LogDetail']  = $result['LogDetail'];
	$arrCol['UkeySolution']  = $result['UkeySolution'];
	$arrCol['DateSolution']  = $DateSolution;
	$arrCol['LogSolution']   = $result['LogSolution'];
	$arrCol['UkeyApp']    = $UkeyApp;
	$arrCol['DateApp']    = $DateApp;
	$arrCol['DataImg']    = $DataImg;
	$arrCol['DataDoc']    = $DataDoc; 
	$arrCol['StatusDoc']  = $StatusDoc; 
	$arrCol['LogNum']     = $result['LogNum']; 
}

if($_GET['a'] == 'Edit') {
	$LogEntry = $_POST['LogEntry'];
	$SQL = "SELECT T0.LogEntry, T0.Category, T0.CategoryRemark, T0.CompMethod, T0.CompUser, T0.DeptCode, T0.CompDate, T0.LogTitle, T0.LogDetail, T0.UkeySolution, T0.DateSolution, T0.LogSolution
			FROM logit_header T0
			WHERE LogEntry = $LogEntry";
	$result = MySQLSelect($SQL);
	$arrCol['Category']       = $result['Category'];
	$arrCol['CategoryRemark'] = $result['CategoryRemark'];
	$arrCol['CompMethod']     = $result['CompMethod'];
	$arrCol['CompUser']       = $result['CompUser'];
	$arrCol['DeptCode']       = $result['DeptCode'];
	$arrCol['CompDate']       = date("Y-m-d H:i", strtotime($result['CompDate']));
	$arrCol['LogTitle']       = $result['LogTitle'];
	$arrCol['LogDetail']      = $result['LogDetail'];
	$arrCol['UkeySolution']   = $result['UkeySolution'];
	$arrCol['DateSolution']   = date("Y-m-d H:i", strtotime($result['DateSolution']));
	$arrCol['LogSolution']    = $result['LogSolution'];
	$arrCol['LogEntry']    = $result['LogEntry'];
}

if($_GET['a'] == 'EditLog') {
	$LogEntry   = $_POST['LogEntry'];
	$Category   = $_POST['eCategory'];     // หมวดหมู่
	if($_POST['eCategoryRemark'] != "") { $CategoryRemark = $_POST['eCategoryRemark']; }else{ $CategoryRemark = ""; } // หมวดหมู่
    $CompMethod = $_POST['eCompMethod'];   // ช่องทางการแจ้ง 
    $CompUser   = $_POST['eCompUser'];     // ผู้แจ้ง
    $CompDate   = $_POST['eCompDate'];     // วันที่แจ้ง
    $LogTitle   = $_POST['eLogTitle'];     // หัวข้อที่แจ้ง
    $LogDetail  = $_POST['eLogDetail'];    // รายละเอียดที่แจ้ง
    $DeptCode  = $_POST['eDeptCode'];      // แผนกผู้แจ้ง

	if(isset($_POST['eUkeySolution'])) { $UkeySolution = $_POST['eUkeySolution']; }else{ $UkeySolution = ""; } // ผู้แก้ปัญหา
	if($_POST['eDateSolution'] != "") { $DateSolution = $_POST['eDateSolution']; }else{ $DateSolution = ""; }  // วันที่แก้ปัญหา
	if($_POST['eLogSolution'] != "") { $LogSolution = $_POST['eLogSolution']; }else{ $LogSolution = ""; }      // วิธีการแก้ปัญหา

	$SQL_HEADER = "
		UPDATE logit_header
		SET	Category = $Category, CategoryRemark = '$CategoryRemark', CompMethod = '$CompMethod', CompUser = '$CompUser', DeptCode = '$DeptCode',
			CompDate = '$CompDate', LogTitle = '$LogTitle', LogDetail = '$LogDetail', UkeySolution = '$UkeySolution',
			DateSolution = '$DateSolution', LogSolution = '$LogSolution'
		WHERE LogEntry = $LogEntry";
	MySQLUpdate($SQL_HEADER);

	if(isset($_FILES['eFileAttach']['name'])) {
		$Totals = count($_FILES['eFileAttach']['name'])-1;
		for($i = 0; $i <= $Totals; $i++) {
			$FileProcess = explode(".",basename($_FILES['eFileAttach']['name'][$i]));
			$countProcess = count($FileProcess);
			if($countProcess == 2){
				$FileOriName = $FileProcess[0]; 
				$FileExt = $FileProcess[1];
			} else {
				$FileOriName = "";
				$FileExt = $FileProcess[$countProcess-1];
				for($n = 0; $n <= $countProcess-2; $n++) {
					$FileOriName .= $FileProcess[$n].".";
				}
				$FileOriName = substr($FileOriName,0,-1);
			}
			$tmpFilePath = $_FILES['eFileAttach']['tmp_name'][$i];
			if($tmpFilePath != "") {
				if($i == 0) {
					MySQLUpdate("UPDATE logit_attach SET FileStatus = 'I' WHERE DocEntry = $LogEntry");
				}
				$fileDirectory = "ITL-".date('YmdHis')."-".$i.".".$FileExt;
				$NewFilePath = "../../../../FileAttach/LOGIT/".$fileDirectory;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				$INSERT2 = "INSERT INTO logit_attach 
							SET DocEntry = $LogEntry, VisOrder = $i, FileOriName = '$FileOriName', FileDirName = '$fileDirectory' , FileExt = '$FileExt', 
								UploadUkey = '".$_SESSION['ukey']."', UploadDate = NOW()";
				MySQLInsert($INSERT2);
			}
		}
	}
}

if($_GET['a'] == 'DelectLog') {
	$LogEntry = $_POST['LogEntry'];
	MySQLUpdate("UPDATE logit_header SET Canceled = 'Y' WHERE LogEntry = $LogEntry");
	MySQLUpdate("UPDATE logit_attach SET FileStatus = 'I' WHERE DocEntry = $LogEntry");
}

if($_GET['a'] == 'AppLog') {
	$LogEntry = $_POST['LogEntry'];
	MySQLUpdate("UPDATE logit_header SET StatusDoc = 'C', UkeyApp = '".$_SESSION['ukey']."', DateApp = NOW() WHERE LogEntry = $LogEntry");
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
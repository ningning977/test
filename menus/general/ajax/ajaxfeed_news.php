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

function NewsType_Icon($NewsType) {
    switch($NewsType) {
        case "DEV": $NewsIcon = "<i class='fas fa-laptop-code fa-fw fa-1x'></i>";   break;
        case "ACO": $NewsIcon = "<i class='fas fa-bullhorn fa-fw fa-1x'></i>";      break;
        case "PLC": $NewsIcon = "<i class='fas fa-file-contract fa-fw fa-1x'></i>"; break;
        case "NWS": $NewsIcon = "<i class='far fa-newspaper fa-fw fa-1x'></i>";     break;
        case "ACT": $NewsIcon = "<i class='fas fa-star fa-fw fa-1x'></i>";          break;
    }
    return $NewsIcon;
}
function NewsType_Name($NewsType) {
    switch($NewsType) {
        case "DEV": $NewsName = "ประกาศทีมพัฒนา"; break;
        case "ACO": $NewsName = "ประกาศบริษัท"; break;
        case "PLC": $NewsName = "นโยบาย"; break;
        case "NWS": $NewsName = "ข่าวสาร"; break;
        case "ACT": $NewsName = "กิจกรรม"; break;
    }
    return $NewsName;
}
function TeamName($DeptCode) {
	if($DeptCode == "ALL") {
		$TeamName = "ทุกฝ่าย";
	} else {
		$SQL = "SELECT DeptName FROM departments WHERE DeptCode = '$DeptCode'";
		$result = MySQLSelect($SQL);
		$TeamName = $result['DeptName'];
	}
	return $TeamName;
}

if($_GET['a'] == 'CallData') {	
	if($_POST['DataType'] == 'ALL') {
		$DataType = "";
	}else{
		$DataType = "AND T0.newsType = '".$_POST['DataType']."'";
	}
	if($_SESSION['DeptCode'] == "DP002" && $_SESSION['DeptCode'] == "DP003") {
        $WHERE = "WHERE T0.deptCode IS NOT NULL AND newsStatus = 1 $DataType";
    }else{
		$WHERE = "WHERE (T0.deptCode LIKE '%".$_SESSION['DeptCode']."%' OR T0.deptCode = 'ALL' OR T0.IDUKey = '".$_SESSION['ukey']."') AND newsStatus = 1 $DataType";
    }

	$SQL1 = "SELECT T0.*, DATEDIFF(CURDATE(),T0.CreateDate) AS 'StartDiff', DATEDIFF(CURDATE(),T0.UpdateDate) AS 'UpdateDiff',
				T1.uName, T1.uLastName, T1.uNickName, T2.DeptCode
			FROM feed_news T0
			LEFT JOIN users T1 ON T0.IDUKey = T1.uKey
			LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
			$WHERE AND ((T0.StartDate <= CURDATE() AND T0.EndDate >= CURDATE()) OR (T0.StartDate IS NULL AND T0.EndDate IS NULL))
			ORDER BY CASE WHEN T0.pinMark = 1 THEN 1 ELSE 2 END, T0.UpdateDate DESC";
	$QRY1 = MySQLSelectX($SQL1);
	$Data = array();
	$r = 0; $No = 0; $iconnew = ""; $Dept = array(); $TeamFull = "";
	while($result1 = mysqli_fetch_array($QRY1)) {
		$No++;
		if($result1['pinMark'] == 1) {
			$pin  = "<span><i class='fas fa-thumbtack fa-fw fa-1x text-warning'></i></span>";
			$arrCol[$r]['rowStyle']  = 1;
		} else {
			$pin  = "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
			$arrCol[$r]['rowStyle']  = 0;
		}
		if($result1['UKeyUpdate'] == NULL && $result1['StartDiff'] <= 3) {
			$iconnew = "<strong class='badge bg-danger'>ใหม่!</strong>";
		}elseif($result1['UKeyUpdate'] != NULL && $result1['UpdateDiff'] <= 3) {
			$iconnew = "<strong class='badge bg-info'>อัพเดต!</strong>";
		}else{
			$iconnew = NULL;
		}
		$arrCol[$r]['No']         = $No;
		$arrCol[$r]['CreateDate'] = date("d/m/Y", strtotime($result1['CreateDate']));
		$arrCol[$r]['newsTitle']  = $pin." ".NewsType_Icon($result1['newsType'])." <a href='javascript:void(0);' onclick='ViewData(".$result1['newsID'].");'>".$result1['newsTitle']."</a> ".$iconnew;
		$arrCol[$r]['newsType']   = NewsType_Name($result1['newsType']);
		$arrCol[$r]['FullName']   = $result1['uName']." ".$result1['uLastName']." (".$result1['uNickName'].")";
		$Dept = explode(",",$result1['deptCode']);
		$TeamFull = "";
		for($p = 0; $p <= count($Dept)-1; $p++) {
			$TeamFull .= TeamName($Dept[$p]).", ";
		}
		$arrCol[$r]['deptCount']  = substr($TeamFull,0,-2);
		if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004") {
			$arrCol[$r]['Edit']       = "<a href='javascript:void(0);' onclick='EditData(".$result1['newsID'].");'><i class='fas fa-edit'></i></a>";
			$arrCol[$r]['Delete']       = "<a href='javascript:void(0);' onclick='DeleteData(".$result1['newsID'].");'><i class='fas fa-trash-alt'></i></a>";
		}
		$r++;
	}
}

if($_GET['a'] == 'ViewData') {
	$newsID = $_POST['newsID'];
	$SQL1 = "SELECT T0.newsTitle, CONCAT(T1.uName, ' ', T1.uLastName, ' (', T1.uNickName, ')') AS FullName, T0.deptCode, T0.startDate, T0.endDate,
	  				T0.newsContent, T0.CreateDate
			 FROM feed_news T0
			 LEFT JOIN users T1 ON T0.IDUKey = T1.uKey
			 WHERE newsID = $newsID";
	$result1 = MySQLSelect($SQL1);

	$Dept = explode(",",$result1['deptCode']);
	$TeamFull = "";
	for($p = 0; $p <= count($Dept)-1; $p++) {
		$TeamFull .= TeamName($Dept[$p]).", ";
	}

	$SQL2 = "SELECT fileDirectory, filetype, fileName FROM feed_attach WHERE newsID = $newsID AND fileStatus = 1";
	$QRY2 = MySQLSelectX($SQL2);
	$FileImg = array(); $FileImgName = array(); $rImg = 0; 
	$FileDoc = array(); $FileDocName = array(); $rDoc = 0;
	while($result2 = mysqli_fetch_array($QRY2)) {
		if($result2['filetype'] == 'jpg' || $result2['filetype'] == 'png' || $result2['filetype'] == 'gif') {
			$FileImg[$rImg] = $result2['fileDirectory'];
			$FileImgName[$rImg] = $result2['fileName'].".".$result2['filetype'];
			$rImg++;
		}else{
			$FileDoc[$rDoc] = $result2['fileDirectory'];
			if($result2['filetype'] == 'pdf') {
				$FileDocName[$rDoc] = "<i class='fas fa-file-pdf text-danger'></i> ".$result2['fileName'].".".$result2['filetype'];
			}elseif($result2['filetype'] == 'xlsx') {
				$FileDocName[$rDoc] = "<i class='fas fa-file-excel text-success'></i> ".$result2['fileName'].".".$result2['filetype'];
			}else{
				$FileDocName[$rDoc] = "<i class='fas fa-file-word' style='color: #0d6efd;'></i> ".$result2['fileName'].".".$result2['filetype'];
			}
			$rDoc++;
		}
	}

	$DataImg = "";
	if($rImg == 0) {
		$DataImg .= "<div class='d-flex justify-content-center' style='font-size: 12px;'>ไม่มีรูปภาพแนบ :(</div>";
	}else{
		for($i = 0; $i < $rImg; $i++) {
			if($i == 0) {
				$DataImg .= "<div class='carousel-item active text-center p-2'>
								<img src='../../../../FileAttach/FEEDNEWS/".$FileImg[$i]."' style='width: 100%;' />
								<div style='font-size: 12px;'><a href='../../../../FileAttach/FEEDNEWS/".$FileImg[$i]."' download>".$FileImgName[$i]."</a></div>
							</div>";
			}else{
				$DataImg .= "<div class='carousel-item text-center p-2'>
								<img src='../../../../FileAttach/FEEDNEWS/".$FileImg[$i]."' style='width: 100%;' />
								<div style='font-size: 12px;'><a href='../../../../FileAttach/FEEDNEWS/".$FileImg[$i]."' download>".$FileImgName[$i]."</a></div>
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
		$DataDoc .= "<div class='d-flex justify-content-center' style='font-size: 12px;'>ไม่มีไฟล์เอกสารแนบ :(</div>";
	}else{
		for($i = 0; $i < $rDoc; $i++) {
			$DataDoc .= "<a href='../../../../FileAttach/FEEDNEWS/".$FileDoc[$i]."' download>".$FileDocName[$i]."</a><br>";
		}
	}

	

	$arrCol['newsTitle'] = $result1['newsTitle'];
	$arrCol['FullName']  = $result1['FullName'];
	$arrCol['DeptCode']  = substr($TeamFull,0,-2);
	if($result1['startDate'] == '' && $result1['endDate'] == '') {
		$arrCol['SEDate'] = "ประกาศเมื่อวันที่ ".date("d/m/Y",strtotime($result1['CreateDate']));
	}else{
		$arrCol['SEDate'] = date("d/m/Y",strtotime($result1['startDate']))." ถึงวันที่ ".date("d/m/Y",strtotime($result1['endDate']));
	}
	// $Content = addslashes(str_replace("<p ","<span ",$result1['newsContent']));
	// $arrCol['Content']   = addslashes(str_replace("</p>","</span>",$Content));
	$arrCol['Content']   = $result1['newsContent'];
	$arrCol['DataImg']   = $DataImg;
	$arrCol['DataDoc']   = $DataDoc;
}

if($_GET['a'] == 'EditData') {
	$newsID = $_POST['newsID'];
	$SQL = "SELECT * FROM feed_news WHERE newsID = $newsID";
	$result = MySQLSelect($SQL); 
	$arrCol['newsTitle']   = $result['newsTitle'];
	$arrCol['newsContent'] = $result['newsContent'];
	$arrCol['startDate']   = $result['startDate'];
	$arrCol['endDate']     = $result['endDate'];
	$arrCol['newsType']    = $result['newsType'];
	$arrCol['deptCode']    = $result['deptCode'];
	$arrCol['attachType']  = $result['attachType'];
	$arrCol['pinMark']     = $result['pinMark'];
}

if($_GET['a'] == 'SaveFeedNew') {
	$Header    = $_POST['txtHeader'];
	$NewType   = $_POST['txtNewType'];
	if($_POST['ChkPin'] == 'true') {
		$ChkPin = 1;
	}else{
		$ChkPin = 0;
	}
	$DeptCode  = array(); 
	foreach($_POST['txtDeptCode'] as $data) { 
		array_push($DeptCode,$data); 
	}
	
	if($_POST['txtStartDate'] == '' || $_POST['txtEndDate'] == '') {
		$StartDate = "NULL";
		$EndDate   = "NULL";
	}else{
		$StartDate = "'".$_POST['txtStartDate']."'";
		$EndDate   = "'".$_POST['txtEndDate']."'";
	}
	$FileType  = $_POST['txtFileType'];
	$DocDetail = addslashes(str_replace("<table>","<table class=\"table table-bordered\">",$_POST['TmpDocDetail']));
	// echo $Header." | ".$NewType." | ".$ChkPin." | ".$DeptCode[0]." | ".$StartDate." | ".$EndDate." | ".$FileType." | ".$DocDetail;

	$INSERT =  "INSERT INTO feed_news 
				SET newsTitle = '$Header', newsType = '$NewType', pinMark = $ChkPin, deptCode = '".implode(",",$DeptCode)."', 
				    startDate = $StartDate, endDate = $EndDate, attachType = '$FileType', newsContent = '$DocDetail', 
					IDUKey = '".$_SESSION['ukey']."'";
	$NewsID = MySQLInsert($INSERT);

	if(isset($_FILES['txtFile']['name'])) {
		$Totals = count($_FILES['txtFile']['name'])-1;
		for($i = 0; $i <= $Totals; $i++) {
			$FileProcess = explode(".",basename($_FILES['txtFile']['name'][$i]));
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

			$tmpFilePath = $_FILES['txtFile']['tmp_name'][$i];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/FEEDNEWS/".date('YmdHis')."-".$i.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				$INSERT2 = "INSERT INTO feed_attach 
				   			SET newsID = $NewsID, fileName = '$FileOriName', filetype = '$FileExt', fileDirectory = '".date('YmdHis')."-".$i.".".$FileExt."', CreateDate = NOW()";
				MySQLInsert($INSERT2);
			}
		}
	}
} 

if($_GET['a'] == 'SaveEditFeedNews') {
	$IDUpdate  = $_POST['IDUpdate'];
	$Header    = $_POST['editHeader'];
	$NewType   = $_POST['editNewType'];
	if($_POST['ChkPin'] == 'true') {
		$ChkPin = 1;
	}else{
		$ChkPin = 0;
	}
	$DeptCode  = array(); 
	foreach($_POST['editDeptCode'] as $data) { 
		array_push($DeptCode,$data); 
	}
	
	if($_POST['editStartDate'] == '' || $_POST['editEndDate'] == '') {
		$StartDate = "NULL";
		$EndDate   = "NULL";
	}else{
		$StartDate = "'".$_POST['editStartDate']."'";
		$EndDate   = "'".$_POST['editEndDate']."'";
	}
	$FileType  = $_POST['editFileType'];
	$DocDetail = addslashes(str_replace("<table>","<table class='table table-bordered'>",$_POST['TmpEditDocDetail']));

	$UPDATE =  "UPDATE feed_news 
				SET newsTitle = '$Header', newsType = '$NewType', pinMark = $ChkPin, deptCode = '".implode(",",$DeptCode)."', 
				    startDate = $StartDate, endDate = $EndDate, attachType = '$FileType', newsContent = '$DocDetail',
					UKeyUpdate = '".$_SESSION['ukey']."', UpdateDate = NOW()
				WHERE newsID = $IDUpdate";
	MySQLUpdate($UPDATE);
	
	if(isset($_FILES['editFile']['name'])) {
		$Totals  = count($_FILES['editFile']['name'])-1;
		for($i = 0; $i <= $Totals; $i++) {
			$FileProcess = explode(".",basename($_FILES['editFile']['name'][$i]));
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

			$tmpFilePath = $_FILES['editFile']['tmp_name'][$i];
			if($tmpFilePath != "") {
				if($i == 0) {
					$UPDATE2 = "UPDATE feed_attach SET fileStatus = 0 WHERE newsID = $IDUpdate";
					MySQLUpdate($UPDATE2);
				}
				$NewFilePath = "../../../../FileAttach/FEEDNEWS/".date('YmdHis')."-".$i.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				$INSERT2 = "INSERT INTO feed_attach 
							SET newsID = $IDUpdate, fileName = '$FileOriName', filetype = '$FileExt', fileDirectory = '".date('YmdHis')."-".$i.".".$FileExt."'";
				MySQLInsert($INSERT2);
			}
		}
	}
} 

if($_GET['a'] == 'DeleteFeedNews') {
	$IDDelete  = $_POST['IDDelete'];
	$UPDATE1 = "UPDATE feed_news SET newsStatus = 0 WHERE newsID = $IDDelete";
	MySQLUpdate($UPDATE1);

	$UPDATE2 = "UPDATE feed_attach SET fileStatus = 0 WHERE newsID = $IDDelete";
	MySQLUpdate($UPDATE2);
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
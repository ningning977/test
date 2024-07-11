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
	$DeptCode = $_SESSION['DeptCode'];
	$SQL = "SELECT * FROM manual_erf WHERE Status = 'A' AND (DeptCode LIKE '%ALL%' OR DeptCode LIKE '%$DeptCode%') ORDER BY DocNum";
	$QRY = MySQLSelectX($SQL);
	$r = 0;
	while($RST = mysqli_fetch_array($QRY)) {
		// $arrCol[$r]['DocNum'] = "<a href='../../../../FileAttach/MANUAL/".$RST['FileDirName'].".".$RST['FileExt']."' target='_blank'>".$RST['DocNum']."</a>";
		$arrCol[$r]['DocNum'] = "<a href='javascript:void(0);' onclick='ViewDoc(\"".$RST['FileDirName'].".".$RST['FileExt']."\",\"".$RST['ThaiName']." (".$RST['EngName'].")\",\"".$RST['FileOriName'].".".$RST['FileExt']."\");'>".$RST['DocNum']."</a>";
		$arrCol[$r]['Name'] = $RST['ThaiName']."<br/><small class='text-muted'>(".$RST['EngName'].")</small>";
		$arrCol[$r]['PublishDate'] = date("d/m/Y", strtotime($RST['PublishDate']));
		$arrCol[$r]['RevisionNum'] = number_format($RST['RevisionNum'],0);
		if($RST['LatestUpdate'] != "") {
			$arrCol[$r]['LatestUpdate'] = date("d/m/Y", strtotime($RST['LatestUpdate']));
		}else{
			$arrCol[$r]['LatestUpdate'] = "";
		}

		if($_SESSION['DeptCode'] == "DP002") {
			$arrCol[$r]['BTN'] = "
				<div calss='dropdown'>
					<button class='btn btn-outline-secondary btn-sm dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false' data-bs-auto-close='inside'>
						<i class='fas fa-cog fa-fw fa-1x'></i>
					</button>
					<ul class='dropdown-menu' style='font-size: 13px;'>
						<li><a href='javascript:void(0);' class='dropdown-item fw-bold' onclick='DownloadPDF(\"".$RST['FileDirName'].".".$RST['FileExt']."\",\"".$RST['FileOriName'].".".$RST['FileExt']."\");'><i class='far fa-file-pdf text-danger'></i> ดาวน์โหลด PDF</a></li>
						<li><a href='javascript:void(0);' class='dropdown-item fw-bold' onclick='EditData(".$RST['ID'].");'><i class='fas fa-edit fa-fw text-warning'></i> แก้ไข</a></li>
						<li><a href='javascript:void(0);' class='dropdown-item fw-bold' onclick='DeleteData(".$RST['ID'].");'><i class='fas fa-trash-alt fa-fw text-danger'></i> ลบ</a></li>
					</ul>
				</div>";
		}else{
			$arrCol[$r]['BTN'] = "<a href='javascript:void(0);' class='btn btn-outline-secondary btn-sm fw-bold' onclick='DownloadPDF(\"".$RST['FileDirName'].".".$RST['FileExt']."\",\"".$RST['FileOriName'].".".$RST['FileExt']."\");'><i class='far fa-file-pdf text-danger'></i> PDF</a>";
		}
		$r++;
	}
}

if($_GET['a'] == 'EditData') {
	$ID = $_POST['ID'];
	$SQL = "SELECT * FROM manual_erf WHERE Status = 'A' AND ID = $ID";
	$RST = MySQLSelect($SQL);
	$arrCol['DocNum'] = $RST['DocNum'];
	$arrCol['ThaiName'] = $RST['ThaiName'];
	$arrCol['EngName'] = $RST['EngName'];
	$arrCol['RevisionNum'] = $RST['RevisionNum'];
	$arrCol['PublishDate'] = date("Y-m-d",strtotime($RST['PublishDate']));
	if($RST['LatestUpdate'] != "") {
		$arrCol['LatestUpdate'] = date("Y-m-d",strtotime($RST['LatestUpdate']));
	}else{
		$arrCol['LatestUpdate'] = "";
	}
	if($RST['DeptCode'] == "ALL"){
		$arrCol['DeptCode'] = "ALL";
	}else{
		$arrCol['DeptCode'] = explode(",",$RST['DeptCode']);
	}
}

if($_GET['a'] == 'SaveEdit') {
	$ID = $_POST['ID'];
	$DocNum = $_POST['editDocNum'];
	$ThaiName = $_POST['editThaiName'];
	$EngName = $_POST['editEngName'];
	$RevisionNum = $_POST['editRevisionNum'];
	$PublishDate = $_POST['editPublishDate'];
	if($_POST['editLatestUpdate'] == "") {
		$LatestUpdate = "NULL";
	}else{
		$LatestUpdate = "'".$_POST['editLatestUpdate']."'";
	}
	$Ukey = $_SESSION['ukey'];
	$DeptCodeArr = array();
	foreach($_POST['editDeptCode'] as $data) { 
		array_push($DeptCodeArr,$data);
	}

	if(count($DeptCodeArr) == 1 && $DeptCodeArr[0] == "ALL") {
		$DeptCode = "ALL";
	} else {
		$DeptCode = implode(",",$DeptCodeArr);
	}

	if($_FILES['editFileAttach']['name'] != '') {
		$FileProcess = explode(".",$_FILES['editFileAttach']['name']);
		$countProcess = count($FileProcess);
		if($countProcess == 2){
			$FileOriName = $FileProcess[0]; 
			$FileExt = $FileProcess[1];
		}else{
			$FileOriName = "";
			$FileExt = $FileProcess[$countProcess-1];
			for($n = 0; $n <= $countProcess-2; $n++) {
				$FileOriName .= $FileProcess[$n].".";
			}
			$FileOriName = substr($FileOriName,0,-1);
		}
		$tmpFilePath = $_FILES['editFileAttach']['tmp_name'];
		$FileDirName = date('YmdHis');

		if($tmpFilePath != "") {
			$NewFilePath = "../../../../FileAttach/MANUAL/".$FileDirName.".".$FileExt;
			move_uploaded_file($tmpFilePath,$NewFilePath);

			$SQL = "SELECT FileDirName, FileExt FROM manual_erf WHERE ID = $ID AND Status = 'A'";
			$RST = MySQLSelect($SQL);
			if(isset($RST['FileDirName'])) {
				unlink("../../../../FileAttach/MANUAL/".$RST['FileDirName'].".".$RST['FileExt']);
			}

			$UPDATE = "
			UPDATE manual_erf 
			SET DocNum = '$DocNum', 
				RevisionNum = '$RevisionNum', 
				ThaiName = '$ThaiName', 
				EngName = '$EngName', 
				PublishDate = '$PublishDate', 
				LatestUpdate = $LatestUpdate, 
				DeptCode = '$DeptCode', 
				FileOriName = '$FileOriName', 
				FileDirName = '".$FileDirName."', 
				FileExt = '$FileExt', 
				UpdateUkey = '$Ukey',
				UpdateDate = NOW()
			WHERE ID = $ID";
		}
	}else{
		$UPDATE = "
		UPDATE manual_erf 
		SET DocNum = '$DocNum', 
			RevisionNum = '$RevisionNum', 
			ThaiName = '$ThaiName', 
			EngName = '$EngName', 
			PublishDate = '$PublishDate', 
			LatestUpdate = $LatestUpdate, 
			DeptCode = '$DeptCode', 
			UpdateUkey = '$Ukey',
			UpdateDate = NOW()
		WHERE ID = $ID";
	}
	MySQLUpdate($UPDATE);
}

if($_GET['a'] == 'DeleteData') {
	$ID = $_POST['ID'];
	$UPDATE = "UPDATE manual_erf SET Status = 'I' WHERE ID = $ID;";
	MySQLUpdate($UPDATE);
}

if($_GET['a'] == 'SaveERF') {
	$DocNum = $_POST['DocNum'];
	$ThaiName = $_POST['ThaiName'];
	$EngName = $_POST['EngName'];
	$RevisionNum = $_POST['RevisionNum'];
	$PublishDate = $_POST['PublishDate'];
	
	if($_POST['LatestUpdate'] == "") {
		$LatestUpdate = "NULL";
	}else{
		$LatestUpdate = "'".$_POST['LatestUpdate']."'";
	}
	$Ukey = $_SESSION['ukey'];

	$DeptCodeArr = array();
	foreach($_POST['DeptCode'] as $data) { 
		array_push($DeptCodeArr,$data);
	}

	if(count($DeptCodeArr) == 1 && $DeptCodeArr[0] == "ALL") {
		$DeptCode = "ALL";
	} else {
		$DeptCode = implode(",",$DeptCodeArr);
	}
	
	if(isset($_FILES['FileAttach']['name'])) { 
		$FileProcess = explode(".",$_FILES['FileAttach']['name']);
		$countProcess = count($FileProcess);
		if($countProcess == 2){
			$FileOriName = $FileProcess[0]; 
			$FileExt = $FileProcess[1];
		}else{
			$FileOriName = "";
			$FileExt = $FileProcess[$countProcess-1];
			for($n = 0; $n <= $countProcess-2; $n++) {
				$FileOriName .= $FileProcess[$n].".";
			}
			$FileOriName = substr($FileOriName,0,-1);
		}
		$tmpFilePath = $_FILES['FileAttach']['tmp_name'];
		$FileDirName = date('YmdHis');

		if($tmpFilePath != "") {
			$NewFilePath = "../../../../FileAttach/MANUAL/".$FileDirName.".".$FileExt;
			move_uploaded_file($tmpFilePath,$NewFilePath);

			$SQL = "SELECT FileDirName, FileExt FROM manual_erf WHERE DocNum = '$DocNum' AND Status = 'A'";
			$RST = MySQLSelect($SQL);
			if(isset($RST['FileDirName'])) {
				unlink("../../../../FileAttach/MANUAL/".$RST['FileDirName'].".".$RST['FileExt']);

				$UPDATE = "UPDATE manual_erf SET Status = 'I' WHERE DocNum = '$DocNum' AND Status = 'A'";
				MySQLUpdate($UPDATE);
			}

			$INSERT = "
				INSERT INTO manual_erf 
			   	SET DocNum = '$DocNum', 
				   	RevisionNum = '$RevisionNum', 
				   	ThaiName = '$ThaiName', 
				   	EngName = '$EngName', 
					PublishDate = '$PublishDate', 
					DeptCode = '$DeptCode', 
					LatestUpdate = $LatestUpdate, 
					FileOriName = '$FileOriName', 
					FileDirName = '".$FileDirName."', 
					FileExt = '$FileExt', 
					Status = 'A',
					CreateUkey = '$Ukey',
					CreateDate = NOW()";
			MySQLInsert($INSERT);
		}
	}
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
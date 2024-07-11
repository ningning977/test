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

if($_GET['p'] == "GetItem") {
	$ItemType = $_POST['ItemType'];
	$WhrSQL = null;

	$ItemSQL =
		"SELECT
			T0.DocEntry, T0.ItemType, T0.VisOrder, CONCAT(T0.ItemType,'-',T0.ItemCode) AS 'DocNum', T0.ItemTitle, T0.CreateDate, T0.VisManager, T0.VisSaleEmp, T0.VisDealer,
			(SELECT CONCAT(P1.FileDirName,'.',P1.FileExt) FROM salepage_attach P1 WHERE P1.DocEntry = T0.DocEntry AND P1.FileType = 'T' AND P1.FileStatus = 'A' ORDER BY P1.AttachID DESC LIMIT 1) AS 'ThmbSRC',
			(SELECT CONCAT(P1.FileDirName,'.',P1.FileExt) FROM salepage_attach P1 WHERE P1.DocEntry = T0.DocEntry AND P1.FileType = 'B' AND P1.FileStatus = 'A' ORDER BY P1.AttachID DESC LIMIT 1) AS 'BookSRC',
			(SELECT CONCAT(P1.FileDirName,'.',P1.FileExt) FROM salepage_attach P1 WHERE P1.DocEntry = T0.DocEntry AND P1.FileType = 'L' AND P1.FileStatus = 'A' ORDER BY P1.AttachID DESC LIMIT 1) AS 'LinkSRC'
		FROM salepage_header T0
		WHERE T0.ItemType = '$ItemType' AND T0.DocStatus = 'A' $WhrSQL
		ORDER BY T0.VisOrder ASC";
	$Rows = ChkRowDB($ItemSQL);
	if($Rows > 0) {
		$ItemQRY = MySQLSelectX($ItemSQL);
		$i = 0;
		while($ItemRST = mysqli_fetch_array($ItemQRY)) {
			$arrCol['BD_'.$i]['DocEntry']	= $ItemRST['DocEntry'];
			$arrCol['BD_'.$i]['ItemType']	= $ItemRST['ItemType'];
			$arrCol['BD_'.$i]['VisOrder']	= $ItemRST['VisOrder'];
			$arrCol['BD_'.$i]['DocNum']		= $ItemRST['DocNum'];
			$arrCol['BD_'.$i]['ItemTitle']	= $ItemRST['ItemTitle'];
			$arrCol['BD_'.$i]['CreateDate']	= date("d/m/Y",strtotime($ItemRST['CreateDate']))." เวลา ".date("H:i",strtotime($ItemRST['CreateDate']))." น.";
			$arrCol['BD_'.$i]['VisManager']	= $ItemRST['VisManager'];
			$arrCol['BD_'.$i]['VisSaleEmp']	= $ItemRST['VisSaleEmp'];
			$arrCol['BD_'.$i]['VisDealer']	= $ItemRST['VisDealer'];
			$arrCol['BD_'.$i]['ThmbSRC']	= $ItemRST['ThmbSRC'];
			$arrCol['BD_'.$i]['BookSRC']	= $ItemRST['BookSRC'];
			$arrCol['BD_'.$i]['LinkSRC'] 	= $ItemRST['LinkSRC'];
			$i++;
		}
	} else {
		$Rows = 0;
	}
	$arrCol['Rows'] = $Rows;
}

if($_GET['p'] == "GetNextOrder") {
	$ItemType = $_POST['ItemType'];
	$SQL1 = "SELECT IFNULL(MAX(T0.VisOrder)+1,1) AS 'NxtOrder' FROM salepage_header T0 WHERE T0.ItemType = '$ItemType' AND T0.CANCELED = 'N' ORDER BY T0.VisOrder DESC";
	if(ChkRowDB($SQL1) == 0) {
		$arrCol['NxtOrder'] = 1;
	} else {
		$RST1 = MySQLSelect($SQL1);
		$arrCol['NxtOrder'] = $RST1['NxtOrder'];
	}
}

if($_GET['p'] == "EditItem") {
	$DocEntry = $_POST['DocEntry'];

	$SQL1 = "SELECT T0.DocEntry, T0.ItemType, T0.ItemTitle, T0.VisOrder, T0.VisManager, T0.VisSaleEmp, T0.VisDealer FROM salepage_header T0 WHERE T0.DocEntry = $DocEntry";
	$RST1 = MySQLSelect($SQL1);

	$arrCol['DocEntry']		= $RST1['DocEntry'];
	$arrCol['VisOrder']		= $RST1['VisOrder'];
	$arrCol['ItemType']		= $RST1['ItemType'];
	$arrCol['ItemTitle']	= $RST1['ItemTitle'];
	$arrCol['VisManager']	= $RST1['VisManager'];
	$arrCol['VisSaleEmp']	= $RST1['VisSaleEmp'];
	$arrCol['VisDealer']	= $RST1['VisDealer'];
}

if($_GET['p'] == "SaveItem") {
	$EditEntry	= $_POST['DocEntry'];
	$ItemType   = $_POST['ItemType'];
	$ItemName   = $_POST['ItemName'];
	$VisOrder   = $_POST['VisOrder'];
	$VisManager = $_POST['VisManager'];
	$VisSaleEmp = $_POST['VisSaleEmp'];
	$VisDealer  = $_POST['VisDealer'];
	$ItemLink   = $_POST['ItemLink'];
	$Ukey       = $_SESSION['ukey'];

	switch($ItemType) {
		case "CAT": $TypeCode = 0; break;
		case "SPP": $TypeCode = 1; break;
		case "PRC": $TypeCode = 2; break;
		case "PRO": $TypeCode = 3; break;
		case "VDO": $TypeCode = 4; break;
		case "SKU": $TypeCode = 5; break;
		case "COP": $TypeCode = 6; break;
	}

	$Prefix = date("y").$TypeCode;

	/* ItemCode Generator */
	$GenSQL = "SELECT SUBSTRING(T0.ItemCode,4,3)+1 AS 'DocNum' FROM salepage_header T0 WHERE T0.ItemCode LIKE '$Prefix%' AND T0.ItemType = '$ItemType' ORDER BY T0.ItemCode DESC LIMIT 1";
	if(ChkRowDB($GenSQL) == 0) {
		$ItemCode = $Prefix."001";
	} else {
		$GenRST = MySQLSelect($GenSQL);
		$NextDocNum = $GenRST['DocNum'];

		if($NextDocNum <= 9) {
			$ItemCode = $Prefix."00".$NextDocNum;
		} elseif($NextDocNum >= 10 && $NextDocNum <= 99) {
			$ItemCode = $Prefix."0".$NextDocNum;
		} else {
			$ItemCode = $Prefix.$NextDocNum;
		}
	}

	if($EditEntry == "0") {

		/* INSERT HEADER */
		$InsertHeader =
			"INSERT INTO salepage_header SET
				ItemType = '$ItemType',
				CANCELED = 'N',
				VisOrder = $VisOrder,
				VisManager = '$VisManager',
				VisSaleEmp = '$VisSaleEmp',
				VisDealer = '$VisDealer',
				ItemCode = '$ItemCode',
				ItemTitle = '$ItemName',
				CreateUkey = '$Ukey',
				DocStatus = 'A'";
		// echo $InsertHeader."<br/>";
		// $DocEntry = 1;
		$DocEntry = MySQLInsert($InsertHeader);

		/* INSERT IMG THUMBNAIL */
		if(isset($_FILES['ImgThumb'])) {
			$TmbTotal = count($_FILES['ImgThumb'])-1;
			$FileProcess = explode(".",basename($_FILES['ImgThumb']['name']));
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

			$tmpFilePath = $_FILES['ImgThumb']['tmp_name'];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/SALEPAGES/thumb/".$ItemCode.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				// $DocEntry = 2;
				$ThumbSQL = "INSERT INTO salepage_attach SET
					DocEntry = $DocEntry,
					FileType = 'T',
					FileOriName = '$FileOriName',
					FileDirName = '".$ItemCode."',
					FileExt = '$FileExt',
					UploadUkey = '$Ukey'
				;";
				// echo $ThumbSQL."<br/>";
				MySQLInsert($ThumbSQL);
			}
		}

		/* INSERT FILE BOOK */
		if(isset($_FILES['ItemFile'])) {
			$TmbTotal = count($_FILES['ItemFile'])-1;
			$FileProcess = explode(".",basename($_FILES['ItemFile']['name']));
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

			$tmpFilePath = $_FILES['ItemFile']['tmp_name'];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/SALEPAGES/".$ItemCode.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				// $DocEntry = 2;
				$ItemSQL = "INSERT INTO salepage_attach SET
					DocEntry = $DocEntry,
					FileType = 'B',
					FileOriName = '$FileOriName',
					FileDirName = '".$ItemCode."',
					FileExt = '$FileExt',
					UploadUkey = '$Ukey'
				;";
				// echo $ItemSQL."<br/>";
				MySQLInsert($ItemSQL);
			}
		}

		/* INSERT YOUTUBE URL */
		if($ItemLink != "") {
			$LinkSQL =	"INSERT INTO salepage_attach SET
				DocEntry = $DocEntry,
				FileType = 'L',
				FileOriName = '$ItemLink',
				FileDirName = NULL,
				FileExt = NULL,
				UploadUkey = '$Ukey'
			;";
			// echo $LinkSQL."<br/>";
			MySQLInsert($LinkSQL);
		}
	} else {
		/* UPDATE OLD HEADER TO INACTIVE */
		$UpdateHeader = "UPDATE salepage_header SET DocStatus = 'I' WHERE DocEntry = $EditEntry";
		MySQLUpdate($UpdateHeader);

		/* INSERT NEW HEADER TO ACTIVE */
		$InsertHeader =
			"INSERT INTO salepage_header SET
				ItemType = '$ItemType',
				CANCELED = 'N',
				VisOrder = $VisOrder,
				VisManager = '$VisManager',
				VisSaleEmp = '$VisSaleEmp',
				VisDealer = '$VisDealer',
				ItemCode = '$ItemCode',
				ItemTitle = '$ItemName',
				CreateUkey = '$Ukey',
				DocStatus = 'A'";
		// echo $InsertHeader."<br/>";
		// $DocEntry = 1;
		$DocEntry = MySQLInsert($InsertHeader);

		/* UPDATE OLD ATTACH TO INACTIVE */
		$UpdateAttach = "UPDATE salepage_attach SET FileStatus = 'I' WHERE DocEntry = $EditEntry";
		MySQLUpdate($UpdateAttach);

		/* SELECT TO INSERT */
		$InsertAttach = 
			"INSERT INTO
				salepage_attach (DocEntry, FileType, FileOriName, FileDirName, FileExt, FileStatus, UploadUkey, UploadDate)
			SELECT
				$DocEntry, FileType, FileOriName, FileDirName, FileExt, 'A', '$Ukey', NOW()
			FROM salepage_attach
			WHERE DocEntry = $EditEntry";
		MySQLInsert($InsertAttach);

		/* INSERT IMG THUMBNAIL */
		if(isset($_FILES['ImgThumb'])) {
			$TmbTotal = count($_FILES['ImgThumb'])-1;
			$FileProcess = explode(".",basename($_FILES['ImgThumb']['name']));
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

			$tmpFilePath = $_FILES['ImgThumb']['tmp_name'];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/SALEPAGES/thumb/".$ItemCode.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				// $DocEntry = 2;
				$ThmbRow = ChkRowDB("SELECT T0.AttachID FROM salepage_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileType = 'T'");
				if($ThmbRow == 0) {
					$ThumbSQL = "INSERT INTO salepage_attach SET
						DocEntry = $DocEntry,
						FileType = 'T',
						FileOriName = '$FileOriName',
						FileDirName = '".$ItemCode."',
						FileExt = '$FileExt',
						UploadUkey = '$Ukey'
					;";
					// echo $ThumbSQL."<br/>";
					MySQLInsert($ThumbSQL);
				} else {
					$ThumbSQL = "UPDATE salepage_attach SET FileOriName = '$FileOriName', FileDirName = '$ItemCode', FileExt = '$FileExt' WHERE DocEntry = $DocEntry AND FileType = 'T'";
					MySQLUpdate($ThumbSQL);
				}
			}
		}

		/* INSERT FILE BOOK */
		if(isset($_FILES['ItemFile'])) {
			$TmbTotal = count($_FILES['ItemFile'])-1;
			$FileProcess = explode(".",basename($_FILES['ItemFile']['name']));
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

			$tmpFilePath = $_FILES['ItemFile']['tmp_name'];
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../FileAttach/SALEPAGES/".$ItemCode.".".$FileExt;
				move_uploaded_file($tmpFilePath,$NewFilePath);
				// $DocEntry = 2;
				$ThmbRow = ChkRowDB("SELECT T0.AttachID FROM salepage_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileType = 'B'");
				if($ThmbRow == 0) {
					$ItemSQL = "INSERT INTO salepage_attach SET
						DocEntry = $DocEntry,
						FileType = 'B',
						FileOriName = '$FileOriName',
						FileDirName = '".$ItemCode."',
						FileExt = '$FileExt',
						UploadUkey = '$Ukey'
					;";
					// echo $ItemSQL."<br/>";
					MySQLInsert($ItemSQL);
				} else {
					$ThumbSQL = "UPDATE salepage_attach SET FileOriName = '$FileOriName', FileDirName = '$ItemCode', FileExt = '$FileExt' WHERE DocEntry = $DocEntry AND FileType = 'B'";
					MySQLUpdate($ThumbSQL);
				}
			}
		}

		/* INSERT YOUTUBE URL */
		if($ItemLink != "") {
			$ThmbRow = ChkRowDB("SELECT T0.AttachID FROM salepage_attach T0 WHERE T0.DocEntry = $DocEntry AND T0.FileType = 'L'");
			if($ThmbRow == 0) {
				$LinkSQL =	"INSERT INTO salepage_attach SET
					DocEntry = $DocEntry,
					FileType = 'L',
					FileOriName = '$ItemLink',
					FileDirName = NULL,
					FileExt = NULL,
					UploadUkey = '$Ukey'
				;";
				// echo $LinkSQL."<br/>";
				MySQLInsert($LinkSQL);
			} else {
				$ThumbSQL = "UPDATE salepage_attach SET FileOriName = '$ItemLink' WHERE DocEntry = $DocEntry AND FileType = 'L'";
				MySQLUpdate($ThumbSQL);
			}
		}
	}
}

if($_GET['p'] == 'GetSKU') {
	$SQL = 
		"SELECT T0.ItemCode, T1.ItemName, T1.BarCode, IFNULL(T0.UpdateDate, T0.CreateDate) AS UpdateDate
		FROM skubook_header T0
		LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
		WHERE T0.UpdateDate IS NOT NULL
		ORDER BY T0.ItemCode";
	$QRY = MySQLSelectX($SQL);
	$r = 0; $no = 0;
	while($RST = mysqli_fetch_array($QRY)) {
		$no++;
		$arrCol[$r]['No'] = $no;
		$arrCol[$r]['ItemCode'] = "<a href='?p=sku_book&ItemCode=".$RST['ItemCode']."' target='_blank');'>".$RST['ItemCode']."</a>";
		$arrCol[$r]['ItemName'] = $RST['ItemName'];
		$arrCol[$r]['BarCode'] = $RST['BarCode'];
		$arrCol[$r]['UpdateDate']  = date("d/m/Y", strtotime($RST['UpdateDate']));
		$r++;
	}
}

if($_GET['p'] == 'DeleteItem') {
	$DocEntry = $_POST['DocEntry'];
	$UPDATE = "UPDATE salepage_header SET DocStatus = 'I' WHERE DocEntry = $DocEntry";
	MySQLUpdate($UPDATE);
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
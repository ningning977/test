<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName']==NULL ){
	echo '<script>window.location="../../../../"</script>';
}
function GenPass($nameeng,$birthday){
	//echo substr(strtolower($nameeng),0,3).date("d",strtotime(dateSQL($birthday))).date("m",strtotime(dateSQL($birthday)));
	$NewPass = md5(substr(strtolower($nameeng),0,3).date("d",strtotime(dateSQL($birthday))).date("m",strtotime(dateSQL($birthday))));

	return $NewPass;

}
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$sql1 = "INSERT INTO uselog SET uKey = '".$_SESSION['ukey']."',MenuKey='".$_POST['MenuCase']."',MenuName = '".$MenuHead['MenuName']."'";
	$addLog = MySQLInsert($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if ($_GET['a'] == 'read'){
	$sql1 = "SELECT T0.uKey,T0.EmpCode,T0.uName,T0.uLastName,T0.uNickName,T1.DeptCode,
				    T1.PositionName,T2.DeptName 
			 FROM users T0
  				  LEFT JOIN positions T1 ON T1.LvCode = T0.LvCode
  				  LEFT JOIN  Departments T2 ON T1.DeptCode = T2.DeptCode
			 WHERE T0.UserStatus = 'A'	
			 ORDER BY T2.DeptCode,T1.LvCode";
	$getEmp = MySQLSelectX($sql1);	
	$i=0;	 
	while ($EmpList = mysqli_fetch_array($getEmp)){
		$i++;
		$output .= "<tr> 
						<td class='text-center'>".$i."</td>
						<td class='text-center'>".$EmpList['EmpCode']."</td>
						<td>".$EmpList['uName']." ".$EmpList['uLastName']." (".$EmpList['uNickName'].")</td>
						<td>".$EmpList['PositionName']."</td>
						<td>".$EmpList['DeptName']."</td>
						<td class='text-center'>
							<button type='button' class='btn-edit btn btn-info btn-xs' data-UserKey='".$EmpList['uKey']."' data-Dept='".$EmpList['DeptCode']."'><i class='fas fa-user-edit'></i></button>
							<button type='button' class='btn-resign btn btn-danger btn-xs' data-UserKey='".$EmpList['uKey']."'><i class='fas fa-user-times'></i></button>
							<button type='button' class='btn-reset btn btn-dark btn-xs' data-UserKey='".$EmpList['uKey']."'><i class='fas fa-unlock-alt'></i></button>
						</td>
				   	</tr>";
	}
}

if ($_GET['a'] == 'posi'){
	$sql1 = "SELECT * FROM positions WHERE DeptCode = '".$_POST['DeptCode']."' ORDER BY LvCode";
	$getPosi = MySQLSelectX($sql1);	
	$i=0;	 
	while ($PosiList = mysqli_fetch_array($getPosi)){
		$i++;
        $arrCol[$i]['LvCode'] = $PosiList['LvCode'];
        $arrCol[$i]['PosiName'] = $PosiList['PositionName'];
	}
	$arrCol['Loop'] = $i;
}

if ($_GET['a'] == 'hrmi'){
	$sql1 = "SELECT P0.*,P1.OrgUnitCode
			 FROM (SELECT T1.EmpCode,T1.MemberCardExcept,T0.FirstName,T0.LastName,T0.NickName,T0.FirstNameEng,T0.LastNameEng,T0.BirthDate,T0.Gender,T1.WorkingStatus,T1.ShiftID,T1.StartDate,
						  (SELECT TOP 1 D1.OrgUnitID 
						   FROM hrEmpWorkProfile D1
						   WHERE T1.EmpID = D1.EmpID  AND D1.EndDate IS NULL AND  D1.IsDeleted != 'TRUE' 
						   ORDER BY D1.ModifiedDate DESC ) AS OrgUnitID,
						   CASE WHEN T1.WorkingStatus IN ('Resign','LayOff') THEN 'I' ELSE 'A' END AS EmpStatus
				   FROM emPerson T0
						LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
				   WHERE  T0.IsDeleted != 'TRUE' AND T1.EmpCode NOT LIKE 'B%'  AND  T1.EmpCode = '".$_POST['EmpCode']."') P0
			 JOIN emOrgUnit P1 ON P0.OrgUnitID = P1.OrgUnitID";
// echo $sql1;
	$getEmp = HRMISelect($sql1) ;
	$EmpData = odbc_fetch_array($getEmp);
	$arrCol[0]['fname'] = conutf8($EmpData['FirstName']);
	$arrCol[0]['lname'] = conutf8($EmpData['LastName']);
	$arrCol[0]['nname'] = conutf8($EmpData['NickName']);
	$arrCol[0]['uname'] = strtolower($EmpData['FirstNameEng'].".".substr($EmpData['LastNameEng'],0,1));
	$arrCol[0]['efname'] = strtolower($EmpData['FirstNameEng']);
	$arrCol[0]['elname'] = strtolower($EmpData['LastNameEng']);
	$arrCol[0]['bdate'] = date("Y-m-d",strtotime($EmpData['BirthDate']));
	$arrCol[0]['Gender'] = strtoupper(substr($EmpData['Gender'],0,1));
	$arrCol[0]['DeptCode'] = $EmpData['OrgUnitCode'];
	$arrCol[0]['EmpStatus'] = $EmpData['EmpStatus'];
	$PCode = SapTHSearch(conutf8($EmpData['FirstName']));

	$sql1 = "SELECT CardCode,CardName FROM OCRD WHERE CardCode LIKE 'P-%' AND CardName LIKE N'%$PCode%' ORDER BY CardCode DESC";
	//echo ChkRowSAP($sql1)." ".$sql1;
	$i=0;
	if (ChkRowSAP($sql1) >0) {
		$getCard = SAPSelect($sql1);
		while ($CardCode = odbc_fetch_array($getCard)){
			$i++;
			$arrCol[$i]['CardCode'] = $CardCode['CardCode'];
			$arrCol[$i]['CardName'] = conutf8($CardCode['CardName']);
		}
	}
	$arrCol[0]['Loop'] = $i;

}
if ($_GET['a'] == 'add'){
	$con = "N";
	switch ($_POST['TypeCommand']){
		case "0" : // AddNew
			if (CHKRowDB("SELECT uKey FROM users WHERE EmpCode = '".$_POST['EmpCode']."' ") > 0) {
				$output = "รายชื่อพนักงานซ้ำ";
			}else{
				$uKey = md5(addslashes($_POST['en_name']).date("YmdHis"));
				$sql1 = "SELECT uClass FROM positions WHERE LvCode = '".$_POST['positions']."'";
				$uClass = MySQLSelect($sql1);	
				switch ($uClass['uClass']){
					case 24 :
					case 25 :
					case 26 :
						$UserName = $_POST['EmpCode'];
						break;
					default :
						$UserName = $_POST['UserName'];
					break;

				}
				if ($_FILES['UserSign']['name'] != ""){
					$tmpFilePath = $_FILES['UserSign']['tmp_name'];
					$filetype = substr($_FILES['UserSign']['name'],-4);
					$newFilePath = "../../../../image/user/".$_POST['EmpCode'].".".$filetype;
					$UserSign = $_POST['EmpCode'].".".$filetype;
					move_uploaded_file($tmpFilePath, $newFilePath);
				}else{
					$UserSign = "";
				}

				if(isset($_POST['savemoney'])) {
					$SaveMoney = "CodeSAP = '".$_POST['savemoney']."',";
				} else {
					$SaveMoney = "";
				}

				if(isset($_POST['OwnerCode'])) {
					$OwnerCode = "OwnerCode = '".$_POST['OwnerCode']."',";
				} else {
					$OwnerCode = "";
				}


				$UserPass = GenPass($_POST['en_name'],$_POST['user_birthdate']);
				$sql1 = "INSERT INTO users SET uKey = '".$uKey."',
				                               EmpCode = '".$_POST['EmpCode']."',
											   uName = '".$_POST['uName']."',
											   uLastName = '".$_POST['uLastName']."',
											   uNickName = '".$_POST['uNickName']."',
											   UserName = '".$UserName."',
											   UserPass = '".$UserPass."',
											   LvCode = '".$_POST['positions']."',
											   UserSign = '".$UserSign."',
											   UserCreate = '".$_SESSION['ukey']."',
											   UserUpdate = '".$_SESSION['ukey']."',
											   $SaveMoney
											   $OwnerCode
											   UserGender = '".$_POST['UserGender']."'";
				MySQLInsert($sql1);
				if (CHKRowDB("SELECT * FROM users WHERE EmpCode = '".$_POST['EmpCode']."' ")  == 0){
					$output = "เกิดข้อผิดพลาด กรุณาลองใหม่";
				}else{
					$output = "เพิ่มข้อมูลพนักงาน ".$_POST['EmpCode']." - ".$_POST['uName']." ".$_POST['uLastName']." เรียบร้อยแล้ว";
				}
			}
			break;
		default : // Edit
			if (substr($_POST['positions'],0,2) != 'LV'){
				$output = "เกิดข้อผิดพลาด กรุณาเลือกตำแหน่ง";
			}else{
				$sql1 = "SELECT uClass FROM positions WHERE LvCode = '".$_POST['positions']."'";
				$uClass = MySQLSelect($sql1);	
				switch ($uClass['uClass']){
					case 24 :
					case 25 :
					case 26 :
						$UserName = $_POST['EmpCode'];
						break;
					default :
						$UserName = $_POST['UserName'];
					break;

				}
				if ($_FILES['UserSign']['name'] != ""){
					$tmpFilePath = $_FILES['UserSign']['tmp_name'];
					$filetype = substr($_FILES['UserSign']['name'],-4);
					$newFilePath = "../../../../image/user/".$_POST['EmpCode'].".".$filetype;
					move_uploaded_file($tmpFilePath, $newFilePath);
					$UserSign = " UserSign = '".$_POST['EmpCode'].".".$filetype."',";
				}else{
					$UserSign = "";
				}
				$uKey = $_POST['uKey'];

				$SQL = "";
				$sql1 = "UPDATE users 
						SET uName = '".$_POST['uName']."',
							uLastName = '".$_POST['uLastName']."',
							uNickName = '".$_POST['uNickName']."',
							UserName = '".$UserName."',
							LvCode = '".$_POST['positions']."',
							".$UserSign."
							UserUpdate = '".$_SESSION['ukey']."',
							UserGender = '".$_POST['UserGender']."',
							OwnerCode = ".$_POST['OwnerCode']."
						WHERE uKey = '".$_POST['uKey']."'";
				MySQLUpdate($sql1);
				$output = "บันทึกข้อมูลเรียบร้อยแล้ว";
			}

		break;
	}
	
}
if ($_GET['a'] == 'edit'){
	$sql1 = "SELECT T0.uKey,T0.EmpCode,T0.uName,T0.uLastName,T0.uNickName,T0.UserName,T0.UserGender,T1.DeptCode,T0.LvCode,T1.PositionName,T0.CodeSAP,
	                CASE WHEN T0.CodeSAP LIKE 'P-%' THEN 'Y' ELSE 'N' END AS DataSAP, T0.OwnerCode
			 FROM users T0
			 	  JOIN positions T1 ON T0.LvCode = T1.LvCode
			 WHERE T0.uKey = '".$_POST['x']."'";
			 //echo $sql1;
	$DataEmp = MySQLSelect($sql1);

	$sql1 = "SELECT P0.*,P1.OrgUnitCode
	FROM (SELECT T1.EmpCode,T1.MemberCardExcept,T0.FirstName,T0.LastName,T0.NickName,T0.FirstNameEng,T0.LastNameEng,T0.BirthDate,T0.Gender,T1.WorkingStatus,T1.ShiftID,T1.StartDate,
				 (SELECT TOP 1 D1.OrgUnitID 
				  FROM hrEmpWorkProfile D1
				  WHERE T1.EmpID = D1.EmpID  AND D1.EndDate IS NULL AND  D1.IsDeleted != 'TRUE' 
				  ORDER BY D1.ModifiedDate DESC ) AS OrgUnitID,
				  CASE WHEN T1.WorkingStatus IN ('Resign','LayOff') THEN 'I' ELSE 'A' END AS EmpStatus
		  FROM emPerson T0
			   LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
		  WHERE  T0.IsDeleted != 'TRUE' AND T1.EmpCode NOT LIKE 'B%'  AND  T1.EmpCode = '".$DataEmp['EmpCode']."') P0
	JOIN emOrgUnit P1 ON P0.OrgUnitID = P1.OrgUnitID";
	//echo $sql1;
	$getEmp = HRMISelect($sql1) ;
	$EmpData = odbc_fetch_array($getEmp);
	$arrCol['fname'] = $DataEmp['uName'];
	$arrCol['lname'] = $DataEmp['uLastName'];
	$arrCol['nname'] = $DataEmp['uNickName'];
	$arrCol['uname'] = $DataEmp['UserName'];
	$arrCol['efname'] = strtolower($EmpData['FirstNameEng']);
	$arrCol['elname'] = strtolower($EmpData['LastNameEng']);
	$arrCol['bdate'] = date("Y-m-d",strtotime($EmpData['BirthDate']));
	$arrCol['Gender'] = $DataEmp['UserGender'];
	$arrCol['DeptCode'] = $DataEmp['DeptCode'];
	$arrCol['LvCode'] = $DataEmp['LvCode'];
	$arrCol['EmpCode'] = $DataEmp['EmpCode'];
	$arrCol['uKey'] = $DataEmp['uKey'];
	$arrCol['PosiName'] = $DataEmp['PositionName'];
	$arrCol['SAPCode'] = $DataEmp['CodeSAP'];
	$arrCol['Money'] = '-';
	if ($DataEmp['DataSAP'] == 'Y' && ($_SESSION['uClass'] == 0 ||$_SESSION['uClass'] == 29 || $_SESSION['uClass'] == 57 || $_SESSION['uClass'] == 59 || $_SESSION['uClass'] == 30)){
		$sql1 = "SELECT Balance FROM OCRD WHERE CardCode = '".$DataEmp['CodeSAP']."'";
		$getSAP = SAPSelect($sql1) ;
		$SAPData = odbc_fetch_array($getSAP);
		//$arrCol['SAPCode'] = $DataEmp['CodeSAP'];
		$arrCol['Money'] = number_format($SAPData['Balance'],2);
	}
	 
	// OwnerCode
	$SQL = "SELECT T0.empID, T0.lastname AS 'FirstName', T0.FirstName AS 'LastName' FROM OHEM T0 ORDER BY T0.LastName ASC";
	$QRY = SAPSelect($SQL);
	$OwnerCode = "<option value='-1'>กรุณาเลือก</option>";
	while($RST = odbc_fetch_array($QRY)) {
		$OwnerCode .= "<option value='".$RST['empID']."'>".conutf8($RST['FirstName'])." ".conutf8($RST['LastName'])."</option>";
	}
	$arrCol['OwnerCode'] = $OwnerCode;
	$arrCol['OwnerCodeUser'] = $DataEmp['OwnerCode'];
}
if ($_GET['a'] == 'recon'){
	switch ($_POST['TypeCon']){
		case 'resign' :
			$sql1 = "UPDATE users SET UserStatus = 'I',UserResign = '".$_SESSION['ukey']."',ResignDate = NOW() WHERE uKey = '".$_POST['uKey']."'";
			MySQLUpdate($sql1);
			$output = "กำหนดให้พนักงานพ้นสภาพเรียบร้อยแล้ว";
		break;
		case 'reset' :
			$sql1 = "SELECT EmpCode FROM users WHERE uKey = '".$_POST['uKey']."'";
			$MySQLData = MySQLSelect($sql1);
			
			$sql1 = "SELECT T0.FirstNameEng,T0.BirthDate
				     FROM emPerson T0
					      LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
					 WHERE  T1.EmpCode = '".$MySQLData['EmpCode']."'";
			$getEmp = HRMISelect($sql1) ;
			$EmpData = odbc_fetch_array($getEmp);
			$UserPass = GenPass($EmpData['FirstNameEng'],$EmpData['BirthDate']);
			$sql1 = "UPDATE users SET UserPass = '".$UserPass."',UserUpdate = '".$_SESSION['ukey']."', LastUpdate = NOW() WHERE uKey = '".$_POST['uKey']."'";
			MySQLUpdate($sql1);
			$output = "เปลี่ยนรหัสผ่านเรียบร้อยแล้ว";
		break;

	}
}


$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
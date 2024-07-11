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
require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}
if ($_GET['a'] == 'data'){
	$thisMonth = $_POST['xMonth'];
	$thisYear = $_POST['xYear'];
	$sql1 = "SELECT A0.buy_market, A0.buy_branch, A1.CardName,
				SUM(A0.PC) AS 'PC', SUM(A0.User) AS 'User',
				CASE WHEN (SUM(A0.PC) + SUM(A0.User) >= 10) THEN (SUM(A0.PC) + SUM(A0.User)) * 2 ELSE 0 END 'Insentive'
			FROM (
			SELECT
				T0.buy_market, T0.buy_branch,
				CASE WHEN status = 'PC' THEN 1 ELSE 0 END AS 'PC',
				CASE WHEN status = 'User' THEN 1 ELSE 0 END AS 'User'
			FROM wrt_buyproducts T0
			WHERE YEAR(T0.buy_timestamp) = $thisYear AND MONTH(T0.buy_timestamp) = $thisMonth
			) A0
			LEFT JOIN OCRD A1 ON A0.buy_branch = A1.CardCode
			GROUP BY A0.buy_market, A0.buy_branch
			ORDER BY A0.buy_market ASC, A0.buy_branch";
			
	switch($_POST['tabno']){
		case '1' :
			$UserProfile = "<div class='row'>
								<div class='col-lg-8'>
									<div class='form-floating mb-3'>
										<input type='text' name='uName' id='uName' class='form-control' placeholder='ชื่อ' />
										<label for='uName'>ชื่อ-นามสกุล</label>
									</div>
								</div>
								<div class='col-lg-2'>
									<div class='form-floating mb-3'>
										<input type='number' name='UserName' id='uAge' class='form-control' placeholder='Username'  readonly/>
										<label for='UserName'>อายุ</label>
									</div>
								</div>
								<div class='col-lg-2'>
									<div class='form-floating mb-3'>
										<select class='form-select' id='UserGender' name='UserGender' disabled >
											<option value='0'></option>
											<option value='M'>ชาย</option>
											<option value='F'>หญิง</option>
										</select>
										<label for='UserGender'>เพศ</label>
									</div>
								</div>
							</div>
							<div class='row'>
								<div class='col-lg-5'>
									<div class='form-floating mb-3'>
										<input type='text' name='uPhone' id='uPhone' class='form-control' placeholder=''  >
										<label for='name'>เบอร์โทรศัพท์</label>
									</div>
								</div>
								<div class='col-lg-3'>
									<div class='form-floating mb-3'>
										<input type='text' name='uLine' id='uLine' class='form-control' placeholder='LineID'  readonly/>
										<label for='lastname'>LineID</label>
									</div>
								</div>
								<div class='col-lg-3'>
									<div class='form-floating mb-3'>
										<input type='text' name='Carreer' id='Carreer' class='form-control' placeholder='อาชีพ'  readonly/>
										<label for='UserName'>อาชีพ</label>
									</div>
								</div>
								<div class='col-lg-1'>
									<div class='form-floating mb-3'>
										<button type='button' class='text-center btn btn-primary btn-sm w-100' id='btn_search' onclick='SearchBox(0);'><i class='fas fa-search fa-fw fa-3x'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
									</div>
								</div>
							</div>";
			$arrCol['UserProfile'] = $UserProfile;
			break;
		case '2' :
			$output = "";
			break;
		case '3' :
			$getCusProfile = MySQLSelectX($sql1);
			$a=0;
			$tmpShop = "0";
			//echo $sql1;
			while ($ItemList = mysqli_fetch_array($getCusProfile)){
				if ($tmpShop != $ItemList['buy_market']){
					// echo $ItemList['buy_market']." | ".strlen($ItemList['buy_market'])." = ".strlen(intval($ItemList['buy_market']))."\n";
					if (strlen($ItemList['buy_market']) == strlen(intval($ItemList['buy_market']))){
						$sql1 = "SELECT * FROM OCQG WHERE GroupCode = '".$ItemList['buy_market']."'";
						// echo $sql1."\n";
						if (ChkRowSAP($sql1) > 0){
							$getGroup = SAPSelect($sql1);
							$GroupData = odbc_fetch_array($getGroup);
							$NameShop = conutf8($GroupData['GroupName']);
							$CardName = $ItemList['CardName'];
						}else{
							$NameShop = $ItemList['buy_market'];
							$CardName = $ItemList['buy_market'];
						}
					}else{

						$NameShop = $ItemList['buy_market'];
						$CardName = $ItemList['buy_market'];
					}
					$tmpShop = $ItemList['buy_market'];
				}else{
					$CardName = $ItemList['CardName'];

				}

				//echo substr($ItemList['buy_branch'],0,2)."<br>";
				switch (substr($ItemList['buy_branch'],0,2)){
					case 'C-' :
					case 'K-' :
					case 'M-' :
					case 'MH' :
						$sql1 = "SELECT T0.CardCode,T1.U_Dim1 FROM OCRD T0 LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode WHERE T0.CardCode = '".$ItemList['buy_branch']."'";
						$getCH = SAPSelect($sql1);
						$CHData = odbc_fetch_array($getCH);
						$CH = $CHData['U_Dim1'];
						break;
					default :
						$CH = "";
					break;


				}
				//echo $CH."<br>";

				if ($CH == 'TT1'){
					$a++;
					$output .=  "<tr>
									<td>$a</td>
									<td>".$NameShop."</td>
									<td class='text-center'>10</td>
									<td>".$CardName."</td>
									<td class='text-right'>".number_format($ItemList['PC'])."</td> 
									<td class='text-right'>".number_format($ItemList['User'])."</td>
									<td class='text-right'>".number_format($ItemList['Insentive'])."</td>
								</tr>";

				}
					
			}

			break;
		case '4' :
			$getCusProfile = MySQLSelectX($sql1);
			$a=0;
			$tmpShop = "0";
			//echo $sql1;
			while ($ItemList = mysqli_fetch_array($getCusProfile)){
				// echo "'".$ItemList['buy_branch']."',";
				if ($tmpShop != $ItemList['buy_market']){
					// echo $ItemList['buy_market']." | ".strlen($ItemList['buy_market'])." = ".strlen(intval($ItemList['buy_market']))."\n";
					// intval($ItemList['buy_market']) >0
					if (strlen($ItemList['buy_market']) == strlen(intval($ItemList['buy_market']))){
						$sql1 = "SELECT * FROM OCQG WHERE GroupCode = '".$ItemList['buy_market']."'";
						if (ChkRowSAP($sql1) > 0){
							$getGroup = SAPSelect($sql1);
							$GroupData = odbc_fetch_array($getGroup);
							$NameShop = conutf8($GroupData['GroupName']);
							$CardName = $ItemList['CardName'];
						}else{
							$NameShop = $ItemList['buy_market'];
							$CardName = $ItemList['buy_market'];
						}
					}else{

						$NameShop = $ItemList['buy_market'];
						$CardName = $ItemList['buy_market'];
					}
					$tmpShop = $ItemList['buy_market'];
				}else{
					$CardName = $ItemList['CardName'];

				}

				//echo substr($ItemList['buy_branch'],0,2)."<br>";
				switch (substr($ItemList['buy_branch'],0,2)){
					case 'C-' :
					case 'K-' :
					case 'M-' :
					case 'MH' :
						$sql1 = "SELECT T0.CardCode,T1.U_Dim1 FROM OCRD T0 LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode WHERE T0.CardCode = '".$ItemList['buy_branch']."'";
						$getCH = SAPSelect($sql1);
						$CHData = odbc_fetch_array($getCH);
						$CH = $CHData['U_Dim1'];
						break;
					default :
						$CH = "";
					break;


				}
				//echo $CH."<br>";

				if ($CH == 'MT1'){
					$a++;
					$output .=  "<tr>
									<td>$a</td>
									<td>".$NameShop."</td>
									<td class='text-center'>10</td>
									<td>".$CardName."</td>
									<td class='text-right'>".number_format($ItemList['PC'])."</td> 
									<td class='text-right'>".number_format($ItemList['User'])."</td>
									<td class='text-right'>".number_format($ItemList['Insentive'])."</td>
								</tr>";

				}
					
			}
			break;
		case '5' :
			$getCusProfile = MySQLSelectX($sql1);
			$a=0;
			$tmpShop = "0";
			//echo $sql1;
			while ($ItemList = mysqli_fetch_array($getCusProfile)){
				if ($tmpShop != $ItemList['buy_market']){
					if (strlen($ItemList['buy_market']) == strlen(intval($ItemList['buy_market']))){
						$sql1 = "SELECT * FROM OCQG WHERE GroupCode = '".$ItemList['buy_market']."'";
						if (ChkRowSAP($sql1) > 0){
							$getGroup = SAPSelect($sql1);
							$GroupData = odbc_fetch_array($getGroup);
							$NameShop = conutf8($GroupData['GroupName']);
							$CardName = $ItemList['CardName'];
						}else{
							$NameShop = $ItemList['buy_market'];
							$CardName = $ItemList['buy_market'];
						}
					}else{

						$NameShop = $ItemList['buy_market'];
						$CardName = $ItemList['buy_market'];
					}
					$tmpShop = $ItemList['buy_market'];
				}else{
					$CardName = $ItemList['CardName'];

				}

				//echo substr($ItemList['buy_branch'],0,2)."<br>";
				switch (substr($ItemList['buy_branch'],0,2)){
					case 'C-' :
					case 'K-' :
					case 'M-' :
					case 'MH' :
						$sql1 = "SELECT T0.CardCode,T1.U_Dim1 FROM OCRD T0 LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode WHERE T0.CardCode = '".$ItemList['buy_branch']."'";
						$getCH = SAPSelect($sql1);
						$CHData = odbc_fetch_array($getCH);
						$CH = $CHData['U_Dim1'];
						break;
					default :
						$CH = "";
					break;


				}
				//echo $CH."<br>";

				if ($CH == 'MT2'){
					$a++;
					$output .=  "<tr>
									<td>$a</td>
									<td>".$NameShop."</td>
									<td class='text-center'>10</td>
									<td>".$CardName."</td>
									<td class='text-right'>".number_format($ItemList['PC'])."</td> 
									<td class='text-right'>".number_format($ItemList['User'])."</td>
									<td class='text-right'>".number_format($ItemList['Insentive'])."</td>
								</tr>";

				}
					
			}
			break;
		case '6' :
			$getCusProfile = MySQLSelectX($sql1);
			$a=0;
			$tmpShop = "0";
			//echo $sql1;
			while ($ItemList = mysqli_fetch_array($getCusProfile)){
				if ($tmpShop != $ItemList['buy_market']){
					if (strlen($ItemList['buy_market']) == strlen(intval($ItemList['buy_market']))){
						$sql1 = "SELECT * FROM OCQG WHERE GroupCode = '".$ItemList['buy_market']."'";
						if (ChkRowSAP($sql1) > 0){
							$getGroup = SAPSelect($sql1);
							$GroupData = odbc_fetch_array($getGroup);
							$NameShop = conutf8($GroupData['GroupName']);
							$CardName = $ItemList['CardName'];
						}else{
							$NameShop = $ItemList['buy_market'];
							$CardName = $ItemList['buy_market'];
						}
					}else{

						$NameShop = $ItemList['buy_market'];
						$CardName = $ItemList['buy_market'];
					}
					$tmpShop = $ItemList['buy_market'];
				}else{
					$CardName = $ItemList['CardName'];

				}

				//echo substr($ItemList['buy_branch'],0,2)."<br>";
				switch (substr($ItemList['buy_branch'],0,2)){
					case 'C-' :
					case 'K-' :
					case 'M-' :
					case 'MH' :
						$sql1 = "SELECT T0.CardCode,T1.U_Dim1 FROM OCRD T0 LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode WHERE T0.CardCode = '".$ItemList['buy_branch']."'";
						$getCH = SAPSelect($sql1);
						$CHData = odbc_fetch_array($getCH);
						$CH = $CHData['U_Dim1'];
						break;
					default :
						$CH = "";
					break;


				}
				//echo $CH."<br>";

				if ($CH == 'OUL' || $CH == 'TT1'){
					$a++;
					$output .=  "<tr>
									<td>$a</td>
									<td>".$NameShop."</td>
									<td class='text-center'>10</td>
									<td>".$CardName."</td>
									<td class='text-right'>".number_format($ItemList['PC'])."</td> 
									<td class='text-right'>".number_format($ItemList['User'])."</td>
									<td class='text-right'>".number_format($ItemList['Insentive'])."</td>
								</tr>";

				}
					
			}

			break;
		case '7' :
			$getCusProfile = MySQLSelectX($sql1);
			$a=0;
			$tmpShop = "0";
			//echo $sql1;
			while ($ItemList = mysqli_fetch_array($getCusProfile)){
				if ($tmpShop != $ItemList['buy_market']){
					if (strlen($ItemList['buy_market']) == strlen(intval($ItemList['buy_market']))){
						$sql1 = "SELECT * FROM OCQG WHERE GroupCode = '".$ItemList['buy_market']."'";
						if (ChkRowSAP($sql1) > 0){
							$getGroup = SAPSelect($sql1);
							$GroupData = odbc_fetch_array($getGroup);
							$NameShop = conutf8($GroupData['GroupName']);
							$CardName = $ItemList['CardName'];
						}else{
							$NameShop = $ItemList['buy_market'];
							$CardName = $ItemList['buy_market'];
						}
					}else{

						$NameShop = $ItemList['buy_market'];
						$CardName = $ItemList['buy_market'];
					}
					$tmpShop = $ItemList['buy_market'];
				}else{
					$CardName = $ItemList['CardName'];

				}

				//echo substr($ItemList['buy_branch'],0,2)."<br>";
				switch (substr($ItemList['buy_branch'],0,2)){
					case 'C-' :
					case 'K-' :
					case 'M-' :
					case 'MH' :
						$sql1 = "SELECT T0.CardCode,T1.U_Dim1 FROM OCRD T0 LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode WHERE T0.CardCode = '".$ItemList['buy_branch']."'";
						$getCH = SAPSelect($sql1);
						$CHData = odbc_fetch_array($getCH);
						$CH = $CHData['U_Dim1'];
						break;
					default :
						$CH = "";
					break;


				}
				//echo $CH."<br>";

				switch ($CH){
					case 'MT1' :
					case 'MT2' :
					case 'TT1' :
					case 'TT2' :
					case 'OUL' :
						break;
					default :
						$a++;
						$output .=  "<tr>
										<td>$a</td>
										<td>".$NameShop."</td>
										<td class='text-center'>10</td>
										<td>".$CardName."</td>
										<td class='text-right'>".number_format($ItemList['PC'])."</td> 
										<td class='text-right'>".number_format($ItemList['User'])."</td>
									</tr>";
						break;
				}
					
			}
			break;

	}
}

if ($_GET['a'] == 'search'){
	$wh = " ";
	$st = 0;
	/*
	$uName = " profile_name LIKE '%".$_POST['uName']."%' ";
	$xName = " profile_name ";
	$uAge = $_POST['uAge'];
	switch (substr($uAge,0,1)){
		case '1' :
			$uAge = " profile_age BETWEEN 10 AND 19 ";
			break;
		case '2' :
			$uAge = " profile_age BETWEEN 20 AND 29 ";
			break;
		case '3' :
			$uAge = " profile_age BETWEEN 30 AND 39 ";
			break;
		case '4' :
			$uAge = " profile_age BETWEEN 40 AND 49 ";
			break;
		case '5' :
			$uAge = " profile_age BETWEEN 50 AND 59 ";
			break;
		case '6' :
			$uAge = " profile_age BETWEEN 60 AND 69 ";
			break;
		case '7' :
			$uAge = " profile_age BETWEEN 70 AND 79 ";
			break;
		case '8' :
			$uAge = " profile_age BETWEEN 80 AND 89 ";
			break;
		case '9' :
			$uAge = " profile_age BETWEEN 80 AND 99 ";
			break;
		default :
			$uAge = " profile_age > 99 ";
			break;
	}
	$uGen = " profile_gender = '".$_POST['uGen']."' ";
	$uPhone = " profile_phone = '".str_replace("-","",str_replace(" ","",$_POST['uPhone']))."' ";
	$uLine = " profile_linID LIKE '%".$_POST['uLine']."'%";
	$uCar = " profile_job LIKE '%".$_POST['uCar']."%' ";

	if (strlen($_POST['uName']) >  0){
		$wh = $uName;
		$st = 1;
	}
	if ($_POST['uAge'] > 10){
		if ($st == 1){
			$wh .= " AND ".$uAge;
		}else{
			$wh = $uAge;
			$st = 1;
		}
	}
	if ($_POST['uGen'] != '0'){
		if ($st == 1){
			$wh .= " AND ".$uGen;
		}else{
			$wh = $Gen;
			$st = 1;
		}
	}
	if (strlen($_POST['uPhone']) > 0){
		if ($st == 1){
			$wh .= " AND ".$uPhone;
		}else{
			$wh = $uPhone;
			$st = 1;
		}
		
	}
	if (strlen($_POST['uLine']) > 0){
		if ($st == 1){
			$wh .= " AND ".$uLine;
		}else{
			$wh = $uLine;
			$st = 1;
		}
	}
	if (strlen($_POST['uCar']) > 0){
		if ($st == 1){
			$wh .= " AND ".$uCar;
		}else{
			$wh = $uCar;
			$st = 1;
		}
	}
	*/

	$uName = " profile_name LIKE '%".$_POST['uName']."%' ";
	$uPhone = " profile_phone = '".str_replace("-","",str_replace(" ","",$_POST['uPhone']))."' ";
	if (strlen($_POST['uName']) >  0){
		$wh = $uName;
		$st = 1;
	}
	//echo $st;
	if (strlen($_POST['uPhone']) > 0){
		if ($st == 1){
			$wh .= " AND ".$uPhone;
		}else{
			$wh = $uPhone;
			$st = 1;
		}
		
	}

	$sql1 = "SELECT DISTINCT profile_name,profile_phone FROM wrt_profile WHERE ".$wh;
	//echo $sql1;
	if (CHKRowDB($sql1) == 1){
		$arrCol['muti'] = 1;
		$CusPhone = MySQLSelect($sql1);
		$sql1 = "SELECT * FROM wrt_profile WHERE profile_name = '".$CusPhone['profile_name']."'AND profile_phone = '".$CusPhone['profile_phone']."' ORDER BY profile_timestamp DESC";

		$getCusProfile = MySQLSelectX($sql1);
		$a=0;
		$IDList = "(";
		while ($CusProfile = mysqli_fetch_array($getCusProfile)){
			if ($a==0){
				$arrCol['uName'] = $CusProfile['profile_name'];
				$arrCol['uAge'] = $CusProfile['profile_age'];
				$arrCol['uGen'] = strtoupper(substr($CusProfile['profile_gender'],0,1));
				$arrCol['uPhone'] = $CusProfile['profile_phone'];
				$arrCol['uLine'] = $CusProfile['profile_lineId'];
				switch($CusProfile['profile_job']){
					case 'official':
						$uJob = "ข้าราชการ";
						break;
					case 'employee':
						$uJob = "พนักงานเอกชน";
						break;
					case 'student':
						$uJob = "";
						break;
					case 'contractor':
						$uJob = "ผู้รับเหมาก่อสร้าง";
						break;
					case 'business':
						$uJob = "ธุรกิจส่วนตัว";
						break;
					case 'farmer':
						$uJob = "เกษตรกร";
						break;
					default :
						$uJob = " ";
						break;
				}
				$arrCol['uCar'] = $uJob;
				$a++;
			}
			$IDList .= $CusProfile['profile_id'].",";
		}

		$IDList = substr($IDList,0,-1).")";
		$sql1 = "SELECT T0.*,
						CASE WHEN T0.buy_branch LIKE 'C-%' THEN T1.CardName 
							WHEN T0.buy_branch LIKE 'M-%' THEN T1.CardName
							WHEN T0.buy_branch LIKE 'K-%' THEN T1.CardName
							WHEN T0.buy_branch LIKE 'MT-%' THEN T1.CardName
							ELSE T0.buy_market END AS CardName
				FROM wrt_buyproducts T0
					LEFT JOIN  OCRD T1 ON T0.buy_branch = T1.CardCode
				WHERE T0.profile_id IN  ".$IDList;
		//echo $sql1;
		$getWarranty = MySQLSelectX($sql1);
		$i=0;
		while ($CusWan = mysqli_fetch_array($getWarranty)){
			$i++;
			switch ($CusWan['buy_objective']){
				case 'contractor' :
					$useFor = "ใช้ในงานรับเหมา";
					break;
				case 'factory' :
					$useFor = "ใช้ในโรงงาน";
					break;
				case 'home' :
					$useFor = "ใช้งานภายในบ้าน";
					break;
				default :
					$useFor = "อื่นๆ";
					break;
			}
			$GName = "";
			
			$sql2 = "SELECT name FROM [SBO_KBI2023].[dbo].[@ITEMGROUP1] WHERE Code = '".$CusWan['buy_product']."'";
			//echo $sql2;
			if (ChkRowSAP($sql2) > 0){
				$getGroup = SAPSelect($sql2);
				$GroupData = odbc_fetch_array($getGroup);
				$GName = conutf8($GroupData['name']);
			}

			if ($CusWan['status'] == 'User'){
				$userClass = " style='background-color:#AFD092;font-weight: bold;' ";
			}else{
				$userClass = " ";

			}
			$output .= "<tr ".$userClass.">
							<td>$i</td>
							<td>".date("d/m/Y",strtotime($CusWan['buy_date']))."</td>
							<td>".$useFor."</td>
							<td>".$CusWan['CardName']."</td>
							<td>".$CusWan['buy_brand']."</td>
							<td>".$GName."</td>
							<td>".$CusWan['buy_nameProduct']."</td>
							<td>".$CusWan['buy_serialNumber']."</td>
							<td>".date("d/m/Y",strtotime($CusWan['buy_timestamp']))."</td>
			";
		}
	}else{
		$sql1 = "SELECT DISTINCT profile_name,profile_phone FROM wrt_profile WHERE ".$wh;
		$getCusList = MySQLSelectX($sql1);
		while ($CusProfile = mysqli_fetch_array($getCusList)){
			$output .= "<tr onclick=\"AddData('".$CusProfile['profile_name']."','".$CusProfile['profile_phone']."')\">
							<td><span  >".$CusProfile['profile_name']."</span></td>
							<td class='text-center'>".$CusProfile['profile_phone']."</td>
						</tr>";
		}
	}
	
}

function StrCell($c) {
	$StrCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
	return $StrCell;
}

if($_GET['a'] == 'Excel') {
	$Year = $_POST['Year'];
	$Month = (($_POST['Month']-1) < 10) ? "0".($_POST['Month']-1) : ($_POST['Month']-1);
	$Last = cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
	$SQL = 
		"SELECT
			T0.profile_timestamp, T0.profile_name,
			CASE WHEN T0.profile_gender = 'Male' THEN 'ชาย' ELSE 'หญิง' END AS 'profile_gender',
			CASE
				WHEN T0.profile_age <= 20 THEN 'ต่ำกว่า 20 ปี'
				WHEN T0.profile_age BETWEEN 21 AND 25 THEN '21 - 25 ปี'
				WHEN T0.profile_age BETWEEN 26 AND 30 THEN '26 - 30 ปี'
				WHEN T0.profile_age BETWEEN 31 AND 35 THEN '31 - 35 ปี'
				WHEN T0.profile_age BETWEEN 35 AND 40 THEN '35 - 40 ปี'
				WHEN T0.profile_age BETWEEN 41 AND 45 THEN '41 - 45 ปี' ELSE '45 ปี ขึ้นไป' END AS 'profile_age',
			T0.profile_phone, '' AS 'Address',
			CASE
				WHEN T0.profile_job = 'official' THEN 'ข้าราชการ / ทหาร / ตำรวจ'
				WHEN T0.profile_job = 'employee' THEN 'พนักงานเอกชน'
				WHEN T0.profile_job = 'student' THEN 'นักเรียน / นักศึกษา'
				WHEN T0.profile_job = 'contractor' THEN 'ผู้รับเหมาก่อสร้าง'
				WHEN T0.profile_job = 'business' THEN 'เจ้าของธุรกิจ / เจ้าของโรงงาน / ธุรกิจส่วนตัว'
				WHEN T0.profile_job = 'farmer' THEN 'เกษตรกร / ฟาร์มเลี้ยงสัตว์'
			ELSE '' END AS 'profile_job' ,          
			CASE
				WHEN T1.buy_objective = 'home' THEN '3. ใช้งานภายในบ้าน'
				WHEN T1.buy_objective = 'factory' THEN '2. ใช้ในโรงงาน'
			ELSE '1. ใช้ในงานรับเหมา' END AS 'buy_objective',
			CASE
				WHEN T1.buy_market NOT IN ('4','8','19','6','11','21','3','5','14','17','26','7','2') THEN T1.buy_market
				WHEN T1.buy_market IN ('4','8','19','6','11','21','3','5','14','17','26','7','2') THEN T2.CardName 
				ELSE '' END AS 'BuyMarket',
			'' AS 'province' , T1.buy_brand,
			CASE
				WHEN T1.buy_product = 'B00001' THEN 'HANDTOOL'
				WHEN T1.buy_product = 'B00003' THEN 'เกษตร'
				WHEN T1.buy_product = 'B00004' THEN 'เคมี/กาว'
				WHEN T1.buy_product = 'B00005' THEN 'เครื่องเชื่อม/ตู้ชาร์จ'
				WHEN T1.buy_product = 'B00006' THEN 'เครื่องมือไฟฟ้า'
				WHEN T1.buy_product = 'B00007' THEN 'เครื่องมือไฟฟ้าไร้สาย'
				WHEN T1.buy_product = 'B00008' THEN 'เครื่องมือทำความสะอาด'
				WHEN T1.buy_product = 'B00009' THEN 'เครื่องมือลม'
				WHEN T1.buy_product = 'B00010' THEN 'เครื่องมือวัด ชั่ง'
				WHEN T1.buy_product = 'B00011' THEN 'เครื่องมืออุตสาหกรรม/ก่อสร้าง'
				WHEN T1.buy_product = 'B00012' THEN 'โปรโมชั่น'
				WHEN T1.buy_product = 'B00013' THEN 'ใบเลื่อย เจียร์ ตัด'
				WHEN T1.buy_product = 'B00014' THEN 'ดอกเร้าเตอร์'
				WHEN T1.buy_product = 'B00015' THEN 'ดอกขัน เจาะ เจียร์ ตัด'
				WHEN T1.buy_product = 'B00016' THEN 'ตะปูยิง'
				WHEN T1.buy_product = 'B00017' THEN 'น็อต/สกรู'
				WHEN T1.buy_product = 'B00018' THEN 'ปั๊มลม'
				WHEN T1.buy_product = 'B00019' THEN 'ปืนยิงตะปู'
			ELSE '' END AS 'buy_product' ,
			T1.buy_nameproduct, '' AS 'photo_product', '' AS 'slip_product', T0.profile_email, T0.profile_lineid, T1.buy_serialNumber,
			CASE
				WHEN T1.status = 'PC'   THEN 'PC / พนักงานกรอกข้อมูลให้'
				WHEN T1.status = 'USER' THEN  'ลูกค้ากรอกข้อมูลด้วยตัวเอง'
			ELSE '' END AS 'Registrant'
		FROM wrt_profile T0
		LEFT JOIN wrt_buyproducts T1 ON T0.profile_id = T1.profile_id
		LEFT JOIN OCRD T2 ON T1.buy_branch = T2.CardCode
		WHERE T0.profile_timestamp BETWEEN '$Year-$Month-01 00:00:00' AND '$Year-$Month-$Last 23:59:59'";
	$QRY = MySQLSelectX($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานใบรับประกันสินค้า บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานใบรับประกันสินค้า บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

	// Style
	$PageHeader = [ 'font' => [ 'bold' => true, 'size' => 9.1 ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextCenterBold = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRightBold  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$Row = 1; $Col = 1;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "เวลาบันทึกข้อมูล"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(25);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ชื่อ"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(35);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "เพศ"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(12);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "อายุ");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "เบอร์"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(17);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ที่อยู่"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(35);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "อาชีพ"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(32);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "วัตถุประสงค์ในการซื้อสินค้า"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(32);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ชื่อร้านค้าหรือห้างสรรพสินค้าที่สั่งซื้อ"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(32);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "จังหวัด"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(20);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "แบรนด์สินค้า"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ประเภทสินค้าที่สั่งซื้อ"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(25);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ชื่อสินค้า"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(35);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "รูปสินค้า"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ที่อยู่สินค้า"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(30);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "อีเมล์"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(28);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ไลน์"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(15);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ซีเรียลนัมเบอร์"); 
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(17);
	$Col++;

	$sheet->setCellValueByColumnAndRow($Col, $Row, "ผู้ลงทะเบียน");
	$spreadsheet->getActiveSheet()->getColumnDimension(StrCell($Col))->setWidth(30);

	$spreadsheet->getActiveSheet()->getRowDimension($Row)->setRowHeight(18);
	$sheet->getStyle(StrCell(1).$Row.':'.StrCell($Col).$Row)->applyFromArray($PageHeader);

	while($RST = mysqli_fetch_array($QRY)) {
		$Row++; $Col = 1;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['profile_timestamp']);
		$sheet->getStyle(StrCell($Col).$Row)->applyFromArray($TextCenter);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['profile_name']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['profile_gender']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['profile_age']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['profile_phone']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['Address']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['profile_job']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['buy_objective']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['BuyMarket']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['province']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['buy_brand']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['buy_product']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['buy_nameproduct']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['photo_product']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['slip_product']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['profile_email']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['profile_lineid']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['buy_serialNumber']);
		$Col++;

		$sheet->setCellValueByColumnAndRow($Col, $Row, $RST['Registrant']);
		$Col++;
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานใบรับประกันสินค้า - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/Warranty/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'Warranty', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
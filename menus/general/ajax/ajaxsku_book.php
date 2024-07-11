<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

function gen14digit($bar,$UnitType){
	$newBar = $UnitType.substr($bar,0,-1);
	$chk=0;
	$RunCHK = 0;
    $chkBar = $newBar;
	for ($i=1;$i<=13;$i++){
		
        if (($i%2) != 0){
			$chk = intval(substr($chkBar,0,1))*3;
		}else{
			$chk = intval(substr($chkBar,0,1));
		}
        $RunCHK = $RunCHK + $chk;
        $chkBar = substr($chkBar,1);
	}
    $newBar = $newBar.fmod(10-fmod($RunCHK,10),10);
    return $newBar;
}

function Counttry() {
	$option = "
		<option value='' selected disabled>เลือกประเทศ</option> 
		<option value='AF'>Afghanistan</option> 
		<option value='AL'>Albania</option> 
		<option value='DZ'>Algeria</option> 
		<option value='AS'>American Samoa</option> 
		<option value='AD'>Andorra</option> 
		<option value='AO'>Angola</option> 
		<option value='AI'>Anguilla</option> 
		<option value='AQ'>Antarctica</option> 
		<option value='AG'>Antigua and Barbuda</option> 
		<option value='AR'>Argentina</option> 
		<option value='AM'>Armenia</option> 
		<option value='AW'>Aruba</option> 
		<option value='AU'>Australia</option> 
		<option value='AT'>Austria</option> 
		<option value='AZ'>Azerbaijan</option> 
		<option value='BS'>Bahamas</option> 
		<option value='BH'>Bahrain</option> 
		<option value='BD'>Bangladesh</option> 
		<option value='BB'>Barbados</option> 
		<option value='BY'>Belarus</option> 
		<option value='BE'>Belgium</option> 
		<option value='BZ'>Belize</option> 
		<option value='BJ'>Benin</option> 
		<option value='BM'>Bermuda</option> 
		<option value='BT'>Bhutan</option> 
		<option value='BO'>Bolivia</option> 
		<option value='BA'>Bosnia and Herzegovina</option> 
		<option value='BW'>Botswana</option> 
		<option value='BV'>Bouvet Island</option> 
		<option value='BR'>Brazil</option> 
		<option value='IO'>British Indian Ocean Territory</option> 
		<option value='BN'>Brunei Darussalam</option> 
		<option value='BG'>Bulgaria</option> 
		<option value='BF'>Burkina Faso</option> 
		<option value='BI'>Burundi</option> 
		<option value='KH'>Cambodia</option> 
		<option value='CM'>Cameroon</option> 
		<option value='CA'>Canada</option> 
		<option value='CV'>Cape Verde</option> 
		<option value='KY'>Cayman Islands</option> 
		<option value='CF'>Central African Republic</option> 
		<option value='TD'>Chad</option> 
		<option value='CL'>Chile</option> 
		<option value='CN'>PRC</option> 
		<option value='CX'>Christmas Island</option> 
		<option value='CC'>Cocos (Keeling) Islands</option> 
		<option value='CO'>Colombia</option> 
		<option value='KM'>Comoros</option> 
		<option value='CG'>Congo, Republic of</option> 
		<option value='CD'>Congo, The Democratic Republic of The</option> 
		<option value='CK'>Cook Islands</option> 
		<option value='CE'>Costa Rica</option> 
		<option value='CI'>Cote D'ivoire</option> 
		<option value='HR'>Croatia</option> 
		<option value='CU'>Cuba</option> 
		<option value='CY'>Cyprus</option> 
		<option value='CZ'>Czech Republic</option> 
		<option value='DK'>Denmark</option> 
		<option value='DJ'>Djibouti</option> 
		<option value='DM'>Dominica</option> 
		<option value='DO'>Dominican Republic</option> 
		<option value='EC'>Ecuador</option> 
		<option value='EG'>Egypt</option> 
		<option value='SV'>El Salvador</option> 
		<option value='GQ'>Equatorial Guinea</option> 
		<option value='ER'>Eritrea</option> 
		<option value='EE'>Estonia</option> 
		<option value='ET'>Ethiopia</option> 
		<option value='FK'>Falkland Islands (Malvinas)</option> 
		<option value='FO'>Faroe Islands</option> 
		<option value='FJ'>Fiji</option> 
		<option value='FI'>Finland</option> 
		<option value='FE'>France</option> 
		<option value='GF'>French Guiana</option> 
		<option value='PF'>French Polynesia</option> 
		<option value='TF'>French Southern Territories</option> 
		<option value='GA'>Gabon</option> 
		<option value='GM'>Gambia</option> 
		<option value='GE'>Georgia</option> 
		<option value='DE'>Germany</option> 
		<option value='GH'>Ghana</option> 
		<option value='GI'>Gibraltar</option> 
		<option value='GR'>Greece</option> 
		<option value='GL'>Greenland</option> 
		<option value='GD'>Grenada</option> 
		<option value='GP'>Guadeloupe</option> 
		<option value='GU'>Guam</option> 
		<option value='GT'>Guatemala</option> 
		<option value='GN'>Guinea</option> 
		<option value='GW'>Guinea-bissau</option> 
		<option value='GY'>Guyana</option> 
		<option value='HT'>Haiti</option> 
		<option value='HM'>Heard Island and Mcdonald Islands</option> 
		<option value='VA'>Holy See (Vatican City State)</option> 
		<option value='HN'>Honduras</option> 
		<option value='HK'>Hong Kong</option> 
		<option value='HU'>Hungary</option> 
		<option value='IS'>Iceland</option> 
		<option value='IN'>India</option> 
		<option value='ID'>Indonesia</option> 
		<option value='IR'>Iran, Islamic Republic of</option> 
		<option value='IQ'>Iraq</option> 
		<option value='IE'>Ireland</option> 
		<option value='IL'>Israel</option> 
		<option value='IT'>Italy</option> 
		<option value='JM'>Jamaica</option> 
		<option value='JP'>Japan</option> 
		<option value='JO'>Jordan</option> 
		<option value='KZ'>Kazakhstan</option> 
		<option value='KE'>Kenya</option> 
		<option value='KI'>Kiribati</option> 
		<option value='KP'>Korea, Democratic People's Republic of</option> 
		<option value='KR'>Korea, Republic of</option> 
		<option value='KW'>Kuwait</option> 
		<option value='KG'>Kyrgyzstan</option> 
		<option value='LA'>Lao People's Democratic Republic</option> 
		<option value='LV'>Latvia</option> 
		<option value='LB'>Lebanon</option> 
		<option value='LS'>Lesotho</option> 
		<option value='LR'>Liberia</option> 
		<option value='LY'>Libyan Arab Jamahiriya</option> 
		<option value='LI'>Liechtenstein</option> 
		<option value='LT'>Lithuania</option> 
		<option value='LU'>Luxembourg</option> 
		<option value='MO'>Macao</option> 
		<option value='MK'>Macedonia, The Former Yugoslav Republic of</option> 
		<option value='MG'>Madagascar</option> 
		<option value='MW'>Malawi</option> 
		<option value='MY'>Malaysia</option> 
		<option value='MV'>Maldives</option> 
		<option value='ML'>Mali</option> 
		<option value='MT'>Malta</option> 
		<option value='MH'>Marshall Islands</option> 
		<option value='MQ'>Martinique</option> 
		<option value='MR'>Mauritania</option> 
		<option value='MU'>Mauritius</option> 
		<option value='YT'>Mayotte</option> 
		<option value='MX'>Mexico</option> 
		<option value='FM'>Micronesia, Federated States of</option> 
		<option value='MD'>Moldova, Republic of</option> 
		<option value='MC'>Monaco</option> 
		<option value='MN'>Mongolia</option> 
		<option value='MS'>Montserrat</option> 
		<option value='MA'>Morocco</option> 
		<option value='MZ'>Mozambique</option> 
		<option value='MM'>Myanmar</option> 
		<option value='NA'>Namibia</option> 
		<option value='NR'>Nauru</option> 
		<option value='NP'>Nepal</option> 
		<option value='NL'>Netherlands</option> 
		<option value='AN'>Netherlands Antilles</option> 
		<option value='NC'>New Caledonia</option> 
		<option value='NZ'>New Zealand</option> 
		<option value='NI'>Nicaragua</option> 
		<option value='NE'>Niger</option> 
		<option value='NG'>Nigeria</option> 
		<option value='NU'>Niue</option> 
		<option value='NF'>Norfolk Island</option> 
		<option value='MP'>Northern Mariana Islands</option> 
		<option value='NO'>Norway</option> 
		<option value='OM'>Oman</option> 
		<option value='PK'>Pakistan</option> 
		<option value='PW'>Palau</option> 
		<option value='PS'>Palestinian Territory, Occupied</option> 
		<option value='PA'>Panama</option> 
		<option value='PG'>Papua New Guinea</option> 
		<option value='PY'>Paraguay</option> 
		<option value='PE'>Peru</option> 
		<option value='PH'>Philippines</option> 
		<option value='PN'>Pitcairn</option> 
		<option value='PL'>Poland</option> 
		<option value='PT'>Portugal</option> 
		<option value='PR'>Puerto Rico</option> 
		<option value='QA'>Qatar</option> 
		<option value='RE'>Reunion</option> 
		<option value='RO'>Romania</option> 
		<option value='RU'>Russian Federation</option> 
		<option value='RW'>Rwanda</option> 
		<option value='SH'>Saint Helena</option> 
		<option value='KN'>Saint Kitts and Nevis</option> 
		<option value='LC'>Saint Lucia</option> 
		<option value='PM'>Saint Pierre and Miquelon</option> 
		<option value='VC'>Saint Vincent and The Grenadines</option> 
		<option value='WS'>Samoa</option> 
		<option value='SM'>San Marino</option> 
		<option value='ST'>Sao Tome and Principe</option> 
		<option value='SA'>Saudi Arabia</option> 
		<option value='SN'>Senegal</option> 
		<option value='CS'>Serbia and Montenegro</option> 
		<option value='SC'>Seychelles</option> 
		<option value='SL'>Sierra Leone</option> 
		<option value='SG'>Singapore</option> 
		<option value='SK'>Slovakia</option> 
		<option value='SI'>Slovenia</option> 
		<option value='SB'>Solomon Islands</option> 
		<option value='SO'>Somalia</option> 
		<option value='ZA'>South Africa</option> 
		<option value='GS'>South Georgia and The South Sandwich Islands</option> 
		<option value='ES'>Spain</option> 
		<option value='LK'>Sri Lanka</option> 
		<option value='SD'>Sudan</option> 
		<option value='SR'>Suriname</option> 
		<option value='SJ'>Svalbard and Jan Mayen</option> 
		<option value='SZ'>Swaziland</option> 
		<option value='SE'>Sweden</option> 
		<option value='CH'>Switzerland</option> 
		<option value='SY'>Syrian Arab Republic</option> 
		<option value='TW'>Taiwan, Province of China</option> 
		<option value='TJ'>Tajikistan</option> 
		<option value='TZ'>Tanzania, United Republic of</option> 
		<option value='TH'>Thailand</option> 
		<option value='TL'>Timor-leste</option> 
		<option value='TG'>Togo</option> 
		<option value='TK'>Tokelau</option> 
		<option value='TO'>Tonga</option> 
		<option value='TT'>Trinidad and Tobago</option> 
		<option value='Tunisia'>TN</option> 
		<option value='TR'>Turkey</option> 
		<option value='TM'>Turkmenistan</option> 
		<option value='TC'>Turks and Caicos Islands</option> 
		<option value='TV'>Tuvalu</option> 
		<option value='UG'>Uganda</option> 
		<option value='UA'>Ukraine</option> 
		<option value='UA'>United Arab Emirates</option> 
		<option value='UK'>United Kingdom</option> 
		<option value='US'>United States</option> 
		<option value='UM'>United States Minor Outlying Islands</option> 
		<option value='UY'>Uruguay</option> 
		<option value='UZ'>Uzbekistan</option> 
		<option value='VU'>Vanuatu</option> 
		<option value='VE'>Venezuela</option> 
		<option value='VN'>Viet Nam</option> 
		<option value='VG'>Virgin Islands, British</option> 
		<option value='VI'>Virgin Islands, U.S.</option> 
		<option value='WF'>Wallis and Futuna</option> 
		<option value='EH'>Western Sahara</option> 
		<option value='YE'>Yemen</option> 
		<option value='ZM'>Zambia</option> 
		<option value='ZW'>Zimbabwe</option>
	";
	return $option;
}

if($_GET['a'] == 'CallData'){
	$ItemCode = $_POST['ItemCode'];
	$Ukey     = $_SESSION['ukey'];
	
	$Chk_HEADER = "SELECT * FROM skubook_header WHERE ItemCode = '$ItemCode'";
	if(CHKRowDB($Chk_HEADER) == 0) {
		$INSERT_HEADER = "INSERT INTO skubook_header SET ItemCode = '$ItemCode', CreateUkey = '$Ukey', CreateDate = NOW()";
		MySQLInsert($INSERT_HEADER);
		$arrCol['ItemColor']   = "";
		$arrCol['BoxColor']    = "";
		$arrCol['MadeOf']      = "";
		$arrCol['ProCountry']  = "";
		$arrCol['TeamCode']    = "";
	}

	// Header. ข้อมูลสินค้า
		$SQL_SAP = "
			SELECT T0.ItemCode, T0.ItemName, T0.FrgnName, T0.CodeBars, T1.Name AS NameType1, T2.Name AS NameType2, T3.CardName, T0.SalUnitMsr, T0.U_ProductStatus,T4.Name AS Brand,T5.Name AS Model
			FROM OITM T0
			LEFT JOIN dbo.[@ITEMGROUP1] T1 ON T1.Code = T0.U_Group1
			LEFT JOIN dbo.[@ITEMGROUP2] T2 ON T2.Code = T0.U_Group2 
			LEFT JOIN OCRD T3 ON T3.CardCode = T0.CardCode
			LEFT JOIN dbo.[@BRAND2] T4 ON T4.Code = T0.U_Brand2
			LEFT JOIN dbo.[@PROMOTION] T5 ON T5.Code = T0.U_Promotion_1
			WHERE T0.ItemCode = '$ItemCode'";
		$QRY_SAP = SAPSelect($SQL_SAP);
		$RST_SAP = odbc_fetch_array($QRY_SAP);
		$arrCol['ItemCode'] = $RST_SAP['ItemCode'];
		$arrCol['ItemName'] = conutf8($RST_SAP['ItemName']);
		$arrCol['ItemNameEng'] = conutf8($RST_SAP['FrgnName']);
		$arrCol['CodeBars'] = $RST_SAP['CodeBars'];
		$arrCol['U_Group1'] = conutf8($RST_SAP['NameType1']);
		$arrCol['U_Group2'] = conutf8($RST_SAP['NameType2']);
		$arrCol['CardName'] = conutf8($RST_SAP['CardName']);
		$arrCol['ProductStatus'] = conutf8($RST_SAP['U_ProductStatus']);
		$arrCol['Brand'] = conutf8($RST_SAP['Brand']);
		$arrCol['Model'] = conutf8($RST_SAP['Model']);
		$SQL_HEADER = "SELECT * FROM skubook_header WHERE ItemCode = '$ItemCode'";
		$RST_HEADER = MySQLSelect($SQL_HEADER);
		$arrCol['ID_SKU']      = $ItemCode;
		$arrCol['DATE_SKU']    = date("d/m/Y",strtotime($RST_HEADER['CreateDate']));
		$arrCol['ItemColor']   = $RST_HEADER['ItemColor'];
		$arrCol['BoxColor']    = $RST_HEADER['BoxColor'];
		$arrCol['MadeOf']      = $RST_HEADER['MadeOf'];
		$arrCol['ProCountry']  = $RST_HEADER['ProCountry'];
		$arrCol['TeamCode']  = $RST_HEADER['TeamCode'];

	// 1. คุณสมบัติ
		$Chk_DETAIL_Type1 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '1'";
		$DataType1 = ""; $ChkDataType1 = 'Y'; $tmpID_Type1 = "";
		if(CHKRowDB($Chk_DETAIL_Type1) == 0) {
			$ChkDataType1 = 'N';
			$DataType1 .= "
			<tr> 
				<td colspan='4' class='text-center'>ยังไม่มีข้อมูล</td>
			</tr>";
		}else{
			$SQL_DETAIL_Type1 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '1'";
			$QRY_DETAIL_Type1 = MySQLSelectX($SQL_DETAIL_Type1);
			$tmpRow = 0; $i = 0;
			while ($RST_DETAIL_Type1 = mysqli_fetch_array($QRY_DETAIL_Type1)) {
				$tmpRow++; 
				if($tmpRow == 1) { 
					$DataType1 .= "
					<tr>
						<th width='15%' class='ps-4'><input type='text' class='form-control-custom ps-0 DETAIL_Type1 fw-bolder' name='Header_".$RST_DETAIL_Type1['ID']."' id='Header_".$RST_DETAIL_Type1['ID']."' value='".$RST_DETAIL_Type1['Header']."' disabled></th>
						<td width='35%'><input type='text' class='form-control-custom ps-0 DETAIL_Type1' name='Detail_".$RST_DETAIL_Type1['ID']."' id='Detail_".$RST_DETAIL_Type1['ID']."' value='".$RST_DETAIL_Type1['Detail']."' disabled></td>"; 
				}else{
					$DataType1 .= "
						<th width='15%'><input type='text' class='form-control-custom ps-0 DETAIL_Type1 fw-bolder' name='Header_".$RST_DETAIL_Type1['ID']."' id='Header_".$RST_DETAIL_Type1['ID']."' value='".$RST_DETAIL_Type1['Header']."' disabled></th>
						<td width='35%'><input type='text' class='form-control-custom ps-0 DETAIL_Type1' name='Detail_".$RST_DETAIL_Type1['ID']."' id='Detail_".$RST_DETAIL_Type1['ID']."' value='".$RST_DETAIL_Type1['Detail']."' disabled></td>
					</tr>";
					$tmpRow = 0;
				}
				
				$i++;
				$tmpID_Type1 .= $RST_DETAIL_Type1['ID'];
				if($i < CHKRowDB($SQL_DETAIL_Type1)) {
					$tmpID_Type1 .= ",";
				}
			}
			if($tmpRow != 0) {
				$DataType1 .= "
					<th width='15%'></th>
					<td width='35%'></td>
				</tr>";
			}
		}
		$arrCol['DataType1'] = $DataType1;
		$arrCol['ChkDataType1'] = $ChkDataType1;
		$arrCol['tmpID_Type1'] = $tmpID_Type1;

	// 2. รายละเอียดบรรจุภัณฑ์
		$Chk_DETAIL_Type2 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '2'";
		$DataType2 = ""; $tmpID_Type2 = "";
		if(CHKRowDB($Chk_DETAIL_Type2) == 0) {
			$Header_Type2 = 
				[ 	
					'ขนาดสินค้า (ซม.)', 'น้ำหนักสินค้า (กก.)', 
					'ขนาดกล่อง 1 (ซม.)', 'ขนาดบรรจุ (กล่อง)', 
					'ขนาดกล่อง 2 (ซม.)',
					'น้ำหนักรวมสินค้า (กก.)', 'บาร์โค้ดกล่อง', 
					'ขนาดลัง (ซม.)', 'ขนาดบรรจุ (ลัง)',
					'น้ำหนักลังรวมสินค้า (กก.)', 'บาร์โค้ดลัง'
				];
			for($i = 0; $i < count($Header_Type2); $i++) {
				$INSERT_DETAIL_Type2 = "
					INSERT INTO skubook_detail 
					SET ItemCode = '$ItemCode', 
						Type = '2', 
						Header = '".$Header_Type2[$i]."', 
						Detail = NULL,";
						if($Header_Type2[$i] == "ขนาดสินค้า (ซม.)" || $Header_Type2[$i] == "น้ำหนักสินค้า (กก.)") {
							$INSERT_DETAIL_Type2 .= "Remark = 'ไม่รวมบรรจุภัณฑ์', ";
						}
						$INSERT_DETAIL_Type2 .= "
						CreateUkey = '$Ukey', 
						CreateDate = NOW()";
				MySQLInsert($INSERT_DETAIL_Type2);
			}
		}
		$SQL_DETAIL_Type2 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '2'";
		$QRY_DETAIL_Type2 = MySQLSelectX($SQL_DETAIL_Type2);
		$tmpRow = 0; $i = 0; $Sum = 0; $tmpBar1 = ""; $tmpBar2 = ""; $tmpBox1 = ""; $DataSizeBox2 = "";
		while ($RST_DETAIL_Type2 = mysqli_fetch_array($QRY_DETAIL_Type2)) {
			$DJ = "";
			switch($RST_DETAIL_Type2['Header']){
				case 'ขนาดสินค้า (ซม.)':
				case 'ขนาดกล่อง 1 (ซม.)':
				case 'น้ำหนักสินค้า (กก.)':
				case 'ขนาดบรรจุ (กล่อง)':
					$DJ = "<span style='color: #9A1118;'>*</span>";
				break;
				case 'น้ำหนักรวมสินค้า (กก.)':
					$DJ = "<span style='color: #9A1118;'>*</span>";
				break;
			}
			$tmpRow++; 
			if($tmpRow == 1) { 
				if($RST_DETAIL_Type2['Header'] == 'ขนาดกล่อง 2 (ซม.)') {
					$Detail_Type2x = explode("x",$RST_DETAIL_Type2['Detail']);
					$DataSizeBox2 .= "
					<tr>
						<th width='15%' class='ps-4'>".$RST_DETAIL_Type2['Header']." $DJ</th>
						<td width='35%'>
							<div class='d-flex align-items-center'>";
								if(isset($Detail_Type2x[0])) { $Detail_Type2x_0 = $Detail_Type2x[0]; }else{ $Detail_Type2x_0 = ""; }
								$DataSizeBox2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 ' style='width: 60px !important;' placeholder='กว้าง' name='Detail_".$RST_DETAIL_Type2['ID']."_A2x' id='Detail_".$RST_DETAIL_Type2['ID']."_A2x' value='".$Detail_Type2x_0."' disabled>
								&nbsp;x&nbsp;";
								if(isset($Detail_Type2x[1])) { $Detail_Type2x_1 = $Detail_Type2x[1]; }else{ $Detail_Type2x_1 = ""; }
								$DataSizeBox2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 text-center' style='width: 60px !important;' placeholder='ยาว' name='Detail_".$RST_DETAIL_Type2['ID']."_B2x' id='Detail_".$RST_DETAIL_Type2['ID']."_B2x' value='".$Detail_Type2x_1."' disabled>
								&nbsp;x&nbsp;";
								if(isset($Detail_Type2x[2])) { $Detail_Type2x_2 = $Detail_Type2x[2]; }else{ $Detail_Type2x_2 = ""; }
								$DataSizeBox2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 text-right' style='width: 60px !important;' placeholder='สูง' name='Detail_".$RST_DETAIL_Type2['ID']."_C2x' id='Detail_".$RST_DETAIL_Type2['ID']."_C2x' value='".$Detail_Type2x_2."' disabled>
							</div>
						</td>
					</tr>";
					$tmpRow = 0;
				}else{
					$DataType2 .= "
					<tr>
						<th width='15%' class='ps-4'>".$RST_DETAIL_Type2['Header']." $DJ</th>
						<td width='35%'>"; 
							if($RST_DETAIL_Type2['Header'] == 'ขนาดสินค้า (ซม.)') {
								$Detail_Type2 = explode("x",$RST_DETAIL_Type2['Detail']);
								$DataType2 .= "
								<div class='d-flex align-items-center'>";
									if(isset($Detail_Type2[0])) { $Detail_Type2_0 = $Detail_Type2[0]; }else{ $Detail_Type2_0 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2' style='width: 60px !important;' placeholder='กว้าง' name='Detail_".$RST_DETAIL_Type2['ID']."_A1' id='Detail_".$RST_DETAIL_Type2['ID']."_A1' value='".$Detail_Type2_0."' disabled>
									&nbsp;x&nbsp;";
									if(isset($Detail_Type2[1])) { $Detail_Type2_1 = $Detail_Type2[1]; }else{ $Detail_Type2_1 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 text-center' style='width: 60px !important;' placeholder='ยาว' name='Detail_".$RST_DETAIL_Type2['ID']."_B1' id='Detail_".$RST_DETAIL_Type2['ID']."_B1' value='".$Detail_Type2_1."' disabled>
									&nbsp;x&nbsp;";
									if(isset($Detail_Type2[2])) { $Detail_Type2_2 = $Detail_Type2[2]; }else{ $Detail_Type2_2 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 text-right' style='width: 60px !important;' placeholder='สูง' name='Detail_".$RST_DETAIL_Type2['ID']."_C1' id='Detail_".$RST_DETAIL_Type2['ID']."_C1' value='".$Detail_Type2_2."' disabled>
									<small> (".$RST_DETAIL_Type2['Remark'].")</small>
								</div>";
							}elseif($RST_DETAIL_Type2['Header'] == 'ขนาดกล่อง 1 (ซม.)') {
								$Detail_Type2 = explode("x",$RST_DETAIL_Type2['Detail']);
								$DataType2 .= "
								<div class='d-flex align-items-center'>";
									if(isset($Detail_Type2[0])) { $Detail_Type2_0 = $Detail_Type2[0]; }else{ $Detail_Type2_0 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 ' style='width: 60px !important;' placeholder='กว้าง' name='Detail_".$RST_DETAIL_Type2['ID']."_A2' id='Detail_".$RST_DETAIL_Type2['ID']."_A2' value='".$Detail_Type2_0."' disabled>
									&nbsp;x&nbsp;";
									if(isset($Detail_Type2[1])) { $Detail_Type2_1 = $Detail_Type2[1]; }else{ $Detail_Type2_1 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 text-center' style='width: 60px !important;' placeholder='ยาว' name='Detail_".$RST_DETAIL_Type2['ID']."_B2' id='Detail_".$RST_DETAIL_Type2['ID']."_B2' value='".$Detail_Type2_1."' disabled>
									&nbsp;x&nbsp;";
									if(isset($Detail_Type2[2])) { $Detail_Type2_2 = $Detail_Type2[2]; }else{ $Detail_Type2_2 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 text-right' style='width: 60px !important;' placeholder='สูง' name='Detail_".$RST_DETAIL_Type2['ID']."_C2' id='Detail_".$RST_DETAIL_Type2['ID']."_C2' value='".$Detail_Type2_2."' disabled>
								</div>";
							}elseif($RST_DETAIL_Type2['Header'] == 'ขนาดลัง (ซม.)') {
								$Detail_Type2 = explode("x",$RST_DETAIL_Type2['Detail']);
								$DataType2 .= "
								<div class='d-flex align-items-center'>";
									if(isset($Detail_Type2[0])) { $Detail_Type2_0 = $Detail_Type2[0]; }else{ $Detail_Type2_0 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 ' style='width: 60px !important;' placeholder='กว้าง' name='Detail_".$RST_DETAIL_Type2['ID']."_A3' id='Detail_".$RST_DETAIL_Type2['ID']."_A2' value='".$Detail_Type2_0."' disabled>
									&nbsp;x&nbsp;";
									if(isset($Detail_Type2[1])) { $Detail_Type2_1 = $Detail_Type2[1]; }else{ $Detail_Type2_1 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 text-center' style='width: 60px !important;' placeholder='ยาว' name='Detail_".$RST_DETAIL_Type2['ID']."_B3' id='Detail_".$RST_DETAIL_Type2['ID']."_B2' value='".$Detail_Type2_1."' disabled>
									&nbsp;x&nbsp;";
									if(isset($Detail_Type2[2])) { $Detail_Type2_2 = $Detail_Type2[2]; }else{ $Detail_Type2_2 = ""; }
									$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2 text-right' style='width: 60px !important;' placeholder='สูง' name='Detail_".$RST_DETAIL_Type2['ID']."_C3' id='Detail_".$RST_DETAIL_Type2['ID']."_C2' value='".$Detail_Type2_2."' disabled>
								</div>";
							}elseif($RST_DETAIL_Type2['Header'] == 'น้ำหนักลังรวมสินค้า (กก.)') {
								$DataType2 .= "<input type='number' class='form-control-custom ps-0' value='".$Sum."' disabled>";
							}else{
								if($RST_DETAIL_Type2['Header'] == 'น้ำหนักรวมสินค้า (กก.)') { $Sum = $RST_DETAIL_Type2['Detail']; }
								$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2' name='Detail_".$RST_DETAIL_Type2['ID']."' id='Detail_".$RST_DETAIL_Type2['ID']."' value='".$RST_DETAIL_Type2['Detail']."' disabled>";
							}
						$DataType2 .= "</td>"; 
				}
			}else{
					$DataType2 .= "
					<th width='15%'>".$RST_DETAIL_Type2['Header']." $DJ</th>
					<td width='35%'>";
						if($RST_DETAIL_Type2['Header'] == 'ขนาดบรรจุ (กล่อง)') {
							$tmpBox1 = $RST_DETAIL_Type2['ID'];
							$DataType2 .= "<input type='number' onfocusout=\"CallBar('".$ItemCode."','Box','".$RST_DETAIL_Type2['ID']."')\" class='form-control-custom ps-0 DETAIL_Type2' style='width: 50px !important;' placeholder='ระบุ' name='Detail_".$RST_DETAIL_Type2['ID']."' id='Detail_".$RST_DETAIL_Type2['ID']."' value='".$RST_DETAIL_Type2['Detail']."' disabled>";
							$DataType2 .= (conutf8($RST_SAP['NameType1']) == 'ตะปูยิง') ? "นัด" : conutf8($RST_SAP['SalUnitMsr']);
						}elseif($RST_DETAIL_Type2['Header'] == 'ขนาดบรรจุ (ลัง)') {
							if($RST_DETAIL_Type2['Detail'] != "" && $RST_DETAIL_Type2['Detail'] != 0) {
								$Sum = $Sum*$RST_DETAIL_Type2['Detail'];
							}else{
								$Sum = "";
							}
							$DataType2 .= "<input type='number' onfocusout=\"CallBar('".$ItemCode."','CTN','".$RST_DETAIL_Type2['ID']."')\" class='form-control-custom ps-0 DETAIL_Type2' style='width: 50px !important;' placeholder='ระบุ' name='Detail_".$RST_DETAIL_Type2['ID']."' id='Detail_".$RST_DETAIL_Type2['ID']."' value='".$RST_DETAIL_Type2['Detail']."' disabled>กล่อง";
						}elseif($RST_DETAIL_Type2['Header'] == 'น้ำหนักสินค้า (กก.)') {
							$DataType2 .= "
							<input type='number' class='form-control-custom ps-0 DETAIL_Type2' style='width: 150px !important;' name='Detail_".$RST_DETAIL_Type2['ID']."' id='Detail_".$RST_DETAIL_Type2['ID']."' value='".$RST_DETAIL_Type2['Detail']."' disabled>
							<small> (".$RST_DETAIL_Type2['Remark'].")</small>";
						}else{
							switch ($RST_DETAIL_Type2['Header']) {
								case 'บาร์โค้ดกล่อง': $tmpBar1 = $RST_DETAIL_Type2['ID']; break;
								case 'บาร์โค้ดลัง': $tmpBar2 = $RST_DETAIL_Type2['ID']; break;
							}
							$DataType2 .= "<input type='number' class='form-control-custom ps-0 DETAIL_Type2' name='Detail_".$RST_DETAIL_Type2['ID']."' id='Detail_".$RST_DETAIL_Type2['ID']."' value='".$RST_DETAIL_Type2['Detail']."' disabled>";
						}
					$DataType2 .= "</td>
				</tr>";
				if($RST_DETAIL_Type2['Header'] == 'ขนาดบรรจุ (กล่อง)') {
					$DataType2 .= "SizeBox2"; 
				}
				$tmpRow = 0;
			}

			$i++;
			$tmpID_Type2 .= $RST_DETAIL_Type2['ID'];
			if($i < CHKRowDB($SQL_DETAIL_Type2)) {
				$tmpID_Type2 .= ",";
			}
		}

		$DataType2 = str_replace("SizeBox2",$DataSizeBox2,$DataType2);

		$arrCol['DataType2'] = $DataType2;
		$arrCol['tmpID_Type2'] = $tmpID_Type2;
		$arrCol['tmpBar1'] = $tmpBar1;
		$arrCol['tmpBar2'] = $tmpBar2;
		$arrCol['tmpBox1'] = $tmpBox1;
	
	// 3. อุปกรณ์ภายในกล่อง
		$Chk_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '3'";
		$DataType3 = ""; $ChkDataType3 = 'Y'; $tmpID_Type3 = "";
		if(CHKRowDB($Chk_DETAIL_Type3) == 0) {
			$ChkDataType3 = 'N';
			$DataType3 .= "
			<tr> 
				<td class='text-center'>ยังไม่มีข้อมูล</td>
			</tr>";
		}else{
			$SQL_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '3'";
			$QRY_DETAIL_Type3 = MySQLSelectX($SQL_DETAIL_Type3);
			$i = 0;
			while ($RST_DETAIL_Type3 = mysqli_fetch_array($QRY_DETAIL_Type3)) {
				// <input type='text' class='form-control-custom ps-0 DETAIL_Type3 w-100 ' name='Detail_".$RST_DETAIL_Type3['ID']."' id='Detail_".$RST_DETAIL_Type3['ID']."' value='".$RST_DETAIL_Type3['Detail']."' disabled>
				$DataType3 .= "
				<tr> 
					<td class='ps-4'>
						<textarea class='form-control-custom ps-0 DETAIL_Type3 w-100 textarea resize-ta' name='Detail_".$RST_DETAIL_Type3['ID']."' id='Detail_".$RST_DETAIL_Type3['ID']."' onkeyup='calcHeight(".$RST_DETAIL_Type3['ID'].")' disabled>".$RST_DETAIL_Type3['Detail']."</textarea>
					</td>
				</tr>";

				$i++;
				$tmpID_Type3 .= $RST_DETAIL_Type3['ID'];
				if($i < CHKRowDB($SQL_DETAIL_Type3)) {
					$tmpID_Type3 .= ",";
				}
			}
		}
		$arrCol['DataType3'] = $DataType3;
		$arrCol['ChkDataType3'] = $ChkDataType3;
		$arrCol['tmpID_Type3'] = $tmpID_Type3;
	// 4. วิธีการใช้งาน
		$SQL_DETAIL_Type4 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '4'";
		$DataType4 = ""; $ChkDataType4 = 'Y'; $tmpID_Type4 = "";
		if(CHKRowDB($SQL_DETAIL_Type4) == 0) {
			$ChkDataType4 = 'N';
			$DataType4 .= "
			<tr> 
				<td class='text-center'>ยังไม่มีข้อมูล</td>
			</tr>";
		}else{
			$QRY_DETAIL_Type4 = MySQLSelectX($SQL_DETAIL_Type4);
			$i = 0;
			while ($RST_DETAIL_Type4 = mysqli_fetch_array($QRY_DETAIL_Type4)) {
				// <td class='ps-4'><input type='text' class='form-control-custom ps-0 DETAIL_Type4 w-100 ' name='Detail_".$RST_DETAIL_Type4['ID']."' id='Detail_".$RST_DETAIL_Type4['ID']."' value='".$RST_DETAIL_Type4['Detail']."' disabled></td>
				$DataType4 .= "
				<tr> 
					<td class='ps-4'>
						<textarea class='form-control-custom ps-0 DETAIL_Type4 w-100' rows='10' name='Detail_".$RST_DETAIL_Type4['ID']."' id='Detail_".$RST_DETAIL_Type4['ID']."' disabled>".$RST_DETAIL_Type4['Detail']."</textarea>
					</td>
				</tr>";

				$i++;
				$tmpID_Type4 .= $RST_DETAIL_Type4['ID'];
				if($i < CHKRowDB($SQL_DETAIL_Type4)) {
					$tmpID_Type4 .= ",";
				}
			}
		}
		$arrCol['DataType4'] = $DataType4;
		$arrCol['ChkDataType4'] = $ChkDataType4;
		$arrCol['tmpID_Type4'] = $tmpID_Type4;

	// 5. จุดเด่น จุดขาย ของสินค้า
		$SQL_DETAIL_Type5 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '5'";
		$DataType5 = ""; $ChkDataType5 = 'Y'; $tmpID_Type5 = "";
		if(CHKRowDB($SQL_DETAIL_Type5) == 0) {
			$ChkDataType5 = 'N';
			$DataType5 .= "
			<tr> 
				<td class='text-center'>ยังไม่มีข้อมูล</td>
			</tr>";
		}else{
			$QRY_DETAIL_Type5 = MySQLSelectX($SQL_DETAIL_Type5);
			$i = 0;
			while ($RST_DETAIL_Type5 = mysqli_fetch_array($QRY_DETAIL_Type5)) {
				// <input type='text' class='form-control-custom ps-0 DETAIL_Type5 w-100 ' name='Detail_".$RST_DETAIL_Type5['ID']."' id='Detail_".$RST_DETAIL_Type5['ID']."' value='".$RST_DETAIL_Type5['Detail']."' disabled>
				$DataType5 .= "
				<tr> 
					<td class='ps-4'>
						<textarea class='form-control-custom ps-0 DETAIL_Type5 w-100' rows='10' name='Detail_".$RST_DETAIL_Type5['ID']."' id='Detail_".$RST_DETAIL_Type5['ID']."' disabled>".$RST_DETAIL_Type5['Detail']."</textarea>
					</td>
				</tr>";

				$i++;
				$tmpID_Type5 .= $RST_DETAIL_Type5['ID'];
				if($i < CHKRowDB($SQL_DETAIL_Type5)) {
					$tmpID_Type5 .= ",";
				}
			}
		}
		$arrCol['DataType5'] = $DataType5;
		$arrCol['ChkDataType5'] = $ChkDataType5;
		$arrCol['tmpID_Type5'] = $tmpID_Type5;

	// 6. การรับประกัน
		$SQL_DETAIL_Type6 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '6'";
		$DataType6 = ""; $tmpID_Type6 = "";
		if(CHKRowDB($SQL_DETAIL_Type6) == 0) {
			$Header_Type6 = [ 'ระยะเวลารับประกัน (เดือน)', 'ประเภทการรับประกัน', 'เงื่อนไขการรับประกัน' ];
			for($i = 0; $i < count($Header_Type6); $i++) {
				$INSERT_DETAIL_Type6 = "
					INSERT INTO skubook_detail 
					SET ItemCode = '$ItemCode', 
						Type = '6', 
						Header = '".$Header_Type6[$i]."',
						Detail = NULL,
						CreateUkey = '$Ukey', 
						CreateDate = NOW()";
				MySQLInsert($INSERT_DETAIL_Type6);
			}
		}
		$QRY_DETAIL_Type6 = MySQLSelectX($SQL_DETAIL_Type6);
		$i = 0;
		while ($RST_DETAIL_Type6 = mysqli_fetch_array($QRY_DETAIL_Type6)) {
			if($RST_DETAIL_Type6['Header'] == 'ระยะเวลารับประกัน (เดือน)') {
				$DataType6 .= "
				<tr>
					<th width='20%' class='ps-4'>".$RST_DETAIL_Type6['Header']." <span style='color: #9A1118;'>*</span></th>
					<td width='80%'>
						<div class='d-flex'>
							<div width='30%'>
								<input type='number' class='form-control-custom ps-0 DETAIL_Type6' name='Detail_".$RST_DETAIL_Type6['ID']."' id='Detail_".$RST_DETAIL_Type6['ID']."' value='".$RST_DETAIL_Type6['Detail']."' disabled>
							</div>
							&nbsp;
							ระบุ&nbsp;
							<div style='width: 71% !important'>
								<input type='text' class='form-control-custom ps-0 DETAIL_Type6' name='Remark_".$RST_DETAIL_Type6['ID']."' id='Remark_".$RST_DETAIL_Type6['ID']."' value='".$RST_DETAIL_Type6['Remark']."' disabled>
							</div>
						</div>
					</td>
				</tr>"; 
			}else{
				$DataType6 .= "
					<tr>
						<th width='20%' class='ps-4'>".$RST_DETAIL_Type6['Header']." <span style='color: #9A1118;'>*</span></th>
						<td width='80%'><input type='text' class='form-control-custom ps-0 DETAIL_Type6 w-100' name='Detail_".$RST_DETAIL_Type6['ID']."' id='Detail_".$RST_DETAIL_Type6['ID']."' value='".$RST_DETAIL_Type6['Detail']."' disabled></td>
					</tr>"; 
			}
			$i++;
			$tmpID_Type6 .= $RST_DETAIL_Type6['ID'];
			if($i < CHKRowDB($SQL_DETAIL_Type6)) {
				$tmpID_Type6 .= ",";
			}
		}
		$arrCol['DataType6'] = $DataType6;
		$arrCol['tmpID_Type6'] = $tmpID_Type6;

	// 7. ข้อมูล สคบ
		$Chk_DETAIL_Type7 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '7'";
		$DataType7 = ""; $tmpID_Type7 = "";
		if(CHKRowDB($Chk_DETAIL_Type7) == 0) {
			$Header_Type7 = [ 'ชื่อสินค้า', 'ผลิตจากประเทศ', 'จัดจำหน่ายโดย', 'จัดจำหน่ายโดย_2', 'จัดจำหน่ายโดย_3', 'บรรจุ', 'วิธีการใช้', 'คำเตือน', 'ราคา', 'วันที่ผลิต' ];
			for($i = 0; $i < count($Header_Type7); $i++) {
				$INSERT_DETAIL_Type7 = "
					INSERT INTO skubook_detail 
					SET ItemCode = '$ItemCode', 
						Type = '7', 
						Header = '".$Header_Type7[$i]."',";
						if($Header_Type7[$i] == 'จัดจำหน่ายโดย') {
							$INSERT_DETAIL_Type7 .= "
							Detail = 'บริษัท คิงบางกอก อินเตอร์เทรด จำกัด',";
						}elseif($Header_Type7[$i] == 'จัดจำหน่ายโดย_2') {
							$INSERT_DETAIL_Type7 .= "
							Detail = '541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220',";
						}elseif($Header_Type7[$i] == 'จัดจำหน่ายโดย_3') {
							$INSERT_DETAIL_Type7 .= "
							Detail = 'เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856',";
						}elseif($Header_Type7[$i] == 'ราคา') {
							$INSERT_DETAIL_Type7 .= "
							Detail = 'ระบุ ณ จุดขาย',";
						}else{
							$INSERT_DETAIL_Type7 .= "
							Detail = NULL,";
						}
						$INSERT_DETAIL_Type7 .= "
						CreateUkey = '$Ukey', 
						CreateDate = NOW()";
				MySQLInsert($INSERT_DETAIL_Type7);
			}
		}
		$SQL_DETAIL_Type7 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '7'";
		$QRY_DETAIL_Type7 = MySQLSelectX($SQL_DETAIL_Type7);
		$i = 0;
		while ($RST_DETAIL_Type7 = mysqli_fetch_array($QRY_DETAIL_Type7)) {
			switch ($RST_DETAIL_Type7['Header']) {
				case 'ชื่อสินค้า':
					$DataType7 .= "
					<tr>
						<th width='20%' class='ps-4 align-top'>".$RST_DETAIL_Type7['Header']."</th>
						<td width='80%'>".conutf8($RST_SAP['ItemName'])."</td>
					</tr>"; 
				break;
				case 'ผลิตจากประเทศ':
					$DataType7 .= "
					<tr>
						<th width='20%' class='ps-4 align-top'>".$RST_DETAIL_Type7['Header']."</th>
						<td width='80%'>
							<select class='form-control-custom ps-0 DETAIL_Type7 w-100 Counttry_Type7' name='Detail_".$RST_DETAIL_Type7['ID']."' id='Detail_".$RST_DETAIL_Type7['ID']."' disabled>
								".Counttry()."
							</select>
							<input type='hidden' id='Country_ID' value='".$RST_DETAIL_Type7['ID']."'>
						</td>
					</tr>"; 
					$arrCol['Counttry_Type7'] = $RST_DETAIL_Type7['Detail'];
				break;
				case 'จัดจำหน่ายโดย':
					$DataType7 .= "
					<tr>
						<th width='20%' class='ps-4 align-top'>".$RST_DETAIL_Type7['Header']."</th>
						<td width='80%'>
							<input type='text' class='form-control-custom ps-0 DETAIL_Type7 w-100 ' placeholder='ชื่อบริษัท' name='Detail_".$RST_DETAIL_Type7['ID']."' id='Detail_".$RST_DETAIL_Type7['ID']."' value='".$RST_DETAIL_Type7['Detail']."' disabled><br>"; 
				break;
				case 'จัดจำหน่ายโดย_2':
					$DataType7 .= "<input type='text' class='form-control-custom ps-0 DETAIL_Type7 w-100 ' placeholder='ที่อยู่บริษัท' name='Detail_".$RST_DETAIL_Type7['ID']."' id='Detail_".$RST_DETAIL_Type7['ID']."' value='".$RST_DETAIL_Type7['Detail']."' disabled><br>"; 
				break;
				case 'จัดจำหน่ายโดย_3':
					$DataType7 .= "
							<input type='text' class='form-control-custom ps-0 DETAIL_Type7 w-100 ' placeholder='ที่อยู่บริษัท' name='Detail_".$RST_DETAIL_Type7['ID']."' id='Detail_".$RST_DETAIL_Type7['ID']."' value='".$RST_DETAIL_Type7['Detail']."' disabled>
						</td>
					</tr>";
				break;
				default:
					$DataType7 .= "
					<tr>
						<th width='20%' class='ps-4 align-top'>".$RST_DETAIL_Type7['Header']."</th>
						<td width='80%'><input type='text' class='form-control-custom ps-0 DETAIL_Type7 w-100' name='Detail_".$RST_DETAIL_Type7['ID']."' id='Detail_".$RST_DETAIL_Type7['ID']."' value='".$RST_DETAIL_Type7['Detail']."' disabled></td>
					</tr>"; 
				break;
			}

			$i++;
			$tmpID_Type7 .= $RST_DETAIL_Type7['ID'];
			if($i < CHKRowDB($SQL_DETAIL_Type7)) {
				$tmpID_Type7 .= ",";
			}
		}
		$arrCol['DataType7'] = $DataType7;
		$arrCol['tmpID_Type7'] = $tmpID_Type7;

	// 8. ข้อควรระวัง
		$SQL_DETAIL_Type8 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '8'";
		$DataType8 = ""; $tmpID_Type8 = "";
		if(CHKRowDB($SQL_DETAIL_Type8) == 0) {
			$Header_Type8 = [ 'คำเตือน', 'ข้อแนะนำในการใช้งาน' ];
			for($i = 0; $i < count($Header_Type8); $i++) {
				$INSERT_DETAIL_Type8 = "
					INSERT INTO skubook_detail 
					SET ItemCode = '$ItemCode', 
						Type = '8', 
						Header = '".$Header_Type8[$i]."',
						Detail = NULL,
						CreateUkey = '$Ukey', 
						CreateDate = NOW()";
				MySQLInsert($INSERT_DETAIL_Type8);
			}
		}
		$QRY_DETAIL_Type8 = MySQLSelectX($SQL_DETAIL_Type8);
		$i = 0;
		while ($RST_DETAIL_Type8 = mysqli_fetch_array($QRY_DETAIL_Type8)) {
			$DataType8 .= "
				<tr>
					<th width='20%' class='ps-4'>".$RST_DETAIL_Type8['Header']."</th>
					<td width='80%'><input type='text' class='form-control-custom ps-0 DETAIL_Type8 w-100' name='Detail_".$RST_DETAIL_Type8['ID']."' id='Detail_".$RST_DETAIL_Type8['ID']."' value='".$RST_DETAIL_Type8['Detail']."' disabled></td>
				</tr>"; 
			$i++;
			$tmpID_Type8 .= $RST_DETAIL_Type8['ID'];
			if($i < CHKRowDB($SQL_DETAIL_Type8)) {
				$tmpID_Type8 .= ",";
			}
		}
		$arrCol['DataType8'] = $DataType8;
		$arrCol['tmpID_Type8'] = $tmpID_Type8;

	

	// หมายเหตุ
		$Remark = "
		<tr>
			<td class='ps-4'><input type='text' class='form-control-custom ps-0 w-100' name='Remark' id='Remark' value='".$RST_HEADER['Remark']."' disabled></td>
		</tr>"; 
		$arrCol['Remark'] = $Remark;
}

if($_GET['a'] == 'EditSKU') {
	$Tab      = $_POST['Tab'];
	$ItemCode = $_POST['ItemCode'];
	$Ukey     = $_SESSION['ukey'];
	$Error = 0;
	switch ($Tab) {
		case 0: // ข้อมูลสินค้า
			$ItemColor  = $_POST['ItemColor'];
			$BoxColor   = $_POST['BoxColor'];
			$MadeOf     = $_POST['MadeOf'];
			$ProCountry = $_POST['ProCountry'];
			$TeamCode   = $_POST['TeamCode'];

			$UPDATE_HEADER = "
				UPDATE skubook_header 
				SET ItemColor = '$ItemColor', 
					BoxColor = '$BoxColor', 
					MadeOf = '$MadeOf', 
					ProCountry = '$ProCountry',
					TeamCode = '$TeamCode',
					UpdateUkey = '$Ukey',
					UpdateDate = NOW()
				WHERE ItemCode = '$ItemCode'";
			MySQLUpdate($UPDATE_HEADER);
		break;
		case 99: // หมายเหตุ
			$Remark = $_POST['Remark'];
			$UPDATE_HEADER = "UPDATE skubook_header SET Remark = '$Remark', UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ItemCode = '$ItemCode'";
			MySQLUpdate($UPDATE_HEADER);
		break;
		case 2: // รายละเอียดบรรจุภัณฑ์
			$tmpID = explode(",",$_POST['tmpID']);
			for($i = 0; $i < count($tmpID); $i++) {
				$Detail = "";
				if(isset($_POST['Detail_'.$tmpID[$i].'_A1'])) {
					$Detail = $_POST['Detail_'.$tmpID[$i].'_A1']."x".$_POST['Detail_'.$tmpID[$i].'_B1']."x".$_POST['Detail_'.$tmpID[$i].'_C1'];
				}elseif(isset($_POST['Detail_'.$tmpID[$i].'_A2'])) {
					$Detail = $_POST['Detail_'.$tmpID[$i].'_A2']."x".$_POST['Detail_'.$tmpID[$i].'_B2']."x".$_POST['Detail_'.$tmpID[$i].'_C2'];
				}elseif(isset($_POST['Detail_'.$tmpID[$i].'_A3'])) {
					$Detail = $_POST['Detail_'.$tmpID[$i].'_A3']."x".$_POST['Detail_'.$tmpID[$i].'_B3']."x".$_POST['Detail_'.$tmpID[$i].'_C3'];
				}elseif(isset($_POST['Detail_'.$tmpID[$i].'_A2x'])){
					$Detail = $_POST['Detail_'.$tmpID[$i].'_A2x']."x".$_POST['Detail_'.$tmpID[$i].'_B2x']."x".$_POST['Detail_'.$tmpID[$i].'_C2x'];
				}else{
					if($_POST['Detail_'.$tmpID[$i]] != "" && $_POST['Detail_'.$tmpID[$i]] != null && $_POST['Detail_'.$tmpID[$i]] != 'undefined' && $_POST['Detail_'.$tmpID[$i]] != 'null') {
						$Detail = $_POST['Detail_'.$tmpID[$i]];
					}
				}
				$Chk = MySQLSelect("SELECT Header FROM skubook_detail WHERE ID = ".$tmpID[$i]."");
				switch($Chk['Header']) {
					case 'ขนาดสินค้า (ซม.)': 
					case 'ขนาดกล่อง 1 (ซม.)': 
						if($Detail == "" || $Detail == "xx" || $Detail == " x x ") {
							$Error++;
						}
					break;
					case 'น้ำหนักรวมสินค้า (กก.)': 
					case 'น้ำหนักสินค้า (กก.)': 
					case 'ขนาดบรรจุ (กล่อง)': 
						if($Detail == "") {
							$Error++;
						}
					break;
				}

				if($Error == 0) {
					$UPDATE_DETAIL = "UPDATE skubook_detail SET Detail = '$Detail', UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ID = ".$tmpID[$i]."";
					MySQLUpdate($UPDATE_DETAIL);

					$UPDATE_HEADER = "UPDATE skubook_header SET UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ItemCode = '$ItemCode'";
					MySQLUpdate($UPDATE_HEADER);
				}
			}
		break;
		case 10: // ช่องทางการขายสินค้า
			$tmpID = explode(",",$_POST['tmpID']);
			for($i = 0; $i < count($tmpID); $i++) {
				$UPDATE_DETAIL = "UPDATE skubook_detail SET CheckBox = '".$_POST['CheckBox_'.$tmpID[$i]]."', UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ID = ".$tmpID[$i]."";
				MySQLUpdate($UPDATE_DETAIL);
			}
			$UPDATE_HEADER = "UPDATE skubook_header SET UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ItemCode = '$ItemCode'";
			MySQLUpdate($UPDATE_HEADER);
		break;
		case 11: // VDO Utility
			$tmpID = explode(",",$_POST['tmpID']);
			for($i = 0; $i < count($tmpID); $i++) {
				$UPDATE_DETAIL = "UPDATE skubook_detail SET Header = '".$_POST['Header_'.$tmpID[$i]]."', Detail = '".$_POST['Detail_'.$tmpID[$i]]."', UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ID = ".$tmpID[$i]."";
				MySQLUpdate($UPDATE_DETAIL);
			}
			$UPDATE_HEADER = "UPDATE skubook_header SET UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ItemCode = '$ItemCode'";
			MySQLUpdate($UPDATE_HEADER);
		break;
		default:
			$tmpID = explode(",",$_POST['tmpID']);
			for($i = 0; $i < count($tmpID); $i++) {
				$Header = "";
				$Detail = "";
				if(isset($_POST['Remark_'.$tmpID[$i]])) {
					$Remark = "'".$_POST['Remark_'.$tmpID[$i]]."'";
				}else{
					$Remark = "NULL";
				}
				if(isset($_POST['Detail_'.$tmpID[$i]])) {
					$Detail = $_POST['Detail_'.$tmpID[$i]];
				}

				if(isset($_POST['Header_'.$tmpID[$i]])) {
					$Header = "Header = '".$_POST['Header_'.$tmpID[$i]]."',";
				}
				
				$Chk = MySQLSelect("SELECT Header FROM skubook_detail WHERE ID = ".$tmpID[$i]."");
				switch($Tab){
					case 1: 
						if($Detail == "" || $Detail == null) {
							$Error++;
						}
						if($Header == "" || $Header == null) {
							$Error++;
						}
					break;
					case 3: 
					case 4: 
					case 5:
					case 7:
					case 8:
						if($Detail == "" || $Detail == null) {
							$Error++;
						}
					break;
					case 6: 
						switch($Chk['Header']) {
							case 'ระยะเวลารับประกัน (เดือน)': 
							case 'ประเภทการรับประกัน': 
							case 'เงื่อนไขการรับประกัน': 
								if($Detail == "" || $Detail == null) {
									$Error++;
								}
							break;
						}
					break;
				}
				
				if($Error == 0) {
					$UPDATE_DETAIL = "UPDATE skubook_detail SET $Header Detail = '$Detail', Remark = $Remark, UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ID = ".$tmpID[$i]."";
					MySQLUpdate($UPDATE_DETAIL);

					$UPDATE_HEADER = "UPDATE skubook_header SET UpdateUkey = '$Ukey', UpdateDate = NOW() WHERE ItemCode = '$ItemCode'";
					MySQLUpdate($UPDATE_HEADER);
				}

			}
		break;
	}
	$arrCol['Error'] = $Error;
}

if($_GET['a'] == 'SaveAddSKU') {
	$Type     = $_POST['temTab'];
	$ItemCode = $_POST['ItemCode'];
	$Header   = $_POST['Header'];
	$Detail   = $_POST['Detail'];
	$Ukey     = $_SESSION['ukey'];
	$INSERT = "INSERT INTO skubook_detail SET ItemCode = '$ItemCode', Type = '$Type', Header = '$Header', Detail = '$Detail', CreateUkey = '$Ukey', CreateDate = NOW()";
	$GetID = MySQLInsert($INSERT);
	$SUCCESS = "";
	if(isset($GetID)) {
		$SUCCESS = "Y";
	}
	$arrCol['SUCCESS'] = $SUCCESS;
}

if($_GET['a'] == 'GetIMG') {
	$ItemCode = $_POST['ItemCode'];
	$SQL = "SELECT * FROM skubook_attach WHERE ItemCode = '$ItemCode' AND FileStatus = 'A' AND Type != '1'";
	$QRY = MySQLSelectX($SQL);
	$n = 0; $DataImg = ""; 
	while($RST = mysqli_fetch_array($QRY)) {
		$ts = date("Ymdhis");
		if($RST['Type'] == 6) {
			if($n == 0) {
				$DataImg .= "
				<div class='carousel-item active text-center p-2'>
					<img src='../../image/products/".$ItemCode."/".$RST['Type']."/".$RST['FileDirName'].'.'.$RST['FileExt']."?v=$ts' style='width: 100%;' />
				</div>";
			}else{
				$DataImg .= "
				<div class='carousel-item text-center p-2'>
					<img src='../../image/products/".$ItemCode."/".$RST['Type']."/".$RST['FileDirName'].'.'.$RST['FileExt']."?v=$ts' style='width: 100%;' />
				</div>";
			}
			$n++;
		}else{
			$arrCol['Img'.$RST['Type']] = "<img src='../../image/products/".$ItemCode."/".$RST['Type']."/".$RST['FileDirName'].'.'.$RST['FileExt']."?v=$ts' style='width: 80%'/>";
		}
	}

	$n1 = 0; $DataImg_1 = "";
	$filesIMG = glob("../../../../image/products/".$ItemCode."/1/*.{jpg,png}",GLOB_BRACE);
	if(isset($filesIMG[0])) {
		if(count($filesIMG) > 1) {
			for ($i = 0; $i < count($filesIMG); $i++){
				$ts = date("Ymdhis");
				if($i == 0) {
					$DataImg_1 .= "
					<div class='carousel-item active text-center p-2'>
						<img src='".$filesIMG[$i]."?v=$ts' style='width: 100%;' />
					</div>";
				}else{
					$DataImg_1 .= "
					<div class='carousel-item text-center p-2'>
						<img src='".$filesIMG[$i]."?v=$ts' style='width: 100%;' />
					</div>";
				}
				$n1++;
			}
			$filesIMG_1 = glob("../../../../image/products/".$ItemCode."/*.{jpg,png}",GLOB_BRACE);
			if(isset($filesIMG_1[0])) {
				for ($i = 0; $i < count($filesIMG_1); $i++){
					$ts = date("Ymdhis");
					$DataImg_1 .= "
					<div class='carousel-item text-center p-2'>
						<img src='".$filesIMG_1[$i]."?v=$ts' style='width: 100%;' />
					</div>";
					$n1++;
				}
			}
		}else{
			$filesIMG_1 = glob("../../../../image/products/".$ItemCode."/*.{jpg,png}",GLOB_BRACE);
			if(isset($filesIMG_1[0])) {
				$ts = date("Ymdhis");
				$DataImg_1 .= "
				<div class='carousel-item active text-center p-2'>
					<img src='".$filesIMG[0]."?v=$ts' style='width: 100%;' />
				</div>";
				for ($i = 0; $i < count($filesIMG_1); $i++){
					$ts = date("Ymdhis");
					$DataImg_1 .= "
					<div class='carousel-item text-center p-2'>
						<img src='".$filesIMG_1[$i]."?v=$ts' style='width: 100%;' />
					</div>";
				}
				$n1++;
			}else{
				$ts = date("Ymdhis");
				$arrCol['Img1'] = "<img src='".$filesIMG[0]."?v=$ts' style='width: 80%'/>";
			}
		}
	}else{
		$filesIMG_1 = glob("../../../../image/products/".$ItemCode."/*.{jpg,png}",GLOB_BRACE);
		if(isset($filesIMG_1[0])) {
			if(count($filesIMG_1) > 1) {
				for ($i = 0; $i < count($filesIMG_1); $i++){
					$ts = date("Ymdhis");
					if($i == 0) {
						$DataImg_1 .= "
						<div class='carousel-item active text-center p-2'>
							<img src='".$filesIMG_1[$i]."?v=$ts' style='width: 100%;' />
						</div>";
					}else{
						$DataImg_1 .= "
						<div class='carousel-item text-center p-2'>
							<img src='".$filesIMG_1[$i]."?v=$ts' style='width: 100%;' />
						</div>";
					}
					$n1++;
				}
			}else{
				$ts = date("Ymdhis");
				$arrCol['Img1'] = "<img src='".$filesIMG_1[0]."?v=$ts' style='width: 80%'/>";
			}
		}
	}

	if($n1 > 0) {
		$DataImg_1 = "
		<div id='viewDataImg_1' class='carousel slide' data-bs-touch='false' data-bs-interval='false'>
			<div class='carousel-inner'>".$DataImg_1."</div>
			<button class='carousel-control-prev' type='button' data-bs-target='#viewDataImg_1' data-bs-slide='prev'>
				<span class='carousel-control-prev-icon rounded bg-dark' aria-hidden='true'></span>
				<span class='visually-hidden'>Previous</span>
			</button>
			<button class='carousel-control-next' type='button' data-bs-target='#viewDataImg_1' data-bs-slide='next'>
				<span class='carousel-control-next-icon rounded bg-dark' aria-hidden='true'></span>
				<span class='visually-hidden'>Next</span>
			</button>
		</div>";
		$arrCol['Img1'] = $DataImg_1;
	}

	if($n > 0) {
		$DataImg = "
		<div id='viewDataImg' class='carousel slide' data-bs-touch='false' data-bs-interval='false'>
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
		$arrCol['Img6'] = $DataImg;
	}


	// ใบโปร/ใบขาย
	$n9 = 0; $DataImg9 = "";
	$filesIMG_9 = glob("../../../../image/products/".$ItemCode."/9/*.{jpg,png}",GLOB_BRACE);
	if(isset($filesIMG_9[0])) {
		if(count($filesIMG_9) > 1) {
			for ($i = 0; $i < count($filesIMG_9); $i++){
				$ts = date("Ymdhis");
				if($i == 0) {
					$DataImg9 .= "
					<div class='carousel-item active text-center p-2'>
						<img src='".$filesIMG_9[$i]."?v=$ts' style='width: 100%;' />
					</div>";
				}else{
					$DataImg9 .= "
					<div class='carousel-item text-center p-2'>
						<img src='".$filesIMG_9[$i]."?v=$ts' style='width: 100%;' />
					</div>";
				}
				$n9++;
			}
		}else{
			$ts = date("Ymdhis");
			$arrCol['Img9'] = "<img src='".$filesIMG_9[0]."?v=$ts' style='width: 80%'/>";
		}
	}
	if($n9 > 0) {
		$DataImg9 = "
		<div id='viewDataImg' class='carousel slide' data-bs-touch='false' data-bs-interval='false'>
			<div class='carousel-inner'>".$DataImg9."</div>
			<button class='carousel-control-prev' type='button' data-bs-target='#viewDataImg' data-bs-slide='prev'>
				<span class='carousel-control-prev-icon rounded bg-dark' aria-hidden='true'></span>
				<span class='visually-hidden'>Previous</span>
			</button>
			<button class='carousel-control-next' type='button' data-bs-target='#viewDataImg' data-bs-slide='next'>
				<span class='carousel-control-next-icon rounded bg-dark' aria-hidden='true'></span>
				<span class='visually-hidden'>Next</span>
			</button>
		</div>";
		$arrCol['Img9'] = $DataImg9;
	}
}

if($_GET['a'] == 'AddIMG') {
	$Type     = $_POST['TypeIMG'];
	$ItemCode = $_POST['ItemCode'];
	$Ukey     = $_SESSION['ukey'];
	if(isset($_FILES['FileIMG']['name'])) {
		$Totals = count($_FILES['FileIMG']['name'])-1;
		// if($Type == 1 && $Type == '1') {
		// 	$filesIMG = glob("../../../../image/products/".$ItemCode."/1/*.{jpg,png}",GLOB_BRACE);
		// 	$i_file = 0;
		// 	if(isset($filesIMG[0])) {
		// 		for ($i = 0; $i < count($filesIMG); $i++){
		// 			$i_file++;
		// 		}
		// 	}
		// 	$i_file = $i_file-1;
		// }

		for($i = 0; $i <= $Totals; $i++) {
			$FileProcess = explode(".",$_FILES['FileIMG']['name'][$i]);
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
			$tmpFilePath = $_FILES['FileIMG']['tmp_name'][$i];
			$FileDirName = $ItemCode."-".$i;
			// $FileDirName = $ItemCode."-".(($Type == 1 && $Type == '1') ? ($i_file+($i+1)) : $i);
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../image/products/$ItemCode/$Type/".$FileDirName.".".$FileExt;
				if (!file_exists("../../../../image/products/$ItemCode/$Type")) {
					mkdir("../../../../image/products/$ItemCode/$Type", 0777, true);
				}
				if($i == 0) {
					$SQL = "SELECT * FROM skubook_attach WHERE ItemCode = '$ItemCode' AND Type = '$Type' AND FileStatus = 'A'";
					$QRY = MySQLSelectX($SQL);
					while($RST = mysqli_fetch_array($QRY)) {
						if(isset($RST['FileDirName'])) {
							unlink("../../../../image/products/$ItemCode/$Type/".$RST['FileDirName'].".".$RST['FileExt']);
						}
					}
					$UPDATE = "UPDATE skubook_attach SET FileStatus = 'I' WHERE ItemCode = '$ItemCode' AND Type = '$Type' AND FileStatus = 'A'";
					MySQLUpdate($UPDATE);
				}
				move_uploaded_file($tmpFilePath,$NewFilePath);
				$INSERT = "
					INSERT INTO skubook_attach 
					   SET ItemCode = '$ItemCode', 
						Type = '$Type', 
						VisOrder = $i, 
						FileOriName = '$FileOriName', 
						FileExt = '$FileExt', 
						FileDirName = '".$FileDirName."', 
						FileStatus = 'A',
						UploadUkey = '$Ukey',
						UploadDate = NOW()";
				MySQLInsert($INSERT);
			}
		}
	}
}

if($_GET['a'] == 'AddIMG_MK') {
	$Type     = $_POST['TypeIMG'];
	$ItemCode = $_POST['ItemCode'];
	$Ukey     = $_SESSION['ukey'];
	if(isset($_FILES['FileIMG_MK']['name'])) {
		$Totals = count($_FILES['FileIMG_MK']['name'])-1;
		for($i = 0; $i <= $Totals; $i++) {
			$FileProcess = explode(".",$_FILES['FileIMG_MK']['name'][$i]);
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
			$tmpFilePath = $_FILES['FileIMG_MK']['tmp_name'][$i];

			if($Type == 9) {
				$SQL_VisOrder = "SELECT VisOrder FROM skubook_attach WHERE ItemCode = '$ItemCode' AND Type = '$Type' AND FileStatus = 'A' ORDER BY VisOrder DESC LIMIT 1";
				$RST_VisOrder = MySQLSelect($SQL_VisOrder);
				$VisOrder = (isset($RST_VisOrder['VisOrder'])) ? $RST_VisOrder['VisOrder']+1 : $i;
				$i = $VisOrder;
			}
			
			$FileDirName = $ItemCode."-".$i;
			if($tmpFilePath != "") {
				$NewFilePath = "../../../../image/products/$ItemCode/$Type/".$FileDirName.".".$FileExt;
				if (!file_exists("../../../../image/products/$ItemCode/$Type")) {
					mkdir("../../../../image/products/$ItemCode/$Type", 0777, true);
				}
				if($i == 0 && $Type != 9) {
					$SQL = "SELECT * FROM skubook_attach WHERE ItemCode = '$ItemCode' AND Type = '$Type' AND FileStatus = 'A'";
					$QRY = MySQLSelectX($SQL);
					while($RST = mysqli_fetch_array($QRY)) {
						if(isset($RST['FileDirName'])) {
							unlink("../../../../image/products/$ItemCode/$Type/".$RST['FileDirName'].".".$RST['FileExt']);
						}
					}
					$UPDATE = "UPDATE skubook_attach SET FileStatus = 'I' WHERE ItemCode = '$ItemCode' AND Type = '$Type' AND FileStatus = 'A'";
					MySQLUpdate($UPDATE);
				}
				move_uploaded_file($tmpFilePath,$NewFilePath);

				$INSERT = "
					INSERT INTO skubook_attach 
					   SET ItemCode = '$ItemCode', 
						Type = '$Type', 
						VisOrder = $i, 
						FileOriName = '$FileOriName', 
						FileExt = '$FileExt', 
						FileDirName = '".$FileDirName."', 
						FileStatus = 'A',
						UploadUkey = '$Ukey',
						UploadDate = NOW()";
				MySQLInsert($INSERT);
			}
		}
	}
}

if($_GET['a'] == 'CallDataMK') {
	$ItemCode = $_POST['ItemCode'];
	$PriceType = $_POST['PriceType'];
	$Ukey = $_SESSION['ukey'];
	$chk_uClass = 'N';
	switch($_SESSION['uClass']) {
		case 0: 
		case 2: 
		case 3: 
		case 4: 
		case 5: 
		case 13: 
		case 14: 
		case 15: 
		case 16: 
		case 17: 
		case 18: 
		case 34: $chk_uClass = 'Y'; break;
	}

	// 1. ข้อมูลสินค้า
		$SQL_SAP = "
			SELECT T0.ItemCode, T0.ItemName, T0.FrgnName, T0.CodeBars, T1.Name AS NameType1, T2.Name AS NameType2, T3.CardName, T0.SalUnitMsr, T0.U_ProductStatus,T4.Name AS Brand,T5.Name AS Model
			FROM OITM T0
			LEFT JOIN dbo.[@ITEMGROUP1] T1 ON T1.Code = T0.U_Group1
			LEFT JOIN dbo.[@ITEMGROUP2] T2 ON T2.Code = T0.U_Group2 
			LEFT JOIN OCRD T3 ON T3.CardCode = T0.CardCode
			LEFT JOIN dbo.[@BRAND2] T4 ON T4.Code = T0.U_Brand2
			LEFT JOIN dbo.[@PROMOTION] T5 ON T5.Code = T0.U_Promotion_1
			WHERE T0.ItemCode = '$ItemCode'";
		$QRY_SAP = SAPSelect($SQL_SAP);
		$RST_SAP = odbc_fetch_array($QRY_SAP);
		$SQL_HEADER = "SELECT * FROM skubook_header WHERE ItemCode = '$ItemCode'";
		$RST_HEADER = MySQLSelect($SQL_HEADER);
		$tbody_box1 = "
			<tr> 
				<th width='15%' class='ps-4'>ประเภท (กลุ่มหลัก)</th>
				<td width='35%'>".conutf8($RST_SAP['NameType1'])."</td>
				<th width='15%'>ประเภท (กลุ่มรอง)</th>
				<td width='35%'>".conutf8($RST_SAP['NameType2'])."</td>
			</tr>
			<tr> 
				<th class='ps-4'>ชื่อภาษาไทย</th>
				<td>".conutf8($RST_SAP['ItemName'])."</td>
				<th>ชื่อภาษาอังกฤษ</th>
				<td>".conutf8($RST_SAP['FrgnName'])."</td>
			</tr>
			<tr> 
				<th class='ps-4'>รหัสสินค้า</th>
				<td>".$ItemCode."</td>
				<th>Barcode</th>
				<td>".$RST_SAP['CodeBars']."</td>
			</tr>
			<tr> 
				<th class='ps-4'>สถานะสินค้า</th>
				<td>".$RST_SAP['U_ProductStatus']."</td>
				<th>รหัสทีมขาย</th>
				<td>".$RST_HEADER['TeamCode']."</td>
			</tr>
			<tr> 
				<th class='ps-4'>รุ่น (Model)</th>
				<td>".conutf8($RST_SAP['Model'])."</td>
				<th>ยี่ห้อ</th>
				<td>".conutf8($RST_SAP['Brand'])."</td>
			</tr>
			<tr> 
				<th class='ps-4'>สีตัวสินค้า</th>
				<td>".$RST_HEADER['ItemColor']."</td>
				<th>สีของบรรจุภัณฑ์</th>
				<td>".$RST_HEADER['BoxColor']."</td>
			</tr>
			<tr> 
				<th class='ps-4'>ทำจากวัสดุ</th>
				<td>".$RST_HEADER['MadeOf']."</td>
				<th>ประเทศผู้ผลิต</th>
				<td>".$RST_HEADER['ProCountry']."</td>
			</tr>";
			if($chk_uClass == 'Y') {
				$tbody_box1 .= "
				<tr> 
					<th class='ps-4'>ผู้ผลิต</th>
					<td colspan='3'>".conutf8($RST_SAP['CardName'])."</td>
				</tr>";
			}

		$arrCol['tbody_box1'] = $tbody_box1;

	// 2. ราคาสินค้า
		$SQL2 = "SELECT T0.ItemCode, T0.P0,T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice, T0.MTPrice2, T0.MTPrice, T1.ItemName, 
				T1.BarCode, T1.ProductStatus AS ST, T0.PriceType, T0.StartDate, T0.EndDate 
			FROM pricelist T0
			LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
			WHERE T0.ItemCode NOT LIKE '%เก่า%' AND T0.ItemCode NOT LIKE '%ZZ%' AND T1.ItemName != '' AND T0.PriceStatus = 'A' AND T0.ItemCode = '$ItemCode' AND T0.PriceType = '$PriceType'";
		$RST2 = MySQLSelect($SQL2);
		$SQL3 =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END ) AS 'LastPurPrc', T0.LstEvlPric
			FROM OITM T0 
			LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode 
			WHERE T0.ItemCode = '$ItemCode'";   
		$QRY3 = SAPSelect($SQL3);
		$RST3 = odbc_fetch_array($QRY3);
		$SQL4 = "SELECT PriceType, P1, S1Q, S1P, S2Q, S2P, StartDate, EndDate FROM pricelist WHERE PriceStatus = 'A' AND ItemCode = '$ItemCode'";
		$QRY4 = MySQLSelectX($SQL4);
		$option_pricetype = ""; $P1 = 0.00; $S1P = 0.00; $S1Q = 0.00; $S2P = 0.00; $S2Q = 0.00; $StartEndDate = " - ";
		while($RST4 = mysqli_fetch_array($QRY4)) {
			if($RST4['PriceType'] == "STD") {
				$option_pricetype .= "<option value='STD' selected>ราคามาตรฐาน</option>";
			}elseif($RST4['PriceType'] == 'PRO'){
				$option_pricetype .= "<option value='".$RST4['PriceType']."'>ราคาโปรโมชั่น</option>";
				$P1 = $RST4['P1']; 
				$S1P = $RST4['S1P']; 
				$S1Q = number_format($RST4['S1Q'],0); 
				$S2P = $RST4['S2P'];
				$S2Q = number_format($RST4['S2Q'],0);
				$StartEndDate = date("d/m/Y", strtotime($RST4['StartDate']))." <span class='fw-bolder'>&nbsp;&nbsp;ถึง&nbsp;&nbsp;</span> ".date("d/m/Y", strtotime($RST4['EndDate']));
			}else{
				$option_pricetype .= "<option value='".$RST4['PriceType']."'>".$RST4['PriceType']."</option>";
			}
		}
		$arrCol['option_pricetype'] = $option_pricetype;
		$LastPurPrc = 0.00;
		if(isset($RST3['LstEvlPric'])) {
			$LastPurPrc = $RST3['LstEvlPric'];
		}
		$tbody_box2 = "
			<tr>
				<th width='14%' class='ps-4'>ราคาตั้ง</th>
				<td width='12%' class='text-right'>";
					if(isset($RST2['P0'])) {
						$tbody_box2 .= number_format($RST2['P0'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<td width='20%'></td>
				<td width='10%'></td>
				<td width='20%'></td>
				<td width='24%'></td>
			</tr>
			<tr>
				<th class='ps-4'>ราคาปลีก</th>
				<td class='text-right'>";
					if(isset($RST2['P1'])) {
						$tbody_box2 .= number_format($RST2['P1'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th class='ps-4'>ราคาส่ง (SEMI)</th>
				<td class='text-right'>";
					if(isset($RST2['P2'])) {
						$tbody_box2 .= number_format($RST2['P2'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<td></td>
				<td></td>";
				if($chk_uClass == "Y") {
					$tbody_box2 .= "
					<th class='text-right'>GP</th>";
					if(isset($RST2['P2'])) {
						if($RST2['P2'] != 0)  { 
							$tbody_box2 .= "<td>".number_format(((($RST2['P2']-$LastPurPrc)/$RST2['P2'])*100),2)."%</td>";
						}else{ 
							$tbody_box2 .= "<td>0.00%</td>";
						}
					}else{
						$tbody_box2 .= "<td>0.00%</td>";
					}
				}else{
					$tbody_box2 .= "
					<td></td>
					<td></td>";
				}
				$tbody_box2 .= "
			</tr>
			<tr>
				<th class='ps-4'>ราคาส่ง (S1)</th>
				<td class='text-right'>";
					if(isset($RST2['S1P'])) {
						$tbody_box2 .= number_format($RST2['S1P'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<th class='text-right'>จำนวน</th>
				<td class='text-right'>";
					if(isset($RST2['S1Q'])) {
						$tbody_box2 .= number_format($RST2['S1Q'],2)." ตัว</td>";
					}else{
						$tbody_box2 .= "- ตัว</td>";
					}
					
				if($chk_uClass == "Y") {
					$tbody_box2 .= "
					<th class='text-right'>GP</th>";
					if(isset($RST2['S1Q'])) {
						if($RST2['S1P'] != 0)  { 
							$tbody_box2 .= "<td>".number_format(((($RST2['S1P']-$LastPurPrc)/$RST2['S1P'])*100),2)."%</td>";
						}else{ 
							$tbody_box2 .= "<td>0.00%</td>";
						}
					}else{
						$tbody_box2 .= "<td>0.00%</td>";
					}
				}else{
					$tbody_box2 .= "
					<td></td>
					<td></td>";
				}
				$tbody_box2 .= "
			</tr>
			<tr>
				<th class='ps-4'>ราคาส่ง (S2)</th>
				<td class='text-right'>";
					if(isset($RST2['S2P'])) {
						$tbody_box2 .= number_format($RST2['S2P'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<th class='text-right'>จำนวน</th>
				<td class='text-right'>";
					if(isset($RST2['S2Q'])) {
						$tbody_box2 .= number_format($RST2['S2Q'],2)." ตัว</td>";
					}else{
						$tbody_box2 .= "- ตัว</td>";
					}
				if($chk_uClass == "Y") {
					$tbody_box2 .= "
					<th class='text-right'>GP</th>";
					if(isset($RST2['S2Q'])) {
						if($RST2['S2P'] != 0)  { 
							$tbody_box2 .= "<td>".number_format(((($RST2['S2P']-$LastPurPrc)/$RST2['S2P'])*100),2)."%</td>";
						}else{ 
							$tbody_box2 .= "<td>0.00%</td>";
						}
					}else{
						$tbody_box2 .= "<td>0.00%</td>";
					}
				}else{
					$tbody_box2 .= "
					<td></td>
					<td></td>";
				}
				$tbody_box2 .= "
			</tr>
			<tr>
				<th class='ps-4'>ราคาส่ง (S3)</th>
				<td class='text-right'>";
					if(isset($RST2['S3P'])) {
						$tbody_box2 .= number_format($RST2['S3P'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<th class='text-right'>จำนวน</th>
				<td class='text-right'>";
					if(isset($RST2['S3Q'])) {
						$tbody_box2 .= number_format($RST2['S3Q'],2)." ตัว</td>";
					}else{
						$tbody_box2 .= "- ตัว</td>";
					}
				if($chk_uClass == "Y") {
					$tbody_box2 .= "
					<th class='text-right'>GP</th>";
					if(isset($RST2['S3Q'])) {
						if($RST2['S3P'] != 0)  { 
							$tbody_box2 .= "<td>".number_format(((($RST2['S3P']-$LastPurPrc)/$RST2['S3P'])*100),2)."%</td>";
						}else{ 
							$tbody_box2 .= "<td>0.00%</td>";
						}
					}else{
						$tbody_box2 .= "<td>0.00%</td>";
					}
					
				}else{
					$tbody_box2 .= "
					<td></td>
					<td></td>";
				}
				$tbody_box2 .= "
			</tr>
			<tr>
				<th class='ps-4'>ราคา (ผจก)</th>
				<td class='text-right'>";
					if(isset($RST2['MgrPrice'])) {
						$tbody_box2 .= number_format($RST2['MgrPrice'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<td></td>
				<td></td>";
				if($chk_uClass == "Y") {
					$tbody_box2 .= "
					<th class='text-right'>GP</th>";
					if(isset($RST2['MgrPrice'])) {
						if($RST2['MgrPrice'] != 0)  { 
							$tbody_box2 .= "<td>".number_format(((($RST2['MgrPrice']-$LastPurPrc)/$RST2['MgrPrice'])*100),2)."%</td>";
						}else{ 
							$tbody_box2 .= "<td>0.00%</td>";
						}
					}else{
						$tbody_box2 .= "<td>0.00%</td>";
					}
				}else{
					$tbody_box2 .= "
					<td></td>
					<td></td>";
				}
				$tbody_box2 .= "
			</tr>
			<tr>
				<th class='ps-4'>ราคาปลีก MT</th>
				<td class='text-right'>";
					if(isset($RST2['MTPrice'])) {
						$tbody_box2 .= number_format($RST2['MTPrice'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<td></td>
				<td></td>";
				if($chk_uClass == "Y") {
					$tbody_box2 .= "
					<th class='text-right'>GP</th>";
					if(isset($RST2['MTPrice'])) {
						if($RST2['MTPrice'] != 0)  { 
							$tbody_box2 .= "<td>".number_format(((($RST2['MTPrice']-$LastPurPrc)/$RST2['MTPrice'])*100),2)."%</td>";
						}else{ 
							$tbody_box2 .= "<td>0.00%</td>";
						}
					}else{
						$tbody_box2 .= "<td>0.00%</td>";
					}
				}else{
					$tbody_box2 .= "
					<td></td>
					<td></td>";
				}
				$tbody_box2 .= "
			</tr>
			<tr>
				<th class='ps-4'>ราคาส่ง MT</th>
				<td class='text-right'>";
					if(isset($RST2['MTPrice2'])) {
						$tbody_box2 .= number_format($RST2['MTPrice2'],2)." บาท";
					}else{
						$tbody_box2 .= "- บาท";
					}
				$tbody_box2 .= "
				</td>
				<td></td>
				<td></td>";
				if($chk_uClass == "Y") {
					$tbody_box2 .= "
					<th class='text-right'>GP</th>";
					if(isset($RST2['MTPrice2'])) {
						if($RST2['MTPrice2'] != 0)  { 
							$tbody_box2 .= "<td>".number_format(((($RST2['MTPrice2']-$LastPurPrc)/$RST2['MTPrice2'])*100),2)."%</td>";
						}else{ 
							$tbody_box2 .= "<td>0.00%</td>";
						}
					}else{
						$tbody_box2 .= "<td>0.00%</td>";
					}
				}else{
					$tbody_box2 .= "
					<td></td>
					<td></td>";
				}
				$tbody_box2 .= "
			</tr>";
			if($chk_uClass == "Y") {
				$tbody_box2 .= "
				<tr>
					<td colspan='3' class='ps-4'><span class='fw-bolder'>ต้นทุนปัจจุบัน</span> ".number_format($LastPurPrc,2)." บาท</td>
					<td colspan='3'><span class='fw-bolder'>ต้นทุก Lot ที่ผ่านมา</span> ";
						if(isset($RST3['LastPurPrc'])) {
							$tbody_box2 .= number_format($RST3['LastPurPrc'],2)." บาท";
						}else{
							$tbody_box2 .= "- บาท";
						}
					$tbody_box2 .= "
					</td>
				</tr>";
			}
		$arrCol['tbody_box2'] = $tbody_box2;

	// 3. โปรโมชั่น
		$SQL5 = "
			SELECT T0.TeamCode,T1.ItemCode,
				SUM(IFNULL(T1.Tar_M01,0)+
				IFNULL(T1.Tar_M02,0)+
				IFNULL(T1.Tar_M03,0)+
				IFNULL(T1.Tar_M04,0)+
				IFNULL(T1.Tar_M05,0)+
				IFNULL(T1.Tar_M06,0)+
				IFNULL(T1.Tar_M07,0)+
				IFNULL(T1.Tar_M08,0)+
				IFNULL(T1.Tar_M09,0)+
				IFNULL(T1.Tar_M10,0)+
				IFNULL(T1.Tar_M11,0)+
				IFNULL(T1.Tar_M12,0)) as Target 
			FROM tarsku_header T0
			LEFT JOIN tarsku_detail T1 ON T0.CPEntry = T1.CPEntry
			WHERE (DATE(NOW()) BETWEEN T0.StartDate AND T0.EndDate) AND T0.CANCELED = 'N' AND T1.TargetStatus = 'A'  AND ItemCode = '$ItemCode'
			GROUP BY T0.TeamCode,T1.ItemCode";
		$QRY5 = MySQLSelectX($SQL5);
		$MT1 = 0; $MT2 = 0; $TT2 = 0; $OULTT1 = 0; $ONL = 0;
		while($RST5 = mysqli_fetch_array($QRY5)) {
			switch($RST5['TeamCode']) {
				case 'MT1': $MT1 = $MT1+$RST5['Target']; break;
				case 'MT2': $MT2 = $MT2+$RST5['Target']; break;
				case 'TT2': $TT2 = $TT2+$RST5['Target']; break;
				case 'OUL': 
				case 'TT1': $OULTT1 = $OULTT1+$RST5['Target']; break;
				case 'ONL': $ONL = $ONL+$RST5['Target']; break;
			}
		}

		$SQL6 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '9'";
		if(CHKRowDB($SQL6) == 0) {
			$Header6 = [ 'สถานะใหม่', 'หมายเหตุการจัดโปร' ];
			for($i = 0; $i < count($Header6); $i++) {
				$INSERT_DETAIL_6 = "
					INSERT INTO skubook_detail 
					SET ItemCode = '$ItemCode', 
						Type = '9', 
						Header = '".$Header6[$i]."', 
						Detail = NULL, 
						CreateUkey = '$Ukey', 
						CreateDate = NOW()";
				MySQLInsert($INSERT_DETAIL_6);
			}
		}

		$QRY6 = MySQLSelectX($SQL6);
		$Data6 = ""; $tmpID_Type9 = ""; $i = 0;
		while($RST6 = mysqli_fetch_array($QRY6)) {
			$Detail = "";
			if($RST6['Detail'] != "" || $RST6['Detail'] != null) {
				$Detail = $RST6['Detail'];
			}
			$Data6 .= "
			<tr>
				<th class='ps-4'>".$RST6['Header']."</th>
				<td colspan='4'><input type='text' class='form-control-custom DETAIL_Type".$RST6['Type']."' placeholder='-' name='Detail_".$RST6['ID']."' id='Detail_".$RST6['ID']."' value='$Detail' disabled></td>
			</tr>
			";

			$i++;
			$tmpID_Type9 .= $RST6['ID'];
			if($i < CHKRowDB($SQL6)) {
				$tmpID_Type9 .= ",";
			}
		}

		$tbody_box3 = "
			<tr>
				<th width='14%' class='ps-4'>ราคาปลีก</th>
				<td width='12%' class='text-right'>";
					if($P1 != 0) {
						$tbody_box3 .= number_format($P1,2)." บาท";
					}else{
						$tbody_box3 .= "0 บาท";
					}
				$tbody_box3 .= "
				</td>
				<th width='20%' class='text-right'>จำนวน</th>
				<td width='10%'>1 ตัว</td>
				<th width='20%' class='text-right'>GP</th>";
				if($P1 != 0)  { 
					$tbody_box3 .= "<td width='24%'>".number_format(((($P1-$LastPurPrc)/$P1)*100),2)."%</td>";
				}else{ 
					$tbody_box3 .= "<td width='24%'>0.00%</td>";
				}
			$tbody_box3 .= "
			</tr>
			<tr>
				<th class='ps-4'>ราคาพิเศษ 1</th>
				<td class='text-right'>".number_format($S1P,2)." บาท</td>
				<th class='text-right'>จำนวน</th>
				<td>$S1Q ตัว</td>
				<th class='text-right'>GP</th>";
				if($S1P > 0)  { 
					$tbody_box3 .= "<td>".number_format(((($S1P-$LastPurPrc)/$S1P)*100),2)."%</td>";
				}else{ 
					$tbody_box3 .= "<td>0.00%</td>";
				}
			$tbody_box3 .= "
			</tr>
			<tr>
				<th class='ps-4'>ราคาพิเศษ 2</th>
				<td class='text-right'>".number_format($S2P,2)." บาท</td>
				<th class='text-right'>จำนวน</th>
				<td>$S2Q ตัว</td>
				<th class='text-right'>GP</th>";
				if($S2P > 0)  { 
					$tbody_box3 .= "<td>".number_format(((($S2P-$LastPurPrc)/$S2P)*100),2)."%</td>";
				}else{ 
					$tbody_box3 .= "<td>0.00%</td>";
				}
			$tbody_box3 .= "
			</tr>
			<tr>
				<th class='ps-4'>ระยะเวลา</th>
				<td colspan='4'>$StartEndDate</td>
			</tr>
			<tr>
				<th colspan='6' class='ps-4'>เป้า</th>
			</tr>
			<tr>
				<td></td>
				<td colspan='2'>
					<table class='table table-sm table-borderless' style='font-size: 12px;'>
						<tr>
							<th width='60%' class='text-start border'>MT1</th>
							<td width='30%' class='text-right border'><input type='number' class='form-control-custom text-right DETAIL_Type9' min='0' value='$MT1' disabled /></td>
							<td width='10%' class='border'>ตัว</td>
						</tr>
						<tr>
							<th class='text-start border'>MT2</th>
							<td class='text-right border'><input type='number' class='form-control-custom text-right DETAIL_Type9' min='0' value='$MT2' disabled /></td>
							<td class='border'>ตัว</td>
						</tr>
						<tr>
							<th class='text-start border'>TT2</th>
							<td class='text-right border'><input type='number' class='form-control-custom text-right DETAIL_Type9' min='0' value='$TT2' disabled /></td>
							<td class='border'>ตัว</td>
						</tr>
					</table>
				</td>
				<td colspan='2'>
					<table class='table table-sm table-borderless' style='font-size: 12px;'>
						<tr>
							<th width='60%' class='text-start border'>OUL/TT1</th>
							<td width='30%' class='text-right border'><input type='number' class='form-control-custom text-right DETAIL_Type9' min='0' value='$OULTT1' disabled /></td>
							<td width='10%' class='border'>ตัว</td>
						</tr>
						<tr>
							<th class='text-start border'>ONL</th>
							<td class='text-right border'><input type='number' class='form-control-custom text-right DETAIL_Type9' min='0' value='$ONL' disabled /></td>
							<td class='border'>ตัว</td>
						</tr>
						<tr>
							<th>&nbsp;</th>
							<td></td>
							<td></td>
						</tr>
					</table>
				</td>
				<td></td>
			</tr>
			$Data6
			";
		$arrCol['tbody_box3'] = $tbody_box3;
		$arrCol['tmpID_Type9'] = $tmpID_Type9;
	
	// 4. ช่องทางการขายสินค้า
		$SQL7 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '10'";
		if(CHKRowDB($SQL7) == 0) {
			$Header7 = [ 'ช่องทางขาย', 'ช่องทางลูกค้า' ];
			$DetailSale7 = [ 'MT1', 'MT2', 'TT2', 'OUL/TT1', 'ONL' ];
			$DetailCus7 = [ 'MT', 'SEMI', 'T2-T3', 'TN', 'ONL', 'TV', 'อื่นๆ' ];
			for($h = 0; $h < count($Header7); $h++) {
				if($h == 0) {
					for($i = 0; $i < count($DetailSale7); $i++) {
						$INSERT_DETAIL_6 = "
							INSERT INTO skubook_detail 
							SET ItemCode = '$ItemCode', 
								Type = '10', 
								Header = '".$Header7[$h]."', 
								Detail = '".$DetailSale7[$i]."', 
								CreateUkey = '$Ukey', 
								CreateDate = NOW()";
						MySQLInsert($INSERT_DETAIL_6);
					}
				}else{
					for($i = 0; $i < count($DetailCus7); $i++) {
						$INSERT_DETAIL_6 = "
							INSERT INTO skubook_detail 
							SET ItemCode = '$ItemCode', 
								Type = '10', 
								Header = '".$Header7[$h]."', 
								Detail = '".$DetailCus7[$i]."', 
								CreateUkey = '$Ukey', 
								CreateDate = NOW()";
						MySQLInsert($INSERT_DETAIL_6);
					}
				}
			}
		}
		$QRY7 = MySQLSelectX($SQL7); $tbody_box4_sub1 = ""; $tbody_box4_sub2 = ""; $Remark = ""; $i = 0; $tmpID_Type10 = "";
		while($RST7 = mysqli_fetch_array($QRY7)) {
			$checked = "";
			if($RST7['CheckBox'] == 'Y') { $checked = "checked"; }

			if($RST7['Header'] == 'ช่องทางขาย') {
				$tbody_box4_sub1 .= "
				<tr>
					<th class='text-start border'>".$RST7['Detail']."</th>
					<td class='border'><input class='form-check-input DETAIL_Type".$RST7['Type']."' type='checkbox' name='CheckBox_".$RST7['ID']."' id='CheckBox_".$RST7['ID']."' value='Y' $checked disabled></td>
				</tr>
				";
			}else{
				$tbody_box4_sub2 .= "
				<tr>
					<th class='text-start border'>".$RST7['Detail']."</th>
					<td class='border'><input class='form-check-input DETAIL_Type".$RST7['Type']."' type='checkbox' name='CheckBox_".$RST7['ID']."' id='CheckBox_".$RST7['ID']."' value='Y' $checked disabled></td>
				</tr>
				";

				if($RST7['Detail'] == 'อื่นๆ') {
					$Remark = "<input type='text' class='form-control-custom DETAIL_Type".$RST7['Type']."' placeholder='ระบุ' name='Remark_".$RST7['ID']."' id='Remark_".$RST7['ID']."' value='".$RST7['Remark']."' disabled>";
				}
			}

			$i++;
			$tmpID_Type10 .= $RST7['ID'];
			if($i < CHKRowDB($SQL7)) {
				$tmpID_Type10 .= ",";
			}
		}
		$tbody_box4 = "
			<tr>
				<th colspan='3' class='text-center ps-4'>ช่องทางขาย</th>
				<th colspan='3' class='text-center'>ช่องทางลูกค้า</th>
			</tr>
			<tr>
				<td width='15%'></td>
				<td class='text-center ps-4'>
					<table class='table table-sm table-borderless' style='font-size: 12px;'>
						$tbody_box4_sub1
						<tr>
							<th>&nbsp;</th>
							<td></td>
						</tr>
						<tr>
							<th>&nbsp;</th>
							<td></td>
						</tr>
					</table>
				</td>
				<td width='10%'></td>
				<td width='10%'></td>
				<td class='text-center ps-4'>
					<table class='table table-sm table-borderless' style='font-size: 12px;'>
						$tbody_box4_sub2
					</table>
				</td>
				<td width='15%' class='align-bottom' style='padding-bottom: 30px;'>$Remark</td>
			</tr>
		";
		$arrCol['tbody_box4'] = $tbody_box4;
		$arrCol['tmpID_Type10'] = $tmpID_Type10;
	// 5. VDO Utility
		$SQL8 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '11'";
		$tbody_box5 = ""; $ChkDataType11 = 'Y'; $tmpID_Type11 = "";
		if(CHKRowDB($SQL8) == 0) {
			$ChkDataType11 = 'N';
			$tbody_box5 .= "<tr><td class='text-center'>ยังไม่มีข้อมูล</td></tr>";
		}else{
			$QRY8 = MySQLSelectX($SQL8);
			$i = 0; $r = 0;
			while($RST8 = mysqli_fetch_array($QRY8)) {
				$r++;
				$tbody_box5 .= "
				<tr>
					<th width='8%' class='ps-4 align-middle'>$r</th>
					<td>
						<input type='text' class='form-control-custom fw-bolder ps-0 DETAIL_Type".$RST8['Type']."' name='Header_".$RST8['ID']."' id='Header_".$RST8['ID']."' value='".$RST8['Header']."' disabled>
						<br>
						<input type='text' class='form-control-custom ps-0 DETAIL_Type".$RST8['Type']."' name='Detail_".$RST8['ID']."' id='Detail_".$RST8['ID']."' value='".$RST8['Detail']."' disabled>
					</td>
				</tr>";

				$i++;
				$tmpID_Type11 .= $RST8['ID'];
				if($i < CHKRowDB($SQL8)) {
					$tmpID_Type11 .= ",";
				}
			}
		}
		$arrCol['tbody_box5'] = $tbody_box5;
		$arrCol['ChkDataType11'] = $ChkDataType11;
		$arrCol['tmpID_Type11'] = $tmpID_Type11;
}

if($_GET['a'] == 'SelectPriceType') {
	$ItemCode = $_POST['ItemCode'];
	$PriceType = $_POST['PriceType'];
	$chk_uClass = 'N';
	switch($_SESSION['uClass']) {
		case 0: 
		case 2: 
		case 3: 
		case 4: 
		case 5: 
		case 13: 
		case 14: 
		case 15: 
		case 16: 
		case 17: 
		case 18: 
		case 34: $chk_uClass = 'Y'; break;
	}
	$SQL2 = "SELECT T0.ItemCode, T0.P0,T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice, T0.MTPrice2, T0.MTPrice, T1.ItemName, T1.BarCode, T1.ProductStatus AS ST, T0.PriceType
		FROM pricelist T0
		LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
		WHERE T0.ItemCode NOT LIKE '%เก่า%' AND T0.ItemCode NOT LIKE '%ZZ%' AND T1.ItemName != '' AND T0.PriceStatus = 'A' AND T0.ItemCode = '$ItemCode' AND T0.PriceType = '$PriceType'";
	$RST2 = MySQLSelect($SQL2);
	$SQL3 =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END ) AS 'LastPurPrc', T0.LstEvlPric
		FROM OITM T0 
		LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode 
		WHERE T0.ItemCode = '$ItemCode'";   
	$QRY3 = SAPSelect($SQL3);
	$RST3 = odbc_fetch_array($QRY3);
	$SQL4 = "SELECT PriceType FROM pricelist WHERE PriceStatus = 'A' AND ItemCode = '$ItemCode'";
	$QRY4 = MySQLSelectX($SQL4);
	$option_pricetype = "";
	while($RST4 = mysqli_fetch_array($QRY4)) {
		if($RST4['PriceType'] == "STD") {
			$option_pricetype .= "<option value='STD' selected>ราคามาตรฐาน</option>";
		}elseif($RST4['PriceType'] == 'PRO'){
			$option_pricetype .= "<option value='".$RST4['PriceType']."'>ราคาโปรโมชั่น</option>";
		}else{
			$option_pricetype .= "<option value='".$RST4['PriceType']."'>".$RST4['PriceType']."</option>";
		}
	}
	$arrCol['option_pricetype'] = $option_pricetype;
	$LastPurPrc = 0.00;
	if(isset($RST3['LstEvlPric'])) {
		$LastPurPrc = $RST3['LstEvlPric'];
	}
	$tbody_box2 = "
		<tr>
			<th width='14%' class='ps-4'>ราคาตั้ง</th>
			<td width='12%' class='text-right'>".number_format($RST2['P0'],2)." บาท</td>
			<td width='20%'></td>
			<td width='10%'></td>
			<td width='20%'></td>
			<td width='24%'></td>
		</tr>
		<tr>
			<th class='ps-4'>ราคาปลีก</th>
			<td class='text-right'>".number_format($RST2['P1'],2)." บาท</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th class='ps-4'>ราคาส่ง (SEMI)</th>
			<td class='text-right'>".number_format($RST2['P2'],2)." บาท</td>
			<td></td>
			<td></td>";
			if($chk_uClass == "Y") {
				$tbody_box2 .= "
				<th class='text-right'>GP</th>";
				if($RST2['P2'] != 0)  { 
					$tbody_box2 .= "<td>".number_format(((($RST2['P2']-$LastPurPrc)/$RST2['P2'])*100),2)."%</td>";
				}else{ 
					$tbody_box2 .= "<td>0.00%</td>";
				}
			}else{
				$tbody_box2 .= "
				<td></td>
				<td></td>";
			}
			$tbody_box2 .= "
		</tr>
		<tr>
			<th class='ps-4'>ราคาส่ง (S1)</th>
			<td class='text-right'>".number_format($RST2['S1P'],2)." บาท</td>
			<th class='text-right'>จำนวน</th>
			<td class='text-right'>".number_format($RST2['S1Q'],0)." ตัว</td>";
			if($chk_uClass == "Y") {
				$tbody_box2 .= "
				<th class='text-right'>GP</th>";
				if($RST2['S1P'] != 0)  { 
					$tbody_box2 .= "<td>".number_format(((($RST2['S1P']-$LastPurPrc)/$RST2['S1P'])*100),2)."%</td>";
				}else{ 
					$tbody_box2 .= "<td>0.00%</td>";
				}
			}else{
				$tbody_box2 .= "
				<td></td>
				<td></td>";
			}
			$tbody_box2 .= "
		</tr>
		<tr>
			<th class='ps-4'>ราคาส่ง (S2)</th>
			<td class='text-right'>".number_format($RST2['S2P'],2)." บาท</td>
			<th class='text-right'>จำนวน</th>
			<td class='text-right'>".number_format($RST2['S2Q'],0)." ตัว</td>";
			if($chk_uClass == "Y") {
				$tbody_box2 .= "
				<th class='text-right'>GP</th>";
				if($RST2['S2P'] != 0)  { 
					$tbody_box2 .= "<td>".number_format(((($RST2['S2P']-$LastPurPrc)/$RST2['S2P'])*100),2)."%</td>";
				}else{ 
					$tbody_box2 .= "<td>0.00%</td>";
				}
			}else{
				$tbody_box2 .= "
				<td></td>
				<td></td>";
			}
			$tbody_box2 .= "
		</tr>
		<tr>
			<th class='ps-4'>ราคาส่ง (S3)</th>
			<td class='text-right'>".number_format($RST2['S3P'],2)." บาท</td>
			<th class='text-right'>จำนวน</th>
			<td class='text-right'>".number_format($RST2['S3Q'],0)." ตัว</td>";
			if($chk_uClass == "Y") {
				$tbody_box2 .= "
				<th class='text-right'>GP</th>";
				if($RST2['S3P'] != 0)  { 
					$tbody_box2 .= "<td>".number_format(((($RST2['S3P']-$LastPurPrc)/$RST2['S3P'])*100),2)."%</td>";
				}else{ 
					$tbody_box2 .= "<td>0.00%</td>";
				}
			}else{
				$tbody_box2 .= "
				<td></td>
				<td></td>";
			}
			$tbody_box2 .= "
		</tr>
		<tr>
			<th class='ps-4'>ราคา (ผจก)</th>
			<td class='text-right'>".number_format($RST2['MgrPrice'],2)." บาท</td>
			<td></td>
			<td></td>";
			if($chk_uClass == "Y") {
				$tbody_box2 .= "
				<th class='text-right'>GP</th>";
				if($RST2['MgrPrice'] != 0)  { 
					$tbody_box2 .= "<td>".number_format(((($RST2['MgrPrice']-$LastPurPrc)/$RST2['MgrPrice'])*100),2)."%</td>";
				}else{ 
					$tbody_box2 .= "<td>0.00%</td>";
				}
			}else{
				$tbody_box2 .= "
				<td></td>
				<td></td>";
			}
			$tbody_box2 .= "
		</tr>
		<tr>
			<th class='ps-4'>ราคาปลีก MT</th>
			<td class='text-right'>".number_format($RST2['MTPrice'],2)." บาท</td>
			<td></td>
			<td></td>";
			if($chk_uClass == "Y") {
				$tbody_box2 .= "
				<th class='text-right'>GP</th>";
				if($RST2['MTPrice'] != 0)  { 
					$tbody_box2 .= "<td>".number_format(((($RST2['MTPrice']-$LastPurPrc)/$RST2['MTPrice'])*100),2)."%</td>";
				}else{ 
					$tbody_box2 .= "<td>0.00%</td>";
				}
			}else{
				$tbody_box2 .= "
				<td></td>
				<td></td>";
			}
			$tbody_box2 .= "
		</tr>
		<tr>
			<th class='ps-4'>ราคาส่ง MT</th>
			<td class='text-right'>".number_format($RST2['MTPrice2'],2)." บาท</td>
			<td></td>
			<td></td>";
			if($chk_uClass == "Y") {
				$tbody_box2 .= "
				<th class='text-right'>GP</th>";
				if($RST2['MTPrice2'] != 0)  { 
					$tbody_box2 .= "<td>".number_format(((($RST2['MTPrice2']-$LastPurPrc)/$RST2['MTPrice2'])*100),2)."%</td>";
				}else{ 
					$tbody_box2 .= "<td>0.00%</td>";
				}
			}else{
				$tbody_box2 .= "
				<td></td>
				<td></td>";
			}
			$tbody_box2 .= "
		</tr>";
		if($chk_uClass == "Y") {
			$tbody_box2 .= "
			<tr>
				<td colspan='3' class='ps-4'><span class='fw-bolder'>ต้นทุนปัจจุบัน</span> ".number_format($LastPurPrc,2)." บาท</td>
				<td colspan='3'><span class='fw-bolder'>ต้นทุก Lot ที่ผ่านมา</span> ".number_format(($RST3['LastPurPrc'] *1.07),2)." บาท</td> 
			</tr>";
		}
		//$RST3['LastPurPrc'] *1.07    log_it:ITL-24060944
	$arrCol['tbody_box2'] = $tbody_box2;
}

if ($_GET['a'] == 'GetBar'){
	$sql1 = "SELECT CodeBars FROM OITM WHERE ItemCode = '".$_POST['ItemCode']."'";
	$WaiQry = SAPSelect($sql1);
	$DataBar = odbc_fetch_array($WaiQry);
	
	if (substr($DataBar['CodeBars'],0,3) != '885' || ($_POST['qty']=='1' && $_POST['Pack'] == 'Box')){
		$arrCol['newBar']=$DataBar['CodeBars'];
	}else{
		switch ($_POST['Pack']){
			case 'Box' :
				$arrCol['newBar'] = gen14digit($DataBar['CodeBars'],2);
			break;
			case 'CTN' :
				$sql1 = "SELECT T0.Detail FROM skubook_detail T0 WHERE T0.Type=2 AND T0.Header = 'ขนาดบรรจุ (กล่อง)' AND T0.ItemCode = '".$_POST['ItemCode']."'";
				$BoxCHK = MySQLSelect($sql1);
				if (intval($BoxCHK['Detail']) > 1){
					$arrCol['newBar'] = gen14digit($DataBar['CodeBars'],3);
				}else{
					$arrCol['newBar'] = gen14digit($DataBar['CodeBars'],2);
				}
			break;
		}
	}
}

if($_GET['a'] == 'Excel') {
	$ItemCode = $_POST['ItemCode'];
	$PriceType = $_POST['PriceType'];
	$SQL_HEADER = "SELECT * FROM skubook_header WHERE ItemCode = '$ItemCode'";
    $RST_HEADER = MySQLSelect($SQL_HEADER);
	$chk_uClass = 'N';
    switch($_SESSION['uClass']) {
        case 0: 
        case 2: 
        case 3: 
        case 4: 
        case 5: 
        case 13: 
        case 14: 
        case 15: 
        case 16: 
        case 17: 
        case 18: 
        case 34: $chk_uClass = 'Y'; break;
    }


	$spreadsheet = new Spreadsheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงาน SKU BOOK บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงาน SKU BOOK บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(9);

	// Style
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.5 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBoldCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ], 'font' => [ 'bold' => true ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextLeft  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];
	$TextBoldRight  = ['font' => [ 'bold' => true ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];

	$sheet1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'PD');
	$spreadsheet->addSheet($sheet1, 0);

	$sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'MK');
	$spreadsheet->addSheet($sheet2, 1);

	$sheet3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'PU');
	$spreadsheet->addSheet($sheet3, 2);

	$sheet4 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'ฝ่ายขาย');
	$spreadsheet->addSheet($sheet4, 3);

	$sheet5 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'สินค้าใหม่');
	$spreadsheet->addSheet($sheet5, 4);

	$spreadsheet->setActiveSheetIndexByName('Worksheet');
	$sheetIndex = $spreadsheet->getActiveSheetIndex();
	$spreadsheet->removeSheetByIndex($sheetIndex);

	$SQL_SAP = "
		SELECT T0.ItemCode, T0.ItemName, T0.FrgnName, T0.CodeBars, T1.Name AS NameType1, T2.Name AS NameType2, T3.CardName, T0.SalUnitMsr, T0.U_ProductStatus,T4.Name AS Brand,T5.Name AS Model
		FROM OITM T0
		LEFT JOIN dbo.[@ITEMGROUP1] T1 ON T1.Code = T0.U_Group1
		LEFT JOIN dbo.[@ITEMGROUP2] T2 ON T2.Code = T0.U_Group2 
		LEFT JOIN OCRD T3 ON T3.CardCode = T0.CardCode
		LEFT JOIN dbo.[@BRAND2] T4 ON T4.Code = T0.U_Brand2
		LEFT JOIN dbo.[@PROMOTION] T5 ON T5.Code = T0.U_Promotion_1
		WHERE T0.ItemCode = '$ItemCode'";
	$QRY_SAP = SAPSelect($SQL_SAP);
	$RST_SAP = odbc_fetch_array($QRY_SAP);

	// #Sheet 1 PD
		$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(25);
		$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(50);
		$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(25);
		$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(50);
		$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(60);

		$RowS1 = 1;
		$sheet1->setCellValue('A'.$RowS1,"เลขที่ SKU : $ItemCode วันที่ : ".date("d/m/Y",strtotime($RST_HEADER['CreateDate'])));
		$sheet1->getStyle('A'.$RowS1)->applyFromArray($PageHeader);
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
		$RowS1++;

		// 1. ข้อมูลสินค้า
			$sheet1->setCellValue('A'.$RowS1,"1. ข้อมูลสินค้า");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$sheet1->setCellValue('A'.$RowS1,"ประเภท (กลุ่มหลัก)");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('B'.$RowS1,conutf8($RST_SAP['NameType1']));
			$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet1->setCellValue('C'.$RowS1,"ประเภท (กลุ่มรอง)");
			$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('D'.$RowS1,conutf8($RST_SAP['NameType2']));
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS1++;

			$sheet1->setCellValue('A'.$RowS1,"ชื่อภาษาไทย");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('B'.$RowS1,conutf8($RST_SAP['ItemName']));
			$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('C'.$RowS1,"ชื่อภาษาอังกฤษ");
			$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('D'.$RowS1,conutf8($RST_SAP['FrgnName']));
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS1++;

			$sheet1->setCellValue('A'.$RowS1,"รหัสสินค้า");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('B'.$RowS1,$RST_SAP['ItemCode']);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('C'.$RowS1,"Barcode");
			$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('D'.$RowS1,$RST_SAP['CodeBars']);
			$sheet1->getStyle('D'.$RowS1)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS1++;

			$sheet1->setCellValue('A'.$RowS1,"สถานะสินค้า");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('B'.$RowS1,$RST_SAP['U_ProductStatus']);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('C'.$RowS1,"รหัสทีมขาย");
			$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('D'.$RowS1,$RST_HEADER['TeamCode']);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS1++;

			$sheet1->setCellValue('A'.$RowS1,"รุ่น (Model)");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('B'.$RowS1,conutf8($RST_SAP['Model']));
			$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('C'.$RowS1,"ยี่ห้อ");
			$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('D'.$RowS1,$RST_SAP['Brand']);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS1++;

			$sheet1->setCellValue('A'.$RowS1,"สีตัวสินค้า");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('B'.$RowS1,$RST_HEADER['ItemColor']);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('C'.$RowS1,"สีของบรรจุภัณฑ์");
			$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('D'.$RowS1,$RST_HEADER['BoxColor']);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS1++;

			$sheet1->setCellValue('A'.$RowS1,"ทำจากวัสดุ");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('B'.$RowS1,$RST_HEADER['MadeOf']);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('C'.$RowS1,"ประเทศผู้ผลิต");
			$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('D'.$RowS1,$RST_HEADER['ProCountry']);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS1++;

			if($chk_uClass == 'Y') {
				$sheet1->setCellValue('A'.$RowS1,"ผู้ผลิต");
				$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

				$sheet1->setCellValue('B'.$RowS1,conutf8($RST_SAP['CardName']));
				$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);

				$RowS1++;
			}

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;

		// 2. คุณสมบัติ
			$sheet1->setCellValue('A'.$RowS1,"2. คุณสมบัติ");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$Chk_DETAIL_Type1 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '1'";
			if(CHKRowDB($Chk_DETAIL_Type1) == 0) {
				$sheet1->setCellValue('A'.$RowS1,"ไม่มีข้อมูล");
				$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextCenter);
				$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$RowS1++;
			}else{
				$tmpRow = 0;
				$QRY_DETAIL_Type1 = MySQLSelectX($Chk_DETAIL_Type1);
				while ($RST_DETAIL_Type1 = mysqli_fetch_array($QRY_DETAIL_Type1)) {
					$tmpRow++; 
					if($tmpRow == 1) {
						$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type1['Header']);
						$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('B'.$RowS1,$RST_DETAIL_Type1['Detail']);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
					}else{
						$sheet1->setCellValue('C'.$RowS1,$RST_DETAIL_Type1['Header']);
						$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('D'.$RowS1,$RST_DETAIL_Type1['Detail']);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$RowS1++;
						$tmpRow = 0;
					}
				}

				if($tmpRow != 0) {
					$sheet1->setCellValue('C'.$RowS1,"");
					$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
		
					$sheet1->setCellValue('D'.$RowS1,"");
					$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
		
					$RowS1++;
				}
			}

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;

		// 3. รายละเอียดบรรจุภัณฑ์
			$SQL_DETAIL_Type2 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '2'";
			$QRY_DETAIL_Type2 = MySQLSelectX($SQL_DETAIL_Type2);

			$sheet1->setCellValue('A'.$RowS1,"3. รายละเอียดบรรจุภัณฑ์");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$tmpRow = 0; $Sum = 0; $DataType2 = ""; $rSizeBox2 = 0; $DataSizeBox2 = ""; $HeaderSizeBox2 = "";
			while ($RST_DETAIL_Type2 = mysqli_fetch_array($QRY_DETAIL_Type2)) {
				$DataType2 = "";
				$tmpRow++;
				if($tmpRow == 1) { 
					if($RST_DETAIL_Type2['Header'] == 'ขนาดกล่อง 2 (ซม.)') {
						$Detail_Type2x_0 = ""; $Detail_Type2x_1 = ""; $Detail_Type2x_2 = "";
						if(isset($Detail_Type2x[0])) { 
							if($Detail_Type2x[0] != "") {
								$Detail_Type2x_0 = $Detail_Type2x[0]."x"; 
							}
						}
						if(isset($Detail_Type2x[1])) { 
							if($Detail_Type2x[1] != "") {
								$Detail_Type2x_1 = $Detail_Type2x[1]."x"; 
							}
						}
						if(isset($Detail_Type2x[2])) { 
							if($Detail_Type2x[2] != "") {
								$Detail_Type2x_2 = $Detail_Type2x[2]; 
							}
						}
						$HeaderSizeBox2 .= $RST_DETAIL_Type2['Header'];
						$DataSizeBox2 .= $Detail_Type2x_0.$Detail_Type2x_1.$Detail_Type2x_2;
					}else{
						switch ($RST_DETAIL_Type2['Header']) {
							case 'ขนาดสินค้า (ซม.)':
							case 'ขนาดกล่อง (ซม.)':
							case 'ขนาดลัง (ซม.)':
								$Detail_Type2 = explode("x",$RST_DETAIL_Type2['Detail']);
								$Detail_Type2_0 = ""; $Detail_Type2_1 = ""; $Detail_Type2_2 = "";
								if(isset($Detail_Type2[0])) { 
									if($Detail_Type2[0] != "") {
										$Detail_Type2_0 = $Detail_Type2[0]."x"; 
									}
								}
								if(isset($Detail_Type2[1])) { 
									if($Detail_Type2[1] != "") {
										$Detail_Type2_1 = $Detail_Type2[1]."x"; 
									}
								}
								if(isset($Detail_Type2[2])) { 
									if($Detail_Type2[2] != "") {
										$Detail_Type2_2 = $Detail_Type2[2]; 
									}
								}
								$DataType2 .= $Detail_Type2_0.$Detail_Type2_1.$Detail_Type2_2;
								if($RST_DETAIL_Type2['Header'] == "ขนาดสินค้า (ซม.)") {
									$DataType2 .= " (".$RST_DETAIL_Type2['Remark'].")";
								}
							break;
							case 'น้ำหนักลังรวมสินค้า (กก.)':
								$DataType2 .= $Sum;
							break;
							default:
								if($RST_DETAIL_Type2['Header'] == 'น้ำหนักรวมสินค้า (กก.)') { $Sum = $RST_DETAIL_Type2['Detail']; }
								$DataType2 .= $RST_DETAIL_Type2['Detail'];
							break;
						}
						
						$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type2['Header']);
						$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('B'.$RowS1,$DataType2);
						$sheet1->getStyle('B'.$RowS1)->applyFromArray($TextLeft);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
					}
				}else{
					switch ($RST_DETAIL_Type2['Header']) {
						case 'ขนาดบรรจุ (กล่อง)':
							if($RST_DETAIL_Type2['Detail'] != '') {
								$DataType2 .= $RST_DETAIL_Type2['Detail']." ".conutf8($RST_SAP['SalUnitMsr']);
							}else{
								$DataType2 .= "";
							}
						break;
						case 'ขนาดบรรจุ (ลัง)':
							if($RST_DETAIL_Type2['Detail'] != "" && $RST_DETAIL_Type2['Detail'] != 0) {
								$Sum = $Sum*$RST_DETAIL_Type2['Detail'];
							}else{
								$Sum = "";
							}
							$DataType2 .= $RST_DETAIL_Type2['Detail'];
						break;
						case 'น้ำหนักสินค้า (กก.)':
							$DataType2 .= $RST_DETAIL_Type2['Detail']." (".$RST_DETAIL_Type2['Remark'].")";
						break;
						default: $DataType2 .= $RST_DETAIL_Type2['Detail']; break;
					}

					$sheet1->setCellValue('C'.$RowS1,$RST_DETAIL_Type2['Header']);
					$sheet1->getStyle('C'.$RowS1)->applyFromArray($TextBold);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
		
					$sheet1->setCellValue('D'.$RowS1,$DataType2);
					$sheet1->getStyle('D'.$RowS1)->applyFromArray($TextLeft);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

					$RowS1++;

					if($RST_DETAIL_Type2['Header'] == 'ขนาดบรรจุ (กล่อง)') {
						$rSizeBox2 = $RowS1;
						$RowS1++;
					}

					$tmpRow = 0;
				}
			}

			$sheet1->setCellValue('A'.$rSizeBox2,$HeaderSizeBox2);
			$sheet1->getStyle('A'.$rSizeBox2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$rSizeBox2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('B'.$rSizeBox2,$DataSizeBox2);
			$sheet1->getStyle('B'.$rSizeBox2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$rSizeBox2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('C'.$rSizeBox2,"");
			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$rSizeBox2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('D'.$rSizeBox2,"");
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$rSizeBox2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;

		// 4. อุปกรณ์ภายในกล่อง
			$sheet1->setCellValue('A'.$RowS1,"4. อุปกรณ์ภายในกล่อง");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$Chk_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '3'";
			if(CHKRowDB($Chk_DETAIL_Type3) == 0) {
				$sheet1->setCellValue('A'.$RowS1,"ไม่มีข้อมูล");
				$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextCenter);
				$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$RowS1++;
			}else{
				$SQL_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '3'";
				$QRY_DETAIL_Type3 = MySQLSelectX($SQL_DETAIL_Type3);
				$i = 0;
				$DataType3 = "";
				while ($RST_DETAIL_Type3 = mysqli_fetch_array($QRY_DETAIL_Type3)) {
					$i++;
					$DataType3 .= $RST_DETAIL_Type3['Detail'];
					if(CHKRowDB($SQL_DETAIL_Type3) != $i) {
						$DataType3 .= ", ";
					}
				}
				$sheet1->setCellValue('A'.$RowS1,$DataType3);
				$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$RowS1++;
			}

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;
		
		// 5. วิธีการใช้งาน
			$sheet1->setCellValue('A'.$RowS1,"5. วิธีการใช้งาน");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$Chk_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '4'";
			if(CHKRowDB($Chk_DETAIL_Type3) == 0) {
				$sheet1->setCellValue('A'.$RowS1,"ไม่มีข้อมูล");
				$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextCenter);
				$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$RowS1++;
			}else{
				$QRY_DETAIL_Type3 = MySQLSelectX($Chk_DETAIL_Type3);
				while ($RST_DETAIL_Type3 = mysqli_fetch_array($QRY_DETAIL_Type3)) {
					$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type3['Detail']);
					$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
					$RowS1++;
				}
			}

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;

		// 6. จุดเด่น จุดขาย ของสินค้า
			$sheet1->setCellValue('A'.$RowS1,"6. จุดเด่น จุดขาย ของสินค้า");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$Chk_DETAIL_Type4 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '5'";
			if(CHKRowDB($Chk_DETAIL_Type4) == 0) {
				$sheet1->setCellValue('A'.$RowS1,"ไม่มีข้อมูล");
				$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextCenter);
				$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$RowS1++;
			}else{
				$QRY_DETAIL_Type4 = MySQLSelectX($Chk_DETAIL_Type4);
				while ($RST_DETAIL_Type4 = mysqli_fetch_array($QRY_DETAIL_Type4)) {
					$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type4['Detail']);
					$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
					$RowS1++;
				}
			}

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;

		// 7. การรับประกัน
			$sheet1->setCellValue('A'.$RowS1,"7. การรับประกัน");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$Chk_DETAIL_Type5 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '6'";
			$QRY_DETAIL_Type5 = MySQLSelectX($Chk_DETAIL_Type5);
			while ($RST_DETAIL_Type5 = mysqli_fetch_array($QRY_DETAIL_Type5)) {
				if($RST_DETAIL_Type5['Header'] == "ระยะเวลารับประกัน (เดือน)") {
					$Detail_Type5 = $RST_DETAIL_Type5['Detail']." ระบุ ".$RST_DETAIL_Type5['Remark'];
				}else{
					$Detail_Type5 = $RST_DETAIL_Type5['Detail'];
				}

				$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type5['Header']);
				$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
	
				$sheet1->setCellValue('B'.$RowS1,$Detail_Type5);
				$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

				$RowS1++;
			}

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;

		// 8. ข้อมูล สคบ.
			$sheet1->setCellValue('A'.$RowS1,"8. ข้อมูล สคบ.");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$SQL_DETAIL_Type5 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '7'";
			$QRY_DETAIL_Type5 = MySQLSelectX($SQL_DETAIL_Type5);
			while ($RST_DETAIL_Type5 = mysqli_fetch_array($QRY_DETAIL_Type5)) {
				switch ($RST_DETAIL_Type5['Header']) {
					case 'ชื่อสินค้า':
						$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type5['Header']);
						$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('B'.$RowS1,conutf8($RST_SAP['ItemName']));
						$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

						$RowS1++;
					break;
					case 'ผลิตจากประเทศ':
						$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type5['Header']);
						$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('B'.$RowS1,$RST_DETAIL_Type5['Remark']);
						$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

						$RowS1++;
					break;
					case 'จัดจำหน่ายโดย':
						$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type5['Header']);
						$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('B'.$RowS1,$RST_DETAIL_Type5['Detail']);
						$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

						$RowS1++;
					break;
					case 'จัดจำหน่ายโดย_2':
						$sheet1->setCellValue('A'.$RowS1,"");
						$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('B'.$RowS1,$RST_DETAIL_Type5['Detail']);
						$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

						$RowS1++;
					break;
					case 'จัดจำหน่ายโดย_3':
						$sheet1->setCellValue('A'.$RowS1,"");
						$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('B'.$RowS1,$RST_DETAIL_Type5['Detail']);
						$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

						$RowS1++;
					break;
					default:
						$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type5['Header']);
						$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
						$sheet1->setCellValue('B'.$RowS1,$RST_DETAIL_Type5['Detail']);
						$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);
						$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

						$RowS1++;
					break;
				}
			}

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;

		// 9. ข้อควรระวัง
			$sheet1->setCellValue('A'.$RowS1,"9. ข้อควรระวัง");
			$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFont()->getColor()->setARGB('ff000000');
			$RowS1++;

			$Chk_DETAIL_Type9 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '8'";
			$QRY_DETAIL_Type9 = MySQLSelectX($Chk_DETAIL_Type9);
			while ($RST_DETAIL_Type9 = mysqli_fetch_array($QRY_DETAIL_Type9)) {
				$sheet1->setCellValue('A'.$RowS1,$RST_DETAIL_Type9['Header']);
				$sheet1->getStyle('A'.$RowS1)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
	
				$sheet1->setCellValue('B'.$RowS1,$RST_DETAIL_Type9['Detail']);
				$spreadsheet->setActiveSheetIndex(0)->mergeCells('B'.$RowS1.':D'.$RowS1);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('B'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

				$RowS1++;
			}

			$sheet1->setCellValue('A'.$RowS1,"");
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$RowS1.':D'.$RowS1);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$RowS1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$RowS1++;

			
			for($cl = 2; $cl <= $RowS1; $cl++) {
				$spreadsheet->setActiveSheetIndex(0)->getRowDimension($cl)->setRowHeight(18);
			}
			
		// Image
			$SQL = "SELECT * FROM skubook_attach WHERE ItemCode = '$ItemCode' AND FileStatus = 'A' AND Type IN (1,2,3,4,5)";
			$QRY = MySQLSelectX($SQL);
			while($RST = mysqli_fetch_array($QRY)) {
				$Img[$RST['Type']] = $RST['FileDirName'].'.'.$RST['FileExt'];
			}
			$NameImg = ['','รูปสินค้า','รูปบรรจุภัณฑ์','อุปกรณ์ภายในกล่อง','รูปลังสินค้า','รูป Barcode'];

			$sheet1->setCellValue('E1',"");
			$spreadsheet->setActiveSheetIndex(0)->getStyle('E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$n = 2;
			for($i = 1; $i <= 5; $i++) {
				if(isset($Img[$i])) {
					$sheet1->setCellValue('E'.$n,$NameImg[$i]);
					$sheet1->getStyle('E'.$n)->applyFromArray($TextBoldCenter);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$n)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
					$n++;
					$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
					$drawing->setName($NameImg[$i]);
					$drawing->setDescription($NameImg[$i]);
					$drawing->setPath("../../../../image/products/".$ItemCode."/".$i."/".$Img[$i]);
					$drawing->setHeight(150); 
					$drawing->setCoordinates('E'.$n);
					$drawing->setOffsetX(10);
					$drawing->setOffsetY(10);
					$drawing->setRotation(0);
					$drawing->setWorksheet($spreadsheet->setActiveSheetIndex(0));
				}else{
					$sheet1->setCellValue('E'.$n,$NameImg[$i]);
					$sheet1->getStyle('E'.$n)->applyFromArray($TextBoldCenter);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$n)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
					$n++;
					$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
					$drawing->setName($NameImg[$i]);
					$drawing->setDescription($NameImg[$i]);
					$drawing->setPath("../../../../image/products/no-image.jpg");
					$drawing->setHeight(150); 
					$drawing->setCoordinates('E'.$n);
					$drawing->setOffsetX(100);
					$drawing->setRotation(0);
					$drawing->setWorksheet($spreadsheet->setActiveSheetIndex(0));
				}

				for($m = $n; $m <= $n+9; $m++) {
					$sheet1->setCellValue('E'.$m,"");
					$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$m)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				}

				$n = $n+9;
			}

	// #Sheet 2 MK
		$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('A')->setWidth(25);
		$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('B')->setWidth(35);
		$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('C')->setWidth(15);
		$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('D')->setWidth(30);
		$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('E')->setWidth(15);
		$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('F')->setWidth(30);
		$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('G')->setWidth(60);

		$RowS2 = 1;
		$sheet2->setCellValue('A'.$RowS2,"เลขที่ SKU : $ItemCode วันที่ : ".date("d/m/Y",strtotime($RST_HEADER['CreateDate'])));
		$sheet2->getStyle('A'.$RowS2)->applyFromArray($PageHeader);
		$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
		$RowS2++;

		// 1. ข้อมูลสินค้า
			$sheet2->setCellValue('A'.$RowS2,"1. ข้อมูลสินค้า");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFont()->getColor()->setARGB('ff000000');
			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ประเภท (กลุ่มหลัก)");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,conutf8($RST_SAP['NameType1']));
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':C'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('D'.$RowS2,"ประเภท (กลุ่มรอง)");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('E'.$RowS2,conutf8($RST_SAP['NameType2']));
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ชื่อภาษาไทย");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,conutf8($RST_SAP['ItemName']));
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':C'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,"ชื่อภาษาอังกฤษ");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('E'.$RowS2,conutf8($RST_SAP['FrgnName']));
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"รหัสสินค้า");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,$RST_SAP['ItemCode']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':C'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,"Barcode");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('E'.$RowS2,$RST_SAP['CodeBars']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
			$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"สถานะสินค้า");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,$RST_SAP['U_ProductStatus']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':C'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,"รหัสทีมขาย");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('E'.$RowS2,$RST_HEADER['TeamCode']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"รุ่น (Model)");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,conutf8($RST_SAP['Model']));
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':C'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,"ยี่ห้อ");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('E'.$RowS2,$RST_SAP['Brand']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"สีตัวสินค้า");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,$RST_HEADER['ItemColor']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':C'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,"สีของบรรจุภัณฑ์");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('E'.$RowS2,$RST_HEADER['BoxColor']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ทำจากวัสดุ");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,$RST_HEADER['MadeOf']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':C'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,"ประเทศผู้ผลิต");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('E'.$RowS2,$RST_HEADER['ProCountry']);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			if($chk_uClass == 'Y') {
				$sheet2->setCellValue('A'.$RowS2,"ผู้ผลิต");
				$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

				$sheet2->setCellValue('B'.$RowS2,conutf8($RST_SAP['CardName']));
				$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':F'.$RowS2);

				$RowS2++;
			}

			$sheet2->setCellValue('A'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$RowS2++;

		// 2. ราคาสินค้า
			$SQL2 = "
				SELECT T0.ItemCode, T0.P0,T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice, T0.MTPrice2, T0.MTPrice, T1.ItemName, 
					T1.BarCode, T1.ProductStatus AS ST, T0.PriceType, T0.StartDate, T0.EndDate 
				FROM pricelist T0
				LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
				WHERE T0.ItemCode NOT LIKE '%เก่า%' AND T0.ItemCode NOT LIKE '%ZZ%' AND T1.ItemName != '' AND T0.PriceStatus = 'A' AND T0.ItemCode = '$ItemCode' AND T0.PriceType = '$PriceType'";
			$RST2 = MySQLSelect($SQL2);
			$SQL3 =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END ) AS 'LastPurPrc', T0.LstEvlPric
				FROM OITM T0 
				LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode 
				WHERE T0.ItemCode = '$ItemCode'";   
			$QRY3 = SAPSelect($SQL3);
			$RST3 = odbc_fetch_array($QRY3);
			$SQL4 = "SELECT PriceType, S1Q, S1P, S2Q, S2P, StartDate, EndDate  FROM pricelist WHERE PriceStatus = 'A' AND ItemCode = '$ItemCode'";
			$QRY4 = MySQLSelectX($SQL4);
			$S1P = 0.00; $S1Q = 0.00; $S2P = 0.00; $S2Q = 0.00; $StartEndDate = "-";
			while($RST4 = mysqli_fetch_array($QRY4)) {
				if($RST4['PriceType'] == 'PRO'){
					$S1P = $RST4['S1P']; 
					$S1Q = number_format($RST4['S1Q'],0); 
					$S2P = $RST4['S2P'];
					$S2Q = number_format($RST4['S2Q'],0);
					$StartEndDate = date("d/m/Y", strtotime($RST4['StartDate']))." ถึง ".date("d/m/Y", strtotime($RST4['EndDate']));
				}
			}
			$LastPurPrc = 0.00;
			if(isset($RST3['LstEvlPric'])) {
				$LastPurPrc = $RST3['LstEvlPric'] * 1.07;
			}

			$sheet2->setCellValue('A'.$RowS2,"2. ราคาสินค้า");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFont()->getColor()->setARGB('ff000000');
			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาตั้ง");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$DataSheet2 = (isset($RST2['P0'])) ? number_format($RST2['P0'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('C'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('C'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาปลีก");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$DataSheet2 = (isset($RST2['P1'])) ? number_format($RST2['P1'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('C'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาส่ง (SEMI)");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$DataSheet2 = (isset($RST2['P2'])) ? number_format($RST2['P2'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('C'.$RowS2.':D'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			
			if($chk_uClass == "Y") {
				$sheet2->setCellValue('E'.$RowS2,"GP");
				$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$DataSheet2 = "0.00%";
				if(isset($RST2['P2'])) {
					if($RST2['P2'] != 0)  { 
						$DataSheet2 = number_format(((($RST2['P2']-$LastPurPrc)/$RST2['P2'])*100),2)."%";
					}
				}
				$sheet2->setCellValue('F'.$RowS2,$DataSheet2);
				$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}else{
				$sheet2->setCellValue('E'.$RowS2,"");
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาส่ง (S1)");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$DataSheet2 = (isset($RST2['S1P'])) ? number_format($RST2['S1P'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"จำนวน");
			$sheet2->getStyle('C'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$DataSheet2 = (isset($RST2['S1Q'])) ? number_format($RST2['S1Q'],0) : '0';
			$sheet2->setCellValue('D'.$RowS2,$DataSheet2." ตัว");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			if($chk_uClass == "Y") {
				$sheet2->setCellValue('E'.$RowS2,"GP");
				$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$DataSheet2 = "0.00%";
				if(isset($RST2['S1P'])) {
					if($RST2['S1P'] != 0)  { 
						$DataSheet2 = number_format(((($RST2['S1P']-$LastPurPrc)/$RST2['S1P'])*100),2)."%";
					}
				}
				$sheet2->setCellValue('F'.$RowS2,$DataSheet2);
				$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}else{
				$sheet2->setCellValue('E'.$RowS2,"");
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาส่ง (S2)");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$DataSheet2 = (isset($RST2['S2P'])) ? number_format($RST2['S2P'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"จำนวน");
			$sheet2->getStyle('C'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$DataSheet2 = (isset($RST2['S2Q'])) ? number_format($RST2['S2Q'],0) : '0';
			$sheet2->setCellValue('D'.$RowS2,$DataSheet2." ตัว");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			if($chk_uClass == "Y") {
				$sheet2->setCellValue('E'.$RowS2,"GP");
				$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$DataSheet2 = "0.00%";
				if(isset($RST2['S2P'])) {
					if($RST2['S2P'] != 0)  { 
						$DataSheet2 = number_format(((($RST2['S2P']-$LastPurPrc)/$RST2['S2P'])*100),2)."%";
					}
				}
				$sheet2->setCellValue('F'.$RowS2,$DataSheet2);
				$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}else{
				$sheet2->setCellValue('E'.$RowS2,"");
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาส่ง (S3)");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$DataSheet2 = (isset($RST2['S3P'])) ? number_format($RST2['S3P'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"จำนวน");
			$sheet2->getStyle('C'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$DataSheet2 = (isset($RST2['S3Q'])) ? number_format($RST2['S3Q'],0) : '0';
			$sheet2->setCellValue('D'.$RowS2,$DataSheet2." ตัว");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			if($chk_uClass == "Y") {
				$sheet2->setCellValue('E'.$RowS2,"GP");
				$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$DataSheet2 = "0.00%";
				if(isset($RST2['S3P'])) {
					if($RST2['S3P'] != 0)  { 
						$DataSheet2 = number_format(((($RST2['S3P']-$LastPurPrc)/$RST2['S3P'])*100),2)."%";
					}
				}
				$sheet2->setCellValue('F'.$RowS2,$DataSheet2);
				$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}else{
				$sheet2->setCellValue('E'.$RowS2,"");
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคา (ผจก)");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$DataSheet2 = (isset($RST2['MgrPrice'])) ? number_format($RST2['MgrPrice'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('C'.$RowS2.':D'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			
			if($chk_uClass == "Y") {
				$sheet2->setCellValue('E'.$RowS2,"GP");
				$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$DataSheet2 = "0.00%";
				if(isset($RST2['MgrPrice'])) {
					if($RST2['MgrPrice'] != 0)  { 
						$DataSheet2 = number_format(((($RST2['MgrPrice']-$LastPurPrc)/$RST2['MgrPrice'])*100),2)."%";
					}
				}
				$sheet2->setCellValue('F'.$RowS2,$DataSheet2);
				$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}else{
				$sheet2->setCellValue('E'.$RowS2,"");
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาปลีก MT");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$DataSheet2 = (isset($RST2['MTPrice'])) ? number_format($RST2['MTPrice'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('C'.$RowS2.':D'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			
			if($chk_uClass == "Y") {
				$sheet2->setCellValue('E'.$RowS2,"GP");
				$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$DataSheet2 = "0.00%";
				if(isset($RST2['MTPrice'])) {
					if($RST2['MTPrice'] != 0)  { 
						$DataSheet2 = number_format(((($RST2['MTPrice']-$LastPurPrc)/$RST2['MTPrice'])*100),2)."%";
					}
				}
				$sheet2->setCellValue('F'.$RowS2,$DataSheet2);
				$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}else{
				$sheet2->setCellValue('E'.$RowS2,"");
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาส่ง MT");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$DataSheet2 = (isset($RST2['MTPrice2'])) ? number_format($RST2['MTPrice2'],2) : '0.00';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('C'.$RowS2.':D'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			
			if($chk_uClass == "Y") {
				$sheet2->setCellValue('E'.$RowS2,"GP");
				$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$DataSheet2 = "0.00%";
				if(isset($RST2['MTPrice2'])) {
					if($RST2['MTPrice2'] != 0)  { 
						$DataSheet2 = number_format(((($RST2['MTPrice2']-$LastPurPrc)/$RST2['MTPrice2'])*100),2)."%";
					}
				}
				$sheet2->setCellValue('F'.$RowS2,$DataSheet2);
				$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}else{
				$sheet2->setCellValue('E'.$RowS2,"");
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('E'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			}

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ต้นทุนปัจจุบัน ".number_format($LastPurPrc,2)." บาท");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':C'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,"ต้นทุก Lot ที่ผ่านมา ".number_format($RST3['LastPurPrc'],2)." บาท");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('D'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$RowS2++;

		// 3. โปรโมชั่น
			$SQL5 = "
				SELECT T0.TeamCode,T1.ItemCode,
					SUM(IFNULL(T1.Tar_M01,0)+
					IFNULL(T1.Tar_M02,0)+
					IFNULL(T1.Tar_M03,0)+
					IFNULL(T1.Tar_M04,0)+
					IFNULL(T1.Tar_M05,0)+
					IFNULL(T1.Tar_M06,0)+
					IFNULL(T1.Tar_M07,0)+
					IFNULL(T1.Tar_M08,0)+
					IFNULL(T1.Tar_M09,0)+
					IFNULL(T1.Tar_M10,0)+
					IFNULL(T1.Tar_M11,0)+
					IFNULL(T1.Tar_M12,0)) as Target 
				FROM tarsku_header T0
				LEFT JOIN tarsku_detail T1 ON T0.CPEntry = T1.CPEntry
				WHERE (DATE(NOW()) BETWEEN T0.StartDate AND T0.EndDate) AND T0.CANCELED = 'N' AND T1.TargetStatus = 'A'  AND ItemCode = '$ItemCode'
				GROUP BY T0.TeamCode,T1.ItemCode";
			$QRY5 = MySQLSelectX($SQL5);
			$MT1 = 0; $MT2 = 0; $TT2 = 0; $OULTT1 = 0; $ONL = 0;
			while($RST5 = mysqli_fetch_array($QRY5)) {
				switch($RST5['TeamCode']) {
					case 'MT1': $MT1 = $MT1+$RST5['Target']; break;
					case 'MT2': $MT2 = $MT2+$RST5['Target']; break;
					case 'TT2': $TT2 = $TT2+$RST5['Target']; break;
					case 'OUL': 
					case 'TT1': $OULTT1 = $OULTT1+$RST5['Target']; break;
					case 'ONL': $ONL = $ONL+$RST5['Target']; break;
				}
			}
			$SQL6 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '9'";
			$QRY6 = MySQLSelectX($SQL6);

			$sheet2->setCellValue('A'.$RowS2,"3. โปรโมชั่น");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFont()->getColor()->setARGB('ff000000');
			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาปลีก");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$DataSheet2 = (isset($RST2['P1'])) ? number_format($RST2['P1'],2) : '-';
			$sheet2->setCellValue('B'.$RowS2,$DataSheet2." บาท");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('C'.$RowS2,"จำนวน");
			$sheet2->getStyle('C'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('D'.$RowS2,"1 ตัว");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('E'.$RowS2,"GP");
			$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$DataSheet2 = "0.00";
			if(isset($RST2['P1'])) {
				$DataSheet2 = ($RST2['P1'] != 0) ? number_format(((($RST2['P1']-$LastPurPrc)/$RST2['P1'])*100),2) : '0.00';
			}
			$sheet2->setCellValue('F'.$RowS2,$DataSheet2."%");
			$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาพิเศษ 1");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('B'.$RowS2,$S1P." บาท");
			$sheet2->getStyle('B'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('C'.$RowS2,"จำนวน");
			$sheet2->getStyle('C'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('D'.$RowS2,$S1Q." ตัว");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('E'.$RowS2,"GP");
			$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$DataSheet2 = ($S1P > 0) ? number_format(((($S1P-$LastPurPrc)/$S1P)*100),2) : '0.00';
			$sheet2->setCellValue('F'.$RowS2,$DataSheet2."%");
			$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ราคาพิเศษ 2");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('B'.$RowS2,$S2P." บาท");
			$sheet2->getStyle('B'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('C'.$RowS2,"จำนวน");
			$sheet2->getStyle('C'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('D'.$RowS2,$S2Q." ตัว");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('E'.$RowS2,"GP");
			$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$DataSheet2 = ($S2P > 0) ? number_format(((($S2P-$LastPurPrc)/$S2P)*100),2) : '0.00';
			$sheet2->setCellValue('F'.$RowS2,$DataSheet2."%");
			$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ระยะเวลา");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('B'.$RowS2,$StartEndDate);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"เป้าต่อเดือน");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"MT1");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBoldRight);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,$MT1." ตัว");
			$sheet2->getStyle('B'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"MT2");
			$sheet2->getStyle('C'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,$MT2." ตัว");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$sheet2->setCellValue('E'.$RowS2,"TT2");
			$sheet2->getStyle('E'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('E'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('F'.$RowS2,$TT2." ตัว");
			$sheet2->getStyle('F'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('F'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$RowS2++;

			$sheet2->setCellValue('A'.$RowS2,"ONL");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBoldRight);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('B'.$RowS2,$ONL." ตัว");
			$sheet2->getStyle('B'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('C'.$RowS2,"OUL/TT1");
			$sheet2->getStyle('C'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('C'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

			$sheet2->setCellValue('D'.$RowS2,$OULTT1." ตัว");
			$sheet2->getStyle('D'.$RowS2)->applyFromArray($TextLeft);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('D'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('D'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
			$RowS2++;

			while($RST6 = mysqli_fetch_array($QRY6)) {
				$Detail = "-";
				if($RST6['Detail'] != "" || $RST6['Detail'] != null) {
					$Detail = $RST6['Detail'];
				}

				$sheet2->setCellValue('A'.$RowS2,$RST6['Header']);
				$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$sheet2->setCellValue('B'.$RowS2,$Detail);
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			
				$RowS2++;
			}

			$sheet2->setCellValue('A'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$RowS2++;

		// 4. ช่องทางการขายสินค้า
			$SQL7 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '10'";
			$QRY7 = MySQLSelectX($SQL7); 

			$sheet2->setCellValue('A'.$RowS2,"4. ช่องทางการขายสินค้า");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFont()->getColor()->setARGB('ff000000');
			$RowS2++;

			$tmpHeader = ""; $Cell = ['0', 'A', 'B', 'C', 'D', 'E', 'F']; $c = 0;
			while($RST7 = mysqli_fetch_array($QRY7)) {
				$checked = ($RST7['CheckBox'] == 'Y') ? "   [/]   " : "   [  ]   ";
				$Remark = ($RST7['Detail'] == 'อื่นๆ') ? "ระบุ ".$RST7['Remark'] : "";
				if($tmpHeader != $RST7['Header']) {
					if($Cell[$c] != '0') {
						$spreadsheet->setActiveSheetIndex(1)->mergeCells($Cell[$c].$RowS2.':F'.$RowS2);
						$RowS2++;
						$c = 0;
					}
					$tmpHeader = $RST7['Header'];
					$sheet2->setCellValue('A'.$RowS2,$RST7['Header']);
					$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
					$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
					$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
					$RowS2++;
				}

				$c++;
				$sheet2->setCellValue($Cell[$c].$RowS2,$RST7['Detail']);
				if($Cell[$c] == "A") {
					$sheet2->getStyle($Cell[$c].$RowS2)->applyFromArray($TextBoldRight);
				}else{
					$sheet2->getStyle($Cell[$c].$RowS2)->applyFromArray($TextBold);
				}
				$spreadsheet->setActiveSheetIndex(1)->getStyle($Cell[$c].$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				
				$c++;

				$sheet2->setCellValue($Cell[$c].$RowS2,$checked." ".$Remark);
				$sheet2->getStyle($Cell[$c].$RowS2)->applyFromArray($TextLeft);
				$spreadsheet->setActiveSheetIndex(1)->getStyle($Cell[$c].$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');

				if($Cell[$c] == 'F') {
					$RowS2++;
					$c = 0;
				}
			}

			if($Cell[$c] != '0') {
				$spreadsheet->setActiveSheetIndex(1)->mergeCells($Cell[$c].$RowS2.':F'.$RowS2);
				$RowS2++;
			}

			$sheet2->setCellValue('A'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$RowS2++;

		// 5. VDO Utility
			$SQL8 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '11'";
			$QRY8 = MySQLSelectX($SQL8);

			$sheet2->setCellValue('A'.$RowS2,"5. VDO Utility");
			$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFont()->getColor()->setARGB('ff000000');
			$RowS2++;

			$r = 0;
			while($RST8 = mysqli_fetch_array($QRY8)) {
				$r++;

				$sheet2->setCellValue('A'.$RowS2,$r);
				$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$RowS2++;

				$sheet2->setCellValue('B'.$RowS2,$RST8['Header']." | ".$RST8['Detail']);
				$sheet2->getStyle('B'.$RowS2)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('B'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('B'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$RowS2++;
			}
			if($r == 0) {
				$sheet2->setCellValue('A'.$RowS2,"ไม่มีข้อมูล");
				$sheet2->getStyle('A'.$RowS2)->applyFromArray($TextCenter);
				$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$RowS2++;
			}

			$sheet2->setCellValue('A'.$RowS2,"");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('A'.$RowS2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			$spreadsheet->setActiveSheetIndex(1)->mergeCells('A'.$RowS2.':F'.$RowS2);
			$RowS2++;

			for($cl = 2; $cl <= $RowS2; $cl++) {
				$spreadsheet->setActiveSheetIndex(1)->getRowDimension($cl)->setRowHeight(18);
			}

		// Image
			$SQL = "SELECT * FROM skubook_attach WHERE ItemCode = '$ItemCode' AND FileStatus = 'A' AND Type IN (6,7,8)";
			$QRY = MySQLSelectX($SQL);
			while($RST = mysqli_fetch_array($QRY)) {
				$Img[$RST['Type']][$RST['VisOrder']] = $RST['FileDirName'].'.'.$RST['FileExt'];
			}
			$NameImg[6] = "รูปสินค้าตัวจริง";
			$NameImg[7] = "รูปแพ็คเกจตัวจริง";
			$NameImg[8] = "รูปอุปกรณ์ภายในกล่องตัวจริง";
			$NameImg[9] = "ใบโปร/ใบขาย";
			$n = 2;
			$sheet2->setCellValue('G1',"");
			$spreadsheet->setActiveSheetIndex(1)->getStyle('G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
			for($i = 6; $i <= 9; $i++) {
				$sheet2->setCellValue('G'.$n,$NameImg[$i]);
				$sheet2->getStyle('G'.$n)->applyFromArray($TextBoldCenter);
				$spreadsheet->setActiveSheetIndex(1)->getStyle('G'.$n)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				$n++;

				if($i == 6) {
					if(isset($Img[$i][0])){
						$IndexIMG = ['G', 'H', 'I', 'J', 'K', 'L', 'M'];
						for($j = 0; $j <= count($Img[$i])-1; $j++) {
							$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
							$drawing->setName($NameImg[$i]." ".($j+1));
							$drawing->setDescription($NameImg[$i]." ".($j+1));
							$drawing->setPath("../../../../image/products/".$ItemCode."/".$i."/".$Img[$i][$j]);
							$drawing->setHeight(150); 
							$drawing->setCoordinates($IndexIMG[$j].$n);
							$drawing->setOffsetX(10);
							$drawing->setOffsetY(10);
							$drawing->setRotation(0);
							$drawing->setWorksheet($spreadsheet->setActiveSheetIndex(1));
						}
					}else{
						$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
						$drawing->setName($NameImg[$i]);
						$drawing->setDescription($NameImg[$i]);
						$drawing->setPath("../../../../image/products/no-image.jpg");
						$drawing->setHeight(150); 
						$drawing->setCoordinates('G'.$n);
						$drawing->setOffsetX(100);
						$drawing->setRotation(0);
						$drawing->setWorksheet($spreadsheet->setActiveSheetIndex(1));
					}
				}else{
					if(isset($Img[$i][0])){
						$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
						$drawing->setName($NameImg[$i]);
						$drawing->setDescription($NameImg[$i]);
						$drawing->setPath("../../../../image/products/".$ItemCode."/".$i."/".$Img[$i][0]);
						$drawing->setHeight(150); 
						$drawing->setCoordinates('G'.$n);
						$drawing->setOffsetX(10);
						$drawing->setOffsetY(10);
						$drawing->setRotation(0);
						$drawing->setWorksheet($spreadsheet->setActiveSheetIndex(1));
					}else{
						$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
						$drawing->setName($NameImg[$i]);
						$drawing->setDescription($NameImg[$i]);
						$drawing->setPath("../../../../image/products/no-image.jpg");
						$drawing->setHeight(150); 
						$drawing->setCoordinates('G'.$n);
						$drawing->setOffsetX(100);
						$drawing->setRotation(0);
						$drawing->setWorksheet($spreadsheet->setActiveSheetIndex(1));
					}
				}

				for($m = $n; $m <= $n+9; $m++) {
					$sheet2->setCellValue('G'.$m,"");
					$spreadsheet->setActiveSheetIndex(1)->getStyle('G'.$m)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffffff');
				}

				$n = $n+9;
			}
			

	// #Set Width and Height Sheet 1 PD
	$spreadsheet->setActiveSheetIndex(0)->getDefaultColumnDimension()->setWidth(16);
	$spreadsheet->setActiveSheetIndex(0)->getRowDimension('1')->setRowHeight(22);

	// #Set Width and Height Sheet 2 MK
	$spreadsheet->setActiveSheetIndex(1)->getDefaultColumnDimension()->setWidth(16);
	$spreadsheet->setActiveSheetIndex(1)->getRowDimension('1')->setRowHeight(22);

	// #Set Width and Height Sheet 3 PU
	$spreadsheet->setActiveSheetIndex(2)->getDefaultColumnDimension()->setWidth(16);
	$spreadsheet->setActiveSheetIndex(2)->getRowDimension('1')->setRowHeight(22);

	// #Set Width and Height Sheet 4 ฝ่ายขาย
	$spreadsheet->setActiveSheetIndex(3)->getDefaultColumnDimension()->setWidth(16);
	$spreadsheet->setActiveSheetIndex(3)->getRowDimension('1')->setRowHeight(22);

	// #Set Width and Height Sheet 5 สินค้าใหม่
	$spreadsheet->setActiveSheetIndex(4)->getDefaultColumnDimension()->setWidth(16);
	$spreadsheet->setActiveSheetIndex(4)->getRowDimension('1')->setRowHeight(22);

	$spreadsheet->setActiveSheetIndex(0);
	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงาน SKU BOOK - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/SKUBook/".$FileName);
	// $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'SKUBook', logFile = '$FileName', DateCreate = NOW()";
	// MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
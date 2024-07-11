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

function numTocha($number){ 
	$txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ'); 
	$txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'); 
	$number = str_replace(",","",$number); 
	$number = str_replace(" ","",$number); 
	$number = str_replace("บาท","",$number); 
	$number = explode(".",$number); 
	if (sizeof($number)>2){ 
		return 'ทศนิยมหลายตัวนะจ๊ะ'; 
		exit; 
	} 
	$strlen = strlen($number[0]); 
	$convert = ''; 
	for ($i=0;$i<$strlen;$i++){ 
		$n = substr($number[0], $i,1); 
		if ($n!=0){ 
			if ($i==($strlen-1) AND $n==1){
			$convert .= 'เอ็ด';
			}elseif ($i==($strlen-2) AND $n==2){
			$convert .= 'ยี่';
			}elseif($i==($strlen-2) AND $n==1){
			$convert .= ''; 
			}else{
			$convert .= $txtnum1[$n];
			} 
			$convert .= $txtnum2[$strlen-$i-1]; 
		} 
	} 
	$convert .= 'บาท'; 
	if ($number[1]=='0' OR $number[1]=='00' OR $number[1]==''){ 
		$convert .= 'ถ้วน'; 
	}else{ 
		$strlen = strlen($number[1]); 
		for ($i=0;$i<$strlen;$i++){ 
		$n = substr($number[1], $i,1); 
		if ($n!=0) { 
			if ($i==($strlen-1) AND $n==1){
			$convert .= 'เอ็ด';
			}elseif ($i==($strlen-2) AND $n==2){
			$convert .= 'ยี่';
			}elseif ($i==($strlen-2) AND $n==1){
			$convert .= '';
			}else{ 
			$convert .= $txtnum1[$n];
			} 
			$convert .= $txtnum2[$strlen-$i-1]; 
		}  
		} 
		$convert .= 'สตางค์'; 
	} 
	return $convert; 
} 

if($_GET['a'] == 'CallData') {
	if((substr($_POST['CardCode'],0,3) == 'BI-')) {
		$sql = "SELECT TransID, CreateDate, CardCode, DocNum, CreateUkey FROM pita_billing WHERE DocNum = '".$_POST['CardCode']."' AND DocStatus = 'A'";
		$ChkR = CHKRowDB($sql);
		if($ChkR != 0) {
			$sqlQRY = MySQLSelectX($sql);
			$r = 0;
			$trnList = "('";
			while($result = mysqli_fetch_array($sqlQRY)) {
				$r++;
				if($r == 1) {
					$FindData = $result['CardCode'];
					$DocNum = $result['DocNum'];
					$DateCreate = date("d/m/Y",strtotime($result['CreateDate']));
					$qry = "SELECT CONCAT(uName,' ',uLastName) AS Creater FROM users WHERE uKey = '".$result['CreateUkey']."'";
					$resultCreat = MySQLSelect($qry);
					$Creater = $resultCreat['Creater'];
				}
				$trnList .= $result['TransID']."','";
			}
			$trnList = substr($trnList,0,-2).")";
			$CalBi = 1;
			$arrCol['alert'] = 0;
			$alert = 0;
		}else{
			$arrCol['alert'] = 1;
			$alert = 1;
		}
	}else{
		$FindData = $_POST['CardCode'];
		$DocYear  = substr(date("Y")+543,2);
		$DocMonth = date("m");
		$Prefix   = "BI-".$DocYear.$DocMonth;
		$sql = "SELECT DISTINCT DocNum FROM pita_billing WHERE DocNum LIKE '$Prefix%' ORDER BY DocNum DESC";
		$chkR = ChkRowDB($sql);
		if($chkR == 0) {
			$DocNum = $Prefix."001";
		} else {
			$rst = MySQLSelect($sql);
			$Subfix = intval(substr($rst['DocNum'],7))+1;

			if($Subfix <= 9) {
				$DocNum = $Prefix."00".$Subfix;
			} elseif($Subfix <= 99) {
				$DocNum = $Prefix."0".$Subfix;
			} else {
				$DocNum = $Prefix.$Subfix;
			}
		}
		$CalBi = 0;
		$DateCreate = date("d/m/Y");
		$Creater = $_SESSION['uName']." ".$_SESSION['uLastName'];
		$arrCol['alert'] = 0;
		$alert = 0;
	}

	
	$sql = "SELECT T0.CardCode, T0.FatherCard FROM OCRD T0 WHERE T0.CardCode = '".$FindData."'";
	$sqlQRY = PITASelect($sql);
	$resultSAP = odbc_fetch_array($sqlQRY);
	if(isset($resultSAP['CardCode'])) {
		if ($resultSAP['FatherCard'] != null){
			$CardCode = $resultSAP['FatherCard'];
		}else{
			$CardCode = $resultSAP['CardCode'];
		}
		$ChkData = "Y";
	}else{
		$ChkData = "N";
	}


	if($ChkData == "Y") {
		$sql = "SELECT OCRD.[CardCode],OCRD.[CardName],OCRD.[LicTradNum],
					CRD1.[Street],CRD1.[Block],CRD1.[City],
					OCRD.[Phone1],OCRD.[Phone2],OCRD.[Cellular],OCRD.[Fax],
					OCTG.[PymntGroup],OCRD.[U_ChqCond],CRD1.[ZipCode]
				FROM OCRD
				LEFT JOIN CRD1 ON OCRD.[CardCode] = CRD1.[CardCode]
				LEFT JOIN OCTG ON OCRD.[GroupNum] = OCTG.[GroupNum]
				WHERE OCRD.[CardCode] = '".$CardCode."' AND (CRD1.[AdresType] = 'B') AND CRD1.[Street] IS NOT NULL";
		$sqlQRY = PITASelect($sql);
		$resultSAP2 = odbc_fetch_array($sqlQRY);

		$arrCol['DocNum'] = $DocNum;
		$arrCol['Creater'] = $Creater;
		$arrCol['CardCode'] = $resultSAP2['CardCode'];

		$CardCodeSAP2 = $resultSAP2['CardCode'];
		$CardName     = conutf8($resultSAP2['CardName']);
		$Address1     = conutf8($resultSAP2['Street']);
		$Address2     = conutf8($resultSAP2['Block'])." ".conutf8($resultSAP2['City'])." ".$resultSAP2['ZipCode'];
		if($resultSAP2['Phone1'] != "" && $resultSAP2['Phone2'] != "") {
			$Phone = conutf8($resultSAP2['Phone1']).", ".conutf8($resultSAP2['Phone2']);
		}else{
			$Phone = conutf8($resultSAP2['Phone1']);
		}
		$Fax          =  conutf8($resultSAP2['Fax']);
		$taxID        = $resultSAP2['LicTradNum'];
		$TermCR       = "เครดิต ".conutf8($resultSAP2['PymntGroup']);

		$CusChq = conutf8($resultSAP2['U_ChqCond']);

		// ADD DATA TABLE->TBODY 
		$Header = [ '0', 
					'รหัสลูกค้า',       'เลขที่ใบวางบิล',
					'ชื่อลูกค้า',        'วันที่/Date',
					'ที่อยู่/Address', 'เงื่อนไขการชำระเงิน',
					'',              'เลขประจำตัวผู้เสียภาษี',
					'โทรศัพท์',       'Fax.',
				]; 
		$Body = [	0,
					$CardCodeSAP2, $DocNum,
					$CardName,     $DateCreate,
					$Address1,     $TermCR,
					$Address2,     $taxID,
					$Phone,        $Fax 
				];
		$H = 0;
		$Tbody = "";
		for($i = 1; $i <= 5; $i++) {
			$H++;
			if($i != 5) {
				$Tbody .= "<tr>";
					$Tbody .= "<td width='20%' class='fw-bolder'>".$Header[$H]."</td>";
					$Tbody .= "<td>".$Body[$H]."</td>";
				$H++;
					$Tbody .= "<td width='20%' class='fw-bolder'>".$Header[$H]."</td>";
					$Tbody .= "<td>".$Body[$H]."</td>";
				$Tbody .= "</tr>";
			}else{
				$Tbody .= "<tr>";
					$Tbody .= "<td width='20%' class='fw-bolder'>".$Header[$H]."</td>";
					$Tbody .= "<td>".$Body[$H]."</td>";
				$H++;
					$Tbody .= "<td width='20%' class='fw-bolder'>".$Header[$H]."</td>";
					$Tbody .= "<td>".$Body[$H]."</td>";
				$Tbody .= "</tr>";
			}
		}
		$arrCol['Tbody'] = $Tbody;

		
		if($alert == 0) {
			if($CalBi == 0) {
				$ChkDis = "";
				$sql1 ="SELECT P0.*
						FROM (SELECT T0.[TransID],T0.[RefDate],T0.[Line_ID],
									CASE WHEN (T0.[TransType] = '13') THEN T0.[Ref2]
										WHEN (T0.[TransType] = '14') THEN (SELECT ORIN.[NumAtCard] FROM ORIN WHERE ORIN.[DocNum] = T0.[BaseRef])
										WHEN (T0.[TransType] = '30') THEN T0.BaseRef
									ELSE T0.[Ref3Line] END AS 'RefNo',
									T0.[DueDate], (T0.[Debit]-T0.[Credit]) AS 'Balanced',T0.[BaseRef] AS 'Remark',
									(T0.[BalScDeb]-T0.[BalScCred]) AS 'ToPaid'
							FROM JDT1 T0
							WHERE T0.[ShortName] = '".$CardCode."' AND (T0.[TransType] = '13' OR T0.[TransType] = '14' OR T0.[TransType] = '30') AND ((T0.[BalScDeb]-T0.[BalScCred]) != 0 OR T0.[MthDate] IS NULL) 
						) P0
						ORDER BY P0.RefDate ASC,P0.RefNo";
						//echo $sql1;
			}else{
				$ChkDis = "checked disabled";
				$sql1 ="SELECT P0.*
						FROM (SELECT T0.[TransID],T0.[RefDate],T0.[Line_ID],
									CASE WHEN (T0.[TransType] = '13') THEN T0.[Ref2]
										WHEN (T0.[TransType] = '14') THEN (SELECT ORIN.[NumAtCard] FROM ORIN WHERE ORIN.[DocNum] = T0.[BaseRef])
										WHEN (T0.[TransType] = '30') THEN T0.BaseRef
									ELSE T0.[Ref3Line] END AS 'RefNo',
									T0.[DueDate], (T0.[Debit]-T0.[Credit]) AS 'Balanced',T0.[BaseRef] AS 'Remark',
									(T0.[BalScDeb]-T0.[BalScCred]) AS 'ToPaid'
							FROM JDT1 T0
							WHERE T0.[ShortName] = '".$CardCode."' AND (T0.[TransType] = '13' OR T0.[TransType] = '14' OR T0.[TransType] = '30') AND ((T0.[BalScDeb]-T0.[BalScCred]) != 0 OR T0.[MthDate] IS NULL) 
									AND T0.[TransID] IN ".$trnList." ) P0
						ORDER BY P0.RefDate ASC,P0.RefNo";
			}

			$sqlQRY = PITASelect($sql1);
			$x = 0;
			$billTotalAmont = 0;
			while($resultBill = odbc_fetch_array($sqlQRY)) {
				$x++;
				$billTransID[$x] = $resultBill['TransID'];
				$billLineID[$x]  = $resultBill['Line_ID'];
				$billRefDate[$x] = $resultBill['RefDate'];
				$billRefNo[$x]   = $resultBill['RefNo'];
				$billDueDate[$x] = $resultBill['DueDate'];
				$billAmount[$x]  = $resultBill['Balanced'];
				$billPaid[$x]    = $resultBill['ToPaid'];
				$billRemark[$x]  = conutf8($resultBill['Remark']);
				$billTotalAmont  = $billTotalAmont + $billPaid[$x];
			}

			$output = "";
			for($i = 1; $i <= $x; $i++) {
				$Chk = CHKRowDB("SELECT * FROM pita_billing WHERE TransID = '".$billTransID[$i]."' AND DocStatus = 'A'");
				if($Chk == 0) {
					$rowClass = "fw-bold";
					$CallBIList = "class='fw-bold'";
				}else{
					$rowClass = "fw-bold text-success";
					$CallBIList = "class='' style='cursor: pointer;' onclick=\"CallBI('".$billTransID[$i]."','".$billLineID[$i]."')\"";
				}

				$output .= "<tr class='$rowClass'>
								<td class='text-center'>$i</td>
								<td class='text-center'>".date('d-m-Y',strtotime($billRefDate[$i]))."</td>
								<td class='text-center'><span $CallBIList>".$billRefNo[$i]."</span></td>
								<td class='text-center'>".date('d-m-Y',strtotime($billDueDate[$i]))."</td>
								<td class='text-right'>".number_format($billAmount[$i],2)."</td>
								<td class='text-right'>".number_format($billPaid[$i],2)."</td>
								<td class='text-center'>".$billRemark[$i]."</td>
								<td class='text-center'>
									<input type='checkbox' class='form-check-input' name='chkid_".$billTransID[$i]."_".$billLineID[$i]."' id='chkid_".$billTransID[$i]."_".$billLineID[$i]."' onclick=\"chkRow('".$billTransID[$i]."','".$billRemark[$i]."','".$billLineID[$i]."')\" $ChkDis>
								</td>
							</tr>";
			}
			$sum = $i-1;
			$output .= "<tr class='fw-bolder' style='font-size: 12.8px;'>
							<td class='text-center' colspan='2'>รวม $sum ฉบับ</td>
							<td class='text-center' colspan='4'>รวมเงิน (GRAND TOTAL) : <span class='text-primary'>".number_format($billTotalAmont,2)."</span> <span class='text-primary fst-italic' style='font-size: 14px;'>*** ".numTocha(number_format($billTotalAmont,2))." ***</span></td>
							<td class='text-center' colspan='2'>กำหนดวางบิล-รับเช็ค: $CusChq</td>
						</tr>";

			$arrCol['output'] = $output;
			$arrCol['Print'] = $CalBi;
			$arrCol['ChkData'] = $ChkData;
		}
	}else{
		$arrCol['ChkData'] = $ChkData;
	}
}

if($_GET['a'] == 'AddData') {
	$ChkRow = CHKRowDB("SELECT * FROM pita_billing WHERE TransID = '".$_POST['TransID']."' AND LineNum = '".$_POST['LineNum']."' AND DocNum = '".$_POST['DocNum']."'");
	if($ChkRow == 0){
		if($_POST['Chk'] == 1) {
 			$sql1 = "SELECT DISTINCT T1.CardCode,T1.CardName 
					 FROM JDT1 T0
					  	  LEFT JOIN OINV T1 ON T0.Ref1 = T1.DocNum  
					 WHERE T0.TransID = ".$_POST['TransID']." AND T0.Account = '1130-01'";
			$sqlQRY = PITASelect($sql1);
			$resultBill = odbc_fetch_array($sqlQRY);


			$INSERT = "INSERT INTO pita_billing 
					   SET CreateDate = NOW(), CardCode = '".$resultBill['CardCode']."', DocNum = '".$_POST['DocNum']."', IVRemark = '".$_POST['IVRemark']."',
					       TransID = '".$_POST['TransID']."', LineNum = '".$_POST['LineNum']."', CreateUkey = '".$_SESSION['ukey']."'";
			
			MySQLInsert($INSERT);
		}
	}else{
		if($_POST['Chk'] == 0) {
			$DELETE = "DELETE FROM pita_billing WHERE TransID = '".$_POST['TransID']."' AND DocNum = '".$_POST['DocNum']."' AND LineNum = '".$_POST['LineNum']."'";
			MySQLDelete($DELETE);
		}
	}
}

if($_GET['a'] == 'CallBI') {
	$sql = "SELECT DocNum FROM pita_billing WHERE DocStatus = 'A' AND TransID = '".$_POST['TransID']."' AND LineNum = '".$_POST['LineNum']."'";
	$result = MySQLSelect($sql);
	$arrCol['DocNum'] = $result['DocNum'];
}

if($_GET['a'] == 'Save') {
	$ChkRow = CHKRowDB("SELECT * FROM pita_billing WHERE DocNum = '".$_POST['DocNum']."'");
	if($ChkRow > 0) {
		$arrCol['DocNum'] = $_POST['DocNum'];
	}else{
		$arrCol['DocNum'] = "You Need Me";
	}
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
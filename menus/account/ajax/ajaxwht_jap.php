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

if($_GET['p'] == "add") {
	$ConF   = $_POST['ConF'];

	$BookNo        = $_POST['BookNo'];
	$TaxDate       = $_POST['TaxDate'];
	$VatMonth      = $_POST['VatMonth'];
	$AddTax        = $_POST['AddTax'];
	$DocNum        = $_POST['DocNum'];
	$DocDate       = $_POST['DocDate'];
	$Department    = $_POST['Department'];
	$CardCode      = $_POST['CardCode'];
	$CardName      = $_POST['CardName'];
	$NamePrefix    = $_POST['Prefix'];
	$Address       = $_POST['Address'];
	$TaxID         = $_POST['TaxID'];
	$BranchID      = $_POST['BranchID'];

	$PayType1      = $_POST['PayType1'];
	$PayType160    = $_POST['PayType160'];
	$PayTotal1     = $_POST['PayTotal1'];
	$TaxRate1      = $_POST['TaxRate1'];
	$DocTotal1     = $_POST['DocTotal1'];
	$VatTotal1     = $_POST['VatTotal1'];
	if(isset($_POST['PayType2'])) {
		$PayType2      = $_POST['PayType2'];
	} else {
		$PayType2      = "";
	}
	if(isset($_POST['PayType260'])) {
		$PayType260    = $_POST['PayType260'];
	} else {
		$PayType260 = "";
	}
	
	$PayTotal2     = $_POST['PayTotal2'];
	$TaxRate2      = $_POST['TaxRate2'];
	$DocTotal2     = $_POST['DocTotal2'];
	$VatTotal2     = $_POST['VatTotal2'];

	$TaxCat        = $_POST['TaxCat'];
	$TaxGroup      = $_POST['TaxGroup'];
	$TaxType       = $_POST['TaxType'];

	if(isset($_POST['TaxType4'])) {
		$TaxTypeRemark = $_POST['TaxType4'];
	} else {
		$TaxTypeRemark = "";
	}
	

	$Ukey          = $_SESSION['ukey'];

	if(isset($_POST['RWID'])) {
		$ID        = $_POST['RWID'];
	}

	if($ConF == 0) {
		$sql1 = "SELECT T0.ID FROM wht_JAP T0 WHERE T0.BookNo = '$BookNo' AND T0.Status != 0";
		$Rows = ChkRowDB($sql1);
		if($Rows == 0) {
			$InsertSQL =
				"INSERT INTO wht_JAP SET
					BookNo = '$BookNo',
					TaxDate = '$TaxDate',
					VatMonth = '$VatMonth',
					AddTax = '$AddTax',
					DocNum = '$DocNum',
					DocDate = '$DocDate',
					Department = '$Department',
					CardCode = '$CardCode',
					CardName = '$CardName',
					NamePrefix = '$NamePrefix',
					Address = '$Address',
					TaxID = '$TaxID',
					BranchID = '$BranchID',
					PayType1 = '$PayType1',
					PayType160 = '$PayType160',
					PayTotal1 = '$PayTotal1',
					TaxRate1 = '$TaxRate1',
					DocTotal1 = '$DocTotal1',
					VatTotal1 = '$VatTotal1',
					PayType2 = '$PayType2',
					PayType260 = '$PayType260',
					PayTotal2 = '$PayTotal2',
					TaxRate2 = '$TaxRate2',
					DocTotal2 = '$DocTotal2',
					VatTotal2 = '$VatTotal2',
					TaxType = '$TaxType',
					TaxTypeRemark = '$TaxTypeRemark',
					TaxCat = '$TaxCat',
					TaxGroup = '$TaxGroup',
					UkeyCreate = '$Ukey',
					DateCreate = NOW(),
					Status = 1";
			$ID = MySQLInsert($InsertSQL);
			$arrCol['Status'] = "SUCCESS::INSERT";
		} else {
			$arrCol['Status'] = "ERR::DUPLICATE";
		}
	} else {
		$UpdateSQL =
			"UPDATE wht_JAP SET
				BookNo = '$BookNo',
				TaxDate = '$TaxDate',
				VatMonth = '$VatMonth',
				AddTax = '$AddTax',
				DocNum = '$DocNum',
				DocDate = '$DocDate',
				Department = '$Department',
				CardCode = '$CardCode',
				CardName = '$CardName',
				NamePrefix = '$NamePrefix',
				Address = '$Address',
				TaxID = '$TaxID',
				BranchID = '$BranchID',
				PayType1 = '$PayType1',
				PayType160 = '$PayType160',
				PayTotal1 = '$PayTotal1',
				TaxRate1 = '$TaxRate1',
				DocTotal1 = '$DocTotal1',
				VatTotal1 = '$VatTotal1',
				PayType2 = '$PayType2',
				PayType260 = '$PayType260',
				PayTotal2 = '$PayTotal2',
				TaxRate2 = '$TaxRate2',
				DocTotal2 = '$DocTotal2',
				VatTotal2 = '$VatTotal2',
				TaxType = '$TaxType',
				TaxTypeRemark = '$TaxTypeRemark',
				TaxCat = '$TaxCat',
				TaxGroup = '$TaxGroup',
				UkeyUpdate = '$Ukey',
				LastUpdate = NOW(),
				Status = 1
			WHERE ID = $ID";
		MySQLUpdate($UpdateSQL);
		$arrCol['Status'] = "SUCESS::UPDATE";
	}
}

if($_GET['p'] == "callno") {
    if ($_POST['TaxMonth'] == ''){
       
        $calYear = date("Y");
        if ($calYear < 2500){
            $calYear = $calYear +543;
        }
        $calYear = substr($calYear,-2);
        $vatMonth = date("m")."/".$calYear;
        
    }else{
        $vatMonth = $_POST['TaxMonth'];
    }
    switch($_POST['TaxType']) {
        case "P53":
        case "S53": $TaxType = "S53"; break;
        case "P03":
        case "S03": $TaxType = "S03"; break;
        case "P02":
        case "S02": $TaxType = "S02"; break;
    }
    $newNO = lastTXTNO($TaxType,$vatMonth);
	// echo $TaxType.",".$vatMonth."<br/>";

    $arrCol['bookno']   = $newNO;
    $arrCol['vatMonth'] = $vatMonth;

}

if($_GET['p'] == "all") {
	$i = 0;
	$no = 1;
	$VatMonth = $_POST['filt_BID'];
	switch($_POST['TaxCat']) {
		case "S02": $TaxCat = $_POST['TaxCat']; break;
		case "S03": $TaxCat = $_POST['TaxCat']; break;
		case "S53": $TaxCat = $_POST['TaxCat']; break;
		default:    $TaxCat = ""; break;
	}


	$sql1 = 
		"SELECT
			T0.ID, T0.NamePrefix, T0.CardCode, T0.CardName, T0.Address, T0.TaxID, T0.BranchID, T0.TaxDate,
			T0.PayType1, T0.PayType160, T0.TaxRate1, T0.DocTotal1, T0.VatTotal1, T0.TaxCat, T0.TaxType, T0.TaxTypeRemark
		FROM wht_JAP T0
		WHERE T0.Status = 1 AND T0.VatMonth = '$VatMonth' AND T0.TaxCat LIKE '$TaxCat%'";
	// echo $sql1;
	$qry1 = MySQLSelectX($sql1);

	while($result = mysqli_fetch_array($qry1)) {
		switch($result['PayType1']) {
			case "10":  $PayShow = "เงินเดือน"; break;
			case "11":  $PayShow = "ค่าจ้าง"; break;
			case "12":  $PayShow = "โบนัส"; break;
			case "21":  $PayShow = "ค่านายหน้า จ่ายบุคคลธรรมดา"; break;
			case "22":  $PayShow = "ค่านายหน้า จ่ายนิติบุคคล"; break;
			case "40":  $PayShow = "ค่าดอกเบี้ย"; break;
			case "413": $PayShow = "1.3 เงินปันผลกิจการ 20%"; break;
			case "422": $PayShow = "เงินส่วนแบ่งกำไร"; break;
			case "50":  $PayShow = "ค่าจ้างทำของ จ่ายบุคคลธรรมดา"; break;
			case "51":  $PayShow = "ค่าจ้างทำของ จ่ายนิติบุคคล"; break;
			case "52":  $PayShow = "ค่าจ้างโฆษณา"; break;
			case "53":  $PayShow = "ค่าเช่า"; break;
			case "60":  $PayShow = $result['PayType160']; break;
			default:    $PayShow = ""; break;
		}

		switch($result['TaxType']) {
			case "1": $TaxType = "ภาษีหัก ณ ที่จ่าย"; break;
			case "2": $TaxType = "ออกให้ตลอดไป"; break;
			case "3": $TaxType = "ออกให้ครั้งเดียว"; break;
			case "4": $TaxType = $result['TaxTypeRemark']; break;
		}

		if($result['TaxCat'] == "S03") {
			$TaxCat = "";
		} else {
			$TaxCat = $result['BranchID'];
		}

		$taxDate1 = date("d/m",strtotime($result['TaxDate']));
		$taxDate2 = date("Y",strtotime($result['TaxDate']));

		if($taxDate2 < 2500) {
			$taxDate2 = $taxDate2+543;
			$taxDate2 = substr($taxDate2,2);
		} else {
			$taxDate2 = date("y",strtotime($result['TaxDate']));
		}
		$taxDate = $taxDate1."/".$taxDate2;

		$arrCol[$i]['VisOrder']  = $no;
		$arrCol[$i]['CardName']  = $result['NamePrefix']." ".$result['CardName'];
		$arrCol[$i]['Address']   = $result['Address'];
		$arrCol[$i]['TaxID']     = $result['TaxID'];
		$arrCol[$i]['TaxCat']    = $TaxCat;
		$arrCol[$i]['taxDate']   = $taxDate;
		$arrCol[$i]['PayShow']   = $PayShow;
		$arrCol[$i]['TaxRate1']  = number_format($result['TaxRate1'],2);
		$arrCol[$i]['DocTotal1'] = number_format($result['DocTotal1'],2);
		$arrCol[$i]['VatTotal1'] = number_format($result['VatTotal1'],2);
		$arrCol[$i]['TaxType']   = $TaxType;
		$arrCol[$i]['ID']        = $result['ID'];

		$no++;
		$i++;
	}
	$arrCol['Rows'] = $i;

}

if($_GET['p'] == "modal") {
	$ID = $_POST['IDwd'];
	$sql1 = "SELECT T0.* FROM wht_JAP T0 WHERE ID = $ID LIMIT 1";
	$result = MySQLSelect($sql1);

	$arrCol['BookNo']     = $result['BookNo'];
	$arrCol['TaxDate']    = $result['TaxDate'];
	$arrCol['VatMonth']   = $result['VatMonth'];
	$arrCol['AddTax']     = $result['AddTax'];
	$arrCol['DocNum']     = $result['DocNum'];
	$arrCol['DocDate']    = $result['DocDate'];
	$arrCol['Department'] = $result['Department'];
	$arrCol['CardCode']   = $result['CardCode'];
	$arrCol['Prefix']     = $result['NamePrefix'];
	$arrCol['CardName']   = $result['CardName'];
	$arrCol['Address']    = $result['Address'];
	$arrCol['TaxID']      = $result['TaxID'];
	$arrCol['BranchID']   = $result['BranchID'];
	$arrCol['PayType1']   = $result['PayType1'];
	$arrCol['PayType160'] = $result['PayType160'];
	$arrCol['PayType2']   = $result['PayType2'];
	$arrCol['PayType260'] = $result['PayType260'];
	$arrCol['PayTotal1']  = $result['PayTotal1'];
	$arrCol['PayTotal2']  = $result['PayTotal2'];
	$arrCol['TaxRate1']   = $result['TaxRate1'];
	$arrCol['TaxRate2']   = $result['TaxRate2'];
	$arrCol['TaxType']    = $result['TaxType'];
	$arrCol['TaxType4']   = $result['TaxTypeRemark'];
	$arrCol['DocTotal1']  = $result['DocTotal1'];
	$arrCol['DocTotal2']  = $result['DocTotal2'];
	$arrCol['VatTotal1']  = $result['VatTotal1'];
	$arrCol['VatTotal2']  = $result['VatTotal2'];
	$arrCol['TaxCat']     = $result['TaxCat'];
	$arrCol['TaxGroup']   = $result['TaxGroup'];
}

if($_GET['p'] == "del") {
	$ID = $_POST['IDwd'];
	$UpdateSQL = "UPDATE wht_JAP SET Status = 0 WHERE ID = $ID";
	MySQLUpdate($UpdateSQL);
	$arrCol['Status'] = "SUCCESS";
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);

?>
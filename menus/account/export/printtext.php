<?php
session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');


$FileName =  str_replace("-","",str_replace("/","",$_GET['vm']))."-".date("YmdHi").".txt";
$myfile = fopen($FileName, "w") or die("Unable to open file!");
$VatMonth = substr($_GET['vm'],4);
$TaxCat = substr($_GET['vm'],0,3);

$sql1 = "SELECT T0.* FROM wht_JAP T0 WHERE T0.VatMonth = '$VatMonth' AND T0.TaxCat = '$TaxCat' AND T0.Status = 1 ORDER BY T0.BookNo, T0.TaxDate";
//fwrite($myfile,$sql1);
$getVAT = MySQLSelectX($sql1);
//$txt = "SELECT * FROM paytax WHERE VatMonth = '".$VatMonth."' AND TaxCat = '".$TaxCat."'  ORDER BY BookNo,TaxDate \n";
$ia=0;
if ($TaxCat != 'S02'){
    while ($DataList = mysqli_fetch_array($getVAT)){
        $txt = "";
        $ia++;
        //ลำดับที่
    
        $txt .= "|";
        if ($ia <= 9){
            $ix = "0000".$ia;
        }else{
            if ($ia<=99){
                $ix = "000".$ia;
            }else{
                if ($ia<=999){
                    $ix = "00".$ia;
                }else{
                    if ($ia <= 9999){
                        $ix = "0".$ia;
                    }else{
                        $ix = $ia;
                    }
                }
            }
        }
        $txt .= $ix;
        //เลขประจำตัวผู้เสียภาษี
        $txt .= "|";
        $txt .= $DataList['TaxID'];
        //สาขา
        $txt .= "|";
        if ($DataList['BranchID'] == ""){
            $BranchID = "-0001";
        }else{
            if ($DataList['BranchID'] == 0){
                $BranchID = "00000";
            }else{
                if ($DataList['BranchID'] <= 9){
                    $BranchID = "0000".$DataList['BranchID'];
                }else{
                    if ($DataList['BranchID'] <= 99){
                        $BranchID = "000".$DataList['BranchID'];
                    }else{
                        if ($DataList['BranchID'] <= 999){
                            $BranchID = "00".$DataList['BranchID'];
                        }else{
                            if ($DataList['BranchID'] <= 9999){
                                $BranchID = "0".$DataList['BranchID'];
                            }else{
                                $BranchID = $DataList['BranchID'];
                            }
                        }
                    }
                }
            }
        }
        $txt .= $BranchID;
        //คำนำหน่า
        $txt .= "|";
        if (mb_strlen($DataList['NamePrefix']) >= 15){
            $NamePrefix = $DataList['NamePrefix'];
        }else{
            $x = 15 - mb_strlen($DataList['NamePrefix']);
            $addSP = "";
            for ($i=1;$i<=$x;$i++){
                $addSP .= " ";
            }
            $NamePrefix = $DataList['NamePrefix'].$addSP;
        }
        $txt .= $NamePrefix;
        //ชื่อลูกค้า
        $txt .= "|";
        if (mb_strlen($DataList['CardName']) >= 60){
            $CardName = $DataList['CardName'];
        }else{
            $x = 60 - mb_strlen($DataList['CardName']);
            $addSP = "";
            for ($i=1;$i<=$x;$i++){
                $addSP .= " ";
            }
            $CardName = $DataList['CardName'].$addSP;
        }
        $txt .= $CardName;
        
        if ($TaxCat == 'S03'){
            $txt .= "|";
            for ($i=1;$i<=60;$i++){
                $txt .= " ";
            }
        }
        
        //ที่อยู่ลูกค้า
        $txt .= "|";
        if (mb_strlen($DataList['Address']) >= 100){
            $Address = $DataList['Address'];
        }else{
            $x = 100 - mb_strlen($DataList['Address']);
            $addSP = "";
            for ($i=1;$i<=$x;$i++){
                $addSP .= " ";
            }
            $Address = $DataList['Address'].$addSP;
        }
        $txt .= $Address;
        //วันเดือนปีที่จ่าย
        $txt .= "|";
        $dateShow = date("d/m",strtotime($DataList['TaxDate']));
        $yearShow = date("Y",strtotime($DataList['TaxDate']));
        if ($yearShow <= 2500){
            $yearShow = $yearShow+543;
        }
        $txt .= $dateShow."/".$yearShow;
        //ประเภทการจ่าย
        $txt .= "|";
        switch ($DataList['PayType1']){
            case "10" :
                $PayShow = "เงินเดือน";
                break;
            case "11" :
                $PayShow = "ค่าจ้าง";
                break;
            case "12" :
                $PayShow = "โบนัส";
                break;
            case "21" :
                $PayShow = "ค่านายหน้า จ่ายบุคคลธรรมดา";
                break;
            case "22" :
                $PayShow = "ค่านายหน้า จ่ายนิติบุคคล";
                break;
            case "40" :
                $PayShow = "ค่าดอกเบี้ย";               
                break;
            case "413" :
                $PayShow = "1.3 เงินปันผลกิจการ 20%";
                break;
            case "42" :
                $PayShow = "เงินส่วนแบ่งกำไร";
                break;
            case "50" :
                $PayShow = "ค่าจ้างทำของ จ่ายบุคคลธรรมดา";
                break;
            case "51" :
                $PayShow = "ค่าจ้างทำของ จ่ายนิติบุคคล";
                break;
            case "52" :
                $PayShow = "ค่าจ้างโฆษณา";
                break;
            case "53" :
                $PayShow = "ค่าเช่า";
                break;
            case "60" :
                $PayShow = $DataList['PayType160'];
                break;
            default :
                $PayShow = "";
                break;
        }
        if (mb_strlen($PayShow) >=  25){
            $txt .= $PayShow;
        }else{
            $x = 25 - mb_strlen($PayShow);
            $addSP = "";
            for ($i=1;$i<=$x;$i++){
                $addSP .= " ";
            }
            $txt .= $PayShow.$addSP;
        }
        
        //อัตรภาษี
        $txt .= "|";
        $TaxRat = number_format($DataList['TaxRate1'],2);
        if (mb_strlen($TaxRat) <=4){
            $TaxRat = "0".$TaxRat;
        }
        $txt .= $TaxRat;
        //จำนวนเงินที่จ่าย
        $txt .= "|";
        $payTotal = number_format($DataList['DocTotal1'],2,".","");
        while (mb_strlen($payTotal) < 14){
            $payTotal = "0".$payTotal;
        }
        $txt .= $payTotal;
        //ภาษีทีหักไว้
        $txt .= "|";
        $vatTotal = number_format($DataList['VatTotal1'],2,".","");
        while (mb_strlen($vatTotal) < 13){
            $vatTotal = "0".$vatTotal;
        }
        $txt .= $vatTotal;
        // เงือนไข
        $txt .= "|";
        $txt .= $DataList['TaxType'];
        $txt .= "|";
        for ($i=1;$i<=10;$i++){
            $txt .= " ";
        }
        $txt .= "|";
        for ($i=1;$i<=25;$i++){
            $txt .= " ";
        }
        $txt .= "|";
        for ($i=1;$i<=5;$i++){
            $txt .= " ";
        }
        $txt .= "|";
        for ($i=1;$i<=14;$i++){
            $txt .= " ";
        }
        $txt .= "|";
        for ($i=1;$i<=13;$i++){
            $txt .= " ";
        }
        $txt .= "| |";
        $txt .= "\r\n";
        fwrite($myfile, $txt);
    }
}else{
    $sq1 = "SELECT T0.ID FROM wht_JAP T0 WHERE T0.VatMonth = '$VatMonth' AND T0.TaxCat = '$TaxCat' AND T0.Status = 1 ORDER BY T0.BookNo, T0.TaxDate";
    $chkrow = ChkRowDB($sql1);
    //$sql1 = "SELECT * FROM paytax WHERE VatMonth = '".$VatMonth."' AND TaxCat = '".$TaxCat."' AND Status = 1 ORDER BY BookNo,TaxDate";
    while ($DataList = mysqli_fetch_array($getVAT)){
        $txt = "";
        $ia++;
        if ($ia < 10){
            $txt .= "0".$ia;
        }else{
            $txt .= $ia;
        }
        $txt .= "|";
        $txt .= "0105545012035";//1
        $txt .= "|";
        $txt .= $DataList['TaxID'];//2
        $txt .= "|";
        $txt .= $DataList['NamePrefix'];//3
        $txt .= "|";
        $name = substr($DataList['CardName'],0,strpos($DataList['CardName']," "));
        $lname = substr($DataList['CardName'],strpos($DataList['CardName']," ")+1);
        //echo $DataList->CardName."<br>";
        //$name = $DataList->CardName;
        //$lname = $DataList->CardName;
        $txt .= $name;//4
        $txt .= "|";
        $txt .= $lname;//5
        $txt .= "|";
        $txt .= " ";//6
        $txt .= "|";
        $PayType = " ";
        switch ($DataList['PayType1']){
            case "40" :
                 $PayType  = 2;
                 $TaxType = "404A";
                 break;
            case "413" :
                 $PayType = 3;
                 $TaxType = "404B";
                 break;
            case "60" :
                 $PayType = 5;
                 break; 
        }

        $txt .= $PayType;//7
        $txt .= "|";
        $yearShow = date("Y",strtotime($DataList['TaxDate']));
        if ($yearShow <= 2500){
            $yearShow = $yearShow+543;
        }
        $TaxDate = date("d",strtotime($DataList['TaxDate'])).date("m",strtotime($DataList['TaxDate'])).$yearShow;
        $txt .= $TaxDate;//8
        $txt .= "|";
        $txt .= number_format($DataList['TaxRate1']);//9
        $txt .= "|";
        $txt .= $DataList['DocTotal1'];//10
        $txt .= "|";
        $txt .= $DataList['VatTotal1'];//11
        $txt .= "|";
        $txt .= $DataList['TaxType'];//12
        $txt .= "|";
        $txt .= $TaxType;
        if ($ia < $chkrow){
            $txt .= "\r";
        }
        fwrite($myfile, $txt);
    }


}
fclose($myfile);
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename='.basename($FileName));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($FileName));
header("Content-Type: text/plain");
readfile($FileName);

?>


<?php
session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
/*
$VatMonth = intval(substr($_GET['vm'],0,2));
$VatYear = intval(substr($_GET['vm'],3));
*/
$sql1 = "SELECT T0.* FROM wht_JAP T0 WHERE T0.ID = ".$_GET['id'];
$DataList = MySQLSelect($sql1);
//echo $sql1;

function numTocha($number){ 
    $txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ'); 
    $txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'); 
    $number = str_replace(",","",$number); 
    $number = str_replace(" ","",$number); 
    $number = str_replace("บาท","",$number); 
    $number = explode(".",$number); 
    if(sizeof($number)>2){ 
    return 'ทศนิยมหลายตัวนะจ๊ะ'; 
    exit; 
    } 
    $strlen = strlen($number[0]); 
    $convert = ''; 
    for($i=0;$i<$strlen;$i++){ 
      $n = substr($number[0], $i,1); 
      if($n!=0){ 
        if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; } 
        elseif($i==($strlen-2) AND $n==2){  $convert .= 'ยี่'; } 
        elseif($i==($strlen-2) AND $n==1){ $convert .= ''; } 
        else{ $convert .= $txtnum1[$n]; } 
        $convert .= $txtnum2[$strlen-$i-1]; 
      } 
    } 
    
    $convert .= 'บาท'; 
    if($number[1]=='0' OR $number[1]=='00' OR 
    $number[1]==''){ 
    $convert .= 'ถ้วน'; 
    }else{ 
    $strlen = strlen($number[1]); 
    for($i=0;$i<$strlen;$i++){ 
    $n = substr($number[1], $i,1); 
      if($n!=0){ 
      if($i==($strlen-1) AND $n==1){$convert 
      .= 'เอ็ด';} 
      elseif($i==($strlen-2) AND 
      $n==2){$convert .= 'ยี่';} 
      elseif($i==($strlen-2) AND 
      $n==1){$convert .= '';} 
      else{ $convert .= $txtnum1[$n];} 
      $convert .= $txtnum2[$strlen-$i-1]; 
      } 
    } 
    $convert .= 'สตางค์'; 
    }
    return $convert; 
    } 

?>

<!DOCTYPE html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>หนังสือรับรองการหักภาษี ณ ที่จ่าย</title>
    <style rel="stylesheet" type="text/css">
        body {
            font-family: 'Tahoma';
            font-size: 12px;
        }
        h1,h2,h3,h4,h5,h6, p { padding: 8px 0 4px 0; margin: 0; }
        h1 { font-size: 24px; }
        h2 { font-size: 20px; }
        h3 { font-size: 16px; }
        th,td { padding: 3px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bd-sl-all { border: 1px solid #000000; }
        .bd-sl-t { border-top: 1px solid #000000; }
        .bd-sl-r { border-right: 1px solid #000000; }
        .bd-sl-b { border-bottom: 1px solid #000000; }
        .bd-sl-l { border-left: 1px solid #000000; }
        .bd-dt-all { border: 1px solid #000000; }
        .bd-dt-t { border-top: 1px dotted #000000; }
        .bd-dt-r { border-right: 1px dotted #000000; }
        .bd-dt-b { border-bottom: 1px dotted #000000; }
        .bd-dt-l { border-left: 1px dotted #000000; }
        .bd-db-b { border-bottom: 3px double #000000; }
        .tablelooping > tr:last-child > td { border-bottom: 1px solid #000000 !important; }
        table tr.page-break { page-break-after: always; }
        span.barcode { font-family: 'BarCode'; }
        table.detail td { padding-top: .2rem; padding-bottom: .2rem;}
        @page {
            size: 7.5in 11in;
            margin:.45in 0in .45in 0in;
            mso-header-margin:.3in;
            mso-footer-margin:.3in;
        }
        @media all {
            .page-break { display: none; }
        }
        @media print {
            hr { display: none; }
            body { 
                font-size: 13px; 
            }
            .page-break { display: block; height: 1px; page-break-before: always; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="page-break">&nbsp;</div> 
    <!-- หัวกระดาษ -->
    <table width="720px" border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td colspan="6" class="text-center">
                    <span style="font-weight: bold;">หนังสือรับรองการหักภาษี ณ ที่จ่าย</span><br/>
                    ตามมาตรา 50 ทวิ แห่งประมวลรัษฎากร
                </td>
            </tr>
            <tr>
                <td width="7.5%">ลำดับที่</td>
                <td width="7.5%">*..........</td>
                <td width="7.5%">ในแบบ</td>
                <?php
                switch ($DataList['TaxCat']){
                    case 'S02':
                        $TaxCat = " ภ.ง.ด. 2 ";
                        break;
                    case 'S03':
                        $TaxCat = " ภ.ง.ด. 3 ";
                        break;
                    case 'S53' :
                        $TaxCat = " ภ.ง.ด. 53 ";
                        break;
                }


                ?>
                <td><?php echo $TaxCat;?></td>
                <td width="7.5%">เลขที่</td>
                <td width="7.5%"><?php echo $DataList['BookNo']; ?></td>
            </tr>
        </thead>
    </table>
    <table width="720px" border="0" cellpadding="0" cellspacing="0" class="bd-sl-t bd-sl-r bd-sl-b bd-sl-l">
        <tr>
            <td colspan="2" width="22.5%">ผู้มีหน้าที่หักภาษี ณ ที่จ่าย:</td>
            <td class="text-right">เลขที่ประจำตัวผู้เสียภาษี</td>
            <td class="text-center">0205564034307</td>
            <td>สำนักงานใหญ่</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="text-right">ชื่อ</td>
            <td colspan="4">บริษัท เจ เอ พี พร็อพเพอร์ตี้ จำกัด</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="text-right">ที่อยุ่</td>
            <td colspan="4">เลขที่ 23/264 หมู่ที่ 1 ตำบลนาป่า อำเภอเมืองชลบุรี จังหวัดชลบุรี 20000</td>
        </tr>
        <tr>
            <td colspan="2" width="22.5%">ผู้ถูกหักภาษี ณ ที่จ่าย:</td>
            <td class="text-right">เลขที่ประจำตัวผู้เสียภาษี</td>
            <td class="text-center"><?php echo $DataList['TaxID']; ?></td>
            <?php
            if ($DataList['TaxCat'] == 'S53' ){
                switch ($DataList['BranchID']){
                    case '0' :
                        $Branch = "สำนักงานใหญ่";
                        break;
                    case '-1' :
                        $Branch = "";
                        break;
                    default :
                        $Branch = $DataList['BranchID'];
                        break;
                }
            }else{
                if ($DataList['TaxCat'] == 'S03'){
                    $Branch = " ";
                }else{
                    $length = strlen($DataList['BranchID']);
                    $full   = 5;
                    $loop0  = $full-$length;
                    $Branch = "สาขาที่ ";
                    for($aa = 1; $aa <= $loop0; $aa++) {
                        $Branch.= "0";
                    }
                    $Branch.= $DataList['BranchID'];
                }
            }
            ?>
            <td><?php echo $Branch;?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="text-right">ชื่อ</td>
            <td colspan="4"><?php echo $DataList['NamePrefix']." ".$DataList['CardName'];?></td> <!-- หยอด -->
        </tr>
        <tr class="bd-sl-b">
            <td>&nbsp;</td>
            <td class="text-right">ที่อยุ่</td>
            <td colspan="4"><?php echo $DataList['Address'];?></td> <!-- หยอด -->
        </tr>
    </table>
    <table width="720px" border="0" cellpadding="4" cellspacing="0" class="detail bd-sl-r bd-sl-b bd-sl-l">
        <tr>
            <?php
               //กำหนดค่า
               
               $dateShow = date("d/m",strtotime($DataList['TaxDate']));
               $yearShow = date("Y",strtotime($DataList['TaxDate']));
               if ($yearShow <= 2500){
                   $yearShow = $yearShow+543;
               }
               $TaxDate = $dateShow."/".$yearShow;
               $DocTotal = number_format($DataList['DocTotal1'],2);
               $VatTotal = number_format($DataList['VatTotal1'],2);
               $L11 = "";
               $L12 = "";
               $L13 = "";
               $L21 = "";
               $L22 = "";
               $L23 = "";
               $L4A1 = "";
               $L4A2 = "";
               $L4A3 = "";
               $L411 = "";
               $L412 = "";
               $L413 = "";
               $L421 = "";
               $L422 = "";
               $L423 = "";
               $L501 = "";
               $L502 = "";
               $L503 = "";
               $L61 = "";
               $L62 = "";
               $L63 = "";
               
                switch ($DataList['PayType1']){
                    case "10" :
                        $PayShow = "เงินเดือน";
                        $L11 = $TaxDate;
                        $L12 = $DocTotal;
                        $L13 = $VatTotal;
                        break;
                    case "11" :
                        $PayShow = "ค่าจ้าง";
                        $L11 = $TaxDate;
                        $L12 = $DocTotal;
                        $L13 = $VatTotal;
                        break;
                    case "12" :
                        $PayShow = "โบนัส";
                        $L11 = $TaxDate;
                        $L12 = $DocTotal;
                        $L13 = $VatTotal;
                        break;
                    case "21" :
                        $PayShow = "ค่านายหน้า จ่ายบุคคลธรรมดา";
                        $L11 = $TaxDate;
                        $L22 = $DocTotal;
                        $L23 = $VatTotal;
                        break;
                    case "22" :
                        $PayShow = "ค่านายหน้า จ่ายนิติบุคคล";
                        $L21 = $TaxDate;
                        $L22 = $DocTotal;
                        $L23 = $VatTotal;
                        break;
                    case "40" :
                        $PayShow = "4(ก) ค่าดอกเบี้ย";
                        $L4A1 = $TaxDate;
                        $L4A2 = $DocTotal;
                        $L4A3 = $VatTotal;
                        break;
                    case "413" :
                        $PayShow = "4(ข)1.3 เงินปันผลกิจการ 20%";
                        $L411 = $TaxDate;
                        $L412 = $DocTotal;
                        $L413 = $VatTotal;
                        break;
                    case "422" :
                        $PayShow = "เงินส่วนแบ่งกำไร";
                        $L421 = $TaxDate;
                        $L422 = $DocTotal;
                        $L423 = $VatTotal;
                        break;
                    case "50" :
                        $PayShow = "ค่าจ้างทำของ จ่ายบุคคลธรรมดา";
                        $L501 = $TaxDate;
                        $L502 = $DocTotal;
                        $L503 = $VatTotal;
                        break;
                    case "51" :
                        $PayShow = "ค่าจ้างทำของ จ่ายนิติบุคคล";
                        $L501 = $TaxDate;
                        $L502 = $DocTotal;
                        $L503 = $VatTotal;
                        break;
                    case "52" :
                        $PayShow = "ค่าจ้างโฆษณา";
                        $L501 = $TaxDate;
                        $L502 = $DocTotal;
                        $L503 = $VatTotal;

                        break;
                    case "53" :
                        $PayShow = "ค่าเช่า";
                        $L501 = $TaxDate;
                        $L502 = $DocTotal;
                        $L503 = $VatTotal;
                        break;
                    case "60" :
                        $PayShow = $DataList['PayType160'];
                        $L61 = $TaxDate;
                        $L62 = $DocTotal;
                        $L63 = $VatTotal;
                        break;
                    default :
                        $PayShow = "";
                }
            ?>
            <td style="padding-left: 3rem;" class="bd-sl-b" width="55%">
                ประเภทเงินได้พึงประเมินที่จ่าย<br/>
                <span><?php echo $PayShow;?></span> <!-- หยอด -->
            </td>
            <td width="15%" class="text-center bd-sl-b">วันเดือนปี<br/>ภาษีที่จ่าย</td>
            <td width="15%" class="text-center bd-sl-b">จำนวนเงิน<br/>ที่จ่าย</td>
            <td width="15%" class="text-center bd-sl-b">ภาษีที่หัก<br/>และนำส่งไว้</td>
        </tr>
        <!-- 1. เงินเดือน -->
        <!-- ถ้า PayType1 เลือก 10,11,12 ให้หยอดที่ <tr>...</tr> นี้ --> 

        <tr>
            <td style="padding-left: 1rem;">1. เงินเดือน ค่าจ้าง เบี้ยเลี้ยง โบนัส ฯลฯ ตาม ม.40 (1)</td>
            <td class="text-center"><?php echo $L11;?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $L12; ?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $L13; ?></td> <!-- หยอด -->
        </tr>
        <!-- 2 ค่าธรรมเนียม -->
        <!-- ถ้า PayType1 เลือก 21,22 ให้หยอดที่ <tr>...</tr> นี้ --> 
        <tr>
            <td style="padding-left: 1rem;">2. ค่าธรรมเนียม ค่านายหน้า ฯลฯ ตาม ม.40 (2)</td>
            <td class="text-center"><?php echo $L22;?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $L22; ?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $L23; ?></td> <!-- หยอด -->
        </tr>
        <!-- 3 ค่าลิขสิทธ์ -->
        <!-- ปล่อยว่าง --> 
        <tr>
            <td style="padding-left: 1rem;">3. ค่าแห่งลิขสิทธิ์ ฯลฯ ตาม ม.40 (3)</td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ก ค่าดอกเบี้ย -->
        <!-- ถ้า PayType1 เลือก 40 ให้หยอดที่ <tr>...</tr> นี้ --> 
        <tr>
            <td style="padding-left: 1rem;">4. (ก) ค่าดอกเบี้ย ฯลฯ ตาม ม.40 (4) (ก)</td>
            <td class="text-center"><?php echo $L4A1;?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $L4A2; ?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $L4A3; ?></td> <!-- หยอด -->
        </tr>
        <!-- 4ข เงินปันผล -->
        <tr>
            <td style="padding-left: 1rem;">&nbsp;&nbsp;&nbsp;&nbsp;(ข) เงินปันผล เงินส่วนแบ่งกำไร ฯลฯ ตาม ม.40 (4) (ข)</td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ข1 -->
        <tr>
            <td style="padding-left: 2rem;">
                (1) กรณีผู้ได้รับเงินปันผลได้รับเครดิตภาษี โดยจ่ายจาก<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;กำไรสุทธิของกิจการที่ต้องเสียภาษีเงินได้นิติบุคคลในอัตราดังนี้
            </td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ข1 1.1 -->
        <tr>
            <td style="padding-left: 3rem;">(1.1) อัตราร้อยละ 30 ของกําไรสุทธิ</td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ข1 1.2 -->
        <tr>
            <td style="padding-left: 3rem;">(1.2) อัตราร้อยละ 25 ของกําไรสุทธิ</td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ข1 1.3 -->
        <!-- ถ้า PayType1 เลือก 413 ให้หยอดที่ <tr>...</tr> นี้ --> 
        <tr>
            <td style="padding-left: 3rem;">(1.3) อัตราร้อยละ 20 ของกําไรสุทธิ</td>
            <td class="text-center"><?php echo $L411;?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $L412; ?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $L413; ?></td> <!-- หยอด -->
        </tr>
        <!-- 4ข1 1.4 -->
        <tr>
            <td style="padding-left: 3rem;">(1.4) อัตราอื่น ๆ (ระบุ) .......... ของกําไรสุทธิ</td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ข2 -->
        <tr>
            <td style="padding-left: 2rem;">(2) กรณีผู้ได้รับเงินปันผลไม่ได้รับเครดิตภาษี เนื่องจากจ่าย</td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ข2 2.1 -->
        <tr>
            <td style="padding-left: 3rem;">(2.1) กําไรสุทธิของกิจการที่ได้รับยกเว้นภาษี</td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ข2 2.2 -->
        <tr>
            <td style="padding-left: 3rem;">
                (2.2)  เงินปันผลหรือเงินส่วนแบ่งของกําไรที่ได้รับยกเว้น<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ไม่ต้องนํามารวมคํานวณเป็นรายได้เพื่อเสียภาษี<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เงินได้นิติบุคคล
            </td>
            <td class="text-center" valign="top"><?php echo $L421;?></td> <!-- หยอด -->
            <td class="text-right" valign="top"><?php echo $L422; ?></td> <!-- หยอด -->
            <td class="text-right" valign="top"><?php echo $L423; ?></td> <!-- หยอด -->
        </tr>
        <!-- 4ข2 2.3 -->
        <tr>
            <td style="padding-left: 3rem;">
                (2.3)  กําไรสุทธิส่วนที่ได้หักผลขาดทุนสุทธิยกมาไม่เกิน 5 ปี<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ก่อนรอบระยะเวลาบัญชีปีปัจจุบัน
            </td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 4ข2 2.4 -->
        <tr>
            <td style="padding-left: 3rem;">
                (2.4)   กําไรที่รับรู้ทางบัญชีโดยวิธีส่วนได้เสีย<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(equity method)
            </td>
            <td class="text-center">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
            <td class="text-right">&nbsp;</td>
        </tr>
        <!-- 5 การจ่ายเงินได้ -->
        <!-- ถ้า PayType1 เลือก 50,51,52,53 ให้หยอดที่ <tr>...</tr> นี้ -->
        <tr>
            <td style="padding-left: 1rem;">
                5. การจ่ายเงินได้ที่ต้องหักภาษี ณ ที่จ่ายตามคําสั่งกรมสรรพากรที่<br/>
                &nbsp;&nbsp;&nbsp;ออกตามมาตรา 3เตรส (ระบุ)<br/>
                &nbsp;&nbsp;&nbsp;(เช่น รางวัล ส่วนลดหรือประโยชน์ใดๆ เนื่องจากการส่งเสริมการขาย<br/>
                &nbsp;&nbsp;&nbsp;รางวัลในการประกวด การแข่งขัน การ การชิงโชค ค่าแสดงของ<br/>
                &nbsp;&nbsp;&nbsp;นักแสดงสาธารณะ ค่าขนส่ง ค่าบริการ ค่าเบี้ยประกันวินาศภัย ฯลฯ)
            </td>
            <td valign="bottom" class="text-center"><?php echo $L501;?></td>
            <td valign="bottom" class="text-right"><?php echo $L502;?></td>
            <td valign="bottom" class="text-right"><?php echo $L503;?></td>
        </tr>
        <!-- 6 อื่น ๆ -->
        <!-- ถ้า PayType1 เลือก 60 ให้หยอดที่ <tr>...</tr> นี้ พร้อมหยอดข้อความระบุ -->
        <tr>
            <td style="padding-left: 1rem;" class="bd-sl-b">6. อื่น ๆ (ระบุ) <?php if ($DataList['PayType1'] == "60"){echo $DataList['PayType160'];}?><!-- ค่าบริการ --></td><!-- หยอด -->
            <td class="text-center bd-sl-b"><?php echo $L61;?></td> <!-- หยอด -->
            <td class="text-right bd-sl-b"><?php echo $L62; ?></td> <!-- หยอด -->
            <td class="text-right bd-sl-b"><?php echo $L63; ?></td> <!-- หยอด -->
        </tr>

        <!-- รวมทั้งหมด -->
        <tr style="font-weight: bold;">
            <td class="text-right">รวมเงินที่จ่ายและภาษีที่หักนำส่ง</td>
            <td class="text-center">&nbsp;</td> <!-- หยอด -->
            <td class="text-right"><?php echo $DocTotal; ?></td> <!-- หยอด -->
            <td class="text-right"><?php echo $VatTotal; ?></td> <!-- หยอด -->
        </tr>
        <tr>
            <td style="padding-left: 1rem;" colspan="4">รวมเงินภาษีที่หักนำส่ง (<?php echo numTocha($VatTotal); ?>).</td><!-- หยอดในวงเล็บ -->
        </tr>
    </table>
    <table width="720px" border="0" cellpadding="4" cellspacing="0" class="bd-sl-r bd-sl-b bd-sl-l">
        <tr>
            <td class="text-right" width="55%">ผู้จ่ายเงิน</td>
            <!-- ถ้า TaxType เป็น 1 ให้ใช้ &#9746; ถ้าไม่ใช่ให้ใช้ &#9744; -->
            <td class="text-center" style="font-size: 22px;"><?php if ($DataList['TaxType'] == 1){echo "&#9746;";}else{echo "&#9744;";}?></td><!-- หยอด -->
            <td>หักภาษี ณ ที่จ่าย</td>
            <!-- ถ้า TaxType เป็น 2 ให้ใช้ &#9746; ถ้าไม่ใช่ให้ใช้ &#9744; -->
            <td class="text-center" style="font-size: 22px;"><?php if ($DataList['TaxType'] == 2){echo "&#9746;";}else{echo "&#9744;";}?></td><!-- หยอด -->
            <td>ออกให้ตลอดไป</td>
        </tr>
        <tr>
            <td></td>
            <!-- ถ้า TaxType เป็น 3 ให้ใช้ &#9746; ถ้าไม่ใช่ให้ใช้ &#9744; -->
            <td class="text-center" style="font-size: 22px;"><?php if ($DataList['TaxType'] == 3){echo "&#9746;";}else{echo "&#9744;";}?></td><!-- หยอด -->
            <td>ออกภาษีให้ครั้งเดียว</td>
            <!-- ถ้า TaxType เป็น 4 ให้ใช้ &#9746; ถ้าไม่ใช่ให้ใช้ &#9744; พร้อมหยอดระบุ -->
            <td class="text-center" style="font-size: 22px;"><?php if ($DataList['TaxType'] == 4){echo "&#9746;";}else{echo "&#9744;";}?></td><!-- หยอด -->
            <td>อื่นๆ (ระบุ) <!-- ระบุ --><?php if ($DataList['TaxType'] == 4){echo $DataList['TaxTypeRemark'];}?></td>
        </tr>
    </table>
    <table width="720px" border="0" cellpadding="4" cellspacing="0" class="detail bd-sl-r bd-sl-b bd-sl-l">
        <tr>
            <td colspan="3" style="padding-left: 1rem;">ขอรับรองว่าข้อความและตัวเลขดังกล่าวข้างต้นถูกต้องตรงกับความเป็นจริงทุกประการ</td>
        </tr>
        <tr>
            <td style="padding-left: 10rem;" width="55%">ผู้ที่มีหน้าที่หักภาษี ณ ที่จ่าย<br/>วันเดือนปี ที่ออกหนังสือรับรองฯ</td>
            <td class="text-center">..........................................<br/><?php echo $TaxDate;?></td><!-- หยอด -->
            <td class="text-center">ประทับตรา<br/>นิติบุคคล</td>
        </tr>
    </table>
</body>   
</html>
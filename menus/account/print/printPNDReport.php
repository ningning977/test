<?php
session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

?>

<!DOCTYPE html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <script src="../../../../js/jquery-min.js"></script>
    <title>รายงานหักภาษี ณ ที่จ่าย ภ.ง.ด.</title>
    <style rel="stylesheet" type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
        body {
            font-family: 'Sarabun';
            font-weight: 400;
            font-size: 12px;
        }
        .page {
            page-break-after: always;
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
        .bd-dh-b { border-bottom: 1px dashed #000000; }
        .tablelooping > tr:last-child > td { border-bottom: 1px solid #000000 !important; }
        table tr.page-break { page-break-after: always; }
        span.barcode { font-family: 'BarCode'; }
        @page {
            size: 9in 11in;
            padding: 0;
        }
        @media print {
            hr { display: none; }
            body { font-size: 12px; }
        }
    </style>
</head>
<?php 

$TaxCat = substr($_GET['vm'],0,3);
$VatMonth = substr($_GET['vm'],4,2)."/".substr($_GET['vm'],-2);
if ($TaxCat == 'S03'){
    $ShowHead = "ภ.ง.ด. 3";
    $linePages = 12;
}else{
    $ShowHead = "ภ.ง.ด. 53";
    $linePages = 10;
}
$TextMonth = txtMonth(intval(substr($_GET['vm'],4,2)));
$FullYear = "25".substr($_GET['vm'],-2);
$sql1 = "SELECT T0.* FROM wht_JAP T0 WHERE T0.VatMonth = '$VatMonth' AND T0.TaxCat = '$TaxCat' AND T0.Status = 1 ORDER BY T0.BookNO";
$getVAT = MySQLSelectX($sql1);
$i=0;
while ($DataList = mysqli_fetch_array($getVAT)){
    $i++;
    $CardName[$i] = $DataList['CardName'];
    $TaxID[$i] = $DataList['TaxID'];
    $Address[$i] = $DataList['Address'];
    if ($DataList['BranchID'] == "0" AND $DataList['TaxCat'] == 'S53' ){
        $BranchID[$i] = "สำนักงานใหญ่";
    }else{
        if ($DataList['TaxCat'] == 'S03'){
            $BranchID[$i] = " ";
        }else{
            $BranchID[$i] = $DataList['BranchID'];
        }
    }
    $payYear = date("Y",strtotime($DataList['TaxDate']));
    $payYear = substr(($payYear + 543),2);
    $TaxDate[$i]= date("d/m/",strtotime($DataList['TaxDate'])).$payYear;
    $PayType1[$i] = $DataList['PayType1'];
    $Paytype160[$i] = $DataList['PayType160'];
    switch ($DataList['PayType1']){
        case "10" :
            $PayShow[$i] = "เงินเดือน";
            break;
        case "11" :
            $PayShow[$i]  = "ค่าจ้าง";
            break;
        case "12" :
            $PayShow[$i]  = "โบนัส";
            break;
        case "21" :
            $PayShow[$i]  = "ค่านายหน้า จ่ายบุคคลธรรมดา";
            break;
        case "22" :
            $PayShow[$i]  = "ค่านายหน้า จ่ายนิติบุคคล";
            break;
        case "40" :
            $PayShow[$i]  = "ค่าดอกเบี้ย";
            break;
        case "413" :
            $PayShow[$i]  = "1.3 เงินปันผลกิจการ 20%";
            break;
        case "422" :
            $PayShow[$i]  = "เงินส่วนแบ่งกำไร";
            break;
        case "50" :
            $PayShow[$i]  = "ค่าจ้างทำของ จ่ายบุคคลธรรมดา";
            break;
        case "51" :
            $PayShow[$i]  = "ค่าจ้างทำของ จ่ายนิติบุคคล";
            break;
        case "52" :
            $PayShow[$i]  = "ค่าจ้างโฆษณา";
            break;
        case "53" :
            $PayShow[$i]  = "ค่าเช่า";
            break;
        case "60" :
            $PayShow[$i]  = $DataList['PayType160'];
            break;
        default :
            $PayShow[$i]  = "";
    }
    $TaxRate[$i] = $DataList['TaxRate1'];

    $DocTotal[$i] = $DataList['DocTotal1'];
    $VatTotal[$i] = $DataList['VatTotal1'];
    $TaxType[$i] = $DataList['TaxType'];
}
//$i=38;
?>
<body>
    <?php
    $pages = 1;
    $newPage = 1;
    $PayTotal[$pages] = 0;
    $PayVat[$pages] = 0;

    for($ax = 1; $ax<=$i; $ax++) {
        if ($newPage == 1) {
            $newPage = 0;
    ?>
    <!-- ส่วน Loop แต่ละหน้า -->
            <table width="100%" border="0" cellpadding="1" cellspacing="0">
                <tr>
                    <td colspan="2">ใบต่อรอง <?php echo $ShowHead;?></td>
                    
                </tr>
                <tr>
                    <td>บริษัท เจ เอ พี พร็อพเพอร์ตี้ จำกัด<br/>สำหรับเดือน <?php echo $TextMonth;?>/<?php echo $FullYear;?></td>
                    <td class="text-right">เลขประจำตัวผู้เสียภาษีอากร <span style="color: #0066FF;">0205564034307</span> สำนักงานใหญ่<br>แผ่นที่ <?php echo $pages;?> ในจำนวน <span class='TotalPages'></span> แผ่น</td>
                </tr>
            </table>
            <table width="100%" border="0" cellpadding="1" cellspacing="0" class="bd-sl-t">
                <tr>
                    <td rowspan="2" class="text-center bd-sl-r bd-sl-b bd-sl-l">ลำดับที่</td>
                    <td rowspan="2" class="bd-sl-b">ชื่อผู้รับเงินได้พึงประเมิน<br/>ที่อยู่ของผู้มีเงินได้</td>
                    <td rowspan="2" class="text-right bd-sl-r bd-sl-b">เลขประจำตัวผู้เสียภาษี</td>
                    <td rowspan="2" class="text-center bd-sl-r bd-sl-b">สาขา#</td>
                    <td colspan="4" class="text-center bd-sl-r bd-sl-b">รายละเอียดเกี่ยวกับการจ่ายเงินที่ได้พึงประเมิน</td>
                    <td rowspan="2" class="text-center bd-sl-r bd-sl-b">เงินภาษีที่หัก<br/>และนำส่ง<br/>ในครั้งนี้</td>
                    <td rowspan="2" class="text-center bd-sl-b bd-sl-r">เงื่อนไข <?php if ($TaxCat == 'S03'){echo "(2)";}?> </td>
                </tr>
                <tr>
                    <td class="text-center bd-sl-r bd-sl-b">วันจ่าย</td>
                    <td class="text-center bd-sl-r bd-sl-b">ประเภทเงินได้ <?php if ($TaxCat == 'S03'){echo "(1)";}?></td>
                    <td class="text-center bd-sl-r bd-sl-b">อัตรา</td>
                    <td class="text-center bd-sl-r bd-sl-b">เงินที่จ่าย</td>
                </tr>
        <?php }?>
        <tr>
            <td rowspan="2" class="text-right bd-sl-r bd-sl-l bd-sl-b" valign="top"><?php  echo $ax; ?></td>
            <td><?php echo $CardName[$ax];?></td>
            <td class="text-right  bd-sl-r"><?php echo $TaxID[$ax];?>&nbsp;&nbsp;</td>
            <td rowspan="2" class="text-center bd-sl-r bd-sl-b" valign="top"><?php echo $BranchID[$ax];?></td>
            <td rowspan="2" class="text-center bd-sl-r bd-sl-b" valign="top"><?php echo $TaxDate[$ax];?></td>
            <td rowspan="2" class="bd-sl-r bd-sl-b" valign="top"><?php echo $PayShow[$ax];?></td>
            <td rowspan="2" class="text-center bd-sl-r bd-sl-b" valign="top"><?php echo number_format($TaxRate[$ax],2);?></td>
            <td rowspan="2" class="text-right bd-sl-r bd-sl-b" valign="top"><?php echo number_format($DocTotal[$ax],2);?></td>
            <td rowspan="2" class="text-right bd-sl-r bd-sl-b" valign="top"><?php echo number_format($VatTotal[$ax],2);?></td>
            <td rowspan="2" class="text-center bd-sl-b bd-sl-r" valign="top"><?php echo $TaxType[$ax];?></td>
        </tr>
        <tr>
            <td colspan="2" class="bd-sl-r bd-sl-b">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Address[$ax];?></td>
        </tr>
        <?php 
        $PayTotal[$pages] = $PayTotal[$pages] + $DocTotal[$ax];
        $PayVat[$pages] = $PayVat[$pages] +  $VatTotal[$ax];


        if (($ax%$linePages == 0 AND $ax != 1) OR $ax == $i ){
            $newPage = 1;
            
        ?>
            <tr>
                <td></td>
                <td colspan="6">รวมยอดเงินได้และภาษีที่นำส่ง (ในแผ่นนี้)</td>
                <td class="text-right bd-sl-b bd-sl-r bd-sl-l"><?php echo number_format($PayTotal[$pages],2);?></td>
                <td class="text-right bd-sl-b bd-sl-r"><?php echo number_format($PayVat[$pages],2);?></td>
            </tr>
            
            
        <?php 
            $pages = $pages+1;
            $PayTotal[$pages] = 0;
            $PayVat[$pages] = 0;
            if ($i > $ax){
        ?>
                </table>
                <div style="page-break-after: always;"></div>
        <?php         
            }
        }
        if ($i == $ax) {


                $VatMonth = substr($_GET['vm'],4,2)."/".substr($_GET['vm'],-2);
                $newYear = "25".substr($_GET['vm'],-2);
                $newYear = $newYear-543;
                //$newYear = substr($newYear,-2);


                $StartDate = $newYear."-".substr($_GET['vm'],4,2)."-01";

                $EndDate = date("t",strtotime($StartDate));
        ?>
                <tr>
                    <td colspan="10">&nbsp;</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">รวมยอดเงินได้และภาษีที่นำส่งทั้งสิ้น</td>
                    <td colspan="4" rowspan="2" valign="top">งวด 01/<?php echo $VatMonth;?> ถึง <?php echo $EndDate."/".$VatMonth;?></td>
                    <td rowspan="2" class="text-right bd-sl-l bd-sl-t bd-sl-b bd-sl-r"><?php echo number_format(array_sum($PayTotal),2); ?></td>
                    <td rowspan="2" class="text-right bd-sl-t bd-sl-b bd-sl-r"><?php echo number_format(array_sum($PayVat),2); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">รวมใบต่อ <?php echo $ShowHead;?> ทั้งสิ้น <?php echo $pages?> แผ่น</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="5" class="text-center" style="line-height: 1.5;">ลงชื่อ.........................................ผู้จ่ายเงิน<br/>
                                                        (.......................................)<br/>
                                                        ตำแหน่ง........................................... (ถ้ามี)<br/>
                                                        ยื่นวันที่........เดือน..................พ.ศ. .........</td>
                    <td class="text-center" style="padding-top: 2rem;">ประทับตรานิติบุคคล<br/>(ถ้ามี)</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="6">หมายเหตุ (1) ให้ระบุว่าจ่ายเป็นค่าอะไร เช่น ค่าเช่าอาคาร ค่าสอบบัญชี ค่าทนายความ ค่าวิชาชีพของแพทย์<br/>
                    &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;ค่าก่อสร้าง รางวัล ส่วนลด หรือประโยชน์ใด ๆ เนื่องจากการส่งเสริมการขาย รางวัลในการประกวด<br/>
                    &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;การแข่งขัน การชิงโชค ค่าจ้างแสดงภาพยนตร์ ร้องเพลง ดนตรี ค่าจ้างทำของ ค่าจ้างโฆษณา ค่าขนส่งสินค้า ฯลฯ<br/>
                    &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&nbsp;(2) เงื่อนไขการหักภาษี ให้กรอกดังนี้</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="6">&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&nbsp;o หัก ณ ที่จ่าย กรอก 1 o ออกให้ตลอดไป กรอก 2 o ออกให้ครั้งเดียว กรอก 3</td>
                </tr>
            </table>
            <div style="page-break-after: always;"></div>
        <?php }?>

    <?php } ?>

    <!-- แสดงผลเฉพาะหน้าสุดท้าย -->
        

    <!-- ใบปะหน้าสุดท้าย -->
    <div style="page-break-after: always;"></div>
    <hr/>
    <table width="100%" border="0" cellpadding="1" cellspacing="0" class="bd-dh-b" style="line-height: 1.5;">
        <tr>
            <td>
                <span style="color: #0066FF;">ใบช่วยกรอก แบบยื่นรายการภาษีเงินได้หัก ณ ที่จ่าย</span> <?php echo $ShowHead;?><br/>
                &ensp;&ensp;&ensp;&ensp;ตามมาตรา 52 และ มาตรา 59
            </td>
        </tr>
        <tr>
            <td>
                กรณีหักภาษี ณ ที่จ่ายตามมาตรา 3 เตรส และมาตรา 50(3)(4)(5)<br/>
                สำหรับเงินได้พึงประเมินตามมาตรา 40(5)(6)(7)(8)<br/>
                และการเสียภาษีตามมาตรา 48 ทวิ แห่งประมวลรัษฎากร
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="1" cellspacing="0" class="bd-dh-b" style="line-height: 1.5;">
        <tr>
            <td>
                เลขประจำตัวผู้เสียภาษีอากร <span style="color: #0066FF;">0205564034307</span><br/>
                ชื่อผู้มีหน้าที่หักภาษี ณ ที่จ่าย: สาขาที่ *
            </td>
            <td>[x] ยื่นปกติ [] ยื่นเพิ่มเติมครั้งที่......</td>
        </tr>
        <tr>
            <td>
            <span style="color: #0066FF;">บริษัท เจ เอ พี พร็อพเพอร์ตี้ จำกัด</span>
            </td>
        </tr>
        <tr>
            <td>
            ที่อยู่ เลขที่ 23/264 หมู่ที่ 1 ตำบลนาป่า อำเภอเมืองชลบุรี จังหวัดชลบุรี 20000<br/>
            โทรศัพท์ 0-2509-3850
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="1" cellspacing="0" class="bd-dh-b" style="line-height: 1.5;">
        <tr>
            <td>นำส่งภาษีตาม</td>
            <td>เดือนที่จ่ายเงินได้พึงประเมิน &ensp;&ensp;&ensp;&ensp; <span style="color: #0066FF;"><?php echo $TextMonth;?>/<?php echo $FullYear;?></span></td>
        </tr>
        <tr>
            <td>
                [ ] (1) มาตรา 3 เตรส<br/>
                [ ] (2) มาตรา 48 ทวิ<br/>
                [ ] (1) มาตรา 50(3)(4)(5)
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="1" cellspacing="0" class="bd-dh-b" style="line-height: 1.5;">
        <tr>
            <td>มีรายละเอียดการหักเป็นรายผู้มีเงินได้ ปรากฏตาม ใบต่อ <span style="color: #0066FF;"><?php echo $ShowHead?></span> ที่แนบมาพร้อมนี้ :</td>
            <td>จำนวน <span style="color: #0066FF;"><?php echo number_format($i);?></span> ราย</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" style="color: #0066FF;">สรุปรายการภาษีที่นำส่ง</td>
        </tr>
        <tr>
            <td>1. รวมยอดเงินได้ทั้งสิ้น (รวมใบต่อทุกฉบับ)<br/>
                2. รวมยอดภาษีที่นำส่งทั้งสิ้น (รวมใบต่อทุกฉบับ) <br/>
                3. เงินเพิ่ม (ถ้ามี)<br/>
                4. รวมยอดภาษีที่นำส่งทั้งสิ้น และเงินเพิ่ม (2+3)<br/>
            </td>
            <td class="text-right">...................<br/>
                ...............<br/>
                ...................<br/>
                ...............
            </td>
            <?php 
            $FinalTotal = 0;
            $FinalVat = 0;
            for ($x=1;$x<=$pages;$x++){
                $FinalTotal = $FinalTotal + $PayTotal[$x];
                $FinalVat = $FinalVat + $PayVat[$x];
            }
            
            ?>
            <td style="color: #0066FF;" class="text-right">
                <?php echo number_format($FinalTotal,2);?><br/>
                <?php echo number_format($FinalVat,2);?><br/>
                <br/>
                <?php echo number_format($FinalVat,2);?><br/>
            </td>
        </tr>
        <tr>
            <td colspan="3">ตัวอักษร (หนึ่งหมื่นสองพันแปดร้อยสามสิบสองบาทหกสิบสามสตางค์).</td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="1" cellspacing="0" class="bd-dh-b" style="line-height: 1.5;">
        <tr>
            <td class="text-center">ข้าพเจ้าขอรับรองว่า รายการที่แจ้งไว้ข้างต้นนี้ พร้อมกับใบต่อจำนวน <span style="color: #0066FF;"><?php echo $pages;?></span> ฉบับ<br/>เป็นรายการที่ถูกต้องและครบถ้วนทุกประการ</td>
        </tr>    
            <td class="text-center">ลงชื่อ.........................................ผู้จ่ายเงิน<br/>
                                                (.......................................)<br/>
                                                ตำแหน่ง........................................... (ถ้ามี)<br/>
                                                ยื่นวันที่........เดือน..................พ.ศ. .........</td>
            <td class="text-center">ประทับตรานิติบุคคล<br/>(ถ้ามี)</td>
        </tr>
        <?php 
        
        $thisYear = date("Y")+543;
        $today = date("d/m/").$thisYear;
        ?>
        <tr>
            <td>พิมพ์วันที่ <?php echo $today;?> เวลา <?php echo date("H:i:s")?></td>
        </tr>
    </table>
    <input type="hidden" id='totalpages' value="<?php echo $pages;?>">

</body>   
</html>
<script type="text/javascript">
  $(document).ready(function(){
    window.print();
    var pages = $('#totalpages').val();
    $('.TotalPages').html(pages);
  });
</script>

<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
    if(!isset($_GET['docety'])) {
        echo '<script type="text/javascript">window.location="../../../../"; </script>';
    } else {
        $DocEntry = $_GET['docety'];
        $HeaderSQL = 
        "SELECT
            T0.CardCode, T0.CardName, T0.DocNum, T0.DocType, T0.LicTradeNum, T0.DocDate, T0.DocDueDate,
            T0.BilltoCode, T0.ShiptoCode, T0.AddressBillto, T0.AddressShipto, CONCAT(T2.uName,' ',T2.uLastName) AS 'SlpName', T0.Payment_Cond, T0.TaxType, T0.Comments,
            T0.ShippingType, T0.ShipCostType,
            (SELECT COUNT(P0.TransID) FROM order_detail P0 WHERE P0.DocEntry = T0.DocEntry AND P0.LineStatus != 'I' LIMIT 1) AS 'ItemCount',
            T0.DiscTotal, T0.DocTotal, T0.VatSum, T1.MainTeam
        FROM order_header T0
        LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
        LEFT JOIN users T2 ON T1.Ukey = T2.UKey
        WHERE T0.DocEntry = $DocEntry
        LIMIT 1";
        $Rows = CHKRowDB($HeaderSQL);
        if($Rows > 0) {
            $HeaderRST = MySQLSelect($HeaderSQL);
            if($_GET['type'] == "q") {
                $PageHeader = "ใบเสนอราคา / Quotation";
                $DocNum = "QT-".$HeaderRST['DocNum'];
            } else {
                $PageHeader = "ใบสั่งขาย / Sales Order";
                $DocNum = $HeaderRST['DocType']."V-".$HeaderRST['DocNum'];
            }
            switch($HeaderRST['Payment_Cond']) {
                case "CR": $txt_payment = "เครดิต"; break;
                case "CS": $txt_payment = "เงินสด"; break;
                default  : $txt_payment = "ไม่ระบุ"; break;
            }
            switch($HeaderRST['TaxType']) {
                case "S07": $txt_tax = "VAT นอก"; break;
                case "S00": $txt_tax = "VAT ใน"; break;
                case "SNV": $txt_tax = "ไม่มี VAT"; break;
                default   : $txt_tax = "ไม่ระบุ"; break;
            }
            if($_GET['type'] == 'o') {
                $rowsperpage = 15; // row per page
                $addressspan = NULL;
            } else {
                $rowsperpage = 5;
                $addressspan = " rowspan='2'";
            }
            $pages = ceil($HeaderRST['ItemCount']/$rowsperpage);
            $offset = 0;
            $SUMLineTotal = 0;
            $TeamCode = $HeaderRST['MainTeam'];
?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="../../../../css/main/app.css" rel="stylesheet" />
                    <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
                    <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />

                    <title><?php echo $DocNum; ?></title>
                    <style rel="stylesheet" type="text/css">
                        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
                        html, body {
                            background-color: #FFFFFF;
                            font-family: 'Sarabun';
                            font-weight: 200;
                            color: #000 !important;
                            font-size: 11px;
                        }

                        h1,h2,h3,h4,h5,h6 {
                            color: #000;
                            padding: 0;
                            margin: 0;
                            font-weight: 600;
                        }
                        .table-bordered.border-dark tbody,
                        .OrderList.table.border-dark tbody, 
                        .OrderList.table.border-dark tfoot,
                        .QuotationList.table.border-dark tbody, 
                        .QuotationList.table.border-dark tfoot {
                            border-color: #212529 !important;
                        }
                        .OrderList.table.border-dark tbody tr td, 
                        .OrderList.table.border-dark tfoot tr th, 
                        .OrderList.table.border-dark tfoot tr td,
                        .QuotationList.table.border-dark tbody tr td,
                        .QuotationList.table.border-dark tfoot tr th, 
                        .QuotationList.table.border-dark tfoot tr td {
                            border: 0px;
                        }
                        .OrderList.table.border-dark tfoot tr:last-child th, 
                        .OrderList.table.border-dark tfoot tr:last-child td,
                        .QuotationList.table.border-dark tfoot tr:last-child th, 
                        .QuotationList.table.border-dark tfoot tr:last-child td {
                            border-top: 1px solid #212529 !important;
                            border-bottom: 3px double #212529 !important;
                        }
                        .page {
                            /* margin: 3mm;
                            width: 204mm;
                            height: 291mm; */
                            /* border: 1px dashed #000; */
                            width: 210mm;
                            height: 297mm;
                            display: block;
                            margin: 3mm auto;
                            padding: 3mm;
                            box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
                        }
                        @page {
                            size: A4;
                            margin: 0;
                        }
                        @media print {
                            .page {
                                /* margin: 3mm;
                                width: 204mm;
                                height: 291mm;
                                page-break-after: always; */
                                height: initial;
                                margin: 0mm auto;
                                box-shadow: 0 0 0;
                                /* border: 1px dotted #000; */
                                page-break-after: always;
                            }
                        }
                    </style>
                </head>
                <body>
                <?php for($p=1;$p<=$pages;$p++) {
                    $offset = ($p-1)*$rowsperpage;
                ?>
                    <div class="page">
                        <!-- PAGE HEADER -->
                        <table class="table table-borderless table-sm" style="color: #000;">
                        <thead>
                            <tr>
                                <td width="20%" class="text-center">
                                    <img src="../../../../image/logo/kbi_logo.png" class="img-fluid" />
                                </td>
                                <td>
                                    <h4>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                                    <small>
                                        541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                        เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856
                                    </small>
                                </td>
                                <td width="15%" class="align-top text-right">หน้าที่ <?php echo $p; ?> จาก <?php echo $pages; ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-center"><h5 style="margin: 1rem;"><?php echo $PageHeader; ?></h5></td>
                            </tr>
                        </thead>
                        </table>
                        <!-- ORDER HEADER -->
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <tr>
                                <th width="15%">ชื่อลูกค้า:</th>
                                <td><?php echo $HeaderRST['CardCode']." ".$HeaderRST['CardName']; ?></td>
                                <th width="12.5%">เลขที่เอกสาร:</th>
                                <td width="22.5%"><?php echo $DocNum; ?></td>
                            </tr>
                            <tr>
                                <th>เลขที่ผู้เสียภาษี:</th>
                                <td><?php echo $HeaderRST['LicTradeNum']; ?></td>
                                <th>วันที่เอกสาร:</th>
                                <td><?php echo date("d/m/Y",strtotime($HeaderRST['DocDate'])); ?></td>
                            </tr>
                            <tr>
                                <th class="align-top"<?php echo $addressspan; ?>>ที่อยู่เปิดบิล:</th>
                                <td class="align-top"<?php echo $addressspan; ?>><?php echo str_replace($HeaderRST['BilltoCode']." ","",$HeaderRST['AddressBillto']); ?></td>
                                <th class="align-top">วันที่กำหนดส่ง:</th>
                                <td class="align-top"><?php if($_GET['type'] == 'o') { echo date("d/m/Y",strtotime($HeaderRST['DocDueDate'])); } ?></td>
                            </tr>
                            <tr>
                                <?php if($_GET['type'] == 'o') { ?>
                                <th class="align-top">ที่อยู่จัดส่ง:</th>
                                <td class="align-top"><?php echo str_replace($HeaderRST['ShiptoCode']." ","",$HeaderRST['AddressShipto']); ?></td>
                                <?php } ?>
                                <th class="align-top">พนักงานขาย:</th>
                                <td class="align-top"><?php echo $HeaderRST['SlpName']; ?></td>
                            </tr>
                            <tr>
                                <th>เงื่อนไขการจ่ายเงิน:</th>
                                <td><?php echo $txt_payment; ?></td>
                                <th>ประเภทภาษี:</th>
                                <td><?php echo $txt_tax; ?></td>
                            </tr>
                        </table>
                        <?php 
                        $DetailSQL = 
                        "SELECT
                                T0.VisOrder, T0.ItemCode, T0.ItemName, T0.ItemStatus, 
                                T0.WhsCode, T0.Quantity, T0.UnitMsr,
                                T0.GrandPrice, T0.Line_Disc1, T0.Line_Disc2, T0.Line_Disc3, T0.Line_Disc4,
                                T0.UnitPrice, T0.UnitVat, T0.LineTotal, T0.LineVatSum, T0.CodeBars, IFNULL(CASE WHEN T1.P1 = 0 THEN T1.MTPrice ELSE T1.P1 END, 0) AS 'SuggestPrice'
                        FROM order_detail T0
                        LEFT JOIN pricelist T1 ON T0.ItemCode = T1.ItemCode AND T1.PriceType = 'STD' AND T1.PriceStatus = 'A'
                        WHERE T0.DocEntry = $DocEntry AND T0.LineStatus != 'I'
                        ORDER BY T0.VisOrder
                        LIMIT $rowsperpage OFFSET $offset";
                        $DetailQRY = MySQLSelectX($DetailSQL);
                        $r = 0;
                        if($_GET['type'] == 'q') { ?>
                        <p class="text-center" style="font-weight: 600;">บริษัทฯ ขอเสนอราคาสินค้ารายละเอียดดังต่อไปนี้</p>
                        <!-- QUOTATION DETAIL -->
                        <table class="table border-dark QuotationList" style="color: #000;">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" width="3.5%">ลำดับ</th>
                                    <th scope="col" width="10.75%"></th>
                                    <th scope="col">รายละเอียด</th>
                                    <th scope="col" colspan="2">จำนวน</th>
                                    <th scope="col" width="10%">ราคา<br/>ต่อหน่วย</th>
                                    <th scope="col" width="15%">ส่วนลด</th>
                                    <th scope="col" width="12.5%">ราคารวม</th>
                                    <?php if($TeamCode != "ONL") { echo "<th scope=\"col\" width=\"10%\">ราคา<br/>แนะนำขาย</th>"; } ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php

                            while($DetailRST = mysqli_fetch_array($DetailQRY)) {
                                $filesIMG = glob("../../../../image/products/".$DetailRST['ItemCode']."/*.{jpg,png}",GLOB_BRACE);
                                if(isset($filesIMG[0])) {
                                    $imgsrc = $filesIMG[0];
                                } else {
                                    $filesIMG_Type1 = glob("../../../../image/products/".$DetailRST['ItemCode']."/1/*.{jpg,png}",GLOB_BRACE);
                                    if(isset($filesIMG_Type1[0])) {
                                        $imgsrc = $filesIMG_Type1[0];
                                    }else{
                                        $imgsrc = "../../../../image/bg/img-dummy.png";
                                    }
                                }
                                $r++;
                                
                                $NameLen = mb_strlen($DetailRST['ItemName'],'UTF-8');
                                if($NameLen <= 32) {
                                    $ItemName = $DetailRST['ItemName'];
                                } else {
                                    $ItemName = iconv_substr($DetailRST['ItemName'],0,32,'UTF-8')."...";
                                }

                                if($DetailRST['Line_Disc4'] != NULL AND $DetailRST['Line_Disc4'] != "" AND $DetailRST['Line_Disc4'] != 0.00) {
                                    $Discount = number_format($DetailRST['Line_Disc1'],1)."%+".number_format($DetailRST['Line_Disc2'],1)."%+".number_format($DetailRST['Line_Disc3'],1)."%+".number_format($DetailRST['Line_Disc4'],1)."%";
                                } elseif($DetailRST['Line_Disc3'] != NULL AND $DetailRST['Line_Disc3'] != "" AND $DetailRST['Line_Disc3'] != 0.00) {
                                    $Discount = number_format($DetailRST['Line_Disc1'],1)."%+".number_format($DetailRST['Line_Disc2'],1)."%+".number_format($DetailRST['Line_Disc3'],1)."%";
                                } elseif($DetailRST['Line_Disc2'] != NULL AND $DetailRST['Line_Disc2'] != "" AND $DetailRST['Line_Disc2'] != 0.00) {
                                    $Discount = number_format($DetailRST['Line_Disc1'],1)."%+".number_format($DetailRST['Line_Disc2'],1)."%";
                                } elseif($DetailRST['Line_Disc1'] != NULL AND $DetailRST['Line_Disc1'] != "" AND $DetailRST['Line_Disc1'] != 0.00) {
                                    $Discount = number_format($DetailRST['Line_Disc1'],1)."%";
                                } else {
                                    $Discount = NULL;
                                }

                                if($HeaderRST['TaxType'] != "S07") {
                                    $GrandPrice = $DetailRST['GrandPrice']*1.07;
                                    $LineTotal  = $DetailRST['LineTotal']+$DetailRST['LineVatSum'];
                                } else {
                                    $GrandPrice = $DetailRST['GrandPrice'];
                                    $LineTotal  = $DetailRST['LineTotal'];
                                }
                                $SUMLineTotal = $SUMLineTotal+$LineTotal;

                                $SuggestPrice = $DetailRST['SuggestPrice'];

                            ?>
                                <tr>
                                    <td scope="row" class="align-top text-center"><?php echo $DetailRST['VisOrder']+1; ?></td>
                                    <td class="align-top text-center"><img src="<?php echo $imgsrc; ?>" width="100%" /></td>
                                    <td class="align-top"><?php echo "<b>".$DetailRST['ItemName']."</b><br/>รหัสสินค้า: ".$DetailRST['ItemCode']."<br>บาร์โค้ด: ".$DetailRST['CodeBars'].""; ?></td>
                                    <td width="5%" class="align-top text-right"><?php echo number_format($DetailRST['Quantity'],0); ?></td>
                                    <td width="6%" class="align-top"><?php echo $DetailRST['UnitMsr']; ?></td>
                                    <td class="align-top text-right"><?php echo number_format($GrandPrice,3); ?></td>
                                    <td class="align-top text-center"><?php echo $Discount; ?></td>
                                    <td class="align-top fw-bolder text-right"><?php echo number_format($LineTotal,2); ?></td>
                                    <?php if($TeamCode != "ONL") { echo "<td class=\"align-top text-danger fw-bolder text-right\">".number_format($SuggestPrice,2)."</td>"; } ?>
                                </tr>
                            <?php }
                            if($p != $pages) { ?>
                                <tr>
                                    <td colspan="9" class="text-center">*** มีต่อหน้าถัดไป ***</td>
                                </tr>
                            <?php } else { $blank = $rowsperpage-$r;
                                for($b = 1; $b <= $blank; $b++)  { ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><img src="../../../../image/bg/img-dummy.png" width="100%" /></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php }
                            } ?>
                            </tbody>
                            <tfoot>
                        <?php if($p == $pages) { ?>
                                <tr>
                                    <th colspan="2">เอกสารอ้างอิง:</th>
                                    <td colspan="3">&nbsp;</td>
                                    <th colspan="2" class="table-active text-dark text-right">ยอดรวมทุกรายการ:</td>
                                    <th class="text-right"><?php echo number_format($SUMLineTotal,2); ?></th>
                                    <th class="table-active text-dark"></th>
                                </tr>
                                <tr>
                                    <th class="align-top" colspan="2" rowspan="3">หมายเหตุ:</th>
                                    <td class="align-top" colspan="3" rowspan="3"><?php echo $HeaderRST['Comments']; ?>
                                    <th colspan="2" class="table-active text-dark text-right">ส่วนลดท้ายบิล:</td>
                                    <td class="text-right"><?php echo number_format($HeaderRST['DiscTotal'],2); ?></td>
                                    <th class="table-active text-dark"></th>
                                </tr>
                            <?php 
                                switch($HeaderRST['TaxType']) {
                                    case "S07":
                                        $txt_pricebefvat = $SUMLineTotal-$HeaderRST['DiscTotal'];
                                        $txt_tax         = $txt_pricebefvat*0.07;
                                        $txt_doctotal    = $txt_pricebefvat+$txt_tax;
                                        break;
                                    case "S00":
                                        $txt_pricebefvat = ($SUMLineTotal-$HeaderRST['DiscTotal'])/1.07;
                                        $txt_tax         = $txt_pricebefvat*0.07;
                                        $txt_doctotal    = $txt_pricebefvat+$txt_tax;
                                    break;
                                    case "SNV":
                                        $txt_pricebefvat = $SUMLineTotal-$HeaderRST['DiscTotal'];
                                        $txt_tax         = 0;
                                        $txt_doctotal    = $txt_pricebefvat+$txt_tax;
                                    break;
                                }
                            ?>
                                <tr>
                                    <th colspan="2" class="table-active text-dark text-right">ยอดสินค้าหลังหักส่วนลด:</th>
                                    <td class="text-right"><?php echo number_format($txt_pricebefvat,2); ?></td>
                                    <th class="table-active text-dark"></th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="table-active text-dark text-right">ภาษีมูลค่าเพิ่ม:</th>
                                    <td class="text-right"><?php echo number_format($txt_tax,2); ?></td>
                                    <th class="table-active text-dark"></th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-center"><i>(<?php echo numText(number_format($HeaderRST['DocTotal'],2)); ?>)</i></th>
                                    <th colspan="2" class="table-active text-dark text-right">จำนวนเงินรวมสุทธิ:</th>
                                    <th class="text-right"><?php echo number_format($HeaderRST['DocTotal'],2); ?></th>
                                    <th class="table-active text-dark"></th>
                                </tr>
                        <?php } else { ?>
                                <tr>
                                    <th colspan="2">เอกสารอ้างอิง:</th>
                                    <td colspan="3">&nbsp;</td>
                                    <th colspan="2" class="table-active text-dark text-right">ยอดรวมทุกรายการ:</td>
                                    <th class="text-right">&nbsp;</th>
                                    <th class="table-active text-dark"></th>
                                </tr>
                                <tr>
                                    <th class="align-top" colspan="2" rowspan="3">หมายเหตุ:</th>
                                    <td class="align-top" colspan="3" rowspan="3"><?php echo $HeaderRST['Comments']; ?>
                                    <th colspan="2" class="table-active text-dark text-right">ส่วนลดท้ายบิล:</td>
                                    <td class="text-right">&nbsp;</td>
                                    <th class="table-active text-dark"></th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="table-active text-dark text-right">ยอดสินค้าหลังหักส่วนลด:</th>
                                    <td class="text-right">&nbsp;</td>
                                    <th class="table-active text-dark"></th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="table-active text-dark text-right">ภาษีมูลค่าเพิ่ม:</th>
                                    <td class="text-right">&nbsp;</td>
                                    <th class="table-active text-dark"></th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-center">&nbsp;</th>
                                    <th colspan="2" class="table-active text-dark text-right">จำนวนเงินรวมสุทธิ:</th>
                                    <td class="text-right">&nbsp;</td>
                                    <th class="table-active text-dark"></th>
                                </tr>
                        <?php } ?>
                            </tfoot>
                        </table>
                        <?php if($p == $pages) { ?>
                        <table class="table table-bordered border-dark" style="color: #000;">
                            <thead class="text-center">
                                <tr>
                                    <th width="50%">ผู้เสนอราคา</th>
                                    <th width="50%">ลงนามยืนยันการสั่งซื้อ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr height="48px">
                                    <td class="align-bottom text-center"><?php echo $_SESSION['uName']." ".$_SESSION['uLastName']; ?></td>
                                    <td class="align-bottom text-center">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="align-bottom text-center">(<?php echo $_SESSION['uName']." ".$_SESSION['uLastName']; ?>)</td>
                                    <td class="align-bottom text-center">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                                </tr>
                                <tr>
                                    <td class="align-bottom text-center">วันที่ <?php echo date("d/m/Y"); ?></td>
                                    <td class="align-bottom text-center">วันที่ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                        <?php } ?>
                    <?php } else { ?>
                        <!-- ORDER DETAIL -->
                        <table class="table border-dark OrderList" style="color: #000;">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" width="3.5%">ลำดับ</th>
                                    <th scope="col" width="10%">รหัสสินค้า</th>
                                    <th scope="col">รายละเอียด</th>
                                    <th scope="col" width="5%">คลัง</th>
                                    <th scope="col" colspan="2">จำนวน</th>
                                    <th scope="col" width="10%">ราคา<br/>ต่อหน่วย</th>
                                    <th scope="col" width="15%">ส่วนลด</th>
                                    <th scope="col" width="12.5%">ราคารวม</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php

                            while($DetailRST = mysqli_fetch_array($DetailQRY)) {
                                $r++;
                                $NameLen = mb_strlen($DetailRST['ItemName'],'UTF-8');
                                if($NameLen <= 32) {
                                    $ItemName = $DetailRST['ItemName'];
                                } else {
                                    $ItemName = iconv_substr($DetailRST['ItemName'],0,32,'UTF-8')."...";
                                }

                                if($DetailRST['Line_Disc4'] != NULL AND $DetailRST['Line_Disc4'] != "" AND $DetailRST['Line_Disc4'] != 0.00) {
                                    $Discount = number_format($DetailRST['Line_Disc1'],1)."%+".number_format($DetailRST['Line_Disc2'],1)."%+".number_format($DetailRST['Line_Disc3'],1)."%+".number_format($DetailRST['Line_Disc4'],1)."%";
                                } elseif($DetailRST['Line_Disc3'] != NULL AND $DetailRST['Line_Disc3'] != "" AND $DetailRST['Line_Disc3'] != 0.00) {
                                    $Discount = number_format($DetailRST['Line_Disc1'],1)."%+".number_format($DetailRST['Line_Disc2'],1)."%+".number_format($DetailRST['Line_Disc3'],1)."%";
                                } elseif($DetailRST['Line_Disc2'] != NULL AND $DetailRST['Line_Disc2'] != "" AND $DetailRST['Line_Disc2'] != 0.00) {
                                    $Discount = number_format($DetailRST['Line_Disc1'],1)."%+".number_format($DetailRST['Line_Disc2'],1)."%";
                                } elseif($DetailRST['Line_Disc1'] != NULL AND $DetailRST['Line_Disc1'] != "" AND $DetailRST['Line_Disc1'] != 0.00) {
                                    $Discount = number_format($DetailRST['Line_Disc1'],1)."%";
                                } else {
                                    $Discount = NULL;
                                }

                                if($HeaderRST['TaxType'] != "S07") {
                                    $GrandPrice = $DetailRST['GrandPrice']*1.07;
                                    $LineTotal  = $DetailRST['LineTotal']+$DetailRST['LineVatSum'];
                                } else {
                                    $GrandPrice = $DetailRST['GrandPrice'];
                                    $LineTotal  = $DetailRST['LineTotal'];
                                }
                                $SUMLineTotal = $SUMLineTotal+$LineTotal;
                            ?>
                                <tr>
                                    <td scope="row" class="align-top text-center"><?php echo $DetailRST['VisOrder']+1; ?></td>
                                    <td class="align-top text-center"><?php echo $DetailRST['ItemCode']; ?></td>
                                    <td class="align-top"><?php echo $ItemName; ?></td>
                                    <td class="align-top text-center"><?php echo $DetailRST['WhsCode']; ?></td>
                                    <td width="5%" class="align-top text-right"><?php echo number_format($DetailRST['Quantity'],0); ?></td>
                                    <td width="6%" class="align-top"><?php echo $DetailRST['UnitMsr']; ?></td>
                                    <td class="align-top text-right"><?php echo number_format($GrandPrice,3); ?></td>
                                    <td class="align-top text-center"><?php echo $Discount; ?></td>
                                    <td class="align-top text-right"><?php echo number_format($LineTotal,2); ?></td>
                                </tr>
                            <?php }
                            if($p != $pages) { ?>
                                <tr>
                                    <td colspan="9" class="text-center">*** มีต่อหน้าถัดไป ***</td>
                                </tr>
                            <?php } else { $blank = ($rowsperpage+1)-$r;
                                for($b = 1; $b <= $blank; $b++)  { ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php }
                            } ?>
                            </tbody>
                            <tfoot>
                        <?php if($p == $pages) { ?>
                                <tr>
                                    <th colspan="2">เอกสารอ้างอิง:</th>
                                    <td colspan="4">&nbsp;</td>
                                    <th colspan="2" class="table-active text-dark text-right">ยอดรวมทุกรายการ:</td>
                                    <th class="text-right"><?php echo number_format($SUMLineTotal,2); ?></th>
                                </tr>
                                <tr>
                                    <th class="align-top" colspan="2" rowspan="3">หมายเหตุ:</th>
                                    <td class="align-top" colspan="4" rowspan="3"><?php echo $HeaderRST['Comments']; ?>
                                    <th colspan="2" class="table-active text-dark text-right">ส่วนลดท้ายบิล:</td>
                                    <td class="text-right"><?php echo number_format($HeaderRST['DiscTotal'],2); ?></td>
                                </tr>
                            <?php 
                                switch($HeaderRST['TaxType']) {
                                    case "S07":
                                        $txt_pricebefvat = $SUMLineTotal-$HeaderRST['DiscTotal'];
                                        $txt_tax         = $txt_pricebefvat*0.07;
                                        $txt_doctotal    = $txt_pricebefvat+$txt_tax;
                                        break;
                                    case "S00":
                                        $txt_pricebefvat = ($SUMLineTotal-$HeaderRST['DiscTotal'])/1.07;
                                        $txt_tax         = $txt_pricebefvat*0.07;
                                        $txt_doctotal    = $txt_pricebefvat+$txt_tax;
                                    break;
                                    case "SNV":
                                        $txt_pricebefvat = $SUMLineTotal-$HeaderRST['DiscTotal'];
                                        $txt_tax         = 0;
                                        $txt_doctotal    = $txt_pricebefvat+$txt_tax;
                                    break;
                                }
                            ?>
                                <tr>
                                    <th colspan="2" class="table-active text-dark text-right">ยอดสินค้าหลังหักส่วนลด:</th>
                                    <td class="text-right"><?php echo number_format($txt_pricebefvat,2); ?></td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="table-active text-dark text-right">ภาษีมูลค่าเพิ่ม:</th>
                                    <td class="text-right"><?php echo number_format($txt_tax,2); ?></td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-center"><i>(<?php echo numText(number_format($HeaderRST['DocTotal'],2)); ?>)</i></th>
                                    <th colspan="2" class="table-active text-dark text-right">จำนวนเงินรวมสุทธิ:</th>
                                    <th class="text-right"><?php echo number_format($HeaderRST['DocTotal'],2); ?></th>
                                </tr>
                        <?php } else { ?>
                                <tr>
                                    <th colspan="2">เอกสารอ้างอิง:</th>
                                    <td colspan="4">&nbsp;</td>
                                    <th colspan="2" class="table-active text-dark text-right">ยอดรวมทุกรายการ:</td>
                                    <th class="text-right">&nbsp;</th>
                                </tr>
                                <tr>
                                    <th class="align-top" colspan="2" rowspan="3">หมายเหตุ:</th>
                                    <td class="align-top" colspan="4" rowspan="3"><?php echo $HeaderRST['Comments']; ?>
                                    <th colspan="2" class="table-active text-dark text-right">ส่วนลดท้ายบิล:</td>
                                    <td class="text-right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="table-active text-dark text-right">ยอดสินค้าหลังหักส่วนลด:</th>
                                    <td class="text-right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="table-active text-dark text-right">ภาษีมูลค่าเพิ่ม:</th>
                                    <td class="text-right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-center">&nbsp;</th>
                                    <th colspan="2" class="table-active text-dark text-right">จำนวนเงินรวมสุทธิ:</th>
                                    <td class="text-right">&nbsp;</td>
                                </tr>
                        <?php } ?>
                            </tfoot>
                        </table>
                    <?php } ?>

                    </div>
                <?php } ?>
                <script type="text/javascript">
                    window.print();
                </script>
                </body>
            </html>
<?php
        }
    }
}
?>
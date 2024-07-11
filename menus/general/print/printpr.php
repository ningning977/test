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
            T0.DocEntry, T0.DocType, T0.DocNum, T0.CANCELED, T0.DraftStatus, T0.AppStatus, T0.DocStatus, T0.Printed, T0.CreateDate,
            CONCAT(T0.DocType,T0.DocNum) AS 'DocumentNo', T0.DocDate, T0.DocDueDate, T0.ProductType, T4.TypeName, T0.U_PONo, T0.CreateUkey, 
            T0.ItemQuotaTeam, T0.ShiptoType, T0.ShiptoAddress, T0.PackageRemark, T0.PackageFilePath,
            CONCAT(T1.uName,' ',T1.uLastName) AS 'CreateName', T1.LvCode, T2.DeptCode, T3.DeptName, T0.DocRemark, T0.Comments,
            (SELECT MAX(P0.UnitCur) FROM purreq_detail P0 WHERE P0.DocEntry = T0.DocEntry AND P0.LineStatus != 'I' LIMIT 1) AS 'DocCur',
            (SELECT MAX(P0.UnitRate) FROM purreq_detail P0 WHERE P0.DocEntry = T0.DocEntry AND P0.LineStatus != 'I' LIMIT 1) AS 'DocRate',
            (SELECT COUNT(P0.TransID) FROM purreq_detail P0 WHERE P0.DocEntry = T0.DocEntry AND P0.LineStatus != 'I' LIMIT 1) AS 'ItemCount'
        FROM purreq_header T0
        LEFT JOIN users T1 ON T0.CreateUkey = T1.Ukey
        LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
        LEFT JOIN departments T3 ON T2.DeptCode = T3.DeptCode
        LEFT JOIN purreq_ItemType T4 ON T0.ProductType = T4.TypeCode
        WHERE T0.DocEntry = $DocEntry LIMIT 1";
        $Rows = CHKRowDB($HeaderSQL);
        if($Rows > 0) {
            $HeaderRST = MySQLSelect($HeaderSQL);
            $PageHeader = "ใบขอสั่งซื้อ / Purchase Requisition (PR)";
            $DocNum = $HeaderRST['DocType'].$HeaderRST['DocNum'];

            switch($HeaderRST['DocType']) {
                case "LC": $txt_DocType = "สั่งซื้อสินค้าในประเทศ"; break;
                case "IM": $txt_DocType = "สั่งซื้อสินค้าต่างประเทศ"; break;
                default  : $txt_DocType = "ไม่ระบุ"; break;
            }

            switch($HeaderRST['ShiptoType']) {
                case "KBI": $txt_ShiptoType = "สำนักงานใหญ่ (KBI)"; break;
                case "KSY": $txt_ShiptoType = "คลังสินค้าลาดสวาย (KSY / KSM)"; break;
                case "OTR": $txt_ShiptoType = $HeaderRST['ShiptoAddress']; break;
                default: $txt_ShiptoType = "ไม่ระบุ"; break;
            }

            switch($HeaderRST['DocCur']) {
                case "THB": $txt_DocCur = $HeaderRST['DocCur']; break;
                default: $txt_DocCur = $HeaderRST['DocCur']." (1 ".$HeaderRST['DocCur']." = ".$HeaderRST['DocRate']."<span class='text-danger'>*</span> บาท)"; break;
            }

            switch($HeaderRST['PackageRemark']) {
                case 1: $txt_Package = "สินค้าเก่า แพ็คเกจเดิม"; break;
                case 2: $txt_Package = "สินค้าเก่า แพ็คเกจใหม่ (พิกัด: ".$Header['PackageFilePath'].")"; break;
                case 3: $txt_Package = "สินค้าใหม่ (พิกัด: ".$Header['PackageFilePath'].")"; break;
                case 4: $txt_Package = "สินค้าใหม่ พร้อมขอแพ็คเกจเปล่าสำรอง (พิกัด: ".$Header['PackageFilePath'].")"; break;
                default: $txt_Package = "ไม่ระบุ"; break;
            }

            $rowsperpage = 15; // row per page
            $pages = ceil($HeaderRST['ItemCount']/$rowsperpage);
            $offset = 0;
            $SUMLineTotal = 0;
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
                                <th width="15%">&nbsp;</th>
                                <td>&nbsp;</td>
                                <th width="20%">เลขที่เอกสาร:</th>
                                <td width="30%"><?php echo $DocNum; ?></td>
                            </tr>
                            <tr>
                                <th>ผู้ขอซื้อ:</th>
                                <td><?php echo $HeaderRST['CreateName']; ?></td>
                                <th>ฝ่ายที่ขอซื้อ:</th>
                                <td><?php echo $HeaderRST['DeptName']; ?></td>
                            </tr>
                            <tr>
                                <th>วันที่ขอสั่งซื้อ:</th>
                                <td><?php echo date("d/m/Y",strtotime($HeaderRST['DocDate'])); ?></td>
                                <th>วันที่ต้องการสินค้า:</th>
                                <td class="text-danger" style="font-weight: bold;"><?php echo date("d/m/Y",strtotime($HeaderRST['DocDueDate'])); ?></td>
                            </tr>
                            <tr>
                                <th>ประเภทการสั่งซื้อ:</th>
                                <td><?php echo $txt_DocType; ?></td>
                                <th>ประเภทสินค้าที่ต้องการ:</th>
                                <td><?php echo $HeaderRST['TypeName']; ?></td>
                            </tr>
                            <tr>
                                <th>สถานที่จัดส่ง:</th>
                                <td colspan="3"><?php echo $txt_ShiptoType; ?></td>
                                <!-- <th>เอกสารอ้างอิง:</th>
                                <td class="text-danger" style="font-weight: bold;"><?php echo $HeaderRST['U_PONo']; ?></td> -->
                            </tr>
                            <tr>
                                <th>ทีมขายที่จองสินค้า:</th>
                                <td><?php echo $HeaderRST['ItemQuotaTeam']; ?></td>
                                <th>สกุลเงินที่สั่งซื้อ:</th>
                                <td><?php echo $txt_DocCur; ?></td>
                            </tr>
                            <tr>
                                <th>เหตุผลในการสั่งซื้อ:</th>
                                <td colspan="3"><?php echo $HeaderRST['DocRemark']; ?></td>
                            </tr>
                        </table>
                        <?php 
                        $DetailSQL = 
                        "SELECT
                            T0.VisOrder, T0.ItemCode, T0.ItemName, T0.ItemStatus, 
                            T0.WhsCode, T0.Qty, T0.OpenQty, T0.UnitMsr, 
                            T0.UnitPrice, T0.UnitPriceTHB, T0.LineTotal, T0.SalePriceTHB
                        FROM purreq_detail T0
                        WHERE T0.DocEntry = $DocEntry AND T0.LineStatus != 'I'
                        ORDER BY T0.VisOrder
                        LIMIT $rowsperpage OFFSET $offset";
                        $DetailQRY = MySQLSelectX($DetailSQL);
                        $r = 0;
                    ?>
                        <p class="text-center" style="font-weight: bold;">
                            *** หากต้องการเปลี่ยนแปลงหรือ<span class="text-danger">ยกเลิก</span>คำขอซื้อ จะต้องแจ้งฝ่ายจัดซื้อ<span class="text-danger">ภายใน 5 วัน</span> (นับตั้งแต่วันที่เอกสารได้รับอนุมัติ) ***
                        </p>
                        <!-- ORDER DETAIL -->
                        <table class="table border-dark OrderList" style="color: #000;">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" width="3.5%">ลำดับ</th>
                                    <th scope="col" width="12.5%">รหัสสินค้า</th>
                                    <th scope="col">รายละเอียด</th>
                                    <th scope="col" width="5%">คลัง</th>
                                    <th scope="col" colspan="2">จำนวน</th>
                                    <th scope="col" width="10%">ราคาต่อหน่วย<br/>(<?php echo substr($txt_DocCur,0,3); ?>)</th>
                                    <th scope="col" width="12.5%">ราคารวม<br/>(<?php echo substr($txt_DocCur,0,3); ?>)</th>
                                    <th scope="col" width="12.5%">ราคาขายต่อหน่วย<br/>(THB)</th>
                                    <th scope="col" width="7.5%">GP<br/>(%)</th>
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

                                if($DetailRST['UnitPrice'] == 0) {
                                    $UnitPrice = "-";
                                } else {
                                    $UnitPrice = number_format($DetailRST['UnitPrice'],3);
                                }

                                if($DetailRST['LineTotal'] == 0) {
                                    $LineTotal = "-";
                                } else {
                                    $LineTotal = number_format($DetailRST['LineTotal'],2);
                                }

                                if($DetailRST['SalePriceTHB'] == 0) {
                                    $SalePriceTHB = "-";
                                    $GP = "-";
                                } else {
                                    $SalePriceTHB = number_format($DetailRST['SalePriceTHB'],2);;
                                    $GP = number_format((($DetailRST['SalePriceTHB']-$DetailRST['UnitPriceTHB'])/$DetailRST['SalePriceTHB'])*100,2);
                                }



                                $SUMLineTotal = $SUMLineTotal+$DetailRST['LineTotal'];
                            ?>
                                <tr>
                                    <td scope="row" class="align-top text-center"><?php echo $DetailRST['VisOrder']+1; ?></td>
                                    <td class="align-top text-center"><?php echo $DetailRST['ItemCode']; ?></td>
                                    <td class="align-top"><?php echo $ItemName; ?></td>
                                    <td class="align-top text-center"><?php echo $DetailRST['WhsCode']; ?></td>
                                    <td width="5%" class="align-top text-right"><?php echo number_format($DetailRST['Qty'],0); ?></td>
                                    <td width="6%" class="align-top"><?php echo $DetailRST['UnitMsr']; ?></td>
                                    <td class="align-top text-right"><?php echo $UnitPrice; ?></td>
                                    <td class="align-top text-right" style="font-weight: bold;"><?php echo $LineTotal; ?></td>
                                    <td class="align-top text-right"><?php echo $SalePriceTHB; ?></td>
                                    <td class="align-top text-center"><?php echo $GP; ?></td>
                                </tr>
                            <?php }
                            if($p != $pages) { ?>
                            <?php } else { $blank = ($rowsperpage)-$r;
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
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php }
                            } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th height="64px" class="align-top" colspan="2">หมายเหตุสำหรับแพ็คเกจ:</th>
                                    <td class="align-top" colspan="8"><?php echo $txt_Package; ?></td>
                                </tr>
                                <tr>
                                    <th height="64px" class="align-top" colspan="2">หมายเหตุแนบท้าย:</th>
                                    <td class="align-top" colspan="8"><?php echo $HeaderRST['Comments']; ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <table class="table table-bordered border-dark" style="color: #000;">
                            <thead class="text-center">
                                <tr>
                                    <th width="50%">ผู้ขอซื้อ</th>
                                    <th width="50%">ผู้จัดการฝ่ายที่ขอซื้อ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr height="48px">
                                    <td class="align-bottom text-center"><?php echo $HeaderRST['CreateName']; ?></td>
                                    <td class="align-bottom text-center">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="align-bottom text-center">(<?php echo $HeaderRST['CreateName']; ?>)</td>
                                    <td class="align-bottom text-center">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                                </tr>
                                <tr>
                                    <td class="align-bottom text-center">วันที่ <?php echo date("d/m/Y"); ?></td>
                                    <td class="align-bottom text-center">วันที่ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                        <small>
                            FM-PU-03 Rev.09 วันที่มีผลบังคับใช้ 01/01/2566 | อายุการจัดเก็บ: 1 ปี | วิธีทำลาย: ย่อยทิ้ง
                        </small>
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
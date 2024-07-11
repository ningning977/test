<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
    exit;
} else {
    if(!isset($_GET['docety'])) {
        echo '<script type="text/javascript">window.location="../../../../"; </script>';
        exit;
    } else {
        $DocEntry = $_GET['docety'];
        $HeaderSQL = 
            "SELECT TOP 1
                T0.DocEntry, T0.DocDate, T0.DocDueDate, (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.CardCode, T0.CardName, T0.Address, T0.Address2, T0.LicTradNum, T0.U_PONo,
                T5.U_ChqCond, T0.VatSum, T0.DocTotal, T0.U_SumInThai, T0.OwnerCode, T0.Comments, T4.U_Dim1,
                T2.LastName, T2.FirstName, T3.USER_CODE, T4.SlpName, T6.Name, T7.PymntGroup, T5.U_BillCond, T8.Name AS 'CntctName', T9.U_Name, T9.U_Address,
                T5.MailStrNo
            FROM ORDR T0
            LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
            LEFT JOIN OHEM T2 ON T0.OwnerCode = T2.empID
            LEFT JOIN OUSR T3 ON T0.UserSign = T3.USERID
            LEFT JOIN OSLP T4 ON T0.SlpCode = T4.SlpCode
            LEFT JOIN OCRD T5 ON T0.CardCode = T5.CardCode
            LEFT JOIN [dbo].[@TERITORY] T6 ON T5.U_Teritory = T6.Code
            LEFT JOIN OCTG T7 ON T0.GroupNum = T7.GroupNum
            LEFT JOIN OCPR T8 ON T0.CntctCode = T8.CntctCode
            LEFT JOIN [dbo].[@SHIPPINGTYPE] T9 ON T0.U_ShippingType = T9.Code
            WHERE T0.DocEntry = $DocEntry";
        $Rows = ChkRowSAP($HeaderSQL);
        if($Rows > 0) {
            $HeaderQRY = SAPSelect($HeaderSQL);
            $HeaderRST = odbc_fetch_array($HeaderQRY);
            $PageHeader = "ใบสั่งขาย / Sales Order";

            // GET PICKER AND TABLE //
            $GetPickSQL = "SELECT CONCAT(T1.uName,' ',T1.uLastName,' (',T1.uNickName,')') AS 'PickerName', T0.TablePacking FROM picker_soheader T0 LEFT JOIN users T1 ON T0.UkeyPicker = T1.uKey WHERE T0.SODocEntry = $DocEntry LIMIT 1";
            $PickRow    = ChkRowDB($GetPickSQL);
            if($PickRow == 0) {
                $PickName = NULL;
                $PackName = NULL;
            } else {
                $GetPickRST = MySQLSelect($GetPickSQL);
                $PickName = $GetPickRST['PickerName'];
                $PackName = $GetPickRST['TablePacking'];
            }

            $rowsperpage = 15; // row per page
            $DetailSQL = "SELECT TOP 1 COUNT(T0.VisOrder) AS 'Row' FROM RDR1 T0 WHERE T0.DocEntry = $DocEntry";
            $DetailQRY = SAPSelect($DetailSQL);
            $DetailRST = odbc_fetch_array($DetailQRY);
            $ItemCount = $DetailRST['Row'];
            $pages = ceil($ItemCount/$rowsperpage);
            $offset = 0;
            $SUMLineTotal = 0;

            if($_SESSION['DeptCode'] == "DP011" || $_SESSION['DeptCode'] == "DP002") {
                $PrintSQL = "SELECT ID FROM picker_soheader WHERE SODocEntry = $DocEntry AND DocType = 'ORDR'";
                if (CHKRowDB($PrintSQL)){
                    $PrintU = MySQLSelect($PrintSQL);
                    $UpdateSQL = "UPDATE picker_soheader SET Printed = 'Y',UkeyPrint = '".$_SESSION['ukey']."', PrintDate=NOW() WHERE ID = ".$PrintU['ID'];
                    // echo $UpdateSQL;
                    MySQLUpdate($UpdateSQL);
                }
            }
?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="../../../../css/main/app.css" rel="stylesheet" />
                    <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
                    <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />

                    <title><?php echo $HeaderRST['DocNum']; ?></title>
                    <style rel="stylesheet" type="text/css">
                        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
                        html, body {
                            background-color: #FFFFFF;
                            font-family: 'Sarabun';
                            font-weight: 400;
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
                        .OrderList.table.border-dark tfoot
                         {
                            border-color: #212529 !important;
                        }
                        .OrderList.table.border-dark tbody tr td, 
                        .OrderList.table.border-dark tfoot tr th, 
                        .OrderList.table.border-dark tfoot tr td
                        {
                            border: 0px;
                        }
                        .OrderList.table.border-dark tfoot tr:last-child th, 
                        .OrderList.table.border-dark tfoot tr:last-child td
                        {
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
                    <script src="../../../../js/JsBarcode.all.min.js" type="text/javascript"></script>
                </head>
                <body>
                <?php for($p=1;$p<=$pages;$p++) {
                // for($p=1;$p==1;$p++) {
                    $offset = ($p-1)*$rowsperpage;
                ?>
                    <div class="page">
                        <!-- PAGE HEADER -->
                        <table class="table table-borderless table-sm" style="color: #000;">
                        <thead>
                            <tr>
                                <td width="17.5%" class="text-center">
                                    <img src="../../../../image/logo/kbi_logo.png" class="img-fluid" />
                                </td>
                                <td>
                                    <h4>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                                    <small>
                                        541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                        เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856
                                    </small>
                                </td>
                                <td width="15%" class="align-top text-right">
                                    หน้าที่ <?php echo $p; ?> จาก <?php echo $pages; ?><br/>
                                    <svg id="sobarcode"></svg>
                                </td>
                            </tr>
                        </thead>
                        </table>
                        <!-- ORDER HEADER -->
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <tr class="align-top">
                                <th width="15%">ชื่อลูกค้า:</th>
                                <td colspan="3"><?php echo conutf8($HeaderRST['CardCode']." ".$HeaderRST['CardName']); ?></td>
                                <th width="12.5%">เลขที่เอกสาร:</th>
                                <td width="15%"><?php echo $HeaderRST['DocNum']; ?></td>
                            </tr>
                            <tr class="align-top">
                                <th rowspan="2">ที่อยู่จัดส่ง</th>
                                <td class="align-top" rowspan="2"><?php echo conutf8($HeaderRST['Address2']); ?></td>
                                <th width="12.5%" rowspan="2">ที่อยู่เปิดบิล</th>
                                <td class="align-top" rowspan="2"><?php echo conutf8($HeaderRST['Address']); ?></td>
                                <th>วันที่ใบสั่งขาย:</th>
                                <td><?php echo date("d/m/Y",strtotime($HeaderRST['DocDate'])); ?></td>
                            </tr>
                            <tr class="align-top">
                                <th>วันที่กำหนดส่ง:</th>
                                <td><?php echo date("d/m/Y",strtotime($HeaderRST['DocDueDate'])); ?></td>
                            </tr>
                            <tr class="align-top">
                                <th>เลขที่ผู้เสียภาษี:</th>
                                <td><?php echo $HeaderRST['LicTradNum']; ?></td>
                                <th>พนักงานขาย:</th>
                                <td colspan="3"><?php echo conutf8($HeaderRST['SlpName']); ?></td>
                            </tr>
                            <tr class="align-top">
                                <th>อ้างอิง:</th>
                                <td colspan="3" style="font-weight: bold; color: #FF0000;"><?php echo conutf8($HeaderRST['U_PONo']); ?></td>
                                <th>เขตการขาย:</th>
                                <td><?php echo conutf8($HeaderRST['Name']); ?></td>
                            </tr>
                            <tr class="align-top">
                                <th>เงื่อนไขชำระเงิน:</th>
                                <td colspan="3"><?php echo conutf8($HeaderRST['U_ChqCond']); ?></td>
                                <th>เครดิต:</th>
                                <td><?php echo conutf8($HeaderRST['PymntGroup']); ?></td>
                            </tr>
                            <tr class="align-top">
                                <th>ขนส่งโดย:</th>
                                <td class="align-top"><?php echo conutf8($HeaderRST['U_Name']); ?></td>
                                <td class="align-top" colspan="2"><?php echo conutf8($HeaderRST['U_Address']); ?></td>
                                <td colspan="2">
                                    <?php echo conutf8($HeaderRST['CntctName']); ?><br/>
                                    <span style="color: #FF0000; font-size: 14px; font-weight: bold; text-decoration: underline;"><?php echo conutf8($HeaderRST['U_BillCond']); ?></span>
                                </td>
                            </tr>
                        </table>

                        <?php 
                        $DetailSQL = "SELECT TOP $rowsperpage T0.* FROM RDR1 T0 WHERE T0.DocEntry = $DocEntry AND T0.VisOrder >= $offset ORDER BY T0.VisOrder";
                        $DetailQRY = SAPSelect($DetailSQL);
                        $r = 0; ?>
                        <table class="table border-dark OrderList" style="color: #000;">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" width="3.5%">ลำดับ</th>
                                    <th scope="col">รายการ</th>
                                    <th scope="col" colspan="2">จำนวน</th>
                                    <th scope="col" width="10%">ราคา<br/>ต่อหน่วย</th>
                                    <th scope="col" width="15%">ส่วนลด</th>
                                    <th scope="col" width="12.5%">ราคารวม</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $no = 0; while($DetailRST = odbc_fetch_array($DetailQRY)) {
                                if($DetailRST['SubCatNum'] == "") { $CodeBars = $DetailRST['CodeBars']; } else { $CodeBars = $DetailRST['SubCatNum']; }
                                if($DetailRST['U_DiscP5'] != NULL AND $DetailRST['U_DiscP5'] != "" AND $DetailRST['U_DiscP5'] != 0.00) {
                                    $Discount = number_format($DetailRST['U_DiscP1'],2)."%+".number_format($DetailRST['U_DiscP2'],2)."%+".number_format($DetailRST['U_DiscP3'],2)."%+".number_format($DetailRST['U_DiscP4'],2)."%+".number_format($DetailRST['U_DiscP5'],2)."%";
                                } elseif($DetailRST['U_DiscP4'] != NULL AND $DetailRST['U_DiscP4'] != "" AND $DetailRST['U_DiscP4'] != 0.00) {
                                    $Discount = number_format($DetailRST['U_DiscP1'],2)."%+".number_format($DetailRST['U_DiscP2'],2)."%+".number_format($DetailRST['U_DiscP3'],2)."%+".number_format($DetailRST['U_DiscP4'],2)."%";
                                } elseif($DetailRST['U_DiscP3'] != NULL AND $DetailRST['U_DiscP3'] != "" AND $DetailRST['U_DiscP3'] != 0.00) {
                                    $Discount = number_format($DetailRST['U_DiscP1'],2)."%+".number_format($DetailRST['U_DiscP2'],2)."%+".number_format($DetailRST['U_DiscP3'],2)."%";
                                } elseif($DetailRST['U_DiscP2'] != NULL AND $DetailRST['U_DiscP2'] != "" AND $DetailRST['U_DiscP2'] != 0.00) {
                                    $Discount = number_format($DetailRST['U_DiscP1'],2)."%+".number_format($DetailRST['U_DiscP2'],2)."%";
                                } elseif($DetailRST['U_DiscP1'] != NULL AND $DetailRST['U_DiscP1'] != "" AND $DetailRST['U_DiscP1'] != 0.00) {
                                    $Discount = number_format($DetailRST['U_DiscP1'],2)."%";
                                } else {
                                    $Discount = NULL;
                                }
                            ?>
                                <tr class="align-top">
                                    <td class="text-right"><?php echo $DetailRST['VisOrder']+1; ?></td>
                                    <td><?php echo $DetailRST['ItemCode']." ".$CodeBars." ".conutf8($DetailRST['WhsCode'])." ".conutf8($DetailRST['Dscription']); ?></td>
                                    <td class="text-right" width="5%"><?php echo number_format($DetailRST['Quantity'],0); ?></td>
                                    <td width="5%"><?php echo conutf8($DetailRST['unitMsr']); ?></td>
                                    <td class="text-right"><?php echo number_format($DetailRST['PriceBefDi'],2); ?></td>
                                    <td class="text-center"><?php echo $Discount; ?></td>
                                    <td class="text-right" style="font-weight: bold;"><?php echo number_format($DetailRST['LineTotal'],2); ?></td>
                                </tr>
                            <?php $no++; }
                                $rows = $no;
                                for($b = 1; $b <= $rowsperpage-$rows; $b++) { ?>
                                <tr class="align-top">
                                    <td class="text-right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="text-right" width="5%">&nbsp;</td>
                                    <td width="5%">&nbsp;</td>
                                    <td class="text-right">&nbsp;</td>
                                    <td class="text-center">&nbsp;</td>
                                    <td class="text-right" style="font-weight: bold;">&nbsp;</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <?php
                                $SumBefVAT = NULL;
                                $VatSum    = NULL;
                                $DocTotal  = NULL;
                                if($p == $pages) {
                                    $SumBefVAT = number_format($HeaderRST['DocTotal']-$HeaderRST['VatSum'],2);
                                    $VatSum    = number_format($HeaderRST['VatSum'],2);
                                    $DocTotal  = number_format($HeaderRST['DocTotal'],2);
                                }
                            ?>
                                <tr>
                                    <th class="align-top" colspan="4" rowspan="2">หมายเหตุ: <span style="color: #FF0000;"><?php echo conutf8($HeaderRST['Comments']); ?></span></th>
                                    <th class="table-active text-right" colspan="2">ยอดรวมทุกรายการ:</th>
                                    <th class="text-right"><?php echo $SumBefVAT; ?></th>
                                </tr>
                                <tr>
                                    <th class="table-active text-right" colspan="2">ภาษีมูลค่าเพิ่ม:</th>
                                    <th class="text-right"><?php echo $VatSum; ?></th>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-center"><i>(<?php echo  numText(number_format($HeaderRST['DocTotal'],2)); ?>)</i></td>
                                    <th class="table-active text-right" colspan="2">จำนวนเงินรวมสุทธิ:</th>
                                    <th class="text-right"><?php echo $DocTotal; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                        <table class="table table-borderless border-dark table-sm" style="width: 75%;">
                            <tr>
                                <th width="17.5%">ผู้เปิดคำสั่งขาย:</th>
                                <td width="22.5%" style="border-bottom: 1px dotted #000;"><?php echo conutf8($HeaderRST['LastName']." ".$HeaderRST['FirstName']); ?></td>
                                <th width="17.5%">ข้อมูลจัดสรรงาน:</th>
                                <td style="border-bottom: 1px dotted #000;"><?php echo "ผู้เบิก: ".$PickName." / โต๊ะ: ".$PackName; ?></td>
                            </tr>
                            <tr>
                                <th>ผู้เปิดบิล:</th>
                                <td style="border-bottom: 1px dotted #000;"></td>
                                <th>เลขที่บิล:</th>
                                <td style="border-bottom: 1px dotted #000;">&nbsp;</td>
                            </tr>
                            <tr>
                                <th>ผู้จัดสินค้า:</th>
                                <td><span>&#9634;</span> ครบ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>&#9634;</span> ไม่ครบ</td>
                                <th>ลงชื่อ:</th>
                                <td style="border-bottom: 1px dotted #000;">&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                <?php } ?>
                <script type="text/javascript">
                    var docnum = '<?php echo $HeaderRST['DocNum']; ?>';
                    JsBarcode("#sobarcode", docnum, { width: 1.20, height: 24, fontSize: 10, marginTop: 0, marginBottom: 0, text: docnum });
                    window.print();
                </script>
                </body>
            </html>
<?php
        }
    }
}
?>
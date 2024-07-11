<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
    if(!isset($_GET['PickID'])) {
        $DocEntry = $_GET['DocEntry'];
        $DocType  = $_GET['Type'];

        switch($DocType) {
            case "OINV": $TableName = array("OINV","INV1"); break;
            case "ODLN": $TableName = array("ODLN","DLN1"); break;
        }
    } else {
        $PickID   = $_GET['PickID'];
        $ORSQL    = "SELECT T0.SODocEntry FROM picker_soheader T0 WHERE T0.ID = $PickID LIMIT 1";
        $ORRST    = MySQLSelect($ORSQL);
        $OREntry  = $ORRST['SODocEntry'];
        
        $GetIVSQL = "SELECT DISTINCT TOP 1 T0.TrgetEntry, T0.TargetType FROM RDR1 T0 WHERE T0.DocEntry = $OREntry ORDER BY T0.TrgetEntry DESC";
        // echo $GetIVSQL;
        $GetIVQRY = SAPSelect($GetIVSQL);
        $GetIVRST = odbc_fetch_array($GetIVQRY);

        $DocEntry  = $GetIVRST['TrgetEntry'];
        switch($GetIVRST['TargetType']) {
            case 13:
                $DocType   = "OINV";
                $TableName = array("OINV","INV1");
            break;
            case 15:
                $DocType   = "ODLN";
                $TableName = array("ODLN","DLN1");
            break;
        }

        if($DocEntry == "" || $DocEntry == NULL) {
            // echo "<script type='text/javascript'>alert(\"กรุณาสร้างเพิ่มบิลให้กับออเดอร์นี้ก่อนครับ\"); window.close();</script>";
            exit("<script type='text/javascript'>alert(\"กรุณาสร้างเพิ่มบิลให้กับออเดอร์นี้ก่อนครับ\"); window.close();</script>");
        }
    }

    $GetIVSQL = "SELECT TOP 1 T0.DocNum, T0.ShipToCode AS 'ShipName', T0.Address2 AS 'ShipAddress' FROM $TableName[0] T0 WHERE T0.DocEntry = $DocEntry";
    $GetIVQRY = SAPSelect($GetIVSQL);
    $GetIVRST = odbc_fetch_array($GetIVQRY);
    $DocNum   = $GetIVRST['DocNum'];
    $ShipName = conutf8($GetIVRST['ShipName']);
    $ShipAddress = conutf8($GetIVRST['ShipAddress']);
?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="../../../../css/main/app.css" rel="stylesheet" />
                    <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
                    <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />

                    <title><?php echo "DL-".$DocNum; ?></title>
                    <style rel="stylesheet" type="text/css">
                        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
                        html, body {
                            background-color: #FFFFFF;
                            font-family: 'Sarabun';
                            font-weight: 400;
                            color: #000 !important;
                            font-size: 12px;
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
                    <div class="page">
                        <h5 style="margin: 1rem;" class="text-center">ใบขนส่งสินค้า<br/><small>(Delivery Order)</small></h5>
                        <p class="text-danger text-center"><strong>*** เอกสารนี้เป็นเอกสารแจ้งรายละเอียดขนส่งสินค้า มิใช่ใบเสร็จหรือใบกำกับภาษี ***</strong></p>
                        <table class="table table-borderless table-sm border-dark" style="color: #000;">
                            <thead>
                                <tr>
                                    <th width="15%">เลขที่จัดส่ง</th>
                                    <td><?php echo "DL-".$DocNum; ?></td>
                                </tr>
                                <tr>
                                    <th>ชื่อลูกค้า</th>
                                    <td><?php echo $ShipName; ?></td>
                                </tr>
                                <tr>
                                    <th>ที่อยู่จัดส่ง</th>
                                    <td><?php echo $ShipAddress; ?></td>
                                </tr>
                            </thead>
                        </table>
                    <?php
                        $BODYSQL = "SELECT T0.VisOrder+1 AS 'No', T0.ItemCode, T0.CodeBars, T0.SubCatNum, T0.Dscription, T0.Quantity, T0.UnitMsr FROM $TableName[1] T0 WHERE T0.DocEntry = $DocEntry ORDER BY T0.VisOrder ASC";
                        $BODYQRY = SAPSelect($BODYSQL);
                    ?>
                        <table class="table border-dark OrderList" style="color: #000; border-bottom: 1px solid #000;">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" width="5%">ลำดับ</th>
                                    <th scope="col" width="15%">รหัสสินค้า</th>
                                    <th scope="col">ชื่อสินค้า</th>
                                    <th scope="col" colspan="2">จำนวน</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                while($BODYRST = odbc_fetch_array($BODYQRY)) {
                                    if($BODYRST['SubCatNum'] != "") {
                                        $BarCode = $BODYRST['SubCatNum'];
                                    } elseif($BODYRST['CodeBars'] != "") {
                                        $BarCode = $BODYRST['CodeBars'];
                                    } else {
                                        $BarCode = $BODYRST['ItemCode'];
                                    }?>
                                <tr>
                                    <td class="text-right"><?php echo number_format($BODYRST['No'],0); ?></td>
                                    <td class="text-center"><?php echo conutf8($BarCode); ?></td>
                                    <td><?php echo conutf8($BODYRST['Dscription']); ?></td>
                                    <td width="10%" class="text-right"><?php echo number_format($BODYRST['Quantity'],0); ?></td>
                                    <td width="10%"><?php echo conutf8($BODYRST['UnitMsr']); ?></td>
                                </tr>
                                <?php }
                            ?>
                            </tbody>
                        </table>

                        <hr/>

                        <table class="table table-borderless border-dark table-sm mt-4" style="width: 50%;">
                            <tr>
                                <th width="25%">ผู้ส่งสินค้า:</th>
                                <td width="25%" style="border-bottom: 1px dotted #000;"></td>
                                <th class="text-center" width="15%">วันที่:</th>
                                <td style="border-bottom: 1px dotted #000;"></td>
                            </tr>
                            <tr>
                                <th>ผู้รับสินค้า:</th>
                                <td style="border-bottom: 1px dotted #000;"></td>
                                <th class="text-center">วันที่:</th>
                                <td style="border-bottom: 1px dotted #000;"></td>
                            </tr>
                        </table>
                    </div>
                <script type="text/javascript">window.print();</script>
                </body>
            </html>
        <?php 
    } ?>
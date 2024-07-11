<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
    echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
    exit;
} else {
    if(!isset($_GET['cardcode']) && !isset($_GET['itemcode'])) {
        echo '<script type="text/javascript">window.location="../../../../"; </script>';
        exit;
    } else {
        $CardCode  = $_GET['cardcode'];
        $ItemCode  = $_GET['itemcode'];

        $SQL_IName = "SELECT T0.ItemName FROM OITM T0 WHERE T0.ItemCode = '$ItemCode'";
        $QRY_IName = SAPSelect($SQL_IName);
        $IName     = odbc_fetch_array($QRY_IName);

        $SQL_CName = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE T0.CardCode = '$CardCode'";
        $QRY_CName = SAPSelect($SQL_CName);
        $CName     = odbc_fetch_array($QRY_CName);


        $SQL = "SELECT T2.BeginStr, T1.DocNum, T1.NumAtCard, T1.DocEntry, T1.DocDate, T1.CardCode, T1.CardName, T0.ItemCode, T0.Dscription, 
                    T0.Quantity, T3.SalUnitMsr, T0.PriceBefDi, T0.U_DiscP1, T0.U_DiscP2, T0.U_DiscP3, T0.U_DiscP4, T0.LineTotal
                FROM INV1 T0
                LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
                LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
                LEFT JOIN OITM T3 ON T3.ItemCode = T0.ItemCode
                WHERE T1.CardCode = '$CardCode' AND T0.ItemCode = '$ItemCode'
                ORDER BY T1.DocDate DESC";
                // echo $SQL;
        $no = 0;
        $U_Disc = "";
        $Tbody = array();
        if(ChkRowSAP($SQL) > 0) {
            $QRY = SAPSelect($SQL);
            while($result = odbc_fetch_array($QRY)) {
                $no++;
                $Tbody[$no]['no'] = $no;                                                              // ลำดับ
                $Tbody[$no]['DocDate'] = date("d/m/Y", strtotime($result['DocDate']));                // วันที่
                if ($result['NumAtCard'] == '') {                                                     // เลขที่เวลา
                    $Tbody[$no]['DocNum'] = $result['BeginStr'].$result['DocNum'];
                }else{
                    $Tbody[$no]['DocNum'] = $result['NumAtCard'];
                }
                $Tbody[$no]['CardName'] = $result['CardCode']." - ".conutf8($result['CardName']);     // ชื่อร้านค้า
                $Tbody[$no]['ItemCode'] = $result['ItemCode']." - ".conutf8($result['Dscription']);   // รายการสินค้า
                $Tbody[$no]['Quantity'] = number_format($result['Quantity'],0);                       // จำนวนสินค้า
                $Tbody[$no]['Unit']     = conutf8($result['SalUnitMsr']);							  // หน่วย		
                $Tbody[$no]['Price']    = number_format($result['PriceBefDi'],2);					  // ราคา (ก่อน VAT)
                if($result['U_DiscP1'] > 0 && $result['U_DiscP1'] != "") {
                    $U_Disc .= number_format($result['U_DiscP1'],0)."+";
                }elseif($result['U_DiscP2'] > 0 && $result['U_DiscP2'] != ""){
                    $U_Disc .= number_format($result['U_DiscP2'],0)."+";
                }elseif($result['U_DiscP3'] > 0 && $result['U_DiscP3'] != ""){
                    $U_Disc .= number_format($result['U_DiscP3'],0)."+";
                }elseif($result['U_DiscP4'] > 0 && $result['U_DiscP4'] != ""){
                    $U_Disc .= number_format($result['U_DiscP4'],0)."+";
                }else{
                    $U_Disc .= "0+";
                }
                $Tbody[$no]['U_Disc'] = substr($U_Disc,0,-1)."%";									  // ส่วนลด
                $Tbody[$no]['LineTotal']  = number_format($result['LineTotal'],2);                    // รวม
                $U_Disc = "";
            }
        }

        if(ChkRowSAP8($SQL) > 0) {
            $QRY = conSAP8($SQL);
            while($result = odbc_fetch_array($QRY)) {
                $no++;
                $Tbody[$no]['no'] = $no;                                                              // ลำดับ
                $Tbody[$no]['DocDate'] = date("d/m/Y", strtotime($result['DocDate']));                // วันที่
                if ($result['NumAtCard'] == '') {                                                     // เลขที่เวลา
                    $Tbody[$no]['DocNum'] = $result['BeginStr'].$result['DocNum'];
                }else{
                    $Tbody[$no]['DocNum'] = $result['NumAtCard'];
                }
                $Tbody[$no]['CardName'] = $result['CardCode']." - ".conutf8($result['CardName']);     // ชื่อร้านค้า
                $Tbody[$no]['ItemCode'] = $result['ItemCode']." - ".conutf8($result['Dscription']);   // รายการสินค้า
                $Tbody[$no]['Quantity'] = number_format($result['Quantity'],0);                       // จำนวนสินค้า
                $Tbody[$no]['Unit']     = conutf8($result['SalUnitMsr']);							  // หน่วย		
                $Tbody[$no]['Price']    = number_format($result['PriceBefDi'],2);					  // ราคา (ก่อน VAT)
                if($result['U_DiscP1'] > 0 && $result['U_DiscP1'] != "") {
                    $U_Disc .= number_format($result['U_DiscP1'],0)."+";
                }elseif($result['U_DiscP2'] > 0 && $result['U_DiscP2'] != ""){
                    $U_Disc .= number_format($result['U_DiscP2'],0)."+";
                }elseif($result['U_DiscP3'] > 0 && $result['U_DiscP3'] != ""){
                    $U_Disc .= number_format($result['U_DiscP3'],0)."+";
                }elseif($result['U_DiscP4'] > 0 && $result['U_DiscP4'] != ""){
                    $U_Disc .= number_format($result['U_DiscP4'],0)."+";
                }else{
                    $U_Disc .= "0+";
                }
                $Tbody[$no]['U_Disc'] = substr($U_Disc,0,-1)."%";									  // ส่วนลด
                $Tbody[$no]['LineTotal']  = number_format($result['LineTotal'],2);                    // รวม
                $U_Disc = "";
            }
        }
        $Row = $no;
        $rowsperpage = 20;
        $pages = ceil($Row/$rowsperpage);
        $r = 1;
        ?>

        <!-- HTML -->
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="../../../../css/main/app.css" rel="stylesheet" />
                <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
                <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />

                <title><?php echo $CardCode; ?></title>
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
            </head>
            <body>
                <?php $offset = 0;
                for($p = 1; $p <= $pages ;$p++) { 
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
                                    หน้าที่ <?php echo $p; ?> จาก <?php echo $pages; ?>
                                </td>
                            </tr>
                        </thead>
                    </table>

                    <div class='d-flex justify-content-center pt-2 pb-2'>
                        <h4>ประวัติการขาย แยกตามสินค้า</h4>
                    </div>

                    <!-- ORDER HEADER -->
                    <table class="table table-borderless table-sm" style="color: #000;">
                        <tr class="align-top">
                            <th width="10%">รหัสลูกค้า</th>
                            <td colspan="3"><?php echo $CardCode; ?></td>
                            <th width="12.5%">ชื่อลูกค้า</th>
                            <td><?php echo conutf8($CName['CardName']); ?></td>
                            <th width="12.5%">ผู้จัดทำ</th>
                            <td width="15%"><?php echo $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")"; ?></td>
                        </tr>
                        <tr class="align-top">
                            <th width="10%">รหัสสินค้า</th>
                            <td colspan="3"><?php echo $ItemCode; ?></td>
                            <th width="12.5%">ชื่อสินค้า</th>
                            <td><?php echo conutf8($IName['ItemName']); ?></td>
                            <th width="12.5%">วันที่จัดทำ</th>
                            <td width="15%"><?php echo date("d")." ".FullMonth(date("m"))." ".date("Y"); ?></td>
                        </tr>
                    </table>

                    <!-- ORDER DETAIL -->
                    <table class="table border-dark OrderList" style="color: #000;">
                        <thead class="text-center">
                            <tr>
                                <th width='5%'>ลำดับ</th>
                                <th width='7.5%'>วันที่</th>
                                <th width='12.5%'>เลขที่เอกสาร</th>
                                <th width=''>รายการสินค้า</th>
                                <th width='5%'>จำนวน</th>
                                <th width='5%'>หน่วย</th>
                                <th width='10%'>ราคา<br>(ก่อน VAT)</th>
                                <th width=''>ส่วนลด</th>
                                <th width='12.5%'>รวมทั้งหมด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($r <= $Row) {
                                for($i = 1; $i <= 30; $i++) {
                                    if($r <= $Row) {
                                    echo"<tr>
                                            <td class='text-center'>".$Tbody[$r]['no']."</td>
                                            <td class='text-center'>".$Tbody[$r]['DocDate']."</td>
                                            <td class='text-center'>".$Tbody[$r]['DocNum']."</td>
                                            <td class=''>".$Tbody[$r]['ItemCode']."</td>
                                            <td class='text-right'>".$Tbody[$r]['Quantity']."</td>
                                            <td class='text-center'>".$Tbody[$r]['Unit']."</td>
                                            <td class='text-right'>".$Tbody[$r]['Price']."</td>
                                            <td class='text-right'>".$Tbody[$r]['U_Disc']."</td>
                                            <td class='text-right'>".$Tbody[$r]['LineTotal']."</td>
                                        </tr>";
                                    $r++;
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
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
?>
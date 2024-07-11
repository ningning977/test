<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
    echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
    exit;
} else {
    if(!isset($_GET['docentry'])) {
        echo '<script type="text/javascript">window.location="../../../../"; </script>';
        exit;
    } else {
        $DocEntry  = $_GET['docentry'];
        $CardCode  = $_GET['cardcode'];
        $StartDate = $_GET['startdate'];
        $EndDate   = $_GET['enddate']; 

        $SQL_HEAD ="SELECT T0.CardCode, T0.CardName, T1.Beginstr, T0.NumAtCard, T1.Beginstr, T0.DocNum,
                        T3.LicTradNum, T0.DocDate, T0.Address, T0.DocDueDate, T2.SlpName, T4.PymntGroup,
                        T0.Comments, T0.DocTotal, T0.VatSum
                    FROM OINV T0
                    LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
                    LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
                    LEFT JOIN OCRD T3 ON T0.CardCode = T3.CardCode
                    LEFT JOIN OCTG T4 ON T3.GroupNum = T4.GroupNum 
                    WHERE T0.DocEntry = '$DocEntry'";
        
        if(intval(substr($EndDate,0,4)) <= 2022) {
            $QRY_HEAD = conSAP8($SQL_HEAD);
        }else{
            $QRY_HEAD = SAPSelect($SQL_HEAD);
        }
        $result_HEAD = odbc_fetch_array($QRY_HEAD);
        if ($result_HEAD['NumAtCard'] != ''){
            $NumAtCard = $result_HEAD['NumAtCard'];
        }else{
            $NumAtCard = $result_HEAD['Beginstr'].$result_HEAD['DocNum'];
        }

        // Detail
        $SQL = "SELECT T0.NumAtCard, T3.Beginstr, T0.DocNum, T0.DocDate, T0.DocDueDate, T4.SlpName, T0.U_PONo,
				T1.ItemCode, T2.U_ProductStatus, T1.Dscription, T1.Quantity, T1.unitMsr, T1.PriceBefDi, T1.LineTotal, 
				T1.U_DiscP1, T1.U_DiscP2, T1.U_DiscP3, T1.U_DiscP4, T1.U_DiscP5,
				T0.DocTotal, T0.VatSum, T0.DocEntry
			FROM OINV T0 
			LEFT JOIN INV1 T1 ON T0.DocEntry = T1.DocEntry 
			LEFT JOIN OITM T2 ON T1.ItemCode = T2.ItemCode 
			LEFT JOIN NNM1 T3 ON T0.Series = T3.Series 
			LEFT JOIN OSLP T4 ON T0.SlpCode = T4.SlpCode 
			WHERE T0.CardCode = '$CardCode' AND T0.DocDate BETWEEN '$StartDate' AND '$EndDate' AND T0.DocEntry = '$DocEntry'
			ORDER BY T0.DocDate DESC,T0.DocNum DESC,T0.CardCode";
        
        if(intval(substr($EndDate,0,4)) <= 2022) {
            $QRY = conSAP8($SQL);
        }else{
            $QRY = SAPSelect($SQL);
        }
        $Tbody = array();
        $r = 0;
        while($result = odbc_fetch_array($QRY)) {
            if($r == 0) {
                if ($result['NumAtCard'] != ''){
                    $Tbody['NumAtCard'][$r] = $result['NumAtCard'];
                }else{
                    $Tbody['NumAtCard'][$r] = $result['Beginstr'].$result['DocNum'];
                }
                $Tbody['DocDate'][$r]    = date("d/m/Y",strtotime($result['DocDate']));
                $Tbody['DocDueDate'][$r] = date("d/m/Y",strtotime($result['DocDueDate']));
                $Tbody['SlpName'][$r]    = conutf8($result['SlpName']);
                $Tbody['U_PONo'][$r]     = $result['U_PONo'];

                $Tbody['SumNoVat'][$r] = number_format(($result['DocTotal']-$result['VatSum']),2);
                $Tbody['VatSum'][$r]   = number_format($result['VatSum'],2);
                $Tbody['DocTotal'][$r] = number_format($result['DocTotal'],2);
            }
            $Tbody['ItemCode'][$r]   = $result['ItemCode'];
            $Tbody['Status'][$r]     = $result['U_ProductStatus'];
            $Tbody['Dscription'][$r] = conutf8($result['Dscription']);
            $Tbody['Quantity'][$r]   = number_format($result['Quantity'],0);
            $Tbody['unitMsr'][$r]    = conutf8($result['unitMsr']);
            $Tbody['PriceBefDi'][$r] = number_format($result['PriceBefDi'],3);
            if(0 < $result['U_DiscP5']){
                $Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'],2)."%+".number_format($result['U_DiscP2'],2)."%+".number_format($result['U_DiscP3'],2)."%+".number_format($result['U_DiscP4'])."%+".number_format($result['U_DiscP5'])."%";
            }else{
                if(0 < $result['U_DiscP4']){
                    $Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'])."%+".number_format($result['U_DiscP2'])."%+".number_format($result['U_DiscP3'])."%+".number_format($result['U_DiscP4'])."%";
                }else{
                    if(0 < $result['U_DiscP3']) {
                        $Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'],2)."%+".number_format($result['U_DiscP2'],2)."%+".number_format($result['U_DiscP3'],2)."%";
                    }else{
                        if(0 < $result['U_DiscP2']) {
                            $Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'],2)."%+".number_format($result['U_DiscP2'],2)."%";
                        }else{
                            if(0 < $result['U_DiscP1']) {
                                $Tbody['U_Disc'][$r] = number_format($result['U_DiscP1'],2)."%";
                            }else{
                                $Tbody['U_Disc'][$r] = "0.00%";
                            }
                        }
                    }
                }
            }
            $Tbody['LineTotal'][$r] = number_format($result['LineTotal'],3);
            $r++;
        }
        $Row = ($r);
        $num = 1;
        $Data = 0;
        $rowsperpage = 20;
        $pages = ceil($Row/$rowsperpage);
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

                <title><?php echo $NumAtCard; ?></title>
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
                        <h4>สำเนาใบกำกับภาษี (ใช้ภายในบริษัท)</h4>
                    </div>

                    <!-- ORDER HEADER -->
                    <table class="table table-borderless table-sm" style="color: #000;">
                        <tr class="align-top">
                            <th width="15%">ลูกค้า</th>
                            <td colspan="3"><?php echo $result_HEAD['CardCode']." - ".conutf8($result_HEAD['CardName']); ?></td>
                            <th width="12.5%">เลขที่ใบกำกับภาษี</th>
                            <td width="15%"><?php echo $NumAtCard; ?></td>
                        </tr>
                        <tr class="align-top">
                            <th width="15%">เลขที่ผู้เสียภาษี</th>
                            <td colspan="3"><?php echo $result_HEAD['LicTradNum']; ?></td>
                            <th width="12.5%">วันที่ใบสั่งขาย</th>
                            <td width="15%"><?php echo date("d/m/Y", strtotime($result_HEAD['DocDate'])); ?></td>
                        </tr>
                        <tr class="align-top">
                            <th width="15%">ที่อยู่เปิดบิล</th>
                            <td colspan="3"><?php echo conutf8($result_HEAD['Address']); ?></td>
                            <th width="12.5%">วันที่กำหนดชำระ</th>
                            <td width="15%"><?php echo date("d/m/Y", strtotime($result_HEAD['DocDueDate'])); ?></td>
                        </tr>
                        <tr class="align-top">
                            <th width="15%">พนักงานขาย</th>
                            <td colspan="3"><?php echo conutf8($result_HEAD['SlpName']); ?></td>
                            <th width="12.5%">เครดิต</th>
                            <td width="15%"><?php echo conutf8($result_HEAD['PymntGroup']); ?></td>
                        </tr>
                    </table>

                    <!-- ORDER DETAIL -->
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
                            <?php 
                            $SQL_ROW = "SELECT TOP $rowsperpage T0.VisOrder , T0.* FROM INV1 T0 WHERE T0.DocEntry = $DocEntry AND T0.VisOrder >= $offset ORDER BY T0.VisOrder";
                            if(intval(substr($EndDate,0,4)) <= 2022) {
                                $QRY_ROW = conSAP8($SQL_ROW);
                            }else{
                                $QRY_ROW = SAPSelect($SQL_ROW);
                            }
                            $ChRow = 0;
                            while($reRow = odbc_fetch_array($QRY_ROW)) {
                                $ChRow++;
                                // for($i = 0; $i <= $Row; $i++) {
                                ?>
                                <tr class="align-top">
                                    <td class="text-right"><?php echo $num; ?></td>
                                    <td><?php echo $Tbody['ItemCode'][$reRow['VisOrder']]." - ".$Tbody['Dscription'][$reRow['VisOrder']]; ?></td>
                                    <td class="text-right" width="5%"><?php echo $Tbody['Quantity'][$reRow['VisOrder']]; ?></td>
                                    <td width="5%"><?php echo $Tbody['unitMsr'][$reRow['VisOrder']]; ?></td>
                                    <td class="text-right"><?php echo $Tbody['PriceBefDi'][$reRow['VisOrder']]; ?></td>
                                    <td class="text-center"><?php echo $Tbody['U_Disc'][$reRow['VisOrder']]; ?></td>
                                    <td class="text-right" style="font-weight: bold;"><?php echo $Tbody['LineTotal'][$reRow['VisOrder']]; ?></td>
                                </tr>
                                <?php 
                                $num++; 
                            } ?>

                            <?php
                            for($b = 1; $b <= $rowsperpage-$ChRow; $b++) { ?>
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
                            <tr>
                                <th class="align-top" colspan="4" rowspan="2">หมายเหตุ: <span style="color: #FF0000;"><?php echo conutf8($result_HEAD['Comments']); ?></span></th>
                                <th class="table-active text-right" colspan="2">ยอดรวมทุกรายการ:</th>
                                <th class="text-right"><?php echo number_format($result_HEAD['DocTotal']-$result_HEAD['VatSum'],3); ?></th>
                            </tr>
                            <tr>
                                <th class="table-active text-right" colspan="2">ภาษีมูลค่าเพิ่ม:</th>
                                <th class="text-right"><?php echo number_format($result_HEAD['VatSum'],3); ?></th>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-center"><i>(<?php echo  numText(number_format($result_HEAD['DocTotal'],3)); ?>)</i></td>
                                <th class="table-active text-right" colspan="2">จำนวนเงินรวมสุทธิ:</th>
                                <th class="text-right"><?php echo number_format($result_HEAD['DocTotal'],3); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class='d-flex justify-content-center pt-3 pb-3'>
                        <span>*** เอกสารสำเนาใช้แนบประกอบภายในบริษัทเท่านั้น ***</span>
                    </div>
                    <div class='d-flex'>
                        <span>ผู้จัดทำ : <?php echo $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")"; ?></span>
                    </div>
                    <div class='d-flex'>
                        <span>จัดทำเมื่อ : <?php echo date("d")." ".FullMonth(date("m"))." ".date("Y"); ?></span>
                    </div>
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
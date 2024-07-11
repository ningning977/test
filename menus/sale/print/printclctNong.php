<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
        $ukey = $_GET['u'];
        // switch($ukey) {
        //     case "B60": $PrintName = "ซ่อมสินค้าหน้าร้าน"; break;
        //     default:
        //         $PrintName = $ukey;
        //         break;
            
        // }
        if($ukey == "fbf63a6d36d54f90d7e36f6656c3b34c") { /* P'Nong */
            $filter1 = "<= 2022";
        } else {
            $filter1 = ">= 2023";
        }

        $FixSale = "";
        if($ukey == '959605247a4439da286b006f1445e867') { /* P'เอก */
            $SlpCode = MySQLSelect("SELECT GROUP_CONCAT(SlpCode) AS SlpCode FROM OSLP WHERE Ukey IN ('8c015c0e3c378ae33009fbdd3754ec42','c44d565cccf48e7ffc998fadcdd6b521','792de4151e0bb0237c0578617981c5a7','1c865f0ae09752a60431843b40ff946d')");
            $FixSale = "AND T0.SlpCode NOT IN (".$SlpCode['SlpCode'].")";
        } 
        $Rows = 1;
        if($Rows > 0) { ?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="../../../../css/main/app.css" rel="stylesheet" />
                    <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
                    <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />

                    <title>รายงานค่าปรับหนี้เกินกำหนด</title>
                    <style rel="stylesheet" type="text/css">
                        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
                        html, body {
                            background-color: #FFFFFF;
                            font-family: 'Sarabun';
                            font-weight: 200;
                            color: #000 !important;
                            font-size: 9px;
                        }

                        h1,h2,h3,h4,h5,h6 {
                            color: #000;
                            padding: 0;
                            margin: 0;
                            font-weight: 600;
                        }
                        .table-bordered.border-dark tbody,
                        .OrderList.table.border-dark tbody {
                            border-color: #212529 !important;
                        }
                        .OrderList.table.border-dark tbody tr:last-child th {
                            border-bottom: 3px double #212529 !important;
                        }

                        .page {
                            /* margin: 3mm;
                            width: 204mm;
                            height: 291mm; */
                            /* border: 1px dashed #000; */
                            width: 297mm;
                            /* height: 210mm; */
                            display: block;
                            margin: 3mm auto;
                            padding: 3mm;
                            box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
                        }
                        @page {
                            size: A4 landscape;
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
                    <div class="page">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <td>
                                    <!-- PAGE HEADER -->
                                    <table class="table table-borderless table-sm" style="color: #000;">
                                        <thead>
                                            <tr>
                                                <td width="12.5%" class="text-center">
                                                    <img src="../../../../image/logo/kbi_logo.png" class="img-fluid" />
                                                </td>
                                                <td>
                                                    <h4>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                                                    <small>
                                                        541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                                        เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856
                                                    </small>
                                                </td>
                                                <td width="15%" class="align-top text-right">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-center"><h5 style="margin: 1rem;">รายงานค่าปรับหนี้เกินกำหนด</h5></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <strong>พนักงานขาย:</strong> ยุทธนา พุ่มจีน<br/>
                                                    <strong>วันที่จัดทำรายงาน:</strong> <?php echo date("d/m/Y")." เวลา ".date("H:i")." น."; ?>
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <table class="table table-bordered table-sm OrderList border-dark" style="color: #000;">
                                        <thead class="text-center">
                                            <tr>
                                                <th colspan="17">ชื่อลูกค้า</th>
                                            </tr>
                                            <tr>
                                                <th width="7.25%" rowspan="2">เลขที<br/>เอกสาร</th>
                                                <th width="7.5%" rowspan="2">BP Ref. No.</th>
                                                <th width="6.5%" rowspan="2">วันที่<br/>เอกสาร</th>
                                                <th width="6.5%" rowspan="2">กำหนด<br/>ชำระ</th>
                                                <th width="6.5%" rowspan="2">มูลค่าสุทธิ<br/>(บาท)</th>
                                                <th width="6.5%" rowspan="2">ชำระแล้ว<br/>(บาท)</th>
                                                <th width="6.5%" rowspan="2">ค้างชำระ<br/>(บาท)</th>
                                                <th colspan="5">ยอดเกินชำระ (วัน)</th>
                                                <th rowspan="2">จำนวนวัน<br/>เกินกำหนด</th>
                                                <th colspan="4">ค่าปรับ (บาท)</th>
                                            </tr>
                                            <tr>
                                                <td width="5.75%">0 - 30</td>
                                                <td width="5.75%">31 - 60</td>
                                                <td width="5.75%">61 - 90</td>
                                                <td width="5.75%">91 - 120</td>
                                                <td width="5.75%">121+</td>
                                                <td width="4.75%">ยอดปรับ</td>
                                                <td width="4.75%">SALE</td>
                                                <td width="4.75%">SUP.</td>
                                                <td width="4.75%">MGR.</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            switch($ukey) {
                                                case "B60": $RptWhr = " (T0.SlpCode = 251)"; break;
                                                default: $RptWhr = " (T1.Memo = '$ukey' OR (T0.SlpCode IN (251,291,296) AND T3.Memo = '$ukey'))"; break;
                                            }
                                            $ReportSQL = "SELECT
                                                            'OINV' AS 'DocType', T0.DocEntry, T0.CardCode, T0.CardName, T0.DocNum, T0.NumAtCard, T0.DocDate, T0.DocDueDate,
                                                            T0.DocTotal, T0.PaidToDate, (T0.DocTotal-T0.PaidToDate) AS 'NoPaid',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) <= 30 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B30D',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 31 AND 60 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B60D',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 61 AND 90 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B90D',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 91 AND 120 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B120D',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) >= 121 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'A120D',
                                                            DATEDIFF(day,T0.DocDueDate,GETDATE()-30) AS 'DueType',
                                                            CASE WHEN T0.SlpCode IN (251,291,296) THEN 0 ELSE 1 END AS 'Fine'
                                                        FROM OINV T0
                                                        LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                                                        LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode
                                                        LEFT JOIN OSLP T3 ON T2.SlpCode = T3.SlpCode
                                                        WHERE
                                                            ((MONTH(T0.DocDueDate) < MONTH(GETDATE()) AND YEAR(T0.DocDueDate) = YEAR(GETDATE())) OR (YEAR(T0.DocDueDate) < YEAR(GETDATE()))) AND 
                                                            (T0.DocStatus = 'O' AND (T0.DocTotal-T0.PaidToDate) > 0) AND T1.U_Dim1 = 'TT2' AND YEAR(T0.DocDate) $filter1
                                                            $FixSale
                                                        UNION ALL
                                                        SELECT
                                                            'ORIN' AS 'DocType', T0.DocEntry, T0.CardCode, T0.CardName, T0.DocNum, T0.NumAtCard, T0.DocDate, T0.DocDueDate,
                                                            -T0.DocTotal, -T0.PaidToDate, -(T0.DocTotal-T0.PaidToDate) AS 'NoPaid',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) <= 30 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B30D',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 31 AND 60 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B60D',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 61 AND 90 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B90D',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 91 AND 120 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B120D',
                                                            CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) >= 121 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'A120D',
                                                            DATEDIFF(day,T0.DocDueDate,GETDATE()-30) AS 'DueType',
                                                            0 AS 'Fine'
                                                        FROM ORIN T0
                                                        LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                                                        LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode
                                                        LEFT JOIN OSLP T3 ON T2.SlpCode = T3.SlpCode
                                                        LEFT JOIN NNM1 T4 ON T0.Series = T4.Series
                                                        WHERE
                                                            ((MONTH(T0.DocDueDate) < MONTH(GETDATE()) AND YEAR(T0.DocDueDate) = YEAR(GETDATE())) OR (YEAR(T0.DocDueDate) < YEAR(GETDATE()))) AND T4.BeginStr IN ('S1-','SR-') AND
                                                            (T0.DocStatus = 'O' AND (T0.DocTotal-T0.PaidToDate) > 0) AND T1.U_Dim1 = 'TT2' AND YEAR(T0.DocDate) $filter1
                                                            $FixSale
                                                        ORDER BY T0.CardCode, T0.DocDate";
                                            // echo $ReportSQL;
                                            $ReportQRY = SAPSelect($ReportSQL);
                                            $tmpCode = "";
                                            $SumDocTotal   = 0;
                                            $SumPaidtoDate = 0;
                                            $SumNoPaid     = 0;
                                            $SumB30D       = 0;
                                            $SumB60D       = 0;
                                            $SumB90D       = 0;
                                            $SumB120D      = 0;
                                            $SumA120D      = 0;
                                            $SumFine       = 0;
                                            $SumFineSAL    = 0;
                                            $SumFineSUP    = 0;
                                            $SumFineMGR    = 0;
                                            $row = 0;
                                            while($ReportRST = odbc_fetch_array($ReportQRY)) {
                                                $Fine = 0;
                                                $row++;
                                                if($ReportRST['CardCode'] != $tmpCode) {
                                                    echo "<tr><td colspan='17'><strong>".conutf8($ReportRST['CardCode']." | ".$ReportRST['CardName'])."</strong></td></tr>";
                                                    $tmpCode = $ReportRST['CardCode'];
                                                }
                                                if($ReportRST['DueType'] >= 91 ) { $FineRate = 0.03; }
                                                elseif($ReportRST['DueType'] >= 61 && $ReportRST['DueType'] <= 90) { $FineRate = 0.01; }
                                                elseif($ReportRST['DueType'] >= 31 && $ReportRST['DueType'] <= 60) { $FineRate = 0.005; }
                                                else { $FineRate = 0; }
                                                // echo $ReportRST['NoPaid']*$FineRate."<br/>";

                                                if($ReportRST['DocType'] == "ORIN") {
                                                    $FineRate = 0;
                                                }

                                                if($FineRate == 0 || $ReportRST['DueType'] <= 30 || $ReportRST['Fine'] == 0) {
                                                    $Fine = null;
                                                    $FineSAL = null;
                                                    $FineSUP = null;
                                                    $FineMGR = null;
                                                    $txtFineSAL = null;
                                                    $txtFineSUP = null;
                                                    $txtFineMGR = null;
                                                } else {
                                                    $Fine = number_format($ReportRST['NoPaid']*$FineRate,2);

                                                    if($ukey == "B60") {
                                                        $FineSAL = NULL;
                                                        $FineSUP = ($ReportRST['NoPaid']*$FineRate)*0.5;
                                                        $FineMGR = ($ReportRST['NoPaid']*$FineRate)*0.5;
                                                    } else {
                                                        $FineSAL = ($ReportRST['NoPaid']*$FineRate)*0.7;
                                                        $FineSUP = ($ReportRST['NoPaid']*$FineRate)*0.2;
                                                        $FineMGR = ($ReportRST['NoPaid']*$FineRate)*0.1;
                                                    }
                                                    $SumFine       = $SumFine+($ReportRST['NoPaid']*$FineRate);
                                                    $SumFineSAL    = $SumFineSAL+$FineSAL;
                                                    $SumFineSUP    = $SumFineSUP+$FineSUP;
                                                    $SumFineMGR    = $SumFineMGR+$FineMGR;
                                                    if($FineSAL == NULL) { $txtFineSAL = NULL; } else { $txtFineSAL = number_format($FineSAL,2); }
                                                    if($FineSUP == NULL) { $txtFineSUP = NULL; } else { $txtFineSUP = number_format($FineSUP,2); }
                                                    if($FineMGR == NULL) { $txtFineMGR = NULL; } else { $txtFineMGR = number_format($FineMGR,2); }
                                                }
                                                
                                                

                                                $B30D = NULL;
                                                $B60D = NULL;
                                                $B90D = NULL;
                                                $B120D = NULL;
                                                $A120D = NULL;
                                                if($ReportRST['B30D'] != NULL) { $B30D = number_format($ReportRST['B30D'],2); }
                                                if($ReportRST['B60D'] != NULL) { $B60D = number_format($ReportRST['B60D'],2); }
                                                if($ReportRST['B90D'] != NULL) { $B90D = number_format($ReportRST['B90D'],2); }
                                                if($ReportRST['B120D'] != NULL) { $B120D = number_format($ReportRST['B120D'],2); }
                                                if($ReportRST['A120D'] != NULL) { $A120D = number_format($ReportRST['A120D'],2); }
                                                if($ReportRST['DueType'] > 0) { $DueType = "+".number_format($ReportRST['DueType'],0); } else { $DueType = "-"; }
                                                echo "<tr>";
                                                    echo "<td class='text-center'>".$ReportRST['DocNum']."</td>";
                                                    echo "<td class='text-center'>".$ReportRST['NumAtCard']."</td>";
                                                    echo "<td class='text-center'>".date("d/m/Y",strtotime($ReportRST['DocDate']))."</td>";
                                                    echo "<td class='text-center'>".date("d/m/Y",strtotime($ReportRST['DocDueDate']))."</td>";
                                                    echo "<td class='text-right'>".number_format($ReportRST['DocTotal'],2)."</td>";
                                                    echo "<td class='text-right'>".number_format($ReportRST['PaidToDate'],2)."</td>";
                                                    echo "<td class='text-right'>".number_format($ReportRST['NoPaid'],2)."</td>";
                                                    echo "<td class='text-right'>".$B30D."</td>";
                                                    echo "<td class='text-right text-danger'>".$B60D."</td>";
                                                    echo "<td class='text-right text-danger'>".$B90D."</td>";
                                                    echo "<td class='text-right text-danger'>".$B120D."</td>";
                                                    echo "<td class='text-right text-danger'>".$A120D."</td>";
                                                    echo "<td class='text-right'>$DueType</td>";
                                                    echo "<td class='text-right'>".$Fine."</td>";
                                                    echo "<td class='text-right'>".$txtFineSAL."</td>";
                                                    echo "<td class='text-right'>".$txtFineSUP."</td>";
                                                    echo "<td class='text-right'>".$txtFineMGR."</td>";
                                                echo "</tr>";
                                                $SumDocTotal   = $SumDocTotal+$ReportRST['DocTotal'];
                                                $SumPaidtoDate = $SumPaidtoDate+$ReportRST['PaidToDate'];
                                                $SumNoPaid     = $SumNoPaid+$ReportRST['NoPaid'];
                                                $SumB30D       = $SumB30D+$ReportRST['B30D'];
                                                $SumB60D       = $SumB60D+$ReportRST['B60D'];
                                                $SumB90D       = $SumB90D+$ReportRST['B90D'];
                                                $SumB120D      = $SumB120D+$ReportRST['B120D'];
                                                $SumA120D      = $SumA120D+$ReportRST['A120D'];
                                            }
                                        ?>
                                            <tr>
                                                <th class="text-center" colspan="4">รวมทั้งหมด</th>
                                                <th class="text-right"><?php echo number_format($SumDocTotal,2); ?></th>
                                                <th class="text-right"><?php echo number_format($SumPaidtoDate,2); ?></th>
                                                <th class="text-right"><?php echo number_format($SumNoPaid,2); ?></th>
                                                <th class="text-right"><?php echo number_format($SumB30D,2); ?></th>
                                                <th class="text-right text-danger"><?php echo number_format($SumB60D,2); ?></th>
                                                <th class="text-right text-danger"><?php echo number_format($SumB90D,2); ?></th>
                                                <th class="text-right text-danger"><?php echo number_format($SumB120D,2); ?></th>
                                                <th class="text-right text-danger"><?php echo number_format($SumA120D,2); ?></th>
                                                <th class="text-right">&nbsp;</th>
                                                <th class="text-right"><?php echo number_format($SumFine,2); ?></th>
                                                <th class="text-right"><?php echo number_format($SumFineSAL,2); ?></th>
                                                <th class="text-right"><?php echo number_format($SumFineSUP,2); ?></th>
                                                <th class="text-right"><?php echo number_format($SumFineMGR,2); ?></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </div>
                <script type="text/javascript">
                    window.print();
                </script>
                </body>
            </html>
<?php 
    }
}
?>
<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
    echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
    exit;
}else{
    if(!isset($_GET['startdate']) && !isset($_GET['enddate'])) {
        echo '<script type="text/javascript">window.location="../../../../"; </script>';
        exit;
    } else {
        $StartDate = $_GET['startdate'];
        $EndDate   = $_GET['enddate'];
        $SQL = "SELECT T0.DocDate, (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocumentNo', (T0.CardCode+' '+T0.CardName) AS 'CustomerName', T4.InvoiceId,
                    CASE WHEN T4.InvType = 13 THEN (SELECT TOP 1 ISNULL(P0.NumAtCard,(P1.BeginStr+CAST(P0.DocNum AS VARCHAR))) FROM OINV P0 LEFT JOIN NNM1 P1 ON P0.Series = P1.Series WHERE P0.DocEntry = T4.DocEntry)
                        WHEN T4.InvType = 14 THEN (SELECT TOP 1 ISNULL(P0.NumAtCard,(P1.BeginStr+CAST(P0.DocNum AS VARCHAR))) FROM ORIN P0 LEFT JOIN NNM1 P1 ON P0.Series = P1.Series WHERE P0.DocEntry = T4.DocEntry)
                        WHEN T4.InvType IN (24,30) THEN (SELECT TOP 1 CAST(P0.TransId AS VARCHAR) FROM OJDT P0 WHERE P0.TransId = T4.DocEntry)
                    ELSE NULL END AS 'ReferenceNo',
                    CASE WHEN T4.InvType = 13 THEN (SELECT TOP 1 P0.DocDate FROM OINV P0 WHERE P0.DocEntry = T4.DocEntry)
                        WHEN T4.InvType = 14 THEN (SELECT TOP 1 P0.DocDate FROM ORIN P0 WHERE P0.DocEntry = T4.DocEntry)
                        WHEN T4.InvType IN (24,30) THEN (SELECT TOP 1 P0.RefDate FROM OJDT P0 WHERE P0.TransId = T4.DocEntry)
                    ELSE NULL END AS 'InvoiceDate',
                    CASE WHEN T6.SlpCode = -1 THEN T0.Comments ELSE T6.SlpName END AS 'SlpName',
                    CASE WHEN T4.InvType = 13 THEN T4.SumApplied
                        WHEN T4.InvType = 14 THEN -T4.SumApplied
                    ELSE T4.SumApplied END AS 'SumApplied',
                    CASE WHEN T0.CashAcct = '4113-10' THEN 0 ELSE T0.CashSum END AS 'CashSum',
                    CASE WHEN T0.TrsfrAcct = '4113-10' THEN 0 ELSE T0.TrsfrSum END AS 'TransferSum',
                    CASE WHEN T0.CheckAcct = '4113-10' THEN 0 ELSE T0.CheckSum END AS 'CheckSum',
                    CASE WHEN T0.CashAcct = '4113-10' THEN T0.CashSum
                    ELSE CASE WHEN T0.TrsfrAcct = '4113-10' THEN T0.TrsfrSum 
                        ELSE CASE WHEN T0.CheckAcct = '4113-10' THEN T0.CheckSum
                            ELSE 0 END
                        END 
                    END AS 'Discount',
                    T2.CheckNum, T2.DueDate, T3.BankName
                FROM ORCT T0
                LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
                LEFT JOIN RCT1 T2 ON T0.DocNum = T2.DocNum
                LEFT JOIN ODSC T3 ON T2.BankCode = T3.BankCode
                LEFT JOIN RCT2 T4 ON T0.DocNum = T4.DocNum
                LEFT JOIN OCRD T5 ON T0.CardCode = T5.CardCode
                LEFT JOIN OSLP T6 ON T5.SlpCode = T6.SlpCode
                WHERE T0.DocDate BETWEEN '$StartDate' AND '$EndDate' AND T1.BeginStr LIKE 'RE-%'
                ORDER BY (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)), T4.InvoiceId ASC";
        if(intval(substr($EndDate,0,4)) <= 2022) {
            $QRY = conSAP8($SQL);
        }else{
            $QRY = SAPSelect($SQL);
        }
    ?> 


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../../../css/main/app.css" rel="stylesheet" />
        <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
        <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />
        <title>รายงานรับชำระหนี้</title>
        <style rel="stylesheet" type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
            html, body {
                background-color: #FFFFFF;
                font-family: 'Sarabun';
                font-weight: 200;
                color: #000 !important;
                font-size: 11px;
            }
            @page {
                size: A4;
                /* margin: 0; */
                size: landscape;
                margin-left: 3mm;
                margin-right: 3mm;
                margin-top: 8mm;
                margin-bottom: 10mm;
                padding: 0;
            }
            @media print {
                body { padding-bottom: 0; }
                .page {
                    /* height: initial;
                    margin: 0mm auto; */
                    box-shadow: 0 0 0;
                    width: 100%;
                    page-break-after: always;
                }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <table class='table table-sm table-borderless' style='font-size: 11px;' id='TableShow'>
                <thead style='background-color: #FFF;'>
                    <tr>
                        <td colspan='14'>
                            <h5>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h5>
                            <span>รายงานการรับชำระหนี้ เรียงตามเลขที่ใบสำคัญรับ<br> วันที่ <?php echo date('d/m/Y', strtotime($StartDate)) ?> ถึงวันที่ <?php echo date('d/m/Y', strtotime($EndDate)) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan='2' class='align-bottom border text-center'>เลขที่ใบสำคัญรับ</th>
                        <th rowspan='2' class='align-bottom border text-center'>วันที่รับเงิน</th>
                        <th rowspan='2' class='align-bottom border text-center'>ชื่อลูกค้า</th>
                        <th rowspan='2' class='align-bottom border text-center'>เลขที่บิล</th>
                        <th rowspan='2' class='align-bottom border text-center'>วันที่บิล</th>
                        <th rowspan='2' class='align-bottom border text-center'>รหัสพนักงานขาย</th>
                        <th rowspan='2' class='align-bottom border text-center'>ยอดรับชำระ</th>
                        <th rowspan='2' class='align-bottom border text-center'>เงินสด</th>
                        <th rowspan='2' class='align-bottom border text-center'>เงินโอน</th>
                        <th rowspan='2' class='align-bottom border text-center'>เช็ค</th>
                        <th rowspan='2' class='align-bottom border text-center'>ส่วนลด</th>
                        <th colspan='3' class='border text-center'>หมายเหตุ</th>
                    </tr>
                    <tr>
                        <th class='border text-center'>เลขที่เช็ค</th>
                        <th class='border text-center'>ลงวันที่</th>
                        <th class='border text-center'>ธนาคาร</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($result = odbc_fetch_array($QRY)) {
                        if($result['InvoiceId'] == "" || $result['InvoiceId'] == 0) {
                            echo"<tr>
                                    <td class='border text-center'>".$result['DocumentNo']."</td>
                                    <td class='border text-center'>".date('d/m/Y', strtotime($result['DocDate']))."</td>
                                    <td colspan='3' class='border'>".conutf8($result['CustomerName'])."</td>
                                    <td colspan='2' class='border'>".conutf8($result['SlpName'])."</td>";
                                    if($result['CashSum'] == 0)     { echo "<td class='border'></td>"; } else { echo "<td class='border text-right'>".number_format($result['CashSum'],2)."</td>"; }
                                    if($result['TransferSum'] == 0) { echo "<td class='border'></td>"; } else { echo "<td class='border text-right'>".number_format($result['TransferSum'],2)."</td>"; }
                                    if($result['CheckSum'] == 0)    { echo "<td class='border'></td>"; } else { echo "<td class='border text-right'>".number_format($result['CheckSum'],2)."</td>"; }
                                    if($result['Discount'] == 0)    { echo "<td class='border'></td>"; } else { echo "<td class='border text-right'>".number_format($result['Discount'],2)."</td>"; }
                                    if($result['CheckNum'] == "")   { echo "<td class='border'></td>"; } else { echo "<td class='border text-center'>".$result['CheckNum']."</td>"; }
                                    if($result['DueDate'] == "")    { echo "<td class='border'></td>"; } else { echo "<td class='border text-center'>".date('d/m/Y', strtotime($result['DueDate']))."</td>"; }
                                    if($result['BankName'] == "")   { echo "<td class='border'></td>"; } else { echo "<td class='border'>".conutf8($result['BankName'])."</td>"; }
                            echo"</tr>
                                <tr>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>";
                                    if($result['ReferenceNo'] == "") { echo "<td class='border'></td>"; }else{ echo "<td class='border'>".$result['ReferenceNo']."</td>"; }
                                echo"<td class='text-center border'>".date('d/m/Y', strtotime($result['InvoiceDate']))."</td>
                                    <td class='border'></td>
                                    <td class='text-right border'>".number_format($result['SumApplied'],2)."</td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                </tr>";
                        }else{
                            echo"<tr>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>";
                                    if($result['ReferenceNo'] == "") { echo "<td class='border'></td>"; }else{ echo "<td class='border'>".$result['ReferenceNo']."</td>"; }
                                echo"<td class='text-center border'>".date('d/m/Y', strtotime($result['InvoiceDate']))."</td>
                                    <td class='border'></td>
                                    <td class='text-right border'>".number_format($result['SumApplied'],2)."</td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                    <td class='border'></td>
                                </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
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
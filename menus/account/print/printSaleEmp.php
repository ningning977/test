<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
    $user_1 = SapTHSearch($_GET['u1']);
    $user_2 = SapTHSearch($_GET['u2']);
    $date_1 = $_GET['d1'];
    $date_2 = $_GET['d2'];

    $SQL1 =
        "SELECT
            A0.SlpName, SUM(A0.DocTotal) AS 'DocTotal'
        FROM (
            SELECT
                T1.SlpName, T0.DocTotal-T0.VatSum AS 'DocTotal'
            FROM OINV T0
            LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
            WHERE (T0.DocDate BETWEEN '$date_1' AND '$date_2') AND (T1.SlpName >= N'$user_1' AND T1.SlpName <= N'$user_2') AND T0.CANCELED = 'N'
            UNION ALL
            SELECT
                T1.SlpName, -(T0.DocTotal-T0.VatSum) AS 'DocTotal'
            FROM ORIN T0
            LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
            WHERE (T0.DocDate BETWEEN '$date_1' AND '$date_2') AND (T1.SlpName >= N'$user_1' AND T1.SlpName <= N'$user_2') AND T0.CANCELED = 'N'
        ) A0
        GROUP BY A0.SlpName";
    $QRY1 = SAPSelect($SQL1);
?>
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="../../../../css/main/app.css" rel="stylesheet" />
                    <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
                    <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />

                    <title>รายงานยอดขายรายบุคคล</title>
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
                            width: 210mm;
                            height: 297mm;
                            display: block;
                            margin: 3mm auto;
                            padding: 3mm;
                            box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
                        }
                        tr.summary td {
                            border-top: 1px solid #000 !important;
                            border-bottom: 3px double #000 !important;
                        }
                        @page {
                            size: A4;
                            margin: 0;
                        }
                        @media print {
                            .page {
                                height: initial;
                                margin: 0mm auto;
                                box-shadow: 0 0 0;
                                page-break-after: always;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="page">
                        <h5 style="margin: 1rem;" class="text-center">รายงานยอดขายรายบุคคล<br/></h5>
                        <table class="table table-borderless table-sm border-dark" style="color: #000;">
                            <thead>
                                <tr>
                                    <th width="15%">พนักงานขายตั้งแต่</th>
                                    <td><?php echo $_GET['u1']." ถึง ".$_GET['u2']; ?></td>
                                </tr>
                                <tr>
                                    <th>ตั้งแต่วันที่</th>
                                    <td><?php echo date("d/m/Y",strtotime($_GET['d1']))." ถึง ".date("d/m/Y",strtotime($_GET['d2'])); ?></td>
                                </tr>
                            </thead>
                        </table>
                        <table class="table border-dark OrderList" style="color: #000;">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" width="3.5%">ลำดับ</th>
                                    <th scope="col">พนักงานขาย</th>
                                    <th scope="col" width="25%">จำนวน (บาท)</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $no = 1;
                                $Sum = 0;
                                while($RST1 = odbc_fetch_array($QRY1)) { ?>
                                <tr>
                                    <td class="text-right"><?php echo number_format($no,0); ?></td>
                                    <td><?php echo conutf8($RST1['SlpName']); ?></td>
                                    <td class="text-right" style="font-weight: bold;"><?php echo number_format($RST1['DocTotal'],2); ?></td>
                                </tr>
                            <?php
                                    $Sum = $Sum + $RST1['DocTotal'];
                                    $no++;
                                }
                            ?>
                                <tr class="summary">
                                    <td colspan="2" style="font-weight: bold;" class="text-center">รวมทั้งหมด</td>
                                    <td class="text-right" style="font-weight: bold;"><?php echo number_format($Sum,2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <script type="text/javascript">window.print();</script>
                </body>
            </html>
        <?php 
    } ?>
<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}else{

    $sql1 = "SELECT T0.EmpCode,T0.CodeSAP,T0.uName,T0.uLastName,T1.PositionName,T2.DeptName 
             FROM users T0
                  LEFT JOIN positions T1 ON T1.LvCode = T0.LvCode
                  LEFT JOIN departments T2 ON T1.DeptCode = T2.DeptCode
             WHERE T0.CodeSAP = '".$_GET['OCRD']."'";
             //echo $sql1;
    $EmpData = MySQLSelect($sql1);
    $sql1 = "SELECT ShortName,LineMeMo,DueDate,Debit,Credit FROM JDT1 WHERE ShortName = '".$_GET['OCRD']."'";
    $getSAP = SAPSelect($sql1) ;
    

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

                    <title><?php echo "รายการสรุปยอดเงินคำประกันพนักงาน"; ?></title>
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
                        <h5 style="margin: 1rem;" class="text-center">รายการสรุปยอดเงินคำประกันพนักงาน<br/></h5>
                        <p class="text-danger text-center"><strong>*** เอกสารนี้เป็นเอกสารแจ้งรายละเอียดเงินค้ำประกันพนักงาน มิใช่ใบเสร็จหรือใบกำกับภาษี ***</strong></p>
                        <table class="table table-borderless table-sm border-dark" style="color: #000;">
                            <thead>
                                <tr>
                                    <th>รหัสพนักงาน </th>
                                    <td><?php echo $EmpData['EmpCode']."  &nbsp;&nbsp;".$EmpData['uName']." ".$EmpData['uLastName']."  &nbsp;&nbsp;[".$EmpData['CodeSAP']."]"; ?></td>
                                </tr>
                                <tr>
                                    <th>ตำแหน่ง/ฝ่าย</th>
                                    <td><?php echo $EmpData['PositionName']."   &nbsp;&nbsp;".$EmpData['DeptName']; ?></td>
                                     </tr>
                            </thead>
                        </table>
                        <table class="table border-dark OrderList" style="color: #000; border-bottom: 1px solid #000;">
                            <thead class="text-center">
                                <tr>
                                    <th scope="col" width="5%">ลำดับ</th>
                                    <th scope="col" width="15%">วันที่เอกสาร</th>
                                    <th scope="col">รายละเอียด</th>
                                    <th scope="col" >Debit</th>
                                    <th scope="col" >Credit</th>
                                    <th scope="col" >Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i=0;
                                    $Balance =0;
                                    while ($SAPData = odbc_fetch_array($getSAP)){
                                        $i++;
                                        $Balance = $Balance + ($SAPData['Debit']-$SAPData['Credit']);
                                        echo "<tr>
                                                    <td class='text-center'>".$i."</td>
                                                    <td class='text-center'>".date("d/m/Y",strtotime($SAPData['DueDate']))."</td>
                                                    <td>".conutf8($SAPData['LineMeMo'])."</td>
                                                    <td width='10%' class='text-right'>".number_format($SAPData['Debit'],2)."</td>
                                                    <td width='10%' class='text-right'>".number_format($SAPData['Credit'],2)."</td>
                                                    <td width='10%' class='text-right'>".number_format($Balance,2)."</td>
                                            </tr>";
                                    }


                                ?>
                            </tbody>
                            <tfoot>
                                <?php
                                    echo "<tr style='font-weight: bold;'>
                                                <td colspan=3 class='text-center'></td>
                                                
                                                <td colspan=2 class='text-center'>สรุปยอด</td>
                                                <td width='10%' class='text-right'>".number_format($Balance,2)."</td>
                                          </tr>";
                                ?>

                            </tfoot>
                        </table>

                        <hr/>

                        <table class="table table-borderless border-dark table-sm mt-4" style="width: 100%;">
                            <tr>
                                <td width="50%" rowspan=3></td>
                                <th width='1'>ลงชื่อ</th>
                                <td style="border-bottom: 1px dotted #000;"></td>
                                <td width="10%" rowspan=3></td>
                            </tr>
                            <tr>
                                <th colspan=2 class='text-center'>ผู้จัดการฝ่ายบัญชี</th>
                            </tr>
                                <th class="text-center">วันที่:</th>
                                <td style="border-bottom: 1px dotted #000;"></td>
                            </tr>

                        </table>
                    </div>
                <script type="text/javascript">window.print();</script>
                </body>
            </html>
        <?php 

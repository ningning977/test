<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else { 
    function GetCardDetail($CardCode) {
        $GetDetailSQL = 
         "SELECT TOP 1
          A0.CardCode, A2.Name, SUM(A0.DocTotal) AS 'DocTotal'
         FROM (
          SELECT T0.CardCode, SUM(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal' FROM OINV T0 WHERE T0.DocStatus = 'O' AND T0.CardCode = '$CardCode' GROUP BY T0.CardCode
          UNION ALL
          SELECT T0.CardCode, -SUM(T0.DocTotal-T0.PaidtoDate) AS 'DocTotal' FROM ORIN T0 LEFT JOIN NNM1 T1 ON T0.Series = T1.Series WHERE T0.DocStatus = 'O' AND T0.CardCode = '$CardCode' AND T1.BeginStr IN ('S1-','SR-') GROUP BY T0.CardCode
         ) A0
         LEFT JOIN OCRD A1 ON A0.CardCode = A1.CardCode
         LEFT JOIN dbo.[@TERITORY] A2 ON A1.U_Teritory = A2.Code
         GROUP BY A0.CardCode, A2.Name";
        $GetDetailQRY = SAPSelect($GetDetailSQL);
        $GetDetailRST = odbc_fetch_array($GetDetailQRY);
       
        if(ChkRowSAP($GetDetailSQL) > 0) {
         $Teritory = conutf8($GetDetailRST['Name']);
         $DocTotal = number_format($GetDetailRST['DocTotal'],2);
        } else {
         $PvSQL = "SELECT T1.Name, T0.CardCode FROM OCRD T0 LEFT JOIN dbo.[@TERITORY] T1 ON T0.U_Teritory = T1.Code WHERE T0.CardCode = '$CardCode'";
         $PvQRY = SAPSelect($PvSQL);
         $PvRST = odbc_fetch_array($PvQRY);
         $Teritory = conutf8($PvRST['Name']);
         $DocTotal = "-";
        }
       
        return array($Teritory, $DocTotal);
    }
    ?>
 
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />
        <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
        <link href="../../../../css/main/app.css" rel="stylesheet" />
        <title>แผนการเข้าพบลูกค้า</title>
        <style rel="stylesheet" type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
            html, body {
                background-color: #FFFFFF;
                font-family: 'Sarabun';
                font-weight: 400;
                color: #000 !important;
                font-size: 11px;
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
            .table {
                color: #000 !important;
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
    <?php 
    $Year  = $_GET['filt_year'];
    $Month = $_GET['filt_month'];
    $User  = $_GET['filt_user'];
    
    $SQL = "SELECT T0.PlanDate, T0.CardCode, T0.CardName, T0.Comments, T0.PlanSale, T0.DocStatus
            FROM route_planner T0
            WHERE T0.CreateUkey = '$User' AND (YEAR(T0.PlanDate) = $Year AND MONTH(T0.PlanDate) = $Month) AND T0.DocStatus = 'A'
            ORDER BY T0.PlanDate, T0.RouteEntry";
    $rowData = CHKRowDB($SQL);
    $QRY = MySQLSelectX($SQL);
    $Data = array(); $r = 0;
    while($result = mysqli_fetch_array($QRY)) {
        $r++;
        $Data['No'][$r]       = $r;
        $Data['PlanDate'][$r] = date("d/m/Y",strtotime($result['PlanDate']));
        $Data['CardName'][$r] = $result['CardName'];
        if($result['PlanSale'] != "" && $result['PlanSale'] != null && $result['PlanSale'] != 0) {
            $Data['PlanSale'][$r] = number_format($result['PlanSale'],0);
        }else{
            $Data['PlanSale'][$r] = "-";
        }
        if($result['CardCode'] != null && $result['CardCode'] != 'NULL') {
            $SAPDATA = GetCardDetail($result['CardCode']);
            $Data['Jungward'][$r] = $SAPDATA[0];
            $Data['DocTotal'][$r] = $SAPDATA[1];
        }else{
            $Data['Jungward'][$r] = "-";
            $Data['DocTotal'][$r] = "-";
        }
        $Data['Comments'][$r] = $result['Comments'];
    }
    $rowsperpage = 25;
    $pages = ceil($rowData/$rowsperpage);
    $row = 0;
    for($p = 1; $p <= $pages; $p++) {
    ?>
        <div class="page">
            <table class="table table-borderless table-sm" style="color: #000;">
                <thead>
                    <tr>
                        <td width="20%" class="text-center">
                            <img src="../../../../image/logo/kbi_logo.png" class="img-fluid" />
                        </td>
                        <td>
                            <h4 class='text-black'>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                            <small>
                                541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856
                            </small>
                        </td>
                        <td width="15%" class="align-top text-right">หน้าที่ <?php echo $p; ?> จาก <?php echo $pages; ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center"><h5 style="margin: 1rem;" class='text-black'>รายการแผนการเข้าพบลูกค้า</h5></td>
                    </tr>
                    <tr>
                        <td colspan="3" class=''>ประจำเดือน: <?php echo FullMonth($Month)." ".($Year+543); ?></td>
                    </tr>
                    <tr>
                        <?php 
                        $NameEmp = MySQLSelect("SELECT CONCAT(T0.uName, ' ', T0.uLastName, ' (', T0.uNickName, ')') AS FullName, T0.uName, T0.uLastName FROM users T0 WHERE T0.uKey = '$User'");
                        ?>
                        <td colspan="3" class=''>ชื่อพนักงาน: <?php echo $NameEmp['FullName']; ?></td>
                    </tr>
                </thead>
            </table>
            <table class='table table-sm table-bordered border-dark' style="table-layout:fixed;">
                <tbody style='font-size: 10px;'>
                    <tr class='text-center'>
                        <th width='5%'  class='align-bottom'>ลำดับ</th>
                        <th width='10%' class='align-bottom'>วันที่<br>วางแผนเข้าพบ</th>
                        <th width='28%' class='align-bottom'>ชื่อร้านค้า</th>
                        <th width='10%' class='align-bottom'>จังหวัด</th>
                        <th width='30%' class='align-bottom'>รายละเอียดเข้าพบ</th>
                        <th width='9%' class='align-bottom'>ประมาณการ<br>ยอดขาย</th>
                        <th width='8%' class='align-bottom'>บิลรอ<br>เรียกเก็บ</th>
                    </tr>
                    
                    <?php 
                    for($i = 1; $i <= $rowsperpage; $i++) { 
                        if($rowData != $row) {
                            $row++; ?>
                            <tr>
                                <td class='text-right align-top'><?php echo $Data['No'][$row]; ?></td>
                                <td class='text-center align-top'><?php echo $Data['PlanDate'][$row]; ?></td>
                                <td style="word-wrap: break-word; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class='align-top'><?php echo $Data['CardName'][$row]; ?></td>
                                <td class='align-top'><?php echo $Data['Jungward'][$row]; ?></td>
                                <td style="word-wrap: break-word; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class='align-top'><?php echo $Data['Comments'][$row]; ?></td>
                                <td class='text-right align-top'><?php echo $Data['PlanSale'][$row]; ?></td>
                                <td class='text-right align-top'><?php echo $Data['DocTotal'][$row]; ?></td>
                            </tr>
                        <?php
                        }else{ ?>
                            <tr>
                                <td class='text-right align-top'>&nbsp;</td>
                                <td class='text-center align-top'>&nbsp;</td>
                                <td class='align-top'>&nbsp;</td>
                                <td class='align-top'>&nbsp;</td>
                                <td style="word-wrap: break-word;" class='align-top'>&nbsp;</td>
                                <td class='text-right align-top'>&nbsp;</td>
                                <td class='text-right align-top'>&nbsp;</td>
                            </tr>
                        <?php   
                        }
                    } 
                    ?>
                </tbody>
            </table>
            <?php if($p < $pages){ 
                echo"<div class='d-flex justify-content-end pt-2'>
                        <span class='text-right'>
                            ผู้จัดการขายลงนาม
                            <span style='border-bottom: 1px dotted #000;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </span>
                    </div>";
            }?>
            <?php if($p == $pages){ ?>
            <table class='table table-sm table-bordered border-dark' style="table-layout:fixed;">
                <tr class='text-center'>
                    <th>ผู้จัดทำ</th>
                    <th>ผู้จัดการฝ่ายขาย</th>
                    <th>ที่ปรึกษาการตลาด</th>
                    <th>CEO</th>
                </tr>
                <tr>
                    <td rowspan='2'>&nbsp;</td>
                    <td>
                        <i class="far fa-square"></i> อนุมัติให้เบิกทดรองจ่ายค่าเบี้ยเลี้ยง<br>
                        <i class="far fa-square"></i> ไม่อนุมัติให้เบิกทดรองจ่ายค่าเบี้ยเลี้ยง
                    </td>
                    <td >
                        <i class="far fa-square"></i> อนุมัติให้เบิกทดรองจ่ายค่าเบี้ยเลี้ยง<br>
                        <i class="far fa-square"></i> ไม่อนุมัติให้เบิกทดรองจ่ายค่าเบี้ยเลี้ยง
                    </td>
                    <td>
                        <i class="far fa-square"></i> อนุมัติให้เบิกทดรองจ่ายค่าเบี้ยเลี้ยง<br>
                        <i class="far fa-square"></i> ไม่อนุมัติให้เบิกทดรองจ่ายค่าเบี้ยเลี้ยง
                    </td>
                </tr>
                <tr>
                    <td class='pt-5'>&nbsp;</td>
                    <td class='pt-5'>&nbsp;</td>
                    <td class='pt-5'>&nbsp;</td>
                </tr>
                <tr class='text-center'>
                    <td>(คุณ<?php echo $NameEmp['uName']." ".$NameEmp['uLastName']; ?>)</td>
                    <td>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                    <td>(คุณพิจิตรา ศรีสุรัตน์)</td>
                    <td>(คุณพีรัช เอื้ออำพน)</td>
                </tr>
                <tr class='text-center'>
                    <td>วันที่ 20/03/2023</td>
                    <td>วันที่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>วันที่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>วันที่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
            </table>
            <?php } ?>
        </div>
    <?php } ?>

        <script type="text/javascript">
            setTimeout(() => {
                window.print();
            }, 500);
        </script>
    </body>
    </html>
    
<?php } ?>
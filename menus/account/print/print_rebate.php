<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else { 
?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="../../../../css/main/app.css" rel="stylesheet" />
            <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
            <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />
            <script src="../../../../js/jquery-min.js" type="text/javascript"></script>

            <title>รายงาน REBATE (รายห้าง)</title>
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
                    height: 210mm;
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
                        width: 204mm; */
                        /* height: 291mm; */
                        /* page-break-after: always;  */
                        height: initial;
                        margin: 0mm auto;
                        box-shadow: 0 0 0;
                        /* border: 1px dotted #000; */
                        page-break-after: always;
                    }
                }
                .top-style {
                    font-size: 11px;
                    padding-top: 10px;
                    /* position: fixed;   */
                    top: 10px;
                    width: 100%;
                }
            </style>
        </head>
        <body>
            <?php 
            $Year  = $_GET['Year'];
            $Month = $_GET['Month'];
            $Hang  = $_GET['Hang'];
            
            $GETNAME = "SELECT T0.GroupName FROM report_rebate T0 WHERE T0.GroupCode = '$Hang' LIMIT 1";
            $NAME = MySQLSelect($GETNAME);
            $NameHang = "ยอด Rebate ".$NAME['GroupName']." ปี ".($Year+543);
        
            $SQL1 = "SELECT T0.CardCode FROM OCRD T0 WHERE T0.QryGroup$Hang = 'Y'";
            $QRY1 = SAPSelect($SQL1);
            $CardCode = "";
            while($result1 = odbc_fetch_array($QRY1)) {
                $CardCode .= "'".$result1['CardCode']."'".",";
            }
            $CardCode = substr($CardCode,0,-1);
        
            $SQL2 = "
                SELECT 
                    B0.CardCode, B1.CardName,
                    SUM(B0.M_1) AS 'M_1', SUM(B0.M_2) AS 'M_2', SUM(B0.M_3) AS 'M_3',
                    SUM(B0.M_4) AS 'M_4', SUM(B0.M_5) AS 'M_5', SUM(B0.M_6) AS 'M_6',
                    SUM(B0.M_7) AS 'M_7', SUM(B0.M_8) AS 'M_8', SUM(B0.M_9) AS 'M_9',
                    SUM(B0.M_10) AS 'M_10', SUM(B0.M_11) AS 'M_11', SUM(B0.M_12) AS 'M_12'
                FROM(
                    SELECT
                        A0.CardCode,
                        CASE WHEN A0.Month = 1 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_1',
                        CASE WHEN A0.Month = 2 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_2',
                        CASE WHEN A0.Month = 3 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_3',
                        CASE WHEN A0.Month = 4 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_4',
                        CASE WHEN A0.Month = 5 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_5',
                        CASE WHEN A0.Month = 6 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_6',
                        CASE WHEN A0.Month = 7 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_7',
                        CASE WHEN A0.Month = 8 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_8',
                        CASE WHEN A0.Month = 9 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_9',
                        CASE WHEN A0.Month = 10 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_10',
                        CASE WHEN A0.Month = 11 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_11',
                        CASE WHEN A0.Month = 12 THEN SUM(A0.DocTotal) ELSE 0 END AS 'M_12'
                    FROM(
                        SELECT T0.CardCode, MONTH(T0.DocDate) AS 'Month', SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal'
                        FROM OINV T0
                        WHERE (YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) <= $Month) AND T0.CardCode IN ($CardCode) AND T0.CANCELED = 'N'
                        GROUP BY T0.CardCode, MONTH(T0.DocDate)
                        UNION ALL
                        SELECT T0.CardCode, MONTH(T0.DocDate) AS 'Month', -SUM(T0.DocTotal-T0.VatSum) AS 'DocTotal'
                        FROM ORIN T0
                        LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
                        WHERE (YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) <= $Month) AND (T1.BeginStr LIKE 'S1-%' OR T1.BeginStr LIKE 'SR-%') AND T0.CardCode IN ($CardCode) AND T0.CANCELED = 'N'
                        GROUP BY T0.CardCode, MONTH(T0.DocDate)
                        ) A0
                    GROUP BY A0.CardCode, A0.Month
                ) B0
                LEFT JOIN OCRD B1 ON B0.CardCode = B1.CardCode
                GROUP BY B0.CardCode, B1.CardName 
                ORDER BY B0.CardCode";
            if($Year <= 2022) {
                $QRY2 = conSAP8($SQL2);
            }else{
                $QRY2 = SAPSelect($SQL2);
            }
            $Data = array();
            for($m = 1; $m <= 12; $m++) { 
                $Data['AllM_'.$m] = 0; 
                $Data['SumTotalAllM_'.$m] = 0; 
                $Data['PerM_'.$m] = 0; 
                $Data['Rebate'.$m] = 0; 
            }
            $Data['SumAllM']     = 0;
            $Data['SumTotalAll'] = 0;
            $Data['TotolPer']     = 0;
            $Data['SumRebate']   = 0; 
            $Data['LastTotalRE']   = 0; 
            $i = 0;
            while($result2 = odbc_fetch_array($QRY2)) {
                $i++;
                //รายการทั้งหมด
                $Data['CardCode'][$i] = conutf8($result2['CardCode']);
                $Data['CardName'][$i] = conutf8($result2['CardName']);
                $Data['Sum'][$i] = 0;
                for($m = 1; $m <= 12; $m++) {
                    $Data['M_'.$m][$i] = 0;
                    if($result2['M_'.$m] != 0) {
                        $Data['M_'.$m][$i] = $result2['M_'.$m];
                        $Data['Sum'][$i]   = $Data['Sum'][$i]+$result2['M_'.$m];
        
                //รวมทุกรายการ
                        $Data['AllM_'.$m]  = $Data['AllM_'.$m]+$result2['M_'.$m];
                        $Data['SumAllM']   = $Data['SumAllM']+$result2['M_'.$m];
                    }
                }
            }
        
            for($m = 1; $m <= $Month; $m++) { 
                //ยอดขายสะสม
                if($m != 1) { $mT = $m-1; }else{ $mT = $m; }
                $Data['SumTotalAllM_'.$m] = $Data['SumTotalAllM_'.$mT]+$Data['AllM_'.$m];
        
                $SQL3 = "SELECT Min, Max, percent FROM report_rebate WHERE Year = $Year AND GroupCode = $Hang";
                $QRY3 = MySQLSelectX($SQL3);
                while($result3 = mysqli_fetch_array($QRY3)) {
                    //ส่วนลด
                    if($Data['SumTotalAllM_'.$m] <= $result3['Max'] && $Data['SumTotalAllM_'.$m] >= $result3['Min']) {
                        $Data['PerM_'.$m] = $result3['percent'];
                        $Data['TotolPer'] = $result3['percent'];
                    }
                }
                
                //ยอด Rebate สะสม
                $Data['Rebate'.$m] = ($Data['PerM_'.$m]*$Data['SumTotalAllM_'.$m])/100;
                $Data['SumRebate'] = $Data['Rebate'.$m];
        
            }
            
            //Table2
            $SQL4 = "SELECT Min, Max, percent, percent_mkt, percent_dc FROM report_rebate WHERE Year = $Year AND GroupCode = $Hang";
            $QRY4 = MySQLSelectX($SQL4);
            $i2 = 0;
            while($result4 = mysqli_fetch_array($QRY4)) {
                $i2++;
                $Data['Percent_mkt'] = $result4['percent_mkt'];
                $Data['Percent_dc']  = $result4['percent_dc'];
        
                //เงื่อนไข 
                if($result4['Min'] == 1 && $result4['Max'] == 999999999) {
                    $Data['Cdt_'.$i2] = "ตั้งแต่บาทแรก";
                }elseif($result4['Max'] == 999999999) {
                    $Data['Cdt_'.$i2] = "ตั้งแต่ ".number_format($result4['Min'],0)." บาท ขึ้นไป";
                }else{
                    $Data['Cdt_'.$i2] = "ตั้งแต่ ".number_format($result4['Min'],0)." บาท ถึง ".number_format($result4['Max'],0)." บาท";
                }
        
                //ส่วนลด (%)
                $Data['Percent_'.$i2]   = $result4['percent'];
        
                //ยอด Rebate (บาท)
                if($Data['SumAllM'] <= $result4['Max'] && $Data['SumAllM'] >= $result4['Min']) {
                    $Data['LastTotal_'.$i2] = ($Data['SumAllM']*$Data['Percent_'.$i2])/100;
                    $Data['Color_'.$i2]     = "text-success";
                }else{
                    $Data['LastTotal_'.$i2] = 0;
                    $Data['Color_'.$i2]     = "";
                }
                // รวม Rebate
                $Data['LastTotalRE'] = $Data['LastTotalRE']+$Data['LastTotal_'.$i2];
            }
        
            //Table3
            $Data['Totalmkt'] = ($Data['SumAllM']*$Data['Percent_mkt'])/100;
        
            //Table4
            $Data['Totaldc'] = ($Data['SumAllM']*$Data['Percent_dc'])/100;

            $rowperpage = 15;
            $pages = ceil($i/$rowperpage);
            $RowData = 1;
            for($p = 1; $p <= $pages; $p++) { 
            ?>
            <div class="page">
                <p class='top-style text-right'><?php echo "หน้าที่ $p / $pages"; ?></p>
                <div class="row pt-2">
                    <div class="col-lg">
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
                                    <td width="30%">
                                        <div class="text-right pt-2 pb-2 ps-4">
                                            <span style='font-size: 17px; font-weight: 900;'>รายงาน REBATE (รายห้าง)</span>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class='d-flex justify-content-center'><span id='NameHang' style='font-size: 14px; font-weight: 500;'><?php echo $NameHang; ?></span></div>
                
                <div class="row pt-2">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered' id='Table1'>
                                <thead class='text-center border-dark'>
                                    <tr>
                                        <?php
                                        echo  "<th width='4%'>ลำดับที่</th>
                                                <th width='7%'>รหัสลูกค้า</th>
                                                <th width='15%'>ชื่อลูกค้า</th>";
                                        ?>
                                        <?php for($m = 1; $m <= 12; $m++) { echo "<th width='5.58%'>".FullMonth($m)."</th>";} ?>
                                        <th width='7%'>รวมทั้งปี</th>
                                    </tr>
                                </thead>
                                <tbody class='border-dark'>
                                    <?php
                                    $ChkPage = 0;
                                    for($r = 1; $r <= $rowperpage; $r++) {
                                        if($RowData <= $i) {
                                            echo"<tr>".
                                                    "<td class='text-center'>$RowData</td>".
                                                    "<td class='text-center'>".$Data['CardCode'][$RowData]."</td>".
                                                    "<td>".$Data['CardName'][$RowData]."</td>";
                                                    for($m = 1; $m <= 12; $m++) {
                                                        echo "<td class='text-right'>".number_format($Data['M_'.$m][$RowData],2)."</td>";
                                                    }
                                                    echo "<td class='text-right fw-bolder'>".number_format($Data['Sum'][$RowData],2)."</td>".
                                                "</tr>";
                                            $RowData++;
                                            $ChkPage++;
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot class='border-dark'>
                                    <?php
                                    $HeadTfoot = ['0', 'รวมทุกรายการ', 'ยอดขายสะสม',   'ส่วนลด (%)', 'ยอด Rebate สะสม'];
                                    $DataM =     ['0', 'AllM_',      'SumTotalAllM_', 'PerM_',     'Rebate'];
                                    $SumData =   ['0', 'SumAllM',    'SumAllM',       'TotolPer',   'SumRebate'];
                                    $CBolder = ""; $Tod = 0;
                                    for($h = 1; $h <= count($HeadTfoot)-1; $h++) {
                                        if($HeadTfoot[$h] == 'รวมทุกรายการ' || $HeadTfoot[$h] == 'ยอด Rebate สะสม') { $CBolder = "fw-bolder"; }else{ $CBolder = ""; }
                                        echo"<tr class='$CBolder'>".
                                                "<td colspan='3'>".$HeadTfoot[$h]."</td>";
                                                if($HeadTfoot[$h] == 'ส่วนลด (%)') { $Tod = 1; }else{ $Tod = 2;}
                                                for($m = 1; $m <= 12; $m++) {
                                                    echo "<td class='text-right'>".number_format($Data[$DataM[$h].$m],$Tod)."</td>";
                                                }
                                                echo "<td class='text-right fw-bolder'>".number_format($Data[$SumData[$h]],$Tod)."</td>".
                                            "</tr>";
                                    } 
                                    ?>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <?php if($p == $pages) { ?>
                <div class="row pt-4">
                    <div class="col-lg">
                        <div class="d-flex">
                            <div style='width: 33.33%'>
                                <table class='table table-sm table-bordered' id='Table2'>
                                    <thead class='border-dark'>
                                        <tr>
                                        <th colspan='2'>ยอดขายทั้งหมด (บาท)</th> 
                                        <th class='SalesTotal text-right'><?php echo number_format($Data['SumAllM'],2); ?></th> 
                                        </tr>
                                        <tr class='text-center'>
                                            <th width='50%'>เงื่อนไข</th>
                                            <th width='20%'>ส่วนลด (%)</th>
                                            <th width='30%'>ยอด Rebate (บาท)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='border-dark'>
                                        <?php
                                        for($r = 1; $r <= $i2; $r++) {
                                            echo"<tr class='".$Data['Color_'.$r]."'>".
                                                    "<td>".$Data['Cdt_'.$r]."</td>".
                                                    "<td class='text-center'>".number_format($Data['Percent_'.$r],1)."</td>".
                                                    "<td class='text-right'>".number_format($Data['LastTotal_'.$r],2)."</td>".
                                                "</tr>";
                                        } 
                                        ?>
                                    </tbody>
                                    <tfoot class='border-dark'>
                                        <?php
                                        echo"<tr class='fw-bolder'>".
                                                "<td colspan='2'>รวม Rebate</td>".
                                                "<td class='text-right'>".number_format($Data['LastTotalRE'],2)."</td>".
                                            "</tr>".
                                            "<tr>".
                                                "<td colspan='2'>หักภาษี ณ ที่จ่าย 3%</td>".
                                                "<td class='text-right'>".number_format($Data['LastTotalRE']*0.03,2)."</td>".
                                            "</tr>".
                                            "<tr class='fw-bolder'>".
                                                "<td colspan='2'>รวมจ่ายเช็คสุทธิ</td>".
                                                "<td class='text-right'>".number_format($Data['LastTotalRE']-($Data['LastTotalRE']*0.03),2)."</td>".
                                            "</tr>"; 
                                        ?>
                                    </tfoot>
                                </table>
                            </div>
                            <?php if($Data['Totalmkt'] != 0) { ?>
                            <span class='ps-3 pe-3'>&nbsp;</span>
                            <div style='width: 33.33%'>
                                <table class='table table-sm table-bordered' id='Table3'>
                                    <thead class='border-dark'>
                                        <tr>
                                            <th colspan='2'>ยอดขายทั้งหมด (บาท)</th>
                                            <th class='SalesTotal2 text-right'><?php echo number_format($Data['SumAllM'],2); ?></th>
                                        </tr>
                                        <tr class='text-center'>
                                            <th width='50%'>เงื่อนไข Marketing Fee</th>
                                            <th width='20%'>ส่วนลด (%)</th>
                                            <th width='30%'>Marketing Fee (บาท)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='border-dark'>
                                        <?php
                                        echo"<tr>".
                                                "<td>Marketing fee ตั้งแต่บาทแรก</td>".
                                                "<td class='text-center'>".number_format($Data['Percent_mkt'],1)."</td>".
                                                "<td class='text-right'>".number_format($Data['Totalmkt'],2)."</td>".
                                            "</tr>"; 
                                        ?>
                                    </tbody>
                                    <tfoot class='border-dark'>
                                        <?php
                                        echo"<tr class='fw-bolder'>".
                                                "<td colspan='2'>รวม Marketing Fee</td>".
                                                "<td class='text-right'>".number_format($Data['Totalmkt'],2)."</td>".
                                            "</tr>".
                                            "<tr>".
                                                "<td colspan='2'>หักภาษี ณ ที่จ่าย 3%</td>".
                                                "<td class='text-right'>".number_format($Data['Totalmkt']*0.03,2)."</td>".
                                            "</tr>".
                                            "<tr class='fw-bolder'>".
                                                "<td colspan='2'>รวมจ่ายเช็คสุทธิ</td>".
                                                "<td class='text-right'>".number_format($Data['Totalmkt']-($Data['Totalmkt']*0.03),2)."</td>".
                                            "</tr>"; 
                                        ?>
                                    </tfoot>
                                </table>
                            </div>
                            <?php } ?>
                            <?php if($Data['Totaldc'] != 0) { ?>
                            <span class='ps-3 pe-3'>&nbsp;</span>
                            <div style='width: 33.33%'>
                                <table class='table table-sm table-bordered' id='Table4'>
                                    <thead class='border-dark'>
                                        <tr>
                                            <th colspan='2'>ยอดขายทั้งหมด (บาท)</th>
                                            <th class='SalesTotal3 text-right'><?php echo number_format($Data['SumAllM'],2); ?></th>
                                        </tr>
                                        <tr class='text-center'>
                                            <th width='50%'>เงื่อนไข DC</th>
                                            <th width='20%'>ส่วนลด (%)</th>
                                            <th width='30%'>DC Fee (บาท)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='border-dark'>
                                        <?php
                                        echo"<tr>".
                                            "<td>ค่า DC ตั้งแต่บาทแรก</td>".
                                            "<td class='text-center'>".number_format($Data['Percent_dc'],1)."</td>".
                                            "<td class='text-right'>".number_format($Data['Totaldc'],2)."</td>".
                                        "</tr>"; 
                                        ?>
                                    </tbody>
                                    <tfoot class='border-dark'>
                                        <?php
                                        echo"<tr class='fw-bolder'>".
                                                "<td colspan='2'>รวม Rebate</td>".
                                                "<td class='text-right'>".number_format($Data['Totaldc'],2)."</td>".
                                            "</tr>".
                                            "<tr>".
                                                "<td colspan='2'>หักภาษี ณ ที่จ่าย 3%</td>".
                                                "<td class='text-right'>".number_format($Data['Totaldc']*0.03,2)."</td>".
                                            "</tr>".
                                            "<tr class='fw-bolder'>".
                                                "<td colspan='2'>รวมจ่ายเช็คสุทธิ</td>".
                                                "<td class='text-right'>".number_format($Data['Totaldc']-($Data['Totaldc']*0.03),2)."</td>".
                                            "</tr>";
                                        ?>
                                    </tfoot>
                                </table>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="row pt-4">
                    <div class="col-lg">
                        <table class='table table-sm table-bordered'>
                            <thead class='border-dark'>
                                <tr class='text-center'>
                                    <th width='25%'>ผู้จัดทำ</th>
                                    <th width='25%'>ผู้ตรวจสอบ</th>
                                    <th width='50%' colspan='2'>ผู้อนุมัติ</th>
                                </tr>
                            </thead>
                            <tbody class='border-dark'>
                                <tr>
                                    <td>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
                                    <td>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
                                    <td>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
                                    <td>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
                                </tr>
                                <tr class='text-center fw-bold'>
                                    <td>(คุณศิริวรรณ เซ่งเลี่ยน)</td>
                                    <td>(คุณภิญญาพัชญ์ วิสุทธิ์ปราชญ์)</td>
                                    <td>(คุณพีรัช เอื้ออำพน)</td>
                                    <td>(คุณพิสิษฐ์ เอื้ออำพน)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </body>
    </html>
    <script type="text/javascript">
        // window.print();
    </script>
<?php } ?>
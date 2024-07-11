<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
    if(!isset($_GET['docnum'])) {
        echo '<script type="text/javascript">window.location="../../../../"; </script>';
    }else{
        function numTocha($number){ 
            $txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ'); 
            $txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'); 
            $number = str_replace(",","",$number); 
            $number = str_replace(" ","",$number); 
            $number = str_replace("บาท","",$number); 
            $number = explode(".",$number); 
            if (sizeof($number)>2){ 
                return 'ทศนิยมหลายตัวนะจ๊ะ'; 
                exit; 
            } 
            $strlen = strlen($number[0]); 
            $convert = ''; 
            for ($i=0;$i<$strlen;$i++){ 
                $n = substr($number[0], $i,1); 
                if ($n!=0){ 
                    if ($i==($strlen-1) AND $n==1){
                    $convert .= 'เอ็ด';
                    }elseif ($i==($strlen-2) AND $n==2){
                    $convert .= 'ยี่';
                    }elseif($i==($strlen-2) AND $n==1){
                    $convert .= ''; 
                    }else{
                    $convert .= $txtnum1[$n];
                    } 
                    $convert .= $txtnum2[$strlen-$i-1]; 
                } 
            } 
            $convert .= 'บาท'; 
            if ($number[1]=='0' OR $number[1]=='00' OR $number[1]==''){ 
                $convert .= 'ถ้วน'; 
            }else{ 
                $strlen = strlen($number[1]); 
                for ($i=0;$i<$strlen;$i++){ 
                $n = substr($number[1], $i,1); 
                if ($n!=0) { 
                    if ($i==($strlen-1) AND $n==1){
                    $convert .= 'เอ็ด';
                    }elseif ($i==($strlen-2) AND $n==2){
                    $convert .= 'ยี่';
                    }elseif ($i==($strlen-2) AND $n==1){
                    $convert .= '';
                    }else{ 
                    $convert .= $txtnum1[$n];
                    } 
                    $convert .= $txtnum2[$strlen-$i-1]; 
                }  
                } 
                $convert .= 'สตางค์'; 
            } 
            return $convert; 
        } 

        $DocNum = $_GET['docnum'];
        $sql = "SELECT * FROM note_billing WHERE DocNum = '$DocNum' AND DocStatus = 'A'";
        $qry = MySQLSelectX($sql);
        $r = 0;
        while($result = mysqli_fetch_array($qry)) {
            ++$r;
            $searchBill     = $result['CardCode'];
            $dateBill       = date("d/m/Y",strtotime($result['CreateDate']));
            $billNo         = $result['DocNum'];
            $tranidBill[$r] = $result['TransID'];
            $CreateUkey     = $result['CreateUkey']; 
        }
        $qry = "SELECT CONCAT(uName,' ',uLastName) AS Creater FROM users WHERE uKey = '$CreateUkey'";
        $resultCreat = MySQLSelect($qry);
        $creater = $resultCreat['Creater'];

        // ดึงข้อมูลลูกค้า - GET DATA CUSTOMER
        $sqlSAP = " SELECT T0.CardCode, T0.CardName, T0.LicTradNum, T1.Street, T1.Block, T1.City, T0.Phone1, T0.Phone2, 
                            T0.Cellular, T0.Fax, T2.PymntGroup, T0.U_ChqCond, T1.ZipCode
                    FROM OCRD T0
                    LEFT JOIN CRD1 T1 ON T0.CardCode = T1.CardCode
                    LEFT JOIN OCTG T2 ON T0.GroupNum = T2.GroupNum
                    WHERE T0.CardCode = '$searchBill' AND (T1.AdresType = 'B') AND T1.Street IS NOT NULL";
        $qrySAP = SAPSelect($sqlSAP);
        $resultSAP = odbc_fetch_array($qrySAP);
        if(isset($resultSAP['CardCode'])) {
            $cusID       = $resultSAP['CardCode'];
            $cusName     = conutf8($resultSAP['CardName']);
            $cusAddress1 = conutf8($resultSAP['Street']);
            $cusAddress2 = conutf8($resultSAP['Block'])." ".conutf8($resultSAP['City'])." ".$resultSAP['ZipCode'];
            if($resultSAP['Phone2'] != "") {
                $cusPhone    = $resultSAP['Phone1'].", ".$resultSAP['Phone2'];
            }else{
                $cusPhone    = $resultSAP['Phone1'];
            }
            $cusFax      = $resultSAP['Fax'];
            $cusTax      = $resultSAP['LicTradNum'];
            $cusCredit   = conutf8($resultSAP['PymntGroup']);
            $cusChq      = conutf8($resultSAP['U_ChqCond']);
        }
        
        $BillAdded = "";
        for ($i = 1; $i <= $r; $i++){
            if($i == 1) {
                $BillAdded .= "T0.TransID = '".$tranidBill[$i]."'";
            }else{
                $BillAdded .= " OR T0.TransID = '".$tranidBill[$i]."'";
            }
        }
        $BillAdded = "AND (".$BillAdded.")";

        $sqlSAP2 = "SELECT (T0.BalScDeb - T0.BalScCred) AS 'ToPaid' 
                    FROM JDT1 T0 
                    WHERE T0.ShortName = '$searchBill' AND (T0.TransType = '13' OR T0.TransType = '14' OR T0.TransType = '30') AND 
                          ((T0.BalScDeb - T0.BalScCred) != 0 OR T0.MthDate IS NULL) $BillAdded";
        $qrySAP2 = SAPSelect($sqlSAP2);
        $Row = 0;
        $billTotalAmont = 0;
        while($resultSAP2 = odbc_fetch_array($qrySAP2)) {
            ++$Row;
            $billPaid[$Row]    = $resultSAP2['ToPaid'];
            $billTotalAmont    = $billTotalAmont + $billPaid[$Row];
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

                <title><?php echo $DocNum; ?></title>
                <style rel="stylesheet" type="text/css">
                        @import url('https://fonts.googleapis.com/css2?family=tahoma:wght@200;300;400;500;600&display=swap');
                        html, body {
                            background-color: #FFFFFF;
                            font-family: 'tahoma';
                            font-weight: 200;
                            color: #000 !important;
                            font-size: 12px;
                        }
                        h1,h2,h3,h4,h5,h6 {
                            color: #000;
                            padding: 0;
                            margin: 0;
                            font-weight: 600;
                        }
                        .page {
                            width: 210mm;
                            display: block;
                            margin: 3mm auto;
                            padding: 3mm;
                            padding-top: 50px;
                            box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
                        }
                        @page {
                            /* size: A4;
                            margin: 0; */
                            /* margin:.75in .45in .75in .45in; */
                            mso-header-margin: .3in;
                            mso-footer-margin: .3in;
                            mso-horizontal-page-align: center;
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
                        .nbsp{
                            border-bottom: 1px dotted #000;
                        }
                </style>
            </head>
            <body>
                <?php
                $rowperpage = 15;
                $offset = 0;
                $pages = ceil($Row/$rowperpage);
                $no = 1;
                for($p = 1; $p <= $pages; $p++) {
                    $offset = ($p-1)*$rowperpage;
                    $SQL = "SELECT * FROM note_billing T0 WHERE T0.DocNum = '$DocNum' ORDER BY T0.DocEntry LIMIT $rowperpage OFFSET $offset";
                    $QRY = MySQLSelectX($SQL);
                    $TransID = [];
                    $LineNum = [];
                    $i = 0;
                    $CardCode = "";
                    $WhrIn = "(";
                    while($RST = mysqli_fetch_array($QRY)) {
                        ++$i;
                        $TransID[$i] = $RST['TransID'];
                        $LineNum[$i] = $RST['LineNum'];
                        $WhrIn .= $RST['TransID'].",";
                        if($CardCode == "") {
                            $CardCode = $RST['CardCode'];
                        }
                        // echo $TransID[$i]." / ".$LineNum[$i]."<br/>";
                    }
                    $WhrIn = substr($WhrIn,0,-1).")";
                
                    $SAPSQL = 
                    "SELECT T0.TransID, T0.RefDate, T0.LINE_ID,
                        CASE
                            WHEN (T0.TransType = '13') THEN T0.Ref2
                            WHEN (T0.TransType = '14') THEN (SELECT NumAtCard FROM ORIN WHERE DocNum = T0.BaseRef)
                            WHEN (T0.TransType = '30') THEN T0.BaseRef 
                            ELSE T0.Ref3Line
                        END AS 'RefNo', 
                        T0.DueDate, (T0.Debit - T0.Credit) AS 'Balanced', T0.BaseRef AS 'Remark', (T0.BalScDeb - T0.BalScCred) AS 'ToPaid' 
                    FROM JDT1 T0 
                    WHERE T0.ShortName = '$CardCode' AND (T0.TransType = '13' OR T0.TransType = '14' OR T0.TransType = '30') AND ((T0.BalScDeb - T0.BalScCred) != 0 OR T0.MthDate IS NULL) AND T0.TransID IN $WhrIn";
                    $SAPQRY = SAPSelect($SAPSQL);
                    $i = 0;
                    // echo $SAPSQL;
                    while($SAPRST = odbc_fetch_array($SAPQRY)) {
                        $BillRefDate[$SAPRST['TransID']][$SAPRST['LINE_ID']]   = date("d/m/Y",strtotime($SAPRST['RefDate']));
                        $BillRefNo[$SAPRST['TransID']][$SAPRST['LINE_ID']]     = $SAPRST['RefNo'];
                        $BillDueDate[$SAPRST['TransID']][$SAPRST['LINE_ID']]   = date("d/m/Y",strtotime($SAPRST['DueDate']));
                        $BillBalanced[$SAPRST['TransID']][$SAPRST['LINE_ID']]  = $SAPRST['Balanced'];
                        $BillRemark[$SAPRST['TransID']][$SAPRST['LINE_ID']]    = conutf8($SAPRST['Remark']);
                        $BillToPaid[$SAPRST['TransID']][$SAPRST['LINE_ID']]    = $SAPRST['ToPaid'];
                        $i++;
                    }
                ?>
                <div class="page">
                    <table class="table table-borderless table-sm m-0" style="color: #000;">
                        <thead>
                            <tr>
                                <td>
                                    <h4>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                                    <span>
                                        541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                        เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856
                                    </span>
                                </td>
                                <td width="20%">
                                    <div class="border border-dark text-center pt-2 pb-2 ps-4 pe-4">
                                        <span style='font-size: 18px; font-weight: 900;'>ใบวางบิล</span>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <table class='table table-sm table-borderless'>
                        <thead class='border border-dark' style='font-size: 12px;'>
                            <tr>
                                <td width='13%' style='font-weight: 900;'>รหัสลูกค้า</td>        <td class='fw-bolder' width='43%'><?php echo $cusID; ?></td>
                                <td width='19%' style='font-weight: 900;'>เลขที่ใบวางบิล</td>     <td class='fw-bolder' width='25%'><?php echo $billNo; ?></td>
                            </tr>
                            <tr>
                                <td width='13%' style='font-weight: 900;'>ชื่อลูกค้า</td>          <td class='fw-bolder' width='43%'><?php echo $cusName; ?></td>
                                <td width='19%' style='font-weight: 900;'>วันที่/Date</td>        <td class='fw-bolder' width='25%'><?php echo $dateBill; ?></td>
                            </tr>
                            <tr>
                                <td width='13%' style='font-weight: 900;'>ที่อยู่/Address</td>     <td class='fw-bolder' width='43%'><?php echo $cusAddress1; ?></td>
                                <td width='19%' style='font-weight: 900;'>เงื่อนไขการชำระเงิน</td>  <td class='fw-bolder' width='25%'><?php echo $cusCredit; ?></td>
                            </tr>
                            <tr>
                                <td width='13%' style='font-weight: 900;'></td>                 <td class='fw-bolder' width='43%'><?php echo $cusAddress2; ?></td>
                                <td width='19%' style='font-weight: 900;'>เลขประจำตัวผู้เสียภาษี</td> <td class='fw-bolder' width='25%'><?php echo $cusTax; ?></td>
                            </tr>
                            <tr>
                                <td width='13%' style='font-weight: 900;'>โทรศัพท์</td>           <td class='fw-bolder' width='43%'><?php echo $cusPhone; ?></td>
                                <td width='19%' style='font-weight: 900;'>Fax.</td>               <td class='fw-bolder' width='25%'><?php echo $cusFax; ?></td>
                            </tr>
                        </thead>
                    </table>
                    <table class='table table-sm table-borderless'>
                        <thead style='font-weight: 900;'>
                            <tr class='text-center'>
                                <td width="5%" class='border border-dark'>ลำดับ</td>
                                <td width="15%" class='border border-dark'>วัน/เดือน/ปี</td>
                                <td width="15%" class='border border-dark'>เลขที่ใบแจ้งหนี้</td>
                                <td width="15%" class='border border-dark'>วันครบกำหนด</td>
                                <td width="15%" class='border border-dark'>จำนวนเงิน</td>
                                <td width="15%" class='border border-dark'>จำนวนเงิน<br>ที่เรียกเก็บ</td>
                                <td width="17%" class='border border-dark'>หมายเหตุ</td>
                            </tr>
                        </thead>
                        <tbody><?php $Data = "";
                            $rowinpage = 0;
                            // echo count($TransID);
                            //<td class='text-center border-start border-end border-dark'>".$BillRefNo[$TransID[$r]][$LineNum[$r]]."</td>
                            for($r = 1; $r <= count($TransID); $r++) {
                                $rowinpage++;
                                $Data .="<tr class='fw-bolder'>
                                            <td class='text-center border-start border-end border-dark'>$no</td>
                                            <td class='text-center border-start border-end border-dark'>".$BillRefDate[$TransID[$r]][$LineNum[$r]]."</td>
                                            <td class='text-center border-start border-end border-dark'>".$BillRefNo[$TransID[$r]][$LineNum[$r]]."</td>
                                            <td class='text-center border-start border-end border-dark'>".$BillDueDate[$TransID[$r]][$LineNum[$r]]."</td>
                                            <td class='text-right border-start border-end border-dark'>".number_format($BillBalanced[$TransID[$r]][$LineNum[$r]],2)."</td>
                                            <td class='text-right border-start border-end border-dark'>".number_format($BillToPaid[$TransID[$r]][$LineNum[$r]],2)."</td>
                                            <td class='text-center border-start border-end border-dark'>".$BillRemark[$TransID[$r]][$LineNum[$r]]."</td>
                                        </tr>";
                                $no++;
                            }
                            for($j = $rowinpage; $j < $rowperpage; $j++) {
                                $Data .="<tr class='fw-bolder'>
                                            <td class='text-center border-start border-end border-dark'>&nbsp;</td>
                                            <td class='text-center border-start border-end border-dark'></td>
                                            <td class='text-center border-start border-end border-dark'></td>
                                            <td class='text-center border-start border-end border-dark'></td>
                                            <td class='text-right border-start border-end border-dark'></td>
                                            <td class='text-right border-start border-end border-dark'></td>
                                            <td class='text-center border-start border-end border-dark'></td>
                                        </tr>";
                            }

                            $Data .="<tr class='fw-bolder'>
                                        <td colspan='7' class='text-center border border-dark'>** ห้ามจ่ายเป็นเงินสด โปรดจ่ายเป็นเช็คขีดคร่อม ในนาม บริษัท คิงบางกอก อินเตอร์เทรด จำกัด เท่านั้น **</td>
                                    </tr>
                                    <tr>
                                        <td colspan='4' class='border border-dark' style='font-size: 12px; font-weight: 500;'>รวม $Row ฉบับ</td>
                                        <td class='text-right border-bottom border-dark' style='font-size: 12px; font-weight: 500;'>รวมเงิน<br>(GRAND TOTAL)</td>
                                        <td class='text-right border-bottom border-dark' style='font-size: 12px; font-weight: 900;'>".number_format($billTotalAmont,2)."</td>
                                        <td class='border-bottom border-end border-dark' style='font-size: 12px; font-weight: 500;'>บาท</td>
                                    </tr>
                                    <tr>
                                        <td colspan='4' class='border border-dark' style='font-size: 12px; font-weight: 500;'>กำหนดวางบิล-รับเช็ค $cusChq</td>
                                        <td colspan='3' class='text-center border border-dark fst-italic' style='font-size: 12px; font-weight: 900;'>*** ".numTocha(number_format($billTotalAmont,2))." ***</td>
                                    </tr>";
                            echo $Data;
                        ?></tbody>
                    </table>
                    <table class='table table-sm table-bordered border-dark'>
                        <tbody style='font-weight: 900; font-size: 12px;'>
                            <tr>
                                <td width='33.33%' class=''>
                                    <div class='d-flex'>
                                        <span style='width: 25%'>ชำระโดย</span>
                                        <input type="checkbox" class='me-3' name='' id=''>
                                        <span>เงินสด CASH</span>
                                    </div>
                                    <div class='d-flex pt-2'>
                                        <span style='width: 25%'></span>
                                        <input type="checkbox" class='me-3' name='' id=''>
                                        <span>เช็ค CHEQUE</span>
                                    </div>
                                    <div class='d-flex' style='padding-top: 40px;'>
                                        <span>วันที่นัดรับเช็ค&nbsp;</span> 
                                        <span class='nbsp'>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </span>
                                    </div>
                                </td>
                                <td width='33.33%' class="align-baseline">
                                    <div class='d-flex' style='padding-top: 50px;'>
                                        <span style='width: 20%'>ผู้วางบิล&nbsp;</span>
                                        <span class='nbsp'>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </span>
                                    </div>
                                    <div class='d-flex' style='padding-top: 13px;'>
                                        <span style='width: 20%'>วันที่&nbsp;</span>
                                        <span class='nbsp'>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </span>
                                    </div>
                                </td>
                                <td width='33.33%' >
                                    <div class='d-flex justify-content-center'>
                                        <span>ข้าพเจ้าได้รับใบกำกับภาษีและรับวางบิล<br>ตามรายการข้างต้นถูกต้องเรียบร้อยแล้ว</span>
                                    </div>
                                    <div class='d-flex' style='padding-top: 16.5px;'>
                                        <span style='width: 26%'>ผู้รับวางบิล&nbsp;</span>
                                        <span class='nbsp'>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </span>
                                    </div>
                                    <div class='d-flex pt-3'>
                                        <span style='width: 26%'>วันที่&nbsp;</span>
                                        <span class='nbsp'>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- <div class='d-flex justify-content-end pt-4 pe-2'>
                        <span class='font-weight: 300;'><?php echo $p; ?> / <?php echo $Page; ?></span>
                    </div> -->
                </div>
                <?php } ?>
                <script type="text/javascript">
                    // window.print();
                </script>
            </body>
        </html>
<?php }
} ?>
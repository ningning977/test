<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
switch($_GET['p']){
    case 'P1' :
        $a = 1;
        $b = 10;
        break;
    case 'P2' :
        $a = 11;
        $b = 21;
        break;
    case 'P3' :
        $a = 21;
        $b = 30;
        break;
    case 'P4' :
        $a = 31;
        $b = 40;
        break;
    case 'P5' :
        $a = 41;
        $b = 50;
        break;
    case 'P6' :
        $a = 51;
        $b = 60;
        break;
    case 'P7' :
        $a = 61;
        $b = 70;
        break;
    case 'P8' :
        $a = 71;
        $b = 80;
        break;
    case 'P9' :
        $a = 81;
        $b = 90;
        break;
    case 'P10' :
        $a = 91;
        $b = 100;
        break;
}
?>

<!DOCTYPE html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>รายงานส่งสินค้าประจำวัน</title>
    <style rel="stylesheet" type="text/css">
        body {
            font-family: 'Tahoma';
            font-size: 12px;
        }
        .page {
            page-break-after: always;
        }
        h1,h2,h3,h4,h5,h6, p { padding: 8px 0 4px 0; margin: 0; }
        h1 { font-size: 24px; }
        h2 { font-size: 20px; }
        h3 { font-size: 16px; }
        th,td { padding: 3px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bd-sl-all { border: 1px solid #000000; }
        .bd-sl-t { border-top: 1px solid #000000; }
        .bd-sl-r { border-right: 1px solid #000000; }
        .bd-sl-b { border-bottom: 1px solid #000000; }
        .bd-sl-l { border-left: 1px solid #000000; }
        .bd-dt-all { border: 1px solid #000000; }
        .bd-dt-t { border-top: 1px dotted #000000; }
        .bd-dt-r { border-right: 1px dotted #000000; }
        .bd-dt-b { border-bottom: 1px dotted #000000; }
        .bd-dt-l { border-left: 1px dotted #000000; }
        .bd-db-b { border-bottom: 3px double #000000; }
        .tablelooping > tr:last-child > td { border-bottom: 1px solid #000000 !important; }
        table tr.page-break { page-break-after: always; }
        span.barcode { font-family: 'BarCode'; }
        p { padding: 0.05 rem; }
        @page {
            size: A4;
            size: portrait;
            margin-left: 3mm;
            margin-right: 3mm;
            margin-top: 8mm;
            margin-bottom: 10mm;
            padding: 0;
        }
        @media print {
            hr { display: none; }
            body { font-family: 'Angsana New'; font-size: 14; }
            #manual td { font-size: 12 !important; }
        }
    </style>
    <script src="../../../js/JsBarcode.all.min.js" type="text/javascript"></script>
</head>
<?php
$LogiNum = $_GET['lid'];
$sqlHead = "SELECT T0.LogiNum,T0.OutDate,T0.DriverName,T0.LcCar,
                   CONCAT(T1.uName,' (',T1.uNickName,')') AS uCreate
            FROM logi_head T0 
                 LEFT JOIN users T1 ON T0.ukeyCreate = T1.uKey
            WHERE T0.LogiNum = '".$LogiNum."'";
//echo $sqlHead;
$Header = MySQLSelect($sqlHead);
$OutDate = FullDate(date("Y-m-d",strtotime($Header['OutDate'])));
$LCcar = $Header['LcCar'];
$DriverName = $Header['DriverName'];
$EmpName = $Header['uCreate'];
?>
<body onload=print();>
    <!-- หัวกระดาษ -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td><img src="../../../../../img/logo.png" style="max-height: 36px;" /></td>
                <th colspan="5"><h1>รายงานส่งสินค้าประจำวัน</h1></th>
                <td valign="bottom" class="text-right" style="font-weight: bold;">เลขที่เอกสาร</td>
                <td valign="bottom" class="text-center bd-dt-b"><span style="font-weight: bold;font-size:16px;"><?php echo $LogiNum;?></span></td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold;">กรุณากรอกข้อมูลในช่องทำงานไม่สำเร็จทุกร้านที่ไป!</td>
                <td colspan="6" class="text-right" style="font-weight: bold;">กรณีติดปัญหาด้านขนส่งติดต่อ ธุรการขนส่ง โทร. 061-662-8453, 02-004-9786,88 ต่อ 107/109</td>
            </tr>
            <tr>
                <td width="20%" style="font-weight: bold;">ผลการส่งสินค้าประจำวันที่</td>
                <td width="12.5%" class="bd-dt-b"><?php echo $OutDate;?></td>
                <td width="10%" style="font-weight: bold;">ทะเบียนรถ</td>
                <td class="bd-dt-b"><?php echo $LCcar;?></td>
                <td width="10%" style="font-weight: bold;">ชื่อคนขับรถ</td>
                <td class="bd-dt-b"><span style="font-weight: bold;font-size:14px;"><?php echo $DriverName;?></span></td>
                <td width="10%" style="font-weight: bold;">ชื่อเด็กติดรถ</td>
                <td class="bd-dt-b"></td>
            </tr>
        </thead>
    </table>

    <!-- รายละเอียดการขนส่ง -->
    <table width="100%" border="0" cellpadding="1" cellspacing="0" style="margin-top: .5rem; margin-bottom: .5rem;">
        <thead>
            <tr>
                <th width="2.5%" class="bd-sl-t bd-sl-r bd-sl-b bd-sl-l" rowspan="3">No.</th>
                <th width="19.25%" class="bd-sl-t bd-sl-r bd-sl-b" rowspan="3">ที่อยู่ลูกค้า/ชื่อขนส่ง<br/>(1)</th>
                <th width="22.75%" class="bd-sl-t bd-sl-r bd-sl-b" rowspan="3">ชื่อร้านค้า<br/>(2)</th>
                <th width="7.5%" class="bd-sl-t bd-sl-r bd-sl-b" rowspan="3">เลขที่บิล/<br/>จำนวนบิล<br/>(3)</th>
                <th width="6%" class="bd-sl-t bd-sl-r bd-sl-b" rowspan="3">วันที่บิล<br/>(4)</th>
                <th class="bd-sl-t bd-sl-b" colspan="4">ไปเพื่ออะไร?</th>
                <th width="5%" class="bd-sl-t bd-sl-r bd-sl-b bd-sl-l" rowspan="3">ถึงเวลา<br/>.....น.<br/>(8)</th>
                <th width="5%" class="bd-sl-t bd-sl-r bd-sl-b" rowspan="3">ออกเวลา<br/>.....น.<br/>(9)</th>
                <th width="8.5%" class="bd-sl-t bd-sl-r bd-sl-b" rowspan="3">ได้ตรวจสอบใบรับสินค้า<br/>ว่าชื่อ/ที่อยู่/จำนวน/สาขา<br/>ตรงกับบิลแล้ว (/) (10)</th>
                <th width="5%" class="bd-sl-t bd-sl-r bd-sl-b" rowspan="3">พบ<br/>ปัญหา</br/>โทรกลับ<br/>ชื่อ (11)</th>
            </tr>
            <tr>
                <th width="5%" class="bd-sl-r bd-sl-b" rowspan="2">ส่งของ<br/>จำนวน (ลัง)<br/>(5)</th>
                <th width="5%" class="bd-sl-r bd-sl-b" rowspan="2">วางบิล<br/>(/)<br/>(6)</th>
                <th class="bd-sl-b" colspan="2">เก็บ (7)</th>
            </tr>
            <tr>
                <th width="5%" class="bd-sl-r bd-sl-b">เช็ค (บาท)</th>
                <th width="5%" class="bd-sl-b">เงินสด (บาท)</th>
            </tr>
        </thead>
        <tbody class="tablelooping">
        <?php
        $mySQLList1 = "SELECT DISTINCT T0.LogiNum, T0.BillEntry,T0.BillType,T0.CostCr,T0.CostCa,T0.LogiName,
                           (SELECT COUNT(P1.BoxCode) FROM logi_detail P1 WHERE P1.LogiNum = T0.LogiNum AND P1.BillEntry = T0.BillEntry AND P1.BillType = T0.BillType) AS TotalBox
                    FROM logi_detail T0
                    WHERE LogiNum = '".$LogiNum."'";
       
        $getMyList1 = MySQLSelectX($mySQLList1);
        $cx=0;
        $OINV = "('";
        $ODLN = "('";
        $OWAS = "('";
        
        $INV1 = 0;
        $DLN1 = 0;
        $WAS1 = 0;
        while ($MyList1 = mysqli_fetch_array($getMyList1)){
            $cx++;
            $DocEntry[$cx] = $MyList1['BillEntry'];
            if ($MyList1['CostCr'] > 0) {
                $CostCr[$DocEntry[$cx]] = number_format($MyList1['CostCr'],2);
            }else{
                $CostCr[$DocEntry[$cx]] = "-";
            }
            if ($MyList1['CostCa']){
                $CostCa[$DocEntry[$cx]] = number_format($MyList1['CostCa'],2);
            }else{
                $CostCa[$DocEntry[$cx]] = "-";
            }
            
            $CostCa[$DocEntry[$cx]] = $MyList1['CostCa'];
            $TotalBox[$DocEntry[$cx]] = $MyList1['TotalBox'];
            $LogiName[$DocEntry[$cx]] = $MyList1['LogiName'];
            $BillType[$DocEntry[$cx]] = $MyList1['BillType'];

            switch ($MyList1['BillType']){
                case 'OINV' :
                    $OINV .= $MyList1['BillEntry']."','";
                    $INV1++;
                break;
                case 'ODLN' :
                    $ODLN .= $MyList1['BillEntry']."','";
                    $DLN1++;
                break;
                default :
                    $OWAS .= $MyList1['BillEntry']."','";
                    $WAS1++;
                break;


            }

        }
        if ($INV1 != 0){
            $OINV = substr($OINV,0,-2).")";
        }else{
            $OINV = "('0')";
        }
        if ($DLN1 !=0){
            $ODLN = substr($ODLN,0,-2).")";
        }else{
            $ODLN = "('0')";
        }

        if ($WAS1 !=0){
            $OWAS = substr($OWAS,0,-2).")";
        }else{
            $OWAS = "('0')";
        }
        $MsSQL = "SELECT DISTINCT
                         T0.[DocEntry] AS 'BillEntry', ISNULL(T1.[BeginStr],'IV-') AS 'BillBeginStr', T0.[DocNum], T0.[DocDate], T2.[BaseEntry] AS 'SoEntry',T0.CardName,T3.Name
                  FROM OINV T0
                       LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
                       LEFT JOIN INV1 T2 ON T0.[DocEntry] = T2.[DocEntry]
                       LEFT JOIN [dbo].[@SHIPPINGTYPE] T3 ON T0.U_ShippingType = T3.Code
                  WHERE T0.[DocEntry] IN ".$OINV." 
                  UNION ALL
                  SELECT DISTINCT
                         T0.[DocEntry] AS 'BillEntry', T1.[BeginStr] AS 'BillBeginStr', T0.[DocNum], T0.[DocDate], T2.[BaseEntry] AS 'SoEntry',T0.CardName,T3.Name
                  FROM ODLN T0
                       LEFT JOIN NNM1 T1 ON T0.[Series] = T1.[Series]
                       LEFT JOIN DLN1 T2 ON T0.[DocEntry] = T2.[DocEntry]
                       LEFT JOIN [dbo].[@SHIPPINGTYPE] T3 ON T0.U_ShippingType = T3.Code
                  WHERE T0.[DocEntry] IN ".$ODLN;
        //echo $MsSQL;
        $BillQRY = SAPSelect($MsSQL);
        
        while($MsBill = odbc_fetch_array($BillQRY)){
            $BillNo[$MsBill['BillEntry']] = $MsBill['BillBeginStr'].$MsBill['DocNum'];
            $CusName[$MsBill['BillEntry']] = conutf8($MsBill['CardName']);
            if ($LogiName[$MsBill['BillEntry']] == NULL OR $LogiName[$MsBill['BillEntry']] == ''){
                $LogiName[$MsBill['BillEntry']] = conutf8($MsBill['Name']);
            }
            
            $DocDate[$MsBill['BillEntry']] = date("d/m/Y",strtotime($MsBill['DocDate']));
        }
        $output = "";
        for ($i=$a;$i<=$b;$i++){
            if ($i<=$cx){
                if ($BillType[$DocEntry[$i]] == 'OWAS' OR $BillType[$DocEntry[$i]] == 'OWAB'){

                    $sql1 = "SELECT T0.DocNum,T0.CusName,T0.LogiName,T0.DateCreate,T0.PaidTotal,
                                    (SELECT COUNT(A0.BoxCode) FROM pack_boxlist A0 WHERE A0.BillEntry = T0.DocEntry AND BillType LIKE 'OW%') AS TotalBox
                             FROM owas T0
                             WHERE T0.DocEntry = '".$DocEntry[$i]."'";
                    //echo $sql1;
                    $MyOWAS = MySQLSelect($sql1);

                    $LogiName[$DocEntry[$i]] =  $MyOWAS['LogiName'];
                    $CusName[$DocEntry[$i]] = $MyOWAS['CusName'];
                    $BillNo[$DocEntry[$i]] = $MyOWAS['DocNum'];
                    $DocDate[$DocEntry[$i]] = date("d/m/Y",strtotime($MyOWAS['DateCreate']));
                    $TotalBox[$DocEntry[$i]] = $MyOWAS['TotalBox'];

                }

                $output .= " 
                            <tr>
                                <td class='text-right bd-sl-r bd-dt-b bd-sl-l'>".$i."</td>
                                <td class='bd-sl-r bd-dt-b'>".$LogiName[$DocEntry[$i]]."</td>
                                <td class='bd-sl-r bd-dt-b'>".$CusName[$DocEntry[$i]]."</td>
                                <td class='bd-sl-r bd-dt-b text-center'>".$BillNo[$DocEntry[$i]]."</td>
                                <td class='bd-sl-r bd-dt-b text-center'>".$DocDate[$DocEntry[$i]]."</td>
                                <td class='bd-sl-r bd-dt-b text-center'>".$TotalBox[$DocEntry[$i]]."</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b text-right'>0</td>
                                <td class='bd-sl-r bd-dt-b text-right'>0</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                            </tr>";
                $tmpLoop =0;
            }
            if ( $i>$cx AND $tmpLoop == 0){
                $tmpLoop = 1;
                $mySQLList2 = "SELECT * FROM logi_detail2 WHERE LogiNum = '".$LogiNum."'";
                $getMyList2 = MySQLSelectX($mySQLList2);
                while ($MyList2 = mysqli_fetch_array($getMyList2)){
                    $output .= "
                                <tr>
                                    <td class='text-right bd-sl-r bd-dt-b bd-sl-l'>".$i."</td>
                                    <td class='bd-sl-r bd-dt-b'>".$MyList2['LogiName']."</td>
                                    <td class='bd-sl-r bd-dt-b'>".$MyList2['CusName']."</td>
                                    <td class='bd-sl-r bd-dt-b text-center'>".$MyList2['BillNo']."</td>
                                    <td class='bd-sl-r bd-dt-b text-center'>".date('d/m/Y',strtotime($MyList2['BillDate']))."</td>
                                    <td class='bd-sl-r bd-dt-b text-center'>".number_format($MyList2['Qty'])."</td>
                                    <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                    <td class='bd-sl-r bd-dt-b text-right'>".$MyList2['CostCr']."</td>
                                    <td class='bd-sl-r bd-dt-b text-right'>".$MyList2['CostCa']."</td>
                                    <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                    <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                    <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                    <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                </tr>";
                                $i++;
                }
            }
            if ($i > $cx AND $tmpLoop == 1){
                $output .= "
                            <tr>
                                <td class='text-right bd-sl-r bd-dt-b bd-sl-l'>".$i."</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                                <td class='bd-sl-r bd-dt-b'>&nbsp;</td>
                            </tr>";
            }
        }

        echo $output;
        ?>
        
        </tbody>
        <!-- ส่วนสรุปท้ายตาราง -->
        <tr height="24px;">
            <td class="bd-sl-l">&nbsp;</td>
            <td valign="bottom" colspan="2" class="bd-sl-b">ประเมินเวลาไปกลับ....................ชม.</td>
            <td>&nbsp;</td>
            <td class="text-right bd-sl-r" style="font-weight: bold;">เป้า</td>
            <td class="bd-sl-r bd-dt-b">&nbsp;</td>
            <td class="bd-sl-r bd-dt-b">&nbsp;</td>
            <td class="bd-sl-r bd-dt-b">&nbsp;</td>
            <td class="bd-sl-r bd-dt-b">&nbsp;</td>
            <td colspan="2" class="text-center bd-sl-r">ลงชื่อ</td>
            <td colspan="2" class="text-center bd-sl-r">ตรวจสอบ (3),(7)</td>
        </tr>
        <tr height="24px;">
            <td class="bd-sl-l">&nbsp;</td>
            <td class="text-center bd-sl-r bd-sl-b bd-sl-l" style="font-weight: bold;">กิจกรรม</td>
            <td class="text-center bd-sl-r bd-sl-b" style="font-weight: bold;">เลขไมล์ (กม.)</td>
            <td>&nbsp;</td>
            <td class="text-right bd-sl-r" style="font-weight: bold;">ทำได้</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td colspan="2" class="text-center bd-dt-b bd-sl-r"><?php echo $EmpName;?></td>
            <td colspan="2" class="text-center bd-dt-b bd-sl-r">&nbsp;</td>
        </tr>
        <tr height="24px;">
            <td class="bd-sl-l">&nbsp;</td>
            <td valign="bottom" class="bd-sl-r bd-sl-b bd-sl-l">เข้าบริษัท เวลา...............น.</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td>&nbsp;</td>
            <td class="text-right bd-sl-r" style="font-weight: bold;">ผลต่าง</td>
            <td class="bd-sl-r">&nbsp;</td>
            <td class="bd-sl-r">&nbsp;</td>
            <td class="bd-sl-r">&nbsp;</td>
            <td class="bd-sl-r">&nbsp;</td>
            <td valign="bottom" colspan="2" class="text-center bd-sl-r">วันที่..........................</td>
            <td colspan="2" valign="bottom" class="text-center bd-sl-r">วันที่..........................</td>
        </tr>
        <tr height="24px;">
            <td class="bd-sl-l">&nbsp;</td>
            <td valign="bottom" class="bd-sl-r bd-sl-b bd-sl-l">ออกรถ เวลา...............น.</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td>&nbsp;</td>
            <td class="bd-sl-r">&nbsp;</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td valign="bottom" colspan="2" class="text-center bd-sl-b bd-sl-r">(ผู้ตรวจสอบ CH4)</td>
            <td colspan="2" valign="bottom" class="text-center bd-sl-b bd-sl-r">(ผู้ตรวจสอบ)</td>
        </tr>
        <tr height="24px;">
            <td class="bd-sl-l">&nbsp;</td>
            <td valign="bottom" class="bd-sl-r bd-sl-b bd-sl-l">ผลต่าง ...............น.</td>
            <td class="bd-sl-r bd-sl-b">&nbsp;</td>
            <td valign="bottom" colspan="5">น้ำมันที่เติม....................บาท ค่าทางด่วน....................บาท</td>
            <td valign="bottom" colspan="4">ผู้ตรวจสอบ........................................ วันที่........................................</td>
            <td class="bd-sl-r">&nbsp;</td>
        </tr>
        <tr height="8px;">
            <td class="bd-sl-r bd-sl-b bd-sl-l" colspan="13"></td>
        </tr>
    </table>
    <!-- คู่มือ --->
    <strong>คู่มือพนักงานคลังสินค้า (ขนส่ง)</strong>
    <table id="manual" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top: .2rem;">
        <tr>
            <th width="17.25%" class="text-center bd-sl-t bd-sl-r bd-sl-b bd-sl-l">บิล HA และ KM</th>
            <th width="22.25%" class="text-center bd-sl-t bd-sl-r bd-sl-b">บิล IV และ บิล IM</th>
            <th width="60.50%" class="text-center bd-sl-t bd-sl-r bd-sl-b">ปัญหาและข้อเสนอแนะในวันนี้</th>
        </tr>
        <tr>
            <td valign="top" class="bd-sl-r bd-sl-b bd-sl-l" rowspan="10">
                <p class="text-center"><strong>ส่งของอย่างเดียว</strong></p>
                - สำเนาบิลลูกค้า (สีม่วง)
                <p class="text-center"><strong>ส่งของ+วางบิล</strong></p>
                - สำเนาบิลลูกค้า (สีม่วง)<br/>
                - ต้นฉบับลูกค้า (สีน้ำตาล)<br/>
                - สำเนาใบวางบิล (สีชมพู)
                <p class="text-center"><strong>ส่งของ+เก็บเช็ค</strong></p>
                - สำเนาบิลลูกค้า (สีม่วง)<br/>
                - ต้นฉบับลูกค้า (สีน้ำตาล)
            </td>
            <td valign="top" class="bd-sl-r bd-sl-b" rowspan="10">
                <p class="text-center"><strong>ส่งของอย่างเดียว</strong></p>
                - ต้นฉบับใบกำกับภาษีลูกค้า (สีน้ำเงิน)
                <p class="text-center"><strong>ส่งของ+วางบิล</strong></p>
                - ต้นฉบับใบกำกับภาษีลูกค้า (สีน้ำเงิน)<br/>
                - สำเนาใบกำกับภาษี (สีเขียว)<br/>
                - สำเนาใบวางบิล (สีชมพู)
                <p class="text-center"><strong>ส่งของ+เก็บเช็ค</strong></p>
                - ต้นฉบับใบกำกับภาษีลูกค้า (สีน้ำเงิน)<br/>
                - สำเนาใบเสร็จรับเงินลูกค้า (สีน้ำตาล)<br/>
                - สำเนาใบกำกับภาษี (สีเขียว)
            </td>
            <td height="14" class="bd-sl-r bd-dt-b"></td>
        </tr>
        <tr><td height="14" class="bd-sl-r bd-dt-b"></td></tr>
        <tr><td height="14" class="bd-sl-r bd-dt-b"></td></tr>
        <tr><td height="14" class="bd-sl-r bd-dt-b"></td></tr>
        <tr><td height="14" class="bd-sl-r bd-dt-b"></td></tr>
        <tr><td height="14" class="bd-sl-r bd-dt-b"></td></tr>
        <tr><td height="14" class="bd-sl-r bd-dt-b"></td></tr>
        <tr><td height="14" class="bd-sl-r bd-dt-b"></td></tr>
        <tr><td height="14" class="bd-sl-r bd-dt-b"></td></tr>
        <tr><td height="14" class="bd-sl-r bd-sl-b"></td></tr>
        <tr>
            <td valign="top" style="font-weight: bold; text-decoration: underline;">ข้อควรจำและปฏิบัติ</td>
            <td colspan="2">
                - กรณีการจ่ายค่าขนส่งต้นทางเป็นเงินสด ต้องขอใบเสร็จรับเงิน ที่มีระบุคำว่า "ใบเสร็จรับเงิน" หรือ "บิลเงินสด"<br/>
                - กรณีติดปัญหาด้านเอกสาร, การวางบิล, การเก็บเช็ค และการเก็บเงินสด ติดต่อแผนกบัญชี โทร.081-358-4546, 02-509-3850 ต่อ 135<br/>
                - กรณีติดปัญหาด้านขนส่งติดต่อประสานงานกับแผนกจัดส่ง (ธุรการขนส่งโทร.061-662-8453 และหัวหน้าจัดส่งโทร.086-340-1598 หรือโทร.02-004-9786,88 ต่อ 107/109)
            </td>
        </tr>
        <tr>
            <td valign="top" style="font-weight: bold; text-decoration: underline;">หมายเหตุ</td>
            <td colspan="2" style="font-weight: bold;">
                1. ทุกกรณีที่เกิดปัญหาด้านการส่งสินค้า และเก็บเช็ค, วางบิล ห้ามโทร.แจ้งหลังออกจากลูกค้าแล้ว<u>เด็ดขาด!</u><br/>
                2. ต้องโทร.แจ้ง + อยู่แก้ไขในขณะอยู่กับลูกค้าหรือหัวหน้างาน เพื่อให้ส่วนกลางที่รับผิดชอบช่วยแก้ไขปัญหาให้จบก่อนทุกครั้ง<br/>
                3. ต้องได้รับคำตอบจากบุคคลที่ทำการติดต่อก่อนทุกครั้งถึงสามารถออกจากงานหน้างานหรือลูกค้าได้
            </td>
        </tr>
        <tr>
            <td colspan="3"><small>FM-WH-01 Rev.06 วันที่มีผลบังคับใช้: 01-11-60 อายุจัดเก็บ: อย่างน้อย 1 ปี วิธีทำลาย: ขีดคร่อม/Re-Use</small></td>
        </tr>
    </table>
</body>   
</html>
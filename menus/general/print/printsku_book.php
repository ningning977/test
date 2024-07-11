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
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />
        <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
        <link href="../../../../css/main/app.css" rel="stylesheet" />
        <title>SKU BOOK : PRINT</title>
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
                margin: '0';
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
    $ItemCode  = $_GET['ItemCode'];
    $Tab  = $_GET['Tab'];
    $PriceType = $_GET['PriceType'];
    $SQL_HEADER = "SELECT * FROM skubook_header WHERE ItemCode = '$ItemCode'";
    $RST_HEADER = MySQLSelect($SQL_HEADER);
    $chk_uClass = 'N';
    switch($_SESSION['uClass']) {
        case 0: 
        case 2: 
        case 3: 
        case 4: 
        case 5: 
        case 13: 
        case 14: 
        case 15: 
        case 16: 
        case 17: 
        case 18: 
        case 34: $chk_uClass = 'Y'; break;
    }
    ?>
    <?php if($Tab == 'PD') { ?>
        <div class="page"> <!-- หน้า 1 -->
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
                        <td width="" class="align-top">
                            <span class='text-left fw-bolder'>หน้าที่ : 1</span><br>
                            <span class='text-left fw-bolder'>เลขที่ SKU : <?php echo $ItemCode; ?></span><br>
                            <span class='text-left fw-bolder'>วันที่ : <?php echo date("d/m/Y",strtotime($RST_HEADER['CreateDate'])); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center text-black">SKU BOOK (PD)</td>
                    </tr>
                </thead>
            </table>
            <div>
                <!-- 1. ข้อมูลสินค้า -->
                    <?php 
                    $SQL_SAP = "
                    SELECT T0.ItemCode, T0.ItemName, T0.FrgnName, T0.CodeBars, T1.Name AS NameType1, T2.Name AS NameType2, T3.CardName, T0.SalUnitMsr, T0.U_ProductStatus,T4.Name AS Brand,T5.Name AS Model
                    FROM OITM T0
                    LEFT JOIN dbo.[@ITEMGROUP1] T1 ON T1.Code = T0.U_Group1
                    LEFT JOIN dbo.[@ITEMGROUP2] T2 ON T2.Code = T0.U_Group2 
                    LEFT JOIN OCRD T3 ON T3.CardCode = T0.CardCode
                    LEFT JOIN dbo.[@BRAND2] T4 ON T4.Code = T0.U_Brand2
                    LEFT JOIN dbo.[@PROMOTION] T5 ON T5.Code = T0.U_Promotion_1
                        WHERE T0.ItemCode = '$ItemCode'";
                    $QRY_SAP = SAPSelect($SQL_SAP);
                    $RST_SAP = odbc_fetch_array($QRY_SAP);
                    ?>
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr> 
                                <th colspan='4'>
                                    1. ข้อมูลสินค้า
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr> 
                                <th width='15%' class='ps-4'>ประเภท (กลุ่มหลัก)</th>
                                <td width='35%'><?php echo conutf8($RST_SAP['NameType1']); ?></td>
                                <th width='15%'>ประเภท (กลุ่มรอง)</th>
                                <td width='35%'><?php echo conutf8($RST_SAP['NameType2']); ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>ชื่อภาษาไทย</th>
                                <td><?php echo conutf8($RST_SAP['ItemName']); ?></td>
                                <th>ชื่อภาษาอังกฤษ</th>
                                <td><?php echo conutf8($RST_SAP['FrgnName']); ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>รหัสสินค้า</th>
                                <td><?php echo $RST_SAP['ItemCode']; ?></td>
                                <th>Barcode</th>
                                <td><?php echo $RST_SAP['CodeBars']; ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>สถานะสินค้า</th>
                                <td><?php echo $RST_SAP['U_ProductStatus']; ?></td>
                                <th>รหัสทีมขาย</th>
                                <td><?php echo $RST_HEADER['TeamCode']; ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>รุ่น (Model)</th>
                                <td><?php echo conutf8($RST_SAP['Model']); ?></td>
                                <th>ยี่ห้อ</th>
                                <td><?php echo $RST_SAP['Brand']; ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>สีตัวสินค้า</th>
                                <td><?php echo $RST_HEADER['ItemColor']; ?></td>
                                <th>สีของบรรจุภัณฑ์</th>
                                <td><?php echo $RST_HEADER['BoxColor']; ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>ทำจากวัสดุ</th>
                                <td><?php echo $RST_HEADER['MadeOf']; ?></td>
                                <th>ประเทศผู้ผลิต</th>
                                <td><?php echo $RST_HEADER['ProCountry']; ?></td>
                            </tr>
                            <?php if($chk_uClass == 'Y') { ?>
                            <tr> 
                                <th class='ps-4'>ผู้ผลิต</th>
                                <td colspan='3'><?php echo conutf8($RST_SAP['CardName']); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <!-- 2. คุณสมบัติ -->
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr> 
                                <th colspan='4'>2. คุณสมบัติ</th>
                            </tr> 
                        </thead>
                        <tbody>
                            <?php 
                            $Chk_DETAIL_Type1 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '1'";
                            if(CHKRowDB($Chk_DETAIL_Type1) == 0) {
                               echo "<tr><td colspan='4' class='text-center'>ยังไม่มีข้อมูล</td></tr>";
                            }else{
                                $SQL_DETAIL_Type1 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '1'";
			                    $QRY_DETAIL_Type1 = MySQLSelectX($SQL_DETAIL_Type1);
                                $tmpRow = 0;
                                while ($RST_DETAIL_Type1 = mysqli_fetch_array($QRY_DETAIL_Type1)) {
                                    $tmpRow++; 
                                    if($tmpRow == 1) { 
                                        echo "
                                        <tr>
                                            <th class='ps-4' width='15%'>".$RST_DETAIL_Type1['Header']."</th>
                                            <td width='35%'>".$RST_DETAIL_Type1['Detail']."</td>"; 
                                    }else{
                                        echo "
                                            <th width='15%'>".$RST_DETAIL_Type1['Header']."</th>
                                            <td width='35%'>".$RST_DETAIL_Type1['Detail']."</td>
                                        </tr>";
                                        $tmpRow = 0;
                                    }
                                }
                                if($tmpRow != 0) {
                                    echo "
                                        <th></th>
                                        <td></td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                <!-- 3. รายละเอียดบรรจุภัณฑ์ -->
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr> 
                                <th colspan='4'>3. รายละเอียดบรรจุภัณฑ์</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $SQL_DETAIL_Type2 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '2'";
                            $QRY_DETAIL_Type2 = MySQLSelectX($SQL_DETAIL_Type2);
                            $tmpRow = 0; $Sum = 0; $DataType2 = ""; $DataSizeBox2 = "";
                            while ($RST_DETAIL_Type2 = mysqli_fetch_array($QRY_DETAIL_Type2)) {
                                $tmpRow++; 
                                if($tmpRow == 1) { 
                                    if($RST_DETAIL_Type2['Header'] == 'ขนาดกล่อง 2 (ซม.)') {
                                        $Detail_Type2x = explode("x",$RST_DETAIL_Type2['Detail']);
                                        $DataSizeBox2 .= "
                                        <tr>
                                            <th width='15%' class='ps-4'>".$RST_DETAIL_Type2['Header']."</th>
                                            <td width='35%'>";
                                                $Detail_Type2x_0 = ""; $Detail_Type2x_1 = ""; $Detail_Type2x_2 = "";
                                                if(isset($Detail_Type2x[0])) { 
                                                    if($Detail_Type2x[0] != "") {
                                                        $Detail_Type2x_0 = $Detail_Type2x[0]."x"; 
                                                    }
                                                }
                                                if(isset($Detail_Type2x[1])) { 
                                                    if($Detail_Type2x[1] != "") {
                                                        $Detail_Type2x_1 = $Detail_Type2x[1]."x"; 
                                                    }
                                                }
                                                if(isset($Detail_Type2x[2])) { 
                                                    if($Detail_Type2x[2] != "") {
                                                        $Detail_Type2x_2 = $Detail_Type2x[2]; 
                                                    }
                                                }
                                                $DataSizeBox2 .= $Detail_Type2x_0.$Detail_Type2x_1.$Detail_Type2x_2;
                                            $DataSizeBox2 .= "
                                            </td>
                                        </tr>";
                                        $tmpRow = 0;
                                    }else{
                                        $DataType2 .= "
                                        <tr>
                                            <th class='ps-4' width='20%'>".$RST_DETAIL_Type2['Header']."</th>
                                            <td width='30%'>";
                                            switch ($RST_DETAIL_Type2['Header']) {
                                                case 'ขนาดสินค้า (ซม.)':
                                                case 'ขนาดกล่อง (ซม.)':
                                                case 'ขนาดลัง (ซม.)':
                                                    $Detail_Type2 = explode("x",$RST_DETAIL_Type2['Detail']);
                                                    $Detail_Type2_0 = ""; $Detail_Type2_1 = ""; $Detail_Type2_2 = "";
                                                    if(isset($Detail_Type2[0])) { 
                                                        if($Detail_Type2[0] != "") {
                                                            $Detail_Type2_0 = $Detail_Type2[0]."x"; 
                                                        }
                                                    }
                                                    if(isset($Detail_Type2[1])) { 
                                                        if($Detail_Type2[1] != "") {
                                                            $Detail_Type2_1 = $Detail_Type2[1]."x"; 
                                                        }
                                                    }
                                                    if(isset($Detail_Type2[2])) { 
                                                        if($Detail_Type2[2] != "") {
                                                            $Detail_Type2_2 = $Detail_Type2[2]; 
                                                        }
                                                    }
                                                    $DataType2 .= $Detail_Type2_0.$Detail_Type2_1.$Detail_Type2_2;
                                                    if($RST_DETAIL_Type2['Header'] == "ขนาดสินค้า (ซม.)") {
                                                        $DataType2 .= "&nbsp;&nbsp;<small>(".$RST_DETAIL_Type2['Remark'].")<small>";
                                                    }
                                                break;
                                                case 'น้ำหนักลังรวมสินค้า (กก.)':
                                                    $DataType2 .= $Sum;
                                                break;
                                                default:
                                                    if($RST_DETAIL_Type2['Header'] == 'น้ำหนักรวมสินค้า (กก.)') { $Sum = $RST_DETAIL_Type2['Detail']; }
                                                    $DataType2 .= $RST_DETAIL_Type2['Detail'];
                                                    break;
                                            }
                                    }
                                }else{
                                    $DataType2 .= "
                                        <th width='15%'>".$RST_DETAIL_Type2['Header']."</th>
                                        <td width='33%'>";
                                        switch ($RST_DETAIL_Type2['Header']) {
                                            case 'ขนาดบรรจุ (กล่อง)':
                                                if($RST_DETAIL_Type2['Detail'] != '') {
                                                    $DataType2 .= $RST_DETAIL_Type2['Detail']." ".conutf8($RST_SAP['SalUnitMsr']);
                                                }else{
                                                    $DataType2 .= "";
                                                }
                                            break;
                                            case 'ขนาดบรรจุ (ลัง)':
                                                if($RST_DETAIL_Type2['Detail'] != "" && $RST_DETAIL_Type2['Detail'] != 0) {
                                                    $Sum = $Sum*$RST_DETAIL_Type2['Detail'];
                                                }else{
                                                    $Sum = "";
                                                }
                                                $DataType2 .= $RST_DETAIL_Type2['Detail'];
                                            break;
                                            case 'น้ำหนักสินค้า (กก.)':
                                                $DataType2 .= $RST_DETAIL_Type2['Detail']."&nbsp;&nbsp;<small>(".$RST_DETAIL_Type2['Remark'].")<small>";
                                            break;
                                            default: $DataType2 .= $RST_DETAIL_Type2['Detail']; break;
                                        }
                                    $DataType2 .= "
                                    </tr>";
                                    if($RST_DETAIL_Type2['Header'] == 'ขนาดบรรจุ (กล่อง)') {
                                        $DataType2 .= "SizeBox2"; 
                                    }
                                    $tmpRow = 0;
                                }
                            }
                            echo str_replace("SizeBox2",$DataSizeBox2,$DataType2);
                            ?>
                        </tbody>
                    </table>
                <!-- 4. อุปกรณ์ภายในกล่อง -->
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr> 
                                <th>4. อุปกรณ์ภายในกล่อง</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $Chk_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '3'";
                            $DataType3 = "";
                            if(CHKRowDB($Chk_DETAIL_Type3) == 0) {
                                $DataType3 .= "<tr><td class='text-center'>ยังไม่มีข้อมูล</td></tr>";
                            }else{
                                $SQL_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '3'";
			                    $QRY_DETAIL_Type3 = MySQLSelectX($SQL_DETAIL_Type3);
                                $i = 0;
                                $DataType3 .= "<tr><td class='ps-4'>";
                                while ($RST_DETAIL_Type3 = mysqli_fetch_array($QRY_DETAIL_Type3)) {
                                    $i++;
                                    $DataType3 .= $RST_DETAIL_Type3['Detail'];
                                    if(CHKRowDB($SQL_DETAIL_Type3) != $i) {
                                        $DataType3 .= ", ";
                                    }
                                }
                                $DataType3 .= "</td></tr>";
                            }
                            echo $DataType3;
                            ?>
                        </tbody>
                    </table>
                <!-- 5. วิธีการใช้งาน -->
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr> 
                                <th colspan='4'>5. วิธีการใช้งาน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $Chk_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '4'";
                            $DataType3 = "";
                            if(CHKRowDB($Chk_DETAIL_Type3) == 0) {
                                $DataType3 .= "<tr><td class='text-center'>ยังไม่มีข้อมูล</td></tr>";
                            }else{
                                $SQL_DETAIL_Type3 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '4'";
			                    $QRY_DETAIL_Type3 = MySQLSelectX($SQL_DETAIL_Type3);
                                $i = 0;
                                $DataType3 = "";
                                while ($RST_DETAIL_Type3 = mysqli_fetch_array($QRY_DETAIL_Type3)) {
                                    $i++;
                                    $DataType3 .= "<tr><td class='ps-4'>".$RST_DETAIL_Type3['Detail']."</td></tr>";
                                }
                            }
                            echo $DataType3;
                            ?>
                        </tbody>
                    </table>
                <!-- 6. จุดเด่น จุดขาย ของสินค้า -->
                    <table table class='table table-sm table-borderless border border-dark border-bottom-1 m-0'>
                        <thead>
                            <tr> 
                                <th>6. จุดเด่น จุดขาย ของสินค้า</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $Chk_DETAIL_Type4 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '5'";
                            if(CHKRowDB($Chk_DETAIL_Type4) == 0) {
                                echo "<tr><td class='text-center'>ยังไม่มีข้อมูล</td></tr>";
                            }else{
                                $SQL_DETAIL_Type4 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '5'";
                                $QRY_DETAIL_Type4 = MySQLSelectX($SQL_DETAIL_Type4);
                                $i = 0;
                                while ($RST_DETAIL_Type4 = mysqli_fetch_array($QRY_DETAIL_Type4)) {
                                    $i++;
                                    echo "<tr><td class='ps-4'>".$RST_DETAIL_Type4['Detail']."</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                
                
            </div>
        </div>
        <div class="page"> <!-- หน้า 2 -->
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
                        <td width="" class="align-top">
                            <span class='text-left fw-bolder'>หน้าที่ : 2</span><br>
                            <span class='text-left fw-bolder'>เลขที่ SKU : <?php echo $ItemCode; ?></span><br>
                            <span class='text-left fw-bolder'>วันที่ : <?php echo date("d/m/Y",strtotime($RST_HEADER['CreateDate'])); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center text-black">SKU BOOK (PD)</td>
                    </tr>
                </thead>
            </table>
            <div>
                <!-- 7. การรับประกัน -->
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr> 
                                <th colspan='4'>
                                    7. การรับประกัน
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $Chk_DETAIL_Type5 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '6'";
                            $QRY_DETAIL_Type5 = MySQLSelectX($Chk_DETAIL_Type5);
                            $r = 0;
                            while ($RST_DETAIL_Type5 = mysqli_fetch_array($QRY_DETAIL_Type5)) {
                                $r++;
                                if($RST_DETAIL_Type5['Header'] == "ระยะเวลารับประกัน (เดือน)") {
                                    echo"
                                    <tr>
                                        <td width='20%' class='ps-4 fw-bolder'>".$RST_DETAIL_Type5['Header']."</td>
                                        <td width='5%'>".$RST_DETAIL_Type5['Detail']."</td>
                                        <td width='5%'>ระบุ</td>
                                        <td width='70%'>".$RST_DETAIL_Type5['Remark']."</td>
                                    </tr>";
                                }else{
                                    echo"
                                    <tr>
                                        <td width='20%' class='ps-4 fw-bolder'>".$RST_DETAIL_Type5['Header']."</td>
                                        <td width='85%' colspan='3'>".$RST_DETAIL_Type5['Detail']."</td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                <!-- 8. ข้อมูล สคบ. -->
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr> 
                                <th colspan='2'>8. ข้อมูล สคบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $SQL_DETAIL_Type5 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '7'";
                            $QRY_DETAIL_Type5 = MySQLSelectX($SQL_DETAIL_Type5);
                            while ($RST_DETAIL_Type5 = mysqli_fetch_array($QRY_DETAIL_Type5)) {
                                switch ($RST_DETAIL_Type5['Header']) {
                                    case 'ชื่อสินค้า':
                                        echo "
                                        <tr>
                                            <th width='15%' class='ps-4 align-top'>".$RST_DETAIL_Type5['Header']."</th>
                                            <td width='85%'>".conutf8($RST_SAP['ItemName'])."</td>
                                        </tr>"; 
                                    break;
                                    case 'ผลิตจากประเทศ':
                                        echo "
                                        <tr>
                                            <th class='ps-4 align-top'>".$RST_DETAIL_Type5['Header']."</th>
                                            <td>
                                                ".$RST_DETAIL_Type5['Remark']."
                                            </td>
                                        </tr>"; 
                                    break;
                                    case 'จัดจำหน่ายโดย':
                                        echo "
                                        <tr>
                                            <th class='ps-4 align-top'>".$RST_DETAIL_Type5['Header']."</th>
                                            <td>
                                                ".$RST_DETAIL_Type5['Detail']."<br>"; 
                                    break;
                                    case 'จัดจำหน่ายโดย_2':
                                        echo $RST_DETAIL_Type5['Detail']."<br>"; 
                                    break;
                                    case 'จัดจำหน่ายโดย_3':
                                        echo "
                                                ".$RST_DETAIL_Type5['Detail']."
                                            </td>
                                        </tr>";
                                    break;
                                    default:
                                        echo "
                                        <tr>
                                            <th class='ps-4 align-top'>".$RST_DETAIL_Type5['Header']."</th>
                                            <td>".$RST_DETAIL_Type5['Detail']."</td>
                                        </tr>"; 
                                    break;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                <!-- 9. ข้อควรระวัง -->
                    <table class='table table-sm table-borderless border border-dark border-bottom-1 m-0'>
                        <thead>
                            <tr> 
                                <th colspan='4'>
                                    9. ข้อควรระวัง
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $Chk_DETAIL_Type9 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '8'";
                            $QRY_DETAIL_Type9 = MySQLSelectX($Chk_DETAIL_Type9);
                            while ($RST_DETAIL_Type9 = mysqli_fetch_array($QRY_DETAIL_Type9)) {
                                echo"
                                <tr>
                                    <td width='20%' class='ps-4 fw-bolder'>".$RST_DETAIL_Type9['Header']."</td>
                                    <td width='80%' colspan='3'>".$RST_DETAIL_Type9['Detail']."</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
            </div>
        </div>
        <div class="page"> <!-- หน้า 3 -->
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
                        <td width="" class="align-top">
                            <span class='text-left fw-bolder'>หน้าที่ : 3</span><br>
                            <span class='text-left fw-bolder'>เลขที่ SKU : <?php echo $ItemCode; ?></span><br>
                            <span class='text-left fw-bolder'>วันที่ : <?php echo date("d/m/Y",strtotime($RST_HEADER['CreateDate'])); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center text-black">SKU BOOK (PD)</td>
                    </tr>
                </thead>
            </table>
            <div>
                <table class='table table-sm table-borderless border border-dark m-0 h-100'>
                    <tbody>
                        <?php 
                        $SQL = "SELECT * FROM skubook_attach WHERE ItemCode = '$ItemCode' AND FileStatus = 'A' AND Type IN (1,2,3,4,5)";
                        $QRY = MySQLSelectX($SQL);
                        while($RST = mysqli_fetch_array($QRY)) {
                            $Img[$RST['Type']] = $RST['FileDirName'].'.'.$RST['FileExt'];
                        }
                        $NameImg = ['','รูปสินค้า','รูปบรรจุภัณฑ์','อุปกรณ์ภายในกล่อง','รูปลังสินค้า','รูป Barcode'];
                        $n = 0;
                        for($i = 1; $i <= 5; $i++) {
                            if(isset($Img[$i])) {
                                if($n == 0) {
                                    echo "
                                    <tr>
                                        <td class='text-center'>
                                            <img style='width: 150px;' src='../../../../image/products/$ItemCode/$i/".$Img[$i]."'/>
                                            <div class='fw-bolder pt-1'>".$NameImg[$i]."</div>
                                        </td>";
                                    $n++;
                                }else{
                                    echo "
                                        <td class='text-center'>
                                            <img style='width: 150px;' src='../../../../image/products/$ItemCode/$i/".$Img[$i]."'/>
                                            <div class='fw-bolder pt-1'>".$NameImg[$i]."</div>
                                        </td>
                                    </tr>";
                                    $n = 0;
                                }
                                
                            }else{
                                if($n == 0) {
                                    echo "
                                    <tr>
                                        <td class='text-center'>
                                            <img style='width: 150px;' src='../../../../image/products/no-image.jpg'/>
                                            <div class='fw-bolder pt-1'>".$NameImg[$i]."</div>
                                        </td>";
                                    $n++;
                                }else{
                                    echo "
                                        <td class='text-center'>
                                            <img style='width: 150px;' src='../../../../image/products/no-image.jpg'/>
                                            <div class='fw-bolder pt-1'>".$NameImg[$i]."</div>
                                        </td>
                                    </tr>";
                                    $n = 0;
                                }
                                
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php }elseif($Tab == 'MK'){ ?>
        <div class="page"> <!-- หน้า 1 -->
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
                        <td width="" class="align-top">
                            <span class='text-left fw-bolder'>หน้าที่ : 1</span><br>
                            <span class='text-left fw-bolder'>เลขที่ SKU : <?php echo $ItemCode; ?></span><br>
                            <span class='text-left fw-bolder'>วันที่ : <?php echo date("d/m/Y",strtotime($RST_HEADER['CreateDate'])); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center text-black">SKU BOOK (MK)</td>
                    </tr>
                </thead>
            </table>
            <div >
                <!-- 1. ข้อมูลสินค้า -->
                    <?php 
                    $SQL_SAP = "
                        SELECT T0.ItemCode, T0.ItemName, T0.FrgnName, T0.CodeBars, T1.Name AS NameType1, T2.Name AS NameType2, T3.CardName, T0.SalUnitMsr, T0.U_ProductStatus,T4.Name AS Brand,T5.Name AS Model
                        FROM OITM T0
                        LEFT JOIN dbo.[@ITEMGROUP1] T1 ON T1.Code = T0.U_Group1
                        LEFT JOIN dbo.[@ITEMGROUP2] T2 ON T2.Code = T0.U_Group2 
                        LEFT JOIN OCRD T3 ON T3.CardCode = T0.CardCode
                        LEFT JOIN dbo.[@BRAND2] T4 ON T4.Code = T0.U_Brand2
                        LEFT JOIN dbo.[@PROMOTION] T5 ON T5.Code = T0.U_Promotion_1
                        WHERE T0.ItemCode = '$ItemCode'";
                    $QRY_SAP = SAPSelect($SQL_SAP);
                    $RST_SAP = odbc_fetch_array($QRY_SAP);
                    ?>
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr> 
                                <th colspan='4'>
                                    1. ข้อมูลสินค้า
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr> 
                                <th width='15%' class='ps-4'>ประเภท (กลุ่มหลัก)</th>
                                <td width='35%'><?php echo conutf8($RST_SAP['NameType1']); ?></td>
                                <th width='15%'>ประเภท (กลุ่มรอง)</th>
                                <td width='35%'><?php echo conutf8($RST_SAP['NameType2']); ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>ชื่อภาษาไทย</th>
                                <td><?php echo conutf8($RST_SAP['ItemName']); ?></td>
                                <th>ชื่อภาษาอังกฤษ</th>
                                <td><?php echo conutf8($RST_SAP['FrgnName']); ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>รหัสสินค้า</th>
                                <td><?php echo $RST_SAP['ItemCode']; ?></td>
                                <th>Barcode</th>
                                <td><?php echo $RST_SAP['CodeBars']; ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>สถานะสินค้า</th>
                                <td><?php echo $RST_SAP['U_ProductStatus']; ?></td>
                                <th>รหัสทีมขาย</th>
                                <td><?php echo $RST_HEADER['TeamCode']; ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>รุ่น (Model)</th>
                                <td><?php echo conutf8($RST_SAP['Model']); ?></td>
                                <th>ยี่ห้อ</th>
                                <td><?php echo conutf8($RST_SAP['Brand']); ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>สีตัวสินค้า</th>
                                <td><?php echo $RST_HEADER['ItemColor']; ?></td>
                                <th>สีของบรรจุภัณฑ์</th>
                                <td><?php echo $RST_HEADER['BoxColor']; ?></td>
                            </tr>
                            <tr> 
                                <th class='ps-4'>ทำจากวัสดุ</th>
                                <td><?php echo $RST_HEADER['MadeOf']; ?></td>
                                <th>ประเทศผู้ผลิต</th>
                                <td><?php echo $RST_HEADER['ProCountry']; ?></td>
                            </tr>
                            <?php if($chk_uClass == 'Y') { ?>
                            <tr> 
                                <th class='ps-4'>ผู้ผลิต</th>
                                <td colspan='3'><?php echo conutf8($RST_SAP['CardName']); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <!-- 2. ราคาสินค้า -->
                    <?php 
                    $SQL2 = "SELECT T0.ItemCode, T0.P0,T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice, T0.MTPrice2, T0.MTPrice, T1.ItemName, 
                            T1.BarCode, T1.ProductStatus AS ST, T0.PriceType, T0.StartDate, T0.EndDate 
                        FROM pricelist T0
                        LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
                        WHERE T0.ItemCode NOT LIKE '%เก่า%' AND T0.ItemCode NOT LIKE '%ZZ%' AND T1.ItemName != '' AND T0.PriceStatus = 'A' AND T0.ItemCode = '$ItemCode' AND T0.PriceType = '$PriceType'";
                    $RST2 = MySQLSelect($SQL2);
                    $SQL3 =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END ) AS 'LastPurPrc', T0.LstEvlPric
                        FROM OITM T0 
                        LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode 
                        WHERE T0.ItemCode = '$ItemCode'";   
                    $QRY3 = SAPSelect($SQL3);
                    $RST3 = odbc_fetch_array($QRY3);
                    $SQL4 = "SELECT PriceType, S1Q, S1P, S2Q, S2P, StartDate, EndDate  FROM pricelist WHERE PriceStatus = 'A' AND ItemCode = '$ItemCode'";
                    $QRY4 = MySQLSelectX($SQL4);
                    $S1P = 0.00; $S1Q = 0.00; $S2P = 0.00; $S2Q = 0.00; $StartEndDate = "-";
                    while($RST4 = mysqli_fetch_array($QRY4)) {
                        if($RST4['PriceType'] == 'PRO'){
                            $S1P = $RST4['S1P']; 
                            $S1Q = number_format($RST4['S1Q'],0); 
                            $S2P = $RST4['S2P'];
                            $S2Q = number_format($RST4['S2Q'],0);
                            $StartEndDate = date("d/m/Y", strtotime($RST4['StartDate']))." <span class='fw-bolder'>&nbsp;&nbsp;ถึง&nbsp;&nbsp;</span> ".date("d/m/Y", strtotime($RST4['EndDate']));
                        }
                    }
                    $LastPurPrc = 0.00;
                    if(isset($RST3['LstEvlPric'])) {
                        $LastPurPrc = $RST3['LstEvlPric'];
                    }
                    ?>
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr>
                                <th>2. ราคาสินค้า</th>
                            </tr>
                        </thead>
                        <?php
                        $tbody_box2 = "
                            <tr>
                                <th width='14%' class='ps-4'>ราคาตั้ง</th>
                                <td width='12%' class='text-right'>";$tbody_box2 .= (isset($RST2['P0'])) ? number_format($RST2['P0'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <td width='20%'></td>
                                <td width='10%'></td>
                                <td width='20%'></td>
                                <td width='24%'></td>
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาปลีก</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['P1'])) ? number_format($RST2['P1'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาส่ง (SEMI)</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['P2'])) ? number_format($RST2['P2'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <td></td>
                                <td></td>";
                                if($chk_uClass == "Y") {
                                    $tbody_box2 .= "
                                    <th class='text-right'>GP</th>";
                                    if(isset($RST2['P2'])) {
                                        if($RST2['P2'] != 0)  { 
                                            $tbody_box2 .= "<td>".number_format(((($RST2['P2']-$LastPurPrc)/$RST2['P2'])*100),2)."%</td>";
                                        }else{ 
                                            $tbody_box2 .= "<td>0.00%</td>";
                                        }
                                    }else{
                                        $tbody_box2 .= "<td>0.00%</td>";
                                    }
                                }else{
                                    $tbody_box2 .= "
                                    <td></td>
                                    <td></td>";
                                }
                                $tbody_box2 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาส่ง (S1)</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['S1P'])) ? number_format($RST2['S1P'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <th class='text-right'>จำนวน</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['S1Q'])) ? number_format($RST2['S1Q'],0) : '0'; $tbody_box2 .= " ตัว</td>";
                                if($chk_uClass == "Y") {
                                    $tbody_box2 .= "
                                    <th class='text-right'>GP</th>";
                                    if(isset($RST2['S1P'])) {
                                        if($RST2['S1P'] != 0)  { 
                                            $tbody_box2 .= "<td>".number_format(((($RST2['S1P']-$LastPurPrc)/$RST2['S1P'])*100),2)."%</td>";
                                        }else{ 
                                            $tbody_box2 .= "<td>0.00%</td>";
                                        }
                                    }else{
                                        $tbody_box2 .= "<td>0.00%</td>";
                                    }
                                }else{
                                    $tbody_box2 .= "
                                    <td></td>
                                    <td></td>";
                                }
                                $tbody_box2 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาส่ง (S2)</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['S2P'])) ? number_format($RST2['S2P'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <th class='text-right'>จำนวน</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['S2Q'])) ? number_format($RST2['S2Q'],0) : '0'; $tbody_box2 .= " ตัว</td>";
                                if($chk_uClass == "Y") {
                                    $tbody_box2 .= "
                                    <th class='text-right'>GP</th>";
                                    if(isset($RST2['S2P'])) {
                                        if($RST2['S2P'] != 0)  { 
                                            $tbody_box2 .= "<td>".number_format(((($RST2['S2P']-$LastPurPrc)/$RST2['S2P'])*100),2)."%</td>";
                                        }else{ 
                                            $tbody_box2 .= "<td>0.00%</td>";
                                        }
                                    }else{
                                        $tbody_box2 .= "<td>0.00%</td>";
                                    }
                                }else{
                                    $tbody_box2 .= "
                                    <td></td>
                                    <td></td>";
                                }
                                $tbody_box2 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาส่ง (S3)</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['S3P'])) ? number_format($RST2['S3P'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <th class='text-right'>จำนวน</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['S3Q'])) ? number_format($RST2['S3Q'],0) : '0'; $tbody_box2 .= " ตัว</td>";
                                if($chk_uClass == "Y") {
                                    $tbody_box2 .= "
                                    <th class='text-right'>GP</th>";
                                    if(isset($RST2['S3P'])) {
                                        if($RST2['S3P'] != 0)  { 
                                            $tbody_box2 .= "<td>".number_format(((($RST2['S3P']-$LastPurPrc)/$RST2['S3P'])*100),2)."%</td>";
                                        }else{ 
                                            $tbody_box2 .= "<td>0.00%</td>";
                                        }
                                    }else{
                                        $tbody_box2 .= "<td>0.00%</td>";
                                    }
                                }else{
                                    $tbody_box2 .= "
                                    <td></td>
                                    <td></td>";
                                }
                                $tbody_box2 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคา (ผจก)</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['MgrPrice'])) ? number_format($RST2['MgrPrice'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <td></td>
                                <td></td>";
                                if($chk_uClass == "Y") {
                                    $tbody_box2 .= "
                                    <th class='text-right'>GP</th>";
                                    if(isset($RST2['MgrPrice'])) {
                                        if($RST2['MgrPrice'] != 0)  { 
                                            $tbody_box2 .= "<td>".number_format(((($RST2['MgrPrice']-$LastPurPrc)/$RST2['MgrPrice'])*100),2)."%</td>";
                                        }else{ 
                                            $tbody_box2 .= "<td>0.00%</td>";
                                        }
                                    }else{
                                        $tbody_box2 .= "<td>0.00%</td>";
                                    }
                                }else{
                                    $tbody_box2 .= "
                                    <td></td>
                                    <td></td>";
                                }
                                $tbody_box2 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาปลีก MT</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['MTPrice'])) ? number_format($RST2['MTPrice'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <td></td>
                                <td></td>";
                                if($chk_uClass == "Y") {
                                    $tbody_box2 .= "
                                    <th class='text-right'>GP</th>";
                                    if(isset($RST2['MTPrice'])) {
                                        if($RST2['MTPrice'] != 0)  { 
                                            $tbody_box2 .= "<td>".number_format(((($RST2['MTPrice']-$LastPurPrc)/$RST2['MTPrice'])*100),2)."%</td>";
                                        }else{ 
                                            $tbody_box2 .= "<td>0.00%</td>";
                                        }
                                    }else{
                                        $tbody_box2 .= "<td>0.00%</td>";
                                    }
                                }else{
                                    $tbody_box2 .= "
                                    <td></td>
                                    <td></td>";
                                }
                                $tbody_box2 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาส่ง MT</th>
                                <td class='text-right'>";$tbody_box2 .= (isset($RST2['MTPrice2'])) ? number_format($RST2['MTPrice2'],2) : '0.00'; $tbody_box2 .= " บาท</td>
                                <td></td>
                                <td></td>";
                                if($chk_uClass == "Y") {
                                    $tbody_box2 .= "
                                    <th class='text-right'>GP</th>";
                                    if(isset($RST2['MTPrice2'])) {
                                        if($RST2['MTPrice2'] != 0)  { 
                                            $tbody_box2 .= "<td>".number_format(((($RST2['MTPrice2']-$LastPurPrc)/$RST2['MTPrice2'])*100),2)."%</td>";
                                        }else{ 
                                            $tbody_box2 .= "<td>0.00%</td>";
                                        }
                                    }else{
                                        $tbody_box2 .= "<td>0.00%</td>";
                                    }
                                }else{
                                    $tbody_box2 .= "
                                    <td></td>
                                    <td></td>";
                                }
                                $tbody_box2 .= "
                            </tr>";
                            if($chk_uClass == "Y") {
                                $tbody_box2 .= "
                                <tr>
                                    <td colspan='3' class='ps-4'><span class='fw-bolder'>ต้นทุนปัจจุบัน</span> ".number_format($LastPurPrc,2)." บาท</td>
                                    <td colspan='3'><span class='fw-bolder'>ต้นทุก Lot ที่ผ่านมา</span> ".number_format($RST3['LastPurPrc'],2)." บาท</td>
                                </tr>";
                            }
                        echo $tbody_box2;
                        ?>
                    </table>
                <!-- 3. โปรโมชั่น -->
                    <?php 
                    $SQL5 = "
                        SELECT T0.TeamCode,T1.ItemCode,
                            SUM(IFNULL(T1.Tar_M01,0)+
                            IFNULL(T1.Tar_M02,0)+
                            IFNULL(T1.Tar_M03,0)+
                            IFNULL(T1.Tar_M04,0)+
                            IFNULL(T1.Tar_M05,0)+
                            IFNULL(T1.Tar_M06,0)+
                            IFNULL(T1.Tar_M07,0)+
                            IFNULL(T1.Tar_M08,0)+
                            IFNULL(T1.Tar_M09,0)+
                            IFNULL(T1.Tar_M10,0)+
                            IFNULL(T1.Tar_M11,0)+
                            IFNULL(T1.Tar_M12,0)) as Target 
                        FROM tarsku_header T0
                        LEFT JOIN tarsku_detail T1 ON T0.CPEntry = T1.CPEntry
                        WHERE (DATE(NOW()) BETWEEN T0.StartDate AND T0.EndDate) AND T0.CANCELED = 'N' AND T1.TargetStatus = 'A'  AND ItemCode = '$ItemCode'
                        GROUP BY T0.TeamCode,T1.ItemCode";
                    $QRY5 = MySQLSelectX($SQL5);
                    $MT1 = 0; $MT2 = 0; $TT2 = 0; $OULTT1 = 0; $ONL = 0;
                    while($RST5 = mysqli_fetch_array($QRY5)) {
                        switch($RST5['TeamCode']) {
                            case 'MT1': $MT1 = $MT1+$RST5['Target']; break;
                            case 'MT2': $MT2 = $MT2+$RST5['Target']; break;
                            case 'TT2': $TT2 = $TT2+$RST5['Target']; break;
                            case 'OUL': 
                            case 'TT1': $OULTT1 = $OULTT1+$RST5['Target']; break;
                            case 'ONL': $ONL = $ONL+$RST5['Target']; break;
                        }
                    }
                    $SQL6 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '9'";
                    $QRY6 = MySQLSelectX($SQL6);
                    ?>
                    <table class='table table-sm table-borderless border border-dark border-bottom-1 m-0'>
                        <thead>
                            <tr>
                                <th>3. โปรโมชั่น</th>
                            </tr>
                        </thead>
                        <?php
                        $tbody_box3 = "
                            <tr>
                                <th width='15%' class='ps-4'>ราคาปลีก</th>
                                <td width='12%' class='text-right'>";
                                    if(isset($RST2['P1'])) {
                                        $tbody_box3 .= number_format($RST2['P1'],2)." บาท";
                                    }else{
                                        $tbody_box3 .= "- บาท";
                                    }
                                $tbody_box3 .= "
                                </td>
                                <th width='20%' class='text-right'>จำนวน</th>
                                <td width='10%'>1 ตัว</td>
                                <th width='20%' class='text-right'>GP</th>";
                                if(isset($RST2['P1'])) {
                                    if($RST2['P1'] != 0)  { 
                                        $tbody_box3 .= "<td width='24%'>".number_format(((($RST2['P1']-$LastPurPrc)/$RST2['P1'])*100),2)."%</td>";
                                    }else{ 
                                        $tbody_box3 .= "<td width='24%'>0.00%</td>";
                                    }
                                }else{
                                    $tbody_box3 .= "<td width='24%'>0.00%</td>";
                                }
                                
                            $tbody_box3 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาพิเศษ 1</th>
                                <td class='text-right'>$S1P บาท</td>
                                <th class='text-right'>จำนวน</th>
                                <td>$S1Q ตัว</td>
                                <th class='text-right'>GP</th>";
                                if($S1P > 0)  { 
                                    $tbody_box3 .= "<td>".number_format(((($S1P-$LastPurPrc)/$S1P)*100),2)."%</td>";
                                }else{ 
                                    $tbody_box3 .= "<td>0.00%</td>";
                                }
                            $tbody_box3 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ราคาพิเศษ 2</th>
                                <td class='text-right'>$S2P บาท</td>
                                <th class='text-right'>จำนวน</th>
                                <td>$S2Q ตัว</td>
                                <th class='text-right'>GP</th>";
                                if($S2P > 0)  { 
                                    $tbody_box3 .= "<td>".number_format(((($S2P-$LastPurPrc)/$S2P)*100),2)."%</td>";
                                }else{ 
                                    $tbody_box3 .= "<td>0.00%</td>";
                                }
                            $tbody_box3 .= "
                            </tr>
                            <tr>
                                <th class='ps-4'>ระยะเวลา</th>
                                <td colspan='4'>$StartEndDate</td>
                            </tr>
                            <tr>
                                <th colspan='6' class='ps-4'>เป้าต่อเดือน</th>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan='2'>
                                    <table class='table table-sm table-borderless' style='font-size: 12px;'>
                                        <tr>
                                            <th width='60%' class='text-start border'>MT1</th>
                                            <td width='30%' class='text-right border'>$MT1</td>
                                            <td width='10%' class='border'>ตัว</td>
                                        </tr>
                                        <tr>
                                            <th class='text-start border'>MT2</th>
                                            <td class='text-right border'>$MT2</td>
                                            <td class='border'>ตัว</td>
                                        </tr>
                                        <tr>
                                            <th class='text-start border'>TT2</th>
                                            <td class='text-right border'>$TT2</td>
                                            <td class='border'>ตัว</td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan='2'>
                                    <table class='table table-sm table-borderless' style='font-size: 12px;'>
                                        <tr>
                                            <th width='60%' class='text-start border'>OUL/TT1</th>
                                            <td width='30%' class='text-right border'>$OULTT1</td>
                                            <td width='10%' class='border'>ตัว</td>
                                        </tr>
                                        <tr>
                                            <th class='text-start border'>ONL</th>
                                            <td class='text-right border'>$ONL</td>
                                            <td class='border'>ตัว</td>
                                        </tr>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </td>
                                <td></td>
                            </tr>";
                            while($RST6 = mysqli_fetch_array($QRY6)) {
                                $Detail = "-";
                                if($RST6['Detail'] != "" || $RST6['Detail'] != null) {
                                    $Detail = $RST6['Detail'];
                                }
                                $tbody_box3 .= "
                                <tr>
                                    <th class='ps-4'>".$RST6['Header']."</th>
                                    <td colspan='4'>$Detail</td>
                                </tr>
                                ";
                            }
                            echo $tbody_box3;
                        ?>
                    </table>
                </div>
        </div>
        <div class="page"> <!-- หน้า 2 -->
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
                        <td width="" class="align-top">
                            <span class='text-left fw-bolder'>หน้าที่ : 2</span><br>
                            <span class='text-left fw-bolder'>เลขที่ SKU : <?php echo $ItemCode; ?></span><br>
                            <span class='text-left fw-bolder'>วันที่ : <?php echo date("d/m/Y",strtotime($RST_HEADER['CreateDate'])); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center text-black">SKU BOOK (MK)</td>
                    </tr>
                </thead>
            </table>
            <div>
                <!-- 4. ช่องทางการขายสินค้า -->
                    <?php 
                    $SQL7 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '10'";
                    $QRY7 = MySQLSelectX($SQL7); 
                    $tbody_box4_sub1 = ""; $tbody_box4_sub2 = ""; $Remark = ""; $i = 0; $tmpID_Type10 = "";
                    ?>
                    <table class='table table-sm table-borderless border border-dark border-bottom-0 m-0'>
                        <thead>
                            <tr>
                                <th colspan='2'>4. ช่องทางการขายสินค้า</th>
                            </tr>
                        </thead>
                        <?php
                        while($RST7 = mysqli_fetch_array($QRY7)) {
                            $checked = "";
                            if($RST7['CheckBox'] == 'Y') { $checked = "<i class='fas fa-check fa-fw'></i>"; }
                            if($RST7['Header'] == 'ช่องทางขาย') {
                                $tbody_box4_sub1 .= "
                                <tr>
                                    <th class='text-start border'>".$RST7['Detail']."</th>
                                    <td class='border'>$checked</td>
                                </tr>
                                ";
                            }else{
                                $tbody_box4_sub2 .= "
                                <tr>
                                    <th class='text-start border'>".$RST7['Detail']."</th>
                                    <td class='border'>$checked</td>
                                </tr>
                                ";
                
                                if($RST7['Detail'] == 'อื่นๆ') {
                                    $Remark = $RST7['Remark'];
                                }
                            }
                        }
                        $tbody_box4 = "
                            <tr>
                                <th colspan='3' class='text-center ps-4'>ช่องทางขาย</th>
                                <th colspan='2' class='text-center'>ช่องทางลูกค้า</th>
                                <td ></td>
                            </tr>
                            <tr>
                                <td width='15%'></td>
                                <td class='text-center ps-4'>
                                    <table class='table table-sm table-borderless' style='font-size: 12px;'>
                                        $tbody_box4_sub1
                                        <tr>
                                            <th>&nbsp;</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <td></td>
                                        </tr>
                                    </table>
                                </td>
                                <td></td>
                                <td class='text-center ps-4' colspan='2'>
                                    <table class='table table-sm table-borderless' style='font-size: 12px;'>
                                        $tbody_box4_sub2
                                    </table>
                                </td>
                                <td width='15%' class='align-bottom' style='padding-bottom: 22px;'>ระบุ $Remark</td>
                            </tr>";
                            echo $tbody_box4;
                        ?>
                    </table>
                <!-- 5. VDO Utility -->
                    <?php 
                    $SQL8 = "SELECT * FROM skubook_detail WHERE ItemCode = '$ItemCode' AND Type = '11'";
                    $tbody_box5 = ""; $ChkDataType11 = 'Y'; $tmpID_Type11 = "";
                    $QRY8 = MySQLSelectX($SQL8);
                    ?>
                    <table class='table table-sm table-borderless border border-dark border-bottom-1 m-0'>
                        <thead>
                            <tr>
                                <th colspan='2'>5. VDO Utility</th>
                            </tr>
                        </thead>
                        <?php
                        $i = 0; $r = 0;
                        while($RST8 = mysqli_fetch_array($QRY8)) {
                            $r++;
                            $tbody_box5 .= "
                            <tr>
                                <th width='8%' class='ps-4 align-middle'>$r</th>
                                <td>
                                    <span class='fw-bolder'>".$RST8['Header']."</span>
                                    <br>
                                    ".$RST8['Detail']."
                                </td>
                            </tr>";
                        }
                        if($r == 0) {
                            $tbody_box5 .= "
                            <tr>
                                <td class='text-center'>ยังไม่มีข้อมูล</td>
                            </tr>";
                        }
                        echo $tbody_box5;
                        ?>
                    </table>
            </div>
        </div>
        <div class="page"> <!-- หน้า 3 -->
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
                        <td width="" class="align-top">
                            <span class='text-left fw-bolder'>หน้าที่ : 3</span><br>
                            <span class='text-left fw-bolder'>เลขที่ SKU : <?php echo $ItemCode; ?></span><br>
                            <span class='text-left fw-bolder'>วันที่ : <?php echo date("d/m/Y",strtotime($RST_HEADER['CreateDate'])); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center text-black">SKU BOOK (MK)</td>
                    </tr>
                </thead>
            </table>
            <div>
                <table class='table table-sm table-borderless border border-dark m-0 h-100'>
                    <tbody>
                        <?php 
                        $SQL = "SELECT * FROM skubook_attach WHERE ItemCode = '$ItemCode' AND FileStatus = 'A' AND Type IN (6,7,8)";
                        $QRY = MySQLSelectX($SQL);
                        while($RST = mysqli_fetch_array($QRY)) {
                            $Img[$RST['Type']][$RST['VisOrder']] = $RST['FileDirName'].'.'.$RST['FileExt'];
                        }
                        $NameImg[6] = "รูปสินค้าตัวจริง";
                        $NameImg[7] = "รูปแพ็คเกจตัวจริง";
                        $NameImg[8] = "รูปอุปกรณ์ภายในกล่องตัวจริง";
                        $NameImg[9] = "ใบโปร/ใบขาย";
                        $n = 0;

                        for($i = 6; $i <= 9; $i++) {
                            if($i == 6) {
                                if(isset($Img[$i][0])){
                                    for($j = 0; $j <= count($Img[$i])-1; $j++) {
                                        $n++;
                                        if($n == 1) { echo "<tr>"; }
                                        if($n <= 3) {
                                            echo "
                                            <td class='text-center'>
                                                <img style='width: 150px;' src='../../../../image/products/$ItemCode/$i/".$Img[$i][$j]."'/>
                                                <div class='fw-bolder pt-1'>".$NameImg[$i]." ".($j+1)."</div>
                                            </td>";
                                        }
                                        if($n == 3) { echo "</tr>"; $n = 0; }
                                    }
                                }else{
                                    $n++;
                                    if($n == 1) { echo "<tr>"; }
                                    if($n <= 3) {
                                        echo "
                                        <td class='text-center'>
                                            <img style='width: 150px;' src='../../../../image/products/no-image.jpg'/>
                                            <div class='fw-bolder pt-1'>".$NameImg[$i]."</div>
                                        </td>";
                                    }
                                    if($n == 3) { echo "</tr>"; $n = 0; }
                                }
                            }else{
                                $n++;
                                if($n == 1) {
                                    echo "<tr>";
                                }
                                if($n <= 3) {
                                    if(isset($Img[$i][0])){
                                        echo "
                                        <td class='text-center'>
                                            <img style='width: 150px;' src='../../../../image/products/$ItemCode/$i/".$Img[$i][0]."'/>
                                            <div class='fw-bolder pt-1'>".$NameImg[$i]."</div>
                                        </td>";
                                    }else{
                                        echo "
                                        <td class='text-center'>
                                            <img style='width: 150px;' src='../../../../image/products/no-image.jpg'/>
                                            <div class='fw-bolder pt-1'>".$NameImg[$i]."</div>
                                        </td>";
                                    }
                                }
                                if($n == 3) { echo "</tr>"; $n = 0; }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
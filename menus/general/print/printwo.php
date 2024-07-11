<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

function ShipmentType($ShipType) {
	switch($ShipType) {
		case 1:  $TypeName = "บริษัทฯ เป็นผู้จ่ายค่าขนส่ง"; break;
		case 2:  $TypeName = "ปลายทางเป็นผู้จ่ายค่าขนส่ง"; break;
		default: $TypeName = "ไม่มีค่าใช้จ่าย"; break;
	}
	return $TypeName;
}

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
    if(!isset($_GET['DocEntry'])) {
        echo '<script type="text/javascript">window.location="../../../../"; </script>';
    } else {
        switch($_GET['Type']) {
            case "B":
                /* Page Settings */
                $PageHeader = "ใบเบิกสินค้าจากคลังใหญ่เข้าหน้าร้าน";
                $pageWidth  = "297mm";
                $pageHeight = "210mm";
                $pageSize   = " landscape";
                $ImgLogo    = "12.5%";
                /* Content */
                $GetSQL = 
                    "SELECT
                        T0.DocNum, CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName', DATE(T0.DateCreate) AS 'DocDate', T0.Remark AS 'DocDetail',
                        T1.ItemCode, T1.ItemName, T1.WhsCode, T1.Qty, T1.UnitMgr, T1.Remark AS 'RowDetail',
                        CONCAT(T4.uName,' ',T4.uLastName,' (',T4.uNickName,')') AS 'PickerName', T3.TablePacking
                    FROM OWAS T0
                    LEFT JOIN WAS1 T1 ON T0.DocEntry = T1.DocEntry
                    LEFT JOIN users T2 ON T0.UserCreate = T2.ukey
                    LEFT JOIN picker_soheader T3 ON T0.DocEntry = T3.SODocEntry AND T3.DocType LIKE 'OWA%'
                    LEFT JOIN users T4 ON T3.UkeyPicker = T4.uKey
                    WHERE T0.DocEntry = ".$_GET['DocEntry']." AND T0.TypeOrder = 'B'";
                $Rows   = ChkRowDB($GetSQL);
                $GetQRY = MySQLSelectX($GetSQL);
                $i      = 1;
                while($GetRST = mysqli_fetch_array($GetQRY)) {
                    $DocNum     = $GetRST['DocNum'];
                    $CreateName = $GetRST['CreateName'];
                    $PickerName = $GetRST['PickerName'];
                    $PackTable  = $GetRST['TablePacking'];
                    $CreateDate = date("d/m/Y",strtotime($GetRST['DocDate']));
                    $DocDetail     = $GetRST['DocDetail'];
                    ${"ItemCode_".$i}  = $GetRST['ItemCode'];
                    ${"ItemName_".$i}  = $GetRST['ItemName'];
                    ${"WhsCode_".$i}   = $GetRST['WhsCode'];
                    ${"Qty_".$i}       = $GetRST['Qty'];
                    ${"UnitMgr_".$i}   = $GetRST['UnitMgr'];
                    ${"RowDetail_".$i} = $GetRST['RowDetail'];
                    $i++;
                }
                break;
            default:
                /* Page Settings */
                $PageHeader = "ใบฝากงานคลังสินค้า";
                $pageWidth  = "210mm";
                $pageHeight = "297mm";
                $pageSize   = NULL;
                $ImgLogo    = "20%";
                /* Content */
                $GetSQL =
                    "SELECT
                            T0.DocNum, DATE(T0.DateCreate) AS 'DocDate', DATE(T0.TimeContrac) AS 'DocDueDate',
                            CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName', T4.DeptName,
                            T0.TypeOrder, T0.TypeDetail,
                            T0.CusCode, T0.CusName, T0.CusAddress, T0.NameContrac, T0.Phone, T0.Remark AS 'DocDetail',
                            T1.ItemCode, T1.ItemName, T1.Qty, T1.UnitMgr, T1.Remark AS 'RowDetail',
                            T0.LogiName, T0.LogiPhone, T0.AddressContrac, T0.TotalBox, T0.PaidType, T0.PaidTotal,
                            CONCAT(T6.uName,' ',T6.uLastName,' (',T6.uNickName,')') AS 'PickerName', T5.TablePacking
                    FROM OWAS T0
                    LEFT JOIN WAS1 T1 ON T0.DocEntry = T1.DocEntry
                    LEFT JOIN users T2 ON T0.UserCreate = T2.uKey
                    LEFT JOIN positions T3 ON T2.LvCode = T3.LvCode
                    LEFT JOIN departments T4 ON T3.DeptCode = T4.DeptCode
                    LEFT JOIN picker_soheader T5 ON T0.DocEntry = T5.SODocEntry AND T5.DocType LIKE 'OWA%'
                    LEFT JOIN users T6 ON T5.UkeyPicker = T6.uKey
                    WHERE T0.DocEntry = ".$_GET['DocEntry'];
                $Rows = ChkRowDB($GetSQL);
                $GetQRY = MySQLSelectX($GetSQL);
                $i = 1;
                while($GetRST = mysqli_fetch_array($GetQRY)) {
                    $DocNum = $GetRST['DocNum'];
                    $DocDate = date("d/m/Y",strtotime($GetRST['DocDate']));
                    $CreateName = $GetRST['CreateName'];
                    $DeptName = $GetRST['DeptName'];
                    switch($GetRST['TypeOrder']) {
                        case "R":
                            $IconType_R = "<i class='far fa-check-square fa-fw fa-lg'></i>";
                            $IconType_S = "<i class='far fa-square fa-fw fa-lg'></i>";
                            break;
                        case "S":
                            $IconType_R = "<i class='far fa-square fa-fw fa-lg'></i>";
                            $IconType_S = "<i class='far fa-check-square fa-fw fa-lg'></i>";
                            break;
                    }
                    $IconType_RP = "<i class='far fa-square fa-fw fa-lg'></i>";
                    $IconType_RD = "<i class='far fa-square fa-fw fa-lg'></i>";
                    $IconType_RR = "<i class='far fa-square fa-fw fa-lg'></i>";
                    $IconType_SP = "<i class='far fa-square fa-fw fa-lg'></i>";
                    $IconType_SQ = "<i class='far fa-square fa-fw fa-lg'></i>";
                    ${"IconType_".$GetRST['TypeDetail']} = "<i class='far fa-check-square fa-fw fa-lg'></i>";
                    if($GetRST['CusCode'] == "") {
                        $CardCode = $GetRST['CusName'];
                    } else {
                        $CardCode = $GetRST['CusCode']." | ".$GetRST['CusName'];
                    }
                    if($GetRST['DocDueDate'] == "NULL") {
                        $DocDueDate = NULL;
                    } else {
                        $DocDueDate = date("d/m/Y",strtotime($GetRST['DocDueDate']));
                    }
                    $NameContact  = $GetRST['NameContrac'];
                    $PhoneContact = $GetRST['Phone'];
                    $CusAddress   = $GetRST['CusAddress'];
                    $DocDetail    = $GetRST['DocDetail'];
                    $LogiName     = $GetRST['LogiName'];
                    $LogiPhone    = $GetRST['LogiPhone'];
                    $LogiAddress  = $GetRST['AddressContrac'];
                    $LogiType     = ShipmentType($GetRST['PaidType']);
                    $LogiCost     = number_format($GetRST['PaidTotal'],2);
                    $PickerName   = $GetRST['PickerName'];
                    $PackTable    = $GetRST['TablePacking'];

                    ${"ItemCode_".$i}  = $GetRST['ItemCode'];
                    ${"ItemName_".$i}  = $GetRST['ItemName'];
                    ${"Qty_".$i}       = $GetRST['Qty'];
                    ${"UnitMgr_".$i}   = $GetRST['UnitMgr'];
                    ${"RowDetail_".$i} = $GetRST['RowDetail'];

                    $i++;
                }
                break;
        } ?>
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
                        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
                        html, body {
                            background-color: #FFFFFF;
                            font-family: 'Sarabun';
                            font-weight: 300;
                            color: #000 !important;
                            font-size: 11px;
                        }

                        h1,h2,h3,h4,h5,h6 {
                            color: #000 !important;
                            padding: 0;
                            margin: 0;
                            font-weight: 600;
                        }
                        .page {
                            /* margin: 3mm;
                            width: 204mm;
                            height: 291mm; */
                            /* border: 1px dashed #000; */
                            width: <?php echo $pageWidth; ?>;
                            height: <?php echo $pageHeight; ?>;
                            display: block;
                            margin: 3mm auto;
                            padding: 3mm;
                            box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
                        }
                        @page {
                            size: A4 <?php echo $pageSize ?>;
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
                        <!-- PAGE HEADER -->
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <thead>
                                <tr>
                                    <td width="<?php echo $ImgLogo; ?>" class="text-center">
                                        <img src="../../../../image/logo/kbi_logo.png" class="img-fluid" />
                                    </td>
                                    <td>
                                        <h4>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                                        <small>
                                            541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                            เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856
                                        </small>
                                    </td>
                                    <td width="10%" class="align-middle text-center"><svg id="sobarcode"></svg></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-center"><h5 style="margin: 1rem;"><?php echo $PageHeader; ?></h5></td>
                                </tr>
                            </thead>
                        </table>
                    <?php
                        /* DOCHEADER */
                    switch($_GET['Type']) {
                        case "B": ?>
                        <table class="table table-borderless table-sm">
                            <thead>
                                <tr>
                                    <th width="12.5%">เลขที่ใบฝากงาน:</th>
                                    <td width="12.5%"><?php echo $DocNum; ?></td>
                                    <th width="12.5%">ชื่อผู้เบิก:</th>
                                    <td><?php echo $CreateName; ?></td>
                                    <th width="12.5%">วันที่ฝากงาน:</th>
                                    <td width="12.5%"><?php echo $CreateDate; ?></td>
                                </tr>
                                <tr>
                                    <th>หมายเหตุ:</th>
                                    <td colspan="5" class="text-danger"><?php echo $DocDetail; ?></td>
                                </tr>
                            </thead>
                        </table>
                        <table class="table table-bordered table-sm border-dark">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">ลำดับที่</th>
                                    <th width="10%">รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th width="5%">คลัง</th>
                                    <th width="6%">จำนวน</th>
                                    <th width="6%">หน่วย</th>
                                    <th width="25%">หมายเหตุ</th>
                                </tr>
                            <tbody>
                            <?php
                            for($i=1;$i<=$Rows;$i++) {
                                echo "<tr>";
                                    echo "<td class='text-right'>$i</td>";
                                    echo "<td class='text-center'>".${"ItemCode_".$i}."</td>";
                                    echo "<td>".${"ItemName_".$i}."</td>";
                                    echo "<td class='text-center'>".${"WhsCode_".$i}."</td>";
                                    echo "<td class='text-right'>".number_format(${"Qty_".$i},0)."</td>";
                                    echo "<td>".${"UnitMgr_".$i}."</td>";
                                    echo "<td>".${"RowDetail_".$i}."</td>";
                                echo "</tr>";
                            }
                            for($b=1;$b<=15-$Rows;$b++) {
                                echo "<tr>";
                                    echo "<td class='text-right'>&nbsp;</td>";
                                    echo "<td class='text-center'>&nbsp;</td>";
                                    echo "<td>&nbsp;</td>";
                                    echo "<td class='text-center'>&nbsp;</td>";
                                    echo "<td class='text-right'>&nbsp;</td>";
                                    echo "<td>&nbsp;</td>";
                                    echo "<td>&nbsp;</td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <table class="table table-borderless table-sm">
                            <thead>
                                <tr>
                                    <th width="12.5%">ผู้เบิกสินค้า:</th>
                                    <td width="12.5%"><?php echo $PickerName; ?></td>
                                    <th width="12.5%">โต๊ะจัดสินค้า:</th>
                                    <td><?php echo $PackTable; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4"><small>FM-SA-09 Rev. 02 วันที่บังคับใช้: 1 มกราคม 2566 อายุการจัดเก็บ: อย่างน้อย 1 ปี วิธีทำลาย: ขีดคร่อม/Re-Use</small></td>
                                </tr>
                            </thead>
                        </table>
                        <?php break;
                        default: ?>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="15%">เลขที่ใบฝากงาน:</th>
                                <td style="border-bottom: 1px dotted #000;"><?php echo $DocNum; ?></td>
                                <th width="15%">วันที่ฝากงาน</th>
                                <td width="20%" style="border-bottom: 1px dotted #000;"><?php echo $DocDate; ?></td>
                            </tr>
                            <tr>
                                <th>ผู้จัดทำ:</th>
                                <td style="border-bottom: 1px dotted #000;"><?php echo $CreateName; ?></td>
                                <th>ฝ่าย:</th>
                                <td style="border-bottom: 1px dotted #000;"><?php echo $DeptName; ?></td>
                            </tr>
                            <tr>
                                <th>ประเภทการฝากงาน:</th>
                                <td colspan="3">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="33%"><?php echo $IconType_R; ?> ฝากรับสินค้า</td>
                                            <td width="33%"><?php echo $IconType_S; ?> ฝากส่งสินค้า</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <th>เลือกงานย่อย:</th>
                                <td colspan="3">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="33%"><?php echo $IconType_RP; ?> ฝากรับสินค้าที่ฝากซื้อ</td>
                                            <td width="33%"><?php echo $IconType_RD; ?> รับสินค้าคืนที่ MT / ขนส่ง / ไปรษณีย์</td>
                                            <td><?php echo $IconType_RR; ?> ฝากรับสินค้าซ่อม</td>
                                        </tr>
                                        <tr>
                                            <td width="33%"><?php echo $IconType_SP; ?> ฝากส่งสินค้าให้ลูกค้า</td>
                                            <td width="33%"><?php echo $IconType_SQ; ?> ฝากส่งสินค้าที่ไม่รับคืน เคลม เปลี่ยน</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <th valign="bottom">รายละเอียดฝากงาน:</th>
                                <td colspan="3" style="font-size: 14px; font-weight: 400; border-bottom: 1px dotted #000;" class="text-danger"><?php echo $DocDetail; ?></td>
                            </tr>
                            <tr>
                                <th height="32px" colspan="4" class="align-middle text-center">*** การฝากงานคลังสินค้า ไม่รับส่งสินค้าซ่อม งานซ่อมออกบิลขาย (HA, IV) เท่านั้น ***</th>
                            </tr>
                            <tr>
                                <th height="24px" colspan="4" class="align-bottom">ข้อมูลการติดต่อ</th>
                            </tr>
                            <tr>
                                <th>ชื่อลูกค้า:</th>
                                <td style="border-bottom: 1px dotted #000;"><?php echo $CardCode; ?></td>
                                <th>วันที่นัดหมาย:</th>
                                <td class="text-danger" style="border-bottom: 1px dotted #000;"><?php echo $DocDueDate; ?></td>
                            </tr>
                            <tr>
                                <th>บุคคลหรือฝ่ายที่ติดต่อ:</th>
                                <th class="text-danger" style="border-bottom: 1px dotted #000;"><?php echo $NameContact; ?></th>
                                <th>เบอร์โทร.ติดต่อ</th>
                                <th class="text-danger" style="border-bottom: 1px dotted #000;"><?php echo $PhoneContact; ?></th>
                            </tr>
                            <tr>
                                <th>ที่อยู่ปลายทาง:</th>
                                <th colspan="3" class="text-danger" style="border-bottom: 1px dotted #000;"><?php echo $CusAddress; ?></th>
                            </tr>
                            <tr>
                                <th height="24px" colspan="4" class="align-bottom">ข้อมูลผู้ให้บริการขนส่ง</th>
                            </tr>
                            <tr>
                                <th>ผู้ให้บริการขนส่ง:</th>
                                <td style="border-bottom: 1px dotted #000;"><?php echo $LogiName; ?></td>
                                <th>เบอร์โทร.ติดต่อ</th>
                                <td style="border-bottom: 1px dotted #000;"><?php echo $LogiPhone; ?></td>
                            </tr>
                            <tr>
                                <th>ที่อยู่ผู้ให้บริการขนส่ง:</th>
                                <td style="border-bottom: 1px dotted #000;"><?php echo $LogiAddress; ?></td>
                                <th>ค่าใช้จ่ายในการขนส่ง:</th>
                                <th class="text-danger" style="border-bottom: 1px dotted #000;"><?php echo $LogiType; ?> (<?php echo $LogiCost; ?> บาท)</th>
                            </tr>
                            <tr>
                                <th height="36px" colspan="4" class="align-bottom text-center">รายการสินค้าที่ฝากให้คลังสินค้าส่งหรือรับ</th>
                            </tr>
                        </table> 
                        <table class="table table-bordered table-sm border-dark">
                            <tr class="text-center">
                                <th width="7.5%">ลำดับ</th>
                                <th width="11.5%">รหัสสินค้า</th>
                                <th>ชื่อสินค้า</th>
                                <th width="7.5%">จำนวน</th>
                                <th width="6.5%">หน่วย</th>
                                <th width="35%">หมายเหตุ</th>
                            </tr>
                        <?php
                        for($r=1;$r<=$Rows;$r++) {
                            echo "<tr>";
                                echo "<td class='text-right'>$r</td>";
                                echo "<td class='text-center'>".${"ItemCode_".$r}."</td>";
                                echo "<td>".${"ItemName_".$r}."</td>";
                                echo "<td class='text-right'>".number_format(${"Qty_".$r},0)."</td>";
                                echo "<td>".${"UnitMgr_".$r}."</td>";
                                echo "<td>".${"RowDetail_".$r}."</td>";
                            echo "</tr>";
                        }
                        for($b=1;$b<=10-$Rows;$b++) {
                            echo "<tr>";
                                echo "<td class='text-right'>&nbsp;</td>";
                                echo "<td class='text-center'>&nbsp;</td>";
                                echo "<td>&nbsp;</td>";
                                echo "<td class='text-right'>&nbsp;</td>";
                                echo "<td>&nbsp;</td>";
                                echo "<td>&nbsp;</td>";
                            echo "</tr>";
                        }
                        ?>
                        </table>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="15%">ผู้เบิกสินค้า:</th>
                                <td><?php echo $PickerName; ?></td>
                                <th width="15%">โต๊ะจัดสินค้า</th>
                                <td width="20%"><?php echo $PackTable; ?></td>
                            </tr>
                        </table>
                        <table class="table table-bordered border-dark table-sm">
                            <tr class="text-center">
                                <th width="50%">สำหรับลูกค้า</th>
                                <th width="50%">สำหรับพนักงาน</th>
                            </tr>
                            <tr class="text-center">
                                <td>
                                    ข้าพเจ้าตรวจสอบแล้วว่าพนักงานเข้ารับ/ส่งสินค้าจริง<br/><br/><br/>
                                    ลงชื่อ: ....................................................... ผู้รับ/ส่งสินค้า<br/>
                                    วันที่: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </td>
                                <td>
                                    ยืนยันการเข้ารับ/ส่งสินค้าจริง<br/><br/><br/>
                                    ลงชื่อ: ....................................................... พนักงานขนส่ง<br/>
                                    วันที่: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><small>FM-WH-02 Rev. 05 วันที่บังคับใช้: 1 มกราคม 2566 อายุการจัดเก็บ: อย่างน้อย 2 ปี วิธีทำลาย: ย่อยทิ้ง</small></td>
                            </tr>
                        </table>
                        <?php break;
                    }
                    ?>
                    </div>
                    <script type="text/javascript">
                        var docnum = '<?php echo $DocNum; ?>';
                        //var barcode = '';
                        // var pkbarcode = '';
                        JsBarcode("#sobarcode", docnum, { width: 1.25, height: 24, fontSize: 10, marginTop: 0, marginBottom: 0, text: docnum });
                        // JsBarcode("#pkbarcode", pkbarcode, { width: 2, height: 24, fontSize: 12, marginTop: 0, marginBottom: 0, text: pkbarcode });
                    </script>
                </body>
            </html>
    <?php }
}
?>
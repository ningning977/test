<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
    if(!isset($_GET['DocEntry'])) {
        echo '<script type="text/javascript">window.location="../../../../"; </script>';
    } else {
        $DocEntry = $_GET['DocEntry'];
        $pages = 1;
        $PageHeader = "SA04: ส่วนลดหนี้ / ลดจ่าย";

        $IconRemark_1 = "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconRemark_2 = "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconRemark_3 = "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconRemark_4 = "<i class='far fa-square fa-fw fa-lg'></i>";
        $RemarkText   = NULL;

        $IconAttach_1 =  "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconAttach_2 =  "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconAttach_3 =  "<i class='far fa-square fa-fw fa-lg'></i>";
        $RemarkAttach = NULL;

        $IconDocTypeA = "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconDocTypeB = "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconDocTypeC = "<i class='far fa-square fa-fw fa-lg'></i>";

        $IconFineSA   = "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconFineCO   = "<i class='far fa-square fa-fw fa-lg'></i>";

        $IconSNRAppY  = "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconSNRAppN  = "<i class='far fa-square fa-fw fa-lg'></i>";
        $SNRSign      = "";
        $SNRRemark    = NULL;
        $SNRName      = NULL;
        $SNRDate      = NULL;
        $IconMGRAppY  = "<i class='far fa-square fa-fw fa-lg'></i>";
        $IconMGRAppN  = "<i class='far fa-square fa-fw fa-lg'></i>";
        $MGRSign      = "";
        $MGRRemark    = NULL;
        $MGRName      = NULL;
        $MGRName      = NULL;

        $DocNum         = NULL;
        $DocDate        = NULL;
        $CardCode       = NULL;
        $BillDocNum     = NULL;
        $BillDocDate    = NULL;
        $BillDocDueDate = NULL;
        $BillSlpName    = NULL;
        $BillCoName     = NULL;
        $CreateName     = NULL;
        $CreateDate     = NULL;

        /* ZONE A */
        $A_BillDocTotal = NULL;
        $A_BillDiscount = NULL;
        $A_BillDiscUnit = NULL;
        $A_BillCNTotal  = NULL;

        /* ZONE B */
        $B_TableDetail  = NULL;
        $B_SumTotal     = NULL;
        $B_VatTotal     = NULL;
        $B_CNTotal      = NULL;

        /* ZONE C */
        $C_BillCNTotal  = NULL;
        $C_BillList     = NULL;

        $HeaderSQL = "SELECT
                T0.DocNum, T0.DocDate, T0.DocType, T0.BillCardCode, T0.BillCardName, T0.BillDocNum, T0.BillSlpName, T0.BillCoName,
                T0.DocRemark, T0.DocRemarkText, T0.Attach_1, T0.Attach_2, T0.Attach_3, T0.Attach_Remark, 
                CONCAT(T1.uName,' ',T1.uLastName) AS 'CreateName', T0.CreateDate, T0.FineSA, T0.FineCO, T1.UserSign AS 'CreateSign'
            FROM SA04_Header T0
            LEFT JOIN users T1 ON T0.CreateUkey = T1.uKey
            WHERE T0.DocEntry = $DocEntry LIMIT 1";
        $HeaderRST = MySQLSelect($HeaderSQL);
        $DocNum         = $HeaderRST['DocNum'];
        $DocDate        = date("d/m/Y",strtotime($HeaderRST['DocDate']));
        $CardCode       = $HeaderRST['BillCardCode']." | ".$HeaderRST['BillCardName'];
        $BillDocNum     = $HeaderRST['BillDocNum'];
        $BillSlpName    = $HeaderRST['BillSlpName'];
        $BillCoName     = $HeaderRST['BillCoName'];
        $CreateName     = $HeaderRST['CreateName'];
        if($HeaderRST['CreateSign'] == NULL || $HeaderRST['CreateSign'] == "") {
            $CreateSign     = $HeaderRST['CreateName'];
        } else {
            $CreateSign     = "<img src='../../../../image/signature/".$HeaderRST['CreateSign']."' height='64px'>";
        }
        $CreateDate     = date("d/m/Y",strtotime($HeaderRST['CreateDate']));
        
        ${"IconRemark_".$HeaderRST['DocRemark']} = "<i class='far fa-check-square fa-fw fa-lg'></i>";
        $RemarkText     = " ".$HeaderRST['DocRemarkText'];
        if($HeaderRST['Attach_1'] == "Y") { $IconAttach_1 = "<i class='far fa-check-square fa-fw fa-lg'></i>"; }
        if($HeaderRST['Attach_2'] == "Y") { $IconAttach_2 = "<i class='far fa-check-square fa-fw fa-lg'></i>"; }
        if($HeaderRST['Attach_3'] == "Y") { $IconAttach_3 = "<i class='far fa-check-square fa-fw fa-lg'></i>"; }
        $RemarkAttach   = " ".$HeaderRST['Attach_Remark'];

        ${"IconDocType".$HeaderRST['DocType']} = "<i class='far fa-check-square fa-fw fa-lg'></i>";

        if($HeaderRST['FineSA'] == "Y") { $IconFineSA = "<i class='far fa-check-square fa-fw fa-lg'></i>"; }
        if($HeaderRST['FineCO'] == "Y") { $IconFineCO = "<i class='far fa-check-square fa-fw fa-lg'></i>"; }
        

        switch($HeaderRST['DocType']) {
            case "A":
                for($l=1; $l<=5; $l++) {
                    $B_TableDetail .= "<tr>";
                        $B_TableDetail .= "<td class='align-top text-center'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top'>&nbsp;</td>";
                    $B_TableDetail .= "</tr>";
                }

                $DetailSQL = "SELECT T0.BillDocDate, T0.BillDocDueDate, T0.BillDocTotal, T0.BillDiscount, T0.BillDiscUnit, T0.BillCNTotal FROM SA04_DetailA T0 WHERE T0.DocEntry = $DocEntry LIMIT 1";
                $DetailRST = MySQLSelect($DetailSQL);
                
                $BillDocDate      = date("d/m/Y",strtotime($DetailRST['BillDocDate']));
                $BillDocDueDate   = date("d/m/Y",strtotime($DetailRST['BillDocDueDate']));
                $A_BillDocTotal   = number_format($DetailRST['BillDocTotal'],3);
                $A_BillDiscount   = number_format($DetailRST['BillDiscount'],3);
                $A_BillCNTotal    = number_format($DetailRST['BillCNTotal'],3);
                if($DetailRST['BillDiscUnit'] == "P") { $A_BillDiscUnit = "%"; } else { $A_BillDiscUnit = "บาท"; }

            break;
            case "B":
                $DetailSQL = "SELECT T0.BillDocDate, T0.BillDocDueDate, T0.BillItemCode, T0.BillDscription, T0.BillOldPrice, T0.BillNewPrice, T0.BillDifPrice, T0.BillQuantity, T0.BillDifTotal, T0.BillRemark FROM SA04_DetailB T0 WHERE T0.DocEntry = $DocEntry";
                $DetailQRY = MySQLSelectX($DetailSQL);
                $row = 0;
                while($DetailRST = mysqli_fetch_array($DetailQRY)) {
                    $row++;
                    $B_TableDetail .= "<tr>";
                        $B_TableDetail .= "<td class='align-top text-center'>".$DetailRST['BillItemCode']."</td>";
                        $B_TableDetail .= "<td class='align-top'>".$DetailRST['BillDscription']."</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>".number_format($DetailRST['BillOldPrice'],3)."</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>".number_format($DetailRST['BillNewPrice'],3)."</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>".number_format($DetailRST['BillDifPrice'],3)."</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>".number_format($DetailRST['BillQuantity'],0)."</td>";
                        $B_TableDetail .= "<td class='align-top text-right' style='font-weight: bold;'>".number_format($DetailRST['BillDifTotal'],3)."</td>";
                        $B_TableDetail .= "<td class='align-top'>".$DetailRST['BillRemark']."</td>";
                    $B_TableDetail .= "</tr>";
                    $B_SumTotal     = $B_SumTotal+$DetailRST['BillDifTotal'];

                    $BillDocDate    = date("d/m/Y",strtotime($DetailRST['BillDocDate']));
                    $BillDocDueDate = date("d/m/Y",strtotime($DetailRST['BillDocDueDate']));
                }

                $blank = 5-$row;
                for($r=1;$r<=$blank;$r++) {
                    $B_TableDetail .= "<tr>";
                        $B_TableDetail .= "<td class='align-top text-center'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top text-right'>&nbsp;</td>";
                        $B_TableDetail .= "<td class='align-top'>&nbsp;</td>";
                    $B_TableDetail .= "</tr>";
                }

                $B_VatTotal = ($B_SumTotal*7)/100;
                $B_CNTotal  = $B_SumTotal+$B_VatTotal;
                $B_SumTotal = $B_SumTotal;
            break;
            case "C":
                $DetailSQL = "SELECT GROUP_CONCAT(T0.BillDocNum SEPARATOR ' / ') AS 'BillDocNum', T1.BillCNTotal FROM SA04_DetailC T0 LEFT JOIN SA04_Header T1 ON T0.DocEntry = T1.DocEntry WHERE T0.DocEntry = $DocEntry LIMIT 1";
                $DetailRST = MySQLSelect($DetailSQL);
                $C_BillList    = $DetailRST['BillDocNum'];
                $C_BillCNTotal = number_format($DetailRST['BillCNTotal'],3);
            break;
        }

        $LvCondition = array("LV006","LV010","LV011","LV027","LV038","LV045","LV051","LV057");
        $ApproveSQL = "SELECT
                T0.DocEntry, T0.VisOrder, T0.AppUkeyAct AS AppUkeyReq, CONCAT(T1.uName,' ',T1.uLastName) AS 'AppName', T1.LvCode, T0.AppState, T0.AppRemark, T0.AppDate, T1.UserSign
            FROM SA04_Approve T0
            LEFT JOIN users T1 ON T0.AppUkeyAct = T1.uKey
            WHERE T0.DocEntry = $DocEntry AND T0.AppUkeyReq != 'DP009'";

        $ApproveQRY = MySQLSelectX($ApproveSQL);
        while($ApproveRST = mysqli_fetch_array($ApproveQRY)) {
            $LvCode = $ApproveRST['LvCode'];
            $LvChk = array_search($LvCode, $LvCondition);

            if(!$LvChk) {
                /* ไม่ใช่ ผจก. */
                $Prefix = "SNR";
            } elseif($LvCode == "LV011") {
                /* ใช่ ผจก. */
                $Prefix = "MKT";
            } else {
                $Prefix = "MGR";
            }

            if($ApproveRST['AppState'] == "Y") {
                ${"Icon".$Prefix."AppY"} = "<i class='far fa-check-square fa-fw fa-lg'></i>";
            }

            if($ApproveRST['UserSign'] == NULL || $ApproveRST['UserSign'] == "") {
                ${$Prefix."Sign"} = $ApproveRST['AppName'];
            } else {
                ${$Prefix."Sign"} = "<img src='../../../../image/signature/".$ApproveRST['UserSign']."' height='64px'>";
            }

            
            ${$Prefix."Name"} = $ApproveRST['AppName'];
            ${$Prefix."Date"} = date("d/m/Y",strtotime($ApproveRST['AppDate']));
            ${$Prefix."Remark"} = $ApproveRST['AppRemark'];

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
                        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
                        html, body {
                            background-color: #FFFFFF;
                            font-family: 'Sarabun';
                            font-weight: 200;
                            color: #000 !important;
                            font-size: 10px;
                        }

                        h1,h2,h3,h4,h5,h6 {
                            color: #000;
                            padding: 0;
                            margin: 0;
                            font-weight: 600;
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
                </head>
                <body>
                <?php for($p=1;$p<=$pages;$p++) { ?>
                    <div class="page">
                        <!-- PAGE HEADER -->
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <thead>
                                <tr>
                                    <td width="20%" class="text-center">
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
                                    <td colspan="3" class="text-center"><h5 style="margin: 1rem;"><?php echo $PageHeader; ?></h5></td>
                                </tr>
                            </thead>
                        </table>
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <tr>
                                <th width="15%">เลขที่เอกสาร</th>
                                <td width="35%" ><?php echo $DocNum; ?></td>
                                <th width="15%">วันที่เอกสาร</th>
                                <td width="35%"><?php echo $DocDate; ?></td>
                            </tr>
                            <tr>
                                <th>ชื่อร้านค้า</th>
                                <td colspan="3"><?php echo $CardCode; ?></td>
                            </tr>
                            <tr>
                                <th>เลขที่บิล</th>
                                <td colspan="3"><?php echo $BillDocNum; ?></td>
                            </tr>
                            <tr>
                                <th>วันที่เปิดบิล</th>
                                <td><?php echo $BillDocDate; ?></td>
                                <th>วันที่กำหนดชำระ</th>
                                <td><?php echo $BillDocDueDate; ?></td>
                            </tr>
                            <tr>
                                <th>พนักงานขาย</th>
                                <td><?php echo $BillSlpName; ?></td>
                                <th>ธุรการขาย</th>
                                <td><?php echo $BillCoName; ?></td>
                            </tr>
                            <tr>
                                <th>เหตุผลขอการลดหนี้</th>
                                <td colspan="3">
                                    <table class="table table-borderless table-sm" width="100%" style="color: #000; margin-bottom: 0;">
                                        <tr>
                                            <td width="20%"><?php echo $IconRemark_1; ?> เซลส์เสนอราคาผิด</td>
                                            <td width="20%"><?php echo $IconRemark_2; ?> ลูกค้าขอราคาเดิม</td>
                                            <td width="20%"><?php echo $IconRemark_3; ?> คู่แข่งขายถูกกว่า</td>
                                            <td width="40%"><?php echo $IconRemark_4; ?> อื่น ๆ: <?php echo $RemarkText; ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <th>เอกสารแนบ</th>
                                <td colspan="3">
                                    <table class="table table-borderless table-sm" width="100%" style="color: #000; margin-bottom: 0;">
                                        <tr>
                                            <td width="20%"><?php echo $IconAttach_1; ?> บิลสินค้า</td>
                                            <td width="20%"><?php echo $IconAttach_2; ?> ใบราคาคู่แข่ง</td>
                                            <td width="60%"><?php echo $IconAttach_3; ?> อื่น ๆ: <?php echo $RemarkAttach; ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <div style="border-bottom: 1px solid #000; margin-bottom: .5rem;"></div>
                        <!-- DOCTYPE A+C -->
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <tr>
                                <td width="50%" colspan="3" height="36px"><h6><?php echo $IconDocTypeA; ?> ต้องการลดหนี้ / ลดจ่ายทั้งบิล</h6></td>
                                <td width="50%" colspan="3" height="36px"><h6><?php echo $IconDocTypeC; ?> ต้องการลดหนี้ / ลดจ่ายค่าขนส่ง</h6></td>
                            </tr>
                            <tr>
                                <th width="15%" class="text-right">จำนวนเงิน</th>
                                <td width="20%" class="text-right" style="border-bottom: 1px dotted #000;"><?php echo $A_BillDocTotal; ?></td>
                                <td width="15%">บาท</td>
                                <th rowspan="2" width="15%" class="text-right">เลขที่บิล</th>
                                <td rowspan="2" colspan="2" class="align-top" width="35%"><?php echo $C_BillList; ?></td>
                            </tr>
                            <tr>
                                <th class="text-right">ส่วนลด</th>
                                <td class="text-right" style="border-bottom: 1px dotted #000;"><?php echo $A_BillDiscount; ?></td>
                                <td><?php echo $A_BillDiscUnit; ?></td>
                            </tr>
                            <tr>
                                <th class="text-right">ส่วนลดหนี้/ลดจ่ายสุทธิ</th>
                                <th class="text-right text-danger" style="border-bottom: 3px double #000;"><?php echo $A_BillCNTotal; ?></th>
                                <td>บาท</td>
                                <th class="text-right">ส่วนลดหนี้/ลดจ่ายสุทธิ</th>
                                <th class="text-right text-danger" style="border-bottom: 3px double #000;"><?php echo $C_BillCNTotal; ?></th>
                                <td width="15%">บาท</td>
                            </tr>
                        </table>
                        <div style="border-bottom: 1px solid #000; margin-bottom: .5rem;"></div>
                        <!-- DOCTYPE B -->
                        <table class="table table-borderless table-sm" style="color: #000;">
                            <tr>
                                <td colspan="3" height="36px"><h6><?php echo $IconDocTypeB; ?> ต้องการลดหนี้ / ลดจ่ายเฉพาะรายการ</h6></td>
                            </tr>
                        </table>
                        <table class="table table-bordered table-sm border-dark" style="color: #000;">
                            <tr class="align-middle text-center" style="border-bottom: 2px solid #000;">
                                <th width="10%" rowspan="2">รหัสสินค้า</th>
                                <th rowspan="2">ชื่อสินค้า</th>
                                <th colspan="3">ราคา/หน่วย (ก่อน VAT)</th>
                                <th width="7.5%" rowspan="2">จำนวน</th>
                                <th width="10%" rowspan="2">ส่วนต่างรวม<br/>(ก่อน VAT)</th>
                                <th width="20%"rowspan="2">หมายเหตุ</th>
                            </tr>
                            <tr class="align-middle text-center" style="border-bottom: 2px solid #000;">
                                <th width="8.5%">เก่า</th>
                                <th width="8.5%">ใหม่</th>
                                <th width="8.5%">ส่วนต่าง</th>
                            </tr>

                            <?php echo $B_TableDetail; ?>

                            <tr style="border-top: 2px solid #000;">
                                <th colspan="6" class="text-right">รวมทุกรายการ</th>
                                <th class="text-right"><?php echo number_format($B_SumTotal,3); ?></th>
                                <th>บาท</th>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right">ภาษีมูลค่าเพิ่ม</th>
                                <td class="text-right"><?php echo number_format($B_VatTotal,3); ?></td>
                                <th>บาท</th>
                            </tr>
                            <tr style="border-bottom: 3px double #000;">
                                <th colspan="6" class="text-right">ส่วนลดหนี้/ลดจ่ายสุทธิ</th>
                                <th class="text-right text-danger"><?php echo number_format($B_CNTotal,3); ?></th>
                                <th>บาท</th>
                            </tr>
                        </table>
                        <!-- <div style="border-bottom: 1px solid #000; margin-bottom: .5rem;"></div> -->
                        
                        <!-- APPROVAL -->
                        <table class="table table-bordered table-sm border-dark" style="color: #000; margin-top: 2rem;">
                            <tr class="align-middle text-center">
                                <th width="25%">ธุรการขาย</th>
                                <th colspan="2" width="50%">หน.ธุรการขาย</th>
                                <th colspan="2" width="25%">ผจก.ขาย</th>
                            </tr>
                            <tr>
                                <td rowspan="3" class="table-active">&nbsp;</td>
                                <td width="25%"><?php echo $IconFineSA; ?> ปรับพนักงานขาย (100 บาท)</td>
                                <td width="25%"><?php echo $IconFineCO; ?> ปรับธุรการขาย (20 บาท)</td>
                                <td colspan="2" class="table-active">&nbsp;</td>
                            </tr>
                            <tr>
                                <td><?php echo $IconSNRAppY; ?> อนุมัติ</td>
                                <td><?php echo $IconSNRAppN; ?> ไม่อนุมัติ</td>
                                <td><?php echo $IconMGRAppY; ?> อนุมัติ</td>
                                <td><?php echo $IconMGRAppN; ?> ไม่อนุมัติ</td>
                            </tr>
                            <tr>
                                <td class="align-top" colspan="2" height="36px"><strong>ความเห็น: </strong><?php echo $SNRRemark; ?></td>
                                <td class="align-top" colspan="2" height="36px"><strong>ความเห็น: </strong><?php echo $MGRRemark; ?></td>
                            </tr>
                            <tr height="48px">
                                <td class="text-center"><?php echo $CreateSign; ?></td>
                                <td colspan="2" class="text-center"><?php echo $SNRSign; ?></td>
                                <td colspan="2" class="text-center"><?php echo $MGRSign; ?></td>
                            </tr>
                            <tr>
                                <td class="text-center">(<?php echo $CreateName; ?>)</td>
                                <td colspan="2" class="text-center"><?php echo $SNRName; ?></td>
                                <td colspan="2" class="text-center"><?php echo $MGRName; ?></td>
                            </tr>
                            <tr>
                                <td class="text-center"><?php echo $CreateDate; ?></td>
                                <td colspan="2" class="text-center"><?php echo $SNRDate; ?></td>
                                <td colspan="2" class="text-center"><?php echo $MGRDate; ?></td>
                            </tr>
                        </table>
                    </div>
                <?php } ?>
                <script type="text/javascript">
                    // window.print();
                </script>
                </body>
            </html>
<?php
        }
    }
?>
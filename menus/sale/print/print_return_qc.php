<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
    $RtQcEntry = $_GET['DocEntry'];
    $DeptCode  = $_SESSION['DeptCode'];
    if($DeptCode == "DP010") {
        $SQL = "SELECT T0.DocEntry FROM docrtqc_header T0 WHERE (T0.RtqcEntry = $RtQcEntry AND T0.RecipientStatus = '1') LIMIT 1 ORDER BY T0.DocEntry DESC";
        $ROW = ChkRowDB($SQL);
        if($ROW > 0) {
            $RST     = MySQLSelect($SQL);
            $DocEntry = $RST['DocEntry'];
            $uKey     = $_SESSION['ukey'];
            $SQL2 = "UPDATE docrtqc_header SET RecipientStatus = 'Y', RecipientUkey = '$uKey', RecipientDate = NOW() WHERE DocEntry = $DocEntry";
            $QRY2 = MySQLUpdate($SQL2);
        }
    }

    $RowPage = 0;
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
        <title>คืนสินค้า QC : PRINT</title>
        <style rel="stylesheet" type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
            html, body {
                background-color: #FFFFFF;
                font-family: 'Sarabun';
                font-weight: 400;
                color: #000 !important;
                font-size: 8px;
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
            .text-input {
                color: #014a8a;
            }
        </style>
    </head>
    <body>
    <?php 
    // DATA SECTION 1
        $SQL3 = "
            SELECT
                T0.DocEntry, T0.DocNum, T0.RefDoc1, T0.RefDoc2, T0.DocDate, T0.DocType, T0.BillDocNum, T0.BillDocNum2, T0.BillSAPVer, T0.BillEntry, T0.BillType,
                T0.BillCardCode, T0.BillCardName, T0.BillDate, T0.BillDueDate,
                CONCAT(T2.uName,' ',T2.uLastName,' (',T2.uNickName,')') AS 'SlpName', CONCAT(T3.uName,' ',T3.uLastName,' (',T3.uNickName,')') AS 'OwnName',
                T0.Att_1, T0.Att_2, T0.Att_3,
                T0.SendType, T0.ShippingName, CONCAT(T4.uName,' ',T4.uLastName,' (',T4.uNickName,')') AS 'CoLogiName',
                T0.ShipCost, T0.ShipCostBaht, CONCAT(T5.uName,' ',T5.uLastName,' (',T5.uNickName,')') AS 'ShipCostName',
                T0.ReturnReason, T0.DeadStockType, T0.Incentive, T0.Incentivebaht, T0.FreeBie, T0.COSA_Remark,
                T0.COSA_FineType, CONCAT(T6.uName,' ',T6.uLastName,' (',T6.uNickName,')') AS 'FineCOSAName', T0.RefDoc3, T0.RefDoc3No,
                T0.SALE_FineType, CONCAT(T7.uName,' ',T7.uLastName,' (',T7.uNickName,')') AS 'FineSALEName', T0.RefDoc4, T0.RefDoc4No,
                T1.BillOrder, T1.ItemCode, T1.ItemName, T1.ItemStatus, T1.WhsCode, T1.GrandPrice, T1.Discount, T1.UnitPrice, T1.Quantity, T1.UnitMsr
            FROM rtqc_header T0
            LEFT JOIN rtqc_detail T1 ON T0.DocEntry = T1.DocEntry
            LEFT JOIN users T2 ON T0.BillSlpUkey    = T2.uKey
            LEFT JOIN users T3 ON T0.BillOwnerCode  = T3.uKey
            LEFT JOIN users T4 ON T0.CoLogiName     = T4.uKey
            LEFT JOIN users T5 ON T0.ShipCostName   = T5.uKey
            LEFT JOIN users T6 ON T0.COSA_FineName  = T6.uKey
            LEFT JOIN users T7 ON T0.SALE_FineName  = T7.uKey
            WHERE T0.DocEntry = $RtQcEntry
            ORDER BY T1.VisOrder ASC";
        $QRY3 = MySQLSelectX($SQL3);
        $Row_RST3 = 0;
        $VisOrder = array();
        while($RST3 = mysqli_fetch_array($QRY3)) {
            $Row_RST3++;
            if($Row_RST3 == 1) {
                $BillSAPVer   = $RST3['BillSAPVer'];
                $BillType  = $RST3['BillType'];
                $BillEntry = $RST3['BillEntry'];

                $DataS1['DocNum'] = $RST3['DocNum'];
                $DataS1['DocDate'] = date("d/m/Y",strtotime($RST3['DocDate']));
                $DataS1['RefDoc1'] = $RST3['RefDoc1'];
                switch($RST3['DocType']) {
                    case "D":
                        $DataS1['TypeName'] = "คืนเพื่อลดหนี้";
                        $DataS1['BillDocNum'] = "ใบกำกับภาษีเลขที่ <b class='text-danger'>".$RST3['BillDocNum']."</b>";
                    break;
                    case "L":
                        $DataS1['TypeName'] = "คืนจากการยืม";
                        $DataS1['BillDocNum'] = "ใบยืมสินค้าเลขที่ <b class='text-danger'>".$RST3['BillDocNum']."</b>";
                    break;
                    case "AC":
                        $DataS1['TypeName'] = "คืนแบบไม่มีสินค้า (คืนลอย)";
                        $DataS1['BillDocNum'] = "เอกสารเลขที่ <b class='text-danger'>".$RST3['BillDocNum']."</b> / เปิดใบใหม่เลขที่ <b class='text-danger'>".$RST3['BillDocNum2']."</b>";
                    break;
                    default:
                        $DataS1['TypeName'] = "คืนจากการที่คลังส่งของผิด ส่งเกิน";
                        $DataS1['BillDocNum'] = "เอกสารเลขที่ <b class='text-danger'>".$RST3['BillDocNum']."</b> / เอกสาร FM-WH-17 เลขที่ <b class='text-danger'>".$RST3['RefDoc2']."</b> / เอกสาร PC เลขที่ <b class='text-danger'>".$RST3['BillDocNum2']."</b>";
                    break;
                }

                $DataS1['BillCardName'] = $RST3['BillCardCode']." | ".$RST3['BillCardName'];
                $DataS1['BillDate'] = date("d/m/Y",strtotime($RST3['BillDate']));
                $DataS1['BillDueDate'] = date("d/m/Y",strtotime($RST3['BillDueDate']));
                $DataS1['SlpName'] = $RST3['SlpName'];
                $DataS1['OwnName'] = $RST3['OwnName'];

                $OwnName = explode(" ",$RST3['OwnName']);
                $OwnName = $OwnName[0]." ".$OwnName[2];
                $OwnDate = date("d/m/Y",strtotime($RST3['DocDate']));

                if($RST3['Att_1'] == "Y") { $Att1 = "<i class='far fa-check-square fa-fw fa-lg'></i>"; } else { $Att1 = "<i class='far fa-square fa-fw fa-lg'></i>"; }
                if($RST3['Att_2'] == "Y") { $Att2 = "<i class='far fa-check-square fa-fw fa-lg'></i>"; } else { $Att2 = "<i class='far fa-square fa-fw fa-lg'></i>"; }
                if($RST3['Att_3'] == "Y") { $Att3 = "<i class='far fa-check-square fa-fw fa-lg'></i>"; } else { $Att3 = "<i class='far fa-square fa-fw fa-lg'></i>"; }
                $DataS1['Attach'] =
                    $Att1." ฟอร์มการคืนสินค้า (ต้นฉบับ)&nbsp;&nbsp;".
                    $Att2." สำเนาใบกำกับภาษี&nbsp;&nbsp;".
                    $Att3." สำเนาใบยืม (PA) / ส่งสินค้าผิด (PC)&nbsp;&nbsp;".
                    "<i class='far fa-check-square fa-fw fa-lg'></i> ภาพถ่ายสินค้า";

                if($RST3['SALE_FineType'] == "Y") {
                    $DataS1['SALE_FineType'] = "มีค่าปรับเซลส์ 50 บาท โดยปรับจากคุณ<span style'font-weight: bold;'>".$RST3['FineSALEName']."</span> (อ้างอิงใบวินัยเซลส์เลขที่: <span style'font-weight: bold;' class='text-danger'>".$RST3['RefDoc4']."</span> ข้อที่: <span style'font-weight: bold;' class='text-danger'>".$RST3['RefDoc4No']."</span>)";
                } else {
                    $DataS1['SALE_FineType'] = "ไม่มีค่าปรับ";
                }

                if($RST3['COSA_FineType'] == "Y") {
                    $DataS1['COSA_FineType'] = "มีค่าปรับธุรการเซลส์ 20 บาท โดยปรับจากคุณ<span style'font-weight: bold;'>".$RST3['FineCOSAName']."</span> (อ้างอิงใบวินัยธุรการเซลส์เลขที่: <span style'font-weight: bold;' class='text-danger'>".$RST3['RefDoc3']."</span> ข้อที่: <span style'font-weight: bold;' class='text-danger'>".$RST3['RefDoc3No']."</span>)";
                } else {
                    $DataS1['COSA_FineType'] = "ไม่มีค่าปรับ";
                }

                $ReturnReason = "";
                switch($RST3['ReturnReason']) {
                    case "1.1": $ReturnReason = "1.1 ลูกค้าสั่งผิด"; break;
                    case "1.2": $ReturnReason = "1.2 คู่แข่งตัดราคา"; break;
                    case "1.3": $ReturnReason = "1.3 ลูกค้ามีปัญหาด้านการเงิน"; break;
                    case "1.4": $ReturnReason = "1.4 LAZADA"; break;
                    case "1.5":
                        switch($RST3['DeadStockType']) {
                            case "1": $ReturnReason = "1.5 สินค้า Dead Stock (0 - 6 เดือน (100% ของราคาขาย))"; break;
                            case "2": $ReturnReason = "1.5 สินค้า Dead Stock (7 - 12 เดือน (80% ของราคาขาย))"; break;
                            case "3": $ReturnReason = "1.5 สินค้า Dead Stock (13 - 24 เดือน (50% ของราคาขาย))"; break;
                            case "4": $ReturnReason = "1.5 สินค้า Dead Stock (25 เดือนขึ้นไป (30% ของราคาขาย))"; break;
                        }
                    break;
                    case "1.6": $ReturnReason = "1.6 ลูกค้าไม่มั่นใจคุณภาพสินค้า"; break;
                    case "2.1": $ReturnReason = "2.1 เซลส์แจ้งผิด"; break;
                    case "2.2": $ReturnReason = "2.2 ธุรการเซลส์เปิดบิลผิด"; break;
                    case "2.3": $ReturnReason = "2.3 คลัง/ขนส่งผิด"; break;
                    case "2.4": $ReturnReason = "2.4 ยืมออกตลาด"; break;
                    case "2.5": $ReturnReason = "2.5 สินค้าฝากขาย (Consign)"; break;
                    case "2.6": $ReturnReason = "2.6 ยืมออกบูธ"; break;
                    case "2.7": $ReturnReason = "2.7 ยืมไปทดลอง/ใช้งาน"; break;
                    case "2.8": $ReturnReason = "2.8 ยืมไปเปลี่ยนสินค้าชำรุด"; break;
                    case "3.1": $ReturnReason = "3.1 อุปกรณ์ไม่ครบ"; break;
                    case "3.2": $ReturnReason = "3.2 ชำรุดจากโรงงาน"; break;
                    case "3.3": $ReturnReason = "3.3 ชำรุดจากขนส่ง"; break;
                    case "3.4": $ReturnReason = "3.4 ชำรุดจากลูกค้า"; break;
                    case "4.1": $ReturnReason = "4.1 เหตุการณ์ภายในประเทศ"; break;
                }
                $DataS1['ReturnReason'] = $ReturnReason;

                if($RST3['Incentive'] == "Y") { 
                    $DataS1['Incentive'] = "ได้รับค่า Incentive แล้ว (".number_format($RST3['Incentivebaht'],0)."บาท)"; 
                } else { 
                    $DataS1['Incentive'] = "ยังไม่ได้รับค่า Incentive"; 
                }

                if($RST3['FreeBie'] == "Y") { $DataS1['FreeBie'] = "ใช่"; } else { $DataS1['FreeBie'] = "ไม่ใช่"; }

                if($RST3['SendType'] == "1") {
                    $DataS1['SendType'] = "ลูกค้าฝากส่งคืน ผ่านขนส่งชื่อ ".$RST3['ShippingName']." (ธุรการขนส่ง คุณ".$RST3['CoLogiName']."เป็นผู้รับสินค้า)";
                } else {
                    $DataS1['SendType'] = "เซลส์รับกลับมาคืน";
                }

                if($RST3['ShipCost'] == "Y") {
                    $DataS1['ShipCost'] = "มีค่าขนส่ง ".number_format($RST3['ShipCostBaht'],2)." บาท ให้คุณ".$RST3['ShipCostName']."รับผิดชอบค่าขนส่ง";
                } else {
                    $DataS1['ShipCost'] = "ไม่มีค่าขนส่ง";
                }

                $DataS1['COSA_Remark'] = $RST3['COSA_Remark'];
            }
            array_push($VisOrder, $RST3['BillOrder']);
            $BodyS1[$Row_RST3]['ItemCode']   = $RST3['ItemCode'];
            $BodyS1[$Row_RST3]['ItemName']   = $RST3['ItemName'];
            $BodyS1[$Row_RST3]['GrandPrice'] = number_format($RST3['GrandPrice'],3);
            $BodyS1[$Row_RST3]['Discount']   = str_replace(".00","",$RST3['Discount']);
            $BodyS1[$Row_RST3]['UnitPrice']  = number_format($RST3['UnitPrice'],3);
            $BodyS1[$Row_RST3]['Quantity']   = number_format($RST3['Quantity'],0);
            $BodyS1[$Row_RST3]['UnitMsr']    = $RST3['UnitMsr'];
        }

        $section1_rowsperpage = 10;
        $section1_pages = ceil($Row_RST3/$section1_rowsperpage); 
        $tmp_rowS1 = 0;

        $AppSQL = 
            "SELECT
                CONCAT(T1.uName,' (',T1.uNickName,')') AS 'AppName', T2.uClass, T0.AppDate
            FROM rtqc_approve T0
            LEFT JOIN users T1 ON T0.AppUkeyAct = T1.uKey
            LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
            WHERE T0.DocEntry = $RtQcEntry";
        $AppQRY = MySQLSelectX($AppSQL);
        while($AppRST = mysqli_fetch_array($AppQRY)) {
            switch($AppRST['uClass']) {
                case "19":
                case "21":
                    $SnrName = $AppRST['AppName'];
                    $SnrDate = date("d/m/Y",strtotime($AppRST['AppDate']));
                break;
                case "3":
                case "18":
                    $MgrName = $AppRST['AppName'];
                    $MgrDate = date("d/m/Y",strtotime($AppRST['AppDate']));
                break;
                
            }
        }

    // DATA SECTION 2
        if($BillType == "OINV") {
            $tbname = array("OINV","INV1");
            $BillName = "ใบกำกับภาษี";
        } else {
            $tbname = array("ODLN","DLN1");
            $BillName = "ใบยืม (PA) หรือส่งสินค้าผิด (PC)";
        }
        $SQL4 = "
            SELECT T0.CardCode, T0.CardName, T1.Beginstr, T0.NumAtCard, T1.Beginstr, T0.DocNum,
                T3.LicTradNum, T0.DocDate, T0.Address, T0.DocDueDate, T2.SlpName, T4.PymntGroup,
                T0.Comments, T0.DocTotal, T0.VatSum
            FROM ".$tbname[0]." T0
            LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
            LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
            LEFT JOIN OCRD T3 ON T0.CardCode = T3.CardCode
            LEFT JOIN OCTG T4 ON T3.GroupNum = T4.GroupNum 
            WHERE T0.DocEntry = '$BillEntry'";
        if($BillSAPVer == "8") {
            $QRY4 = conSAP8($SQL4);
        } else {
            $QRY4 = SAPSelect($SQL4);
        }
        $RST4 = odbc_fetch_array($QRY4);
        $NumAtCard = ($RST4['NumAtCard'] != '') ? $RST4['NumAtCard'] : $RST4['Beginstr'].$RST4['DocNum'];

        $SQL5 = "
            SELECT T0.NumAtCard, T3.Beginstr, T0.DocNum, T0.DocDate, T0.DocDueDate, T4.SlpName, T0.U_PONo,
                T1.VisOrder, T1.ItemCode, T2.U_ProductStatus, T1.Dscription, T1.Quantity, T1.unitMsr, T1.PriceBefDi, T1.LineTotal, 
                T1.U_DiscP1, T1.U_DiscP2, T1.U_DiscP3, T1.U_DiscP4, T1.U_DiscP5,
                T0.DocTotal, T0.VatSum, T0.DocEntry
            FROM ".$tbname[0]." T0 
            LEFT JOIN ".$tbname[1]." T1 ON T0.DocEntry = T1.DocEntry 
            LEFT JOIN OITM T2 ON T1.ItemCode = T2.ItemCode 
            LEFT JOIN NNM1 T3 ON T0.Series = T3.Series 
            LEFT JOIN OSLP T4 ON T0.SlpCode = T4.SlpCode 
            WHERE T0.CardCode = '".$RST4['CardCode']."' AND T0.DocEntry = '$BillEntry'
            ORDER BY T0.DocDate DESC,T0.DocNum DESC,T0.CardCode";
        if($BillSAPVer == "8") {
            $QRY5 = conSAP8($SQL5);
        } else {
            $QRY5 = SAPSelect($SQL5);
        }
        $Row_RST5 = 0;
        while($RST5 = odbc_fetch_array($QRY5)) { 
            $Row_RST5++;
            if(array_search($RST5['VisOrder'],$VisOrder) !== false) {
                $BodyS2[$Row_RST5]['trClass'] = "table-warning";
            } else {
                $BodyS2[$Row_RST5]['trClass'] = "";
            }
            $BodyS2[$Row_RST5]['ItemCode'] = $RST5['ItemCode']." - ".conutf8($RST5['Dscription']);
            $BodyS2[$Row_RST5]['Quantity'] = number_format($RST5['Quantity'],0);
            $BodyS2[$Row_RST5]['unitMsr'] = conutf8($RST5['unitMsr']);
            $BodyS2[$Row_RST5]['PriceBefDi'] = number_format($RST5['PriceBefDi'],3);
            if(0 < $RST5['U_DiscP5']){
                $BodyS2[$Row_RST5]['U_Disc'] = number_format($RST5['U_DiscP1'],2)."%+".number_format($RST5['U_DiscP2'],2)."%+".number_format($RST5['U_DiscP3'],2)."%+".number_format($RST5['U_DiscP4'])."%+".number_format($RST5['U_DiscP5'])."%";
            }else{
                if(0 < $RST5['U_DiscP4']){
                    $BodyS2[$Row_RST5]['U_Disc'] = number_format($RST5['U_DiscP1'])."%+".number_format($RST5['U_DiscP2'])."%+".number_format($RST5['U_DiscP3'])."%+".number_format($RST5['U_DiscP4'])."%";
                }else{
                    if(0 < $RST5['U_DiscP3']) {
                        $BodyS2[$Row_RST5]['U_Disc'] = number_format($RST5['U_DiscP1'],2)."%+".number_format($RST5['U_DiscP2'],2)."%+".number_format($RST5['U_DiscP3'],2)."%";
                    }else{
                        if(0 < $RST5['U_DiscP2']) {
                            $BodyS2[$Row_RST5]['U_Disc'] = number_format($RST5['U_DiscP1'],2)."%+".number_format($RST5['U_DiscP2'],2)."%";
                        }else{
                            if(0 < $RST5['U_DiscP1']) {
                                $BodyS2[$Row_RST5]['U_Disc'] = number_format($RST5['U_DiscP1'],2)."%";
                            }else{
                                $BodyS2[$Row_RST5]['U_Disc'] = "0.00%";
                            }
                        }
                    }
                }
            }
            $BodyS2[$Row_RST5]['LineTotal'] = number_format($RST5['LineTotal'],3);
        }

        $section2_rowsperpage = 25;
        $section2_pages = ceil($Row_RST5/$section2_rowsperpage); 
        $tmp_rowS2 = 0;


    // DATA SECTION 3
        $SQL6 = "SELECT T0.AttachID, T0.FileOriName, T0.FileDirName, T0.FileExt FROM rtqc_attach T0 WHERE T0.DocEntry = $RtQcEntry";
        $QRY6 = MySQLSelectX($SQL6);
        $Row_RST6 = 0;
        while($RST6 = mysqli_fetch_array($QRY6)) {
            $Row_RST6++;
            $ATTACH[$Row_RST6]['AttachID'] = $RST6['AttachID'];
            $ATTACH[$Row_RST6]['FileOriName'] = $RST6['FileOriName'].".".$RST6['FileExt'];
            $ATTACH[$Row_RST6]['FileDirName'] = $RST6['FileDirName'].".".$RST6['FileExt'];
        }

        $section3_pages = 0;
        if($Row_RST6 != 0) {
            $section3_rowsperpage = 2;
            $section3_pages = ceil($Row_RST6/$section3_rowsperpage); 
            $tmp_rowS3 = 0;
        }

    $AllPage = $section1_pages+$section2_pages+$section3_pages;
    ?>
    <!-- SECTION 1 -->
        <?php 
        for($s1 = 1; $s1 <= $section1_pages; $s1++) { 
            $RowPage++; ?>
            <div class="page">
                <table class="table table-sm table-borderless m-0" style="color: #000;">
                    <thead>
                        <tr>
                            <td width="15%" class="text-center">
                                <img src="../../../../image/logo/kbi_logo.png" class="img-fluid" />
                            </td>
                            <td>
                                <h4 class='text-black'>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                                <small>
                                    541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                    เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856
                                </small>
                            </td>
                            <td width="" class="align-top text-right">
                                <span class='text-left fw-bolder'>หน้าที่ : <?php echo $RowPage." - ".$AllPage; ?></span><br>
                                <span class='text-left fw-bolder'>เลขที่เอกสาร : <?php echo $DataS1['DocNum']; ?></span><br>
                                <span class='text-left fw-bolder'>วันที่ : <?php echo $DataS1['DocDate']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-center text-black fw-bolder fs-5">ใบคืนสินค้า QC</td>
                        </tr>
                    </thead>
                </table>

                <table class='table table-sm table-borderless m-0'>
                    <tr>
                        <th colspan='6' class='border border-dark'>ส่วนที่ 1 ธุรการเซลล์ลงข้อมูล</th>
                    </tr>

                    <tr>
                        <th width='10%' class='border-start border-dark'>เลขที่เอกสาร</th>
                        <td width='35%' class='text-input'><?php echo $DataS1['DocNum']; ?></td>
                        <th width='8%'>เลขที่ใบมา</th>
                        <td width='22%' class='text-input fw-bolder'><?php echo $DataS1['RefDoc1']; ?></td>
                        <th width='10%'>วันที่เอกสาร</th>
                        <td width='15%' class='text-input border-end border-dark'><?php echo $DataS1['DocDate']; ?></td>
                    </tr>
                    <tr>
                        <th class='border-start border-dark'>ประเภทการคืน</th>
                        <td class='text-input'><?php echo $DataS1['TypeName']; ?></td>
                        <th>เอกสารอ้างอิง</th>
                        <td colspan='3' class='text-input border-end border-dark'><?php echo $DataS1['BillDocNum']; ?></td>
                    </tr>

                    <tr>
                        <th class='border-start border-top border-dark'>ชื่อลูกค้า</th>
                        <td class='text-input fw-bolder border-top border-dark align-top'><?php echo $DataS1['BillCardName']; ?></td>
                        <th class='border-top border-dark'>วันที่เปิดบิล</th>
                        <td class='text-input fw-bolder border-top border-dark align-top'><?php echo $DataS1['BillDate']; ?></td>
                        <th class='border-top border-dark'>วันที่กำหนดชำระ</th>
                        <td class='text-input fw-bolder border-end border-top border-dark align-top'><?php echo $DataS1['BillDueDate']; ?></td>
                    </tr>
                    <tr>
                        <th class='border-start border-dark'>ชื่อพนักงานขาย</th>
                        <td class='text-input fw-bolder align-top'><?php echo $DataS1['SlpName']; ?></td>
                        <th>ชื่อธุรการขาย</th>
                        <td colspan='3' class='text-input fw-bolder border-end border-dark align-top'><?php echo $DataS1['OwnName']; ?></td>
                    </tr>
                    <tr>
                        <th class='border-start border-dark'>เอกสารแนบ</th>
                        <td colspan='5' class='border-end border-dark align-top'><?php echo $DataS1['Attach']; ?></td>
                    </tr>

                    <tr>
                        <th class='border-start border-top border-dark'>ค่าปรับเซลส์</th>
                        <td colspan='5' class='text-input border-end border-top border-dark'><?php echo $DataS1['SALE_FineType']; ?></td>
                    </tr>
                    <tr>
                        <th class='border-start border-dark'>ค่าปรับ Co-Sales</th>
                        <td colspan='5' class='text-input border-end border-dark'><?php echo $DataS1['COSA_FineType']; ?></td>
                    </tr>

                    <tr>
                        <th class='border-start border-top border-dark'>สาเหตุการคืน</th>
                        <td class='text-input border-top border-dark align-top'><?php echo $DataS1['ReturnReason']; ?></td>
                        <th class='border-top border-dark'>ค่า Incentive</th>
                        <td class='text-input border-top border-dark align-top'><?php echo $DataS1['Incentive']; ?></td>
                        <th class='border-top border-dark'>เป็นของแถมหรือไม่</th>
                        <td class='text-input border-end border-top border-dark align-top'><?php echo $DataS1['FreeBie']; ?></td>
                    </tr>
                    <tr>
                        <th class='border-start border-dark'>รูปแบบการส่งคืน</th>
                        <td colspan='5' class='text-input border-end border-dark'><?php echo $DataS1['SendType']; ?></td>
                    </tr>
                    <tr>
                        <th class='border-start border-dark'>ค่าขนส่ง</th>
                        <td colspan='5' class='text-input border-end border-dark'><?php echo $DataS1['ShipCost']; ?></td>
                    </tr>
                    <tr>
                        <th class='border-start border-dark'>หมายเหตุ</th>
                        <td colspan='5' class='text-danger fw-bolder border-end border-dark'><?php echo $DataS1['COSA_Remark']; ?></td>
                    </tr>
                </table>

                <table class='table table-sm table-borderless m-0'>
                    <thead>
                        <tr>
                            <th colspan='13' class='text-center border border-dark table-warning'>
                                เกรดสินค้า: QC = สินค้าใหม่ขายได้ || A = สินค้าสภาพดี/ไม่มีกล่อง || AB = สินค้ามีตำหนิ || AX = สินค้าชำรุดสภาพดี || BX = สินค้าชำรุดมาก
                            </th>
                        </tr>
                        <tr>
                            <th colspan='8' class='text-center border border-dark table-danger'>ธุรการเซลล์ลงรายละเอียดสินค้า</th>
                            <th colspan='5' class='text-center border border-dark table-success'>ส่วนที่ 2 QC ลงข้อมูลผลการตรวจ</th>
                        </tr>
                        <tr>
                            <th width='2%' class='border border-dark text-center table-danger'>No.</th>
                            <th width='7%' class='border border-dark text-center table-danger'>รหัสสินค้า</th>
                            <th width='20%' class='border border-dark text-center table-danger'>ชื่อสินค้า</th>
                            <th width='5%' class='border border-dark text-center table-danger'>ราคา<br>ก่อนส่วนลด</th>
                            <th width='5%' class='border border-dark text-center table-danger'>ส่วนลด<br>(%)</th>
                            <th width='5%' class='border border-dark text-center table-danger'>ราคา<br>หลังส่วนลด</th>
                            <th width='2%' class='border border-dark text-center table-danger'>จำนวน</th>
                            <th width='4%' class='border border-dark text-center table-danger'>หน่วย</th>

                            <th width='4%' class='border border-dark text-center table-success'>ระบุ<br>เกรด</th>
                            <th width='6%' class='border border-dark text-center table-success'>เก็บลงคลัง<br>ระบุคลัง</th>
                            <th width='6%' class='border border-dark text-center table-success'>เจ้าของคลัง</th>
                            <th width='7%' class='border border-dark text-center table-success'>เลขที่เอกสาร<br>HA IV</th>
                            <th width='22%' class='border border-dark text-center table-success'>ผลการตรวจ<br>สินค้ามัปัญหา</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        for($r = 1; $r <= 10; $r++) { 
                            $tmp_rowS1++;
                            if(isset($BodyS1[$tmp_rowS1]['ItemCode'])) { ?>
                                <tr>
                                    <td class='text-input text-right border border-dark'><?php echo $tmp_rowS1; ?></td>
                                    <td class='text-input text-center border border-dark'><?php echo $BodyS1[$tmp_rowS1]['ItemCode']; ?></td>
                                    <td style="word-wrap: break-word; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class='text-input border border-dark'><?php echo $BodyS1[$tmp_rowS1]['ItemName']; ?></td>
                                    <td class='text-input text-right border border-dark'><?php echo $BodyS1[$tmp_rowS1]['GrandPrice']; ?></td>
                                    <td class='text-input text-center border border-dark'><?php echo $BodyS1[$tmp_rowS1]['Discount']; ?></td>
                                    <td class='text-input text-right border border-dark'><?php echo $BodyS1[$tmp_rowS1]['UnitPrice']; ?></td>
                                    <td class='text-input text-right border border-dark'><?php echo $BodyS1[$tmp_rowS1]['Quantity']; ?></td>
                                    <td class='text-input text-center border border-dark'><?php echo $BodyS1[$tmp_rowS1]['UnitMsr']; ?></td>

                                    <td class='border border-dark'></td>
                                    <td class='border border-dark'></td>
                                    <td class='border border-dark'></td>
                                    <td class='border border-dark'></td>
                                    <td class='border border-dark'></td>
                                </tr>
                            <?php
                            }else{ 
                            ?>
                                <tr>
                                    <td class='text-right border border-dark'>&nbsp;</td>
                                    <td class='text-center border border-dark'></td>
                                    <td class='border border-dark'></td>
                                    <td class='text-right border border-dark'></td>
                                    <td class='text-right border border-dark'></td>
                                    <td class='text-right border border-dark'></td>
                                    <td class='text-right border border-dark'></td>
                                    <td class='text-center border border-dark'></td>

                                    <td class='border border-dark'></td>
                                    <td class='border border-dark'></td>
                                    <td class='border border-dark'></td>
                                    <td class='border border-dark'></td>
                                    <td class='border border-dark'></td>
                                </tr>
                            <?php
                            }
                            ?>
                        <?php 
                        } 
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="align-bottom">
                            <th colspan='8' class='border border-bottom-0 border-dark pt-3'>
                                ลงชื่อหัวหน้าธุรการเซลล์ <span style='text-decoration: underline dotted;'><?php /*for($u=1;$u<=50;$u++){ echo "&nbsp;"; }*/ for($u=1;$u<=10;$u++){ echo "&nbsp;"; } echo "<span class='text-input' style='font-size: 12px;'>$SnrName</span>"; for($u=1;$u<=10;$u++){ echo "&nbsp;"; } ?></span> ผู้ตรวจสอบ
                                วันที่ <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=10;$u++){ echo "&nbsp;"; } echo "<span class='text-input' style='font-size: 12px;'>$SnrDate</span>"; for($u=1;$u<=10;$u++){ echo "&nbsp;"; } ?> </span>
                            </th>
                            <th colspan='5' class='border border-bottom-0 border-dark pt-3'>
                                ลงชื่อธุรการรับคืน คีย์ข้อมูลเข้าระบบ SAP <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=50;$u++){ echo "&nbsp;"; } ?></span>
                                วันที่ <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=40;$u++){ echo "&nbsp;"; } ?></span>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <table class='table table-sm table-borderless m-0'>
                    <tr>
                        <th colspan='5' class='border border-dark'>**หมายเหตุ : ให้เจ้าหน้าที่คลังตรวจสอบสินค้าทุกครั้งก่อนเซ็นชื่อรับสินค้าและนำเก็บเข้าคลัง เมื่อเซ็นชื่อรับสินค้าไปแแล้วถือว่าสินค้านั้นอยู่ในความรับผิดชอบของผู้รับ</th>
                    </tr>
                    <tr>
                        <th width='17%' rowspan='2' class='border-start border-dark pt-3'>ส่วนที่ 2.1 QC ค่าปรับตามกฏบริษัท</th>
                        <td width='10%' class='pt-3'><i class="far fa-square fa-fw fa-1x"></i> 1. ไม่มีค่าปรับ</td>
                        <td width='12%' class='pt-3'><i class="far fa-square fa-fw fa-1x"></i> คลังจัดสินค้าผิด</td>
                        <td width='18%' class='pt-3'><i class="far fa-square fa-fw fa-1x"></i> ชำรุดจากผลิตภัณฑ์ (ไม่มีค่าปรับ)</td>
                        <td class='border-end border-dark pt-3'><i class="far fa-square fa-fw fa-1x"></i> อื่นๆ : <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=100;$u++){ echo "&nbsp;"; } ?></span></td>
                    </tr>
                    <tr>
                        <td class='pt-3'><i class="far fa-square fa-fw fa-1x"></i> 2. มีค่าปรับ</td>
                        <td class='pt-3'><i class="far fa-square fa-fw fa-1x"></i> คลังจัดสินค้าผิด</td>
                        <td class='pt-3'><i class="far fa-square fa-fw fa-1x"></i> ชำรุดจากผลิตภัณฑ์ (ไม่มีค่าปรับ)</td>
                        <td class='border-end border-dark'><i class="far fa-square fa-fw fa-1x"></i> อื่นๆ : <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=100;$u++){ echo "&nbsp;"; } ?></span></td>
                    </tr>
                    <tr>
                        <td colspan='5' class='border-start border-end border-dark pt-3'>
                            <span class='fw-bolder'>ข้อมูลค่าปรับ</span> &nbsp;&nbsp;&nbsp;&nbsp;
                            ชื่อผู้ถูกปรับ : <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=50;$u++){ echo "&nbsp;"; } ?></span>&nbsp;
                            ใบวินัยเซลล์ เลขที่ : <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=50;$u++){ echo "&nbsp;"; } ?></span>&nbsp;
                            ผิดข้อที่ : <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=50;$u++){ echo "&nbsp;"; } ?></span>&nbsp;
                            มูลค่าสินค้า/บิล : <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=50;$u++){ echo "&nbsp;"; } ?></span> บาท
                        </td>
                    </tr>
                    <tr>
                        <th class='text-right border-start border-dark pt-3'>1. ค่าบริการ+ค่าปรับบริษัท</th>
                        <td colspan='4' class='border-end border-dark'>
                            <i class="far fa-square fa-fw fa-1x"></i> 100+3%<?php for($u=1;$u<=7;$u++){ echo "&nbsp;"; } ?>
                            <i class="far fa-square fa-fw fa-1x"></i> 100+10%<?php for($u=1;$u<=7;$u++){ echo "&nbsp;"; } ?>
                            <i class="far fa-square fa-fw fa-1x"></i> 100+20%<?php for($u=1;$u<=7;$u++){ echo "&nbsp;"; } ?>
                            <i class="far fa-square fa-fw fa-1x"></i> 100+100%<?php for($u=1;$u<=7;$u++){ echo "&nbsp;"; } ?>
                            คิดเป็นเงิน : <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=50;$u++){ echo "&nbsp;"; } ?></span> บาท
                        </td>
                    </tr>
                    <tr>
                        <td colspan='5' class='border-start border-end border-dark pt-3'>
                            <?php for($u=1;$u<=17;$u++){ echo "&nbsp;"; } ?>
                            <span class='fw-bolder'><i class="far fa-square fa-fw fa-1x"></i> 2. ค่าปรับเข้า HWP 20 บาท</span>
                            <?php for($u=1;$u<=7;$u++){ echo "&nbsp;"; } ?>
                            <span class='fw-bolder'><i class="far fa-square fa-fw fa-1x"></i> 3. QC ปรับตามกฏหมาย 60 บาท</span>
                            <?php for($u=1;$u<=7;$u++){ echo "&nbsp;"; } ?>
                            <span class='fw-bolder'><i class="far fa-square fa-fw fa-1x"></i> 4. ธุรการเซลล์ปรับตามกฏ 20 บาท</span>
                            <?php for($u=1;$u<=7;$u++){ echo "&nbsp;"; } ?>
                            <span class='fw-bolder'>รวมเงินค่าปรับ <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=50;$u++){ echo "&nbsp;"; } ?></span> บาท</span>
                        </td>
                    </tr>
                    <tr>
                        <th colspan='5' class='border-start border-end border-dark'>ส่วนที่ 2.2 ความคิดเห็น QC : <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=352;$u++){ echo "&nbsp;"; } ?></span></th>
                    </tr>
                    <tr>
                        <th colspan='5' class='border-start border-end border-dark'><span style='text-decoration: underline dotted;'><?php for($u=1;$u<=401;$u++){ echo "&nbsp;"; } ?></span></th>
                    </tr>
                    <tr>
                        <th colspan='5' class='border-start border-end border-dark pt-3'>
                            <i class="far fa-square fa-fw fa-1x"></i> 1. รับคืนได้&nbsp;
                            <i class="far fa-square fa-fw fa-1x"></i> 2. ไม่รับคืน ออกใบแจ้งไม่รับคืนและเคลมเปลี่ยน (FM-QC-21)&nbsp;
                            หัวหน้า QC ลงชื่อ <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=70;$u++){ echo "&nbsp;"; } ?></span> ผู้ตรวจสอบเอกสาร&nbsp;&nbsp;&nbsp;
                            วันที่ <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=40;$u++){ echo "&nbsp;"; } ?></span>&nbsp;
                        </th>
                    </tr>
                    <tr>
                        <th colspan='5' class='border-start border-end border-dark pt-3'>
                            <?php for($u=1;$u<=99;$u++){ echo "&nbsp;"; } ?>
                            สินค้าชำรุดจากขนส่ง หัวหน้าขนส่ง ลงชื่อ <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=70;$u++){ echo "&nbsp;"; } ?></span> รับทราบ
                            <?php for($u=1;$u<=20;$u++){ echo "&nbsp;"; } ?>
                            วันที่ <span style='text-decoration: underline dotted;'><?php for($u=1;$u<=40;$u++){ echo "&nbsp;"; } ?></span>&nbsp;
                        </th>
                    </tr>
                </table>

                <table class='table table-sm table-borderless m-0'>
                    <tr>
                        <th width='10%' class='border border-dark align-top'>คลัง KSY<br>ดีขายได้</th>
                        <td width='15%' class='border-top border-dark align-top'>KN - คลังคิงเนลเลอร์<br>KB5 - สินค้าดีกล่องชำรุด</td>
                        <td width='17%' class='border-top border-dark align-top'>KB1 - หน้าร้าน<br>KB6 - คลังสินค้ามีตำหนิ</td>
                        <td width='20%' class='border-top border-dark align-top'>KSY - คลังใหญ่, ตะปู, ปืน<br>KB7 - สินค้ามือสองมีตำหนิ หน้าร้าน</td>
                        <td class='border-top border-end border-dark align-top'>KB4 - คลังอะไหล่</td>
                    </tr>
                    <tr>
                        <th class='border border-dark align-top'>คลังรอซ่อม</th>
                        <td class='border-top border-dark align-top'>WP1 - คลังรอซ่อม<br>WP4 - สินค้าส่ง Supplier นปท.</td>
                        <td class='border-top border-dark align-top'>WP2 - คลังถอดอะไหล่ (บริษัทจ่าย)<br>WP5 - สินค้าส่ง Supplier ตปท.</td>
                        <td class='border-top border-dark align-top'>WP2.2. - ถอดอะไหล่เคลม SUP (Sup จ่าย)<br>WM1 - ไม่รับคืนของชำรุดของ MT1</td>
                        <td class='border-top border-end border-dark align-top'>WM3 - คลังตะปูรอซ่อม<br>WM2-ไม่รับคืนของชำรุดของ MT2</td>
                    </tr>
                    <tr>
                        <th class='border border-dark align-top'>คลังเซลล์</th>
                        <td colspan='4' class='border-top border-end border-dark'>ระบุชื่อ SALES กรณีเป็นสินค้าโป๊ะที่ขายยาก ให้กลับลงคลังชื่อ SALES ซึ่งต้องรับผิดชอบขายเองภายใน 90 วัน มิเช่นนั้นจะต้องซื้อสินค้าชนิดนั้นเอง</td>
                    </tr>
                    <tr>
                        <th class='border border-dark align-top'>จำหน่ายออก</th>
                        <td class='border-top border-dark align-top'>Z1 - ทิ้งเป็นซาก (บริษัทจ่าย)</td>
                        <td class='border-top border-dark align-top'>Z2 - ทิ้งซากเคลมแล้ว (Sup จ่าย)</td>
                        <td colspan='2' class='border-top border-end border-dark align-top'>
                            XS - ให้เป็นตัวอย่าง
                            <?php for($u=1;$u<=15;$u++){ echo "&nbsp;"; } ?>
                            ZZ - ของหาย
                            <?php for($u=1;$u<=15;$u++){ echo "&nbsp;"; } ?>
                            OU - เบิกไปใช้
                        </td>
                    </tr>
                    <tr>
                        <th colspan='5' class='border border-dark'>**หมายเหตุ : สินค้าคืนทุกประเภทไม่อนุญาตให้ QC นำสินค้าเก็บเข้าคลัง ดังนี้ 1. Z1 - ทิ้งเป็นซาก 2. Z2 - ทิ้งซากเคลมแล้ว (Sup จ่าย)</th>
                    </tr>
                    <tr>
                        <th colspan='5' class='border-start border-end border-dark pt-3'>
                            <div class='d-flex' style='width: 100%;'>
                                <div class='text-end' style='width: 10%;'>1. ผจก.ขาย ลงชื่อ</div>
                                <div style='width: 15%; border-bottom: 1px dotted #000;' class='text-input text-center'><?php echo $MgrName; ?></div>
                                <div class='text-end' style='width: 10%;'>2. ธุรการเซลล์ ลงชื่อ</div>
                                <div style='width: 15%; border-bottom: 1px dotted #000;' class='text-input text-center'><?php echo $OwnName; ?></div>
                                <div class='text-end' style='width: 10%;'>3. ผจก.PD อนุมัติ</div>
                                <div style='width: 15%; border-bottom: 1px dotted #000;' class='text-center'>&nbsp;</div>
                                <div class='text-end' style='width: 10%;'>4. บัญชีคีย์ข้อมูล</div>
                                <div style='width: 15%; border-bottom: 1px dotted #000;' class='text-center'>&nbsp;</div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan='5' class='border-start border-end border-dark pt-1'>
                            <div class='d-flex' style='width: 100%;'>
                                <div class='text-end' style='width: 10%;'>วันที่</div>
                                <div style='width: 15%; border-bottom: 1px dotted #000;' class='text-input text-center'><?php echo $MgrDate; ?></div>
                                <div class='text-end' style='width: 10%;'>วันที่</div>
                                <div style='width: 15%; border-bottom: 1px dotted #000;' class='text-input text-center'><?php echo $OwnDate; ?></div>
                                <div class='text-end' style='width: 10%;'>วันที่</div>
                                <div style='width: 15%; border-bottom: 1px dotted #000;' class='text-center'>&nbsp;</div>
                                <div class='text-end' style='width: 10%;'>วันที่</div>
                                <div style='width: 15%; border-bottom: 1px dotted #000;' class='text-center'>&nbsp;</div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan='5' class='border-start border-end border-bottom border-dark'>**หมายเหตุ : 1. กรณี สินค้าเกรด QC สินค้าใหม่ขายได้ KSY, KB4, KB1 ให้นำเอกสารส่งบัญชี ไม่ต้องผ่าน ผจก.PD อนุมัติ 2. ผจก.PD ต้องสรุปเป็นรายงานแจ้งให้ CEO รับทราบทุกสัปดาห์</th>
                    </tr>
                    <tr>
                        <th colspan='5' class=''>FM-QC-01 Rev.15 วันที่มีผลบังคับใช้ 01-11-66 อายุการจัดเก็บอย่างน้อย 1 ปี วิธีทำลาย ขีดคร่อม/Re-use</th>
                    </tr>
                </table>
            </div>
        <?php
        } 
        ?>
    <!-- SECTION 2 -->
        <?php
        for($s2 = 1; $s2 <= $section2_pages; $s2++) {
            $RowPage++; ?>
            <div class="page" style='font-size: 11px;'>
                <table class="table table-sm table-borderless m-0" style="color: #000;">
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
                            <td width="" class="align-top text-right" style='font-size: 8px;'>
                                <span class='text-left fw-bolder'>หน้าที่ : <?php echo $RowPage." - ".$AllPage; ?></span><br>
                                <span class='text-left fw-bolder'>เลขที่เอกสาร : <?php echo $DataS1['DocNum']; ?></span><br>
                                <span class='text-left fw-bolder'>วันที่ : <?php echo $DataS1['DocDate']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-center text-black fw-bolder" style='font-size: 12px;'>สำเนา<?php echo $BillName; ?> (ใช้ภายในบริษัท)</td>
                        </tr>
                    </thead>
                </table>

                <table class="table table-borderless table-sm" style="color: #000;">
                    <tr class="align-top">
                        <th width="15%">ลูกค้า</th>
                        <td colspan="3"><?php echo $RST4['CardCode']." - ".conutf8($RST4['CardName']); ?></td>
                        <th width="12.5%">เลขที่ใบกำกับภาษี</th>
                        <td width="15%"><?php echo $NumAtCard; ?></td>
                    </tr>
                    <tr class="align-top">
                        <th width="15%">เลขที่ผู้เสียภาษี</th>
                        <td colspan="3"><?php echo $RST4['LicTradNum']; ?></td>
                        <th width="12.5%">วันที่ใบสั่งขาย</th>
                        <td width="15%"><?php echo date("d/m/Y", strtotime($RST4['DocDate'])); ?></td>
                    </tr>
                    <tr class="align-top">
                        <th width="15%">ที่อยู่เปิดบิล</th>
                        <td colspan="3"><?php echo conutf8($RST4['Address']); ?></td>
                        <th width="12.5%">วันที่กำหนดชำระ</th>
                        <td width="15%"><?php echo date("d/m/Y", strtotime($RST4['DocDueDate'])); ?></td>
                    </tr>
                    <tr class="align-top">
                        <th width="15%">พนักงานขาย</th>
                        <td colspan="3"><?php echo conutf8($RST4['SlpName']); ?></td>
                        <th width="12.5%">เครดิต</th>
                        <td width="15%"><?php echo conutf8($RST4['PymntGroup']); ?></td>
                    </tr>
                </table>

                <table class="table border-dark OrderList" style="color: #000;">
                    <thead class="text-center">
                        <tr>
                            <th scope="col" width="3.5%">ลำดับ</th>
                            <th scope="col">รายการ</th>
                            <th scope="col" colspan="2">จำนวน</th>
                            <th scope="col" width="10%">ราคา<br/>ต่อหน่วย</th>
                            <th scope="col" width="15%">ส่วนลด</th>
                            <th scope="col" width="12.5%">ราคารวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for($r = 1; $r <= 25; $r++) { 
                            $tmp_rowS2++;
                            if(isset($BodyS2[$tmp_rowS2]['ItemCode'])) { ?>
                                <tr class="align-top <?php echo $BodyS2[$tmp_rowS2]['trClass']; ?>">
                                    <td class="text-right"><?php echo $tmp_rowS2; ?></td>
                                    <td><?php echo $BodyS2[$tmp_rowS2]['ItemCode']; ?></td>
                                    <td class="text-right" width="5%"><?php echo $BodyS2[$tmp_rowS2]['Quantity']; ?></td>
                                    <td width="5%"><?php echo $BodyS2[$tmp_rowS2]['unitMsr']; ?></td>
                                    <td class="text-right"><?php echo $BodyS2[$tmp_rowS2]['PriceBefDi']; ?></td>
                                    <td class="text-center"><?php echo $BodyS2[$tmp_rowS2]['U_Disc']; ?></td>
                                    <td class="text-right" style="font-weight: bold;"><?php echo $BodyS2[$tmp_rowS2]['LineTotal']; ?></td>
                                </tr>
                            <?php
                            }else{
                            ?>
                                <tr class="align-top">
                                    <td class="text-right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="text-right" width="5%">&nbsp;</td>
                                    <td width="5%">&nbsp;</td>
                                    <td class="text-right">&nbsp;</td>
                                    <td class="text-center">&nbsp;</td>
                                    <td class="text-right" style="font-weight: bold;">&nbsp;</td>
                                </tr>
                            <?php
                            }
                        }?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="align-top" colspan="4" rowspan="2">
                                หมายเหตุ: 
                                <span style="color: #FF0000;"><?php echo conutf8($RST4['Comments']); ?></span>
                            </th>
                            <th class="table-active text-right" colspan="2">ยอดรวมทุกรายการ:</th>
                            <th class="text-right"><?php echo number_format($RST4['DocTotal']-$RST4['VatSum'],3); ?></th>
                        </tr>
                        <tr>
                            <th class="table-active text-right" colspan="2">ภาษีมูลค่าเพิ่ม:</th>
                            <th class="text-right"><?php echo number_format($RST4['VatSum'],3); ?></th>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-center"><i>(<?php echo numText(number_format($RST4['DocTotal'],3)); ?>)</i></td>
                            <th class="table-active text-right" colspan="2">จำนวนเงินรวมสุทธิ:</th>
                            <th class="text-right"><?php echo number_format($RST4['DocTotal'],3); ?></th>
                        </tr>
                    </tfoot>
                </table>

                <div class='d-flex justify-content-center pt-3 pb-3'>
                    <span>*** เอกสารสำเนาใช้แนบประกอบภายในบริษัทเท่านั้น ***</span>
                </div>
                <div class='d-flex'>
                    <span>ผู้จัดทำ : <?php echo $_SESSION['uName']." ".$_SESSION['uLastName']." (".$_SESSION['uNickName'].")"; ?></span>
                </div>
                <div class='d-flex'>
                    <span>จัดทำเมื่อ : <?php echo date("d")." ".FullMonth(date("m"))." ".date("Y"); ?></span>
                </div>
            </div>
        <?php
        } 
        ?>
    <!-- SECTION 3 -->
        <?php 
        if($Row_RST6 != 0) {
            for($s3 = 1; $s3 <= $section3_pages; $s3++) {
                $RowPage++;?>
                <div class="page" style='font-size: 11px;'>
                    <table class="table table-sm table-borderless m-0" style="color: #000;">
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
                                <td width="" class="align-top text-right" style='font-size: 8px;'>
                                    <span class='text-left fw-bolder'>หน้าที่ : <?php echo $RowPage." - ".$AllPage; ?></span><br>
                                    <span class='text-left fw-bolder'>เลขที่เอกสาร : <?php echo $DataS1['DocNum']; ?></span><br>
                                    <span class='text-left fw-bolder'>วันที่ : <?php echo $DataS1['DocDate']; ?></span>
                                </td>
                            </tr>
                        </thead>
                    </table>

                    <table class='table table-sm table-borderless m-0 pt-2'>
                        <?php 
                        for($r = 1; $r <= 2; $r++) { 
                            $tmp_rowS3++;
                            if(isset($ATTACH[$r]['FileOriName'])) { ?>
                                <tr>
                                    <td class='text-center pt-5'>
                                        <figure class='figure'>
                                            <figcaption class='figure-caption'><?php echo $ATTACH[$tmp_rowS3]['FileOriName']; ?></figcaption>
                                            <img class='figure-img img-thumbnail w-75 rounded' src='../../../../FileAttach/RTQC/<?php echo $ATTACH[$tmp_rowS3]['FileDirName']; ?>'>
                                        </figure>
                                    </td>
                                </tr>
                            <?php
                            }
                        } ?>
                    </table>  
                </div>
            <?php 
            }
        }
        ?>
    </body>
</html>
<?php
}
?>
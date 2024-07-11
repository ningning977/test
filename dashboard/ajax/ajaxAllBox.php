<?php
include('../../../core/config.core.php');
include('../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";

require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

// ยอดขายรายเดือน
    if($_GET['a'] == 'ChartReportSale') {
        $thisYear = date("Y");
        $thisMonth = date("m");
        $uClass = $_SESSION['uClass'];
        switch ($_SESSION['uClass']){
            case '20' : //Sale
            case '19' :
                if ($_SESSION['DeptCode'] == 'DP005' OR $_SESSION['uClass'] == 20){
                    if($_SESSION['uClass'] == 20) {
                        $sql = "SELECT M".$thisMonth." AS Mx FROM saletarget WHERE DocStatus = 'A' AND DocYear = '".$thisYear."' AND Ukey = '".$_SESSION['ukey']."'";
                    } else {
                        $xql = "SELECT T0.TeamCode FROM saletarget T0 WHERE T0.DocStatus = 'A' AND DocYear = $thisYear AND T0.Ukey = '".$_SESSION['ukey']."' LIMIT 1";
                        $rxt = MySQLSelect($xql);

                        $sql = "SELECT SUM(M".$thisMonth.") AS Mx FROM saletarget WHERE DocStatus = 'A' AND DocYear = '".$thisYear."' AND TeamCode = '".$rxt['TeamCode']."'";
                    }
                    $sqlQRY = MySQLSelectX($sql);
                    $i=1;
                    while ($result = mysqli_fetch_array($sqlQRY)){
                            $target[$i] = $result['Mx'];
                            $i++;
                    }
                    $i=$i-1;
                    if ($i==1){
                        $alltar = $target[1];
                    }else{
                        $alltar = 0;
                        for ($x = 1; $x <= $i; $x++){
                            $alltar = $alltar + $target[$x];
                        }
                    }
                }else{
                    $sql = "SELECT M".$thisMonth." AS Mx FROM saletarget WHERE DocStatus = 'A' AND DocYear = '".$thisYear."' AND (TeamCode LIKE 'TT1%')";
                    $sqlQRY = MySQLSelectX($sql);
                    $alltar = 0;
                    while ($result = mysqli_fetch_array($sqlQRY)){
                        $alltar = $alltar + $result['Mx'];
                    }
                    $sqlDate = "curdate()<date_add(salebill.DocDate,interval 3 day) ORDER BY salebill.DocDate DESC,salebill.DocNum DESC,salebill.saleID,salebill.Cuscode";
                }
                break; 
                
            default :
                switch ($_SESSION['DeptCode']){
                    case 'DP001' :
                    case 'DP002' :
                    case 'DP003' :
                    case 'DP009' :
                    case 'DP010' :
                    case 'DP011' :
                        $sql = "SELECT M".$thisMonth." AS Mx FROM saletarget WHERE DocStatus = 'A' AND DocYear = '".$thisYear."'";
                        $sqlQRY = MySQLSelectX($sql);
                        $alltar = 0;
                        while ($result = mysqli_fetch_array($sqlQRY)){
                            $alltar = $alltar + $result['Mx'];
                        }
                        $sqlDate = "curdate()<date_add(salebill.DocDate,interval 3 day) ORDER BY salebill.DocDate DESC,salebill.DocNum DESC,salebill.saleID,salebill.Cuscode";
                        break ;
                    case 'DP005' :
                        $sql = "SELECT M".$thisMonth." AS Mx FROM saletarget WHERE DocStatus = 'A' AND DocYear = '".$thisYear."' AND (TeamCode LIKE 'TT2%')";
                        $sqlQRY = MySQLSelectX($sql);
                        $alltar = 0;
                        while ($result = mysqli_fetch_array($sqlQRY)){
                            $alltar = $alltar + $result['Mx'];
                        }
                        $sqlDate = "(salebill.DeptCode = '".$_SESSION['DeptCode']."') AND curdate()<date_add(salebill.DocDate,interval 3 day) ORDER BY salebill.DocDate DESC,salebill.DocNum DESC,salebill.saleID,salebill.Cuscode";
                        break ;
                    case 'DP006' :
                        $sql = "SELECT M".$thisMonth." AS Mx FROM saletarget WHERE DocStatus = 'A' AND DocYear = '".$thisYear."' AND (TeamCode LIKE 'MT1%')";
                        $sqlQRY = MySQLSelectX($sql);
                        $alltar = 0;
                        while ($result = mysqli_fetch_array($sqlQRY)){
                            $alltar = $alltar + $result['Mx'];
                        }
                        $sqlDate = "curdate()<date_add(salebill.DocDate,interval 3 day) ORDER BY salebill.DocDate DESC,salebill.DocNum DESC,salebill.saleID,salebill.Cuscode";
                        break ;
                    case 'DP007' :
                        $sql = "SELECT M".$thisMonth." AS Mx FROM saletarget WHERE DocStatus = 'A' AND DocYear = '".$thisYear."' AND (TeamCode LIKE 'MT2%')";
                        $sqlQRY = MySQLSelectX($sql);
                        $alltar = 0;
                        while ($result = mysqli_fetch_array($sqlQRY)){
                            $alltar = $alltar + $result['Mx'];
                        }
                        $sqlDate = "curdate()<date_add(salebill.DocDate,interval 3 day) ORDER BY salebill.DocDate DESC,salebill.DocNum DESC,salebill.saleID,salebill.Cuscode";
                        break ;
                    case 'DP008' :
                        $sql = "SELECT M".$thisMonth." AS Mx FROM saletarget WHERE DocStatus = 'A' AND DocYear = '".$thisYear."' AND (TeamCode LIKE 'TT1%')";
                        $sqlQRY = MySQLSelectX($sql);
                        $alltar = 0;
                        while ($result = mysqli_fetch_array($sqlQRY)){
                            $alltar = $alltar + $result['Mx'];
                        }
                        $sqlDate = "curdate()<date_add(salebill.DocDate,interval 3 day) ORDER BY salebill.DocDate DESC,salebill.DocNum DESC,salebill.saleID,salebill.Cuscode";
                        break ;
                }
            break ;
        }
        
        $FuncSaleUkey = NULL;
        $FuncTeamCode = NULL;
        $LvCode = $_SESSION['LvCode'];
        switch($_SESSION['DeptCode']) {
            case "DP003":
                switch($LvCode) {
                    case "LV104":
                    case "LV105":
                    case "LV106":
                        $FuncTeamCode = "ONL";
                    break;
                }
            break;
            case "DP005":
                switch($uClass) {
                    case 20: $FuncSaleUkey = $_SESSION['ukey']; break;
                    case 19:
                        switch($LvCode) {
                            case "LV030": $FuncTeamCode = "TT201"; break;
                            case "LV032": $FuncTeamCode = "TT202"; break;
                        }
                    break;
                    default: $FuncTeamCode = "TT2"; break;
                }
            break;
            case "DP006": $FuncTeamCode = "MT1"; break;
            case "DP007": $FuncTeamCode = "MT2"; break;
            case "DP008":
                switch($uClass) {
                    case 20: $FuncSaleUkey = $_SESSION['ukey']; break;
                    default: $FuncTeamCode = "OUL"; break;
                }
            break;
            default: $FuncSaleUkey = $_SESSION['ukey']; break;
        }
        $Data = slpCodeData($FuncTeamCode,$FuncSaleUkey);
        if($Data['SlpCode'] != 'none') {
            $sqlSlpCode = " and OSLP.[SlpCode] IN ".$Data['SlpCode'];
        }else{
            $sqlSlpCode = "";
        }

        $sql1 = "SELECT (sum(OINV.DocTotal)-sum(OINV.VatSum)) as data1 from OINV LEFT JOIN OSLP ON OINV.SlpCode = OSLP.SlpCode where (year(oinv.docdate)= '".$thisYear."' and month(oinv.docdate) = '".$thisMonth."') AND OINV.CANCELED = 'N'";
        $sql1 = $sql1.$sqlSlpCode;
        $sql1QRY = SAPSelect($sql1);
        $result1 = odbc_fetch_array($sql1QRY);

        $sql2 = "SELECT (sum(orin.doctotal)-sum(orin.vatsum)) as data2 from ORIN LEFT JOIN OSLP ON ORIN.SlpCode = OSLP.SlpCode LEFT JOIN NNM1 ON ORIN.Series = NNM1.Series where (year(orin.docdate)= '".$thisYear."' and month(orin.docdate) = '".$thisMonth."') AND ORIN.CANCELED = 'N' AND NNM1.BeginStr IN ('S1-','SR-')";
        $sql2 = $sql2.$sqlSlpCode;
        $sql2QRY = SAPSelect($sql2);
        $result2 = odbc_fetch_array($sql2QRY);

        $DataSale = $result1['data1'] - $result2['data2'];

        if ($alltar == 0){ $alltar  = 1; }
        $perSale = number_format(($DataSale/$alltar)*100,2);

        $arrCol['DataSale'] = number_format($DataSale,0);
        $arrCol['alltar'] = number_format($alltar,0);
        $arrCol['perSale'] = $perSale;
    }

// ระบบอนุมัติเอกสาร
    if($_GET['a'] == "WhsQuota") {
        $DeptCode = $_SESSION['DeptCode'];
        $LvCode   = $_SESSION['LvCode'];
        $SubString = 0;
        switch($DeptCode) {
            case "DP005": $SubString = 2; break;
            case "DP006": $SubString = 3; break;
            case "DP007": $SubString = 4; break;
            case "DP008": $SubString = 5; break;
            case "DP002":
            case "DP003":
                switch($LvCode) {
                    case "LV103":
                    case "LV104":
                    case "LV105":
                    case "LV106":
                        $SubString = 6;
                        break;
                    default:
                        $SubString = 1;
                        break;
                }
            break;
        }

        switch($DeptCode) {
            case "DP003":
            case "DP005":
            case "DP006":
            case "DP007":
            case "DP008":
                $WhrSQL = " AND SUBSTRING(T0.AppState,$SubString,1) = 'A'";
                break;
            default:
                $WhrSQL = NULL;
                break;
        }
        $GetSQL =
            "SELECT
                T0.ID, DATE(T0.DocDate) AS 'DocDate', T0.DocNum, T0.ItemCode, T1.ItemName,
                CASE
                    WHEN T0.All_Out > 0 THEN 'KBI'
                    WHEN T0.TTC_Out > 0 THEN 'TT2'
                    WHEN T0.MT1_Out > 0 THEN 'MT1'
                    WHEN T0.MT2_Out > 0 THEN 'MT2'
                    WHEN T0.OUL_Out > 0 THEN 'OUL'
                    WHEN T0.ONL_Out > 0 THEN 'ONL'
                ELSE '' END AS 'QuotaOut',
                CASE
                    WHEN T0.All_In > 0 THEN 'KBI'
                    WHEN T0.TTC_In > 0 THEN 'TT2'
                    WHEN T0.MT1_In > 0 THEN 'MT1'
                    WHEN T0.MT2_In > 0 THEN 'MT2'
                    WHEN T0.OUL_In > 0 THEN 'OUL'
                    WHEN T0.ONL_In > 0 THEN 'ONL'
                ELSE '' END AS 'QuotaIn',
                (T0.All_Out+T0.TTC_Out+T0.MT1_Out+T0.MT2_Out+T0.OUL_Out+T0.ONL_Out) AS 'Qty',
                T2.uName AS 'ReqName', T2.uNickName
            FROM whsequota_header T0
            LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
            LEFT JOIN users T2 ON T0.UkeyCreate = T2.uKey
            WHERE T0.StatusDoc = '1'$WhrSQL";
        $Rows = ChkRowDB($GetSQL);

        $ChkRow = "N";
        if(isset($_GET['tab'])) { 
            if($_GET['tab'] == "ChkRow") {
                $arrCol['Rows'] = $Rows;
                $ChkRow = "Y";
            }
        }

        if($Rows == 0 && $ChkRow == "Y") {
            $output .= "<tr><td class='text-center' colspan='7'>ไม่มีข้อมูลการขออนุมัติ :)</td></tr>";
        } else {
            $GetQRY = MySQLSelectX($GetSQL);
            while($GetRST = mysqli_fetch_array($GetQRY)) {
                $output .= "<tr>";
                    $output .= "<td class='text-center'>".date("d/m/Y",strtotime($GetRST['DocDate']))."</td>";
                    $output .= "<td class='text-center'><a href='javascript:void(0);' onclick='DataDetail(\"".$GetRST['ItemCode']."\")'>".$GetRST['DocNum']."</td>";
                    $output .= "<td>".$GetRST['ItemCode']." | ".$GetRST['ItemName']."</td>";
                    $output .= "<td class='text-center'>".$GetRST['QuotaOut']."</td>";
                    $output .= "<td class='text-center'>".$GetRST['QuotaIn']."</td>";
                    $output .= "<td class='text-right'>".number_format($GetRST['Qty'],0)."</td>";
                    $output .= "<td>".$GetRST['ReqName']." (".$GetRST['uNickName'].")</td>";
                $output .= "</tr>";                                                                                                                    
            }
        }
        $arrCol['output'] = $output;
    }

// เช็คสต๊อกออนไลน์
    if($_GET['a'] == 'ChangeStockItem'){
        $sql = "SELECT T0.ItemCode, T0.ItemName 
                FROM OITM T0
                WHERE (T0.ItemName != '' AND T0.ItemCode !=''
                    AND T0.ItemCode != '00-000-003' AND T0.ItemCode != '01-001-001' 
                    AND T0.ItemCode != '02-001-100' AND T0.ItemCode != '02-013-101'
                    AND T0.ItemCode != '05-013-104' AND T0.ItemCode != '05-013-126'
                    AND T0.ItemCode != '05-013-127' AND T0.ItemCode != '05-013-131'
                    AND T0.ItemCode != '05-013-132' AND T0.ItemCode != '05-013-161'
                    AND T0.ItemCode != '05-013-211' AND T0.ItemCode != '05-013-710'
                    AND T0.ItemCode != '05-013-711' AND T0.ItemCode != '05-015-200'
                    AND T0.ItemCode != '05-015-220' AND T0.ItemCode != '05-015-350'
                    AND T0.ItemCode != '05-013-101' AND T0.ItemCode != '05-000-005'
                    AND T0.ItemCode != '07-000-008' AND T0.ItemCode != '07-009-303'
                    AND T0.ItemCode != '07-999-011' AND T0.ItemCode != '31-601-025'
                    AND T0.ItemCode != '31-601-041' AND T0.ItemCode != '32-750-072'
                    AND T0.ItemCode != '34-080-050' AND T0.ItemCode != '31-411-011'
                    AND T0.ItemCode != '35-411-105' AND T0.ItemCode != '88-520-145'
                    AND T0.ItemCode != '88-520-150' AND T0.ItemCode != '88-520-170'
                    AND T0.ItemCode != '99-665-091' AND T0.ItemCode != '07-000-005'
                    AND T0.ItemCode != '34-411-011' ) ORDER BY T0.ItemCode";
        $sqlQRY = MySQLSelectX($sql);
        $Row = 0;
        while ($result = mysqli_fetch_array($sqlQRY)){
            ++$Row;
            // if ($result['U_ProductStatus'] != ''){
            //     $stcode = " - [".$result['U_ProductStatus']."] - ";
            // }else{
            //     $stcode = " - ";
            // }
            $arrCol['Stock']['ItemCode'][$Row] = $result['ItemCode'];
            $arrCol['Stock']['ItemName'][$Row] = $result['ItemCode']." - ".$result['ItemName'];
        }
        $arrCol['RowStock'] = $Row;
    }
    if($_GET['a'] == "GetInStock") {
        /* GETSAP */
        $ItemCode = $_POST['ItemCode'];
        $SAP_W100 = 0; $SAP_W101 = 0; $SAP_W102 = 0; $SAP_W103 = 0; $SAP_W104 = 0; $SAP_W105 = 0; $SAP_W106 = 0;
        $SAP_W200 = 0; $SAP_W201 = 0; $SAP_W202 = 0; $SAP_W203 = 0; $SAP_W204 = 0; $SAP_W205 = 0; $SAP_NULL = 0;
        $ERF_W100 = 0; $ERF_W101 = 0; $ERF_W102 = 0; $ERF_W103 = 0; $ERF_W104 = 0; $ERF_W105 = 0; $ERF_W106 = 0;
        $ERF_W200 = 0; $ERF_W201 = 0; $ERF_W202 = 0; $ERF_W203 = 0; $ERF_W204 = 0; $ERF_W205 = 0; $ERF_NULL = 0;
        $GetSAPSQL =
            "SELECT
                A0.WhsGroup, SUM(A0.OnHand) AS 'Qty'
            FROM (
                SELECT T0.WhsCode,
                    CASE
                        WHEN T0.WhsCode IN ('KSY','KSM','KB4') THEN 'W100'           /* 1st Hand KBI */
                        WHEN T0.WhsCode = 'MT' THEN 'W101'                           /* 1st Hand MT1 */
                        WHEN T0.WhsCode = 'MT2' THEN 'W102'                          /* 1st Hand MT2 */
                        WHEN T0.WhsCode = 'TT-C' THEN 'W104'                         /* 1st Hand TT2 */
                        WHEN T0.WhsCode IN ('OUL','KB1','KB1.1') THEN 'W105'         /* 1st Hand TT1 + OUL */
                        WHEN T0.WhsCode IN ('KB5','KB5.1','KB6','KB6.1') THEN 'W200' /* 2nd Hand KBI */
                        WHEN T0.WhsCode = 'WM1' THEN 'W201'                          /* 2nd Hand MT1 */
                        WHEN T0.WhsCode = 'WM2' THEN 'W202'                          /* 2nd Hand MT2 */
                        WHEN T0.WhsCode LIKE 'WA%' THEN 'W203'                       /* 2nd Hand TT1 */
                        WHEN T0.WhsCode LIKE 'WB%' OR T0.WhsCode LIKE 'WC%' OR T0.WhsCode LIKE 'WD%' OR T0.WhsCode LIKE 'WK%' THEN 'W204' /* 2nd Hand TT2 */
                        WHEN T0.WhsCode = 'KB7' THEN 'W205'                          /* 2nd Hand OUL */
                    ELSE 'NULL' END AS 'WhsGroup',
                    T0.OnHand
                FROM OITW T0
                WHERE T0.ItemCode = '$ItemCode'
            ) A0
            WHERE A0.WhsGroup != 'NULL'
            GROUP BY A0.WhsGroup";
        $GetSAPQRY = SAPSelect($GetSAPSQL);
        while($GetSAPRST = odbc_fetch_array($GetSAPQRY)) {
            ${"SAP_".$GetSAPRST['WhsGroup']} = $GetSAPRST['Qty'];
        }

        $GetERFSQL =
            "SELECT
                A0.WhsGroup, SUM(A0.OnHand) As 'Qty'
            FROM (
                SELECT
                    CASE
                        WHEN T0.CH = 'MT1' THEN 'W101'
                        WHEN T0.CH = 'MT2' THEN 'W102'
                        WHEN T0.CH = 'TTC' THEN 'W104'
                        WHEN T0.CH = 'OUL' THEN 'W105'
                        WHEN T0.CH = 'ONL' THEN 'W106'
                    ELSE 'NULL' END AS 'WhsGroup',
                    T0.OnHand
                FROM whsquota T0
                WHERE T0.ItemCode = '$ItemCode'
            ) A0
            GROUP BY A0.WhsGroup";
        $GetERFQRY = MySQLSelectX($GetERFSQL);
        while($GetERFRST = mysqli_fetch_array($GetERFQRY)) {
            ${"ERF_".$GetERFRST['WhsGroup']} = $GetERFRST['Qty'];
        }

        $Qty_B = ($SAP_W100) - ($ERF_W101+$ERF_W102+$ERF_W103+$ERF_W104+$ERF_W105+$ERF_W106);
        
        switch($_SESSION['DeptCode']) {
            case "DP005":
                $Qty_Team = "TT2";
                $Qty_A    = $ERF_W104;
                $Qty_C    = $SAP_W200;
                $Qty_D    = $SAP_W204;
                break;
            case "DP006":
                $Qty_Team = "MT1";

                $Qty_A = $ERF_W101;
                $Qty_C    = $SAP_W200;
                $Qty_D    = $SAP_W201;
                break;
            case "DP007":
                $Qty_Team = "MT2";
                $Qty_A = $ERF_W102;
                $Qty_C    = $SAP_W200;
                $Qty_D    = $SAP_W202;
                break;
            case "DP008":
                $Qty_Team = "TT1&OUL";
                $Qty_A    = $ERF_W105;
                $Qty_C    = $SAP_W200;
                $Qty_D    = $SAP_W203+$SAP_W205;
                break;
            case "DP003":
                switch($_SESSION['LvCode']) {
                    case "LV103":
                    case "LV104":
                    case "LV105":
                    case "LV106":
                        $Qty_Team = "ONL";
                        $Qty_A    = $ERF_W106;
                        $Qty_C    = $SAP_W200;
                        $Qty_D    = 0;
                        break;
                    default:
                        $Qty_Team = "ทั้งหมด";
                        $Qty_A    = $ERF_W101+$ERF_W102+$ERF_W103+$ERF_W104+$ERF_W105+$ERF_W106;
                        $Qty_C    = $SAP_W200;
                        $Qty_D    = $SAP_W201+$SAP_W202+$SAP_W203+$SAP_W204+$SAP_W205;
                        break;
                }
                break;
            default:
                $Qty_Team = "ทั้งหมด";
                $Qty_A    = $ERF_W101+$ERF_W102+$ERF_W103+$ERF_W104+$ERF_W105+$ERF_W106;
                $Qty_C    = $SAP_W200;
                $Qty_D    = $SAP_W201+$SAP_W202+$SAP_W203+$SAP_W204+$SAP_W205;
                break;
        }
        $arrCol['Qty_Team'] = $Qty_Team;
        $arrCol['Qty_A']    = number_format($Qty_A,0);
        $arrCol['Qty_B']    = number_format($Qty_B,0);
        $arrCol['Qty_C']    = number_format($Qty_C,0);
        $arrCol['Qty_D']    = number_format($Qty_D,0);
    }

// รายงานสรุปฝ่ายบริหาร
    if($_GET['a'] == 'CallData') {
        // $Year = date("Y");
        // $Month = date("m");
        $Year = $_POST['Year'];
        $Month = $_POST['Month'];
        switch ($_SESSION['DeptCode']){
            case 'DP005' : //TT2
                $TeamArr = [ 'TT2' ];
            break;
            case 'DP006' : //MT1
                if($_SESSION['uClass'] == '18') {
                    $TeamArr = [ 'TT1', 'TT2', 'OUL', 'ONL', 'MT1', 'MT2' ];
                }else{
                    $TeamArr = [ 'MT1' ];
                }
            break;
            case 'DP007' : //MT2
                $TeamArr = [ 'MT2' ];
            break;
            case 'DP008' : //TT1 //OUL
                $TeamArr = [ 'TT1', 'OUL' ];
            break;
            default:  //ALL
                $TeamArr = [ 'TT1', 'TT2', 'OUL', 'ONL', 'MT1', 'MT2' ];
            break;
        }

        foreach($TeamArr as $Team) {
            $Data[$Team]['OutSaleMonth'] = 0;
            $Data[$Team]['OutSaleYear'] = 0;

            if($Team == 'MT1') {
                $WhereSQL1 = "(T0.TeamCode LIKE '$Team%' OR T0.TeamCode LIKE 'EXP%') AND T0.DocYear = $Year";
            }else{
                switch($Team) {
                    case 'TT2': $Central = "(T0.DocStatus != 'I' OR T0.Ukey = 'f14ebccb93d03ac5de9fe0683d19No01')"; break;
                    case 'MT2': $Central = "T0.DocStatus != 'I'"; break;
                    default: $Central = "T0.DocStatus != 'I'"; break;
                }
                $WhereSQL1 = "
                    T0.TeamCode LIKE '$Team%' AND T0.DocYear = $Year AND $Central
                    ORDER BY T0.TeamCode,
                    CASE WHEN T0.DocStatus = 'A' THEN 1 ELSE 2 END , T0.TeamCode, T1.LvCode, CONCAT(T1.uName,' ',T1.uLastName,' (',T1.uNickName,')')";
            }
            
            $SQL1 = 
                "SELECT
                    T0.uKey, CONCAT(T1.uName,' ',T1.uLastName,' (',T1.uNickName,')') AS 'Name', T0.TeamCode, T0.DocStatus,
                    T0.M01, T0.M02, T0.M03, T0.M04, T0.M05, T0.M06, T0.M07, T0.M08, T0.M09, T0.M10, T0.M11, T0.M12
                FROM saletarget T0
                LEFT JOIN users T1 ON T0.Ukey = T1.uKey
                WHERE $WhereSQL1";
            //echo $SQL1."\n";
            $QRY1 = MySQLSelectX($SQL1);
            $tmpTeam = ""; $numTeam = 1; $r = 0; $tmpUkey = "";
            while($RST1 = mysqli_fetch_array($QRY1)) {
                $YearSaleTarget = 0;
                if($RST1['DocStatus'] == 'A') {
                    $Where_SlpCode = ($Team == 'MT1') ? "(T0.TeamCode LIKE '$Team%' OR T0.TeamCode LIKE 'EXP%')" : "T0.TeamCode LIKE '$Team%'";
                    $SQL_SlpCode = 
                        "SELECT GROUP_CONCAT(T0.SlpCode) AS SlpCode
                        FROM oslp T0
                        WHERE $Where_SlpCode AND Ukey = '".$RST1['uKey']."'";
                    $SlpCode = MySQLSelect($SQL_SlpCode);

                    if($SlpCode['SlpCode'] == '23' OR $SlpCode['SlpCode'] == '24' OR $SlpCode['SlpCode'] == '158' OR $SlpCode['SlpCode'] == '406') {
                        switch ($SlpCode['SlpCode']) {
                            case '23': $Name = "โฮมโปร-ฝากขาย"; break;
                            case '24': $Name = "ไทวัสดุ-ฝากขาย"; break;
                            case '158': $Name = "เกศศินี ฝากขาย เมกาโฮม"; break;
                            case '406': $Name = "ส่วนกลาง"; break;
                        }
                    }else{
                        $Name = $RST1['Name'];
                    }

                    $r++;
                    if($tmpTeam == $RST1['TeamCode'] || $tmpTeam == "") {
                        $Data[$Team]['T'.$numTeam][$r]['Name'] = $Name;
                        // for($m = 1; $m <= date("m"); $m++) {
                        for($m = 1; $m <= intval($Month); $m++) {
                            $YearSaleTarget = ($m < 10) ? $YearSaleTarget+$RST1['M0'.$m] : $YearSaleTarget+$RST1['M'.$m];
                            // if($m == date("m")) {
                            if($m == intval($Month)) {
                                $Data[$Team]['T'.$numTeam][$r]['MonthSaleTarget'] = ($m < 10) ? $RST1['M0'.$m] : $RST1['M'.$m];
                            }
                        }
                        $Data[$Team]['T'.$numTeam][$r]['YearSaleTarget'] = $YearSaleTarget;
                    }else{
                        
                        if($RST1['TeamCode'] != 'TT203' && $RST1['TeamCode'] != 'EXP101') { 
                            $numTeam++; $r = 1;
                            $Data[$Team]['T'.$numTeam][$r]['Name'] = $Name;
                        }else{
                            if($RST1['TeamCode'] == 'TT203') {
                                $Data[$Team]['T'.$numTeam][$r]['Name'] = $Name." (ลาว)";
                            }else{
                                $Data[$Team]['T'.$numTeam][$r]['Name'] = $Name." (ต่างประเทศ)";
                            }
                        }
                        // for($m = 1; $m <= date("m"); $m++) {
                        for($m = 1; $m <= intval($Month); $m++) {
                            $YearSaleTarget = ($m < 10) ? $YearSaleTarget+$RST1['M0'.$m] : $YearSaleTarget+$RST1['M'.$m];
                            // if($m == date("m")) {
                            if($m == intval($Month)) {
                                $Data[$Team]['T'.$numTeam][$r]['MonthSaleTarget'] = ($m < 10) ? $RST1['M0'.$m] : $RST1['M'.$m];
                            }
                        }
                        $Data[$Team]['T'.$numTeam][$r]['YearSaleTarget'] = $YearSaleTarget;
                    }
                    $Data[$Team]['T'.$numTeam][$r]['uKey'] = $RST1['uKey'];
                }

                $tmpTeam = $RST1['TeamCode'];

                // $MonthSQL2 = "A0.M".intval(date("m"));
                $MonthSQL2 = "A0.M".intval($Month);
                $YearSQL2 = "";
                // for($m = 1; $m <= intval(date("m")); $m++) {
                for($m = 1; $m <= intval($Month); $m++) {
                    $YearSQL2 .= "A0.M".$m;
                    // if($m != intval(date("m"))) {
                    if($m != intval($Month)) {
                        $YearSQL2 .= "+";
                    }
                }

                $HhereSQL2 = "T1.Memo = '".$RST1['uKey']."'";
                switch($RST1['TeamCode']) {
                    case "TT202": 
                        $ArrSlpCode = explode(",",$SlpCode['SlpCode']);
                        if(array_search(298,$ArrSlpCode) !== false) {
                            $HhereSQL2 .= " AND T1.SlpCode != 298";
                        }
                    break;
                    case "TT203": 
                        $HhereSQL2 = "T1.SlpCode IN (298)";
                    break;
                    case "MT100": 
                        $ArrSlpCode = explode(",",$SlpCode['SlpCode']);
                        if(array_search(191,$ArrSlpCode) !== false) {
                            $HhereSQL2 .= " AND T1.SlpCode NOT IN (191,136)";
                        }
                    break;
                    case "EXP101": 
                        $HhereSQL2 = "T1.SlpCode IN (191)";
                    break;
                    case "TT101":
                    case "OUL":
                    case "ONL":
                        $HhereSQL2 .= " AND T1.U_Dim1 = '".substr($RST1['TeamCode'], 0, 3)."'";
                    break;
                }

                $SQL2 = 
                    "SELECT
                        A0.uKey,
                        SUM($MonthSQL2) AS SaleMonth,
                        SUM($YearSQL2) AS SaleYear
                    FROM (
                        SELECT
                            T1.Memo AS 'uKey', 
                            CASE WHEN MONTH(T0.DocDate) = 1 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M1',
                            CASE WHEN MONTH(T0.DocDate) = 2 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M2',
                            CASE WHEN MONTH(T0.DocDate) = 3 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M3',
                            CASE WHEN MONTH(T0.DocDate) = 4 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M4',
                            CASE WHEN MONTH(T0.DocDate) = 5 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M5',
                            CASE WHEN MONTH(T0.DocDate) = 6 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M6',
                            CASE WHEN MONTH(T0.DocDate) = 7 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M7',
                            CASE WHEN MONTH(T0.DocDate) = 8 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M8',
                            CASE WHEN MONTH(T0.DocDate) = 9 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M9',
                            CASE WHEN MONTH(T0.DocDate) = 10 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M10',
                            CASE WHEN MONTH(T0.DocDate) = 11 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M11',
                            CASE WHEN MONTH(T0.DocDate) = 12 THEN SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M12'
                        FROM OINV T0
                        LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                        WHERE $HhereSQL2 AND YEAR(T0.DocDate) = $Year AND T0.CANCELED = 'N'
                        GROUP BY T1.Memo, MONTH(T0.DocDate)
                        UNION ALL
                        SELECT
                            T1.Memo AS 'uKey', 
                            CASE WHEN MONTH(T0.DocDate) = 1 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M1',
                            CASE WHEN MONTH(T0.DocDate) = 2 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M2',
                            CASE WHEN MONTH(T0.DocDate) = 3 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M3',
                            CASE WHEN MONTH(T0.DocDate) = 4 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M4',
                            CASE WHEN MONTH(T0.DocDate) = 5 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M5',
                            CASE WHEN MONTH(T0.DocDate) = 6 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M6',
                            CASE WHEN MONTH(T0.DocDate) = 7 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M7',
                            CASE WHEN MONTH(T0.DocDate) = 8 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M8',
                            CASE WHEN MONTH(T0.DocDate) = 9 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M9',
                            CASE WHEN MONTH(T0.DocDate) = 10 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M10',
                            CASE WHEN MONTH(T0.DocDate) = 11 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M11',
                            CASE WHEN MONTH(T0.DocDate) = 12 THEN -SUM(T0.DocTotal - T0.VatSum) ELSE 0 END AS 'M12'
                        FROM ORIN T0
                        LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                        WHERE $HhereSQL2 AND YEAR(T0.DocDate) = $Year AND T0.CANCELED = 'N'
                        GROUP BY T1.Memo, MONTH(T0.DocDate)
                    ) A0
                    GROUP BY A0.uKey";

                    //echo $SQL2."\n";
                if(ChkRowSAP($SQL2) != 0) {
                    $QRY2 = SAPSelect($SQL2);
                    $RST2 = odbc_fetch_array($QRY2);
                    $tmpUkey = "";
                    if($RST1['DocStatus'] == 'A') {
                        $Data[$Team]['T'.$numTeam][$r]['SaleMonth'] = $RST2['SaleMonth'];
                        if($Data[$Team]['T'.$numTeam][$r]['MonthSaleTarget'] > 0) {
                            $Data[$Team]['T'.$numTeam][$r]['PercentSaleMonth'] = ($Data[$Team]['T'.$numTeam][$r]['SaleMonth']/$Data[$Team]['T'.$numTeam][$r]['MonthSaleTarget'])*100;
                        }else{
                            $Data[$Team]['T'.$numTeam][$r]['PercentSaleMonth'] = 0;
                        }
        
                        $Data[$Team]['T'.$numTeam][$r]['SaleYear'] = $RST2['SaleYear'];
                        if($Data[$Team]['T'.$numTeam][$r]['YearSaleTarget'] > 0) {
                            $Data[$Team]['T'.$numTeam][$r]['PercentSaleYear'] = ($Data[$Team]['T'.$numTeam][$r]['SaleYear']/$Data[$Team]['T'.$numTeam][$r]['YearSaleTarget'])*100;
                        }else{
                            $Data[$Team]['T'.$numTeam][$r]['PercentSaleYear'] = 0;
                        }
                    }else{
                        if($tmpUkey != $RST1['uKey']) {
                            $Data[$Team]['OutSaleMonth'] = $Data[$Team]['OutSaleMonth']+$RST2['SaleMonth'];
                            $Data[$Team]['OutSaleYear'] = $Data[$Team]['OutSaleYear']+$RST2['SaleYear'];
                        }
                        $tmpUkey = $RST1['uKey'];
                    }
                }else{
                    if($RST1['DocStatus'] == 'A') {
                        $Data[$Team]['T'.$numTeam][$r]['SaleMonth'] = 0;
                        $Data[$Team]['T'.$numTeam][$r]['PercentSaleMonth'] = 0;
                        $Data[$Team]['T'.$numTeam][$r]['SaleYear'] = 0;
                        $Data[$Team]['T'.$numTeam][$r]['PercentSaleYear'] = 0;
                    }
                }

                $SQL3 = "SELECT SUM(T0.TrgAmount) AS TrgAmount FROM teamtarget T0 WHERE T0.TeamCode LIKE '$Team%' AND T0.DocYear = $Year AND T0.DocStatus = 'A'";
                $RST3 = MySQLSelect($SQL3);
                $Data[$Team]['TrgAmount'] = $RST3['TrgAmount'];
            }
        }
        //$Data = "";
        $arrCol['Data'] = $Data;
    }

    if($_GET['a'] == 'SelectYear') {
        $loopCode = array("TT1","TT2","OUL","ONL","MT1","MT2");
        for ($i = 0; $i <= count($loopCode)-1; $i++){ $SLPteam[$loopCode[$i]] = ""; }

        for ($i = 0; $i <= count($loopCode)-1; $i++){
            $WheTeamCode = ($loopCode[$i] == 'MT1') ? "(T0.TeamCode LIKE '".$loopCode[$i]."%' OR T0.TeamCode LIKE 'EXP%')" : "T0.TeamCode LIKE '".$loopCode[$i]."%'";
            $SQL_Ukey = 
                "SELECT GROUP_CONCAT(CONCAT(\"'\", T0.uKey, \"'\")) AS uKey, T0.TeamCode, T0.DocStatus
                FROM saletarget T0
                WHERE $WheTeamCode AND T0.DocYear = ".$_POST['YearSelect']." AND T0.DocStatus != 'I'
                ORDER BY T0.TeamCode";
            //echo $SQL_Ukey."\n";
            $GROUP_Ukey = MySQLSelect($SQL_Ukey);
            $SQLSlpCode = ($_POST['YearSelect'] <= 2022) ? "T0.SlpCode8" : "T0.SlpCode";
            $SQL_SlpCode = 
                "SELECT GROUP_CONCAT($SQLSlpCode) AS SlpCode
                FROM oslp T0
                WHERE $WheTeamCode AND T0.Ukey IN (".$GROUP_Ukey['uKey'].") AND $SQLSlpCode IS NOT NULL";
            //echo $SQL_SlpCode;
            $GROUP_SlpCode = MySQLSelect($SQL_SlpCode);
            $SLPteam[$loopCode[$i]] = $GROUP_SlpCode['SlpCode'];
        }

        // if($_POST['YearSelect'] <= 2022) {
        //     $SQLSlpCode = "T1.SlpCode8";
        // } else {
        //     $SQLSlpCode = "T1.SlpCode";
        // }
        // $sql = "SELECT $SQLSlpCode AS 'SlpCode', T0.TeamCode 
        //         FROM saletarget T0 
        //         LEFT JOIN oslp T1 ON T1.Ukey = T0.Ukey
        //         WHERE T0.DocYear = ".$_POST['YearSelect']." AND T0.DocStatus != 'I' AND $SQLSlpCode IS NOT NULL ORDER BY T0.TeamCode";
        // $sqlQRY = MySQLSelectX($sql);
        // while($result = mysqli_fetch_array($sqlQRY)) {
        //     switch ($result['TeamCode']) {
        //         case 'TT101':
        //             $SLPteam['TT1'] .= $result['SlpCode'].",";
        //             break;
        //         case 'TT201':
        //         case 'TT202':
        //         case 'TT203':
        //             $SLPteam['TT2'] .= $result['SlpCode'].",";
        //             break;
        //         case 'OUL':
        //             $SLPteam['OUL'] .= $result['SlpCode'].",";
        //             break;
        //         case 'ONL':
        //             $SLPteam['ONL'] .= $result['SlpCode'].",";
        //             break;
        //         case 'MT100':
        //         case 'EXP101':
        //             $SLPteam['MT1'] .= $result['SlpCode'].",";
        //             break;
        //         case 'MT200':
        //             $SLPteam['MT2'] .= $result['SlpCode'].",";
        //             break;
        //     }
        // }

        // for ($i = 0; $i <= count($loopCode)-1; $i++){ $SLPteam[$loopCode[$i]] = substr($SLPteam[$loopCode[$i]],0,-1); }

        $sqlSAP ="SELECT A1.DocMonth, SUM(A1.TT100) AS TT1, SUM(A1.TT200) AS TT2, SUM(A1.OO1) AS OUL, SUM(A1.OO2) AS ONL, SUM(A1.MT101) AS MT1, SUM(A1.MT201) AS MT2 
                FROM (
                    SELECT P1.DocMonth, 
                        CASE WHEN P1.TEAM = 'TT100' THEN P1.DocTotal ELSE 0 END AS TT100, 
                        CASE WHEN P1.TEAM = 'TT200' THEN P1.DocTotal ELSE 0 END AS TT200, 
                        CASE WHEN P1.TEAM = 'OO1' THEN P1.DocTotal ELSE 0 END AS OO1, 
                        CASE WHEN P1.TEAM = 'OO2' THEN P1.DocTotal ELSE 0 END AS OO2, 
                        CASE WHEN P1.TEAM = 'MT101' THEN P1.DocTotal ELSE 0 END AS MT101, 
                        CASE WHEN P1.TEAM = 'MT201' THEN P1.DocTotal ELSE 0 END AS MT201 
                    FROM (
                        SELECT W1.DocMonth,W1.TEAM,SUM(W1.DocTotal) AS DocTotal 
                        FROM (
                            SELECT month(T0.DocDate) AS DocMonth, T0.DocTotal-T0.VatSum AS DocTotal, 
                                CASE WHEN T1.[SlpCode] IN (".$SLPteam['TT1'].") THEN 'TT100' 
                                    WHEN T1.[SlpCode] IN (".$SLPteam['TT2'].") THEN 'TT200' 
                                    WHEN T1.[SlpCode] IN (".$SLPteam['OUL'].") THEN 'OO1' 
                                    WHEN T1.[SlpCode] IN (".$SLPteam['ONL'].") THEN 'OO2' 
                                    WHEN T1.[SlpCode] IN (".$SLPteam['MT1'].") THEN 'MT101' 
                                    WHEN T1.[SlpCode] IN (".$SLPteam['MT2'].") THEN 'MT201' 
                                    END AS TEAM 
                            FROM OINV T0 
                            LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode 
                            WHERE (year(T0.DocDate)= '".$_POST['YearSelect']."') AND T0.Canceled = 'N'
                            UNION ALL 
                            SELECT month(T0.DocDate) AS DocMonth, -1*(T0.DocTotal-T0.VatSum) AS DocTotal, 
                                    CASE WHEN T1.[SlpCode] IN (".$SLPteam['TT1'].") THEN 'TT100' 
                                        WHEN T1.[SlpCode] IN (".$SLPteam['TT2'].") THEN 'TT200' 
                                        WHEN T1.[SlpCode] IN (".$SLPteam['OUL'].") THEN 'OO1' 
                                        WHEN T1.[SlpCode] IN (".$SLPteam['ONL'].") THEN 'OO2' 
                                        WHEN T1.[SlpCode] IN (".$SLPteam['MT1'].") THEN 'MT101' 
                                        WHEN T1.[SlpCode] IN (".$SLPteam['MT2'].") THEN 'MT201' 
                                        END AS TEAM 
                            FROM ORIN T0 
                            LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode 
                            WHERE (year(T0.DocDate)= '".$_POST['YearSelect']."') AND T0.Canceled = 'N'
                        ) W1 
                        GROUP BY W1.DocMonth,W1.TEAM
                    ) P1
                ) A1 
                GROUP BY A1.DocMonth 
                ORDER BY A1.DocMonth";
        // echo $sqlSAP;
        if($_POST['YearSelect'] <= 2022) {
            $sqlSAPQRY = conSAP8($sqlSAP);
        }else{
            $sqlSAPQRY = SAPSelect($sqlSAP);
        }
        $Tbody = "";
        for ($i = 0; $i <= count($loopCode)-1; $i++){ $SUM[$loopCode[$i]] = 0; }
        while($resultSAP = odbc_fetch_array($sqlSAPQRY)) {
            if(isset($resultSAP['TT1'])) { $Team['TT1'][$resultSAP['DocMonth']] = $resultSAP['TT1']; }else{ $Team['TT1'][$resultSAP['DocMonth']] = 0; }
            if(isset($resultSAP['TT2'])) { $Team['TT2'][$resultSAP['DocMonth']] = $resultSAP['TT2']; }else{ $Team['TT2'][$resultSAP['DocMonth']] = 0; }
            if(isset($resultSAP['OUL'])) { $Team['OUL'][$resultSAP['DocMonth']] = $resultSAP['OUL']; }else{ $Team['OUL'][$resultSAP['DocMonth']] = 0; }
            if(isset($resultSAP['ONL'])) { $Team['ONL'][$resultSAP['DocMonth']] = $resultSAP['ONL']; }else{ $Team['ONL'][$resultSAP['DocMonth']] = 0; }
            if(isset($resultSAP['MT1'])) { $Team['MT1'][$resultSAP['DocMonth']] = $resultSAP['MT1']; }else{ $Team['MT1'][$resultSAP['DocMonth']] = 0; }
            if(isset($resultSAP['MT2'])) { $Team['MT2'][$resultSAP['DocMonth']] = $resultSAP['MT2']; }else{ $Team['MT2'][$resultSAP['DocMonth']] = 0; }

            $SUM['TT1'] = $SUM['TT1']+$Team['TT1'][$resultSAP['DocMonth']];
            $SUM['TT2'] = $SUM['TT2']+$Team['TT2'][$resultSAP['DocMonth']];
            $SUM['OUL'] = $SUM['OUL']+$Team['OUL'][$resultSAP['DocMonth']];
            $SUM['ONL'] = $SUM['ONL']+$Team['ONL'][$resultSAP['DocMonth']];
            $SUM['MT1'] = $SUM['MT1']+$Team['MT1'][$resultSAP['DocMonth']];
            $SUM['MT2'] = $SUM['MT2']+$Team['MT2'][$resultSAP['DocMonth']];

            $Month_Number[$resultSAP['DocMonth']] = $resultSAP['DocMonth'];
        }
        // Charts
        for ($i = 0; $i <= 12; $i++){ $CM[$i] = 0; }
        $sqlCharts="SELECT TeamCode, M01, M02, M03, M04, M05, M06, M07, M08, M09, M10, M11, M12 
                    FROM saletarget 
                    WHERE DocYear = ".$_POST['YearSelect']." AND TeamCode IN ('TT101','TT201','TT202','TT203','OUL','ONL','MT100','MT200','EXP101') AND DocStatus != 'I'";
        $sqlChartsQRY = MySQLSelectX($sqlCharts);
        while($resultCharts = mysqli_fetch_array($sqlChartsQRY)) {
            switch ($resultCharts['TeamCode']) {
                case 'TT101':
                    for($i = 1; $i <= 12; $i++) {
                        if($i < 10){
                            $CM[$i] = $CM[$i]+$resultCharts['M0'.$i];
                        }else{
                            $CM[$i] = $CM[$i]+$resultCharts['M'.$i];
                        }
                    }
                    break;
                case 'TT201':
                case 'TT202':
                case 'TT203':
                    for($i = 1; $i <= 12; $i++) {
                        if($i < 10){
                            $CM[$i] = $CM[$i]+$resultCharts['M0'.$i];
                        }else{
                            $CM[$i] = $CM[$i]+$resultCharts['M'.$i];
                        }
                    }
                    break;
                case 'OUL':
                    for($i = 1; $i <= 12; $i++) {
                        if($i < 10){
                            $CM[$i] = $CM[$i]+$resultCharts['M0'.$i];
                        }else{
                            $CM[$i] = $CM[$i]+$resultCharts['M'.$i];
                        }
                    }
                    break;
                case 'ONL':
                    for($i = 1; $i <= 12; $i++) {
                        if($i < 10){
                            $CM[$i] = $CM[$i]+$resultCharts['M0'.$i];
                        }else{
                            $CM[$i] = $CM[$i]+$resultCharts['M'.$i];
                        }
                    }
                    break;
                case 'MT100':
                case 'EXP101':
                    for($i = 1; $i <= 12; $i++) {
                        if($i < 10){
                            $CM[$i] = $CM[$i]+$resultCharts['M0'.$i];
                        }else{
                            $CM[$i] = $CM[$i]+$resultCharts['M'.$i];
                        }
                    }
                    break;
                case 'MT200':
                    for($i = 1; $i <= 12; $i++) {
                        if($i < 10){
                            $CM[$i] = $CM[$i]+$resultCharts['M0'.$i];
                        }else{
                            $CM[$i] = $CM[$i]+$resultCharts['M'.$i];
                        }
                    }
                    break;
            }
        }
        for($i = 1; $i <= 12; $i++) {
            $arrCol["CM".$i] = $CM[$i];
        }
        for($i = 1; $i <= 12; $i++) {
            if(isset($Month_Number[$i])) {
                $arrCol["smM".$i] = ($Team['TT1'][$i]+$Team['TT2'][$i]+$Team['OUL'][$i]+$Team['ONL'][$i]+$Team['MT1'][$i]+$Team['MT2'][$i]);
            }else{
                $arrCol["smM".$i] = 0;
            }
        }

        // Get Target
        if($_POST['YearSelect'] <= 2022) {
            $SlctSlpCode = "SlpCode8";
        } else {
            $SlctSlpCode = "SlpCode";
        }
        $TargetSQL = 
        "SELECT
            A0.TeamCode, GROUP_CONCAT(A0.SlpCode) AS 'SlpCode',
            SUM(A0.M01) AS 'M01', SUM(A0.M02) AS 'M02', SUM(A0.M03) AS 'M03',
            SUM(A0.M04) AS 'M04', SUM(A0.M05) AS 'M05', SUM(A0.M06) AS 'M06',
            SUM(A0.M07) AS 'M07', SUM(A0.M08) AS 'M08', SUM(A0.M09) AS 'M09',
            SUM(A0.M10) AS 'M10', SUM(A0.M11) AS 'M11', SUM(A0.M12) AS 'M12',
            SUM(A0.YEAR) AS 'YEAR'
        FROM (
            SELECT 
                CASE
                    WHEN T0.TeamCode IN ('MT100')  THEN 'MT1'
                    WHEN T0.TeamCode IN ('EXP101') THEN 'EXP'
                    WHEN T0.TeamCode IN ('MT200')  THEN 'MT2'
                    WHEN T0.TeamCode IN ('TT101')  THEN 'TT1'
                    WHEN T0.TeamCode IN ('TT201','TT202','TT203') THEN 'TT2'
                ELSE T0.TeamCode END AS 'TeamCode',
                (SELECT GROUP_CONCAT($SlctSlpCode) FROM OSLP P0 WHERE P0.TeamCode = T0.TeamCode) AS 'SlpCode',
                T0.TeamCode AS 'SubTeam',
                SUM(T0.M01) AS 'M01', SUM(T0.M02) AS 'M02', SUM(T0.M03) AS 'M03',
                SUM(T0.M04) AS 'M04', SUM(T0.M05) AS 'M05', SUM(T0.M06) AS 'M06',
                SUM(T0.M07) AS 'M07', SUM(T0.M08) AS 'M08', SUM(T0.M09) AS 'M09',
                SUM(T0.M10) AS 'M10', SUM(T0.M11) AS 'M11', SUM(T0.M12) AS 'M12',
                SUM(T0.M01+T0.M02+T0.M03+T0.M04+T0.M05+T0.M06+T0.M07+T0.M08+T0.M09+T0.M10+T0.M11+T0.M12) AS 'YEAR'
            FROM saletarget T0 
            WHERE
                T0.DocYear = ".$_POST['YearSelect']." AND T0.DocStatus = 'A' AND T0.TeamCode != 'PTA100'
            GROUP BY T0.TeamCode
        ) A0 GROUP BY A0.TeamCode";
        $TargetQRY = MySQLSelectX($TargetSQL);
        for($m = 1; $m <= 12; $m++) { $Tar[$m] = 0; }
        while($TargetRST = mysqli_fetch_array($TargetQRY)) {
            for($m = 1; $m <= 12; $m++) {
                if($m < 10) {
                    $Tar[$m] = $Tar[$m]+$TargetRST['M0'.$m];
                }else{
                    $Tar[$m] = $Tar[$m]+$TargetRST['M'.$m];
                }
            }
        }
        
        for($i = 1; $i <= 12; $i++) {
            if(isset($Month_Number[$i])) {
                $Tbody .="<tr class='text-right'>".
                            "<td class='text-center fw-bold'>".FullMonth($i)."</td>".
                            "<td>".number_format($Team['TT1'][$i],0)."</td>".
                            "<td>".number_format($Team['TT2'][$i],0)."</td>".
                            "<td>".number_format($Team['OUL'][$i],0)."</td>".
                            "<td>".number_format($Team['ONL'][$i],0)."</td>".
                            "<td>".number_format($Team['MT1'][$i],0)."</td>".
                            "<td>".number_format($Team['MT2'][$i],0)."</td>".
                            "<td class='fw-bolder'>".number_format(($Team['TT1'][$i]+$Team['TT2'][$i]+$Team['OUL'][$i]+$Team['ONL'][$i]+$Team['MT1'][$i]+$Team['MT2'][$i]),0)."</td>".
                            "<td class='fw-bolder'>".number_format($Tar[$i],0)."</td>";
                            if($Tar[$i] != 0) {
                                $Tbody .="
                                <td class='fw-bolder'>".number_format((($Team['TT1'][$i]+$Team['TT2'][$i]+$Team['OUL'][$i]+$Team['ONL'][$i]+$Team['MT1'][$i]+$Team['MT2'][$i])/$Tar[$i])*100,2)."</td>";
                            }else{
                                $Tbody .="
                                <td class='fw-bolder'>0.00</td>";
                            }
                        $Tbody .=
                        "</tr>";
                $arrCol["CMDTT1".$i] = $Team['TT1'][$i];
                $arrCol["CMDTT2".$i] = $Team['TT2'][$i];
                $arrCol["CMDOUL".$i] = $Team['OUL'][$i];
                $arrCol["CMDONL".$i] = $Team['ONL'][$i];
                $arrCol["CMDMT1".$i] = $Team['MT1'][$i];
                $arrCol["CMDMT2".$i] = $Team['MT2'][$i];
            }else{
                $Tbody .="<tr class='text-right'>".
                            "<td class='text-center fw-bold'>".FullMonth($i)."</td>".
                            "<td>0</td>".
                            "<td>0</td>".
                            "<td>0</td>".
                            "<td>0</td>".
                            "<td>0</td>".
                            "<td>0</td>".
                            "<td class='fw-bolder'>0</td>".
                            "<td class='fw-bolder'>0</td>".
                            "<td class='fw-bolder'>0</td>".
                        "</tr>";
                $arrCol["CMDTT1".$i] = 0;
                $arrCol["CMDTT2".$i] = 0;
                $arrCol["CMDOUL".$i] = 0;
                $arrCol["CMDONL".$i] = 0;
                $arrCol["CMDMT1".$i] = 0;
                $arrCol["CMDMT2".$i] = 0;
            }
        }
        $Tfoot = "<tr class='text-right table-active'>".
                        "<td class='text-center text-primary fw-bolder'>ยอดขายรวม</td>".
                        "<td class='text-primary fw-bolder'>".number_format($SUM['TT1'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($SUM['TT2'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($SUM['OUL'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($SUM['ONL'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($SUM['MT1'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($SUM['MT2'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format(($SUM['TT1']+$SUM['TT2']+$SUM['OUL']+$SUM['ONL']+$SUM['MT1']+$SUM['MT2']),0)."</td>".
                        "<td class='text-primary fw-bolder'></td>".
                        "<td class='text-primary fw-bolder'></td>".
                    "</tr>";
        $SQLTfoot ="SELECT
                        SUM(A0.TT1) AS 'TT1', SUM(A0.TT2) AS 'TT2', SUM(A0.OUL) AS 'OUL', SUM(A0.ONL) AS 'ONL', SUM(A0.MT1) AS 'MT1', SUM(A0.MT2) AS 'MT2'
                    FROM (
                            SELECT
                                CASE WHEN T0.TeamCode = 'TT101' THEN SUM(T0.TrgAmount) ELSE 0 END AS 'TT1',
                                CASE WHEN T0.TeamCode LIKE 'TT2%' THEN SUM(T0.TrgAmount) ELSE 0 END AS 'TT2',
                                CASE WHEN T0.TeamCode = 'OUL' THEN SUM(T0.TrgAmount) ELSE 0 END AS 'OUL',
                                CASE WHEN T0.TeamCode = 'ONL' THEN SUM(T0.TrgAmount) ELSE 0 END AS 'ONL',
                                CASE WHEN T0.TeamCode LIKE 'MT1%' OR T0.TeamCode LIKE 'EXP%' THEN SUM(T0.TrgAmount) ELSE 0 END AS 'MT1',
                                CASE WHEN T0.TeamCode LIKE 'MT2%' THEN SUM(T0.TrgAmount) ELSE 0 END AS 'MT2'
                            FROM teamtarget T0
                            WHERE T0.DocYear = ".$_POST['YearSelect']." AND T0.DocStatus = 'A'
                            GROUP BY T0.TeamCode
                    ) A0";
        $resultTfoot = MySQLSelect($SQLTfoot);

        $Tfoot .= "<tr class='text-right table-danger'>".
                        "<td class='text-center text-primary fw-bolder'>เป้าปี ".$_POST['YearSelect']."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($resultTfoot['TT1'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($resultTfoot['TT2'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($resultTfoot['OUL'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($resultTfoot['ONL'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($resultTfoot['MT1'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format($resultTfoot['MT2'],0)."</td>".
                        "<td class='text-primary fw-bolder'>".number_format(($resultTfoot['TT1']+$resultTfoot['TT2']+$resultTfoot['OUL']+$resultTfoot['ONL']+$resultTfoot['MT1']+$resultTfoot['MT2']),0)."</td>".
                        "<td class='text-primary fw-bolder'></td>".
                        "<td class='text-primary fw-bolder'></td>".
                    "</tr>";
        if($resultTfoot['TT1'] != 0 && $resultTfoot['TT1'] != '') { $perTT1 = number_format(($SUM['TT1']/$resultTfoot['TT1'])*100,2); }else{ $perTT1 = 0.00; }
        if($resultTfoot['TT2'] != 0 && $resultTfoot['TT2'] != '') { $perTT2 = number_format(($SUM['TT2']/$resultTfoot['TT2'])*100,2); }else{ $perTT2 = 0.00; }
        if($resultTfoot['OUL'] != 0 && $resultTfoot['OUL'] != '') { $perOUL = number_format(($SUM['OUL']/$resultTfoot['OUL'])*100,2); }else{ $perOUL = 0.00; }
        if($resultTfoot['ONL'] != 0 && $resultTfoot['ONL'] != '') { $perONL = number_format(($SUM['ONL']/$resultTfoot['ONL'])*100,2); }else{ $perONL = 0.00; }
        if($resultTfoot['MT1'] != 0 && $resultTfoot['MT1'] != '') { $perMT1 = number_format(($SUM['MT1']/$resultTfoot['MT1'])*100,2); }else{ $perMT1 = 0.00; }
        if($resultTfoot['MT2'] != 0 && $resultTfoot['MT2'] != '') { $perMT2 = number_format(($SUM['MT2']/$resultTfoot['MT2'])*100,2); }else{ $perMT2 = 0.00; }
        $perSUM = $SUM['TT1']+$SUM['TT2']+$SUM['OUL']+$SUM['ONL']+$SUM['MT1']+$SUM['MT2'];
        $perTar = $resultTfoot['TT1']+$resultTfoot['TT2']+$resultTfoot['OUL']+$resultTfoot['ONL']+$resultTfoot['MT1']+$resultTfoot['MT2'];
        if($perTar != 0 && $perTar != "") { $perSUMALL = number_format(($perSUM/$perTar)*100,2); }else{ $perSUMALL = 0.00; }
        $Tfoot .= "<tr class='text-right table-success'>".
                        "<td class='text-center text-primary fw-bolder'>คิดเป็น %</td>".
                        "<td class='text-primary fw-bolder'>".$perTT1."%</td>".
                        "<td class='text-primary fw-bolder'>".$perTT2."%</td>".
                        "<td class='text-primary fw-bolder'>".$perOUL."%</td>".
                        "<td class='text-primary fw-bolder'>".$perONL."%</td>".
                        "<td class='text-primary fw-bolder'>".$perMT1."%</td>".
                        "<td class='text-primary fw-bolder'>".$perMT2."%</td>".
                        "<td class='text-primary fw-bolder'>".$perSUMALL."%</td>".
                        "<td class='text-primary fw-bolder'></td>".
                        "<td class='text-primary fw-bolder'></td>".
                    "</tr>";

        $arrCol['Tbody'] = $Tbody;
        $arrCol['Tfoot'] = $Tfoot;
    }

// ยอกขายทีมรายบุคคล
    if($_GET['a'] == 'IndiSale') {
        switch ($_SESSION['DeptCode']){
            case 'DP005' : //TT2
                
            break;
            case 'DP006' : //MT1
                
            break;
            case 'DP007' : //MT2
                
            break;
            case 'DP008' : //TT1 //OUL
                
            break;
            default: break;
        }
    }

// ข้อมูลการขาย
    if($_GET['a'] == 'SelectItemCode') {
        $ItemCode = $_POST['ItemCode'];
        $SQLSAP = "SELECT T0.ItemCode, T1.CodeBars, T1.ItemName, T1.SalUnitMsr, T0.WhsCode, T0.OnHand, T0.OnOrder, 
                    (T0.OnHand - T0.IsCommited + T0.OnOrder) AS Available, 
                    CASE WHEN T1.LastPurDat = '2022-12-31' THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07 AS LastPurPrc
                FROM OITW T0 
                LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode 
                LEFT JOIN OWHS T2 ON T0.WhsCode = T2.WhsCode
                LEFT JOIN KBI_DB2022.dbo.OITM T3 ON T0.ItemCode = T3.ItemCode
                WHERE T1.InvntItem = 'Y' AND T0.ItemCode = '$ItemCode' AND T2.location !='8' AND (T0.OnHand > 0  OR T0.IsCommited > 0 OR T0.OnOrder > 0) ORDER BY T2.location, T0.WhsCode";
        $QRYSAP = SAPSelect($SQLSAP);
        $s = 0;
        $ItemCode = array();
        while($resultSAP = odbc_fetch_array($QRYSAP)) {
            $ItemCode[$s]['ItemCode']   = $resultSAP['ItemCode'];
            $ItemCode[$s]['CodeBars']   = $resultSAP['CodeBars'];
            $ItemCode[$s]['ItemName']   = conutf8($resultSAP['ItemName']);
            $ItemCode[$s]['SalUnitMsr'] = conutf8($resultSAP['SalUnitMsr']);
            $ItemCode[$s]['WhsCode']    = $resultSAP['WhsCode'];
            $ItemCode[$s]['OnHand']     = $resultSAP['OnHand'];
            $ItemCode[$s]['OnOrder']    = $resultSAP['OnOrder'];
            $ItemCode[$s]['Available']  = $resultSAP['Available'];
            $ItemCode[$s]['LastPurPrc'] = $resultSAP['LastPurPrc'];
            $s++;
        }

        $tbody = "";
        $num = 1;
        for($i = 0; $i < $s; $i++) {
            $tbody.="<tr>
                        <td class='text-center'>".$num."</td>
                        <td class='text-center'>".$ItemCode[$i]['ItemCode']."</td>
                        <td class='text-center'>".$ItemCode[$i]['CodeBars']."</td>
                        <td>".$ItemCode[$i]['ItemName']."</td>
                        <td class='text-center'>".$ItemCode[$i]['WhsCode']."</td>
                        <td class='text-right'>".number_format($ItemCode[$i]['OnHand'],0)."</td>
                        <td class='text-right'>
                            ".(($ItemCode[$i]['OnOrder'] > 0) ? "<a href='javascript:void(0);' onclick='ViewOnOrder(\"".$ItemCode[$i]['ItemCode']."\");'>".number_format($ItemCode[$i]['OnOrder'],0)."</a>" : number_format($ItemCode[$i]['OnOrder'],0))."
                        </td>";
                        if(($ItemCode[$i]['OnHand']+$ItemCode[$i]['OnOrder']) != $ItemCode[$i]['Available']) {
                            $tbody.="<td class='text-right'><a href='javascript:void(0);' onclick='DetailAvailable(\"".$ItemCode[$i]['ItemCode']."\",\"".$ItemCode[$i]['WhsCode']."\");'>".number_format($ItemCode[$i]['Available'],0)."</td>";
                        }else{
                            $tbody.="<td class='text-right'>".number_format($ItemCode[$i]['Available'],0)."</td>";
                        }
                        $tbody.="
                        <td class='text-center'>".$ItemCode[$i]['SalUnitMsr']."</td>";
                        if (($_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 19) OR $_SESSION['DeptCode'] == 'DP003'){
                            $tbody .= "<td class='text-right'>".number_format($ItemCode[$i]['LastPurPrc'],2)."</td>";
                        }
            $tbody .="</tr>";
            $num++;
        }
        if($s == 0) {
            if (($_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 19) OR $_SESSION['DeptCode'] == 'DP003'){
                $tbody .= "<td colspan='10' class='text-center'>ไม่มีข้อมูล :)</td>";
            }else{
                $tbody .= "<td colspan='9' class='text-center'>ไม่มีข้อมูล :)</td>";
            }
        }
        $arrCol['tbody'] = $tbody;
    }

    if($_GET['a'] == 'SelectCardCode') {
        $CardCode = $_POST['CardCode'];

        // ข้อมูลลูกค้า
        $SQL1 = "SELECT T0.CardCode, T0.CardName, T0.City, T0.GroupCode, T0.SlpCode, T1.SlpName
                FROM OCRD T0
                JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                WHERE T0.CardCode = '$CardCode'";
        $QRY1 = SAPSelect($SQL1);
        $result1 = odbc_fetch_array($QRY1);
        $arrCol['CusName'] = "<i class='fas fa-user-alt'></i> ".$result1['CardCode']." - ".conutf8($result1['CardName'])." | <i class='fas fa-house-user'></i> ".conutf8($result1['City']);

        // ข้อมูลการขายสินค้า
        $SQL2 ="SELECT MAX(P1.[MONTH]) AS 'MONTH',SUM(P1.[PSTYEAR_SALE]) AS 'PASTYEAR', SUM(P1.[CURYEAR_SALE]) AS 'CURRYEAR'
                    FROM
                        (SELECT MONTH(OINV.[DocDate]) AS 'MONTH',
                        CASE
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 1) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 2) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 3) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 4) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 5) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 6) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 7) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 8) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 9) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 10) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 11) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(OINV.[DocDate]) = 12) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        END AS 'PSTYEAR_SALE',
                        CASE
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 1) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 2) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 3) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 4) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 5) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 6) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 7) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 8) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 9) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 10) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 11) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                            WHEN (YEAR(OINV.[DocDate]) = YEAR(GETDATE()) AND MONTH(OINV.[DocDate]) = 12) THEN SUM(OINV.[DocTotal]-OINV.[VatSum])
                        END AS 'CURYEAR_SALE'  																					
                    FROM OINV
                    WHERE YEAR(OINV.[DocDate]) >= YEAR(GETDATE())-1 AND OINV.[CardCode] = '$CardCode'
                    GROUP BY YEAR(OINV.[DocDate]),MONTH(OINV.[DocDate])
                    UNION ALL
                    SELECT MONTH(ORIN.[DocDate]) AS 'MONTH',
                        CASE
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 1) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 2) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 3) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 4) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 5) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 6) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 7) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 8) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 9) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 10) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 11) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE())-1 AND MONTH(ORIN.[DocDate]) = 12) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        END AS 'PSTYEAR_SALE',
                        CASE
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 1) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 2) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 3) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 4) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 5) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 6) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 7) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 8) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 9) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 10) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 11) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                            WHEN (YEAR(ORIN.[DocDate]) = YEAR(GETDATE()) AND MONTH(ORIN.[DocDate]) = 12) THEN -SUM(ORIN.[DocTotal]-ORIN.[VatSum])
                        END AS 'CURYEAR_SALE'    																					
                    FROM ORIN
                    WHERE YEAR(ORIN.[DocDate]) >= YEAR(GETDATE())-1 AND ORIN.[CardCode] = '$CardCode'
                    GROUP BY YEAR(ORIN.[DocDate]),MONTH(ORIN.[DocDate])
                ) P1
                GROUP BY P1.[Month]
                ORDER BY P1.[Month]";

        $sqlP2QRY = SAPSelect($SQL2); /* EDIT Y */
        $cYEAR = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
        $pYEAR = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
        if(date("Y") == 2023) {
            while ($resultP2 = odbc_fetch_array($sqlP2QRY)) {
                $cYEAR[$resultP2['MONTH']] = $resultP2['CURRYEAR'];
            }
            $sqlP2QRY_conSAP8 = conSAP8($SQL2);
            while ($resultP2_conSAP8 = odbc_fetch_array($sqlP2QRY_conSAP8)) {
                $pYEAR[$resultP2_conSAP8['MONTH']] = $resultP2_conSAP8['PASTYEAR'];
            }
        }else{
            while ($resultP2 = odbc_fetch_array($sqlP2QRY)) {
                $cYEAR[$resultP2['MONTH']] = $resultP2['CURRYEAR'];
                $pYEAR[$resultP2['MONTH']] = $resultP2['PASTYEAR'];
            }
        }
        $AllcYEAR = 0;
        $Tbody1 = "<tr class='text-right'>";
            $Tbody1 .= "<td class='fw-bolder'>ยอดขาย ".date("Y")."</td>";
            for($i = 1; $i <= 12; $i++) {
                $Tbody1 .= "<td><a href='javascript:void(0);' class='ViewDetailSM' onclick='ViewDetailSaleMonth(".date("Y").",".$i.")'>".number_format($cYEAR[$i],0)."</a></td>";
                $AllcYEAR = $AllcYEAR+$cYEAR[$i];
            }
            $Tbody1 .= "<td class='fw-bolder text-primary'>".number_format($AllcYEAR,0)."</td>";
        $Tbody1 .= "</tr>";

        $AllpYEAR = 0;
        $Tbody1 .= "<tr class='text-right'>";
            $Tbody1 .= "<td class='fw-bolder'>ยอดขาย ".(date("Y")-1)."</td>";
            for($i = 1; $i <= 12; $i++) {
                $Tbody1 .= "<td><a href='javascript:void(0);' class='ViewDetailSM' onclick='ViewDetailSaleMonth(".(date("Y")-1).",".$i.")'>".number_format($pYEAR[$i],0)."</a></td>";
                $AllpYEAR = $AllpYEAR+$pYEAR[$i];
            }
            $Tbody1 .= "<td class='fw-bolder text-primary'>".number_format($AllpYEAR,0)."</td>";
        $Tbody1 .= "</tr>";
        $arrCol['Tbody1'] = $Tbody1;

        // รายการสินค้าขายดี 20  
        $cYear = date("Y");
        $pYear = date("Y")-1;
        $SQLTop20 ="SELECT TOP 20 W1.ItemCode, W1.CodeBars, W1.Dscription, W2.SalUnitMsr, SUM(W1.YO) AS YO, SUM(W1.YO*W1.Price) AS oldYEAR, SUM(W1.YN) AS YN, SUM(W1.YN*W1.Price) AS nowYEAR
                    FROM 
                        (SELECT T1.ItemCode, T1.CodeBars, T1.Dscription, T1.Price,
                            CASE WHEN YEAR(T0.DocDate) = '$pYear' THEN T1.Quantity ELSE 0 END AS YO,
                            CASE WHEN YEAR(T0.DocDate) = '$cYear'  THEN T1.Quantity ELSE 0 END AS YN
                        FROM OINV T0
                        JOIN INV1 T1 ON T0.DocEntry = T1.DocEntry
                        WHERE YEAR(T0.DocDate) >= '$pYear' AND T0.CardCode = '$CardCode' AND T1.ItemCode IS NOT NULL
                        UNION ALL
                        SELECT T1.ItemCode, T1.CodeBars, T1.Dscription, T1.Price,
                            CASE WHEN YEAR(T0.DocDate) = '$pYear' THEN -T1.Quantity ELSE 0 END AS YO,
                            CASE WHEN YEAR(T0.DocDate) = '$cYear' THEN -T1.Quantity ELSE 0 END AS YN
                        FROM ORIN T0
                        JOIN RIN1 T1 ON T0.DocEntry = T1.DocEntry
                        WHERE YEAR(T0.DocDate) >= '$pYear' AND T0.CardCode = '$CardCode' AND T1.ItemCode IS NOT NULL
                        ) W1
                    LEFT JOIN OITM W2 ON W1.ItemCode = W2.ItemCode
                    GROUP BY W1.ItemCode, W1.CodeBars, W1.Dscription, W2.SalUnitMsr
                    ORDER BY nowYEAR DESC, oldYEAR DESC";
        $QRYTop20 = SAPSelect($SQLTop20);
        $Tbody2 = "";
        $row = 0;
        while ($resultCTop20 = odbc_fetch_array($QRYTop20)) {
            $row++;
            $Tbody2 .= 
            "<tr>
                <td class='text-center'>".$row."</td>
                <td class='text-center'>".$resultCTop20['ItemCode']."</td>
                <td class='text-center'>".$resultCTop20['CodeBars']."</td>
                <td>". conutf8($resultCTop20['Dscription'])."</td>
                <td class='text-center'>".conutf8($resultCTop20['SalUnitMsr'])."</td>
                <td class='text-right'>".number_format($resultCTop20['YO'],0)."</td>
                <td class='text-right'>".number_format($resultCTop20['oldYEAR'],2)."</td>
                <td class='text-right'>".number_format($resultCTop20['YN'],0)."</td>
                <td class='text-right'>".number_format($resultCTop20['nowYEAR'],2)."</td>
            </tr>";
        }

        if($row == 0) {
            $Tbody2 = "<tr><td colspan='7' class='text-center'>ไม่มีข้อมูล :)</td></tr>";
        }
        $arrCol['Tbody2'] = $Tbody2;

        // รายการขายล่าสุด
        $SQL_TopBill = "SELECT TOP 10 T0.DocDate, T1.BeginStr, T0.DocEntry, T0.DocNum, T2.SlpName, (T0.DocTotal-T0.VatSum) AS 'DOC_TOTAL'
                        FROM OINV T0
                        LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
                        JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
                        WHERE T0.CardCode = '$CardCode'
                        ORDER BY T0.DocEntry DESC";
        $QRY_TopBill = SAPSelect($SQL_TopBill);
        $r1 = 0;
        while($result_TopBill = odbc_fetch_array($QRY_TopBill)) {
            $Bill_Date[$r1]   = $result_TopBill['DocDate'];
            $Bill_Entry[$r1]  = $result_TopBill['DocEntry'];
            $Bill_DocNum[$r1] = $result_TopBill['BeginStr'].$result_TopBill['DocNum'];
            $Bill_Total[$r1]  = $result_TopBill['DOC_TOTAL'];
            $r1++;
        }
        // รายการคืนล่าสุด
        $SQL_ReBill = "SELECT TOP 10 T0.DocDate, T1.BeginStr, T0.DocEntry, T0.DocNum, T2.SlpName, (T0.DocTotal-T0.VatSum) AS 'DOC_TOTAL', T0.NumAtCard
                        FROM ORIN T0
                        LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
                        LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
                        WHERE T0.CardCode = '$CardCode' AND T1.BeginStr != 'CN-'
                        ORDER BY T0.DocEntry DESC";
        $QRY_ReBill = SAPSelect($SQL_ReBill);
        $r2 = 0;
        while($result_ReBill = odbc_fetch_array($QRY_ReBill)) {
            $Return_Date[$r2]   = $result_ReBill['DocDate'];
            $Return_Entry[$r2]  = $result_ReBill['DocEntry']; 
            $Return_DocNum[$r2] = $result_ReBill['NumAtCard']; 
            $Return_Total[$r2]  = $result_ReBill['DOC_TOTAL'];
            $r2++;
        }
        $Tbody3 = "";
        if($r1 != 0 || $r2 != 0) {
            for($i = 0; $i < 10; $i++) {
                $Tbody3 .= "<tr>";
                    if(isset($Bill_Date[$i])){
                        $Tbody3 .= "<td class='text-center'>".date("d/m/Y",strtotime($Bill_Date[$i]))."</td>";
                    }else{
                        $Tbody3 .= "<td>&nbsp;</td>"; 
                    }
                    if(isset($Bill_DocNum[$i])) {
                        $Tbody3 .= "<td class='text-center'><a href='javascript:void(0);' class='Modal-Bill' databill-entry='".$Bill_Entry[$i]."' style='border:0; background-color:transparent; color:#000000;'>".$Bill_DocNum[$i]."</td>";
                    }else{
                        $Tbody3 .= "<td>&nbsp;</td>"; 
                    }
                    if(isset($Bill_Total[$i])) {
                        $Tbody3 .= "<td class='text-right'>".number_format($Bill_Total[$i],2)."</td>";
                    }else{
                        $Tbody3 .= "<td>&nbsp;</td>"; 
                    }
                    if(isset($Return_Date[$i])) { 
                        $Tbody3 .= "<td class='text-center'>".date("d/m/Y",strtotime($Return_Date[$i]))."</td>"; 
                    }else{ 
                        $Tbody3 .= "<td></td>"; 
                    }
                    if(isset($Return_DocNum[$i])) { 
                        $Tbody3 .= "<td class='text-center'><a href='javascript:void(0);' class='Modal-ReBill' datarebill-entry='".$Return_Entry[$i]."' style='border:0; background-color:transparent; color:#000000;'>".$Return_DocNum[$i]."</td>";
                    }else{ 
                        $Tbody3 .= "<td>&nbsp;</td>"; 
                    }
                    if(isset($Return_Total[$i])) { 
                        $Tbody3 .= "<td class='text-right'>".number_format($Return_Total[$i],2)."</td>";
                    }else{ 
                        $Tbody3 .= "<td>&nbsp;</td>"; 
                    }
                $Tbody3 .= "</tr>";
            }
        }else{
            $Tbody3 .= "<tr>
                            <td colspan='6' class='text-center'>ไม่มีข้อมูล :)</td>
                        </tr>";
        }
        $arrCol['Tbody3'] = $Tbody3;
    }

    if($_GET['a'] == 'ViewDetailSaleMonth') {
        $Year = $_POST['Year'];
        $Month = $_POST['Month'];
        $CardCode = $_POST['CardCode'];

        $SQL = 
            "SELECT A0.*
            FROM (
                SELECT I1.ItemCode, I1.Dscription, ISNULL(I2.BeginStr, 'IV-') AS BeginStr, I0.DocNum, I1.[LineTotal]
                FROM OINV I0
                LEFT JOIN INV1 I1 ON I1.DocEntry = I0.DocEntry
                LEFT JOIN NNM1 I2 ON I2.Series = I0.Series 
                WHERE I0.[CardCode] = '$CardCode' AND YEAR(I0.[DocDate]) = $Year AND MONTH(I0.[DocDate]) = $Month
                UNION ALL
                SELECT R1.ItemCode, R1.Dscription, ISNULL(R2.BeginStr, 'IV-') AS BeginStr, R0.DocNum, -R1.[LineTotal]
                FROM ORIN R0
                LEFT JOIN RIN1 R1 ON R1.DocEntry = R0.DocEntry
                LEFT JOIN NNM1 R2 ON R2.Series = R0.Series 
                WHERE R0.[CardCode] = '$CardCode' AND YEAR(R0.[DocDate]) = $Year AND MONTH(R0.[DocDate]) = $Month
            ) A0
            ORDER BY A0.DocNum, A0.ItemCode";
        $QRY = SAPSelect($SQL);
        $tbody = ""; $r = 0;
        while($RST = odbc_fetch_array($QRY)) {
            $r++;
            $tbody .= "
            <tr>
                <td class='text-center'>$r</td>
                <td class='text-center'>".$RST['ItemCode']."</td>
                <td>".conutf8($RST['Dscription'])."</td>
                <td class='text-center'>".$RST['BeginStr']."".$RST['DocNum']."</td>
                <td class='text-right'>".number_format($RST['LineTotal'],0)."</td>
            </tr>";
        }
        $arrCol['tbody'] = ($r != 0) ? $tbody : "<tr><td colspan='5' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
    }

    if($_GET['a'] == 'DetailAvailable') {
        $ItemCode = $_POST['ItemCode'];
        $WhsCode  = $_POST['WhsCode'];

        $SQL = "
        SELECT T1.DocEntry, T2.BeginStr, T1.DocNum, T1.CardCode, T1.CardName, T3.SlpName, T3.U_Dim1, T0.OpenQty, T1.DocDate 
        FROM RDR1 T0 
        LEFT JOIN ORDR T1 ON T0.DocEntry = T1.DocEntry 
        LEFT JOIN NNM1 T2 ON T1.Series = T2.Series 
        LEFT JOIN OSLP T3 ON T1.SlpCode = T3.SlpCode
        WHERE T0.ItemCode = '$ItemCode' AND T0.WhsCode = '$WhsCode' AND T0.LineStatus = 'O' AND T1.DocStatus = 'O'";
        $QRY = SAPSelect($SQL);
        $tbody = "";
        while($RST = odbc_fetch_array($QRY)) {
            $tbody .= "
            <tr>
                <td class='text-center'><a href='javascript:void(0);' onclick='ViewDetail(".$RST['DocEntry'].",\"".$ItemCode."\");'>".$RST['BeginStr'].$RST['DocNum']."</td>
                <td class='text-center'>".date("d/m/Y",strtotime($RST['DocDate']))."</td>
                <td class='text-center'>".$RST['CardCode']."</td>
                <td>".conutf8($RST['CardName'])."</td>
                <td>".conutf8($RST['SlpName'])."</td>
                <td class='text-center'>".$RST['U_Dim1']."</td>
                <td class='text-right'>".number_format($RST['OpenQty'],0)."</td>
            </tr>";
        }
        $arrCol['tbody'] = $tbody;
    }

    if($_GET['a'] == 'ViewDetail') {
        $DocEntry = $_POST['DocEntry'];

        $GetSQL =
            "SELECT
                /* SO HEADER */
                T0.DocEntry, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'SODocNum', T0.CardCode, T0.CardName,
                T3.SlpName, T0.DocDate, T0.DocDueDate, T0.Comments, T0.U_PONo, T0.AtcEntry,
                /* SO DETAIL */
                T1.VisOrder,T1.ItemCode, CASE WHEN (T1.SubCatNum = '' OR T1.SubCatNum IS NULL) THEN T1.CodeBars ELSE T1.SubCatNum END AS 'CodeBars', T1.Dscription, T1.WhsCode, T1.Quantity, T1.UnitMsr, 
                T1.PriceBefDi, 
                T1.DiscPrcnt, T1.U_DiscP1, T1.U_DiscP2, T1.U_DiscP3, T1.U_DiscP4, T1.U_DiscP5,
                T1.LineTotal,
                /* SO FOOTER */
                T0.DocTotal, T0.VatSum, (T4.lastName+' '+T4.firstName) AS 'OwnerName'
            FROM ORDR T0
            LEFT JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry
            LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
            LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
            LEFT JOIN OHEM T4 ON T0.OwnerCode = T4.empID
            WHERE T0.DocEntry = $DocEntry
            ORDER BY T1.VisOrder ASC";
        $GetQRY = SAPSelect($GetSQL);

        /* GET PICKED NAME AND TABLE */
        $PKSQL = "SELECT T0.ID, T0.DateCreate, T0.UKeyPicker, T0.TablePacking FROM picker_soheader T0 WHERE T0.SODocEntry = $DocEntry AND T0.DocType = 'ORDR' LIMIT 1";
        $Rows  = ChkRowDB($PKSQL);

        if($Rows > 0) {
            $PKRST = MySQLSelect($PKSQL);
            $arrCol['HD']['PickID']     = $PKRST['ID'];
            $UName = MySQLSelect("SELECT CONCAT(uName, ' ', uLastName, ' (', uNickName, ')') AS FullName  FROM users WHERE uKey = '".$PKRST['UKeyPicker']."'");
            $arrCol['HD']['PickUkey']   = $UName['FullName'];
            $arrCol['HD']['TablePack']  = $PKRST['TablePacking'];
            $arrCol['HD']['DateCreate'] = date("Y-m-d\TH:i",strtotime($PKRST['DateCreate']));
        } else {
            $arrCol['HD']['PickID']     = NULL;
            $arrCol['HD']['PickUkey']   = NULL;
            $arrCol['HD']['TablePack']  = NULL;
            $arrCol['HD']['DateCreate'] = NULL;
        }
        
        $arrCol['HD']['DocEntry'] = NULL;

        $i = 0;
        while($GetRST = odbc_fetch_array($GetQRY)) {
            if($arrCol['HD']['DocEntry'] == NULL) {
                $SODocEntry = $GetRST['DocEntry'];
                $arrCol['HD']['DocEntry']   = $GetRST['DocEntry'];
                $arrCol['HD']['SODocNum']   = $GetRST['SODocNum'];
                $arrCol['HD']['CardCode']   = conutf8($GetRST['CardCode']." | ".$GetRST['CardName']);
                $arrCol['HD']['DocDate']    = date("Y-m-d",strtotime($GetRST['DocDate']));
                $arrCol['HD']['DocDueDate'] = date("Y-m-d",strtotime($GetRST['DocDueDate']));
                $arrCol['HD']['SlpName']    = conutf8($GetRST['SlpName']);
                $arrCol['HD']['Comments']   = conutf8($GetRST['Comments']);
                $arrCol['HD']['U_PONo']     = conutf8($GetRST['U_PONo']);
                $arrCol['FT']['DocTotal']   = $GetRST['DocTotal'];
                $arrCol['FT']['VatSum']     = $GetRST['VatSum'];
                $arrCol['FT']['OwnerName']  = conutf8($GetRST['OwnerName']);
                if($GetRST['AtcEntry'] != NULL) {
                    $AtcEntry = $GetRST['AtcEntry'];
                }
            }
            $arrCol['BD_'.$i]['VisOrder']   = $GetRST['VisOrder'];
            $arrCol['BD_'.$i]['ItemCode']   = $GetRST['ItemCode'];
            $arrCol['BD_'.$i]['CodeBars']   = $GetRST['CodeBars'];
            $arrCol['BD_'.$i]['Dscription'] = conutf8($GetRST['Dscription']);
            $arrCol['BD_'.$i]['WhsCode']    = conutf8($GetRST['WhsCode']);
            $arrCol['BD_'.$i]['Quantity']   = $GetRST['Quantity'];
            $arrCol['BD_'.$i]['UnitMsr']    = conutf8($GetRST['UnitMsr']);
            $arrCol['BD_'.$i]['PriceBefDi'] = $GetRST['PriceBefDi'];
            $arrCol['BD_'.$i]['LineTotal']  = $GetRST['LineTotal'];

            if ($GetRST['U_DiscP5'] != NULL and $GetRST['U_DiscP5'] != "" and $GetRST['U_DiscP5'] != 0.00) {
                $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%+" . number_format($GetRST['U_DiscP4'], 2) . "%+" . number_format($GetRST['U_DiscP5'], 2) . "%";
            } elseif ($GetRST['U_DiscP4'] != NULL and $GetRST['U_DiscP4'] != "" and $GetRST['U_DiscP4'] != 0.00) {
                $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%+" . number_format($GetRST['U_DiscP4'], 2) . "%";
            } elseif ($GetRST['U_DiscP3'] != NULL and $GetRST['U_DiscP3'] != "" and $GetRST['U_DiscP3'] != 0.00) {
                $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%+" . number_format($GetRST['U_DiscP3'], 2) . "%";
            } elseif ($GetRST['U_DiscP2'] != NULL and $GetRST['U_DiscP2'] != "" and $GetRST['U_DiscP2'] != 0.00) {
                $Discount = number_format($GetRST['U_DiscP1'], 2) . "%+" . number_format($GetRST['U_DiscP2'], 2) . "%";
            } elseif ($GetRST['U_DiscP1'] != NULL and $GetRST['U_DiscP1'] != "" and $GetRST['U_DiscP1'] != 0.00) {
                $Discount = number_format($GetRST['U_DiscP1'], 2) . "%";
            } else {
                $Discount = NULL;
            }
            $arrCol['BD_'.$i]['Discount']   = $Discount;
            $i++;
        }
        $Rows = $i;
        $arrCol['Rows'] = $Rows;

        /* ATTACHMENT */

        if(isset($AtcEntry)) {
            $AttSQL  = "SELECT T0.trgtPath, T0.FileName,T0.FileExt FROM ATC1 T0 WHERE T0.AbsEntry = $AtcEntry ORDER BY T0.Line ASC";
            $AttRows = ChkRowSAP($AttSQL);
            if($AttRows == 0) {
                $arrCol['AttRows'] = 0;
            } else {
                $arrCol['AttRows'] = $AttRows;

                $AttQRY = SAPSelect($AttSQL);
                $i = 0;
                while($AttRST = odbc_fetch_array($AttQRY)) {
                    $arrCol['AT_'.$i]['FileName'] = conutf8($AttRST['FileName'].".".$AttRST['FileExt']);
                    $arrCol['AT_'.$i]['FilePath'] = "file:".str_replace(" ","%20",str_replace("\\","/",$AttRST['trgtPath']))."/".conutf8(str_replace(" ","%20",$AttRST['FileName']).".".$AttRST['FileExt']);
                    $i++;
                }
            }
        } else {
            $AttSQL =
                "SELECT
                    T0.VisOrder, T0.FileOriName, T0.FileDirName, T0.FileExt
                FROM order_attach T0
                LEFT JOIN order_header T1 ON T0.DocEntry = T1.DocEntry
                WHERE T1.ImportEntry = $SODocEntry AND T0.FileStatus = 'A'";
            $AttRows = ChkRowDB($AttSQL);
            if($AttRows == 0) {
                $arrCol['AttRows'] = 0;
            } else {
                $arrCol['AttRows'] = $AttRows;
                
                $AttQRY = MySQLSelectX($AttSQL);
                $i = 0;
                while($AttRST = mysqli_fetch_array($AttQRY)) {
                    $arrCol['AT_'.$i]['FileName'] = $AttRST['FileOriName'].".".$AttRST['FileExt'];
                    $arrCol['AT_'.$i]['FilePath'] = "../FileAttach/SO/".$AttRST['FileDirName'].".".$AttRST['FileExt'];
                    $i++;
                }
            }
        }

    }

    if($_GET['a'] == 'ModalBill') {
        $BillEntry = $_POST['BillEntry'];
        $SQL_HEAD ="SELECT T1.BeginStr, T0.DocNum, T0.DocDate, T0.U_PONo, T0.CardCode, T0.CardName, T0.DocTotal, T0.VatSum, T0.DocEntry, T2.SlpName 
                    FROM OINV T0
                    LEFT JOIN NNM1 T1 ON T0.Series = T1.Series 
                    LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
                    WHERE T0.DocEntry = '$BillEntry'";
        $QRY_HEAD = SAPSelect($SQL_HEAD);
        $result_HEAD = odbc_fetch_array($QRY_HEAD);
        $arrCol['H_CardName'] = conutf8($result_HEAD['CardName']);
        $arrCol['H_DocNum'] = $result_HEAD['DocNum'];
        $arrCol['H_SlpName'] = conutf8($result_HEAD['SlpName']);
        $arrCol['H_DocDate'] = date("d/m/Y",strtotime($result_HEAD['DocDate']));
        $arrCol['H_U_PONo'] = $result_HEAD['U_PONo'];

        $SQL_DETAIL =  "SELECT T0.DocEntry, T0.LineNum, T0.ItemCode, T0.Dscription, T0.Quantity, T0.unitMsr, T0.PriceBefDi AS Price,
                            T0.U_DiscP1, T0.U_DiscP2, T0.U_DiscP3, T0.U_DiscP4, T0.LineTotal 
                        FROM INV1 T0 
                        WHERE T0.DocEntry = '$BillEntry' 
                        ORDER BY T0.LineNum ASC";
        $QRY_DETAIL = SAPSelect($SQL_DETAIL);
        $num = 1;
        $Tbody = "";
        $DocTotal = 0;
        while($result_DETAIL = odbc_fetch_array($QRY_DETAIL)) {
            $Tbody .=   "<tr>
                            <td class='text-center'>".$num."</td>
                            <td class='text-center'>".$result_DETAIL['ItemCode']."</td>
                            <td>".conutf8($result_DETAIL['Dscription'])."</td>
                            <td class='text-right'>".number_format($result_DETAIL['Quantity'],0)."</td>
                            <td class='text-center'>".conutf8($result_DETAIL['unitMsr'])."</td>
                            <td class='text-right'>".number_format($result_DETAIL['Price'],2)."</td>";
                            if(0 < $result_DETAIL['U_DiscP4']){
                                $Tbody .= "<td class='text-center'>".number_format($result_DETAIL['U_DiscP1'],2)."%+".number_format($result_DETAIL['U_DiscP2'],2)."%+".number_format($result_DETAIL['U_DiscP3'],2)."%+".number_format($result_DETAIL['U_DiscP4'])."%</td>";
                            }else{
                                if(0 < $result_DETAIL['U_DiscP3']){
                                    $Tbody .= "<td class='text-center'>".number_format($result_DETAIL['U_DiscP1'])."%+".number_format($result_DETAIL['U_DiscP2'])."%+".number_format($result_DETAIL['U_DiscP3'])."%</td>";
                                }else{
                                    if(0 < $result_DETAIL['U_DiscP2']) {
                                        $Tbody .= "<td class='text-center'>".number_format($result_DETAIL['U_DiscP1'],2)."%+".number_format($result_DETAIL['U_DiscP2'],2)."%</td>";
                                    }else{
                                        $Tbody .= "<td class='text-center'>".number_format($result_DETAIL['U_DiscP1'],2)."%</td>";
                                    }
                                }
                            }
                            $Tbody .= "<td class='text-right'>".number_format($result_DETAIL['LineTotal'],2)."</td>";
            $Tbody .=   "</tr>";
            $DocTotal = $DocTotal+$result_DETAIL['LineTotal'];
            $num++;
        }
        $arrCol['Tbody'] = $Tbody;
        $arrCol['DocTotal'] = number_format($DocTotal,2);
        $arrCol['VatSum'] = number_format($result_HEAD['VatSum'],2);
        $arrCol['Total'] = number_format($result_HEAD['DocTotal'],2);
    }

    if($_GET['a'] == 'ModalReBill'){
        $BillEntry = $_POST['BillEntry'];
        $SQL_HEAD ="SELECT T1.BeginStr, T0.DocNum, T0.DocDate, T0.U_PONo, T0.CardCode, T0.CardName, T0.DocTotal, 
                        T0.VatSum, T0.DocEntry, T2.SlpName, T0.U_RefInv, T0.U_RefNoCust, T0.NumAtCard, T0.U_CNReason 
                    FROM ORIN T0 
                    LEFT JOIN NNM1 T1 ON T0.Series = T1.Series 
                    LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode 
                    WHERE T0.DocEntry = '$BillEntry'";
        $QRY_HEAD = SAPSelect($SQL_HEAD);
        $result_HEAD = odbc_fetch_array($QRY_HEAD);
        $arrCol['CusName'] = $result_HEAD['CardCode']." - ".conutf8($result_HEAD['CardName']);
        $arrCol['Date']    = date("d/m/Y",strtotime($result_HEAD ['DocDate']));
        $arrCol['DucNum']  = $result_HEAD['BeginStr'].$result_HEAD ['DocNum'];
        $arrCol['SlpName'] = conutf8($result_HEAD['SlpName']);
        $arrCol['RefInv']  = conutf8($result_HEAD['U_RefInv']);
        $arrCol['SR']      = $result_HEAD['NumAtCard'];
        $arrCol['CusRef']  = $result_HEAD['U_RefNoCust'];

        $SQL_DETAIL =  "SELECT T0.DocEntry, T0.LineNum, T0.ItemCode, T0.Dscription, T0.Quantity, T0.unitMsr, 
                            T0.Price, T0.U_DiscP1, T0.U_DiscP2, T0.U_DiscP3, T0.U_DiscP4, T0.LineTotal 
                        FROM RIN1 T0
                        WHERE T0.DocEntry = '$BillEntry' 
                        ORDER BY T0.LineNum ASC";
        $QRY_DETAIL = SAPSelect($SQL_DETAIL);
        $num = 1;
        $Tbody = "";
        $DocTotal = 0;
        while($result_DETAIL = odbc_fetch_array($QRY_DETAIL)) {
            $Tbody .=   "<tr>
                            <td class='text-center'>".$num."</td>
                            <td class='text-center'>".$result_DETAIL['ItemCode']."</td>
                            <td>".conutf8($result_DETAIL['Dscription'])."</td>
                            <td class='text-right'>".number_format($result_DETAIL['Quantity'],0)."</td>
                            <td class='text-center'>".conutf8($result_DETAIL['unitMsr'])."</td>
                            <td class='text-right'>".number_format($result_DETAIL['Price'],2)."</td>";
                            if(0 < $result_DETAIL['U_DiscP4']){
                                $Tbody .= "<td class='text-center'>".number_format($result_DETAIL['U_DiscP1'],2)."%+".number_format($result_DETAIL['U_DiscP2'],2)."%+".number_format($result_DETAIL['U_DiscP3'],2)."%+".number_format($result_DETAIL['U_DiscP4'])."%</td>";
                            }else{
                                if(0 < $result_DETAIL['U_DiscP3']){
                                    $Tbody .= "<td class='text-center'>".number_format($result_DETAIL['U_DiscP1'])."%+".number_format($result_DETAIL['U_DiscP2'])."%+".number_format($result_DETAIL['U_DiscP3'])."%</td>";
                                }else{
                                    if(0 < $result_DETAIL['U_DiscP2']) {
                                        $Tbody .= "<td class='text-center'>".number_format($result_DETAIL['U_DiscP1'],2)."%+".number_format($result_DETAIL['U_DiscP2'],2)."%</td>";
                                    }else{
                                        $Tbody .= "<td class='text-center'>".number_format($result_DETAIL['U_DiscP1'],2)."%</td>";
                                    }
                                }
                            }
                            $Tbody .= "<td class='text-right'>".number_format($result_DETAIL['LineTotal'],2)."</td>";
            $Tbody .=   "</tr>";
            $DocTotal = $DocTotal+$result_DETAIL['LineTotal'];
            $num++;
        }
        $arrCol['Tbody']    = $Tbody;
        $arrCol['Remark']   = conutf8($result_HEAD['U_CNReason']);
        $arrCol['DocTotal'] = number_format($DocTotal,2);
        $arrCol['VatSum']   = number_format($result_HEAD['VatSum'],2);
        $arrCol['Total']    = number_format($result_HEAD['DocTotal'],2);
    }

    if($_GET['a'] == 'SelectCardCodeHis') {
        $ItemCode = $_POST['ItemCode'];
        $Year     = $_POST['Year'];
        $Month    = $_POST['Month'];
        $SQL = "SELECT T2.BeginStr, T1.DocNum, T1.NumAtCard ,T1.DocEntry, T1.DocDate, T1.CardCode, T1.CardName, T0.Quantity, T3.SalUnitMsr, T0.Price, T0.LineTotal, T4.U_Dim1, T4.SlpName
                FROM INV1 T0
                LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
                LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
                LEFT JOIN OITM T3 ON T3.ItemCode = T0.ItemCode
                LEFT JOIN OSLP T4 ON T4.SlpCode = T1.SlpCode
                WHERE T0.ItemCode = '$ItemCode' AND Month(T1.DocDate) = '$Month' AND YEAR(T1.DocDate) = '$Year'  
                ORDER BY T1.DocDate DESC, T1.CardCode, T0.LineNum";
        if($Year < 2023) {
            $QRY = conSAP8($SQL);
        }else{
            $QRY = SAPSelect($SQL);
        }
        $Row = 0;
        $Tbody = array();
        $Quantity = 0;
        $Total = 0;
        while($result = odbc_fetch_array($QRY)) {
            $Tbody["DocDate"][$Row]   = date("d/m/Y", strtotime($result['DocDate']));
            $Tbody["CardName"][$Row]  = $result['CardCode']." - ".conutf8($result['CardName']);
            $Tbody["CH"][$Row]        = $result['U_Dim1'];
            $Tbody["NumAtCard"][$Row] = $result['NumAtCard'];
            $Tbody["SlpName"][$Row]   = conutf8($result['SlpName']);
            $Tbody["Quantity"][$Row]  = number_format($result['Quantity'],0);
            $Tbody["Unit"][$Row]      = conutf8($result['SalUnitMsr']);
            $Tbody["Price"][$Row]     = number_format($result['Price'],2);
            $Tbody["Total"][$Row]     = number_format($result['LineTotal'],2);
            $Row++;

            $Quantity = $Quantity+$result['Quantity'];
            $Total    = $Total+$result['LineTotal'];
        }
        $arrCol["Tbody"]    = $Tbody;
        $arrCol["Row"]      = $Row;
        $arrCol["Quantity"] = number_format($Quantity,0);
        $arrCol["Total"]    = number_format($Total,2);
    }

    if($_GET['a'] == 'ExportCardCodeHis') {
        $ItemCode = $_POST['ItemCode'];
        $Year     = $_POST['Year'];
        $Month    = $_POST['Month'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getProperties()
            ->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setTitle("รายงานประวัติการขายสินค้า บจ.คิงบางกอก อินเตอร์เทรด")
            ->setSubject("รายงานประวัติการขายสินค้า บจ.คิงบางกอก อินเตอร์เทรด");
        $spreadsheet->getDefaultStyle()->getFont()->setSize(8);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);

        // Style 
        $PageHeader = [
            'font' => [ 'bold' => true, 'size' => 9.1 ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
        ];
        $TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
        $TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
        $TextBold  = ['font' => [ 'bold' => true ]];

        $sheet->setCellValue('A1',"ลำดับ");
        $sheet->setCellValue('B1',"วันที่");
        $sheet->setCellValue('C1',"รหัสร้านค้า");
        $sheet->setCellValue('D1',"ชื่อร้านค้า");
        $sheet->setCellValue('E1',"CH");
        $sheet->setCellValue('F1',"พนักงานขาย");
        $sheet->setCellValue('G1',"จำนวน");
        $sheet->setCellValue('H1',"หน่วย");
        $sheet->setCellValue('I1',"ราคาขาย");
        $sheet->setCellValue('J1',"ราคารวม");

        $sheet->getStyle('A1:J1')->applyFromArray($PageHeader);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(8);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(9);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(18);

        $SQL = "SELECT T2.BeginStr, T1.DocNum, T1.NumAtCard ,T1.DocEntry, T1.DocDate, T1.CardCode, T1.CardName, T0.Quantity, T3.SalUnitMsr, T0.Price, T0.LineTotal, T4.U_Dim1, T4.SlpName
                FROM INV1 T0
                LEFT JOIN OINV T1 ON T0.DocEntry = T1.DocEntry
                LEFT JOIN NNM1 T2 ON T1.Series = T2.Series
                LEFT JOIN OITM T3 ON T3.ItemCode = T0.ItemCode
                LEFT JOIN OSLP T4 ON T4.SlpCode = T1.SlpCode
                WHERE T0.ItemCode = '$ItemCode' AND Month(T1.DocDate) = '$Month' AND YEAR(T1.DocDate) = '$Year'  
                ORDER BY T1.DocDate DESC, T1.CardCode, T0.LineNum";
        if($Year < 2023) {
            $QRY = conSAP8($SQL);
        }else{
            $QRY = SAPSelect($SQL);
        }

        $Row = 1; $No = 0;
        while($result = odbc_fetch_array($QRY)) {
            $Row++; $No++;
            // ลำดับ
            $sheet->setCellValue('A'.$Row,$No);
            $sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);
            // วันที่
            $sheet->setCellValue('B'.$Row,date("d/m/Y", strtotime($result['DocDate'])));
            $sheet->getStyle('B'.$Row)->applyFromArray($TextCenter);
            // รหัสร้านค้า
            $sheet->setCellValue('C'.$Row,$result['CardCode']);
            $sheet->getStyle('C'.$Row)->applyFromArray($TextCenter);
            // ชื่อร้านค้า
            $sheet->setCellValue('D'.$Row,conutf8($result['CardName']));
            // CH
            $sheet->setCellValue('E'.$Row,$result['U_Dim1']);
            $sheet->getStyle('E'.$Row)->applyFromArray($TextCenter);
            // พนักงานขาย
            $sheet->setCellValue('F'.$Row,conutf8($result['SlpName']));
            // จำนวน
            $sheet->setCellValue('G'.$Row,$result['Quantity']);
            $sheet->getStyle('G'.$Row)->applyFromArray($TextRight);
            $spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");
            // หน่วย
            $sheet->setCellValue('H'.$Row,conutf8($result['SalUnitMsr']));
            $sheet->getStyle('H'.$Row)->applyFromArray($TextCenter);
            // ราคาขาย
            $sheet->setCellValue('I'.$Row,conutf8($result['Price']));
            $sheet->getStyle('I'.$Row)->applyFromArray($TextRight);
            $spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
            // ราคาขาย
            $sheet->setCellValue('J'.$Row,conutf8($result['LineTotal']));
            $sheet->getStyle('J'.$Row)->applyFromArray($TextRight);
            $spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
        }

        $writer = new Xlsx($spreadsheet);
        $FileName = "รายงานประวัติการขายสินค้า - ".date("YmdHis").".xlsx";
        $writer->save("../../../FileExport/HisSalesProduct/".$FileName);
        $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'HisSalesProduct', logFile = '$FileName', DateCreate = NOW()";
        MySQLInsert($InsertSQL);
        $arrCol['FileName'] = $FileName;
    }

    if($_GET['a'] == 'ViewOnOrder') {
        $ItemCode = $_POST['ItemCode'];
        $SQL = 
            "SELECT
                T0.DocEntry,
                T1.ItemCode, T1.Dscription, T3.U_ProductStatus, T0.DocDate, T0.DocDueDate, T2.BeginStr+CAST(T0.DocNum AS VARCHAR) AS 'DocNum',
                T1.Quantity, T1.unitMsr, T1.WhsCode, T1.U_MT1, T1.U_MT2, T1.U_TT2, T1.U_OUL, T1.U_ONL
            FROM OPOR T0
            LEFT JOIN POR1 T1 ON T0.DocEntry = T1.DocEntry
            LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
            LEFT JOIN OITM T3 ON T1.ItemCode = T3.ItemCode
            WHERE T1.ItemCode = '$ItemCode' AND T0.DocStatus = 'O' AND T1.LineStatus = 'O' AND T0.CANCELED = 'N'
            ORDER BY T0.DocDueDate ASC";
        $QRY = SAPSelect($SQL);
        $Data = ""; $DateCreate = "";
        $r = 0;
        while($RST = odbc_fetch_array($QRY)) {
            $r++;
            // $DateCreate = (($RST['DateCreate'] == '') ? "" : date("d/m/Y", strtotime($RST['DateCreate'])));
            $Data .= "
            <tr>
                <td class='text-center'>$r</td>
                <td class='text-center'>".$RST['ItemCode']."</td>
                <td>".conutf8($RST['Dscription'])."</td>
                <td class='text-center'>".$RST['U_ProductStatus']."</td>
                <td class='text-center'>".(($RST['DocDate'] == '') ? '-' : date("d/m/Y", strtotime($RST['DocDate'])))."</td>
                <td class='text-center'>".(($RST['DocDueDate'] == '') ? '-' : date("d/m/Y", strtotime($RST['DocDueDate'])))."</td>
                <td class='text-center'>".$RST['DocNum']."</td>
                <td class='text-right'>".number_format($RST['Quantity'],0)."</td>
                <td>".conutf8($RST['unitMsr'])."</td>
                <td class='text-center'>".$RST['WhsCode']."</td>
                <td class='text-right'>".number_format($RST['U_MT1'],0)."</td>
                <td class='text-right'>".number_format($RST['U_MT2'],0)."</td>
                <td class='text-right'>".number_format($RST['U_TT2'],0)."</td>
                <td class='text-right'>".number_format($RST['U_OUL'],0)."</td>
                <td class='text-right'>".number_format($RST['U_ONL'],0)."</td>
            </tr>";
        }
        if($r == 0) {
            $Data = "<tr><td colspan='15' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
        }
        $arrCol['Data'] = $Data;
        $arrCol['DateCreate'] = $DateCreate;
    }

// รายการเป้าขายสินค้า
    if($_GET['a'] == 'GetTarSale') {
        switch($_SESSION['DeptCode']) {
            case "DP005":
                if($_SESSION['uClass'] == 20) {
                    $TeamSQL = " AND (T0.MngType = 'P' AND T0.TeamCode = 'TT2' AND T0.SaleUkey LIKE ('%".$_SESSION['ukey']."%')) OR (T0.MngType = 'T' AND T0.TeamCode = 'TT2')";
                } else {
                    $TeamSQL = " AND T0.TeamCode = 'TT2'";
                }
            break;
            case "DP006": $TeamSQL = " AND T0.TeamCode = 'MT1'"; break;
            case "DP007": $TeamSQL = " AND T0.TeamCode = 'MT2'"; break;
            case "DP008":
                if($_SESSION['uClass'] == 20) {
                    $TeamSQL = " AND (T0.MngType = 'P' AND T0.TeamCode = 'OUL' AND T0.SaleUkey LIKE ('%".$_SESSION['ukey']."%')) OR (T0.MngType = 'T' AND T0.TeamCode = 'OUL')";
                } else {
                    $TeamSQL = " AND T0.TeamCode = 'OUL'";
                }
            break;
            default:
                $TeamSQL = "";
            break;
        }
    
        $SQL = "
            SELECT T0.CPEntry, T0.DocNum, T0.CPTitle, T0.TeamCode, T0.MngType, T0.CPType, T0.StartDate, T0.EndDate, T0.CPDescription, T0.DocStatus 
            FROM tarsku_header T0 
            WHERE T0.CANCELED = 'N' $TeamSQL
            ORDER BY
                CASE 
                    WHEN DATE(T0.StartDate) <= NOW() AND DATE(T0.EndDate) >= NOW() THEN 1
                    WHEN DATE(T0.StartDate) > DATE(NOW()) THEN 2
                    ELSE 3
                END
            LIMIT 5";

        $QRY = MySQLSelectX($SQL);
        $Data = ""; $r = 0;
        while($result = mysqli_fetch_array($QRY)) {
            $r++;
            $Data .= "
                <tr>
                    <td>".$result['CPTitle']."<br/>
                        <small class='text-muted'><i class='far fa-clock fa-fw fa-1x'></i> ".date("d/m/Y",strtotime($result['StartDate']))." ถึง ".date("d/m/Y",strtotime($result['EndDate']))."</small>
                    </td>
                </tr>";
        }
        if($r == 0) {
            $Data .= "
                <tr>
                    <td class='text-center'>ไม่มีรายการในขณะนี้</td>
                </tr>";
        }
        $arrCol['Data'] = $Data;
    }

    if ($_GET['a'] =='P01'){
        $sqlP01 =
            "SELECT
                T0.[WhsCode], 
                SUM(T0.[OnHand] * ( CASE WHEN T1.LastPurDat > '2022-12-31' THEN T1.[LastPurPrc] ELSE T2.[LastPurPrc] END * 1.07)) AS 'InvtCost'
            FROM OITW T0
            LEFT JOIN OITM T1 ON T0.[ItemCode] = T1.[ItemCode]
            LEFT JOIN KBI_DB2022.dbo.OITM T2 ON T1.[ItemCode] = T2.[ItemCode]
            WHERE T0.[WhsCode] IN ('P01','P02','PO2','P03')
            GROUP BY T0.[WhsCode]
            ORDER BY CASE WHEN T0.[WhsCode] = 'P01' THEN 1 ELSE 2 END"; 
        //echo $sqlP01;
        $sql1QRY = SAPSelect($sqlP01);
        while ($result1 = odbc_fetch_array($sql1QRY)){
            $output .="<tr>";
            $output .= "<td class='text-center'>".$result1['WhsCode']."</td>";
            $output .= "<td class='text-right'>".number_format($result1['InvtCost'],2)."</td>";
            $output .="</tr>";
        }
                    
        $arrCol['Data'] = $output;

    }
    if ($_GET['a'] =='WP45'){
        $sqlP01 = "SELECT T0.[WhsCode], SUM(T0.[OnHand]*(T1.[LastPurPrc]*1.07)) AS 'InvtCost'
                    FROM OITW T0
                        LEFT JOIN OITM T1 ON T0.[ItemCode] = T1.[ItemCode]
                    WHERE T0.[WhsCode] IN ('WP4','WP5')
                    GROUP BY T0.[WhsCode]
                    ORDER BY  T0.[WhsCode] ";
        //echo $sqlP01;
        $sql1QRY = SAPSelect($sqlP01);
        while ($result1 = odbc_fetch_array($sql1QRY)){
            $output .="<tr>";
            $output .= "<td class='text-center'>".$result1['WhsCode']."</td>";
            $output .= "<td class='text-right'>".number_format($result1['InvtCost'],2)."</td>";
            $output .="</tr>";
        }
                    
        $arrCol['Data'] = $output;

    }

// รายการสรุป Co Sales 
    if($_GET['a'] == 'GetCoSales') {
        $Year = $_POST['Year'];
        $Month = $_POST['Month'];
        $DeptCode = $_SESSION['DeptCode'];

        $SQL = "
            SELECT P0.uName,SUM(P0.ItemCount) AS DataList,SUM(P0.CountBill) AS CountBill,SUM(P0.DocTotal) AS DocTotal
            FROM (
                SELECT T0.CreateUkey,CONCAT(T1.uName,' (',T1.uNickName,')') AS uName,T2.DeptCode,1 AS CountBill,DocTotal,(SELECT COUNT(A0.TransID) FROM order_detail A0 WHERE A0.DocEntry = T0.DocEntry) AS ItemCount  
                FROM order_header T0 
                    LEFT JOIN users T1 ON T0.CreateUkey = T1.UKey
                    LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode 
                WHERE (YEAR(CreateDate) = $Year AND MONTH(CreateDate) = $Month) AND T2.DeptCode = '$DeptCode'  AND T0.ImPortEntry IS NOT NULL
            ) P0
            GROUP BY P0.uName";
        $QRY = MySQLSelectX($SQL);
        $Data = "";
        while($result = mysqli_fetch_array($QRY)) {
            $Data .= "
                <tr>
                    <td>".$result['uName']."</td>
                    <td class='text-right'>".number_format($result['DataList'],0)."</td>
                    <td class='text-right'>".number_format($result['CountBill'],0)."</td>
                    <td class='text-right'>".number_format($result['DocTotal'],2)."</td>
                </tr>";
        }
        $arrCol['Data'] = $Data;
    }

// มูลค่าสินค้าคลังมือสอง
    if($_GET['a'] == 'GetWarehouse2') {
        $Warehouse2 = $_POST['Warehouse2'];
        $SQL = 
            "SELECT
                T0.[WhsCode],
                SUM(T0.[OnHand] * ( CASE WHEN T1.LastPurDat > '2022-12-31' THEN T1.[LastPurPrc] ELSE T2.[LastPurPrc] END * 1.07)) AS 'InvtCost'
            FROM OITW T0
            LEFT JOIN OITM T1 ON T0.[ItemCode] = T1.[ItemCode]
            LEFT JOIN KBI_DB2022.dbo.OITM T2 ON T1.[ItemCode] = T2.[ItemCode]
            LEFT JOIN OWHS T3 ON T0.[WhsCode] = T3.[WhsCode]
            WHERE T3.[Location] = 6 AND T3.[StreetNo] = '$Warehouse2'
            GROUP BY T0.[WhsCode]
            ORDER BY T0.[WhsCode]";
        $sql1QRY = SAPSelect($SQL);
        while ($result1 = odbc_fetch_array($sql1QRY)){
            $output .="<tr>";
            $output .= "<td class=''>".$result1['WhsCode']."</td>";
            $output .= "<td class='text-right'>".number_format($result1['InvtCost'],2)."</td>";
            $output .="</tr>";
        }
                    
        $arrCol['Data'] = ($output != "") ? $output : "<tr><td colspan='2' class='text-center'>ไม่มีข้อมูล</td></tr>";
    }

// SearchMenu
    if($_GET['a'] == 'SearchMenu') {
        $txtSearch = $_POST['txtSearch'];
        if($_SESSION['uClass'] == 0){
            $WherCode = "";
        }else{
            $WherCode = "AND (T0.TypeOpen IN ('A') OR (T0.TypeOpen = 'C' AND SUBSTRING(T0.ClassOpen, ".$_SESSION['uClass'].", 1) = '1') OR (T0.TypeOpen = 'D' AND T1.DeptCode = '".$_SESSION['DeptCode']."') OR (T0.TypeOpen = 'L' AND T1.LvCode = '".$_SESSION['LvCode']."'))";
        }
        $SQL = 
            "SELECT T0.MenuKey, T0.UpKey, T0.MenuLink, T0.MenuIcon, T0.MenuName, T0.ClassOpen, T0.TypeOpen, T0.MenuLv, T1.StatusDoc 
            FROM menus T0 
            LEFT JOIN menugroup T1 ON T1.MenuKey = T0.MenuKey 
            WHERE T0.MenuName LIKE '%$txtSearch%' AND T0.MenuStatus = 'A' AND T0.MenuLink != '' $WherCode
            GROUP BY T0.MenuKey ";
        $QRY = MySQLSelectX($SQL);
        $Data = "";
        while($RST = mysqli_fetch_array($QRY)) {
            $Data .= "<a href='".$RST['MenuLink']."' class='d-flex menu-search-list align-items-center'>".$RST['MenuIcon']."&nbsp;".$RST['MenuName']."</a>";
        }
        $arrCol['Data'] = $Data;
    }
    if($_GET['a'] == 'slpdetail') {
        $ukey = $_POST['SlpCode'];
        $Year = $_POST['Year'];
        $Month = $_POST['Month'];
        $output = "";
        $SQL1 = "SELECT P0.* 
                 FROM (SELECT  'OINV' AS tb,T2.BeginStr,T0.DocNum,T0.DocDate,T0.CardCode,T0.CardName,(T0.DocTotal-T0.VatSum) AS DocTotal, CASE WHEN T0.DocTotal = 0 THEN 0 ELSE (T0.GrosProfit / (T0.DocTotal-T0.VatSum)) * 100 END AS GP, T0.DocEntry
                 FROM OINV T0
                 LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                 LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
                 WHERE T1.Memo = '$ukey' AND YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) = $Month AND T0.CANCELED = 'N'
                 UNION ALL
                 SELECT  'ORIN' AS tb,T2.BeginStr,T0.DocNum,T0.DocDate,T0.CardCode,T0.CardName,-1*(T0.DocTotal-T0.VatSum) AS DocTotal, CASE WHEN T0.DocTotal = 0 THEN 0 ELSE (T0.GrosProfit / (T0.DocTotal-T0.VatSum)) * 100 END AS GP, T0.DocEntry
                 FROM ORIN T0
                 LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                 LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
                 WHERE T1.Memo = '$ukey' AND YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) = $Month AND T0.CANCELED = 'N'
                 ) P0
                 ORDER BY P0.CardName,P0.tb,P0.DocDate,P0.DocNum";
                 //echo $SQL1;
        $sql1QRY = SAPSelect($SQL1);
        $i=0;
        while ($result1 = odbc_fetch_array($sql1QRY)){
            $i++;
            if ($result1['BeginStr'] == NULL || $result1['BeginStr'] == ""){
                $Prefix = "IV-";
            }else{
                $Prefix = $result1['BeginStr'];
            }
            if ($result1['tb'] == 'OINV'){
                $GP = number_format($result1['GP'],2)."%";
            }else{
                $GP = "";
            }

            $SQL3 = "SELECT TOP 1 (T0.[MailAddres]+' '+T0.[MailZipCod]+' '+T0.MailBlock+' '+T0.MailCity) AS 'Address' FROM OCRD T0 WHERE T0.[CardCode] = '".$result1['CardCode']."'";
            $QRY3 = SAPSelect($SQL3);
            $RST3 = odbc_fetch_array($QRY3);

            $TrClass = ($result1['GP'] < 25.00) ? " class='table-danger text-danger'" : "" ;
            $output .="<tr$TrClass>";
            $output .= "<td class='text-center'>".$i."</td>";
            $output .= "<td class='text-center'><a href='javascript:void(0);' onclick='CallIV(".$result1['DocEntry'].",\"".$result1['tb']."\")'>".$Prefix.$result1['DocNum']."</td>";
            $output .= "<td class='text-center'>".date("d/m/Y",strtotime($result1['DocDate']))."</td>";
            $output .= "<td class='text-left'>".$result1['CardCode']." ".conutf8($result1['CardName'])."</td>";
            $output .= "<td class='text-left'>".conutf8($RST3['Address'])."</td>";
            $output .= "<td class='text-right'>".number_format($result1['DocTotal'],2)."</td>";
            $output .= "<td class='text-right'>".$GP."</td>";
            $output .="</tr>";
        }
        $arrCol['Data'] = $output;     
        
        $SQL2 = 
            "SELECT T0.DocDate,T1.BeginStr,T0.DocNum,T0.CardCode,T0.CardName,T0.SlpCode,T2.SlpName,(T0.DocTotal-T0.VatSum) AS DocTotal
            FROM ODLN T0
                LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
                LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode 
            WHERE T0.CANCELED = 'N' AND T0.DocStatus = 'O' AND T2.Memo = '$ukey' AND T0.DocDate <= GETDATE() ";
        $sql2QRY = SAPSelect($SQL2);
        $output2 = ""; 
        $r = 0;
        while ($RST2 = odbc_fetch_array($sql2QRY)){
            $r++;
            $output2 .="<tr>";
                $output2 .= "<td class='text-center'>".$r."</td>";
                $output2 .= "<td class='text-center'>".$RST2['BeginStr'].$RST2['DocNum']."</td>";
                $output2 .= "<td class='text-center'>".date("d/m/Y",strtotime($RST2['DocDate']))."</td>";
                $output2 .= "<td>".$RST2['CardCode']." ".conutf8($RST2['CardName'])."</td>";
                $output2 .= "<td>".conutf8($RST2['SlpName'])."</td>";
                $output2 .= "<td class='text-right'>".number_format($RST2['DocTotal'],2)."</td>";
            $output2 .="</tr>";
        }
        $arrCol['Data2'] = $output2;  
    }


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
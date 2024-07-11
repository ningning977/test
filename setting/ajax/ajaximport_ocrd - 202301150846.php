<?php
include('../../../core/config.core.php');
include('../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
if($_SESSION['UserName'] == NULL ){
	echo '<script>window.location="../"</script>';
}

function SAPThai($ThaiText) { /* แสดงผลภาษาไทย */
    // return iconv("ISO-IR-166", "UTF-8", $ThaiText); มีปัญหาเรื่องแสดงผล Space Bar ภาษาไทย
    return str_replace("?","",iconv("ISO-8859-11", "UTF-8", $ThaiText));
}

$resultArray = array();
$arrCol = array();

if($_GET['p'] == "GetCardCode") {
    $OcrdSQL = "SELECT TOP 1000 T0.CardCode, T0.CardName FROM OCRD T0 WHERE (T0.CardCode != '' OR T0.CardName != '') ORDER BY T0.CardCode ASC";
    $OcrdQRY = SAPSelect($OcrdSQL);
    $i = 0;
    while($OcrdRST = odbc_fetch_array($OcrdQRY)) {
        $arrCol[$i]['CardCode'] = $OcrdRST['CardCode'];
        $arrCol[$i]['CardName'] = SAPThai($OcrdRST['CardName']);
        $i++;
    }
    $arrCol['Rows'] = $i;

}

if($_GET['p'] == "ImportCard") {
    $CardCode = $_POST['CardCode'];

    $SAPSQL = 
        "SELECT TOP 1
            T0.CardCode, T0.CardName, T0.FatherCard AS 'MasterCode', T0.CardType, T0.GroupCode,
            CASE WHEN T0.QryGroup1 = 'Y' OR T0.QryGroup14 = 'Y' THEN 1 ELSE 0 END AS 'BPGroup',
            T0.U_ExternalCust AS 'BCode',
            CASE
                WHEN T0.QryGroup1 = 'Y' THEN 1
                WHEN T0.QryGroup2 = 'Y' THEN 2
                WHEN T0.QryGroup3 = 'Y' THEN 3
                WHEN T0.QryGroup4 = 'Y' THEN 4
                WHEN T0.QryGroup5 = 'Y' THEN 5
                WHEN T0.QryGroup6 = 'Y' THEN 6
                WHEN T0.QryGroup7 = 'Y' THEN 7
                WHEN T0.QryGroup8 = 'Y' THEN 8
                WHEN T0.QryGroup9 = 'Y' THEN 9
                WHEN T0.QryGroup10 = 'Y' THEN 10
                WHEN T0.QryGroup11 = 'Y' THEN 11
                WHEN T0.QryGroup12 = 'Y' THEN 12
                WHEN T0.QryGroup13 = 'Y' THEN 13
                WHEN T0.QryGroup14 = 'Y' THEN 14
                WHEN T0.QryGroup15 = 'Y' THEN 15
                WHEN T0.QryGroup16 = 'Y' THEN 16
                WHEN T0.QryGroup17 = 'Y' THEN 17
                WHEN T0.QryGroup18 = 'Y' THEN 18
                WHEN T0.QryGroup19 = 'Y' THEN 19
                WHEN T0.QryGroup20 = 'Y' THEN 20
                WHEN T0.QryGroup21 = 'Y' THEN 21
                WHEN T0.QryGroup22 = 'Y' THEN 22
                WHEN T0.QryGroup23 = 'Y' THEN 23
                WHEN T0.QryGroup24 = 'Y' THEN 24
                WHEN T0.QryGroup25 = 'Y' THEN 25
                WHEN T0.QryGroup26 = 'Y' THEN 26
                WHEN T0.QryGroup27 = 'Y' THEN 27
                WHEN T0.QryGroup28 = 'Y' THEN 28
                WHEN T0.QryGroup29 = 'Y' THEN 29
                WHEN T0.QryGroup30 = 'Y' THEN 30
                WHEN T0.QryGroup31 = 'Y' THEN 31
                WHEN T0.QryGroup32 = 'Y' THEN 32
                WHEN T0.QryGroup33 = 'Y' THEN 33
                WHEN T0.QryGroup34 = 'Y' THEN 34
                WHEN T0.QryGroup35 = 'Y' THEN 35
                WHEN T0.QryGroup36 = 'Y' THEN 36
                WHEN T0.QryGroup37 = 'Y' THEN 37
                WHEN T0.QryGroup38 = 'Y' THEN 38
                WHEN T0.QryGroup39 = 'Y' THEN 39
                WHEN T0.QryGroup40 = 'Y' THEN 40
                WHEN T0.QryGroup41 = 'Y' THEN 41
                WHEN T0.QryGroup42 = 'Y' THEN 42
                WHEN T0.QryGroup43 = 'Y' THEN 43
                WHEN T0.QryGroup44 = 'Y' THEN 44
                WHEN T0.QryGroup45 = 'Y' THEN 45
                WHEN T0.QryGroup46 = 'Y' THEN 46
                WHEN T0.QryGroup47 = 'Y' THEN 47
                WHEN T0.QryGroup48 = 'Y' THEN 48
                WHEN T0.QryGroup49 = 'Y' THEN 49
                WHEN T0.QryGroup50 = 'Y' THEN 50
                WHEN T0.QryGroup51 = 'Y' THEN 51
                WHEN T0.QryGroup52 = 'Y' THEN 52
                WHEN T0.QryGroup53 = 'Y' THEN 53
                WHEN T0.QryGroup54 = 'Y' THEN 54
                WHEN T0.QryGroup55 = 'Y' THEN 55
                WHEN T0.QryGroup56 = 'Y' THEN 56
                WHEN T0.QryGroup57 = 'Y' THEN 57
                WHEN T0.QryGroup58 = 'Y' THEN 58
                WHEN T0.QryGroup59 = 'Y' THEN 59
                WHEN T0.QryGroup60 = 'Y' THEN 60
                WHEN T0.QryGroup61 = 'Y' THEN 61
                WHEN T0.QryGroup62 = 'Y' THEN 62
                WHEN T0.QryGroup63 = 'Y' THEN 63
                WHEN T0.QryGroup64 = 'Y' THEN 64
            ELSE 0 END AS 'MTGroup'
        FROM OCRD T0
        WHERE T0.CardCode = '$CardCode'";
    $SAPRow = ChkRowSAP($SAPSQL);
    if($SAPRow == 0) {
        $arrCol['Status'] = "ERR::NORESULT";
    } else {
        $arrCol['Status'] = "SUCCESS";
        $SAPQRY = SAPSelect($SAPSQL);
        $SAPRST = odbc_fetch_array($SAPQRY);

        $CardCode   = $SAPRST['CardCode'];
        $CardName   = conutf8($SAPRST['CardName']);
        $MasterCode = $SAPRST['MasterCode'];
        $CardType   = $SAPRST['CardType'];
        $GroupCode  = $SAPRST['GroupCode'];
        $BPGroup    = $SAPRST['BPGroup'];
        $BrachCode  = $SAPRST['BCode'];
        $MTGroup    = $SAPRST['MTGroup'];
        $UkeyCreate = $_SESSION['ukey'];

        $ERFSQL = "SELECT T0.CardCode FROM OCRD T0 WHERE T0.CardCode = '$CardCode'";
        $ERFRow = ChkRowDB($ERFSQL);

        if($ERFRow > 0) {
            $UpdateSQL =
                "UPDATE OCRD SET
                    MasterCode = '$MasterCode',
                    CardType = '$CardType',
                    GroupCode = '$GroupCode',
                    BPGroup = '$BPGroup',
                    BCode = '$BrachCode',
                    MTGroup = '$MTGroup',
                    UkeyUpdate = '$UkeyCreate',
                    DateUpDate = NOW()
                WHERE CardCode = '$CardCode'";
            MySQLUpdate($UpdateSQL);
        } else {
            $InsertSQL =
                "INSERT INTO OCRD SET
                    CardCode = '$CardCode',
                    CardName = '$CardName',
                    MasterCode = '$MasterCode',
                    CardType = '$CardType',
                    GroupCode = '$GroupCode',
                    BPGroup = '$BPGroup',
                    BCode = '$BrachCode',
                    MTGroup = '$MTGroup',
                    UkeyCreate = '$UkeyCreate',
                    DateCreate = NOW(),
                    CardStatus = 'A'";
            $ID = MySQLInsert($InsertSQL);
        }

        if($arrCol['Status'] == "SUCCESS") {
            $EditSQL = "SELECT T0.CardCode, T0.CardName, CONCAT(T0.Lat,', ',T0.Lon) AS 'GPS', T0.CardStatus FROM OCRD T0 WHERE T0.CardCode = '$CardCode' LIMIT 1";
            $EditRST = MySQLSelect($EditSQL);
            $arrCol['txt_CardCode']   = $EditRST['CardCode'];
            $arrCol['txt_CardName']   = $EditRST['CardName'];
            $arrCol['txt_GPS']        = $EditRST['GPS'];
            $arrCol['txt_CardStatus'] = $EditRST['CardStatus'];
        }
    }
}

if($_GET['p'] == "SaveCard") {
    $CardCode   = $_POST['txt_CardCode'];
    $CardName   = $_POST['txt_CardName'];
    if($_POST['txt_GPS'] == "") {
        $Lat        = "0";
        $Lon        = "0";
    }else{
        $LatAndLon  = explode(", ",$_POST['txt_GPS']);
        $Lat        = $LatAndLon[0];
        $Lon        = $LatAndLon[1];
    }
    $CardStatus = $_POST['txt_CardStatus'];

    $SQL_UPDATE = " UPDATE OCRD SET CardName = '$CardName', Lat = '$Lat', Lon = '$Lon', CardStatus = '$CardStatus', UkeyUpdate = '".$_SESSION['ukey']."', DateUpdate = NOW() WHERE CardCode = '$CardCode'";
    MySQLUpdate($SQL_UPDATE);
    $arrCol['Status'] = "SUCCESS";
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
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

if($_GET['p'] == "ImportItem") {
    $ItemCode = $_POST['ItemCode'];

    $SAPSQL = 
        "SELECT T0.ItemCode, T0.CodeBars, T0.ItemName, T0.SalUnitMsr, T0.U_ProductStatus, T0.DfltWH FROM OITM T0 WHERE T0.ItemCode = '$ItemCode'";
    // echo $SAPSQL;
    $SAPRow = ChkRowSAP($SAPSQL);
    if($SAPRow == 0) {
        $arrCol['Status'] = "ERR::NORESULT";
    } else {
        $arrCol['Status'] = "SUCCESS";
        $SAPQRY = SAPSelect($SAPSQL);
        $SAPRST = odbc_fetch_array($SAPQRY);

        $ItemCode      = $SAPRST['ItemCode'];
        $BarCode       = $SAPRST['CodeBars'];
        $ItemName      = conutf8($SAPRST['ItemName']);
        $MgrUnit       = conutf8($SAPRST['SalUnitMsr']);
        $ProductStatus = $SAPRST['U_ProductStatus'];
        $DftWhsCode    = conutf8($SAPRST['DfltWH']);
        $IsBom         = 0;
        $BomGroup      = 0;
        $UkeyCreate    = $_SESSION['ukey'];

        // echo $ProductStatus;


        $ERFSQL = "SELECT T0.ItemCode FROM OITM T0 WHERE T0.ItemCode = '$ItemCode'";
        $ERFRow = ChkRowDB($ERFSQL);

        if($ERFRow > 0) {
            $UpdateSQL =
                "UPDATE OITM SET
                    BarCode = '$BarCode',
                    -- ItemName = '$ItemName',
                    MgrUnit = '$MgrUnit',
                    ProductStatus = '$ProductStatus',
                    DftWhsCode = '$DftWhsCode',
                    IsBom = 0,
                    BomGroup = 0,
                    UkeyUpdate = '$UkeyCreate',
                    DateUpdate = NOW(),
                    ItemStatus = 'A'
                WHERE ItemCode = '$ItemCode'";
            MySQLUpdate($UpdateSQL);
        } else {
            $InsertSQL =
                "INSERT INTO OITM SET
                    ItemCode = '$ItemCode',
                    BarCode = '$BarCode',
                    ItemName = '$ItemName',
                    MgrUnit = '$MgrUnit',
                    ProductStatus = '$ProductStatus',
                    DftWhsCode = '$DftWhsCode',
                    IsBom = 0,
                    BomGroup = 0,
                    UkeyCreate = '$UkeyCreate',
                    DateCreate = NOW(),
                    ItemStatus = 'A'";
            $ID = MySQLInsert($InsertSQL);
        }

        if($arrCol['Status'] == "SUCCESS") {
            $file_path = "../../../json/OITM.json";
            if(file_exists($file_path)) {
                $DelJSON = unlink($file_path);
            }
            $GetJSONSQL = "SELECT T0.ItemCode, T0.BarCode, T0.ItemName, T0.IsBom, T0.ProductStatus, T0.ItemStatus FROM oitm T0 WHERE T0.ItemCode != '' AND T0.ItemName != '' ORDER BY T0.ItemCode ASC";
            if(CHKRowDB($GetJSONSQL) > 0) {
                $GetJSONQRY = MySQLSelectX($GetJSONSQL);
                $ArrJSON = array();
                $i = 0;
                while($resGetJSON = mysqli_fetch_array($GetJSONQRY)) {
                    $SQL2 = "SELECT TOP 1 ISNULL(P0.U_ProductStatus,'K') AS 'ProductStatus' FROM OITM P0 WHERE P0.ItemCode = '".$resGetJSON['ItemCode']."'";
                    $ROW2 = ChkRowSAP($SQL2);
                    if($ROW2 > 0) {
                        $QRY2 = SAPSelect($SQL2);
                        $RST2 = odbc_fetch_array($QRY2);
                        $ProductStatus = $RST2['ProductStatus'];
                    } else {
                        $ProductStatus = $resGetJSON['ProductStatus'];
                    }
                    $ArrJSON[$i]['ItemCode']      = $resGetJSON['ItemCode'];
                    $ArrJSON[$i]['BarCode']       = $resGetJSON['BarCode'];
                    $ArrJSON[$i]['ItemName']      = $resGetJSON['ItemName'];
                    $ArrJSON[$i]['IsBom']         = $resGetJSON['IsBom'];
                    $ArrJSON[$i]['ProductStatus'] = $ProductStatus;
                    $ArrJSON[$i]['ItemStatus']    = $resGetJSON['ItemStatus'];
                    $i++;
                }
                file_put_contents($file_path, json_encode($ArrJSON, JSON_UNESCAPED_UNICODE));
            }

            $EditSQL = "SELECT T0.ItemCode, T0.BarCode, T0.BarCode2, T0.BarCode3, T0.ItemName, T0.ItemName2, T0.DftWhsCode, T0.MgrUnit, T0.ItemStatus, T0.ProductStatus FROM OITM T0 WHERE T0.ItemCode = '$ItemCode' LIMIT 1";
            $EditRST = MySQLSelect($EditSQL);
            $arrCol['txt_ItemCode']      = $EditRST['ItemCode'];
            $arrCol['txt_BarCode']       = $EditRST['BarCode'];
            $arrCol['txt_BarCode2']      = $EditRST['BarCode2'];
            $arrCol['txt_BarCode3']      = $EditRST['BarCode3'];
            $arrCol['txt_ItemName']      = $EditRST['ItemName'];
            $arrCol['txt_ItemName2']     = $EditRST['ItemName2'];
            $arrCol['txt_MgrUnit']       = $EditRST['MgrUnit'];
            $arrCol['txt_DftWhsCode']    = $EditRST['DftWhsCode'];
            $arrCol['txt_ProductStatus'] = $EditRST['ProductStatus'];
            $arrCol['txt_ItemStatus']    = $EditRST['ItemStatus'];

            $ChkPrcSQL = "SELECT * FROM pricelist WHERE ItemCode = '$ItemCode' AND PriceType = 'STD'";
            if(CHKRowDB($ChkPrcSQL) == 0) {
                $InsertSQL = "INSERT INTO pricelist SET ItemCode = '$ItemCode', UkeyCreate = '".$_SESSION['ukey']."";
                MySQLInsert($InsertSQL);
            }
        }
    }
}

if($_GET['p'] == "SaveItem") {
    $ItemCode      = $_POST['txt_ItemCode'];
    $ItemName      = $_POST['txt_ItemName'];
    $ItemName2     = $_POST['txt_ItemName2'];
    $BarCode       = $_POST['txt_BarCode'];
    $BarCode2      = $_POST['txt_BarCode2'];
    $BarCode3      = $_POST['txt_BarCode3'];
    $MgrUnit       = $_POST['txt_MgrUnit'];
    $DftWhsCode    = $_POST['txt_DftWhsCode'];
    $ProductStatus = $_POST['txt_ProductStatus'];
    $ItemStatus    = $_POST['txt_ItemStatus'];

    $SQL_UPDATE = " UPDATE OITM SET ItemCode = '$ItemCode', ItemName = '$ItemName', ItemName2 = '$ItemName2', BarCode = '$BarCode', BarCode2 = '$BarCode2', BarCode3 = '$BarCode3', MgrUnit = '$MgrUnit', DftWhsCode = '$DftWhsCode', ProductStatus = '$ProductStatus', ItemStatus = '$ItemStatus' WHERE ItemCode = '$ItemCode'";
    MySQLUpdate($SQL_UPDATE);
    $arrCol['Status'] = "SUCCESS";

    if($arrCol['Status'] == "SUCCESS") {
        $file_path = "../../../json/OITM.json";
        if(file_exists($file_path)) {
            $DelJSON = unlink($file_path);
        }
        $GetJSONSQL = "SELECT T0.ItemCode, T0.BarCode, T0.ItemName, T0.IsBom, T0.ProductStatus, T0.ItemStatus FROM oitm T0 WHERE T0.ItemCode != '' AND T0.ItemName != '' ORDER BY T0.ItemCode ASC";
        if(CHKRowDB($GetJSONSQL) > 0) {
            $GetJSONQRY = MySQLSelectX($GetJSONSQL);
            $ArrJSON = array();
            $i = 0;
            while($resGetJSON = mysqli_fetch_array($GetJSONQRY)) {
                $SQL2 = "SELECT TOP 1 ISNULL(P0.U_ProductStatus,'K') AS 'ProductStatus' FROM OITM P0 WHERE P0.ItemCode = '".$resGetJSON['ItemCode']."'";
                $ROW2 = ChkRowSAP($SQL2);
                if($ROW2 > 0) {
                    $QRY2 = SAPSelect($SQL2);
                    $RST2 = odbc_fetch_array($QRY2);
                    $ProductStatus = $RST2['ProductStatus'];
                } else {
                    $ProductStatus = $resGetJSON['ProductStatus'];
                }
                $ArrJSON[$i]['ItemCode']      = $resGetJSON['ItemCode'];
                $ArrJSON[$i]['BarCode']       = $resGetJSON['BarCode'];
                $ArrJSON[$i]['ItemName']      = $resGetJSON['ItemName'];
                $ArrJSON[$i]['IsBom']         = $resGetJSON['IsBom'];
                $ArrJSON[$i]['ProductStatus'] = $ProductStatus;
                $ArrJSON[$i]['ItemStatus']    = $resGetJSON['ItemStatus'];
                $i++;
            }
            file_put_contents($file_path, json_encode($ArrJSON, JSON_UNESCAPED_UNICODE));
        }

        $EditSQL = "SELECT T0.ItemCode, T0.BarCode, T0.BarCode2, T0.BarCode3, T0.ItemName, T0.ItemName2, T0.DftWhsCode, T0.MgrUnit, T0.ItemStatus, T0.ProductStatus FROM OITM T0 WHERE T0.ItemCode = '$ItemCode' LIMIT 1";
        $EditRST = MySQLSelect($EditSQL);
        $arrCol['txt_ItemCode']      = $EditRST['ItemCode'];
        $arrCol['txt_BarCode']       = $EditRST['BarCode'];
        $arrCol['txt_BarCode2']      = $EditRST['BarCode2'];
        $arrCol['txt_BarCode3']      = $EditRST['BarCode3'];
        $arrCol['txt_ItemName']      = $EditRST['ItemName'];
        $arrCol['txt_ItemName2']     = $EditRST['ItemName2'];
        $arrCol['txt_MgrUnit']       = $EditRST['MgrUnit'];
        $arrCol['txt_DftWhsCode']    = $EditRST['DftWhsCode'];
        $arrCol['txt_ProductStatus'] = $EditRST['ProductStatus'];
        $arrCol['txt_ItemStatus']    = $EditRST['ItemStatus'];

        $ChkPrcSQL = "SELECT * FROM pricelist WHERE ItemCode = '$ItemCode' AND PriceType = 'STD'";
        if(CHKRowDB($ChkPrcSQL) == 0) {
            $InsertSQL = "INSERT INTO pricelist SET ItemCode = '$ItemCode', UkeyCreate = '".$_SESSION['ukey']."";
            MySQLInsert($InsertSQL);
        }
    }
}

if($_GET['p'] == 'SyncItem') {
    $SQL = "SELECT T0.* FROM OITM T0";
    $QRY = MySQLSelectX($SQL);
    $ChkRow = 0;
    // $CkItem = '';
    while($RST = mysqli_fetch_array($QRY)) {
        $SQLChk = "SELECT T0.ItemCode, T0.ItemName, T0.U_ProductStatus, T0.SalUnitMsr, T0.DfltWH FROM OITM T0 WHERE T0.ItemCode = '".$RST['ItemCode']."'";
        if(ChkRowSAP($SQLChk) != 0) {
            $QRYChk = SAPSelect($SQLChk);
            $RSTChk = odbc_fetch_array($QRYChk);
            if($RST['ItemName'] != conutf8($RSTChk['ItemName']) || $RST['ProductStatus'] != $RSTChk['U_ProductStatus'] || $RST['MgrUnit'] != conutf8($RSTChk['SalUnitMsr']) || $RST['DftWhsCode'] != conutf8($RSTChk['DfltWH'])) {
                $ChkRow++;
                $UPDATE = "
                    UPDATE OITM 
                    SET ItemName = '".$RSTChk['ItemName']."', ProductStatus = '".$RSTChk['U_ProductStatus']."', MgrUnit = '".conutf8($RSTChk['SalUnitMsr'])."', DftWhsCode = '".conutf8($RSTChk['DfltWH'])."', UkeyUpdate = '".$_SESSION['ukey']."', DateUpdate = NOW()
                    WHERE ItemCode = '".$RST['ItemCode']."'";
                MySQLUpdate($UPDATE);
                // $CkItem .= $RST['ItemCode'].",";
            }
        }
    }
    // echo $CkItem;
    $arrCol['ChkRow'] = $ChkRow;

    $file_path = "../../../json/OITM.json";
            if(file_exists($file_path)) {
                $DelJSON = unlink($file_path);
            }
            $GetJSONSQL = "SELECT T0.ItemCode, T0.BarCode, T0.ItemName, T0.IsBom, T0.ProductStatus, T0.ItemStatus FROM oitm T0 WHERE T0.ItemCode != '' AND T0.ItemName != '' ORDER BY T0.ItemCode ASC";
            if(CHKRowDB($GetJSONSQL) > 0) {
                $GetJSONQRY = MySQLSelectX($GetJSONSQL);
                $ArrJSON = array();
                $i = 0;
                while($resGetJSON = mysqli_fetch_array($GetJSONQRY)) {
                    $SQL2 = "SELECT TOP 1 ISNULL(P0.U_ProductStatus,'K') AS 'ProductStatus' FROM OITM P0 WHERE P0.ItemCode = '".$resGetJSON['ItemCode']."'";
                    $ROW2 = ChkRowSAP($SQL2);
                    if($ROW2 > 0) {
                        $QRY2 = SAPSelect($SQL2);
                        $RST2 = odbc_fetch_array($QRY2);
                        $ProductStatus = $RST2['ProductStatus'];
                    } else {
                        $ProductStatus = $resGetJSON['ProductStatus'];
                    }
                    $ArrJSON[$i]['ItemCode']      = $resGetJSON['ItemCode'];
                    $ArrJSON[$i]['BarCode']       = $resGetJSON['BarCode'];
                    $ArrJSON[$i]['ItemName']      = $resGetJSON['ItemName'];
                    $ArrJSON[$i]['IsBom']         = $resGetJSON['IsBom'];
                    $ArrJSON[$i]['ProductStatus'] = $ProductStatus;
                    $ArrJSON[$i]['ItemStatus']    = $resGetJSON['ItemStatus'];
                    $i++;
                }
                file_put_contents($file_path, json_encode($ArrJSON, JSON_UNESCAPED_UNICODE));
            }
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
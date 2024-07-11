<?php
include('../../../core/config.core.php');
include('../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
if($_SESSION['UserName'] == NULL ){
	echo '<script>window.location="../"</script>';
}
$resultArray = array();
$arrCol = array();

if($_GET['p'] == 'SyncSlp') {
    $SQL1 = "SELECT T0.SlpCode FROM OSLP T0";
    $SQL2 = "SELECT T0.* FROM OSLP T0 WHERE SlpCode != '-1'";

    $NumERF = CHKRowDB($SQL1);
    $NumSAP = ChkRowSAP($SQL2);

    $r = 0;
    if($NumERF != $NumSAP) {
        $QRY1 = MySQLSelectX($SQL1);
        $ArrSlpCode = array();
        while($RST1 = mysqli_fetch_array($QRY1)) { array_push($ArrSlpCode,$RST1['SlpCode']); }

        $QRY2 = SAPSelect($SQL2);
        while($RST2 = odbc_fetch_array($QRY2)){
            if(array_search($RST2['SlpCode'],$ArrSlpCode) === false) {
                $r++;
                $INSERT = 
                    "INSERT INTO OSLP 
                    SET SlpCode = ".$RST2['SlpCode'].",
                        SlpName = '".conutf8($RST2['SlpName'])."',
                        Ukey = '".conutf8($RST2['Memo'])."',
                        TeamCode = '',
                        MainTeam = '".$RST2['U_Dim1']."',
                        WhsCode = '',
                        SaleEmpCode = '".$RST2['U_Name_end']."',
                        UkeyCreate = '".$_SESSION['ukey']."',
                        DateCreate = NOW(),
                        CodeStatus = '".(($RST2['Active'] == 'Y') ? "A" : "I")."'";
                MySQLInsert($INSERT);
            }
        }
        
    }
    $arrCol['RowInsert'] = $r;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
<?php
include('../../../core/config.core.php');
include('../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";

$thisYear = date("Y");
$thisMonth = date("m");

// ยอดขายรายเดือน
if($_GET['a'] == 'ChartReportSale') {
    

    $sql1 = "SELECT (sum(OINV.DocTotal)-sum(OINV.VatSum)) as data1 from OINV LEFT JOIN OSLP ON OINV.SlpCode = OSLP.SlpCode where (year(oinv.docdate)= '".$thisYear."' and month(oinv.docdate) = '".$thisMonth."') AND OINV.CANCELED = 'N'";
    $sql1 = $sql1;
    $sql1QRY = PITASelect($sql1);
    $result1 = odbc_fetch_array($sql1QRY);

    $sql2 = "SELECT (sum(orin.doctotal)-sum(orin.vatsum)) as data2 from ORIN LEFT JOIN OSLP ON ORIN.SlpCode = OSLP.SlpCode LEFT JOIN NNM1 ON ORIN.Series = NNM1.Series where (year(orin.docdate)= '".$thisYear."' and month(orin.docdate) = '".$thisMonth."') AND ORIN.CANCELED = 'N' AND NNM1.BeginStr IN ('S1-','SR-')";
    $sql2 = $sql2;
    $sql2QRY = PITASelect($sql2);
    $result2 = odbc_fetch_array($sql2QRY);

    $DataSale = $result1['data1'] - $result2['data2'];

    $alltar = 1000000;

    if ($alltar == 0){ $alltar  = 1; }
    $perSale = number_format(($DataSale/$alltar)*100,2);

    $arrCol['DataSale'] = number_format($DataSale,0);
    $arrCol['alltar'] = number_format($alltar,0);
    $arrCol['perSale'] = $perSale;
}

if($_GET['a'] == "AppIV") {
    $SQL1 = 
        "SELECT TOP 10
            T0.DocDate, T0.DocDueDate,
            (T1.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum',
            T0.CardCode, T0.CardName, (T0.DocTotal-T0.VatSum) AS 'DocTotal',
            T2.SlpName
        FROM OINV T0
        LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
        LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
        WHERE (YEAR(T0.DocDate) = $thisYear) AND T0.CANCELED = 'N'
        ORDER BY T0.DocEntry DESC";
    $Rows = ChkRowPITA($SQL1);

    if($Rows > 0) {
        $QRY1 = PITASelect($SQL1);
        while($RST1 = odbc_fetch_array($QRY1)) {
            $output .= "<tr>";
                $output .= "<td class='text-center'>".date("d/m/Y",strtotime($RST1['DocDate']))."</td>";
                $output .= "<td class='text-center'>".date("d/m/Y",strtotime($RST1['DocDueDate']))."</td>";
                $output .= "<td class='text-center'>".$RST1['DocNum']."</td>";
                $output .= "<td>".$RST1['CardCode']." ".conutf8($RST1['CardName'])."</td>";
                $output .= "<td class='text-right'>".number_format($RST1['DocTotal'],2)."</td>";
                $output .= "<td>".conutf8($RST1['SlpName'])."</td>";
            $output .= "</tr>";
        }
    } else {
        $output .= "<tr><td class='text-center' colspan='6'>ไม่มีข้อมูล :(</td></tr>";
    }
}

if($_GET['a'] == "AppPO") {
    $SQL1 = 
        "SELECT TOP 10
            T0.DocDate, T0.DocDueDate,
            (ISNULL(T1.BeginStr,'PO-')+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum',
            T0.CardCode, T0.CardName, (T0.DocTotal-T0.VatSum) AS 'DocTotal'
        FROM OPOR T0
        LEFT JOIN NNM1 T1 ON T0.Series = T1.Series
        LEFT JOIN OSLP T2 ON T0.SlpCode = T2.SlpCode
        WHERE (YEAR(T0.DocDate) = $thisYear) AND T0.CANCELED = 'N'
        ORDER BY T0.DocEntry DESC";
    $Rows = ChkRowPITA($SQL1);

    if($Rows > 0) {
        $QRY1 = PITASelect($SQL1);
        while($RST1 = odbc_fetch_array($QRY1)) {
            $output .= "<tr>";
                $output .= "<td class='text-center'>".date("d/m/Y",strtotime($RST1['DocDate']))."</td>";
                $output .= "<td class='text-center'>".date("d/m/Y",strtotime($RST1['DocDueDate']))."</td>";
                $output .= "<td class='text-center'>".$RST1['DocNum']."</td>";
                $output .= "<td>".$RST1['CardCode']." ".conutf8($RST1['CardName'])."</td>";
                $output .= "<td class='text-right'>".number_format($RST1['DocTotal'],2)."</td>";
            $output .= "</tr>";
        }
    } else {
        $output .= "<tr><td class='text-center' colspan='5'>ไม่มีข้อมูล :(</td></tr>";
    }
    
}

$arrCol['output'] = $output;
array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
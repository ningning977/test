<?php
date_default_timezone_set('Asia/Bangkok');
include("../../../../core/config.core.php");
include("../../../../core/functions.core.php");
session_start();


require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql1 = "SELECT T0.DocNum,T0.DocType,T0.DocDate,T0.DocDueDate,T0.CardCode,T0.CardName,T0.LicTradeNum,T0.Slpcode,T1.SlpName,T0.TaxType
         FROM order_header T0
              LEFT JOIN oslp T1 ON T0.SlpCode = T1.SlpCode
         WHERE T0.DocEntry = ".$_POST['DocEntry'];
$OrderHeader = MySQLSelect($sql1);
$FileName = $OrderHeader['DocType']."V-".$OrderHeader['DocNum'];
$sql1 = "SELECT ItemCode,ItemName,Quantity,UnitPrice,Line_Disc1,Line_Disc2,Line_Disc3,Line_Disc4,LineTotal
         FROM  order_detail 
         WHERE DocEntry = '".$_POST['DocEntry']."' AND LineStatus = 'O'
         ORDER BY VisOrder";
         //echo $sql1;
$OrderDetail = MySQLSelectX($sql1);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getProperties()->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
							 ->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName']);

$sheet->setCellValue('A1', 'เลขที่ใบสั่งขาย')
       ->setCellValue('B1', 'ชื่อลูกค้า')
       ->setCellValue('C1', 'รหัสลูกค้า')
       ->setCellValue('D1', 'วันที่เอกสาร')
        ->setCellValue('E1', 'วันส่งของ')
       ->setCellValue('F1', 'พนักงานขาย')
       ->setCellValue('G1', 'TaxCode')
       ->setCellValue('H1', 'ชื่อขนส่ง')
       ->setCellValue('I1', 'รหัสสินค้า')
       ->setCellValue('J1', 'บาร์โค๊ด')
       ->setCellValue('K1', 'รายละเอียดสินค้า')
       ->setCellValue('L1', 'จำนวน')
       ->setCellValue('M1', 'หน่วย')
       ->setCellValue('N1', 'ราคา/หน่วย')
       ->setCellValue('O1', 'Disc1')
       ->setCellValue('P1', 'Disc2')
       ->setCellValue('Q1', 'Disc3')
       ->setCellValue('R1', 'Disc4')
       ->setCellValue('S1', 'เลขที่ผู้เสียภาษี')
       ->setCellValue('T1', 'TotalLine')
       ->setCellValue('U1', 'Reatail Type')
       ->setCellValue('V1', 'GrossPrice');
$i = 2;

while ($ExcelBill=mysqli_fetch_array($OrderDetail)){
    if ($i==2){
    $sheet->setCellValue('A'.$i,$OrderHeader['DocType'].'V-'.$OrderHeader['DocNum'])
          ->setCellValue('B'.$i,$OrderHeader['CardName'])
          ->setCellValue('C'.$i,$OrderHeader['CardCode'])
          ->setCellValue('D'.$i,date("d/m/Y",strtotime($OrderHeader['DocDate'])))
          ->setCellValue('E'.$i,date("d/m/Y",strtotime($OrderHeader['DocDueDate'])))
          ->setCellValue('F'.$i,$OrderHeader['SlpName'])
          ->setCellValue('H'.$i,'');
    }
    switch ($OrderHeader['TaxType']){
        case 'S07':
            $UnitPrice = $ExcelBill['UnitPrice'];
            $GrossPrice = $ExcelBill['UnitPrice']*1.07;
        break;
        case 'S00':
        case 'SNV':
            $UnitPrice = $ExcelBill['UnitPrice']*1.07;
            $GrossPrice = $ExcelBill['UnitPrice']*1.07;
        break;

    }

    $sheet->setCellValue('I'.$i,$ExcelBill['ItemCode'])
          ->setCellValue('K'.$i,$ExcelBill['ItemName'])
          ->setCellValue('L'.$i,$ExcelBill['Quantity'])
          ->setCellValue('N'.$i,$UnitPrice)
          ->setCellValue('O'.$i,number_format($ExcelBill['Line_Disc1']))
          ->setCellValue('P'.$i,number_format($ExcelBill['Line_Disc2']))
          ->setCellValue('Q'.$i,number_format($ExcelBill['Line_Disc3']))
          ->setCellValue('R'.$i,number_format($ExcelBill['Line_Disc4']))
          ->setCellValue('T'.$i,$ExcelBill['LineTotal'])
          ->setCellValue('U'.$i,'TT')
          ->setCellValue('V'.$i,$GrossPrice);

    $i++;

}
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(TRUE);
$spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(TRUE);

$sql1 = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."',
                                   ExportGroup='SaleOrder',
                                   logFile='".$FileName."'";
MySQLInsert($sql1);

$writer = new Xlsx($spreadsheet);
$writer->save("../../../../FileExport/SaleOrder/".$FileName.".xlsx");
echo $FileName.".xlsx";
?>
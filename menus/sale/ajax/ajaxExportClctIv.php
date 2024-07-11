 
 <?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

$resultArray = array();
$arrCol = array();
if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else {
    if(!isset($_POST['u'])) {
        echo '<script type="text/javascript">alert("ไม่สามารถพิมพ์เอกสารได้เนื่องจากข้อมูลไม่สมบูรณ์"); window.close();</script>';
    } else {
        $ukey = $_POST['u'];
        $pta  = FALSE;
        if(isset($_POST['PITA'])) {
            $pta = TRUE;
        }
        switch($ukey) {
            case "B60": $PrintName = "ซ่อมสินค้าหน้าร้าน";  $RptWhr = " (T0.SlpCode = 251)"; break;
            case "B98": $PrintName = "ซ่อมภายนอก (QC)"; $RptWhr = " (T0.SlpCode = 296)"; break;
            case "B99": $PrintName = "ซ่อมภายใน";       $RptWhr = " (T0.SlpCode = 291)"; break;
            case 'PITA' :
                $PrintName = 'PITA';
                $RptWhr = " (T1.Memo != '')";
                break;
            default:
                $GetNameSQL = "SELECT CONCAT(T0.uName,' ',T0.uLastName) AS 'Name' FROM users T0 WHERE T0.ukey = '$ukey' LIMIT 1";
                //echo $GetNameSQL;
                $GetNameRST = MySQLSelect($GetNameSQL);
                $PrintName = $GetNameRST['Name'];
                $RptWhr = " (T1.Memo = '$ukey' OR (T0.SlpCode IN (251,291,296) AND T3.Memo = '$ukey'))";
                break;
        }
        $GetDataSQL = "SELECT
                        'OINV' AS 'DocType', T0.DocEntry, T0.CardCode, T0.CardName, T0.DocNum, T0.NumAtCard, CASE WHEN T0.DocDate = '2022-12-31' THEN T7.DocDate ELSE T0.DocDate END AS 'DocDate', T0.DocDueDate, 
                        T0.DocTotal, T0.PaidToDate, (T0.DocTotal-T0.PaidToDate) AS 'NoPaid', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) <= 30 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B30D', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 31 AND 60 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B60D', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 61 AND 90 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B90D', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 91 AND 120 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B120D', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) >= 121 THEN (T0.DocTotal-T0.PaidToDate) ELSE null END AS 'A120D', 
                        DATEDIFF(day,T0.DocDueDate,GETDATE()-30) AS 'DueType', CASE WHEN T0.SlpCode IN (251,291,296) THEN 0 ELSE 1 END AS 'Fine' 
                    FROM OINV T0
                    LEFT JOIN KBI_DB2022.dbo.OINV T7 ON T0.NumAtCard = T7.NumAtCard
                    LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                    LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode
                    LEFT JOIN OSLP T3 ON T2.SlpCode = T3.SlpCode 
                    WHERE T1.SlpName NOT LIKE '%.3%' AND 
                        ((MONTH(T0.DocDueDate) < MONTH(GETDATE()) AND YEAR(T0.DocDueDate) = YEAR(GETDATE())) OR (YEAR(T0.DocDueDate) < YEAR(GETDATE()))) AND 
                        (T0.CANCELED = 'N' AND T0.DocStatus = 'O' AND (T0.DocTotal-T0.PaidToDate) > 0) AND $RptWhr 
                    UNION ALL 
                    SELECT
                        'ORIN' AS 'DocType', T0.DocEntry, T0.CardCode, T0.CardName, T0.DocNum, T0.NumAtCard, CASE WHEN T0.DocDate = '2022-12-31' THEN T7.DocDate ELSE T0.DocDate END AS 'DocDate', T0.DocDueDate, 
                        -T0.DocTotal, -T0.PaidToDate, -(T0.DocTotal-T0.PaidToDate) AS 'NoPaid', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) <= 30 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B30D', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 31 AND 60 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B60D', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 61 AND 90 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B90D', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) BETWEEN 91 AND 120 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'B120D', 
                        CASE WHEN DATEDIFF(day,T0.DocDueDate,GETDATE()-30) >= 121 THEN -(T0.DocTotal-T0.PaidToDate) ELSE null END AS 'A120D', 
                        DATEDIFF(day,T0.DocDueDate,GETDATE()-30) AS 'DueType', 0 AS 'Fine' 
                    FROM ORIN T0
                    LEFT JOIN KBI_DB2022.dbo.ORIN T7 ON T0.NumAtCard = T7.NumAtCard
                    LEFT JOIN OSLP T1 ON T0.SlpCode = T1.SlpCode
                    LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode
                    LEFT JOIN OSLP T3 ON T2.SlpCode = T3.SlpCode
                    LEFT JOIN NNM1 T4 ON T0.Series = T4.Series 
                    WHERE T1.SlpName NOT LIKE '%.3%' AND  ((MONTH(T0.DocDueDate) < MONTH(GETDATE()) AND YEAR(T0.DocDueDate) = YEAR(GETDATE())) OR (YEAR(T0.DocDueDate) < YEAR(GETDATE()))) AND T4.BeginStr IN ('S1-','SR-') AND 
                    (T0.CANCELED = 'N' AND T0.DocStatus = 'O' AND (T0.DocTotal-T0.PaidToDate) > 0) AND $RptWhr 
                    ORDER BY T0.CardCode, CASE WHEN T0.DocDate = '2022-12-31' THEN T7.DocDate ELSE T0.DocDate END";
        
        if($pta == TRUE){
            $ChkRow = ChkRowPITA($GetDataSQL);
        }else{
            $ChkRow = ChkRowSAP($GetDataSQL);
        }
        if($ChkRow > 0) {
            if($pta == TRUE){
                $GetDataQRY = PITASelect($GetDataSQL);
                $tetPITA = ' (PITA)';
            }else{
                $GetDataQRY = SAPSelect($GetDataSQL);
                $tetPITA = '';
            }
           
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $spreadsheet->getProperties()
                ->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
                ->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
                ->setTitle("รายงานค่าปรับหนี้เกินกำหนด".$tetPITA." บจ.คิงบางกอก อินเตอร์เทรด")
                ->setSubject("รายงานค่าปรับหนี้เกินกำหนด".$tetPITA." บจ.คิงบางกอก อินเตอร์เทรด");
            $spreadsheet->getDefaultStyle()->getFont()->setSize(8);
            $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(12); // Value x 6 = pixel in excel
            $spreadsheet->setActiveSheetIndex(0);

            /* HEADER */
            $PageHeader =
            [
                'font' => [ 'bold' => true, ],
                'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
            ];
            /* Row 1-3 */
            $sheet->setCellValue('A1',"รายงานค่าปรับเกินกำหนด");
            $sheet->setCellValue('A2',"พนักงานขาย: ".$PrintName);
            $sheet->setCellValue('A3',"วันที่ดึงข้อมูล: ".date("d/m/Y")." เวลา ".date("H:i")." น.");

            $sheet->getStyle('A1')->applyFromArray($PageHeader);
            $sheet->getStyle('A2:Q3')->applyFromArray([ 'alignment'=>['vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] ]);

            $spreadsheet->getActiveSheet()->mergeCells('A1:Q1');
            $spreadsheet->getActiveSheet()->mergeCells('A2:Q2');
            $spreadsheet->getActiveSheet()->mergeCells('A3:Q3');
            $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(24,'px');
            $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(24,'px');
            $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(24,'px');
            $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(24,'px');
            $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(24,'px');

            /* Row 4-5 */
            $sheet->setCellValue('A4',"เลขที่\nเอกสาร");
            $sheet->setCellValue('B4',"BP Ref. No");
            $sheet->setCellValue('C4',"วันที่\nเอกสาร");
            $sheet->setCellValue('D4',"กำหนด\nชำระ");
            $sheet->setCellValue('E4',"มูลค่าสุทธิ\n(บาท)");
            $sheet->setCellValue('F4',"ชำระแล้ว\n(บาท)");
            $sheet->setCellValue('G4',"ค้างชำระ\n(บาท)");
            $sheet->setCellValue('H4',"ยอดเกินกำหนด (วัน)");
            $sheet->setCellValue('M4',"จำนวนวัน\nเกินกำหนด");
            $sheet->setCellValue('N4',"ค่าปรับ");
            $sheet->setCellValue('H5',"0 - 30");
            $sheet->setCellValue('I5',"31 - 60");
            $sheet->setCellValue('J5',"61 - 90");
            $sheet->setCellValue('K5',"91 - 120");
            $sheet->setCellValue('L5',"121+");
            $sheet->setCellValue('N5',"ยอดปรับ");
            $sheet->setCellValue('O5',"SALE");
            $sheet->setCellValue('P5',"SUP.");
            $sheet->setCellValue('Q5',"MGR.");
            $sheet->getStyle('A4:Q5')->applyFromArray($PageHeader);
            $spreadsheet->getActiveSheet()->getStyle('A4:Q5')->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->mergeCells('A4:A5');
            $spreadsheet->getActiveSheet()->mergeCells('B4:B5');
            $spreadsheet->getActiveSheet()->mergeCells('C4:C5');
            $spreadsheet->getActiveSheet()->mergeCells('D4:D5');
            $spreadsheet->getActiveSheet()->mergeCells('E4:E5');
            $spreadsheet->getActiveSheet()->mergeCells('F4:F5');
            $spreadsheet->getActiveSheet()->mergeCells('G4:G5');
            $spreadsheet->getActiveSheet()->mergeCells('H4:L4');
            $spreadsheet->getActiveSheet()->mergeCells('M4:M5');
            $spreadsheet->getActiveSheet()->mergeCells('N4:Q4');

            /* BODY */
            $row = 6;
            $tmpCode = "";
            while($GetDataRST = odbc_fetch_array($GetDataQRY)) {
                if($tmpCode != $GetDataRST['CardCode']) {
                    $tmpCode = $GetDataRST['CardCode'];

                    $sheet->setCellValue('A'.$row,$GetDataRST['CardCode']." | ".conutf8($GetDataRST['CardName']));
                    $sheet->getStyle('A'.$row)->applyFromArray([ 'font' => [ 'bold' => true, ], 'alignment'=>['vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] ]);
                    $spreadsheet->getActiveSheet()->mergeCells("A".$row.":Q".$row);
                    $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(24,'px');
                    $row++;
                }
                $sheet->setCellValue('A'.$row, $GetDataRST['DocNum']);
                $sheet->setCellValue('B'.$row, $GetDataRST['NumAtCard']);
                $sheet->setCellValue('C'.$row, date("d/m/Y",strtotime($GetDataRST['DocDate'])));
                $sheet->setCellValue('D'.$row, date("d/m/Y",strtotime($GetDataRST['DocDueDate'])));
                $sheet->setCellValue('E'.$row, $GetDataRST['DocTotal']);
                $sheet->setCellValue('F'.$row, $GetDataRST['PaidToDate']);
                $sheet->setCellValue('G'.$row, $GetDataRST['NoPaid']);

                $B30D = NULL;
                $B60D = NULL;
                $B90D = NULL;
                $B120D = NULL;
                $A120D = NULL;
                if($GetDataRST['B30D'] != NULL) { $B30D =  $GetDataRST['B30D']; }
                if($GetDataRST['B60D'] != NULL) { $B60D =  $GetDataRST['B60D']; }
                if($GetDataRST['B90D'] != NULL) { $B90D =  $GetDataRST['B90D']; }
                if($GetDataRST['B120D'] != NULL) { $B120D =  $GetDataRST['B120D']; }
                if($GetDataRST['A120D'] != NULL) { $A120D =  $GetDataRST['A120D']; }

                $sheet->setCellValue('H'.$row, $B30D);
                $sheet->setCellValue('I'.$row, $B60D);
                $sheet->setCellValue('J'.$row, $B90D);
                $sheet->setCellValue('K'.$row, $B120D);
                $sheet->setCellValue('L'.$row, $A120D);
                $sheet->setCellValue('M'.$row, $GetDataRST['DueType']);
                if($GetDataRST['Fine'] == 0 || $GetDataRST['DocType'] != "OINV" || $GetDataRST['DueType'] <= 30) {
                    $FineRate = 0;
                    $Fine = NULL;
                    $FineSAL = NULL;
                    $FineSUP = NULL;
                    $FineMGR = NULL;
                } else {
                    if($GetDataRST['DueType'] >= 91 ) { $FineRate = 0.03; }
                    elseif($GetDataRST['DueType'] >= 61 && $GetDataRST['DueType'] <= 90) { $FineRate = 0.01; }
                    elseif($GetDataRST['DueType'] >= 31 && $GetDataRST['DueType'] <= 60) { $FineRate = 0.005; }
                    else { $FineRate = 0; }
                    $Fine = $GetDataRST['NoPaid']*$FineRate;
                    if($ukey == "B60") {
                        $FineSAL = NULL;
                        $FineSUP = $Fine*0.5;
                        $FineMGR = $Fine*0.5;
                    } else {
                        $FineSAL = $Fine*0.7;
                        $FineSUP = $Fine*0.2;
                        $FineMGR = $Fine*0.1;
                    }
                }

                $sheet->setCellValue('N'.$row, $Fine);
                $sheet->setCellValue('O'.$row, $FineSAL);
                $sheet->setCellValue('P'.$row, $FineSUP);
                $sheet->setCellValue('Q'.$row, $FineMGR);

                $sheet->getStyle("A".$row.":Q".$row)->applyFromArray([ 'alignment'=>['vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] ]);
                $sheet->getStyle("A".$row.":D".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER] ]);
                $sheet->getStyle("E".$row.":Q".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT] ]);
                $spreadsheet->getActiveSheet()->getStyle("I".$row.":L".$row)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                $spreadsheet->getActiveSheet()->getStyle("C".$row.":D".$row)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
                $spreadsheet->getActiveSheet()->getStyle("E".$row.":L".$row)->getNumberFormat()->setFormatCode("#,##0.00");
                $spreadsheet->getActiveSheet()->getStyle("M".$row)->getNumberFormat()->setFormatCode("#,##");
                $spreadsheet->getActiveSheet()->getStyle("N".$row.":Q".$row)->getNumberFormat()->setFormatCode("#,##0.00");
                $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(24,'px');
                $row++;
            }

            $sheet->setCellValue('A'.$row, "รวมทั้งหมด");
            $sheet->setCellValue('E'.$row, "=SUM(E6:E".$row.")");
            $sheet->setCellValue('F'.$row, "=SUM(F6:F".$row.")");
            $sheet->setCellValue('G'.$row, "=SUM(G6:G".$row.")");
            $sheet->setCellValue('H'.$row, "=SUM(H6:H".$row.")");
            $sheet->setCellValue('I'.$row, "=SUM(I6:I".$row.")");
            $sheet->setCellValue('J'.$row, "=SUM(J6:J".$row.")");
            $sheet->setCellValue('K'.$row, "=SUM(K6:K".$row.")");
            $sheet->setCellValue('L'.$row, "=SUM(L6:L".$row.")");
            $sheet->setCellValue('N'.$row, "=SUM(N6:N".$row.")");
            $sheet->setCellValue('O'.$row, "=SUM(O6:O".$row.")");
            $sheet->setCellValue('P'.$row, "=SUM(P6:P".$row.")");
            $sheet->setCellValue('Q'.$row, "=SUM(Q6:Q".$row.")");

            $spreadsheet->getActiveSheet()->mergeCells("A".$row.":D".$row);
            $sheet->getStyle("A".$row.":D".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER] ]);
            $sheet->getStyle("E".$row.":Q".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT] ]);
            $sheet->getStyle("A".$row.":Q".$row)->applyFromArray([ 'font' => [ 'bold' => true, ], 'alignment'=>['vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] ]);
            $spreadsheet->getActiveSheet()->getStyle("I".$row.":L".$row)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            $spreadsheet->getActiveSheet()->getStyle("E".$row.":L".$row)->getNumberFormat()->setFormatCode("#,##0.00");
            $spreadsheet->getActiveSheet()->getStyle("N".$row.":Q".$row)->getNumberFormat()->setFormatCode("#,##0.00");
            $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(24,'px');
            
            $writer = new Xlsx($spreadsheet);
            $FileName = "รายงานหนี้เกินกำหนด".$tetPITA." - ".$PrintName." - ".date("YmdHis").".xlsx";

            if($pta == TRUE){
                $writer->save("../../../../FileExport/CollectInvoicePTA/".$FileName);
                $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'CollectInvoicePTA', logFile = '$FileName', DateCreate = NOW()";
            }else{
                $writer->save("../../../../FileExport/CollectInvoice/".$FileName);
                $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'CollectInvoice', logFile = '$FileName', DateCreate = NOW()";
            }
            MySQLInsert($InsertSQL);

            $arrCol['FileName'] = $FileName;
            $arrCol['ExportStatus'] = "SUCCESS";
        } else {
            $arrCol['FileName'] = null;
            $arrCol['ExportStatus'] = "NOROW";
        }
    }
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);

?>
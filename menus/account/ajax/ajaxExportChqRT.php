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
    $GetDataSQL = 
        "SELECT
            T0.CHQ_ID, T0.DocNum, T0.CHQ_No, T0.CHQ_Amount, T0.CHQ_DateReturn, T0.CHQ_SaleReceive, 
            DATEDIFF(NOW(),T0.CHQ_DateReturn) AS 'DateDiff', T0.CardCode, IFNULL(T0.CardName,T5.CardName) AS 'CardName', 
            CONCAT(T3.uName,' ',T3.uLastName) AS 'SalesName', T4.DeptCode, CONCAT(T2.uName,' ',T2.uLastName) AS 'CreateName',
            CONCAT(T1.ReturnCode,' | ',T1.ReturnName) AS 'CauseReturn',
            IFNULL((SELECT SUM(P1.Amount) FROM chq_detail P1 WHERE P1.DocNum = T0.DocNum),0) AS 'Paid',
            IFNULL((SELECT P2.Remark FROM chq_remark P2 WHERE P2.DocNum = T0.DocNum AND Status = 1 ORDER BY P2.CreateDate DESC LIMIT 1),'') AS 'Remark'
        FROM chq_return T0
        LEFT JOIN chq_causereturn T1 ON T0.CauseReturn = T1.TransID
        LEFT JOIN users T2 ON T0.CreateUkey = T2.uKey
        LEFT JOIN users T3 ON T0.SaleUkey = T3.uKey
        LEFT JOIN positions T4 ON T3.LvCode = T4.LvCode
        LEFT JOIN OCRD T5 ON T0.CardCode = T5.CardCode
        WHERE T0.Status = 0
        ORDER BY T0.CHQ_DateReturn, T0.DocNum";
    $ChkRow = ChkRowDB($GetDataSQL);
    if($ChkRow > 0) {
        $GetDataQRY = MySQLSelectX($GetDataSQL);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getProperties()
            ->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setTitle("รายงานเช็คคืน บจ.คิงบางกอก อินเตอร์เทรด")
            ->setSubject("รายงานเช็คคืน บจ.คิงบางกอก อินเตอร์เทรด");
        $spreadsheet->getDefaultStyle()->getFont()->setSize(8);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(12); // Value x 6 = pixel in excel
        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(66);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(24);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(32);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(14);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(14);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(14);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(42.67);

        /* HEADER */
        $PageHeader =
        [
            'font' => [ 'bold' => true, ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
        ];

        /* Row 1-2 */
        $sheet->setCellValue('A1',"เลขที่\nเอกสาร");
        $sheet->setCellValue('B1',"วันที่\nเซลส์รับทราบ");
        $sheet->setCellValue('C1',"รหัสคู่ค้า");
        $sheet->setCellValue('D1',"ชื่อคู่ค้า");
        $sheet->setCellValue('E1',"วันที่\nเช็คเด้ง");
        $sheet->setCellValue('F1',"เกินกำหนด\n(วัน)");
        $sheet->setCellValue('G1',"พนักงานขาย");
        $sheet->setCellValue('H1',"สาเหตุ\nเช็คเด้ง");
        $sheet->setCellValue('I1',"เลขที่เช็ค");
        $sheet->setCellValue('J1',"จำนวนเงิน\n(บาท)");
        $sheet->setCellValue('K1',"ชำระมาแล้ว\n(บาท)");
        $sheet->setCellValue('L1',"ยอดคงเหลือ\n(บาท)");
        $sheet->setCellValue('M1',"ค่าปรับ (บาท)");
        $sheet->setCellValue('Q1',"หมายเหตุ");

        $sheet->setCellValue('M2',"ทั้งหมด");
        $sheet->setCellValue('N2',"ทั้งหมด");
        $sheet->setCellValue('O2',"ทั้งหมด");
        $sheet->setCellValue('P2',"ทั้งหมด");


        $spreadsheet->getActiveSheet()->mergeCells('A1:A2');
        $spreadsheet->getActiveSheet()->mergeCells('B1:B2');
        $spreadsheet->getActiveSheet()->mergeCells('C1:C2');
        $spreadsheet->getActiveSheet()->mergeCells('D1:D2');
        $spreadsheet->getActiveSheet()->mergeCells('E1:E2');
        $spreadsheet->getActiveSheet()->mergeCells('F1:F2');
        $spreadsheet->getActiveSheet()->mergeCells('G1:G2');
        $spreadsheet->getActiveSheet()->mergeCells('H1:H2');
        $spreadsheet->getActiveSheet()->mergeCells('I1:I2');
        $spreadsheet->getActiveSheet()->mergeCells('J1:J2');
        $spreadsheet->getActiveSheet()->mergeCells('K1:K2');
        $spreadsheet->getActiveSheet()->mergeCells('L1:L2');
        $spreadsheet->getActiveSheet()->mergeCells('M1:P1');
        $spreadsheet->getActiveSheet()->mergeCells('Q1:Q2');
        $sheet->getStyle('A1:Q2')->applyFromArray($PageHeader);
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(24,'px');
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(24,'px');

        /* BODY */
        $row = 3;
        while($GetDataRST = mysqli_fetch_array($GetDataQRY)) {
            $DateDiff = intval($GetDataRST['DateDiff']);
            if($DateDiff > 90) {
				$FineRate = 0.03;
			} elseif($DateDiff > 60) {
				$FineRate = 0.01;
			} elseif($DateDiff > 30) {
				$FineRate = 0.005;
			} else {
				$FineRate = 0;
			}

            $sheet->setCellValue('A'.$row, $GetDataRST['DocNum']);
            $sheet->setCellValue('B'.$row, date("d/m/Y",strtotime($GetDataRST['CHQ_SaleReceive'])));
            $sheet->setCellValue('C'.$row, $GetDataRST['CardCode']);
            $sheet->setCellValue('D'.$row, $GetDataRST['CardName']);
            $sheet->setCellValue('E'.$row, date("d/m/Y",strtotime($GetDataRST['CHQ_DateReturn'])));
            $sheet->setCellValue('F'.$row, $GetDataRST['DateDiff']);
            $sheet->setCellValue('G'.$row, $GetDataRST['SalesName']);
            $sheet->setCellValue('H'.$row, $GetDataRST['CauseReturn']);
            $sheet->setCellValue('I'.$row, $GetDataRST['CHQ_No']);
            $sheet->setCellValue('J'.$row, $GetDataRST['CHQ_Amount']);
            $sheet->setCellValue('K'.$row, $GetDataRST['Paid']);
            $sheet->setCellValue('L'.$row, "=(J$row-K$row)");
            $sheet->setCellValue('M'.$row, "=(L$row*$FineRate)");
            $sheet->setCellValue('N'.$row, "=(M$row*0.7)");
            $sheet->setCellValue('O'.$row, "=(M$row*0.2)");
            $sheet->setCellValue('P'.$row, "=(M$row*0.1)");
            $sheet->setCellValue('Q'.$row, $GetDataRST['Remark']);

            $sheet->getStyle("A".$row.":Q".$row)->applyFromArray([ 'alignment'=>['vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] ]);
            $sheet->getStyle("A".$row.":C".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER] ]);
            $sheet->getStyle("E".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER] ]);
            $sheet->getStyle("I".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER] ]);
            $sheet->getStyle("F".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT] ]);
            $sheet->getStyle("J".$row.":P".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT] ]);
            $sheet->getStyle("L".$row)->applyFromArray([ 'font' => [ 'bold' => true, ] ]);
            $spreadsheet->getActiveSheet()->getStyle("L".$row.":P".$row)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);


            $spreadsheet->getActiveSheet()->getStyle("B".$row)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
            $spreadsheet->getActiveSheet()->getStyle("E".$row)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
            $spreadsheet->getActiveSheet()->getStyle("F".$row)->getNumberFormat()->setFormatCode("#,##");
            $spreadsheet->getActiveSheet()->getStyle("J".$row.":P".$row)->getNumberFormat()->setFormatCode("#,##0.00");
            $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(24,'px');
            $row++;
        }

        $sheet->setCellValue('A'.$row, "รวมทั้งหมด");
        $sheet->setCellValue('J'.$row, "=SUM(J3:J".$row.")");
        $sheet->setCellValue('K'.$row, "=SUM(K3:K".$row.")");
        $sheet->setCellValue('L'.$row, "=SUM(L3:L".$row.")");
        $sheet->setCellValue('M'.$row, "=SUM(M3:M".$row.")");
        $sheet->setCellValue('N'.$row, "=SUM(N3:N".$row.")");
        $sheet->setCellValue('O'.$row, "=SUM(O3:O".$row.")");
        $sheet->setCellValue('P'.$row, "=SUM(P3:P".$row.")");


        $spreadsheet->getActiveSheet()->mergeCells("A".$row.":I".$row);
        $sheet->getStyle("A".$row.":Q".$row)->applyFromArray([ 'alignment'=>['horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT] ]);
        $sheet->getStyle("A".$row.":Q".$row)->applyFromArray([ 'font' => [ 'bold' => true, ], 'alignment'=>['vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] ]);
        $spreadsheet->getActiveSheet()->getStyle("L".$row.":P".$row)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $spreadsheet->getActiveSheet()->getStyle("J".$row.":P".$row)->getNumberFormat()->setFormatCode("#,##0.00");
        $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(24,'px');

        $writer = new Xlsx($spreadsheet);
        $FileName = "รายงานเช็คคืน - ".date("YmdHis").".xlsx";
        $writer->save("../../../../FileExport/ChqReturn/".$FileName);
        $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'ChqReturn', logFile = '$FileName', DateCreate = NOW()";
        MySQLInsert($InsertSQL);
        $arrCol['FileName'] = $FileName;
        $arrCol['ExportStatus'] = "SUCCESS";
    } else {
        $arrCol['FileName'] = null;
        $arrCol['ExportStatus'] = "NOROW";
    }
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);

?>
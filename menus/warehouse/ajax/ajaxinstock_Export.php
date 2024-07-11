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
$output = "";
if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

if($_GET['a'] == 'Export') {
    if($_POST['status'] != "SALL") {
		if($_POST['status'] == "D") {
			$WhrStatus1 = " AND T0.U_ProductStatus LIKE 'D%'";
			$WhrStatus2 = " AND T1.U_ProductStatus LIKE 'D%'";
		} else {
			$WhrStatus1 = " AND T0.U_ProductStatus LIKE '".$_POST['status']."%'";
			$WhrStatus2 = " AND T1.U_ProductStatus LIKE '".$_POST['status']."%'";
		}
	}else{ $WhrStatus1 = ""; $WhrStatus2 = ""; }

	if($_POST['zero'] == "true") { 
		$WhrZero1 = ""; $WhrZero2 = ""; 
	}else{ $WhrZero1 = " AND (T1.[OnHand] > 0)"; $WhrZero2 = " AND (T0.[OnHand] > 0)"; }

	if($_POST['aging'] == "true") {
		$SQLPurDat = "ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = B0.ItemCode ORDER BY P0.DocEntry DESC),(CASE WHEN B1.LastPurDat = '2022-12-31' OR B1.LastPurDat IS NULL THEN ISNULL(B2.LastPurDat, B1.LastPurDat) ELSE B1.LastPurDat END))";
		$SQLAging  = "DATEDIFF(m,ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = B0.ItemCode ORDER BY P0.DocEntry DESC),(CASE WHEN B1.LastPurDat = '2022-12-31' OR B1.LastPurDat IS NULL THEN ISNULL(B2.LastPurDat, B1.LastPurDat) ELSE B1.LastPurDat END)),GETDATE())";
	}else{ $SQLPurDat = "''"; $SQLAging  = "'0'"; }

	if($_POST['whsgroup'] == "WALL" || $_POST['whsgroup'] == "WALL") {
		$ListSQL = "SELECT DISTINCT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
						B0.ItemCode, B1.CodeBars, B1.ItemName, ISNULL(B1.U_ProductStatus,'K') AS 'Status', B1.InvntryUom,
						SUM(B0.W100) AS 'W100', SUM(B0.W101) AS 'W101', SUM(B0.W102) AS 'W102', SUM(B0.W103) AS 'W103', SUM(B0.W104) AS 'W104',
						SUM(B0.W200) AS 'W200', SUM(B0.W300) AS 'W300', SUM(B0.W400) AS 'W400', SUM(B0.W500) AS 'W500', B1.OnOrder,
						(CASE WHEN B1.LastPurDat = '2022-12-31' THEN ISNULL(B2.LastPurPrc, B1.LastPurPrc) ELSE B1.LastPurPrc END *1.07) AS 'LastPurPrc', $SQLPurDat AS 'LastPurDat', $SQLAging AS 'Aging'
					FROM (
						SELECT DISTINCT
							A0.ItemCode,
							CASE WHEN A0.WhsGroup IN ('W100') THEN SUM(A0.OnHand) ELSE 0 END AS 'W100',
							CASE WHEN A0.WhsGroup IN ('W101') THEN SUM(A0.OnHand) ELSE 0 END AS 'W101',
							CASE WHEN A0.WhsGroup IN ('W102') THEN SUM(A0.OnHand) ELSE 0 END AS 'W102',
							CASE WHEN A0.WhsGroup IN ('W103') THEN SUM(A0.OnHand) ELSE 0 END AS 'W103',
							CASE WHEN A0.WhsGroup IN ('W104') THEN SUM(A0.OnHand) ELSE 0 END AS 'W104',
							CASE WHEN A0.WhsGroup IN ('W200') THEN SUM(A0.OnHand) ELSE 0 END AS 'W200',
							CASE WHEN A0.WhsGroup IN ('W300') THEN SUM(A0.OnHand) ELSE 0 END AS 'W300',
							CASE WHEN A0.WhsGroup IN ('W400') THEN SUM(A0.OnHand) ELSE 0 END AS 'W400',
							CASE WHEN A0.WhsGroup IN ('W500') THEN SUM(A0.OnHand) ELSE 0 END AS 'W500'
						FROM (
							SELECT
								T0.ItemCode, T1.WhsCode, T2.Location,
								CASE
									WHEN T1.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') THEN 'W100'
									WHEN T1.WhsCode IN ('MT') THEN 'W101'
									WHEN T1.WhsCode IN ('MT2') THEN 'W102'
									WHEN T1.WhsCode IN ('TT-C') THEN 'W103'
									WHEN T1.WhsCode IN ('OUL') THEN 'W104'
									WHEN T1.WhsCode IN ('KB1','KB1.1') THEN 'W200'
									WHEN T2.Location IN (2) THEN 'W300'
									WHEN T2.Location IN (6,7,9) THEN 'W400'
								ELSE 'W500' END AS 'WhsGroup',
								T1.OnHand
							FROM OITM T0
							LEFT JOIN OITW T1 ON T0.ItemCode = T1.ItemCode
							LEFT JOIN OWHS T2 ON T1.WhsCode = T2.WhsCode
							WHERE (T0.InvntItem != 'N' AND T0.ItemCode != '00-000-003') ".$WhrZero1.$WhrStatus1."
						) A0 
						GROUP BY A0.ItemCode, A0.WhsGroup
					) B0 
					LEFT JOIN OITM B1 ON B0.ItemCode = B1.ItemCode 
                    LEFT JOIN KBI_DB2022.dbo.OITM B2 ON B0.ItemCode = B2.ItemCode
					GROUP BY B0.ItemCode, B1.CodeBars, B1.ItemName, B1.U_ProductStatus, B1.LastPurPrc, B2.LastPurPrc, B1.LastPurDat, B2.LastPurDat, B1.InvntryUom, B1.OnOrder
					ORDER BY B0.ItemCode";
		$ListQRY = SAPSelect($ListSQL);
		// echo $ListSQL;
		$QuotaSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
						B0.ItemCode,
						SUM(B0.W101) AS 'W101', SUM(B0.W102) AS 'W102', SUM(B0.W103) AS 'W103', SUM(B0.W104) AS 'W104', SUM(B0.W105) AS 'W105'
					FROM (
						SELECT
							A0.ItemCode,
							CASE WHEN A0.WhsGroup IN ('W101') THEN A0.OnHand ELSE 0 END AS 'W101',
							CASE WHEN A0.WhsGroup IN ('W102') THEN A0.OnHand ELSE 0 END AS 'W102',
							CASE WHEN A0.WhsGroup IN ('W103') THEN A0.OnHand ELSE 0 END AS 'W103',
							CASE WHEN A0.WhsGroup IN ('W104') THEN A0.OnHand ELSE 0 END AS 'W104',
							CASE WHEN A0.WhsGroup IN ('W105') THEN A0.OnHand ELSE 0 END AS 'W105'
						FROM (
							SELECT
								T0.ItemCode, T0.CH AS 'WhsCode',
								CASE
									WHEN T0.CH IN ('MT1') THEN 'W101'
									WHEN T0.CH IN ('MT2') THEN 'W102'
									WHEN T0.CH IN ('TTC') THEN 'W103'
									WHEN T0.CH IN ('OUL') THEN 'W104'
									WHEN T0.CH IN ('ONL') THEN 'W105'
								ELSE 'W500' END AS 'WhsGroup',
								T0.OnHand
							FROM whsquota T0
						) A0
					) B0 GROUP BY B0.ItemCode ORDER BY B0.ItemCode";
		// echo $QuotaSQL;
		$QuotaQRY = MySQLSelectX($QuotaSQL);
		while($QuotaRST = mysqli_fetch_array($QuotaQRY)) {
			// $QuotaRST_ItemCode = str_replace("-", "", $QuotaRST['ItemCode']);
			${$QuotaRST['ItemCode']."_Q101"} = $QuotaRST['W101'];
			${$QuotaRST['ItemCode']."_Q102"} = $QuotaRST['W102'];
			${$QuotaRST['ItemCode']."_Q103"} = $QuotaRST['W103'];
			${$QuotaRST['ItemCode']."_Q104"} = $QuotaRST['W104'];
			${$QuotaRST['ItemCode']."_Q105"} = $QuotaRST['W105'];
			
			${"ItemCode_".$QuotaRST['ItemCode']} = $QuotaRST['ItemCode'];
		}

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getProperties()
            ->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setTitle("รายงานสินค้าคงคลัง บจ.คิงบางกอก อินเตอร์เทรด")
            ->setSubject("รายงานสินค้าคงคลัง บจ.คิงบางกอก อินเตอร์เทรด");
        $spreadsheet->getDefaultStyle()->getFont()->setSize(8);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(12); // Value x 6 = pixel in excel
        $spreadsheet->setActiveSheetIndex(0);

        $PageHeader = [
            'font' => [ 'bold' => true, 'size' => 9 ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
        ];
        $ColorSAP = [
            'font' => [ 'bold' => true, 'size' => 9 ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ],
            'fill' => [ 'fillType' => \PHPOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'ffd9edf7' ],
                      ]
        ];
        $ColorDF = [
            'font' => [ 'bold' => true, 'size' => 9 ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
        ];

        if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
            // HEADER //
            $sheet->setCellValue('A1',"รหัสสินค้า");
            $sheet->setCellValue('B1',"บาร์โค้ด");
            $sheet->setCellValue('C1',"ชื่อสินค้า");
            $sheet->setCellValue('D1',"สถานะ");
            $sheet->setCellValue('E1',"หน่วย");
            $sheet->setCellValue('F1',"มูลค่า (บาท)");
            $spreadsheet->getActiveSheet()->mergeCells('A1:A3');
            $spreadsheet->getActiveSheet()->mergeCells('B1:B3');
            $spreadsheet->getActiveSheet()->mergeCells('C1:C3');
            $spreadsheet->getActiveSheet()->mergeCells('D1:D3');
            $spreadsheet->getActiveSheet()->mergeCells('E1:E3');
            $spreadsheet->getActiveSheet()->mergeCells('F1:F3');
    
            $sheet->setCellValue('G1',"จำนวน (หน่วย)");
    
            $sheet->setCellValue('G2',"จำนวนคงคลังใน SAP"); 
            $sheet->setCellValue('L2',"จำนวนสินค้าที่สามารถเบิกได้"); 
            $sheet->setCellValue('G3',"พร้อมขาย KSY/KSM");
            $sheet->setCellValue('H3',"พร้อมขาย KIB");
            $sheet->setCellValue('I3',"พร้อมขาย SUPPLIER");
            $sheet->setCellValue('J3',"มือสอง");
            $sheet->setCellValue('K3',"อื่น ๆ");
            $sheet->setCellValue('L3',"กำลังสั่งซื้อ");
    
            $sheet->setCellValue('M2',"จำนวนสินค้าที่สามารถเบิกได้"); 
            $sheet->setCellValue('M3',"ส่วนกลาง"); 
            $sheet->setCellValue('N3',"โควต้า MT1"); 
            $sheet->setCellValue('O3',"โควต้า MT2"); 
            $sheet->setCellValue('P3',"โควต้า TT"); 
            $sheet->setCellValue('Q3',"โควต้า หน้าร้าน"); 
            $sheet->setCellValue('R3',"โควต้า ออนไลน์"); 
            $spreadsheet->getActiveSheet()->mergeCells('G1:R1');
            $spreadsheet->getActiveSheet()->mergeCells('G2:L2');
            $spreadsheet->getActiveSheet()->mergeCells('M2:R2');
    
            $sheet->setCellValue('S1',"AGING (เดือน)"); 
            $sheet->setCellValue('T1',"มูลค่ารวม (บาท)"); 
            $spreadsheet->getActiveSheet()->mergeCells('S1:S3');
            $spreadsheet->getActiveSheet()->mergeCells('T1:T3');

            $sheet->getStyle('A1:T1')->applyFromArray($PageHeader);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13.5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(34);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
            $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(13.4);
            $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(16.8);

            $sheet->getStyle('G2')->applyFromArray($ColorSAP);
            $sheet->getStyle('G3:L3')->applyFromArray($ColorSAP);
            $sheet->getStyle('M2')->applyFromArray($ColorDF);
            $sheet->getStyle('M3:R3')->applyFromArray($ColorDF);
            $Head = ['G','H','I','J','K','L','M','N','O','P','Q','R'];
            for($i = 0; $i < count($Head); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($Head[$i])->setWidth(18.2);
            }

            // BODY //
            $Row = 3;
            while($ListRST = odbc_fetch_array($ListQRY)) {
                ++$Row;
                $WALL = ($ListRST['W100'])+($ListRST['W101'])+($ListRST['W102'])+($ListRST['W103'])+($ListRST['W104'])+
                        ($ListRST['W200'])+($ListRST['W300'])+($ListRST['W400'])+($ListRST['W500']);
                if(isset(${"ItemCode_".$ListRST['ItemCode']})){
                    $QuotaW100 = ($ListRST['W100'] - (${$ListRST['ItemCode']."_Q101"}+${$ListRST['ItemCode']."_Q102"}+${$ListRST['ItemCode']."_Q103"}+${$ListRST['ItemCode']."_Q104"}+${$ListRST['ItemCode']."_Q105"}));
                    if(${$ListRST['ItemCode']."_Q101"} > 0) {
                        $QuotaW101 = ${$ListRST['ItemCode']."_Q101"};
                    }else{
                        $QuotaW101 = $ListRST['W101'];
                    }
                    if(${$ListRST['ItemCode']."_Q102"} > 0) {
                        $QuotaW102 = ${$ListRST['ItemCode']."_Q102"};
                    }else{
                        $QuotaW102 = $ListRST['W102'];
                    }
                    $QuotaW103 = ${$ListRST['ItemCode']."_Q103"};
                    $QuotaW104 = ${$ListRST['ItemCode']."_Q104"};
                    $QuotaW105 = ${$ListRST['ItemCode']."_Q105"};
                }else{
                    $QuotaW100 = $ListRST['W100'];
                    $QuotaW101 = 0;
                    $QuotaW102 = 0;
                    $QuotaW103 = 0;
                    $QuotaW104 = 0;
                    $QuotaW105 = 0;
                }
                $LastPurPrc = floatval($ListRST['LastPurPrc']);
                $W100 = intval($ListRST['W100']);
                $W200 = intval($ListRST['W200']);
                $W300 = intval($ListRST['W300']);
                $W400 = intval($ListRST['W400']);
                $W500 = intval($ListRST['W500'])+intval($ListRST['W101'])+intval($ListRST['W102'])+intval($ListRST['W103'])+intval($ListRST['W104']);
                $QuotaW100 = intval($QuotaW100);
                $QuotaW101 = intval($QuotaW101);
                $QuotaW102 = intval($QuotaW102);
                $QuotaW103 = intval($QuotaW103);
                $QuotaW104 = intval($QuotaW104);
                $QuotaW105 = intval($QuotaW105);
                $Aging = intval($ListRST['Aging']);

                $sheet->setCellValue('A'.$Row,$ListRST['ItemCode']); 
                $sheet->setCellValue('B'.$Row,$ListRST['CodeBars']); 
                $sheet->setCellValue('C'.$Row,conutf8($ListRST['ItemName'])); 
                $sheet->setCellValue('D'.$Row,$ListRST['Status']); 
                $sheet->setCellValue('E'.$Row,conutf8($ListRST['InvntryUom'])); 
                if($LastPurPrc != 0) { 
                    $sheet->setCellValue('F'.$Row,$LastPurPrc); 
                    $spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
                }else{ 
                    $sheet->setCellValue('F'.$Row,"-"); 
                }
                if($W100 != 0) {
                    $sheet->setCellValue('G'.$Row,$W100); 
                    $spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('G'.$Row,'-'); 
                }
                if($W200 != 0) {
                    $sheet->setCellValue('H'.$Row,$W200); 
                    $spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('H'.$Row,'-'); 
                }
                if($W300 != 0) {
                    $sheet->setCellValue('I'.$Row,$W300); 
                    $spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('I'.$Row,'-'); 
                }
                if($W400 != 0) {
                    $sheet->setCellValue('J'.$Row,$W400); 
                    $spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('J'.$Row,'-'); 
                }
                if($W500 != 0) {
                    $sheet->setCellValue('K'.$Row,$W500); 
                    $spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('K'.$Row,'-'); 
                }
                if($ListRST['OnOrder'] != 0) {
                    $sheet->setCellValue('L'.$Row,$ListRST['OnOrder']); 
                    $spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{ 
                    $sheet->setCellValue('L'.$Row,'-'); 
                }
                if($QuotaW100 != 0) {
                    $sheet->setCellValue('M'.$Row,$QuotaW100); 
                    $spreadsheet->getActiveSheet()->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{ 
                    $sheet->setCellValue('M'.$Row,'-'); 
                }
                if($QuotaW101 != 0) {
                    $sheet->setCellValue('N'.$Row,$QuotaW101); 
                    $spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('N'.$Row,'-'); 
                }
                if($QuotaW102 != 0) {
                    $sheet->setCellValue('O'.$Row,$QuotaW102); 
                    $spreadsheet->getActiveSheet()->getStyle('O'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('O'.$Row,'-'); 
                }
                if($QuotaW103 != 0) {
                    $sheet->setCellValue('P'.$Row,$QuotaW103); 
                    $spreadsheet->getActiveSheet()->getStyle('P'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('P'.$Row,'-'); 
                }
                if($QuotaW104 != 0) {
                    $sheet->setCellValue('Q'.$Row,$QuotaW104); 
                    $spreadsheet->getActiveSheet()->getStyle('Q'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('Q'.$Row,'-'); 
                }
                if($QuotaW105 != 0) {
                    $sheet->setCellValue('R'.$Row,$QuotaW105); 
                    $spreadsheet->getActiveSheet()->getStyle('R'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('R'.$Row,'-'); 
                }
                if($Aging != 0) {
                    $sheet->setCellValue('S'.$Row,$Aging);
                }else{
                    $sheet->setCellValue('S'.$Row,'-'); 
                }
                if(($WALL*$LastPurPrc) != 0) {
                    $sheet->setCellValue('T'.$Row,$WALL*$LastPurPrc);
                    $spreadsheet->getActiveSheet()->getStyle('T'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
                }else{
                    $sheet->setCellValue('T'.$Row,'-');
                }

                $sheet->getStyle('A'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('B'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('D'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('E'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('F'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                for($i = 0; $i < count($Head); $i++) {
                    $sheet->getStyle($Head[$i].$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                }
                $sheet->getStyle('S'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('T'.$Row)->applyFromArray([ 'font' => [ 'bold' => true ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
            }
        }else{
            // HEADER //
            $sheet->setCellValue('A1',"รหัสสินค้า");
            $sheet->setCellValue('B1',"บาร์โค้ด");
            $sheet->setCellValue('C1',"ชื่อสินค้า");
            $sheet->setCellValue('D1',"สถานะ");
            $sheet->setCellValue('E1',"หน่วย");
            $spreadsheet->getActiveSheet()->mergeCells('A1:A3');
            $spreadsheet->getActiveSheet()->mergeCells('B1:B3');
            $spreadsheet->getActiveSheet()->mergeCells('C1:C3');
            $spreadsheet->getActiveSheet()->mergeCells('D1:D3');
            $spreadsheet->getActiveSheet()->mergeCells('E1:E3');
    
            $sheet->setCellValue('F1',"จำนวน (หน่วย)");
    
            $sheet->setCellValue('F2',"จำนวนคงคลังใน SAP"); 
            $sheet->setCellValue('F3',"พร้อมขาย KSY/KSM");
            $sheet->setCellValue('G3',"พร้อมขาย KIB");
            $sheet->setCellValue('H3',"พร้อมขาย SUPPLIER");
            $sheet->setCellValue('I3',"มือสอง");
            $sheet->setCellValue('J3',"อื่น ๆ");
            $sheet->setCellValue('K3',"กำลังสั่งซื้อ");
    
            $sheet->setCellValue('L2',"จำนวนสินค้าที่สามารถเบิกได้"); 
            $sheet->setCellValue('L3',"ส่วนกลาง"); 
            $sheet->setCellValue('M3',"โควต้า MT1"); 
            $sheet->setCellValue('N3',"โควต้า MT2"); 
            $sheet->setCellValue('O3',"โควต้า TT"); 
            $sheet->setCellValue('P3',"โควต้า หน้าร้าน"); 
            $sheet->setCellValue('Q3',"โควต้า ออนไลน์"); 
            $spreadsheet->getActiveSheet()->mergeCells('F1:Q1');
            $spreadsheet->getActiveSheet()->mergeCells('F2:K2');
            $spreadsheet->getActiveSheet()->mergeCells('L2:Q2');
    
            $sheet->setCellValue('R1',"AGING (เดือน)"); 
            $spreadsheet->getActiveSheet()->mergeCells('R1:R3');

            $sheet->getStyle('A1:R1')->applyFromArray($PageHeader);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13.5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(34);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(13.4);
            $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(16.8);

            $sheet->getStyle('F2')->applyFromArray($ColorSAP);
            $sheet->getStyle('F3:K3')->applyFromArray($ColorSAP);
            $sheet->getStyle('L2')->applyFromArray($ColorDF);
            $sheet->getStyle('L3:Q3')->applyFromArray($ColorDF);
            $Head = ['F','G','H','I','J','K','L','M','N','O','P','Q'];
            for($i = 0; $i < count($Head); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($Head[$i])->setWidth(18.2);
            }

            // BODY //
            $Row = 3;
            while($ListRST = odbc_fetch_array($ListQRY)) {
                ++$Row;
                $WALL = ($ListRST['W100'])+($ListRST['W101'])+($ListRST['W102'])+($ListRST['W103'])+($ListRST['W104'])+
                        ($ListRST['W200'])+($ListRST['W300'])+($ListRST['W400'])+($ListRST['W500']);
                if(isset(${"ItemCode_".$ListRST['ItemCode']})){
                    $QuotaW100 = ($ListRST['W100'] - (${$ListRST['ItemCode']."_Q101"}+${$ListRST['ItemCode']."_Q102"}+${$ListRST['ItemCode']."_Q103"}+${$ListRST['ItemCode']."_Q104"}+${$ListRST['ItemCode']."_Q105"}));
                    if(${$ListRST['ItemCode']."_Q101"} > 0) {
                        $QuotaW101 = ${$ListRST['ItemCode']."_Q101"};
                    }else{
                        $QuotaW101 = $ListRST['W101'];
                    }
                    if(${$ListRST['ItemCode']."_Q102"} > 0) {
                        $QuotaW102 = ${$ListRST['ItemCode']."_Q102"};
                    }else{
                        $QuotaW102 = $ListRST['W102'];
                    }
                    $QuotaW103 = ${$ListRST['ItemCode']."_Q103"};
                    $QuotaW104 = ${$ListRST['ItemCode']."_Q104"};
                    $QuotaW105 = ${$ListRST['ItemCode']."_Q105"};
                }else{
                    $QuotaW100 = $ListRST['W100'];
                    $QuotaW101 = 0;
                    $QuotaW102 = 0;
                    $QuotaW103 = 0;
                    $QuotaW104 = 0;
                    $QuotaW105 = 0;
                }
                $LastPurPrc = floatval($ListRST['LastPurPrc']);
                $W100 = intval($ListRST['W100']);
                $W200 = intval($ListRST['W200']);
                $W300 = intval($ListRST['W300']);
                $W400 = intval($ListRST['W400']);
                $W500 = intval($ListRST['W500']);
                $QuotaW100 = intval($QuotaW100);
                $QuotaW101 = intval($QuotaW101);
                $QuotaW102 = intval($QuotaW102);
                $QuotaW103 = intval($QuotaW103);
                $QuotaW104 = intval($QuotaW104);
                $QuotaW105 = intval($QuotaW105);
                $Aging = intval($ListRST['Aging']);

                $sheet->setCellValue('A'.$Row,$ListRST['ItemCode']); 
                $sheet->setCellValue('B'.$Row,$ListRST['CodeBars']); 
                $sheet->setCellValue('C'.$Row,conutf8($ListRST['ItemName'])); 
                $sheet->setCellValue('D'.$Row,$ListRST['Status']); 
                $sheet->setCellValue('E'.$Row,conutf8($ListRST['InvntryUom'])); 
                if($W100 != 0) {
                    $sheet->setCellValue('F'.$Row,$W100); 
                    $spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('F'.$Row,'-'); 
                }
                if($W200 != 0) {
                    $sheet->setCellValue('G'.$Row,$W200); 
                    $spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('G'.$Row,'-'); 
                }
                if($W300 != 0) {
                    $sheet->setCellValue('H'.$Row,$W300); 
                    $spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('H'.$Row,'-'); 
                }
                if($W400 != 0) {
                    $sheet->setCellValue('I'.$Row,$W400); 
                    $spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('I'.$Row,'-'); 
                }
                if($W500 != 0) {
                    $sheet->setCellValue('J'.$Row,$W500); 
                    $spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('J'.$Row,'-'); 
                }
                if($ListRST['OnOrder'] != 0) {
                    $sheet->setCellValue('K'.$Row,$ListRST['OnOrder']); 
                    $spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{ 
                    $sheet->setCellValue('K'.$Row,'-'); 
                }
                if($QuotaW100 != 0) {
                    $sheet->setCellValue('L'.$Row,$QuotaW100); 
                    $spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{ 
                    $sheet->setCellValue('L'.$Row,'-'); 
                }
                if($QuotaW101 != 0) {
                    $sheet->setCellValue('M'.$Row,$QuotaW101); 
                    $spreadsheet->getActiveSheet()->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('M'.$Row,'-'); 
                }
                if($QuotaW102 != 0) {
                    $sheet->setCellValue('N'.$Row,$QuotaW102); 
                    $spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('N'.$Row,'-'); 
                }
                if($QuotaW103 != 0) {
                    $sheet->setCellValue('O'.$Row,$QuotaW103); 
                    $spreadsheet->getActiveSheet()->getStyle('O'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('O'.$Row,'-'); 
                }
                if($QuotaW104 != 0) {
                    $sheet->setCellValue('P'.$Row,$QuotaW104); 
                    $spreadsheet->getActiveSheet()->getStyle('P'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('P'.$Row,'-'); 
                }
                if($QuotaW105 != 0) {
                    $sheet->setCellValue('Q'.$Row,$QuotaW105); 
                    $spreadsheet->getActiveSheet()->getStyle('Q'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('Q'.$Row,'-'); 
                }
                if($Aging != 0) {
                    $sheet->setCellValue('R'.$Row,$Aging);
                }else{
                    $sheet->setCellValue('R'.$Row,'-'); 
                }

                $sheet->getStyle('A'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('B'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('D'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('E'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                for($i = 0; $i < count($Head); $i++) {
                    $sheet->getStyle($Head[$i].$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                }
                $sheet->getStyle('R'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
            }
        }
    }elseif($_POST['whsgroup'] == "CMT1" || $_POST['whsgroup'] == "CMT2" || $_POST['whsgroup'] == "CTT2" || $_POST['whsgroup'] == "COUL" || $_POST['whsgroup'] == "CONL"){
        switch($_POST['whsgroup']) {
			case "CMT1": $CH = "MT1"; break;
			case "CMT2": $CH = "MT2"; break;
			case "CTT2": $CH = "TTC"; break;
			case "COUL": $CH = "OUL"; break;
			case "CONL": $CH = "ONL"; break;
		}

		$QtaListQRY = MySQLSelectX("SELECT T0.ItemCode FROM whsquota T0 WHERE T0.CH = '".$CH."'");
		$WhereQuota = "";
		while($QtaListRST = mysqli_fetch_array($QtaListQRY)) {
			$WhereQuota .= "'".$QtaListRST['ItemCode']."',";
		}
		$WhereQuota = substr($WhereQuota,0,-1);

		$ListSQL = "SELECT DISTINCT 
						B0.ItemCode, B1.CodeBars, B1.ItemName, ISNULL(B1.U_ProductStatus,'K') AS 'Status', B1.InvntryUom,
						SUM(B0.W100) AS 'W100', SUM(B0.W101) AS 'W101', SUM(B0.W102) AS 'W102', SUM(B0.W103) AS 'W103', SUM(B0.W104) AS 'W104',
						SUM(B0.W200) AS 'W200', SUM(B0.W300) AS 'W300', SUM(B0.W400) AS 'W400', SUM(B0.W500) AS 'W500', B1.OnOrder,
						(CASE WHEN B1.LastPurDat = '2022-12-31' THEN ISNULL(B2.LastPurPrc, B1.LastPurPrc) ELSE B1.LastPurPrc END *1.07) AS 'LastPurPrc', $SQLPurDat AS 'LastPurDat', $SQLAging AS 'Aging'
					FROM (
						SELECT DISTINCT
							A0.ItemCode,
							CASE WHEN A0.WhsGroup IN ('W100') THEN SUM(A0.OnHand) ELSE 0 END AS 'W100',
							CASE WHEN A0.WhsGroup IN ('W101') THEN SUM(A0.OnHand) ELSE 0 END AS 'W101',
							CASE WHEN A0.WhsGroup IN ('W102') THEN SUM(A0.OnHand) ELSE 0 END AS 'W102',
							CASE WHEN A0.WhsGroup IN ('W103') THEN SUM(A0.OnHand) ELSE 0 END AS 'W103',
							CASE WHEN A0.WhsGroup IN ('W104') THEN SUM(A0.OnHand) ELSE 0 END AS 'W104',
							CASE WHEN A0.WhsGroup IN ('W200') THEN SUM(A0.OnHand) ELSE 0 END AS 'W200',
							CASE WHEN A0.WhsGroup IN ('W300') THEN SUM(A0.OnHand) ELSE 0 END AS 'W300',
							CASE WHEN A0.WhsGroup IN ('W400') THEN SUM(A0.OnHand) ELSE 0 END AS 'W400',
							CASE WHEN A0.WhsGroup IN ('W500') THEN SUM(A0.OnHand) ELSE 0 END AS 'W500'
						FROM (
							SELECT
								T0.ItemCode, T1.WhsCode, T2.Location,
								CASE
									WHEN T1.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') THEN 'W100'
									WHEN T1.WhsCode IN ('MT') THEN 'W101'
									WHEN T1.WhsCode IN ('MT2') THEN 'W102'
									WHEN T1.WhsCode IN ('TT-C') THEN 'W103'
									WHEN T1.WhsCode IN ('OUL') THEN 'W104'
									WHEN T1.WhsCode IN ('KB1','KB1.1') THEN 'W200'
									WHEN T2.Location IN (2) THEN 'W300'
									WHEN T2.Location IN (6,7,9) THEN 'W400'
								ELSE 'W500' END AS 'WhsGroup',
								T1.OnHand
							FROM OITM T0
							LEFT JOIN OITW T1 ON T0.ItemCode = T1.ItemCode
							LEFT JOIN OWHS T2 ON T1.WhsCode = T2.WhsCode
							WHERE T0.ItemCode IN (".$WhereQuota.") $WhrStatus1
						) A0 
						GROUP BY A0.ItemCode, A0.WhsGroup
					) B0 
					LEFT JOIN OITM B1 ON B0.ItemCode = B1.ItemCode 
                    LEFT JOIN KBI_DB2022.dbo.OITM B2 ON B0.ItemCode = B2.ItemCode
					GROUP BY B0.ItemCode, B1.CodeBars, B1.ItemName, B1.U_ProductStatus, B1.LastPurPrc, B2.LastPurPrc, B1.LastPurDat, B2.LastPurDat, B1.InvntryUom, B1.OnOrder
					ORDER BY B0.ItemCode";
		$ListQRY = SAPSelect($ListSQL);

		$QuotaSQL = "SELECT 
						B0.ItemCode,
						SUM(B0.W101) AS 'W101', SUM(B0.W102) AS 'W102', SUM(B0.W103) AS 'W103', SUM(B0.W104) AS 'W104', SUM(B0.W105) AS 'W105'
					FROM (
						SELECT
							A0.ItemCode,
							CASE WHEN A0.WhsGroup IN ('W101') THEN A0.OnHand ELSE 0 END AS 'W101',
							CASE WHEN A0.WhsGroup IN ('W102') THEN A0.OnHand ELSE 0 END AS 'W102',
							CASE WHEN A0.WhsGroup IN ('W103') THEN A0.OnHand ELSE 0 END AS 'W103',
							CASE WHEN A0.WhsGroup IN ('W104') THEN A0.OnHand ELSE 0 END AS 'W104',
							CASE WHEN A0.WhsGroup IN ('W105') THEN A0.OnHand ELSE 0 END AS 'W105'
						FROM (
							SELECT
								T0.ItemCode, T0.CH AS 'WhsCode',
								CASE
									WHEN T0.CH IN ('MT1') THEN 'W101'
									WHEN T0.CH IN ('MT2') THEN 'W102'
									WHEN T0.CH IN ('TTC') THEN 'W103'
									WHEN T0.CH IN ('OUL') THEN 'W104'
									WHEN T0.CH IN ('ONL') THEN 'W105'
								ELSE 'W500' END AS 'WhsGroup',
								T0.OnHand
							FROM whsquota T0
						) A0
					) B0 GROUP BY B0.ItemCode ORDER BY B0.ItemCode";
		$QuotaQRY = MySQLSelectX($QuotaSQL);
		while($QuotaRST = mysqli_fetch_array($QuotaQRY)) {
			${$QuotaRST['ItemCode']."_Q101"} = $QuotaRST['W101'];
			${$QuotaRST['ItemCode']."_Q102"} = $QuotaRST['W102'];
			${$QuotaRST['ItemCode']."_Q103"} = $QuotaRST['W103'];
			${$QuotaRST['ItemCode']."_Q104"} = $QuotaRST['W104'];
			${$QuotaRST['ItemCode']."_Q105"} = $QuotaRST['W105'];

			${"ItemCode_".$QuotaRST['ItemCode']} = $QuotaRST['ItemCode'];
		}

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getProperties()
            ->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setTitle("รายงานสินค้าคงคลัง บจ.คิงบางกอก อินเตอร์เทรด")
            ->setSubject("รายงานสินค้าคงคลัง บจ.คิงบางกอก อินเตอร์เทรด");
        $spreadsheet->getDefaultStyle()->getFont()->setSize(8);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(12); // Value x 6 = pixel in excel
        $spreadsheet->setActiveSheetIndex(0);

        $PageHeader = [
            'font' => [ 'bold' => true, 'size' => 9 ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
        ];
        $ColorSAP = [
            'font' => [ 'bold' => true, 'size' => 9 ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ],
            'fill' => [ 'fillType' => \PHPOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'ffd9edf7' ],
                      ]
        ];
        $ColorDF = [
            'font' => [ 'bold' => true, 'size' => 9 ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
        ];

        if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
            // HEADER //
            $sheet->setCellValue('A1',"รหัสสินค้า");
            $sheet->setCellValue('B1',"บาร์โค้ด");
            $sheet->setCellValue('C1',"ชื่อสินค้า");
            $sheet->setCellValue('D1',"สถานะ");
            $sheet->setCellValue('E1',"หน่วย");
            $sheet->setCellValue('F1',"มูลค่า (บาท)");
            $spreadsheet->getActiveSheet()->mergeCells('A1:A3');
            $spreadsheet->getActiveSheet()->mergeCells('B1:B3');
            $spreadsheet->getActiveSheet()->mergeCells('C1:C3');
            $spreadsheet->getActiveSheet()->mergeCells('D1:D3');
            $spreadsheet->getActiveSheet()->mergeCells('E1:E3');
            $spreadsheet->getActiveSheet()->mergeCells('F1:F3');
    
            $sheet->setCellValue('G1',"จำนวน (หน่วย)");
    
            $sheet->setCellValue('G2',"จำนวนคงคลังใน SAP"); 
            $sheet->setCellValue('L2',"จำนวนสินค้าที่สามารถเบิกได้"); 
            $sheet->setCellValue('G3',"พร้อมขาย KSY/KSM");
            $sheet->setCellValue('H3',"พร้อมขาย KIB");
            $sheet->setCellValue('I3',"พร้อมขาย SUPPLIER");
            $sheet->setCellValue('J3',"มือสอง");
            $sheet->setCellValue('K3',"อื่น ๆ");
            $sheet->setCellValue('L3',"กำลังสั่งซื้อ");
    
            $sheet->setCellValue('M2',"จำนวนสินค้าที่สามารถเบิกได้"); 
            $sheet->setCellValue('M3',"ส่วนกลาง"); 
            $sheet->setCellValue('N3',"โควต้า MT1"); 
            $sheet->setCellValue('O3',"โควต้า MT2"); 
            $sheet->setCellValue('P3',"โควต้า TT"); 
            $sheet->setCellValue('Q3',"โควต้า หน้าร้าน"); 
            $sheet->setCellValue('R3',"โควต้า ออนไลน์"); 
            $spreadsheet->getActiveSheet()->mergeCells('G1:R1');
            $spreadsheet->getActiveSheet()->mergeCells('G2:L2');
            $spreadsheet->getActiveSheet()->mergeCells('M2:R2');
    
            $sheet->setCellValue('S1',"AGING (เดือน)"); 
            $sheet->setCellValue('T1',"มูลค่ารวม (บาท)"); 
            $spreadsheet->getActiveSheet()->mergeCells('S1:S3');
            $spreadsheet->getActiveSheet()->mergeCells('T1:T3');

            $sheet->getStyle('A1:T1')->applyFromArray($PageHeader);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13.5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(34);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
            $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(13.4);
            $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(16.8);

            $sheet->getStyle('G2')->applyFromArray($ColorSAP);
            $sheet->getStyle('G3:L3')->applyFromArray($ColorSAP);
            $sheet->getStyle('M2')->applyFromArray($ColorDF);
            $sheet->getStyle('M3:R3')->applyFromArray($ColorDF);
            $Head = ['G','H','I','J','K','L','M','N','O','P','Q','R'];
            for($i = 0; $i < count($Head); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($Head[$i])->setWidth(18.2);
            }

            // BODY //
            $Row = 3;
            while($ListRST = odbc_fetch_array($ListQRY)) {
                ++$Row;
                $WALL = ($ListRST['W100'])+($ListRST['W101'])+($ListRST['W102'])+($ListRST['W103'])+($ListRST['W104'])+
                        ($ListRST['W200'])+($ListRST['W300'])+($ListRST['W400'])+($ListRST['W500']);
                if(isset(${"ItemCode_".$ListRST['ItemCode']})){
                    $QuotaW100 = ($ListRST['W100'] - (${$ListRST['ItemCode']."_Q101"}+${$ListRST['ItemCode']."_Q102"}+${$ListRST['ItemCode']."_Q103"}+${$ListRST['ItemCode']."_Q104"}+${$ListRST['ItemCode']."_Q105"}));
                    if(${$ListRST['ItemCode']."_Q101"} > 0) {
                        $QuotaW101 = ${$ListRST['ItemCode']."_Q101"};
                    }else{
                        $QuotaW101 = $ListRST['W101'];
                    }
                    if(${$ListRST['ItemCode']."_Q102"} > 0) {
                        $QuotaW102 = ${$ListRST['ItemCode']."_Q102"};
                    }else{
                        $QuotaW102 = $ListRST['W102'];
                    }
                    $QuotaW103 = ${$ListRST['ItemCode']."_Q103"};
                    $QuotaW104 = ${$ListRST['ItemCode']."_Q104"};
                    $QuotaW105 = ${$ListRST['ItemCode']."_Q105"};
                }else{
                    $QuotaW100 = $ListRST['W100'];
                    $QuotaW101 = 0;
                    $QuotaW102 = 0;
                    $QuotaW103 = 0;
                    $QuotaW104 = 0;
                    $QuotaW105 = 0;
                }
                $LastPurPrc = floatval($ListRST['LastPurPrc']);
                $W100 = intval($ListRST['W100']);
                $W200 = intval($ListRST['W200']);
                $W300 = intval($ListRST['W300']);
                $W400 = intval($ListRST['W400']);
                $W500 = intval($ListRST['W500'])+intval($ListRST['W101'])+intval($ListRST['W102'])+intval($ListRST['W103'])+intval($ListRST['W104']);
                $QuotaW100 = intval($QuotaW100);
                $QuotaW101 = intval($QuotaW101);
                $QuotaW102 = intval($QuotaW102);
                $QuotaW103 = intval($QuotaW103);
                $QuotaW104 = intval($QuotaW104);
                $QuotaW105 = intval($QuotaW105);
                $Aging = intval($ListRST['Aging']);

                $sheet->setCellValue('A'.$Row,$ListRST['ItemCode']); 
                $sheet->setCellValue('B'.$Row,$ListRST['CodeBars']); 
                $sheet->setCellValue('C'.$Row,conutf8($ListRST['ItemName'])); 
                $sheet->setCellValue('D'.$Row,$ListRST['Status']); 
                $sheet->setCellValue('E'.$Row,conutf8($ListRST['InvntryUom'])); 
                if($LastPurPrc != 0) { 
                    $sheet->setCellValue('F'.$Row,$LastPurPrc); 
                    $spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
                }else{ 
                    $sheet->setCellValue('F'.$Row,"-"); 
                }
                if($W100 != 0) {
                    $sheet->setCellValue('G'.$Row,$W100); 
                    $spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('G'.$Row,'-'); 
                }
                if($W200 != 0) {
                    $sheet->setCellValue('H'.$Row,$W200); 
                    $spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('H'.$Row,'-'); 
                }
                if($W300 != 0) {
                    $sheet->setCellValue('I'.$Row,$W300); 
                    $spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('I'.$Row,'-'); 
                }
                if($W400 != 0) {
                    $sheet->setCellValue('J'.$Row,$W400); 
                    $spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('J'.$Row,'-'); 
                }
                if($W500 != 0) {
                    $sheet->setCellValue('K'.$Row,$W500); 
                    $spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('K'.$Row,'-'); 
                }
                if($ListRST['OnOrder'] != 0) {
                    $sheet->setCellValue('L'.$Row,$ListRST['OnOrder']); 
                    $spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{ 
                    $sheet->setCellValue('L'.$Row,'-'); 
                }
                if($QuotaW100 != 0) {
                    $sheet->setCellValue('M'.$Row,$QuotaW100); 
                    $spreadsheet->getActiveSheet()->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{ 
                    $sheet->setCellValue('M'.$Row,'-'); 
                }
                if($QuotaW101 != 0) {
                    $sheet->setCellValue('N'.$Row,$QuotaW101); 
                    $spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('N'.$Row,'-'); 
                }
                if($QuotaW102 != 0) {
                    $sheet->setCellValue('O'.$Row,$QuotaW102); 
                    $spreadsheet->getActiveSheet()->getStyle('O'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('O'.$Row,'-'); 
                }
                if($QuotaW103 != 0) {
                    $sheet->setCellValue('P'.$Row,$QuotaW103); 
                    $spreadsheet->getActiveSheet()->getStyle('P'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('P'.$Row,'-'); 
                }
                if($QuotaW104 != 0) {
                    $sheet->setCellValue('Q'.$Row,$QuotaW104); 
                    $spreadsheet->getActiveSheet()->getStyle('Q'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('Q'.$Row,'-'); 
                }
                if($QuotaW105 != 0) {
                    $sheet->setCellValue('R'.$Row,$QuotaW105); 
                    $spreadsheet->getActiveSheet()->getStyle('R'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('R'.$Row,'-'); 
                }
                if($Aging != 0) {
                    $sheet->setCellValue('S'.$Row,$Aging);
                }else{
                    $sheet->setCellValue('S'.$Row,'-'); 
                }
                if(($WALL*$LastPurPrc) != 0) {
                    $sheet->setCellValue('T'.$Row,$WALL*$LastPurPrc);
                    $spreadsheet->getActiveSheet()->getStyle('T'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
                }else{
                    $sheet->setCellValue('T'.$Row,'-');
                }

                $sheet->getStyle('A'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('B'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('D'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('E'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('F'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                for($i = 0; $i < count($Head); $i++) {
                    $sheet->getStyle($Head[$i].$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                }
                $sheet->getStyle('S'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('T'.$Row)->applyFromArray([ 'font' => [ 'bold' => true ], 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
            }
        }else{
            // HEADER //
            $sheet->setCellValue('A1',"รหัสสินค้า");
            $sheet->setCellValue('B1',"บาร์โค้ด");
            $sheet->setCellValue('C1',"ชื่อสินค้า");
            $sheet->setCellValue('D1',"สถานะ");
            $sheet->setCellValue('E1',"หน่วย");
            $spreadsheet->getActiveSheet()->mergeCells('A1:A3');
            $spreadsheet->getActiveSheet()->mergeCells('B1:B3');
            $spreadsheet->getActiveSheet()->mergeCells('C1:C3');
            $spreadsheet->getActiveSheet()->mergeCells('D1:D3');
            $spreadsheet->getActiveSheet()->mergeCells('E1:E3');
    
            $sheet->setCellValue('F1',"จำนวน (หน่วย)");
    
            $sheet->setCellValue('F2',"จำนวนคงคลังใน SAP"); 
            $sheet->setCellValue('F3',"พร้อมขาย KSY/KSM");
            $sheet->setCellValue('G3',"พร้อมขาย KIB");
            $sheet->setCellValue('H3',"พร้อมขาย SUPPLIER");
            $sheet->setCellValue('I3',"มือสอง");
            $sheet->setCellValue('J3',"อื่น ๆ");
            $sheet->setCellValue('K3',"กำลังสั่งซื้อ");
    
            $sheet->setCellValue('L2',"จำนวนสินค้าที่สามารถเบิกได้"); 
            $sheet->setCellValue('L3',"ส่วนกลาง"); 
            $sheet->setCellValue('M3',"โควต้า MT1"); 
            $sheet->setCellValue('N3',"โควต้า MT2"); 
            $sheet->setCellValue('O3',"โควต้า TT"); 
            $sheet->setCellValue('P3',"โควต้า หน้าร้าน"); 
            $sheet->setCellValue('Q3',"โควต้า ออนไลน์"); 
            $spreadsheet->getActiveSheet()->mergeCells('F1:Q1');
            $spreadsheet->getActiveSheet()->mergeCells('F2:K2');
            $spreadsheet->getActiveSheet()->mergeCells('L2:Q2');
    
            $sheet->setCellValue('R1',"AGING (เดือน)"); 
            $spreadsheet->getActiveSheet()->mergeCells('R1:R3');

            $sheet->getStyle('A1:R1')->applyFromArray($PageHeader);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13.5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(34);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(13.4);

            $sheet->getStyle('F2')->applyFromArray($ColorSAP);
            $sheet->getStyle('F3:K3')->applyFromArray($ColorSAP);
            $sheet->getStyle('L2')->applyFromArray($ColorDF);
            $sheet->getStyle('L3:Q3')->applyFromArray($ColorDF);
            $Head = ['F','G','H','I','J','K','L','M','N','O','P','Q'];
            for($i = 0; $i < count($Head); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($Head[$i])->setWidth(18.2);
            }

            // BODY //
            $Row = 3;
            while($ListRST = odbc_fetch_array($ListQRY)) {
                ++$Row;
                $WALL = ($ListRST['W100'])+($ListRST['W101'])+($ListRST['W102'])+($ListRST['W103'])+($ListRST['W104'])+
                        ($ListRST['W200'])+($ListRST['W300'])+($ListRST['W400'])+($ListRST['W500']);
                if(isset(${"ItemCode_".$ListRST['ItemCode']})){
                    $QuotaW100 = ($ListRST['W100'] - (${$ListRST['ItemCode']."_Q101"}+${$ListRST['ItemCode']."_Q102"}+${$ListRST['ItemCode']."_Q103"}+${$ListRST['ItemCode']."_Q104"}+${$ListRST['ItemCode']."_Q105"}));
                    if(${$ListRST['ItemCode']."_Q101"} > 0) {
                        $QuotaW101 = ${$ListRST['ItemCode']."_Q101"};
                    }else{
                        $QuotaW101 = $ListRST['W101'];
                    }
                    if(${$ListRST['ItemCode']."_Q102"} > 0) {
                        $QuotaW102 = ${$ListRST['ItemCode']."_Q102"};
                    }else{
                        $QuotaW102 = $ListRST['W102'];
                    }
                    $QuotaW103 = ${$ListRST['ItemCode']."_Q103"};
                    $QuotaW104 = ${$ListRST['ItemCode']."_Q104"};
                    $QuotaW105 = ${$ListRST['ItemCode']."_Q105"};
                }else{
                    $QuotaW100 = $ListRST['W100'];
                    $QuotaW101 = 0;
                    $QuotaW102 = 0;
                    $QuotaW103 = 0;
                    $QuotaW104 = 0;
                    $QuotaW105 = 0;
                }
                $LastPurPrc = floatval($ListRST['LastPurPrc']);
                $W100 = intval($ListRST['W100']);
                $W200 = intval($ListRST['W200']);
                $W300 = intval($ListRST['W300']);
                $W400 = intval($ListRST['W400']);
                $W500 = intval($ListRST['W500']);
                $QuotaW100 = intval($QuotaW100);
                $QuotaW101 = intval($QuotaW101);
                $QuotaW102 = intval($QuotaW102);
                $QuotaW103 = intval($QuotaW103);
                $QuotaW104 = intval($QuotaW104);
                $QuotaW105 = intval($QuotaW105);
                $Aging = intval($ListRST['Aging']);

                $sheet->setCellValue('A'.$Row,$ListRST['ItemCode']); 
                $sheet->setCellValue('B'.$Row,$ListRST['CodeBars']); 
                $sheet->setCellValue('C'.$Row,conutf8($ListRST['ItemName'])); 
                $sheet->setCellValue('D'.$Row,$ListRST['Status']); 
                $sheet->setCellValue('E'.$Row,conutf8($ListRST['InvntryUom'])); 
                if($W100 != 0) {
                    $sheet->setCellValue('F'.$Row,$W100); 
                    $spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('F'.$Row,'-'); 
                }
                if($W200 != 0) {
                    $sheet->setCellValue('G'.$Row,$W200); 
                    $spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('G'.$Row,'-'); 
                }
                if($W300 != 0) {
                    $sheet->setCellValue('H'.$Row,$W300); 
                    $spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('H'.$Row,'-'); 
                }
                if($W400 != 0) {
                    $sheet->setCellValue('I'.$Row,$W400); 
                    $spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('I'.$Row,'-'); 
                }
                if($W500 != 0) {
                    $sheet->setCellValue('J'.$Row,$W500); 
                    $spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('J'.$Row,'-'); 
                }
                if($QuotaW100 != 0) {
                    $sheet->setCellValue('K'.$Row,$QuotaW100); 
                    $spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{ 
                    $sheet->setCellValue('K'.$Row,'-'); 
                }
                if($ListRST['OnOrder'] != 0) {
                    $sheet->setCellValue('L'.$Row,$ListRST['OnOrder']); 
                    $spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('L'.$Row,'-'); 
                }
                if($QuotaW101 != 0) {
                    $sheet->setCellValue('M'.$Row,$QuotaW101); 
                    $spreadsheet->getActiveSheet()->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('M'.$Row,'-'); 
                }
                if($QuotaW102 != 0) {
                    $sheet->setCellValue('N'.$Row,$QuotaW102); 
                    $spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('N'.$Row,'-'); 
                }
                if($QuotaW103 != 0) {
                    $sheet->setCellValue('O'.$Row,$QuotaW103); 
                    $spreadsheet->getActiveSheet()->getStyle('O'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('O'.$Row,'-'); 
                }
                if($QuotaW104 != 0) {
                    $sheet->setCellValue('P'.$Row,$QuotaW104); 
                    $spreadsheet->getActiveSheet()->getStyle('P'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('P'.$Row,'-'); 
                }
                if($QuotaW105 != 0) {
                    $sheet->setCellValue('Q'.$Row,$QuotaW105); 
                    $spreadsheet->getActiveSheet()->getStyle('Q'.$Row)->getNumberFormat()->setFormatCode("#,##0");
                }else{
                    $sheet->setCellValue('Q'.$Row,'-'); 
                }
                if($Aging != 0) {
                    $sheet->setCellValue('R'.$Row,$Aging);
                }else{
                    $sheet->setCellValue('R'.$Row,'-'); 
                }

                $sheet->getStyle('A'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('B'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('D'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                $sheet->getStyle('E'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                for($i = 0; $i < count($Head); $i++) {
                    $sheet->getStyle($Head[$i].$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
                }
                $sheet->getStyle('R'.$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ] ]);
            }
        }
    }elseif(($_POST['whsgroup'] != "WALL" && $_POST['status'] == "SALL") || ($_POST['whsgroup'] != "WALL" && $_POST['status'] != "SALL")) {
        if($_POST['status'] != "SALL") {
			if($_POST['status'] == "D") {
				$WhrStatus = " AND T1.U_ProductStatus LIKE 'D%'";
			} else {
				$WhrStatus = " AND T1.U_ProductStatus = '".$_POST['status']."'";
			}
		} else {
			$WhrStatus = "";
		}
		switch($_POST['whsgroup']) {
			case "GW100":
				$SAPWhre = " AND T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4')";
				$ERFWhre = " AND T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4')";
			break;
	
			case "GW200":
				$SAPWhre = " AND T0.WhsCode IN ('KB1','KB1.1')";
				$ERFWhre = " AND T0.WhsCode IN ('KB1','KB1.1')";
			break;
	
			case "GW300":
				$SAPWhre = " AND T2.Location IN (2)";
				$ERFWhre = " AND T0.WhsCode IN ('AGT','IMAX','JSI','KN','KTW','NST','PLA','PU','RST','SY','TC','VRK','YEE','YMT')";
			break;
	
			case "GW400":
				$SAPWhre = " AND T2.Location IN (6,7,9)";
				$ERFWhre = " AND (T0.WhsCode LIKE 'B%' OR T0.WhsCode LIKE 'K%' OR T0.WhsCode LIKE 'M%' OR T0.WhsCode = 'MK01' OR T0.WhsCode = 'SALE' OR T0.WhsCode = 'TT' OR T0.WhsCode LIKE 'WA%' OR T0.WhsCode LIKE 'WB%' OR T0.WhsCode LIKE 'WC%' OR T0.WhsCode LIKE 'WD%' OR T0.WhsCode LIKE 'WK%' OR T0.WhsCode LIKE 'WM%' OR T0.WhsCode = 'WP01' OR T0.WhsCode LIKE 'RD%' OR T0.WhsCode LIKE 'KB5%' OR T0.WhsCode LIKE 'KB6%' OR T0.WhsCode = 'KB7')";
			break;
	
			case "GW500":
				$SAPWhre = " AND (T0.WhsCode NOT IN ('KB2','KSY','KSM','KBM','KB4','MT','MT2','TT-C','OUL','KB1','KB1.1') AND T2.Location NOT IN (2,6,7,9))";
				$ERFWhre = " AND (T0.WhsCode NOT IN ('KB2','KSY','KSM','KBM','KB4','MT','MT2','TT-C','OUL','KB1','KB1.1','AGT','IMAX','JSI','KN','KTW','NST','PLA','PU','RST','SY','TC','VRK','YEE','YMT') AND 
							(T0.WhsCode NOT LIKE 'B%' OR T0.WhsCode NOT LIKE 'K%' OR T0.WhsCode NOT LIKE 'M%' OR T0.WhsCode != 'MK01' OR T0.WhsCode != 'SALE' OR T0.WhsCode != 'TT' OR T0.WhsCode NOT LIKE 'WA%' OR T0.WhsCode NOT LIKE 'WB%' OR T0.WhsCode NOT LIKE 'WC%' OR T0.WhsCode NOT LIKE 'WD%' OR T0.WhsCode NOT LIKE 'WK%' OR T0.WhsCode NOT LIKE 'WM%' OR T0.WhsCode != 'WP01' OR T0.WhsCode NOT LIKE 'RD%' OR T0.WhsCode NOT LIKE 'KB5%' OR T0.WhsCode NOT LIKE 'KB6%' OR T0.WhsCode != 'KB7'))";
			break;
	
			default:
				$SAPWhre = " AND T0.WhsCode = '".$_POST['whsgroup']."'";
				$ERFWhre = " AND T0.WhsCode = '".$_POST['whsgroup']."'";
			break;
		}

		$ListSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
						T0.ItemCode, T1.CodeBars, T1.ItemName, T1.U_ProductStatus AS 'Status', T1.InvntryUom,
						(CASE WHEN T1.LastPurDat = '2022-12-31' THEN ISNULL(T3.LastPurPrc, T1.LastPurPrc) ELSE T1.LastPurPrc END *1.07) AS 'LastPurPrc', ".str_replace(array("B0","B1","B2"),array("T0","T1","T3"),$SQLAging)." AS 'Aging',
						T0.OnHand, T0.OnOrder, T0.WhsCode, T2.WhsName
					FROM OITW T0
					LEFT JOIN OITM T1 ON T0.ItemCode = T1.ItemCode
					LEFT JOIN OWHS T2 ON T0.WhsCode = T2.WhsCode
                    LEFT JOIN KBI_DB2022.dbo.OITM T3 ON T0.ItemCode = T3.ItemCode
					WHERE (T1.InvntItem != 'N' AND T0.ItemCode != '00-000-003') ".$WhrZero2.$SAPWhre.$WhrStatus2."
					ORDER BY T0.WhsCode, T0.ItemCode";
					// echo $ListSQL;
		$ListQRY = SAPSelect($ListSQL);

		$PickSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
						T0.ItemCode, T0.WhsCode, SUM(T0.Qty) AS 'Qty',
						SUM(T0.OpenQty) AS 'OpenQty'
					FROM picker_sodetail T0
					LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry
					WHERE (T1.DocType IN ('ORDR','OWAS','OWAB') AND (T1.StatusDoc BETWEEN 2 AND 8)) ".$ERFWhre."
					GROUP BY T0.ItemCode, T0.WhsCode";
					// echo $PickSQL;
		$PickQRY = MySQLSelectX($PickSQL);
		while($PickRST = mysqli_fetch_array($PickQRY)) {
			${$PickRST['ItemCode']."_".$PickRST['WhsCode']."_Qty"} = $PickRST['Qty'];
        	${$PickRST['ItemCode']."_".$PickRST['WhsCode']."_OpenQty"} = $PickRST['OpenQty'];
		}

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getProperties()
            ->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
            ->setTitle("รายงานสินค้าคงคลัง บจ.คิงบางกอก อินเตอร์เทรด")
            ->setSubject("รายงานสินค้าคงคลัง บจ.คิงบางกอก อินเตอร์เทรด");
        $spreadsheet->getDefaultStyle()->getFont()->setSize(8);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(12); // Value x 6 = pixel in excel
        $spreadsheet->setActiveSheetIndex(0);

        $PageHeader = [
            'font' => [ 'bold' => true, 'size' => 9 ],
            'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
        ];
        if($_SESSION['uClass'] == 0 OR $_SESSION['uClass'] == 2 OR $_SESSION['uClass'] == 3 OR $_SESSION['uClass'] == 4 OR $_SESSION['uClass'] == 5 OR $_SESSION['uClass'] == 13 OR $_SESSION['uClass'] == 16 OR $_SESSION['uClass'] == 14 OR $_SESSION['uClass'] == 15 OR $_SESSION['uClass'] == 17 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 29 OR $_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 34 OR $_SESSION['uClass'] == 43 OR $_SESSION['LvCode'] == 'LV052') {
            // HEADER //
            $sheet->setCellValue('A1',"รหัสสินค้า");
            $sheet->setCellValue('B1',"บาร์โค้ด");
            $sheet->setCellValue('C1',"ชื่อสินค้า");
            $sheet->setCellValue('D1',"สถานะ");
            $sheet->setCellValue('E1',"หน่วย");
            $sheet->setCellValue('F1',"มูลค่า (บาท)");
            $sheet->setCellValue('G1',"คลังสินค้า");
            $spreadsheet->getActiveSheet()->mergeCells('A1:A2');
            $spreadsheet->getActiveSheet()->mergeCells('B1:B2');
            $spreadsheet->getActiveSheet()->mergeCells('C1:C2');
            $spreadsheet->getActiveSheet()->mergeCells('D1:D2');
            $spreadsheet->getActiveSheet()->mergeCells('E1:E2');
            $spreadsheet->getActiveSheet()->mergeCells('F1:F2');
            $spreadsheet->getActiveSheet()->mergeCells('G1:G2');

            $sheet->setCellValue('H1',"จำนวน (หน่วย)");
            $spreadsheet->getActiveSheet()->mergeCells('H1:L1');

            $sheet->setCellValue('H2',"คงคลัง"); 
            $sheet->setCellValue('I2',"รอเบิก"); 
            $sheet->setCellValue('J2',"เบิกแล้ว");
            $sheet->setCellValue('K2',"คงเหลือ");
            $sheet->setCellValue('L2',"กำลังสั่งซื้อ");

            $sheet->setCellValue('M1',"AGING (เดือน)"); 
            $sheet->setCellValue('N1',"มูลค่ารวม (บาท)"); 
            $spreadsheet->getActiveSheet()->mergeCells('M1:M2');
            $spreadsheet->getActiveSheet()->mergeCells('N1:N2');

            $sheet->getStyle('A1:N1')->applyFromArray($PageHeader);
            $sheet->getStyle('H2:L2')->applyFromArray($PageHeader);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13.5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(34);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(13.4);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(16.8);

            $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(24,'px');
            $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(24,'px');

            $Head = ['H','I','J','K','L'];
            for($i = 0; $i < count($Head); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($Head[$i])->setWidth(18.2);
            }
            
            $Row = 2;
            $tempWhs = "";	
            while($ListRST = odbc_fetch_array($ListQRY)) {
                ++$Row;

                if(isset(${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_Qty"})){
                    $DT1 = ${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_Qty"}-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
                    $DT2 = ${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
                    $DT3 = $ListRST['OnHand']-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
                    $SUM = ($ListRST['OnHand']-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"})*$ListRST['LastPurPrc'];
                }else{
                    $DT1 = 0;
                    $DT2 = 0;
                    $DT3 = $ListRST['OnHand'];
                    $SUM = ($ListRST['OnHand']*$ListRST['LastPurPrc']);
                }

                $sheet->setCellValue('A'.$Row,$ListRST['ItemCode']); 
                $sheet->setCellValue('B'.$Row,$ListRST['CodeBars']); 
                $sheet->setCellValue('C'.$Row,conutf8($ListRST['ItemName'])); 
                $sheet->setCellValue('D'.$Row,$ListRST['Status']); 
                $sheet->setCellValue('E'.$Row,conutf8($ListRST['InvntryUom'])); 
                if(floatval($ListRST['LastPurPrc']) != 0) {
                    $sheet->setCellValue('F'.$Row,floatval($ListRST['LastPurPrc'])); 
                    $spreadsheet->getActiveSheet()->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
                }else{
                    $sheet->setCellValue('F'.$Row,'-'); 
                }
                $sheet->setCellValue('G'.$Row,conutf8($ListRST['WhsCode'])); 
                if(intval($ListRST['OnHand']) != 0) {
                    $sheet->setCellValue('H'.$Row,intval($ListRST['OnHand'])); 
                    $spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                }else{
                    $sheet->setCellValue('H'.$Row,'-'); 
                }
                if(intval($DT1) != 0) {
                    $sheet->setCellValue('I'.$Row,intval($DT1)); 
                    $spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                }else{
                    $sheet->setCellValue('I'.$Row,'-'); 
                }
                if(intval($DT2) != 0) {
                    $sheet->setCellValue('J'.$Row,intval($DT2)); 
                    $spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                }else{
                    $sheet->setCellValue('J'.$Row,'-'); 
                }
                if(intval($DT3) != 0) {
                    $sheet->setCellValue('K'.$Row,intval($DT3)); 
                    $spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                    $sheet->getStyle('K'.$Row)->applyFromArray(['font' => [ 'color' => ['rgb' => '9A1118'] ]]);
                }else{
                    $sheet->setCellValue('K'.$Row,'-'); 
                }
                if(intval($ListRST['OnOrder']) != 0) {
                    $sheet->setCellValue('L'.$Row,intval($ListRST['OnOrder'])); 
                    $spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                }else{
                    $sheet->setCellValue('L'.$Row,'-'); 
                }
                if(intval($ListRST['Aging']) != 0) {
                    $sheet->setCellValue('M'.$Row,intval($ListRST['Aging'])); 
                    $spreadsheet->getActiveSheet()->getStyle('M'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                    if($ListRST['Aging'] >= 25) {
                        $sheet->getStyle('M'.$Row)->applyFromArray(['font' => [ 'color' => ['rgb' => 'dc3545'] ]]);
                    } elseif($ListRST['Aging'] >= 7 && $ListRST['Aging'] <= 24) {
                        $sheet->getStyle('M'.$Row)->applyFromArray(['font' => [ 'color' => ['rgb' => '967102'] ]]);
                    } else {
                        $sheet->getStyle('M'.$Row)->applyFromArray(['font' => [ 'color' => ['rgb' => '198754'] ]]);
                    }
                }else{
                    $sheet->setCellValue('M'.$Row,'-'); 
                }
                if(floatval($SUM) != 0) {
                    $sheet->setCellValue('N'.$Row,floatval($SUM)); 
                    $spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
                }else{
                    $sheet->setCellValue('N'.$Row,'-'); 
                }

                $sheet->getStyle("A".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("B".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("D".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("E".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("F".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("G".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("H".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("I".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("J".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("K".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("L".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("M".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("N".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);

                $spreadsheet->getActiveSheet()->getRowDimension($Row)->setRowHeight(17,'px');
            }
        }else{
            // HEADER //
            $sheet->setCellValue('A1',"รหัสสินค้า");
            $sheet->setCellValue('B1',"บาร์โค้ด");
            $sheet->setCellValue('C1',"ชื่อสินค้า");
            $sheet->setCellValue('D1',"สถานะ");
            $sheet->setCellValue('E1',"หน่วย");
            $sheet->setCellValue('F1',"คลังสินค้า");
            $spreadsheet->getActiveSheet()->mergeCells('A1:A2');
            $spreadsheet->getActiveSheet()->mergeCells('B1:B2');
            $spreadsheet->getActiveSheet()->mergeCells('C1:C2');
            $spreadsheet->getActiveSheet()->mergeCells('D1:D2');
            $spreadsheet->getActiveSheet()->mergeCells('E1:E2');
            $spreadsheet->getActiveSheet()->mergeCells('F1:F2');

            $sheet->setCellValue('G1',"จำนวน (หน่วย)");
            $spreadsheet->getActiveSheet()->mergeCells('H1:K1');

            $sheet->setCellValue('G2',"คงคลัง"); 
            $sheet->setCellValue('H2',"รอเบิก"); 
            $sheet->setCellValue('I2',"เบิกแล้ว");
            $sheet->setCellValue('J2',"คงเหลือ");
            $sheet->setCellValue('K2',"กำลังสั่งซื้อ");

            $sheet->setCellValue('L1',"AGING (เดือน)"); 
            $spreadsheet->getActiveSheet()->mergeCells('L1:L2');

            $sheet->getStyle('A1:M1')->applyFromArray($PageHeader);
            $sheet->getStyle('G2:K2')->applyFromArray($PageHeader);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13.5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(34);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(13);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(13.4);

            $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(24,'px');
            $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(24,'px');

            $Head = ['G','H','I','J','K'];
            for($i = 0; $i < count($Head); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($Head[$i])->setWidth(18.2);
            }
            
            $Row = 2;
            $tempWhs = "";	
            while($ListRST = odbc_fetch_array($ListQRY)) {
                ++$Row;

                if(isset(${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_Qty"})){
                    $DT1 = ${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_Qty"}-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
                    $DT2 = ${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
                    $DT3 = $ListRST['OnHand']-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"};
                    $SUM = ($ListRST['OnHand']-${$ListRST['ItemCode']."_".$ListRST['WhsCode']."_OpenQty"})*$ListRST['LastPurPrc'];
                }else{
                    $DT1 = 0;
                    $DT2 = 0;
                    $DT3 = $ListRST['OnHand'];
                    $SUM = ($ListRST['OnHand']*$ListRST['LastPurPrc']);
                }

                $sheet->setCellValue('A'.$Row,$ListRST['ItemCode']); 
                $sheet->setCellValue('B'.$Row,$ListRST['CodeBars']); 
                $sheet->setCellValue('C'.$Row,conutf8($ListRST['ItemName'])); 
                $sheet->setCellValue('D'.$Row,$ListRST['Status']); 
                $sheet->setCellValue('E'.$Row,conutf8($ListRST['InvntryUom']));
                $sheet->setCellValue('F'.$Row,conutf8($ListRST['WhsCode'])); 
                if(intval($ListRST['OnHand']) != 0) {
                    $sheet->setCellValue('G'.$Row,intval($ListRST['OnHand'])); 
                    $spreadsheet->getActiveSheet()->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                }else{
                    $sheet->setCellValue('G'.$Row,'-'); 
                }
                if(intval($DT1) != 0) {
                    $sheet->setCellValue('H'.$Row,intval($DT1)); 
                    $spreadsheet->getActiveSheet()->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                }else{
                    $sheet->setCellValue('H'.$Row,'-'); 
                }
                if(intval($DT2) != 0) {
                    $sheet->setCellValue('I'.$Row,intval($DT2)); 
                    $spreadsheet->getActiveSheet()->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                }else{
                    $sheet->setCellValue('I'.$Row,'-'); 
                }
                if(intval($DT3) != 0) {
                    $sheet->setCellValue('J'.$Row,intval($DT3)); 
                    $spreadsheet->getActiveSheet()->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                    $sheet->getStyle('J'.$Row)->applyFromArray(['font' => [ 'color' => ['rgb' => '9A1118'] ]]);
                }else{
                    $sheet->setCellValue('J'.$Row,'-'); 
                }
                if(intval($ListRST['OnOrder']) != 0) {
                    $sheet->setCellValue('K'.$Row,intval($ListRST['OnOrder'])); 
                    $spreadsheet->getActiveSheet()->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                }else{
                    $sheet->setCellValue('K'.$Row,'-'); 
                }
                if(intval($ListRST['Aging']) != 0) {
                    $sheet->setCellValue('L'.$Row,intval($ListRST['Aging'])); 
                    $spreadsheet->getActiveSheet()->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
                    if($ListRST['Aging'] >= 25) {
                        $sheet->getStyle('L'.$Row)->applyFromArray(['font' => [ 'color' => ['rgb' => 'dc3545'] ]]);
                    } elseif($ListRST['Aging'] >= 7 && $ListRST['Aging'] <= 24) {
                        $sheet->getStyle('L'.$Row)->applyFromArray(['font' => [ 'color' => ['rgb' => '967102'] ]]);
                    } else {
                        $sheet->getStyle('L'.$Row)->applyFromArray(['font' => [ 'color' => ['rgb' => '198754'] ]]);
                    }
                }else{
                    $sheet->setCellValue('L'.$Row,'-'); 
                }

                $sheet->getStyle("A".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("B".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("D".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("E".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("F".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("G".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("H".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("I".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("J".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("K".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
                $sheet->getStyle("L".$Row)->applyFromArray([ 'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);

                $spreadsheet->getActiveSheet()->getRowDimension($Row)->setRowHeight(17,'px');
            }
        }
    }

    $writer = new Xlsx($spreadsheet);
	$FileName = "รายงานสินค้าคงคลัง -".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/InStock/".$FileName);

    $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'InStock', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
	$arrCol['ExportStatus'] = "SUCCESS";
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
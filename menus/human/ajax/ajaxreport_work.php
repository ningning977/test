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
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == 'CallData') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	$Team  = ($_POST['Team'] == 'ALL') ? "'DP002','DP003','DP004','DP005','DP006','DP007','DP008','DP009','DP010','DP011','DP012'" : "'".$_POST['Team']."'";

	/*$SQL = 
		"SELECT Q0.*,Q1.DateTimeStamp AS CheckOut,  
				CASE 
				WHEN Q1.RecordType = 'Import' THEN 'SCAN' 
				WHEN Q1.RecordType = 'ESS' THEN Q1.LocationName
				ELSE '' END AS CHKOutName
		FROM(
			SELECT K0.*,
				(
					SELECT TOP 1 (Y0.TempImportID) 
					FROM hrTimeTempImport Y0 
					WHERE K0.EmpID = Y0.EmpID AND DATEADD(dd,0,DATEDIFF(dd,0,Y0.DateTimeStamp)) = K0.DateWork  ORDER BY Y0.DateTimeStamp DESC 
				) AS TimeOutID
			FROM(
				SELECT Z0.EmpID,Z0.EmpCode,Z0.MemberCardExcept,Z0.FirstName,Z0.LastName,Z0.NickName,Z0.PositionName,Z0.DivName,Z0.DeptName,
						CASE WHEN Z0.Office = 0 THEN 'Office' ELSE 'PC' END AS EmpType,Z0.LevelRun,
						Z0.DeptCode,Z0.TimeIn,DATEADD(dd,0,DATEDIFF(dd,0,Z0.CheckIN)) AS DateWork,Z0.CheckIN,Z0.CHKName AS CheckINName
				FROM(       
					SELECT
						W0.*, W3.DateTimeStamp AS CheckIN,W1.OrgUnitName AS 'DivName', W2.OrgUnitName AS 'DeptName',
						CASE
							WHEN W3.RecordType = 'Import' THEN 'SCAN' 
							WHEN W3.RecordType = 'ESS' THEN W3.LocationName
						ELSE '' END AS CHKName,
						CASE 
							WHEN W0.DivCode IN (
								'DV011','DV014','DV017','DV022','DV032','DV033','DV034','DV035','DV036','DV037','DV038',
								'DV039','DV040','DV041','DV042','DV043','DV044','DV045','DV046','DV047','DV048','DV050'
							) THEN 1 ELSE 0 END AS Office,
						CASE
							WHEN W0.PosLevel = 'M2' THEN 0 
							WHEN W0.PosLevel = 'M1' THEN 1
							WHEN W0.PosLevel = 'L2' THEN 2
							WHEN W0.PosLevel = 'L1' THEN 3
							WHEN W0.PosLevel = 'O3' THEN 4
							WHEN W0.PosLevel = 'O2' THEN 5
							WHEN W0.PosLevel = 'O1' THEN 6
						ELSE 7 END AS LevelRun
					FROM(
						SELECT
							P0.PersonID,P0.EmpID,P0.WorkProFileID,P0.EmpCode,P0.MemberCardExcept,P0.FirstName,P0.LastName,P0.NickName,P0.WorkingStatus,P2.PositionCode,
							SUBSTRING(P2.PositionCode,0,3) AS PosLevel,P2.PositionName,
							CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DV' THEN P3.OrgUnitCode ELSE NULL END AS 'DivCode',
							CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DP' THEN P3.OrgUnitCode ELSE P4.OrgUnitCode END AS 'DeptCode',
							(SELECT TOP 1 (X0.TempImportID) FROM hrTimeTempImport X0 WHERE X0.EmpID = P0.EmpID AND YEAR(X0.DateTimeStamp) = $Year AND MONTH(X0.DateTimeStamp) = $Month ORDER BY X0.DateTimeStamp) AS TimeTmpID,
							CASE
								WHEN P5.TimeIn1 = 490 THEN '810AM' 
								WHEN P5.TimeIn1 = 510 THEN '830AM'
								WHEN P5.TimeIn1 = 540 THEN '900AM'
							ELSE 'PM' END AS TimeIN, 
							(
								SELECT TOP 1 N0.ApproveStatus 
								FROM  hrTimeAbstainTimeStamp N0
								JOIN hrTimeAbstainTimeStampDT N1 ON N1.AbstainTimeStampID =   N0.AbstainTimeStampID
								WHERE N0.EmpID = P0.EmpID AND N0.ApproveStatus = 'Y' AND  N0.IsCancel != 'TRUE'  AND N0.IsDeleted != 'TRUE' AND 
								YEAR(N1.StartDate) = $Year AND MONTH(N1.StartDate) >= $Month AND YEAR(N1.EndDate) = $Year AND MONTH(N1.EndDate) <= $Month
							) AS OutSite                        
						FROM(
							SELECT
								T0.PersonID,T1.EmpID,T1.EmpCode,T1.MemberCardExcept,T0.FirstName,T0.LastName,T0.NickName,T1.WorkingStatus,T1.ShiftID,
								(
									SELECT TOP 1 D1.WorkProfileID 
									FROM hrEmpWorkProfile D1 
									WHERE T1.EmpID = D1.EmpID  AND D1.EndDate IS NULL AND  D1.IsDeleted != 'TRUE' ORDER BY D1.ModifiedDate DESC
								) AS WorkProFileID               
								FROM emPerson T0
								LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
								WHERE T1.WorkingStatus = 'Working' AND  T0.IsDeleted != 'TRUE' AND T1.EmpCode NOT LIKE 'B%'
						) P0
						LEFT JOIN hrEmpWorkProfile P1 ON  P1.WorkProfileID = P0.WorkProfileID 
						LEFT JOIN emPosition P2 ON P1.PositionID =  P2.PositionID
						LEFT JOIN emOrgUnit P3 ON P1.OrgUnitID = P3.OrgUnitID
						LEFT JOIN emOrgUnit P4 ON P3.ParentOrgUnit = P4.OrgUnitID
						LEFT JOIN hrTimeShift P5 ON P0.ShiftID = P5.ShiftID 
						WHERE P1.OrgID = '3F3BF3AD-B4C9-4D44-A56F-AB55C4E4FB01' AND P2.PositionCode  NOT LIKE 'C%'
					) W0
					LEFT JOIN  emOrgUnit W1 ON W0.DivCode =  W1.OrgUnitCode
					LEFT JOIN  emOrgUnit W2 ON W0.DeptCode =  W2.OrgUnitCode
					LEFT JOIN hrTimeTempImport  W3 ON W0.TimeTmpID = W3.TempImportID
					WHERE W0.DeptCode IN ($Team)   
				) Z0 
			) K0 
		)Q0
		LEFT JOIN hrTimeTempImport Q1 ON Q0.TimeOutID = Q1.TempImportID ";*/

		$SQL ="SELECT Q0.*,Q1.DateTimeStamp AS CheckOut,		
		              CASE WHEN Q1.RecordType = 'Import' THEN 'SCAN' 
		                   WHEN Q1.RecordType = 'ESS' THEN Q1.LocationName
		                   ELSE '' END AS CHKOutName
               FROM (SELECT K0.*,
			               (SELECT TOP 1 (Y0.TempImportID) FROM hrTimeTempImport Y0 WHERE K0.EmpID = Y0.EmpID AND DATEADD(dd,0,DATEDIFF(dd,0,Y0.DateTimeStamp)) = K0.DateWork  ORDER BY Y0.DateTimeStamp DESC ) AS TimeOutID
                     FROM (SELECT Z0.EmpID,Z0.EmpCode,Z0.MemberCardExcept,Z0.FirstName,Z0.LastName,Z0.NickName,Z0.PositionName,Z0.DivName,Z0.DeptName,
		                          CASE WHEN Z0.Office = 0 THEN 'Office' ELSE 'PC' END AS EmpType,Z0.LevelRun,
		                          Z0.DeptCode,Z0.TimeIn,DATEADD(dd,0,DATEDIFF(dd,0,Z0.CheckIN)) AS DateWork,Z0.CheckIN,Z0.CHKName AS CheckINName
                           FROM (SELECT W0.*, W3.DateTimeStamp AS CheckIN,W1.OrgUnitName AS 'DivName', W2.OrgUnitName AS 'DeptName',
		 								CASE WHEN W3.RecordType = 'Import' THEN 'SCAN' 
			 								 WHEN W3.RecordType = 'ESS' THEN W3.LocationName
		 									 ELSE '' END AS CHKName,
		 								CASE WHEN W0.DivCode IN ('DV011','DV014','DV017','DV022','DV032','DV033','DV034','DV035','DV036','DV037','DV038','DV039','DV040','DV041','DV042','DV043','DV044','DV045','DV046','DV047','DV048','DV050') THEN 1 ELSE 0 END AS Office,
		 								CASE WHEN W0.PosLevel = 'M2' THEN 0 
			 								 WHEN W0.PosLevel = 'M1' THEN 1
											 WHEN W0.PosLevel = 'L2' THEN 2
											 WHEN W0.PosLevel = 'L1' THEN 3
											 WHEN W0.PosLevel = 'O3' THEN 4
											 WHEN W0.PosLevel = 'O2' THEN 5
											 WHEN W0.PosLevel = 'O1' THEN 6
											 ELSE 7 END AS LevelRun
								FROM (SELECT P0.PersonID,P0.EmpID,P0.WorkProFileID,P0.EmpCode,P0.MemberCardExcept,P0.FirstName,P0.LastName,P0.NickName,P0.WorkingStatus,P2.PositionCode,
										  	 SUBSTRING(P2.PositionCode,0,3) AS PosLevel,P2.PositionName,
											 CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DV' THEN P3.OrgUnitCode ELSE NULL END AS 'DivCode',
											 CASE WHEN SUBSTRING(P3.OrgUnitCode,0,3) = 'DP' THEN P3.OrgUnitCode ELSE P4.OrgUnitCode END AS 'DeptCode',
											 CASE WHEN P5.TimeIn1 = 490 THEN '810AM' 
											  	  WHEN P5.TimeIn1 = 510 THEN '830AM'
												  WHEN P5.TimeIn1 = 540 THEN '900AM'
												  ELSE 'PM' END AS TimeIN,
											 (SELECT TOP 1 (X0.TempImportID) FROM hrTimeTempImport X0 WHERE X0.EmpID = P0.EmpID AND (YEAR(X0.DateTimeStamp) = $Year AND MONTH(X0.DateTimeStamp) = $Month) ORDER BY X0.DateTimeStamp) AS TimeTmpID
		 							  FROM  (SELECT T0.PersonID,T1.EmpID,T1.EmpCode,T1.MemberCardExcept,T0.FirstName,T0.LastName,T0.NickName,T1.WorkingStatus,T1.ShiftID,
  				                                    (SELECT TOP 1 D1.WorkProfileID FROM hrEmpWorkProfile D1 WHERE T1.EmpID = D1.EmpID  AND D1.EndDate IS NULL AND  D1.IsDeleted != 'TRUE' ORDER BY D1.ModifiedDate DESC) AS WorkProFileID               
			                                 FROM emPerson T0
			 									  LEFT JOIN emEmployee T1 ON T1.PersonID =  T0.PersonID
			 								 WHERE T1.WorkingStatus = 'Working' AND  T0.IsDeleted != 'TRUE' AND T1.EmpCode NOT LIKE 'B%' ) P0
										    LEFT JOIN hrEmpWorkProfile P1 ON  P1.WorkProfileID = P0.WorkProfileID 
									        LEFT JOIN emPosition P2 ON P1.PositionID =  P2.PositionID
											LEFT JOIN emOrgUnit P3 ON P1.OrgUnitID = P3.OrgUnitID
											LEFT JOIN emOrgUnit P4 ON P3.ParentOrgUnit = P4.OrgUnitID
											LEFT JOIN hrTimeShift P5 ON P0.ShiftID = P5.ShiftID 
		 							  WHERE P1.OrgID = '3F3BF3AD-B4C9-4D44-A56F-AB55C4E4FB01' AND P2.PositionCode  NOT LIKE 'C%')  W0
									 LEFT JOIN  emOrgUnit W1 ON W0.DivCode =  W1.OrgUnitCode
									 LEFT JOIN  emOrgUnit W2 ON W0.DeptCode =  W2.OrgUnitCode
									 LEFT JOIN hrTimeTempImport  W3 ON W0.TimeTmpID = W3.TempImportID
	                 				WHERE W0.DeptCode IN ($Team)   ) Z0 ) K0 )Q0
  								     LEFT JOIN hrTimeTempImport Q1 ON Q0.TimeOutID = Q1.TempImportID ";

		 echo $SQL;
	// Add data
	for($r = 0; $r <= 7; $r++) {
		$arrCol['DataBody'][$r]['SlpCode'] = "6608105";
		$arrCol['DataBody'][$r]['SlpName'] = "พงษ์ศักดิ์ ขาวสุข (โอ๋)";
		$arrCol['DataBody'][$r]['DetpCode'] = "ฝ่ายขายห้างสรรพสินค้า 2";
		$DayInMonth = cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
		for($day = 1; $day <= $DayInMonth; $day++) {
			if(date("w",strtotime(date("Y-m-d",strtotime($Year."-".$Month."-".$day)))) == 0) {
				$arrCol['DataBody'][$r]['DaySun_'.$day] = "Y";
			}

			$arrCol['DataBody'][$r]['Day_'.$day] = "";
	
		}
		$arrCol['DataBody'][$r]['Work'] = "-";
		$arrCol['DataBody'][$r]['Sick'] = "-";
		$arrCol['DataBody'][$r]['Annual'] = "-";
		$arrCol['DataBody'][$r]['Vacation'] = "-";
		$arrCol['DataBody'][$r]['Other'] = "-";
		$arrCol['DataBody'][$r]['Late'] = "-";
	}
}

if($_GET['a'] == 'Export') {
	$Year  = $_POST['Year'];
	$Month = $_POST['Month'];
	$Team  = $_POST['Team'];

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานการทำงาน ขาด ลา มาสาย บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานการทำงาน ขาด ลา มาสาย บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	$sheet->setCellValue('A1',"รหัสพนักงาน");
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"ชื่อ - นามสกุล (ชื่อเล่น)");
	$spreadsheet->getActiveSheet()->mergeCells('B1:B2');
	$sheet->setCellValue('C1',"แผนก");
	$spreadsheet->getActiveSheet()->mergeCells('C1:C2');

	$DayInMonth = cal_days_in_month(CAL_GREGORIAN, $Month, $Year);
	$col = 3;
	for($day = 1; $day <= $DayInMonth; $day++) {
		$col++;
		$NameDay = "";
		switch (date("w",strtotime(date("Y-m-d",strtotime($Year."-".$Month."-".$day))))) {
			case 0: $NameDay = "Sun"; break;
			case 1: $NameDay = "Mon"; break;
			case 2: $NameDay = "Tue"; break;
			case 3: $NameDay = "Wed"; break;
			case 4: $NameDay = "Thu"; break;
			case 5: $NameDay = "Fri"; break;
			case 6: $NameDay = "Sat"; break;
		}
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $NameDay);
		if($NameDay == "Sun") {
			$StrRow1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
			$row1 = 1;
			$CellsetARGB1 = $StrRow1.$row1;
			$spreadsheet->getActiveSheet()->getStyle($CellsetARGB1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff8d7da');
		}

		$NumDay = ($day < 10) ? "0".$day : $day;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, 2, $NumDay);
		if($NameDay == "Sun") {
			$StrRow2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
			$row2 = 2;
			$CellsetARGB2 = $StrRow2.$row2;
			$spreadsheet->getActiveSheet()->getStyle($CellsetARGB2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff8d7da');
		}

		$StrIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
		$spreadsheet->getActiveSheet()->getColumnDimension($StrIndex)->setWidth(6);
	}

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+1, 1, "Summary");
	$spreadsheet->getActiveSheet()->mergeCellsByColumnAndRow($col+1, 1, $col+5, 1);

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+1, 2, "Work");
	$spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+1))->setWidth(9.5);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+2, 2, "Sick");
	$spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+2))->setWidth(9.5);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+3, 2, "Annual");
	$spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+3))->setWidth(9.5);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+4, 2, "Vacation");
	$spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+4))->setWidth(9.5);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+5, 2, "Other");
	$spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+5))->setWidth(9.5);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+6, 2, "Late");
	$spreadsheet->getActiveSheet()->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+6))->setWidth(9.5);

	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(27);
	$spreadsheet->getActiveSheet()->freezePane('D1');

	$colplus = $col+6;
	$StrCol1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colplus);
	$r1 = 1;
	$CellStyle1 = $StrCol1.$r1;
	$StrCol2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colplus);
	$r2 = 2;
	$CellStyle2 = $StrCol2.$r2;
	$sheet->getStyle('A1:'.$CellStyle1)->applyFromArray($PageHeader);
	$sheet->getStyle('A2:'.$CellStyle2)->applyFromArray($PageHeader);

	// Add data
	$Row = 2;
	for($r = 0; $r <= 7; $r++) {
		$Row++;
		$sheet->setCellValue('A'.$Row,'6608105');
		$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);

		$sheet->setCellValue('B'.$Row,'พงษ์ศักดิ์ ขาวสุข (โอ๋)');

		$sheet->setCellValue('C'.$Row,'ฝ่ายขายห้างสรรพสินค้า 2');

		$col_data = 3;
		for($day = 1; $day <= $DayInMonth; $day++) {
			$col_data++;
			$sheet->setCellValueByColumnAndRow($col_data, $Row, "");
			$StrCol_data = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_data);
			$CellStyle_data = $StrCol_data.$Row;
			$sheet->getStyle($CellStyle_data)->applyFromArray($TextCenter);
			if((date("w",strtotime(date("Y-m-d",strtotime($Year."-".$Month."-".$day))))) == 0) {
				$spreadsheet->getActiveSheet()->getStyle($CellStyle_data)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff8d7da');
			}
		}

		$sheet->setCellValueByColumnAndRow($col_data+1, $Row, "-");
		$StrCol_data1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_data+1);
		$CellStyle_data1 = $StrCol_data1.$Row;
		$sheet->getStyle($CellStyle_data1)->applyFromArray($TextRight);

		$sheet->setCellValueByColumnAndRow($col_data+2, $Row, "-");
		$StrCol_data2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_data+2);
		$CellStyle_data2 = $StrCol_data2.$Row;
		$sheet->getStyle($CellStyle_data2)->applyFromArray($TextRight);

		$sheet->setCellValueByColumnAndRow($col_data+3, $Row, "-");
		$StrCol_data3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_data+3);
		$CellStyle_data3 = $StrCol_data3.$Row;
		$sheet->getStyle($CellStyle_data3)->applyFromArray($TextRight);

		$sheet->setCellValueByColumnAndRow($col_data+4, $Row, "-");
		$StrCol_data4 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_data+4);
		$CellStyle_data4 = $StrCol_data4.$Row;
		$sheet->getStyle($CellStyle_data4)->applyFromArray($TextRight);

		$sheet->setCellValueByColumnAndRow($col_data+5, $Row, "-");
		$StrCol_data5 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_data+5);
		$CellStyle_data5 = $StrCol_data5.$Row;
		$sheet->getStyle($CellStyle_data5)->applyFromArray($TextRight);

		$sheet->setCellValueByColumnAndRow($col_data+6, $Row, "-");
		$StrCol_data6 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_data+6);
		$CellStyle_data6 = $StrCol_data6.$Row;
		$sheet->getStyle($CellStyle_data6)->applyFromArray($TextRight);
	}

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานการทำงาน ขาด ลา มาสาย - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/ReportWork/".$FileName);
	// $InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'ReportWork', logFile = '$FileName', DateCreate = NOW()";
	// MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
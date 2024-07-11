<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

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
	$Year = $_POST['Year'];
	$Month = date("m");
	$vType = ($Year >= 2023) ? "126" : "108";

	$SQL = "
		SELECT
			B0.SupType, B0.ProductStatus,
			SUM(B0.M_01) AS 'M_01', SUM(B0.M_02) AS 'M_02', SUM(B0.M_03) AS 'M_03',
			SUM(B0.M_04) AS 'M_04', SUM(B0.M_05) AS 'M_05', SUM(B0.M_06) AS 'M_06',
			SUM(B0.M_07) AS 'M_07', SUM(B0.M_08) AS 'M_08', SUM(B0.M_09) AS 'M_09',
			SUM(B0.M_10) AS 'M_10', SUM(B0.M_11) AS 'M_11', SUM(B0.M_12) AS 'M_12'
		FROM (
			SELECT
			A0.SupType, A0.ProductStatus,
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 1) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_01',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 2) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_02',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 3) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_03',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 4) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_04',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 5) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_05',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 6) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_06',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 7) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_07',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 8) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_08',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 9) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_09',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 10) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_10',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 11) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_11',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 12) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_12'
			FROM (
				SELECT
				YEAR(T0.DocDate) AS 'DateY', MONTH(T0.DocDate) AS 'DateM',
				CASE
					WHEN T1.GroupCode NOT IN ($vType) THEN 'DMT'
					WHEN T1.GroupCode IN ($vType) THEN 'OVS'
				ELSE 'XXX' END AS 'SupType',
				CASE
					WHEN T3.U_ProductStatus LIKE 'D%' THEN 'D'
					WHEN T3.U_ProductStatus = 'R' THEN 'R'
					WHEN T3.U_ProductStatus = 'A' THEN 'A'
					WHEN T3.U_ProductStatus = 'W' THEN 'W'
					WHEN T3.U_ProductStatus = 'N' THEN 'N'
					WHEN T3.U_ProductStatus = 'M' THEN 'M'
				ELSE 'K' END AS 'ProductStatus' , (T2.LineTotal) AS 'LineTotal'
				FROM OPDN T0
				LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode
				LEFT JOIN PDN1 T2 ON T0.DocEntry = T2.DocEntry
				LEFT JOIN OITM T3 ON T2.ItemCode = T3.ItemCode
				WHERE (T0.CANCELED = 'N' AND YEAR(T0.DocDate) = $Year)";
				if($Year == date("Y")) {
				$SQL .= "
					UNION ALL
					SELECT
					YEAR(T0.DocDueDate) AS 'DateY', MONTH(T0.DocDueDate) AS 'DateM',
					CASE
						WHEN T1.GroupCode NOT IN ($vType) THEN 'DMT'
						WHEN T1.GroupCode IN ($vType) THEN 'OVS'
					ELSE 'XXX' END AS 'SupType',
					CASE
						WHEN T3.U_ProductStatus LIKE 'D%' THEN 'D'
						WHEN T3.U_ProductStatus = 'R' THEN 'R'
						WHEN T3.U_ProductStatus = 'A' THEN 'A'
						WHEN T3.U_ProductStatus = 'W' THEN 'W'
						WHEN T3.U_ProductStatus = 'N' THEN 'N'
						WHEN T3.U_ProductStatus = 'M' THEN 'M'
					ELSE 'K' END AS 'ProductStatus' , (T2.LineTotal) AS 'LineTotal'
					FROM OPOR T0
					LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode
					LEFT JOIN POR1 T2 ON T0.DocEntry = T2.DocEntry
					LEFT JOIN OITM T3 ON T2.ItemCode = T3.ItemCode
					WHERE (T0.CANCELED = 'N' AND T0.DocStatus = 'O') AND (YEAR(T0.DocDueDate) = $Year AND MONTH(T0.DocDueDate) BETWEEN $Month AND 12)";
				}
			$SQL .= "
			) A0
			GROUP BY A0.SupType, A0.ProductStatus, A0.DateY, A0.DateM
		) B0
		GROUP BY B0.SupType, B0.ProductStatus
		ORDER BY
		B0.SupType,
		CASE
		WHEN B0.ProductStatus = 'D' THEN 1
		WHEN B0.ProductStatus = 'R' THEN 2
		WHEN B0.ProductStatus = 'A' THEN 3
		WHEN B0.ProductStatus = 'W' THEN 4
		WHEN B0.ProductStatus = 'N' THEN 5
		WHEN B0.ProductStatus = 'M' THEN 6
		ELSE 7 END";
	
	$QRY = ($Year >= 2023) ? SAPSelect($SQL) : conSAP8($SQL);
	$tmpSupType = "";
	$DataDMT = ""; 
	$DataOVS = "";
	for($m = 1; $m <= 12; $m++) { 
		${"SumDMTM_".$m} = 0; 
		${"SumOVSM_".$m} = 0; 
	}
	while($RST = odbc_fetch_array($QRY)) {
		if($tmpSupType != $RST['SupType']) {
			$tmpSupType = $RST['SupType'];
			switch($RST['SupType']) {
				case 'DMT': $NameSub = 'ในประเทศ'; break;
				case 'OVS': $NameSub = 'ต่างประเทศ'; break;
			}

			${"Data".$RST['SupType']} .= "
				<tr>
					<th colspan='14' class='text-center table-danger '>$NameSub</th>
				</tr>
				<tr>
					<td>".$RST['ProductStatus']."</td>";
					$Sum = 0;
					for($m = 1; $m <= 12; $m++) {
						$value_M = ($m < 10) ? $RST['M_0'.$m] : $RST['M_'.$m];
						$Sum = $Sum+$value_M;
						${"Sum".$RST['SupType']."M_".$m} = ${"Sum".$RST['SupType']."M_".$m}+$value_M;
						$style = ($m > date("m") && $Year == date("Y")) ? "text-danger" : "" ;
						${"Data".$RST['SupType']} .= "<td class='text-right $style'>".number_format($value_M,2)."</td>";
					}
					${"Data".$RST['SupType']} .= "
					<th class='text-right'>".number_format($Sum,2)."</th>";
				${"Data".$RST['SupType']} .= "
				<tr>";
		}else{
			${"Data".$RST['SupType']} .= "
				<tr>
					<td>".$RST['ProductStatus']."</td>";
					$Sum = 0;
					for($m = 1; $m <= 12; $m++) {
						$value_M = ($m < 10) ? $RST['M_0'.$m] : $RST['M_'.$m];
						$Sum = $Sum+$value_M;
						${"Sum".$RST['SupType']."M_".$m} = ${"Sum".$RST['SupType']."M_".$m}+$value_M;
						$style = ($m > date("m") && $Year == date("Y")) ? "text-danger" : "" ;
						${"Data".$RST['SupType']} .= "<td class='text-right $style'>".number_format($value_M,2)."</td>";
					}
					${"Data".$RST['SupType']} .= "
					<th class='text-right'>".number_format($Sum,2)."</th>";
				${"Data".$RST['SupType']} .= "
				<tr>";
		}
	}

	$Data = "";
		$Data .= ($DataDMT != '') ? $DataDMT : "<tr><th colspan='14' class='text-center'>ในประเทศ (ไม่มีข้อมูล)</th><tr>";
		$Data .= "
		<tr class='table-secondary'>
			<th>รวมทั้งหมด</th>";
			$SumDMT = 0;
			for($m = 1; $m <= 12; $m++) {
				$SumDMT = $SumDMT+${"SumDMTM_".$m};
				$Data .= "<th class='text-right text-danger'><a href='javascript:void(0);' onclick='ShowDetail($Year,$m,\"DMT\");'>".number_format(${"SumDMTM_".$m},2)."</a></th>";
			}
			$Data .= "<th class='text-right'>".number_format($SumDMT,2)."</th>";
		$Data .= "
		</tr>";
		$Data .= ($DataOVS != '') ? $DataOVS : "<tr><th colspan='14' class='text-center'>ในต่างประเทศ (ไม่มีข้อมูล)</th><tr>";
		$Data .= "
		<tr class='table-secondary'>
			<th>รวมทั้งหมด</th>";
			$SumOVS = 0;
			for($m = 1; $m <= 12; $m++) {
				$SumOVS = $SumOVS+${"SumOVSM_".$m};
				$Data .= "<th class='text-right text-danger'><a href='javascript:void(0);' onclick='ShowDetail($Year,$m,\"OVS\");'>".number_format(${"SumOVSM_".$m},2)."</a></th>";
			}
			$Data .= "<th class='text-right'>".number_format($SumOVS,2)."</th>";
		$Data .= "
		</tr>
		<tr>
			<th colspan='14' class='text-center table-danger'>รวมทั้งหมด</th>
		</tr>
		<tr>
			<td>ในประเทศ</td>";
			$SumAllDMT = 0;
			for($m = 1; $m <= 12; $m++) {
				$SumAllDMT = $SumAllDMT+${"SumDMTM_".$m};
				$Data .= "<td class='text-right'>".number_format(${"SumDMTM_".$m},2)."</td>";
			}
			$Data .= "<th class='text-right'>".number_format($SumAllDMT,2)."</th>";
		$Data .= "
		</tr>
		<tr>
			<td>ในต่างประเทศ</td>";
			$SumAllOVS = 0;
			for($m = 1; $m <= 12; $m++) {
				$SumAllOVS = $SumAllOVS+${"SumOVSM_".$m};
				$Data .= "<td class='text-right'>".number_format(${"SumOVSM_".$m},2)."</td>";
			}
			$Data .= "<th class='text-right'>".number_format($SumAllOVS,2)."</th>";
		$Data .= "
		</tr>
		<tr class='table-secondary'>
			<th>รวมทั้งหมด</th>";
			$SumAll = 0;
			for($m = 1; $m <= 12; $m++) {
				$SumAll = $SumAll+(${"SumDMTM_".$m}+${"SumOVSM_".$m});
				$Data .= "<th class='text-right'>".number_format((${"SumDMTM_".$m}+${"SumOVSM_".$m}),2)."</th>";
			}
			$Data .= "<th class='text-right'>".number_format($SumAll,2)."</th>";
		$Data .= "
		</tr>";

	$arrCol['Data'] = $Data;
}

if($_GET['a'] == 'ShowDetail') {
	$Year = $_POST['Year'];
	$Month = $_POST['Month'];
	$Type = ($_POST['Type'] == 'DMT') ? "NOT IN" : "IN";
	$vType = ($Year >= 2023) ? "126" : "108";

	$SQL = "
		SELECT
			'OPDN' AS 'DocType', T0.DocDate, NULL AS 'DocDueDate',
			T3.BeginStr+CAST(T0.DocNum AS VARCHAR) AS 'DocNum', T0.CardCode, T0.CardName,
			T1.ItemCode, T1.Dscription, T1.WhsCode, T1.Quantity, T1.unitMsr, (T1.LineTotal) AS 'LineTotal', T4.U_ProductStatus
		FROM OPDN T0
		LEFT JOIN PDN1 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode
		LEFT JOIN NNM1 T3 ON T0.Series = T3.Series
		LEFT JOIN OITM T4 ON T1.ItemCode = T4.ItemCode
		WHERE (YEAR(T0.DocDate) = $Year AND MONTH(T0.DocDate) = $Month AND T0.CANCELED = 'N') AND T2.GroupCode $Type ($vType)";

	if($Year == date("Y") && $Month >= date("m")) {
		$SQL .= "
			UNION ALL
			SELECT
				'OPOR' AS 'DocType', NULL AS 'DocDate', T0.DocDueDate AS 'DocDueDate',
				ISNULL(T3.BeginStr,'')+CAST(T0.DocNum AS VARCHAR) AS 'DocNum', T0.CardCode, T0.CardName,
				T1.ItemCode, T1.Dscription, T1.WhsCode, T1.Quantity, T1.unitMsr, (T1.LineTotal) AS 'LineTotal', T4.U_ProductStatus
			FROM OPOR T0
			LEFT JOIN POR1 T1 ON T0.DocEntry = T1.DocEntry
			LEFT JOIN OCRD T2 ON T0.CardCode = T2.CardCode
			LEFT JOIN NNM1 T3 ON T0.Series = T3.Series
			LEFT JOIN OITM T4 ON T1.ItemCode = T4.ItemCode
			WHERE (YEAR(T0.DocDueDate) = $Year AND MONTH(T0.DocDueDate) = $Month AND T0.CANCELED = 'N') AND T2.GroupCode $Type ($vType)";
	}

	$QRY = ($Year >= 2023) ? SAPSelect($SQL) : conSAP8($SQL);
	$r = 0;
	while($RST = odbc_fetch_array($QRY)) {
		$arrCol[$r]['DocType'] = $RST['DocType'];

		$arrCol[$r]['DocDate'] = (date("d/m/Y", strtotime($RST['DocDate'])) != '01/01/1970') ? date("d/m/Y", strtotime($RST['DocDate'])) : "-";
		$arrCol[$r]['DocDueDate'] = (date("d/m/Y", strtotime($RST['DocDueDate'])) != '01/01/1970') ? date("d/m/Y", strtotime($RST['DocDueDate'])) : "-";
		$arrCol[$r]['DocNum'] = $RST['DocNum'];
		$arrCol[$r]['CardCode'] = $RST['CardCode'];
		$arrCol[$r]['CardName'] = conutf8($RST['CardName']);
		$arrCol[$r]['ItemCode'] = $RST['ItemCode'];
		$arrCol[$r]['ItemName'] = conutf8($RST['Dscription']);
		$arrCol[$r]['ProductStatus'] = conutf8($RST['U_ProductStatus']);
		$arrCol[$r]['WhsCode'] = $RST['WhsCode'];
		$arrCol[$r]['Quantity'] = number_format($RST['Quantity'],0);
		$arrCol[$r]['unitMsr'] = conutf8($RST['unitMsr']);
		$arrCol[$r]['Linetotal'] = number_format($RST['LineTotal'],2);
		$r++;
	}
}

if($_GET['a'] == 'Export') {
	$Year = $_POST['Year'];
	$Month = date("m");
	$vType = ($Year >= 2023) ? "126" : "108";

	$SQL = "
		SELECT
			B0.SupType, B0.ProductStatus,
			SUM(B0.M_01) AS 'M_01', SUM(B0.M_02) AS 'M_02', SUM(B0.M_03) AS 'M_03',
			SUM(B0.M_04) AS 'M_04', SUM(B0.M_05) AS 'M_05', SUM(B0.M_06) AS 'M_06',
			SUM(B0.M_07) AS 'M_07', SUM(B0.M_08) AS 'M_08', SUM(B0.M_09) AS 'M_09',
			SUM(B0.M_10) AS 'M_10', SUM(B0.M_11) AS 'M_11', SUM(B0.M_12) AS 'M_12'
		FROM (
			SELECT
			A0.SupType, A0.ProductStatus,
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 1) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_01',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 2) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_02',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 3) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_03',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 4) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_04',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 5) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_05',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 6) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_06',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 7) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_07',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 8) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_08',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 9) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_09',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 10) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_10',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 11) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_11',
			CASE WHEN (A0.DateY = $Year AND A0.DateM = 12) THEN SUM(A0.LineTotal) ELSE 0 END AS 'M_12'
			FROM (
				SELECT
				YEAR(T0.DocDate) AS 'DateY', MONTH(T0.DocDate) AS 'DateM',
				CASE
					WHEN T1.GroupCode NOT IN ($vType) THEN 'DMT'
					WHEN T1.GroupCode IN ($vType) THEN 'OVS'
				ELSE 'XXX' END AS 'SupType',
				CASE
					WHEN T3.U_ProductStatus LIKE 'D%' THEN 'D'
					WHEN T3.U_ProductStatus = 'R' THEN 'R'
					WHEN T3.U_ProductStatus = 'A' THEN 'A'
					WHEN T3.U_ProductStatus = 'W' THEN 'W'
					WHEN T3.U_ProductStatus = 'N' THEN 'N'
					WHEN T3.U_ProductStatus = 'M' THEN 'M'
				ELSE 'K' END AS 'ProductStatus' , (T2.LineTotal) AS 'LineTotal'
				FROM OPDN T0
				LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode
				LEFT JOIN PDN1 T2 ON T0.DocEntry = T2.DocEntry
				LEFT JOIN OITM T3 ON T2.ItemCode = T3.ItemCode
				WHERE (T0.CANCELED = 'N' AND YEAR(T0.DocDate) = $Year)";
				if($Year == date("Y")) {
				$SQL .= "
					UNION ALL
					SELECT
					YEAR(T0.DocDueDate) AS 'DateY', MONTH(T0.DocDueDate) AS 'DateM',
					CASE
						WHEN T1.GroupCode NOT IN ($vType) THEN 'DMT'
						WHEN T1.GroupCode IN ($vType) THEN 'OVS'
					ELSE 'XXX' END AS 'SupType',
					CASE
						WHEN T3.U_ProductStatus LIKE 'D%' THEN 'D'
						WHEN T3.U_ProductStatus = 'R' THEN 'R'
						WHEN T3.U_ProductStatus = 'A' THEN 'A'
						WHEN T3.U_ProductStatus = 'W' THEN 'W'
						WHEN T3.U_ProductStatus = 'N' THEN 'N'
						WHEN T3.U_ProductStatus = 'M' THEN 'M'
					ELSE 'K' END AS 'ProductStatus' , (T2.LineTotal) AS 'LineTotal'
					FROM OPOR T0
					LEFT JOIN OCRD T1 ON T0.CardCode = T1.CardCode
					LEFT JOIN POR1 T2 ON T0.DocEntry = T2.DocEntry
					LEFT JOIN OITM T3 ON T2.ItemCode = T3.ItemCode
					WHERE (T0.CANCELED = 'N' AND T0.DocStatus = 'O') AND (YEAR(T0.DocDueDate) = $Year AND MONTH(T0.DocDueDate) BETWEEN $Month AND 12)";
				}
			$SQL .= "
			) A0
			GROUP BY A0.SupType, A0.ProductStatus, A0.DateY, A0.DateM
		) B0
		GROUP BY B0.SupType, B0.ProductStatus
		ORDER BY
		B0.SupType,
		CASE
		WHEN B0.ProductStatus = 'D' THEN 1
		WHEN B0.ProductStatus = 'R' THEN 2
		WHEN B0.ProductStatus = 'A' THEN 3
		WHEN B0.ProductStatus = 'W' THEN 4
		WHEN B0.ProductStatus = 'N' THEN 5
		WHEN B0.ProductStatus = 'M' THEN 6
		ELSE 7 END";
	
	$QRY = ($Year >= 2023) ? SAPSelect($SQL) : conSAP8($SQL);

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานปฏิทินจัดซื้อ บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานปฏิทินจัดซื้อ บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);
	$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0);

	$sheet->setCellValue('A1',"สถานะ");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ff9a1118');
	$spreadsheet->setActiveSheetIndex(0)->getStyle('A1')->getFont()->getColor()->setARGB('ffffffff');
	$spreadsheet->getActiveSheet()->mergeCells('A1:A2');
	$sheet->setCellValue('B1',"มูลค่าสินค้าเข้าแล้ว + แผนการรับสินค้าเข้าปี $Year");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ff9a1118');
	$spreadsheet->setActiveSheetIndex(0)->getStyle('B1')->getFont()->getColor()->setARGB('ffffffff');
	$spreadsheet->getActiveSheet()->mergeCells('B1:N1');
	$mCell = ['0','B','C','D','E','F','G','H','I','J','K','L','M'];
	for($m = 1; $m <= 12; $m++) {
		$sheet->setCellValue($mCell[$m]."2",FullMonth($m));
		$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m]."2")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ff9a1118');
		$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m]."2")->getFont()->getColor()->setARGB('ffffffff');
	}
	$sheet->setCellValue("N2","รวมทั้งหมด");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("N2")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ff9a1118');
	$spreadsheet->setActiveSheetIndex(0)->getStyle("N2")->getFont()->getColor()->setARGB('ffffffff');

	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];
	$TextSum = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ],
				'font' => [ 'bold' => true ]];

	$sheet->getStyle('A1:N1')->applyFromArray($PageHeader);
	$sheet->getStyle('A2:N2')->applyFromArray($PageHeader);
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13);
	for($m = 1; $m <= 12; $m++) {
		$spreadsheet->getActiveSheet()->getColumnDimension($mCell[$m])->setWidth(16);
	}
	$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(16);
	
	$Row = 3; $No = 0;
	$tmpSupType = "NULL";
	for($m = 1; $m <= 12; $m++) { 
		${"SumDMTM_".$m} = 0; 
		${"SumOVSM_".$m} = 0; 
	}
	while($RST = odbc_fetch_array($QRY)) {
		if($tmpSupType != $RST['SupType']) {
			if($tmpSupType != "NULL") {
				$sheet->setCellValue('A'.$Row,'รวมทั้งหมด');
				$sheet->getStyle('A'.$Row)->applyFromArray($TextBold);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');

				$Sum = 0;
				for($m = 1; $m <= 12; $m++) {
					$Sum = $Sum+${"SumDMTM_".$m};
					
					$sheet->setCellValue($mCell[$m].$Row,${"SumDMTM_".$m});
					$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0.00");
					$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextSum);
					$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m].$Row)->getFont()->getColor()->setARGB('ff9a1118');
					$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m].$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');
				}

				$sheet->setCellValue('N'.$Row,$Sum);
				$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
				$sheet->getStyle('N'.$Row)->applyFromArray($TextSum);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('N'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');

				$Row++;
			}

			$tmpSupType = $RST['SupType'];
			switch($RST['SupType']) {
				case 'DMT': $NameSub = 'ในประเทศ'; break;
				case 'OVS': $NameSub = 'ต่างประเทศ'; break;
			}

			$sheet->setCellValue('A'.$Row,$NameSub);
			$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);
			$sheet->getStyle('A'.$Row)->applyFromArray($TextBold);
			$spreadsheet->getActiveSheet()->mergeCells('A'.$Row.':N'.$Row);
			$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff8d7da');
			$Row++;

			$sheet->setCellValue('A'.$Row,$RST['ProductStatus']);

			$Sum = 0;
			for($m = 1; $m <= 12; $m++) {
				$value_M = ($m < 10) ? $RST['M_0'.$m] : $RST['M_'.$m];
				$Sum = $Sum+$value_M;
				${"Sum".$RST['SupType']."M_".$m} = ${"Sum".$RST['SupType']."M_".$m}+$value_M;
				
				$sheet->setCellValue($mCell[$m].$Row,$value_M);
				$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0.00");
				$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
				if($m > date("m") && $Year == date("Y")) {
					$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m].$Row)->getFont()->getColor()->setARGB('ffdc3545');
				}
			}

			$sheet->setCellValue('N'.$Row,$Sum);
			$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
			$sheet->getStyle('N'.$Row)->applyFromArray($TextSum);

			$Row++;
		}else{
			$sheet->setCellValue('A'.$Row,$RST['ProductStatus']);

			$Sum = 0;
			for($m = 1; $m <= 12; $m++) {
				$value_M = ($m < 10) ? $RST['M_0'.$m] : $RST['M_'.$m];
				$Sum = $Sum+$value_M;
				${"Sum".$RST['SupType']."M_".$m} = ${"Sum".$RST['SupType']."M_".$m}+$value_M;
				
				$sheet->setCellValue($mCell[$m].$Row,$value_M);
				$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0.00");
				$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
				if($m > date("m") && $Year == date("Y")) {
					$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m].$Row)->getFont()->getColor()->setARGB('ffdc3545');
				}
			}

			$sheet->setCellValue('N'.$Row,$Sum);
			$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
			$sheet->getStyle('N'.$Row)->applyFromArray($TextSum);
			
			$Row++;
		}
	}

	$sheet->setCellValue('A'.$Row,'รวมทั้งหมด');
	$sheet->getStyle('A'.$Row)->applyFromArray($TextBold);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');

	$Sum = 0;
	for($m = 1; $m <= 12; $m++) {
		$Sum = $Sum+${"SumOVSM_".$m};
		
		$sheet->setCellValue($mCell[$m].$Row,${"SumOVSM_".$m});
		$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextSum);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m].$Row)->getFont()->getColor()->setARGB('ff9a1118');
		$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m].$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');
	}

	$sheet->setCellValue('N'.$Row,$Sum);
	$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet->getStyle('N'.$Row)->applyFromArray($TextSum);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('N'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');

	$Row++;

	$sheet->setCellValue('A'.$Row,'รวมทั้งหมด');
	$sheet->getStyle('A'.$Row)->applyFromArray($TextCenter);
	$sheet->getStyle('A'.$Row)->applyFromArray($TextBold);
	$spreadsheet->getActiveSheet()->mergeCells('A'.$Row.':N'.$Row);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff8d7da');
	
	$Row++;

	$sheet->setCellValue('A'.$Row,'ในประเทศ');
	$SumAllDMT = 0;
	for($m = 1; $m <= 12; $m++) {
		$SumAllDMT = $SumAllDMT+${"SumDMTM_".$m};
		
		$sheet->setCellValue($mCell[$m].$Row,${"SumDMTM_".$m});
		$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
	}
	$sheet->setCellValue('N'.$Row,$SumAllDMT);
	$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet->getStyle('N'.$Row)->applyFromArray($TextSum);

	$Row++;

	$sheet->setCellValue('A'.$Row,'ในต่างประเทศ');
	$SumAllOVS = 0;
	for($m = 1; $m <= 12; $m++) {
		$SumAllOVS = $SumAllOVS+${"SumOVSM_".$m};
		
		$sheet->setCellValue($mCell[$m].$Row,${"SumOVSM_".$m});
		$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextRight);
	}
	$sheet->setCellValue('N'.$Row,$SumAllOVS);
	$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet->getStyle('N'.$Row)->applyFromArray($TextSum);

	$Row++;

	$sheet->setCellValue('A'.$Row,'รวมทั้งหมด');
	$sheet->getStyle('A'.$Row)->applyFromArray($TextBold);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('A'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');

	$SumAll = 0;
	for($m = 1; $m <= 12; $m++) {
		$SumAll = $SumAll+(${"SumDMTM_".$m}+${"SumOVSM_".$m});
		
		$sheet->setCellValue($mCell[$m].$Row,(${"SumDMTM_".$m}+${"SumOVSM_".$m}));
		$spreadsheet->getActiveSheet()->getStyle($mCell[$m].$Row)->getNumberFormat()->setFormatCode("#,##0.00");
		$sheet->getStyle($mCell[$m].$Row)->applyFromArray($TextSum);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($mCell[$m].$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');
	}

	$sheet->setCellValue('N'.$Row,$SumAll);
	$spreadsheet->getActiveSheet()->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet->getStyle('N'.$Row)->applyFromArray($TextSum);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('N'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffe2e3e5');

	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานปฏิทินจัดซื้อ - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/Pucalendar/".$FileName);
	$arrCol['FileName'] = $FileName;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
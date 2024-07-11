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
if($_SESSION['UserName']==NULL ){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
}

if($_GET['p'] == "GetData") {
	$cnt_year = date("Y");
	$ps1_year = $cnt_year - 1;
	$ps2_year = $ps1_year - 1;
	$CardCode = $_POST['c'];
	$tab = $_POST['t'];

	$tar_year = (date("m") == 12) ? $cnt_year+1 : $cnt_year;

	$THEAD =
		"<tr>".
			"<th width='4.5%' rowspan='2'>ลำดับ</th>".	
			"<th width='7.5%' rowspan='2'>รหัสสินค้า</th>".
			"<th rowspan='2'>ชื่อสินค้า</th>".
			"<th width='5%' rowspan='2'>สถานะ</th>";
	switch($tab) {
		case 1:
			$THEAD .= 
				"<th colspan='2'>ยอดซื้อ $ps2_year</th>".
				"<th colspan='2'>ยอดซื้อ $ps1_year</th>".
				"<th colspan='2'>ยอดซื้อจริง $cnt_year</th>".
				"<th colspan='3'>ประมาณการสั่งซื้อ $tar_year</th>".
			"</tr>".
			"<tr>".
				"<th width='5%'>จำนวน<br/>(หน่วย)</th>".
				"<th width='7.5%'>มูลค่า<br/>(THB)</th>".
				"<th width='5%'>จำนวน<br/>(หน่วย)</th>".
				"<th width='7.5%'>มูลค่า<br/>(THB)</th>".
				"<th width='5%'>จำนวน<br/>(หน่วย)</th>".
				"<th width='7.5%'>มูลค่า<br/>(THB)</th>".
				"<th width='5%'>จำนวน<br/>(หน่วย)</th>".
				"<th width='7.5%'>มูลค่า<br/>(THB)</th>".
				"<th width='5%'>Growth<br/>(%)</th>".
			"</tr>";
			break;
		case 2:
			$THEAD .=
				"<th colspan='2'>ยอดขาย $ps1_year</th>".
				"<th colspan='2'>ยอดขาย $cnt_year</th>".
				"<th colspan='4'>สินค้าคงคลัง ".date("d/m/Y")."</th>".
			"</tr>".
			"<tr>".
				"<th width='7.5%'>รวมทั้งหมด<br/>(หน่วย)</th>".
				"<th width='7.5%'>เฉลี่ยต่อเดือน<br/>(หน่วย)</th>".
				"<th width='7.5%'>รวมทั้งหมด<br/>(หน่วย)</th>".
				"<th width='7.5%'>เฉลี่ยต่อเดือน<br/>(หน่วย)</th>".
				"<th width='7.5%'>จำนวนคงคลัง<br/>(หน่วย)</th>".
				"<th width='7.5%'>มูลค่าคงคลัง<br/>(THB)</th>".
				"<th width='7.5%'>ยอดขายเฉลี่ย<br/>12 เดือน (หน่วย)</th>".
				"<th width='5%'>T/O<br/>(เดือน)</th>".
			"</tr>";
			break;
		case 3:
			$THEAD .=
				"<th colspan='7'>วิเคราะห์การขาย $ps1_year</th>".
			"</tr>".
			"<tr>".
				"<th width='7.5%'>ต้นทุนขายรวม (VAT) (THB)</th>".
				"<th width='7.5%'>ต้นทุนขายเฉลี่ย/ตัว (VAT) (THB)</th>".
				"<th width='7.5%'>ราคาขายรวม (VAT) (THB)</th>".
				"<th width='7.5%'>ราคาขายเฉลี่ย/ตัว (VAT) (THB)</th>".
				"<th width='7.5%'>กำไรรวม (THB)</th>".
				"<th width='7.5%'>กำไรเฉลี่ย/ตัว (VAT) (THB)</th>".
				"<th width='5%'>% of GP</th>".
			"</tr>";
			break;
		case 4:
			$THEAD .=
				"<th colspan='7'>ข้อมูลการเคลมสินค้า $ps1_year</th>".
			"</tr>".
			"<tr>".
				"<th width='7.5%'>ยอดขาย $ps1_year<br/>(หน่วย)</th>".
				"<th width='7.5%'>คืนเพื่อเคลมซัพฯ<br/>$ps1_year (หน่วย)</th>".
				"<th width='7.5%'>เคลมซัพฯ แล้ว<br/>$ps1_year (หน่วย)</th>".
				"<th width='7.5%'>มูลค่าการเคลม $ps1_year (THB)</th>".
				"<th width='5%'>% การเคลม<br/>$ps1_year</th>".
				"<th width='7.5%'>รอเคลมซัพฯ<br/>(หน่วย)</th>".
				"<th width='7.5%'>มูลค่ารอเคลมซัพฯ (THB)</th>".
			"</tr>";
			break;
		case 5:
			$THEAD .=
				"<th colspan='7'>วิเคราะห์การขาย $cnt_year</th>".
			"</tr>".
			"<tr>".
				"<th width='7.5%'>ต้นทุนขายรวม (VAT) (THB)</th>".
				"<th width='7.5%'>ต้นทุนขายเฉลี่ย/ตัว (VAT) (THB)</th>".
				"<th width='7.5%'>ราคาขายรวม (VAT) (THB)</th>".
				"<th width='7.5%'>ราคาขายเฉลี่ย/ตัว (VAT) (THB)</th>".
				"<th width='7.5%'>กำไรรวม (THB)</th>".
				"<th width='7.5%'>กำไรเฉลี่ย/ตัว (VAT) (THB)</th>".
				"<th width='5%'>% of GP</th>".
			"</tr>";
			break;
		case 6:
			$THEAD .=
				"<th colspan='7'>ข้อมูลการเคลมสินค้า $cnt_year</th>".
			"</tr>".
			"<tr>".
				"<th width='7.5%'>ยอดขาย $cnt_year<br/>(หน่วย)</th>".
				"<th width='7.5%'>คืนเพื่อเคลมซัพฯ<br/>$cnt_year (หน่วย)</th>".
				"<th width='7.5%'>เคลมซัพฯ แล้ว<br/>$cnt_year (หน่วย)</th>".
				"<th width='7.5%'>มูลค่าการเคลม $cnt_year (THB)</th>".
				"<th width='5%'>% การเคลม<br/>$cnt_year</th>".
				"<th width='7.5%'>รอเคลมซัพฯ<br/>(หน่วย)</th>".
				"<th width='7.5%'>มูลค่ารอเคลมซัพฯ (THB)</th>".
			"</tr>";
			break;
	}

	$MinSQL = "";
	for($m=1; $m <= date("m"); $m++) {
		if($m < 10) {
			$MinSQL .= "(R0.M_0$m)";
		} else {
			$MinSQL .= "(R0.M_$m)";
		}

		if($m != date("m")) {
			$MinSQL .= ", ";
		}
	}

		if($cnt_year == 2023) {
			$tbprefix = "KBI_DB2022.dbo.";
		} else {
			$tbprefix = NULL;
		}
		
	$SQL1 = 
		"SELECT
			/* SECTION 0 */
			B0.ItemCode, B0.ItemName, B0.ProductStatus,
			/* SECTION 1 */
			B0.P2Y_Qty, B0.P2Y_Prc,
			B0.P1Y_Qty, B0.P1Y_Prc,
			CASE WHEN MONTH(GETDATE()) = 12 THEN ((B0.CRT_SumQty-B0.CRT_MinQty)/11)*14 ELSE ((B0.P1Y_SumQty-B0.P1Y_MinQty)/11)*14 END AS 'CRT_TarQty',
			(B0.CRT_TarPrc * CASE WHEN MONTH(GETDATE()) = 12 THEN ((B0.CRT_SumQty-B0.CRT_MinQty)/11)*14 ELSE ((B0.P1Y_SumQty-B0.P1Y_MinQty)/11)*14 END) AS 'CRT_TarPrc',
			B0.CRT_Qty, B0.CRT_Prc,
			/* SECTION 2 */
			((B0.P1Y_SumQty-B0.P1Y_MinQty)/11) AS 'P1Y_AvgQty', B0.P1Y_SumQty,
			((B0.CRT_SumQty-B0.CRT_MinQty)/CASE WHEN MONTH(GETDATE()) = 1 THEN 1 ELSE MONTH(GETDATE()) -1 END) AS 'CRT_AvgQty', B0.CRT_SumQty,
			B0.StockQty, (B0.StockQty * B0.CRT_TarPrc) AS 'StockValue',
			(B0.P12M_Qty/12) AS 'P12M_Qty',
			/* SECTION 3 */
			B0.P1Y_SumCost,
			B0.P1Y_SumPrce,
			(B0.P1Y_SumPrce - B0.P1Y_SumCost) AS 'P1Y_SumPrft',
			/* SECTION 4 */
			B0.CRT_SumCost,
			B0.CRT_SumPrce,
			(B0.CRT_SumPrce - B0.CRT_SumCost) AS 'CRT_SumPrft',
			/* SECTION 5 */
			B0.P1Y_QcQty, B0.P1Y_ClaimQty, (B0.P1Y_ClaimQty * B0.CRT_TarPrc) AS 'P1Y_ClaimPrc',
			B0.CRT_QcQty, B0.CRT_ClaimQty, (B0.CRT_ClaimQty * B0.CRT_TarPrc) AS 'CRT_ClaimPrc',
			B0.WPStockQty, (B0.WPStockQty * B0.CRT_TarPrc) AS 'WPStockValue'
		FROM (
			SELECT
			A0.ItemCode, A1.ItemName, A1.U_ProductStatus AS 'ProductStatus',
			SUM(A0.P2Y_Qty) AS 'P2Y_Qty', SUM(A0.P2Y_Prc) AS 'P2Y_Prc',
			SUM(A0.P1Y_Qty) AS 'P1Y_Qty', SUM(A0.P1Y_Prc) AS 'P1Y_Prc',
			SUM(A0.CRT_Qty) AS 'CRT_Qty', SUM(A0.CRT_Prc) AS 'CRT_Prc',
			ISNULL((
				SELECT SUM(P0.OnHand) FROM OITW P0 WHERE P0.ItemCode = A0.ItemCode AND P0.WhsCode IN ('KSY','KSM','MT','MT2','TT-C','OUL','KB4','PLA')
			),0) AS 'StockQty',
			ISNULL((
				SELECT SUM(P0.OnHand) FROM OITW P0 WHERE P0.ItemCode = A0.ItemCode AND P0.WhsCode IN ('WP4','WP5')
			),0) AS 'WPStockQty',
			/* CURRENT YEAR PURCHASE TARGET (PRICE/QTY) */
			ISNULL(
				CASE
					WHEN A1.LastPurDat = '2022-12-31'
					THEN (SELECT TOP 1 P0.PriceAfVat FROM KBI_DB2022.dbo.PDN1 P0 WHERE P0.ItemCode = A0.ItemCode AND P0.PriceAfVat > 0 ORDER BY P0.DocEntry DESC)
					ELSE ISNULL((SELECT TOP 1 P0.PriceAfVat FROM PDN1 P0 WHERE P0.ItemCode = A0.ItemCode AND P0.PriceAfVat > 0 ORDER BY P0.DocEntry DESC), A1.LastPurPrc)
				END
			,0) AS 'CRT_TarPrc',
			/* PAST 1 YEAR SALES DATA (MIN QTY) */
			ISNULL((
				SELECT
					(SELECT MIN(MinQty) FROM (VALUES (R0.M_01), (R0.M_02), (R0.M_03), (R0.M_04), (R0.M_05), (R0.M_06), (R0.M_07), (R0.M_08), (R0.M_09), (R0.M_10), (R0.M_11), (R0.M_12)) AS x (MinQty)) AS 'MinQty'
				FROM (
				SELECT
					SUM(Q0.M_01) AS 'M_01', SUM(Q0.M_02) AS 'M_02', SUM(Q0.M_03) AS 'M_03', SUM(Q0.M_04) AS 'M_04',
					SUM(Q0.M_05) AS 'M_05', SUM(Q0.M_06) AS 'M_06', SUM(Q0.M_07) AS 'M_07', SUM(Q0.M_08) AS 'M_08',
					SUM(Q0.M_09) AS 'M_09', SUM(Q0.M_10) AS 'M_10', SUM(Q0.M_11) AS 'M_11', SUM(Q0.M_12) AS 'M_12'
				FROM (
						SELECT
							CASE WHEN MONTH(P1.DocDate) = 1 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(P1.DocDate) = 2 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(P1.DocDate) = 3 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(P1.DocDate) = 4 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(P1.DocDate) = 5 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(P1.DocDate) = 6 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(P1.DocDate) = 7 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(P1.DocDate) = 8 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(P1.DocDate) = 9 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(P1.DocDate) = 10 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(P1.DocDate) = 11 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(P1.DocDate) = 12 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_12'
						FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' GROUP BY P1.DocDate
						UNION ALL
						SELECT
							CASE WHEN MONTH(P1.DocDate) = 1 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(P1.DocDate) = 2 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(P1.DocDate) = 3 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(P1.DocDate) = 4 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(P1.DocDate) = 5 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(P1.DocDate) = 6 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(P1.DocDate) = 7 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(P1.DocDate) = 8 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(P1.DocDate) = 9 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(P1.DocDate) = 10 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(P1.DocDate) = 11 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(P1.DocDate) = 12 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_12'
						FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' GROUP BY P1.DocDate
					) Q0
				) R0
			),0) AS 'P1Y_MinQty',
			/* CURRENT YEAR SALES DATA (SUM QTY) */
			ISNULL((
				SELECT
					SUM(Q0.Quantity) AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'P1Y_SumQty',
			/* CURRENT YEAR SALES DATA (MIN QTY) */
			ISNULL((
				SELECT
					(SELECT MIN(MinQty) FROM (VALUES $MinSQL) AS x (MinQty)) AS 'MinQty'
				FROM (
				SELECT
					SUM(Q0.M_01) AS 'M_01', SUM(Q0.M_02) AS 'M_02', SUM(Q0.M_03) AS 'M_03', SUM(Q0.M_04) AS 'M_04',
					SUM(Q0.M_05) AS 'M_05', SUM(Q0.M_06) AS 'M_06', SUM(Q0.M_07) AS 'M_07', SUM(Q0.M_08) AS 'M_08',
					SUM(Q0.M_09) AS 'M_09', SUM(Q0.M_10) AS 'M_10', SUM(Q0.M_11) AS 'M_11', SUM(Q0.M_12) AS 'M_12'
				FROM (
						SELECT
							CASE WHEN MONTH(P1.DocDate) = 1 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(P1.DocDate) = 2 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(P1.DocDate) = 3 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(P1.DocDate) = 4 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(P1.DocDate) = 5 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(P1.DocDate) = 6 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(P1.DocDate) = 7 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(P1.DocDate) = 8 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(P1.DocDate) = 9 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(P1.DocDate) = 10 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(P1.DocDate) = 11 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(P1.DocDate) = 12 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_12'
						FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' GROUP BY P1.DocDate
						UNION ALL
						SELECT
							CASE WHEN MONTH(P1.DocDate) = 1 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(P1.DocDate) = 2 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(P1.DocDate) = 3 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(P1.DocDate) = 4 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(P1.DocDate) = 5 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(P1.DocDate) = 6 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(P1.DocDate) = 7 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(P1.DocDate) = 8 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(P1.DocDate) = 9 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(P1.DocDate) = 10 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(P1.DocDate) = 11 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(P1.DocDate) = 12 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_12'
						FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' GROUP BY P1.DocDate
					) Q0
				) R0
			),0) AS 'CRT_MinQty',
			/* CURRENT YEAR SALES DATA (SUM QTY) */
			ISNULL((
				SELECT
					SUM(Q0.Quantity) AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Quantity) AS 'Quantity' FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'CRT_SumQty',

			/* LAST 12 MONTH (QTY) */
			ISNULL((
				SELECT
					SUM(Q0.Quantity) AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE P0.ItemCode = A0.ItemCode AND P0.DocDate > DATEADD(m,-12,GETDATE()) AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Quantity) AS 'Quantity' FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE P0.ItemCode = A0.ItemCode AND P0.DocDate > DATEADD(m,-12,GETDATE()) AND P1.CANCELED = 'N'
					UNION ALL
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE P0.ItemCode = A0.ItemCode AND P0.DocDate > DATEADD(m,-12,GETDATE()) AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE P0.ItemCode = A0.ItemCode AND P0.DocDate > DATEADD(m,-12,GETDATE()) AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'P12M_Qty',

			/* PAST 1 YEAR SALE ANALYSE (COST) */
			ISNULL((
				SELECT
					SUM(Q0.GrossBuyPr) AS 'GrossBuyPr'
				FROM (
					SELECT SUM(P0.GrossBuyPr * P0.Quantity) AS 'GrossBuyPr' FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.GrossBuyPr * P0.Quantity) AS 'GrossBuyPr' FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'P1Y_SumCost',

			/* PAST 1 YEAR SALE ANALYSE (PRICE) */
			ISNULL((
				SELECT
					SUM(Q0.Price) AS 'Price'
				FROM (
					SELECT SUM(P0.Price * P0.Quantity) AS 'Price' FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Price * P0.Quantity) AS 'Price' FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'P1Y_SumPrce',

			/* PAST 1 YEAR RETURN (QTY) */
			ISNULL((
				SELECT
					Q0.Quantity AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."RDN1 P0 LEFT JOIN ".$tbprefix."ORDN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' AND P0.WhsCode IN ('WP4','WP5')
				) Q0
			),0) AS 'P1Y_QcQty',
			/* PAST 1 YEAR CLAIM (QTY) */
			ISNULL((
				SELECT
					Q0.Quantity AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."RPD1 P0 LEFT JOIN ".$tbprefix."ORPD P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' AND P0.WhsCode IN ('WP4','WP5')
				) Q0
			),0) AS 'P1Y_ClaimQty',

			/* CURRENT YEAR SALE ANALYSE (COST) */
			ISNULL((
				SELECT
					SUM(Q0.GrossBuyPr) AS 'GrossBuyPr'
				FROM (
					SELECT SUM(P0.GrossBuyPr * P0.Quantity) AS 'GrossBuyPr' FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.GrossBuyPr * P0.Quantity) AS 'GrossBuyPr' FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'CRT_SumCost',

			/* CURRENT YEAR SALE ANALYSE (PRICE) */
			ISNULL((
				SELECT
					SUM(Q0.Price) AS 'Price'
				FROM (
					SELECT SUM(P0.Price * P0.Quantity) AS 'Price' FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Price * P0.Quantity) AS 'Price' FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'CRT_SumPrce',

			/* CURRENT YEAR RETURN (QTY) */
			ISNULL((
				SELECT
					Q0.Quantity AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM RDN1 P0 LEFT JOIN ORDN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' AND P0.WhsCode IN ('WP4','WP5')
				) Q0
			),0) AS 'CRT_QcQty',
			/* CURRENT YEAR CLAIM (QTY) */
			ISNULL((
				SELECT
					Q0.Quantity AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM RPD1 P0 LEFT JOIN ORPD P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' AND P0.WhsCode IN ('WP4','WP5')
				) Q0
			),0) AS 'CRT_ClaimQty'
			FROM (
				SELECT DISTINCT
					T0.ItemCode,
					SUM(CASE WHEN YEAR(T1.DocDate) = $ps2_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'P2Y_Qty',
					SUM(CASE WHEN YEAR(T1.DocDate) = $ps2_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'P2Y_Prc',
					SUM(CASE WHEN YEAR(T1.DocDate) = $ps1_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'P1Y_Qty',
					SUM(CASE WHEN YEAR(T1.DocDate) = $ps1_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'P1Y_Prc',
					SUM(CASE WHEN YEAR(T1.DocDate) = $cnt_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'CRT_Qty',
					SUM(CASE WHEN YEAR(T1.DocDate) = $cnt_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'CRT_Prc'
				FROM PDN1 T0
				LEFT JOIN OPDN T1 ON T0.DocEntry = T1.DocEntry
				WHERE T1.CardCode = '$CardCode' AND YEAR(T1.DocDate) BETWEEN $ps2_year AND $cnt_year AND T1.CANCELED = 'N'
				GROUP BY T0.ItemCode
				UNION ALL
				SELECT DISTINCT
					T0.ItemCode,
					SUM(CASE WHEN YEAR(T1.DocDate) = $ps2_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'P2Y_Qty',
					SUM(CASE WHEN YEAR(T1.DocDate) = $ps2_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'P2Y_Prc',
					SUM(CASE WHEN YEAR(T1.DocDate) = $ps1_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'P1Y_Qty',
					SUM(CASE WHEN YEAR(T1.DocDate) = $ps1_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'P1Y_Prc',
					SUM(CASE WHEN YEAR(T1.DocDate) = $cnt_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'CRT_Qty',
					SUM(CASE WHEN YEAR(T1.DocDate) = $cnt_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'CRT_Prc'
				FROM KBI_DB2022.dbo.PDN1 T0
				LEFT JOIN KBI_DB2022.dbo.OPDN T1 ON T0.DocEntry = T1.DocEntry
				WHERE T1.CardCode = '$CardCode' AND YEAR(T1.DocDate) BETWEEN $ps2_year AND $cnt_year AND T1.CANCELED = 'N'
				GROUP BY T0.ItemCode
			) A0
			LEFT JOIN OITM A1 ON A0.ItemCode = A1.ItemCode
			WHERE A0.ItemCode != '00-999-999'
			GROUP BY A0.ItemCode, A1.ItemName, A1.U_ProductStatus, A1.LastPurDat, A1.LastPurPrc
		) B0
		ORDER BY B0.P1Y_Prc DESC, B0.P1Y_Qty DESC, B0.ItemCode ASC";

	//echo $SQL1;
	$QRY1 = SAPSelect($SQL1);
	$TBODY = "";
	$no = 1;

	$P2Y_SumPrc = 0;
	$P1Y_SumPrc = 0;
	$CRT_SumPrc = 0;
	$CRT_SumTarPrc = 0;

	$StockSumValue = 0;

	$P1Y_SumAllCost = 0;
	$P1Y_SumAllPrce = 0;
	$P1Y_SumAllPrft = 0;

	$P1Y_SumClaimPrc = 0;
	$WPStockSumValue = 0;
	$CRT_SumClaimPrc = 0;

	while($RST1 = odbc_fetch_array($QRY1)) {
		$ItemCode = $RST1['ItemCode'];
		$ItemName = conutf8($RST1['ItemName']);
		$ProductStatus = conutf8($RST1['ProductStatus']);

		/* TAB 1 */
		if($RST1['P2Y_Qty'] == 0) { $P2Y_Qty = "-"; } else { $P2Y_Qty = number_format($RST1['P2Y_Qty'],0); }
		if($RST1['P2Y_Prc'] == 0) { $P2Y_Prc = "-"; } else { $P2Y_Prc = number_format($RST1['P2Y_Prc'],2); }
		if($RST1['P1Y_Qty'] == 0) { $P1Y_Qty = "-"; } else { $P1Y_Qty = number_format($RST1['P1Y_Qty'],0); }
		if($RST1['P1Y_Prc'] == 0) { $P1Y_Prc = "-"; } else { $P1Y_Prc = number_format($RST1['P1Y_Prc'],2); }
		if($RST1['CRT_Qty'] == 0) { $CRT_Qty = "-"; } else { $CRT_Qty = number_format($RST1['CRT_Qty'],0); }
		if($RST1['CRT_Prc'] == 0) { $CRT_Prc = "-"; } else { $CRT_Prc = number_format($RST1['CRT_Prc'],2); }
		if($RST1['CRT_TarQty'] == 0) { $CRT_TarQty = "-"; } else { $CRT_TarQty = number_format($RST1['CRT_TarQty'],0); }
		if($RST1['CRT_TarPrc'] == 0) { $CRT_TarPrc = "-"; } else { $CRT_TarPrc = number_format($RST1['CRT_TarPrc'],2); }
		if($RST1['CRT_TarQty'] > 0) {
			$TarPct = (date("m") == 12) ? (($RST1['CRT_TarQty'] - $RST1['CRT_Qty']) / $RST1['CRT_TarQty']) * 100 : (($RST1['CRT_TarQty'] - $RST1['P1Y_Qty']) / $RST1['CRT_TarQty']) * 100;
		} else {
			$TarPct = 0;
		}
		if($TarPct == 0) {
			$PctCls = "";
			$PctTxt = "-";
		} elseif($TarPct > 0) {
			$PctCls = "text-success";
			$PctTxt = number_format($TarPct,2)."%";
		} else {
			$PctCls = "text-danger";
			$PctTxt = number_format($TarPct,2)."%";
		}

		$P2Y_SumPrc = $P2Y_SumPrc + $RST1['P2Y_Prc'];
		$P1Y_SumPrc = $P1Y_SumPrc + $RST1['P1Y_Prc'];
		$CRT_SumPrc = $CRT_SumPrc + $RST1['CRT_Prc'];
		$CRT_SumTarPrc = $CRT_SumTarPrc + $RST1['CRT_TarPrc'];

		/* TAB 2 */
		if($RST1['P1Y_SumQty'] == 0) { $P1Y_SumQty = "-"; } else { $P1Y_SumQty = number_format($RST1['P1Y_SumQty'],0); }
		if($RST1['P1Y_AvgQty'] == 0) { $P1Y_AvgQty = "-"; } else { $P1Y_AvgQty = number_format($RST1['P1Y_AvgQty'],0); }
		if($RST1['CRT_AvgQty'] == 0) { $CRT_AvgQty = "-"; } else { $CRT_AvgQty = number_format($RST1['CRT_AvgQty'],0); }
		if($RST1['CRT_SumQty'] == 0) { $CRT_SumQty = "-"; } else { $CRT_SumQty = number_format($RST1['CRT_SumQty'],0); }
		if($RST1['StockQty'] == 0) { $StockQty = "-"; } else { $StockQty = number_format($RST1['StockQty'],0); }
		if($RST1['StockValue'] == 0) { $StockValue = "-"; } else { $StockValue = number_format($RST1['StockValue'],2); }
		if($RST1['P12M_Qty'] == 0) { $P12M_Qty = "-"; } else { $P12M_Qty = number_format($RST1['P12M_Qty'],0); }
		if($RST1['P12M_Qty'] > 0) {
			$TOV = ($RST1['StockQty']/$RST1['P12M_Qty']);
		} else {
			$TOV = "DEAD";
		}
		if($TOV == "DEAD") {
			$TovCls = "text-danger";
			$TovTxt = "DEAD";
		} elseif($TOV > 0) {
			if($TOV <= 4) {
				$TovCls = "table-warning text-warning";
			} elseif($TOV <= 6) {
				$TovCls = "table-success text-success";
			} else {
				$TovCls = "table-danger text-danger";
			}
			$TovTxt = number_format($TOV,1);
		}

		$StockSumValue = $StockSumValue + $RST1['StockValue'];

		/* TAB 3 */
		if($RST1['P1Y_SumCost'] == 0) { $P1Y_SumCost = "-"; } else { $P1Y_SumCost = number_format($RST1['P1Y_SumCost'],2); }
		if($RST1['P1Y_SumPrce'] == 0) { $P1Y_SumPrce = "-"; } else { $P1Y_SumPrce = number_format($RST1['P1Y_SumPrce'],2); }
		if($RST1['P1Y_SumPrft'] == 0) { $P1Y_SumPrft = "-"; } else { $P1Y_SumPrft = number_format($RST1['P1Y_SumPrft'],2); }
		if($RST1['P1Y_SumQty'] <= 0.00) {
			$P1Y_AvgCost = "-";
			$P1Y_AvgPrce = "-";
			$P1Y_AvgPrft = "-";
		} else {
			$P1Y_AvgCost = number_format($RST1['P1Y_SumCost']/$RST1['P1Y_SumQty'] ,2);
			$P1Y_AvgPrce = number_format($RST1['P1Y_SumPrce']/$RST1['P1Y_SumQty'] ,2);
			$P1Y_AvgPrft = number_format($RST1['P1Y_SumPrft']/$RST1['P1Y_SumQty'] ,2);
		}
		if($RST1['P1Y_SumPrft'] == 0) {
			$PrftCls = "";
			$PrftTxt = "-";
		} elseif($RST1['P1Y_SumPrft'] < 0) {
			$PrftCls = "text-danger";
			if($RST1['P1Y_SumPrce'] > 0) {
				$PrftTxt = number_format(($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])*100,2)."%";
			} else {
				$PrftTxt = "-";
			}
		} else {
			if($RST1['P1Y_SumPrce'] != 0) {
				if(($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])*100 >= 25.00) {
					$PrftCls = "table-success text-success";
				} elseif(($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])*100 >= 0.00 && ($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])*100 <= 24.99) {
					$PrftCls = "text-danger";
				} else {
					$PrftCls = "table-danger text-danger";
				}
				$PrftTxt = number_format(($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])*100,2)."%";
			} else {
				$PrftCls = "";
				$PrftTxt = "-";
			}
		}

		$P1Y_SumAllCost = $P1Y_SumAllCost + $RST1['P1Y_SumCost'];
		$P1Y_SumAllPrce = $P1Y_SumAllPrce + $RST1['P1Y_SumPrce'];
		$P1Y_SumAllPrft = $P1Y_SumAllPrft + $RST1['P1Y_SumPrft'];

		/* TAB 4 */
		if($RST1['P1Y_SumQty'] == 0) { $P1Y_SumQty = "-"; } else { $P1Y_SumQty = number_format($RST1['P1Y_SumQty'],0); }
		if($RST1['P1Y_QcQty'] == 0) { $P1Y_QcQty = "-"; } else { $P1Y_QcQty = number_format($RST1['P1Y_QcQty'],0); }
		if($RST1['P1Y_ClaimQty'] == 0) { $P1Y_ClaimQty = "-"; } else { $P1Y_ClaimQty = number_format($RST1['P1Y_ClaimQty'],0); }
		if($RST1['P1Y_ClaimPrc'] == 0) { $P1Y_ClaimPrc = "-"; } else { $P1Y_ClaimPrc = number_format($RST1['P1Y_ClaimPrc'],2); }
		if($RST1['WPStockQty'] == 0) { $WPStockQty = "-"; } else { $WPStockQty = number_format($RST1['WPStockQty'],0); }
		if($RST1['WPStockValue'] == 0) { $WPStockValue = "-"; } else { $WPStockValue = number_format($RST1['WPStockValue'],2); }
		if($RST1['P1Y_SumQty'] > 0) {
			$P1Y_ClaimPct = ($RST1['P1Y_ClaimQty'] / $RST1['P1Y_SumQty']) * 100;
		} else {
			$P1Y_ClaimPct = 0;
		}
		if($P1Y_ClaimPct > 0) {
			$P1Y_ClaimTxt = number_format($P1Y_ClaimPct,2)."%";
		} else {
			$P1Y_ClaimTxt = "-";
		}

		$P1Y_SumClaimPrc = $P1Y_SumClaimPrc + $RST1['P1Y_ClaimPrc'];
		$WPStockSumValue = $WPStockSumValue + $RST1['WPStockValue'];

		/* TAB 5 */
		if($RST1['CRT_SumCost'] == 0) { $CRT_SumCost = "-"; } else { $CRT_SumCost = number_format($RST1['CRT_SumCost'],2); }
		if($RST1['CRT_SumPrce'] == 0) { $CRT_SumPrce = "-"; } else { $CRT_SumPrce = number_format($RST1['CRT_SumPrce'],2); }
		if($RST1['CRT_SumPrft'] == 0) { $CRT_SumPrft = "-"; } else { $CRT_SumPrft = number_format($RST1['CRT_SumPrft'],2); }
		if($RST1['CRT_SumQty'] <= 0.00) {
			$CRT_AvgCost = "-";
			$CRT_AvgPrce = "-";
			$CRT_AvgPrft = "-";
		} else {
			$CRT_AvgCost = number_format($RST1['CRT_SumCost']/$RST1['CRT_SumQty'] ,2);
			$CRT_AvgPrce = number_format($RST1['CRT_SumPrce']/$RST1['CRT_SumQty'] ,2);
			$CRT_AvgPrft = number_format($RST1['CRT_SumPrft']/$RST1['CRT_SumQty'] ,2);
		}
		if($RST1['CRT_SumPrft'] == 0) {
			$PrftCls = "";
			$PrftTxt = "-";
		} elseif($RST1['CRT_SumPrft'] < 0) {
			$PrftCls = "text-danger";
			if($RST1['CRT_SumPrce'] > 0) {
				$PrftTxt = number_format(($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])*100,2)."%";
			} else {
				$PrftTxt = "-";
			}
		} else {
			if($RST1['CRT_SumPrce'] != 0) {
				if(($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])*100 >= 25.00) {
					$PrftCls = "table-success text-success";
				} elseif(($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])*100 >= 0.00 && ($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])*100 <= 24.99) {
					$PrftCls = "text-danger";
				} else {
					$PrftCls = "table-danger text-danger";
				}
				$PrftTxt = number_format(($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])*100,2)."%";
			} else {
				$PrftCls = "";
				$PrftTxt = "-";
			}
		}


		/* TAB 6 */
		if($RST1['CRT_SumQty'] == 0) { $CRT_SumQty = "-"; } else { $CRT_SumQty = number_format($RST1['CRT_SumQty'],0); }
		if($RST1['CRT_QcQty'] == 0) { $CRT_QcQty = "-"; } else { $CRT_QcQty = number_format($RST1['CRT_QcQty'],0); }
		if($RST1['CRT_ClaimQty'] == 0) { $CRT_ClaimQty = "-"; } else { $CRT_ClaimQty = number_format($RST1['CRT_ClaimQty'],0); }
		if($RST1['CRT_ClaimPrc'] == 0) { $CRT_ClaimPrc = "-"; } else { $CRT_ClaimPrc = number_format($RST1['CRT_ClaimPrc'],2); }
		if($RST1['WPStockQty'] == 0) { $WPStockQty = "-"; } else { $WPStockQty = "<a href='javascript:void(0);' onclick='ViewWhsSub(\"".$ItemCode."\",\"".number_format($RST1['WPStockQty'],0)."\")'>".number_format($RST1['WPStockQty'],0)."</a>"; }
		if($RST1['WPStockValue'] == 0) { $WPStockValue = "-"; } else { $WPStockValue = number_format($RST1['WPStockValue'],2); }
		if($RST1['CRT_SumQty'] > 0) {
			$CRT_ClaimPct = ($RST1['CRT_ClaimQty'] / $RST1['CRT_SumQty']) * 100;
		} else {
			$CRT_ClaimPct = 0;
		}
		if($CRT_ClaimPct > 0) {
			$CRT_ClaimTxt = number_format($CRT_ClaimPct,2)."%";
		} else {
			$CRT_ClaimTxt = "-";
		}

		$CRT_SumClaimPrc = $CRT_SumClaimPrc + $RST1['CRT_ClaimPrc'];

		$TBODY.=
			"<tr>".
				"<td class='text-right'>$no</td>".
				"<td class='text-center'>$ItemCode</td>".
				"<td>$ItemName</td>".
				"<td class='text-center'>$ProductStatus</td>";
		
		switch($tab) {
			case 1:
				$TBODY .=
					"<td class='text-right'>$P2Y_Qty</td>".
					"<td class='text-right'>$P2Y_Prc</td>".
					"<td class='table-info text-right'>$P1Y_Qty</td>".
					"<td class='table-info text-right'>$P1Y_Prc</td>".
					"<td class='text-right'>$CRT_Qty</td>".
					"<td class='text-right'>$CRT_Prc</td>".
					"<td class='text-right'>$CRT_TarQty</td>".
					"<td class='text-right'>$CRT_TarPrc</td>".
					"<td class='text-right table-active fw-bolder $PctCls'>$PctTxt</td>";
				break;
			case 2:
				$TBODY .=
					"<td class='text-right'>$P1Y_SumQty</td>".
					"<td class='text-right'>$P1Y_AvgQty</td>".
					"<td class='text-right'>$CRT_SumQty</td>".
					"<td class='text-right'>$CRT_AvgQty</td>".
					"<td class='text-right'>$StockQty</td>".
					"<td class='text-right'>$StockValue</td>".
					"<td class='text-right'>$P12M_Qty</td>".
					"<td class='text-center fw-bolder $TovCls'>$TovTxt</td>";
				
				break;
			case 3:
				$TBODY .=
					"<td class='text-right'>$P1Y_SumCost</td>".
					"<td class='text-right'>$P1Y_AvgCost</td>".
					"<td class='text-right'>$P1Y_SumPrce</td>".
					"<td class='text-right'>$P1Y_AvgPrce</td>".
					"<td class='text-right'>$P1Y_SumPrft</td>".
					"<td class='text-right'>$P1Y_AvgPrft</td>".
					"<td class='text-right fw-bolder $PrftCls'>$PrftTxt</td>";
				break;
			case 4:
				$TBODY .=
					"<td class='text-right'>$P1Y_SumQty</td>".
					"<td class='text-right'><a href='javascript:void(0);' onclick='ReturnQc(\"$ps1_year\",\"$ItemCode\")'>$P1Y_QcQty</a></td>".
					"<td class='text-right'>$P1Y_ClaimQty</td>".
					"<td class='text-right'>$P1Y_ClaimPrc</td>".
					"<td class='table-active text-center fw-bolder'>$P1Y_ClaimTxt</td>".
					"<td class='text-right'>$WPStockQty</td>".
					"<td class='text-right'>$WPStockValue</td>";
				break;
			case 5:
				$TBODY .=
					"<td class='text-right'>$CRT_SumCost</td>".
					"<td class='text-right'>$CRT_AvgCost</td>".
					"<td class='text-right'>$CRT_SumPrce</td>".
					"<td class='text-right'>$CRT_AvgPrce</td>".
					"<td class='text-right'>$CRT_SumPrft</td>".
					"<td class='text-right'>$CRT_AvgPrft</td>".
					"<td class='text-right fw-bolder $PrftCls'>$PrftTxt</td>";
				break;
			case 6:
				$TBODY .=
					"<td class='text-right'>$CRT_SumQty</td>".
					"<td class='text-right'><a href='javascript:void(0);' onclick='ReturnQc(\"$cnt_year\",\"$ItemCode\")'>$CRT_QcQty</a></td>".
					"<td class='text-right'>$CRT_ClaimQty</td>".
					"<td class='text-right'>$CRT_ClaimPrc</td>".
					"<td class='table-active text-center fw-bolder'>$CRT_ClaimTxt</td>".
					"<td class='text-right'>$WPStockQty</td>".
					"<td class='text-right'>$WPStockValue</td>";
				break;
		}
		$TBODY.= "</tr>";
		$no++;
	}

	if($CRT_SumTarPrc > 0) {
		$CRT_SumTarPct = (($CRT_SumTarPrc - $P1Y_SumPrc) / $CRT_SumTarPrc) * 100;
	} else {
		$CRT_SumTarPct = 0;
	}

	if($P1Y_SumAllPrce > 0) {
		$CRT_SumAllPnct = ($P1Y_SumAllPrft / $P1Y_SumAllPrce) * 100;
	} else {
		$CRT_SumAllPnct = 0;
	}


	$TFOOT = "<tr class='fw-bolder table-active'>";
	switch($tab) {
		case 1:
			$TFOOT .=
				"<td colspan='4' class='text-center'>รวมทั้งหมด</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($P2Y_SumPrc,2)."</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($P1Y_SumPrc,2)."</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($CRT_SumPrc,2)."</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($CRT_SumTarPrc,2)."</td>".
				"<td class='text-right text-success'>".number_format($CRT_SumTarPct,2)."%</td>";
		break;
		case 2:
			$TFOOT .= 
				"<td colspan='4' class='text-center'>รวมทั้งหมด</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($StockSumValue,2)."</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>";
		break;
		case 3:
			$TFOOT .=
				"<td colspan='4' class='text-center'>รวมทั้งหมด</td>".
				"<td class='text-right'>".number_format($P1Y_SumAllCost,2)."</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($P1Y_SumAllPrce,2)."</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($P1Y_SumAllPrft,2)."</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right text-success'>".number_format($CRT_SumAllPnct,2)."%</td>";
		break;
		case 4: 
			$TFOOT .=
				"<td colspan='4' class='text-center'>รวมทั้งหมด</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($P1Y_SumClaimPrc,2)."</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($WPStockSumValue,2)."</td>";
		break;
		case 6:
			$TFOOT .=
				"<td colspan='4' class='text-center'>รวมทั้งหมด</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($CRT_SumClaimPrc,2)."</td>".
				"<td>&nbsp;</td>".
				"<td>&nbsp;</td>".
				"<td class='text-right'>".number_format($WPStockSumValue,2)."</td>";
		break;
	}
	$TFOOT .= "</tr>";
	
	$arrCol['THEAD'] = $THEAD;
	$arrCol['TBODY'] = $TBODY.$TFOOT;
}

if($_GET['p'] == "ReturnQC") {
	$DocYear  = $_POST['DocYear'];
	$ItemCode = $_POST['ItemCode'];

	if($DocYear < 2023) {
		$tbprefix = "KBI_DB2022.dbo.";
	} else {
		$tbprefix = "";
	}

	$SQL1 =
		"SELECT
			T0.DocEntry, T3.U_Dim1 AS 'TeamCode', T3.SlpName, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.DocDate,
			T0.CardCode, T0.CardName, T1.ItemCode, T1.Dscription, T1.WhsCode, T1.Quantity, T1.unitMsr, T1.U_Investigate_QC, T0.Comments,
			T4.Name AS 'ReturnType'
		FROM ".$tbprefix."ORDN T0
		LEFT JOIN ".$tbprefix."RDN1 T1 ON T0.DocEntry = T1.DocEntry
		LEFT JOIN ".$tbprefix."NNM1 T2 ON T0.Series = T2.Series
		LEFT JOIN ".$tbprefix."OSLP T3 ON T0.SlpCode = T3.SlpCode
		LEFT JOIN ".$tbprefix."[@CNREASON] T4 ON T0.U_CNReason2 = T4.Code
		WHERE (T1.ItemCode = '$ItemCode' AND T1.WhsCode IN ('WP4','WP5')) AND (YEAR(T0.DocDate) = $DocYear AND T0.CANCELED = 'N')
		ORDER BY T0.DocDate";
	$ROW1 = ChkRowSAP($SQL1);
	
	if($ROW1 > 0) {
		$QRY1 = SAPSelect($SQL1);
		$TBODY = "";
		$no = 1;
		while($RST1 = odbc_fetch_array($QRY1)) {
			switch(conutf8($RST1['TeamCode'])) {
				case "MT1": $TeamCode = "ฝ่ายขายโมเดิร์นเทรด 1"; break;
				case "MT2": $TeamCode = "ฝ่ายขายโมเดิร์นเทรด 2"; break;
				case "TT1": $TeamCode = "ฝ่ายขายร้านค้า กทม."; break;
				case "TT2": $TeamCode = "ฝ่ายขายร้านค้า ตจว."; break;
				case "OUL": $TeamCode = "ฝ่ายขายหน้าร้าน"; break;
				case "ONL": $TeamCode = "ฝ่ายขายออนไลน์"; break;
			}
			$SlpName    = conutf8($RST1['SlpName']);
			$DocNum     = $RST1['DocNum'];
			$DocDate    = date("d/m/Y",strtotime($RST1['DocDate']));
			$CardName   = conutf8($RST1['CardCode']." - ".$RST1['CardName']);
			$ItemName   = "[".conutf8($RST1['ItemCode']." - ".$RST1['Dscription'])."]";
			$WhsCode    = conutf8($RST1['WhsCode']);

			if($RST1['Quantity'] == 0) { $Quantity = "-"; } else { $Quantity = number_format($RST1['Quantity'],0); }
			$UnitMsr    = conutf8($RST1['unitMsr']);
			$ReturnType = conutf8($RST1['ReturnType']);
			$ReturnQC   = conutf8($RST1['U_Investigate_QC']);

			$TBODY .=
				"<tr>".
					"<td class='text-right'>$no</td>".
					"<td>$TeamCode</td>".
					"<td class='text-center'>$DocNum</td>".
					"<td class='text-center'>$DocDate</td>".
					"<td>$CardName</td>".
					"<td class='text-center'>$WhsCode</td>".
					"<td class='text-right'>$Quantity</td>".
					"<td>$UnitMsr</td>".
					"<td>$ReturnType</td>".
					"<td>$ReturnQC</td>".
				"</tr>";
			$no++;
		}
	} else {
		$ItemName = "";
		$TBODY = "<tr><td class='text-center' colspan='10'>ไม่มีข้อมูลการเคลม</td></tr>";
	}
	$arrCol['ITEMNAME'] = $ItemName;
	$arrCol['TBODY'] = $TBODY;
}

if($_GET['p'] == 'ExportDoc') {
	$cnt_year = date("Y");
	$ps1_year = $cnt_year - 1;
	$ps2_year = $ps1_year - 1;
	$CardCode = $_POST['CardCode'];

	$spreadsheet = new Spreadsheet();
	$spreadsheet->getProperties()
		->setCreator($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setLastModifiedBy($_SESSION['uName']." ".$_SESSION['uLastName'])
		->setTitle("รายงานประเมินซัพพลายเออร์ บจ.คิงบางกอก อินเตอร์เทรด")
		->setSubject("รายงานประเมินซัพพลายเออร์ บจ.คิงบางกอก อินเตอร์เทรด");
	$spreadsheet->getDefaultStyle()->getFont()->setSize(8);

	// Style
	$PageHeader = [
		'font' => [ 'bold' => true, 'size' => 9.1 ],
		'alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]
	];
	$TextCenter = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextRight  = ['alignment' => [ 'horizontal' => \PHPOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PHPOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ]];
	$TextBold  = ['font' => [ 'bold' => true ]];

	$sheet1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'ข้อมูลซัพพลายเออร์');
	$spreadsheet->addSheet($sheet1, 0);

	$sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'ข้อมูลการคืนเคลมสินค้า');
	$spreadsheet->addSheet($sheet2, 1);

	$spreadsheet->setActiveSheetIndexByName('Worksheet');
	$sheetIndex = $spreadsheet->getActiveSheetIndex();
	$spreadsheet->removeSheetByIndex($sheetIndex);

	// Header Data Sheet 1
	$sheet1->setCellValue('A1',"ลำดับ");
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:A2');

	$sheet1->setCellValue('B1',"รหัสสินค้า");
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('B1:B2');

	$sheet1->setCellValue('C1',"ชื่อสินค้า");
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('C1:C2');

	$sheet1->setCellValue('D1',"สถานะ");
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('D1:D2');

	$sheet1->setCellValue('E1',"ยอดซื้อ ".$ps2_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('E1:F1');
	$sheet1->setCellValue('E2',"จำนวน\n(หน่วย)");
	$sheet1->setCellValue('F2',"มูลค่า\n(THB)");

	$sheet1->setCellValue('G1',"ยอดซื้อ ".$ps1_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('G1:H1');
	$sheet1->setCellValue('G2',"จำนวน\n(หน่วย)");
	$sheet1->setCellValue('H2',"มูลค่า\n(THB)");

	$sheet1->setCellValue('I1',"ยอดซื้อจริง ".$cnt_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('I1:J1');
	$sheet1->setCellValue('I2',"จำนวน\n(หน่วย)");
	$sheet1->setCellValue('J2',"มูลค่า\n(THB)");

	$tar_year = (date("m") == 12) ? $cnt_year+1 : $cnt_year;

	$sheet1->setCellValue('K1',"ประมาณการสั่งซื้อ ".$tar_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('K1:M1');
	$sheet1->setCellValue('K2',"จำนวน\n(หน่วย)");
	$sheet1->setCellValue('L2',"มูลค่า\n(THB)");
	$sheet1->setCellValue('M2',"Growth\n(%)");

	$sheet1->setCellValue('N1',"ยอดขาย ".$ps1_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('N1:O1');
	$sheet1->setCellValue('N2',"รวมทั้งหมด\n(หน่วย)");
	$sheet1->setCellValue('O2',"เฉลี่ยต่อเดือน\n(หน่วย)");

	$sheet1->setCellValue('P1',"ยอดขาย ".$cnt_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('P1:Q1');
	$sheet1->setCellValue('P2',"รวมทั้งหมด\n(หน่วย)");
	$sheet1->setCellValue('Q2',"เฉลี่ยต่อเดือน\n(หน่วย)");

	$sheet1->setCellValue('R1',"สินค้าคงคลัง ".date("d/m/Y"));
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('R1:U1');
	$sheet1->setCellValue('R2',"จำนวนคงคลัง\n(หน่วย)");
	$sheet1->setCellValue('S2',"มูลค่าคงคลัง\n(THB)");
	$sheet1->setCellValue('T2',"ยอดขายเฉลี่ย\n12 เดือน (หน่วย)");
	$sheet1->setCellValue('U2',"T/O\n(เดือน)");

	$sheet1->setCellValue('V1',"วิเคราะห์การขาย ".$ps1_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('V1:AB1');
	$sheet1->setCellValue('V2',"ต้นทุนขายรวม\n(VAT) (THB)");
	$sheet1->setCellValue('W2',"ต้นทุนขายเฉลี่ย/ตัว\n(VAT) (THB)");
	$sheet1->setCellValue('X2',"ราคาขายรวม\n(VAT) (THB)");
	$sheet1->setCellValue('Y2',"ราคาขายเฉลี่ย/ตัว\n(VAT) (THB)");
	$sheet1->setCellValue('Z2',"กำไรรวม\n(THB)");
	$sheet1->setCellValue('AA2',"กำไรเฉลี่ย/ตัว\n(VAT) (THB)");
	$sheet1->setCellValue('AB2',"% of GP");

	$sheet1->setCellValue('AC1',"ข้อมูลการเคลมสินค้า ".$ps1_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('AC1:AI1');
	$sheet1->setCellValue('AC2',"ยอดขาย ".$ps1_year."\n(หน่วย)");
	$sheet1->setCellValue('AD2',"คืนเพื่อเคลมซัพฯ\n".$ps1_year." (หน่วย)");
	$sheet1->setCellValue('AE2',"เคลมซัพฯ แล้ว\n".$ps1_year." (หน่วย)");
	$sheet1->setCellValue('AF2',"มูลค่าการเคลม\n".$ps1_year." (THB)");
	$sheet1->setCellValue('AG2',"% การเคลม\n".$ps1_year);
	$sheet1->setCellValue('AH2',"รอเคลมซัพฯ\n(หน่วย)");
	$sheet1->setCellValue('AI2',"มูลค่ารอเคลมซัพฯ\n(THB)");

	$sheet1->setCellValue('AJ1',"วิเคราะห์การขาย ".$cnt_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('AJ1:AP1');
	$sheet1->setCellValue('AJ2',"ต้นทุนขายรวม\n(VAT) (THB)");
	$sheet1->setCellValue('AK2',"ต้นทุนขายเฉลี่ย/ตัว\n(VAT) (THB)");
	$sheet1->setCellValue('AL2',"ราคาขายรวม\n(VAT) (THB)");
	$sheet1->setCellValue('AM2',"ราคาขายเฉลี่ย/ตัว\n(VAT) (THB)");
	$sheet1->setCellValue('AN2',"กำไรรวม\n(THB)");
	$sheet1->setCellValue('AO2',"กำไรเฉลี่ย/ตัว\n(VAT) (THB)");
	$sheet1->setCellValue('AP2',"% of GP");

	$sheet1->setCellValue('AQ1',"ข้อมูลการเคลมสินค้า ".$cnt_year);
	$spreadsheet->setActiveSheetIndex(0)->mergeCells('AQ1:AW1');
	$sheet1->setCellValue('AQ2',"ยอดขาย ".$cnt_year."\n(หน่วย)");
	$sheet1->setCellValue('AR2',"คืนเพื่อเคลมซัพฯ\n".$cnt_year." (หน่วย)");
	$sheet1->setCellValue('AS2',"เคลมซัพฯ แล้ว\n".$cnt_year." (หน่วย)");
	$sheet1->setCellValue('AT2',"มูลค่าการเคลม\n".$cnt_year." (THB)");
	$sheet1->setCellValue('AU2',"% การเคลม\n".$cnt_year);
	$sheet1->setCellValue('AV2',"รอเคลมซัพฯ\n(หน่วย)");
	$sheet1->setCellValue('AW2',"มูลค่ารอเคลมซัพฯ\n(THB)");

	// Add Style Sheet 1
	$sheet1->getStyle('A1:AQ1')->applyFromArray($PageHeader);
	$sheet1->getStyle('E2:AW2')->applyFromArray($PageHeader);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(6);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(40);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(9);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('T')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('U')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('V')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('W')->setWidth(18);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('X')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('Y')->setWidth(17);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('Z')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AA')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AB')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AC')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AD')->setWidth(18);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AE')->setWidth(18);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AF')->setWidth(18);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AG')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AH')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AI')->setWidth(18);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AJ')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AK')->setWidth(18);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AL')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AM')->setWidth(17);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AN')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AO')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AP')->setWidth(12);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AQ')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AR')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AS')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AT')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AU')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AV')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('AW')->setWidth(18);

	// Add Data Sheet 1
	$MinSQL = "";
	for($m=1; $m <= date("m"); $m++) {
		if($m < 10) {
			$MinSQL .= "(R0.M_0$m)";
		} else {
			$MinSQL .= "(R0.M_$m)";
		}

		if($m != date("m")) {
			$MinSQL .= ", ";
		}
	}

	if($cnt_year == 2023) {
		$tbprefix = "KBI_DB2022.dbo.";
	} else {
		$tbprefix = NULL;
	}
	$SQL1 = 
		"SELECT
			/* SECTION 0 */
			B0.ItemCode, B0.ItemName, B0.ProductStatus,
			/* SECTION 1 */
			B0.P2Y_Qty, B0.P2Y_Prc,
			B0.P1Y_Qty, B0.P1Y_Prc,
			CASE WHEN MONTH(GETDATE()) = 12 THEN ((B0.CRT_SumQty-B0.CRT_MinQty)/11)*14 ELSE ((B0.P1Y_SumQty-B0.P1Y_MinQty)/11)*14 END AS 'CRT_TarQty',
			(B0.CRT_TarPrc * CASE WHEN MONTH(GETDATE()) = 12 THEN ((B0.CRT_SumQty-B0.CRT_MinQty)/11)*14 ELSE ((B0.P1Y_SumQty-B0.P1Y_MinQty)/11)*14 END) AS 'CRT_TarPrc',
			B0.CRT_Qty, B0.CRT_Prc,
			/* SECTION 2 */
			((B0.P1Y_SumQty-B0.P1Y_MinQty)/11) AS 'P1Y_AvgQty', B0.P1Y_SumQty,
			((B0.CRT_SumQty-B0.CRT_MinQty)/CASE WHEN MONTH(GETDATE()) = 1 THEN 1 ELSE MONTH(GETDATE()) -1 END) AS 'CRT_AvgQty', B0.CRT_SumQty,
			B0.StockQty, (B0.StockQty * B0.CRT_TarPrc) AS 'StockValue',
			(B0.P12M_Qty/12) AS 'P12M_Qty',
			/* SECTION 3 */
			B0.P1Y_SumCost,
			B0.P1Y_SumPrce,
			(B0.P1Y_SumPrce - B0.P1Y_SumCost) AS 'P1Y_SumPrft',
			/* SECTION 4 */
			B0.CRT_SumCost,
			B0.CRT_SumPrce,
			(B0.CRT_SumPrce - B0.CRT_SumCost) AS 'CRT_SumPrft',
			/* SECTION 5 */
			B0.P1Y_QcQty, B0.P1Y_ClaimQty, (B0.P1Y_ClaimQty * B0.CRT_TarPrc) AS 'P1Y_ClaimPrc',
			B0.CRT_QcQty, B0.CRT_ClaimQty, (B0.CRT_ClaimQty * B0.CRT_TarPrc) AS 'CRT_ClaimPrc',
			B0.WPStockQty, (B0.WPStockQty * B0.CRT_TarPrc) AS 'WPStockValue'
		FROM (
			SELECT
			A0.ItemCode, A1.ItemName, A1.U_ProductStatus AS 'ProductStatus',
			SUM(A0.P2Y_Qty) AS 'P2Y_Qty', SUM(A0.P2Y_Prc) AS 'P2Y_Prc',
			SUM(A0.P1Y_Qty) AS 'P1Y_Qty', SUM(A0.P1Y_Prc) AS 'P1Y_Prc',
			SUM(A0.CRT_Qty) AS 'CRT_Qty', SUM(A0.CRT_Prc) AS 'CRT_Prc',
			ISNULL((
				SELECT SUM(P0.OnHand) FROM OITW P0 WHERE P0.ItemCode = A0.ItemCode AND P0.WhsCode IN ('KSY','KSM','MT','MT2','TT-C','OUL','KB4','PLA')
			),0) AS 'StockQty',
			ISNULL((
				SELECT SUM(P0.OnHand) FROM OITW P0 WHERE P0.ItemCode = A0.ItemCode AND P0.WhsCode IN ('WP4','WP5')
			),0) AS 'WPStockQty',
			/* CURRENT YEAR PURCHASE TARGET (PRICE/QTY) */
			ISNULL(
				CASE
					WHEN A1.LastPurDat = '2022-12-31'
					THEN (SELECT TOP 1 P0.PriceAfVat FROM KBI_DB2022.dbo.PDN1 P0 WHERE P0.ItemCode = A0.ItemCode AND P0.PriceAfVat > 0 ORDER BY P0.DocEntry DESC)
					ELSE ISNULL((SELECT TOP 1 P0.PriceAfVat FROM PDN1 P0 WHERE P0.ItemCode = A0.ItemCode AND P0.PriceAfVat > 0 ORDER BY P0.DocEntry DESC), A1.LastPurPrc)
				END
			,0) AS 'CRT_TarPrc',
			/* PAST 1 YEAR SALES DATA (MIN QTY) */
			ISNULL((
				SELECT
					(
						SELECT MIN(MinQty)
						FROM (VALUES (R0.M_01), (R0.M_02), (R0.M_03), (R0.M_04), (R0.M_05), (R0.M_06), (R0.M_07), (R0.M_08), (R0.M_09), (R0.M_10), (R0.M_11), (R0.M_12)) AS x (MinQty)
					) AS 'MinQty'
				FROM (
				SELECT
					SUM(Q0.M_01) AS 'M_01', SUM(Q0.M_02) AS 'M_02', SUM(Q0.M_03) AS 'M_03', SUM(Q0.M_04) AS 'M_04',
					SUM(Q0.M_05) AS 'M_05', SUM(Q0.M_06) AS 'M_06', SUM(Q0.M_07) AS 'M_07', SUM(Q0.M_08) AS 'M_08',
					SUM(Q0.M_09) AS 'M_09', SUM(Q0.M_10) AS 'M_10', SUM(Q0.M_11) AS 'M_11', SUM(Q0.M_12) AS 'M_12'
				FROM (
						SELECT
							CASE WHEN MONTH(P1.DocDate) = 1 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(P1.DocDate) = 2 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(P1.DocDate) = 3 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(P1.DocDate) = 4 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(P1.DocDate) = 5 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(P1.DocDate) = 6 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(P1.DocDate) = 7 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(P1.DocDate) = 8 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(P1.DocDate) = 9 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(P1.DocDate) = 10 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(P1.DocDate) = 11 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(P1.DocDate) = 12 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_12'
						FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' GROUP BY P1.DocDate
						UNION ALL
						SELECT
							CASE WHEN MONTH(P1.DocDate) = 1 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(P1.DocDate) = 2 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(P1.DocDate) = 3 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(P1.DocDate) = 4 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(P1.DocDate) = 5 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(P1.DocDate) = 6 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(P1.DocDate) = 7 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(P1.DocDate) = 8 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(P1.DocDate) = 9 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(P1.DocDate) = 10 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(P1.DocDate) = 11 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(P1.DocDate) = 12 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_12'
						FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' GROUP BY P1.DocDate
					) Q0
				) R0
			),0) AS 'P1Y_MinQty',
			/* CURRENT YEAR SALES DATA (SUM QTY) */
			ISNULL((
				SELECT
					SUM(Q0.Quantity) AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'P1Y_SumQty',
			/* PAST 1 YEAR SALES DATA (MIN QTY) */
			ISNULL((
				SELECT
					(
						SELECT MIN(MinQty)
						FROM (VALUES $MinSQL) AS x (MinQty)
					) AS 'MinQty'
				FROM (
				SELECT
					SUM(Q0.M_01) AS 'M_01', SUM(Q0.M_02) AS 'M_02', SUM(Q0.M_03) AS 'M_03', SUM(Q0.M_04) AS 'M_04',
					SUM(Q0.M_05) AS 'M_05', SUM(Q0.M_06) AS 'M_06', SUM(Q0.M_07) AS 'M_07', SUM(Q0.M_08) AS 'M_08',
					SUM(Q0.M_09) AS 'M_09', SUM(Q0.M_10) AS 'M_10', SUM(Q0.M_11) AS 'M_11', SUM(Q0.M_12) AS 'M_12'
				FROM (
						SELECT
							CASE WHEN MONTH(P1.DocDate) = 1 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(P1.DocDate) = 2 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(P1.DocDate) = 3 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(P1.DocDate) = 4 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(P1.DocDate) = 5 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(P1.DocDate) = 6 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(P1.DocDate) = 7 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(P1.DocDate) = 8 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(P1.DocDate) = 9 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(P1.DocDate) = 10 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(P1.DocDate) = 11 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(P1.DocDate) = 12 THEN SUM(P0.Quantity) ELSE 0 END AS 'M_12'
						FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' GROUP BY P1.DocDate
						UNION ALL
						SELECT
							CASE WHEN MONTH(P1.DocDate) = 1 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_01',
							CASE WHEN MONTH(P1.DocDate) = 2 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_02',
							CASE WHEN MONTH(P1.DocDate) = 3 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_03',
							CASE WHEN MONTH(P1.DocDate) = 4 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_04',
							CASE WHEN MONTH(P1.DocDate) = 5 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_05',
							CASE WHEN MONTH(P1.DocDate) = 6 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_06',
							CASE WHEN MONTH(P1.DocDate) = 7 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_07',
							CASE WHEN MONTH(P1.DocDate) = 8 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_08',
							CASE WHEN MONTH(P1.DocDate) = 9 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_09',
							CASE WHEN MONTH(P1.DocDate) = 10 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_10',
							CASE WHEN MONTH(P1.DocDate) = 11 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_11',
							CASE WHEN MONTH(P1.DocDate) = 12 THEN -SUM(P0.Quantity) ELSE 0 END AS 'M_12'
						FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' GROUP BY P1.DocDate
					) Q0
				) R0
			),0) AS 'CRT_MinQty',
			/* CURRENT YEAR SALES DATA (SUM QTY) */
			ISNULL((
				SELECT
					SUM(Q0.Quantity) AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Quantity) AS 'Quantity' FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'CRT_SumQty',

			/* LAST 12 MONTH (QTY) */
			ISNULL((
				SELECT
					SUM(Q0.Quantity) AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE P0.ItemCode = A0.ItemCode AND P0.DocDate > DATEADD(m,-12,GETDATE()) AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Quantity) AS 'Quantity' FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE P0.ItemCode = A0.ItemCode AND P0.DocDate > DATEADD(m,-12,GETDATE()) AND P1.CANCELED = 'N'
					UNION ALL
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE P0.ItemCode = A0.ItemCode AND P0.DocDate > DATEADD(m,-12,GETDATE()) AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE P0.ItemCode = A0.ItemCode AND P0.DocDate > DATEADD(m,-12,GETDATE()) AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'P12M_Qty',

			/* PAST 1 YEAR SALE ANALYSE (COST) */
			ISNULL((
				SELECT
					SUM(Q0.GrossBuyPr) AS 'GrossBuyPr'
				FROM (
					SELECT SUM(P0.GrossBuyPr * P0.Quantity) AS 'GrossBuyPr' FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.GrossBuyPr * P0.Quantity) AS 'GrossBuyPr' FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'P1Y_SumCost',

			/* PAST 1 YEAR SALE ANALYSE (PRICE) */
			ISNULL((
				SELECT
					SUM(Q0.Price) AS 'Price'
				FROM (
					SELECT SUM(P0.Price * P0.Quantity) AS 'Price' FROM ".$tbprefix."INV1 P0 LEFT JOIN ".$tbprefix."OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					UNION ALL
					SELECT -SUM(P0.Price * P0.Quantity) AS 'Price' FROM ".$tbprefix."RIN1 P0 LEFT JOIN ".$tbprefix."ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
				) Q0
			),0) AS 'P1Y_SumPrce',

			/* PAST 1 YEAR RETURN (QTY) */
			ISNULL((
				SELECT
					Q0.Quantity AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."RDN1 P0 LEFT JOIN ".$tbprefix."ORDN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' AND P0.WhsCode IN ('WP4','WP5')
				) Q0
			),0) AS 'P1Y_QcQty',
			/* PAST 1 YEAR CLAIM (QTY) */
			ISNULL((
				SELECT
					Q0.Quantity AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM ".$tbprefix."RPD1 P0 LEFT JOIN ".$tbprefix."ORPD P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $ps1_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' AND P0.WhsCode IN ('WP4','WP5')
				) Q0
			),0) AS 'P1Y_ClaimQty',

			/* CURRENT YEAR SALE ANALYSE (COST) */
				ISNULL((
					SELECT
						SUM(Q0.GrossBuyPr) AS 'GrossBuyPr'
					FROM (
						SELECT SUM(P0.GrossBuyPr * P0.Quantity) AS 'GrossBuyPr' FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
						UNION ALL
						SELECT -SUM(P0.GrossBuyPr * P0.Quantity) AS 'GrossBuyPr' FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					) Q0
				),0) AS 'CRT_SumCost',

				/* CURRENT YEAR SALE ANALYSE (PRICE) */
				ISNULL((
					SELECT
						SUM(Q0.Price) AS 'Price'
					FROM (
						SELECT SUM(P0.Price * P0.Quantity) AS 'Price' FROM INV1 P0 LEFT JOIN OINV P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
						UNION ALL
						SELECT -SUM(P0.Price * P0.Quantity) AS 'Price' FROM RIN1 P0 LEFT JOIN ORIN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N'
					) Q0
				),0) AS 'CRT_SumPrce',

			/* CURRENT YEAR RETURN (QTY) */
			ISNULL((
				SELECT
					Q0.Quantity AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM RDN1 P0 LEFT JOIN ORDN P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' AND P0.WhsCode IN ('WP4','WP5')
				) Q0
			),0) AS 'CRT_QcQty',
			/* CURRENT YEAR CLAIM (QTY) */
			ISNULL((
				SELECT
					Q0.Quantity AS 'Quantity'
				FROM (
					SELECT SUM(P0.Quantity) AS 'Quantity' FROM RPD1 P0 LEFT JOIN ORPD P1 ON P0.DocEntry = P1.DocEntry WHERE YEAR(P1.DocDate) = $cnt_year AND P0.ItemCode = A0.ItemCode AND P1.CANCELED = 'N' AND P0.WhsCode IN ('WP4','WP5')
				) Q0
			),0) AS 'CRT_ClaimQty'
		FROM (
			SELECT DISTINCT
				T0.ItemCode,
				SUM(CASE WHEN YEAR(T1.DocDate) = $ps2_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'P2Y_Qty',
				SUM(CASE WHEN YEAR(T1.DocDate) = $ps2_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'P2Y_Prc',
				SUM(CASE WHEN YEAR(T1.DocDate) = $ps1_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'P1Y_Qty',
				SUM(CASE WHEN YEAR(T1.DocDate) = $ps1_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'P1Y_Prc',
				SUM(CASE WHEN YEAR(T1.DocDate) = $cnt_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'CRT_Qty',
				SUM(CASE WHEN YEAR(T1.DocDate) = $cnt_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'CRT_Prc'
			FROM PDN1 T0
			LEFT JOIN OPDN T1 ON T0.DocEntry = T1.DocEntry
			WHERE T1.CardCode = '$CardCode' AND YEAR(T1.DocDate) BETWEEN $ps2_year AND $cnt_year AND T1.CANCELED = 'N'
			GROUP BY T0.ItemCode
			UNION ALL
			SELECT DISTINCT
				T0.ItemCode,
				SUM(CASE WHEN YEAR(T1.DocDate) = $ps2_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'P2Y_Qty',
				SUM(CASE WHEN YEAR(T1.DocDate) = $ps2_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'P2Y_Prc',
				SUM(CASE WHEN YEAR(T1.DocDate) = $ps1_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'P1Y_Qty',
				SUM(CASE WHEN YEAR(T1.DocDate) = $ps1_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'P1Y_Prc',
				SUM(CASE WHEN YEAR(T1.DocDate) = $cnt_year THEN CASE WHEN T0.Quantity = 0 THEN 1 ELSE T0.Quantity END ELSE 0 END) AS 'CRT_Qty',
				SUM(CASE WHEN YEAR(T1.DocDate) = $cnt_year THEN CASE WHEN T0.Quantity = 0 THEN 1 * T0.PriceAfVAT ELSE T0.Quantity * T0.PriceAfVAT END ELSE 0 END) AS 'CRT_Prc'
			FROM KBI_DB2022.dbo.PDN1 T0
			LEFT JOIN KBI_DB2022.dbo.OPDN T1 ON T0.DocEntry = T1.DocEntry
			WHERE T1.CardCode = '$CardCode' AND YEAR(T1.DocDate) BETWEEN $ps2_year AND $cnt_year AND T1.CANCELED = 'N'
			GROUP BY T0.ItemCode
		) A0
		LEFT JOIN OITM A1 ON A0.ItemCode = A1.ItemCode
		WHERE A0.ItemCode != '00-999-999'
		GROUP BY A0.ItemCode, A1.ItemName, A1.U_ProductStatus, A1.LastPurDat, A1.LastPurPrc
	) B0
	ORDER BY B0.P1Y_Prc DESC, B0.P1Y_Qty DESC, B0.ItemCode ASC";

	$ItemArr = array();

	$QRY1 = SAPSelect($SQL1);
	$Row = 2; $No = 0;
	while($RST1 = odbc_fetch_array($QRY1)) {
		array_push($ItemArr,$RST1['ItemCode']);

		$Row++; $No++;
		// ลำดับ
		$sheet1->setCellValue('A'.$Row,$No);
		$sheet1->getStyle('A'.$Row)->applyFromArray($TextCenter);
		// รหัสสินค้า
		$sheet1->setCellValue('B'.$Row,$RST1['ItemCode']);
		$sheet1->getStyle('B'.$Row)->applyFromArray($TextCenter);
		// ชื่อสินค้า
		$sheet1->setCellValue('C'.$Row,conutf8($RST1['ItemName']));
		// สถานะ
		$sheet1->setCellValue('D'.$Row,conutf8($RST1['ProductStatus']));
		$sheet1->getStyle('D'.$Row)->applyFromArray($TextCenter);
		// ยอดซื้อ 2021 
			// จำนวน (หน่วย)
			if($RST1['P2Y_Qty'] == 0) { 
				$sheet1->setCellValue('E'.$Row,0); 
			} else { 
				$sheet1->setCellValue('E'.$Row,$RST1['P2Y_Qty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$Row)->getNumberFormat()->setFormatCode("#,##0");
			}
			$sheet1->getStyle('E'.$Row)->applyFromArray($TextRight);
			// จำนวน (THB)
			if($RST1['P2Y_Prc'] == 0) { 
				$sheet1->setCellValue('F'.$Row,0); 
			} else { 
				$sheet1->setCellValue('F'.$Row,$RST1['P2Y_Prc']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
			}
			$sheet1->getStyle('F'.$Row)->applyFromArray($TextRight);
		// ยอดซื้อ 2022 
			// จำนวน (หน่วย)
			if($RST1['P1Y_Qty'] == 0) { 
				$sheet1->setCellValue('G'.$Row,0); 
			} else { 
				$sheet1->setCellValue('G'.$Row,$RST1['P1Y_Qty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$Row)->getNumberFormat()->setFormatCode("#,##0");
			}
			$sheet1->getStyle('G'.$Row)->applyFromArray($TextRight);
			// จำนวน (THB)
			if($RST1['P1Y_Prc'] == 0) { 
				$sheet1->setCellValue('H'.$Row,0); 
			} else { 
				$sheet1->setCellValue('H'.$Row,$RST1['P1Y_Prc']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
			}
			$sheet1->getStyle('H'.$Row)->applyFromArray($TextRight);
		// ยอดซื้อจริง 2023
			// จำนวน (หน่วย)
			if($RST1['CRT_Qty'] == 0) { 
				$sheet1->setCellValue('I'.$Row,0); 
			} else { 
				$sheet1->setCellValue('I'.$Row,$RST1['CRT_Qty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");
			}
			$sheet1->getStyle('I'.$Row)->applyFromArray($TextRight);
			// จำนวน (THB)
			if($RST1['CRT_Prc'] == 0) { 
				$sheet1->setCellValue('J'.$Row,0); 
			} else { 
				$sheet1->setCellValue('J'.$Row,$RST1['CRT_Prc']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
			}
			$sheet1->getStyle('J'.$Row)->applyFromArray($TextRight);
		// ประมาณการสั่งซื้อ 2023
			// จำนวน (หน่วย)
			if($RST1['CRT_TarQty'] == 0) { 
				$sheet1->setCellValue('K'.$Row,0); 
			} else { 
				$sheet1->setCellValue('K'.$Row,$RST1['CRT_TarQty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$Row)->getNumberFormat()->setFormatCode("#,##0");
			}
			$sheet1->getStyle('K'.$Row)->applyFromArray($TextRight);
			// จำนวน (THB)
			if($RST1['CRT_TarPrc'] == 0) { 
				$sheet1->setCellValue('L'.$Row,0); 
			} else { 
				$sheet1->setCellValue('L'.$Row,$RST1['CRT_TarPrc']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");
			}
			$sheet1->getStyle('L'.$Row)->applyFromArray($TextRight);
			// Growth (%)
			if($RST1['CRT_TarQty'] > 0) {
				$TarPct = (date("m") == 12) ? (($RST1['CRT_TarQty'] - $RST1['CRT_Qty']) / $RST1['CRT_TarQty']) : (($RST1['CRT_TarQty'] - $RST1['P1Y_Qty']) / $RST1['CRT_TarQty']);
			} else {
				$TarPct = 0;
			}
			if($TarPct == 0) {
				$sheet1->setCellValue('M'.$Row,0); 
			} elseif($TarPct > 0) {
				$sheet1->setCellValue('M'.$Row,$TarPct);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('M'.$Row)->getFont()->getColor()->setARGB('ff198754');
			} else {
				$sheet1->setCellValue('M'.$Row,$TarPct);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('M'.$Row)->getFont()->getColor()->setARGB('ffdc3545');
			}
			$spreadsheet->setActiveSheetIndex(0)->getStyle('M'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
			$sheet1->getStyle('M'.$Row)->applyFromArray($TextRight);
		// ยอดขาย 2022
			// รวมทั้งหมด (หน่วย)
			if($RST1['P1Y_SumQty'] == 0) { 
				$sheet1->setCellValue('N'.$Row,0); 
			} else { 
				$sheet1->setCellValue('N'.$Row,$RST1['P1Y_SumQty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('N'.$Row)->getNumberFormat()->setFormatCode("#,##0");
			}
			$sheet1->getStyle('N'.$Row)->applyFromArray($TextRight);
			// เฉลี่ยต่อเดือน (หน่วย)
			if($RST1['P1Y_AvgQty'] == 0) { 
				$sheet1->setCellValue('O'.$Row,0);  
			} else { 
				$sheet1->setCellValue('O'.$Row,$RST1['P1Y_AvgQty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('O'.$Row)->getNumberFormat()->setFormatCode("#,##0");
			}
			$sheet1->getStyle('O'.$Row)->applyFromArray($TextRight);
		// ยอดขาย 2023
			// รวมทั้งหมด (หน่วย)
			if($RST1['CRT_SumQty'] == 0) { 
				$sheet1->setCellValue('P'.$Row,0);  
			} else { 
				$sheet1->setCellValue('P'.$Row,$RST1['CRT_SumQty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('P'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
			}
			$sheet1->getStyle('P'.$Row)->applyFromArray($TextRight);
			// เฉลี่ยต่อเดือน (หน่วย)
			if($RST1['CRT_AvgQty'] == 0) { 
				$sheet1->setCellValue('Q'.$Row,0);  
			} else { 
				$sheet1->setCellValue('Q'.$Row,$RST1['CRT_AvgQty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('Q'.$Row)->getNumberFormat()->setFormatCode("#,##0");
			}
			$sheet1->getStyle('Q'.$Row)->applyFromArray($TextRight);
		// สินค้าคงคลัง 29/05/2023
			// จำนวนคงคลัง (หน่วย)
			if($RST1['StockQty'] == 0) { 
				$sheet1->setCellValue('R'.$Row,0);  
			} else { 
				$sheet1->setCellValue('R'.$Row,$RST1['StockQty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('R'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
			}
			$sheet1->getStyle('R'.$Row)->applyFromArray($TextRight);
			// มูลค่าคงคลัง (THB)
			if($RST1['StockValue'] == 0) { 
				$sheet1->setCellValue('S'.$Row,0);  
			} else { 
				$sheet1->setCellValue('S'.$Row,$RST1['StockValue']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('S'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('S'.$Row)->applyFromArray($TextRight);
			// ยอดขายเฉลี่ย 12 เดือน (หน่วย)
			if($RST1['P12M_Qty'] == 0) { 
				$sheet1->setCellValue('T'.$Row,0);  
			} else { 
				$sheet1->setCellValue('T'.$Row,$RST1['P12M_Qty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('T'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
			}
			$sheet1->getStyle('T'.$Row)->applyFromArray($TextRight);
			// T/O (เดือน)
			if($RST1['P12M_Qty'] > 0) {
				$TOV = ($RST1['StockQty']/$RST1['P12M_Qty']);
			} else {
				$TOV = "DEAD";
			}
			if($TOV == "DEAD") {
				$sheet1->setCellValue('U'.$Row,"DEAD");  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('U'.$Row)->getFont()->getColor()->setARGB('ffdc3545');
			} elseif($TOV > 0) {
				$sheet1->setCellValue('U'.$Row,$TOV);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('U'.$Row)->getNumberFormat()->setFormatCode("#,##0.0"); 
				if($TOV <= 4) {
					$spreadsheet->setActiveSheetIndex(0)->getStyle('U'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fffff3cd');
					$spreadsheet->setActiveSheetIndex(0)->getStyle('U'.$Row)->getFont()->getColor()->setARGB('ff967102');
				} elseif($TOV <= 6) {
					$spreadsheet->setActiveSheetIndex(0)->getStyle('U'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
					$spreadsheet->setActiveSheetIndex(0)->getStyle('U'.$Row)->getFont()->getColor()->setARGB('ff198754');
				} else {
					$spreadsheet->setActiveSheetIndex(0)->getStyle('U'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff8d7da');
					$spreadsheet->setActiveSheetIndex(0)->getStyle('U'.$Row)->getFont()->getColor()->setARGB('ffdc3545');
				}
			}
			$sheet1->getStyle('U'.$Row)->applyFromArray($TextCenter);
		// วิเคราะห์การขาย 2022
			// ต้นทุนขายรวม (VAT) (THB)
			if($RST1['P1Y_SumCost'] == 0) { 
				$sheet1->setCellValue('V'.$Row,0);  
			} else { 
				$sheet1->setCellValue('V'.$Row,$RST1['P1Y_SumCost']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('V'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('V'.$Row)->applyFromArray($TextRight);
			// ต้นทุนขายเฉลี่ย/ตัว (VAT) (THB)
			if($RST1['P1Y_SumQty'] <= 0.00) {
				$sheet1->setCellValue('W'.$Row,0);  
			} else {
				$sheet1->setCellValue('W'.$Row,($RST1['P1Y_SumCost']/$RST1['P1Y_SumQty']));  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('W'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('W'.$Row)->applyFromArray($TextRight);
			// ราคาขายรวม (VAT) (THB)
			if($RST1['P1Y_SumPrce'] == 0) { 
				$sheet1->setCellValue('X'.$Row,0);  
			} else { 
				$sheet1->setCellValue('X'.$Row,$RST1['P1Y_SumPrce']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('X'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");  
			}
			$sheet1->getStyle('X'.$Row)->applyFromArray($TextRight);
			// ราคาขายเฉลี่ย/ตัว (VAT) (THB)
			if($RST1['P1Y_SumQty'] <= 0.00) {
				$sheet1->setCellValue('Y'.$Row,0);  
			} else {
				$sheet1->setCellValue('Y'.$Row,($RST1['P1Y_SumPrce']/$RST1['P1Y_SumQty']));  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('Y'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('Y'.$Row)->applyFromArray($TextRight);
			// กำไรรวม (THB)
			if($RST1['P1Y_SumPrft'] == 0) { 
				$sheet1->setCellValue('Z'.$Row,0);  
			} else {  
				$sheet1->setCellValue('Z'.$Row,$RST1['P1Y_SumPrft']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('Z'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('Z'.$Row)->applyFromArray($TextRight);
			// กำไรเฉลี่ย/ตัว (VAT) (THB)
			if($RST1['P1Y_SumQty'] <= 0.00) {
				$sheet1->setCellValue('AA'.$Row,0);  
			} else {
				$sheet1->setCellValue('AA'.$Row,($RST1['P1Y_SumPrft']/$RST1['P1Y_SumQty']));  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AA'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('AA'.$Row)->applyFromArray($TextRight);
			// % of GP
			if($RST1['P1Y_SumPrft'] == 0) {
				$sheet1->setCellValue('AB'.$Row,0);  
			} elseif($RST1['P1Y_SumPrft'] < 0) {
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AB'.$Row)->getFont()->getColor()->setARGB('ff9a1118');
				if($RST1['P1Y_SumPrce'] > 0) {
					$sheet1->setCellValue('AB'.$Row,(($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])));  
					$spreadsheet->setActiveSheetIndex(0)->getStyle('AB'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
				} else {
					$sheet1->setCellValue('AB'.$Row,0);  
				}
			} else {
				if($RST1['P1Y_SumPrce'] != 0) {
					$sheet1->setCellValue('AB'.$Row,(($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])));  
					$spreadsheet->setActiveSheetIndex(0)->getStyle('AB'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
					if(($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])*100 >= 25.00) {
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AB'.$Row)->getFont()->getColor()->setARGB('ff198754');
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AB'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
					} elseif(($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])*100 >= 0.00 && ($RST1['P1Y_SumPrft']/$RST1['P1Y_SumPrce'])*100 <= 24.99) {
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AB'.$Row)->getFont()->getColor()->setARGB('ffdc3545');
					} else {
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AB'.$Row)->getFont()->getColor()->setARGB('ff9a1118');
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AB'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff8d7da');
					}
				} else {
					$sheet1->setCellValue('AB'.$Row,0);  
				}
			}
			$sheet1->getStyle('AB'.$Row)->applyFromArray($TextCenter);
		// ข้อมูลการเคลมสินค้า 2022
			// ยอดขาย 2022 (หน่วย)
			if($RST1['P1Y_SumQty'] == 0) { 
				$sheet1->setCellValue('AC'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AC'.$Row,$RST1['P1Y_SumQty']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AC'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
			}
			$sheet1->getStyle('AC'.$Row)->applyFromArray($TextRight);
			// คืนเพื่อเคลมซัพฯ 2022 (หน่วย)
			if($RST1['P1Y_QcQty'] == 0) { 
				$sheet1->setCellValue('AD'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AD'.$Row,$RST1['P1Y_QcQty']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AD'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
			}
			$sheet1->getStyle('AD'.$Row)->applyFromArray($TextRight);
			// เคลมซัพฯ แล้ว 2022 (หน่วย)
			if($RST1['P1Y_ClaimQty'] == 0) { 
				$sheet1->setCellValue('AE'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AE'.$Row,$RST1['P1Y_ClaimQty']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AE'.$Row)->getNumberFormat()->setFormatCode("#,##0"); 
			}
			$sheet1->getStyle('AE'.$Row)->applyFromArray($TextRight);
			// มูลค่าการเคลม 2022 (THB)
			if($RST1['CRT_ClaimPrc'] == 0) { 
				$sheet1->setCellValue('AF'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AF'.$Row,$RST1['CRT_ClaimPrc']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AE'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('AF'.$Row)->applyFromArray($TextRight);
			// % การเคลม 2022
			if($RST1['P1Y_SumQty'] > 0) {
				$P1Y_ClaimPct = ($RST1['P1Y_ClaimQty'] / $RST1['P1Y_SumQty']);
			} else {
				$P1Y_ClaimPct = 0;
			}
			if($P1Y_ClaimPct > 0) {
				$sheet1->setCellValue('AG'.$Row,$P1Y_ClaimPct);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AG'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
			} else {
				$sheet1->setCellValue('AG'.$Row,0);  
			}
			$sheet1->getStyle('AG'.$Row)->applyFromArray($TextCenter);
			// รอเคลมซัพฯ (หน่วย)
			if($RST1['WPStockQty'] == 0) { 
				$sheet1->setCellValue('AH'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AH'.$Row,$RST1['WPStockQty']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AH'.$Row)->getNumberFormat()->setFormatCode("#,##0");  
			}
			$sheet1->getStyle('AH'.$Row)->applyFromArray($TextRight);
			// มูลค่ารอเคลมซัพฯ (THB)
			if($RST1['WPStockValue'] == 0) { 
				$sheet1->setCellValue('AI'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AI'.$Row,$RST1['WPStockValue']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AI'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");  
			}
			$sheet1->getStyle('AI'.$Row)->applyFromArray($TextRight);

		// วิเคราะห์การขาย 2023 
			// ต้นทุนขายรวม (VAT) (THB)
			if($RST1['CRT_SumCost'] == 0) { 
				$sheet1->setCellValue('AJ'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AJ'.$Row,$RST1['CRT_SumCost']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AJ'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('AJ'.$Row)->applyFromArray($TextRight);
			// ต้นทุนขายเฉลี่ย/ตัว (VAT) (THB)
			if($RST1['CRT_SumQty'] <= 0.00) {
				$sheet1->setCellValue('AK'.$Row,0);  
			} else {
				$sheet1->setCellValue('AK'.$Row,($RST1['CRT_SumCost']/$RST1['CRT_SumQty']));  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AK'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('AK'.$Row)->applyFromArray($TextRight);
			// ราคาขายรวม (VAT) (THB)
			if($RST1['CRT_SumPrce'] == 0) { 
				$sheet1->setCellValue('AL'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AL'.$Row,$RST1['CRT_SumPrce']); 
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AL'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");  
			}
			$sheet1->getStyle('AL'.$Row)->applyFromArray($TextRight);
			// ราคาขายเฉลี่ย/ตัว (VAT) (THB)
			if($RST1['CRT_SumQty'] <= 0.00) {
				$sheet1->setCellValue('AM'.$Row,0);  
			} else {
				$sheet1->setCellValue('AM'.$Row,($RST1['CRT_SumPrce']/$RST1['CRT_SumQty']));  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AM'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('AM'.$Row)->applyFromArray($TextRight);
			// กำไรรวม (THB)
			if($RST1['CRT_SumPrft'] == 0) { 
				$sheet1->setCellValue('AN'.$Row,0);  
			} else {  
				$sheet1->setCellValue('AN'.$Row,$RST1['CRT_SumPrft']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AN'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('AN'.$Row)->applyFromArray($TextRight);
			// กำไรเฉลี่ย/ตัว (VAT) (THB)
			if($RST1['CRT_SumQty'] <= 0.00) {
				$sheet1->setCellValue('AO'.$Row,0);  
			} else {
				$sheet1->setCellValue('AO'.$Row,($RST1['CRT_SumPrft']/$RST1['CRT_SumQty']));  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AO'.$Row)->getNumberFormat()->setFormatCode("#,##0.00"); 
			}
			$sheet1->getStyle('AO'.$Row)->applyFromArray($TextRight);
			// % of GP
			if($RST1['CRT_SumPrft'] == 0) {
				$sheet1->setCellValue('AP'.$Row,0);  
			} elseif($RST1['CRT_SumPrft'] < 0) {
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AP'.$Row)->getFont()->getColor()->setARGB('ff9a1118');
				if($RST1['CRT_SumPrce'] > 0) {
					$sheet1->setCellValue('AP'.$Row,(($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])));  
					$spreadsheet->setActiveSheetIndex(0)->getStyle('AP'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
				} else {
					$sheet1->setCellValue('AP'.$Row,0);  
				}
			} else {
				if($RST1['CRT_SumPrce'] != 0) {
					$sheet1->setCellValue('AP'.$Row,(($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])));  
					$spreadsheet->setActiveSheetIndex(0)->getStyle('AP'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
					if(($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])*100 >= 25.00) {
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AP'.$Row)->getFont()->getColor()->setARGB('ff198754');
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AP'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffd1e7dd');
					} elseif(($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])*100 >= 0.00 && ($RST1['CRT_SumPrft']/$RST1['CRT_SumPrce'])*100 <= 24.99) {
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AP'.$Row)->getFont()->getColor()->setARGB('ffdc3545');
					} else {
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AP'.$Row)->getFont()->getColor()->setARGB('ff9a1118');
						$spreadsheet->setActiveSheetIndex(0)->getStyle('AP'.$Row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff8d7da');
					}
				} else {
					$sheet1->setCellValue('AP'.$Row,0);  
				}
			}
			$sheet1->getStyle('AP'.$Row)->applyFromArray($TextCenter);
		// ข้อมูลการเคลมสินค้า 2023
			// ยอดขาย 2023 (หน่วย)
			if($RST1['CRT_SumQty'] == 0) { 
				$sheet1->setCellValue('AQ'.$Row,0);  
			} else { 
				$sheet1->setCellValue('AQ'.$Row,$RST1['CRT_SumQty']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AQ'.$Row)->getNumberFormat()->setFormatCode("#,##0");  
			}
			$sheet1->getStyle('AQ'.$Row)->applyFromArray($TextRight);
			// คืนเพื่อเคลมซัพฯ 2023 (หน่วย)
			if($RST1['CRT_QcQty'] == 0) { 
				$sheet1->setCellValue('AR'.$Row,0);
			} else { 
				$sheet1->setCellValue('AR'.$Row,$RST1['CRT_QcQty']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AR'.$Row)->getNumberFormat()->setFormatCode("#,##0");  
			}
			$sheet1->getStyle('AR'.$Row)->applyFromArray($TextRight);
			// เคลมซัพฯ แล้ว 2023 (หน่วย)
			if($RST1['CRT_ClaimQty'] == 0) { 
				$sheet1->setCellValue('AS'.$Row,0);
			} else { 
				$sheet1->setCellValue('AS'.$Row,$RST1['CRT_ClaimQty']);  
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AS'.$Row)->getNumberFormat()->setFormatCode("#,##0");  
			}
			$sheet1->getStyle('AS'.$Row)->applyFromArray($TextRight);
			// มูลค่าการเคลม 2023 (THB)
			if($RST1['CRT_ClaimPrc'] == 0) { 
				$sheet1->setCellValue('AT'.$Row,0);
			} else { 
				$sheet1->setCellValue('AT'.$Row,$RST1['CRT_ClaimPrc']);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AT'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");  
			}
			$sheet1->getStyle('AT'.$Row)->applyFromArray($TextRight);
			// % การเคลม 2023
			if($RST1['CRT_SumQty'] > 0) {
				$CRT_ClaimPct = ($RST1['CRT_ClaimQty'] / $RST1['CRT_SumQty']);
			} else {
				$CRT_ClaimPct = 0;
			}
			if($CRT_ClaimPct > 0) {
				$sheet1->setCellValue('AU'.$Row,$CRT_ClaimPct);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AU'.$Row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
			} else {
				$sheet1->setCellValue('AU'.$Row,0);
			}
			$sheet1->getStyle('AU'.$Row)->applyFromArray($TextCenter);
			// รอเคลมซัพฯ (หน่วย)
			if($RST1['WPStockQty'] == 0) { 
				$sheet1->setCellValue('AV'.$Row,0);
			} else { 
				$sheet1->setCellValue('AV'.$Row,$RST1['WPStockQty']);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AV'.$Row)->getNumberFormat()->setFormatCode("#,##0");  
			}
			$sheet1->getStyle('AV'.$Row)->applyFromArray($TextRight);
			// มูลค่ารอเคลมซัพฯ (THB) 
			if($RST1['WPStockValue'] == 0) { 
				$sheet1->setCellValue('AW'.$Row,0);
			} else { 
				$sheet1->setCellValue('AW'.$Row,$RST1['WPStockValue']);
				$spreadsheet->setActiveSheetIndex(0)->getStyle('AW'.$Row)->getNumberFormat()->setFormatCode("#,##0.00");  
			}
			$sheet1->getStyle('AW'.$Row)->applyFromArray($TextRight);
	}
	$LastRow = $Row+1;
	$sheet1->setCellValue('A'.$LastRow,"รวมทั้งหมด");
	$sheet1->setCellValue('F'.$LastRow,"=SUM(F3:F".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('H'.$LastRow,"=SUM(H3:H".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('J'.$LastRow,"=SUM(J3:J".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('L'.$LastRow,"=SUM(L3:L".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('S'.$LastRow,"=SUM(S3:S".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('S'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('V'.$LastRow,"=SUM(V3:V".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('V'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('X'.$LastRow,"=SUM(X3:X".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('X'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('Z'.$LastRow,"=SUM(Z3:Z".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('Z'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('AF'.$LastRow,"=SUM(AF3:AF".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('AF'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('AI'.$LastRow,"=SUM(AI3:AI".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('AI'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('AJ'.$LastRow,"=SUM(AJ3:V".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('AJ'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('AL'.$LastRow,"=SUM(AL3:AL".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('AL'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('AN'.$LastRow,"=SUM(AN3:AN".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('AN'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('AT'.$LastRow,"=SUM(AT3:AT".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('AT'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");
	$sheet1->setCellValue('AW'.$LastRow,"=SUM(AW3:AW".$LastRow.")");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('AW'.$LastRow)->getNumberFormat()->setFormatCode("#,##0.00");

	$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$LastRow.':D'.$LastRow);
	$sheet1->getStyle('A'.$LastRow.':AW'.$LastRow)->applyFromArray($PageHeader);

	$sheet1->getStyle('F'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('H'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('J'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('L'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('S'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('V'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('X'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('Z'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('AF'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('AI'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('AJ'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('AL'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('AN'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('AT'.$LastRow)->applyFromArray($TextRight);
	$sheet1->getStyle('AW'.$LastRow)->applyFromArray($TextRight);


	// SHEET 2
	// Header Data Sheet 2
	// $spreadsheet->setActiveSheetIndex(1)->mergeCells('A1:A2');
	$sheet2->setCellValue('A1',"ลำดับ");
	$sheet2->setCellValue('B1',"ทีมขาย");
	$sheet2->setCellValue('C1',"เลขที่เอกสาร");
	$sheet2->setCellValue('D1',"วันที่เอกสาร");
	$sheet2->setCellValue('E1',"ชื่อลูกค้า");
	$sheet2->setCellValue('F1',"รหัสสินค้า");
	$sheet2->setCellValue('G1',"ชื่อสินค้า");
	$sheet2->setCellValue('H1',"คลัง");
	$sheet2->setCellValue('I1',"จำนวน");
	$sheet2->setCellValue('J1',"หน่วย");
	$sheet2->setCellValue('K1',"รายละเอียดการคืน");
	$sheet2->setCellValue('L1',"เหตุผลการคืน");

	// Add Style Sheet 2
	$sheet2->getStyle('A1:AJ1')->applyFromArray($PageHeader);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('A')->setWidth(6);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('B')->setWidth(20);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('C')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('D')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('E')->setWidth(50);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('F')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('G')->setWidth(50);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('H')->setWidth(8);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('I')->setWidth(15);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('J')->setWidth(13);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('K')->setWidth(30);
	$spreadsheet->setActiveSheetIndex(1)->getColumnDimension('L')->setWidth(60);

	$ItemList = "";
	for($i = 0; $i < count($ItemArr); $i++) {
		$ItemList .= "'".$ItemArr[$i]."'";
		if($i != count($ItemArr)-1) {
			$ItemList .= ",";
		}
	}

	$SQL = "
		SELECT A0.* FROM
			(
				SELECT
					T0.DocEntry, T3.U_Dim1 AS 'TeamCode', T3.SlpName, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.DocDate,
					T0.CardCode, T0.CardName, T1.ItemCode, T1.Dscription, T1.WhsCode, T1.Quantity, T1.unitMsr, T1.U_Investigate_QC, T0.Comments,
					T4.Name AS 'ReturnType'
				FROM ORDN T0
				LEFT JOIN RDN1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN NNM1 T2 ON T0.Series = T2.Series
				LEFT JOIN OSLP T3 ON T0.SlpCode = T3.SlpCode
				LEFT JOIN [@CNREASON] T4 ON T0.U_CNReason2 = T4.Code
				WHERE (T1.ItemCode IN ($ItemList) AND T1.WhsCode IN ('WP4','WP5')) AND (YEAR(T0.DocDate) BETWEEN $ps1_year AND $cnt_year AND T0.CANCELED = 'N')
				UNION ALL
				SELECT
					T0.DocEntry, T3.U_Dim1 AS 'TeamCode', T3.SlpName, (T2.BeginStr+CAST(T0.DocNum AS VARCHAR)) AS 'DocNum', T0.DocDate,
					T0.CardCode, T0.CardName, T1.ItemCode, T1.Dscription, T1.WhsCode, T1.Quantity, T1.unitMsr, T1.U_Investigate_QC, T0.Comments,
					T4.Name AS 'ReturnType'
				FROM KBI_DB2022.dbo.ORDN T0
				LEFT JOIN KBI_DB2022.dbo.RDN1 T1 ON T0.DocEntry = T1.DocEntry
				LEFT JOIN KBI_DB2022.dbo.NNM1 T2 ON T0.Series = T2.Series
				LEFT JOIN KBI_DB2022.dbo.OSLP T3 ON T0.SlpCode = T3.SlpCode
				LEFT JOIN KBI_DB2022.dbo.[@CNREASON] T4 ON T0.U_CNReason2 = T4.Code
				WHERE (T1.ItemCode IN ($ItemList) AND T1.WhsCode IN ('WP4','WP5')) AND (YEAR(T0.DocDate) BETWEEN $ps1_year AND $cnt_year AND T0.CANCELED = 'N')
			) A0
		ORDER BY A0.ItemCode, A0.DocDate";
	$QRY = SAPSelect($SQL);
	$Row = 1; $No = 0;
	while($result = odbc_fetch_array($QRY)) {
		$Row++; $No++;
		// ลำดับ
		$sheet2->setCellValue('A'.$Row,$No); 
		$sheet2->getStyle('A'.$Row)->applyFromArray($TextCenter);
		// ทีมขาย
		switch(conutf8($result['TeamCode'])) {
			case "MT1": $TeamCode = "ฝ่ายขายโมเดิร์นเทรด 1"; break;
			case "MT2": $TeamCode = "ฝ่ายขายโมเดิร์นเทรด 2"; break;
			case "TT1": $TeamCode = "ฝ่ายขายร้านค้า กทม."; break;
			case "TT2": $TeamCode = "ฝ่ายขายร้านค้า ตจว."; break;
			case "OUL": $TeamCode = "ฝ่ายขายหน้าร้าน"; break;
			case "ONL": $TeamCode = "ฝ่ายขายออนไลน์"; break;
		}
		$sheet2->setCellValue('B'.$Row,$TeamCode); 
		// เลขที่เอกสาร
		$sheet2->setCellValue('C'.$Row,$result['DocNum']); 
		$sheet2->getStyle('C'.$Row)->applyFromArray($TextCenter);
		// วันที่เอกสาร
		$sheet2->setCellValue('D'.$Row,date("d/m/Y",strtotime($result['DocDate']))); 
		$sheet2->getStyle('D'.$Row)->applyFromArray($TextCenter);
		// ชื่อลูกค้า
		$sheet2->setCellValue('E'.$Row,conutf8($result['CardCode']." - ".$result['CardName'])); 
		// รหัสสินค้า
		$sheet2->setCellValue('F'.$Row,$result['ItemCode']); 
		$sheet2->getStyle('F'.$Row)->applyFromArray($TextCenter);
		// ชื่อสินค้า
		$sheet2->setCellValue('G'.$Row,conutf8($result['Dscription'])); 
		// คลัง
		$sheet2->setCellValue('H'.$Row,$result['WhsCode']); 
		$sheet2->getStyle('H'.$Row)->applyFromArray($TextCenter);
		// จำนวน
		if($result['Quantity'] == 0) { 
			$sheet2->setCellValue('I'.$Row,0); 
		} else { 
			$sheet2->setCellValue('I'.$Row,$result['Quantity']); 
			$spreadsheet->setActiveSheetIndex(1)->getStyle('I'.$Row)->getNumberFormat()->setFormatCode("#,##0");
		}
		$sheet2->getStyle('I'.$Row)->applyFromArray($TextRight);
		// หน่วย
		$sheet2->setCellValue('J'.$Row,conutf8($result['unitMsr'])); 
		// รายละเอียดการคืน
		$sheet2->setCellValue('K'.$Row,conutf8($result['ReturnType'])); 
		// เหตุผลการคืน
		$sheet2->setCellValue('L'.$Row,conutf8($result['U_Investigate_QC'])); 
	}

	$spreadsheet->setActiveSheetIndex(0)->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(0)->getRowDimension('1')->setRowHeight(18);
	$spreadsheet->setActiveSheetIndex(0)->getRowDimension('2')->setRowHeight(36);
	$spreadsheet->SetActiveSheetIndex(0)->freezePane('E3');

	$spreadsheet->setActiveSheetIndex(1)->getDefaultColumnDimension()->setWidth(13);
	$spreadsheet->setActiveSheetIndex(1)->getRowDimension('1')->setRowHeight(18);
	$spreadsheet->SetActiveSheetIndex(1)->freezePane('A2');

	$spreadsheet->setActiveSheetIndex(0);
	$writer = new Xlsx($spreadsheet);
	$FileName = "รายงานประเมินซัพพลายเออร์ - ".date("YmdHis").".xlsx";
	$writer->save("../../../../FileExport/SurveySupplier/".$FileName);
	$InsertSQL = "INSERT INTO logexport SET uKey = '".$_SESSION['ukey']."', ExportGroup = 'SurveySupplier', logFile = '$FileName', DateCreate = NOW()";
	MySQLInsert($InsertSQL);
	$arrCol['FileName'] = $FileName;
}

if($_GET['p'] == 'ViewWhsSub') {
	$ItemCode = $_POST['ItemCode'];
	$LimitQty = $_POST['Qty'];
	$SQL = 
		"SELECT
			T0.TransType,
			COALESCE(N14.BeginStr,N16.BeginStr,N59.BeginStr,N67.BeginStr)+CAST(COALESCE(T14.DocNum,T16.DocNum,T59.DocNum,T67.DocNum) AS VARCHAR) AS [DocNum],
			T0.[DocDate], T0.[CardCode], T0.[CardName], COALESCE(S14.SlpName,S16.SlpName,S59.SlpName,S67.SlpName) AS [SlpName],
			CASE WHEN T0.TransType = 67 THEN T0.Ref2 ELSE NULL END AS [TransFrom], T0.[Warehouse] AS [WhsCode], T0.[InQty],
			COALESCE(D14.unitMsr,D16.unitMsr,D59.unitMsr,D67.unitMsr) AS [unitMsr],
			COALESCE(G14.Name,G16.Name,G59.Name,G67.Name) AS [GradeItem],
			COALESCE (D14.U_Investigate_QC,D16.U_Investigate_QC,D59.U_Investigate_QC,T67.Comments) AS [InvestigateQC]
		FROM OINM T0
		LEFT JOIN ORIN T14 ON T0.TransType = 14 AND T0.BASE_REF = T14.DocNum
		LEFT JOIN RIN1 D14 ON T14.DocEntry = D14.DocEntry AND T0.DocLineNum = D14.LineNum
		LEFT JOIN NNM1 N14 ON T14.Series = N14.Series
		LEFT JOIN dbo.[@GRADE_ITEM] G14 ON D14.U_Grade_Item = G14.Code
		LEFT JOIN OSLP S14 ON T14.SlpCode = S14.SlpCode
		LEFT JOIN ORDN T16 ON T0.TransType = 16 AND T0.BASE_REF = T16.DocNum
		LEFT JOIN RDN1 D16 ON T16.DocEntry = D16.DocEntry AND T0.DocLineNum = D16.LineNum
		LEFT JOIN NNM1 N16 ON T16.Series = N16.Series
		LEFT JOIN dbo.[@GRADE_ITEM] G16 ON D16.U_Grade_Item = G16.Code
		LEFT JOIN OSLP S16 ON T16.SlpCode = S16.SlpCode
		LEFT JOIN OIGN T59 ON T0.TransType = 59 AND T0.BASE_REF = T59.DocNum
		LEFT JOIN IGN1 D59 ON T59.DocEntry = D59.DocEntry AND T0.DocLineNum = D59.LineNum
		LEFT JOIN NNM1 N59 ON T59.Series = N59.Series
		LEFT JOIN OSLP S59 ON T59.SlpCode = S59.SlpCode
		LEFT JOIN dbo.[@GRADE_ITEM] G59 ON D59.U_Grade_Item = G59.Code
		LEFT JOIN OWTR T67 ON T0.TransType = 67 AND T0.BASE_REF = T67.DocNum
		LEFT JOIN WTR1 D67 ON T67.DocEntry = D67.DocEntry AND T0.DocLineNum = D67.LineNum
		LEFT JOIN NNM1 N67 ON T67.Series = N67.Series
		LEFT JOIN OSLP S67 ON T67.SlpCode = S67.SlpCode
		LEFT JOIN dbo.[@GRADE_ITEM] G67 ON D67.U_Grade_Item = G67.Code
		WHERE T0.[Warehouse] IN ('WP4','WP5') AND T0.[InQty] > 0 AND T0.[ItemCode] = '$ItemCode'
		ORDER BY T0.[TransNum] DESC";
	$QRY = SAPSelect($SQL);
	$tmpQty = 0;
	$Data = "";
	$r = 0;
	while($RST = odbc_fetch_array($QRY)) {
		$r++;
		if($tmpQty < intval($LimitQty)) {
			$tmpQty = $tmpQty+$RST['InQty'];
			$Data .= 
				"<tr>
					<td class='text-center'>$r</td>
					<td class='text-center'>".$RST['DocNum']."</td>
					<td class='text-center'>".date("d/m/Y", strtotime($RST['DocDate']))."</td>
					<td class='text-center'>".$RST['CardCode']."</td>
					<td>".conutf8($RST['CardName'])."</td>
					<td>".conutf8($RST['SlpName'])."</td>
					<td class='text-center'>".$RST['TransFrom']."</td>
					<td class='text-center'>".$RST['WhsCode']."</td>
					<td class='text-right'>".number_format($RST['InQty'],0)."</td>
					<td>".conutf8($RST['unitMsr'])."</td>
					<td>".conutf8($RST['GradeItem'])."</td>
					<td>".conutf8($RST['InvestigateQC'])."</td>
				</tr>";
		}
	}
	$arrCol['Data'] = $Data;
}

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
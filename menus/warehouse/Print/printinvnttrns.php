<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if($_SESSION['UserName'] == NULL){
	echo '<script type="text/javascript">alert("ไม่สามารถดำเนินการใด ๆ ได้ เนื่องจาก Session หมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง"); window.location="../../../../"; </script>';
} else { 
    ?>
 
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />
        <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
        <link href="../../../../css/main/app.css" rel="stylesheet" />
        <title>รายงานความเคลื่อนไหวสินค้าคงคลัง</title>
        <style rel="stylesheet" type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
            html, body {
                background-color: #FFFFFF;
                font-family: 'Sarabun';
                font-weight: 400;
                color: #000 !important;
                font-size: 11px;
            }

            .page {
                /* margin: 3mm;
                width: 204mm;
                height: 291mm; */
                /* border: 1px dashed #000; */
                width: 210mm;
                height: 297mm;
                display: block;
                margin: 3mm auto;
                padding: 3mm;
                box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
            }
            .table {
                color: #000 !important;
            }
            @page {
                size: A4;
                margin: 0;
            }
            @media print {
                .page {
                    /* margin: 3mm;
                    width: 204mm;
                    height: 291mm;
                    page-break-after: always; */
                    height: initial;
                    margin: 0mm auto;
                    box-shadow: 0 0 0;
                    /* border: 1px dotted #000; */
                    page-break-after: always;
                }
            }
        </style>
    </head>
    
    <body>
    <?php 
    $ItemCode  = $_GET['ItemCode'];
	$WareHouse = $_GET['WareHouse'];
	$StartDate = $_GET['StartDate'];
	$EndDate   = $_GET['EndDate'];
	$Year      = $_GET['Year'];
    
    $SQL = "
		SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', X2.U_Dim1 AS SaleTeam,X0.*
		FROM (
			SELECT CASE WHEN P0.TransType IN (13,15) THEN 'A' ELSE 'B' END AS 'ORDR',P0.TransNum, P0.DocDate,P0.CreateDate,P0.TransType,
				CASE WHEN P0.TransType = 13 THEN (SELECT DISTINCT ISNULL('IV-',W1.BeginStr) FROM OINV W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W1.BeginStr FROM ORIN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W1.BeginStr FROM ODLN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W1.BeginStr FROM ORDN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W1.BeginStr FROM OPDN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W1.BeginStr FROM ORPD W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W1.BeginStr FROM OIGN W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W1.BeginStr FROM OIGE W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W1.BeginStr FROM OWTR W0 LEFT JOIN NNM1 W1 ON W0.Series = W1.Series WHERE W0.DocNum = P0.DocNum)
				END AS BeginStr,
				P0.DocNum,P0.SAPtb,
				CASE WHEN P0.TransType = 13 THEN (SELECT DISTINCT W0.DocEntry FROM OINV W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W0.DocEntry FROM ORIN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W0.DocEntry FROM ODLN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W0.DocEntry FROM ORDN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W0.DocEntry FROM OPDN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W0.DocEntry FROM ORPD W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W0.DocEntry FROM OIGN W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W0.DocEntry FROM OIGE W0 WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W0.DocEntry FROM OWTR W0 WHERE W0.DocNum = P0.DocNum)
				END AS DocEntry,
				CASE WHEN P0.TransType = 13 THEN (SELECT DISTINCT W0.BaseEntry FROM INV1 W0 LEFT JOIN OINV W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W0.BaseEntry FROM DLN1 W0 LEFT JOIN ODLN W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W0.BaseEntry FROM PDN1 W0 LEFT JOIN OPDN W1 ON W0.DocEntry = W1.DocEntry AND W0.ItemCode = P0.ItemCode WHERE W1.DocNum = P0.DocNum) 
					ELSE NULL 
				END AS SODocEntry,P0.CardCode,P0.CardName,
				CASE WHEN P0.TransType = 13 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName FROM OINV W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 14 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORIN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 15 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ODLN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 16 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORDN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 20 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OPDN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 21 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM ORPD W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 59 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OIGN W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 60 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OIGE W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
					WHEN P0.TransType = 67 THEN (SELECT DISTINCT W1.LastName+' '+W1.FirstName  FROM OWTR W0 LEFT JOIN OHEM W1 ON W0.OwnerCode = W1.EMPID WHERE W0.DocNum = P0.DocNum)
				END AS Owner,
				P0.ItemCode, P0.ItemName,P0.WhsCode,P0.InQty,P0.OutQty
			FROM (
				SELECT MAX(T0.TransNum) AS TransNum,T0.[DocDate] AS DocDate, T0.[CreateDate] AS 'CreateDate', T0.TransType,
					CASE WHEN T0.TransType = 13 THEN 'OINV'
						WHEN T0.TransType = 14 THEN 'ORIN'
						WHEN T0.TransType = 15 THEN 'ODLN'
						WHEN T0.TransType = 16 THEN 'ORDN'
						WHEN T0.TransType = 20 THEN 'OPDN'
						WHEN T0.TransType = 21 THEN 'ORPD'
						WHEN T0.TransType = 59 THEN 'OIGN'
						WHEN T0.TransType = 60 THEN 'OIGE'
						WHEN T0.TransType = 67 THEN 'OWTR'
					END AS SAPtb,T0.[BASE_REF] AS 'DocNum',
					T0.[ItemCode] AS ItemCode, T0.[Dscription] AS ItemName,T0.[WareHouse] AS WhsCode,SUM(T0.[InQty]) AS InQty,SUM(T0.[OutQty]) AS OutQty,T0.CardCode,T0.CardName
				FROM OINM T0 
				WHERE (T0.[CreateDate] BETWEEN '$StartDate' AND '$EndDate') AND T0.[WareHouse] = '$WareHouse' AND T0.ItemCode = '$ItemCode' AND ((T0.[InQty] + T0.[OutQty]) != 0) 
				GROUP BY T0.[DocDate], T0.[CreateDate], T0.TransType,T0.[BASE_REF],T0.[ItemCode],T0.[Dscription],T0.[WareHouse],T0.CardCode,T0.CardName 
			) P0
		) X0
		LEFT JOIN ORDR X1 ON X0.SODocEntry = X1.DocEntry
		LEFT JOIN OSLP X2 ON X1.SlpCode = X2.SlpCode
		ORDER BY X0.TransNum";
    $SQL2 = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', SUM(T0.[InQty]-T0.[OutQty]) AS OnHand FROM OINM T0 WHERE T0.[ItemCode] = '$ItemCode' AND T0.[Warehouse] = '$WareHouse' AND T0.[CreateDate] < '$StartDate'";
    if($Year >= 2023){
		$QRY2 = SAPSelect($SQL2);
	}else{
		$QRY2 = conSAP8($SQL2);
	}
	$OnHand = odbc_fetch_array($QRY2);
	$QtyOld = 0;
	if(isset($OnHand['OnHand'])) { $QtyOld = $OnHand['OnHand']; }
    $Data = array();
	$Data['No'][0]         = "&nbsp;";
	$Data['CreateDate'][0] = "&nbsp;";
	$Data['DocDate'][0]    = "&nbsp;";
	$Data['DocNum'][0]     = "&nbsp;";
	$Data['DocType'][0]    = "ยอดยกมา";
	$Data['ReceivePay'][0] = "Opening Balance";
	$Data['Team'][0]       = "&nbsp;";
	$Data['WhsCode'][0]    = "&nbsp;";
	$Data['Location'][0]   = "&nbsp;";
	$Data['InQty'][0]      = "&nbsp;";
	$Data['OutQty'][0]     = "&nbsp;";
	$Data['QtyShow'][0]    = number_format($QtyOld,0);
	$Data['Owner'][0]      = "&nbsp;";
    if($Year >= 2023){
		$QRY = SAPSelect($SQL);
	}else{
		$QRY = conSAP8($SQL);
	}
	$r = 0; $TotalIn = 0; $TotalOut = 0;
	while($result = odbc_fetch_array($QRY)) {
		$r++;
		$ORDR[$r]       = $result['ORDR'];
        $CreateDate[$r] = $result['CreateDate'];
        $DocDate[$r]    = $result['DocDate'];
        $TransType[$r]  = $result['TransType'];
        $BeginStr[$r]   = $result['BeginStr'];
        $DocNum[$r]     = $BeginStr[$r].$result['DocNum'];
		if($result['SaleTeam'] != null) {
			$SaleTeam[$r]   = $result['SaleTeam'];
		}else{
			$SaleTeam[$r]   = "&nbsp;";
		}
        $SAPtb[$r]      = $result['SAPtb'];
        $DocEntry[$r]   = $result['DocEntry'];
        $SODocEntry[$r] = $result['SODocEntry'];
        $Owner[$r]      = conutf8($result['Owner']);
        $CardCode[$r]   = $result['CardCode'];
        $CardName[$r]   = conutf8($result['CardName']);
        $WhsCode[$r]    = $result['WhsCode'];
		if($result['InQty'] != 0) {
			$InQty[$r]  = $result['InQty'];
		}else{
			$InQty[$r]  = "-";
		}
        if($result['OutQty'] != 0) {
			$OutQty[$r] = $result['OutQty'];
		}else{
			$OutQty[$r] = "-";
		}
	}
    $QtyShow = $QtyOld; 
    for($i = 1; $i <= $r; $i++) {
		if($InQty[$i] == "-")  { $In = 0;}  else { $In = $InQty[$i];   $InQty[$i]  = number_format($InQty[$i],0); }
		if($OutQty[$i] == "-") { $Out = 0;} else { $Out = $OutQty[$i]; $OutQty[$i] = number_format($OutQty[$i],0); }
		$QtyShow = $QtyShow + (1*$In) + (-1*$Out);
		// echo $QtyShow." + (1*".$In.") + (-1*".$Out.")\n";
		switch($TransType[$i]) {
			case '13': 
				$textType = "เบิกสินค้าเพื่อขาย";
				$SQLCase  = "
					SELECT T0.DocNum, T1.uName, T1.uLastName, T2.LocationRack
					FROM picker_soheader T0
					LEFT JOIN users T1 ON T0.UkeyPicker = T1.uKey 
					LEFT JOIN transecdata T2 ON T0.SODocEntry = T2.trnCode AND T2.ItemCode = '$ItemCode'  
					WHERE T0.SODocEntry = ".$SODocEntry[$i]." AND T0.DocType = 'ORDR'";
				$result    = MySQLSelect($SQLCase);
				$Owner[$i] = $result['uName']." ".$result['uLastName'];
				if($result['LocationRack'] != null) {
					$Location = $result['LocationRack'];
				}else{
					$Location = "&nbsp;";
				}
			break;
			case '14': 
				$textType = "รับคืนสินค้าขาย";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '15': 
				$textType = "เบิกยืมสินค้า";
				$SQLCase  = "
					SELECT T0.DocNum, T1.uName, T1.uLastName, T2.LocationRack
					FROM picker_soheader T0
					LEFT JOIN users T1 ON T0.UkeyPicker = T1.uKey 
					LEFT JOIN transecdata T2 ON T0.SODocEntry = T2.trnCode AND T2.ItemCode = '$ItemCode'  
					WHERE T0.SODocEntry = ".$SODocEntry[$i]." AND T0.DocType = 'ORDR'";
				$result    = MySQLSelect($SQLCase);
				$Owner[$i] = $result['uName']." ".$result['uLastName'];
				if($result['LocationRack'] != null) {
					$Location = $result['LocationRack'];
				}else{
					$Location = "&nbsp;";
				}
			break;
			case '16': 
				$textType = "รับคืนสินค้ายืม";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '20': 
				$textType = "รับสินค้าเข้า";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '21': 
				$textType = "คืนสินค้าซัพพลายเออร์";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '60': 
			case '59': 
				$textType = "ปรับสต๊อคภายใน";
                $Location = $WhsCode[$i]."-Recive";
			break;
			case '67': 
				$textType = "โอนย้ายระหว่างคลัง";
                $Location = $WhsCode[$i]."-Recive";
			break;
		}

		$Data['No'][$i]         = $i;
		$Data['CreateDate'][$i] = date("d/m/Y",strtotime($CreateDate[$i]));
		$Data['DocDate'][$i]    = date("d/m/Y",strtotime($DocDate[$i]));
		$Data['DocNum'][$i]     = $DocNum[$i];
		$Data['DocType'][$i]    = $textType;
		$Data['ReceivePay'][$i] = $CardCode[$i]." ".$CardName[$i];
		$Data['Team'][$i]       = $SaleTeam[$i];
		$Data['WhsCode'][$i]    = $WhsCode[$i];
		$Data['Location'][$i]   = $Location;
		$Data['InQty'][$i]      = $InQty[$i];
		$Data['OutQty'][$i]     = $OutQty[$i];
		$Data['QtyShow'][$i]    = number_format($QtyShow,0);
		$Data['Owner'][$i]      = $Owner[$i];
	}

	$LastRow = $r+1;
	$Data['No'][$LastRow]         = "&nbsp;";
	$Data['CreateDate'][$LastRow] = "&nbsp;";
	$Data['DocDate'][$LastRow]    = "&nbsp;";
	$Data['DocNum'][$LastRow]     = "&nbsp;";
	$Data['DocType'][$LastRow]    = "ยอดสุดท้าย";
	$Data['ReceivePay'][$LastRow] = "Closed Balance";
	$Data['Team'][$LastRow]       = "&nbsp;";
	$Data['WhsCode'][$LastRow]    = "&nbsp;";
	$Data['Location'][$LastRow]   = "&nbsp;";
	$Data['InQty'][$LastRow]      = "&nbsp;";
	$Data['OutQty'][$LastRow]     = "&nbsp;";
	$Data['QtyShow'][$LastRow]    = number_format($QtyShow,0);
	$Data['Owner'][$LastRow]      = "&nbsp;";

    $Sumrow = ($r+1);
    $rowsperpage = 35;
    $pages = ceil($Sumrow/$rowsperpage);
    $row = 0;
    for($p = 1; $p <= $pages; $p++) {
    ?>
        <div class="page">
            <table class="table table-borderless table-sm" style="color: #000;">
                <thead>
                    <tr>
                        <td width="20%" class="text-center">
                            <img src="../../../../image/logo/kbi_logo.png" class="img-fluid" />
                        </td>
                        <td>
                            <h4 class='text-black'>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                            <small>
                                541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                เลขประจำตัวผู้เสียภาษี: 0105545012035 สำนักงานใหญ่ | โทรศัพท์: 02-509-3850 | โทรสาร: 02-509-3856
                            </small>
                        </td>
                        <td width="15%" class="align-top text-right">หน้าที่ <?php echo $p; ?> จาก <?php echo $pages; ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center"><h5 style="margin-top: 1rem;" class='text-black'>รายงานความเคลื่อนไหวสินค้าคงคลัง</h5></td>
                    </tr>
                </thead>
            </table>
            <?php 
            $SQLHEAD = "SELECT ItemCode, ItemName, SalUnitMSr AS UnitMsr FROM OITM WHERE ItemCode = '$ItemCode'"; 
            if($Year >= 2023){
                $QRYHEAD = SAPSelect($SQLHEAD);
            }else{
                $QRYHEAD = conSAP8($SQLHEAD);
            }
            $HEAD = odbc_fetch_array($QRYHEAD);
            ?>
            <table class='mb-1'>
                <tbody>
                    <tr class=''>
                        <td class='fw-bold pt-1 pb-1'>ชื่อสินค้า:</td>
                        <td colspan='3'><?php echo $HEAD['ItemCode']." ".conutf8($HEAD['ItemName']); ?></td>
                    </tr>
                    <tr>
                        <td width='10%'  class='fw-bold pt-1 pb-1'>หน่วย:</td>
                        <td width='20%'><?php echo conutf8($HEAD['UnitMsr']); ?></td>
                        <td width='10%' class='fw-bold'>คลังสินค้า:</td>
                        <td width='23%'><?php echo $WareHouse; ?></td>
                        <td width='10%' class='fw-bold'>ระยะเวลา:</td>
                        <td width='27%'><?php echo date("d/m/Y",strtotime($StartDate))." ถึง ".date("d/m/Y",strtotime($EndDate)); ?></td>
                    </tr>
                    <tr>
                        <td class='fw-bold pt-1 pb-1'>ผู้จัดทำ:</td>
                        <td><?php echo $_SESSION['uName']." ".$_SESSION['uLastName'] ?></td>
                        <td class='fw-bold'>วันที่ดึงข้อมูล:</td>
                        <td><?php echo date("d/m/Y")." เวลา ".date("H:i")." น."; ?></td>
                        
                    </tr>
                </tbody>
            </table>
            <table class='table table-sm table-hover table-bordered border-dark' style="table-layout:fixed;">
                <tbody style='font-size: 9px;'>
                    <tr class="text-center">
                        <th rowspan='2' width='3%' class='align-bottom'>No.</th>
                        <th rowspan='2' width='7.5%' class='align-bottom'>วันที่เข้าระบบ</th>
                        <th rowspan='2' width='7.5%' class='align-bottom'>วันที่เอกสาร</th>
                        <th rowspan='2' width='9%' class='align-bottom'>เลขที่เอกสาร</th>
                        <th rowspan='2' width='11%' class='align-bottom'>ประเภทเอกสาร</th>
                        <th rowspan='2' width='21%' class='align-bottom'>รับจาก/จ่ายให้</th>
                        <th rowspan='2' width='4%' class='align-bottom'>ทีม</th>
                        <th rowspan='2' width='5%' class='align-bottom'>คลังสินค้า</th>
                        <th rowspan='2' width='8%' class='align-bottom'>พื้นที่จัดเก็บ</th>
                        <th colspan='3'>จำนวน</th>
                        <th rowspan='2' width='11%' class='align-bottom'>ผู้ปฏิบัติงาน</th>
                    </tr>
                    <tr class="text-center">
                        <th width='4%'>เข้า</th>
                        <th width='4%'>ออก</th>
                        <th width='5%'>เหลือ</th>
                    </tr>
                    
                    <?php 
                    for($i = 1; $i <= $rowsperpage; $i++) { 
                        if($Sumrow >= $row) { 
                            $cActive = "";
                            if($row == 0 || $row == $Sumrow) {
                                $cActive = "table-active text-primary";
                            }
                            ?>
                        
                            <tr class='<?php echo $cActive; ?>'>
                                <td class='text-right'><?php echo $Data['No'][$row]; ?></td>
                                <td class='text-center'><?php echo $Data['CreateDate'][$row]; ?></td>
                                <td class='text-center'><?php echo $Data['DocDate'][$row]; ?></td>
                                <td class='text-center'><?php echo $Data['DocNum'][$row]; ?></td>
                                <td><?php echo $Data['DocType'][$row]; ?></td>
                                <td style="word-wrap: break-word; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $Data['ReceivePay'][$row]; ?></td>
                                <td class='text-center'><?php echo $Data['Team'][$row]; ?></td>
                                <td class='text-center'><?php echo $Data['WhsCode'][$row]; ?></td>
                                <td class='text-center'><?php echo $Data['Location'][$row]; ?></td>
                                <td class='text-right'><?php echo $Data['InQty'][$row]; ?></td>
                                <td class='text-right'><?php echo $Data['OutQty'][$row]; ?></td>
                                <td class='text-right'><?php echo $Data['QtyShow'][$row]; ?></td>
                                <td><?php echo $Data['Owner'][$row]; ?></td>
                            </tr>
                        <?php
                        $row++;
                        }
                    } 
                    ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

        <script type="text/javascript">
            // setTimeout(() => {
                window.print();
            // }, 500);
        </script>
    </body>
    </html>
    
<?php } ?>
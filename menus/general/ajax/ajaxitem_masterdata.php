<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
$resultArray = array();
$arrCol = array();
$output = "";
if($_SESSION['UserName']==NULL ){
	echo '<script>window.location="../../../../"</script>';
}
if ($_GET['a'] == 'head' ){
	$sql1 = "SELECT MenuName,MenuIcon FROM menus WHERE MenuCase = '".$_POST['MenuCase']."'";
	$MenuHead = MySQLSelect($sql1);
	$arrCol['header1'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
	$arrCol['header2'] = $MenuHead['MenuIcon']." ".$MenuHead['MenuName'];
}

if($_GET['a'] == 'CallData') {
	$ItemCode = '';
	if(isset($_POST['ItemCode'])) {
		$ItemCode = "AND T1.ItemCode = '".$_POST['ItemCode']."' ";
	}
	
	$SQL = "SELECT T0.ItemCode, T0.P0,T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice, T1.ItemName, T1.BarCode, T1.ProductStatus AS ST, T0.PriceType
			FROM pricelist T0
			LEFT JOIN OITM T1 ON T1.ItemCode = T0.ItemCode
			WHERE T0.ItemCode NOT LIKE '%เก่า%' AND T0.ItemCode NOT LIKE '%ZZ%' AND T1.ItemName != '' $ItemCode AND T0.PriceStatus = 'A' AND 
				(T0.PriceType != 'PRO' OR (T0.PriceType = 'PRO' AND DATE(T0.EndDate) >= NOW()))
			ORDER BY T0.ItemCode";
	$SQLQRY = MySQLSelectX($SQL);
	$r = 0;
	while($result = mysqli_fetch_array($SQLQRY)) {
		if($result['PriceType'] == 'STD') {
			$arrCol[$r]['PriceType'] = "ราคามาตรฐาน";
		}else{
			$arrCol[$r]['PriceType'] = $result['PriceType'];
		}

		$QRYSAP = "
			SELECT
				T0.ItemCode, SUM(T0.OnHand) AS 'OnHand'
			FROM OITW T0
			LEFT JOIN OWHS T1 ON T0.WhsCode = T1.WhsCode
			WHERE T1.Location IN (1,2) AND T0.ItemCode = '".$result['ItemCode']."'
			GROUP BY T0.ItemCode";
		$QRYSAP = SAPSelect($QRYSAP);
		$RSTSAP = odbc_fetch_array($QRYSAP);
		$OnHand = 0;
		if(isset($RSTSAP['OnHand'])) {
			$OnHand = $RSTSAP['OnHand'];
		}
		
		// รหัสสินค้า
		$arrCol[$r]['ItemCode'] = "<a href='javascript:void(0);' onclick='SelectItemCode(\"".$result['ItemCode']."\",\"".$result['PriceType']."\")'>".$result['ItemCode']."</a>";
		// ชื่อสินค้า
		$arrCol[$r]['ItemName'] = $result['ItemName']." <span class='text-primary'>[".$result['ST']."]</span>";
		// บาร์โต้ด
		$arrCol[$r]['BarCode']  = $result['BarCode'];
		// Stock
		$arrCol[$r]['OnHand']  = number_format($OnHand,0);
		// ราคาตั้ง
		$arrCol[$r]['P0'] = number_format($result['P0'],2);
		// ปลีก SEMI
		$arrCol[$r]['P1'] = number_format($result['P1'],2);
		// ส่ง SEMI
		$arrCol[$r]['P2'] = number_format($result['P2'],2);
		// S1
		$arrCol[$r]['S1'] = number_format($result['S1P'],2);
		// จำนวน S1
		$arrCol[$r]['S1Q'] = number_format($result['S1Q'],0);
		// S2
		$arrCol[$r]['S2'] = number_format($result['S2P'],2);
		// จำนวน S2
		$arrCol[$r]['S2Q'] = number_format($result['S2Q'],0);
		// S3
		$arrCol[$r]['S3'] = number_format($result['S3P'],2);
		// จำนวน S3
		$arrCol[$r]['S3Q'] = number_format($result['S3Q'],0);

		if($_SESSION['uClass'] == 0 || $_SESSION['uClass'] == 1 || $_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4) {
			// ทุน
			$sqlSAP =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc'
						FROM OITM T0 
						LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode 
						WHERE T0.ItemCode = '".$result['ItemCode']."'";   
			$qrySAP = SAPSelect($sqlSAP);
			$resultSAP = odbc_fetch_array($qrySAP);
			$LastPurPrc = 0.00;
			if(isset($resultSAP['LastPurPrc'])) {
				$arrCol[$r]['LastPurPrc']  = number_format($resultSAP['LastPurPrc'],2);
				$LastPurPrc = $resultSAP['LastPurPrc'];
			}else{
				$arrCol[$r]['LastPurPrc'] = "0.00";
			}
			// GP ส่ง SEMI
			if($result['P2'] != 0)  { $arrCol[$r]['GP_P2'] = number_format(((($result['P2']-$LastPurPrc)/$result['P2'])*100),2)."%";   }else{ $arrCol[$r]['GP_P2'] = "0.00%"; }
			// GP S1	
			if($result['S1P'] != 0) { $arrCol[$r]['GP_S1'] = number_format(((($result['S1P']-$LastPurPrc)/$result['S1P'])*100),2)."%"; }else{ $arrCol[$r]['GP_S1'] = "0.00%"; }
			// GP S2
			if($result['S2P'] != 0) { $arrCol[$r]['GP_S2'] = number_format(((($result['S2P']-$LastPurPrc)/$result['S2P'])*100),2)."%"; }else{ $arrCol[$r]['GP_S2'] = "0.00%"; }
			// GP S3
			if($result['S3P'] != 0) { $arrCol[$r]['GP_S3'] = number_format(((($result['S3P']-$LastPurPrc)/$result['S3P'])*100),2)."%"; }else{ $arrCol[$r]['GP_S3'] = "0.00%"; }
			// ผจก Net
			$arrCol[$r]['MgrPrice'] = number_format($result['MgrPrice'],2);
			// GP ผจก Net
			if($result['MgrPrice'] != 0) { $arrCol[$r]['GP_Mgr'] = number_format(((($result['MgrPrice']-$LastPurPrc)/$result['MgrPrice'])*100),2); }else{ $arrCol[$r]['GP_Mgr'] = "0.00%"; }
			// ปลิก MT
			$arrCol[$r]['MTPrice'] = number_format($result['MTPrice'],2);
		}
		$r++;
	}
}

if ($_GET['a'] == 'SelectItemCode') {
	$sql = "SELECT T0.ItemCode, T0.BarCode, T0.ItemName, T0.MgrUnit, T0.ProductStatus,T1.P0
			FROM OITM T0
			LEFT JOIN pricelist T1 ON  T0.ItemCode = T1.ItemCode AND T1.PriceType = 'STD' AND T1.PriceStatus = 'A'
			WHERE T0.ItemCode = '".$_POST['ItemCode']."' LIMIT 1";
			// echo $sql;
	$result = MySQLSelect($sql);
	$ItemRowMainH = "<tr>"."<th colspan='2' class='font-extrabold text-primary fs-5 pt-0 pb-0'>".$result['ItemName']."</th>"."</tr>";
	$ItemRowMainB = "<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>รหัสสินค้า</td>"."<td class='pt-0 pb-0'>".$result['ItemCode']."</td>"."</tr>".
					"<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>บาร์โค้ด</td>"."<td class='pt-0 pb-0'>".$result['BarCode']."</td>"."</tr>".
					"<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>ชื่อสินค้า</td>"."<td class='pt-0 pb-0'>".$result['ItemName']."</td>"."</tr>".
					"<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>ยี่ห้อ</td>"."<td class='pt-0 pb-0'</td>"."</tr>".
					"<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>กลุ่มสินค้าหลัก</td>"."<td class='pt-0 pb-0'></td>"."</tr>".
					"<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>กลุ่มสินค้ารอง</td>"."<td class='pt-0 pb-0'></td>"."</tr>".
					"<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>หน่วย</td>"."<td class='pt-0 pb-0'>".$result['MgrUnit']."</td>"."</tr>".
					"<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>สถานะ</td>"."<td class='pt-0 pb-0'>".$result['ProductStatus']."</td>"."</tr>".
					"<tr class='font-rps'>"."<td class='font-extrabold pt-0 pb-0'>ราคาตั้ง</td>"."<td class='pt-0 pb-0'>".number_format($result['P0'])." บาท </td></tr>";

	$ItemRowSpecH = "<tr class='table-dange'>"."<th colspan='2' class='text-primary fs-6'>สเปค ".$result['ItemName']."arrange</th>"."</tr>";
	$ItemRowSpecB =	"<tr>"."<td class='fw-bolder ps-3'>รหัสสินค้า</td>"."<td>".$result['ItemCode'].""."</tr>".
					"<tr>"."<td class='fw-bolder ps-3'>แรงดันไฟฟ้า</td>"."<td>220V15% (1PH) / 50-60Hz</td>"."</tr>".
					"<tr>"."<td class='fw-bolder ps-3'>กระแสไฟเขื่อม (แอมป์)</td>"."<td>40-130A</td>"."</tr>".
					"<tr>"."<td class='fw-bolder ps-3'>Duty Cycle</td>"."<td>35%</td>"."</tr>".
					"<tr>"."<td class='fw-bolder ps-3'>ขนาดลวดเชื่อม (ระบบ MIG)</td>"."<td>0.8-1.0 มม.</td>"."</tr>".
					"<tr>"."<td class='fw-bolder ps-3'>รูปเชื่อม (ระบบ MMA)</td>"."<td>2.6-3.2 มม.</td>"."</tr>".
					"<tr>"."<td class='fw-bolder ps-3'>ระดับป้อมกัน</td>"."<td>IP21S</td>"."</tr>".
					"<tr>"."<td class='fw-bolder ps-3'>สายเชื่อม / สายดิน / ชุดสายเชื่อม</td>"."<td>1.8 / 1.2 / 2.2 เมตร</td>"."</tr>";		

	$filesIMG = glob("../../../../image/products/".$result['ItemCode']."/*.{jpg,png}",GLOB_BRACE);
	if(isset($filesIMG[0])) {
		$Slide = 1; $r = 0;
		for ($i = 0; $i < count($filesIMG); $i++){
			if($i == 0) {
				$ItemImages =	"<div class='carousel-item active text-center' data-bs-interval='5000'>".
									"<img src='".$filesIMG[$i]."' style='max-width: 255px;'>".
									"<div class='carousel-caption d-none d-md-block'></div>".
								"</div>";
				$btnImages = "<button type='button' data-bs-target='#carouselExampleDark' data-bs-slide-to='".$r."' class='active' aria-current='true' aria-label='Slide ".$Slide."'></button>";
			}else{
				$ItemImages .=	"<div class='carousel-item text-center' data-bs-interval='5000'>".
									"<img src='".$filesIMG[$i]."' style='max-width: 255px;'>".
									"<div class='carousel-caption d-none d-md-block'></div>".
								"</div>";
				$btnImages .= "<button type='button' data-bs-target='#carouselExampleDark' data-bs-slide-to='".$r."' aria-label='Slide ".++$Slide."'></button>";
			}
			$r++;
		}

		$filesIMG_Type1 = glob("../../../../image/products/".$result['ItemCode']."/1/*.{jpg,png}",GLOB_BRACE);
		if(isset($filesIMG_Type1[0])) {
			for($i = 0; $i < count($filesIMG_Type1); $i++){
				$ItemImages .=	"<div class='carousel-item text-center' data-bs-interval='5000'>".
									"<img src='".$filesIMG_Type1[$i]."' style='max-width: 255px;'>".
									"<div class='carousel-caption d-none d-md-block'></div>".
								"</div>";
				$btnImages .= "<button type='button' data-bs-target='#carouselExampleDark' data-bs-slide-to='".$r."' aria-label='Slide ".$Slide++."'></button>";
				$r++;
			}
		}

		$arrCol['btnImages'] = $btnImages;
		$arrCol['ItemImages'] = $ItemImages;
	}else{
		$filesIMG_Type1 = glob("../../../../image/products/".$result['ItemCode']."/1/*.{jpg,png}",GLOB_BRACE);
		if(isset($filesIMG_Type1[0])) {
			$Slide = 1;
			for ($i = 0; $i < count($filesIMG_Type1); $i++){
				if ($i == 0) {
					$ItemImages =	"<div class='carousel-item active text-center' data-bs-interval='5000'>".
										"<img src='".$filesIMG_Type1[$i]."' style='max-width: 255px;'>".
										"<div class='carousel-caption d-none d-md-block'></div>".
									"</div>";
					$btnImages = "<button type='button' data-bs-target='#carouselExampleDark' data-bs-slide-to='".$i."' class='active' aria-current='true' aria-label='Slide ".$Slide."'></button>";
				}else{
					$ItemImages .=	"<div class='carousel-item text-center' data-bs-interval='5000'>".
										"<img src='".$filesIMG_Type1[$i]."' style='max-width: 255px;'>".
										"<div class='carousel-caption d-none d-md-block'></div>".
									"</div>";
					$btnImages .= "<button type='button' data-bs-target='#carouselExampleDark' data-bs-slide-to='".$i."' aria-label='Slide ".++$Slide."'></button>";
				}
			}
		}else{
			$ItemImages =	"<div class='carousel-item active text-center' data-bs-interval='5000'>".
								"<img src='../../../../image/products/no-image.jpg' style='max-width: 255px;'>".
								"<div class='carousel-caption d-none d-md-block'></div>".
							"</div>";
			$btnImages = "";
		}
		$arrCol['ItemImages'] = $ItemImages;
		$arrCol['btnImages'] = $btnImages;
	}

	$arrCol['ItemRowMainH'] = $ItemRowMainH;
	$arrCol['ItemRowMainB'] = $ItemRowMainB;
	$arrCol['ItemRowSpecH'] = $ItemRowSpecH;
	$arrCol['ItemRowSpecB'] = $ItemRowSpecB;

	// ราคา
	$SQL_GroupCode = "SELECT GroupCode FROM groupprice GROUP BY GroupCode ORDER BY GroupCode";
	$QRY_GroupCode = MySQLSelectX($SQL_GroupCode);
	$ArrPriceType = ['STD', 'PRO'];
	while($RST_GroupCode = mysqli_fetch_array($QRY_GroupCode)) {
		array_push($ArrPriceType, $RST_GroupCode['GroupCode']);
	}
	$DataPrice = "";
	foreach($ArrPriceType as $key=>$DataPriceType) {
		$ProPrice = "";
		switch($DataPriceType) {
			case 'STD': $HeaderPriceType = "<i class='fas fa-file-invoice-dollar'></i> ราคามาตรฐาน"; break;
			case 'PRO': $HeaderPriceType = "<i class='fas fa-file-invoice-dollar'></i> ราคาโปรโมชั่น"; break;
			default: $HeaderPriceType = "<i class='fas fa-file-invoice-dollar'></i> ราคา ".$DataPriceType; break;
		}

		$SQL_PRO = ($DataPriceType == 'PRO') ? "AND DATE(T0.EndDate) >= NOW()" : "";

		$SQLp1 = "
			SELECT T0.P0,T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice, T0.MTPrice2, T0.StartDate, T0.EndDate
			FROM pricelist T0
			WHERE T0.ItemCode = '".$_POST['ItemCode']."' AND T0.PriceType = '".$DataPriceType."' AND T0.PriceStatus = 'A' $SQL_PRO LIMIT 1";
		
		if(CHKRowDB($SQLp1) != 0) {
			$RSTp1 = MySQLSelect($SQLp1);

			$sqlSAP =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc'
					FROM OITM T0 
					LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode 
					WHERE T0.ItemCode = '".$_POST['ItemCode']."'";   
			$qrySAP = SAPSelect($sqlSAP);
			$resultSAP = odbc_fetch_array($qrySAP);
			$LastPurPrc = 0;
			if(isset($resultSAP['LastPurPrc'])) {
				$LastPurPrc = $resultSAP['LastPurPrc'];
			}

			switch ($_SESSION['uClass']){
				case 0 :
				case 2 :
				case 3 :
				case 4 :
				case 18 :
				case 19 :
					if($RSTp1['S1P'] != 0) {
						$GP_1 = number_format(((($RSTp1['S1P']-$LastPurPrc)/$RSTp1['S1P'])*100),2)."%";
					}else{
						$GP_1 = "0.00%";
					}
					if($RSTp1['S2P'] != 0) {
						$GP_2 = number_format(((($RSTp1['S2P']-$LastPurPrc)/$RSTp1['S2P'])*100),2)."%";
					}else{
						$GP_2 = "0.00%";
					}
					if($RSTp1['S3P'] != 0) {
						$GP_3 = number_format(((($RSTp1['S3P']-$LastPurPrc)/$RSTp1['S3P'])*100),2)."%";
					}else{
						$GP_3 = "0.00%";
					}
					$MgrPrice = number_format($RSTp1['MgrPrice'],2);
					break;
				default :
					$MgrPrice = "0.00";
					break;
			}

			$ProPrice = ($DataPriceType == 'PRO') ? "(เริ่ม ".date("d/m/Y", strtotime($RSTp1['StartDate']))." ถึง ".date("d/m/Y", strtotime($RSTp1['EndDate'])).")" : "";

			$DataPrice .= "
			<div class='fw-bolder text-primary'>$HeaderPriceType $ProPrice</div>
			<div class='ms-3 me-3'>
				<div class='row pt-2'>";
					switch ($_SESSION['uClass']){
						case 0 :
						case 2 :
						case 3 :
						case 4 :
						case 18 :
						case 19 :
							$DataPrice .= "
							<div class='col-lg FixMgr'>
								<div class='form-group'>
									<label for='GrossPrice'>ต้นทุนล่าสุด (บาท)</label>
									<input type='text' class='form-control form-control-sm text-right' name='GrossPrice' id='GrossPrice' value='".number_format($LastPurPrc,2)."' readonly>
								</div>
							</div>";
						break;
						default: break;
					}
					$DataPrice .= "
					<div class='col-lg '>
						<div class='form-group'>
							<label for='P0'></span>ราคาตั้ง (บาท)</label>
							<input type='text' class='form-control form-control-sm text-right' name='P0' id='P0' value='".number_format($RSTp1['P0'],2)."' readonly>
						</div>
					</div>
					<div class='col-lg '>
						<div class='form-group'>
							<label for='P1'>ราคาขายปลีก SEMI (บาท)</label>
							<input type='text' class='form-control form-control-sm text-right' name='P1' id='P1' value='".number_format($RSTp1['P1'],2)."' readonly>
						</div>
					</div>
					<div class='col-lg '>
						<div class='form-group'>
							<label for='P2'>ราคาขายส่ง SEMI (บาท)</span></label>
							<input type='text' class='form-control form-control-sm text-right' name='P2' id='P2' value='".number_format($RSTp1['P2'],2)."' readonly>
						</div>
					</div>
				</div>
				<div class='row '>
					<div class='col-lg'>
						<div class='table-responsive'>
							<table class='table table-borderless'>
								<thead>
									<tr class='text-center'>
										<th width='10%'>Step ที่</th>
										<th width='30%'>จำนวน</th>
										<th width='30%'>ราคา (บาท)</th>";
										switch ($_SESSION['uClass']){
											case 0 :
											case 2 :
											case 3 :
											case 4 :
											case 18 :
											case 19 :
												$DataPrice .= "<th width='30%'>GP</th>";
											break;
											default: break;
										}
									$DataPrice .= "
									</tr>
								</thead>
								<tbody>";
									for($i = 1; $i <=3; $i++) { 
										$DataPrice .= "
										<tr>
											<td class='text-center'>".$i."</td>
											<td><input type='number' class='form-control form-control-sm text-right' name='S".$i."Q' id='S".$i."Q' value='".number_format($RSTp1['S'.$i.'Q'],0)."' placeholder='จำนวน Step ".$i."' readonly></td>
											<td>
												<input type='text' class='form-control form-control-sm text-right' name='S".$i."P' id='S".$i."P' value='".number_format($RSTp1['S'.$i.'P'],2)."' placeholder='ราคา Step ".$i."' readonly>
											</td>";
											switch ($_SESSION['uClass']){
												case 0 :
												case 2 :
												case 3 :
												case 4 :
												case 18 :
												case 19 :
													$DataPrice .= "
													<td>
														<div class='d-flex align-items-center'>
															<input type='text' class='form-control form-control-sm text-right' name='GP_".$i."' id='GP_".$i."' value='".${'GP_'.$i}."' readonly>
														</div>
													</td>";
												break;
												default: break;
											}
										$DataPrice .= "
										</tr>";
									}
								$DataPrice .= "
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class='row '>
					<div class='col-lg'>
						<div class='form-group'>
							<label for='MTPrice'>ราคาขายปลีก MT (บาท)</label>
							<input type='text' class='form-control form-control-sm text-right' name='MTPrice' id='MTPrice' value='".number_format($RSTp1['MTPrice'],2)."' readonly>
						</div>
					</div>
					<div class='col-lg '>
						<div class='form-group'>
							<label for='MTPrice2'>ราคาขายส่ง MT (บาท)</label>
							<input type='text' class='form-control form-control-sm text-right' name='MTPrice2' id='MTPrice2' value='".number_format($RSTp1['MTPrice2'],2)."' readonly>
						</div>
					</div>
					<div class='col-lg FixMgr'>
						<div class='form-group'>
							<label for='MgrPrice'>ราคาผู้จัดการ (บาท)</label>
							<input type='text' class='form-control form-control-sm text-right' name='MgrPrice' id='MgrPrice' value='$MgrPrice' readonly>
						</div>
					</div>
				</div>
			</div>";
			$DataPrice .= (($key+1) != count($ArrPriceType)) ? "<hr class='hrprice border-secondary'>" : "";
		}
	}

	$arrCol['DataPrice'] = $DataPrice;

	// สินค้าคงคลัง
	$ItemSQL = "SELECT TOP 1  '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
                    T0.[ItemCode], T0.[CodeBars], T0.[ItemName], T0.[InvntryUom], 
                    CASE
                        WHEN T0.[U_ProductStatus] = 'D' THEN 'D - Delete Item'
                        WHEN T0.[U_ProductStatus] = 'D21' THEN 'D21 - Delete Item (2021)'
                        WHEN T0.[U_ProductStatus] = 'D22' THEN 'D22 - Delete Item (2022)'
                        WHEN T0.[U_ProductStatus] = 'R' THEN 'R - Replace Item'
                        WHEN T0.[U_ProductStatus] = 'A' THEN 'A - Active Item'
                        WHEN T0.[U_ProductStatus] = 'W' THEN 'W - Watchout / Warning Item'
                        WHEN T0.[U_ProductStatus] = 'N' THEN 'N - New Item'
                        WHEN T0.[U_ProductStatus] = 'M' THEN 'M - Made to order Item'
                    ELSE 'NULL' END AS 'U_ProductStatus',                         
                    (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T4.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc', 
                    ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC),T0.LastPurDat) AS 'LastPurDat', DATEDIFF(m,ISNULL((SELECT TOP 1 P0.DocDate FROM OPDN P0 LEFT JOIN PDN1 P1 ON P0.DocEntry = P1.DocEntry WHERE P1.ItemCode = T0.ItemCode ORDER BY P0.DocEntry DESC),T0.LastPurDat),GETDATE()) AS 'Aging',
                    T1.Name AS 'Brand', T2.Name AS 'MainGroup', T3.Name AS 'SupGroup'
                FROM OITM T0
                LEFT JOIN [dbo].[@BRAND2]     T1 ON T0.[U_Brand2] = T1.[Code]
                LEFT JOIN [dbo].[@ITEMGROUP1] T2 ON T0.[U_Group1] = T2.[Code]
                LEFT JOIN [dbo].[@ITEMGROUP2] T3 ON T0.[U_Group2] = T3.[Code]
				LEFT JOIN KBI_DB2022.dbo.OITM T4 ON T0.ItemCode = T4.ItemCode
                WHERE T0.[ItemCode] = '".$_POST['ItemCode']."'";
	// echo $ItemSQL;
	$ItemQRY = SAPSelect($ItemSQL);
	$ItemRST = odbc_fetch_array($ItemQRY);
	$WhseSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
                    T0.[ItemCode], T0.[WhsCode], T1.[WhsName],
                    CASE
                        WHEN T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') THEN 'W100'
                        WHEN T0.WhsCode IN ('MT') THEN 'W101'
                        WHEN T0.WhsCode IN ('MT2') THEN 'W102'
                        WHEN T0.WhsCode IN ('TT-C') THEN 'W103'
                        WHEN T0.WhsCode IN ('OUL') THEN 'W104'
                        WHEN T0.WhsCode IN ('KB1','KB1.1') THEN 'W200'
                        WHEN T1.Location IN (2) THEN 'W300'
                        WHEN T1.Location IN (6,7,9) THEN 'W400'
                    ELSE 'W500' END AS 'WhsGroup', T0.[OnHand], T0.[OnOrder]
                FROM OITW T0
                LEFT JOIN OWHS T1 ON T0.[WhsCode] = T1.[WhsCode]
                WHERE T0.[ItemCode] = '".$_POST['ItemCode']."' AND (T0.[OnHand] !=0 OR T0.[OnOrder] != 0)
                ORDER BY 'WhsGroup', T0.[WhsCode]";
	$WhseQRY = SAPSelect($WhseSQL);
	$tempGroup = "";
	$PickSQL = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', 
                    T0.ItemCode, T0.WhsCode, SUM(T0.OpenQty) AS 'OpenQty', SUM(T0.Qty) AS 'Qty'
                FROM picker_sodetail T0
                LEFT JOIN picker_soheader T1 ON T0.DocEntry = T1.SODocEntry
                WHERE (T1.DocType = 'ORDR' AND (T1.StatusDoc BETWEEN 2 AND 8)) AND T0.ItemCode = '".$_POST['ItemCode']."'
                GROUP BY T0.ItemCode, T0.WhsCode";
	$PickQRY = MySQLSelectX($PickSQL);
	while($PickRST = mysqli_fetch_array($PickQRY)) {
		${$PickRST['ItemCode']."_".$PickRST['WhsCode']."_Qty"} = $PickRST['Qty'];
        ${$PickRST['ItemCode']."_".$PickRST['WhsCode']."_OpenQty"} = $PickRST['OpenQty'];
	}
	$output2 = "<table class='table table-sm table-bordered rounded rounded-3 overflow-hidden table-hover'>
					<thead style='font-size: 13px;'>
						<tr class='text-center'>
							<th rowspan='2'>ชื่อคลัง</th>
							<th colspan='5'>จำนวน (หน่วย)</th>";
							if($_SESSION['uClass'] == 0 || $_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4 || $_SESSION['uClass'] == 18 || $_SESSION['uClass'] == 19) {
								$output2 .= "<th width='15%' rowspan='2'>มูลค่ารวม</th>";
							}
			$output2 .= "</tr>
						<tr class='text-center'>
							<th width='12.5%'>คงคลัง</th>
							<th width='12.5%'>รอเบิก</th>
							<th width='12.5%'>เบิกแล้ว</th>
							<th width='12.5%'>คงเหลือ</th>
							<th width='12.5%'>กำลังสั่งซื้อ</th>
						</tr>
					</thead>
					<tbody style='font-size: 12px;'>";
			while($WhseRST = odbc_fetch_array($WhseQRY)) {
				if($tempGroup != $WhseRST['WhsGroup']) {
					$tempGroup = $WhseRST['WhsGroup'];
					$output2 .= "<tr><td colspan='7' class='fw-bolder text-primary' style='background-color: rgba(189, 189, 189, 0.15);'>".WhsGroupName($tempGroup)."</td></tr>";
				}
				if(isset(${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_Qty"})) {
					$DT1 = ${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_Qty"}-${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_OpenQty"};
					$DT2 = ${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_OpenQty"};
					$DT3 = $WhseRST['OnHand']-${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_OpenQty"};
					$DT4 = ($WhseRST['OnHand']-${$WhseRST['ItemCode']."_".$WhseRST['WhsCode']."_OpenQty"})*$ItemRST['LastPurPrc'];
				}else{
					$DT1 = 0;
					$DT2 = 0;
					$DT3 = $WhseRST['OnHand'];
					$DT4 = ($WhseRST['OnHand']*$ItemRST['LastPurPrc']);
				}
				$output2 .= "<tr>
								<td>".conutf8($WhseRST['WhsCode'])." - ".conutf8($WhseRST['WhsName'])."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnHand'],0))."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT1,0))."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT2,0))."</td>
								<td class='text-right fw-bolder text-primary'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($DT3,0))."</td>
								<td class='text-right'>".preg_replace('/\b'.'0'.'\b/i',"-",number_format($WhseRST['OnOrder'],0))."</td>";
								if($_SESSION['uClass'] == 0 || $_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4 || $_SESSION['uClass'] == 18 || $_SESSION['uClass'] == 19) {
									$output2 .= "<td class='text-right fw-bolder'>".preg_replace('/\b'.'0.00'.'\b/i',"-",number_format($DT4,2))." ฿</td>";
								}
				$output2 .= "</tr>";
			}
		$output2 .= "</tbody>
				</table>";

		$WhsSQL = "SELECT T0.WhsCode, T1.WhsName, T0.OnHand, T1.Location, T3.Location AS 'LocationName', ISNULL(T2.DfltWH, 'KSY') AS 'DfltWH', T2.SalUnitMsr, T0.Locked 
		FROM OITW T0
			LEFT JOIN OWHS T1 ON T0.WhsCode = T1.WhsCode
		LEFT JOIN OITM T2 ON T0.ItemCode = T2.ItemCode
		LEFT JOIN OLCT T3 ON T1.Location = T3.Code
		WHERE (T0.ItemCode = '".$_POST['ItemCode']."') AND T1.InActive = 'N'
		ORDER BY T1.Location, T0.WhsCode";
	 	$WhsQRY = SAPSelect($WhsSQL);
		$WhsAll  = 0;
		$WhsChk  = array('KB2','KSY','KSM','KBM','KB4');
		while ($result = odbc_fetch_array($WhsQRY)) {
			if(in_array(conutf8($result['WhsCode']), $WhsChk, TRUE)) {
				$WhsAll = $WhsAll + $result['OnHand'];
			}
		}

		$QtaSQL = "SELECT
			T0.ItemCode,
			SUM(CASE WHEN T0.CH = 'MT1' THEN T0.OnHand ELSE 0 END) AS 'MT1',
			SUM(CASE WHEN T0.CH = 'MT2' THEN T0.OnHand ELSE 0 END) AS 'MT2',
			SUM(CASE WHEN T0.CH = 'TTC' THEN T0.OnHand ELSE 0 END) AS 'TTC',
			SUM(CASE WHEN T0.CH = 'OUL' THEN T0.OnHand ELSE 0 END) AS 'OUL',
			SUM(CASE WHEN T0.CH = 'ONL' THEN T0.OnHand ELSE 0 END) AS 'ONL'
		FROM whsquota T0
		WHERE (T0.ItemCode = '".$_POST['ItemCode']."')
		GROUP BY T0.ItemCode";

		$Quota = "";
		if(ChkRowDB($QtaSQL) > 0) {
			$QtaRST = MySQLSelect($QtaSQL);
			$NotQuota = $WhsAll - ($QtaRST['MT1'] + $QtaRST['MT2'] + $QtaRST['TTC'] + $QtaRST['OUL'] + $QtaRST['ONL']);

			if($NotQuota == 0)    { $NotQuota = "-"; } else { $NotQuota = number_format($NotQuota,0); }
			if($QtaRST['MT1'] == 0) { $QtaMT1 = "-"; } else { $QtaMT1 = number_format($QtaRST['MT1'],0); }
			if($QtaRST['MT2'] == 0) { $QtaMT2 = "-"; } else { $QtaMT2 = number_format($QtaRST['MT2'],0); }
			if($QtaRST['TTC'] == 0) { $QtaTTC = "-"; } else { $QtaTTC = number_format($QtaRST['TTC'],0); }
			if($QtaRST['OUL'] == 0) { $QtaOUL = "-"; } else { $QtaOUL = number_format($QtaRST['OUL'],0); }
			if($QtaRST['ONL'] == 0) { $QtaONL = "-"; } else { $QtaONL = number_format($QtaRST['ONL'],0); }

			$HeadALL = ""; $BodyALL = "";
			$HeadMT1 = ""; $BodyMT1 = "";
			$HeadMT2 = ""; $BodyMT2 = "";
			$HeadTTC = ""; $BodyTTC = "";
			$HeadOUL = ""; $BodyOUL = "";
			$HeadONL = ""; $BodyONL = "";

			switch($_SESSION['DeptCode']) {
				case "DP005": $HeadTTC = " class='text-warning table-warning'"; $BodyTTC = " text-warning table-warning"; break;
				case "DP006": $HeadMT1 = " class='text-warning table-warning'"; $BodyMT1 = " text-warning table-warning"; break;
				case "DP007": $HeadMT2 = " class='text-warning table-warning'"; $BodyMT2 = " text-warning table-warning"; break;
				case "DP008": $HeadOUL = " class='text-warning table-warning'"; $BodyOUL = " text-warning table-warning"; break;
				case "DP003": $HeadONL = " class='text-warning table-warning'"; $BodyONL = " text-warning table-warning"; break;
				default:      $HeadALL = " class='text-success table-success'"; $BodyALL = " fw-bolder text-success table-success"; break;
			}

			$Quota .= "<div class='table-responsive'>";
			$Quota .= "<table class='table table-bordered table-hover' style='font-size: 13px;'>";
			$Quota .= "<thead class='text-center table-group-divider'>";
			$Quota .= "<tr>";
			$Quota .= "<th width='25%' style='background-color: #d9edf7;'>พร้อมขาย SAP<br/>KSY/KSM</th>";
			$Quota .= "<th $HeadALL width='12.5%'>ส่วน<br/>กลาง</th>";
			$Quota .= "<th $HeadMT1 width='12.5%'>โควต้า<br/>MT1</th>";
			$Quota .= "<th $HeadMT2 width='12.5%'>โควต้า<br/>MT2</th>";
			$Quota .= "<th $HeadTTC width='12.5%'>โควต้า<br/>TT</th>";
			$Quota .= "<th $HeadOUL width='12.5%'>โควต้า<br/>หน้าร้าน</th>";
			$Quota .= "<th $HeadONL width='12.5%'>โควต้า<br/>ออนไลน์</th>";
			$Quota .= "</tr>";
			$Quota .= "</thead>";
			$Quota .= "<tbody>";
			$Quota .= "<tr>";
			$Quota .= "<td class='text-right' style='background-color: #d9edf7; font-weight: bold;'>".number_format($WhsAll,0)."</td>";
			$Quota .= "<td class='text-right $BodyALL'>$NotQuota</td>";
			$Quota .= "<td class='text-right $BodyMT1'>$QtaMT1</td>";
			$Quota .= "<td class='text-right $BodyMT2'>$QtaMT2</td>";
			$Quota .= "<td class='text-right $BodyTTC'>$QtaTTC</td>";
			$Quota .= "<td class='text-right $BodyOUL'>$QtaOUL</td>";
			$Quota .= "<td class='text-right $BodyONL'>$QtaONL</td>";
			$Quota .= "</tr>";
			$Quota .= "</tbody>";
			$Quota .= "</table>";
			$Quota .= "</div>";
		} else {
			$Quota = "<p class=\"text-center text-muted\">ไม่พบโควต้าสินค้า</p>";
		}

	/* OUT PUT 2 => จำนวนสินค้าคงคลัง SAP */
	$arrCol['output2'] = $output2.$Quota;

	// เป้าที่ตั้งไว้
	$ItemCode = $_POST['ItemCode'];

	$CPTypeArr = ['Q', 'F', 'P', '2', 'O'];
	$TeamCodeArr = ['MT1', 'MT2', 'TT2', 'OUL', 'TT1', 'ONL'];
	foreach($CPTypeArr as $dataType) { 
		foreach($TeamCodeArr as $dataTeam) { 
			$Data[$dataType][$dataTeam] = 0;
		}
	}

	foreach($TeamCodeArr as $key=>$data) {
		$SQL1 = "SELECT DocNum, CPType FROM tarsku_header WHERE TeamCode = '$data' AND CANCELED = 'N' AND DATE(EndDate) >= NOW()";
		if(CHKRowDB($SQL1) != 0) {
			$QRY1 = MySQLSelectX($SQL1);
			while($RST1 = mysqli_fetch_array($QRY1)) {
				$SQL2 = 
				   "SELECT T0.ItemCode, T0.TargetTotal, T1.CPType, T1.TeamCode
					FROM tarsku_itemlist T0 
					LEFT JOIN tarsku_header T1 ON T1.DocNum = T0.DocNum
					WHERE T0.DocNum = '".$RST1['DocNum']."' AND T0.ItemCode = '$ItemCode' AND T1.CPType = '".$RST1['CPType']."' AND T0.RowStatus = 'A' AND T1.CANCELED = 'N'";
				if(CHKRowDB($SQL2) != 0) { 
					$RST2 = MySQLSelect($SQL2);
					$Data[$RST2['CPType']][$RST2['TeamCode']] = $Data[$RST2['CPType']][$RST2['TeamCode']]+$RST2['TargetTotal'];
				}
			}
		}
	}

	$DataTarget = "";
	foreach($CPTypeArr as $CPType) { 
		$OULTT1 = 0;
		switch ($CPType) {
			case 'Q': $NameCPType = 'สินค้าจอง (Quota)'; break;
			case 'F': $NameCPType = 'สินค้าต้องขาย (Focus)'; break;
			case 'P': $NameCPType = 'สินค้าโปรโมชั่น (Promotion)'; break;
			case '2': $NameCPType = 'สินค้ามือสอง (2nd Hand)'; break;
			case 'O': $NameCPType = 'อื่น ๆ'; break;
		}
		$DataTarget .= "
		<tr>
			<th>$NameCPType</th>";
			foreach($TeamCodeArr as $TeamCode) { 
				$SQL3 = "SELECT MainTeam FROM oslp WHERE Ukey = '".$_SESSION['ukey']."'";
				$ColorTeam = "";
				if(CHKRowDB($SQL3) != 0) { 
					$RST3 = MySQLSelect($SQL3);
					if($RST3['MainTeam'] == 'OUL' || $RST3['MainTeam'] == 'TT1') {
						if($TeamCode == 'OUL' || $TeamCode == 'TT1') {
							$ColorTeam = "table-warning";
						}
					}else{
						$ColorTeam = ($RST3['MainTeam'] == $TeamCode) ? "table-warning" : "";
					}
				}

				if($TeamCode == 'OUL'){
					$OULTT1 = $OULTT1+$Data[$CPType][$TeamCode];
				}elseif($TeamCode == 'TT1'){
					$OULTT1 = $OULTT1+$Data[$CPType][$TeamCode];
					$DataTarget .= "<td class='text-right $ColorTeam'>".$OULTT1."</td>";
				}else{
					$DataTarget .= "<td class='text-right $ColorTeam'>".$Data[$CPType][$TeamCode]."</td>";
				}
			}
		$DataTarget .= "
		</td>";
	}
	$arrCol['DataTarget'] = $DataTarget;
}

if ($_GET['a'] == 'SelectPriceList') {
	$sql = "SELECT T0.P0,T0.P1, T0.P2, T0.S1Q, T0.S1P, T0.S2Q, T0.S2P, T0.S3Q, T0.S3P, T0.MgrPrice, T0.MTPrice, T0.MTPrice2
			FROM pricelist T0
			WHERE T0.ItemCode = '".$_POST['ItemCode']."' AND T0.PriceType = '".$_POST['PriceType']."' AND T0.PriceStatus = 'A' LIMIT 1";
	$row = CHKRowDB($sql);
	if ($row == 1) {
		$result = MySQLSelect($sql);
		$arrCol['P0'] = $result['P0'];
		$arrCol['P1'] = $result['P1'];
		$arrCol['P2'] = $result['P2'];
		$arrCol['S1Q'] = $result['S1Q'];
		$arrCol['S1P'] = $result['S1P'];
		$arrCol['S2Q'] = $result['S2Q'];
		$arrCol['S2P'] = $result['S2P'];
		$arrCol['S3Q'] = $result['S3Q'];
		$arrCol['S3P'] = $result['S3P'];
		$sqlSAP =  "SELECT TOP 1 (CASE WHEN T0.LastPurDat = '2022-12-31' THEN ISNULL(T1.LastPurPrc, T0.LastPurPrc) ELSE T0.LastPurPrc END *1.07) AS 'LastPurPrc'
					FROM OITM T0 
					LEFT JOIN KBI_DB2022.dbo.OITM T1 ON T0.ItemCode = T1.ItemCode 
					WHERE T0.ItemCode = '".$_POST['ItemCode']."'";   
		$qrySAP = SAPSelect($sqlSAP);
		$resultSAP = odbc_fetch_array($qrySAP);
		$LastPurPrc = 0;
		if(isset($resultSAP['LastPurPrc'])) {
			$LastPurPrc = $resultSAP['LastPurPrc'];
		}
		switch ($_SESSION['uClass']){
			case 0 :
			case 2 :
			case 3 :
			case 4 :
			case 18 :
			case 19 :
				$arrCol['GrossPrice'] = $LastPurPrc;
				if($result['S1P'] != 0) {
					$arrCol['GP_1'] = number_format(((($result['S1P']-$LastPurPrc)/$result['S1P'])*100),2)."%";
				}else{
					$arrCol['GP_1'] = "0.00%";
				}
				if($result['S2P'] != 0) {
					$arrCol['GP_2'] = number_format(((($result['S2P']-$LastPurPrc)/$result['S2P'])*100),2)."%";
				}else{
					$arrCol['GP_2'] = "0.00%";
				}
				if($result['S3P'] != 0) {
					$arrCol['GP_3'] = number_format(((($result['S3P']-$LastPurPrc)/$result['S3P'])*100),2)."%";
				}else{
					$arrCol['GP_3'] = "0.00%";
				}
				$arrCol['MgrPrice'] = $result['MgrPrice'];
				break;
			default :
				$arrCol['MgrPrice'] = "";
				break;
		}
		$arrCol['MTPrice'] = $result['MTPrice'];
		$arrCol['MTPrice2'] = $result['MTPrice2'];
	}else{
		$arrCol['P0'] = "0";
		$arrCol['P1'] = "0";
		$arrCol['P2'] = "0";
		$arrCol['S1Q'] = "0";
		$arrCol['S1P'] = "0";
		$arrCol['S2Q'] = "0";
		$arrCol['S2P'] = "0";
		$arrCol['S3Q'] = "0";
		$arrCol['S3P'] = "0";
		$arrCol['MgrPrice'] = "0";
		$arrCol['MTPrice'] = "0";
		$arrCol['MTPrice2'] = "0";
		$arrCol['GP_1'] = "0.00%";
		$arrCol['GP_2'] = "0.00%";
		$arrCol['GP_3'] = "0.00%";
		$arrCol['GrossPrice'] = "0";
	}
	
}

if($_GET['a'] == 'GetImgPro') {
	$ItemCode = $_POST['ItemCode'];
	// โปรโมชั่น
	$arrCol['ImgPro'] = "";
	$SQLImg = "SELECT AttachID, CONCAT(FileDirName, '.', FileExt) AS 'FileName' FROM skubook_attach WHERE ItemCode = '$ItemCode' AND  TYPE = 9 AND FileStatus = 'A'";
	// $filesIMG_9 = glob("../../../../image/products/".$ItemCode."/9/*.{jpg,png}",GLOB_BRACE);
	if(CHKRowDB($SQLImg) != 0) {
		$QRYImg = MySQLSelectX($SQLImg);
		while($RSTImg = mysqli_fetch_array($QRYImg)) {
			$ts = date("Ymdhis");
			$btn_delete = "";
			if($_SESSION['DeptCode'] == 'DP002' || $_SESSION['DeptCode'] == 'DP003') {
				$btn_delete = "
					<div class='position-absolute p-1'>
						<a class='btn btn-outline-danger ' true='' onclick='DeletePro(".$RSTImg['AttachID'].")'><i class='fas fa-trash fa-fw fa-1x'></i></a>
					</div>";
			}

			$arrCol['ImgPro'] .= 
				"<div class='col-12 col-md-6 col-lg-4 col-xl-3'>
					<div class='card'>
						<div class='text-center' style='background-color: #000; border-radius: 10px 10px 10px 10px;'><a href='../../../../image/products/$ItemCode/9/".$RSTImg['FileName']."?v=$ts' target='_blank'>
							<img src='../../../../image/products/$ItemCode/9/".$RSTImg['FileName']."?v=$ts' class='card-img-top'></a>
						</div>
						$btn_delete
					</div>
				</div>";
		}
	}
}

if($_GET['a'] == 'DeletePro') {
	$ID = $_POST['ID'];
	$SQL1 = "SELECT ItemCode, FileDirName, FileExt FROM skubook_attach WHERE AttachID = $ID";
	$RST1 = MySQLSelect($SQL1);
	if(isset($RST1['FileDirName'])) {
		$ItemCode = $RST1['ItemCode'];
		unlink("../../../../image/products/$ItemCode/9/".$RST1['FileDirName'].".".$RST1['FileExt']);
		$arrCol['ItemCode'] = $ItemCode;
	}

	$SQL2 = "UPDATE skubook_attach SET FileStatus = 'I' WHERE AttachID = $ID";
	MySQLUpdate($SQL2);

	
}

// $arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
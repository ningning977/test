<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();
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
if ($_GET['a'] == 'read' ){
	$sql1 = "SELECT DISTINCT T0.LogiNum,DATE(T0.CreateDate) AS DocDate,T0.DriverName,T0.LcCar,CONCAT(T1.uName,'(',T1.uNickName,')') AS uLoad,T0.Printed

	         FROM logi_head T0
			      LEFT JOIN users T1 ON T0.uKeyCreate = T1.uKey
			 WHERE MONTH(T0.CreateDate) = ".$_POST['Mselect']." AND YEAR(T0.CreateDate) = ".$_POST['Yselect']."  
			 ORDER BY LogiNum DESC";
	$getLoad = MySQLSelectX($sql1);
    while ($result = mysqli_fetch_array($getLoad)) {
		$sql1 = "SELECT DISTINCT BillEntry,BillType FROM logi_detail WHERE LogiNum = '".$result['LogiNum']."'";
		$output .= "<tr>
						<td class='text-center'>".$result['LogiNum']."</td>
						<td class='text-center'>".date("d/m/Y",strtotime($result['DocDate']))."</td>
						<td class='text-left'>".$result['DriverName']."</td>
						<td class='text-center'>".$result['LcCar']."</td>
						<td>".$result['uLoad']."</td>
						<td class='text-center'><a href='javascript:void(0);' onclick=\"CallPrint('".$result['LogiNum']."')\"><i class='fas fa-print fa-fw fa-2x'></i></td>
					</tr>";
	}
}
if ($_GET['a'] == 'detail' ){
	$CountTotal = 0;
	$CHQ = 0;
	$CSH = 0;
	$AllBox = 0;
	$output2 = "";
	$sql1 = "SELECT T0.LogiNum,T0.CreateDate,T0.LcCar AS CarLC,T0.DriverName,CONCAT(T1.uName,' (',T1.uNickName,')') AS EmpLoad,
	                '".$_SESSION['uName']."' AS uName,'".$_SESSION['uNickName']."' AS uNickName
	         FROM logi_head T0
			      LEFT JOIN users T1 ON T0.uKeyCreate = T1.uKey
			 WHERE T0.LogiNum = '".$_POST['LogiNum']."'";
			 //echo $sql1;
	$LogiHead = MySQLSelect($sql1);
	$arrCol['LogiNum'] = $LogiHead['LogiNum'];
	$arrCol['DocDate'] = date("Y-m-d",strtotime($LogiHead['CreateDate']));
	$arrCol['EmpLoad'] = $LogiHead['EmpLoad'];
	$arrCol['DriverName'] = $LogiHead['DriverName'];
	$arrCol['CarLC'] = $LogiHead['CarLC'];
	$arrCol['NameCreate'] = $LogiHead['uName']." (".$LogiHead['uNickName'].")";


	$sql1 = "SELECT DISTINCT T0.LogiName,T0.CardCode,T1.CardName,T2.DocNum,T0.BillEntry,T0.BillType,
	                (SELECT COUNT(A0.ID) FROM logi_detail A0 WHERE A0.LogiNum = T0.LogiNum AND A0.BillType = T0.BillType AND A0.BillEntry = T0.BillEntry) AS TotalBox,
					(SELECT MAX(A1.CostCr) FROM logi_detail A1 WHERE A1.LogiNum = T0.LogiNum AND A1.BillType = T0.BillType AND A1.BillEntry = T0.BillEntry) AS CostCR,
					(SELECT Max(A2.CostCa) FROM logi_detail A2 WHERE A2.LogiNum = T0.LogiNum AND A2.BillType = T0.BillType AND A2.BillEntry = T0.BillEntry) AS CostCa
			FROM logi_detail T0
			    LEFT JOIN ocrd T1 ON T0.CardCode = T1.CardCode
				LEFT JOIN pack_header T2 ON T0.BillEntry = T2.BillEntry AND T0.BillType = T2.BillType
			WHERE T0.LogiNum = '".$_POST['LogiNum']."'";
	$getDetail = MySQLSelectX($sql1);
	while ($result = mysqli_fetch_array($getDetail)) {
		$CountTotal++;
		$LogiName = $result['LogiName'];
		$DocDate = "2022-12-11";
		switch($result['BillType']){
			case 'OINV' :
				$sql1 = "SELECT DocDate FROM OINV WHERE DocEntry = ".$result['BillEntry'];
				$getDoc = SAPSelect($sql1);
				$DataDoc = odbc_fetch_array($getDoc);
				break;
			case 'ODLN'	:
				$sql1 = "SELECT DocDate FROM OINV WHERE DocEntry = ".$result['BillEntry'];
				$getDoc = SAPSelect($sql1);
				$DataDoc = odbc_fetch_array($getDoc);
				break;
			default :
				$sql1 = "SELECT DateCreate AS DocDate FROM owas WHERE DocEntry = ".$result['BillEntry'];
				$DataDoc = MySQLSelect($sql1);
			break;	

		}
		$output .= "<tr>
						<td class='text-center'>".$CountTotal."</td>
						<td>".$LogiName."</td>
						<td>".$result['CardName']."</td>
						<td class='text-center'>".$result['DocNum']."</td>
						<td class='text-center'>".date("d/m/Y",strtotime($DataDoc['DocDate']))."</td>
						<td class='text-center'>".$result['TotalBox']."</td>
						<td class='text-right'><input type='text' class='form-control form-control-sm text-right' id='CR_".$result['BillType']."_".$result['BillEntry']."' value='".$result['CostCR']."' readonly></td>
						<td class='text-right'><input type='text' class='form-control form-control-sm text-right' id='CA_".$result['BillType']."_".$result['BillEntry']."' value='".$result['CostCa']."' readonly></td>
						<td class='text-center'>
								<button type='button' class='btn-delete btn btn-danger btn-sm w-100' onclick=\"EditRow('".$result['BillType']."',".$result['BillEntry'].")\"><i class='fas fa-trash-alt fa-fw fa-1x' aria-hidden='true'></i></button>
								<button type='button' class='btn-save btn btn-info btn-sm w-100' onclick=\"EditRow('".$result['BillType']."',".$result['BillEntry'].")\"><i class='fas fa-save fa-fw fa-1x' aria-hidden='true'></i></button>
						</td>
				   	</tr>	";
		$CHQ = $CHQ + $result['CostCR'];
		$CSH = $CSH + $result['CostCa'];
		$AllBox = $AllBox + $result['TotalBox'];

	}
	$CountTotal++;
	$output .= "<tr>
						<td class='text-center'>".$CountTotal."</td>
						<td><input type='text' class='form-control' id='newData' value=''></td>
						<td></td>
						<td></td>
						<td></td>
						<td><input type='text' class='form-control' id='newData' value=''></td>
						<td><input type='text' class='form-control text-right' id='newCR' value='0'></td>
						<td><input type='text' class='form-control text-right' id='newCA' value='0'></td>
						<td><button type='button' class='btn-delete btn btn-success btn-sm w-100' onclick=\"AddRow()\"><i class='fas fa-plus fa-fw fa-1x' aria-hidden='true'></i></button></td>
						
	            </tr>";
	$arrCol['Count'] = $AllBox;
	$arrCol['CHQ'] = number_format($CHQ,2);
	$arrCol['CSH'] = number_format($CSH,2);

	//$page = $CountTotal;
	$pages = ceil($CountTotal/10);
	$output2 = "<select class='form-select form-select-sm' name='ListPages' id='ListPages'>";
	for ($i=1;$i<=$pages;$i++){
		$output2 .= "<option value='P".$i."'>หน้า ".$i."</option>";
	}
	$output2 .="</select>";
	$arrCol['output2'] = $output2;
}

$arrCol['output'] = $output;


array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
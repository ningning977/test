<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content="Microsoft Excel 15">
<link rel=Stylesheet href=stylesheet.css>


<link href="../../../../css/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="../../../../css/iconset/ios7-set-filled-1/flaticon.css" rel="stylesheet" type="text/css">

<link href="../../../../css/bootstrap.min.css" rel="stylesheet">
<link href="../../../../css/datepicker.css" rel="stylesheet">


<link href="../../../../css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">


<link href="../../../../css/plugins/timeline.css" rel="stylesheet">

<link href="../../../../css/sb-admin-2.css" rel="stylesheet">
<link href="../../../../css/bootstrap-combobox.css" rel="stylesheet">
<link href="../../../../css/bootstrap-colorpicker.min.css" rel="stylesheet">

<link href="../../../../css/plugins/morris.css" rel="stylesheet">



<link rel="shortcut icon" href="../media/favicon/<?php echo @$system_info->site_favicon;?>"/>
<link rel="stylesheet" href="../css/selectize.default.css">
<link rel=Stylesheet href=stylesheet.css>

<?php
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

    function numTocha($number){ 
      $txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ'); 
      $txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'); 
      $number = str_replace(",","",$number); 
      $number = str_replace(" ","",$number); 
      $number = str_replace("บาท","",$number); 
      $number = explode(".",$number); 
      if(sizeof($number)>2){ 
      return 'ทศนิยมหลายตัวนะจ๊ะ'; 
      exit; 
      } 
      $strlen = strlen($number[0]); 
      $convert = ''; 
      for($i=0;$i<$strlen;$i++){ 
        $n = substr($number[0], $i,1); 
        if($n!=0){ 
          if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; } 
          elseif($i==($strlen-2) AND $n==2){  $convert .= 'ยี่'; } 
          elseif($i==($strlen-2) AND $n==1){ $convert .= ''; } 
          else{ $convert .= $txtnum1[$n]; } 
          $convert .= $txtnum2[$strlen-$i-1]; 
        } 
      } 
      
      $convert .= 'บาท'; 
      if($number[1]=='0' OR $number[1]=='00' OR 
      $number[1]==''){ 
      $convert .= 'ถ้วน'; 
      }else{ 
      $strlen = strlen($number[1]); 
      for($i=0;$i<$strlen;$i++){ 
      $n = substr($number[1], $i,1); 
        if($n!=0){ 
        if($i==($strlen-1) AND $n==1){$convert 
        .= 'เอ็ด';} 
        elseif($i==($strlen-2) AND 
        $n==2){$convert .= 'ยี่';} 
        elseif($i==($strlen-2) AND 
        $n==1){$convert .= '';} 
        else{ $convert .= $txtnum1[$n];} 
        $convert .= $txtnum2[$strlen-$i-1]; 
        } 
      } 
      $convert .= 'สตางค์'; 
      } 
      return $convert; 
      } 

?>
<style>

	{mso-displayed-decimal-separator:"\.";
	mso-displayed-thousand-separator:"\,";}
@page
	{margin:.75in .45in .75in .45in;
	mso-header-margin:.3in;
	mso-footer-margin:.3in;
  mso-horizontal-page-align:center;}
 @media print {
            body { padding-bottom: 0; }
            [role="main"] { padding-top: 0; }
            .hide-print { display: none !important; }
 }
 p.test {
    word-wrap: break-word!important;;
}
</style>


</head>

<body link="#0563C1" vlink="#954F72">
<table border=0 cellpadding=0 cellspacing=0 width=809 style='border-collapse:collapse;table-layout:fixed;width:608pt; margin: 0 auto;'>
      <col width=11 style='mso-width-source:userset;mso-width-alt:402;width:8pt'>
      <col width=45 style='mso-width-source:userset;mso-width-alt:1645;width:34pt'>
      <col width=20 style='mso-width-source:userset;mso-width-alt:731;width:15pt'>
      <col width=79 style='mso-width-source:userset;mso-width-alt:2889;width:59pt'>
      <col width=117 style='mso-width-source:userset;mso-width-alt:4278;width:88pt'>
      <col width=15 style='mso-width-source:userset;mso-width-alt:548;width:11pt'>
      <col width=11 style='mso-width-source:userset;mso-width-alt:402;width:8pt'>
      <col width=53 style='mso-width-source:userset;mso-width-alt:1938;width:40pt'>
      <col width=62 style='mso-width-source:userset;mso-width-alt:2267;width:47pt'>
      <col width=46 style='mso-width-source:userset;mso-width-alt:1682;width:35pt'>
      <col width=70 style='mso-width-source:userset;mso-width-alt:2560;width:53pt'>
      <col width=15 style='mso-width-source:userset;mso-width-alt:548;width:11pt'>
      <col width=40 style='mso-width-source:userset;mso-width-alt:1462;width:30pt'>
      <col width=30 style='mso-width-source:userset;mso-width-alt:1097;width:23pt'>
      <col width=85 style='mso-width-source:userset;mso-width-alt:3108;width:64pt'>
      <col width=99 style='mso-width-source:userset;mso-width-alt:3620;width:74pt'>
      <col width=11 style='mso-width-source:userset;mso-width-alt:402;width:8pt'>
 <tr height=9 style='mso-height-source:userset;height:6.75pt'>
      <td height=9 width=11 style='height:6.75pt;width:8pt'></td>
      <td width=45 style='width:34pt'></td>
      <td width=20 style='width:15pt'></td>
      <td width=79 style='width:59pt'></td>
      <td width=117 style='width:88pt'></td>
      <td width=15 style='width:11pt'></td>
      <td width=11 style='width:8pt'></td>
      <td width=53 style='width:40pt'></td>
      <td width=62 style='width:47pt'></td>
      <td width=46 style='width:35pt'></td>
      <td width=70 style='width:53pt'></td>
      <td width=15 style='width:11pt'></td>
      <td width=40 style='width:30pt'></td>
      <td width=30 style='width:23pt'></td>
      <td width=85 style='width:64pt'></td>
      <td width=99 style='width:74pt'></td>
      <td width=11 style='width:8pt'></td>
 </tr>
 <?php
  $billData = $_GET['docnum'];
  $sql1 ="SELECT * FROM  pita_billing WHERE DocNum = '".$billData."' AND DocStatus = 'A'";
  $getBill = MySQLSelectX($sql1);
  //echo $sql1;
  $perziix = 1;
  while ($printBill = mysqli_fetch_array($getBill)){
    $searchBill = $printBill['CardCode'];
    $dateBill = $printBill['CreateDate'];
    $billNo = $printBill['DocNum'];
    $tranidBill[$perziix] = $printBill['TransID'];
    $CreateUkey = $printBill['CreateUkey']; 
    $perziix++;
  }
  $qry = "SELECT CONCAT(uName,' ',uLastName) AS Creater FROM users WHERE uKey = '$CreateUkey'";
  $resultCreat = MySQLSelect($qry);
  $creater = $resultCreat['Creater'];

//=== ดึงข้อมูลลูกค้า ==== 
$sqlSAP = "SELECT
              OCRD.[CardCode],OCRD.[BillToDef] AS 'CardName',OCRD.[LicTradNum],
              CRD1.[Street],CRD1.[Block],CRD1.[City],
              OCRD.[Phone1],OCRD.[Phone2],OCRD.[Cellular],OCRD.[Fax],
              OCTG.[PymntGroup],OCRD.[U_ChqCond],CRD1.[ZipCode]

          FROM OCRD
              LEFT JOIN CRD1 ON OCRD.[CardCode] = CRD1.[CardCode] AND OCRD.[BillToDef] = CRD1.Address
              LEFT JOIN OCTG ON OCRD.[GroupNum] = OCTG.[GroupNum]
          WHERE OCRD.[CardCode] = '".$searchBill."'
          AND (CRD1.[AdresType] = 'B') ";
          //echo $sqlSAP;
$qrySAP = PITASelect($sqlSAP);
$DataCus = odbc_fetch_array($qrySAP);
if(isset($DataCus['CardCode'])) {
    $cusID = $DataCus['CardCode'];
    $cusName = conutf8($DataCus['CardName']);
    $cusAddress1 = conutf8($DataCus['Street']);
    $cusAddress2 = conutf8($DataCus['Block'])."&nbsp;&nbsp;".conutf8($DataCus['City'])."&nbsp;&nbsp;".$DataCus['ZipCode'];
    $cusPhone = conutf8($DataCus['Phone1']).", ".conutf8($DataCus['Phone2']);
    $cusFax = conutf8($DataCus['Fax']);
    $cusTax = $DataCus['LicTradNum'];
    $cusCredit = conutf8($DataCus['PymntGroup']);
    $cusChq = conutf8($DataCus['U_ChqCond']);
    if (strlen($cusChq) > 148){
      $a1 = substr($cusChq,0,148);
      $a2 = substr($cusChq,148);
      $cusChq = $a1."</br>".$a2;
    }
}

//ดึงบิล

  $BillAdded = "T0.[TransID] = '".$tranidBill[1]."'";
  for ($x=2;$x<=($perziix-1);$x++){
    $BillAdded = $BillAdded." OR T0.[TransID] = '".$tranidBill[$x]."'";
  }
  $sql2 = "SELECT P0.* 
              FROM (SELECT T0.[TransID],T0.[RefDate],
                    CASE
                        WHEN (T0.[TransType] = '13') THEN T0.[Ref2]
                        WHEN (T0.[TransType] = '14') THEN (SELECT ORIN.[NumAtCard] FROM ORIN WHERE ORIN.[DocNum] = T0.[BaseRef])
                        ELSE T0.[Ref3Line] END AS 'RefNo',
                        T0.[DueDate], (T0.[Debit]-T0.[Credit]) AS 'Balanced',T0.[BaseRef] AS 'Remark',
                        (T0.[BalScDeb]-T0.[BalScCred]) AS 'ToPaid'
              FROM JDT1 T0
              WHERE T0.Account = '1130-01' AND (T0.[TransType] = '13' OR T0.[TransType] = '14' OR T0.[TransType] = '30') 
              AND ((T0.[BalScDeb]-T0.[BalScCred]) != 0 OR T0.[MthDate] IS NULL) 
              AND (".$BillAdded.")) P0
              ORDER BY P0.RefDate ASC,P0.RefNo";
              // echo $sql2;

$CountBill=1;
$billTotalAmont = 0;
$qrySAP2 = PITASelect($sql2);
//echo $sql2;
while ($DataBill = odbc_fetch_array($qrySAP2)){
  $billTransID[$CountBill] = $DataBill['TransID'];
  $billRefDate[$CountBill] = $DataBill['RefDate'];
  $billRefNo[$CountBill] = $DataBill['RefNo'];
  $billDueDate[$CountBill] = $DataBill['DueDate'];
  $billAmount[$CountBill] = $DataBill['Balanced'];
  $billPaid[$CountBill] = $DataBill['ToPaid'];
  $billRemark[$CountBill] = conutf8($DataBill['Remark']);
  $billTotalAmont = $billTotalAmont + $billPaid[$CountBill];
  $CountBill++;
}
$CountBill = $CountBill-1;

 ?>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=12 rowspan=2 class=xl96>บริษัท พีต้า อินเตอร์เทรด จำกัด</td>
  <td></td>
  <td colspan=3 rowspan=2 class=xl118 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black'>ใบวางบิล</td>
 </tr>
 <tr height=17 style='mso-height-source:userset;height:12.75pt'>
  <td height=17 style='height:12.75pt'></td>
  <td></td>
 </tr>
 <tr height=20 style='mso-height-source:userset;height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=12 class=xl98>330,332 ถนนรามอินทรา (กม4.)
  แขวงท่าแร้ง<span style='mso-spacerun:yes'>  </span>เขตบางเขน</td>
  <td colspan=4 style='mso-ignore:colspan'></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.75pt'>
  <td height=21 style='height:15.75pt'></td>
  <td colspan=12 class=xl98>กรุงเทพมหานคร<span style='mso-spacerun:yes'> 
  </span>10230 TEL. 02-509-3022 FAX.02-509-3024</td>
  <td colspan=4 style='mso-ignore:colspan'></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=12 class=xl98>เลขประจำตัวผู้เสียภาษี 0105562147521</td>
  <td colspan=4 style='mso-ignore:colspan'></td>
 </tr>
 <tr height=12 style='mso-height-source:userset;height:9.0pt'>
  <td height=12 style='height:9.0pt'></td>
  <td colspan=15 class=xl97></td>
  <td></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=2 class=xl169>&nbsp;รหัสลูกค้า</td>
  <td colspan=7 class=xl155><?php echo $searchBill;?></td>
  <td colspan=3 class=xl137>&nbsp;เลขที่ใบวางบิล</td>
  <td colspan=2 class=xl155><?php echo $billNo;?></td>
  <td class=xl74>&nbsp;</td>
  <td class=xl67>&nbsp;</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=2 class=xl139>&nbsp;ชื่อลูกค้า</td>
  <td colspan=7 class=xl98><?php echo $cusName;?></td>
  <td colspan=3 class=xl139>&nbsp;วันที่</td>
  <td colspan=2 class=xl165><?php echo date("d/m/Y",strtotime($dateBill));?></td>
  <td></td>
  <td class=xl68>&nbsp;</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=2 class=xl139>&nbsp;ที่อยู่</td>
  <td colspan=7 class=xl98><?php echo $cusAddress1?></td>
  <td colspan=3 rowspan=3 class=xl136>&nbsp;</td>
  <td colspan=2 rowspan=3 class=xl97></td>
  <td></td>
  <td class=xl68>&nbsp;</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=2 class=xl139>&nbsp;</td>
  <td colspan=7 class=xl98><?php echo $cusAddress2;?></td>
  <td></td>
  <td class=xl68>&nbsp;</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=2 class=xl139>&nbsp;โทรศัพท์</td>
  <td colspan=2 align=left style='mso-ignore:colspan'><?php echo $cusPhone;?></td>
  <td colspan=2 class=xl97>Fax.</td>
  <td colspan=3 class=xl98 style='border-right:1.0pt solid black'><?php echo $cusFax;?></td>
  <td></td>
  <td class=xl68>&nbsp;</td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 style='height:15.75pt'></td>
  <td colspan=3 class=xl170>&nbsp;เลขประจำตัวผู้เสียภาษี</td>
  <td colspan=6 class=xl156><?php echo $cusTax;?></td>
  <td class=xl76 colspan=3 align=left style='mso-ignore:colspan'>&nbsp;เงือนไขการชำระเงิน</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69 align=left><?php echo $cusCredit;?></td>
  <td class=xl69>&nbsp;</td>
  <td class=xl70>&nbsp;</td>
 </tr>
 <tr height=13 style='mso-height-source:userset;height:9.75pt'>
  <td height=13 style='height:9.75pt'></td>
  <td colspan=15 class=xl97></td>
  <td></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:19.5pt'>
  <td height=26 style='height:19.5pt'></td>
  <td class=xl77>ลำดับ</td>
  <td colspan=2 class=xl89 style='border-right:.5pt solid black'>วัน/เดือน/ปี</td>
  <td colspan=3 class=xl89 style='border-right:.5pt solid black;border-left:
  none'>เลขที่ใบแจ้งหนี้</td>
  <td colspan=2 class=xl89 style='border-right:.5pt solid black;border-left:
  none'>วันครบกำหนด</td>
  <td colspan=3 class=xl90>จำนวนเงิน</td>
  <td colspan=3 class=xl89 style='border-right:.5pt solid black'>จำนวนเงินที่เรียกเก็บ</td>
  <td colspan=2 class=xl90 style='border-right:1.0pt solid black'>หมายเหตุ</td>
 </tr>
  <!---- #1 ------>
 <tr class=xl78 height=28 style='mso-height-source:userset;height:21.0pt'>
  <td height=28 class=xl78 style='height:21.0pt'></td>
  <td class=xl80 style='border-top:none'>1</td> 
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'><?php echo date("d/m/Y",strtotime($billRefDate[1]));?></td>
  <td colspan=3 class=xl100 style='border-right:.5pt solid black;border-left:none'><?php echo $billRefNo[1];?></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black;border-left:none'><?php echo date("d/m/Y",strtotime($billDueDate[1]));?></td>
  <td colspan=3 class=xl154><?php echo number_format($billAmount[1],2);?>&nbsp;</td>
  <td colspan=3 class=xl157 style='border-right:.5pt solid black'><?php echo number_format($billPaid[1],2);?>&nbsp;</td>
  <td colspan=2 class=xl101 style='border-right:1.0pt solid black'><?php echo $billRemark[1];?></td>
 </tr>
<!------ LOOP----------->
<?php
  if ($CountBill >=2){
    for ($i=2;$i<=$CountBill;$i++){
      echo "<tr class=xl78 height=28 style='mso-height-source:userset;height:21.0pt'>
                <td height=28 class=xl78 style='height:21.0pt'></td>
                <td class=xl81>".$i."</td> 
                <td colspan=2 class=xl87 style='border-right:.5pt solid black'>".date("d/m/Y",strtotime($billRefDate[$i]))."</td>
                <td colspan=3 class=xl82 style='border-right:.5pt solid black;border-left:none'>".$billRefNo[$i]."</td>
                <td colspan=2 class=xl87 style='border-right:.5pt solid black;border-left:none'>".date("d/m/Y",strtotime($billDueDate[$i]))."</td>
                <td colspan=3 class=xl85>".number_format($billAmount[$i],2)."&nbsp;</td>
                <td colspan=3 class=xl84 style='border-right:.5pt solid black'>".number_format($billPaid[$i],2)."&nbsp;</td>
                <td colspan=2 class=xl78 style='border-right:1.0pt solid black'>".$billRemark[$i]."</td>
            </tr>";
    } 
  }
  $loopNo = 15-$CountBill;
  for ($b=1;$b<=$loopNo;$b++){
      echo  "<tr class=xl78 height=28 style='mso-height-source:userset;height:21.0pt'>
                  <td height=28 class=xl78 style='height:21.0pt'></td>
                  <td class=xl81></td>
                  <td colspan=2 class=xl87 style='border-right:.5pt solid black'>&nbsp;</td>
                  <td colspan=3 class=xl82 style='border-right:.5pt solid black;border-left:none'>&nbsp;</td>
                  <td colspan=2 class=xl87 style='border-right:.5pt solid black;border-left:none'>&nbsp;</td>
                  <td colspan=3 class=xl85 style='border-right:.5pt solid black;border-left:none'>&nbsp;</td>
                  <td colspan=3 class=xl84 style='border-right:.5pt solid black;border-left:none'>&nbsp;</td>
                  <td colspan=2 class=xl78 style='border-right:1.0pt solid black;border-left:none'>&nbsp;</td>
              </tr>";

  }?>
 <!------ #บรรทัดสุดท้าย ----->
 <tr class=xl78 height=28 style='mso-height-source:userset;height:21.0pt'>
  <td height=28 class=xl78 style='height:21.0pt'></td>
  <td class=xl79>&nbsp;</td>
  <td colspan=2 class=xl110 style='border-right:.5pt solid black'>&nbsp;</td>
  <td colspan=3 class=xl93 style='border-right:.5pt solid black;border-left:
  none'>&nbsp;</td>
  <td colspan=2 class=xl110 style='border-right:.5pt solid black;border-left:
  none'>&nbsp;</td>
  <td colspan=3 class=xl108>&nbsp;</td>
  <td colspan=3 class=xl107 style='border-right:.5pt solid black'>&nbsp;</td>
  <td colspan=2 class=xl103 style='border-right:1.0pt solid black'>&nbsp;</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=16 class=xl130 style='border-right:1.0pt solid black'>**
  ห้ามจ่ายเป็นเงินสด โปรดจ่ายเป็นเช็คขีดคร่อม ในนาม บริษัท พีต้า
  อินเตอร์เทรด จำกัด เท่านั้น **</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=8 rowspan=2 class=xl147 style='border-bottom:.5pt solid black'>&nbsp;รวม  <?php echo $CountBill;?> ฉบับ</td>
  <td colspan=3 class=xl144 style='border-right:.5pt solid black'>&nbsp;รวมเงิน</td>
  <td colspan=3 rowspan=2 class=xl159 style='border-right:.5pt solid black;
  border-bottom:.5pt solid black'><?php echo number_format($billTotalAmont,2);?>&nbsp;</td>
  <td colspan=2 rowspan=2 class=xl126 style='border-right:1.0pt solid black;
  border-bottom:.5pt solid black'>&nbsp;</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=3 class=xl166 style='border-right:.5pt solid black'>&nbsp;GRAND TOTAL</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td colspan=16 class=xl133 style='border-right:1.0pt solid black'>&nbsp;</td>
 </tr>
 <tr height=31 style='mso-height-source:userset;height:23.25pt'>
  <td height=31 style='height:23.25pt'></td>
  <td class=xl71 align=left>&nbsp;บาท<span style='mso-spacerun:yes'>  </span></td>
  <td class=xl72></td>
  <td colspan=13 class=xl151>*** <?php echo numTocha(number_format($billTotalAmont,2));?> ***</td>
  <td class=xl68>&nbsp;</td>
 </tr>
 <tr >
  <td style='height:45.25pt'></td>
  <td colspan=3 class=xl105 style='vertical-align: text-top;'>&nbsp;กำหนดวางบิล-รับเช็ค</td>
  <td colspan=12 style='vertical-align: text-top;' class=xl150><?php echo $cusChq;?></p></td>
  <td class=xl70>&nbsp;</td>
 </tr>
 <tr height=11 style='mso-height-source:userset;height:8.25pt'>
  <td height=11 style='height:8.25pt'></td>
  <td colspan=15 class=xl97></td>
  <td></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td class=xl73 colspan=2 align=left style='mso-ignore:colspan'>&nbsp;ชำระโดย</td>
  <td class=xl174>&nbsp;<input type="checkbox">&nbsp;</td>
  <td class=xl74 align=left>&nbsp;เงินสด CASH</td>
  <td class=xl67>&nbsp;</td>
  <td class=xl74>&nbsp;</td>
  <td rowspan=2 class=xl140>ผู้วางบิล</td>
  <td colspan=3 rowspan=2 class=xl142 style='border-bottom:.5pt dotted black'>&nbsp;</td>
  <td class=xl74>&nbsp;</td>
  <td colspan=5 rowspan=2 class=xl112 width=265 style='border-right:1.0pt solid black;
  width:199pt'>&nbsp;ข้าพเจ้าได้รับใบกำกับภาษีและรับวางบิลตามรายการข้างต้นถูกต้องเรียบร้อยแล้ว</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td class=xl75>&nbsp;</td>
  <td></td>
  <td class=xl173>&nbsp;<input type="checkbox">&nbsp;</td>
  <td align=left>&nbsp;เช็ค CHEQUE</td>
  <td class=xl68>&nbsp;</td>
  <td></td>
  <td></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td class=xl75 colspan=2 align=left style='mso-ignore:colspan'>&nbsp;วันที่นัดรับ<span
  style='mso-spacerun:yes'> </span></td>
  <td class=xl65>&nbsp;</td>
  <td class=xl65>&nbsp;</td>
  <td class=xl68>&nbsp;</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td class=xl75 colspan=2 align=left style='mso-ignore:colspan'>&nbsp;ผู้รับวางบิล</td>
  <td class=xl65>&nbsp;</td>
  <td class=xl65>&nbsp;</td>
  <td class=xl68>&nbsp;</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 style='height:15.0pt'></td>
  <td class=xl75 align=left>&nbsp;เช็ค</td>
  <td></td>
  <td class=xl65>&nbsp;</td>
  <td class=xl65>&nbsp;</td>
  <td class=xl68>&nbsp;</td>
  <td></td>
  <td align=left>วันที่</td>
  <td colspan=3 class=xl143>&nbsp;</td>
  <td></td>
  <td class=xl75 align=left>&nbsp;วันที่</td>
  <td></td>
  <td class=xl66 style='border-top:none'>&nbsp;</td>
  <td class=xl66 style='border-top:none'>&nbsp;</td>
  <td class=xl68>&nbsp;</td>
 </tr>
 <tr height=12 style='mso-height-source:userset;height:9.0pt'>
  <td height=12 style='height:9.0pt'></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl70>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl76>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl69>&nbsp;</td>
  <td class=xl70>&nbsp;</td>
 </tr>
 <tr height=0 style='display:none'>
  <td width=11 style='width:8pt'></td>
  <td width=45 style='width:34pt'></td>
  <td width=20 style='width:15pt'></td>
  <td width=79 style='width:59pt'></td>
  <td width=117 style='width:88pt'></td>
  <td width=15 style='width:11pt'></td>
  <td width=11 style='width:8pt'></td>
  <td width=53 style='width:40pt'></td>
  <td width=62 style='width:47pt'></td>
  <td width=46 style='width:35pt'></td>
  <td width=70 style='width:53pt'></td>
  <td width=15 style='width:11pt'></td>
  <td width=40 style='width:30pt'></td>
  <td width=30 style='width:23pt'></td>
  <td width=85 style='width:64pt'></td>
  <td width=99 style='width:74pt'></td>
  <td width=11 style='width:8pt'></td>
 </tr>
</table>
<script type="text/javascript">
    window.print();
</script>
</body>
</html>

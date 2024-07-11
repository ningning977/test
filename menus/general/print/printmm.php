<?php session_start();
include('../../../../core/config.core.php');
include('../../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');

if(!isset($_GET['DocEntry'])) {
    echo '<script type="text/javascript">window.location="../../../../"; </script>';
} else {
    $DocEntry = $_GET['DocEntry'];
    $HeaderSQL = 
    "SELECT
        T0.DocEntry, T0.DocNum, T0.DocDate, T0.DocTitle, T0.DocMention, T0.DocCopyTo, T0.DocDetail, T0.DocSignOff, CONCAT(T1.uName,' ',T1.uLastName) AS 'CreateName'
    FROM memo_header T0
    LEFT JOIN users T1 ON T0.CreateUkey = T1.uKey
    WHERE T0.DocEntry = $DocEntry LIMIT 1";
    $Rows = CHKRowDB($HeaderSQL);
    if($Rows > 0) {
        $HeaderRST = MySQLSelect($HeaderSQL);
        $Mention = explode(",",$HeaderRST['DocMention']);
        $DocMention = "";
        for($l = 0; $l < count($Mention); $l++) {
            $GetNameSQL = "SELECT CONCAT(uName,' ',uLastName) AS 'MentionName' FROM users WHERE ukey = '".$Mention[$l]."'";
            // echo $GetNameSQL;
            $GetNameRST = MySQLSelect($GetNameSQL);
            $DocMention .= "คุณ".$GetNameRST['MentionName'];
            if($l != count($Mention)-1) {
                $DocMention .= ", ";
            }
        }

        $AttachSQL = "SELECT FileOriName FROM memo_attach WHERE DocEntry = $DocEntry AND FileStatus = 'A'";
        $Rows = ChkRowDB($AttachSQL);
        $Attach = "";
        if($Rows > 0) {
            $AttachQRY = MySQLSelectX($AttachSQL);
            $no = 1;
            while($AttachList = mysqli_fetch_array($AttachQRY)) {
                // echo $AttachList['FileOriName'];
                $Attach .= $AttachList['FileOriName'];
                if($no != $Rows) {
                    $Attach .= ", ";
                }
                $no++;
            }
        }

        

        $ApproveSQL = 
        "SELECT
            T0.VisOrder+1 AS 'VisOrder', T0.AppUkeyReq AS 'AppUkey',
            CONCAT(T1.uName,' ',T1.uLastName) AS 'AppName', T0.AppState, T0.AppDate, T0.AppRemark, IFNULL(T1.UserSign,NULL) AS 'AppSign', T2.PositionName
        FROM memo_approve T0
        LEFT JOIN users T1 ON T0.AppUkeyReq = T1.uKey
        LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
        WHERE T0.DocEntry = $DocEntry ORDER BY T0.ApproveID ASC";
        $Rows = ChkRowDB($ApproveSQL);
        $ShowApprove = "";
        if($Rows > 0) {
            $col_size = 3*$Rows;
            $offset_size = 12-$col_size;
            $ApproveQRY = MySQLSelectX($ApproveSQL);
            $no = 0;
            while($ApproveRST = mysqli_fetch_array($ApproveQRY)) {
                $no++;
                ${"APPUkey_".$no}     = $ApproveRST['AppUkey'];
                ${"APPName_".$no}     = $ApproveRST['AppName'];
                ${"APPPosition_".$no} = $ApproveRST['PositionName'];
                ${"APPState_".$no}    = $ApproveRST['AppState'];
                ${"APPRemark_".$no}   = $ApproveRST['AppRemark'];
                ${"APPDate_".$no}     = date("d/m/Y",strtotime($ApproveRST['AppDate']));
                ${"AppSign_".$no}     = $ApproveRST['AppSign'];
            }
            if($Rows < 4) {
                $ShowApprove .= "<div class='col-$offset_size mb-2'></div>";
            }
            for($l = 1; $l <= $Rows; $l++) {
                $next = $l+1;
                if(${"APPState_".$l} == "Y" ) {
                    $IconApp    = "&#9745;";
                    $IconNotApp = "&#9744";
                } else {
                    $IconApp    = "&#9744;";
                    $IconNotApp = "&#9745";
                }

                if(${"AppSign_".$l} == NULL)  {
                    $Signature = ${"APPName_".$l}; 
                } else {
                    $Signature = "<img src='../../../../image/signature/".${"AppSign_".$l}."' style='max-height: 32px;' />"; 
                }

                if($l != $Rows && ${"APPUkey_".$next} == "42b4e5ab67feb54da8216a5439fd6dcb") {
                    $Signature  = NULL;
                }

                $AppDate = ${"APPDate_".$l};

                if(${"APPUkey_".$l} == "42b4e5ab67feb54da8216a5439fd6dcb") {
                    $IconApp    = "&#9744;";
                    $IconNotApp = "&#9744";
                    $Signature  = NULL;
                    $AppDate    = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }

                $ShowApprove .= "<div class='col-3 mb-2'>";
                    $ShowApprove .= "<table class='table table-borderless table-sm' style='color: #000; margin: 0 auto;'>";
                        $ShowApprove .= "<tr>";
                            $ShowApprove .= "<td width='50%'><span style='font-size: 14px;'>$IconApp</span> อนุมัติ</td>";
                            $ShowApprove .= "<td width='50%'><span style='font-size: 14px;'>$IconNotApp</span> ไม่อนุมัติ</td>";
                        $ShowApprove .= "</tr>";
                        $ShowApprove .= "<tr>";
                            $ShowApprove .= "<td colspan='2' class='align-top' style='height: 84px;'><strong>ความคิดเห็น:</strong><br/>".${"APPRemark_".$l}."&nbsp;</td>";
                        $ShowApprove .= "</tr>";
                        $ShowApprove .= "<tr>";
                            $ShowApprove .= "<td colspan='2' class='align-bottom text-center' style='border-bottom: 1px dotted #000; height: 42px;'>$Signature</td>";
                        $ShowApprove .= "</tr>";
                        $ShowApprove .= "<tr>";
                            $ShowApprove .= "<td colspan='2' class='text-center'>(คุณ".${"APPName_".$l}.")<br/><small>".${"APPPosition_".$l}."</small></td>";
                        $ShowApprove .= "</tr>";
                        $ShowApprove .= "<tr>";
                            $ShowApprove .= "<td colspan='2' class='text-center'>วันที $AppDate</td>";
                        $ShowApprove .= "</tr>";
                    $ShowApprove .= "</table>";
                $ShowApprove .= "</div>";
            }
        }





        $rowsperpage = 15; // row per page
        $pages = 1;
?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="../../../../css/main/app.css" rel="stylesheet" />
                <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>
                <link href="../../../../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />

                <title><?php echo $HeaderRST['DocNum']." - ".$HeaderRST['DocTitle']; ?></title>
                <style rel="stylesheet" type="text/css">
                    @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600&display=swap');
                    html, body {
                        background-color: #FFFFFF;
                        font-family: 'Sarabun';
                        font-weight: 400;
                        color: #000 !important;
                        font-size: 11px;
                    }

                    h1,h2,h3,h4,h5,h6 {
                        color: #000;
                        padding: 0;
                        margin: 0;
                        font-weight: 600;
                    }
                    #DocDetail {
                        line-height: 175%;
                    }
                    #DocDetail p {
                        text-indent: 2rem;
                    }

                    .page {
                        /* margin: 3mm;/
                        width: 204mm;
                        height: 291mm; */
                        /* border: 1px dashed #000; */
                        width: 210mm;
                        height: 297mm;
                        display: block;
                        margin: 3mm auto;
                        padding: 3mm 10mm 3mm 10mm;
                        box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
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
                <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
            </head>
            <body>
            <?php for($p=1;$p<=$pages;$p++) { ?>
                <input type="hidden" size="60" maxlength="60" class="form-control" id="content" placeholder="Enter content" value="https://www.euroxforce.com:20443/kbi/menus/general/print/printmm.php?DocEntry=<?php echo $DocEntry; ?>"/>
                <div class="page">
                    <!-- PAGE HEADER -->
                    <table class="table table-borderless table-sm" style="color: #000;">
                    <thead>
                        <tr>
                            <td width="20%" class="text-center">
                                <img src="../../../../image/logo/kbi_logo.png" class="img-fluid" />
                            </td>
                            <td>
                                <h4>บริษัท คิงบางกอก อินเตอร์เทรด จำกัด</h4>
                                <small>
                                    541,543,545 ซอย 39/1 แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220<br/>
                                    <strong>KINGBANGKOK INTERTRADE CO.,LTD.</strong> 541,543,545 Soi 39/1, Tha Raeng, Bang Khen, Bangkok 10220
                                </small>
                            </td>
                            <td width="12.5%" class="align-top text-right"><img src="../../../../image/logo/ISO9001-2015.jpeg" class="img-fluid" /></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-center"><h5 style="margin: 1rem;">บันทึกภายใน</br><small>(Memorandum)</small></h5></td>
                        </tr>
                    </thead>
                    </table>
                    <!-- ORDER HEADER -->
                    <table class="table table-borderless table-sm" style="color: #000;">
                        <tr>
                            <th width="15%">เลขที่เอกสาร:</th>
                            <td><?php echo $HeaderRST['DocNum']; ?></td>
                            <th width="7.5%">วันที่:</th>
                            <td width="12.5%"><?php echo date("d/m/Y",strtotime($HeaderRST['DocDate'])); ?></td>
                        </tr>
                        <tr>
                            <th>เรื่อง:</th>
                            <td><?php echo $HeaderRST['DocTitle']; ?></td>
                            <td colspan="2" rowspan="4" class="text-center"><img src="" class="qr-code img-fluid" /></td>
                        </tr>
                        <tr>
                            <th>เรียน (To):</th>
                            <td><?php echo $DocMention; ?></td>
                        </tr>
                        <tr>
                            <th>สำเนา (CC):</th>
                            <td><?php echo $HeaderRST['DocCopyTo']; ?></td>
                        </tr>
                        <tr>
                            <th>สิ่งที่แนบมาด้วย:</th>
                            <td><?php echo $Attach; ?></td>
                        </tr>
                    </table>

                    <div id="DocDetail" style="min-height: 10cm; padding-top: 3mm; border-top: 1px solid #000;">
                        <?php echo $HeaderRST['DocDetail']; ?>
                    </div>

                    <div id="DocSignOff">
                        <p style="text-indent: 2rem;"><?php echo $HeaderRST['DocSignOff']; ?></p>
                    </div>

                    <div class="offset-8 col-4">
                        <table class="table table-borderless table-sm text-center" style="color: #000;">
                            <tr>
                                <td class="align-bottom" style="border-bottom: 1px dotted #000;"><?php echo $HeaderRST['CreateName']; ?></td>
                            </tr>
                            <tr>
                                <th>ผู้จัดทำ</th>
                            </tr>
                        </table>
                    </div>
                    <div class="row" style="margin: 0; padding-top: 3mm; border-top: 1px solid #000;">
                        <div class="col-12 text-center mb-2"><strong>ผลการพิจารณา</strong></div>
                        <?php echo $ShowApprove; ?>
                    </div>
                    
                    <small>
                        FM-HR-21 Rev.02 วันที่มีผลบังคับใช้ 01/01/2566 | อายุการจัดเก็บ: 1 ปี | วิธีทำลาย: ย่อยทิ้ง
                    </small>
                </div>
            <?php } ?>
            <script type="text/javascript">
                // window.print();

                function htmlEncode(value) {
                    return $('<div/>').text(value).html();
                }

                $(function () {
                    let finalURL ='https://chart.googleapis.com/chart?cht=qr&chl='+htmlEncode($('#content').val())+'&chs=80x80&chld=L|0'
                    $('.qr-code').attr('src', finalURL);
                });
            </script>
            </body>
        </html>
<?php
    }
}
?>
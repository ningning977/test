<style type="text/css">
    .table-bordered thead, .table-bordered tbody, .table-bordered tr, .table-bordered th, .table-bordered td, .table:not(.table-borderless) thead th {
        border-color: #151515 !important;
    }

    @media only screen and (max-width:820px) {
        .tableFix {
            overflow-y: auto;
            height: 800px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:821px) and (max-width:1180px) {
        .tableFix {
            overflow-y: auto;
            height: 450px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:1181px) {
        .tableFix {
            overflow-y: auto;
            height: 800px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }
</style>
<?php
    echo "<input type='hidden' id='HeadeMenuLink' value = '".$_GET['p']."'>";
?>
<div class="page-heading">
    <h3><span id='header1'></span></h3>
</div>
<div class='text-secondary'>
    <?php echo PathMenu($_GET['p']); ?>
</div>
<hr class='mt-1'>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br/><br/>
        กำลังโหลด...
    </div>
</div>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="HeaderTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="tab_01" data-bs-toggle="tab" href="#content_01" role="tab" aria-controls="content_01" aria-selected="true">KPI ฝ่ายขาย</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab_02" data-bs-toggle="tab" href="#content_02" role="tab" aria-controls="content_02" aria-selected="true">ภาพรวมการขาย</a>
                    </li>
                </ul>
                <div class="tab-content mt-4" id="HeaderContent">
                    <div class="tab-pane fade show active" id="content_01" role="tabpanel" aria-labelledby="tab_01">
                        <div class="row">
                            <div class="col-auto">
                                <div class="form-group">
                                    <label for="txtYear"><i class="fas fa-calendar-alt"></i> เลือกปี</label>
                                    <select class="form-select form-select-sm " name="txtYear" id="txtYear" onchange="GetSaleName()">
                                        <?php 
                                            for($y = date('Y'); $y >= 2023; $y--) {
                                                echo (($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>");
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group">
                                    <label for="SlpCode"><i class="fas fa-users"></i> เลือกทีม / พนักงานขาย</label>
                                    <select class="form-control form-control-sm " id="SlpCode" name="SlpCode" data-live-search="true" onchange="SelectSlpCode()">
                                        <option selected disabled>กรุณารอสักครู่...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!--------------------------------------------- TABLE --------------------------------------------->
                        <div class="table-responsive mt-4 tableFix">
                            <table class="table table-bordered table-hover" style="font-size: 12px;">
                                <!--------------------------------------------- THEAD --------------------------------------------->
                                <thead class="text-center"> 
                                    <tr style='background-color: #9A1118; color: #fff;'>
                                        <th width="15.5%" rowspan="2">รายละเอียด</th>
                                        <th colspan="13">เดือน</th>
                                    </tr>
                                    <tr style='background-color: #9A1118; color: #fff;'>
                                        <?php for($m = 1; $m <= 12; $m++) { echo "<th width='6.50%' class='text-center'>".FullMonth($m)."</th>"; } ?>
                                        <th width="6.50%">รวมทั้งหมด</th>
                                    </tr>
                                </thead>
                                <!--------------------------------------------- TBODY --------------------------------------------->
                                    <?php
                                    $HeadDetail = [ 'เป้าขาย (บาท)',                     'ยอดขาย (บาท)',                        'สัดส่วนยอดขายต่อเป้าขาย (%)',        'GP',                   'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'จำนวนลูกค้าเก่าทั้งหมด (ราย)',          'จำนวนลูกค้าผู้มุ่งหวัง (ราย)',                'NULL',                           'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'เป้าหมายลูกค้าเก่าที่ต้องเข้าพบ (ราย)',    'จำนวนลูกค้าเก่าที่เข้าพบ (ราย) (> 80 ราย)',   'สัดส่วนการเข้าพบจริง (%) (> 80%)',   'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'เป้าหมายลูกค้ามุ่งหวังที่ตั้งใจเข้าพบ (ราย)', 'จำนวนลูกค้ามุ่งหวังที่เข้าพบ (ราย)',          'สัดส่วนการเข้าพบจริง (%)',            'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'จำนวนร้านค้าที่เปิดใหม่ (ราย)',          'จำนวนร้านค้าที่เปิดบิล (ราย)',               'จำนวนบิลที่เปิด (บิล)',               'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'รายงานประจำวัน (> 80 ร้านค้า/เดือน)',   'รายงานประจำเดือน (> 1 ครั้ง/เดือน)',        'การสำรวจราคาร้านค้า (ส่งรูปอัลบั้มไลน์)', 'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'หนี้เกินกำหนด < 30 วัน (บาท)',        'หนี้เกินกำหนด 31-90 วัน (บาท)',           'หนี้เกินกำหนด > 90 วัน (บาท)',       'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'จำนวนเช็คเด้ง (ใบ)',                  'จำนวนเช็คเกินกำหนด > 30 วัน (บิล)',        'NULL',                           'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'มูลค่ารวมการคืนสินค้า (บาท)',           'มูลค่ารวมเซลส์รับผิดชอบที่รับสินค้าคืน (บาท)',  'NULL',                           'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'มูลค่าการยืมสินค้าที่ยังไม่คืน (บาท)',       'มูลค่าการยืมสินค้าที่ยังไม่คืน > 6 เดือน (บาท)', 'NULL',                           'NULL',                 'NULL',                      'NULL',                    'NULL',                      'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'ต้นทุนคลัง ณ ต้นเดือน (บาท)',          'ต้นทุนรับเข้ารวม (บาท)',                  'ต้นทุนนำออกรวม (บาท)',             'ต้นทุนออก (ขาย) (บาท)', 'ต้นทุนออก (เบิก/แถม) (บาท)', 'ต้นทุนออก (JU/โอน) (บาท)', 'ต้นทุนคลัง ณ สิ้นเดือน (บาท)',  'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'ต้นทุนคลัง ณ ต้นเดือน (บาท)',          'ต้นทุนรับเข้ารวม (บาท)',                  'ต้นทุนนำออกรวม (บาท)',             'ต้นทุนออก (ขาย) (บาท)', 'ต้นทุนออก (เบิก/แถม) (บาท)', 'ต้นทุนออก (JU/โอน) (บาท)', 'ต้นทุนคลัง ณ สิ้นเดือน (บาท)',  'NULL',                      'NULL',                       'NULL',                          'NULL',       'NULL',                  'NULL',         'NULL',
                                                    'ต้นทุนยกมา ณ ต้นเดือน (บาท)',         'ตั้งเป้าเพิ่มเติม (บาท)',                    'ต้นทุนนำออกรวม (ขาย) (บาท)',       'ต้นทุนออก (แถม) (บาท)', 'ต้นทุนออก (ทั้งหมด) (บาท)',   'คงเหลือ ณ สิ้นเดือน',        'คงเหลือ (Aging 0 - 3 เดือน)', 'คงเหลือ (Aging 4 - 6 เดือน)', 'คงเหลือ (Aging 7 - 12 เดือน)', 'คงเหลือ (Aging มากกว่า 12 เดือน)', 'T/O (เดือน)', 'ต้นทุนเข้าสะสม (เป้าหมาย)', 'ต้นทุนออกสะสม', '% ความสำเร็จ (≥ 70% ของเป้า)',
                                                    'ต้นทุนยกมา ณ ต้นเดือน (บาท)',         'ตั้งเป้าเพิ่มเติม (บาท)',                    'ต้นทุนนำออกรวม (ขาย) (บาท)',       'ต้นทุนออก (แถม) (บาท)', 'ต้นทุนออก (ทั้งหมด) (บาท)',   'คงเหลือ ณ สิ้นเดือน',        'คงเหลือ (Aging 0 - 3 เดือน)', 'คงเหลือ (Aging 4 - 6 เดือน)', 'คงเหลือ (Aging 7 - 12 เดือน)', 'คงเหลือ (Aging มากกว่า 12 เดือน)', 'T/O (เดือน)', 'ต้นทุนเข้าสะสม (เป้าหมาย)', 'ต้นทุนออกสะสม', '% ความสำเร็จ (≥ 70% ของเป้า)'];

                                    $cName =      [ 'trg',        'SALES',         'TrgPer',        'GP',          '', '', '', '', '', '', '', '', '', '',
                                                    'CusTar',     'NewCus',        '',              '',          '', '', '', '', '', '', '', '', '', '',
                                                    'O_OldCus',   'O_MeetCus',     'O_Per',         '',          '', '', '', '', '', '', '', '', '', '',
                                                    'N_OldCus',   'N_MeetCus',     'N_Per',         '',          '', '', '', '', '', '', '', '', '', '',
                                                    'nOpenCus',   'LOpenCus',      'bOpenCus',      '',          '', '', '', '', '', '', '', '', '', '',
                                                    'PLAN80',     'PLAN1',         'Album',         '',          '', '', '', '', '', '', '', '', '', '',
                                                    'NeeB30D',    'NeeB31D-B91D',  'NeeA90D',       '',          '', '', '', '', '', '', '', '', '', '',
                                                    'ChkD',       'ChkDue',        '',              '',          '', '', '', '', '', '', '', '', '', '',
                                                    'KProKBI',    'KProSA',        '',              '',          '', '', '', '', '', '', '', '', '', '',
                                                    'borrowed',   'borrowedDif',   '',              '',          '', '', '', '', '', '', '', '', '', '',
                                                    'WhseSale1HR1',  'WhseSale1HR2',  'WhseSale1HR3',     'WhseSale1HR4',  'WhseSale1HR5',  'WhseSale1HR6',  'WhseSale1HR7', '', '', '', '', '', '', '',
                                                    'WhseSaleR1',  'WhseSaleR2',  'WhseSaleR3',     'WhseSaleR4',  'WhseSaleR5',  'WhseSaleR6',  'WhseSaleR7', '', '', '', '', '', '', '',
                                                    'TargetSkuQ1M', 'TargetSkuQ2M',  'TargetSkuQ3M',   'TargetSkuQ4M', 'TargetSkuQ5M', 'TargetSkuQ6M', 'TargetSkuQ7M', 'TargetSkuQ8M', 'TargetSkuQ9M', 'TargetSkuQ10M', 'TargetSkuQ11M', 'TargetSkuQ12M', 'TargetSkuQ13M', 'TargetSkuQ14M',
                                                    'TargetSkuF1M', 'TargetSkuF2M',  'TargetSkuF3M',   'TargetSkuF4M', 'TargetSkuF5M', 'TargetSkuF6M', 'TargetSkuF7M', 'TargetSkuF8M', 'TargetSkuF9M', 'TargetSkuF10M', 'TargetSkuF11M', 'TargetSkuF12M', 'TargetSkuF13M', 'TargetSkuF14M' ];
                                   
                                    $HeadName = [ 
                                        'ข้อมูลยอดขาย', 
                                        'จำนวนลูกค้า',
                                        'การเข้าพบลูกค้า',
                                        'ลูกค้ามุ่งหวัง',
                                        'ข้อมูลลูกค้าเปิดใหม่',
                                        'ข้อมูลการทำรายงาน',
                                        'หนี้เกินกำหนด',
                                        'ข้อมูลเช็คเด้ง',
                                        'ข้อมูลการคืนสินค้า',
                                        'ข้อมูลการยืมสินค้า',
                                        'ความเคลื่อนไหวคลังเซลส์มือหนึ่ง',
                                        'ความเคลื่อนไหวคลังเซลส์มือสอง',
                                        'ความเคลื่อนไหวสินค้าจอง (Quota)',
                                        'ความเคลื่อนไหวสินค้าต้องขาย (Focus)'
                                    ];
                                   ?>         

                                    <?php
                                    $Tbody = 0; $cID = 0;
                                    for($D = 0; $D < count($HeadDetail); $D++) {
                                        $Tbody++;
                                        if($Tbody == 1) {
                                            echo "<tbody class='table-group-divider ' style='border-top: 8px double #9A1118 !important;'>
                                                    <tr>
                                                        <th colspan='14' class='text-center' style='background-color: #9A1118; color: #fff;'>".$HeadName[$cID]."</th>
                                                    </tr>"; 
                                            
                                            $cID++;
                                        }
                                        if($HeadDetail[$D] != 'NULL') {
                                            $row_class = "";
                                            $txt_align = "text-right";
                                            switch($cName[$D]) {
                                                case 'TrgPer': 
                                                case 'O_Per': 
                                                case 'N_Per': 
                                                case 'GP': 
                                                    $row_class = "table-active fw-bolder";
                                                    $txt_align ="text-center";
                                                break;
                                                case 'WhseSale1HR1':  case 'WhseSaleR1': 
                                                    $row_class = "table-warning text-warning fw-bolder";
                                                break;
                                                case 'WhseSale1HR2':  case 'WhseSaleR2': 
                                                    $row_class = "table-success text-success fw-bolder";
                                                break;
                                                case 'trg': 
                                                case 'WhseSale1HR3':  case 'WhseSaleR3': 
                                                    $row_class = "table-danger text-primary fw-bolder";
                                                break;
                                                case 'WhseSale1HR4': case 'WhseSale1HR5': case 'WhseSale1HR6': 
                                                case 'WhseSaleR4': case 'WhseSaleR5': case 'WhseSaleR6': 
                                                    $row_class = "text-primary";
                                                break;
                                                case 'WhseSale1HR7': case 'WhseSaleR7': 
                                                case 'TargetSkuQ1M': case 'TargetSkuQ6M': case 'TargetSkuQ14M':
                                                case 'TargetSkuF1M': case 'TargetSkuF6M': case 'TargetSkuF14M':
                                                    $row_class = "table-active fw-bolder";
                                                break;
                                                case 'KProKBI':
                                                case 'ChkD': case 'ChkDue':
                                                case 'NeeB31D-B91D': case 'NeeA90D':
                                                case 'TargetSkuQ2M': case 'TargetSkuQ12M':
                                                case 'TargetSkuF2M':  case 'TargetSkuF12M':
                                                case 'borrowedDif': 
                                                    $row_class = "text-danger";
                                                break;
                                                case 'SALES': 
                                                case 'N_MeetCus': 
                                                case 'O_MeetCus': 
                                                case 'TargetSkuQ3M': case 'TargetSkuQ4M': case 'TargetSkuQ13M': 
                                                case 'TargetSkuF3M': case 'TargetSkuF4M': case 'TargetSkuF13M': 
                                                    $row_class = "text-success";
                                                break;
                                                case 'TargetSkuQ5M': 
                                                case 'TargetSkuF5M': 
                                                    $row_class = "text-success fw-bolder";
                                                break;
                                                case 'TargetSkuQ11M': 
                                                case 'TargetSkuF11M': 
                                                    $row_class = "table-warning";
                                                break;
                                                case 'KProSA': 
                                                    $row_class = "table-active text-danger fw-bolder";
                                                break;
                                                case 'borrowed':
                                                    $row_class = "table-active";
                                                break;
                                                case 'N_OldCus':
                                                case 'O_OldCus':
                                                    $row_class = "fw-bolder";
                                                break;
                                            }

                                            echo"<tr class='$row_class' >";
                                                echo"<td>".$HeadDetail[$D]."</td>";
                                                for($m = 1; $m <= 12; $m++) {
                                                    
                                                    echo"<td class='$txt_align ".$cName[$D].$m." cls_Data'>&nbsp;</td>";
                                                }
                                                echo"<td class='$txt_align ".$cName[$D]."All cls_Data' style='font-weight: bold;'>&nbsp;</td>";
                                            echo"</tr>";
                                        }

                                        if($Tbody == 14) {
                                            echo "</tbody>";
                                            $Tbody = 0;
                                        }
                                    } 
                                    ?>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="content_02" role="tabpanel" aria-labelledby="tab_02">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
        GetSaleName();
	});
</script> 
<script type="text/javascript">
    function CallHead(){
        $(".overlay").show();
        var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            data : {MenuCase : MenuCase,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#header1").html(inval["header1"]);
                    $("#header2").html(inval["header2"]);
                });
                $(".overlay").hide();
            }
        });
    };
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */

    function number_format(number,decimal) {
        var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
        var formatter = new Intl.NumberFormat("en",options);
        return formatter.format(number)
    }

    function GetSaleName() {
        $(".overlay").show();
        $("#SlpCode").selectpicker("destroy");
        $.ajax({
            url: "menus/sale/ajax/ajaxsalereport.php?p=GetSaleName",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            data: { Year : $("#txtYear").val() },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#SlpCode").html(inval["output"]);
                    if(inval['LogCode'] != "N") {
                        $("#SlpCode").val(inval['LogCode']).change();
                    }
                });
                $("#SlpCode").selectpicker();
                $(".cls_Data").html("");
                $(".overlay").hide();
            }
        });
    }

    function SelectSlpCode() {
        if($("#SlpCode").val() != null) {
            $(".overlay").show();
            $.ajax({
                url: "menus/sale/ajax/ajaxsalereport.php?p=SelectSlpCode",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
                type: "POST",
                data: { SelectSlpCode : $("#SlpCode").val(), Year : $("#txtYear").val() },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        // เดือน
                        for(var m = 1; m <= 12; m++) {
                            $(".trg"+m).html(inval['trg'][m]);             // เป้าขาย
                            $(".SALES"+m).html(inval['SALES'][m]);         // ยอดขาย
                            $(".TrgPer"+m).html(inval['TrgPer'][m]);       // สัดส่วนยอดขายต่อเป้าขาย (%)
                            $(".GP"+m).html(inval['GP'][m]);               // GP
                            (inval['TrgPer'][m] >= 100) ? $(".TrgPer"+m).addClass("text-success") : "";
    
                            $(".CusTar"+m).html(inval['CusTar'][m]);       // จำนวนลูกค้าเก่า ทั้งหมด
                            $(".NewCus"+m).html(inval['NewCus'][m]);       // จำนวนลูกค้าผู้มุ่งหวัง (ราย)
    
                            $(".O_OldCus"+m).html(inval['O_OldCus'][m]);   // เป้าหมายลูกค้าเก่าที่ต้องเข้าพบ (ราย)
                            $(".O_MeetCus"+m).html(inval['O_MeetCus'][m]); // จำนวนลูกค้าเก่าที่เข้าพบ (ราย)
                            $(".O_Per"+m).html(inval['O_Per'][m]);         // สัดส่วนการเข้าพบจริง (%) 1
                            (inval['O_Per'][m] >= 100) ? $(".O_Per"+m).addClass("text-success") : "";
    
                            $(".N_OldCus"+m).html(inval['N_OldCus'][m]);   // เป้าหมายลูกค้ามุ่งหวังที่ตั้งใจเข้าพบ (ราย)
                            $(".N_MeetCus"+m).html(inval['N_MeetCus'][m]); // จำนวนลูกค้ามุ่งหวังที่เข้าพบ (ราย)
                            $(".N_Per"+m).html(inval['N_Per'][m]);         // สัดส่วนการเข้าพบจริง (%) 2
                            (inval['N_Per'][m] >= 100) ? $(".N_Per"+m).addClass("text-success") : "";
    
                            $(".nOpenCus"+m).html(inval['nOpenCus'][m]);   // จำนวนร้านค้าที่เปิดใหม่ (ราย)
                            $(".LOpenCus"+m).html(inval['LOpenCus'][m]);   // จำนวนร้านค้าที่เปิดบิล (ราย)
                            $(".bOpenCus"+m).html(inval['bOpenCus'][m]);   // จำนวนบิลที่เปิด (บิล)
    
                            $(".PLAN80"+m).html(inval['PLAN80'][m]);       // รายงานประจำวัน (> 80 ร้านค้า/เดือน)
                            (inval['PLAN80'][m] >= 80) ? $(".PLAN80"+m).addClass("text-danger") : "";
                            $(".PLAN1"+m).html(inval['PLAN1'][m]);         // รายงานประจำเดือน (> 1 ครั้ง/เดือน)
                            (inval['PLAN1'][m] >= 1) ? $(".PLAN1"+m).addClass("text-danger") : "";
                            $(".Album"+m).html(inval['Album'][m]);         // การสำรวจราคาร้านค้า (ส่งรูปอัลบั้มไลน์)
                            
                            $(".NeeB30D"+m).html(inval['Nee']['B30D'][m]); // หนี้เกินกำหนด < 30 วัน (บาท)
                            $(".NeeB31D-B91D"+m).html(inval['Nee']['B31D-B91D'][m]); // หนี้เกินกำหนด 31-90 วัน (บาท)
                            $(".NeeA90D"+m).html(inval['Nee']['A90D'][m]); // หนี้เกินกำหนด > 90 วัน (บาท)
                            
                            $(".ChkD"+m).html(inval['ChkD'][m]);           // จำนวนเช็คเด้ง (ใบ)
                            $(".ChkDue"+m).html(inval['ChkDue'][m]);       // จำนวนเช็คเกินกำหนด > 30 วัน (บิล)
    
                            $(".KProKBI"+m).html(inval['KPro']['KBI'][m]); // มูลค่ารวมการคืนสินค้า (บาท)
                            $(".KProSA"+m).html(inval['KPro']['SA'][m]);   // ต้นทุนรวมเซลส์รับผิดชอบที่รับสินค้าคืน (บาท)
                            
                            $(".borrowed"+m).html(inval['borrowed'][m]);   // มูลค่าการยืมสินค้าที่ยังไม่คืน (บาท)
                            $(".borrowedDif"+m).html(inval['borrowedDif'][m]); // มูลค่าการยืมสินค้าที่ยังไม่คืน > 6 เดือน (บาท)
    
                            // มือหนึ่ง
                            if(inval['WhseSale'] != '-') {
                                $(".WhseSale1HR1"+m).html(number_format(inval['WhseSale1H'][m]['r1'],0)); // ต้นทุนคลัง ณ ต้นเดือน (บาท)
                                $(".WhseSale1HR2"+m).html(number_format(inval['WhseSale1H'][m]['r2'],0)); // ต้นทุนรับเข้ารวม (บาท)
                                $(".WhseSale1HR3"+m).html(number_format(inval['WhseSale1H'][m]['r3'],0)); // ต้นทุนนำออกรวม (บาท)
                                $(".WhseSale1HR4"+m).html(number_format(inval['WhseSale1H'][m]['r5'],0)); // ต้นทุนออก (ขาย) (บาท)
                                $(".WhseSale1HR5"+m).html(number_format(inval['WhseSale1H'][m]['r6'],0)); // ต้นทุนออก (เบิก/แถม) (บาท)
                                $(".WhseSale1HR6"+m).html(number_format(inval['WhseSale1H'][m]['r7'],0)); // ต้นทุนออก (JU/โอน) (บาท)
                                $(".WhseSale1HR7"+m).html(number_format(inval['WhseSale1H'][m]['r4'],0)); // ต้นทุนคลัง ณ สิ้นเดือน (บาท)
                            }else{
                                for(let i = 1; i <= 7; i++) { $(".WhseSale1HR"+i+m).html("-"); }
                            }
    
                            // มือสอง
                            if(inval['WhseSale'] != '-') {
                                $(".WhseSaleR1"+m).html(number_format(inval['WhseSale'][m]['r1'],0)); // ต้นทุนคลัง ณ ต้นเดือน (บาท)
                                $(".WhseSaleR2"+m).html(number_format(inval['WhseSale'][m]['r2'],0)); // ต้นทุนรับเข้ารวม (บาท)
                                $(".WhseSaleR3"+m).html(number_format(inval['WhseSale'][m]['r3'],0)); // ต้นทุนนำออกรวม (บาท)
                                $(".WhseSaleR4"+m).html(number_format(inval['WhseSale'][m]['r5'],0)); // ต้นทุนออก (ขาย) (บาท)
                                $(".WhseSaleR5"+m).html(number_format(inval['WhseSale'][m]['r6'],0)); // ต้นทุนออก (เบิก/แถม) (บาท)
                                $(".WhseSaleR6"+m).html(number_format(inval['WhseSale'][m]['r7'],0)); // ต้นทุนออก (JU/โอน) (บาท)
                                $(".WhseSaleR7"+m).html(number_format(inval['WhseSale'][m]['r4'],0)); // ต้นทุนคลัง ณ สิ้นเดือน (บาท)
                            }else{
                                for(let i = 1; i <= 7; i++) { $(".WhseSaleR"+i+m).html("-"); }
                            }
                            let persent = ""; let dec = 0;
                            for(let t = 1; t <= 14; t++) {
                                persent = (t == 14 && inval['TargetSkuQ']['R'+t][m] > 0) ? "%" : "";
                                dec = (t == 11 && inval['TargetSkuQ']['R'+t][m] > 0 || t == 14 && inval['TargetSkuQ']['R'+t][m] > 0) ? 2 : 0;
                                (inval['TargetSkuQ']['R'+t][m] != 0) ?  $(".TargetSkuQ"+t+"M"+m).html(number_format(inval['TargetSkuQ']['R'+t][m],dec)+persent) : $(".TargetSkuQ"+t+"M"+m).html('-');
                               
                                if(t == 11) {
                                    if(inval['TargetSkuQ']['R'+t][m] == 0) {
                                        $(".TargetSkuQ"+t+"M"+m).addClass("text-warning table-warning");
                                    } else {
                                        if(inval['TargetSkuQ']['R'+t][m] <= 4) {
                                            $(".TargetSkuQ"+t+"M"+m).addClass("text-warning table-warning");
                                        }else if(inval['TargetSkuQ']['R'+t][m] <= 6) {
                                            $(".TargetSkuQ"+t+"M"+m).addClass("text-success table-success");
                                        } else {
                                            $(".TargetSkuQ"+t+"M"+m).addClass("text-danger table-danger");
                                        }
                                    }
                                }
                                    
                                persent = (t == 14 && inval['TargetSkuF']['R'+t][m] > 0) ? "%" : "";
                                dec = (t == 11 && inval['TargetSkuF']['R'+t][m] != 0 || t == 14 && inval['TargetSkuF']['R'+t][m] != 0) ? 2 : 0;
                                (inval['TargetSkuF']['R'+t][m] != 0) ? $(".TargetSkuF"+t+"M"+m).html(number_format(inval['TargetSkuF']['R'+t][m],dec)+persent) : $(".TargetSkuF"+t+"M"+m).html('-');
                               
                                if(t == 11) {
                                    if(inval['TargetSkuF']['R'+t][m] == 0) {
                                        $(".TargetSkuF"+t+"M"+m).addClass("text-warning table-warning");
                                    } else {
                                        if(inval['TargetSkuF']['R'+t][m] <= 4) {
                                            $(".TargetSkuF"+t+"M"+m).addClass("text-warning table-warning");
                                        }else if(inval['TargetSkuF']['R'+t][m] <= 6) {
                                            $(".TargetSkuF"+t+"M"+m).addClass("text-success table-success");
                                        } else {
                                            $(".TargetSkuF"+t+"M"+m).addClass("text-danger table-danger");
                                        }
                                    }
                                }
                            }
                        }
    
                        // รวม
                        $(".trgAll").html(number_format(inval['trg']['All'],0));               // เป้าขาย
                        $(".SALESAll").html(number_format(inval['SALES']['All'],0));           // ยอดขาย
                        $(".TrgPerAll").html(number_format(inval['TrgPer']['All'],2)+"%");     // สัดส่วนยอดขายต่อเป้าขาย (%)
                        $(".GPAll").html(number_format(inval['GP']['All'],2)+"%");             // GP
                        (inval['TrgPer']['All'] >= 100) ? $(".TrgPerAll").addClass("text-success") : "";
    
                        $(".CusTarAll").html(number_format(inval['CusTar']['All'],0));         // จำนวนลูกค้าเก่า ทั้งหมด
                        $(".NewCusAll").html(number_format(inval['NewCus']['All'],0));         // จำนวนลูกค้าผู้มุ่งหวัง (ราย)
    
                        $(".O_OldCusAll").html(number_format(inval['O_OldCus']['All'],0));     // เป้าหมายลูกค้าเก่าที่ต้องเข้าพบ (ราย)
                        $(".O_MeetCusAll").html(number_format(inval['O_MeetCus']['All'],0));   // จำนวนลูกค้าเก่าที่เข้าพบ (ราย)
                        $(".O_PerAll").html(number_format(inval['O_Per']['All'],2)+"%");       // สัดส่วนการเข้าพบจริง (%) 1
    
                        $(".N_OldCusAll").html(number_format(inval['N_OldCus']['All'],0));     // เป้าหมายลูกค้ามุ่งหวังที่ตั้งใจเข้าพบ (ราย)
                        $(".N_MeetCusAll").html(number_format(inval['N_MeetCus']['All'],0));   // จำนวนลูกค้ามุ่งหวังที่เข้าพบ (ราย)
                        $(".N_PerAll").html(number_format(inval['N_Per']['All'],2)+"%");       // สัดส่วนการเข้าพบจริง (%) 2
    
                        $(".nOpenCusAll").html(number_format(inval['nOpenCus']['All'],0));     // จำนวนร้านค้าที่เปิดใหม่ (ราย)
                        $(".LOpenCusAll").html(number_format(inval['LOpenCus']['All'],0));     // จำนวนร้านค้าที่เปิดบิล (ราย)
                        $(".bOpenCusAll").html(number_format(inval['bOpenCus']['All'],0));     // จำนวนบิลที่เปิด (บิล)
                        
                        $(".PLAN80All").html(number_format(inval['PLAN80']['All'],0));         // รายงานประจำวัน (> 80 ร้านค้า/เดือน)
                        $(".PLAN1All").html(number_format(inval['PLAN1']['All'],0));           // รายงานประจำเดือน (> 1 ครั้ง/เดือน)
                        $(".AlbumAll").html(number_format(inval['Album']['All'],0));           // การสำรวจราคาร้านค้า (ส่งรูปอัลบั้มไลน์)
                        
                        $(".NeeB30DAll").html(number_format(inval['Nee']['B30D']['All'],0));   // หนี้เกินกำหนด < 30 วัน (บาท)
                        $(".NeeB31D-B91DAll").html(number_format(inval['Nee']['B31D-B91D']['All'],0)); // หนี้เกินกำหนด 31-90 วัน (บาท)
                        $(".NeeA90DAll").html(number_format(inval['Nee']['A90D']['All'],0));   // หนี้เกินกำหนด > 90 วัน (บาท)
                        
                        $(".ChkDAll").html(number_format(inval['ChkD']['All'],0));             // จำนวนเช็คเด้ง (ใบ)
                        $(".ChkDueAll").html(number_format(inval['ChkDue']['All'],0));         // จำนวนเช็คเกินกำหนด > 30 วัน (บิล)
    
                        $(".KProKBIAll").html(number_format(inval['KPro']['KBIAll'],0));       // มูลค่ารวมการคืนสินค้า (บาท)
                        $(".KProSAAll").html(number_format(inval['KPro']['SAAll'],0));         // ต้นทุนรวมเซลส์รับผิดชอบที่รับสินค้าคืน (บาท)
    
                        $(".borrowedAll").html(number_format(inval['borrowed']['All'],0));     // มูลค่าการยืมสินค้าที่ยังไม่คืน (บาท)
                        $(".borrowedDifAll").html(number_format(inval['borrowedDif']['All'],0)); // มูลค่าการยืมสินค้าที่ยังไม่คืน > 6 เดือน (บาท)
                    });
                    $(".overlay").hide();
                }
            });
        }
    }
</script> 
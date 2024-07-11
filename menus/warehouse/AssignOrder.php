<style type="text/css">

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
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm" onclick="GetData();"><i class="fas fa-sync fa-fw fa-1x"></i> Refresh</button>
                        <h5 class="mt-3">การปฏิบัติงานประจำเดือน<?php echo FullMonth(date("m"))." ".date("Y"); ?></h5>
                        <table class="table table-bordered table-hover table-sm mt-2" style="font-size: 12px;">
                            <thead class="text-center text-white" style="background-color: #9A1118;">
                                <tr>
                                    <th rowspan="3">รายชื่อพนักงานเบิก</th>
                                    <th colspan="8">ร้านค้าทั่วไป</th>
                                    <th colspan="8">โมเดิร์นเทรด</th>
                                    <th colspan="2">ทั้งหมด</th>
                                </tr>
                                <tr>
                                    <th colspan="2">S/O ค้างเบิก</th>
                                    <th colspan="2">S/O กำลังเบิก</th>
                                    <th colspan="2">S/O เสร็จสิ้น</th>
                                    <th colspan="2">S/O ทั้งเดือน</th>
                                    <th colspan="2">S/O ค้างเบิก</th>
                                    <th colspan="2">S/O กำลังเบิก</th>
                                    <th colspan="2">S/O เสร็จสิ้น</th>
                                    <th colspan="2">S/O ทั้งเดือน</th>
                                    <th colspan="2">S/O ทั้งเดือน</th>
                                </tr>
                                <tr>
                                    <th width="4.5%">ปัจจุบัน</th>
                                    <th width="4.5%">ล่วงหน้า</th>
                                    <th width="4.5%">S/O</th>
                                    <th width="4.5%">SKU</th>
                                    <th width="4.5%">S/O</th>
                                    <th width="4.5%">SKU</th>
                                    <th width="4.5%">S/O</th>
                                    <th width="4.5%">SKU</th>
                                    <th width="4.5%">ปัจจุบัน</th>
                                    <th width="4.5%">ล่วงหน้า</th>
                                    <th width="4.5%">S/O</th>
                                    <th width="4.5%">SKU</th>
                                    <th width="4.5%">S/O</th>
                                    <th width="4.5%">SKU</th>
                                    <th width="4.5%">S/O</th>
                                    <th width="4.5%">SKU</th>
                                    <th width="4.5%">S/O</th>
                                    <th width="4.5%">SKU</th>
                                </tr>
                            </thead>
                            <tbody id="PickEmpDetail">
                                <tr>
                                    <td class="text-center" colspan="19">ไม่มีข้อมูล :(</td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered table-hover table-sm mt-2" style="font-size: 12px;">
                            <thead class="text-center text-white" style="background-color: #9A1118;">
                                <tr>
                                    <th rowspan="3">โต๊ะที่</th>
                                    <th colspan="8">ร้านค้าทั่วไป</th>
                                    <th colspan="8">โมเดิร์นเทรด</th>
                                    <th colspan="2">ทั้งหมด</th>
                                </tr>
                                <tr>
                                    <th colspan="2">บิลรอแพ็ก</th>
                                    <th colspan="2">บิลกำลังแพ็ก</th>
                                    <th colspan="2">บิลเสร็จสิ้น</th>
                                    <th colspan="2">บิลทั้งเดือน</th>
                                    <th colspan="2">บิลรอแพ็ก</th>
                                    <th colspan="2">บิลกำลังแพ็ก</th>
                                    <th colspan="2">บิลเสร็จสิ้น</th>
                                    <th colspan="2">บิลทั้งเดือน</th>
                                    <th colspan="2">บิลทั้งเดือน</th>
                                </tr>
                                <tr>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">SKU</th>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">ลัง</th>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">ลัง</th>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">ลัง</th>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">SKU</th>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">ลัง</th>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">ลัง</th>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">ลัง</th>
                                    <th width="4.5%">บิล</th>
                                    <th width="4.5%">ลัง</th>
                                </tr>
                            </thead>
                            <tbody id="PackEmpDetail">
                                <tr>
                                    <td class="text-center" colspan="19">ไม่มีข้อมูล :(</td>
                                </tr>
                            </tbody>
                        </table>

                        <hr/>
                        <h5 class="mt-3">การปฏิบัติงานประจำปี <?php echo date("Y"); ?></h5>
                        <table class="table table-bordered table-hover table-sm mt-2" style="font-size: 12px;">
                            <thead class="text-center text-white" style="background-color: #9A1118;">
                                <tr>
                                    <th rowspan="2">รายชื่อพนักงานเบิก</th>
                                    <?php for($m = 1; $m <= 12; $m++) { echo "<th colspan='2'>".FullMonth($m)."</th>"; } ?>
                                </tr>
                                <tr>
                                    <?php for($m = 1; $m <= 12; $m++) { echo "<th width='3.5%'>S/O</th><th width='3.5%'>SKU</th>"; } ?>
                                </tr>
                            </thead>
                            <tbody id="PickMthDetail">
                                <tr>
                                    <td class="text-center" colspan="25">ไม่มีข้อมูล :(</td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered table-hover table-sm mt-2" style="font-size: 12px;">
                            <thead class="text-center text-white" style="background-color: #9A1118;">
                                <tr>
                                    <th rowspan="2">โต๊ะที่</th>
                                    <?php for($m = 1; $m <= 12; $m++) { echo "<th colspan='2'>".FullMonth($m)."</th>"; } ?>
                                </tr>
                                <tr>
                                    <?php for($m = 1; $m <= 12; $m++) { echo "<th width='3.5%'>S/O</th><th width='3.5%'>ลัง</th>"; } ?>
                                </tr>
                            </thead>
                            <tbody id="PackMthDetail">
                                <tr>
                                    <td class="text-center" colspan="25">ไม่มีข้อมูล :(</td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="SearchResult" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice fa-fa fa-lg"></i> รายการ S/O</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <table class="table table-bordered table-hover table-sm" id="ResultList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">ลำดับ</th>
                                    <th width="10%">เลขที่ใบสั่งขาย</th>
                                    <th>ชื่อลูกค้า</th>
                                    <th width="10%">วันที่เอกสาร</th>
                                    <th width="10%">วันที่กำหนดส่ง</th>
                                    <th width="20%">พนักงานขาย</th>
                                    <th width="5%">SKU</th>
                                    <th width="10%">สถานะการเบิก</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function number_format(number,decimal) {
    var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
    var formatter = new Intl.NumberFormat("en",options);
    return formatter.format(number)
}

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

function GetSO(ukey, SoType) {
    $(".overlay").show();
    $.ajax({
        url: "menus/warehouse/ajax/ajaxAssignOrder.php?p=GetSO",
        type: "POST",
        data: {
            u: ukey,
            t: SoType
        },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                let Rows = parseFloat(inval['Row']);
                if(Rows > 0) {
                    let tBody = "";
                    let no    = 1;

                    for(i = 0; i < Rows; i++) {
                        let Cls = "";
                        switch(inval[i]['StatusDoc']) {
                            case '4':
                                Cls = " class='table-warning text-warning'";
                            break;
                            case '5':
                                Cls = " class='table-success text-success'";
                            break;
                            case '6':
                                Cls = " class='table-info'";
                            break;
                        }
                        tBody +=
                            "<tr"+Cls+">"+
                                "<td class='text-right'>"+number_format(no,0)+"</td>"+
                                "<td class='text-center'>"+inval[i]['DocNum']+"</td>"+
                                "<td>"+inval[i]['CusName']+"</td>"+
                                "<td class='text-center'>"+inval[i]['DocDate']+"</td>"+
                                "<td class='text-center'>"+inval[i]['DocDueDate']+"</td>"+
                                "<td>"+inval[i]['SlpName']+"</td>"+
                                "<td class='text-right'>"+inval[i]['ItemCount']+"</td>"+
                                "<td class='text-center'>"+inval[i]['StatusTxt']+"</td>"+
                            "</tr>";
                        no++;
                    }
                    $("#ResultList tbody").html(tBody);
                    $("#SearchResult").modal('show');
                } else {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("ไม่มีข้อมูล :(");
                    $("#alert_modal").modal('show');
                }
            })
        }
    });
}

function GetData() {
    $(".overlay").show();
    $.ajax({
        url: "menus/warehouse/ajax/ajaxAssignOrder.php?p=GetData",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                let PickEmpRows   = parseFloat(inval['PICKEMP']['Row']);
                let PickMthRows   = parseFloat(inval['PICKMTH']['Row']);
                let PackEmpRows   = parseFloat(inval['PACKEMP']['Row']);
                let PackMthRows   = parseFloat(inval['PACKMTH']['Row']);

                let PickEMPtBody  = "";
                let PickMTHtBody  = "";
                let PackEMPtBody  = "";
                let PackMTHtBody  = "";
                let Online = "";

                let TT_CrntSO = TT_NextSo = TT_PickSo = TT_PickSku = TT_FnshSo = TT_FnshSku = TT_WorkSo = TT_WorkSku = "";
                let MT_CrntSO = MT_NextSo = MT_PickSo = MT_PickSku = MT_FnshSo = MT_FnshSku = MT_WorkSo = MT_WorkSku = "";
                let SUMTT_CrntSo = SUMTT_NextSo = SUMTT_PickSo = SUMTT_PickSku = SUMTT_FnshSo = SUMTT_FnshSku = SUMTT_WorkSo = SUMTT_WorkSku = 0;
                let SUMMT_CrntSo = SUMMT_NextSo = SUMMT_PickSo = SUMMT_PickSku = SUMMT_FnshSo = SUMMT_FnshSku = SUMMT_WorkSo = SUMMT_WorkSku = 0;
                let SUMAL_PickSo = SUMAL_PickSku = 0;
                
                let TT_CrntIV = TT_CrntSku = TT_PackIV = TT_PackBox = TT_FnshIV = TT_FnshBox = TT_WorkIV = TT_WorkBox = "";
                let MT_CrntIV = MT_CrntSku = MT_PackIV = MT_PackBox = MT_FnshIV = MT_FnshBox = MT_WorkIV = MT_WorkBox = "";
                let SUMTT_CrntIV = SUMTT_CrntSku = SUMTT_PackIV = SUMTT_PackBox = SUMTT_FnshIV = SUMTT_FnshBox = SUMTT_WorkIV = SUMTT_WorkBox = 0;
                let SUMMT_CrntIV = SUMMT_CrntSku = SUMMT_PackIV = SUMMT_PackBox = SUMMT_FnshIV = SUMMT_FnshBox = SUMMT_WorkIV = SUMMT_WorkBox = 0;
                let SUMAL_PackIV = SUMAL_PackBox = 0;

                if(PickEmpRows > 0) {
                    for(i = 0; i < PickEmpRows; i++) {
                        if(inval['PICKEMP'][i]['Online'] == "Y") {
                            Online = "<i class='fas fa-user fa-fw fa-1x text-success'></i>";
                        } else {
                            Online = "<i class='fas fa-user fa-fw fa-1x text-muted'></i>";
                        }

                        if(inval['PICKEMP'][i]['TT_CrntSo'] == "0" || inval['PICKEMP'][i]['TT_CrntSo'] == null) { TT_CrntSo = "-"; } else { TT_CrntSo = number_format(inval['PICKEMP'][i]['TT_CrntSo'],0); }
                        if(inval['PICKEMP'][i]['TT_NextSo'] == "0" || inval['PICKEMP'][i]['TT_NextSo'] == null) { TT_NextSo = "-"; } else { TT_NextSo = number_format(inval['PICKEMP'][i]['TT_NextSo'],0); }

                        if(inval['PICKEMP'][i]['TT_PickSo'] == "0"  || inval['PICKEMP'][i]['TT_PickSo'] == null) { TT_PickSo = "-"; } else { TT_PickSo = number_format(inval['PICKEMP'][i]['TT_PickSo'],0); }
                        if(inval['PICKEMP'][i]['TT_PickSku'] == "0" || inval['PICKEMP'][i]['TT_PickSku'] == null) { TT_PickSku = "-"; } else { TT_PickSku = number_format(inval['PICKEMP'][i]['TT_PickSku'],0); }
                        if(inval['PICKEMP'][i]['TT_FnshSo'] == "0"  || inval['PICKEMP'][i]['TT_FnshSo'] == null) { TT_FnshSo = "-"; } else { TT_FnshSo = number_format(inval['PICKEMP'][i]['TT_FnshSo'],0); }
                        if(inval['PICKEMP'][i]['TT_FnshSku'] == "0" || inval['PICKEMP'][i]['TT_FnshSku'] == null) { TT_FnshSku = "-"; } else { TT_FnshSku = number_format(inval['PICKEMP'][i]['TT_FnshSku'],0); }
                        if(inval['PICKEMP'][i]['TT_WorkSo'] == "0"  || inval['PICKEMP'][i]['TT_WorkSo'] == null) { TT_WorkSo = "-"; } else { TT_WorkSo = number_format(inval['PICKEMP'][i]['TT_WorkSo'],0); }
                        if(inval['PICKEMP'][i]['TT_WorkSku'] == "0" || inval['PICKEMP'][i]['TT_WorkSku'] == null) { TT_WorkSku = "-"; } else { TT_WorkSku = number_format(inval['PICKEMP'][i]['TT_WorkSku'],0); }

                        if(inval['PICKEMP'][i]['MT_CrntSo'] == "0" || inval['PICKEMP'][i]['MT_CrntSo'] == null) { MT_CrntSo = "-"; } else { MT_CrntSo = number_format(inval['PICKEMP'][i]['MT_CrntSo'],0); }
                        if(inval['PICKEMP'][i]['MT_NextSo'] == "0" || inval['PICKEMP'][i]['MT_NextSo'] == null) { MT_NextSo = "-"; } else { MT_NextSo = number_format(inval['PICKEMP'][i]['MT_NextSo'],0); }

                        if(inval['PICKEMP'][i]['MT_PickSo'] == "0"  || inval['PICKEMP'][i]['MT_PickSo'] == null) { MT_PickSo = "-"; } else { MT_PickSo = number_format(inval['PICKEMP'][i]['MT_PickSo'],0); }
                        if(inval['PICKEMP'][i]['MT_PickSku'] == "0" || inval['PICKEMP'][i]['MT_PickSku'] == null) { MT_PickSku = "-"; } else { MT_PickSku = number_format(inval['PICKEMP'][i]['MT_PickSku'],0); }
                        if(inval['PICKEMP'][i]['MT_FnshSo'] == "0"  || inval['PICKEMP'][i]['MT_FnshSo'] == null) { MT_FnshSo = "-"; } else { MT_FnshSo = number_format(inval['PICKEMP'][i]['MT_FnshSo'],0); }
                        if(inval['PICKEMP'][i]['MT_FnshSku'] == "0" || inval['PICKEMP'][i]['MT_FnshSku'] == null) { MT_FnshSku = "-"; } else { MT_FnshSku = number_format(inval['PICKEMP'][i]['MT_FnshSku'],0); }
                        if(inval['PICKEMP'][i]['MT_WorkSo'] == "0"  || inval['PICKEMP'][i]['MT_WorkSo'] == null) { MT_WorkSo = "-"; } else { MT_WorkSo = number_format(inval['PICKEMP'][i]['MT_WorkSo'],0); }
                        if(inval['PICKEMP'][i]['MT_WorkSku'] == "0" || inval['PICKEMP'][i]['MT_WorkSku'] == null) { MT_WorkSku = "-"; } else { MT_WorkSku = number_format(inval['PICKEMP'][i]['MT_WorkSku'],0); }

                        if(inval['PICKEMP'][i]['AL_WorkSo'] == "0"  || inval['PICKEMP'][i]['AL_WorkSo'] == null) { AL_WorkSo = "-"; } else { AL_WorkSo = number_format(inval['PICKEMP'][i]['AL_WorkSo'],0); }
                        if(inval['PICKEMP'][i]['AL_WorkSku'] == "0" || inval['PICKEMP'][i]['AL_WorkSku'] == null) { AL_WorkSku = "-"; } else { AL_WorkSku = number_format(inval['PICKEMP'][i]['AL_WorkSku'],0); }

                        SUMTT_CrntSo  = SUMTT_CrntSo  + parseInt(inval['PICKEMP'][i]['TT_CrntSo']);
                        SUMTT_NextSo  = SUMTT_NextSo  + parseInt(inval['PICKEMP'][i]['TT_NextSo']);
                        SUMTT_PickSo  = SUMTT_PickSo  + parseInt(inval['PICKEMP'][i]['TT_PickSo']);
                        SUMTT_PickSku = SUMTT_PickSku + parseInt(inval['PICKEMP'][i]['TT_PickSku']);
                        SUMTT_FnshSo  = SUMTT_FnshSo  + parseInt(inval['PICKEMP'][i]['TT_FnshSo']);
                        SUMTT_FnshSku = SUMTT_FnshSku + parseInt(inval['PICKEMP'][i]['TT_FnshSku']);
                        SUMTT_WorkSo  = SUMTT_WorkSo  + parseInt(inval['PICKEMP'][i]['TT_WorkSo']);
                        SUMTT_WorkSku = SUMTT_WorkSku + parseInt(inval['PICKEMP'][i]['TT_WorkSku']);

                        SUMMT_CrntSo  = SUMMT_CrntSo  + parseInt(inval['PICKEMP'][i]['MT_CrntSo']);
                        SUMMT_NextSo  = SUMMT_NextSo  + parseInt(inval['PICKEMP'][i]['MT_NextSo']);
                        SUMMT_PickSo  = SUMMT_PickSo  + parseInt(inval['PICKEMP'][i]['MT_PickSo']);
                        SUMMT_PickSku = SUMMT_PickSku + parseInt(inval['PICKEMP'][i]['MT_PickSku']);
                        SUMMT_FnshSo  = SUMMT_FnshSo  + parseInt(inval['PICKEMP'][i]['MT_FnshSo']);
                        SUMMT_FnshSku = SUMMT_FnshSku + parseInt(inval['PICKEMP'][i]['MT_FnshSku']);
                        SUMMT_WorkSo  = SUMMT_WorkSo  + parseInt(inval['PICKEMP'][i]['MT_WorkSo']);
                        SUMMT_WorkSku = SUMMT_WorkSku + parseInt(inval['PICKEMP'][i]['MT_WorkSku']);

                        SUMAL_PickSo  = SUMAL_PickSo  + parseInt(inval['PICKEMP'][i]['AL_WorkSo']);
                        SUMAL_PickSku = SUMAL_PickSku + parseInt(inval['PICKEMP'][i]['AL_WorkSku']);

                        PickEMPtBody +=
                            "<tr>"+
                                "<td>"+Online+" "+inval['PICKEMP'][i]['FullName']+"</td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PICKEMP'][i]['uKey']+"\',\'TT_CrntSo\');\">"+TT_CrntSo+"</a></td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PICKEMP'][i]['uKey']+"\',\'TT_NextSo\');\">"+TT_NextSo+"</a></td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PICKEMP'][i]['uKey']+"\',\'TT_PickSo\');\">"+TT_PickSo+"</a></td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+TT_PickSku+"</td>"+
                                "<td class='text-right text-success table-success'>"+TT_FnshSo+"</td>"+
                                "<td class='text-right text-success table-success' style='font-weight: bold;'>"+TT_FnshSku+"</td>"+
                                "<td class='text-right table-info'>"+TT_WorkSo+"</td>"+
                                "<td class='text-right table-info' style='font-weight: bold;'>"+TT_WorkSku+"</td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PICKEMP'][i]['uKey']+"\',\'MT_CrntSo\');\">"+MT_CrntSo+"</a></td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PICKEMP'][i]['uKey']+"\',\'MT_NextSo\');\">"+MT_NextSo+"</a></td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PICKEMP'][i]['uKey']+"\',\'MT_PickSo\');\">"+MT_PickSo+"</a></td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+MT_PickSku+"</td>"+
                                "<td class='text-right text-success table-success'>"+MT_FnshSo+"</td>"+
                                "<td class='text-right text-success table-success' style='font-weight: bold;'>"+MT_FnshSku+"</td>"+
                                "<td class='text-right table-info'>"+MT_WorkSo+"</td>"+
                                "<td class='text-right table-info' style='font-weight: bold;'>"+MT_WorkSku+"</td>"+
                                "<td class='text-right table-warning'>"+AL_WorkSo+"</td>"+
                                "<td class='text-right table-warning' style='font-weight: bold;'>"+AL_WorkSku+"</td>"+
                            "</tr>";
                    }
                    PickEMPtBody +=
                        "<tr class='table-active' style='font-weight: bold;'>"+
                            "<td>รวมทั้งหมด</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_CrntSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_NextSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_PickSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_PickSku,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_FnshSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_FnshSku,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_WorkSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_WorkSku,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_CrntSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_NextSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_PickSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_PickSku,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_FnshSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_FnshSku,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_WorkSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_WorkSku,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMAL_PickSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMAL_PickSku,0)+"</td>"+
                        "</tr>";
                    $("#PickEmpDetail").html(PickEMPtBody);
                }

                if(PickMthRows > 0) {
                    for(i = 0; i < PickMthRows; i++) {
                        PickMTHtBody +=
                            "<tr>"+
                                "<td>"+inval['PICKMTH'][i]['FullName']+"</td>";
                        
                        for(m = 1; m <= 12; m++) {
                            let WorkSo  = "";
                            let WorkSku = "";
                            if(m < 10) {
                                if(inval['PICKMTH'][i]['M0'+m+'_WorkSo'] == "0" || inval['PICKMTH'][i]['M0'+m+'_WorkSo'] == null) { WorkSo = "-"; } else { WorkSo = number_format(inval['PICKMTH'][i]['M0'+m+'_WorkSo'],0); }
                                if(inval['PICKMTH'][i]['M0'+m+'_WorkSku'] == "0" || inval['PICKMTH'][i]['M0'+m+'_WorkSku'] == null) { WorkSku = "-"; } else { WorkSku = number_format(inval['PICKMTH'][i]['M0'+m+'_WorkSku'],0); }
                            } else {
                                if(inval['PICKMTH'][i]['M'+m+'_WorkSo'] == "0" || inval['PICKMTH'][i]['M'+m+'_WorkSo'] == null) { WorkSo = "-"; } else { WorkSo = number_format(inval['PICKMTH'][i]['M'+m+'_WorkSo'],0); }
                                if(inval['PICKMTH'][i]['M'+m+'_WorkSku'] == "0" || inval['PICKMTH'][i]['M'+m+'_WorkSku'] == null) { WorkSku = "-"; } else { WorkSku = number_format(inval['PICKMTH'][i]['M'+m+'_WorkSku'],0); }
                            }
                            PickMTHtBody +=
                                    "<td class='text-right'>"+WorkSo+"</td>"+
                                    "<td class='text-right table-active' style='font-weight: bold;'>"+WorkSku+"</td>";
                        }
                        
                        PickMTHtBody += "</tr>";

                    }
                    $("#PickMthDetail").html(PickMTHtBody);
                }

                if(PackEmpRows > 0) {
                    for(i = 0; i < PackEmpRows; i++) {

                        if(inval['PACKEMP'][i]['TT_CrntIV'] == "0" || inval['PACKEMP'][i]['TT_CrntIV'] == null) { TT_CrntIV = "-"; } else { TT_CrntIV = number_format(inval['PACKEMP'][i]['TT_CrntIV'],0); }
                        if(inval['PACKEMP'][i]['TT_CrntSku'] == "0" || inval['PACKEMP'][i]['TT_CrntSku'] == null) { TT_CrntSku = "-"; } else { TT_CrntSku = number_format(inval['PACKEMP'][i]['TT_CrntSku'],0); }
                        if(inval['PACKEMP'][i]['TT_PackIV'] == "0" || inval['PACKEMP'][i]['TT_PackIV'] == null) { TT_PackIV = "-"; } else { TT_PackIV = number_format(inval['PACKEMP'][i]['TT_PackIV'],0); }
                        if(inval['PACKEMP'][i]['TT_PackBox'] == "0" || inval['PACKEMP'][i]['TT_PackBox'] == null) { TT_PackBox = "-"; } else { TT_PackBox = number_format(inval['PACKEMP'][i]['TT_PackBox'],0); }
                        if(inval['PACKEMP'][i]['TT_FnshIV'] == "0" || inval['PACKEMP'][i]['TT_FnshIV'] == null) { TT_FnshIV = "-"; } else { TT_FnshIV = number_format(inval['PACKEMP'][i]['TT_FnshIV'],0); }
                        if(inval['PACKEMP'][i]['TT_FnshBox'] == "0" || inval['PACKEMP'][i]['TT_FnshBox'] == null) { TT_FnshBox = "-"; } else { TT_FnshBox = number_format(inval['PACKEMP'][i]['TT_FnshBox'],0); }
                        if(inval['PACKEMP'][i]['TT_WorkIV'] == "0" || inval['PACKEMP'][i]['TT_WorkIV'] == null) { TT_WorkIV = "-"; } else { TT_WorkIV = number_format(inval['PACKEMP'][i]['TT_WorkIV'],0); }
                        if(inval['PACKEMP'][i]['TT_WorkBox'] == "0" || inval['PACKEMP'][i]['TT_WorkBox'] == null) { TT_WorkBox = "-"; } else { TT_WorkBox = number_format(inval['PACKEMP'][i]['TT_WorkBox'],0); }

                        if(inval['PACKEMP'][i]['MT_CrntIV'] == "0" || inval['PACKEMP'][i]['MT_CrntIV'] == null) { MT_CrntIV = "-"; } else { MT_CrntIV = number_format(inval['PACKEMP'][i]['MT_CrntIV'],0); }
                        if(inval['PACKEMP'][i]['MT_CrntSku'] == "0" || inval['PACKEMP'][i]['MT_CrntSku'] == null) { MT_CrntSku = "-"; } else { MT_CrntSku = number_format(inval['PACKEMP'][i]['MT_CrntSku'],0); }
                        if(inval['PACKEMP'][i]['MT_PackIV'] == "0" || inval['PACKEMP'][i]['MT_PackIV'] == null) { MT_PackIV = "-"; } else { MT_PackIV = number_format(inval['PACKEMP'][i]['MT_PackIV'],0); }
                        if(inval['PACKEMP'][i]['MT_PackBox'] == "0" || inval['PACKEMP'][i]['MT_PackBox'] == null) { MT_PackBox = "-"; } else { MT_PackBox = number_format(inval['PACKEMP'][i]['MT_PackBox'],0); }
                        if(inval['PACKEMP'][i]['MT_FnshIV'] == "0" || inval['PACKEMP'][i]['MT_FnshIV'] == null) { MT_FnshIV = "-"; } else { MT_FnshIV = number_format(inval['PACKEMP'][i]['MT_FnshIV'],0); }
                        if(inval['PACKEMP'][i]['MT_FnshBox'] == "0" || inval['PACKEMP'][i]['MT_FnshBox'] == null) { MT_FnshBox = "-"; } else { MT_FnshBox = number_format(inval['PACKEMP'][i]['MT_FnshBox'],0); }
                        if(inval['PACKEMP'][i]['MT_WorkIV'] == "0" || inval['PACKEMP'][i]['MT_WorkIV'] == null) { MT_WorkIV = "-"; } else { MT_WorkIV = number_format(inval['PACKEMP'][i]['MT_WorkIV'],0); }
                        if(inval['PACKEMP'][i]['MT_WorkBox'] == "0" || inval['PACKEMP'][i]['MT_WorkBox'] == null) { MT_WorkBox = "-"; } else { MT_WorkBox = number_format(inval['PACKEMP'][i]['MT_WorkBox'],0); }

                        if(inval['PACKEMP'][i]['AL_WorkIV'] == "0" || inval['PACKEMP'][i]['AL_WorkIV'] == null) { AL_WorkIV = "-"; } else { AL_WorkIV = number_format(inval['PACKEMP'][i]['AL_WorkIV'],0); }
                        if(inval['PACKEMP'][i]['AL_WorkBox'] == "0" || inval['PACKEMP'][i]['AL_WorkBox'] == null) { AL_WorkBox = "-"; } else { AL_WorkBox = number_format(inval['PACKEMP'][i]['AL_WorkBox'],0); }

                        SUMTT_CrntIV  = SUMTT_CrntIV  + parseInt(inval['PACKEMP'][i]['TT_CrntIV']);
                        SUMTT_CrntSku = SUMTT_CrntSku + parseInt(inval['PACKEMP'][i]['TT_CrntSku']);
                        SUMTT_PackIV  = SUMTT_PackIV  + parseInt(inval['PACKEMP'][i]['TT_PackIV']);
                        SUMTT_PackBox = SUMTT_PackBox + parseInt(inval['PACKEMP'][i]['TT_PackBox']);
                        SUMTT_FnshIV  = SUMTT_FnshIV  + parseInt(inval['PACKEMP'][i]['TT_FnshIV']);
                        SUMTT_FnshBox = SUMTT_FnshBox + parseInt(inval['PACKEMP'][i]['TT_FnshBox']);
                        SUMTT_WorkIV  = SUMTT_WorkIV  + parseInt(inval['PACKEMP'][i]['TT_WorkIV']);
                        SUMTT_WorkBox = SUMTT_WorkBox + parseInt(inval['PACKEMP'][i]['TT_WorkBox']);

                        SUMMT_CrntIV  = SUMMT_CrntIV  + parseInt(inval['PACKEMP'][i]['MT_CrntIV']);
                        SUMMT_CrntSku = SUMMT_CrntSku + parseInt(inval['PACKEMP'][i]['MT_CrntSku']);
                        SUMMT_PackIV  = SUMMT_PackIV  + parseInt(inval['PACKEMP'][i]['MT_PackIV']);
                        SUMMT_PackBox = SUMMT_PackBox + parseInt(inval['PACKEMP'][i]['MT_PackBox']);
                        SUMMT_FnshIV  = SUMMT_FnshIV  + parseInt(inval['PACKEMP'][i]['MT_FnshIV']);
                        SUMMT_FnshBox = SUMMT_FnshBox + parseInt(inval['PACKEMP'][i]['MT_FnshBox']);
                        SUMMT_WorkIV  = SUMMT_WorkIV  + parseInt(inval['PACKEMP'][i]['MT_WorkIV']);
                        SUMMT_WorkBox = SUMMT_WorkBox + parseInt(inval['PACKEMP'][i]['MT_WorkBox']);

                        SUMAL_PackIV  = SUMAL_PackIV  + parseInt(inval['PACKEMP'][i]['AL_WorkIV']);
                        SUMAL_PackBox = SUMAL_PackBox + parseInt(inval['PACKEMP'][i]['AL_WorkBox']);

                        PackEMPtBody +=
                            "<tr>"+
                                "<td>"+inval['PACKEMP'][i]['FullName']+"</td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PACKEMP'][i]['TableID']+"\',\'TT_CrntIV\');\">"+TT_CrntIV+"</a></td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+TT_CrntSku+"</td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PACKEMP'][i]['TableID']+"\',\'TT_PackIV\');\">"+TT_PackIV+"</a></td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+TT_PackBox+"</td>"+
                                "<td class='text-right table-success text-success'>"+TT_FnshIV+"</a></td>"+
                                "<td class='text-right table-success text-success' style='font-weight: bold;'>"+TT_FnshBox+"</td>"+
                                "<td class='text-right table-info'>"+TT_WorkIV+"</a></td>"+
                                "<td class='text-right table-info' style='font-weight: bold;'>"+TT_WorkBox+"</td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PACKEMP'][i]['TableID']+"\',\'MT_CrntIV\');\">"+MT_CrntIV+"</a></td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+MT_CrntSku+"</td>"+
                                "<td class='text-right'><a href='javascript:void(0);' onclick=\"GetSO(\'"+inval['PACKEMP'][i]['TableID']+"\',\'MT_PackIV\');\">"+MT_PackIV+"</a></td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+MT_PackBox+"</td>"+
                                "<td class='text-right table-success text-success'>"+MT_FnshIV+"</a></td>"+
                                "<td class='text-right table-success text-success' style='font-weight: bold;'>"+MT_FnshBox+"</td>"+
                                "<td class='text-right table-info'>"+MT_WorkIV+"</a></td>"+
                                "<td class='text-right table-info' style='font-weight: bold;'>"+MT_WorkBox+"</td>"+
                                "<td class='text-right table-warning'>"+AL_WorkIV+"</a></td>"+
                                "<td class='text-right table-warning' style='font-weight: bold;'>"+AL_WorkBox+"</td>"+
                            "</tr>";

                    }
                    PackEMPtBody +=
                        "<tr class='table-active' style='font-weight: bold;'>"+
                            "<td>รวมทั้งหมด</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_CrntSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_CrntSku,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_PackIV,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_PackBox,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_FnshIV,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_FnshBox,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_WorkIV,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMTT_WorkBox,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_CrntSo,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_CrntSku,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_PackIV,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_PackBox,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_FnshIV,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_FnshBox,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_WorkIV,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMMT_WorkBox,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMAL_PackIV,0)+"</td>"+
                            "<td class='text-right'>"+number_format(SUMAL_PackBox,0)+"</td>"+
                        "</tr>";
                    $("#PackEmpDetail").html(PackEMPtBody);
                }

                if(PackMthRows > 0) {
                    for(i = 0; i < PackMthRows; i++) {
                        PackMTHtBody += 
                            "<tr>"+
                                "<td>"+inval['PACKMTH'][i]['FullName']+"</td>";
                                for(let m = 1; m <= 12; m++) {
                                    if(m < 10) {
                                        PackMTHtBody += "<td class='text-right'>"+inval['PACKMTH'][i]['M0'+m+'_WorkIV']+"</td>";
                                        PackMTHtBody += "<td class='text-right table-active fw-bolder'>"+inval['PACKMTH'][i]['M0'+m+'_WorkSku']+"</td>";
                                    }else{
                                        PackMTHtBody += "<td class='text-right'>"+inval['PACKMTH'][i]['M'+m+'_WorkIV']+"</td>";
                                        PackMTHtBody += "<td class='text-right table-active fw-bolder'>"+inval['PACKMTH'][i]['M'+m+'_WorkSku']+"</td>";
                                    }
                                }
                            PackMTHtBody += "</tr>";
                    }
                    $("#PackMthDetail").html(PackMTHtBody);
                }

                $(".overlay").hide();
            });
        }
    });
}
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */

$(document).ready(function(){
    CallHead();
    GetData();
});
</script> 
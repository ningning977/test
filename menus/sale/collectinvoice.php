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
                    <div class="col-lg-3 col-8">
                        <div class="form-group">
                            <label for="filt_user">เลือกพนักงานขาย</label>
                            <select class="form-select form-select-sm" name="filt_user" id="filt_user">
                                <option value="NULL">กรุณาเลือก</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-4">
                        <div class="form-group">
                            <label for="filt_type">เลือกรายการ</label>
                            <select class="form-select form-select-sm" name="filt_type" id="filt_type">
                                <option value="ALL">รายการทั้งหมด</option>
                                <option value="BONUS">เลือกเฉพาะรายการที่ได้โบนัส</option>
                                <option value="OVDUE">เลือกเฉพาะรายการที่เกินกำหนด +30 วัน</option>
                                <option value="Faxkay">เลือกเฉพาะรายการฝากขาย</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1 col-3">
                        <div class="form-group mb-3">
                            <label for="btn-print">&nbsp;</label>
                            <button type="button" class="btn btn-secondary btn-block btn-sm" id="btn-print" name="btn-print" onclick="PrintDoc()";><i class="fas fa-print fa-fw fa-1x"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-1 col-3">
                        <div class="form-group mb-3">
                            <label for="btn-excel">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-block btn-sm" id="btn-excel" name="btn-excel" onclick="ExportDoc()"><i class="fas fa-file-excel fa-fw fa-1x"></i></button>
                        </div>
                    </div>

                    <div class="offset-lg-2 col-lg-3 col-6">
                        <div class="form-group">
                            <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา:</label>
                            <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรอกข้อความเพื่อค้นหา" />
                        </div>
                    </div>
                </div>

                <div class="row pb-2">
                    <div class="col">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="TabActive nav-link active" id="Bill-tab" data-bs-toggle="tab" data-bs-target="#Bill" type="button" role="tab" aria-controls="Bill" aria-selected="true">บิลเกินกำหนด</button>
                                <button class="TabActive nav-link" id="CheckR-tab" data-bs-toggle="tab" data-bs-target="#CheckR" type="button" role="tab" aria-controls="CheckR" aria-selected="false">เช็คคืน</button>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="Bill" role="tabpanel" aria-labelledby="Bill-tab">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm" style="font-size: 12px;">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="2" width="7.5%">เลขที่<br/>เอกสาร</th>
                                                <th rowspan="2" width="6.5%">วันที่<br/>เอกสาร</th>
                                                <th rowspan="2" width="6.5%">วันที่<br/>ครบกำหนด</th>
                                                <th rowspan="2" width="5%">จำนวนวัน<br/>เกินกำหนด</th>
                                                <th rowspan="2">ร้านค้า</th>
                                                <th rowspan="2" width="7.5%">มูลค่าสุทธิ<br/>(บาท)</th>
                                                <th rowspan="2" width="7.5%">ยอดค้างชำระ<br/>(บาท)</th>
                                                <th rowspan="2" width="12.5%">หมายเหตุ</th>
                                                <th colspan="3">ค่าปรับ (บาท)</th>
                                                <th rowspan="2" width="4%">โบนัส</th>
                                                <th rowspan="2" width="5.5%">วิธี<br/>วางบิล</th>
                                                <th rowspan="2" width="5.5%">วิธี<br/>เก็บเงิน</th>
                                            </tr>
                                            <tr>
                                                <th width="4.5%">SALE</th>
                                                <th width="4.5%">SUP.</th>
                                                <th width="4.5%">MGR.</th>
                                            </tr>
                                        </thead>
                                        <tbody id="view_collectlist">
                                            <tr><td class="text-center" colspan="14">กรุณาเลือกพนักงานขาย</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-12" id="view_overdue"></div>
                            <div class="col-lg-4 col-12" id="view_finedue"></div>
                            <div class="col-lg-4 col-12" id="view_bonusdue"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="CheckR" role="tabpanel" aria-labelledby="CheckR-tab">
                        <div class="row mt-3">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm" style="font-size: 12px;" id="ChqList">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="6.5%" rowspan="2">เลขที่เอกสาร</th>
                                            <th width="5.5%" rowspan="2">วันที่<br/>เซลส์รับทราบ</th>
                                            <th rowspan="6">ชื่อคู่ค้า</th>
                                            <th width="5.5%" rowspan="2">วันที่<br/>เช็คเด้ง</th>
                                            <th width="3.5%" rowspan="2">จำนวน<br/>(วัน)</th>
                                            <th rowspan="2">พนักงานขาย</th>
                                            <th rowspan="2">สาเหตุเช็คเด้ง</th>
                                            <th width="5.5%" rowspan="2">เลขที่เช็ค</th>
                                            <th width="5.5%" rowspan="2">จำนวนเงิน<br/>(บาท)</th>
                                            <th width="5.5%" rowspan="2">ยอดคงเหลือ<br/>(บาท)</th>
                                            <th rowspan="2">หมายเหตุ</th>
                                            <th colspan="4">ค่าปรับ (บาท)</th>
                                        </tr>
                                        <tr>
                                            <th width="3.5%">รวม</th>
                                            <th width="3.5%">เซลส์</th>
                                            <th width="3.5%">หนง.</th>
                                            <th width="3.5%">ผจก.</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot style="font-weight: bold;">
                                        <tr>
                                            <td class="text-right" colspan="8">รวมทั้งหมด</td>
                                            <td class="text-right" id="SUM_CheckSUM"></td>
                                            <td class="text-right text-danger" id="SUM_Applied"></td>
                                            <td>&nbsp;</td>
                                            <td class="text-right text-danger" id="SUM_FineALL"></td>
                                            <td class="text-right text-danger" id="SUM_FineSAL"></td>
                                            <td class="text-right text-danger" id="SUM_FineSUP"></td>
                                            <td class="text-right text-danger" id="SUM_FineMGR"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalBonusCond" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle fa-fw fa-lg"></i> เงื่อนไขโบนัส</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 13px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="70%">ยอดขายในเดือน<br/>ที่นำมาคิดโบนัส (บาท)</th>
                                    <th width="30%">โบนัสที่จะได้รับ<br/>(บาท)</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009") { ?>
                                <tr>
                                    <td>ตั้งแต่ 500,001 - 600,000</td>
                                    <td style="font-weight: bold;" class="text-right">500</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 600,001 - 700,000</td>
                                    <td style="font-weight: bold;" class="text-right">500</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 700,001 - 800,000</td>
                                    <td style="font-weight: bold;" class="text-right">700</td>
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td>ตั้งแต่ 800,001 - 900,000</td>
                                    <td style="font-weight: bold;" class="text-right">1,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 900,001 - 1,000,000</td>
                                    <td style="font-weight: bold;" class="text-right">2,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,000,001 - 1,100,000</td>
                                    <td style="font-weight: bold;" class="text-right">4,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,100,001 - 1,200,000</td>
                                    <td style="font-weight: bold;" class="text-right">5,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,200,001 - 1,300,000</td>
                                    <td style="font-weight: bold;" class="text-right">6,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,300,001 - 1,400,000</td>
                                    <td style="font-weight: bold;" class="text-right">8,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,400,001 - 1,500,000</td>
                                    <td style="font-weight: bold;" class="text-right">9,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,500,001 - 1,600,000</td>
                                    <td style="font-weight: bold;" class="text-right">11,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,600,001 - 1,700,000</td>
                                    <td style="font-weight: bold;" class="text-right">13,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,700,001 - 1,800,000</td>
                                    <td style="font-weight: bold;" class="text-right">17,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,800,001 - 1,900,000</td>
                                    <td style="font-weight: bold;" class="text-right">20,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 1,900,001 - 2,000,000</td>
                                    <td style="font-weight: bold;" class="text-right">24,000</td>
                                </tr>
                                <tr>
                                    <td>ตั้งแต่ 2,000,001 ขึ้นไป</td>
                                    <td style="font-weight: bold;" class="text-right">28,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalFineCond" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle fa-fw fa-lg"></i> เงื่อนไขค่าปรับ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 13px;">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2">รายละเอียด</th>
                                    <th rowspan="2">ค่าปรับ</th>
                                    <th colspan="3">สัดส่วนการรับผิดชอบค่าปรับ (%)</th>
                                </tr>
                                <tr>
                                    <th width="10%">SALE</th>
                                    <th width="10%">SUP.</th>
                                    <th width="10%">MGR.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-active">
                                    <th colspan="5">เงื่อนไข 1: บิลทั่วไป</th>
                                </tr>
                                <tr>
                                    <td>หนี้เกินกำหนด <strong>น้อยกว่า 30 วัน</strong></td>
                                    <td class="text-center">ไม่มีค่าปรับ</td>
                                    <td class="text-center">&dash;</td>
                                    <td class="text-center">&dash;</td>
                                    <td class="text-center">&dash;</td>
                                </tr>
                                <tr>
                                    <td>หนี้เกินกำหนด <strong>31 - 60 วัน</strong></td>
                                    <td><strong class="text-danger">0.5%</strong> ของยอดค้างชำระ</td>
                                    <td rowspan="3" class="text-center">70</td>
                                    <td rowspan="3" class="text-center">20</td>
                                    <td rowspan="3" class="text-center">10</td>
                                </tr>
                                <tr>
                                    <td>หนี้เกินกำหนด <strong>61 - 90 วัน</strong></td>
                                    <td><strong class="text-danger">1%</strong> ของยอดค้างชำระ</td>
                                </tr>
                                <tr>
                                    <td>หนี้เกินกำหนด <strong>มากกว่า 90 วัน</strong></td>
                                    <td><strong class="text-danger">3%</strong> ของยอดค้างชำระ</td>
                                </tr>
                                <tr class="table-active">
                                    <th colspan="5">เงื่อนไข 2: บิลซ่อมสินค้าหน้าร้าน (พนักงานขาย 60-ซ่อมสินค้า / ฝ่ายขายหน้าร้านรับผิดชอบค่าปรับ)</th>
                                </tr>
                                <tr>
                                    <td>หนี้เกินกำหนด <strong>น้อยกว่า 30 วัน</strong></td>
                                    <td class="text-center">ไม่มีค่าปรับ</td>
                                    <td class="text-center">&dash;</td>
                                    <td class="text-center">&dash;</td>
                                    <td class="text-center">&dash;</td>
                                </tr>
                                <tr>
                                    <td>หนี้เกินกำหนด <strong>31 - 60 วัน</strong></td>
                                    <td><strong class="text-danger">0.5%</strong> ของยอดค้างชำระ</td>
                                    <td rowspan="3" class="text-center">&dash;</td>
                                    <td rowspan="3" class="text-center">50</td>
                                    <td rowspan="3" class="text-center">50</td>
                                </tr>
                                <tr>
                                    <td>หนี้เกินกำหนด <strong>61 - 90 วัน</strong></td>
                                    <td><strong class="text-danger">1%</strong> ของยอดค้างชำระ</td>
                                </tr>
                                <tr>
                                    <td>หนี้เกินกำหนด <strong>มากกว่า 90 วัน</strong></td>
                                    <td><strong class="text-danger">3%</strong> ของยอดค้างชำระ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

function GetEmpName() {
    $.ajax({
        url: "menus/sale/ajax/ajaxcollectinvoice.php?p=GetEmpName",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#filt_user").append(inval['filt_user']);
            });
        }
    })
}

function GetInvoice(filt_user,filt_type) {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxcollectinvoice.php?p=GetInvoice",
        type: "POST",
        data: { u: filt_user, t: filt_type },
        success: function(result) {
            var obj= jQuery.parseJSON(result);
            $.each(obj,function(key,inval){
                $(".overlay").hide();
                $("#view_collectlist").html(inval['view_collectlist']);
                $("#view_overdue").html(inval['view_overdue']);
                $("#view_finedue").html(inval['view_finedue']);
                $("#view_bonusdue").html(inval['view_bonusdue']);
                $(".CollectRemark").on("focusout",function(){
                    var DocType  = $(this).attr("data-DocType");
                    var DocEntry = $(this).attr("data-DocEntry");
                    var DocText  = $(this).val();
                    SaveRemark(DocType, DocEntry, DocText);
                });

                GetChkReturn(filt_user);
            });
        }
    })
}

function OpenBonusCond() {
    $(".modal").modal("hide");
    $("#ModalBonusCond").modal("show");
}

function OpenFineCond() {
    $(".modal").modal("hide");
    $("#ModalFineCond").modal("show");
}

function SaveRemark(DocType, DocEntry, DocText) {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxcollectinvoice.php?p=SaveRemark",
        type: "POST",
        data: { DocType: DocType, DocEntry: DocEntry, DocText: DocText },
        success: function(result) {
            $(".overlay").hide();
        }
    });
}

function PrintDoc() {
    var ukey = $("#filt_user").val();
    switch(ukey) {
        case "NULL":
        case "B11":
        case "B98":
        case "B99":
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("ไม่สามารถเลือกรายการนี้ได้");
            $("#alert_modal").modal('show');
            break;
        default:
            window.open('menus/sale/print/printclctiv.php?u='+ukey,'_blank');
            break;
    }
}

function ExportDoc() {
    var ukey = $("#filt_user").val();
    $(".overlay").show();
    switch(ukey) {
        case "NULL":
        case "B11":
            $(".overlay").hide();
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("ไม่สามารถเลือกรายการนี้ได้");
            $("#alert_modal").modal('show');
            
            break;
        default:
            if($(".TabActive.active").attr("aria-controls") == 'Bill') {
                $.ajax({
                    url: "menus/sale/ajax/ajaxExportClctIv.php",
                    type: "POST",
                    data: { u: ukey },
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj, function(key, inval) {
                            $(".overlay").hide();
                            if(inval['ExportStatus'] == "SUCCESS") {
                                window.open("../../FileExport/CollectInvoice/"+inval['FileName'],'_blank');
                            } else {
                                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                $("#alert_body").html("ไม่สามารถส่งออกได้เนื่องจากไม่มีรายการหนี้เกินกำหนด");
                                $("#alert_modal").modal('show');
                            }
                        });
                    }
                });
            }else{
                $.ajax({
                    url: "menus/account/ajax/ajaxExportChqRT.php",
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj, function(key,inval) {
                            $(".overlay").hide();
                            if(inval['ExportStatus'] == "SUCCESS") {
                                window.open("../../FileExport/ChqReturn/"+inval['FileName'],'_blank');
                            } else {
                                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                $("#alert_body").html("ไม่สามารถส่งออกได้เนื่องจากไม่มีรายการหนี้เกินกำหนด");
                                $("#alert_modal").modal('show');
                            }
                        });
                    }
                });
            }
        break;
    }
}

$(document).ready(function(){
    CallHead();
    GetEmpName();
    setTimeout(() => {
        var filt_user = $("#filt_user").val();
        var filt_type = $("#filt_type").val();
        if(filt_user != "NULL") {
            GetInvoice(filt_user,filt_type);
        }
    }, 1500);
});

$("#filt_user, #filt_type").on("change", function(e) {
    e.preventDefault();
    var filt_user = $("#filt_user").val();
    var filt_type = $("#filt_type").val();
    GetInvoice(filt_user,filt_type);
});

/* เมื่อกรอกข้อความสำหรับค้นหา */
$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    if($(".TabActive.active").attr("aria-controls") == 'Bill') {
        $("#view_collectlist tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    }else{
        $("#ChqList tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    }
});

function GetChkReturn(filt_user) {
    $.ajax({
        url: "menus/sale/ajax/ajaxcollectinvoice.php?p=GetChkReturn",
        type: "POST",
        data: { uKey: filt_user, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                let row = "";
                if(inval['Rows'] == 0) {
                    row = "<tr><td class='text-center' colspan='16'>ไม่มีข้อมูล :(</td></tr>";
                } else {
                    for(i = 0; i < inval['Rows']; i++) {
                        row += 
                            "<tr>"+
                                "<td class='text-center align-baseline'>"+inval['BD_'+i]['DocNum']+"</td>"+
                                "<td class='text-center align-baseline'>"+inval['BD_'+i]['CHQ_SaleReceive']+"</td>"+
                                "<td class='align-baseline'>"+inval['BD_'+i]['CardCode']+"</td>"+
                                "<td class='text-center align-baseline'>"+inval['BD_'+i]['CHQ_DateReturn']+"</td>"+
                                "<td class='text-right align-baseline'><strong>"+inval['BD_'+i]['DateDiff']+"</strong></td>"+
                                "<td class='align-baseline'>"+inval['BD_'+i]['SalesName']+"</td>"+
                                "<td class='align-baseline'>"+inval['BD_'+i]['CauseReturn']+"</td>"+
                                "<td class='text-center align-baseline'>"+inval['BD_'+i]['CHQ_No']+"</td>"+
                                "<td class='text-right align-baseline'>"+inval['BD_'+i]['CHQ_Amount']+"</td>"+
                                "<td class='text-right text-danger align-baseline'>"+inval['BD_'+i]['Balance']+"</td>"+
                                "<td class='align-baseline'>"+inval['BD_'+i]['Remark']+"</td>"+
                                "<td class='text-right text-danger align-baseline'><strong>"+inval['BD_'+i]['FineALL']+"</strong></td>"+
                                "<td class='text-right text-danger align-baseline'>"+inval['BD_'+i]['FineSAL']+"</td>"+
                                "<td class='text-right text-danger align-baseline'>"+inval['BD_'+i]['FineSUP']+"</td>"+
                                "<td class='text-right text-danger align-baseline'>"+inval['BD_'+i]['FineMGR']+"</td>"+
                            "</tr>";
                    }
                    $("#SUM_CheckSUM").html(inval['FT']['SUM_CheckSUM']);
                    $("#SUM_Applied").html(inval['FT']['SUM_Applied']);

                    $("#SUM_FineALL").html(inval['FT']['SUM_FineALL']);
                    $("#SUM_FineSAL").html(inval['FT']['SUM_FineSAL']);
                    $("#SUM_FineSUP").html(inval['FT']['SUM_FineSUP']);
                    $("#SUM_FineMGR").html(inval['FT']['SUM_FineMGR']);
                }
                $("#ChqList tbody").html(row);
            });
        }
    })
}

$(".TabActive").on("click",function() {
    if($(this).attr("aria-controls") == 'Bill') {
        $("#btn-print").prop('disabled', false);
    }else{
        $("#btn-print").prop('disabled', true);
    }
})

</script> 
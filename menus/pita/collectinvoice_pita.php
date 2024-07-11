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
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="filt_user">เลือกพนักงานขาย</label>
                            <select class="form-select form-select-sm" name="filt_user" id="filt_user">
                                <option value="" selected disabled>กรุณาเลือก</option>
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
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group mb-3">
                            <label for="btn-print">&nbsp;</label>
                            <button type="button" class="btn btn-secondary btn-block btn-sm" id="btn-print" name="btn-print" onclick="PrintDoc()";><i class="fas fa-print fa-fw fa-1x"></i> Print</button>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group mb-3">
                            <label for="btn-excel">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-block btn-sm" id="btn-excel" name="btn-excel" onclick="ExportDoc()"><i class="fas fa-file-excel fa-fw fa-1x"></i> Excel</button>
                        </div>
                    </div>

                    <div class="col-lg d-flex justify-content-end">
                        <div class="form-group" style='width: 250px;'>
                            <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา:</label>
                            <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรอกข้อความเพื่อค้นหา" />
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
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
        </div>
    </div>
</section>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
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
$(document).ready(function(){
    GetSlpCode();
    GetInvoice('PITA','ALL');
});

function GetSlpCode() {
    var Dept = '<?php echo $_SESSION['DeptCode']; ?>';
    $.ajax({
        url: "menus/pita/ajax/ajaxcollectinvoice_pita.php?a=GetSlpCode",
        type: "POST",
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                let slct = "";
                let Rows = inval['Rows'];
                let DeptCode = '<?php echo $_SESSION['DeptCode']; ?>';
                for(i = 0; i < Rows; i++) {
                    let s = "";
                    if(inval[i]['ukey'] == '<?php echo $_SESSION['ukey']; ?>') {
                        s = "selected";
                    }
                    if ((Dept == 'DP009' || Dept == 'DP002') && i == 0){
                        s = " selected ";
                    }else{
                        s = " ";
                    }
                    slct += "<option value='"+inval[i]['ukey']+"' "+s+">"+inval[i]['SlpName']+"</option>";
                }
                $("#filt_user").append(slct);
            });
        }
    })
}

$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#view_collectlist tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

$("#filt_user, #filt_type").on("change", function(e) {
    e.preventDefault();
    var filt_user = $("#filt_user").val();
    var filt_type = $("#filt_type").val();
    GetInvoice(filt_user,filt_type);
});

function GetInvoice(filt_user,filt_type) {
    $(".overlay").show();
    let PITA = "PITA";
    $.ajax({
        url: "menus/sale/ajax/ajaxcollectinvoice.php?p=GetInvoice",
        type: "POST",
        data: { u: filt_user, t: filt_type, PITA : PITA },
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
            });
        }
    })
}

function SaveRemark(DocType, DocEntry, DocText) {
    $(".overlay").show();
    let PITA = "PITA";
    $.ajax({
        url: "menus/sale/ajax/ajaxcollectinvoice.php?p=SaveRemark",
        type: "POST",
        data: { DocType: DocType, DocEntry: DocEntry, DocText: DocText, PITA : PITA },
        success: function(result) {
            $(".overlay").hide();
        }
    });
}

function PrintDoc() {
    let ukey = $("#filt_user").val();
    let PITA = "PITA";
    if(ukey != null) {
        window.open('menus/sale/print/printclctiv.php?u='+ukey+'&PITA='+PITA,'_blank');
    }else{
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกพนักงานก่อน");
        $("#alert_modal").modal('show');
    }
}

function ExportDoc() {
    $(".overlay").show();
    let ukey = $("#filt_user").val();
    let PITA = "PITA";
    switch(ukey) {
        case "NULL":
        case "B11":
            $(".overlay").hide();
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("ไม่สามารถเลือกรายการนี้ได้");
            $("#alert_modal").modal('show');
            
            break;
        default:
            $.ajax({
                url: "menus/sale/ajax/ajaxExportClctIv.php",
                type: "POST",
                data: { u: ukey, PITA : PITA },
                success: function(result) {
                    let obj = jQuery.parseJSON(result);
                    $.each(obj, function(key, inval) {
                        $(".overlay").hide();
                        if(inval['ExportStatus'] == "SUCCESS") {
                            window.open("../../FileExport/CollectInvoicePTA/"+inval['FileName'],'_blank');
                        } else {
                            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                            $("#alert_body").html("ไม่สามารถส่งออกได้เนื่องจากไม่มีรายการหนี้เกินกำหนด");
                            $("#alert_modal").modal('show');
                        }
                    });
                }
            });
            break;
    }
}
</script> 
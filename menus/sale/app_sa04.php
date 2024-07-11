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
                <div class="table-responsive mt-4">
                    <table class="table table-sm table-hover table-bordered" style="font-size: 12px;">
                        <thead class="text-center">
                            <tr>
                                <th width="3.5%">ลำดับ</th>
                                <th width="7.5%">วันที่เอกสาร</th>
                                <th width="10%">เลขที่เอกสาร</th>
                                <th>ชื่อลูกค้า</th>
                                <th width="15%">พนักงานขาย</th>
                                <th width="10%">ผู้จัดทำ</th>
                                <th width="7.5%">เอกสารอ้างอิง</th>
                                <th width="7.5%">ยอดลดหนี้ /<br/>ลดจ่ายสุทธิ</th>
                                <th width="7.5%">สถานะเอกสาร</th>
                            </tr>
                        </thead>
                        <tbody id="DocListTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL SAVE SUCCESS -->
<div class="modal fade" id="confirm_saved" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-check-circle fa-fw fa-lg text-success"></i> สำเร็จ</h5>
                <p id="confirm_body" class="my-4">บันทึกข้อมูลสำเร็จ</p>
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PREVIEW -->
<div class="modal fade" id="PreviewDoc" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file fa-fw fa-1x"></i> เอกสารเลขที่: <span class="ViewDocNum"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-lg">
                        <table class="table table-borderless table-sm" style="font-size: 12px;">
                            <tr>
                                <td width="20%" style="font-weight: bold;">เลขที่เอกสาร</td>
                                <td width="30%" class="ViewDocNum"></td>
                                <td width="20%" style="font-weight: bold;">วันที่เอกสาร</td>
                                <td width="30%" id="ViewDocDate"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">ชื่อร้านค้า</td>
                                <td colspan="3" id="ViewCardCode"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">เหตุผลขอการลดหนี้/ลดจ่าย</td>
                                <td colspan="3" id="ViewDocRemark"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">เงื่อนไขการลดหนี้/ลดจ่าย</td>
                                <td colspan="3" id="ViewDocType"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">ผู้จัดทำ</td>
                                <td colspan="3" id="ViewCreateName"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- DOC TAB -->
                <ul class="nav nav-tabs" id="Doc-Tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#DocDetail" class="btn btn-tabs nav-link active" id="DocDetailTab" data-bs-toggle="tab" data-bs-target="#DocDetail" role="tab" data-tabs="0" aria-controls="DocDetailTab" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-list-ol fa-fw fa-1x"></i> รายละเอียด
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#ApproveList" class="btn btn-tabs nav-link" id="DocApproveTab" data-bs-toggle="tab" data-bs-target="#ApproveList" role="tab" data-tabs="1" aria-controls="ApproveList" aria-selected="true" style="font-size: 12px;">
                            <i class="fas fa-tasks fa-fw fa-1x"></i> สถานะการอนุมัติ
                        </a>
                    </li>
                </ul>
                <!-- CONTENT TAB -->
                <div class="tab-content">
                    <div class="tab-pane show active" id="DocDetail" role="tabpanel" aria-labelledby="DocDetailTab" style="font-size: 12px;"></div>
                    <div class="tab-pane show" id="ApproveList" role="tabpanel" aria-labelledby="DocApproveTab" style="font-size: 12px;">
                        <div class="row mt-4">
                            <div class="col-12" id="preview_Approve">
                                <table class="table table-bordered" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="2" width="5%">ลำดับ</th>
                                            <th rowspan="2" width="15%">ผู้อนุมัติ</th>
                                            <th rowspan="2" width="10%">ผลการ<br/>พิจารณา</th>
                                            <th colspan="3">ค่าปรับ</th>
                                            <th rowspan="2">หมายเหตุ</th>
                                            <th rowspan="2" width="15%" >วันที่อนุมัติ</th>
                                            <th rowspan="2" width="5%">บันทึก</th>
                                        </tr>
                                        <tr>
                                            <th width="10%">ไม่ปรับ</th>
                                            <th width="10%">ปรับพนักงานขาย<br/>(100 บาท)</th>
                                            <th width="10%">ปรับธุรการขาย</br/>(20 บาท)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ApproveListTable"></tbody>
                                </table>
                            </div>
                        </div>
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

function number_format(number,decimal) {
     var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
     var formatter = new Intl.NumberFormat("en",options);
     return formatter.format(number)
}

function GetApproveList() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxapp_sa04.php?p=DocList",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#DocListTable").html(inval['DocList']);
            });
            $(".overlay").hide();
        }
    });
}

function PreviewSA04(DocEntry,int_status) {
    /* do something */
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxDocSA04.php?p=PreviewDoc&App=Y",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $(".ViewDocNum").html(inval['ViewDocNum']);
                $("#ViewDocDate").html(inval['ViewDocDate']);
                $("#ViewCardCode").html(inval['ViewCardCode']);
                $("#ViewDocRemark").html(inval['ViewDocRemark']);
                $("#ViewDocType").html(inval['ViewDocType']);
                $("#ViewCreateName").html(inval['ViewCreateName']);

                $("#DocDetail").html(inval['DocDetail']);
                $("#ApproveListTable").html(inval['view_approvelist']);
            });
            $("#PreviewDoc").modal("show");
            $(".nav-tabs a[href='#DocDetail']").tab("show");

            $("input[id*='NoFine_']").on("click",function(){
                if($(this).is(":checked")) {
                    $("input[id*='SAFine_'], input[id*='CoFine_']").prop("checked",false);
                }
            });

            $("input[id*='SAFine_'], input[id*='CoFine_']").on("click",function(){
                if($(this).is(":checked")) {
                    $("input[id*='NoFine_']").prop("checked",false);
                }
            });
        }
    });
}

function AppDoc(ApproveID, DocEntry) {
    $(".overlay").show();
    var ApproveID = ApproveID;
    var DocEntry  = DocEntry;
    var AppState  = $("#AppState_"+ApproveID).val();
    var Remark    = $("#Remark_"+ApproveID).val();
    var NoFine    = $("#NoFine_"+ApproveID+":checked").val();
    var SAFine    = $("#SAFine_"+ApproveID+":checked").val();
    var CoFine    = $("#CoFine_"+ApproveID+":checked").val();
    if(AppState == "1" || Remark.length == 0) {
        $(".overlay").hide();
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        // console.log(NoFine, SAFine, CoFine, ApproveID);
        $.ajax({
            url: "menus/sale/ajax/ajaxapp_sa04.php?p=AppDoc",
            type: "POST",
            data: { a: AppState, r: Remark, aid: ApproveID, d: DocEntry, FineN: NoFine, FineS: SAFine, FineC: CoFine },
            success: function(result) {
                $(".overlay").hide();
                $(".modal").hide();
                $("#confirm_saved").modal('show');
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        })
    }
}


$(document).ready(function(){
    CallHead();
    GetApproveList();
});
</script> 
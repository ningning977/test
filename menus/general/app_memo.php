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
                        <thead>
                            <tr class="text-center">
                                <th width="3.5%">ลำดับ</th>
                                <th width="7%">วันที่เอกสาร</th>
                                <th width="10%">เลขที่เอกสาร</th>
                                <th>หัวข้อ</th>
                                <th width="12.5%">ฝ่าย</th>
                                <th width="7.5%">สถานะเอกสาร</th>
                            </tr>
                        </thead>
                        <tbody id="MemoListTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL MEMO PREVIEW -->
<div class="modal fade" id="ModalPreviewMM" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file fa-fw fa-lg"></i> รายละเอียดบันทึกภายใน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="h6">บันทึกภายในเลขที่: <span id="preview_DocNum"></span></h5>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-borderless table-sm" style="font-size: 12px;">
                        <tr>
                            <td class="font-weight" width="15%">วันที่เอกสาร</td>
                            <td id="preview_DocDate"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">เรื่อง</td>
                            <td id="preview_DocTitle"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">เรียน (To)</td>
                            <td id="preview_DocMention"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">สำเนาถึง (CC)</td>
                            <td id="preview_DocCopy"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">ผู้จัดทำ</td>
                            <td id="preview_Create"></td>
                        </tr>
                    </table>
                </div>
                <ul class="nav nav-tabs" id="mm-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#MMDetail" class="btn btn-tabs nav-link active" id="MMDetailTab" data-bs-toggle="tab" data-bs-target="#MMDetail" role="tab" data-tabs="0" aria-controls="MMDetailTab" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-file-alt fa-fw fa-1x"></i> รายละเอียด
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#MMAttach" class="btn btn-tabs nav-link" id="MMAttachTab" data-bs-toggle="tab" data-bs-target="#MMAttach" role="tab" data-tabs="1" aria-controls="MMAttachTab" style="font-size: 12px;">
                            <i class="fas fa-paperclip fa-fw fa-1x"></i> เอกสารแนบ
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#MMApprove" class="btn btn-tabs nav-link" id="MMApproveTab" data-bs-toggle="tab" data-bs-target="#MMApprove" role="tab" data-tabs="1" aria-controls="MMApproveTab" style="font-size: 12px;">
                            <i class="fas fa-tasks fa-fw fa-1x"></i> สถานะการอนุมัติ
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="MMDetail" role="tabpanel" aria-labelledy="MMDetailTab">
                        <div class="row mt-4">
                            <div class="col-12" id="preview_DocDetail" style="font-size: 13px;"></div>
                        </div>
                    </div>
                    <div class="tab-pane" id="MMAttach" role="tabpanel" aria-labelledby="MMAttachTab">
                        <div class="row mt-4">
                            <div class="col-12">
                                <table class="table table-bordered" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th>ชื่อเอกสารแนบ</th>
                                            <th width="15%">วันที่อัพโหลด</th>
                                            <th width="7.5%"><i class="fas fa-file-download fa-fw fa-lg"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview_MMAttach"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="MMApprove" role="tabpanel" aria-labelledby="MMApproveTab">
                        <div class="row mt-4">
                            <div class="col-12" id="preview_Approve">
                                <table class="table table-bordered" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th width="15%">ผู้อนุมัติ</th>
                                            <th width="10%">ผลการ<br/>พิจารณา</th>
                                            <th>หมายเหตุ</th>
                                            <th width="15%" >วันที่อนุมัติ</th>
                                            <th width="5%">บันทึก</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview_approvelist"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
        url: "menus/general/ajax/ajaxapp_memo.php?p=MemoList",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#MemoListTable").html(inval['MemoList']);
            });
            $(".overlay").hide();
        }
    });
}

function PreviewMM(docentry,int_status) {
    $("#ModalPreviewMM").modal("show");
    $(".nav-tabs a[href='#MMDetail']").tab("show");
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=PreviewMM&App=Y",
        type: "POST",
        data: { DocEntry: docentry, int_status: int_status },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#preview_DocNum").html(inval['view_DocNum']);
                $("#preview_DocDate").html(inval['view_DocDate']);
                $("#preview_DocTitle").html(inval['view_DocTitle']);
                $("#preview_DocMention").html(inval['view_MentionName']);
                $("#preview_DocCopy").html(inval['view_DocCopyTo']);
                $("#preview_DocDetail").html(inval['view_DocDetail']);

                $("#preview_MMAttach").html(inval['view_attachlist']);

                $("#preview_approvelist").html(inval['view_approvelist']);

                $("#preview_Create").html(inval['view_CreateName']);
            });
        }
    });
}

function AppMemo(ApproveID,DocEntry) {
    $(".overlay").show();
    var ApproveID = ApproveID;
    var DocEntry  = DocEntry;
    var AppState  = $("#AppState_"+ApproveID).val();
    var Remark    = $("#Remark_"+ApproveID).val();
    if(AppState == "1" || Remark.length == 0) {
        $(".overlay").hide();
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/general/ajax/ajaxapp_memo.php?p=AppMemo",
            type: "POST",
            data: { a: AppState, r: Remark, aid: ApproveID, d: DocEntry },
            success: function(result) {
                $(".overlay").hide();
                $("#confirm_saved").modal('show');
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        });
    }
    
}


$(document).ready(function(){
    CallHead();
    GetApproveList();
});
</script> 
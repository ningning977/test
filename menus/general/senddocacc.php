<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
    $today      = date("d");
?>
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
                <!-- CONTENT TAB -->
                <ul class="nav nav-tabs" id="main-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#LogList" class="btn-tabs nav-link active" id="LogList-tab" data-bs-toggle="tab" data-tabs="0" aria-controls="LogList" aria-selected="false">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการรับ/ส่งเอกสาร
                        </a>
                    </li>
                    <?php
                        if($today >= 26 || $today <= 2) {
                            $btn_class = " disabled";
                            // $btn_class = " ";
                            $dis_text  = " <small>(งดรับทุกวันที่ 26 - 2 ของเดือนถัดไป)</small>";
                        } else {
                            $btn_class = NULL;
                            $dis_text  = NULL;
                        }
                    ?>
                    <li class="nav-item" role="presentation">
                        <a href="#NewLoc" class="btn-tabs nav-link <?php echo $btn_class; ?>" id="NewLoc-tab" data-bs-toggle="tab" data-tabs="1" aria-controls="NewLoc" aria-selected="true">
                            <i class="fas fa-plus fa-fw fa-1x"></i> ส่งเอกสารใหม่ <?php echo $dis_text; ?>
                        </a>
                    </li>
                </ul>
                <!-- END CONTENT TAB -->
                <div class="tab-content">
                    <!-- TAB 1 -->
                    <div class="tab-pane fade show active" id="LogList" role="tabpanel" aria-labelledby="LogList-tab">
                        <div class="row mt-4">
                            <div class="col-lg-1 col-5">
                                <div class="form-group">
                                    <label for="filt_year">เลือกปี</label>
                                    <select class="form-select form-select-sm" name="filt_year" id="filt_year">
                                    <?php
                                        for($y = $this_year; $y >= $start_year; $y--) {
                                            if($y == $this_year) {
                                                $y_slct = " selected";
                                            } else {
                                                $y_slct = "";
                                            }
                                            echo "<option value='$y'$y_slct>$y</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-7">
                                <div class="form-group">
                                    <label for="filt_month">เลือกเดือน</label>
                                    <select class="form-select form-select-sm" name="filt_month" id="filt_month">
                                    <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            if($m == $this_month) {
                                                $m_slct = " selected";
                                            } else {
                                                $m_slct = "";
                                            }
                                            echo "<option value='$m'$m_slct>".FullMonth($m)."</option>";
                                        }
                                        $DeptCode = $_SESSION['DeptCode'];
                                        if(($DeptCode == "DP001" || $DeptCode == "DP002" || $DeptCode == "DP009")) {
                                            $opt_dis = NULL;
                                        } else {
                                            $opt_dis = " disabled";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-6">
                                <div class="form-group">
                                    <label for="filt_team">เลือกทีม</label>
                                    <select class="form-select form-select-sm" name="filt_team" id="filt_team">
                                        <option value="ALL"<?php echo $opt_dis; ?>>ทุกทีม</option>
                                    <?php
                                        $DeptSQL = "SELECT T0.DeptCode, T0.DeptName FROM departments T0 ORDER BY T0.DeptCode ASC";
                                        $DeptQRY = MySQLSelectX($DeptSQL);
                                        while($DeptRST = mysqli_fetch_array($DeptQRY)) {
                                            if(($DeptCode != "DP001" && $DeptCode != "DP002" && $DeptCode != "DP009") && ($DeptCode != $DeptRST['DeptCode'])) {
                                                $opt_dis = " disabled";
                                            } else {
                                                $opt_dis = NULL;
                                            }
                                            echo "<option value='".$DeptRST['DeptCode']."'$opt_dis>".$DeptRST['DeptName']."</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-6">
                                <div class="form-group">
                                    <label for="filt_type">เลือกประเภทเอกสาร</label>
                                    <select class="form-select form-select-sm" id="filt_type" name="filt_type">
                                        <option value="ALL" selected>เอกสารทั้งหมด</option>
                                        <option value="C">[C] เอกสารใบเคลมเปลี่ยน</option>
                                        <option value="L">[L] เอกสารคืนจากการยืม/คืนใบยืมที่ผ่าน QC แล้ว</option>
                                        <option value="D">[D] เอกสารลดหนี้ที่ผ่าน QC แล้ว</option>
                                        <option value="RE">[RE] เอกสารใบไม่รับคืนเคลมเปลี่ยน</option>
                                        <option value="SA-04">[SA-04] เอกสารทำลดหนี้ส่วนลดจ่าย</option>
                                        <option value="SA-08">[SA-08] เอกสารแก้ไขบิล/เปลี่ยนที่อยู่บิล/แก้ไขบิล</option>
                                        <option value="AC">[AC] เอกสารคืนลอย/คืนลอยเพื่อเปิดบิล</option>
                                        <option value="MM">[MM] เอกสาร MEMO</option>
                                        <option value="MP">[MP] เอกสาร MEMO จ่ายเงิน</option>
                                    </select>
                                </div>
                            </div>
                            <div class="offset-lg-2 col-lg-3 col-6">
                                <div class="form-group">
                                    <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i>  ค้นหา:</label>
                                    <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรุณากรอกเพื่อค้นหา..." />
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered" style="font-size: 12px;" id="AccTable">
                                <thead class="text-center">
                                    <tr>
                                        <th colspan="5">ข้อมูลผู้ส่ง</th>
                                        <th colspan="4">ข้อมูลผู้รับ</th>
                                        <th rowspan="2"  width="3.5%"><i class="fas fa-cog fa-fw fa-1x"></i></th>
                                    </tr>
                                    <tr>
                                        <th width="6.5%">เลขที่เอกสาร</th>
                                        <th width="6%">วันที่เอกสาร</th>
                                        <th>ชื่อลูกค้า / ชื่อเรื่อง</th>
                                        <th width="10%">ผู้ส่ง</th>
                                        <th width="6%">วันที่ส่งบัญชี</th>
                                        <th width="3.5%">รับแล้ว</th>
                                        <th width="3.5%">ตีกลับ</th>
                                        <th width="12.5%">ผู้ดำเนินการ (วันที่)</th>
                                        <th width="20%">หมายเหตุบัญชี</th>
                                    </tr>
                                </thead>
                                <tbody id="LogListTable"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- TAB 2 -->
                    <div class="tab-pane fade show" id="NewLoc" role="tabpanel" aria-labelledby="NewLoc-tab">
                        <form class="form" id="SendDocForm" enctype="multipart/form-data">
                            <div class="row mt-4">
                                <div class="col-lg-4 col-12">
                                    <div class="form-group mb-3">
                                        <label for="DocType">เลือกประเภทเอกสาร<span class="text-danger">*</span></label>
                                        <select class="form-select" id="DocType" name="DocType" required>
                                            <option value="NULL" selected disabled>กรุณาเลือกประเภทเอกสาร</option>
                                            <option value="C">[C] เอกสารใบเคลมเปลี่ยน</option>
                                            <option value="L">[L] เอกสารคืนจากการยืม/คืนใบยืมที่ผ่าน QC แล้ว</option>
                                            <option value="D">[D] เอกสารลดหนี้ที่ผ่าน QC แล้ว</option>
                                            <option value="RE">[RE] เอกสารใบไม่รับคืนเคลมเปลี่ยน</option>
                                            <!-- <option value="SA-04">[SA-04] เอกสารทำลดหนี้ส่วนลดจ่าย</option> -->
                                            <option value="SA-08">[SA-08] เอกสารแก้ไขบิล/เปลี่ยนที่อยู่บิล/แก้ไขบิล</option>
                                            <option value="AC">[AC] เอกสารคืนลอย/คืนลอยเพื่อเปิดบิล</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="form-group mb-3">
                                        <label for="DocNum">เลขที่เอกสาร<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="DocNum" id="DocNum" placeholder="กรุณาระบุเลขที่เอกสาร..." required />
                                        <small class="text-danger">บันทึกเลขที่เอกสารลงในแบบฟอร์มทุกครั้ง</small>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="form-group mb-3">
                                        <label for="DocDate">วันที่เอกสาร<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="DocDate" id="DocDate" value="<?php echo date("Y-m-d"); ?>" placeholder="dd/mm/yyyy" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-6">
                                    <div class="form-group mb-3">
                                        <label for="CardCode">คู่ค้า</label>
                                        <select class="form-control" name="CardCode" id="CardCode" data-live-search="true" aria-placeholder="กรุณาเลือกคู่ค้า">
                                            <option value="" selected disabled>กรุณาเลือกคู่ค้า</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <div class="form-group mb-3">
                                        <label for="RefDocNum">เอกสารอ้างอิง</label>
                                        <input type="text" class="form-control" name="RefDocNum" id="RefDocNum" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 text-right">
                                    <button class="btn btn-primary" type="button" onclick="AddDocAcc();"><i class="fas fa-save fa-fw fa-1x"></i> บันทึก</button>
                                </div>
                            </div>
                        </div>
                    </div>
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

<!-- MODAL CANCEL ALERT -->
<div class="modal fade" id="confirm_cancel" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-question-circle fa-fw fa-lg"></i> ยืนยันการยกเลิก</h5>
                <p id="confirm_body" class="my-4">คุณต้องการยกเลิกการส่งเอกสารนี้หรือไม่?</p>

                <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-dismiss" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-cancel-confirm" data-docentry="0" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HISTORY LOG -->
<div class="modal fade" id="ModalHistory" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history fa-fw fa-lg"></i> ประวัติการบันทึก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="10%">ครั้งที่</th>
                                    <th>หมายเหตุ</th>
                                    <th width="20%">ผู้บันทึก</th>
                                    <th width="25%">วันที่บันทึก</th>
                                </tr>
                            </thead>
                            <tbody id="HistoryList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

function GetLogList(filt_year,filt_month,filt_team,filt_type) {
    $(".overlay").show();
    $("#LogListTable").empty();
    $.ajax({
        url: "menus/general/ajax/ajaxsenddocacc.php?p=GetLogList",
        type: "POST",
        data: {
            y: filt_year,
            m: filt_month,
            t: filt_team,
            d: filt_type
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#LogListTable").html(inval['DocList']);
            });
            $(".overlay").hide();

            $("input[id*='Remark_']").on("focusout", function() {
                var DocEntry = $(this).attr("data-DocEntry");
                var Content  = $(this).val();

                if(Content.length > 0 || Content != " ") {
                    $(".overlay").show();
                    $.ajax({
                        url: "menus/general/ajax/ajaxsenddocacc.php?p=SaveRemark",
                        type: "POST",
                        data: {
                            DocEntry: DocEntry,
                            Content: Content
                        },
                        success: function(result) {
                            $(".overlay").hide();
                        }
                    });
                }
            });
        }
    });
}

function GetCardCode(){
    $(".overlay").show();
    $.ajax({
        url: "../json/OCRD.json",
        cache: false,
        success: function(result) {
            var filt_data = result.
                                filter(x => x.CardStatus == "A").
                                sort(function(key, inval) {
                                    return key.CardCode.localeCompare(inval.CardCode);
                                });
            var opt = "";

            $.each(filt_data, function(key, inval) {
                opt += "<option value='"+inval.CardCode+"'>"+inval.CardCode+" | "+inval.CardName+"</option>";
            });
            $("#CardCode").append(opt).selectpicker();
        }
    });
    $(".overlay").hide();
}

function AddDocAcc() {
    var ErrorPoint = 0;
    var ErrorID    = [];
    var SuccessID  = [];
    var CheckID    = ["DocDate","DocType","DocNum"];
    if(CheckID.length > 0) {
        for(let i = 0; i < CheckID.length; i++) {
            if($("#"+CheckID[i]).val() == null || $("#"+CheckID[i]).val() == "" || $("#"+CheckID[i]).val() == "NULL") {
                ErrorPoint = ErrorPoint+1;
                ErrorID.push(CheckID[i]);
            } else {
                SuccessID.push(CheckID[i]);
            }
        }
    }

    if(ErrorPoint > 0) {
        for(let i = 0; i < ErrorID.length; i++) { $("#"+ErrorID[i]).removeClass("is-valid is-invalid").addClass("is-invalid"); }
        for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        var AddDocForm = new FormData($("#SendDocForm")[0]);
        $.ajax({
            url: "menus/general/ajax/ajaxsenddocacc.php?p=AddDoc",
            type: "POST",
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: AddDocForm,
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval){
                    if(inval['AddStatus'] == "SUCCESS") {
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                    } else {
                        var alert_header = "<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!";
                        var alert_body;
                        switch(inval['AddStatus']) {
                            case "ERR::DUPLICATE":
                                alert_body = "ไม่สามารถเพิ่มเอกสารนี้ได้เนื่องจากเอกสารนี้ยังไม่ถูกบัญชีตีกลับ";
                            break;
                            case "ERR::CANNOT_INSERT":
                                alert_body = "ไม่สามารถเพิ่มเอกสารเข้าไปในฐานข้อมูลได้ กรุณาติดต่อฝ่าย IT";
                            break;
                        }
                        $("#alert_header").html(alert_header);
                        $("#alert_body").html(alert_body);
                        $("#alert_modal").modal('show');
                    }
                });
            }
        });
    }
}

function ReceiveDoc(DocEntry, Status) {
    var DocEntry = DocEntry;
    var Status   = Status;
    $(".overlay").show();
    $.ajax({
        url: "menus/general/ajax/ajaxsenddocacc.php?p=ReceiveDoc",
        type: "POST",
        data: {
            DocEntry: DocEntry,
            Status: Status
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                if(inval['AddStatus'] != "SUCCESS") {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("ไม่สามารถอัพเดตข้อมูลได้ กรุณาติดต่อฝ่าย IT");
                    $("#alert_modal").modal('show');
                } else {
                    var filt_year  = $("#filt_year").val();
                    var filt_month = $("#filt_month").val();
                    var filt_team  = $("#filt_team").val();
                    var filt_type  = $("#filt_type").val();
                    GetLogList(filt_year,filt_month,filt_team,filt_type)
                }
            });
            $(".overlay").hide();
        }
    });

}

function HistoryDoc(DocEntry) {
    var DocEntry = DocEntry;
    $(".modal").modal("hide");

    $.ajax({
        url: 'menus/general/ajax/ajaxsenddocacc.php?p=HistoryDoc',
        type: 'POST',
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#HistoryList").html(inval['output']);
            });
            $("#ModalHistory").modal("show");
        }
    })
}

function CancelDoc(DocEntry) {
    var DocEntry = DocEntry;
    $(".modal").modal("hide");
    $("#confirm_cancel").modal("show");

    $("#btn-cancel-confirm").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            url: 'menus/general/ajax/ajaxsenddocacc.php?p=CancelDoc',
            type: 'POST',
            data: { DocEntry: DocEntry },
            success: function(result) {
                $("#confirm_saved").modal("show");
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        });
    });
}

/* MEMO SYSTEM */
function PreviewMM(docentry,int_status) {
    $("#ModalPreviewMM").modal("show");
    $(".nav-tabs a[href='#MMDetail']").tab("show");
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=PreviewMM",
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
            });
        }
    });
}


$(document).ready(function(){
    CallHead();
    GetCardCode();

    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team  = $("#filt_team").val();
    var filt_type  = $("#filt_type").val();
    GetLogList(filt_year,filt_month,filt_team,filt_type);
});

$("#DocType").on("change", function() {
    var DocType = $(this).val();
    console.log(DocType);
    var DocPrefix;
    switch(DocType) {
        case "SA-04": DocPrefix = "SA04-"; break;
        case "SA-08": DocPrefix = "SA08-";
            $.ajax({
                url: 'menus/general/ajax/ajaxsenddocacc.php?p=GetSA08DocNum',
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key,inval) {
                        $("#DocNum").val(inval['DocNum']).focus();
                    })
                }
            });
        break;
        case "AC":
            $.ajax({
                url: 'menus/general/ajax/ajaxsenddocacc.php?p=GetACDocNum',
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key,inval) {
                        $("#DocNum").val(inval['DocNum']).focus();
                    })
                }
            });
        break;
        default:      DocPrefix = DocType; break;
    }
    $("#DocNum").val(DocPrefix).focus();
});

$("#filt_year, #filt_month, #filt_team, #filt_type").on("change", function(){
    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team  = $("#filt_team").val();
    var filt_type  = $("#filt_type").val();
    GetLogList(filt_year,filt_month,filt_team,filt_type);
});

$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#LogListTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});
</script> 
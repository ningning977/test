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
                                if(($DeptCode == "DP001" || $DeptCode == "DP002" || $DeptCode == "DP011")) {
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
                    <div class="offset-lg-4 col-lg-3 col-6">
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
                                <th>ชื่อลูกค้า</th>
                                <th width="10%">ฝ่ายที่ส่ง</th>
                                <th width="6%">วันที่ส่งคลังฯ</th>
                                <th width="3.5%">รับแล้ว</th>
                                <th width="3.5%">ตีกลับ</th>
                                <th width="12.5%">ผู้ดำเนินการ (วันที่)</th>
                                <th width="20%">หมายเหตุคลังฯ</th>
                            </tr>
                        </thead>
                        <tbody id="LogListTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

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

<!-- MODAL DOC PREVIEW -->
<div class="modal fade" id="ModalPreviewDoc" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file fa-fw fa-lg"></i> รายละเอียดใบฝากงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="h6">ใบฝากงานเลขที่: <span id="view_DocNum"></span></h5>
                        <input type="hidden" class="form-control" name="view_WOEntry" id="view_WOEntry" />
                        <input type="hidden" class="form-control" name="view_TypeOrder" id="view_TypeOrder" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless table-sm" style="font-size:12px;">
                            <tr>
                                <th width="15%">วันที่ฝากงาน</th>
                                <td width="45%" id="view_DocDate"></td>
                                <th width="15%">วันที่นัดหมาย</th>
                                <td width="25%" class="text-danger" id="view_DocDueDate"></td>
                            </tr>
                            <tr>
                                <th>ชื่อผู้ฝากงาน</th>
                                <td id="view_CreateName"></td>
                                <th>ฝ่าย</th>
                                <td id="view_DeptName"></td>
                            </tr>
                            <tr>
                                <th>ประเภทการฝากงาน</th>
                                <td colspan="3" id="view_DocType"></td>
                            </tr>
                            <tr>
                                <th>ชื่อลูกค้า</th>
                                <td colspan="3" id="view_CusCode"></td>
                            </tr>
                            <tr>
                                <th>บุคคลหรือหน่วยงานที่ติดต่อ</th>
                                <td class="text-danger" id="view_ContactName"></td>
                                <th>เบอร์โทร.ติดต่อ</th>
                                <td class="text-danger" id="view_ContactTel"></td>
                            </tr>
                            <tr>
                                <th>ที่อยู่สำหรับติดต่อ</th>
                                <td colspan="3" class="text-danger" id="view_CusAddress"></td>
                            </tr>
                            <tr>
                                <th>รายละเอียดการฝากงาน</th>
                                <td colspan="3" id="view_DocDetail"></td>
                            </tr>
                        </table>
                        <hr/>
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">ลำดับ</th>
                                    <th width="7.5%">รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th width="7.5%">คลัง</th>
                                    <th width="7.5%">จำนวน</th>
                                    <th width="7.5%">หน่วย</th>
                                    <th width="25%">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody id="view_ItemList"></tbody>
                        </table>
                        <hr/>
                        <table class="table table-borderless table-sm" style="font-size:12px;">
                            <tr>
                                <th width="15%">ชื่อผู้ให้บริการขนส่ง</th>
                                <td width="45%" id="view_ShippingName"></td>
                                <th width="15%">เบอร์โทร.ติดต่อ</th>
                                <td width="25%" id="view_ShippingTel"></td>
                            </tr>
                            <tr>
                                <th>ที่อยู่สำหรับติดต่อ</th>
                                <td id="view_ShippingAddress">&nbsp;</td>
                                <th>การจ่ายค่าขนส่ง</th>
                                <td class="text-danger" id="view_ShippingCost"></td>
                            </tr>
                            <tr>
                                <th>จำนวนลังสินค้าที่ต้องรับ/ส่ง</th>
                                <td colspan="3" id="view_TotalBox"></td>
                            </tr>
                            <tr>
                                <th>เอกสารแนบ</th>
                                <td colspan="3" id="view_AttachList"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="PrintWO();"><i class="fas fa-print fa-fw fa-1x"></i> พิมพ์ใบฝากงาน</button>
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
                <p id="confirm_Wai" class="my-4">บันทึกข้อมูลสำเร็จ</p>
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

function GetLogList(filt_year,filt_month,filt_team) {
    $(".overlay").show();
    $("#LogListTable").empty();
    $.ajax({
        url: "menus/general/ajax/ajaxsenddocwho.php?p=GetLogList",
        type: "POST",
        data: {
            y: filt_year,
            m: filt_month,
            t: filt_team
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
                        url: "menus/general/ajax/ajaxsenddocwho.php?p=SaveRemark",
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

function ReceiveDoc(DocEntry, Status) {
    var DocEntry = DocEntry;
    var Status   = Status;
    $(".overlay").show();
    $.ajax({
        url: "menus/general/ajax/ajaxsenddocwho.php?p=ReceiveDoc",
        type: "POST",
        data: {
            DocEntry: DocEntry,
            Status: Status
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                switch(inval['AddStatus']) {
                    case "SUCCESS":
                        var WOEntry = inval['WOEntry'];
                        var DocType = inval['DocType'];
                        if(DocType == "S" || DocType == "B") {
                            $.ajax({ url: "../core/OWAS.php?x="+WOEntry });
                        }
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            var filt_year  = $("#filt_year").val();
                            var filt_month = $("#filt_month").val();
                            var filt_team  = $("#filt_team").val();
                            GetLogList(filt_year,filt_month,filt_team);
                        });
                        break;
                    case "REJECTED":
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            var filt_year  = $("#filt_year").val();
                            var filt_month = $("#filt_month").val();
                            var filt_team  = $("#filt_team").val();
                            GetLogList(filt_year,filt_month,filt_team);
                        });
                        break;
                    default:
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่สามารถอัพเดตข้อมูลได้ กรุณาติดต่อฝ่าย IT");
                        $("#alert_modal").modal('show');
                        break;
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
        url: 'menus/general/ajax/ajaxsenddocwho.php?p=HistoryDoc',
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

function PreviewDoc(DocEntry) {
    var DocEntry = DocEntry;
    $.ajax({
        url: "menus/general/ajax/ajaxwhseorder.php?p=PreviewDoc",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#view_WOEntry").val(inval['WOEntry']);
                $("#view_TypeOrder").val(inval['TypeOrder']);

                console.log(inval['WOEntry'],inval['TypeOrder']);

                $("#view_DocNum").html(inval['DocNum']);
                $("#view_DocDate").html(inval['DocDate']);
                $("#view_DocDueDate").html(inval['DocDueDate']);
                $("#view_CreateName").html(inval['CreateName']);
                $("#view_DeptName").html(inval['DeptName']);
                $("#view_DocType").html(inval['DocType']);
                $("#view_CusCode").html(inval['CusCode']);
                $("#view_ContactName").html(inval['ContactName']);
                $("#view_ContactTel").html(inval['ContactTel']);
                $("#view_CusAddress").html(inval['CusAddress']);
                $("#view_DocDetail").html(inval['DocDetail']);

                $("#view_ItemList").html(inval['ItemList']);

                $("#view_ShippingName").html(inval['LogiName']);
                $("#view_ShippingTel").html(inval['LogiPhone']);
                $("#view_ShippingAddress").html(inval['LogiAddress']);
                $("#view_ShippingCost").html(inval['LogiCost']);
                $("#view_TotalBox").html(inval['TotalBox']);

                $("#view_AttachList").html(inval['AttList']);
            });
            $("#ModalPreviewDoc").modal("show");
        }
    });
}

function PrintWO() {
    var WOEntry = $("#view_WOEntry").val();
    var TypeOrder = $("#view_TypeOrder").val();
    window.open('menus/general/print/printwo.php?DocEntry='+WOEntry+'&Type='+TypeOrder,'_blank');
}

$(document).ready(function(){
    CallHead();
    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team  = $("#filt_team").val();
    GetLogList(filt_year,filt_month,filt_team);
});

$("#filt_year, #filt_month, #filt_team").on("change", function(){
    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team  = $("#filt_team").val();
    GetLogList(filt_year,filt_month,filt_team);
});

$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#LogListTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});
</script> 
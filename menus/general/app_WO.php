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
                <div class="table-responsive">
                    <!-- TABLE -->
                    <table class="table table-bordered table-hover table-sm" style="font-size: 12px;">
                        <thead class="text-center">
                            <tr>
                                <th width="7.5%">วันที่ฝากงาน</th>
                                <th width="10%">เลขที่ฝากงาน</th>
                                <th width="7.5%">วันที่นัดหมาย</th>
                                <th width="7.5%">รายละเอียด</th>
                                <th>ลูกค้า / ผู้ติดต่อ</th>
                                <th width="12.5%">ฝ่าย</th>
                                <th width="7.5%">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="WhOrderList"></tbody>
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
                <div id="AppName"></div>
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

function GetAppList() {
    $(".overlay").show();
    $.ajax({
        url: "menus/general/ajax/ajaxapp_WO.php?p=AppList",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $(".overlay").hide();
                $("#WhOrderList").html(inval['OrderList']);
            });
        }
    });
}

function PreviewDoc(DocEntry,intstatus) {
    var DocEntry = DocEntry;
    $(".modal").hide();
    $.ajax({
        url: "menus/general/ajax/ajaxwhseorder.php?p=PreviewDoc&App=Y",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
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

                $("#AppName").html(inval['AppName']);
            });
            $("#ModalPreviewDoc").modal("show");
        }
    });
}

function AppDoc(DocEntry) {
    $(".overlay").show();
    $(".modal").hide();
    var DocEntry = DocEntry;
    var AppState = $("#AppState_").val();
    var Remark   = $("#Remark_").val();

    if(AppState == "1" || Remark.length == 0) {
        $(".overlay").hide();
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/general/ajax/ajaxapp_WO.php?p=AppDoc",
            type: "POST",
            data: { a: AppState, r: Remark, d: DocEntry },
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
    GetAppList();
});
</script> 
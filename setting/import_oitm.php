<style type="text/css">
</style>

<?php
echo "<input type='hidden' id='HeadeMenuLink' value = '" . $_GET['p'] . "'>";
?>
<div class="page-heading">
    <h3><i class="fas fa-cube fa-fw fa-1x"></i> นำเข้า Item</h3>
</div>
<hr>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br /><br />
        กำลังโหลด...
    </div>
</div>


<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-cube fa-fw fa-1x"></i> นำเข้า Item</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-12">
                        <div class="form-group mb-3">
                            <label for="PickItemCode"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</label>
                            <input type="text" class="form-control form-control-sm" id="PickItemCode" placeholder="กรอกรหัสสินค้า เช่น 02-065-010 เป็นต้น">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group mb-3">
                            <label for="Btn-Import">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="ImportItem();"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group mb-3">
                            <label for="Btn-Import">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm w-100" onclick="SyncItem();"><i class="fas fa-sync fa-fw fa-1x"></i> Sync ข้อมูลสินค้าทั้งหมด</button>
                        </div>
                    </div>
                </div>
                <?php if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003") { ?>
                <hr/>
                <form id="FormEditItem">
                    <div class="row mt-4">
                        <div class="col-lg-2 col-md-12 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_ItemCode">รหัสสินค้า</label>
                                <input type="text" class="form-control form-control-sm text-center" name="txt_ItemCode" id="txt_ItemCode" readonly />
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_ItemName">ชื่อสินค้า 1</label>
                                <input type="text" class="form-control form-control-sm" name="txt_ItemName" id="txt_ItemName" />
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_ItemName2">ชื่อสินค้า 2</label>
                                <input type="text" class="form-control form-control-sm" name="txt_ItemName2" id="txt_ItemName2" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_BarCode">Bar Code</label>
                                <input type="text" class="form-control form-control-sm text-center" name="txt_BarCode" id="txt_BarCode" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_BarCode2">Bar Code 2</label>
                                <input type="text" class="form-control form-control-sm text-center" name="txt_BarCode2" id="txt_BarCode2" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_BarCode3">Bar Code 3</label>
                                <input type="text" class="form-control form-control-sm text-center" name="txt_BarCode3" id="txt_BarCode3" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_MgrUnit">หน่วยขาย</label>
                                <input type="text" class="form-control form-control-sm" name="txt_MgrUnit" id="txt_MgrUnit" />
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_DftWhsCode">คลังเริ่มต้น</label>
                                <input type="text" class="form-control form-control-sm text-center" name="txt_DftWhsCode" id="txt_DftWhsCode" />
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_ProductStatus">สถานะการขายสินค้า</label>
                                <select class="form-select form-select-sm" name="txt_ProductStatus" id="txt_ProductStatus">
                                    <option value='K' selected>กรุณาเลือก</option>
                                    <option value='D'>สถานะ D</option>
                                    <option value='D21'>สถานะ D21</option>
                                    <option value='D22'>สถานะ D22</option>
                                    <option value='D23'>สถานะ D23</option>
                                    <option value='R'>สถานะ R</option>
                                    <option value='A'>สถานะ A</option>
                                    <option value='W'>สถานะ W</option>
                                    <option value='N'>สถานะ N</option>
                                    <option value='M'>สถานะ M</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_ItemStatus">สถานะสินค้า</label>
                                <select class="form-select form-select-sm" name="txt_ItemStatus" id="txt_ItemStatus">
                                    <option value='A' selected>Active</option>
                                    <option value='I'>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-right">
                            <button type="button" class="btn btn-primary btn-sm" onclick="SaveItem();"><i class="far fa-save fa-fw fa-1x"></i> บันทึก</button>
                        </div>
                    </div>
                </form>
                <?php } ?>
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
                <p id="confirm" class="my-4">บันทึกข้อมูลสำเร็จ</p>
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function ImportItem() {
        $(".overlay").show();
        var ItemCode = $("#PickItemCode").val();
        if(ItemCode == '') {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณากรอกรหัสสินค้า หรือบาร์โค้ดก่อน");
            $("#alert_modal").modal('show');
        } else {
            $.ajax({
                url: "setting/ajax/ajaximport_oitm.php?p=ImportItem",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                success: function(result) {
                    $(".overlay").hide();
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key, inval) {
                        var Status = inval['Status'];
                        var modal_body = "";
                        if(Status == "SUCCESS") {
                            $("#txt_ItemCode").val(inval['txt_ItemCode']);
                            $("#txt_ItemName").val(inval['txt_ItemName']);
                            $("#txt_ItemName2").val(inval['txt_ItemName2']);
                            $("#txt_BarCode").val(inval['txt_BarCode']);
                            $("#txt_BarCode2").val(inval['txt_BarCode2']);
                            $("#txt_BarCode3").val(inval['txt_BarCode3']);
                            $("#txt_MgrUnit").val(inval['txt_MgrUnit']);
                            $("#txt_DftWhsCode").val(inval['txt_DftWhsCode']);
                            $("#txt_ProductStatus").val(inval['txt_ProductStatus']).change();
                            $("#txt_ItemStatus").val(inval['txt_ItemStatus']).change();
                        } else {
                            switch(Status) {
                                case "ERR::NORESULT":   modal_body = "ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากไม่พบข้อมูลในระบบ SAP"; break;
                            }
                            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                            $("#alert_body").html(modal_body);
                            $("#alert_modal").modal('show');
                        }
                    });
                }
            })
        }
    }

    $('#PickItemCode').keypress(function (e) {
        if (e.which == 13) {
            ImportCard();
        }
    });

    function SaveItem() {
        $(".overlay").show();
        var FormEditItem = new FormData($("#FormEditItem")[0]);
        $.ajax({
            url: "setting/ajax/ajaximport_oitm.php?p=SaveItem",
            type: "POST",
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: FormEditItem,
            success: function(result) {
                $(".overlay").hide();
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    if(inval['Status'] == 'SUCCESS') {
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                    }
                });
            }
        })
    }

    function SyncItem() {
        $(".overlay").show();
        $.ajax({
            url: "setting/ajax/ajaximport_oitm.php?p=SyncItem",
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $("#alert_header").html("<i class=\"fas fa-check-circle fa-fw fa-lg text-danger\"></i> ดำเนินการเสร็จสิ้น!");
                    $("#alert_body").html("Sync ข้อมูลสินค้าทั้งหมดแล้ว ("+inval['ChkRow']+" รายการ)");
                    $("#alert_modal").modal('show');
                });
                $(".overlay").hide();
            }
        })
    }

    
</script>

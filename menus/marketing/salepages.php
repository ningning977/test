<style type="text/css">
    #ShowItem div.card {
        border: 1px solid #6c757d;
    }

    .card-img-top {
        width: 100%;
        height: 20vw;
        object-fit: contain;
    }
    @media screen and (max-width: 426px) {
        .card-img-top {
            height: 60vw;
        }
    }
    @media screen and (min-width: 426px) and (max-width: 1023px) {
        .card-img-top {
            height: 40vw;
        }
    }

    .buttons-excel {
        position: absolute;
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

<!-- Alert Remark -->
<div class="modal fade" id="ModalAlertRemark" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h1 class="modal-title text-center" id="HeaderModalAlertRemark"></h1>
                <p id="DetailModalAlertRemark" class="my-3 text-primary"></p>
                <button type="button" class="btn btn-sm btn-secondary w-25 mt-4" data-bs-dismiss="modal">ตกลง</button>
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

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <!-- Tabs Menu -->
                <div class="row">
                    <div class="col-lg">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary active" id="T1-tab" data-bs-toggle="tab" data-bs-target="#T1" type="button" role="tab" aria-controls="T1" aria-selected="false" data-ItemType="CAT">แคตตาล็อกสินค้า</button>
                                <button class="nav-link text-primary" id="T2-tab" data-bs-toggle="tab" data-bs-target="#T1" type="button" role="tab" aria-controls="T1" aria-selected="false" data-ItemType="SPP">แคตตาล็อกอะไหล่</button>
                                <button class="nav-link text-primary" id="T3-tab" data-bs-toggle="tab" data-bs-target="#T1" type="button" role="tab" aria-controls="T1" aria-selected="false" data-ItemType="PRC">ใบราคาสินค้า</button>
                                <button class="nav-link text-primary" id="T4-tab" data-bs-toggle="tab" data-bs-target="#T1" type="button" role="tab" aria-controls="T1" aria-selected="false" data-ItemType="PRO">ใบโปรโมชัน</button>
                                <button class="nav-link text-primary" id="T5-tab" data-bs-toggle="tab" data-bs-target="#T1" type="button" role="tab" aria-controls="T1" aria-selected="false" data-ItemType="VDO">วิดีโอ</button>
                                <button class="nav-link text-primary" id="SKU-tab" data-bs-toggle="tab" data-bs-target="#SKU" type="button" role="tab" aria-controls="SKU" aria-selected="false" data-ItemType="SKU">SKU BOOK</button>
                                <button class="nav-link text-primary" id="T7-tab" data-bs-toggle="tab" data-bs-target="#T1" type="button" role="tab" aria-controls="T1" aria-selected="false" data-ItemType="COP">COMPANY PROFILE</button>
                            <?php $DeptChck = array("DP001","DP002","DP003"); if(in_array($_SESSION['DeptCode'], $DeptChck, TRUE)) { ?>
                                <button class="nav-link text-primary" id="N1-tab" data-bs-toggle="tab" data-bs-target="#N1" type="button" role="tab" aria-controls="N1" aria-selected="false" data-ItemType=""><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มรายการใหม่</button>
                            <?php } ?>
                            </div>
                        </nav>
                    </div>
                </div>

                <!-- Tabs Content -->
                <div class="row pt-2">
                    <div class="col-lg">
                        <div class="tab-content mt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="T1" role="tabpanel" aria-labelledby="T1-tab">
                                <div class="row" id="ShowItem"></div>
                            </div>

                            <div class="tab-pane fade" id="N1" role="tabpanel" aria-labelledby="N1-tab">
                                <form class="form" id="SalePageForm" enctype="multipart/form-data">
                                    <div class="row mt-4">
                                        <div class="col-lg-12">
                                            <h4 class="h4">เพิ่ม / แก้ไขรายการ</h4>
                                            <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="ItemType">ประเภทเอกสาร<span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm" name="ItemType" id="ItemType">
                                                    <option selected disabled>กรุณาเลือก</option>
                                                    <option value="CAT">แคตตาล็อกสินค้า (Catalogues)</option>
                                                    <option value="SPP">แคตตาล็อกสินค้าอะไหล่ (Spare Parts)</option>
                                                    <option value="PRC">ใบราคาสินค้า (Price Lists)</option>
                                                    <option value="PRO">ใบโปรโมชัน (Promotions)</option>
                                                    <option value="VDO">วิดีโอ (Videos)</option>
                                                    <option value="SKU">SKU Book</option>
                                                    <option value="COP">Company Profile</option>
                                                </select>
                                                <input type="hidden" id="DocEntry" name="DocEntry" value="0" />
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="form-group mb-3">
                                                <label for="ItemName">ชื่อเอกสาร<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" name="ItemName" id="ItemName">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="ImgThumb">หน้าปก</label>
                                                <input type="file" class="form-control form-control-sm" name="ImgThumb" id="ImgThumb" accept="image/*" />
                                                <small class="text-muted">รองรับไฟล์รูปภาพเท่านั้น <span class="text-danger">(ขนาดไม่เกิน 5 MB)</span></small>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="ItemFile">ไฟล์เอกสาร</label>
                                                <input type="file" class="form-control form-control-sm" name="ItemFile" id="ItemFile" accept="application/pdf" />
                                                <small class="text-muted">รองรับไฟล์ *.pdf เท่านั้น <span class="text-danger">(ขนาดไม่เกิน 90 MB)</span></small>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group mb-3">
                                                <label for="ItemLink">Youtube URL</label>
                                                <input type="text" class="form-control form-control-sm" name="ItemLink" id="ItemLink" placeholder="https://www.youtube.com/" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-1">
                                            <div class="form-group mb-3">
                                                <label for="VisOrder">ลำดับที่</label>
                                                <input type="number" class="form-control form-control-sm text-right" name="VisOrder" id="VisOrder" value="0" />
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="VisManager">แสดงผลสำหรับผู้จัดการ</label>
                                                <select class="form-select form-select-sm" id="VisManager" name="VisManager">
                                                    <option value="Y" selected>แสดง</option>
                                                    <option value="N">ไม่แสดง</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="VisSaleEmp">แสดงผลสำหรับพนักงานขาย</label>
                                                <select class="form-select form-select-sm" id="VisSaleEmp" name="VisSaleEmp">
                                                    <option value="Y" selected>แสดง</option>
                                                    <option value="N">ไม่แสดง</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group mb-3">
                                                <label for="VisDealer">แสดงผลสำหรับร้านค้า</label>
                                                <select class="form-select form-select-sm" id="VisDealer" name="VisDealer">
                                                    <option value="Y" selected>แสดง</option>
                                                    <option value="N">ไม่แสดง</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-7 text-right">
                                            <button type="button" class="btn btn-primary" onclick="SaveItem();"><i class="fas fa-save fa-fw fa-1x"></i> บันทึก</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="SKU" role="tabpanel" aria-labelledby="SKU-tab">
                                <div class="table-responsive">
                                    <table class='table table-sm table-bordered table-hover' style='font-size: 13px;' id='TableSKU'>
                                        <thead>
                                            <tr>
                                                <th class='text-center border'>รายการที่</th>
                                                <th class='text-center border'>รหัสสินค้า</th>
                                                <th class='text-center border'>บาร์โค้ด</th>
                                                <th class='text-center border'>ชื่อสินค้า</th>
                                                <th class='text-center border'>วันที่อัพเดตล่าสุด</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>

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
}

function GetItem(ItemType) {
    $(".overlay").show();
    $.ajax({
        url: "menus/marketing/ajax/ajaxsalepages.php?p=GetItem",
        type: "POST",
        data: { ItemType: ItemType },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $(".overlay").hide();
                var Rows = parseFloat(inval['Rows']);
                var Card = "";
                var DeptCode = '<?php echo $_SESSION['DeptCode']; ?>';
                switch(DeptCode) {
                    case "DP001":
                    case "DP002":
                    case "DP003":
                        var DisEdit = true;
                    break;
                    default:
                        var DisEdit = false;
                    break;
                }
                if(Rows == 0) {
                    $("#ShowItem").html("<div class='col'><p class='text-center'>ไม่มีข้อมูล :(</p></div>");
                } else {
                    for(i = 0; i < Rows; i++) {
                        var btnEdit;
                        if(DisEdit == true) {
                            btnEdit = "<a class='btn btn-outline-secondary'"+DisEdit+" onclick='EditItem("+inval['BD_'+i]['DocEntry']+");'><i class='fas fa-cog fa-fw fa-1x'></i></a> ";
                            btnEdit += "<a class='btn btn-outline-danger'"+DisEdit+" onclick='DeleteItem("+inval['BD_'+i]['DocEntry']+");'><i class='fas fa-trash fa-fw fa-1x'></i></a>";
                        } else {
                            btnEdit = "";
                        }
                        Card +=
                            "<div class='col-12 col-md-6 col-lg-4 col-xl-3'>"+
                                "<div class='card'>"+
                                    "<div class='text-center' style='background-color: #000; border-radius: 10px 10px 0 0;'>"+
                                        "<a href='../FileAttach/SALEPAGES/"+inval['BD_'+i]['BookSRC']+"' target='_blank'><img src='../FileAttach/SALEPAGES/thumb/"+inval['BD_'+i]['ThmbSRC']+"' class='card-img-top'></a>"+
                                    "</div>"+
                                    "<div class='card-body'>"+
                                        "<h5 class='card-title'>"+inval['BD_'+i]['ItemTitle']+"</h5>"+
                                        "<p class='text-muted'>วันที่อัพเดต: "+inval['BD_'+i]['CreateDate']+"</p>"+
                                        "<div>"+
                                            "<a class='btn btn-outline-success w-25' href='../FileAttach/SALEPAGES/"+inval['BD_'+i]['BookSRC']+"' target='_blank'><i class='fas fa-eye fa-fw fa-1x'></i></a> "+
                                            "<a class='btn btn-outline-info' href='../FileAttach/SALEPAGES/"+inval['BD_'+i]['BookSRC']+"' download><i class='fas fa-download fa-fw fa-1x'></i></a> "+
                                            btnEdit+
                                        "</div>"+
                                    "</div>"+
                                "</div>"+
                            "</div>";
                    }
                    $("#ShowItem").html(Card);            
                }
            });
        }
    });
}

function GetNextOrder(ItemType) {
    $.ajax({
        url: "menus/marketing/ajax/ajaxsalepages.php?p=GetNextOrder",
        type: "POST",
        data: { ItemType: ItemType },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#VisOrder").val(inval['NxtOrder']);
            });
        }
    });
}

function EditItem(DocEntry) {
    $(".nav-tabs button[data-bs-target='#N1']").tab("show");
    $.ajax({
        url: "menus/marketing/ajax/ajaxsalepages.php?p=EditItem",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#ItemType").val(inval['ItemType']).change();
                $("#DocEntry").val(inval['DocEntry']);
                $("#ItemName").val(inval['ItemTitle']);
                $("#VisManager").val(inval['VisManager']).change();
                $("#VisSaleEmp").val(inval['VisSaleEmp']).change();
                $("#VisDealer").val(inval['VisDealer']).change();

                setTimeout(() => { $("#VisOrder").val(inval['VisOrder']);}, 500);
            })
        }
    });
}

function SaveItem() {
    $(".overlay").show();
    var ItemType = $("#ItemType").val();
    var ItemName = $("#ItemName").val();
    if(ItemType == "" || ItemName == "") {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        // $(".overlay").show();
        var SalePageForm = new FormData($("#SalePageForm")[0]);
        $.ajax({
            url: "menus/marketing/ajax/ajaxsalepages.php?p=SaveItem",
            type: "POST",
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: SalePageForm,
            success: function(result) {
                $(".overlay").hide();
                $("#confirm_saved").modal('show');
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        })
    }
}

/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
$(document).ready(function(){
    CallHead();
    GetItem("CAT");

    $("button.nav-link[id^='T']").on("click", function() {
        var ItemType = $(this).attr("data-ItemType");
        GetItem(ItemType);
    });

    $("button.nav-link#N1-tab").on("click", function() {
        $("#DocEntry").val(0);
        $("#SalePageForm")[0].reset();
    });

    $("#ItemType").on("change", function() {
        var ItemType = $(this).val();
        GetNextOrder(ItemType);
    });

    $("#ImgThumb").on("change", function() {
        var FileSize = parseFloat(((this.files[0].size)/1024)/1024); /* Unit in MB */
        if(FileSize > 5.00) {
            $(this).val(null);
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 5 MB");
            $("#alert_modal").modal('show');
        }
    });

    $("#ItemFile").on("change", function() {
        var FileSize = parseFloat(((this.files[0].size)/1024)/1024); /* Unit in MB */
        if(FileSize > 90.00) {
            $(this).val(null);
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 90 MB");
            $("#alert_modal").modal('show');
        }
    });

    $("#HeaderModalAlertRemark").html("<i class='fas fa-exclamation-circle fa-fw fa-lg'></i> คำเตือน");
    $("#DetailModalAlertRemark").html("ข้อมูลเอกสารของบริษัทถือเป็นความลับสุดยอด<br/>ห้ามส่งต่อหรือทำการ Copy ให้ผู้ที่ไม่เกี่ยวข้องเด็ดขาด");
    $("#ModalAlertRemark").modal("show");
});

$("#SKU-tab").on("click", function(){
    $(".overlay").show();
    $("#TableSKU").dataTable().fnClearTable();
    $("#TableSKU").dataTable().fnDraw();
    $("#TableSKU").dataTable().fnDestroy();
    $("#TableSKU").DataTable({
        "ajax": {
            url: "menus/marketing/ajax/ajaxsalepages.php?p=GetSKU",
            type: "GET",
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "No", class: "dt-body-center border-start border-bottom" },
            { "data": "ItemCode", class: "dt-body-center border-start border-bottom" },
            { "data": "BarCode", class: "dt-body-center border-start border-bottom" },
            { "data": "ItemName", class: "border-start border-bottom" },
            { "data": "UpdateDate", class: "dt-body-center border-start border-bottom" },
        ],
        "columnDefs": [
            { "width": "7%", "targets": 0 },
            { "width": "12%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "50%", "targets": 3 },
            { "width": "16%", "targets": 4 },
        ],
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "language":{ 
            "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        },
        "dom": 'Bfrtip',
        "buttons": [{ "extend": 'excelHtml5',"footer": true, },]
    });
    $(".overlay").hide();
})

function DeleteItem(DocEntry) {
    $("#confirm_modal").modal("show");
    $(document).off("click","#btn-confirm").on("click","#btn-confirm", function() {
        $.ajax({
            url: "menus/marketing/ajax/ajaxsalepages.php?p=DeleteItem",
            type: "POST",
            data: { DocEntry: DocEntry },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    const ItemType = $(".nav-tabs button.active").attr("data-ItemType");
                    GetItem(ItemType);
                    $("#confirm_modal").modal("hide");
                })
            }
        });
    });
}

function PushSKU(ItemCode) {
    console.log(ItemCode);
}
</script> 
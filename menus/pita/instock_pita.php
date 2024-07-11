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
                    <div class="col-auto">
                        <div class="form-group ps-3" style='width: 320px;'>
                            <label for="">เงื่อนไข</label>
                            <div class="form-control form-control-sm d-flex align-items-center justify-content-around">
                                <input class="form-check-input m-0" type="checkbox" id="filt_getzero">
                                <span class="ms-1">สินค้าคงคลัง = 0</span>
                                <input class="form-check-input m-0 ms-2" type="checkbox" id="filt_aging">
                                <span class="ms-1">ดึงอายุสินค้า (Aging)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group ps-3">
                            <label for=""></label>
                            <button class='btn btn-sm btn-primary w-100' onclick="Search()"><i class="fas fa-search"></i> ค้นหา</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group ps-3">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick="Export()"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>
                <hr class='m-2' style='color: #BDBDBD;'>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered rounded rounded-3 overflow-hidden' id='TableDATA'>
                                <thead style='font-size: 13px;' id='Thead'>
                                    <tr class='text-center'>
                                        <th>กำลังโหลดข้อมูล <i class="fas fa-spinner fa-pulse"></i></th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='Tbody'></tbody>
                                <tfoot style='font-size: 12px;' id='Tfoot'></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalDataItem" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " id='ModalSize'>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search-plus" style='font-size: 15px;'></i> ข้อมูลสินค้าคงคลัง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name='ItemCode' id='ItemCode' >
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden'>
                                <tbody style='font-size: 13px;' id='Tbody1'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr class='m-2' style='color: rgba(244, 67, 54, 0.25);'>
                <div class="row pt-3">
                    <div class="col-lg">
                        <span class='fw-bolder pb-1'>จำนวนสินค้าคงคลังในระบบ SAP</span>
                        <div class="table-responsive pt-1" id='Table2'>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
    </div>
</div>

<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>

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
    Search();
});
try{ document.createEvent("TouchEvent"); var isMobile = true; }
catch(e){ var isMobile = false; }

function ShowDataTable() {
    setTimeout(function(){
        switch(isMobile) {
            case true: var PageLength = 5; break;
            case false: var PageLength = 15; break;
            default: var PageLength = 10; break;
        }
        $('#TableDATA').DataTable({
            destroy: true,
            "bAutoWidth": false,
            "ordering": false,
            "pageLength": PageLength,
            dom: 'frtip'
        });
    }, 1000);
}

function Search() {
    $(".overlay").show();
    $.ajax({
        url: "menus/pita/ajax/ajaxinstock_pita.php?a=Search",
        type: "POST",
        data: { aging : $("#filt_aging").is(":checked"), },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $('#TableDATA').DataTable().destroy();
                $('#Thead, #Tbody, #Tfoot').empty();
                $("#Thead").html(inval['Thead']);
                $("#Tbody").html(inval['Tbody']);
                $("#Tfoot").html(inval['Tfoot']);
                ShowDataTable();
            })
            $(".overlay").hide();

            $(".Data-Item").on("click", function() {
                var DataItem = $(this).attr('data-item');
                // console.log(DataItem);
                DataDetail(DataItem);
            })
        } 
    })
}

function DataDetail(DataItem) {
    switch(isMobile) {
        case true: var ModalSize = "modal-full"; break;
        case false: var ModalSize = "modal-xl"; break;
        default: var ModalSize = "modal-xl"; break;
    }
    $.ajax({
        url: "menus/pita/ajax/ajaxinstock_pita.php?a=DataDetail",
        type: "POST",
        data: { ItemCode : DataItem, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#Tbody1").html(inval['output1']);
                $("#Table2").html(inval['output2']);

                $("#ItemCode").val(inval['ItemCode']);
                // console.log(inval['ItemCode']);

                $("#ModalSize").addClass(ModalSize);
                $("#ModalDataItem").modal("show");
            })
        }
    })
}

function Export() {
    $(".overlay").show();
    $.ajax({
        url: "menus/pita/ajax/ajaxinstock_pita.php?a=Export",
        type: "POST",
        data: { aging : $("#filt_aging").is(":checked"),},
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                if(inval['ExportStatus'] == 'SUCCESS') {
                    window.open("../../../FileExport/InStockPTA/"+inval['FileName'],'_blank');
                }
            })
            $(".overlay").hide();
        } 
    })
}
</script> 
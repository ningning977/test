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
                        <div class="form-group">
                            <label for="Year">เลือกปี</label>
                            <select class='form-select form-select-sm' name="Year" id="Year" onchange='CallData();'>
                                <?php 
                                for($y = date("Y"); $y >= 2014; $y--) {
                                    if($y == date("Y")) {
                                        echo "<option value='".$y."' seleted>".$y."</option>";
                                    }else{
                                        echo "<option value='".$y."'>".$y."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="Month">เลือกเดือน</label>
                            <select class='form-select form-select-sm' name="Month" id="Month" onchange='CallData();'>
                                <option value='ALL' selected>ทุกเดือน</option>
                                <?php for($m = 1; $m <= 12; $m++) {
                                    echo "<option value='".$m."'>".FullMonth($m)."</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="CardCode">เลือกซัพพลายเออร์</label>
                            <select class="form-control form-control-sm" id="CardCode" name="CardCode" data-live-search="true" onchange='CallData();'>
                                <!-- <option value='' selected disabled>เลือกซัพพลายเออร์</option> -->
                                <option value='ALL' selected>เลือกทั้งหมด</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="DocStatus">เลือกสถานะเอกสาร</label>
                            <select class='form-select form-select-sm' name="DocStatus" id="DocStatus" onchange='CallData();'>
                                <option value='ALL' selected>เลือกทั้งหมด</option>
                                <option value='O'>สถานะ Open</option>
                                <option value='C'>สถานะ Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="GroupCode">เลือกกลุ่มร้านค้า</label>
                            <select class='form-select form-select-sm' name="GroupCode" id="GroupCode" onchange='CallData();'>
                                <option value='ALL' selected>เลือกทั้งหมด</option>
                                <option value='IN'>ในประเทศ</option>
                                <option value='OUT'>ต่างประเทศ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Excel();'><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' style='font-size: 12.5px;' id='Table1'>
                                <thead>
                                    <tr>
                                        <th width='7%' class='text-center border-top'>เลขที่ใบสั่งซื้อ</th>
                                        <th width='7%' class='text-center border-top'>รหัสร้านค้า</th>
                                        <th width='20%' class='text-center border-top'>ชื่อร้านค้า</th>
                                        <th width='8%' class='text-center border-top'>เลขที่ PR</th>
                                        <th width='5%' class='text-center border-top'>ประเภทเอกสาร</th>
                                        <th width='8%' class='text-center border-top'>วันที่สั่งซื้อ</th>
                                        <th width='8%' class='text-center border-top'>วันที่กำหนดส่ง</th>
                                        <th width='8%' class='text-center border-top'>ยอดสั่งซื้อ<br/>(THB)</th>
                                        <th width='37%' class='text-center border-top'>หมายเหตุ</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fab fa-readme' style='font-size: 20px;'></i>&nbsp;&nbsp;&nbsp;รายละเอียดการสั่งซื้อ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class='table table-sm table-borderless' style='font-size: 13px;' id='Table2'></table>
                </div>
                <div class="table-responsive">
                    <table class='table table-sm table-bordered table-hover' style='font-size: 13px;' id='Table3'>
                        <thead>
                            <tr class='text-center'>
                                <th>ลำดับที่</th>
                                <th>รหัสสินค้า</th>
                                <th>ชื่อสินค้า</th>
                                <th>จำนวน</th>
                                <th>หน่วย</th>
                                <th>ราคา/หน่วย</th>
                                <th>ส่วนลด</th>
                                <th>จำนวนเงิน</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
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
    GetCardCode();
    CallData();
});

function GetCardCode() {
    $.ajax({
        url: "menus/purchase/ajax/ajaxp_order.php?a=GetCardCode",
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#CardCode").append(inval['Data']).selectpicker();
            });
        }
    })
}

function CallData() {
    let Year      = $("#Year").val();
    let Month     = $("#Month").val();
    let CardCode  = $("#CardCode").val();
    let DocStatus = $("#DocStatus").val();
    let GroupCode = $("#GroupCode").val();
    // console.log(Year, Month, CardCode, DocStatus, GroupCode);
    if(CardCode != null) {
        $("#Table1").dataTable().fnClearTable();
        $("#Table1").dataTable().fnDraw();
        $("#Table1").dataTable().fnDestroy();
        $("#Table1").DataTable({
            "ajax": {
                url: "menus/purchase/ajax/ajaxp_order.php?a=CallData",
                type: "POST",
                data: { Year : Year, Month : Month, CardCode : CardCode, DocStatus : DocStatus, GroupCode : GroupCode, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "DocNum",        class: "dt-body-center border-top" },
                { "data": "CardCode",      class: "dt-body-center border-top" },
                { "data": "CardName",      class: "border-top" },
                { "data": "U_PurchaseFor", class: "dt-body-center border-top" },
                { "data": "DocType",       class: "dt-body-center border-top" },
                { "data": "DocDate",       class: "dt-body-center border-top" },
                { "data": "DocDueDate",    class: "dt-body-center border-top" },
                { "data": "DocTotal",    class: "dt-body-right border-top" },
                { "data": "Comments",      class: "border-top" }
            ],
            "columnDefs": [
                { "width": "7%", "targets": 0 },
                { "width": "7%", "targets": 1 },
                { "width": "20%", "targets": 2 },
                { "width": "8%", "targets": 3 },
                { "width": "5%", "targets": 4 },
                { "width": "8%", "targets": 5 },
                { "width": "8%", "targets": 6 },
                { "width": "8%", "targets": 7 },
                { "width": "37%", "targets": 8 },
            ],
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 15,
            "searching": true,
            "bInfo": false,
            "ordering": false,
            "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
            },
        });
    }else{
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกร้านค้าก่อน");
        $("#alert_modal").modal('show');
    }
}

function GetDetail(DocEntry, Year) {
    // console.log(DocEntry, Year);
    $.ajax({
        url: "menus/purchase/ajax/ajaxp_order.php?a=GetDetail",
        type: "POST",
        data: { DocEntry : DocEntry, Year : Year, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#Table2").html(inval['Head']);
                $("#Table3 tbody").html(inval['Body']);
                $("#Table3 tfoot").html(inval['Tfoot']);
                $("#ModalDetail").modal("show");
            });
        }
    })
}

function Excel() {
    let Year      = $("#Year").val();
    let Month     = $("#Month").val();
    let CardCode  = $("#CardCode").val();
    let DocStatus = $("#DocStatus").val();
    let GroupCode = $("#GroupCode").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/purchase/ajax/ajaxp_order.php?a=Excel",
        type: "POST",
        data: { Year : Year, Month : Month, CardCode : CardCode, DocStatus : DocStatus, GroupCode : GroupCode, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/POrder/"+inval['FileName'],'_blank');
            });
        }
    })
}

</script> 
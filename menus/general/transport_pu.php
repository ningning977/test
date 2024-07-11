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
                    <div class="col-12 d-flex justify-content-between">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button onclick='TabData1();' class="nav-link text-primary active" id="Tab1-tab" data-bs-toggle="tab" data-bs-target="#Tab1" type="button" role="tab" aria-controls="Tab1" aria-selected="false"><i class="fas fa-list"></i> รายการสินค้าเข้าเรียบร้อย</button>
                                <button onclick='TabData2();' class="nav-link text-primary" id="Tab2-tab" data-bs-toggle="tab" data-bs-target="#Tab2" type="button" role="tab" aria-controls="Tab2" aria-selected="false"><i class="fas fa-truck fa-fw"></i> สินค้าใกล้หมดในประเทศ</button>
                                <button onclick='TabData3();' class="nav-link text-primary" id="Tab3-tab" data-bs-toggle="tab" data-bs-target="#Tab3" type="button" role="tab" aria-controls="Tab3" aria-selected="false"><i class="fas fa-ship fa-fw"></i> สินค้าใกล้หมดต่างประเทศ</button>
                                <button onclick='TabData4();' class="nav-link text-primary" id="Tab4-tab" data-bs-toggle="tab" data-bs-target="#Tab4" type="button" role="tab" aria-controls="Tab4" aria-selected="false"><i class="fas fa-list-ol"></i> สินค้ารอเข้า</button>
                            </div>
                        </nav>
                        <?php if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP004") { ?>
                            <button class='btn btn-success btn-sm' onclick='ShowFormImport();'><i class='fas fa-file-excel fa-fw fa-1x'></i> นำเข้า Excel</button>
                        <?php } ?>
                    </div>
                </div>
                <form class="form" id="FormImport" enctype="multipart/form-data"> 
                    <input type="file" name='FileImport' id='FileImport' accept=".xlsx" style='display: none;'>
                </form>
            
                <div class="row pt-3">
                    <div class="col-lg">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="Tab1" role="tabpanel" aria-labelledby="Tab1-tab">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for="">เลือกปี</label>
                                            <select class='form-select form-select-sm' name="txtYear" id="txtYear" onchange='TabData1();'>
                                                <?php
                                                for($y = date("Y"); $y >= 2023; $y--) {
                                                    echo ($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for="">เลือกเดือน</label>
                                            <select class='form-select form-select-sm' name="txtMonth" id="txtMonth" onchange='TabData1();'>
                                                <?php
                                                for($m = 1; $m <= 12; $m++) {
                                                    echo ($m == date("m")) ? "<option value='$m' selected>".FullMonth($m)."</option>" : "<option value='$m'>".FullMonth($m)."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive pt-2">
                                    <table class='table table-sm table-hover table-bordered' id='Table1' style='font-size: 12px;'>
                                        <thead style='background-color: #9A1118; color: #fff;'>
                                            <tr>
                                                <th rowspan='2' class='border-top text-center'>No.</th>
                                                <th rowspan='2' class='border-top text-center'>วันที่เอกสาร</th>
                                                <th rowspan='2' class='border-top text-center'>รหัสสินค้า</th>
                                                <th rowspan='2' class='border-top text-center'>บาร์โค้ด</th>
                                                <th rowspan='2' class='border-top text-center'>ชื่อสินค้า</th>
                                                <th rowspan='2' class='border-top text-center'>สถานะ</th>
                                                <th rowspan='2' class='border-top text-center'>จำนวน</th>
                                                <th rowspan='2' class='border-top text-center'>หน่วย</th>
                                                <th rowspan='2' class='border-top text-center'>คลังสินค้า</th>
                                                <th rowspan='2' class='border-top text-center'>เอกสารอ้างอิง</th>
                                                <th rowspan='2' class='border-top text-center'>วันที่เข้าระบบ</th>
                                                <th colspan='6' class='border-top text-center'>โควตา</th>
                                                <th rowspan='2' class='border-top text-center'>จัดสรร</th>
                                            </tr>
                                            <tr>
                                                <th class='border-top text-center'>MT1</th>
                                                <th class='border-top text-center'>MT2</th>
                                                <th class='border-top text-center'>TT</th>
                                                <th class='border-top text-center'>หน้าร้าน</th>
                                                <th class='border-top text-center'>ออนไลน์</th>
                                                <th class='border-top text-center'>การตลาด</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <?php for($i = 2; $i <= 3; $i++) { ?>
                            <div class="tab-pane fade" id="Tab<?php echo $i; ?>" role="tabpanel" aria-labelledby="Tab<?php echo $i; ?>-tab">
                                <span id='DateCreate<?php echo $i; ?>'></span>
                                <div class="table-responsive">
                                    <table class='table table-sm table-hover table-bordered' id='Table<?php echo $i; ?>' style='font-size: 12px;'>
                                        <thead style='background-color: #9A1118; color: #fff;'>
                                            <tr>
                                                <th rowspan='2' width='2%' class='border-top text-center'>No.</th>
                                                <th rowspan='2' width='6%' class='border-top text-center'>รหัสสินค้า</th>
                                                <th rowspan='2' width='15%' class='border-top text-center'>ชื่อสินค้า</th>
                                                <th rowspan='2' width='5%' class='border-top text-center'>สถานะสินค้า</th>
                                                <th rowspan='2' width='7%' class='border-top text-center'>วันที่สินค้า<br>คาดว่าจะหมด</th>
                                                <th rowspan='2' width='6%' class='border-top text-center'>จำนวน<br>สินค้า<br>คงเหลือ<br>PCs.</th>
                                                <th rowspan='2' width='5%' class='border-top text-center'>วันที่เปิด PO</th>
                                                <th colspan='5' class='border-top text-center'>กำหนดและระยะเวลาการส่งสินค้า</th>
                                                <th rowspan='2' width='5%' class='border-top text-center'>จำนวนที่เข้า<br>ในล็อตถัดไป<br>(pcs)</th>
                                                <th rowspan='2' width='5%' class='border-top text-center'>จำนวน<br>สั่งซื้อ<br>ทั้งหมด<br>(pcs)</th>
                                                <th rowspan='2' width='15%' class='border-top text-center'>หมายเหตุ</th>
                                            </tr>
                                            <tr>
                                                <th width='5.5%' class='text-center'>กำหนดส่ง</th>
                                                <th width='5.5%' class='text-center'>เลื่อนส่ง<br>ครั้งที่ 1</th>
                                                <th width='5.5%' class='text-center'>เลื่อนส่ง<br>ครั้งที่ 2</th>
                                                <th width='5.5%' class='text-center'>ประมาณการ<br>สินค้าถึง KBI</th>
                                                <th width='5.5%' class='text-center'>วันที่สินค้า<br>พร้อมขาย</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="tab-pane fade" id="Tab4" role="tabpanel" aria-labelledby="Tab4-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' id='Table4' style='font-size: 12px;'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th rowspan='2' width='5%' class='text-center border border-top align-bottom'>No.</th>
                                                        <th rowspan='2' width='6%' class='text-center border border-top align-bottom'>รหัสสินค้า</th>
                                                        <th rowspan='2' width='20.5%' class='text-center border border-top align-bottom'>ชื่อสินค้า</th>
                                                        <th rowspan='2' width='5%' class='text-center border border-top align-bottom'>สถานะสินค้า</th>
                                                        <th rowspan='2' width='7%' class='text-center border border-top align-bottom'>วันที่เปิด PO</th>
                                                        <th rowspan='2' width='7%' class='text-center border border-top align-bottom'>ประมาณการ<br>สินค้าคลัง KBI</th>
                                                        <th rowspan='2' width='7%' class='text-center border border-top align-bottom'>อ้างอิง PO</th>
                                                        <th rowspan='2' width='5%' class='text-center border border-top align-bottom'>จำนวน</th>
                                                        <th rowspan='2' width='5%' class='text-center border border-top align-bottom'>หน่วย</th>
                                                        <th rowspan='2' width='5%' class='text-center border border-top align-bottom'>คลังสินค้า</th>
                                                        <th colspan='5' class='text-center border border-top align-bottom'>โควต้าทีม</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='5.5%' class='text-center border border-top'>MT1</th>
                                                        <th width='5.5%' class='text-center border border-top'>MT2</th>
                                                        <th width='5.5%' class='text-center border border-top'>TT2</th>
                                                        <th width='5.5%' class='text-center border border-top'>OUL</th>
                                                        <th width='5.5%' class='text-center border border-top'>ONL</th>
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
        </div>
    </div>
</section>

<div class="modal fade" id="ModalViewData" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fab fa-readme' style='font-size: 20px;'></i>&nbsp;&nbsp;&nbsp;รายละเอียดสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='TableDetail'>
                                <thead>
                                    <tr class='text-center'>
                                        <th>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>บาร์โค้ด 1</th>
                                        <th>บาร์โค้ด 2</th>
                                        <th>บาร์โค้ด 3</th>
                                        <th>หน่วย</th>
                                        <th>คลัง</th>
                                        <th>EUROX FORCE</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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

<div class="modal fade" id="ModalReload" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="ReloadHeader"></h5>
                <p id="ReloadBody" class="my-4"></p>
                <button type="button" class="btn btn-primary btn-sm btn-reload" >ตกลง</button>
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

function ShowFormImport() {
    $("#FileImport").click();
}

$('#FileImport').on('change', function() {
    if($("#FileImport").val() != "") {
    var FormImport = new FormData($("#FormImport")[0]);
    $.ajax({
        url: "menus/general/ajax/ajaxtransport_pu.php?a=FileImport",
        type: 'POST',
        dataType: 'text',
        cache: false,
        processData: false,
        contentType: false,
        data: FormImport,
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#ReloadHeader").html("<i class='fas fa-check-circle' style='font-size: 65px;'></i>");
                $("#ReloadBody").html("นำเข้า Excel สำเร็จ");
                $("#ModalReload").modal("show");
            });
        } 
    })
    }
})

$(".btn-reload").on("click", function(){
    $("#ModalReload").modal("hide");
    $("#Tab1-tab").click();
})

$(document).ready(function(){
    TabData1();
});

function TabData1() {
    $("#Table1").dataTable().fnClearTable();
    $("#Table1").dataTable().fnDraw();
    $("#Table1").dataTable().fnDestroy();
    $("#Table1").DataTable({
        "ajax": {
            url: "menus/general/ajax/ajaxtransport_pu.php?a=CallData1",
            type: "POST",
            data: { txtYear: $("#txtYear").val(), txtMonth: $("#txtMonth").val(), },
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "No", class: "text-center border-start border-bottom" },
            { "data": "DocDate", class: "text-center border-start border-bottom" },
            { "data": "ItemCode", class: "text-center border-start border-bottom" },
            { "data": "CodeBars", class: "text-center border-start border-bottom" },
            { "data": "Dscription", class: "border-start border-bottom border-end" },
            { "data": "ProductStatus", class: "dt-body-center border-start border-bottom border-end" },
            { "data": "Quantity", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "UnitMsr", class: "text-center border-start border-bottom border-end" },
            { "data": "WhsCode", class: "text-center border-start border-bottom border-end" },
            { "data": "DocNum", class: "text-center border-start border-bottom border-end" },
            { "data": "CreateDate", class: "text-center border-start border-bottom border-end" },
            { "data": "Q_MT1", class: "text-center border-start border-bottom border-end" },
            { "data": "Q_MT2", class: "text-center border-start border-bottom border-end" },
            { "data": "Q_TT", class: "text-center border-start border-bottom border-end" },
            { "data": "Q_OUL", class: "text-center border-start border-bottom border-end" },
            { "data": "Q_ONL", class: "text-center border-start border-bottom border-end" },
            { "data": "Q_MKT", class: "text-center border-start border-bottom border-end" },
            { "data": "Quota", class: "text-center border-start border-bottom border-end" },
        ],
        "columnDefs": [
            { "width": "4%", "targets": 0 },
            { "width": "6%", "targets": 1 },
            { "width": "6%", "targets": 2 },
            { "width": "8%", "targets": 3 },
            { "width": "20%", "targets": 4 },
            { "width": "4%", "targets": 5 },
            { "width": "4%", "targets": 6 },
            { "width": "4%", "targets": 7 },
            { "width": "4%", "targets": 8 },
            { "width": "8%", "targets": 9 },
            { "width": "6%", "targets": 10 },
            { "width": "4%", "targets": 11 },
            { "width": "4%", "targets": 12 },
            { "width": "4%", "targets": 13 },
            { "width": "4%", "targets": 14 },
            { "width": "4%", "targets": 15 },
            { "width": "4%", "targets": 16 },
            { "width": "2%", "targets": 17 },
        ],
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "ordering": false,
        "dom": 'Bfrtip',
        "buttons": [{ "extend": 'excelHtml5',"footer": true, },]
    });
}

function TabData2() {
    $("#Table2").dataTable().fnClearTable();
    $("#Table2").dataTable().fnDraw();
    $("#Table2").dataTable().fnDestroy();
    $("#Table2").DataTable({
        "ajax": {
            url: "menus/general/ajax/ajaxtransport_pu.php?a=CallData2",
            type: "POST",
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "lnNum", class: "text-center border-start border-bottom" },
            { "data": "ItemCode", class: "text-center border-start border-bottom" },
            { "data": "ItemName", class: "border-start border-bottom" },
            { "data": "StatusItem", class: "text-center border-start border-bottom" },
            { "data": "EndDate", class: "text-center border-start border-bottom border-end" },
            { "data": "StockQty", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "PODate", class: "text-center border-start border-bottom border-end" },
            { "data": "DL1", class: "text-center border-start border-bottom border-end" },
            { "data": "DL2", class: "text-center border-start border-bottom border-end" },
            { "data": "DL3", class: "text-center border-start border-bottom border-end" },
            { "data": "KBIRecive", class: "text-center border-start border-bottom border-end" },
            { "data": "SaleDate", class: "text-center border-start border-bottom border-end" },
            { "data": "InQty", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "TotalQty", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "Remark", class: "border-start border-bottom border-end" },
        ],
        "columnDefs": [
            { "width": "2%", "targets": 0 },
            { "width": "6%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "5%", "targets": 3 },
            { "width": "7%", "targets": 4 },
            { "width": "6%", "targets": 5 },
            { "width": "5%", "targets": 6 },
            { "width": "5.5%", "targets": 7 },
            { "width": "5.5%", "targets": 8 },
            { "width": "5.5%", "targets": 9 },
            { "width": "5.5%", "targets": 10 },
            { "width": "5.5%", "targets": 11 },
            { "width": "5%", "targets": 12 },
            { "width": "5%", "targets": 13 },
            { "width": "15%", "targets": 14 },
        ],
        "createdRow": function (row, data, dataIndex, cells) {
            if(data.lnNum == ""){
                $('td:eq(0)', row).css('display', 'none');
                $('td:eq(1)', row).css('display', 'none');
                $('td:eq(2)', row).attr('colspan', 15);
                $('td:eq(3)', row).css('display', 'none');
                $('td:eq(4)', row).css('display', 'none');
                $('td:eq(5)', row).css('display', 'none');
                $('td:eq(6)', row).css('display', 'none');
                $('td:eq(7)', row).css('display', 'none');
                $('td:eq(8)', row).css('display', 'none');
                $('td:eq(9)', row).css('display', 'none');
                $('td:eq(10)', row).css('display', 'none');
                $('td:eq(11)', row).css('display', 'none');
                $('td:eq(12)', row).css('display', 'none');
                $('td:eq(13)', row).css('display', 'none');
                $('td:eq(14)', row).css('display', 'none');
            }
            if(data.StatusItem == 'M') {
                // $(row).addClass("table-warning");
                $(row).css({"background": "#b3ccff"});
            }else{
                if(data.Remark == 'สินค้าเข้าคลังเรียบร้อยแล้ว') {
                    $(row).css('background', '#B1F5BC');
                }
            }
            if(data.ItemCode == 'H') {
                $(row).addClass("table-danger fw-bolder");
            }
            if(data.lnNum == "NoData"){
                $('td:eq(0)', row).css('display', 'none');
                $('td:eq(1)', row).css('display', 'none');
                $('td:eq(2)', row).attr('colspan', 15);
                $('td:eq(3)', row).css('display', 'none');
                $('td:eq(4)', row).css('display', 'none');
                $('td:eq(5)', row).css('display', 'none');
                $('td:eq(6)', row).css('display', 'none');
                $('td:eq(7)', row).css('display', 'none');
                $('td:eq(8)', row).css('display', 'none');
                $('td:eq(9)', row).css('display', 'none');
                $('td:eq(10)', row).css('display', 'none');
                $('td:eq(11)', row).css('display', 'none');
                $('td:eq(12)', row).css('display', 'none');
                $('td:eq(13)', row).css('display', 'none');
                $('td:eq(14)', row).css('display', 'none');
                $(row).addClass("text-center");
            }
        },
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 12,
        "ordering": false,
        "bInfo": false,
        "dom": 'Bfrtip',
        "buttons": [{ "extend": 'excelHtml5',"footer": true, },]
    });
    $.ajax({
        url: "menus/general/ajax/ajaxtransport_pu.php?a=GetDateUpdate2",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#DateCreate2").html(inval['DateCreate']);
            });
        }
    })
}

function TabData3() {
    $("#Table3").dataTable().fnClearTable();
    $("#Table3").dataTable().fnDraw();
    $("#Table3").dataTable().fnDestroy();
    $("#Table3").DataTable({
        "ajax": {
            url: "menus/general/ajax/ajaxtransport_pu.php?a=CallData3",
            type: "POST",
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "lnNum", class: "text-center border-start border-bottom" },
            { "data": "ItemCode", class: "text-center border-start border-bottom" },
            { "data": "ItemName", class: "border-start border-bottom" },
            { "data": "StatusItem", class: "text-center border-start border-bottom" },
            { "data": "EndDate", class: "text-center border-start border-bottom border-end" },
            { "data": "StockQty", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "PODate", class: "text-center border-start border-bottom border-end" },
            { "data": "DL1", class: "text-center border-start border-bottom border-end" },
            { "data": "DL2", class: "text-center border-start border-bottom border-end" },
            { "data": "DL3", class: "text-center border-start border-bottom border-end" },
            { "data": "KBIRecive", class: "text-center border-start border-bottom border-end" },
            { "data": "SaleDate", class: "text-center border-start border-bottom border-end" },
            { "data": "InQty", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "TotalQty", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "Remark", class: "border-start border-bottom border-end" },
        ],
        "columnDefs": [
            { "width": "2%", "targets": 0 },
            { "width": "6%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "5%", "targets": 3 },
            { "width": "7%", "targets": 4 },
            { "width": "6%", "targets": 5 },
            { "width": "5%", "targets": 6 },
            { "width": "5.5%", "targets": 7 },
            { "width": "5.5%", "targets": 8 },
            { "width": "5.5%", "targets": 9 },
            { "width": "5.5%", "targets": 10 },
            { "width": "5.5%", "targets": 11 },
            { "width": "5%", "targets": 12 },
            { "width": "5%", "targets": 13 },
            { "width": "15%", "targets": 14 },
        ],
        "createdRow": function (row, data, dataIndex, cells) {
            if(data.lnNum == ""){
                $('td:eq(0)', row).css('display', 'none');
                $('td:eq(1)', row).css('display', 'none');
                $('td:eq(2)', row).attr('colspan', 15);
                $('td:eq(3)', row).css('display', 'none');
                $('td:eq(4)', row).css('display', 'none');
                $('td:eq(5)', row).css('display', 'none');
                $('td:eq(6)', row).css('display', 'none');
                $('td:eq(7)', row).css('display', 'none');
                $('td:eq(8)', row).css('display', 'none');
                $('td:eq(9)', row).css('display', 'none');
                $('td:eq(10)', row).css('display', 'none');
                $('td:eq(11)', row).css('display', 'none');
                $('td:eq(12)', row).css('display', 'none');
                $('td:eq(13)', row).css('display', 'none');
                $('td:eq(14)', row).css('display', 'none');
            }
            if(data.StatusItem == 'M') {
                $(row).css({"background": "#b3ccff"});
            }else{
                if(data.Remark == 'สินค้าเข้าคลังเรียบร้อยแล้ว') {
                    $(row).css('background', '#B1F5BC');
                }
            }
            if(data.ItemCode == 'H') {
                $(row).addClass("table-danger fw-bolder");
            }
            if(data.lnNum == "NoData"){
                $('td:eq(0)', row).css('display', 'none');
                $('td:eq(1)', row).css('display', 'none');
                $('td:eq(2)', row).attr('colspan', 15);
                $('td:eq(3)', row).css('display', 'none');
                $('td:eq(4)', row).css('display', 'none');
                $('td:eq(5)', row).css('display', 'none');
                $('td:eq(6)', row).css('display', 'none');
                $('td:eq(7)', row).css('display', 'none');
                $('td:eq(8)', row).css('display', 'none');
                $('td:eq(9)', row).css('display', 'none');
                $('td:eq(10)', row).css('display', 'none');
                $('td:eq(11)', row).css('display', 'none');
                $('td:eq(12)', row).css('display', 'none');
                $('td:eq(13)', row).css('display', 'none');
                $('td:eq(14)', row).css('display', 'none');
                $(row).addClass("text-center");
            }
        },
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 12,
        "ordering": false,
        "bInfo": false,
        "dom": 'Bfrtip',
        "buttons": [{ "extend": 'excelHtml5',"footer": true, },]
    });
    $.ajax({
        url: "menus/general/ajax/ajaxtransport_pu.php?a=GetDateUpdate3",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#DateCreate3").html(inval['DateCreate']);
            });
        }
    })
}

function ViewData(ItemCode) {
    $.ajax({
        url: "menus/general/ajax/ajaxtransport_pu.php?a=ViewData",
        type: "POST",
        data: { ItemCode : ItemCode, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                var Tbody = "<tr>"+
                                "<td class='text-center'>"+inval['ItemCode']+"</td>"+
                                "<td>"+inval['ItemName']+"</td>"+
                                "<td class='text-center'>"+inval['CodeBars1']+"</td>"+
                                "<td class='text-center'>"+inval['CodeBars2']+"</td>"+
                                "<td class='text-center'>"+inval['CodeBars3']+"</td>"+
                                "<td class='text-center'>"+inval['SalUnitMsr']+"</td>"+
                                "<td>"+inval['DfltWH']+"</td>"+
                                "<td class='text-center'>"+inval['AddItem']+"</td>"+
                            "</tr>";
                $("#TableDetail tbody").html(Tbody);
                $("#ModalViewData").modal("show");
            });
        }
    })
}

function AddItem(ItemCode) {
    $.ajax({
        url: "setting/ajax/ajaximport_oitm.php?p=ImportItem",
        type: "POST",
        data: { ItemCode : ItemCode, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 65px;'></i>");
                $("#alert_body").html("เพิ่มข้อมูลสำเร็จ");
                $("#alert_modal").modal("show");
                $("#ModalViewData").modal("hide");
            });
        }
    })
}

function TabData4() {
    $("#Table4").dataTable().fnClearTable();
    $("#Table4").dataTable().fnDraw();
    $("#Table4").dataTable().fnDestroy();
    $("#Table4").DataTable({
        "ajax": {
            url: "menus/general/ajax/ajaxtransport_pu.php?a=CallData4",
            type: "GET",
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
          { "data": "No", class: "dt-body-center border-start border-bottom align-baseline" },
          { "data": "ItemCode", class: "dt-body-center border-start border-bottom align-baseline" },
          { "data": "Dscription", class: "border-start border-bottom align-baseline" },
          { "data": "U_ProductStatus", class: "dt-body-center border-start border-bottom align-baseline" },
          { "data": "DocDate", class: "dt-body-center border-start border-bottom align-baseline" },
          { "data": "DocDueDate", class: "dt-body-center border-start border-bottom align-baseline" },
          { "data": "DocNum", class: "dt-body-center border-start border-bottom align-baseline" },
          { "data": "Quantity", class: "dt-body-right border-start border-bottom align-baseline" },
          { "data": "unitMsr", class: "border-start border-bottom align-baseline" },
          { "data": "WhsCode", class: "dt-body-center border-start border-bottom align-baseline" },
          { "data": "U_MT1", class: "dt-body-right border-start border-bottom align-baseline" },
          { "data": "U_MT2", class: "dt-body-right border-start border-bottom align-baseline" },
          { "data": "U_TT2", class: "dt-body-right border-start border-bottom align-baseline" },
          { "data": "U_OUL", class: "dt-body-right border-start border-bottom align-baseline" },
          { "data": "U_ONL", class: "dt-body-right border-start border-bottom align-baseline" },
        ],
        "columnDefs": [
            { "width": "4%", "targets": 0 },
            { "width": "7%", "targets": 1 },
            { "width": "25%", "targets": 2 },
            { "width": "5%", "targets": 3 },
            { "width": "5%", "targets": 4 },
            { "width": "6%", "targets": 5 },
            { "width": "8%", "targets": 6 },
            { "width": "5%", "targets": 7 },
            { "width": "5%", "targets": 8 },
            { "width": "5%", "targets": 9 },
            { "width": "5%", "targets": 10 },
            { "width": "5%", "targets": 11 },
            { "width": "5%", "targets": 12 },
            { "width": "5%", "targets": 13 },
            { "width": "5%", "targets": 14 },
        ],
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "ordering": false,
        "language":{ 
            "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        },
        "bInfo": false,
        "dom": 'Bfrtip',
        "buttons": [{ "extend": 'excelHtml5',"footer": true, },]
    });
}

</script> 
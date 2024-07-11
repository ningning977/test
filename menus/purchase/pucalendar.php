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
                            <select class='form-select form-select-sm' name="Year" id="Year" onchange="CallData();">
                                <?php 
                                for($y = date("Y"); $y >= 2022; $y--) {
                                    $show_y = ($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>"; 
                                    echo $show_y;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Export();'><i class="fas fa-file-excel fa-fw"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="row pt-2">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered' style='font-size: 13px;' id='table1'>
                                <thead class='text-center text-white' style='background-color: #9A1118;'>
                                    <tr>
                                        <th rowspan='2'>สถานะ</th>
                                        <th colspan='13'>มูลค่าสินค้าเข้าแล้ว + แผนการรับสินค้าเข้าปี <span id='YearSelect'></span></th>
                                    </tr>
                                    <tr>
                                        <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            echo "<th>".FullMonth($m)."</th>";
                                        }
                                        ?>
                                        <th>รวมทั้งหมด</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalShowDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search-plus" style='font-size: 15px;'></i> รายละเอียดมูลค่าสินค้าเข้าแล้ว + แผนการรับสินค้าเข้าปี <span id='YearShowDetail'></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' id='Table2' style='font-size: 13px;'>
                                <thead>
                                    <tr>
                                        <th class='text-center border'>วันที่รับเข้า</th>
                                        <th class='text-center border'>วันที่<br>คาดว่าสินค้าเข้า</th>
                                        <th class='text-center border'>เลขที่เอกสาร</th>
                                        <th class='text-center border'>รหัส<br>ซัพพลายเออร์</th>
                                        <th class='text-center border'>ชื่อซัพพลายเออร์</th>
                                        <th class='text-center border'>รหัสสินค้า</th>
                                        <th class='text-center border'>ชื่อสินค้า</th>
                                        <th class='text-center border'>สถานะสินค้า</th>
                                        <th class='text-center border'>คลังสินค้า</th>
                                        <th class='text-center border'>จำนวน</th>
                                        <th class='text-center border'>หน่วย</th>
                                        <th class='text-center border'>มูลค่า (บาท)<br>(Vat)</th>
                                    </tr>
                                </thead>
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
            url: "menus/purchase/ajax/ajaxpucalendar.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
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
    CallData();
});

function CallData() {
    const Year = $("#Year").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/purchase/ajax/ajaxpucalendar.php?a=CallData",
        type: "POST",
        data: { Year : Year, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#YearSelect").html(Year);
                $("#table1 tbody").html(inval['Data']);
            });
            $(".overlay").hide();
        }
    })
}

function ShowDetail(Year,Month,Type) {
    $("#Table2").dataTable().fnClearTable();
    $("#Table2").dataTable().fnDraw();
    $("#Table2").dataTable().fnDestroy();
    $("#Table2").DataTable({
        "ajax": {
            url: "menus/purchase/ajax/ajaxpucalendar.php?a=ShowDetail",
            type: "POST",
            data: { Year : Year, Month : Month, Type : Type, },
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "DocDate", class: "dt-body-center border-start" },
            { "data": "DocDueDate", class: "dt-body-center border-start" },
            { "data": "DocNum", class: "dt-body-center border-start" },
            { "data": "CardCode", class: "dt-body-center border-start" },
            { "data": "CardName", class: "border-start" },
            { "data": "ItemCode", class: "dt-body-center border-start" },
            { "data": "ItemName", class: "border-start" },
            { "data": "ProductStatus", class: "dt-body-center border-start" },
            { "data": "WhsCode", class: "dt-body-center border-start" },
            { "data": "Quantity", class: "dt-body-right border-start" },
            { "data": "unitMsr", class: "border-start" },
            { "data": "Linetotal", class: "dt-body-right border-start border-end" },
        ],
        "columnDefs": [
            { "width": "6%", "targets": 0 },
            { "width": "6%", "targets": 1 },
            { "width": "7%", "targets": 2 },
            { "width": "5%", "targets": 3 },
            { "width": "%", "targets": 4 },
            { "width": "6%", "targets": 5 },
            { "width": "%", "targets": 6 },
            { "width": "5%", "targets": 7 },
            { "width": "5%", "targets": 8 },
            { "width": "5%", "targets": 9 },
            { "width": "5%", "targets": 10 },
            { "width": "5%", "targets": 11 },
        ],
        "createdRow": function (row, data, dataIndex, cells) {
            if(data.DocType == 'OPOR') {
                $(row).addClass('text-primary');
            }
        },
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "ordering": false,
        "dom": 'Bfrtip',
        "buttons": [{ "extend": 'excelHtml5' },]
    })
    $("#YearShowDetail").html(Year);
    $("#ModalShowDetail").modal("show");
}

function Export() {
    const Year = $("#Year").val();
    $.ajax({
        url: "menus/purchase/ajax/ajaxpucalendar.php?a=Export",
        type: "POST",
        data: { Year : Year, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                window.open("../../FileExport/Pucalendar/"+inval['FileName'],'_blank');
            });
        }
    })
}
</script> 
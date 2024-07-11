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
                            <label for="txtYear">เลือกปี</label>
                            <select class='form-select form-select-sm' name="txtYear" id="txtYear" onchange='GetDataSup()'>
                                <?php for($y = date("Y"); $y >= 2023; $y--) {
                                    echo (($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>");
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="txtTaimas">เลือกไตรมาส</label>
                            <select class='form-select form-select-sm' name="txtTaimas" id="txtTaimas" onchange='GetDataSup()'>
                                <?php for($t = 1; $t <= 4; $t++) {
                                    echo (($t == ceil(date("m")/3)) ? "<option value='$t' selected>ไตรมาส $t</option>" : "<option value='$t'>ไตรมาส $t</option>");
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="txtTypesub">เลือกประเภทซัพพลายเออร์</label>
                            <select class='form-select form-select-sm' name="txtTypesub" id="txtTypesub" onchange='GetDataSup()'>
                                <option value="ALL" selected>ทั้งหมด</option>
                                <option value="101,127">ในประเทศ</option>
                                <option value="126">ต่างประเทศ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Export()'><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class='table table-sm table-bordered table-hover' id='TableSub' style='font-size: 11px;'>
                        <thead>
                            <tr>
                                <th rowspan='2' width='4.5%' class='text-center'>รหัสซัพฯ</th>
                                <th rowspan='2' class='text-center'>ชื่อซัพพลายเออร์</th>
                                <th colspan='4' class='text-center border-top'>ภาพรวมปี <span id='YearSelected'></span></th>
                                <th colspan='4' class='text-center border-top' id='HeadTaimas1'></th>
                                <th colspan='4' class='text-center border-top' id='HeadTaimas2'></th>
                                <th colspan='4' class='text-center border-top' id='HeadTaimas3'></th>
                            </tr>
                            <tr>
                                <?php for($r = 1; $r <= 4; $r++){ ?>
                                <th width='4.75%' class='text-center'>P/O<br>ทั้งหมด</th>
                                <th width='4.75%' class='text-center'>P/O<br>ตรงกำหนด</th>
                                <th width='4.75%' class='text-center'>P/O<br>เกินกำหนด</th>
                                <th width='4.75%' class='text-center'>%<br>ตรงกำหนด</th>
                                <?php } ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalViewData" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายการ SO รับสินค้าเข้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class='table table-sm table-bordered table-hover' style='font-size: 12px;' id='TableViewData'>
                        <thead>
                            <tr class='text-center'>
                                <th class='text-center'>เลขที่ P/O</th>
                                <th class='text-center'>เอกสารอ้างอิง</th>
                                <th class='text-center'>ลำดับ</th>
                                <th class='text-center'>รหัสสินค้า</th>
                                <th class='text-center'>ชื่อสินค้า</th>
                                <th class='text-center'>จำนวน</th>
                                <th class='text-center'>หน่วย</th>
                                <th class='text-center'>กำหนดเข้า</th>
                                <th class='text-center'>ระยะเวลา<br>เกินกำหนด (วัน)</th>
                            </tr>
                        </thead>
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

    function FullMonth(nomount){
        let w = "";
        switch  (nomount){
            case 1:
                w = 'มกราคม';	
                break;
            case 2:
                w = 'กุมภาพันธ์';
                break;
            case 3:
                w = 'มีนาคม';	
                break;
            case 4:
                w = 'เมษายน';
                break;
            case 5:
                w = 'พฤษภาคม';	
                break;
            case 6:
                w = 'มิถุนายน';	
                break;
            case 7:
                w = 'กรกฎาคม';	
                break;
            case 8:
                w = 'สิงหาคม';
                break;
            case 9:
                w = 'กันยายน';	
                break;
            case 10:
                w = 'ตุลาคม';	
                break;
            case 11:
                w = 'พฤศจิกายน';	
                break;
            case 12:
                w = 'ธันวาคม';	
                break;
        }
        return w;
    }
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
$(document).ready(function(){
    GetDataSup();
});

function GetDataSup() {
    const Year = $("#txtYear").val();
    const Taimas = $("#txtTaimas").val();
    const Typesub = $("#txtTypesub").val();
    $("#YearSelected").html(Year);
    let start = 0; let end = 0;
    switch (Taimas) {
        case '1': start = 1; end = 3; break;
        case '2': start = 4; end = 6; break;
        case '3': start = 7; end = 9; break;
        case '4': start = 10; end = 12; break;
    }
    let i = start; let NameHead = 1;
    while(i <= end) {
        $("#HeadTaimas"+NameHead).html("เดือน "+FullMonth(i)+" ปี "+Year);
        i++;
        NameHead++;
    }

    $("#TableSub").dataTable().fnClearTable();
    $("#TableSub").dataTable().fnDraw();
    $("#TableSub").dataTable().fnDestroy();
    $("#TableSub").DataTable({
        "ajax": {
            url: "menus/purchase/ajax/ajaxreport_sendsupplier.php?a=GetDataSup",
            type: "POST",
            data: { Year: Year, Taimas: Taimas, Typesub: Typesub },
            dataType: "json",
            dataSrc: "0"
        }, 
        "columns": [
            { "data": "CardCode", class: "dt-body-center border-top" },
            { "data": "CardName", class: "border-top" },

            { "data": "ALLPodue", class: "dt-body-right border-top" },
            { "data": "ALLIndue", class: "dt-body-right border-top" },
            { "data": "ALLOvdue", class: "dt-body-right border-top" },
            { "data": "ALLPercent", class: "dt-body-center border-top" },

            <?php for($m = 1; $m <= 3; $m++) { ?>
                { "data": "M<?php echo $m; ?>Podue", class: "dt-body-right border-top" },
                { "data": "M<?php echo $m; ?>Indue", class: "dt-body-right border-top" },
                { "data": "M<?php echo $m; ?>Ovdue", class: "dt-body-right border-top" },
                { "data": "M<?php echo $m; ?>Percent", class: "dt-body-center border-top" },
            <?php } ?>
        ],
        "createdRow": function (row, data, dataIndex, cells) {
            if(data.ALLOvdue > 0 || data.M1Ovdue > 0 || data.M2Ovdue > 0 || data.M3Ovdue > 0) {
                $(row).addClass("table-danger");
            }
        },
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "bInfo": false,
        "ordering": false,
        "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        },
    });
}

function ViewData(CardCode) {
    $("#TableViewData").dataTable().fnClearTable();
    $("#TableViewData").dataTable().fnDraw();
    $("#TableViewData").dataTable().fnDestroy();
    $("#TableViewData").DataTable({
        "ajax": {
            url: "menus/purchase/ajax/ajaxreport_sendsupplier.php?a=ViewData",
            type: "POST",
            data: { CardCode: CardCode },
            dataType: "json",
            dataSrc: "0"
        }, 
        "columns": [
            { "data": "DocNum", class: "dt-body-center border-top" },
            { "data": "RefDoc", class: "dt-body-center border-top" },
            { "data": "VisOrder", class: "dt-body-center border-top" },
            { "data": "ItemCode", class: "dt-body-center border-top" },
            { "data": "ItemName", class: "border-top" },
            { "data": "Quantity", class: "dt-body-right border-top" },
            { "data": "unitMsr", class: "border-top" },
            { "data": "DocDueDate", class: "dt-body-center border-top" },
            { "data": "DIFF", class: "dt-body-center border-top" },
        ],
        "columnDefs": [
            { "width": "10%", "targets": 0 },
            { "width": "10%", "targets": 1 },
            { "width": "5%", "targets": 2 },
            { "width": "10%", "targets": 3 },
            { "width": "7%", "targets": 5 },
            { "width": "7%", "targets": 6 },
            { "width": "10%", "targets": 7 },
            { "width": "10%", "targets": 8 },
        ],
        "createdRow": function (row, data, dataIndex, cells) {
            if(data.DIFF != "") {
                $(row).addClass("table-danger");
            }
        },
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "bInfo": false,
        "ordering": false,
        "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        },
    });
    $("#ModalViewData").modal("show");
}

function Export() {
    const Year = $("#txtYear").val();
    const Taimas = $("#txtTaimas").val();
    const Typesub = $("#txtTypesub").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/purchase/ajax/ajaxreport_sendsupplier.php?a=Export",
        type: "POST",
        data: { Year: Year, Taimas: Taimas, Typesub: Typesub },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/SendSupplier/"+inval['FileName'],'_blank');
            });
        }
    })
}
</script> 
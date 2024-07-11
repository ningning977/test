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
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for="SelectYear">เลือกปี</label>
                            <select class='form-select form-select-sm' name="SelectYear" id="SelectYear">
                                <?php 
                                for($y = date("Y"); $y >= 2015; $y--) {
                                    if($y == date("Y")) {
                                        echo "<option value='".$y."' selected>".$y."</option>";
                                    }else{
                                        echo "<option value='".$y."'>".$y."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="WareHouse">เลือกคลังสินค้า</label>
                            <select class='form-control form-control-sm' name="WareHouse" id="WareHouse" data-live-search="true">
                                <option value='' selected disabled>เลือกคลังสินค้า</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for="">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="CallData();"><i class="fas fa-search"></i> ค้นหา</button>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for="">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm w-100" onclick="Export();"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered mb-0' style='font-size: 12px;' id='Table1'>
                                <thead>
                                    <tr class='text-center'>
                                        <th class='border-top text-center'>รหัสสินค้า</th>
                                        <th class='border-top text-center'>ชื่อสินค้า</th>
                                        <th class='border-top text-center'>จำนวนคงเหลือ</th>
                                        <th class='border-top text-center'>ประเภทการคืน</th>
                                        <th class='border-top text-center'>วันที่เอกสาร</th>
                                        <th class='border-top text-center'>เลขที่เอกสาร</th>
                                        <th class='border-top text-center'>เลขที่เอกสาร QC</th>
                                        <th class='border-top text-center'>จำนวนคืน</th>
                                        <th class='border-top text-center'>ต้นทุนรวม</th>
                                        <th class='border-top text-center'>สภาพสินค้า</th>
                                        <th class='border-top text-center'>รายละเอียดเซลส์</th>
                                        <th class='border-top text-center'>รายละเอียด QC</th>
                                        <th class='border-top text-center'>รายละเอียดการคืน</th>
                                        <th class='border-top text-center'>ผลตรวจสอบ QC</th>
                                    </tr>
                                </thead>
                            </table>
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='NoData'>
                                <tbody>
                                    <tr class='text-center'>
                                        <td colspan='14'>ไม่มีข้อมูล :(</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="../../js/extensions/apexcharts.js"></script>
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
    GetWareHouse();
});

function GetWareHouse() {
    $.ajax({
        url: "menus/warehouse/ajax/ajaxreport_return.php?a=GetWareHouse",
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#WareHouse").append(inval['output']).selectpicker();
            });
        }
    })
}

function CallData() {
    let SelectYear = $("#SelectYear").val();
    let WareHouse  = $("#WareHouse").val();
    // console.log(SelectYear, WareHouse);
    if(SelectYear != "" && WareHouse != null) {
        $("#NoData").hide();
        $("#Table1").dataTable().fnClearTable();
        $("#Table1").dataTable().fnDraw();
        $("#Table1").dataTable().fnDestroy();
        $("#Table1").DataTable({
            "ajax": {
                url: "menus/warehouse/ajax/ajaxreport_return.php?a=CallData",
                type: "POST",
                data: { SelectYear : SelectYear, WareHouse : WareHouse, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "ItemCode", class: "dt-body-center border-start border-bottom align-top" },
                { "data": "ItemName", class: "border-start border-bottom align-top" },
                { "data": "OnHand", class: "dt-body-right border-start border-bottom align-top" },
                { "data": "ReturnType", class: "border-start border-bottom align-top" },
                { "data": "DocDate", class: "dt-body-center border-start border-bottom align-top" },
                { "data": "DocNum", class: "dt-body-center border-start border-bottom align-top" },
                { "data": "RefNoCust", class: "dt-body-center border-start border-bottom align-top" },
                { "data": "Quantity", class: "dt-body-right border-start border-bottom align-top" },
                { "data": "CostTotal", class: "dt-body-right border-start border-bottom align-top" },
                { "data": "Sapaw", class: "border-start border-bottom align-top" },
                { "data": "InvestigateSales", class: "border-start border-bottom align-top" },
                { "data": "InvestigateQC", class: "border-start border-bottom align-top" },
                { "data": "ReturnDetail", class: "border-start border-bottom align-top" },
                { "data": "ResultReturn", class: "border-start border-bottom align-top" },
            ],
            "createdRow": function (row, data, dataIndex, cells) {
                if(data.Row == 0) {
                    $('td:eq(0)', row).attr('colspan', 14);
                    $('td:eq(1)', row).css('display', 'none');
                    $('td:eq(2)', row).css('display', 'none');
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
                }
            },
            "columnDefs": [
                { "width": "6%",  "targets": 0 },
                { "width": "15%",  "targets": 1 },
                { "width": "5%",  "targets": 2 },
                { "width": "6%",  "targets": 3 },
                { "width": "6%",  "targets": 4 },
                { "width": "7%",  "targets": 5 },
                { "width": "6%",  "targets": 6 },
                { "width": "5%",  "targets": 7 },
                { "width": "5%",  "targets": 8 },
                { "width": "8%",  "targets": 9 },
                { "width": "9.2%",  "targets": 10 },
                { "width": "9.2%",  "targets": 11 },
                { "width": "9.2%",  "targets": 12 },
                { "width": "9.2%",  "targets": 13 },
            ],
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 15,
            "bInfo": false,
            "ordering": false,
            "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
            }
        });
    }else{
        $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
        $("#alert_body").html("กรุณาเลือกข้อมูลที่ต้องการค้นหาให้ครบ");
        $("#alert_modal").modal('show');
        $("#NoData").show();
    }
}

function Export() {
    let SelectYear = $("#SelectYear").val();
    let WareHouse  = $("#WareHouse").val();
    console.log(SelectYear, WareHouse);
    if(SelectYear != "" && WareHouse != null) {
        $(".overlay").show();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxreport_return.php?a=Export",
            type: "POST",
            data: { SelectYear : SelectYear, WareHouse : WareHouse, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $(".overlay").hide();
                    if(inval['Row'] > 1) {
                        window.open("../../FileExport/ReportReturn/"+inval['FileName'],'_blank');
                    }else{
                        $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
                        $("#alert_body").html("ไม่มีข้อมูล คลัง "+WareHouse+" ปี "+SelectYear);
                        $("#alert_modal").modal('show');
                    }
                });
            }
        })
    }else{
        $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
        $("#alert_body").html("กรุณาเลือกข้อมูลที่ต้องการค้นหาให้ครบ");
        $("#alert_modal").modal('show');
    }
}

</script> 
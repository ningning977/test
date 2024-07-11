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
                        <div class="form-gruop">
                            <label for="">เลือกปี</label>
                            <select class='form-select form-select-sm' name="txtYaer" id="txtYaer" onchange='CallData();'>
                                <?php
                                for($y = date("Y"); $y >= 2023; $y--) {
                                    $select_year = ($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>";
                                    echo $select_year;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="">เลือกเดือน</label>
                            <select class='form-select form-select-sm' name="txtMonth" id="txtMonth" onchange='CallData();'>
                                <?php
                                for($m = 1; $m <= 12; $m++) {
                                    $select_month = ($m == date("m")) ? "<option value='$m' selected>".FullMonth($m)."</option>" : "<option value='$m'>".FullMonth($m)."</option>";
                                    echo $select_month;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Export();'><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive pt-3">
                    <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableWithDraw'>
                        <thead>
                            <tr>
                                <th rowspan='2' class='text-center border'><input type='checkbox' class='form-check-input' name='AllChk' id='AllChk' onclick='CheckAll();'></th>
                                <th rowspan='2' class='text-center border'>พนักงานขนส่ง</th>
                                <th rowspan='2' class='text-center border'>เลขที่บิล</th>
                                <th rowspan='2' class='text-center border'>รหัสลูกค้า</th>
                                <th rowspan='2' class='text-center border'>ชื่อลูกค้า</th>
                                <th rowspan='2' class='text-center border'>ชื่อขนส่ง</th>
                                <th rowspan='2' class='text-center border'>วันที่ส่ง</th>
                                <th colspan='7' class='text-center border'>ค่าขนส่ง</th>
                            </tr>
                            <tr>
                                <th class='text-center border'>MT1</th>
                                <th class='text-center border'>MT2</th>
                                <th class='text-center border'>TT1</th>
                                <th class='text-center border'>TT2</th>
                                <th class='text-center border'>หน้าร้าน</th>
                                <th class='text-center border'>ออนไลน์</th>
                                <th class='text-center border'>ส่วนกลาง</th>
                            </tr>
                        </thead>
                    </table>
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
        CallData();
	});

    function CallData() {
        const Year = $("#txtYaer").val();
        const Month = $("#txtMonth").val();
        $("#TableWithDraw").dataTable().fnClearTable();
        $("#TableWithDraw").dataTable().fnDraw();
        $("#TableWithDraw").dataTable().fnDestroy();
        $("#TableWithDraw").DataTable({
            "ajax": {
                url: "menus/warehouse/ajax/ajaxwithdraw_shipcost.php?a=CallData",
                type: "POST",
                data: { Year: Year, Month: Month, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "CheckBox", class: "dt-body-center border-start border-bottom" },
                { "data": "LogiName", class: "border-start border-bottom" },
                { "data": "BillDocNum", class: "dt-body-center border-start border-bottom" },
                { "data": "CardCode", class: "dt-body-center border-start border-bottom" },
                { "data": "CardName", class: "border-start border-bottom" },
                { "data": "ShippingName", class: "border-start border-bottom" },
                { "data": "ReceiveDate", class: "dt-body-center border-start border-bottom" },
                { "data": "COST_MT1", class: "dt-body-right border-start border-bottom" },
                { "data": "COST_MT2", class: "dt-body-right border-start border-bottom" },
                { "data": "COST_TT1", class: "dt-body-right border-start border-bottom" },
                { "data": "COST_TT2", class: "dt-body-right border-start border-bottom" },
                { "data": "COST_OUL", class: "dt-body-right border-start border-bottom" },
                { "data": "COST_ONL", class: "dt-body-right border-start border-bottom" },
                { "data": "COST_KBI", class: "dt-body-right border-start border-bottom" },
            ],
            "columnDefs": [
                { "width": "2%", "targets": 0 },
                { "width": "10%", "targets": 1 },
                { "width": "8%", "targets": 2 },
                { "width": "6%", "targets": 3 },
                { "width": "23%", "targets": 4 },
                { "width": "13%", "targets": 5 },
                { "width": "6%", "targets": 6 },
                { "width": "4.5%", "targets": 7 },
                { "width": "4.5%", "targets": 8 },
                { "width": "4.5%", "targets": 9 },
                { "width": "4.5%", "targets": 10 },
                { "width": "4.5%", "targets": 11 },
                { "width": "4.5%", "targets": 12 },
                { "width": "4.5%", "targets": 13 },
            ],
            "createdRow": function (row, data, dataIndex, cells) {
                if(data.Withdraw == 'Y') {
                    $(row).addClass("table-success");
                }
            },
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "ordering": false,
            "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
            }
        });
    }

    function CheckAll() {
        if($("#AllChk").is(":checked")) {
            $("input[id*='Chk_']").prop("checked",true);
        }else{
            $("input[id*='Chk_']").prop("checked",false);
        }
    }

    function Export() {
        let ID = "";
        const Year = $("#txtYaer").val();
        const Month = $("#txtMonth").val();
        $.each($(".Chk_Doc:checked"), function(k) {
            ID += (k < $(".Chk_Doc:checked").length-1) ? $(this).val()+"," : $(this).val(); 
        });

        if(ID != '') {
            $.ajax({
                url: "menus/warehouse/ajax/ajaxwithdraw_shipcost.php?a=Export",
                type: "POST",
                data: { ID: ID, Year: Year, Month: Month, },
                success: function(result) {
                    let obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        window.open("../../FileExport/WithdrawShipcost/"+inval['FileName'],'_blank');
                    });
                }
            })
        }else{
            $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
            $("#alert_body").html("กรุณาเลือกรายการก่อน");
            $("#alert_modal").modal('show');
        }
    }

</script> 
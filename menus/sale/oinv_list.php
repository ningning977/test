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
                            <label for="Year">เลือกปี</label>
                            <select class="form-select form-select-sm" id="Year" name="Year" onchange="GetSaleName();">
                                <?php 
                                for($y = date("Y"); $y >= 2023; $y--) {
                                    if($y == date("Y")) {
                                        echo "<option value='$y' selected>$y</option>";
                                    }elseif($y >= date("Y")-1){
                                        echo "<option value='$y'>$y</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="Month">เลือกเดือน</label>
                            <select class="form-select form-select-sm" id="Month" name="Month" onchange="CallData();">
                                <?php 
                                for($m = 1; $m <= 12; $m++) {
                                    if($m == date("m")) {
                                        echo "<option value='$m' selected>".FullMonth($m)."</option>";
                                    }else{
                                        echo "<option value='$m'>".FullMonth($m)."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="SlpCode"><i class="fas fa-users"></i> เลือกทีม / พนักงานขาย</label>
                            <select class="form-control form-control-sm" id="SlpCode" name="SlpCode" data-live-search="true" onchange="CallData();">
                                <option selected disabled>กรุณารอสักครู่...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Excel();'><i class="fas fa-file-excel fa-fw"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered' style='font-size: 13px;' id='Table1'>
                                <thead>
                                    <tr>
                                        <th class='text-center border-bottom border-top'>เลขที่เอกสาร</th>
                                        <th class='text-center border-bottom border-top'>วันที่</th>
                                        <th class='text-center border-bottom border-top'>รหัสลูกค้า</th>
                                        <th class='text-center border-bottom border-top'>ชื่อลูกค้า</th>
                                        <th class='text-center border-bottom border-top'>พนักงานขาย</th>
                                        <th class='text-center border-bottom border-top'>ยอดขาย (บาท)</th>
                                        <th class='text-center border-bottom border-top'>เลขที่ PO</th>
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
    GetSaleName();
});

function GetSaleName() {
    $(".overlay").show();
    const Year = $("#Year").val();
    $("#SlpCode").selectpicker("destroy");
    $("#Table1").dataTable().fnClearTable();
    $("#Table1").dataTable().fnDraw();
    $("#Table1").dataTable().fnDestroy();
    $.ajax({
        url: "menus/sale/ajax/ajaxoinv_list.php?a=GetSaleName",
        type: "POST",
        data: { Year: Year },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#SlpCode").html(inval["output"]);
                if(inval['LogCode'] != "N") {
                    $("#SlpCode").val(inval['LogCode']).change();
                }
            });
            $("#SlpCode").selectpicker();
            $(".overlay").hide();
        }
    });
}

function CallData() {
    const SlpCode = $("#SlpCode").val();
    const Year    = $("#Year").val();
    const Month   = $("#Month").val();
    if(SlpCode != null) {
        $("#Table1").dataTable().fnClearTable();
        $("#Table1").dataTable().fnDraw();
        $("#Table1").dataTable().fnDestroy();
        $("#Table1").DataTable({
            "ajax": {
                url: "menus/sale/ajax/ajaxoinv_list.php?a=CallData",
                type: "POST",
                data: { SlpCode : SlpCode, Year : Year, Month : Month, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "DocNum", class: "text-center border-start border-bottom" },
                { "data": "DocDate", class: "text-center border-start border-bottom" },
                { "data": "CardCode", class: "text-center border-start border-bottom" },
                { "data": "CardName", class: "border-start border-bottom" },
                { "data": "SlpName", class: "border-start border-bottom" },
                { "data": "DocTotal", class: "dt-body-right border-start border-bottom" },
                { "data": "PO", class: "border-start border-bottom" },
            ],
            "columnDefs": [
                { "width": "8%", "targets": 0 },
                { "width": "8%", "targets": 1 },
                { "width": "6%", "targets": 2 },
                { "width": "24%", "targets": 3 },
                { "width": "24%", "targets": 4 },
                { "width": "8%", "targets": 5 },
                { "width": "12%", "targets": 6 }
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
    }
}

function Excel() {
    const SlpCode = $("#SlpCode").val();
    const Year    = $("#Year").val();
    const Month   = $("#Month").val();
    if(SlpCode != null) {
        $(".overlay").show();
        $.ajax({
            url: "menus/sale/ajax/ajaxoinv_list.php?a=Excel",
            type: "POST",
            data: { SlpCode : SlpCode, Year : Year, Month : Month, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    window.open("../../FileExport/OINVList/"+inval['FileName'],'_blank');
                });
                $(".overlay").hide();
            }
        })
    }else{
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 65px;'></i>");
        $("#alert_body").html("โปรดเลือก ทีม / พนักงานขายก่อน :(");
        $("#alert_modal").modal("show");
    }
}
</script> 
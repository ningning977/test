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
                            <select class='form-select form-select-sm' name="Year" id="Year">
                                <?php 
                                for($y = date("Y"); $y >= 2023; $y--) {
                                    if($y == date("Y")) {
                                        echo "<option value='$y' selected>$y</option>";
                                    }else{
                                        echo "<option value='$y'>$y</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="Month">เลือกเดือน</label>
                            <select class='form-select form-select-sm' name="Month" id="Month">
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
                        <div class="form-group">
                            <label for="CH">เลือกช่องทางขาย</label>
                            <select class='form-select form-select-sm' name="CH" id="CH">
                                <option value="'MT1','MT2','TT2','OUL','TT1','ONL'" selected>เลือกทั้งหมด</option>
                                <option value="'MT1'">MT1</option>
                                <option value="'MT2'">MT2</option>
                                <option value="'TT2'">TT2</option>
                                <option value="'OUL','TT1'">OUL+TT1</option>
                                <option value="'ONL'">ONL</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="GP">GP</label>
                            <input type="number" name='GP' id='GP' class='form-control form-control-sm text-right' style='width: 120px;'>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-primary w-100' onclick='CallData();'><i class="fas fa-search fa-fw"></i> ค้นหา</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12.5px;' id='Table1'>
                                <thead>
                                    <tr>
                                        <th class='text-center border-top'>เลขที่เอกสาร</th>
                                        <th class='text-center border-top'>ชื่อลูกค้า</th>
                                        <th class='text-center border-top'>พนักงานขาย</th>
                                        <th class='text-center border-top'>CH</th>
                                        <th class='text-center border-top'>ชื่อสินค้า</th>
                                        <th class='text-center border-top'>จำนวน<br>(หน่วย)</th>
                                        <th class='text-center border-top'>ราคา (บาท)</th>
                                        <th class='text-center border-top'>GP</th>
                                        <th class='text-center border-top'>GP รวม</th>
                                        <th class='text-center border-top'>อนุมัติ GP</th>
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
});

function CallData() {
    const Year  = $("#Year").val();
    const Month = $("#Month").val();
    const CH    = $("#CH").val();
    const GP    = $("#GP").val();

    $("#Table1").dataTable().fnClearTable();
    $("#Table1").dataTable().fnDraw();
    $("#Table1").dataTable().fnDestroy();
    $("#Table1").DataTable({
        "ajax": {
            url: "menus/marketing/ajax/ajaxreport_profit.php?a=CallData",
            type: "POST",
            data: { Year : Year, Month : Month, CH : CH, GP : GP, },
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "DocNum", class: "dt-body-center border-start border-bottom align-top" },
            { "data": "CardName", class: "border-start border-bottom align-top" },
            { "data": "SlpName", class: "border-start border-bottom align-top" },
            { "data": "CH", class: "dt-body-center border-start border-bottom align-top" },
            { "data": "ItemName", class: "border-start border-bottom align-top" },
            { "data": "Quantity", class: "dt-body-right border-start border-bottom align-top" },
            { "data": "Price", class: "dt-body-right border-start border-bottom align-top" },
            { "data": "GP", class: "dt-body-right border-start border-bottom align-top" },
            { "data": "TotalGP", class: "dt-body-right border-start border-bottom align-top" },
            { "data": "GPApp", class: "dt-body-center border-start border-bottom align-top" },
        ],
        "columnDefs": [
            { "width": "7%", "targets": 0 },
            { "width": "20%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "3%", "targets": 3 },
            { "width": "25%", "targets": 4 },
            { "width": "5%", "targets": 5 },
            { "width": "7%", "targets": 6 },
            { "width": "6%", "targets": 7 },
            { "width": "6%", "targets": 8 },
            { "width": "6%", "targets": 9 },
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
</script> 
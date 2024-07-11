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
                                for($y = date("Y"); $y >= 2023; $y--) {
                                    echo "<option value='$y'>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="form-group">
                            <label for="Team">เลือกทีม <span class='text-danger'>*</span></label>
                            <select class='form-select form-select-sm' name="Team" id="Team" onchange='CallData();'>
                                <option value="" selected disabled>เลือกทีม</option>
                                <option value="MT1">ทีม MT1</option>
                                <option value="MT2">ทีม MT2</option>
                                <option value="TT2">ทีม TT2</option>
                                <option value="OUL">ทีม OUL + TT1</option>
                                <option value="ONL">ทีม ONL</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label for="ItemCode">เลือกสินค้า <span class='text-danger'>*</span></label>
                            <select class='form-control form-control-sm' name="ItemCode" id="ItemCode" data-live-search="true" onchange='CallData();'>
                                <option value="" selected disabled>เลือกสินค้า</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row pt-2">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table1'>
                                <thead>
                                    <tr>
                                        <th class='text-center border-top'>ชื่อเซลล์</th>
                                        <?php for($m = 1; $m <= 12; $m++) {
                                            echo "<th class='text-center border-top'>".FullMonth($m)."</th>";
                                        } ?>
                                        <th class='text-center border-top'>รวม</th>
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
$.ajax({
    url: "../json/OITM.json",
    cache: false,
    success: function(result) {
        var filt_data = 
            result.
                filter(x => x.ItemStatus == "A").
                sort(function(key, inval) {
                    return key.ItemCode.localeCompare(inval.ItemCode);
                });

        var opt = "";

        $.each(filt_data, function(key, inval) {
            opt += "<option value='"+inval.ItemCode+"'>"+inval.ItemCode+" | ["+inval.ProductStatus+"] - "+inval.ItemName+" ["+inval.BarCode+"]</option>";
        });

        $("#ItemCode").append(opt).selectpicker();
    }
});

function CallData() {
    const Year = $("#Year").val();
    const Team = $("#Team").val();
    const ItemCode = $("#ItemCode").val();
    console.log(Year, Team, ItemCode);
    if(Team != null && ItemCode != null) {
        $("#Table1").dataTable().fnClearTable();
        $("#Table1").dataTable().fnDraw();
        $("#Table1").dataTable().fnDestroy();
        $("#Table1").DataTable({
            "ajax": {
                url: "menus/sale/ajax/ajaxsalereport_byitem.php?a=CallData",
                type: "POST",
                data: { Year : Year, Team : Team, ItemCode : ItemCode, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "SlpName", class: "border-start border-bottom" },
                <?php for($m = 1 ; $m <= 12; $m++) {?>
                    { "data": "M_<?php echo $m; ?>", class: "dt-body-right border-start border-bottom" },
                <?php }?>
                { "data": "Sum", class: "dt-body-right border-start border-bottom" },
            ],
            "columnDefs": [
                { "width": "18%", "targets": 0 },
                <?php for($r = 1 ; $r <= 12; $r++) {?>
                    { "width": "6%", "targets": <?php echo $r; ?> },
                <?php }?>
                { "width": "10%", "targets": 13 },
            ],
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 15,
            "bInfo": false,
            // "ordering": false,
            "language":{ 
                    "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
            },
        });
    }
}
</script> 
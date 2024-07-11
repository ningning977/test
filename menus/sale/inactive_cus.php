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
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">ลูกค้าไม่เคลื่อนไหว</label>
                            <select class='form-select form-select-sm' name="Active" id="Active">
                                <option value="3" selected>3 เดือน</option>
                                <option value="6">6 เดือน</option>
                                <option value="12">12 เดือน</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-primary w-100' onclick='CallData();'><i class="fas fa-search fa-fw"></i> ค้นหา</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Export();'><i class="fas fa-file-excel fa-fw"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='Table1'>
                                <thead>
                                    <tr>
                                        <th class='text-center border-top'>รหัสลูกค้า</th>
                                        <th class='text-center border-top'>ชื่อลูกค้า</th>
                                        <th class='text-center border-top'>ทีมขายล่าสุด</th>
                                        <th class='text-center border-top'>พนักงานขายล่าสุด</th>
                                        <th class='text-center border-top'>วันที่เปิดบิลล่าสุด</th>
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
function CallData() {
    const Active = $("#Active").val();
    $("#Table1").dataTable().fnClearTable();
    $("#Table1").dataTable().fnDraw();
    $("#Table1").dataTable().fnDestroy();
    $("#Table1").DataTable({
        "ajax": {
            url: "menus/sale/ajax/ajaxinactive_cus.php?a=CallData",
            type: "POST",
            data: { Active : Active, },
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "CardCode", class: "dt-body-center border-start border-bottom" },
            { "data": "CardName", class: "border-start border-bottom" },
            { "data": "TeamName", class: "border-start border-bottom" },
            { "data": "SlpName", class: "border-start border-bottom" },
            { "data": "PastDate", class: "dt-body-center border-start border-bottom" },
        ],
        "columnDefs": [
            { "width": "10%", "targets": 0 },
            { "width": "30%", "targets": 1 },
            { "width": "22%", "targets": 2 },
            { "width": "22%", "targets": 3 },
            { "width": "16%", "targets": 4 },
        ],
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

function Export() {
    const Active = $("#Active").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxinactive_cus.php?a=Export",
        type: "POST",
        data: { Active : Active, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/InActiveCus/"+inval['FileName'],'_blank');
            });
        }
    })
}
    
</script> 
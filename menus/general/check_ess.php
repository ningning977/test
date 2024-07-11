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
                <div class="table-responsive">
                    <table class='table table-sm table-bordered table-hover' id='TableCheckESS'>
                        <thead>
                            <tr>
                                <th class='text-center border border-top'>No.</th>
                                <th class='text-center border border-top'>ชื่อสถานที่</th>
                                <th class='text-center border border-top'><i class='fas fa-map-marker-alt'></i></th>
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
    GetDataCheckESS();
});

function GetDataCheckESS() {
    $("#TableCheckESS").dataTable().fnClearTable();
    $("#TableCheckESS").dataTable().fnDraw();
    $("#TableCheckESS").dataTable().fnDestroy();
    $("#TableCheckESS").DataTable({
        "ajax": {
            url: "menus/general/ajax/ajaxcheck_ess.php?a=GetDataCheckESS",
            type: "GET",
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
          { "data": "No", class: "dt-body-center border-start border-bottom" },
          { "data": "LocationName", class: "border-start border-bottom" },
          { "data": "Location", class: "dt-body-center border-start border-bottom" },
        ],
        "columnDefs": [
            { "width": "8%", "targets": 0 },
            { "width": "13%", "targets": 2 },
        ],
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "ordering": false,
        "language":{ 
            "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        }
    });
}

</script> 
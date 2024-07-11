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
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12.5px;' id='Table1'>
                                <thead class='bg-primary text-white'>
                                    <tr>
                                        <th class='text-center border-top'>เลขที่เอกสาร</th>
                                        <th class='text-center border-top'>วันที่</th>
                                        <th class='text-center border-top'>รหัสลูกค้า</th>
                                        <th class='text-center border-top'>ชื่อลูกค้า</th>
                                        <th class='text-center border-top'>พนักงานขาย</th>
                                        <th class='text-center border-top'>มูลค่า<br>ท้ายบิล</th>
                                        <th class='text-center border-top'>ผู้จัดทำเอกสาร</th>
                                        <th class='text-center border-top'>หมายเหตุ</th>
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

<div class="modal fade" id="ModalDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search-plus" style='font-size: 15px;'></i> ข้อมูลเอกสารคงค้าง (<span id='HDetail'></span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableDetail'>
                                <thead>
                                    <tr class='text-center'>
                                        <th width='7%'>ลำดับที่</th>
                                        <th width='12%'>รหัสสินค้า</th>
                                        <th width='15%'>บาร์โค้ดสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th width='6%'>คลัง</th>
                                        <th width='8%'>จำนวน</th>
                                        <th width='4%'>หน่วย</th>
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

<script src="../../js/extensions/apexcharts.js"></script>
<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
        CallData();
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
        $("#Table1").dataTable().fnClearTable();
        $("#Table1").dataTable().fnDraw();
        $("#Table1").dataTable().fnDestroy();
        $("#Table1").DataTable({
            "ajax": {
                url: "menus/warehouse/ajax/ajaxdocopen.php?a=CallData",
                type: "POST",
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "DocNum",   class: "dt-body-center border-top align-top" },
                { "data": "DocDate",  class: "dt-body-center border-top align-top" },
                { "data": "CardCode", class: "dt-body-center border-top align-top" },
                { "data": "CardName", class: "border-top align-top" },
                { "data": "SlpName",  class: "border-top align-top" },
                { "data": "Total",    class: "dt-body-right border-top align-top" },
                { "data": "ShipName", class: "border-top align-top" },
                { "data": "Comments", class: "border-top align-top" },
            ],
            "columnDefs": [
                { "width": "7%",  "targets": 0 },
                { "width": "6%",  "targets": 1 },
                { "width": "5%",  "targets": 2 },
                { "width": "15%", "targets": 3 },
                { "width": "15%", "targets": 4 },
                { "width": "5%",  "targets": 5 },
                { "width": "9%",  "targets": 6 },
                { "width": "38%", "targets": 7 },
            ],
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 15,
            "ordering": false,
            "language":{ 
                    "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
            },
        });
    }

    function Detail(DocEntry, DocNum) {
        $.ajax({
            url: "menus/warehouse/ajax/ajaxdocopen.php?a=Detail",
            type: "POST",
            data: { DocEntry : DocEntry, },
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#HDetail").html(DocNum);
                    $("#TableDetail tbody").html(inval['Data']);
                    $("#ModalDetail").modal("show");
                });
            }
        })
    }

</script> 
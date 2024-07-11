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
                    <div class="col-lg d-flex">
                        <div class='form-group' style='width: 400px;'>
                            <label for="CardCode">เลือกร้านค้า<span class='alertCus text-danger ps-1 pe-0 pt-0 pb-0'></span></label>
                            <select class="form-control form-control-sm " id="CardCode" data-live-search="true" onchange="SelectData();">
                                <option value="" selected disabled>กรุณาเลือกร้านค้า</option>
                            </select>
                        </div>
                        <div class='ps-3 form-group' style='width: 400px;'>
                            <label for="ItemCode">เลือกสินค้า<span class='alertItem text-danger ps-1 pe-0 pt-0 pb-0'></span></label>
                            <select class="form-control form-control-sm " id="ItemCode" data-live-search="true" onchange="SelectData();">
                                <option value="" selected disabled>กรุณาเลือกสินค้า</option>
                            </select>
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg">
                        <div class='d-flex justify-content-end pb-1'>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="PrintTaxItem();"><i class="fas fa-print fa-fw fa-1x" aria-hidden="true"></i> พิมพ์</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id='TableShow'>
                                <thead class='text-center'>
                                    <th class='border-top text-center'>ลำดับ</th>
                                    <th class='border-top text-center'>วันที่</th>
                                    <th class='border-top text-center'>เลขที่เอกสาร</th>
                                    <th class='border-top text-center'>ชื่อร้านค้า</th>
                                    <th class='border-top text-center'>รายการสินค้า</th>
                                    <th class='border-top text-center'>จำนวนสินค้า</th>
                                    <th class='border-top text-center'>หน่วย</th>
                                    <th class='border-top text-center'>ราคา<br>(ก่อน VAT)</th>
                                    <th class='border-top text-center'>ส่วนลด</th>
                                    <th class='border-top text-center'>รวม</th>
                                </thead>
                                <tbody>
                                    <td colspan='10' class='text-center'>ไม่มีข้อมูล :)</td>
                                </tbody>
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

    $.ajax({
        url: "../json/OCRD.json",
        cache: false,
        success: function(result) {
            var filt_data = 
                result.
                    filter(x => x.CardStatus == "A").
                    filter(x => x.CardType == "C").
                    sort(function(key, inval) {
                        return key.CardCode.localeCompare(inval.CardCode);
                    });

            var opt = "";
            $.each(filt_data, function(key, inval) {
                opt += "<option value='"+inval.CardCode+"'>"+inval.CardCode+" | "+inval.CardName+"</option>";
            });
            $("#CardCode").append(opt).selectpicker();
        }
    });

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

    function SelectData() {
        var CardCode = $("#CardCode").val();
        var ItemCode = $("#ItemCode").val();
        $(".alertCus").html(""); $(".alertItem").html("");
        if(CardCode != null && ItemCode != null) {
            $(".overlay").show();
            $("#TableShow").DataTable({
                "ajax": {
                    url: "menus/sale/ajax/ajaxreport_byitems.php?a=SelectData",
                    type: "POST",
                    data: { CardCode : CardCode, ItemCode : ItemCode, },
                    dataType: "json",
                    dataSrc: "0"
                },
                "columns": [
                    { "data": "no", class: "text-center border-start border-bottom" },
                    { "data": "DocDate", class: "text-center border-start border-bottom" },
                    { "data": "DocNum", class: "text-center border-start border-bottom" },
                    { "data": "CardName", class: "border-start border-bottom" },
                    { "data": "ItemCode", class: "border-start border-bottom" },
                    { "data": "Quantity", class: "dt-body-right border-start border-bottom" },
                    { "data": "Unit", class: "text-center border-start border-bottom" },
                    { "data": "Price", class: "dt-body-right border-start border-bottom" },
                    { "data": "U_Disc", class: "text-center border-start border-bottom" },
                    { "data": "LineTotal", class: "dt-body-right border-start border-bottom" },
                ],
                "columnDefs": [
                    { "width": "3%", "targets": 0 },
                    { "width": "5%", "targets": 1 },
                    { "width": "8%", "targets": 2 },
                    { "width": "20%", "targets": 3 },
                    { "width": "20%", "targets": 4 },
                    { "width": "5%", "targets": 5 },
                    { "width": "5%", "targets": 6 },
                    { "width": "7%", "targets": 7 },
                    { "width": "5%", "targets": 8 },
                    { "width": "12%", "targets": 9 },
                ],
                "createdRow": function (row, data, dataIndex, cells) {
                    if(data.no == ""){ 
                        $('td:eq(0)', row).css('display', 'none');
                        $('td:eq(1)', row).attr('colspan', '10');
                        $('td:eq(2)', row).css('display','none');
                        $('td:eq(3)', row).css('display', 'none');
                        $('td:eq(4)', row).css('display', 'none');
                        $('td:eq(5)', row).css('display', 'none');
                        $('td:eq(6)', row).css('display', 'none');
                        $('td:eq(7)', row).css('display', 'none');
                        $('td:eq(8)', row).css('display', 'none');
                        $('td:eq(9)', row).css('display', 'none');
                    }
                },
                destroy: true,
                "responsive": true, 
                "lengthChange": false, 
                "autoWidth": false,
                "pageLength": 15,
                "searching": false,
            });
            $(".overlay").hide();
        }else{
            if(CardCode == null) {
                $(".alertCus").html("<i class='fas fa-exclamation-circle'></i>");
            }else{
                $(".alertItem").html("<i class='fas fa-exclamation-circle'></i>");
            }
        }
    }

    function PrintTaxItem() {
        var CardCode = $("#CardCode").val();
        var ItemCode = $("#ItemCode").val();
        if(CardCode != null && ItemCode != null) {
            window.open ('menus/sale/print/printtax_items.php?cardcode='+CardCode+'&itemcode='+ItemCode,'_blank');
        }
    }
</script> 
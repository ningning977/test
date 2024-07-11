<style type="text/css">

</style>
<?php
    echo "<input type='hidden' id='HeadeMenuLink' value = '".$_GET['p']."'>";
    
    switch($_SESSION['DeptCode']) {
        case "DP001":
        case "DP002":
        case "DP004":
        case "DP009":
            $VisCost = true;
            // $VisCost = false;
        break;
        case "DP003":
            switch($_SESSION['LvCode']) {
                case "LV010":
                case "LV011":
                case "LV012":
                case "LV013":
                    $VisCost = true;
                break;
                default: $VisCost = false;
            }
        break;
        default: $VisCost = false; break;
    }
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
                        <div class="form-group" style='width: 120px;'>
                            <label for="">เลือกปี</label>
                            <select class="form-select form-select-sm" name="Year" id="Year" onchange='CallData();'>
                                <?php 
                                for($y = date("Y"); $y >= 2022; $y--) {
                                    if($y == date("Y")) {
                                        echo "<option value='".$y."' seleted>".$y."</option>";
                                    }else{
                                        echo "<option value='".$y."'>".$y."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group" style='width: 170px;'>
                            <label for="">เลือกสถานะสินค้า</label>
                            <select class="form-select form-select-sm" name="Product" id="Product" onchange='CallData();'>
                                <option value='ALL' seleted>สถานะสินค้าทั้งหมด</option>
                                <option value='D'>D</option>
                                <option value='R'>R</option>
                                <option value='A'>A</option>
                                <option value='W'>W</option>
                                <option value='N'>N</option>
                                <option value='M'>M</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group" style="width: 256px;">
                            <label for="">เลือกพนักงานขาย</label>
                            <select class="form-select form-select-sm" name="SlpCode" id="SlpCode" onchange='CallData();'>
                                <option value='ALL' selected'>พนักงานขายทั้งหมด</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group" style="width: 256px;">
                            <label for="">เลือกร้านค้า</label>
                            <select class="form-control form-control-sm" id="CardCode" name="CardCode" data-live-search="true" data-size="10" onchange='CallData();'>
                                <option value='ALL' selected>ลูกค้าทั้งหมด</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Excel();'><i class="fas fa-file-excel fa-spin"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table1'>
                                <thead style='background-color: #9A1118; color: #fff;'>
                                    <tr class='text-center'>
                                        <th rowspan='2' class='border-top text-center'>No.</th>
                                        <th rowspan='2' class='border-top text-center'>รหัสสินค้า</th>
                                        <th rowspan='2' class='border-top text-center'>ชื่อสินค้า</th>
                                        <th rowspan='2' class='border-top text-center'>สถานะสินค้า</th>
                                        <th rowspan='2' class='border-top text-center'>สินค้าคงคลัง</th>
                                        <th rowspan='2' class='border-top text-center'>หน่วย</th>
                                    <?php if($VisCost == true) { ?>
                                        <th rowspan='2' class='border-top text-center'>ต้นทุนล่าสุด</th>
                                        <th rowspan='2' class='border-top text-center'>วันที่เข้าล่าสุด</th>
                                    <?php } ?>
                                        <th colspan='12' class='border-top text-center'>ยอดขายปี <span class='HYear'></span></th>
                                    </tr>
                                    <tr class='text-center'>
                                    <?php 
                                        for($m = 1; $m <= 12; $m++) {
                                            echo "<th class='text-center'>".FullMonth($m)."</th>";
                                        }
                                    ?>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalViewData" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fab fa-readme' style='font-size: 20px;'></i>&nbsp;&nbsp;&nbsp;รายละเอียดการสั่งซื้อ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='TableDetail'>
                                <thead>
                                    <tr class='text-center'>
                                        <th width='5%'>No.</th>
                                        <th width='8%'>รหัสสินค้า</th>
                                        <th width='26%'>ชื่อสินค้า</th>
                                        <th width='25%'>Vendor</th>
                                        <th width='5%'>หน่วย</th>
                                        <th width='8%'>วันที่เอกสาร</th>
                                        <th width='7%'>จำนวน</th>
                                        <th width='8%'>ราคา/หน่วย</th>
                                        <th width='8%'>ราคารวม</th>
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
    GetSlpCode();
    GetCardCode();
});

function GetSlpCode() {
    $.ajax({
        url: "menus/purchase/ajax/ajaxhisitem.php?a=GetSlpCode",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            var slct = "";
            $.each(obj, function(key,inval) {
                var Rows = inval['Rows'];
                for(i = 0; i < Rows; i++) {
                    slct += "<option value='"+inval[i]['SlpCode']+"'>"+inval[i]['SlpName']+"</option>";
                }
                $("#SlpCode").append(slct);
            });
        }
    })
}

function GetCardCode() {
    $.ajax({
        url: "../json/OCRD.json",
        cache: false,
        success: function(result) {
            var filt_data = result.
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
}

function CallData() {
    $(".overlay").show();
    var Product  = $("#Product").val();
    var Year     = $("#Year").val();
    var SlpCode  = $("#SlpCode").val();
    var CardCode = $("#CardCode").val();
    $(".HYear").html(Year);
    $("#Table1").dataTable().fnClearTable();
    $("#Table1").dataTable().fnDraw();
    $("#Table1").dataTable().fnDestroy();
    $("#Table1").DataTable({
        "ajax": {
            url: "menus/purchase/ajax/ajaxhisitem.php?a=CallData",
            type: "POST",
            data: { Product : Product, Year : Year, SlpCode: SlpCode, CardCode: CardCode },
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "No", class: "text-center border-start border-bottom" },
            { "data": "ItemCode", class: "text-center border-start border-bottom" },
            { "data": "ItemName", class: "border-start border-bottom" },
            { "data": "ProductStatus", class: "text-center border-start border-bottom" },
            { "data": "OnHand", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "UnitMsr", class: "text-center border-start border-bottom border-end" },
            <?php if($VisCost == true) { ?>
                { "data": "LastPurc", class: "dt-body-right border-start border-bottom border-end" },
                { "data": "LastDocDate", class: "text-center border-start border-bottom border-end" },
            <?php } ?>
            <?php for($m = 1; $m <= 12; $m++) { ?>
                { "data": "M<?php echo $m; ?>", class: "dt-body-right border-start border-bottom border-end" },
            <?php } ?>
        ],
        "columnDefs": [
        <?php if($VisCost == true) { ?>
            { "width": "2%", "targets": 0 },
            { "width": "6%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "2%", "targets": 3 },
            { "width": "4%", "targets": 4 },
            { "width": "3%", "targets": 5 },
            { "width": "4%", "targets": 6 },
            { "width": "6%", "targets": 7 },
            { "width": "4.8%", "targets": 8 },
            { "width": "4.8%", "targets": 9 },
            { "width": "4.8%", "targets": 10 },
            { "width": "4.8%", "targets": 11 },
            { "width": "4.8%", "targets": 12 },
            { "width": "4.8%", "targets": 13 },
            { "width": "4.8%", "targets": 14 },
            { "width": "4.8%", "targets": 15 },
            { "width": "4.8%", "targets": 16 },
            { "width": "4.8%", "targets": 17 },
            { "width": "4.8%", "targets": 18 },
            { "width": "4.8%", "targets": 19 },
        <?php } else { ?>
            { "width": "2%", "targets": 0 },
            { "width": "6%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "2%", "targets": 3 },
            { "width": "4%", "targets": 4 },
            { "width": "3%", "targets": 5 },
            { "width": "4.8%", "targets": 6 },
            { "width": "4.8%", "targets": 7 },
            { "width": "4.8%", "targets": 8 },
            { "width": "4.8%", "targets": 9 },
            { "width": "4.8%", "targets": 10 },
            { "width": "4.8%", "targets": 11 },
            { "width": "4.8%", "targets": 12 },
            { "width": "4.8%", "targets": 13 },
            { "width": "4.8%", "targets": 14 },
            { "width": "4.8%", "targets": 15 },
            { "width": "4.8%", "targets": 16 },
            { "width": "4.8%", "targets": 17 },
        <?php } ?>
        ],
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "ordering": false,
        // "dom": 'Bfrtip',
        // "buttons": [{ "extend": 'excelHtml5',"footer": true, },]
    });
    $(".overlay").hide();
}

function ViewData(ItemCode) {
    var Year = $("#Year").val();
    $.ajax({
        url: "menus/purchase/ajax/ajaxhisitem.php?a=ViewData",
        type: "POST",
        data: { ItemCode : ItemCode, Year : Year, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                var Tbody = "";
                if(inval['Row'] > 0) {
                    for(var r = 1; r <= inval['Row']; r++){
                        Tbody +="<tr>"+
                                    "<td class='text-center'>"+inval['Data']['No'][r]+"</td>"+
                                    "<td class='text-center'>"+inval['Data']['ItemCode'][r]+"</td>"+
                                    "<td>"+inval['Data']['ItemName'][r]+"</td>"+
                                    "<td>"+inval['Data']['CardName'][r]+"</td>"+
                                    "<td class='text-center'>"+inval['Data']['UnitMsr'][r]+"</td>"+
                                    "<td class='text-center'>"+inval['Data']['DocDate'][r]+"</td>"+
                                    "<td class='text-right'>"+inval['Data']['Qty'][r]+"</td>"+
                                    "<td class='text-right'>"+inval['Data']['Price'][r]+"</td>"+
                                    "<td class='text-right'>"+inval['Data']['LotalLine'][r]+"</td>"+
                                "</tr>";
                    }
                }else{
                    Tbody +="<tr><td colspan='9' class='text-center'>ไม่มีข้อมูล :(</td></tr>";
                }
                $("#TableDetail tbody").html(Tbody);
                $("#ModalViewData").modal("show");
            });
        }
    })
}

function Excel() {
    $(".overlay").show(); 
    let Product  = $("#Product").val();
    let Year     = $("#Year").val();
    let SlpCode  = $("#SlpCode").val();
    let CardCode = $("#CardCode").val();
    $.ajax({
        url: "menus/purchase/ajax/ajaxhisitem.php?a=Excel",
        type: "POST",
        data: { Product : Product, Year : Year, SlpCode, SlpCode, CardCode : CardCode, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/HisItem/"+inval['FileName'],'_blank');
            });
        }
    })
}
</script> 
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
                            <label for="YearSelect">เลือกปี</label>
                            <select class='form-select form-select-sm' name='YearSelect' id='YearSelect' onchange='CallData();'>
                                <?php
                                for($y = date("Y"); $y >= 2023; $y--) {
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
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for="MonthSelect">เลือกเดือน</label>
                            <select class='form-select form-select-sm' name='MonthSelect' id='MonthSelect' onchange='CallData();'>
                                <option value="ALL" selected>ทุกเดือน</option>
                                <?php
                                for($m = 1; $m <= 12; $m++) {
                                    if($m == date("m")) {
                                        echo "<option value='".$m."' selected>".FullMonth($m)."</option>";
                                    }else{
                                        echo "<option value='".$m."'>".FullMonth($m)."</option>";
                                    }
                                } 
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for="TeamSelect">เลือกทีม</label>
                            <select class='form-select form-select-sm' name='TeamSelect' id='TeamSelect' onchange='CallData();'>
                                <option value="ALL" selected>ทุกทีม</option>
                                <option value="MT1"><?php echo SATeamName("MT1"); ?></option>
                                <option value="MT2"><?php echo SATeamName("MT2"); ?></option>
                                <option value="TT2"><?php echo SATeamName("TT2"); ?></option>
                                <option value="TT1"><?php echo SATeamName("TT1"); ?></option>
                                <option value="OUL"><?php echo SATeamName("OUL"); ?></option>
                                <option value="ONL"><?php echo SATeamName("ONL"); ?></option>
                                <option value="KBI"><?php echo SATeamName("KBI"); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group" style='width: 250px;'>
                            <label for="ItemCode">เลือกสินค้า</label>
                            <select class='form-control form-control-sm' name='ItemCode' id='ItemCode' data-live-search="true" onchange='CallData();'>
                                <option value="ALL" selected>สินค้าทั้งหมด</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for="ItemStatus">เลือกสถานะสินค้า</label>
                            <select class='form-select form-select-sm' name='ItemStatus' id='ItemStatus' onchange='CallData();'>
                                <option value="ALL" selected>สถานะสินค้าทั้งหมด</option>
                                <option value="D">สถานะสินค้า D</option>
                                <option value="R">สถานะสินค้า R</option>
                                <option value="A">สถานะสินค้า A</option>
                                <option value="W">สถานะสินค้า W</option>
                                <option value="N">สถานะสินค้า N</option>
                                <option value="M">สถานะสินค้า M</option>
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
                            <?php 
                            $Chk = 0;
                            switch($_SESSION['DeptCode']){
                                case 'DP001': 
                                case 'DP002': 
                                case 'DP004': 
                                    $Chk = 1;
                                    break;
                                case 'DP003': 
                                    if($_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4) {
                                        $Chk = 1;
                                    }
                                    break;
                                case 'DP005': 
                                case 'DP006': 
                                case 'DP007': 
                                case 'DP008': 
                                    if($_SESSION['uClass'] == 18) {
                                        $Chk = 1;
                                    }
                                    break;
                                case 'DP009': 
                                    $Chk = 1;
                                    break;
                                default: break;
                            }
                            ?>
                            <table class='table table-sm table-hover' style='font-size: 12px;' id='Table1'>
                                <thead style='background-color: #9A1118; color: #fff;'>
                                    <tr>
                                        <th class='text-center border-top'>ลำดับ</th>
                                        <th class='text-center border-top'>ทีม</th>
                                        <th class='text-center border-top'>ชื่อลูกค้า</th>
                                        <th class='text-center border-top'>รหัสสินค้า</th>
                                        <th class='text-center border-top'>ชื่อสินค้า</th>
                                        <th class='text-center border-top'>สถานะ</th>
                                        <th class='text-center border-top'>คลังสินค้า</th>
                                        <th class='text-center border-top'>จำนวน</th>
                                        <th class='text-center border-top'>มูลค่า</th>
                                        <?php if($Chk == 1) { ?>
                                            <th class='text-center border-top'>ต้นทุน</th>
                                            <th class='text-center border-top'>วันที่เข้าล่าสุด</th>
                                        <?php } ?>
                                        <?php if($_SESSION['DeptCode'] == 'DP004' || $_SESSION['DeptCode'] == 'DP002') { ?>
                                            <th class='text-center border-top'>ซัพพลายเออร์</th>
                                        <?php }else{ ?>
                                            <th class='text-center border-top'>พนักงานขาย</th>
                                        <?php } ?>
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
    CallData();
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

function CallData() {
    let ItemCode    = $("#ItemCode").val();
    let ItemStatus  = $("#ItemStatus").val();
    let TeamSelect  = $("#TeamSelect").val();
    let YearSelect  = $("#YearSelect").val();
    let MonthSelect = $("#MonthSelect").val();
    $("#Table1").dataTable().fnClearTable();
    $("#Table1").dataTable().fnDraw();
    $("#Table1").dataTable().fnDestroy();
    $("#Table1").DataTable({
        "ajax": {
            url: "menus/purchase/ajax/ajaxbackorder.php?a=CallData",
            type: "POST",
            data: { ItemCode : ItemCode, ItemStatus : ItemStatus, TeamSelect : TeamSelect, YearSelect : YearSelect, MonthSelect : MonthSelect, },
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "No", class: "dt-body-center border-start border-bottom" },
            { "data": "Team", class: "dt-body-center border-start border-bottom" },
            { "data": "CardName", class: "border-start border-bottom" },
            { "data": "ItemCode", class: "dt-body-center border-start border-bottom" },
            { "data": "ItemName", class: "border-start border-bottom border-end" },
            { "data": "U_ProductStatus", class: "dt-body-center border-start border-bottom border-end" },
            { "data": "WhsCode", class: "dt-body-center border-start border-bottom border-end" },
            { "data": "OpenQty", class: "dt-body-right border-start border-bottom border-end" },
            { "data": "LineTotal", class: "dt-body-right border-start border-bottom border-end" },
            <?php if($Chk == 1) { ?>
                { "data": "LastPurPrc", class: "dt-body-right border-start border-bottom border-end" },
                { "data": "LastPurDat", class: "dt-body-center border-start border-bottom border-end" },
            <?php } ?>
            { "data": "VName", class: "border-start border-bottom border-end" },
        ],
        "columnDefs": [
            <?php if($Chk == 1) { ?>
                { "width": "2%", "targets": 0 },
                { "width": "3%", "targets": 1 },
                { "width": "21%", "targets": 2 },
                { "width": "6%", "targets": 3 },
                { "width": "25%", "targets": 4 },
                { "width": "3%", "targets": 5 },
                { "width": "5%", "targets": 6 },
                { "width": "4%", "targets": 7 },
                { "width": "6%", "targets": 8 },
                { "width": "6%", "targets": 9 },
                { "width": "7%", "targets": 10 },
                { "width": "21%", "targets": 11 },
            <?php }else{ ?>
                { "width": "2%", "targets": 0 },
                { "width": "5%", "targets": 1 },
                { "width": "21%", "targets": 2 },
                { "width": "8%", "targets": 3 },
                { "width": "25%", "targets": 4 },
                { "width": "5%", "targets": 6 },
                { "width": "8%", "targets": 5 },
                { "width": "6%", "targets": 7 },
                { "width": "8%", "targets": 8 },
                { "width": "21%", "targets": 9 },
            <?php } ?>
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

function Excel() {
    $(".overlay").show();
    let ItemCode    = $("#ItemCode").val();
    let ItemStatus  = $("#ItemStatus").val();
    let TeamSelect  = $("#TeamSelect").val();
    let YearSelect  = $("#YearSelect").val();
    let MonthSelect = $("#MonthSelect").val();
    $.ajax({
        url: "menus/purchase/ajax/ajaxbackorder.php?a=Excel",
        type: "POST",
        data: { ItemCode : ItemCode, ItemStatus : ItemStatus, TeamSelect : TeamSelect, YearSelect : YearSelect, MonthSelect : MonthSelect, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/BackOrder/"+inval['FileName'],'_blank');
            });
        }
    })
}
</script> 
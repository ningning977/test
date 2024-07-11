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
                            <label for="txtYear">เลือกปี</label>
                            <select class='form-select form-select-sm' name="txtYear" id="txtYear" onchange='GetSO04()'>
                                <?php 
                                for($y = date("Y"); $y >= 2023; $y--) {
                                    echo (($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>");
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="txtMonth">เลือกเดือน</label>
                            <select class='form-select form-select-sm' name="txtMonth" id="txtMonth" onchange='GetSO04()'>
                                <?php 
                                for($m = 1; $m <= 12; $m++) {
                                    echo (($m == date("m")) ? "<option value='$m' selected>".FullMonth($m)."</option>" : "<option value='$m'>".FullMonth($m)."</option>");
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='ExcelSO04()'><i class="fas fa-file-excel"></i>Excel</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive pt-2">
                    <table class='table table-sm table-bordered table-hover' id='TableSA04' style='font-size: 12px;'>
                        <thead>
                            <tr>
                                <th rowspan='2' width='3%' class='text-center'>No.</th>
                                <th rowspan='2' width='5%' class='text-center'>วันที่เอกสาร</th>
                                <th rowspan='2' width='8%' class='text-center'>เลขที่เอกสาร (SA-04)</th>
                                <th rowspan='2' width='21%' class='text-center'>ชื่อร้าน</th>
                                <th rowspan='2' width='10%' class='text-center'>Sales</th>
                                <th rowspan='2' width='13%' class='text-center'>Co-Sales</th>
                                <th rowspan='2' width='6%' class='text-center'>เลขที่บิล</th>
                                <th rowspan='2' width='4%' class='text-center'>ช่องทาง<br>การขาย</th>
                                <th rowspan='2' width='5%' class='text-center'>วันที่บิล</th>
                                <th rowspan='2' width='10%' class='text-center'>รายละเอียด</th>
                                <th colspan='3' class='text-center'>จำนวนที่ปรับ</th>
                            </tr>
                            <tr>
                                <th width='5%' class='text-center'>Sales<br>(100 บาท)</th>
                                <th width='5%' class='text-center'>Co-Sales<br>(20 บาท)</th>
                                <th width='5%' class='text-center'>ไม่มีค่าปรับ</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
        GetSO04();
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


function GetSO04() {
    const Year = $("#txtYear").val();
    const Month = $("#txtMonth").val();
    $.ajax({
        url: "menus/sale/ajax/ajaxreport_sa04.php?a=GetSO04",
        type: "POST",
        data: { Year: Year, Month: Month},
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                if(inval['Row'] != 0) {
                    $("#TableSA04 tbody").html(inval['Data']);
                }else{
                    $("#TableSA04 tbody").html("<tr><td colspan='13' class='text-center'>ไม่มีข้อมูล :(</td></tr>");
                }
            });
        }
    })
}

function ExcelSO04() {
    const Year = $("#txtYear").val();
    const Month = $("#txtMonth").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxreport_sa04.php?a=ExcelSO04",
        type: "POST",
        data: { Year : Year, Month : Month, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/SA04/"+inval['FileName'],'_blank');
            });
        }
    })
}
</script> 
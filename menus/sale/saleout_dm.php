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
        <div class="card mb-3">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <div class="form-goup">
                            <label for="uKey">เลือกเซลล์</label>
                            <select class='form-control form-control-sm' name='uKey' id='uKey' data-live-search="true" onchange='GetTarget()'>
                                <option value="" selected disabled>เลือกเซลล์</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-goup">
                            <label for="uTarget">เป้าขาย (บาท)</label>
                            <input type="number" class='form-control form-control-sm text-right' name="uTarget" id="uTarget" min='0' >
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-goup">
                            <label for="uSale">ยอดขาย (บาท)</label>
                            <input type="number" class='form-control form-control-sm text-right' name="uSale" id="uSale" min='0' >
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-goup">
                            <label for=""></label>
                            <button class='btn btn-sm btn-primary w-100' onclick='AddSaleOut();'><i class="fas fa-user-plus"></i> เพิ่ม</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TableListSaleOut'>
                                <thead>
                                    <tr class='text-center'>
                                        <th width='10%'>ลำดับ</th>
                                        <th>ชื่อ</th>
                                        <th width='15%'>เป้าขาย (บาท)</th>
                                        <th width='15%'>ยอดขาย (บาท)</th>
                                        <th width='10%'>คิดเป็น %</th>
                                        <th width='20%'>วันที่อัพเดตล่าสุด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan='5' class='text-center'>ไม่มีข้อมูล :(</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


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
    GetDM();
    ListSaleOut();
});

function GetDM() {
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleout_dm.php?a=GetDM",
        type: "GET",
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#uKey").append(inval['Data']).selectpicker();
            });
        }
    })
}

function GetTarget() {
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleout_dm.php?a=GetTarget",
        type: "POST",
        data: { uKey: $("#uKey").val() },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#uTarget").val(inval['SaleTarget']);
                $("#uSale").val(inval['SaleActual']);
            });
        }
    })
}

function AddSaleOut() {
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleout_dm.php?a=AddSaleOut",
        type: "POST",
        data: { uKey: $("#uKey").val(), Target: $("#uTarget").val(), Sale: $("#uSale").val() },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                ListSaleOut();
            });
        }
    })
}

function ListSaleOut() {
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleout_dm.php?a=ListSaleOut",
        type: "GET",
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#TableListSaleOut tbody").html((inval['Tbody'] != '') ? inval['Tbody'] : "<tr><td colspan='4' class='text-center'>ไม่มีข้อมูล :(</td></tr>");
            });
        }
    })
}

</script> 
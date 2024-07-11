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
                    <div class="col-lg-5">
                        <label for="ItemCode">เลือกสินค้า</label>
                        <select class='form-control form-control-sm' name="ItemCode" id="ItemCode" data-live-search="true">
                            <option value="" selected disabled>เลือกสินค้า</option>
                        </select>
                    </div>
                    <div class="col-lg-auto">
                        <label for=""></label>
                        <button class='btn btn-sm btn-primary w-100' onclick='GetItemData()'><i class="fas fa-search"></i> ค้นหา</button>
                    </div>
                </div>

                <div class="table-responsive pt-3">
                    <table class='table table-sm table-hover table-bordered' id='TableAddItem'>
                        <thead>
                            <tr class='text-center'>
                                <th width='20%'>รหัสสินค้า</th>
                                <th>ชื่อสินค้า</th>
                                <th width='10'></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan='3' class='text-center'>ไม่มีข้อมูล :(</td>
                            </tr>
                        </tbody>
                    </table>
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
    GetOITM();
});

function GetOITM() {
    $.ajax({
        url: "../json/OITM.json",
        cache: false,
        success: function(result) {
            var filt_data = 
                result.
                    filter(x => x.ItemStatus == "A").
                    filter(x => x.ItemCode.substr(0,3) != "00-").
                    sort(function(key, inval) {
                        return key.ItemCode.localeCompare(inval.ItemCode);
                    });
    
            var opt = "";
    
            $.each(filt_data, function(key, inval) {
                opt += "<option value='"+inval.ItemCode+"'>"+inval.ItemCode+" | ["+inval.ProductStatus+"] - "+inval.ItemName+" ["+inval.BarCode+"]</option>";
            });
            $("#ItemCode").append(opt).selectpicker();
    
            <?php if(isset($_GET['ItemCode'])) { ?>
                setTimeout(function(){
                    $("#ItemCode").selectpicker('destroy').val("<?php echo $_GET['ItemCode']; ?>").change().selectpicker();
                }, 2000);
            <?php } ?>
        }
    });
}

function GetItemData() {
    const ItemCode = $("#ItemCode").val();
    $.ajax({
        url: "menus/warehouse/ajax/ajaxadditem_sub.php?a=GetItemData",
        type: "POST",
        data: { ItemCode: ItemCode },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#TableAddItem tbody").html(inval['Data']);
            });
        }
    })
}

function AddItemCode() {
    const ItemCode = $("#ItemCode").val();
    const NewItemCode = $("#NewItemCode").val();
    const NewItemName = $("#NewItemName").val();
    if(NewItemName == "") {
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
        $("#alert_body").html("กรุณากรอกชื่อสินค้าก่อน");
        $("#alert_modal").modal("show");
        return;
    }else{
        $.ajax({
            url: "menus/warehouse/ajax/ajaxadditem_sub.php?a=AddItemCode",
            type: "POST",
            data: { ItemCode: ItemCode, NewItemCode: NewItemCode, NewItemName: NewItemName },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    GetItemData();
                    $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 60px;'></i>");
                    $("#alert_body").html("เพิ่มรหัสลูกสำเร็จ");
                    $("#alert_modal").modal("show");
                });
            }
        })
    }
}
</script> 
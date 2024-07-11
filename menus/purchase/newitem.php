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
                <form class="form">
                    <div class="row">
                        <div class="col-lg d-flex">
                            <div class="form-group" style='width: 230px;'>
                                <label for="">รหัสสินค้า <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="ItemCode" id="ItemCode" placeholder="รหัสสินค้า">
                                <input type="hidden" name="rowItem" id="rowItem">
                            </div>

                            <div class='align-self-center ps-2' style='width: 120px;'>
                                <button class='btn btn-sm btn-primary' type="button" style='margin-top: 10px;' onclick="GetItemCode()"><i class="fas fa-search"></i> ค้นหา</button>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-lg">
                            <div class="form-group mt-3">
                                <label for="ItemName">ชื่อสินค้า<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="ItemName" id="ItemName" disabled>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="form-group mt-3">
                                <label for="ItemName2">ชื่อสินค้า 2</label>
                                <input type="text" class="form-control form-control-sm" name="ItemName2" id="ItemName2" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg">
                            <div class="form-group mb-3">
                                <label for="BarCode">บาร์โค้ด<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="BarCode" id="BarCode" disabled>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="form-group mb-3">
                                <label for="BarCode2">บาร์โค้ด 2</label>
                                <input type="text" class="form-control form-control-sm" name="BarCode2" id="BarCode2" disabled>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="form-group mb-3">
                                <label for="BarCode3">บาร์โค้ด 3</label>
                                <input type="text" class="form-control form-control-sm" name="BarCode3" id="BarCode3" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg">
                            <div class="form-group mt-3">
                                <label for="MgrUnit">หน่วย<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="MgrUnit" id="MgrUnit" disabled>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="form-group mt-3">
                                <label for="ProductStatus">สถานะสินค้า<span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" name="ProductStatus" id="ProductStatus"  disabled>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg">
                            <div class="form-group mt-3">
                                <label for="DftWhsCode">คลังเริ่มต้น<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="DftWhsCode" id="DftWhsCode"  disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg mt-3 text-right">
                            <button type="button" class="btn btn-sm btn-primary" id='btnAddItemCode' onclick="AddItemCode()"><i class="fas fa-plus fa-fw"></i> เพิ่ม</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
	$(document).ready(function(){
        CallHeade();
	});
</script> 
<script type="text/javascript">
    function CallHeade(){
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

    function GetItemCode() {
        $("#BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #DftWhsCode").val("");
        $("#ProductStatus").val("").change();
        $(".overlay").show(); 
        if ($("#ItemCode").val() == "") {
            $("#BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #ProductStatus, #DftWhsCode").prop("disabled", true); 
            $("#BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #ProductStatus, #DftWhsCode").removeClass("is-valid is-invalid");
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณากรอกรหัสสินค้า");
            $("#alert_modal").modal('show');
            $("#ItemCode").removeClass("is-valid is-invalid").addClass("is-invalid");
        }else{
            $("#ItemCode").removeClass("is-invalid is-invalid");
            $.ajax({
                url: "menus/purchase/ajax/ajaxnewitem.php?a=GetItemCode",
                type: "POST",
                data: {ItemCode : $("#ItemCode").val(),},
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key,inval) {
                        if (inval['row'] == 1) {
                            $("#BarCode").val(inval['BarCode']);
                            $("#ItemName").val(inval['ItemName']);
                            $("#MgrUnit").val(inval['MgrUnit']);
                            $("#ProductStatus").html(inval['ProductStatus']);
                            $("#DftWhsCode").val(inval['DftWhsCode']);
                            $("#BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #ProductStatus, #DftWhsCode").removeAttr("disabled");
                            $("#BarCode, #ItemName, #MgrUnit, #ProductStatus, #DftWhsCode").removeClass("is-valid is-invalid");
                            $("#rowItem").val($("#ItemCode").val());
                            var ProductStatusSAP = inval['ProductStatusSAP'];
                            $("#ProductStatus").val(ProductStatusSAP).change();

                            if(inval['Chkrow'] == 0) {
                                $("#btnAddItemCode").html("<i class='fas fa-plus fa-fw'></i> เพิ่ม");
                            }else{
                                $("#btnAddItemCode").html("<i class='fas fa-save fa-fw'></i> อัพเดต");
                            }
                        }else{
                            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                            $("#alert_body").html("ไม่พบข้อมูลสินค้านี้ใน SAP");
                            $("#alert_modal").modal('show');
                            $("#BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #ProductStatus, #DftWhsCode").attr("disabled", true);
                            $("#BarCode, #ItemName, #MgrUnit, #ProductStatus, #DftWhsCode").removeClass("is-valid is-invalid");
                            $("#btnAddItemCode").html("<i class='fas fa-plus fa-fw'></i> เพิ่ม");
                        }
                    });
                }
            });
        }
        $(".overlay").hide();
    };

    function AddItemCode() {
        $(".overlay").show(); 
        var ItemCode = $("#ItemCode").val();
        var ItemName = $("#ItemName").val();
        var ItemName2 = $("#ItemName2").val();
        var BarCode = $("#BarCode").val();
        var BarCode2 = $("#BarCode2").val();
        var BarCode3 = $("#BarCode3").val();
        var MgrUnit = $("#MgrUnit").val();
        var ProductStatus = $("#ProductStatus").val();
        var DftWhsCode = $("#DftWhsCode").val();
        // rowItem คือค่า ItemCode ตอนกดค้นหา
        var rowItem = $("#rowItem").val();
        if (ItemCode != "" && ItemCode == rowItem) {
            var item = ["ItemName","BarCode","MgrUnit","ProductStatus","DftWhsCode"];
            var CheckItem = 0;
            for (var i = 0; i <= item.length; i++) {
                if ($("#"+item[i]).val() == "") {
                    $("#"+item[i]).removeClass("is-valid is-invalid").addClass("is-invalid");
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
                    $("#alert_modal").modal('show');
                    CheckItem++;
                }else{
                    $("#"+item[i]).removeClass("is-invalid is-invalid").addClass("is-valid");
                }
            }
            if (CheckItem == 0) {
                $.ajax({
                    url: "menus/purchase/ajax/ajaxnewitem.php?a=AddItemCode",
                    type: "POST",
                    data: { ItemCode      : ItemCode,
                            ItemName      : ItemName,
                            ItemName2     : ItemName2,
                            BarCode       : BarCode,
                            BarCode2      : BarCode2,
                            BarCode3      : BarCode3,
                            MgrUnit       : MgrUnit,
                            ProductStatus : ProductStatus,
                            DftWhsCode    : DftWhsCode,
                        },
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj, function(key,inval) {
                            $("#ItemCode, #BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #DftWhsCode, #rowItem").val("");
                            $("#ProductStatus").val("").change();
                            $("#BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #ProductStatus, #DftWhsCode").attr("disabled", true);
                            $("#BarCode, #ItemName, #MgrUnit, #ProductStatus, #DftWhsCode").removeClass("is-valid is-invalid");
                            $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-primary\" style='font-size: 70px;''></i>");
                            $("#alert_body").html(inval['note']);
                            $("#alert_modal").modal('show');
                        });
                    }
                })
            }
        }else{
            $("#BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #ProductStatus, #DftWhsCode").attr("disabled", true);
            $("#ItemCode, #BarCode, #ItemName, #MgrUnit, #ProductStatus, #DftWhsCode").removeClass("is-valid is-invalid");
            $("#BarCode, #BarCode2, #BarCode3, #ItemName, #ItemName2, #MgrUnit, #DftWhsCode, #rowItem").val("");
            $("#ProductStatus").val("").change();
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("ค้นหา/เปลี่ยนรหัสสินค้า<br>กรุณากดที่รูปแว่นขยายก่อน <i class='fas fa-search'>");
            $("#alert_modal").modal('show');
        }
        $(".overlay").hide();
    }
</script> 
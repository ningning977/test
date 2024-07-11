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
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary " id="List-tab" data-bs-toggle="tab" data-bs-target="#List" type="button" role="tab" aria-controls="List" aria-selected="false"><i class="fas fa-list fa-fw"></i> รายการใบสั่งผลิตสินค้า</button>
                                <button class="nav-link text-primary active" id="Add-tab" data-bs-toggle="tab" data-bs-target="#Add" type="button" role="tab" aria-controls="Add" aria-selected="false"><i class="fas fa-plus fa-fw"></i> เพิ่มใบสั่งผลิตสินค้า</button>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="row pt-4">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show " id="List" role="tabpanel" aria-labelledby="List-tab">
                            1
                        </div>
                        <div class="tab-pane fade show active" id="Add" role="tabpanel" aria-labelledby="Add-tab">
                            <!-- Step 1 -->
                            <div class='Step1'>
                                <h5>ส่วนที่ 1 ฝ่ายผู้ขอผลิตสินค้า</h5>
                                <div class="row pt-2">
                                    <div class="col-3 ">
                                        <div class='d-flex'>
                                            <div class="border border-bottom-0 " style='padding: 2px 3px; border-radius: 10px 10px 0px 0px;'>
                                                <input class="form-check-input" type="radio" value="1" name='Type' id="Type1">
                                            </div>
                                            <div class='border border-start-0 border-end-0 border-top-0' style='width: 100%'>&nbsp;</div>
                                        </div>
                                        <div class="border border-top-0 rounded-bottom p-1">
                                            <p class='fw-bolder mb-1'>1. งานประกอบสินค้า</p>
                                            <div class='p-1'>
                                                ประกอบสินค้าทุกประเภท เช่น เครื่องสีข้าว,
                                            </div>
                                            <div class='p-1'>
                                                เครื่องสับหญ้า, เครื่องบด เป็นต้น
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class='d-flex'>
                                            <div class="border border-bottom-0 " style='padding: 2px 3px; border-radius: 10px 10px 0px 0px;'>
                                                <input class="form-check-input" type="radio" value="2" name='Type' id="Type2">
                                            </div>
                                            <div class='border border-start-0 border-end-0 border-top-0' style='width: 100%'>&nbsp;</div>
                                        </div>
                                        <div class="border border-top-0 rounded-bottom p-1">
                                            <p class='fw-bolder mb-1'>2. เปลี่ยนแปลงสภาพสินค้า <small class='text-muted'>(ระบุได้มากกว่า 1 ข้อ)</small></p>
                                            <div class='p-1'>
                                                <input class="form-check-input Type2_Sub" type="checkbox" value="Y" name='Type2_Sub1' id="Type2_Sub1" disabled>&nbsp;เปลี่ยน/เพิ่ม กล่อง ลัง
                                                &nbsp;&nbsp;
                                                <input class="form-check-input Type2_Sub" type="checkbox" value="Y" name='Type2_Sub2' id="Type2_Sub2" disabled>&nbsp;เพิ่ม/ลด อุปกรณ์
                                                &nbsp;&nbsp;
                                                <input class="form-check-input Type2_Sub" type="checkbox" value="Y" name='Type2_Sub3' id="Type2_Sub3" disabled>&nbsp;แปลง/สลับตัว สินค้า
                                            </div>
                                            <div class='p-1'>
                                                <input class="form-check-input Type2_Sub" type="checkbox" value="Y" name='Type2_Sub4' id="Type2_Sub4" disabled>&nbsp;เปลี่ยนแปลงแพ็คเกจ บรรจุภัณฑ์ เช่น ติดสติกเกอร์ เฉพาะ จุดที่ผู้ร้องต้องการ
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class='d-flex'>
                                            <div class="border border-bottom-0 " style='padding: 2px 3px; border-radius: 10px 10px 0px 0px;'>
                                                <input class="form-check-input" type="radio" value="3" name='Type' id="Type3">
                                            </div>
                                            <div class='border border-start-0 border-end-0 border-top-0' style='width: 100%'>&nbsp;</div>
                                        </div>
                                        <div class="border border-top-0 rounded-bottom p-1">
                                            <p class='fw-bolder mb-1'>3. งานติดสติดเกอร์</p>
                                            <div class='p-1'>
                                                เปลี่ยนแปลงแพ็คเกจ บรรจุภัณฑ์ เช่น แก้ไข เปลี่ยนแปลง ติดสติกเกอร์ ชื่อรุ่น
                                            </div>
                                            <div class='p-1'>
                                                หรืออื่นๆที่เป็นงานสติกเกอร์ เท่านั้น
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class='bg-secondary bg-opacity-50'>

                                <div class="row">
                                    <div class="col-auto d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="Never" name='Never' id="Never">
                                        &nbsp;
                                        ไม่เคยสั่งผลิตมาก่อน&nbsp;<small class='text-muted'>(สินค้าใหม่)</small>
                                    </div>
                                    <div class="col d-flex align-items-center ">
                                        <input class="form-check-input" type="checkbox" value="Ever" name='Ever' id="Ever">
                                        &nbsp;
                                        เคยผ่านการผลิตมาแล้ว พร้อมแนบทะเบียนสินค้าสั่งผลิตเลขที่ : 
                                        &nbsp;
                                        <input type="text" class='form-control form-control-sm w-25' name='EverSub' id='EverSub' disabled>
                                    </div>
                                </div>

                                <hr class='bg-secondary bg-opacity-50'>

                                <div class="row">
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' style='font-size: 12px;' id='TB_AddData1'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th colspan='5'>ส่วนที่ 1 สินค้าที่เบิกเพื่อทำการเปลี่ยนแปลง</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <th width='5%'>ลำดับ</th>
                                                        <th width='15%'>รหัสสินค้า</th>
                                                        <th>รายการสินค้า <a href="javascript:void(0);" onclick='AddItem(1);'><i class="fas fa-plus-square fa-fw"></i></a></th>
                                                        <th width='15%'>จำนวน</th>
                                                        <th width='7%'>ลบ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan='4' class='text-center'>ไม่มีข้อมูล :(</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' style='font-size: 12px;' id='TB_AddData2'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th colspan='5'>ส่วนที่ 2 สินค้าเปลี่ยนแปลงเป็น</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <th width='5%'>ลำดับ</th>
                                                        <th width='15%'>รหัสสินค้า</th>
                                                        <th>รายการสินค้า <a href="javascript:void(0);" onclick='AddItem(2);'><i class="fas fa-plus-square fa-fw"></i></a></th>
                                                        <th width='15%'>จำนวน</th>
                                                        <th width='7%'>ลบ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan='4' class='text-center'>ไม่มีข้อมูล :(</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row pt-3">
                                    <div class="col d-flex justify-content-end ">
                                        <button class='btn btn-sm btn-primary' onclick="Step('Next',2);">ต่อไป <i class="fas fa-angle-right"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class='Step2 d-none'>
                                <h5>ส่วนที่ 2 ผู้ขอผลิต - ดำเนินการเรื่องสิทธ์โควต้า</h5>
                                <div class="row pt-2">

                                </div>

                                <div class="row pt-3">
                                    <div class="col d-flex justify-content-between">
                                        <button class='btn btn-sm btn-primary' onclick="Step('Return',1);"><i class="fas fa-angle-left"></i> กลับ</i></button>
                                        <button class='btn btn-sm btn-primary' onclick="Step('Next',3);">ต่อไป <i class="fas fa-angle-right"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class='Step3 d-none'>
                                <h5>ส่วนที่ 3 ฝ่าย PD กำหนด Spec รูปแบบงาน จัดทำคู่มือการผลิต</h5>
                                <div class="row pt-2">

                                </div>

                                <div class="row pt-3">
                                    <div class="col d-flex justify-content-between">
                                        <button class='btn btn-sm btn-primary' onclick="Step('Return',2);"><i class="fas fa-angle-left"></i> กลับ</i></button>
                                        <button class='btn btn-sm btn-primary' onclick="Step('Next',4);">ต่อไป <i class="fas fa-angle-right"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class='Step4 d-none'>
                                <h5>ส่วนที่ 4 ฝ่ายการตลาดพิจารณา</h5>
                                <div class="row pt-2">

                                </div>

                                <div class="row pt-3">
                                    <div class="col d-flex justify-content-between">
                                        <button class='btn btn-sm btn-primary' onclick="Step('Return',3);"><i class="fas fa-angle-left"></i> กลับ</i></button>
                                        <button class='btn btn-sm btn-primary' onclick="">บันทึก <i class="fas fa-angle-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class='modal fade' id='ModalAddItem' tabindex='-1' role='dialog' data-bs-backdrop='static' aria-hidden='true'>
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header pt-2 pb-2'>
                <h5 class='modal-title'></h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <input type="hidden" name='tmpAddItem1' id='tmpAddItem1'>
                <input type="hidden" name='tmpAddItem2' id='tmpAddItem2'>
                <div class="row">
                    <div class="col d-flex">
                        <div class="form-group" style='width: 65%;'>
                            <label for="ItemCode">เลือกสินค้า</label>
                            <select class='form-control form-control-sm' name='ItemCode' id='ItemCode' data-live-search="true" onchange=''>
                                <option value="" selected disabled>เลือกสินค้า</option>
                            </select>
                        </div>
                        <div style='width: 5%;'></div>
                        <div class="form-group" style='width: 30%;'>
                            <label for="Quantity">จำนวน</label>
                            <input type='number' class='form-control form-control-sm' name='Quantity' id='Quantity' min='1' onchange=''></input>
                        </div>
                    </div>
                </div>
            </div>
            <div class='modal-footer pt-1 pb-1'></div>
        </div>
    </div>
</div>      

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
            url: "menus/sale/ajax/ajaxproduction.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
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
                opt += "<option value='"+inval.ItemCode+"||"+inval.ItemName+"'>"+inval.ItemCode+" | ["+inval.ProductStatus+"] - "+inval.ItemName+" ["+inval.BarCode+"]</option>";
            });
            $("#ItemCode").append(opt).selectpicker();
        }
    });

    $("#Type1, #Type2, #Type3").change(function() {
        $(".Type1_Sub, .Type2_Sub").prop("disabled", true);
        $(".Type1_Sub, .Type2_Sub").prop("checked", false);
        switch($(this).val()) {
            case '1': 
                $(".Type1_Sub").prop("disabled", false);
            break;
            case '2': 
                $(".Type2_Sub").prop("disabled", false);
            break;
            case '3': 
            break;
        }
    });

    $("#Never, #Ever").change(function() {
        console.log($(this).val());
        switch($(this).val()) {
            case 'Never': 
                $("#EverSub").val("");
                $("#EverSub").prop("disabled", true);
                $("#Ever").prop("checked", false);
            break;
            case 'Ever':
                $("#EverSub").prop("disabled", false);
                $("#Never").prop("checked", false);
            break;
        }
    });

    function AddItem(Type) {
        $("#ItemCode").selectpicker('val', ['']);
        $("#Quantity").val(1);
        $("#ModalAddItem .modal-title").html("<i class='fas fa-plus-square' style='font-size: 15px;'></i>&nbsp;&nbsp;เพิ่มรายการสินค้า ส่วนที่ "+Type);
        $("#ModalAddItem .modal-footer").html(
            "<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button>"+
            "<button type='button' class='btn btn-primary btn-sm btn-saveitem' onclick='SaveItem("+Type+");'>บันทึก</button>"
        );
        $("#ModalAddItem").modal("show");
    }

    $("#ItemCode, #Quantity").on('keypress',function(e) {
        if(e.which == 13) {
            $(".btn-saveitem").click();
        }
    });

    function SaveItem(Type) {
        let ItemCode = "";
        let ItemName = "";
        console.log($("#ItemCode").val());
        if($("#ItemCode").val() != null) {
            const SplitItem = $("#ItemCode").val().split("||");
            ItemCode = SplitItem[0];
            ItemName = SplitItem[1];
        }
        const Quantity = $("#Quantity").val();
        console.log(ItemCode);
        if(ItemCode != "" && Quantity != "") {
            let Tbody = "";
            if($("#tmpAddItem"+Type).val() == "") {
                $("#tmpAddItem"+Type).val("1&&"+ItemCode+"&&"+ItemName+"&&"+Quantity);
                Tbody += 
                    "<tr>"+
                        "<td class='text-center'>1</td>"+
                        "<td class='text-center'>"+ItemCode+"</td>"+
                        "<td>"+ItemName+"</td>"+
                        "<td class='text-right'>"+Quantity+"</td>"+
                        "<td><a href='javascript:void(0);' onclick='DeleteItem("+Type+",\"1&&"+ItemCode+"&&"+ItemName+"&&"+Quantity+"\")'><i class='fas fa-trash fa-fw'></i></a></td>"+
                    "</tr>";
            }else{
                const tmpAddItem = $("#tmpAddItem"+Type).val();
                const GetItem = $("#tmpAddItem"+Type).val().split("||");
                const ItemDetail = GetItem[GetItem.length-1].split("&&");
                $("#tmpAddItem"+Type).val(tmpAddItem+"||"+(parseInt(ItemDetail[0])+1)+"&&"+ItemCode+"&&"+ItemName+"&&"+Quantity);
                const Item = $("#tmpAddItem"+Type).val().split("||");
                $.each(Item, function (key, val) {
                    let I = val.split("&&");
                    Tbody += 
                        "<tr>"+
                            "<td class='text-center'>"+(key+1)+"</td>"+
                            "<td class='text-center'>"+I[1]+"</td>"+
                            "<td>"+I[2]+"</td>"+
                            "<td class='text-right'>"+I[3]+"</td>"+
                            "<td><a href='javascript:void(0);' onclick='DeleteItem("+Type+",\""+val+"\")'><i class='fas fa-trash fa-fw'></i></a></td>"+
                        "</tr>";
                });
            }
            $("#TB_AddData"+Type+" tbody").html(Tbody);
            $("#ModalAddItem").modal("hide");
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
            $("#alert_body").html("กรุณากรอกข้อมูลให้ครบ");
            $("#alert_modal").modal("show");
        }
    }

    function DeleteItem(Type,Item) {
        const ArrayItem = $("#tmpAddItem"+Type).val().split("||");
        const RowItem = ArrayItem.length;
        let tmpAddItem = $("#tmpAddItem"+Type).val();
        if(parseInt(RowItem) == 1) {
            $("#tmpAddItem"+Type).val(tmpAddItem.replace(Item, ""));
        }else{
            if(ArrayItem[0] == Item) {
                $("#tmpAddItem"+Type).val(tmpAddItem.replace(Item+"||", ""));
            }else{
                $("#tmpAddItem"+Type).val(tmpAddItem.replace("||"+Item, ""));
            }
        }

        const ItemAdd = $("#tmpAddItem"+Type).val().split("||");
        let Tbody = "";
        if(ItemAdd.length == 1) {
            if(ItemAdd[0] == "") {
                Tbody += 
                    "<tr>"+
                        "<td colspan='5' class='text-center'>ไม่มีข้อมูล :(</td>"+
                    "</tr>";
            }else{
                $.each(ItemAdd, function (key, val) {
                    const I = val.split("&&");
                    Tbody += 
                        "<tr>"+
                            "<td class='text-center'>"+(key+1)+"</td>"+
                            "<td class='text-center'>"+I[1]+"</td>"+
                            "<td>"+I[2]+"</td>"+
                            "<td class='text-right'>"+I[3]+"</td>"+
                            "<td><a href='javascript:void(0);' onclick='DeleteItem("+Type+",\""+val+"\")'><i class='fas fa-trash fa-fw'></i></a></td>"+
                        "</tr>";
                });
            }
        }else{
            $.each(ItemAdd, function (key, val) {
                const I = val.split("&&");
                Tbody += 
                    "<tr>"+
                        "<td class='text-center'>"+(key+1)+"</td>"+
                        "<td class='text-center'>"+I[1]+"</td>"+
                        "<td>"+I[2]+"</td>"+
                        "<td class='text-right'>"+I[3]+"</td>"+
                        "<td><a href='javascript:void(0);' onclick='DeleteItem("+Type+",\""+val+"\")'><i class='fas fa-trash fa-fw'></i></a></td>"+
                    "</tr>";
            });
        }
        $("#TB_AddData"+Type+" tbody").html(Tbody);
    }

    function Step(Type,Page) {
        if(Type == 'Return') {
            $(".Step"+(Page+1)).addClass("d-none");
            $(".Step"+Page).removeClass("d-none");
        }else{
            $(".Step"+(Page-1)).addClass("d-none");
            $(".Step"+Page).removeClass("d-none");
        }
    }
</script> 
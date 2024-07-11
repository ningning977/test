function CallHead(){
    $(".overlay").show();
    var MenuCase = $('#HeadeMenuLink').val()
    $.ajax({
        url: "menus/human/ajax/ajaxsaleorder.php?p=head",//แก้ บรรทัดนี้ทุกครั้ง URL ajax เอง
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
function GETList() {
    $(".overlay").show();
    alert("Click!");
    $(".overlay").hide();
}

function GetCardCode(){
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetCardCode",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#CardCode").html(inval["output"]);
            });
            $("#CardCode").selectpicker("refresh");
        }
    });
    $(".overlay").hide();
}

function GetShippingType() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetShipping",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#ShippingType").html(inval["output"]);
            });
        }
    });
    $(".overlay").hide();
}

function GetSlpCode() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetSlpCode",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval){
                $("#SlpCode").html(inval["outputSlp"]);
            });
        }
    });
    $(".overlay").hide();
}

function CheckForm(StepNow,StepTo) {
    /* 1. สร้างตัวแปรที่จำเป็นต่อฟังก์ชั่น */
    /* 1.1 รับค่าหน้าปัจจุบัน และหน้าต่อไป */
    var Now = StepNow;
    var To  = StepTo;
    var ErrorPoint   = 0;
    var ErrorID      = [];
    var SuccessID    = [];
    var CheckID      = null;

    /* 2. ตรวจสอบว่าค่าที่ส่งมาเป็นค่าว่างหรือไม่ ถ้าใช่ให้ ErrorPoint + 1 ถ้าไม่ ไม่ต้องบวก ErrorPoint */
    /* 2.1 ระบุ ID ที่ต้องการเช็คค่าว่างของหน้านั้นๆ */
    switch(Now) {
        case "1": CheckID = ["CardCode", "AddressBillTo", "AddressShipto", "TaxType", "PaymentTerm", "DocDate", "SlpCode"]; break;
        default : CheckID = []; break;
    }
    /* 2.2 ตรวจสอบ CheckID ว่ามี ID ที่ต้องตรวจสอบหรือไม่ หากมี ให้ตรวจสอบ ถ้าไม่มีให้ SKIP */
    if(CheckID.length > 0) {
        for(let i = 0; i < CheckID.length; i++) {
            /* 2.3 ตรวจสอบค่าว่างของ ID ที่กำหนด ถ้าว่างให้ ErrorPoint +1 ถ้าไม่ว่างไม่ต้องบวก ErrorPoint */
            if($("#"+CheckID[i]).val() == null || $("#"+CheckID[i]).val() == "") {
                ErrorPoint = ErrorPoint+1;
                ErrorID.push(CheckID[i]);
            } else {
                SuccessID.push(CheckID[i]);
            }
        }
    }

    /* 3. ตรวจสอบค่า ErrorPoint ถ้ามากกว่า 0 ให้แสดงข้อความแจ้งเตือนข้อผิดพลาด ถ้าไม่ ให้แสดงผลหน้าต่อไป */
    if(ErrorPoint > 0) {
        for(let i = 0; i < ErrorID.length; i++) { $("#"+ErrorID[i]).removeClass("is-valid is-invalid").addClass("is-invalid"); }
        for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        switch(Now) {
            case "1":
                for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
                $("#order-step"+Now).hide();
                $("#order-step"+To).show();
                $("#TaxType").attr("disabled",true);
            break;
            case "2":
                var TotalTR = $("#ItemListData tr").length;
                if(TotalTR == 0) {
                    if (To == "1") {
                        $("#order-step"+Now).hide();
                        $("#order-step"+To).show();
                    }else{
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("กรุณาเพิ่มรายการสินค้าอย่างน้อย 1 รายการ");
                        $("#alert_modal").modal('show');
                    }
                } else {
                    OrderPreview();
                    $("#order-step"+Now).hide();
                    $("#order-step"+To).show();
                }
            break;
            default:
                $("#order-step"+Now).hide();
                $("#order-step"+To).show();
            break;
        }
        
    }
}

function AddItem() {
    $("#AddItem").on("click", function(e) {
        
        var SelectOption = $("#ItemSelect").html();
        $("#RowID").val(0);
        $("#ItemSelect").empty().selectpicker('destroy');
        $("#ItemSelect").html(SelectOption).selectpicker();
        $("#ItemWhse").empty();
        $("#ItemHistory").html("<p class=\"text-center text-muted\">กรุณาเลือกสินค้า และกรอกจำนวน</p>");
        $("#GrandPrice, #Discount, #PriceAfDisc, #ItemQuantity").val('');
        var HeaderAddItem = "<i class='fas fa-plus fa-fw fa-1x'></i> เพิ่มรายการใหม่";
        $("#HeaderModal").html(HeaderAddItem);
        var btnSave = "<i class='fas fa-plus fa-fw fa-1x'></i> เพิ่ม";
        $("#btn-AddRow").html(btnSave);
        $("#ModalAddItem").modal("show");
        $("input[type='checkbox']").prop('checked',false).removeAttr("disabled");
    });
}

function OldBill() {
    $("#OldBill").on("click", function(e) {
        $("#ModalOldBill").modal("show");
    });
}

function PullDataExcel() {
    $("#PullDataExcel").on("click", function(e){
        $("#ModalPullDataExcel").modal("show");
    });
}

function GetItemProduct() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetItemProduct",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#ItemSelect").html(inval["outputPro"])
            });
            // $("#ItemSelect").selectpicker("refresh");

            $("#ItemSelect").on("change",function(){
                $("#ItemQuantity").focus();
            });

            $("#ItemQuantity").on("keypress",function(e){
                var KeyBoard = e.key;
                if(KeyBoard === "Enter") {
                    var ItemCode = $("#ItemSelect").val();
                    var CardCode = $("#CardCode").val();
                    var Quantity = $(this).val();
                    if(ItemCode == null || CardCode == null || Quantity < 1) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("กรุณาเลือกชื่อลูกค้า รหัสสินค้า และจำนวนสินค้าให้ครบถ้วน");
                        $("#alert_modal").modal('show');
                    } else {
                        GetItemDetail(CardCode,ItemCode,Quantity);
                    }
                }
            });

            $("#btn-calprice").on("click",function(e){
                e.preventDefault();
                var ItemCode = $("#ItemSelect").val();
                var CardCode = $("#CardCode").val();
                var Quantity = $("#ItemQuantity").val();
                if(ItemCode == null || CardCode == null || Quantity < 1) {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("กรุณาเลือกชื่อลูกค้า รหัสสินค้า และจำนวนสินค้าให้ครบถ้วน");
                    $("#alert_modal").modal('show');
                } else {
                    GetItemDetail(CardCode,ItemCode,Quantity);
                }
            });
        }
    });
    $(".overlay").hide();
}

function GetItemDetail(CardCode,ItemCode,Quantity) {
    $(".overlay").show();
    $("#ItemHistory, #ItemWhse").empty();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetItemDetail",
        type: 'POST',
        data: { CardCode: CardCode, ItemCode: ItemCode, Quantity: Quantity },
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval){
                $("#ItemHistory").html(inval['History']);
                var uClass = '<?php echo $_SESSION['uClass']; ?>';
                switch(uClass) {
                    case "0":
                    case "9":
                    case "18":
                    case "19":
                    case "20": var ValDisabled = ["00","ACC","AGT","IMAX","JSI","KB9","KN","KS","KSM","KTW","NST","OSP","P01","P02","PLA","PO2","PU","RD","RD2","RD3","RD4","RD5","RST","RTR","SY","TC","TT-ขาย","VRK","W","WP","WP01","WP1","WP2","WP2.2","WP3","WP4","WP5","WP6","WP6-AGT","WP6-JSI","WP6-KN","WP6-KS","WP7","YEE","YMT","Z1","Z2","Z3TT2","Z4MT1","Z5MT2","Z6OUL"]; break;
                    default  : var ValDisabled = []; break;
                }
                // console.log(ValDisabled.length);
                $("#ItemWhse").html(inval['Warehouse']);
                if(ValDisabled.length > 0) {
                    for(var i = 0; i < ValDisabled.length; i++) {
                        $("select#ItemWhse option[value='"+ValDisabled[i]+"']").prop("disabled","disabled");
                    }
                }
                $("#ItemWhse").val(inval['DefWhse']).change();
                var TaxType = $("#TaxType").val();
                var DefaultPrice = 0;
                if(TaxType == "S07") {
                    DefaultPrice = (inval['DefaultPrice']/1.07).toFixed(3);
                } else {
                    DefaultPrice = inval['DefaultPrice'];
                }
                $("#GrandPrice, #PriceAfDisc, #Chk_DefaultPrice").val(DefaultPrice);
                $("#chk_price").prop('checked',false).removeAttr("readonly disabled");
                $("#GrandPrice").focus();
            });
        }
    });
    $(".overlay").hide();
}

function AddNewRow() {
    // 1.เอาค่า RowID ที่เรากำหนดค่าไว้ว่าเท่ากับ 0 ตั้งแต่กดเพิ่มรายการใหม่ (เมื่อกดจากปุ่มเพิ่มรายการใหม่) หรือ ค่าแถวที่ต้องการแก้ไข (เมื่อกดจากปุ่มแก้ไข) 
    var EditRow = $("#RowID").val();
    // 2.เก็บค่าที่มีใน ID เข้าไว้ในตัวแปร
    var ItemCode     = $("#ItemSelect").val();
    var ItemName     = $("#text_ItemName").val();
    var BarCode      = $("#text_BarCode").val();
    var UnitMsr      = $("#text_UnitMsr").val();
    var GrandPrice   = parseFloat($("#GrandPrice").val());
    var Discount     = $("#Discount").val();
    var PriceAfDisc  = parseFloat($("#PriceAfDisc").val());
    var ItemQuantity = $("#ItemQuantity").val();
    var ItemWhse     = $("#ItemWhse").val();
    var SPPrice      = "";
    var Convert      = "";
    var BackOrder    = "";
    var SPPrice_Icon      = "";
    var Convert_Icon      = "";
    var BackOrder_Icon    = "";
    // 3.ถ้า text_ItemStatus เป็นค่าว่าง ไม่ต้องเพิ่มค่าให้กับ ItemStatus ถ้า text_ItemStatus ไม่ใช่ค่าว่าง ให้เพิ่มค่าให้กับ text_ItemStatus ตามที่กำหนดไว้
    if($("#text_ItemStatus").val().length == 0) {
        var ItemStatus = null;
    } else {
        var ItemStatus = "["+$("#text_ItemStatus").val()+"]";
    }

    // 4.ถ้า ItemCode, GrandPrice, ItemQuantity, ItemWhse เป็นค่าว่างให้แจ้งเตือน error
    if (ItemCode == null || GrandPrice.length == 0 || (ItemQuantity.length == 0 || ItemQuantity < 1) || ItemWhse == null){
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    }else{
        if($("#chk_price").is(":checked")) {
            SPPrice_Icon = "<i class='fas fa-hand-holding-usd fa-fw fa-1x text-warning'></i> ";
            SPPrice      = "Y";
        } else {
            SPPrice_Icon = "<i class='fas fa-hand-holding-usd fa-fw fa-1x text-muted'></i> ";
            SPPrice      = "N";
        }
        if($("#chk_convert").is(":checked")) {
            Convert_Icon = "<i class='fas fa-retweet fa-fw fa-1x text-info'></i> ";
            Convert      = "Y";
        } else {
            Convert_Icon = "<i class='fas fa-retweet fa-fw fa-1x text-muted'></i> ";
            Convert      = "N";
        }
        if($("#chk_backorder").is(":checked")) {
            BackOrder_Icon = "<i class='fas fa-cart-arrow-down fa-fw fa-1x text-danger'></i>";
            BackOrder      = "Y";
        } else {
            BackOrder_Icon = "<i class='fas fa-cart-arrow-down fa-fw fa-1x text-muted'></i>";
            BackOrder      = "N";
        }
        // 4.1 ถ้า ItemCode, GrandPrice, ItemQuantity, ItemWhse ไม่ใชาค่าว่างให้ตรวจสอบเงื่อนไขค่าตัวแปร EditRow 
        if (EditRow == "0") {
            // 4.1.1 ถ้าเงื่อนไขเป็นจริง แปลงค่าสตริงจำนวนบรรทัดทั้งหมด (TotalRow) แปลงให้เป็นตัวเลขแล้วเก็บค่าไว้ในตัวแปร LastRow แล้วนำค่า LastRow+1 แล้วเก็บไว้ในตัวแปร RowID
            var LastRow = parseInt($("#TotalRow").val());
            var RowID   = LastRow+1;
            // 4.1.2 ผลคูณจำนวนสินค้า*ราคาสินค้า
            var LineTotal = ItemQuantity*PriceAfDisc;
            // 4.1.3 สร้างตัวแปร เก็บ โครงสร้าง HTML สำหรับบรรทัดที่ต้องการจะเพิ่ม โดยนำค่าที่ได้กลับไปหยอดใน input ที่เกี่ยวข้อง เพื่อให้สามารถนำข้อมูลกลับไปใช้ต่อในการแก้ไขรายการได้

            var NewRow = "<tr data-rowid='"+RowID+"'>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemRow_"+RowID+"' id='ItemRow_"+RowID+"' value='"+ItemCode+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemBarCode_"+RowID+"' id='ItemBarCode_"+RowID+"' value='"+BarCode+"' readonly></td>"+
                            "<td><input type='text' class='form-control-plaintext' name='ItemName_"+RowID+"' id='ItemName_"+RowID+"' value='"+ItemStatus+" "+ItemName+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemWhse_"+RowID+"' id='ItemWhse_"+RowID+"' value='"+ItemWhse+"' readonly></td>"+
                            "<td class='text-right'><input type='number' class='form-control-plaintext text-right' name='ItemQuantity_"+RowID+"' id='ItemQuantity_"+RowID+"' value='"+ItemQuantity+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext' name='ItemUnit_"+RowID+"' id='ItemUnit_"+RowID+"' value='"+UnitMsr+"' readonly></td>"+
                            "<td class='text-right'><input type='number' class='form-control-plaintext text-right' name='GrandPrice_"+RowID+"' id='GrandPrice_"+RowID+"' value='"+GrandPrice.toFixed(3)+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='Discount_"+RowID+"' id='Discount_"+RowID+"' value='"+Discount+"' readonly></td>"+
                            "<td class='text-right'><input type='number' class='form-control-plaintext text-right' name='PriceAfDisc_"+RowID+"' id='PriceAfDisc_"+RowID+"' value='"+PriceAfDisc.toFixed(3)+"' readonly></td>"+
                            "<td class='text-right'><input type='number' class='form-control-plaintext text-right' style='font-weight:bold;' name='LineTotal_"+RowID+"' id='LineTotal_"+RowID+"' value='"+LineTotal.toFixed(3)+"' readonly></td>"+
                            "<td class='text-center'>"+
                                "<span id='SPPriceIcon_"+RowID+"'>"+SPPrice_Icon+"</span>"+
                                "<span id='ConvertIcon_"+RowID+"'>"+Convert_Icon+"</span>"+
                                "<span id='BackOrderIcon_"+RowID+"'>"+BackOrder_Icon+"</span>"+
                            "</td>"+
                            "<td class='text-center'>"+
                                "<input type='hidden' id='input_spprice_"+RowID+"' name='input_spprice_"+RowID+"' value='"+SPPrice+"' readonly/>"+
                                "<input type='hidden' id='input_convert_"+RowID+"' name='input_convert_"+RowID+"' value='"+Convert+"' readonly/>"+
                                "<input type='hidden' id='input_backorder_"+RowID+"' name='input_backorder_"+RowID+"' value='"+BackOrder+"' readonly/>"+
                                "<button type='button' class='btn btn-secondary btn-sm' onclick='EditItem("+RowID+")'><i class='fas fa-edit fa-fw fa-1x'></i></button> "+
                                "<button type='button' class='btn btn-danger btn-sm' onclick='DeleteItem("+RowID+")'><i class='fas fa-trash fa-fw fa-1x'></i></button>"+
                            "</td>"+
                        "</tr>";
            // 4.1.4 นำค่า RowID ที่ได้จาก 4.1.1 มาหยอดกลับเข้า input ที่เก็บจำนวนบรรทัดทั้งหมด (TotalRow)
            $("#TotalRow").val(RowID);
            // 4.1.5 นำค่า NewRow ที่ได้จาก 4.1.3 ไปแสดงที่ ID:ItemListData โดยใช้ append ในการแสดงข้อมูลต่อๆกัน
            $("#ItemListData").append(NewRow);
        }else{
            // 4.2 ถ้าเงื่อนไขเป็นเท็จ ให้ update เก็บค่าเข้าไปใน ID ต่างๆ ตาม Row ที่เลือกแก้ไข
            var LineTotal = ItemQuantity*PriceAfDisc;
            $("#ItemRow_"+EditRow).val(ItemCode);
            $("#ItemBarCode_"+EditRow).val(BarCode);
            $("#ItemName_"+EditRow).val(ItemStatus+" "+ItemName);
            $("#ItemWhse_"+EditRow).val(ItemWhse);
            $("#ItemQuantity_"+EditRow).val(ItemQuantity);
            $("#ItemUnit_"+EditRow).val(UnitMsr);
            $("#GrandPrice_"+EditRow).val(GrandPrice.toFixed(3));
            $("#Discount_"+EditRow).val(Discount);
            $("#PriceAfDisc_"+EditRow).val(PriceAfDisc.toFixed(3));
            $("#LineTotal_"+EditRow).val(LineTotal.toFixed(3));
            $("#SPPriceIcon_"+EditRow).html(SPPrice_Icon);
            $("#ConvertIcon_"+EditRow).html(Convert_Icon);
            $("#BackOrderIcon_"+EditRow).html(BackOrder_Icon);
            $("#input_spprice_"+EditRow).val(SPPrice);
            $("#input_convert_"+EditRow).val(Convert);
            $("#input_backorder_"+EditRow).val(BackOrder);
        }
        // 4.2 ปิด modal
        $("#ModalAddItem").modal("hide");
    }
    GetDocTotal();
}

function EditItem (row) {
    /* รับค่าจาก Input ในแถวที่รับมาจาก row */
    var ItemCode = $("#ItemRow_"+row).val();
    var ItemWhse = $("#ItemWhse_"+row).val();
    var ItemQuantity = $("#ItemQuantity_"+row).val();
    var GrandPrice = $("#GrandPrice_"+row).val();
    var Discount = $("#Discount_"+row).val();
    var PriceAfDisc = $("#PriceAfDisc_"+row).val();
    var ChkPrice = $("#input_spprice_"+row).val();
    var ChkConvert = $("#input_convert_"+row).val();
    var ChkBackOrder = $("#input_backorder_"+row).val();

    // เก็บค่า row ไว้ใน ID:RowID
    $("#RowID").val(row);

    // เอาค่าเก็บใน ID เพื่อนำไปแสดงที่ modal
    $("#ItemSelect").selectpicker('destroy');
    $("#ItemSelect").val(ItemCode).change();
    $("#ItemSelect").selectpicker();

    $("#ItemQuantity").val(ItemQuantity);
    GetItemDetail($("#CardCode").val(),ItemCode,ItemQuantity);


    
    setTimeout(function() {
        var Chk_DP = $("#Chk_DefaultPrice").val();
        $("#GrandPrice").val(GrandPrice);
        $("#Discount").val(Discount);
        $("#PriceAfDisc").val(PriceAfDisc);
        $("#ItemWhse").val(ItemWhse).change();
        Chk_SPPrice(PriceAfDisc, Chk_DP);

        if(ChkPrice == 'Y') {
            $("#chk_price").prop('checked',true).attr('disabled',true);
        } else {
            $("#chk_price").prop('checked',false).removeAttr('disabled');
        }
        if(ChkConvert == 'Y') {
            $("#chk_convert").prop('checked',true);
        } else {
            $("#chk_convert").prop('checked',false);
        }
        if(ChkBackOrder == 'Y') {
            $("#chk_backorder").prop('checked',true);
        } else {
            $("#chk_backorder").prop('checked',false);
        }
    },500);

    // แสดง modal
    var HeadEdit = "<i class='far fa-edit fa-fw fa-1x'></i> แก้ไขข้อมูลรายการสินค้า";
    var btnSave = "<i class='far fa-save fa-fw fa-1x'></i> บันทึก";
    $("#HeaderModal").html(HeadEdit);
    $("#btn-AddRow").html(btnSave);
    $("#ModalAddItem").modal("show");
}

function DeleteItem (row) {
    // แสดง modal ยืนยันการลบข้อมูล
    $("#confirm_delete").modal("show");
    // นำค่าจาก row เก็บใน Attribut: data-rowid ของ ID:btn-del-confirm
    $("#btn-del-confirm").attr("data-rowid",row);

    // เมื่อมีการคลิก ปุ่ม ID:btn-del-confirm ให้เอาค่าแอททริบิว data-rowid เก็บไว้ในตัวแปร RowID แล้วลบแถวของตารางตาม ID ของของแถวนั้นๆ
    $("#btn-del-confirm").on("click",function(e){
        var RowID = $(this).attr("data-rowid");
        $("#ItemListData tr[data-rowid='"+RowID+"']").remove();
        GetDocTotal();
    });
}

function GetDocTotal() {
    var TotalRow = $("#TotalRow").val();
    var DiscountSum = $("#DiscountSum").val();
    var TotalPrice = 0.00;
    var TaxType = $("#TaxType").val();
    var VatSum = 0.00;
    for (var i = 1; i <= TotalRow; i++){
        var LineTotal = parseFloat($("#LineTotal_"+[i]).val()).toFixed(3);
        if(isNaN(LineTotal) == false) {
            TotalPrice = parseFloat(TotalPrice)+parseFloat(LineTotal);
        }
    }

    if(parseFloat(DiscountSum) > TotalPrice) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("ไม่สามารถกรอกส่วนลดให้มากกว่ายอดรวมทุกรายการได้");
        $("#alert_modal").modal('show');
        var DiscountSum = 0.00;
        $("#DiscountSum").val(DiscountSum.toFixed(3));
        GetDocTotal();
    } else{
        if (isNaN(DiscountSum) == false){
            DiscountSum = parseFloat(TotalPrice)-parseFloat(DiscountSum);
        }
        if(TaxType == "SNV") {
            VatSum = 0.00;
        } else {
            VatSum = parseFloat(DiscountSum)*(7/100);
        }
        var DocTotal = parseFloat(DiscountSum)+parseFloat(VatSum);
        $("#TotalPrice").val(TotalPrice.toFixed(3));
        $("#DocBefVat").val(DiscountSum.toFixed(3));
        $("#VatSum").val(VatSum.toFixed(3));
        $("#DocTotal").val(DocTotal.toFixed(3));
    }
}

function ClearDisc() {
    var DiscountSum = 0.00;
    $("#DiscountSum").val(DiscountSum.toFixed(3));
    GetDocTotal();
}

function OrderPreview() {
    $("#view_ItemList").empty();
    /* Order Header */  
    var view_CardCode      = $("#CardCode option:selected").text();
    var [Y_DocDate, M_DocDate, D_DocDate] = $("#DocDate").val().split('-');
    var view_DocDate       = ""+D_DocDate+"/"+M_DocDate+"/"+Y_DocDate+"";
    var [Y_DocDueDate, M_DocDueDate, D_DocDueDate] = $("#DocDueDate").val().split('-');
    var view_DocDueDate    = ""+D_DocDueDate+"/"+M_DocDueDate+"/"+Y_DocDueDate+"";
    var view_AddressShipTo = $("#AddressShipto option:selected").text();
    var view_AddressBillTo = $("#AddressBillTo option:selected").text();
    var view_LicTradeNum   = $("#LicTradeNum").val();
    var view_SlpName       = $("#SlpCode option:selected").text();
    var view_PONo          = $("#U_PONo").val();
    var view_TaxType       = $("#TaxType option:selected").text();
    var view_PaymentTerm   = $("#PaymentTerm option:selected").text();
    $("#view_CardCode").html(view_CardCode);
    $("#view_DocDate").html(view_DocDate);
    $("#view_DocDueDate").html(view_DocDueDate);
    $("#view_AddressShipTo").html(view_AddressShipTo.replace(" (ค่าเริ่มต้น)",""));
    $("#view_AddressBillTo").html(view_AddressBillTo.replace(" (ค่าเริ่มต้น)",""));
    $("#view_LicTradeNum").html(view_LicTradeNum);
    $("#view_SlpName").html(view_SlpName);
    $("#view_PONo").html(view_PONo);
    $("#view_TaxType").html(view_TaxType);
    $("#view_PaymentTerm").html(view_PaymentTerm);

    var TotalRow = $("#TotalRow").val();
    var No = 1;
    for (var i = 1; i <= TotalRow; i++) {
        var ItemRow_       = $("#ItemRow_"+[i]).val();
        var ItemBarCode_   = $("#ItemBarCode_"+[i]).val();
        var ItemWhse_      = $("#ItemWhse_"+[i]).val();
        var ItemName_      = $("#ItemName_"+[i]).val();
        var ItemQuantity_  = $("#ItemQuantity_"+[i]).val();
        var ItemUnit_      = $("#ItemUnit_"+[i]).val();
        var GrandPrice_    = parseFloat($("#GrandPrice_"+[i]).val());
        var Discount_      = $("#Discount_"+[i]).val();
        var LineTotal_     = parseFloat($("#LineTotal_"+[i]).val());
        if (ItemRow_ != undefined) {
            var ItemRow = "<tr>"+
                              "<td class='text-center'>"+No+"</td>"+
                              "<td>"+ItemRow_+" "+ItemBarCode_+" "+ItemWhse_+" "+ItemName_+"</td>"+
                              "<td width='5%' class='text-right'>"+ItemQuantity_+"</td>"+
                              "<td width='5%'>"+ItemUnit_+"</td>"+
                              "<td class='text-right'>"+GrandPrice_.toFixed(3)+"</td>"+
                              "<td class='text-center'>"+Discount_+"</td>"+
                              "<td class='text-right'>"+LineTotal_.toFixed(3)+"</td>"+
                          "</tr>"
            $("#view_ItemList").append(ItemRow);
            No++;
        } 
    }
    var view_DocRemark = $("#DocRemark").val();
    var view_TotalPrice = $("#TotalPrice").val();
    var view_DiscountSum = $("#DiscountSum").val();
    var view_DocBefVat = $("#DocBefVat").val();
    var view_VatSum = $("#VatSum").val();
    var view_DocTotal = $("#DocTotal").val();
    $("#view_DocRemark").html(view_DocRemark);
    $("#view_TotalPrice").html(view_TotalPrice);
    $("#view_DiscountSum").html(view_DiscountSum);
    $("#view_DocBefVat").html(view_DocBefVat);
    $("#view_VatSum").html(view_VatSum);
    $("#view_DocTotal").html(view_DocTotal);
}

function Chk_SPPrice(TotalPrice, DefaultPrice) {
    var PriceCheck = parseFloat(TotalPrice);
    var PriceDefault = parseFloat(DefaultPrice);
    if(PriceCheck < PriceDefault) {
        $("#chk_price").prop('checked',true).attr({ readonly: true, disabled: true});
    } else {
        $("#chk_price").prop('checked',false).removeAttr("readonly disabled");
    }
}
	$(document).ready(function(){
        CallHead();
        GetCardCode();
        GetSlpCode();
        GetShippingType()
        AddItem();
        OldBill();
        PullDataExcel();
        GetItemProduct();
	});

    // $("#order-step1, #order-step2").hide();
    $("#order-step2, #order-step3, #order-step4, #order-step5").hide();

    /* เมื่อเปลี่ยน TAB หัวข้อ */
    $(".btn-tabs").on("click",function(e){
        e.preventDefault();
        var tabno = $(this).attr("data-tabs");
        if(tabno == "1") {
            GETList();
        } else {
            $("#form_NewOrder").trigger("reset");
            $("#form_NewOrder").trigger("change");
        }
    });

    /* เมื่อกดปุ่ม ย้อนกลับ / ต่อไป */
    $(".btn-prev, .btn-next").on("click", function(e) {
        e.preventDefault();
        var StepNow  = $(this).attr("data-step");
        var StepGoto = $(this).attr("data-goto");
        CheckForm(StepNow,StepGoto);
    });

    /* Step 1 เมื่อเลือกชื่อร้านค้า */
    $("#CardCode").on("change", function() {
        /* 1. สร้างตัวแปรเก็บค่า Value จาก #CardCode */
        var CardCode = $(this).val();

        /* 2. Reset ข้อมูล Option ใน Select ที่อยู่ทั้งหมด */
        $("#AddressBillTo, #AddressShipto").empty();
        $("#AddressBillTo").html("<option value=''>กรุณาเลือกที่อยู่เปิดบิล</option>");
        $("#AddressShipto").html("<option value=''>กรุณาเลือกที่อยู่จัดส่ง</option>");

        /* 3. นำ Value ที่ได้จากข้อ 1 ไป Ajax */
        $.ajax({
            url: "menus/sale/ajax/ajaxsaleorder.php?p=GetAddress",
            type: "POST",
            data: {CardCode : CardCode},
            success: function(result){
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    /* 4. เอาข้อมูลมาหยอดใน ที่อยู่จัดเปิดบิล และที่อยู่จัดส่ง */
                    setTimeout(function() { $("#AddressBillTo").html(inval["outputB"]).change(); $("#AddressShipto").html(inval["outputS"]).change(); },500);
                    /* 5. หยอดข้อมูลพนักงานขาย */
                    var SaleCode = inval["outputSaleCode"];
                    var ShippingType = inval["outputShipping"];       
                    $("#SlpCode").val(SaleCode).change();
                    $("#ShippingType").val(ShippingType).change(); 
                    $("#LicTradeNum").val(inval["outputTaxID"]);  
                });
            }
        });
        /* 6. เปิด Input ที่เหลือให้กรอกได้ */
        $("#TaxType").val("S07").change();
        $("#PaymentTerm").val("CR").change();
        $("#AddressBillTo, #AddressShipto, #TaxType, #PaymentTerm, #DocDate, #DocDueDate, #U_PONo, #OrderAttach, #SlpCode, #ShippingType, #CODTotal").removeAttr("disabled");
        setTimeout(function() { $("#ShippingType").selectpicker(); },500);
    });

    $("#AddressBillTo").on("change", function() {
        var inputBillto = $("#AddressBillTo option:selected").text();
        $("#AddressBillTo_text").val(inputBillto);
    });

    $("#AddressShipto").on("change", function() {
        var inputShipto = $("#AddressShipto option:selected").text();
        $("#AddressShipto_text").val(inputShipto);
    });

    $("#SlpCode").on("change", function(){
        var inputSlpName = $("#SlpCode option:selected").text();
        $("#SlpName").val(inputSlpName);
    });

    $("#TaxType").on("change", function() {
        // $("#PaymentTerm").focus();
    });

    $("#PaymentTerm").on("change", function() {
        // $("#DocDate").focus();
    });

    /* Step 2 */
    $("#ItemSelect").on("change", function(){
        var ItemName = $("#ItemSelect option:selected").attr("data-ItemName");
        var BarCode = $("#ItemSelect option:selected").attr("data-BarCode");
        var ItemStatus = $("#ItemSelect option:selected").attr("data-ItemStatus");
        var UnitMsr = $("#ItemSelect option:selected").attr("data-UnitMsr");
        $("#text_ItemName").val(ItemName);
        $("#text_BarCode").val(BarCode);
        $("#text_ItemStatus").val(ItemStatus);
        $("#text_UnitMsr").val(UnitMsr);
    });

    $("#GrandPrice , #Discount").focusout(function() {
        var GrandPrice = $("#GrandPrice").val();
        var Discount   = $("#Discount").val();
        var Chk_DP     = $("#Chk_DefaultPrice").val();

        if(GrandPrice.length == 0) {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณากรอกราคาให้ถูกต้อง");
            $("#alert_modal").modal('show');
        }

        if(Discount.length > 0) {
            var DiscPrefix = Discount.charAt(0);
            if(DiscPrefix != '*') {
                /* 1. ตรวจสอบรูปแบบของค่าที่รับมา (ตัวเลข และเครื่องหมายบวก) */
                var pattern =  /^[0-9-.]+$/;
                var result = pattern.test(Discount);
                /* 1.1 ถ้าไม่ใช่ ให้แจ้งเตือน "กรุณากรอกส่วนลดให้ถูกต้อง" ถ้าใช่ ไป ข้อ 2. */
                if (result == false) {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พอข้อผิดพลาด!");
                    $("#alert_body").html("กรุณากรอกส่วนลดให้ถูกต้อง<br/>(ใช้ตัวเลข และเครื่องหมายลบ (-) คั่นส่วนลดระหว่าง STEP ได้เท่านั้น)");
                    $("#alert_modal").modal('show');
                } else {
                    /* 2. แบ่งตัวเลขเข้า Array */
                    var disStep = Discount.split("-");
                    var errorPoint = 0;
                    var stepPrice = GrandPrice;
                    var conDisStep = 0;
                    /* disStep = [50,2,3]; */
                    /* 3. ตรวจสอบส่วนลดแต่ละ Step ว่าเกิน 100 หรือไม่? ถ้าใช่ (>= 100) ให้แจ้งเตือน "กรุณาระบุส่วนลดให้ถูกต้อง" */
                    if(disStep.length <= 5) {
                        for (var i = 0; i < disStep.length; i++) {
                            conDisStep = conDisStep+parseInt(disStep[i]);   
                        }
                        if (conDisStep > 100.00) {
                            errorPoint++;
                        }
                        if (errorPoint > 0) {
                            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พอข้อผิดพลาด!");
                            $("#alert_body").html("กรุณากรอกส่วนลดให้ถูกต้อง<br/>(ส่วนลดต้องไม่เกิน 100%)");
                            $("#alert_modal").modal('show');
                        }else{
                            for (var i = 0; i < disStep.length; i++) {
                                var conDisStep = parseInt(disStep[i]);
                                var stepDiscount = stepPrice*(disStep[i]/100);
                                stepPrice = stepPrice - stepDiscount;
                            }
                            $("#PriceAfDisc").val(stepPrice.toFixed(3));
                            
                            Chk_SPPrice(stepPrice, Chk_DP);
                            
                        } 
                    } else {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พอข้อผิดพลาด!");
                        $("#alert_body").html("กรุณากรอกส่วนลดต้องไม่เกิน 4 สเต็ป");
                        $("#alert_modal").modal('show');
                    }
                }  
            } else {
                /* 1. ตรวจสอบรูปแบบของค่าที่รับมา (ตัวเลข และเครื่องหมายลบ) */
                var pattern =  /^[0-9*.]+$/;
                var result = pattern.test(Discount);
                if (result == false) {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พอข้อผิดพลาด!");
                    $("#alert_body").html("กรุณากรอกส่วนลดให้ถูกต้อง<br/>(ระบุจำนวนส่วนลดหลังเครื่องหมายดอกจันทร์ (*) เท่านั้น)");
                    $("#alert_modal").modal('show');
                } else {
                    var DiscAmount = parseFloat(Discount.substring(1));
                    stepPrice = GrandPrice-DiscAmount;
                    if(DiscAmount <= GrandPrice) {
                        $("#PriceAfDisc").val(stepPrice.toFixed(3));
                        Chk_SPPrice(stepPrice, Chk_DP);
                    } else {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พอข้อผิดพลาด!");
                        $("#alert_body").html("กรุณากรอกส่วนลดให้ถูกต้อง<br/>(ส่วนลดต้องไม่เกินราคาขาย)");
                        $("#alert_modal").modal('show');
                    }
                }
            }
        } else {
            $("#PriceAfDisc").val(GrandPrice);
            if(GrandPrice > 0) {
                Chk_SPPrice(GrandPrice, Chk_DP);
            }
        }
    });

    $("#btn-AddRow").on("click",function(e){
        e.preventDefault();
        AddNewRow();
    });

    
<div class="card mb-3">
    <div class="card-header">
        <h4><i class="fas fa-warehouse fa-fw fa-1x"></i> เช็คสต็อกออนไลน์</h4>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-12">
                <select class="selectpicker me-2 form-control" data-live-search="true" name='CheckStockItem' id="CheckStockItem" onchange="GetInStock()"></select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <table class="table">
                    <tbody>
                        <tr>
                            <td width="60%">โควต้าทีม <span id="Qty_Team"></span></td>
                            <td class="text-right" width="40%"> <span class="text-primary" style="font-weight: bold;" id="Qty_A">0</span></td>
                        </tr>
                        <tr>
                            <td>สินค้าพร้อมขายส่วนกลาง</td>
                            <td class="text-right"> <span class="text-primary" style="font-weight: bold;" id="Qty_B">0</span></td>
                        </tr>
                        <tr>
                            <td>คลังสินค้ามือสองส่วนกลาง</td>
                            <td class="text-right"> <span class="text-primary" style="font-weight: bold;" id="Qty_C">0</span></td>
                        </tr>
                        <tr>
                            <td>คลังสินค้ามือสองทีม</td>
                            <td class="text-right"> <span class="text-primary" style="font-weight: bold;" id="Qty_D">0</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        ChangeStockItem();
    });

    function ChangeStockItem() {
        var StockItem = $("#CheckStockItem").val();
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=ChangeStockItem",
            type: "POST",
            data: { StockItem : StockItem, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    // เช็คสต๊อกออนไลน์
                    var form_select = "<option selected disabled>กรุณาเลือกรายการสินค้า</option>";
                    for(var i = 1; i <= inval['RowStock']; i++) {
                        form_select += "<option value='"+inval['Stock']['ItemCode'][i]+"'>"+inval['Stock']['ItemName'][i]+"</option>";
                    }
                    $("#CheckStockItem").html(form_select);
                    $("#CheckStockItem").selectpicker("refresh");
                });
            }
        })
    }

    function GetInStock() {
        var ItemCode = $("#CheckStockItem").val();
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=GetInStock",
            type: "POST",
            data : { 
                ItemCode: ItemCode
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#Qty_Team").html(inval['Qty_Team']);
                    $("#Qty_A").html(inval['Qty_A']);
                    $("#Qty_B").html(inval['Qty_B']);
                    $("#Qty_C").html(inval['Qty_C']);
                    $("#Qty_D").html(inval['Qty_D']);
                });
            }
        });
    }
</script>
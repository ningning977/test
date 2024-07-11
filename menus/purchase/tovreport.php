<style type="text/css">
    button.btnSort {
        border: 0px;
        background: rgba(0,0,0,0);
        color: #fff;
        padding: 0;
    }

    @media only screen and (max-width:820px) {
        .tableFix {
            overflow-y: auto;
            height: 800px;
        }
    }

    @media (min-width:821px) and (max-width: 1180px) {
        .tableFix {
            overflow-y: auto;
            height: 500px;
        }
    }

    @media (min-width:1181px) {
        .tableFix {
            overflow-y: auto;
            height: 620px;
        }
    }

    .tableFix table.table {
        border-collapse: collapse;
    }

    .tableFix thead tr th {
        box-shadow: inset 0.5px 0.5px #eee, 0 0.5px #eee;
        position: sticky;
        top: 0;
    }

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
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="filt_ItemType">เลือกประเภทสินค้า</label>
                            <select class="form-select form-select-sm" name="filt_ItemType" id="filt_ItemType">
                                <option value="1">สินค้าทั้งหมด</option>
                                <option value="2">สินค้าอะไหล่</option>
                                <option value="3">ไม่รวมอะไหล่</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button class="btn btn-sm btn-primary w-100" id="btn_search" type="button" onclick="TOVData(0);"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</button>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="btn_export">&nbsp;</label>
                            <button class="btn btn-sm btn-success w-100" id="btn_export" type="button" onclick="ExportData();"><i class="fas fa-file-excel fa-fw fa-1x"></i> Excel</button>
                            <input type="hidden" name='SortType' id='SortType' value='0'>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="filt_search">ค้นหา:</label>
                            <input type="text" id="filt_search" class="form-control form-control-sm" placeholder="กรุณากรอกเพื่อค้นหา..." />
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="table-responsive tableFix">
                            <table class="table table-bordered table-hover table-sm" id="TOVList" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="5%">No.</th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center">
                                            รายการสินค้า<br/>
                                            ** เฉพาะรายการสินค้าที่มีความเคลื่อนไหวภายใน 12 เดือนล่าสุด **<br/>
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="ItemCode#ASC"><i class="fas fa-chevron-up"></i>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="ItemCode#DESC"><i class="fas fa-chevron-down"></i>
                                        </th>
                                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP010") { ?>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center">ตัวแทนจำหน่าย</th>
                                    <?php } ?>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center">หน่วย</th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="5%">สถานะสินค้า</th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="7.5%">
                                            ยอดขาย 12 เดือน<br/>ย้อนหลัง<br/>
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="Qty#ASC"><i class="fas fa-chevron-up"></i>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="Qty#DESC"><i class="fas fa-chevron-down"></i>
                                        </th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="6.5%">
                                            ยอดขายเฉลี่ย<br/>ต่อเดือน<br/>
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="AvgQty#ASC"><i class="fas fa-chevron-up"></i>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="AvgQty#DESC"><i class="fas fa-chevron-down"></i>
                                        </th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="6.5%">
                                            จำนวนสินค้า<br/>คงเหลือ<br/>
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="OnHand#ASC"><i class="fas fa-chevron-up"></i>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="OnHand#DESC"><i class="fas fa-chevron-down"></i>
                                        </th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="5%">
                                            T/O<br/>ปัจจุบัน<br/>
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="TOV#ASC"><i class="fas fa-chevron-up"></i>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="TOV#DESC"><i class="fas fa-chevron-down"></i>
                                        </th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="6.5%">วันที่คาดว่า<br/>สินค้าหมด</th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="6.5%">จำนวนสั่งซื้อ<br/>ในระบบ</th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="6.5%">จำนวนสั่งซื้อ<br/>ที่ต้องการ</th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="5%">
                                            Re Order<br/>Point<br/>
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="ROP#ASC"><i class="fas fa-chevron-up"></i>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="ROP#DESC"><i class="fas fa-chevron-down"></i>
                                        </th>
                                        <th style="background-color: #9A1118; color: #FFFFFF;" class="text-center" width="5%">
                                            T/O<br/>เมื่อของเข้า<br/>
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="EstTOV#ASC"><i class="fas fa-chevron-up"></i>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn btnSort btn-sm" data-Sort="EstTOV#DESC"><i class="fas fa-chevron-down"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="14" class="text-center">กรุณากดปุ่มค้นหาก่อน :)</td>
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

function TOVData(SortType) {
    var ItemType = $("#filt_ItemType").val();
    var SortType = SortType;
    $(".overlay").show();
    $.ajax({
        url: "menus/purchase/ajax/ajaxtovreport.php?p=GetData",
        type: "POST",
        data: {
            ItemType: ItemType,
            SortType: SortType
        },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            var tbody = "";
            $.each(obj, function(key,inval) {
                var Rows = parseFloat(inval['Rows']);
                if(Rows != 0) {
                    for(i = 0; i < Rows; i++) {
                        tbody +=
                            "<tr style='"+inval[i]['RowStyle']+"'>"+
                                "<td class='text-right'>"+inval[i]['No']+"</td>"+
                                "<td>"+inval[i]['ItemDscription']+"</td>";
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP010") { ?>
                                    tbody +="<td>"+inval[i]['Vendor']+"</td>";
                                <?php } ?>
                        tbody +="<td>"+inval[i]['SaleUnitMsr']+"</td>"+
                                "<td class='text-center'>"+inval[i]['ProductStatus']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty']+"</td>"+
                                "<td class='text-right'>"+inval[i]['AvgQty']+"</td>"+
                                "<td class='text-right'>"+inval[i]['OnHand']+"</td>"+
                                "<td style='"+inval[i]['TOVStyle']+"'>"+inval[i]['TOVText']+"</td>"+
                                "<td class='text-center'>"+inval[i]['EmptyDate']+"</td>"+
                                "<td class='text-right'>"+inval[i]['OnOrder']+"</td>"+
                                "<td class='text-right'>"+inval[i]['NewOrder']+"</td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+inval[i]['ROP']+"</td>"+
                                "<td style='"+inval[i]['EstTOVStyle']+"'>"+inval[i]['EstTOVText']+"</td>"+
                            "</tr>";
                    }
                }else{
                    tbody = "<tr><td <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP010") { echo "colspan='14'"; }else{ echo "colspan='13'"; } ?>>ไม่มีข้อมูล :(</td></tr>";
                }
                $("#TOVList tbody").html(tbody);
            });
        }
    });
}

function ExportData() {
    var ItemType = $("#filt_ItemType").val();
    var SortType = $("#SortType").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/purchase/ajax/ajaxtovreport.php?p=ExportData",
        type: "POST",
        data: { ItemType : ItemType, SortType : SortType, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/TOV/"+inval['FileName'],'_blank');
            });
        }
    })
}

$(document).ready(function(){
    CallHead();

    $("button.btnSort").on("click",function() {
        var SortType = $(this).attr("data-Sort");
        $("#SortType").val(SortType);
        TOVData(SortType);
    });

    $("#filt_search").on("keyup", function(){
        var value = $(this).val().toLowerCase();
        $("#TOVList tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script> 
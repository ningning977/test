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
                <div class="row pb-2">
                    <div class="col-sm-12 col-lg-3">
                        <span class='fw-bolder'>เลือกลูกค้า</span>
                        <select class="form-control form-control-sm" id="CardCode" data-live-search="true" onchange="SelectData();">
                            <option value="" selected disabled>กรุณาเลือกลูกค้า</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-lg-3">
                        <span class='fw-bolder'>เลือกสินค้า</span>
                        <select class="form-control form-control-sm" id="ItemCode" data-live-search="true" onchange="SelectData();">
                            <option value="" selected disabled>กรุณาเลือกสินค้า</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <span class='fw-bolder'>ระหว่างวันที่</span>
                        <div class='d-flex align-items-center'>
                            <input type="date" id='StartDate' value='<?php echo date("Y")."-01-01"; ?>' class="form-control form-control-sm" style='width: 120px;' onchange="SelectData();">  
                            <span class='text-primary ps-2 pe-2'>ถึง</span>             
                            <input type="date" id='EndDate' value='<?php echo date("Y-m-d"); ?>' class="form-control form-control-sm" style='width: 120px;' onchange="SelectData();">   
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive pt-1" >
                            <table class="table table-borderless rounded rounded-3 overflow-hidden" style='background-color: rgba(155, 0, 0, 0.04);' id='TheadCardCode'>
                                <thead style='background-color: rgba(136, 0, 0, 0.70);'>
                                    <tr>
                                        <td colspan='6' class='text-light'><div class='d-flex align-items-center'><i class="fas fa-user-alt"></i>&nbsp;ข้อมูลลูกค้า</div></td>
                                    </tr>
                                </tdead>
                                <tbody style='font-size: 13.5px;'>
                                    <?php
                                        $Head = ["รหัสลูกค้า",   "ผู้แทนขาย",           "เครดิต",
                                                 "ชื่อลูกค้า",    "รหัสประจำตัวผู้เสียภาษี", "วงเงินเครดิต",
                                                 "กลุ่มลูกค้า",   "เงื่อนไข",            "วงเงินคงเหลือ",
                                                 "เบอร์โทรศัพท์","ที่อยู่"]; 
                                        $n = 0;
                                        $c = "";
                                        for($r = 1; $r <= 4; $r++) {
                                            if($r <= 3){
                                                if($r == 1){ $c = "pt-3"; }else{ $c = ""; }
                                                echo "<tr>";
                                                    echo "<th class='".$c."'>".$Head[$n]."</th>"; $n++;
                                                    echo "<th class='".$c."'></th>";
                                                    echo "<th class='".$c."'>".$Head[$n]."</th>"; $n++;
                                                    echo "<th class='".$c."'></th>";
                                                    echo "<th class='".$c."'>".$Head[$n]."</th>"; $n++;
                                                    echo "<th class='".$c."'></th>";
                                                echo "</tr>";
                                            }else{
                                                echo "<tr>";
                                                    echo "<th>".$Head[$n]."</th>"; $n++;
                                                    echo "<th></th>";
                                                    echo "<th>".$Head[$n]."</th>"; $n++;
                                                    echo "<th></th>";
                                                echo "</tr>";
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row pt-3"> <!-- Tab Menus -->
                    <div class="col-lg">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-tab='tabs1' id='IDtabs1' href="#tabs1">รายการการขายสินค้า</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link " data-bs-toggle="tab" data-tab='tabs2' id='IDtabs2' href="#tabs2">ยอดขายของร้านค้า</button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row pt-3"> <!-- Tabs Content -->
                    <div class="col-lg">
                        <div class="tab-content">
                            <div id="tabs1" class="tab-pane active">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class='table-responsive'>
                                            <table class='table table-sm table-bordered' id='TableDetail'>
                                                <thead class='text-center' style='font-size: 13px;'>
                                                    <tr>
                                                        <th colspan='5' class='text-center border-top border-start border-end'>รายการการขายสินค้า <span id='H_TB'></span></th>
                                                    </tr>
                                                    <tr>
                                                        <th width='18%' class='text-center border-start border-bottom'>เลขที่เอกสาร</th>
                                                        <th width='15%' class='text-center border-start border-bottom'>วันที่</th>
                                                        <th width='15%' class='text-center border-start border-bottom'>กำหนดชำระเงิน</th>
                                                        <th class='text-center border-start border-bottom'>พนักงานขาย</th>
                                                        <th width='20%' class='text-center border-start border-bottom border-end'>เลขที่ P/O</th>
                                                    </tr>
                                                </thead>
                                                <tbody style='font-size: 12px;'>
                                                    <?php for($l = 1; $l <= 4; $l++) { ?>
                                                    <tr>
                                                        <td class='border-bottom'>&nbsp;</td>
                                                        <td class='border-bottom'>&nbsp;</td>
                                                        <td class='border-bottom'>&nbsp;</td>
                                                        <td class='border-bottom'>&nbsp;</td>
                                                        <td class='border-bottom'>&nbsp;</td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class='d-flex justify-content-end pt-1' style='padding-bottom: 7.5px;'>
                                            <input type="hidden" id='DocEntry'>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="PrintTax();"><i class="fas fa-print fa-fw fa-1x" aria-hidden="true"></i> พิมพ์</button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sm" id="view_Table" style="font-size: 12px;">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th width="5%">ลำดับ</th>
                                                        <th width="10%">รหัสสินค้า</th>
                                                        <th>ชื่อสินค้า</th>
                                                        <th width="10%">จำนวน</th>
                                                        <th width="5%">หน่วยนับ</th>
                                                        <th width="10%">ราคา/หน่วย</th>
                                                        <th width="15%">ส่วนลด</th>
                                                        <th width="10%">มูลค่าสินค้า</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td>
                                                    </tr>
                                                </tbody>
                                                <tfoot class="text-right">
                                                    <tr>
                                                        <th colspan="7">ราคาก่อนหักภาษีมูลค่าเพิ่ม</th>
                                                        <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_DocTotal" id="view_DocTotal" readonly /></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="7">ภาษีมูลค่าเพิ่ม</th>
                                                        <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_VatSum" id="view_VatSum" readonly /></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="7">ราคาสุทธิ</th>
                                                        <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_SumTotal" id="view_SumTotal" style="font-weight: bold;" readonly /></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="tabs2" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden' id='table_trab2'>
                                                <thead class='text-center' style='background-color: rgba(155, 0, 0, 0.04); font-size: 13px;'>
                                                    <tr>
                                                        <th colspan='15'>ยอดขายรายเดือนของร้านค้า (ปีปัจจุบันและย้อนหลัง 1 ปี)</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%'>ปี</th>
                                                        <?php 
                                                            for($m = 1; $m <= 12; $m++){
                                                                echo "<th width='6.33%'>".txtMonth($m)."</th>";
                                                            }
                                                        ?>
                                                        <th width='8%'>รวมทั้งหมด</th>
                                                        <th width='8%'>เฉลี่ยต่อเดือน</th>
                                                    </tr>
                                                </thead>
                                                <tbody style='font-size: 12px;'>
                                                    <tr>
                                                        <?php 
                                                            echo "<td class='text-center'>ยอดขาย ".date("Y")."</td>";
                                                            for($i = 1; $i <= 14; $i++){
                                                                echo "<td></td>";
                                                            }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <?php 
                                                            echo "<td class='text-center'>ยอดขาย ".(date("Y")-1)."</td>";
                                                            for($i = 1; $i <= 14; $i++){
                                                                echo "<td></td>";
                                                            }
                                                        ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </die>
                    </die>
                </die>
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
        $("#TableDetail").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 10,
        });
	});

    $.ajax({
        url: "../json/OCRD.json",
        cache: false,
        success: function(result) {
            var filt_data = 
                result.
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

    function SelectData() {
        var CardCode  = $("#CardCode").val();
        var StartDate = $("#StartDate").val();
        var EndDate   = $("#EndDate").val()
        var ItemCode  = $("#ItemCode").val();
        
        $("#view_Table tbody").html("<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td></tr>");
        $("#view_DocTotal").val("");
        $("#view_VatSum").val("");
        $("#view_SumTotal").val("");
        $("#DocEntry").val("");
        var DeStartDate = StartDate.split("-");
        var DeEndDate = EndDate.split("-");
        var Chk = 0;
        if(parseInt(DeStartDate[0]) <= 2022 && parseInt(DeEndDate[0]) <= 2022) {
            Chk = 1;
        }else{
            if(parseInt(DeStartDate[0]) >= 2023 && parseInt(DeEndDate[0]) >= 2023) {
                Chk = 1;
            }
        }

        if(Chk == 1) {
            $.ajax({
                url: "menus/sale/ajax/ajaxreport_bystore.php?a=SelectData",
                type: "POST",
                data: { CardCode : CardCode, StartDate : StartDate, EndDate : EndDate, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#TheadCardCode tbody").html(inval['Thead']);
                        $("#table_trab2 tbody").html(inval['Tbody2']);
                        $("#H_TB").html(inval['H_TB']);
                        DataTB();
                    });
                }
            })
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
            $("#alert_body").html("ไม่สามารถดึงข้อมูลข้ามปี 2022 - 2023 ได้");
            $("#alert_modal").modal("show");

            var Head = ["รหัสลูกค้า",   "ผู้แทนขาย",           "เครดิต",
                        "ชื่อลูกค้า",    "รหัสประจำตัวผู้เสียภาษี", "วงเงินเครดิต",
                        "กลุ่มลูกค้า",   "เงื่อนไข",            "วงเงินคงเหลือ",
                        "เบอร์โทรศัพท์","ที่อยู่"]; 
            var n = 0;
            var c = "";
            var tbody = "";
            for(var r = 1; r <= 4; r++) {
                if(r <= 3){
                    if(r == 1){ c = "pt-3"; }else{ c = ""; }
                    tbody += "<tr>";
                        tbody += "<th class='"+c+"'>"+Head[n]+"</th>"; n++;
                        tbody += "<th class='"+c+"'></th>";
                        tbody += "<th class='"+c+"'>"+Head[n]+"</th>"; n++;
                        tbody += "<th class='"+c+"'></th>";
                        tbody += "<th class='"+c+"'>"+Head[n]+"</th>"; n++;
                        tbody += "<th class='"+c+"'></th>";
                    tbody += "</tr>";
                }else{
                    tbody += "<tr>";
                        tbody += "<th>"+Head[n]+"</th>"; n++;
                        tbody += "<th></th>";
                        tbody += "<th>"+Head[n]+"</th>"; n++;
                        tbody += "<th></th>";
                    tbody += "</tr>";
                }
            }
            $("#TheadCardCode tbody").html(tbody);

            $("#TableDetail").dataTable().fnClearTable();
            $("#TableDetail").dataTable().fnDraw();
            $("#TableDetail").dataTable().fnDestroy();
            $("#H_TB").html("");
            $("#TableDetail").DataTable({
                "responsive": true, 
                "lengthChange": false, 
                "autoWidth": false,
                "pageLength": 10,
            });
        }
    }

    function DataTB() {
        $(".overlay").show();
        $("#TableDetail").dataTable().fnClearTable();
        $("#TableDetail").dataTable().fnDraw();
        $("#TableDetail").dataTable().fnDestroy();
        var CardCode  = $("#CardCode").val();
        var StartDate = $("#StartDate").val();
        var EndDate   = $("#EndDate").val();
        var ItemCode  = $("#ItemCode").val();
        // console.log(ItemCode);
        $("#TableDetail").DataTable({
            "ajax": {
                url: "menus/sale/ajax/ajaxreport_bystore.php?a=DataTB",
                type: "POST",
                data: { CardCode : CardCode, StartDate : StartDate, EndDate : EndDate, ItemCode : ItemCode, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "NumAtCard", class: "text-center border-start border-bottom" },
                { "data": "DocDate", class: "text-center border-start border-bottom" },
                { "data": "DocDueDate", class: "text-center border-start border-bottom" },
                { "data": "SlpName", class: "border-start border-bottom" },
                { "data": "U_PONo", class: "text-center border-start border-bottom border-end" },
            ],
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 10,
        });
        $(".overlay").hide();
    }

    function Detail(DocEntry) {
        var CardCode  = $("#CardCode").val();
        var StartDate = $("#StartDate").val();
        var EndDate   = $("#EndDate").val();
        $("#DocEntry").val(DocEntry);
        $.ajax({
            url: "menus/sale/ajax/ajaxreport_bystore.php?a=Detail",
            type: "POST",
            data: { DocEntry : DocEntry, CardCode : CardCode, StartDate : StartDate, EndDate : EndDate, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    var H_TB = Tbody = Tfoot = SumNoVat = VatSum = DocTotal = ViewIV = ViewDate = ViewEndDate = ViewSlpName = ViewPO = "";
                    if(inval['Row'] != 0) {
                        var Row = 1;
                        for(var r = 0; r < inval['Row']; r++) {
                            Tbody +="<tr>"+
                                        "<td class='text-center'>"+Row+"</td>"+
                                        "<td class='text-center'>"+inval["Tbody"]["ItemCode"][r]+"</td>"+
                                        "<td>"+inval["Tbody"]["Dscription"][r]+"</td>"+
                                        "<td class='text-center'>"+inval["Tbody"]["Quantity"][r]+"</td>"+
                                        "<td class='text-center'>"+inval["Tbody"]["unitMsr"][r]+"</td>"+
                                        "<td class='text-right'>"+inval["Tbody"]["PriceBefDi"][r]+"</td>"+
                                        "<td class='text-center'>"+inval["Tbody"]["U_Disc"][r]+"</td>"+
                                        "<td class='text-right'>"+inval["Tbody"]["LineTotal"][r]+"</td>"+
                                    "</tr>";
                            Row++;
                        }
                        SumNoVat = inval["Tbody"]['SumNoVat'][0];
                        VatSum   = inval["Tbody"]['VatSum'][0];
                        DocTotal = inval["Tbody"]['DocTotal'][0];
                    }else{
                        Tbody +="<tr>"+
                                    "<td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td>"+
                                "</tr>";
                    }

                    $("#view_Table tbody").html(Tbody);
                    $("#view_DocTotal").val(SumNoVat);
                    $("#view_VatSum").val(VatSum);
                    $("#view_SumTotal").val(DocTotal);
                    $("#TableDetail tbody tr").removeClass("table-danger");
                    $("a[data-id='"+DocEntry+"']").parents("tr").addClass("table-danger");
                });
            }
        });
    }

    function PrintTax() {
        var DocEntry = $("#DocEntry").val();
        var CardCode  = $("#CardCode").val();
        var StartDate = $("#StartDate").val();
        var EndDate   = $("#EndDate").val();
        if(DocEntry != "" && DocEntry != undefined) {
            window.open ('menus/sale/print/printtax.php?docentry='+DocEntry+'&cardcode='+CardCode+'&startdate='+StartDate+'&enddate='+EndDate,'_blank');
        }
    }
</script> 
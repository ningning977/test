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
                    <div class="col-sm-auto col-lg-auto">
                        <div class="form-group" style='width: 140px;'>
                            <label for="">เลือกปี</label>
                            <select class="form-select form-select-sm" name="SelectYear" id="SelectYear" onchange='GetHang();'>
                                <?php
                                $Year = "";
                                for($y = 2022; $y <= date("Y"); $y++) {
                                    if($y == date("Y")) {
                                        $Year = "<option value='$y' selected>$y</option>".$Year;
                                    }else{
                                        $Year = "<option value='$y'>$y</option>".$Year;
                                    }
                                } 
                                echo $Year;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-auto col-lg-auto">
                        <div class="form-group" style='width: 170px;'>
                            <label for="">เลือกเดือน</label>
                            <select class="form-select form-select-sm" name="SelectMonth" id="SelectMonth" onchange='CallData();'>
                                <?php
                                for($m = 1; $m <= 12; $m++) {
                                    if($m == date("m")) {
                                        echo "<option value='$m' selected>".FullMonth($m)."</option>";
                                    }else{
                                        echo "<option value='$m'>".FullMonth($m)."</option>";
                                    }
                                } 
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-auto col-lg-auto">
                        <div class="form-group" style='width: 170px;'>
                            <label for="">เลือกห้าง</label>
                            <select class="form-select form-select-sm" name="SelectHang" id="SelectHang" onchange='CallData();'></select>
                        </div>
                    </div>
                    <div class="col-sm-auto col-lg-auto">
                        <label></label>
                        <div class='d-flex'><button class='btn btn-sm btn-primary' onclick="ShowMDRebate();"><i class="fas fa-plus"></i>เพิ่มเงื่อนไข REBATE</button></div>
                    </div>
                    <div class="col-sm-auto col-lg-auto">
                        <label></label>
                        <div class='d-flex'><button class='btn btn-sm btn-outline-info' onclick="PrintRebate();"><i class="fas fa-print"></i>Print</button></div>
                    </div>
                </div>

                <div class="row pt-2">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table1'>
                                <thead class='text-center'>
                                    <tr>
                                        <th width='4%'>ลำดับที่</th>
                                        <th width='7%'>รหัสลูกค้า</th>
                                        <th width='15%'>ชื่อลูกค้า</th>
                                        <?php for($m = 1; $m <= 12; $m++) { echo "<th width='5.58%'>".FullMonth($m)."</th>";} ?>
                                        <th width='7%'>รวมทั้งปี</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan='16' class='text-center'>ไม่มีข้อมูล :)</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table2'>
                                <thead>
                                    <tr>
                                       <th colspan='2'>ยอดขายทั้งหมด (บาท)</th> 
                                       <th class='SalesTotal text-right'></th> 
                                    </tr>
                                    <tr class='text-center'>
                                        <th width='50%'>เงื่อนไข</th>
                                        <th width='20%'>ส่วนลด (%)</th>
                                        <th width='30%'>ยอด Rebate (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table3'>
                                <thead>
                                    <tr>
                                        <th colspan='2'>ยอดขายทั้งหมด (บาท)</th>
                                        <th class='SalesTotal2 text-right'></th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th width='50%'>เงื่อนไข Marketing Fee</th>
                                        <th width='20%'>ส่วนลด (%)</th>
                                        <th width='30%'>Marketing Fee (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table4'>
                                <thead>
                                    <tr>
                                        <th colspan='2'>ยอดขายทั้งหมด (บาท)</th>
                                        <th class='SalesTotal3 text-right'></th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th width='50%'>เงื่อนไข DC</th>
                                        <th width='20%'>ส่วนลด (%)</th>
                                        <th width='30%'>DC Fee (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalAddRebate" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus" style='font-size: 18px;'></i>&nbsp;&nbsp;เพิ่มเงื่อนไข REBATE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-auto col-lg-auto">
                        <div class="form-group" style='width: 140px;'>
                            <label for="">เลือกปี</label>
                            <select class="form-select form-select-sm" name="MDYear" id="MDYear" onchange=''>
                                <?php
                                $Year = "";
                                for($y = 2022; $y <= date("Y"); $y++) {
                                    if($y == date("Y")) {
                                        $Year = "<option value='$y' selected>$y</option>".$Year;
                                    }else{
                                        $Year = "<option value='$y'>$y</option>".$Year;
                                    }
                                } 
                                echo $Year;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-auto col-lg-auto">
                        <div class="form-group" style='width: 170px;'>
                            <label for="">เลือกห้าง</label>
                            <select class="form-select form-select-sm" name="MDHang" id="MDHang"></select>
                        </div>
                    </div>
                </div>

                <div class="row pt-2" style='font-size: 12px;'>
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for="">Min.</label>
                            <input type="number" class='form-control form-control-sm text-right' name='Min' id='Min' >
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group" >
                            <label for="">Max.</label>
                            <input type="number" class='form-control form-control-sm text-right' name='Max' id='Max' list="Txt9">
                            <datalist id="Txt9">
                                <option>999999999</option>
                            </datalist>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group" >
                            <label for="">ส่วนลด (%)</label>
                            <input type="number" class='form-control form-control-sm text-right' name='Discount' id='Discount'>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group" >
                            <label for="">ค่า Marketing Fee (%)</label>
                            <input type="number" class='form-control form-control-sm text-right' name='MarketingFee' id='MarketingFee'>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group" >
                            <label for="">ค่า DC (%)</label>
                            <input type="number" class='form-control form-control-sm text-right' name='DCFee' id='DCFee'>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <label></label>
                        <div class='d-flex'><button class='btn btn-sm btn-outline-info' onclick="AddList();"><i class="fas fa-plus"></i>เพิ่ม</button></div>
                    </div>
                </div>

                <!-- Table Show Data -->
                <!-- Data Form -->
                <form id="DataRebate" enctype="multipart/form-data">
                    <input type="hidden" name='No' id='No' value='0'>
                    <div class="row pt-2">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='TableShow'>
                                    <thead class='text-center'>
                                        <tr>
                                            <th width='8%'>ลำดับที่</th>
                                            <th width='17%'>Min.</th>
                                            <th width='17%'>Max.</th>
                                            <th width='13%'>ส่วนลด (%)</th>
                                            <th width='13%'>ค่า Marketing Fee (%)</th>
                                            <th width='13%'>ค่า DC (%)</th>
                                            <th width='7%'><i class="fas fa-edit"></i> แก้ไข</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="SaveData();">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alert_add" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="head_alert_add"></h5>
                <p id="body_alert_add" class="my-4"></p>
                <button type="button" class="btn btn-primary btn-sm" onclick='ChkConAdd();' data-bs-dismiss="modal">ตกลง</button>
            </div>
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
        GetHang();
	});

    function number_format(number,decimal) {
        var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
        var formatter = new Intl.NumberFormat("en",options);
        return formatter.format(number)
    }

    function GetHang() {
        $.ajax({
            url: "menus/account/ajax/ajaxreport_rebate.php?a=GetHang",
            type: "POST",
            data: { Year : $("#SelectYear").val(), }, 
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#SelectHang").html(inval['Data']);
                    CallData();
                });
            }
        })
    }

    function CallData() {
        var Year  = $("#SelectYear").val();
        var Month = $("#SelectMonth").val();
        var Hang  = $("#SelectHang").val();
        if(Hang != "" && Hang != null) {
            $(".overlay").show();
            // console.log(Year+" | "+Month+" | "+Hang);
            $.ajax({
                url: "menus/account/ajax/ajaxreport_rebate.php?a=CallData",
                type: "POST",
                data: { Year : Year, Month : Month, Hang : Hang, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        var Tbody = ""; var Tfoot = "";
                        var Tbody2 = ""; var Tfoot2 = "";
                        var Tbody3 = ""; var Tfoot3 = "";
                        var Tbody4 = ""; var Tfoot4 = "";
                        if(inval['Row'] != 0) {
                            //Table1
                            //รายการ Tbody ทั้งหมด
                            for(var r = 1; r <= inval['Row']; r++) {
                                Tbody +="<tr>"+
                                            "<td class='text-center'>"+r+"</td>"+
                                            "<td class='text-center'>"+inval['Data']['CardCode'][r]+"</td>"+
                                            "<td>"+inval['Data']['CardName'][r]+"</td>";
                                            for(var m = 1; m <= 12; m++) {
                                                Tbody += "<td class='text-right'>"+number_format(inval['Data']['M_'+m][r],2)+"</td>";
                                            }
                                            Tbody += "<td class='text-right fw-bolder'>"+number_format(inval['Data']['Sum'][r],2)+"</td>";
                                Tbody +="</tr>";
                            }

                            //รายการ Tfoot ทั้งหมด
                            var HeadTfoot = ['0', 'รวมทุกรายการ', 'ยอดขายสะสม',   'ส่วนลด (%)', 'ยอด Rebate สะสม'];
                            var Data =      ['0', 'AllM_',      'SumTotalAllM_', 'PerM_',     'Rebate'];
                            var SumData =   ['0', 'SumAllM',    'SumAllM',       'TotolPer',   'SumRebate'];
                            var CBolder = ""; var Aum = 0;
                            for(var h = 1; h <= (HeadTfoot.length-1); h++) {
                                if(HeadTfoot[h] == 'รวมทุกรายการ' || HeadTfoot[h] == 'ยอด Rebate สะสม') { CBolder = "fw-bolder"; }else{ CBolder = ""; }
                                Tfoot +="<tr class='"+CBolder+"'>"+ 
                                            "<td colspan='3'>"+HeadTfoot[h]+"</td>";
                                            if(HeadTfoot[h] == 'ส่วนลด (%)') { Aum = 1; }else{ Aum = 2;}
                                            for(var m = 1; m <= 12; m++) {
                                                Tfoot += "<td class='text-right'>"+number_format(inval['Data'][Data[h]+m],Aum)+"</td>";
                                            }
                                            Tfoot += "<td class='text-right fw-bolder'>"+number_format(inval['Data'][SumData[h]],Aum)+"</td>";
                                Tfoot +="</tr>";
                            }

                            //Table2
                            //ยอดขายทั้งหมด (บาท)
                            $(".SalesTotal").html(number_format(inval['Data']['SumAllM'],2));

                            //เงื่อนไข, ส่วนลด (%), ยอด Rebate (บาท)
                            for(var r = 1; r <= inval['Row2']; r++) {
                                Tbody2 +="<tr class='"+inval['Data']['Color_'+r]+"'>"+
                                            "<td>"+inval['Data']['Cdt_'+r]+"</td>"+
                                            "<td class='text-center'>"+number_format(inval['Data']['Percent_'+r],1)+"</td>"+
                                            "<td class='text-right'>"+number_format(inval['Data']['LastTotal_'+r],2)+"</td>"+
                                         "</tr>";
                            }

                            //รวม Rebate, หักภาษี ณ ที่จ่าย 3%, รวมจ่ายเช็คสุทธิ
                            Tfoot2 +="<tr class='fw-bolder'>"+
                                        "<td colspan='2'>รวม Rebate</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['LastTotalRE'],2)+"</td>"+
                                     "</tr>"+
                                     "<tr>"+
                                        "<td colspan='2'>หักภาษี ณ ที่จ่าย 3%</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['LastTotalRE']*0.03,2)+"</td>"+
                                     "</tr>"+
                                     "<tr class='fw-bolder'>"+
                                        "<td colspan='2'>รวมจ่ายเช็คสุทธิ</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['LastTotalRE']-(inval['Data']['LastTotalRE']*0.03),2)+"</td>"+
                                     "</tr>";
                            
                            //Table3
                            //ยอดขายทั้งหมด (บาท)
                            $(".SalesTotal2").html(number_format(inval['Data']['SumAllM'],2));

                            //เงื่อนไข Marketing Fee, ส่วนลด (%), Marketing Fee (บาท)
                            Tbody3 +="<tr>"+
                                        "<td>Marketing fee ตั้งแต่บาทแรก</td>"+
                                        "<td class='text-center'>"+number_format(inval['Data']['Percent_mkt'],1)+"</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['Totalmkt'],2)+"</td>"+
                                     "</tr>";
                            //รวม Marketing Fee, หักภาษี ณ ที่จ่าย 3%, รวมจ่ายเช็คสุทธิ
                            Tfoot3 +="<tr class='fw-bolder'>"+
                                        "<td colspan='2'>รวม Marketing Fee</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['Totalmkt'],2)+"</td>"+
                                    "</tr>"+
                                    "<tr>"+
                                        "<td colspan='2'>หักภาษี ณ ที่จ่าย 3%</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['Totalmkt']*0.03,2)+"</td>"+
                                    "</tr>"+
                                    "<tr class='fw-bolder'>"+
                                        "<td colspan='2'>รวมจ่ายเช็คสุทธิ</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['Totalmkt']-(inval['Data']['Totalmkt']*0.03),2)+"</td>"+
                                    "</tr>";

                            //Table4
                            //ยอดขายทั้งหมด (บาท)
                            $(".SalesTotal3").html(number_format(inval['Data']['SumAllM'],2));

                            //เงื่อนไข DC, ส่วนลด (%), DC Fee (บาท)
                            Tbody4 +="<tr>"+
                                        "<td>ค่า DC ตั้งแต่บาทแรก</td>"+
                                        "<td class='text-center'>"+number_format(inval['Data']['Percent_dc'],1)+"</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['Totaldc'],2)+"</td>"+
                                     "</tr>";
                            //รวม Rebate, หักภาษี ณ ที่จ่าย 3%, รวมจ่ายเช็คสุทธิ
                            Tfoot4 +="<tr class='fw-bolder'>"+
                                        "<td colspan='2'>รวม Rebate</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['Totaldc'],2)+"</td>"+
                                    "</tr>"+
                                    "<tr>"+
                                        "<td colspan='2'>หักภาษี ณ ที่จ่าย 3%</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['Totaldc']*0.03,2)+"</td>"+
                                    "</tr>"+
                                    "<tr class='fw-bolder'>"+
                                        "<td colspan='2'>รวมจ่ายเช็คสุทธิ</td>"+
                                        "<td class='text-right'>"+number_format(inval['Data']['Totaldc']-(inval['Data']['Totaldc']*0.03),2)+"</td>"+
                                    "</tr>";
                        }else{
                            Tbody = "<tr><td colspan='16' class='text-center'>ไม่มีข้อมูล :)</td></tr>";
                            $("#Table2").hide();
                            $("#Table3").hide();
                            $("#Table4").hide();
                        }
                        $("#Table1 tbody").html(Tbody);
                        $("#Table1 tfoot").html(Tfoot);

                        $("#Table2").show();
                        $("#Table2 tbody").html(Tbody2);
                        $("#Table2 tfoot").html(Tfoot2);

                        $("#Table3").show();
                        $("#Table3 tbody").html(Tbody3);
                        $("#Table3 tfoot").html(Tfoot3);

                        $("#Table4").show();
                        $("#Table4 tbody").html(Tbody4);
                        $("#Table4 tfoot").html(Tfoot4);
                    });
                    $(".overlay").hide();
                }
            })
        }else{
            $("#Table1 tbody").html("<tr><td colspan='16' class='text-center'>ไม่มีข้อมูล :)</td></tr>");
            $("#Table1 tfoot").html("");
            $("#Table2").hide();
            $("#Table3").hide();
            $("#Table4").hide();
        }
    }

    function ShowMDRebate() {
        $.ajax({
            url: "menus/account/ajax/ajaxreport_rebate.php?a=GetHang2",
            type: "GET",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#No").val("0");
                    $("#Min").val(1);
                    $("#Max").val("");
                    $("#Discount").val("0");
                    $("#MarketingFee").val("0");
                    $("#DCFee").val("0");
                    $("#MDHang").html(inval['Data']);
                    $("#TableShow tbody").html("");
                    $("#ModalAddRebate").modal("show");
                });
            }
        })
    }

    function AddList() {
        var Min          = $("#Min").val();
        var Max          = $("#Max").val();
        var Discount     = $("#Discount").val();
        var MarketingFee = $("#MarketingFee").val();
        var DCFee        = $("#DCFee").val();
        if(Min != "" && Max != "" && Discount != "" && MarketingFee != "" && DCFee != "") {
            if(parseInt(Min) < parseInt(Max)) {
                if(parseInt($("#No").val()) <= 10) {
                    var No = parseInt($("#No").val())+1;
                    $("#No").val(No);
                    var DataShow="<tr>"+
                                    "<td class='text-center pt-1 pb-1'>"+No+"</td>"+
                                    "<td class='text-right pt-1 pb-1'><input type='text' class='text-right form-control-sm ps-1 pe-1 pt-0 pb-0 form-control-plaintext ' name='Min"+No+"'          id='Min"+No+"'          value='"+number_format(Min,0)+"'          style='min-height: 0;' readonly></td>"+
                                    "<td class='text-right pt-1 pb-1'><input type='text' class='text-right form-control-sm ps-1 pe-1 pt-0 pb-0 form-control-plaintext ' name='Max"+No+"'          id='Max"+No+"'          value='"+number_format(Max,0)+"'          style='min-height: 0;' readonly></td>"+
                                    "<td class='text-center pt-1 pb-1'><input type='text'class='text-right form-control-sm ps-1 pe-1 pt-0 pb-0 form-control-plaintext ' name='Discount"+No+"'     id='Discount"+No+"'     value='"+number_format(Discount,1)+"'     style='min-height: 0;' readonly></td>"+
                                    "<td class='text-right pt-1 pb-1'><input type='text' class='text-right form-control-sm ps-1 pe-1 pt-0 pb-0 form-control-plaintext ' name='MarketingFee"+No+"' id='MarketingFee"+No+"' value='"+number_format(MarketingFee,2)+"' style='min-height: 0;' readonly></td>"+
                                    "<td class='text-right pt-1 pb-1'><input type='text' class='text-right form-control-sm ps-1 pe-1 pt-0 pb-0 form-control-plaintext ' name='DCFee"+No+"'        id='DCFee"+No+"'        value='"+number_format(DCFee,2)+"'         style='min-height: 0;' readonly></td>"+
                                    "<td class='text-center pt-1 pb-1'><a href='javascript:void(0);' class='EditList"+No+"' onclick='EditList("+No+");'><i class='fas fa-edit fa-fw fa-lg'></i></a><a href='javascript:void(0);' class='SaveList"+No+"' onclick='SaveList("+No+");'><i class='fas fa-save fa-fw fa-lg'></i></a></td>"+
                                "</tr>";
                    $("#TableShow tbody").append(DataShow);
        
                    $("#Min").val((parseInt(Max)+1));
                    $("#Max").val("");
                    $("#Discount").val("0");
                    $("#MarketingFee").val("0");
                    $("#DCFee").val("0");
    
                    $(".SaveList"+No).hide();
                }else{
                    $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 50px;'></i>");
                    $("#alert_body").html("ไม่สามารถใส่เกิน 10 เงื่อนไข");
                    $("#alert_modal").modal("show");
                }
            }else{
                $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 50px;'></i>");
                $("#alert_body").html("ค่า Max. ต้องมากกว่า ค่า Min.");
                $("#alert_modal").modal("show");
            }
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 50px;'></i>");
            $("#alert_body").html("กรุณากรอกข้อมูลให้ครบ");
            $("#alert_modal").modal("show");
        }
    }

    function EditList(No) {
        $("#Min"+No).removeClass("form-control-plaintext");          $("#Min"+No).addClass("form-control");          $("#Min"+No).removeAttr("readonly");
        $("#Max"+No).removeClass("form-control-plaintext");          $("#Max"+No).addClass("form-control");          $("#Max"+No).removeAttr("readonly");
        $("#Discount"+No).removeClass("form-control-plaintext");     $("#Discount"+No).addClass("form-control");     $("#Discount"+No).removeAttr("readonly");
        $("#MarketingFee"+No).removeClass("form-control-plaintext"); $("#MarketingFee"+No).addClass("form-control"); $("#MarketingFee"+No).removeAttr("readonly");
        $("#DCFee"+No).removeClass("form-control-plaintext");        $("#DCFee"+No).addClass("form-control");        $("#DCFee"+No).removeAttr("readonly");

        $(".EditList"+No).hide(); $(".SaveList"+No).show();
    }

    function SaveList(No) {
        $("#Min"+No).removeClass("form-control");          $("#Min"+No).addClass("form-control-plaintext");          $("#Min"+No).attr("readonly", true);
        $("#Max"+No).removeClass("form-control");          $("#Max"+No).addClass("form-control-plaintext");          $("#Max"+No).attr("readonly", true);
        $("#Discount"+No).removeClass("form-control");     $("#Discount"+No).addClass("form-control-plaintext");     $("#Discount"+No).attr("readonly", true);
        $("#MarketingFee"+No).removeClass("form-control"); $("#MarketingFee"+No).addClass("form-control-plaintext"); $("#MarketingFee"+No).attr("readonly", true);
        $("#DCFee"+No).removeClass("form-control");        $("#DCFee"+No).addClass("form-control-plaintext");        $("#DCFee"+No).attr("readonly", true);

        $(".EditList"+No).show(); $(".SaveList"+No).hide();
    }

    function SaveData() {
        if(parseInt($("#No").val()) != 0) {
            if($("#MDHang").val() != null && $("#MDHang").val() != "") {
                var DataRebate = new FormData($("#DataRebate")[0]);
                DataRebate.append('Hang',$("#MDHang").val());
                DataRebate.append('Year',$("#MDYear").val());
                $.ajax({
                    url: "menus/account/ajax/ajaxreport_rebate.php?a=SaveData",
                    type: "POST",
                    dataType: 'text',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: DataRebate,
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#ModalAddRebate").modal("hide");
                            $("#head_alert_add").html("<i class='fas fa-check-circle' style='font-size: 60px;'></i>");
                            $("#body_alert_add").html("เพิ่มเงื่อนไข REBATE สำเร็จ");
                            $("#alert_add").modal("show");
                        });
                    }
                })
            }else{
                $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
                $("#alert_body").html("กรุณาเลือกห้างก่อน");
                $("#alert_modal").modal("show");
            }
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
            $("#alert_body").html("กรุณาเพิ่มข้อมูลก่อน");
            $("#alert_modal").modal("show");
        }
    }

    function ChkConAdd() {
        location.reload();
    }

    function PrintRebate() {
        var Year  = $("#SelectYear").val();
        var Month = $("#SelectMonth").val();
        var Hang  = $("#SelectHang").val();
        if(Hang != "" && Hang != null) {
            window.open("menus/account/print/print_rebate.php?Year="+Year+"&Month="+Month+"&Hang="+Hang);  
        }else{
            
        }
    }
</script> 
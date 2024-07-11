<style type="text/css">
    @media only screen and (max-width:820px) {
        .tableFix {
            overflow-y: auto;
            height: 800px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:821px) and (max-width:1180px) {
        .tableFix {
            overflow-y: auto;
            height: 450px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:1181px) {
        .tableFix {
            overflow-y: auto;
            height: 630px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
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
                    <div class="col-lg-7 col-sm-7">
                        <div class='d-flex align-items-center'>
                            <span class='text-primary pe-2'>ระหว่างวันที่</span>
                            <input type="date" id='StartDate' value='<?php echo date("Y")."-01-01"; ?>' class="form-control form-control-sm" style='width: 120px;' onchange="SelectDate();">  
                            <span class='text-primary ps-2 pe-2'>ถึง</span>             
                            <input type="date" id='EndDate' class="form-control form-control-sm" style='width: 120px;' onchange="SelectDate();">
                        </div>
                    </div>
                    <div class="col-lg-5 col-sm-5 d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="PrintDebtreceive();"><i class="fas fa-print fa-fw fa-1x" aria-hidden="true"></i> พิมพ์</button>
                    </div>
                </div>

                <div class="row pt-2">
                    <div class="col-lg">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-bordered table-hover' id='TableShow'>
                                <thead class='text-center' style='background-color: #FFF; font-size: 12.5px;'>
                                    <tr>
                                        <th rowspan='2' class='align-bottom'>เลขที่ใบสำคัญรับ</th>
                                        <th rowspan='2' class='align-bottom'>วันที่รับเงิน</th>
                                        <th rowspan='2' class='align-bottom'>ชื่อลูกค้า</th>
                                        <th rowspan='2' class='align-bottom'>เลขที่บิล</th>
                                        <th rowspan='2' class='align-bottom'>วันที่บิล</th>
                                        <th rowspan='2' class='align-bottom'>รหัสพนักงานขาย</th>
                                        <th rowspan='2' class='align-bottom'>ยอดรับชำระ</th>
                                        <th rowspan='2' class='align-bottom'>เงินสด</th>
                                        <th rowspan='2' class='align-bottom'>เงินโอน</th>
                                        <th rowspan='2' class='align-bottom'>เช็ค</th>
                                        <th rowspan='2' class='align-bottom'>ส่วนลด</th>
                                        <th colspan='3'>หมายเหตุ</th>
                                    </tr>
                                    <tr>
                                        <th>เลขที่เช็ค</th>
                                        <th>ลงวันที่</th>
                                        <th>ธนาคาร</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;'>
                                    <tr>
                                        <td colspan='14' class='text-center'>ไม่มีข้อมูล :)</td>
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

    function SelectDate() {
        var StartDate = $("#StartDate").val();
        var EndDate   = $("#EndDate").val();
        var DeStartDate = StartDate.split("-");
        var DeEndDate = EndDate.split("-");
        var Chk = 0;
        if(parseInt(DeStartDate[0]) <= 2022 && parseInt(DeEndDate[0]) <= 2022)       { Chk = 1; }
        else{ if(parseInt(DeStartDate[0]) >= 2023 && parseInt(DeEndDate[0]) >= 2023) { Chk = 1; } }
        if(Chk == 1) {
            $(".overlay").show();
            $.ajax({
                url: "menus/account/ajax/ajaxdebtreceive.php?a=SelectDate",
                type: "POST",
                data: { StartDate : StartDate, EndDate : EndDate, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        var Tbody = "";
                        if(inval['Row'] != 0) {
                            for(var r = 0; r <= inval['Row']; r++) {
                                if(inval[r]['InvoiceId'] == 0 || inval[r]['InvoiceId'] == "") {
                                    Tbody +="<tr class='table-warning'>"+
                                                "<td class='text-center'>"+inval[r]['DocNo']+"</td>"+
                                                "<td class='text-center'>"+inval[r]['DocDate']+"</td>"+
                                                "<td colspan='3'>"+inval[r]['CusName']+"</td>"+
                                                "<td colspan='2'>"+inval[r]['SlpName']+"</td>"+
                                                "<td class='text-right'>"+inval[r]['CashSum']+"</td>"+
                                                "<td class='text-right'>"+inval[r]['TransSum']+"</td>"+
                                                "<td class='text-right'>"+inval[r]['CheckSum']+"</td>"+
                                                "<td class='text-right'>"+inval[r]['Discount']+"</td>"+
                                                "<td class='text-center'>"+inval[r]['CheckNum']+"</td>"+
                                                "<td class='text-center'>"+inval[r]['DueDate']+"</td>"+
                                                "<td class='text-right'>"+inval[r]['BankName']+"</td>"+
                                            "</tr>"+
                                            "<tr>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td class='text-center'>"+inval[r]['ReferenceNo']+"</td>"+
                                                "<td class='text-center'>"+inval[r]['InvoiceDate']+"</td>"+
                                                "<td></td>"+
                                                "<td class='text-right'>"+inval[r]['SumApplied']+"</td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                            "</tr>";
                                }else{
                                    Tbody +="<tr>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td class='text-center'>"+inval[r]['ReferenceNo']+"</td>"+
                                                "<td class='text-center'>"+inval[r]['InvoiceDate']+"</td>"+
                                                "<td></td>"+
                                                "<td class='text-right'>"+inval[r]['SumApplied']+"</td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                                "<td></td>"+
                                            "</tr>";
                                }
                            }
                        }else{
                            Tbody += "<tr><td colspan='14' class='text-center'>ไม่มีข้อมูล :)</td></tr>";
                        }
                        $("#TableShow tbody").html(Tbody);
                    });
                    $(".overlay").hide();
                }
            })
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
            $("#alert_body").html("ห้ามข้ามปี 2022 - 2023");
            $("#alert_modal").modal("show");
            $("#TableShow tbody").html("<tr><td colspan='14' class='text-center'>ไม่มีข้อมูล :)</td></tr>");
        }
    }

    function PrintDebtreceive() {
        var StartDate = $("#StartDate").val();
        var EndDate   = $("#EndDate").val();
        if(EndDate != "") {
            var DeStartDate = StartDate.split("-");
            var DeEndDate = EndDate.split("-");
            var Chk = 0;
            if(parseInt(DeStartDate[0]) <= 2022 && parseInt(DeEndDate[0]) <= 2022)       { Chk = 1; }
            else{ if(parseInt(DeStartDate[0]) >= 2023 && parseInt(DeEndDate[0]) >= 2023) { Chk = 1; } }
            if(Chk == 1) {
                window.open ('menus/account/print/print_debtreceive.php?startdate='+StartDate+'&enddate='+EndDate,'_blank');
            }else{
                $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
                $("#alert_body").html("ห้ามข้ามปี 2022 - 2023");
                $("#alert_modal").modal("show");
            }
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
            $("#alert_body").html("กรุณาเลือกวันที่");
            $("#alert_modal").modal("show");
        }
    }
</script> 
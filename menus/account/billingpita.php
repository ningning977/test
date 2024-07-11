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
                    <div class="col-lg d-flex">
                        <div class="form-group" style='width: 200px;'>
                            <label for="TxtCode">ค้นหา</label>
                            <input type="text" class='form-control form-control-sm' name='TxtCode' id='TxtCode' placeholder="รหัสลูกค้า/รหัสเอกสาร">
                        </div>

                        <div class='align-self-center ps-2' style='width: 110px;'>
                            <button class='btn btn-sm btn-primary' style='margin-top: 10px;' onclick="CallData()"><i class="fas fa-search"></i> ค้นหา</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive pt-1" >
                            <table id='TableHead' class="table table-borderless rounded rounded-3 overflow-hidden" style='background-color: rgba(155, 0, 0, 0.04);'>
                                <thead style='background-color: rgba(136, 0, 0, 0.70);'>
                                    <tr>
                                        <td colspan='6' class='text-light'><div class='d-flex align-center justify-content-between'>ข้อมูลลูกค้า </div></td>
                                    </tr>
                                </tdead>
                                <tbody style='font-size: 13px;'>
                                    <?php
                                        $Header = [ '0', 
                                                    'รหัสลูกค้า',     'เลขที่ใบวางบิล',
                                                    'ชื่อลูกค้า',      'วันที่/Date',
                                                    'ที่อยู่/Address', 'เงื่อนไขการชำระเงิน',
                                                    '', 'เลขประจำตัวผู้เสียภาษี',
                                                    'โทรศัพท์',      'Fax.'
                                                    ]; 
                                        $H = 0;
                                        $Tbody = "";
                                        for($i = 1; $i <= 5; $i++) {
                                            $H++;
                                            if($i != 5) {
                                                $Tbody .= "<tr>";
                                                    $Tbody .= "<td width='20%' class='fw-bolder'>".$Header[$H]."</td>";
                                                    $Tbody .= "<td></td>";
                                                    $H++;
                                                    $Tbody .= "<td width='20%' class='fw-bolder'>".$Header[$H]."</td>";
                                                    $Tbody .= "<td></td>";
                                                $Tbody .= "</tr>";
                                            }else{
                                                $Tbody .= "<tr>";
                                                    $Tbody .= "<td width='20%' class='fw-bolder'>".$Header[$H]."</td>";
                                                    $Tbody .= "<td></td>";
                                                    $H++;
                                                    $Tbody .= "<td width='20%' class='fw-bolder'>".$Header[$H]."</td>";
                                                    $Tbody .= "<td></td>";
                                                $Tbody .= "</tr>";
                                            }
                                        }
                                        echo $Tbody;
                                    ?>
                                </tbody>
                            </table>
                            <input type="hidden" name='TbodyHead' id='TbodyHead' value="<?php echo $Tbody; ?>"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table id='TableList' class='table table-sm table-bordered table-hover'>
                                <thead style='font-size: 13px;'>
                                    <tr class='text-center'>
                                        <th width='5%'>ลำดับที่</th>
                                        <th width='15%'>วันเดือนปี</th>
                                        <th width='15%'>เลขที่ใบแจ้งหนี้</th>
                                        <th width='15%'>วันครบกำหนด</th>
                                        <th width='15%'>จำนวนเงิน</th>
                                        <th width='15%'>จำนวนเงินที่เรียกเก็บ</th>
                                        <th width='15%'>หมายเหตุ</th>
                                        <th width='5%'>เลือก</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;'>
                                    <tr>
                                        <td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td>
                                    </tr>
                                </tbody>
                                <tfoot style='font-size: 13.5px;'>
                                    <tr>
                                        <td colspan='6' class='text-right fw-bolder'>ผู้ออกเอกสาร: <span class='fw-bold' id='Creater'></span></td>
                                        <td colspan='2' class='text-center'>
                                            <input type="hidden" name='CardCode' id='CardCode'>
                                            <input type="hidden" name='DocNum' id='DocNum'>
                                            <button class='btn btn-sm btn-primary' id='btnSave' onclick='Save()' disabled><i class='fas fa-save'></i> บันทึก</button>
                                            <button class='btn btn-sm btn-secondary' id='btnPrint' onclick='Print()' data-bs-dismiss="modal" ><i class='fas fa-print'></i> Print</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="alert_modal_print" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5 class="modal-title" id="alert_header_print"></h5>
                    <p id="alert_body_print" class="my-4"></p>
                    <button class='btn btn-sm btn-secondary' data-bs-dismiss="modal" style='width: 69.22px;'>ออก</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button class='btn btn-sm btn-outline-info' id='btnPrint' onclick='Print()' data-bs-dismiss="modal" ><i class='fas fa-print'></i> Print</button>
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
        $("#btnPrint").hide();
	});

    $('#TxtCode').keypress(function (e) {
        if (e.which == 13) {
            CallData();
        }
    });

    function CallData() {
        var CardCode = $("#TxtCode").val().toUpperCase();
        if(CardCode.substr(0,2) == "BI") {
            $("#btnPrint").show();
            $("#btnSave").hide();
        }else{
            $("#btnPrint").hide();
            $("#btnSave").show();
        }
        var TbodyHead = $("#TbodyHead").val();
        if(CardCode != "") {
            $.ajax({
                url: "menus/account/ajax/ajaxbillingpita.php?a=CallData",
                type: "POST",
                data: { CardCode : CardCode },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        if(inval['ChkData'] == 'Y') {
                            if(inval['alert'] == 0) {
                                $("#TableHead tbody").html(inval['Tbody']);
                                $("#TableList tbody").html(inval['output']);
                                $("#Creater").html(inval['Creater']);
                                $("#CardCode").val(inval['CardCode']);
                                $("#DocNum").val(inval['DocNum']);
                                if(inval['Print'] == 0) {
                                    // $("#btnPrint").attr("disabled", true);
                                    $("#btnSave").removeAttr("disabled", true);
                                }else{
                                    $("#btnSave").attr("disabled", true);
                                    // $("#btnPrint").removeAttr("disabled", true);
                                }
                            }else{
                                $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
                                $("#alert_body").html("ไม่มีใบวางบิลนี้");
                                $("#alert_modal").modal("show");
                                $("#btnSave").attr("disabled", true);
                                // $("#btnSave, #btnPrint").attr("disabled", true);
                                $("#TableHead tbody").html(TbodyHead);
                                $("#TableList tbody").html("<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td></tr>");
                                $("#Creater").html("");
                            }
                        }else{
                            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
                            $("#alert_body").html("ไม่มีรหัสลูกค้า/รหัสเอกสารนี้");
                            $("#alert_modal").modal("show");
                            $("#btnSave").attr("disabled", true);
                            // $("#btnSave, #btnPrint").attr("disabled", true);
                            $("#TableHead tbody").html(TbodyHead);
                            $("#TableList tbody").html("<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td></tr>");
                            $("#Creater").html("");
                        }
                    });
                }
            })
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
            $("#alert_body").html("กรุณากรอกรหัสลูกค้า/รหัสเอกสารก่อน");
            $("#alert_modal").modal("show");
            $("#btnSave, #btnPrint").attr("disabled", true);
            $("#TableHead tbody").html(TbodyHead);
            $("#TableList tbody").html("<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td></tr>");
            $("#Creater").html("");
        }
    }

    function chkRow(TransID,Remark,LineNum) {
        if($("#chkid_"+TransID+"_"+LineNum).is(":checked")) { var Chk = 1; }else{ var Chk = 0; }
        $.ajax({
            url: "menus/account/ajax/ajaxbillingpita.php?a=AddData",
            type: "POST",
            data: { Chk      : Chk,
                    TransID  : TransID,
                    IVRemark   : Remark,
                    LineNum : LineNum,
                    CardCode : $("#CardCode").val(),
                    DocNum   : $("#DocNum").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    
                });
            }
        })
    }

    function CallBI(TransID,LineNum) {
        $.ajax({
            url: "menus/account/ajax/ajaxbillingpita.php?a=CallBI",
            type: "POST",
            data: { TransID : TransID, LineNum: LineNum },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TxtCode").val(inval['DocNum']);
                    CallData();
                });
            }
        })
    }

    function Save() {
        $.ajax({
            url: "menus/account/ajax/ajaxbillingpita.php?a=Save",
            type: "POST",
            data: { DocNum : $("#DocNum").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    if(inval['DocNum'] == 'You Need Me') {
                        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
                        $("#alert_body").html("บันทึกข้อมูลไม่สำเร็จ");
                        $("#alert_modal").modal("show");
                    }else{
                        $("#alert_header_print").html("<i class='fas fa-check-circle text-success' style='font-size: 60px;'></i>");
                        $("#alert_body_print").html("บันทึกข้อมูลสำเร็จ");
                        $("#alert_modal_print").modal("show");
                        // $("#TxtCode").val(inval['DocNum']);
                        // CallData();
                    }
                });
            }
        })
    }

    function Print() {
        var DocNum = $("#DocNum").val();
        window.open ('menus/account/print/printBillpita.php?docnum='+DocNum,'_blank');
        CallData();
    }
</script> 
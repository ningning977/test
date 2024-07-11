<style type="text/css">
    .tableFix thead {
        z-index: 99;
    }
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
                    <div class="col-lg mb-3">
                        <div class="form-group mb-3 d-flex justify-content-end align-items-center">
                            <label for="" class="me-2">ค้นหา</label>
                            <div><input type="text" class="form-control" name="FilterBox" id="FilterBox" placeholder="กรอกข้อมูลที่ต้องการค้นหา"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th>วันที่เอกสาร</th>
                                        <th>กำหนดส่ง</th>
                                        <th>เลขที่ S/O</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th>มูลค่าท้ายบิล</th>
                                        <th>พนักงานขาย</th>
                                        <th>สถานะการอนุมัติ</th>
                                    </tr>
                                </thead>
                                <tbody id='mainbody'>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalAppOrder" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="HeaderModal">อนุมัติใบสั่งขาย</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg">
                    <input type='hidden' name='CardCode' id='CardCode'>
                    <input type='hidden' name='AllCard' id='AllCard'>
                    <div class="row fw-bold">
                        <div class="col-lg">วันที่เอกสาร :&nbsp;<span class="text-primary" id="DocDate">10/09/2022</span></div>
                        <div class="col-lg">ชื่อลูกค้า :&nbsp;<span class="text-primary" id="CardName">M-00422 - เล็กก่อสร้าง อุดร</span></div>
                        <div class="col-lg">สถานะการอนุมัติ :&nbsp;<span class="text-primary" id="StatusApp">เครดิตวงเงิน</span></div>
                    </div>
                    <div class="row fw-bold">
                        <div class="col-lg">เลขที่เอกสาร :&nbsp;<span class="text-primary" id="DocNum">xx-xxxxxxxxx</span></div>
                        <div class="col-lg">พนักงานขาย :&nbsp;<span class="text-primary" id="SlpName">B98-ซ่อมภายนอก QC</span></div>
                        <div class="col-lg">เขตการขาย :&nbsp;<span class="text-primary" id="CH"></span></div>
                    </div>
                    <div class="row fw-bold">
                        <div class="col-lg">เครดิต :&nbsp;<span class="text-primary" id="CreditTerm"></span></div>
                        <div class="col-lg">วิธีการวางบิล :&nbsp;<span class="text-primary" id="ActionBill"></span></div>
                        <div class="col-lg">วิธีการชำระเงิน:&nbsp;<span class="text-primary" id="ActionPay"></span></div>
                    </div>
                    <div class="row fw-bold">
                        <div class="col-lg">ลูกค้าสั่งซื้อมาแล้ว :&nbsp;<span class="text-primary" id="CountBill"></span><span class="text-primary">&nbsp;บิล</span></div>
                        <div class="col-lg">วันที่เปิดบิลครั้งแรก :&nbsp;<span class="text-primary" id="FristBill"></span></div>
                        <div class="col-lg">มียอดชำระเงินมาแล้ว :&nbsp;<span class="text-primary" id="TotalPay"></span><span class="text-primary">&nbsp;บาท</span></div>
                    </div>
                    <div class="row fw-bold">
                        <div class="col-lg">ยอดสั่งซื้อปี <?php echo date("Y")-1;?> :&nbsp;<span class="text-primary" id="PayOldYear"></span><span class="text-primary">&nbsp;บาท</span></div>
                        <div class="col-lg">ยอดสั่งซื้อปี <?php echo date("Y");?> :&nbsp;<span class="text-primary" id="PayNowYear"></span><span class="text-primary">&nbsp;บาท</span></div>
                        <div class="col-lg">เครดิตวงเงิน :&nbsp;<span class="text-primary" id="Crlimit"></span><span class="text-primary">&nbsp;บาท</span></div>
                    </div>
                    <div class="row fw-bold">
                        <div class="col-lg">หมายเหตุท้าย SO :&nbsp;<span class="text-primary" id="OrderRemark"></span></div>
                    </div>
                </div>
            </div>
            <input type='hidden' id='DocEntry' name='DocEntry' value=''>
            <div class="tab-content mt-3" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-report-sales" role="tabpanel" aria-labelledby="nav-report-sales-tab">
                    <div class="row">
                        <div class="col-lg-12 d-flex align-items-center">
                            <div class="me-1">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab" href="#list-app">รายการอนุมัติ</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" href="#cr-app" onclick="CallCR()">เครดิตวงเงิน</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" href="#gp-app" onclick="CallGP()" >ขอราคาพิเศษ</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" href="#atth-app" onclick="CallAtth()" >เอกสารแนบ</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div id="list-app" class="tab-pane active">
                                <div class="row mt-3">
                                    <div class="col-lg" id="ApproveContent"></div>
                                </div>
                            </div>

                            <!-- เครดิตวงเงิน -->
                            <div id="cr-app" class="tab-pane fade">
                                <div class="row mt-3">
                                    <div class="col-lg">
                                        <div class="table-responsive tableFix">
                                            <!-- Row 1 -->
                                            <div class="d-flex">
                                                <table class="table table-bordered me-3 rounded rounded-3 overflow-hidden">
                                                    <thead style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class="text-center">
                                                            <th colspan="3" class="text-primary">บิลที่ยังไม่เรียกเก็บ</th>
                                                        </tr>
                                                        <tr class="text-center">
                                                            <th width="40%">วันที่เกินกำหนด</th>
                                                            <th width="30%">จำนวน</th>
                                                            <th width="30%">มูลค่า (บาท)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='CR_T1'></tbody>
                                                </table>
                                                <table class="table table-bordered rounded rounded-3 overflow-hidden">
                                                    <thead style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class="text-center">
                                                            <th colspan="3" class="text-primary">ใบยืมสินค้าที่ยังไม่คืน</th>
                                                        </tr>
                                                        <tr class="text-center">
                                                            <th width="40%">วันที่เกินกำหนด</th>
                                                            <th width="30%">จำนวน</th>
                                                            <th width="30%">มูลค่า (บาท)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='CR_T2'></tbody>
                                                </table>
                                            </div>
                                            <!-- End Row 1 -->

                                            <!-- Row 2 -->
                                            <div class="d-flex">
                                                <table class="table table-bordered me-3 rounded rounded-3 overflow-hidden">
                                                    <thead style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class="text-center">
                                                            <th colspan="3" class="text-primary">ใบสั่งขายที่ยังไม่ส่งสินค้า</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='TB1'></tbody>
                                                </table>
                                                <table class="table table-bordered rounded rounded-3 overflow-hidden">
                                                    <thead style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class="text-center">
                                                            <th colspan="3" class="text-primary">ประวัติการคืนสินค้า</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='TB2'></tbody>
                                                </table>
                                            </div>
                                            <!-- End Row 2 -->

                                            <!-- Row 3 -->
                                            <div class="d-flex">
                                                <table class="table table-bordered me-3 rounded rounded-3 overflow-hidden">
                                                    <thead style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class="text-center">
                                                            <th colspan="3" class="text-primary">รายการรอเช็คขึ้นเงิน</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='TB3'></tbody>
                                                </table>
                                                <table class="table table-bordered rounded rounded-3 overflow-hidden">
                                                    <thead style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class="text-center">
                                                            <th colspan="3" class="text-primary">ประวัติเช็คเด้ง</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='TB4'></tbody>
                                                </table>
                                            </div>
                                            <!-- End Row 3 -->

                                            <!-- Row 4 -->
                                            <div class="d-flex">
                                                <table class="table table-bordered me-3 rounded rounded-3 overflow-hidden">
                                                    <thead style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class="text-center">
                                                            <th class="text-primary">หมายเหตุเพื่มเติม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='TBNote'></tbody>
                                                </table>
                                                <table class="table table-bordered rounded rounded-3 overflow-hidden">
                                                    <thead style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class="text-center">
                                                            <th colspan="3" class="text-primary">ขอนุมัติวงเงินเฉพาะบิล<span id='TypeCRTxt' stype='color:'></span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td width="50%">รายการสั่งซื้อใหม่ครั้งนี้</td>
                                                            <td width="40%" class="text-right"><span id='L1'>0.00</span></td>
                                                            <td width="10%">บาท</td>
                                                        </tr>
                                                        <tr>
                                                            <td>วงเงินเครดิตเดิม</td>
                                                            <td class="text-right"><span id='L2'>0.00</span></td>
                                                            <td>บาท</td>
                                                        </tr>
                                                        <tr>
                                                            <td>วงเงินเครดิตใหม่</td>
                                                            <td class="text-right"><span id='L3'>0.00</span></td>
                                                            <td>บาท</td>
                                                        </tr>
                                                        <tr>
                                                            <td>วงเงินเกินเครดิต</td>
                                                            <td class="text-right"><span id='L4'>0.00</span></td>
                                                            <td>บาท</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- End Row 4 -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End เครดิตวงเงิน -->

                            <!-- ขอราคาพิเศษ -->
                            <div id="gp-app" class="tab-pane fade">
                                <div class="row mt-3">
                                    <div class="col-lg">
                                        <div class="table-responsive tableFix">
                                            <table class="table table-bordered">
                                                <thead style='background-color: #FFF;'>
                                                    <tr class="text-center">
                                                        <th width="1%"></th>
                                                        <th width="2%">No.</th>
                                                        <th>ชื่อสินค้า</th>
                                                        <th width="3%">คลังสินค้า</th>
                                                        <th width="3%">จำนวน</th>
                                                        <th width="3%">หน่วย</th>
                                                        <th>ต้นทุนต่อชิ้น<br>(บาท) (VAT)</th>
                                                        <th>ราคาต่อชิ้น<br>(บาท) (VAT)</th>
                                                        <th>ราคารวม<br>(บาท) (VAT)</th>
                                                        <th>กำไรรวม<br>(บาท)</th>
                                                        <th width="3%">% กำไร</th>
                                                        <th>ขายเข้า MT<br>ล่าสุด (บาท)</th>
                                                        <th>หน้าขาย MT<br>(บาท)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id='gptab' >
                                                    
                                                </tbody>
                                                <tfoot id="gptab2">

                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End ขอราคาพิเศษ -->
                            <!-- เอกสารแนบ -->
                            <div id="atth-app" class="tab-pane fade">
                                <div class="row mt-3">
                                    <div class="col-lg">
                                        <div class="table-responsive tableFix">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th width="5%">ลำดับ</th>
                                                        <th >ชื่อเอกสารแนบ</th>
                                                        <th width="20%">วันที่อัพโหลด</th>
                                                        <th width="7.5%"><i class="fas fa-file-download fa-fw fa-lg"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody id='atth' >
                                                    
                                                </tbody>
                                                <tfoot id="atth2">
                                                    <tr>
                                                        <td colspan='4'>
                                                            <label for="AttachOrder">แนบไฟล์เพิ่มเติม</label>  <a href="javascript:void(0);" class="text-muted" data-bs-toggle="tooltip" title="รองรับนามสกุลไฟล์รูปภาพ (*.jpg, *.jpeg, *.png) / MS Word (*.doc, *.docx) / MS Excel (*.xls, *.xlsx) / เอกสาร (*.pdf) เท่านั้น"><i class="far fa-question-circle fa-fw fa-lg"></i></a>
                                                            <form id="UploadsForm" enctype="multipart/form-data">
                                                                <input type="file" class="form-control form-control-sm w-25" name="AttachOrder[]" id="AttachOrder" accept=".jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf" multiple onchange="UploadsFile()"/>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End ขอราคาพิเศษ -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type='hidden' id='RowApp' name='RowApp' value='0'>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            <button type="button" class="btn btn-primary" id='SaveApp' onclick="SaveAppr()">บันทึก</button>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ContentModal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id='HeaderModal'></h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg" id='BoxContentModal'></div>
                </div>
            </div>
            <div class="modal-footer pt-1 pb-1">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm_saved" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-check-circle fa-fw fa-lg text-success"></i> สำเร็จ</h5>
                <p id="confirm_Wai" class="my-4">บันทึกข้อมูลสำเร็จ</p>
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        CallHeade();
        CallData();
        var tooltipTriggerList = [].slice.call($('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
	});
</script> 
<script type="text/javascript">
    try{ document.createEvent("TouchEvent"); var isMobile = true; }
    catch(e){ var isMobile = false; }
    function CallHeade(){
        $(".overlay").show();
        var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
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
    $("#FilterBox").on("keyup", function(){
        var kwd = $(this).val().toLowerCase();
        $("#mainbody tr").filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(kwd) > -1)
        });
    });

    function SOAppOrder(y,x){
        $(".overlay").show();
        var MenuCase = $('#HeadeMenuLink').val();
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=approv",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            data : {
                DocEntry : x,
                ChkApp : y,
                Mobile: isMobile,
                },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#DocEntry').val(x);
                    $('#CardName').html(inval['CardName']);
                    $('#CardCode').val(inval['CardCode']);
                    $('#DocDate').html(inval['DocDate']);
                    $('#DocNum').html(inval['DocNum']);
                    $('#SlpName').html(inval['SlpName']);
                    $('#StatusApp').html(inval['StatusApp']);
                    

                    $('#CreditTerm').html(inval['TextCR']);
                    $('#ActionBill').html(inval['TextBill']);
                    $('#Crlimit').html(inval['Crlimit']);
                    $('#ActionPay').html(inval['SaveTxt']);

                    $('#OrderRemark').html(inval['OrderRemark']);
                    $('#FristBill').html(inval['FristBill']);
                    $('#CountBill').html(inval['CountBill']);
                    $('#TotalPay').html(inval['TotalPay']);
                    if(isMobile == true) {
                        var ApproveContent = 
                            "<div class='table-responsive'>"+
                                "<table class='table table-borderless'>"+
                                    "<tbody>"+inval['output']+"</tbody>"+
                                "</table>"+
                            "</div>";
                    } else {
                        var ApproveContent = 
                            "<div class='table-responsive'>"+
                                "<table class='table table-bordered'>"+
                                    "<thead>"+
                                        "<tr class='text-center'>"+
                                            "<th width='5%'>ลำดับ</th>"+
                                            "<th width='25%'>ผู้อนุมัติ</th>"+
                                            "<th width='15%'>การดำเนินการ</th>"+
                                            "<th>หมายเหตุ</th>"+
                                            "<th width='15%'>สถานะอนุมัติ</th>"+
                                        "</tr>"+
                                    "</thead>"+
                                    "<tbody>"+inval['output']+"</tbody>"+
                                "</table>"+
                            "</div>"; 
                    }
                    $('#ApproveContent').html(ApproveContent);
                    
                    CallCR();
                    CallGP();
                    $("#ModalAppOrder").modal("show");

                    // GET VALUE
                    $('#CardCode').val(inval['CardCode']);
                    $('#DocNum').val(inval['DocNum']);
                    if (inval['AppPermit'] == '0'){
                        $("#SaveApp").attr("disabled","disabled");
                    }else{
                        $("#SaveApp").removeAttr("disabled");
                        $('#RowApp').val(inval['IDApp']);
                    }
                });
                $(".overlay").hide();
            }
        });
    }
    function CallData(){
        $(".overlay").show();
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=read",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#mainbody").html(inval["output"]);
                });
                $(".overlay").hide();
            }
        });
    };
    function CallCR(){
        $(".overlay").show();
        var DocEntry = $('#DocEntry').val();
        var DocNum = $('#DocNum').val();
        var CardCode = $('#CardCode').val();
        // console.log($('#DocNum').val()+" | "+$('#CardCode').val());
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=crtab",
            type: "POST",
            data : { DocEntry : DocEntry, DocNum : DocNum, CardCode : CardCode, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#AllCard").val(inval["AllCard"]);
                    $("#CR_T1").html(inval["CR_T1"]);
                    $("#CR_T2").html(inval["CR_T2"]);
                    $("#TB1").html(inval["TB1"]);
                    $("#TB2").html(inval["TB2"]);
                    $("#TB3").html(inval["TB3"]);
                    $("#TB4").html(inval["TB4"]);
                    $("#TBNote").html(inval["TBNote"]);
                    $('#TypeCRTxt').html(inval['CRType']);
                    $("#L1").html(inval["L1"]);
                    $("#L2").html(inval["L2"]);
                    $("#L3").html(inval["L3"]);
                    $("#L4").html(inval["L4"]);
                    
                });
                $(".overlay").hide();
            }
        });
    };
    function CallGP(){
        $(".overlay").show();
        var DocEntry = $('#DocEntry').val();
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=gptab",
            type: "POST",
            data : { DocEntry : DocEntry, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#gptab").html(inval["output"]);
                    $("#gptab2").html(inval["tb2"]);
                });
                $(".overlay").hide();
            }
        });
    };

    function CallAtth(){
        $(".overlay").show();
        var DocEntry = $('#DocEntry').val();
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=atttab",
            type: "POST",
            data : { DocEntry : DocEntry, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    if (inval['dpt'] == 'Y'){
                        $('#OrderAttach').removeAttr('disabled');
                    }
                    $("#atth").html(inval["output"]);
                });
                $(".overlay").hide();
            }
        });
    };

    function CallORDR(wai){
    $.ajax({
        url: "../core/ORDR.php?x="+wai,
        type: 'POST',
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                switch (inval['Status']){
                    case 'N' :
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html(inval['errMsg']);
                        $("#alert_modal").modal('show');
                        break;
                    default : // รออนุมัติ
                        $("#confirm_Wai").html(inval['errMsg']);
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                        break;
                }

            });
        }
    });
    }
    function SaveAppr(){
        $(".overlay").show();
        var ID = $('#RowApp').val();
        var Remark = $('#Remark_'+ID).val();
        var App =$('#App_'+ID).val();
        var DocEntry = $('#DocEntry').val();
        
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=save",
            type: "POST",
            data : { ID : ID,
                     Remark : Remark,
                     App : App, 
                     DocEntry : DocEntry,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    if (inval['output'] == 'WAI'){
                        CallORDR(inval['DocEntry']);
                    }else{
                        //alert(inval['output']);
                        $("#confirm_Wai").html(inval['output']);
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                    }
                });
                $(".overlay").hide();
            }
        });
    };

    function CallModal(T) {
        $(".overlay").show();
        var AllCard = $('#AllCard').val();
        var CardCode = $('#CardCode').val();
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=CallModal",
            type: "POST",
            data: { AllCard : AllCard, CardCode : CardCode, TbModal : T, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#HeaderModal").html(inval["HeadModal"]);
                    $("#BoxContentModal").html(inval["Tbody"]);
                    $("#ContentModal").modal('show');
                });
                $(".overlay").hide();
            }
        })
    }

    function UploadsFile() {
        var DocEntry = $('#DocEntry').val();
        var UploadsForm = new FormData($("#UploadsForm")[0]);
        UploadsForm.append('DocEntry',DocEntry);
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=UploadsFile",
            type: "POST",
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: UploadsForm,
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    $("#AttachOrder").val("");
                    CallAtth();
                })
            }
        })
    }

    function ViewPur(ItemCode) {
        // console.log(ItemCode);
        $.ajax({
            url: "menus/general/ajax/ajaxapp_order.php?a=GetViewPur",
            type: "POST",
            data: { ItemCode : ItemCode, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    alert(inval['Data']);
                })
            }
        })
    }
</script> 
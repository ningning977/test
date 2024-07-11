<?php
    $this_year  = substr(date("Y"),2);
    $this_month = date("m");

    $TaxM = $this_month;
    $TaxY = substr(date("Y")+543,2);
    $TaxMonth = $TaxM."/".$TaxY;
?>
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
                    <div class="col-md-1">
                        <select class="form-select form-select-sm" name="xTaxCat" id="xTaxCat" placeholder="หมวดภาษีหัก ณ ที่จ่าย">
                            <option value="All" selected >หมวดภาษี</option>
                            <option value="S02">ภ.ง.ด.2</option>
                            <option value="S03">ภ.ง.ด.3</option>
                            <option value="S53">ภ.ง.ด.53</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <input type="text" class="form-control form-control-sm text-center" id="filt_BID" name="filt_BID" placeholder="งวดภาษี" value="<?php echo $TaxMonth; ?>">
                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-2">
                        
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="btn-add" class="btn btn-sm btn-primary w-100" onclick="ResetData()"><i class="fa fa-plus fa-fw fa-1x"></i> เพิ่มรายการ</button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="btn-print" class="btn btn-sm btn-secondary w-100"><i class="fas fa-print fa-fw fa-1x"></i> พิมพ์</button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="btn-text" class="btn btn-sm btn-secondary w-100"><i class="far fa-file-alt fa-fw fa-1x"></i> TEXT </button>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="3.5%" rowspan="2">ลำดับ</th>
                                    <th style="border-bottom: none; border-right: none">ชื่อผู้รับเงินได้พึงประเมิน</th>
                                    <th width="10%" style="border-left: none">เลขประจำตัวผู้เสียภาษี</th>
                                    <th rowspan="2" width="5%">สาขา</th>
                                    <th colspan="4">รายละเอียดเกี่ยวกับการจ่ายเงินได้พึงประเมิน</th>
                                    <th rowspan="2" width="7.5%">เงินภาษีที่หัก<p>และนำส่งในครั้งนี้</p></th>
                                    <th rowspan="2">เงือนไข <span id="H2"></span></th>
                                    <th rowspan="2" width="3.5%">แก้ไข</th>
                                    <th rowspan="2" width="3.5%">พิมพ์</th>
                                    <th rowspan="2" width="3.5%">ลบ</th>
                                </tr>
                                <tr>
                                    <th style="border-top: none; border-right: none">ที่อยู่ของผู้มีเงินได้</th>
                                    <th style="border-left: none">&nbsp;</th>
                                    <th width="5%">วันที่จ่าย</th>
                                    <th>ประเภทเงินได้ <span id="H1"></span></th>
                                    <th width="3.5%">อัตรา</th>
                                    <th width="7.5%">เงินที่จ่าย</th>
                                </tr>
                            </thead>
                            <tbody id="LogList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form method="post" enctype="multipart/form-data" name="form1" id="addVat">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">ป้อนรายละเอียดรายการภาษีหัก ณ ที่จ่าย</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="BookNo">เล่มที่/เลขที่</label>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" id="ConF" name="ConF" value="0">
                                <input type="hidden" id="RWID" name="RWID" value="">
                                <input type="text" class="form-control form-control-sm" id="BookNo" name="BookNo" placeholder="กรุณากรอกเล่มที่ / เลขที่" readonly/>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="TaxDate">วันที่ออกหนังสือ</label>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control form-control-sm" id="TaxDate" name="TaxDate" value="<?php echo date('Y-m-d'); ?>" />
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="VatMonth">ยื่นภาษีรวมในงวดที่</label>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" id="VatMonth" name="VatMonth" placeholder="กรุณากรอกยื่นภาษีรวมในงวดที่" onfocusout="CallNewNo()" value=""  >
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="AddTax">เพิ่มเติม (พิมพ์แยก?)</label>
                            </div>
                            <div class="col-md-1"><input type="text" class="form-control form-control-sm" id="AddTax" name="AddTax" placeholder=""></div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="DocNum">เลขที่เอกสารภายใน</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" id="DocNum" name="DocNum" placeholder="กรุณากรอกเลขที่เอกสารภายใน">
                            </div>
                            <div class="col-md-2">
                                <label for="DocDate">วันที่เอกสารภายใน</label>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control form-control-sm" id="DocDate" name="DocDate" placeholder="กรุณากรอกวันที่เอกสารภายใน" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="Department">เป็นรายการของแผนก</label>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" id="Department" name="Department" placeholder="">
                            </div>
                        </div>
                        <hr/>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="CardCode">รหัสเจ้าหนี้/ผู้ถูกหัก</label>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" id="CardCode" name="CardCode" placeholder="กรุณากรอกรหัสเจ้าหนี้/ผู้ถูกหัก">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="Prefix">ชื่อ</label>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" id="Prefix" name="Prefix" placeholder="คำนำหน้า">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm" id="CardName" name="CardName" placeholder="กรุณากรอกชื่อ สกุล หรือชื่อบริษัทฯ">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="Address">ที่อยู่</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control form-control-sm" id="Address" name="Address" placeholder="กรุณากรอกที่อยู่">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="TaxID">เลขประจำตัวผู้เสียภาษี</label>
                            </div>
                            <div class="col-md-3"><input type="text" class="form-control form-control-sm" id="TaxID" name="TaxID" placeholder="กรุณากรอกเลขประจำตัวผู้เสียภาษี"></div>
                            <div class="col-md-1">
                                <label for="BranchID">สาขา#</label>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control form-control-sm" id="BranchID" name="BranchID" value="0">
                            </div>
                            <div class="col-md-2">
                                <label for="">[0 = สำนักงานใหญ่]</label>
                            </div>
                        </div>
                        <hr/>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="offset-md-2 col-md-4 text-center">
                                <strong>กลุ่มที่ 1</strong>
                            </div>
                            <div class="col-md-4 text-center">
                                <strong>กลุ่มที่ 2 </strong>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="PayType">ประเภทเงินได้ที่จ่าย</label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" name="PayType1" id="PayType1" placeholder="ประเภทเงินได้ (กลุ่ม 1)">
                                    <option value="NULL" selected disabled>กรุณาเลือกประเภทเงินได้ (กลุ่ม 1)</option>
                                    <option value="10">1. เงินเดือน</option>
                                    <option value="11">1. ค่าจ้าง</option>
                                    <option value="12">1. โบนัส</option>
                                    <option value="21">2. ค่านายหน้า จ่ายบุคคลธรรมดา</option>
                                    <option value="22">2. ค่านายหน้า จ่ายนิติบุคคล</option>
                                    <option value="40">4. ค่าดอกเบี้ย</option>
                                    <option value="413">4(ข) 1.3 เงินปันผลกิจการ 20%</option>
                                    <option value="422">4. เงินส่วนแบ่งกำไร</option>
                                    <option value="50">5. ค่าจ้างทำของ จ่ายบุคคลธรรมดา</option>
                                    <option value="51">5. ค่าจ้างทำของ จ่ายนิติบุคคล</option>
                                    <option value="52">5. ค่าจ้างโฆษณา</option>
                                    <option value="53">5. ค่าเช่า</option>
                                    <option value="60">6. อื่น ๆ (ระบุ)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" name="PayType2" id="PayType2" placeholder="ประเภทเงินได้ (กลุ่ม 2)">
                                    <option value="NULL" selected disabled>กรุณาเลือกประเภทเงินได้ (กลุ่ม 2)</option>
                                    <option value="10">1. เงินเดือน</option>
                                    <option value="11">1. ค่าจ้าง</option>
                                    <option value="12">1. โบนัส</option>
                                    <option value="21">2. ค่านายหน้า จ่ายบุคคลธรรมดา</option>
                                    <option value="22">2. ค่านายหน้า จ่ายนิติบุคคล</option>
                                    <option value="40">4. ค่าดอกเบี้ย</option>
                                    <option value="413">4(ข) 1.3 เงินปันผลกิจการ 20%</option>
                                    <option value="422">4. เงินส่วนแบ่งกำไร</option>
                                    <option value="50">5. ค่าจ้างทำของ จ่ายบุคคลธรรมดา</option>
                                    <option value="51">5. ค่าจ้างทำของ จ่ายนิติบุคคล</option>
                                    <option value="52">5. ค่าจ้างโฆษณา</option>
                                    <option value="53">5. ค่าเช่า</option>
                                    <option value="60">6. อื่น ๆ (ระบุ)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="offset-md-2 col-md-4">
                                <input type="text" class="form-control form-control-sm" name="PayType160" id="PayType160" disabled />
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" name="PayType260" id="PayType260" disabled />
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2">
                                <label for="PayTotal">จำนวนเงินที่คำนวณภาษี</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm text-right" name="PayTotal1" id="PayTotal1" value="0.00" onfocusout="CalTax(1,'PAY')" />
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm text-right" name="PayTotal2" id="PayTotal2" value="0.00" onfocusout="CalTax(2,'PAY')"/>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2">
                                <label for="TaxRate">อัตราที่หัก</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm text-right" name="TaxRate1" id="TaxRate1" value="0.00" onfocusout="CalTax(1,'TAX')"/>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm text-right" name="TaxRate2" id="TaxRate2" value="0.00" onfocusout="CalTax(2,'TAX')"/>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2">
                                <label for="TaxType">เงื่อนไขหักภาษี</label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" id="TaxType" name="TaxType">
                                    <option value="1" selected>ภาษีหัก ณ ที่จ่าย</option>
                                    <option value="2">ออกให้ตลอดไป</option>
                                    <option value="3">ออกให้ครั้งเดียว</option>
                                    <option value="4">อื่น ๆ (ระบุ)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-select form-select-sm" name="TaxType4" id="TaxType4" disabled />
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2">
                                <label for="DocTotal">จำนวนเงินที่จ่าย</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm text-right" name="DocTotal1" id="DocTotal1" value="0.00" />

                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm text-right" name="DocTotal2" id="DocTotal2" value="0.00" />
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2">
                                <label for="VatTotal">ภาษีที่หักไว้</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm text-right" name="VatTotal1" id="VatTotal1" value="0.00" />
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm text-right" name="VatTotal2" id="VatTotal2" value="0.00" />
                            </div>
                        </div>
                        <hr/>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2">
                                <label for="TaxCat">หมวดภาษีหัก ณ ที่จ่าย</label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" name="TaxCat" id="TaxCat" onchange="CallNewNo()">
                                    <option value="S02">ภ.ง.ด.2</option>
                                    <option value="S03" selected>ภ.ง.ด.3</option>
                                    <option value="S53">ภ.ง.ด.53</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-2">
                                <label for="TaxGroup">กลุ่ม</label>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" name="TaxGroup" id="TaxGroup" placeholder="กลุ่ม" />
                            </div>
                            <div class="col-md-3">
                                <label for="">(สำหรับแบบ ภงด.2 เท่านั้น)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btn-edit" name="edit_menu" class="btn btn-primary btn-sm"><i class="fa fa-save fa-fw fa-1x"></i>ตกลง</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><i class="fa fa-times fa-fw fa-1x"></i>ยกเลิก</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>
    <!-- /.modal-dialog -->
</div>

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
    }

    function ResetData(){
        $("#addVat").trigger("reset");
        //$('#BookNo').val("PX3/YYMMXXX");
        $("#PayType160").attr("disabled","disabled"); 
        $("#PayType260").attr("disabled","disabled"); 
        var x = $('#xTaxCat').val();
        var y = $('#filt_BID').val();
        if(x != "All") {
            switch(x){
                case 'S53':
                    var d = 'P53';
                    $('#TaxCat').val('S53');
                    break;
                case 'S03':
                    var d = 'P03';
                    $('#TaxCat').val('S03');
                    break;
                case 'S02':
                    var d = 'P02';
                    $('#TaxCat').val('S02');
                    break;
            }
            $.ajax({
                url: "menus/account/ajax/ajaxwht_jap.php?p=callno",
                type: "POST",
                data: {
                    TaxType:d,
                    TaxMonth:y,
                },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key, inval) {
                        console.log(inval['bookno']);
                        $('#BookNo').val(inval['bookno']);
                        $('#VatMonth').val(inval['vatMonth']);
                        $('#TaxCat').val();
                        $('#ConF').val(0);
                        $('#myModal').modal('show');
                    });
                }
            });
        } else {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณาเลือกหมวดภาษีก่อนเพิ่มรายการ");
            $("#alert_modal").modal('show');
        }
    }

    function CallNewNo(){
        var x = $('#TaxCat').val();
        var y = $('#VatMonth').val();
        //console.log(x);
        $.ajax({
        url: "menus/account/ajax/ajaxwht_jap.php?p=callno",
        type: "POST",
        data: {
                TaxType:x,
                TaxMonth:y,
              },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $('#BookNo').val(inval['bookno']);
                $('#VatMonth').val(inval['vatMonth']);
            });
        }
       });
    }

    function CalTax(x,y){
        var paytotal  = parseFloat($('#PayTotal'+x).val()).toFixed(2);
        var taxRate = parseFloat($('#TaxRate'+x).val()).toFixed(2);
        var VatTotal = parseFloat(paytotal*(taxRate/100)).toFixed(3);
        //var VatTotal = parseFloat(473.265);
        //console.log(VatTotal);
        var  a = VatTotal.charAt(VatTotal.length -1);
        console.log("a"+a);
        if (a >= 5){
            //console.log();
            var b = VatTotal.substring(0,VatTotal.length -1);
            console.log("b"+b);
            VatTotal = parseFloat(b)+0.01;
            //VatTotal = VatTotal.charAt(VatTotal.length -1)
            //console.log()
            VatTotal = parseFloat(VatTotal).toFixed(2);;

        }else{
            VatTotal =parseFloat(VatTotal).toFixed(2);
        }
        $('#PayTotal'+x).val(paytotal);
        $('#TaxRate'+x).val(taxRate);
        $('#DocTotal'+x).val(paytotal);
        $('#VatTotal'+x).val(VatTotal);
    }

    function ajaxSearch() {
        var filt_BID = $("#filt_BID").val();
        var TaxCat = $("#xTaxCat").val();
        $.ajax({
            url: "menus/account/ajax/ajaxwht_jap.php?p=all",
            type: "POST",
            data: {
                filt_BID: $("#filt_BID").val(),
                TaxCat: $("#xTaxCat").val(),
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                var tBody = "";
                $.each(obj, function(key, inval) {
                    var Rows = inval['Rows'];

                    for(i = 0; i < Rows; i++) {
                        tBody +=
                            "<tr>"+
                                "<td rowspan='2' class='text-right'>"+inval[i]['VisOrder']+"</td>"+
                                "<td style='border-bottom: none; border-right: none'>"+inval[i]['CardName']+"</td>"+
                                "<td class='text-right' style='border-bottom: none; border-left: none;'>"+inval[i]['TaxID']+"</td>"+
                                "<td class='text-center' rowspan='2'>"+inval[i]['TaxCat']+"</td>"+
                                "<td class='text-center' rowspan='2'>"+inval[i]['taxDate']+"</td>"+
                                "<td rowspan='2'>"+inval[i]['PayShow']+"</td>"+
                                "<td class='text-right' rowspan='2'>"+inval[i]['TaxRate1']+"</td>"+
                                "<td class='text-right' rowspan='2'>"+inval[i]['DocTotal1']+"</td>"+
                                "<td class='text-right' rowspan='2'>"+inval[i]['VatTotal1']+"</td>"+
                                "<td class='text-center' rowspan='2'>"+inval[i]['TaxType']+"</td>"+
                                "<td class='text-center' rowspan='2'><span id='print1' onclick=\"CallModal('"+inval[i]['ID']+"')\" style='cursor: pointer;'><i class='fas fa-edit fa-fw fa-1x'></i></span></td>"+
                                "<td class='text-center' rowspan='2'><span id='print1' onclick=\"PrintOut('"+inval[i]['ID']+"')\" style='cursor: pointer;'><i class='fas fa-print fa-fw fa-1x'></i></span></td>"+
                                "<td class='text-center' rowspan='2'><span id='print1' onclick=\"DelRow('"+inval[i]['ID']+"')\" style='cursor: pointer;'><i class='fas fa-times fa-fw fa-1x'></i></span></td>"+
                            "</tr>"+
                            "<tr>"+
                                "<td colspan='2' style='border-top: none;'>"+inval[i]['Address']+"</td>"+
                            "</tr>";
                    }

                    $("#LogList").html(tBody);

                    if (TaxCat == 'S03'){
                        $('#H1').html("(1)");
                        $('#H2').html("(2)");
                    }
                });
            }
        });
    }

    function DelRow(x) {
      $.ajax({
        url: "menus/account/ajax/ajaxwht_jap.php?p=del",
        type: "POST",
        data: {
            IDwd: x,
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-success\"></i> บันทึกสำเร็จ!");
                $("#alert_body").html("ลบรายการเรียบร้อยแล้ว");
                $("#alert_modal").modal('show');
            });
        }
      });
    }

    function CallModal(x) {
      $.ajax({
        url: "menus/account/ajax/ajaxwht_jap.php?p=modal",
        type: "POST",
        data: {
            IDwd: x
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#BookNo").val(inval['BookNo']);
                $("#TaxDate").val(inval['TaxDate']);
                $("#VatMonth").val(inval['VatMonth']);
                $("#AddTax").val(inval['AddTax']);
                $("#DocNum").val(inval['DocNum']);
                $("#DocDate").val(inval['DocDate']);
                $("#Department").val(inval['Department']);
                $("#CardCode").val(inval['CardCode']);
                $("#Prefix").val(inval['Prefix']);
                $("#CardName").val(inval['CardName']);
                $("#Address").val(inval['Address']);
                $("#TaxID").val(inval['TaxID']);
                $("#BranchID").val(inval['BranchID']);
                $("#PayType1").val(inval['PayType1']);
                if (inval['PayType1'] == '60'){
                    $("#PayType160").removeAttr("disabled","disabled"); 
                }
                $("#PayType160").val(inval['PayType160']);
                $("#PayType2").val(inval['PayType2']);

                if (inval['PayType2'] == '60'){
                    $("#PayType260").removeAttr("disabled","disabled"); 
                }
                $("#PayType260").val(inval['PayType260']);
                $("#PayTotal1").val(inval['PayTotal1']);
                $("#PayTotal2").val(inval['PayTotal2']);
                $("#TaxRate1").val(inval['TaxRate1']);
                $("#TaxRate2").val(inval['TaxRate2']);
                $("#TaxType").val(inval['TaxType']);
                $("#TaxType4").val(inval['TaxType4']);
                $("#DocTotal1").val(inval['DocTotal1']);
                $("#DocTotal2").val(inval['DocTotal2']);
                $("#VatTotal1").val(inval['VatTotal1']);
                $("#VatTotal2").val(inval['VatTotal2']);
                $("#TaxCat").val(inval['TaxCat']);
                $("#TaxGroup").val(inval['TaxGroup']);
                $("#RWID").val(x);
                $("#ConF").val(1);
                $("#myModal").modal('show');

            });
        }
      });
    }

    function PrintOut(x) {
        window.open("menus/account/print/printPND.php?id="+x,"_blank");
    }

    /* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
    $(document).ready(function(){
        CallHead();
        ajaxSearch();

        $("#addVat").on("submit", function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "menus/account/ajax/ajaxwht_jap.php?p=add",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    $.each(obj, function(key, inval) {
                        var AddStatus = inval['Status'].split("::");
                        var modal_txt = "";

                        if(AddStatus[0] == "SUCCESS") {
                            switch(AddStatus[1]) {
                                case "INSERT": modal_txt = "เพื่มรายการหัก ณ ที่จ่ายสำเร็จ"; break;
                                case "UPDATE": modal_txt = "แก้ไขรายการหัก ณ ที่จ่ายสำเร็จ"; break;
                            }
                            $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-success\"></i> บันทึกสำเร็จ!");
                            $("#alert_body").html(modal_txt);
                            $("#alert_modal").modal('show');
                            $("#myModal").modal('hide');
                            ajaxSearch();
                        } else {
                            switch(AddStatus[1]) {
                                case "DUPLICATE": modal_txt = "ไม่สามารถบันทึกได้เนื่องจากมีเล่มที่/เลขที่ของเอกสารนี้ในระบบแล้ว"; break;
                            }
                            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                            $("#alert_body").html(modal_txt);
                            $("#alert_modal").modal('show');
                        }

                        switch(AddStatus[0]) {
                            case "ERR":
                                
                            break;
                            default:
                                var InsertStatus = AddStatus.split("::");
                            break;
                        }
                    });
                }
            });
        });

        $("#btn-print").on("click",function(e){
            e.preventDefault();
            var filt_BID = $("#filt_BID").val();
            var TaxCat= $("#xTaxCat").val();
            //console.log(CatX);
            var BID = filt_BID.replace("/","");
            if(filt_BID == "" || TaxCat == "All" ){
                
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("กรุณากรอกงวดภาษี/หมวดภาษีให้ถูกต้อง");
                $("#alert_modal").modal('show');
                $("#filt_BID").focus();
            }else{
                window.open("menus/account/print/printPNDReport.php?vm="+TaxCat+"-"+BID,"_blank");
            }
        });

        $("#btn-text").on("click",function(e){
            e.preventDefault();
            var filt_BID = $("#filt_BID").val();
            var TaxCat= $("#xTaxCat").val();
            if(filt_BID == "" || TaxCat == "ALL" ){
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("กรุณากรอกงวดภาษี/หมวดภาษีให้ถูกต้อง");
                $("#alert_modal").modal('show');

                $("#filt_BID").focus();
            } else {
                window.open("menus/account/export/printtext.php?vm="+TaxCat+"-"+filt_BID,"_blank");
            }
        });

        $("#filt_BID").on("change", function() {
        var filt_BID = $(this).val();
        var TaxCat = $("#xTaxCat").val();
        ajaxSearch();
        });

        $("#xTaxCat").on("change", function() {
        var filt_BID = $("#filt_BID").val();
        var TaxCat = $(this).val();
        ajaxSearch();
        });

        $("#TaxType").on("change",function(){
            var TaxType = $(this).val();
            if(TaxType == 4) {
                $("#TaxType4").attr("disabled",false).focus();
            } else {
                $("#TaxType4").attr("disabled",true).val("");
            }
        });
        $("#PayType1").on("change",function(){
            var PayType1 = $(this).val();
            if(PayType1 == 60) {
                $("#PayType160").attr("disabled",false).focus();
            } else {
                $("#PayType160").attr("disabled",true).val("");
            }
        });
        $("#PayType2").on("change",function(){
            var PayType2 = $(this).val();
            if(PayType2 == 60) {
                $("#PayType260").attr("disabled",false).focus();
            } else {
                $("#PayType260").attr("disabled",true).val("");
            }
        });
    });
</script> 
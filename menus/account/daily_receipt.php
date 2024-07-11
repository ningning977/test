<style type="text/css">
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

    .tableFix thead tr:first-child th {
        box-shadow: inset 0.5px 0.5px #eee, 0 0.5px #eee;
        position: sticky;
        top: 0;
        height: 36px;
    }
    .tableFix thead tr:last-child th {
        box-shadow: inset 0.5px 0.5px #eee, 0 0.5px #eee;
        position: sticky;
        top: 36px;
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
                <!---------- เนื้อหา Pages ------------>
                <div class="row">
                    <div class="col-lg d-flex">
                        <div class="form-group" style='width: 100px;'>
                            <label for=""><i class="far fa-calendar"></i> เลือกปี</label>
                            <select class="form-select form-select-sm" name="filt_year" id="filt_year" onchange="CallData()">
                                <?php 
                                    $Y = date("Y");
                                    for($STY = 2020; $STY <= $Y; $Y--) {
                                        if($Y == date("Y")) {
                                            echo "<option value='".$Y."' selected>".$Y."</option>";
                                        }else{
                                            echo "<option value='".$Y."'>".$Y."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group ps-3" style='width: 140px;'>
                            <label for=""><i class="fas fa-calendar-alt"></i> เลือกเดือน</label>
                            <select class="form-select form-select-sm" name="filt_month" id="filt_month" onchange="CallData()">
                                <?php 
                                    for($m = 1; $m <= 12; $m++) {
                                        if($m == date("m")) {
                                            echo "<option value='".$m."' selected>".FullMonth($m)."</option>";
                                        }else{
                                            echo "<option value='".$m."'>".FullMonth($m)."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-hover table-bordered'>
                                <thead class='sticky-top' style='font-size: 13px;'>
                                    <tr class='text-center'>
                                        <th width='5%' rowspan='2' style='background-color: #fff'>วัน</th>
                                        <th width='5%' rowspan='2' style='background-color: #fff'>วันที่</th>
                                        <th colspan='9' style='background-color: #fffdd9;'>ยอดเก็บเงิน KBI</th>
                                        <th colspan='3' style='background-color: #D9EDF7;'>ยอดเก็บเงิน PITA</th>
                                        <?php
                                            if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP009") {
                                                echo "<th rowspan='2' style='background-color: #fff'>บันทึก</th>";
                                            } 
                                        ?>
                                    </tr>
                                    <tr class='text-center'>
                                        <th width='7%' style='background-color: #fffdd9'>หน้าร้าน<br>(บิล AA)</th>
                                        <th width='7%' style='background-color: #fffdd9'>หน้าร้าน<br>(ไม่ใช่บิล AA)</th>
                                        <th width='7%' style='background-color: #fffdd9'>ออนไลน์</th>
                                        <th width='7%' style='background-color: #fffdd9'>TT กทม.</th>
                                        <th width='7%' style='background-color: #fffdd9'>TT ตจว.</th>
                                        <th width='7%' style='background-color: #fffdd9'>MT 1</th>
                                        <th width='7%' style='background-color: #fffdd9'>MT 2</th>
                                        <th width='7%' style='background-color: #fffdd9'>ยอดเก็บเงิน<br>ทั้งหมด</th>
                                        <th width='7%' style='background-color: #fffdd9'>ค่าใช้จ่าย</th>
                                        <th width='7%' style='background-color: #D9EDF7;'>ยอดขายทั้งหมด</th>
                                        <th width='7%' style='background-color: #D9EDF7;'>ยอดเก็บทั้งหมด</th>
                                        <th width='7%' style='background-color: #D9EDF7;'>ค่าใช้จ่าย</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='Tbody'>
                                    <tr>
                                        <td colspan='15' class='text-center'>กำลังโหลด <i class='fas fa-spinner fa-pulse'></i></td>
                                    </tr>
                                </tbody>
                                <tfoot style='font-size: 12px;' id='Tfoot'></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-------- สินสุดเนื้อหา Pages --------->
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalAlert" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="ModalAlert-head"></h5>
                <p id="ModalAlert-body" class="my-4"></p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAddData" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header pt-2 pb-2">
                <h5 class="modal-title"><i class="far fa-save" style='font-size: 30px;'></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>บันทึกข้อมูลค่าใช้จ่าย</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class='d-flex justify-content-end'>
                    <span class='text-primary fw-bolder'>วันที่บันทึก</span>&nbsp;
                    <span class='text-primary fw-bolder' id='Df_ReceiptDate'></span>
                    <input class='form-control form-control-sm' type="hidden" name='ReceiptDate' id='ReceiptDate'>
                </div>
                <div class='d-flex justify-content-center'>
                    <span class='fw-bolder'>บันทึกข้อมูล PITA</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-3">
                    <div style='width: 75px;'><span>ยอดขาย</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='AC_PTASale' id='AC_PTASale' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-2">
                    <div style='width: 75px;'><span>ยอดเก็บเงิน</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='AC_PTAReceipt' id='AC_PTAReceipt' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-2">
                    <div style='width: 75px;'><span>ค่าใช้จ่าย</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='AC_PTACost' id='AC_PTACost' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
            </div>
            <div class="modal-footer pt-2 pb-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" onclick="AddData()"><i class="far fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAddTarget" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header pt-2 pb-2">
                <h5 class="modal-title"><i class="far fa-save" style='font-size: 30px;'></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>บันทึกเป้าการเก็บเงิน</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center justify-content-center">
                    <div style='width: 170px;'><span>ทีมขายหน้าร้าน (บิล AA)</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='TAR_OULAA' id='TAR_OULAA' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-2">
                    <div style='width: 170px;'><span>ทีมขายหน้าร้าน (ไม่ใช่บิล AA)</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='TAR_OUL' id='TAR_OUL' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-2">
                    <div style='width: 170px;'><span>ทีมขายออนไลน์</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='TAR_ONL' id='TAR_ONL' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-2">
                    <div style='width: 170px;'><span>ทีมขาย TT กทม.</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='TAR_TT1' id='TAR_TT1' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-2">
                    <div style='width: 170px;'><span>ทีมขาย TT ตจว.</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='TAR_TT2' id='TAR_TT2' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-2">
                    <div style='width: 170px;'><span>ทีมขาย MT1</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='TAR_MT1' id='TAR_MT1' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
                <div class="d-flex align-items-center justify-content-center pt-2">
                    <div style='width: 170px;'><span>ทีมขาย MT2</span>&nbsp;</div>
                    <input class='form-control form-control-sm text-right' style='width: 200px;' type="text" name='TAR_MT2' id='TAR_MT2' placeholder='0.00'>&nbsp;<span>บาท</span>
                </div>
            </div>
            <div class="modal-footer pt-2 pb-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" onclick="AddTarget()"><i class="far fa-save"></i> บันทึก</button>
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
        CallData();
	});

    function number_format(number,decimal) {
        var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
        var formatter = new Intl.NumberFormat("en",options);
        return formatter.format(number)
    }

    $("#AC_PTASale, #AC_PTAReceipt, #AC_PTACost, #TAR_OULAA, #TAR_OUL, #TAR_ONL, #TAR_TT1, #TAR_TT2, #TAR_MT1, #TAR_MT2").on("focus", function(){
        if($(this).val() != "") {
            var num = parseFloat($(this).val().replace(/,/g, ""));
            $(this).val(num);
        }
    })
    $("#AC_PTASale, #AC_PTAReceipt, #AC_PTACost, #TAR_OULAA, #TAR_OUL, #TAR_ONL, #TAR_TT1, #TAR_TT2, #TAR_MT1, #TAR_MT2").on("focusout", function(){
        if(number_format($(this).val(),2) == "NaN") {
            $(this).val("0.00");
        }else{
            var number = number_format($(this).val(),2);
            $(this).val(number);
        }
    })

    function CallData() {
        // console.log($("#filt_year").val()+" | "+$("#filt_month").val());
        $(".overlay").show();
        $.ajax({
            url: "menus/account/ajax/ajaxdaily_receipt.php?a=CallData",
            type: "POST",
            data: { Year : $("#filt_year").val(), Month : $("#filt_month").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#Tbody").html(inval['Tbody']);
                    $("#Tfoot").html(inval['Tfoot']);
                });
                $(".overlay").hide();

                $(".adddata").on("click", function(e) {
                    e.preventDefault();
                    var DocDate = $(this).attr('data-date');
                    PickData(DocDate);
                });

                $(".addtarget").on("click", function(e) {
                    e.preventDefault();
                    var Year = $(this).attr('data-year');
                    var Month = $(this).attr('data-month');
                    PickTarget(Year,Month);
                });
            }
        })
    }

    function PickData(DocDate) {
        $.ajax({
            url: "menus/account/ajax/ajaxdaily_receipt.php?a=PickData",
            type: "POST",
            data: { ReceiptDate : DocDate, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#ReceiptDate").val(DocDate);
                    var [Y, M, D] = DocDate.split('-');
                    var Format_Date = ""+D+"/"+M+"/"+Y+"";
                    $("#Df_ReceiptDate").html(Format_Date);

                    $("#AC_PTASale").val(number_format(inval['AC_PTASale'],2));
                    $("#AC_PTAReceipt").val(number_format(inval['AC_PTAReceipt'],2));
                    $("#AC_PTACost").val(number_format(inval['AC_PTACost'],2));
                    
                    $("#ModalAddData").modal("show");
                });
            }
        })
    }

    function AddData() {
        $.ajax({
            url: "menus/account/ajax/ajaxdaily_receipt.php?a=UpdateData",
            type: "POST",
            data: { ReceiptDate    : $("#ReceiptDate").val(),
                    AC_PTASale     : $("#AC_PTASale").val(),
                    AC_PTAReceipt  : $("#AC_PTAReceipt").val(),
                    AC_PTACost  : $("#AC_PTACost").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#ModalAlert-head").html("<i class='fas fa-check-circle text-success' style='font-size: 75px;'></i>");
                    $("#ModalAlert-body").html("บันทึกข้อมูลเสร็จสิ้น");
                    $("#ModalAlert").modal("show");
                    CallData();
                });
            }
        })
    }

    function PickTarget(Year,Month) {
        $.ajax({
            url: "menus/account/ajax/ajaxdaily_receipt.php?a=PickTarget",
            type: "POST",
            data: { Year : Year, Month : Month, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TAR_OULAA").val(number_format(inval['TAR_OULAA'],2));
                    $("#TAR_OUL").val(number_format(inval['TAR_OUL'],2));
                    $("#TAR_ONL").val(number_format(inval['TAR_ONL'],2));
                    $("#TAR_TT1").val(number_format(inval['TAR_TT1'],2));
                    $("#TAR_TT2").val(number_format(inval['TAR_TT2'],2));
                    $("#TAR_MT1").val(number_format(inval['TAR_MT1'],2));
                    $("#TAR_MT2").val(number_format(inval['TAR_MT2'],2));

                    $("#ModalAddTarget").modal("show");
                });
            }
        })
    }

    function AddTarget() {
        $.ajax({
            url: "menus/account/ajax/ajaxdaily_receipt.php?a=UpdateTarget",
            type: "POST",
            data: { Year      : $("#filt_year").val(),
                    Month     : $("#filt_month").val(),
                    TAR_OULAA : $("#TAR_OULAA").val(),
                    TAR_OUL   : $("#TAR_OUL").val(),
                    TAR_ONL   : $("#TAR_ONL").val(),
                    TAR_TT1   : $("#TAR_TT1").val(),
                    TAR_TT2   : $("#TAR_TT2").val(),
                    TAR_MT1   : $("#TAR_MT1").val(),
                    TAR_MT2   : $("#TAR_MT2").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#ModalAlert-head").html("<i class='fas fa-check-circle text-success' style='font-size: 75px;'></i>");
                    $("#ModalAlert-body").html("บันทึกข้อมูลเสร็จสิ้น");
                    $("#ModalAlert").modal("show");
                    CallData();
                });
            }
        })
    }
</script> 
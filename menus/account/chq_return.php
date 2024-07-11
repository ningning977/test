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
                <!-- CONTENT TAB -->
                <ul class="nav nav-tabs" id="main-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#ChqList" class="btn-tabs nav-link active" id="ChqList-tab" data-bs-toggle="tab" data-bs-target="#ChqList" role="tab" data-tabs="0" aria-controls="ChqList" aria-selected="true">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการเช็คคืน
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#NewChq" class="btn-tabs nav-link" id="NewChq-tab" data-bs-toggle="tab" data-bs-target="#NewChq" role="tab" data-tabs="1" aria-controls="NewChq" aria-selected="false">
                            <i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มเช็คคืนใหม่
                        </a>
                    </li>
                </ul>
                <!-- END CONTENT TAB -->
                <div class="tab-content">
                    <!-- TAB 0 -->
                    <div class="tab-pane fade show active" id="ChqList" role="tabpanel" aria-labelledby="ChqList-tab">
                        <div class="row mt-4">
                            <div class="col-lg-3 col-md-4 col-9">
                                <div class="form-group mb-3">
                                    <label for="SearchBar">ค้นหา</label>
                                    <input type="text" class="form-control form-control-sm" name="SearchBar" id="SearchBar" placeholder="รหัสลูกค้า / รหัสเอกสาร / ชื่อลูกค้า / เลขที่เช็คเพื่อค้นหารายการ..." />
                                    <small class="text-muted">กดปุ่ม <i class="fas fa-search fa-fw fa-1x"></i> เพื่อค้นหาข้อมูลที่ไม่ปรากฏในตาราง</small>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-2 col-3">
                                <div class="form-group mb-3">
                                    <label for="SearchBtn">&nbsp;</label>
                                    <button type="button" class="btn btn-primary btn-sm btn-block" id="SearchBtn" onclick="SearchDoc('');"><i class="fas fa-search fa-fw fa-1x"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm" style="font-size: 12px;" id="ChqList">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="6.5%" rowspan="2">เลขที่เอกสาร</th>
                                            <th width="5.5%" rowspan="2">วันที่<br/>เซลส์รับทราบ</th>
                                            <th rowspan="2">ชื่อคู่ค้า</th>
                                            <th width="5.5%" rowspan="2">วันที่<br/>เช็คเด้ง</th>
                                            <th width="3.5%" rowspan="2">จำนวน<br/>(วัน)</th>
                                            <th rowspan="2">พนักงานขาย</th>
                                            <th rowspan="2">สาเหตุเช็คเด้ง</th>
                                            <th width="5.5%" rowspan="2">เลขที่เช็ค</th>
                                            <th width="5.5%" rowspan="2">จำนวนเงิน<br/>(บาท)</th>
                                            <th width="5.5%" rowspan="2">ยอดคงเหลือ<br/>(บาท)</th>
                                            <th rowspan="2">หมายเหตุ</th>
                                            <th colspan="4">ค่าปรับ (บาท)</th>
                                            <th width="4%" rowspan="2">จัดการ</th>
                                        </tr>
                                        <tr>
                                            <th width="3.5%">รวม</th>
                                            <th width="3.5%">เซลส์</th>
                                            <th width="3.5%">หนง.</th>
                                            <th width="3.5%">ผจก.</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot style="font-weight: bold;">
                                        <tr>
                                            <td class="text-right" colspan="8">รวมทั้งหมด</td>
                                            <td class="text-right" id="SUM_CheckSUM"></td>
                                            <td class="text-right text-danger" id="SUM_Applied"></td>
                                            <td>&nbsp;</td>
                                            <td class="text-right text-danger" id="SUM_FineALL"></td>
                                            <td class="text-right text-danger" id="SUM_FineSAL"></td>
                                            <td class="text-right text-danger" id="SUM_FineSUP"></td>
                                            <td class="text-right text-danger" id="SUM_FineMGR"></td>
                                            <td><button type="button" class="btn btn-success btn-sm w-100" onclick="ExportDoc();"><i class="fas fa-file-excel fa-fw fa-1x"></i></button></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- TAB 1 -->
                    <div class="tab-pane fade" id="NewChq" role="tabpanel" aria-labelledby="NewChq-tab">
                        <form  class="form" id="AddChq">
                            <div class="row mt-4">
                                <div class="col-lg-3 col-md-4 col-9">
                                    <div class="form-group mb-3">
                                        <label for="ChqDocNum">เลขที่เช็ค</label>
                                        <input type="text" class="form-control form-control-sm" name="ChqDocNum" id="ChqDocNum" placeholder="กรุณากรอกเลขที่เช็ค" />
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-2 col-3">
                                    <div class="form-group mb-3">
                                        <label for="btnSearch">&nbsp;</label>
                                        <button type="button" class="btn btn-primary btn-sm btn-block" id="btnSearch" onclick="SearchChq();"><i class="fas fa-search fa-fw fa-1x"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="CardCode">ชื่อคู่ค้า</label>
                                        <select class="form-control form-control-sm" name="CardCode" id="CardCode" data-live-search="true" data-size="10">
                                            <option value="" disabled selected>กรุณาเลือกชื่อคู่ค้า</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="SlpCode">ผู้แทนขาย</label>
                                        <select class="form-control form-control-sm" name="SlpCode" id="SlpCode" data-live-search="true" data-size="10">
                                            <option value="NULL" selected>กรุณาเลือกชื่อผู้แทนขาย</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12 col-12">
                                    <div class="form-group mb-3">
                                        <label for="ChqCauseReturn">สาเหตุเช็คเด้ง</label>
                                        <select class="form-select form-select-sm" name="ChqCauseReturn" id="ChqCauseReturn">
                                            <option value="" disabled selected>กรุณาเลือกสาเหตุ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="ChqAmount">จำนวนเงิน (บาท)</label>
                                        <input type="number" class="form-control form-control-sm text-right" name="ChqAmount" id="ChqAmount" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="DueAmount">ยอดคงเหลือ (บาท)</label>
                                        <input type="number" class="form-control form-control-sm text-right" name="DueAmount" id="DueAmount" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="ChqReturnDate">วันที่เช็คเด้ง</label>
                                        <input type="date" class="form-control form-control-sm" name="ChqReturnDate" id="ChqReturnDate" value="<?php echo date("Y-m-d"); ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="SaleReceiveDate">วันที่เซลส์รับทราบ</label>
                                        <input type="date" class="form-control form-control-sm" name="SaleReceiveDate" id="SaleReceiveDate" value="<?php echo date("Y-m-d"); ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label for="ChqRemark">หมายเหตุ</label>
                                        <input type="text" class="form-control form-control-sm" name="ChqRemark" id="ChqRemark" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg text-right">
                                    <button type="button" class="btn btn-primary" onclick="SaveChq();"><i class="fas fa-save fa-fw fa-1x"></i> บันทึก</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="AddDetail" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history fa-fw fa-1x"></i> ประวัติการจ่ายเงิน เอกสารเลขที่: <span id="dt_DocNum"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form id="AddDetailForm">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-hover table-bordered" style="font-size: 12px;" id="AppliedList">
                            <thead class="text-center">
                                <tr>
                                    <th width="10%">วันที่ชำระเงิน</th>
                                    <th>หมายเหตุ</th>
                                    <th width="10%">จำนวนเงิน<br/>(บาท)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot style="font-weight: bold;">
                                <tr>
                                    <td colspan="2" class="text-right">จำนวนเงิน</td>
                                    <td class="text-right" id="dt_SUMCheckSUM"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">ชำระมาแล้ว</td>
                                    <td class="text-right text-success" id="dt_SUMApplied"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">ยอดคงเหลือ</td>
                                    <td class="text-right text-danger" id="dt_SUMBalance"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <hr/>
                <h6><i class="fas fa-plus fa-fw fa-1x"></i> ชำระเพิ่มเติม</h6>
                <div class="row mt-4">
                    <div class="col-lg-3 col-12">
                        <div class="form-group mb-3">
                            <label for="add_applied">จำนวนเงิน (บาท)</label>
                            <input type="number" class="form-control form-control-sm text-right" name="add_applied" id="add_applied" />
                            <input type="hidden" class="form-control form-control-sm" name="add_chqid" id="add_chqid" readonly />
                        </div>
                    </div>
                    <div class="col-lg-7 col-12">
                        <div class="form-group mb-3">
                            <label for="add_remark">หมายเหตุ</label>
                            <input type="text" class="form-control form-control-sm" name="add_remark" id="add_remark" />
                        </div>
                    </div>
                    <div class="col-lg-2 col-12">
                        <div class="form-group form-check mb-3">
                            <label for="add_closed">&nbsp;</label>
                            <div class="checkbox pt-1">
                                <input type="checkbox" class="form-check-input" name="add_closed" id="add_closed" />
                                <label for="add_closed">ปิดบัญชี</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="SaveDetail();"><i class="fas fa-save fa-fw fa-1x"></i> บันทึก</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ShowResult" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<!-- MODAL SAVE SUCCESS -->
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

function GetCustomer() {
    $.ajax({
        url: "menus/account/ajax/ajaxchq_return.php?p=GetCustomer",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            var opt = "";
            $.each(obj,function(key,inval) {
                var Rows = parseFloat(inval['Rows']);
                for(i=0;i<Rows;i++) {
                    opt += "<option value='"+inval[i]['CardCode']+"'>"+inval[i]['CardCode']+" | "+inval[i]['CardName']+"</option>";
                }
            });
            $("#CardCode").append(opt).selectpicker();
        }
    });
}

function GetSlpName() {
    $.ajax({
        url: "menus/account/ajax/ajaxchq_return.php?p=GetSlpName",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            var opt = "";
            $.each(obj,function(key,inval) {
                var Rows = parseFloat(inval['Rows']);
                for(i=0;i<Rows;i++) {
                    opt += "<option value='"+inval[i]['SlpCode']+"'>"+inval[i]['SlpName']+"</option>";
                }
            });
            $("#SlpCode").append(opt).selectpicker();
        }
    });
}

function GetChqCause() {
    $.ajax({
        url: "menus/account/ajax/ajaxchq_return.php?p=GetChqCause",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            var opt = "";
            $.each(obj,function(key,inval) {
                var Rows = parseFloat(inval['Rows']);
                for(i=0;i<Rows;i++) {
                    opt += "<option value='"+inval[i]['ReturnCode']+"'>"+inval[i]['ReturnCode']+" | "+inval[i]['ReturnName']+"</option>";
                }
            });
            $("#ChqCauseReturn").append(opt);
        }
    });
}

function SearchDoc(DocNum) {
    var Search_bar = "";
    if(DocNum ==  "") {
        var search_bar = $("#SearchBar").val();
    } else {
        var search_bar = DocNum;
    }
    
    if(search_bar.length > 0) {
        $(".overlay").show();
        $.ajax({
            url: "menus/account/ajax/ajaxchq_return.php?p=SearchDoc",
            type: "POST",
            data: {
                SearchBar: search_bar
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    var Rows = parseFloat(inval['Rows']);
                    switch(Rows) {
                        case 0:
                            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                            $("#alert_body").html("ไม่พบรายการเช็คคืน<br/>กรุณาลองใหม่อีกครั้ง");
                            $("#alert_modal").modal('show');
                            break;
                        case 1:
                            $("#ShowResult h5.modal-title").html("<i class='fas fa-history fa-fw fa-1x'></i> ประวัติการจ่ายเงิน เอกสารเลขที่: "+inval['HD']['DocNum']);
                            var modal_body =
                                "<div class='row'>"+
                                    "<div class='table-responsive'>"+
                                        "<table class='table table-bordered table-sm' style='font-size: 12px;'>"+
                                            "<tr>"+
                                                "<th width='15%'>ชื่อคู่ค้า</th>"+
                                                "<td width='35%'>"+inval['HD']['CardCode']+"</td>"+
                                                "<th width='15%'>วันที่เช็คเด้ง</th>"+
                                                "<td width='35%'>"+inval['HD']['CHQ_DateReturn']+"</td>"+
                                            "</tr>"+
                                            "<tr>"+
                                                "<th>เลขที่เช็ค</th>"+
                                                "<td>"+inval['HD']['CHQ_No']+"</td>"+
                                                "<th>จำนวนเงิน (บาท)</th>"+
                                                "<td class='text-right text-danger' style='font-weight: bold;'>"+inval['HD']['CHQ_Amount']+"</td>"+
                                            "</tr>"+
                                            "<tr>"+
                                                "<th>สาเหตุเช็คเด้ง</th>"+
                                                "<td colspan='3'>"+inval['HD']['CauseReturn']+"</td>"+
                                            "</tr>"+
                                        "</table>"+
                                    "</div>"+
                                "</div>"+
                                "<hr>"+
                                "<div class='row'>"+
                                    "<div class='table-responsive'>"+
                                        "<table class='table table-bordered table-hover table-sm' style='font-size: 12px;'>"+
                                            "<thead class='text-center'>"+
                                                "<tr>"+
                                                    "<th width='15%'>วันที่<br/>ชำระเงิน</th>"+
                                                    "<th>หมายเหตุ</th>"+
                                                    "<th width='15%'>จำนวนเงิน<br/>(บาท)"+
                                                "</tr>"+
                                            "</thead>"+
                                            "<tbody>";
                            var Loop = parseFloat(inval['LOOP']);
                                    if(Loop == 0) {
                                        modal_body += "<tr><td class='text-center' colspan='3'>ไม่พบประวัติชำระ :(</td></tr>";
                                    } else {
                                        for(i = 0; i < Loop; i++) {
                                            modal_body +=
                                                "<tr>"+
                                                    "<td class='text-center'>"+inval['BD_'+i]['DatePaid']+"</td>"+
                                                    "<td>"+inval['BD_'+i]['Remark']+"</td>"+
                                                    "<td class='text-right'>"+inval['BD_'+i]['Amount']+"</td>"+
                                                "</tr>";
                                        }
                                    }
                            modal_body +=
                                            "</tbody>"+
                                            "<tfoot style='font-weight: bold;'>"+
                                                "<tr>"+
                                                    "<td colspan='2' class='text-right'>จำนวนเงิน</td>"+
                                                    "<td class='text-right'>"+inval['FT']['SUM_CheckSUM']+"</td>"+
                                                "</tr>"+
                                                "<tr>"+
                                                    "<td colspan='2' class='text-right'>ชำระมาแล้ว</td>"+
                                                    "<td class='text-right text-success'>"+inval['FT']['SUM_Applied']+"</td>"+
                                                "</tr>"+
                                                "<tr>"+
                                                    "<td colspan='2' class='text-right'>ยอดคงเหลือ</td>"+
                                                    "<td class='text-right text-danger'>"+inval['FT']['SUM_Balance']+"</td>"+
                                                "</tr>"+
                                            "</tfott>"+
                                        "</table>"+
                                    "</div>"+
                                "</div>";
                            $("#ShowResult div.modal-body").html(modal_body);
                            $("#ShowResult").modal("show");
                            break;
                        default:
                            $("#ShowResult h5.modal-title").html("<i class='fas fa-search fa-fw fa-1x'></i> ผลการค้นหา");
                            var modal_body =
                                "<div class='row'>"+
                                    "<div class='table-responsive'>"+
                                        "<table class='table table-bordered table-hover' style='font-size: 12px;'>"+
                                            "<thead class='text-center'>"+
                                                "<tr>"+
                                                    "<th width='10%'>เลขที่เอกสาร</th>"+
                                                    "<th width='10%'>วันที่เช็คเด้ง</th>"+
                                                    "<th>ชื่อคู่ค้า</th>"+
                                                    "<th width='10%'>เลขที่เช็ค</th>"+
                                                    "<th width='10%'>จำนวนเงิน<br/>(บาท)</th>"+
                                                    "<th width='10%'>ชำระมาแล้ว<br/>(บาท)</th>"+
                                                    "<th width='10%'>ยอดคงเหลือ<br/>(บาท)</th>"+
                                                "</tr>"+
                                            "</thead>"+
                                            "<tbody>";
                                        for(i = 0; i < Rows; i++) {
                                            var Balance = parseFloat(inval['BD_'+i]['CHQ_Balance']);
                                            if(Balance == 0) {
                                                modal_body +=
                                                "<tr class='table-success text-success'>";
                                            } else {
                                                modal_body +=
                                                "<tr>";
                                            }
                                            modal_body +=
                                                    "<td class='text-center'><a href='javascript:void(0);' onclick='SearchDoc(\""+inval['BD_'+i]['DocNum']+"\");'>"+inval['BD_'+i]['DocNum']+"</a></td>"+
                                                    "<td class='text-center'>"+inval['BD_'+i]['CHQ_DateReturn']+"</td>"+
                                                    "<td>"+inval['BD_'+i]['CardCode']+"</td>"+
                                                    "<td class='text-center'>"+inval['BD_'+i]['CHQ_No']+"</td>"+
                                                    "<td class='text-right'>"+inval['BD_'+i]['CHQ_Amount']+"</td>"+
                                                    "<td class='text-right text-success'>"+inval['BD_'+i]['CHQ_Applied']+"</td>"+
                                                    "<td class='text-right text-danger' style='font-weight: bold;'>"+inval['BD_'+i]['CHQ_Balance']+"</td>"+
                                                "</tr>";
                                        }
                            modal_body +=
                                            "</tbody>"+
                                        "</table>"+
                                    "</div>";
                                "</div>"+
                            $("#ShowResult div.modal-body").html(modal_body);
                            $("#ShowResult").modal("show");
                            break;
                    }
                    $(".overlay").hide();
                });
            }
        });
    } 
}

function ExportDoc() {
    $(".overlay").show();
    $.ajax({
        url: "menus/account/ajax/ajaxExportChqRT.php",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $(".overlay").hide();
                if(inval['ExportStatus'] == "SUCCESS") {
                    window.open("../../FileExport/ChqReturn/"+inval['FileName'],'_blank');
                } else {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("ไม่สามารถส่งออกได้เนื่องจากไม่มีรายการหนี้เกินกำหนด");
                    $("#alert_modal").modal('show');
                }
            });
        }
    });
}

function SearchChq() {
    var ChqDocNo = $("#ChqDocNum").val();
    if(ChqDocNo.length == 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/account/ajax/ajaxchq_return.php?p=SearchChq",
            type: "POST",
            data: {
                ChqDoc: ChqDocNo
            },
            success: function(result) {
                
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    var Rows = parseFloat(inval['Rows']);
                    if(Rows == 0) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่พบข้อมูลเช็คในระบบ SAP");
                        $("#alert_modal").modal('show');
                        $("#ChqDocNum").focus();
                    } else {
                        $("#CardCode, #SlpCode").selectpicker("destroy");
                        var CheckSum = parseFloat(inval['CheckSum']).toFixed(2);
                        $("#CardCode").val(inval['CardCode']).change().selectpicker();
                        $("#SlpCode").val(inval['SlpCode']).change().selectpicker();
                        $("#ChqAmount, #DueAmount").val(CheckSum);
                        $("#ChqReturnDate").val(inval['CheckDate']);
                    }
                });
            }
        });
    }
}

function SaveChq() {   
    var ErrorPoint = 0;
    var modal_body = "";

    if($("#ChqDocNum").val() == "") { 
        modal_body = "กรุณากรอกเลขที่เช็ค";
        ErrorPoint = ErrorPoint+1;
    } else if($("#CardCode").val() == "") {
        modal_body = "กรุณาเลือกชื่อคู่ค้า";
        ErrorPoint = ErrorPoint+1;
    } else if($("#ChqCauseReturn").val() == "") {
        modal_body = "กรุณาเลือกสาเหตุเช็คเด้ง";
        ErrorPoint = ErrorPoint+1;
    } else if($("#ChqAmount").val() == "") {
        modal_body = "กรุณาระบุจำนวนเงินหน้าเช็ค";
        ErrorPoint = ErrorPoint+1;
    }
    
    if(ErrorPoint > 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html(modal_body);
        $("#alert_modal").modal('show');
    } else {
        $(".overlay").show();
        var formData = new FormData($("#AddChq")[0]);
        $.ajax({
            url: "menus/account/ajax/ajaxchq_return.php?p=SaveChq",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(result) {
                $(".overlay").hide();
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    if(inval['SaveStatus'] == "SUCCESS") {
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                    } else {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่สามารถบันทึกได้ กรุณาติดต่อฝ่ายไอที");
                        $("#alert_modal").modal('show');
                    }
                });
            }
        });
    }
}

function GetList() {
    let row = "";
    $(".overlay").show();
    $("#SUM_CheckSUM, #SUM_Applied").html("0.00");
    $("#SUM_FineALL, #SUM_FineSAL, #SUM_FineSUP, #SUM_FineMGR").html("0");
    $.ajax({
        url: "menus/account/ajax/ajaxchq_return.php?p=GetList",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                if(inval['Rows'] == 0) {
                    row = "<tr><td class='text-center' colspan='16'>ไม่มีข้อมูล :(</td></tr>";
                    
                } else {
                    for(i = 0; i < inval['Rows']; i++) {
                        row += 
                            "<tr>"+
                                "<td class='text-center'>"+inval['BD_'+i]['DocNum']+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['CHQ_SaleReceive']+"</td>"+
                                "<td>"+inval['BD_'+i]['CardCode']+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['CHQ_DateReturn']+"</td>"+
                                "<td class='text-right'><strong>"+inval['BD_'+i]['DateDiff']+"</strong></td>"+
                                "<td>"+inval['BD_'+i]['SalesName']+"</td>"+
                                "<td>"+inval['BD_'+i]['CauseReturn']+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['CHQ_No']+"</td>"+
                                "<td class='text-right'>"+inval['BD_'+i]['CHQ_Amount']+"</td>"+
                                "<td class='text-right text-danger'>"+inval['BD_'+i]['Balance']+"</td>"+
                                "<td><input type='text' class='form-control form-control-sm' name='Remark_"+inval['BD_'+i]['CHQ_ID']+"' id='Remark_"+inval['BD_'+i]['CHQ_ID']+"' value='"+inval['BD_'+i]['Remark']+"' style='font-size: 12px;' /></td>"+
                                "<td class='text-right text-danger'><strong>"+inval['BD_'+i]['FineALL']+"</strong></td>"+
                                "<td class='text-right text-danger'>"+inval['BD_'+i]['FineSAL']+"</td>"+
                                "<td class='text-right text-danger'>"+inval['BD_'+i]['FineSUP']+"</td>"+
                                "<td class='text-right text-danger'>"+inval['BD_'+i]['FineMGR']+"</td>"+
                                "<td class='text-center'><button type='button' class='btn btn-outline-secondary btn-sm w-100' onclick='AddDetail("+inval['BD_'+i]['CHQ_ID']+");'><i class='fas fa-edit fa-fw fa-1x'></i></button></td>"+
                            "</tr>";
                    }
                    $("#SUM_CheckSUM").html(inval['FT']['SUM_CheckSUM']);
                    $("#SUM_Applied").html(inval['FT']['SUM_Applied']);

                    $("#SUM_FineALL").html(inval['FT']['SUM_FineALL']);
                    $("#SUM_FineSAL").html(inval['FT']['SUM_FineSAL']);
                    $("#SUM_FineSUP").html(inval['FT']['SUM_FineSUP']);
                    $("#SUM_FineMGR").html(inval['FT']['SUM_FineMGR']);
                }
                
            });
            $("#ChqList tbody").html(row);
            $(".overlay").hide();

            $("input[name^='Remark_']").focusout(function() {
                var content = $(this).val();
                if(content.length > 0) {
                    $(".overlay").show();
                    var RemarkAttr = $(this).attr("name")
                    var arrCHQ     = RemarkAttr.split("_");
                    var DocEntry   = arrCHQ[1];
                    $.ajax({
                        url: "menus/account/ajax/ajaxchq_return.php?p=SaveRemark",
                        type: "POST",
                        data: {
                            cid: DocEntry,
                            content: content
                        },
                        success: function(result) {
                            $(".overlay").hide();
                        }
                    });
                }
            });
        }
    });
}

function AddDetail(ChqID) {
    var CHQ_ID = ChqID;
    let row = "";
    $("#dt_SUMCheckSUM, #dt_SUMApplied, #dt_SUMBalance").html("0.00");
    $("#add_applied, #add_remark").val("");
    $("#add_closed").prop("checked",false);
    $.ajax({
        url: "menus/account/ajax/ajaxchq_return.php?p=GetDetail",
        type: "POST",
        data: {
            cid: CHQ_ID
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                if(inval['Rows'] == 0) {
                    row = "<tr><td class='text-center' colspan='3'>ไม่มีประวัติการชำระ :(</td></tr>";
                } else {
                    for(i=0; i < inval['Rows']; i++) {
                        row +=
                            "<tr>"+
                                "<td class='text-center'>"+inval['BD_'+i]['DatePaid']+"</td>"+
                                "<td>"+inval['BD_'+i]['Remark']+"</td>"+
                                "<td class='text-right'>"+inval['BD_'+i]['Applied']+"</td>"+
                            "</tr>";
                    }
                }

                $("#dt_SUMCheckSUM").html(inval['FT']['SumCheckSUM']);
                $("#dt_SUMApplied").html(inval['FT']['SumApplied']);
                $("#dt_SUMBalance").html(inval['FT']['SumBalance']);

                $("#dt_DocNum").html(inval['HD']['DocNum']);
                $("#add_chqid").val(inval['HD']['CHQ_ID']);
            });

            $("#AppliedList tbody").html(row);
        }
    });
    $("#AddDetail").modal("show");
}

function SaveDetail() {
    $("#AddDetail").modal("hide");
    $(".overlay").show();
    if($("#add_applied").val() == "" || $("#add_applied").val() == 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกจำนวนเงินก่อนบันทึกการชำระ");
        $("#alert_modal").modal('show');
    } else {
        var formData = new FormData($("#AddDetailForm")[0]);
        $.ajax({
            url: "menus/account/ajax/ajaxchq_return.php?p=SaveDetail",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(result) {
                $(".overlay").hide();
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    if(inval['SaveStatus'] == "SUCCESS") {
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            GetList();
                        });
                    } else {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่สามารถบันทึกได้ กรุณาติดต่อฝ่ายไอที");
                        $("#alert_modal").modal('show');
                    }
                });
            }
        });
    }
}

/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */

$(document).ready(function(){
    CallHead();
    GetCustomer();
    GetSlpName();
    GetChqCause();
    GetList();
});

$("#SearchBar").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#ChqList tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});
</script> 
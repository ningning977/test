<style type="text/css">
    .font-weight {
        font-weight: bold;
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

<div class="modal fade" id="PreviewIV" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดใบยืม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ORDER HEADER -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tr class="align-middle">
                                <th width="10%">ชื่อลูกค้า</th>
                                <td colspan="3"><input type="text" class="form-control-plaintext form-control-sm" id="view_IVCardName" readonly /></td>
                                <th width="10%">เลขที่เอกสาร</th>
                                <td width="10%">
                                    <input type="text" class="form-control-plaintext form-control-sm" id="view_IVDocNum" readonly />
                                    <input type="hidden" class="form-control" id="view_IVPickID" name="view_IVPickID" readonly />
                                    <input type="hidden" class="form-control" id="view_IVDocEntry" name="view_IVDocEntry" readonly />
                                    <input type="hidden" class="form-control" id="view_IVDocType" name="view_IVDocType" readonly />
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานขาย</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="view_IVSlpName" readonly /></td>
                                <th width="10%">วันที่เอกสาร</th>
                                <td width="10%"><input type="date" class="form-control form-control-sm" name="view_IVDocDate" id="view_IVDocDate" readonly /></td>
                                <th>วันที่กำหนดชำระ</th>
                                <td><input type="date" class="form-control form-control-sm" name="view_IVDocDueDate" id="view_IVDocDueDate" readonly /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>หมายเหตุ</th>
                                <td colspan="3"><input type="text" class="form-control-plaintext form-control-sm" name="view_IVComment" id="view_IVComment" readonly /></td>
                                <th>เอกสารอ้างอิง</th>
                                <td><input type="text" class="form-control-plaintext form-control-sm" name="view_IVUPONo" id="view_IVUPONo" readonly /></td>
                            </tr>
                        </table>
                        <!-- ORDER TAB -->
                        <ul class="nav nav-tabs mt-4" id="order-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#view_IVItemList" class="btn btn-tabs nav-link active" id="view_IVItemTab" data-bs-toggle="tab" data-bs-target="#view_IVItemList" role="tab" data-tabs="0" aria-controls="view_IVItemList" aria-selected="false" style="font-size: 12px;">
                                    <i class="fas fa-list fa-fw fa-1x"></i> รายการสินค้า
                                </a>
                            </li>
                        </ul>
                        <!-- CONTENT TAB -->
                        <div class="tab-content mt-2">
                            <div class="tab-pane show active" id="view_IVItemList" role="tabpanel" aria-labelledby="view_IVItemTab">
                                <table class="table table-bordered table-hover table-sm" id="IVItem" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th width="10%">รหัสสินค้า</th>
                                            <th width="10%">บาร์โค้ด</th>
                                            <th>ชื่อสินค้า</th>
                                            <th width="5%">คลัง</th>
                                            <th width="10%"colspan="2">จำนวน</th>
                                            <th width="10%">ราคา/หน่วย</th>
                                            <th width="15%">ส่วนลด</th>
                                            <th width="10%">มูลค่าสินค้า</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="text-right">
                                        <tr>
                                            <th colspan="9">ราคาก่อนหักภาษีมูลค่าเพิ่ม</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_IVDocTotal" id="view_IVDocTotal" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="9">ภาษีมูลค่าเพิ่ม</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_IVVatSum" id="view_IVVatSum" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="9">ราคาสุทธิ</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_IVSumTotal" id="view_IVSumTotal" style="font-weight: bold;" readonly /></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <p>ผู้จัดทำ: <span id="view_IVOwnerName"></span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <div class="row mt-2">
                    <div class="col-3">
                        <div class="form-group">
                            <label for="filt_sale">เลือกพนง.ขาย</label>
                            <select class="form-control form-control-sm" id="filt_sale" data-live-search="true" data-size="5">
                                <option value="" selected disabled>กรุณาเลือก</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" style="min-height: 512px;">
                    <table class="table table-bordered table-hover table-sm" id="PAList" style="font-size: 11px;">
                        <thead style="background-color: #9A1118;">
                            <tr>
                                <th class="dt-head-center text-white" width="3%">No.</th>
                                <th class="dt-head-center text-white" width="6.5%">เลขที่<br/>เอกสาร</th>
                                <th class="dt-head-center text-white" width="6.5%">วันที่<br/>เอกสาร</th>
                                <th class="dt-head-center text-white">รายชื่อลูกค้า</th>
                                <th class="dt-head-center text-white">ชื่อผู้เบิก</th>
                                <th class="dt-head-center text-white">รายการสินค้า</th>
                                <th class="dt-head-center text-white" width="5.5%">มูลค่า<br/>ต่อตัว</th>
                                <th class="dt-head-center text-white" width="5%">จำนวน<br/>ที่เบิก</th>
                                <th class="dt-head-center text-white" width="5%">จำนวน<br/>ที่คืน</th>
                                <th class="dt-head-center text-white" width="5%">จำนวน<br/>คงเหลือ</th>
                                <th class="dt-head-center text-white" width="6%">มูลค่า<br/>รวม</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="10" class="text-right">รวมทั้งหมด</th>
                                <th class="text-right"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
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

function number_format(number,decimal) {
    var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
    var formatter = new Intl.NumberFormat("en",options);
    return formatter.format(number)
}

function GetEmpList() {
    $.ajax({
        url: "menus/sale/ajax/ajaxpa_noreturn.php?p=GetEmpList",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                var Rows = parseFloat(inval['Rows']);
                var Opt  = "";
                for(i = 0; i < Rows; i++) {
                    Opt += "<option value='"+inval[i]['OptVal']+"'>"+inval[i]['OptTxt']+"</option>";
                }
                $("#filt_sale").append(Opt).selectpicker();
            });
        }
    });
<?php if($_SESSION['uClass'] == 18) { ?>
    $("#filt_sale").val("<?php echo $_SESSION['ukey']; ?>").change();
<?php } ?>
}

function GetPAList(SlpCode) {
    var filt_sale = SlpCode;
    $("#PAList").dataTable().fnClearTable();
    $("#PAList").dataTable().fnDraw();
    $("#PAList").dataTable().fnDestroy();

    $("#PAList").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 20,
        "dom": "Bfrtip",

        "buttons": [ "excel" ],

        "ajax": {
            url: "menus/sale/ajax/ajaxpa_noreturn.php?p=GetPAList",
            type: "POST",
            data: { filt_sale: filt_sale },
            dataType: "json",
            dataSrc: "0"
        },

        "columns": [
            { "data": "no", class: "dt-body-right border-start border-bottom" },
            { "data": "DocNum", class: "dt-body-center border-start border-bottom" },
            { "data": "DocDate", class: "dt-body-center border-start border-bottom" },
            { "data": "Customer", class: "border-start border-bottom" },
            { "data": "ShipToCode", class: "border-start border-bottom" },
            { "data": "ItemDetail", class: "border-start border-bottom" },
            { "data": "PriceAfVAT", class: "dt-body-right border-start border-bottom", render: $.fn.dataTable.render.number(',','.',2,'') },
            { "data": "Quantity", class: "dt-body-right border-start border-bottom", render: $.fn.dataTable.render.number(',','.',0,'') },
            { "data": "Returned", class: "dt-body-right border-start border-bottom text-success", render: $.fn.dataTable.render.number(',','.',0,'') },
            { "data": "OpenQty", class: "dt-body-right border-start border-bottom text-danger", render: $.fn.dataTable.render.number(',','.',0,'') },
            { "data": "LineTotal", class: "dt-body-right border-start border-bottom text-danger font-weight", render: $.fn.dataTable.render.number(',','.',2,'') }
        ],

        "createdRow": function (row, data, dataIndex, cells) {
            if(data.DateDiff > 6) {
                $(row).addClass("table-danger");
            }
        },
        
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();
            var intVal = function(i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            }
            total = api.column(10).data().reduce(function(a,b) {
                return intVal(a) + intVal(b);
            },0);
            pageTotal = api.column(10, { page: 'current' }).data().reduce(function(a,b) {
                return intVal(a) + intVal(b);
            },0);
            $(api.column(10).footer()).html(number_format(total,2));
        }

    });
}

/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
$(document).ready(function(){
    CallHead();
    GetEmpList();
    $("#filt_sale").on("change",function() {
        var filt_sale = $(this).val();
        GetPAList(filt_sale);
    });
    
});

function Detail(DocEntry) {
        $.ajax({
            url: "menus/sale/ajax/ajaxpa_noreturn.php?p=Detail",
            type: "POST",
            data: { DocEntry : DocEntry, },
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    //$("#HDetail").html(DocNum);
                    //$("#TableDetail tbody").html(inval['Data']);
                    $("#view_IVCardName").val(inval['CardName']);
                    $("#view_IVDocNum").val(inval['DocNum']);
                    $("#view_IVSlpName").val(inval['SaleName']);
                    $("#view_IVDocDate").val(inval['DocDate']);
                    $("#view_IVDocDueDate").val(inval['DocDuDate']);
                    $("#view_IVComment").val(inval['Remark']);
                    $("#PreviewIV").modal("show");
                });
            }
        })
    }
</script> 
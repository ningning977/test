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

<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="filt_year">เลือกปี</label>
                            <select name="filt_year" id="filt_year" class="form-select form-select-sm" onchange='CallData();'>
                            <?php
                                for($y = $this_year; $y >= $start_year; $y--) {
                                    if($y == $this_year) {
                                        $y_slct = " selected";
                                    } else {
                                        $y_slct = "";
                                    }
                                    echo "<option value='$y'$y_slct>$y</option>";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="filt_month">เลือกเดือน</label>
                            <select name="filt_month" id="filt_month" class="form-select form-select-sm" onchange='CallData();'>
                            <?php
                                for($m = 1; $m <= 12; $m++) {
                                    if($m == $this_month) {
                                        $m_slct = " selected";
                                    } else {
                                        $m_slct = "";
                                    }
                                    echo "<option value='$m'$m_slct>".FullMonth($m)."</option>";
                                }
                                $DeptCode = $_SESSION['DeptCode'];
                                if(($DeptCode == "DP001" || $DeptCode == "DP002")) {
                                    $opt_dis = NULL;
                                } else {
                                    $opt_dis = " disabled";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <?php if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP007") { ?>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Export();'><i class="fas fa-file-excel fa-fw"></i> Excel</button>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='Table1'>
                                <thead style='font-size: 12px;'>
                                    <tr>
                                        <th rowspan='2' class='text-center border-top'>เลขที่ P/O</th>
                                        <th rowspan='2' class='text-center border-top'>ชื่อลูกค้า</th>
                                        <th rowspan='2' class='text-center border-top'>พนักงานขาย</th>
                                        <th colspan='4' class='text-center border-top'>ใบสั่งขาย (S/O)</th>
                                        <th colspan='4' class='text-center border-top'>ใบกำกับภาษี (Invoice)</th>
                                        <th rowspan='2' class='text-center border-top'>เปรียบเทียบ</th>
                                    </tr>
                                    <tr>
                                        <th class='text-center'>เลขที่เอกสาร</th>
                                        <th class='text-center'>วันที่เอกสาร</th>
                                        <th class='text-center'>วันที่กำหนดส่ง</th>
                                        <th class='text-center'>มูลค่า (บาท)</th>
                                        <th class='text-center'>เลขที่เอกสาร</th>
                                        <th class='text-center'>วันที่เปิดบิล</th>
                                        <th class='text-center'>วันที่กำหนดชำระ</th>
                                        <th class='text-center'>มูลค่า (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 11.5px;'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class='modal fade' id='ModalViewData' tabindex='-1' role='dialog' data-bs-backdrop='static' aria-hidden='true'>
    <div class='modal-dialog modal-full'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title'><i class="fas fa-shopping-basket" style='font-size: 15px;'></i>&nbsp;&nbsp;รายละเอียดการเปิด S/O</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class="row sticky-top" style='background-color: #FFF;'>
                    <div class="col-lg">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tr class="align-middle">
                                <th width="10%">ชื่อลูกค้า</th>
                                <td colspan="3"><input type="text" class="form-control-plaintext form-control-sm" id="view_CUTCardName" readonly /></td>
                                <th width="10%">เลขที่ใบสั่งขาย</th>
                                <td width="10%">
                                    <input type="text" class="form-control-plaintext form-control-sm" id="view_CUTDocNum" readonly /></td>
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานขาย</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="view_CUTSlpName" readonly /></td>
                                <th width="10%">วันที่สั่งสินค้า</th>
                                <td width="10%"><input type="date" class="form-control form-control-sm" name="view_CUTDocDate" id="view_CUTDocDate" readonly /></td>
                                <th>วันที่กำหนดส่ง</th>
                                <td><input type="date" class="form-control form-control-sm" name="view_CUTDocDueDate" id="view_CUTDocDueDate" readonly /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>หมายเหตุ</th>
                                <td colspan="5"><input type="text" class="form-control-plaintext form-control-sm" name="view_CUTComment" id="view_CUTComment" readonly /></td>
                            </tr>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-bordered table-hover' style='font-size: 12px;' id='Table2'>
                                <thead style='background-color: #FFF;'>
                                    <tr>
                                        <th width='5%' rowspan='2' class='text-center border-top'>ลำดับ</th>
                                        <th width='10%' rowspan='2' class='text-center border-top'>รหัสสินค้า</th>
                                        <th width='10%' rowspan='2' class='text-center border-top'>บาร์โค้ด</th>
                                        <th width='30%' rowspan='2' class='text-center border-top'>ชื่อสินค้า</th>
                                        <th width='10%' rowspan='2' class='text-center border-top'>คลัง</th>
                                        <th colspan='2' class='text-center border-top'>จำนวน</th>
                                        <th width='7%' rowspan='2' class='text-center border-top'>หน่วย</th>
                                    </tr>
                                    <tr>
                                        <th width='14%' class='text-center'>สั่งซื้อ</th>
                                        <th width='14%' class='text-center'>เปิดบิล</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button>
            </div>
        </div>
    </div>
</div>       

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
    CallData();
});

function CallData() {
    let Year  = $("#filt_year").val();
    let Month = $("#filt_month").val();
    $("#Table1").dataTable().fnClearTable();
    $("#Table1").dataTable().fnDraw();
    $("#Table1").dataTable().fnDestroy();
    $("#Table1").DataTable({
        "ajax": {
            url: "menus/sale/ajax/ajaxSoStatus.php?a=CallData",
            type: "POST",
            data: { Year : Year, Month : Month, },
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "U_PoNo",     class: "border-start border-bottom" },
            { "data": "CardName",   class: "border-start border-bottom" },
            { "data": "SlpName",    class: "border-start border-bottom" },
            { "data": "SoDocNum",   class: "dt-body-center border-start border-bottom" },
            { "data": "SoDocDate",  class: "dt-body-center border-start border-bottom" },
            { "data": "SoDueDate",  class: "dt-body-center border-start border-bottom" },
            { "data": "SoDocTotal", class: "dt-body-right border-start border-bottom" },
            { "data": "IvDocNum",   class: "dt-body-center border-start border-bottom" },
            { "data": "IvDocDate",  class: "dt-body-center border-start border-bottom" },
            { "data": "IvDueDate",  class: "dt-body-center border-start border-bottom" },
            { "data": "IvDocTotal", class: "dt-body-right border-start border-bottom" },
            { "data": "ViewData",   class: "dt-body-center border-start border-bottom border-end" },
        ],
        "columnDefs": [
            { "width": "9.5%", "targets": 0 },
            { "width": "22%", "targets": 1 },
            { "width": "14%", "targets": 2 },
            { "width": "7%", "targets": 3 },
            { "width": "5.5%", "targets": 4 },
            { "width": "5.5%", "targets": 5 },
            { "width": "5.8%", "targets": 6 },
            { "width": "6.5%", "targets": 7 },
            { "width": "5.5%", "targets": 8 },
            { "width": "6.6%", "targets": 9 },
            { "width": "5.5%", "targets": 10 },
            { "width": "3%", "targets": 11 },
        ],
        "createdRow": function (row, data, dataIndex, cells) {
          if(data.SoDocTotal !== data.IvDocTotal) {
              $(row).addClass("table-danger text-primary");
          }
        },
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "ordering": false,
    });
}

function ViewData(SoDocEntry) {
    $.ajax({
        url: "menus/sale/ajax/ajaxSoStatus.php?a=ViewData",
        type: "POST",
        data: { SoDocEntry : SoDocEntry, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                if(inval['Row'] != 0) {
                    $("#view_CUTCardName").val(inval['CardCode']);
                    $("#view_CUTDocNum").val(inval['SoDocNum']);
                    $("#view_CUTSlpName").val(inval['SlpName']);
                    $("#view_CUTDocDate").val(inval['DocDate']);
                    $("#view_CUTDocDueDate").val(inval['DocDueDate']);
                    $("#view_CUTComment").val(inval['Comments']);

                    let Tbody = "";
                    for(let i = 1; i <= inval['Row']; i++) {
                        let cColor = '';
                        if(parseInt(inval[i]['IvQty']) == 0) {
                            cColor = "table-danger text-primary";
                        }else if(parseInt(inval[i]['IvQty']) < parseInt(inval[i]['SoQty'])){
                            cColor = "table-warning text-warning";
                        }
                        Tbody+="<tr class='"+cColor+"'>"+
                                    "<td class='text-center'>"+i+"</td>"+
                                    "<td class='text-center'>"+inval[i]['ItemCode']+"</td>"+
                                    "<td class='text-center'>"+inval[i]['CodeBars']+"</td>"+
                                    "<td>"+inval[i]['Dscription']+"</td>"+
                                    "<td class='text-center'>"+inval[i]['WhsCode']+"</td>"+
                                    "<td class='text-right'>"+inval[i]['SoQty']+"</td>"+
                                    "<td class='text-right fw-bolder'>"+inval[i]['IvQty']+"</td>"+
                                    "<td>"+inval[i]['UnitMsr']+"</td>"+
                                "</tr>";
                    }
                    $("#Table2 tbody").html(Tbody);
                    $("#ModalViewData").modal("show");
                }
            });
        }
    })
}

function Export() {
    const Year  = $("#filt_year").val();
    const Month = $("#filt_month").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxSoStatus.php?a=Export",
        type: "POST",
        data: { Year : Year, Month : Month, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/SoStatus/"+inval['FileName'],'_blank');
            });
        }
    })
}
</script> 
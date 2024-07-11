<div class='card h-100'>
    <div class='card-header'>
        <h4><i class='fas fa-dollar-sign fa-fw fa-1x'></i> ยอดขายทีมรายบุคคล</h4>
    </div>
    <div class='card-body'>
        <div class="row">
            <div class="col-lg">
                <nav>
                    <div class="nav nav-tabs" id="team-tab" role="tablist">
                        <?php 
                        switch ($_SESSION['DeptCode']){
                            case 'DP005' : 
                                echo '<button class="nav-link text-primary active" id="TT2-team-tab" data-bs-toggle="tab" data-bs-target="#TT2-team" type="button" role="tab" aria-controls="TT2-team" aria-selected="false">ทีม ตจว.</button>';
                            break;
                            case 'DP006' : 
                                echo '<button class="nav-link text-primary active" id="MT1-team-tab" data-bs-toggle="tab" data-bs-target="#MT1-team" type="button" role="tab" aria-controls="MT1-team" aria-selected="false">ทีม โมเดิร์นเทรด 1</button>';
                            break;
                            case 'DP007' : 
                                echo '<button class="nav-link text-primary active" id="MT2-team-tab" data-bs-toggle="tab" data-bs-target="#MT2-team" type="button" role="tab" aria-controls="MT2-team" aria-selected="false">ทีม โมเดิร์นเทรด 2</button>';
                            break;
                            case 'DP008' : 
                               echo '<button class="nav-link text-primary active" id="TT1-team-tab" data-bs-toggle="tab" data-bs-target="#TT1-team" type="button" role="tab" aria-controls="TT1-team" aria-selected="false">ทีม กทม.</button>';
                               echo '<button class="nav-link text-primary" id="OUL-team-tab" data-bs-toggle="tab" data-bs-target="#OUL-team" type="button" role="tab" aria-controls="OUL-team" aria-selected="false">ทีม หน้าร้าน</button>';
                            break;
                        }
                        ?>
                    </div>
                </nav>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-auto">
                <div class="form-group">
                    <label for="EmpSelect">เลือกปี</label>
                    <select class="form-select form-select-sm " name="txtYear" id="txtYear" onchange="IndiSale();">
                        <?php 
                            for($y = date('Y'); $y >= 2023; $y--) {
                                echo (($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>");
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group">
                    <label for="EmpSelect">เลือเดือน</label>
                    <select class="form-select form-select-sm " name="txtMonth" id="txtMonth" onchange="IndiSale();">
                        <?php 
                            for($m = 1; $m <= 12; $m++) {
                                echo (($m == date("m")) ? "<option value='$m' selected>".FullMonth($m)."</option>" : "<option value='$m'>".FullMonth($m)."</option>");
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <?php 
        switch ($_SESSION['DeptCode']){
                case 'DP005' : //TT2
                    $TeamArr = [ 'TT2' ];
                break;
                case 'DP006' : //MT1
                    $TeamArr = [ 'MT1' ];
                break;
                case 'DP007' : //MT2
                    $TeamArr = [ 'MT2' ];
                break;
                case 'DP008' : //TT1 //OUL
                    $TeamArr = [ 'TT1', 'OUL' ];
                break;
            }
        ?>
        <div class="tab-content mt-2" id="nav-tabContent">
            <?php for($t = 0; $t < count($TeamArr); $t++) { ?>
                <div class="tab-pane fade <?php if($t == 0){ echo 'show active'; } ?>" id="<?php echo $TeamArr[$t]; ?>-team" role="tabpanel" aria-labelledby="<?php echo $TeamArr[$t]; ?>-team-tab">
                    <div class="table-responsive">
                        <table class='table table-sm table-bordered table-hover' id='Table<?php echo $TeamArr[$t]; ?>'>
                            <thead class='bg-primary text-white'>
                                <tr class='text-center'>
                                    <th rowspan='2'>ทีม</th>
                                    <th rowspan='2'>พนักงานขาย</th>
                                    <th colspan='3'>ยอดขายเดือน <span class='ShowMonthSale'></span></th>
                                    <th colspan='3'>ยอดขายสะสมปี <span class='ShowSumSale'></span></th>
                                </tr>
                                <tr class='text-center'>
                                    <th>เป้าขาย</th>
                                    <th>ยอดขาย</th>
                                    <th>% ยอดขาย</th>
                                    <th>เป้าขาย</th>
                                    <th>ยอดขาย</th>
                                    <th>% ยอดขาย</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalViewINV" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายงานยอดขายรายบุคคล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary active" id="Sales-tab" data-bs-toggle="tab" data-bs-target="#Sales" type="button" role="tab" aria-controls="Sales" aria-selected="false">บิลขาย</button>
                                <button class="nav-link text-primary" id="Return-tab" data-bs-toggle="tab" data-bs-target="#Return" type="button" role="tab" aria-controls="Return" aria-selected="false">บิลยืม</button>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="tab-content mt-3" id="nav-tabdata">
                    <div class="tab-pane fade show active" id="Sales" role="tabpanel" aria-labelledby="Sales-tab">
                        <div class="row">
                            <div class="col">
                                <div class="table-responsive">
                                    <table class='table table-sm table-hover table-bordered' id='tbModalViewINV' style='font-size: 12px;'>
                                        <thead>
                                            <tr class='text-center'>
                                                <th rowspan='1' width='5%' class='text-center border'>No.</th>
                                                <th rowspan='1' width='10%' class='text-center border'>เลขที่เอกสาร</th>
                                                <th rowspan='1' width='10%' class='text-center border'>วันที่เอกสาร</th>
                                                <th rowspan='1' class='text-center border'>ชื่อร้านค้า</th>
                                                <th rowspan='1' class='text-center border'>ที่อยู่ร้านค้า</th>
                                                <th rowspan='1' width='10%' class='text-center border'>ยอดขาย</th>
                                                <th rowspan='1' width='5%' class='text-center border'>GP</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Return" role="tabpanel" aria-labelledby="Return-tab">
                        <div class="row">
                            <div class="col">
                                <div class="table-responsive">
                                    <table class='table table-sm table-hover table-bordered' id='tbViewReturn' style='font-size: 12px;'>
                                        <thead>
                                            <tr class='text-center'>
                                                <th rowspan='1' width='5%' class='text-center border'>No.</th>
                                                <th rowspan='1' width='10%' class='text-center border'>เลขที่เอกสาร</th>
                                                <th rowspan='1' width='10%' class='text-center border'>วันที่เอกสาร</th>
                                                <th rowspan='1' class='text-center border'>ชื่อร้านค้า</th>
                                                <th rowspan='1' width='25%' class='text-center border'>ผู้แทนขาย</th>
                                                <th rowspan='1' width='10%' class='text-center border'>มูลค่า</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="PreviewIV" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดใบกำกับภาษี</h5>
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
                                <th width="10%">เลขที่ใบกำกับภาษี</th>
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
                                <th width="10%">วันที่ใบกำกับภาษี</th>
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
                            <li class="nav-item" role="presentation">
                                <a href="#view_IVAttachList" class="btn btn-tabs nav-link disabled" id="view_IVAttachTab" data-bs-toggle="tab" data-bs-target="#view_IVAttachList" role="tab" data-tabs="0" aria-controls="view_IVAttachList" aria-selected="true" style="font-size: 12px;">
                                    <i class="fas fa-paperclip fa-fw fa-1x"></i> เอกสารแนบ
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
                            <div class="tab-pane" id="view_IVAttachList" role="tabpanel" aria-labelledby="view_IVAttachTab">
                                <table class="table table-bordered table-hover table-sm" id="IVAttachItem" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th>ชื่อเอกสารแนบ</th>
                                            <th width="7.5%">ดาวน์โหลด</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div class="alert alert-warning text-center">
                                    <strong>คำเตือน!</strong> กรุณาดาวน์โหลดไฟล์ผ่านเครือข่ายภายในบริษัทฯ เท่านั้น<br/>หากดาวน์โหลดไม่ได้กรุณาติดตั้ง <a href="https://chrome.google.com/webstore/detail/enable-local-file-links/nikfmfgobenbhmocjaaboihbeocackld" target="_blank">ส่วนขยาย Google Chrome <i class="fas fa-external-link-alt fa-fw fa-1x"></i></a> ก่อนดาวน์โหลดอีกครั้ง
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        IndiSale();
    });

    function fMonth(nomount){
        let w = "";
        switch(parseInt(nomount)){
            case 1: w = 'มกราคม'; break;
            case 2: w = 'กุมภาพันธ์'; break;
            case 3: w = 'มีนาคม'; break;
            case 4: w = 'เมษายน'; break;
            case 5: w = 'พฤษภาคม'; break;
            case 6: w = 'มิถุนายน'; break;
            case 7: w = 'กรกฎาคม'; break;
            case 8: w = 'สิงหาคม'; break;
            case 9: w = 'กันยายน'; break;
            case 10: w = 'ตุลาคม'; break;
            case 11: w = 'พฤศจิกายน'; break;
            case 12: w = 'ธันวาคม'; break;
        }
        return w;
    }

    function tMonth(numMount){
        switch(parseInt(numMount)){
            case 1: return 'ม.ค.'; break;
            case 2: return 'ก.พ.'; break;
            case 3: return 'มี.ค.';	break;
            case 4: return 'เม.ย.'; break;
            case 5: return 'พ.ค.'; break;
            case 6: return 'มิ.ย.';	break;
            case 7: return 'ก.ค.'; break;
            case 8: return 'ส.ค.'; break;
            case 9: return 'ก.ย.'; break;
            case 10: return 'ต.ค.';	break;
            case 11: return 'พ.ย.';	break;
            case 12: return 'ธ.ค.';	break;
        }

    }

    function SlpDetail(slp) {
        let Year = $("#txtYear").val();
        let Month = $("#txtMonth").val();
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=slpdetail",
            type: "POST",
            data: { SlpCode : slp,Year: Year, Month: Month },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#tbModalViewINV").dataTable().fnClearTable();
                    $("#tbModalViewINV").dataTable().fnDraw();
                    $("#tbModalViewINV").dataTable().fnDestroy();
                    $("#tbModalViewINV tbody").html(inval['Data']);
                    $('#tbModalViewINV').DataTable({
                        "columnDefs": [
                            { "width": "5%", "targets": 0 },
                            { "width": "10%", "targets": 1 },
                            { "width": "10%", "targets": 2 },
                            { "width": "", "targets": 3 },
                            { "width": "", "targets": 4 },
                            { "width": "10%", "targets": 5 },
                            { "width": "5%", "targets": 6 }
                        ],
                        "responsive": true, 
                        "lengthChange": false, 
                        "autoWidth": false,
                        "pageLength": 20,
                        "ordering": false,
                        "dom": 'Bfrtip',
                        "buttons": [{ 
                            "extend": 'excelHtml5',
                            "footer": true, 
                        }]
                    });
                    $("#tbViewReturn").dataTable().fnClearTable();
                    $("#tbViewReturn").dataTable().fnDraw();
                    $("#tbViewReturn").dataTable().fnDestroy();
                    $("#tbViewReturn tbody").html(inval['Data2']);
                    $('#tbViewReturn').DataTable({
                        "columnDefs": [
                            { "width": "5%", "targets": 0 },
                            { "width": "10%", "targets": 1 },
                            { "width": "10%", "targets": 2 },
                            { "width": "", "targets": 3 },
                            { "width": "25", "targets": 4 },
                            { "width": "10%", "targets": 5 },
                        ],
                        "responsive": true, 
                        "lengthChange": false, 
                        "autoWidth": false,
                        "pageLength": 20,
                        "ordering": false,
                        "dom": 'Bfrtip',
                        "buttons": [{ 
                            "extend": 'excelHtml5',
                            "footer": true, 
                        }]
                    });
                    $("#ModalViewINV").modal("show");
                });
            }
        })
    }

    function CallIV(DocEntry, DocType) {
        $(".overlay").show();
        $.ajax({
            url: "menus/sale/ajax/ajaxordermng.php?p=CallIV",
            type: "POST",
            data: {
                DocEntry: DocEntry,
                DocType: DocType
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    /* HEADER */
                    $("#view_IVDocEntry").val(inval['HD']['DocEntry']);
                    $("#view_IVDocNum").val(inval['HD']['IVDocNum']);
                    $("#view_IVDocType").val(inval['HD']['DocType']);
                    $("#view_IVCardName").val(inval['HD']['CardCode']);
                    $("#view_IVSlpName").val(inval['HD']['SlpName']);
                    $("#view_IVDocDate").val(inval['HD']['DocDate']);
                    $("#view_IVDocDueDate").val(inval['HD']['DocDueDate']);
                    $("#view_IVComment").val(inval['HD']['Comments']);
                    $("#view_IVUPONo").val(inval['HD']['U_PONo']);
                    $("#view_IVPickerName").val(inval['HD']['PickUkey']).change();
                    $("#view_IVTablePack").val(inval['HD']['TablePack']).change();

                    /* DETAIL */
                    var r = parseFloat(inval['Rows']);
                    var row;
                    var visorder = 1;
                    for(i = 0; i < r; i++) {
                        var Discount = "";
                        if(inval['BD_'+i]['Discount'] != null) {
                            Discount = inval['BD_'+i]['Discount'];
                        }
                        row +=
                            "<tr>"+
                                "<td class='text-right'>"+visorder+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['ItemCode']+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['CodeBars']+"</td>"+
                                "<td>"+inval['BD_'+i]['Dscription']+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['WhsCode']+"</td>"+
                                "<td width='5%' class='text-right'>"+number_format(inval['BD_'+i]['Quantity'],0)+"</td>"+
                                "<td width='5%'>"+inval['BD_'+i]['UnitMsr']+"</td>"+
                                "<td class='text-right'>"+number_format(inval['BD_'+i]['PriceBefDi'],3)+"</td>"+
                                "<td class='text-center'>"+Discount+"</td>"+
                                "<td class='text-right'>"+number_format(inval['BD_'+i]['LineTotal'],2)+"</td>"+
                            "</tr>";
                        visorder++;
                    }
                    $("#IVItem tbody").html(row);

                    /* FOOTER */
                    var SumTotal = parseFloat(inval['FT']['DocTotal']) - parseFloat(inval['FT']['VatSum']);
                    $("#view_IVDocTotal").val(number_format(SumTotal,2));
                    $("#view_IVVatSum").val(number_format(inval['FT']['VatSum'],2));
                    $("#view_IVSumTotal").val(number_format(inval['FT']['DocTotal'],2));
                    $("#view_IVOwnerName").html(inval['FT']['OwnerName']);

                    /* ATTACHMENT */
                    if(inval['AttRows'] == 0) {
                        $("#view_IVAttachTab").addClass("disabled");
                    } else {
                        $("#view_IVAttachTab").removeClass("disabled");
                        var AttRow = inval['AttRows'];
                        var atrow;
                        var visorder = 1;
                        for(i = 0; i < AttRow; i++) {
                            atrow +=
                                "<tr>"+
                                    "<td class='text-right'>"+visorder+"</td>"+
                                    "<td>"+inval['AT_'+i]['FileName']+"</td>"+
                                    "<td class='text-center'><a href='"+inval['AT_'+i]['FilePath']+"' target='_blank'><i class='fas fa-download fa-fw fa-1x'></i></a></td>"+
                                "</tr>";
                            visorder++;
                        }
                        $("#IVAttachItem tbody").html(atrow);
                    }

                });
            }
        });
        $(".overlay").hide();
        $("#PreviewIV").modal("show");
    }

    
    function IndiSale() {
        let Year = $("#txtYear").val();
        let Month = $("#txtMonth").val();
        $(".ShowMonthSale").html(fMonth(Month)+" "+Year);
        $(".ShowSumSale").html(Year+" (ม.ค. - "+tMonth(Month)+")");
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=CallData",
            type: "POST",
            data: { Year: Year, Month: Month },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    let TeamArr = "";
                    switch ('<?php echo $_SESSION['DeptCode']; ?>'){
                        case 'DP005' : //TT2
                            TeamArr = [ 'TT2' ];
                        break;
                        case 'DP006' : //MT1
                            TeamArr = [ 'MT1' ];
                        break;
                        case 'DP007' : //MT2
                            TeamArr = [ 'MT2' ];
                        break;
                        case 'DP008' : //TT1 //OUL
                            TeamArr = [ 'TT1', 'OUL' ];
                        break;
                        default:  //ALL
                            TeamArr = [ 'MT1', 'MT2', 'ONL', 'OUL', 'TT1', 'TT2' ];
                        break;
                    }
                    
                    for(let t = 0; t < TeamArr.length; t++) {
                        let Data = "";
                        let All_MonthSaleTarget = 0;
                        let All_SaleMonth = 0;
                        let All_YearSaleTarget = 0;
                        let All_SaleYear = 0;
                        for(let s = 1; s <= (Object.keys(inval['Data'][TeamArr[t]]).length-2); s++) {
                            let Sum_MonthSaleTarget = 0;
                            let Sum_SaleMonth = 0;
                            let Sum_YearSaleTarget = 0;
                            let Sum_SaleYear = 0;
                            $.each(inval['Data'][TeamArr[t]]['T'+s],function(row,td) {
                                Sum_MonthSaleTarget = Sum_MonthSaleTarget+parseFloat(td['MonthSaleTarget']);
                                Sum_SaleMonth = Sum_SaleMonth+parseFloat(td['SaleMonth']);
                                Sum_YearSaleTarget = Sum_YearSaleTarget+parseFloat(td['YearSaleTarget']);
                                Sum_SaleYear = Sum_SaleYear+parseFloat(td['SaleYear']);
                                if(row == 1) {
                                    Data += (`
                                        <tr>
                                            <td rowspan='`+Object.keys(inval['Data'][TeamArr[t]]['T'+s]).length+`' class='text-center'>ทีม `+s+`</td>
                                            <td>`+td['Name']+`</td>
                                            <th class='text-right'>`+number_format(td['MonthSaleTarget'],0)+`</th>
                                            <td class='text-right'><a href="javascript:void(0);" onclick="SlpDetail('`+td['uKey']+`')">`+number_format(td['SaleMonth'],0)+`</td>
                                            <td class='text-right'>`+number_format(td['PercentSaleMonth'],2)+`%</td>
                                            <th class='text-right'>`+number_format(td['YearSaleTarget'],0)+`</th>
                                            <td class='text-right'>`+number_format(td['SaleYear'],0)+`</td>
                                            <td class='text-right'>`+number_format(td['PercentSaleYear'],2)+`%</td>
                                        </tr>
                                    `);
                                }else{
                                    Data += (`
                                        <tr>
                                            <td>`+td['Name']+`</td>
                                            <th class='text-right'>`+number_format(td['MonthSaleTarget'],0)+`</th>
                                            <td class='text-right'><a href="javascript:void(0);" onclick="SlpDetail('`+td['uKey']+`')">`+number_format(td['SaleMonth'],0)+`</td>
                                            <td class='text-right'>`+number_format(td['PercentSaleMonth'],2)+`%</td>
                                            <th class='text-right'>`+number_format(td['YearSaleTarget'],0)+`</th>
                                            <td class='text-right'>`+number_format(td['SaleYear'],0)+`</td>
                                            <td class='text-right'>`+number_format(td['PercentSaleYear'],2)+`%</td>
                                        </tr>
                                    `);
                                }
                                
                                if(row == Object.keys(inval['Data'][TeamArr[t]]['T'+s]).length) {
                                    let PercentSum_Month = (Sum_MonthSaleTarget != 0) ? ((Sum_SaleMonth/Sum_MonthSaleTarget)*100) : 0;
                                    let PercentSum_Year = (Sum_YearSaleTarget != 0) ? ((Sum_SaleYear/Sum_YearSaleTarget)*100) : 0;
                                    Data += (`
                                        <tr class='table-danger'>
                                            <th colspan='2' class='text-center'>รวมทีม `+s+`</th>
                                            <th class='text-right'>`+number_format(Sum_MonthSaleTarget,0)+`</th>
                                            <th class='text-right'>`+number_format(Sum_SaleMonth,0)+`</th>
                                            <th class='text-right'>`+number_format(PercentSum_Month,2)+`%</th>
                                            <th class='text-right'>`+number_format(Sum_YearSaleTarget,0)+`</th>
                                            <th class='text-right'>`+number_format(Sum_SaleYear,0)+`</th>
                                            <th class='text-right'>`+number_format(PercentSum_Year,2)+`%</th>
                                        </tr>
                                    `);

                                    All_MonthSaleTarget = All_MonthSaleTarget+Sum_MonthSaleTarget;
                                    All_SaleMonth = All_SaleMonth+Sum_SaleMonth;
                                    All_SaleYear = All_SaleYear+Sum_SaleYear;
                                }
                            });
                        }

                        All_SaleMonth = (All_SaleMonth+inval['Data'][TeamArr[t]]['OutSaleMonth']);
                        All_SaleYear = (All_SaleYear+inval['Data'][TeamArr[t]]['OutSaleYear']);
                        All_YearSaleTarget = (inval['Data'][TeamArr[t]]['TrgAmount']/12)*parseInt(Month);

                        let All_PercentSum_Month = (All_MonthSaleTarget != 0) ? ((All_SaleMonth/All_MonthSaleTarget)*100) : 0;
                        let All_PercentSum_Year = (All_YearSaleTarget != 0) ? ((All_SaleYear/All_YearSaleTarget)*100) : 0;
                        let All_PercentAllYear = (inval['Data'][TeamArr[t]]['TrgAmount'] != 0) ? ((All_SaleYear/inval['Data'][TeamArr[t]]['TrgAmount'])*100) : 0;

                        let Datatfoot = `
                            <tr class='text-dark'>
                                <td colspan='2' class='text-center'>รวมมูลค่าพนักงานลาออกทั้งหมด</td>
                                <td class='text-right'></td>
                                <td class='text-right'>`+number_format(inval['Data'][TeamArr[t]]['OutSaleMonth'],0)+`</td>
                                <td class='text-right'></td>
                                <td class='text-right'></td>
                                <td class='text-right'>`+number_format(inval['Data'][TeamArr[t]]['OutSaleYear'],0)+`</td>
                                <td class='text-right'></td>
                            </tr>
                            <tr class='text-dark' style='background-color: #FF7C80;'>
                                <th colspan='2' class='text-center'>รวมทั้งทีมขาย `+TeamArr[t]+`</th>
                                <th class='text-right'>`+number_format(All_MonthSaleTarget,0)+`</th>
                                <th class='text-right'>`+number_format(All_SaleMonth,0)+`</th>
                                <th class='text-right'>`+number_format(All_PercentSum_Month,2)+`%</th>
                                <th class='text-right'>`+number_format(All_YearSaleTarget,0)+`</th>
                                <th class='text-right'>`+number_format(All_SaleYear,0)+`</th>
                                <th class='text-right'>`+number_format(All_PercentSum_Year,2)+`%</th>
                            </tr>
                            <tr class='bg-primary text-white'>
                                <th colspan='5' class='text-center'>รวมทั้งปี `+Year+`</th>
                                <th class='text-right'>`+number_format(inval['Data'][TeamArr[t]]['TrgAmount'],0)+`</th>
                                <th class='text-right'>`+number_format(All_SaleYear,0)+`</th>
                                <th class='text-right'>`+number_format(All_PercentAllYear,2)+`%</th>
                            </tr>`;

                        $("#Table"+TeamArr[t]+" tbody").html(Data);
                        $("#Table"+TeamArr[t]+" tfoot").html(Datatfoot);
                    }
                });
            }
        })
    }
</script>
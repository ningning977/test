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
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="txtYear">เลือกปี</label>
                            <select class='form-select form-select-sm' name="txtYear" id="txtYear" onchange='GetOrderTracking()'>
                                <?php for($y = date("Y"); $y >= 2023; $y--) {
                                    echo (($y == date("Y") ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>"));
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="txtMonth">เลือกเดือน</label>
                            <select class='form-select form-select-sm' name="txtMonth" id="txtMonth" onchange='GetOrderTracking()'>
                                <?php for($m = 1; $m <= 12; $m++) {
                                    echo (($m == date("m") ? "<option value='$m' selected>".FullMonth($m)."</option>" : "<option value='$m'>".FullMonth($m)."</option>"));
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="txtTeam">เลือกทีม</label>
                            <select class='form-select form-select-sm' name="txtTeam" id="txtTeam" onchange='GetOrderTracking()'></select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="table-responsive pt-3">
                            <table class='table table-sm table-bordered table-hover' id='TableTracking' style='font-size: 11px;'>
                                <thead>
                                    <tr>
                                        <th rowspan='2' class='text-center border border-top align-bottom'>No.</th>
                                        <th colspan='5' class='text-center border border-top align-bottom'>EUROX FORCE</th>
                                        <th colspan='3' class='text-center border border-top align-bottom'>[SAP] คำสั่งขาย</th>
                                        <th colspan='5' class='text-center border border-top align-bottom'>[SAP] บิลขาย/บิลเบิก-ยืม</th>
                                        <th rowspan='2' class='text-center border border-top align-bottom'>หมายเหตุ SO</th>
                                        <th rowspan='2' class='text-center border border-top align-bottom'>ใบขนส่ง</th>
                                    </tr>
                                    <tr>
                                        <th class='text-center align-bottom'>เลขที่<br>เอกสาร</th>
                                        <th class='text-center align-bottom'>วันที่<br>เอกสาร</th>
                                        <th class='text-center align-bottom'>ชื่อลูกค้า</th>
                                        <th class='text-center align-bottom'>เอกสารอ้างอิง</th>
                                        <th class='text-center align-bottom'>มูลค่า<br>(บาท)</th>

                                        <th class='text-center align-bottom'>เลขที่<br>เอกสาร</th>
                                        <th class='text-center align-bottom'>วันที่<br>เอกสาร</th>
                                        <th class='text-center align-bottom'>มูลค่า<br>(บาท)</th>

                                        <th class='text-center align-bottom'>เลขที่<br>เอกสาร</th>
                                        <th class='text-center align-bottom'>วันที่<br>เอกสาร</th>
                                        <th class='text-center align-bottom'>วันที่<br>กำหนดชำระ</th>
                                        <th class='text-center align-bottom'>มูลค่า<br>(บาท)</th>
                                        <th class='text-center align-bottom'>ชำระมาแล้ว<br>(บาท)</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalPreview" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดใบสั่งขาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="h6">ใบสั่งขายเลขที่: <span id="soview_DocNum">SOV-YYMMAXXXX</span></h5>
                        <input type="hidden" id="soview_DocEntry" />
                        <input type="hidden" id="soview_IntStatus" />
                    </div>
                </div>
                <!-- ORDER HEADER -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless table-sm" style="font-size: 12px;">
                            <tr>
                                <td class="font-weight" width="15%">ชื่อลูกค้า</td>
                                <td width="35%" id="soview_CardCode"></td>
                                <td class="font-weight" width="15%">เลขที่ผู้เสียภาษี</td>
                                <td width="35%" id="soview_LictradeNum">EIEI</td>
                            </tr>
                            <tr>
                                <td class="font-weight">วันที่ใบสั่งขาย</td>
                                <td id="soview_DocDate"></td>
                                <td class="font-weight">วันที่กำหนดส่ง</td>
                                <td id="soview_DocDueDate"></td>
                            </tr>
                            <tr>
                                <td class="font-weight">ประเภทภาษี</td>
                                <td id="soview_TaxType"></td>
                                <td class="font-weight">เงื่อนไขการชำระเงิน</td>
                                <td id="soview_Payment_Cond"></td>
                            </tr>
                            <tr>
                                <td class="font-weight">พนักงานขาย</td>
                                <td id="soview_SlpCode"></td>
                                <td class="font-weight">สถานะเอกสาร</td>
                                <td id="soview_DocStatus"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- ORDER TAB -->
                <ul class="nav nav-tabs" id="so-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#SOItemList" class="btn btn-tabs nav-link active" id="SOItemTab" data-bs-toggle="tab" data-bs-target="#SOItemList" role="tab" data-tabs="0" aria-controls="SOItemList" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-list-ol fa-fw fa-1x"></i> รายการสินค้า
                        </a>
                    </li>
                </ul>
                <!-- CONTENT TAB -->
                <div class="tab-content">
                    <div class="tab-pane show active" id="SOItemList" role="tabpanel" aria-labelledby="SOItemTab" style="font-size: 12px;">
                        <div class="row mt-4">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th>รายการ</th>
                                            <th>คลังสินค้า</th>
                                            <th colspan="2">จำนวน</th>
                                            <th width="12.5%">ราคาตั้ง</th>
                                            <th width="15%">ส่วนลด (%)</th>
                                            <th width="12.5%">ราคารวม</th>
                                        </tr>
                                    </thead>
                                    <tbody id="soview_ItemList"></td>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id='ModalViewDoc' tabindex='-1' role='dialog' data-bs-backdrop='static' aria-hidden='true'>
    <div class='modal-dialog modal-full'>
        <div class='modal-content'>
            <div class='modal-header pt-2 pb-2'>
                <h5 class="modal-title text-primary"><i class="fas fa-file-invoice-dollar"></i> รายละเอียดใบสั่งขาย</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body' style='font-size: 12px;'>
                <div class="row pb-3">
                    <div class="col" id='DataViewDoc'></div>
                </div>

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="itemlist-tab" data-bs-toggle="tab" data-bs-target="#itemlist" type="button" role="tab" aria-controls="itemlist" aria-selected="true">รายการสินค้า</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="itemlist" role="tabpanel" aria-labelledby="itemlist-tab">
                        <div class="table-responsive tableFix2">
                            <table class='table table-sm table-bordered table-hover' id='ItemListViewDoc' >
                                <thead>
                                    <tr class='text-center py-2'>
                                        <th width='3.5%'>No.</th>
                                        <th width='7.5%'>รหัสสินค้า</th>
                                        <th width='8%'>บาร์โค้ด</th>
                                        <th>ชื่อสินค้า</th>
                                        <th width='5%'>คลังสินค้า</th>
                                        <th width='5%'>จำนวน</th>
                                        <th width='5%'>หน่วยขาย</th>
                                        <th width='7.5%'>ราคาขาย</th>
                                        <th width='10%'>ส่วนลด</th>
                                        <th width='7.5%'>ราคาสุทธิ</th>
                                        <th width='10%'>รวมทั้งหมด</th>
                                        <th width='3%'>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="13">ไม่มีข้อมูล :(</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan='8' rowspan='5'>
                                            <textarea class='form-control form-control-sm' rows='7' placeholder='ระบุหมายเหตุ' id='vd_comments' readonly></textarea>
                                        </td>
                                        <td colspan='2' class='fw-bolder text-end'>ยอดรวมทุกรายการ</td>
                                        <td><input type='text' class='fw-bolder text-end form-control-plaintext form-control-sm' id='vd_AllTotal' value='0.00' readonly /></td>
                                        <td class='fw-bolder'>บาท</td>
                                    </tr>
                                    <tr>
                                        <td colspan='2' class='text-success text-end'>ส่วนลดท้ายบิล</td>
                                        <td><input type='text' class='text-success text-end form-control-plaintext form-control-sm' id='vd_DiscPcnt' value='0.00' /></td>
                                        <td id='vd_TypeDiscPcnt'></td>
                                    </tr>
                                    <tr>
                                        <td colspan='2' class='text-success text-end'>ยอดสินค้าหลังหักส่วนลด</td>
                                        <td><input type='text' class='fw-bolder text-success text-end form-control-plaintext form-control-sm' id='vd_Discount' value='0.00' readonly /></td>
                                        <td>บาท</td>
                                    </tr>
                                    <tr>
                                        <td colspan='2' class='text-end'>ภาษีมูลค่าเพิ่ม (VAT)</td>
                                        <td><input type='text' class='text-end form-control-plaintext form-control-sm' id='vd_VatSum' value='0.00' readonly /></td>
                                        <td>บาท</td>
                                    </tr>
                                    <tr>
                                        <th colspan='2' class='text-end fw-bolder text-primary '>จำนวนเงินรวมสุทธิ</th>
                                        <th>
                                            <input type='text' class='text-primary fw-bolder  text-end form-control-plaintext form-control-sm' id='vd_Total' value='0.00' readonly />
                                        </th>
                                        <th class='fw-bolder text-primary '>บาท</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ShipTrackModal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-file-alt" style='font-size: 20px;'></i>&nbsp;&nbsp;รายละเอียดข้อมูลจัดส่งสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class='table-responsive'>
                            <table class='table table-sm table-borderless' id='TableShipTrack'>
                                <tbody style='font-size: 13px;'></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class='d-flex'>รายการใบขนส่งสินค้า</div>
                        <div class='table-responsive'>
                            <table class='table table-sm' id='TableIMGShipTrack'>
                                <tbody style='font-size: 13px;'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pt-2 pb-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
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
try{ document.createEvent("TouchEvent"); var isMobile = true; }
catch(e){ var isMobile = false; }

$(document).ready(function(){
    GetTeam();
});

function GetTeam() {
    $.ajax({
        url: "menus/sale/ajax/ajaxorder_tracking.php?a=GetTeam",
        type: "GET",
        async: "false",
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#txtTeam").html(inval['option']);
                GetOrderTracking();
            });
        }
    })
}

function GetOrderTracking() {
    const Year = $("#txtYear").val();
    const Month = $("#txtMonth").val();
    const Team = $("#txtTeam").val();

    $("#TableTracking").dataTable().fnClearTable();
    $("#TableTracking").dataTable().fnDraw();
    $("#TableTracking").dataTable().fnDestroy();
    $("#TableTracking").DataTable({
        "ajax": {
            url: "menus/sale/ajax/ajaxorder_tracking.php?a=GetOrderTracking",
            type: "POST",
            data: { Year: Year, Month: Month, Team: Team },
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
          { "data": "No", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "DocNum", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "DocDate", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "CardName", class: " border border-bottom align-baseline" },
          { "data": "U_PONo", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "DocTotal", class: "dt-body-right border border-bottom align-baseline" },

          { "data": "SoDocNum", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "SoDocDate", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "SoDocTotal", class: "dt-body-right border border-bottom align-baseline" },

          { "data": "BillDocNum", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "BillDocDate", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "BillDocDueDate", class: "dt-body-center border border-bottom align-baseline" },
          { "data": "BillDocTotal", class: "dt-body-right border border-bottom align-baseline" },
          { "data": "BillPaid", class: "dt-body-right border border-bottom align-baseline" },

          { "data": "Comments", class: " border border-bottom align-baseline" },
          { "data": "Ship", class: "dt-body-center border border-bottom align-baseline" },
        ],
        "columnDefs": [
            { "width": "8%", "targets": 13 },
        ],
        "createdRow": function (row, data, dataIndex, cells) {
            if(data.Cancel == 'Y') {
                $(row).addClass("table-secondary");
            }
        },
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "ordering": false,
        "language":{ 
            "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        }
    });
}

function number_format(number,decimal) {
    var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
    var formatter = new Intl.NumberFormat("en",options);
    return formatter.format(number)
}

function ViewDoc(Type, DocEntry, DocType) {
    if(Type == 'APP') {
        $.ajax({
            url: 'menus/sale/ajax/ajaxorder_tracking.php?a=ViewDoc',
            type: 'POST',
            data: { Type: Type, DocEntry: DocEntry, DocType: DocType },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    /* SO Header */
                    $("#soview_DocEntry").val(inval['DocEntry']);
                    $("#soview_DocNum").html(inval['view_DocNum']);
                    $("#soview_CardCode").html(inval['view_CardCode']);
                    $("#soview_LictradeNum").html(inval['view_LicTradeNum']);
                    $("#soview_DocDate").html(inval['view_DocDate']);
                    $("#soview_DocDueDate").html(inval['view_DocDueDate']);
                    $("#soview_TaxType").html(inval['view_TaxType']);
                    $("#soview_Payment_Cond").html(inval['view_Payment_Cond']);
                    $("#soview_SlpCode").html(inval['view_SlpCode']);
                    $("#soview_DocStatus").html(inval['view_DocStatus']);

                    /* SO Detail */
                    $("#soview_ItemList").html(inval['view_ItemList']);

                    /* Address */
                    $("#soview_BilltoAddress").html(inval['view_BilltoAddress']);
                    $("#soview_ShiptoAddress").html(inval['view_ShiptoAddress']);
                    $("#soview_ShippingType").html(inval['view_ShippingType']);
                    $("#soview_ShipComment").html(inval['view_ShipComment']);

                    /* Attach */
                    $("#soview_attachlist").html(inval['view_attachlist']);

                    /* SO Process Status */
                    $("#soview_approvelist").html(inval['view_approvelist']);

                    /* SO Approve GP */
                    $("#soview_approveGP").html(inval['view_approveGP']);
                });
            }
        });
        $("#ModalPreview").modal("show");
    }else{
        $.ajax({
            url: "menus/sale/ajax/ajaxorder_tracking.php?a=ViewDoc",
            type: "POST",
            data: { Type: Type, DocEntry: DocEntry, DocType: DocType },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    let tHead = [
                        'เลขที่เอกสาร', 'สถานะเอกสาร',
                        'ชื่อลูกค้า', 'เลขที่ผู้เสียภาษี',
                        'วันที่ใบสั่งขาย', 'วันที่กำหนดส่ง',
                        'เงื่อนไขการชำระเงิน', '',
                        'ที่อยู่เปิดบิล', 'ที่อยู่จัดส่ง',
                        'พนักงานขาย', 'เอกสารอ้างอิง'
                    ];
    
                    let DataViewDoc = "";
                    for(let r = 0; r < tHead.length; r++) {
                        DataViewDoc += `
                        <div class="row p-0 pb-2">`;
                        DataViewDoc += `
                            <div class="col-lg d-flex" style='font-size: 12px;'>
                                <div style='width: 20%;' class='fw-bold'>`+tHead[r]+`</div>
                                <div style='width: 80%;'>`+inval['DataHead'][r]+`</div>
                            </div>`;
                            r++;
                            DataViewDoc += `
                            <div class="col-lg d-flex" style='font-size: 12px;'>
                                <div style='width: 20%;' class='fw-bold'>`+tHead[r]+`</div>
                                <div style='width: 80%;'>`+inval['DataHead'][r]+`</div>
                            </div>`;
                            DataViewDoc += `
                        </div>`;
                    }
                    $("#DataViewDoc").html(DataViewDoc);
                    
    
                    let tBody = "";
                    let AllTotal = 0;
                    $.each(inval['DataView'], function(k, data) {
                        AllTotal = AllTotal+parseFloat(data['LineTotal']);
                        let Line_Disc = "";
                        if(data['U_DiscP4'] != null && data['U_DiscP4'] != 0) {
                            Line_Disc = number_format(data['U_DiscP1'],2)+"%+"+number_format(data['U_DiscP2'],2)+"%+"+number_format(data['U_DiscP3'],2)+"%+"+number_format(data['U_DiscP4'],2)+"%";
                        }else if(data['U_DiscP3'] != null && data['U_DiscP3'] != 0){
                            Line_Disc = number_format(data['U_DiscP1'],2)+"%+"+number_format(data['U_DiscP2'],2)+"%+"+number_format(data['U_DiscP3'],2)+"%";
                        }else if(data['U_DiscP2'] != null && data['U_DiscP2'] != 0){
                            Line_Disc = number_format(data['U_DiscP1'],2)+"%+"+number_format(data['U_DiscP2'],2)+"%";
                        }else if(data['U_DiscP1'] != null && data['U_DiscP1'] != 0){
                            Line_Disc = number_format(data['U_DiscP1'],2)+"%";
                        }
                        let Line_SP = (data['LineStatus'] == 'O') ? [data['LineStatus'],"class='table-warning'"] : [data['LineStatus'],""];
    
                        tBody += 
                            `<tr `+Line_SP[1]+`>
                                <td class='text-end'>`+(k+1)+`</td>
                                <td class='text-center'>`+data['ItemCode']+`</td>
                                <td class='text-center'>`+data['CodeBars']+`</td>
                                <td>`+data['Dscription']+`</td>
                                <td class='text-center'>`+data['WhsCode']+`</td>
                                <td class='text-end'>`+number_format(data['Quantity'],0)+`</td>
                                <td>`+data['unitMsr']+`</td>
                                <td class='text-end'>`+number_format(data['PriceBefDi'],2)+`</td>
                                <td class='text-center'>`+Line_Disc+`</td>
                                <td class='text-end'>`+number_format(data['PriceAfVAT'],2)+`</td>
                                <th class='text-end'>`+number_format(data['LineTotal'],2)+`</th>
                                <td class='text-center'>&nbsp;</td>
                            </tr>`;
                    });
                    $("#ItemListViewDoc tbody").html(tBody);
                    
                    $("#vd_comments").val(inval['DataView'][0]['Comments']);
                    $("#vd_AllTotal").val(number_format(AllTotal,2));
                    let DiscPrcnt = (parseInt(inval['DataView'][0]['DiscPrcnt']) == 0) ? [number_format(parseFloat(inval['DiscTotal']),2), "บาท"] : [number_format(parseFloat(inval['DataView'][0]['DiscPrcnt']),2), "%"];
                    $("#vd_DiscPcnt").val(DiscPrcnt[0]);
                    $("#vd_TypeDiscPcnt").html(DiscPrcnt[1]);
                    $("#vd_Discount").val(number_format(parseFloat(inval['DataView'][0]['DocTotal'])-parseFloat(inval['DataView'][0]['VatSum']),2));
                    $("#vd_VatSum").val(number_format(parseFloat(inval['DataView'][0]['VatSum']),2));
                    $("#vd_Total").val(number_format(parseFloat(inval['DataView'][0]['DocTotal']),2));
                    $("#ModalViewDoc").modal("show");
                });
            }
        })
    }
}

function ViewShip(DocEntry, DocType) {
    switch(isMobile) {
        case true: var w1 = "15"; w2 = "85"; break;
        case false: var w1 = "7"; w2 = "93"; break;
        default: var w1 = "7"; w2 = "93"; break;
    }
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=ShipTrack",
        type: "POST",
        data: { DocEntry : DocEntry, DocType : DocType, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                if(inval['Chk'] == "1") {
                    var Tbody = "<tr>"+
                                    "<td width='"+w1+"%'>ชื่อผู้ส่ง</td>"+
                                    "<td width='"+w2+"%'>"+inval['ShipTrack']['Name']+"</td>"+
                                "</tr>"+
                                "<tr>"+
                                    "<td width='"+w1+"%'>ชื่อผู้รับ</td>"+
                                    "<td width='"+w2+"%'>"+inval['ShipTrack']['ReceiveName']+"</td>"+
                                "</tr>"+
                                "<tr>"+
                                    "<td width='"+w1+"%'>วันที่ส่ง</td>"+
                                    "<td width='"+w2+"%'>"+inval['ShipTrack']['ReceiveDate']+"</td>"+
                                "</tr>";
                    $("#TableShipTrack tbody").html(Tbody);

                    if(inval['ChkRow'] != 0) {
                        var output = "";
                        for(var i = 1; i <= inval['ChkRow']; i++) {
                            output +=   "<tr class='text-center'>"+
                                            "<td colspan='2'>"+
                                                "<a href='../../../FileAttach/SHIPPING/"+inval[i]['FileName']+"' target='_blank'>"+
                                                    "<img style='width: 100%' src='../../../FileAttach/SHIPPING/"+inval[i]['FileName']+"'>"+
                                                "</a>"+
                                            "</td>"+
                                        "</tr>";
                        }
                    }else{
                        output +=   "<tr class='text-center'>"+
                                        "<td>ไม่มีข้อมูลใบขนส่งสินค้า</td>"
                                    "</tr>";
                    }
                    $("#TableIMGShipTrack tbody").html(output);
                    $("#ShipTrackModal").modal("show");
                }else{
                    $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
                    $("#alert_body").html("ไม่มีข้อมูลใบขนส่งสินค้า");
                    $("#alert_modal").modal('show');
                }
            })
        } 
    })
}
</script> 
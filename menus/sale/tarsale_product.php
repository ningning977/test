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
            height: 550px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    .headtext{
        animation-name: head;
        animation-delay: 0s;
        animation-duration: 12s;
        animation-iteration-count: infinite;
    }

    @keyframes head {
        0% {opacity:0.5;}
        10% {opacity:1;}
        20% {opacity:1;}
        30% {opacity:1;}
        40% {opacity:1;}
        50% {opacity:1;}
        60% {opacity:0;}
        70% {opacity:0;}
        80% {opacity:0;}
        90% {opacity:0;}
        100% {opacity:0.5;}
    }

    .headtext2{
        animation-name: head2;
        animation-delay: 0s;
        animation-duration: 12s;
        animation-iteration-count: infinite;
    }

    @keyframes head2 {
        0% {opacity:0.5;}
        10% {opacity:0;}
        20% {opacity:0;}
        30% {opacity:0;}
        40% {opacity:0;}
        50% {opacity:0;}
        60% {opacity:1;}
        70% {opacity:1;}
        80% {opacity:1;}
        90% {opacity:1;}
        100% {opacity:0.5;}
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
                    <div class="col">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary active" id="Tab1-tab" onclick='GetTar();' data-bs-toggle="tab" data-bs-target="#Tab1" type="button" role="tab" aria-controls="Tab1" aria-selected="false"><i class="fas fa-list fa-fw fa-1x"></i> รายการเป้าสินค้า</button>
                                <button class="nav-link text-primary" id="Tab3-tab" onclick='TarSummary();' data-bs-toggle="tab" data-bs-target="#Tab3" type="button" role="tab" aria-controls="Tab3" aria-selected="false"><i class="fas fa-table fa-fw fa-1x"></i> สรุปภาพรวม</button>
                            </div>
                        </nav>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="tab-content pt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="Tab1" role="tabpanel" aria-labelledby="Tab1-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="table-responsive" style='height: 750px;'>
                                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='TableTar'>
                                                <thead>
                                                    <tr>
                                                        <th class='text-center border-top'>เลขที่เอกสาร</th>
                                                        <th class='text-center border-top'>ชื่อเป้าขายสินค้า</th>
                                                        <th class='text-center border-top'>ทีมขาย</th>
                                                        <th class='text-center border-top'>รูปแบบวัดผล</th>
                                                        <th class='text-center border-top'>ประเภทเป้าขายสินค้า</th>
                                                        <th class='text-center border-top'>วันที่ Campaign</th>
                                                        <th class='text-center border-top'>สถานะ Campaign</th>
                                                        <th class='text-center border-top'>รายละเอียดเป้าขาย</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="Tab3" role="tabpanel" aria-labelledby="Tab3-tab">
                                <div class="row mt-3">
                                    <div class="col-lg-1 col-5">
                                        <div class="form-group">
                                            <label for="filt_year">เลือกปี</label>
                                            <select class="form-select form-select-sm" name="filt_year" id="filt_year">
                                            <?php
                                                for($y = date("Y"); $y >= 2023; $y--) {
                                                    if($y == date("Y")) {
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
                                    <div class="col-lg-2 col-5">
                                        <div class="form-group">
                                            <label for="filt_CPType">เลือกประเภทเป้าขายสินค้า</label>
                                            <select class="form-select form-select-sm" name="filt_CPType" id="filt_CPType">
                                                <option value="Q" selected>สินค้าจอง (Quota)</option>
                                                <option value="F">สินค้าต้องขาย (Focus)</option>
                                                <option value="P">สินค้าโปรโมชั่น (Promotion)</option>
                                                <option value="2">สินค้ามือสอง (2nd Hand)</option>
                                                <option value="O">อื่น ๆ</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive tableFix">
                                            <table class='table table-sm table-bordered' style='font-size: 12px;' id='TBShowSummary'>
                                                <thead class="text-center text-white" style="background-color: #9A1118;">
                                                    <tr>
                                                        <th width="12.5%" rowspan="2">ทีมขาย</th>
                                                        <th width="15%" rowspan="2">รายละเอียด</th>
                                                        <th colspan="12">มูลค่า (บาท)</th>
                                                    </tr>
                                                    <tr>
                                                        <?php for($m = 1; $m <= 12; $m++) { echo "<th width='6.125%'>".FullMonth($m)."</th>"; } ?>
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
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalViewDoc" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-full" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-book-reader fa-fw fa-1x"></i> ข้อมูลเป้าขายสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-borderless rounded rounded-3 overflow-hidden" style='background-color: rgba(155, 0, 0, 0.04);' id='TableHeaderViewDoc'>
                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                    <tr>
                                        <td colspan='6' class='text-white'>ข้อมูลเป้าขายสินค้า</td>
                                    </tr>
                                </tdead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col d-flex align-items-center justify-content-end">
                        <span><i class="fas fa-search"></i></span>&nbsp;
                        <div style='width: 200px;'>
                            <input class='form-control form-control-sm' type="text" id='search' name='search'>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableViewDoc'>
                                <thead class='text-center bg-white'>
                                    <tr>
                                        <th width='5%'>ลำดับ</th>
                                        <th width='10%'>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th width='7.5%'>สถานะ</th>
                                        <th width='5%'>Aging<br>(เดือน)</th>
                                        <th width='5%'>Stock<br>ตั้งต้น</th>
                                        <th width='5%'>หน่วย</th>
                                        <th width='5%'>เป้าทั้งหมด<br>(หน่วย)</th>
                                        <th width='5%'>เป้าเฉลี่ย/คน<br>(หน่วย)</th>
                                        <th width='5%'>ยอดขาย<br>(หน่วย)</th>
                                        <th width='5%'>% of Success</th>
                                        <th width='5%'>Stock<br>ปัจจุบัน</th>
                                        <th width='5%'>กำลัง<br>สั่งซื้อ</th>
                                        <th width='5%'><i class='fas fa-search-plus fa-fw'></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalViewDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-book-reader fa-fw fa-3x"></i> รายละเอียดเป้าขายสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id='AddList'>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="">ชื่อสินค้า</label>
                                <input type="text" class='form-control form-control-sm' style='background-color: #fff;' name='vItemCode' id='vItemCode' disabled>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-group">
                                <label for="">เป้าทั้ง Campaign (หน่วย)</label>
                                <input type="number" class='form-control form-control-sm text-right' style='background-color: #fff;' name='vTarCampaign' id='vTarCampaign' disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableViewDetail'>
                                    <thead class='text-center bg-light'>
                                        <tr>
                                            <th rowspan='2'>พนักงานขาย</th>
                                            <th colspan='12'>ยอดขาย (หน่วย)</th>
                                            <th rowspan='2' width='5%'>เป้าทั้งหมด<br>(หน่วย)</th>
                                            <th rowspan='2' width='6%'>ยอดรวม<br/>(หน่วย)</th>
                                            <th rowspan='2' width='4%'>% Success</th>
                                        </tr>
                                        <tr>
                                            <?php 
                                            for($m = 1; $m <= 12; $m++) {
                                                echo "<th width='6%'>".FullMonth($m)."</th>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan='16' class='text-center'>ไม่มีข้อมูล :)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name='tmpRow' id='tmpRow'>
                </form>
            </div>
            <div class="modal-footer">
                <button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button>
            </div>
        </div>
    </div>
</div>

<script src="../../js/extensions/apexcharts.js"></script>
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
    GetTar();
});

function GetTar() {
    $("#TableTar").dataTable().fnClearTable();
    $("#TableTar").dataTable().fnDraw();
    $("#TableTar").dataTable().fnDestroy();
    $("#TableTar").DataTable({
        "ajax": {
            url: "menus/sale/ajax/ajaxtarsale_product.php?a=GetTar",
            type: "POST",
            dataType: "json",
            dataSrc: "0"
        },
        "columns": [
            { "data": "DocNum",   class: "dt-body-center" },
            { "data": "CPTitle",  class: "" },
            { "data": "TeamCode", class: "" },
            { "data": "MngType",  class: "dt-body-center" },
            { "data": "CPType",   class: "" },
            { "data": "CamDate",  class: "dt-body-center" },
            { "data": "DocStatus",  class: "dt-body-center" },
            { "data": "Detail",   class: "" },
        ],
        "columnDefs": [
            { "width": "7%",  "targets": 0 },
            { "width": "20%",   "targets": 1 },
            { "width": "10%",   "targets": 2 },
            { "width": "5%",  "targets": 3 },
            { "width": "11%", "targets": 4 },
            { "width": "12%",   "targets": 5 },
            { "width": "8%",   "targets": 6 },
            { "width": "27%",   "targets": 7 },
        ],
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "ordering": false,
        "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        },
    });
}

function ViewDoc(DocNum, DocStatus) {
    $(".overlay").show();
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=ViewDoc",
        type: "POST",
        data: { DocNum : DocNum, DocStatus : DocStatus, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TableHeaderViewDoc tbody").html(inval['DataHeader']);
                $("#TableViewDoc tbody").html(inval['Data']);
                $("#ModalViewDoc").modal("show");
            });
            $(".overlay").hide();
        }
    })
}

$("#search").on("keyup", function(){
    var kwd = $(this).val().toLowerCase();
    $("#TableViewDoc tbody tr").filter(function(){
        $(this).toggle($(this).text().toLowerCase().indexOf(kwd) > -1)
    });
});

function ViewDetail(StartDate, EndDate, TeamCode, ItemCode, DocNum, RowID, MngType, SaleUkey) {
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=ViewDetail",
        type: "POST",
        data: { StartDate : StartDate, EndDate : EndDate, TeamCode : TeamCode, ItemCode : ItemCode, DocNum : DocNum, RowID : RowID, MngType : MngType, SaleUkey : SaleUkey, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#vItemCode").val(inval['ItemName']);
                $("#vTarCampaign").val(inval['Target']);
                $("#TableViewDetail tbody").html(inval['Data']);
                $("#ModalViewDetail").modal("show");
            });
        }
    })
}

function TarSummary() {
    let DocYear = $("#filt_year").val();
    let CPType  = $("#filt_CPType").val();
    $(".overlay").show();

    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=TarSummary",
        type: "POST",
        data: {
            y: DocYear,
            t: CPType,
        },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TBShowSummary tbody").html(inval['TBODY']);
            });
        }
    })
}

$("#filt_year, #filt_CPType").on("change", function(e) {
    e.preventDefault();
    TarSummary();
})

</script> 
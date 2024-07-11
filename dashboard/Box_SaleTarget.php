<style>
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
</style>

<div class='card'>
    <div class='card-header'>
        <h4><i class='fas fa-bullseye fa-fw fa-1x'></i> รายการเป้าขายสินค้า</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg">
                <table class="table table-hover" id='TableTarget'>
                    <thead class="text-center">
                        <tr>
                            <th>รายการเป้าขายสินค้า</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td class='text-center'><a href='?p=tarsale_product' target="_blank" style='font-size: 12.5px;'><i class="fas fa-search-plus fa-fw"></i> ดูเอกสารเพิ่มเติม</a></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

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
                                        <th width='5%'>Stock<br>ตั้งต้น</th>
                                        <th width='5%'>หน่วย</th>
                                        <th width='5%'>เป้าทั้งหมด<br>(หน่วย)</th>
                                        <th width='5%'>ยอดขาย<br>(หน่วย)</th>
                                        <th width='5%'>คิดเป็น %</th>
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
                                    <thead class='text-center'>
                                        <tr>
                                            <th rowspan='2'>พนักงานขาย</th>
                                            <th rowspan='2' width='5%'>เป้าทั้งหมด<br>(หน่วย)</th>
                                            <th colspan='12'>ยอดขาย (หน่วย)</th>
                                            <th rowspan='2' width='6%'>ยอดรวม (หน่วย)</th>
                                            <th rowspan='2' width='4%'>คิดเป็น %</th>
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
        GetTar();
	});


    function GetTar() {
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=GetTarSale",
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TableTarget tbody").html(inval['Data']);
                });
            }
        })
    }

    function ViewDoc(DocNum, DocStatus) {
        $(".overlay").show();
        const Limit = 'Y';
        $.ajax({
            url: "menus/marketing/ajax/ajaxTargetSku.php?p=ViewDoc",
            type: "POST",
            data: { DocNum : DocNum, DocStatus : DocStatus, Limit : Limit, },
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
        $("#TableViewDoc tr").filter(function(){
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

    function PushTarsale() {
        
    }
</script> 
<style type="text/css">
    /* @media only screen and (max-width:820px) {
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
    } */
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
                <div class="row mb-4 align-items-center">
                    <div class="col-lg mt-lg-0 justify-content-end mb-sm-3 d-lg-none">
                        <button class='btn btn-sm btn-primary' style='margin-top: 10px;' id="MoAddPriceSM"><i class='fas fa-plus fa-fw fa-1x'></i> เพิ่มประเภทราคา</button>
                    </div>

                    <div class="form-group ps-3" style='width: 400px;'>
                        <label for="">รหัสสินค้า</label>
                        <select class="form-control form-control-sm selectpicker" name="ItemCode" id="ItemCode" data-id="ItemCode" data-live-search="true"></select>
                        <input type="hidden" name="DfItemCode" id="DfItemCode">
                    </div>

                    <div class="form-group ps-3" style='width: 200px;'>
                        <label for="">ประเภทราคา</label>
                        <select class="form-select form-select-sm" name="PriceType" id="PriceType" onchange="GetPriceList()">
                            <?php
                               echo "<option value='STD' selected>ราคามาตรฐาน</option>";
                               echo "<option value='PRO'>ราคาโปรโมชั่น</option>";
                                $sql = "SELECT GroupCode FROM groupprice GROUP BY GroupCode ORDER BY GroupCode";
                                $sqlQRY = MySQLSelectX($sql);
                                while ($result = mysqli_fetch_array($sqlQRY)) {
                                    echo "<option value='".$result['GroupCode']."'>".$result['GroupCode']."</option>";
                                }   
                            ?>
                        </select>
                    </div>

                    <div class="col-auto ps-0">
                        <button class='btn btn-sm btn-secondary' style='margin-top: 10px;' onclick="GetItemCode()"><i class="fas fa-search"></i></button>
                        <input type="hidden" name="DfPriceType" id="DfPriceType">
                    </div>
                    <div class="col-lg mt-lg-0 d-flex justify-content-end d-sm-none d-lg-flex">
                        <button class='btn btn-sm btn-primary' style='margin-top: 10px;' id="AddPrice"><i class='fas fa-plus fa-fw fa-1x'></i> เพิ่มราคาสินค้าใหม่</button>
                    </div>
                    <div class="col-lg mt-lg-0 d-flex justify-content-end d-sm-none d-lg-flex">
                        <button class='btn btn-sm btn-primary' style='margin-top: 10px;' id="MoAddPriceLG"><i class='fas fa-plus fa-fw fa-1x'></i> เพิ่มประเภทราคา</button>
                    </div>
                </div>

                <!-- เก็บข้อมูลว่าเป็น Update or Insert-->
                <input type="hidden" name="val-submit" id="val-submit">

                <div class="row">
                    <div class="col-lg">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-report-sales" role="tabpanel" aria-labelledby="nav-report-sales-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm table-bordered" style="width:100%; font-size: 11.5px;" id='TablePriceList'>
                                                <thead class='fw-bolder' style="font-size: 13px; background-color: rgba(245, 245, 245, 0.63);">
                                                    <?php
                                                        if ($_SESSION['uClass'] == 18 || $_SESSION['uClass'] == 0 || $_SESSION['uClass'] == 1 || $_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4) {
                                                            echo"<tr style='font-size: 12px;'>".
                                                                    "<td width='5%' class='text-center'>รหัสสินค้า</td>".
                                                                    "<td width='11%' class='text-center'>ชื่อสินค้า</td>".
                                                                    "<td width='5%' class='text-center'>บาร์โค้ด</td>".
                                                                    "<td width='5%' class='text-center'>ทุน</td>".
                                                                    "<td width='5%' class='text-center'>ราคาตั้ง</td>".
                                                                    "<td width='5%' class='text-center'>ปลีก SEMI</td>".
                                                                    "<td width='5%' class='text-center'>ส่ง SEMI</td>".
                                                                    "<td width='5%' class='text-center'>GP</td>".
                                                                    "<td width='5%' class='text-center'>S1</td>".
                                                                    "<td width='5%' class='text-center'>GP</td>".
                                                                    "<td width='3%' class='text-center'>จำนวน S1</td>".
                                                                    "<td width='5%' class='text-center'>S2</td>".
                                                                    "<td width='5%' class='text-center'>GP</td>".
                                                                    "<td width='3%' class='text-center'>จำนวน S2</td>".
                                                                    "<td width='5%' class='text-center'>S3</td>".
                                                                    "<td width='5%' class='text-center'>GP</td>".
                                                                    "<td width='3%' class='text-center'>จำนวน S3</td>".
                                                                    "<td width='5%' class='text-center'>ผจก Net</td>".
                                                                    "<td width='5%' class='text-center'>GP</td>".
                                                                    "<td width='5%' class='text-center'>ปลิก MT</td>".
                                                                "</tr>";
                                                        }else{
                                                            echo"<tr style='font-size: 12px;'>".
                                                                    "<td width='7%' class='text-center'>รหัสสินค้า</td>".
                                                                    "<td width='13%' class='text-center'>ชื่อสินค้า</td>".
                                                                    "<td width='10%' class='text-center'>บาร์โค้ด</td>".
                                                                    "<td width='7%' class='text-center'>ทุน</td>".
                                                                    "<td width='7%' class='text-center'>ราคาตั้ง</td>".
                                                                    "<td width='7%' class='text-center'>ปลีก SEMI</td>".
                                                                    "<td width='7%' class='text-center'>ส่ง SEMI</td>".
                                                                    "<td width='7%' class='text-center'>S1</td>".
                                                                    "<td width='5%' class='text-center'>จำนวน S1</td>".
                                                                    "<td width='7%' class='text-center'>S2</td>".
                                                                    "<td width='5%' class='text-center'>จำนวน S2</td>".
                                                                    "<td width='7%' class='text-center'>S3</td>".
                                                                    "<td width='5%' class='text-center'>จำนวน S3</td>".
                                                                "</tr>";
                                                        }
                                                    ?>      
                                                </thead>
                                                <!-- <tbody  id="item-price-list"></tbody> -->
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

<div class="modal fade" id="ActivePrice" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " id='SizeModal'>
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="HeadActivePrice"><i class='fas fa-plus fa-fw fa-1x'></i> เพิ่ม/อัพเดต</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col-lg">
                    <div class="form-group">
                        <label for="ItemName">ชื่อสินค้า</label>
                        <div><input type="text" class="form-control form-control-sm" name="ItemName" id="ItemName" readonly></div>
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-group">
                        <label for="BarCode">บาร์โค้ด</label>
                        <div><input type="text" class="form-control form-control-sm" name="BarCode" id="BarCode" readonly></div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg">
                    <div class="form-group">
                        <label for="ProductStatus">สถานะสินค้า</label>
                        <div><input type="text" class="form-control form-control-sm" name="ProductStatus" id="ProductStatus" readonly></div>
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-group">
                        <label for="">ต้นทุน</label>
                        <div><input type="text" class="form-control form-control-sm" name="LstEvlPric" id="LstEvlPric" readonly></div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-3">
                    <div class="form-group StartDate d-none">
                        <label for="StartDate">วันที่เริ่มโปรโมชั่น<span class="text-danger">*</span></label>
                        <input type="date" class='form-control form-control-sm' name='StartDate' id='StartDate'>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group EndDate d-none">
                        <label for="EndDate">วันที่สิ้นสุดโปรโมชั่น<span class="text-danger">*</span></label>
                        <input type="date" class='form-control form-control-sm' name='EndDate' id='EndDate'>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="P2">ราคาตั้ง (บาท)<span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm text-right" name="P0" id="P0">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg">
                    <div class="form-group">
                        <label for="P1">ราคาขายปลีก SEMI (บาท)<span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm text-right" name="P1" id="P1">
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-group">
                        <label for="P2">ราคาขายส่ง SEMI (บาท)<span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm text-right" name="P2" id="P2">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr class="text-center">
                                    <th width="8%">Step ที่</th>
                                    <th width="37%">จำนวน</th>
                                    <th width="37%">ราคา (บาท)</th>
                                    <th width="18%">GP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td><input type="number" class="form-control form-control-sm text-right" name="S1Q" id="S1Q" placeholder="จำนวน Step 1"></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-right" name="S1P" id="S1P" placeholder="ราคา Step 1">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control form-control-sm text-right" name="GP_S1" id="GP_S1" placeholder="GP Step 1" readonly>&nbsp;%
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td><input type="number" class="form-control form-control-sm text-right" name="S2Q" id="S2Q" placeholder="จำนวน Step 2"></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-right" name="S2P" id="S2P" placeholder="ราคา Step 2">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control form-control-sm text-right" name="GP_S2" id="GP_S2" placeholder="GP Step 2" readonly>&nbsp;%
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td><input type="number" class="form-control form-control-sm text-right" name="S3Q" id="S3Q" placeholder="จำนวน Step 3"></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-right" name="S3P" id="S3P" placeholder="ราคา Step 3">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control form-control-sm text-right" name="GP_S3" id="GP_S3" placeholder="GP Step 3" readonly>&nbsp;%
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg">
                    <div class="form-group">
                        <label for="MgrPrice">ราคาขายปลีก MT (บาท)</label>
                        <input type="text" class="form-control form-control-sm text-right" name="MTPrice" id="MTPrice">
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-group">
                        <label for="MTPrice">ราคาขายส่ง MT (บาท)</label>
                        <input type="text" class="form-control form-control-sm text-right" name="MTPrice2" id="MTPrice2">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg">
                    <div class="form-group">
                        
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-group">
                        <label for="MgrPrice">ราคาขาย ผจก. (บาท)</label>
                        <input type="text" class="form-control form-control-sm text-right" name="MgrPrice" id="MgrPrice">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer pt-1 pb-1">
            <button type="button" class="btn btn-sm btn-secondary " data-bs-dismiss="modal">ปิด</button>
            <button type="button" class="btn btn-sm btn-primary " id="btn-submit" onclick="ActionPrice()"></button>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAddPrice" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="HeaderModal"><i class='fas fa-plus fa-fw fa-1x'></i> เพิ่มประเภทราคา</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row align-items-center justify-content-center mb-4">
                <div class="col-auto pe-0">
                    <div class="form-group mb-0">
                        <label for="ItemCode" class="col-form-label">รหัสประเภทราคา<span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="col-lg-5 pe-0">
                    <select class="form-select form-select-sm" name="GroupCode" id="GroupCode" ></select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-3 d-flex align-items-center">
                    <div class="form-check">
                        <div class="checkbox">
                            <input type="checkbox" class="form-check-input" name="chk_cardcode" id="chk_cardcode"/>
                            <label for="chk_cardcode"><i class="fas fa-store-alt fa-fw fa-1x text-warning"></i> ร้านค้า</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="form-group mb-0">
                        <select class="form-control form-control-sm selectpicker"name="CardCode" id="CardCode" data-id="CardCode" data-live-search="true" disabled></select>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-3 d-flex align-items-center">
                    <div class="form-check">
                        <div class="checkbox">
                            <input type="checkbox" class="form-check-input" name="chk_mtgroup" id="chk_mtgroup"/>
                            <label for="chk_mtgroup"><i class="fas fa-users fa-fw fa-1x text-info"></i> กลุ่มลูกค้า</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select class="form-select form-select-sm" name="MTGroup" id="MTGroup" disabled></select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">ปิด</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="AddPriceType()"><i class='fas fa-plus fa-fw fa-1x'></i> เพิ่ม</button>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAddItem" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="HeaderModal"><i class='fas fa-plus fa-fw fa-1x'></i> เพิ่มประเภทราคา</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mb-4">
                <div class="col-lg-3 d-flex align-items-center">
                    <div class="form-check">
                        <div class="checkbox">
                            <label for="chk_cardcode"><i class="fas fa-cube fa-fw fa-1x text-warning"></i> รหัสสินค้า</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="form-group mb-0">
                        <input type='textbox' class="form-control form-control-sm" name="NewItem" id="NewItem"></select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">ปิด</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="AddItem()"><i class='fas fa-plus fa-fw fa-1x'></i> เพิ่ม</button>
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
        CallHeade();
	});

    try{ document.createEvent("TouchEvent"); var isMobile = true; }
    catch(e){ var isMobile = false; }
</script> 
<script type="text/javascript">
    function CallHeade(){
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
        GetCardCode();
        GetMTGroup();
        GetPriceCode()
        GetPriceList();
    });

    function GetPriceCode() {
        $(".overlay").show();
            $.ajax({
                url: "menus/marketing/ajax/ajaxmange_price.php?a=GetPriceCode",
                success: function (result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#ItemCode").html(inval["ItemCode"]);
                    })
                    // SelectItemCode();
                    $("#ItemCode").selectpicker("refresh");
                }
            });
        $(".overlay").hide();
    }

    function GetPriceList() {
        const PriceType = $("#PriceType").val();
        $("#TablePriceList").dataTable().fnClearTable();
        $("#TablePriceList").dataTable().fnDraw();
        $("#TablePriceList").dataTable().fnDestroy();
        $("#TablePriceList").DataTable({
            "ajax": {
                url: "menus/marketing/ajax/ajaxmange_price.php?a=GetPriceList",
                type: "POST",
                data: { PriceType : PriceType, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                <?php $uClass = $_SESSION['uClass']; 
                if ($uClass == 18 || $uClass == 0 || $uClass == 1 || $uClass == 2 || $uClass == 3 || $uClass == 4) { ?>
                    { "data": "ItemCode",   class: "dt-body-center" },
                    { "data": "ItemName",   class: "" },
                    { "data": "BarCode",    class: "dt-body-center" },
                    { "data": "LastPurPrc", class: "dt-body-right" },
                    { "data": "P0",         class: "dt-body-right" },
                    { "data": "P1",         class: "dt-body-right" },
                    { "data": "P2",         class: "dt-body-right" },
                    { "data": "GP_P2",      class: "dt-body-right" },
                    { "data": "S1P",        class: "dt-body-right" },
                    { "data": "GP_S1P",     class: "dt-body-right" },
                    { "data": "S1Q",        class: "dt-body-right" },
                    { "data": "S2P",        class: "dt-body-right" },
                    { "data": "GP_S2P",     class: "dt-body-right" },
                    { "data": "S2Q",        class: "dt-body-right" },
                    { "data": "S3P",        class: "dt-body-right" },
                    { "data": "GP_S3P",     class: "dt-body-right" },
                    { "data": "S3Q",        class: "dt-body-right" },
                    { "data": "MgrPrice",   class: "dt-body-right" },
                    { "data": "GP_MgrPrice",class: "dt-body-right" },
                    { "data": "MTPrice",    class: "dt-body-right" },
                <?php }else{ ?>
                    { "data": "ItemCode",   class: "dt-body-center" },
                    { "data": "ItemName",   class: "" },
                    { "data": "BarCode",    class: "dt-body-center" },
                    { "data": "LastPurPrc", class: "dt-body-right" },
                    { "data": "P0",         class: "dt-body-right" },
                    { "data": "P1",         class: "dt-body-right" },
                    { "data": "P2",         class: "dt-body-right" },
                    { "data": "S1P",        class: "dt-body-right" },
                    { "data": "S1Q",        class: "dt-body-right" },
                    { "data": "S2P",        class: "dt-body-right" },
                    { "data": "S2Q",        class: "dt-body-right" },
                    { "data": "S3P",        class: "dt-body-right" },
                    { "data": "S3Q",        class: "dt-body-right" },
                <?php } ?>
            ],
            "columnDefs": [
                <?php if ($uClass == 18 || $uClass == 0 || $uClass == 1 || $uClass == 2 || $uClass == 3 || $uClass == 4) { ?>
                    { "width": "7%", "targets": 0 },
                    { "width": "13%","targets": 1 },
                    { "width": "5%", "targets": 2 },
                    { "width": "5%", "targets": 3 },
                    { "width": "5%", "targets": 4 },
                    { "width": "5%", "targets": 5 },
                    { "width": "5%", "targets": 6 },
                    { "width": "5%", "targets": 7 },
                    { "width": "5%", "targets": 8 },
                    { "width": "4.5%", "targets": 9 },
                    { "width": "3%", "targets": 10 },
                    { "width": "4.5%", "targets": 11 },
                    { "width": "4.5%", "targets": 12 },
                    { "width": "3%", "targets": 13 },
                    { "width": "4.5%", "targets": 14 },
                    { "width": "4.5%", "targets": 15 },
                    { "width": "3%", "targets": 16 },
                    { "width": "4.5%", "targets": 17 },
                    { "width": "4.5%", "targets": 18 },
                    { "width": "4.5%", "targets": 19 },
                <?php }else{ ?>
                    { "width": "7%", "targets": 0 },
                    { "width": "13%","targets": 1 },
                    { "width": "10%","targets": 2 },
                    { "width": "7%", "targets": 3 },
                    { "width": "7%", "targets": 4 },
                    { "width": "7%", "targets": 5 },
                    { "width": "7%", "targets": 6 },
                    { "width": "7%", "targets": 7 },
                    { "width": "5%", "targets": 8 },
                    { "width": "7%", "targets": 9 },
                    { "width": "5%", "targets": 10 },
                    { "width": "7%", "targets": 11 },
                    { "width": "5%", "targets": 12 },
                <?php } ?>
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

    // function SelectItemCode(Item) {
    //     if (Item == undefined) {
    //         $("#ItemCode").selectpicker("refresh");
    //     }else{
    //         $("#ItemCode").selectpicker("destroy");
    //         $("#ItemCode").val(Item).change(); 
    //         $("#ItemCode").selectpicker();
    //     }
    // }

    function GetItemCode(Item) {
        $("#P0, #P1, #P2, #S1Q, #S1P, #S2Q, #S2P, #S3Q, #S3P, #MgrPrice, #MTPrice, #MTPrice2, #ItemName, #BarCode, #ProductStatus, #DfItemCode, #DfPriceType, #StartDate, #EndDate").val("");
        $("#P0, #P1, #P2, #S1Q, #S1P, #S2Q, #S2P, #S3Q, #S3P, #MgrPrice, #MTPrice, #MTPrice2, #DfItem, #StartDate, #EndDate, #btn-submit").removeAttr("disabled");
        $("#P0, #P1, #P2").removeClass("is-valid is-invalid");
        $("#SizeModal").removeClass("modal-full");
        $("#SizeModal").removeClass("modal-lg");
        $(".overlay").show();

        if(Item == undefined) {
            var ItemCode = $("#ItemCode").val();
        }else{
            $("#ItemCode").val(Item);
            var ItemCode = $("#ItemCode").val();
        }
        var PriceType = $("#PriceType").val();
        var item = ["ItemCode", "PriceType"];
        var ErrorItem =0;
        for (var i = 0; i < item.length; i++) {
            if ($("#"+item[i]).val() == "" || $("#"+item[i]).val() == null) {
                // $("#"+item[i]).removeClass("is-valid is-invalid").addClass("is-invalid");
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("กรุณากรอกรหัสสินค้า");
                $("#alert_modal").modal('show');
                ErrorItem++;
            }else{
                $("#"+item[i]).removeClass("is-valid is-invalid");
            }
        }
        if (ErrorItem == 0){
            switch(isMobile) {
                case true: var SizeModal = "modal-full"; break;
                case false: var SizeModal = "modal-lg"; break;
                default: var SizeModal = "modal-lg"; break;
            }
            $.ajax({
                url: "menus/marketing/ajax/ajaxmange_price.php?a=GetItemCode",
                type: "POST",
                data: { ItemCode  : ItemCode,
                        PriceType : PriceType,},
                success: function (result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        if (inval['rowOITM'] == 1) {
                            if (inval['rowPL'] == 1) {
                                // SHOW DATA UPDATE
                                $("#P0").val(parseFloat(inval['P0']).toFixed(2));
                                $("#P1").val(parseFloat(inval['P1']).toFixed(2));
                                $("#P2").val(parseFloat(inval['P2']).toFixed(2));
                                $("#S1Q").val(parseFloat(inval['S1Q']).toFixed(0));
                                $("#S1P").val(parseFloat(inval['S1P']).toFixed(2));
                                $("#S2Q").val(parseFloat(inval['S2Q']).toFixed(0));
                                $("#S2P").val(parseFloat(inval['S2P']).toFixed(2));
                                $("#S3Q").val(parseFloat(inval['S3Q']).toFixed(0));
                                $("#S3P").val(parseFloat(inval['S3P']).toFixed(2));
                                $("#MgrPrice").val(parseFloat(inval['MgrPrice']).toFixed(2));
                                $("#MTPrice").val(parseFloat(inval['MTPrice']).toFixed(2));
                                $("#MTPrice2").val(parseFloat(inval['MTPrice2']).toFixed(2));
                                $("#ItemName").val(inval['ItemName']);
                                $("#BarCode").val(inval['BarCode']);
                                $("#ProductStatus").val(inval['ProductStatus']);
                                $("#LstEvlPric").val(parseFloat(inval['LstEvlPric']).toFixed(3));
                                $("#GP_S1").val(parseFloat(inval['GP_S1']).toFixed(2));
                                $("#GP_S2").val(parseFloat(inval['GP_S2']).toFixed(2));
                                $("#GP_S3").val(parseFloat(inval['GP_S3']).toFixed(2));

                                // เก็บข้อมูลไว้ใช้
                                $("#DfItemCode").val($("#ItemCode").val());
                                $("#DfPriceType").val($("#PriceType").val());
                                $("#val-submit").val("UpdatePrice");

                                if(PriceType == 'PRO') {
                                    $(".StartDate, .EndDate").removeClass("d-none");
                                    $("#StartDate").val(inval['StartDate']);
                                    $("#EndDate").val(inval['EndDate']);
                                }else{
                                    $(".StartDate, .EndDate").removeClass("d-none").addClass("d-none");
                                }

                                // MODAL
                                $("#SizeModal").addClass(SizeModal);
                                $("#HeadActivePrice").html("<i class='fas fa-save'></i> อัพเดตข้อมูลราคา");
                                $("#btn-submit").html("<i class='fas fa-save'></i> บันทึก");
                                $("#ActivePrice").modal("show");
                            }else{
                                // SHOW DATA ADD
                                $("#ItemName").val(inval['ItemName']);
                                $("#BarCode").val(inval['BarCode']);
                                $("#ProductStatus").val(inval['ProductStatus']);
                                $("#LstEvlPric").val(parseFloat(inval['LstEvlPric']).toFixed(3));

                                // เก็บข้อมูลไว้ใช้
                                $("#DfItemCode").val($("#ItemCode").val());
                                $("#DfPriceType").val($("#PriceType").val());
                                $("#val-submit").val("AddPrice");

                                if(PriceType == 'PRO') {
                                    $(".StartDate, .EndDate").removeClass("d-none");
                                    $("#StartDate").val(inval['StartDate']);
                                    $("#EndDate").val(inval['EndDate']);
                                }else{
                                    $(".StartDate, .EndDate").removeClass("d-none").addClass("d-none");
                                }

                                // MODAL
                                $("#SizeModal").addClass(SizeModal);
                                $("#HeadActivePrice").html("<i class='fas fa-plus fa-fw'></i> เพิ่มข้อมูลราคา");
                                $("#btn-submit").html("<i class='fas fa-plus fa-fw'></i> เพิ่ม");
                                $("#ActivePrice").modal("show");
                            }
                        }else{
                            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                            $("#alert_body").html(inval['NoData']);
                            $("#alert_modal").modal('show');
                        }
                    });
                }
            });
        }
        $(".overlay").hide();
    }

    function ActionPrice() {
        var ItemCode = $("#ItemCode").val();
        var P0 = $("#P0").val();
        var P1 = $("#P1").val();
        var P2 = $("#P2").val();
        var S1Q = $("#S1Q").val();
        var S1P = $("#S1P").val();
        var S2Q = $("#S2Q").val();
        var S2P = $("#S2P").val();
        var S3Q = $("#S3Q").val();
        var S3P = $("#S3P").val();
        var MgrPrice = $("#MgrPrice").val();
        var MTPrice = $("#MTPrice").val();
        var MTPrice2 = $("#MTPrice2").val();
        var PriceType = $("#PriceType").val();
        var StartDate = $("#StartDate").val();
        var EndDate = $("#EndDate").val();

        if(PriceType == 'PRO') {
            if(StartDate == "" || EndDate == "") {
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("กรุณากรอกวันที่โปรโมชั่น");
                $("#alert_modal").modal('show');
                return;
            }
        }

        // ตรวจความถูกต้องของข้อมูล
        if (ItemCode != "" && ItemCode == $("#DfItemCode").val() && PriceType != null && PriceType == $("#DfPriceType").val()) {
            var item = ["P1", "P2"];
            var CheckItem = 0;
            for (var i = 0; i <= item.length; i++) {
                if ($("#"+item[i]).val() == "") {
                    $("#"+item[i]).removeClass("is-valid is-invalid").addClass("is-invalid");
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
                    $("#alert_modal").modal('show');
                    CheckItem++;
                }else{
                    $("#"+item[i]).removeClass("is-invalid is-invalid").addClass("is-valid");
                }
            }
            if (CheckItem == 0) {
                // submit เก็บค่าปุ่มว่าเป็นปุ่มเพิ่ม (AddPrice) หรือปุ่มบันทึก (UpdatePrice)
                var submit = $("#val-submit").val();
                $.ajax({
                    url: "menus/marketing/ajax/ajaxmange_price.php?a=ActionPrice",
                    type: "POST",
                    data: { ItemCode  : ItemCode,
                            P0        : P0,    
                            P1        : P1,
                            P2        : P2,
                            S1Q       : S1Q,
                            S1P       : S1P,
                            S2Q       : S2Q,
                            S2P       : S2P,
                            S3Q       : S3Q,
                            S3P       : S3P,
                            MgrPrice  : MgrPrice,
                            MTPrice   : MTPrice,
                            MTPrice2   : MTPrice2,
                            PriceType : PriceType,
                            StartDate : StartDate,
                            EndDate : EndDate,
                            submit    : submit,
                        },
                    success: function (result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-primary\" style='font-size: 60px;''></i>");
                            $("#alert_body").html(inval['note']);
                            $("#alert_modal").modal('show');
                            $("#P0, #P1, #P2, #S1Q, #S1P, #S2Q, #S2P, #S3Q, #S3P, #MgrPrice, #MTPrice, #MTPrice2, #StartDate, #EndDate, #btn-submit").attr("disabled", true);
                            $("#P0, #P1, #P2, #PriceType").removeClass("is-valid is-invalid");
                        });
                    }
                });
            }
        }else{
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("เปลี่ยนรหัสสินค้า/เปลี่ยนประเภทราคา<br>กรุณากดที่รูปแว่นขยายก่อน <i class='fas fa-search'>");
            $("#alert_modal").modal('show');
        }
    }

    // เพิ่มประเภทราคา
    $("#MoAddPriceSM, #MoAddPriceLG").on('click', function(e) {
        $("#chk_cardcode, #chk_mtgroup").prop('checked', false);
        var SelectOption = $("#CardCode").html();
        $("#CardCode").empty().selectpicker('destroy');
        $("#CardCode").html(SelectOption).selectpicker();
        $("#CardCode").prop("disabled",true);
        $("[data-id='CardCode']").addClass("disabled");
        $("#MTGroup").attr("disabled", true);
        $("#MTGroup").val("").change("");
        GetIDPriceType();
        $("#ModalAddPrice").modal("show");
    });
    $("#AddPrice").on('click', function(e) {
        $("#ModalAddItem").modal("show");
    });

    function GetIDPriceType() {
        $.ajax({
            url: "menus/marketing/ajax/ajaxmange_price.php?a=GetIDPriceType",
            success: function (result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#GroupCode").html(inval['GroupCode']);
                })
            }
        });
    }

    function GetCardCode() {
        $(".overlay").show();
            $.ajax({
                url: "menus/marketing/ajax/ajaxmange_price.php?a=GetCardCode",
                success: function (result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#CardCode").html(inval["CardCode"]);
                    })
                    // $("#CardCode").selectpicker("refresh");
                }
            });
        $(".overlay").hide();
    }
    function GetMTGroup() {
        $(".overlay").show();
            $.ajax({
                url: "menus/marketing/ajax/ajaxmange_price.php?a=GetMTGroup",
                success: function (result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#MTGroup").html(inval["MTGroup"]);
                    })
                }
            });
        $(".overlay").hide();
    }

    $("#chk_cardcode").change(function() {
        if ($("#chk_cardcode").is(':checked')) {
            $("#CardCode").prop("disabled",false);
            $("[data-id='CardCode']").removeClass("disabled");
        } else {
            var SelectOption = $("#CardCode").html();
            $("#CardCode").empty().selectpicker('destroy');
            $("#CardCode").html(SelectOption).selectpicker();
            $("#CardCode").prop("disabled",true);
            $("[data-id='CardCode']").addClass("disabled");
        }
        $("#CardCode").selectpicker();
    });

    $("#chk_mtgroup").change(function(e) {
        if ($("#chk_mtgroup").is(':checked')) {
            $("#MTGroup").removeAttr("disabled");
        } else {
            $("#MTGroup").attr("disabled", true);
            $("#MTGroup").val("").change("");
        }
    });

    function AddPriceType() {
        var GroupCode = $("#GroupCode").val();
        var CardCode = $("#CardCode").val();
        var MTGroup = $("#MTGroup").val();
        if (GroupCode != "") {
            if (CardCode != null || MTGroup != null) {
                if(MTGroup == null || MTGroup == "") { MTGroup = "0"; }
                $.ajax({
                    url: "menus/marketing/ajax/ajaxmange_price.php?a=AddPriceType",
                    type: "POST",
                    data: { GroupCode : GroupCode,
                            CardCode  : CardCode,
                            MTGroup   : MTGroup,},
                    success: function (result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-primary\" style='font-size: 60px;''></i>");
                            $("#alert_body").html(inval['note']);
                            $("#alert_modal").modal('show');
                            $("#chk_cardcode, #chk_mtgroup").attr("disabled", true);
                            $("#chk_cardcode, #chk_mtgroup").prop('checked', false);
                            var SelectOption = $("#CardCode").html();
                            $("#CardCode").empty().selectpicker('destroy');
                            $("#CardCode").html(SelectOption).selectpicker();
                            $("#CardCode").prop("disabled",true);
                            $("[data-id='CardCode']").addClass("disabled");
                            $("#MTGroup").attr("disabled", true);
                            $("#MTGroup").val("").change("");
                            GetIDPriceType();
                        })
                    }
                });
            }else{
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("กรุณาเลือกร้านค้าหรือกลุ่มลูกค้าก่อน");
                $("#alert_modal").modal('show');
            }
        }
    }

    function AddItem() {
        var PriceType = $("#PriceType").val();
        var Itemcode = $("#NewItem").val();
        $.ajax({
            url: "menus/marketing/ajax/ajaxmange_price.php?a=AddItem",
            type: "POST",
            data: { ItemCode : Itemcode,
                    PriceType  : PriceType,
                    },
            success: function (result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    if (inval['output'] == 'Y'){
                        $("#ModalAddItem").modal("hide");
                        $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-primary\">สำเร็จ</i>");   
                        $("#alert_modal").modal('show');
                    }else{
                        $("#ModalAddItem").modal("hide");
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_modal").modal('show');
                    }
                })
            }
        });
    }    
    // END เพิ่มประเภทราคา

</script> 
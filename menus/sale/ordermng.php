<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<style type="text/css">
    input.form-control-plaintext, input[type="date"], input[type="datetime-local"], select.form-select {
        font-size: 12px !important;
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
            <!-- FILTER -->
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-1 col-5">
                        <div class="form-group">
                            <label for="filt_year">เลือกปี</label>
                            <select name="filt_year" id="filt_year" class="form-select form-select-sm">
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
                    <div class="col-lg-2 col-7">
                        <div class="form-group">
                            <label for="filt_month">เลือกเดือน</label>
                            <select name="filt_month" id="filt_month" class="form-select form-select-sm">
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
                    <div class="col-lg-2 col-12">
                        <div class="form-group">
                            <label for="filt_search">ค้นหา:</label>
                            <input type="text" id="filt_search" class="form-control form-control-sm" placeholder="กรุณากรอกเพื่อค้นหา..." />
                        </div>
                    </div>
                    <div class="col-lg-1 col-4">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" id="btn_search" onclick="SearchBox();"><i class="fas fa-search fa-fw fa-1x"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-1 col-4">
                        <div class="form-group">
                            <label for="btn_thanos">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm w-100" id="btn_thanos" disabled onclick="AddPicker();"><i class="fas fa-cart-plus fa-fw fa-1x"></i> จัดสรร</button>
                        </div>
                    </div>
                    <?php
                        if($_SESSION['DeptCode'] != "DP002" && $_SESSION['DeptCode'] != "DP008") {
                            $DisPrint = " disabled";
                        } else {
                            $DisPrint = "";
                        }
                    ?>
                    <div class="col-lg-1 col-4">
                        <div class="form-group">
                            <label for="btn_printDoc">&nbsp;</label>
                            <button type="button" class="btn btn-info btn-sm w-100" id="btn_printDoc" onclick="PrintDoc();"><i class="fas fa-print fa-fw fa-1x"></i></button>
                        </div>
                    </div>
                </div>

                <!-- CONTENT TAB -->
                <ul class="nav nav-tabs mt-4" id="main-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link active" id="order_tab1" data-bs-toggle="tab" data-template="1" data-tab="1" aria-controls="order_status" aria-selected="true" style="font-size: 12px;">
                            <i class="fas fa-file-alt fa-fw fa-1x"></i> รอจัดสรร
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link" id="order_tab2" data-bs-toggle="tab" data-template="2" data-tab="2" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-shopping-basket fa-fw fa-1x"></i> เบิก/หยิบสินค้า
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link" id="order_tab3" data-bs-toggle="tab" data-template="2" data-tab="3" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-exclamation-triangle fa-fw fa-1x"></i> ตัด / รอสินค้า
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link" id="order_tab4" data-bs-toggle="tab" data-template="3" data-tab="4" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-file-invoice fa-fw fa-1x"></i> เปิดบิล
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link" id="order_tab5" data-bs-toggle="tab" data-template="4" data-tab="5" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-box-open fa-fw fa-1x"></i> แพ็กสินค้า
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link" id="order_tab6" data-bs-toggle="tab" data-template="4" data-tab="6" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-truck-loading fa-fw fa-1x"></i> โหลดสินค้า
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link" id="order_tab7" data-bs-toggle="tab" data-template="5" data-tab="7" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-check fa-fw fa-1x"></i> จัดส่งเรียบร้อย
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link" id="order_tab8" data-bs-toggle="tab" data-template="1" data-tab="8" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-ban fa-fw fa-1x"></i> ยกเลิก
                        </a>
                    </li>
                    <?php if($_SESSION['DeptCode'] == 'DP002' || $_SESSION['DeptCode'] == 'DP011') { ?>
                        <li class="nav-item" role="presentation">
                        <a href="#order_status" class="btn-tabs nav-link" id="order_tab9" data-bs-toggle="tab" data-template="6" data-tab="9" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-boxes fa-fw fa-1x"></i> สินค้าค้างส่ง
                        </a>
                    </li>
                    <?php } ?>
                </ul>

                <!-- CONTENT -->
                <div class="tab-pane show active mt-4" id="order_status" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm" id="OrderTable" style="font-size: 12px; color: #000;">
                            <thead class="text-center"></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL CALLSO -->
<div class="modal fade" id="PreviewSO" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดคำสั่งขาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ORDER HEADER -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tr class="align-middle">
                                <th width="10%">ชื่อลูกค้า</th>
                                <td><input type="text" class="form-control-plaintext form-control-sm" id="view_CardName" readonly /></td>
                                <th width="10%">วันที่คลังรับออเดอร์</th>
                                <td width="10%"><input type="datetime-local" class="form-control form-control-sm text-danger" id="view_DateCreate" readonly /></td>
                                <th width="10%">เลขที่ใบสั่งขาย</th>
                                <td width="10%">
                                    <input type="text" class="form-control-plaintext form-control-sm" id="view_DocNum" readonly />
                                    <input type="hidden" class="form-control" id="view_PickID" name="view_PickID" readonly />
                                    <input type="hidden" class="form-control" id="view_DocEntry" name="view_DocEntry" readonly />
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานขาย</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="view_SlpName" readonly /></td>
                                <th width="10%">วันที่สั่งของ</th>
                                <td width="10%"><input type="date" class="form-control form-control-sm" name="view_DocDate" id="view_DocDate" readonly /></td>
                                <th>วันที่กำหนดส่ง</th>
                                <td><input type="date" class="form-control form-control-sm" name="view_DocDueDate" id="view_DocDueDate" min="<?php echo date("Y-m-d"); ?>" /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>หมายเหตุ</th>
                                <td colspan="3"><input type="text" class="form-control-plaintext form-control-sm" name="view_Comment" id="view_Comment" readonly /></td>
                                <th>เอกสารอ้างอิง</th>
                                <td><input type="text" class="form-control-plaintext form-control-sm" name="view_UPONo" id="view_UPONo" readonly /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานเบิก</th>
                                <td>
                                    <select class="form-select form-select-sm" name="view_PickerName" id="view_PickerName">
                                        <option selected disabled>รอจัดสรร</option>
                                    </select>
                                </td>
                                <th>โต๊ะแพ็ก</th>
                                <td>
                                    <select class="form-select form-select-sm" name="view_TablePack" id="view_TablePack">
                                        <option selected disabled>รอจัดสรร</option>
                                        <option value="1">โต๊ะ 1</option>
                                        <option value="2">โต๊ะ 2</option>
                                        <option value="3">โต๊ะ 3</option>
                                        <option value="4">โต๊ะ 4</option>
                                        <option value="5">โต๊ะ 5</option>
                                    </select>
                                </td>
                                <td colspan="2"><button class="btn btn-outline-success btn-sm w-100" id="btn_update" onclick="UpdateSO();"><i class="fas fa-save fa-fw fa-1x"></i> อัพเดต</button></td>
                            </tr>
                        </table>
                        <!-- ORDER TAB -->
                        <ul class="nav nav-tabs mt-4" id="order-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#view_ItemList" class="btn btn-tabs nav-link active" id="view_ItemTab" data-bs-toggle="tab" data-bs-target="#view_ItemList" role="tab" data-tabs="0" aria-controls="view_ItemList" aria-selected="false" style="font-size: 12px;">
                                    <i class="fas fa-list fa-fw fa-1x"></i> รายการสินค้า
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#view_AttachList" class="btn btn-tabs nav-link" id="view_AttachTab" data-bs-toggle="tab" data-bs-target="#view_AttachList" role="tab" data-tabs="0" aria-controls="view_AttachList" aria-selected="true" style="font-size: 12px;">
                                    <i class="fas fa-paperclip fa-fw fa-1x"></i> เอกสารแนบ
                                </a>
                            </li>
                        </ul>
                        <!-- CONTENT TAB -->
                        <div class="tab-content mt-2">
                            <div class="tab-pane show active" id="view_ItemList" role="tabpanel" aria-labelledby="view_ItemTab">
                                <table class="table table-bordered table-hover table-sm" id="OrderItem" style="font-size: 12px;">
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
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_DocTotal" id="view_DocTotal" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="9">ภาษีมูลค่าเพิ่ม</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_VatSum" id="view_VatSum" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="9">ราคาสุทธิ</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_SumTotal" id="view_SumTotal" style="font-weight: bold;" readonly /></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <p>ผู้จัดทำ: <span id="view_OwnerName"></span>
                            </div>
                            <div class="tab-pane" id="view_AttachList" role="tabpanel" aria-labelledby="view_AttachTab">
                                <table class="table table-bordered table-hover table-sm" id="AttachItem" style="font-size: 12px;">
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
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<!-- MODAL CALL IV -->
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
            <div class="modal-footer">
                <div class="col-12 text-left">
                    <button type="button" class="btn btn-sm btn-outline-info" id="btn_printDL" onclick="PrintDL(1);"><i class="fas fa-print fa-fw fa-1x"></i> พิมพ์ใบขนส่ง</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CALL CUTSO(); -->
<div class="modal fade" id="PreviewCUT" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-shopping-basket fa-fw fa-lg"></i> รายละเอียดการเบิกสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tr class="align-middle">
                                <th width="10%">ชื่อลูกค้า</th>
                                <td colspan="3"><input type="text" class="form-control-plaintext form-control-sm" id="view_CUTCardName" readonly /></td>
                                <th width="10%">เลขที่ใบสั่งขาย</th>
                                <td width="10%">
                                    <input type="text" class="form-control-plaintext form-control-sm" id="view_CUTDocNum" readonly /></td>
                                    <input type="hidden" class="form-control" id="view_CUTPickID" name="view_CUTPickID" readonly />
                                    <input type="hidden" class="form-control" id="view_CUTDocEntry" name="view_CUTDocEntry" readonly />
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
                        <form class="form" id="CUTFORM" enctype="multipart/form-data">
                        <table class="table table-bordered table-hover table-sm" id="CUTItem" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2" width="5%">ลำดับ</th>
                                    <th rowspan="2" width="7.5%">รหัสสินค้า</th>
                                    <th rowspan="2" width="7.5%">บาร์โค้ด</th>
                                    <th rowspan="2">ชื่อสินค้า</th>
                                    <th rowspan="2" width="5%">คลัง</th>
                                    <th colspan="3" width="15%">จำนวน</th>
                                    <th rowspan="2" width="5%">หน่วย</th>
                                    <th rowspan="2" width="10%">เงื่อนไขการตัด</th>
                                    <th rowspan="2" width="20%">Co-Sales ตอบ</th>
                                </tr>
                                <tr>
                                    <th width="5%">คงคลัง</th>
                                    <th width="5%">สั่งซื้อ</th>
                                    <th width="5%">เบิกแล้ว</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <input type="hidden" class="form-control form-control-sm" name="TotalRow" id="view_CUTTotalRow" readonly />
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<!-- MODAL CHKONHAND -->
<div class="modal fade" id="ChkOnHand" tabindex="-1" role="dialog" data-bs-backdrop="Static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search fa-fw fa-1x"></i> รายละเอียดสินค้าคงคลังและการเบิกสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-sm table-bordered" style="font-size: 12px;">
                            <tr>
                                <th width="25%">ข้อมูลสินค้า</th>
                                <td id="chk_ItemCode"></td>
                            </tr>
                            <tr>
                                <th width="25%">จำนวนสินค้าคงคลัง (SAP)</th>
                                <td id="chk_SAPOnHand"></td>
                            </tr>
                        </table>
                        <h6>รายการเบิก</h6>
                        <table class="table table-sm table-bordered" id="ChkItemList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">ลำดับ</th>
                                    <th width="20%">เลขที่ S/O</th>
                                    <th width="12.5%">วันที่สั่งสินค้า</th>
                                    <th>ชื่อร้านค้า</th>
                                    <th width="12.5%">จำนวนเบิก</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right" colspan="4">รวมเบิกทุกรายการ</td>
                                    <td class="text-right" id="chk_SumQty"></td>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="4">จำนวนสินค้าเบิกได้จริง</th>
                                    <th class="text-right text-success" id="chk_SumTotal"></th>
                                </tr>
                            </tfoot>
                        </table>
                        <h6>รายการคงคลังใน SAP</h6>
                        <table class="table table-sm table-bordered" id="ChkWhseList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="12.5%">รหัสคลังฯ</th>
                                    <th>ชื่อคลังสินค้า</th>
                                    <th width="12.5%">จำนวนคงคลัง</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <h6>โควต้าสินค้า</h6>
                        <table class="table table-sm table-bordered" id="QuotaList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="25%" style="background-color: #d9edf7;">พร้อมขาย SAP<br/>KSY/KSM</th>
                                    <th width="12.5%" class="text-success table-success">ส่วน<br/>กลาง</th>
                                    <th width="12.5%">โควต้า<br/>MT1</th>
                                    <th width="12.5%">โควต้า<br/>MT2</th>
                                    <th width="12.5%">โควต้า<br/>TT</th>
                                    <th width="12.5%">โควต้า<br/>หน้าร้าน</th>
                                    <th width="12.5%">โควต้า<br/>ออนไลน์</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-right" style="background-color: #d9edf7;" id="QtaSAP">-</td>
                                    <td class="text-right text-success table-success" id="QtaCEN">-</td>
                                    <td class="text-right" id="QtaMT1">-</td>
                                    <td class="text-right" id="QtaMT2">-</td>
                                    <td class="text-right" id="QtaTTC">-</td>
                                    <td class="text-right" id="QtaOUL">-</td>
                                    <td class="text-right" id="QtaONL">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CALL WO -->
<div class="modal fade" id="PreviewWO" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt fa-fw fa-1x"></i> รายละเอียดคำสั่งฝากงานคลังสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ORDER HEADER -->
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tr class="align-middle">
                                <th width="10%">ชื่อลูกค้า</th>
                                <td><input type="text" class="form-control-plaintext form-control-sm" id="view_WOCardName" readonly /></td>
                                <th width="10%">เลขที่ใบสั่งขาย</th>
                                <td width="10%">
                                    <input type="text" class="form-control-plaintext form-control-sm" id="view_WODocNum" readonly /></td>
                                    <input type="hidden" class="form-control" id="view_WOPickID"   name="view_WOPickID" readonly />
                                    <input type="hidden" class="form-control" id="view_WODocEntry" name="view_WODocEntry" readonly />
                                    <input type="hidden" class="form-control" id="view_WODocTypeCode"  name="view_WODocTypeCode" readonly />
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <th>ผู้ฝากงาน</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="view_WOSlpName" readonly /></td>
                                <th width="10%">วันที่ฝากงาน</th>
                                <td width="10%"><input type="date" class="form-control form-control-sm" name="view_WODocDate" id="view_WODocDate" readonly /></td>
                                <th>วันที่กำหนดส่ง</th>
                                <td><input type="date" class="form-control form-control-sm" name="view_WODocDueDate" id="view_WODocDueDate" readonly /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>หมายเหตุ</th>
                                <td colspan="3"><input type="text" class="form-control-plaintext form-control-sm" name="view_WOComment" id="view_WOComment" readonly /></td>
                                <th>ประเภทการฝากงาน</th>
                                <td><input type="text" class="form-control-plaintext form-control-sm" name="view_WODocType" id="view_WODocType" readonly /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานเบิก</th>
                                <td>
                                    <select class="form-select form-select-sm" name="view_PickerName" id="view_WOPickerName">
                                        <option selected disabled>รอจัดสรร</option>
                                    </select>
                                </td>
                                <th>โต๊ะแพ็ก</th>
                                <td>
                                    <select class="form-select form-select-sm" name="view_TablePack" id="view_WOTablePack">
                                        <option selected disabled>รอจัดสรร</option>
                                        <option value="1">โต๊ะ 1</option>
                                        <option value="2">โต๊ะ 2</option>
                                        <option value="3">โต๊ะ 3</option>
                                        <option value="4">โต๊ะ 4</option>
                                        <option value="5">โต๊ะ 5</option>
                                    </select>
                                </td>
                                <td colspan="2"><button class="btn btn-outline-success btn-sm w-100" id="btn_WOupdate" onclick="UpdateWO();"><i class="fas fa-save fa-fw fa-1x"></i> อัพเดต</button></td>
                            </tr>
                        </table>
                        <!-- ORDER TAB -->
                        <ul class="nav nav-tabs mt-4" id="whorder-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#view_WOItemList" class="btn btn-tabs nav-link active" id="view_WOItemTab" data-bs-toggle="tab" data-bs-target="#view_WOItemList" role="tab" data-tabs="0" aria-controls="view_WOItemList" aria-selected="false" style="font-size: 12px;">
                                    <i class="fas fa-list fa-fw fa-1x"></i> รายการสินค้า
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#view_WOAttachList" class="btn btn-tabs nav-link" id="view_WOAttachTab" data-bs-toggle="tab" data-bs-target="#view_WOAttachList" role="tab" data-tabs="0" aria-controls="view_WOAttachList" aria-selected="true" style="font-size: 12px;">
                                    <i class="fas fa-paperclip fa-fw fa-1x"></i> เอกสารแนบ
                                </a>
                            </li>
                        </ul>
                        <!-- CONTENT TAB -->
                        <div class="tab-content mt-2">
                            <div class="tab-pane show active" id="view_WOItemList" role="tabpanel" aria-labelledby="view_WOItemTab">
                                <table class="table table-bordered table-hover table-sm" id="WHOItem" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th width="10%">รหัสสินค้า</th>
                                            <th width="10%">บาร์โค้ด</th>
                                            <th>ชื่อสินค้า</th>
                                            <th width="5%">คลัง</th>
                                            <th width="10%"colspan="2">จำนวน</th>
                                            <th width="15%">หมายเหตุ</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="view_WOAttachList" role="tabpanel" aria-labelledby="view_WOAttachTab">
                                <table class="table table-bordered table-hover table-sm" id="WOAttachItem" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th>ชื่อเอกสารแนบ</th>
                                            <th width="7.5%">ดาวน์โหลด</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<!-- MODAL OPEN BILL -->
<div class="modal fade" id="ModalOpenBill" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice fa-fw fa-1x"></i> รายละเอียดการเปิดบิล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-borderless table-sm" style="font-size: 12px;">
                            <tr>
                                <th class="align-top" width="15%">ชื่อลูกค้า:</th>
                                <td class="align-top" colspan="3" id="OB_CardCode"></td>
                                <th class="align-top" width="12.5%">เลขที่เอกสาร:</th>
                                <td class="align-top" width="15%" id="OB_DocNum"></td>
                            </tr>
                            <tr>
                                <th class="align-top" rowspan="2">ที่อยู่จัดส่ง:</th>
                                <td class="align-top" rowspan="2" id="OB_ShipAddress"></td>
                                <th class="align-top" rowspan="2" width="12.5%">ที่อยู่เปิดบิล:</th>
                                <td class="align-top" rowspan="2" id="OB_BillAddress"></td>
                                <th class="align-top">วันที่ใบสั่งขาย:</th>
                                <td id="OB_DocDate"></td>
                            </tr>
                            <tr>
                                <th class="align-top">วันที่กำหนดส่ง:</th>
                                <td class="text-danger" id="OB_DocDueDate"  style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <th class="align-top">เลขที่ผู้เสียภาษี:</th>
                                <td class="align-top" colspan="3" id="OB_TaxID"></td>
                                <th class="align-top">พนักงานขาย:</th>
                                <td class="align-top" id="OB_SlpCode"></td>
                            </tr>
                            <tr>
                                <th class="align-top">อ้างอิง:</th>
                                <td class="align-top text-danger" style="font-weight: bold;" colspan="3" id="OB_UPONo"></td>
                                <th class="align-top">เขตการขาย:</th>
                                <td class="align-top" id="OB_UTeritory"></td>
                            </tr>
                            <tr>
                                <th class="align-top">เงื่อนไขชำระเงิน:</th>
                                <td class="align-top" colspan="3" id="OB_PaymentType"></td>
                                <th class="align-top">เครดิต:</th>
                                <td class="align-top" id="OB_CreditGroup"></td>
                            </tr>
                            <tr>
                                <th class="align-top">ขนส่งโดย:</th>
                                <td class="align-top" id="OB_LGType"></td>
                                <td class="align-top" colspan="2" id="OB_LGAddress"></td>
                                <td class="align-top" colspan="2">
                                    <span id="OB_Contact"></span><br/>
                                    <span id="OB_BillCond" style="color: #FF0000; font-size: 14px; font-weight: bold; text-decoration: underline;"></span>
                                </td>
                            </tr>
                            <tr>
                                <th class="align-top">ผู้เบิกสินค้า:</th>
                                <td class="align-top" colspan="3" id="OB_PickerName"></td>
                                <th class="align-top">โต๊ะจัด:</th>
                                <td class="align-top" id="OB_TablePack"></td>
                            </tr>
                        </table>
                        <table class="table table-bordered table-hover table-sm mt-2" id="OB_ItemList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th colspan="2" width="5%">ลำดับ</th>
                                    <th>รายการ</th>
                                    <th colspan="2">จำนวน</th>
                                    <th width="7.5%">ราคา</br>ต่อหน่วย</th>
                                    <th width="10%">ส่วนลด</th>
                                    <th width="10%">ราคารวม</th>
                                    <th width="15%">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" rowspan="2" class="align-top"><strong>หมายเหตุ: </strong> <span id="OB_Comments"></span></td>
                                    <td colspan="4" class="align-top text-right table-active" style="font-weight: bold;">ราคาก่อนหักภาษีมูลค่าเพิ่ม</td>
                                    <td class="align-top text-right" id="OB_SumTotal"></td>
                                    <td>บาท</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="align-top text-right table-active" style="font-weight: bold;">ภาษีมูลค่าเพิ่ม</td>
                                    <td class="align-top text-right" id="OB_VatTotal"></td>
                                    <td>บาท</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="align-top text-center" id="OB_SumInThai" style="font-weight: bold; font-style: italic;"></td>
                                    <td colspan="4" class="align-top text-right table-active" style="font-weight: bold;">ราคาสุทธิ</td>
                                    <td class="align-top text-right" id="OB_DocTotal" style="font-weight: bold;"></td>
                                    <td>บาท</td>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" class="form-control form-control-sm" id="OB_PickID"   name="OB_PickID"   readonly />
                        <input type="hidden" class="form-control form-control-sm" id="OB_DocEntry" name="OB_DocEntry" readonly />
                        <input type="hidden" class="form-control form-control-sm" id="OB_DocType"  name="OB_DocType"  readonly />
                    </div>
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="OB_ConfirmBill" />
                            <label class="form-check-label" for="OB_ConfirmBill" id="OB_ConfirmName">ยังไม่มีผู้เปิดบิล</label> 
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="OB_BillLoy" disabled />
                            <label class="form-check-label" for="OB_BillLoy">เปิดบิลลอย (ไม่แพ็กสินค้า)</label> 
                        </div>
                    </div>
                    <div class="col-6"></div>
                </div>
                <hr/>
                <table class="table table-bordered table-hover table-sm" id="OB_AttachItem" style="font-size: 12px;">
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
            <div class="modal-footer">
                <button type="button" onclick="PrintDL(0);" class="btn btn-outline-info"><i class="fas fa-print fa-fw fa-1x"></i> พิมพ์ใบขนส่ง</button>
                <button type="button" onclick="ReturnSO();" class="btn btn-primary"><i class="fas fa-undo fa-fw fa-1x"></i> ตี S/O คืนพนักงานเบิก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="PreviewBox" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog " id='SizeModal-PB'>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-box-open fa-fw fa-1x"></i> รายการแพ็คสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-borderless table-sm" style="font-size: 12px;">
                            <tr>
                                <th width="15%">เลขที่บิล:</th>
                                <td id="Box_DocNum"></td>
                            </tr>
                            <tr>
                                <th>พนักงานจัด:</th>
                                <td id="Box_Checker"></td>
                            </tr>
                            <tr>
                                <th>วันที่จัดสินค้า:</th>
                                <td id="Box_DateCreate"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-5 col-xl-5">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="BoxList">
                            <thead class="text-center">
                                <tr>
                                    <th width="26%">เลขที่กล่อง</th>
                                    <th width="15%">บิล</th>
                                    <th width="15%">จำนวน<br/>รายการ</th>
                                    <th>วันที่<br/>โหลดสินค้า</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="col-12 col-lg-7 col-xl-7">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="2%">ลำดับ</th>
                                    <th width="12.5%">รหัสสินค้า</th>
                                    <th width="12.5%">บาร์โค้ด</th>
                                    <th>ชื่อ</th>
                                    <th width="7%">จำนวน</th>
                                    <th width="25%">วันที่<br/>จัดของ</th>
                                </tr>
                            </thead>
                            <tbody id="ItemInBox"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="SearchResult" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search"></i> ผลลัพธ์การค้นหา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <table class="table table-bordered table-hover table-sm" id="ResultList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="12.5%">เลขที่ใบสั่งขาย</th>
                                    <th>ชื่อลูกค้า</th>
                                    <th width="10%">วันที่เอกสาร</th>
                                    <th width="10%">วันที่กำหนดส่ง</th>
                                    <th width="20%">สถานะการเบิก</th>
                                    <th width="7.5%"><i class="fas fa-paperclip fa-fw fa-1x"></i></th>
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

<div class="modal fade" id="SendModal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-file-alt" style='font-size: 20px;'></i>&nbsp;&nbsp;เพิ่มข้อมูลจัดส่งสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="form" id="SendForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg d-flex">
                            <div class="form-group" style='width: 60%;'>
                                <label for="">ชื่อขนส่ง</label>
                                <input type="text" class="form-control form-control-sm" name='Ship_Name' id="Ship_Name">
                            </div>
                            <div class="form-group ms-3" style='width: 40%;'>
                                <label for="">วันที่ส่ง</label>
                                <input type="date" class="form-control" name='Date_Receive' id="Date_Receive">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg d-flex">
                            <div class="form-group" style="width: 60%">
                                <label for="">ชื่อผู้รับ</label>
                                <input type="text" class="form-control form-control-sm" name='Name_Receive' id="Name_Received">
                            </div>

                            <div class="form-group ms-3" style="width: 40%">
                                <label for="">สถานะ</label>
                                <div class="form-control form-control-sm d-flex align-items-center">
                                    <input class="form-check-input m-0" type="checkbox" name='ST_Receive' id="ST_Received">
                                    <span class="ms-2">ลูกค้าได้รับสินค้าแล้ว</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg d-flex">
                            <div class="form-group" style="width: 60%">
                                <label for="">ค่าใช้จ่ายทีม</label>
                                <select class="form-select form-select-sm" name="ShipTeam" id="ShipTeam">
                                    <option value="" selected disabled>กรุณาเลือก</option>
                                    <option value="KBI">ส่วนกลาง</option>
                                    <option value="MT1">ทีมขายโมเดิร์นเทรด 1 (MT1)</option>
                                    <option value="MT2">ทีมขายโมเดิร์นเทรด 2 (MT2)</option>
                                    <option value="TT1">ทีมขายร้านค้ากรุงเทพฯ (TT1)</option>
                                    <option value="TT2">ทีมขายร้านค้าต่างจังหวัด (TT2)</option>
                                    <option value="OUL">ทีมขายหน้าร้าน</option>
                                    <option value="ONL">ทีมขายออนไลน์</option>
                                </select>
                            </div>
                            <div class="form-group ms-3" style="width: 40%">
                                <label for="">ค่าขนส่งหรือ COD</label>
                                <input type="text" class="form-control form-control-sm text-right" name="ShipCost" id="ShipCost" value="0.00" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg">
                            <div class="form-group w-100">
                                <label for="">แนบใบขนส่ง</label>
                                <input type="file" accept="image/*, application/pdf" class="form-control form-control-sm" name='file_upload[]' id="file_upload" multiple onchange="ViewFile()">
                                <p id='ListFile' class='pt-2 mb-1'></p>
                                <div id="showimg" class="carousel slide" data-bs-touch="false" data-bs-interval="false"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm"  onclick='SaveSend()'><i class="fas fa-save"></i> บันทึก</button>
                    <input type="hidden" name='SendDocEntry' id='SendDocEntry'>
                    <input type="hidden" name='SendDocType' id='SendDocType'>
                </div>
            </form>
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
                <input type="hidden" name='DocEntryDelete' id='DocEntryDelete'>
                <input type="hidden" name='DocTypeDelete' id='DocTypeDelete'>
                <?php if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP011") {
                    echo "<button type='button' class='btn btn-primary btn-sm' onclick='cancelSend()'>ยกเลิก</button>";
                } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ConDelectShipTrack" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title"><i class="fas fa-question-circle" style='font-size: 70px;'></i></h5>
                <p class="my-4">ยืนยันการลบข้อมูลใบขนส่งสินค้า</p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" onclick='DelectShipTrack()'>ตกลง</button>
                <input type="hidden" name='ID_Att' id='ID_Att'>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalCancelSend" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title"><i class="fas fa-question-circle" style='font-size: 70px;'></i></h5>
                <p class="my-4">คุณต้องการยกเลิกใบขนส่งนี้หรือไม่</p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" onclick='ConCancelSend()'>ตกลง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalCallLoad" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-boxes fa-fw fa-1x"></i> รายการโหลดสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class='table table-sm table-bordered table-hover' style='font-size: 12px;' id='TableCallLoad'>
                        <thead>
                            <tr class='text-center'>
                                <th>เลขที่กล่อง</th>
                                <th>โหลดแล้ว</th>
                                <th>วันที่โหลด</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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
                <p id="confirm_body" class="my-4">บันทึกข้อมูลสำเร็จ</p>
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
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
try{ document.createEvent("TouchEvent"); var isMobile = true; }
catch(e){ var isMobile = false; }

function number_format(number,decimal) {
     var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
     var formatter = new Intl.NumberFormat("en",options);
     return formatter.format(number)
}

function SumInThai(number) {
    var txtnum1  = ["ศูนย์","หนึ่ง","สอง","สาม","สี่","ห้า","หก","เจ็ด","แปด","เก้า","สิบ"];
    var txtnum2  = ["","สิบ","ร้อย","พัน","หมื่น","แสน","ล้าน","สิบ","ร้อย","พัน","หมื่น","แสน","ล้าน"];
    var number   = number.split(".");
    var digitnum = number[0].replace(/,/g,"");
    var decimal  = number[1];
    if(decimal.length > 2) {
        return "ERR::TOOMUCHDECIMAL";
    } else {
        var txt = "";
        /* NUMBER */
        for(i = 0; i < digitnum.length; i++) {
            var n = digitnum.substr(i,1);
            if(n != 0) {
                if(i == (digitnum.length-1) && n == 1) { txt += "เอ็ด"; }
                else if(i == (digitnum.length-2) && n == 2) { txt += "ยี่"; }
                else if(i == (digitnum.length-2) && n == 1) { txt += ""; }
                else { txt += txtnum1[n]; }
                txt += txtnum2[digitnum.length-i-1];
            }
        }
        txt += "บาท";
        if(decimal == "0" || decimal == "00" || decimal == "") {
            txt += "ถ้วน";
        } else {
            /* DECIMAL */
            for(i = 0; i < decimal.length; i++) {
                var d = decimal.substr(i,1);
                if(d != 0) {
                    if(i == (decimal.length-1) && d == 1) { txt += "เอ็ด"; }
                    else if(i == (decimal.length-2) && d == 2) { txt += "ยี่"; }
                    else if(i == (decimal.length-2) && d == 1) { txt += ""; }
                    else { txt += txtnum1[d]; }
                    txt += txtnum2[decimal.length-i-1]
                }
            }
            txt += "สตางค์";
        }
        return txt;
    }
}

function DefaultTab() {
    var DeptCode = '<?php echo $_SESSION['DeptCode']; ?>';
    var LvCode   = '<?php echo $_SESSION['LvCode']; ?>';
    var uClass   = '<?php echo $_SESSION['uClass']; ?>';
    var DefaultTab;

    switch(DeptCode) {
        case "DP003":
        case "DP005":
        case "DP006":
        case "DP007":
        case "DP008":
        case "DP010":
            switch(uClass) {
                case 18:
                case 19:
                case 20:
                    DefaultTab = 7;
                    break;
                default:
                    DefaultTab = 3;
                    break;
            }
            break;
        case "DP011":
            switch(LvCode) {
                case "LV081":
                    DefaultTab = 4;
                    break;
                case "LV082":
                    DefaultTab = 6;
                    break;
                default:
                    DefaultTab = 2;
                    break;
            }
            break;
        default:
            DefaultTab = 2;
            break;
    }
    return DefaultTab;
}

function GetPickerName() {
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=GetPickerName",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            var opt;
            $.each(obj, function(key,inval) {
                var Rows = parseFloat(inval['Rows']);
                for(i=0; i<Rows; i++) {
                    opt += "<option value='"+inval[i]['PickUkey']+"'>"+inval[i]['PickName']+"</option>";
                }
            });
            $("#view_PickerName, #view_WOPickerName").append(opt);
        }
    })
}

function CallTab(tabno) {
    $("#main-tabs .nav-item a.btn-tabs#order_tab"+tabno).tab("show").click();
}

function CallSO(SODocEntry) {
    var SODocEntry = SODocEntry;
    var tabno      = parseFloat($("#main-tabs a.active").attr("data-tab"));
    var DeptCode   = '<?php echo $_SESSION['DeptCode']; ?>';
    switch(tabno) {
        case 1:
        case 2:
            switch(DeptCode) {
                case "DP002":
                case "DP011":
                    $("#view_DocDueDate").attr("readonly",false);
                    $("#view_PickerName, #view_TablePack, #btn_update").attr("disabled",false);
                break;
                default:
                    $("#view_DocDueDate").attr("readonly",true)
                    $("#view_PickerName, #view_TablePack, #btn_update").attr("disabled",true);
                break;
            }
        break;
        default:
            $("#view_DocDueDate").attr("readonly",true)
            $("#view_PickerName, #view_TablePack, #btn_update").attr("disabled",true);
        break;
    }
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=CallSO",
        type: "POST",
        data: { DocEntry: SODocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                /* HEADER */
                $("#view_PickID").val(inval['HD']['PickID']);
                $("#view_DocEntry").val(inval['HD']['DocEntry']);
                $("#view_DocNum").val(inval['HD']['SODocNum']);
                $("#view_CardName").val(inval['HD']['CardCode']);
                $("#view_DateCreate").val(inval['HD']['DateCreate']);
                $("#view_SlpName").val(inval['HD']['SlpName']);
                $("#view_DocDate").val(inval['HD']['DocDate']);
                $("#view_DocDueDate").val(inval['HD']['DocDueDate']);
                $("#view_Comment").val(inval['HD']['Comments']);
                $("#view_UPONo").val(inval['HD']['U_PONo']);
                $("#view_PickerName").val(inval['HD']['PickUkey']).change();
                $("#view_TablePack").val(inval['HD']['TablePack']).change();
                
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
                $("#OrderItem tbody").html(row);

                /* FOOTER */
                var SumTotal = parseFloat(inval['FT']['DocTotal']) - parseFloat(inval['FT']['VatSum']);
                $("#view_DocTotal").val(number_format(SumTotal,2));
                $("#view_VatSum").val(number_format(inval['FT']['VatSum'],2));
                $("#view_SumTotal").val(number_format(inval['FT']['DocTotal'],2));
                $("#view_OwnerName").html(inval['FT']['OwnerName']);

                /* ATTACHMENT */
                if(inval['AttRows'] == 0) {
                    $("#view_AttachTab").addClass("disabled");
                } else {
                    $("#view_AttachTab").removeClass("disabled");
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
                    $("#AttachItem tbody").html(atrow);
                }
            });
        }
    });
    var m_footer;
    switch(DeptCode) {
        case "DP002":
        case "DP011":
            switch(tabno) {
                case 1:
                case 8:
                    m_footer =
                        "<div class='col-12 text-left'>"+ 
                            "<button type='button' class='btn btn-sm btn-outline-info' id='btn_printSO' onclick='PrintSO();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์ใบสั่งขาย</button> ";
                        "</div>"
                break;
                case 2:
                case 3:
                case 4:
                    m_footer = 
                        "<div class='col-12 text-left'>"+ 
                            "<button type='button' class='btn btn-sm btn-outline-info' id='btn_printSO' onclick='PrintSO();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button> "+
                            "<button type='button' class='btn btn-sm btn-outline-secondary' id='btn_unlockSO' onclick='UpdateStatus(3);'><i class='fas fa-unlock fa-fw fa-1x'></i> ปลดล็อก</button> "+
                            "<button type='button' class='btn btn-sm btn-danger' id='btn_unlockSO' onclick='UpdateStatus(0);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</button> "+
                        "</div>";
                break;
                case 5:
                case 6:
                case 7:
                    m_footer = 
                        "<div class='col-12 text-left'>"+ 
                            "<button type='button' class='btn btn-sm btn-outline-info' id='btn_printSO' onclick='PrintSO();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button> "+
                            "<button type='button' class='btn btn-sm btn-danger' id='btn_unlockSO' onclick='UpdateStatus(0);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</button> "+
                        "</div>";
                break;
            }
        break;
        default:
            m_footer = 
                "<div class='col-12 text-left'>"+ 
                    "<button type='button' class='btn btn-sm btn-outline-info' id='btn_printSO' onclick='PrintSO();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์ใบสั่งขาย</button> ";
                "</div>";
        break;
    }
    $(".overlay").hide();
    $("#PreviewSO .modal-footer").html(m_footer);
    $("#order-tabs .nav-item a.btn-tabs#view_ItemTab").tab("show");
    $("#view_WOPickID").val("");
    $("#view_CUTPickID").val("");
    $("#PreviewSO").modal("show");
    $("#SearchResult").modal('hide');

}

function CallIV(IVDocEntry, IVDocType) {
    var DocEntry = IVDocEntry;
    var tabno    = parseFloat($("#main-tabs a.active").attr("data-tab"));
    var DeptCode = '<?php echo $_SESSION['DeptCode']; ?>';
    var DocType  = IVDocType;
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

function CallCut(PickID) {
    var PickID = PickID;
    var tabno  = parseFloat($("#main-tabs a.active").attr("data-tab"));
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=CallCut",
        type: "POST",
        data: {
            pid: PickID
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                /* HEADER */
                $("#view_CUTPickID").val(inval['HD']['PickID']);
                $("#view_CUTDocEntry").val(inval['HD']['DocEntry']);
                $("#view_CUTDocNum").val(inval['HD']['SODocNum']);
                $("#view_CUTCardName").val(inval['HD']['CardCode']);
                $("#view_CUTSlpName").val(inval['HD']['SlpName']);
                $("#view_CUTDocDate").val(inval['HD']['DocDate']);
                $("#view_CUTDocDueDate").val(inval['HD']['DocDueDate']);CallCut
                $("#view_CUTComment").val(inval['HD']['Comments']);
                $("#view_CUTUPONo").val(inval['HD']['U_PONo']);
                $("#view_CUTTotalRow").val(inval['Rows']);

                var r = parseFloat(inval['Rows']);
                var row;
                var visorder = 1;
                for(i = 0; i < r; i++) {
                    var qty     = parseFloat(inval['BD_'+i]['Quantity']);
                    var openqty = parseFloat(inval['BD_'+i]['OpenQty']);
                    var DocType = inval['HD']['DocType'];
                    var cls_openqty = opt_1 = opt_2 = opt_3 = opt_9 = opt_41 = opt_42 = opt_43 = opt_44 = opt_45 = opt_46 = qty_plus = "";

                    if(openqty == 0) {
                        row += "<tr class='table-danger text-danger'>";
                        opt_3 = " selected";
                    } else if(qty > openqty) {
                        row += "<tr class='table-warning text-warning'>";
                        opt_2 = " selected";
                    } else {
                        row += "<tr>";
                        opt_1 = " selected";
                        cls_openqty = " text-success";
                    }
                    if(DocType != "OWAS") {
                        qty_plus = " onclick='ChkOnHand(\""+inval['BD_'+i]['ItemCode']+"\",\""+inval['BD_'+i]['WhsCode']+"\");'";
                    }
                    // console.log(DocType,qty_plus)
                    row +=
                            "<td class='text-right'>"+visorder+"</td>"+
                            "<td class='text-center'>"+inval['BD_'+i]['ItemCode']+"</td>"+
                            "<td class='text-center'>"+inval['BD_'+i]['CodeBars']+"</td>"+
                            "<td>"+inval['BD_'+i]['Dscription']+"</td>"+
                            "<td class='text-center'>"+inval['BD_'+i]['WhsCode']+"</td>"+
                            "<td class='text-right' style='color: #055C9D;'>"+
                                number_format(inval['BD_'+i]['SAPOnHand'],0)+
                                " <a href='javascript:void(0);'"+qty_plus+"><i class='fas fa-search-plus fa-fw fa-1x'></i></a>"+
                            "</td>"+
                            "<td class='text-right'>"+number_format(inval['BD_'+i]['Quantity'],0)+"</td>"+
                            "<td class='text-right"+cls_openqty+"' style='font-weight: bold;'>"+number_format(inval['BD_'+i]['OpenQty'],0)+"</td>"+
                            "<td width='5%'>"+inval['BD_'+i]['UnitMsr']+"</td>"+
                            "<td>"+
                                "<select class='form-select form-select-sm' name='RowStatus_"+i+"' id='RowStatus_"+i+"'>"+
                                    "<option value='1'"+opt_1+">ส่งทั้งหมด</option>"+
                                    "<option value='2'"+opt_2+">ส่งเท่าที่มี</option>"+
                                    "<option value='3'"+opt_3+">ยกเลิกรายการ</option>"+
                                    "<option value='9'"+opt_9+">โอนสินค้าแล้วเบิกเพิ่ม</option>"+
                                    "<option value='41'"+opt_41+">รอสินค้าจาก KBI</option>"+
                                    "<option value='42'"+opt_42+">รอประกอบสินค้า</option>"+
                                    "<option value='43'"+opt_43+">รอแปลงสินค้า</option>"+
                                    "<option value='44'"+opt_44+">รอถอดอะไหล่</option>"+
                                    "<option value='45'"+opt_45+">รอสินค้าเข้า</option>"+
                                    "<option value='46'"+opt_46+">รอสินค้า</option>"+
                                "</select>"+
                            "</td>"+
                            "<td>"+
                                "<input type='text' class='form-control form-control-sm' name='Remark_"+i+"' id='Remark_"+i+"' value='"+inval['BD_'+i]['Remark']+"' placeholder='กรุณาระบุข้อความ...'>"+
                                "<input type='hidden' class='form-control form-control-sm' name='TransID_"+i+"' id='TransID_"+i+"' value='"+inval['BD_'+i]['TransID']+"' readonly />"+
                            "</td>"+
                        "</tr>";
                    visorder++;
                }
                $("#CUTItem tbody").html(row);

                var m_footer = 
                    "<button type='button' class='btn btn-sm btn-outline-danger' onclick='CancelSO();'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิกคำสั่งขาย</button>"+
                    "<button type='button' class='btn btn-sm btn-success' onclick='ConfirmSO();'><i class='fas fa-check fa-fw fa-1x'></i> ยืนยันข้อมูล</button>";
                $("#PreviewCUT .modal-footer").html(m_footer);
            });
            
            $("#view_PickID").val("");
            $("#view_WOPickID").val("");
            $(".overlay").hide();
            $("#PreviewCUT").modal("show");
        }
    });
}

function CancelSO() {
    var PickID  = $("#view_CUTPickID").val();
    var tabno   = parseFloat($("#main-tabs a.active").attr("data-tab"));
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=CancelSO",
        type: "POST",
        data: {
            pid: PickID
        },
        success: function(result) {
            $(".overlay").hide();
            $(".modal").modal('hide');
            $("#confirm_saved").modal("show");
            $(document).off("click","#btn-save-reload").on("click","#btn-save-reload", function(e) {
                e.preventDefault();
                CallTab(tabno);
            });
        }
    })
}

function ConfirmSO() {
    var PickID  = $("#view_CUTPickID").val();
    var tabno   = parseFloat($("#main-tabs a.active").attr("data-tab"));
    var CutForm = new FormData($("#CUTFORM")[0]);
    CutForm.append("PickID",PickID);
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=CutSO",
        type: "POST",
        dataType: 'text',
        cache: false,
        processData: false,
        contentType: false,
        data: CutForm,
        success: function() {
            $(".overlay").hide();
            $(".modal").modal('hide');
            $("#confirm_saved").modal("show");
            $(document).off("click","#btn-save-reload").on("click","#btn-save-reload", function(e) {
                e.preventDefault();
                CallTab(tabno);
            });
        }
    });

}

function ChkOnHand(ItemCode, WhsCode) {
    var ItemCode = ItemCode;
    var WhsCode  = WhsCode;
    
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=ChkOnHand",
        type: "POST",
        data: {
            ItemCode: ItemCode,
            WhsCode:  WhsCode
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#chk_ItemCode").html(inval['HD']['ItemCode']+" | "+inval['HD']['ItemName']);
                $("#chk_SAPOnHand").html(number_format(inval['HD']['SAPOnHand'],0)+" "+inval['HD']['UnitMsr']);

                var PickRow = parseFloat(inval['PickRow']);
                var row;
                if(PickRow == 0) {
                    row += "<tr><td class='text-center' colspan='5'>ไม่มีรายการเบิก :(</td></tr>";
                } else {
                    var no = 1;
                    for(i = 0; i < PickRow; i++) {
                        row +=  "<tr>"+
                                    "<td class='text-right'>"+no+"</td>"+
                                    "<td class='text-center'>"+inval['BD_'+i]['DocNum']+"</td>"+
                                    "<td class='text-center'>"+inval['BD_'+i]['DocDate']+"</td>"+
                                    "<td>"+inval['BD_'+i]['CardCode']+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['BD_'+i]['OpenQty'],0)+" "+inval['HD']['UnitMsr']+"</td>"+
                                "</tr>";
                        no++;
                    }
                }
                $("#ChkItemList tbody").html(row);
                $("#chk_SumQty").html(number_format(inval['FT']['SumQty'],0)+" "+inval['HD']['UnitMsr']);
                $("#chk_SumTotal").html(number_format(inval['FT']['SumTotal'],0)+" "+inval['HD']['UnitMsr']);

                var SAPRow = parseFloat(inval['SAPRow']);
                var OnHandRow;
                if(SAPRow == 0) {
                    OnHandRow += "<tr><td class='text-center' colspan='3'>ไม่มีสินค้าคงคลัง</td></tr>";
                } else {
                    for(i = 0; i < SAPRow; i++) {
                        OnHandRow +=    "<tr>"+
                                            "<td class='text-center'>"+inval['SAP_'+i]['WhsCode']+"</td>"+
                                            "<td>"+inval['SAP_'+i]['WhsName']+"</td>"+
                                            "<td class='text-right'>"+number_format(inval['SAP_'+i]['OnHand'],0)+" "+inval['HD']['InvntryUoM']+"</td>"+
                                        "</tr>";
                    }
                }
                $("#ChkWhseList tbody").html(OnHandRow);

                /* Quota */
                $("td#QtaSAP").html(inval['Qta']['SAP']);
                $("td#QtaMT1").html(inval['Qta']['MT1']);
                $("td#QtaMT2").html(inval['Qta']['MT2']);
                $("td#QtaTTC").html(inval['Qta']['TTC']);
                $("td#QtaOUL").html(inval['Qta']['OUL']);
                $("td#QtaONL").html(inval['Qta']['ONL']);



                $("#ChkOnHand").modal("show");
            });
        }
    });
}

function CallWait(PickID) {
    var PickID = PickID;
    var tabno  = parseFloat($("#main-tabs a.active").attr("data-tab"));
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=CallWait",
        type: "POST",
        data: {
            pid: PickID
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                /* HEADER */
                $("#view_CUTPickID").val(inval['HD']['PickID']);
                $("#view_CUTDocEntry").val(inval['HD']['DocEntry']);
                $("#view_CUTDocNum").val(inval['HD']['SODocNum']);
                $("#view_CUTCardName").val(inval['HD']['CardCode']);
                $("#view_CUTSlpName").val(inval['HD']['SlpName']);
                $("#view_CUTDocDate").val(inval['HD']['DocDate']);
                $("#view_CUTDocDueDate").val(inval['HD']['DocDueDate']);
                $("#view_CUTComment").val(inval['HD']['Comments']);
                $("#view_CUTUPONo").val(inval['HD']['U_PONo']);
                $("#view_CUTTotalRow").val(inval['Rows']);

                var r = parseFloat(inval['Rows']);
                var row;
                var visorder = 1;
                for(i = 0; i < r; i++) {
                    var qty     = parseFloat(inval['BD_'+i]['Quantity']);
                    var openqty = parseFloat(inval['BD_'+i]['OpenQty']);
                    var DocType = inval['HD']['DocType'];
                    var cls_row = cls_openqty = opt_1 = opt_2 = opt_3 = opt_9 = opt_41 = opt_42 = opt_43 = opt_44 = opt_45 = opt_46 = qty_plus = "";
                    switch(inval['BD_'+i]['RowStatus']) {
                        case "1": opt_1 = " selected"; break;
                        case "2": opt_2 = " selected"; break;
                        case "3": opt_3 = " selected"; break;
                        case "9": opt_9 = " selected"; break;
                        case "41": opt_41 = " selected"; cls_row = " class='table-info'"; cls_openqty = " text-info"; break;
                        case "42": opt_42 = " selected"; cls_row = " class='table-info'"; cls_openqty = " text-info"; break;
                        case "43": opt_43 = " selected"; cls_row = " class='table-info'"; cls_openqty = " text-info"; break;
                        case "44": opt_44 = " selected"; cls_row = " class='table-info'"; cls_openqty = " text-info"; break;
                        case "45": opt_45 = " selected"; cls_row = " class='table-info'"; cls_openqty = " text-info"; break;
                        case "46": opt_46 = " selected"; cls_row = " class='table-info'"; cls_openqty = " text-info"; break;
                    }
                    if(qty == openqty) {
                        cls_openqty = " text-success";
                    }

                    if(DocType != "OWAS") {
                        qty_plus = " onclick='ChkOnHand(\""+inval['BD_'+i]['ItemCode']+"\",\""+inval['BD_'+i]['WhsCode']+"\");'";
                    }

                    row += "<tr "+cls_row+">"+
                            "<td class='text-right'>"+visorder+"</td>"+
                            "<td class='text-center'>"+inval['BD_'+i]['ItemCode']+"</td>"+
                            "<td class='text-center'>"+inval['BD_'+i]['CodeBars']+"</td>"+
                            "<td>"+inval['BD_'+i]['Dscription']+"</td>"+
                            "<td class='text-center'>"+inval['BD_'+i]['WhsCode']+"</td>"+
                            "<td class='text-right' style='color: #055C9D;'>"+
                            number_format(inval['BD_'+i]['SAPOnHand'],0)+
                            " <a href='javascript:void(0);'"+qty_plus+"><i class='fas fa-search-plus fa-fw fa-1x'></i></a>"+
                            "</td>"+
                            "<td class='text-right'>"+number_format(inval['BD_'+i]['Quantity'],0)+"</td>"+
                            "<td class='text-right"+cls_openqty+"' style='font-weight: bold;'>"+number_format(inval['BD_'+i]['OpenQty'],0)+"</td>"+
                            "<td width='5%'>"+inval['BD_'+i]['UnitMsr']+"</td>"+
                            "<td>"+
                                "<select class='form-select form-select-sm' name='RowStatus_"+i+"' id='RowStatus_"+i+"' disabled>"+
                                    "<option value='1'"+opt_1+">ส่งทั้งหมด</option>"+
                                    "<option value='2'"+opt_2+">ส่งเท่าที่มี</option>"+
                                    "<option value='3'"+opt_3+">ยกเลิกรายการ</option>"+
                                    "<option value='9'"+opt_9+">โอนสินค้าแล้วเบิกเพิ่ม</option>"+
                                    "<option value='41'"+opt_41+">รอสินค้าจาก KBI</option>"+
                                    "<option value='42'"+opt_42+">รอประกอบสินค้า</option>"+
                                    "<option value='43'"+opt_43+">รอแปลงสินค้า</option>"+
                                    "<option value='44'"+opt_44+">รอถอดอะไหล่</option>"+
                                    "<option value='45'"+opt_45+">รอสินค้าเข้า</option>"+
                                    "<option value='46'"+opt_46+">รอสินค้า</option>"+
                                "</select>"+
                            "</td>"+
                            "<td>"+
                                "<input type='text' class='form-control form-control-sm' name='Remark_"+i+"' id='Remark_"+i+"' value='"+inval['BD_'+i]['Remark']+"' placeholder='กรุณาระบุข้อความ...' disabled>"+
                                "<input type='hidden' class='form-control form-control-sm' name='TransID_"+i+"' id='TransID_"+i+"' value='"+inval['BD_'+i]['TransID']+"' readonly />"+
                            "</td>"+
                        "</tr>";
                    visorder++;
                }
                $("#CUTItem tbody").html(row);

                var m_footer = 
                    "<button type='button' class='btn btn-sm btn-outline-secondary' data-bs-dismiss='modal'><i class='fas fa-times fa-fw fa-1x'></i> ปิด</button>";
                    // "<button type='button' class='btn btn-sm btn-success' onclick='ConfirmSO();'><i class='fas fa-check fa-fw fa-1x'></i> ยืนยันข้อมูล</button>";
                $("#PreviewCUT .modal-footer").html(m_footer);
            });
            
            $("#view_PickID").val("");
            $("#view_WOPickID").val("");
            $(".overlay").hide();
            $("#PreviewCUT").modal("show");
        }
    });
}

function AddBill(PickID) {
    var PickID = PickID;
    $(".overlay").show();

    $("#OB_ConfirmName").html("ยังไม่มีผู้เปิดบิล");
    $("#OB_ConfirmBill").attr("disabled",false);
    
    $("#OB_BillLoy").attr("disabled",true);
    $("#OB_ConfirmBill, #OB_BillLoy").prop("checked",false);
    
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=AddBill",
        type: "POST",
        data: {
            pid: PickID
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result)
            $.each(obj, function(key, inval) {
                $("#OB_CardCode").html(inval['HD']['CardCode']);
                $("#OB_DocNum").html(inval['HD']['DocNum']);
                $("#OB_ShipAddress").html(inval['HD']['ShipAddress']);
                $("#OB_BillAddress").html(inval['HD']['BillAddress']);
                $("#OB_DocDate").html(inval['HD']['DocDate']);
                $("#OB_DocDueDate").html(inval['HD']['DocDueDate']);
                $("#OB_TaxID").html(inval['HD']['TaxID']);
                $("#OB_SlpCode").html(inval['HD']['SlpCode']);
                $("#OB_UPONo").html(inval['HD']['UPONo']);
                $("#OB_UTeritory").html(inval['HD']['UTeritory']);
                $("#OB_PaymentType").html(inval['HD']['PaymentType']);
                $("#OB_CreditGroup").html(inval['HD']['CreditGroup']);
                $("#OB_LGType").html(inval['HD']['LGType']);
                $("#OB_LGAddress").html(inval['HD']['LGAddress']);
                $("#OB_Contact").html(inval['HD']['Contact']);
                $("#OB_BillCond").html(inval['HD']['BillCond']);
                $("#OB_PickerName").html(inval['HD']['PickerName']);
                $("#OB_TablePack").html(inval['HD']['PackerName']);

                $("#OB_PickID").val(inval['HD']['PickID']);
                $("#OB_DocEntry").val(inval['HD']['DocEntry']);
                $("#OB_DocType").val(inval['HD']['DocType']);

                var r = parseFloat(inval['Rows']);
                var rows = "";
                var SumTotal = 0;
                
                for(i = 0; i < r; i++) {
                    var cls_row = "";
                    var txt_rmk = "";
                    var dis_chk = "";
                    var LineTotal = 0;
                    switch(inval['BD_'+i]['RowStatus']) {
                        case "3":
                            cls_row = " class='table-danger text-danger' style='font-weight: bold;'";
                            txt_rmk = "[ยกเลิกรายการ] ";
                            dis_chk = " disabled";
                        break;
                        case "2":
                            cls_row = " class='table-warning text-warning' style='font-weight: bold;'";
                            txt_rmk = "[ส่งเท่าที่มี] ";
                            dis_chk = "";
                        break;
                    }
                    LineTotal = parseFloat(inval['BD_'+i]['Price']) * parseFloat(inval['BD_'+i]['Quantity']);
                    SumTotal  = SumTotal + LineTotal;
                    rows +=
                        "<tr data-rowid='"+i+"' "+cls_row+">"+
                            "<td width='2.5%' class='text-center'><input type='checkbox' class='OB_ChkBox' data-rowid='"+i+"' "+dis_chk+" /></td>"+
                            "<td width='2.5%' class='text-right'>"+inval['BD_'+i]['VisOrder']+"</td>"+
                            "<td>"+inval['BD_'+i]['ItemName']+"</td>"+
                            "<td width='5%' class='text-right'>"+number_format(inval['BD_'+i]['Quantity'],0)+"</td>"+
                            "<td width='5%'>"+inval['BD_'+i]['unitMsr']+"</td>"+
                            "<td class='text-right'>"+number_format(inval['BD_'+i]['PriceBefDi'],3)+"</td>"+
                            "<td class='text-center'>"+inval['BD_'+i]['Discount']+"</td>"+
                            "<td class='text-right'>"+number_format(LineTotal,2)+"</td>"+
                            "<td>"+txt_rmk+inval['BD_'+i]['Remark']+"</td>"+
                        "</tr>";
                }
                $("#OB_ItemList tbody").html(rows);

                var VatSum   = parseFloat(inval['FT']['VatSum']);
                if(VatSum != 0.00) {
                    var VatTotal = (SumTotal*7)/100;
                } else {
                    var VatTotal = 0;
                }
                var DocTotal = SumTotal + VatTotal;
                $("#OB_Comments").html(inval['FT']['Comments']);
                $("#OB_SumTotal").html(number_format(SumTotal,2));
                $("#OB_VatTotal").html(number_format(VatTotal,2));
                $("#OB_DocTotal").html(number_format(DocTotal,2));
                var thai_baht = SumInThai(number_format(DocTotal,2));
                if(thai_baht == "ERR::TOOMUCHDECIMAL") {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("กรุณาระบุทศนิยมไม่เกิน 2 ตำแหน่ง");
                    $("#alert_modal").modal('show');
                } else {
                    $("#OB_SumInThai").html("("+thai_baht+")");
                }

                $(".OB_ChkBox").on("click", function() {
                    var RowID = $(this).attr("data-rowid");
                    if($(this).is(":checked") == true) {
                        $("#OB_ItemList tbody tr[data-rowid='"+RowID+"']").addClass("text-success table-success");
                    } else {
                        $("#OB_ItemList tbody tr[data-rowid='"+RowID+"']").removeAttr("class");
                    }
                });

                $("#OB_ConfirmBill").on("click",function() {
                    var PickID   = $("#OB_PickID").val();
                    var DocEntry = $("#OB_DocEntry").val();
                    var DocType  = $("#OB_DocType").val();

                    if($(this).is(":checked") == true) {
                        $.ajax({
                            url: "menus/sale/ajax/ajaxordermng.php?p=ConfirmBill",
                            type: "POST",
                            data: {
                                PickID: PickID,
                                DocEntry: DocEntry,
                                DocType: DocType
                            },
                            success: function(result) {
                                var obj = jQuery.parseJSON(result);
                                $.each(obj, function(key,inval) {
                                    if(inval['ChkStatus'] == "SUCCESS") {
                                        $("#OB_ConfirmName").html(inval['OpenName']);
                                        $("#OB_ConfirmBill").attr("disabled",true);

                                        $("#OB_BillLoy").removeAttr("disabled");
                                    } else {
                                        switch(inval['ChkStatus']) {
                                            case "ERR::NOINVOICE":      var alert_body = "กรุณาสร้างเอกสาร A/R Invoice หรือ Delivery ในระบบ SAP ก่อน"; break;
                                            case "ERR::NOPERMISSION":   var alert_body = "คุณไม่มีสิทธิ์ในการเพิ่มเอกสาร"; break;
                                        }
                                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                        $("#alert_body").html(alert_body);
                                        $("#alert_modal").modal('show');
                                        $("#OB_ConfirmBill").prop("checked",false);
                                    }
                                });
                            }
                        });
                    }
                });

                /* ATTACHMENT */
                var atrow;
                if(inval['AttRows'] == 0) {
                    atrow +=
                        "<tr>"+
                            "<td colspan='3' class='text-center'>ไม่มีเอกสารแนบ</td>"+
                        "</tr>";
                    $("#OB_AttachItem tbody").html(atrow);
                } else {
                    var AttRow = inval['AttRows'];
                    
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
                    $("#OB_AttachItem tbody").html(atrow);
                }
            });
            $(".overlay").hide();
        }
    })

    $("#ModalOpenBill").modal("show");
}

function ReturnSO() {
    var PickID = $("#OB_PickID").val();
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=ReturnPick",
        type: "POST",
        data: {
            pid: PickID
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                if(inval['ChkStatus'] == "SUCCESS") {
                    $("#confirm_saved").modal("show");
                } else {
                    switch(inval['ChkStatus']) {
                        case "ERR::NOPERMISSION":   var alert_body = "คุณไม่มีสิทธิ์ในการดำเนินการ"; break;
                    }
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html(alert_body);
                    $("#alert_modal").modal('show');
                }
                
            });
        }
    })
}


function CallWO(WODocEntry) {
    var WODocEntry = WODocEntry;
    var tabno      = parseFloat($("#main-tabs a.active").attr("data-tab"));
    var DeptCode   = '<?php echo $_SESSION['DeptCode']; ?>';

    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=CallWO",
        type: "POST",
        data: { DocEntry: WODocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                /* HEADER */
                $("#view_WOPickID").val(inval['HD']['PickID']);
                $("#view_WODocEntry").val(inval['HD']['DocEntry']);
                $("#view_WODocTypeCode").val(inval['HD']['DocType']);
                $("#view_WODocNum").val(inval['HD']['WODocNum']);
                $("#view_WOCardName").val(inval['HD']['CardCode']);
                $("#view_WOSlpName").val(inval['HD']['SlpName']);
                $("#view_WODocDate").val(inval['HD']['DocDate']);
                $("#view_WODocDueDate").val(inval['HD']['DocDueDate']);
                $("#view_WOComment").val(inval['HD']['Comments']);
                var DocType = inval['HD']['DocType'];
                var txt_DocType;
                switch(DocType) {
                    case "B": txt_DocType = "ฝากเบิกสินค้าหน้าร้าน"; break;
                    case "S": txt_DocType = "ฝากส่งสินค้า"; break;
                    case "R": txt_DocType = "ฝากรับสินค้า"; break;
                }
                $("#view_WODocType").val(txt_DocType);
                if(DocType != "R") {
                    $("#view_WOPickerName").val(inval['HD']['PickUkey']).change();
                    $("#view_WOTablePack").val(inval['HD']['TablePack']).change(); 
                } else {
                    $("#view_WOPickerName, #view_WOTablePack").prop('selectedIndex',0);
                }
                var r = parseFloat(inval['Rows']);
                var row;
                if(r > 0) {
                    var visorder = 1;
                    for(i = 0; i < r; i++) {
                        row +=
                            "<tr>"+
                                "<td class='text-right'>"+visorder+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['ItemCode']+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['CodeBars']+"</td>"+
                                "<td>"+inval['BD_'+i]['ItemName']+"</td>"+
                                "<td class='text-center'>"+inval['BD_'+i]['WhsCode']+"</td>"+
                                "<td width='5%' class='text-right'>"+number_format(inval['BD_'+i]['Quantity'],0)+"</td>"+
                                "<td width='5%'>"+inval['BD_'+i]['UnitMsr']+"</td>"+
                                "<td>"+inval['BD_'+i]['Remark']+"</td>"+
                            "</tr>";
                        visorder++;
                    }
                } else {
                    row = "<tr><td class='text-center' colspan='8'>ไม่มีรายการสินค้า :(</td></tr>";
                }
                $("#WHOItem tbody").html(row);

                /* ATTACHMENT */
                if(inval['AttRows'] == 0) {
                    $("#view_WOAttachTab").addClass("disabled");
                } else {
                    $("#view_WOAttachTab").removeClass("disabled");
                    var AttRow = inval['AttRows'];
                    var atrow;
                    var visorder = 1;
                    for(i = 0; i < AttRow; i++) {
                        atrow +=
                            "<tr>"+
                                "<td class='text-right'>"+visorder+"</td>"+
                                "<td>"+inval['AT_'+i]['NameShow']+"</td>"+
                                "<td class='text-center'><a href='../FileAttach/WHORDER/"+inval['AT_'+i]['FileName']+"' target='_blank'><i class='fas fa-download fa-fw fa-1x'></i></a></td>"+
                            "</tr>";
                    }
                    $("#WOAttachItem tbody").html(atrow);
                }

                /* UPDATE BUTTON */
                switch(tabno) {
                    case 1:
                    case 2:
                        switch(DeptCode) {
                            case "DP002":
                            case "DP011":
                                if(DocType != "R") {
                                    $("#view_WODocDueDate").attr("readonly",false);
                                    $("#view_WOPickerName, #view_WOTablePack, #btn_WOupdate").attr("disabled",false);
                                } else {
                                    $("#view_WODocDueDate").attr("readonly",true);
                                    $("#view_WOPickerName, #view_WOTablePack, #btn_WOupdate").attr("disabled",true);
                                }
                            break;
                            default:
                                $("#view_WODocDueDate").attr("readonly",true);
                                $("#view_WOPickerName, #view_WOTablePack, #btn_WOupdate").attr("disabled",true);
                            break;
                        }
                    break;
                    default:
                        $("#view_WODocDueDate").attr("readonly",true);
                        $("#view_WOPickerName, #view_WOTablePack, #btn_WOupdate").attr("disabled",true);
                    break;
                }

                /* FOOTER */
                var m_footer;
                switch(DeptCode) {
                    case "DP002":
                    case "DP011":
                        switch(tabno) {
                            case 1:
                            case 8:
                                m_footer =
                                    "<div class='col-12 text-left'>"+
                                        "<button type='button' class='btn btn-sm btn-outline-info' id='btn_printSO' onclick='PrintWO();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button> ";
                                    "</div>";
                            break;
                            case 2:
                            case 3:
                            case 4:
                                if(DocType != "R") {
                                    m_footer =
                                        "<div class='col-12 text-left'>"+
                                            "<button type='button' class='btn btn-sm btn-outline-info' id='btn_printSO' onclick='PrintWO();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button> "+
                                            "<button type='button' class='btn btn-sm btn-outline-secondary' id='btn_unlockSO' onclick='UpdateStatus(3);'><i class='fas fa-unlock fa-fw fa-1x'></i> ปลดล็อก</button> "+
                                            "<button type='button' class='btn btn-sm btn-danger' id='btn_unlockSO' onclick='UpdateStatus(0);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</button> "+
                                        "</div>";
                                } else {
                                    m_footer =
                                        "<div class='col-12 text-left'>"+
                                            "<button type='button' class='btn btn-sm btn-outline-info' id='btn_printSO' onclick='PrintWO();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button> "+
                                            "<button type='button' class='btn btn-sm btn-danger' id='btn_unlockSO' onclick='UpdateStatus(0);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</button> "+
                                        "</div>";
                                }
                            break;
                            case 5:
                            case 6:
                            case 7:
                                m_footer = 
                                    "<div class='col-12 text-left'>"+ 
                                        "<button type='button' class='btn btn-sm btn-outline-info' id='btn_printSO' onclick='PrintWO();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button> "+
                                        "<button type='button' class='btn btn-sm btn-danger' id='btn_unlockSO' onclick='UpdateStatus(0);'><i class='fas fa-ban fa-fw fa-1x'></i> ยกเลิก</button> "+
                                    "</div>";
                            break;
                        }
                    break;
                    default:
                    break;
                }
                $("#PreviewWO .modal-footer").html(m_footer);
                $("#whorder-tabs .nav-item a.btn-tabs#view_WOItemTab").tab("show");

            });
            
        }
    });

    $(".overlay").hide();
    $("#view_PickID").val("");
    $("#view_CUTPickID").val("");
    $("#PreviewWO").modal("show");
}

function CallBox(BillEntry, BillType) {
    var DocEntry = BillEntry;
    var DocType  = BillType;
    // console.log(DocEntry+" | "+DocType);
    switch(isMobile) {
        case true: var SizeModal = "modal-full"; break;
        case false: var SizeModal = "modal-xl"; break;
        default: var SizeModal = "modal-xl"; break;
    }
    $("#SizeModal-PB").removeClass("modal-full");
    $("#SizeModal-PB").removeClass("modal-xl");
    $("#ItemInBox").html("<tr><td class='text-center' colspan='6'>กรุณาเลือกเลขที่กล่อง</td></tr>");
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=CallBox",
        type: "POST",
        data: { DocEntry : DocEntry, DocType : DocType, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#Box_DocNum").html(inval['DocNum']);
                $("#Box_Checker").html(inval['Checker']);
                $("#Box_DateCreate").html(inval['DateCreate']);
                var Tbody = "";
                
                for(var i = 1; i <= inval['Row']; i++) {
                    var BillChk = "";
                    if(inval['Row_'+i]['BillInBoxc'] == 1) {
                        BillChk = "<i class='fas fa-check fa-fw fa-1x'></i>";
                    }
                    Tbody +="<tr>"+
                                "<td><a href='javascript:void(0);' class='BoxCode' data-idbox='"+inval['Row_'+i]['ID']+"''>"+inval['Row_'+i]['BoxCode']+"</td>"+
                                "<td class='text-center'>"+BillChk+"</td>"+
                                "<td class='text-right'>"+inval['Row_'+i]['TotalItem']+"</td>"+
                                "<td class='text-center'>"+inval['Row_'+i]['OutTime']+"</td>"+
                            "</tr>";
                }
                $("#BoxList tbody").html(Tbody);

                $("#SizeModal-PB").addClass(SizeModal);
                $("#PreviewBox").modal("show");
            });

            $(".BoxCode").on("click", function(e) {
                e.preventDefault();
                var ID_Box = $(this).attr("data-idbox");
                // $("#ItemInBox").html("<tr><td colspan='6' class='text-center'>กำลังโหลด <i class='fas fa-spinner fa-pulse'></i></td></tr>");
                $.ajax({
                    url: "menus/sale/ajax/ajaxordermng.php?p=CallBoxDetail",
                    type: "POST",
                    data: { ID : ID_Box, },
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj, function(key,inval) {
                            var Tbody = "";
                            for(var i = 1; i <= inval['Row']; i++) {
                                Tbody +="<tr class='text-center'>"+
                                            "<td>"+i+"</td>"+
                                            "<td>"+inval['Row_'+i]['ItemCode']+"</td>"+
                                            "<td>"+inval['Row_'+i]['BarCode']+"</td>";
                                            if(inval['Row_'+i]['ItemName'] == null) {
                                                Tbody +="<td class='text-start'>-</td>";
                                            }else{
                                                Tbody +="<td class='text-start'>"+inval['Row_'+i]['ItemName']+"</td>";
                                            }
                                    Tbody +="<td class='text-right'>"+inval['Row_'+i]['Qty']+"</td>"+
                                            "<td>"+inval['Row_'+i]['DateCreate']+"</td>"+
                                        "</tr>";
                            }
                            $("#ItemInBox").html(Tbody);
                            
                        });
                    }
                });
                $("#BoxList tbody tr").removeAttr("class");
                $(this).parents("tr").addClass("table-danger");
            })
        }
    })
}

function UpdateSO() {
    var PickID     = $("#view_PickID").val();
    var DocDueDate = $("#view_DocDueDate").val();
    var PickerName = $("#view_PickerName").val();
    var TablePack  = $("#view_TablePack").val();
    var tabno      = parseFloat($("#main-tabs a.active").attr("data-tab"));
    // console.log("UpdateSO");
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=UpdateSO",
        type: "POST",
        data: {
            pid: PickID,
            ddd: DocDueDate,
            pkn: PickerName,
            tpk: TablePack
        },
        success: function(result) {
            $(".overlay").hide();
            $(".modal").modal('hide');
            $("#confirm_saved").modal("show");
            $(document).off("click","#btn-save-reload").on("click","#btn-save-reload", function(e) {
                e.preventDefault();
                CallTab(tabno);
            });
        }
    });
}

function UpdateWO() {
    var PickID     = $("#view_WOPickID").val();
    var DocDueDate = $("#view_WODocDueDate").val();
    var PickerName = $("#view_WOPickerName").val();
    var TablePack  = $("#view_WOTablePack").val();
    var tabno      = parseFloat($("#main-tabs a.active").attr("data-tab"));
    // console.log(PickID,DocDueDate,PickerName,TablePack,tabno);
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=UpdateWO",
        type: "POST",
        data: {
            pid: PickID,
            ddd: DocDueDate,
            pkn: PickerName,
            tpk: TablePack
        },
        success: function(result) {
            $(".overlay").hide();
            $(".modal").modal('hide');
            $("#confirm_saved").modal("show");
            $(document).off("click","#btn-save-reload").on("click","#btn-save-reload", function(e) {
                e.preventDefault();
                CallTab(tabno);
            });
        }
    });
}

function SearchBox() {
    var text_box = $("#filt_search").val().toUpperCase();
    var prefix   = text_box.substring(0,2);
    var method   = null;

    if(text_box.length > 0) {
        switch(prefix) {
            case "AA":
            case "HA":
            case "IC":
            case "IV":
                method = "OINV";
                break;
            case "PA":
            case "PB":
            case "PC":
            case "PD":
                method = "ODLN";
                break;
            default:
                method = "ORDR";
                break;
        }

        $.ajax({
            url: "menus/sale/ajax/ajaxordermng.php?p=SearchBox",
            type: "POST",
            data: {
                method: method,
                txtbox: text_box
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    var Rows = parseFloat(inval['Rows']);
                    var tBody = "";
                    if(Rows == 0) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่พบรายการที่ค้นหา");
                        $("#alert_modal").modal('show');
                    } else {
                        for(i = 0; i < Rows; i++) {
                            if(inval[i]['SODocType'] == "ORDR") {
                                var DocNum = "<a href='javascript:void(0);' onclick='CallSO(\""+inval[i]['SODocEntry']+"\");'>"+inval[i]['DocNum']+"</a>";
                            } else {
                                var DocNum = "<a href='javascript:void(0);' onclick='CallWO(\""+inval[i]['SODocEntry']+"\");'>"+inval[i]['DocNum']+"</a>";
                            }

                            if(inval[i]['StatusDoc'] == 14) {
                                var ShipTrack = "<a href='javascript:void(0);' onclick='ShipTrack(\""+inval[i]['BillEntry']+"\",\""+inval[i]['BillType']+"\")'><i class='far fa-file-alt fa-fw fa-1x'></i></a>";
                            } else {
                                var ShipTrack = "";
                            }
                            var trClass ="";
                            switch(inval[i]['StatusDoc']) {
                                case '0': trClass = " class='text-muted table-active'"; break;
                                case '4':
                                case '5': trClass = " class='text-warning table-warning'"; break;
                                case '6': trClass = " class='text-info table-info'"; break;
                                case '14': trClass = " class='text-success table-success'"; break;
                                default: trClass = ""; break;
                                
                            }
                            tBody +=
                                "<tr"+trClass+">"+
                                    "<td class='text-center'>"+DocNum+"</td>"+
                                    "<td>"+inval[i]['CardName']+"</td>"+
                                    "<td class='text-center'>"+inval[i]['DocDate']+"</td>"+
                                    "<td class='text-center'>"+inval[i]['DocDueDate']+"</td>"+
                                    "<td>"+inval[i]['StatusTxt']+" <a href='javascript:void();' onclick='CallTab("+inval[i]['CallTab']+");'><i class='fas fa-arrow-right fa-fw fa-1x'></i></a></td>"+
                                    "<td class='text-center'>"+ShipTrack+"</td>"+
                                "</tr>";
                        }
                        $("#SearchResult").modal('show');
                    }
                    $("#ResultList tbody").html(tBody);
                })
            }
        });
    }
}

function PrintSO() {
    var DocEntry = $("#view_DocEntry").val();
    window.open ('menus/sale/print/printpickso.php?docety='+DocEntry,'_blank');
}

function PrintWO() {
    var DocEntry = $("#view_WODocEntry").val();
    var DocType  = $("#view_WODocTypeCode").val();
    window.open ('menus/general/print/printwo.php?DocEntry='+DocEntry+'&Type='+DocType,'_blank');
}

function PrintDL(type) {
    switch(type) {
        case 0:
            var PickID   = $("#OB_PickID").val();
            var geturl   = "?PickID="+PickID;
        break;
        case 1:
            var DocEntry = $("#view_IVDocEntry").val();
            var DocType  = $("#view_IVDocType").val();
            var geturl   = "?DocEntry="+DocEntry+"&Type="+DocType;
        break;
    }
    window.open ('menus/sale/print/printdl.php'+geturl,'_blank')
}

function PrintDoc() {
    var SearchBox = $("#filt_search").val();
    if(SearchBox == "" || SearchBox.length == 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกเลขที่ใบสั่งขายหรือใบฝากงานลงในช่องค้นหา");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/sale/ajax/ajaxordermng.php?p=PrintDoc",
            type: "POST",
            data: {
                DocNum: SearchBox
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    var Status = inval['GetStatus'];
                    if(Status == "SUCCESS") {
                        if(inval['DocType'] == "ORDR") {
                            var DocEntry = inval['DocEntry'];
                            window.open ('menus/sale/print/printpickso.php?docety='+DocEntry,'_blank');
                        } else {
                            var DocEntry = inval['DocEntry'];
                            var DocType  = inval['DocType'];
                            window.open ('menus/general/print/printwo.php?DocEntry='+DocEntry+'&Type='+DocType,'_blank');
                        }
                    } else {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่พบเลขที่ใบสั่งขาย/ใบฝากงานในระบบ<br/>หรือกรุณากรอกข้อมูลให้ครบถ้วน");
                        $("#alert_modal").modal('show');
                    }
                });
            }
        })
    }
}

function UpdateStatus(StatusDoc) {
    var PickID    = $("#view_PickID").val();
    var WOPickID  = $("#view_WOPickID").val();
    var CutPickID = $("#view_CUTPickID").val();
    var StatusDoc = StatusDoc;
    var tabno     = parseFloat($("#main-tabs a.active").attr("data-tab"));
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=UpdateStatus",
        type: "POST",
        data: {
            pid: PickID,
            wid: WOPickID,
            cid: CutPickID,
            std: StatusDoc
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $(".overlay").hide();
                $(".modal").modal('hide');
                $("#confirm_saved").modal("show");
                $(document).off("click","#btn-save-reload").on("click","#btn-save-reload", function(e) {
                    e.preventDefault();
                    CallTab(tabno);
                });
            })
        }
    });
}

$(document).ready(function(){
    CallHead();
    CallTab(DefaultTab());
    GetPickerName();

});

$("#filt_year, #filt_month").on("change", function(){
    CallTab(7);
});

$("#filt_search").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#OrderTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

$("#main-tabs .nav-item a.btn-tabs").on("click",function(e) {
    $(".overlay").show();
    e.preventDefault();
    var Template = $(this).attr("data-template");
    var TabState = $(this).attr("data-tab")
    var theadtmp;
    /* Generate Thead */
    switch(Template) {
        case "1":
            /* TAB 1 & 8 */
            if(TabState == "8") {
                var dis = " disabled";
            } else {
                var dis = "";
            }
            theadtmp =  "<tr>"+
                            "<td><input type='checkbox' id='addallso'"+dis+" /></td>"+
                            "<th>วันที่เอกสาร</th>"+
                            "<th>กำหนดส่ง</th>"+
                            "<th>เลขที่ใบสั่งขาย</th>"+
                            "<th>ชื่อลูกค้า</th>"+
                            "<th>เอกสารอ้างอิง</th>"+
                            "<th>มูลค่า (บาท)</th>"+
                            "<th>ทีม</th>"+
                            "<th>พนักงานขาย</th>"+
                            "<th>จำนวนรายการ</th>"+
                        "</tr>";
            break;
        case "2":
            /* TAB 2 & 3 */
            theadtmp =  "<tr>"+
                            "<th>วันที่เอกสาร</th>"+
                            "<th>กำหนดส่ง</th>"+
                            "<th>เลขที่ใบสั่งขาย</th>"+
                            "<th>ชื่อลูกค้า</th>"+
                            "<th>เอกสารอ้างอิง</th>"+
                            "<th>มูลค่า (บาท)</th>"+
                            "<th>ทีม</th>"+
                            "<th>พนักงานขาย</th>"+
                            "<th>พนักงานเบิก</th>"+
                            "<th>โต๊ะแพ็ก</th>"+
                            "<th>สถานะ</th>"+
                        "</tr>";
            break;
        case "3":
            /* TAB 4 */
            theadtmp =  "<tr>"+
                            "<th>วันที่เอกสาร</th>"+
                            "<th>กำหนดส่ง</th>"+
                            "<th>เลขที่ใบสั่งขาย</th>"+
                            "<th>ชื่อลูกค้า</th>"+
                            "<th>ทีม</th>"+
                            "<th>พนักงานขาย</th>"+
                            "<th>พนักงานเบิก</th>"+
                            "<th>เอกสารอ้างอิง</th>"+
                            "<th>มูลค่า (บาท)</th>"+
                            "<th>เลขที่บิล</th>"+
                            "<th>พนักงานเปิดบิล</th>"+
                            "<th>โต๊ะแพ็ก</th>"+
                            "<th>สถานะ</th>"+
                        "</tr>";
            break;
        case "4":
            /* TAB 5 & 6 */
            theadtmp =  "<tr>"+
                            "<th>วันที่เอกสาร</th>"+
                            "<th>กำหนดส่ง</th>"+
                            "<th>เลขที่ใบสั่งขาย</th>"+
                            "<th>ชื่อลูกค้า</th>"+
                            "<th>เอกสารอ้างอิง</th>"+
                            "<th>ทีม</th>"+
                            "<th>พนักงานขาย</th>"+
                            "<th>เลขที่บิล</th>"+
                            "<th>พนักงานเปิดบิล</th>"+
                            "<th>โต๊ะแพ็ก</th>"+
                            "<th>ลัง</th>"+
                            "<th>สถานะ</th>"+
                        "</tr>";
            break;
        case "5":
            /* TAB 7 */
            theadtmp =  "<tr>"+
                            "<th>วันที่เอกสาร</th>"+
                            "<th>กำหนดส่ง</th>"+
                            "<th>เลขที่ใบสั่งขาย</th>"+
                            "<th>ชื่อลูกค้า</th>"+
                            "<th>เอกสารอ้างอิง</th>"+
                            "<th>ทีม</th>"+
                            "<th>พนักงานขาย</th>"+
                            "<th>เลขที่บิล</th>"+
                            "<th>ลัง</th>"+
                            "<th>สถานะ</th>"+
                            "<th><i class='fas fa-file-alt fa-fw fa-1x'></i>"+
                            "<th><i class='fas fa-paperclip fa-fw fa-1x'></i>"+
                        "</tr>";
            break;
        case "6":
            /* TAB 9 */
            theadtmp =  "<tr>"+
                            "<th>เลขที่ใบสั่งขาย</th>"+
                            "<th>ชื่อลูกค้า</th>"+
                            "<th>ทีม</th>"+
                            "<th>พนักงานขาย</th>"+
                            "<th>เลขที่บิล</th>"+
                            "<th width='4.5%'>ลังทั้งหมด</th>"+
                            "<th width='4.5%'>โหลดแล้ว</th>"+
                            "<th width='4.5%'>คงเหลือ</th>"+
                            "<th><i class='fas fa-file-alt fa-fw fa-1x'></i>"+
                        "</tr>";
            break;
    }
    var y     = $("#filt_year").val();
    var m     = $("#filt_month").val();
    var tabno = TabState;
    /* AJAX */
    if(tabno == 9) {
        var GetUrlTab = "menus/sale/ajax/ajaxordermng.php?p=GetOrderBacklog";
    }else{
        var GetUrlTab = "menus/sale/ajax/ajaxordermng.php?p=GetOrder";
    }
    $.ajax({
        url: GetUrlTab,
        type: "POST",
        data: {
            y: y,
            m: m,
            tabno: tabno
        },
        success: function(result) {
            $(".overlay").hide();
            $("#OrderTable thead").html(theadtmp);
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#OrderTable tbody").html(inval['OrderList']);
            });
            $("#addallso").on("click",function() {
                if($(this).is(":checked")) {
                    $(".addso").prop("checked",true);
                    
                } else {
                    $(".addso").prop("checked",false);
                }
            });
            // console.log(tabno);
            if(tabno == 1) {
                $("#btn_thanos").removeAttr("disabled");
            } else {
                $("#btn_thanos").attr("disabled",true);
            }
        }
    });
});

function AddPicker() {
    var arrPickID = [];
    $.each($(".addso:checked"), function() {
        arrPickID.push($(this).val());
    });
    var CountChk = arrPickID.length;
    if(CountChk == 0) {
        var SearchBar = $("#filt_search").val();
        if(SearchBar == "" || SearchBar.length == 0) {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณาเลือกรายการอย่างน้อย 1 รายการ<br/>หรือกรุณากรอกเลขที่ SO ในช่องค้นหา");
            $("#alert_modal").modal('show');
        } else {
            $.ajax({
                url: "../core/ajaxaddpicker.php",
                type: 'POST',
                data: {
                    DocNum: SearchBar
                },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key, inval) {
                        // console.log(inval['ThanosTxt']);
                        switch(inval['ThanosTxt']) {
                            case "ERR::NORESULT":
                            case "ERR::DUPLICATE":
                                if(inval['ThanosTxt'] == "ERR::NORESULT") {
                                    var modal_body = "ไม่พบเลขที่ใบสั่งขายนี้ในระบบ SAP<br/>หรือกรุณากรอกข้อมูลใบสั่งขายให้ครบถ้วน";
                                } else {
                                    var modal_body = "ไม่สามารถจัดสรรได้ เนื่องจากใบสั่งขายนี้มีการจัดสรรไปแล้ว";
                                }
                                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                $("#alert_body").html(modal_body);
                                $("#alert_modal").modal('show');
                            break;
                            default:
                                $("#confirm_saved").modal("show");
                                $(document).off("click","#btn-save-reload").on("click","#btn-save-reload", function(e) {
                                    e.preventDefault();
                                    location.reload();
                                });
                            break;
                        }
                    })
                }
            });
        }
    } else {
        for(i=0;i<CountChk;i++) {
            // console.log(arrPickID[i]);
            var pid = arrPickID[i]
            $.ajax({
                url: "../core/ajaxaddpicker.php",
                type: 'POST',
                data: {
                    pid: pid
                }
            });
        }
        $("#confirm_saved").modal("show");
        $(document).off("click","#btn-save-reload").on("click","#btn-save-reload", function(e) {
            e.preventDefault();
            location.reload();
        });

    }
}

function Send(DocEntry,DocType) {
    $("#file_upload, #Ship_Name, #Date_Receive").val("");
    $("#ListFile, #showimg").html("");
    $("#ShipTeam").val("").change();
    $("#ST_Received").prop("checked",false);
    if(DocEntry != null || DocType != null) {
        $.ajax({
            url: "menus/sale/ajax/ajaxordermng.php?p=GetSender",
            type: "POST",
            data: { DocEntry: DocEntry, DocType: DocType },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    if(DocType == "OINV" || DocType == "ODLN") {
                        if(inval['Status'] == "OK") {
                            $("#SendDocEntry").val(DocEntry);
                            $("#SendDocType").val(DocType);
                            $("#Ship_Name").val(inval['LogiName']);
                            $("#Date_Receive").val(inval['LoadDate']);
                            let TeamCode = "";
                            switch(inval['TeamCode']) {
                                case "MT1":
                                case "EXP": TeamCode = "MT1"; break;
                                default: TeamCode = inval['TeamCode']; break;
                            }
                            $("#ShipTeam").val(TeamCode).change();
                            $("#SendModal").modal('show');

                            $("#ST_Received").prop("checked",true);
                        } else {
                            $("#SendModal").modal('hide');
                            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
                            $("#alert_body").html("บิลนี้ยังไม่ได้คีย์รับบิลคืน");
                            $("#alert_modal").modal('show');
                        }
                    } else {
                        $("#Ship_Name").val(inval['LogiName']);
                        let TeamCode = "";
                        switch(inval['TeamCode']) {
                            case "MT1":
                            case "EXP": TeamCode = "MT1"; break;
                            case "TT1":
                            case "OUL": TeamCode = "OUL"; break;
                            default: TeamCode = inval['TeamCode']; break;
                        }
                        $("#ShipTeam").val(TeamCode).change();
                        $("#SendDocEntry").val(DocEntry);
                        $("#SendDocType").val(DocType);
                        $("#SendModal").modal('show');
                    }
                });
            }
        })
    }else{
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
        $("#alert_body").html("เกิดข้อผิดพลาดโปรดแจ้ง IT");
        $("#alert_modal").modal('show');
    }
}

function ViewFile() {
    var show = "";
    var img = 0;
    var pdf = 0;
    if(file_upload.files.length != 0) {
        for(var i = 0; i < file_upload.files.length; i++) {
            var [file] = [file_upload.files[i]];
            var Chk = file['name'].substring(file['name'].length - 3);
            if(Chk != 'pdf') {
                if(i == 0){
                    show += "<div class='carousel-item active text-center p-2'>"+
                                "<img src='"+URL.createObjectURL(file)+"' style='width: 100%;'>"+
                                "<div class='carousel-caption d-none d-md-block'></div>"+
                            "</div>";
                }else{
                    show += "<div class='carousel-item text-center p-2'>"+
                                "<img src='"+URL.createObjectURL(file)+"' style='width: 100%;'>"+
                                "<div class='carousel-caption d-none d-md-block'></div>"+
                            "</div>";
                }
                ++img;
            }else{
                ++pdf;
            }
        }

        var add_pdf = "";
        var add_img = "";
        if(pdf != 0) {
            add_pdf = "PDF มี "+pdf+" ไฟล์ <i class='fas fa-file-pdf text-primary'></i>";
        }
        if(img != 0) {
            var showimg="<div class='carousel-inner'>"+show+"</div>"+
                        "<button class='carousel-control-prev' type='button' data-bs-target='#showimg' data-bs-slide='prev'>"+
                            "<span class='carousel-control-prev-icon' aria-hidden='true'></span>"+
                            "<span class='visually-hidden'>Previous</span>"+
                        "</button>"+
                        "<button class='carousel-control-next' type='button' data-bs-target='#showimg' data-bs-slide='next'>"+
                            "<span class='carousel-control-next-icon' aria-hidden='true'></span>"+
                            "<span class='visually-hidden'>Next</span>"+
                        "</button>";
            $("#showimg").html(showimg);
            add_img = "ภาพมี "+img+" ไฟล์ <i class='far fa-image text-info'></i>";
        }
        $("#ListFile").html(add_img+"&nbsp;&nbsp;&nbsp;"+add_pdf);
    }else{
        $("#file_upload").val("");
        $("#ListFile, #showimg").html("");
    }
}

function SaveSend() {
    if($("#Ship_Name").val() == "" || $("#Date_Received").val() == "" || $("#Name_Received").val() == "" || $("#ST_Received").val() == ""){
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบ !");
        $("#alert_modal").modal('show');
    }else{
        var SendForm = new FormData($("#SendForm")[0]);
        if($("#ST_Received").is(":checked") == true) {
            var Satus_Receive = "Y";
        }else{
            var Satus_Receive = "N";
        }
        SendForm.append('Satus_Receive',Satus_Receive);
        $.ajax({
            url: "menus/sale/ajax/ajaxordermng.php?p=SaveSend",
            type: "POST",
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: SendForm,
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    if(inval['output'] == 'SUCCESS') {
                        $("#SendModal").modal('hide');
                        $("#confirm_saved").modal('show');
                        $("#order_tab6").click();
                    }else{
                        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
                        $("#alert_body").html("เกิดข้อผิดพลาดโปรดแจ้ง IT");
                        $("#alert_modal").modal('show');
                    }
                })
            }
        })
    }
}

function ShipTrack(DocEntry,DocType) {
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
                                            // "<td>"+
                                            //     "<a class='att_delect' href='javascript:void(0);' data-idatt='"+inval[i]['TransID']+"'><i class='fas fa-trash text-danger'></i></a>"+
                                            // "</td>"+
                                        "</tr>";
                        }
                    }else{
                        // ไม่มีรูป
                        output +=   "<tr class='text-center'>"+
                                        "<td>ไม่มีข้อมูลใบขนส่งสินค้า</td>"
                                    "</tr>";
                    }
                    $("#TableIMGShipTrack tbody").html(output);
                    $("#DocEntryDelete").val(inval['DocEntry']);
                    $("#DocTypeDelete").val(inval['DocType']);
                    $("#ShipTrackModal").modal("show");
                }else{
                    $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
                    $("#alert_body").html("ไม่มีข้อมูลใบขนส่งสินค้า");
                    $("#alert_modal").modal('show');
                }
            })

            // $(".att_delect").on("click", function(e) {
            //     e.preventDefault();
            //     var ID_Att = $(this).attr("data-idatt")
            //     $("#ID_Att").val(ID_Att);
            //     $("#ConDelectShipTrack").modal("show");
            // })
        } 
    })
}


function cancelSend(DocEntry,DocType) {
    // console.log($("#DocEntryDelete").val()+" | "+$("#DocTypeDelete").val());
    $("#ModalCancelSend").modal("show");
}

function ConCancelSend() {
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=ConCancelSend",
        type: "POST",
        data: { DocEntry : $("#DocEntryDelete").val(), DocType : $("#DocTypeDelete").val(),}, 
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 70px;'></i>");
                $("#alert_body").html("ยกเลิกสำเร็จ");
                $("#alert_modal").modal('show');
                $("#ShipTrackModal").modal("hide");
                $("#order_tab7").click();
            })
        }
    })
}

function CallLoad(BillEntry, BillType) {
    // console.log(BillEntry, BillType);
    $.ajax({
        url: "menus/sale/ajax/ajaxordermng.php?p=CallLoad",
        type: "POST",
        data: { BillEntry : BillEntry, BillType : BillType, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#TableCallLoad tbody").html(inval['Data']);
                $("#ModalCallLoad").modal("show");
            })
        }
    })
}
</script> 
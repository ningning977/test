<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>
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
                        <a href="#OrderList" class="btn-tabs nav-link active" id="OrderList-tab" data-bs-toggle="tab" data-tabs="0" aria-controls="OrderList" aria-selected="false">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการฝากงานคลังสินค้า
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#NewOrder" class="btn-tabs nav-link" id="NewOrder-tab" data-bs-toggle="tab" data-tabs="1" aria-controls="NewOrder" aria-selected="true">
                            <i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มฝากงานคลังสินค้า
                        </a>
                    </li>
                </ul>
                <!-- END CONTENT TAB-->
                <div class="tab-content">
                    <!-- TAB 0 -->
                    <div class="tab-pane fade show active" id="OrderList" role="tabpanel" aria-labelledby="OrderList-tab">
                        <div class="row mt-4">
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
                                    <label for="filt_team">เลือกเดือน</label>
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
                            <div class="col-lg-2 col-6">
                                <div class="form-group">
                                    <label for="filt_team">เลือกทีม</label>
                                    <select class="form-select form-select-sm" name="filt_team" id="filt_team">
                                        <option value="ALL"<?php echo $opt_dis; ?>>ทุกทีม</option>
                                    <?php
                                        $DeptSQL = "SELECT T0.DeptCode, T0.DeptName FROM departments T0 ORDER BY T0.DeptCode ASC";
                                        $DeptQRY = MySQLSelectX($DeptSQL);
                                        while($DeptRST = mysqli_fetch_array($DeptQRY)) {
                                            if(($DeptCode != "DP001" && $DeptCode != "DP002") && ($DeptCode != $DeptRST['DeptCode'])) {
                                                $opt_dis = " disabled";
                                            } else {
                                                $opt_dis = NULL;
                                            }
                                            echo "<option value='".$DeptRST['DeptCode']."'$opt_dis>".$DeptRST['DeptName']."</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="offset-lg-4 col-lg-3 col-6">
                                <div class="form-group">
                                    <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i>  ค้นหา:</label>
                                    <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรุณากรอกเพื่อค้นหา..." />
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <!-- TABLE -->
                            <table class="table table-bordered table-hover table-sm" style="font-size: 12px;">
                                <thead class="text-center">
                                    <tr>
                                        <th width="7.5%">วันที่ฝากงาน</th>
                                        <th width="10%">เลขที่ฝากงาน</th>
                                        <th width="7.5%">วันที่นัดหมาย</th>
                                        <th width="7.5%">รายละเอียด</th>
                                        <th>ลูกค้า / ผู้ติดต่อ</th>
                                        <th width="12.5%">ฝ่าย</th>
                                        <th width="7.5%">สถานะ</th>
                                        <th width="5%">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="WhOrderList"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- TAB 1 -->
                    <div class="tab-pane fade show" id="NewOrder" role="tabpanel" aria-labelledby="NewOrder-tab">
                        <form class="form" id="OrderForm" enctype="multipart/form-data">
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h4 class="h4">Step 1: กรอกรายละเอียดการฝากงาน</h4>
                                    <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-lg-3 col-6">
                                    <div class="form-group mb-3">
                                        <label for="DocDate">วันที่เอกสาร<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="DocDate" id="DocDate" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" required />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="form-group mb-3">
                                        <label for="DocDueDate">วันที่กำหนดรับ/ส่ง<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="DocDueDate" id="DocDueDate" value="<?php echo date("Y-m-d", strtotime(' +1 day')); ?>" min="<?php echo date("Y-m-d", strtotime(' +1 day')); ?>" required />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="form-group mb-3">
                                        <label for="MainDocType">ประเภทการฝากงาน<span class="text-danger">*</span></label>
                                        <select class="form-select" name="MainDocType" id="MainDocType">
                                            <option value="NULL" selected disabled>กรุณาเลือก</option>
                                            <option value="R">ฝากรับสินค้า</option>
                                            <option value="S">ฝากส่งสินค้า</option>
                                            <option value="B">ฝากเบิกสินค้าหน้าร้าน</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="form-group mb-3">
                                        <label for="SubDocType">งานย่อย</label>
                                        <select class="form-select" name="SubDocType" id="SubDocType" disabled>
                                            <option value="NULL" selected disabled>กรุณาเลือกประเภทฝากงานก่อน...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="ContactName">ชื่อลูกค้าติดต่อรับ/ส่ง<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ContactName" id="ContactName" placeholder="กรุณากรอกชื่อลูกค้าที่ต้องการติดต่อ" list="ContactList" />
                                        <datalist id="ContactList"></datalist>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-9">
                                    <div class="form-group mb-3">
                                        <label for="ContactPerson">บุคคลหรือหน่วยงานที่ต้องการติดต่อรับ/ส่ง</label>
                                        <input type="text" class="form-control" name="ContactPerson" id="ContactPerson" placeholder="กรุณากรอกชื่อบุคคลหรือหน่วยงานที่ต้องการติดต่อ" />
                                        <datalist id="ContactList"></datalist>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-3">
                                    <div class="form-group mb-3">
                                        <label for="ContactTel">เบอร์โทรศัพท์ติดต่อ</label>
                                        <input type="text" class="form-control" name="ContactTel" id="ContactTel" placeholder="กรุณากรอกเบอร์โทรศัพท์" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="ContactAddress">ที่อยู่สำหรับติดต่อรับ/ส่ง</label>
                                        <textarea class="form-control" name="ContactAddress" id="ContactAddress" rows="5" placeholder="กรุณากรอกที่อยู่สำหรับติดต่อ"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group mb-3">
                                        <label for="DocDetail">รายละเอียดการฝากงาน</label>
                                        <textarea class="form-control" name="DocDetail" id="DocDetail" rows="5" placeholder="กรุณากรอกรายละเอียดการฝากงาน"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-1 col-6">
                                    <div class="form-group mb-3">
                                        <label for="TotalBox">จำนวนลัง</label>
                                        <input type="number" class="form-control text-right" name="TotalBox" id="TotalBox" step="1" value="0" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="form-group mb-3">
                                        <label for="FileAttach">แนบไฟล์</label>  <a href="javascript:void(0);" class="text-muted" data-bs-toggle="tooltip" title="รองรับนามสกุลไฟล์รูปภาพ (*.jpg, *.jpeg, *.png) / MS Word (*.doc, *.docx) / MS Excel (*.xls, *.xlsx) / เอกสาร (*.pdf) เท่านั้น"><i class="far fa-question-circle fa-fw fa-lg"></i></a>
                                        <input type="file" class="form-control" name="FileAttach[]" id="FileAttach" accept=".jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf" multiple />
                                        <small class="text-danger" id="TextAttach"></small>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <div class="form-group mb-3">
                                        <label for="ShippingName">เลือกผู้ให้บริการขนส่ง</label>
                                        <!-- <input type="text" class="form-control" name="ShippingName" id= "ShippingName" /> -->
                                        <select class="form-control" name="ShippingName" id="ShippingName" data-live-search="true" data-size="10"></select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="form-group mb-3">
                                        <label for="ShippingAddress">ที่อยู่ของผู้ให้บริการขนส่ง</label>
                                        <input type="text" class="form-control" name="ShippingAddress" id="ShippingAddress" placeholder="กรุณากรอกที่อยู่ผู้ให้บริการขนส่ง" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-6">
                                    <div class="form-group mb-3">
                                        <label for="ShippingTel">เบอร์ติดต่อผู้ให้บริการขนส่ง</label>
                                        <input type="text" class="form-control" name="ShippingTel" id="ShippingTel" placeholder="กรุณากรอกเบอร์ติดต่อผู้ให้บริการขนส่ง" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-6">
                                    <div class="form-group mb-3">
                                        <label for="ShippingType">การจ่ายค่าจัดส่ง</label>
                                        <select class="form-select" name="ShippingType" id="ShippingType">
                                            <option value="0" selected>ไม่มีค่าขนส่ง</option>
                                            <option value="1">บริษัทฯ เป็นผู้จ่ายค่าขนส่ง</option>
                                            <option value="2">ปลายทางเป็นผู้จ่ายค่าขนส่ง</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-6">
                                    <div class="form-group mb-3">
                                        <label for="ShippingCost">ค่าขนส่งสินค้า</label>
                                        <input type="number" class="form-control text-right" name="ShippingCost" id="ShippingCost" value="0.00" step="any" />
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <h4 class="h4">Step 2: เพิ่มข้อมูลสินค้า</h4>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-5 col-12">
                                    <div class="form-group mb-3">
                                        <label for="AddItemName">ชื่อสินค้า</label>
                                        <input type="text" class="form-control" name="AddItemName" id="AddItemName" list="AddItemList" />
                                        <datalist id="AddItemList"></datalist>
                                        <!-- <input type="number" name="RowID" id="RowID" readonly /> -->
                                    </div>
                                </div>
                                <div class="col-lg-2 col-3">
                                    <div class="form-group mb-3">
                                        <label for="AddQuantity">จำนวน</label>
                                        <input type="number" class="form-control text-right" name="AddQuantity" id="AddQuantity" step="1" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-3">
                                    <div class="form-group mb-3">
                                        <label for="AddUnitMsr">หน่วย</label>
                                        <input type="text" class="form-control" name="AddUnitMsr" id="AddUnitMsr" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-3">
                                    <div class="form-group mb-3">
                                        <label for="AddWhsCode">คลังสินค้า</label>
                                        <input type="text" class="form-control" name="AddWhsCode" id="AddWhsCode" list="WhsList">
                                        <datalist id="WhsList">
                                            <option value="KSY">คลังขาย KSY</option>
                                            <option value="KB4">คลังอะไหล่ KB4</option>
                                            <option value="MT">คลังขาย MT1</option>
                                            <option value="MT2">คลังขาย MT2</option>
                                            <option value="TT-C">คลังขาย TT</option>
                                            <option value="OUL">คลังขายหน้าร้าน (KSY)</option>
                                            <option value="WM1">คลังมือสอง MT1</option>
                                            <option value="WM2">คลังมือสอง MT2</option>
                                            <option value="KB5">คลังมือสองส่วนกลาง (KB5)</option>
                                            <option value="KB6">คลังมือสองส่วนกลาง (KB6)</option>
                                            <option value="TT">คลังมือสอง TT</option>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-3">
                                    <div class="form-group mb-3">
                                        <label for="AddButton">&nbsp;</label>
                                        <button type="button" class="btn btn-primary btn-block" onclick="AddRow();"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่ม</button>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table table-responsive tableFixeHead">
                                        <table class="table table-bordered table-striped table-hover" id="ItemList">
                                            <thead class="text-center table-group-divider">
                                                <tr>
                                                    <th width="12.5%">รหัสสินค้า</th>
                                                    <th>ชื่อสินค้า</th>
                                                    <th width="7.5%">คลังสินค้า</th>
                                                    <th width="7.5%">จำนวน</th>
                                                    <th width="7.5%">หน่วย</th>
                                                    <th width="25%">หมายเหตุ</th>
                                                    <th width="7.5%">จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ItemListData"></tbody>
                                        </table>
                                        <input type="hidden" class="form-control" name="TotalRow" id="TotalRow" value="0" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-right">
                                    <button type="button" class="btn btn-primary" onclick="SaveDoc();"><i class="fas fa-save fa-fw fa-1x"></i> บันทึก</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL DELETE ALERT -->
<div class="modal fade" id="confirm_delete" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-question-circle fa-fw fa-lg text-info"></i> ยืนยันการลบ</h5>
                <p id="confirm_body" class="my-4">คุณต้องการลบรายการสินค้านี้หรือไม่?</p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-del-confirm" data-rowid="0" data-bs-dismiss="modal">ตกลง</button>
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

<!-- MODAL CANCEL ALERT -->
<div class="modal fade" id="confirm_cancel" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-question-circle fa-fw fa-lg"></i> ยืนยันการยกเลิก</h5>
                <p id="confirm_body" class="my-4">คุณต้องการยกเลิกใบบันทึกภายในหรือไม่?</p>

                <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-dismiss" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-cancel-confirm" data-docentry="0" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DOC PREVIEW -->
<div class="modal fade" id="ModalPreviewDoc" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file fa-fw fa-lg"></i> รายละเอียดใบฝากงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="h6">ใบฝากงานเลขที่: <span id="view_DocNum"></span></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless table-sm" style="font-size:12px;">
                            <tr>
                                <th width="15%">วันที่ฝากงาน</th>
                                <td width="45%" id="view_DocDate"></td>
                                <th width="15%">วันที่นัดหมาย</th>
                                <td width="25%" class="text-danger" id="view_DocDueDate"></td>
                            </tr>
                            <tr>
                                <th>ชื่อผู้ฝากงาน</th>
                                <td id="view_CreateName"></td>
                                <th>ฝ่าย</th>
                                <td id="view_DeptName"></td>
                            </tr>
                            <tr>
                                <th>ประเภทการฝากงาน</th>
                                <td colspan="3" id="view_DocType"></td>
                            </tr>
                            <tr>
                                <th>ชื่อลูกค้า</th>
                                <td colspan="3" id="view_CusCode"></td>
                            </tr>
                            <tr>
                                <th>บุคคลหรือหน่วยงานที่ติดต่อ</th>
                                <td class="text-danger" id="view_ContactName"></td>
                                <th>เบอร์โทร.ติดต่อ</th>
                                <td class="text-danger" id="view_ContactTel"></td>
                            </tr>
                            <tr>
                                <th>ที่อยู่สำหรับติดต่อ</th>
                                <td colspan="3" class="text-danger" id="view_CusAddress"></td>
                            </tr>
                            <tr>
                                <th>รายละเอียดการฝากงาน</th>
                                <td colspan="3" id="view_DocDetail"></td>
                            </tr>
                        </table>
                        <hr/>
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">ลำดับ</th>
                                    <th width="7.5%">รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th width="7.5%">คลัง</th>
                                    <th width="7.5%">จำนวน</th>
                                    <th width="7.5%">หน่วย</th>
                                    <th width="25%">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody id="view_ItemList"></tbody>
                        </table>
                        <hr/>
                        <table class="table table-borderless table-sm" style="font-size:12px;">
                            <tr>
                                <th width="15%">ชื่อผู้ให้บริการขนส่ง</th>
                                <td width="45%" id="view_ShippingName"></td>
                                <th width="15%">เบอร์โทร.ติดต่อ</th>
                                <td width="25%" id="view_ShippingTel"></td>
                            </tr>
                            <tr>
                                <th>ที่อยู่สำหรับติดต่อ</th>
                                <td id="view_ShippingAddress">&nbsp;</td>
                                <th>การจ่ายค่าขนส่ง</th>
                                <td class="text-danger" id="view_ShippingCost"></td>
                            </tr>
                            <tr>
                                <th>จำนวนลังสินค้าที่ต้องรับ/ส่ง</th>
                                <td colspan="3" id="view_TotalBox"></td>
                            </tr>
                            <tr>
                                <th>เอกสารแนบ</th>
                                <td colspan="3" id="view_AttachList"></td>
                            </tr>
                        </table>
                    </div>
                </div>
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

function GetShipName() {
    $.ajax({
        url: "menus/general/ajax/ajaxwhseorder.php?p=GetShipName",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#ShippingName").html(inval['ShipName']).selectpicker();
            });
        }
    })
}

function GetCardName() {

    $.ajax({
        url: "../json/OCRD.json",
        cache: false,
        success: function(result) {
            var filt_data = result.
                                filter(x => x.CardStatus == "A").
                                sort(function(key, inval) {
                                    return key.CardCode.localeCompare(inval.CardCode);
                                });
            var opt = "";

            $.each(filt_data, function(key, inval) {
                opt += "<option value='"+inval.CardCode+" | "+inval.CardName+"'>";
            });
            $("#ContactList").append(opt).selectpicker();
        }
    });
}

function GetItemList() {
    $(".overlay").show();
    $.ajax({
        url: "menus/general/ajax/ajaxwhseorder.php?p=GetItemList",
        type: "POST",
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#AddItemList").html(inval['ItemName']);
            })
        }
    });
}

function AddRow() {
    var ItemName     = $("#AddItemName").val();
    var ItemQuantity = $("#AddQuantity").val();
    var ItemUnitMsr  = $("#AddUnitMsr").val();
    var ItemWhsCode  = $("#AddWhsCode").val();
    var DocType      = $("#MainDocType").val();

    if(ItemName == null || (ItemQuantity.length == 0 || ItemQuantity < 1)) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        if(ItemUnitMsr == null || ItemUnitMsr == "" || ItemUnitMsr.length == 0) {
            ItemUnitMsr = "EA";
        }
        if(ItemWhsCode == null || ItemWhsCode == "" || ItemWhsCode.length == 0) {
            ItemWhsCode = "RC";
        }
        var LastRow = parseInt($("#TotalRow").val());
        var RowID   = LastRow+1;

        var ItemArr    = ItemName.split(" | ");
        var SKUPattern = /^[A-Za-z0-9]{2}-[0-9]{3}-[0-9]{3}$/;
        var Result     = SKUPattern.test(ItemArr[0]);
        var ItemCode;
        var ItemName;
        if(Result == true) {
            ItemCode = ItemArr[0];
            ItemName = ItemArr[1];
        } else {
            ItemCode = "";
            ItemName = ItemName;
        }


        if(DocType == "B" && ItemWhsCode == "RC") {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณากรอกชื่อคลังสินค้า");
            $("#alert_modal").modal('show');
        } else {
            var NewRow =    "<tr data-RowID='"+RowID+"'>"+
                                "<td><input type='text' class='form-control-plaintext text-center' name='ItemCode_"+RowID+"' id='ItemCode_"+RowID+"' value='"+ItemCode+"' readonly /></td>"+
                                "<td><input type='text' class='form-control-plaintext' name='ItemName_"+RowID+"' id='ItemName_"+RowID+"' value='"+ItemName+"' readonly /></td>"+
                                "<td><input type='text' class='form-control-plaintext text-center' name='ItemWhsCode_"+RowID+"' id='ItemWhsCode_"+RowID+"' value='"+ItemWhsCode+"' readonly /></td>"+
                                "<td><input type='number' class='form-control-plaintext text-right' name='ItemQuantity_"+RowID+"' id='ItemQuantity_"+RowID+"' value='"+ItemQuantity+"' readonly /></td>"+
                                "<td><input type='text' class='form-control-plaintext' name='ItemUnitMsr_"+RowID+"' id='ItemUnitMsr_"+RowID+"' value='"+ItemUnitMsr+"' readonly /></td>"+
                                "<td><input type='text' class='form-control' name='Remark_"+RowID+"' id='Remark_"+RowID+"' /></td>"+
                                "<td class='text-center'>"+
                                    "<button type='button' class='btn btn-danger btn-sm' onclick='DeleteItem("+RowID+")'><i class='fas fa-trash fa-fw fa-1x'></i></button>"+
                                "</td>"+
                            "</tr>";

            $("#ItemList").append(NewRow);
            $("#TotalRow").val(RowID);
            $("#AddItemName, #AddQuantity, #AddUnitMsr, #AddWhsCode").val("");
            $("#AddItemName").focus();
        }
    }
}

function DeleteItem(Row) {
    $("#confirm_delete").modal("show");
    $("#btn-del-confirm").attr("data-RowID",Row);

    $("#btn-del-confirm").on("click", function(e){
        var RowID = $(this).attr("data-RowID");
        $("#ItemList tr[data-RowID='"+RowID+"']").remove();
    });
}

function SaveDoc() {
    var ErrorPoint = 0;
    var ErrorID    = [];
    var SuccessID  = [];
    var CheckID    = ["DocDate","DocDueDate","MainDocType","ContactName"];
    if(CheckID.length > 0) {
        for(let i = 0; i < CheckID.length; i++) {
            if($("#"+CheckID[i]).val() == null || $("#"+CheckID[i]).val() == "") {
                ErrorPoint = ErrorPoint+1;
                ErrorID.push(CheckID[i]);
            } else {
                SuccessID.push(CheckID[i]);
            }
        }
    }

    var TotalRow   = $("#ItemList tr[data-rowid]").length;
    var DocType    = $("#MainDocType").val();
    var SubDocType = $("#SubDocType").val();
    var DeptCode   = '<?php echo $_SESSION['DeptCode']; ?>';
    var FileAttach = $("#FileAttach").val();

    if(DocType != "B" && SubDocType == null) {
        ErrorPoint = ErrorPoint+1;
        $("#SubDocType").removeClass("is-valid is-invalid").addClass("is-invalid");
    } else {
        $("#SubDocType").removeClass("is-valid is-invalid").addClass("is-valid");
    }
    if(DocType != "R" && TotalRow == 0) {
        ErrorPoint = ErrorPoint+1;
        $("#AddItemName").removeClass("is-valid is-invalid").addClass("is-invalid");
    }

    if(DocType == "R" && SubDocType == "RD" && DeptCode == "DP005" && FileAttach.length == 0) {
        ErrorPoint = ErrorPoint+1;
        $("#FileAttach").removeClass("is-valid is-invalid").addClass("is-invalid");
        $("#TextAttach").html("กรุณาแนบในขนส่งสินค้า (เฉพาะฝ่ายขาย ตจว.)");
    }
    
    if(ErrorPoint > 0) {
        for(let i = 0; i < ErrorID.length; i++) { $("#"+ErrorID[i]).removeClass("is-valid is-invalid").addClass("is-invalid"); }
        for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-valid is-invalid").addClass("is-valid"); }
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        var OrderForm = new FormData($("#OrderForm")[0]);
        $.ajax({
            url: "menus/general/ajax/ajaxwhseorder.php?p=SaveDoc",
            type: 'POST',
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: OrderForm,
            success: function() {
                $("#confirm_saved").modal('show');
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        });
    }
}

function GetOrderList(filt_year, filt_month, filt_team) {
    $.ajax({
        url: "menus/general/ajax/ajaxwhseorder.php?p=GetOrderList",
        type: "POST",
        data: {
            y: filt_year, 
            m: filt_month, 
            t: filt_team
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#WhOrderList").html(inval['OrderList']);
            });
        }
    });
}

function CancelDoc(DocEntry) {
    var DocEntry = DocEntry;
    $(".modal").modal("hide");
    $("#confirm_cancel").modal("show");

    $("#btn-cancel-confirm").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            url: 'menus/general/ajax/ajaxwhseorder.php?p=CancelDoc',
            type: 'POST',
            data: { DocEntry: DocEntry },
            success: function(result) {
                $("#confirm_saved").modal("show");
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        });
    });
}

function PreviewDoc(DocEntry,intstatus) {
    var DocEntry = DocEntry;
    $.ajax({
        url: "menus/general/ajax/ajaxwhseorder.php?p=PreviewDoc",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#view_DocNum").html(inval['DocNum']);
                $("#view_DocDate").html(inval['DocDate']);
                $("#view_DocDueDate").html(inval['DocDueDate']);
                $("#view_CreateName").html(inval['CreateName']);
                $("#view_DeptName").html(inval['DeptName']);
                $("#view_DocType").html(inval['DocType']);
                $("#view_CusCode").html(inval['CusCode']);
                $("#view_ContactName").html(inval['ContactName']);
                $("#view_ContactTel").html(inval['ContactTel']);
                $("#view_CusAddress").html(inval['CusAddress']);
                $("#view_DocDetail").html(inval['DocDetail']);

                $("#view_ItemList").html(inval['ItemList']);


                $("#view_ShippingName").html(inval['LogiName']);
                $("#view_ShippingTel").html(inval['LogiPhone']);
                $("#view_ShippingAddress").html(inval['LogiAddress']);
                $("#view_ShippingCost").html(inval['LogiCost']);
                $("#view_TotalBox").html(inval['TotalBox']);

                $("#view_AttachList").html(inval['AttList']);
            });
            $("#ModalPreviewDoc").modal("show");
        }
    });
}

function ExportDoc(DocEntry) {
    var DocEntry = DocEntry;
    $.ajax({
        url: "menus/general/ajax/ajaxwhseorder.php?p=ExportDoc",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                if(inval['AddStatus'] == "SUCCESS") {
                    $("#confirm_saved").modal('show');
                    $("#btn-save-reload").on("click", function(e){
                        e.preventDefault();
                        window.location.reload();
                    });
                } else {
                    var alert_header = "<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!";
                    var alert_body;
                    switch(inval['AddStatus']) {
                        case "ERR::DUPLICATE":
                            alert_body   = "ไม่สามารถเพิ่มเอกสารนี้ได้เนื่องจากเอกสารนี้ยังไม่ถูกฝ่ายคลังสินค้าตีกลับในระบบรับ/ส่งเอกสารใบฝากงาน";
                        break;
                        case "ERR::CANNOT_INSERT":
                            alert_body   = "ไม่สามารถเพิ่มเอกสารเข้าไปในฐานข้อมูลได้ กรุณาติดต่อฝ่าย IT";
                        break;
                    }
                    $("#alert_header").html(alert_header);
                    $("#alert_body").html(alert_body);
                    $("#alert_modal").modal('show');
                }
            });
        }
    });
}

function PrintDoc(DocEntry,DocType) {
    var DocEntry = DocEntry;
    var DocType  = DocType;
    window.open('menus/general/print/printwo.php?DocEntry='+DocEntry+'&Type='+DocType,'_blank');
}

$(document).ready(function(){
    CallHead();
    GetShipName();
    GetCardName();
    GetItemList();

    var tooltipTriggerList = [].slice.call($('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    var filt_year = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team = $("#filt_team").val();
    GetOrderList(filt_year, filt_month, filt_team);

    // $("#ModalPreviewDoc").modal("show");
});

$("#MainDocType").on("change",function(){
    var DocType = $(this).val();
    var SubDocType;
    switch(DocType) {
        case "R":
            $("#ContactName").val("");
            SubDocType += "<option value='NULL' selected disabled>กรุณาเลือก</option>";
            SubDocType += "<option value='RP'>ฝากรับสินค้าที่ฝากซื้อ</option>";
            SubDocType += "<option value='RD'>ฝากรับสินค้าคืนที่ MT/ขนส่งหรือไปรษณีย์</option>";
            SubDocType += "<option value='RR'>ฝากรับสินค้าซ่อม</option>";
            $("#SubDocType").removeAttr("disabled");
            break;
        case "S":
            $("#ContactName").val("");
            SubDocType += "<option value='NULL' selected disabled>กรุณาเลือก</option>";
            SubDocType += "<option value='SP'>ฝากส่งสินค้าให้ลูกค้า</option>";
            SubDocType += "<option value='SQ'>ฝากส่งสินค้าที่ไม่รับคืน เคลม เปลี่ยน (จาก QC)</option>";
            $("#SubDocType").removeAttr("disabled");
            break;
        case "B":
            $("#ContactName").val("D-00004 | Co-sales หน้าร้าน");
            SubDocType += "<option value='NULL' selected disabled>กรุณาเลือกประเภทการฝากงานก่อน</option>";  
            $("#SubDocType").attr("disabled",true);
            break;
    }
    $("#SubDocType").html(SubDocType);
});

$("#ShippingName").on("change",function() {
    var ShipAddress = $("#ShippingName option:selected").attr("data-Address");
    var ShipTelNo   = $("#ShippingName option:selected").attr("data-TelNo");
    $("#ShippingAddress").val(ShipAddress);
    $("#ShippingTel").val(ShipTelNo);
});

$("#ContactName").on("focusout",function() {
    var ContactName = $(this).val();
    var ContactArr  = ContactName.split(" | ");
    if(ContactArr[0].length == 7) {
        var CardCode    = ContactArr[0];
        var CardPattern = /^[A-Za-z0-9]{1}-[0-9]{5}$/;
        var Result      = CardPattern.test(CardCode);
        if(Result == true) {
            $(".overlay").show();
            $.ajax({
                url: "menus/general/ajax/ajaxwhseorder.php?p=GetCardDetail",
                type: "POST",
                data: { CardCode: CardCode },
                success: function(result) {
                    $(".overlay").hide();
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key,inval) {
                        if(inval['GetStatus'] == "SUCCESS") {
                            
                            $("#ContactAddress").val(inval['CardAddress']);
                            $("#ContactTel").val(inval['CardTelNo']);
                            if(inval['CardShip'] != "") {
                                $("#ShippingName").selectpicker("destroy");
                                $("#ShippingName").val(inval['CardShip']).change().selectpicker();
                            }
                        }
                    });
                }
            });
        }
    }
});

$("#AddItemName").on("focusout", function() {
    $("#AddQuantity").focus();
});

$("#filt_year, #filt_month, #filt_team").on("change", function() {
    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team  = $("#filt_team").val();
    GetOrderList(filt_year, filt_month, filt_team);
});

$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#WhOrderList tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});
</script> 
<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<style type="text/css">
    .tableFixHead {
        overflow-y: auto;
        height: 500px;
    }
    .tableFixHead table {
        border-collapse: collapse !important;
    }

    .tableFixHead thead tr.first th {
        position: sticky;
        top: 0;
        background: #fff;
    }
    .tableFixHead thead tr.second th {
        position: sticky;
        background: #fff;
    }
    #ItemList, .order-preview {
        font-size: 13px;
    }
    select option:disabled {
        color: #CCCCCC;
    }
    select option.default {
        font-weight: bold !important;
    }
    .font-weight{
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
                        <a href="#PurReqList" class="btn-tabs nav-link active" id="PurReqList-tab" data-bs-toggle="tab" data-bs-target="#PurReqList" role="tab" data-tabs="0" aria-controls="PurReqList" aria-selected="false">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการใบขอซื้อ
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#NewPurReq" class="btn-tabs nav-link" id="NewPurReq-tab" data-bs-toggle="tab" data-bs-target="#NewPurReq" role="tab" data-tabs="1" aria-controls="NewPurReq" aria-selected="true">
                            <i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มใบขอซื้อใหม่
                        </a>
                    </li>
                </ul>
                <!-- END CONTENT TAB -->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="PurReqList" role="tabpanel" aria-labelledby="PurReqList-tab">
                        <div class="row mt-4">
                            <div class="col-lg-1 col-5">
                                <div class="form-group">
                                    <label for="filt_year">เลือกปี</label>
                                    <select class="form-select form-select-sm" name="filt_year" id="filt_year">
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
                                    <select class="form-select form-select-sm" name="filt_month" id="filt_month">
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
                                        if(($DeptCode == "DP001" || $DeptCode == "DP002" || $DeptCode == "DP004" || $DeptCode == "DP012")) {
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
                                            if(($DeptCode != "DP001" && $DeptCode != "DP002" && $DeptCode != "DP004" && $DeptCode != "DP012") && ($DeptCode != $DeptRST['DeptCode'])) {
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
                                    <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา:</label>
                                    <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรุณากรอกเพื่อค้นหา..." />
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered" style="font-size: 12px;">
                                <thead>
                                    <tr class="text-center">
                                        <th width="3.5%">ลำดับ</th>
                                        <th width="7%">วันที่เอกสาร</th>
                                        <th width="7%">วันที่ต้องการสินค้า</th>
                                        <th width="10%">เลขที่เอกสาร</th>
                                        <th width="15%">ประเภทสินค้า</th>
                                        <th>รายละเอียด</th>
                                        <th width="12.5%">ฝ่าย</th>
                                        <th width="7.5%">สถานะเอกสาร</th>
                                        <th width="5%"><i class="fas fa-cog fa-fw fa-1x"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="PurReqTable">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="NewPurReq" role="tabpanel" aria-labelledby="NewPurReq-tab">
                        <form class="form" id="PurchaseForm" enctype="multipart/form-data">
                            <!-- STEP 1 -->
                            <div id="pur-step1" class="need-validation" data-step="1">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 1: กรอกรายละเอียดการขอซื้อสินค้า</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="DocDate">วันที่เอกสาร<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="DocDate" id="DocDate" value="<?php echo date("Y-m-d"); ?>" required />
                                            <input type="hidden" name="PurReqEntry" id="PurReqEntry" readonly />
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="DocDueDate">วันที่ต้องการใช้สินค้า<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="DocDueDate" id="DocDueDate" value="<?php echo date("Y-m-d"); ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="DocType">ประเภทเอกสาร<span class="text-danger">*</span></label>
                                            <select class="form-select" name="DocType" id="DocType" required>
                                                <option selected disabled>กรุณาเลือก</option>
                                                <option value="LC">สั่งซื้อสินค้าในประเทศ (Domestic)</option>
                                                <option value="IM">สั่งซื้อสินค้าต่างประเทศ (Oversea)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="ItemType">ประเภทสินค้าที่ต้องการ<span class="text-danger">*</span></label>
                                            <select class="form-select" name="ItemType" id="ItemType">
                                                <option selected disabled>กรุณาเลือก</option>
                                                <optgroup label="สินค้า">
                                                    <option value="A01">A1 - สินค้าสำเร็จรูปเพื่อขาย</option>
                                                    <option value="A02">A2 - สินค้าอะไหล่เพื่อซ่อม</option>
                                                    <option value="A03">A3 - วัตถุดิบสิ้นเปลือง (สำหรับงานผลิต/แปลง)</option>
                                                    <option value="A04">A4 - สินค้าพรีเมียม</option>
                                                </optgroup>
                                                <optgroup label="อื่น ๆ">
                                                    <option value="B01">B1 - สื่อการตลาด</option>
                                                    <option value="B02">B2 - อุปกรณ์สำนักงาน / วัสดุสิ้นเปลือง</option>
                                                    <option value="B03">B3 - อุปกรณ์ซ่อมบำรุงอาคาร / งานซ่อมอาคาร</option>
                                                    <option value="B04">B4 - ทรัพย์สิน</option>
                                                    <option value="B05">B5 - โปรเจ็กต์ / สัญญาต่าง ๆ</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="ItemQuota">ทีมขายที่จองสินค้า</label>
                                            <select class="form-control selectpicker" name="ItemQuota[]" id="ItemQuota" multiple data-selected-text-format="count" disabled>
                                                <option disabled>กรุณาเลือก</option>
                                                <option value="MT1"><?php echo SATeamName("MT1"); ?></option>
                                                <option value="MT2"><?php echo SATeamName("MT2"); ?></option>
                                                <option value="TT2"><?php echo SATeamName("TT2"); ?></option>
                                                <option value="TT1"><?php echo SATeamName("TT1"); ?></option>
                                                <option value="OUL"><?php echo SATeamName("OUL"); ?></option>
                                                <option value="ONL"><?php echo SATeamName("ONL"); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="ShiptoType">สถานที่จัดส่ง<span class="text-danger">*</span>
                                            <select class="form-select" name="ShiptoType" id="ShiptoType" disabled required>
                                                <option selected disabled>กรุณาเลือก</option>
                                                <option value="KBI">สำนักงานใหญ่ (KBI)</option>
                                                <option value="KSY">คลังสินค้าลาดสวาย (KSY / KSM)</option>
                                                <option value="OTR">อื่น ๆ (ระบุ)</option>
                                                <option value="NULL">ไม่ระบุ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-group mb-3">
                                            <label for="ShiptoAddress">ที่อยู่จัดส่ง</label>
                                            <input type="text" class="form-control" name="ShiptoAddress" id="ShiptoAddress" placeholder="ที่อยู่จัดส่ง (ถ้ามี)" disabled />
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="ShiptoWhse">คลังที่จัดเก็บ</label>
                                            <select class="form-select" name="ShiptoWhse" id="ShiptoWhse" disalbed>
                                                <option selected disabled>กรุณาเลือก</option>
                                                <option value="NULL">ไม่ระบุ</option>
                                                <optgroup data-locate="KBI" label="สำนักงานใหญ่">
                                                    <option value="KB1">KB1 - คลังหน้าร้าน</option>
                                                    <option value="KB1.1">KB1.1 - คลังอะไหล่หน้าร้าน</option>
                                                    <option value="PM-OUL">PM-OUL - คลังพรีเมียม: ฝ่ายขายหน้าร้าน</option>
                                                    <option value="PM-KBI">PM-KBI - คลังพรีเมียม: สำนักงานใหญ่</option>
                                                    <option value="PM-HR">PM-HR - คลังพรีเมียม: เสื้อฟอร์มพนักงาน</option>
                                                    <option value="PMTT-KBI">PMTT-KBI - คลังพรีเมียม: ฝ่ายขาย TT</option>
                                                </optgroup>
                                                <optgroup data-locate="KSY" label="คลังสินค้าลาดสวาย">
                                                    <option value="KSY">KSY - คลังจัดเก็บสินค้า KSY</option>
                                                    <option value="KB4">KB4 - คลังจัดเก็บสินค้าอะไหล่</option>
                                                    <option value="KSM">KSM - คลังจัดเก็บสินค้า KSM</option>
                                                    <option value="KB9">KB9 - คลังวัตถุดิบ</option>
                                                    <option value="PM-KSY">PM-KSY - คลังพรีเมียม: คลังสินค้า KSY</option>
                                                    <option value="PMTT-KSY">PM-TTKSY - คลังพรีเมียม: ฝ่ายขาย TT</option>
                                                </optgroup>
                                                <optgroup data-locate="SUP" label="คลังสินค้าซัพพลายเออร์ (ฝากเก็บ)">
                                                    <option value="PLA">PLA - คลังสินค้า บจ.พลา กรุ๊ป (ไทยแลนด์)</option>
                                                    <option value="IMAX">IMAX - คลังสินค้า บจ.ไอแม็กซ์ เพาเวอร์ทูล</option>
                                                    <option value="NST">NST - คลังสินค้า บจ.นิวแสงไทยอินดัสตรี</option> 
                                                </optgroup>
                                                <optgroup data-locate="SAL" label="คลังสำหรับฝ่ายขาย (มือสอง)">
                                                    <option value="WM1">WM1 - คลังมือสอง ฝ่ายขาย MT1</option>
                                                    <option value="WM2">WM2 - คลังมือสอง ฝ่ายขาย MT2</option>
                                                    <option value="TT">TT - คลังมือสอง ฝ่ายขาย TT2</option>
                                                    <option value="KB7">KB7 - คลังมือสอง ฝ่ายขายหน้าร้าน</option>
                                                    <option value="WA26.1">WA26.1 - คลังมือสอง ฝ่ายขาย TT1</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="RemarkPackage">หมายเหตุสำหรับแพ็คเกจ</label>
                                            <select class="form-select" name="RemarkPackage" id="RemarkPackage" disabled>
                                                <option selected disabled>กรุณาเลือก</option>
                                                <option value="1">สินค้าเก่า แพ็คเกจเดิม</option>
                                                <option value="2">สินค้าเก่า แพ็คเกจใหม่ (ระบุพิกัด)</option>
                                                <option value="3">สินค้าใหม่ (ระบุพิกัด)</option>
                                                <option value="4">สินค้าใหม่ พร้อมขอแพ็คเกจเปล่าสำรอง (ระบุพิกัด)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-group mb-3">
                                            <label for="PackageFilePath">พิกัดจัดเก็บแพ็คเกจ</label>
                                            <input type="text" class="form-control" name="PackageFilePath" id="PackageFilePath" disabled />
                                        </div>
                                    </div>
                                    <!-- <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="RefDocNum">เอกสารอ้างอิง</label>
                                            <input type="text" class="form-control" name="RefDocNum" id="RefDocNum" />
                                        </div>
                                    </div> -->
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="FileAttach">เอกสารแนบ</label> <a href="javascript:void(0);" class="text-muted" data-bs-toggle="tooltip" title="รองรับนามสกุลไฟล์รูปภาพ (*.jpg, *.jpeg, *.png) / MS Word (*.doc, *.docx) / MS Excel (*.xls, *.xlsx) / เอกสาร (*.pdf) เท่านั้น"><i class="far fa-question-circle fa-fw fa-lg"></i></a>
                                            <input type="file" class="form-control" name="FileAttach[]" id="FileAttach" accept=".jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf" multiple />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="PurchaseReasons">เหตุผลในการสั่งซื้อ<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="PurchaseReasons" id="PurchaseReasons" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg text-right">
                                        <button type="button" class="btn-next btn btn-primary" data-step="1" data-goto="2">ต่อไป <i class="fas fa-chevron-right fa-fw fa-1x"></i></button>
                                    </div>
                                </div>
                            </div>
                            <!-- END OF STEP 1 -->

                            <!-- STEP 2 -->
                            <div id="pur-step2" class="need-validation" data-step="2">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 2: เพิ่มรายการสินค้า</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <button type="button" class="btn btn-primary" id="AddItem"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มรายการใหม่</button>
                                        <button type="button" class="btn btn-secondary" id="ImportItem"><i class="fas fa-file-import fa-fw fa-1x"></i> นำเข้า</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <div class="table-responsive tableFixHead">
                                            <table class="table table-bordered table-striped table-hover" id="ItemList">
                                                <thead class="text-center table-group-divider">
                                                    <tr class="first">
                                                        <th rowspan="2" width="7.5%">รหัสสินค้า</th>
                                                        <th rowspan="2">ชื่อสินค้า</th>
                                                        <th rowspan="2" width="5%">จำนวน</th>
                                                        <th rowspan="2" width="5%">หน่วยซื้อ</th>
                                                        <th colspan="3">สกุลเงินที่สั่งซื้อ</th>
                                                        <th colspan="3">สกุลเงินไทย (โดยประมาณ)</th>
                                                        <th colspan="2">ข้อมูลการขาย</th>
                                                        <th rowspan="2" width="7.5%">จัดการ</th>
                                                    </tr>
                                                    <tr class="second">
                                                        <th width="7.5%">ราคาซื้อ<br/>ต่อหน่วย</th>
                                                        <th width="8.25%">ราคาซื้อ<br/>ทั้งหมด</th>
                                                        <th width="5%">สกุลเงิน</th>
                                                        <th width="7.5%">ราคาซื้อ</br>ต่อหน่วย</th>
                                                        <th width="8.25%">ราคาซื้อ</br>ทั้งหมด</th>
                                                        <th width="5%">สกุลเงิน</th>
                                                        <th width="8.25%">ราคาขาย</br>ต่อหน่วย</th>
                                                        <th width="5.5%">กำไร<br/>(%)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ItemListData"></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3"><input type="text" class="form-control" id="Comments" name="Comments" placeholder="หมายเหตุ..." /></td>
                                                        <td colspan="2" class="text-right text-primary" style="font-weight: bold;">ยอดรวมทุกรายการ</td>
                                                        <td><input type="text" class="form-control-plaintext text-right text-primary" style="font-weight: bold;" name="DocTotal" id="DocTotal" value="0.00" readonly /></td>
                                                        <td class="text-primary" style="font-weight: bold;" id="DocCurrency">&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td><input type="text" class="form-control-plaintext text-right text-primary" style="font-weight: bold;" name="DocTotalTHB" id="DocTotalTHB" value="0.00" readonly /></td>
                                                        <td class="text-primary" style="font-weight: bold;">บาท</td>
                                                        <td colspan="3">&nbsp;</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <input type="hidden" id="TotalRow" name="TotalRow" value="0" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg text-right">
                                        <button type="button" class="btn-prev btn btn-secondary" data-step="2" data-goto="1"><i class="fas fa-chevron-left fa-fw fa-1x"></i> ย้อนกลับ</button>
                                        <button type="button" class="btn-next btn btn-primary" data-step="2" data-goto="3">ต่อไป <i class="fas fa-chevron-right fa-fw fa-1x"></i></button>
                                    </div>
                                </div>
                            </div>
                            <!-- END OF STEP 2 -->
                            <!-- STEP 3 -->
                            <div id="pur-step3" data-step="3">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 3: ตรวจสอบความถูกต้องของข้อมูล</h4>
                                        <small class="text-danger">ตรวจสอบข้อมูลให้ครบถ้วนก่อนกดบันทึก</small>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless order-preview">
                                                <tr>
                                                    <td class="font-weight" width="12.5%">ผู้จัดทำ</td>
                                                    <td width="37.5%"><?php echo $_SESSION['uName']." ".$_SESSION['uLastName']; ?></td>
                                                    <td class="font-weight" width="12.5%">ฝ่าย</td>
                                                    <td width="37.5%"><?php echo SATeamName($_SESSION['DeptCode']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight">วันที่เอกสาร</td>
                                                    <td id="view_DocDate"></td>
                                                    <td class="font-weight">วันที่ต้องการสินค้า</td>
                                                    <td id="view_DocDueDate"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight">ประเภทการสั่งซื้อ</td>
                                                    <td id="view_DocType"></td>
                                                    <td class="font-weight">ประเภทสินค้าที่ต้องการ</td>
                                                    <td id="view_ItemType"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight">ทีมขายที่จองสินค้า</td>
                                                    <td id="view_ItemQuota"></td>
                                                    <td class="font-weight">เอกสารอ้างอิง</td>
                                                    <td class="font-weight text-primary" id="view_RefDocNum"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight">สถานที่จัดส่ง</td>
                                                    <td id="view_ShiptoType"></td>
                                                    <td class="font-weight">คลังที่จัดเก็บ</td>
                                                    <td id="view_ShiptoWhse"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight">เหตุผลในการสั่งซื้อ</td>
                                                    <td colspan="3" id="view_PurchaseReasons"></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered order-preview">
                                                <thead class="text-center table-group-divider">
                                                    <tr>
                                                        <th rowspan="2" width="5%">ลำดับ</th>
                                                        <th rowspan="2" width="10%">รหัสสินค้า</th>
                                                        <th rowspan="2">ชื่อสินค้า</th>
                                                        <th rowspan="2" width="5%">จำนวน</th>
                                                        <th rowspan="2" width="5%">หน่วยซื้อ</th>
                                                        <th colspan="3">ข้อมูลการซื้อ</th>
                                                        <th colspan="2">ข้อมูลการขาย</th>
                                                    </tr>
                                                    <tr>
                                                        <th width="7.5%">ราคาซื้อ<br/>ต่อหน่วย</th>
                                                        <th width="8.25%">ราคาซื้อ<br/>ทั้งหมด</th>
                                                        <th width="5%">สกุลเงิน</th>
                                                        <th width="8.25%">ราคาขาย</br>ต่อตัว</th>
                                                        <th width="5.5%">กำไร<br/>(%)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="view_ItemList"></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3"><b>หมายเหตุ:</b> <span id="view_Comments" class="text-danger"></span></td>
                                                        <td colspan="3" class="text-right font-weight text-primary">ยอดรวมทุกรายการ</td>
                                                        <td id="view_DocTotal" class="text-right font-weight text-primary"></td>
                                                        <td id="view_DocCur" class="font-weight text-primary"></td>
                                                        <td colspan="2" class="table-active">&nbsp;</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg text-right">
                                        <button type="button" class="btn-prev btn btn-secondary" data-step="3" data-goto="2"><i class="fas fa-chevron-left fa-fw fa-1x"></i> ย้อนกลับ</button>
                                        <button type="button" class="btn btn-primary" onclick="SaveDraft(1);" data-step="3" data-goto="4"><i class="fas fa-save fa-fw fa-1x"></i> สร้างใบขอซื้อสินค้าใหม่</button>
                                    </div>
                                </div>
                            </div>
                            <!-- END OF STEP 3 -->
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- MODAL ADD ITEM -->
<div class="modal fade" id="ModalAddItem" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="HeaderModal"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mt-4">
                <div class="col-lg-3">
                    <div class="form-group mb-3">
                        <label for="AddItemCode">รหัสสินค้า</label>
                        <input type="text" class="form-control" name="AddItemCode" id="AddItemCode" list="DataItem" />
                        <!-- DATALIST -->
                        <datalist id="DataItem"></datalist>
                        <input type="hidden" name="RowID" id="RowID" readonly />
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-group mb-3">
                        <label for="AddItemName">ชื่อสินค้า</label>
                        <input type="text" class="form-control" name="AddItemName" id="AddItemName" />
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-3">
                        <label for="AddQuantity">จำนวน</label>
                        <input type="number" class="form-control text-right" name="AddQuantity" id="AddQuantity" step="1" />
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-3">
                        <label for="AddUnit">หน่วยซื้อ</label>
                        <input type="text" class="form-control" name="AddUnit" id="AddUnit" />
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-lg-12">
                    <h5 class="h5">ข้อมูลสกุลเงินที่สั่งซื้อ</h5>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-lg-6">
                    <div class="form-group mb-3">
                        <label for="AddUnitPrice">ราคาซื้อต่อตัว</label>
                        <input type="number" class="form-control text-right" name="AddUnitPrice" id="AddUnitPrice" step="any" />
                        <input type="hidden" name="AddUnitRate" id="AddUnitRate"  readonly/>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group mb-3">
                        <label for="AddDocCurrency">สกุลเงินที่สั่งซื้อ</label>
                        <select class="form-select" name="AddDocCurrency" id="AddDocCurrency" required>
                            <option value="THB" data-exchange="1" selected>THB - บาท</option>
                            <option value="CNY" data-exchange="5.50">CNY - หยวน [1 CNY = 5.50 THB]</option>
                            <option value="USD" data-exchange="35.00">USD - ดอลลาร์สหรัฐ [1 USD = 35.00 THB]</option>
                            <option value="TWD" data-exchange="1.25">TWD - ดอลลาร์ไต้หวัน [1 TWD = 1.25 THB]</option>
                            <option value="EUR" data-exchange="38.00">EUR - ยูโร [1 EUR = 38.00 THB]</option>
                        </select>
                        <small class="text-muted"><b>ที่มา:</b> อัตราแลกเปลี่ยนเฉลี่ยย้อนหลัง 5 ปี (2560-2564) ธนาคารแห่งประเทศไทย</small>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-lg-12">
                    <h5 class="h5">ข้อมูลราคาเงินไทย (โดยประมาณ)</h5>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-lg-5">
                    <div class="form-group mb-3">
                        <label for="AddSalePrice">ราคาซื้อต่อตัว</label>
                        <input type="number" class="form-control text-right" name="AddUnitPriceTHB" id="AddUnitPriceTHB" step="any" readonly />
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-group mb-3">
                        <label for="AddSalePrice">ราคาขายต่อตัว</label>
                        <input type="number" class="form-control text-right" name="AddSalePrice" id="AddSalePrice" step="any" />
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-3">
                        <label for="AddGP">กำไร (%)</label>
                        <input type="number" class="form-control text-right text-success" name="AddGP" id="AddGP" step="any" readonly />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            <button type="button" class="btn btn-primary" id="btn-AddRow"></button>
        </div>
        </div>
    </div>
</div>

<!-- MODAL PULL DATA EXCEL -->
<div class="modal fade" id="ModalImport" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-file-import fa-fw fa-1x"></i> นำเข้า</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="alert alert-light-info color-info alert-dismissable fade show">
                        <i class="fas fa-exclamation-circle fa-fw fa-1x"></i> <strong>การนำเข้ารายการสินค้า</strong>
                        <ul>
                            <li>ระบบจะนำข้อมูล รหัสสินค้า และชื่อสินค้าเดิมมาเท่านั้น</li>
                            <li class="text-danger">การนำเข้าข้อมูล จะทำให้รายการที่เพิ่มไปก่อนหน้าหายทั้งหมด</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- IMPORT TAB -->
            <ul class="nav nav-tabs" id="main-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="javascript:void(0);" class="btn-tabs nav-link active" id="ImportSearch-tab" data-bs-toggle="tab" data-bs-target="#ImportSearch" role="tab" data-tabs="0" aria-controls="ImportSearch" aria-selected="false">
                        <i class="fas fa-search fa-fw fa-1x"></i> นำเข้าจากเลขที่เอกสาร
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="javascript:void(0);" class="btn-tabs nav-link disabled" id="ImportExcel-tab" data-bs-toggle="tab" data-bs-target="#ImportExcel" role="tab" data-tabs="1" aria-controls="ImportExcel" aria-selected="true">
                        <i class="fas fa-file-excel fa-fw fa-1x"></i> นำเข้าจากไฟล์ Excel
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <!-- TAB 0 -->
                <div class="tab-pane fade show active" id="ImportSearch" role="tabpanel" aria-labelledby="ImportSearch-tab">
                    <div class="row mt-4">
                        <div class="col-10">
                            <div class="form-group mb-3">
                                <label for="ImportSearchInput">ค้นหาจากเลขที่เอกสาร<span class="text-danger">*</span></label>
                                <input type="text" name="ImportSearchInput" id="ImportSearchInput" class="form-control" placeholder="LCXXXX-0XXX / IMXXXX-1XXX" />
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group mb-3">
                                <label for="btn-searchdoc">&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="btn-searchdoc" name="btn-searchdoc"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TAB 1 -->
                <div class="tab-pane fade" id="ImportExcel" role="tabpanel" aria-labelledby="ImportExcel-tab"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        </div>
        </div>
    </div>
</div>

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

<!-- MODAL ORDER PREVIEW -->
<div class="modal fade" id="ModalPreview" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full modal-dialog-scrollable">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดใบขอซื้อ</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="h6">ใบขอซื้อเลขที่: <span id="prview_DocNum">IM-YYMM-AXXX</span></h5>
                </div>
            </div>
            <!-- ORDER HEADER -->
            <div class="row">
                <div class="col-12">
                    <table class="table table-borderless table-sm" style="font-size: 12px;">
                        <tr>
                            <td class="font-weight" width="15%">ผู้ขอซื้อ</td>
                            <td width="35%" id="prview_CreateName"></td>
                            <td class="font-weight" width="15%">ฝ่ายที่ขอซื้อ</td>
                            <td width="35%" id="prview_DeptName"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">วันที่ขอสั่งซื้อ</td>
                            <td id="prview_DocDate"></td>
                            <td class="font-weight">วันที่ต้องการสินค้า</td>
                            <td id="prview_DocDueDate" class="font-weight text-danger"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">ประเภทการสั่งซื้อ</td>
                            <td id="prview_DocType"></td>
                            <td class="font-weight">ประเภทสินค้าที่ต้องการ</td>
                            <td id="prview_ProductName"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">สถานที่จัดส่ง</td>
                            <td colspan="3" id="prview_ShiptoType"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">ทีมขายที่จองสินค้า</td>
                            <td id="prview_ItemQuotaTeam"></td>
                            <td class="font-weight">สกุลเงินที่สั่งซื้อ</td>
                            <td id="prview_DocCur"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">เหตุผลในการสั่งซื้อ</td>
                            <td colspan="3" id="prview_DocRemark"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- ORDER TAB -->
            <ul class="nav nav-tabs" id="pr-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#PRItemList" class="btn btn-tabs nav-link active" id="PRItemTab" data-bs-toggle="tab" data-bs-target="#PRItemList" role="tab" data-tabs="0" aria-controls="PRItemList" aria-selected="false" style="font-size: 12px;">
                        <i class="fas fa-list-ol fa-fw fa-1x"></i> รายการสินค้า
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#PRAttachList" class="btn btn-tabs nav-link" id="PRAttachTab" data-bs-toggle="tab" data-bs-target="#PRAttachList" role="tab" data-tabs="1" aria-controls="PRAttachList" aria-selected="true" style="font-size: 12px;">
                        <i class="fas fa-paperclip fa-fw fa-1x"></i> เอกสารแนบ
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#PRApproveList" class="btn btn-tabs nav-link disabled" id="PRApproveTab" data-bs-toggle="tab" data-bs-target="#PRApproveList" role="tab" data-tabs="2" aria-controls="PRApproveList" aria-selected="true" style="font-size: 12px;">
                        <i class="fas fa-tasks fa-fw fa-1x"></i> สถานะการอนุมัติ
                    </a>
                </li>
            </ul>
            
            <!-- CONTENT TAB -->
            <div class="tab-content">
                <div class="tab-pane show active" id="PRItemList" role="tabpanel" aria-labelledby="PRItemTab" style="font-size: 12px;">
                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th width="5%">ลำดับ</th>
                                        <th width="10%">รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th colspan="2">จำนวน</th>
                                        <th width="12.5%">ราคาต่อหน่วย<br/>(<span class="prview_currency"></span>)</th>
                                        <th width="12.5%">ราคารวม<br/>(<span class="prview_currency"></span>)</th>
                                        <th width="12.5%">ราคาขายต่อหน่วย<br/>(THB)</th>
                                        <th width="10%">GP<br/>(%)</th>
                                    </tr>
                                </thead>
                                <tbody id="prview_ItemList"></td>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="PRAttachList" role="tabpanel" aria-labelledby="PRAttachTab">
                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-bordered" style="font-size: 12px;">
                                <thead class="text-center">
                                    <tr>
                                        <th width="5%">ลำดับ</th>
                                        <th>ชื่อเอกสารแนบ</th>
                                        <th width="15%">วันที่อัพโหลด</th>
                                        <th width="7.5%"><i class="fas fa-file-download fa-fw fa-lg"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="prview_attachlist"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="PRApproveList" role="tabpanel" aria-labelledby="PRApproveTab">
                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-bordered" style="font-size: 12px;">
                                <thead class="text-center">
                                    <tr>
                                        <th rowspan="2" width="5%">ลำดับ</th>
                                        <th rowspan="2" width="15%">ผู้อนุมัติ</th>
                                        <th colspan="2">เงื่อนไขการอนุมัติ</th>
                                        <th rowspan="2" width="10%">ผลการ<br/>พิจารณา</th>
                                        <th rowspan="2">หมายเหตุ</th>
                                        <th width="15%" rowspan="2">วันที่อนุมัติ</th>
                                    </tr>
                                    <tr>
                                        <th width="7.5%">เกินวงเงิน</th>
                                        <th width="7.5%">ราคาพิเศษ</th>
                                    </tr>
                                </thead>
                                <tbody id="prview_approvelist"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" id="preview_footer"></div>
        </div>
    </div>
</div>

<!-- MODAL CANCEL ALERT -->
<div class="modal fade" id="confirm_cancel" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-question-circle fa-fw fa-lg"></i> ยืนยันการยกเลิก</h5>
                <p id="confirm_body" class="my-4">คุณต้องการยกเลิกใบขอซื้อนี้หรือไม่?</p>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-dismiss" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-cancel-confirm" data-docentry="0" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

function number_format(number,decimal) {
     var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
     var formatter = new Intl.NumberFormat("en",options);
     return formatter.format(number)
}

function strtoNumber(number) {
    return parseFloat(number.replace(/,/g,""));
}

function CallHead(){
    $(".overlay").show();
    var MenuCase = $('#HeadeMenuLink').val()
    $.ajax({
        url: "menus/human/ajax/ajaxemplist.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
        type: "POST",
        data : {MenuCase : MenuCase },
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

function GETList(filt_year,filt_month,filt_team) {
    $(".overlay").show();
    // var filt_year = $("#filt_year").val();
    // var filt_month = $("#filt_month").val();
    $.ajax({
        url: "menus/general/ajax/ajaxpurreqlist.php?p=GetPurReqList",
        type: "POST",
        data: { filt_year: filt_year, filt_month: filt_month, filt_team: filt_team },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#PurReqTable").html(inval['output']);
            });
        }
    });
    $(".overlay").hide();
}


function GetItemList() {
    $.ajax({
        url: "menus/general/ajax/ajaxpurreq.php?p=GetItemList",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#DataItem").html(inval["output"]);
            });
        }
    })
}

function CheckForm(StepNow,StepTo) {
    var Now = StepNow;
    var To  = StepTo;
    var ErrorPoint = 0;
    var ErrorID    = [];
    var SuccessID  = [];
    var CheckID    = [];

    switch(Now) {
        case "1": CheckID = ["DocDate","DocDueDate","DocType","ItemType","ShiptoType","PurchaseReasons"]; break;
        default: CheckID = []; break;
    }
    
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

    if(ErrorPoint > 0) {
        for(let i = 0; i < ErrorID.length; i++) { $("#"+ErrorID[i]).removeClass("is-valid is-invalid").addClass("is-invalid"); }
        for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-valid is-invalid").addClass("is-valid"); }
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        switch(Now) {
            case "1":
                for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
                $("#pur-step"+Now).hide();
                $("#pur-step"+To).show();
            break;
            case "2":
                var TotalTR = $("#ItemListData tr").length;
                if(TotalTR == 0) {
                    if(To == "1") {
                        $("#pur-step"+Now).hide();
                        $("#pur-step"+To).show();
                    } else {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("กรุณาเพิ่มรายการสินค้าอย่างน้อย 1 รายการ");
                        $("#alert_modal").modal('show');
                    }
                } else {
                    PurPreview();
                    $("#pur-step"+Now).hide();
                    $("#pur-step"+To).show();
                }
            break;
            default:
                $("#pur-step"+Now).hide();
                $("#pur-step"+To).show();
            break;
        }
    }
}

function AddNewRow() {
    console.log("function working...")
    // 1. Get RowID;
    var EditRow = $("#RowID").val();

    // 2. Input Value into Variable
    var ItemCode  = $("#AddItemCode").val();
    var ItemName  = $("#AddItemName").val();
    var Quantity  = $("#AddQuantity").val();
    var UnitMsr   = $("#AddUnit").val();
    var DocCurrency = $("#AddDocCurrency").val();
    if($("#AddUnitPrice").val() == null || $("#AddUnitPrice").val().length == 0) {
        var UnitPrice = 0;
        var UnitRate  = 0;
    } else {
        var UnitPrice = parseFloat($("#AddUnitPrice").val());
        var UnitRate  = parseFloat($("#AddUnitRate").val());
    }
    if($("#AddUnitPriceTHB").val() == null || $("#AddUnitPriceTHB").val().length == 0) {
        var UnitPriceTHB = 0;
    } else {
        var UnitPriceTHB = parseFloat($("#AddUnitPriceTHB").val());
    }
    if($("#AddSalePrice").val() == null || $("#AddSalePrice").val().length == 0) {
        var SalePrice = 0
    } else {
        var SalePrice = parseFloat($("#AddSalePrice").val());
    }
    if(SalePrice != 0) {
        var GrossPrft = ((SalePrice-UnitPriceTHB)/SalePrice)*100;
    } else {
        var GrossPrft = 0;
    }
    var LineTotal    = UnitPrice*Quantity;
    var LineTotalTHB = UnitPriceTHB*Quantity;
    
    // 3. Check Form Required
    if(ItemName == null || (Quantity.length == 0 || Quantity < 1)) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        // 4. Check Add Type [0 = AddNewRow // Other = Update Exist Row]
        if(EditRow == "0") {
            var LastRow = parseInt($("#TotalRow").val());
            var RowID   = LastRow+1;

            // Render to html template
            var NewRow =    "<tr data-rowid='"+RowID+"'>"+
                                "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemCode_"+RowID+"' id='ItemCode_"+RowID+"' value='"+ItemCode+"' readonly /></td>"+
                                "<td class='text-center'><input type='text' class='form-control-plaintext' name='ItemName_"+RowID+"' id='ItemName_"+RowID+"' value='"+ItemName+"' readonly /></td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='Quantity_"+RowID+"' id='Quantity_"+RowID+"' value='"+number_format(Quantity,0)+"' readonly></td>"+
                                "<td class='text-center'><input type='text' class='form-control-plaintext' name='Unit_"+RowID+"' id='Unit_"+RowID+"' value='"+UnitMsr+"' readonly></td>"+
                                "<td class='text-right'>"+
                                    "<input type='text' class='form-control-plaintext text-right' name='UnitPrice_"+RowID+"' id='UnitPrice_"+RowID+"' value='"+number_format(UnitPrice,3)+"' readonly>"+
                                    "<input type='hidden' name='UnitRate_"+RowID+"' id='UnitRate_"+RowID+"' value='"+UnitRate.toFixed(2)+"' readonly />"+
                                "</td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right font-weight' name='LineTotal_"+RowID+"' id='LineTotal_"+RowID+"' value='"+number_format(LineTotal,2)+"' readonly></td>"+
                                "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='Currency_"+RowID+"' id='Currency_"+RowID+"' value='"+DocCurrency+"' readonly></td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='UnitPriceTHB_"+RowID+"' id='UnitPriceTHB_"+RowID+"' value='"+number_format(UnitPriceTHB,3)+"' readonly></td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right font-weight' name='LineTotalTHB_"+RowID+"' id='LineTotalTHB_"+RowID+"' value='"+number_format(LineTotalTHB,2)+"' readonly></td>"+
                                "<td class='text-center'>THB</td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='SalePrice_"+RowID+"' id='SalePrice_"+RowID+"' value='"+number_format(SalePrice,3)+"' readonly></td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right text-success' name='GrossPrft_"+RowID+"' id='GrossPrft_"+RowID+"' value='"+number_format(GrossPrft,2)+"' readonly></td>"+
                                "<td class='text-center'>"+
                                    "<button type='button' class='btn btn-secondary btn-sm' onclick='EditItem("+RowID+")'><i class='fas fa-edit fa-fw fa-1x'></i></button> "+
                                    "<button type='button' class='btn btn-danger btn-sm' onclick='DeleteItem("+RowID+")'><i class='fas fa-trash fa-fw fa-1x'></i></button>"+
                                "</td>"+
                            "</tr>";
            $("#TotalRow").val(RowID);
            $("#ItemListData").append(NewRow);
        } else {
            $("#ItemCode_"+EditRow).val(ItemCode);
            $("#ItemName_"+EditRow).val(ItemName);
            $("#Quantity_"+EditRow).val(number_format(Quantity,0));
            $("#Unit_"+EditRow).val(UnitMsr);
            $("#UnitPrice_"+EditRow).val(number_format(UnitPrice,3));
            $("#UnitRate_"+EditRow).val(UnitRate.toFixed(2));
            $("#Currency_"+EditRow).val(DocCurrency);
            $("#LineTotal_"+EditRow).val(number_format(LineTotal,2));
            $("#UnitPriceTHB_"+EditRow).val(number_format(UnitPriceTHB,3));
            $("#LineTotalTHB_"+EditRow).val(number_format(LineTotalTHB,2));
            $("#SalePrice_"+EditRow).val(number_format(SalePrice,3));
            $("#GrossPrft_"+EditRow).val(number_format(GrossPrft,2));
        }
        $("#ModalAddItem").modal("hide");
    }
    GetDocTotal();
}

function EditItem(row) {
    var ItemCode     = $("#ItemCode_"+row).val();
    var ItemName     = $("#ItemName_"+row).val();
    var Quantity     = strtoNumber($("#Quantity_"+row).val());
    var UnitMsr      = $("#Unit_"+row).val();
    var UnitPrice    = strtoNumber($("#UnitPrice_"+row).val());
    var DocCurrency  = $("#Currency_"+row).val();
    var LineTotal    = strtoNumber($("#LineTotal_"+row).val());
    var UnitPriceTHB = strtoNumber($("#UnitPriceTHB_"+row).val());
    var LineTotalTHB = strtoNumber($("#LineTotalTHB_"+row).val());
    var SalePrice    = strtoNumber($("#SalePrice_"+row).val());
    var GrossPrft    = strtoNumber($("#GrossPrft_"+row).val());

    $("#RowID").val(row);

    $("#AddItemCode").val(ItemCode);
    $("#AddItemName").val(ItemName);
    $("#AddQuantity").val(Quantity);
    $("#AddUnit").val(UnitMsr);
    $("#AddUnitPrice").val(UnitPrice);
    $("#AddDocCurrency").val(DocCurrency).change();
    $("#AddUnitPriceTHB").val(UnitPriceTHB);
    $("#AddSalePrice").val(SalePrice);
    $("#AddGP").val(GrossPrft);

    // แสดง modal
    var HeadEdit = "<i class='far fa-edit fa-fw fa-1x'></i> แก้ไขข้อมูลรายการสินค้า";
    var btnSave = "<i class='far fa-save fa-fw fa-1x'></i> บันทึก";
    $("#HeaderModal").html(HeadEdit);
    $("#btn-AddRow").html(btnSave);
    $("#ModalAddItem").modal("show");
}

function DeleteItem(row) {
    $("#confirm_delete").modal("show");
    $("#btn-del-confirm").attr("data-rowid",row);

    $("#btn-del-confirm").on("click", function(e){
        var RowID = $(this).attr("data-rowid");
        $("#ItemListData tr[data-rowid='"+RowID+"']").remove();
        GetDocTotal();
    });
}

function SearchDoc(DocNum) {
    $.ajax({
        url: "menus/general/ajax/ajaxpurreq.php?p=SearchDoc",
        type: "POST",
        data: { kwd: DocNum },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval){
                if(inval['Rows'] == 0) {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("ไม่พบรายการสินค้าในเอกสารที่คุณค้นหา");
                    $("#alert_modal").modal('show');
                } else {
                    $("#ItemListData").empty();
                    var RowID = 0;
                    var NewRow = '';

                    for(i=0;i<=inval['Rows']-1;i++) {
                        RowID++;
                        var UnitPrice = parseFloat(inval[i]['UnitPrice']);
                        var UnitPriceTHB = parseFloat(inval[i]['UnitPriceTHB']);
                        var UnitRate = parseFloat(inval[i]['UnitRate']);
                        var LineTotal = parseFloat(inval[i]['LineTotal']);
                        var LineTotalTHB = parseFloat(inval[i]['LineTotalTHB']);
                        var SalePrice = parseFloat(inval[i]['SalePriceTHB']);
                        var GrossPrft = parseFloat(inval[i]['GrossPrft']);
                        NewRow +=    "<tr data-rowid='"+RowID+"'>"+
                                "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemCode_"+RowID+"' id='ItemCode_"+RowID+"' value='"+inval[i]['ItemCode']+"' readonly /></td>"+
                                "<td class='text-center'><input type='text' class='form-control-plaintext' name='ItemName_"+RowID+"' id='ItemName_"+RowID+"' value='"+inval[i]['ItemName']+"' readonly /></td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='Quantity_"+RowID+"' id='Quantity_"+RowID+"' value='"+number_format(inval[i]['Qty'],0)+"' readonly></td>"+
                                "<td class='text-center'><input type='text' class='form-control-plaintext' name='Unit_"+RowID+"' id='Unit_"+RowID+"' value='"+inval[i]['UnitMsr']+"' readonly></td>"+
                                "<td class='text-right'>"+
                                    "<input type='text' class='form-control-plaintext text-right' name='UnitPrice_"+RowID+"' id='UnitPrice_"+RowID+"' value='"+number_format(UnitPrice,3)+"' readonly>"+
                                    "<input type='hidden' name='UnitRate_"+RowID+"' id='UnitRate_"+RowID+"' value='"+UnitRate.toFixed(2)+"' readonly />"+
                                "</td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right font-weight' name='LineTotal_"+RowID+"' id='LineTotal_"+RowID+"' value='"+number_format(LineTotal,2)+"' readonly></td>"+
                                "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='Currency_"+RowID+"' id='Currency_"+RowID+"' value='"+inval[i]['UnitCur']+"' readonly></td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='UnitPriceTHB_"+RowID+"' id='UnitPriceTHB_"+RowID+"' value='"+number_format(UnitPriceTHB,3)+"' readonly></td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right font-weight' name='LineTotalTHB_"+RowID+"' id='LineTotalTHB_"+RowID+"' value='"+number_format(LineTotalTHB,2)+"' readonly></td>"+
                                "<td class='text-center'>THB</td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='SalePrice_"+RowID+"' id='SalePrice_"+RowID+"' value='"+number_format(SalePrice,3)+"' readonly></td>"+
                                "<td class='text-right'><input type='text' class='form-control-plaintext text-right text-success' name='GrossPrft_"+RowID+"' id='GrossPrft_"+RowID+"' value='"+number_format(GrossPrft,2)+"' readonly></td>"+
                                "<td class='text-center'>"+
                                    "<button type='button' class='btn btn-secondary btn-sm' onclick='EditItem("+RowID+")'><i class='fas fa-edit fa-fw fa-1x'></i></button> "+
                                    "<button type='button' class='btn btn-danger btn-sm' onclick='DeleteItem("+RowID+")'><i class='fas fa-trash fa-fw fa-1x'></i></button>"+
                                "</td>"+
                            "</tr>";
                        
                    }
                    console.log(NewRow);
                    $("#ItemListData").append(NewRow);
                    $("#TotalRow").val(inval['Rows']);
                    GetDocTotal(); 
                }
            });
        }
    });
}

function GetDocTotal() {
    var TotalRow = $("#TotalRow").val();
    var DocCur   = $("#AddDocCurrency").val();
    var DocTotal    = 0.000;
    var DocTotalTHB = 0.000;

    for(var i = 1; i <= TotalRow; i++) {
        if($("#ItemCode_"+[i]).val() != undefined || $("#ItemName_"+[i]).val() != undefined)  {
            var LineTotal    = strtoNumber($("#LineTotal_"+[i]).val());
            var LineTotalTHB = strtoNumber($("#LineTotalTHB_"+[i]).val());
            if(isNaN(LineTotal) == false && isNaN(LineTotalTHB) == false) {
                DocTotal    = parseFloat(DocTotal)+parseFloat(LineTotal);
                DocTotalTHB = parseFloat(DocTotalTHB)+parseFloat(LineTotalTHB);
            } else {
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("ไม่สามารถประมวลผลข้อมูลได้ กรุณาติดต่อผู้ดูแลระบบ");
                $("#alert_modal").modal('show');
            }
        }
    }
    $("#DocCurrency").html(DocCur);
    $("#DocTotal").val(number_format(DocTotal,2));
    $("#DocTotalTHB").val(number_format(DocTotalTHB,2));
}

function PurPreview() {
    $("#view_ItemList").empty();
    var [Y_DocDate, M_DocDate, D_DocDate] = $("#DocDate").val().split('-');
    var view_DocDate         = ""+D_DocDate+"/"+M_DocDate+"/"+Y_DocDate+"";
    var [Y_DocDueDate, M_DocDueDate, D_DocDueDate] = $("#DocDueDate").val().split('-');
    var view_DocDueDate      = ""+D_DocDueDate+"/"+M_DocDueDate+"/"+Y_DocDueDate+"";
    var view_DocType         = $("#DocType option:selected").text();
    var view_ItemType        = $("#ItemType option:selected").text();

    var ItemQuota            = $("#ItemQuota").val();
    var LoopTeam             = ItemQuota.length;
    var view_ItemQuota       = "";
    if(LoopTeam > 0) {
        for(var i = 0; i <= LoopTeam-1; i++) {
            view_ItemQuota += ItemQuota[i];
            if(i != LoopTeam-1) {
                view_ItemQuota += ", ";
            }
        }
    } else {
        view_ItemQuota = "<i class='text-muted'>ไม่ระบุ</span>";
    }

    var view_RefDocNum       = $("#RefDocNum").val();
    var ShiptoVal            = $("#ShiptoType").val();
    var ShiptoType           = $("#ShiptoType option:selected").text();
    var ShiptoAddress        = $("#ShiptoAddress").val();
    var view_ShiptoWhse      = $("#ShiptoWhse option:selected").text();
    var view_PurchaseReasons = $("#PurchaseReasons").val();
    var view_Comments        = $("#Comments").val();

    if(ShiptoVal == "OTR") {
        var view_ShiptoType = ShiptoAddress;
    } else {
        var view_ShiptoType = ShiptoType;
    }

    $("#view_DocDate").html(view_DocDate);
    $("#view_DocDueDate").html(view_DocDueDate);
    $("#view_DocType").html(view_DocType);
    $("#view_ItemType").html(view_ItemType);
    $("#view_ItemQuota").html(view_ItemQuota);
    $("#view_RefDocNum").html(view_RefDocNum);
    $("#view_ShiptoType").html(view_ShiptoType);
    $("#view_ShiptoWhse").html(view_ShiptoWhse);
    $("#view_PurchaseReasons").html(view_PurchaseReasons);
    $("#view_Comments").html(view_Comments);

    var TotalRow = $("#TotalRow").val();
    var No = 1;
    for(var i = 1; i <= TotalRow; i++) {
        var ItemCode_        = $("#ItemCode_"+[i]).val();
        var ItemName_        = $("#ItemName_"+[i]).val();
        var Quantity_        = $("#Quantity_"+[i]).val();
        var Unit_            = $("#Unit_"+[i]).val();
        var UnitPrice_       = $("#UnitPrice_"+[i]).val();
        var LineTotal_       = $("#LineTotal_"+[i]).val();
        var Currency_        = $("#Currency_"+[i]).val();
        var SalePrice_       = $("#SalePrice_"+[i]).val();
        var GrossPrft_       = $("#GrossPrft_"+[i]).val();

        if(ItemCode_ != undefined || ItemName_ != undefined) {
            var ItemRow =   "<tr>"+
                                "<td class='text-center'>"+No+"</td>"+
                                "<td class='text-center'>"+ItemCode_+"</td>"+
                                "<td>"+ItemName_+"</td>"+
                                "<td class='text-right'>"+Quantity_+"</td>"+
                                "<td>"+Unit_+"</td>"+
                                "<td class='text-right'>"+UnitPrice_+"</td>"+
                                "<td class='text-right'>"+LineTotal_+"</td>"+
                                "<td class='text-center'>"+Currency_+"</td>"+
                                "<td class='text-right'>"+SalePrice_+"</td>"+
                                "<td class='text-right'>"+GrossPrft_+"</td>"+
                            "</tr>";
            $("#view_ItemList").append(ItemRow);
            No++;
            var view_DocCur = Currency_;
        }
    }

    var view_DocTotal = $("#DocTotal").val();
    $("#view_DocTotal").html(view_DocTotal);
    $("#view_DocCur").html(view_DocCur);

}

function SaveDraft(SaveType) {
    /* Checked Order DocEntry */

    var PurchaseForm = new FormData($("#PurchaseForm")[0]);
    PurchaseForm.append('SaveType',SaveType);

    $.ajax({
        url: "menus/general/ajax/ajaxpurreq.php?p=SavePurReq",
        type: 'POST',
        dataType: 'text',
        cache: false,
        processData: false,
        contentType: false,
        data: PurchaseForm,
        success: function() {
            $("#confirm_saved").modal('show');
            $("#btn-save-reload").on("click", function(e){
                e.preventDefault();
                window.location.reload();
            });
        }
    });
}

function PreviewPR(DocEntry,int_status) {
    $("#SOApproveTab").removeClass("disabled");
    $(".nav-tabs a[href='#PRItemList']").tab("show");
    $.ajax({
        url: 'menus/general/ajax/ajaxpurreqlist.php?p=PRPreview',
        type: 'POST',
        data: { DocEntry: DocEntry, int_status: int_status },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                /* HEADER */
                $("#prview_DocNum").html(inval['view_DocNum']);
                $("#prview_CreateName").html(inval['view_CreateName']);
                $("#prview_DeptName").html(inval['view_DeptName']);
                $("#prview_DocDate").html(inval['view_DocDate']);
                $("#prview_DocDueDate").html(inval['view_DocDueDate']);
                $("#prview_DocType").html(inval['view_DocType']);
                $("#prview_ProductName").html(inval['view_TypeName']);
                $("#prview_ShiptoType").html(inval['view_ShiptoType']);
                $("#prview_ItemQuotaTeam").html(inval['view_ItemQuota']);
                $("#prview_DocCur").html(inval['view_DocCur']);
                $("#prview_DocRemark").html(inval['view_DocRemark']);
                $(".prview_currency").html(inval['view_DocCurSign']);

                /* ITEM LIST */
                $("#prview_ItemList").html(inval['view_ItemList']);
                $("#prview_attachlist").html(inval['view_attachlist']);
                $("#preview_footer").html(inval['footer']);
            });
        }
    });
    $("#ModalPreview").modal("show");
}

function PrintPR(docentry,intstatus) {
    var DocEntry = docentry;
    var DocType  = intstatus;
    window.open('menus/general/print/printpr.php?docety='+DocEntry,'_blank');
}

function CancelPR(DocEntry) {
    var DocEntry = DocEntry;
    console.log(DocEntry);
    $("#ModalPreview").modal("hide");
    $("#confirm_cancel").modal("show");

    $("#btn-cancel-confirm").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            url: 'menus/general/ajax/ajaxpurreqlist.php?p=CancelPR',
            type: 'POST',
            data: { DocEntry: DocEntry },
            success: function(result) {
                $("#confirm_saved").modal("show");
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        })
    });

    $("#btn-cancel-dismiss").on("click", function(e) {
        e.preventDefault();
        location.reload();
    });
}


/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
$(document).ready(function(){
    CallHead();
    GetItemList();
    var filt_year = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team = $("#filt_team").val();
    GETList(filt_year,filt_month,filt_team);

    $("#pur-step2, #pur-step3").hide();

    var tooltipTriggerList = [].slice.call($('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    var FirstHeight = $("tr.first").height();
    $(".tableFixHead thead tr.second th").css("top",FirstHeight+1);
});

/* เมื่อเลือก ปี เดือน หรือ ทีม */
$("#filt_year, #filt_month, #filt_team").on("change", function(){
    var filt_year = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team = $("#filt_team").val();
    GETList(filt_year,filt_month,filt_team)
});

/* เมื่อกรอกข้อความสำหรับค้นหา */
$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#PurReqTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

$("#DocType").on("change",function(){
    var DocType = $(this).val();
    if(DocType == "LC") {
        var DocCur = "THB";
    } else {
        var DocCur = "USD";
    }
    $("#AddDocCurrency").val(DocCur).change();
});

/* เมื่อ USER เลือกประเภทสินค้า */
$("#ItemType").on("change",function(){
    $("#ItemQuota").selectpicker('destroy');
    var ItemType = $(this).val();
    switch(ItemType) {
        case "A01":
        case "A02":
        case "A03":
        case "A04":
            $("#ShiptoType, #ShiptoWhse, #ItemQuota, #RemarkPackage").attr("disabled",false);
        break;
        default:
            $("#ShiptoType, #ShiptoWhse, #ItemQuota, #RemarkPackage").attr("disabled",true);
            $("#ShiptoType").attr("disabled",false);
        break;
    }
    $("#ItemQuota").selectpicker();
});

/* เมื่อ USER เลือกสถานที่จัดส่ง */
$("#ShiptoType").on("change",function(){
    
    var ShiptoType = $(this).val();
    var ItemType   = $("#ItemType").val();
    switch(ShiptoType) {
        case "KBI":
            $("#ShiptoWhse optgroup[data-locate='KSY'] option, #ShiptoWhse optgroup[data-locate='SUP'] option, #ShiptoAddress").attr("disabled", true);
            $("#ShiptoWhse optgroup[data-locate='KBI'] option").attr("disabled", false);
            $("#ShiptoAddress").val("");
            switch(ItemType) {
                case "A01": 
                case "A03": var DefaultWhse = "KB1"; break;
                case "A02": var DefaultWhse = "KB1.1"; break;
                case "A04": var DefaultWhse = "PM-KBI"; break;
                default: var DefaultWhse = "NULL"; break;
            }
        break;
        case "KSY":
            $("#ShiptoWhse optgroup[data-locate='KBI'] option, #ShiptoWhse optgroup[data-locate='SUP'] option, #ShiptoAddress").attr("disabled", true);
            $("#ShiptoWhse optgroup[data-locate='KSY'] option").attr("disabled", false);
            $("#ShiptoAddress").val("");
            switch(ItemType) {
                case "A01": 
                case "A03": var DefaultWhse = "KSM"; break;
                case "A02": var DefaultWhse = "KB4"; break;
                case "A04": var DefaultWhse = "PM-KSY"; break;
                default: var DefaultWhse = "NULL"; break;
            }
        break;
        case "OTR":
            $("#ShiptoWhse optgroup[data-locate='KBI'] option, #ShiptoWhse optgroup[data-locate='KSY'] option").attr("disabled", true);
            $("#ShiptoWhse optgroup[data-locate='SUP'] option, #ShiptoAddress").attr("disabled", false);
            $("#ShiptoAddress").val("");
            switch(ItemType) {
                case "A01":
                case "A02":
                case "A03": var DefaultWhse = "PLA"; break;
                case "A04": var DefaultWhse = "NULL"; break;
                default: var DefaultWhse = "NULL"; break;
            }
            $("#ShiptoAddress").focus();
        break;
        case "NULL":
            $("#ShiptoWhse optgroup[data-locate='KBI'] option, #ShiptoWhse optgroup[data-locate='KSY'] option, #ShiptoWhse optgroup[data-locate='SUP'] option, #ShiptoAddress").attr("disabled", true);
            $("#ShiptoAddress").val("");
            var DefaultWhse = "NULL";
        break;
    }
    $("#ShiptoWhse").val(DefaultWhse).change();
});

/* เมื่อ USER เลือกหมายเหตุ Package */
$("#RemarkPackage").on("change",function(){
    var RemarkPackage = $(this).val();
    switch(RemarkPackage) {
        case "1": $("#PackageFilePath").val("").attr("disabled",true); break;
        default: $("#PackageFilePath").attr("disabled",false).focus(); break;
    }
});

/* เมื่อกดปุ่ม ย้อนกลับ / ต่อไป */
$(".btn-prev, .btn-next").on("click", function(e) {
    e.preventDefault();
    var StepNow  = $(this).attr("data-step");
    var StepGoto = $(this).attr("data-goto");
    CheckForm(StepNow,StepGoto);
});

$("#AddItem").on("click",function(e){
    e.preventDefault();
    $("#AddItemCode, #AddItemName, #AddQuantity, #AddUnit, #AddUnitPrice, #AddUnitPriceTHB, #AddSalePrice, #AddGP").val("")
    $("#RowID").val(0);
    var HeaderAddItem = "<i class='fas fa-plus fa-fw fa-1x'></i> เพิ่มรายการใหม่";
    $("#HeaderModal").html(HeaderAddItem);
    var btnSave = "<i class='fas fa-plus fa-fw fa-1x'></i> เพิ่ม";
    $("#btn-AddRow").html(btnSave);
    $("#ModalAddItem").modal("show");
});

$("#AddItemCode").focusout(function() {
    var ItemGet    = $(this).val().split(" | ");
    var SKUPattern = /^[A-Za-z0-9]{2}-[0-9]{3}-[0-9]{3}$/;
    var Result     = SKUPattern.test(ItemGet[0]);
    if(ItemGet.length == 2) {
        $("#AddItemCode").val(ItemGet[0]);
        $("#AddItemName").val(ItemGet[1]);
        if(Result == true) {
            $.ajax({
                url: "menus/general/ajax/ajaxpurreq.php?p=GetItemDetail",
                type: "POST",
                data: { ItemCode: ItemGet[0] },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#AddUnit").val(inval["output"]);
                        $("#AddSalePrice").val(parseFloat(inval["SalePriceTHB"]).toFixed(2));
                    });
                }
            });
        }
        $("#AddQuantity").focus();
    } else {
        if(Result == false) {
            $("#AddItemName").val("");
        }
        $("#AddItemName").focus();
    }
    
});


$("#AddUnitPrice, #AddDocCurrency").focusout(function() {
    var UnitPrice    = parseFloat($("#AddUnitPrice").val());
    var Currency     = $("#AddDocCurrency").val();
    var ExchangeRate = parseFloat($("#AddDocCurrency option:selected").attr("data-exchange"));
    var UnitPriceTHB = UnitPrice*ExchangeRate;
    $("#AddUnitPriceTHB").val(UnitPriceTHB.toFixed(3));
    $("#AddUnitRate").val(ExchangeRate.toFixed(2));
});

$("#AddSalePrice, #AddUnitPrice").focusout(function() {
    var UnitPriceTHB = parseFloat($("#AddUnitPriceTHB").val());
    var SalePrice    = parseFloat($("#AddSalePrice").val());
    if(SalePrice != 0 || SalePrice.length != 0) {
        var GrossPrft    = ((SalePrice-UnitPriceTHB)/SalePrice)*100;
        $("#AddGP").val(GrossPrft.toFixed(3));
    }
});

$("#btn-AddRow").on("click", function(e) {
    e.preventDefault();
    AddNewRow();
});

$("#ImportItem").on("click",function(e){
    e.preventDefault();
    $("#ModalImport").modal("show");
});

/* 2.1 นำเข้าด้วยวิธีการค้นหาเลขที่เอกสาร */
$("#btn-searchdoc").on("click", function(e){
    e.preventDefault();
    var SearchBox = $("#ImportSearchInput").val();
    if(SearchBox.length == 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกเลขที่เอกสารก่อนกดค้นหา");
        $("#alert_modal").modal('show');
    } else {
        if(SearchBox.includes("-") == true) {
            SearchDoc(SearchBox);
            $("#ModalImport").modal("hide");
            $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-success\"></i> สำเร็จ!");
            $("#alert_body").html("นำเข้าข้อมูลสำเร็จ!");
            $("#alert_modal").modal('show');
            
        } else {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณากรอกรูปแบบเอกสารให้ถูกต้องก่อนค้นหา<br/>(ตัวอย่าง: SOV-YYMM0XXXX)");
            $("#alert_modal").modal('show');
        }
    }
});

</script> 
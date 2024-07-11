<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<style type="text/css">
    .btn-DocType .btn {
        font-weight: normal;
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
                        <a href="#DocList" class="btn-tabs nav-link active" id="DocList-tab" data-bs-toggle="tab" data-tabs="0" aria-controls="DocList" aria-selected="false">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการเอกสาร SA-04
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#NewDoc" class="btn-tabs nav-link" id="NewDoc-tab" data-bs-toggle="tab" data-tabs="1" aria-controls="NewDoc" aria-selected="true">
                            <i class="fas fa-plus fa-fw fa-1x"></i> สร้างเอกสารใหม่
                        </a>
                    </li>
                </ul>
                <!-- END CONTENT TAB -->
                <div class="tab-content">
                    <!-- TAB 1 -->
                    <div class="tab-pane fade show active" id="DocList" role="tabpanel" aria-labelledby="DocList-tab">
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
                                        if(($DeptCode == "DP001" || $DeptCode == "DP002" || $DeptCode == "DP009")) {
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
                                        $DeptSQL = "SELECT T0.DeptCode, T0.DeptName FROM departments T0 WHERE T0.DeptCode IN ('DP003','DP005','DP006','DP007','DP008') ORDER BY T0.DeptCode ASC";
                                        $DeptQRY = MySQLSelectX($DeptSQL);
                                        while($DeptRST = mysqli_fetch_array($DeptQRY)) {
                                            if(($DeptCode != "DP001" && $DeptCode != "DP002" && $DeptCode != "DP009") && ($DeptCode != $DeptRST['DeptCode'])) {
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
                            <table class="table table-sm table-hover table-bordered" style="font-size: 12px;">
                                <thead class="text-center">
                                    <tr>
                                        <th width="3.5%">ลำดับ</th>
                                        <th width="7.5%">วันที่เอกสาร</th>
                                        <th width="10%">เลขที่เอกสาร</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th width="15%">พนักงานขาย</th>
                                        <th width="10%">ผู้จัดทำ</th>
                                        <th width="7.5%">เอกสารอ้างอิง</th>
                                        <th width="7.5%">ยอดลดหนี้ /<br/>ลดจ่ายสุทธิ</th>
                                        <th width="7.5%">สถานะเอกสาร</th>
                                        <th width="3.5%"><i class="fas fa-cog fa-fw fa-1x"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="DocListTable"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- TAB 2 -->
                    <div class="tab-pane fade show" id="NewDoc" role="tabpanel" aria-labelledby="NewDoc-tab">
                        <form class="form" id="SendDocForm" enctype="multipart/form-data">
                            <!-- STEP 1 -->
                            <div id="newdoc-step1" class="need-validation" data-step="1">
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4 class="h4">Step 1: เลือกข้อมูลลูกค้า และเงื่อนไขการลดหนี้/ลดจ่าย</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-10 col-6">
                                        <div class="form-group mb-3">
                                            <label for="CardCode">ชื่อลูกค้า<span class="text-danger">*</span></label>
                                            <select class="form-control" name="CardCode" id="CardCode" data-live-search="true" aria-placeholder="กรุณาเลือกลูกค้า" required>
                                                <option value="" selected disabled>กรุณาเลือกลูกค้า</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-6">
                                        <div class="form-group mb-3">
                                            <label for="DocDate">วันที่เอกสาร<span class="text-danger">*</span></label>
                                            <input type="Date" class="form-control" name="DocDate" id="DocDate" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d"); ?>" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="DocRemark">เหตุผลในการขอลดหนี้ / ลดจ่าย<span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="radio" class="form-check-input" name="DocRemark" id="DocRemark_1" value="1" />
                                            <label class="form-check-label" for="DocRemark_1">เซลส์เสนอราคาผิด</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="radio" class="form-check-input" name="DocRemark" id="DocRemark_2" value="2" />
                                            <label class="form-check-label" for="DocRemark_2">ลูกค้าขอราคาเดิม</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="radio" class="form-check-input" name="DocRemark" id="DocRemark_3" value="3" />
                                            <label class="form-check-label" for="DocRemark_3">คู่แข่งขายถูกกว่า</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-2">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="radio" class="form-check-input" name="DocRemark" id="DocRemark_4" value="4" />
                                            <label class="form-check-label" for="DocRemark_4">อื่น ๆ:</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-10">
                                        <input type="text" class="form-control" name="DocRemarkText" id="DocRemarkText" disabled />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="Attach">เอกสารแนบ</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="checkbox" class="form-check-input" name="Attach_1" id="Attach_1" value="Y" />
                                            <label class="form-check-label" for="Attach_1">บิล</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="checkbox" class="form-check-input" name="Attach_2" id="Attach_2" value="Y" />
                                            <label class="form-check-label" for="Attach_2">ใบราคาคู่แข่ง</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-2">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="checkbox" class="form-check-input" name="Attach_3" id="Attach_3" value="Y" />
                                            <label class="form-check-label" for="Attach_3">อื่น ๆ:</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-10">
                                        <input type="text" class="form-control" name="Attach_Remark" id="Attach_Remark" disabled />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="DocType">เงื่อนไขการลดหนี้/ลดจ่าย<span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="radio" class="form-check-input" name="DocType" id="DocType_A" value="A" />
                                            <label class="form-check-label" for="DocType_A">1. ลดหนี้ / ลดจ่ายทั้งบิล</label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="radio" class="form-check-input" name="DocType" id="DocType_B" value="B" />
                                            <label class="form-check-label" for="DocType_B">2. ลดหนี้ / ลดจ่ายเฉพาะรายการ</label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check mb-3 pt-2">
                                            <input type="radio" class="form-check-input" name="DocType" id="DocType_C" value="C" />
                                            <label class="form-check-label" for="DocType_C">3. ลดหนี้ / ลดจ่ายค่าขนส่งสินค้า</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- STEP 2A -->
                            <div id="newdoc-step2A" class="need-validation" data-step="2A">
                                <hr/>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4 class="h4">Step 2: กรอกข้อมูลการลดหนี้/ลดจ่ายทั้งบิล</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mt-4">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_RefDocNum">ค้นหาเลขที่บิล<span class="text-danger">*</span></label></div>
                                    <div class="col-lg-3 col-4"><input type="text" class="form-control" name="A_RefDocNum" id="A_RefDocNum" /></div>
                                    <div class="col-lg-1 col-2"><button type="button" class="btn btn-primary btn-block" onclick="RefDocSearch('A');"><i class="fas fa-search fa-fw fa-1x"></i></div>
                                    <div class="col-lg-2 col-2">
                                        <div class="form-check mb-3 pt-3">
                                            <input type="checkbox" class="form-check-input" name="A_BillVer" id="A_BillVer" value="Y" />
                                            <label class="form-check-label" for="A_BillVer">บิลก่อนวันที่ 31/12/2565</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mt-2 mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewDocNum">เลขที่บิล</label></div>
                                    <div class="col-lg-5 col-8">
                                        <input type="text" class="form-control-plaintext" name="A_ViewDocNum" id="A_ViewDocNum" placeholder="กรุณาค้นหาเลขที่บิลก่อน..." readonly />
                                        <input type="hidden" class="form-control" name="A_ViewDocEntry" id="A_ViewDocEntry" readonly />
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewDocDate">วันที่เปิดบิล</label></div>
                                    <div class="col-lg-2 col-8"><input type="date" class="form-control-plaintext" name="A_ViewDocDate" id="A_ViewDocDate" readonly /></div>
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewDocDueDate">วันที่กำหนดชำระ</label></div>
                                    <div class="col-lg-2 col-8"><input type="date" class="form-control-plaintext" name="A_ViewDocDueDate" id="A_ViewDocDueDate" readonly /></div>
                                </div>
                                <div class="form-group row align-items-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewSlpName">พนักงานขาย</label></div>
                                    <div class="col-lg-5 col-8">
                                        <input type="text" class="form-control-plaintext" name="A_ViewSlpName" id="A_ViewSlpName" placeholder="กรุณาค้นหาเลขที่บิลก่อน..." readonly />
                                        <input type="hidden" class="form-control" name="A_ViewSlpCode" id="A_ViewSlpCode" readonly />
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewCoName">ธุรการขาย</label></div>
                                    <div class="col-lg-5 col-8">
                                        <input type="text" class="form-control-plaintext" name="A_ViewCoName" id="A_ViewCoName" placeholder="กรุณาค้นหาเลขที่บิลก่อน..." readonly />
                                        <input type="hidden" class="form-control" name="A_ViewCoCode" id="A_ViewCoCode" readonly />
                                    </div>
                                </div>
                                <div class="form-group row align-item-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewDocTotal">จำนวนเงินทั้งหมด</label></div>
                                    <div class="col-lg-2 col-4">
                                        <input type="text" style="font-weight: bold;" class="form-control text-right text-danger" name="A_ViewDocTotal" id="A_ViewDocTotal" readonly />
                                        <!-- <input type="number" class="form-control" name="A_ViewDocTotalInt" id="A_ViewDocTotalInt" readonly /> -->
                                    </div>
                                    <div class="col-lg-1 col-4"><label class="col-form-label">บาท</label></div>
                                </div>
                                <div class="form-group row align-item-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewDiscount">ส่วนลด</label></div>
                                    <div class="col-lg-2 col-4"><input type="text" class="form-control text-right" name="A_ViewDiscount" id="A_ViewDiscount" /></label></div>
                                    <div class="col-lg-1 col-2"><select class="form-select" name="A_ViewDiscUnit" id="A_ViewDiscUnit"><option value="P">%</option><option value="B">บาท</option></select></div>
                                    <div class="col-lg-1 col-2"><button type="button" class="btn btn-primary btn-block" onclick="CalDiscA()"><i class="fas fa-calculator fa-fw fa-1x"></i></button></div>

                                </div>
                                <div class="form-group row align-item-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewSumDiscount">คิดเป็นเงิน</label></div>
                                    <div class="col-lg-2 col-4"><input type="text" style="font-weight: bold;" class="form-control text-right text-success" name="A_ViewSumDiscount" id="A_ViewSumDiscount" readonly /></label></div>
                                    <div class="col-lg-1 col-4"><label class="col-form-label">บาท</label></div>
                                </div>
                            </div>
                            <!-- STEP 2B -->
                            <div id="newdoc-step2B" class="need-validation" data-step="2B">
                                <hr/>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4 class="h4">Step 2: กรอกข้อมูลการลดหนี้/ลดจ่ายเฉพาะรายการ</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mt-4">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="B_RefDocNum">ค้นหาเลขที่บิล<span class="text-danger">*</span></label></div>
                                    <div class="col-lg-3 col-4"><input type="text" class="form-control" name="B_RefDocNum" id="B_RefDocNum" /></div>
                                    <div class="col-lg-1 col-2"><button type="button" class="btn btn-primary btn-block" onclick="RefDocSearch('B');"><i class="fas fa-search fa-fw fa-1x"></i></div>
                                    <div class="col-lg-2 col-2">
                                        <div class="form-check mb-3 pt-3">
                                            <input type="checkbox" class="form-check-input" name="B_BillVer" id="B_BillVer" value="Y" />
                                            <label class="form-check-label" for="B_BillVer">บิลก่อนวันที่ 31/12/2565</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mt-2 mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="B_ViewDocNum">เลขที่บิล</label></div>
                                    <div class="col-lg-5 col-8">
                                        <input type="text" class="form-control-plaintext" name="B_ViewDocNum" id="B_ViewDocNum" placeholder="กรุณาค้นหาเลขที่บิลก่อน..." readonly />
                                        <input type="hidden" class="form-control" name="B_ViewDocEntry" id="B_ViewDocEntry" readonly />
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="B_ViewDocDate">วันที่เปิดบิล</label></div>
                                    <div class="col-lg-2 col-8"><input type="date" class="form-control-plaintext" name="B_ViewDocDate" id="B_ViewDocDate" readonly /></div>
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="B_ViewDocDueDate">วันที่กำหนดชำระ</label></div>
                                    <div class="col-lg-2 col-8"><input type="date" class="form-control-plaintext" name="B_ViewDocDueDate" id="B_ViewDocDueDate" readonly /></div>
                                </div>
                                <div class="form-group row align-items-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="A_ViewSlpName">พนักงานขาย</label></div>
                                    <div class="col-lg-5 col-8">
                                        <input type="text" class="form-control-plaintext" name="B_ViewSlpName" id="B_ViewSlpName" placeholder="กรุณาค้นหาเลขที่บิลก่อน..." readonly />
                                        <input type="hidden" class="form-control" name="B_ViewSlpCode" id="B_ViewSlpCode" readonly />
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mb-3">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="B_ViewCoName">ธุรการขาย</label></div>
                                    <div class="col-lg-5 col-8">
                                        <input type="text" class="form-control-plaintext" name="B_ViewCoName" id="B_ViewCoName" placeholder="กรุณาค้นหาเลขที่บิลก่อน..." readonly />
                                        <input type="hidden" class="form-control" name="B_ViewCoCode" id="B_ViewCoCode" readonly />
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-sm" style="font-size: 12px;">
                                        <thead class="text-center">
                                            <tr>
                                                <th rowspan="2" width="3.5%">เลือก<br/><input type="CheckBox" id="ItemChckAll" /></th>
                                                <th rowspan="2" width="7.5%">รหัสสินค้า</th>
                                                <th rowspan="2">ชื่อสินค้า</th>
                                                <th colspan="3">ราคา/หน่วย (ก่อน VAT)</th>
                                                <th rowspan="2" colspan="2" width="10%" >จำนวน</th>
                                                <th rowspan="2" width="10%">ลดหนี้/ลดจ่ายรวม<br/>(ก่อน VAT)</th>
                                                <th rowspan="2" width="25%">หมายเหตุ</th>
                                            </tr>
                                            <tr>
                                                <th width="7%">เก่า</th>
                                                <th width="7%">ใหม่</th>
                                                <th width="7%">ส่วนต่าง</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ItemList"></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="8" class="text-right">รวมทุกรายการ</th>
                                                <td class="text-right"><input type="text" name="SumTotal" id="SumTotal" class="form-control-plaintext form-control-sm text-danger text-right" style="font-weight: bold;" value="0.00" readonly /></th>
                                                <th>บาท</th>
                                            </tr>
                                            <tr>
                                                <th colspan="8" class="text-right">ภาษีมูลค่าเพิ่ม</th>
                                                <td class="text-right"><input type="text" name="VatTotal" id="VatTotal" class="form-control-plaintext form-control-sm text-danger text-right" style="font-weight: bold;" value="0.00" readonly /></th>
                                                <th>บาท</th>
                                            </tr>
                                            <tr>
                                                <th colspan="8" class="text-right">ยอดลดหนี้/ลดจ่ายสุทธิ</th>
                                                <td class="text-right"><input type="text" name="CNTotal" id="CNTotal" class="form-control-plaintext form-control-sm text-danger text-right" style="font-weight: bold;" value="0.00" readonly /></th>
                                                <th>บาท</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <input type="hidden" class="form-control" id="TotalRow" name="TotalRow" readonly />
                                </div>
                            </div>
                            <!-- STEP 2C -->
                            <div id="newdoc-step2C" class="need-validation" data-step="2C">
                                <hr/>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4 class="h4">Step 2: กรอกข้อมูลการลดหนี้/ลดจ่ายค่าขนส่งสินค้า</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mt-4">
                                    <div class="col-lg-1 col-4"><label class="col-form-label" for="C_ViewSlpName">พนักงานขาย</label></div>
                                    <div class="col-lg-5 col-8">
                                        <input type="text" class="form-control-plaintext" name="C_ViewSlpName" id="C_ViewSlpName" placeholder="กรุณาค้นหาเลขที่บิลก่อน..." readonly />
                                        <input type="hidden" class="form-control" name="C_ViewSlpCode" id="C_ViewSlpCode" readonly />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 col-5">
                                        <div class="form-group mb-3">
                                            <label for="C_DocYear">เลือกปีที่เปิดบิล<span class="text-danger">*</span></label>
                                            <select class="form-select" name="C_DocYear" id="C_DocYear">
                                            <?php
                                            for($y = date("Y"); $y >= 2015; $y--) {
                                                echo "<option value='$y'>$y</option>";
                                            } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-5">
                                        <div class="form-group mb-3">
                                            <label for="C_DocMonth">เลือกเดือน<span class="text-danger">*</span></label>
                                            <select class="form-select" name="C_DocMonth" id="C_DocMonth">
                                            <?php
                                            for($m = 1; $m <= 12; $m++) {
                                                if($m == $this_month) {
                                                    $m_slct = " selected";
                                                } else {
                                                    $m_slct = "";
                                                }
                                                echo "<option value='$m'$m_slct>".FullMonth($m)."</option>";
                                            }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-2">
                                        <div class="form-group mb-3">
                                            <label for="null">&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" onclick="GetShipBill()"><i class="fas fa-search fa-fw fa-1x"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row align-item-center mb-3">
                                    <div class="col-lg-3 col-4"><label class="col-form-label" for="C_ShipCostTotal">ขอลดหนี้/ลดจ่ายให้กับค่าขนส่งของบิลต่อไปนี้คิดเป็นเงิน</label></div>
                                    <div class="col-lg-1 col-4"><input type="text" class="form-control text-right" name="C_ShipCostTotal" id="C_ShipCostTotal" /></label></div>
                                    <div class="col-lg-1 col-4"><label class="col-form-label">บาท</label></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-sm" style="font-size: 12px;">
                                        <thead class="text-center">
                                            <tr>
                                                <th width="3.5%">เลือก<br/><input type="CheckBox" id="BillChckAll" /></th>
                                                <th width="7.5%">วันที่เปิดบิล</th>
                                                <th width="7.5%">วันที่กำหนดชำระ</th>
                                                <th>เลขที่เอกสาร</th>
                                                <th width="12.5%">จำนวนเงิน (บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ShipBillList"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg text-right">
                                    <button type="button" class="btn btn-primary" onclick="SaveDoc();"><i class="fas fa-save fa-fw fa-1x"></i> เพิ่มเอกสารใหม่</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

<!-- MODAL PREVIEW -->
<div class="modal fade" id="PreviewDoc" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file fa-fw fa-1x"></i> เอกสารเลขที่: <span class="ViewDocNum"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-lg">
                        <table class="table table-borderless table-sm" style="font-size: 12px;">
                            <tr>
                                <td width="20%" style="font-weight: bold;">เลขที่เอกสาร</td>
                                <td width="30%" class="ViewDocNum"></td>
                                <td width="20%" style="font-weight: bold;">วันที่เอกสาร</td>
                                <td width="30%" id="ViewDocDate"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">ชื่อร้านค้า</td>
                                <td colspan="3" id="ViewCardCode"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">เหตุผลขอการลดหนี้/ลดจ่าย</td>
                                <td colspan="3" id="ViewDocRemark"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">เงื่อนไขการลดหนี้/ลดจ่าย</td>
                                <td colspan="3" id="ViewDocType"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">ผู้จัดทำ</td>
                                <td colspan="3" id="ViewCreateName"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- DOC TAB -->
                <ul class="nav nav-tabs" id="Doc-Tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#DocDetail" class="btn btn-tabs nav-link active" id="DocDetailTab" data-bs-toggle="tab" data-bs-target="#DocDetail" role="tab" data-tabs="0" aria-controls="DocDetailTab" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-list-ol fa-fw fa-1x"></i> รายละเอียด
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#ApproveList" class="btn btn-tabs nav-link" id="DocApproveTab" data-bs-toggle="tab" data-bs-target="#ApproveList" role="tab" data-tabs="1" aria-controls="ApproveList" aria-selected="true" style="font-size: 12px;">
                            <i class="fas fa-tasks fa-fw fa-1x"></i> สถานะการอนุมัติ
                        </a>
                    </li>
                </ul>
                <!-- CONTENT TAB -->
                <div class="tab-content">
                    <div class="tab-pane show active" id="DocDetail" role="tabpanel" aria-labelledby="DocDetailTab" style="font-size: 12px;"></div>
                    <div class="tab-pane show" id="ApproveList" role="tabpanel" aria-labelledby="DocApproveTab" style="font-size: 12px;">
                        <div class="row mt-4">
                            <div class="col-12" id="preview_Approve">
                                <table class="table table-bordered" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="2" width="5%">ลำดับ</th>
                                            <th rowspan="2" width="15%">ผู้อนุมัติ</th>
                                            <th rowspan="2" width="10%">ผลการ<br/>พิจารณา</th>
                                            <th colspan="3">ค่าปรับ</th>
                                            <th rowspan="2">หมายเหตุ</th>
                                            <th rowspan="2" width="15%" >วันที่อนุมัติ</th>
                                        </tr>
                                        <tr>
                                            <th width="10%">ไม่ปรับ</th>
                                            <th width="10%">ปรับพนักงานขาย<br/>(100 บาท)</th>
                                            <th width="10%">ปรับธุรการขาย</br/>(20 บาท)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ApproveListTable"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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

function GetCardCode(){
    $(".overlay").show();
    $.ajax({
        url: "../json/OCRD.json",
        cache: false,
        success: function(result) {
            var filt_data = result.
                                filter(x => x.CardStatus == "A").
                                filter(x => x.CardType == "C").
                                sort(function(key, inval) {
                                    return key.CardCode.localeCompare(inval.CardCode);
                                });
            var opt = "";

            $.each(filt_data, function(key, inval) {
                opt += "<option value='"+inval.CardCode+"'>"+inval.CardCode+" | "+inval.CardName+"</option>";
            });
            $("#CardCode").append(opt).selectpicker();
        }
    });
    $(".overlay").hide();
}

function Reset2A() {
    $("#A_RefDocNum").val('');
    $("#A_ViewDocNum").val('');
    $("#A_ViewDocDate").val('');
    $("#A_ViewDocDueDate").val('');
    $("#A_ViewSlpCode").val('');
    $("#A_ViewSlpName").val('');
    $("#A_ViewCoCode").val('');
    $("#A_ViewCoName").val('');
    $("#A_ViewDocTotal").val('');
    $("#A_ViewDiscount").val('');
    $("#A_ViewDiscUnit").val('P').change();
    $("#A_SumDiscount").val('');
}

function Reset2B() {
    $("#B_RefDocNum").val('');
    $("#B_ViewDocNum").val('');
    $("#B_ViewDocDate").val('');
    $("#B_ViewDocDueDate").val('');
    $("#B_ViewSlpCode").val('');
    $("#B_ViewSlpName").val('');
    $("#B_ViewCoCode").val('');
    $("#B_ViewCoName").val('');
    $("#ItemList").empty();
    $("#SumTotal").val('');
    $("#VatTotal").val('');
    $("#CNTotal").val('');
}

function Reset2C() {
    var this_year  = '<?php echo date("Y"); ?>';
    var this_month = '<?php echo date("m"); ?>';
    $("#C_DocYear").val(this_year).change();
    $("#C_DocMonth").val(this_month).change();
    $("#C_ShipCostTotal").val('');
    $("#ShipBillList").empty();

}

function RefDocSearch(DocType) {
    var DocType = DocType;
    var CardCode  = $("#CardCode").val();
    var SearchBox = $("#"+DocType+"_RefDocNum").val();
    var BillVer   = $("#"+DocType+"_BillVer").is(":checked");
    if((CardCode == "" || CardCode == null) || SearchBox == "") {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกลูกค้าก่อนค้นหาบิล");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/sale/ajax/ajaxDocSA04.php?p=GetRefDoc",
            type: "POST",
            data: {
                c: CardCode,
                s: SearchBox,
                t: DocType,
                v: BillVer
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    if(inval['GetStatus'] == "SUCCESS") {
                        $("#"+DocType+"_ViewDocEntry").val(inval['IVEntry']);
                        $("#"+DocType+"_ViewDocNum").val(inval['DocNum']);
                        $("#"+DocType+"_ViewDocDate").val(inval['DocDate']);
                        $("#"+DocType+"_ViewDocDueDate").val(inval['DocDueDate']);
                        $("#"+DocType+"_ViewSlpCode").val(inval['SlpCode']);
                        $("#"+DocType+"_ViewSlpName").val(inval['SlpName']);
                        $("#"+DocType+"_ViewCoCode").val(inval['CoCode']);
                        $("#"+DocType+"_ViewCoName").val(inval['CoName']);

                        switch(DocType) {
                            case "A":
                                $("#A_ViewDocTotal").val(number_format(inval['DocTotal'],3))
                                $("#A_ViewDocTotalInt").val(inval['DocTotal']);
                            break;
                            case "B":
                                $("#TotalRow").val(inval['Rows']);
                                var NewRow;
                                for(i=0;i<inval['Rows'];i++) {
                                    NewRow += "<tr data-ItemRow='"+i+"'>"+
                                        "<td class='text-center'><input type='checkbox' class='chck' name='ItemCheck_"+i+"' data-ItemRow='"+i+"' value='"+i+"' /></td>"+
                                        "<td><input type='text' data-ItemRow='"+i+"' name='ItemCode_"+i+"' class='form-control-plaintext form-control-sm text-center' value='"+inval['ItemRow_'+i]['ItemCode']+"' readonly /></td>"+
                                        "<td><input type='text' data-ItemRow='"+i+"' name='ItemName_"+i+"' class='form-control-plaintext form-control-sm' value='"+inval['ItemRow_'+i]['ItemName']+"' readonly /></td>"+
                                        "<td><input type='text' data-ItemRow='"+i+"' name='OldPrice_"+i+"' class='form-control-plaintext form-control-sm text-right' value='"+number_format(inval['ItemRow_'+i]['DocPrice'],3)+"' readonly /></td>"+
                                        "<td><input type='text' data-ItemRow='"+i+"' name='NewPrice_"+i+"' class='form-control form-control-sm text-right' readonly /></td>"+
                                        "<td><input type='text' data-ItemRow='"+i+"' name='DifPrice_"+i+"' class='form-control-plaintext form-control-sm text-right' readonly /></td>"+
                                        "<td width='5%'><input type='text' data-ItemRow='"+i+"' name='Quantity_"+i+"' class='form-control form-control-sm text-right' value='"+number_format(inval['ItemRow_'+i]['Quantity'],0)+"' readonly /></td>"+
                                        "<td width='5%'><input type='text' data-ItemRow='"+i+"' name='UnitMsr_"+i+"' class='form-control-plaintext form-control-sm' value='"+inval['ItemRow_'+i]['UnitMsr']+"' readonly /></td>"+
                                        "<td><input type='text' data-ItemRow='"+i+"' name='DifTotal_"+i+"' class='form-control-plaintext form-control-sm text-danger text-right' style='font-weight: bold;' readonly /></td>"+
                                        "<td><input type='text' data-ItemRow='"+i+"' name='Remark_"+i+"' class='form-control form-control-sm' readonly /></td>"+
                                    "</tr>";
                                }
                                $("#ItemList").html(NewRow);

                                /* Event Listener */
                                $("input#ItemChckAll").on("click", function() {
                                    if($(this).is(":checked")) {
                                        $("#ItemList tr input.chck").prop("checked",true);
                                        $("#ItemList tr").addClass("table-warning");
                                        $("#ItemList tr input[name*='NewPrice_'], #ItemList tr input[name*='Quantity_'], #ItemList tr input[name*='Remark_']").removeAttr("readonly");
                                    } else {
                                        $("#ItemList tr input.chck").prop("checked",false);
                                        $("#ItemList tr").removeAttr("class");
                                        $("#ItemList tr input[name*='NewPrice_'], #ItemList tr input[name*='Quantity_'], #ItemList tr input[name*='Remark_']").val('').attr("readonly",true);
                                        $("#ItemList tr input[name*='DifPrice_'], #ItemList tr input[name*='DifTotal_']").val('');
                                    }
                                    CalDiscB();
                                });

                                $("input.chck").on("click",function(){
                                    var RowID = $(this).val();
                                    if($(this).is(":checked")) {
                                        $("#ItemList tr[data-ItemRow='"+RowID+"']").addClass("table-warning");
                                        $("#ItemList tr input[name='NewPrice_"+RowID+"'], #ItemList tr input[name*='Quantity_"+RowID+"'], #ItemList tr input[name='Remark_"+RowID+"']").removeAttr("readonly");
                                        $("#ItemList tr input[name='NewPrice_"+RowID+"']").focus();
                                    } else {
                                        $("#ItemList tr[data-ItemRow='"+RowID+"']").removeAttr("class");
                                        $("#ItemList tr input[name='NewPrice_"+RowID+"'], #ItemList tr input[name*='Quantity_"+RowID+"'], #ItemList tr input[name='Remark_"+RowID+"']").val('').attr("readonly",true);
                                        $("#ItemList tr input[name='DifPrice_"+RowID+"'], #ItemList tr input[name='DifTotal_"+RowID+"']").val('');
                                    }
                                    CalDiscB();
                                });

                                $("input[name*='NewPrice_'], input[name*='Quantity_']").on("focusout", function() {
                                    var RowID    = strtoNumber($(this).attr("data-ItemRow"));
                                    var OldPrice = strtoNumber($("input[name='OldPrice_"+RowID+"']").val());
                                    var NewPrice = strtoNumber($("input[name='NewPrice_"+RowID+"']").val());
                                    var Quantity = strtoNumber($("input[name='Quantity_"+RowID+"']").val());
                                    if(isNaN(NewPrice) == false) {
                                        var DifPrice = OldPrice - NewPrice;
                                        var DifTotal = DifPrice * Quantity;
                                        $("input[name='NewPrice_"+RowID+"']").val(number_format(NewPrice,3));
                                        $("input[name='DifPrice_"+RowID+"']").val(number_format(DifPrice,3));
                                        $("input[name='DifTotal_"+RowID+"']").val(number_format(DifTotal,3));
                                        CalDiscB();
                                    }
                                });
                            break;
                        }
                    } else {
                        var alert_header = "<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!";
                        var alert_body;
                        switch(inval['GetStatus']) {
                            case "ERR::NO_RESULT":
                                alert_body   = "ไม่พบบิลที่ต้องการค้นหา กรุณาลองใหม่อีกครั้ง";
                            break;
                        }
                        $("#alert_header").html(alert_header);
                        $("#alert_body").html(alert_body);
                        $("#alert_modal").modal('show');
                    }
                });
            }
        })
    }
}

function CalDiscA() {
    console.log("click");
    var DocTotal = strtoNumber($("#A_ViewDocTotal").val());
    var Discount = strtoNumber($("#A_ViewDiscount").val());
    var DiscUnit = $("#A_ViewDiscUnit").val();
    var FinalDis;
    switch(DiscUnit) {
        case "P": FinalDis = (DocTotal*Discount)/100; break;
        case "B": FinalDis = Discount; break;
    }
    $("#A_ViewDiscount").val(number_format(Discount,3));
    $("#A_ViewSumDiscount").val(number_format(FinalDis,3));
}

function CalDiscB() {
    var TotalRow = $("#TotalRow").val();
    var SumTotal = 0;
    for(i=0;i<TotalRow;i++) {
        if($("input.chck[data-ItemRow='"+i+"']").is(":checked")) {
            var LineTotal = strtoNumber($("input[name='DifTotal_"+i+"']").val());
            console.log(SumTotal,LineTotal);
            SumTotal = SumTotal+LineTotal;
        }
    }
    console.log(SumTotal);
    VatTotal = (SumTotal*7)/100;
    CNTotal  = SumTotal + VatTotal;

    if(isNaN(SumTotal) == false) {
        $("#SumTotal").val(number_format(SumTotal,3));
        $("#VatTotal").val(number_format(VatTotal,3));
        $("#CNTotal").val(number_format(CNTotal,3));
    }
    
}

function GetShipBill() {
    var filt_year  = $("#C_DocYear").val();
    var filt_month = $("#C_DocMonth").val();
    var CardCode   = $("#CardCode").val();

    if(CardCode == "" || CardCode == null) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกลูกค้าก่อนค้นหาบิล");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/sale/ajax/ajaxDocSA04.php?p=GetShipBill",
            type: "POST",
            data: {
                y: filt_year,
                m: filt_month,
                c: CardCode
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    if(inval['GetStatus'] == "SUCCESS") {
                        var NewRow;
                        for(i=0;i<inval['Rows'];i++) {
                            NewRow += "<tr data-ItemRow='"+i+"'>"+
                                "<td class='text-center'><input type='checkbox' class='BillChk' name='BillCheck_"+i+"' value='"+inval['BillRow_'+i]['DocEntry']+"' data-ItemRow='"+i+"' /></td>"+
                                "<td class='text-center'>"+inval['BillRow_'+i]['DocDate']+"</td>"+
                                "<td class='text-center'>"+inval['BillRow_'+i]['DocDueDate']+"</td>"+
                                "<td><input type='text' class='form-control-plaintext form-control-sm' name='BillDocNum_"+i+"' value='"+inval['BillRow_'+i]['DocNum']+"' readonly /></td>"+
                                "<td class='text-right text-danger' style='font-weight: bold;'>"+number_format(inval['BillRow_'+i]['DocTotal'],3)+"</td>"+
                            "</tr>";
                        }
                        $("#ShipBillList").html(NewRow);
                        $("#TotalRow").val(inval['Rows']);
                        $("#C_ViewSlpCode").val(inval['SlpCode']);
                        $("#C_ViewSlpName").val(inval['SlpName']);

                        /* Event Listener */
                        $("#BillChckAll").on("click",function(){
                            if($(this).is(":checked")) {
                                $("#ShipBillList tr input.BillChk").prop("checked",true);
                                $("#ShipBillList tr").addClass("table-warning");
                            } else {
                                $("#ShipBillList tr input.BillChk").prop("checked",false);
                                $("#ShipBillList tr").removeAttr("class");
                            }
                        });

                        $(".BillChk").on("click",function(){
                            var RowID = $(this).attr("data-ItemRow");
                            if($(this).is(":checked")) {
                                $("#ShipBillList tr[data-ItemRow='"+RowID+"']").addClass("table-warning");
                            } else {
                                $("#ShipBillList tr[data-ItemRow='"+RowID+"']").removeAttr("class");
                            }
                        });
                        
                    } else {
                        var alert_header = "<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!";
                        var alert_body;
                        switch(inval['GetStatus']) {
                            case "ERR::NO_RESULT":
                                alert_body   = "ไม่พบบิลที่ต้องการค้นหา กรุณาลองใหม่อีกครั้ง";
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
}

function SaveDoc() {
    var ErrorPoint = 0;
    var ErrorID    = [];
    var SuccessID  = [];

    if($("#CardCode").val() == null) { 
        ErrorPoint++;
    }

    if($("#DocDate").val() == null) {
        ErrorPoint++;
    }

    if($("input[name='DocRemark']:checked").val() == null) {
        ErrorPoint++;
    } else {
        if($("input[name='DocRemark']:checked").val() == "4" && $("#DocRemarkText").val() == "") {
            ErrorPoint++;
        }
    }

    if($("input[name='DocType']:checked").val() == null) {
        ErrorPoint++;
    }

    if(ErrorPoint > 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        var SA04Form = new FormData($("#SendDocForm")[0]);
        $.ajax({
            url: "menus/sale/ajax/ajaxDocSA04.php?p=SaveDoc",
            type: "POST",
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: SA04Form ,
            success: function() {
                $("#confirm_saved").modal("show");

                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                })
            }
        });
    }
}

function PreviewDoc(DocEntry,int_status) {
    /* do something */
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxDocSA04.php?p=PreviewDoc",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $(".ViewDocNum").html(inval['ViewDocNum']);
                $("#ViewDocDate").html(inval['ViewDocDate']);
                $("#ViewCardCode").html(inval['ViewCardCode']);
                $("#ViewDocRemark").html(inval['ViewDocRemark']);
                $("#ViewDocType").html(inval['ViewDocType']);
                $("#ViewCreateName").html(inval['ViewCreateName']);

                $("#DocDetail").html(inval['DocDetail']);
                $("#ApproveListTable").html(inval['view_approvelist'])
            });
            $("#PreviewDoc").modal("show");
            $(".nav-tabs a[href='#DocDetail']").tab("show");
        }
    });
}

function ExportDoc(DocEntry) {
    var DocEntry = DocEntry;
    $.ajax({
        url: "menus/sale/ajax/ajaxDocSA04.php?p=ExportDoc",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval){
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
                            alert_body   = "ไม่สามารถเพิ่มเอกสารนี้ได้เนื่องจากเอกสารนี้ยังไม่ถูกบัญชีตีกลับในระบบรับ/ส่งเอกสารบัญชี";
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

function CancelDoc(DocEntry) {
    var DocEntry = DocEntry;
    $(".modal").modal("hide");
    $("#confirm_cancel").modal("show");

    $("#btn-cancel-confirm").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            url: 'menus/sale/ajax/ajaxDocSA04.php?p=CancelDoc',
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

function PrintDoc(DocEntry,int_status) {
    var DocEntry   = DocEntry;
    var int_status = int_status;
    window.open('menus/sale/print/printsa04.php?DocEntry='+DocEntry,'_blank');
}

function GetDocList(filt_year,filt_month,filt_team) {
    $(".overlay").show();

    $.ajax({
        url: "menus/sale/ajax/ajaxDocSA04.php?p=GetDocList",
        type: "POST",
        data: { filt_year: filt_year, filt_month: filt_month, filt_team: filt_team },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                $("#DocListTable").html(inval['DocList']);
            });
        }
    });
}

$(document).ready(function(){
    CallHead();
    GetCardCode();
    
    $("div[id*='newdoc-step2']").hide();

    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team  = $("#filt_team").val();
    GetDocList(filt_year, filt_month, filt_team);

});

/* เมื่อเลือก ปี เดือน หรือ ทีม */
$("#filt_year, #filt_month, #filt_team").on("change", function() {
    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team  = $("#filt_team").val();
    GetDocList(filt_year, filt_month, filt_team);
});

/* เมื่อกรอกข้อความสำหรับค้นหา */
$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#DocListTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

$("input[name='DocRemark']").on("click",function() {
    var DocRemark = $(this).val();
    var DisOption;
    switch(DocRemark) {
        case "4": $("#DocRemarkText").attr("disabled",false).val('').focus(); break;
        default:  $("#DocRemarkText").attr("disabled",true).val(''); break;
    }
});

$("#Attach_3").on("click",function() {
    if($(this).is(":checked")) {
        $("#Attach_Remark").attr("disabled",false).val('').focus();
    } else {
        $("#Attach_Remark").attr("disabled",true).val('');
    }
});

$("input[name='DocType']").on("click",function() {
    var DocType = $(this).val();
    switch(DocType) {
        case "A": Reset2B(); Reset2C(); break;
        case "B": Reset2A(); Reset2C(); break;
        case "C": Reset2A(); Reset2B(); break;
    }
    $("div[id*='newdoc-step2']").hide();
    $("#newdoc-step2"+DocType).show();
});
</script> 
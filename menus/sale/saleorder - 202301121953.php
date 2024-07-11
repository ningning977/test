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

    .tableFixHead th {
        position: sticky;
        top: 0;
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
<script src="https://cdn.ckeditor.com/ckeditor5/35.2.1/classic/ckeditor.js"></script>
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
<div class="container-fluid">
  <div class="row">
    <div class="table-responsive" id="ShowData"></div>
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
                        <a href="#OrderList" class="btn-tabs nav-link active" id="OrderList-tab" data-bs-toggle="tab" data-bs-target="#OrderList" role="tab" data-tabs="0" aria-controls="OrderList" aria-selected="false">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการใบสั่งขาย
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#NewOrder" class="btn-tabs nav-link" id="NewOrder-tab" data-bs-toggle="tab" data-bs-target="#NewOrder" role="tab" data-tabs="1" aria-controls="NewOrder" aria-selected="true">
                            <i class="fas fa-plus fa-fw fa-1x"></i> เพิ่ม/แก้ไขใบสั่งขายใหม่
                        </a>
                    </li>
                </ul>
                <!-- END CONTENT TAB -->
                <div class="tab-content">
                    <!-- TAB 0 -->
                    <div class="tab-pane fade show active" id="OrderList" role="tabpanel" aria-labelledby="OrderList-tab">
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
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                                $DeptCode = $_SESSION['DeptCode'];
                                $opt_ALL = " disabled";
                                $opt_MT1 = " disabled";
                                $opt_EXP = " disabled";
                                $opt_MT2 = " disabled";
                                $opt_TT2 = " disabled";
                                $opt_TT1 = " disabled";
                                $opt_OUL = " disabled";
                                $opt_ONL = " disabled";
                                switch($DeptCode) {
                                    case "DP006": 
                                        $opt_MT1 = " selected";
                                        $opt_EXP = NULL;
                                    break;
                                    case "DP007":
                                        $opt_MT2 = " selected";
                                    break;
                                    case "DP005":
                                        $opt_TT2 = " selected";
                                    break;
                                    case "DP008":
                                        $opt_TT1 = " selected";
                                        $opt_OUL = NULL;
                                    break;
                                    default:
                                        $opt_ALL = NULL;
                                        $opt_MT1 = NULL;
                                        $opt_EXP = NULL;
                                        $opt_MT2 = NULL;
                                        $opt_TT2 = NULL;
                                        $opt_TT1 = NULL;
                                        $opt_OUL = NULL;
                                        $opt_ONL = NULL;
                                    break;
                                }
                            ?>
                            <div class="col-lg-2 col-6">
                                <div class="form-group">
                                    <label for="filt_team">เลือกทีมขาย</label>
                                    <select class="form-select form-select-sm" name="filt_team" id="filt_team">
                                        <option value="ALL"<?php echo $opt_ALL; ?>>ทุกทีม</option>
                                        <option value="MT1"<?php echo $opt_MT1; ?>><?php echo SATeamName("MT1"); ?></option>
                                        <option value="EXP"<?php echo $opt_EXP; ?>><?php echo SATeamName("EXP"); ?></option>
                                        <option value="MT2"<?php echo $opt_MT2; ?>><?php echo SATeamName("MT2"); ?></option>
                                        <option value="TT2"<?php echo $opt_TT2; ?>><?php echo SATeamName("TT2"); ?></option>
                                        <option value="TT1"<?php echo $opt_TT1; ?>><?php echo SATeamName("TT1"); ?></option>
                                        <option value="OUL"<?php echo $opt_OUL; ?>><?php echo SATeamName("OUL"); ?></option>
                                        <option value="ONL"<?php echo $opt_ONL; ?>><?php echo SATeamName("ONL"); ?></option>
                                        <option value="KBI"><?php echo SATeamName("KBI"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="offset-lg-4 col-lg-3 col-6">
                                <div class="form-group">
                                    <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา:</label>
                                    <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรอกข้อความเพื่อค้นหา" list="filt_list" />
                                    <datalist id="filt_list">
                                        <option value="MT1">
                                        <option value="MT2">
                                        <option value="TT2">
                                        <option value="TT1">
                                        <option value="OUL">
                                        <option value="ONL">
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered" style="font-size: 12px;">
                                <thead>
                                    <tr class="text-center">
                                        <th width="3.5%">ลำดับ</th>
                                        <th width="6.5%">วันที่เอกสาร</th>
                                        <th width="6.5%">วันที่กำหนดส่ง</th>
                                        <th width="7.5%">เลขที่เอกสาร</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th width="10%">เอกสารอ้างอิง</th>
                                        <th width="6.5%">ยอดขาย</th>
                                        <th width="17.5%"><span class='badge bg-dark'>ทีม</span> พนักงานขาย</th>
                                        <th width="7.5%">สถานะเอกสาร</th>
                                        <th width="7.5%">SAP S/O No.</th>
                                        <th width="5%"><i class="fas fa-cog fa-fw fa-1x"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="OrderTable">

                                </tbody>
                            </table>
                        </div>
                        <div id="OrderTable"></div>
                    </div>
                    <!-- TAB 1 -->
                    <div class="tab-pane fade" id="NewOrder" role="tabpanel" aria-labelledby="NewOrder-tab">
                        <form class="form" id="OrderForm" enctype="multipart/form-data">
                            <!-- STEP 1 -->
                            <div id="order-step1" class="need-validation" data-step="1">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 1: เลือกข้อมูลลูกค้า และข้อมูลการเปิดบิล</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="CardCode">ชื่อลูกค้า<span class="text-danger">*</span></label>
                                            <select class="selectpicker form-control" name="CardCode" id="CardCode" data-live-search="true" data-size="10" aria-placeholder="กรุณาเลือกลูกค้า" required></select>
                                            <input type="hidden" name="OrderEntry" id="OrderEntry" readonly />
                                            <!-- <select class="form-select" name="CardCode" id="CardCode" data-live-search="true" aria-placeholder="กรุณาเลือกลูกค้า" required></select> -->
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="LicTradeNum">เลขที่ประจำตัวผู้เสียภาษี</label>
                                            <input type="text" class="form-control text-center" name="LicTradeNum" id="LicTradeNum" placeholder="กรุณากรอกเลขที่ประจำตัวผู้เสียภาษี (ถ้ามี)" disabled />
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="DocType">ประเภทเอกสาร<span class="text-danger">*</span></label>
                                            <select class="form-select" id="DocType" name="DocType">
                                                <option value="SO">ใบสั่งขาย (SO / SN)</option>
                                                <option value="SA">ใบยืมสินค้า (SA)</option>
                                                <option value="SB">ใบเบิกสินค้า (SB)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg">
                                        <div class="form-group mb-3">
                                            <label for="AddressBillto">ที่อยู่สำหรับเปิดบิล<span class="text-danger">*</span></label>
                                            <select class="form-select" name="AddressBillTo" id="AddressBillTo" disabled required>
                                                <option value="" disabled>กรุณาเลือกที่อยู่เปิดบิล</option>
                                            </select>
                                            <small class="text-muted">ตัวหนังสือหนาหมายถึงค่าเริ่มต้น</small>
                                            <input type="hidden" name="AddressBillTo_text" id="AddressBillTo_text" readonly />
                                        </div>
                                    </div>
                                    <div class="col-lg">
                                        <div class="form-group mb-3">
                                            <label for="AddressShipto">ที่อยู่สำหรับจัดส่ง<span class="text-danger">*</span></label>
                                            <select class="form-select" name="AddressShipto" id="AddressShipto" disabled required>
                                                <option value="" disabled>กรุณาเลือกที่อยู่จัดส่ง</option>
                                            </select>
                                            <small class="text-muted">ตัวหนังสือหนาหมายถึงค่าเริ่มต้น</small>
                                            <input type="hidden" name="AddressShipto_text" id="AddressShipto_text" readonly />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg col-sm-6">
                                        <div class="form-group mb-3">
                                            <label for="TaxType">ประเภทภาษี<span class="text-danger">*</span></label>
                                            <select class="form-select" name="TaxType" id="TaxType" disabled required>
                                                <option value="" selected disabled>กรุณาเลือกประเภทภาษี</option>
                                                <option value="S07">VAT นอก</option>
                                                <option value="S00">VAT ใน</option>
                                                <option value="SNV">ไม่มี VAT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg col-sm-6">
                                        <div class="form-group mb-3">
                                            <label for="PaymentTerm">เงื่อนไขชำระเงิน<span class="text-danger">*</span></label>
                                            <select class="form-select" name="PaymentTerm" id="PaymentTerm" disabled required>
                                                <option value="" selected disabled>กรุณาเลือกเงื่อนไขชำระเงิน</option>
                                                <option value="CR">เครดิต</option>
                                                <option value="CS">เงินสด</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg col-sm-6">
                                        <div class="form-group mb-3">
                                            <label for="DocDate">วันที่เอกสาร<span class="text-danger">*</span></label>
                                            <input type="Date" class="form-control" name="DocDate" id="DocDate" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d"); ?>" disabled required />
                                        </div>
                                    </div>
                                    <div class="col-lg col-sm-6">
                                        <div class="form-group mb-3">
                                            <label for="DocDueDate">วันที่กำหนดส่ง</label>
                                            <input type="Date" class="form-control" name="DocDueDate" id="DocDueDate" min="<?php echo date("Y-m-d"); ?>" value="<?php echo Next3Day(date("Y-m-d")); ?>" disabled />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="SlpCode">พนักงานขาย<span class="text-danger">*</span></label>
                                            <select class="form-select" name="SlpCode" id="SlpCode" disabled required></select>
                                            <input type="hidden" name="SlpName" id="SlpName" readonly />
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="U_PONo">อ้างอิงเลขที่ PO</label>
                                            <input type="text" class="form-control me-1" name="U_PONo" id="U_PONo" placeholder="กรุณากรอกเลขที่ PO" disabled/>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="ShippingType">ขนส่ง</label>
                                            <select class="form-control" name="ShippingType" id="ShippingType" data-live-search="true" aria-placeholder="กรุณาเลือกขนส่ง" disabled></select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="ShipCostType">รูปแบบชำระค่าขนส่ง</label>
                                            <select class="form-select" name="ShipCostType" id="ShipCostType" disabled>
                                                <option disabled selected>กรุณาเลือกรูปแบบค่าขนส่ง</option>
                                                <option value="PRE">เก็บเงินค่าขนส่งต้นทาง</option>
                                                <option value="PST">เก็บเงินค่าขนส่งปลายทาง</option>
                                                <option value="COD">ชำระค่าสินค้าปลายทาง (COD)</option>
                                                <option value="FREE">ไม่มีค่าขนส่ง</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="ShipComment">หมายเหตุขนส่ง</label>
                                            <input type="text" class="form-control" name="ShipComment" id="ShipComment" placeholder="กรอกข้อมูลของผู้รับปลายทาง เช่น ชื่อ เบอร์ สถานที่นัดหมาย..." disabled />
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="OrderAttach">แนบไฟล์</label>  <a href="javascript:void(0);" class="text-muted" data-bs-toggle="tooltip" title="รองรับนามสกุลไฟล์รูปภาพ (*.jpg, *.jpeg, *.png) / MS Word (*.doc, *.docx) / MS Excel (*.xls, *.xlsx) / เอกสาร (*.pdf) เท่านั้น"><i class="far fa-question-circle fa-fw fa-lg"></i></a>
                                            <input type="file" class="form-control" name="OrderAttach[]" id="OrderAttach" accept=".jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf" disabled multiple />
                                            <a href="javascript:void(0);" class="text-muted" id="btn-browse-file"><i class="fas fa-folder-open fa-fw fa-1x"></i> รายการเอกสารแนบ</a>
                                        </div>
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="col-lg text-right">
                                        <!-- <button type="button" class="btn-prev btn btn-secondary" data-step="2" data-goto="1"><i class="fas fa-chevron-left fa-fw fa-1x"></i> ย้อนกลับ</button> -->
                                        <button type="button" class="btn-next btn btn-primary" data-step="1" data-goto="2">ต่อไป <i class="fas fa-chevron-right fa-fw fa-1x"></i></button>
                                    </div>
                                </div>
                            </div>
                            <!-- END OF STEP 1 -->
                            
                            <!-- STEP 2 -->
                            <div id="order-step2" class="need-validation" data-step="2">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 2: เพิ่มข้อมูลสินค้า</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <button type="button" class="btn btn-primary" id="AddItem"><i class="fas fa-plus fa-fw fa-1X"></i> เพิ่มรายการใหม่</button>
                                        <button type="button" class="btn btn-secondary" id="ImportItem"><i class="fas fa-file-import fa-fw fa-1x"></i> นำเข้า</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <div class="table-responsive tableFixHead">
                                            <table class="table table-bordered table-striped table-hover" id="ItemList">
                                                <thead class="text-center table-group-divider">
                                                    <tr>
                                                        <th width="7.5%">รหัสสินค้า</th>
                                                        <th width="10%">บาร์โค้ด</th>
                                                        <th>[สถานะ] ชื่อสินค้า</th>
                                                        <th width="7.5%">คลังสินค้า</th>
                                                        <th width="7.5%">จำนวน</th>
                                                        <th width="5%">หน่วยขาย</th>
                                                        <th width="10%">ราคาขาย</th>
                                                        <th width="10%">ส่วนลด</th>
                                                        <th width="7.5%">ราคาสุทธิ</th>
                                                        <th width="10%">รวมทั้งหมด</th>
                                                        <th width="5%">ดำเนินการ</th>
                                                        <th width="7.5%">จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ItemListData"></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="7" rowspan="5" style="vertical-align: top;">
                                                            <textarea class="form-control" id="DocRemark" name="DocRemark" rows="8" placeholder="ระบุหมายเหตุ"></textarea>
                                                        </td>
                                                        <td colspan="2" class="text-right">ยอดรวมทุกรายการ</td>
                                                        <td><input class="form-control-plaintext text-right" type="text" step="any" name="TotalPrice" id="TotalPrice" readonly/></td>
                                                        <td colspan="2">บาท</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-right">ส่วนลดท้ายบิล</td>
                                                        <td><input class="form-control-plaintext text-right text-success" type="text" step="any" name="DiscountSum" id="DiscountSum" value="0.00"/></td>
                                                        <td colspan="2">บาท&nbsp;&nbsp;
                                                            <a href="javascript:void(0);" onclick="GetDocTotal()"><i class="fas fa-calculator fa-fw fa-1x"></i></a>
                                                            <a href="javascript:void(0);" onclick="ClearDisc()" class="text-muted"><i class="fas fa-times fa-fw fa-1x"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-right">ยอดสินค้าหลังหักส่วนลด</td>
                                                        <td><input class="form-control-plaintext text-right" type="text" step="any" name="DocBefVat" id="DocBefVat" readonly/></td>
                                                        <td colspan="2">บาท</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-right">ภาษีมูลค่าเพิ่ม (VAT)</td>
                                                        <td><input class="form-control-plaintext text-right" type="text" step="any" name="VatSum" id="VatSum" readonly/></td>
                                                        <td colspan="2">บาท</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-right text-primary" style="font-weight:bold;">จำนวนเงินรวมสุทธิ</td>
                                                        <td>
                                                            <input class="form-control-plaintext text-right text-primary" style="font-weight: bold;" type="text" step="any" name="DocTotal" id="DocTotal" readonly/>
                                                            <input type="hidden" step="any" name="ProfitTotal" id="ProfitTotal" readonly />
                                                        </td>
                                                        <td colspan="2" class="text-primary" style="font-weight:bold;">บาท</td>
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
                            <div id="order-step3" class="need-validation" data-step="3">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 3: ตรวจสอบความถูกต้องของข้อมูล</h4>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class="table table-borderless order-preview">
                                                <tr>
                                                    <td class="font-weight" width="12.5%">ชื่อลูกค้า</td>
                                                    <td width="37.5%" id="view_CardCode">
                                                    <td class="font-weight" width="12.5%">เลขที่ผู้เสียภาษี</td>
                                                    <td width="37.5%" id="view_LicTradeNum">
                                                </tr>
                                                <tr>
                                                    <td class="font-weight">วันที่ใบสั่งขาย</td>
                                                    <td id="view_DocDate">
                                                    <td class="font-weight">วันที่กำหนดส่ง</td>
                                                    <td id="view_DocDueDate">
                                                </tr>
                                                <tr>
                                                    <td class="font-weight">ประเภทภาษี</td>
                                                    <td id="view_TaxType">
                                                    <td class="font-weight">เงื่อนไขการชำระเงิน</td>
                                                    <td id="view_PaymentTerm">
                                                </tr>
                                                <tr>
                                                    <td class="font-weight align-top">ที่อยู่เปิดบิล</td>
                                                    <td class="align-top" id="view_AddressBillTo">
                                                    <td class="font-weight align-top">ที่อยู่จัดส่ง</td>
                                                    <td class="align-top" id="view_AddressShipTo">
                                                </tr>
                                                <tr>
                                                    <td class="font-weight align-top">เอกสารอ้างอิง</td>
                                                    <td class="font-weight text-danger align-top" id="view_PONo">
                                                    <td rowspan="2" class="font-weight align-top">ข้อมูลการจัดส่ง</td>
                                                    <td rowspan="2" class="align-top" id="view_ShippingType">
                                                </tr>
                                                <tr>
                                                    <td class="font-weight align-top">พนักงานขาย</td>
                                                    <td id="view_SlpName">
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class="table table-bordered order-preview">
                                                <thead>
                                                <tr class="text-center">
                                                    <th width="5%">ลำดับ</th>
                                                    <th>รายการ</th>
                                                    <th colspan="2">จำนวน</th>
                                                    <th width="7.5%">ราคาตั้ง</th>
                                                    <th>ส่วนลด (%)</th>
                                                    <th width="10%">ราคารวม</th>
                                                </tr>
                                                </thead>
                                                <tbody id="view_ItemList"></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" rowspan="5" class="align-top">
                                                            <strong>หมายเหตุ:</strong><br/><span id="view_DocRemark" class="text-danger"></span>
                                                        </td>
                                                        <td class="text-right" colspan="2">ยอดรวมทุกรายการ</td>
                                                        <td class="text-right" id="view_TotalPrice"></td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-right" colspan="2">ส่วนลดท้ายบิล</td>
                                                        <td class="text-right text-success" id="view_DiscountSum"></td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-right" colspan="2">ยอดสินค้าหลังหักส่วนลด</td>
                                                        <td class="text-right" id="view_DocBefVat"></td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-right" colspan="2">ภาษีมูลค่าเพิ่ม (VAT)</td>
                                                        <td class="text-right" id="view_VatSum"></td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-right font-weight text-primary" colspan="2">จำนวนเงินรวมสุทธิ</td>
                                                        <td class="text-right font-weight text-primary" id="view_DocTotal"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div> 
                                    </div>             
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg text-right">
                                        <button type="button" class="btn-prev btn btn-secondary" data-step="3" data-goto="2"><i class="fas fa-chevron-left fa-fw fa-1x"></i> ย้อนกลับ</button>
                                        <button type="button" class="btn btn-info" onclick="SaveDraft(0);" data-step="3" data-goto="4"><i class="fas fa-save fa-fw fa-1x"></i> บันทึกร่าง</button>
                                        <button type="button" class="btn btn-primary" onclick="SaveDraft(1);" data-step="3" data-goto="4"><i class="fas fa-save fa-fw fa-1x"></i> สร้างคำสั่งขายใหม่</button>
                                    </div>
                                </div>
                            </div>
                            <!-- END OF STEP 3 -->

                            <!-- STEP 4 -->
                            <!-- END OF STEP 4 -->
                            
                            <!-- STEP 5 -->
                            <!-- END OF STEP 5 -->
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
                <div class="col-lg-8">
                    <div class="form-group mb-3">
                        <label for="ItemSelect">รหัสสินค้า<span class="text-danger">*</span></label>
                        <select class="form-control selectpicker" name="ItemSelect" id="ItemSelect" data-live-search="true"></select>
                        <small class="text-muted">รหัสสินค้า | ชื่อสินค้า | รหัสบาร์โค้ด | สถานะสินค้า</small>
                        <input type="hidden" name="text_ItemName" id="text_ItemName" readonly />
                        <input type="hidden" name="text_BarCode" id="text_BarCode" readonly />
                        <input type="hidden" name="text_ItemStatus" id="text_ItemStatus" readonly />
                        <input type="hidden" name="text_UnitMsr" id="text_UnitMsr" readonly />
                        <input type="hidden" name="RowID" id="RowID" readonly/>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group mb-3">
                        <label for="ItemQuantity">จำนวน<span class="text-danger">*</span></label>
                        <input type="number" class="form-control text-right" name="ItemQuantity" id="ItemQuantity" step="1" />
                    </div>
                </div>
                <div class="col-lg-1">
                    <div class="form-group mb-3">
                        <label for="btn-calprice">&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-block" id="btn-calprice" name="btn-calprice"><i class="fas fa-calculator fa-fw fa-1x"></i></button>
                    </div>
                </div>
            </div>
            <!-- ประวัติราคา -->
            <div class="row mt-4">
                <div class="col-lg">
                    <h5><i class="fas fa-history fa-fw fa-1x"></i> ประวัติการสั่งซื้อสินค้า <small>(3 รายการล่าสุด)</small></h5>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-lg" id="ItemHistory"></div>
            </div>
            <div class="row mt-4">
                <div class="col-lg">
                    <div class="form-group mb-3">
                        <label for="GrandPrice">ราคาขาย<span class="text-danger">*</span></label>
                        <input type="number" class="form-control text-right" name="GrandPrice" id="GrandPrice" step="any" />
                        <input type="hidden" name="Chk_DefaultPrice" id="Chk_DefaultPrice" readonly />
                        <input type="hidden" name="Chk_CXST" id="Chk_CXST" readonly />
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-group mb-3">
                        <label for="Discount">ส่วนลด<sup class="text-muted">1/2</sup></label>
                        <input type="text" class="form-control text-center" name="Discount" id="Discount"/>
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-group mb-3">
                        <label for="PriceAfDisc">ราคาสุทธิ</label>
                        <input type="number" class="form-control text-right text-success" name="PriceAfDisc" id="PriceAfDisc" step="any" readonly />
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg">
                    <div class="form-group mb-3">
                        <label for="ItemWhse">คลังสินค้า<span class="text-danger">*</span></label>
                        <select class="form-select" name="ItemWhse" id="ItemWhse"></select>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg">
                    <div class="form-check mb-3">
                        <div class="checkbox">
                            <input type="checkbox" class="form-check-input" name="chk_price" id="chk_price" />
                            <label for="chk_price"><i class="fas fa-hand-holding-usd fa-fw fa-1x text-warning"></i> ขออนุมัติราคาพิเศษ</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-check mb-3">
                        <div class="checkbox">
                            <input type="checkbox" class="form-check-input" name="chk_convert" id="chk_convert" />
                            <label for="chk_convert"><i class="fas fa-retweet fa-fw fa-1x text-info"></i> แปลงสินค้า</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg">
                    <div class="form-check mb-3">
                        <div class="checkbox">
                            <input type="checkbox" class="form-check-input" name="chk_backorder" id="chk_backorder" />
                            <label for="chk_backorder"><i class="fas fa-cart-arrow-down fa-fw fa-1x text-danger"></i> Back Order</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg text-muted" style="font-size: 12px;">
                    <p class="font-weight">หมายเหตุ</p>
                    <ol>
                        <li>ในช่องส่วนลด หากต้องการส่วนลดที่เป็น % ใช้เครื่องหมายลบ (-) คั่นส่วนลดระหว่าง STEP ได้เท่านั้น</li>
                        <li>หากต้องการส่วนลดเป็นจำนวนเงินให้ใส่เครื่องหมายดอกจัน (*) ไว้ด้านหน้า</li>
                    </ol>
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
                            <li>ระบบจะนำข้อมูล รหัสสินค้า - บาร์โค้ด - จำนวน - ราคา - ส่วนลด จากรายการบิลเดิมมาเท่านั้น</li>
                            <li>ชื่อสินค้า - สถานะสินค้า - คลังสินค้าจะดึงมาค่าเริ่มต้นจากฐานข้อมูลเท่านั้น</li>
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
                                <input type="text" name="ImportSearchInput" id="ImportSearchInput" class="form-control" placeholder="SOV-XXXX0XXXX / SNV-XXXX3XXXX / SAV-XXXX1XXXX / SBV-XXXX2XXXX" />
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
                <p id="confirm_Wai" class="my-4">บันทึกข้อมูลสำเร็จ</p>
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
                <li class="nav-item" role="presentation">
                    <a href="#SOAddressList" class="btn btn-tabs nav-link" id="SOAddressTab" data-bs-toggle="tab" data-bs-target="#SOAddressList" role="tab" data-tabs="1" aria-controls="SOAddressList" aria-selected="true" style="font-size: 12px;">
                        <i class="fas fa-address-book fa-fw fa-1x"></i> ที่อยู่เปิดบิลและจัดส่ง
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#SOAttachList" class="btn btn-tabs nav-link" id="SOAttachTab" data-bs-toggle="tab" data-bs-target="#SOAttachList" role="tab" data-tabs="1" aria-controls="SOAttachList" aria-selected="true" style="font-size: 12px;">
                        <i class="fas fa-paperclip fa-fw fa-1x"></i> เอกสารแนบ
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#SOApproveList" class="btn btn-tabs nav-link disabled" id="SOApproveTab" data-bs-toggle="tab" data-bs-target="#SOApproveList" role="tab" data-tabs="2" aria-controls="SOApproveList" aria-selected="true" style="font-size: 12px;">
                        <i class="fas fa-tasks fa-fw fa-1x"></i> สถานะการอนุมัติ
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
                <div class="tab-pane" id="SOAddressList" role="tabpanel" aria-labelledby="SOAddressTab">
                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-borderless" style="font-size: 12px;">
                                <tr>
                                    <td class="font-weight align-top" width="17.5%">ที่อยู่เปิดบิล</td>
                                    <td class="align-top" id="soview_BilltoAddress" height="72px"></td>
                                </tr>
                                <tr>
                                    <td class="font-weight align-top">ที่อยู่จัดส่ง</td>
                                    <td class="align-top" id="soview_ShiptoAddress" height="72px"></td>
                                </tr>
                                <tr>
                                    <td class="font-weight align-top">ประเภทขนส่ง</td>
                                    <td class="align-top" id="soview_ShippingType"></td>
                                </tr>
                                <tr>
                                    <td class="font-weight align-top">หมายเหตุขนส่ง</td>
                                    <td class="align-top" id="soview_ShipComment"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="SOAttachList" role="tabpanel" aria-labelledby="SOAttachTab">
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
                                <tbody id="soview_attachlist"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan='4'>
                                            <label for="AttachOrder">แนบไฟล์เพิ่มเติม</label>  <a href="javascript:void(0);" class="text-muted" data-bs-toggle="tooltip" title="รองรับนามสกุลไฟล์รูปภาพ (*.jpg, *.jpeg, *.png) / MS Word (*.doc, *.docx) / MS Excel (*.xls, *.xlsx) / เอกสาร (*.pdf) เท่านั้น"><i class="far fa-question-circle fa-fw fa-lg"></i></a>
                                            <form id="UploadsForm" enctype="multipart/form-data">
                                                <input type="file" class="form-control form-control-sm w-25" name="AttachOrder[]" id="AttachOrder" accept=".jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf" multiple onchange="UploadsFile()"/>
                                            </form>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="SOApproveList" role="tabpanel" aria-labelledby="SOApproveTab">
                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-bordered" style="font-size: 12px;">
                                <thead class="text-center">
                                    <tr>
                                        <th rowspan="2" width="5%">ลำดับ</th>
                                        <th rowspan="2" width="15%">ผู้อนุมัติ</th>
                                        <th colspan="3">เงื่อนไขการอนุมัติ</th>
                                        <th rowspan="2" width="10%">ผลการ<br/>พิจารณา</th>
                                        <th rowspan="2">หมายเหตุ</th>
                                        <th rowspan="2" width="15%">ผู้อนุมัติ</th>
                                        <th width="15%" rowspan="2">วันที่อนุมัติ</th>
                                    </tr>
                                    <tr>
                                        <th width="7.5%">ใบสั่งขาย</th>
                                        <th width="7.5%">เกินวงเงิน</th>
                                        <th width="7.5%">ราคาพิเศษ</th>
                                    </tr>
                                </thead>
                                <tbody id="soview_approvelist"></tbody>
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
                <p id="confirm_body" class="my-4">คุณต้องการยกเลิกใบสั่งขายนี้หรือไม่?</p>
                <div class="row mb-3">
                    <div class="col-12">
                    <?php
                        $ReasonSQL = "SELECT T0.CancelID, T0.Description FROM order_cancelreasons T0 ORDER BY T0.CancelID ASC";
                        $ReasonQRY = MySQLSelectX($ReasonSQL);
                    ?>
                        <select class="form-select" name="CancelReason" id="CancelReason">
                            <option value="" selected>กรุณาเลือกสาเหตุการยกเลิก</option>
                    <?php
                        while($ReasonRST = mysqli_fetch_array($ReasonQRY)) {
                            echo "<option value='".$ReasonRST['CancelID']."'>".$ReasonRST['Description']."</option>";
                        }
                    ?>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-dismiss" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-cancel-confirm" data-docentry="0" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FILE ATTACH -->
<div class="modal fade" id="ModalAttachFile" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-folder-open fa-fw fa-1x"></i> รายการเอกสารแนบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="7.5%">ลำดับ</th>
                                    <th>ชื่อไฟล์</th>
                                    <th width="7.5%"><i class="fas fa-trash fa-fw fa-lg"></i></th>
                                </tr>
                            </thead>
                            <tbody id="FileAttachList"></tbody>
                        </table>
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
        url: "menus/sale/ajax/ajaxsaleorder.php?p=head",//แก้ บรรทัดนี้ทุกครั้ง URL ajax เอง
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
function GETList(filt_year,filt_month,filt_team) {
    $(".overlay").show();
    // var filt_year = $("#filt_year").val();
    // var filt_month = $("#filt_month").val();
    $.ajax({
        url: "menus/sale/ajax/ajaxorderlist.php?p=GetOrderList",
        type: "POST",
        data: { filt_year: filt_year, filt_month: filt_month, filt_team: filt_team },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#OrderTable").html(inval['output']);
            });
        }
    });
    $(".overlay").hide();
}

function GetCardCode(){
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetCardCode",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#CardCode").html(inval["output"]);
            });
            $("#CardCode").selectpicker("refresh");

            <?php if(isset($_GET['CardCode'])) { ?>
                var CardCode = '<?php echo $_GET['CardCode']; ?>';
                $("#CardCode").selectpicker("destroy");
                $("#CardCode").val(CardCode).change().selectpicker();
            <?php } ?>
        }
    });
    $(".overlay").hide();
}

function GetShippingType() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetShipping",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#ShippingType").html(inval["output"]);
            });
        }
    });
    $(".overlay").hide();
}

function GetSlpCode() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetSlpCode",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval){
                $("#SlpCode").html(inval["outputSlp"]);
            });
        }
    });
    $(".overlay").hide();
}

function GetAttach(DocEntry) {
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetAttach",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#FileAttachList").html(inval['AttachList'])
            });
        }
    });
}

function DeleteAttach(DocEntry, AttachID) {
    //console.log(DocEntry, AttachID);
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=DelAttach",
        type: "POST",
        data: { AttachID: AttachID },
        success: function(result) {
            $("#ModalAttachFile").modal("hide");
            $("#confirm_saved").modal("show");
            GetAttach(DocEntry);
        }
    });
}

function CheckForm(StepNow,StepTo) {
    /* 1. สร้างตัวแปรที่จำเป็นต่อฟังก์ชั่น */
    /* 1.1 รับค่าหน้าปัจจุบัน และหน้าต่อไป */
    var Now = StepNow;
    var To  = StepTo;
    var ErrorPoint   = 0;
    var ErrorID      = [];
    var SuccessID    = [];
    var CheckID      = null;

    /* 2. ตรวจสอบว่าค่าที่ส่งมาเป็นค่าว่างหรือไม่ ถ้าใช่ให้ ErrorPoint + 1 ถ้าไม่ ไม่ต้องบวก ErrorPoint */
    /* 2.1 ระบุ ID ที่ต้องการเช็คค่าว่างของหน้านั้นๆ */
    switch(Now) {
        case "1": CheckID = ["CardCode", "AddressBillTo", "AddressShipto", "TaxType", "PaymentTerm", "DocDate", "SlpCode", "DocType"]; break;
        default : CheckID = []; break;
    }
    /* 2.2 ตรวจสอบ CheckID ว่ามี ID ที่ต้องตรวจสอบหรือไม่ หากมี ให้ตรวจสอบ ถ้าไม่มีให้ SKIP */
    if(CheckID.length > 0) {
        for(let i = 0; i < CheckID.length; i++) {
            /* 2.3 ตรวจสอบค่าว่างของ ID ที่กำหนด ถ้าว่างให้ ErrorPoint +1 ถ้าไม่ว่างไม่ต้องบวก ErrorPoint */
            if($("#"+CheckID[i]).val() == null || $("#"+CheckID[i]).val() == "") {
                ErrorPoint = ErrorPoint+1;
                ErrorID.push(CheckID[i]);
            } else {
                SuccessID.push(CheckID[i]);
            }
        }
    }

    /* 3. ตรวจสอบค่า ErrorPoint ถ้ามากกว่า 0 ให้แสดงข้อความแจ้งเตือนข้อผิดพลาด ถ้าไม่ ให้แสดงผลหน้าต่อไป */
    if(ErrorPoint > 0) {
        for(let i = 0; i < ErrorID.length; i++) { $("#"+ErrorID[i]).removeClass("is-valid is-invalid").addClass("is-invalid"); }
        for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        switch(Now) {
            case "1":
                for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
                $("#order-step"+Now).hide();
                $("#order-step"+To).show();
                $("#TaxType").attr("disabled",true);
            break;
            case "2":
                var TotalTR = $("#ItemListData tr").length;
                if(TotalTR == 0) {
                    if (To == "1") {
                        $("#order-step"+Now).hide();
                        $("#order-step"+To).show();
                    }else{
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("กรุณาเพิ่มรายการสินค้าอย่างน้อย 1 รายการ");
                        $("#alert_modal").modal('show');
                    }
                } else {
                    OrderPreview();
                    $("#order-step"+Now).hide();
                    $("#order-step"+To).show();
                }
            break;
            default:
                $("#order-step"+Now).hide();
                $("#order-step"+To).show();
            break;
        }
        
    }
}

function AddItem() {
    $("#AddItem").on("click", function(e) {
        var SelectOption = $("#ItemSelect").html();
        $("#RowID").val(0);
        $("#ItemSelect").empty().selectpicker('destroy');
        $("#ItemSelect").html(SelectOption).selectpicker();
        $("#ItemWhse").empty();
        $("#ItemHistory").html("<p class=\"text-center text-muted\">กรุณาเลือกสินค้า และกรอกจำนวน</p>");
        $("#GrandPrice, #Discount, #PriceAfDisc, #ItemQuantity").val('');
        var HeaderAddItem = "<i class='fas fa-plus fa-fw fa-1x'></i> เพิ่มรายการใหม่";
        $("#HeaderModal").html(HeaderAddItem);
        var btnSave = "<i class='fas fa-plus fa-fw fa-1x'></i> เพิ่ม";
        $("#btn-AddRow").html(btnSave);
        $("#ModalAddItem").modal("show");
        $("input[type='checkbox']").prop('checked',false).removeAttr("disabled");
    });
}

function SearchDoc(DocNum, CardCode) {
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=SearchDoc",
        type: "POST",
        data: { kwd: DocNum, CardCode: CardCode },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
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
                        var GrandPrice = parseFloat(inval[i]['GrandPrice']);
                        if(inval[i]['Discount'] == null) {
                            Discount = '';
                        } else {
                            Discount = inval[i]['Discount'];
                        }
                        var PriceAfDisc = parseFloat(inval[i]['PriceAfDisc']);
                        var LineTotal = parseFloat(inval[i]['LineTotal']);
                        var CXSTTotal = parseFloat(inval[i]['CXSTTotal']);

                        if(inval[i]['SPPrice'] == "Y") {
                            var SPPrice_Icon = "<i class='fas fa-hand-holding-usd fa-fw fa-1x text-warning'></i> ";
                            var SPPrice = "Y";
                        } else {
                            var SPPrice_Icon = "<i class='fas fa-hand-holding-usd fa-fw fa-1x text-muted'></i> ";
                            var SPPrice = "N";
                        }
                        
                        var BackOrder_Icon = "<i class='fas fa-cart-arrow-down fa-fw fa-1x text-muted'></i> ";
                        var Convert_Icon = "<i class='fas fa-retweet fa-fw fa-1x text-muted'></i> ";

                        NewRow += "<tr data-rowid='"+RowID+"'>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemRow_"+RowID+"' id='ItemRow_"+RowID+"' value='"+inval[i]['ItemRow']+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemBarCode_"+RowID+"' id='ItemBarCode_"+RowID+"' value='"+inval[i]['ItemBarCode']+"' readonly></td>"+
                            "<td><input type='text' class='form-control' name='ItemName_"+RowID+"' id='ItemName_"+RowID+"' value='["+inval[i]['ItemStatus']+"] | "+inval[i]['ItemName']+"'></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemWhse_"+RowID+"' id='ItemWhse_"+RowID+"' value='"+inval[i]['ItemWhse']+"' readonly></td>"+
                            "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='ItemQuantity_"+RowID+"' id='ItemQuantity_"+RowID+"' value='"+number_format(inval[i]['ItemQuantity'],0)+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext' name='ItemUnit_"+RowID+"' id='ItemUnit_"+RowID+"' value='"+inval[i]['ItemUnit']+"' readonly></td>"+
                            "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='GrandPrice_"+RowID+"' id='GrandPrice_"+RowID+"' value='"+number_format(GrandPrice,3)+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='Discount_"+RowID+"' id='Discount_"+RowID+"' value='"+Discount+"' readonly></td>"+
                            "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='PriceAfDisc_"+RowID+"' id='PriceAfDisc_"+RowID+"' value='"+number_format(PriceAfDisc,3)+"' readonly></td>"+
                            "<td class='text-right'>"+
                                "<input type='text' class='form-control-plaintext text-right' style='font-weight:bold;' name='LineTotal_"+RowID+"' id='LineTotal_"+RowID+"' value='"+number_format(LineTotal,3)+"' readonly />"+
                                "<input type='hidden' name='CXSTTotal_"+RowID+"' id='CXSTTotal_"+RowID+"' value='"+CXSTTotal+"' readonly / >"+
                            "</td>"+
                            "<td class='text-center'>"+
                                "<span id='SPPriceIcon_"+RowID+"'>"+SPPrice_Icon+"</span>"+
                                "<span id='ConvertIcon_"+RowID+"'>"+Convert_Icon+"</span>"+
                                "<span id='BackOrderIcon_"+RowID+"'>"+BackOrder_Icon+"</span>"+
                            "</td>"+
                            "<td class='text-center'>"+
                                "<input type='hidden' id='input_spprice_"+RowID+"' name='input_spprice_"+RowID+"' value='"+SPPrice+"' readonly/>"+
                                "<input type='hidden' id='input_convert_"+RowID+"' name='input_convert_"+RowID+"' value='N' readonly/>"+
                                "<input type='hidden' id='input_backorder_"+RowID+"' name='input_backorder_"+RowID+"' value='N' readonly/>"+
                                "<button type='button' class='btn btn-secondary btn-sm' onclick='EditItem("+RowID+")'><i class='fas fa-edit fa-fw fa-1x'></i></button> "+
                                "<button type='button' class='btn btn-danger btn-sm' onclick='DeleteItem("+RowID+")'><i class='fas fa-trash fa-fw fa-1x'></i></button>"+
                            "</td>"+
                        "</tr>";
                    }
                    $("#ItemListData").append(NewRow);
                    $("#TotalRow").val(inval['Rows']);
                    GetDocTotal(); 
                }
            });
        }
    });
}


function GetItemProduct() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetItemProduct",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#ItemSelect").html(inval["outputPro"])
            });
            // $("#ItemSelect").selectpicker("refresh");

            $("#ItemSelect").on("change",function(){
                $("#ItemQuantity").focus();
            });

            $("#ItemQuantity").on("keypress",function(e){
                var KeyBoard = e.key;
                if(KeyBoard === "Enter") {
                    var ItemCode = $("#ItemSelect").val();
                    var CardCode = $("#CardCode").val();
                    var Quantity = $(this).val();
                    if(ItemCode == null || CardCode == null || Quantity < 1) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("กรุณาเลือกชื่อลูกค้า รหัสสินค้า และจำนวนสินค้าให้ครบถ้วน");
                        $("#alert_modal").modal('show');
                    } else {
                        GetItemDetail(CardCode,ItemCode,Quantity);
                    }
                }
            });

            $("#btn-calprice").on("click",function(e){
                e.preventDefault();
                var ItemCode = $("#ItemSelect").val();
                var CardCode = $("#CardCode").val();
                var Quantity = $("#ItemQuantity").val();
                if(ItemCode == null || CardCode == null || Quantity < 1) {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("กรุณาเลือกชื่อลูกค้า รหัสสินค้า และจำนวนสินค้าให้ครบถ้วน");
                    $("#alert_modal").modal('show');
                } else {
                    GetItemDetail(CardCode,ItemCode,Quantity);
                }
            });
        }
    });
    $(".overlay").hide();
}

function GetItemDetail(CardCode,ItemCode,Quantity) {
    $(".overlay").show();
    $("#ItemHistory, #ItemWhse").empty();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetItemDetail",
        type: 'POST',
        data: { CardCode: CardCode, ItemCode: ItemCode, Quantity: Quantity },
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval){
                $("#ItemHistory").html(inval['History']);
                var uClass = '<?php echo $_SESSION['uClass']; ?>';
                switch(uClass) {
                    case "0":
                    case "9":
                    case "18":
                    case "19":
                    case "20": var ValDisabled = ["00","ACC","AGT","IMAX","JSI","KB9","KN","KS","KSM","KTW","NST","PLA","PU","RST","RTR","SY","TC","TT-ขาย","VRK","W",,"YEE","YMT"]; break;
                    default  : var ValDisabled = []; break;
                }
                // console.log(ValDisabled.length);
                $("#ItemWhse").html(inval['Warehouse']);
                if(ValDisabled.length > 0) {
                    for(var i = 0; i < ValDisabled.length; i++) {
                        $("select#ItemWhse option[value='"+ValDisabled[i]+"']").prop("disabled","disabled");
                    }
                }
                $("#ItemWhse").val(inval['DefWhse']).change();
                var TaxType = $("#TaxType").val();
                var DefaultPrice = 0;
                if(TaxType == "S07") {
                    DefaultPrice = (inval['DefaultPrice']/1.07).toFixed(3);
                } else {
                    DefaultPrice = inval['DefaultPrice'];
                }
                $("#GrandPrice, #PriceAfDisc, #Chk_DefaultPrice").val(DefaultPrice);
                $("#chk_price").prop('checked',false).removeAttr("readonly disabled");
                $("#GrandPrice").focus();
                $("#Chk_CXST").val(inval['CXST']);
            });
        }
    });
    $(".overlay").hide();
}

function AddNewRow() {
    // 1.เอาค่า RowID ที่เรากำหนดค่าไว้ว่าเท่ากับ 0 ตั้งแต่กดเพิ่มรายการใหม่ (เมื่อกดจากปุ่มเพิ่มรายการใหม่) หรือ ค่าแถวที่ต้องการแก้ไข (เมื่อกดจากปุ่มแก้ไข) 
    var EditRow = $("#RowID").val();
    // 2.เก็บค่าที่มีใน ID เข้าไว้ในตัวแปร
    var ItemCode     = $("#ItemSelect").val();
    var ItemName     = $("#text_ItemName").val();
    var BarCode      = $("#text_BarCode").val();
    var UnitMsr      = $("#text_UnitMsr").val();
    var GrandPrice   = parseFloat($("#GrandPrice").val());
    var Discount     = $("#Discount").val();
    var PriceAfDisc  = parseFloat($("#PriceAfDisc").val());
    var ItemQuantity = $("#ItemQuantity").val();
    var ItemWhse     = $("#ItemWhse").val();
    var ItemCXST     = $("#Chk_CXST").val();
    var SPPrice      = "";
    var Convert      = "";
    var BackOrder    = "";
    var SPPrice_Icon      = "";
    var Convert_Icon      = "";
    var BackOrder_Icon    = "";
    // 3.ถ้า text_ItemStatus เป็นค่าว่าง ไม่ต้องเพิ่มค่าให้กับ ItemStatus ถ้า text_ItemStatus ไม่ใช่ค่าว่าง ให้เพิ่มค่าให้กับ text_ItemStatus ตามที่กำหนดไว้
    if($("#text_ItemStatus").val().length == 0) {
        var ItemStatus = null;
    } else {
        var ItemStatus = "["+$("#text_ItemStatus").val()+"] |";
    }

    // 4.ถ้า ItemCode, GrandPrice, ItemQuantity, ItemWhse เป็นค่าว่างให้แจ้งเตือน error
    if (ItemCode == null || GrandPrice.length == 0 || (ItemQuantity.length == 0 || ItemQuantity < 1) || ItemWhse == null){
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    }else{
        /* เช็คราคาพิเศษ (%) */
        if($("#chk_price").is(":checked")) {
            SPPrice_Icon = "<i class='fas fa-hand-holding-usd fa-fw fa-1x text-warning'></i> ";
            SPPrice      = "Y";
        } else {
            SPPrice_Icon = "<i class='fas fa-hand-holding-usd fa-fw fa-1x text-muted'></i> ";
            SPPrice      = "N";
        }
        if($("#chk_convert").is(":checked")) {
            Convert_Icon = "<i class='fas fa-retweet fa-fw fa-1x text-info'></i> ";
            Convert      = "Y";
        } else {
            Convert_Icon = "<i class='fas fa-retweet fa-fw fa-1x text-muted'></i> ";
            Convert      = "N";
        }
        if($("#chk_backorder").is(":checked")) {
            BackOrder_Icon = "<i class='fas fa-cart-arrow-down fa-fw fa-1x text-danger'></i>";
            BackOrder      = "Y";
        } else {
            BackOrder_Icon = "<i class='fas fa-cart-arrow-down fa-fw fa-1x text-muted'></i>";
            BackOrder      = "N";
        }
        // 4.1 ถ้า ItemCode, GrandPrice, ItemQuantity, ItemWhse ไม่ใชาค่าว่างให้ตรวจสอบเงื่อนไขค่าตัวแปร EditRow 
        if (EditRow == "0") {
            // 4.1.1 ถ้าเงื่อนไขเป็นจริง แปลงค่าสตริงจำนวนบรรทัดทั้งหมด (TotalRow) แปลงให้เป็นตัวเลขแล้วเก็บค่าไว้ในตัวแปร LastRow แล้วนำค่า LastRow+1 แล้วเก็บไว้ในตัวแปร RowID
            var LastRow = parseInt($("#TotalRow").val());
            var RowID   = LastRow+1;
            // 4.1.2 ผลคูณจำนวนสินค้า*ราคาสินค้า
            var CXSTTotal = ItemQuantity*conData(ItemCXST);
            var LineTotal = ItemQuantity*PriceAfDisc;
            // 4.1.3 สร้างตัวแปร เก็บ โครงสร้าง HTML สำหรับบรรทัดที่ต้องการจะเพิ่ม โดยนำค่าที่ได้กลับไปหยอดใน input ที่เกี่ยวข้อง เพื่อให้สามารถนำข้อมูลกลับไปใช้ต่อในการแก้ไขรายการได้

            var NewRow = "<tr data-rowid='"+RowID+"'>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemRow_"+RowID+"' id='ItemRow_"+RowID+"' value='"+ItemCode+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemBarCode_"+RowID+"' id='ItemBarCode_"+RowID+"' value='"+BarCode+"' readonly></td>"+
                            "<td><input type='text' class='form-control' name='ItemName_"+RowID+"' id='ItemName_"+RowID+"' value='"+ItemStatus+" "+ItemName+"'></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='ItemWhse_"+RowID+"' id='ItemWhse_"+RowID+"' value='"+ItemWhse+"' readonly></td>"+
                            "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='ItemQuantity_"+RowID+"' id='ItemQuantity_"+RowID+"' value='"+number_format(ItemQuantity,0)+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext' name='ItemUnit_"+RowID+"' id='ItemUnit_"+RowID+"' value='"+UnitMsr+"' readonly></td>"+
                            "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='GrandPrice_"+RowID+"' id='GrandPrice_"+RowID+"' value='"+number_format(GrandPrice,3)+"' readonly></td>"+
                            "<td class='text-center'><input type='text' class='form-control-plaintext text-center' name='Discount_"+RowID+"' id='Discount_"+RowID+"' value='"+Discount+"' readonly></td>"+
                            "<td class='text-right'><input type='text' class='form-control-plaintext text-right' name='PriceAfDisc_"+RowID+"' id='PriceAfDisc_"+RowID+"' value='"+number_format(PriceAfDisc,3)+"' readonly></td>"+
                            "<td class='text-right'>"+
                                "<input type='text' class='form-control-plaintext text-right' style='font-weight:bold;' name='LineTotal_"+RowID+"' id='LineTotal_"+RowID+"' value='"+number_format(LineTotal,3)+"' readonly />"+
                                "<input type='hidden' name='CXSTTotal_"+RowID+"' id='CXSTTotal_"+RowID+"' value='"+CXSTTotal+"' readonly / >"+
                            "</td>"+
                            "<td class='text-center'>"+
                                "<span id='SPPriceIcon_"+RowID+"'>"+SPPrice_Icon+"</span>"+
                                "<span id='ConvertIcon_"+RowID+"'>"+Convert_Icon+"</span>"+
                                "<span id='BackOrderIcon_"+RowID+"'>"+BackOrder_Icon+"</span>"+
                            "</td>"+
                            "<td class='text-center'>"+
                                "<input type='hidden' id='input_spprice_"+RowID+"' name='input_spprice_"+RowID+"' value='"+SPPrice+"' readonly/>"+
                                "<input type='hidden' id='input_convert_"+RowID+"' name='input_convert_"+RowID+"' value='"+Convert+"' readonly/>"+
                                "<input type='hidden' id='input_backorder_"+RowID+"' name='input_backorder_"+RowID+"' value='"+BackOrder+"' readonly/>"+
                                "<button type='button' class='btn btn-secondary btn-sm' onclick='EditItem("+RowID+")'><i class='fas fa-edit fa-fw fa-1x'></i></button> "+
                                "<button type='button' class='btn btn-danger btn-sm' onclick='DeleteItem("+RowID+")'><i class='fas fa-trash fa-fw fa-1x'></i></button>"+
                            "</td>"+
                        "</tr>";
            // 4.1.4 นำค่า RowID ที่ได้จาก 4.1.1 มาหยอดกลับเข้า input ที่เก็บจำนวนบรรทัดทั้งหมด (TotalRow)
            $("#TotalRow").val(RowID);
            // 4.1.5 นำค่า NewRow ที่ได้จาก 4.1.3 ไปแสดงที่ ID:ItemListData โดยใช้ append ในการแสดงข้อมูลต่อๆกัน
            $("#ItemListData").append(NewRow);
        }else{
            // 4.2 ถ้าเงื่อนไขเป็นเท็จ ให้ update เก็บค่าเข้าไปใน ID ต่างๆ ตาม Row ที่เลือกแก้ไข
            var CXSTTotal = ItemQuantity*conData(ItemCXST);
            var LineTotal = ItemQuantity*PriceAfDisc;
            $("#ItemRow_"+EditRow).val(ItemCode);
            $("#ItemBarCode_"+EditRow).val(BarCode);
            $("#ItemName_"+EditRow).val(ItemStatus+" "+ItemName);
            $("#ItemWhse_"+EditRow).val(ItemWhse);
            $("#ItemQuantity_"+EditRow).val(number_format(ItemQuantity,0));
            $("#ItemUnit_"+EditRow).val(UnitMsr);
            $("#GrandPrice_"+EditRow).val(number_format(GrandPrice,3));
            $("#Discount_"+EditRow).val(Discount);
            $("#PriceAfDisc_"+EditRow).val(number_format(PriceAfDisc,3));
            $("#LineTotal_"+EditRow).val(number_format(LineTotal,3));
            $("#CXSTTotal_"+EditRow).val(CXSTTotal.toFixed(3));
            $("#SPPriceIcon_"+EditRow).html(SPPrice_Icon);
            $("#ConvertIcon_"+EditRow).html(Convert_Icon);
            $("#BackOrderIcon_"+EditRow).html(BackOrder_Icon);
            $("#input_spprice_"+EditRow).val(SPPrice);
            $("#input_convert_"+EditRow).val(Convert);
            $("#input_backorder_"+EditRow).val(BackOrder);
        }
        // 4.2 ปิด modal
        $("#ModalAddItem").modal("hide");
    }
    GetDocTotal();
}

function EditItem (row) {
    /* รับค่าจาก Input ในแถวที่รับมาจาก row */
    var ItemCode = $("#ItemRow_"+row).val();
    var ItemWhse = $("#ItemWhse_"+row).val();
    var ItemQuantity = strtoNumber($("#ItemQuantity_"+row).val());
    var GrandPrice = strtoNumber($("#GrandPrice_"+row).val());
    var Discount = $("#Discount_"+row).val();
    var PriceAfDisc = strtoNumber($("#PriceAfDisc_"+row).val());
    var ChkPrice = $("#input_spprice_"+row).val();
    var ChkConvert = $("#input_convert_"+row).val();
    var ChkBackOrder = $("#input_backorder_"+row).val();

    // เก็บค่า row ไว้ใน ID:RowID
    $("#RowID").val(row);

    // เอาค่าเก็บใน ID เพื่อนำไปแสดงที่ modal
    $("#ItemSelect").selectpicker('destroy');
    $("#ItemSelect").val(ItemCode).change();
    $("#ItemSelect").selectpicker();

    $("#ItemQuantity").val(ItemQuantity);
    GetItemDetail($("#CardCode").val(),ItemCode,ItemQuantity);

    
    setTimeout(function() {
        var Chk_DP = $("#Chk_DefaultPrice").val();
        $("#GrandPrice").val(GrandPrice);
        $("#Discount").val(Discount);
        $("#PriceAfDisc").val(PriceAfDisc);
        $("#ItemWhse").val(ItemWhse).change();
        Chk_SPPrice(PriceAfDisc, Chk_DP);

        if(ChkPrice == 'Y') {
            $("#chk_price").prop('checked',true).attr('disabled',true);
        } else {
            $("#chk_price").prop('checked',false).removeAttr('disabled');
        }
        if(ChkConvert == 'Y') {
            $("#chk_convert").prop('checked',true);
        } else {
            $("#chk_convert").prop('checked',false);
        }
        if(ChkBackOrder == 'Y') {
            $("#chk_backorder").prop('checked',true);
        } else {
            $("#chk_backorder").prop('checked',false);
        }
    },500);

    // แสดง modal
    var HeadEdit = "<i class='far fa-edit fa-fw fa-1x'></i> แก้ไขข้อมูลรายการสินค้า";
    var btnSave = "<i class='far fa-save fa-fw fa-1x'></i> บันทึก";
    $("#HeaderModal").html(HeadEdit);
    $("#btn-AddRow").html(btnSave);
    $("#ModalAddItem").modal("show");
}

function DeleteItem (row) {
    // แสดง modal ยืนยันการลบข้อมูล
    $("#confirm_delete").modal("show");
    // นำค่าจาก row เก็บใน Attribut: data-rowid ของ ID:btn-del-confirm
    $("#btn-del-confirm").attr("data-rowid",row);

    // เมื่อมีการคลิก ปุ่ม ID:btn-del-confirm ให้เอาค่าแอททริบิว data-rowid เก็บไว้ในตัวแปร RowID แล้วลบแถวของตารางตาม ID ของของแถวนั้นๆ
    $("#btn-del-confirm").on("click",function(e){
        var RowID = $(this).attr("data-rowid");
        $("#ItemListData tr[data-rowid='"+RowID+"']").remove();
        GetDocTotal();
    });
}

function GetDocTotal() {
    var TotalRow = $("#TotalRow").val();
    var DiscountSum = $("#DiscountSum").val();
    var TotalPrice = 0.000;
    var TotalCxst  = 0.000;
    var TaxType = $("#TaxType").val();
    var VatSum = 0.000;
    for (var i = 1; i <= TotalRow; i++){
        if($("#ItemRow_"+[i]).val() != undefined) {
            // console.log(i+"|"+$("#ItemRow_"+[i]).val());
            var LineTotal = strtoNumber($("#LineTotal_"+[i]).val());
            var CxstTotal = strtoNumber($("#CXSTTotal_"+[i]).val());
            // console.log(CxstTotal);
            if(isNaN(LineTotal) == false && isNaN(CxstTotal) == false) {
                TotalPrice = parseFloat(TotalPrice)+parseFloat(LineTotal);
                TotalCxst  = parseFloat(TotalCxst)+parseFloat(CxstTotal);
            } else {
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("ไม่สามารถประมวลผลข้อมูลได้ กรุณาติดต่อผู้ดูแลระบบ");
                $("#alert_modal").modal('show');
            }
        }
    }

    if(parseFloat(DiscountSum) > TotalPrice) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("ไม่สามารถกรอกส่วนลดให้มากกว่ายอดรวมทุกรายการได้");
        $("#alert_modal").modal('show');
        var DiscountSum = 0.00;
        $("#DiscountSum").val(DiscountSum.toFixed(3));
        GetDocTotal();
    } else{
        if (isNaN(DiscountSum) == false){
            DiscountSum = parseFloat(TotalPrice)-parseFloat(DiscountSum);
        }
        switch(TaxType) {
            case "S07":
                var DocBefVAT = parseFloat(DiscountSum);
                var VatSum    = parseFloat(DocBefVAT)*0.07;
                var DocTotal  = parseFloat(DocBefVAT)+parseFloat(VatSum);
            break; 
            case "S00":
                var DocBefVAT = parseFloat(DiscountSum)/1.07;
                var VatSum    = parseFloat(DocBefVAT)*0.07;
                var DocTotal  = parseFloat(DocBefVAT)+parseFloat(VatSum);
            break; 
            case "SNV":
                var DocBefVAT = parseFloat(DiscountSum);
                var VatSum    = 0;
                var DocTotal  = DocBefVAT;
            break; 
        }
        console.log(DocBefVAT);
        // console.log("SubTotal: "+DiscountSum);
        // console.log("CostTotal: "+TotalCxst);
        // console.log("GP: "+(DiscountSum-TotalCxst));
        /* รวมทั้งหมด */
        $("#TotalPrice").val(number_format(TotalPrice,3));
        /* ราคาหลังหักส่วนลด */
        $("#DocBefVat").val(number_format(DocBefVAT,3));
        /* ภาษี */
        $("#VatSum").val(number_format(VatSum,3));
        /* รวมทั้งหมด */
        $("#DocTotal").val(number_format(DocTotal,3));
        $("#ProfitTotal").val(number_format((DocBefVAT-TotalCxst),3));
    }
}

function ClearDisc() {
    var DiscountSum = 0.00;
    $("#DiscountSum").val(DiscountSum.toFixed(3));
    GetDocTotal();
}

function OrderPreview() {
    $("#view_ItemList").empty();
    /* Order Header */  
    var view_CardCode      = $("#CardCode option:selected").text();
    var [Y_DocDate, M_DocDate, D_DocDate] = $("#DocDate").val().split('-');
    var view_DocDate       = ""+D_DocDate+"/"+M_DocDate+"/"+Y_DocDate+"";
    var [Y_DocDueDate, M_DocDueDate, D_DocDueDate] = $("#DocDueDate").val().split('-');
    var view_DocDueDate    = ""+D_DocDueDate+"/"+M_DocDueDate+"/"+Y_DocDueDate+"";
    var view_AddressShipTo = $("#AddressShipto option:selected").text();
    var view_AddressBillTo = $("#AddressBillTo option:selected").text();
    var view_LicTradeNum   = $("#LicTradeNum").val();
    var view_SlpName       = $("#SlpCode option:selected").text();
    var view_PONo          = $("#U_PONo").val();
    var view_TaxType       = $("#TaxType option:selected").text();
    var view_PaymentTerm   = $("#PaymentTerm option:selected").text();
    var view_ShippingType  = $("#ShippingType option:selected").text()+"<br/>("+$("#ShipCostType option:selected").text()+")<br/>[หมายเหตุ: "+$("#ShipComment").val()+"]";
    $("#view_CardCode").html(view_CardCode);
    $("#view_DocDate").html(view_DocDate);
    $("#view_DocDueDate").html(view_DocDueDate);
    $("#view_AddressShipTo").html(view_AddressShipTo.replace(" (ค่าเริ่มต้น)",""));
    $("#view_AddressBillTo").html(view_AddressBillTo.replace(" (ค่าเริ่มต้น)",""));
    $("#view_LicTradeNum").html(view_LicTradeNum);
    $("#view_SlpName").html(view_SlpName);
    $("#view_PONo").html(view_PONo);
    $("#view_TaxType").html(view_TaxType);
    $("#view_PaymentTerm").html(view_PaymentTerm);
    $("#view_ShippingType").html(view_ShippingType);

    var TotalRow = $("#TotalRow").val();
    var No = 1;
    for (var i = 1; i <= TotalRow; i++) {
        var ItemRow_       = $("#ItemRow_"+[i]).val();
        var ItemBarCode_   = $("#ItemBarCode_"+[i]).val();
        var ItemWhse_      = $("#ItemWhse_"+[i]).val();
        var ItemName_      = $("#ItemName_"+[i]).val();
        var ItemQuantity_  = $("#ItemQuantity_"+[i]).val();
        var ItemUnit_      = $("#ItemUnit_"+[i]).val();
        var GrandPrice_    = $("#GrandPrice_"+[i]).val();
        var Discount_      = $("#Discount_"+[i]).val();
        var LineTotal_     = $("#LineTotal_"+[i]).val();
        if (ItemRow_ != undefined) {
            var ItemRow = "<tr>"+
                              "<td class='text-center'>"+No+"</td>"+
                              "<td>"+ItemRow_+" "+ItemBarCode_+" "+ItemWhse_+" "+ItemName_+"</td>"+
                              "<td width='5%' class='text-right'>"+ItemQuantity_+"</td>"+
                              "<td width='5%'>"+ItemUnit_+"</td>"+
                              "<td class='text-right'>"+GrandPrice_+"</td>"+
                              "<td class='text-center'>"+Discount_+"</td>"+
                              "<td class='text-right'>"+LineTotal_+"</td>"+
                          "</tr>"
            $("#view_ItemList").append(ItemRow);
            No++;
        } 
    }
    var view_DocRemark = $("#DocRemark").val();
    var view_TotalPrice = $("#TotalPrice").val();
    var view_DiscountSum = $("#DiscountSum").val();
    var view_DocBefVat = $("#DocBefVat").val();
    var view_VatSum = $("#VatSum").val();
    var view_DocTotal = $("#DocTotal").val();
    $("#view_DocRemark").html(view_DocRemark);
    $("#view_TotalPrice").html(view_TotalPrice);
    $("#view_DiscountSum").html(view_DiscountSum);
    $("#view_DocBefVat").html(view_DocBefVat);
    $("#view_VatSum").html(view_VatSum);
    $("#view_DocTotal").html(view_DocTotal);
}

function Chk_SPPrice(TotalPrice, DefaultPrice) {
    var PriceCheck = parseFloat(TotalPrice);
    var PriceDefault = parseFloat(DefaultPrice);
    if(PriceCheck < PriceDefault || PriceDefault == 0) {
        $("#chk_price").prop('checked',true).attr({ readonly: true, disabled: true});
    } else {
        $("#chk_price").prop('checked',false).removeAttr("readonly disabled");
    }
}

function conData(cha) {
    var loop = cha.length;
    var recha = "";
    var NewX  = "";
    for(i=0;i<loop;i++) {
        switch(cha[i]) {
            case 'h' :
                NewX = '0';
            break;
            case 'q' :
                NewX = '1';
            break;
            case 'j' :
                NewX = '2';
            break;
            case 'm' :
                NewX = '3';
            break;
            case 's' :
                NewX = '4';
            break;
            case 'R' :
                NewX = '5';
            break;
            case 'P' :
                NewX = '6';
            break;
            case 'T' :
                NewX = '7';
            break;
            case 'U' :
                NewX = '8';
            break;
            case 'w' :
                NewX = '9';
            break;
            case 'x' :
                NewX = '.';
            break;
        }
        recha += NewX;
    }
    return parseFloat(recha).toFixed(3);
}

function SaveDraft(SaveType) {
    /* Checked Order DocEntry */
    $(".overlay").show();
    $("#CardCode").selectpicker("destroy");
    $("#CardCode, #TaxType, #PaymentTerm").attr("disabled",false);
    var OrderForm = new FormData($("#OrderForm")[0]);
    OrderForm.append('SaveType',SaveType);
    // var OrderFile = $("#OrderAttach").prop("files")[0];
    // OrderForm.append('files',OrderFile);

    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=SaveDraft",
        type: 'POST',
        dataType: 'text',
        cache: false,
        processData: false,
        contentType: false,
        data: OrderForm,
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                switch (inval['Status']){
                    /*
                    case 'N' :
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html(inval['errMsg']);
                        $("#alert_modal").modal('show');
                        break;
                        */
                    case 'F' :
                        CallORDR(inval['errMsg']);
                        break;
                    default : // รออนุมัติ
                        $("#confirm_Wai").html(inval['errMsg']);
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                        break;

                }

            });
            
        }
    });
}
function CallORDR(wai){
    $(".overlay").show();
    $.ajax({
        url: "../core/ORDR.php?x="+wai,
        type: 'POST',
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                switch (inval['Status']){
                    case 'N' :
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html(inval['errMsg']);
                        $("#alert_modal").modal('show');
                        break;
                    default : // รออนุมัติ
                        $("#confirm_Wai").html(inval['errMsg']);
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                        break;
                }

            });
        }
    });
}

function EditSO(DocEntry) {
    var DocEntry = DocEntry;
    $(".modal").modal("hide");
    $("#OrderEntry").val(DocEntry);

    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=EditSO",
        type: 'POST',
        data: { DocEntry: DocEntry },
        success: function(result) {
            $(".nav-tabs a[href='#NewOrder']").tab("show");

            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#CardCode").selectpicker("destroy");
                $("#CardCode").val(inval['CardCode']).change();
                $("#CardCode, #TaxType, #PaymentTerm").attr("disabled",true);
                $("#CardCode").selectpicker();
                setTimeout(function() {
                    $("#LicTradeNum").val(inval['LicTradeNum']);
                    $("#AddressBillTo").val(inval['BilltoCode']).change();
                    $("#AddressShipto").val(inval['ShiptoCode']).change();
                    $("#TaxType").val(inval['TaxType']).change();
                    $("#PaymentTerm").val(inval['Payment_Cond']).change();
                    $("#DocDate").val(inval['DocDate']);
                    $("#DocDueDate").val(inval['DocDueDate']);
                    $("#SlpCode").val(inval['SlpCode']).change();
                    $("#U_PONo").val(inval['U_PONo']);
                    $("#ShippingType").selectpicker("destroy");
                    $("#ShippingType").val(inval['ShippingType']).change();
                    $("#ShippingType").selectpicker();
                    $("#ShipCostType").val(inval['ShipCostType']).change();
                    $("#ShipComment").val(inval['ShipComment']);

                    $("#FileAttachList").html(inval['AttachList'])
                    $("#DocRemark").val(inval['Comments']);

                    SearchDoc(inval['DocNum'],inval['CardCode']);
                }, 500);
            });
        }
    });
}

function PrintSO(docentry,intstatus) {
    var DocEntry = docentry;
    var DocType  = intstatus;

    switch(DocType) {
        case 3:
        case 5:
            var PrintType = 'o';
        break;
        default: var PrintType = 'q';
        break;
    }

    window.open ('menus/sale/print/printso.php?docety='+DocEntry+'&type='+PrintType,'_blank');
}

function CancelSO(DocEntry) {
    var DocEntry = DocEntry;
    $(".modal").modal("hide");
    $("#confirm_cancel").modal("show");

    $("#btn-cancel-confirm").on("click", function(e) {
        e.preventDefault();
        var Reasons = $("#CancelReason").val();
        if(Reasons == "") {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณาระบุสาเหตุการยกเลิกเอกสาร");
            $("#alert_modal").modal('show');
        } else {
            $.ajax({
                url: 'menus/sale/ajax/ajaxorderlist.php?p=CancelSO',
                type: 'POST',
                data: { DocEntry: DocEntry, Reasons: Reasons },
                success: function(result) {
                    $("#confirm_saved").modal("show");
                    $("#btn-save-reload").on("click", function(e){
                        e.preventDefault();
                        window.location.reload();
                    });
                }
            })
        }
    });

    $("#btn-cancel-dismiss").on("click", function(e) {
        e.preventDefault();
        location.reload();
    });
}

function ExportSO(DocEntry) {
    $(".overlay").show();
    // $("#ShowData").html("<div class=\"text-center text-muted\" style=\"padding: 2rem;\"><i class=\"fas fa-spinner fa-pulse fa-4x\"></i><br/><small>Generating Report...</small></div>");
    $.ajax({
    //   url: 'menus/sale/ajax/ajaxExportOrder.php',
      url: "../core/ORDR.php?x="+DocEntry,
      type: 'POST',
    //   data: { DocEntry: DocEntry},
      success: function(result) {
        // $("#ShowData").html("<div class=\"text-center text-success\" style=\"padding: 2rem;\"><i class=\"fas fa-check fa-fw fa-4x\"></i><br/><small>Generate Complete...</small></div>");
        // window.open('../../FileExport/SaleOrder/'+data, '_blank');
        // $("#ShowData").hide();
        var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                switch (inval['Status']){
                    /*
                    case 'N' :
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html(inval['errMsg']);
                        $("#alert_modal").modal('show');
                        break;
                        */
                    case 'F' :
                        CallORDR(inval['errMsg']);
                        break;
                    default : // รออนุมัติ
                        $("#confirm_Wai").html(inval['errMsg']);
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            // window.location.reload();
                        });
                        break;
                }

            });
      }
    });

}

function PreviewSO(DocEntry,int_status) {
    $("#SOApproveTab").removeClass("disabled");
    $(".nav-tabs a[href='#SOItemList']").tab("show");
    switch(int_status) {
        case 0:
        case 1:
            $("#SOApproveTab").addClass("disabled");
        break;
        default: $("#SOApproveTab").removeClass("disabled"); break;
    }
    $.ajax({
        url: 'menus/sale/ajax/ajaxorderlist.php?p=SOPreview',
        type: 'POST',
        data: { DocEntry: DocEntry, int_status: int_status },
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

                $("#preview_footer").html(inval['footer']);
            });
        }
    });
    $("#ModalPreview").modal("show");
}

function UploadsFile(DocEntry) {
    var DocEntry = $("#soview_DocEntry").val();
    var UploadsForm = new FormData($("#UploadsForm")[0]);
    UploadsForm.append('DocEntry',DocEntry);
    $.ajax({
        url: "menus/general/ajax/ajaxapp_order.php?a=UploadsFile",
        type: "POST",
        dataType: 'text',
        cache: false,
        processData: false,
        contentType: false,
        data: UploadsForm,
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#AttachOrder").val("");
                var int_status = $("#soview_IntStatus").val();
                PreviewSO(DocEntry,int_status);
                $(".nav-tabs a[href='#SOAttachList']").tab("show");
            })
        }
    })
}

$(document).ready(function(){
    CallHead();
    GetCardCode();
    GetSlpCode();
    GetShippingType()
    AddItem();
    GetItemProduct();
    var filt_year = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team = $("#filt_team").val();
    GETList(filt_year,filt_month,filt_team);

    var tooltipTriggerList = [].slice.call($('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    <?php if(isset($_GET['CardCode'])) { ?>
        $(".nav-tabs a[href='#NewOrder']").tab("show");
    <?php } ?>
});

// $("#order-step1, #order-step2").hide();
$("#order-step2, #order-step3, #order-step4, #order-step5").hide();

/* เมื่อเปลี่ยน TAB หัวข้อ */


$(".btn-tabs").on("click",function(e){
    e.preventDefault();
    var tabno = $(this).attr("data-tabs");
    if(tabno == "0") {
        var filt_year = $("#filt_year").val();
        var filt_month = $("#filt_month").val();
        var filt_team = $("#filt_team").val();
        GETList(filt_year,filt_month,filt_team);
    } else {
        $("#form_NewOrder").trigger("reset");
        $("#form_NewOrder").trigger("change");
    }
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
    $("#OrderTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

/* เมื่อกดปุ่ม ย้อนกลับ / ต่อไป */
$(".btn-prev, .btn-next").on("click", function(e) {
    e.preventDefault();
    var StepNow  = $(this).attr("data-step");
    var StepGoto = $(this).attr("data-goto");
    CheckForm(StepNow,StepGoto);
});

/* Step 1 เมื่อเลือกชื่อร้านค้า */
$("#CardCode").on("change", function() {
    /* 1. สร้างตัวแปรเก็บค่า Value จาก #CardCode */
    var CardCode = $(this).val();

    /* 2. Reset ข้อมูล Option ใน Select ที่อยู่ทั้งหมด */
    $("#AddressBillTo, #AddressShipto").empty();
    $("#AddressBillTo").html("<option value=''>กรุณาเลือกที่อยู่เปิดบิล</option>");
    $("#AddressShipto").html("<option value=''>กรุณาเลือกที่อยู่จัดส่ง</option>");

    /* 3. นำ Value ที่ได้จากข้อ 1 ไป Ajax */
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetAddress",
        type: "POST",
        data: {CardCode : CardCode},
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                /* 4. เอาข้อมูลมาหยอดใน ที่อยู่จัดเปิดบิล และที่อยู่จัดส่ง */
                setTimeout(function() {
                    $("#AddressBillTo").html(inval["outputB"]);
                    $("#AddressShipto").html(inval["outputS"]);
                    $("#AddressBillTo").val(inval["outputBD"]).change();
                    $("#AddressShipto").val(inval["outputSD"]).change();
                },500);
                /* 5. หยอดข้อมูลพนักงานขาย */
                var SaleCode = inval["outputSaleCode"];
                var ShippingType = inval["outputShipping"];       
                $("#SlpCode").val(SaleCode).change();
                $("#ShippingType").val(ShippingType).change(); 
                $("#LicTradeNum").val(inval["outputTaxID"]);  
            });
        }
    });
    /* 6. เปิด Input ที่เหลือให้กรอกได้ */
    $("#TaxType").val("S07").change();
    $("#PaymentTerm").val("CR").change();
    $("#ShipCostType").val("PRE").change();

    $("#LicTradeNum, #AddressBillTo, #AddressShipto, #TaxType, #PaymentTerm, #DocDate, #DocDueDate, #U_PONo, #OrderAttach, #SlpCode, #ShippingType, #ShipCostType, #ShipComment").removeAttr("disabled");
    // /* ใช้ชั่วคราว */ $("#AddressBillTo, #AddressShipto, #DocDate, #DocDueDate, #U_PONo, #OrderAttach, #SlpCode, #ShippingType, #ShipCostType").removeAttr("disabled");
    setTimeout(function() { $("#ShippingType").selectpicker(); },500);
});

$("#AddressBillTo").on("change", function() {
    var inputBillto = $("#AddressBillTo option:selected").text();
    $("#AddressBillTo_text").val(inputBillto);
});

$("#AddressShipto").on("change", function() {
    var inputShipto = $("#AddressShipto option:selected").text();
    $("#AddressShipto_text").val(inputShipto);
});

$("#SlpCode").on("change", function(){
    var inputSlpName = $("#SlpCode option:selected").text();
    $("#SlpName").val(inputSlpName);
});

$("#btn-browse-file").on("click",function(e) {
    $("#ModalAttachFile").modal("show");
    var OrderEntry = $("#OrderEntry").val();
    var FileLine = "";
    if(OrderEntry.length == 0) {
        var FileList = document.getElementById('OrderAttach').files;
        if(FileList.length == 0) {
            FileLine += "<tr><td class='text-center' colspan='3'>ไม่มีเอกสารแนบ :(</td></tr>";
        } else{
            var no = 1;
            
            for(i=0;i<=FileList.length-1;i++) {
                FileLine += 
                "<tr>"+
                    "<td class='text-right'>"+number_format(no,0)+"</td>"+
                    "<td>"+FileList[i].name+"</td>"+
                    "<td class='text-center'>&nbsp;</td>"+
                "</tr>";
                no++;
            }
            
        }
        $("#FileAttachList").html(FileLine);
    }
});

/* Step 2 */
$("#ItemSelect").on("change", function(){
    var ItemName = $("#ItemSelect option:selected").attr("data-ItemName");
    var BarCode = $("#ItemSelect option:selected").attr("data-BarCode");
    var ItemStatus = $("#ItemSelect option:selected").attr("data-ItemStatus");
    var UnitMsr = $("#ItemSelect option:selected").attr("data-UnitMsr");
    $("#text_ItemName").val(ItemName);

    $("#text_BarCode").val(BarCode);
    $("#text_ItemStatus").val(ItemStatus);
    $("#text_UnitMsr").val(UnitMsr);
});

$("#GrandPrice , #Discount").focusout(function() {
    var GrandPrice = $("#GrandPrice").val();
    var Discount   = $("#Discount").val();
    var Chk_DP     = $("#Chk_DefaultPrice").val();

    if(GrandPrice.length == 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกราคาให้ถูกต้อง");
        $("#alert_modal").modal('show');
    }

    if(Discount.length > 0) {
        var DiscPrefix = Discount.charAt(0);
        if(DiscPrefix != '*') {
            /* 1. ตรวจสอบรูปแบบของค่าที่รับมา (ตัวเลข และเครื่องหมายบวก) */
            var pattern =  /^[0-9-.]+$/;
            var result = pattern.test(Discount);
            /* 1.1 ถ้าไม่ใช่ ให้แจ้งเตือน "กรุณากรอกส่วนลดให้ถูกต้อง" ถ้าใช่ ไป ข้อ 2. */
            if (result == false) {
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("กรุณากรอกส่วนลดให้ถูกต้อง<br/>(ใช้ตัวเลข และเครื่องหมายลบ (-) คั่นส่วนลดระหว่าง STEP ได้เท่านั้น)");
                $("#alert_modal").modal('show');
            } else {
                /* 2. แบ่งตัวเลขเข้า Array */
                var disStep = Discount.split("-");
                var errorPoint = 0;
                var stepPrice = GrandPrice;
                var conDisStep = 0;
                /* disStep = [50,2,3]; */
                /* 3. ตรวจสอบส่วนลดแต่ละ Step ว่าเกิน 100 หรือไม่? ถ้าใช่ (>= 100) ให้แจ้งเตือน "กรุณาระบุส่วนลดให้ถูกต้อง" */
                if(disStep.length < 5) {
                    for (var i = 0; i < disStep.length; i++) {
                        conDisStep = conDisStep+parseInt(disStep[i]);   
                    }
                    if (conDisStep > 100.00) {
                        errorPoint++;
                    }
                    if (errorPoint > 0) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("กรุณากรอกส่วนลดให้ถูกต้อง<br/>(ส่วนลดต้องไม่เกิน 100%)");
                        $("#alert_modal").modal('show');
                    }else{
                        for (var i = 0; i < disStep.length; i++) {
                            var conDisStep = parseInt(disStep[i]);
                            var stepDiscount = stepPrice*(disStep[i]/100);
                            stepPrice = stepPrice - stepDiscount;
                        }
                        $("#PriceAfDisc").val(stepPrice.toFixed(3));
                        
                        Chk_SPPrice(stepPrice, Chk_DP);
                        
                    } 
                } else {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("กรุณากรอกส่วนลดต้องไม่เกิน 4 สเต็ป");
                    $("#alert_modal").modal('show');
                }
            }  
        } else {
            /* 1. ตรวจสอบรูปแบบของค่าที่รับมา (ตัวเลข และเครื่องหมายลบ) */
            var pattern =  /^[0-9*.]+$/;
            var result = pattern.test(Discount);
            if (result == false) {
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("กรุณากรอกส่วนลดให้ถูกต้อง<br/>(ระบุจำนวนส่วนลดหลังเครื่องหมายดอกจัน (*) เท่านั้น)");
                $("#alert_modal").modal('show');
            } else {
                var DiscAmount = parseFloat(Discount.substring(1));
                stepPrice = GrandPrice-DiscAmount;
                if(DiscAmount <= GrandPrice) {
                    $("#PriceAfDisc").val(stepPrice.toFixed(3));
                    Chk_SPPrice(stepPrice, Chk_DP);
                } else {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("กรุณากรอกส่วนลดให้ถูกต้อง<br/>(ส่วนลดต้องไม่เกินราคาขาย)");
                    $("#alert_modal").modal('show');
                }
            }
        }
    } else {
        $("#PriceAfDisc").val(GrandPrice);
        if(GrandPrice > 0) {
            Chk_SPPrice(GrandPrice, Chk_DP);
        }
    }
});

$("#ImportItem").on("click",function(e){
    e.preventDefault();
    $("#ModalImport").modal("show");
});

$("#btn-AddRow").on("click",function(e){
    e.preventDefault();
    AddNewRow();
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
            var CardCode = $("#CardCode").val();

            SearchDoc(SearchBox, CardCode);
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
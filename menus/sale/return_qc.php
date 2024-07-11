<?php
    $start_year = 2023;
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
                        <a href="#QcRtList" class="btn-tabs nav-link active" id="QcRtList-tab" data-bs-toggle="tab" data-bs-target="#QcRtList" role="tab" data-tabs="0" aria-controls="QcRtList" aria-selected="false">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการคืน QC
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#NewQcRt" class="btn-tabs nav-link" id="NewQcRt-tab" data-bs-toggle="tab" data-bs-target="#NewQcRt" role="tab" data-tabs="1" aria-controls="NewQcRt" aria-selected="true">
                            <i class="fas fa-plus fa-fw fa-1x"></i> เพิ่ม/แก้ไขเอกสารใหม่
                        </a>
                    </li>
                </ul>
                <!-- END CONTENT TAB -->
                <div class="tab-content">
                    <!-- TAB 0 -->
                    <div class="tab-pane fade show active" id="QcRtList" role="tabpanel" aria-labelledby="QcRtList-tab">
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
                                $opt_DMN = " disabled";
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
                                        $opt_DMN = NULL;
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
                                        $opt_DMN = NULL;
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
                                        <option value="DMN"<?php echo $opt_DMN; ?> disabled><?php echo SATeamName("DMN"); ?></option>
                                        <option value="TT1"<?php echo $opt_TT1; ?>><?php echo SATeamName("TT1"); ?></option>
                                        <option value="OUL"<?php echo $opt_OUL; ?>><?php echo SATeamName("OUL"); ?></option>
                                        <option value="ONL"<?php echo $opt_ONL; ?>><?php echo SATeamName("ONL"); ?></option>
                                        <option value="KBI"><?php echo SATeamName("KBI"); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="min-height: 512px;">
                            <table class="table table-sm table-hover table-bordered" id="QcRtListTable" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th class="text-center border-top" width="3.5%">ลำดับ</th>
                                        <th class="text-center border-top" width="6.5%">วันที่<br/>เอกสาร</th>
                                        <th class="text-center border-top" width="12.5%">ประเภท<br/>เอกสาร</th>
                                        <th class="text-center border-top" width="7.5%">เลขที่<br/>เอกสาร</th>
                                        <th class="text-center border-top">ชื่อลูกค้า</th>
                                        <th class="text-center border-top" width="7.5%">เลขที่<br/>ใบมา</th>
                                        <th class="text-center border-top" width="10%">เอกสาร<br/>อ้างอิง</th>
                                        <th class="text-center border-top" width="17.5%"><span class='badge bg-dark'>ทีม</span> พนักงานขาย</th>
                                        <th class="text-center border-top" width="7.5%">สถานะเอกสาร</th>
                                        <th class="text-center border-top" width="5%"><i class="fas fa-cog fa-fw fa-1x"></i></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div id="QcRtTable"></div>
                    </div>
                    <!-- TAB 1 -->
                    <div class="tab-pane fade" id="NewQcRt" role="tabpanel" aria-labelledby="NewQcRt-tab">
                        <form class="form" id="QcRtForm" enctype="multipart/form-data">
                            <!-- STEP 1 -->
                            <div id="QcRt-step1" class="need-validation" data-step="1">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 1: เลือกข้อมูลการคืน QC</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_DocDate">วันที่เอกสาร<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="txt_DocDate" id="txt_DocDate" value="<?php echo date("Y-m-d"); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_RefDoc1">เลขที่ใบมา<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="txt_RefDoc1" id="txt_RefDoc1" placeholder="เลขที่ใบมา" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="txt_DocType">ประเภทเอกสาร<span class="text-danger">*</span></label>
                                            <select class="form-select" type="txt_DocType" id="txt_DocType">
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                                <option value="D">[D] คืนเพื่อลดหนี้</option>
                                                <option value="L">[L] คืนจากการยืม</option>
                                                <option value="X" disabled>[X] คืนจากการที่คลังส่งของผิด ส่งเกิน</option>
                                                <option value="AC">[AC] คืนแบบไม่มีสินค้า (คืนลอย)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="txt_BillDocNum">เลขที่เอกสาร <span id="BillTypeTxt"></span></label>
                                            <input type="text" class="form-control" name="txt_BillDocNum" id="txt_BillDocNum" disabled>
                                            <input type="hidden" name="txt_BillDocType" id="txt_BillDocType" readonly>
                                            <input type="hidden" name="txt_BillDocEntry" id="txt_BillDocEntry" readonly>
                                            <input type="hidden" name="txt_BillSAPVer" id="txt_BillSAPVer" readonly>
                                            <input type="hidden" name="txt_BillTeamCode" id="txt_BillTeamCode" readonly>
                                            <small class="text-muted">กรอกเลขที่เอกสารบิล / PA / PC</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="txt_RefDoc2">เลขที่เอกสาร FM-WH-17</label>
                                            <input type="text" class="form-control" name="txt_RefDoc2" id="txt_RefDoc2" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group mb-3">
                                            <label for="txt_BillDocNum2">กรุณาเลือกประเภทเอกสาร</label>
                                            <input type="text" class="form-control" name="txt_BillDocNum2" id="txt_BillDocNum2" disabled>
                                            <input type="hidden" name="txt_BillDocType2" id="txt_BillDocType2" readonly>
                                            <input type="hidden" name="txt_BillDocEntry2" id="txt_BillDocEntry2" readonly>
                                            <input type="hidden" name="txt_BillSAPVer2" id="txt_BillSAPVer2" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="txt_SendType">รูปแบบการส่งคืน<span class="text-danger">*</span></label>
                                            <select class="form-select" name="txt_SendType" id="txt_SendType" required>
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                                <option value="1">ลูกค้าฝากขนส่ง</option>
                                                <option value="2">เซลส์รับกลับมาคืน</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="txt_ShippingName">ชื่อขนส่ง</label>
                                            <input type="text" class="form-control" name="txt_ShippingName" id="txt_ShippingName" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="txt_CoLogiName">ธุรการขนส่งที่รับสินค้า</label>
                                            <select class="form-control selectpicker" name="txt_CoLogiName" id="txt_CoLogiName" data-live-search="true">
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="txt_ShipCost">ค่าขนส่ง<span class="text-danger">*</span></label>
                                            <select class="form-select" name="txt_ShipCost" id="txt_ShipCost">
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                                <option value="Y">มีค่าขนส่ง</option>
                                                <option value="N">ไม่มีค่าขนส่ง</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="txt_ShipCostBaht">มูลค่าขนส่ง (บาท)</label>
                                            <input type="number" min="0" class="form-control text-right" name="txt_ShipCostBaht" id="txt_ShipCostBaht" value="0" disabled/>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="txt_ShipCostName">ผู้รับผิดชอบค่าขนส่ง</label>
                                            <select class="form-control selectpicker" name="txt_ShipCostName" id="txt_ShipCostName" data-live-search="true" disabled>
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="txt_AttDoc">เอกสารแนบ</label>
                                        </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="txt_Att1" id="txt_Att1">
                                                <label class="form-check-label" for="txt_Att1">ฟอร์มการคืนสินค้า (ต้นฉบับ)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="txt_Att2" id="txt_Att2">
                                                <label class="form-check-label" for="txt_Att2">สำเนาใบกำกับภาษี</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="txt_Att3" id="txt_Att3">
                                                <label class="form-check-label" for="txt_Att3">สำเนาใบยืมสินค้า (PA) หรือใบส่งสินค้าผิด (PC)</label>
                                            </div>
                                            <!-- <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="txt_Att4" id="txt_Att4">
                                                <label class="form-check-label" for="txt_Att4">รูปถ่ายสินค้า</label>
                                            </div> -->
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="DocAttach">ภาพถ่ายสินค้า<span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="DocAttach" name="DocAttach[]" accept="image/*" multiple required />
                                            <small class="text-muted">เลือกภาพถ่ายสินค้า<span class="text-danger">ได้มากกว่า 1 ไฟล์</span></small>
                                        </div>
                                    </div>
                                    <div class="offset-lg-6 col-lg-6">
                                        <span class="text-danger">* การแนบรูปภาพถ่ายสินค้า จะต้องแนบภาพถ่ายสินค้าให้ครบถ้วน มิฉะนั้นท่านอาจถูกปฏิเสธการรับคืนสินค้า QC *</span>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 2: รายละเอียดการคืน</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="txt_BillCardCode">ชื่อลูกค้า<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="txt_BillCardCode" id="txt_BillCardCode" required readonly />
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_BillSlpCode">ชื่อพนักงานขาย<span class="text-danger">*</span></label>
                                            <select class="form-control selectpicker" name="txt_BillSlpCode" id="txt_BillSlpCode" data-live-search="true" required>
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_BillOwnerCode">ชื่อธุรการขาย<span class="text-danger">*</span></label>
                                            <select class="form-control selectpicker" name="txt_BillOwnerCode" id="txt_BillOwnerCode" data-live-search="true" required>
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_BillDate">วันที่เปิดบิล<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="txt_BillDate" id="txt_BillDate" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_BillDueDate">วันที่กำหนดชำระ<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="txt_BillDueDate" id="txt_BillDueDate" readonly required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="txt_ReturnReason">สาเหตุการคืน<span class="text-danger">*</span></label>
                                            <select class="form-control selectpicker" name="txt_ReturnReason" id="txt_ReturnReason" data-live-search="true">
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                                <optgroup label="1. ลูกค้า">
                                                    <option value="1.1">1.1 ลูกค้าสั่งผิด</option>
                                                    <option value="1.2">1.2 คู่แข่งตัดราคา</option>
                                                    <option value="1.3">1.3 ลูกค้ามีปัญหาด้านการเงิน</option>
                                                    <option value="1.4">1.4 LAZADA</option>
                                                    <option value="1.5">1.5 สินค้า Dead Stock (ระบุระยะเวลา)</option>
                                                    <option value="1.6">1.6 ลูกค้าไม่มั่นใจคุณภาพสินค้า</option>
                                                </optgroup>
                                                <optgroup label="2. พนักงาน">
                                                    <option value="2.1">2.1 เซลส์แจ้งผิด</option>
                                                    <option value="2.2">2.2 ธุรการเซลส์เปิดบิลผิด</option>
                                                    <option value="2.3">2.3 คลัง/ขนส่งผิด</option>
                                                    <option value="2.4">2.4 ยืมออกตลาด</option>
                                                    <option value="2.5">2.5 สินค้า Consign (ฝากขาย)</option>
                                                    <option value="2.6">2.6 ยืมออกบูธ</option>
                                                    <option value="2.7">2.7 ยืมไปทดลอง/ใช้งาน</option>
                                                    <option value="2.8">2.8 ยืมไปเปลี่ยนสินค้าชำรุด</option>
                                                </optgroup>
                                                <optgroup label="3. ผลิตภัณฑ์">
                                                    <option value="3.1">3.1 อุปกรณ์ไม่ครบ</option>
                                                    <option value="3.2">3.2 ชำรุดจากโรงงาน</option>
                                                    <option value="3.3">3.3 ชำรุดจากขนส่ง</option>
                                                    <option value="3.4">3.4 ชำรุดจากลูกค้า</option>
                                                </optgroup>
                                                <optgroup label="4. อื่น ๆ">
                                                    <option value="4.1">4.1 เหตุการณ์ภายในประเทศ</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_DeadStockType">ระยะเวลา Dead Stock</label>
                                            <select class="form-select" name="txt_DeadStockType" id="txt_DeadStockType" disabled>
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                                <option value="1">0 - 6 เดือน (100% ของราคาขาย)</option>
                                                <option value="2">7 - 12 เดือน (80% ของราคาขาย)</option>
                                                <option value="3">13 - 24 เดือน (50% ของราคาขาย)</option>
                                                <option value="4">25 เดือนขึ้นไป (30% ของราคาขาย)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_FreeBie">ของแถม</label>
                                            <select class="form-select" name="txt_FreeBie" id="txt_FreeBie">
                                                <option value="Y">เป็นของแถม</option>
                                                <option value="N" selected>ไม่เป็นของแถม</option>
                                            </select>
                                            <small class="text-muted">คำนวณตามสัดส่วนค่าเฉลี่ย</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_Incentive">การจ่ายค่า Incentive<span class="text-danger">*</span></label>
                                            <select class="form-select" name="txt_Incentive" id="txt_Incentive">
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                                <option value="Y">จ่ายค่า Incentive แล้ว</option>
                                                <option value="N">ยังไม่จ่ายค่า Incentive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_Incentivebaht">มูลค่า Incentive (บาท)</label>
                                            <input type="number" class="form-control text-right" name="txt_Incentivebaht" id="txt_Incentivebaht" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- COSA -->
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_COSA_FineType">ค่าปรับธุรการเซลส์</label>
                                            <select class="form-select" name="txt_COSA_FineType" id="txt_COSA_FineType">
                                                <option value="Y">ปรับ (20 บาท)</option>
                                                <option value="N" selected>ไม่ปรับ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_COSA_FineName">ชื่อธุรการเซลส์ที่โดนปรับ</label>
                                            <select class="form-control selectpicker" name="txt_COSA_FineName" id="txt_COSA_FineName" data-live-search="true" disabled>
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group mb-3">
                                            <label for="txt_RefDoc3">ใบวินัยเลขที่</label>
                                            <input type="text" class="form-control" name="txt_RefDoc3" id="txt_RefDoc3" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group mb-3">
                                            <label for="txt_RefDoc3No">ข้อที่</label>
                                            <input type="text" class="form-control" name="txt_RefDoc3No" id="txt_RefDoc3No" disabled>
                                        </div>
                                    </div>
                                    <!-- SALE -->
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_SALE_FineType">ค่าปรับเซลส์</label>
                                            <select class="form-select" name="txt_SALE_FineType" id="txt_SALE_FineType">
                                                <option value="Y">ปรับ (50 บาท)</option>
                                                <option value="N" selected>ไม่ปรับ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="txt_SALE_FineName">ชื่อเซลส์ที่โดนปรับ</label>
                                            <select class="form-control selectpicker" name="txt_SALE_FineName" id="txt_SALE_FineName" data-live-search="true" disabled>
                                                <option value="" selected disabled>กรุณาเลือก</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group mb-3">
                                            <label for="txt_RefDoc4">ใบวินัยเลขที่</label>
                                            <input type="text" class="form-control" name="txt_RefDoc4" id="txt_RefDoc4" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group mb-3">
                                            <label for="txt_RefDoc4No">ข้อที่</label>
                                            <input type="text" class="form-control" name="txt_RefDoc4No" id="txt_RefDoc4No" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label for="txt_COSA_Remark">หมายเหตุธุรการเซลส์</label>
                                            <input type="text" class="form-control" name="txt_COSA_Remark" id="txt_COSA_Remark">
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row mt-2">
                                    <!-- <div class="col-lg-9">
                                        <p class="fw-bolder">กรุณาเลือกรายการสินค้าที่ต้องการคืน</p>
                                    </div>
                                    <div class="col-lg-1 text-right">
                                        <i class="fas fa-filter fa-fw fa-1x mt-2"></i>
                                    </div>
                                    <div class="col-lg-2">
                                        <input type="text" class="form-control form-control-sm" style="font-size: 12px;" id="filt_QCItem" />
                                    </div> -->
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="slct_ItemList">เลือกสินค้าที่ต้องการรับคืน</label>
                                            <select class="form-control selectpicker" data-live-search="true" name="slct_ItemList" id="slct_ItemList" disabled>
                                                <option selected disabled>กรุณาเลือก</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="form-group mb-3">
                                            <label for="btn-searchdoc">&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" id="btn-addRow" name="btn-addRow" disabled><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่ม</button>
                                        </div>
                                    </div>
                                    <div class="offset-lg-5 col-lg-2">
                                        <div class="form-group mb-3">
                                            <label for="filt_QCItem"><i class="fas fa-filter fa-fw fa-1x"></i></label>
                                            <input type="text" class="form-control form-control-sm" style="font-size: 12px;" id="filt_QCItem" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 table-responsive">
                                        <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="QCItemList">
                                            <thead class="text-center">
                                                <th width="7.5%">รหัสสินค้า</th>
                                                <th width="8%">บาร์โค้ด</th>
                                                <th>ชื่อสินค้า</th>
                                                <th width="5%">สถานะ</th>
                                                <th width="7.5%">ราคา<br/>ก่อนส่วนลด</th>
                                                <th width="15%">ส่วนลด (%)</th>
                                                <th width="7.5%">ราคา<br/>หลังส่วนลด</th>
                                                <th width="6.5%">จำนวน</th>
                                                <th width="5%">หน่วย</th>
                                                <th width="6%">คลังสินค้า</th>
                                                <th width="3.5%"><i class="fas fa-trash fa-fw fa-1x"></i></th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" colspan="12">กรุณากรอกเลขที่บิล</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg text-right">
                                        <input type="hidden" id="TotalRow" value="0" readonly>
                                        <button type="button" class="btn btn-secondary" onclick="window.location.reload();"><i class="fas fa-sync fa-fw fa-1x"></i></button>
                                        <button type="button" class="btn btn-primary" onclick="SaveDoc();"><i class="fas fa-save fa-fw fa-1x"></i> สร้างเอกสารใหม่</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL PREVIEW DOC -->
<div class="modal fade" id="ModalPreviewDoc" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt fa-fw fa-1x"></i> รายละเอียดการคืน QC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tbody>
                                <tr>
                                    <th width="10%">เลขที่เอกสาร</th>
                                    <td width="30%" id="prev_DocNum"></td>
                                    <th width="10%">เลขที่ใบมา</th>
                                    <td width="20%" id="prev_RefDoc1"></td>
                                    <th width="10%">วันที่เอกสาร</th>
                                    <td width="20%" id="prev_DocDate"></td>
                                </tr>
                                <tr>
                                    <th>ประเภทการคืน</th>
                                    <td id="prev_DocType"></td>
                                    <th>เอกสารอ้างอิง</th>
                                    <td colspan="3" id="prev_BillDocNum"></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th>ชื่อลูกค้า</th>
                                    <td class="text-success" id="prev_BillCardCode"></td>
                                    <th>วันที่เปิดบิล</th>
                                    <td class="text-success" id="prev_BillDocDate"></td>
                                    <th>วันที่กำหนดชำระ</th>
                                    <td class="text-success" id="prev_BillDocDueDate"></td>
                                </tr>
                                <tr>
                                    <th>ชื่อพนักงานขาย</th>
                                    <td class="align-top text-success" id="prev_BillSlpName"></td>
                                    <th>ชื่อธุรการขาย</th>
                                    <td class="align-top text-success" id="prev_BillOwnName"></td>
                                    <th>เอกสารแนบ</th>
                                    <td id="prev_Attach"></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th>ค่าปรับเซลส์</th>
                                    <td colspan="5" id="prev_FineSALE"></td>
                                </tr>
                                <tr>
                                    <th>ค่าปรับ Co-Sales</th>
                                    <td colspan="5" id="prev_FineCOSA"></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th>สาเหตุการคืน</th>
                                    <td id="prev_ReturnReason" class="text-danger" style="font-weight: bold;"></td>
                                    <th>ค่า Incentive</th>
                                    <td id="prev_Incentive"></td>
                                    <th>เป็นของแถมหรือไม่</th>
                                    <td id="prev_Freebie"></td>
                                </tr>
                                <tr>
                                    <th>รูปแบบการส่งคืน</th>
                                    <td colspan="5" id="prev_SendType"></td>
                                </tr>
                                <tr>
                                    <th>ค่าขนส่ง</th>
                                    <td colspan="5" id="prev_ShipCost"></td>
                                </tr>
                                <tr>
                                    <th>หมายเหตุ</th>
                                    <td colspan="5" id="prev_Remark" class="text-danger" style="font-weight: bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                        <h6><i class="fas fa-list fa-fw fa-1x"></i> รายการสินค้า</h6>
                        <div class="alert alert-secondary text-center" role="alert">
                            <p>
                                <strong>เกรดสินค้า</strong><br/>
                                QC = สินค้าใหม่ขายได้ || A = สินค้าสภาพดี/ไม่มีกล่อง || AB = สินค้ามีตำหนิ || AX = สินค้าชำรุดสภาพดี || BX = สินค้าชำรุดมาก
                            </p>
                        </div>
                        <table class="table table-bordered table-hover table-sm" id="prev_ItemList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="10%">รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th width="5%">สถานะ</th>
                                    <th width="7.5%">ราคา<br/>ก่อนส่วนลด</th>
                                    <th width="15%">ส่วนลด (%)</th>
                                    <th width="7.5%">ราคา<br/>หลังส่วนลด</th>
                                    <th width="7.5%">จำนวน</th>
                                    <th width="5%">หน่วย</th>
                                    <th width="7.5%">คลังสินค้า<br/>ที่เปิดบิล</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr/>
                        <h6><i class="fas fa-tasks fa-fw fa-1x"></i> สถานะการอนุมัติ</h6>
                        <table class="table table-bordered table-hover table-sm" id="prev_ApproveList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="20%">ผู้อนุมัติ</th>
                                    <th width="15%">ผลการพิจารณา</th>
                                    <th>หมายเหตุ</th>
                                    <th width="20%">ผู้อนุมัติ</th>
                                    <th width="15%">วันที่อนุมัติ</th>
                                    <th width="5%"><i class="fas fa-save fa-fw fa-lg"></i></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="prev_footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalViewAttach" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-images fa-fw fa-1x"></i> รายการภาพถ่ายสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" id="ImgAttach">

                    </div>
                </div>
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
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><i class="far fa-times-circle fa-fw fa-1x"></i> ไม่</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-del-confirm" data-rowid="0" data-bs-dismiss="modal"><i class="far fa-check-circle fa-fw fa-1x"></i> ใช่</button>
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
                <p id="confirm_body" class="my-4">คุณต้องการยกเลิกเอกสารนี้หรือไม่?</p>
                <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-dismiss" data-bs-dismiss="modal"><i class="far fa-times-circle fa-fw fa-1x"></i> ไม่</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-cancel-confirm" data-docentry="0" data-bs-dismiss="modal"><i class="far fa-check-circle fa-fw fa-1x"></i> ใช่</button>
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
    function CallHead(){
        var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            data : {MenuCase : MenuCase,},
            beforeSend: function() { $(".overlay").show(); },
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
    function CallDropDown() {
        $.ajax({
            url: "menus/sale/ajax/ajaxreturn_qc.php?p=CallDropDown",
            type: "POST",
            beforeSend: function() { $(".overlay").show(); },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    /* ROWS */
                    var SetArr = ["COLO","USER","COSA","SALE"];
                    var SelArr = ["#txt_CoLogiName","#txt_ShipCostName","#txt_BillOwnerCode, #txt_COSA_FineName","#txt_BillSlpCode, #txt_SALE_FineName"];
                    for(i = 0; i < SetArr.length; i++) {
                        var TxtOpt = "";
                        var ROW    = inval[SetArr[i]]['ROW'];
                        for(r = 0; r < ROW; r++) {
                            TxtOpt += "<option value='"+inval[SetArr[i]][r]['VAL']+"' data-subtext='"+inval[SetArr[i]][r]['DPN']+"'>"+inval[SetArr[i]][r]['TXT']+"</option>\n";
                        }
                        $(SelArr[i]).selectpicker("destroy").append(TxtOpt).selectpicker();
                    }
                });
                $(".overlay").hide();
            }
        });
    }

    function DelRow(ROW) {
        $("#confirm_delete").modal("show");
        // นำค่าจาก row เก็บใน Attribut: data-rowid ของ ID:btn-del-confirm
        $("#btn-del-confirm").attr("data-rowid",ROW);

        // เมื่อมีการคลิก ปุ่ม ID:btn-del-confirm ให้เอาค่าแอททริบิว data-rowid เก็บไว้ในตัวแปร RowID แล้วลบแถวของตารางตาม ID ของของแถวนั้นๆ
        $(document).off("click","#btn-del-confirm").on("click","#btn-del-confirm", function(e){
            e.preventDefault();
            let RowID = $(this).attr("data-rowid");
            $("#QCItemList tbody tr[data-row='"+RowID+"']").remove();

            let RowCount = $("#QCItemList tbody tr").length;


            if(RowCount == 0) {
                $("#TotalRow").val(0);
                $("#QCItemList tbody").html("<tr><td class='text-center' colspan='11'>กรุณากรอกเลขที่บิล</td></tr>");
            }
        });
    }

    function SearchBill(TextBox, BillType) {
        $.ajax({
            url: "menus/sale/ajax/ajaxreturn_qc.php?p=SearchBill",
            type: "POST",
            beforeSend: function() { $(".overlay").show(); },
            data: { TextBox: TextBox, BillType: BillType },
            success: function(result) {
                $("#txt_Att2, #txt_Att3").prop("checked",false);
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    if(inval['HEAD']['ROW'] == 0) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่พบรายการบิล กรุณากรอกเลขที่เอกสารให้ถูกต้อง");
                        $("#alert_modal").modal('show');

                        /* RESET INVOICE FORM */
                        $("#txt_BillCardCode, #txt_BillDate, #txt_BillDueDate, #txt_RefDoc3, #txt_RefDoc3No, #txt_RefDoc4, #txt_RefDoc4No, #txt_BillDocType, #txt_BillDocEntry").val("");
                        $("#txt_BillSlpCode, #txt_SALE_FineName, #txt_BillOwnerCode, #txt_COSA_FineName, #txt_ReturnReason").selectpicker('destroy').val("").change().selectpicker();
                        $("#txt_DeadStockType").val("").change();
                        $("#txt_Incentive, #txt_COSA_FineType, #txt_SALE_FineType").val("N").change();
                        $("input#ItemChckAll").prop("checked",false).attr("disabled",true);
                        $("#QCItemList tbody").html("<tr><td class='text-center' colspan='12'>กรุณากรอกเลขที่บิล</td></tr>");
                    } else {
                        /* INPUT DATA */
                        $("#txt_BillCardCode").val(inval['HEAD']['CardCode']);
                        $("#txt_BillSlpCode, #txt_SALE_FineName").selectpicker('destroy').val(inval['HEAD']['SlpUkey']).change().selectpicker();
                        $("#txt_BillOwnerCode, #txt_COSA_FineName").selectpicker('destroy').val(inval['HEAD']['OwnUkey']).change().selectpicker();
                        $("#txt_BillDate").val(inval['HEAD']['DocDate']);
                        $("#txt_BillDueDate").val(inval['HEAD']['DocDueDate']);
                        $("#txt_BillDocType").val(inval['HEAD']['BillType']);
                        $("#txt_BillDocEntry").val(inval['HEAD']['DocEntry']);
                        $("#txt_BillSAPVer").val(inval['HEAD']['SAPVer']);
                        $("#txt_BillTeamCode").val(inval['HEAD']['TeamCode']);

                        switch(BillType) {
                            case "OINV": $("#txt_Att2").prop("checked",true); break;
                            case "ODLN": $("#txt_Att3").prop("checked",true); break;
                        }

                        /* DETAIL */
                        let tBody = "<option selected disabled>กรุณาเลือกสินค้า</option>";
                        let No = 1;
                        for(r = 0; r < inval['HEAD']['ROW']; r++) {
                            tBody +=
                                "<option value='"+inval['BODY'][r]['OptValue']+"'>"+
                                    No+" | "+
                                    inval['BODY'][r]['ItemCode']+" | "+
                                    inval['BODY'][r]['CodeBars']+" | "+
                                    inval['BODY'][r]['ItemName']+" | "+
                                    inval['BODY'][r]['WhsCode']+" | "+
                                    inval['BODY'][r]['Quantity']+" "+inval['BODY'][r]['UnitMsr']
                                "</option>";
                                No++;
                        }

                        $("#slct_ItemList").selectpicker("destroy").removeAttr("disabled").html(tBody).selectpicker();
                        $("#btn-addRow").removeAttr("disabled");

                        // $("input#ItemChckAll").attr("disabled",false);
                        // $("#QCItemList tbody").html(tBody);
                    }
                });

                $(document).off("click","#btn-addRow").on("click","#btn-addRow", function(e){
                    e.preventDefault();
                    let slct_ItemList = $("#slct_ItemList").val();
                    if(slct_ItemList == "" || slct_ItemList == null) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("กรุณาเลือกสินค้าที่ต้องการรับคืน");
                        $("#alert_modal").modal('show');
                    } else {
                        let ItemArr = slct_ItemList.split("::");
                        /*
                            Pos 0 = VisOrder
                            Pos 1 = ItemCode
                            Pos 2 = CodeBars
                            Pos 3 = ItemName
                            Pos 4 = ItemStatus
                            Pos 5 = Quantity
                            Pos 6 = UnitMsr
                            Pos 7 = WhsCode
                            Pos 8 = Unit Price
                            Pos 9 = Grand Price
                            Pos10 = Discount
                        */
                        let Row = parseInt($("#TotalRow").val());
                        if(Row == 0) {
                            $("#QCItemList tbody").empty();
                        }
                        Row = Row+1;
                        let tBody =
                            "<tr data-row='"+Row+"'>"+
                                "<td>"+
                                    "<input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm text-center' name='txtItemCode_"+Row+"' id='txtItemCode_"+Row+"' value='"+ItemArr[1]+"' readonly>"+
                                    "<input data-row='"+Row+"' type='hidden' class='txtChkRow' name='txtChkRow_"+Row+"' id='txt_ChkRow_"+Row+"' value='"+Row+"'>"+
                                    "<input style='font-size: 12px;' type='hidden' min='1' data-row='"+Row+"' class='form-control form-control-sm text-right' name='txtVisOrder_"+Row+"' id='txtVisOrder_"+Row+"' value='"+ItemArr[0]+"' readonly>"+
                                "</td>"+
                                "<td><input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm text-center' name='txtCodeBars_"+Row+"' id='txtCodeBars_"+Row+"' value='"+ItemArr[2]+"' readonly></td>"+
                                "<td><input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm' name='txtItemName_"+Row+"' id='txtItemName_"+Row+"' value='"+ItemArr[3]+"' readonly></td>"+
                                "<td><input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm text-center' name='txtItemStatus_"+Row+"' id='txtItemStatus_"+Row+"' value='"+ItemArr[4]+"' readonly></td>"+
                                "<td><input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm text-right' name='txtGrandPrice_"+Row+"' id='txtGrandPrice_"+Row+"' value='"+ItemArr[9]+"'</td>"+
                                "<td><input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm text-center' name='txtDiscount_"+Row+"' id='txtDiscount_"+Row+"' value='"+ItemArr[10]+"'</td>"+
                                "<td><input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm text-right' name='txtUnitPrice_"+Row+"' id='txtUnitPrice_"+Row+"' value='"+ItemArr[8]+"'</td>"+
                                "<td>"+
                                    "<input style='font-size: 12px;' type='number' min='1' data-row='"+Row+"' class='form-control form-control-sm text-right' name='txtQuantity_"+Row+"' id='txtQuantity_"+Row+"' value='"+ItemArr[5]+"'>"+
                                    "<input style='font-size: 12px;' type='hidden' min='1' data-row='"+Row+"' class='form-control form-control-sm text-right' name='txtChkQty_"+Row+"' id='txtChkQty_"+Row+"' value='"+ItemArr[5]+"' readonly>"+
                                "</td>"+
                                "<td><input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm' name='txtUnitMsr_"+Row+"' id='txtUnitMsr_"+Row+"' value='"+ItemArr[6]+"' readonly></td>"+
                                "<td><input style='font-size: 12px;' type='text' class='form-control-plaintext form-control-sm text-center' name='txtWhsCode_"+Row+"' id='txtWhsCode_"+Row+"' value='"+ItemArr[7]+"' readonly></td>"+
                                "<td class='text-center'><button type='button' class='btn btn-danger btn-sm' onclick='DelRow("+Row+");'><i class='fas fa-trash fa-fw fa-1x'></i></button></td>"+
                            "</tr>";
                        $("#QCItemList tbody").append(tBody);
                        $("#TotalRow").val(Row);

                        $("input[id*=\"txtQuantity_\"]").on("focusout", function(e){
                            let RID = $(this).attr("data-row");
                            let NewQty = parseInt($(this).val());
                            let ChkQty = parseInt($("#txtChkQty_"+RID).val());
                            if(NewQty > ChkQty || NewQty <= 0) {
                                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                $("#alert_body").html("กรุณาใส่จำนวนให้ถูกต้อง");
                                $("#alert_modal").modal('show');
                                $(this).val(ChkQty);
                            }
                        });

                        $("input[id*=\"txtSAWhsCode_\"]").on("focusout", function(e){
                            let RID       = $(this).attr("data-row");
                            let WhsCode   = $(this).val();
                            let ItemCode  = $("input#txtItemCode_"+RID).val();
                            let ItemGrade = $("select#txtSAGrade_"+RID).val();
                            if(WhsCode.length > 0 && ItemGrade != null) {
                                $.ajax({
                                    url: "menus/sale/ajax/ajaxreturn_qc.php?p=ChkWhs",
                                    type: "POST",
                                    beforeSend: function() { $(".overlay").show(); },
                                    data: { WhsCode: WhsCode, ItemCode: ItemCode },
                                    success: function(result) {
                                        var obj = jQuery.parseJSON(result);
                                        $.each(obj, function(key, inval) {
                                            if(inval['Status'] != "SUCCESS") {
                                                let ErrCode = inval['Status'].split("::");
                                                $("td#txtWhsStatus_"+RID).empty();
                                                switch(ErrCode[1]) {
                                                    case "NOWHSE":
                                                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                                        $("#alert_body").html("ไม่พบคลังสินค้า กรุณาระบุคลังสินค้าให้ถูกต้อง");
                                                        $("td#txtWhsStatus_"+RID).html("<span class='text-danger'><i class='fas fa-times fa-fw fa-1x'></i>");
                                                        $("input#txtSAWhsCode_"+RID).val("");
                                                    break;
                                                    case "NOINVT":
                                                        $("#alert_header").html("<span class=\"text-warning\"><i class=\"fas fa-exclamation-triangle fa-fw fa-lg\"></i> คำเตือน</span>");
                                                        $("#alert_body").html("ไม่พบคลังสินค้าในรายการสินค้านี้<br/><small>(แจ้งฝ่ายไอทีเพิ่มคลังสินค้า <b class='text-danger'>"+WhsCode+"</b> ในรายการสินค้า <b class='text-danger'>"+ItemCode+"</b> ในระบบ SAP)</small>");
                                                        $("td#txtWhsStatus_"+RID).html("<span class='text-warning'><i class='fas fa-exclamation-triangle fa-fw fa-1x'></i>");
                                                    break;
                                                }
                                                $("#alert_modal").modal('show');
                                            } else {
                                                $("td#txtWhsStatus_"+RID).html("<span class='text-success'><i class='fas fa-check fa-fw fa-1x'></i>");
                                            }
                                        });
                                        $(".overlay").hide();
                                    }
                                })
                            } else if(ItemGrade == null) {
                                if($("select#txtSAGrade_"+RID).attr("disabled") == "disabled") {
                                } else {
                                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                    $("#alert_body").html("กรุณาระบุเกรดสินค้า");
                                    $("#alert_modal").modal('show');
                                }
                            }
                        });

                        /* Filter input value in tables */
                        var $rows = $("#QCItemList tbody tr");
                        $("#filt_QCItem").on("keyup", function() {
                            var value = $(this).val();
                            $rows.show().filter(function() {
                                var $inputs = $(this).find("input:text");
                                var found   = value.length == 0;
                                for(var i = 0; i < $inputs.length && !found; i++) {
                                    var text = $inputs.eq(i).val().replace(/\s+/g,' ');
                                    found = text.length > 0 && text.indexOf(value) >= 0;
                                }
                                return !found;
                            }).hide();

                            if(value.length > 0) {
                                $("input#ItemChckAll").attr("disabled",true);
                                
                            } else {
                                $("input#ItemChckAll").attr("disabled",false);
                            }

                        });
                    }
                })

                $(".overlay").hide();
            }
        });
    }

    function GetDocList(filt_year,filt_month,filt_team) {
        $(".overlay").show();
        $("#QcRtListTable").dataTable().fnClearTable();
        $("#QcRtListTable").dataTable().fnDraw();
        $("#QcRtListTable").dataTable().fnDestroy();
        $("#QcRtListTable").DataTable({
            "ajax": {
                url: "menus/sale/ajax/ajaxreturn_qc.php?p=GetDocList",
                type: "POST",
                data: {filt_y: filt_year, filt_m: filt_month, filt_t: filt_team},
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "no", class: "text-right border-start border-bottom" },
                { "data": "DocDate", class: "text-center border-start border-bottom" },
                { "data": "DocType", class: "border-start border-bottom" },
                { "data": "DocNum", class: "text-center border-start border-bottom" },
                { "data": "BillCardCode", class: "border-start border-bottom" },
                { "data": "RefDocNum", class: "text-center border-start border-bottom" },
                { "data": "BillDocNum", class: "text-center border-start border-bottom" },
                { "data": "BillSlpName", class: "border-start border-bottom" },
                { "data": "txt_status", class: "text-center border-start border-bottom" },
                { "data": "txt_opt", class: "text-center border-start border-bottom" },
            ],
            createdRow: (row, data, dataIndex, cells) => {
                switch(data.int_status) {
                    case 0:
                        $(row).addClass("text-active table-secondary");
                    break;
                    case 1.5:
                    case 2:
                        $(row).addClass("text-warning table-warning");
                    break;
                    case 3:
                    case 5:
                        $(row).addClass("text-success table-success");
                    break;
                    case 4:
                        $(row).addClass("text-danger table-danger");
                    break;
                }
            },
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 20,
        });
        $(".overlay").hide();
    }

    function PreviewDoc(DocEntry,int_status) {
        $.ajax({
            url: "menus/sale/ajax/ajaxreturn_qc.php?p=PreviewDoc",
            type: "POST",
            data: { DocEntry: DocEntry, int_status: int_status },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    if(inval['HEAD']['Row'] == 0) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่พบข้อมูลการคืน QC กรุณาลองใหม่อีกครั้ง");
                        $("#alert_modal").modal('show');
                    } else {
                        /* HEADER */
                        $("#prev_DocNum").html(inval['HEAD']['DocNum']);
                        $("#prev_RefDoc1").html(inval['HEAD']['RefDoc1']);
                        $("#prev_DocDate").html(inval['HEAD']['DocDate']);
                        $("#prev_DocType").html(inval['HEAD']['DocType']);
                        $("#prev_BillDocNum").html(inval['HEAD']['BillDocNum']);
                        $("#prev_BillCardCode").html(inval['HEAD']['BillCardCode']);
                        $("#prev_BillDocDate").html(inval['HEAD']['BillDocDate']);
                        $("#prev_BillDocDueDate").html(inval['HEAD']['BillDocDueDate']);
                        $("#prev_BillSlpName").html(inval['HEAD']['BillSlpName']);
                        $("#prev_BillOwnName").html(inval['HEAD']['BillOwnName']);
                        $("#prev_Attach").html(inval['HEAD']['Attach']);
                        $("#prev_ReturnReason").html(inval['HEAD']['ReturnReason']);
                        $("#prev_Incentive").html(inval['HEAD']['Incentive']);
                        $("#prev_Freebie").html(inval['HEAD']['Freebie']);
                        $("#prev_Remark").html(inval['HEAD']['Remark']);
                        $("#prev_SendType").html(inval['HEAD']['SendType']);
                        $("#prev_ShipCost").html(inval['HEAD']['ShipCost']);
                        $("#prev_FineSALE").html(inval['HEAD']['FineSALE']);
                        $("#prev_FineCOSA").html(inval['HEAD']['FineCOSA']);

                        /* ITEMLIST */
                        let ItemBody = "";
                        let ItemNo = 1;
                        for(i = 0; i < inval['HEAD']['Row']; i++) {
                            ItemBody +=
                                "<tr>"+
                                    "<td class='text-right'>"+ItemNo+"</td>"+
                                    "<td class='text-center'>"+inval['BODY'][i]['ItemCode']+"</td>"+
                                    "<td>"+inval['BODY'][i]['ItemName']+"</td>"+
                                    "<td class='text-center'>"+inval['BODY'][i]['ItemStatus']+"</td>"+
                                    "<td class='text-right'>"+inval['BODY'][i]['GrandPrice']+"</td>"+
                                    "<td class='text-center'>"+inval['BODY'][i]['Discount']+"</td>"+
                                    "<td class='text-right'>"+inval['BODY'][i]['UnitPrice']+"</td>"+
                                    "<td class='text-right'>"+inval['BODY'][i]['Quantity']+"</td>"+
                                    "<td>"+inval['BODY'][i]['UnitMsr']+"</td>"+
                                    "<td class='text-center'>"+inval['BODY'][i]['WhsCode']+"</td>"+
                                "</tr>";
                            ItemNo++;
                        }
                        $("table#prev_ItemList tbody").html(ItemBody);

                        /* APPROVE */
                        let AppBody = "";
                        let AppNo = 1;
                        for(i = 0; i < inval['APPROVE']['ROW']; i++) {
                            let AppState = "";
                            switch(inval['APPROVE'][i]['AppState']) {
                                case "0": AppState = "<span class='text-muted'><i class='far fa-times fa-fw fa-lg'></i> ไม่ต้องอนุมัติ</span>"; break;
                                case "1": AppState = "<span class='text-muted'><i class='far fa-clock fa-fw fa-lg'></i> รอพิจารณา</span>"; break;
                                case "Y": AppState = "<span class='text-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
                                case "N": AppState = "<span class='text-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
                            }
                            if(inval['APPROVE'][i]['APP'] == "Y") {
                                AppBody +=
                                    "<tr>"+
                                        "<td class='text-right'>"+AppNo+"</td>"+
                                        "<td>"+inval['APPROVE'][i]['NameReq']+"</td>"+
                                        "<td>"+
                                            "<select class='form-select form-select-sm' name='AppState_"+inval['APPROVE'][i]['ApproveID']+"' id='AppState_"+inval['APPROVE'][i]['ApproveID']+"'>"+
                                                "<option selected disabled>กรุณาเลือก</option>"+
                                                "<option value='Y'>อนุมัติ</option>"+
                                                "<option value='N'>ไม่อนุมัติ</option>"+
                                            "</select>"+
                                        "</td>"+
                                        "<td><input type='text' class='form-control form-control-sm' name='AppRemark_"+inval['APPROVE'][i]['ApproveID']+"' id='AppRemark_"+inval['APPROVE'][i]['ApproveID']+"' /></td>"+
                                        "<td>&nbsp;</td>"+
                                        "<td>&nbsp;</td>"+
                                        "<td><button type='button' class='btn btn-primary btn-sm w-100' onclick='AppSave("+inval['APPROVE'][i]['ApproveID']+")'><i class='fas fa-save fa-fw fa-1x'></i></button></td>"+
                                    "</tr>";

                            } else {
                                AppBody +=
                                    "<tr>"+
                                        "<td class='text-right'>"+AppNo+"</td>"+
                                        "<td>"+inval['APPROVE'][i]['NameReq']+"</td>"+
                                        "<td>"+AppState+"</td>"+
                                        "<td>"+inval['APPROVE'][i]['AppRemark']+"</td>"+
                                        "<td>"+inval['APPROVE'][i]['NameAct']+"</td>"+
                                        "<td class='text-center'>"+inval['APPROVE'][i]['AppDate']+"</td>"+
                                        "<td>&nbsp;</td>"+
                                    "</tr>";
                            }
                            
                            AppNo++;
                        }
                        $("table#prev_ApproveList tbody").html(AppBody);

                        /* Attachment */
                        let Attach = "";
                        for(i = 0; i < inval['ATTACH']['ROW']; i++) {
                            Attach += 
                                "<figure class='figure'>"+
                                    "<figcaption class='figure-caption'>"+inval['ATTACH'][i]['FileOriName']+"</figcaption>"+
                                    "<img class='figure-img img-thumbnail w-100 rounded' src='../FileAttach/RTQC/"+inval['ATTACH'][i]['FileDirName']+"'>"+
                                "</figure>";
                        }
                        $("#ImgAttach").html(Attach);

                        $("#ModalPreviewDoc").modal("show");
                    }
                    $("#prev_footer").html(inval['FOOT']);
                });
            }
        });
    }

    function ViewAttach(DocEntry) {
        $.ajax({
            url: "menus/sale/ajax/ajaxreturn_qc.php?p=ViewAttach",
            type: "POST",
            data: { DocEntry: DocEntry },
            success: function(result) {
                $("#ModalViewAttach").modal("show"); 
            }
        })
        
    }

    function CancelDoc(DocEntry) {
        $("#confirm_cancel").modal("show");
        $("#btn-cancel-confirm").on("click",function(e) {
            $.ajax({
                url: "menus/sale/ajax/ajaxreturn_qc.php?p=CancelDoc",
                type: "POST",
                data: { DocEntry: DocEntry },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key,inval) {
                        if(inval['Status'] == "SUCCESS") {
                            window.location.reload();
                        }
                    })
                }
            })
        });
    }

    function AppSave(AppID) {
        let AppState  = $("#AppState_"+AppID).val();
        let AppRemark = $("#AppRemark_"+AppID).val();
        $(".overlay").show();
        $.ajax({
            url: "menus/sale/ajax/ajaxreturn_qc.php?p=AppSave",
            type: "POST",
            data: {
                AppState: AppState,
                AppRemark: AppRemark,
                ApproveID: AppID
            },
            success: function(result) {
                $(".overlay").hide();
                $("#confirm_saved").modal('show');
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        })
    }

    function SendDoc(DocEntry) {
        $(".overlay").show();
        $.ajax({
            url: "menus/sale/ajax/ajaxreturn_qc.php?p=SendDoc",
            type: "POST",
            data: { DocEntry: DocEntry },
            success: function(result) {
                $(".overlay").hide();
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    if(inval['ROW'] == 0) {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่พบเอกสาร");
                        $("#alert_modal").modal('show');
                    } else {
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                    }
                });
                
                
            }
        })
    }

$(document).ready(function(){
    CallHead();
    CallDropDown();
    GetDocList(<?php echo date("Y"); ?>,<?php echo date("m"); ?>, $("#filt_team").val());
});

$("#filt_year, #filt_month, #filt_team").on("change", function() {
    let filt_y = $("#filt_year").val();
    let filt_m = $("#filt_month").val();
    let filt_t = $("#filt_team").val();
    GetDocList(filt_y,filt_m,filt_t);
});

$("#txt_RefDoc1").on("focusout", function(e) {
    let RefDoc1 = $(this).val();
    if(RefDoc1.length > 0 || RefDoc1 != "") {
        $("#txt_Att1").prop("checked",true);
    } else {
        $("#txt_Att1").prop("checked",false);
    }
});



/* CHANGE DOCUMENT TYPE */
$("#txt_DocType").on("change", function(e) {
    e.preventDefault();
    var DocType = $(this).val();
    $("#txt_BillDocNum, #txt_RefDoc2, #txt_BillDocNum2").attr("disabled",true);
    switch(DocType) {
        case "D":
        case "L": var RefDoc = "#txt_BillDocNum"; $("label[for='txt_BillDocNum2']").html("เลขที่บิลใหม่ (กรณีคืนบิลลอย)"); break;
        case "X": var RefDoc = "#txt_BillDocNum, #txt_RefDoc2, #txt_BillDocNum2"; $("label[for='txt_BillDocNum2']").html("เลขที่เอกสาร PC"); break;
        case "AC": var RefDoc = "#txt_BillDocNum, #txt_BillDocNum2"; $("label[for='txt_BillDocNum2']").html("เลขที่บิลใหม่ (กรณีคืนบิลลอย)"); break;
    }
    $(RefDoc).attr("disabled",false).val('').focus();
});

/* SEND TYPE */
$("#txt_SendType").on("change", function(e){
    e.preventDefault();
    var SendType = $(this).val();
    $("#txt_ShippingName").attr("disabled",true).val('');
    if(SendType == 1) { $("#txt_ShippingName").attr("disabled",false).val('').focus(); }
});

/* SHIP COST */
$("#txt_ShipCost").on("change", function(e) {
    e.preventDefault();
    var ShipCost = $(this).val();
    $("#txt_ShipCostBaht").attr("disabled",true).val('').change();
    $("#txt_ShipCostName").selectpicker('destroy').attr("disabled",true).val('').change().selectpicker();
    if(ShipCost == "Y") {
        $("#txt_ShipCostBaht").attr("disabled",false).val('').focus();
        $("#txt_ShipCostName").selectpicker('destroy').attr("disabled",false).val('').change().selectpicker();
    }
});

/* RETURN REASONS */
$("#txt_ReturnReason").on("change", function(e) {
    e.preventDefault();
    var RtReason = $(this).val();
    $("#txt_DeadStockType").attr("disabled",true).val('').change();
    if(RtReason == "1.5" || RtReason == 1.5) {
        $("#txt_DeadStockType").attr("disabled",false).val('').change();
    }
});

/* INCENTIVE */
$("#txt_Incentive").on("change", function(e) {
    e.preventDefault();
    var Incentive = $(this).val();
    $("#txt_Incentivebaht").attr("disabled",true).val('');
    if(Incentive == "Y") {
        $("#txt_Incentivebaht").attr("disabled",false).val('').focus();
    }
});

/* FINE COSA */
$("#txt_COSA_FineType").on("change", function(e) {
    e.preventDefault();
    var FineType = $(this).val();
    $("#txt_COSA_FineName").selectpicker('destroy').attr("disabled",true).val('').change().selectpicker();
    $("#txt_RefDoc3, #txt_RefDoc3No").val('').attr("disabled",true);
    if(FineType == "Y") {
        $("#txt_RefDoc3, #txt_RefDoc3No").attr("disabled",false).val('').focus();

        var OwnUkey = $("#txt_BillOwnerCode").val();
        if(OwnUkey == "") {
            $("#txt_COSA_FineName").selectpicker('destroy').attr("disabled",false).val('').change().selectpicker();
        } else {
            $("#txt_COSA_FineName").selectpicker('destroy').attr("disabled",false).val(OwnUkey).change().selectpicker();
        }
        
    }
});

$("#txt_SALE_FineType").on("change", function(e) {
    e.preventDefault();
    var FineType = $(this).val();
    $("#txt_SALE_FineName").selectpicker('destroy').attr("disabled",true).val('').change().selectpicker();
    $("#txt_RefDoc4, #txt_RefDoc4No").val('').attr("disabled",true);
    if(FineType == "Y") {
        $("#txt_RefDoc4, #txt_RefDoc4No").attr("disabled",false).val('').focus();

        var SlpUkey = $("#txt_BillSlpCode").val();
        if(SlpUkey == "") {
            $("#txt_SALE_FineName").selectpicker('destroy').attr("disabled",false).val('').change().selectpicker();
        } else {
            $("#txt_SALE_FineName").selectpicker('destroy').attr("disabled",false).val(SlpUkey).change().selectpicker();
        }
        
    }
});

$("#txt_BillDocNum").on("focusout", function(e) {
    e.preventDefault();
    let TextBox = $(this).val().replace("-","");
    let ErPoint = 0;
    let alert_body = "";
    if(TextBox.length == 0) {
        alert_body = "กรุณากรอกเลขที่บิลให้ครบถ้วน";
        // ErPoint++;
    } else {
        let Prefix   = TextBox.substring(0, 2).toUpperCase();
        let DocType  = $("#txt_DocType").val();
        let BillType = "";

        switch(DocType) {
            case "D":
                switch(Prefix) {
                    case "IV":
                    case "IC":
                    case "AA":
                    case "HA": SearchBill(TextBox, "OINV"); break;
                    default: alert_body = "กรุณากรอกเลขที่บิลให้ถูกต้อง<br/>(ขึ้นต้นด้วย IV, IC, AA, HA)"; ErPoint++; break;
                }
            break;
            case "L":
                switch(Prefix) {
                    case "PA":
                    case "PB":
                    case "PC":
                    case "PD":
                    case "PF": SearchBill(TextBox, "ODLN"); break;
                    default: alert_body = "กรุณากรอกเลขที่บิลให้ถูกต้อง<br/>(ขึ้นต้นด้วย PA, PB, PC, PD, PF)"; ErPoint++; break;
                }
            break;
            default:
                switch(Prefix) {
                    case "IV":
                    case "IC":
                    case "AA":
                    case "HA": SearchBill(TextBox, "OINV"); break;
                    case "PA":
                    case "PB":
                    case "PC":
                    case "PD":
                    case "PF": SearchBill(TextBox, "ODLN"); break;
                    default: alert_body = "กรุณากรอกเลขที่บิลให้ถูกต้อง<br/>(ขึ้นต้นด้วย IV, IC, AA, HA หรือ PA, PB, PC, PD, PF)"; ErPoint++; break;
                }
            break;
        }
    }
    if(ErPoint > 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html(alert_body);
        $("#alert_modal").modal('show');
        $(this).val("");
    }
});

function SaveDoc() {
    $("#QcRtForm input.is-invalid, #QcRtForm select.is-invalid").removeClass("is-invalid");

    let ErrArr   = [];
    let ErrPoint = 0;
    let txt_DocDate = $("#txt_DocDate").val();
    let txt_RefDoc1 = $("#txt_RefDoc1").val();
    let txt_DocType = $("#txt_DocType").val();
    const PostData = {}

    function AddPostData(key, value) {
        if(!PostData[key]) {
            PostData[key] = value;
        }
    }

    if(txt_DocDate == "" || txt_DocDate.length == 0) { $("#txt_DocDate").addClass("is-invalid"); ErrPoint++; ErrArr.push("วันที่เอกสาร"); } else { AddPostData("txt_DocDate", txt_DocDate); }
    if(txt_RefDoc1 == "" || txt_RefDoc1.length == 0) { $("#txt_RefDoc1").addClass("is-invalid"); ErrPoint++; ErrArr.push("เลขที่ใบมา"); } else { AddPostData("txt_RefDoc1", txt_RefDoc1); }
    if(txt_DocType == "" || txt_DocType == null) { $("#txt_DocType").addClass("is-invalid"); ErrPoint++; ErrArr.push("ประเภทเอกสาร"); } else {

        AddPostData("txt_DocType", txt_DocType);

        let txt_BillDocNum   = $("#txt_BillDocNum").val();
        let txt_BillDocType  = $("#txt_BillDocType").val();
        let txt_BillDocEntry = $("#txt_BillDocEntry").val();
        let txt_BillSAPVer   = $("#txt_BillSAPVer").val();
        let txt_BillTeamCode = $("#txt_BillTeamCode").val();

        /* Check Reference Invoice */
        if(txt_BillDocNum.length == 0 || txt_BillDocType.length == 0 || txt_BillDocEntry == 0) {
            $("#txt_BillDocNum").addClass("is-invalid"); ErrPoint++; ErrArr.push("เลขที่เอกสาร (บิล)");
        } else {
            AddPostData("txt_BillDocNum", txt_BillDocNum);
            AddPostData("txt_BillDocType", txt_BillDocType);
            AddPostData("txt_BillDocEntry", txt_BillDocEntry);
            AddPostData("txt_BillSAPVer", txt_BillSAPVer);
            AddPostData("txt_BillTeamCode", txt_BillTeamCode);
        }
        let txt_RefDoc2 = $("#txt_RefDoc2").val();
        let txt_BillDocNum2 = $("#txt_BillDocNum2").val();
        switch(txt_DocType) {
            case "X":
                if(txt_RefDoc2.length == 0) {
                    $("#txt_RefDoc2").addClass("is-invalid"); ErrPoint++; ErrArr.push("เลขที่เอกสาร FM-WH-17");
                } else {
                    AddPostData("txt_RefDoc2", txt_RefDoc2);
                }
                if(txt_BillDocNum2.length == 0) {
                    $("#txt_BillDocNum2").addClass("is-invalid"); ErrPoint++; ErrArr.push("เลขที่เอกสาร PC");
                } else {
                    AddPostData("txt_BillDocNum2", txt_BillDocNum2);
                }
            break;
            case "AC":
                if(txt_BillDocNum2.length == 0) {
                    $("#txt_BillDocNum2").addClass("is-invalid"); ErrPoint++; ErrArr.push("เลขที่บิลใหม่ (กรณีคืนบิลลอย)");
                } else {
                    AddPostData("txt_BillDocNum2", txt_BillDocNum2);
                }
            break;
        }
    }

    /* Check Send Return Type */
    let txt_SendType = $("#txt_SendType").val();
    if(txt_SendType == "" || txt_SendType == null) { $("#txt_SendType").addClass("is-invalid"); ErrPoint++; ErrArr.push("รูปแบบการส่งคืน"); } else {
        AddPostData("txt_SendType", txt_SendType);

        let txt_ShippingName = $("#txt_ShippingName").val();
        let txt_CoLogiName   = $("#txt_CoLogiName").val();

        if(txt_SendType == "1") {
            if(txt_ShippingName.length == 0) {
                $("#txt_ShippingName").addClass("is-invalid"); ErrPoint++; ErrArr.push("ชื่อขนส่ง");
            } else {
                AddPostData("txt_ShippingName", txt_ShippingName);
            }

            if(txt_CoLogiName == "" || txt_CoLogiName == null) {
                $("#txt_CoLogiName").addClass("is-invalid"); ErrPoint++; ErrArr.push("ธุรการขนส่งที่รับสินค้า");
            } else {
                AddPostData("txt_CoLogiName", txt_CoLogiName);
            }
        }
    }

    /* Check Shipping Cost */
    let txt_ShipCost = $("#txt_ShipCost").val();
    if(txt_ShipCost == "" || txt_ShipCost == null) { $("#txt_ShipCost").addClass("is-invalid"); ErrPoint++; ErrArr.push("ค่าขนส่ง"); } else {
        AddPostData("txt_ShipCost", txt_ShipCost);

        if(txt_ShipCost == "Y") {
            let txt_ShipCostBaht = $("#txt_ShipCostBaht").val();
            let txt_ShipCostName = $("#txt_ShipCostName").val();

            if(txt_ShipCostBaht.length == 0) {
                $("#txt_ShipCostBaht").addClass("is-invalid"); ErrPoint++; ErrArr.push("มูลค่าขนส่ง");
            } else {
                AddPostData("txt_ShipCostBaht", txt_ShipCostBaht);
            }

            if(txt_ShipCostName == "" || txt_ShipCostName == null) {
                $("#txt_ShipCostName").addClass("is-invalid"); ErrPoint++; ErrArr.push("ผู้รับผิดชอบค่าขนส่ง");
            } else {
                AddPostData("txt_ShipCostName", txt_ShipCostName);
            }
        }
    }

    /* Check Attachment */
    let txt_Att1  = $("#txt_Att1").is(":checked");
    let txt_Att2  = $("#txt_Att2").is(":checked");
    let txt_Att3  = $("#txt_Att3").is(":checked");
    AddPostData("txt_Att1", txt_Att1);
    AddPostData("txt_Att2", txt_Att2);
    AddPostData("txt_Att3", txt_Att3);
    let DocAttach = $("#DocAttach")[0].files;
    if(DocAttach.length == 0) {
        $("#DocAttach").addClass("is-invalid"); ErrPoint++; ErrArr.push("ภาพถ่ายสินค้า");
    }

    /* Check Return Detail */
    let txt_BillCardCode  = $("#txt_BillCardCode").val();
    let txt_BillSlpCode   = $("#txt_BillSlpCode").val();
    let txt_BillOwnerCode = $("#txt_BillOwnerCode").val();
    let txt_BillDate      = $("#txt_BillDate").val();
    let txt_BillDueDate   = $("#txt_BillDueDate").val();

    if(txt_BillCardCode == "" || txt_BillCardCode.length == 0) { $("#txt_BillCardCode").addClass("is-invalid"); ErrPoint++; ErrArr.push("ชื่อลูกค้า"); } else { AddPostData("txt_BillCardCode", txt_BillCardCode); }
    if(txt_BillSlpCode == "" || txt_BillSlpCode == null) { $("#txt_BillSlpCode").addClass("is-invalid"); ErrPoint++; ErrArr.push("ชื่อพนักงานขาย"); } else { AddPostData("txt_BillSlpCode", txt_BillSlpCode); }
    if(txt_BillOwnerCode == "" || txt_BillOwnerCode == null) { $("#txt_BillOwnerCode").addClass("is-invalid"); ErrPoint++; ErrArr.push("ชื่อธุรการขาย"); } else { AddPostData("txt_BillOwnerCode", txt_BillOwnerCode); }
    if(txt_BillDate == "" || txt_BillDate.length == 0) { $("#txt_BillDate").addClass("is-invalid"); ErrPoint++; ErrArr.push("วันที่เปิดบิล"); } else { AddPostData("txt_BillDate", txt_BillDate); }
    if(txt_BillDueDate == "" || txt_BillDueDate.length == 0) { $("#txt_BillDueDate").addClass("is-invalid"); ErrPoint++; ErrArr.push("วันที่กำหนดชำระ"); } else { AddPostData("txt_BillDueDate", txt_BillDueDate); }

    let txt_ReturnReason = $("#txt_ReturnReason").val();
    if(txt_ReturnReason == "" || txt_ReturnReason == null) { $("#txt_ReturnReason").addClass("is-invalid"); ErrPoint++; ErrArr.push("สาเหตุการคืน"); } else {
        AddPostData("txt_ReturnReason", txt_ReturnReason);
        if(txt_ReturnReason == "1.5") {
            let txt_DeadStockType = $("#txt_DeadStockType").val();
            if(txt_DeadStockType == "" || txt_DeadStockType == null) { $("#txt_DeadStockType").addClass("is-invalid"); ErrPoint++; ErrArr.push("ระยะเวลา Dead Stock"); } else { AddPostData("txt_DeadStockType", txt_DeadStockType); }
        }
    }
    
    let txt_FreeBie = $("#txt_FreeBie").val();
    AddPostData("txt_FreeBie", txt_FreeBie);

    let txt_Incentive = $("#txt_Incentive").val();
    if(txt_Incentive == "" || txt_Incentive == null) { $("#txt_Incentive").addClass("is-invalid"); ErrPoint++; ErrArr.push("การจ่ายค่า Incentive"); } else { AddPostData("txt_Incentive", txt_Incentive); }
    if(txt_Incentive == "Y") {
        let txt_Incentivebaht = $("#txt_Incentivebaht").val();
        if(txt_Incentivebaht <= 0) {
            $("#txt_Incentivebaht").addClass("is-invalid"); ErrPoint++; ErrArr.push("มูลค่า Incentive (บาท)");
        } else {
            AddPostData("txt_Incentivebaht", txt_Incentivebaht);
        }
    }

    /* FINE CO_SALE */
    let txt_COSA_FineType = $("#txt_COSA_FineType").val();
    AddPostData("txt_COSA_FineType", txt_COSA_FineType);
    if(txt_COSA_FineType == "Y") {
        let txt_COSA_FineName = $("#txt_COSA_FineName").val();
        let txt_RefDoc3       = $("#txt_RefDoc3").val();
        let txt_RefDoc3No     = $("#txt_RefDoc3No").val();

        if(txt_COSA_FineName == "" || txt_COSA_FineName == null) { $("#txt_COSA_FineName").addClass("is-invalid"); ErrPoint++; ErrArr.push("ชื่อธุรการเซลส์ที่โดนปรับ"); } else { AddPostData("txt_COSA_FineName", txt_COSA_FineName); }
        if(txt_RefDoc3 == "" || txt_RefDoc3.length == 0) { $("#txt_RefDoc3").addClass("is-invalid"); ErrPoint++; ErrArr.push("ใบวินัยธุรการเซลส์เลขที่"); } else { AddPostData("txt_RefDoc3", txt_RefDoc3); }
        if(txt_RefDoc3No == "" || txt_RefDoc3No.length == 0) { $("#txt_RefDoc3No").addClass("is-invalid"); ErrPoint++; ErrArr.push("ใบวินัยธุรการเซลส์ข้อที่"); } else { AddPostData("txt_RefDoc3No", txt_RefDoc3No); }
    }

    /* FINE SALE */
    let txt_SALE_FineType = $("#txt_SALE_FineType").val();
    AddPostData("txt_SALE_FineType", txt_SALE_FineType);
    if(txt_SALE_FineType == "Y") {
        let txt_SALE_FineName = $("#txt_SALE_FineName").val();
        let txt_RefDoc4       = $("#txt_RefDoc4").val();
        let txt_RefDoc4No     = $("#txt_RefDoc4No").val();

        if(txt_SALE_FineName == "" || txt_SALE_FineName == null) { $("#txt_SALE_FineName").addClass("is-invalid"); ErrPoint++; ErrArr.push("ชื่อธุรการเซลส์ที่โดนปรับ"); } else { AddPostData("txt_SALE_FineName", txt_SALE_FineName); }
        if(txt_RefDoc4 == "" || txt_RefDoc4.length == 0) { $("#txt_RefDoc4").addClass("is-invalid"); ErrPoint++; ErrArr.push("ใบวินัยเซลส์เลขที่"); } else { AddPostData("txt_RefDoc4", txt_RefDoc4); }
        if(txt_RefDoc4No == "" || txt_RefDoc4No.length == 0) { $("#txt_RefDoc4No").addClass("is-invalid"); ErrPoint++; ErrArr.push("ใบวินัยเซลส์ข้อที่"); } else { AddPostData("txt_RefDoc4No", txt_RefDoc4No); }
    }

    /* REMARK */
    let txt_COSA_Remark = $("#txt_COSA_Remark").val();
    AddPostData("txt_COSA_Remark",txt_COSA_Remark)

    /* ITEM LIST */
    let ItemCheckRow = parseInt($("#TotalRow").val());
    if(ItemCheckRow == 0) {
        ErrPoint++; ErrArr.push("เลือกสินค้าอย่างน้อย 1 รายการ");
    } else {
        let RID     = [];
        let ChkList = [].slice.call(document.querySelectorAll("#QCItemList tbody tr input.txtChkRow"));
        ChkList.forEach(input => { RID.push(input.value); });
        AddPostData("DataRow",RID);

        for(r = 0; r < RID.length; r++) {
            let txtSAGrade   = $("#txtSAGrade_"+RID[r]).val();
            let txtSAWhsCode = $("#txtSAWhsCode_"+RID[r]).val();
            let RowNo        = parseInt(RID[r]) + 1;
            let ItemDetail   = "";
            let txtVisOrder   = $("#txtVisOrder_"+RID[r]).val();
            let txtItemCode   = $("#txtItemCode_"+RID[r]).val();
            let txtCodeBars   = $("#txtCodeBars_"+RID[r]).val();
            let txtItemName   = $("#txtItemName_"+RID[r]).val();
            let txtItemStatus = $("#txtItemStatus_"+RID[r]).val();
            let txtQuantity   = $("#txtQuantity_"+RID[r]).val();
            let txtUnitMsr    = $("#txtUnitMsr_"+RID[r]).val();
            let txtWhsCode    = $("#txtWhsCode_"+RID[r]).val();
            let txtUnitPrice  = $("#txtUnitPrice_"+RID[r]).val();
            let txtGrandPrice = $("#txtGrandPrice_"+RID[r]).val();
            let txtDiscount   = $("#txtDiscount_"+RID[r]).val();

            ItemDetail =
            /* Pos 0 */ txtVisOrder+"::"+
            /* Pos 1 */ txtItemCode+"::"+
            /* Pos 2 */ txtCodeBars+"::"+
            /* Pos 3 */ txtItemName+"::"+
            /* Pos 4 */ txtItemStatus+"::"+
            /* Pos 5 */ txtQuantity+"::"+
            /* Pos 6 */ txtUnitMsr+"::"+
            /* Pos 7 */ txtWhsCode+"::"+
            /* Pos 8 */ txtUnitPrice+"::"+
            /* Pos 9 */ txtGrandPrice+"::"+
            /* Pos 10 */ txtDiscount;
            AddPostData("ItemRow_"+RID[r],ItemDetail);
        }
    }

    if(ErrPoint > 0) {
        let alert_body = "กรุณากรอกข้อมูลให้ครบถ้วน";
        if(ErrArr.length > 0) {
            alert_body += "<br/><span class='text-danger'>";
            for(a = 0; a < ErrArr.length; a++) { alert_body += "&#9679; "+ErrArr[a]+"<br/>"; }
        }
        alert_body += "</span>";
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html(alert_body);
        $("#alert_modal").modal('show');
    } else {
        var Form_Data = new FormData();
        for(var key in PostData) {
            Form_Data.append(key, PostData[key]);
        }
        $.each($("#DocAttach"), function(i, obj) {
            $.each(obj.files, function(j, file) {
                Form_Data.append('DocAttach['+j+']',file);
            });
        });

        $(".overlay").show();

        $.ajax({
            url: "menus/sale/ajax/ajaxreturn_qc.php?p=SaveDoc",
            type: "POST",
            data: Form_Data,
            processData : false,
            contentType : false,
            success: function(result) {
                $(".overlay").hide();
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    if(inval['Status'] == "SUCCESS") {
                        
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                    }
                })
            }
        });
    }
}

function PrintDoc(DocEntry) {
    // const Year = $("#filt_year").val();
    // const Month = $("#filt_month").val();
    // const Team = $("#filt_team").val();
    window.open ('menus/sale/print/print_return_qc.php?DocEntry='+DocEntry,'_blank');
}
</script> 
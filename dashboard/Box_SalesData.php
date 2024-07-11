<style>
    .ViewDetailSM {
        color: #515151;
    }
</style>
<div class="card h-100">
    <div class="card-header">
        <h4><i class='fas fa-dollar-sign fa-fw fa-1x'></i> ข้อมูลการขาย</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-tab='tab1' id='IDtab1' href="#tab1">สินค้าคงคลัง</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link " data-bs-toggle="tab" data-tab='tab2' id='IDtab2' href="#tab2">ข้อมูลร้านค้า</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link " data-bs-toggle="tab" data-tab='tab3' id='IDtab3' href="#tab3">ประวัติสินค้า</button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-lg-12">
                <div class="tab-content">
                    
                    <div id="tab1" class="tab-pane active">
                        <div class='d-flex align-items-center'>
                            <span class='text-primary pe-2'><i class="fas fa-search pe-1"></i>ค้นหาสินค้าคงคลัง</span>
                            <div style='width: 500px;'>
                                <select class="form-control form-control-sm" id="ItemCode1" data-live-search="true" onchange="SelectItemCode();">
                                    <option value="" selected disabled>กรุณาเลือกรายการสินค้า</option>
                                </select>
                            </div>
                        </div>
                        <div class='table-responsive pt-3'>
                            <table class='table table-sm table-bordered table-hover rounded rounded-3 overflow-hidden' id='show_tab1'>
                                <thead class='text-center text-light' style='background-color: #9A1118; font-size: 13px;'>
                                    <tr>
                                        <th width='3%'>ลำดับ</th>
                                        <th width='10%'>รหัสสินค้า</th>
                                        <th width='14%'>บาร์โค้ด</th>
                                        <th>ชื่อสินค้า</th>
                                        <th width='8%'>คลังสินค้า</th>
                                        <th width='8%'>คงคลัง</th>
                                        <th width='8%'>กำลังสั่งซื้อ</th>
                                        <th width='8%'>จำนวนที่ใช้ได้</th>
                                        <th width='8%'>หน่วย</th>
                                        <?php if (($_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 19) OR $_SESSION['DeptCode'] == 'DP003'){
                                            echo "<th width='10%'>ราคาทุนล่าสุด</th>";
                                        } ?>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;'>
                                    <tr>
                                        <?php if (($_SESSION['uClass'] == 18 OR $_SESSION['uClass'] == 63 OR $_SESSION['uClass'] == 19) OR $_SESSION['DeptCode'] == 'DP003'){
                                            echo "<td colspan='10' class='text-center'>ไม่มีข้อมูล :)</td>";
                                        }else{
                                            echo "<td colspan='9' class='text-center'>ไม่มีข้อมูล :)</td>";
                                        } ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div id="tab2" class="tab-pane fade">
                        <div class='d-flex align-items-center'>
                            <span class='text-primary pe-2'><i class="fas fa-search pe-1"></i>ค้นหาข้อมูลร้านค้า</span>
                            <div style='width: 500px;'>
                                <select class="form-control form-control-sm" id="CardCode" data-live-search="true" onchange="SelectCardCode();">
                                    <option value="" selected disabled>เลือกข้อมูลร้านค้า</option>
                                </select>
                            </div>
                        </div>
                        <div class='d-flex align-items-center justify-content-end pt-3'>
                            <span class='text-primary fw-bolder' id='CusName'></span>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class='d-flex align-items-center pt-3'>
                                    <span class='fw-bolder'>ยอดขายรายเดือนของร้านค้า (ปีปัจจุบัน และย้อนหลัง 1 ปี) (บาท)</span>
                                </div>
                                <div class='table-responsive pt-2'>
                                    <table class='table table-sm table-bordered table-hover rounded rounded-3 overflow-hidden' id='show_tab2_1'>
                                        <thead class='text-center text-light' style='background-color: #9A1118; font-size: 13px;'>
                                            <tr>
                                                <?php
                                                echo "<th>ปี</th>";
                                                for($m = 1; $m <= 12; $m++) {
                                                    echo "<th>".FullMonth($m)."</th>";
                                                } 
                                                echo "<th>รวมทั้งหมด</th>";
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody style='font-size: 12px;'>
                                            <tr>
                                                <?php
                                                    echo "<td class='text-center'>".date("Y")."</td>";
                                                    for($m = 1; $m <= 12; $m++) {
                                                        echo "<td></td>";
                                                    } 
                                                    echo "<td></td>";
                                                ?>
                                            </tr>
                                            <tr>
                                                <?php
                                                    echo "<td class='text-center'>".(date("Y")-1)."</td>";
                                                    for($m = 1; $m <= 12; $m++) {
                                                        echo "<td></td>";
                                                    } 
                                                    echo "<td></td>";
                                                ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class='d-flex align-items-center pt-3'>
                                    <span class='fw-bolder'>สินค้าขายดี 20 อันดับ</span>
                                </div>
                                <div class="table-responsive pt-2">
                                    <table class='table table-sm table-bordered table-hover rounded rounded-3 overflow-hidden' id='show_tab2_2'>
                                        <thead class='text-center text-light' style='background-color: #9A1118; font-size: 13px;'>
                                            <tr>
                                                <th rowspan='2' width='3%' class='align-bottom'>ลำดับ</th>
                                                <th rowspan='2' width='8%' class='align-bottom'>รหัสสินค้า</th>
                                                <th rowspan='2' width='11%' class='align-bottom'>บาร์โค้ด</th>
                                                <th rowspan='2' class='align-bottom'>ชื่อสินค้า</th>
                                                <th rowspan='2' width='7%' class='align-bottom'>หน่วย</th>
                                                <th colspan='2'><?php echo date("Y")-1; ?></th>
                                                <th colspan='2'><?php echo date("Y"); ?></th>
                                            </tr>
                                            <tr>
                                                <th width='8%'>จำนวน</th>
                                                <th width='8%'>มูลค่าสินค้า</th>
                                                <th width='8%'>จำนวน</th>
                                                <th width='8%'>มูลค่าสินค้า</th>
                                            </tr>
                                        </thead>
                                        <tbody style='font-size: 12px' id=''>
                                            <tr>
                                                <td colspan='7' class='text-center'>ไม่มีข้อมูล :)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class='d-flex align-items-center pt-3'>
                                    <span class='fw-bolder'>รายการขาย 10 รายการล่าสุด</span>
                                </div>
                                <div class="table-responsive pt-2">
                                    <table class='table table-sm table-bordered table-hover rounded rounded-3 overflow-hidden' id='show_tab2_3'>
                                        <thead class='text-center text-light' style='background-color: #9A1118; font-size: 13px;'>
                                            <tr>
                                                <th colspan='3'>รายการขาย 10 รายการล่าสุด</th>
                                                <th colspan='3'>รายการคืน 10 รายการล่าสุด</th>
                                            </tr>
                                            <tr>
                                                <th width='10%'>วันที่</th>
                                                <th width='25%'>เลขที่เอกสาร</th>
                                                <th width='15%'>ยอดบิล</th>
                                                <th width='10%'>วันที่</th>
                                                <th width='25%'>เลขที่เอกสาร</th>
                                                <th width='15%'>ยอดบิล</th>
                                            </tr>
                                        </thead>
                                        <tbody style='font-size: 12px'>
                                            <tr>
                                                <td colspan='6' class='text-center'>ไม่มีข้อมูล :)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>   
                            </div>
                        </div>
                    </div>
                    
                    <div id="tab3" class="tab-pane fade">
                        <div class='d-flex align-items-center justify-content-between'>
                            <div class='d-flex align-items-center'>
                                <span class='text-primary pe-2'><i class="fas fa-search pe-1"></i>ค้นหาประวัติสินค้า</span>
                                <div class='pe-2' style='width: 450px;'>
                                    <select class="form-control form-control-sm"  id="ItemCode2" data-live-search="true" onchange="SelectCardCodeHis();">
                                        <option value="" selected disabled>กรุณาเลือกรายการสินค้า</option>
                                    </select>
                                </div>
                                <select class="form-select form-select-sm me-2" style='width: 100px;' id="MonthHis" onchange="SelectCardCodeHis();">
                                    <?php
                                    for($m = 1; $m <= 12; $m++) {
                                        if($m == date("m")) {
                                            echo "<option value='".$m."' selected>".FullMonth($m)."</option>";
                                        }else{
                                            echo "<option value='".$m."'>".FullMonth($m)."</option>";
                                        }
                                    } 
                                    ?>
                                </select>                 
                                <select class="form-select form-select-sm" style='width: 100px;' id="YearHis" onchange="SelectCardCodeHis();">
                                    <?php
                                    for($y = date("Y"); $y >= 2020; $y--) {
                                        if($y == date("Y")) {
                                            echo "<option value='".$y."' selected>".$y."</option>";
                                        }else{
                                            echo "<option value='".$y."'>".$y."</option>";
                                        }
                                    } 
                                    ?>
                                </select>      
                            </div>
                            <div class='d-flex align-items-center'>
                                <button class='btn btn-sm btn-success' onclick='Excel();'><i class="fas fa-file-excel fa-fw"></i> Excel</button>
                            </div>
                        </div>
                        <div class="row pt-3">
                            <div class="col-lg">
                                <div class='table-responsive'>
                                    <table class='table table-sm table-bordered table-hover rounded rounded-3 overflow-hidden' id='show_tab3'>
                                        <thead class='text-center text-light' style='background-color: #9A1118; font-size: 13px;'>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>วันที่</th>
                                                <th>ชื่อร้านค้า</th>
                                                <th>CH</th>
                                                <th>พนักงานขาย</th>
                                                <th>เลขที่เอกสาร</th>
                                                <th>จำนวน</th>
                                                <th>หน่วย</th>
                                                <th>ราคาขาย</th>
                                                <th>ราคารวม</th>
                                            </tr>
                                        </thead>
                                        <tbody style='font-size: 12px;'>
                                            <tr>
                                                <td colspan='10' class='text-center'>ไม่มีข้อมูล :)</td>
                                            </tr>
                                        </tbody>
                                        <tfoot style='font-size: 12px;'>
                                        </tfoot>
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

<div class="modal fade" id="ModalBill" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดการขาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ORDER HEADER -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tr class="align-middle">
                                <th width="10%">ชื่อลูกค้า</th>
                                <td colspan="3"><input type="text" class="form-control-plaintext form-control-sm" id="view_CardName" readonly /></td>
                                <th width="10%">เลขที่ใบขาย</th>
                                <td width="10%"><input type="text" class="form-control-plaintext form-control-sm" id="view_DocNum" readonly /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานขาย</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="view_SlpName" readonly /></td>
                                <th width="10%">วันที่เอกสาร</th>
                                <td width="10%"><input type="text" class="form-control-plaintext form-control-sm" id="view_Date" readonly /></td>
                                <th>เลขที่ PO</th>
                                <td><input type="text" class="form-control-plaintext form-control-sm" id="view_PO" readonly /></td>
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
                                <table class="table table-bordered table-hover table-sm" id="view_Table" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th width="10%">รหัสสินค้า</th>
                                            <th>ชื่อสินค้า</th>
                                            <th width="10%">จำนวน</th>
                                            <th width="5%">หน่วยนับ</th>
                                            <th width="10%">ราคา/หน่วย</th>
                                            <th width="15%">ส่วนลด</th>
                                            <th width="10%">มูลค่าสินค้า</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="text-right">
                                        <tr>
                                            <th colspan="7">ราคาก่อนหักภาษีมูลค่าเพิ่ม</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_DocTotal" id="view_DocTotal" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="7">ภาษีมูลค่าเพิ่ม</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_VatSum" id="view_VatSum" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="7">ราคาสุทธิ</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_SumTotal" id="view_SumTotal" style="font-weight: bold;" readonly /></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalReBill" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดการคืน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ORDER HEADER -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tr class="align-middle">
                                <th>ชื่อลูกค้า</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="viewRe_CardName" readonly /></td>
                                <th width="10%">วันที่เอกสาร</th>
                                <td width="15%"><input type="text" class="form-control-plaintext form-control-sm" id="viewRe_Date" readonly /></td>
                                <th>เลขที่ใบลดหนี้</th>
                                <td><input type="text" class="form-control-plaintext form-control-sm" id="viewRe_DocNum" readonly /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานขาย</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="viewRe_SlpName" readonly /></td>
                                <th width="10%">อ้างอิงใบกำกับฯ</th>
                                <td width="15%"><input type="text" class="form-control-plaintext form-control-sm" id="viewRe_Code" readonly /></td>
                                <th>Customer Ref.</th>
                                <td><input type="text" class="form-control-plaintext form-control-sm" id="viewRe_DocNo" readonly /></td>
                            </tr>
                            <tr class="align-middle">
                                <th>อ้างอิง</th>
                                <td width="35%" colspan='5'><input type="text" class="form-control-plaintext form-control-sm" id="viewRe_CusRef" readonly /></td>
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
                                <table class="table table-bordered table-hover table-sm" id="viewRe_Table" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th width="10%">รหัสสินค้า</th>
                                            <th>ชื่อสินค้า</th>
                                            <th width="10%">จำนวน</th>
                                            <th width="5%">หน่วยนับ</th>
                                            <th width="10%">ราคา/หน่วย</th>
                                            <th width="15%">ส่วนลด</th>
                                            <th width="10%">มูลค่าสินค้า</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="text-right">
                                        <tr>
                                            <td colspan='4' rowspan='3'>
                                                <div class='form-floating'>
                                                    <textarea class='form-control' id='viewRe_Remark' style='height: 119px; font-size: 12.5px;' readonly></textarea>
                                                    <label for=''>สาเหตุการคืน</label>
                                                </div>
                                            </td>
                                            <th colspan="3">ราคาก่อนหักภาษีมูลค่าเพิ่ม</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="viewRe_DocTotal" id="viewRe_DocTotal" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3">ภาษีมูลค่าเพิ่ม</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="viewRe_VatSum" id="viewRe_VatSum" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="3">ราคาสุทธิ</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="viewRe_SumTotal" id="viewRe_SumTotal" style="font-weight: bold;" readonly /></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDetailAvailable" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียด SO ที่รอเปิด</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' id='tbDetailAvailable'>
                                <thead>
                                    <tr class='text-center'>
                                        <th>เลขที่เอกสาร</th>
                                        <th>วันที่เอกสาร</th>
                                        <th>รหัสลูกค้า</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th>พนักงานขาย</th>
                                        <th>ทีม</th>
                                        <th>SO รอเปิด</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

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
                                <td><input type="text" class="form-control-plaintext form-control-sm" id="view_CardName2" readonly /></td>
                                <th width="10%">วันที่คลังรับออเดอร์</th>
                                <td width="10%"><input type="datetime-local" class="form-control form-control-sm text-danger" id="view_DateCreate" readonly /></td>
                                <th width="10%">เลขที่ใบสั่งขาย</th>
                                <td width="10%">
                                    <input type="text" class="form-control-plaintext form-control-sm" id="view_DocNum2" readonly />
                                    <input type="hidden" class="form-control" id="view_PickID" name="view_PickID" readonly />
                                    <input type="hidden" class="form-control" id="view_DocEntry" name="view_DocEntry" readonly />
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานขาย</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="view_SlpName2" readonly /></td>
                                <th width="10%">วันที่สั่งของ</th>
                                <td width="10%"><input type="date" class="form-control form-control-sm" name="view_DocDate" id="view_DocDate" readonly /></td>
                                <th>วันที่กำหนดส่ง</th>
                                <td><input type="date" class="form-control form-control-sm" name="view_DocDueDate" id="view_DocDueDate" min="<?php echo date("Y-m-d"); ?>" disabled/></td>
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
                                    <input type="text" class="form-control-plaintext form-control-sm" name="view_PickerName" id="view_PickerName" readonly />
                                </td>
                                <th>โต๊ะแพ็ก</th>
                                <td>
                                    <select class="form-select form-select-sm" name="view_TablePack" id="view_TablePack" disabled>
                                        <option selected disabled>รอจัดสรร</option>
                                        <option value="1">โต๊ะ 1</option>
                                        <option value="2">โต๊ะ 2</option>
                                        <option value="3">โต๊ะ 3</option>
                                        <option value="4">โต๊ะ 4</option>
                                        <option value="5">โต๊ะ 5</option>
                                    </select>
                                </td>
                                <td colspan="2"></td>
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
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_DocTotal2" id="view_DocTotal2" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="9">ภาษีมูลค่าเพิ่ม</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_VatSum2" id="view_VatSum2" readonly /></td>
                                        </tr>
                                        <tr>
                                            <th colspan="9">ราคาสุทธิ</th>
                                            <td><input type="text" class="form-control-plaintext form-control-sm text-right" name="view_SumTotal2" id="view_SumTotal2" style="font-weight: bold;" readonly /></td>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalViewOnOrder" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดการสั่งซื้อสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' id='TableViewOnOrder' style='font-size: 12px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th rowspan='2' width='5%' class='text-center'>No.</th>
                                        <th rowspan='2' width='6%' class='text-center'>รหัสสินค้า</th>
                                        <th rowspan='2' width='20.5%' class='text-center'>ชื่อสินค้า</th>
                                        <th rowspan='2' width='5%' class='text-center'>สถานะสินค้า</th>
                                        <th rowspan='2' width='7%' class='text-center'>วันที่เปิด PO</th>
                                        <th rowspan='2' width='7%' class='text-center'>ประมาณการ<br>สินค้าคลัง KBI</th>
                                        <th rowspan='2' width='7%' class='text-center'>อ้างอิง PO</th>
                                        <th rowspan='2' width='5%' class='text-center'>จำนวน</th>
                                        <th rowspan='2' width='5%' class='text-center'>หน่วย</th>
                                        <th rowspan='2' width='5%' class='text-center'>คลังสินค้า</th>
                                        <th colspan='5' class='text-center'>โควต้าทีม</th>
                                    </tr>
                                    <tr>
                                        <th width='5.5%' class='text-center'>MT1</th>
                                        <th width='5.5%' class='text-center'>MT2</th>
                                        <th width='5.5%' class='text-center'>TT2</th>
                                        <th width='5.5%' class='text-center'>OUL</th>
                                        <th width='5.5%' class='text-center'>ONL</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <small>วันที่อัพเดตข้อมูล : <span id='DateCreate'></span></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalViewDetailSaleMonth" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดยอดขาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class='table table-sm table-hover table-bordered' id='TableDetailSaleMonth' style='font-size: 13px;'>
                        <thead>
                            <tr class='text-center'>
                                <th>No.</th>
                                <th>รหัสสินค้า</th>
                                <th>ชื่อสินค้า</th>
                                <th>เลขที่เอกสาร</th>
                                <th>ราคา</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
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

    $.ajax({
        url: "../json/OITM.json",
        cache: false,
        success: function(result) {
            var filt_data = 
                result.
                    filter(x => x.ItemStatus == "A").
                    sort(function(key, inval) {
                        return key.ItemCode.localeCompare(inval.ItemCode);
                    });

            var opt = "";

            $.each(filt_data, function(key, inval) {
                opt += "<option value='"+inval.ItemCode+"'>"+inval.ItemCode+" | ["+inval.ProductStatus+"] - "+inval.ItemName+" ["+inval.BarCode+"]</option>";
            });
            $("#ItemCode1").append(opt).selectpicker();
            $("#ItemCode2").append(opt).selectpicker();
        }
    });

    $.ajax({
        url: "../json/OCRD.json",
        cache: false,
        success: function(result) {
            var filt_data = 
                result.
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

    function SelectItemCode() {
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=SelectItemCode",
            type: "POST",
            data: { ItemCode : $("#ItemCode1").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#show_tab1 tbody").html(inval['tbody']);
                });
            }
        })
    }

    function DetailAvailable(ItemCode,WhsCode) {
        // console.log(ItemCode, WhsCode);
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=DetailAvailable",
            type: "POST",
            data: { ItemCode : ItemCode, WhsCode : WhsCode, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#tbDetailAvailable tbody").html(inval['tbody']);
                    $("#ModalDetailAvailable").modal("show");
                });
            }
        })
    }

    function ViewDetail(DocEntry,ItemCode) {
        console.log(ItemCode);
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=ViewDetail",
            type: "POST",
            data: { DocEntry : DocEntry, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    console.log(inval['HD']['CardCode']);
                    /* HEADER */
                    $("#view_PickID").val(inval['HD']['PickID']);
                    $("#view_DocEntry").val(inval['HD']['DocEntry']);
                    $("#view_DocNum2").val(inval['HD']['SODocNum']);
                    $("#view_CardName2").val(inval['HD']['CardCode']);
                    $("#view_DateCreate").val(inval['HD']['DateCreate']);
                    $("#view_SlpName2").val(inval['HD']['SlpName']);
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
                    let bg = "";
                    for(i = 0; i < r; i++) {
                        var Discount = "";
                        if(inval['BD_'+i]['Discount'] != null) {
                            Discount = inval['BD_'+i]['Discount'];
                        }
                        bg = "";
                        if(inval['BD_'+i]['ItemCode'] == ItemCode) {
                            bg = "table-danger";
                        }
                        row +=
                            "<tr class='"+bg+"'>"+
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
                    $("#view_DocTotal2").val(number_format(SumTotal,2));
                    $("#view_VatSum2").val(number_format(inval['FT']['VatSum'],2));
                    $("#view_SumTotal2").val(number_format(inval['FT']['DocTotal'],2));
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
        $("#order-tabs .nav-item a.btn-tabs#view_ItemTab").tab("show");
        $("#PreviewSO").modal("show");
    }

    function SelectCardCode() {
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=SelectCardCode",
            type: "POST",
            data: { CardCode : $("#CardCode").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#CusName").html(inval['CusName']);
                    $("#show_tab2_1 tbody").html(inval['Tbody1']);
                    $("#show_tab2_2 tbody").html(inval['Tbody2']);
                    $("#show_tab2_3 tbody").html(inval['Tbody3']);
                });

                $(".Modal-Bill").on("click", function(){
                    var BillEntry = $(this).attr("databill-entry");
                    $.ajax({
                        url: "dashboard/ajax/ajaxAllBox.php?a=ModalBill",
                        type: "POST",
                        data: { BillEntry : BillEntry, },
                        success: function(result) {
                            var obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) {
                                $("#view_CardName").val(inval['H_CardName']);
                                $("#view_DocNum").val(inval['H_DocNum']);
                                $("#view_SlpName").val(inval['H_SlpName']);
                                $("#view_Date").val(inval['H_DocDate']);
                                $("#view_PO").val(inval['H_U_PONo']);
                                $("#view_Table tbody").html(inval['Tbody']);
                                $("#view_DocTotal").val(inval['DocTotal']);
                                $("#view_VatSum").val(inval['VatSum']);
                                $("#view_SumTotal").val(inval['Total']);
                                $("#ModalBill").modal("show");
                            });
                        }
                    })
                });

                $(".Modal-ReBill").on("click", function(){
                    var BillEntry = $(this).attr("datarebill-entry");
                    console.log(BillEntry);
                    $.ajax({
                        url: "dashboard/ajax/ajaxAllBox.php?a=ModalReBill",
                        type: "POST",
                        data: { BillEntry : BillEntry, },
                        success: function(result) {
                            var obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) {
                                $("#viewRe_CardName").val(inval['CusName']);
                                $("#viewRe_Date").val(inval['Date']);
                                $("#viewRe_DocNum").val(inval['DucNum']);

                                $("#viewRe_SlpName").val(inval['SlpName']);
                                $("#viewRe_Code").val(inval['RefInv']);
                                $("#viewRe_DocNo").val(inval['SR']);

                                $("#viewRe_CusRef").val(inval['CusRef']);
                                $("#viewRe_Table tbody").html(inval['Tbody']);
                                $("#viewRe_Remark").val(inval['Remark']);
                                $("#viewRe_DocTotal").val(inval['DocTotal']);
                                $("#viewRe_VatSum").val(inval['VatSum']);
                                $("#viewRe_SumTotal").val(inval['Total']);
                                $("#ModalReBill").modal("show");
                            });
                        }
                    })
                });
            }
        })
    }

    function ViewDetailSaleMonth(Year,Month) {
        const CardCode = $("#CardCode").val();
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=ViewDetailSaleMonth",
            type: "POST",
            data: { Year: Year, Month: Month, CardCode: CardCode },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TableDetailSaleMonth tbody").html(inval['tbody']);
                    $("#ModalViewDetailSaleMonth").modal("show");
                });
            }
        })
    }

    function SelectCardCodeHis() {
        var Month     = $("#MonthHis").val();
        var Year      = $("#YearHis").val();
        var ItemCode  = $("#ItemCode2").val();
        if(ItemCode != null) {
            $.ajax({
                url: "dashboard/ajax/ajaxAllBox.php?a=SelectCardCodeHis",
                type: "POST",
                data: { ItemCode : ItemCode, Year : Year, Month : Month, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        var Row = 1;
                        var Tbody = "";
                        var Tfoot = "";
                        if(inval['Row'] != 0) {
                            for(var r = 0; r < inval['Row']; r++) {
                                Tbody +="<tr>"+
                                            "<td class='text-center'>"+Row+"</td>"+
                                            "<td class='text-center'>"+inval["Tbody"]["DocDate"][r]+"</td>"+
                                            "<td>"+inval["Tbody"]["CardName"][r]+"</td>"+
                                            "<td class='text-center'>"+inval["Tbody"]["CH"][r]+"</td>"+
                                            "<td>"+inval["Tbody"]["SlpName"][r]+"</td>"+
                                            "<td class='text-center'>"+inval["Tbody"]["NumAtCard"][r]+"</td>"+
                                            "<td class='text-center'>"+inval["Tbody"]["Quantity"][r]+"</td>"+
                                            "<td class='text-center'>"+inval["Tbody"]["Unit"][r]+"</td>"+
                                            "<td class='text-right'>"+inval["Tbody"]["Price"][r]+"</td>"+
                                            "<td class='text-right'>"+inval["Tbody"]["Total"][r]+"</td>"+
                                        "</tr>";
                                Row++;
                            }
                            Tfoot +="<tr>"+
                                        "<td class='text-right fw-bolder' colspan='6'>จำนวนรวมทั้งหมด</td>"+
                                        "<td class='text-center fw-bolder'>"+inval["Quantity"]+"</td>"+
                                        "<td class='text-center fw-bolder'>"+inval["Tbody"]["Unit"][0]+"</td>"+
                                        "<td class='text-right fw-bolder'>ราคารวมทั้งหมด</td>"+
                                        "<td class='text-right fw-bolder'>"+inval["Total"]+"</td>"+
                                    "</tr>";
                        }else{
                            Tbody +="<tr>"+
                                        "<td colspan='9' class='text-center'>ไม่มีข้อมูล :)</td>"+
                                    "</tr>";
                        }
                        $("#show_tab3 tbody").html(Tbody);
                        $("#show_tab3 tfoot").html(Tfoot);
                    });
                }
            })
        }
    }

    function Excel() {
        let Month    = $("#MonthHis").val();
        let Year     = $("#YearHis").val();
        let ItemCode = $("#ItemCode2").val();
        if(ItemCode != null) {
            $.ajax({
                url: "dashboard/ajax/ajaxAllBox.php?a=ExportCardCodeHis",
                type: "POST",
                data: { ItemCode : ItemCode, Year : Year, Month : Month, },
                success: function(result) {
                    let obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        window.open("../FileExport/HisSalesProduct/"+inval['FileName'],'_blank');
                    })
                }
            });
        }
    }

    function ViewOnOrder(ItemCode) {
        console.log(ItemCode);
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=ViewOnOrder",
            type: "POST",
            data: { ItemCode : ItemCode },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#DateCreate").html(inval['DateCreate']);
                    $("#TableViewOnOrder tbody").html(inval['Data']);
                    $("#ModalViewOnOrder").modal("show");
                })
            }
        });
    }
</script>

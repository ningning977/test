<?php
    $start_year = 2023;
    $this_year  = date("Y");
?>
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
                                <button class="nav-link text-primary active" id="Tab1-tab" onclick='ShowTar();' data-bs-toggle="tab" data-bs-target="#Tab1" type="button" role="tab" aria-controls="Tab1" aria-selected="false"><i class="fas fa-list fa-fw fa-1x"></i> รายการเป้าสินค้า</button>
                                <button class="nav-link text-primary" id="Tab3-tab" onclick='TarSummary();' data-bs-toggle="tab" data-bs-target="#Tab3" type="button" role="tab" aria-controls="Tab3" aria-selected="false"><i class="fas fa-table fa-fw fa-1x"></i> สรุปภาพรวม</button>
                                <button class="nav-link text-primary " id="Tab2-tab" onclick='' data-bs-toggle="tab" data-bs-target="#Tab2" type="button" role="tab" aria-controls="Tab2" aria-selected="false"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่ม/แก้ไขเป้าสินค้าใหม่</button>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg">
                        <div class="tab-content pt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="Tab1" role="tabpanel" aria-labelledby="Tab1-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="table-responsive" style='height: 750px;'>
                                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='TableShowTar'>
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
                                                        <th class='text-center border-top'>จัดการ</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="Tab2" role="tabpanel" aria-labelledby="Tab2-tab">
                                <form class='' id='Step1'>
                                    <div class="row mt-4">
                                        <div class="col-lg-12">
                                            <h4 class="h4">Step 1: ข้อมูลเป้าขายสินค้า และกำหนดการวัดผล</h4>
                                            <small>
                                                <span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)<br/>
                                            </small>
                                        </div>
                                    </div>
                                    <input type="hidden" name='CPEntry' id='CPEntry' value='0'>
                                    <input type="hidden" name='CPDocNum' id='CPDocNum' value='0'>
                                    <div class="row mt-4">
                                        <div class="col-5">
                                            <div class="form-group mb-3">
                                                <label for="CPTitle">ชื่อเป้าขายสินค้า<span class='text-danger'>*<span></label>
                                                <input type="text" class='form-control form-control-sm' name='CPTitle' id='CPTitle' placeholder="กรุณากรอกชื่อ Campaign" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group mb-3">
                                                <label for="TeamCode">เลือกทีมขาย <span class='text-danger'>*<span></label>
                                                <select class='form-select form-select-sm' name="TeamCode" id="TeamCode">
                                                    <option value="" selected disabled>เลือกทีมขาย</option>
                                                    <option value="MT1">โมเดิร์นเทรด 1</option>
                                                    <option value="MT2">โมเดิร์นเทรด 2</option>
                                                    <option value="TT2">ร้านค้าเขตต่างจังหวัด</option>
                                                    <option value="OUL">หน้าร้าน + ร้านค้าเขตกรุงเทพฯ</option>
                                                    <option value="ONL">ออนไลน์</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group mb-3">
                                                <label for="MngType">รูปแบบวัดผล <span class='text-danger'>*<span></label>&nbsp;<small class="text-muted" style='font-size: 12px;'>(รายบุคคลเลือกได้มากกว่า 1 คน)</small>
                                                <select class="selectpicker form-control form-control-sm" name="MngType[]" id="MngType" multiple>
                                                    <option value="T" selected>รายทีม</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group mb-3">
                                                <label for="CPType">เลือกประเภทเป้าขายสินค้า <span class='text-danger'>*<span></label>
                                                <select class='form-select form-select-sm' name="CPType" id="CPType">
                                                    <option value="Q">สินค้าจอง (Quota)</option>
                                                    <option value="F">สินค้าต้องขาย (Focus)</option>
                                                    <option value="P">สินค้าโปรโมชั่น (Promotion)</option>
                                                    <option value="2">สินค้ามือสอง (2nd Hand)</option>
                                                    <option value="O" selected>อื่น ๆ</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group mb-3">
                                                <label for="StartDate">วันที่เริ่มต้น <span class='text-danger'>*<span></label>
                                                <input type="month" class='form-control form-control-sm' name='StartDate' id='StartDate' value="<?php echo date("Y-m"); ?>">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group mb-3">
                                                <label for="EndDate">วันที่สิ้นสุด <span class='text-danger'>*<span></label>
                                                <input type="month" class='form-control form-control-sm' name='EndDate' id='EndDate' value="<?php echo date("Y-m"); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label for="CPDescription">รายละเอียดเป้าขายสินค้า</label>
                                                <input class='form-control form-control-sm' name="CPDescription" id="CPDescription" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-9">
                                            <span class="text-danger">* กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนกดปุ่มถัดไป เนื่องจากจะไม่สามารถกลับมาแก้ไขได้อีก *</span>
                                        </div>
                                        <div class="col-3 text-right">
                                            <button type="button" class="btn-next btn btn-primary" data-step="1" data-goto="2">ต่อไป <i class="fas fa-chevron-right fa-fw fa-1x"></i></button>
                                        </div>
                                    </div>
                                </form>

                                <form id='Step2'>
                                    <div class="col-lg-12">
                                        <h4 class="h4">Step 2: เพิ่มรายการสินค้า</h4>
                                        <small>
                                            <span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)<br/>
                                        </small>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-auto">
                                            <button type="button" class='btn btn-sm btn-primary mb-3' onclick="AddList();"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มรายการ</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="table-responsive tableFix">
                                                <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableShowList'>
                                                    <thead class='text-center bg-white'>
                                                        <tr>
                                                            <th width='5%'>ลำดับ</th>
                                                            <th width='10%'>รหัสสินค้า</th>
                                                            <th>ชื่อสินค้า</th>
                                                            <th width='7.5%'>สถานะ</th>
                                                            <th width='10%'>ต้นทุน (บาท)</th>
                                                            <th width='10%'>Stock ปัจจุบัน</th>
                                                            <th width='5%'>หน่วย</th>
                                                            <th width='10%'>เป้าทั้งหมด</th>
                                                            <th width='10%'>จัดการ</th>
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
                                    <div class="row">
                                        <div class="col d-flex justify-content-end">
                                            <button type='button' class='btn btn-sm btn btn-outline-info me-4' onclick="SaveStatus('O');"><i class="far fa-save fa-fw fa-1x"></i> บันทึกร่าง</button>
                                            <button type='button' class='btn btn-sm btn-primary' onclick="SaveStatus('C');"><i class="fas fa-save fa-fw fa-1x"></i> บันทึก</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="Tab3" role="tabpanel" aria-labelledby="Tab3-tab">
                                <div class="row mt-3">
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
                                    <div class="col-lg-2 col-5">
                                        <div class="form-group">
                                            <label for="filt_CPType">เลือกประเภทเป้าขายสินค้า</label>
                                            <select class="form-select form-select-sm" name="filt_CPType" id="filt_CPType">
                                                <option value="Q" selected>สินค้าจอง (Quota)</option>
                                                <option value="F">สินค้าต้องขาย (Focus)</option>
                                                <option value="P">สินค้าโปรโมชั่น (Promotion)</option>
                                                <option value="2">สินค้ามือสอง (2nd Hand)</option>
                                                <option value="SD">สินค้าสถานะ D</option>
                                                <option value="SR">สินค้าสถานะ R</option>
                                                <option value="SAW">สินค้าสถานะ A / W</option>
                                                <option value="SM">สินค้าสถานะ M</option>
                                                <option value="SN">สินค้าสถานะ N</option>
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

<!-- Modal Tab1 -->
<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit fa-fw fa-1x"></i> แก้ไข</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-borderless rounded rounded-3 overflow-hidden" style='background-color: rgba(155, 0, 0, 0.04);' id='TableHeaderEdit'>
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
                    <div class="col-auto">
                        <button type="button" class='btn btn-sm btn-primary mb-3' onclick="AddList2();"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มรายการ</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableShowListEdit'>
                                <thead class='text-center bg-white'>
                                    <tr>
                                        <th width='5%'>ลำดับ</th>
                                        <th width='10%'>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th width='7.5%'>สถานะ</th>
                                        <th width='10%'>ต้นทุน (บาท)</th>
                                        <th width='10%'>Stock ตั้งต้น</th>
                                        <th width='5%'>หน่วย</th>
                                        <th width='10%'>เป้าทั้งหมด</th>
                                        <th width='10%'>จัดการ</th>
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
            <div class="modal-footer btnedit">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDeleteHeader" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class='d-flex justify-content-center'>
                    <span><i class="fas fa-question-circle fa-fw fa-5x text-primary"></i></span>
                </div>
                <div class='d-flex justify-content-center pt-2'>
                    <span>คุณต้องการลบรายการนี้หรือไม่ ?</span>
                </div>
                <div class="row pt-3">
                    <div class="col d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" onclick='ConDeleteHeader();'>ตกลง</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalSale" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มพนักงานขาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="EditMngType">เลือกพนักงานขาย</label>
                    <select class='selectpicker form-control form-control-sm' name='EditMngType[]' id='EditMngType' multiple title='เลือกพนักงานขาย'></select>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
                <button type="button" class="btn btn-primary btn-sm" onclick='AddSale();'><i class="fas fa-save fa-fw"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Step 2 -->
<div class="modal fade" id="ModalAddList" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มรายการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id='AddList'>
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label for="ItemSelect">เลือกสินค้า&nbsp;<span class='text-primary' id='CkhStock'></span></label>
                                <select class='selectpicker form-control form-control-sm' name="ItemSelect" id="ItemSelect" data-live-search="true" onchange='ChkStock();'></select>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-group">
                                <label for="">เป้าทั้ง Campaign</label>
                                <input type="number" class='form-control form-control-sm text-right' name='TarCampaign' id='TarCampaign' value='1'>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-group">
                                <label for=""></label>
                                <button type="button" class='btn btn-sm btn-primary w-100' onclick='CalData();'><i class="fas fa-calculator fa-fw"></i> คำนวน</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableAddList'>
                                    <thead class='text-center'>
                                        <tr>
                                            <th rowspan='2'>พนักงานขาย</th>
                                            <th colspan='12'>เป้าขาย</th>
                                        </tr>
                                        <tr>
                                            <?php 
                                            for($m = 1; $m <= 12; $m++) {
                                                echo "<th width='7%'>".FullMonth($m)."</th>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan='13' class='text-center'>ไม่มีข้อมูล :)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name='tmpRow' id='tmpRow'>
                </form>
            </div>
            <div class="modal-footer savelist">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDelete" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class='d-flex justify-content-center'>
                    <span><i class="fas fa-question-circle fa-fw fa-5x text-primary"></i></span>
                </div>
                <div class='d-flex justify-content-center pt-2'>
                    <span>คุณต้องการลบรายการนี้หรือไม่ ?</span>
                </div>
                <div class="row pt-3">
                    <div class="col d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
                    </div>
                    <div class="col deletelist">
                        
                    </div>
                </div>
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
                                <thead class='text-center bg-light'>
                                    <tr>
                                        <th width='5%'>ลำดับ</th>
                                        <th width='10%'>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th width='5%'>สถานะ</th>
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
                                        <td colspan='9' class='text-center'>ไม่มีข้อมูล :)</td>
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
                                            <th rowspan='2' width='6%'>ยอดรวม<br>(หน่วย)</th>
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
        CallHead();
        sessionStorage.setItem('MngType',JSON.stringify(""));
        ShowTar();
        GetItemProduct();
        $("#Step2").hide();
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

// ------------------------------------------------------------------------------ TAB 2 ----------------------------------------------------------------------------------- //

function CheckForm(StepNow,StepTo) {
    var Now = StepNow;
    var To  = StepTo;
    var ErrorPoint = 0;
    var ErrorID    = [];
    var SuccessID  = [];
    var CheckID    = null;

    switch(Now) {
        case "1": CheckID = ["CPTitle","TeamCode","MngType","CPType","StartDate","EndDate"]; break;
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
        for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        switch(Now) {
            case "1":
                for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
                var FormStep1 = new FormData($("#Step1")[0]);
                $.ajax({
                    url: "menus/marketing/ajax/ajaxTargetSku.php?p=AddHeader",
                    type: "POST",
                    dataType: 'text',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: FormStep1,
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj, function(key, inval) {
                            let status = inval['Status'];
                            if(status == "SUCCESS") {
                                $("#CPEntry").val(inval['CPEntry']);
                                $("#CPDocNum").val(inval['DocNum']);
                                sessionStorage.setItem('tmpTeamCode',JSON.stringify(inval['TeamCode']));
                                $("#Step1").hide();
                                $("#Step2").show();
                            } else {
                                $("#CPEntry").val(0);
                                $("#CPDocNum").val(0);
                                let ErrTxt = "";
                                let ErrDsc = status.split("::");
                                switch(ErrDsc[1]) {
                                    case "CANNOTINSERT": ErrTxt = "ไม่สามารถเพิ่มข้อมูลได้<br/>กรุณาติดต่อฝ่าย IT"; break;
                                }
                                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                $("#alert_body").html(ErrTxt);
                                $("#alert_modal").modal('show');
                            }
                        });
                    }
                });
            break;
        }
    }
}

function AddList() {
    $(".savelist").html("");
    GetDataList();
    $("#ItemSelect").attr("disabled", false);
    var SelectOption = $("#ItemSelect").html();
    $("#ItemSelect").empty().selectpicker('destroy');
    $("#ItemSelect").html(SelectOption).selectpicker();
    $("#TarCampaign").val(1);
    $("#CkhStock").html("");
    $(".savelist").html("<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button><button type='button' class='btn btn-primary btn-sm' onclick='SaveList()'><i class='fas fa-save fa-fw'></i> บันทึก</button>");
    $("#ModalAddList").modal("show");
}

$(".btn-prev, .btn-next").on("click", function(e) {
    e.preventDefault();
    var StepNow  = $(this).attr("data-step");
    var StepGoto = $(this).attr("data-goto");
    CheckForm(StepNow,StepGoto);
});

$("#TeamCode").on("change", function() {
    let TeamCode = $(this).val();
    var slct_opt = "<option value='T' selected>รายทีม</option>";

    if(TeamCode == "TT2" || TeamCode == "OUL") {
        $.ajax({
            url: "menus/marketing/ajax/ajaxTargetSku.php?p=GetSaleEmp",
            type: "POST",
            data: { t: TeamCode },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    Row = inval['Rows'];
                    for(i = 0; i < Row; i++) {
                        slct_opt += "<option value='"+inval[i]['ukey']+"'>"+inval[i]['SaleName']+"</option>";
                    }
                    $("#MngType")
                        .selectpicker("destroy")
                        .html(slct_opt)
                        .selectpicker();
                });
            }
        })
    } else {
        $("#MngType")
            .selectpicker("destroy")
            .html(slct_opt)
            .selectpicker();
    }
});

$("#MngType").on("change", function() {
    var slct_value = $(this).val();
    if(slct_value.length != 1) {
        const slct_value2 = slct_value[1];
        if(slct_value[0] == "T") {
            if(JSON.parse(sessionStorage.getItem('MngType')) == "") {
                $(this).selectpicker('val',[]);
                $(this).selectpicker('val',[slct_value2]);
                sessionStorage.setItem('MngType',JSON.stringify($(this).val()[0]));
            } else {
                // sessionStorage.setItem('MngType',JSON.stringify($(this).val()[0]));
                if($(this).val()[0] == "T") {
                    $(this).selectpicker('val',[]);
                    $(this).selectpicker('val',['T']);
                    sessionStorage.setItem('MngType',JSON.stringify(""));
                }
            }
        }
    }
});

function GetItemProduct() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxsaleorder.php?p=GetItemProduct",
        success: function(result){
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#ItemSelect").html(inval["outputPro"])
            });
        }
    });
    $(".overlay").hide();
}

function GetDataList(){
    const CPDocNum  = $("#CPDocNum").val();
    const MngType = $("#MngType").val()[0];
    // const MngType  = "P";
    const TeamCode = $("#TeamCode").val();
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=GetSales",
        type: "POST",
        data: { CPDocNum : CPDocNum, MngType : MngType, TeamCode : TeamCode, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TableAddList tbody").html(inval['Data']);
            });
        }
    })
}

$("#TarCampaign").keypress(function (e) {
    if (e.which == 13) {
        CalData();
    }
});

function ChkStock() {
    const ItemCode = $("#ItemSelect").val();
    const CPType   = $("#CPType").val();
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=ChkStock",
        type: "POST",
        data: { ItemCode : ItemCode, CPType : CPType, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#CkhStock").html(inval['OpenStock']);
            });
        }
    })
}

function CalData() {
    const ItemCode = $("#ItemSelect").val();
    const TarCam   = $("#TarCampaign").val();
    const CPDocNum = $("#CPDocNum").val();
    // const TeamCode = $("#TeamCode").val();
    const TeamCode = JSON.parse(sessionStorage.getItem('tmpTeamCode'));
    // console.log(TeamCode);
    const CPType   = $("#CPType").val();
    if(ItemCode != null && TarCam != "") {
        $.ajax({
            url: "menus/marketing/ajax/ajaxTargetSku.php?p=CalData",
            type: "POST",
            data: { ItemCode : ItemCode, TarCam : TarCam, CPDocNum : CPDocNum, TeamCode : TeamCode, CPType : CPType, },
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    if(inval['ChkStock'] == 'Y') {
                        $("#TableAddList tbody").html(inval['Data']);
                        sessionStorage.setItem('temRowID',JSON.stringify(inval['Row']));
                    }else{
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("สินค้าใน Stock มีไม่เพียงพอ");
                        $("#alert_modal").modal('show');
                    }
                });
            }
        })
    }
}

function SaveList() {
    $("#ItemSelect").attr("disabled", false);
    let DataList = new FormData($("#AddList")[0]);
    DataList.append('CPEntry',$("#CPEntry").val());
    DataList.append('CPDocNum',$("#CPDocNum").val());
    DataList.append('CPType',$("#CPType").val());
    DataList.append('RowID',JSON.parse(sessionStorage.getItem('temRowID')));
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=SaveList",
        type: "POST",
        dataType: 'text',
        cache: false,
        processData: false,
        contentType: false,
        data: DataList,
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                if(inval['ChkStock'] == 'Y') {
                    if(inval['SUCCESS'] == 'Y') {
                        $("#alert_header").html("<i class='fas fa-check-circle fa-fw fa-lg text-primary' style='font-size: 60px;'></i>");
                        $("#alert_body").html("บันทึกสำเร็จ");
                        $("#alert_modal").modal('show');
                        $("#ModalAddList").modal("hide");
                        ShowList();
                    }else{
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("บันทึกไม่สำเร็จ");
                        $("#alert_modal").modal('show');
                    }
                }else{
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("สินค้าใน Stock มีไม่เพียงพอ");
                    $("#alert_modal").modal('show');
                }
            });
        }
    });
}

function ShowList(){
    const CPDocNum = $("#CPDocNum").val();
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=ShowList",
        type: "POST",
        data: { CPDocNum : CPDocNum, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TableShowList tbody").html(inval['Data']);
            });
        }
    })
}

function EditList(DocNum, RowID) {
    sessionStorage.setItem('temRowID',JSON.stringify(RowID));
    $(".savelist").html("");
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=EditList",
        type: "POST",
        data: { DocNum : DocNum, RowID : RowID, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TableAddList tbody").html(inval['Data']);

                $("#ItemSelect").attr("disabled", true);
                var SelectOption = $("#ItemSelect").html();
                $("#ItemSelect").empty().selectpicker('destroy');
                $("#ItemSelect").html(SelectOption).val(inval['ItemCode']).change().selectpicker();
                $("#TarCampaign").val(inval['TarCam']);
                $("#tmpRow").val(inval['Row']);

                $(".savelist").html("<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button><button type='button' class='btn btn-primary btn-sm' onclick='SaveList()'><i class='fas fa-save fa-fw'></i> บันทึก</button>");
                $("#ModalAddList").modal("show");
            });
        }
    })
}

function DeleteList(RowID,DocStatus) {
    $("#ModalDelete").modal("show");
    $(".deletelist").html("<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' onclick='ConDeleteList();'>ตกลง</button>");
    sessionStorage.setItem('temRowID',JSON.stringify(RowID));
    sessionStorage.setItem('temDocStatus',JSON.stringify(DocStatus));
}

function ConDeleteList() {
    const RowID = JSON.parse(sessionStorage.getItem('temRowID'));
    const DocStatus = JSON.parse(sessionStorage.getItem('temDocStatus'));
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=DeleteList",
        type: "POST",
        data: { RowID : RowID, DocStatus : DocStatus, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#alert_header").html("<i class='fas fa-check-circle fa-fw fa-lg text-primary' style='font-size: 60px;'></i>");
                $("#alert_body").html("ลบสำเร็จ");
                $("#alert_modal").modal('show');
                ShowList();
                sessionStorage.setItem('temRowID',JSON.stringify(""));
                sessionStorage.setItem('temDocStatus',JSON.stringify(""));
            });
        }
    })
}

function SaveStatus(Status) {
    const CPDocNum = $("#CPDocNum").val();
    if(Status == 'C') {
        $.ajax({
            url: "menus/marketing/ajax/ajaxTargetSku.php?p=SaveStatus",
            data: { CPDocNum : CPDocNum, },
            type: "POST",
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $("#alert_header").html("<i class='fas fa-check-circle fa-fw fa-lg text-primary' style='font-size: 60px;'></i>");
                    $("#alert_body").html("บันทึกสำเร็จ");
                    $("#alert_modal").modal('show');
                });
            }
        })
    }else{
        $("#alert_header").html("<i class='fas fa-check-circle fa-fw fa-lg text-primary' style='font-size: 60px;'></i>");
        $("#alert_body").html("บันทึกร่างสำเร็จ");
        $("#alert_modal").modal('show');
    }

    // Reset Data
    $("#TableShowList tbody").html("<tr><td colspan='8' class='text-center'>ไม่มีข้อมูล :)</td></tr>");
    $("#CPTitle").val("");
    $("#TeamCode").val("");
    $("#MngType").selectpicker('val',['T']);
    $("#CPType").val("O");
    $("#StartDate").val("<?php echo date("Y-m"); ?>");
    $("#EndDate").val("<?php echo date("Y-m"); ?>");
    $("#CPDescription").val("");
    $("#Step1").show();
    $("#Step2").hide();
    $("#CPTitle, #TeamCode, #MngType, #CPType, #StartDate, #EndDate").removeClass("is-valid");
    ShowTar();
}


// ------------------------------------------------------------------------------ TAB 1 ----------------------------------------------------------------------------------- //

// Tab 1
function ShowTar(){
    $(".savelist").html("");
    $("#TableShowTar").dataTable().fnClearTable();
    $("#TableShowTar").dataTable().fnDraw();
    $("#TableShowTar").dataTable().fnDestroy();
    $("#TableShowTar").DataTable({
        "ajax": {
            url: "menus/marketing/ajax/ajaxTargetSku.php?p=ShowTar",
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
            { "data": "Manage",   class: "dt-body-center" },
        ],
        "columnDefs": [
            { "width": "7%",  "targets": 0 },
            { "width": "20%",   "targets": 1 },
            { "width": "10%",   "targets": 2 },
            { "width": "5%",  "targets": 3 },
            { "width": "11%", "targets": 4 },
            { "width": "12%",   "targets": 5 },
            { "width": "8%",   "targets": 6 },
            { "width": "22%",   "targets": 7 },
            { "width": "5%",  "targets": 8 },
        ],
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 12,
        "ordering": false,
        "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        },
    });
    
}

function Edit(DocNum,DocStatus) {
    sessionStorage.setItem('temMngType',JSON.stringify(""));
    sessionStorage.setItem('temTeamCode',JSON.stringify(""));
    sessionStorage.setItem('temCPType',JSON.stringify(""));
    sessionStorage.setItem('temDocStatus',JSON.stringify(DocStatus));
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=Edit",
        type: "POST",
        data: { DocNum : DocNum, DocStatus : DocStatus, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TableHeaderEdit tbody").html(inval['DataHeader']);
                $("#TableShowListEdit tbody").html(inval['Data']);

                $("#CPDocNum").val(DocNum);
                $("#CPEntry").val(inval['CPEntry']);

                sessionStorage.setItem('temMngType',JSON.stringify(inval['ChkMngType']));
                sessionStorage.setItem('temTeamCode',JSON.stringify(inval['TeamCode']));
                sessionStorage.setItem('temCPType',JSON.stringify(inval['CPType']));

                if(DocStatus == 'O') {
                    $(".btnedit").html("<button type='button' class='btn btn-sm btn btn-outline-info me-4' data-bs-dismiss='modal' onclick=\"SaveStatus('O');\"><i class='far fa-save fa-fw fa-1x'></i> บันทึกร่าง</button><button type='button' class='btn btn-sm btn-primary' data-bs-dismiss='modal' onclick=\"SaveStatus('C');\"><i class='fas fa-save fa-fw fa-1x'></i> บันทึก</button>");
                }else{
                    $(".btnedit").html("<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button>");
                }
                $("#ModalEdit").modal("show");
            });
        }
    })
}

function ModalAddSale() {
    const DocStatus = JSON.parse(sessionStorage.getItem('temDocStatus'));
    const DocNum    = $("#CPDocNum").val();
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=GetSaleEdit",
        type: "POST",
        data: { DocStatus : DocStatus, DocNum : DocNum, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                if(inval['Row'] != 0) {
                    $("#EditMngType").selectpicker("destroy").html(inval['option']).selectpicker();
                    var EditMngType = $("#EditMngType").html();
                    $("#EditMngType").empty().selectpicker('destroy');
                    $("#EditMngType").html(EditMngType).selectpicker();
                    $("#ModalSale").modal("show");
                }else{
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("เพิ่มพนักงานขายทุกคนแล้ว");
                    $("#alert_modal").modal('show');
                }
            });
        }
    })
}

function AddSale() {
    const EditMngType = $("#EditMngType").val();
    const DocNum      = $("#CPDocNum").val();
    // console.log(EditMngType);
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=AddSale",
        type: "POST",
        data: { EditMngType : EditMngType, DocNum : DocNum, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#alert_header").html("<i class='fas fa-check-circle fa-fw fa-lg text-primary' style='font-size: 60px;'></i>");
                $("#alert_body").html("บันทึกสำเร็จ");
                $("#alert_modal").modal('show');
                $("#ModalSale").modal("hide");
            });
        }
    })
}

function DeleteHeader(DocNum) {
    $("#CPDocNum").val(DocNum);
    $("#ModalDeleteHeader").modal("show");
}

function ConDeleteHeader() {
    const DocNum    = $("#CPDocNum").val();
    const DocStatus = JSON.parse(sessionStorage.getItem('temDocStatus'));
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=ConDeleteHeader",
        type: "POST",
        data: { DocNum : DocNum, DocStatus : DocStatus, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#alert_header").html("<i class='fas fa-check-circle fa-fw fa-lg text-primary' style='font-size: 60px;'></i>");
                $("#alert_body").html("ลบสำเร็จ");
                $("#alert_modal").modal('show');
                ShowTar();
            });
        }
    })
}

function AddList2() {
    GetDataList2();
    $("#ItemSelect").attr("disabled", false);
    var SelectOption = $("#ItemSelect").html();
    $("#ItemSelect").empty().selectpicker('destroy');
    $("#ItemSelect").html(SelectOption).selectpicker();
    $("#TarCampaign").val(1);
    $("#CkhStock").html("");
    $(".savelist").html("<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button><button type='button' class='btn btn-primary btn-sm' onclick='SaveList2()'><i class='fas fa-save fa-fw'></i> บันทึก</button>");
    $("#ModalAddList").modal("show");
}

function GetDataList2(){
    const CPDocNum = $("#CPDocNum").val();
    const MngType  = JSON.parse(sessionStorage.getItem('temMngType'));
    const TeamCode = JSON.parse(sessionStorage.getItem('temTeamCode'));
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=GetSales",
        type: "POST",
        data: { CPDocNum : CPDocNum, MngType : MngType, TeamCode : TeamCode, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TableAddList tbody").html(inval['Data']);
            });
        }
    })
}

function EditList2(DocNum, RowID) {
    sessionStorage.setItem('temRowID',JSON.stringify(RowID));
    $(".savelist").html("");
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=EditList",
        type: "POST",
        data: { DocNum : DocNum, RowID : RowID, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TableAddList tbody").html(inval['Data']);

                $("#ItemSelect").attr("disabled", true);
                var SelectOption = $("#ItemSelect").html();
                $("#ItemSelect").empty().selectpicker('destroy');
                $("#ItemSelect").html(SelectOption).val(inval['ItemCode']).change().selectpicker();
                $("#TarCampaign").val(inval['TarCam']);
                $("#tmpRow").val(inval['Row']);

                $(".savelist").html("<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button><button type='button' class='btn btn-primary btn-sm' onclick='SaveList2()'><i class='fas fa-save fa-fw'></i> บันทึก</button>");
                $("#ModalAddList").modal("show");
            });
        }
    })
}

function SaveList2() {
    $("#ItemSelect").attr("disabled", false);
    let DataList = new FormData($("#AddList")[0]);
    DataList.append('CPEntry',$("#CPEntry").val());
    DataList.append('CPDocNum',$("#CPDocNum").val());
    DataList.append('CPType',JSON.parse(sessionStorage.getItem('temCPType')));
    DataList.append('DocStatus',JSON.parse(sessionStorage.getItem('temDocStatus')));
    DataList.append('RowID',JSON.parse(sessionStorage.getItem('temRowID')));
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=SaveList2",
        type: "POST",
        dataType: 'text',
        cache: false,
        processData: false,
        contentType: false,
        data: DataList,
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                if(inval['ChkStock'] == 'Y') {
                    if(inval['SUCCESS'] == 'Y') {
                        $("#alert_header").html("<i class='fas fa-check-circle fa-fw fa-lg text-primary' style='font-size: 60px;'></i>");
                        $("#alert_body").html("บันทึกสำเร็จ");
                        $("#alert_modal").modal('show');
                        $("#ModalAddList").modal("hide");
                        ShowList2();
                    }else{
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("บันทึกไม่สำเร็จ");
                        $("#alert_modal").modal('show');
                    }
                }else{
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("สินค้าใน Stock มีไม่เพียงพอ");
                    $("#alert_modal").modal('show');
                }
            });
        }
    });
}

function ShowList2(){
    const CPDocNum = $("#CPDocNum").val();
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=ShowList2",
        type: "POST",
        data: { CPDocNum : CPDocNum, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#TableShowListEdit tbody").html(inval['Data']);
            });
        }
    })
}

function DeleteList2(RowID,DocStatus) {
    $("#ModalDelete").modal("show");
    $(".deletelist").html("<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' onclick='ConDeleteList2();'>ตกลง</button>");
    sessionStorage.setItem('temRowID',JSON.stringify(RowID));
    sessionStorage.setItem('temDocStatus',JSON.stringify(DocStatus));
}

function ConDeleteList2() {
    const RowID     = JSON.parse(sessionStorage.getItem('temRowID'));
    const DocStatus = JSON.parse(sessionStorage.getItem('temDocStatus'));
    $.ajax({
        url: "menus/marketing/ajax/ajaxTargetSku.php?p=DeleteList",
        type: "POST",
        data: { RowID : RowID, DocStatus : DocStatus, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $("#alert_header").html("<i class='fas fa-check-circle fa-fw fa-lg text-primary' style='font-size: 60px;'></i>");
                $("#alert_body").html("ลบสำเร็จ");
                $("#alert_modal").modal('show');
                ShowList2();
                sessionStorage.setItem('temDocStatus',JSON.stringify(""));
            });
        }
    })
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
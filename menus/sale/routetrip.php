<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<style type="text/css">
    .clearfix::after,
    .calendar ol::after {
    content: ".";
    display: block;
    height: 0;
    clear: both;
    visibility: hidden;
    }

    /* ================
    Calendar Styling */
    .calendar {
    border-radius: 10px;
    }

    .month {
    font-size: 2rem;
    }

    @media (min-width: 992px) {
    .month {
        font-size: 3.5rem;
    }
    }

    .calendar ol li {
        float: left;
        width: 14.28571%;
    }

    .calendar .day-names {
        border-bottom: 1px solid #eee;
    }

    .calendar .day-names li {
        font-weight: 600;
    }

    .calendar .days li {
        border-bottom: 1px solid #eee;
        min-height: 8rem;
    }

    .calendar .days li .date {
        margin: 0.5rem 0.25rem;
    }

    .calendar .days li .event {
        font-size: 0.75rem;
        margin: 0rem 0.25rem;
        padding: 0.25rem 0.4rem;
        color: white;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-radius: 1rem;
        margin-bottom: 1px;
    }
    .calendar .days li.outside {
        background-color: #FAFAFA;
    }
    .calendar .days li .date:hover, td.lead:hover {
        cursor: pointer;
    }
    td.lead:hover {
        background-color: rgba(0,0,0,.05);
    }

    .calendar .days li:not(.outside):hover {
        background-color: rgba(0,0,0,.05);
    }

    .calendar .days li .event:hover {
        cursor: pointer;
    }

    .calendar .days li .event.event-more {
        border: 1px solid #9A1118;
        background-color: rgba(255,255,255,1);
        color: #9A1118;
    }

    .calendar .days li .event.span-2 {
        width: 200%;
    }

    .calendar .days li .event.begin {
        border-radius: 1rem 0 0 1rem;
    }

    .calendar .days li .event.end {
        border-radius: 0 1rem 1rem 0;
    }

    .calendar .days li .event.clear {
        background: none;
    }

    .calendar .days li:nth-child(n+36) {
        border-bottom: none;
    }

    .calendar .days li.outside .date {
        color: #ddd;
    }

    .MeetType0 { text-align: left; background-color: #666666; }
    .MeetType1 { text-align: left; background-color: #048f43; }
    .MeetType2 { text-align: left; background-color: #f08935; }
    .MeetType5 { text-align: left; background-color: #295dd6; }
    .MeetType6 { text-align: left; background-color: #e8c825; }
    .MeetType7 { text-align: left; background-color: #24a2d4; }
    .MeetType8 { text-align: left; background-color: #6d24d4; }
    .today { background-color: rgba(154, 17, 24, .10); }
    .calendar .days li.today:hover { background-color: rgba(154, 17, 24, .15); }
    .today .date, .today td.lead { font-weight: bold; color: #9A1118; }
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
                    <div class="col-lg-3 col-6">
                        <div class="form-group">
                            <label for="filt_user">เลือกพนักงาน</label>
                            <select class="form-select form-select-sm" name="filt_user" id="filt_user">
                                <option value="NULL">กรุณาเลือก</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="filt_view">เลือกมุมมอง</label>
                            <select class="form-select form-select-sm" name="filt_view" id="filt_view">
                                <option value="GRID">ปฏิทินแผนงาน</option>
                                <option value="LIST">รายการแผนงาน</option>
                                <option value="LISTTRUE">รายงานปฎิบัติการจริง</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3 mb-3">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-sm btn-primary" id="btn-AddTrip"><i class="fas fa-flag-checkered fa-fw fa-1x"></i> เพิ่มแผนงานใหม่</button>
                        <!-- <button type="button" class="btn btn-sm btn-secondary" id="btn-CloneTrip"><i class="fas fa-copy fa-fw fa-1x"></i> คัดลอก</button> -->
                        <button type="button" class="btn btn-sm btn-info" id="btn-HistoryTrip"><i class="fas fa-history fa-fw fa-1x"></i> ประวัติ</button>
                        <button type="button" class="btn btn-sm btn-success" id="btn-PrintTrip"><i class="fas fa-print fa-fw fa-1x"></i> พิมพ์</button>
                        <button type="button" class="btn btn-sm btn-success d-none" id="btn-Excel"><i class="fas fa-file-excel fa-fw fa-1x"></i> Excel</button>
                    </div>
                </div>

                <div id="view_worktrip" class="mt-2"></div>
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

<!-- MODAL NEW TRIP -->
<div class="modal fade" id="ModalAddTrip" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="FormAddTrip">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-flag-checkered fa-fw fa-1x"></i> เพิ่มแผนงานใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label for="CardCode">ชื่อร้านค้า</label>
                            <select class="form-control" name="CardCode" id="CardCode" data-live-search="true">
                                <option value="NULL">กรุณาเลือก</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label for="NewCardCode">ลูกค้ามุ่งหวัง <small class="text-muted">(กรณีที่ไม่มีลูกค้าในระบบ SAP)</small></label>
                            <input type="text" class="form-control" id="NewCardCode" name="NewCardCode" placeholder="กรอกชื่อลูกค้ามุ่งหวัง..." />
                            <input type="hidden" id="RouteEntry" name="RouteEntry" value="0" readonly />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="PlanDate">วันที่จะเข้าพบ</label>
                            <input type="date" class="form-control" name="PlanDate" id="PlanDate" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="PlanSale">ประมาณการยอดขาย</label>
                            <input type="number" class="form-control text-right" name="PlanSale" id="PlanSale" value="0" min="0" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label for="PlanRemark">รายละเอียดแผนงาน</label>
                            <input type="text" class="form-control" name="PlanRemark" id="PlanRemark" placeholder="กรอกรายละเอียดหรือแผนงานที่จะเข้าพบ..." />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="ShowMaps" style="height: 25rem;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-AddNewRow" onclick="AddTrip();"><i class="fas fa-save fa-fw fa-1x"></i> บันทึก</button>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- MODAL TRIP DATE -->
<div class="modal fade" id="ModalTripDate" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-tasks fa-fw fa-1x"></i> แผนการเข้าพบประจำวัน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <h6>วันที่: <span id="AgendaDate"></span></h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 13px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">ลำดับ</th>
                                    <th width="25%">ชื่อร้านค้า</th>
                                    <th>รายละเอียดแผนงาน</th>
                                    <th width="10%">ประมาณการ<br/>ยอดขาย (บาท)</th>
                                    <th width="10%">ยอดขาย<br/>(บาท)</th>
                                    <th width="10%">บิลรอเรียกเก็บ<br/>(บาท)</th>
                                    <th width="7.5%">สถานะ</th>
                                    <!-- <th width="7.5%">นำทาง</th>
                                    <th width="7.5%">เช็คอิน</th> -->
                                    <th width="7.5%"><i class="fas fa-cog fa-fw fa-1x"></i></th>
                                </tr>
                            </thead>
                            <tbody id="view_agenda"></tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <p style="font-weight: bold; font-size: 13px;">หมายเหตุสถานะ</p>
                        <ul class="fa-ul" style="font-size: 13px;">
                            <li><span class="fa-li"><i class="fas fa-clock"></i></span> = รอเข้าพบ</li>
                            <li><span class="fa-li"><i class="fas fa-street-view"></i></span> = เข้าพบ (ในพื้นที่)</li>
                            <li><span class="fa-li"><i class="fas fa-male"></i></span> = เข้าพบ (นอกพื้นที่)</li>
                            <li><span class="fa-li"><i class="fas fa-exclamation-circle"></i></span> = เข้าพบ (ไม่มีพิกัด)</li>
                            <li><span class="fa-li"><i class="fas fa-phone-volume"></i></span> = โทร / ไลน์</li>
                            <li><span class="fa-li"><i class="fas fa-star"></i></span> = ลูกค้ามุ่งหวัง</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HISTORY TRIP -->
<div class="modal fade" id="ModalHistoryTrip" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history fa-fw fa-1x"></i> ประวัติการเข้าพบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <h6>ผู้เช็คอิน: <span id="CheckInName"></span></h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 13px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="15%">วันที่เช็คอิน</th>
                                    <th width="35%">ชื่อร้านค้า</th>
                                    <th>แผนการเข้าพบ</th>
                                    <th width="15%">สถานะ</th>
                                    <th width="7.5%">รายงาน</th>
                                </tr>
                            </thead>
                            <tbody id="view_history"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL OPEN BILL -->
<div class="modal fade" id="ModalOpenIV" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice fa-fw fa-1x"></i> รายการบิลรอเรียกเก็บ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <h6>ร้านค้า: <span id="IVCardCode"></span></h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 13px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">ลำดับ</th>
                                    <th>เลขที่เอกสาร</th>
                                    <th width="15%">วันที่เปิดบิล</th>
                                    <th width="15%">วันที่กำหนดชำระ</th>
                                    <th width="15%">ยอดรอเรียกเก็บ<br/>(บาท)</th>
                                    <th width="15%">จำนวนวัน<br/>เกินกำหนด</th>
                                </tr>
                            </thead>
                            <tbody id="view_openbill"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CHECKIN -->
<div class="modal fade" id="ModalCheckIn" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="CheckInTrip">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt fa-fw fa-1x"></i> เช็คอิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php include("menus/sale/ajax/ajaxcheckin.php"); ?>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- MODAL COPY TRIP -->
<div class="modal fade" id="ModalCopyTrip" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <form id="FormCopyTrip">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-copy fa-fw fa-1x"></i> คัดลอกแผนงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-3">
                        <label for="copy_from_m">คัดลอกจาก</label>
                    </div>
                    <div class="col-4">
                        <select class="form-select form-select-sm" name="copy_from_m" id="copy_from_m">
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
                    <div class="col-3">
                        <select class="form-select form-select-sm" name="copy_from_y" id="copy_from_y">
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
                <div class="row mt-2">
                    <div class="col-3">
                        <label for="copy_to_m">ไปยัง</label>
                    </div>
                    <div class="col-4">
                        <select class="form-select form-select-sm" name="copy_to_m" id="copy_to_m">
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
                    <div class="col-3">
                        <select class="form-select form-select-sm" name="copy_to_y" id="copy_to_y">
                        <?php
                            for($y = $this_year+1; $y >= $start_year; $y--) {
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-copy-confirm"><i class="fas fa-copy fa-fw fa-1x"></i> คัดลอก</button>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- MODAL REPORT TRIP -->
<div class="modal fade" id="ModalReportTrip" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt fa-fw fa-lg"></i> รายงานการเข้าพบลูกค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <table class="table table-borderless" style="font-size: 13px;">
                            <tr>
                                <th width="20%">ร้านค้า</th>
                                <td id="RptCardCode" colspan="2"></td>
                            </tr>
                            <tr>
                                <th>รายละเอียดแผนงาน</th>
                                <td id="RptComments" colspan="2"></td>
                            </tr>
                            <tr>
                                <th>วันที่จะเข้าพบ</th>
                                <td id="RptPlanDate" colspan="2"></td>
                            </tr>
                            <tr>
                                <th>วันที่เช็คอิน</th>
                                <td id="RptCheckInDate" colspan="2"></td>
                            </tr>
                            <tr>
                                <th>ระยะห่างที่เช็คอิน</th>
                                <td id="RptDistance" colspan="2"></td>
                            </tr>
                            <tr>
                                <th class="align-top">พิกัดที่เช็คอิน</th>
                                <td  colspan="2"><div id="RptCheckInMaps" style="height: 25rem; border: 1px solid #000;"></div></td>
                            </tr>
                            <tr>
                                <th>รายงานผลการเข้าพบ</th>
                                <td>
                                    <input type='hidden' id='CHKEntry' value=''>    
                                    <input type="text" class="form-control" name="ReportCHK" id="ReportCHK" placeholder="กรอกรายละเอียดสรุปผลการเข้าพบ...">
                                        
                                </td>
                                <td width="15"><button type="button" class="btn btn-primary" id="btn-SaveReport" onclick="AddReportCHK()"><!--<i class="fas fa-save fa-fw fa-1x" aria-hidden="true"></i>--> บันทึก</button></td>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm_modal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title"><i class="far fa-question-circle fa-fw fa-lg"></i> ยืนยัน</h5>
                <p class="defult my-4">คุณต้องการดำเนินการต่อหรือไม่?</p>
                <p class='custom d-none my-4'></p>
                <button type="button" class="btn btn-secondary btn-sm w-25" data-bs-dismiss="modal"><i class="fas fa-times fa-fw fa-1x"></i> ไม่</button>
                <button type="button" class="btn btn-primary btn-sm w-25" id="btn-confirm"><i class="fas fa-check fa-fw fa-1x"></i> ใช่</button>
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

function GetEmpName() {
    $.ajax({
        url: "menus/sale/ajax/ajaxroutetrip.php?p=GetEmpName",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#filt_user").append(inval['view_user']);
            });
        }
    })
}

function GetCardCode() {
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
}

function GetWorkTrip(filt_year,filt_month, filt_user, filt_view) {
    if(filt_view == 'LISTTRUE') {
        $("#btn-Excel").removeClass('d-none');
    }else{
        $("#btn-Excel").addClass('d-none');
    }
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxroutetrip.php?p=GetWorkTrip",
        type: "POST",
        data: { y: filt_year, m: filt_month, v: filt_view, u: filt_user },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#view_worktrip").html(inval['view_worktrip']);
            });
            $(".overlay").hide();
        }
    })
}

function GetOpenIV(CardCode) {
    $(".modal").modal("hide");
    $.ajax({
        url: "menus/sale/ajax/ajaxroutetrip.php?p=GetIVList",
        type: "POST",
        data: { CardCode: CardCode },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#IVCardCode").html(inval['open_cardcode']);
                $("#view_openbill").html(inval['open_list']);
            });
            $("#ModalOpenIV").modal("show");
        }
    });
}

function TripDate(ClickDate,ukey) {
    $("#ModalTripDate").modal("show");
    $.ajax({
        url: "menus/sale/ajax/ajaxroutetrip.php?p=GetAgenda",
        type: "POST",
        data: { u: ukey, d: ClickDate },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#view_agenda").html(inval['view_agenda']);
                $("#AgendaDate").html(inval['view_date']);
            });
        }
    });
}

function DeleteTrip(RouteEntry,ClickDate,ukey) {
    $("#confirm_modal").modal("show");
    $(document).off("click","#btn-confirm").on("click","#btn-confirm", function() {
        $.ajax({
            url: "menus/sale/ajax/ajaxroutetrip.php?p=DeleteTrip",
            type: "POST",
            data: { RouteEntry: RouteEntry},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    GetWorkTrip($("#filt_year").val(),$("#filt_month").val(), $("#filt_user").val(),$("#filt_view").val());
                    TripDate(ClickDate,ukey);
                    $("#confirm_modal").modal("hide");
                });
            }
        });
    });
}

function ShowMaps(geo_lon, geo_lat) {
    var geolon;
    var geolat;
    if(geo_lon != null && geo_lat != null) {
        geolon = parseFloat(geo_lon);
        geolat = parseFloat(geo_lat);
    } else {
        /* KINGBANGKOK INTERTRADE CO., LTD. */
        geolon = 100.63077769632491;
        geolat = 13.856049258527104;
    }
    
    var map = new longdo.Map({
        placeholder: document.getElementById("ShowMaps"),
        lastview: false,
        language: 'th',
        ui: longdo.UiComponent.Mobile
    });
    map.Layers.setBase(longdo.Layers.GRAY);
    map.zoom(13,true);
    map.location({ lon: geolon, lat: geolat }, true);

    /* Add Mark Icon */
    var StorePin = new longdo.Marker({ lon: geolon, lat: geolat },{ icon: { html: '<i class=\'fas fa-map-marker-alt fa-4x text-primary\'></i>', offset: { x: 18, y: 48 } }, weight: 999 });
    
    map.Overlays.add(StorePin);
    // var CheckPin = new longdo.Marker({ lon: geolon, lat: geolat },{ icon: { html: '<i class=\'fas fa-male fa-4x text-primary\'></i>', offset: { x: 9, y: 48 } }, weight: 999 });
    // map.Overlays.add(CheckPin);

    /* 
        Add CirCle radius ~5km.
        ระยะห่าง 1 องศา Lat/Lon = ~111km. @ เส้นศูนย์สูตรโลก
        ~1km. = 1/111 = 0.009009009009009 degree
        ~5km. = 0.009009009009009 * 5 = 0.045045045045
     */
    var SafeZone = new longdo.Circle({
        lon: geolon, lat: geolat
    }, 0.0465, {
        lineWidth: 2,
        lineColor: 'rgba(128,252,3,0.8)',
        fillColor: 'rgba(128,252,3,0.25)'
    });
    map.Overlays.add(SafeZone);

    $("#ModalAddTrip").on("shown.bs.modal", function () {
        map.resize();
    });
}

function AddTrip() {
    var FormAddTrip = new FormData($("#FormAddTrip")[0]);
    $("#CardCode").attr("disabled");
    var CardCode = $("#CardCode").val();
    var NewCardCode = $("#NewCardCode").val();
    if(CardCode == 'NULL' || CardCode == "" || CardCode == null) {
        if(NewCardCode == 'NULL' || NewCardCode == '' || NewCardCode == ' ' || NewCardCode == null) {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("ถ้าเป็นลูกค้ามุ่งหวัง<br>กรุณากรอกชื่อลูกค้ามุ่งหวัง");
            $("#alert_modal").modal('show');
            return;
        }
    }
    var DatePlan = $("#PlanDate").val();
    if(CardCode.length == 0 && NewCardCode.length == 0) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกลูกค้า หรือกรอกชื่อลูกค้ามุ่งหวัง");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/sale/ajax/ajaxroutetrip.php?p=AddTrip",
            type: 'POST',
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: FormAddTrip,
            success: function() {
                $(".modal").modal("hide");
                // if(sessionStorage.getItem('DatePlan') != DatePlan) {
                //     sessionStorage.setItem('DatePlan',JSON.stringify(DatePlan));
                // }
                $("#confirm_saved").modal('show');
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    var filt_year  = $("#filt_year").val();
                    var filt_month = $("#filt_month").val();
                    var filt_user  = $("#filt_user").val();
                    var filt_view  = $("#filt_view").val();
                    GetWorkTrip(filt_year,filt_month,filt_user,filt_view);
                });
            }
        });
    }
}

function AddReportCHK() {
    var ReportCHK = $("#ReportCHK").val();
    var RouteEntry = $("#CHKEntry").val();
    if(ReportCHK.length == 0 ) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกรายละเอียดการเข้าพบ");
        $("#alert_modal").modal('show');
    } else {
        //url: "menus/sale/ajax/ajaxroutetrip.php?p=Addtreport",
        //data: { RouteEntry: RouteEntry,ReportCHK:ReportCHK },
        $.ajax({
            url: "menus/sale/ajax/ajaxroutetrip.php?p=Addtreport",
            type: "POST",
            data: { RouteEntry: RouteEntry,ReportCHK:ReportCHK },
            success: function(result) {
                $(".modal").modal("hide");
            }
        });
    }
}

function EditTrip(RouteEntry) {
    $("#CardCode").selectpicker("destroy");
    $("#CardCode, #NewCardCode").attr("disabled", true);
    $("#CardCode").val("").change();
    $.ajax({
        url: "menus/sale/ajax/ajaxroutetrip.php?p=HookTrip",
        type: "POST",
        data: { RID: RouteEntry },
        success: function(result) {
            $(".modal").modal("hide");
            $("#ModalAddTrip").modal("show");
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval){
                $("#RouteEntry").val(RouteEntry);
                if(inval['CardCode'] == null) {
                    $("#NewCardCode").val(inval['CardName']);
                } else {
                    $("#CardCode").val(inval['CardCode']).change();
                }
                $("#CardCode").selectpicker();
                $("#PlanDate").val(inval['PlanDate']);
                $("#PlanRemark").val(inval['Comments']);

                var GeoLon = inval['geo_lon'];
                var GeoLat = inval['geo_lat'];
                if(GeoLon != null && GeoLat != null) {
                    ShowMaps(GeoLon,GeoLat);
                } else {
                    var text_html;
                    text_html = "<div class='d-flex align-items-center justify-content-center text-muted' style='height: 100%'>"+
                                    "<div class='text-center'>"+
                                        "<i class='fas fa-store-alt-slash fa-fw fa-4x mb-3'></i><br/>"+
                                        "ไม่มีข้อมูลพิกัด"+
                                    "</div>"+
                                "</div>"
                    $("#ShowMaps").empty();
                    $("#ShowMaps").html(text_html);
                }
            });
        }
    })
}

function CheckInReport(RouteEntry) {
    // $(".modal").modal("hide");
    $.ajax({
        url: "menus/sale/ajax/ajaxroutetrip.php?p=RouteReport",
        type: "POST",
        data: { RouteEntry: RouteEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                /* do something */
                $("#ModalReportTrip").modal("show");
                $("#RptCardCode").html(inval['CardName']);
                $("#RptComments").html(inval['Comments']);
                $("#RptPlanDate").html(inval['PlanDate']);
                $("#RptCheckInDate").html(inval['ChckDate']);
                $("#RptDistance").html(inval['ChkDistance']);
                // ReportCHK btn-SaveReport
                if(inval['Disabled_ReportCHK'] == "Y") {
                    $("#btn-SaveReport").prop('disabled', false);
                    $("#ReportCHK").prop('disabled', false);
                }else{
                    $("#btn-SaveReport").prop('disabled', true);
                    $("#ReportCHK").prop('disabled', true);
                }
                $("#ReportCHK").val(inval['ReportCHK']);
                $("#CHKEntry").val(inval['CHKEntry']);
                

                var map = new longdo.Map({
                    placeholder: document.getElementById("RptCheckInMaps"),
                    lastview: false,
                    language: 'th',
                    ui: longdo.UiComponent.Mobile
                });

                map.Layers.setBase(longdo.Layers.GRAY);
                map.zoom(15,true);
                map.zoomRange({ min: 10, max: 20 });
                map.location({ lon: inval['chk_lon'], lat: inval['chk_lat'] }, true);

                /* CheckIn Marker */
                var CheckPin = new longdo.Marker({ lon: inval['chk_lon'], lat: inval['chk_lat'] },{ icon: { html: '<i class=\'fas fa-male fa-4x\' style=\'color: #fc0380;\'></i>', offset: { x: 9, y: 48 } }, weight: 999 });
                map.Overlays.add(CheckPin);

                if(inval['chk_lon'].length != 0 && inval['chk_lat'].length != 0) {
                    var StorePin = new longdo.Marker({ lon: inval['plan_lon'], lat: inval['plan_lat'] },{ icon: { html: '<i class=\'fas fa-map-marker-alt fa-2x text-primary\'></i>', offset: { x: 9, y: 24 } }, weight: 999 });
                    map.Overlays.add(StorePin);
                    /* 
                        Safezone Generator
                        Add CirCle radius ~5km.
                        ระยะห่าง 1 องศา Lat/Lon = ~111km. @ เส้นศูนย์สูตรโลก
                        ~1km. = 1/111 = 0.009009009009009 degree
                        ~5km. = 0.009009009009009 * 5 = 0.045045045045
                    */
                    var SafeZone = new longdo.Circle({
                        lon: inval['plan_lon'], lat: inval['plan_lat']
                    }, 0.0465, {
                        lineWidth: 2,
                        lineColor: 'rgba(128,252,3,0.8)',
                        fillColor: 'rgba(128,252,3,0.25)'
                    });
                    map.Overlays.add(SafeZone);
                    var LineDistance = new longdo.Polyline([CheckPin.location(),StorePin.location()],{ lineColor: "rgba(154,17,24,1)", lineWidth: 2, lineStyle: longdo.LineStyle.Dashed });
                    map.Overlays.add(LineDistance);
                }
                $("#ModalReportTrip").on("shown.bs.modal", function () {
                    map.resize();
                });
            });
        }
    });
}



$(document).ready(function() {
    CallHead();
    GetEmpName();
    GetCardCode();
    var ViewType = JSON.parse(sessionStorage.getItem('ViewType'));
    if(ViewType != null) {
        $("#filt_view").val(ViewType).change();
    }
    setTimeout(() => {
        var filt_year  = $("#filt_year").val();
        var filt_month = $("#filt_month").val();
        var filt_user  = $("#filt_user").val();
        var filt_view  = $("#filt_view").val();
        GetWorkTrip(filt_year,filt_month,filt_user,filt_view);
    }, 1500);

    $("#filt_year, #filt_month, #filt_user, #filt_view").on("change", function(){
        var filt_year  = $("#filt_year").val();
        var filt_month = $("#filt_month").val();
        var filt_user  = $("#filt_user").val();
        var filt_view  = $("#filt_view").val();
        GetWorkTrip(filt_year,filt_month,filt_user,filt_view);
    });

    $("#filt_view").on("change", function() {
        var ViewType = $(this).val();
        if(sessionStorage.getItem('ViewType') != ViewType) {
            sessionStorage.setItem('ViewType',JSON.stringify(ViewType));
        }
    });
    $(document).off("click","#btn-AddTrip").on("click","#btn-AddTrip", function(e){
        e.preventDefault();
        $("#ModalAddTrip").modal("show");
        $("#CardCode").selectpicker("destroy");
        $("#CardCode").val("NULL").change().attr("disabled",false);
        $("#CardCode").selectpicker();
        $("#NewCardCode, #PlanSale").val("").attr("disabled",false);
        $("#PlanRemark").val("");
        $("#ShowMaps").empty();
        $("#RouteEntry").val(0);

        // var DatePlan = JSON.parse(sessionStorage.getItem('DatePlan'));
        // if(DatePlan != null) {
        //     $("#PlanDate").val(DatePlan);
        // }
        
        /* When Change Cardcode */
        $(document).off("change","#CardCode").on("change","#CardCode", function(){
            var CardCode = $(this).val();
            var PlanDate = $("#PlanDate").val();
            if(CardCode != "NULL") {
                $("#NewCardCode").val('').attr("disabled",true);
                $.ajax({
                    url: "menus/sale/ajax/ajaxroutetrip.php?p=GetGPS",
                    type: "POST",
                    data: { CardCode: CardCode, PlanDate: PlanDate },
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            var GeoLon     = inval['geo_lon'];
                            var GeoLat     = inval['geo_lat'];
                            var CardCode   = inval['geo_cardcode'];
                            var CardName   = inval['geo_cardname'];
                            var PlanSale   = inval['cus_target'];
                            var PlanRemark = inval['cus_Plan'];
                            if((GeoLon != null && GeoLon != "0.000000000000000") && (GeoLat != null && GeoLat != "0.000000000000000")) {
                                ShowMaps(GeoLon,GeoLat);
                            } else {
                                var text_html;
                                text_html = "<div class='d-flex align-items-center justify-content-center text-muted' style='height: 100%'>"+
                                                "<div class='text-center'>"+
                                                    "<i class='fas fa-store-alt-slash fa-fw fa-4x mb-3'></i><br/>"+
                                                    "ไม่มีข้อมูลพิกัด"+
                                                "</div>"+
                                            "</div>"
                                $("#ShowMaps").empty();
                                $("#ShowMaps").html(text_html);
                            }

                            $("#PlanSale").val(PlanSale.toFixed(0));
                            if(PlanRemark != null) {
                                $("#PlanRemark").val(PlanRemark);
                            }
                            
                        });
                    }
                });
            } else {
                $("#NewCardCode").val('').attr("disabled",false);
            }
        });
        $(document).off("focusout","#NewCardCode").on("focusout","#NewCardCode", function(){
            $("#CardCode").selectpicker("destroy");
            var Chk = $(this).val();
            if(Chk.length > 0) {
                $("#CardCode").val('NULL').change().attr("disabled",true);
            } else {
                $("#CardCode").val('NULL').change().attr("disabled",false);
            }
            $("#CardCode").selectpicker();
            $("#NewCardCode").val(Chk);
        });
    });
    $(document).off("click","#btn-CloneTrip").on("click","#btn-CloneTrip", function(e){
        e.preventDefault();
        $("#ModalCopyTrip").modal("show");

        $("#btn-copy-confirm").on("click", function(e){
            e.preventDefault();
            var from_m = $("#copy_from_m").val();
            var from_y = $("#copy_from_y").val();
            var to_m = $("#copy_to_m").val();
            var to_y = $("#copy_to_y").val();

            if(from_m == to_m && from_y == to_y) {
                $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                $("#alert_body").html("ห้ามคัดลอกข้อมูลในเดือน และปีเดียวกัน");
                $("#alert_modal").modal('show');
            } else {
                var user = $("#filt_user").val();
                if(user == "NULL") {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("กรุณากรอกเลือกพนักงาน");
                    $("#alert_modal").modal('show');
                } else {
                    $(".overlay").show();
                    var FormCopyTrip = new FormData($("#FormCopyTrip")[0]);
                    FormCopyTrip.append('u',user);
                    $.ajax({
                        url: "menus/sale/ajax/ajaxroutetrip.php?p=CopyTrip",
                        type: 'POST',
                        cache: false,
                        processData: false,
                        contentType: false,
                        data: FormCopyTrip,
                        success: function(result) {
                            $(".overlay").hide();
                            var obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval){
                                if(inval['copy_status'] == "SUCCESS") {
                                    $(".modal").modal("hide");
                                    $("#confirm_saved").modal('show');
                                    $("#btn-save-reload").on("click", function(e){
                                        e.preventDefault();
                                        window.location.reload();
                                    });
                                } else {
                                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                                    $("#alert_body").html("ไม่สามารถคัดลอกได้เนื่องจากมีข้อมูลอยู่แล้วในเดือนนี้");
                                    $("#alert_modal").modal('show');
                                }
                            });
                        }
                    });
                }
            }
        })
    });
    $(document).off("click","#btn-HistoryTrip").on("click","#btn-HistoryTrip", function(e){
        e.preventDefault();
        $(".modal").modal("hide");
        
        var CheckInName = $("#filt_user option:selected").text();
        $("#CheckInName").html(CheckInName);
        var filt_year  = $("#filt_year").val();
        var filt_month = $("#filt_month").val();
        var filt_user  = $("#filt_user").val();

        $.ajax({
            url: "menus/sale/ajax/ajaxroutetrip.php?p=history",
            type: "POST",
            data: { y: filt_year, m: filt_month, u: filt_user },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#view_history").html(inval['view_history']);
                });
            }
        });





        $("#ModalHistoryTrip").modal("show");
    });


    $("#btn-PrintTrip").on("click", function() {
        let filt_year  = $("#filt_year").val();
        let filt_month = $("#filt_month").val();
        let filt_user  = $("#filt_user").val();
        if(filt_year != null && filt_month != null && filt_user != null) {
            window.open ('menus/sale/print/print_routetrip.php?filt_year='+filt_year+'&filt_month='+filt_month+'&filt_user='+filt_user,'_blank');
        }
    });

    $("#btn-Excel").on("click", function(e) {
        e.preventDefault();
        let filt_year  = $("#filt_year").val();
        let filt_month = $("#filt_month").val();
        let filt_user  = $("#filt_user").val();
        let filt_view  =  $("#filt_view").val();

        $.ajax({
            url: "menus/sale/ajax/ajaxroutetrip.php?p=Excel",
            type: "POST",
            data: { y: filt_year, m: filt_month, u: filt_user, v: filt_view },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    window.open("../../FileExport/Routetrip/"+inval['FileName'],'_blank');
                });
            }
        });
    })
});



</script> 
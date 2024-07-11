<div class="tab-pane fade" id="return-report" role="tabpanel" aria-labelledby="return-report-tab">
    <div class="col-lg">
        <ul class="nav nav-tabsCus" id="tabReturn" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-linkCus active" id="Debt-tab" data-bs-toggle="tab" data-bs-target="#Debt" type="button" role="tab" aria-controls="Debt" aria-selected="true">คืนลดหนี้</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-linkCus" id="QC-tab" data-bs-toggle="tab" data-bs-target="#QC" type="button" role="tab" aria-controls="QC" aria-selected="false">คืน QC</button>
            </li>
        </ul>
        <div class="tab-content" id="tabReturnContent">
            <!-- คืนลดหนี้ -->
            <div class="tab-pane fade show active" id="Debt" role="tabpanel" aria-labelledby="Debt-tab"> 
                <div class="row mt-4">
                    <div class="col-lg-12 col-md-12 d-flex justify-content-between align-items-center">
                        <div class="d-flex"> <!-- Dropdowns Select Year -->
                            <select class="me-1 text-center form-select" style="width: 10rem;" name="SelectYearRT" id="SelectYearRT" onchange="SelectYearRT()">
                                <?php
                                    $Year = date('Y');
                                    $YearLoop = $Year;
                                    echo "<option value='".$Year."' selected>".$Year."</option>";
                                    for ($i = 2015; $i < $YearLoop; $i++) {
                                        --$Year;
                                        echo "<option value='".$Year."'>".$Year."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="me-1" id="Tabs-Debt"> <!-- Menu Tabs คืนลดหนี้ -->
                            <ul class="nav nav-tabs" role="tablist">
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link active bg-light" data-bs-toggle="tab" id="btn-RTall" data-tab="all" href="#RTall"> รวมทั้งหมด</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP006" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id="btn-RTMT1" data-tab="MT1" href="#RTMT1"> MT1</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP007" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id="btn-RTMT2" data-tab="MT2" href="#RTMT2"> MT2</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id="btn-RTTT1" data-tab="TT1" href="#RTTT1"> TT กทม.</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP005" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id="btn-RTTT2" data-tab="TT2" href="#RTTT2"> TT ตจว.</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id="btn-RTOUL" data-tab="OUL" href="#RTOUL"> หน้าร้าน</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id="btn-RTONL" data-tab="ONL" href="#RTONL"> ออนไลน์</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id="btn-RTKBI" data-tab="KBI" href="#RTKBI"> ส่วนกลาง</button>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg">
                        <div class="tab-content"> <!-- Tab Content คืนลดหนี้ -->
                            <!-- รวมทั้งหมด -->
                            <div id="RTall" class="tab-pane active"> 
                                <div class="row">
                                    <div class="col-lg"> <!-- Chart -->
                                        <div id="RTAllReport" class=""></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg"> <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="RTAllReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='16' class='text-primary text-center' id='HRTAll'></th>
                                                    </tr>
                                                    <tr>
                                                        <th class='text-center' width='12%'>ทีมขาย</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th class='text-center' width='7%'>รวมทั้งหมด</th>
                                                        <th class='text-center' width='7%'>ยอดขาย</th>
                                                        <th class='text-center' width='5%'>%</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyRTAll'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MT1 -->
                            <div id="RTMT1" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-lg">
                                        <div id="RTReportMT1" class=""></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg"> <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="RTReportMT1Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='14' class='text-primary text-center' id='HRTMT1'></th>
                                                    </tr>
                                                    <tr>
                                                        <th class='text-center' width='12%'>สาเหตุการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th class='text-center' width='7%'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyRTMT1'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MT2 -->
                            <div id="RTMT2" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-lg">
                                        <div id="RTReportMT2" class=""></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg"> <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="RTReportMT2Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='14' class='text-primary text-center' id='HRTMT2'></th>
                                                    </tr>
                                                    <tr>
                                                        <th class='text-center' width='12%'>สาเหตุการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th class='text-center' width='7%'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyRTMT2'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TT กทม. -->
                            <div id="RTTT1" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-lg">
                                        <div id="RTReportTT1" class=""></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg"> <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="RTReportTT1Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='14' class='text-primary text-center' id='HRTTT1'></th>
                                                    </tr>
                                                    <tr>
                                                        <th class='text-center' width='12%'>สาเหตุการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th class='text-center' width='7%'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyRTTT1'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TT ตจว. -->
                            <div id="RTTT2" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-lg">
                                        <div id="RTReportTT2" class=""></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg"> <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="RTReportTT2Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='14' class='text-primary text-center' id='HRTTT2'></th>
                                                    </tr>
                                                    <tr>
                                                        <th class='text-center' width='12%'>สาเหตุการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th class='text-center' width='7%'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyRTTT2'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- หน้าร้าน -->
                            <div id="RTOUL" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-lg">
                                        <div id="RTReportOUL" class=""></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg"> <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="RTReportOULExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='14' class='text-primary text-center' id='HRTOUL'></th>
                                                    </tr>
                                                    <tr>
                                                        <th class='text-center' width='12%'>สาเหตุการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th class='text-center' width='7%'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyRTOUL'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ออนไลน์ -->
                            <div id="RTONL" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-lg">
                                        <div id="RTReportONL" class=""></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg"> <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="RTReportONLExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='14' class='text-primary text-center' id='HRTONL'></th>
                                                    </tr>
                                                    <tr>
                                                        <th class='text-center' width='12%'>สาเหตุการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th class='text-center' width='7%'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyRTONL'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ส่วนกลาง -->
                            <div id="RTKBI" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-lg">
                                        <div id="RTReportKBI" class=""></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg"> <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="RTReportKBIExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='14' class='text-primary text-center' id='HRTKBI'></th>
                                                    </tr>
                                                    <tr>
                                                        <th class='text-center' width='12%'>สาเหตุการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th class='text-center' width='7%'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyRTKBI'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- คืน QC -->
            <div class="tab-pane fade" id="QC" role="tabpanel" aria-labelledby="QC-tab"> 
                <div class="row mt-4">
                    <div class="col-lg-12 col-md-12 d-flex justify-content-end align-items-center">
                        <!-- Menu Tabs คืน QC -->
                        <div class="me-1" id="Tabs-Debt">
                            <ul class="nav nav-tabs" role="tablist">
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link active bg-light" data-bs-toggle="tab" id='btn-QCall' data-tab='all' href="#QCall"> รวมทั้งหมด</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP006" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id='btn-QCMT1' data-tab='MT1' href="#QCMT1"> MT1</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP007" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id='btn-QCMT2' data-tab='MT2' href="#QCMT2"> MT2</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id='btn-QCTT1' data-tab='TT1' href="#QCTT1"> TT กทม.</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP005" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id='btn-QCTT2' data-tab='TT2' href="#QCTT2"> TT ตจว.</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id='btn-QCOUL' data-tab='OUL' href="#QCOUL"> หน้าร้าน</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id='btn-QCONL' data-tab='ONL' href="#QCONL"> ออนไลน์</button>
                                    </li>
                                <?php } ?>
                                <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id='btn-QCDMN' data-tab='DMN' href="#QCDMN"> เดมอน</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" id='btn-QCKBI' data-tab='KBI' href="#QCKBI"> ส่วนกลาง</button>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg">
                        <!-- Tab Content คืน QC -->
                        <div class="tab-content"> 
                            <!-- รวมทั้งหมด -->
                            <div id="QCall" class="tab-pane active"> 
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCAllReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th width='5%' class='text-center'>ทีม</th>
                                                        <th width='9%' class='text-center'>ความรับผิดชอบ</th>
                                                        <th width='6.5%' class='text-center'>มกราคม</th>
                                                        <th width='6.5%' class='text-center'>กุมภาพันธ์</th>
                                                        <th width='6.5%' class='text-center'>มีนาคม</th>
                                                        <th width='6.5%' class='text-center'>เมษายน</th>
                                                        <th width='6.5%' class='text-center'>พฤษภาคม</th>
                                                        <th width='6.5%' class='text-center'>มิถุนายน</th>
                                                        <th width='6.5%' class='text-center'>กรกฎาคม</th>
                                                        <th width='6.5%' class='text-center'>สิงหาคม</th>
                                                        <th width='6.5%' class='text-center'>กันยายน</th>
                                                        <th width='6.5%' class='text-center'>ตุลาคม</th>
                                                        <th width='6.5%' class='text-center'>พฤศจิกายน</th>
                                                        <th width='6.5%' class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCall'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- MT1 -->
                            <div id="QCMT1" class="tab-pane fade">
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCMT1ReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?> : ทีม MT 1</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%' class='text-center'>ผู้รับผิดชอบ</th>
                                                        <th width='9%' class='text-center'>ประเภทการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCMT1'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- MT2 -->
                            <div id="QCMT2" class="tab-pane fade">
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCMT2ReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?> : ทีม MT 2</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%' class='text-center'>ผู้รับผิดชอบ</th>
                                                        <th width='9%' class='text-center'>ประเภทการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCMT2'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- TT กทม. -->
                            <div id="QCTT1" class="tab-pane fade">
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCTT1ReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?> : ทีม TT 1</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%' class='text-center'>ผู้รับผิดชอบ</th>
                                                        <th width='9%' class='text-center'>ประเภทการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCTT1'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- TT ตจว. -->
                            <div id="QCTT2" class="tab-pane fade">
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCTT2ReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?> : ทีม TT 2</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%' class='text-center'>ผู้รับผิดชอบ</th>
                                                        <th width='9%' class='text-center'>ประเภทการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCTT2'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- หน้าร้าน -->
                            <div id="QCOUL" class="tab-pane fade">
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCOULReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?> : ทีม หน้าร้าน</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%' class='text-center'>ผู้รับผิดชอบ</th>
                                                        <th width='9%' class='text-center'>ประเภทการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCOUL'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ออนไลน์ -->
                            <div id="QCONL" class="tab-pane fade">
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCONLReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?> : ทีม ออนไลน์</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%' class='text-center'>ผู้รับผิดชอบ</th>
                                                        <th width='9%' class='text-center'>ประเภทการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCONL'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- เดมอน -->
                            <div id="QCDMN" class="tab-pane fade">
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCDMNReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?> : ทีม เดมอน</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%' class='text-center'>ผู้รับผิดชอบ</th>
                                                        <th width='9%' class='text-center'>ประเภทการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCDMN'></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ส่วนกลาง -->
                            <div id="QCKBI" class="tab-pane fade">
                                <div class="row">
                                    <!-- Table -->
                                    <div class="col-lg"> 
                                        <div class="table-responsive">
                                            <table id="QCKBIReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                                <thead class='text-center font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                    <tr>
                                                        <th colspan='15' class='text-primary text-center'>ยอดคืน QC ปี <?php echo date("Y"); ?> : ทีม ส่วนกลาง</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='8%' class='text-center'>ผู้รับผิดชอบ</th>
                                                        <th width='9%' class='text-center'>ประเภทการคืน</th>
                                                        <th class='text-center'>มกราคม</th>
                                                        <th class='text-center'>กุมภาพันธ์</th>
                                                        <th class='text-center'>มีนาคม</th>
                                                        <th class='text-center'>เมษายน</th>
                                                        <th class='text-center'>พฤษภาคม</th>
                                                        <th class='text-center'>มิถุนายน</th>
                                                        <th class='text-center'>กรกฎาคม</th>
                                                        <th class='text-center'>สิงหาคม</th>
                                                        <th class='text-center'>กันยายน</th>
                                                        <th class='text-center'>ตุลาคม</th>
                                                        <th class='text-center'>พฤศจิกายน</th>
                                                        <th class='text-center'>ธันวาคม</th>
                                                        <th width='8%' class='text-center'>รวมทั้งหมด</th>
                                                    </tr>
                                                </thead>
                                                <tbody class='font-rps' id='TbodyQCKBI'></tbody>
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
</div>
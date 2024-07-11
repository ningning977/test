<div class="tab-pane fade show active" id="sales-report" role="tabpanel" aria-labelledby="sales-report-tab">
    <div class="row pb-3">
        <div class="col-lg-12 col-md-12 d-flex justify-content-between align-items-center">
            <!-- Dropdowns Select Year -->
            <div class="d-flex">
                <select class="me-1 text-center form-select" style="width: 10rem;" name="YearAll" id="YearAll" onchange="SelectYearAll()">
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
            <!-- Tabs Menu Content -->
            <div class="me-1">
                <ul class="nav nav-tabs" role="tablist">
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link active bg-light" data-bs-toggle="tab" data-tab='all' id='IDall' href="#all"> รวมทั้งหมด</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP006" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" data-tab='MT1' id='IDMT1' href="#MT1"> MT1</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP007" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" data-tab='MT2' id='IDMT2' href="#MT2"> MT2</button>
                        </li>
                    <?php } ?> 
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" data-tab='TT1' id='IDTT1' href="#TT1"> TT กทม.</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP005" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" data-tab='TT2' id='IDTT2' href="#TT2"> TT ตจว.</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" data-tab='OUL' id='IDOUL' href="#storefront"> หน้าร้าน</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" data-tab='ONL' id='IDONL' href="#online"> ออนไลน์</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" data-bs-toggle="tab" data-tab='KBI' id='IDKBI' href="#center"> ส่วนกลาง</button>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <button class="nav-link bg-light EXP" style="width: 104.92px;" data-bs-toggle="tab" data-tab='EXP' id='IDEXP' href="#EXP"> ต่างประเทศ</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg">
            <!-- Tabs Content -->
            <div class="tab-content">
                <!-- รวมทั้งหมด -->
                <div id="all" class="tab-pane active">
                    <div class="row">
                        <div class="col-lg">
                            <div id="AllReport" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="AllReportExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' id='DataTheadAll' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HAll'>ยอดขายปี </th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='15%'>ทีมขาย</th>
                                            <th class='text-center' width='6.4%'>มกราคม</th>
                                            <th class='text-center' width='6.4%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.4%'>มีนาคม</th>
                                            <th class='text-center' width='6.4%'>เมษายน</th>
                                            <th class='text-center' width='6.4%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.4%'>มิถุนายน</th>
                                            <th class='text-center' width='6.4%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.4%'>สิงหาคม</th>
                                            <th class='text-center' width='6.4%'>กันยายน</th>
                                            <th class='text-center' width='6.4%'>ตุลาคม</th>
                                            <th class='text-center' width='6.4%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.4%'>ธันวาคม</th>
                                            <th class='text-center' width='8.2%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyAll'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MT1 -->
                <div id="MT1" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg">
                            <div id="ReportMT1" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ReportMT1Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HMT1'></th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='11.4%'>กลุ่มลูกค้า</th>
                                            <th class='text-center' width='6.8%'>มกราคม</th>
                                            <th class='text-center' width='6.8%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.8%'>มีนาคม</th>
                                            <th class='text-center' width='6.8%'>เมษายน</th>
                                            <th class='text-center' width='6.8%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.8%'>มิถุนายน</th>
                                            <th class='text-center' width='6.8%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.8%'>สิงหาคม</th>
                                            <th class='text-center' width='6.8%'>กันยายน</th>
                                            <th class='text-center' width='6.8%'>ตุลาคม</th>
                                            <th class='text-center' width='6.8%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.8%'>ธันวาคม</th>
                                            <th class='text-center' width='8%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyMT1'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MT2 -->
                <div id="MT2" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg">
                            <div id="ReportMT2" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ReportMT2Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HMT2'></th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='11.4%'>กลุ่มลูกค้า</th>
                                            <th class='text-center' width='6.8%'>มกราคม</th>
                                            <th class='text-center' width='6.8%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.8%'>มีนาคม</th>
                                            <th class='text-center' width='6.8%'>เมษายน</th>
                                            <th class='text-center' width='6.8%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.8%'>มิถุนายน</th>
                                            <th class='text-center' width='6.8%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.8%'>สิงหาคม</th>
                                            <th class='text-center' width='6.8%'>กันยายน</th>
                                            <th class='text-center' width='6.8%'>ตุลาคม</th>
                                            <th class='text-center' width='6.8%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.8%'>ธันวาคม</th>
                                            <th class='text-center' width='8%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyMT2'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TT กทม. -->
                <div id="TT1" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg">
                            <div id="ReportTT1" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ReportTT1Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HTT1'></th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='11.4%'>กลุ่มลูกค้า</th>
                                            <th class='text-center' width='6.8%'>มกราคม</th>
                                            <th class='text-center' width='6.8%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.8%'>มีนาคม</th>
                                            <th class='text-center' width='6.8%'>เมษายน</th>
                                            <th class='text-center' width='6.8%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.8%'>มิถุนายน</th>
                                            <th class='text-center' width='6.8%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.8%'>สิงหาคม</th>
                                            <th class='text-center' width='6.8%'>กันยายน</th>
                                            <th class='text-center' width='6.8%'>ตุลาคม</th>
                                            <th class='text-center' width='6.8%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.8%'>ธันวาคม</th>
                                            <th class='text-center' width='8%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyTT1'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TT ตจว. -->
                <div id="TT2" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg">
                            <div id="ReportTT2" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ReportTT2Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HTT2'></th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='11.4%'>กลุ่มลูกค้า</th>
                                            <th class='text-center' width='6.8%'>มกราคม</th>
                                            <th class='text-center' width='6.8%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.8%'>มีนาคม</th>
                                            <th class='text-center' width='6.8%'>เมษายน</th>
                                            <th class='text-center' width='6.8%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.8%'>มิถุนายน</th>
                                            <th class='text-center' width='6.8%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.8%'>สิงหาคม</th>
                                            <th class='text-center' width='6.8%'>กันยายน</th>
                                            <th class='text-center' width='6.8%'>ตุลาคม</th>
                                            <th class='text-center' width='6.8%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.8%'>ธันวาคม</th>
                                            <th class='text-center' width='8%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyTT2'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- หน้าร้าน -->
                <div id="storefront" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg">
                            <div id="ReportOUL" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ReportOULExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HOUL'></th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='11.4%'>กลุ่มลูกค้า</th>
                                            <th class='text-center' width='6.8%'>มกราคม</th>
                                            <th class='text-center' width='6.8%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.8%'>มีนาคม</th>
                                            <th class='text-center' width='6.8%'>เมษายน</th>
                                            <th class='text-center' width='6.8%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.8%'>มิถุนายน</th>
                                            <th class='text-center' width='6.8%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.8%'>สิงหาคม</th>
                                            <th class='text-center' width='6.8%'>กันยายน</th>
                                            <th class='text-center' width='6.8%'>ตุลาคม</th>
                                            <th class='text-center' width='6.8%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.8%'>ธันวาคม</th>
                                            <th class='text-center' width='8%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyOUL'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ออนไลน์ -->
                <div id="online" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg">
                            <div id="ReportONL" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ReportONLExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HONL'></th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='11.4%'>กลุ่มลูกค้า</th>
                                            <th class='text-center' width='6.8%'>มกราคม</th>
                                            <th class='text-center' width='6.8%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.8%'>มีนาคม</th>
                                            <th class='text-center' width='6.8%'>เมษายน</th>
                                            <th class='text-center' width='6.8%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.8%'>มิถุนายน</th>
                                            <th class='text-center' width='6.8%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.8%'>สิงหาคม</th>
                                            <th class='text-center' width='6.8%'>กันยายน</th>
                                            <th class='text-center' width='6.8%'>ตุลาคม</th>
                                            <th class='text-center' width='6.8%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.8%'>ธันวาคม</th>
                                            <th class='text-center' width='8%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyONL'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ส่วนกลาง -->
                <div id="center" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg">
                            <div id="ReportKBI" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ReportKBIExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HKBI'></th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='11.4%'>กลุ่มลูกค้า</th>
                                            <th class='text-center' width='6.8%'>มกราคม</th>
                                            <th class='text-center' width='6.8%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.8%'>มีนาคม</th>
                                            <th class='text-center' width='6.8%'>เมษายน</th>
                                            <th class='text-center' width='6.8%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.8%'>มิถุนายน</th>
                                            <th class='text-center' width='6.8%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.8%'>สิงหาคม</th>
                                            <th class='text-center' width='6.8%'>กันยายน</th>
                                            <th class='text-center' width='6.8%'>ตุลาคม</th>
                                            <th class='text-center' width='6.8%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.8%'>ธันวาคม</th>
                                            <th class='text-center' width='8%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyKBI'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ต่างประเทศ -->
                <div id="EXP" class="tab-pane fade">
                    <div class="row">
                        <div class="col-lg">
                            <div id="ReportEXP" class=""></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ReportEXPExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th colspan='14' class='text-primary text-center' id='HEXP'></th>
                                        </tr>
                                        <tr>
                                            <th class='text-center' width='11.4%'>กลุ่มลูกค้า</th>
                                            <th class='text-center' width='6.8%'>มกราคม</th>
                                            <th class='text-center' width='6.8%'>กุมภาพันธ์</th>
                                            <th class='text-center' width='6.8%'>มีนาคม</th>
                                            <th class='text-center' width='6.8%'>เมษายน</th>
                                            <th class='text-center' width='6.8%'>พฤษภาคม</th>
                                            <th class='text-center' width='6.8%'>มิถุนายน</th>
                                            <th class='text-center' width='6.8%'>กรกฎาคม</th>
                                            <th class='text-center' width='6.8%'>สิงหาคม</th>
                                            <th class='text-center' width='6.8%'>กันยายน</th>
                                            <th class='text-center' width='6.8%'>ตุลาคม</th>
                                            <th class='text-center' width='6.8%'>พฤศจิกายน</th>
                                            <th class='text-center' width='6.8%'>ธันวาคม</th>
                                            <th class='text-center' width='8%'>รวมทั้งหมด</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='DataTbodyEXP'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
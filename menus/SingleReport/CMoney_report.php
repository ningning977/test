<div class="tab-pane fade" id="CMoney-report" role="tabpanel" aria-labelledby="CMoney-report-tab">
    <div class="row mt-4">
        <div class="col-lg-12 col-md-12 d-flex justify-content-end align-items-center">
            <!-- Menu Tabs รายงานการเก็บเงิน -->
            <div class="me-1" id="Tabs-Debt">
                <ul class="nav nav-tabs" role="tablist">
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link active bg-light" id='btn-CMall' data-tab='all' data-bs-toggle="tab" href="#CMall"> รวมทั้งหมด</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP006" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" id='btn-CMMT1' data-tab='MT1' data-bs-toggle="tab" href="#CMMT1"> MT1</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP007" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" id='btn-CMMT2' data-tab='MT2' data-bs-toggle="tab" href="#CMMT2"> MT2</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" id='btn-CMTT1' data-tab='TT1' data-bs-toggle="tab" href="#CMTT1"> TT กทม.</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP005" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" id='btn-CMTT2' data-tab='TT2' data-bs-toggle="tab" href="#CMTT2"> TT ตจว.</button>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004" || $_SESSION['DeptCode'] == "DP008" || $_SESSION['DeptCode'] == "DP009" || $_SESSION['DeptCode'] == "DP010" || $_SESSION['DeptCode'] == "DP012") { ?>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" id='btn-CMOUL' data-tab='OUL' data-bs-toggle="tab" href="#CMOUL"> หน้าร้าน</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link bg-light" style="width: 104.92px;" id='btn-CMONL' data-tab='ONL' data-bs-toggle="tab" href="#CMONL"> ออนไลน์</button>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg">
            <!-- Tab Content รายงานการเก็บเงิน -->
            <div class="tab-content"> 
                <!-- รวมทั้งหมด -->
                <div id="CMall" class="tab-pane active"> 
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg"> 
                            <div class="table-responsive">
                                <table id="CMallExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead id='TheadCMall' class='text-center font-rps text-light fw-bold' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th width=''>ทีม</th>
                                            <th width=''>รายละเอียด</th>
                                            <th width=''>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน ... ....)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyCMall'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MT1 -->
                <div id="CMMT1" class="tab-pane fade">
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg"> 
                            <div class="table-responsive">
                                <table id="CMMT1Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='text-center font-rps' id='TheadCMMT1' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <!-- <tr>
                                            <th class='text-primary' colspan='2'>ทีม ...</th>
                                        </tr> -->
                                        <tr>
                                            <th>รายละเอียด</th>
                                            <th>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน ... ...)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyCMMT1'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MT2 -->
                <div id="CMMT2" class="tab-pane fade">
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg"> 
                            <div class="table-responsive">
                                <table id="CMMT2Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='text-center font-rps' id='TheadCMMT2' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th class='text-primary' colspan='2'>ทีม ...</th>
                                        </tr>
                                        <tr>
                                            <th>รายละเอียด</th>
                                            <th>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน ... ...)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyCMMT2'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TT กทม. -->
                <div id="CMTT1" class="tab-pane fade">
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg"> 
                            <div class="table-responsive">
                                <table id="CMTT1Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='text-center font-rps' id='TheadCMTT1' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th class='text-primary' colspan='2'>ทีม ...</th>
                                        </tr>
                                        <tr>
                                            <th>รายละเอียด</th>
                                            <th>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน ... ...)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyCMTT1'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TT ตจว. -->
                <div id="CMTT2" class="tab-pane fade">
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg"> 
                            <div class="table-responsive">
                                <table id="CMTT2Excel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='text-center font-rps' id='TheadCMTT2' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th class='text-primary' colspan='2'>ทีม ...</th>
                                        </tr>
                                        <tr>
                                            <th>รายละเอียด</th>
                                            <th>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน ... ...)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyCMTT2'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- หน้าร้าน -->
                <div id="CMOUL" class="tab-pane fade">
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg"> 
                            <div class="table-responsive">
                                <table id="CMOULExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='text-center font-rps' id='TheadCMOUL' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th class='text-primary' colspan='2'>ทีม ...</th>
                                        </tr>
                                        <tr>
                                            <th>รายละเอียด</th>
                                            <th>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน ... ...)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyCMOUL'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ออนไลน์ -->
                <div id="CMONL" class="tab-pane fade">
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg"> 
                            <div class="table-responsive">
                                <table id="CMONLExcel" class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='text-center font-rps' id='TheadCMONL' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th class='text-primary' colspan='2'>ทีม ...</th>
                                        </tr>
                                        <tr>
                                            <th>รายละเอียด</th>
                                            <th>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน ... ...)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyCMONL'></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
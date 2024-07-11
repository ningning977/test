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
                <div class="row">
                    <div class="col-lg">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button onclick='CallData();' class="nav-link text-primary active" id="Tab1-tab" data-bs-toggle="tab" data-bs-target="#Tab1" type="button" role="tab" aria-controls="Tab1" aria-selected="false"><i class="fas fa-list"></i> รายการ Log</button>
                                <?php if($_SESSION['LvCode'] == 'LV006' || $_SESSION['LvCode'] == 'LV008') { $Dis = ""; } else { $Dis = "disabled text-reset"; } ?>
                                <button onclick='CallData2();' class="nav-link text-primary <?php echo $Dis; ?>"  id="Tab2-tab" data-bs-toggle="tab" data-bs-target="#Tab2" type="button" role="tab" aria-controls="Tab2" aria-selected="false"><i class="fas fa-tasks"></i> อนุมัติ Log</button>
                                <input type="hidden" name='LogTab' id='LogTab' value='1'>
                            </div>
                        </nav>
                        
                        <div class="tab-content pt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="Tab1" role="tabpanel" aria-labelledby="Tab1-tab">
                                <div class="row">
                                    <div class="col-lg-auto">
                                        <div class="form-group" style='width: 120px;'>
                                            <label for="sYear">เลือกปี</label>
                                            <select class="form-select form-select-sm" name="sYear" id="sYear" onchange='CallData();'>
                                                <?php
                                                for($y = date("Y"); $y >= 2023; $y--) {
                                                    if($y == date("Y")) {
                                                        echo "<option value='".$y."' selected>".$y."</option>";
                                                    }else{
                                                        echo "<option value='".$y."'>".$y."</option>";
                                                    }
                                                } 
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="form-group" style='width: 120px;'>
                                            <label for="sMonth">เลือกเดือน</label>
                                            <select class="form-select form-select-sm" name="sMonth" id="sMonth" onchange='CallData();'>
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
                                        </div>
                                    </div>
                                    <?php if($_SESSION['DeptCode'] != 'DP001') { ?>
                                        <div class="col-lg d-flex justify-content-end">
                                            <div class='align-self-center' style='width: 120px;'>
                                                <button class='btn btn-sm btn-primary' style='margin-top: 10px;' onclick="ModalAddLog();"><i class="fas fa-plus"></i> เพิ่ม Log</button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table1'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th width='5%'>No.</th>
                                                        <th width='10%'>เลขที่เอกสาร</th>
                                                        <th>หมวดหมู่</th>
                                                        <th>หัวข้อร้องเรียน</th>
                                                        <th width='13%'>ชื่อ</th>
                                                        <th width='15%'>วันที่แจ้ง</th>
                                                        <th width='10%'>สถานะ</th>
                                                        <th width='8%'><i class="fas fa-cog"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="Tab2" role="tabpanel" aria-labelledby="Tab2-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table2'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th width='5%'>No.</th>
                                                        <th width='10%'>เลขที่เอกสาร</th>
                                                        <th>หมวดหมู่</th>
                                                        <th>หัวข้อร้องเรียน</th>
                                                        <th width='13%'>ชื่อ</th>
                                                        <th width='15%'>วันที่แจ้ง</th>
                                                        <th width='10%'>สถานะ</th>
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

<div class="modal fade" id="ModalViewLog" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียด Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-borderless' style='font-size: 12.5px;'>
                                <tr>
                                    <th>หมวดหมู่</th>
                                    <td colspan='4'><span id='vCategory'></span></td>
                                    <th class='text-right'><span id='vStatusDoc'></span></th>
                                </tr>
                                <tr>
                                    <th width='18%'>ช่องทางการแจ้ง</th>
                                    <td width='22%'><span id='vCompMethod'></span></td>

                                    <th width='12%'>ผู้แจ้ง</th>
                                    <td width='12%'><span id='vCompUser'></span></td>

                                    <th>แผนกผู้แจ้ง</th>
                                    <td><span id='vDeptCode'></span></td>
                                </tr>
                                <tr>
                                    <th>หัวข้อที่แจ้ง</th>
                                    <td><span id='vLogTitle'></span></td>

                                    <th>วันที่แจ้ง</th>
                                    <td colspan='3'><span id='vCompDate'></span></td>
                                </tr>
                                <tr>
                                    <td colspan='6'>
                                        <span class='fw-bolder'>รายละเอียดที่แจ้ง</span>
                                        <textarea class='form-control form-control-sm' style='background-color: #fff;' disabled name='vLogDetail' id='vLogDetail' style='font-size: 12.5px;'></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th>ผู้แก้ปัญหา</th>
                                    <td><span id='vUkeySolution'></span></td>

                                    <th>วันที่แก้ปัญหา</th>
                                    <td colspan='3'><span id='vDateSolution'></span></td>
                                </tr>
                                <tr>
                                    <td colspan='6'>
                                        <span class='fw-bolder'>วิธีการแก้ปัญหา</span>
                                        <textarea class='form-control form-control-sm' style='background-color: #fff;' disabled name='vLogSolution' id='vLogSolution' style='font-size: 12.5px;'></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='6'>
                                        <span class='fw-bolder'><i class="fas fa-paperclip fa-fw"></i> ไฟล์แนบ</span>
                                        <div class='d-flex'>
                                            <div style='width: 50%;' id="vImg" class='border p-2'></div>
                                            <div style='width: 50%;' id='vDoc' class='p-2'></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class='pb-0'>ผู้อนุมัติ</th>
                                    <td class='pb-0' colspan='5'><span id='vUkeyApp'></span></td>
                                </tr>
                                <tr>
                                    <th>วันที่อนุมัติ</th>
                                    <td colspan='5'><span id='vDateApp'></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
                <button type="button" class="btn btn-primary btn-sm btn-app" onclick="AppLog();">อนุมัติ</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAddLog" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus" style='font-size: 15px;'></i> เพิ่ม Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form" id="DataForm" enctype="multipart/form-data"> 
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="Category">เลือกหมวดหมู่</label><span class='text-primary fw-bolder'>*</span>
                                <select class="form-select form-select-sm" name="Category" id="Category" onchange="ChkCategory();">
                                    <option value="" selected disabled>เลือกหมวดหมู่</option>
                                    <option value="1">ปัญหาเครื่องคอมพิวเตอร์</option>
                                    <option value="2">ปัญหาเครือข่ายอินเตอร์เน็ต (ภายใน)</option>
                                    <option value="3">ปัญหาอุปกรณ์ IT</option>
                                    <option value="16">CCTV</option>
                                    <option value="17">Solar Cell</option>
                                    <option value="18">โทรศัพท์ภายใน</option>
                                    <option value="19">โอนย้ายทรัพย์สิน</option>
                                    <option value="4">ปัญหาระบบ EUROX FORCE</option>
                                    <option value="5">ปัญหาระบบ WMS</option>
                                    <option value="6">ปัญหาระบบ SAP</option>
                                    <option value="7">ปัญหาระบบ HRMI</option>
                                    <option value="8">ปัญหาระบบ ESS</option>
                                    <option value="20">ปัญหาระบบ CRM</option>
                                    <option value="9">ขอเพิ่มระบบงานใน EUROX FORCE</option>
                                    <option value="10">ขอเพิ่มรายงาน Excel</option>
                                    <option value="12">เพิ่มข้อมูลใน EUROX FORCE</option>
                                    <option value="21">ขอแก้ไขข้อมูลใน EUROX FORCE</option>
                                    <option value="22">ขอแก้ไขข้อมูลใน WMS</option>
                                    <option value="13">Project MA</option>
                                    <option value="14">Project IT</option>
                                    <option value="15">ประชุม</option>
                                    <option value="11">อื่น ๆ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group " id='FormRemark'>
                                <label for="CategoryRemark">อื่น ๆ โปรดระบุ</label><span class='text-primary fw-bolder'>*</span>
                                <input type='txet' class='form-control form-control-sm' name='CategoryRemark' id='CategoryRemark'>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="CompDate">วันที่แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <input type='datetime-local' class='form-control form-control-sm' name='CompDate' id='CompDate'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="CompMethod">เลือกช่องทางการแจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <select class="form-select form-select-sm" name="CompMethod" id="CompMethod">
                                    <option value="" selected disabled>เลือกช่องทางการแจ้ง</option>
                                    <option value="TEL">โทรศัพท์</option>
                                    <option value="LNG">LINE กลุ่ม</option>
                                    <option value="LNP">LINE ส่วนตัว</option>
                                    <option value="SPK">แจ้งปากเปล่า</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="CompUser">ผู้แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <input type='txet' class='form-control form-control-sm' name='CompUser' id='CompUser' placeholder='ระบุชื่อผู้แจ้ง'>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="DeptCode">แผนกผู้แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <select class='form-select form-select-sm' name='DeptCode' id='DeptCode'>
                                    <option value="" selected disabled>เลือกแผนกผู้แจ้ง</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr class='mt-2 mb-2'>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="LogTitle">หัวข้อที่แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <input type='txet' class='form-control form-control-sm' name='LogTitle' id='LogTitle'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="LogDetail">รายละเอียดที่แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <textarea class='form-control form-control-sm' name='LogDetail' id='LogDetail'></textarea>
                            </div>
                        </div>
                    </div>
                    <hr class='mt-2 mb-2'>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="UkeySolution">เลือกผู้แก้ปัญหา</label>
                                <select class="form-select form-select-sm" name="UkeySolution" id="UkeySolution">
                                    <option value="" selected disabled>เลือกผู้แก้ปัญหา</option>
                                    <?php 
                                        $SQL = "SELECT CONCAT(T1.uName, ' ',T1.uLastName, ' (', T1.uNickName, ')') AS FullName, T1.uKey 
                                                FROM users T1
                                                LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
                                                WHERE T1.UserStatus = 'A' AND T2.DeptCode = 'DP002'";
                                        $QRY = MySQLSelectX($SQL); 
                                        while($result = mysqli_fetch_array($QRY)) {
                                            echo "<option value='".$result['uKey']."'>".$result['FullName']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="DateSolution">วันที่แก้ปัญหา</label>
                                <input type='datetime-local' class='form-control form-control-sm' name='DateSolution' id='DateSolution'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="LogSolution">วิธีการแก้ปัญหา</label>
                                <textarea class='form-control form-control-sm' name='LogSolution' id='LogSolution'></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="FileAttach">ไฟล์แนบ</label>
                                <input type='file' class='form-control form-control-sm' accept="" name='FileAttach[]' id="FileAttach" multiple>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="AddLog();">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalEditLog" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus" style='font-size: 15px;'></i> เพิ่ม Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form" id="eDataForm" enctype="multipart/form-data"> 
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="eCategory">เลือกหมวดหมู่</label><span class='text-primary fw-bolder'>*</span>
                                <select class="form-select form-select-sm" name="eCategory" id="eCategory" onchange="eChkCategory();">
                                <option value="1">ปัญหาเครื่องคอมพิวเตอร์</option>
                                    <option value="2">ปัญหาเครือข่ายอินเตอร์เน็ต (ภายใน)</option>
                                    <option value="3">ปัญหาอุปกรณ์ IT</option>
                                    <option value="16">CCTV</option>
                                    <option value="17">Solar Cell</option>
                                    <option value="18">โทรศัพท์ภายใน</option>
                                    <option value="19">โอนย้ายทรัพย์สิน</option>
                                    <option value="4">ปัญหาระบบ EUROX FORCE</option>
                                    <option value="5">ปัญหาระบบ WMS</option>
                                    <option value="6">ปัญหาระบบ SAP</option>
                                    <option value="7">ปัญหาระบบ HRMI</option>
                                    <option value="8">ปัญหาระบบ ESS</option>
                                    <option value="20">ปัญหาระบบ CRM</option>
                                    <option value="9">ขอเพิ่มระบบงานใน EUROX FORCE</option>
                                    <option value="10">ขอเพิ่มรายงาน Excel</option>
                                    <option value="12">เพิ่มข้อมูลใน EUROX FORCE</option>
                                    <option value="21">ขอแก้ไขข้อมูลใน EUROX FORCE</option>
                                    <option value="22">ขอแก้ไขข้อมูลใน WMS</option>
                                    <option value="13">Project MA</option>
                                    <option value="14">Project IT</option>
                                    <option value="15">ประชุม</option>
                                    <option value="11">อื่น ๆ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group " id='eFormRemark'>
                                <label for="eCategoryRemark">อื่น ๆ โปรดระบุ</label><span class='text-primary fw-bolder'>*</span>
                                <input type='txet' class='form-control form-control-sm' name='eCategoryRemark' id='eCategoryRemark'>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="eCompDate">วันที่แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <input type='datetime-local' class='form-control form-control-sm' name='eCompDate' id='eCompDate'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="eCompMethod">เลือกช่องทางการแจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <select class="form-select form-select-sm" name="eCompMethod" id="eCompMethod">
                                    <option value="" selected disabled>เลือกช่องทางการแจ้ง</option>
                                    <option value="TEL">โทรศัพท์</option>
                                    <option value="LNG">LINE กลุ่ม</option>
                                    <option value="LNP">LINE ส่วนตัว</option>
                                    <option value="SPK">แจ้งปากเปล่า</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="eCompUser">ผู้แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <input type='txet' class='form-control form-control-sm' name='eCompUser' id='eCompUser' placeholder='ระบุชื่อผู้แจ้ง'>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="eDeptCode">แผนกผู้แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <select class='form-select form-select-sm' name='eDeptCode' id='eDeptCode'>
                                    <option value="" selected disabled>เลือกแผนกผู้แจ้ง</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr class='mt-2 mb-2'>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="eLogTitle">หัวข้อที่แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <input type='txet' class='form-control form-control-sm' name='eLogTitle' id='eLogTitle'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="eLogDetail">รายละเอียดที่แจ้ง</label><span class='text-primary fw-bolder'>*</span>
                                <textarea class='form-control form-control-sm' name='eLogDetail' id='eLogDetail'></textarea>
                            </div>
                        </div>
                    </div>
                    <hr class='mt-2 mb-2'>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="eUkeySolution">เลือกผู้แก้ปัญหา</label>
                                <select class="form-select form-select-sm" name="eUkeySolution" id="eUkeySolution">
                                    <option value="" selected disabled>เลือกผู้แก้ปัญหา</option>
                                    <?php 
                                        $SQL = "SELECT CONCAT(T1.uName, ' ',T1.uLastName, ' (', T1.uNickName, ')') AS FullName, T1.uKey 
                                                FROM users T1
                                                LEFT JOIN positions T2 ON T1.LvCode = T2.LvCode
                                                WHERE T1.UserStatus = 'A' AND T2.DeptCode = 'DP002'";
                                        $QRY = MySQLSelectX($SQL); 
                                        while($result = mysqli_fetch_array($QRY)) {
                                            echo "<option value='".$result['uKey']."'>".$result['FullName']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="eDateSolution">วันที่แก้ปัญหา</label>
                                <input type='datetime-local' class='form-control form-control-sm' name='eDateSolution' id='eDateSolution'>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="eLogSolution">วิธีการแก้ปัญหา</label>
                                <textarea class='form-control form-control-sm' name='eLogSolution' id='eLogSolution'></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="eFileAttach">ไฟล์แนบ</label>
                                <input type='file' class='form-control form-control-sm' accept="" name='eFileAttach[]' id="eFileAttach" multiple>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="EditLog();">บันทึก</button>
                <input type="hidden" name='LogEntry' id='LogEntry'>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDelete" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="DeleteHeader"></h5>
                <p id="DeleteBody" class="my-4 pb-3"></p>
                <button type="button" class="btn btn-secondary btn-sm me-4" data-bs-dismiss="modal">ออก</button>
                <button type="button" class="btn btn-primary btn-sm ms-4" onclick='DelectLog();'>ตกลง</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
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
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */

$(document).ready(function(){
    CallData();
    GetDeptCode();
});

function GetDeptCode() {
    $.ajax({
        url: "menus/it/ajax/ajaxlog_it.php?a=GetDeptCode",
        type: "GET",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#DeptCode").append(inval['option']);
                $("#eDeptCode").append(inval['option']);
            })
        }
    });
}

function CallData() {
    $("#LogTab").val("1");
    var Year  = $("#sYear").val();
    var Month = $("#sMonth").val();
    $.ajax({
        url: "menus/it/ajax/ajaxlog_it.php?a=CallData",
        type: "POST",
        data: { Year : Year, Month : Month, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                var Tbody = "";
                for(var r = 1; r <= inval['Row']; r++) {
                    Tbody +="<tr>"+
                                "<td class='text-center'>"+r+"</td>"+
                                "<td class='text-center'>"+inval['Data']['LogNum'][r]+"</td>"+
                                "<td>"+inval['Data']['Category'][r]+"</td>"+
                                "<td>"+inval['Data']['LogTitle'][r]+"</td>"+
                                "<td>"+inval['Data']['Name'][r]+"</td>"+
                                "<td class='text-center'>"+inval['Data']['CompDate'][r]+"</td>"+
                                "<td class='text-center'>"+inval['Data']['StatusDoc'][r]+"</td>"+
                                "<td class='text-center'>"+inval['Data']['Setting'][r]+"</td>"+
                            "</tr>";
                }
                $("#Table1 tbody").html(Tbody);
            });
        }
    })
}

function View(LogEntry) {
    $("#LogEntry").val(LogEntry);
    $.ajax({
        url: "menus/it/ajax/ajaxlog_it.php?a=View",
        type: "POST",
        data: { LogEntry : LogEntry, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#vCategory").html(inval['Category']);
                $("#vCompMethod").html(inval['CompMethod']);
                $("#vCompUser").html(inval['CompUser']);
                $("#vDeptCode").html(inval['DeptCode']);
                $("#vCompDate").html(inval['CompDate']);
                $("#vLogTitle").html(inval['LogTitle']);
                $("#vLogDetail").html(inval['LogDetail']);
                $("#vUkeySolution").html(inval['UkeySolution']);
                $("#vDateSolution").html(inval['DateSolution']);
                $("#vLogSolution").html(inval['LogSolution']);
                $("#vImg").html(inval['DataImg']);
                $("#vDoc").html(inval['DataDoc']);
                $("#vUkeyApp").html(inval['UkeyApp']);
                $("#vDateApp").html(inval['DateApp']);
                $("#vStatusDoc").html(inval['StatusDoc']);

                if($("#LogTab").val() == "2") {
                    $(".btn-app").show();
                }else{
                    $(".btn-app").hide();
                }
                $("#ModalViewLog .modal-title").html("<i class='fas fa-book-open fs-5'></i>&nbsp;&nbsp;&nbsp;รายละเอียด Log เลขที่ "+inval['LogNum']);
                $("#ModalViewLog").modal("show");
            });
        }
    })
}

function Edit(LogEntry) {
    $.ajax({
        url: "menus/it/ajax/ajaxlog_it.php?a=Edit",
        type: "POST",
        data: { LogEntry : LogEntry, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#LogEntry").val(inval['LogEntry']);
                $("#eCategory").val(inval['Category']); eChkCategory();
                $("#eCategoryRemark").val(inval['CategoryRemark']);
                $("#eCompMethod").val(inval['CompMethod']);
                $("#eCompUser").val(inval['CompUser']);
                $("#eDeptCode").val(inval['DeptCode']).change();
                $("#eCompDate").val(inval['CompDate']);
                $("#eLogTitle").val(inval['LogTitle']);
                $("#eLogDetail").val(inval['LogDetail']);
                $("#eUkeySolution").val(inval['UkeySolution']);
                $("#eDateSolution").val(inval['DateSolution']);
                $("#eLogSolution").val(inval['LogSolution']);
                $("#eFileAttach").val("");   // ไฟล์แนบ

                $("#ModalEditLog").modal("show");
            });
        }
    })
}

function eChkCategory() {
    if($("#eCategory").val() == 11) {
        $("#eFormRemark").removeClass("d-none");
        $("#eCategoryRemark").val("");
        $("#eCategoryRemark").focus();
    }else{
        $("#eFormRemark").addClass("d-none");
    }
}
$("#eCategoryRemark").focus(function() {
    $("#eCategoryRemark").removeClass("is-invalid");
})

function EditLog() {
    var Category     = $("#eCategory").val();     // หมวดหมู่
    var CompMethod   = $("#eCompMethod").val();   // ช่องทางการแจ้ง
    var CompUser     = $("#eCompUser").val();     // ผู้แจ้ง
    var CompDate     = $("#eCompDate").val();     // วันที่แจ้ง
    var LogTitle     = $("#eLogTitle").val();     // หัวข้อที่แจ้ง
    var LogDetail    = $("#eLogDetail").val();    // รายละเอียดที่แจ้ง
    var DeptCode     = $("#eDeptCode").val();     // แผนกผู้แจ้ง
    var DataForm = new FormData($("#eDataForm")[0]);
    DataForm.append('LogEntry',$("#LogEntry").val());
    if(Category != "" && CompMethod != "" && CompUser != "" && CompDate != "" && LogTitle != "" && LogDetail != "" && DeptCode != null) {
        if(Category == 11) {
            if($("#eCategoryRemark").val() != "") { $Chk = "Y"; }else{ $Chk = "N"; }
        }else{
            $Chk = "Y";
        }
        if($Chk == "Y") {
            $.ajax({
                url: "menus/it/ajax/ajaxlog_it.php?a=EditLog",
                type: 'POST',
                dataType: 'text',
                cache: false,
                processData: false,
                contentType: false,
                data: DataForm,
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 65px;'></i>");
                        $("#alert_body").html("บันทึกข้อมูลสำเร็จ");
                        $("#alert_modal").modal("show");
                        $("#ModalEditLog").modal("hide"); 
                        CallData();
                    });
                } 
            })
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 65px;'></i>");
            $("#alert_body").html("หมวดหมู่อื่น ๆ กรุณาโปรดระบุ");
            $("#alert_modal").modal("show");
            $("#eCategoryRemark").addClass("is-invalid");
        }
    }else{
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 65px;'></i>");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal("show");
    }
}

function Delete(LogEntry) {
    $("#LogEntry").val(LogEntry);
    $("#DeleteHeader").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
    $("#DeleteBody").html("คุณต้องลบรายการ Log นี้หรือไม่ ?");
    $("#ModalDelete").modal("show");
}

function DelectLog() {
    var LogEntry = $("#LogEntry").val();
    $.ajax({
        url: "menus/it/ajax/ajaxlog_it.php?a=DelectLog",
        type: "POST",
        data: { LogEntry : LogEntry, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 65px;'></i>");
                $("#alert_body").html("ลบข้อมูลสำเร็จ");
                $("#alert_modal").modal("show");
                $("#ModalDelete").modal("hide");
                CallData();
            });
        }
    })
}

function CallData2() {
    $("#LogTab").val("2");
    $.ajax({
        url: "menus/it/ajax/ajaxlog_it.php?a=CallData2",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                var Tbody = "";
                for(var r = 1; r <= inval['Row']; r++) {
                    Tbody +="<tr>"+
                                "<td class='text-center'>"+r+"</td>"+
                                "<td class='text-center'>"+inval['Data']['LogNum'][r]+"</td>"+
                                "<td>"+inval['Data']['Category'][r]+"</td>"+
                                "<td>"+inval['Data']['LogTitle'][r]+"</td>"+
                                "<td>"+inval['Data']['Name'][r]+"</td>"+
                                "<td class='text-center'>"+inval['Data']['CompDate'][r]+"</td>"+
                                "<td class='text-center'>"+inval['Data']['StatusDoc'][r]+"</td>"+
                            "</tr>";
                }
                $("#Table2 tbody").html(Tbody);
            });
        }
    })
}

function ModalAddLog() { 
    $("#Category").val(""); $("#FormRemark").addClass("d-none"); // หมวดหมู่
    $("#CompMethod").val("");   // ช่องทางการแจ้ง
    $("#CompUser").val("");     // ผู้แจ้ง
    $("#CompDate").val("");     // วันที่แจ้ง
    $("#LogTitle").val("");     // หัวข้อที่แจ้ง
    $("#LogDetail").val("");    // รายละเอียดที่แจ้ง
    $("#LogSolution").val("");  // วิธีการแก้ปัญหา
    $("#UkeySolution").val(""); // ผู้แก้ปัญหา
    $("#DateSolution").val(""); // วันที่แก้ปัญหา
    $("#FileAttach").val("");   // ไฟล์แนบ
    $("#DeptCode").val("");     // แผนกผู้แจ้ง

    $("#ModalAddLog").modal("show"); 
}

function AddLog() {
    var Category     = $("#Category").val();     // หมวดหมู่
    var CompMethod   = $("#CompMethod").val();   // ช่องทางการแจ้ง
    var CompUser     = $("#CompUser").val();     // ผู้แจ้ง
    var CompDate     = $("#CompDate").val();     // วันที่แจ้ง
    var LogTitle     = $("#LogTitle").val();     // หัวข้อที่แจ้ง
    var LogDetail    = $("#LogDetail").val();    // รายละเอียดที่แจ้ง
    var DeptCode     = $("#DeptCode").val();    // แผนกผู้แจ้ง
    var DataForm = new FormData($("#DataForm")[0]);
    // console.log(DeptCode);
    if(Category != "" && CompMethod != "" && CompMethod != null && CompUser != "" && CompDate != "" && LogTitle != "" && LogDetail != "" && DeptCode != null) {
        if(Category == 11) {
            if($("#CategoryRemark").val() != "") { $Chk = "Y"; }else{ $Chk = "N"; }
        }else{
            $Chk = "Y";
        }
        if($Chk == "Y") {
            $.ajax({
                url: "menus/it/ajax/ajaxlog_it.php?a=AddLog",
                type: 'POST',
                dataType: 'text',
                cache: false,
                processData: false,
                contentType: false,
                data: DataForm,
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 65px;'></i>");
                        $("#alert_body").html("บันทึกข้อมูลสำเร็จ");
                        $("#alert_modal").modal("show");
                        $("#ModalAddLog").modal("hide"); 
                        CallData();
                    });
                } 
            })
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 65px;'></i>");
            $("#alert_body").html("หมวดหมู่อื่น ๆ กรุณาโปรดระบุ");
            $("#alert_modal").modal("show");
            $("#CategoryRemark").addClass("is-invalid");
        }
    }else{
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 65px;'></i>");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal("show");
    }
}

function ChkCategory() {
    if($("#Category").val() == 11) {
        $("#FormRemark").removeClass("d-none");
        $("#CategoryRemark").val("");
        $("#CategoryRemark").focus();
    }else{
        $("#FormRemark").addClass("d-none");
    }
}
$("#CategoryRemark").focus(function() {
    $("#CategoryRemark").removeClass("is-invalid");
})

function AppLog() {
    var LogEntry = $("#LogEntry").val();
    $.ajax({
        url: "menus/it/ajax/ajaxlog_it.php?a=AppLog",
        type: "POST",
        data: { LogEntry : LogEntry, },
        success: function(result) {
            $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 65px;'></i>");
            $("#alert_body").html("บันทึกข้อมูลสำเร็จ");
            $("#alert_modal").modal("show");
            $("#ModalViewLog").modal("hide"); 
            CallData2();
        }
    })
}
</script> 
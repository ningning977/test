<style type="text/css">
    .form-control-custom {
        border: 1px solid #dce7f1 !important;
        width: 90%;
        background: rgba(255, 255, 255, 0.01);
    }

    .form-control-custom:disabled {
        border: rgba(255, 255, 255, 0.01) !important;
        width: 90%;
        background: rgba(255, 255, 255, 0.01);
    }

    .form-control-custom:focus {
        border: 1px solid #dce7f1 !important;
        outline: none;
    }

    .form-select-custom {
        border: 1px solid #dce7f1 !important;
        border-radius: 3px;
    }

    .form-select-custom:disabled {
        border: rgba(255, 255, 255, 0.01) !important;
    }

    .form-select-custom:focus {
        border: 1px solid #dce7f1 !important;
        outline: none;
    }

</style>

<style>
    .bowl{
	position: relative;
	width: 150px;
	height: 150px;
	background: rgba(255, 255, 255, 0.1);
	border-radius: 50%;
	border: 8px solid transparent;
	animation: animate 5s linear infinite;
        transform-origin: bottom center;
        animation-play-state: run;
    }
    @keyframes animate{
        0%{
            transform: rotate(0deg);
        }
        25%{
            transform: rotate(22deg);
        }
            50%{
            transform: rotate(0deg);
        }
            75%{
            transform: rotate(-22deg);
        }
        100%{
            filter: hue-rotate(360deg);
        }
    }
    .bowl::before{
        content: "";
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 40%;
        height: 30px;
        border: 15px solid #444;
        border-radius: 50%;
        box-shadow: 0 10px #222;

    }
    .bowl::after{
        content: "";
        position: absolute;
        top: 35%;
        left: 50%;
        transform: translate(-50%,-50%);
        width: 150px;
        height: 60px;
        background: rgba(255, 255, 255, 0.05);
        transform-origin: center;
        animation: animatebowlshadow 5s linear infinite;
        border-radius: 50%;
        animation-play-state: running;

    }


    @keyframes animatebowlshadow{
        0%{
            left: 50%;
            width: 150px;
            height: 60px;
        }
    25%{
            left: 55%;
            width: 140px;
            height: 65px;
        }50%{
            left: 50%;
            width: 150px;
            height: 60px;
        }
        75%{
            left: 45%;
            width: 140px;
            height: 65px;
        }
        100%{


        }
    }


    .liquid{
        position: absolute;
        top: 50%;
        left: 5px;
        right: 5px;
        bottom: 5px;
        background: #1ae907;
        border-bottom-left-radius: 150px;
        border-bottom-right-radius: 150px;
        filter: drop-shadow(0 0 80px #1ae907);
        transform-origin: top center;
        animation: animateliquid 5s linear infinite;
        animation-play-state: running;
    }

    @keyframes animateliquid{
        0%{
            transform: rotate(0deg);
        }
        25%{
            transform: rotate(-22deg);
        }
        30%{
            transform: rotate(-23deg);
        }
        50%{
            transform: rotate(0deg);
        }
        75%{
            transform: rotate(22deg);
        }
            80%{
            transform: rotate(23deg);
        }
        100%{
            transform: rotate(0deg);
        }


    }
    .liquid::before{
        content: "";
        position: absolute;
        top: -10px;
        width: 100%;
        height: 20px;
        background: #15be05;
        border-radius: 50%;
        filter:drop-shadow(0 0 80px #15be05) ;
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
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="ItemCode">เลือกสินค้า</label>
                            <select class='form-control form-control-sm' name='ItemCode' id='ItemCode' data-live-search="true" onchange='CallData();'>
                                <option value="" selected disabled>เลือกสินค้า</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-outline-info w-100' onclick='Print();'><i class="fas fa-print fa-fw"></i> Print</button>
                        </div>
                    </div>
                    <div class="col-lg-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Excel();'><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="row pt-1">
                    <div class="col-lg-auto">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary active" id="PD-tab" data-bs-toggle="tab" data-bs-target="#PD" type="button" role="tab" aria-controls="PD" aria-selected="false"><i class="fab fa-product-hunt"></i> PD</button>
                                <button class="nav-link text-primary" id="MK-tab" data-bs-toggle="tab" data-bs-target="#MK" type="button" role="tab" aria-controls="MK" aria-selected="false"><i class="fas fa-poll"></i> MK</button>
                                <button class="nav-link text-primary" id="PU-tab" data-bs-toggle="tab" data-bs-target="#PU" type="button" role="tab" aria-controls="PU" aria-selected="false"><i class="fas fa-money-bill-wave"></i> PU</button>
                                <button class="nav-link text-primary" id="SD-tab" data-bs-toggle="tab" data-bs-target="#SD" type="button" role="tab" aria-controls="SD" aria-selected="false"><i class="fas fa-hands-helping"></i> ฝ่ายขาย</button>
                                <button class="nav-link text-primary" id="NP-tab" data-bs-toggle="tab" data-bs-target="#NP" type="button" role="tab" aria-controls="NP" aria-selected="false"><i class="fas fa-shopping-basket"></i> สินค้าใหม่</button>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col-lg">
                        <div class="tab-content" id="nav-tabContent">
                            <!--#### PD ### -->
                            <?php 
                                $chk_edit_tab1 = "";
                                switch($_SESSION['DeptCode']) {
                                    case "DP002":
                                    case "DP003":
                                    case "DP004":
                                    case "DP010": $chk_edit_tab1 = "Y"; break;
                                }
                            ?>
                            <div class="tab-pane fade show active" id="PD" role="tabpanel" aria-labelledby="PD-tab">
                                <div class="row">
                                    <div class="col-lg-auto">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless'>
                                                <thead>
                                                    <tr>
                                                        <th id='ID_SKU'></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col-lg-9">
                                    <!-- Header. ข้อมูลสินค้า -->
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr> 
                                                        <td class='text-light' colspan='4'>
                                                            <div class="d-flex justify-content-between ">
                                                                <div>
                                                                    1. ข้อมูลสินค้า
                                                                </div>
                                                                <a href='javascript:void(0);' class='text-light EditTab0' status-tab0='Edit' onclick="EditSKU(0);"></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr> 
                                                        <th width='15%' class='ps-4'>ประเภท (กลุ่มหลัก) <span style='color: #1E88E5;'>*</span></th>
                                                        <td width='35%' id='U_Group1'></td>
                                                        <th width='15%'>ประเภท (กลุ่มรอง) <span style='color: #1E88E5;'>*</span></th>
                                                        <td width='35%' id='U_Group2'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>ชื่อภาษาไทย <span style='color: #1E88E5;'>*</span></th>
                                                        <td id='ItemName'></td>
                                                        <th>ชื่อภาษาอังกฤษ <span style='color: #1E88E5;'>*</span></th>
                                                        <td id='ItemNameEng'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>รหัสสินค้า</th>
                                                        <td id='vItemCode'></td>
                                                        <th>Barcode</th>
                                                        <td id='Barcode'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>สถานะสินค้า <span style='color: #1E88E5;'>*</span></th>
                                                        <td id='ProductStatus'></td>
                                                        <th>รหัสทีมขาย <span style='color: #9A1118;'>*</span></th>
                                                        <td><input type='text' class='form-control-custom ps-0' name='TeamCode' id='TeamCode' disabled></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>รุ่น (Model) <span style='color: #1E88E5;'>*</span></th>
                                                        <td id='Model'></td>
                                                        <th>ยี่ห้อสินค้า <span style='color: #1E88E5;'>*</span></th>
                                                        <td id='Brand'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>สีตัวสินค้า <span style='color: #9A1118;'>*</span></th>
                                                        <td><input type='text' class='form-control-custom ps-0' name='ItemColor' id='ItemColor' disabled></td>
                                                        <th>สีของบรรจุภัณฑ์ <span style='color: #9A1118;'>*</span></th>
                                                        <td><input type='text' class='form-control-custom ps-0' name='BoxColor' id='BoxColor' disabled></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>ทำจากวัสดุ <span style='color: #9A1118;'>*</span></th>
                                                        <td><input type='text' class='form-control-custom ps-0' name='MadeOf' id='MadeOf' disabled></td>
                                                        <th>ประเทศผู้ผลิต <span style='color: #9A1118;'>*</span></th>
                                                        <td><input type='text' class='form-control-custom ps-0' name='ProCountry' id='ProCountry' disabled></td>
                                                    </tr>
                                                    <?php 
                                                    $chk_uClass = 'N';
                                                    switch($_SESSION['uClass']) {
                                                        case 0: 
                                                        case 2: 
                                                        case 3: 
                                                        case 4: 
                                                        case 5: 
                                                        case 13: 
                                                        case 14: 
                                                        case 15: 
                                                        case 16: 
                                                        case 17: 
                                                        case 18: 
                                                        case 34: $chk_uClass = 'Y'; break;
                                                    }
                                                    ?>
                                                    <?php 
                                                    if($chk_uClass == 'Y') {
                                                        echo "<tr> 
                                                            <th class='ps-4'>ผู้ผลิต <span style='color: #1E88E5;'>*</span> </th>
                                                            <td colspan='3' id='CardName'></td>
                                                        </tr>";
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td colspan='4' class='text-center'><span style='color: #1E88E5;font-weight:bold'>*</span> ต้องกรอกข้อมูลในระบบ SAP</td>    
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- 1. คุณสมบัติ -->
                                        <form id='FormDataTab1' enctype="multipart/form-data"></form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableTab1'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td colspan='4'>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    2. คุณสมบัติ <span class='text-danger'>*</span>
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light AddTab1' onclick="AddSKU(1);"></a>
                                                                    &nbsp;&nbsp;
                                                                    <a href='javascript:void(0);' class='text-light EditTab1' status-tab1='Edit' onclick="EditSKU(1);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr> 
                                                </thead>
                                                <tbody>
                                                    <tr> 
                                                        <td colspan='4' class='text-center'>ยังไม่มีข้อมูล</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    <!-- 2. รายละเอียดบรรจุภัณฑ์ -->
                                        <form id='FormDataTab2' enctype="multipart/form-data"></form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableTab2'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td colspan='4'>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    3. รายละเอียดบรรจุภัณฑ์
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light EditTab2' status-tab2='Edit' onclick="EditSKU(2);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr> 
                                                        <th width='15%' class='ps-4'>ขนาดสินค้า (ซม.)</th>
                                                        <td width='35%'></td>
                                                        <th width='15%'>น้ำหนักสินค้า</th>
                                                        <td width='35%'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th width='15%' class='ps-4'>ขนาดกล่อง 1 (ซม.)</th>
                                                        <td width='35%'></td>
                                                        <th width='15%'>ขนาดบรรจุ</th>
                                                        <td width='35%'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th width='15%' class='ps-4'>ขนาดกล่อง 2 (ซม.)</th>
                                                        <td width='35%'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th width='15%' class='ps-4'>น้ำหนักรวมสินค้า</th>
                                                        <td width='35%'></td>
                                                        <th width='15%'>บาร์โค้ดกล่อง</th>
                                                        <td width='35%'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th width='15%' class='ps-4'>ขนาดลัง (ซม.)</th>
                                                        <td width='35%'></td>
                                                        <th width='15%'>ขนาดบรรจุ</th>
                                                        <td width='35%'></td>
                                                    </tr>
                                                    <tr> 
                                                        <th width='15%' class='ps-4'>น้ำหนักลังรวมสินค้า</th>
                                                        <td width='35%'></td>
                                                        <th width='15%'>บาร์โค้ดลัง</th>
                                                        <td width='35%'></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    <!-- 3. อุปกรณ์ภายในกล่อง -->
                                        <form id='FormDataTab3' enctype="multipart/form-data"></form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableTab3'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    4. อุปกรณ์ภายในกล่อง <span class='text-danger'>*</span>
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light AddTab3' onclick="AddSKU(3);"></a>
                                                                    &nbsp;&nbsp;
                                                                    <a href='javascript:void(0);' class='text-light EditTab3' status-tab3='Edit' onclick="EditSKU(3);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr> 
                                                        <td class='text-center'>ยังไม่มีข้อมูล</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- 4. วิธีการใช้งาน -->
                                        <form id='FormDataTab4' enctype="multipart/form-data"></form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableTab4'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    5. วิธีการใช้งาน <span class='text-danger'>*</span>
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light AddTab4' onclick="AddSKU(4);"></a>
                                                                    &nbsp;&nbsp;
                                                                    <a href='javascript:void(0);' class='text-light EditTab4' status-tab4='Edit' onclick="EditSKU(4);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr> 
                                                        <td class='text-center'>ยังไม่มีข้อมูล</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- 5. จุดเด่น จุดขาย ของสินค้า -->
                                        <form id='FormDataTab5' enctype="multipart/form-data"></form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableTab5'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                6. จุดเด่น จุดขาย ของสินค้า <span class='text-danger'>*</span>
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light AddTab5' onclick="AddSKU(5);"></a>
                                                                    &nbsp;&nbsp;
                                                                    <a href='javascript:void(0);' class='text-light EditTab5' status-tab5='Edit' onclick="EditSKU(5);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr> 
                                                        <td class='text-center'>ยังไม่มีข้อมูล</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    <!-- 6. การรับประกัน -->
                                        <form id='FormDataTab6' enctype="multipart/form-data"></form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableTab6'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td colspan='2'>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                7. การรับประกัน
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light EditTab6' status-Tab6='Edit' onclick="EditSKU(6);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th class='ps-4'>ระยะเวลารับประกัน</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th class='ps-4'>ประเภทการรับประกัน</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th class='ps-4'>เงื่อนไขการรับประกัน</th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- 7. ข้อมูล สคบ. -->
                                        <form id='FormDataTab7' enctype="multipart/form-data"></form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableTab7'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td colspan='2'>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                8. ข้อมูล สคบ <span class='text-danger'>*</span>
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light EditTab7' status-Tab7='Edit' onclick="EditSKU(7);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr> 
                                                        <th class='ps-4'>ชื่อสินค้า</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>ผลิตจากประเทศ</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4 align-top'>จัดจำหน่ายโดย</th>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>บรรจุ</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>วิธีการใช้</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>คำเตือน</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>ราคา</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr> 
                                                        <th class='ps-4'>วันที่ผลิต</th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- 8. ข้อควรระวัง -->
                                        <form id='FormDataTab8' enctype="multipart/form-data"></form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableTab8'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td colspan='2'>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                9. ข้อควรระวัง <span class='text-danger'>*</span>
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light EditTab8' status-Tab8='Edit' onclick="EditSKU(8);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th class='ps-4'>คำเตือน</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th class='ps-4'>ข้อแนะนำในการใช้งาน</th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- หมายเหตุ -->
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableRemark'>
                                                <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                    <tr class='text-light'> 
                                                        <td>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                หมายเหตุ
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light EditTab99' status-tab99='Edit' onclick="EditSKU(99);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr> 
                                                        <td class='ps-4' id='Remark'>&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg">
                                        <form id='FormDataIMG' enctype="multipart/form-data">
                                            <input type="file" class="form-control form-control-sm d-none" name="FileIMG[]" id="FileIMG" accept=".jpg,.png,.gif" onchange="AddIMG(0);">
                                        </form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);'>
                                                <tbody>
                                                    <tr>
                                                        <td class='text-center p-4 pb-0'>
                                                            <div class='img1'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>รูปสินค้า <a href='#javascript:void(0);' class='img' onclick='AddIMG(1);'></a></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='text-center p-4 pb-0'>
                                                            <div class='img2'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>รูปบรรจุภัณฑ์ <a href='#javascript:void(0);' class='img' onclick='AddIMG(2);'></a></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='text-center p-4 pb-0'>
                                                            <div class='img3'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>อุปกรณ์ภายในกล่อง <a href='#javascript:void(0);' class='img' onclick='AddIMG(3);'></a></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='text-center p-4 pb-0'>
                                                            <div class='img4'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>รูปลังสินค้า <a href='#javascript:void(0);' class='img' onclick='AddIMG(4);'></a></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='text-center p-4'>
                                                            <div class='img5'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>รูป Barcode <a href='#javascript:void(0);' class='img' onclick='AddIMG(5);'></a></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--#### MK ### -->
                            <?php 
                                $chk_edit_tab2 = "";
                                switch($_SESSION['DeptCode']) {
                                    case "DP002":
                                    case "DP003": $chk_edit_tab2 = "Y"; break;
                                }
                            ?>
                            <div class="tab-pane fade " id="MK" role="tabpanel" aria-labelledby="MK-tab">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <!-- 1. ข้อมูลสินค้า -->
                                            <div class="table-responsive">
                                                <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableMK_1'>
                                                    <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                        <tr> 
                                                            <td class='text-light' colspan='4'>1. ข้อมูลสินค้า</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class='text-center'>ยังไม่เลือกสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <!-- 2. ราคาสินค้า -->
                                            <div class="table-responsive">
                                                <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableMK_2'>
                                                    <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                        <td class='text-light' colspan='6'>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    2. ราคาสินค้า
                                                                </div>
                                                                <div>
                                                                    ประเภทราคา&nbsp;
                                                                    <select class='form-select-custom' name="PriceType" id="PriceType" onchange='SelectPriceType();'></select>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class='text-center'>ยังไม่เลือกสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <!-- 3. โปรโมชั่น -->
                                            <form id='FormDataTab9' enctype="multipart/form-data"></form>
                                            <div class="table-responsive">
                                                <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableMK_3'>
                                                    <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                        <td class='text-light' colspan='6'>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    3. โปรโมชั่น
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light EditTab9' status-tab9='Edit' onclick="EditSKU_MK(9);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class='text-center'>ยังไม่เลือกสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <!-- 4. ช่องทางการขายสินค้า -->
                                            <div class="table-responsive">
                                                <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableMK_4'>
                                                    <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                        <td class='text-light' colspan='6'>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    4. ช่องทางการขายสินค้า
                                                                </div>
                                                                <div>
                                                                    <a href='javascript:void(0);' class='text-light EditTab10' status-tab10='Edit' onclick="EditSKU_MK(10);"></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class='text-center'>ยังไม่เลือกสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <!-- 5. VDO Utility -->
                                            <div class="table-responsive">
                                                <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);' id='TableMK_5'>
                                                    <thead style='background-color: rgba(136, 0, 0, 0.8);'>
                                                        <tr class='text-light'> 
                                                            <td colspan='2'>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        5. VDO Utility
                                                                    </div>
                                                                    <div>
                                                                        <a href='javascript:void(0);' class='text-light AddTab11' onclick="AddSKU(11);"></a>
                                                                        &nbsp;&nbsp;
                                                                        <a href='javascript:void(0);' class='text-light EditTab11' status-tab11='Edit' onclick="EditSKU_MK(11);"></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class='text-center'>ยังไม่เลือกสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                    </div>

                                    <div class="col-lg">
                                        <form id='FormDataIMG_MK' enctype="multipart/form-data">
                                            <input type="file" class="form-control form-control-sm d-none" name="FileIMG_MK[]" id="FileIMG_MK" accept=".jpg,.png,.gif" onchange="AddIMG_MK(0);">
                                        </form>
                                        <div class="table-responsive">
                                            <table class='table table-sm table-borderless rounded rounded-3 overflow-hidden' style='font-size: 13px; background-color: rgba(155, 0, 0, 0.04);'>
                                                <tbody>
                                                    <tr>
                                                        <td class='text-center p-4 pb-0'>
                                                            <div class='img6'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>รูปสินค้าตัวจริง อย่างน้อย 6 รูป <a href='#javascript:void(0);' class='img_mk' onclick='AddIMG_MK(6);'></a></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='text-center p-4 pb-0'>
                                                            <div class='img7'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>รูปแพ็คเกจตัวจริง <a href='#javascript:void(0);' class='img_mk' onclick='AddIMG_MK(7);'></a></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='text-center p-4 pb-0'>
                                                            <div class='img8'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>รูปอุปกรณ์ภายในกล่องตัวจริง <a href='#javascript:void(0);' class='img_mk' onclick='AddIMG_MK(8);'></a></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='text-center p-4 pb-0'>
                                                            <div class='img9'>
                                                                <img src="../../image/products/no-image.jpg" style='width: 80%'/>
                                                            </div>
                                                            <div class='fw-bolder pt-1'>ใบโปร/ใบขาย <a href='#javascript:void(0);' class='img_mk' onclick='AddIMG_MK(9);'></a></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--#### PU ### -->
                            <div class="tab-pane fade " id="PU" role="tabpanel" aria-labelledby="PU-tab">
                                <div class="row pt-5">
                                    <div class="col-5"></div>
                                    <div class="col-2">
                                        <div class="bowl">
                                            <div class="liquid"></div>
                                        </div>
                                        <div>&nbsp;</div>
                                        <h5>รอสรุปผลเพื่อพัฒนา <i class="fas fa-circle-notch fa-spin"></i></h5>
                                    </div>
                                    <div class="col-5"></div>
                                </div>
                            </div>

                            <!--#### ฝ่ายขาย ### -->
                            <div class="tab-pane fade " id="SD" role="tabpanel" aria-labelledby="SD-tab">
                                <div class="row pt-5">
                                    <div class="col-5"></div>
                                    <div class="col-2">
                                        <div class="bowl">
                                            <div class="liquid"></div>
                                        </div>
                                        <div>&nbsp;</div>
                                        <h5>รอสรุปผลเพื่อพัฒนา <i class="fas fa-circle-notch fa-spin"></i></h5>
                                    </div>
                                    <div class="col-5"></div>
                                </div>
                            </div>

                            <!--#### สินค้าใหม่ ### -->
                            <div class="tab-pane fade " id="NP" role="tabpanel" aria-labelledby="NP-tab">
                                <div class="row pt-5">
                                    <div class="col-5"></div>
                                    <div class="col-2">
                                        <div class="bowl">
                                            <div class="liquid"></div>
                                        </div>
                                        <div>&nbsp;</div>
                                        <h5>รอสรุปผลเพื่อพัฒนา <i class="fas fa-circle-notch fa-spin"></i></h5>
                                    </div>
                                    <div class="col-5"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalAddSKU" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fas fa-plus-square fa-fw' style='font-size: 20px;'></i>&nbsp;&nbsp;&nbsp;<span id='AddSKU-header'></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id='AddSKU-body'>
                
            </div>
            <div class="modal-footer" id='AddSKU-footer'>
                <button type='button' class='btn btn-primary btn-sm' onclick='SaveAddSKU();'>บันทึก</button>
                <button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button>
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
function calcHeight(ID) {
    let value = $("#Detail_"+ID).val();
    let numberOfLineBreaks = 0;
    let newHeight = 0;
    let match = /\r|\n/.exec(value);
    if (match) {
        numberOfLineBreaks = (value.match(/\n/g)||[]).length;
        newHeight = 20 + numberOfLineBreaks * 20 + 12 + 2;
    }else{
        newHeight = Math.ceil(value.length/150) * 25;
    }
    $("#Detail_"+ID).attr("style", "height: "+newHeight+"px");
}

$(document).ready(function(){
    sessionStorage.setItem('tmpTab',JSON.stringify("PD"));
    GetOITM();
});

$("#PD-tab, #MK-tab").on("click", function() {
    const Tab = $(this).attr("aria-controls");
    const ItemCode = $("#ItemCode").val();
    sessionStorage.setItem('tmpTab',JSON.stringify(Tab));
    if(ItemCode != null) {
        switch(Tab) {
            case 'MK': CallDataMK(); break;
        }
    }
})

function GetOITM() {
    $.ajax({
        url: "../json/OITM.json",
        cache: false,
        success: function(result) {
            var filt_data = 
                result.
                    filter(x => x.ItemStatus == "A").
                    filter(x => x.ItemCode.substr(0,3) != "00-").
                    sort(function(key, inval) {
                        return key.ItemCode.localeCompare(inval.ItemCode);
                    });
    
            var opt = "";
    
            $.each(filt_data, function(key, inval) {
                opt += "<option value='"+inval.ItemCode+"'>"+inval.ItemCode+" | ["+inval.ProductStatus+"] - "+inval.ItemName+" ["+inval.BarCode+"]</option>";
            });
            $("#ItemCode").append(opt).selectpicker();
    
            <?php if(isset($_GET['ItemCode'])) { ?>
                setTimeout(function(){
                    $("#ItemCode").selectpicker('destroy').val("<?php echo $_GET['ItemCode']; ?>").change().selectpicker();
                }, 2000);
            <?php } ?>
        }
    });
}

function CallData() {
    const ItemCode = $("#ItemCode").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/general/ajax/ajaxsku_book.php?a=CallData",
        type: "POST",
        data: { ItemCode : ItemCode, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#ID_SKU").html("เลขที่ SKU : "+inval['ID_SKU']+"  วันที่ : "+inval['DATE_SKU']+"");

                // Header. ข้อมูลสินค้า
                    $("#U_Group1").html(inval['U_Group1']);
                    $("#U_Group2").html(inval['U_Group2']);
                    <?php if($chk_uClass == 'Y') { ?>
                        $("#CardName").html(inval['CardName']);
                    <?php } ?>
                    $("#ItemName").html(inval['ItemName']);
                    $("#vItemCode").html(inval['ItemCode']);
                    $("#Barcode").html(inval['CodeBars']);
                    $("#ProductStatus").html(inval['ProductStatus']);
                    $("#TeamCode").val(inval['TeamCode']);
                    $("#Model").html(inval['Model']);
                    $("#Brand").html(inval['Brand']);
                    $("#ItemNameEng").html(inval['ItemNameEng']);
                    $("#ItemColor").val(inval['ItemColor']);
                    $("#BoxColor").val(inval['BoxColor']);
                    $("#MadeOf").val(inval['MadeOf']);
                    $("#ProCountry").val(inval['ProCountry']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        $(".EditTab0").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                        $(".EditTab0").attr("status-tab0", "Edit");
                    <?php } ?>
                    $("#ItemColor").prop("disabled", true);
                    $("#BoxColor").prop("disabled", true);
                    $("#MadeOf").prop("disabled", true);
                    $("#ProCountry").prop("disabled", true);
                    $("#TeamCode").prop("disabled", true);

                // 1. คุณสมบัติ
                    $("#TableTab1 tbody").html(inval['DataType1']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        $(".AddTab1").html("<i class='fas fa-plus-square fa-fw'></i>เพิ่ม");
                        if(inval['ChkDataType1'] == 'Y') {
                            $(".EditTab1").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                            $(".EditTab1").attr("status-tab1", "Edit");
                        }else{
                            $(".EditTab1").html("");
                        }
                    <?php } ?>
                    sessionStorage.setItem('tmpID_Type1',JSON.stringify(inval['tmpID_Type1']));

                // 2. รายละเอียดบรรจุภัณฑ์
                    $("#TableTab2 tbody").html(inval['DataType2']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        $(".EditTab2").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                        $(".EditTab2").attr("status-tab2", "Edit");
                    <?php } ?>
                    sessionStorage.setItem('tmpID_Type2',JSON.stringify(inval['tmpID_Type2']));

                // 3. อุปกรณ์ภายในกล่อง
                    $("#TableTab3 tbody").html(inval['DataType3']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        if(inval['ChkDataType3'] == 'Y') {
                            $(".AddTab3").html("");
                            $(".EditTab3").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                            $(".EditTab3").attr("status-tab3", "Edit");
                            calcHeight(inval['tmpID_Type3']);
                        }else{
                            $(".EditTab3").html("");
                            $(".AddTab3").html("<i class='fas fa-plus-square fa-fw'></i>เพิ่ม");
                        }
                    <?php } ?>
                    sessionStorage.setItem('tmpID_Type3',JSON.stringify(inval['tmpID_Type3']));
                    sessionStorage.setItem('tmpBar1',JSON.stringify(inval['tmpBar1']));
                    sessionStorage.setItem('tmpBar2',JSON.stringify(inval['tmpBar2']));
                    sessionStorage.setItem('tmpBox1',JSON.stringify(inval['tmpBox1']));

                // 4. วิธีการใช้งาน
                    $("#TableTab4 tbody").html(inval['DataType4']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        if(inval['ChkDataType4'] == 'Y') {
                            $(".AddTab4").html("");
                            $(".EditTab4").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                            $(".EditTab4").attr("status-tab4", "Edit");
                            calcHeight(inval['tmpID_Type4']);
                        }else{
                            $(".EditTab4").html("");
                            $(".AddTab4").html("<i class='fas fa-plus-square fa-fw'></i>เพิ่ม");
                        }
                    <?php } ?>
                    sessionStorage.setItem('tmpID_Type4',JSON.stringify(inval['tmpID_Type4']));

                // 5. จุดเด่น จุดขาย ของสินค้า
                    $("#TableTab5 tbody").html(inval['DataType5']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        if(inval['ChkDataType5'] == 'Y') {
                            $(".AddTab5").html("");
                            $(".EditTab5").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                            $(".EditTab5").attr("status-tab5", "Edit");
                            calcHeight(inval['tmpID_Type5']);
                        }else{
                            $(".EditTab5").html("");
                            $(".AddTab5").html("<i class='fas fa-plus-square fa-fw'></i>เพิ่ม");
                        }
                    <?php } ?>
                    sessionStorage.setItem('tmpID_Type5',JSON.stringify(inval['tmpID_Type5']));

                // 6. การรับประกัน
                    $("#TableTab6 tbody").html(inval['DataType6']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        $(".EditTab6").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                        $(".EditTab6").attr("status-Tab6", "Edit");
                    <?php } ?>
                    sessionStorage.setItem('tmpID_Type6',JSON.stringify(inval['tmpID_Type6']));

                // 7. ข้อมูล สคบ
                    $("#TableTab7 tbody").html(inval['DataType7']);
                    $(".Counttry_Type7").val(inval['Counttry_Type7']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        $(".EditTab7").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                        $(".EditTab7").attr("status-Tab7", "Edit");
                    <?php } ?>
                    sessionStorage.setItem('tmpID_Type7',JSON.stringify(inval['tmpID_Type7']));

                // 8. ข้อควรระวัง
                    $("#TableTab8 tbody").html(inval['DataType8']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        $(".EditTab8").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                        $(".EditTab8").attr("status-Tab6", "Edit");
                    <?php } ?>
                    sessionStorage.setItem('tmpID_Type8',JSON.stringify(inval['tmpID_Type8']));

                // หมายเหตุ
                    $("#TableRemark tbody").html(inval['Remark']);
                    <?php if($chk_edit_tab1 == 'Y') { ?>
                        $(".EditTab99").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                        $(".EditTab99").attr("status-tab99", "Edit");
                    <?php } ?>

                // IMG
                GetIMG();

                CallDataMK();
            });
            $(".overlay").hide();
        }
    })
}

function EditSKU(Tab) {
    const ItemCode = $("#ItemCode").val();
    switch (Tab) {
        case 0: // Header. ข้อมูลสินค้า
            if($(".EditTab0").attr("status-tab0") == 'Edit') {
                $(".EditTab0").html("<i class='fas fa-save fa-fw'></i>บันทึก");
                $(".EditTab0").attr("status-tab0", "Save");
                $("#ItemColor").prop("disabled", false);
                $("#BoxColor").prop("disabled", false);
                $("#MadeOf").prop("disabled", false);
                $("#ProCountry").prop("disabled", false);
                $("#TeamCode").prop("disabled", false);
            }else{
                const ItemColor   = $("#ItemColor").val();
                const BoxColor    = $("#BoxColor").val();
                const MadeOf      = $("#MadeOf").val();
                const ProCountry  = $("#ProCountry").val();
                const TeamCode  = $("#TeamCode").val();
                console.log(TeamCode);
                if(TeamCode != "" && BoxColor != "" && MadeOf != "" && ProCountry != "" && TeamCode != "") {
                    $(".overlay").show();
                    $.ajax({
                        url: "menus/general/ajax/ajaxsku_book.php?a=EditSKU",
                        type: "POST",
                        data: { Tab         : Tab,
                                ItemColor   : ItemColor,
                                BoxColor    : BoxColor,
                                MadeOf      : MadeOf,
                                ProCountry  : ProCountry,
                                ItemCode    : ItemCode,
                                TeamCode    : TeamCode, },
                        success: function(result) {
                            let obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) {
                                $(".EditTab0").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                                $(".EditTab0").attr("status-tab0", "Edit");
                                $("#ItemColor").prop("disabled", true);
                                $("#BoxColor").prop("disabled", true);
                                $("#MadeOf").prop("disabled", true);
                                $("#ProCountry").prop("disabled", true);
                                $("#TeamCode").prop("disabled", true);
                            });
                            $(".overlay").hide();
                        }
                    })
                }else{
                    $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
                    $("#alert_body").html("กรุณากรอกข้อมูลที่มีเครื่องหมาย (<span style='color: #9A1118;'>*</span>) ให้ครบ");
                    $("#alert_modal").modal("show");
                }
            }
        break;
        case 99:
            if($(".EditTab99").attr("status-tab99") == 'Edit') {
                $(".EditTab99").html("<i class='fas fa-save fa-fw'></i>บันทึก");
                $(".EditTab99").attr("status-tab99", "Save");
                $("#Remark").prop("disabled", false);
            }else{
                const Remark = $("#Remark").val();
                $(".overlay").show();
                $.ajax({
                    url: "menus/general/ajax/ajaxsku_book.php?a=EditSKU",
                    type: "POST",
                    data: { Tab         : Tab,
                            Remark  : Remark,
                            ItemCode    : ItemCode, },
                    success: function(result) {
                        let obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $(".EditTab99").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                            $(".EditTab99").attr("status-tab99", "Edit");
                            $("#Remark").prop("disabled", true);
                        });
                        $(".overlay").hide();
                    }
                })
            }
        break;
    }

    let IDTab = [ 
        '1. คุณสมบัติ', '2. รายละเอียดบรรจุภัณฑ์', '3. อุปกรณ์ภายในกล่อง', '4. วิธีการใช้งาน', '5. จุดเด่น จุดขาย ของสินค้า', 
        '6. การรับประกัน', '7. ข้อมูล สคบ', '8. ข้อควรระวัง'
    ];
    for(let T = 1; T <= IDTab.length; T++) {
        if(Tab == T) {
            if($(".EditTab"+T).attr("status-tab"+T) == 'Edit') {
                $(".EditTab"+T).html("<i class='fas fa-save fa-fw'></i>บันทึก");
                $(".EditTab"+T).attr("status-tab"+T, "Save");
                $(".DETAIL_Type"+T).prop("disabled", false);
            }else{
                let FormDataTab = new FormData($("#FormDataTab"+T)[0]);
                FormDataTab.append('tmpID',JSON.parse(sessionStorage.getItem('tmpID_Type'+T)));
                FormDataTab.append('Tab',Tab);
                FormDataTab.append('ItemCode',ItemCode);
                let Input =  JSON.parse(sessionStorage.getItem('tmpID_Type'+T)).split(',');
                for(let i = 0; i < Input.length; i++) {
                    if(typeof($("#Detail_"+Input[i]+"_A1").val()) != "undefined") {
                        FormDataTab.append("Detail_"+Input[i]+"_A1",$("#Detail_"+Input[i]+"_A1").val());
                        FormDataTab.append("Detail_"+Input[i]+"_B1",$("#Detail_"+Input[i]+"_B1").val());
                        FormDataTab.append("Detail_"+Input[i]+"_C1",$("#Detail_"+Input[i]+"_C1").val());
                    }else if(typeof($("#Detail_"+Input[i]+"_A2").val()) != "undefined") {
                        FormDataTab.append("Detail_"+Input[i]+"_A2",$("#Detail_"+Input[i]+"_A2").val());
                        FormDataTab.append("Detail_"+Input[i]+"_B2",$("#Detail_"+Input[i]+"_B2").val());
                        FormDataTab.append("Detail_"+Input[i]+"_C2",$("#Detail_"+Input[i]+"_C2").val());
                    }else if(typeof($("#Detail_"+Input[i]+"_A3").val()) != "undefined") {
                        FormDataTab.append("Detail_"+Input[i]+"_A3",$("#Detail_"+Input[i]+"_A3").val());
                        FormDataTab.append("Detail_"+Input[i]+"_B3",$("#Detail_"+Input[i]+"_B3").val());
                        FormDataTab.append("Detail_"+Input[i]+"_C3",$("#Detail_"+Input[i]+"_C3").val());
                    }else if(typeof($("#Remark_"+Input[i]).val()) != "undefined"){
                        FormDataTab.append("Remark_"+Input[i],$("#Remark_"+Input[i]).val());
                        FormDataTab.append("Detail_"+Input[i],$("#Detail_"+Input[i]).val());
                    }else if(typeof($("#Detail_"+Input[i]+"_A2x").val()) != "undefined") {
                        FormDataTab.append("Detail_"+Input[i]+"_A2x",$("#Detail_"+Input[i]+"_A2x").val());
                        FormDataTab.append("Detail_"+Input[i]+"_B2x",$("#Detail_"+Input[i]+"_B2x").val());
                        FormDataTab.append("Detail_"+Input[i]+"_C2x",$("#Detail_"+Input[i]+"_C2x").val());
                    }else{
                        if(typeof($("#Header_"+Input[i]).val()) != "undefined") {
                            FormDataTab.append("Header_"+Input[i],$("#Header_"+Input[i]).val());
                        }
                        FormDataTab.append("Detail_"+Input[i],$("#Detail_"+Input[i]).val());
                    }
                }
                if(T == 7) {
                    if(typeof($("#Country_ID").val()) != "undefined") {
                        let CID = $("#Country_ID").val();
                        FormDataTab.append("Remark_"+CID,$("#Detail_"+CID+" option:selected").text());
                    }
                }

                $(".overlay").show();
                $.ajax({
                    url: "menus/general/ajax/ajaxsku_book.php?a=EditSKU",
                    type: 'POST',
                    dataType: 'text',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: FormDataTab,
                    success: function(result) {
                        let obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            if(inval['Error'] == 0) {
                                $(".EditTab"+T).html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                                $(".EditTab"+T).attr("status-tab"+T, "Edit");
                                $(".DETAIL_Type"+T).prop("disabled", true);
                                CallData();
                            }else{
                                $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
                                $("#alert_body").html("กรุณากรอกข้อมูลที่มีเครื่องหมาย (<span style='color: #9A1118;'>*</span>) ให้ครบ");
                                $("#alert_modal").modal("show");
                            }
                            
                        });
                        $(".overlay").hide();
                    }
                });
            }
        }
    }
    
}

function AddSKU(Tab) {
    sessionStorage.setItem('temTab',JSON.stringify(Tab));
    let body = "";
    switch (Tab) {
        case 1:
            $("#AddSKU-header").html("เพิ่มคุณสมบัติ");
            body += 
            "<div class='row'>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Header'>หัวข้อ</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Header' id='Header'>"+
                    "</div>"+
                "</div>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Detail'>รายละเอียด</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Detail' id='Detail'>"+
                    "</div>"+
                "</div>"+
            "</div>";
        break;
        case 3:
            $("#AddSKU-header").html("เพิ่มอุปกรณ์ภายในกล่อง");
            body += 
            "<div class='row'>"+
                "<div class='col-2'>"+
                "</div>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Detail'>ระบุชื่ออุปกรณ์</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Detail' id='Detail'>"+
                    "</div>"+
                "</div>"+
                "<div class='col-2'>"+
                    "<input type='hidden' class='form-control form-control-sm' name='Header' id='Header' value=''>"+
                "</div>"+
            "</div>";
        break;
        case 4:
            $("#AddSKU-header").html("เพิ่มวิธีการใช้งาน");
            body += 
            "<div class='row'>"+
                "<div class='col-2'>"+
                "</div>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Detail'>ระบุวิธีการใช้งาน</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Detail' id='Detail'>"+
                    "</div>"+
                "</div>"+
                "<div class='col-2'>"+
                    "<input type='hidden' class='form-control form-control-sm' name='Header' id='Header' value=''>"+
                "</div>"+
            "</div>";
        break;
        case 5:
            $("#AddSKU-header").html("เพิ่มจุดเด่น จุดขาย ของสินค้า");
            body += 
            "<div class='row'>"+
                "<div class='col-2'>"+
                "</div>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Detail'>ระบุจุดเด่น จุดขาย ของสินค้า</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Detail' id='Detail'>"+
                    "</div>"+
                "</div>"+
                "<div class='col-2'>"+
                    "<input type='hidden' class='form-control form-control-sm' name='Header' id='Header' value=''>"+
                "</div>"+
            "</div>";
        break;
        case 6:
            $("#AddSKU-header").html("เพิ่มอ้างอิง");
            body += 
            "<div class='row'>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Header'>หัวข้อ</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Header' id='Header'>"+
                    "</div>"+
                "</div>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Detail'>รายละเอียด</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Detail' id='Detail'>"+
                    "</div>"+
                "</div>"+
            "</div>";
        break;
        case 11:
            $("#AddSKU-header").html("เพิ่ม VDO Utility");
            body += 
            "<div class='row'>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Header'>หัวข้อ VDO</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Header' id='Header'>"+
                    "</div>"+
                "</div>"+
                "<div class='col'>"+
                    "<div class='form-group'>"+
                        "<label for='Detail'>Link VDO</label>"+
                        "<input type='text' class='form-control form-control-sm' name='Detail' id='Detail'>"+
                    "</div>"+
                "</div>"+
            "</div>";
        break;
    }
    $("#AddSKU-body").html(body);
    $("#ModalAddSKU").modal("show");
}

function SaveAddSKU() {
    const ItemCode = $("#ItemCode").val();
    const temTab   = JSON.parse(sessionStorage.getItem('temTab'));
    const Header   = $("#Header").val();
    const Detail   = $("#Detail").val();

    $.ajax({
        url: "menus/general/ajax/ajaxsku_book.php?a=SaveAddSKU",
        type: "POST",
        data: { ItemCode : ItemCode, temTab : temTab, Header : Header, Detail : Detail, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                if(inval['SUCCESS'] == 'Y') {
                    $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 60px;'></i>");
                    $("#alert_body").html("บันทึกข้อมูลสำเร็จ");
                    $("#alert_modal").modal("show");
                    $("#ModalAddSKU").modal("hide");
                    CallData();
                }else{
                    $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
                    $("#alert_body").html("บันทึกข้อมูลไม่สำเร็จ");
                    $("#alert_modal").modal("show");
                }
            });
        }
    })
}

function GetIMG() {
    for(let i = 1; i <= 9; i++) {
        $(".img"+i).html("");
    }
    const ItemCode = $("#ItemCode").val();
    $.ajax({
        url: "menus/general/ajax/ajaxsku_book.php?a=GetIMG",
        type: "POST",
        data: { ItemCode : ItemCode, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                for(let i = 1; i <= 9; i++) {
                    if(typeof(inval['Img'+i]) != "undefined") {
                        $(".img"+i).html(inval['Img'+i]);
                    }else{
                        $(".img"+i).html("<img src='../../image/products/no-image.jpg' style='width: 80%'/>");
                    }
                }
                <?php if($chk_edit_tab1 == 'Y') { ?>
                    $(".img").html("(<i class='fas fa-folder-plus'></i>)");
                <?php } ?>

                <?php if($chk_edit_tab2 == 'Y') { ?>
                    $(".img_mk").html("(<i class='fas fa-folder-plus'></i>)");
                <?php } ?>
            });
        }
    })
}
function CallBar(ItemCode,Pack,InputID) {
    var data1 =  $('#Detail_'+InputID).val();
    const Bar1 = JSON.parse(sessionStorage.getItem('tmpBar1'));
    const Bar2= JSON.parse(sessionStorage.getItem('tmpBar2'));
    const Box1= JSON.parse(sessionStorage.getItem('tmpBox1'));
    //console.log(ItemCode+" "+Pack+" "+data1);
    $.ajax({
        url: "menus/general/ajax/ajaxsku_book.php?a=GetBar",
        type: "POST",
        data: { ItemCode : ItemCode,
                Pack : Pack,
                qty : data1,    },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                console.log(inval['newBar']);
                if(InputID == Box1) {
                    $('#Detail_'+Bar1).val(inval['newBar']);
                }else{
                    $('#Detail_'+Bar2).val(inval['newBar']);
                }
            });
        }
    })
    

}

function AddIMG(Type) {
    switch (Type) {
        case 1:
        case 2:
        case 3:
        case 4:
        case 5: 
            $("#FileIMG").click(); 
            sessionStorage.setItem('tmpIMG',JSON.stringify(Type));
        break;
        case 0: 
            const TypeIMG   = JSON.parse(sessionStorage.getItem('tmpIMG'));
            const ItemCode  = $("#ItemCode").val();
            let FormDataIMG = new FormData($("#FormDataIMG")[0]);
            FormDataIMG.append('TypeIMG',TypeIMG);
            FormDataIMG.append('ItemCode',ItemCode);
            $.ajax({
                url: "menus/general/ajax/ajaxsku_book.php?a=AddIMG",
                type: 'POST',
                dataType: 'text',
                cache: false,
                processData: false,
                contentType: false,
                data: FormDataIMG,
                success: function(result) {
                    let obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        setTimeout(function() { 
                            GetIMG();
                        }, 1000);
                    });
                }
            });
        break;
    }
}

function Print() {
    const ItemCode = $("#ItemCode").val();
    const tmpTab = JSON.parse(sessionStorage.getItem('tmpTab'));
    const PriceType = $("#PriceType").val();
    if(ItemCode != null) {
        window.open ('menus/general/print/printsku_book.php?ItemCode='+ItemCode+'&Tab='+tmpTab+'&PriceType='+PriceType,'_blank');
    }else{
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
        $("#alert_body").html("กรุณาเลือกสินค้าก่อน");
        $("#alert_modal").modal("show");
    }
}

function CallDataMK() {
    const ItemCode = $("#ItemCode").val();
    const PriceType = "STD";
    $.ajax({
        url: "menus/general/ajax/ajaxsku_book.php?a=CallDataMK",
        type: "POST",
        data: { ItemCode : ItemCode, PriceType : PriceType, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#TableMK_1 tbody").html(inval['tbody_box1']);

                $("#PriceType").html(inval['option_pricetype']);
                $("#TableMK_2 tbody").html(inval['tbody_box2']);

                $("#TableMK_3 tbody").html(inval['tbody_box3']);
                <?php if($chk_edit_tab2 == 'Y') { ?>
                    $(".EditTab9").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                    $(".EditTab9").attr("status-tab9", "Edit");
                <?php } ?>
                sessionStorage.setItem('tmpID_Type9',JSON.stringify(inval['tmpID_Type9']));

                $("#TableMK_4 tbody").html(inval['tbody_box4']);
                <?php if($chk_edit_tab2 == 'Y') { ?>
                    $(".EditTab10").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                    $(".EditTab10").attr("status-tab10", "Edit");
                <?php } ?>
                sessionStorage.setItem('tmpID_Type10',JSON.stringify(inval['tmpID_Type10']));

                $("#TableMK_5 tbody").html(inval['tbody_box5']);
                <?php if($chk_edit_tab2 == 'Y') { ?>
                    $(".AddTab11").html("<i class='fas fa-plus-square fa-fw'></i>เพิ่ม");
                    if(inval['ChkDataType11'] == 'Y') {
                        $(".EditTab11").html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                        $(".EditTab11").attr("status-tab11", "Edit");
                    }else{
                        $(".EditTab11").html("");
                    }
                <?php } ?>
                sessionStorage.setItem('tmpID_Type11',JSON.stringify(inval['tmpID_Type11']));

            });
        }
    })
}

function SelectPriceType() {
    const ItemCode = $("#ItemCode").val();
    const PriceType = $("#PriceType").val();
    if(ItemCode != null) {
        $.ajax({
            url: "menus/general/ajax/ajaxsku_book.php?a=SelectPriceType",
            type: "POST",
            data: { ItemCode : ItemCode, PriceType : PriceType, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TableMK_2 tbody").html(inval['tbody_box2']);
                });
            }
        })
    }
}

function AddIMG_MK(Type) {
    // console.log(Type);
    switch(Type) {
        case 6: 
        case 9: 
            sessionStorage.setItem('tmpTypeIMG_MK',JSON.stringify(Type));
            $("#FileIMG_MK").prop("multiple", true);
            $("#FileIMG_MK").click(); 
        break; 
        case 7: 
        case 8: 
            sessionStorage.setItem('tmpTypeIMG_MK',JSON.stringify(Type));
            $("#FileIMG_MK").prop("multiple", false);
            $("#FileIMG_MK").click(); 
        break; 
        case 0: 
            if(sessionStorage.getItem('tmpTypeIMG_MK') == 6) {
                if($("#FileIMG_MK")[0].files.length >= 6) {
                    const TypeIMG   = JSON.parse(sessionStorage.getItem('tmpTypeIMG_MK'));
                    const ItemCode  = $("#ItemCode").val();
                    let FormDataIMG = new FormData($("#FormDataIMG_MK")[0]);
                    FormDataIMG.append('TypeIMG',TypeIMG);
                    FormDataIMG.append('ItemCode',ItemCode);
                    $.ajax({
                        url: "menus/general/ajax/ajaxsku_book.php?a=AddIMG_MK",
                        type: 'POST',
                        dataType: 'text',
                        cache: false,
                        processData: false,
                        contentType: false,
                        data: FormDataIMG,
                        success: function(result) {
                            let obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) {
                                setTimeout(function() { 
                                    GetIMG();
                                }, 1000);
                            });
                        }
                    });
                }else{
                    $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
                    $("#alert_body").html("รูปสินค้าตัวจริงต้องมีอย่างน้อย 6 รูป");
                    $("#alert_modal").modal("show");
                }
            }else{
                const TypeIMG   = JSON.parse(sessionStorage.getItem('tmpTypeIMG_MK'));
                const ItemCode  = $("#ItemCode").val();
                let FormDataIMG = new FormData($("#FormDataIMG_MK")[0]);
                FormDataIMG.append('TypeIMG',TypeIMG);
                FormDataIMG.append('ItemCode',ItemCode);
                $.ajax({
                    url: "menus/general/ajax/ajaxsku_book.php?a=AddIMG_MK",
                    type: 'POST',
                    dataType: 'text',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: FormDataIMG,
                    success: function(result) {
                        let obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            setTimeout(function() { 
                                GetIMG();
                            }, 1000);
                        });
                    }
                });
            }
        break; 
    }
}

function EditSKU_MK(Tab) {
    const ItemCode = $("#ItemCode").val();

    // 9.โปรโมชั่น, 10.ช่องทางการขายสินค้า, 11.VDO Utility
    for(let T = 9; T <= 11; T++) {
        switch (Tab) {   
            case T:
                if($(".EditTab"+T).attr("status-tab"+T) == 'Edit') {
                    $(".EditTab"+T).html("<i class='fas fa-save fa-fw'></i>บันทึก");
                    $(".EditTab"+T).attr("status-tab"+T, "Save");
                    $(".DETAIL_Type"+T).prop("disabled", false);
                }else{
                    let FormDataTab = new FormData($("#FormDataTab"+T)[0]);
                    FormDataTab.append('tmpID',JSON.parse(sessionStorage.getItem('tmpID_Type'+T)));
                    FormDataTab.append('Tab',Tab);
                    FormDataTab.append('ItemCode',ItemCode);
                    let Input =  JSON.parse(sessionStorage.getItem('tmpID_Type'+T)).split(',');
                    for(let i = 0; i < Input.length; i++) {
                        if(T == 10) {
                            if($("#CheckBox_"+Input[i]+":checked").val() == "Y") {
                                FormDataTab.append("CheckBox_"+Input[i],$("#CheckBox_"+Input[i]+":checked").val());
                            }else{
                                FormDataTab.append("CheckBox_"+Input[i],"N");
                            }
                            if(typeof($("#Remark_"+Input[i]).val()) != "undefined") {
                                FormDataTab.append("Remark_"+Input[i],$("#Remark_"+Input[i]).val());
                            }
                        }else if(T == 11){
                            FormDataTab.append("Header_"+Input[i],$("#Header_"+Input[i]).val());
                            FormDataTab.append("Detail_"+Input[i],$("#Detail_"+Input[i]).val());
                        }else{
                            FormDataTab.append("Detail_"+Input[i],$("#Detail_"+Input[i]).val());
                        }
                    }
                    $(".overlay").show();
                    $.ajax({
                        url: "menus/general/ajax/ajaxsku_book.php?a=EditSKU",
                        type: 'POST',
                        dataType: 'text',
                        cache: false,
                        processData: false,
                        contentType: false,
                        data: FormDataTab,
                        success: function(result) {
                            let obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) {
                                $(".EditTab"+T).html("<i class='fas fa-edit fa-fw'></i>แก้ไข");
                                $(".EditTab"+T).attr("status-tab"+T, "Edit");
                                $(".DETAIL_Type"+T).prop("disabled", true);
                                CallDataMK();
                            });
                            $(".overlay").hide();
                        }
                    });
                }
            break;
        }
    }
}

function Excel() {
    const ItemCode = $("#ItemCode").val();
    const PriceType = $("#PriceType").val();
    if(ItemCode != null) {
        $.ajax({
            url: "menus/general/ajax/ajaxsku_book.php?a=Excel",
            type: "POST",
            data: { ItemCode : ItemCode, PriceType : PriceType, },  
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    window.open("../../FileExport/SKUBook/"+inval['FileName'],'_blank');
                });
            }
        })
    }else{
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
        $("#alert_body").html("กรุณาเลือกสินค้าก่อน");
        $("#alert_modal").modal("show");
    }
}
</script> 
<style type="text/css">
    @media only screen and (max-width:820px) {
        .font-rps {
            font-size: 12px;
        }
    }

    @media (min-width:821px) {
        .font-rps {
            font-size: 13px;
        }
    }

    /* The switch - the box around the slider */
    .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    }

    /* Hide default HTML checkbox */
    .switch input {
    opacity: 0;
    width: 0;
    height: 0;
    }

    /* The slider */
    .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #9A1118;
    -webkit-transition: .4s;
    transition: .4s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 2.5px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }

    input:checked + .slider {
    background-color: #2196F3;
    }

    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
    -webkit-transform: translateX(23px);
    -ms-transform: translateX(23px);
    transform: translateX(23px);
    }

    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }

    .slider.round:before {
    border-radius: 50%;
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
                    <div class="col-lg">
                        <div class='d-flex align-items-center'>
                            <span class='text-primary pe-2 fw-bold' style='font-size: 17px;'><i class="fas fa-search pe-1"></i>ค้นหาข้อมูลลูกค้า</span>
                            <select class="form-control w-50" name="SelectCardCode" id="SelectCardCode" data-live-search="true" onchange="ChangeCardCode()">
                                <option value="" selected disabled>กรุณาเลือกลูกค้า</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-4" id='content'>
                    <div class="row"> <!-- Data Customer -->
                        <div class="col-lg">
                            <div class="table-responsive pt-1" >
                                <table class="table table-borderless rounded rounded-3 overflow-hidden" style='background-color: rgba(155, 0, 0, 0.04);'>
                                    <thead style='background-color: rgba(136, 0, 0, 0.70);'>
                                        <tr>
                                            <td colspan='6' class='text-light'><div class='d-flex align-center justify-content-between'>ข้อมูลลูกค้า <a href='javascript:void(0);' class='text-light aCus' style='font-size: 12px; padding-top: 1px' onclick='CreateSO()'><i class="fas fa-share"></i> ไปหน้าเปิดบิล</a></div></td>
                                        </tr>
                                    </tdead>
                                    <tbody class='font-rps' id='TheadCardCode'>
                                        <?php
                                            $Head = ["รหัสลูกค้า","ผู้แทนขาย", "เครดิต",
                                                     "ชื่อลูกค้า", "รหัสประจำตัวผู้เสียภาษี","วงเงินเครดิต",
                                                     "กลุ่มลูกค้า", "เงื่อนไขการชำระเงิน <a href='javascript:void(0);' onclick=\"ContentModal('Condition')\"><i class='fas fa-search-plus'></i></a>",
                                                     "ยอดหนี้คงค้าง <a href='javascript:void(0);' onclick=\"ContentModal()\"><i class='fas fa-search-plus'></i></a>",
                                                     "เบอร์โทรศัพท์", "ที่อยู่ <a href='javascript:void(0);'><i class='fas fa-map-marker-alt'></i></a>",
                                                     "วิธีการวางบิล", "วิธีการเก็บเงิน"]; 
                                            $n = 0;
                                            for($r = 1; $r <= 5; $r++) {
                                                if($r <= 3){
                                                    if($r == 1){
                                                        echo "<tr>";
                                                            echo "<th class='pt-3'>".$Head[$n]."</th>"; $n++;
                                                            echo "<th class='pt-3'></th>";
                                                            echo "<th class='pt-3'>".$Head[$n]."</th>"; $n++;
                                                            echo "<th class='pt-3'></th>";
                                                            echo "<th class='pt-3'>".$Head[$n]."</th>"; $n++;
                                                            echo "<th class='pt-3'></th>";
                                                        echo "</tr>";
                                                    }else{
                                                        echo "<tr>";
                                                            echo "<th>".$Head[$n]."</th>"; $n++;
                                                            echo "<th></th>";
                                                            echo "<th>".$Head[$n]."</th>"; $n++;
                                                            echo "<th></th>";
                                                            echo "<th>".$Head[$n]."</th>"; $n++;
                                                            echo "<th></th>";
                                                        echo "</tr>";
                                                    }
                                                }else{
                                                    echo "<tr>";
                                                        echo "<th>".$Head[$n]."</th>"; $n++;
                                                        echo "<th></th>";
                                                        echo "<th>".$Head[$n]."</th>"; $n++;
                                                        echo "<th></th>";
                                                    echo "</tr>";
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  
                    <div class="row pt-3"> <!-- Tabs Menu -->
                        <div class="col-lg">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-tab='tabs1' id='IDtabs1' href="#tabs1">การเข้าพบลูกค้า</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link " data-bs-toggle="tab" data-tab='tabs2' id='IDtabs2' href="#tabs2">ยอดขายของร้านค้า</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link " data-bs-toggle="tab" data-tab='tabs3' id='IDtabs3' href="#tabs3">ประวัติสินค้า</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link " data-bs-toggle="tab" data-tab='tabs4' id='IDtabs4' href="#tabs4">ประวัติการสั่งซื้อสินค้า</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link " data-bs-toggle="tab" data-tab='tabs5' id='IDtabs5' href="#tabs5">ประวัติการเข้าพบร้านค้า</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row pt-3"> <!-- Tabs Content -->
                        <div class="col-lg">
                            <div class="tab-content">
                                <div id="tabs1" class="tab-pane active">
                                    <div class="table-responsive">
                                        <table class='table table-bordered table-hover rounded rounded-3 overflow-hidden'>
                                            <thead class='font-rps text-center' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                <tr>
                                                    <th width='70%'>หัวข้อ</th>
                                                    <th>เรียบร้อย</th>
                                                    <th>ไม่เรียบร้อย</th>
                                                </tr>
                                            </thead>
                                            <tbody class='font-rps'>
                                                <?php 
                                                $data = ["0","1. สินค้าถูกโชว์เรียง และสะอาดสวยงาม","2. มี Shelf Talker หรือป้ายราคาเพื่อทำ Sales","3. มี Shelf หรือ Display","4. มี PC หรือ มือปืน","5. ได้สอบถาม PC ในเรื่องปัญหาสินค้าภายในร้านค้าแล้วหรือไม่?","6. นับสต๊อคเพื่อเติมสินค้าที่ขาด","7. ส่งสำรวจราคาคู่แข่งใน LINE กลุ่ม"];
                                                    for($i = 1; $i <= count($data)-1; $i++) {
                                                        echo"<tr>".
                                                                "<td>".$data[$i]."</td>".
                                                                "<td class='text-center'><input class='form-check-input QY".$i."' type='radio' name='Q".$i."' id='Q".$i."' value='Y' onclick=\"AddQ('Q".$i."')\" disabled></td>".
                                                                "<td class='text-center'><input class='form-check-input QN".$i."' type='radio' name='Q".$i."' id='Q".$i."' value='N' onclick=\"AddQ('Q".$i."')\" disabled></td>".
                                                            "</tr>";
                                                    }
                                                ?>
                                            </tbody>
                                            <tfoot class='font-rps' style='background-color: rgba(0, 0, 0, 0.04);'>
                                                <tr>
                                                    <td class='text-center d-flex align-items-center justify-content-around'>
                                                        <?php if ($_SESSION['DeptCode'] == 'DP001' OR $_SESSION['DeptCode'] == 'DP002'OR $_SESSION['DeptCode'] == 'DP005' OR $_SESSION['DeptCode'] == 'DP008'){ ?>
                                                            <button class='btn btn-sm btn-primary' id='CheckList' disabled><i class="fas fa-search-plus"></i> Check List การเข้าพบลูกค้า</button>
                                                        <?php } ?>

                                                        <?php if ($_SESSION['DeptCode'] == 'DP001' OR $_SESSION['DeptCode'] == 'DP002' OR $_SESSION['DeptCode'] == 'DP005' OR $_SESSION['DeptCode'] == 'DP008'){ ?>
                                                            <button class='btn btn-sm btn-info' id='MeetingPlan' disabled><i class="fas fa-calendar-day"></i> แผนการทำงาน</button>
                                                        <?php } ?>
                                                        <span>กดปุ่ม "เช็คอิน" เพื่อเช็คร้านค้า</span>
                                                    </td>
                                                    <td class='text-center' colspan='2'>
                                                        <?php if ($_SESSION['DeptCode'] == 'DP002' OR $_SESSION['DeptCode'] == 'DP005' OR $_SESSION['DeptCode'] == 'DP008'){ ?>
                                                            <button class='btn btn-sm btn-success' id='CheckedMap' disabled><i class="fas fa-map-marked-alt"></i> เช็คอิน</button>
                                                        <?php } ?>   
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div id="tabs2" class="tab-pane fade">
                                    <div class="row">
                                        <div class="col-lg">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered rounded rounded-3 overflow-hidden">
                                                    <thead class="font-rps text-center" style="background-color: rgba(155, 0, 0, 0.04);">
                                                        <tr>
                                                            <th rowspan="2">เดือน</th>
                                                            <th colspan="2">ปี <?php echo date("Y"); ?></th>
                                                            <th colspan="2">ปี <?php echo date("Y")-1; ?></th>
                                                        </tr>
                                                        <tr>
                                                            <th width="25%">ยอดขาย (บาท)</th>
                                                            <th width="10%">% กำไร</th>
                                                            <th width="25%">ยอดขาย (บาท)</th>
                                                            <th width="10%">% กำไร</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="font-rps" id="TbodyTab1">
                                                    <?php
                                                        for($m = 1; $m <= 12; $m++) {
                                                            echo "<tr>";
                                                                echo "<td>".FullMonth($m)."</td>";
                                                                echo "<td class='text-right'>&nbsp;</td>";
                                                                echo "<td class='text-center'>&nbsp;</td>";
                                                                echo "<td class='text-right'>&nbsp;</td>";
                                                                echo "<td class='text-center'>&nbsp;</td>";
                                                            echo "</tr>";
                                                        }
                                                    ?>
                                                    </tbody>
                                                    <tfoot class="font-rps" id="TfootTab1">
                                                        <tr class="table-active">
                                                            <th>รวมทั้งหมด</th>
                                                            <th class="text-right">&nbsp;</th>
                                                            <th class="text-center">&nbsp;</th>
                                                            <th class="text-right">&nbsp;</th>
                                                            <th class="text-center">&nbsp;</th>
                                                        </tr>
                                                        <tr>
                                                            <th>เป้าขายร้านค้า</th>
                                                            <th class="text-right">&nbsp;</th>
                                                            <th class="text-center">&nbsp;</th>
                                                            <th class="text-right">&nbsp;</th>
                                                            <th class="text-center">&nbsp;</th>
                                                        </tr>
                                                        <tr>
                                                            <th>% ยอดขายต่อเป้าขาย</th>
                                                            <th class="text-center">&nbsp;</th>
                                                            <th class="text-center">&nbsp;</th>
                                                            <th class="text-center">&nbsp;</th>
                                                            <th class="text-center">&nbsp;</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tabs3" class="tab-pane fade">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class='d-flex align-items-center'>
                                                <span class='text-primary pe-2'><i class="fas fa-search pe-1"></i>ค้นหาประวัติสินค้า</span>
                                                <select class="form-control selectpicker w-50" name="SelectItemCode" id="SelectItemCode" data-live-search="true" onchange="HisProduct()"></select>
                                                <button class='btn btn-sm btn-secondary ms-3' id='CallStock'><i class='fas fa-warehouse'></i> สินค้าคงคลัง</button>
                                            </div>
                                            <div class="table-responsive pt-3">
                                                <table class='table table-bordered table-hover rounded rounded-3 overflow-hidden'>
                                                    <thead class='font-rps text-center' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr>
                                                            <th>เลขที่บิล</th>
                                                            <th>วันที่ออกบิล</th>
                                                            <th>รหัสสินค้า</th>
                                                            <th>ชื่อสินค้า</th>
                                                            <th>คลังสินค้า</th>
                                                            <th>จำนวน</th>
                                                            <th>มูลค่าต่อชิ้น</th>
                                                            <th>ภาษีรวม</th>
                                                            <th>ราคาสุทธิ (VAT)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style='font-size: 12px' id='HisproductTbody'>
                                                        <?php
                                                            for($i = 1; $i <= 10; $i++) {
                                                                echo"<tr>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                    "</tr>";
                                                            } 
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <p class='text-primary' style='font-size: 12px'>ข้อมูลตั้งแต่ 2023 เป็นต้นไป</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class='d-flex align-items-center pt-3'>
                                                <span>ประวัติการสั่งซื้อสินค้า (10 รายการล่าสุด)</span>
                                            </div>
                                            <div class="table-responsive pt-3">
                                                <table class='table table-bordered table-hover rounded rounded-3 overflow-hidden'>
                                                    <thead class='font-rps text-center' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr>
                                                            <th>เลขที่บิล</th>
                                                            <th>วันที่สั่งซื้อ</th>
                                                            <th>พนักงานขาย</th>
                                                            <th>ยอดขาย (บาท)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style='font-size: 12px' id='HisItem'>
                                                        <?php
                                                            for($i = 1; $i <= 10; $i++) {
                                                                echo"<tr>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                        "<td>&nbsp;</td>".
                                                                    "</tr>";
                                                            }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>   
                                        </div>
                                    </div>
                                </div>
                                <div id="tabs4" class="tab-pane fade">
                                    <div class="row">
                                        <div class="col-lg">
                                            <div class='d-flex align-items-center'>
                                                <span>ประวัติการสั่งซื้อสินค้า (รายสินค้า) สูงสุด 10 อันดับ&nbsp;&nbsp;<a href='javascript:void(0);'><i class='fas fa-search-plus'></i></a></span>
                                            </div>
                                            <input type="hidden" id='CheckID' value='C'>
                                            <div class="table-responsive pt-3">
                                                <table class='table table-bordered table-hover rounded rounded-3 overflow-hidden'>
                                                    <thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>
                                                        <tr class='text-center'>
                                                            <th width='7%' rowspan='2' class='align-bottom'>รหัสสินค้า</th>
                                                            <th width='20%' rowspan='2' class='align-bottom'>ชื่อสินค้า</th>
                                                            <th width='5%' rowspan='2' class='align-bottom'>หน่วยขาย</th>
                                                            <th colspan='12' id='NameHeadTB'>ยอดขาย (หน่วย)</th>
                                                            <th class='d-flex align-items-center'>
                                                                <label class="switch">
                                                                    <input type="checkbox" onclick="CheckT4('checkbox')" id='CkBox' disabled>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                                <span class='ps-2' id='NameSwitch'>จำนวนตัว</span>
                                                            </th>
                                                        </tr>
                                                        <tr class='text-center'>
                                                            <?php
                                                                for($m = 1; $m <= 12; $m++) {
                                                                    echo "<th>".txtMonth($m)."</th>";
                                                                } 
                                                            ?>
                                                            <th width='10%'>รวม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id='TbodyCk' style='font-size: 12px;'>
                                                        <?php 
                                                            for($i = 1; $i <= 10; $i++) {
                                                                echo "<tr>";
                                                                for($d = 1; $d <= 16; $d++) {
                                                                    echo "<td>&nbsp;</td>";
                                                                }
                                                                echo "</tr>";
                                                            }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                                <div id="tabs5" class="tab-pane fade">
                                    <div class="row">
                                        <div class="col">
                                            <div class="table-responsive">
                                                <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableTab5'>
                                                    <thead>
                                                        <tr class='text-center'>
                                                            <th width='15%'>วันที่เข้าพบ</th>
                                                            <th>หัวข้อที่เข้าพบ</th>
                                                            <th>รายละเอียดเข้าพบ</th>
                                                            <th width='10%'>พนักงานที่เข้าพบ</th>
                                                            <th width='5%'><i class="fas fa-search-location fa-fw fa-lg"></i></th>
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
    </div>
</section>

<div class="modal fade" id="ContentModal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl" id='SizeModal'>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id='HeaderModal'></h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg" id='BoxContentModal'></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalCheckList" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl" id='SizeModalCheckList'>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id='HeadCheckList'></h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg" id='BoxCheckList'></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ORDER PREVIEW -->
<div class="modal fade" id="ModalPreview" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full modal-dialog-scrollable">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-file-invoice-dollar fa-fw fa-1x"></i> รายละเอียดใบกำกับภาษี</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="h6">ใบกำกับภาษีเลขที่: <span id="soview_DocNum">IV-YYMMAXXXX</span></h5>
                </div>
            </div>
            <!-- ORDER HEADER -->
            <div class="row">
                <div class="col-12">
                    <table class="table table-borderless table-sm" style="font-size: 12px;">
                        <tr>
                            <td class="fw-bolder" width="15%">ชื่อลูกค้า</td>
                            <td class="fw-bold" width="35%" id="soview_CardCode"></td>
                            <td class="fw-bolder" width="15%">เลขที่ผู้เสียภาษี</td>
                            <td class="fw-bold" width="35%" id="soview_LictradeNum"></td>
                        </tr>
                        <tr>
                            <td class="fw-bolder">วันที่ใบสั่งขาย</td>
                            <td class="fw-bold" id="soview_DocDate"></td>
                            <td class="fw-bolder">วันที่กำหนดส่ง</td>
                            <td class="fw-bold" id="soview_DocDueDate"></td>
                        </tr>
                        <tr>
                            <td class="fw-bolder">พนักงานขาย</td>
                            <td class="fw-bold" id="soview_SlpCode"></td>
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
            </ul>
            <!-- CONTENT TAB -->
            <div class="tab-content">
                <div class="tab-pane active" id="SOItemList" role="tabpanel" aria-labelledby="SOItemTab" style="font-size: 12px;">
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
                <div class="tab-pane fade" id="SOAddressList" role="tabpanel" aria-labelledby="SOAddressTab">
                    <div class="row mt-4">
                        <div class="col-12">
                            <table class="table table-borderless" style="font-size: 12px;">
                                <tr>
                                    <td class="fw-bolder align-top" width="17.5%">ที่อยู่เปิดบิล</td>
                                    <td class="align-top" id="soview_BilltoAddress" height="72px"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder align-top">ที่อยู่จัดส่ง</td>
                                    <td class="align-top" id="soview_ShiptoAddress" height="72px"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder align-top">ประเภทขนส่ง</td>
                                    <td class="align-top" id="soview_ShippingType"></td>
                                </tr>
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

<!-- MODAL REPORT TRIP -->
<div class="modal fade" id="ModalReportTrip" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt fa-fw fa-lg"></i> รายละเอียดการเข้าพบร้านค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <table class="table table-borderless" style="font-size: 13px;">
                            <tr>
                                <th width="20%">ร้านค้า</th>
                                <td id="RptCardCode"></td>
                            </tr>
                            <tr>
                                <th>รายละเอียดแผนงาน</th>
                                <td id="RptComments"></td>
                            </tr>
                            <tr>
                                <th>วันที่จะเข้าพบ</th>
                                <td id="RptPlanDate"></td>
                            </tr>
                            <tr>
                                <th>วันที่เช็คอิน</th>
                                <td id="RptCheckInDate"></td>
                            </tr>
                            <tr>
                                <th>ระยะห่างที่เช็คอิน</th>
                                <td id="RptDistance"></td>
                            </tr>
                            <tr>
                                <th class="align-top">พิกัดที่เช็คอิน</th>
                                <td><div id="RptCheckInMaps" style="height: 25rem; border: 1px solid #000;"></div></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        CallHeade();
	});
</script> 
<script type="text/javascript">
    function CallHeade(){
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
</script> 

<script>
    $(document).ready(function(){
        SeleteCardCode();
	});
</script>

<script>
    function SeleteCardCode(){
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

                <?php if(isset($_GET['CardCode'])){ ?>
                    $("#SelectCardCode").append(opt).val('<?php echo $_GET['CardCode']; ?>').change().selectpicker();
                <?php }else{ ?>
                    $("#SelectCardCode").append(opt).selectpicker();
                <?php } ?>
            }
        });
    }

    function ChangeCardCode(){
        $(".overlay").show();
        var CardCode = $("#SelectCardCode").val();
            $.ajax({
                url: "menus/sale/ajax/ajaxreportcus.php?a=ChangeCardCode",
                type: "POST",
                data: { CardCode : CardCode, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        // PART 1
                        $("#TheadCardCode").html(inval['TheadP1']);

                        // TAB 1
                        for(var c = 1; c <= 7; c++) { 
                            $(".QY"+c).removeAttr("disabled");
                            $(".QN"+c).removeAttr("disabled");
                            if(inval["QY"+c] == 'true') { $(".QY"+c).prop('checked', true); }else{ $(".QY"+c).prop('checked', false); }
                            if(inval["QN"+c] == 'true') { $(".QN"+c).prop('checked', true); }else{ $(".QN"+c).prop('checked', false); }
                        }

                        $("#IDtabs2, #IDtabs3, #IDtabs4, #tabs2, #tabs3, #tabs4").removeClass("active");
                        $("#IDtabs1, #tabs1").addClass("active");

                        // btn การเข้าพบลูกค้า
                        $("#CheckList, #MeetingPlan, #CheckedMap").removeAttr("disabled");

                        // PART 2 
                        // $("#tgr1").html(inval['tgr1']);
                        // $("#tgr2").html(inval['tgr2']);
                        $("#TbodyTab1").html(inval['TbodyP2']);
                        $("#TfootTab1").html(inval['TfootP2']);

                        HisItem();
                        CheckT4();
                        HisMeet();
                    })
                    $(".overlay").hide();
                }
            })
    }

    function ContentModal(Data) {
        var DataTrue = Data;
        switch (DataTrue) {
            case "Condition":
                $("#SizeModal").removeClass("modal-xl");
                var BoxContentModal =  "<span class='fw-bold' style='font-size: 13px'>"+
                                    "1. A: นับวันส่งของ, B: ไม่นับวันส่งของ <br>"+
                                    "2. A: ไม่ต้องวางบิล, B: ส่งของพร้อมวางบิล, C: วางบิลทาง Fax, D: วางบิลทางจดหมาย <br>"+
                                    "3. A: ส่งของเก็บเช็คเลย, B: เก็บเช็ค วันที่/ทุกวัน, Y: โอนเงินหลังส่งของวันที่, Z: จ่ายเงินสด"+
                                "</span>";
                $("#HeaderModal").html("<i class='fas fa-money-check-alt' style='font-size: 16px;'></i>&nbsp;&nbsp;&nbsp;เงื่อนไขการชำระเงิน");
                $("#BoxContentModal").html(BoxContentModal);
                $("#ContentModal").modal("show");
                break;
            default:
                if($("#SelectCardCode").val() != null){
                    if(DataTrue != undefined){
                        $(".overlay").show();
                        $.ajax({
                            url: "menus/sale/ajax/ajaxreportcus.php?a=Debt",
                            type: "POST",
                            data: { CardCode : DataTrue, },
                            success: function(result) {
                                var obj = jQuery.parseJSON(result);
                                $.each(obj,function(key,inval) {
                                    $("#SizeModal").removeClass("modal-xl");
                                    $("#SizeModal").addClass("modal-xl");
                                    var Table = "<div class='table-responsive'>"+
                                                    "<table class='table table-sm table-hover rounded rounded-3 overflow-hidden'>"+
                                                        "<thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>"+
                                                            "<tr class='text-center'>"+
                                                                "<th>เลขที่บิล</th>"+
                                                                "<th>วันที่เอกสาร</th>"+
                                                                "<th>กำหนดชำระเงิน</th>"+
                                                                "<th width='25%'>ยอดรวม</th>"+
                                                                "<th width='25%'>ยอดคงค้าง</th>"+
                                                                "<th width='10%'>จำนวนวัน</th>"+
                                                            "</tr>"+
                                                        "</thead>"+
                                                        "<tbody class='font-rps' id='TbodyDebt'></tbody>"+
                                                    "</table>"+
                                                "</div>";
                                    $("#HeaderModal").html("<i class='fas fa-clipboard-list' style='font-size: 18px;'></i>&nbsp;&nbsp;รายการบิลคงค้าง");
                                    $("#BoxContentModal").html(Table);
                                    $("#TbodyDebt").html(inval['Tbody']);
                                    $("#ContentModal").modal("show");
                                });
                                $(".overlay").hide();
                            }
                        });
                    }else{
                        $("#SizeModal").removeClass("modal-xl");
                        $("#SizeModal").addClass("modal-xl");
                        var Table = "<div class='table-responsive'>"+
                                        "<table class='table table-sm rounded rounded-3 overflow-hidden'>"+
                                            "<thead class='font-rps' style='background-color: rgba(155, 0, 0, 0.04);'>"+
                                                "<tr class='text-center'>"+
                                                    "<th>เลขที่บิล</th>"+
                                                    "<th>วันที่เอกสาร</th>"+
                                                    "<th>กำหนดชำระเงิน</th>"+
                                                    "<th>ยอดรวม</th>"+
                                                    "<th>ยอดคงค้าง</th>"+
                                                    "<th>จำนวนวัน</th>"+
                                                "</tr>"+
                                            "</thead>"+
                                            "<tbody class='font-rps' id='TbodyDebt'></tbody>"+
                                        "</table>"+
                                    "</div>";
                        $("#HeaderModal").html("<i class='fas fa-clipboard-list' style='font-size: 18px;'></i>&nbsp;&nbsp;รายการบิลคงค้าง");
                        $("#BoxContentModal").html(Table);
                        $("#ContentModal").modal("show");
                    }
                    break;
                }
        }
    }

    function MapCardCode(Data) {
        $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
        $("#alert_body").html("ไม่มีข้อมูลที่อยู่ใน Google Map");
        $("#alert_modal").modal("show");
    }

    function YearSales() {
        $(".overlay").show();
        var CardCode = $("#SelectCardCode").val();
        if(CardCode != null) {
            $.ajax({
                url: "menus/sale/ajax/ajaxreportcus.php?a=YearSales",
                type: "POST",
                data: { CardCode : CardCode, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#SizeModal").removeClass("modal-xl");
                        var BoxContentModal =  "<span class='fw-bold' style='font-size: 13px'>"+
                                            inval['TextBox']+
                                        "</span>";
                        $("#HeaderModal").html("ยอดขายปีปัจจุบันและย้อนหลัง 3 ปี");
                        $("#BoxContentModal").html(BoxContentModal);
                        $("#ContentModal").modal("show");
                    });
                    $(".overlay").hide();
                }
            });
        }
    }

    function AddQ(Q) {
        var Qtion = Q;
        var QtionValue = $("#"+Q+":checked").val();
        if($("#SelectCardCode").val() != null){
            var CardCode = $("#SelectCardCode").val();
            $.ajax({
                url: "menus/sale/ajax/ajaxreportcus.php?a=AddQ",
                type: "POST",
                data: { CardCode : CardCode, Qtion : Qtion, QtionValue : QtionValue, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        
                    });
                }
            });
        }
    }
    $("#CheckedMap").on("click", function() {
        if($("#SelectCardCode").val() != null){
            var CardCode = $("#SelectCardCode").val();
            CheckIn(CardCode, null);
            $("#ModalCheckIn").modal("show");
        }
    })

    $("#MeetingPlan").on("click", function() {
        if($("#SelectCardCode").val() != null){
            $("#SizeModal").removeClass("modal-xl");
            $("#SizeModal").addClass("modal-xl");
            var Year = new Date().getFullYear();
            var Content ="<div class='d-flex'>"+
                            "<i class='far fa-calendar me-1' style='font-size: 30px; color: #084298;'></i>"+
                            "<select class='me-3 text-center form-select p-1' style='width: 8rem; font-size: 15.5px;' name='MpTi' id='MpTi' onchange='MeetingPlan()'>"+
                                "<option value='Ti1' selected>เดือน 1-4</option>"+
                                "<option value='Ti2'>เดือน 5-8</option>"+
                                "<option value='Ti3'>เดือน 9-12</option>"+
                            "</select>"+
                            "<i class='far fa-calendar-alt me-1 text-warning' style='font-size: 30px;'></i>"+
                            "<select class='text-center form-select p-1' style='width: 8rem; font-size: 15.5px;' name='MpYear' id='MpYear' onchange='MeetingPlan()'>"+
                                "<option value='"+(Year+2)+"'>"+(Year+2)+"</option>"+
                                "<option value='"+(Year+1)+"'>"+(Year+1)+"</option>"+
                                "<option value='"+Year+"' selected>"+Year+"</option>"+
                                "<option value='"+(Year-1)+"'>"+(Year-1)+"</option>"+
                                "<option value='"+(Year-2)+"'>"+(Year-2)+"</option>"+
                            "</select>"+
                          "</div>";

            Content += "<div class='table-responsive pt-2'>"+
                            "<table class='table table-sm rounded rounded-3 overflow-hidden'>"+
                                "<thead class='font-rps' style='background-color: rgba(136, 0, 0, 0.70);'>"+
                                    "<tr class='text-center text-light'>"+
                                        "<td>เดือน</td>"+
                                        "<td>แผนการดำเนินงาน</td>"+
                                        "<td>ผลการดำเนินงาน</td>"+
                                    "</tr>"+
                                "</thead>"+
                                "<tbody class='font-rps' id='MpTbody' style='background-color: rgba(155, 0, 0, 0.02);'></tbody>"+
                            "</table>"+
                        "</div>";
            $("#HeaderModal").html("รายงานแผนและการเข้าพบ");
            $("#BoxContentModal").html(Content);
            $("#ContentModal").modal("show");
            MeetingPlan();
        }
    })
    function MeetingPlan() {
        var Ti = $("#MpTi").val();
        var Year = $("#MpYear").val();
        var CardCode = $("#SelectCardCode").val();
        $.ajax({
            url: "menus/sale/ajax/ajaxreportcus.php?a=MeetingPlan",
            type: "POST",
            data: { CardCode : CardCode, Ti : Ti, Year : Year, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#MpTbody").html(inval['Tbody']);
                });
            }
        });
    }
    function AddPlan(Mp,m){
        var Comments = $("#"+Mp+m).val();
        if(Comments != '') {
            $.ajax({
                url: "menus/sale/ajax/ajaxreportcus.php?a=AddPlan",
                type: "POST",
                data: { CardCode : $("#SelectCardCode").val(), Mp : Mp, Month : m, Comments : Comments, Year : $("#MpYear").val(), },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        
                    });
                }
            });
        }
    }

    $("#CheckList").on("click", function() {
        if($("#SelectCardCode").val() != null) {
            $("#SizeModalCheckList").addClass("modal-full");
            var Year = new Date().getFullYear();
            var ContentBox ="<div class='d-flex justify-content-end'>"+
                                "<i class='far fa-calendar-alt me-1 text-warning' style='font-size: 30px;'></i>"+
                                "<select class='text-center form-select p-1' style='width: 8rem; font-size: 15.5px;' name='CLYear' id='CLYear' onchange='CheckList()'>"+
                                    "<option value='"+(Year+2)+"'>"+(Year+2)+"</option>"+
                                    "<option value='"+(Year+1)+"'>"+(Year+1)+"</option>"+
                                    "<option value='"+Year+"' selected>"+Year+"</option>"+
                                    "<option value='"+(Year-1)+"'>"+(Year-1)+"</option>"+
                                    "<option value='"+(Year-2)+"'>"+(Year-2)+"</option>"+
                                "</select>"+
                            "</div>"+
                            "<div class='table-responsive pt-2'>"+
                                "<table class='table table-sm rounded rounded-3 overflow-hidden'>"+
                                    "<thead class='font-rps' style='background-color: rgba(136, 0, 0, 0.70);'>"+
                                        "<tr class='text-center text-light'>"+
                                            "<td>หัวข้อ</td>";
                                            var month = ["0", "ม.ค.", "ก.พ.", "มี.ค.",	"เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
                                            for (var m = 1; m <= 12; m++ ){
                                                ContentBox += "<td>"+month[m]+"</td>";
                                            }
                            ContentBox += "</tr>"+
                                    "</thead>"+
                                    "<tbody class='font-rps' id='CLTbody' style='background-color: rgba(155, 0, 0, 0.02);'></tbody>"+
                                "</table>"+
                            "</div>"+
                            "<div class='d-flex'>"+
                                "<span class='me-3 text-primary'><i class='fas fa-check'></i>&nbsp;เรียบร้อย</span>"+
                                "<span class='text-primary'><i class='fas fa-times'></i>&nbsp;ไม่เรียบร้อย</span>"+
                            "</div>";
            $("#HeadCheckList").html("<i class='fas fa-search-plus' style='font-size: 15px;'></i>&nbsp;&nbsp;Checklist การเข้าพบลูกค้า");
            $("#BoxCheckList").html(ContentBox);
            $("#ModalCheckList").modal("show");
            CheckList();
        }
    })
    function CheckList() {
        var CardCode = $("#SelectCardCode").val();
        var CLYear = $("#CLYear").val();
        $.ajax({
            url: "menus/sale/ajax/ajaxreportcus.php?a=CheckList",
            type: "POST",
            data: { CardCode : CardCode, CLYear : CLYear, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#CLTbody").html(inval['Tbody']);
                });
            }
        });
    }

    $(document).ready(function(){
        SelectItemCode();
	});
    function SelectItemCode(){
        $.ajax({
            url: "menus/sale/ajax/ajaxreportcus.php?a=SelectItemCode",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#SelectItemCode").html(inval['option']);
                    $("#SelectItemCode").selectpicker("refresh");
                });
            }
        });
    }
    function HisProduct() {
        if($("#SelectCardCode").val() != null) {
            var ItemCode = $("#SelectItemCode").val();
            var CardCode = $("#SelectCardCode").val();
            $.ajax({
                url: "menus/sale/ajax/ajaxreportcus.php?a=HisProduct",
                type: "POST",
                data: { CardCode : CardCode, ItemCode : ItemCode, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#HisproductTbody").html(inval['TbodyT3']);
                    });
                }
            });
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
            $("#alert_body").html("กรุณาเลือกลูกค้าก่อน");
            $("#alert_modal").modal("show");
        }
    }
    $("#CallStock").on("click", function() {
        if($("#SelectCardCode").val() != null && $("#SelectItemCode").val() != null) {
            $(".overlay").show();
            var ItemCode = $("#SelectItemCode").val();
            $.ajax({
                url: "menus/sale/ajax/ajaxreportcus.php?a=CallStock",
                type: "POST",
                data: { ItemCode : ItemCode, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#SizeModal").removeClass("modal-xl");
                        var Head = "<i class='fas fa-warehouse' style='font-size: 17px;'></i>&nbsp;&nbsp;&nbsp;สินค้าคงคลัง&nbsp;&nbsp;<i class='fas fa-caret-right'></i>&nbsp;"+ItemCode+"";
                        var Htable ="<div class='table-responsive pt-2'>"+
                                        "<table class='table table-sm rounded rounded-3 overflow-hidden'>"+
                                            "<thead class='font-rps' style='background-color: rgba(136, 0, 0, 0.70);'>"+
                                                "<tr class='text-center text-light'>"+
                                                    "<td>คลังสินค้า</td>"+
                                                    "<td>จำนวนคงคลัง</td>"+
                                                "</tr>"+
                                            "</thead>"+
                                            "<tbody class='font-rps' id='TbodyCallStock' style='background-color: rgba(155, 0, 0, 0.02);'></tbody>"+
                                        "</table>"+
                                    "</div>";
                        $("#HeaderModal").html(Head);
                        $("#BoxContentModal").html(Htable);
                        $("#TbodyCallStock").html(inval['Tbody']);
                        $("#ContentModal").modal("show");
                    });
                    $(".overlay").hide();
                }
            });
        }
    })
    function HisItem(){
        if($("#SelectCardCode").val() != null) {
            var CardCode = $("#SelectCardCode").val();
            $(".overlay").show();
            $.ajax({
                url: "menus/sale/ajax/ajaxreportcus.php?a=HisItem",
                type: "POST",
                data: { CardCode : CardCode, },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#HisItem").html(inval['Tbody']);
                    });

                    $(".DataHisItem").on("click", function() {
                        var DocEntry = $(this).attr("data-hisitem");
                        $.ajax({
                            url: "menus/sale/ajax/ajaxreportcus.php?a=DataHisItem",
                            type: "POST",
                            data: { DocEntry : DocEntry, },
                            success: function(result) {
                                var obj = jQuery.parseJSON(result);
                                $.each(obj,function(key,inval) {
                                    $("#SOAddressTab, #SOAddressList").removeClass("active");
                                    $("#SOItemTab, #SOItemList").addClass("active");

                                    /* SO Header */
                                    $("#soview_DocNum").html(inval['view_DocNum']);
                                    $("#soview_CardCode").html(inval['view_CardCode']);
                                    $("#soview_LictradeNum").html(inval['view_LicTradeNum']);
                                    $("#soview_DocDate").html(inval['view_DocDate']);
                                    $("#soview_DocDueDate").html(inval['view_DocDueDate']);
                                    $("#soview_SlpCode").html(inval['view_SlpCode']);

                                    /* SO Detail */
                                    $("#soview_ItemList").html(inval['view_ItemList']);

                                    /* Address */
                                    $("#soview_BilltoAddress").html(inval['view_BilltoAddress']);
                                    $("#soview_ShiptoAddress").html(inval['view_ShiptoAddress']);
                                    $("#soview_ShippingType").html(inval['view_ShippingType']);

                                    $("#ModalPreview").modal("show");
                                });
                            }
                        })
                    });
                    $(".overlay").hide();
                }
            });
        }
    }

    // TAB 4
    function CheckT4(s) {
        $("#CkBox").removeAttr("disabled");
        var CheckID = s;
        if(CheckID != undefined) {
            switch ($("#CheckID").val()) {
                case "C": 
                    $("#CheckID").val("U"); 
                    $("#NameHeadTB").html("ยอดขาย (บาท)");
                    $("#NameSwitch").html("ราคา");
                    break;
                case "U": 
                    $("#CheckID").val("C"); 
                    $("#NameHeadTB").html("ยอดขาย (หน่วย)");
                    $("#NameSwitch").html("จำนวนตัว");
                    break;
                default:break;
            }
        }
        var CardCode = $("#SelectCardCode").val();
        var UCode = $("#CheckID").val();
        $.ajax({
            url: "menus/sale/ajax/ajaxreportcus.php?a=CheckT4",
            type: "POST",
            data: { CardCode : CardCode, UCode : UCode, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TbodyCk").html(inval['Tbody']);
                });
            }
        });
    }
    
    function CreateSO() {
        var CardCode = $("#SelectCardCode").val();
        if(CardCode != null) {
            window.open("?p=saleorder&CardCode="+CardCode,"_blank");
        }
    }

    function HisMeet() {
        var CardCode = $("#SelectCardCode").val();
        $.ajax({
            url: "menus/sale/ajax/ajaxreportcus.php?a=HisMeet",
            type: "POST",
            data: { CardCode : CardCode, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    let Tbody = "";
                    if(inval['Row'] != 0) {
                        for(let r = 1; r <= inval['Row']; r++) {
                            Tbody +="<tr>"+
                                        "<td class='text-center'>"+inval[r]['CreateDate']+"</td>"+
                                        "<td>"+inval[r]['Comments']+"</td>"+
                                        "<td>"+inval[r]['Remark']+"</td>"+
                                        "<td>"+inval[r]['ChkName']+"</td>"+
                                        "<td class='text-center'>"+inval[r]['RouteEntry']+"</td>"+
                                    "</tr>";
                        }
                    }
                    $("#TableTab5 tbody").html(Tbody);
                });
            }
        })
    }

    function HisMeetRoute(RouteEntry) {
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
    
</script>
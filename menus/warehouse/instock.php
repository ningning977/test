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
            <div class="row d-flex justify-content-between">
                <div class="col-lg d-flex">
                    <div class='form-group' style='width: 200px;'>
                        <label for="">เลือกคลัง</label>
                        <select class="form-select form-select-sm" name="filt_whsgroup" id="filt_whsgroup">
                            <option value="WALL" selected>เลือกคลังสินค้าทั้งหมด</option>
                            <option style='color: rgba(158, 158, 158, 0.25);' disabled>__________________________________________________________________</option>
                            <optgroup label="สิทธิ์จองแต่ละช่องทาง">
                                <option value="CMT1">โควต้า MT1</option>
                                <option value="CMT2">โควต้า MT2</option>
                                <option value="CTT2">โควต้า TT</option>
                                <option value="COUL">โควต้าหน้าร้าน</option>
                                <option value="CONL">โควต้าออนไลน์</option>                     
                            </optgroup>
                            <option style='color: rgba(158, 158, 158, 0.25);' disabled>__________________________________________________________________</option>
                            <?php
                                $sqlWhs = "SELECT '".$_SESSION['uName']." ".$_SESSION['uLastName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP',
                                                T0.WhsCode, T0.WhsName, T0.Location,
                                                CASE
                                                    WHEN T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') THEN 'W100'
                                                    WHEN T0.WhsCode IN ('MT') THEN 'W101'
                                                    WHEN T0.WhsCode IN ('MT2') THEN 'W102'
                                                    WHEN T0.WhsCode IN ('TT-C') THEN 'W103'
                                                    WHEN T0.WhsCode IN ('OUL') THEN 'W104'
                                                    WHEN T0.WhsCode IN ('KB1','KB1.1') THEN 'W200'
                                                    WHEN T0.Location IN (2) THEN 'W300'
                                                    WHEN T0.Location IN (6,7,9) THEN 'W400'
                                                ELSE 'W500' END AS 'WhsGroup'
                                            FROM OWHS T0
                                            WHERE T0.WhsCode NOT IN ('W','0') AND T0.InActive = 'N'
                                            ORDER BY 'WhsGroup', T0.Location, T0.WhsCode";
                                $QRYWhs = SAPSelect($sqlWhs);
                                $tempGroup = "";
                                while($WhsGroup = odbc_fetch_array($QRYWhs)) {
                                    if($tempGroup != $WhsGroup['WhsGroup']) {
                                        if($tempGroup != NULL) {
                                            echo "</optgroup>
                                            <option style='color: rgba(158, 158, 158, 0.25);' disabled>__________________________________________________________________</option>";
                                        }
                                        $tempGroup = $WhsGroup['WhsGroup'];
                                        echo "<optgroup label='".WhsGroupName($WhsGroup['WhsGroup'])."'>";
                                            if($tempGroup != "W101" && $tempGroup != "W102" && $tempGroup != "W103" && $tempGroup != "W104") {
                                                echo "<option value='G".$WhsGroup['WhsGroup']."'>เลือก ".WhsGroupName($WhsGroup['WhsGroup'])." ทั้งหมด</option>";
                                            }
                                            echo "<option value='".conutf8($WhsGroup['WhsCode'])."'>".conutf8($WhsGroup['WhsCode'])." - ".conutf8($WhsGroup['WhsName'])."</option>";
                                    } else {
                                        echo "<option value='".conutf8($WhsGroup['WhsCode'])."'>".conutf8($WhsGroup['WhsCode'])." - ".conutf8($WhsGroup['WhsName'])."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group ps-3" style='width: 170px;'>
                        <label for="">เลือกสถานะ</label>
                        <select class="form-select form-select-sm" name="filt_status" id="filt_status">
                            <option value="SALL">เลือกสถานะทั้งหมด</option>
                            <option value="D">สถานะสินค้า D </option>
                            <option value="R">สถานะสินค้า R </option>
                            <option value="A">สถานะสินค้า A </option>
                            <option value="W">สถานะสินค้า W </option>
                            <option value="N">สถานะสินค้า N </option>
                            <option value="M">สถานะสินค้า M </option>
                            <option value="P">สถานะสินค้า P</option>
                            <option value="E">สถานะสินค้า E</option>
                        </select>
                    </div>
                    <div class="form-group ps-3" style='width: 320px;'>
                        <label for="">เงื่อนไข</label>
                        <div class="form-control form-control-sm d-flex align-items-center justify-content-around">
                            <input class="form-check-input m-0" type="checkbox" id="filt_getzero">
                            <span class="ms-1">สินค้าคงคลัง = 0</span>
                            <input class="form-check-input m-0 ms-2" type="checkbox" id="filt_aging">
                            <span class="ms-1">ดึงอายุสินค้า (Aging)</span>
                        </div>
                    </div>

                    <!-- ปุ่ม "ค้นหา" สำหรับหน้าจอใหญ่ -->
                    <div class='align-self-center ps-3 d-sm-none d-lg-block' style='width: 120px;'>
                        <button class='btn btn-sm btn-primary' style='margin-top: 10px;' onclick="Search()"><i class="fas fa-search"></i> ค้นหา</button>
                    </div>
                </div>
                <div class="col-lg d-flex justify-content-between">
                    <div class='align-self-center'>
                        <!-- ปุ่ม "ค้นหา" สำหรับหน้าจอเล็ก -->
                        <button class='btn btn-sm btn-primary d-lg-none me-5' style='margin-top: 10px;' onclick="Search()"><i class="fas fa-search"></i> ค้นหา</button>
                        <button class='btn btn-sm btn-success' style='margin-top: 10px;' onclick="Export()"><i class="fas fa-file-excel"></i> Excel</button>
                    </div>
                    <div class='align-self-center'>
                        <button class='btn btn-sm btn-info' style='margin-top: 10px;' id='Define'><i class="fas fa-info-circle"></i> นิยามคลัง</button>
                    </div>
                </div>
            </div>
            <hr class='m-2' style='color: #BDBDBD;'>

            <div class="row">
                <div class="col-lg">
                    <div class="table-responsive">
                        <table class='table table-sm table-hover table-bordered rounded rounded-3 overflow-hidden' id='TableDATA'>
                            <thead style='font-size: 13px;' id='Thead'>
                                <tr class='text-center'>
                                    <th>กำลังโหลดข้อมูล <i class="fas fa-spinner fa-pulse"></i></th>
                                </tr>
                            </thead>
                            <tbody style='font-size: 12px;' id='Tbody'></tbody>
                            <tfoot style='font-size: 12px;' id='Tfoot'></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- นิยามคลัง -->
<div class="modal fade" tabindex="-1" role="dialog" id="ModalDefine">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-info-circle" style='font-size: 15px;'></i> นิยามคลัง</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" style="color: #000;">
                <div class="col-lg-12">
                    <p>รายงานนี้ได้แบ่งคลังสินค้าออกเป็น 5 กลุ่มใหญ่ดังนี้ (ข้อมูล ณ 25 ตุลาคม 2564)</p>
                    <!-- W100 TO W104 -->
                    <h4 class="font-weight-bold">1. คลังสินค้าพร้อมขาย KSY</h4>
                    <p>คลังสินค้าประเภทนี้จะจัดเก็บสินค้าที่มีสภาพพร้อมขายที่ตั้งอยู่ ณ คลังสินค้าลาดสวาย (อาคาร KSY และ KSM) จังหวัดปทุมธานี ประกอบไปด้วย กลุ่มย่อยดังนี้</p>
                    
                    <!-- W100 -->
                    <h4 class="font-weight-bold">1.1 คลังพร้อมขายส่วนกลาง</h4>
                    <p>คลังสินค้าที่จัดเก็บสินค้าส่วนกลาง โดยฝ่ายการตลาดเป็นผู้รับผิดชอบในการระบายสินค้า และฝ่ายคลังสินค้าเป็นผู้รับผิดชอบในการดูแลจัดเก็บ</p>
                    <?php
                    $w100SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE T0.WhsCode IN ('KB2','KSY','KSM','KBM','KB4') AND T0.WhsCode IS NOT NULL ORDER BY T0.WhsCode ASC";
                    $w100QRY = SAPSelect($w100SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w100RST = odbc_fetch_array($w100QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w100RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w100RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>

                    <!-- W101 -->
                    <h4 class="font-weight-bold">1.2 คลังพร้อมขาย MT1</h4>
                    <p>คลังสินค้าที่จัดเก็บสินค้าส่วนกลาง โดยฝ่ายขาย MT1 เป็นผู้รับผิดชอบในการระบายสินค้า และฝ่ายคลังสินค้าเป็นผู้รับผิดชอบในการดูแลจัดเก็บ</p>
                    <?php
                    $w101SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE T0.WhsCode IN ('MT') AND T0.WhsCode IS NOT NULL ORDER BY T0.WhsCode ASC";
                    $w101QRY = SAPSelect($w101SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w101RST = odbc_fetch_array($w101QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w101RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w101RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>

                    <!-- W102 -->
                    <h4 class="font-weight-bold">1.3 คลังพร้อมขาย MT2</h4>
                    <p>คลังสินค้าที่จัดเก็บสินค้าส่วนกลาง โดยฝ่ายขาย MT2 เป็นผู้รับผิดชอบในการระบายสินค้า และฝ่ายคลังสินค้าเป็นผู้รับผิดชอบในการดูแลจัดเก็บ</p>
                    <?php
                    $w102SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE T0.WhsCode IN ('MT2') AND T0.WhsCode IS NOT NULL ORDER BY T0.WhsCode ASC";
                    $w102QRY = SAPSelect($w102SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w102RST = odbc_fetch_array($w102QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w102RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w102RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>

                    <!-- W103 -->
                    <h4 class="font-weight-bold">1.4 คลังพร้อมขาย TT</h4>
                    <p>คลังสินค้าที่จัดเก็บสินค้าส่วนกลาง โดยฝ่ายขาย TT เป็นผู้รับผิดชอบในการระบายสินค้า และฝ่ายคลังสินค้าเป็นผู้รับผิดชอบในการดูแลจัดเก็บ</p>
                    <?php
                    $w103SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE T0.WhsCode IN ('TT-C') AND T0.WhsCode IS NOT NULL ORDER BY T0.WhsCode ASC";
                    $w103QRY = SAPSelect($w103SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w103RST = odbc_fetch_array($w103QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w103RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w103RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>

                    <!-- W104 -->
                    <h4 class="font-weight-bold">1.5 คลังพร้อมขายหน้าร้าน</h4>
                    <p>คลังสินค้าที่จัดเก็บสินค้าส่วนกลาง โดยฝ่ายขายหน้าร้านเป็นผู้รับผิดชอบในการระบายสินค้า และฝ่ายคลังสินค้าเป็นผู้รับผิดชอบในการดูแลจัดเก็บ</p>
                    <?php
                    $w104SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE T0.WhsCode IN ('OUL') AND T0.WhsCode IS NOT NULL ORDER BY T0.WhsCode ASC";
                    $w104QRY = SAPSelect($w104SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w104RST = odbc_fetch_array($w104QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w104RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w104RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>
                    <hr />
                    <!-- W200 -->
                    <h4 class="font-weight-bold">2. คลังพร้อมขาย KBI</h4>
                    <p>คลังสินค้าประเภทนี้จะจัดเก็บสินค้าที่มีสภาพพร้อมขายที่ตั้งอยู่ ณ สำนักงานใหญ่ (รามอินทรา กม.4) โดยฝ่ายขายหน้าร้านเป็นผู้รับผิดชอบในการระบายสินค้าและดูแลจัดเก็บ</p>
                    <?php
                    $w200SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE T0.WhsCode IN ('KB1','KB1.1') AND T0.WhsCode IS NOT NULL ORDER BY T0.WhsCode ASC";
                    $w200QRY = SAPSelect($w200SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w200RST = odbc_fetch_array($w200QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w200RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w200RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>
                    <hr />
                    <!-- W300 -->
                    <h4 class="font-weight-bold">3. คลังพร้อมขายซัพพลายเออร์</h4>
                    <p>คลังสินค้าประเภทนี้จะจัดเก็บสินค้าที่มีสภาพพร้อมขายที่ตั้งอยู่ ณ คลังสินค้าของซัพพลายเออร์ โดยซัพพลายเออร์เป็นผู้รับผิดชอบในการดูแลจัดเก็บ</p>
                    <?php
                    $w300SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE T0.Location IN (2) AND T0.WhsCode IS NOT NULL ORDER BY T0.WhsCode ASC";
                    $w300QRY = SAPSelect($w300SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w300RST = odbc_fetch_array($w300QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w300RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w300RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>
                    <hr />
                    <!-- W400 -->
                    <h4 class="font-weight-bold">4. คลังสินค้ามือสอง</h4>
                    <p>คลังสินค้าประเภทนี้จะจัดเก็บสินค้าที่มีสภาพมือสอง โดยผู้รับผิดชอบจะเป็นฝ่ายการตลาด และฝ่ายขายที่ตนเองสังกัด</p>
                    <?php
                    $w400SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE T0.Location IN (6,7,9) AND T0.WhsCode IS NOT NULL ORDER BY T0.WhsCode ASC";
                    $w400QRY = SAPSelect($w400SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w400RST = odbc_fetch_array($w400QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w400RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w400RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>
                    <hr/>
                    <!-- W500 -->
                    <h4 class="font-weight-bold">5. คลังอื่น ๆ</h4>
                    <p>คลังสินค้าประเภทนี้ไม่จัดอยู่ใน 4 ประเภทข้างต้น</p>
                    <?php
                    $w500SQL = "SELECT T0.WhsCode, T0.WhsName FROM OWHS T0 WHERE (T0.WhsCode NOT IN ('KB2','KSY','KSM','KBM','KB4','MT','MT2','TT-C','OUL','KB1','KB1.1') AND T0.Location NOT IN (2,6,7,9)) AND T0.WhsCode != 'W' ORDER BY T0.WhsCode ASC";
                    $w500QRY = SAPSelect($w500SQL);
                    echo "<table class='table table-bordered'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th width='15%'>รหัสคลังสินค้า</th>";
                            echo "<th>ชื่อคลังสินค้า</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($w500RST = odbc_fetch_array($w500QRY)) {
                        echo "<tr>";
                            echo "<td>".conutf8($w500RST['WhsCode'])."</td>";
                            echo "<td>".conutf8($w500RST['WhsName'])."</td>";
                        echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    ?>
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDataItem" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " id='ModalSize'>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search-plus" style='font-size: 15px;'></i> ข้อมูลสินค้าคงคลัง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name='ItemCode' id='ItemCode' >
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden'>
                                <tbody style='font-size: 13px;' id='Tbody1'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr class='m-2' style='color: rgba(244, 67, 54, 0.25);'>
                <div class="row pt-3">
                    <div class="col-lg">
                        <span class='fw-bolder pb-1'>จำนวนสินค้าคงคลังในระบบ SAP</span>
                        <div class="table-responsive pt-1" id='Table2'>
                        </div>
                    </div>
                </div>
                <hr class='m-2' style='color: rgba(244, 67, 54, 0.25);'>
                <div class="row pt-3">
                    <div class="col-lg">
                        <div class='fw-bolder pb-1 d-flex justify-content-between ' id='Chk_KB4'>
                            <div>โอนย้ายสินค้าคลังจอง</div>
                        </div>
                        <div class="table-responsive pt-1">
                            <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden'>
                                <thead style='font-size: 13px;'>
                                    <tr class='text-center'>
                                        <th>คลังสินค้า</th>
                                        <th>จำนวนปัจจุบัน</th>
                                        <th>เพิ่ม</th>
                                        <th>ลบ</th>
                                        <th>จำนวนใหม่</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='Table3'></tbody>
                            </table>

                            <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden' id='Table4'>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAlert" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="ModalAlert-head"></h5>
                <p id="ModalAlert-body" class="my-4"></p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
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
        Search();
	});
    try{ document.createEvent("TouchEvent"); var isMobile = true; }
    catch(e){ var isMobile = false; }

    $("#Define").on("click", function() {
        $("#ModalDefine").modal("show");
    })

    function ShowDataTable() {
        setTimeout(function(){
            switch(isMobile) {
                case true: var PageLength = 5; break;
                case false: var PageLength = 15; break;
                default: var PageLength = 10; break;
            }
            $('#TableDATA').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "pageLength": PageLength,
                dom: 'frtip'
            });
        }, 1000);
    }

    function Search() {
        $(".overlay").show();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=Search",
            type: "POST",
            data: { whsgroup : $("#filt_whsgroup").val(),
                    status : $("#filt_status").val(),
                    zero : $("#filt_getzero").is(":checked"),
                    aging : $("#filt_aging").is(":checked"), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#TableDATA').DataTable().destroy();
                    $('#Thead, #Tbody, #Tfoot').empty();
                    $("#Thead").html(inval['Thead']);
                    $("#Tbody").html(inval['Tbody']);
                    $("#Tfoot").html(inval['Tfoot']);
                    ShowDataTable();
                })
                $(".overlay").hide();

                $(".Data-Item").on("click", function() {
                    var DataItem = $(this).attr('data-item');
                    // console.log(DataItem);
                    DataDetail(DataItem);
                })
            } 
        })
    }

    function Export() {
        var TEXT_H = "";
        switch($("#filt_whsgroup").val()) {
            case 'WALL': TEXT_H = "คลังสินค้าทั้งหมด"; break;
            
            case 'CMT1': TEXT_H = "โควต้า MT1"; break;
            case 'CMT2': TEXT_H = "โควต้า MT2"; break;
            case 'CTT2': TEXT_H = "โควต้า TT"; break;
            case 'COUL': TEXT_H = "โควต้าหน้าร้าน"; break;
            case 'CONL': TEXT_H = "โควต้าออนไลน์"; break;
        }
        // console.log(TEXT_H);
        $(".overlay").show();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock_Export.php?a=Export",
            type: "POST",
            data: { whsgroup : $("#filt_whsgroup").val(),
                    status : $("#filt_status").val(),
                    zero : $("#filt_getzero").is(":checked"),
                    aging : $("#filt_aging").is(":checked"),
                    TEXT_H : TEXT_H, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    if(inval['ExportStatus'] == 'SUCCESS') {
                        window.open("../../../FileExport/InStock/"+inval['FileName'],'_blank');
                    }
                })
                $(".overlay").hide();
            } 
        })
    }

    function DataDetail(DataItem) {
        switch(isMobile) {
            case true: var ModalSize = "modal-full"; break;
            case false: var ModalSize = "modal-xl"; break;
            default: var ModalSize = "modal-xl"; break;
        }
        $(".overlay").show();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=DataDetail",
            type: "POST",
            data: { ItemCode : DataItem, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#Tbody1").html(inval['output1']);
                    $("#Table2").html(inval['output2']);
                    $("#Table3").html(inval['output3']);
                    $("#Chk_KB4").html(inval['output3_kb4']);
                    $("#Table4").html(inval['output4']);

                    $("#ItemCode").val(inval['ItemCode']);

                    $("#ModalSize").addClass(ModalSize);
                    $("#ModalDataItem").modal("show");
                })
                $(".overlay").hide();
            }
        })
    }

    function CHKdata(x,y) {
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=CHKdata",
            type: "POST",
            data: { Fun : x,
                    CH : y,

                    Now_ALL : $('#Now_All').val(),
                    Add_ALL : $('#Add_All').val(),
                    Red_ALL : $('#Red_All').val(),
                    New_ALL : $('#New_All').val(), 

                    Now_TTC : $('#Now_TTC').val(), 
                    Add_TTC : $('#Add_TTC').val(), 
                    Red_TTC : $('#Red_TTC').val(), 
                    New_TTC : $('#New_TTC').val(), 

                    Now_MT1 : $('#Now_MT1').val(), 
                    Add_MT1 : $('#Add_MT1').val(), 
                    Red_MT1 : $('#Red_MT1').val(), 
                    New_MT1 : $('#New_MT1').val(), 

                    Now_MT2 : $('#Now_MT2').val(), 
                    Add_MT2 : $('#Add_MT2').val(), 
                    Red_MT2 : $('#Red_MT2').val(), 
                    New_MT2 : $('#New_MT2').val(), 

                    Now_OUL : $('#Now_OUL').val(), 
                    Add_OUL : $('#Add_OUL').val(), 
                    Red_OUL : $('#Red_OUL').val(), 
                    New_OUL : $('#New_OUL').val(), 

                    Now_ONL : $('#Now_ONL').val(), 
                    Add_ONL : $('#Add_ONL').val(), 
                    Red_ONL : $('#Red_ONL').val(), 
                    New_ONL : $('#New_ONL').val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#Add_'+inval['CH']).val(inval['Add']);
                    $('#Red_'+inval['CH']).val(inval['Red']);
                    $('#New_'+inval['CH']).val(inval['New']);

                    $('#Final_Add').html(inval['TotalAdd']);
                    $('#Final_Red').html(inval['TotalRed']);
                    $('#Final_New').html(inval['TotalNew']);
                })
            } 
        })
    }

    function SaveApp(x) {
        // console.log(x);
        let WhsCode = $("#WhsCaseKB4").val();
        let SaleTime = $("#SaleTime").val();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=SaveApp",
            type: "POST",
            data: { Pos : x,
                    ItemCode : $("#ItemCode").val(),
                    App : $('#Mgr'+x+'App').val(),
                    Remark : $('#Mgr'+x+'Remark').val(),
                    WhsCode : WhsCode,
                    SaleTime : SaleTime,

                    Now_ALL : $('#Now_All').val(),
                    Add_ALL : $('#Add_All').val(),
                    Red_ALL : $('#Red_All').val(),
                    New_ALL : $('#New_All').val(), 

                    Now_TTC : $('#Now_TTC').val(), 
                    Add_TTC : $('#Add_TTC').val(), 
                    Red_TTC : $('#Red_TTC').val(), 
                    New_TTC : $('#New_TTC').val(), 

                    Now_MT1 : $('#Now_MT1').val(), 
                    Add_MT1 : $('#Add_MT1').val(), 
                    Red_MT1 : $('#Red_MT1').val(), 
                    New_MT1 : $('#New_MT1').val(), 

                    Now_MT2 : $('#Now_MT2').val(), 
                    Add_MT2 : $('#Add_MT2').val(), 
                    Red_MT2 : $('#Red_MT2').val(), 
                    New_MT2 : $('#New_MT2').val(), 

                    Now_OUL : $('#Now_OUL').val(), 
                    Add_OUL : $('#Add_OUL').val(), 
                    Red_OUL : $('#Red_OUL').val(), 
                    New_OUL : $('#New_OUL').val(), 

                    Now_ONL : $('#Now_ONL').val(), 
                    Add_ONL : $('#Add_ONL').val(), 
                    Red_ONL : $('#Red_ONL').val(), 
                    New_ONL : $('#New_ONL').val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#ModalAlert-head").html(inval['Halert']);
                    $("#ModalAlert-body").html(inval['alert']);
                    $("#ModalAlert").modal("show");
                    DataDetail($("#ItemCode").val());
                })
            } 
        })
    }

    function WhsCaseKB4(ItemCode) {
        const WhsCode = $("#WhsCaseKB4").val();
        // console.log(ItemCode, WhsCode);
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=WhsCaseKB4",
            type: "POST",
            data: { ItemCode : ItemCode, WhsCode : WhsCode, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#Table3").html(inval['output3']);
                })
            }
        })
    }

    function Cancel(DocNum) {
        $("#confirm_modal").modal("show");
        $("#confirm_modal p.defult").html("คุณต้องการยกเลิกการโอนย้ายสินค้า ?");
        $(document).off("click","#btn-Cancel").on("click","#btn-confirm", function() {
            $("#confirm_modal").modal("hide");
            $.ajax({
                url: "menus/warehouse/ajax/ajaxinstock.php?a=Cancel",
                type: "POST",
                data : { DocNum: DocNum },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#alert_header").html("<i class=\"fas fa-check-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
                        $("#alert_body").html("ยกเลิกการโอนย้ายสินค้าสำเร็จ");
                        $("#alert_modal").modal('show');
                    });
                }
            });
        });
    }
</script>
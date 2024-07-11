<div class="tab-pane fade" id="warehouse-report" role="tabpanel" aria-labelledby="warehouse-report-tab">
    <div class="row">
        <div class="col-lg">
            <p style="font-size: 18px;">
                <i class="fas fa-coins text-primary"></i> รวมมูลค่าสินค้าคงคลังทั้งหมด 
                <span class="text-primary" id='G0'>รวม VAT : 188,170,827.49 บาท</span>
                <?php 
                    if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP004") {
                        echo " / <span class='text-primary' id='G0N'></span>";
                    }
                ?>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg">
            <p><button class="btn btn-sm text-start text-primary fw-bold" style="font-size: 16px;" type="button" data-bs-toggle="collapse" data-bs-target="#CollaT1" aria-expanded="false" aria-controls="CollaT1">
                ข้อมูลคลังสินค้าส่วนกลาง <i class="fas fa-chevron-circle-down"></i>
            </button></p>
            <div class="collapse" id="CollaT1">
                <div class="ps-3 pe-3 mb-2">
                    <div class="row">
                        <div class="col-lg">
                            <p>คลังสินค้าส่วนกลางมูลค่า 
                                <span class="text-primary" id='G1'>รวม VAT : ... บาท</span>
                                <?php 
                                    if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP004") {
                                        echo " / <span class='text-primary' id='G1N'></span>";
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                            $Hname = ["สินค้าพร้อมขาย","สินค้ามือสอง"];
                            $Data = ["invnt_WG1","invnt_WG2"];
                            $DataWG = ["WG01","WG02"];
                            for ($i = 0; $i < count($Hname); $i++) {
                                echo"<div class='col-lg-4'>".
                                        "<div class='table-responsive'>".
                                            "<table class='table table-bordered rounded rounded-3 overflow-hidden' style='width:100%'>".
                                                "<thead style='background-color: rgba(155, 0, 0, 0.04);'>".
                                                    "<tr>".
                                                        "<th class='text-primary'><i class='fas fa-warehouse'></i> ".$Hname[$i]."</th>".
                                                    "</tr>".
                                                "</thead>".
                                                "<tbody>".
                                                    "<tr>".
                                                        "<td class='text-right'>".
                                                            "<p class='m-0 pt-4 pb-4' id='".$Data[$i]."'>... ล้านบาท</p>".
                                                            "<p class='m-0' style='font-size: 14px;'><a href='javascript:void(0);' class='WG' data-wg='".$DataWG[$i]."'>ข้อมูลเพิ่มเติม</a></p>".
                                                        "</td>".
                                                    "</tr>".
                                                "</tbody>".
                                            "</table>".
                                        "</div>".
                                    "</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <p><button class="btn btn-sm text-start text-primary fw-bold" style="font-size: 16px;" type="button" data-bs-toggle="collapse" data-bs-target="#CollaT2" aria-expanded="false" aria-controls="CollaT2">
                ข้อมูลคลังสินค้าสำหรับฝ่ายขาย <i class="fas fa-chevron-circle-down"></i>
            </button></p>
            <div class="collapse" id="CollaT2">
                <div class="ps-3 pe-3 mb-2">
                    <div class="row">
                        <div class="col-lg">
                            <p>คลังสินค้าสำหรับฝ่ายขายมูลค่า 
                                <span class="text-primary" id='G2'>รวม VAT : ... บาท</span>
                                <?php 
                                    if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP004") {
                                        echo " / <span class='text-primary' id='G2N'></span>";
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <?php 
                            $Hname = ["ฝ่ายขาย MT1","ฝ่ายขาย MT2","ฝ่ายขาย TT","ฝ่ายขายหน้าร้าน/ออนไลน์","ฝ่ายขายโรงงานฯ"];
                            $Data = ["invnt_WG3","invnt_WG4","invnt_WG5","invnt_WG6","invnt_WG7"];
                            $DataWG = ["WG03","WG04","WG05","WG06","WG07"];
                            for ($i = 0; $i < count($Hname); $i++) {
                                echo"<div class='col-lg-4'>".
                                        "<div class='table-responsive'>".
                                            "<table class='table table-bordered rounded rounded-3 overflow-hidden' style='width:100%'>".
                                                "<thead style='background-color: rgba(155, 0, 0, 0.04);'>".
                                                    "<tr>".
                                                        "<th class='text-primary'><i class='fas fa-warehouse'></i> ".$Hname[$i]."</th>".
                                                    "</tr>".
                                                "</thead>".
                                                "<tbody>".
                                                    "<tr>".
                                                        "<td class='text-right'>".
                                                            "<p class='m-0 pt-4 pb-4' id='".$Data[$i]."'>... ล้านบาท</p>".
                                                            "<p class='m-0' style='font-size: 14px;'><a href='javascript:void(0);' class='WG' data-wg='".$DataWG[$i]."'>ข้อมูลเพิ่มเติม</a></p>".
                                                        "</td>".
                                                    "</tr>".
                                                "</tbody>".
                                            "</table>".
                                        "</div>".
                                    "</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <p><button class="btn btn-sm text-start text-primary fw-bold" style="font-size: 16px;" type="button" data-bs-toggle="collapse" data-bs-target="#CollaT3" aria-expanded="false" aria-controls="CollaT3">
                ข้อมูลคลังสินค้าสำหรับงานซ่อม งานเคลม งาน QC <i class="fas fa-chevron-circle-down"></i>
            </button></p>
            <div class="collapse" id="CollaT3">
                <div class="ps-3 pe-3 mb-2">
                    <div class="row">
                        <div class="col-lg">
                            <p>คลังสินค้าสำหรับงานซ่อม เคลม QC มูลค่า 
                                <span class="text-primary" id='G3'>รวม VAT : ... บาท</span>
                                <?php 
                                    if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP004") {
                                        echo " / <span class='text-primary' id='G3N'></span>";
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <?php 
                            $Hname = ["สำหรับรอซ่อม ตรวจสอบ QC","สำหรับการเคลม"];
                            $Data = ["invnt_WG8","invnt_WG9"];
                            $DataWG = ["WG08","WG09"];
                            for ($i = 0; $i < count($Hname); $i++) {
                                echo"<div class='col-lg-4'>".
                                        "<div class='table-responsive'>".
                                            "<table class='table table-bordered rounded rounded-3 overflow-hidden' style='width:100%'>".
                                                "<thead style='background-color: rgba(155, 0, 0, 0.04);'>".
                                                    "<tr>".
                                                        "<th class='text-primary'><i class='fas fa-warehouse'></i> ".$Hname[$i]."</th>".
                                                    "</tr>".
                                                "</thead>".
                                                "<tbody>".
                                                    "<tr>".
                                                        "<td class='text-right'>".
                                                            "<p class='m-0 pt-4 pb-4' id='".$Data[$i]."'>... ล้านบาท</p>".
                                                            "<p class='m-0' style='font-size: 14px;'><a href='javascript:void(0);' class='WG' data-wg='".$DataWG[$i]."'>ข้อมูลเพิ่มเติม</a></p>".
                                                        "</td>".
                                                    "</tr>".
                                                "</tbody>".
                                            "</table>".
                                        "</div>".
                                    "</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <p><button class="btn btn-sm text-start text-primary fw-bold" style="font-size: 16px;" type="button" data-bs-toggle="collapse" data-bs-target="#CollaT4" aria-expanded="false" aria-controls="CollaT4">
                ข้อมูลคลังสินค้าสำหรับงานบัญชี <i class="fas fa-chevron-circle-down"></i>
            </button></p>
            <div class="collapse" id="CollaT4">
                <div class="ps-3 pe-3 mb-2">
                    <div class="row">
                        <div class="col-lg">
                            <p>คลังสินค้าสำหรับงานบัญชีมูลค่า 
                                <span class="text-primary" id='G4'>รวม VAT : ... บาท</span>
                                <?php 
                                    if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP004") {
                                        echo " / <span class='text-primary' id='G4N'></span>";
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <?php 
                            $Hname = ["สำหรับงานคืนลอย","สำหรับทิ้งซาก"];
                            $Data = ["invnt_WG10","invnt_WG11"];
                            $DataWG = ["WG10","WG11"];
                            for ($i = 0; $i < count($Hname); $i++) {
                                echo"<div class='col-lg-4'>".
                                        "<div class='table-responsive'>".
                                            "<table class='table table-bordered rounded rounded-3 overflow-hidden' style='width:100%'>".
                                                "<thead style='background-color: rgba(155, 0, 0, 0.04);'>".
                                                    "<tr>".
                                                        "<th class='text-primary'><i class='fas fa-warehouse'></i> ".$Hname[$i]."</th>".
                                                    "</tr>".
                                                "</thead>".
                                                "<tbody>".
                                                    "<tr>".
                                                        "<td class='text-right'>".
                                                            "<p class='m-0 pt-4 pb-4' id='".$Data[$i]."'>... ล้านบาท</p>".
                                                            "<p class='m-0' style='font-size: 14px;'><a href='javascript:void(0);' class='WG' data-wg='".$DataWG[$i]."'>ข้อมูลเพิ่มเติม</a></p>".
                                                        "</td>".
                                                    "</tr>".
                                                "</tbody>".
                                            "</table>".
                                        "</div>".
                                    "</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <p><button class="btn btn-sm text-start text-primary fw-bold" style="font-size: 16px;" type="button" data-bs-toggle="collapse" data-bs-target="#CollaT5" aria-expanded="false" aria-controls="CollaT5">
                ข้อมูลคลังสินค้าสำหรับงานอื่น ๆ <i class="fas fa-chevron-circle-down"></i>
            </button></p>
            <div class="collapse" id="CollaT5">
                <div class="ps-3 pe-3 mb-2">
                    <div class="row">
                        <div class="col-lg">
                            <p>คลังสินค้าสำหรับงานอื่น ๆ มูลค่า  
                                <span class="text-primary" id='G5'>รวม VAT : ... บาท</span>
                                <?php 
                                    if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP001" || $_SESSION['DeptCode'] == "DP004") {
                                        echo " / <span class='text-primary' id='G5N'></span>";
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <?php 
                            $Hname = ["สำหรับงานอื่น ๆ"];
                            $Data = ["invnt_WG12"];
                            $DataWG = ["WG12"];
                            for ($i = 0; $i < count($Hname); $i++) {
                                echo"<div class='col-lg-4'>".
                                        "<div class='table-responsive'>".
                                            "<table class='table table-bordered rounded rounded-3 overflow-hidden' style='width:100%'>".
                                                "<thead style='background-color: rgba(155, 0, 0, 0.04);'>".
                                                    "<tr>".
                                                        "<th class='text-primary'><i class='fas fa-warehouse'></i> ".$Hname[$i]."</th>".
                                                    "</tr>".
                                                "</thead>".
                                                "<tbody>".
                                                    "<tr>".
                                                        "<td class='text-right'>".
                                                            "<p class='m-0 pt-4 pb-4' id='".$Data[$i]."'>... ล้านบาท</p>".
                                                            "<p class='m-0' style='font-size: 14px;'><a href='javascript:void(0);' class='WG' data-wg='".$DataWG[$i]."'>ข้อมูลเพิ่มเติม</a></p>".
                                                        "</td>".
                                                    "</tr>".
                                                "</tbody>".
                                            "</table>".
                                        "</div>".
                                    "</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
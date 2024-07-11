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
            height: 630px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
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
                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label for="filt_card">เลือกซัพพลายเออร์</label>
                            <select name="filt_card" id="filt_card" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="NULL" selected disabled>กรุณาเลือก</option>
                            <?php
                                $SQL1 = "SELECT T0.CardCode, T0.CardName FROM OCRD T0 WHERE T0.CardCode LIKE 'V-%' ORDER BY T0.GroupCode, T0.CardCode ASC";
                                $QRY1 = SAPSelect($SQL1);
                                while($RST1 = odbc_fetch_array($QRY1)) {
                                    echo "<option value='".$RST1['CardCode']."'>".conutf8($RST1['CardCode']." - ".$RST1['CardName'])."</option>";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1 col-4">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" id="btn_search" onclick="SearchBox(1);"><i class="fas fa-search fa-fw fa-1x"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-1 col-4">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm w-100" id="btn_search" onclick="ExportDoc();"><i class="fas fa-file-excel fa-fw fa-1x"></i> Excel</button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <!-- CONTENT TAB -->
                        <ul class="nav nav-tabs mt-4" id="main-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#report_table" class="btn-tabs nav-link active" id="report_tab1" data-bs-toggle="tab" data-tab="1" onclick="SearchBox(1);" aria-controls="order_status" aria-selected="true">
                                    1. ข้อมูลการสั่งซื้อสินค้า
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#report_table" class="btn-tabs nav-link" id="report_tab2" data-bs-toggle="tab" data-tab="2" onclick="SearchBox(2);" aria-controls="order_status" aria-selected="false">
                                    2. ยอดขายสินค้า และสินค้าคงคลัง
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#report_table" class="btn-tabs nav-link" id="report_tab3" data-bs-toggle="tab" data-tab="3" onclick="SearchBox(3);" aria-controls="order_status" aria-selected="false">
                                    3. วิเคราะห์การขายสินค้าปี <?php echo date("Y")-1; ?>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#report_table" class="btn-tabs nav-link" id="report_tab4" data-bs-toggle="tab" data-tab="4" onclick="SearchBox(4);" aria-controls="order_status" aria-selected="false">
                                    4. ข้อมูลการเคลมสินค้าปี <?php echo date("Y")-1; ?>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#report_table" class="btn-tabs nav-link" id="report_tab5" data-bs-toggle="tab" data-tab="5" onclick="SearchBox(5);" aria-controls="order_status" aria-selected="false">
                                    5. วิเคราะห์การขายสินค้าปี <?php echo date("Y"); ?>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#report_table" class="btn-tabs nav-link" id="report_tab6" data-bs-toggle="tab" data-tab="6" onclick="SearchBox(6);" aria-controls="order_status" aria-selected="false">
                                    6. ข้อมูลการเคลมสินค้าปี <?php echo date("Y"); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-7 col-lg-9 col-xl-10">
                        <button type="button" class="btn btn-info btn-sm" onclick="ShowHelp();"><i class="fas fa-info-circle fa-fw fa-1x"></i> คำอธิบาย</button>
                    </div>
                    <div class="col-sm-5 col-lg-3 col-xl-2">
                        <input type="text" class='form-control form-control-sm' name='FilterBox' id='FilterBox' placeholder="ค้นหาจากรหัสสินค้า หรือชื่อสินค้า">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="table-responsive tableFix">
                            <table id="ItemList" class="table table-bordered table-hover table-sm" style="font-size: 12px;">
                                <thead class="text-center bg-light"></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SHOW RETURN QC -->
<div class="modal fade" id="ShowQC" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ShowQCLabel"><i class="far fa-file-alt fa-fw fa-lg"></i> ข้อมูลการคืน QC <span id="Qc_ItemName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Clase"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive TableFix">
                    <table id="ReturnTB" class="table table-bordered table-sm table-hover" style="font-size: 12px;">
                        <thead class="text-center bg-light">
                            <tr>
                                <th width="3.5%">No</th>
                                <th width="7.5%">ทีมขาย</th>
                                <th width="7.5%">เลขที่เอกสาร</th>
                                <th width="7.5%">วันที่เอกสาร</th>
                                <th width="25%">ชื่อลูกค้า</th>
                                <th width="3.5%">คลัง</th>
                                <th width="5%">จำนวน</th>
                                <th width="5%">หน่วย</th>
                                <th width="10%">รายละเอียดการคืน</th>
                                <th>เหตุผลการคืน</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SHOW HELP -->
<div class="modal fade" id="ShowHelp" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ShowHelpLabel"><i class="fas fa-info-circle fa-fw fa-lg"></i> คำอธิบาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>รายงานการประเมินซัพพลายเออร์</h5>
                <p>
                    รายงานนี้เป็นการประเมินซัพพลายเออร์ประจำปี ด้วยการนำข้อมูลในอดีตมาประมวลผลเพื่อประกอบการตัดสินใจ เจรจากับทางซัพพลายเออร์ ในหน้าต่างนี้จะแสดงคำอธิบายถึงที่มา (สูตร) ในหัวข้อต่าง ๆ โดยขอแบ่งเป็น 5 หัวข้อได้แก่
                    <ol>
                        <li>ข้อมูลการสั่งซื้อสินค้า</li>
                        <li>ยอดขายสินค้า และสินค้าคงคลัง</li>
                        <li>วิเคราะห์การขายสินค้าปี <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></li>
                        <li>ข้อมูลการเคลมสินค้าปี <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></li>
                        <li>ข้อมูลการเคลมสินค้าปี <i class="text-muted">[ปีปัจจุบัน]</i></li>
                    </ol>
                </p>
                <p class="text-center"><small class="text-danger">*** รายงานนี้เป็นการดึงข้อมูลมาจากระบบ และสูตรจากทางผู้บริหาร ***</small></p>
                <hr/>
                <h6>1. ข้อมูลการสั่งซื้อสินค้า</h6>
                <p>แสดงผลประวัติการสั่งซื้อสินค้าช่วง 2 ปีย้อนหลัง และประมาณการสั่งซื้อสินค้าในปีปัจจุบัน</p>
                <!-- TABLE 1 -->
                <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="TB_Help1">
                    <thead class="text-center bg-light">
                        <tr>
                            <th width="3%">ลำดับ</th>
                            <th width="12.5%">หัวข้อหลัก</th>
                            <th width="12.5%">หัวข้อรอง</th>
                            <th width="7.5%">หน่วยการแสดงผล</th>
                            <th>รายละเอียด / สูตรการคำนวณ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ยอดซื้อ สองปีก่อนหน้า -->
                        <tr>
                            <td class="text-right">1</td>
                            <td rowspan="2" class="align-top">ยอดซื้อ <i class="text-muted">[สองปีก่อนหน้า]</i></td>
                            <td>จำนวน (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการรับเข้ามา และบันทึกเข้าในโมดูล Goods Receipt P/O ในระบบ SAP เมื่อสองปีก่อนหน้า</td>
                        </tr>
                        <tr>
                            <td class="text-right">2</td>
                            <td>มูลค่า (THB)</td>
                            <td class="text-center">บาท</td>
                            <td>มูลค่าสินค้าที่มีการรับเข้ามา และบันทึกเข้าในโมดูล Goods Receipt P/O ในระบบ SAP เมื่อสองปีก่อนหน้า</td>
                        </tr>
                        <!-- ยอดซื้อ หนึ่งปีก่อนหน้า -->
                        <tr>
                            <td class="text-right">3</td>
                            <td rowspan="2" class="align-top">ยอดซื้อ <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></td>
                            <td>จำนวน (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการรับเข้ามา และบันทึกเข้าในโมดูล Goods Receipt P/O ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า</td>
                        </tr>
                        <tr>
                            <td class="text-right">4</td>
                            <td>มูลค่า (THB)</td>
                            <td class="text-center">บาท</td>
                            <td>มูลค่าสินค้าที่มีการรับเข้ามา และบันทึกเข้าในโมดูล Goods Receipt P/O ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า</td>
                        </tr>
                        <!-- ยอดซื้อ ปีปัจจุบัน -->
                        <tr>
                            <td class="text-right">5</td>
                            <td rowspan="2" class="align-top">ยอดซื้อ <i class="text-muted">[ปีปัจจุบัน]</i></td>
                            <td>จำนวน (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการรับเข้ามา และบันทึกเข้าในโมดูล Goods Receipt P/O ในระบบ SAP ในปีปัจจุบัน</td>
                        </tr>
                        <tr>
                            <td class="text-right">6</td>
                            <td>มูลค่า (THB)</td>
                            <td class="text-center">บาท</td>
                            <td>มูลค่าสินค้าที่มีการรับเข้ามา และบันทึกเข้าในโมดูล Goods Receipt P/O ในระบบ SAP ในปีปัจจุบัน</td>
                        </tr>
                        <!-- ประมาณการสั่งซื้อ ปีปัจจุบัน -->
                        <tr>
                            <td class="text-right">7</td>
                            <td rowspan="3" class="align-top">ประมาณการสั่งซื้อ <i class="text-muted">[ปีปัจจุบัน]</i></td>
                            <td>จำนวน (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>(<span class='badge bg-light-info'>ผลลัพธ์จากข้อ 10</span> &div; <span class="badge bg-light-secondary text-danger">12</span>) &times; <span class="badge bg-light-secondary text-danger">16</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">8</td>
                            <td>มูลค่า (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class='badge bg-light-warning'>ผลลัพธ์จากข้อ 7</span> &times; (<span class="badge bg-light-secondary text-danger">ต้นทุนรับเข้าล่าสุด</span> &times; <span class="badge bg-light-secondary text-danger">1.07</span>)</td>
                        </tr>
                        <tr>
                            <td class="text-right">9</td>
                            <td>Growth (%)</td>
                            <td class="text-center">เปอร์เซ็นต์ (%)</td>
                            <td>[(<span class='badge bg-light-warning'>ผลลัพธ์จากข้อ 7</span> - <span class='badge bg-light-success'>ผลลัพธ์จากข้อ 3</span>) &div; <span class='badge bg-light-warning'>ผลลัพธ์จากข้อ 7</span>] &times; <span class="badge bg-light-secondary text-danger">100</span></td>
                        </tr>
                    </tbody>
                </table>
                <hr/>
                <h6>2. ยอดขายสินค้า และสินค้าคงคลัง</h6>
                <p>แสดงผลยอดขายสินค้า ในช่วงหนึ่งปีก่อนหน้า ปีปัจจุบัน และแสดงผลจำนวนสินค้าคงคลัง มูลค่า และอัตราหมุนเวียน (Turn Over: T/O)</p>
                <!-- TABLE 2 -->
                <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="TB_Help2">
                    <thead class="text-center bg-light">
                        <tr>
                            <th width="3%">ลำดับ</th>
                            <th width="12.5%">หัวข้อหลัก</th>
                            <th width="12.5%">หัวข้อรอง</th>
                            <th width="7.5%">หน่วยการแสดงผล</th>
                            <th>รายละเอียด / สูตรการคำนวณ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ยอดขาย หนึ่งปีก่อนหน้า -->
                        <tr>
                            <td class="text-right">10</td>
                            <td rowspan="2" class="align-top">ยอดขาย <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></td>
                            <td>รวมทั้งหมด (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการขายหรือลดหนี้ และบันทึกเข้าในโมดูล A/R Invoice หรือ A/R Credit Memo ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า</td>
                        </tr>
                        <tr>
                            <td class="text-right">11</td>
                            <td>เฉลี่ยต่อเดือน (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td><span class='badge bg-light-info'>ผลลัพธ์จากข้อ 10</span> &div; <span class="badge bg-light-secondary text-danger">12</span></td>
                        </tr>
                        <!-- ยอดขาย ปีปัจจุบัน -->
                        <tr>
                            <td class="text-right">12</td>
                            <td rowspan="2" class="align-top">ยอดขาย <i class="text-muted">[ปีปัจจุบัน]</i></td>
                            <td>รวมทั้งหมด (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการขายหรือลดหนี้ และบันทึกเข้าในโมดูล A/R Invoice หรือ A/R Credit Memo ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า</td>
                        </tr>
                        <tr>
                            <td class="text-right">13</td>
                            <td>เฉลี่ยต่อเดือน (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td><span class='badge bg-light-danger'>ผลลัพธ์จากข้อ 12</span> &div; <span class="badge bg-light-secondary text-danger">จำนวนเดือนตั้งแต่เดือนมกราคมถึงเดือนปัจจุบัน</span></td>
                        </tr>
                        <!-- สินค้าคงคลังปัจจุบัน -->
                        <tr>
                            <td class="text-right">14</td>
                            <td rowspan="4" class="align-top">สินค้าคงคลัง <i class="text-muted">[วันที่ปัจจุบัน]</i></td>
                            <td>จำนวนคงคลัง (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าคงคลังในระบบ SAP (KSY, KSM, MT, MT2, TT-C, OUL, KB4, PLA)</td>
                        </tr>
                        <tr>
                            <td class="text-right">15</td>
                            <td>มูลค่าคงคลัง (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class="badge bg-light-success">ผลลัพธ์จากข้อ 14</span> &times; (<span class="badge bg-light-secondary text-danger">ต้นทุนรับเข้าล่าสุด</span> &times; <span class="badge bg-light-secondary text-danger">1.07</span>)</td>
                        </tr>
                        <tr>
                            <td class="text-right">16</td>
                            <td>ยอดขายเฉลี่ย 12 เดือน (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>ยอดขายย้อนหลัง 12 เดือนล่าสุด (เฉลี่ยต่อเดือน) ที่มีการขายหรือลดหนี้ และบันทึกเข้าในโมดูล A/R Invoice หรือ A/R Credit Memo ในระบบ SAP
                        </tr>
                        <!-- TOV -->
                        <tr>
                            <td class="text-right">17</td>
                            <td>T/O (เดือน)</td>
                            <td class="text-center">เดือน</td>
                            <td><span class="badge bg-light-success">ผลลัพธ์จากข้อ 14</span> &div; <span class="badge bg-light-warning">ผลลัพธ์จากข้อ 16</span></td>
                        </tr>
                    </tbody>
                </table>
                <hr/>
                <h6>3. วิเคราะห์การขายสินค้าปี <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></h6>
                <p>วิเคราะห์ต้นทุน ยอดขาย และกำไรในช่วงหนึ่งปีก่อนหน้า</p>
                <!-- TABLE 3 -->
                <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="TB_Help3">
                    <thead class="text-center bg-light">
                        <tr>
                            <th width="3%">ลำดับ</th>
                            <th width="12.5%">หัวข้อหลัก</th>
                            <th width="12.5%">หัวข้อรอง</th>
                            <th width="7.5%">หน่วยการแสดงผล</th>
                            <th>รายละเอียด / สูตรการคำนวณ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- วิเคราะห์การขาย หนึ่งปีก่อนหน้า -->
                        <tr>
                            <td class="text-right">18</td>
                            <td rowspan="7" class="align-top">วิเคราะห์การขาย <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></td>
                            <td>ต้นทุนขายรวม (VAT) (THB)</td>
                            <td class="text-center">บาท</td>
                            <td>มูลค่าต้นทุนรวมที่มีการขายหรือลดหนี้ และบันทึกเข้าในโมดูล A/R Invoice หรือ A/R Credit Memo ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า</td>
                        </tr>
                        <tr>
                            <td class="text-right">19</td>
                            <td>ต้นทุนขายเฉลี่ย/ตัว (VAT) (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class='badge bg-light-info'>ผลลัพธ์จากข้อ 18</span> &div; <span class="badge bg-light-secondary text-danger">จำนวนที่ขายไปเมื่อหนึ่งปีก่อนหน้า</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">20</td>
                            <td>ราคาขายรวม (VAT) (THB)</td>
                            <td class="text-center">บาท</td>
                            <td>มูลค่ายอดขายรวมที่มีการขายหรือลดหนี้ และบันทึกเข้าในโมดูล A/R Invoice หรือ A/R Credit Memo ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า</td>
                        </tr>
                        <tr>
                            <td class="text-right">21</td>
                            <td>ราคาขายเฉลี่ย/ตัว (VAT) (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class='badge bg-light-danger'>ผลลัพธ์จากข้อ 20</span> &div; <span class="badge bg-light-secondary text-danger">จำนวนที่ขายไปเมื่อหนึ่งปีก่อนหน้า</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">22</td>
                            <td>กำไรรวม (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class='badge bg-light-danger'>ผลลัพธ์จากข้อ 20</span> - <span class='badge bg-light-info'>ผลลัพธ์จากข้อ 17</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">23</td>
                            <td>กำไรเฉลี่ย/ตัว (VAT) (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class='badge bg-light-success'>ผลลัพธ์จากข้อ 22</span> &div; <span class="badge bg-light-secondary text-danger">จำนวนที่ขายไปเมื่อหนึ่งปีก่อนหน้า</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">24</td>
                            <td>% of GP</td>
                            <td class="text-center">เปอร์เซ็นต์ (%)</td>
                            <td>(<span class='badge bg-light-success'>ผลลัพธ์จากข้อ 22</span> &div; <span class='badge bg-light-danger'>ผลลัพธ์จากข้อ 20</span>) &times; <span class="badge bg-light-secondary text-danger">100</span></td>
                        </tr>
                    </tbody>
                </table>
                <hr/>
                <h6>4. ข้อมูลการเคลมสินค้าปี <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></h6>
                <p>แสดงข้อมูลการเคลมในช่วงหนึ่งปีก่อนหน้า</p>
                <!-- TABLE 4 -->
                <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="TB_Help4">
                    <thead class="text-center bg-light">
                        <tr>
                            <th width="3%">ลำดับ</th>
                            <th width="12.5%">หัวข้อหลัก</th>
                            <th width="12.5%">หัวข้อรอง</th>
                            <th width="7.5%">หน่วยการแสดงผล</th>
                            <th>รายละเอียด / สูตรการคำนวณ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- วิเคราะห์การขาย หนึ่งปีก่อนหน้า -->
                        <tr>
                            <td class="text-right">25</td>
                            <td rowspan="7" class="align-top">ข้อมูลการเคลมสินค้า <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></td>
                            <td>ยอดขาย <i class="text-muted">[หนึ่งปีก่อนหน้า]</i> (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการขายหรือลดหนี้ และบันทึกเข้าในโมดูล A/R Invoice หรือ A/R Credit Memo ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า</td>
                        </tr>
                        <tr>
                            <td class="text-right">26</td>
                            <td>คืนเพื่อเคลมซัพฯ <i class="text-muted">[หนึ่งปีก่อนหน้า]</i> (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการแจ้งคืนจากลูกค้าเพื่อเคลมซัพฯ และบันทึกเข้าในโมดูล Return ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า <span class="text-danger">(สามารถคลิกที่จำนวนเพื่อดูรายละเอียดการคืนจากลูกค้าได้)</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">27</td>
                            <td>เคลมซัพฯ แล้ว <i class="text-muted">[หนึ่งปีก่อนหน้า]</i> (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการเคลม และบันทึกเข้าในโมดูล Goods Return ในระบบ SAP เมื่อหนึ่งปีก่อนหน้า</td>
                        </tr>
                        <tr>
                            <td class="text-right">28</td>
                            <td>มูลค่าการเคลม <i class="text-muted">[หนึ่งปีก่อนหน้า]</i> (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class="badge bg-light-success">ผลลัพธ์จากข้อ 27</span> &times; (<span class="badge bg-light-secondary text-danger">ต้นทุนรับเข้าล่าสุด</span> &times; <span class="badge bg-light-secondary text-danger">1.07</span>)</td>
                        </tr>
                        <tr>
                            <td class="text-right">29</td>
                            <td>% การเคลม <i class="text-muted">[หนึ่งปีก่อนหน้า]</i></td>
                            <td class="text-center">เปอร์เซ็นต์ (%)</td>
                            <td>(<span class="badge bg-light-success">ผลลัพธ์จากข้อ 27</span> &div; <span class="badge bg-light-warning">ผลลัพธ์จากข้อ 25</span>) &times; <span class="badge bg-light-secondary text-danger">100</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">30</td>
                            <td>รอเคลมซัพฯ (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าคงคลัง WP4, WP5</td>
                        </tr>
                        <tr>
                            <td class="text-right">31</td>
                            <td>มูลค่ารอเคลมซัพฯ (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class="badge bg-light-info">ผลลัพธ์จากข้อ 30</span> &times; (<span class="badge bg-light-secondary text-danger">ต้นทุนรับเข้าล่าสุด</span> &times; <span class="badge bg-light-secondary text-danger">1.07</span>)</td>
                        </tr>
                    </tbody>
                </table>
                <hr/>
                <h6>6. ข้อมูลการเคลมสินค้าปี <i class="text-muted">[ปีปัจจุบัน]</i></h6>
                <p>แสดงข้อมูลการเคลมในช่วงหนึ่งปีปัจจุบัน</p>
                <!-- TABLE 4 -->
                <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="TB_Help4">
                    <thead class="text-center bg-light">
                        <tr>
                            <th width="3%">ลำดับ</th>
                            <th width="12.5%">หัวข้อหลัก</th>
                            <th width="12.5%">หัวข้อรอง</th>
                            <th width="7.5%">หน่วยการแสดงผล</th>
                            <th>รายละเอียด / สูตรการคำนวณ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- วิเคราะห์การขาย หนึ่งปีก่อนหน้า -->
                        <tr>
                            <td class="text-right">39</td>
                            <td rowspan="7" class="align-top">ข้อมูลการเคลมสินค้า <i class="text-muted">[ปีปัจจุบัน]</i></td>
                            <td>ยอดขาย <i class="text-muted">[ปีปัจจุบัน]</i> (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการขายหรือลดหนี้ และบันทึกเข้าในโมดูล A/R Invoice หรือ A/R Credit Memo ในระบบ SAP เมื่อหนึ่งปีปัจจุบัน</td>
                        </tr>
                        <tr>
                            <td class="text-right">40</td>
                            <td>คืนเพื่อเคลมซัพฯ <i class="text-muted">[ปีปัจจุบัน]</i> (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการแจ้งคืนจากลูกค้าเพื่อเคลมซัพฯ และบันทึกเข้าในโมดูล Return ในระบบ SAP เมื่อหนึ่งปีปัจจุบัน <span class="text-danger">(สามารถคลิกที่จำนวนเพื่อดูรายละเอียดการคืนจากลูกค้าได้)</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">41</td>
                            <td>เคลมซัพฯ แล้ว <i class="text-muted">[ปีปัจจุบัน]</i> (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าที่มีการเคลม และบันทึกเข้าในโมดูล Goods Return ในระบบ SAP เมื่อหนึ่งปีปัจจุบัน</td>
                        </tr>
                        <tr>
                            <td class="text-right">42</td>
                            <td>มูลค่าการเคลม <i class="text-muted">[ปีปัจจุบัน]</i> (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class="badge bg-light-success">ผลลัพธ์จากข้อ 41</span> &times; (<span class="badge bg-light-secondary text-danger">ต้นทุนรับเข้าล่าสุด</span> &times; <span class="badge bg-light-secondary text-danger">1.07</span>)</td>
                        </tr>
                        <tr>
                            <td class="text-right">43</td>
                            <td>% การเคลม <i class="text-muted">[ปีปัจจุบัน]</i></td>
                            <td class="text-center">เปอร์เซ็นต์ (%)</td>
                            <td>(<span class="badge bg-light-success">ผลลัพธ์จากข้อ 41</span> &div; <span class="badge bg-light-warning">ผลลัพธ์จากข้อ 40</span>) &times; <span class="badge bg-light-secondary text-danger">100</span></td>
                        </tr>
                        <tr>
                            <td class="text-right">44</td>
                            <td>รอเคลมซัพฯ (หน่วย)</td>
                            <td class="text-center">หน่วยสินค้า</td>
                            <td>จำนวนสินค้าคงคลัง WP4, WP5</td>
                        </tr>
                        <tr>
                            <td class="text-right">45</td>
                            <td>มูลค่ารอเคลมซัพฯ (THB)</td>
                            <td class="text-center">บาท</td>
                            <td><span class="badge bg-light-info">ผลลัพธ์จากข้อ 44</span> &times; (<span class="badge bg-light-secondary text-danger">ต้นทุนรับเข้าล่าสุด</span> &times; <span class="badge bg-light-secondary text-danger">1.07</span>)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalViewWhsSub" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-file-alt fa-fw fa-lg"></i> ข้อมูลรอเคลมซัพฯ รหัสสินค้า : <span></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Clase"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive TableFix">
                    <table id="TableViewWhsSub" class="table table-bordered table-sm table-hover" style="font-size: 12px;">
                        <thead class="text-center bg-light">
                            <tr>
                                <th width="2%">No</th>
                                <th width="6.5%">เลขที่เอกสาร</th>
                                <th width="6%">วันที่เอกสาร</th>
                                <th width="5%">รหัสลูกค้า</th>
                                <th width="20%">ชื่อลูกค้า</th>
                                <th width="15%">ชื่อพนักงานขาย</th>
                                <th width="3.5%">โอนจากคลัง</th>
                                <th width="3.5%">คลัง</th>
                                <th width="5%">จำนวน</th>
                                <th width="4%">หน่วย</th>
                                <th width="10%">สภาพสินค้า</th>
                                <th>สาเหตุการคืน</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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
function SearchBox(tab) {
    let CardCode = $("#filt_card").val();
    let Tab = tab
    if(CardCode == "" || CardCode == "NULL" || CardCode == null) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกซัพพลายเออร์");
        $("#alert_modal").modal('show');
    } else {
        $(".overlay").show();
        $.ajax({
            url: "menus/purchase/ajax/ajaxSurveySupplier.php?p=GetData",
            type: "POST",
            data: {
                c: CardCode,
                t: Tab
            },
            success: function(result) {
                $(".overlay").hide();
                
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    $("#ItemList thead").html(inval['THEAD']);
                    $("#ItemList tbody").html(inval['TBODY']);
                });
                if(Tab != 1) {
                    $(".nav-tabs a[data-tab='"+Tab+"']").tab("show");
                } else {
                    $(".nav-tabs a[data-tab='1']").tab("show");
                }

                $("#FilterBox").on("keyup", function(){
                    var kwd = $(this).val().toLowerCase();
                    $("#ItemList tbody tr").filter(function(){
                        $(this).toggle($(this).text().toLowerCase().indexOf(kwd) > -1)
                    });
                });
            }
        })
    }
}

function ExportDoc() {
    let CardCode = $("#filt_card").val();
    if(CardCode == "" || CardCode == "NULL" || CardCode == null) {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกซัพพลายเออร์");
        $("#alert_modal").modal('show');
    } else {
        $(".overlay").show();
        $.ajax({
            url: "menus/purchase/ajax/ajaxSurveySupplier.php?p=ExportDoc",
            type: "POST",
            data: {
                CardCode: CardCode
            },
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    window.open("../../FileExport/SurveySupplier/"+inval['FileName'],'_blank');
                });
                $(".overlay").hide();
            }
        })
    }
}

function ReturnQc(DocYear, ItemCode) {
    $(".overlay").show();
    $.ajax({
        url: "menus/purchase/ajax/ajaxSurveySupplier.php?p=ReturnQC",
        type: "POST",
        data: {
            DocYear: DocYear,
            ItemCode: ItemCode
        },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#Qc_ItemName").html(inval['ITEMNAME']);
                $("#ReturnTB tbody").html(inval['TBODY']);
            });
            $("#ShowQC").modal("show");
        }
    })
}

function ShowHelp() {
    $("#ShowHelp").modal("show");
}

function ViewWhsSub(ItemCode,Qty) {
    console.log(ItemCode, Qty);
    $("#ModalViewWhsSub .modal-title span").html(ItemCode);
    $.ajax({
        url: "menus/purchase/ajax/ajaxSurveySupplier.php?p=ViewWhsSub",
        type: "POST",
        data: { ItemCode: ItemCode, Qty: Qty },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#TableViewWhsSub tbody").html(inval['Data']);
                $("#ModalViewWhsSub").modal("show");
            });
        }
    })
}

$(document).ready(function(){
    CallHead();
});
</script> 
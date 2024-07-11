<style type="text/css">
    .tableFixHead {
        overflow-y: auto;
        height: 630px;
    }

    .text-green {
        color: #3C763C;
    }
    /* .tableFixHead th {
        position: sticky;
        top: 0;
        background: #fff;
    } */
    
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

    /* Nav Custom */
    .nav-tabsCus {
        border-bottom: 1px solid rgba(121, 48, 48, 0.46);
        /* border-bottom: 1px solid #dee2e6; */
    }

    .nav-tabsCus .nav-linkCus {
        margin-bottom: -1px;
        background: 0 0;
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        padding: 8px 16px 4px;
        color: #662F2F;
    }

    .nav-tabsCus .nav-linkCus:focus,
    .nav-tabsCus .nav-linkCus:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate
    }

    .nav-tabsCus .nav-itemCus.show .nav-linkCus,
    .nav-tabsCus .nav-linkCus.active {
        color: #9A1118;
        /* color: #495057; */
        background-color: #fff;
        border-color: rgba(121, 48, 48, 0.46) rgba(121, 48, 48, 0.46) #fff
        /* border-color: #dee2e6 #dee2e6 #fff */
    }
    /* END Nav Custom */
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
                        <!-- Menu Single Report -->
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link text-primary fw-bold active" id="sales-report-tab" data-tab-SR='SalesReport' data-bs-toggle="tab" data-bs-target="#sales-report" type="button" role="tab" aria-controls="sales-report" aria-selected="true">รายงานการขาย</button>
                                <button class="nav-link text-primary fw-bold" id="return-report-tab" data-tab-SR='ReturnReport' data-bs-toggle="tab" data-bs-target="#return-report" type="button" role="tab" aria-controls="return-report" aria-selected="false">รายงานการคืน</button>
                                <button class="nav-link text-primary fw-bold" id="warehouse-report-tab" data-tab-SR='WarehouseReport' data-bs-toggle="tab" data-bs-target="#warehouse-report" type="button" role="tab" aria-controls="warehouse-report" aria-selected="false">รายงานคลังสินค้า</button>
                                <button class="nav-link text-primary fw-bold" id="CMoney-report-tab" data-tab-SR='CMoneyReport' data-bs-toggle="tab" data-bs-target="#CMoney-report" type="button" role="tab" aria-controls="CMoney-report" aria-selected="false">รายงานการเก็บเงิน</button>
                            </div>
                        </nav>
                        <!-- Content Single Report -->
                        <div class="tab-content mt-3" id="nav-tabContent">
                            <!-- รายงานการขาย -->
                            <?php require("sales_report.php"); ?>

                            <!-- รายงานการคืน -->
                            <?php require("return_report.php"); ?>

                            <!-- รายงานคลังสินค้า -->
                            <?php require("warehouse_report.php"); ?>

                            <!-- รายงานการเก็บเงิน -->
                            <?php require("CMoney_report.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL รายงานการขาย-->
<div class="modal fade" id="ModalViewData" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id='ModalHeader'></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table id="ModalDataExport" class="table table-sm rounded rounded-3 overflow-hidden" style="width:100%">
                                <thead class='font-rps' id='ModalThead' style='background-color: rgba(155, 0, 0, 0.04);'>
                                    <tr>
                                        <th colspan='11' width='100%' class='text-primary text-center'></th>
                                    </tr>
                                    <tr>
                                        <th rowspan='2' width='5%' class='text-center align-bottom'>No.</th>
                                        <th rowspan='2' class='text-center align-bottom border-start'>ชื่อลูกค้า</th>
                                        <th rowspan='2' class='text-center align-bottom border-start'>ผู้แทนขาย</th>
                                        <th rowspan='2' class='text-center align-bottom border-start'>ยอดขายปี </th>
                                        <th rowspan='2' class='text-center align-bottom border-start'>ยอดขายปี </th>
                                        <th rowspan='2' class='text-center align-bottom border-start'>% การเติบโต<br></th>
                                        <th rowspan='2' class='text-center align-bottom border-start'>กำไรปี</th>
                                        <th colspan='4' width='30%' class='text-center align-bottom border-start'>ยอดขายปี </th>
                                    </tr>
                                    <tr>
                                        <th width='25%' class='text-center border-start'>ไตรมาส 1</th>
                                        <th width='25%' class='text-center border-start'>ไตรมาส 2</th>
                                        <th width='25%'class='text-center border-start'>ไตรมาส 3</th>
                                        <th width='25%' class='text-center border-start'>ไตรมาส 4</th>
                                    </tr>
                                </thead>
                                <tbody class='font-rps' id='ModalTbody'></tbody>
                                <tfoot class='font-rps' id='ModalTfoot'></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL -->

<!-- MODAL รายงานการคืน-->
<div class="modal fade" id="ModalViewDataRT" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id='HeadRTModal'></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table id="ModalDataExportRT" class="table table-sm rounded rounded-3 overflow-hidden" style="width:100%">
                                <thead class='font-rps' id='TheadRTModal' style='background-color: rgba(155, 0, 0, 0.04);'>
                                    <tr>
                                        <th colspan='8' class='text-primary text-center'>ข้อมูลการคืนทีม ...</th>
                                    </tr>
                                    <tr>
                                        <th width='3%' class='text-center align-bottom'>No.</th>
                                        <th width='17%' class='text-center align-bottom border-start'>สาเหตุการคืน</th>
                                        <th width='13%' class='text-center align-bottom border-start'>ผู้แทนขาย</th>
                                        <th width='10%' class='text-center align-bottom border-start'>วันที่ลดหนี้</th>
                                        <th width='10%' class='text-center align-bottom border-start'>เลขที่เอกสาร</th>
                                        <th width='10%' class='text-center align-bottom border-start'>เอกสารอ้างอิง</th>
                                        <th class='text-center align-bottom border-start'>ชื่อลูกค้า</th>
                                        <th width='8%' class='text-center align-bottom border-start'>มูลค่า</th>
                                    </tr>
                                </thead>
                                <tbody class='font-rps' id='TbodyRTModal'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
    <!-- MODAL รายงานการคืน รายละเอียดลดหนี้ -->
    <div class="modal fade" id="ModalViewDataRTDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id=''>รายละเอียดการลดหนี้</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="" class="table table-sm table-borderless rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps' class='font-rps' id='TheadMasterRTModalDetail'></thead>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <table id="" class="table table-sm rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps text-center' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <th width='4%' class='border-end'>No.</th>
                                        <th width='14%' class='border-end'>รหัสสินค้า</th>
                                        <th width='28%' class='border-end'>ชื่อสินค้า</th>
                                        <th width='12%' class='border-end'>คลัง</th>
                                        <th width='14%' class='border-end'>ราคาต่อหน่วย</th>
                                        <th width='12%' class='border-end'>จำนวน</th>
                                        <th width='14%'>มูลค่ารวม (ก่อน VAT)</th>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyRTModalDetail'></tbody>
                                    <tfoot class='font-rps' id='TfooterRTModalDetail' style='background-color: rgba(0, 0, 0, 0.04);'></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->
<!-- END MODAL -->

<!-- MODAL คลังสินค้า -->
<div class="modal fade" id="ModalWarehouse" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id='NameWarehouse'></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id='ModalCloseWH1'></button>
            </div>
            <div class="modal-body">
                <div class="row" id='Content-Warehouse'></div>
                <div class="row pt-4" id='Table-Content'>
                    <div class="col-lg">
                        <div class='d-flex align-items-center'>
                            <i class='fas fa-warehouse text-primary'></i>&nbsp;<span class='text-primary' id='H-Content'></span>&nbsp;
                            <i class="fas fa-angle-right"></i>&nbsp;<i class="fas fa-pallet"></i>&nbsp;ข้อมูลสินค้าคงคลัง 
                        </div>
                        <div class="d-flex justify-content-end pb-1">
                            <select class="me-1 form-select" style="width: 12.5rem;" name="SelectWSG" id="SelectWSG" onchange="SelectWSG()">
                                <option value="status" selected>แบ่งกลุ่มตามสถานะสินค้า</option>
                                <option value="aging">แบ่งกลุ่มตามอายุการจัดเก็บสินค้า (ใช้เวลาดึง 1-2 นาที)</option>
                                <option value="moving">ความเคลื่อนไหวคลังสินค้า</option>
                            </select>
                        </div>
                        <div class='table-responsive'>
                            <table class='table table-bordered rounded rounded-3 overflow-hidden table-hover ' style='width:100%'>
                                <thead class='font-rps' id='TheadWSG' style='background-color: rgba(155, 0, 0, 0.04);'></thead>
                                <tbody class='font-rps' id='TbodyWSG'></tbody>
                                <tfoot class='font-rps' id='TfootWSG'></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name='DataWSG' id='DataWSG'>
                <input type="hidden" name='DataName' id='DataName'>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
    <!-- MODAL คลังสินค้า รายละเอียดรายงานสินค้าคงคลัง -->
    <div class="modal fade" id="ModalWH-WSGws" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id='H-ModalWH-WSGws'>รายงานสินค้าคงคลัง : ...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg">
                            <div class="table-responsive">
                                <table id="ModalExportWH-WSGws" class="table table-sm rounded rounded-3 overflow-hidden" style="width:100%">
                                    <thead class='font-rps text-center' id='TheadWH-WSGws' style='background-color: rgba(155, 0, 0, 0.04);'>
                                        <tr>
                                            <th width='' rowspan='2' class='text-center border-end align-bottom'>No.</th>
                                            <th width='' rowspan='2' class='text-center border-end align-bottom'>ชื่อสินค้า</th>
                                            <th width='' rowspan='2' class='text-center border-end align-bottom'>สถานะ</th>
                                            <th width='' rowspan='2' class='text-center border-end align-bottom'>หน่วย</th>
                                            <th width='' colspan='4' class='text-center border-end'>จำนวนสินค้าในคลัง : ...</th>
                                            <th width='' rowspan='2' class='text-center border-end align-bottom'>วันที่เข้าล่าสุด</th>
                                            <th width='' rowspan='2' class='text-center align-bottom'>Aging (เดือน)</th>
                                        </tr>
                                        <tr>
                                            <th class='text-center border-end'>คงคลัง</th>
                                            <th class='text-center border-end'>จอง</th>
                                            <th class='text-center border-end'>กำลังสั่ง</th>
                                            <th class='text-center border-end'>มูลค่ารวม (บาท)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='font-rps' id='TbodyWH-WSGws'></tbody>
                                    <tfoot class='font-rps' id='TfootWH-WSGws' style='background-color: rgba(0, 0, 0, 0.04);'></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
<!-- END MODAL -->

<script src="../../js/extensions/apexcharts.js"></script>
<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>

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
    try{ document.createEvent("TouchEvent"); var isMobile = true; }
    catch(e){ var isMobile = false; }
</script> 

<script> 
    $("#sales-report-tab, #return-report-tab, #warehouse-report-tab, #CMoney-report-tab").on("click", function(e) {
        e.preventDefault();
        var Data = $(this).attr("data-tab-SR");
        // console.log(Data);
        switch (Data) {
            case "SalesReport": $("#IDall").click(); SelectYearAll(); break;
            case "ReturnReport": $("#Debt-tab").click(); break;
            case "WarehouseReport": $("#CollaT1, #CollaT2, #CollaT3, #CollaT4, #CollaT5").removeClass("show"); Warehouse(); break;
            case "CMoneyReport": 
                $("#btn-CMall").click(); 
                <?php if($_SESSION['DeptCode'] == "DP006") { ?>
                    $("#btn-CMMT1").click();
                <?php }elseif($_SESSION['DeptCode'] == "DP007") {?>
                    $("#btn-CMMT2").click();
                <?php }elseif($_SESSION['DeptCode'] == "DP008") { ?>
                    $("#btn-CMTT1").click();
                <?php }elseif($_SESSION['DeptCode'] == "DP005") { ?>
                    $("#btn-CMTT2").click();
                <?php } ?>
                break;
            default: break;
        }
    })

    // number_format(5000.25,2)
    function number_format(number,decimal) {
        var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
        var formatter = new Intl.NumberFormat("en",options);
        return formatter.format(number)
    }

    // Export
    function Export() {
        setTimeout(function(){
            // รายงานการขาย
            $('#AllReportExcel, #ReportMT1Excel, #ReportMT2Excel, #ReportTT1Excel, #ReportTT2Excel, #ReportOULExcel, #ReportONLExcel, #ReportKBIExcel, #ReportEXPExcel').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "bInfo" : false,
                "bFilter": false,
                paging: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                }]
            });
            // รายงานการคืน ลดหนี้/QC
            $('#RTAllReportExcel, #RTReportMT1Excel, #RTReportMT2Excel, #RTReportTT1Excel, #RTReportTT2Excel, #RTReportOULExcel, #RTReportONLExcel, #RTReportKBIExcel').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "bInfo" : false,
                "bFilter": false,
                paging: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                }]
            });
            $('#QCAllReportExcel, #QCMT1ReportExcel, #QCMT2ReportExcel, #QCTT1ReportExcel, #QCTT2ReportExcel, #QCOULReportExcel, #QCONLReportExcel, #QCDMNReportExcel, #QCKBIReportExcel').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "bInfo" : false,
                "bFilter": false,
                paging: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                }]
            });
            // รายงานการเก็บเงิน
            $('#CMallExcel, #CMMT1Excel, #CMMT2Excel, #CMTT1Excel, #CMTT2Excel, #CMOULExcel, #CMONLExcel').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "bInfo" : false,
                "bFilter": false,
                paging: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                }]
            });
        }, 800);
    }

    // Modal Export
    function ModalExport() {
        setTimeout(function(){    
            switch(isMobile) {
                case true: var PageLength = 5; break;
                case false: var PageLength = 15; break;
                default: var PageLength = 10; break;
            }
            // รายงานการขาย
            $('#ModalDataExport').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "pageLength": PageLength,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excelHtml5',footer: true, },
                    'print'
                ]
            });
            // รายงานการคืน ลดหนี้
            $('#ModalDataExportRT').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "pageLength": PageLength,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excelHtml5',footer: true, },
                    'print'
                ]
            });
            // รายงานคลังสินค้า ข้อมูลคลังสินค้า
            $('#ModalExportWH-WSGws').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "pageLength": PageLength,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excelHtml5',footer: true, },
                    'print'
                ]
            });
        }, 800);
    }

    // Form Charts
    var DataOption = {
        series: [],
        // theme: { palette: 'palette2'},
        // colors: ['rgba(255,87,34,1)','rgba(255,152,0,1)','rgba(154,17,24,1)','rgba(231,36,46,1)','rgba(60,88,152,1)','rgba(79,112,186,1)','rgba(63,81,181,1)','rgba(233,30,99,1)','#101010'],
        colors: ['#F46036','#D7263D','#662E9B','#2983FF','#1B998B','#F9C80E','#A300D6','#9A1118','#C4BBAF','#5C4742','#E2C044',''],
        chart: {
            fontFamily: 'https://fonts.googleapis.com/css2?family=Niramit:wght@200;300;400;500;600&family=Noto+Sans+Thai:wght@300;400;500&display=swap',
            stacked: true,
            type: 'bar',
            height: 400,
            labelDisplay: "rotate",
            slantLabel: "1",
            toolbar: {
                show: true,
                tools: {
                        download: false,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false,
                    }
            }
        },
        // stroke: {
        //     // curve: 'smooth',
        //     curve: 'straight',
        //     width: [0, 0, 0, 0, 0, 0, 0, 0, 3],
        //     colors: ['rgba(48, 34, 36, 0.31)']
        // },
        title: {
            text: 'ข้อมูล',
            align: 'center',
            style: {
                fontSize:  '14px',
                fontWeight:  'bold',
                // fontFamily:  undefined,
                color:  '#9A1118'
            },
        },
        plotOptions: {
            bar: {
                columnWidth: '80%'
            },
        },
        dataLabels: {
            enabled: false,
            // enabledOnSeries: [1]
        },
        fill: {
            opacity: 1,
            // gradient: {
            //     inverseColors: false,
            //     shade: 'light',
            //     type: "vertical",
            //     opacityFrom: 0.85,
            //     opacityTo: 0.55,
            //     stops: [0, 100, 100, 100]
            // }
        },
        markers: {
            size: 4,
            strokeOpacity: 0.5,
        },
        xaxis: {
            categories: ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],
            tickPlacement: 'on',
            labels: {
                show: true,
                rotate: -40,
                rotateAlways: true,
                // maxWidth: 820
            }
        },
        yaxis: {
            max: 50000000,
            min: 0,
            title: {
                    text: 'บาท',
            },
            labels: {
                formatter: function (val) {
                    return val.toFixed(0)
                }
            }
        },
        // T
            // Hover Data
            // legend: {
            //     position: 'top',
            // },
            // แสดงชื่อบนกราฟข้อมูล
            // dataLabels: {
            //     enabled: true,
            //     formatter: function(value, { seriesIndex, dataPointIndex, w }) {
            //         return w.config.series[seriesIndex].name
            //     }
            // },
        // 
        tooltip: {
            shared: false,
            intersect: false,
            y: {
                formatter: function (y) {
                    if (typeof y !== "undefined") {
                        return y.toLocaleString() + " บาท";
                    }
                    return y;
                }
            }
        },
        noData: {
            text: 'กำลังโหลด...'
        }
    };
</script> 


<!-- รายงานการขาย -->
<?php require("JS_sales_report.php"); ?>
<!-- END รายงานการขาย -->

<!-- รายงานการคืน -->
<?php require("JS_return_report.php"); ?>
<!-- END รายงานการคืน -->

<!-- รายงานคลังสินค้า -->
<?php require("JS_warehouse_report.php"); ?>
<!-- END รายงานคลังสินค้า -->

<!-- รายงานการเก็บเงิน -->
<?php require("JS_CMoney_report.php"); ?>
<!-- END รายงานการเก็บเงิน -->
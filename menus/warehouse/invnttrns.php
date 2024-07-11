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

    span.v-detail:hover{
        color: #151515;
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
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary active" id="Tab1-tab" data-bs-toggle="tab" data-bs-target="#Tab1" type="button" role="tab" aria-controls="Tab1" aria-selected="false"><i class="fas fa-list"></i> สรุปรายเดือน</button>
                                <button class="nav-link text-primary" id="Tab2-tab" data-bs-toggle="tab" data-bs-target="#Tab2" type="button" role="tab" aria-controls="Tab2" aria-selected="false"><i class="fas fa-list-ol"></i> รายการความเคลื่อนไหว</button>
                                <button class="nav-link text-primary" id="Tab3-tab" data-bs-toggle="tab" data-bs-target="#Tab3" type="button" role="tab" aria-controls="Tab3" aria-selected="false"><i class="fas fa-exchange-alt"></i> ความเคลื่อนไหวคลังสินค้า</button>
                            </div>
                        </nav>
                        
                        <div class="tab-content pt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="Tab1" role="tabpanel" aria-labelledby="Tab1-tab">
                                <div class="row">
                                    <div class="col-lg-auto">
                                        <div class="form-group">
                                            <label for="SelectYear">เลือกปี</label>
                                            <select class='form-select form-select-sm' name="SelectYear" id="SelectYear" onchange="ChkYear();">
                                                <?php 
                                                for($y = date("Y"); $y >= 2015; $y--) {
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
                                    <div class="col-sm-auto">
                                        <div class="form-group">
                                            <label for="WareHouse">เลือกคลังสินค้า</label>
                                            <select class='form-control form-control-sm' name="WareHouse" id="WareHouse" data-live-search="true">
                                                <option value='' selected disabled>เลือกคลังสินค้า</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="form-group">
                                            <label for="HideZero">เงื่อนไข</label>
                                            <div class="form-control form-control-sm">
                                                <input class="form-check-input" type="checkbox" id="HideZero">
                                                <span class="ms-1">ซ่อนรายการที่มีจำนวนเป็น 0 ในปัจจุบัน</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto">
                                        <div class="form-group">
                                            <label for="WareHouse">รูปแบบการแสดงผล</label>
                                            <select class='form-select form-select-sm' name="Type" id="Type">
                                                <option value='Q' selected>จำนวนคงคลัง</option>
                                                <option value='A'>มูลค่าคงคลัง (บาท) [จำนวน &times; ราคารับเข้าล่าสุด]</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="form-group">
                                            <label for="">&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="CallData();"><i class="fas fa-search"></i> ค้นหา</button>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="form-group">
                                            <label for="">&nbsp;</label>
                                            <button type="button" class="btn btn-success btn-sm w-100" onclick="Export();"><i class="fas fa-file-excel"></i> Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table1'>
                                                <thead>
                                                    <tr class='bg-primary text-white'>
                                                        <th rowspan='2' width="2%" class='border-top text-center'>ลำดับ</th>
                                                        <th rowspan='2' width="5%" class='border-top text-center'>รหัสสินค้า</th>
                                                        <th rowspan='2' width="15%" class='border-top text-center'>ชื่อสินค้า</th>
                                                        <th rowspan='2' width="5%" class='border-top text-center'>สถานะ</th>
                                                        <th rowspan='2' width="5%" class='border-top text-center'>หน่วย</th>
                                                        <th rowspan='2' width="5%" class='border-top text-center'>วันที่รับเข้าล่าสุด</th>
                                                        <th colspan='13' class='border-top text-center'>จำนวนคงคลัง ณ สิ้นเดือน ของปี <span class='Htable'></span></th>
                                                    </tr>
                                                    <tr class='bg-primary text-white'>
                                                        <th class='text-center' width="5.2%">ตั้งต้น</th>
                                                        <?php 
                                                        for($m = 1; $m <= 12; $m++) {
                                                            echo "<th class='text-center' width='5.2%'>".txtMonth($m)."</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan='6'></th>
                                                        <th class='text-right'></th>
                                                        <?php 
                                                        for($m = 1; $m <= 12; $m++) {
                                                            echo "<th class='text-right'></th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="Tab2" role="tabpanel" aria-labelledby="Tab2-tab">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="ItemCode">เลือกสินค้า</label>
                                            <select class='form-control form-control-sm' name="ItemCode" id="ItemCode" data-live-search="true">
                                                <option value='' selected disabled>เลือกสินค้า</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="form-group">
                                            <label for="WareHouse2">เลือกคลังสินค้า</label>
                                            <select class='form-control form-control-sm' name="WareHouse2" id="WareHouse2" data-live-search="true">
                                                <option value='' selected disabled>เลือกคลังสินค้า</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="form-group">
                                            <label for="HideZero">เลือกระยะเวลา</label>
                                            <div class='d-flex align-items-center'>
                                                <input class="form-control form-control-sm" type="date" name="StartDate" id="StartDate">
                                                <span>&nbsp;ถึง&nbsp;</span>
                                                <input class="form-control form-control-sm" type="date" name="EndDate" id="EndDate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="form-group">
                                            <label for="">&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="CallData2();"><i class="fas fa-search"></i> ค้นหา</button>
                                        </div>
                                    </div>
                                    <div class="col-lg-auto">
                                        <div class="form-group">
                                            <label for="">&nbsp;</label>
                                            <button type="button" class="btn btn-outline-info btn-sm w-100" onclick="Print();"><i class="fas fa-print"></i> Print</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive tableFix">
                                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='Table2'>
                                                <thead class='text-center bg-white'>
                                                    <tr>
                                                        <th rowspan='2' width='3%'>No.</th>
                                                        <th rowspan='2' width='7%'>วันที่เข้าระบบ</th>
                                                        <th rowspan='2' width='7%'>วันที่เอกสาร</th>
                                                        <th rowspan='2' width='8%'>เลขที่เอกสาร</th>
                                                        <th rowspan='2' width='8%'>ประเภทเอกสาร</th>
                                                        <th rowspan='2' width='30%'>รับจาก/จ่ายให้</th>
                                                        <th rowspan='2' width='4%'>ทีม</th>
                                                        <th rowspan='2' width='5%'>คลังสินค้า</th>
                                                        <th rowspan='2' width='7%'>พื้นที่จัดเก็บ</th>
                                                        <th colspan='3'>จำนวน</th>
                                                        <th rowspan='2' width='11%'>ผู้ปฏิบัติงาน</th>
                                                    </tr>
                                                    <tr>
                                                        <th width='3%'>เข้า</th>
                                                        <th width='3%'>ออก</th>
                                                        <th width='4%'>คงเหลือ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan='13' class='text-center'>ไม่มีข้อมูล :(</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="Tab3" role="tabpanel" aria-labelledby="Tab3-tab">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="form-gruop">
                                            <label for="">เลือกปี</label>
                                            <select class='form-select form-select-sm' name="YearTab3" id="YearTab3">
                                                <?php 
                                                for($y = date("Y"); $y >= 2023; $y--) {
                                                    if($y == date("Y")) {
                                                        echo "<option value='".$y."' seleted>".$y."</option>";
                                                    }else{
                                                        echo "<option value='".$y."'>".$y."</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-gruop">
                                            <label for="">เลือกประเภทคลัง</label>
                                            <select class='form-select form-select-sm' name="WareHTab3" id="WareHTab3">
                                                <option value="" selected disabled>เลือกคลัง</option>
                                                <option value="KBI">คลังมือสองส่วนกลาง</option>
                                                <option value="MT1">คลังมือสอง MT1</option>
                                                <option value="MT2">คลังมือสอง MT2</option>
                                                <option value="TT2">คลังมือสอง TT ตจว.</option>
                                                <option value="OUL">คลังมือสอง หน้าร้าน + TT กทม.</option>
                                                <option value="WPP">คลังรอซ่อมและอะไหล่ (WP) และ RD4</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-gruop">
                                            <label for=""></label>
                                            <button class='btn btn-sm btn-primary w-100' onclick='CallData3();'><i class="fas fa-search"></i> ค้นหา</button> 
                                        </div>
                                    </div>
                                </div>

                                <div class="row pt-2">
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-hover table-bordered' style='font-size: 13px;' id='TableT3'>
                                                <thead class='bg-primary text-light'>
                                                    <tr class='text-center'>
                                                        <th rowspan='2'>ชื่อคลัง</th>
                                                        <th rowspan='2' width='10%'>รายละเอียด</th>
                                                        <th colspan='12'>ต้นทุน (บาท)</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <?php
                                                        for($m = 1; $m <= 12; $m++) {
                                                            echo "<th width='6.66%'>".FullMonth($m)."</th>";
                                                        } 
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan='14' class='text-center'>ไม่มีข้อมูล :)</td>
                                                    </tr>
                                                </tbody>
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

<div class="modal fade" id="ModalDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fab fa-readme' style='font-size: 20px;'></i>&nbsp;&nbsp;&nbsp;รายละเอียดความเคลื่อนไหวคลังสินค้ามือสอง (<span id='H'></span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='TableDetail'>
                                <thead class='text-white' style="background-color: #9A1118;">
                                    <tr class='text-center'>
                                        <th width='3%' rowspan='2'>No.</th>
                                        <th width='9%' rowspan='2'>วันที่เข้าระบบ</th>
                                        <th width='10%' rowspan='2'>เลขที่เอกสาร</th>
                                        <th rowspan='2'>รับจาก/จ่ายให้</th>
                                        <th width='8%' rowspan='2'>รหัสสินค้า</th>
                                        <th rowspan='2'>ชื่อสินค้า</th>
                                        <th width='7%' rowspan='2'>คลังสินค้า</th>
                                        <th colspan='4'>จำนวน</th>
                                        <th width='10%' rowspan='2'>มูลค่าคงเหลือ (บาท)</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th width='5%'>ยกมา</th>
                                        <th width='5%'>เข้า</th>
                                        <th width='5%'>ออก</th>
                                        <th width='5%'>คงเหลือ</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
    GetWareHouse();
});

function GetWareHouse() {
    $.ajax({
        url: "menus/warehouse/ajax/ajaxinvnttrns.php?a=GetWareHouse",
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#WareHouse").append(inval['output']).selectpicker();
                $("#WareHouse2").append(inval['output']).selectpicker();
            });
        }
    })
}

function ChkYear() {
    let Year = $("#SelectYear").val();
    if(Year != new Date().getFullYear()) {
        $("#HideZero").attr('disabled', true);
        $("#HideZero").prop('checked', false);
    }else{
        $("#HideZero").attr('disabled', false);
    }
}

function reMonth(M) {
    let td = "";
    switch(M) {
        <?php $td = 6; for($m = 1; $m <= 12; $m++) { $td++; ?>
            case <?php echo $m; ?>: td = <?php echo $td; ?>; break;
        <?php } ?>
    }
    return td;
}

function number_format(number,decimal) {
    var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
    var formatter = new Intl.NumberFormat("en",options);
    return formatter.format(number)
}

function intVal(number){
    return typeof number === 'string' ? 
        number.replace(/[\$,]/g, '')*1 : 
        typeof number === 'number' ? number : 
        0;
};

function CallData() {
    let Year      = $("#SelectYear").val();
    let WareHouse = $("#WareHouse").val();
    let Type = $("#Type").val();
    let HideZero  = $("#HideZero").is(":checked");
    if(WareHouse != null) {
        $(".Htable").html(Year);
        $("#Table1").dataTable().fnClearTable();
        $("#Table1").dataTable().fnDraw();
        $("#Table1").dataTable().fnDestroy();
        $("#Table1").DataTable({
            "ajax": {
                url: "menus/warehouse/ajax/ajaxinvnttrns.php?a=CallData",
                type: "POST",
                data: { Year : Year, WareHouse : WareHouse, HideZero : HideZero, Type : Type, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "No", class: "dt-body-center border-start border-bottom" },
                { "data": "ItemCode", class: "dt-body-center border-start border-bottom" },
                { "data": "ItemName", class: "border-start border-bottom" },
                { "data": "Status", class: "dt-body-center border-start border-bottom" },
                { "data": "Unit", class: "dt-body-center border-start border-bottom" },
                { "data": "LastDate", class: "dt-body-center border-start border-bottom" },
                <?php for($m = 0; $m <= 12; $m++) { ?>
                    { "data": "M_<?php echo $m; ?>", class: "dt-body-right border-start border-bottom" },
                <?php }?>
            ],
            "createdRow": function (row, data, dataIndex, cells) {
                if(data.Year == new Date().getFullYear()) {
                    $('td', row).eq(reMonth(<?php echo date("m"); ?>)).addClass('table-danger');
                }
            },
            "columnDefs": [
                { "width": "2%",  "targets": 0 },
                { "width": "7%",  "targets": 1 },
                { "width": "", "targets": 2 },
                { "width": "3%",  "targets": 3 },
                { "width": "5%",  "targets": 4 },
                { "width": "6%",  "targets": 5 },
                <?php $r = 5; for($m = 0; $m <= 12; $m++) { $r++;?>
                    { "width": "4.3%",  "targets": <?php echo $r; ?> },
                <?php }?>
            ],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                if(Type != "Q") {
                    var Total_M0 = api.column(6).data().reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                    $(api.column(6).footer()).html(number_format(Total_M0,0));
    
                    <?php $th = 6; for($m = 1; $m <= date("m"); $m++) {  $th++; ?>
                        var Total_M<?php echo $m; ?> = api.column(<?php echo $th; ?>).data().reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
    
                        $(api.column(<?php echo $th; ?>).footer()).html(number_format(Total_M<?php echo $m; ?>,0));
                    <?php } ?>
                }else{
                    $(api.column(6).footer()).html("-");
                    <?php $th = 6; for($m = 1; $m <= date("m"); $m++) {  $th++; ?>
                        $(api.column(<?php echo $th; ?>).footer()).html("-");
                    <?php } ?>
                }
            },
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 15,
            "bInfo": false,
            "ordering": false,
            "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
            }
        });
    }else{
        $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
        $("#alert_body").html("กรุณาเลือกคลังสินค้าก่อน");
        $("#alert_modal").modal('show');
    }
}

function Export() {
    let Year      = $("#SelectYear").val();
    let WareHouse = $("#WareHouse").val();
    let HideZero  = $("#HideZero").is(":checked");
    let Type = $("#Type").val();
    // console.log(WareHouse);
    if(WareHouse != null) {
        $(".overlay").show();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinvnttrns.php?a=Export",
            type: "POST",
            data: { Year : Year, WareHouse : WareHouse, HideZero : HideZero, Type : Type, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $(".overlay").hide();
                    window.open("../../FileExport/Invnttrns/"+inval['FileName'],'_blank');
                });
            }
        })
    }else{
        $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
        $("#alert_body").html("กรุณาเลือกคลังสินค้าก่อน");
        $("#alert_modal").modal('show');
    }
    
}

$.ajax({
    url: "../json/OITM.json",
    cache: false,
    success: function(result) {
        var filt_data = 
            result.
                filter(x => x.ItemStatus == "A").
                sort(function(key, inval) {
                    return key.ItemCode.localeCompare(inval.ItemCode);
                });

        var opt = "";

        $.each(filt_data, function(key, inval) {
            opt += "<option value='"+inval.ItemCode+"'>"+inval.ItemCode+" | ["+inval.ProductStatus+"] - "+inval.ItemName+" ["+inval.BarCode+"]</option>";
        });

        $("#ItemCode").append(opt).selectpicker();
    }
});

function CallData2() {
    let ItemCode  = $("#ItemCode").val();
    let WareHouse = $("#WareHouse2").val();
    let StartDate = $("#StartDate").val();
    let EndDate   = $("#EndDate").val();
    // console.log(ItemCode, WareHouse, StartDate, EndDate);
    if(ItemCode != null && WareHouse != null && StartDate != "" && EndDate != "") {
        let DeStartDate = StartDate.split("-");
        let DeEndDate   = EndDate.split("-");
        let Chk = 0;
        if(parseInt(DeStartDate[0]) <= 2022 && parseInt(DeEndDate[0]) <= 2022) {
            Chk = 1;
        }else{
            if(parseInt(DeStartDate[0]) >= 2023 && parseInt(DeEndDate[0]) >= 2023) {
                Chk = 1;
            }
        }
        if(Chk == 1) {
            // $(".overlay").show();
            $.ajax({
                url: "menus/warehouse/ajax/ajaxinvnttrns.php?a=CallData2",
                type: "POST",
                data: { ItemCode : ItemCode, WareHouse : WareHouse, StartDate : StartDate, EndDate : EndDate, Year : DeStartDate[0], },
                success: function(result) {
                    let obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        let Tbody = "";
                        for(let r = 0; r <= inval['Row']; r++) {
                            let cActive = "";
                            if(r == 0 || r == inval['Row']) {
                                cActive = "table-active text-primary";
                            }
                            Tbody+=`<tr class='${cActive}'>
                                        <td class='text-center'>${inval['Data']['No'][r]}</td>
                                        <td class='text-center'>${inval['Data']['CreateDate'][r]}</td>
                                        <td class='text-center'>${inval['Data']['DocDate'][r]}</td>
                                        <td class='text-center'>${inval['Data']['DocNum'][r]}</td>
                                        <td>${inval['Data']['DocType'][r]}</td>
                                        <td>${inval['Data']['ReceivePay'][r]}</td>
                                        <td class='text-center'>${inval['Data']['Team'][r]}</td>
                                        <td class='text-center'>${inval['Data']['WhsCode'][r]}</td>
                                        <td class='text-center'>${inval['Data']['Location'][r]}</td>
                                        <td class='text-right text-success'>${inval['Data']['InQty'][r]}</td>
                                        <td class='text-right text-primary'>${inval['Data']['OutQty'][r]}</td>
                                        <td class='text-right fw-bolder'>${inval['Data']['QtyShow'][r]}</td>
                                        <td>${inval['Data']['Owner'][r]}</td>
                                    </tr>`;
                        }
                        $("#Table2 tbody").html(Tbody);
                        // $(".overlay").hide();
                    });
                }
            })
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
            $("#alert_body").html("ไม่สามารถดึงข้อมูลข้ามปี 2022 - 2023 ได้");
            $("#alert_modal").modal("show");
            $("#Table2 tbody").html("<tr><td colspan='13' class='text-center'>ไม่มีข้อมูล :(</td></tr>");
        }
        
    }else{
        $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
        $("#alert_body").html("กรุณาเลือกข้อมูลที่ต้องการค้นหาให้ครบ");
        $("#alert_modal").modal('show');
    }
}

function Print() {
    let ItemCode  = $("#ItemCode").val();
    let WareHouse = $("#WareHouse2").val();
    let StartDate = $("#StartDate").val();
    let EndDate   = $("#EndDate").val();
    if(ItemCode != null && WareHouse != null && StartDate != "" && EndDate != "") {
        let DeStartDate = StartDate.split("-");
        let DeEndDate   = EndDate.split("-");
        let Chk = 0;
        if(parseInt(DeStartDate[0]) <= 2022 && parseInt(DeEndDate[0]) <= 2022) {
            Chk = 1;
        }else{
            if(parseInt(DeStartDate[0]) >= 2023 && parseInt(DeEndDate[0]) >= 2023) {
                Chk = 1;
            }
        }
        if(Chk == 1) {
            let Year = DeStartDate[0];
            window.open ('menus/warehouse/print/printinvnttrns.php?ItemCode='+ItemCode+'&WareHouse='+WareHouse+'&StartDate='+StartDate+'&EndDate='+EndDate+'&Year='+Year,'_blank');
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
            $("#alert_body").html("ไม่สามารถดึงข้อมูลข้ามปี 2022 - 2023 ได้");
            $("#alert_modal").modal("show");
            $("#Table2 tbody").html("<tr><td colspan='13' class='text-center'>ไม่มีข้อมูล :(</td></tr>");
        }
    }else{
        $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
        $("#alert_body").html("กรุณาเลือกข้อมูลให้ครบ");
        $("#alert_modal").modal('show');
    }
}

function CallData3() {
    let Year  = $("#YearTab3").val();
    let WareH = $("#WareHTab3").val();
    // console.log(Year, WareH);
    $.ajax({
        url: "menus/warehouse/ajax/ajaxinvnttrns.php?a=CallData3",
        type: "POST",
        data: { Year : Year, WareH : WareH, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#TableT3 tbody").html(inval['Tbody']);
            });
        }
    })
}

function Detail(Year, Month, WareH) {
    // console.log(Year, Month, WareH);
    $.ajax({
        url: "menus/warehouse/ajax/ajaxinvnttrns.php?a=Detail",
        type: "POST",
        data: { Year : Year, Month : Month, WareH : WareH, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#H").html(inval['H']);
                $("#TableDetail tbody").html(inval['Data']);
                $("#ModalDetail").modal("show");
            });
        }
    })
}
</script> 
<style type="text/css">

</style>
<?php
$start_year = 2022;
$this_year  = date("Y");
$this_month = date("m");
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
                <!---------- เนื้อหา Pages ------------>
                <div class="row">
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="filt_year">เลือกปี</label>
                            <select name="filt_year" id="filt_year" class="form-select form-select-sm">
                            <?php
                                for($y = $this_year; $y >= $start_year; $y--) {
                                    if($y == $this_year) {
                                        $y_slct = " selected";
                                    } else {
                                        $y_slct = "";
                                    }
                                    echo "<option value='$y'$y_slct>$y</option>";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-7">
                        <div class="form-group">
                            <label for="filt_month">เลือกเดือน</label>
                            <select name="filt_month" id="filt_month" class="form-select form-select-sm">
                            <?php
                                for($m = 1; $m <= 12; $m++) {
                                    if($m == $this_month) {
                                        $m_slct = " selected";
                                    } else {
                                        $m_slct = "";
                                    }
                                    echo "<option value='$m'$m_slct>".FullMonth($m)."</option>";
                                }
                                $DeptCode = $_SESSION['DeptCode'];
                                if(($DeptCode == "DP001" || $DeptCode == "DP002")) {
                                    $opt_dis = NULL;
                                } else {
                                    $opt_dis = " disabled";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <label for="filt_search">ค้นหา:</label>
                            <input type="text" id="filt_search" class="form-control form-control-sm" placeholder="กรุณากรอกเพื่อค้นหา..." />
                        </div>
                    </div>
                    <div class="col-lg-1 col-6">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" id="btn_search"  onclick="CallData()"><i class="fas fa-search fa-fw fa-1x"></i></button>
                        </div>
                    </div>
                </div>
<hr>
                <div class="row">
                    <div class="col-lg">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-report-sales" role="tabpanel" aria-labelledby="nav-report-sales-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm table-bordered" style="width:100%" id='TablePriceList'>
                                                <thead class='fw-bolder' style="font-size: 13px; background-color: rgba(245, 245, 245, 0.63);">
                                                    <tr style='font-size: 12px;'>
                                                        <td width='11%' class='text-center'>เลขที่ใบขนส่ง</td>
                                                        <td width='11%' class='text-center'>วันที่เอกสาร</td>
                                                        <td class='text-center'>พนักงานขับรถ</td>
                                                        <td width='10%' class='text-center'>ทะเบียนรถ</td>
                                                        <td width='15%' class='text-center'>พนักงานออกรถ</td>
                                                        <td width='8%' class='text-center'><i class="fas fa-cog fa-fw fa-2x"></i></td>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size: 11.5px;" id="LDNList"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-------- สินสุดเนื้อหา Pages --------->
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="LoadCard" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-shipping-fast fa-fw fa-2x"></i> รายละเอียดใบส่งสินค้า</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ORDER HEADER -->
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tr class="align-middle">
                                <th width="10%">พนักงานโหลดสินค้า</th>
                                <td colspan="3"><input type="text" class="form-control-plaintext form-control-sm" id="EmpLoad" readonly /></td>
                                <th width="10%">เลขที่ใบส่งสินค้า</th>
                                <td width="10%">
                                    <input type="text" class="form-control-plaintext form-control-sm" id="LogiNum" readonly />
                                </td>
                            </tr>
                            <tr class="align-middle">
                                <th>พนักงานขับรถ</th>
                                <td width="35%"><input type="text" class="form-control-plaintext form-control-sm" id="DriverName" readonly /></td>
                                <th width="10%">ทะเบียนรถ</th>
                                <td width="10%"><input type="text" class="form-control form-control-sm" name="CarLC" id="CarLC" readonly /></td>
                                <th>วันที่โหลดสินค้า</th>
                                <td><input type="date" class="form-control form-control-sm" name="DocDate" id="DocDate" ></td>
                            </tr>
                        </table>
 

                        <!-- CONTENT TAB -->
                        <div class="tab-content mt-2">
                            <div class="tab-pane show active" id="view_ItemList" role="tabpanel" aria-labelledby="view_ItemTab">
                                <table class="table table-bordered table-hover table-sm" id="OrderItem" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%" rowspan="2">ลำดับ</th>
                                            <th width="15%" rowspan="2">ชื่อลูกค้า/ขนส่ง</th>
                                            <th rowspan="2">ชื่อร้านค้า</th>
                                            <th width="10%" rowspan="2">เลขที่บิล</th>
                                            <th width="10%" rowspan="2">วันที่บิล</th>
                                            <th width="5%" rowspan="2">จำนวน</th>
                                            <th colspan='2'>ยอดเรียกเก็บ</th>
                                            <th width="5%" rowspan="2"><i class="fas fa-cog fa-fw fa-2x"></i></th>
                                        </tr>
                                        <tr>
                                            <th width="5%">เช็ค(บาท)</th>
                                            <th width="5%">เงินสด(บาท)</th>
                                        </tr>
                                    </thead>
                                    <tbody id='LogiList'> </tbody>
                                    <tfoot class="text-right">
                                        <tr>
                                            <th colspan="5">รวม</th>
                                                <td><span name="CountTotal" id="CountTotal"></td>
                                                <td><span  name="ChqTotal" id="ChqTotal"></td>
                                                <td><span  name="CashTotal" id="CashTotal"></td>
                                                <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <p>ผู้จัดทำ: <span id="OwnerName"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                        <div class="col-6 text-right" id="DataPages">
                            
                        </div>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-sm btn-outline-info" id="btn_printDL" onclick="printLG();"><i class="fas fa-print fa-fw fa-1x"></i> พิมพ์</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
        CallData();
	});
</script> 
<script>
    $(document).ready(function() {
        $("#filt_search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#LDNList tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
<script type="text/javascript">
    function CallHead(){
        $(".overlay").show();
        var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/warehouse/ajax/ajaxlogistreport.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
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
    function CallData(){
        $(".overlay").show();
        //var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/warehouse/ajax/ajaxlogistreport.php?a=read",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            data : {Mselect : $('#filt_month').val(),
                    Yselect : $('#filt_year').val(),
                   },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#LDNList').empty();
                    $("#LDNList").html(inval["output"]);
                });
                $(".overlay").hide();
            }
        });
    };
    function CallPrint(x){
        $(".overlay").show();
        //var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/warehouse/ajax/ajaxlogistreport.php?a=detail",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            data : {LogiNum : x,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    //$('#LDNList').empty();
                    //$("#LDNList").html(inval["output"]);
                    $('#LogiNum').val(inval['LogiNum']);
                    $('#EmpLoad').val(inval['EmpLoad']);
                    $('#CarLC').val(inval['CarLC']);
                    $('#DriverName').val(inval['DriverName']);
                    $('#DocDate').val(inval['DocDate']);

                    $('#OwnerName').html(inval['NameCreate']);
                    $("#LogiList").html(inval["output"]);
                    $("#CountTotal").html(inval["Count"]);
                    $("#ChqTotal").html(inval["CHQ"]);
                    $("#CashTotal").html(inval["CSH"]);
                    $("#DataPages").html(inval['output2']);


                    $('#LoadCard').modal('show');
                });
                $(".overlay").hide();
            }
        });
    };
    
    function printLG(){
        var pages = $('#ListPages').val();
        var loginum = $('#LogiNum').val();
        window.open('menus/warehouse/Print/printSendBox.php?lid='+loginum+'&p='+pages+'','_blank');
    }
    
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
</script> 
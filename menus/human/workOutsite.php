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
                    <div class="col-lg d-flex">
                        <div class="form-group" style='width: 150px;'>
                            <label for="YearSelect">เลือกปี</label>
                            <select class="form-select form-select-sm" name="YearSelect" id="YearSelect" onchange="CallData();">
                                <?php 
                                $cYear = "";
                                for($y = 2023; $y <= date("Y"); $y++) {
                                    if($y == date("Y")) {
                                        $cYear = "<option value='".$y."' selected>".$y."</option>".$cYear;
                                    }else{
                                        $cYear = "<option value='".$y."'>".$y."</option>".$cYear;
                                    }
                                } 
                                echo $cYear;
                                ?>
                            </select>
                        </div>
                        <div class="form-group ps-3" style='width: 170px;'>
                            <label for="MonthSelect">เลือกเดือน</label>
                            <select class="form-select form-select-sm" name="MonthSelect" id="MonthSelect" onchange="CallData();">
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
                    <div class="col-lg d-flex justify-content-end">
                        <div class="form-group" style='width: 200px;'>
                            <label for="SearchData">ค้นหา</label>
                            <input type="text" class='form-control form-control-sm' name='SearchData' id='SearchData'>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class='table-responsive'>
                            <table class='table table-sm table-bordered table-hover table-sm' id='WorkData' style='font-size: 12px;'>
                                <thead class='text-center'>
                                    <tr>
                                        <th width='5%'>ลำดับที่</th>
                                        <th width='7.5%'>รหัสพนักงาน</th>
                                        <th>ชื่อพนักงาน</th>
                                        <th width='17.5%'>ฝ่าย</th>
                                        <th width='25%'>ตำแหน่ง</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search-plus"></i> สถิติการทำงานนอกสถานที่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm col-lg-4 col-xl-4">
                        <div class='d-flex border align-items-center justify-content-center p-2 rounded'>
                            <div class='pe-3'>
                                <i class="fas fa-id-badge" style='font-size: 70px;'></i>
                            </div>
                            <div>
                                <span class='d-flex fw-bolder' style='font-size: 12px;'>รหัสพนักงาน&nbsp;
                                    <span class='fw-bold' id='dIdEmp'></span>
                                </span>
                                <span class='d-flex fw-bolder' style='font-size: 12px;'>ชื่อ&nbsp;
                                    <span class='fw-bold' id='dName'></span>
                                </span>
                                <span class='d-flex fw-bolder' style='font-size: 12px;'>ตำแหน่ง&nbsp;
                                    <span class='fw-bold' id='dPosition'></span>
                                </span>
                                <span class='d-flex fw-bolder' style='font-size: 12px;'>ฝ่าย&nbsp;
                                    <span class='fw-bold' id='dOrgUnit'></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm col-lg-8 col-xl-8">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TableDetail' style='font-size: 12px;'>
                                <thead class='text-center' >
                                    <tr>
                                        <th width='7.5%'>ลำดับที่</th>
                                        <th width='20%'>วันที่</th>
                                        <th width='20%'>เวลา</th>
                                        <th width='52.5%'>สถานที่</th>
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
});

function CallData() {
    var Year  = $("#YearSelect").val();
    var Month = $("#MonthSelect").val();
    $.ajax({
        url: "menus/human/ajax/ajaxworkOutsite.php?p=WorkData",
        type: "POST",
        data: {
            y: Year,
            m: Month
        },
        success: function(result) {
            var obj =jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                var Rows = parseFloat(inval['Rows']);
                var tBody = "";
                var VisOrder = 0;
                for(i = 0; i < Rows; i++) {
                    VisOrder++;
                    tBody +=
                        "<tr>"+
                            "<td class='text-center'>"+VisOrder+"</td>"+
                            "<td class='text-center'><a href='javascript:void(0);' class='detail' data-detail='"+inval[i]['EmpCode']+"'>"+inval[i]['EmpCode']+"</a></td>"+
                            "<td>"+inval[i]['FullName']+"</td>"+
                            "<td>"+inval[i]['Department']+"</td>"+
                            "<td>"+inval[i]['Position']+"</td>"+
                        "</tr>";
                }
                $("#WorkData tbody").html(tBody);

                $(".detail").on("click", function() {
                    var EmpCode = $(this).attr("data-detail");


                    $.ajax({
                        url: "menus/human/ajax/ajaxworkOutSite.php?p=GetDetail",
                        type: "POST",
                        data: {
                            y: $("#YearSelect").val(),
                            m: $("#MonthSelect").val(),
                            e: EmpCode
                        },
                        success: function(result) {
                            var obj = jQuery.parseJSON(result);
                            $.each(obj, function(key, inval) {
                                $("#dIdEmp").html(inval['HEAD']['EmpCode']);
                                $("#dName").html(inval['HEAD']['FullName']);
                                $("#dPosition").html(inval['HEAD']['Position']);
                                $("#dOrgUnit").html(inval['HEAD']['Department']);

                                var Rows = parseFloat(inval['Rows']);
                                var Tbody = "";
                                var VisOrder = 0;
                                for(i = 0; i < Rows; i++) {
                                    VisOrder++;
                                    Tbody +=
                                        "<tr>"+
                                            "<td class='text-right'>"+VisOrder+"</td>"+
                                            "<td class='text-center'>"+inval[i]['DateStamp']+"</td>"+
                                            "<td class='text-center'>"+inval[i]['TimeStamp']+"</td>"+
                                            "<td>"+inval[i]['Location']+"</td>"+
                                        "</tr>";
                                }
                                $("#TableDetail tbody").html(Tbody);
                            });
                        }
                    })

                    $("#ModalDetail").modal("show");
                });
            })
        }
    })
}

$("#SearchData").on("keyup", function(){
    var kwd = $(this).val().toLowerCase();
    $("#TableCallData tbody tr").filter(function(){
        $(this).toggle($(this).text().toLowerCase().indexOf(kwd) > -1)
    });
});


</script> 
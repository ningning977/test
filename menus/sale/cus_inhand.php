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
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="YearSelect">เลือกปี</label>
                            <select class="form-select form-select-sm " name="YearSelect" id="YearSelect">
                                <?php 
                                    for($y = date('Y'); $y >= 2023; $y--) {
                                        echo (($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>");
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="EmpSelect">เลือกพนักงานขาย</label>
                            <select class="form-select form-select-sm " name="EmpSelect" id="EmpSelect">
                                <option value="" selected disabled>กรุณาเลือก</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="GroupCus">เลือกกลุ่มลูกค้า&nbsp;<span class="text-muted" style='font-size: 12px;'>(เลือกได้มากกว่า 1 รายการ)</span></label>
                            <select class="selectpicker form-control form-control-sm " name="GroupCus[]" id="GroupCus" multiple>
                                <option value="ALL" selected >ทั้งหมด</option>
                                <?php 
                                $SQL = "SELECT T0.GroupCode, T0.GroupName FROM OCRG T0 ORDER BY T0.GroupName";
                                $QRY = SAPSelect($SQL);
                                while($result = odbc_fetch_array($QRY)) {
                                    echo "<option value='".$result['GroupCode']."'>".conutf8($result['GroupName'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="CallData();"><i class="fas fa-search"></i> ค้นหา</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button type="button" class="btn btn-success btn-sm w-100" onclick="Export();"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered mb-0' style='font-size: 11.5px;' id='Table1'>
                                <thead>
                                    <tr>
                                        <th rowspan='2' class='border-top text-center'>รหัสลูกค้า</th>
                                        <th rowspan='2' class='border-top text-center'>ชื่อลูกค้า</th>
                                        <th rowspan='2' class='border-top text-center'>กลุ่มลูกค้า</th>
                                        <th rowspan='2' class='border-top text-center'>เป้าขาย<br>ต่อปี</th>
                                        <th rowspan='2' class='border-top text-center'>เป้าขาย<br>ต่อเดือน</th>
                                        <th rowspan='2' class='border-top text-center'>ยอดรวม<br>ปัจจุบัน<br><span class='cYear'></span></th>
                                        <th rowspan='2' class='border-top text-center'>ยอดรวม<br>ปีที่แล้ว<br><span class='pYear'></span></th>
                                        <th colspan='12' class='border-top text-center'>ยอดขายปี <span class='cYear'></span></th>
                                    </tr>
                                    <tr>
                                        <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            echo "<th class='text-center'>".FullMonth($m)."</th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                            </table>
                            <table class='table table-sm table-hover table-bordered fak-table' style='font-size: 11.5px;'>
                                <tr>
                                    <td colspan='19' class='text-center'>ไม่มีข้อมูล :(</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
    GetEmp();
    sessionStorage.setItem('GroupCus',JSON.stringify(""));
});

function GetEmp() {
    $.ajax({
        url: "menus/sale/ajax/ajaxcus_inhand.php?a=GetEmp",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#EmpSelect").append(inval['Data']);
            });
        }
    })
}

function CallData() {
    let Year = $("#YearSelect").val();
    let Emp = $("#EmpSelect").val();
    let Cus = $("#GroupCus").val().join();

    $(".cYear").html(Year);
    $(".pYear").html(Year-1);
    // console.log(Emp, Cus);
    let Chk = 0;
    if(Emp == null) {
        $("#EmpSelect").addClass("is-invalid");
        Chk++;
    }else{
        $("#EmpSelect").removeClass("is-invalid");
    }
    if(Cus == "") {
        Chk++;
    }
    if(Chk == 0){
        $(".fak-table").hide();
        $("#EmpSelect").removeClass("is-invalid");
        $("#Table1").dataTable().fnClearTable();
        $("#Table1").dataTable().fnDraw();
        $("#Table1").dataTable().fnDestroy();
        $("#Table1").DataTable({
            "ajax": {
                url: "menus/sale/ajax/ajaxcus_inhand.php?a=CallData",
                type: "POST",
                data: { Year : Year, Emp : Emp, Cus : Cus, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "CardCode", class: "dt-body-center border-start border-bottom" },
                { "data": "CardName", class: "border-start border-bottom" },
                { "data": "GroupName", class: "border-start border-bottom" },
                { "data": "TarYear", class: "dt-body-right border-start border-bottom" },
                { "data": "TarMonth", class: "dt-body-right border-start border-bottom border-end" },
                { "data": "cTotal", class: "dt-body-right border-start border-bottom border-end bg-body fw-bolder" },
                { "data": "pTotal", class: "dt-body-right border-start border-bottom border-end bg-body" },
                <?php for($m = 1; $m <= 12; $m++) { ?>
                    { "data": "M<?php echo $m; ?>", class: "dt-body-right border-start border-bottom border-end" },
                <?php } ?>
            ],
            "columnDefs": [
                { "width": "5%", "targets": 0 },
                { "width": "12%", "targets": 1 },
                { "width": "7%", "targets": 2 },
                { "width": "4.8%", "targets": 3 },
                { "width": "4.8%", "targets": 4 },
                { "width": "4.8%", "targets": 5 },
                { "width": "4.8%", "targets": 6 },
                <?php $w = 6; for($m = 1; $m <= 12; $m++) { $w++; ?>
                    { "width": "4.8%", "targets": <?php echo $w; ?> },
                <?php } ?>
            ],
            "order": [
                [3, 'desc']
            ],
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 15,
            // "ordering": false,
        });
    }
}

function Export() {
    let Year = $("#YearSelect").val();
    let Emp = $("#EmpSelect").val();
    let Cus = $("#GroupCus").val().join();
    let Chk = 0;
    if(Emp == null) {
        $("#EmpSelect").addClass("is-invalid");
        Chk++;
    }else{
        $("#EmpSelect").removeClass("is-invalid");
    }
    if(Cus == "") {
        Chk++;
    }
    if(Chk == 0){
        $(".overlay").show();
        $.ajax({
            url: "menus/sale/ajax/ajaxcus_inhand.php?a=Export",
            type: "POST",
            data: { Year : Year, Emp : Emp, Cus : Cus, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $(".overlay").hide();
                    window.open("../../FileExport/CusInHand/"+inval['FileName'],'_blank');
                });
            }
        })
    }
}

$("#GroupCus").on("change", function(){
    if($("#GroupCus").val().length != 1) {
        const v = $("#GroupCus").val()[1];
        if($("#GroupCus").val()[0] == 'ALL') {
            if(JSON.parse(sessionStorage.getItem('GroupCus')) == "") {
                $("#GroupCus").selectpicker('val', []);
                $("#GroupCus").selectpicker('val', [v]);
                sessionStorage.setItem('GroupCus',JSON.stringify($(this).val()[0]));
            }else{
                sessionStorage.setItem('GroupCus',JSON.stringify($(this).val()[0]));
                if(JSON.parse(sessionStorage.getItem('GroupCus')) == 'ALL') {
                    $("#GroupCus").selectpicker('val', []);
                    $("#GroupCus").selectpicker('val', ['ALL']);
                    sessionStorage.setItem('GroupCus',JSON.stringify(""));
                }
            }
        }
    }
})

</script> 
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
                            <label for="Month">เลือกเดือน</label>
                            <select class='form-select form-select-sm' name="Month" id="Month" onchange='CallData();'>
                                <?php
                                for($m = 1; $m <= 12; $m++) {
                                    if($m == date("m")) {
                                        echo "<option value='$m' selected>".FullMonth($m)."</option>";
                                    }else{
                                        echo "<option value='$m'>".FullMonth($m)."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="Year">เลือกปี</label>
                            <select class='form-select form-select-sm' name="Year" id="Year" onchange='CallData();'>
                                <?php
                                for($y = date("Y"); $y >= 2023; $y--) {
                                    if($y == date("Y")) {
                                        echo "<option value='$y' selected>$y</option>";
                                    }else{
                                        echo "<option value='$y'>$y</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' id='table1'>
                                <thead>
                                    <tr>
                                        <th rowspan='2' class='text-center border-top' width='7%'>รหัสลูกค้า</th>
                                        <th rowspan='2' class='text-center border-top' width='20%'>ชื่อลูกค้า</th>
                                        <th rowspan='2' class='text-center border-top' width='15%'>พนักงานขาย</th>
                                        <th colspan='7' class='text-center border-top'>ผลการเข้าพบลูกค้า</th>
                                        <th rowspan='2' class='text-center border-top'width='15%'>แผนการดำเนินงาน</th>
                                        <th rowspan='2' class='text-center border-top'width='15%'>ผลการดำเนินงาน</th>
                                        <th rowspan='2' class='text-center border-top' width='7%'>วันที่<br>กรอกข้อมูล</th>
                                    </tr>
                                    <tr>
                                        <th class='text-center' width='3%' style='cursor: pointer;' title="1. สินค้าถูกโชว์เรียง และสะอาดสวยงาม" onclick='ListHeaderQ(1);'>1</th>
                                        <th class='text-center' width='3%' style='cursor: pointer;' title="2. มี Shelf Talker หรือป้ายราคาเพื่อทำ Sales" onclick='ListHeaderQ(2);'>2</th>
                                        <th class='text-center' width='3%' style='cursor: pointer;' title="3. มี Shelf หรือ Display" onclick='ListHeaderQ(3);'>3</th>
                                        <th class='text-center' width='3%' style='cursor: pointer;' title="4. มี PC หรือ มือปืน" onclick='ListHeaderQ(4);'>4</th>
                                        <th class='text-center' width='3%' style='cursor: pointer;' title="5. ได้สอบถาม PC ในเรื่องปัญหาสินค้าภายในร้านค้าแล้วหรือไม่?" onclick='ListHeaderQ(5);'>5</th>
                                        <th class='text-center' width='3%' style='cursor: pointer;' title="6. นับสต๊อคเพื่อเติมสินค้าที่ขาด" onclick='ListHeaderQ(6);'>6</th>
                                        <th class='text-center' width='3%' style='cursor: pointer;' title="7. ส่งสำรวจราคาคู่แข่งใน LINE กลุ่ม" onclick='ListHeaderQ(7);'>7</th>
                                    </tr>
                                </thead>
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
        CallData();
    });

    function CallData() {
        const Month = $("#Month").val();
        const Year = $("#Year").val();
        $("#table1").dataTable().fnClearTable();
        $("#table1").dataTable().fnDraw();
        $("#table1").dataTable().fnDestroy();
        $("#table1").DataTable({
            "ajax": {
                url: "menus/sale/ajax/ajaxmeet_customer.php?a=CallData",
                type: "POST",
                data: { Month : Month, Year : Year, },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "CardCode", class: "dt-body-center align-top border-start border-bottom" },
                { "data": "CardName", class: "border-start align-top border-bottom" },
                { "data": "SlpName", class: "border-start align-top border-bottom" },
                { "data": "Q1", class: "dt-body-center align-top border-start border-bottom" },
                { "data": "Q2", class: "dt-body-center align-top border-start border-bottom" },
                { "data": "Q3", class: "dt-body-center align-top border-start border-bottom" },
                { "data": "Q4", class: "dt-body-center align-top border-start border-bottom" },
                { "data": "Q5", class: "dt-body-center align-top border-start border-bottom" },
                { "data": "Q6", class: "dt-body-center align-top border-start border-bottom" },
                { "data": "Q7", class: "dt-body-center align-top border-start border-bottom" },
                { "data": "DetailPlan", class: "align-top border-start border-bottom" },
                { "data": "DetailActual", class: "align-top border-start border-bottom" },
                { "data": "DataDate", class: "dt-body-center align-top border-start border-bottom" },
            ],
            "columnDefs": [
                { "width": "7%", "targets": 0 },
                { "width": "20%", "targets": 1 },
                { "width": "15%", "targets": 2 },
                { "width": "3%", "targets": 3 },
                { "width": "3%", "targets": 4 },
                { "width": "3%", "targets": 5 },
                { "width": "3%", "targets": 6 },
                { "width": "3%", "targets": 7 },
                { "width": "3%", "targets": 8 },
                { "width": "3%", "targets": 9 },
                { "width": "15%", "targets": 10 },
                { "width": "15%", "targets": 11 },
                { "width": "7%", "targets": 12 },
            ],
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 15,
            "ordering": false,
            "language":{ 
                    "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
            },
        });

        $("th").removeClass("align-top");
    }

    function ListHeaderQ(Q) {
        let mess = "";
        switch(Q) {
            case 1: mess = "1. สินค้าถูกโชว์เรียง และสะอาดสวยงาม"; break;
            case 2: mess = "2. มี Shelf Talker หรือป้ายราคาเพื่อทำ Sales"; break;
            case 3: mess = "3. มี Shelf หรือ Display"; break;
            case 4: mess = "4. มี PC หรือ มือปืน"; break;
            case 5: mess = "5. ได้สอบถาม PC ในเรื่องปัญหาสินค้าภายในร้านค้าแล้วหรือไม่?"; break;
            case 6: mess = "6. นับสต๊อคเพื่อเติมสินค้าที่ขาด"; break;
            case 7: mess = "7. ส่งสำรวจราคาคู่แข่งใน LINE กลุ่ม"; break;
        }
        $("#alert_header").html("ความหมายการเข้าพบลูกค้า ข้อที่ "+Q);
        $("#alert_body").html(mess);
        $("#alert_modal").modal("show");
    }
</script> 
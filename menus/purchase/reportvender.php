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
                <!---------- เนื้อหา Pages ------------>
                <div class="row d-flex justify-content-between">
                    <div class="col-sm col-lg col-xl d-flex">
                        <div class="form-group" style='width: 150px;'>
                            <label for="SLvender">เลือกซัพพลายเออร์</label>
                            <select class="form-select form-select-sm" name="SLvender" id="SLvender" onchange="SLvender()">
                                <option value="1" selected>ทั้งหมด</option>
                                <option value="2">ในประเทศ</option>
                                <option value="3">ต่างประเทศ</option>                             
                            </select>
                        </div>

                        <div class="form-group ps-3" style='width: 120px;'>
                            <label for="YearSL">เลือกปี</label>
                            <select class="form-select form-select-sm" name="YearSL" id="YearSL" onchange="SLvender()">
                                <?php
                                    $Y = date("Y");
                                    for($STY = 2020; $STY <= $Y; $Y--) {
                                        if($Y == date("Y")) {
                                            echo "<option value='$Y' selected>$Y</option>";
                                        }else{
                                            echo "<option value='$Y'>$Y</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group ps-3">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Export();'><i class="fas fa-file-excel fa-fw"></i> Excel</button>
                        </div>
                    </div>

                    <div class="col-sm-5 col-lg-3 col-xl-2">
                        <div class="form-group">
                            <label for="FilterBox">ค้นหา</label>
                            <input type="text" class='form-control form-control-sm' name='FilterBox' id='FilterBox'>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-hover table-bordered rounded rounded-3 '>
                                <thead style='font-size: 13px;' class='bg-light'>
                                    <tr>
                                        <th rowspan='2' class='text-center align-bottom' width='2.8%'>No.</th>
                                        <th rowspan='2' class='text-center align-bottom'>รายชื่อซัพพลายเออร์</th>
                                        <th rowspan='2' class='text-center align-bottom' width='6.8%'>ยอดสั่งซื้อ<br><span id='H_YearP'></span> (บาท)</th>
                                        <th rowspan='2' class='text-center align-bottom' width='6.8%'>ยอดสั่งซื้อ<br><span id='H_Year1'></span> (บาท)</th>
                                        <th rowspan='2' class='text-center align-bottom' width='4.8%'>% การเติบโต</th>
                                        <th colspan='12' class='text-center'>ยอดสั่งซื้อรายเดือน ปี <span id='H_Year2'></span> (บาท)</th>
                                    </tr>
                                    <tr>
                                        <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            echo "<th width='4.83%' class='text-center'>".txtMonth($m)."</th>";
                                        } 
                                        ?>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='Tbody'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-------- สินสุดเนื้อหา Pages --------->
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span style='font-size: 17px;'>ซัพพลายเออร์</span> <i class="fas fa-caret-right"></i> <span style='font-size: 17px;' id='SupCus'></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered'>
                                <thead style='font-size: 13px;'>
                                    <tr class='text-center'>
                                        <th>ยอดสั่งซื้อรายเดือน</th>
                                        <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            echo "<th>".txtMonth($m)."</th>";
                                        } 
                                        ?>
                                        <th>รวม</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='TbodyModal1'></tbody>
                            </table>

                            <table class='table table-sm table-hover rounded rounded-3 overflow-hidden w-100' id="DTTable">
                                <thead style='font-size: 13px;' id='TheadModal2' class='bg-light'>
                                    <tr>
                                        <th width='10%' class='text-center'>เลขที่เอกสาร</th>
                                        <th width='10%' class='text-center'>วันที่</th>
                                        <th width='10%' class='text-center'>รหัสสินค้า</th>
                                        <th width='30%' class='text-center'>รายการสินค้า</th>
                                        <th width='10%' class='text-center'>จำนวน</th>
                                        <th width='10%' class='text-center'>ราคา/หน่วย</th>
                                        <th width='10%' class='text-center'>ราคารวม</th>
                                        <th width='10%' class='text-center'>สกุลเงิน</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='TbodyModal2'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="preview_footer"></div>
        </div>
    </div>
</div>

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
        SLvender();
	});

    try{ document.createEvent("TouchEvent"); var isMobile = true; }
    catch(e){ var isMobile = false; }

    function SLvender() {
        $.ajax({
            url: "menus/purchase/ajax/ajaxreportvender.php?a=SLvender",
            type: "POST",
            data: { DataSL : $("#SLvender").val(), Year : $("#YearSL").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#H_Year1").html(inval['cYear']);
                    $("#H_Year2").html(inval['cYear']);
                    $("#H_YearP").html(inval['pYear']);
                    $("#Tbody").html(inval['Tbody']);
                })
            }  
        })
    }

    $("#FilterBox").on("keyup", function(){
        var kwd = $(this).val().toLowerCase();
        $("#Tbody tr").filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(kwd) > -1)
        });
    });

    function DataDetail(CardCode) {
        $(".overlay").show();
        $.ajax({
            url: "menus/purchase/ajax/ajaxreportvender.php?a=DataDetail",
            type: "POST",
            data: { CardCode : CardCode, Year : $("#YearSL").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#SupCus").html(inval['SupCus']);
                    $("#TbodyModal1").html(inval['Tbody1']);
                    // $("#TbodyModal2").html(inval['Tbody2']);
                    var Thead = "<tr>"+
                                    "<th width='10%' class='text-center'>เลขที่เอกสาร</th>"+
                                    "<th width='10%' class='text-center'>วันที่</th>"+
                                    "<th width='10%' class='text-center'>รหัสสินค้า</th>"+
                                    "<th width='44%' class='text-center'>รายการสินค้า</th>"+
                                    "<th width='7%' class='text-center'>ราคา/หน่วย</th>"+
                                    "<th width='5%' class='text-center'>จำนวน</th>"+
                                    "<th width='7%' class='text-center'>ราคารวม</th>"+
                                    "<th width='7%' class='text-center'>สกุลเงิน</th>"+
                                "</tr>";
                    $('#DTTable').DataTable().destroy();
                    $("#TheadModal2").html(Thead);
                    $('#TbodyModal2').empty();
                    $("#TbodyModal2").html(inval['Tbody2']);
                    ShowDataTable();
                    $("#ModalDetail").modal("show");
                });
                $(".overlay").hide();
            }  
        })
    }

    function ShowDataTable() {
        setTimeout(function(){
            switch(isMobile) {
                case true: var PageLength = 5; break;
                case false: var PageLength = 15; break;
                default: var PageLength = 10; break;
            }
            $('#DTTable').DataTable({
                destroy: true,
                "bAutoWidth": false,
                "ordering": false,
                "pageLength": PageLength,
                dom: 'frtip'
            });
        }, 500);
    }

    function Export() {
        let Year = $("#YearSL").val();
        $(".overlay").show();
        $.ajax({
            url: "menus/purchase/ajax/ajaxreportvender.php?a=Export",
            type: "POST",
            data: { Year : Year, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $(".overlay").hide();
                    window.open("../../FileExport/SupData/"+inval['FileName'],'_blank');
                });
            }
        })
    }
</script> 
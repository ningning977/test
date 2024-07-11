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
                    <div class="col-lg col-sm d-flex">
                        <div class="form-group" style='width: 100px;'>
                            <label for=""><i class="far fa-calendar"></i> เลือกปี</label>
                            <select class="form-select form-select-sm" name="filt_year" id="filt_year" onchange="CallData()">
                                <?php 
                                    $Y = date("Y");
                                    for($STY = 2018; $STY <= $Y; $Y--) {
                                        if($Y == date("Y")) {
                                            echo "<option value='".$Y."' selected>".$Y."</option>";
                                        }else{
                                            echo "<option value='".$Y."'>".$Y."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group ps-3" style='width: 430px;'>
                            <label for=""><i class="fas fa-store-alt"></i> เลือกร้าน</label>
                            <select class="form-control form-control-sm selectpicker" name="filt_cus" id="filt_cus" data-live-search="true" onchange="CallData()"></select>
                        </div>
                    </div>
                    <div class="col-lg col-sm d-flex justify-content-end">
                        <div class='align-self-center'>
                            <button class='btn btn-sm btn-success' style='margin-top: 10px;' onclick="Export()"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>
                <hr class='m-2' style='color: #BDBDBD;'>

                <div class="row pt-2">
                    <div class="col-lg">
                        <div class='table-responsive'>
                            <table class='table table-sm table-hover table-bordered'>
                                <thead style='font-size: 13px;'>
                                    <tr class='text-center'>
                                        <th>No.</th>
                                        <th>เลขที่เอกสาร</th>
                                        <th>วันที่เอกสาร</th>
                                        <th>รายการสินค้า</th>
                                        <th>จำนวน</th>
                                        <th>หน่วย</th>
                                        <th>ยอดรวมท้ายบิล</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='Tbody'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
        GetCusCode();
        CallData();
	});

    function GetCusCode() {
        $.ajax({
            url: "menus/sale/ajax/ajaxpmreport.php?a=GetCusCode",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    var option = "<option value='' selected disabled>เลือกร้านค้า</option>";
                    for(var i = 1; i <= inval['Row']; i++) {
                        option += "<option value='"+inval['Row_'+i]['CardCode']+"'>"+inval['Row_'+i]['CardCode']+" - "+inval['Row_'+i]['CardName']+"</option>";
                    }
                    $("#filt_cus").html(option);
                    $("#filt_cus").selectpicker("refresh");
                });
            }
        })
    }

    function CallData() {
        $(".overlay").show();
        $.ajax({
            url: "menus/sale/ajax/ajaxpmreport.php?a=CallData",
            type: "POST",
            data: { Year : $("#filt_year").val(), CardCode : $("#filt_cus").val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    var Tbody = "";
                    if(inval['Row'] != 0) {
                        for(var i = 1; i <= inval['Row']; i++) {
                            Tbody += "<tr>"+
                                        "<td class='text-center'>"+i+"</td>"+
                                        "<td class='text-center'>"+inval['Row_'+i]['DocNum']+"</td>"+
                                        "<td class='text-center'>"+inval['Row_'+i]['DocDate']+"</td>"+
                                        "<td>"+inval['Row_'+i]['ItemList']+"</td>"+
                                        "<td class='text-right'>"+inval['Row_'+i]['Quantity']+"</td>"+
                                        "<td class='text-center'>"+inval['Row_'+i]['UnitMsr']+"</td>"+
                                        "<td class='text-right'>"+inval['Row_'+i]['DocTotal']+"</td>"+
                                     "</tr>";
                        }
                    }else{
                        Tbody += "<tr>"+
                                    "<td colspan='7' class='text-center'>ไม่มีข้อมูล :(</td>"+
                                "</tr>";
                    }
                    $("#Tbody").html(Tbody);
                });
                $(".overlay").hide();
            }
        })
    }

    function Export() {
        console.log($("#filt_cus").val());
        if($("#filt_cus").val() != null) {
            $.ajax({
                url: "menus/sale/ajax/ajaxpmreport.php?a=Export",
                type: "POST",
                data: { Year : $("#filt_year").val(), CardCode : $("#filt_cus").val(), },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        if(inval['ExportStatus'] == 'SUCCESS') {
                            window.open("../../FileExport/PremiumReport/"+inval['FileName'],'_blank');
                        }
                    });
                } 
            })
        }else{
            $("#ModalAlert-head").html("<i class='fas fa-exclamation-circle' style='font-size: 75px;'></i>");
            $("#ModalAlert-body").html("กรุณาเลือกร้านค้าก่อน");
            $("#ModalAlert").modal("show");
        }
    }

</script> 
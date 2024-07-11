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
                            <label for="txtDocNum">เลขที่เอกสาร</label>
                            <input type="text" class='form-control form-control-sm' name='txtDocNum' id='txtDocNum'>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-primary w-100' onclick='GetListItem()'><i class="fas fa-search"></i> ค้นหา</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TableOld' style='font-size: 12px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th>No.</th>
                                        <th>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>คลัง</th>
                                        <th>จำนวน</th>
                                        <th>จำนวนที่แพ็คแล้ว</th>
                                    </tr>
                                </thead>
                                <tbody><tr><td colspan='6' class='text-center'>ไม่มีข้อมูล :(</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-auto align-content-around"><button class='btn btn-sm btn-success' title="ยืนยันรีงาน Pack" id='btn-repack' onclick='RePack()' disabled><i class="fas fa-arrow-right"></i></button></div>
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TableNew' style='font-size: 12px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th>No.</th>
                                        <th>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>คลัง</th>
                                        <th>จำนวน</th>
                                        <th>จำนวนที่แพ็คแล้ว</th>
                                    </tr>
                                </thead>
                                <tbody><tr><td colspan='6' class='text-center'>ไม่มีข้อมูล :(</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
$("#txtDocNum").keypress(function (e) {
    if (e.which == 13) {
        GetListItem();
    }
});

function GetListItem() {
    const DocNum = $("#txtDocNum").val();
    $.ajax({
        url: "menus/warehouse/ajax/ajaxreset_pack.php?a=GetListItem",
        type: "POST",
        data : { DocNum: DocNum },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                if(inval['Status'] == 'Success') {
                    $("#btn-repack").prop("disabled", false);
                    $("#TableOld tbody").html(inval['DataOld']);
                    $("#TableNew tbody").html(inval['DataNew']);
                }else{
                    $("#btn-repack").prop("disabled", true);
                    $("#TableOld tbody").html("<tr><td colspan='6' class='text-center'>ไม่มีข้อมูล :(</td></tr>");
                    $("#TableNew tbody").html("<tr><td colspan='6' class='text-center'>ไม่มีข้อมูล :(</td></tr>");
                }
            });
        }
    });
}

function RePack() {
    $("#confirm_modal").modal("show");
    $("#confirm_modal p.defult").html("หากกดยืนยันข้อมูลการแพ็คทั้งหมดจะถูก Reset<br><span class='text-danger'>หาก Reset แล้วจะไม่สามารถนำข้อมูลเก่ากลับมาได้อีก</span>");
    $(document).off("click","#btn-repack").on("click","#btn-confirm", function() {
        $("#confirm_modal").modal("hide");
        const DocNum = $("#txtDocNum").val();
        if(DocNum == '' || DocNum == null) { 
            $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
            $("#alert_body").html("กรุณากรอกเลขที่เอกสารก่อน");
            $("#alert_modal").modal('show');
        }else{
            $.ajax({
                url: "menus/warehouse/ajax/ajaxreset_pack.php?a=RePack",
                type: "POST",
                data : { DocNum: DocNum },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        if(inval['Status'] == 'Success') {
                            $("#alert_header").html("<i class=\"fas fa-check-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
                            $("#alert_body").html("รีงาน Pack สำเร็จ");
                            $("#alert_modal").modal('show');
                            GetListItem();
                        }else{
                            $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
                            $("#alert_body").html("ไม่มีเลขที่เอกสารนี้");
                            $("#alert_modal").modal('show');
                        }
                    });
                }
            });
        }
    });
}

</script> 
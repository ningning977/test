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
                    <div class="col-lg-auto">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary active" id="SO-tab" data-bs-toggle="tab" data-bs-target="#SO" type="button" role="tab" aria-controls="SO" aria-selected="false">โอนย้าย SO</button>
                                <button class="nav-link text-primary" id="IV-tab" data-bs-toggle="tab" data-bs-target="#IV" type="button" role="tab" aria-controls="IV" aria-selected="false">โอนย้าย IV</button>
                                <button class="nav-link text-primary" id="PA-PB-tab" data-bs-toggle="tab" data-bs-target="#PA-PB" type="button" role="tab" aria-controls="PA-PB" aria-selected="false">โอนย้าย PA, PB</button>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col-lg">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="SO" role="tabpanel" aria-labelledby="SO-tab">
                                <div class="row d-flex justify-content-center ">
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for="OldSO">SO เก่า</label>
                                            <input type="text" class='form-control form-control-sm' name='OldSO' id='OldSO' placeholder='SO-000000000'>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <span class='form-control form-control-sm w-100 border-0'><i class="fas fa-arrow-right"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for="NewSO">SO ใหม่</label>
                                            <input type="text" class='form-control form-control-sm' name='NewSO' id='NewSO' placeholder='SO-000000000'>
                                        </div>
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col text-center">
                                        <button class='btn btn-sm btn-success' onclick='GetDataSO()'>โอนย้ายข้อมูล</button>
                                    </div>
                                </div>

                                <div class="row pt-3 d-flex justify-content-center ">
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' id='TableOldSO' style='font-size: 11px;'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th colspan='5'>ข้อมูล SO เก่า</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <th>DocEntry</th>
                                                        <th>ลำดับ</th>
                                                        <th>รหัสสินค้า</th>
                                                        <th>ชื่อสินค้า</th>
                                                        <th>จำนวน</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' id='TableNewSO' style='font-size: 11px;'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th colspan='5'>ข้อมูล SO ใหม่</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <th>DocEntry</th>
                                                        <th>ลำดับ</th>
                                                        <th>รหัสสินค้า</th>
                                                        <th>ชื่อสินค้า</th>
                                                        <th>จำนวน</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="IV" role="tabpanel" aria-labelledby="IV-tab">
                                <div class="row d-flex justify-content-center ">
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for="OldIV">IV เก่า</label>
                                            <input type="text" class='form-control form-control-sm' name='OldIV' id='OldIV' placeholder='IV-000000000'>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <span class='form-control form-control-sm w-100 border-0'><a href='javascript:void(0);' onclick='CheckDataIV()' title='ตรวจสอบข้อมูล'><i class="fas fa-arrow-right"></i></a></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for="NewIV">IV ใหม่</label>
                                            <input type="text" class='form-control form-control-sm' name='NewIV' id='NewIV' placeholder='IV-000000000'>
                                        </div>
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col text-center">
                                        <button class='btn btn-sm btn-success' onclick='GetDataIV()'>โอนย้ายข้อมูล</button>
                                    </div>
                                </div>

                                <div class="row pt-3 d-flex justify-content-center ">
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' id='TableOldIV' style='font-size: 11px;'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th colspan='5'>ข้อมูล IV เก่า</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <th>เลขที่บิล</th>
                                                        <th>ลำดับ</th>
                                                        <th>รหัสสินค้า</th>
                                                        <th>ชื่อสินค้า</th>
                                                        <th>จำนวน</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' id='TableNewIV' style='font-size: 11px;'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th colspan='5'>ข้อมูล IV ใหม่</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <th>เลขที่บิล</th>
                                                        <th>ลำดับ</th>
                                                        <th>รหัสสินค้า</th>
                                                        <th>ชื่อสินค้า</th>
                                                        <th>จำนวน</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="PA-PB" role="tabpanel" aria-labelledby="PA-PB-tab">
                                <div class="row d-flex justify-content-center ">
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for="OldPAPB">PA, PB เก่า</label>
                                            <input type="text" class='form-control form-control-sm' name='OldPAPB' id='OldPAPB' placeholder='PA หรือ PB-000000000'>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <span class='form-control form-control-sm w-100 border-0'><i class="fas fa-arrow-right"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label for="NewPAPB">PA, PB ใหม่</label>
                                            <input type="text" class='form-control form-control-sm' name='NewPAPB' id='NewPAPB' placeholder='PA หรือ PB-000000000'>
                                        </div>
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col text-center">
                                        <button class='btn btn-sm btn-success' onclick='GetDataPAPB()'>โอนย้ายข้อมูล</button>
                                    </div>
                                </div>

                                <div class="row pt-3 d-flex justify-content-center ">
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' id='TableOldPAPB' style='font-size: 11px;'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th colspan='5'>ข้อมูล PA, PB เก่า</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <th>เลขที่บิล</th>
                                                        <th>ลำดับ</th>
                                                        <th>รหัสสินค้า</th>
                                                        <th>ชื่อสินค้า</th>
                                                        <th>จำนวน</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-bordered table-hover' id='TableNewPAPB' style='font-size: 11px;'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th colspan='5'>ข้อมูล PA, PB ใหม่</th>
                                                    </tr>
                                                    <tr class='text-center'>
                                                        <th>เลขที่บิล</th>
                                                        <th>ลำดับ</th>
                                                        <th>รหัสสินค้า</th>
                                                        <th>ชื่อสินค้า</th>
                                                        <th>จำนวน</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
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

<div class="modal fade" id="report_modal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="report_header"><i class="far fa-question-circle fa-fw fa-lg text-info"></i> ยืนยันการลบ</h5>
                <p id="report_body" class="my-4">คุณต้องการลบรายการสินค้านี้หรือไม่?</p>
                <button type="button" class="btn btn-primary btn-sm" id="btn-del-confirm" data-rowid="0" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalCheckData" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ตรวจสอบข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row pt-3 d-flex justify-content-center ">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TbodyPackHeaderOld' style='font-size: 11px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th colspan='5'>ข้อมูล pack_header เก่า</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ID</th>
                                        <th>IDPick</th>
                                        <th>BillEntry</th>
                                        <th>BillType</th>
                                        <th>DocNum</th>
                                        <th>CardCode</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TbodyPackHeaderNew' style='font-size: 11px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th colspan='5'>ข้อมูล pack_header ใหม่</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ID</th>
                                        <th>IDPick</th>
                                        <th>BillEntry</th>
                                        <th>BillType</th>
                                        <th>DocNum</th>
                                        <th>CardCode</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row pt-3 d-flex justify-content-center ">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TbodyPackBoxlistOld' style='font-size: 11px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th colspan='5'>ข้อมูล pack_boxlist เก่า</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ID</th>
                                        <th>Retails</th>
                                        <th>BillEntry</th>
                                        <th>BillType</th>
                                        <th>BoxCode</th>
                                        <th>BoxNo</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TbodyPackBoxlistNew' style='font-size: 11px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th colspan='5'>ข้อมูล pack_boxlist ใหม่</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ID</th>
                                        <th>Retails</th>
                                        <th>BillEntry</th>
                                        <th>BillType</th>
                                        <th>BoxCode</th>
                                        <th>BoxNo</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row pt-3 d-flex justify-content-center ">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TbodyPackTranOld' style='font-size: 11px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th colspan='5'>ข้อมูล pack_tran เก่า</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ID</th>
                                        <th>BillEntry</th>
                                        <th>BillType</th>
                                        <th>BoxCode</th>
                                        <th>BoxNo</th>
                                        <th>ItemCode</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TbodyPackTranNew' style='font-size: 11px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th colspan='5'>ข้อมูล pack_tran ใหม่</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ID</th>
                                        <th>BillEntry</th>
                                        <th>BillType</th>
                                        <th>BoxCode</th>
                                        <th>BoxNo</th>
                                        <th>ItemCode</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row pt-3 d-flex justify-content-center ">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TbodyPickerSoheaderOld' style='font-size: 11px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th colspan='5'>ข้อมูล picker_soheader เก่า</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ID</th>
                                        <th>SODocEntry</th>
                                        <th>DocNum</th>
                                        <th>DocType</th>
                                        <th>CardName</th>
                                        <th>StatusDoc</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered table-hover' id='TbodyPickerSoheaderNew' style='font-size: 11px;'>
                                <thead>
                                    <tr class='text-center'>
                                        <th colspan='5'>ข้อมูล picker_soheader ใหม่</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ID</th>
                                        <th>SODocEntry</th>
                                        <th>DocNum</th>
                                        <th>DocType</th>
                                        <th>CardName</th>
                                        <th>StatusDoc</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
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

    function GetDataSO() {
        const OldSO = $("#OldSO").val();
        const NewSO = $("#NewSO").val();
        $.ajax({
            url: "menus/it/ajax/ajaxtransfer_data.php?a=GetDataSO",
            type: "POST",
            data: { OldSO: OldSO, NewSO: NewSO },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $("#TableOldSO tbody").html(inval['TbodyOld']);
                    $("#TableNewSO tbody").html(inval['TbodyNew']);
                    if (inval['errCode'] == 0){
                        $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-success\"></i> สำเร็จ!");
                    }else{
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    }
                    $("#alert_body").html(inval['errMsg']);
                    $("#alert_modal").modal("show");
                });
            }
        })
    }

    function GetDataIV() {
        const OldIV = $("#OldIV").val();
        const NewIV = $("#NewIV").val();
        $.ajax({
            url: "menus/it/ajax/ajaxtransfer_data.php?a=GetDataPack",
            type: "POST",
            data: { Old: OldIV, New: NewIV, DocType: 'OINV' },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $("#TableOldIV tbody").html(inval['TbodyOld']);
                    $("#TableNewIV tbody").html(inval['TbodyNew']);

                    if (inval['errCode'] == 0){
                        $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-success\"></i> สำเร็จ!");
                    }else{
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    }
                    $("#alert_body").html(inval['errMsg']);
                    $("#alert_modal").modal("show");
                });
            }
        })
    }

    function GetDataPAPB() {
        const Old = $("#OldPAPB").val();
        const New = $("#NewPAPB").val();
        $.ajax({
            url: "menus/it/ajax/ajaxtransfer_data.php?a=GetDataPack",
            type: "POST",
            data: { Old: Old, New: New, DocType: 'ODLN' },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $("#TableOldPAPB tbody").html(inval['TbodyOld']);
                    $("#TableNewPAPB tbody").html(inval['TbodyNew']);

                    if (inval['errCode'] == 0){
                        $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-success\"></i> สำเร็จ!");
                    }else{
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    }
                    $("#alert_body").html(inval['errMsg']);
                    $("#alert_modal").modal("show");
                });
            }
        })
    }

    function CheckDataIV() {
        const OldIV = $("#OldIV").val();
        const NewIV = $("#NewIV").val();
        console.log(OldIV,NewIV);
        $.ajax({
            url: "menus/it/ajax/ajaxtransfer_data.php?a=CheckDataIV",
            type: "POST",
            data: { Old: OldIV, New: NewIV, DocType: 'OINV' },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $("#TbodyPackHeaderOld tbody").html(inval['TbodyPackHeaderOld']);
                    $("#TbodyPackHeaderNew tbody").html(inval['TbodyPackHeaderNew']);

                    $("#TbodyPackBoxlistOld tbody").html(inval['TbodyPackBoxlistOld']);
                    $("#TbodyPackBoxlistNew tbody").html(inval['TbodyPackBoxlistNew']);

                    $("#TbodyPackTranOld tbody").html(inval['TbodyPackTranOld']);
                    $("#TbodyPackTranNew tbody").html(inval['TbodyPackTranNew']);

                    $("#TbodyPickerSoheaderOld tbody").html(inval['TbodyPickerSoheaderOld']);
                    $("#TbodyPickerSoheaderNew tbody").html(inval['TbodyPickerSoheaderNew']);

                    // $("#TableNewPAPB tbody").html(inval['TbodyNew']);

                    // if (inval['errCode'] == 0){
                    //     $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg text-success\"></i> สำเร็จ!");
                    // }else{
                    //     $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    // }
                    // $("#alert_body").html(inval['errMsg']);
                    $("#ModalCheckData").modal("show");
                });
            }
        })
    }
</script> 
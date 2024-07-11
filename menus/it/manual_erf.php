<style type="text/css">
    /* .jconfirm-title-c{
        text-align: center !important;
    }
    .jconfirm-content{
        text-align: center !important;
    }
    .jconfirm-buttons{
        text-align: center !important;
    } */

    .btn-red{
        width: 100px;
    }
    .btn-default{
        width: 100px;
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
                    <div class="col">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary active" id="Tab1-tab" data-bs-toggle="tab" data-bs-target="#Tab1" type="button" role="tab" aria-controls="Tab1" aria-selected="false"><i class="fas fa-list"></i> รายการคู่มือ ERF</button>
                                <?php if($_SESSION['DeptCode'] == "DP002") { ?>
                                    <button class="nav-link text-primary" id="Tab2-tab" data-bs-toggle="tab" data-bs-target="#Tab2" type="button" role="tab" aria-controls="Tab2" aria-selected="false"><i class="fas fa-plus-square fa-fw"></i> เพิ่มคู่มือ ERF</button>
                                <?php } ?>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col">
                        <div class="tab-content" id="nav-tabContent">
                            <!-- รายการคู่มือ ERF -->
                            <div class="tab-pane fade show active" id="Tab1" role="tabpanel" aria-labelledby="Tab1-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="table-responsive" style='height: 750px;'>
                                            <table class='table table-sm table-hover table-bordered ' style='font-size: 13px;' id='Table1'>
                                                <thead>
                                                    <tr>
                                                        <th class='text-center border-top'>เลขเอกสาร</th>
                                                        <th class='text-center border-top'>ชื่อเรื่อง</th>
                                                        <th class='text-center border-top'>วันที่เผยแพร่</th>
                                                        <th class='text-center border-top'>แก้ไขครั้งที่</th>
                                                        <th class='text-center border-top'>วันที่แก้ไขล่าสุด</th>
                                                        <th width="3%" class='text-center border-top'><i class="fas fa-cog fa-fw"></i></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- เพิ่มคู่มือ ERF -->
                            <div class="tab-pane fade" id="Tab2" role="tabpanel" aria-labelledby="Tab2-tab">
                                <form class="form" id="FormCreateERF" enctype="multipart/form-data" onsubmit="return false"> 
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="DocNum">หมายเลขเอกสาร (Document No.) <span class='text-danger'>*</span></label>
                                                <input type="text" class='form-control form-control-sm ' name='DocNum' id='DocNum' placeholder='SD-IT-00'>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="ThaiName">ชื่อเรื่อง <span class='text-danger'>*</span></label>
                                                <input type="text" class='form-control form-control-sm' name='ThaiName' id='ThaiName' placeholder='ชื่อภาษาไทย'>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="EngName">ชื่อเรื่อง (อังกฤษ) <span class='text-danger'>*</span></label>
                                                <input type="text" class='form-control form-control-sm' name='EngName' id='EngName' placeholder='ชื่อภาษาอังกฤษ'>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="RevisionNum">แก้ไขครั้งที่ (Revision No.) <span class='text-danger'>*</span></label>
                                                <input type="number" class='form-control form-control-sm' name='RevisionNum' id='RevisionNum' value='0' min="0" style='background-color: #FFF !important; opacity: 1 !important;'>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="PublishDate">วันที่เผยแพร่ (Publish Date) <span class='text-danger'>*</span></label>
                                                <input type="date" class='form-control form-control-sm' name='PublishDate' id='PublishDate'>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="LatestUpdate">วันที่แก้ไขล่าสุด (Latest Update)</label>
                                                <input type="date" class='form-control form-control-sm' name='LatestUpdate' id='LatestUpdate'>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group mb-3">
                                                <label for="DeptCode">เลือกฝ่าย <span class='text-danger'>*<span></label>&nbsp;<small class="text-muted" style='font-size: 12px;'>(เลือกได้มากกว่า 1 ฝ่าย)</small>
                                                <select class="selectpicker form-control form-control-sm" name="DeptCode[]" id="DeptCode" multiple>
                                                    <option value="ALL" selected>ทุกฝ่าย</option>
                                                    <?php
                                                    $SQL = "SELECT * FROM departments WHERE DeptCode != 'DP101'";
                                                    $QRY = MySQLSelectX($SQL);
                                                    while ($result = mysqli_fetch_array($QRY)){ 
                                                        echo "<option value='".$result['DeptCode']."'>".$result['DeptName']."</option>";
                                                    } 
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="FileAttach">ไฟล์แนบ (PDF) <span class='text-danger'>*</span></label>
                                                <input type="file" class='form-control form-control-sm' name='FileAttach' id='FileAttach' accept=".pdf">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col text-right">
                                            <button class='btn btn-sm btn-primary' onclick="SaveERF();"><i class="fas fa-save fa-fw"></i> บันทึก</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit fa-fw fa-3x"></i> แก้ไขคู่มือ ERF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form" id="FormEditERF" enctype="multipart/form-data" onsubmit="return false"> 
                    <div class="row">
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label for="editDocNum">หมายเลขเอกสาร (Document No.) <span class='text-danger'>*</span></label>
                                <input type="text" class='form-control form-control-sm ' name='editDocNum' id='editDocNum'>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label for="editThaiName">ชื่อเรื่อง <span class='text-danger'>*</span></label>
                                <input type="text" class='form-control form-control-sm' name='editThaiName' id='editThaiName' placeholder='ชื่อภาษาไทย'>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label for="editEngName">ชื่อเรื่อง (อังกฤษ) <span class='text-danger'>*</span></label>
                                <input type="text" class='form-control form-control-sm' name='editEngName' id='editEngName' placeholder='ชื่อภาษาอังกฤษ'>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label for="editRevisionNum">แก้ไขครั้งที่ (Revision No.) <span class='text-danger'>*</span></label>
                                <input type="number" class='form-control form-control-sm' name='editRevisionNum' id='editRevisionNum' value='0' min="0" style='background-color: #FFF !important; opacity: 1 !important;'>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label for="editPublishDate">วันที่เผยแพร่ (Publish Date) <span class='text-danger'>*</span></label>
                                <input type="date" class='form-control form-control-sm' name='editPublishDate' id='editPublishDate'>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label for="editLatestUpdate">วันที่แก้ไขล่าสุด (Latest Update)</label>
                                <input type="date" class='form-control form-control-sm' name='editLatestUpdate' id='editLatestUpdate'>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group mb-3">
                                <label for="editDeptCode">เลือกฝ่าย <span class='text-danger'>*<span></label>&nbsp;<small class="text-muted" style='font-size: 12px;'>(เลือกได้มากกว่า 1 ฝ่าย)</small>
                                <select class="selectpicker form-control form-control-sm" name="editDeptCode[]" id="editDeptCode" multiple>
                                    <option value="ALL" selected>ทุกฝ่าย</option>
                                    <?php
                                    $SQL = "SELECT * FROM departments WHERE DeptCode != 'DP101'";
                                    $QRY = MySQLSelectX($SQL);
                                    while ($result = mysqli_fetch_array($QRY)){ 
                                        echo "<option value='".$result['DeptCode']."'>".$result['DeptName']."</option>";
                                    } 
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="form-group">
                                <label for="editFileAttach">ไฟล์แนบ (PDF)</label>
                                <input type="file" class='form-control form-control-sm' name='editFileAttach' id='editFileAttach' accept=".pdf">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button>
                <button class='btn btn-sm btn-primary' onclick="SaveEdit();"><i class="fas fa-save fa-fw"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalView" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col" id='ObjectData'></div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
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
    CallData();
    sessionStorage.setItem('DeptCode',JSON.stringify(""));
    sessionStorage.setItem('editDeptCode',JSON.stringify(""));
});

function CallData() {
    $("#Table1").dataTable().fnClearTable();
    $("#Table1").dataTable().fnDraw();
    $("#Table1").dataTable().fnDestroy();
    $("#Table1").DataTable({
        "ajax": {
          url: "menus/it/ajax/ajaxmanual_erf.php?a=CallData",
          type: "POST",
          dataType: "json",
          dataSrc: "0"
        },
        "columns": [
            { "data": "DocNum", class: "dt-body-center border-start border-bottom" },
            { "data": "Name", class: "border-start border-bottom" },
            { "data": "PublishDate", class: "dt-body-center border-start border-bottom" },
            { "data": "RevisionNum", class: "dt-body-center border-start border-bottom" },
            { "data": "LatestUpdate", class: "dt-body-center border-start border-bottom" },
            { "data": "BTN", class: "dt-body-center border-start border-bottom border-end" },
        ],
        "columnDefs": [
            { "width": "10%", "targets": 0 },
            { "width": "40%", "targets": 1 },
            { "width": "10%", "targets": 2 },
            { "width": "5%",  "targets": 3 },
            { "width": "10%", "targets": 4 },
            { "width": "5%",  "targets": 5 },
        ],
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "pageLength": 15,
        "bInfo": false,
        "ordering": false,
        "language":{ 
                "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
        },
    });
}

function ViewDoc(File,Name,FileOri) {
    $("#ObjectData").html(
        "<object data='../../../FileAttach/MANUAL/"+File+"' type='application/pdf' width='100%' height='800px'>"+
            "<p>กรุณากดดาวน์โหลดไฟล์ PDF</p>"+
        "</object>"
    );
    $("#ModalView .modal-title").html("<i class='fas fa-atlas fa-fw'></i> คู่มือ : "+Name);
    $("#ModalView .modal-footer").html(
        "<button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>ออก</button>"+
        "<button type='button' class='btn btn-primary btn-sm' onclick='DownloadPDF(\""+File+"\",\""+FileOri+"\");'><i class='fas fa-file-pdf'></i> PDF</button>"
    );
    $("#ModalView").modal("show");
}

function DownloadPDF(FileName,FileOriName) {
    let req = new XMLHttpRequest();
    req.open("GET", "../../../FileAttach/MANUAL/"+FileName, true);
    req.responseType = "blob";
    req.onload = function (event) {
        let blob = req.response;
        let link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = FileOriName;
        link.click();
    };
    req.send();
}

function EditData(ID) {
    sessionStorage.setItem('tmpID',JSON.stringify(ID));
    $.ajax({
        url: "menus/it/ajax/ajaxmanual_erf.php?a=EditData",
        type: "POST",
        data: { ID : ID, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#editDocNum").val(inval['DocNum']);
                $("#editThaiName").val(inval['ThaiName']);
                $("#editEngName").val(inval['EngName']);
                $("#editPublishDate").val(inval['PublishDate']);
                $("#editRevisionNum").val(inval['RevisionNum']);
                $("#editLatestUpdate").val(inval['LatestUpdate']);
                $("#editDeptCode").selectpicker('val',inval['DeptCode']);
                $("#editFileAttach").val("");
                $("#ModalEdit").modal("show");
            });
        } 
    })
}

function SaveEdit() {
    let Error = 0;
    $("#editDocNum, #editThaiName, #editEngName, #editPublishDate, #editLatestUpdate, #editFileAttach").removeClass("is-invalid");
    if($("#editDocNum").val() == "") { 
        $("#editDocNum").addClass("is-invalid"); 
        Error++;
    }
    if($("#editThaiName").val() == "") { 
        $("#editThaiName").addClass("is-invalid"); 
        Error++;
    }
    if($("#editEngName").val() == "") { 
        $("#editEngName").addClass("is-invalid"); 
        Error++;
    }
    if($("#editPublishDate").val() == "") { 
        $("#editPublishDate").addClass("is-invalid"); 
        Error++;
    }
    if($("#editDeptCode").val().length == 0) {
        Error++;
    }

    if(Error == 0) {
        let FormEditERF = new FormData($("#FormEditERF")[0]);
        FormEditERF.append('ID',JSON.parse(sessionStorage.getItem('tmpID')));
        $.ajax({
            url: "menus/it/ajax/ajaxmanual_erf.php?a=SaveEdit",
            type: 'POST',
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: FormEditERF,
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#editDocNum, #editThaiName, #editEngName, #editPublishDate, #editFileAttach").val("");
                    $("#editRevisionNum").val("0");
                    $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 60px;'></i>");
                    $("#alert_body").html("บันทึกข้อมูลสำเร็จ");
                    $("#alert_modal").modal("show");
                    $("#ModalEdit").modal("hide");
                    CallData();
                });
            }
        });
    }
}

function DeleteData(ID) {
    $.confirm({
        title: "ต้องการลบรายการคู่มือ ERF นี้หรือไม่?",
        content: '',
        type: 'red',
        typeAnimated: true,
        buttons: {
            tryAgain: {
                text: 'ลบ',
                btnClass: 'btn-red',
                action: function(){
                    $.ajax({
                        url: "menus/it/ajax/ajaxmanual_erf.php?a=DeleteData",
                        type: "POST",
                        data: { ID : ID, },
                        success: function(result) {
                            const obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) {
                                CallData();
                            });
                        } 
                    })
                }
            },
            ออก: function () {}
        }
    });
}

function SaveERF() {
    let Error = 0;
    $("#DocNum, #ThaiName, #EngName, #PublishDate, #LatestUpdate, #FileAttach").removeClass("is-invalid");
    if($("#DocNum").val() == "") { 
        $("#DocNum").addClass("is-invalid"); 
        Error++;
    }
    if($("#ThaiName").val() == "") { 
        $("#ThaiName").addClass("is-invalid"); 
        Error++;
    }
    if($("#EngName").val() == "") { 
        $("#EngName").addClass("is-invalid"); 
        Error++;
    }
    if($("#PublishDate").val() == "") { 
        $("#PublishDate").addClass("is-invalid"); 
        Error++;
    }
    if($("#FileAttach").val() == "") { 
        $("#FileAttach").addClass("is-invalid"); 
        Error++;
    }
    if($("#DeptCode").val().length == 0) {
        Error++;
    }

    if(Error == 0) {
        const FormCreateERF = new FormData($("#FormCreateERF")[0]);
        $.ajax({
            url: "menus/it/ajax/ajaxmanual_erf.php?a=SaveERF",
            type: 'POST',
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: FormCreateERF,
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#DocNum, #ThaiName, #EngName, #PublishDate, #FileAttach").val("");
                    $("#RevisionNum").val("0");
                    $("#DeptCode").selectpicker('val',['ALL']);
                    $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 60px;'></i>");
                    $("#alert_body").html("บันทึกข้อมูลสำเร็จ");
                    $("#alert_modal").modal("show");
                    CallData();
                });
            }
        });
    }
}

$("#DeptCode").on("change", function() {
    var slct_value = $(this).val();
    if(slct_value.length != 1) {
        const slct_value2 = slct_value[1];
        if(slct_value[0] == "ALL") {
            if(JSON.parse(sessionStorage.getItem('DeptCode')) == "") {
                $(this).selectpicker('val',[]);
                $(this).selectpicker('val',[slct_value2]);
                sessionStorage.setItem('DeptCode',JSON.stringify($(this).val()[0]));
            } else {
                if($(this).val()[0] == "ALL") {
                    $(this).selectpicker('val',[]);
                    $(this).selectpicker('val',['ALL']);
                    sessionStorage.setItem('DeptCode',JSON.stringify(""));
                }
            }
        }
    }
});

$("#editDeptCode").on("change", function() {
    var slct_value = $(this).val();
    if(slct_value.length != 1) {
        const slct_value2 = slct_value[1];
        if(slct_value[0] == "ALL") {
            if(JSON.parse(sessionStorage.getItem('editDeptCode')) == "") {
                $(this).selectpicker('val',[]);
                $(this).selectpicker('val',[slct_value2]);
                sessionStorage.setItem('editDeptCode',JSON.stringify($(this).val()[0]));
            } else {
                if($(this).val()[0] == "ALL") {
                    $(this).selectpicker('val',[]);
                    $(this).selectpicker('val',['ALL']);
                    sessionStorage.setItem('editDeptCode',JSON.stringify(""));
                }
            }
        }
    }
});
</script> 
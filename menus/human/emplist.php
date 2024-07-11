<style type="text/css">

</style>
<?php
    echo "<input type='hidden' id='HeadeMenuLink' value = '".$_GET['p']."'>";
?>
<div class="page-heading">
    <h3><span id='header1'></h3>
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
                <h4><span id='header2'></h4>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-lg">
                        <button type="button" class="btn btn-success btn-sm mt-4" onClick=AddNew()><i class="fa fa-plus fa-fw"></i>เพิ่มพนักงานใหม่</button>
                    </div>
                    <div class="col-md-3">
                        <label for="InputCHK"> ค้นหาค้อมูล: </label>
                        <input type="text" id="FindData" class="form-control" placeholder="กรอกข้อมูลเพื่อค้นหา">
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mt-4">
                            <thead class="text-center">
                                <tr>
                                    <th width="7.5%">No</th>
                                    <th width="15.5%">รหัสพนักงาน</th>
                                    <th width="15.5%">ชื่อพนักงาน</th>
                                    <th width="15.5%">ตำแหน่ง</th>
                                    <th width="15.5%">ฝ่าย</th>
                                    <th width="20%">ตัวจัดการ</th>
                                </tr>
                            </thead>
                            <tbody id='EmpList'></tbody>
                        </table> 
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="myModalCon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">ยืนยันการลบข้อมูล</h4>
            </div>
            <div class="modal-body">
                    <label for="InputCHK"> กรอกรหัสยืนยัน: </label><span style="font-size: 22px; color:#FF0000;font-weight: bold;" id="NumRandom"> &nbsp; &nbsp;</span>
                    <input type="number" id="InputCHK" value="" class="form-control form-control-lg text-center" placeholder="กรอกรหัสยืนยัน" />
                    <input type="hidden" id="RandomCHK" value="" />
                    <input type="hidden" id="ComKey" value="" />
                    <input type="hidden" id="TypeCon" value="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm close "  data-bs-dismiss="modal"><i class="fa fa-times fa-fw"></i>ปิด</button>
                <button type="button" name="ConMenu_menu" class="btn btn-primary btn-sm" onClick="ConF()" ><i class="fa fa-save fa-fw"></i>ยืนยัน</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><i class="fas fa-user-plus fa-fw fa-1x"></i> เพิ่มพนักงานใหม่</h4>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="addEmp">
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3 mb-3">
                        <label for="EmpCode" class="form-control-label">รหัสพนักงาน</label>
                        <input type="text" name="EmpCode" id="EmpCode" class="form-control" placeholder="ค้นหารหัสพนักงาน" />
                    </div>
                    <div class="col-lg-2 mb-3">
                        <label for="SearchEmp" class="form-control-label">&nbsp;</label>
                        <button type="button" name="SearchEmp" id="SearchEmp" class="btn btn-info form-control" onClick="FindHRMI()"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</button>
                    </div>
                    <div class="col-lg-2 mb-3">
                        <label for="" class="form-control-label">&nbsp;</label>
                        <button type='button' class='btn form-control btn-outline-info ' id='btn_PrintLists' onclick='PrintLists();'><i class='fas fa-print fa-fw fa-1x'></i> พิมพ์</button> 
                    </div>
                </div>
                <hr/>
                <!-- ชื่อ นามสกุล ชื่อเล่น (ไทย) -->
                <div class="row">
                    <div class="col-lg-5">
                        <div class="form-floating mb-3">
                            <input type="text" name="uName" id="uName" class="form-control" placeholder="ชื่อ" />
                            <label for="uName">ชื่อ (ไทย)</label>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-floating mb-3">
                            <input type="text" name="uLastName" id="uLastName" class="form-control" placeholder="นามสกุล" />
                            <label for="uLastName">นามสกุล (ไทย)</label>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-floating mb-3">
                            <input type="text" name="uNickName" id="uNickName" class="form-control" placeholder="ชื่อเล่น" />
                            <label for="uNickName">ชื่อเล่น</label>
                        </div>
                    </div>
                </div>
                <!-- ชื่อ นามสกุล (อังกฤษ) เพศ -->
                <div class="row">
                    <div class="col-lg-5">
                        <div class="form-floating mb-3">
                            <input type="text" name="en_name" id="en_name" class="form-control" placeholder="ชื่อ" />
                            <label for="name">ชื่อ (อังกฤษ)</label>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-floating mb-3">
                            <input type="text" name="en_lastname" id="en_lastname" class="form-control" placeholder="นามสกุล" />
                            <label for="lastname">นามสกุล (อังกฤษ)</label>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="UserGender" name="UserGender">
                                <option value="M">ชาย</option>
                                <option value="F">หญิง</option>
                            </select>
                            <label for="UserGender">เพศ</label>
                        </div>
                    </div>
                </div>
                <!-- วันเดือนปีเกิด -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="text" name="UserName" id="UserName" class="form-control" placeholder="Username" readonly />
                            <label for="UserName">Username</label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="date" name="user_birthdate" id="user_birthdate" class="form-control" placeholder="วันเกิด" />
                            <label for="lastname">วันเกิด (ดด/วว/ปปปป)</label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="user_name" class="form-control-label">ลายเซ็น</label>
                        <input type="file" name="UserSign" id="UserSign" class="form-control">
                    </div>
                </div>
                <!-- ฝ่าย ตำแหน่ง -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="departments" name="departments">
                            <?php 
                                $sql1 = "SELECT * FROM departments ORDER BY DeptCode";
                                $getDept = MySQLSelectX($sql1);	
                                echo "<option value='' disabled selected></option>";
                                while ($DeptList = mysqli_fetch_array($getDept)){
                                    echo "<option value='".$DeptList['DeptCode']."'>".$DeptList['DeptName']."</option>";
                                }
                            ?>
                            </select>
                            <label for="departments">ฝ่าย</label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="positions" name="positions" disabled></select>
                            <label for="positions">ตำแหน่ง</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-floating mb-3">
                            <!---<input type="text" name="savemoney" id="savemoney" class="form-control text-right" readonly value='-' />--->
                            <select class="form-select" id="savemoney" name="savemoney" disabled>
                            </select>                                
                            <input type="hidden" name="SAPCode" id="SAPCode" class="form-control text-right" readonly value='-' />
                            <label for="kmoney">เงินคำประกัน</label>
                        </div>
                    </div>
                    <div class="col-lg-6" >
                        <div class="form-floating mb-3">   
                            <select class='selectpicker form-control ' name="OwnerCode" id="OwnerCode" data-live-search="true"></select>                            
                            <label for="OwnerCode">OwnerCode</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id='TypeCommand' name='TypeCommand' value="0">
                <input type="hidden" id='uKey' name='uKey' value="">
                <button type="button" class="btn btn-secondary btn-sm close "  data-bs-dismiss="modal"><i class="fa fa-times fa-fw"></i>ปิด</button>
                <button type="submit" name="save_menu" id= "save_menu" class="btn btn-primary btn-sm" ><i class="fa fa-save fa-fw"></i>บันทึก</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        CallHeade();
        CallData();
        $("#FindData").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#EmpList tr").filter(function() {    
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        $('#departments').change(function(){
            CallPosi();
        });
	});
    $("#addEmp").on('submit',function(e){
        e.preventDefault();
        $(".overlay").show();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=add",
            type: "POST",
            data: formData,   
            processData: false,
            contentType: false,
            success: function(result){
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval){
                    alert(inval['output']);
                    $('#myModal').modal("hide");
                });
                CallData();
                $(".overlay").hide();
            }   
        });
    });
</script> 

<script>
	$(document).ready(function(){
    $("#myInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
	});
</script>

<script type="text/javascript">
    function CallHeade(){
        $(".overlay").show();
        //แก้   URL ajax เอง
        var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=head",
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
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=read",
            type: "POST",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#EmpList").html(inval["output"]);
                });
                $(".overlay").hide();
                $(".btn-edit").on("click",function(e){
                    e.preventDefault();
                    var uKey = $(this).attr("data-UserKey");
                    var DeptCode = $(this).attr("data-Dept");
                    EditData(uKey,DeptCode);
                });
                $(".btn-resign").on("click",function(e){
                    e.preventDefault();
                    var uKey = $(this).attr("data-UserKey");
                    ChkCommand(uKey,'resign');
                });
                $(".btn-reset").on("click",function(e){
                    e.preventDefault();
                    var uKey = $(this).attr("data-UserKey");
                    ChkCommand(uKey,'reset');
                });
            }
        });
    }
    function CallPosi(){
        var Dept = $('#departments').val();
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=posi",
            type: "POST",
            data : {DeptCode : Dept,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#positions').empty();
                    $('#positions').removeAttr('disabled','disabled');
                    $("#positions").append("<option value='' selected></option>'");  
                    for (var ai=1;ai<=inval['Loop'];ai++){
                        $("#positions").append("<option value='"+obj[0][ai]["LvCode"]+"' >"+obj[0][ai]['PosiName']+'</option>');  
                    }
                });
            }
        });
    }
    function AddNew(){
        $(".overlay").show();
        $("#EmpCode").val("");
        $("#uName").val("");
        $("#uLastName").val("");
        $("#uNickName").val("");
        $("#en_name").val("");
        $("#en_lastname").val("");
        $("#UserGender").val("");
        $("#UserName").val("");
        $("#user_birthdate").val("");
        $("#UserSign").val("");
        $("#position").val("");
        $("#TypeCommand").val(0);
        $("#uKey").val("");
        $('#myModal').modal("show");
        $(".overlay").hide();
        $('#savemoney').empty();
        $('#savemoney').removeAttr('disabled','disabled');
    }

    function FindHRMI(){
        var EmpCode = $('#EmpCode').val();
        $(".overlay").show();
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=hrmi",
            type: "POST",
            data : {EmpCode : EmpCode,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#uName").val(obj[0][0]['fname']);
                    $("#uLastName").val(obj[0][0]['lname']);
                    $("#uNickName").val(obj[0][0]['nname']);
                    $("#en_name").val(obj[0][0]['efname']);
                    $("#en_lastname").val(obj[0][0]['elname']);
                    $("#UserName").val(obj[0][0]['uname']);
                    $("#user_birthdate").val(obj[0][0]['bdate']);
                    $("#UserGender option[value="+obj[0][0]['Gender']+"]").prop('selected', true);
                    $("#departments option[value="+obj[0][0]['DeptCode']+"]").prop('selected', true);
                    CallPosi();
                    $("#savemoney").empty();
                    if (obj[0][0]['Loop'] != 0){
                        for (var ai=1;ai<=obj[0][0]['Loop'];ai++){
                            $("#savemoney").append("<option value='"+obj[0][ai]["CardCode"]+"' >["+obj[0][ai]["CardCode"]+"] "+obj[0][ai]['CardName']+'</option>');  
                        }
                    }else{
                        $('#savemoney').attr('disabled','disabled');
                    }
                    if (obj[0][0]['EmpStatus'] == 'I'){
                        alert("พนักงานออกแล้ว ไม่สามารถเพิ่มข้อมูลได้");
                        $('#save_menu').attr('disabled','disabled');
                    }else{
                        $('#save_menu').removeAttr('disabled','disabled');   
                    }
                });
                $(".overlay").hide();
            }
        });
    }
    function EditData(x,y){
        $(".overlay").show();
        //console.log(y);
        //$("#departments option[value="+y+"]").prop('selected', true);
        // console.log(x, y);
        CallPosi();
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=edit",
            type: "POST",
            data : {x : x,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#TypeCommand').val(1);
                    $('#uKey').val(inval['uKey']);
                    $('#EmpCode').val(inval['EmpCode']);
                    $("#uName").val(inval['fname']);
                    $("#uLastName").val(inval['lname']);
                    $("#uNickName").val(inval['nname']);
                    $("#en_name").val(inval['efname']);
                    $("#en_lastname").val(inval['elname']);
                    $("#UserName").val(inval['uname']);
                    $("#UserSign").val("");
                    $("#user_birthdate").val(inval['bdate']);
                    //$("#savemoney").val(inval['Money']);
                    //$("#SAPCode").val(inval['SAPCode']);
                    $("#UserGender option[value="+inval['Gender']+"]").prop('selected', true);
                    $("#departments option[value="+inval['DeptCode']+"]").prop('selected', true);
                    $("#positions").append("<option value='"+inval['LvCode']+"' >"+inval['PosiName']+'</option>');  
                    $("#positions option[value="+inval['LvCode']+"]").prop('selected', true);
                    if (inval['Money'] == '-'){
                        $('#btn_PrintLists').attr('disabled','disabled');
                    }else{
                        $('#btn_PrintLists').removeAttr('disabled','disabled');  
                    }
                    $('#savemoney').empty();
                    $("#savemoney").append("<option value='"+inval["SAPCode"]+"' >"+inval["Money"]+"</option>'");  

                    // OwnerCode
                    $("#OwnerCode").empty().selectpicker('destroy');
                    $("#OwnerCode").html(inval['OwnerCode']).val(inval['OwnerCodeUser']).change().selectpicker();
                });
                $('#myModal').modal("show");
                $(".overlay").hide();
            } 
        });
    }
    function PrintLists() {
        var SAPCode = $("#savemoney").val();
        window.open ('menus/human/print/printList.php?OCRD='+SAPCode,'_blank');
    }
    function ChkCommand(x,y){
        var a = Math.floor(Math.random().toFixed(2)*100);
        if (a < 10){
            a = "0"+a;
        }
        $('#ComKey').val(x);
        $('#InputCHK').val("");
        $('#RandomCHK').val(a);
        $('#NumRandom').html(a);
        $('#TypeCon').val(y);
        $('#myModalCon').modal("show");
        $('#InputCHK').focus();
    }
    function ConF(){
        $(".overlay").show();
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=recon",
            type: "POST",
            data : {uKey : $('#ComKey').val(),
                    TypeCon :$('#TypeCon').val(), 
                   },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                   alert(inval['output']);
                });
                $('#myModalCon').modal("hide");
                $(".overlay").hide();
            } 
        });

    }



</script> 
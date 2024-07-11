<style type="text/css">
    .hiderow {
        display:none;
    }
    /* The container */
.container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #9A1118;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>

<div class="page-heading">
    <h3><i class="fas fa-cogs fa-fw fa-1x"></i><span id='header'> ตั้งค่าเมนู</span></h3>
</div>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br/><br/>
        กำลังโหลด...
    </div>
</div>
<hr>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มเมนู</h4>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="level_menu">ระดับเมนู</label>
                    <select name="level_menu" id="level_menu" class="form-select">
                        <option value="0" selected>เมนูหลัก (ระดับ 0)</option>
                        <option value="1">เมนูรอง 1 (ระดับ 1)</option>
                        <option value="2">เมนูรอง 2 (ระดับ 2)</option>
                    </select>
                </div>
                <div class="form-group hidemainmenu">
                    <label for="main_menu">เมนูหลัก</label>
                    <select name="main_menu" id="main_menu" class="form-select"  disabled="disabled">
                        <option value=""></option>
                    </select>
                </div>
                <div class="form-group hidesubmenu">
                    <label for="sub_menu">เมนูรอง</label>
                    <select name="sub_menu" id="sub_menu" class="form-select"  disabled="disabled">
                        <option value=""></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="menu_name">ชื่อเมนู (ตัวแปรสำหรับแปลภาษา)</label>
                    <input type="text" name="menu_name" id="menu_name" class="form-control" autofocus>
                </div>
                <div class="form-group">
                    <label for="menu_icon">ไอคอน</label>
                    <input type='text' name="menu_icon" id="menu_icon" class="form-control" >
                    <span stype="color:#FF0000;"> *ไม่สามารถใช้ ' ได้</span>
                </div>
                <div class="form-group">
                    <label for="menu_case">case</label>
                    <input type="text" name="menu_case" id="menu_case" class="form-control" >
                </div>
                <div class="form-group">
                    <label for="menu_link">ลิงค์</label>
                    <input type="text" name="menu_link" id="menu_link" class="form-control" >
                </div>
                <div class="form-group">
                        <label for="menu_sorting">ลำดับ</label>
                        <input type="text" name="menu_sorting" id="menu_sorting" class="form-control" >
                </div>
                <div class="form-gvroup">
                        <label for="menu_status">สถานะ</label>
                        <select name="menu_status" id="menu_status" class="form-select">
                            <option value="A" >แสดง</option>
                            <option value="I">ไม่แสดง</option>
                        </select>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id='TypeCommand' value="0">
                <input type="hidden" id='Menu_key' value="">
                <button type="button" class="btn btn-secondary btn-sm close "  data-bs-dismiss="modal"><i class="fa fa-times fa-fw"></i>ปิด</button>
                <button type="button" name="save_menu" class="btn btn-primary btn-sm" onClick=SaveMenu()><i class="fa fa-save fa-fw"></i>บันทึก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalCon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <input type="hidden" id="KeyDelete" value="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm close "  data-bs-dismiss="modal"><i class="fa fa-times fa-fw"></i>ปิด</button>
                <button type="button" name="ConMenu_menu" class="btn btn-primary btn-sm" onClick="DelRow()" ><i class="fa fa-save fa-fw"></i>ยืนยัน</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalPermiss" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                
                <h4 class="modal-title" id="myModalLabel">กำหนดสิทธิ์การเข้าถึง (Permission)</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body" id="BodyPermiss">
            <!--- Ajack Data --->
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm"  data-bs-dismiss="modal"><i class="fa fa-times fa-fw"></i>ปิด</button>
            </div>
        </div>
    </div>
</div>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-list fa-fw"></i> ตั้งค่าเมนู</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg">
                        <button type="button" class="btn btn-success btn-sm mt-4" onClick=AddNew()><i class="fa fa-plus fa-fw"></i> เพิ่มเมนู</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mt-4">
                        <thead class="text-center">
                            <tr>
                                <th width="7.5%">No</th>
                                <th colspan='2' width="67.5%">ชื่อเมนู</th>
                                <th width="20%">ตัวจัดการ</th>
                            </tr>
                        </thead>
                        <tbody id='MenuList'></tbody>
                    </table>    
                </div>
            </div>
        </div>
    </div>
</section>

<!--<button type="button" class="btn btn-danger btn-xs" id="" onClick=""><i class="fa fa-lock" id=""></i> <span id="">ซ่อน</span></button>';-->
<script type="text/javascript">
	$(document).ready(function(){
		CallData();
        $('#level_menu').change(function(){
            CallMainMenu();
        });
        $('#main_menu').change(function(){
            var LvMenu = $('#level_menu').val(); 
            if (LvMenu == 2){
                AddOption(2);
            }
        });
	});
</script> 
<script type="text/javascript">
    function CallData(){
        $.ajax({
            url: "setting/ajax/ajaxMenu.php?a=read",
            type: "POST",
            success: function(result) {
                $(".overlay").show();
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#MenuList").html(inval["output"]);
                });
                $(".overlay").hide();

                $(".btn-more").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    var MainKey = $(this).attr("data-MainKey");
                    $("tr[data-UpKey='"+MenuKey+"']").toggleClass("hiderow");
                });
                $(".btn-view").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    var st = $(this).attr("data-status");
                    viewData(st,MenuKey);
                });
                $(".btn-delete").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    chkDelete(MenuKey);
                });
                $(".btn-edit").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    EditData(MenuKey);
                });
                $(".btn-permission").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    Permiss(MenuKey);
                });

            }
        });
    }
    function AddNew(){
        $("#level_menu").val(0);
        $('#main_menu').empty();
        $('#sub_menu').empty();
        $("#menu_name").val("");
        $("#menu_icon").val("");
        $("#menu_case").val("");
        $("#menu_link").val("");
        $("#menu_sorting").val(1);
        $("#menu_status").val("A");
        $("#menu_key").val("");
        $('#TypeCommand').val(0);
        $("#myModal").modal("show");
        
    }
    function CallMainMenu(){
        var LvMenu = $('#level_menu').val();
        var Read = 0;
        switch (LvMenu){
            case '0' :
                $('#main_menu').find('option').remove();
                $('#main_menu').attr('disabled','disabled');
                $('#sub_menu').find('option').remove();
                $('#sub_menu').attr('disabled','disabled');
            break;
            case '1' :
                $('#main_menu').find('option').remove();
                $('#main_menu').removeAttr('disabled','disabled');
                $('#sub_menu').find('option').remove();
                $('#sub_menu').attr('disabled','disabled');
                AddOption(1);
            break;
            case '2' :
                $('#main_menu').find('option').remove();
                $('#main_menu').removeAttr('disabled','disabled');
                $('#sub_menu').find('option').remove();
                $('#sub_menu').removeAttr('disabled','disabled');
                AddOption(1);
                AddOption(2);
            break;
        }
    }
    function AddOption(LvMenu){
        $(".overlay").show();
        if (LvMenu == "2"){
            var MainMenu = $('#main_menu').val();
        }else{
            var MainMenu = "";
        }
        $.ajax({
            url: "setting/ajax/ajaxMenu.php?a=callmainmenu",
            type: "POST",
            data: {LvMenu : LvMenu,
                   MainMenu : MainMenu},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    var ai;
                    var dis = "";
                    for (ai=1;ai<=inval['Loop'];ai++){
                        if ((ai == 1 || ai == (inval['Loop'] - 1) || ai == inval['Loop']) && inval['output'] == "main_menu"){
                            dis = " disabled ";
                        }else{
                            dis = " ";
                        }
                        $("#"+inval["output"]).append("<option value='"+obj[0][ai]["MenuKey"]+"' "+dis+" >"+obj[0][ai]['MenuName']+'</option>');  
                    }
                });
                $(".overlay").hide();
            }
        });
    }
    function SaveMenu(){
        var chkMenuName = $('#menu_name').val();
        var chkMenuSort = $('#menu_sorting').val();
        var xRun = 0;
        $(".overlay").show();
        if (chkMenuName == ""){
            alert("ระบุชื่อเมนู");
        }else{
            if (chkMenuSort > 0  && chkMenuSort < 998){
                xRun = 1;
            }else{
                alert("ลำดับ ใช้ได้ ตั้งแต่ 1 - 997");
            }
        }
        if (xRun == 1){
            $.ajax({
                url: "setting/ajax/ajaxMenu.php?a=save",
                type: "POST",
                data: { typeCom : $("#TypeCommand").val(),
                        MenuKey : $("#Menu_key").val(),
                        MenuLv : $("#level_menu").val(),
                        MainMemu: $("#main_menu").val(),
                        SubMenu : $("#sub_menu").val(),
                        MenuName: $("#menu_name").val(),
                        MenuIcon: $("#menu_icon").val(),
                        MenuCase: $("#menu_case").val(),
                        MenuLink: $("#menu_link").val(),
                        MenuSort: $("#menu_sorting").val(),
                        MenuStatus: $("#menu_status").val(),
                    },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        alert(inval["output"]);
                        $("#myModal").modal("hide");
                        CallData();
                    });
                    $(".overlay").hide();
                }

            });
        }
    }
    function viewData(x,y){
        $(".overlay").show();
        $.ajax({
            url: "setting/ajax/ajaxMenu.php?a=view",
            type: "POST",
            data: { typeCom : x,
                    MenuKey : y,
                    },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    CallData();
                });
                $(".overlay").hide();
            }
        });
    }
    function EditData(x){
        $(".overlay").show();
        $.ajax({
                url: "setting/ajax/ajaxMenu.php?a=edit",
                type: "POST",
                data: { MenuKey : x,
                      },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        var ai;var dis1 = "";var sel1 = "";var sel2 = "";
                        $('#main_menu').empty();
                        $('#sub_menu').empty();
                        $('#TypeCommand').val(1);
                        $("#Menu_key").val(inval['MenuKey']);
                        $("#level_menu option[value="+inval['MenuLv']+"]").prop('selected', true);
                        $('#main_menu').removeAttr('disabled','disabled');
                        for (ai=1;ai<=inval['LoopLv1'];ai++){
                            if (ai == 1 || ai == (inval['LoopLv1'] - 1) || ai == inval['LoopLv1']){
                                dis1 = " disabled ";
                            }else{
                                dis1 = " ";
                            }
                            if (obj[0][ai]["MenuKeyLv1"] == inval['MenuLv1']){
                                sel1 = " selected ";
                            }else{
                                sel1 = " ";
                            }
                            $("#main_menu").append("<option value='"+obj[0][ai]["MenuKeyLv1"]+"' "+dis1+sel1+"  >"+obj[0][ai]['MenuNameLv1']+'</option>');  
                        }
                        if (inval['LoopLv2'] > 0){
                            $('#sub_menu').removeAttr('disabled','disabled');
                            for (ai=1;ai<=inval['LoopLv2'];ai++){
                                if (obj[0][ai]["MenuKeyLv2"] == inval['MenuLv2']){
                                    sel2 = " selected ";
                                }else{
                                    sel2 = " ";
                                }
                                $("#sub_menu").append("<option value='"+obj[0][ai]["MenuKeyLv2"]+"' "+sel2+"  >"+obj[0][ai]['MenuNameLv2']+'</option>');  
                            }
                        }
                        $("#menu_name").val(inval['MenuName']);
                        $("#menu_icon").val(inval['MenuIcon']);
                        $("#menu_case").val(inval['MenuCase']);
                        $("#menu_link").val(inval['MenuLink']);
                        $("#menu_sorting").val(inval['MenuSort']);
                        $("#menu_status option[value="+inval['MenuStatus']+"]").prop('selected', true);
                        $("#myModal").modal("show");
                    });
                    $(".overlay").hide();
                }
            });
    }
    function chkDelete(x){
        var a = Math.floor(Math.random().toFixed(2)*100);
        if (a < 10){
            a = "0"+a;
        }
        $('#KeyDelete').val(x);
        $('#InputCHK').val("");
        $('#RandomCHK').val(a);
        $('#NumRandom').html(a);
        $('#myModalCon').modal("show");
        $('#InputCHK').focus();
    }
    function DelRow(){
        var addKey = $('#InputCHK').val();
        var chkKey = $('#RandomCHK').val();
        var MenuKey = $('#KeyDelete').val();
        $(".overlay").show();
        if (addKey == chkKey && MenuKey != ""){
            $.ajax({
                url: "setting/ajax/ajaxMenu.php?a=del",
                type: "POST",
                data: { MenuKey : MenuKey,
                      },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $('#myModalCon').modal("hide");
                        alert(inval['output']);
                        CallData();
                    });
                    $(".overlay").hide();
                }
            });
       }else{
            alert("ข้อมูลไม่ถูกต้อง กรุณาลองใหม่");
            $('#myModalCon').modal("hide");
        }
    }
    function Permiss(x){
        $(".overlay").show();
        $.ajax({
            url: "setting/ajax/ajaxMenu.php?a=permiss",
            type: "POST",
            data: { MenuKey : x,
                    },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#BodyPermiss').html(inval['output']);
                    $('#ModalPermiss').modal("show");
                });
                $(".overlay").hide();
            }
        });
    }

</script> 



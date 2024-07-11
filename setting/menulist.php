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
    <h3><i class="fas fa-link fa-fw fa-1x"></i><span id='header'> ลิงค์หน้าเมนู</span></h3>
</div>
<hr>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br/><br/>
        กำลังโหลด...
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มลิงค์หน้าเมนู</h4>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="menu_name">Case</label>
                    <input type="text" name="MenuCase" id="MenuCase" class="form-control" autofocus>
                </div>
                <div class="form-group">
                    <label for="menu_icon">MenuGroup</label>
                    <input type='text' name="MenuGroup" id="MenuGroup" class="form-control" >
                </div>
                <div class="form-group">
                    <label for="menu_case">Pages</label>
                    <input type="text" name="urlPages" id="urlPages" class="form-control" >
                </div>
                <div class="form-gvroup">
                        <label for="menu_status">สถานะ</label>
                        <select name="CaseStatus" id="CaseStatus" class="form-select">
                            <option value="A" >แสดง</option>
                            <option value="I">ไม่แสดง</option>
                        </select>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id='TypeCommand' value="0">
                <input type="hidden" id='CaseID' value="">
                <button type="button" class="btn btn-default btn-sm close "  data-bs-dismiss="modal"><i class="fa fa-times fa-fw"></i>ปิด</button>
                <button type="button" name="save_menu" class="btn btn-primary btn-sm" onClick="SaveCase()"><i class="fa fa-save fa-fw"></i>บันทึก</button>
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
                <button type="button" name="ConMenu_menu" class="btn btn-primary btn-sm" onClick="DelData()" ><i class="fa fa-save fa-fw"></i>ยืนยัน</button>
            </div>
        </div>
    </div>
</div>

<section class="row">
  <div class="col-lg">
    <div class="card">
      <div class="card-header">
        <h4><i class="fas fa-list fa-fw"></i> ตั้งค่าลิ้งค์หน้าเมนู</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg">
            <button type="button" class="btn btn-success btn-sm mt-4" onClick=AddNew()><i class="fa fa-plus fa-fw"></i>เพิ่มลิงค์</button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover mt-4">
            <thead class="text-center">
              <tr>
                <th width="7.5%">No</th>
                <th width="15.5%">MenuCase</th>
                <th width="15.5%">MenuGroup</th>
                <th width="41.5%">Pages</th>
                <th width="20%">ตัวจัดการ</th>
              </tr>
            </thead>
            <tbody id='FileList'></tbody>
          </table> 
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
    $(document).ready(function(){
        CallData();
    });
</script> 
<script type="text/javascript">
    function CallData(){
      $(".overlay").show();
        $.ajax({
            url: "setting/ajax/ajaxMenuList.php?a=read",
            type: "POST",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#FileList").html(inval["output"]);
                });
                $(".btn-create").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    FileCreate(MenuKey);
                });
                $(".btn-edit").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    EditData(MenuKey);
                });

                $(".btn-delete").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    chkDelete(MenuKey);
                });

                $(".btn-view").on("click",function(e){
                    e.preventDefault();
                    var MenuKey = $(this).attr("data-MenuKey");
                    var st = $(this).attr("data-status");
                    ViewData(st,MenuKey);
                });
                $(".overlay").hide();
                
            }
        });
      
    }
    
    function AddNew(){
        $("#CaseID").val(0);
        $("#MenuCase").val("");
        $("#MenuGroup").val("");
        $("#urlPages").val("");
        $("#CaseStatus option[value='A']").prop('selected', true);
        $('#TypeCommand').val(0);
        $("#myModal").modal("show");
    }
    
    function FileCreate(x){
      $(".overlay").show();
      $.ajax({
            url: "setting/ajax/ajaxMenuList.php?a=createfile",
            type: "POST",
            data: {IDList : x},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    alert("File Create Success !!!");
                    CallData();
                });
                $(".overlay").hide();  
            }
        });
    }
    
    function SaveCase(){
      var MenuCase = $('#MenuCase').val();
      var MenuGroup = $('#MenuGroup').val();
      var urlPages = $('#urlPages').val();
      var xRun = 0;
      $(".overlay").show();
      if (MenuCase == '' || MenuGroup == '' || urlPages == ''){
        alert("กรอกข้อมูลให้ครบด้วยสิวะ");
      }else{
        xRun = 1;
      }
      if (xRun ==1){
        $.ajax({
            url: "setting/ajax/ajaxMenuList.php?a=newlink",
            type: "POST",
            data: {MenuCase : MenuCase,
                   MenuGroup : MenuGroup,
                   urlPages : urlPages,
                   CaseStatus :  $('#CaseStatus').val(),
                   TypeCom : $('#TypeCommand').val(),
                   CaseID : $('#CaseID').val(),},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                  alert(inval['output']);
                  $("#myModal").modal("hide");
                  CallData();
                });
            $(".overlay").hide();   
            }
        });
      }
    }
    function EditData(x){
        $(".overlay").show();
        $.ajax({
                url: "setting/ajax/ajaxMenuList.php?a=edit",
                type: "POST",
                data: { ID : x,
                      },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                      $('#MenuCase').val(inval['MenuCase']);
                      $('#MenuGroup').val(inval['MenuGroup']);
                      $('#urlPages').val(inval['Pages']);
                      $("#CaseStatus option[value='"+inval['CaseStatus']+"']").prop('selected', true);
                      $('#TypeCommand').val(inval['TypeCom']);
                      $("#CaseID").val(inval['CaseID']);
                      $("#myModal").modal("show");
                    });
                $(".overlay").hide();
                }
            });
    }
    function DelData(){
        $(".overlay").show();
        $.ajax({
                url: "setting/ajax/ajaxMenuList.php?a=del",
                type: "POST",
                data: { ID : $('#KeyDelete').val(),
                      },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                      CallData();
                      $('#myModalCon').modal("hide");
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
    function ViewData(x,y){
      $(".overlay").show();
      $.ajax({
            url: "setting/ajax/ajaxMenuList.php?a=view",
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


</script> 

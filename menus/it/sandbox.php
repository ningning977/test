<div class="page-heading">
    <h3><span id='header1'></span></h3>
</div>
<hr class='mt-1'>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br/><br/>
        กำลังโหลด...
    </div>
</div>
<div class="container-fluid">
  <div class="row">
    <div class="table-responsive" id="ShowData"></div>
  </div>
</div>


<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <button class="btn btn-primary" onclick="Updatestatus()">อัปเดตสถานะ</button>
                <table class="table table-hover" id = 'TableUpdatestatus'>
                    <thead>
                        <tr class= "text-center">
                            <th scope="col">No.</th>
                            <th scope="col">เลขที่เอกสาร</th>
                            <th scope="col">ชื่อลูกค้า</th>
                            <th scope="col">วันที่สร้าง</th>
                            <th scope="col">วันที่ทำเสร็จ</th>
                            <th scope="col">วันที่กำหนดเอกสาร</th>
                            <th scope="col">สถานะ<br>ก่อนอัปเดต</th>
                            <th scope="col">สถานะ<br>หลังอัปเดต</th>
                        </tr>
                    </thead>
                        <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script type='text/javascript'>
function Updatestatus(){
    $(".overlay").show();
    $.ajax({
            url: "menus/it/ajax/ajaxsandbox.php?a=Updatestatus",
            type: "GET",
            success:function(result){
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TableUpdatestatus tbody").html(inval['$data']);
                    $("#alert_header").html("<i class=\"far fa-check-circle fa-fw fa-lg test-success\"></i> สำเร็จ!");
                    $("alert_body").html(inval['status']);
                    $("#alert_modal").modal("show");
                    $(".overlay").hide();
                });
            }
    });
}

$("#btn-update").click(function(e){
    e.preventDefault();
    Updatestatus();
});
</script>


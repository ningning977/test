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
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="filt_user">เลือกพนักงานขายจาก</label>
                            <select class="form-select form-select-sm" name="filt_user1" id="filt_user1">
                                <option value="NULL">กรุณาเลือก</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="filt_user">ถึง</label>
                            <select class="form-select form-select-sm" name="filt_user2" id="filt_user2">
                                <option value="NULL">กรุณาเลือก</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="filt_date1">ตั้งแต่วันที่</label>
                            <input class="form-control form-control-sm" type="date" name="filt_date1" id="filt_date1" min="2023-01-01" />
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="filt_date2">ถึงวันที่</label>
                            <input class="form-control form-control-sm" type="date" name="filt_date2" id="filt_date2" min="2023-01-01" />
                        </div>
                    </div>
                    <div class="col-lg-1 col-3">
                        <div class="form-group mb-3">
                            <label for="btn-search">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block btn-sm" id="btn-search" name="btn-search" onclick="SearchDoc()";><i class="fas fa-search fa-fw fa-1x"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-1 col-3">
                        <div class="form-group mb-3">
                            <label for="btn-print">&nbsp;</label>
                            <button type="button" class="btn btn-secondary btn-block btn-sm" id="btn-print" name="btn-print" onclick="PrintDoc()";><i class="fas fa-print fa-fw fa-1x"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-hover" id="SaleData" style="font-size: 12px;">
                                <thead class="text-center">
                                    <tr>
                                        <th>พนักงานขาย</th>
                                        <th width="25%">จำนวนเงิน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="2">กรุณาเลือกข้อมูลด้านบนก่อนค้นหา :)</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-active">
                                    <tr>
                                        <th>รวมทั้งหมด</th>
                                        <th id="AllTotal" class="text-right"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
	
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

function GetEmpName() {
    $.ajax({
        url: "menus/account/ajax/ajaxSaleByEmp.php?p=GetSlp",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                var Rows = parseFloat(inval['Rows']);
                var Opt = "";
                for(i = 0; i < Rows; i++) {
                    Opt += "<option value='"+inval[i]['SlpName']+"'>"+inval[i]['SlpName']+"</option>";
                }
                $("#filt_user1, #filt_user2").append(Opt);
            });
        }
    })
}

function PrintDoc()  {
    var user_1 = $("#filt_user1").val();
    var user_2 = $("#filt_user2").val();
    var date_1 = $("#filt_date1").val();
    var date_2 = $("#filt_date2").val();

    window.open ('menus/account/print/printSaleEmp.php?u1='+user_1+'&u2='+user_2+'&d1='+date_1+'&d2='+date_2+'','_blank');
}

function SearchDoc() {
    var user_1 = $("#filt_user1").val();
    var user_2 = $("#filt_user2").val();
    var date_1 = $("#filt_date1").val();
    var date_2 = $("#filt_date2").val();

    if(user_1 == "" || user_2 == "" || date_1 == "" || date_2 == "") {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/account/ajax/ajaxSaleByEmp.php?p=GetData",
            type: "POST",
            data: {
                u1: user_1,
                u2: user_2,
                d1: date_1,
                d2: date_2
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    var Rows = parseFloat(inval['Rows']);
                    var tBody = "";
                    if(Rows == 0) {
                        tBody += "<tr><td class='text-center' colspan='2'>ไม่มีข้อมูลที่คุณค้นหา :(</td></tr>";
                        $("#AllTotal").html(0);
                    } else {
                        for(i = 0; i < Rows; i++) {
                            tBody +=
                                "<tr>"+
                                    "<td>"+inval[i]['SlpName']+"</td>"+
                                    "<td class='text-right' style='font-weight: bold;'>"+inval[i]['DocTotal']+"</td>"+
                                "</tr>";
                        }
                        $("#AllTotal").html(inval['SumTotal']);
                    }
                    $("#SaleData tbody").html(tBody);
                })
            }
        })
    }
}

$(document).ready(function(){
        CallHead();
        GetEmpName();
});
</script> 
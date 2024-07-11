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
                <!---------- เนื้อหา Pages ------------>
                <div class="row">
                    <div class="col-xl-1 col-lg-2 col-sm-2">
                        <div class="form-group">
                            <label for="filt_year">เลือกปี</label>
                            <select class="form-select form-select-sm" name="filt_year" id="filt_year" onchange="CallData()">
                                <?php
                                echo "<option value='".date("Y")."' selected>".date("Y")."</option>";
                                echo "<option value='".(date("Y")-1)."'>".(date("Y")-1)."</option>";
                                echo "<option value='".(date("Y")-2)."'>".(date("Y")-2)."</option>";
                                echo "<option value='".(date("Y")-3)."'>".(date("Y")-3)."</option>";
                                echo "<option value='".(date("Y")-4)."'>".(date("Y")-4)."</option>";
                                echo "<option value='".(date("Y")-5)."'>".(date("Y")-5)."</option>";
                                ?>                                 
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-sm-7">
                        <div class="form-group">
                            <label for="groupCode">เลือกห้าง</label>
                            <select class="form-select form-select-sm" name="groupCode" id="groupCode" onchange="CallData()">
                                <option value="" selected disabled>เลือกห้าง</option>
                                <?php
                                    switch ($_SESSION['DeptCode']){
                                        /*
                                        case 'DP005' :
                                            //$slpTeam = " WHERE W1.teamGroup LIKE 'TT' ";
                                            $slpTeam = " WHERE (W1.teamGroup LIKE 'MT1' OR  W1.teamGroup LIKE 'EXP' OR W1.teamGroup LIKE 'TT') ";
                                            break;
                                        case 'DP006' :
                                            $slpTeam = " WHERE (W1.teamGroup LIKE 'MT1' OR  W1.teamGroup LIKE 'EXP' OR W1.teamGroup LIKE 'TT') ";
                                            break;
                                        case 'DP007' :
                                            $slpTeam = " WHERE W1.teamGroup LIKE 'MT2' ";
                                            break;
                                            */
                                        case 'DP008' :
                                            $slpTeam = " WHERE W1.teamGroup LIKE 'OUT' ";
                                            break;
                                        case 'DP001' :
                                        case 'DP002' :
                                        case 'DP003' :
                                        case 'DP005' :
                                        case 'DP006' :
                                        case 'DP007' :
                                            $sqlselect = "";
                                            break;
                                    }

                                    
                                    
                                    $sql = "SELECT W1.* 
                                            FROM 
                                                (SELECT T0.GroupCode,T0.GroupName,
                                                    CASE WHEN T0.GroupCode IN (20,22) THEN 'EXP'
                                                            WHEN T0.GroupCode IN (3,4,5,6,8,9,10,11,13,18,19,21,25,27,28,30,31,34,35) THEN 'MT1'
                                                            WHEN T0.GroupCode IN (1,2,7,12,14,17,26,29) THEN 'MT2'
                                                            WHEN T0.GroupCode IN (15,16) THEN 'OUT'
                                                            WHEN T0.GroupCode IN (23,24) THEN 'TT2'
                                                    END AS teamGroup    
                                                FROM OCQG T0
                                                WHERE T0.GroupName Not LIKE 'Business%') W1 ".$slpTeam." ORDER BY W1.GroupCode,W1.GroupName";
                                    $sqlQRY = SAPSelect($sql);
                                    while($result = odbc_fetch_array($sqlQRY)) {
                                        echo "<option value='".$result['GroupCode']."'>".$result['GroupCode'].". ".conutf8($result['GroupName'])."</option>";    
                                    }
                                ?>               
                            </select>
                        </div>
                    </div>
                    <div class="col-xl col-lg col-sm align-self-center text-right">
                        <button class='btn btn-sm btn-success' style='margin-top: 10px;' onclick="Export()"><i class="fas fa-file-excel"></i> Excel</button>
                        <input type="hidden" name='SQL1' id='SQL1'>
                        <input type="hidden" name='SQL2' id='SQL2'>
                        <input type="hidden" name='cYear' id='cYear'>
                        <input type="hidden" name='pYear' id='pYear'>
                        <input type="hidden" name='ExNameCus' id='ExNameCus'>
                    </div>
                </div>

                <div class="row pt-2">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-bordered table-hover rounded rounded-3 overflow-hidden'>
                                <thead class='bg-light' style='font-size: 13px;'>
                                    <tr>
                                        <th colspan='15' class='text-center text-primary'>รายงานยอดขายห้างร้าน : <span id='NameCus'></span></th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th>ชื่อลูกค้า</th>
                                        <?php
                                            for($m = 1; $m <= 12; $m++) {
                                                echo "<th width='5%'>".FullMonth($m)."</th>";
                                            }
                                        ?>
                                        <th width="5.70%">รวมทั้งหมด</th>
                                        <th width="5%">% GP</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='Tbody'>
                                    <tr>
                                        <td colspan='15' class='text-center pt-3 pb-3'>ไม่มีข้อมูล</td>
                                    </tr>
                                </tbody>
                                <tfoot style='font-size: 12px; background-color: rgba(0, 0, 0, 0.04);' id='Tfoot'></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-------- สินสุดเนื้อหา Pages --------->
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
        CallData();
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
    function CallData() {
        if($("#groupCode").val() != null) {
            $(".overlay").show();
            $.ajax({
                url: "menus/sale/ajax/ajaxsaleby_store.php?a=CallData",
                type: "POST",
                data: { Year : $("#filt_year").val(), GroupCode : $("#groupCode").val(), },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#NameCus").html(inval['NameCus']);
                        $("#Tbody").html(inval['Tbody']);
                        $("#Tfoot").html(inval['Tfoot']);

                        $("#SQL1").val(inval['SAPsql']);
                        $("#SQL2").val(inval['SAPsql2']);
                        $("#ExNameCus").val(inval['NameCus']);
                        $("#cYear").val(inval['cYear']);
                        $("#pYear").val(inval['pYear']);
                    })
                    $(".overlay").hide();
                }
            })
        }
    }

    function Export() {
        if($("#SQL1").val() != "" && $("#SQL2").val() != "" && $("#ExNameCus").val() != "") {
            $.ajax({
                url: "menus/sale/ajax/ajaxsaleby_store.php?a=Export",
                type: "POST",
                data: { SQL1 : $("#SQL1").val(), 
                        SQL2 : $("#SQL2").val(),
                        NameCus : $("#ExNameCus").val(),
                        cYear : $("#cYear").val(),
                        pYear : $("#pYear").val(), },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        if(inval['ExportStatus'] == 'SUCCESS') {
                            window.open("../../FileExport/SaleByStore/"+inval['FileName'],'_blank');
                        }else{
                            alert("เกิดข้อผิดพลาดกรุณาแจ้งแผนก IT");
                        }
                    })
                }
            })
        }
    }
</script> 
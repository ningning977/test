<style type="text/css">
    @media only screen and (max-width:820px) {
        .tableFix {
            overflow-y: auto;
            height: 800px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:821px) and (max-width:1180px) {
        .tableFix {
            overflow-y: auto;
            height: 450px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:1181px) {
        .tableFix {
            overflow-y: auto;
            height: 630px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    span.v-detail:hover{
        color: #151515;
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
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="">เลือกปี</label>
                            <select class='form-select form-select-sm' name="Year" id="Year" onchange='CallData();'>
                                <?php 
                                for($y = date("Y"); $y >= 2023; $y--) {
                                    if($y == date("Y")) {
                                        echo "<option value='".$y."' seleted>".$y."</option>";
                                    }else{
                                        echo "<option value='".$y."'>".$y."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row pt-2">
                    <div class="col">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered' style='font-size: 13px;' id='Table1'>
                                <thead class='bg-primary text-light'>
                                    <tr class='text-center'>
                                        <th rowspan='2'>ชื่อคลัง</th>
                                        <th rowspan='2' width='10%'>รายละเอียด</th>
                                        <th colspan='12'>ต้นทุน (บาท)</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            echo "<th width='6.66%'>".FullMonth($m)."</th>";
                                        } 
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan='14' class='text-center'>ไม่มีข้อมูล :)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalDetail" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fab fa-readme' style='font-size: 20px;'></i>&nbsp;&nbsp;&nbsp;รายละเอียดความเคลื่อนไหวคลังเคลมซัพฯ (<span id='H'></span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='TableDetail'>
                                <thead class='text-white' style="background-color: #9A1118;">
                                    <tr class='text-center'>
                                        <th width='3%' rowspan='2'>No.</th>
                                        <th width='9%' rowspan='2'>วันที่เข้าระบบ</th>
                                        <th width='10%' rowspan='2'>เลขที่เอกสาร</th>
                                        <th rowspan='2'>รับจาก/จ่ายให้</th>
                                        <th width='8%' rowspan='2'>รหัสสินค้า</th>
                                        <th rowspan='2'>ชื่อสินค้า</th>
                                        <th width='7%' rowspan='2'>คลังสินค้า</th>
                                        <th colspan='4'>จำนวน</th>
                                        <th width='10%' rowspan='2'>มูลค่าคงเหลือ (บาท)</th>
                                    </tr>
                                    <tr class='text-center'>
                                        <th width='5%'>ยกมา</th>
                                        <th width='5%'>เข้า</th>
                                        <th width='5%'>ออก</th>
                                        <th width='5%'>คงเหลือ</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ออก</button>
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
    CallData();
});

function CallData() {
    let Year  = $("#Year").val();
    $.ajax({
        url: "menus/purchase/ajax/ajaxwhseclaim.php?a=CallData",
        type: "POST",
        data: { Year : Year, },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#Table1 tbody").html(inval['Tbody']);
            });
        }
    })
}

function Detail(Year, Month, WareH) {
    $(".overlay").show();
    $.ajax({
        url: "menus/purchase/ajax/ajaxwhseclaim.php?a=Detail",
        type: "POST",
        data: { Year : Year, Month : Month, WareH : WareH,},
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#H").html(inval['H']);
                $("#TableDetail tbody").html(inval['Data']);
                $("#ModalDetail").modal("show");
            });
            $(".overlay").hide();
        }
    })
}
</script> 
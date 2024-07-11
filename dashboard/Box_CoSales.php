<style>
   
</style>

<div class='card'>
    <div class='card-header'>
        <h4><i class="fas fa-universal-access fa-fw fa-1x"></i> รายการสรุป Co Sales</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-auto">
                <div class="form-gruop">
                    <label for="CoYear">เลือกปี</label>
                    <select class='form-select form-select-sm' name="CoYear" id="CoYear" onchange='CoSales();'>
                        <?php 
                        for($y = Date("Y"); $y >= 2023; $y--) {
                            if($y == Date("Y")) {
                                echo "<option value='$y' selected>$y</option>";
                            }else{
                                echo "<option value='$y'>$y</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-auto">
                <div class="form-gruop">
                    <label for="CoMonth">เลือกเดือน</label>
                    <select class='form-select form-select-sm' name="CoMonth" id="CoMonth" onchange='CoSales();'>
                        <?php 
                        for($m = 1; $m <= 12; $m++) {
                            if($m == Date("m")) {
                                echo "<option value='$m' selected>".FullMonth($m)."</option>";
                            }else{
                                echo "<option value='$m'>".FullMonth($m)."</option>";
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
                    <table class='table table-sm table-hover table-bordered ' style='font-size: 11.5px;' id='TableCoSales'>
                        <thead>
                            <tr class='text-center'>
                                <th width='35%'>ชื่อ</th>
                                <th width='20%'>จำนวน<br>รายการ</th>
                                <th width='20%'>จำนวน<br>บิล</th>
                                <th width='25%'>ยอดรวม (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        CoSales();
	});

    function CoSales() {
        const Year = $("#CoYear").val();
        const Month = $("#CoMonth").val();
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=GetCoSales",
            type: "POST",
            data: { Year : Year, Month : Month, },
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TableCoSales tbody").html(inval['Data']);
                });
            }
        })
    }
</script> 
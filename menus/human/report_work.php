<style type="text/css">
    .view {
        margin: auto;
        width: 100%;
    }

    .wrapper {
        position: relative;
        overflow: auto;
        white-space: nowrap;
    }

    .sticky-col {
        position: -webkit-sticky;
        position: sticky;
    }

    @media only screen and (max-width:820px) {
        
    }

    @media (min-width:821px) and (max-width: 1180px) {
        
    }

    @media (min-width:1181px) {
        .col-1 {
            width: 100px;
            min-width: 100px;
            max-width: 100px;
            left: 0px;
        }

        .col-2 {
            width: 150px;
            min-width: 150px;
            max-width: 150px;
            left: 100px;
        }

        .col-3 {
            width: 150px;
            min-width: 150px;
            max-width: 150px;
            left: 250px;
        }
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
                        <div class="form-group">
                            <label for="txtYear">เลือกปี</label>
                            <select class='form-select form-select-sm' name="txtYear" id="txtYear" onchange='CallData();'>
                                <?php 
                                for($y = date("Y"); $y >= 2023; $y--) {
                                    $opYaer = ($y == date("Y")) ? "<option value='$y' selected>$y</option>" : "<option value='$y'>$y</option>";
                                    echo $opYaer;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="txtMonth">เลือกเดือน</label>
                            <select class='form-select form-select-sm' name="txtMonth" id="txtMonth" onchange='CallData();'>
                                <?php 
                                for($m = 1; $m <= 12; $m++) {
                                    $opMonth = ($m == date("m")) ? "<option value='$m' selected>".FullMonth($m)."</option>" : "<option value='$m'>".FullMonth($m)."</option>";
                                    echo $opMonth;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="txtTeam"><i class="fas fa-users fa-fw fa-1x"></i> เลือกฝ่าย</label>
                            <select class="form-select form-select-sm" id="txtTeam" name="txtTeam" onchange='CallData();'>
                                <option selected disabled>เลือกฝ่าย</option>
                                <option value='ALL'>เลือกทั้งหมด</option>
                                <option value="DP002">ฝ่ายเทคโนโลยีสารสนเทศ</option>
                                <option value="DP003">ฝ่ายการตลาด</option>
                                <option value="DP004">ฝ่ายจัดซื้อ</option>
                                <option value="DP005">ฝ่ายขายตจว.</option>
                                <option value="DP006">ฝ่ายขาย MT1</option>
                                <option value="DP007">ฝ่ายขาย MT2</option>
                                <option value="DP008">ฝ่ายขายหน้าร้าน</option>
                                <option value="DP009">ฝ่ายบัญชี</option>
                                <option value="DP010">ฝ่ายพัฒนาผลิตภัณฑ์</option>
                                <option value="DP011">ฝ่ายคลังสินค้าและขนส่ง</option>
                                <option value="DP012">ฝ่ายทรัพยากรมนุษย์</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Export();'><i class="far fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                </div>

                <div class="view pt-2">
                    <div class="wrapper">
                        <table class='table table-sm table-hover table-bordered' style='font-size: 12px;' id='TableWork'>
                            <thead>
                                <tr>
                                    <th class='text-center p-5' style='font-size: 13px;'>กรุณาเลือกฝ่าย <i class="fas fa-users fa-fw fa-1x"></i></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="row pt-4">
                    <div class="col-auto" style='font-size: 12px;'>
                        <p class='text-center m-1 fw-bolder'>ความหมายของสถานะ</p>
                        <p class='m-0 border ps-2 pe-2 pt-1 pb-1 mb-1'>S = ลาป่าย <small class='text-muted'>ตามด้วยตัวเลข (หน่วยเป็นชั่วโมง)</small></p>
                        <p class='m-0 border ps-2 pe-2 pt-1 pb-1 mb-1'>A = ลากิจ <small class='text-muted'>ตามด้วยตัวเลข (หน่วยเป็นชั่วโมง)</small></p>
                        <p class='m-0 border ps-2 pe-2 pt-1 pb-1 mb-1'>V = ลาพักร้อน <small class='text-muted'>ตามด้วยตัวเลข (หน่วยเป็นชั่วโมง)</small></p>
                        <p class='m-0 border ps-2 pe-2 pt-1 pb-1 mb-1'>O = ขาดงาน <small class='text-muted'>ตามด้วยตัวเลข (หน่วยเป็นชั่วโมง)</small></p>
                        <p class='m-0 border ps-2 pe-2 pt-1 pb-1 mb-1'>L = สาย <small class='text-muted'>ตามด้วยตัวเลข (หน่วยเป็นนาที)</small></p>
                    </div>
                    <div class="col-auto" style='font-size: 12px;'>
                        <p class='text-center m-1 fw-bolder'>ความหมายของสี</p>
                        <p class='m-0 border ps-2 pe-2 pt-1 pb-1 mb-1 text-center' style='background-color: #fff;'>ทำงานปกติ</p>
                        <p class='m-0 border ps-2 pe-2 pt-1 pb-1 mb-1 text-center' style='background-color: #fff3cd;'>ลา</p>
                        <p class='m-0 border ps-2 pe-2 pt-1 pb-1 mb-1 text-center' style='background-color: #f8d7da;'>วันหยุด</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

function CallData() {
    let Year = $("#txtYear").val();
    let Month = $("#txtMonth").val();
    let Team = $("#txtTeam").val();

    if(Team != null) {
        let DayInMonth = new Date(Year, Month, 0).getDate();
    
        let NameDay = "";
        let NumDay = "";
        for(let day = 1; day <= DayInMonth; day++) {
            let ArrNameDay = new Date(Year+'-'+Month+'-'+day).getDay();
            let ColorSun = "";
            switch(ArrNameDay) {
                case 0: NameDay += "<th class='text-center table-danger'>Sun</th>"; ColorSun = "table-danger"; break;
                case 1: NameDay += "<th class='text-center'>Mon</th>"; break;
                case 2: NameDay += "<th class='text-center'>Tue</th>"; break;
                case 3: NameDay += "<th class='text-center'>Wed</th>"; break;
                case 4: NameDay += "<th class='text-center'>Thu</th>"; break;
                case 5: NameDay += "<th class='text-center'>Fri</th>"; break;
                case 6: NameDay += "<th class='text-center'>Sat</th>"; break;
            }
            NumDay += (day < 10) ? "<th class='text-center long "+ColorSun+"'>0"+day+"</th>" : "<th class='text-center long "+ColorSun+"'>"+day+"</th>";
        }
    
        let DataHead = `
            <tr>
                <th rowspan='2' class='text-center bg-white sticky-col col-1'>รหัสพนักงาน</th>
                <th rowspan='2' class='text-center bg-white sticky-col col-2'>ชื่อ - นามสกุล (ชื่อเล่น)</th>
                <th rowspan='2' class='text-center bg-white sticky-col col-3'>แผนก</th>
                `+NameDay+`
                <th colspan='6' class='text-center '>Summary</th>
            </tr>
            <tr>
                `+NumDay+`
                <th class='text-center'>Work</th>
                <th class='text-center'>Sick</th>
                <th class='text-center'>Annual</th>
                <th class='text-center'>Vacation</th>
                <th class='text-center'>Other</th>
                <th class='text-center'>Late</th>
            </tr>
        `;
        $("#TableWork thead").html(DataHead);
    
        $.ajax({
            url: "menus/human/ajax/ajaxreport_work.php?a=CallData",
            type: "POST",
            data: { Year: Year, Month: Month, Team: Team },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    let DataBody = ``;
                    $.each(inval['DataBody'],function(k,data) {
                        DataBody += `
                            <tr>
                                <td class='text-center bg-white sticky-col col-1'>`+data['SlpCode']+`</td>
                                <td class='bg-white sticky-col col-2'>`+data['SlpName']+`</td>
                                <td class='bg-white sticky-col col-3'>`+data['DetpCode']+`</td>`;
                                for(let day = 1; day <= DayInMonth; day++) {
                                    let ColorSunBody = "";
                                    if(typeof(data['DaySun_'+day]) != "undefined") {
                                        ColorSunBody = "table-danger";
                                    }
                                    DataBody += `
                                        <td class='text-center long `+ColorSunBody+`'>`+data['Day_'+day]+`</td>
                                    `;
                                }
                                DataBody += `
                                <td class='text-right long'>`+data['Work']+`</td>
                                <td class='text-right long'>`+data['Sick']+`</td>
                                <td class='text-right long'>`+data['Annual']+`</td>
                                <td class='text-right long'>`+data['Vacation']+`</td>
                                <td class='text-right long'>`+data['Other']+`</td>
                                <td class='text-right long'>`+data['Late']+`</td>
                            </tr>
                        `;
                    });
                    $("#TableWork tbody").html(DataBody);
                });
            }
        })
    }
}

function Export() {
    let Year = $("#txtYear").val();
    let Month = $("#txtMonth").val();
    let Team = $("#txtTeam").val();
    // $(".overlay").show();
    $.ajax({
        url: "menus/human/ajax/ajaxreport_work.php?a=Export",
        type: "POST",
        data: { Year: Year, Month: Month, Team: Team },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                // $(".overlay").hide();
                window.open("../../FileExport/ReportWork/"+inval['FileName'],'_blank');
            });
        }
    })
}

$(document).ready(function(){
    
});
</script> 
<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<style type="text/css">
    @media only screen and (max-width:820px) {
        .tableFix {
            overflow-y: auto;
            height: 800px;
        }
    }

    @media (min-width:821px) and (max-width: 1180px) {
        .tableFix {
            overflow-y: auto;
            height: 500px;
        }
    }

    @media (min-width:1181px) {
        .tableFix {
            overflow-y: auto;
            height: 620px;
        }
    }

    .tableFix table.table {
        border-collapse: collapse;
    }

    .tableFix thead tr th {
        box-shadow: inset 0.5px 0.5px #eee, 0 0.5px #eee;
        position: sticky;
        top: 0;
        background-color: #9A1118;
    }

/* ================
    Calendar Styling */
    .calendar {
    border-radius: 10px;
    }

    .month {
    font-size: 2rem;
    }

    @media (min-width: 992px) {
    .month {
        font-size: 3.5rem;
    }
    }

    .calendar ol li {
        float: left;
        width: 14.28571%;
    }

    .calendar .day-names {
        border-bottom: 1px solid #eee;
    }

    .calendar .day-names li {
        font-weight: 600;
    }

    .calendar .days li {
        border-bottom: 1px solid #eee;
        min-height: 8rem;
    }

    .calendar .days li .date {
        margin: 0.5rem 0.25rem;
    }

    .calendar .days li .event {
        font-size: 0.75rem;
        margin: 0rem 0.25rem;
        padding: 0.25rem 0.4rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-radius: 1rem;
        margin-bottom: 1px;
    }
    .calendar .days li.outside {
        background-color: #FAFAFA;
    }
    .calendar .days li .date:hover, td.lead:hover {
        cursor: pointer;
    }
    td.lead:hover {
        background-color: rgba(0,0,0,.05);
    }

    .calendar .days li:not(.outside):hover {
        background-color: rgba(0,0,0,.05);
    }

    .calendar .days li .event:hover {
        cursor: pointer;
    }

    .calendar .days li .event.event-more {
        border: 1px solid #9A1118;
        background-color: rgba(255,255,255,1);
        color: #9A1118;
    }

    .calendar .days li .event.span-2 {
        width: 200%;
    }

    .calendar .days li .event.begin {
        border-radius: 1rem 0 0 1rem;
    }

    .calendar .days li .event.end {
        border-radius: 0 1rem 1rem 0;
    }

    .calendar .days li .event.clear {
        background: none;
    }

    .calendar .days li:nth-child(n+36) {
        border-bottom: none;
    }

    .calendar .days li.outside .date {
        color: #ddd;
    }

    .workdate, .noscan, .workoutside, .workformhome { text-align: center; background-color: #DAF7F7; border: 1px solid #00A39B; }
    .workalert { text-align: center; background-color: #FFFFFF; border: 1px solid #FF0000; color: #FF0000; }
    .holiday   { text-align: center; background-color: #FF0000; border: 1px solid #990000; color: #FFFFFF; }
    .leave     { text-align: center; background-color: #FAE3F6; border: 1px solid #E000B7; color: #E000B7; }
    .offdate   { text-align: center; background-color: #FFEDDB; border: 1px solid #E36E00; color: #E36E00; }
    .workoutside, .workformhome { color: #00661B; font-weight: bold; }
    .workalert { color: #FF0000; font-weight: bold; border: 1px solid #FF0000; }
    .noscan    { color: #006B22; font-weight: bold; }
    .today { background-color: rgba(154, 17, 24, .10); }
    .today .date { font-weight: bold; color: #9A1118; }

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
                    <div class="col-lg-2 col-5">
                        <div class="form-group">
                            <label for="filt_date"><i class="far fa-calendar-alt fa-fw fa-1x"></i> เลือกวันที่</label>
                            <input type="date" id="filt_date" name="filt_date" class="form-control form-control-sm" min="<?php echo $this_year; ?>-01-01" value="<?php echo date("Y-m-d"); ?>"/>
                        </div>
                    </div>
                    <?php
                        $DeptCode   = $_SESSION['DeptCode'];
                        $Opt_ALL    = " disabled";
                        $Opt_DP001  = " disabled";
                        $Opt_DP002  = " disabled";
                        $Opt_DP003  = " disabled";
                        $Opt_DP004  = " disabled";
                        $Opt_DP005  = " disabled";
                        $Opt_DP006  = " disabled";
                        $Opt_DP007  = " disabled";
                        $Opt_DP008  = " disabled";
                        $Opt_DP009  = " disabled";
                        $Opt_DP010  = " disabled";
                        $Opt_DP011  = " disabled";
                        $Opt_DP012  = " disabled";

                        if($_SESSION['uClass'] == 29) {
                            $Opt_DP002 = "";
                            $Opt_DP009 = "";
                        } else {
                            if($DeptCode == "DP001" || $DeptCode == "DP002" || $DeptCode == "DP012") {
                                $Opt_DP001  = "";
                                $Opt_DP002  = "";
                                $Opt_DP003  = "";
                                $Opt_DP004  = "";
                                $Opt_DP005  = "";
                                $Opt_DP006  = "";
                                $Opt_DP007  = "";
                                $Opt_DP008  = "";
                                $Opt_DP009  = "";
                                $Opt_DP010  = "";
                                $Opt_DP011  = "";
                                $Opt_DP012  = "";
                            } else {
                                ${"Opt_".$DeptCode} = "";
                            }
                            
                        }
                        
                    ?>
                    <div class="col-lg-2 col-7">
                        <div class="form-group">
                            <label for="filt_team"><i class="fas fa-users fa-fw fa-1x"></i> เลือกทีม</label>
                            <select class="form-select form-select-sm" id="filt_team" name="filt_team">
                                <option selected disabled>เลือกทีม</option>
                                <option value="DP000"<?php echo $Opt_DP001;?>>ทุกฝ่าย</option>
                                <option value="DP002"<?php echo $Opt_DP002; ?>>ฝ่ายเทคโนโลยีสารสนเทศ</option>
                                <option value="DP003"<?php echo $Opt_DP003; ?>>ฝ่ายการตลาด</option>
                                <option value="DP004"<?php echo $Opt_DP004; ?>>ฝ่ายจัดซื้อ</option>
                                <option value="DP005"<?php echo $Opt_DP005; ?>>ฝ่ายขายตจว.</option>
                                <option value="DP006"<?php echo $Opt_DP006; ?>>ฝ่ายขาย MT1</option>
                                <option value="DP007"<?php echo $Opt_DP007; ?>>ฝ่ายขาย MT2</option>
                                <option value="DP008"<?php echo $Opt_DP008; ?>>ฝ่ายขายหน้าร้าน</option>
                                <option value="DP009"<?php echo $Opt_DP009; ?>>ฝ่ายบัญชี</option>
                                <option value="DP010"<?php echo $Opt_DP010; ?>>ฝ่ายพัฒนาผลิตภัณฑ์</option>
                                <option value="DP011"<?php echo $Opt_DP011; ?>>ฝ่ายคลังสินค้าและขนส่ง</option>
                                <option value="DP012"<?php echo $Opt_DP012; ?>>ฝ่ายทรัพยากรมนุษย์</option>
                                
                            </select>
                        </div>
                    </div>
                    <?php if($_SESSION['DeptCode'] == 'DP012' || $_SESSION['DeptCode'] == 'DP002') { ?>
                        <div class="col-auto">
                            <div class="form-group">
                                <label for=""></label>
                                <button class='btn btn-sm btn-success w-100 btn-export' onclick='Export()'><i class="fas fa-file-excel"></i> Excel</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-7 d-flex align-items-end">
                        <h5>สถิติการมาทำงาน <span id="TeamName"></span> <small class="text-muted">&mdash; วันที่ <span id="PickDate"></span></small></h5>
                    </div>
                    <div class="col-5 d-flex align-items-center justify-content-end">
                        <i class="fas fa-search" style='font-size: 20px;'></i>
                        <div class="ps-1 pb-1" style='width: 200px;'>
                            <input type="text" class='form-control form-control-sm' name='SearchData' id='SearchData' >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive tableFix">
                            <table class="table table-bordered table-sm table-hover" style="font-size: 12px;" id="StampList">
                                <thead>
                                    <tr class="text-center text-white" style="background-color: #9A1118;">
                                        <th>ชื่อพนักงาน (ชื่อเล่น)</th>
                                        <th width="7.5%">เวลาทำงาน</th>
                                        <th width="7.5%">ลา</th>
                                        <th width="7.5%">ยังไม่ลงเวลา<br/>/ขาดงาน</th>
                                        <th width="7.5%">ทำงาน<br/>นอกสถานที่</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center">กรุณาเลือกทีม :)</td>
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

<div class="modal fade" id="ModalEmpData" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-clock fa-fw fa-1x"></i> ข้อมูลลงเวลาทำงานของ: <span id="EmpName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="calendar mt-2">

                        <ol class="day-names list-unstyled">
                            <li class="font-weight-bold text-center text-danger">วันอาทิตย์</li>
                            <li class="font-weight-bold text-center">วันจันทร์</li>
                            <li class="font-weight-bold text-center">วันอังคาร</li>
                            <li class="font-weight-bold text-center">วันพุธ</li>
                            <li class="font-weight-bold text-center">วันพฤหัสบดี</li>
                            <li class="font-weight-bold text-center">วันศุกร์</li>
                            <li class="font-weight-bold text-center">วันเสาร์</li>
                        </ol>

                        <ol class="days list-unstyled" id="WorkDate"></ol>

                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

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

$("#SearchData").on("keyup", function(){
    var kwd = $(this).val().toLowerCase();
    $("#StampList tbody tr").filter(function(){
        $(this).toggle($(this).text().toLowerCase().indexOf(kwd) > -1)
    });
});

function GetEmpStamp(DocDate, TeamCode) {
    $(".overlay").show();
    $.ajax({
        url: "menus/human/ajax/ajaxEmpTimeStamp.php?p=GetTimeStamp",
        type: "POST",
        data: {
            TeamCode: TeamCode,
            DocDate: DocDate
        },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            var tbody = "";
            $.each(obj, function(key, inval) {
                var ax = parseFloat(inval['ax']);
                var bx = parseFloat(inval['bx']);
                if(ax > 0) {
                    tbody +=
                        "<tr class='table-danger'>"+
                            "<td colspan='5' style='font-weight:bold;'>พนง.สายสำนักงานและคลังสินค้า</td>"+
                        "</tr>";
                    for(ai = 0; ai < ax; ai++) {
                        tbody +=
                            "<tr>"+
                                "<td class='d-flex'>"+
                                    "<div class='w-75'>"+
                                        "<strong>["+inval[0][ai]['EmpCode']+"] "+inval[0][ai]['FullName']+"</strong><br/>"+
                                        "<small class='text-muted'>ตำแหน่ง: "+inval[0][ai]['PositionName']+"</small>>";
                        if (TeamCode=='DP000'){
                            tbody +=  "<br/><small class='text-muted'>ฝ่าย: "+inval[0][ai]['DeptName']+"</small>";
                        }

                        tbody +=   "</div>"+
                                    "<div class='w-25 text-right'>"+
                                        "<a href='javascript:void(0);' onclick=\"GetData("+inval[0][ai]['EmpCode']+",'"+inval[0][ai]['FullName']+"');\"><i class='far fa-calendar-alt fa-fw fa-lg'></i></a>"+
                                    "</div>"+
                                "</td>"+
                                "<td class='text-center'>"+inval[0][ai]['TimeStamp']+"</td>"+
                                "<td class='text-center'>"+inval[0][ai]['Leave']+"</td>"+
                                "<td class='text-center'>"+inval[0][ai]['NoStamp']+"</td>"+
                                "<td class='text-center'>"+inval[0][ai]['Outsite']+"</td>"+
                            "</tr>";
                    }
                }

                if(inval['bx'] > 0) {
                    tbody +=
                        "<tr class='table-danger'>"+
                            "<td colspan='5' style='font-weight:bold;'>พนง.สาย DEMON / PC</td>"+
                        "</tr>";
                    for(bi = 0; bi < bx; bi++) {
                        tbody +=
                            "<tr>"+
                                "<td class='d-flex'>"+
                                    "<div class='w-75'>"+
                                        "<strong>["+inval[1][bi]['EmpCode']+"] "+inval[1][bi]['FullName']+"</strong><br/>"+
                                        "<small class='text-muted'>ตำแหน่ง: "+inval[1][bi]['PositionName']+"</small>";
                        if (TeamCode = 'DP000'){
                            tbody += "<br><small class='text-muted'>ฝ่าย: "+inval[1][bi]['DeptName']+"</small>";
                        }
                        tbody +=               
                                    "</div>"+
                                    "<div class='w-25 text-right'>"+
                                        "<a href='javascript:void(0);' onclick=\"GetData("+inval[1][bi]['EmpCode']+",'"+inval[1][bi]['FullName']+"');\"><i class='far fa-calendar-alt fa-fw fa-lg'></i></a>"+
                                    "</div>"+
                                "</td>"+
                                "<td class='text-center'>"+inval[1][bi]['TimeStamp']+"</td>"+
                                "<td class='text-center'>"+inval[1][bi]['Leave']+"</td>"+
                                "<td class='text-center'>"+inval[1][bi]['NoStamp']+"</td>"+
                                "<td class='text-center'>"+inval[1][bi]['Outsite']+"</td>"+
                            "</tr>";
                    }
                }
            });

            var PickDate = new Date($("#filt_date").val());
            var DateY    = PickDate.getFullYear();
            var DateM    = PickDate.getMonth()+1;
            var DateD    = PickDate.getDate();
            $("#TeamName").html($("#filt_team option:selected").text());
            $("#PickDate").html(DateD+'/'+DateM+'/'+DateY);
            $("#StampList tbody").html(tbody);
        }
    });
}

function GetData(EmpCode, EmpName) {
    var DocDate = $("#filt_date").val();
    $.ajax({
        url: "menus/human/ajax/ajaxEmpTimeStamp.php?p=GetData",
        type: "POST",
        data: {
            DocDate: DocDate,
            EmpCode: EmpCode
        },
        success: function(result) {
            var obj =jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                /* Do something */

                $("#WorkDate").html(inval['output']);
                $("#EmpName").html(EmpName);
                $("#ModalEmpData").modal("show");
            });
        }
    });
}

$(document).ready(function(){
    CallHead();
    var DeptCode = '<?php echo $_SESSION['DeptCode']; ?>';
    if(DeptCode != "DP001") {
        $("#filt_team").val(DeptCode).change();
    }
});

$("#filt_date, #filt_team").on("change", function() {
    var filt_date = $("#filt_date").val();
    var filt_team = $("#filt_team").val();
    if(filt_team == 'DP000') {
        $(".btn-export").show();
    }else{
        $(".btn-export").hide();
    }

    if(filt_team != "") {
        GetEmpStamp(filt_date,filt_team);
    }
});

function Export() {
    var filt_date = $("#filt_date").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/human/ajax/ajaxEmpTimeStamp.php?p=Export",
        type: "POST",
        data: { DocDate: filt_date },
        success: function(result) {
            let obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                $(".overlay").hide();
                window.open("../../FileExport/EmpTimeStamp/"+inval['FileName'],'_blank');
            });
        }
    });
}
</script> 
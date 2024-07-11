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
                <ul class="nav nav-tabs" id="main-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#SaleByMonth" class="btn-tabs nav-link active" id="SaleByMonth-tab" data-bs-toggle="tab" data-bs-target="#SaleByMonth" role="tab" data-tabs="0" aria-controls="SaleByMonth" aria-selected="false">
                            <i class="fas fa-user fa-fw fa-1x"></i> ยอดขายรายบุคคล
                        </a>
                    </li>
                    <!-- <li class="nav-item" role="presentation">
                        <a href="#SaleByItemStatus" class="btn-tabs nav-link" id="SaleByItemStatus-tab" data-bs-toggle="tab" data-bs-target="#SaleByItemStatus" role="tab" data-tabs="0" aria-controls="SaleByItemStatus" aria-selected="false">
                            <i class="fas fa-toolbox fa-fw fa-1x"></i> ยอดขายรายสถานะสินค้า
                        </a>
                    </li> -->
                </ul>

                <div class="tab-content">
                    <div class="tab-pane show active" id="SaleByMonth" role="tabpanel" aria-labelledby="SaleByMonth-tab">
                        <div class="row mt-4">
                            <div class="col-lg-1 col-5">
                                <div class="form-group">
                                    <label for="filt_year">เลือกปี</label>
                                    <select class="form-select form-select-sm" id="filt_year" onchange='GetSalePTA();'>
                                    <?php
                                        for($y = date("Y"); $y >= 2023; $y--) {
                                            if($y == $this_year) { $y_slct = " selected"; } else { $y_slct = ""; }
                                            echo "<option value='$y' $y_slct>$y</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-7">
                                <div class="form-group">
                                    <label for="filt_month">เลือกเดือน</label>
                                    <select class="form-select form-select-sm" id="filt_month" onchange='GetSalePTA();'>
                                    <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            if($m == date('m')) {
                                                $m_slct = " selected";
                                            } else {
                                                $m_slct = "";
                                            }
                                            echo "<option value='$m' $m_slct>".FullMonth($m)."</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg">
                                <div class="table-responsive" id="SAContent"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane show" id="SaleByItemStatus" role="tabpanel" aria-labelledby="SaleByItemStatus-tab">
                        2
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" tabindex="-1" id="SaleByMonth">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-dollar-sign fa-fw fa-1x"></i> ข้อมูลการขายของ<span id="txt_TeamName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">ตกลง</button>
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
        let MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            data : {MenuCase : MenuCase,},
            success: function(result) {
                let obj = jQuery.parseJSON(result);
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
    GetSalePTA();
});

function number_format(number,decimal) {
    var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
    var formatter = new Intl.NumberFormat("en",options);
    return formatter.format(number)
}

function ThaiMonth(m) {
    var MonthName = "";
    switch(m) {
        case 1: MonthName = "มกราคม"; break;
        case 2: MonthName = "กุมภาพันธ์"; break;
        case 3: MonthName = "มีนาคม"; break;
        case 4: MonthName = "เมษายน"; break;
        case 5: MonthName = "พฤษภาคม"; break;
        case 6: MonthName = "มิถุนายน"; break;
        case 7: MonthName = "กรกฎาคม"; break;
        case 8: MonthName = "สิงหาคม"; break;
        case 9: MonthName = "กันยายน"; break;
        case 10: MonthName = "ตุลาคม"; break;
        case 11: MonthName = "พฤศจิกายน"; break;
        case 12: MonthName = "ธันวาคม"; break;
    }
    return MonthName;
}

function GetSalePTA() {
    let filt_year  = $("#filt_year").val();
    let filt_month = $("#filt_month").val();
    let filt_team = t = 'PTA';
    let SAL_JSON = "";
    let txt_year = parseFloat(filt_year);
    let txt_month = $("#filt_month option:selected").text();
    $(".overlay").show();
    $.ajax({
        url: "../ceo/ajax/ajaxCEO.php?p=SaleKPI",
        type: "POST",
        data: { y : filt_year, m : filt_month, t : filt_team, },
        success: function(result) {
            $(".overlay").hide();
            SAL_JSON = result;
            let obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                let SUM_TarAll  = SUM_ActAll  = 0;
                let SaTbody = SaTfoot = "";
                let SaThead = 
                    "<div class='table-responsive'>"+
                        "<table class='table table-bordered'>"+
                            "<thead class='text-center'>"+
                                "<tr class='text-white' style='background-color: #9A1118;'>"+
                                    "<th>พนง.ขาย</th>"+
                                    "<th>เป้าขาย<br/>(บาท)</th>"+
                                    "<th>ยอดขาย<br/>(บาท)</th>"+
                                    "<th>%</th>"+
                                "</tr>"+
                            "</thead>"+
                            "<tbody></tbody>"+
                            "<tfoot></tfoot>"+
                        "</table>"+
                    "</div>";
                $("#SAContent").html(SaThead);

                let TeamCode = [];
                for(const [key,value] of Object.entries(inval)) {
                    if(key != "YEAR") {
                        TeamCode.push(`${key}`);
                    }
                }

                for(ax = 0; ax < TeamCode.length; ax++) {
                    let TEAM = TeamCode[ax];
                    let b = {};
                    switch(TEAM) {
                        case "PTA100": TeamName = "ทีม PTA"; break;
                    }

                    SaTh =
                        "<tr class='table-danger text-center' style='font-weight: bold;'>"+
                            "<td colspan='5'>"+TeamName+"</td>"+
                        "</tr>";
                    $("#SAContent table.table tbody").append(SaTh);

                    let UserCode = [];
                    for(const [key,value] of Object.entries(inval[TEAM])) {
                        UserCode.push(`${key}`);
                    }

                    let SUM_TarTeam = SUM_ActTeam = 0;
                    let cx = 0;

                    for(bx = 0; bx < UserCode.length; bx++) {
                        let SlpCode = UserCode[bx];
                        let b = {};

                        if(filt_month <= 9) {
                            b[TeamCode[t]+'_TarM'] = inval[TEAM][SlpCode]['TAR']['M0'+filt_month];
                            if(typeof inval[TEAM][SlpCode]['ACT'] !== "undefined") {
                                b[TeamCode[t]+'_ActM'] = inval[TEAM][SlpCode]['ACT']['M0'+filt_month];
                            } else {
                                b[TeamCode[t]+'_ActM'] = 0;
                            }
                        } else {
                            b[TeamCode[t]+'_TarM'] = inval[TEAM][SlpCode]['TAR']['M'+filt_month];
                            if(typeof inval[TEAM][SlpCode]['ACT'] !== "undefined") {
                                b[TeamCode[t]+'_ActM'] = inval[TEAM][SlpCode]['ACT']['M'+filt_month];
                            } else {
                                b[TeamCode[t]+'_ActM'] = 0;
                            }
                        }

                        if(b[TeamCode[t]+'_TarM'] > 0) {
                            PctM = (b[TeamCode[t]+'_ActM'] / b[TeamCode[t]+'_TarM']) * 100;
                        } else {
                            PctM = 0;
                        }

                        if(PctM >= 100.01) {
                            PctMClass = "bg-success text-white";
                        } else if(PctM >= 80.01 && PctM < 100) {
                            PctMClass = "text-success font-weight";
                        } else if(PctM >= 60.01 && PctM < 80) {
                            PctMClass = "text-warning";
                        } else if(PctM >= 40.01 && PctM < 60) {
                            PctMClass = "text-danger";
                        } else {
                            PctMClass = null;
                        }

                        SaTbody =
                            "<tr>"+
                                "<td class='d-flex'>"+
                                    "<div class='w-75'>"+inval[TEAM][SlpCode]['SlpName']+"</div>"+
                                    "<div class='w-25 text-right'>"+
                                        "<a href='javascript:void();' class='btn_SAbySlp' data-Name='"+inval[TEAM][SlpCode]['SlpName']+"' data-Ukey='"+SlpCode+"' data-TeamCode='"+TEAM+"'>"+
                                            "<i class='fas fa-clipboard-list fa-fw fa-lg'></i>"+
                                        "</a>"+
                                    "</div>"+
                                "</td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+number_format(b[TeamCode[t]+'_TarM'],0)+"</td>"+
                                "<td class='text-right'>"+number_format(b[TeamCode[t]+'_ActM'],0)+"</td>"+
                                "<td class='text-center "+PctMClass+"'>"+number_format(PctM,2)+"</td>"+
                            "</tr>";
                        $("#SAContent table.table tbody").append(SaTbody);

                        SUM_TarTeam = SUM_TarTeam + parseFloat(b[TeamCode[t]+'_TarM']);
                        SUM_ActTeam = SUM_ActTeam + parseFloat(b[TeamCode[t]+'_ActM']);

                        if(SUM_TarTeam > 0) {
                            SUM_PctTeam = (SUM_ActTeam / SUM_TarTeam) * 100
                        } else {
                            SUM_PctTeam = 0;
                        }

                        if(SUM_PctTeam >= 100.01) {
                            SumPctMClass = "bg-success text-white";
                        } else if(SUM_PctTeam >= 80.01 && SUM_PctTeam < 100) {
                            SumPctMClass = "text-success font-weight";
                        } else if(SUM_PctTeam >= 60.01 && SUM_PctTeam < 80) {
                            SumPctMClass = "text-warning";
                        } else if(SUM_PctTeam >= 40.01 && SUM_PctTeam < 60) {
                            SumPctMClass = "text-danger";
                        } else {
                            SumPctMClass = null;
                        }

                        cx++;
                        if((TeamCode.length > 1 && cx == UserCode.length)) {
                            SaTbody =
                            "<tr class='table-active' style='font-weight: bold;'>"+
                                "<td>รวม"+TeamName+"</td>"+
                                "<td class='text-right'>"+number_format(SUM_TarTeam,0)+"</td>"+
                                "<td class='text-right'>"+number_format(SUM_ActTeam,0)+"</td>"+
                                "<td class='text-center "+SumPctMClass+"'>"+number_format(SUM_PctTeam,2)+"</td>"+
                            "</tr>";
                            $("#SAContent table.table tbody").append(SaTbody);
                        }
                        SUM_TarAll = SUM_TarAll + parseFloat(b[TeamCode[t]+'_TarM']);
                        SUM_ActAll = SUM_ActAll + parseFloat(b[TeamCode[t]+'_ActM']);
                    }
                }
                if(SUM_TarAll > 0) {
                    SUM_PctTeam = (SUM_ActAll / SUM_TarAll) * 100
                } else {
                    SUM_PctTeam = 0;
                }
                SaTfoot =
                    "<tr class='bg-danger text-white' style='font-weight: bold;'>"+
                        "<td>รวมทุกทีม</td>"+
                        "<td class='text-right'>"+number_format(SUM_TarAll,0)+"</td>"+
                        "<td class='text-right'>"+number_format(SUM_ActAll,0)+"</td>"+
                        "<td class='text-center'>"+number_format(SUM_PctTeam,2)+"</td>"+
                    "</tr>";
                $("#SAContent table.table tbody").append(SaTfoot);

                if(filt_month <= 9) {
                    NoActive = inval['YEAR']['NOACTIVE']['M0'+filt_month];
                } else {
                    NoActive = inval['YEAR']['NOACTIVE']['M'+filt_month];
                }

                if(inval['YEAR']['TAR'] > 0) {
                    YearPct = (inval['YEAR']['ACT'] / inval['YEAR']['TAR']) * 100;
                } else {
                    YearPct = 0;
                }

                SaTfoot =
                    "<tr>"+
                        "<td colspan='2'>ยอดขาย พนง.ที่ลาออก</td>"+
                        "<td class='text-right'>"+number_format(NoActive,0)+"</td>"+
                        "<td>&nbsp;</td>"+
                    "</tr>"+
                    "<tr class='text-white' style='font-weight: bold; background-color: #9A1118;'>"+
                        "<td>รวมทั้งปี "+filt_year+"</td>"+
                        "<td class='text-right'>"+number_format(inval['YEAR']['TAR'],0)+"</td>"+
                        "<td class='text-right'>"+number_format(inval['YEAR']['ACT'],0)+"</td>"+
                        "<td class='text-center'>"+number_format(YearPct,2)+"</td>"+
                    "</tr>";
                $("#SAContent table.table tfoot").append(SaTfoot);
            });

            $(".btn_SAbySlp").on("click", function(e) {
                e.preventDefault();
                var SlpName  = $(this).attr("data-Name");
                var TeamCode = $(this).attr("data-TeamCode");
                var UserKey  = $(this).attr("data-Ukey");
                GetSaleBySlp(TeamCode,UserKey,SlpName,SAL_JSON);
            })
        }
    })
}

function GetSaleBySlp(TeamCode,UserKey,SlpName,SAL_JSON) {
    var TeamCode = TeamCode;
    var UserKey  = UserKey;
    var SlpName  = SlpName;

    var Thead = 
        "<div class='table-responsive'>"+
            "<table class='table table-bordered'>"+
                "<thead class='text-center'>"+
                    "<tr class='text-white' style='background-color: #9A1118;'>"+
                        "<th>เดือน</th>"+
                        "<th>เป้าขาย<br/>(บาท)</th>"+
                        "<th>ยอดขาย<br/>(บาท)</th>"+
                        "<th>%</th>"+
                    "</tr>"+
                "</thead>"+
                "<tbody></tbody>"+
            "</table>"+
        "</div>";
    
    $("div.modal#SaleByMonth .modal-content .modal-body").html(Thead);
    $("#txt_TeamName").html(SlpName+" ["+$("#filt_year").val()+"]");
    var obj = jQuery.parseJSON(SAL_JSON);
    $.each(obj, function(key, inval) {
        var TarY = ActY = 0;
        for(m = 1; m <= 12; m++) {
            if(m <= 9) {
                TarM = inval[TeamCode][UserKey]['TAR']['M0'+m];
                if(typeof inval[TeamCode][UserKey]['ACT'] !== "undefined") {
                    ActM = inval[TeamCode][UserKey]['ACT']['M0'+m];
                } else {
                    ActM = 0;
                }
            } else {
                TarM = inval[TeamCode][UserKey]['TAR']['M'+m];
                if(typeof inval[TeamCode][UserKey]['ACT'] !== "undefined") {
                    ActM = inval[TeamCode][UserKey]['ACT']['M'+m];
                } else {
                    ActM = 0;
                }
            }

            TarY = TarY + parseFloat(TarM);
            ActY = ActY + parseFloat(ActM);

            if(TarM > 0) {
                PctM = (ActM / TarM) * 100;
            } else {
                PctM = 0;
            }

            if(TarY > 0) {
                PctY = (ActY / TarY) * 100;
            } else {
                PctY = 0;
            }
            
            if(PctM >= 100.01) {    
                PctMClass = "bg-success text-white";
            } else if(PctM >= 80.01 && PctM < 100) {
                PctMClass = "text-success";
            } else if(PctM >= 60.01 && PctM < 80) {
                PctMClass = "text-warning";
            } else if(PctM >= 40.01 && PctM < 60) {
                PctMClass = "text-danger";
            } else {
                PctMClass = "";
            }

            if(PctY >= 100.01) {    
                PctYClass = "bg-success text-white";
            } else if(PctY >= 80.01 && PctY < 100) {
                PctYClass = "text-success";
            } else if(PctY >= 60.01 && PctY < 80) {
                PctYClass = "text-warning";
            } else if(PctY >= 40.01 && PctY < 60) {
                PctYClass = "text-danger";
            } else {
                PctYClass = "";
            }

            Tbody =
                "<tr>"+
                    "<td>"+ThaiMonth(m)+"</td>"+
                    "<td class='text-right' style='font-weight: bold;'>"+number_format(TarM,0)+"</td>"+
                    "<td class='text-right'>"+number_format(ActM,0)+"</td>"+
                    "<td class='text-center "+PctMClass+"'>"+number_format(PctM,2)+"</td>"+
                "</tr>"; 
            $("div.modal#SaleByMonth .modal-content .modal-body table.table tbody").append(Tbody);
        }
        Tfoot =
            "<tr class='table-active' style='font-weight: bold'>"+
                "<td>รวมทั้งปี</td>"+
                "<td class='text-right'>"+number_format(TarY,0)+"</td>"+
                "<td class='text-right'>"+number_format(ActY,0)+"</td>"+
                "<td class='text-center "+PctYClass+"'>"+number_format(PctY,2)+"</td>"+
            "</tr>"; 
        $("div.modal#SaleByMonth .modal-content .modal-body table.table tbody").append(Tfoot);
    });
    $("div.modal#SaleByMonth").modal("show");
}
</script> 
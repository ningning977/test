<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>

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
<!-- Alert Remark -->
<div class="modal fade" id="ModalAlertRemark" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h1 class="modal-title text-center" id="HeaderModalAlertRemark"></h1>
                <p id="DetailModalAlertRemark" class="my-3 text-primary"></p>
                <button type="button" class="btn btn-sm btn-secondary w-25 mt-4" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
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
                    <li class="nav-item" role="presentation">
                        <a href="#SaleByItemStatus" class="btn-tabs nav-link" id="SaleByItemStatus-tab" data-bs-toggle="tab" data-bs-target="#SaleByItemStatus" role="tab" data-tabs="0" aria-controls="SaleByItemStatus" aria-selected="false">
                            <i class="fas fa-toolbox fa-fw fa-1x"></i> ยอดขายรายสถานะสินค้า
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane show active" id="SaleByMonth" role="tabpanel" aria-labelledby="SaleByMonth-tab">
                        <div class="row mt-4">
                            <div class="col-lg-1 col-5">
                                <div class="form-group">
                                    <label for="filt_year">เลือกปี</label>
                                    <select class="form-select form-select-sm" id="filt_year">
                                    <?php
                                        for($y = $this_year; $y >= $start_year; $y--) {
                                            if($y == $this_year) {
                                                $y_slct = " selected";
                                            } else {
                                                // $y_slct = " disabled";
                                                $y_slct = "";
                                            }
                                            echo "<option value='$y'$y_slct>$y</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-7">
                                <div class="form-group">
                                    <label for="filt_month">เลือกเดือน</label>
                                    <select class="form-select form-select-sm" id="filt_month">
                                    <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            if($m == $this_month) {
                                                $m_slct = " selected";
                                            } else {
                                                $m_slct = "";
                                            }
                                            echo "<option value='$m'$m_slct>".FullMonth($m)."</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                                $DeptCode = $_SESSION['DeptCode'];
                                $LvCode   = $_SESSION['LvCode'];
                                $Opt_ALL = " disabled";
                                $Opt_MT1 = " disabled";
                                $Opt_EXP = " disabled";
                                $Opt_MT2 = " disabled";
                                $Opt_TT2 = " disabled";
                                $Opt_TT1 = " disabled";
                                $Opt_OUL = " disabled";
                                $Opt_ONL = " disabled";

                                switch($DeptCode) {
                                    case "DP005": $Opt_TT2 = ""; break;
                                    case "DP006": $Opt_MT1 = ""; $Opt_EXP = ""; break;
                                    case "DP007": $Opt_MT2 = ""; break;
                                    case "DP008": $Opt_TT1 = ""; $Opt_OUL = ""; break;
                                    case "DP003":
                                        if($LvCode == "LV104" || $LvCode == "LV105" || $LvCode == "LV106") {
                                            $Opt_ONL = "";
                                        } else {
                                            $Opt_ALL = "";
                                            $Opt_MT1 = "";
                                            $Opt_EXP = "";
                                            $Opt_MT2 = "";
                                            $Opt_TT2 = "";
                                            $Opt_TT1 = "";
                                            $Opt_OUL = "";
                                            $Opt_ONL = "";
                                        }
                                    break;
                                    default:
                                        $Opt_ALL = "";
                                        $Opt_MT1 = "";
                                        $Opt_EXP = "";
                                        $Opt_MT2 = "";
                                        $Opt_TT2 = "";
                                        $Opt_TT1 = "";
                                        $Opt_OUL = "";
                                        $Opt_ONL = "";
                                    break;
                                }
                            ?>
                            <div class="col-lg-3 col-12">
                                <div class="form-group">
                                    <label for="filt_team">เลือกทีม</label>
                                    <select class="form-select form-select-sm" name="filt_team" id="filt_team">
                                        <option value="ALL">ทุกทีม</option>
                                        <option value="MT1" <?php echo $Opt_MT1; ?>><?php echo SATeamName("MT1"); ?></option>
                                        <option value="EXP" <?php echo $Opt_EXP; ?>><?php echo SATeamName("EXP"); ?></option>
                                        <option value="MT2" <?php echo $Opt_MT2; ?>><?php echo SATeamName("MT2"); ?></option>
                                        <option value="TT2" <?php echo $Opt_TT2; ?>><?php echo SATeamName("TT2"); ?></option>
                                        <option value="TT1" <?php echo $Opt_TT1; ?>><?php echo SATeamName("TT1"); ?></option>
                                        <option value="OUL" <?php echo $Opt_OUL; ?>><?php echo SATeamName("OUL"); ?></option>
                                        <option value="ONL" <?php echo $Opt_ONL; ?>><?php echo SATeamName("ONL"); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="SAContent"></div>
                    </div>

                    <div class="tab-pane show" id="SaleByItemStatus" role="tabpanel" aria-labelledby="SaleByItemStatus-tab">
                        <div class="row mt-4">
                            <div class="col-lg-1 col-5">
                                <div class="form-group">
                                    <label for="item_year">เลือกปี</label>
                                    <select class="form-select form-select-sm" id="item_year">
                                    <?php
                                        for($y = $this_year; $y >= $start_year; $y--) {
                                            if($y == $this_year) {
                                                $y_slct = " selected";
                                            } else {
                                                $y_slct = "";
                                            }
                                            echo "<option value='$y'$y_slct>$y</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-7">
                                <div class="form-group">
                                    <label for="item_month">เลือกเดือน</label>
                                    <select class="form-select form-select-sm" id="item_month">
                                    <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            if($m == $this_month) {
                                                $m_slct = " selected";
                                            } else {
                                                $m_slct = "";
                                            }
                                            echo "<option value='$m'$m_slct>".FullMonth($m)."</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="form-group">
                                    <label for="item_team">เลือกทีม</label>
                                    <select class="form-select form-select-sm" name="item_team" id="item_team">
                                        <option value="ALL">ทุกทีม</option>
                                        <option value="MT1" <?php echo $Opt_MT1; ?>><?php echo SATeamName("MT1"); ?></option>
                                        <option value="EXP" <?php echo $Opt_EXP; ?>><?php echo SATeamName("EXP"); ?></option>
                                        <option value="MT2" <?php echo $Opt_MT2; ?>><?php echo SATeamName("MT2"); ?></option>
                                        <option value="TT2" <?php echo $Opt_TT2; ?>><?php echo SATeamName("TT2"); ?></option>
                                        <option value="TT1" <?php echo $Opt_TT1; ?>><?php echo SATeamName("TT1"); ?></option>
                                        <option value="OUL" <?php echo $Opt_OUL; ?>><?php echo SATeamName("OUL"); ?></option>
                                        <option value="ONL" <?php echo $Opt_ONL; ?>><?php echo SATeamName("ONL"); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div id="ApexChart" style="max-height: 320px; !important"></div>
                            </div>
                            <div class="col-6">
                                <div id="ItemContent"></div>
                            </div>
                        </div>
                    </div>
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

<script src="../../js/extensions/apexcharts.js"></script>
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

function GetSaleByMonth(TeamCode, SAL_JSON) {

    var TeamArr  = ["MT1","MT2","TT2","TT1","OUL","ONL","EXP"];

    var TeamCode = TeamCode;
    var TeamName = "";
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
    var Tbody = "";

    switch(TeamCode) {
        case "MT1": TeamName = "ทีมโมเดิร์นเทรด 1"; break;
        case "MT2": TeamName = "ทีมโมเดิร์นเทรด 2"; break;
        case "TT2": TeamName = "ทีมต่างจังหวัด";    break;
        case "TT1": TeamName = "ทีมกรุงเทพฯ";     break;
        case "OUL": TeamName = "ทีมหน้าร้าน";      break;
        case "ONL": TeamName = "ทีมออนไลน์";     break;
        case "EXP": TeamName = "ทีมต่างประเทศ";   break;
        case "ALL": TeamName = "ทุกทีม";   break;
    }

    $("div.modal#SaleByMonth .modal-content .modal-body").html(Thead);
    $("#txt_TeamName").html(TeamName);

    var obj = jQuery.parseJSON(SAL_JSON);
    $.each(obj, function(key, inval) {
        switch(TeamCode) {
            case "ALL":
                var TarY = ActY = 0;
                for(m = 1; m <= 12; m++) {
                    var TarM = ActM = 0;
                    for(t = 0; t < TeamArr.length; t++) {
                        if(m <= 9) {
                            TarM = TarM + parseFloat(inval[TeamArr[t]]['TAR']['M0'+m]);
                            if(typeof inval[TeamArr[t]]['ACT'] !== "undefined") {
                                ActM = ActM + parseFloat(inval[TeamArr[t]]['ACT']['M0'+m]);
                            } else {
                                ActM = ActM;
                            }
                        } else {
                            TarM = TarM + parseFloat(inval[TeamArr[t]]['TAR']['M'+m]);
                            if(typeof inval[TeamArr[t]]['ACT'] !== "undefined") {
                                ActM = ActM + parseFloat(inval[TeamArr[t]]['ACT']['M'+m]);
                            } else {
                                ActM = ActM;
                            }
                        }
                    }

                    ActY = ActY + ActM;
                    if(TarM > 0) {
                        PctM = (ActM / TarM) * 100;
                    } else {
                        PctM = 0;
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
                    Tbody =
                        "<tr>"+
                            "<td>"+ThaiMonth(m)+"</td>"+
                            "<td class='text-right' style='font-weight: bold;'>"+number_format(TarM,0)+"</td>"+
                            "<td class='text-right'>"+number_format(ActM,0)+"</td>"+
                            "<td class='text-center "+PctMClass+"'>"+number_format(PctM,2)+"</td>"+
                        "</tr>"; 
                    $("div.modal#SaleByMonth .modal-content .modal-body table.table tbody").append(Tbody);
                }
                for(t = 0; t < TeamArr.length; t++) {
                    TarY = TarY + parseFloat(inval[TeamArr[t]]['TAR']['YEAR']);
                }
                if(TarY > 0) {
                    PctY = (ActY / TarY) * 100;
                } else {
                    PctY = 0;
                }
                
                if(PctY >= 100.01) {  
                    PctYClass = "bg-success text-white";
                } else if(PctY >= 80.01 && PctY < 100) {
                    PctYClass = "text-success";
                } else if(PctY >= 50.01 && PctY < 75) {
                    PctMClass = "text-warning";
                } else if(PctY >= 25.01 && PctY < 50) {
                    PctYClass = "text-danger";
                } else {
                    PctYClass = "";
                }
                Tfoot =
                        "<tr class='table-active' style='font-weight: bold'>"+
                            "<td>รวมทั้งหมด</td>"+
                            "<td class='text-right' style='font-weight: bold;'>"+number_format(TarY,0)+"</td>"+
                            "<td class='text-right'>"+number_format(ActY,0)+"</td>"+
                            "<td class='text-center "+PctYClass+"'>"+number_format(PctY,2)+"</td>"+
                        "</tr>"; 
                    $("div.modal#SaleByMonth .modal-content .modal-body table.table tbody").append(Tfoot);
            break;
            default:
                for(m = 1; m <= 12; m++) {
                    var TarM = "";
                    if(m <= 9) {
                        TarM = inval[TeamCode]['TAR']['M0'+m];
                        if(typeof inval[TeamCode]['ACT'] !== "undefined") {
                            ActM = inval[TeamCode]['ACT']['M0'+m];
                        } else {
                            ActM = 0;
                        }
                    } else {
                        TarM = inval[TeamCode]['TAR']['M'+m];
                        if(typeof inval[TeamCode]['ACT'] !== "undefined") {
                            ActM = inval[TeamCode]['ACT']['M'+m];
                        } else {
                            ActM = 0;
                        }
                    }

                    TarY = inval[TeamCode]['TAR']['YEAR'];
                    if(typeof inval[TeamCode]['ACT'] !== "undefined") {
                        ActY = inval[TeamCode]['ACT']['YEAR'];
                    } else {
                        ActY = 0;
                    }

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
                    } else if(PctY >= 50.01 && PctY < 75) {
                        PctMClass = "text-warning";
                    } else if(PctY >= 25.01 && PctY < 50) {
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
            break;
        }
    });
    $("div.modal#SaleByMonth").modal("show");
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

function GetItemKPI() {
    var filt_year  = $("#item_year").val();
    var filt_month = $("#item_month").val();
    var filt_team  = $("#item_team").val();

    var SaThead = 
        "<div class='table-responsive mt-4'>"+
            "<table class='table table-bordered'>"+
                "<thead class='text-center'>"+
                    "<tr class='text-white' style='background-color: #9A1118;'>"+
                        "<th>สถานะสินค้า</th>"+
                        "<th>ต้นทุนรวม<br/>(บาท)</th>"+
                        "<th>ยอดขายรวม<br/>(บาท)</th>"+
                        "<th>กำไรรวม<br/>(บาท)</th>"+
                        "<th>%</th>"+
                    "</tr>"+
                "</thead>"+
                "<tbody>"+
                "</tbody>"+
                "<tfoot>"+
                "</tfoot>"+
            "</table>"+
        "</div>";

    $("#ItemContent").html(SaThead);
    $(".overlay").show();
    $.ajax({
        url: "menus/marketing/ajax/ajaxsalekpi.php?p=ItemKPI",
        type: "POST",
        data: {
            y: filt_year,
            m: filt_month,
            t: filt_team
        },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                var Rows = parseFloat(inval['Rows']);
                var SaTbody = "";
                var Cost = Sale = Profit = 0;

                var ChartStatus = [];
                var ChartCost = [];
                var ChartSales = [];
                var ChartPercent = [];
                var ChartTarPcnt = [];

                if(Rows == 0) {
                    SaTbody =
                            "<tr>"+
                                "<td class='text-center' colspan='5'>ไม่มีข้อมูล :(</td>"+
                            "</tr>";
                    $("#ItemContent table.table tbody").append(SaTbody);
                    $("#ApexChart").html('');
                } else {
                    for(i = 0; i < Rows; i++) {
                        SaTbody =
                            "<tr>"+
                                "<td>"+inval[i]['Status']+"</td>"+
                                "<td class='text-right'>"+number_format(inval[i]['COST'],2)+"</td>"+
                                "<td class='text-right'>"+number_format(inval[i]['SALE'],2)+"</td>"+
                                "<td class='text-right'>"+number_format(inval[i]['PRFT'],2)+"</td>"+
                                "<td class='text-center'>"+number_format(inval[i]['PCNTPRFT'],2)+"</td>"+
                            "</tr>";
                        $("#ItemContent table.table tbody").append(SaTbody);
                        Cost   = Cost + parseFloat(inval[i]['COST']);
                        Sale   = Sale + parseFloat(inval[i]['SALE']);
                        Profit = Profit + parseFloat(inval[i]['PRFT']);
                        ChartStatus.push(inval[i]['Status']);
                        ChartCost.push(inval[i]['COST']);
                        ChartSales.push(inval[i]['SALE']);
                        ChartPercent.push(inval[i]['PCNTPRFT']);
                        ChartTarPcnt.push(25);
                    }

                    if(Profit > 0) {
                        var Percent = (Profit / Sale) * 100;
                    } else {
                        var Percent = 0;
                    }

                    SaTfoot =
                        "<tr class='table-active' style='font-weight: bold;'>"+
                            "<td class='text-center'>รวมทั้งหมด</td>"+
                                "<td class='text-right'>"+number_format(Cost,2)+"</td>"+
                                "<td class='text-right'>"+number_format(Sale,2)+"</td>"+
                                "<td class='text-right'>"+number_format(Profit,2)+"</td>"+
                                "<td class='text-center'>"+number_format(Percent,2)+"</td>"+
                        "</tr>";
                    $("#ItemContent table.table tfoot").append(SaTfoot);


                    $("#ApexChart").html('');

                    /* APEX CHART */
                    var options = {
                        chart: {
                            fontFamily: 'https://fonts.googleapis.com/css2?family=Niramit:wght@200;300;400;500;600&family=Noto+Sans+Thai:wght@300;400;500&display=swap',
                            // height: 350,
                            type: "line",
                            toolbar: {
                                show: true,
                                tools:
                                    {
                                        download: false,
                                        selection: false,
                                        pan: false,
                                        reset: false,
                                    }
                            }
                        },
                        markers: {
                            show: true,
                            size: 5
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#9A1118','#114C9A','#119A4F','#FFA200'],
                        plotOptions: {
                            bar: { columnWidth: '50%' }
                        },
                        fill: {
                            opacity: 1
                        },
                        stroke: {
                            linecap: 'round',
                            dashArray: [0,0,5,5]
                        },
                        noData: {
                            text: 'กำลังโหลด...'
                        },
                        series: [
                            {
                                name: 'ต้นทุน (บาท)',
                                type: 'column',
                                data: ChartCost,
                            },
                            {
                                name: 'ยอดขาย (บาท)',
                                type: 'column',
                                data: ChartSales
                            },
                            {
                                name: '% กำไร',
                                type: 'line',
                                data: ChartPercent
                            },
                            {
                                name: '% เป้ากำไร',
                                type: 'line',
                                data: ChartTarPcnt
                            }
                        ],
                        xaxis: {
                            categories: ChartStatus,
                            tickPlacement: 'on',
                            title: {
                                text: 'สถานะ'
                            }
                        },
                        yaxis: [
                            {
                                seriesName: 'Amount',
                                max: 35000000,
                                min: 0,
                                title: {
                                    text: 'จำนวนเงิน (บาท)'
                                },
                                labels: {
                                    formatter: function (val) {
                                        return val.toLocaleString();
                                    }
                                },
                                axisBorder: {
                                    show: true
                                },
                                axisTicks: {
                                    show: true
                                }

                            }, {
                                seriesName: 'Amount',
                                max: 35000000,
                                min: 0,
                                show: false
                            }, {
                                seriesName: 'Profit',
                                opposite: true,
                                max: 100,
                                min: 0,
                                title: {
                                    text: 'กำไร (%)'
                                },
                                labels: {
                                    formatter: function (val) {
                                        return val.toLocaleString();
                                    }
                                },
                                axisBorder: {
                                    show: true
                                },
                                axisTicks: {
                                    show: true
                                }
                            }, {
                                seriesName: 'Profit',
                                max: 100,
                                min: 0,
                                show: false
                            }
                        ],
                        tooltip: {
                            shared: false,
                            intersect: false,
                            y: {
                                formatter: function(y) {
                                    if(typeof y !== "undefined") {
                                        return y.toLocaleString();
                                    }
                                    return y;
                                }
                            }
                        }
                    }

                    var chart = new ApexCharts(document.querySelector("#ApexChart"), options);
                    chart.render();
                }

                
            });
        }
    });
}

function GetSaleKPI() {
    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team  = $("#filt_team").val();

    var MT1_TarM = MT1_TarA = MT1_TarY = "";
    var MT2_TarM = MT2_TarA = MT2_TarY = "";
    var TT1_TarM = TT1_TarA = TT1_TarY = "";
    var TT2_TarM = TT2_TarA = TT2_TarY = "";
    var OUL_TarM = OUL_TarA = OUL_TarY = "";
    var ONL_TarM = ONL_TarA = OUL_TarY = "";
    var PctClass = SAL_JSON = "";

    var txt_year = parseFloat(filt_year);
    var txt_month = $("#filt_month option:selected").text();
    var SaTbody = SaTfoot = "";
    $(".overlay").show();
    $.ajax({
        url: "../ceo/ajax/ajaxCEO.php?p=SaleKPI",
        type: "POST",
        data: {
            y: filt_year,
            m: filt_month,
            t: filt_team
        },
        success: function(result) {
            $(".overlay").hide();
            SAL_JSON = result;
            
            var obj = jQuery.parseJSON(result);
            var SUM_TarM = SUM_TarA = SUM_TarY = 0;
            var SUM_ActM = SUM_ActA = SUM_ActY = 0;
            var SUM_PctM = SUM_PctA = SUM_PctY = 0;
            $.each(obj, function(key, inval) {
                var TeamCode = ["MT1","MT2","TT2","TT1","OUL","ONL","EXP"];
                var TeamName = [
                            "โมเดิร์นเทรด 1<br/>(K. ส้ม)",
                            "โมเดิร์นเทรด 2<br/>(K. มุ่น)",
                            "ต่างจังหวัด<br/>(K. นิด)",
                            "กรุงเทพฯ<br/>(K. แจ็ค)",
                            "หน้าร้าน<br/>(K. แจ็ค)",
                            "ออนไลน์<br/>(K. วุฒิ)",
                            "ต่างประเทศ<br/>(K. ส้ม)",
                        ];
                switch(filt_team) {
                    case "ALL":
                        var SaThead = 
                            "<div class='table-responsive'>"+
                                "<table class='table table-bordered'>"+
                                    "<thead class='text-center'>"+
                                        "<tr class='text-white' style='background-color: #9A1118;'>"+
                                            "<th>ทีม</th>"+
                                            "<th>รายละเอียด</th>"+
                                            "<th>เป้าขาย<br/>(บาท)</th>"+
                                            "<th>ยอดขาย<br/>(บาท)</th>"+
                                            "<th>%</th>"+
                                        "</tr>"+
                                    "</thead>"+
                                    "<tbody>"+
                                    "</tbody>"+
                                "</table>"+
                            "</div>";
                        
                        $("#SAContent").html(SaThead);
                        for(t = 0; t < TeamCode.length; t++) {
                            var b = {};
                            if(filt_month <= 9) {
                                b[TeamCode[t]+'_TarM'] = inval[TeamCode[t]]['TAR']['M0'+filt_month];

                                if(typeof inval[TeamCode[t]]['ACT'] !== "undefined") {
                                    b[TeamCode[t]+'_ActM'] = inval[TeamCode[t]]['ACT']['M0'+filt_month];
                                } else {
                                    b[TeamCode[t]+'_ActM'] = 0;
                                }
                            } else {
                                b[TeamCode[t]+'_TarM'] = inval[TeamCode[t]]['TAR']['M'+filt_month];

                                if(typeof inval[TeamCode[t]]['ACT'] !== "undefined") {
                                    b[TeamCode[t]+'_ActM'] = inval[TeamCode[t]]['ACT']['M'+filt_month];
                                } else {
                                    b[TeamCode[t]+'_ActM'] = 0;
                                }
                            }
                            if(typeof inval[TeamCode[t]]['ACT'] !== "undefined") {
                                if(b[TeamCode[t]+'_TarM'] > 0) {
                                    b[TeamCode[t]+'_PctM'] = (b[TeamCode[t]+'_ActM'] / b[TeamCode[t]+'_TarM'])*100;
                                } else {
                                    b[TeamCode[t]+'_PctM'] = 0;
                                }
                                if(inval[TeamCode[t]]['TAR']['YEAR'] > 0) {
                                    b[TeamCode[t]+'_PctY'] = (inval[TeamCode[t]]['ACT']['YEAR'] / inval[TeamCode[t]]['TAR']['YEAR'])*100;
                                } else {
                                    b[TeamCode[t]+'_PctY'] = 0;
                                }
                                b[TeamCode[t]+'_ActY'] = inval[TeamCode[t]]['ACT']['YEAR']
                            } else {
                                b[TeamCode[t]+'_PctM'] = b[TeamCode[t]+'_PctY'] = b[TeamCode[t]+'_ActY'] = 0;
                            }
                            if(b[TeamCode[t]+'_PctM'] >= 100.01) {
                                PctMClass = "bg-success text-white";
                            } else if(b[TeamCode[t]+'_PctM'] >= 80.01 && b[TeamCode[t]+'_PctM'] < 100) {
                                PctMClass = "text-success";
                            } else if(b[TeamCode[t]+'_PctM'] >= 60.01 && b[TeamCode[t]+'_PctM'] < 80) {
                                PctMClass = "text-warning";
                            } else if(b[TeamCode[t]+'_PctM'] >= 40.01 && b[TeamCode[t]+'_PctM'] < 60) {
                                PctMClass = "text-danger";
                            } else {
                                PctMClass = "";
                            }

                            if(b[TeamCode[t]+'_PctY'] >= 100.01) {
                                PctMClass = "bg-success text-white";
                            } else if(b[TeamCode[t]+'_PctY'] >= 80.01 && b[TeamCode[t]+'_PctY'] < 100) {
                                PctYClass = "text-success";
                            } else if(b[TeamCode[t]+'_PctY'] >= 60.01 && b[TeamCode[t]+'_PctY'] < 80) {
                                PctYClass = "text-warning";
                            } else if(b[TeamCode[t]+'_PctY'] >= 40.01 && b[TeamCode[t]+'_PctY'] < 60) {
                                PctYClass = "text-danger";
                            } else {
                                PctYClass = "";
                            }

                            SaTbody =
                                "<tr>"+
                                    "<td rowspan='2'>"+TeamName[t]+"</td>"+
                                    "<td>เดือน"+txt_month+"</td>"+
                                    "<td class='text-right' style='font-weight: bold;'>"+number_format(b[TeamCode[t]+'_TarM'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(b[TeamCode[t]+'_ActM'],0)+"</td>"+
                                    "<td class='text-center "+PctMClass+"'>"+number_format(b[TeamCode[t]+'_PctM'],2)+"</td>"+
                                "</tr>"+
                                "<tr class='table-active' style='font-weight: bold'>"+
                                    "<td><a href='javascript:void(0);' class='btn_SAbyMonth' data-TeamCode='"+TeamCode[t]+"'><i class='fas fa-clipboard-list fa-fw fa-lg'></i> ทั้งปี "+txt_year+"</a></td>"+
                                    "<td class='text-right'>"+number_format(inval[TeamCode[t]]['TAR']['YEAR'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(b[TeamCode[t]+'_ActY'],0)+"</td>"+
                                    "<td class='text-center "+PctYClass+"'>"+number_format(b[TeamCode[t]+'_PctY'],2)+"</td>"+
                                "</tr>";
                            
                            SUM_TarM = SUM_TarM + parseFloat(b[TeamCode[t]+'_TarM']);
                            SUM_ActM = SUM_ActM + parseFloat(b[TeamCode[t]+'_ActM']);
                            SUM_TarY = SUM_TarY + parseFloat(inval[TeamCode[t]]['TAR']['YEAR']);
                            SUM_ActY = SUM_ActY + parseFloat(b[TeamCode[t]+'_ActY']);
                            $("#SAContent table.table tbody").append(SaTbody);
                        }
                        if(SUM_TarM > 0) {
                            SUM_PctM = (SUM_ActM / SUM_TarM) * 100;
                        } else {
                            SUM_PctM = 0;
                        }

                        if(SUM_TarY > 0) {
                            SUM_PctY = (SUM_ActY / SUM_TarY) * 100;
                        } else {
                            SUM_PctY = 0;
                        }

                        if(SUM_PctM >= 100.01){
                            PctMClass = "bg-success text-white";
                        } else if(SUM_PctM >= 80.01 && SUM_PctM < 100) {
                            PctMClass = "text-success";
                        } else if(SUM_PctM >= 60.01 && SUM_PctM < 80) {
                            PctMClass = "text-warning";
                        } else if(SUM_PctM >= 40.01 && SUM_PctM < 60) {
                            PctMClass = "text-danger";
                        } else {
                            PctMClass = null;
                        }

                        if(SUM_PctY >= 100.01) {
                            PctYClass = "bg-success text-white";
                        } else if(SUM_PctY >= 80.01 && SUM_PctY < 100) {
                            PctYClass = "text-success";
                        } else if(SUM_PctY >= 60.01 && SUM_PctY < 80) {
                            PctYClass = "text-warning";
                        } else if(SUM_PctY >= 40.01 && SUM_PctY < 60) {
                            PctYClass = "text-danger";
                        } else {
                            PctYClass = null;
                        }

                        SaTfoot +=
                            "<tr>"+
                                "<td rowspan='2'>รวมทุกทีม</td>"+
                                "<td>เดือน"+txt_month+"</td>"+
                                "<td class='text-right' style='font-weight: bold;'>"+number_format(SUM_TarM,0)+"</td>"+
                                "<td class='text-right'>"+number_format(SUM_ActM,0)+"</td>"+
                                "<td class='text-center "+PctMClass+"'>"+number_format(SUM_PctM, 2)+"</td>"+
                            "</tr>"+
                            "<tr class='table-active' style='font-weight: bold'>"+
                                "<td><a href='javascript:void(0);' class='btn_SAbyMonth' data-TeamCode='ALL'><i class='fas fa-clipboard-list fa-fw fa-lg'></i> ทั้งปี "+txt_year+"</a></td>"+
                                "<td class='text-right'>"+number_format(SUM_TarY,0)+"</td>"+
                                "<td class='text-right'>"+number_format(SUM_ActY,0)+"</td>"+
                                "<td class='text-center "+PctYClass+"'>"+number_format(SUM_PctY, 2)+"</td>"+
                            "</tr>";
                        $("#SAContent table.table tbody").append(SaTfoot);
                    break;
                    default:
                        var SUM_TarAll  = SUM_ActAll  = 0;
                        var SaThead = 
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
                        var TeamCode = [];
                        for(const [key,value] of Object.entries(inval)) {
                            if(key != "YEAR") {
                                TeamCode.push(`${key}`);
                            }
                        }
                        for(ax = 0; ax < TeamCode.length; ax++) {
                            var TEAM = TeamCode[ax];
                            var b = {};
                            switch(TEAM) {
                                case "MT100": TeamName = "ทีมโมเดิร์นเทรด 1"; break;
                                case "MT200": TeamName = "ทีมโมเดิร์นเทรด 2"; break;
                                case "TT101": TeamName = "ทีมกรุงเทพฯ"; break;
                                case "TT201": TeamName = "ทีมต่างจังหวัด (ทีม 1)"; break;
                                case "TT202": TeamName = "ทีมต่างจังหวัด (ทีม 2)"; break;
                                case "TT203": TeamName = "ทีมต่างจังหวัด (ประเทศลาว)"; break;
                                case "OUL":   TeamName = "ทีมหน้าร้าน"; break;
                                case "ONL":   TeamName = "ทีมออนไลน์"; break;
                                case "EXP101":   TeamName = "ทีมต่างประเทศ"; break;
                            }
                            SaTh =
                                "<tr class='table-danger text-center' style='font-weight: bold;'>"+
                                    "<td colspan='5'>"+TeamName+"</td>"+
                                "</tr>";
                            $("#SAContent table.table tbody").append(SaTh);

                            var UserCode = [];
                            for(const [key,value] of Object.entries(inval[TEAM])) {
                                UserCode.push(`${key}`);
                            }

                            var SUM_TarTeam = SUM_ActTeam = 0;
                            
                            var cx = 0;

                            for(bx = 0; bx < UserCode.length; bx++) {
                                var SlpCode = UserCode[bx];
                                var b = {};

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
                    break;
                }
            });

            $(".btn_SAbyMonth").on("click", function(e) {
                e.preventDefault();
                var TeamCode = $(this).attr("data-TeamCode");
                GetSaleByMonth(TeamCode,SAL_JSON);
            });

            $(".btn_SAbySlp").on("click", function(e) {
                e.preventDefault();
                var SlpName  = $(this).attr("data-Name");
                var TeamCode = $(this).attr("data-TeamCode");
                var UserKey  = $(this).attr("data-Ukey");
                GetSaleBySlp(TeamCode,UserKey,SlpName,SAL_JSON);
            })
        }
    });
}

$(document).ready(function() {
    CallHead();
    GetSaleKPI();
    GetItemKPI();
    // var DeptCode = '<?php echo $DeptCode; ?>';
    // var LvCode   = '<?php echo $LvCode; ?>';
    // var TeamID   = '';
    // switch(DeptCode) {
    //     case "DP005": TeamID = "TT2"; break;
    //     case "DP006": TeamID = "MT1"; break;
    //     case "DP007": TeamID = "MT2"; break;
    //     case "DP008": TeamID = "OUL"; break;
    //     case "DP003":
    //         if(LvCode == "LV104" || LvCode == "LV105" || LvCode == "LV106") {
    //             TeamID = "ONL";
    //         } else {
    //             TeamID = "ALL";
    //         }
    //     break;
    //     default: TeamID = "ALL"; break;
    // }

    $("#filt_year, #filt_month, #filt_team").on("change", function() {
        GetSaleKPI();
    });

    $("#item_year, #item_month, #item_team").on("change", function() {
        GetItemKPI();
    });

    // $("#filt_team").val(TeamID).change();

    $("#HeaderModalAlertRemark").html("<i class='fas fa-exclamation-circle fa-fw fa-lg'></i> คำเตือน");
    $("#DetailModalAlertRemark").html("ข้อมูลในการรายงานยอดขายของบริษัทถือเป็นความลับสุดยอด<br/>ห้ามส่งต่อหรือทำการ Copy เด็ดขาด");
    $("#ModalAlertRemark").modal("show");
});
</script> 
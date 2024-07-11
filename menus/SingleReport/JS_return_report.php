<!-- รายงานการคืน -->
<script> 
    // เมื่อคลิกคืนลดหนี้ // คืน QC
    $("#Debt-tab").on("click", function() {
        $("#btn-RTall").click(); 
        <?php if($_SESSION['DeptCode'] == "DP006") { ?>
            $("#btn-RTMT1").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP007") {?>
            $("#btn-RTMT2").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP008") { ?>
            $("#btn-RTTT1").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP005") { ?>
            $("#btn-RTTT2").click();
        <?php } ?>
    })
    $("#QC-tab").on("click", function() {
        $("#btn-QCall").click(); 
        <?php if($_SESSION['DeptCode'] == "DP006") { ?>
            $("#btn-QCMT1").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP007") {?>
            $("#btn-QCMT2").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP008") { ?>
            $("#btn-QCTT1").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP005") { ?>
            $("#btn-QCTT2").click();
        <?php } ?>
    })
    // เมื่อคลิก Tab คืนลดหนี้ 
    $("#btn-RTall, #btn-RTMT1, #btn-RTMT2, #btn-RTTT1, #btn-RTTT2, #btn-RTOUL, #btn-RTONL, #btn-RTKBI").on('click', function(){
        setTimeout(function(){
            // $("#SelectYearRT").val(new Date().getFullYear()).change();
            $("#SelectYearRT").val($("#SelectYearRT").val()).change();
        }, 500);
    })
    
    // คืนลดหนี้
    function SelectYearRT() {
        var Year = parseInt($("#SelectYearRT").val());
        if ($("#btn-RTall").hasClass("active") == true) { var Team = "all"; }
        if ($("#btn-RTMT1").hasClass("active") == true) { var Team = "MT1"; }
        if ($("#btn-RTMT2").hasClass("active") == true) { var Team = "MT2"; }
        if ($("#btn-RTTT1").hasClass("active") == true) { var Team = "TT1"; }
        if ($("#btn-RTTT2").hasClass("active") == true) { var Team = "TT2"; }
        if ($("#btn-RTOUL").hasClass("active") == true) { var Team = "OUL"; }
        if ($("#btn-RTONL").hasClass("active") == true) { var Team = "ONL"; }
        if ($("#btn-RTKBI").hasClass("active") == true) { var Team = "KBI"; }
        // console.log(Team, " | ", Year);
        $(".overlay").show();
        $.ajax({
            url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=SelectYearRT",
            type: "POST",
            data: { Year : Year, Team : Team, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    var YearSelect =  inval["YearSelect"];
                    var TeamSelect =  inval["TeamSelect"];
                    // ข้อมูล Charts
                        var TEAM =  inval["TEAM"].split('|');
                        var M_1 =  inval["M_1"].split(' ');
                        var M_2 =  inval["M_2"].split(' ');
                        var M_3 =  inval["M_3"].split(' ');
                        var M_4 =  inval["M_4"].split(' ');
                        var M_5 =  inval["M_5"].split(' ');
                        var M_6 =  inval["M_6"].split(' ');
                        var M_7 =  inval["M_7"].split(' ');
                        var M_8 =  inval["M_8"].split(' ');
                        var M_9 =  inval["M_9"].split(' ');
                        var M_10 =  inval["M_10"].split(' ');
                        var M_11 =  inval["M_11"].split(' ');
                        var M_12 =  inval["M_12"].split(' ');
                    // END ข้อมูล Charts

                    // Data for show
                    var vaTeam = TEAM.length-1;
                    switch (TeamSelect) {
                        case "all":
                            $("#HRTAll").html("ยอดคืนปี "+YearSelect);
                            // Charts
                            switch (vaTeam) {
                                <?php $case = 1; $data = 0; ?>
                                <?php for($i = 1; $i <= 10; $i++) { ?>
                                case <?php echo $i; ?>:
                                    chartRTAll.updateSeries([
                                        <?php for($t = 0; $t <= $data; $t++) { ?>
                                            <?php if($t != $data) { ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }, 
                                            <?php }else{ ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }
                                            <?php } ?>
                                        <?php } ?>
                                    ])
                                    chartRTAll.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                    break;
                                <?php $data++; } ?>
                                default: 
                                    chartRTAll.updateSeries([{
                                        name: "ยังไม่มียอดคืน", type: 'bar',
                                        data: [0,0,0,0,0,0,0,0,0,0,0,0]
                                    }])
                                    chartRTAll.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            max: 500000,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                break;
                            }
                            $('#RTAllReportExcel').DataTable().destroy();
                            $('#TbodyRTAll').empty();
                            $("#TbodyRTAll").html(inval["TbodyRT"]);
                            break;
                        case "MT1":
                            $("#HRTMT1").html("ยอดคืนปี "+YearSelect+" ทีม "+TeamSelect);
                            // Charts chartRTMT1
                            switch (vaTeam) {
                                <?php $case = 1; $data = 0; ?>
                                <?php for($i = 1; $i <= 10; $i++) { ?>
                                case <?php echo $i; ?>:
                                    chartRTMT1.updateSeries([
                                        <?php for($t = 0; $t <= $data; $t++) { ?>
                                            <?php if($t != $data) { ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }, 
                                            <?php }else{ ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }
                                            <?php } ?>
                                        <?php } ?>
                                    ])
                                    chartRTMT1.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect+" : ทีม "+TeamSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                    break;
                                <?php $data++; } ?>
                                default: 
                                    chartRTMT1.updateSeries([{
                                        name: "ยังไม่มียอดคืน", type: 'bar',
                                        data: [0,0,0,0,0,0,0,0,0,0,0,0]
                                    }])
                                    chartRTMT1.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            max: 500000,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                break;
                            }
                            $('#RTReportMT1Excel').DataTable().destroy();
                            $('#TbodyRTMT1').empty();
                            $("#TbodyRTMT1").html(inval["TbodyRT"]);
                            break;
                        case "MT2":
                            $("#HRTMT2").html("ยอดคืนปี "+YearSelect+" ทีม "+TeamSelect);
                            // Charts chartRTMT2
                            switch (vaTeam) {
                                <?php $case = 1; $data = 0; ?>
                                <?php for($i = 1; $i <= 10; $i++) { ?>
                                case <?php echo $i; ?>:
                                    chartRTMT2.updateSeries([
                                        <?php for($t = 0; $t <= $data; $t++) { ?>
                                            <?php if($t != $data) { ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }, 
                                            <?php }else{ ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }
                                            <?php } ?>
                                        <?php } ?>
                                    ])
                                    chartRTMT2.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect+" : ทีม "+TeamSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                    break;
                                <?php $data++; } ?>
                                default: 
                                    chartRTMT2.updateSeries([{
                                        name: "ยังไม่มียอดคืน", type: 'bar',
                                        data: [0,0,0,0,0,0,0,0,0,0,0,0]
                                    }])
                                    chartRTMT2.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            max: 500000,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                break;
                            }
                            $('#RTReportMT2Excel').DataTable().destroy();
                            $('#TbodyRTMT2').empty();
                            $("#TbodyRTMT2").html(inval["TbodyRT"]);
                            break;
                        case "TT1":
                            $("#HRTTT1").html("ยอดคืนปี "+YearSelect+" ทีม "+TeamSelect+" กทม.");
                            // Charts chartRTTT1
                            switch (vaTeam) {
                                <?php $case = 1; $data = 0; ?>
                                <?php for($i = 1; $i <= 10; $i++) { ?>
                                case <?php echo $i; ?>:
                                    chartRTTT1.updateSeries([
                                        <?php for($t = 0; $t <= $data; $t++) { ?>
                                            <?php if($t != $data) { ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }, 
                                            <?php }else{ ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }
                                            <?php } ?>
                                        <?php } ?>
                                    ])
                                    chartRTTT1.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect+" : ทีม "+TeamSelect+" กทม.",
                                        },
                                        yaxis: {
                                            min: 0,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                    break;
                                <?php $data++; } ?>
                                default: 
                                    chartRTTT1.updateSeries([{
                                        name: "ยังไม่มียอดคืน", type: 'bar',
                                        data: [0,0,0,0,0,0,0,0,0,0,0,0]
                                    }])
                                    chartRTTT1.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            max: 500000,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                break;
                            }
                            $('#RTReportTT1Excel').DataTable().destroy();
                            $('#TbodyRTTT1').empty();
                            $("#TbodyRTTT1").html(inval["TbodyRT"]);
                            break;
                        case "TT2":
                            $("#HRTTT2").html("ยอดคืนปี "+YearSelect+" "+TeamSelect+" ตจว.");
                            // Charts chartRTTT2
                            switch (vaTeam) {
                                <?php $case = 1; $data = 0; ?>
                                <?php for($i = 1; $i <= 10; $i++) { ?>
                                case <?php echo $i; ?>:
                                    chartRTTT2.updateSeries([
                                        <?php for($t = 0; $t <= $data; $t++) { ?>
                                            <?php if($t != $data) { ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }, 
                                            <?php }else{ ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }
                                            <?php } ?>
                                        <?php } ?>
                                    ])
                                    chartRTTT2.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect+" : "+TeamSelect+" ตจว.",
                                        },
                                        yaxis: {
                                            min: 0,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                    break;
                                <?php $data++; } ?>
                                default: 
                                    chartRTTT2.updateSeries([{
                                        name: "ยังไม่มียอดคืน", type: 'bar',
                                        data: [0,0,0,0,0,0,0,0,0,0,0,0]
                                    }])
                                    chartRTTT2.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            max: 500000,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                break;
                            }
                            $('#RTReportTT2Excel').DataTable().destroy();
                            $('#TbodyRTTT2').empty();
                            $("#TbodyRTTT2").html(inval["TbodyRT"]);
                            break;
                        case "OUL":
                            $("#HRTOUL").html("ยอดคืนปี "+YearSelect+" ทีม หน้าร้าน");
                            // Charts
                            switch (vaTeam) {
                                <?php $case = 1; $data = 0; ?>
                                <?php for($i = 1; $i <= 10; $i++) { ?>
                                case <?php echo $i; ?>:
                                    chartRTOUL.updateSeries([
                                        <?php for($t = 0; $t <= $data; $t++) { ?>
                                            <?php if($t != $data) { ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }, 
                                            <?php }else{ ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }
                                            <?php } ?>
                                        <?php } ?>
                                    ])
                                    chartRTOUL.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect+" : ทีม หน้าร้าน",
                                        },
                                        yaxis: {
                                            min: 0,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                    break;
                                <?php $data++; } ?>
                                default: 
                                    chartRTOUL.updateSeries([{
                                        name: "ยังไม่มียอดคืน", type: 'bar',
                                        data: [0,0,0,0,0,0,0,0,0,0,0,0]
                                    }])
                                    chartRTOUL.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            max: 500000,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                break;
                            }
                            $('#RTReportOULExcel').DataTable().destroy();
                            $('#TbodyRTOUL').empty();
                            $("#TbodyRTOUL").html(inval["TbodyRT"]);
                            break;
                        case "ONL":
                            $("#HRTONL").html("ยอดคืนปี "+YearSelect+" ทีม ออนไลน์");
                            // Charts
                            switch (vaTeam) {
                                <?php $case = 1; $data = 0; ?>
                                <?php for($i = 1; $i <= 10; $i++) { ?>
                                case <?php echo $i; ?>:
                                    chartRTONL.updateSeries([
                                        <?php for($t = 0; $t <= $data; $t++) { ?>
                                            <?php if($t != $data) { ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }, 
                                            <?php }else{ ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }
                                            <?php } ?>
                                        <?php } ?>
                                    ])
                                    chartRTONL.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect+" : ทีม ออนไลน์",
                                        },
                                        yaxis: {
                                            min: 0,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                    break;
                                <?php $data++; } ?>
                                default: 
                                    chartRTONL.updateSeries([{
                                        name: "ยังไม่มียอดคืน", type: 'bar',
                                        data: [0,0,0,0,0,0,0,0,0,0,0,0]
                                    }])
                                    chartRTONL.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            max: 500000,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                break;
                            }
                            $('#RTReportONLExcel').DataTable().destroy();
                            $('#TbodyRTONL').empty();
                            $("#TbodyRTONL").html(inval["TbodyRT"]);
                            break;
                        case "KBI":
                            $("#HRTKBI").html("ยอดคืนปี "+YearSelect+" ทีม ส่วนกลาง");
                            // Charts
                            switch (vaTeam) {
                                <?php $case = 1; $data = 0; ?>
                                <?php for($i = 1; $i <= 10; $i++) { ?>
                                case <?php echo $i; ?>:
                                    chartRTKBI.updateSeries([
                                        <?php for($t = 0; $t <= $data; $t++) { ?>
                                            <?php if($t != $data) { ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }, 
                                            <?php }else{ ?>
                                                {
                                                name: TEAM[<?php echo $t; ?>], type: 'bar',
                                                data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                        M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                        M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                                }
                                            <?php } ?>
                                        <?php } ?>
                                    ])
                                    chartRTKBI.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect+" : ทีม ส่วนกลาง",
                                        },
                                        yaxis: {
                                            min: 0,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                    break;
                                <?php $data++; } ?>
                                default: 
                                    chartRTKBI.updateSeries([{
                                        name: "ยังไม่มียอดคืน", type: 'bar',
                                        data: [0,0,0,0,0,0,0,0,0,0,0,0]
                                    }])
                                    chartRTKBI.updateOptions({
                                        title: {
                                            text: 'ข้อมูลการคืนปี'+' '+YearSelect,
                                        },
                                        yaxis: {
                                            min: 0,
                                            max: 500000,
                                            title: {
                                                    text: 'บาท',
                                            },
                                            labels: {
                                                formatter: function (val) {
                                                    return val.toFixed(0)
                                                }
                                            }
                                        }
                                    })
                                break;
                            }
                            $('#RTReportKBIExcel').DataTable().destroy();
                            $('#TbodyRTKBI').empty();
                            $("#TbodyRTKBI").html(inval["TbodyRT"]);
                            break;
                        default: alert("เกิดข้อผิดพลาด (คืนลดหนี้) กรุณาแจ้งแผนก IT"); break;
                    }
                    Export();
                })
                $(".btn-team").on("click", function(e){
                    e.preventDefault();
                    var DataTeam = $(this).attr("data-team");
                    switch (DataTeam) {
                            case "MT1": $("#btn-RTMT1").click(); break;
                            case "MT2": $("#btn-RTMT2").click(); break;
                            case "TT1": $("#btn-RTTT1").click(); break;
                            case "TT2": $("#btn-RTTT2").click(); break;
                            case "OUL": $("#btn-RTOUL").click(); break;
                            case "ONL": $("#btn-RTONL").click(); break;
                            case "KBI": $("#btn-RTKBI").click(); break;
                            default: break;
                    }
                })
                $(".btn-group").on("click",function(e){
                    e.preventDefault();
                    var team = $(this).attr("data-team");
                    var year = $(this).attr("data-year");
                    var month = $(this).attr("data-month");
                    $.ajax({
                        url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=SelectYearRTgroup",
                        type: 'POST',
                        data: { Team: team, Year: year, Month: month },
                        success: function(result) {
                            var obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) { 
                                var TeamSelect = inval["TeamSelect"];
                                var MonthSelect = inval["MonthSelect"];
                                var YearSelect = inval["YearSelect"];
                                var HeadRTModal =  "<i class='fas fa-file-invoice-dollar' style='font-size: 20px;'></i>&nbsp;&nbsp;ข้อมูลการคืนทีม "+TeamSelect;
                                var TheadRTModal =  "<tr>"+"<th colspan='8' class='text-primary text-center'>ข้อมูลการคืนทีม "+TeamSelect+" เดือน "+MonthSelect+" "+YearSelect+"</th>"+"</tr>"+
                                                    "<tr>"+
                                                        "<th width='3%' class='text-center align-bottom'>No.</th>"+
                                                        "<th width='17%' class='text-center align-bottom border-start'>สาเหตุการคืน</th>"+
                                                        "<th width='13%' class='text-center align-bottom border-start'>ผู้แทนขาย</th>"+
                                                        "<th width='10%' class='text-center align-bottom border-start'>วันที่ลดหนี้</th>"+
                                                        "<th width='10%' class='text-center align-bottom border-start'>เลขที่เอกสาร</th>"+
                                                        "<th width='10%' class='text-center align-bottom border-start'>เอกสารอ้างอิง</th>"+
                                                        "<th class='text-center align-bottom border-start'>ชื่อลูกค้า</th>"+
                                                        "<th width='8%' class='text-center align-bottom border-start'>มูลค่า</th>"+
                                                    "</tr>";
                                var column1 =  inval["column1"].split('|');
                                var column2 =  inval["column2"].split('|');
                                var column3 =  inval["column3"].split('|');
                                var column4 =  inval["column4"].split('|');
                                var column5 =  inval["column5"].split('|');
                                var column6 =  inval["column6"].split('|');
                                var column7 =  inval["column7"].split('|');
                                var column8 =  inval["column8"].split('|');
                                var DocEntry =  inval["DocEntry"].split('|');
                                var Tbody = "";
                                for (var i = 0; i < column1.length-1; i++) {
                                    Tbody += "<tr>"+
                                                "<td class='text-center'>"+column1[i]+"</td>"+
                                                "<td>"+column2[i]+"</td>"+
                                                "<td>"+column3[i]+"</td>"+
                                                "<td class='text-center'>"+column4[i]+"</td>"+
                                                "<td class='text-center'><a class='btn-SR' data-docentry='"+DocEntry[i]+"' href='javascript:void(0);'>"+column5[i]+"</a></td>"+
                                                "<td>"+column6[i]+"</td>"+
                                                "<td>"+column7[i]+"</td>"+
                                                "<td class='text-right text-primary fw-bolder'>"+column8[i]+"</td>"+
                                            "</tr>";
                                }
                                $("#HeadRTModal").html(HeadRTModal);
                                $('#ModalDataExportRT').DataTable().destroy();
                                $('#TheadRTModal, #TbodyRTModal').empty();
                                $("#TheadRTModal").html(TheadRTModal);
                                $("#TbodyRTModal").html(Tbody);
                                ModalExport();
                                $("#ModalViewDataRT").modal("show"); 
                            })
                            $(".btn-SR").on("click", function(e){
                                e.preventDefault();
                                var Year = $("#SelectYearRT").val();
                                var DocEntry = $(this).attr("data-docentry");
                                $.ajax({
                                    url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=RTSelectSR",
                                    type: 'POST',
                                    data: { DocEntry : DocEntry, Year: Year },
                                    success: function(result) {
                                        var obj = jQuery.parseJSON(result);
                                        $.each(obj,function(key,inval) { 
                                            $("#TheadMasterRTModalDetail").html(inval["TheadMaster"]);
                                            $("#TbodyRTModalDetail").html(inval["Tbody"]);
                                            $("#TfooterRTModalDetail").html(inval["Tfooter"]);
                                            $("#ModalViewDataRTDetail").modal('show');
                                        })
                                    }
                                })
                            })
                        }
                        
                    });
                })
            }
        })
        
        $(".overlay").hide();
    }

    // // Rander Charts
    var chartRTAll = new ApexCharts(document.querySelector("#RTAllReport"), DataOption); chartRTAll.render();
    var chartRTMT1 = new ApexCharts(document.querySelector("#RTReportMT1"), DataOption); chartRTMT1.render();
    var chartRTMT2 = new ApexCharts(document.querySelector("#RTReportMT2"), DataOption); chartRTMT2.render();
    var chartRTTT1 = new ApexCharts(document.querySelector("#RTReportTT1"), DataOption); chartRTTT1.render();
    var chartRTTT2 = new ApexCharts(document.querySelector("#RTReportTT2"), DataOption); chartRTTT2.render();
    var chartRTOUL = new ApexCharts(document.querySelector("#RTReportOUL"), DataOption); chartRTOUL.render();
    var chartRTONL = new ApexCharts(document.querySelector("#RTReportONL"), DataOption); chartRTONL.render();
    var chartRTKBI = new ApexCharts(document.querySelector("#RTReportKBI"), DataOption); chartRTKBI.render();

    // คืน QC
    $("#btn-QCall, #btn-QCMT1, #btn-QCMT2, #btn-QCTT1, #btn-QCTT2, #btn-QCOUL, #btn-QCONL, #btn-QCDMN, #btn-QCKBI").on("click", function() {
        var DataTeam = $(this).attr("data-tab");
        var DataYear = new Date().getFullYear();
        $.ajax({
            url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=QC",
            type: "POST",
            data: { Team : DataTeam, Year : DataYear,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    switch (inval['TeamSelect']) {
                        case 'all': $('#QCAllReportExcel').DataTable().destroy(); $("#TbodyQCall").html(inval['Tbody']); Export(); break;
                        case 'MT1': $('#QCMT1ReportExcel').DataTable().destroy(); $("#TbodyQCMT1").html(inval['Tbody']); Export(); break;
                        case 'MT2': $('#QCMT2ReportExcel').DataTable().destroy(); $("#TbodyQCMT2").html(inval['Tbody']); Export(); break;
                        case 'TT1': $('#QCTT1ReportExcel').DataTable().destroy(); $("#TbodyQCTT1").html(inval['Tbody']); Export(); break;
                        case 'TT2': $('#QCTT2ReportExcel').DataTable().destroy(); $("#TbodyQCTT2").html(inval['Tbody']); Export(); break;
                        case 'OUL': $('#QCOULReportExcel').DataTable().destroy(); $("#TbodyQCOUL").html(inval['Tbody']); Export(); break;
                        case 'ONL': $('#QCONLReportExcel').DataTable().destroy(); $("#TbodyQCONL").html(inval['Tbody']); Export(); break;
                        case 'DMN': $('#QCDMNReportExcel').DataTable().destroy(); $("#TbodyQCDMN").html(inval['Tbody']); Export(); break;
                        case 'KBI': $('#QCKBIReportExcel').DataTable().destroy(); $("#TbodyQCKBI").html(inval['Tbody']); Export(); break;
                        default: alert("เกิดข้อผิดพลาด (คืน QC) กรุณาแจ้งแผนก IT"); break;
                    }
                    
                })
            }
        })
    })
</script>
<!-- END รายงานการคืน -->
<!-- รายงานการขาย -->
<script> 
    $(document).ready(function(){
        $(".EXP").hide();
        // $("#sales-report-tab").click();
        // $("#IDall").click();
        // if($("#sales-report-tab").attr("aria-selected") == "true"){
        //     console.log($("#sales-report-tab").attr("aria-selected"));
        //     $("#IDall").click();
        // var thisYear = '<?php echo date("Y"); ?>';
        // $("#YearAll").val(thisYear).change();
        SelectYearAll();
        // }

        <?php if($_SESSION['DeptCode'] == "DP006") { ?>
            $("#IDMT1").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP007") {?>
            $("#IDMT2").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP008") { ?>
            $("#IDTT1").click();
        <?php }elseif($_SESSION['DeptCode'] == "DP005") { ?>
            $("#IDTT2").click();
        <?php } ?>
    });

    // Tab Team
    $("#IDall, #IDMT1, #IDMT2, #IDTT1, #IDTT2, #IDOUL, #IDONL, #IDKBI, #IDEXP").on('click', function(){
        // var TabTeam = $(this).attr("data-tab");
        setTimeout(function(){
            // $("#YearAll").val(new Date().getFullYear()).change();
            $("#YearAll").val($("#YearAll").val()).change();
        }, 500);
    })
    function SelectYearAll() {
        var Year = parseInt($("#YearAll").val());
        if ($("#IDall").hasClass("active") == true) { var Team = "all"; }
        if ($("#IDMT1").hasClass("active") == true) { var Team = "MT1"; }
        if ($("#IDMT2").hasClass("active") == true) { var Team = "MT2"; }
        if ($("#IDTT1").hasClass("active") == true) { var Team = "TT1"; }
        if ($("#IDTT2").hasClass("active") == true) { var Team = "TT2"; }
        if ($("#IDOUL").hasClass("active") == true) { var Team = "OUL"; }
        if ($("#IDONL").hasClass("active") == true) { var Team = "ONL"; }
        if ($("#IDKBI").hasClass("active") == true) { var Team = "KBI"; }
        if ($("#IDEXP").hasClass("active") == true) { var Team = "EXP"; }
        // console.log(Team, " | ", Year);
        $(".overlay").show();
        $.ajax({
            url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=SelectYearAll",
            type: "POST",
            data: { Year : Year, Team : Team, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    // ทีม/ปีที่เลือก
                    var TeamSelect =  inval["TeamSelect"];
                    var YearSelect =  inval["YearSelect"];
                    // ยอดแต่ละเดือน
                        var TEAM =  inval["TEAM"].split('|');
                        var M_1 =  inval["M_1"].split('|');
                        var M_2 =  inval["M_2"].split('|');
                        var M_3 =  inval["M_3"].split('|');
                        var M_4 =  inval["M_4"].split('|');
                        var M_5 =  inval["M_5"].split('|');
                        var M_6 =  inval["M_6"].split('|');
                        var M_7 =  inval["M_7"].split('|');
                        var M_8 =  inval["M_8"].split('|');
                        var M_9 =  inval["M_9"].split('|');
                        var M_10 =  inval["M_10"].split('|');
                        var M_11 =  inval["M_11"].split('|');
                        var M_12 =  inval["M_12"].split('|');
                    // END ยอดแต่ละเดือน

                    // ยอดรวมแต่ละทีม
                        var SumTeam = "";
                        for (var i = 0; i < TEAM.length-1; i++) {
                            switch (i) {
                                case 0:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 1:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 2:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 3:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 4:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 5:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 6:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 7:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 8:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 9:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                case 10:
                                    SumTeam += (parseFloat(M_1[i])+parseFloat(M_2[i])+parseFloat(M_3[i])+parseFloat(M_4[i])+parseFloat(M_5[i])+parseFloat(M_6[i])+parseFloat(M_7[i])+parseFloat(M_8[i])+parseFloat(M_9[i])+parseFloat(M_10[i])+parseFloat(M_11[i])+parseFloat(M_12[i]))+" ";
                                    break;
                                default: alert("เกิดข้อผิดพลาดกรุณาแจ้งแผนก IT");
                                    break;
                            }
                        }
                        SumTeam = SumTeam.split(" ");
                    // END ยอดรวมแต่ละทีม

                    // ยอดรวมแต่ละเดือน (รวมทุกทีม)
                        var sumM_1=0, sumM_2=0, sumM_3=0, sumM_4=0, sumM_5=0, sumM_6=0, sumM_7=0, sumM_8=0, sumM_9=0, sumM_10=0, sumM_11=0, sumM_12=0;
                        for (var i = 0; i < TEAM.length-1; i++) {
                            sumM_1 = sumM_1+parseFloat(M_1[i]);
                            sumM_2 = sumM_2+parseFloat(M_2[i]);
                            sumM_3 = sumM_3+parseFloat(M_3[i]);
                            sumM_4 = sumM_4+parseFloat(M_4[i]);
                            sumM_5 = sumM_5+parseFloat(M_5[i]);
                            sumM_6 = sumM_6+parseFloat(M_6[i]);
                            sumM_7 = sumM_7+parseFloat(M_7[i]);
                            sumM_8 = sumM_8+parseFloat(M_8[i]);
                            sumM_9 = sumM_9+parseFloat(M_9[i]);
                            sumM_10 = sumM_10+parseFloat(M_10[i]);
                            sumM_11 = sumM_11+parseFloat(M_11[i]);
                            sumM_12 = sumM_12+parseFloat(M_12[i]);
                        }
                    // END ยอดรวมแต่ละเดือน (รวมทุกทีม)

                    // ยอดขายปีที่แล้ว
                        var YearPrev =  inval["YearPrev"];
                        var SM_1 =  inval["SM_1"];
                        var SM_2 =  inval["SM_2"];
                        var SM_3 =  inval["SM_3"];
                        var SM_4 =  inval["SM_4"];
                        var SM_5 =  inval["SM_5"];
                        var SM_6 =  inval["SM_6"];
                        var SM_7 =  inval["SM_7"];
                        var SM_8 =  inval["SM_8"];
                        var SM_9 =  inval["SM_9"];
                        var SM_10 =  inval["SM_10"];
                        var SM_11 =  inval["SM_11"];
                        var SM_12 =  inval["SM_12"];
                        var sumSM = [SM_1,SM_2,SM_3,SM_4,SM_5,SM_6,SM_7,SM_8,SM_9,SM_10,SM_11,SM_12];
                        // Format Display ยอดขายปีที่แล้ว
                        if( YearSelect == new Date().getFullYear()) {
                            var CurrentSM_M = (new Date().getMonth()+1);
                        }else{
                            var CurrentSM_M = 12;
                        }  
                        var CurrentSM = ""; 
                        for (var i = 0; i < 12; i++) {
                            if (i < CurrentSM_M) {
                                CurrentSM += sumSM[i]+" ";
                            }else{
                                CurrentSM += "0.00"+" ";
                            }
                        }
                        var DataSM = CurrentSM.split(" ");
                    // END ยอดขายปีที่แล้ว
                    
                    // % การเติมโต
                        var PM_1 = parseFloat((((sumM_1 - SM_1)/(SM_1))*100).toFixed(2));
                        var PM_2 = parseFloat((((sumM_2 - SM_2)/(SM_2))*100).toFixed(2));
                        var PM_3 = parseFloat((((sumM_3 - SM_3)/(SM_3))*100).toFixed(2));
                        var PM_4 = parseFloat((((sumM_4 - SM_4)/(SM_4))*100).toFixed(2));
                        var PM_5 = parseFloat((((sumM_5 - SM_5)/(SM_5))*100).toFixed(2));
                        var PM_6 = parseFloat((((sumM_6 - SM_6)/(SM_6))*100).toFixed(2));
                        var PM_7 = parseFloat((((sumM_7 - SM_7)/(SM_7))*100).toFixed(2));
                        var PM_8 = parseFloat((((sumM_8 - SM_8)/(SM_8))*100).toFixed(2));
                        var PM_9 = parseFloat((((sumM_9 - SM_9)/(SM_9))*100).toFixed(2));
                        var PM_10 = parseFloat((((sumM_10 - SM_10)/(SM_10))*100).toFixed(2));
                        var PM_11 = parseFloat((((sumM_11 - SM_11)/(SM_11))*100).toFixed(2));
                        var PM_12 = parseFloat((((sumM_12 - SM_12)/(SM_12))*100).toFixed(2));
                        var sumPM = [PM_1,PM_2,PM_3,PM_4,PM_5,PM_6,PM_7,PM_8,PM_9,PM_10,PM_11,PM_12];

                        // Format Display % การเติมโต
                        if( YearSelect == new Date().getFullYear()) {
                            var CurrentM = (new Date().getMonth()+1);
                        }else{
                            var CurrentM = 12;
                        }    
                        var CurrentPM = "";
                        for (var i = 0; i < 12; i++) {
                            if (i < CurrentM) {
                                if (isNaN(sumPM[i])) {
                                    CurrentPM += "0.00"+" ";
                                }else{
                                    switch (sumPM[i]) {
                                        case Infinity: CurrentPM += "100"+" "; break;
                                        case -Infinity: CurrentPM += "-100"+" "; break;
                                        default: CurrentPM += sumPM[i]+" "; break;
                                    }
                                }
                            }else{
                                CurrentPM += "0.00"+" ";
                            }
                        }
                        var DataPM = CurrentPM.split(" ");
                    // END % การเติมโต

                    // Max in charts
                        var maxAll = 0, maxAllM = 0, maxAllSM = 0;;
                        for(var i = 0; i < 12; i++){
                            var sumFullM = [sumM_1,sumM_2,sumM_3,sumM_4,sumM_5,sumM_6,sumM_7,sumM_8,sumM_9,sumM_10,sumM_11,sumM_12];
                            if (maxAllM < sumFullM[i]){
                                maxAllM = sumFullM[i];
                            }
                            var sumFullSM = [SM_1,SM_2,SM_3,SM_4,SM_5,SM_6,SM_7,SM_8,SM_9,SM_10,SM_11,SM_12];
                            if (maxAllSM < sumFullSM[i]){
                                maxAllSM = sumFullSM[i];
                            }
                        }
                        if (maxAllM < maxAllSM) {
                            maxAll = maxAllSM;
                        }else{
                            maxAll = maxAllM;
                        }
                    // END Max in charts

                    // DATA (DataTbody)
                        // Tbody Table
                        var DataTbody = "";
                        // ทีมขาย
                        for (var i = 0; i < TEAM.length-1; i++) {
                            // TextColor
                            if(parseFloat(M_1[i]) < 0) {var TextColor1 = "text-primary";}else{var TextColor1 = "";}
                            if(parseFloat(M_2[i]) < 0) {var TextColor2 = "text-primary";}else{var TextColor2 = "";}
                            if(parseFloat(M_3[i]) < 0) {var TextColor3 = "text-primary";}else{var TextColor3 = "";}
                            if(parseFloat(M_4[i]) < 0) {var TextColor4 = "text-primary";}else{var TextColor4 = "";}
                            if(parseFloat(M_5[i]) < 0) {var TextColor5 = "text-primary";}else{var TextColor5 = "";}
                            if(parseFloat(M_6[i]) < 0) {var TextColor6 = "text-primary";}else{var TextColor6 = "";}
                            if(parseFloat(M_7[i]) < 0) {var TextColor7 = "text-primary";}else{var TextColor7 = "";}
                            if(parseFloat(M_8[i]) < 0) {var TextColor8 = "text-primary";}else{var TextColor8 = "";}
                            if(parseFloat(M_9[i]) < 0) {var TextColor9 = "text-primary";}else{var TextColor9 = "";}
                            if(parseFloat(M_10[i]) < 0) {var TextColor10 = "text-primary";}else{var TextColor10 = "";}
                            if(parseFloat(M_11[i]) < 0) {var TextColor11 = "text-primary";}else{var TextColor11 = "";}
                            if(parseFloat(M_12[i]) < 0) {var TextColor12 = "text-primary";}else{var TextColor12 = "";}
                            if(parseFloat(SumTeam[i]) < 0) {var TextColorST = "text-primary";}else{var TextColorST = "";}

                            // OutPutTable
                            DataTbody +="<tr class='text-right'>"+
                                            "<td class='text-start text-primary fw-bolder'><a href='javascript:void(0);' class='btn-group' data-team='"+TeamSelect+"' data-group='"+TEAM[i]+"' data-year='"+YearSelect+"'>"+TEAM[i]+"</a></td>"+
                                            "<td class='"+TextColor1+"'>"+number_format(M_1[i],2)+"</td>"+
                                            "<td class='"+TextColor2+"'>"+number_format(M_2[i],2)+"</td>"+
                                            "<td class='"+TextColor3+"'>"+number_format(M_3[i],2)+"</td>"+
                                            "<td class='"+TextColor4+"'>"+number_format(M_4[i],2)+"</td>"+
                                            "<td class='"+TextColor5+"'>"+number_format(M_5[i],2)+"</td>"+
                                            "<td class='"+TextColor6+"'>"+number_format(M_6[i],2)+"</td>"+
                                            "<td class='"+TextColor7+"'>"+number_format(M_7[i],2)+"</td>"+
                                            "<td class='"+TextColor8+"'>"+number_format(M_8[i],2)+"</td>"+
                                            "<td class='"+TextColor9+"'>"+number_format(M_9[i],2)+"</td>"+
                                            "<td class='"+TextColor10+"'>"+number_format(M_10[i],2)+"</td>"+
                                            "<td class='"+TextColor11+"'>"+number_format(M_11[i],2)+"</td>"+
                                            "<td class='"+TextColor12+"'>"+number_format(M_12[i],2)+"</td>"+
                                            "<td class='fw-bolder "+TextColorST+"'>"+number_format(SumTeam[i],2)+"</td>"+
                                        "</tr>";
                        }
                        // รวมทุกทีม
                        var SumDataM = (sumM_1+sumM_2+sumM_3+sumM_4+sumM_5+sumM_6+sumM_7+sumM_8+sumM_9+sumM_10+sumM_11+sumM_12);
                        DataTbody +="<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>"+
                                        "<td class='fw-bolder text-start fw-bolder'>รวมทุกทีม</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_1,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_2,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_3,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_4,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_5,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_6,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_7,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_8,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_9,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_10,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_11,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(sumM_12,2)+"</td>"+
                                        "<td class='fw-bolder text-green'>"+number_format(SumDataM,2)+"</td>"+
                                    "</tr>";
                        
                        // ถ้าไม่ใช่ปี 2015
                        if(YearSelect != "2015") {
                            // ยอดขายปีเก่า
                            var SumDataSM = 0;
                            for (var i = 0; i < 12; i++) { SumDataSM = SumDataSM+parseFloat(DataSM[i]); }
                            DataTbody +="<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>"+
                                            "<td class='text-start fw-bolder'>ยอดขายปี "+YearPrev+"</td>"+
                                            "<td>"+number_format(DataSM[0],2)+"</td>"+
                                            "<td>"+number_format(DataSM[1],2)+"</td>"+
                                            "<td>"+number_format(DataSM[2],2)+"</td>"+
                                            "<td>"+number_format(DataSM[3],2)+"</td>"+
                                            "<td>"+number_format(DataSM[4],2)+"</td>"+
                                            "<td>"+number_format(DataSM[5],2)+"</td>"+
                                            "<td>"+number_format(DataSM[6],2)+"</td>"+
                                            "<td>"+number_format(DataSM[7],2)+"</td>"+
                                            "<td>"+number_format(DataSM[8],2)+"</td>"+
                                            "<td>"+number_format(DataSM[9],2)+"</td>"+
                                            "<td>"+number_format(DataSM[10],2)+"</td>"+
                                            "<td>"+number_format(DataSM[11],2)+"</td>"+
                                            "<td class='fw-bolder'>"+number_format(SumDataSM,2)+"</td>"+
                                        "</tr>";
                            // % การเติบโต
                            if (isNaN(parseFloat((((SumDataM - SumDataSM)/(SumDataSM))*100).toFixed(2)))) {
                                var SumDataPM = "0.00"+" ";
                            }else{
                                switch (parseFloat((((SumDataM - SumDataSM)/(SumDataSM))*100).toFixed(2))) {
                                    case Infinity: var SumDataPM = "100"+" "; break;
                                    case -Infinity: var SumDataPM = "-100"+" "; break;
                                    default: var SumDataPM = parseFloat((((SumDataM - SumDataSM)/(SumDataSM))*100).toFixed(2)); break;
                                }
                            }
                            if (SumDataPM) {
                                if(parseFloat(DataPM[0]) < 0) {var TextColorPM0 = "text-primary";}else{var TextColorPM0 = "text-green";}
                                if(parseFloat(DataPM[1]) < 0) {var TextColorPM1 = "text-primary";}else{var TextColorPM1 = "text-green";}
                                if(parseFloat(DataPM[2]) < 0) {var TextColorPM2 = "text-primary";}else{var TextColorPM2 = "text-green";}
                                if(parseFloat(DataPM[3]) < 0) {var TextColorPM3 = "text-primary";}else{var TextColorPM3 = "text-green";}
                                if(parseFloat(DataPM[4]) < 0) {var TextColorPM4 = "text-primary";}else{var TextColorPM4 = "text-green";}
                                if(parseFloat(DataPM[5]) < 0) {var TextColorPM5 = "text-primary";}else{var TextColorPM5 = "text-green";}
                                if(parseFloat(DataPM[6]) < 0) {var TextColorPM6 = "text-primary";}else{var TextColorPM6 = "text-green";}
                                if(parseFloat(DataPM[7]) < 0) {var TextColorPM7 = "text-primary";}else{var TextColorPM7 = "text-green";}
                                if(parseFloat(DataPM[8]) < 0) {var TextColorPM8 = "text-primary";}else{var TextColorPM8 = "text-green";}
                                if(parseFloat(DataPM[9]) < 0) {var TextColorPM9 = "text-primary";}else{var TextColorPM9 = "text-green";}
                                if(parseFloat(DataPM[10]) < 0) {var TextColorPM10 = "text-primary";}else{var TextColorPM10 = "text-green";}
                                if(parseFloat(DataPM[11]) < 0) {var TextColorPM11 = "text-primary";}else{var TextColorPM11 = "text-green";}
                                if(SumDataPM < 0) {var TextColorSPM = "text-primary";}else{var TextColorSPM = "text-green";}
                                DataTbody +="<tr class='text-right' style='background-color: rgba(0, 0, 0, 0.04);'>"+
                                                "<td class='text-start fw-bolder'>% การเติบโต</td>"+
                                                "<td class='fw-bolder "+TextColorPM0+"'>"+number_format(DataPM[0],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM1+"'>"+number_format(DataPM[1],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM2+"'>"+number_format(DataPM[2],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM3+"'>"+number_format(DataPM[3],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM4+"'>"+number_format(DataPM[4],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM5+"'>"+number_format(DataPM[5],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM6+"'>"+number_format(DataPM[6],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM7+"'>"+number_format(DataPM[7],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM8+"'>"+number_format(DataPM[8],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM9+"'>"+number_format(DataPM[9],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM10+"'>"+number_format(DataPM[10],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorPM11+"'>"+number_format(DataPM[11],2)+"%</td>"+
                                                "<td class='fw-bolder "+TextColorSPM+"'>"+number_format(SumDataPM,2)+"%</td>"+
                                            "</tr>";
                            }
                        }else{
                            YearPrev = "ไม่มียอดปีเก่า";
                        }
                    // END DATA

                    // Data for show
                        var vaTeam = TEAM.length-1;
                        switch (TeamSelect) {
                            case "all":
                                var TheadAll = "<tr>"+
                                                "<th colspan='14' class='text-primary text-center'>ยอดขายปี "+YearSelect+"</th>"+
                                            "</tr>"+
                                            "<tr>"+
                                                "<th class='text-center' width='15%'>ทีมขาย</th>"+
                                                "<th class='text-center' width='6.4%'>มกราคม</th>"+
                                                "<th class='text-center' width='6.4%'>กุมภาพันธ์</th>"+
                                                "<th class='text-center' width='6.4%'>มีนาคม</th>"+
                                                "<th class='text-center' width='6.4%'>เมษายน</th>"+
                                                "<th class='text-center' width='6.4%'>พฤษภาคม</th>"+
                                                "<th class='text-center' width='6.4%'>มิถุนายน</th>"+
                                                "<th class='text-center' width='6.4%'>กรกฎาคม</th>"+
                                                "<th class='text-center' width='6.4%'>สิงหาคม</th>"+
                                                "<th class='text-center' width='6.4%'>กันยายน</th>"+
                                                "<th class='text-center' width='6.4%'>ตุลาคม</th>"+
                                                "<th class='text-center' width='6.4%'>พฤศจิกายน</th>"+
                                                "<th class='text-center' width='6.4%'>ธันวาคม</th>"+
                                                "<th class='text-center' width='8.2%'>รวมทั้งหมด</th>"+
                                            "</tr>";
                                // $("#HAll").html("ยอดขายปี "+YearSelect);
                                // Charts 
                                switch (vaTeam) {
                                    <?php $case = 1; $data = 0; ?>
                                    <?php for($i = 1; $i <= 11; $i++) { ?>
                                    case <?php echo $i; ?>:
                                        chartAll.updateSeries([
                                            <?php for($t = 0; $t <= $data; $t++) { ?>
                                            {
                                            name: TEAM[<?php echo $t; ?>], type: 'bar',
                                            data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                    M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                    M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                            }, <?php } ?>
                                            {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartAll.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [<?php for($w = 1; $w <= $i; $w++) { echo 0; echo ",";} ?>3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect,
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                        chartAll.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartAll.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0,3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect,
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                }
                                $('#AllReportExcel').DataTable().destroy();
                                $('#DataTheadAll, #DataTbodyAll').empty();
                                $("#DataTheadAll").html(TheadAll);
                                $("#DataTbodyAll").html(DataTbody);
                                break;
                            case "MT1":
                                $("#HMT1").html("ยอดขายปี "+YearSelect+" ทีม "+TeamSelect);
                                // Charts 
                                switch (vaTeam) {
                                    <?php $case = 1; $data = 0; ?>
                                    <?php for($i = 1; $i <= 7; $i++) { ?>
                                    case <?php echo $i; ?>:
                                        chartMT1.updateSeries([
                                            <?php for($t = 0; $t <= $data; $t++) { ?>
                                            {
                                            name: TEAM[<?php echo $t; ?>], type: 'bar',
                                            data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                    M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                    M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                            }, <?php } ?>
                                            {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartMT1.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [<?php for($w = 1; $w <= $i; $w++) { echo 0; echo ",";} ?>3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม '+TeamSelect,
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                        chartMT1.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartMT1.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0,3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม '+TeamSelect,
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                }
                                $('#ReportMT1Excel').DataTable().destroy();
                                $('#DataTbodyMT1').empty();
                                $("#DataTbodyMT1").html(DataTbody);
                                break;
                            case "MT2":
                                $("#HMT2").html("ยอดขายปี "+YearSelect+" ทีม "+TeamSelect);
                                // Charts chartMT2 text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม '+TeamSelect,
                                switch (vaTeam) {
                                    <?php $case = 1; $data = 0; ?>
                                    <?php for($i = 1; $i <= 8; $i++) { ?>
                                    case <?php echo $i; ?>:
                                        chartMT2.updateSeries([
                                            <?php for($t = 0; $t <= $data; $t++) { ?>
                                            {
                                            name: TEAM[<?php echo $t; ?>], type: 'bar',
                                            data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                    M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                    M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                            }, <?php } ?>
                                            {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartMT2.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [<?php for($w = 1; $w <= $i; $w++) { echo 0; echo ",";} ?>3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม '+TeamSelect,
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                        chartMT2.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartMT2.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0,3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม '+TeamSelect,
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                }
                                $('#ReportMT2Excel').DataTable().destroy();
                                $('#DataTbodyMT2').empty();
                                $("#DataTbodyMT2").html(DataTbody);
                                break;
                            case "TT1":
                                $("#HTT1").html("ยอดขายปี "+YearSelect+" ทีม TT กทม.");
                                // Charts chartTT1 text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม TT กทม.',
                                switch (vaTeam) {
                                    <?php $case = 1; $data = 0; ?>
                                    <?php for($i = 1; $i <= 8; $i++) { ?>
                                    case <?php echo $i; ?>:
                                        chartTT1.updateSeries([
                                            <?php for($t = 0; $t <= $data; $t++) { ?>
                                            {
                                            name: TEAM[<?php echo $t; ?>], type: 'bar',
                                            data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                    M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                    M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                            }, <?php } ?>
                                            {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartTT1.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [<?php for($w = 1; $w <= $i; $w++) { echo 0; echo ",";} ?>3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม TT กทม.',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                        chartTT1.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartTT1.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0,3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม TT กทม.',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                }
                                $('#ReportTT1Excel').DataTable().destroy();
                                $('#DataTbodyTT1').empty();
                                $("#DataTbodyTT1").html(DataTbody);
                                break;
                            case "TT2":
                                $("#HTT2").html("ยอดขายปี "+YearSelect+" ทีม TT ตจว.");
                                // Charts chartTT2 text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม TT ตจว.',
                                switch (vaTeam) {
                                    <?php $case = 1; $data = 0; ?>
                                    <?php for($i = 1; $i <= 8; $i++) { ?>
                                    case <?php echo $i; ?>:
                                        chartTT2.updateSeries([
                                            <?php for($t = 0; $t <= $data; $t++) { ?>
                                            {
                                            name: TEAM[<?php echo $t; ?>], type: 'bar',
                                            data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                    M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                    M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                            }, <?php } ?>
                                            {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartTT2.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [<?php for($w = 1; $w <= $i; $w++) { echo 0; echo ",";} ?>3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม TT ตจว.',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                        chartTT2.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartTT2.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0,3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม TT ตจว.',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                }
                                $('#ReportTT2Excel').DataTable().destroy();
                                $('#DataTbodyTT2').empty();
                                $("#DataTbodyTT2").html(DataTbody);
                                break;
                            case "OUL":
                                $("#HOUL").html("ยอดขายปี "+YearSelect+" ทีม หน้าร้าน");
                                // Charts
                                switch (vaTeam) {
                                    <?php $case = 1; $data = 0; ?>
                                    <?php for($i = 1; $i <= 9; $i++) { ?>
                                    case <?php echo $i; ?>:
                                        chartOUL.updateSeries([
                                            <?php for($t = 0; $t <= $data; $t++) { ?>
                                            {
                                            name: TEAM[<?php echo $t; ?>], type: 'bar',
                                            data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                    M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                    M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                            }, <?php } ?>
                                            {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartOUL.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [<?php for($w = 1; $w <= $i; $w++) { echo 0; echo ",";} ?>3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม หน้าร้าน',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                        chartOUL.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartOUL.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0,3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม หน้าร้าน',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                }
                                $('#ReportOULExcel').DataTable().destroy();
                                $('#DataTbodyOUL').empty();
                                $("#DataTbodyOUL").html(DataTbody);
                                break;
                            case "ONL":
                                $("#HONL").html("ยอดขายปี "+YearSelect+" ทีม ออนไลน์");
                                // Charts
                                switch (vaTeam) {
                                    <?php $case = 1; $data = 0; ?>
                                    <?php for($i = 1; $i <= 8; $i++) { ?>
                                    case <?php echo $i; ?>:
                                        chartONL.updateSeries([
                                            <?php for($t = 0; $t <= $data; $t++) { ?>
                                            {
                                            name: TEAM[<?php echo $t; ?>], type: 'bar',
                                            data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                    M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                    M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                            }, <?php } ?>
                                            {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartONL.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [<?php for($w = 1; $w <= $i; $w++) { echo 0; echo ",";} ?>3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม ออนไลน์',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                        chartONL.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartOUL.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0,3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม ออนไลน์',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                }
                                $('#ReportONLExcel').DataTable().destroy();
                                $('#DataTbodyONL').empty();
                                $("#DataTbodyONL").html(DataTbody);
                                break;
                            case "KBI":
                                $("#HKBI").html("ยอดขายปี "+YearSelect+" ทีม ส่วนกลาง");
                                // Charts
                                switch (vaTeam) {
                                    <?php $case = 1; $data = 0; ?>
                                    <?php for($i = 1; $i <= 9; $i++) { ?>
                                    case <?php echo $i; ?>:
                                        chartKBI.updateSeries([
                                            <?php for($t = 0; $t <= $data; $t++) { ?>
                                            {
                                            name: TEAM[<?php echo $t; ?>], type: 'bar',
                                            data: [ M_1[<?php echo $t; ?>], M_2[<?php echo $t; ?>], M_3[<?php echo $t; ?>], M_4[<?php echo $t; ?>], M_5[<?php echo $t; ?>], 
                                                    M_6[<?php echo $t; ?>], M_7[<?php echo $t; ?>], M_8[<?php echo $t; ?>], M_9[<?php echo $t; ?>], M_10[<?php echo $t; ?>], 
                                                    M_11[<?php echo $t; ?>], M_12[<?php echo $t; ?>]]
                                            }, <?php } ?>
                                            {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartKBI.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [<?php for($w = 1; $w <= $i; $w++) { echo 0; echo ",";} ?>3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม ส่วนกลาง',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                        chartKBI.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartKBI.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0,3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม ส่วนกลาง',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                }
                                $('#ReportKBIExcel').DataTable().destroy();
                                $('#DataTbodyKBI').empty();
                                $("#DataTbodyKBI").html(DataTbody);
                                break;
                            case "EXP":
                                $("#HEXP").html("ยอดขายปี "+YearSelect+" ทีม ต่างประเทศ");
                                // Charts
                                switch (vaTeam) {
                                    case 1:
                                        chartEXP.updateSeries([{
                                            name: TEAM[0], type: 'bar',
                                            data: [M_1[0], M_2[0], M_3[0], M_4[0], M_5[0], M_6[0], M_7[0], M_8[0], M_9[0], M_10[0], M_11[0], M_12[0]]
                                            }, {
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [SM_1, SM_2, SM_3, SM_4, SM_5, SM_6, SM_7, SM_8, SM_9, SM_10, SM_11, SM_12]
                                        }])
                                        chartEXP.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0, 3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม ต่างประเทศ',
                                            },
                                            yaxis: {
                                                max: parseFloat(maxAll.toFixed(2)),
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
                                    default:
                                        chartEXP.updateSeries([{
                                            name: "ยังไม่มียอดขาย", type: 'bar',
                                            data: []
                                            },{
                                            name: 'ยอดขายปี'+' '+YearPrev, type: 'line',
                                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                                        }])
                                        chartEXP.updateOptions({
                                            stroke: {
                                                curve: 'straight',
                                                width: [0, 3],
                                                colors: ['rgba(48, 34, 36, 0.31)']
                                            },
                                            title: {
                                                text: 'ข้อมูลการขายปี'+' '+YearSelect+' : ทีม ต่างประเทศ',
                                            },
                                            yaxis: {
                                                max: 500000,
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
                                }
                                $('#ReportEXPExcel').DataTable().destroy();
                                $('#DataTbodyEXP').empty();
                                $("#DataTbodyEXP").html(DataTbody);
                                break;
                            default: alert("เกิดข้อผิดพลาด 'switch (Team)' กรุณาแจ้งแผนก IT"); break;
                        }
                    // END TEAM Show Charts and Table
                    
                    Export();
                });

                $(".btn-group").on("click", function(e){
                    e.preventDefault();
                    var DataTeam = $(this).attr("data-team");
                    var DataGroup = $(this).attr("data-group");
                    // var DataYear = $(this).attr("data-year");
                    if (DataTeam == "all") {
                        switch (DataGroup) {
                            case "ฝ่ายขายห้างสรรพสินค้า 1": $("#IDMT1").click(); break;
                            case "ฝ่ายขายห้างสรรพสินค้า 2": $("#IDMT2").click(); break;
                            case "ฝ่ายขายร้านค้ากรุงเทพฯ": $("#IDTT1").click(); break;
                            case "ฝ่ายขายร้านค้าต่างจังหวัด": $("#IDTT2").click(); break;
                            case "ฝ่ายขายหน้าร้าน": $("#IDOUL").click(); break;
                            case "ฝ่ายขายออนไลน์": $("#IDONL").click(); break;
                            case "ส่วนกลาง": $("#IDKBI").click(); break;
                            case "ฝ่ายขายต่างประเทศ": $("#IDEXP").click(); break;
                            default: break;
                        }
                    }else{
                       $.ajax({
                            url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=SelectGroup",
                            type: "POST",
                            data: { Team : DataTeam, Group : DataGroup, Year : $("#YearAll").val(),},
                            success: function(result) {
                                var obj = jQuery.parseJSON(result);
                                $.each(obj,function(key,inval) {
                                    var TeamSelect =  inval["TeamSelect"];
                                    var YearCurrent =  inval["YearCurrent"];
                                    var GroupSelect =  inval["GroupSelect"];
                                    var YearPrev =  inval["YearPrev"];
                                    var ModalThead ="<tr>"+"<th colspan='11' width='100%' class='text-primary text-center'>ยอดขายปี "+YearCurrent+" ทีม "+TeamSelect+" กลุ่ม "+GroupSelect+"</th>"+"</tr>"+
                                                    "<tr>"+
                                                        "<th rowspan='2' width='3%' class='text-center align-bottom'>No.</th>"+
                                                        "<th rowspan='2' width='17%' class='text-center align-bottom border-start'>ชื่อลูกค้า</th>"+
                                                        "<th rowspan='2' width='15%' class='text-center align-bottom border-start'>ผู้แทนขาย</th>"+
                                                        "<th rowspan='2' width='10%' class='text-center align-bottom border-start'>ยอดขายปี "+YearPrev+"<br>(ม.ค. - <?php echo txtMonth(date("m")) ?>)</th>"+
                                                        "<th rowspan='2' width='10%' class='text-center align-bottom border-start'>ยอดขายปี "+YearCurrent+"<br>(ม.ค. - <?php echo txtMonth(date("m")) ?>)</th>"+
                                                        "<th rowspan='2' width='10%' class='text-center align-bottom border-start'>% การเติบโต<br>("+YearPrev+" - "+YearCurrent+")</th>"+
                                                        "<th rowspan='2' width='10%' class='text-center align-bottom border-start'>กำไรปี "+YearCurrent+"</th>"+
                                                        "<th colspan='4' width='25%' class='text-center align-bottom border-start'>ยอดขายปี "+YearCurrent+"</th>"+
                                                    "</tr>"+
                                                    "<tr>"+
                                                        "<th width='' class='text-center border-start'>ไตรมาส 1</th>"+
                                                        "<th width='' class='text-center border-start'>ไตรมาส 2</th>"+
                                                        "<th width=''class='text-center border-start'>ไตรมาส 3</th>"+
                                                        "<th width='' class='text-center border-start'>ไตรมาส 4</th>"+
                                                    "</tr>";
                                    $("#ModalHeader").html("<i class='fas fa-users fa-fw fa-1x'></i>&nbsp;&nbsp;ยอดขายทีม "+TeamSelect+" กลุ่ม "+GroupSelect+"");
                                    $('#ModalDataExport').DataTable().destroy();
                                    $('#ModalThead, #ModalTbody, #ModalTfoot').empty();
                                    $("#ModalThead").html(ModalThead);
                                    $("#ModalTbody").html(inval["ModalTbody"]);
                                    $("#ModalTfoot").html(inval["ModalTfoot"]);
                                    ModalExport();
                                    $("#ModalViewData").modal("show"); 
                                });
                            }
                        });
                    }
                })
            }
        })
        $(".overlay").hide();
    }

    // Rander Charts
    var chartAll = new ApexCharts(document.querySelector("#AllReport"), DataOption); chartAll.render();
    var chartMT1 = new ApexCharts(document.querySelector("#ReportMT1"), DataOption); chartMT1.render();
    var chartMT2 = new ApexCharts(document.querySelector("#ReportMT2"), DataOption); chartMT2.render();
    var chartTT1 = new ApexCharts(document.querySelector("#ReportTT1"), DataOption); chartTT1.render();
    var chartTT2 = new ApexCharts(document.querySelector("#ReportTT2"), DataOption); chartTT2.render();
    var chartOUL = new ApexCharts(document.querySelector("#ReportOUL"), DataOption); chartOUL.render();
    var chartONL = new ApexCharts(document.querySelector("#ReportONL"), DataOption); chartONL.render();
    var chartKBI = new ApexCharts(document.querySelector("#ReportKBI"), DataOption); chartKBI.render();
    var chartEXP = new ApexCharts(document.querySelector("#ReportEXP"), DataOption); chartEXP.render();
</script>
<!-- END รายงานการขาย -->
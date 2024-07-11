<!-- รายงานการเก็บเงิน -->
<script>
    $("#btn-CMall, #btn-CMMT1, #btn-CMMT2, #btn-CMTT1, #btn-CMTT2, #btn-CMOUL, #btn-CMONL").on("click", function() { 
        var DataTeam = $(this).attr("data-tab");
        $.ajax({
            url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=CollectingMoney",
            type: "POST",
            data: { Team : DataTeam,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    if(inval['Team'] == 'all') {
                        var Thead ="<tr class='text-light fw-bold' style='background-color: rgba(136, 0, 0, 0.80);'>"+
                                        "<td width='5%' class='text-center'>ทีม</td>"+
                                        "<td class='text-center'>รายละเอียด</td>"+
                                        "<td class='text-center'>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน "+inval['Month']+" "+inval['Year']+")</td>"+
                                    "</tr>";
                        $('#CMallExcel').DataTable().destroy();
                        $("#TheadCMall").html(Thead);
                        $("#TbodyCMall").html(inval['Tbody']);
                        Export();
                    }else{
                        var Thead =
                                    // "<tr>"+
                                    //     "<th class='text-primary text-center' colspan='3'>ทีม "+inval['TeamName']+"</th>"+
                                    // "</tr>"+
                                    "<tr class='text-light fw-bold' style='background-color: rgba(136, 0, 0, 0.80);'>"+
                                        "<td width='15%' class='text-center'>ชื่อ (ทีม "+inval['TeamName']+")</td>"+
                                        "<td class='text-center'>รายละเอียด</td>"+
                                        "<td class='text-center'>ยอดหนี้เกินกำหนดชำระ (ยอดกำหนดชำระ เดือน "+inval['Month']+" "+inval['Year']+")</td>"+
                                    "</tr>";
                        switch (inval['Team']) {
                            case 'MT1':
                                $('#CMMT1Excel').DataTable().destroy();
                                $("#TheadCMMT1").html(Thead);
                                $("#TbodyCMMT1").html(inval['Tbody']);
                                Export();
                                break;
                            case 'MT2':
                                $('#CMMT2Excel').DataTable().destroy();
                                $("#TheadCMMT2").html(Thead);
                                $("#TbodyCMMT2").html(inval['Tbody']);
                                Export();
                                break;
                            case 'TT1':
                                $('#CMTT1Excel').DataTable().destroy();
                                $("#TheadCMTT1").html(Thead);
                                $("#TbodyCMTT1").html(inval['Tbody']);
                                Export();
                                break;
                            case 'TT2':
                                $('#CMTT2Excel').DataTable().destroy();
                                $("#TheadCMTT2").html(Thead);
                                $("#TbodyCMTT2").html(inval['Tbody']);
                                Export();
                                break;
                            case 'OUL':
                                $('#CMOULExcel').DataTable().destroy();
                                $("#TheadCMOUL").html(Thead);
                                $("#TbodyCMOUL").html(inval['Tbody']);
                                Export();
                                break;
                            case 'ONL':
                                $('#CMONLExcel').DataTable().destroy();
                                $("#TheadCMONL").html(Thead);
                                $("#TbodyCMONL").html(inval['Tbody']);
                                Export();
                                break;
                            default: break;
                        }
                        
                    }
                })
            } 
        })
    })
</script>
<!-- END รายงานการเก็บเงิน -->
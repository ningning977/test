<!-- รายงานคลังสินค้า -->
<script>
    function Warehouse() {
        $.ajax({
            url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=Warehouse",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    var Data = inval['Data'].split('|');
                    if(Data[0] != null) { $("#invnt_WG1").html(Data[0]+" ล้านบาท"); } else { $("#invnt_WG1").html("0.00 ล้านบาท"); }
                    if(Data[2] != null) { $("#invnt_WG2").html(Data[2]+" ล้านบาท"); } else { $("#invnt_WG2").html("0.00 ล้านบาท"); }
                    if(Data[4] != null) { $("#invnt_WG3").html(Data[4]+" ล้านบาท"); } else { $("#invnt_WG3").html("0.00 ล้านบาท"); }
                    if(Data[6] != null) { $("#invnt_WG4").html(Data[6]+" ล้านบาท"); } else { $("#invnt_WG4").html("0.00 ล้านบาท"); }
                    if(Data[8] != null) { $("#invnt_WG5").html(Data[8]+" ล้านบาท"); } else { $("#invnt_WG5").html("0.00 ล้านบาท"); }
                    if(Data[10] != null) { $("#invnt_WG6").html(Data[10]+" ล้านบาท"); } else { $("#invnt_WG6").html("0.00 ล้านบาท"); }
                    if(Data[12] != null) { $("#invnt_WG7").html(Data[12]+" ล้านบาท"); } else { $("#invnt_WG7").html("0.00 ล้านบาท"); }
                    if(Data[14] != null) { $("#invnt_WG8").html(Data[14]+" ล้านบาท"); } else { $("#invnt_WG8").html("0.00 ล้านบาท"); }
                    if(Data[16] != null) { $("#invnt_WG9").html(Data[16]+" ล้านบาท"); } else { $("#invnt_WG9").html("0.00 ล้านบาท"); }
                    // console.log(Data);
                    if(Data[18] != null) { $("#invnt_WG10").html(Data[18]+" ล้านบาท"); } else { $("#invnt_WG10").html("0.00 ล้านบาท"); }
                    if(Data[20] != null) { $("#invnt_WG11").html(Data[20]+" ล้านบาท"); } else { $("#invnt_WG11").html("0.00 ล้านบาท"); }
                    if(Data[22] != null) { $("#invnt_WG12").html(Data[22]+" ล้านบาท"); } else { $("#invnt_WG12").html("0.00 ล้านบาท"); }
                    var G1 = parseFloat(Data[1])+parseFloat(Data[3]);
                    var G2 = parseFloat(Data[5])+parseFloat(Data[7])+parseFloat(Data[9])+parseFloat(Data[11])+parseFloat(Data[13]);
                    var G3 = parseFloat(Data[15])+parseFloat(Data[17]);
                    var G4 = parseFloat(Data[19])+parseFloat(Data[21]);
                    var G5 = parseFloat(Data[23]);
                    var G0 = parseFloat(G1+G2+G3+G4+G5);
                    var G0N = parseFloat(G0/1.07);                    
                    var G1N = parseFloat(G1/1.07);                    
                    var G2N = parseFloat(G2/1.07);                    
                    var G3N = parseFloat(G3/1.07);                    
                    var G4N = parseFloat(G4/1.07);                    
                    var G5N = parseFloat(G5/1.07); 
                    $("#G0").html("รวม VAT : "+number_format(G0,2)+" บาท");
                    $("#G1").html("รวม VAT : "+number_format(G1,2)+" บาท");
                    $("#G2").html("รวม VAT : "+number_format(G2,2)+" บาท");
                    $("#G3").html("รวม VAT : "+number_format(G3,2)+" บาท");
                    $("#G4").html("รวม VAT : "+number_format(G4,2)+" บาท");
                    $("#G5").html("รวม VAT : "+number_format(G5,2)+" บาท");
                    $("#G0N").html("ไม่รวม VAT : "+number_format(G0N,2)+" บาท");
                    $("#G1N").html("ไม่รวม VAT : "+number_format(G1N,2)+" บาท");
                    $("#G2N").html("ไม่รวม VAT : "+number_format(G2N,2)+" บาท");
                    $("#G3N").html("ไม่รวม VAT : "+number_format(G3N,2)+" บาท");
                    $("#G4N").html("ไม่รวม VAT : "+number_format(G4N,2)+" บาท");
                    $("#G5N").html("ไม่รวม VAT : "+number_format(G5N,2)+" บาท");
                    // console.log(Data);
                })
            }
        })
    }
    $(".WG").on("click", function(e) {
        e.preventDefault();
        $("#Table-Content").hide();
        var DataWG = $(this).attr("data-wg");
        $.ajax({
            url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=WG",
            type: "POST",
            data: { WG : DataWG, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#NameWarehouse").html(inval['Name']);
                    $("#Content-Warehouse").html(inval['Data']);
                    $("#ModalWarehouse").modal("show");
                })
                $(".WSG").on("click", function(e) {
                    e.preventDefault();
                    $("#Table-Content").show();
                    var DataWSG = $(this).attr("data-wsg");
                    var DataName = $(this).attr("data-ws-name");
                    $("#DataWSG").val(DataWSG);
                    $("#DataName").val(DataName);
                    $("#SelectWSG").val("status").change();
                    SelectWSG();
                })
            }
        })
    })

    function SelectWSG() {
        var DataSelectWSG = $("#SelectWSG").val();
        var DataWSG = $("#DataWSG").val();
        var DataName = $("#DataName").val();
        switch (DataSelectWSG) {
            case "status":
                $(".overlay").show();
                $.ajax({
                    url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=WSG",
                    type: "POST",
                    data: { WSG : DataWSG,},
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#H-Content").html(DataName);
                            var Thead = "<tr>"+
                                            "<th width='30%' rowspan='2' class='text-center align-bottom'>คลังสินค้า</th>"+
                                            "<th colspan='7' class='text-center'>มูลค่าสินค้าคงคลัง (บาท) : แบ่งตามสถานะสินค้า</th>"+
                                        "</tr>"+
                                        "<tr class='text-center'>"+
                                            "<th>สถานะ D</th>"+
                                            "<th>สถานะ A</th>"+
                                            "<th>สถานะ W</th>"+
                                            "<th>สถานะ N</th>"+
                                            "<th>สถานะ M</th>"+
                                            "<th>สถานะอื่น ๆ</th>"+
                                            "<th>รวมทุกสถานะ</th>"+
                                        "</tr>";
                            $("#TheadWSG").html(Thead);
                            $("#TbodyWSG").html(inval['Tbody']);
                            $("#TfootWSG").html(inval['Tfoot']);
                            $(".overlay").hide();
                        })
                        $(".WSG-ws").on("click", function(e) {
                            e.preventDefault();
                            var DataWS = $(this).attr("data-ws");
                            WSGws(DataWS);
                        })
                    }
                })
            break;
            case "aging":
                $(".overlay").show();
                $.ajax({
                    url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=WSG_aging",
                    type: "POST",
                    data: { WSG : DataWSG,},
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#H-Content").html(DataName);
                            var Thead = "<tr>"+
                                            "<th width='30%' rowspan='2' class='text-center align-bottom'>คลังสินค้า</th>"+
                                            "<th colspan='6' class='text-center'>มูลค่าสินค้าคงคลัง (บาท) : แบ่งตามอายุจัดเก็บ</th>"+
                                        "</tr>"+
                                        "<tr class='text-center'>"+
                                            "<th>0 - 3 เดือน</th>"+
                                            "<th>4 - 6 เดือน</th>"+
                                            "<th>7 - 12 เดือน</th>"+
                                            "<th>13 - 24 เดือน</th>"+
                                            "<th>24 เดือนขึ้นไป</th>"+
                                            "<th>รวมทั้งหมด</th>"+
                                        "</tr>";
                            $("#TheadWSG").html(Thead);
                            $("#TbodyWSG").html(inval['Tbody']);
                            $("#TfootWSG").html(inval['Tfoot']);
                            $(".overlay").hide();
                        })
                        $(".WSG-ws").on("click", function(e) {
                            e.preventDefault();
                            var DataWS = $(this).attr("data-ws");
                            WSGws(DataWS);
                        })
                    }
                })
            break;
            case "moving": 
                $(".overlay").show();
                $.ajax({
                    url: "menus/warehouse/ajax/ajaxinvnttrns.php?a=CallData3&WSG=moving",
                    type: "POST",
                    data: { WSG : DataWSG,},
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#H-Content").html(DataName);
                            var Thead = 
                                "<tr class='text-center'>"+
                                    "<th rowspan='2'>ชื่อคลัง</th>"+
                                    "<th rowspan='2' width='10%'>รายละเอียด</th>"+
                                    "<th colspan='12'>ต้นทุน (บาท)</th>"+
                                "</tr>"+
                                "<tr class='text-center'>";
                                    <?php for($m = 1; $m <= 12; $m++) { ?>
                                        Thead += "<th width='6.66%'><?php echo FullMonth($m); ?></th>";
                                    <?php } ?>
                                Thead += "</tr>";
                            $("#TheadWSG").html(Thead);
                            $("#TbodyWSG").html(inval['Tbody']);
                            $("#TfootWSG").html("");
                            $(".overlay").hide();
                        })
                        $(".WSG-ws").on("click", function(e) {
                            e.preventDefault();
                            var DataWS = $(this).attr("data-ws");
                            WSGws(DataWS);
                        })
                    }
                })
            break;
            default: break;
        }
        function WSGws(DataWS) {
            $(".overlay").show();
            $.ajax({
                url: "menus/SingleReport/ajax/ajaxsingle_report.php?a=WSGws",
                type: "POST",
                data: { WSGws : DataWS },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#H-ModalWH-WSGws").html(inval['HeadName']);
                        if(inval['Row'] != 0) {
                            var no = inval['no'].split('|');
                            var ItemName = inval['ItemName'].split('|');
                            var Status = inval['Status'].split('|');
                            var Invntry = inval['Invntry'].split('|');
                            var OnHand = inval['OnHand'].split('|');
                            var IsCommited = inval['IsCommited'].split('|');
                            var OnOrder = inval['OnOrder'].split('|');
                            var StockValue = inval['StockValue'].split('|');
                            var LastImpDate = inval['LastImpDate'].split('|');
                            var Aging = inval['Aging'].split('|');
                            var StockValueSum = inval['StockValueSum'];
    
                            var Thead = "<tr>"+
                                            "<th width='3%' rowspan='2' class='text-center border-end align-bottom'>No.</th>"+
                                            "<th width='30%' rowspan='2' class='text-center border-end align-bottom'>ชื่อสินค้า</th>"+
                                            "<th width='10' rowspan='2' class='text-center border-end align-bottom'>สถานะ</th>"+
                                            "<th width='10' rowspan='2' class='text-center border-end align-bottom'>หน่วย</th>"+
                                            "<th width='' colspan='4' class='text-center border-end'>จำนวนสินค้าในคลัง : "+inval['HeadName']+"</th>"+
                                            "<th width='8%' rowspan='2' class='text-center border-end align-bottom'>วันที่เข้าล่าสุด</th>"+
                                            "<th width='8%' rowspan='2' class='text-center align-bottom'>Aging (เดือน)</th>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<th width='10%' class='text-center border-end'>คงคลัง</th>"+
                                            "<th width='10%' class='text-center border-end'>จอง</th>"+
                                            "<th width='10%' class='text-center border-end'>กำลังสั่ง</th>"+
                                            "<th width='10%' class='text-center border-end'>มูลค่ารวม (บาท)</th>"+
                                        "</tr>";
                            var Tbody = "";
                            for (var i = 0; i <= no.length-1; i++) {
                                Tbody +="<tr>"+
                                            "<td class='text-center'>"+no[i]+"</td>"+
                                            "<td>"+ItemName[i]+"</td>"+
                                            "<td class='text-center'>"+Status[i]+"</td>"+
                                            "<td class='text-center'>"+Invntry[i]+"</td>"+
                                            "<td class='text-right'>"+OnHand[i]+"</td>"+
                                            "<td class='text-right'>"+IsCommited[i]+"</td>"+
                                            "<td class='text-right'>"+OnOrder[i]+"</td>"+
                                            "<td class='text-right'>"+StockValue[i]+"</td>"+
                                            "<td class='text-center'>"+LastImpDate[i]+"</td>"+
                                            Aging[i]+
                                        "</tr>";
                            }
                            var Tfoot = "<tr>"+
                                            "<td></td>"+
                                            "<td></td>"+
                                            "<td></td>"+
                                            "<td></td>"+
                                            "<td></td>"+
                                            "<td></td>"+
                                            "<td class='text-right fw-bolder'>รวมมูลค่าทั้งหมด</td>"+
                                            "<td class='text-right fw-bolder text-primary'>"+StockValueSum+"</td>"+
                                            "<td></td>"+
                                            "<td></td>"+
                                        "</tr>";
                            $('#ModalExportWH-WSGws').DataTable().destroy();
                            $('#TheadWH-WSGws, #TbodyWH-WSGws, #TfootWH-WSGws').empty();
                            $("#TheadWH-WSGws").html(Thead);
                            $("#TbodyWH-WSGws").html(Tbody);
                            $("#TfootWH-WSGws").html(Tfoot);
                            ModalExport();
                            $("#ModalWH-WSGws").modal('show');
                        }else{
                            $("#alert_header").html("<i class=\"fas fa-exclamation-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
                            $("#alert_body").html("ไม่มีข้อมูลความเคลื่อนไหวสินค้า");
                            $("#alert_modal").modal('show');
                        }
                        $(".overlay").hide();
                    })
                }
            })
        }
    }
</script>
<!-- END รายงานคลังสินค้า -->
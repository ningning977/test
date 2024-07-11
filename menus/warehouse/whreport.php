<style type="text/css">

</style>
<?php
echo "<input type='hidden' id='HeadeMenuLink' value = '" . $_GET['p'] . "'>";
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
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br /><br />
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
                <!---------- เนื้อหา Pages ------------>
                <div class="row">
                    <div class="col-12">
                        <nav class="nav nav-pills nav-justified">
                            <a class="maintab nav-link active" data-tab="0" href="javascript:void(0);">พนง.เบิกสินค้า</a>
                            <a class="maintab nav-link" data-tab="1" href="javascript:void(0);">พนง.เติมสินค้า</a>
                            <a class="maintab nav-link" data-tab="2" href="javascript:void(0);">ธุรการเปิดบิล</a>
                            <a class="maintab nav-link" data-tab="3" href="javascript:void(0);">ธุรการคลังสินค้า</a>
                            <a class="maintab nav-link" data-tab="4" href="javascript:void(0);">แพ็กสินค้า</a>
                            <a class="maintab nav-link" data-tab="5" href="javascript:void(0);">ขนส่งสินค้า</a>
                        </nav>
                    </div>
                </div>

                <div class="row mt-4" id="TopFilter"></div>
                <div class="row mt-2" id="DivContent">
                    <div class="col-12">
                        <table class="table table-bordered table-hover table-sm" id="WhReportTB" style="font-size: 13px;">
                            <thead class="text-center text-white" style="background-color: #9A1118;"></thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
                <!-------- สินสุดเนื้อหา Pages --------->
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function CallHead() {
        $(".overlay").show();
        var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/human/ajax/ajaxemplist.php?a=head", //แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
            type: "POST",
            data: {
                MenuCase: MenuCase,
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
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

    class RenderLayout {
        constructor(Tab) {
            this.Tab = Tab
        }

        RenderTop() {
            let thai_month = ["","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
            let start_year = 2023;
            let this_year  = (new Date().getFullYear());
            let this_month = (new Date().getMonth()) + 1;
            let filt_year  = "";
            let filt_month = "";
            let y_slct     = "";
            let m_slct     = "";
            let TopFilter  = "";
            for(let y = this_year; y >= start_year; y--) {
                if(y == this_year) {
                    y_slct = " selected";
                } else {
                    y_slct = "";
                }
                filt_year += "<option value='"+y+"'"+y_slct+">"+y+"</option>";
            }

            for(let m = 1; m <= 12; m++) {
                if(m == this_month) {
                    m_slct = " selected";
                } else {
                    m_slct = "";
                }
                filt_month += "<option value='"+m+"'"+m_slct+">"+thai_month[m]+"</option>";
            }

            TopFilter = 
                "<div class='col-1'>"+
                    "<div class='form-group'>"+
                        "<label for='filt_year'>เลือกปี</label>"+
                        "<select class='form-select form-select-sm' id='filt_year'>"+filt_year+"</select>"+
                    "</div>"+
                "</div>"+
                "<div class='col-2'>"+
                    "<div class='form-group'>"+
                        "<label for='filt_month'>เลือกปี</label>"+
                        "<select class='form-select form-select-sm' id='filt_month'>"+filt_month+"</select>"+
                    "</div>"+
                "</div>";

            switch(this.Tab) {
                case "0":
                case "1":
                case "2":
                case "4":
                    TopFilter +=
                        "<div class='col-2'>"+
                            "<div class='form-group'>"+
                                "<label for='filt_user'>เลือกพนักงาน/โต๊ะ</label>"+
                                "<select class='form-select form-select-sm' id='filt_user'><option value='ALL'>เลือกทั้งหมด</option></select>"+
                            "</div>"+
                        "</div>";
                break;
            }

            let label_user   = "";
            let filt_content = "";
            switch(this.Tab) {
                case "0":
                case "1":
                case "2":
                case "4":            
                    $.ajax({
                        url: "menus/warehouse/ajax/ajaxwhreport.php?a=FiltUser",
                        type: "POST",
                        data: {
                            Tab: this.Tab
                        },
                        success: function(result) {
                            var obj = jQuery.parseJSON(result);
                            $.each(obj, function(key, inval) {
                                let Rows = inval['Rows'];
                                for(let i = 0; i < Rows; i++) {
                                    filt_content += "<option value='"+inval[i]['ukey']+"'>"+inval[i]['Name']+"</option>";
                                }
                                $("#filt_user").append(filt_content);
                            });
                        }
                    });
                break;
            }

            switch(this.Tab) {
                case "0":
                    TopFilter +=
                        "<div class='col-2'>"+
                            "<div class='form-group'>"+
                                "<label for='filt_type'>ประเภทร้านค้า</label>"+
                                "<select class='form-select form-select-sm' id='filt_type'><option value='ALL' selected>ร้านค้าทั้งหมด</option><option value='MT'>ห้างโมเดิร์นเทรด</option><option value='TT'>ร้านค้าทั่วไป</option></select>"+
                            "</div>"+
                        "</div>";
                break;
            }

            TopFilter += 
                "<div class='col-1'>"+
                    "<div class='form-group'>"+
                        "<label for='filt_search'>&nbsp;</label>"+
                        "<button type='button' class='btn btn-primary btn-sm w-100' id='btn_search' onclick='GetData("+this.Tab+");'><i class='fas fa-search fa-fw fa-1x'></i> ค้นหา</button>"+
                    "</div>"+
                "</div>";

            return TopFilter;
        }

        RenderThead() {
            let tHead = "";
            switch(this.Tab) {
                case "0":
                    tHead +=
                        "<tr>"+
                            "<th rowspan='2'>วัน</th>"+
                            "<th width='12.5%' rowspan='2'>วันที่</th>"+
                            "<th colspan='2'>เป้าหมายการเบิก</th>"+
                            "<th colspan='2'>เบิกตามกำหนด</th>"+
                            "<th width='12.5%' rowspan='2'>S/O ยกเลิก</th>"+
                        "</tr>"+
                        "<tr>"+
                            "<th width='12.5%'>S/O</th>"+
                            "<th width='12.5%'>SKU</th>"+
                            "<th width='12.5%'>S/O</th>"+
                            "<th width='12.5%'>SKU</th>"+
                        "</tr>";
                break;
                case "1":
                    tHead +=
                        "<tr>"+
                            "<th>วัน</th>"+
                            "<th width='12.5%'>วันที่</th>"+
                            "<th width='25%'>เติมสินค้า<br/>(SKU)</th>"+
                            "<th width='25%'>โอนย้ายสินค้า<br/>(SKU)</th>"+
                        "</tr>";
                break;
                case "2":
                    tHead +=
                        "<tr>"+
                            "<th rowspan='2'>วัน</th>"+
                            "<th width='12.5%' rowspan='2'>วันที่</th>"+
                            "<th colspan='3'>จำนวนการเปิดบิล (ใบ)</th>"+
                        "</tr>"+
                        "<tr>"+
                            "<th width='12.5%'>ร้านค้าโมเดิร์นเทรด</th>"+
                            "<th width='12.5%'>ร้านค้าทั่วไป</th>"+
                            "<th width='12.5%'>รวมทั้งหมด</th>"+
                        "</tr>";
                break;
                case "3":
                    tHead += 
                        "<tr>"+
                            "<th rowspan='2'>วัน</th>"+
                            "<th width='12.5%' rowspan='2'>วันที่</th>"+
                            "<th colspan='4'>จำนวนเอกสาร (ใบ)</th>"+
                        "</tr>"+
                        "<tr>"+
                            "<th width='12.5%'>ใบเบิก (PA / PB)</th>"+
                            "<th width='12.5%'>โอนย้ายคลังสินค้า</th>"+
                            "<th width='12.5%'>แปลง / ผลิต</th>"+
                            "<th width='12.5%'>ถอดอะไหล่</th>"+
                        "</tr>";
                break;
                case "4":
                    tHead +=
                        "<tr>"+
                            "<th rowspan='3'>วัน</th>"+
                            "<th width='12.5%' rowspan='3'>วันที่</th>"+
                            "<th colspan='6'>ข้อมูลการแพ็ก</th>"+
                        "</tr>"+
                        "<tr>"+
                            "<th colspan='2'>ห้างโมเดิร์นเทรด</th>"+
                            "<th colspan='2'>ร้านค้าทั่วไป</th>"+
                            "<th colspan='2'>รวมทั้งหมด</th>"+
                        "</tr>"+
                        "<tr>"+
                            "<th width='12.5%'>บิล</th>"+
                            "<th width='12.5%'>ลัง</th>"+
                            "<th width='12.5%'>บิล</th>"+
                            "<th width='12.5%'>ลัง</th>"+
                            "<th width='12.5%'>บิล</th>"+
                            "<th width='12.5%'>ลัง</th>"+
                        "</tr>";
                break;
                case "5":
                    tHead +=
                        "<tr>"+
                            "<th>วัน</th>"+
                            "<th width='12.5%'>วันที่</th>"+
                            "<th width='12.5%'>จำนวนรถ<br/>ที่เข้าโหลด (คัน)</th>"+
                            "<th width='12.5%'>จำนวนบิล<br/>ที่โหลด (ใบ)</th>"+
                            "<th width='12.5%'>จำนวนลัง<br/>ที่โหลด (ลัง)</th>"+
                        "</tr>";
                break;
            }

            return tHead;
        }

    }

    function GetData(Tab) {
        var DataAJAX;
        let filt_year = $("#filt_year").val();
        let filt_month = $("#filt_month").val();
        let filt_user = $("#filt_user").val();
        let filt_type = $("#filt_type").val();

        DataAJAX = {
            o: Tab,
            y: filt_year,
            m: filt_month,
        }

        switch(Tab) {
            case 0:
            case 3:
                DataAJAX.u = filt_user;
                DataAJAX.t = filt_type;
            break;
            case 1:
            case 2:
            case 4:
                DataAJAX.u = filt_user;
            break;
        }
        $(".overlay").show();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxwhreport.php?a=GetData",
            type: "POST",
            data: DataAJAX,
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $(".overlay").hide();
                    let tBody = "";
                    let tFoot = "";
                    let LoopDay  = parseFloat(inval['LoopDay']);
                    switch(Tab) {
                        case 0:
                            for(d = 1; d <= LoopDay; d++) {
                                if(inval[d]['WeekDate'] == 0) {
                                    var RowCls = "class= 'table-danger text-danger'";
                                } else {
                                    var RowCls = "";
                                }

                                if(inval[d]['TargetSO'] == null || inval[d]['TargetSO'] == 0) { var TargetSO = "-"; } else { var TargetSO = inval[d]['TargetSO']; }
                                if(inval[d]['TargetSKU'] == null || inval[d]['TargetSKU'] == 0) { var TargetSKU = "-"; } else { var TargetSKU = inval[d]['TargetSKU']; }
                                if(inval[d]['ONTIME_SO'] == null || inval[d]['ONTIME_SO'] == 0) { var ONTIME_SO = "-"; } else { var ONTIME_SO = inval[d]['ONTIME_SO']; }
                                if(inval[d]['ONTIME_SKU'] == null || inval[d]['ONTIME_SKU'] == 0) { var ONTIME_SKU = "-"; } else { var ONTIME_SKU = inval[d]['ONTIME_SKU']; }
                                if(inval[d]['CANCELED_SO'] == null || inval[d]['CANCELED_SO'] == 0) { var CANCELED_SO = "-"; } else { var CANCELED_SO = inval[d]['CANCELED_SO']; }
                                tBody +=
                                    "<tr "+RowCls+">"+
                                        "<td>"+inval[d]['WeekName']+"</td>"+
                                        "<td class='text-center'>"+d+"</td>"+
                                        "<td class='text-right'>"+TargetSO+"</td>"+
                                        "<td class='text-right'>"+TargetSKU+"</td>"+
                                        "<td class='text-right' style='font-weight: bold;'>"+ONTIME_SO+"</td>"+
                                        "<td class='text-right' style='font-weight: bold;'>"+ONTIME_SKU+"</td>"+
                                        "<td class='text-right'>"+CANCELED_SO+"</td>"+
                                    "</tr>";
                            }

                            tFoot =
                                "<tr class='table-active' style='font-weight: bold;'>"+
                                    "<td colspan='2'>รวมทั้งหมด</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_TargetSO'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_TargetSKU'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_ONTIME_SO'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_ONTIME_SKU'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_CANCELED_SO'],0)+"</td>"+
                                "</tr>";
                        break;
                        case 1:
                            for(d = 1; d <= LoopDay; d++) {
                                if(inval[d]['WeekDate'] == 0) {
                                    var RowCls = "class= 'table-danger text-danger'";
                                } else {
                                    var RowCls = "";
                                }

                                if(inval[d]['Refill'] == null || inval[d]['Refill'] == 0) { var Refill = "-"; } else { var Refill = inval[d]['Refill']; }
                                if(inval[d]['Transfer'] == null || inval[d]['Transfer'] == 0) { var Transfer = "-"; } else { var Transfer = inval[d]['Transfer']; }
                                tBody +=
                                    "<tr "+RowCls+">"+
                                        "<td>"+inval[d]['WeekName']+"</td>"+
                                        "<td class='text-center'>"+d+"</td>"+
                                        "<td class='text-right'>"+Refill+"</td>"+
                                        "<td class='text-right'>"+Transfer+"</td>"+
                                    "</tr>";
                            }
                            tFoot =
                                "<tr class='table-active' style='font-weight: bold;'>"+
                                    "<td colspan='2'>รวมทั้งหมด</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_Refill'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_Transfer'],0)+"</td>"+
                                "</tr>";
                        break;
                        case 2:
                            for(d = 1; d <= LoopDay; d++) {
                                if(inval[d]['WeekDate'] == 0) {
                                    var RowCls = "class= 'table-danger text-danger'";
                                } else {
                                    var RowCls = "";
                                }

                                if(inval[d]['IV_MT'] == null || inval[d]['IV_MT'] == 0) { var IV_MT = "-"; } else { var IV_MT = inval[d]['IV_MT']; }
                                if(inval[d]['IV_TT'] == null || inval[d]['IV_TT'] == 0) { var IV_TT = "-"; } else { var IV_TT = inval[d]['IV_TT']; }
                                if(inval[d]['IV_ALL'] == null || inval[d]['IV_ALL'] == 0) { var IV_ALL = "-"; } else { var IV_ALL = inval[d]['IV_ALL']; }
                                tBody +=
                                    "<tr "+RowCls+">"+
                                        "<td>"+inval[d]['WeekName']+"</td>"+
                                        "<td class='text-center'>"+d+"</td>"+
                                        "<td class='text-right'>"+IV_MT+"</td>"+
                                        "<td class='text-right'>"+IV_TT+"</td>"+
                                        "<td class='text-right' style='font-weight: bold;'>"+IV_ALL+"</td>"+
                                    "</tr>";
                            }
                            tFoot =
                                "<tr class='table-active' style='font-weight: bold;'>"+
                                    "<td colspan='2'>รวมทั้งหมด</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_IV_MT'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_IV_TT'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_IV_ALL'],0)+"</td>"+
                                "</tr>";
                        break;
                        case 3:
                            for(d = 1; d <= LoopDay; d++) {
                                if(inval[d]['WeekDate'] == 0) {
                                    var RowCls = "class= 'table-danger text-danger'";
                                } else {
                                    var RowCls = "";
                                }

                                if(inval[d]['Cnt_PA'] == null || inval[d]['Cnt_PA'] == 0) { var Cnt_PA = "-"; } else { var Cnt_PA = inval[d]['Cnt_PA']; }
                                if(inval[d]['Cnt_TR'] == null || inval[d]['Cnt_TR'] == 0) { var Cnt_TR = "-"; } else { var Cnt_TR = inval[d]['Cnt_TR']; }
                                if(inval[d]['Cnt_PD'] == null || inval[d]['Cnt_PD'] == 0) { var Cnt_PD = "-"; } else { var Cnt_PD = inval[d]['Cnt_PD']; }
                                if(inval[d]['Cnt_SP'] == null || inval[d]['Cnt_SP'] == 0) { var Cnt_SP = "-"; } else { var Cnt_SP = inval[d]['Cnt_SP']; }
                                tBody +=
                                    "<tr "+RowCls+">"+
                                        "<td>"+inval[d]['WeekName']+"</td>"+
                                        "<td class='text-center'>"+d+"</td>"+
                                        "<td class='text-right'>"+Cnt_PA+"</td>"+
                                        "<td class='text-right'>"+Cnt_TR+"</td>"+
                                        "<td class='text-right'>"+Cnt_PD+"</td>"+
                                        "<td class='text-right'>"+Cnt_SP+"</td>"+
                                    "</tr>";
                            }
                        break;
                        case 4:
                            for(d = 1; d <= LoopDay; d++) {
                                if(inval[d]['WeekDate'] == 0) {
                                    var RowCls = "class= 'table-danger text-danger'";
                                } else {
                                    var RowCls = "";
                                }

                                if(inval[d]['MT_Bills'] == null || inval[d]['MT_Bills'] == 0) { var MT_Bills = "-"; } else { var MT_Bills = inval[d]['MT_Bills']; }
                                if(inval[d]['MT_Boxes'] == null || inval[d]['MT_Boxes'] == 0) { var MT_Boxes = "-"; } else { var MT_Boxes = inval[d]['MT_Boxes']; }
                                if(inval[d]['TT_Bills'] == null || inval[d]['TT_Bills'] == 0) { var TT_Bills = "-"; } else { var TT_Bills = inval[d]['TT_Bills']; }
                                if(inval[d]['TT_Boxes'] == null || inval[d]['TT_Boxes'] == 0) { var TT_Boxes = "-"; } else { var TT_Boxes = inval[d]['TT_Boxes']; }
                                if(inval[d]['ALL_Bills'] == null || inval[d]['ALL_Bills'] == 0) { var ALL_Bills = "-"; } else { var ALL_Bills = inval[d]['ALL_Bills']; }
                                if(inval[d]['ALL_Boxes'] == null || inval[d]['ALL_Boxes'] == 0) { var ALL_Boxes = "-"; } else { var ALL_Boxes = inval[d]['ALL_Boxes']; }
                                tBody +=
                                    "<tr "+RowCls+">"+
                                        "<td>"+inval[d]['WeekName']+"</td>"+
                                        "<td class='text-center'>"+d+"</td>"+
                                        "<td class='text-right'>"+MT_Bills+"</td>"+
                                        "<td class='text-right'>"+MT_Boxes+"</td>"+
                                        "<td class='text-right'>"+TT_Bills+"</td>"+
                                        "<td class='text-right'>"+TT_Boxes+"</td>"+
                                        "<td class='text-right' style='font-weight: bold;'>"+ALL_Bills+"</td>"+
                                        "<td class='text-right' style='font-weight: bold;'>"+ALL_Boxes+"</td>"+
                                    "</tr>";
                            }
                            tFoot =
                                "<tr class='table-active' style='font-weight: bold;'>"+
                                    "<td colspan='2'>รวมทั้งหมด</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_MT_Bills'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_MT_Boxes'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_TT_Bills'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_TT_Boxes'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_ALL_Bills'],0)+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['SUM_ALL_Boxes'],0)+"</td>"+
                                "</tr>";
                        break;
                        case 5:
                            for(d = 1; d <= LoopDay; d++) {
                                if(inval[d]['WeekDate'] == 0) {
                                    var RowCls = "class= 'table-danger text-danger'";
                                } else {
                                    var RowCls = "";
                                }

                                if(inval[d]['Cars'] == null || inval[d]['Cars'] == 0) { var Cars = "-"; } else { var Cars = inval[d]['Cars']; }
                                if(inval[d]['Bills'] == null || inval[d]['Bills'] == 0) { var Bills = "-"; } else { var Bills = inval[d]['Bills']; }
                                if(inval[d]['Boxes'] == null || inval[d]['Boxes'] == 0) { var Boxes = "-"; } else { var Boxes = inval[d]['Boxes']; }
                                tBody +=
                                    "<tr "+RowCls+">"+
                                        "<td>"+inval[d]['WeekName']+"</td>"+
                                        "<td class='text-center'>"+d+"</td>"+
                                        "<td class='text-right'>"+Cars+"</td>"+
                                        "<td class='text-right'>"+Bills+"</td>"+
                                        "<td class='text-right'>"+Boxes+"</td>"+
                                    "</tr>";
                            }
                        break;
                    }

                    $("#WhReportTB tbody").html(tBody);
                    $("#WhReportTB tfoot").html(tFoot);
                });
            }
        })
    }



    /* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
    $(document).ready(function() {
        CallHead();
        let CallData = new RenderLayout("0");
        $("#TopFilter").html(CallData.RenderTop());
        $("#WhReportTB thead").html(CallData.RenderThead());
        GetData(0);
    });

    $(".maintab").on("click", function(e) {
        e.preventDefault();
        let Tab = $(this).attr("data-tab");
        $(".maintab").removeClass("active");
        $(".maintab[data-tab='"+Tab+"']").addClass("active");

        let CallData = new RenderLayout(Tab);
        $("#TopFilter").html(CallData.RenderTop());
        $("#WhReportTB tbody, #WhReportTB tfoot").empty();
        $("#WhReportTB thead").html(CallData.RenderThead());
        GetData(parseFloat(Tab) );
    });
</script>
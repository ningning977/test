<?php
    $start_year = 2015;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<style type="text/css">
    .font-weight-bold {
        font-weight: bold;
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
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="filt_top">จำนวนอันดับ</label>
                            <input type="number" class="form-control form-control-sm text-right" name="filt_top" id="filt_top" value="50" />
                            <small class="text-muted">0 = ไม่จำกัดอันดับ</small>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="filt_year">ยอดขายปี</label>
                            <select class="form-control form-control-sm selectpicker" name="filt_year" id="filt_year" data-size="5">
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
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="filt_month1">ตั้งแต่เดือน</label>
                            <select class="form-control form-control-sm selectpicker" name="filt_month1" id="filt_month1">
                            <?php
                                for($m = 1; $m <= 12; $m++) {
                                    if($m == 1) {
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
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="filt_month2">ถึงเดือน</label>
                            <select class="form-control form-control-sm selectpicker" name="filt_month2" id="filt_month2">
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
                        $opt_ALL = " disabled";
                        $opt_MT1 = " disabled";
                        $opt_MT2 = " disabled";
                        $opt_TT2 = " disabled";
                        $opt_TT1 = " disabled";
                        $opt_ONL = " disabled";

                        switch($DeptCode) {
                            case "DP005": $opt_TT2 = " selected"; break;
                            case "DP006": $opt_MT1 = " selected"; break;
                            case "DP007": $opt_MT2 = " selected"; break;
                            case "DP008": $opt_TT1 = " selected"; break;
                            case "DP003":
                                switch($LvCode) {
                                    case "LV018":
                                    case "LV019":
                                    case "LV104":
                                    case "LV105":
                                    case "LV106":
                                        $opt_ONL = " selected"; 
                                    break;
                                    default:
                                        $opt_ALL = " selected";
                                        $opt_MT1 = "";
                                        $opt_MT2 = "";
                                        $opt_TT2 = "";
                                        $opt_TT1 = "";
                                        $opt_ONL = "";
                                    break;
                                }
                            break;
                            default:
                                $opt_ALL = " selected";
                                $opt_MT1 = "";
                                $opt_MT2 = "";
                                $opt_TT2 = "";
                                $opt_TT1 = "";
                                $opt_ONL = "";
                            break;
                         }
                    ?>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="filt_TeamCode">เลือกทีมขาย</label>
                            <select class="form-control form-control-sm selectpicker" name="filt_TeamCode" id="filt_TeamCode">
                                <option value="ALL" <?php echo $opt_ALL; ?>>ทุกทีม</option>
                                <option value="MT1" <?php echo $opt_MT1; ?>>ขาย โมเดิร์นเทรด 1</option>
                                <option value="MT2" <?php echo $opt_MT2; ?>>ขาย โมเดิร์นเทรด 2</option>
                                <option value="TT2" <?php echo $opt_TT2; ?>>ขาย ตจว.</option>
                                <option value="TT1" <?php echo $opt_TT1; ?>>ขาย กทม. / หน้าร้าน</option>
                                <option value="ONL" <?php echo $opt_ONL; ?>>ขาย ออนไลน์</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="filt_SlpCode">เลือกพนักงานขาย</label>
                            <select class="form-control form-control-sm" name="filt_SlpCode" id="filt_SlpCode">
                                <option value="ALL">เลือกทั้งหมด</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="filt_CardCode">เลือกร้านค้า</label>
                            <select class="form-control form-control-sm" name="filt_CardCode" id="filt_CardCode" data-live-search="true" data-size="10">
                                <option value="ALL">เลือกทั้งหมด</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" id="btn_search" onclick="SearchData('CRNT::SAL::DESC');"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</button>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm w-100" id="btn_search" onclick="ExportData('CRNT::SAL::DESC');"><i class="fas fa-file-excel fa-fw fa-1x"></i> Excel</button>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="TopSkuData">
                                <thead class="text-center" style="background-color: #9A1118; color: #FFF;">
                                    <tr>
                                        <th width="5%" rowspan="3">No.</th>
                                        <th width="7.5%" rowspan="3">
                                            รหัสสินค้า<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('NULL::ITEM::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('NULL::ITEM::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th rowspan="3">ชื่อสินค้า</th>
                                        <th width="5%" rowspan="3">สถานะสินค้า</th>
                                        <th width="7.5%" rowspan="3">หน่วยขาย</th>
                                        <th colspan="6">ยอดขาย</th>
                                        <th width="5%" rowspan="3">% การเติบโต<br/>(จำนวน)</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" id="Prev_Txt">&nbsp;</th>
                                        <th colspan="3" id="Crnt_Txt">&nbsp;</th>
                                    </tr>
                                    <tr>
                                        <th width="7.5%">
                                            จำนวน (หน่วย)<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('PREV::QTY::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('PREV::QTY::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="10%">
                                            มูลค่า (บาท)<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('PREV::SAL::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('PREV::SAL::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            % GP
                                        </th>
                                        <th width="7.5%">
                                            จำนวน (หน่วย)<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('CRNT::QTY::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('CRNT::QTY::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="10%">
                                            มูลค่า (บาท)<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('CRNT::SAL::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('CRNT::SAL::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            % GP
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="10">กรุณากดปุ่มค้นหาก่อน :(</td>
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

function GetSlpCode() {
    $.ajax({
        url: "menus/sale/ajax/ajaxtopsku.php?p=GetSlpCode",
        type: "POST",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            var slct = "";
            $.each(obj, function(key,inval) {
                var Rows = inval['Rows'];
                var DeptCode = '<?php echo $_SESSION['DeptCode']; ?>';
                for(i = 0; i < Rows; i++) {
                    var opt = "";
                    if(DeptCode != "DP001" && DeptCode != "DP002" && DeptCode != "DP003" && DeptCode != inval[i]['DeptCode'] && DeptCode != "DP010") {
                        opt = " disabled";
                    }
                    slct += "<option value='"+inval[i]['SlpCode']+"'"+opt+">"+inval[i]['SlpName']+"</option>";
                }
                $("#filt_SlpCode").append(slct).selectpicker();
            });
        }
    })
}

function GetCardCode() {
    $.ajax({
        url: "../json/OCRD.json",
        cache: false,
        success: function(result) {
            var filt_data = result.
                                filter(x => x.CardStatus == "A").
                                filter(x => x.CardType == "C").
                                sort(function(key, inval) {
                                    return key.CardCode.localeCompare(inval.CardCode);
                                });
            var opt = "";

            $.each(filt_data, function(key, inval) {
                opt += "<option value='"+inval.CardCode+"'>"+inval.CardCode+" | "+inval.CardName+"</option>";
            });
            $("#filt_CardCode").append(opt).selectpicker();
        }
    });
}

function SearchData(SortType) {
    var filt_top      = $("#filt_top").val();
    var filt_year     = $("#filt_year").val();
    var filt_month1   = $("#filt_month1").val();
    var filt_month2   = $("#filt_month2").val();
    var filt_TeamCode = $("#filt_TeamCode").val();
    var filt_SlpCode  = $("#filt_SlpCode").val();
    var filt_CardCode = $("#filt_CardCode").val();
    var prev_year     = parseFloat(filt_year) - 1;
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxtopsku.php?p=SearchData",
        type: "POST",
        data: {
            filt_top     : filt_top,
            filt_year    : filt_year,
            filt_month1  : filt_month1,
            filt_month2  : filt_month2,
            filt_TeamCode: filt_TeamCode,
            filt_SlpCode : filt_SlpCode,
            filt_CardCode: filt_CardCode,
            SortType: SortType
        },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            var Crnt_Txt = $("#filt_month1 option:selected").text()+" - "+$("#filt_month2 option:selected").text()+" ปี "+filt_year;
            var Prev_Txt = $("#filt_month1 option:selected").text()+" - "+$("#filt_month2 option:selected").text()+" ปี "+prev_year;
            $("#Prev_Txt").html(Prev_Txt);
            $("#Crnt_Txt").html(Crnt_Txt);
            $.each(obj, function(key, inval) {
                var Rows = parseFloat(inval['Rows']);
                var tBody = "";
                var VisOrder = 1;
                var SortT = SortType.split("::");

                var SortItem = SortPvQt = SortPvGP = SortPvLt = SortCrQt = SortCrLt = SortCrGP = "";
                var ClassTxt = "table-warning font-weight-bold";
                switch(SortT[0]) {
                    case "NULL":
                        SortItem = ClassTxt;
                    break;
                    case "PREV":
                        switch(SortT[1]) {
                            case "QTY": SortPvQt = ClassTxt; break;
                            case "SAL": SortPvLt = ClassTxt; break;
                        }
                    break;
                    case "CRNT":
                        switch(SortT[1]) {
                            case "QTY": SortCrQt = ClassTxt; break;
                            case "SAL": SortCrLt = ClassTxt; break;
                        }
                    break;
                }
                if(Rows == 0) {
                    tBody += "<tr><td class='text-center' colspan='10'>ไม่พบผลลัพธ์ที่ค้นหา :(</td></tr>";
                } else {
                    for(i = 0; i < Rows; i++) {
                        
                        tBody +=
                            "<tr>"+
                                "<td class='text-right'>"+VisOrder+"</td>"+
                                "<td class='text-center "+SortItem+"'>"+inval[i]['ItemCode']+"</td>"+
                                "<td>"+inval[i]['ItemName']+"</td>"+
                                "<td class='text-center'>"+inval[i]['U_ProductStatus']+"</td>"+
                                "<td>"+inval[i]['SalUnitMsr']+"</td>"+
                                "<td class='text-right "+SortPvQt+"'>"+inval[i]['Prev_Qty']+"</td>"+
                                "<td class='text-right "+SortPvLt+"'>"+inval[i]['Prev_LineTotal']+"</td>"+
                                "<td class='text-center'>"+inval[i]['Prev_GP']+"</td>"+
                                "<td class='text-right "+SortCrQt+"'>"+inval[i]['Crnt_Qty']+"</td>"+
                                "<td class='text-right "+SortCrLt+"'>"+inval[i]['Crnt_LineTotal']+"</td>"+
                                "<td class='text-center'>"+inval[i]['Crnt_GP']+"</td>"+
                                "<td class='text-right "+SortCrLt+inval[i]['PcntCls']+"'>"+inval[i]['Pcnt']+"</td>"+
                            "</tr>";
                        VisOrder++;
                    }
                }
                $("#TopSkuData tbody").html(tBody);
                
            });
        }
    });
}

function ExportData(SortType) {
    var filt_top      = $("#filt_top").val();
    var filt_year     = $("#filt_year").val();
    var filt_month1   = $("#filt_month1").val();
    var filt_month2   = $("#filt_month2").val();
    var filt_TeamCode = $("#filt_TeamCode").val();
    var filt_SlpCode  = $("#filt_SlpCode").val();
    var filt_CardCode = $("#filt_CardCode").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxtopsku.php?p=ExportData",
        type: "POST",
        data: { filt_top : filt_top,
                filt_year : filt_year,
                filt_month1 : filt_month1,
                filt_month2 : filt_month2,
                filt_TeamCode : filt_TeamCode,
                filt_SlpCode : filt_SlpCode,
                filt_CardCode : filt_CardCode,
                SortType: SortType, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                if(inval['Rows'] != 3) {
                    window.open("../../FileExport/TopSku/"+inval['FileName'],'_blank');
                }else{
                    $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 65px;'></i>");
                    $("#alert_body").html("ไม่มีข้อมูล :(");
                    $("#alert_modal").modal("show");
                }
            });
            $(".overlay").hide();
        }
    });
}

$(document).ready(function(){
    CallHead();
    GetCardCode();
    GetSlpCode();
});
</script> 
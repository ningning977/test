<?php
    $start_year = 2015;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<style type="text/css">
    .font-weight-bold {
        font-weight: bold;
    }

    @media only screen and (max-width:820px) {
        .tableFix {
            overflow-y: auto;
            height: 800px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:821px) and (max-width:1180px) {
        .tableFix {
            overflow-y: auto;
            height: 450px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:1181px) {
        .tableFix {
            overflow-y: auto;
            height: 580px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
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
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" id="btn_search" onclick="SearchData('ITEM::ASC');"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</button>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm w-100" id="btn_search" onclick="ExportData('ITEM::ASC');"><i class="fas fa-file-excel fa-fw fa-1x"></i> Excel</button>
                        </div>
                    </div>
                    <div class="offset-lg-4 col-lg-3 col-6">
                        <div class="form-group">
                            <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i>  ค้นหา:</label>
                            <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรุณากรอกเพื่อค้นหา..." />
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive tableFix">
                            <table class="table table-bordered table-hover table-sm" style="font-size: 12px;" id="ItemByTeamData">
                                <thead class="text-center" style="background-color: #9A1118; color: #FFF;">
                                    <tr>
                                        <th width="5%" rowspan="2">No.</th>
                                        <th width="7.5%" rowspan="2">
                                            รหัสสินค้า<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('ITEM::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('ITEM::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th rowspan="2">ชื่อสินค้า</th>
                                        <th width="5%" rowspan="2">สถานะสินค้า</th>
                                        <th width="7.5%" rowspan="2">หน่วยขาย</th>
                                        <th colspan="8">ยอดขาย (หน่วย)</th>
                                        <th width="7.5%" rowspan="2">รวมทั้งหมด<br/>(หน่วย)</th>
                                    </tr>
                                    <tr>
                                        <th width="5%">
                                            MT1<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('MT1::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('MT1::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            MT2<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('MT2::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('MT2::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            TT กทม.<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('TT1::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('TT1::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            TT ตจว.<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('TT2::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('TT2::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            หน้าร้าน<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('OUL::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('OUL::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            ออนไลน์<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('ONL::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('ONL::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            ต่างประเทศ<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('EXP::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('EXP::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                        <th width="5%">
                                            ส่วนกลาง<br/>
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('KBI::ASC');"><i class="fas fa-sort-numeric-up fa-fw fa-lg"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="javascript: void(0);" class="text-white" onclick="SearchData('KBI::DESC');"><i class="fas fa-sort-numeric-down-alt fa-fw fa-lg"></i></a> 
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="14">กรุณากดปุ่มค้นหาก่อน :(</td>
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

function SearchData(SortType) {
    var filt_year     = $("#filt_year").val();
    var filt_month1   = $("#filt_month1").val();
    var filt_month2   = $("#filt_month2").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/marketing/ajax/ajaxreport_itembyteam.php?p=SearchData",
        type: "POST",
        data: {
            filt_year    : filt_year,
            filt_month1  : filt_month1,
            filt_month2  : filt_month2,
            SortType: SortType
        },
        success: function(result) {
            $(".overlay").hide();
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                var Rows = parseFloat(inval['Rows']);
                var tBody = "";
                var VisOrder = 1;

                if(Rows == 0) {
                    tBody += "<tr><td class='text-center' colspan='14'>ไม่พบผลลัพธ์ที่ค้นหา :(</td></tr>";
                } else {
                    for(i = 0; i < Rows; i++) {
                        
                        tBody +=
                            "<tr>"+
                                "<td class='text-right'>"+VisOrder+"</td>"+
                                "<td class='text-center'>"+inval[i]['ItemCode']+"</td>"+
                                "<td>"+inval[i]['ItemName']+"</td>"+
                                "<td class='text-center'>"+inval[i]['U_ProductStatus']+"</td>"+
                                "<td>"+inval[i]['SalUnitMsr']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty_MT1']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty_MT2']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty_TT1']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty_TT2']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty_OUL']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty_ONL']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty_EXP']+"</td>"+
                                "<td class='text-right'>"+inval[i]['Qty_KBI']+"</td>"+
                                "<td class='text-right font-weight-bold'>"+inval[i]['Qty_All']+"</td>"+
                            "</tr>";
                        VisOrder++;
                    }
                }
                $("#ItemByTeamData tbody").html(tBody);
            });
        }
    })
}

function ExportData(SortType) {
    var filt_year     = $("#filt_year").val();
    var filt_month1   = $("#filt_month1").val();
    var filt_month2   = $("#filt_month2").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/marketing/ajax/ajaxreport_itembyteam.php?p=ExportData",
        type: "POST",
        data: {
            filt_year    : filt_year,
            filt_month1  : filt_month1,
            filt_month2  : filt_month2,
            SortType: SortType
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                if(inval['Rows'] != 3) {
                    window.open("../../FileExport/ItemByTeam/"+inval['FileName'],'_blank');
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

/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
$(document).ready(function(){
    CallHead();
});

$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#ItemByTeamData tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});
</script> 
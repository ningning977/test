<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
    $today      = date("d");
?>
<style type="text/css">
    input[id^='Rmk'] {
        font-size: 12px;
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
                <div class="row mt-4">
                    <div class="col-lg-1 col-5">
                        <div class="form-group">
                            <label for="filt_year">เลือกปี</label>
                            <select class="form-select form-select-sm" name="filt_year" id="filt_year">
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
                            <label for="filt_month">เลือกเดือน</label>
                            <select class="form-select form-select-sm" name="filt_month" id="filt_month">
                            <?php
                                for($m = 1; $m <= 12; $m++) {
                                    if($m == $this_month) {
                                        $m_slct = " selected";
                                    } else {
                                        $m_slct = "";
                                    }
                                    echo "<option value='$m'$m_slct>".FullMonth($m)."</option>";
                                }
                                $DeptCode = $_SESSION['DeptCode'];
                                if(($DeptCode == "DP001" || $DeptCode == "DP002" || $DeptCode == "DP011")) {
                                    $opt_dis = NULL;
                                } else {
                                    $opt_dis = " disabled";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="offset-lg-6 col-lg-3 col-6">
                        <div class="form-group">
                            <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i>  ค้นหา:</label>
                            <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรุณากรอกเพื่อค้นหา..." />
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" style="font-size: 12px;" id="BillList">
                        <thead class="text-center"></thead>
                        <tbody></tbody>
                    </table>
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

function GetList() {
    var filt_year  = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var DeptCode   = '<?php echo $_SESSION['DeptCode']; ?>';
    var thead      = "";


    switch(DeptCode) {
        case "DP009":
            thead +=
                "<tr>"+
                    "<th width='6.5%' rowspan='2'>เลขที่บิล</th>"+
                    "<th width='5%'   rowspan='2'>วันที่บิล</th>"+
                    "<th width='10%'  rowspan='2'>ชื่อลูกค้า</th>"+
                    "<th width='5.5%' rowspan='2'>จำนวนเงิน</th>"+
                    "<th width='5%'   rowspan='2'>เครดิต</th>"+
                    "<th width='10%'  rowspan='2'>พนง.ขาย</th>"+
                    "<th width='10%'  rowspan='2'>หมายเหตุ</th>"+
                    "<th width='6.5%' rowspan='2'>พนง.เปิดบิล</th>"+
                    "<th              colspan='2'><i class='fas fa-file-invoice fa-fw fa-1x'></i> ธุรการคลัง</th>"+
                    "<th              colspan='2'><i class='fas fa-landmark fa-fw fa-1x'></i> บัญชี</th>"+
                "</tr>"+
                "<tr>"+
                    "<th width='5%'>วันที่<br/>ส่งบิล</th>"+
                    "<th width='7.5%'>หมายเหตุ</th>"+
                    "<th width='5%'>วันที่<br/>รับบิล</th>"+
                    "<th width='7.5%'>หมายเหตุ</th>"+
                "</tr>";
        break;
        case "DP011":
            thead +=
                "<tr>"+
                    "<th width='6.5%' rowspan='2'>เลขที่บิล</th>"+
                    "<th width='5%'   rowspan='2'>วันที่บิล</th>"+
                    "<th width='10%'  rowspan='2'>ชื่อลูกค้า</th>"+
                    "<th width='5.5%' rowspan='2'>จำนวนเงิน</th>"+
                    "<th width='6.5%' rowspan='2'>พนง.เปิดบิล</th>"+
                    "<th              colspan='3'><i class='fas fa-truck fa-fw fa-1x'></i> ขนส่ง</th>"+
                    "<th              colspan='2'><i class='fas fa-file-invoice fa-fw fa-1x'></i> ธุรการคลัง</th>"+
                    "<th              colspan='2'><i class='fas fa-landmark fa-fw fa-1x'></i> บัญชี</th>"+
                "</tr>"+
                "<tr>"+
                    "<th width='5%'>วันที่<br/>โหลดสินค้า</th>"+
                    "<th width='5%'>วันที่<br/>คืนบิล</th>"+
                    "<th width='6.5%'>พนง.</th>"+
                    "<th width='5%'>วันที่<br/>ส่งบิล</th>"+
                    "<th width='7.5%'>หมายเหตุ</th>"+
                    "<th width='5%'>วันที่<br/>รับบิล</th>"+
                    "<th width='7.5%'>หมายเหตุ</th>"+
                "</tr>";
        break;
        default:
        thead +=
                "<tr>"+
                    "<th width='6.5%' rowspan='2'>เลขที่บิล</th>"+
                    "<th width='5%'   rowspan='2'>วันที่บิล</th>"+
                    "<th width='10%'  rowspan='2'>ชื่อลูกค้า</th>"+
                    "<th width='5.5%' rowspan='2'>จำนวนเงิน</th>"+
                    "<th width='5%'   rowspan='2'>เครดิต</th>"+
                    "<th width='10%'  rowspan='2'>พนง.ขาย</th>"+
                    "<th width='10%'  rowspan='2'>หมายเหตุ</th>"+
                    "<th width='6.5%' rowspan='2'>พนง.เปิดบิล</th>"+
                    "<th              colspan='3'><i class='fas fa-truck fa-fw fa-1x'></i> ขนส่ง</th>"+
                    "<th              colspan='2'><i class='fas fa-file-invoice fa-fw fa-1x'></i> ธุรการคลัง</th>"+
                    "<th              colspan='2'><i class='fas fa-landmark fa-fw fa-1x'></i> บัญชี</th>"+
                "</tr>"+
                "<tr>"+
                    "<th width='5%'>วันที่<br/>โหลดสินค้า</th>"+
                    "<th width='5%'>วันที่<br/>คืนบิล</th>"+
                    "<th width='6.5%'>พนง.</th>"+
                    "<th width='5%'>วันที่<br/>ส่งบิล</th>"+
                    "<th width='7.5%'>หมายเหตุ</th>"+
                    "<th width='5%'>วันที่<br/>รับบิล</th>"+
                    "<th width='7.5%'>หมายเหตุ</th>"+
                "</tr>";
        break;

    }
    $.ajax({
        url: "menus/general/ajax/ajaxsenddocDL.php?p=GetList",
        type: "POST",
        data: {
            y: filt_year,
            m: filt_month
        },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                var Rows = inval['Rows'];
                var tbody = "";
                for(i=0; i<Rows; i++) {
                    var DateDiff = parseFloat(inval['BD_'+i]['DateDiff']);
                    var ChkLoad = ChkLogi = ChkWhse = ChkAcnt = LoadDate = LogiDate = WhseDate = AcntDate = LogiName = "";

                    if(inval['BD_'+i]['ChkLoad'] == 1) { ChkLoad = "checked"; LoadDate = inval['BD_'+i]['LoadDate']; }
                    if(inval['BD_'+i]['ChkLogi'] == 1) { ChkLogi = "checked"; LogiDate = inval['BD_'+i]['LogiDate']; LogiName = inval['BD_'+i]['LogiName']; }
                    if(inval['BD_'+i]['ChkWhse'] == 1) { ChkWhse = "checked"; WhseDate = inval['BD_'+i]['WhseDate']; }
                    if(inval['BD_'+i]['ChkAcnt'] == 1) { ChkAcnt = "checked"; AcntDate = inval['BD_'+i]['AcntDate']; }

                    switch(inval['BD_'+i]['GroupNum']) {
                        case "-1":
                        case "5":
                        case "17":
                        case "19":
                        case "20":
                        case "21":
                            if(DateDiff >= 3) {
                                tbody += "<tr class='table-danger text-danger'>";
                            } else {
                                tbody += "<tr>";
                            }
                        break;
                        default: 
                            if(DateDiff >= 7) {
                                tbody += "<tr class='table-danger text-danger'>";
                            } else {
                                tbody += "<tr>";
                            }
                        break;
                    }
                    
                    switch(DeptCode) {
                        case "DP009":
                        tbody +=
                            "<td class='align-top text-center'>"+inval['BD_'+i]['NumAtCard']+"</td>"+
                            "<td class='align-top text-center'>"+inval['BD_'+i]['DocDate']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['CardCode']+"</td>"+
                            "<td class='align-top text-right'>"+inval['BD_'+i]['DocTotal']+"</td>"+
                            "<td class='align-top text-center'>"+inval['BD_'+i]['PymntGroup']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['SlpName']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['Comments']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['OwnerName']+"</td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkWhse_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkWhse+" disabled><br/>"+WhseDate+"</td>"+
                            "<td class='align-top'><input type='text' class='form-control' id='RmkWhse_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' value='"+inval['BD_'+i]['RmkWhse']+"' readonly></td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkAcnt_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkAcnt+"><br/>"+AcntDate+"</td>"+
                            "<td class='align-top'><input type='text' class='form-control' id='RmkAcnt_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' value='"+inval['BD_'+i]['RmkAcnt']+"'></td>";
                        break;
                        case "DP011":
                        tbody +=
                        "<td class='align-top text-center'>"+inval['BD_'+i]['NumAtCard']+"</td>"+
                            "<td class='align-top text-center'>"+inval['BD_'+i]['DocDate']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['CardCode']+"</td>"+
                            "<td class='align-top text-right'>"+inval['BD_'+i]['DocTotal']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['OwnerName']+"</td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkLoad_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkLoad+" disabled><br/>"+LoadDate+"</td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkLogi_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkLogi+" disabled><br/>"+LogiDate+"</td>"+
                            "<td class='align-top'>"+LogiName+"</td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkWhse_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkWhse+"><br/>"+WhseDate+"</td>"+
                            "<td class='align-top'><input type='text' class='form-control' id='RmkWhse_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' value='"+inval['BD_'+i]['RmkWhse']+"'></td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkAcnt_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkAcnt+" disabled><br/>"+AcntDate+"</td>"+
                            "<td class='align-top'><input type='text' class='form-control' id='RmkAcnt_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' value='"+inval['BD_'+i]['RmkAcnt']+"' readonly></td>";
                        break;
                        default:
                        tbody +=
                            "<td class='align-top text-center'>"+inval['BD_'+i]['NumAtCard']+"</td>"+
                            "<td class='align-top text-center'>"+inval['BD_'+i]['DocDate']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['CardCode']+"</td>"+
                            "<td class='align-top text-right'>"+inval['BD_'+i]['DocTotal']+"</td>"+
                            "<td class='align-top text-center'>"+inval['BD_'+i]['PymntGroup']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['SlpName']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['Comments']+"</td>"+
                            "<td class='align-top'>"+inval['BD_'+i]['OwnerName']+"</td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkLoad_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkLoad+" disabled><br/>"+LoadDate+"</td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkLogi_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkLogi+" disabled><br/>"+LogiDate+"</td>"+
                            "<td class='align-top'>"+LogiName+"</td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkWhse_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkWhse+"><br/>"+WhseDate+"</td>"+
                            "<td class='align-top'><input type='text' class='form-control' id='RmkWhse_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' value='"+inval['BD_'+i]['RmkWhse']+"'></td>"+
                            "<td class='align-top text-center'><input type='checkbox' id='ChkAcnt_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' "+ChkAcnt+"><br/>"+AcntDate+"</td>"+
                            "<td class='align-top'><input type='text' class='form-control' id='RmkAcnt_"+inval['BD_'+i]['DocEntry']+"' data-DocEntry='"+inval['BD_'+i]['DocEntry']+"' value='"+inval['BD_'+i]['RmkAcnt']+"'></td>";
                 
                        break;
                    }
                    tbody += "</tr>";

                    

                }
                
                $("#BillList thead").html(thead);
                $("#BillList tbody").html(tbody);
            });

            $("input[type='checkbox'][id^='Chk']").on("click",function(){
                var TagID    = $(this).attr("id").split("_");
                var Status   = $(this).is(":checked");
                var Approve  = 0;
                var Prefix   = TagID[0];
                var DocEntry = TagID[1];
                var Access   = 1;
                // var Access   = 0;
                var alert_body = "";

                // switch(DeptCode) {
                //     case "DP009":
                //         switch(Prefix) {
                //             case "ChkAcnt":
                //                 if($("#ChkWhse_"+DocEntry).is(":checked") == true) {
                //                     Access = 1;
                //                 } else {
                //                     Access = 0;
                //                     alert_body = "บิลนี้ยังไม่ได้ส่งมาจากคลังสินค้า";
                //                 }
                //             break;
                //         }
                //     break;
                //     case "DP011":
                //         switch(Prefix) {
                //             case "ChkWhse":
                //                 if($("#ChkLoad_"+DocEntry).is(":checked") == true) {
                //                     Access = 1;
                //                 } else {
                //                     Access = 0;
                //                     alert_body = "บิลนี้ยังไม่ได้รับคืนจากขนส่ง";
                //                 }
                //             break;
                //         }
                //     break;
                //     default:
                //         switch(Prefix) {
                //             case "ChkWhse":
                //                 if($("#ChkLoad_"+DocEntry).is(":checked") == true) {
                //                     Access = 1;
                //                 } else {
                //                     Access = 0;
                //                     alert_body = "บิลนี้ยังไม่ได้รับคืนจากขนส่ง";
                //                 }
                //             break;
                //             case "ChkAcnt":
                //                 if($("#ChkLoad_"+DocEntry).is(":checked") == true && $("#ChkWhse_"+DocEntry).is(":checked") == true) {
                //                     Access = 1;
                //                 } else {
                //                     Access = 0;
                //                     alert_body = "บิลนี้ยังไม่ได้ส่งมาจากคลังสินค้า";
                //                 }
                //             break;
                //         }
                //     break;
                // }

                if(Access == 0) {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html(alert_body);
                    $("#alert_modal").modal('show');
                    $(this).prop("checked",false);
                } else {
                    if(Status == true) {
                        Approve = 1;
                    }

                    $(".overlay").show();
                    $.ajax({
                        url: "menus/general/ajax/ajaxsenddocDL.php?p=Approve",
                        type: "POST",
                        data: {
                            Type: Prefix,
                            DocEntry: DocEntry,
                            App: Approve
                        },
                        success: function(result) {
                            $(".overlay").hide(); 
                            GetList();
                        }
                    });
                }
            });

            $("input[id^='Rmk']").on("focusout",function() {
                var TagID    = $(this).attr("id").split("_");
                var Prefix   = TagID[0];
                var DocEntry = TagID[1];
                var Content  = $(this).val();
                if(Content.length > 0) {
                    $(".overlay").show();
                    $.ajax({
                        url: "menus/general/ajax/ajaxsenddocDL.php?p=Remark",
                        type: "POST",
                        data: {
                            Type: Prefix,
                            DocEntry: DocEntry,
                            Content: Content
                        },
                        success: function(result) {
                            $(".overlay").hide(); 
                        }
                    });
                }
            });
        }
    });
}
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */

$(document).ready(function(){
    CallHead();
    GetList();
});

$("#filt_year, #filt_month").on("change",function() {
    GetList();
});

$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#BillList tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});
</script> 
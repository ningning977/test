<div class="card h-100">
    <div class="card-header">
        <h4><i class="fas fa-file-signature fa-fw fa-1x"></i> ระบบอนุมัติเอกสาร</h4>
    </div>
    <?php
    switch($_SESSION['DeptCode']) {
        case "DP002":
        case "DP005":
        case "DP006":
        case "DP007":
        case "DP008":
            $dis_quota = NULL;
            break;
        case "DP003":
            switch($_SESSION['LvCode']) {
                case "LV010":
                case "LV011":
                case "LV012":
                case "LV013":
                case "LV103":
                case "LV104":
                case "LV105":
                case "LV106":
                    $dis_quota = NULL;
                    break;
                default: $dis_quota = " disabled"; break;
            }
            break;
        default:
            $dis_quota = " disabled";
            break;
    }
    ?>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <nav>
                    <div class="nav nav-tabs" id="ApproveTab" role="tablist">
                        <button onclick="CallAppTab(1)" class="d-flex align-items-center nav-link text-primary active" id="tab_AppSO" data-bs-toggle="tab" data-bs-target="#AppSO" type="button" role="tab" aria-controls="AppSO" aria-selected="true">ใบสั่งขาย</button>
                        <button onclick="CallAppTab(2)" class="d-flex align-items-center nav-link text-primary" id="tab_AppWO" data-bs-toggle="tab" data-bs-target="#AppWO" type="button" role="tab" aria-controls="AppWO" aria-selected="false">ใบฝากงาน</button>
                        <button onclick="CallAppTab(3)" class="d-flex align-items-center nav-link text-primary" id="tab_AppMemo" data-bs-toggle="tab" data-bs-target="#AppMemo" type="button" role="tab" aria-controls="AppMemo" aria-selected="false">บันทึกภายใน</button>
                        <button onclick="CallAppTab(4)" class="d-flex align-items-center nav-link text-primary" id="tab_AppSA04" data-bs-toggle="tab" data-bs-target="#AppSA04" type="button" role="tab" aria-controls="AppSA04" aria-selected="false">ส่วนลดหนี้/ลดจ่าย (SA-04)</button>
                        <button onclick="CallAppTab(5)" class="d-flex align-items-center nav-link text-primary<?php echo $dis_quota; ?>" id="tab_AppQuota" data-bs-toggle="tab" data-bs-target="#AppQuota" type="button" role="tab" aria-controls="AppQuota" aria-selected="false" <?php echo $dis_quota; ?>>คลังสินค้าจอง</button>
                    </div>
                </nav>
                <div class="tab-content mt-3" id="nav-tabContent">
                    <div class="tab-pane show active" id="AppSO" role="tabpanel" aria-labelledby="tab_AppSO">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="font-size: 12px;" id="AppSOList">
                                <thead class="text-center text-light" style='background-color: #9A1118;'>
                                    <tr>
                                        <th>วันที่เอกสาร</th>
                                        <th>กำหนดส่ง</th>
                                        <th>เลขที่ S/O</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th>มูลค่าท้ายบิล</th>
                                        <th>พนักงานขาย</th>
                                        <th>สถานะการอนุมัติ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="7"><i class="fas fa-spinner fa-pulse fa-fw fa-1x"></i> กำลังโหลด...</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center" colspan="7"><a href="?p=app_order" target="_blank"><i class="fas fa-search-plus fa-fw fa-1x"></i> ดูเอกสารเพิ่มเติม</a></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="AppWO" role="tabpanel" aria-labelledby="tab_AppWO">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="font-size: 12px;" id="AppWOList">
                                <thead class="text-center text-light" style='background-color: #9A1118;'>
                                    <tr>
                                        <th width="7.5%">วันที่ฝากงาน</th>
                                        <th width="10%">เลขที่ฝากงาน</th>
                                        <th width="7.5%">วันที่นัดหมาย</th>
                                        <th width="7.5%">รายละเอียด</th>
                                        <th>ลูกค้า / ผู้ติดต่อ</th>
                                        <th width="12.5%">ฝ่าย</th>
                                        <th width="7.5%">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="7"><i class="fas fa-spinner fa-pulse fa-fw fa-1x"></i> กำลังโหลด...</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center" colspan="7"><a href="?p=app_WO" target="_blank"><i class="fas fa-search-plus fa-fw fa-1x"></i> ดูเอกสารเพิ่มเติม</a></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="AppMemo" role="tabpanel" aria-labelledby="tab_AppMemo">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="font-size: 12px;" id="AppMemoList">
                                <thead class="text-center text-light" style='background-color: #9A1118;'>
                                    <tr>
                                        <th width="3.5%">ลำดับ</th>
                                        <th width="7%">วันที่เอกสาร</th>
                                        <th width="10%">เลขที่เอกสาร</th>
                                        <th>หัวข้อ</th>
                                        <th width="12.5%">ฝ่าย</th>
                                        <th width="7.5%">สถานะเอกสาร</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="6"><i class="fas fa-spinner fa-pulse fa-fw fa-1x"></i> กำลังโหลด...</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center" colspan="6"><a href="?p=app_memo" target="_blank"><i class="fas fa-search-plus fa-fw fa-1x"></i> ดูเอกสารเพิ่มเติม</a></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="AppSA04" role="tabpanel" aria-labelledby="tab_AppSA04">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="font-size: 12px;" id="AppSA04List">
                                <thead class="text-center text-light" style='background-color: #9A1118;'>
                                    <tr>
                                        <th width="3.5%">ลำดับ</th>
                                        <th width="7.5%">วันที่เอกสาร</th>
                                        <th width="10%">เลขที่เอกสาร</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th width="15%">พนักงานขาย</th>
                                        <th width="10%">ผู้จัดทำ</th>
                                        <th width="7.5%">เอกสารอ้างอิง</th>
                                        <th width="7.5%">ยอดลดหนี้ /<br/>ลดจ่ายสุทธิ</th>
                                        <th width="7.5%">สถานะเอกสาร</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="9"><i class="fas fa-spinner fa-pulse fa-fw fa-1x"></i> กำลังโหลด...</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center" colspan="9"><a href="?p=app_sa04" target="_blank"><i class="fas fa-search-plus fa-fw fa-1x"></i> ดูเอกสารเพิ่มเติม</a></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="AppQuota" role="tabpanel" aria-labelledby="tab_AppQuota">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="font-size: 12px;" id="AppQuotaList">
                                <thead class="text-center text-light" style='background-color: #9A1118;'>
                                    <tr>
                                        <th width="5%">วันที่เอกสาร</th>
                                        <th width="10%">เลขที่เอกสาร</th>
                                        <th>รายละเอียดสินค้า</th>
                                        <th width="7.5%">โอนย้ายจาก</th>
                                        <th width="7.5%">ไปยัง</th>
                                        <th width="5%">จำนวน</th>
                                        <th width="10%">ผู้ขอ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="8"><i class="fas fa-spinner fa-pulse fa-fw fa-1x"></i> กำลังโหลด...</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center" colspan="8"><a href="?p=instock" target="_blank"><i class="fas fa-search-plus fa-fw fa-1x"></i> ดูเอกสารเพิ่มเติม</a></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDataItem" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " id='ModalSize'>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search-plus" style='font-size: 15px;'></i> ข้อมูลสินค้าคงคลัง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name='ItemCode' id='ItemCode' >
                <div class="row">
                    <div class="col-lg">
                        <div class="table-responsive">
                            <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden'>
                                <tbody style='font-size: 13px;' id='Tbody1'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr class='m-2' style='color: rgba(244, 67, 54, 0.25);'>
                <div class="row pt-3">
                    <div class="col-lg">
                        <span class='fw-bolder pb-1'>จำนวนสินค้าคงคลังในระบบ SAP</span>
                        <div class="table-responsive pt-1" id='Table2'>
                        </div>
                    </div>
                </div>
                <hr class='m-2' style='color: rgba(244, 67, 54, 0.25);'>
                <div class="row pt-3">
                    <div class="col-lg">
                        <div class='fw-bolder pb-1 d-flex justify-content-between ' id='Chk_KB4'>
                            <div>โอนย้ายสินค้าคลังจอง</div>
                        </div>
                        <div class="table-responsive pt-1">
                            <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden'>
                                <thead style='font-size: 13px;'>
                                    <tr class='text-center'>
                                        <th>คลังสินค้า</th>
                                        <th>จำนวนปัจจุบัน</th>
                                        <th>เพิ่ม</th>
                                        <th>ลบ</th>
                                        <th>จำนวนใหม่</th>
                                    </tr>
                                </thead>
                                <tbody style='font-size: 12px;' id='Table3'></tbody>
                            </table>

                            <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden' id='Table4'>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalAlert" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="ModalAlert-head"></h5>
                <p id="ModalAlert-body" class="my-4"></p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        CallAppTab(1);
        CountBadge();
    });

    try{ document.createEvent("TouchEvent"); var isMobile = true; }
    catch(e){ var isMobile = false; }

    function SOAppOrder() {
        window.open("?p=app_order")
    }

    function PreviewMM() {
        window.open("?p=app_memo");
    }

    function PreviewDoc() {
        window.open("?p=app_WO")
    }

    function PreviewSA04() {
        window.open("?p=add_sa04");
    }

    function CountBadge() {
        let Url    = [  'menus/general/ajax/ajaxapp_order.php?a=read&tab=ChkRow',
                        'menus/general/ajax/ajaxapp_WO.php?p=AppList&tab=ChkRow',
                        'menus/general/ajax/ajaxapp_memo.php?p=MemoList&tab=ChkRow',
                        'menus/sale/ajax/ajaxapp_sa04.php?p=DocList&tab=ChkRow',
                        'dashboard/ajax/ajaxAllBox.php?a=WhsQuota&tab=ChkRow'
                     ];
        let Tabs   = [  'ใบสั่งขาย', 'ใบฝากงาน', 'บันทึกภายใน', 'ส่วนลดหนี้/ลดจ่าย (SA-04)', 'คลังสินค้าจอง' ];
        let IdTabs = [  'tab_AppSO', 'tab_AppWO', 'tab_AppMemo', 'tab_AppSA04', 'tab_AppQuota' ];
        for(let i = 0; i < IdTabs.length; i++) {
            $.ajax({
                url: Url[i],
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        if(inval['Rows'] != 0) {
                            $("#"+IdTabs[i]).html(Tabs[i]+"&nbsp;<span class='badge bg-primary'>"+inval['Rows']+"</span>");
                        }else{
                            $("#"+IdTabs[i]).html(Tabs[i]);
                        }
                    });
                }
            })
        }
    }


    function CallAppTab(tab) {
        switch(tab) {
            case 1:
                // ajax App Order
                $("#AppSOList tbody").html("<tr><td class=\"text-center\" colspan=\"7\"><i class=\"fas fa-spinner fa-pulse fa-fw fa-1x\"></i> กำลังโหลด...</td></tr>");
                $.ajax({
                    url: "menus/general/ajax/ajaxapp_order.php?a=read&tab=Y",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
                    type: "POST",
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#AppSOList tbody").html(inval["output"]);
                        });
                    }
                });
                break;
            case 2:
                $("#AppWOList tbody").html("<tr><td class=\"text-center\" colspan=\"7\"><i class=\"fas fa-spinner fa-pulse fa-fw fa-1x\"></i> กำลังโหลด...</td></tr>");
                $.ajax({
                    url: "menus/general/ajax/ajaxapp_WO.php?p=AppList&tab=Y",
                    type: "POST",
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj, function(key,inval) {
                            $(".overlay").hide();
                            $("#AppWOList tbody").html(inval['OrderList']);
                        });
                    }
                });

                break;
            case 3:
                $("#AppMemoList tbody").html("<tr><td class=\"text-center\" colspan=\"6\"><i class=\"fas fa-spinner fa-pulse fa-fw fa-1x\"></i> กำลังโหลด...</td></tr>");
                $.ajax({
                    url: "menus/general/ajax/ajaxapp_memo.php?p=MemoList&tab=Y",
                    type: "POST",
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#AppMemoList tbody").html(inval['MemoList']);
                        });
                    }
                });
                break;
            case 4:
                $("#AppSA04List tbody").html("<tr><td class=\"text-center\" colspan=\"9\"><i class=\"fas fa-spinner fa-pulse fa-fw fa-1x\"></i> กำลังโหลด...</td></tr>");
                $.ajax({
                    url: "menus/sale/ajax/ajaxapp_sa04.php?p=DocList&m=main",
                    type: "POST",
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#AppSA04List tbody").html(inval['DocList']);
                        });
                    }
                });
                break;
            case 5:
                $("#AppQuotaList tbody").html("<tr><td class=\"text-center\" colspan=\"9\"><i class=\"fas fa-spinner fa-pulse fa-fw fa-1x\"></i> กำลังโหลด...</td></tr>");
                $.ajax({
                    url: "dashboard/ajax/ajaxAllBox.php?a=WhsQuota",
                    type: "POST",
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#AppQuotaList tbody").html(inval['output']);
                        });
                    }
                });
                break;
        }
    }

    function DataDetail(DataItem) {
        switch(isMobile) {
            case true: var ModalSize = "modal-full"; break;
            case false: var ModalSize = "modal-xl"; break;
            default: var ModalSize = "modal-xl"; break;
        }
        $(".overlay").show();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=DataDetail",
            type: "POST",
            data: { ItemCode : DataItem, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#Tbody1").html(inval['output1']);
                    $("#Table2").html(inval['output2']);
                    $("#Table3").html(inval['output3']);
                    $("#Chk_KB4").html(inval['output3_kb4']);
                    $("#Table4").html(inval['output4']);

                    $("#ItemCode").val(inval['ItemCode']);
                    // console.log(inval['ItemCode']);

                    $("#ModalSize").addClass(ModalSize);
                    $("#ModalDataItem").modal("show");
                })
                $(".overlay").hide();
            }
        })
    }

    function CHKdata(x,y) {
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=CHKdata",
            type: "POST",
            data: { Fun : x,
                    CH : y,

                    Now_ALL : $('#Now_All').val(),
                    Add_ALL : $('#Add_All').val(),
                    Red_ALL : $('#Red_All').val(),
                    New_ALL : $('#New_All').val(), 

                    Now_TTC : $('#Now_TTC').val(), 
                    Add_TTC : $('#Add_TTC').val(), 
                    Red_TTC : $('#Red_TTC').val(), 
                    New_TTC : $('#New_TTC').val(), 

                    Now_MT1 : $('#Now_MT1').val(), 
                    Add_MT1 : $('#Add_MT1').val(), 
                    Red_MT1 : $('#Red_MT1').val(), 
                    New_MT1 : $('#New_MT1').val(), 

                    Now_MT2 : $('#Now_MT2').val(), 
                    Add_MT2 : $('#Add_MT2').val(), 
                    Red_MT2 : $('#Red_MT2').val(), 
                    New_MT2 : $('#New_MT2').val(), 

                    Now_OUL : $('#Now_OUL').val(), 
                    Add_OUL : $('#Add_OUL').val(), 
                    Red_OUL : $('#Red_OUL').val(), 
                    New_OUL : $('#New_OUL').val(), 

                    Now_ONL : $('#Now_ONL').val(), 
                    Add_ONL : $('#Add_ONL').val(), 
                    Red_ONL : $('#Red_ONL').val(), 
                    New_ONL : $('#New_ONL').val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#Add_'+inval['CH']).val(inval['Add']);
                    $('#Red_'+inval['CH']).val(inval['Red']);
                    $('#New_'+inval['CH']).val(inval['New']);

                    $('#Final_Add').html(inval['TotalAdd']);
                    $('#Final_Red').html(inval['TotalRed']);
                    $('#Final_New').html(inval['TotalNew']);
                })
            } 
        })
    }

    function SaveApp(x) {
        // console.log(x);
        let WhsCode = $("#WhsCaseKB4").val();
        let SaleTime = $("#SaleTime").val();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=SaveApp",
            type: "POST",
            data: { Pos : x,
                    ItemCode : $("#ItemCode").val(),
                    App : $('#Mgr'+x+'App').val(),
                    Remark : $('#Mgr'+x+'Remark').val(),
                    WhsCode : WhsCode,
                    SaleTime : SaleTime,

                    Now_ALL : $('#Now_All').val(),
                    Add_ALL : $('#Add_All').val(),
                    Red_ALL : $('#Red_All').val(),
                    New_ALL : $('#New_All').val(), 

                    Now_TTC : $('#Now_TTC').val(), 
                    Add_TTC : $('#Add_TTC').val(), 
                    Red_TTC : $('#Red_TTC').val(), 
                    New_TTC : $('#New_TTC').val(), 

                    Now_MT1 : $('#Now_MT1').val(), 
                    Add_MT1 : $('#Add_MT1').val(), 
                    Red_MT1 : $('#Red_MT1').val(), 
                    New_MT1 : $('#New_MT1').val(), 

                    Now_MT2 : $('#Now_MT2').val(), 
                    Add_MT2 : $('#Add_MT2').val(), 
                    Red_MT2 : $('#Red_MT2').val(), 
                    New_MT2 : $('#New_MT2').val(), 

                    Now_OUL : $('#Now_OUL').val(), 
                    Add_OUL : $('#Add_OUL').val(), 
                    Red_OUL : $('#Red_OUL').val(), 
                    New_OUL : $('#New_OUL').val(), 

                    Now_ONL : $('#Now_ONL').val(), 
                    Add_ONL : $('#Add_ONL').val(), 
                    Red_ONL : $('#Red_ONL').val(), 
                    New_ONL : $('#New_ONL').val(), },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#ModalAlert-head").html(inval['Halert']);
                    $("#ModalAlert-body").html(inval['alert']);
                    $("#ModalAlert").modal("show");
                    DataDetail($("#ItemCode").val());
                    setTimeout(function() { 
                        CallAppTab(5);
                        CountBadge();
                    }, 1000);
                })
            } 
        })
    }

    function WhsCaseKB4(ItemCode) {
        const WhsCode = $("#WhsCaseKB4").val();
        // console.log(ItemCode, WhsCode);
        $.ajax({
            url: "menus/warehouse/ajax/ajaxinstock.php?a=WhsCaseKB4",
            type: "POST",
            data: { ItemCode : ItemCode, WhsCode : WhsCode, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#Table3").html(inval['output3']);
                })
            }
        })
    }

    function Cancel(DocNum) {
        $("#confirm_modal").modal("show");
        $("#confirm_modal p.defult").html("คุณต้องการยกเลิกการโอนย้ายสินค้า ?");
        $(document).off("click","#btn-Cancel").on("click","#btn-confirm", function() {
            $("#confirm_modal").modal("hide");
            $.ajax({
                url: "menus/warehouse/ajax/ajaxinstock.php?a=Cancel",
                type: "POST",
                data : { DocNum: DocNum },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#alert_header").html("<i class=\"fas fa-check-circle fa-lg text-primary\" style='font-size: 70px;''></i>");
                        $("#alert_body").html("ยกเลิกการโอนย้ายสินค้าสำเร็จ");
                        $("#alert_modal").modal('show');
                    });
                }
            });
        });
    }

</script>
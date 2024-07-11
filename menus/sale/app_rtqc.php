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

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-4">
                    <table class="table table-sm table-hover table-bordered" id="ApproveList" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th class="text-center border-top" width="3.5%">ลำดับ</th>
                                <th class="text-center border-top" width="6.5%">วันที่<br/>เอกสาร</th>
                                <th class="text-center border-top" width="12.5%">ประเภท<br/>เอกสาร</th>
                                <th class="text-center border-top" width="7.5%">เลขที่<br/>เอกสาร</th>
                                <th class="text-center border-top">ชื่อลูกค้า</th>
                                <th class="text-center border-top" width="7.5%">เลขที่<br/>ใบมา</th>
                                <th class="text-center border-top" width="10%">เอกสาร<br/>อ้างอิง</th>
                                <th class="text-center border-top" width="17.5%"><span class='badge bg-dark'>ทีม</span> พนักงานขาย</th>
                                <th class="text-center border-top" width="7.5%">สถานะเอกสาร</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL PREVIEW DOC -->
<div class="modal fade" id="ModalPreviewDoc" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt fa-fw fa-1x"></i> รายละเอียดการคืน QC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered table-sm" style="font-size: 12px;">
                            <tbody>
                                <tr>
                                    <th width="10%">เลขที่เอกสาร</th>
                                    <td width="30%" id="prev_DocNum"></td>
                                    <th width="10%">เลขที่ใบมา</th>
                                    <td width="20%" id="prev_RefDoc1"></td>
                                    <th width="10%">วันที่เอกสาร</th>
                                    <td width="20%" id="prev_DocDate"></td>
                                </tr>
                                <tr>
                                    <th>ประเภทการคืน</th>
                                    <td id="prev_DocType"></td>
                                    <th>เอกสารอ้างอิง</th>
                                    <td colspan="3" id="prev_BillDocNum"></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th>ชื่อลูกค้า</th>
                                    <td class="text-success" id="prev_BillCardCode"></td>
                                    <th>วันที่เปิดบิล</th>
                                    <td class="text-success" id="prev_BillDocDate"></td>
                                    <th>วันที่กำหนดชำระ</th>
                                    <td class="text-success" id="prev_BillDocDueDate"></td>
                                </tr>
                                <tr>
                                    <th>ชื่อพนักงานขาย</th>
                                    <td class="align-top text-success" id="prev_BillSlpName"></td>
                                    <th>ชื่อธุรการขาย</th>
                                    <td class="align-top text-success" id="prev_BillOwnName"></td>
                                    <th>เอกสารแนบ</th>
                                    <td id="prev_Attach"></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th>ค่าปรับเซลส์</th>
                                    <td colspan="5" id="prev_FineSALE"></td>
                                </tr>
                                <tr>
                                    <th>ค่าปรับ Co-Sales</th>
                                    <td colspan="5" id="prev_FineCOSA"></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th>สาเหตุการคืน</th>
                                    <td id="prev_ReturnReason" class="text-danger" style="font-weight: bold;"></td>
                                    <th>ค่า Incentive</th>
                                    <td id="prev_Incentive"></td>
                                    <th>เป็นของแถมหรือไม่</th>
                                    <td id="prev_Freebie"></td>
                                </tr>
                                <tr>
                                    <th>รูปแบบการส่งคืน</th>
                                    <td colspan="5" id="prev_SendType"></td>
                                </tr>
                                <tr>
                                    <th>ค่าขนส่ง</th>
                                    <td colspan="5" id="prev_ShipCost"></td>
                                </tr>
                                <tr>
                                    <th>หมายเหตุ</th>
                                    <td colspan="5" id="prev_Remark" class="text-danger" style="font-weight: bold;"></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                        <h6><i class="fas fa-list fa-fw fa-1x"></i> รายการสินค้า</h6>
                        <div class="alert alert-secondary text-center" role="alert">
                            <p>
                                <strong>เกรดสินค้า</strong><br/>
                                QC = สินค้าใหม่ขายได้ || A = สินค้าสภาพดี/ไม่มีกล่อง || AB = สินค้ามีตำหนิ || AX = สินค้าชำรุดสภาพดี || BX = สินค้าชำรุดมาก
                            </p>
                        </div>
                        <table class="table table-bordered table-hover table-sm" id="prev_ItemList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="10%">รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th width="5%">สถานะ</th>
                                    <th width="7.5%">ราคา<br/>ก่อนส่วนลด</th>
                                    <th width="15%">ส่วนลด (%)</th>
                                    <th width="7.5%">ราคา<br/>หลังส่วนลด</th>
                                    <th width="7.5%">จำนวน</th>
                                    <th width="5%">หน่วย</th>
                                    <th width="7.5%">คลังสินค้า<br/>ที่เปิดบิล</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr/>
                        <h6><i class="fas fa-tasks fa-fw fa-1x"></i> สถานะการอนุมัติ</h6>
                        <table class="table table-bordered table-hover table-sm" id="prev_ApproveList" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="20%">ผู้อนุมัติ</th>
                                    <th width="15%">ผลการพิจารณา</th>
                                    <th>หมายเหตุ</th>
                                    <th width="20%">ผู้อนุมัติ</th>
                                    <th width="15%">วันที่อนุมัติ</th>
                                    <th width="5%"><i class="fas fa-save fa-fw fa-lg"></i></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="prev_footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalViewAttach" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-images fa-fw fa-1x"></i> รายการภาพถ่ายสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" id="ImgAttach">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SAVE SUCCESS -->
<div class="modal fade" id="confirm_saved" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-check-circle fa-fw fa-lg text-success"></i> สำเร็จ</h5>
                <p id="confirm_body" class="my-4">บันทึกข้อมูลสำเร็จ</p>
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

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

function GetAppList() {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxreturn_qc.php?p=AppList",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                let tBody = "";
                if(inval['ROW'] == 0) {
                    tBody += "<tr><td colspan='9' class='text-center'>ไม่มีข้อมูล :)</td></tr>";
                } else {
                    
                    for(i = 0; i < inval['ROW']; i++) {
                        tBody +=
                            "<tr>"+
                                "<td class='text-right'>"+inval['BODY'][i]['no']+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['DocDate']+"</td>"+
                                "<td>"+inval['BODY'][i]['DocType']+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['DocNum']+"</td>"+
                                "<td>"+inval['BODY'][i]['BillCardCode']+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['RefDocNum']+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['BillDocNum']+"</td>"+
                                "<td>"+inval['BODY'][i]['BillSlpName']+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['txt_status']+"</td>"+
                            "</tr>";
                    }
                }
                
                $("#ApproveList tbody").html(tBody);
            });
            $(".overlay").hide();
        }
    })
}

function PreviewDoc(DocEntry,int_status) {
    $.ajax({
        url: "menus/sale/ajax/ajaxreturn_qc.php?p=PreviewDoc",
        type: "POST",
        data: { DocEntry: DocEntry, int_status: int_status },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key, inval) {
                if(inval['HEAD']['Row'] == 0) {
                    $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                    $("#alert_body").html("ไม่พบข้อมูลการคืน QC กรุณาลองใหม่อีกครั้ง");
                    $("#alert_modal").modal('show');
                } else {
                    /* HEADER */
                    $("#prev_DocNum").html(inval['HEAD']['DocNum']);
                    $("#prev_RefDoc1").html(inval['HEAD']['RefDoc1']);
                    $("#prev_DocDate").html(inval['HEAD']['DocDate']);
                    $("#prev_DocType").html(inval['HEAD']['DocType']);
                    $("#prev_BillDocNum").html(inval['HEAD']['BillDocNum']);
                    $("#prev_BillCardCode").html(inval['HEAD']['BillCardCode']);
                    $("#prev_BillDocDate").html(inval['HEAD']['BillDocDate']);
                    $("#prev_BillDocDueDate").html(inval['HEAD']['BillDocDueDate']);
                    $("#prev_BillSlpName").html(inval['HEAD']['BillSlpName']);
                    $("#prev_BillOwnName").html(inval['HEAD']['BillOwnName']);
                    $("#prev_Attach").html(inval['HEAD']['Attach']);
                    $("#prev_ReturnReason").html(inval['HEAD']['ReturnReason']);
                    $("#prev_Incentive").html(inval['HEAD']['Incentive']);
                    $("#prev_Freebie").html(inval['HEAD']['Freebie']);
                    $("#prev_Remark").html(inval['HEAD']['Remark']);
                    $("#prev_SendType").html(inval['HEAD']['SendType']);
                    $("#prev_ShipCost").html(inval['HEAD']['ShipCost']);
                    $("#prev_FineSALE").html(inval['HEAD']['FineSALE']);
                    $("#prev_FineCOSA").html(inval['HEAD']['FineCOSA']);

                    /* ITEMLIST */
                    let ItemBody = "";
                    let ItemNo = 1;
                    for(i = 0; i < inval['HEAD']['Row']; i++) {
                        ItemBody +=
                            "<tr>"+
                                "<td class='text-right'>"+ItemNo+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['ItemCode']+"</td>"+
                                "<td>"+inval['BODY'][i]['ItemName']+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['ItemStatus']+"</td>"+
                                "<td class='text-right'>"+inval['BODY'][i]['GrandPrice']+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['Discount']+"</td>"+
                                "<td class='text-right'>"+inval['BODY'][i]['UnitPrice']+"</td>"+
                                "<td class='text-right'>"+inval['BODY'][i]['Quantity']+"</td>"+
                                "<td>"+inval['BODY'][i]['UnitMsr']+"</td>"+
                                "<td class='text-center'>"+inval['BODY'][i]['WhsCode']+"</td>"+
                            "</tr>";
                        ItemNo++;
                    }
                    $("table#prev_ItemList tbody").html(ItemBody);

                    /* APPROVE */
                    let AppBody = "";
                    let AppNo = 1;
                    for(i = 0; i < inval['APPROVE']['ROW']; i++) {
                        let AppState = "";
                        switch(inval['APPROVE'][i]['AppState']) {
                            case "0": AppState = "<span class='text-muted'><i class='far fa-times fa-fw fa-lg'></i> ไม่ต้องอนุมัติ</span>"; break;
                            case "1": AppState = "<span class='text-muted'><i class='far fa-clock fa-fw fa-lg'></i> รอพิจารณา</span>"; break;
                            case "Y": AppState = "<span class='text-success'><i class='far fa-check-circle fa-fw fa-lg'></i> อนุมัติ</span>"; break;
                            case "N": AppState = "<span class='text-danger'><i class='far fa-times-circle fa-fw fa-lg'></i> ไม่อนุมัติ</span>"; break;
                        }
                        if(inval['APPROVE'][i]['APP'] == "Y") {
                            AppBody +=
                                "<tr>"+
                                    "<td class='text-right'>"+AppNo+"</td>"+
                                    "<td>"+inval['APPROVE'][i]['NameReq']+"</td>"+
                                    "<td>"+
                                        "<select class='form-select form-select-sm' name='AppState_"+inval['APPROVE'][i]['ApproveID']+"' id='AppState_"+inval['APPROVE'][i]['ApproveID']+"'>"+
                                            "<option selected disabled>กรุณาเลือก</option>"+
                                            "<option value='Y'>อนุมัติ</option>"+
                                            "<option value='N'>ไม่อนุมัติ</option>"+
                                        "</select>"+
                                    "</td>"+
                                    "<td><input type='text' class='form-control form-control-sm' name='AppRemark_"+inval['APPROVE'][i]['ApproveID']+"' id='AppRemark_"+inval['APPROVE'][i]['ApproveID']+"' /></td>"+
                                    "<td>&nbsp;</td>"+
                                    "<td>&nbsp;</td>"+
                                    "<td><button type='button' class='btn btn-primary btn-sm w-100' onclick='AppSave("+inval['APPROVE'][i]['ApproveID']+")'><i class='fas fa-save fa-fw fa-1x'></i></button></td>"+
                                "</tr>";

                        } else {
                            AppBody +=
                                "<tr>"+
                                    "<td class='text-right'>"+AppNo+"</td>"+
                                    "<td>"+inval['APPROVE'][i]['NameReq']+"</td>"+
                                    "<td>"+AppState+"</td>"+
                                    "<td>"+inval['APPROVE'][i]['AppRemark']+"</td>"+
                                    "<td>"+inval['APPROVE'][i]['NameAct']+"</td>"+
                                    "<td class='text-center'>"+inval['APPROVE'][i]['AppDate']+"</td>"+
                                    "<td>&nbsp;</td>"+
                                "</tr>";
                        }
                        
                        AppNo++;
                    }
                    $("table#prev_ApproveList tbody").html(AppBody);

                    /* Attachment */
                    let Attach = "";
                    for(i = 0; i < inval['ATTACH']['ROW']; i++) {
                        Attach += 
                            "<figure class='figure'>"+
                                "<figcaption class='figure-caption'>"+inval['ATTACH'][i]['FileOriName']+"</figcaption>"+
                                "<img class='figure-img img-thumbnail w-100 rounded' src='../FileAttach/RTQC/"+inval['ATTACH'][i]['FileDirName']+"'>"+
                            "</figure>";
                    }
                    $("#ImgAttach").html(Attach);

                    $("#ModalPreviewDoc").modal("show");
                }
                $("#prev_footer").html(inval['FOOT']);
            });
        }
    });
}

function ViewAttach(DocEntry) {
    $.ajax({
        url: "menus/sale/ajax/ajaxreturn_qc.php?p=ViewAttach",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            $("#ModalViewAttach").modal("show"); 
        }
    })
    
}

function CancelDoc(DocEntry) {
    $("#confirm_cancel").modal("show");
    $("#btn-cancel-confirm").on("click",function(e) {
        $.ajax({
            url: "menus/sale/ajax/ajaxreturn_qc.php?p=CancelDoc",
            type: "POST",
            data: { DocEntry: DocEntry },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    if(inval['Status'] == "SUCCESS") {
                        window.location.reload();
                    }
                })
            }
        })
    });
}

function AppSave(AppID) {
    let AppState  = $("#AppState_"+AppID).val();
    let AppRemark = $("#AppRemark_"+AppID).val();
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxreturn_qc.php?p=AppSave",
        type: "POST",
        data: {
            AppState: AppState,
            AppRemark: AppRemark,
            ApproveID: AppID
        },
        success: function(result) {
            $(".overlay").hide();
            $("#confirm_saved").modal('show');
            $("#btn-save-reload").on("click", function(e){
                e.preventDefault();
                window.location.reload();
            });
        }
    })
}

$(document).ready(function(){
    CallHead();
    GetAppList();
});
</script> 
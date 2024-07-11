<?php
    $start_year = 2023;
    $this_year  = date("Y");
    $this_month = date("m");
?>
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
                            ?>
                            </select>
                        </div>
                    </div>
                    <?php
                        $DeptCode = $_SESSION['DeptCode'];
                        $opt_ALL = " disabled";
                        $opt_MT1 = " disabled";
                        $opt_EXP = " disabled";
                        $opt_MT2 = " disabled";
                        $opt_TT2 = " disabled";
                        $opt_DMN = " disabled";
                        $opt_TT1 = " disabled";
                        $opt_OUL = " disabled";
                        $opt_ONL = " disabled";
                        switch($DeptCode) {
                            case "DP006": 
                                $opt_MT1 = " selected";
                                $opt_EXP = NULL;
                            break;
                            case "DP007":
                                $opt_MT2 = " selected";
                            break;
                            case "DP005":
                                $opt_TT2 = " selected";
                                $opt_DMN = NULL;
                            break;
                            case "DP008":
                                $opt_TT1 = " selected";
                                $opt_OUL = NULL;
                            break;
                            default:
                                $opt_ALL = NULL;
                                $opt_MT1 = NULL;
                                $opt_EXP = NULL;
                                $opt_MT2 = NULL;
                                $opt_TT2 = NULL;
                                $opt_DMN = NULL;
                                $opt_TT1 = NULL;
                                $opt_OUL = NULL;
                                $opt_ONL = NULL;
                            break;
                        }
                    ?>
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="filt_team">เลือกทีมขาย</label>
                            <select class="form-select form-select-sm" name="filt_team" id="filt_team">
                                <option value="ALL"<?php echo $opt_ALL; ?>>ทุกทีม</option>
                                <option value="MT1"<?php echo $opt_MT1; ?>><?php echo SATeamName("MT1"); ?></option>
                                <option value="EXP"<?php echo $opt_EXP; ?>><?php echo SATeamName("EXP"); ?></option>
                                <option value="MT2"<?php echo $opt_MT2; ?>><?php echo SATeamName("MT2"); ?></option>
                                <option value="TT2"<?php echo $opt_TT2; ?>><?php echo SATeamName("TT2"); ?></option>
                                <option value="DMN"<?php echo $opt_DMN; ?> disabled><?php echo SATeamName("DMN"); ?></option>
                                <option value="TT1"<?php echo $opt_TT1; ?>><?php echo SATeamName("TT1"); ?></option>
                                <option value="OUL"<?php echo $opt_OUL; ?>><?php echo SATeamName("OUL"); ?></option>
                                <option value="ONL"<?php echo $opt_ONL; ?>><?php echo SATeamName("ONL"); ?></option>
                                <option value="KBI"><?php echo SATeamName("KBI"); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="min-height: 512px;">
                    <table class="table table-sm table-hover table-bordered" id="QcRtListTable" style="font-size: 12px;">
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
                                <th class="text-center border-top" width="5%"><i class="fas fa-cog fa-fw fa-1x"></i></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div id="QcRtTable"></div>
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

<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>
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

    function GetDocList(filt_year,filt_month,filt_team) {
        $(".overlay").show();
        $("#QcRtListTable").dataTable().fnClearTable();
        $("#QcRtListTable").dataTable().fnDraw();
        $("#QcRtListTable").dataTable().fnDestroy();
        $("#QcRtListTable").DataTable({
            "ajax": {
                url: "menus/product/ajax/ajaxrtqc.php?p=GetDocList",
                type: "POST",
                data: {filt_y: filt_year, filt_m: filt_month, filt_t: filt_team},
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "no", class: "text-right border-start border-bottom" },
                { "data": "DocDate", class: "text-center border-start border-bottom" },
                { "data": "DocType", class: "border-start border-bottom" },
                { "data": "DocNum", class: "text-center border-start border-bottom" },
                { "data": "BillCardCode", class: "border-start border-bottom" },
                { "data": "RefDocNum", class: "text-center border-start border-bottom" },
                { "data": "BillDocNum", class: "text-center border-start border-bottom" },
                { "data": "BillSlpName", class: "border-start border-bottom" },
                { "data": "txt_status", class: "text-center border-start border-bottom" },
                { "data": "txt_opt", class: "text-center border-start border-bottom" },
            ],
            createdRow: (row, data, dataIndex, cells) => {
                switch(data.RecptStatus) {
                    case "1": $(row).addClass("text-warning table-warning"); break;
                    case "Y": $(row).addClass("text-success table-success"); break;
                    case "N": $(row).addClass("text-danger table-danger"); break;
                }
            },
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 20,
        });
        $(".overlay").hide();
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
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
$(document).ready(function(){
    CallHead();

    let filt_year  = $("#filt_year").val();
    let filt_month = $("#filt_month").val();
    let filt_team  = $("#filt_team").val();
    GetDocList(filt_year,filt_month,filt_team)
});
</script> 
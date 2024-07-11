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
                <ul class="nav nav-tabs" id="main-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#AddReturn" class="btn-tabs nav-link active" id="AddReturn-tab" data-bs-toggle="tab" data-bs-target="#AddReturn" role="tab" data-tabs="0" aria-controls="AddReturn" aria-selected="false">
                            <i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มโควต้าจากการรับคืน QC
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#AddItem" class="btn-tabs nav-link" id="AddItem-tab" data-bs-toggle="tab" data-bs-target="#AddItem" role="tab" data-tabs="1" aria-controls="AddItem" aria-selected="true">
                            <i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มโควต้าเฉพาะรายการ
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="AddReturn" tole="tabpanel" aria-labelledby="AddReturn-tab">
                        <div class="row mt-4">
                            <div class="col-2">
                                <label for="filt_searchbox">ค้นหาเลขที่เอกสาร:</label>
                                <input type="text" class="form-control form-control-sm" name="filt_searchbox" id="filt_searchbox" placeholder="ค้นหาเลขที่เอกสารใน SAP หรือเลขที่เอกสาร QC" />
                            </div>
                            <div class="col-1">
                                <label for="filt_searchbtn">&nbsp;</label>
                                <button class="btn btn-primary btn-sm w-100" onclick="GetDocData();"><i class="fas fa-search fa-fw fa-1x"></i></button>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <table class="table table-sm table-bordered" style="font-size: 13px;">
                                    <tbody>
                                        <tr>
                                            <th width="10%">เลขที่เอกสาร</th>
                                            <td width="60%" id="HD_DocNum"></td>
                                            <th width="10%">เอกสาร QC</th>
                                            <td width="20%" id="HD_RefDocNum"></td>
                                        </tr>
                                        <tr>
                                            <th>ชื่อลูกค้า</th>
                                            <td id="HD_CusName"></td>
                                            <th>พนักงานขาย</th>
                                            <td id="HD_SlpName"></td>
                                        </tr>
                                        <tr>
                                            <th>หมายเหตุ</th>
                                            <td id="HD_Remark"></td>
                                            <th>วันที่เอกสาร</th>
                                            <td id="HD_DocDate"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr/>
                                <table id="DocItem" class="table table-sm table-bordered" style="font-size: 13px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="3%">No.</th>
                                            <th width="10%">รหัสสินค้า</th>
                                            <th>ชื่อสินค้า</th>
                                            <th width="5%">คลัง</th>
                                            <th width="5%">จำนวน</th>
                                            <th width="5%">หน่วย</th>
                                            <th width="10%">โควต้า</th>
                                            <th width="5%">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center">กรุณาค้นหาเอกสาร :)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="AddItem" role="tabpanel" aria-labelledby="AddItem-tab">
                        <div class="row mt-4">
                            <div class="col-auto">
                                <div class="form-group" style='width: 350px;'>
                                    <label for="ItemCode">เลือกสินค้า</label>
                                    <select class='form-control form-control-sm' name="ItemCode" id="ItemCode" data-live-search="true">
                                        <option value='' selected disabled>เลือกสินค้า</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group">
                                    <label for=""></label>
                                    <button class='btn btn-sm btn-primary w-100' onclick="CallData();"><i class="fas fa-search"></i> ค้นหา</button>
                                </div>
                            </div>
                        </div>

                        <div class="row ShowData d-none pt-2">
                            <div class="col">
                                <span class='fw-bolder pb-1'>จำนวนสินค้าคงคลังในระบบ SAP</span>
                                <div class="table-responsive pt-1">
                                    <table class='table table-sm table-bordered' style='font-size: 13px;' id='Table1'>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr class='m-2 ShowData d-none' style='color: rgba(244, 67, 54, 0.25);'>
                        <div class="row ShowData d-none">
                            <div class="col">
                                <div class='fw-bolder pb-1 d-flex justify-content-between ' id='Chk_KB4'>
                                    <div>โอนย้ายสินค้าคลังจอง</div>
                                </div>
                                <div class="table-responsive pt-1">
                                    <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden' style='font-size: 12px;'>
                                        <thead>
                                            <tr class='text-center'>
                                                <th>คลังสินค้า</th>
                                                <th>จำนวนปัจจุบัน</th>
                                                <th>เพิ่ม</th>
                                                <th>ลบ</th>
                                                <th>จำนวนใหม่</th>
                                            </tr>
                                        </thead>
                                        <tbody id='Table3'></tbody>
                                    </table>

                                    <table class='table table-sm table-bordered rounded rounded-3 overflow-hidden' id='Table4'>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

<script type="text/javascript">
    function number_format(number,decimal) {
        var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
        var formatter = new Intl.NumberFormat("en",options);
        return formatter.format(number)
    }

    function CallHead() {
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

    function GetDocData() {
        let filt_box = $("#filt_searchbox").val();
        if(filt_box.length == 0 || filt_box == "" || filt_box == " ") {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
            $("#alert_modal").modal('show');
        } else {
            $.ajax({
                url: "menus/warehouse/ajax/ajaxReturnQuota.php?a=GetDocData",
                type: "POST",
                data: {
                    box: filt_box
                },
                success: function(result) {
                    let tBody = "";
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key, inval) {
                        let Row = parseFloat(inval['Row']);
                        let no  = 1;
                        $("#HD_DocNum").html(inval['HD']['DocNum']);
                        $("#HD_RefDocNum").html(inval['HD']['RefDocNum']);
                        $("#HD_CusName").html(inval['HD']['CardCode']+' '+inval['HD']['CardName']);
                        $("#HD_SlpName").html(inval['HD']['SlpName']);
                        $("#HD_Remark").html(inval['HD']['Comments']);
                        $("#HD_DocDate").html(inval['HD']['DocDate']);

                        for(r = 0; r < Row; r++) {
                            let Opt_MT1 = Opt_MT2 = Opt_TTC = Opt_OUL = Opt_ONL = "";
                            let RowCls = BtnDis = "";
                            switch(inval['BD'][r]['CH']) {
                                case "MT1": Opt_MT1 = " selected"; break;
                                case "MT2": Opt_MT2 = " selected"; break;
                                case "TTC": Opt_TTC = " selected"; break;
                                case "ONL": Opt_ONL = " selected"; break;
                                case "OUL": Opt_OUL = " selected"; break;
                            }
                            if(inval['BD'][r]['DONE'] == "Y") {
                                BtnDis = " disabled";
                                RowCls = " class='table-success text-success'";
                            }
                            tBody +=
                                "<tr data-row='"+r+"'"+RowCls+">"+
                                    "<td class='text-right'>"+number_format(no,0)+"</td>"+
                                    "<td class='text-center'>"+inval['BD'][r]['ItemCode']+"</td>"+
                                    "<td>"+inval['BD'][r]['Dscription']+"</td>"+
                                    "<td class='text-center'>"+inval['BD'][r]['WhsCode']+"</td>"+
                                    "<td class='text-right'>"+number_format(inval['BD'][r]['Quantity'],0)+"</td>"+
                                    "<td class='text-center'>"+inval['BD'][r]['unitMsr']+"</td>"+
                                    "<td>"+
                                        "<select class='form-select form-select-sm' id='CH_"+r+"'"+BtnDis+">"+
                                            "<option disabled selected>กรุณาเลือก</option>"+
                                            "<option value='MT1'"+Opt_MT1+">MT1</option>"+
                                            "<option value='MT2'"+Opt_MT2+">MT2</option>"+
                                            "<option value='TTC'"+Opt_TTC+">TT2 (ตจว.)</option>"+
                                            "<option value='OUL'"+Opt_OUL+">หน้าร้าน + TT1 (กทม.)</option>"+
                                            "<option value='ONL'"+Opt_ONL+">ออนไลน์</option>"+
                                        "</select>"+
                                    "</td>"+
                                    "<td><button type='button' class='btn btn-primary btn-sm w-100' id='Btn_"+r+"' onclick=\"AddQuota("+inval['HD']['DocEntry']+","+inval['BD'][r]['LineNum']+","+r+");\""+BtnDis+"><i class='fas fa-save fa-fw fa-1x'></i></button></td>"
                                "</tr>";
                            no++;
                        }

                        $("#DocItem tbody").html(tBody);
                    });
                }
            });
        }
    }

    
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */

$(document).ready(function(){
    CallHead();
});

$.ajax({
    url: "../json/OITM.json",
    cache: false,
    success: function(result) {
        var filt_data = 
            result.
                filter(x => x.ItemStatus == "A").
                sort(function(key, inval) {
                    return key.ItemCode.localeCompare(inval.ItemCode);
                });
        var opt = "";
        $.each(filt_data, function(key, inval) {
            opt += "<option value='"+inval.ItemCode+"'>"+inval.ItemCode+" | ["+inval.ProductStatus+"] - "+inval.ItemName+" ["+inval.BarCode+"]</option>";
        });

        $("#ItemCode").append(opt).selectpicker();
    }
});

function CallData() {
    const ItemCode = $("#ItemCode").val();
    if(ItemCode != null) {
        $(".overlay").show();
        $.ajax({
            url: "menus/warehouse/ajax/ajaxReturnQuota.php?a=CallData",
            type: "POST",
            data: { ItemCode : ItemCode, },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $(".ShowData").removeClass("d-none");
                    $("#Table1 tbody").html(inval['output1']);
                    $("#Table3").html(inval['output3']);
                    $("#Chk_KB4").html(inval['output3_kb4']);
                    $("#Table4").html(inval['output4']);
                });
                $(".overlay").hide();
            }
        })
    }else{
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกสินค้าก่อน");
        $("#alert_modal").modal('show');
    }
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

function AddQuota(DocEntry, LineNum, LineRow) {
    let CH = $("#CH_"+LineRow).val();
    if(CH == "") {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณาเลือกทีมก่อนเพิ่มโควต้า");
        $("#alert_modal").modal('show');
    } else {
        $.ajax({
            url: "menus/warehouse/ajax/ajaxReturnQuota.php?a=AddQuota",
            type: "POST",
            data: {
                DocEntry: DocEntry,
                LineNum : LineNum,
                CH: CH,
                SaleTime: 0
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    let Status = inval['Status'];
                    if(Status == "SUCCESS") {
                        $("tr[data-row='"+LineRow+"']").addClass("table-success text-success");
                        $("#Btn_"+LineRow).attr("disabled",true);
                    } else {
                        let ErrType = Status.split("::");
                        let modal_body = "";
                        switch(ErrType[1]) {
                            case "NORESULT":     modal_body = "ไม่พบเอกสารในระบบ SAP"; break;
                            case "CANNOTINSERT": modal_body = "ไม่สามารถเพิ่มข้อมูลได้"; break;
                        }

                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html(modal_body);
                        $("#alert_modal").modal('show');
                    }
                });
            }
        });
    }
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
    $.ajax({
        url: "menus/warehouse/ajax/ajaxinstock.php?a=SaveApp",
        type: "POST",
        data: { Pos : x,
                ItemCode : $("#ItemCode").val(),
                App : $('#Mgr'+x+'App').val(),
                Remark : $('#Mgr'+x+'Remark').val(),

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
                CallData();
            })
        } 
    })
}

</script> 
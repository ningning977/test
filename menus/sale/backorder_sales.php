<style type="text/css">
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
            overflow-X: auto;
            height: 630px;
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
<?php
switch ($_SESSION['DeptCode']){
    case 'DP001' :
    case 'DP002' :
    case 'DP004' :
        $opt0 = " selected ";
        $opt1 = " ";
        $opt2 = " ";
        $opt3 = " ";
        $opt4 = " ";
        $opt5 = " ";
        break;
    case 'DP003' :
        if ($_SESSION['LvCode'] == 'LV010' || $_SESSION['LvCode'] == 'LV011' || $_SESSION['LvCode'] == 'LV012' || $_SESSION['LvCode'] == 'LV013'){
            $opt0 = " selected ";
            $opt1 = " ";
            $opt2 = " ";
            $opt3 = " ";
            $opt4 = " ";
            $opt5 = " ";
        }else{
            $opt0 = " disabled ";
            $opt1 = " disabled ";
            $opt2 = " disabled ";
            $opt3 = " disabled ";
            $opt4 = " disabled ";
            $opt5 = " selected ";
        }
        break;
    case 'DP005' :
        $opt0 = " disabled ";
        $opt1 = " disabled ";
        $opt2 = " disabled ";
        $opt3 = " selected ";
        $opt4 = " disabled ";
        $opt5 = " disabled ";
        break;
    case 'DP006' :
        $opt0 = " disabled ";
        $opt1 = " selected ";
        $opt2 = " disabled ";
        $opt3 = " disabled ";
        $opt4 = " disabled ";
        $opt5 = " disabled ";
        break;
    case 'DP007' :
        $opt0 = " disabled ";
        $opt1 = " disabled ";
        $opt2 = " selected ";
        $opt3 = " disabled ";
        $opt4 = " disabled ";
        $opt5 = " disabled ";
        break;
    case 'DP008' :
        $opt0 = " disabled ";
        $opt1 = " disabled ";
        $opt2 = " disabled ";
        $opt3 = " disabled ";
        $opt4 = " selected ";
        $opt5 = " disabled ";
        break;
    

}


?>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for="TeamSale">เลือกทีม</label>
                            <select class='form-select form-select-sm' name="TeamSale" id="TeamSale" onchange='CallData();'>
                                <option value="'MT1','MT2','TT2','OUL','TT1','ONL'" <?php echo $opt0;?>>เลือกทุกทีม</option>
                                <option value="'MT1'" <?php echo $opt1;?>>ทีม MT1</option>
                                <option value="'MT2'" <?php echo $opt2;?>>ทีม MT2</option>
                                <option value="'TT2'" <?php echo $opt3;?>>ทีม TT2</option>
                                <option value="'OUL','TT1'" <?php echo $opt4;?>>ทีม OUL + TT1</option>
                                <option value="'ONL'" <?php echo $opt5;?>>ทีม ONL</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-gruop">
                            <label for=""></label>
                            <button class='btn btn-sm btn-success w-100' onclick='Excel();'><i class="fas fa-file-excel fa-fw"></i> Excel</button>
                        </div>
                    </div>
                </div>
                <br>
                <table width = '100%' class="table-responsive" >
                    <tr>
                        <td>
                        <span style='font-weight: bold;'>*หมายเหตุ : </span> วันที่พร้อมขาย/เข้าล่าสุด  <br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. <span class='text-danger' style='font-weight: bold;'>วันที่สีแดง</span>  คือ วันที่พร้อมขาย ถ้ามีกำหนดสินค้าเข้า จากรายงานสถานะเลื่อนส่ง<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. <span class='text-success' style='font-weight: bold;'>วันที่สิเขียว</span>  คือ วันที่รับเข้าล่าสุด ถ้าไม่มีกำหนดการรับสินค้าเข้า <br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. <span class='text-wai' style='font-weight: bold;'>ของเก่าคงค้าง</span>  คือ ไม่มีการซื้อเข้าตั้งแต่ ปี 2023 เป็นต้นไป <br>
                        <span class='text-primary fw-bolder' >*ฝ่ายควรพิจารณาจัดการ Back Order ทุกสิ้นเดือนหรือต้นเดือน</span>
                        </td>
                        <td class='text-right align-bottom'>
                                <label><i class="fas fa-filter text-primary" style="font-size: 19px;" aria-hidden="true"></i>&nbsp;&nbsp;<input id='myInput' type="search" class=" dataTable-input" placeholder="" aria-controls="Table1"></label>            
                        </td>
                    </tr>
                </table>
                

                

                <div class="row pt-2">
                    <div class="col">
                        <div class="table-responsive tableFix">
                            <table class='table table-sm table-hover table-bordered ' style='font-size: 12px;' id='Table1'>
                                <thead class='bg-white'>
                                    <tr>
                                        <th width='7%' class='text-center'>เลขที่เอกสาร</th>
                                        <th width='5%' class='text-center'>วันที่เอกสาร</th>
                                        <th width='5%' class='text-center'>กำหนดส่ง</th>
                                        <th width='15%' class='text-center'>ชื่อลูกค้า</th>
                                        <th width='8%' class='text-center'>เลขที่ PO</th>
                                        <th width='20%' class='text-center'>ชื่อสินค้า</th>
                                        <th width='3%' class='text-center'>สถานะ</th>
                                        <th width='3%' class='text-center'>คงคลัง</th>
                                        <th width='3%' class='text-center'>ค้างส่ง (หน่วย)</th>
                                        <th width='4%' class='text-center'>ราคา / หน่วย</th>
                                        <th width='5%' class='text-center'>ราคารวม (บาท)</th>
                                        <th width='13%' class='text-center'>พนักงานขาย</th>
                                        <th width='5%' class='text-center'>วันที่<br>พร้อมขาย/<br>เข้าล่าสุด</th>
                                        <th class='text-center'>หมายเหตุ<br><span class='text-primary'>(กรุณาแตะที่ว่างเพื่อบันทึก)</span></th>
                                    </tr>
                                </thead>
                                <tbody id='myTable'>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="../../js/extensions/apexcharts.js"></script>
<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
	});
</script> 
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

$(document).ready(function(){
    CallData();
});

function CallData() {
    const TeamSale = $("#TeamSale").val();
    $.ajax({
        url: "menus/sale/ajax/ajaxbackorder_sales.php?a=CallData",
        type: "POST",
        data: { TeamSale : TeamSale, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#Table1 tbody").html(inval['Data']);
            });

            $("textarea.remark").on("focusout", function(e) {
                let TextRemark = $(this).val();
                let RemarkData = $(this).attr("dataRemark").split("::");
                let DocEntry   = RemarkData[0];
                let ItemCode    = RemarkData[1];

                SaveRemark(DocEntry,ItemCode,TextRemark)
            })
        }
    });
}

function SaveRemark(DocEntry,ItemCode,TextRemark) {
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxbackorder_sales.php?a=SaveRemark",
        type: "POST",
        data: { DocEntry : DocEntry, ItemCode : ItemCode, Remark : TextRemark, },
        success: function(result) {
            const obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                
            });
            $(".overlay").hide();
        }
    });
}

function Excel() {
    const TeamSale = $("#TeamSale").val();
    $(".overlay").show();
    $.ajax({
        url: "menus/sale/ajax/ajaxbackorder_sales.php?a=Excel",
        type: "POST",
        data: { TeamSale : TeamSale, },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $(".overlay").hide();
                window.open("../../FileExport/BackOrderSales/"+inval['FileName'],'_blank');
            });
        }
    })
}

</script> 
<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
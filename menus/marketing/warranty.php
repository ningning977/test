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
<div class="modal fade" id="ModalData" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " id='ModalSize'>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-search-plus" style='font-size: 15px;'></i> ข้อมูลผู้ลงทะเบียน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover table-sm" id="CusList" style="font-size: 12px; color: #000;">
                            <thead class="text-center">
                                <tr>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>เบอร์โทรศัพท์</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                 </table>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
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
                    <div class="col-lg-1 col-5">
                        <div class="form-group">
                            <label for="filt_year">เลือกปี</label>
                            <select name="filt_year" id="filt_year" class="form-select form-select-sm">
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
                            <select name="filt_month" id="filt_month" class="form-select form-select-sm">
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
                    <div class="col-lg-1 col-4">
                        <div class="form-group">
                            <label for="btn_search">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" id="btn_search" onclick="SearchBox();"><i class="fas fa-search fa-fw fa-1x"></i></button>
                        </div>
                    </div>
                    <?php if($_SESSION['DeptCode'] == 'DP002') { ?>
                    <div class="col-auto">
                        <div div class="form-group">
                            <label for=""></label>
                            <button type="button" class="btn btn-success btn-sm w-100" onclick="Excel();"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <ul class="nav nav-tabs mt-4" id="main-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#WrtCard" class="btn-tabs nav-link active" id="order_tab1" data-bs-toggle="tab" data-template="1" data-tab="1" aria-controls="order_status" aria-selected="true" style="font-size: 12px;">
                            <i class="far fa-credit-card"></i></i>ข้อมูลการรับประกัน 
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#WrtReport" class="btn-tabs nav-link" id="order_tab2" data-bs-toggle="tab" data-template="2" data-tab="2" aria-controls="order_status" aria-selected="false" style="font-size: 12px;" disabled>
                            <i class="fas fa-passport"></i> สรุปการรับประกัน
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#WrtTT2" class="btn-tabs nav-link" id="order_tab3" data-bs-toggle="tab" data-template="3" data-tab="3" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-store-alt"></i> สรุปใบรับประกันฝ่าย TT2
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#WrtMT1" class="btn-tabs nav-link" id="order_tab4" data-bs-toggle="tab" data-template="4" data-tab="4" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-store-alt"></i> สรุปใบรับประกันฝ่าย MT1
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#WrtMT2" class="btn-tabs nav-link" id="order_tab5" data-bs-toggle="tab" data-template="5" data-tab="5" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-store-alt"></i> สรุปใบรับประกันฝ่าย MT2
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#WrtOUL" class="btn-tabs nav-link" id="order_tab6" data-bs-toggle="tab" data-template="6" data-tab="6" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-store-alt"></i> สรุปใบรับประกันฝ่าย OUL
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#WrtOTH" class="btn-tabs nav-link" id="order_tab7" data-bs-toggle="tab" data-template="7" data-tab="7" aria-controls="order_status" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-store-alt"></i> สรุปใบรับประกันอื่นๆ
                        </a>
                    </li>
                </ul>
                <div class="tab-pane show active mt-4" id="order_status" role="tabpanel">
                    <div id='UserProfile'></div>
                    <div class="table-responsive">
                        <!----
                        <table class="table table-bordered table-hover table-sm" id="ProfileTable" style="font-size: 12px; color: #000;">
                            <thead class="text-center"></thead>
                            <tbody></tbody>
                        </table>
                        ---->
                        <table class="table table-bordered table-hover table-sm" id="OrderTable" style="font-size: 12px; color: #000;">
                            <thead class="text-center"></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-------- สินสุดเนื้อหา Pages --------->
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
	$(document).ready(function(){
        CallHead();
        CallTab(2);
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
    function CallTab(tabno) {
        $("#main-tabs .nav-item a.btn-tabs#order_tab"+tabno).tab("show").click();
    }
    $("#main-tabs .nav-item a.btn-tabs").on("click",function(e) {
        $(".overlay").show();
        e.preventDefault();
        var Template = $(this).attr("data-template");
        var TabState = $(this).attr("data-tab")
        var theadtmp;
        var ChkYear = $('#filt_year').val();
        var sMonth = $('#filt_month').val();
        switch ($('#filt_month').val()){
		case '1':
		    var ChkMonth = "มกราคม";	
			break;
		case '2':
			var ChkMonth = "กุมภาพันธ์";
			break;
		case '3':
			var ChkMonth = "มีนาคม";	
			break;
		case '4':
			var ChkMonth = "เมษายน";
			break;
		case '5':
			var ChkMonth = "พฤษภาคม";	
			break;
		case '6':
			var ChkMonth= "มิถุนายน";	
			break;
		case '7':
			var ChkMonth = "กรกฎาคม";	
			break;
		case '8':
			var ChkMonth = "สิงหาคม";
			break;
		case '9':
			var ChkMonth = "กันยายน";	
			break;
		case '10':
			var ChkMonth = "ตุลาคม";	
			break;
		case '11':
			var ChkMonth = "พฤศจิกายน";	
			break;
		case '12':
			var ChkMonth = "ธันวาคม";	
			break;
	}


        /* Generate Thead */
        switch(Template) {
            case "1":
                theadtmp =  "<tr>"+
                                "<td>No.</td>"+
                                "<th>วันที่ซื้อ</th>"+
                                "<th>วัตถุประสงค์</th>"+
                                "<th>ร้านค้า</th>"+
                                "<th>ยี้ห้อสินค้า</th>"+
                                "<th>กลุ่มสินค้า</th>"+
                                "<th>รุ่นสินค้า</th>"+
                                "<th>SN</th>"+
                                "<th>วันที่ลงทะเบียน</th>"+
                            "</tr>";
                break;
            case "2":
                theadtmp =  "<tr>"+
                                "<th colspan='5'>สรุปใบรับประกัน เดือน " + ChkMonth +" "+ChkYear+"</th>"+
                            "</tr>"+
                            "<tr>"+
                                "<th width='20%'>ทีม</th>"+
                                "<th width='20%'>เป้า</th>"+
                                "<th width='20%'>PC / พนักงานกรอกข้อมูลให้</th>"+
                                "<th width='20%'>ลูกค้ากรอกข้อมูลด้วยตัวเอง</th>"+
                                "<th width='20%'>ค่าตอบแทน 10 ใบขึ้นไป (บาท)</th>"+
                            "</tr>";
                break;
            case "3":
                theadtmp =  "<tr>"+
                                "<th colspan='7'>สรุปใบรับประกัน TT2 เดือน " + ChkMonth +" "+ChkYear+"</th>"+
                            "</tr>"+
                            "<tr>"+
                                "<th width='2%'>ลำดับ</th>"+
                                "<th width='23%'>ห้าง</th>"+
                                "<th width='10%'>เป้า</th>"+
                                "<th width='20%'>Store</th>"+
                                "<th width='15%'>PC / พนักงานกรอกข้อมูลให้</th>"+
                                "<th width='15%'>ลูกค้ากรอกข้อมูลด้วยตัวเอง</th>"+
                                "<th width='15%'>ค่าตอบแทน 10 ใบขึ้นไป (บาท)</th>"+
                            "</tr>";
                break;
            case "4":
                theadtmp =  "<tr>"+
                                "<th colspan='7'>สรุปใบรับประกัน MT1 เดือน " + ChkMonth +" "+ChkYear+"</th>"+
                            "</tr>"+
                            "<tr>"+
                                "<th width='2%'>ลำดับ</th>"+
                                "<th width='23%'>ห้าง</th>"+
                                "<th width='10%'>เป้า</th>"+
                                "<th width='22%'>Store</th>"+
                                "<th width='15%'>PC / พนักงานกรอกข้อมูลให้</th>"+
                                "<th width='15%'>ลูกค้ากรอกข้อมูลด้วยตัวเอง</th>"+
                                "<th width='15%'>ค่าตอบแทน 10 ใบขึ้นไป (บาท)</th>"+
                            "</tr>";
                break;
            case "5":
                theadtmp =  "<tr>"+
                                "<th colspan='7'>สรุปใบรับประกัน MT2 เดือน " + ChkMonth +" "+ChkYear+"</th>"+
                            "</tr>"+
                            "<tr>"+
                                "<th width='2%'>ลำดับ</th>"+
                                "<th width='23%'>ห้าง</th>"+
                                "<th width='10%'>เป้า</th>"+
                                "<th width='22%'>Store</th>"+
                                "<th width='15%'>PC / พนักงานกรอกข้อมูลให้</th>"+
                                "<th width='15%'>ลูกค้ากรอกข้อมูลด้วยตัวเอง</th>"+
                                "<th width='15%'>ค่าตอบแทน 10 ใบขึ้นไป (บาท)</th>"+
                            "</tr>";
                break;
            case "6":
                theadtmp =  "<tr>"+
                                "<th colspan='7'>สรุปใบรับประกัน OUL เดือน " + ChkMonth +" "+ChkYear+"</th>"+
                            "</tr>"+
                            "<tr>"+
                                "<th width='2%'>ลำดับ</th>"+
                                "<th width='23%'>ห้าง</th>"+
                                "<th width='10%'>เป้า</th>"+
                                "<th width='22%'>Store</th>"+
                                "<th width='15%'>PC / พนักงานกรอกข้อมูลให้</th>"+
                                "<th width='15%'>ลูกค้ากรอกข้อมูลด้วยตัวเอง</th>"+
                                "<th width='15%'>ค่าตอบแทน 10 ใบขึ้นไป (บาท)</th>"+
                            "</tr>";
                break;
            case "7":
                theadtmp =  "<tr>"+
                                "<th colspan='7'>สรุปใบรับประกัน  เดือน " + ChkMonth +" "+ChkYear+"</th>"+
                            "</tr>"+
                            "<tr>"+
                                "<th width='2%'>ลำดับ</th>"+
                                "<th width='23%'>ห้าง</th>"+
                                "<th width='10%'>เป้า</th>"+
                                "<th width='22%'>Store</th>"+
                                "<th width='15%'>PC / พนักงานกรอกข้อมูลให้</th>"+
                                "<th width='15%'>ลูกค้ากรอกข้อมูลด้วยตัวเอง</th>"+
                            "</tr>";
                break;
        }
        var tabno = TabState;
        $.ajax({
            url: "menus/marketing/ajax/ajaxwarranty.php?a=data",
            type: "POST",
            data: {
                tabno: tabno,
                xMonth : sMonth,
                xYear : ChkYear
            },
            success: function(result) {
                $(".overlay").hide();
                $("#OrderTable thead").html(theadtmp);
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    if (tabno == '1'){
                        $('#UserProfile').html(inval['UserProfile']);
                    }else{
                        $('#UserProfile').html("");

                    }
                    $("#OrderTable tbody").html(inval['output']);
                });
               
            }
        });
    });
    function SearchBox(x){
        var NameBox = $('#uName').val();
        var AgeBox = $('#uAge').val();
        var GenderBox = $('#UserGender').val();
        var PhoneBox = $('#uPhone').val();
        var LineBox = $('#uLine').val();
        var CarBox = $('#Carreer').val();
        $.ajax({
            url: "menus/marketing/ajax/ajaxwarranty.php?a=search",
            type: "POST",
            data: {
                uName: NameBox,
                uAge :AgeBox,
                uGen :GenderBox,
                uPhone : PhoneBox,
                uLine :LineBox,
                uCar : CarBox, 
                func : x
            },
            success: function(result) {
                $(".overlay").hide();
                //$("#OrderTable thead").html(theadtmp);
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key,inval) {
                    if (inval['muti'] == 1){
                        $('#uName').val(inval['uName']);
                        $('#uAge').val(inval['uAge']);
                        $('#UserGender').val(inval['uGen']);
                        $('#uPhone').val(inval['uPhone']);
                        $('#uLine').val(inval['uLine']);
                        $("#Carreer option[value="+inval['uGen']+"]").prop('selected', true);
                        $("#OrderTable tbody").html(inval['output']);
                    }else{
                        $("#CusList tbody").html(inval['output']);
                        $('#ModalData').modal('show');
                    }
                    
                });
               
            }
        });
    }
    function AddData(x,y){
        $('#uName').val(x);    
        $('#uPhone').val(y);
        SearchBox(0);
        $('#ModalData').modal('hide');
   }

   function Excel() {
        const Year = $('#filt_year').val();
        const Month = $('#filt_month').val();
        $(".overlay").show();
        $.ajax({
            url: "menus/marketing/ajax/ajaxwarranty.php?a=Excel",
            type: "POST",
            data: { Year: Year, Month: Month },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $(".overlay").hide();
                    window.open("../../FileExport/Warranty/"+inval['FileName'],'_blank');
                });
            }
        })
   }
</script> 
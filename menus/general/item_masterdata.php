<style type="text/css">
    .hrprice {
        border: none;
        border-top: 3px double #333;
        color: #333;
        overflow: visible;
        text-align: center;
        height: 5px;
    }

    .hrprice:after {
        background: #fff;
        content: '$';
        padding: 0 4px;
        position: relative;
        top: -11px;
    }

    #pro .row div.card {
        border: 1px solid #6c757d;
    }

    .card-img-top {
        width: 100%;
        height: 20vw;
        object-fit: contain;
    }
    @media screen and (max-width: 426px) {
        .card-img-top {
            height: 60vw;
        }
    }
    @media screen and (min-width: 426px) and (max-width: 1023px) {
        .card-img-top {
            height: 40vw;
        }
    }
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
                <div class="table-responsive">
                    <input type="hidden" name="ItemCode" id="ItemCode">
                    <table class="table table-sm table-hover table-bordered" id="table1">
                        <thead class='fw-bolder' style="font-size: 12px">
                            <?php
                                if ($_SESSION['uClass'] == 0 || $_SESSION['uClass'] == 1 || $_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4) {
                                    echo"<tr style='font-size: 13px;'>
                                            <td width='8%' class='text-center border-top'>รหัสสินค้า</td>
                                            <td class='text-center border-top'>ชื่อสินค้า</td>
                                            <td class='text-center border-top'>บาร์โค้ด</td>
                                            <td class='text-center border-top'>Stock</td>
                                            <td class='text-center border-top'>ประเภทราคา</td>
                                            <td class='text-center border-top'>ทุน</td>
                                            <td class='text-center border-top'>ราคาตั้ง</td>
                                            <td class='text-center border-top'>ปลีก SEMI</td>
                                            <td class='text-center border-top'>ส่ง SEMI</td>
                                            <td class='text-center border-top'>GP</td>
                                            <td class='text-center border-top'>S1</td>
                                            <td class='text-center border-top'>GP</td>
                                            <td class='text-center border-top'>จำนวน S1</td>
                                            <td class='text-center border-top'>S2</td>
                                            <td class='text-center border-top'>GP</td>
                                            <td class='text-center border-top'>จำนวน S2</td>
                                            <td class='text-center border-top'>S3</td>
                                            <td class='text-center border-top'>GP</td>
                                            <td class='text-center border-top'>จำนวน S3</td>
                                            <td class='text-center border-top'>ผจก Net</td>
                                            <td class='text-center border-top'>GP</td>
                                            <td class='text-center border-top'>ปลิก MT</td>
                                        </tr>";
                                }else{
                                    echo"<tr style='font-size: 13px;'>
                                            <td width='6%' class='text-center border-top'>รหัสสินค้า</td>
                                            <td class='text-center border-top'>ชื่อสินค้า</td>
                                            <td class='text-center border-top'>บาร์โค้ด</td>
                                            <td class='text-center border-top'>Stock</td>
                                            <td class='text-center border-top'>ประเภทราคา</td>
                                            <td class='text-center border-top'>ราคาตั้ง</td>
                                            <td class='text-center border-top'>ปลีก SEMI</td>
                                            <td class='text-center border-top'>ส่ง SEMI</td>
                                            <td class='text-center border-top'>S1</td>
                                            <td class='text-center border-top'>จำนวน S1</td>
                                            <td class='text-center border-top'>S2</td>
                                            <td class='text-center border-top'>จำนวน S2</td>
                                            <td class='text-center border-top'>S3</td>
                                            <td class='text-center border-top'>จำนวน S3</td>
                                        </tr>";
                                }
                            ?>      
                        </thead>
                        <tbody style="font-size: 11.5px;">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL ข้อมูลสินค้า -->
<div class="modal fade" id="ModalItemMaster" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " id='SizeModal'>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="HeaderModal"><i class='fas fa-book-open fa-fw fa-1x'></i> ข้อมูลสินค้า</h5>
                <button type="button" class="btn-close" id="btn-close1" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- MODAL DETAIL PRODUCT -->
                <div class="row">
                    <div class="col-lg-6">
                        <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
                            <div class="carousel-indicators" id="btnImages"></div>
                            <div class="carousel-inner" id="ItemImages"></div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead id="ItemMainH"></thead>
                                <tbody id="ItemMainB"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- END MODAL DETAIL PRODUCT -->

                <!-- MODAL TAB ข้อมูลสินค้า -->
                <div class="row">
                    <div class="col-lg-12">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link disabled" id="feature-tab" data-bs-toggle="tab" data-bs-target="#feature" type="button" role="tab" aria-controls="feature" aria-selected="false">คุณสมบัติ*</button>
                                <button class="nav-link disabled" id="spec-tab" data-bs-toggle="tab" data-bs-target="#spec" type="button" role="tab" aria-controls="spec" aria-selected="false">สเปค*</button>
                                <button class="nav-link active" id="price-tab" data-bs-toggle="tab" data-bs-target="#price" type="button" role="tab" aria-controls="price" aria-selected="false">ราคา</button>
                                <button class="nav-link " id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="false">สินค้าคงคลัง</button>
                                <button class="nav-link disabled" id="refer-tab" data-bs-toggle="tab" data-bs-target="#refer" type="button" role="tab" aria-controls="refer" aria-selected="false">อ้างอิง*</button>
                                <button class="nav-link" id="target-tab" data-bs-toggle="tab" data-bs-target="#target" type="button" role="tab" aria-controls="target" aria-selected="false">เป้าขายที่ตั้งไว้</button>
                                <button class="nav-link" id="pro-tab" data-bs-toggle="tab" data-bs-target="#pro" type="button" role="tab" aria-controls="pro" aria-selected="false">โปรโมชั่น</button>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col-lg">
                        <div class="tab-content" id="nav-tabContent">
                            <!-- TAB คุณสมบัติ -->
                            <div class="tab-pane fade " id="feature" role="tabpanel" aria-labelledby="feature-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="row">
                                            <div class="col-lg d-flex align-items-center">
                                                <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>&nbsp;<span id="">ตู้เชื่อม 2 ระบบ MIG MMA</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg d-flex align-items-center">
                                                <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>&nbsp;<span id="">ชุดสายเชื่อม MIG ถอดได้ ยาว 3 เมตร</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- TAB สเปค -->
                            <div class="tab-pane fade " id="spec" role="tabpanel" aria-labelledby="spec-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead id="ItemSpecH"></thead>
                                                <tbody id="ItemSpecB"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- TAB ราคา -->
                            <div class="tab-pane fade show active" id="price" role="tabpanel" aria-labelledby="price-tab">
                                
                            </div>
                            <!-- TAB สินค้าคงคลัง -->
                            <div class="tab-pane fade " id="products" role="tabpanel" aria-labelledby="products-tab">
                                <div class="row ">
                                    <div class="col-lg">
                                        <div class="table-responsive pt-1" id='Table2'></div>
                                    </div>
                                </div>
                            </div>
                            <!-- TAB อ้างอิง -->
                            <div class="tab-pane fade " id="refer" role="tabpanel" aria-labelledby="refer-tab">
                                <div class="row ">
                                    <div class="col-lg">
                                        <div class="row ">
                                            <div class="col-lg d-flex align-items-center">
                                                <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>&nbsp;<span>ลิงค์ YouTube :</span>&nbsp;<a id="" href="https://www.youtube.com/watch?v=XUrjLSBzJwY">https://www.youtube.com/watch?v=XUrjLSBzJwY</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- เป้าขายที่ตั้งไว้ -->
                            <div class="tab-pane fade " id="target" role="tabpanel" aria-labelledby="target-tab">
                                <div class="table-responsive">
                                    <table class='table table-sm table-bordered' id='table-target'>
                                        <thead>
                                            <tr>
                                                <th class='text-center'>&nbsp;</th>
                                                <th width='15%' class='text-center color-header-team'>MT1</th>
                                                <th width='15%' class='text-center color-header-team'>MT2</th>
                                                <th width='15%' class='text-center color-header-team'>TT ตจว.</th>
                                                <th width='15%' class='text-center color-header-team'>หน้าร้าน + TT กทม.</th>
                                                <th width='15%' class='text-center color-header-team'>ออนไลน์</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- โปรโมชั่น -->
                            <div class="tab-pane fade " id="pro" role="tabpanel" aria-labelledby="pro-tab">
                                <div class="row align-items-center text-center">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END MODAL TAB ข้อมูลสินค้า -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-close2" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDeletePro" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title">แจ้งเตือน</h5>
                <p class="my-4">คุณต้องการลบหรือไม่?</p>
                <button type="button" class="btn btn-secondary btn-sm me-4" data-bs-dismiss="modal">ออก</button>
                <button type="button" class="btn btn-primary btn-sm ms-4" id='btn-condeletepro'>ตกลง</button>
            </div>
        </div>
    </div>
</div>

<?php
// echo "<input type='hidden' id='ViewData' value='".$ViewStatus."'>";
?>
<!-- END MODAL ข้อมูลสินค้า -->

<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        CallHeade();
    });
    try{ document.createEvent("TouchEvent"); var isMobile = true; }
    catch(e){ var isMobile = false; }
</script>
<script type="text/javascript">
    function CallHeade() {
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

    /* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */
    $(document).ready(function() {
        CallData();

        
    });

    function CallData() {
        if(isMobile == true) {
            var Length = 10;
        }else{
            var Length = 17;
        }
        $("#table1").DataTable({
            "ajax": {
                url: "menus/general/ajax/ajaxitem_masterdata.php?a=CallData",
                type: "POST",
                data: {
                    <?php if(isset($_GET['Sku'])) { ?> 
                        ItemCode : '<?php echo $_GET['Sku']; ?>'
                    <?php } ?>
                },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                <?php if($_SESSION['uClass'] == 0 || $_SESSION['uClass'] == 1 || $_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4) { ?>
                    { "data": "ItemCode", class: "dt-body-center" },
                    { "data": "ItemName", class: "" },
                    { "data": "BarCode", class: "dt-body-center" },
                    { "data": "OnHand", class: "dt-body-right" },
                    { "data": "PriceType", class: "" },
                    { "data": "LastPurPrc", class: "dt-body-right" },
                    { "data": "P0", class: "dt-body-right" },
                    { "data": "P1", class: "dt-body-right" },
                    { "data": "P2", class: "dt-body-right" },
                    { "data": "GP_P2", class: "dt-body-right" },
                    { "data": "S1", class: "dt-body-right" },
                    { "data": "GP_S1", class: "dt-body-right" },
                    { "data": "S1Q", class: "dt-body-right" },
                    { "data": "S2", class: "dt-body-right" },
                    { "data": "GP_S2", class: "dt-body-right" },
                    { "data": "S2Q", class: "dt-body-right" },
                    { "data": "S3", class: "dt-body-right" },
                    { "data": "GP_S3", class: "dt-body-right" },
                    { "data": "S3Q", class: "dt-body-right" },
                    { "data": "MgrPrice", class: "dt-body-right" },
                    { "data": "GP_Mgr", class: "dt-body-right" },
                    { "data": "MTPrice", class: "dt-body-right" },
                <?php }else{ ?>
                    { "data": "ItemCode", class: "text-center " },
                    { "data": "ItemName", class: "" },
                    { "data": "BarCode", class: "text-center" },
                    { "data": "OnHand", class: "dt-body-right" },
                    { "data": "PriceType", class: "" },
                    { "data": "P0", class: "dt-body-right" },
                    { "data": "P1", class: "dt-body-right" },
                    { "data": "P2", class: "dt-body-right" },
                    { "data": "S1", class: "dt-body-right" },
                    { "data": "S1Q", class: "dt-body-right" },
                    { "data": "S2", class: "dt-body-right" },
                    { "data": "S2Q", class: "dt-body-right" },
                    { "data": "S3", class: "dt-body-right" },
                    { "data": "S3Q", class: "dt-body-right" },
                <?php } ?>
            ],
            "columnDefs": [
                <?php if($_SESSION['uClass'] == 0 || $_SESSION['uClass'] == 1 || $_SESSION['uClass'] == 2 || $_SESSION['uClass'] == 3 || $_SESSION['uClass'] == 4) { ?>
                    { "width": "6.4%", "targets": 0 },
                    { "width": "15%", "targets": 1 },
                    { "width": "4.7%", "targets": 2 },
                    { "width": "3%", "targets": 3 },
                    { "width": "6.7%", "targets": 4 },
                    { "width": "3.7%", "targets": 5 },
                    { "width": "3.7%", "targets": 6 },
                    { "width": "3.7%", "targets": 7 },
                    { "width": "3.7%", "targets": 8 },
                    { "width": "3.7%", "targets": 9 },
                    { "width": "3.7%", "targets": 10 },
                    { "width": "3.7%", "targets": 11 },
                    { "width": "3.7%", "targets": 12 },
                    { "width": "3.7%", "targets": 13 },
                    { "width": "3.7%", "targets": 14 },
                    { "width": "3.7%", "targets": 15 },
                    { "width": "3.7%", "targets": 16 },
                    { "width": "3.7%", "targets": 17 },
                    { "width": "3.7%", "targets": 18 },
                    { "width": "3.7%", "targets": 19 },
                    { "width": "3.7%", "targets": 20 },
                    { "width": "3.7%", "targets": 21 },
                <?php }else{ ?>
                    { "width": "7.6%", "targets": 0 },    
                    { "width": "10.6%", "targets": 1 },    
                    { "width": "8.6%", "targets": 2 },    
                    { "width": "5%", "targets": 3 },    
                    { "width": "7.6%", "targets": 4 },    
                    { "width": "6.6%", "targets": 5 },    
                    { "width": "6.6%", "targets": 6 },    
                    { "width": "6.6%", "targets": 7 },    
                    { "width": "6.6%", "targets": 8 },    
                    { "width": "6.6%", "targets": 9 },    
                    { "width": "6.6%", "targets": 10 },    
                    { "width": "6.6%", "targets": 11 },    
                    { "width": "6.6%", "targets": 12 },    
                    { "width": "6.6%", "targets": 13 },    
                <?php } ?>
            ],
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": Length,
            "language":{ 
                    "loadingRecords": "กำลังโหลด...<i class='fas fa-spinner fa-spin'></i>",
            },
        });
    }

    function SelectItemCode(Item,PriceType) {
        $(".overlay").show();
        var ItemCode = Item;
        if (ItemCode != undefined) {
            $("#SizeModal").removeClass("modal-full");
            $("#SizeModal").removeClass("modal-xl");
            switch(isMobile) {
                case true: var SizeModal = "modal-full"; break;
                case false: var SizeModal = "modal-xl"; break;
                default: var SizeModal = "modal-xl"; break;
            }
            $.ajax({
                url: "menus/general/ajax/ajaxitem_masterdata.php?a=SelectItemCode",
                type: "POST",
                data: {
                    ItemCode: ItemCode,
                },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key, inval) {
                        $("#price-tab").click();
                        $("#btnImages").html(inval["btnImages"]);
                        $("#ItemImages").html(inval["ItemImages"]);
                        $("#ItemMainH").html(inval["ItemRowMainH"]);
                        $("#ItemMainB").html(inval["ItemRowMainB"]);
                        $("#ItemSpecH").html(inval["ItemRowSpecH"]);
                        $("#ItemSpecB").html(inval["ItemRowSpecB"]);
                        $("#ItemCode").val(ItemCode);

                        // $("#PriceType").val(PriceType).change();
                        $("#price").html(inval['DataPrice']);

                        $("#Table2").html(inval['output2']);
                        $("#SizeModal").addClass(SizeModal);

                        $("#table-target tbody").html(inval['DataTarget']);

                        GetImgPro(ItemCode);

                        $("#ModalItemMaster").modal("show");
                    })
                }
            });
        } else {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณาแจ้งแผนก IT");
            $("#alert_modal").modal('show');
        }
        $(".overlay").hide();
    }

    // เมื่อเลือกประเภทราคา
    function SelectPriceList() {
        $(".overlay").show();
        $.ajax({
            url: "menus/general/ajax/ajaxitem_masterdata.php?a=SelectPriceList",
            type: "POST",
            data: {
                PriceType: $("#PriceType").val(),
                ItemCode: $("#ItemCode").val(),
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    <?php 
                    switch ($_SESSION['uClass']){
                        case 0 :
                        case 2 :
                        case 3 :
                        case 4 :
                        case 18 :
                        case 19 :
                            ?>
                            $(".FixMgr").show();
                            $("#GrossPrice").val(parseFloat(inval['GrossPrice']).toFixed(2));
                            $("#GP_1").val(inval['GP_1']);
                            $("#GP_2").val(inval['GP_2']);
                            $("#GP_3").val(inval['GP_3']);
                            $("#MgrPrice").val(parseFloat(inval['MgrPrice']).toFixed(2));
                            <?php 
                        break;
                        default :
                            ?>
                            $(".FixMgr").hide();
                            <?php 
                        break;   
                    } 
                    ?>
                    $("#P0").val(parseFloat(inval['P0']).toFixed(2));
                    $("#P1").val(parseFloat(inval['P1']).toFixed(2));
                    $("#P2").val(parseFloat(inval['P2']).toFixed(2));
                    $("#S1Q").val(parseFloat(inval['S1Q']).toFixed(0));
                    $("#S1P").val(parseFloat(inval['S1P']).toFixed(2));
                    $("#S2Q").val(parseFloat(inval['S2Q']).toFixed(0));
                    $("#S2P").val(parseFloat(inval['S2P']).toFixed(2));
                    $("#S3Q").val(parseFloat(inval['S3Q']).toFixed(0));
                    $("#S3P").val(parseFloat(inval['S3P']).toFixed(2));
                    $("#MTPrice").val(parseFloat(inval['MTPrice']).toFixed(2));
                    $("#MTPrice2").val(parseFloat(inval['MTPrice2']).toFixed(2));
                })
            }
        })
        $(".overlay").hide();
    }

    // Close Modal
    $("#btn-close1, #btn-close2").on('click', function() {
        $("#ItemCode").val(null);
    })

    function GetImgPro(ItemCode) {
        $.ajax({
            url: "menus/general/ajax/ajaxitem_masterdata.php?a=GetImgPro",
            type: "POST",
            data: { ItemCode: ItemCode },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $("#pro .row").html(inval['ImgPro']);
                })
            }
        });
    }

    function DeletePro(ID) {
        $("#ModalDeletePro").modal("show");
        $(document).off("click","#btn-condeletepro").on("click","#btn-condeletepro", function() {
            $.ajax({
                url: "menus/general/ajax/ajaxitem_masterdata.php?a=DeletePro",
                type: "POST",
                data: { ID: ID },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key, inval) {
                        $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 60px;'></i>");
                        $("#alert_body").html("ลบสำเร็จ");
                        $("#alert_modal").modal("show");
                        GetImgPro(inval['ItemCode']);
                        $("#ModalDeletePro").modal("hide");
                    })
                }
            });
        });
    }
</script>
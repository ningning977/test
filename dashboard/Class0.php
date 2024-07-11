<section class="row mb-4">
    <div class="col-lg-3 col-md col-sm-12">
        <!-- รายงานยอดขายเดือน -->
        <?php require("Box_server.php");?>
    </div>
    <div class="col-lg-9 col-md col-sm-12">
        <!-- ระบบอนุมัติเอกสาร -->
        <?php require("Box_DocApp.php");?>
    </div>
</section>

<section class="row mb-4">
    <!-- เช็คสต็อกออนไลน์ -->
    <div class="col-lg-3 col-md col-sm-12">
        <!-- เช็คสต็อกออนไลน์ -->
        <?php require("Box_Stock.php");?>

        <!-- รายการเป้าขายสินค้า -->
        <?php require("Box_SaleTarget.php");?>

        <!-- รายการสรุป Co Sales -->
        <?php require("Box_CoSales.php");?>
    </div>
    <div class="col-lg-9 col-md col-sm-12">
        <!-- รายงานสรุปฝ่ายบริหาร -->
        <?php require("Box_Mgr.php");?>
    </div>
</section>


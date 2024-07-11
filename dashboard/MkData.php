<section class="row mb-4">
    <div class="col-lg-3 col-md col-sm-12">
        <!-- รายงานยอดขายเดือน -->
        <?php require("Box_Sales.php");?>
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
    </div>
    <div class="col-lg-9 col-md col-sm-12">
        <!-- รายงานสรุปฝ่ายบริหาร -->
        <?php require("Box_Mgr.php");?>
    </div>
</section>
<section class="row mb-4">
    <div class="col-lg-3 col-md col-sm-12">&nbsp;</div>
    <div class="col-lg-9 col-md col-sm-12">
        <!-- ข้อมูลการขาย -->
        <?php require("Box_SalesData.php");?>
    </div>
</section>

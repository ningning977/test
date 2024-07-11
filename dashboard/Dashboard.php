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
    <div class="col-lg-3 col-md col-sm-12">
        <div class="row">
            <div class="col-lg">
                <!-- เช็คสต็อกออนไลน์ -->
                <?php require("Box_Stock.php");?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <?php switch ($_SESSION['DeptCode']){ case 'DP005' : case 'DP006' : case 'DP007' : case 'DP008' :?>
                    <!-- รายการเป้าขายสินค้า  -->
                    <?php require("Box_SaleTarget.php");?>
                <?php break; } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <?php if($_SESSION['uClass'] == 18) { ?>
                    <?php require("Box_CoSales.php");?>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md col-sm-12">
        <div class="row">
            <div class="col-lg">
                <?php if($_SESSION['DeptCode'] == 'DP006' && $_SESSION['uClass'] == '18'){ ?>
                    <!-- รายงานสรุปฝ่ายบริหาร -->
                    <?php require("Box_Mgr.php");?>
                <?php }elseif($_SESSION['DeptCode'] == 'DP005' || $_SESSION['DeptCode'] == 'DP006' || $_SESSION['DeptCode'] == 'DP007' || $_SESSION['DeptCode'] == 'DP008') { ?>
                    <!-- ยอดขายทีมรายบุคคล -->
                    <?php require("Box_IndiSale.php");?>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <!-- ข้อมูลการขาย -->
                <?php require("Box_SalesData.php");?>
            </div>
        </div>
    </div>
</section>

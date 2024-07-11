<style type="text/css">
    .btn-menu {
        height: 96px;
        padding: 1.5rem 0 1.5rem 0;
    }
    .btn-menu i {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<div class="page-heading">
    <h3><i class="fas fa-cog fa-fw fa-1x"></i> <span id='header'>ตั้งค่า</span></h3>
</div>
<hr/>
<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-cog fa-fw fa-1x"></i> จัดการข้อมูล Master Data</h4></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg col-md-6 col-sm-12 my-2"><a href="?p=import_ocrd" class="btn-menu btn btn-primary btn-block"><i style="margin: 0 auto;" class="fas fa-address-book fa-fw fa-2x" aria-hidden="true"></i><br/>นำเข้า Business Partner</a></div>
                    <div class="col-lg col-md-6 col-sm-12 my-2"><a href="?p=import_oitm" class="btn-menu btn btn-primary btn-block"><i style="margin: 0 auto;" class="fas fa-cube fa-fw fa-2x" aria-hidden="true"></i><br/>นำเข้า Item</a></div>
                    <div class="col-lg col-md-6 col-sm-12 my-2"><a href="?p=emplist" class="btn-menu btn btn-primary btn-block"><i style="margin: 0 auto;" class="fas fa-users fa-fw fa-2x" aria-hidden="true"></i><br/>ข้อมูลผู้ใช้งาน</a></div>
                    <div class="col-lg col-md-6 col-sm-12 my-2"><a href="javascript:void(0);" class="btn-menu btn btn-primary btn-block" onclick="SyncSlp()"><i style="margin: 0 auto;" class="fas fa-users fa-fw fa-2x" aria-hidden="true"></i><br/>นำเข้าพนักงานขาย</a></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-list fa-fw fa-1x"></i> กำหนดค่าเมนู</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg col-md-6 col-sm-12 my-2"><a href="?p=menu" class="btn-menu btn btn-primary btn-block"><i style="margin: 0 auto;" class="fas fa-list fa-fw fa-2x" aria-hidden="true"></i><br/>ตั้งค่าเมนู</a></div>
                    <div class="col-lg col-md-6 col-sm-12 my-2"><a href="?p=menulist" class="btn-menu btn btn-primary btn-block"><i style="margin: 0 auto;" class="fas fa-link fa-fw fa-2x" aria-hidden="true"></i><br/>ลิงห์หน้าเมนู</a></div>
                    <!-- <div class="col-lg col-md-6 col-sm-12 my-2"><a href="?p=updatecus" class="btn-menu btn btn-primary btn-block"><i style="margin: 0 auto;" class="fas fa-tasks fa-fw fa-2x" aria-hidden="true"></i><br/>กำหนดสิทธิ์เข้าใช้งาน</a></div> -->
                </div>
            </div>
        </div>
    </div>
</section>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-heartbeat fa-fw fa-1x"></i> Monitoring</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg col-md-6 col-sm-12 my-2"><a href="?p=servconnect" class="btn-menu btn btn-primary btn-block"><i style="margin: 0 auto;" class="fas fa-server fa-fw fa-2x" aria-hidden="true"></i><br/>SERVER Monitoring</a></div>
                    <div class="col-lg col-md-6 col-sm-12 my-2"><a href="?p=sqlconnect" class="btn-menu btn btn-primary btn-block"><i style="margin: 0 auto;" class="fas fa-database fa-fw fa-2x" aria-hidden="true"></i><br/>SQL Monitoring</a></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function SyncSlp() {
        $(".overlay").show();
        $.ajax({
            url: "setting/ajax/ajaximport_oslp.php?p=SyncSlp",
            success: function(result) {
                const obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    $("#alert_header").html("<i class=\"fas fa-check-circle fa-fw fa-lg text-danger\"></i> ดำเนินการเสร็จสิ้น!");
                    $("#alert_body").html("Sync ข้อมูลพนักงานขายทั้งหมดแล้ว ("+inval['RowInsert']+" รายการ)");
                    $("#alert_modal").modal('show');
                });
                $(".overlay").hide();
            }
        })
    }
</script>
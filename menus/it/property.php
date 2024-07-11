<style type="text/css">
    #myInput {
        align-items: flex-end;
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
                <!---------- เนื้อหา Pages ------------>
                <div class="d-flex flex-row-reverse">
                    <div class="p-2">
                        <input type="text" class="form-control" id="myInput" name="myInput">
                    </div>
                </div>
                <table class="table table-bordered rounded rounded-3 overflow-hidden" style="width:100%;" id='table_all_team'>
                    <thead>

                        <tr class="text-center text-white " style='background-color: #9A1118;'>
                            <!-- <th colspan='2'></th> -->
                            <th style="width: 10%;">รหัสทรัพย์สิน</th>
                            <th style="width: 10%;">ประเภท</th>
                            <th style="width: 10%;">วันที่จัดซื้อ</th>
                            <th style="width: 30%;">ผู้รับผิดชอบ</th>
                            <th style="width: 20%;">หน่วยงาน</th>
                            <th style="width: 10%;">วัน MA ล่าสุด</th>
                            <th style="width: 10%;">คน MA</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php for ($i = 1; $i <= 10; $i++) { ?>
                            <tr>
                                <td><?php echo "รหัสทรัพย์สิน ($i)" ?></td>
                                <td><?php echo "ประเภท ($i)" ?></td>
                                <td><?php echo "วันที่จัดซื้อ ($i)" ?></td>
                                <td><?php echo "ผู้รับผิดชอบ ($i)" ?></td>
                                <td><?php echo "หน่วยงาน ($i)" ?></td>
                                <td><?php echo "วัน MA ล่าสุด ($i)" ?></td>
                                <td><?php echo "คน MA ($i)" ?></td>
                            </tr>
                        <?php } ?>
                


                    </tbody>
                    <tbody></tbody>
                </table>
                <!-------- สินสุดเนื้อหา Pages --------->
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        CallHead();
    });
</script>
<script type="text/javascript">
    function CallHead() {
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
</script>
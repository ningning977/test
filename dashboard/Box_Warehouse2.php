<div class='card'>
    <div class='card-header'>
        <h4><i class='fas fa-bullseye fa-fw fa-1x'></i> มูลค่าสินค้าคลังมือสอง</h4>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center">
            เลือกทีม&nbsp;
            <select class='form-select form-select-sm w-50' name="Warehouse2" id="Warehouse2" onchange='GetWarehouse2();'>
                <option value="" selected disabled>เลือกทีม</option>
                <option value="MT1">ทีม MT1</option>
                <option value="MT2">ทีม MT2</option>
                <option value="TT2">ทีม ตจว.</option>
                <option value="OUL">ทีม กทม. + หน้าร้าน</option>
                <option value="ONL">ทีม ออนไลน์</option>
            </select>
        </div>
        <div class="row mt-2">
            <div class="col-lg">
                <table class="table table-hover" id='TableWarehouse2'>
                    <thead class="text-center">
                        <tr>
                            <th>ชื่อคลัง</th>
                            <th>มูลค่าสินค่า [บาท]</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function GetWarehouse2() {
        const Warehouse2 = $("#Warehouse2").val();
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=GetWarehouse2",
            type: "POST",
            data: { Warehouse2: Warehouse2 },
            success: function(result) {
                let obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#TableWarehouse2 tbody").html(inval['Data']);
                });
            }
        })
    }
</script>
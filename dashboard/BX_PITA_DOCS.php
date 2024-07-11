<div class="card h-100">
    <div class="card-header">
        <h4><i class="fas fa-file-invoice fa-fw fa-1x"></i> รายการซื้อ/ขาย <small class="text-muted">&mdash; 10 รายการล่าสุด</small></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <nav>
                    <div class="nav nav-tabs" id="ApproveTab" role="tablist">
                        <button onclick="CallAppTab(1)" class="nav-link text-primary active" id="tab_AppIV" data-bs-toggle="tab" data-bs-target="#AppIV" type="button" role="tab" aria-controls="AppIV" aria-selected="true">รายการขาย (Invoice)</button>
                        <button onclick="CallAppTab(2)" class="nav-link text-primary" id="tab_AppPO" data-bs-toggle="tab" data-bs-target="#AppPO" type="button" role="tab" aria-controls="AppPO" aria-selected="false">รายการสั่งซื้อ (P/O)</button>
                    </div>
                </nav>
                <div class="tab-content mt-3" id="nav-tabContent">
                    <div class="tab-pane show active" id="AppIV" role="tabpanel" aria-labelledby="tab_AppIV">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="font-size: 12px;" id="AppIVList">
                                <thead class="text-center text-light" style='background-color: #9A1118;'>
                                    <tr>
                                        <th>วันที่เปิดบิล</th>
                                        <th>กำหนดชำระ</th>
                                        <th>เลขที่บิล</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th>มูลค่าท้ายบิล</th>
                                        <th>พนักงานขาย</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="7"><i class="fas fa-spinner fa-pulse fa-fw fa-1x"></i> กำลังโหลด...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="AppPO" role="tabpanel" aria-labelledby="tab_AppPO">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="font-size: 12px;" id="AppPOList">
                                <thead class="text-center text-light" style='background-color: #9A1118;'>
                                    <tr>
                                        <th>วันที่เปิด P/O</th>
                                        <th>กำหนดชำระ</th>
                                        <th>เลขที่บิล</th>
                                        <th>ชื่อลูกค้า</th>
                                        <th>มูลค่าท้ายบิล</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="7"><i class="fas fa-spinner fa-pulse fa-fw fa-1x"></i> กำลังโหลด...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        CallAppTab(1);
    });


    function CallAppTab(tab) {
        switch(tab) {
            case 1:
                // ajax App Order
                $("#AppSOList tbody").html("<tr><td class=\"text-center\" colspan=\"7\"><i class=\"fas fa-spinner fa-pulse fa-fw fa-1x\"></i> กำลังโหลด...</td></tr>");
                $.ajax({
                    url: "dashboard/ajax/ajaxPitaBox.php?a=AppIV",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
                    type: "POST",
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj,function(key,inval) {
                            $("#AppIVList tbody").html(inval["output"]);
                        });
                    }
                });
                break;
            case 2:
                $("#AppWOList tbody").html("<tr><td class=\"text-center\" colspan=\"7\"><i class=\"fas fa-spinner fa-pulse fa-fw fa-1x\"></i> กำลังโหลด...</td></tr>");
                $.ajax({
                    url: "dashboard/ajax/ajaxPitaBox.php?a=AppPO",
                    type: "POST",
                    success: function(result) {
                        var obj = jQuery.parseJSON(result);
                        $.each(obj, function(key,inval) {
                            $(".overlay").hide();
                            $("#AppPOList tbody").html(inval['output']);
                        });
                    }
                });

                break;
        }
    }

</script>
<style type="text/css">
</style>

<?php
echo "<input type='hidden' id='HeadeMenuLink' value = '" . $_GET['p'] . "'>";
?>
<div class="page-heading">
    <h3><i class="fas fa-address-book fa-fw fa-1x"></i> นำเข้า Business Partner</h3>
</div>
<hr>
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
                <h4><i class="fas fa-address-book fa-fw fa-1x"></i> นำเข้า Business Partner</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-12">
                        <div class="form-group mb-3">
                            <label for="PickCardCode"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</label>
                            <input type="text" class="form-control form-control-sm" id="PickCardCode" placeholder="กรอกรหัสคู่ค้า เช่น C-00918 เป็นต้น">
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-6">
                        <div class="form-group mb-3">
                            <label for="Btn-Import">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="ImportCard();"><i class="fas fa-search fa-fw fa-1x"></i> ค้นหา</button>
                        </div>
                    </div>
                </div>
                <?php if($_SESSION['DeptCode'] == "DP002") { ?>
                <hr/>
                <form id="FormEditCard">
                    <div class="row mt-4">
                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group mb-3">
                                <label for="txt_CardCode">รหัสคู่ค้า</label>
                                <input type="type" class="form-control form-control-sm text-center" name="txt_CardCode" id="txt_CardCode" readonly />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-8 col-6">
                            <div class="form-group mb-3">
                                <label for="txt_CardName">ชื่อคู่ค้า</label>
                                <input type="text" class="form-control form-control-sm" name="txt_CardName" id="txt_CardName" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-8 col-6">
                            <div class="form-group mb-3">
                                <label for="txt_GPS">พิกัดละติจูดและลองจิจูด (GPS) <a href="https://www.google.com/maps/" target="_blank">ไปยัง Google Maps <i class="fas fa-external-link-alt fa-fw fa-1x"></i></a></label>
                                <input type="text" class="form-control form-control-sm text-center" name="txt_GPS" id="txt_GPS" />
                                <small class="text-muted">สามารถศึกษาวิธีการดึงค่าได้จาก <a href="https://support.google.com/maps/answer/18539?hl=th" target="_blank">ที่นี่ <i class="fas fa-external-link-alt fa-fw fa-1x"></i></a> (หัวข้อ ดูพิกัดของสถานที่)</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group mb-3">
                                <label for="txt_CardStatus">สถานะคู่ค้า</label>
                                <select class="form-select form-select-sm" name="txt_CardStatus" id="txt_CardStatus">
                                    <option value="A">Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-12 text-right">
                            <button type="button" class="btn btn-primary btn-sm" onclick="SaveCard();"><i class="far fa-save fa-fw fa-1x"></i> บันทึกข้อมูล</button>
                        </div>
                    </div>
                </form>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<!-- MODAL SAVE SUCCESS -->
<div class="modal fade" id="confirm_saved" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-check-circle fa-fw fa-lg text-success"></i> สำเร็จ</h5>
                <p id="confirm" class="my-4">บันทึกข้อมูลสำเร็จ</p>
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function ImportCard() {
        var CardCode = $("#PickCardCode").val();
        if(CardCode == '') {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณากรอกรหัสลูกค้าก่อน");
            $("#alert_modal").modal('show');
        } else {
            $.ajax({
                url: "setting/ajax/ajaximport_ocrd.php?p=ImportCard",
                type: "POST",
                data: {
                    CardCode: CardCode
                },
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj, function(key, inval) {
                        var Status = inval['Status'];
                        var modal_body = "";
                        if(Status == "SUCCESS") {
                            $("#txt_CardCode").val(inval['txt_CardCode']);
                            $("#txt_CardName").val(inval['txt_CardName']);
                            $("#txt_GPS").val(inval['txt_GPS']);
                            $("#txt_CardStatus").val(inval['txt_CardStatus']).change();
                        } else {
                            switch(Status) {
                                case "ERR::NORESULT":   modal_body = "ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากไม่พบข้อมูลในระบบ SAP"; break;
                            }
                            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                            $("#alert_body").html(modal_body);
                            $("#alert_modal").modal('show');
                        }
                    });
                }
            })
        }
    }

    $('#PickCardCode').keypress(function (e) {
        if (e.which == 13) {
            ImportCard();
        }
    });

    function SaveCard() {
        var FormEditCard = new FormData($("#FormEditCard")[0]);
        $.ajax({
            url: "setting/ajax/ajaximport_ocrd.php?p=SaveCard",
            type: "POST",
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: FormEditCard,
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    if(inval['Status'] == 'SUCCESS') {
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            window.location.reload();
                        });
                    }
                });
            }
        })
    }

$(document).ready(function() {
});

    
</script>

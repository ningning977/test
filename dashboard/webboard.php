<section class="row">
    <div class="col-lg">
        <div class="card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-bullhorn fa-fw fa-1x"></i> กระดานข่าว</h4>
            </div>
            <div class="card-body">
                <div class='table-responsive'>
                <table class='table table-sm table-bordered table-hover' id='Table'>
                    <thead class='text-center' style='font-size: 13px;'>
                        <tr>
                            <th>วันที่ประกาศ</th>
                            <th>หัวข้อ</th>
                            <th>ประเภทข่าว</th>
                            <th>ผู้เขียน</th>
                        </tr>
                    </thead>
                    <tbody style='font-size: 12.5px;'>
                    </tbody>
                    <tfoot style='font-size: 12.5px;'>
                        <tr>
                            <td colspan='4' class='text-center'><a href='#' onclick='PushNews()'>อ่านเพิ่มเติม <i class='fas fa-chevron-right'></i></a></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalViewNews" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fab fa-readme' style='font-size: 20px;'></i>&nbsp;&nbsp;&nbsp;รายละเอียดข่าว</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xl-1"><span class='fw-bolder'><i class="fas fa-bullhorn fa-fw"></i> ชื่อเรื่อง</span></div>
                    <div class="col"><span id='viewHeader'></span></div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xl-1"><span class='fw-bolder'><i class="fas fa-user-edit fa-fw"></i> ผู้เขียน</span></div>
                    <div class="col"><span id='viewName'></span></div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xl-1"><span class='fw-bolder'><i class="fas fa-book-reader fa-fw"></i> ถึงฝ่าย</span></div>
                    <div class="col"><span id='viewDeptCode'></span></div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xl-1"><span class='fw-bolder'><i class="fas fa-calendar-alt fa-fw"></i> วันที่</span></div>
                    <div class="col"><span id='viewSEDate'></span></div>
                </div>
                <div class="row pt-1">
                    <div class="col-lg">
                        <div class='d-flex'><span class='fw-bolder'><i class="fas fa-newspaper fa-fw"></i> รายละเอียด</span></div>
                        <div class='p-2' id='viewDetail'></div>
                    </div>
                </div>
                <div class="row pt-1">
                    <div class="col-lg">
                        <div class='d-flex align-items-center'><span class='fw-bolder'><i class="fas fa-paperclip fa-fw"></i> เอกสารแนบ</span>&nbsp;<span class="text-muted" style='font-size: 12px;'>(คลิกที่ชื่อรูปเพื่อดาวน์โหลด, คลิกที่ไฟล์เพื่อดาวน์โหลด)</span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7">
                        <div class='border p-2 w-100' id="viewImg"></div>
                    </div>
                    <div class="col-lg-5">
                        <div class='p-2' id='viewDoc'></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $.ajax({
        url: 'dashboard/ajax/ajaxWebboard.php?a=CallData',
        type: "GET",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                var No = inval['No'];
                var Data = "";
                if(No != 0) {
                    for(var i = 0; i < No; i++) {
                        Data += "<tr class='"+inval['rowStyle'][i]+"'>"+
                                    "<td class='text-center'>"+inval['CreateDate'][i]+"</td>"+
                                    "<td>"+inval['newsTitle'][i]+"</td>"+
                                    "<td>"+inval['newsType'][i]+"</td>"+
                                    "<td>"+inval['FullName'][i]+"</td>"+
                                "</tr>";
                    }
                }else{
                    Data += "<tr class=''>"+
                                "<td colspan='4' class='text-center'>ยังไม่มีประกาศในตอนนี้ :(</td>"+
                            "</tr>";
                }
                $("#Table tbody").html(Data);
            });
        }
    });

    function ViewData(newsID) {
        $.ajax({
            url: "menus/general/ajax/ajaxfeed_news.php?a=ViewData",
            type: "POST",
            data: { newsID : newsID, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#viewHeader").html(inval['newsTitle']);
                    $("#viewName").html(inval['FullName']);
                    $("#viewDeptCode").html(inval['DeptCode']);
                    $("#viewSEDate").html(inval['SEDate']);
                    $("#viewDetail").html(inval['Content']);
                    $("#viewImg").html(inval['DataImg']);
                    $("#viewDoc").html(inval['DataDoc']);

                    // $("#viewDetail p").addClass("m-0");

                    $("#ModalViewNews").modal("show");
                });
            }
        })
    }

    function PushNews() {
        window.open("?p=feed_news","_blank");
    }
</script>
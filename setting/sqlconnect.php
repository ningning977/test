<style type="text/css">
</style>

<?php
echo "<input type='hidden' id='HeadeMenuLink' value = '" . $_GET['p'] . "'>";
?>
<div class="page-heading">
    <h3><i class="fas fa-database fa-fw fa-1x"></i> SQL Monitoring</h3>
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
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2 col-6">
                        <div class="form-group">
                            <label for="filt_server">เลือกเซิร์ฟเวอร์</label>
                            <select class="form-select form-select-sm" name="filt_server" id="filt_server">
                                <option selected disabled>กรุณาเลือกเซิร์ฟเวอร์</option>
                                <option value="ERFSERVER_MAIN">EUROX FORCE [192.168.1.9]</option>
                                <option value="SAPSERVER_NEW">SAP [192.168.1.11]</option>
                                <option value="HRMISERVER">HRMI [192.168.1.11]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1 col-6">
                        <div class="form-group">
                            <label for="filt_refresh">Refresh Time</label>
                            <select class="form-select form-select-sm" name="filt_refresh" id="filt_refresh">
                                <option value="10000">10 วินาที</option>
                                <option value="5000">5 วินาที</option>
                                <option value="3000">3 วินาที</option>
                                <option value="1000">1 วินาที</option>
                                <option value="500">0.5 วินาที</option>
                                <option value="250">0.25 วินาที</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1 col-6">
                        <div class="form-group mb-3">
                            <label for="btn-playsql">&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm btn-block" id="btn-play" onclick="GetSQLCon();"><i class="fas fa-play fa-fw fa-1x"></i> เริ่ม</button>
                        </div>
                    </div>
                    <div class="col-lg-1 col-6">
                        <div class="form-group mb-3">
                            <label for="btn-playsql">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm btn-block" id="btn-stop" disabled><i class="fas fa-stop fa-fw fa-1x"></i> หยุด</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-sm table-bordered table-hover" style="font-size: 12px;" id="QueryList">
                            <thead class="text-center">
                                <tr>
                                    <th colspan="7">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="7">กรุณาเลือกเซิร์ฟเวอร์...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function GetData() {
        var serv = $("#filt_server").val();
        var thead = "";

        switch (serv) {
            case "ERFSERVER_MAIN":
                thead +=
                    "<tr>"+
                        "<th width='3.5%'>No.</th>"+
                        "<th width='15%'>IP Address / Computer Name</th>"+
                        "<th width='10%'>DB Name</th>"+
                        "<th width='10%'>Command</th>"+
                        "<th width='10%'>Duration</th>"+
                        "<th width='15%'>Query Status</th>"+
                        "<th width='36.5%'>SQL Statement</th>"+
                    "</tr>";
                break;
            default:
                thead +=
                    "<tr>" +
                        "<th width='3.5%'>No.</th>" +
                        "<th width='10%'>IP Address</th>" +
                        "<th width='15%'>Host Name</th>" +
                        "<th width='15%'>Program / App</th>" +
                        "<th width='10%'>Duration</th>" +
                        "<th width='10%'>Query Status</th>" +
                        "<th width='36.5%'>SQL Statement</th>" +
                    "</tr>";
                break;
        }
        // ajax
        $.ajax({
            url: 'setting/ajax/ajaxsqlconnect.php',
            type: 'POST',
            data: {
                server: serv,
            },
            success: function(data) {
                $var = jQuery.parseJSON(data);
                $.each($var, function(key, inval) {
                    var row = "";
                    var no = 1;
                    if (inval['row'] > 0) {
                        switch(serv) {
                            case "ERFSERVER_MAIN":
                                for (index = 0; index < inval['row']; index++) {
                                    row +=
                                        "<tr"+inval[index]['trClass']+">"+
                                        "<td class='text-right'>" + no + "</td>" +
                                        "<td>"+inval[index]['PROCESSLIST_HOST']+"</td>"+
                                        "<td class='text-center'>"+inval[index]['PROCESSLIST_DB']+"</td>"+
                                        "<td>"+inval[index]['PROCESSLIST_COMMAND']+"</td>"+
                                        "<td class='text-center'>"+inval[index]['PROCESSLIST_TIME']+"</td>"+
                                        "<td>"+inval[index]['PROCESSLIST_STATE']+"</td>"+
                                        "<td>"+inval[index]['PROCESSLIST_INFO']+"</td>"+
                                        "</tr>";
                                    no++;
                                }
                                break;
                            default:
                                for (index = 0; index < inval['row']; index++) {
                                    switch(inval[index]['client_net_address']) {
                                        case "192.168.1.9":
                                            row += "<tr class='text-danger table-danger'>";
                                        break;
                                        default:
                                            row += "<tr>";
                                        break;
                                    }
                                    row +=
                                        "<td class='text-right'>" + no + "</td>" +
                                        "<td class='text-center'>" + inval[index]['client_net_address'] + "</td>" +
                                        "<td>" + inval[index]['host_name'] + "</td>" +
                                        "<td>" + inval[index]['program_name'] + "</td>" +
                                        "<td class='text-center'>" + inval[index]['Time'] + "</td>" +
                                        "<td class='text-center'>" + inval[index]['status'] + "</td>" +
                                        "<td>" + inval[index]['text'] + "</td>" +
                                        "</tr>";
                                        no++;
                                }
                                break;
                        }
                        
                    } else {
                        row += "<tr><td class='text-center' colspan='7'>ไม่มีคนดึง Query ในขณะนี้</td></tr>";
                    }
                    $('#QueryList thead').html(thead);
                    $('#QueryList tbody').html(row);
                });
            }
        })
    }

    function GetSQLCon() {
        var refh = parseFloat($("#filt_refresh").val());
        var serv = $("#filt_server").val();
        if(serv == null || serv == "") {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("กรุณาเลือกเซิร์ฟเวอร์ก่อน");
            $("#alert_modal").modal('show');
        } else {
            $("#btn-stop").removeAttr("disabled");
            $("#btn-play").attr("disabled",true);

            GetData();
            const getdata = setInterval(GetData, refh);

            $("#btn-stop").on("click",function(e) {
                e.preventDefault();
                clearInterval(getdata);
                $("#btn-play").removeAttr("disabled");
                $("#btn-stop").attr("disabled",true);
            });
        }
    }

    $(document).ready(function() {
        var default_server  = JSON.parse(sessionStorage.getItem('filt_server'));
        var default_refresh = JSON.parse(sessionStorage.getItem('filt_refresh'));

        if(default_server != null) {
            $("#filt_server").val(default_server).change();
        }
        if(default_refresh != null) {
            $("#filt_refresh").val(default_refresh).change();
        }

        $("#filt_server, #filt_refresh").on("change", function() {
            var refh = parseFloat($("#filt_refresh").val());
            var serv = $("#filt_server").val();

            if(default_server != serv) {
                sessionStorage.setItem('filt_server',JSON.stringify(serv));
            }
            if(default_refresh != refh) {
                sessionStorage.setItem('filt_refresh',JSON.stringify(refh));
            }
        });
    });

    
</script>
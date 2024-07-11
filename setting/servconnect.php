<style type="text/css">
</style>

<?php
    echo "<input type='hidden' id='HeadeMenuLink' value = '".$_GET['p']."'>";
?>
<div class="page-heading">
    <h3><i class="fas fa-server fa-fw fa-1x"></i> SERVER Monitoring</h3>
</div>
<hr>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br/><br/>
        กำลังทดสอบการเชื่อมต่อ...
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
                    <div class="col-12">
                        <button type="button" class="btn btn-success btn-sm" onclick="GetServer();"><i class="fas fa-sync fa-fw fa-1x"></i> Refresh</button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <table class="table table-hover table-bordered" id="ServList">
                            <thead>
                                <tr>
                                    <th colspan="2">SERVER Status</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php for($i=0; $i<4; $i++) {
                            echo
                            "<tr>".
                                "<td width='5%' class='text-center'>".
                                    "<i class='fas fa-server fa-fw fa-4x'></i>".
                                    
                                "</td>".
                                "<td>".
                                    "<b id='SRV".$i."_SVNAME'>SERVERNAME</b><br/>".
                                    "<pre style='margin:0;'><small id='SRV".$i."_IPADDRESS' class='text-primary'>IP Address</small></pre>".
                                    "<small class='load_text' id='SRV".$i."_STATUS'><i class='fas fa-spinner fa-pulse fa-fw'></i> Pinging...</small>".
                                "</td>".
                            "</tr>";
                                    
                            }
                        ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function GetServer() {
        $("table#ServList tbody tr td small.load_text").html("<i class='fas fa-spinner fa-pulse fa-fw'></i> Pinging...");
        $.ajax({
            url: "setting/ajax/ajaxservconnect.php",
            success: function(result) {
                
                var obj = jQuery.parseJSON(result);
                $.each(obj, function(key, inval) {
                    for(i=0;i<inval['Rows'];i++) {
                        var IconStatus = "";
                        if(inval['SRV'+i+'_Status'] == 1) {
                            IconStatus = "<i class=\"fas fa-circle fa-fw text-success\"></i> Connected";
                            
                        } else {
                            IconStatus = "<i class=\"fas fa-circle fa-fw text-danger\"></i> Disconnected";
                            $("#SRV"+i+"_SVNAME").parents("tr").addClass("table-dander text-dander");
                        }
                        $("#SRV"+i+"_SVNAME").html(inval['SRV'+i+'_SVName']);
                        $("#SRV"+i+"_IPADDRESS").html(inval['SRV'+i+'_SVIPAd']);
                        $("#SRV"+i+"_STATUS").html(IconStatus);
                    }
                });
            }
        })
    }

    GetServer();
</script>
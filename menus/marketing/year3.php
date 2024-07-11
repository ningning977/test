<style type="text/css">

</style>
<?php
    echo "<input type='hidden' id='HeadeMenuLink' value = '".$_GET['p']."'>";
?>
<div class="page-heading">
    <h3><span id='header1'></h3>
</div>
<div class='text-secondary'>
    <?php echo PathMenu($_GET['p']); ?>
</div>
<hr class='mt-1'>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br/><br/>
        กำลังโหลด...
    </div>
</div>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></h4>
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
	$(document).ready(function(){
        CallHeade();
	});
</script> 
<script type="text/javascript">
    function CallHeade(){
        $(".overlay").show();
        //แก้   URL ajax เอง
        var MenuCase = $('#HeadeMenuLink').val()
        $.ajax({
            url: "menus/marketting/ajax/ajaxyear3.php?a=head",
            type: "POST",
            data : {MenuCase : MenuCase,},
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#header1").html(inval["header1"]);
                    $("#header2").html(inval["header2"]);
                });
                $(".overlay").hide();
            }
        });
    };

</script> 
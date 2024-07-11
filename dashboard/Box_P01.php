<style>
    @media only screen and (max-width:820px) {
        .tableFix {
            overflow-y: auto;
            height: 800px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:821px) and (max-width:1180px) {
        .tableFix {
            overflow-y: auto;
            height: 450px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }

    @media (min-width:1181px) {
        .tableFix {
            overflow-y: auto;
            height: 550px;
        }
        .tableFix thead {
            position: sticky;
            top: 0;
        }
    }
</style>

<div class='card'>
    <div class='card-header'>
        <h4><i class='fas fa-bullseye fa-fw fa-1x'></i> มูลค่าสินค้าคงคลัง P01,P02,P03 </h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg">
                <table class="table table-hover" id='TbP01'>
                    <thead class="text-center">
                        <tr>
                            <th>ชื่อคลัง</th>
                            <th>มูลค่าสินค้า [บาท]</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../../js/extensions/apexcharts.js"></script>
<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
        CallP01();
	});
    
    function CallP01(){
        console.log('wai');
        $.ajax({
            url: "dashboard/ajax/ajaxAllBox.php?a=P01",
            type: "POST",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $('#TbP01 tbody').html(inval['Data']);
                
                });
            }
        })

    }
    
</script> 
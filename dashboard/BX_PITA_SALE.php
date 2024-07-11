<div class="card mb-3 h-100">
    <div class="card-header">
        <h4><i class="fas fa-dollar-sign fa-fw fa-1x"></i> ยอดขายเดือน <?php echo FullMonth(date("m"))." ".date("Y"); ?></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg">
                <div class="row">
                    <div class="col-lg">
                        <div id="SaleTargetProgress" class="text-center"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg">
                        <table class="table">
                            <tr>
                                <td width="50%">ยอดขายเดือนนี้</td>
                                <td class="text-right" width="50%"> <span class="text-primary" style="font-weight: bold;" id='DataSale'></span> บาท</td>
                            </tr>
                            <tr>
                                <td width="50%">เป้าขายเดือนนี้</td>
                                <td class="text-right" width="50%"> <span class="text-primary" style="font-weight: bold;" id='alltar'></span> บาท</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../js/extensions/apexcharts.js"></script>
<script>
    $(document).ready(function(){
        ChartReportSale();
    });

    function ChartReportSale() {
        $.ajax({
            url: "dashboard/ajax/ajaxPitaBox.php?a=ChartReportSale",
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    // ยอดขายรายเดือน
                    $("#DataSale").html(inval['DataSale']);
                    $("#alltar").html(inval['alltar']);
                    chart_report_sale.updateSeries([inval['perSale']]);
                })
            }
        })
    }
</script>
<script type="text/javascript"> // ยอดขาย
    var options = {
        series: [],
        colors: ["#EE0000"],
        chart: {
            width: '100%',
            height: 256,
            type: "radialBar",
            toolbar: {
                show: false
            },
            fontFamily: "https://fonts.googleapis.com/css2?family=Niramit:wght@200;300;400;500;600&family=Noto+Sans+Thai:wght@300;400;500&display=swap"
        },
        plotOptions: {
            radialBar: {
                startAngle: 0,
                endAngle: 360,
                hollow: {
                    margin: 0,
                    size: "70%",
                    background: "#fff",
                    image: undefined,
                    position: "front",
                    dropShadow: {
                        enabled: true,
                        top: 3,
                        left: 0,
                        blur: 4,
                        opacity: 0.24
                    }
                },
                track: {
                    background: "#fff",
                    strokeWidth: "67%",
                    margin: 0, // margin is in pixels
                    dropShadow: {
                        enabled: true,
                        top: -3,
                        left: 0,
                        blur: 4,
                        opacity: 0.35
                    }
                },

                dataLabels: {
                    show: true,
                    name: {
                        offsetY: -10,
                        show: true,
                        color: "#888",
                        fontSize: "17px",
                        fontWeight: '400'
                    },
                    value: {
                        // formatter: function(val) {
                        //     return parseInt(val.toString(), 10).toString();
                        // },
                        color: "#111",
                        fontSize: "36px",
                        show: true
                    }
                }
            }
        },
        fill: {
            type: "gradient",
            gradient: {
                shade: "dark",
                type: "horizontal",
                shadeIntensity: 0.5,
                gradientToColors: ["#9A1118"],
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            }
        },
        stroke: {
            lineCap: "round"
        },
        labels: ["ยอดขายต่อเป้า"]
    };
    var chart_report_sale = new ApexCharts(document.querySelector("#SaleTargetProgress"), options); chart_report_sale.render();
</script>
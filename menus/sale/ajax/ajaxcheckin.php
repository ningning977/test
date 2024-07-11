<div class="row mt-2">
    <div class="col-lg-12">
        <h6>ร้านค้า: <span id="ChkCardName"></span> <small class='text-muted'>&mdash; <span id="GetDistance"></span></small></h6>
        <input type="hidden" name="ChkRouteEty" id="ChkRouteEty" value="0" readonly />
        <input type="hidden" name="ChkLon" id="ChkLon" readonly />
        <input type="hidden" name="ChkLat" id="ChkLat" readonly />
        <input type="hidden" name="PlanLon" id="PlanLon" readonly />
        <input type="hidden" name="PlanLat" id="PlanLat" readonly />
        <input type="hidden" name="ChkCardCode" id="ChkCardCode" readonly />
        <input type="hidden" name="ChkDistance" id="ChkDistance" readonly />
    </div>
</div>
<div class="row mt-2">
    <div class="col-lg-12" id="CheckInMaps" style="height: 25rem;"></div>
</div>
<div class="row mt-4">
    <div class="col-lg-6">
        <button type="button" class="btn btn-warning w-100" id="btn-telline" disabled><i class="fas fa-phone-volume fa-fw fa-lg"></i> โทร / LINE</button>
    </div>
    <div class="col-lg-6">
        <button type="button" class="btn btn-success w-100" id="btn-checkin"><i class="fas fa-map-marker-alt fa-fw fa-lg"></i> เช็คอิน</button>
    </div>
</div>
<script src="https://api.longdo.com/map/?key=46cf330bb7a5a715d30d214b291cb8a2"></script>
<script type="text/javascript">
function number_format(number,decimal) {
     var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
     var formatter = new Intl.NumberFormat("en",options);
     return formatter.format(number)
}

function CheckInMaps(Cus_Lon, Cus_Lat, Chk_Lon, Chk_Lat) {
    /* CusLon + CusLat = พิกัดร้านค้า || ChkLon + ChkLat = พิกัดผู้ใช้ */
    var CusLon;
    var CusLat;
    var ChkLon = Chk_Lon;
    var ChkLat = Chk_Lat;

    var map = new longdo.Map({
        placeholder: document.getElementById("CheckInMaps"),
        lastview: false,
        language: 'th',
        ui: longdo.UiComponent.Mobile
    });

    map.Layers.setBase(longdo.Layers.GRAY);
    map.zoom(15,true);
    map.zoomRange({ min: 10, max: 20 });
    map.location({ lon: ChkLon, lat: ChkLat }, true);

    /* CheckIn Marker */
    var CheckPin = new longdo.Marker({ lon: ChkLon, lat: ChkLat },{ icon: { html: '<i class=\'fas fa-male fa-4x\' style=\'color: #fc0380;\'></i>', offset: { x: 9, y: 48 } }, weight: 999 });
    map.Overlays.add(CheckPin);

    if(Cus_Lon != 0.00 && Cus_Lat != 0.00) {
        /* Customer Marker */
        var StorePin = new longdo.Marker({ lon: Cus_Lon, lat: Cus_Lat },{ icon: { html: '<i class=\'fas fa-map-marker-alt fa-2x text-primary\'></i>', offset: { x: 9, y: 24 } }, weight: 999 });
        map.Overlays.add(StorePin);
        /* 
            Safezone Generator
            Add CirCle radius ~5km.
            ระยะห่าง 1 องศา Lat/Lon = ~111km. @ เส้นศูนย์สูตรโลก
            ~1km. = 1/111 = 0.009009009009009 degree
            ~5km. = 0.009009009009009 * 5 = 0.045045045045
        */
        var SafeZone = new longdo.Circle({
            lon: Cus_Lon, lat: Cus_Lat
        }, 0.0465, {
            lineWidth: 2,
            lineColor: 'rgba(128,252,3,0.8)',
            fillColor: 'rgba(128,252,3,0.25)'
        });
        map.Overlays.add(SafeZone);

        /* Find Distance */
        var Distance = longdo.Util.distance([CheckPin.location(),StorePin.location()]);
        $("#ChkDistance").val((Distance/1000).toFixed(2));
        var LineDistance = new longdo.Polyline([CheckPin.location(),StorePin.location()],{ lineColor: "rgba(154,17,24,1)", lineWidth: 2, lineStyle: longdo.LineStyle.Dashed });
        map.Overlays.add(LineDistance);
    }

    console.log

    if(isNaN(Distance) == true) {
        ShowDistance = "ไม่พบพิกัดร้านค้า";
    } else {
        ShowDistance = "ระยะห่างจากจุดเช็คอินถึงร้านค้า: "+(number_format(Distance/1000,2))+" กม.";
    }

    $("#GetDistance").html(ShowDistance);

    $("#ModalCheckIn").on("shown.bs.modal", function () {
        map.resize();
    });
}
function CheckIn(CardCode, RouteEntry) {
    $(".modal").modal("hide");
    $(".overlay").show();

    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var GeoLon = position.coords.longitude;
            var GeoLat = position.coords.latitude;
            $("#ChkLon").val(GeoLon);
            $("#ChkLat").val(GeoLat);
        }, function(error) {
            var txt_error;
            switch(error.code) {
                case error.PERMISSION_DENIED: txt_error = "กรุณาอนุญาตสิทธิ์ระบุพิกัดก่อนใช้งาน (Permission Denied.)<br/>วิธีแก้ไข <a href='https://support.google.com/chrome/answer/142065?co=GENIE.Platform%3DAndroid&oco=1' target='_blank'>Google Chrome / Microsoft Edge</a> | <a href='https://support.apple.com/guide/iphone/customize-your-safari-settings-iphb3100d149/16.0/ios/16.0' target='_blank'>Safari</a>"; break;
                case error.POSITION_UNAVAILABLE: txt_error = "ไม่สามารถระบุพิกัดได้ (Location Unavailable.)"; break;
                case error.TIMEOUT: txt_error = "การร้องขอระบุพิกัดหมดอายุ (Request time out.)"; break;
                case error.UNKNOWN_ERROR: txt_error = "ข้อผิดพลาดที่ไม่รู้จัก (Unknown Error.)<br/>กรุณาติดต่อผู้ดูแลระบบ"; break;
            }
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html(txt_error);
            $("#alert_modal").modal('show');
        });
    } else {
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("อุปกรณ์หรือแอปพลิเคชั่นนี้ไม่รองรับการใช้งานระบุพิกัด");
        $("#alert_modal").modal('show');
    }
    $.ajax({
        url: "menus/sale/ajax/ajaxroutetrip.php?p=GetLocation",
        type: "POST",
        data: { CardCode: CardCode, RouteEntry: RouteEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#ChkCardName").html(inval['plan_customer']);
                $("#ChkCardCode").val(inval['plan_cardcode']);
                $("#PlanLon").val(inval['plan_lon']);
                $("#PlanLat").val(inval['plan_lat']);
            });
        }
    });

    setTimeout(() => {
        $("#ChkRouteEty").val(RouteEntry);
        var ChkLon = $("#ChkLon").val();
        var ChkLat = $("#ChkLat").val();
        var CusLon = $("#PlanLon").val();
        var CusLat = $("#PlanLat").val();
        if(ChkLon.length == 0 || ChkLat.length == 0) {
            $(".overlay").hide();
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("หน้าเว็บโหลดไม่ทัน กดเช็คอินใหม่อีกรอบครับ :)");
            $("#alert_modal").modal('show');
        } else {
            CheckInMaps(CusLon, CusLat, ChkLon, ChkLat);
            $(".overlay").hide();
            $("#ModalCheckIn").modal("show");
            $("#btn-checkin").on("click", function(e){
                e.preventDefault();
                AddCheckIn(0);
            });
            $("#btn-telline").on("click", function(e){
                e.preventDefault();
                AddCheckIn(1);
            });
        }
    }, 3000); 
}

function AddCheckIn(CheckType) {
    $(document).off("click","#btn-checkin, #btn-telline").on("click","#btn-checkin, #btn-telline", function(e){
        e.preventDefault();
        var RouteEntry  = $("#ChkRouteEty").val();
        var ChkCardCode = $("#ChkCardCode").val();
        var ChkLon      = $("#ChkLon").val();
        var ChkLat      = $("#ChkLat").val();
        var PlanLon     = $("#PlanLon").val();
        var PlanLat     = $("#PlanLat").val();
        var ChkDistance = $("#ChkDistance").val();
        $.ajax({
            url: "menus/sale/ajax/ajaxroutetrip.php?p=AddCheckIn",
            type: "POST",
            data: { 
                RouteEntry: RouteEntry,
                ChkCardCode: ChkCardCode,
                ChkLon: ChkLon,
                ChkLat: ChkLat,
                PlanLon: PlanLon,
                PlanLat: PlanLat,
                ChkDistance, ChkDistance,
                CheckType: CheckType
            },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    if(inval['chk_status'] == "SUCCESS") {
                        $(".modal").modal("hide");
                        $("#confirm_saved").modal('show');
                        $("#btn-save-reload").on("click", function(e){
                            e.preventDefault();
                            <?php if($_GET['p'] == "routetrip") { ?>
                                var filt_year  = $("#filt_year").val();
                                var filt_month = $("#filt_month").val();
                                var filt_user  = $("#filt_user").val();
                                var filt_view  = $("#filt_view").val();
                                GetWorkTrip(filt_year,filt_month,filt_user,filt_view);
                            <?php } ?>
                        });
                    } else {
                        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
                        $("#alert_body").html("ไม่สามารถเช็คอินได้ กรุณาลองใหม่อีกครั้ง");
                        $("#alert_modal").modal('show');
                    }
                });
            }
        });
    });
    

    
}
</script>
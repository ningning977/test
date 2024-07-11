<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if($_SESSION['UserName']==NULL ){
	echo '<script>window.location="../"</script>';
}
date_default_timezone_set('Asia/Bangkok');
require("../core/config.core.php");
include("../core/functions.core.php");

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">

    <title>EUROX By KingBangkok Intertrade Co., Ltd.</title>

    <link rel="stylesheet" href="../css/main/app.css">
    <link rel="stylesheet" href="../css/main/jquery.dataTables.min.css">
    <!-- <link rel="stylesheet" href="../css/main/app-dark.css"> -->
    <!-- <link rel="stylesheet" href="../assets/bootstrap-select/css/bootstrap-select.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css">
    <link href="../image/logo/favicon_96.jpg" rel="shortcut icon" type="image/png" />
    <!-- <link rel="stylesheet" href="../css/pages/datatables.css"> -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap.min.css"> -->
    <link rel="stylesheet" href="../css/pages/simple-datatables.css">
    <script src="https://kit.fontawesome.com/3288009746.js" crossorigin="anonymous"></script>

    <!-- <link rel="stylesheet" href="../css/shared/iconly.css"> -->
    <script src="../js/jquery-min.js" type="text/javascript"></script>
    <!-- One Signal Web Push Notify -->
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
    <!-- <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            // OneSignal.SERVICE_WORKER_PARAM = { scope: '../js/' };
            // OneSignal.SERVICE_WORKER_PATH = '../js/OneSignalSDKWorker.js';
            // OneSignal.SERVICE_WORKER_UPDATER_PATH = '../js/OneSignalSDKWorker.js';
            OneSignal.init({
                appId: "d6d9b154-9d2a-473d-83f0-26f9c6a0f021",
                safari_web_id: "web.onesignal.auto.52bd6d36-ef00-42e1-a687-b4f3eaae4ff3",
                notifyButton: {
                    enable: true,
                },
                subdomainName: "euroxforce",
            });

        });
    </script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <style rel="stylesheet" type="text/css">
        /* CUSTOM SCROLL BAR */
        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, .75);
        }

        ::-webkit-scrollbar-track
        {
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.02);
        }

        ::-webkit-scrollbar
        {
            width: 12px;
            height: 12px;
            background-color: #F5F5F5;
        }

        ::-webkit-scrollbar-thumb
        {
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.50);
        }

        .RowNoti:hover {
            background-color: rgba(0, 0, 0, 0.11);
            color: #9A1118;
        }

        .menu-search-list {
            padding: 5px 10px;
        }

        .menu-search-list:hover {
            background-color: rgba(0, 0, 0, 0.11);
            color: #9A1118;
            border-radius: 5px;
        }
    </style>

</head>


<body>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br/><br/>
        กำลังโหลด...
    </div>
</div>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper shadow-sm">
                <div class="sidebar-header position-relative p-0">
                    <div class="logo d-flex justify-content-center">
                        <a href="" class=""><img src="../image/logo/eurox_force_2.jpg" alt="Logo"  style="height: 100%; width: 100%;"></a>
                    </div>
                    <div class="x position-absolute top-0 end-0">
                        <a href="#" class="sidebar-hide d-xl-none d-block"><i class="far fa-times-circle fa-fw fa-1x" style="color: #781212;"></i></a>
                    </div>
                    <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                        <div class="form-check form-switch fs-6" style="display: none;">
                            <input class="form-check-input  me-0" type="checkbox" id="toggle-dark">
                            <label class="form-check-label"></label>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title p-0 pb-3 ms-4 me-4">
                            <div class="row">
                                <div class="row d-flex align-items-center p-2 border border-1" style="background-color: #FFFFFF; border-top-left-radius: 15px; border-top-right-radius: 15px; box-shadow: 1.5px 1.5px #7812125e;">
                                    <div class="col-lg-3 d-flex justify-content-center">
                                        <?php echo"<img src='../image/user/".$_SESSION['UserPhoto']."' alt='ImgUser' style='width: 50px; border-radius: 50%;'>"; ?>
                                    </div>
                                    <div class="col-lg-9 d-flex justify-content-center">
                                        <?php echo"<a href='#' class='ms-1 text-dark'>".$_SESSION['uName']." (".$_SESSION['uNickName'].")</a>"; ?>
                                    </div>
                                </div>
                                <div class="row border border-1" style="background-color: #FFFFFF; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; box-shadow: 1.5px 1.5px #7812125e;">
                                    <div class="d-flex justify-content-end">
                                        <a href="javascript:void(0);" class="d-flex flex-row-reverse bd-highlight mb-0" onClick=Noti('EUR')>
                                            <p class="mb-2" style="color: #781212; font-size: 15px;" id='NotiEUR'>0</p>
                                            <i class="fas fa-bell fs-5 mt-2 text-primary"></i>
                                        </a> 
                                    </div>
                                     
                                    
                                </div>
                            </div>
                        </li>
                        <li class="sidebar-title p-0 m-0 d-flex justify-content-between align-items-center">
                            <div>หัวข้อเมนู</div>
                            <div><button class='btn btn-sm btn-outline-secondary p-0 ps-1 pe-1' onclick="SearchMenu('Open')"><i class="fab fa-searchengin"></i> ค้นหา</button></div> 
                        </li>
                        <?php
                        if ($_SESSION['uClass'] == 0){
                            $WherCode = " ";
                        }else{
                            $WherCode = " AND (T0.TypeOpen IN ('A','C') OR (T0.TypeOpen = 'D' AND T1.DeptCode = '".$_SESSION['DeptCode']."')
                                                                        OR (T0.TypeOpen = 'L' AND T1.LvCode = '".$_SESSION['LvCode']."')) ";
                        }
                        $sql1 = "SELECT T0.MenuKey,T0.MenuIcon,T0.MenuName,T0.MenuCase,T0.MenuLink,T0.ClassOpen,T0.TypeOpen 
                                 FROM menus T0
                                      LEFT JOIN menugroup T1 ON T0.MenuKey = T1.MenuKey
                                 WHERE T0.MenuKey = T0.UpKey  ".$WherCode."
                                       AND MenuStatus = 'A' AND MenuLv = 0 
                                 ORDER BY T0.MenuSort";
                        // echo $sql1;
                        $getmenu= MySQLSelectX($sql1);
                        $tmpMenu1 = "";
                        $MenuOpen = 0;
                        while ($MainMenu = mysqli_fetch_array($getmenu)){
                            if ($tmpMenu1  != $MainMenu['MenuKey']){
                                $tmpMenu1 = $MainMenu['MenuKey'];
                                $sql1 = "SELECT * FROM menus WHERE MenuStatus = 'A' AND MenuLv = 1 AND UpKey = '".$MainMenu['MenuKey']."' AND MenuKey != UpKey";
                                if (CHKRowDB($sql1) == 0){
                                    if (strlen($MainMenu['ClassOpen']) < 300 && $MainMenu['TypeOpen'] != 'A' AND $_SESSION['uClass'] != 0){
                                        for ($i=(strlen($MainMenu['ClassOpen'])+1);$i<=300;$i++){
                                            $MainMenu['ClassOpen'] .= "0";
                                        }
                                        $MainMenu['ClassOpen'] = "A".$MainMenu['ClassOpen'];
                                        switch ($MainMenu['TypeOpen']){
                                            case 'C' :
                                                $MenuOpen = substr($MainMenu['ClassOpen'],$_SESSION['uClass'],1);
                                            break;
                                            case 'D' :
                                                $sql7 = "SELECT DeptCode FROM menugroup WHERE MenuKey = '".$MainMenu['MenuKey']."' AND StatusDoc = 'A' AND DeptCode  = '".$_SESSION['DeptCode']."'";
                                                if (CHKRowDB($sql7) != 0){
                                                    $MenuOpen = 1;
                                                }

                                            break;
                                            case 'L' :
                                                $sql7 = "SELECT DeptCode FROM menugroup WHERE MenuKey = '".$MainMenu['MenuKey']."' AND StatusDoc = 'A' AND LvCode  = '".$_SESSION['LvCode']."'";
                                                if (CHKRowDB($sql7) != 0){
                                                    $MenuOpen = 1;
                                                }
                                            break;
                                        }
                                        
                                    }

                                    if ($_SESSION['uClass'] == 0 || ($MainMenu['TypeOpen'] == 'A' || $MenuOpen == 1 )){
                                        echo "<li class='sidebar-item ' id='".$MainMenu['MenuKey']."'>
                                                    <a href='".$MainMenu['MenuLink']."' class='sidebar-link'>
                                                        ".$MainMenu['MenuIcon']."
                                                        <span>".$MainMenu['MenuName']."</span>
                                                    </a>
                                                </li>";
                                        $MenuOpen = 0;
                                    }
                                }else{
                                    echo "<li class='sidebar-item  has-sub' id='".$MainMenu['MenuKey']."'>
                                                <a href='#' class='sidebar-link'>
                                                    ".$MainMenu['MenuIcon']."
                                                    <span>".$MainMenu['MenuName']."</span>
                                                </a>
                                            <ul class='submenu'>";
                                    $sql1 = "SELECT T0.MenuKey,T0.Upkey,T0.MenuIcon,T0.MenuName,T0.MenuCase,T0.MenuLink,T0.ClassOpen,T0.TypeOpen 
                                             FROM menus T0
                                                  LEFT JOIN menugroup T1 ON T0.MenuKey = T1.MenuKey
                                             WHERE T0.MenuKey != T0.UpKey ".$WherCode."  
                                                   AND MenuStatus = 'A'  AND T0.upKey = '".$tmpMenu1."' AND MenuLv = 1   
                                             ORDER BY T0.MenuSort";
                                    $getmenu2= MySQLSelectX($sql1);
                                    $tmpMenu2 = "";
                                    while ($subMenu1 = mysqli_fetch_array($getmenu2)){      

                                        if ($tmpMenu2 != $subMenu1['MenuKey']){
                                            $tmpMenu2 = $subMenu1['MenuKey'];
                                            $sql1 = "SELECT * FROM menus WHERE MenuStatus = 'A' AND MenuLv = 2 AND UpKey = '".$subMenu1['MenuKey']."'";
                                            //echo $sql1;
                                            if (CHKRowDB($sql1) == 0){
                                                if (strlen($subMenu1['ClassOpen']) < 300 && $subMenu1['TypeOpen'] != 'A' AND $_SESSION['uClass'] != 0){
                                                    for ($i=(strlen($subMenu1['ClassOpen'])+1);$i<=300;$i++){
                                                        $subMenu1['ClassOpen'] .= "0";
                                                    }
                                                    $subMenu1['ClassOpen'] = "A".$subMenu1['ClassOpen'];
                                                    //$MenuOpen = substr($subMenu1['ClassOpen'],$_SESSION['uClass'],1);
                                                    switch ($subMenu1['TypeOpen']){
                                                        case 'C' :
                                                            $MenuOpen = substr($subMenu1['ClassOpen'],$_SESSION['uClass'],1);
                                                        break;
                                                        case 'D' :
                                                            $sql7 = "SELECT DeptCode FROM menugroup WHERE MenuKey = '".$subMenu1['MenuKey']."' AND StatusDoc = 'A' AND DeptCode  = '".$_SESSION['DeptCode']."'";
                                                            if (CHKRowDB($sql7) != 0){
                                                                $MenuOpen = 1;
                                                            }
            
                                                        break;
                                                        case 'L' :
                                                            $sql7 = "SELECT LvCode FROM menugroup WHERE MenuKey = '".$subMenu1['MenuKey']."' AND StatusDoc = 'A' AND LvCode  = '".$_SESSION['LvCode']."'";
                                                            if (CHKRowDB($sql7) != 0){
                                                                $MenuOpen = 1;
                                                            }
                                                        break;
                                                    }
                                                }
                                                if ($_SESSION['uClass'] == 0 || ($subMenu1['TypeOpen'] == 'A' || $MenuOpen == 1 )){
                                                    echo "<li class='submenu-item' id='".$subMenu1['MenuKey']."'>
                                                                <a href='".$subMenu1['MenuLink']."' >
                                                                    <span>".$subMenu1['MenuName']."</span>
                                                                </a>
                                                          </li>";
                                                    $MenuOpen = 0;
                                                }
                                            }else{
                                                //echo "wai";
                                                echo "<li class='submenu-item sidebar-item has-sub'>
                                                            <a href='#' class='sidebar-link'>
                                                                <p style='padding: 0px; margin: 0px;'>".$subMenu1['MenuName']."</p>
                                                            </a>
                                                            <ul class='submenu '>";
                                                $sql1 = "SELECT T0.MenuKey,T0.Upkey,T0.MenuIcon,T0.MenuName,T0.MenuCase,T0.MenuLink,T0.ClassOpen,T0.TypeOpen 
                                                            FROM menus T0
                                                                 LEFT JOIN menugroup T1 ON T0.MenuKey = T1.MenuKey
                                                            WHERE T0.MenuKey != T0.UpKey ".$WherCode." 
                                                                  AND MenuStatus = 'A'  AND T0.upKey = '".$tmpMenu2."' AND MenuLv = 2   
                                                            ORDER BY T0.MenuSort";
                                                             //echo $sql1;
                                                $getmenu3= MySQLSelectX($sql1);
                                                $tmpMenu3= "";
                                                while ($subMenu2 = mysqli_fetch_array($getmenu3)){  
                                                    if ($tmpMenu3 != $subMenu2['MenuKey']){
                                                        $tmpMenu3 = $subMenu2['MenuKey'];
                                                        // echo $subMenu2['MenuName']."<br/>";
                                                        // echo $subMenu2['ClassOpen']."/".strlen($subMenu2['ClassOpen'])."/".$subMenu2['TypeOpen']."/".$_SESSION['uClass']."<br/>";
                                                        if (strlen($subMenu2['ClassOpen']) < 300 && $subMenu2['TypeOpen'] != 'A' AND $_SESSION['uClass'] != 0){
                                                            for ($i=(strlen($subMenu2['ClassOpen'])+1);$i<=300;$i++){
                                                                $subMenu2['ClassOpen'] .= "0";
                                                            }
                                                            $subMenu2['ClassOpen'] = "A".$subMenu2['ClassOpen'];
                                                            //$MenuOpen = substr($subMenu2['ClassOpen'],$_SESSION['uClass'],1);
                                                            switch ($subMenu2['TypeOpen']){
                                                                case 'C' :
                                                                    $MenuOpen = substr($subMenu2['ClassOpen'],$_SESSION['uClass'],1);
                                                                break;
                                                                case 'D' :
                                                                    $sql7 = "SELECT DeptCode FROM menugroup WHERE MenuKey = '".$subMenu2['MenuKey']."' AND StatusDoc = 'A' AND DeptCode  = '".$_SESSION['DeptCode']."'";
                                                                    // echo $sql7;
                                                                    if (CHKRowDB($sql7) != 0){
                                                                        $MenuOpen = 1;
                                                                    }
                    
                                                                break;
                                                                case 'L' :
                                                                    $sql7 = "SELECT LvCode FROM menugroup WHERE MenuKey = '".$subMenu2['MenuKey']."' AND StatusDoc = 'A' AND LvCode  = '".$_SESSION['LvCode']."'";
                                                                    if (CHKRowDB($sql7) != 0){
                                                                        $MenuOpen = 1;
                                                                    }
                                                                break;
                                                            }
                                                            
                                                        }
                                                        if ($_SESSION['uClass'] == 0 || ($subMenu2['TypeOpen'] == 'A' || $MenuOpen == 1 )){
                                                            echo "<li class='submenu-item'>
                                                                            <a href='".$subMenu2['MenuLink']."'>".$subMenu2['MenuName']."</a>
                                                                  </li>";
                                                            $MenuOpen = 0;
                                                        }
                                                    }
                                                }   
                                                echo "      </ul>
                                                      </li>";
                                            }
                                        }
                                    }
                                    echo "      </ul>
                                          </li>";
                                }
                            }       
                        }


                        ?>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <div class='' id="main">
            <!-- MENU TOGGLER -->
            <header class="">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="fas fa-bars fa-fw fa-lg" style="color: #781212;"></i>
                </a>
            </header>
            <!-- END MENU TOGGLER -->

            <!-- PAGE HEADING 

            PAGE HEADING -->

            <!-- CONTENT -->
            <div class="page-content" id="MainPage">
            <!-- ใส่เนื้อหาในนี้ -->
            <?php
                $page=addslashes(@$_GET['p']);
                $sql1="SELECT pages FROM  menulist WHERE MenuCase = '".$page."'";
                if (CHKRowDB($sql1)){
                    $ShowPage= MySQLSelect($sql1);
                    require($ShowPage['pages']);
                }else{
                    require("dashboard/webboard.php");
                    switch ($_SESSION['DeptCode']){
                        case 'DP001' :
                            if ($_SESSION['LvCode'] == 'LV004'){
                                require("dashboard/Class0.php");
                            }else{
                                require("dashboard/SaleMgr.php");
                            }
                            break;
                        case 'DP002' :
                            require("dashboard/Class0.php");
                            break;
                        case 'DP003' :
                            require("dashboard/MKData.php");
                            break;
                        case 'DP005' :
                        case 'DP006' :
                        case 'DP007' :
                        case 'DP008' :
                        case 'DP010' :
                        case 'DP011' :
                        case 'DP012' :
                            require("dashboard/Dashboard.php");
                            break;
                        case 'DP101' :
                            require("dashboard/PitaMgr.php");
                            break;
                        case 'DP009' :
                                require("dashboard/MgrAcc.php");
                            break;
                        case 'DP004' :
                            require("dashboard/Pudashbaord.php");
                            break;
                       

                    }
                }
            ?>
            <!-- END CONTENT -->

            <!-- FOOTER -->
            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p><?php echo date("Y"); ?> &copy; บจ.คิงบางกอก อินเตอร์เทรด</p>
                    </div>
                    <div class="float-end">
                        <p><span class="text-danger">IT KBI</span></p>
                    </div>
                </div>
            </footer>
            <!-- END FOOTER -->
        </div>
    </div>

    <!-- Modal noti -->
    <div class="modal fade" id="notiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">แจ้งเตือน <span id='HModal'></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    ...
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> -->
            </div>
        </div>
    </div>

    <!-- MODAL ALERT -->
    <div class="modal fade" id="alert_modal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5 class="modal-title" id="alert_header"></h5>
                    <p id="alert_body" class="my-4"></p>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">ตกลง</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CONFIRM -->
    <div class="modal fade" id="confirm_modal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5 class="modal-title"><i class="far fa-question-circle fa-fw fa-lg"></i> ยืนยัน</h5>
                    <p class="defult my-4">คุณต้องการดำเนินการต่อหรือไม่?</p>
                    <p class='custom d-none my-4'></p>
                    <button type="button" class="btn btn-secondary btn-sm w-25" data-bs-dismiss="modal"><i class="fas fa-times fa-fw fa-1x"></i> ไม่</button>
                    <button type="button" class="btn btn-primary btn-sm w-25" id="btn-confirm"><i class="fas fa-check fa-fw fa-1x"></i> ใช่</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalSearch" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-header p-2">
                    <h5 class="modal-title"><i class="fab fa-searchengin" style='font-size: 20px;'></i>&nbsp;&nbsp;ค้นหาเมนู</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <input type="text" class='form-control form-control-sm' name='txtSearch' id='txtSearch' onkeyup="SearchMenu('Search')">
                    <div class='pt-2 pb-2 ps-1 pe-1 mt-1' style='border-radius: 0px 5px;' id='DataSearch'></div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function(){
            GetNoti();
        });

        function GetNoti() {
            if('<?php echo $_SESSION['DeptCode']; ?>' != 'DP101') {
                let Url = [  
                    'menus/general/ajax/ajaxapp_order.php?a=read&tab=ChkRow',
                    'menus/general/ajax/ajaxapp_WO.php?p=AppList&tab=ChkRow',
                    'menus/general/ajax/ajaxapp_memo.php?p=MemoList&tab=ChkRow',
                    'menus/sale/ajax/ajaxapp_sa04.php?p=DocList&tab=ChkRow',
                    'dashboard/ajax/ajaxAllBox.php?a=WhsQuota&tab=ChkRow',
                    'menus/sale/ajax/ajaxordermng.php?p=GetOrder&tab=ChkRow'
                ];
                let SumRow = 0;
                let y = new Date().getFullYear();
                let m = new Date().getMonth()+1;
                let tabno = 3;
                for(let i = 0; i < Url.length; i++) {
                    $.ajax({
                        url: Url[i],
                        type: "POST",
                        data: { y: y, m: m, tabno: tabno },
                        success: function(result) {
                            var obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) {
                                if(inval['Rows'] != 0) {
                                    if(Url[i] == 'dashboard/ajax/ajaxAllBox.php?a=WhsQuota&tab=ChkRow') {
                                        <?php
                                        switch($_SESSION['DeptCode']) {
                                            case "DP002":
                                            case "DP005":
                                            case "DP006":
                                            case "DP007":
                                            case "DP008": ?>
                                                SumRow = SumRow+inval['Rows'];
                                                <?php break;
                                            case "DP003":
                                                switch($_SESSION['LvCode']) {
                                                    case "LV010":
                                                    case "LV011":
                                                    case "LV012":
                                                    case "LV013":
                                                    case "LV103":
                                                    case "LV104":
                                                    case "LV105":
                                                    case "LV106": ?>
                                                        SumRow = SumRow+inval['Rows'];
                                                        <?php break;
                                                    default: break;
                                                }
                                                break;
                                            default: break;
                                        }
                                        ?>
                                    }else{
                                        SumRow = SumRow+inval['Rows'];
                                    } 
                                }
                            });
                            if(i == Url.length-1) {
                                $("#NotiEUR").html(SumRow);
                            }
                        }
                    })
                }
            }
        }
       

        function Noti(x){
            $('#notiModal .modal-body').html("");
            if (x == 'ESS'){
                $('#HModal').html("ESS");
            }else{
                $('#HModal').html("Eurox Force");
                if('<?php echo $_SESSION['DeptCode']; ?>' != 'DP101') {
                    let Data =
                        "<div class='row'>"+
                            "<div class='col'>"+
                                "<table class='table table-sm table-borderless' id='TableNotiEUR'>"+
                                    "<tbody>"+
                                    "</tbody>"+
                                "</table>"+
                            "</div>"+
                        "</div>";
                    $('#notiModal .modal-body').html(Data);
                    $("#TableNotiEUR tbody").html("");

                    let Url = [  
                        'menus/general/ajax/ajaxapp_order.php?a=read&tab=ChkRow',
                        'menus/general/ajax/ajaxapp_WO.php?p=AppList&tab=ChkRow',
                        'menus/general/ajax/ajaxapp_memo.php?p=MemoList&tab=ChkRow',
                        'menus/sale/ajax/ajaxapp_sa04.php?p=DocList&tab=ChkRow',
                        'dashboard/ajax/ajaxAllBox.php?a=WhsQuota&tab=ChkRow',
                        'menus/sale/ajax/ajaxordermng.php?p=GetOrder&tab=ChkRow'
                    ];
                    let Name = ['ใบสั่งขาย', 'ใบฝากงาน', 'บันทึกภายใน', 'ส่วนลดหนี้/ลดจ่าย (SA-04)', 'คลังสินค้าจอง', 'ใบสั่งขาย รอตัด/รอสินค้า' ];
                    let Link = ['?p=app_order', '?p=app_WO', '?p=app_memo', '?p=app_sa04', '?p=instock', '?p=ordermng'];
                    
                    let DataBody = "";
                    let SumRow = 0;
                    let y = new Date().getFullYear();
                    let m = new Date().getMonth()+1;
                    let tabno = 3;
                    for(let i = 0; i < Url.length; i++) {
                        $.ajax({
                            url: Url[i],
                            type: "POST",
                            data: { y : y, m : m, tabno : tabno, },
                            success: function(result) {
                                var obj = jQuery.parseJSON(result);
                                $.each(obj,function(key,inval) {
                                    if(inval['Rows'] != 0) {
                                        if(Link[i] == '?p=instock') {
                                            <?php
                                            switch($_SESSION['DeptCode']) {
                                                case "DP002":
                                                case "DP005":
                                                case "DP006":
                                                case "DP007":
                                                case "DP008": ?>
                                                    DataBody += 
                                                    "<tr>"+
                                                        "<td class='pb-0'>"+
                                                            "<a href='"+Link[i]+"' target='_blank'>"+
                                                                "<div class='row p-3 rounded-3 bg-light-secondary RowNoti'>"+
                                                                    "<div class='col-1'><i class='fas fa-file-alt fa-fw fa-lg'></i></div>"+
                                                                    "<div class='col'>"+Name[i]+"</div>"+
                                                                    "<div class='col d-flex justify-content-end align-items-center text-primary fw-bolder' style='font-size: 12px;'>"+inval['Rows']+"</div>"+
                                                                "</div>"+
                                                            "</a>"+
                                                        "</td>"+
                                                    "</tr>";
                                                    SumRow = SumRow+inval['Rows'];
                                                    <?php break;
                                                case "DP003":
                                                    switch($_SESSION['LvCode']) {
                                                        case "LV010":
                                                        case "LV011":
                                                        case "LV012":
                                                        case "LV013":
                                                        case "LV103":
                                                        case "LV104":
                                                        case "LV105":
                                                        case "LV106": ?>
                                                            DataBody += 
                                                            "<tr>"+
                                                                "<td class='pb-0'>"+
                                                                    "<a href='"+Link[i]+"' target='_blank'>"+
                                                                        "<div class='row p-3 rounded-3 bg-light-secondary RowNoti'>"+
                                                                            "<div class='col-1'><i class='fas fa-file-alt fa-fw fa-lg'></i></div>"+
                                                                            "<div class='col'>"+Name[i]+"</div>"+
                                                                            "<div class='col d-flex justify-content-end align-items-center text-primary fw-bolder' style='font-size: 12px;'>"+inval['Rows']+"</div>"+
                                                                        "</div>"+
                                                                    "</a>"+
                                                                "</td>"+
                                                            "</tr>";
                                                            SumRow = SumRow+inval['Rows'];
                                                            <?php break;
                                                        default: break;
                                                    }
                                                    break;
                                                default: break;
                                            }
                                            ?>
                                        }else{
                                            DataBody += 
                                            "<tr>"+
                                                "<td class='pb-0'>"+
                                                    "<a href='"+Link[i]+"' target='_blank'>"+
                                                        "<div class='row p-3 rounded-3 bg-light-secondary RowNoti'>"+
                                                            "<div class='col-1'><i class='fas fa-file-alt fa-fw fa-lg'></i></div>"+
                                                            "<div class='col'>"+Name[i]+"</div>"+
                                                            "<div class='col d-flex justify-content-end align-items-center text-primary fw-bolder' style='font-size: 12px;'>"+inval['Rows']+"</div>"+
                                                        "</div>"+
                                                    "</a>"+
                                                "</td>"+
                                            "</tr>";
                                            SumRow = SumRow+inval['Rows'];
                                        }
                                    }
                                })
                                
                                if(i == Url.length-1) {
                                    $("#TableNotiEUR tbody").html(DataBody);
                                    $("#NotiEUR").html(SumRow);
                                }
                            },
                            async: false
                        });
                    }
                }
            }
            $("#notiModal").modal("show");
        }

        function SearchMenu(x) {
            $("#DataSearch").html("").removeClass("bg-body");
            if(x == 'Open') {
                $("#txtSearch").val("");
                $("#ModalSearch").modal("show");
                setTimeout(function() { 
                    $("#txtSearch").focus();
                }, 500);
            }else{
                const txtSearch = $("#txtSearch").val();
                if(txtSearch != '') {
                    $.ajax({
                        url: "dashboard/ajax/ajaxAllBox.php?a=SearchMenu",
                        type: "POST",
                        data: { txtSearch: txtSearch },
                        success: function(result) {
                            let obj = jQuery.parseJSON(result);
                            $.each(obj,function(key,inval) {
                                if(inval['Data'] != "") {
                                    $("#DataSearch").addClass("bg-body");
                                    $("#DataSearch").html(inval['Data']);
                                }
                            })
                        }
                    })
                }
            }
        }
    </script>
    <!-- End Modal noti -->
    <!-- <script src="../js/boostrap-select.js"></script> -->
    <script src="../js/app.js"></script>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js" type="text/javascript"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/i18n/defaults-*.min.js"></script> -->
    <!-- <script src="../assets/bootstrap-select/js/bootstrap-select.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script type="text/javascript">
        // OneSignal.push(function() {
        //     OneSignal.showNativePrompt();
        // });
    </script>
</body>
</html>
<?php
include('../../../core/config.core.php');
include('../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();

if($_SESSION['UserName']==NULL ){
	echo '<script>window.location="../"</script>';
}

$resultArray = array();
$arrCol = array();
$output = "";
if ($_GET['a'] == 'read'){
    
    $MenuSQL = "SELECT
                    T0.MenuIcon, T0.MenuName, T0.MenuKey, T0.UpKey, T0.MenuStatus, (SELECT COUNT(N0.MenuKey) FROM menus N0 WHERE (N0.UpKey = T0.MenuKey AND N0.UpKey != N0.MenuKey)) AS 'HasSub'
                FROM menus T0
                WHERE T0.MenuLv = 0
                ORDER BY T0.MenuSort ASC";
    $MenuQRY = MySQLSelectX($MenuSQL);
    $menuno = 0;
    while($MenuRST = mysqli_fetch_array($MenuQRY)) {
        $menuno++;
        $MainMenu = $MenuRST['MenuKey'];
        if($MenuRST['HasSub'] == 0) {
            $menu_lv2 = null;
        } else {
            $menu_lv2 = "<a href='javascript:void();' class='btn-more' data-MenuKey = '".$MenuRST['MenuKey']."'><i class='far fa-plus-square'></i></a>";
        }

        if($MenuRST['MenuStatus'] == "A") {
            $btnview = "<button type='button' class='btn-view btn btn-success btn-xs' data-status='I' data-MenuKey='".$MenuRST['MenuKey']."' ><i class='fas fa-eye' id=''></i></button>";
        } else {
            $btnview = "<button type='button' class='btn-view btn btn-success btn-xs' data-status='A' data-MenuKey='".$MenuRST['MenuKey']."' ><i class='fas fa-eye-slash' id=''></i></button>";;
        }
        $output .=  "<tr data-rowKey='".$MenuRST['MenuKey']."'>
                        <td class='text-center'>$menuno</td>
                        <td width='5%' class='text-center'>$menu_lv2</td>
                        <td>".$MenuRST['MenuIcon']." ".$MenuRST['MenuName']."</td>
                        <td class='text-center'>
                            $btnview
                            <button type='button' class='btn-edit btn btn-info btn-xs' data-MenuKey='".$MenuRST['MenuKey']."'><i class='fas fa-edit'></i></button>
                            <button type='button' class='btn-permission btn btn-dark btn-xs' data-MenuKey='".$MenuRST['MenuKey']."'><i class='fas fa-tasks'></i></button>
                            <button type='button' class='btn-delete btn btn-danger btn-xs' data-MenuKey='".$MenuRST['MenuKey']."'><i class='fas fa-trash-alt'></i></button>
                        </td>
                    </tr>";
        if($MenuRST['HasSub'] > 0) {
            $Sublv2SQL = "SELECT
                            T0.MenuIcon, T0.MenuName, T0.MenuKey, T0.UpKey, T0.MenuStatus, (SELECT COUNT(N0.MenuKey) FROM menus N0 WHERE (N0.UpKey = T0.MenuKey AND N0.UpKey != N0.MenuKey)) AS 'HasSub'
                        FROM menus T0
                        WHERE T0.MenuLv = 1 AND T0.UpKey = '".$MenuRST['MenuKey']."'
                        ORDER BY T0.MenuSort ASC";
            $Sublv2QRY = MySQLSelectX($Sublv2SQL);
            while($Sublv2RST = mysqli_fetch_array($Sublv2QRY)) {
                if($Sublv2RST['HasSub'] == 0) {
                    $menu_lv3 = null;
                } else {
                    $menu_lv3 = "<a href='javascript:void();' class='btn-more' data-MenuKey = '".$Sublv2RST['MenuKey']."'><i class='far fa-plus-square'></i></a>";
                }

                if($Sublv2RST['MenuStatus'] == "A") {
                    $btnview = "<button type='button' class='btn-view btn btn-success btn-xs' data-status='I' data-MenuKey='".$Sublv2RST['MenuKey']."' ><i class='fas fa-eye' id=''></i></button>";
                } else {
                    $btnview = "<button type='button' class='btn-view btn btn-success btn-xs' data-status='A' data-MenuKey='".$Sublv2RST['MenuKey']."' ><i class='fas fa-eye-slash' id=''></i></button>";
                }

                $output .=   "<tr class='hiderow' data-rowKey='".$Sublv2RST['MenuKey']."' data-UpKey='".$Sublv2RST['UpKey']."'>
                                <td>&nbsp;</td>
                                <td class='text-center'>$menu_lv3</td>
                                <td style='text-indent: 2rem;'>".$Sublv2RST['MenuIcon']." ".$Sublv2RST['MenuName']."</td>
                                <td class='text-center'>
                                    $btnview
                                    <button type='button' class='btn-edit btn btn-info btn-xs' data-MenuKey='".$Sublv2RST['MenuKey']."'><i class='fas fa-edit'></i></button>
                                    <button type='button' class='btn-permission btn btn-dark btn-xs' data-MenuKey='".$Sublv2RST['MenuKey']."'><i class='fas fa-tasks'></i></button>
                                    <button type='button' class='btn-delete btn btn-danger btn-xs' data-MenuKey='".$Sublv2RST['MenuKey']."'><i class='fas fa-trash-alt'></i></button>
                                </td>
                            </tr>";

                if($Sublv2RST['HasSub'] > 0) {
                    $Sublv3SQL = "SELECT
                                    T0.MenuIcon, T0.MenuName, T0.MenuKey, T0.UpKey, T0.MenuStatus, (SELECT COUNT(N0.MenuKey) FROM menus N0 WHERE (N0.UpKey = T0.MenuKey AND N0.UpKey != N0.MenuKey)) AS 'HasSub'
                                FROM menus T0
                                WHERE T0.MenuLv = 2 AND T0.UpKey = '".$Sublv2RST['MenuKey']."'
                                ORDER BY T0.MenuSort ASC";
                    $Sublv3QRY = MySQLSelectX($Sublv3SQL);
                    while($Sublv3RST = mysqli_fetch_array($Sublv3QRY)) {
                        if($Sublv3RST['HasSub'] == 0) {
                            $menu_lv3 = null;
                        } else {
                            $menu_lv3 = "<a href='javascript:void();' class='btn-more' data-MenuKey = '".$Sublv3RST['MenuKey']."'><i class='far fa-plus-square'></i></a>";
                        }

                        if($Sublv3RST['MenuStatus'] == "A") {
                            $btnview = "<button type='button' class='btn-view btn btn-success btn-xs' data-status='I' data-MenuKey='".$Sublv3RST['MenuKey']."' ><i class='fas fa-eye' id=''></i></button>";
                        } else {
                            $btnview = "<button type='button' class='btn-view btn btn-success btn-xs' data-status='A' data-MenuKey='".$Sublv3RST['MenuKey']."' ><i class='fas fa-eye-slash' id=''></i></button>";
                        }

                        $output .=   "<tr class='hiderow' data-rowKey='".$Sublv3RST['MenuKey']."' data-UpKey='".$Sublv3RST['UpKey']."' data-MainKey='".$MainMenu."'>
                                        <td>&nbsp;</td>
                                        <td class='text-center'>$menu_lv3</td>
                                        <td style='text-indent: 4rem;'>".$Sublv3RST['MenuIcon']." ".$Sublv3RST['MenuName']."</td>
                                        <td class='text-center'>
                                            $btnview
                                            <button type='button' class='btn-edit btn btn-info btn-xs' data-MenuKey='".$Sublv3RST['MenuKey']."'><i class='fas fa-edit'></i></button>
                                            <button type='button' class='btn-permission btn btn-dark btn-xs' data-MenuKey='".$Sublv3RST['MenuKey']."'><i class='fas fa-tasks'></i></button>
                                            <button type='button' class='btn-delete btn btn-danger btn-xs' data-MenuKey='".$Sublv3RST['MenuKey']."'><i class='fas fa-trash-alt'></i></button>
                                        </td>
                                    </tr>";
                    }

                }
            }

        }
    }
}
if ($_GET['a'] == "callmainmenu"){
    $MenuLv = intval($_POST['LvMenu'])-1;
    if (intval($_POST['LvMenu']) == 2){
        $upKey = " AND upKey = '".$_POST['MainMenu']."' ";
    }else{
        $upKey = " ";
    }
    $sql1 = "SELECT MenuKey,MenuName FROM menus WHERE MenuLv = '".$MenuLv."' ".$upKey." ORDER BY MenuSort";
    $getmenu = MySQLSelectX($sql1);
    $i=0;
    while($MenuList = mysqli_fetch_array($getmenu)) {
        $i++;
        $arrCol[$i]['MenuKey'] = $MenuList['MenuKey'];
        $arrCol[$i]['MenuName'] = $MenuList['MenuName'];
    }
    if ($_POST['LvMenu'] == 1){
        $output = "main_menu";
    }else{
        $output = "sub_menu";
    }
    $arrCol['Loop'] = $i;
    
}
if ($_GET['a'] == 'save'){
    if ($_POST['typeCom'] == "0"){
        $MenuKey = md5(date("YmdHis"));
        if ($_POST['MenuLv'] == "0"){
            $upKey = $MenuKey;
        }else{
            if ($_POST['MenuLv'] == "1"){
                $upKey = $_POST['MainMemu'];
            }else{
                $upKey = $_POST['SubMenu'];
            }
            
        }
        $sql1 = "INSERT INTO menus SET MenuKey = '".$MenuKey."',
                                       UpKey = '".$upKey."',
                                       MenuIcon = '".$_POST['MenuIcon']."',
                                       MenuLv = '".$_POST['MenuLv']."',
                                       MenuName = '".$_POST['MenuName']."',
                                       MenuCase = '".$_POST['MenuCase']."',
                                       MenuLink = '".$_POST['MenuLink']."',
                                       ClassOpen = '1',
                                       MenuSort = ".$_POST['MenuSort'].",
                                       MenuStatus = '".$_POST['MenuStatus']."',
                                       UserCreate = '".$_SESSION['ukey']."',
                                       UserUpdate = '".$_SESSION['ukey']."'";
        // echo $sql1;
        MySQLInsert($sql1);
        $chk = CHKRowDB("SELECT * FROM menus WHERE MenuKey = '".$MenuKey."'");
        if ($chk > 0){
            $output = "เพิ่มข้อมูลเมนูสำเร็จ";
        }else{
            $output = "พบข้อผิดพลาดกรุณาลองใหม่";
        }
    }else{
        switch ($_POST['MenuLv']){
            case '0' :
                $upKey = $_POST['MenuKey'];
            break;
            case '1' :
                $upKey = $_POST['MainMemu'];
            break;
            case '2' :
                $upKey = $_POST['SubMenu'];
            break;
        }
        $sql1 = "UPDATE  menus SET UpKey = '".$upKey."',
                                    MenuIcon = '".$_POST['MenuIcon']."',
                                    MenuLv = ".$_POST['MenuLv'].",
                                    MenuName = '".$_POST['MenuName']."',
                                    MenuCase = '".$_POST['MenuCase']."',
                                    MenuLink = '".$_POST['MenuLink']."',
                                    MenuSort = ".$_POST['MenuSort'].",
                                    MenuStatus = '".$_POST['MenuStatus']."',
                                    UserUpdate = '".$_SESSION['ukey']."'
                 WHERE MenuKey = '".$_POST['MenuKey']."'";
        MySQLUpdate($sql1);
        $output = "แก้ไขข้อมูลเรียบร้อยแล้ว";
    }
}

if ($_GET['a'] == 'view'){
    $sql1 = "UPDATE menus SET MenuStatus = '".$_POST['typeCom']."' WHERE MenuKey = '".$_POST['MenuKey']."'";
    MySQLUpdate($sql1);
}

if ($_GET['a'] == 'edit'){
    $i=0;
    $x=0;
    $sql1 = "SELECT T0.MenuKey,T0.UpKey,T0.MenuIcon,T0.MenuLv,T0.MenuName,T0.MenuCase,T0.MenuLink,T0.MenuSort,T0.MenuStatus FROM menus T0 WHERE T0.MenuKey = '".$_POST['MenuKey']."'";

    $DataMenu = MySQLSelect($sql1);
    switch ($DataMenu['MenuLv']) {
        case '0':
        case '1' :
            $MenuLv1 = $DataMenu['UpKey'];
            $MenuLv2 = "";
            break;
        case '2' :
            $MenuLv2 = $DataMenu['UpKey'];
            $sql1 = "SELECT UpKey FROM menus WHERE MenuKey = '".$DataMenu['UpKey']."'";
            $UpMenu = MySQLSelect($sql1);
            $MenuLv1 = $UpMenu['UpKey'];
            break;
    }
    $sql2 = "SELECT MenuKey,MenuName FROM menus WHERE MenuLv = 0 AND MenuStatus = 'A' ORDER BY MenuSort";
    $getLv1 = MySQLSelectX($sql2);
    while ($optLv1 = mysqli_fetch_array($getLv1)){
        $i++;
        $arrCol[$i]['MenuKeyLv1'] = $optLv1['MenuKey'];
        $arrCol[$i]['MenuNameLv1'] = $optLv1['MenuName'];
    }
    $sql3 = "SELECT * FROM menus WHERE UpKey = '".$MenuLv1."' AND MenuLv = 1 AND MenuStatus = 'A'";
    if (CHKRowDB($sql3) > 0) {
        $sql4 = "SELECT MenuKey,MenuName FROM Menus WHERE UpKey = '".$MenuLv1."' AND MenuLv = 1 AND MenuStatus = 'A' ORDER BY MenuSort";
        $getLv2 = MySQLSelectX($sql4);
        while ($optLv2 = mysqli_fetch_array($getLv2)){
            $x++;
            $arrCol[$x]['MenuKeyLv2'] = $optLv2['MenuKey'];
            $arrCol[$x]['MenuNameLv2'] = $optLv2['MenuName'];
        }
    }
    $arrCol['MenuLv'] = $DataMenu['MenuLv'];
    $arrCol['MenuKey'] = $DataMenu['MenuKey'];
    $arrCol['MenuLv1'] = $MenuLv1;
    $arrCol['MenuLv2'] = $MenuLv2;
    $arrCol['MenuName'] = $DataMenu['MenuName'];
    $arrCol['MenuIcon'] = $DataMenu['MenuIcon'];
    $arrCol['MenuCase'] = $DataMenu['MenuCase'];
    $arrCol['MenuLink'] = $DataMenu['MenuLink'];
    $arrCol['MenuSort'] = $DataMenu['MenuSort'];
    $arrCol['MenuStatus'] = $DataMenu['MenuStatus'];
    $arrCol['LoopLv1'] = $i;
    $arrCol['LoopLv2'] = $x;
    
}

if ($_GET['a'] == 'del'){
    $sql1 = "DELETE FROM menus WHERE MenuKey = '".$_POST['MenuKey']."'";
    MySQLDelete($sql1);
    $chk = CHKRowDB("SELECT * FROM menus WHERE MenuKey = '".$_POST['MenuKey']."'");
    if ($chk == 0){
        $output = "ลบข้อมูลเรียบร้อย";
    }else{
        $output = "เกิดข้อผิดพลาดไม่สามารถลบข้อมูลได้";
    }
}

if ($_GET['a'] == 'permiss'){
    $sql1 = "SELECT MenuIcon,MenuName,TypeOpen FROM menus WHERE MenuKey = '".$_POST['MenuKey']."'"; 
    $MenuHead = MySQLSelect($sql1);
    switch ($MenuHead['TypeOpen']){
        case 'A' :
            $selA = " selected ";
            $selC = " ";
            $selD = " ";
            $selL = " ";
            break;
        case 'C' :
            $selA = " ";
            $selC = " selected ";
            $selD = " ";
            $selL = " ";
            break;
        case 'D' :
            $selA = " ";
            $selC = " ";
            $selD = " selected ";
            $selL = " ";
            break;
        case 'L' :
            $selA = " ";
            $selC = " ";
            $selD = " ";
            $selL = " selected ";
            break;
        
    }
    $output = " 
                    <div class='row'>
                        <div class='col-12'>
                            <h5>กำหนดสิทธิ์การเข้าถึงเมนู ".$MenuHead['MenuName']."</h5>
                            <input type='hidden' id='KeyPermiss' value='".$_POST['MenuKey']."'>
                        </div>
                    </div>
                    <div class='row mt-2'>
                        <div class='col-2'>
                            <label class='form-control-label'>ระดับการเข้าถึง</label>
                        </div>
                        <div class='col-4'>
                            <select id='TypeOpen' class='form-select' onchange=\"CallHead()\">
                                <option value='A' ".$selA.">[A] - เข้าถึงทั้งหมด</option>
                                <option value='C' ".$selC.">[C] - แบ่งสิทธิ์ตาม Class ผู้ใช้งาน</option>
                                <option value='D' ".$selD.">[D] - แบ่งสิทธิ์ตามฝ่าย (DeptCode)</option>
                                <option value='L' ".$selL.">[L] - แบ่งสิทธิ์ตามตำแหน่ง (LvCode)</option>
                            </select>
                        </div>
                        
                    </div>
                    <div id='TbPermiss' class='row mt-2'></div>
                    <hr>
                    <div class='row'>
                        <div class='col-12' id='TbPosition'></div>
                    </div>
    ";
    $output .= "    <script type='text/javascript'>
                        $(document).ready(function(){
                            CallHead();
                        });
                    </script>
                    <script> 
                        function CallHead(){
                            $.ajax({
                                url: 'setting/ajax/ajaxMenu.php?a=datamiss',
                                type: 'POST',
                                data: { MenuKey : $('#KeyPermiss').val(),
                                        TypeOpen : $('#TypeOpen').val(),
                                    },
                                success: function(result) {
                                    var obj = jQuery.parseJSON(result);
                                    $.each(obj,function(key,inval) {
                                        $('#TbPermiss').html(inval['output']);
                                        $('#TbPosition').html(inval['posi']);
                                    });
                                }
                            });
                        }
                        function CallPosition(){
                            $.ajax({
                                url: 'setting/ajax/ajaxMenu.php?a=datalv',
                                type: 'POST',
                                data: { MenuKey : $('#KeyPermiss').val(),
                                        DeptCode : $('#DeptCode').val(),
                                    },
                                success: function(result) {
                                    var obj = jQuery.parseJSON(result);
                                    $.each(obj,function(key,inval) {
                                        $('#TbPosition').html(inval['posi']);
                                    });
                                }
                            });
                        }
                        function AddPermiss(x){
                            var chkData = document.getElementById('chk_'+x).checked;
                            if (chkData){
                                chkvalue = 1;
                            }else{
                                chkvalue = 0;
                            }
                            $.ajax({
                                url: 'setting/ajax/ajaxMenu.php?a=addpermiss',
                                type: 'POST',
                                data: { DataKey: x,
                                        TypeOpen : $('#TypeOpen').val(),
                                        CHK : chkvalue,
                                        MenuKey : $('#KeyPermiss').val(),
                                    },
                                success: function(result) {
                                    var obj = jQuery.parseJSON(result);
                                    $.each(obj,function(key,inval) {
                                        $('#TbPosition').html(inval['posi']);
                                    });
                                }
                            });
                        }
                    </script> ";
}

if ($_GET['a'] == 'datamiss'){
    $sql1 = "UPDATE menus SET TypeOpen = '".$_POST['TypeOpen']."' WHERE MenuKey = '".$_POST['MenuKey']."'";
    MySQLUpdate($sql1);
    if ($_POST['TypeOpen'] != 'A'){
        $sql1 = "SELECT uClass FROM positions ORDER BY uClass DESC LIMIT 1";
        $sql2 = "SELECT ClassOpen FROM menus WHERE MenuKey = '".$_POST['MenuKey']."'";
        $uClass = MySQLSelect($sql1);
        $ClassOpen = MySQLSelect($sql2);
        $ClassX = $ClassOpen['ClassOpen'];
        if (strlen($ClassOpen['ClassOpen']) < $uClass['uClass']){
            for ($i=(strlen($ClassOpen['ClassOpen'])+1);$i<=$uClass['uClass'];$i++){
                $ClassX .= "0";
            }
            $sql1 = "UPDATE menus SET ClassOpen = '".$ClassX."' WHERE MenuKey = '".$_POST['MenuKey']."'";
            MySQLUpdate($sql1);
        }
    }
    switch ($_POST['TypeOpen']){
        case 'A' :
            $sql1 = "SELECT T0.DeptCode,DeptName FROM departments T0 ORDER BY T0.DeptCode ";
            $getDept = MySQLSelectX($sql1);
            $output = "<div class='col-2'><label class='fomr-control-label'>ฝ่าย</label></div>";
            $output.= "<div class='col-4'>";
            while ($Dept = mysqli_fetch_array($getDept)){
                $output .= "<div class='form-check'>
                            <div class='custom-control custome-checkbox'>
                                <input type='checkbox' class='form-check-input form-check-danger' checked disabled>
                                <label class='form-check-label'>".$Dept['DeptName']."</label>
                            </div>
                        </div>";
            }
            $output.= "</div>";
            break;

        case 'C' :
            $sql1 = "SELECT DeptCode,DeptName FROM departments WHERE DeptCode NOT IN ('DP001','DP002') ORDER BY DeptCode ";
            $getDept = MySQLSelectX($sql1);
            $output  = "<div class='col-2'>
                            <label class='form-control-label' for='main_menu'>ฝ่าย</label>
                        </div>
                        <div class='col-4'>
                                <select name='DeptCode' id='DeptCode' class='form-select' onchange=\"CallPosition()\">";

            while ($Dept = mysqli_fetch_array($getDept)){
                if ($Dept['DeptCode'] == 'DP001' || $Dept['DeptCode'] == 'DP002'){
                    $dis = " disabled ";
                }else{
                    $dis = " ";
                }
                $output .= "        <option value='".$Dept['DeptCode']."' ".$dis.">".$Dept['DeptName']."</option>";
            }
            $output .= "        </select>
                            </div>";
        break;
        case 'D' :
            $sql1 = "SELECT T0.DeptCode,DeptName, 
                            CASE WHEN (SELECT COUNT(ID) FROM menugroup A0 WHERE A0.MenuKey = '".$_POST['MenuKey']."' AND A0.DeptCode = T0.DeptCode AND A0.StatusDoc = 'A')  >= 1 THEN 'Y' ELSE 'N' END AS MenuOpen
                     FROM departments T0
                     ORDER BY T0.DeptCode ";
            $getDept = MySQLSelectX($sql1);
            $output = "<div class='col-2'><label class='fomr-control-label'>ฝ่าย</label></div>";
            $output.= "<div class='col-4'>";
            $i=0;                
            while ($Dept = mysqli_fetch_array($getDept)){
                $i++;
                if ($Dept['DeptCode'] == 'DP001' || $Dept['DeptCode'] == 'DP002' || $Dept['MenuOpen'] == 'Y'){
                    $sel = " checked ";
                }else{
                    $sel = " ";
                }
                if ($Dept['DeptCode'] == 'DP001' || $Dept['DeptCode'] == 'DP002'){
                    $dis = " disabled ";
                }else{
                    $dis = " ";
                }
                $output .= "<div class='form-check'>
                                <div class='custom-control custome-checkbox'>
                                    <input type='checkbox' id='chk_".$Dept['DeptCode']."' class='chkbox form-check-input form-check-danger' ".$sel.$dis." onClick=\"AddPermiss('".$Dept['DeptCode']."')\">
                                    <label class='form-check-label'>".$Dept['DeptName']."</label>
                                </div>
                            </div>";
            }
            $output.= "</div>";
        break;
        case 'L' :
            $sql1 = "SELECT DeptCode,DeptName FROM departments WHERE DeptCode NOT IN ('DP001','DP002') ORDER BY DeptCode ";
            $getDept = MySQLSelectX($sql1);
            $output  = "<div class='col-2'>
                            <label class='form-control-label' for='main_menu'>ฝ่าย</label>
                        </div>
                        <div class='col-4'>
                            <select name='DeptCode' id='DeptCode' class='form-select' onchange=\"CallPosition()\">";

            while ($Dept = mysqli_fetch_array($getDept)){
                if ($Dept['DeptCode'] == 'DP001' || $Dept['DeptCode'] == 'DP002'){
                    $dis = " disabled ";
                }else{
                    $dis = " ";
                }
                $output .= "        <option value='".$Dept['DeptCode']."' ".$dis.">".$Dept['DeptName']."</option>";
            }
            $output .= "        </select>
                        </div>";
        break;
    }
    $arrCol['posi'] = "";
}
if ($_GET['a'] == 'datalv'){
    $sql1 = "SELECT T0.LvCode,T0.PositionName,uClass,
                    CASE WHEN (SELECT COUNT(A0.ID) FROM menugroup A0 WHERE A0.MenuKey = '".$_POST['MenuKey']."' AND T0.LvCode = A0.LvCode AND StatusDoc = 'A') >= 1 THEN 'Y' ELSE 'N' END AS MenuOpen
             FROM positions T0
             WHERE T0.DeptCode = '".$_POST['DeptCode']."' ORDER BY T0.LvCode";
    $getLvCode = MySQLSelectX($sql1);
    $posi = " <h6>ตำแหน่ง</h6>";
    while ($DataLv = mysqli_fetch_array($getLvCode)){
        if ($DataLv['MenuOpen'] == 'Y'){
            $chk = " checked ";
        }else{
            $chk = " ";
        }
        $posi .= "<div class='form-check'>
                        <div class='custom-control custome-checkbox'>
                                <input type='checkbox' id='chk_".$DataLv['LvCode']."' class='chkbox form-check-input form-check-danger' ".$chk." onClick=\"AddPermiss('".$DataLv['LvCode']."')\">
                            <label class='form-check-label'>".$DataLv['PositionName']."</label>
                        </div>
                    </div>";

    }
    $arrCol['posi'] = $posi;

    
}
if ($_GET['a'] == 'addpermiss'){
    if ($_POST['CHK'] == 1){
        $CHK = 'A';
    }else{
        $CHK = 'I';
    }
    switch ($_POST['TypeOpen']){
        case 'C' :
            $sql1 = "SELECT uClass FROM positions WHERE LvCode = '".$_POST['DataKey']."'";
            $uClass = MySQLSelect($sql1);
            $sql1 = "SELECT ClassOpen FROM menus WHERE MenuKey = '".$_POST['MenuKey']."'";
            $xClass = MySQLSelect($sql1);
            $NewClass = substr_replace($xClass['ClassOpen'],$_POST['CHK'],($uClass['uClass']+1),1);
            $sql1 = "UPDATE menus SET ClassOpen = '".$NewClass."' WHERE MenuKey = '".$_POST['MenuKey']."'";
            MySQLUpdate($sql1);
            break;
        case 'D' :
            $sql1 = "SELECT * FROM menugroup WHERE MenuKey ='' AND DeptCode = ''";
            if (CHKRowDB($sql1) != 0){
                $sql1 = "UPDATE menugroup SET StatusDoc = '".$CHK."',UserUpdate = '".$_SESSION['ukey']."' WHERE MenuKey = '".$_POST['MenuKey']."' AND DeptCode = '".$_POST['DataKey']."'";
                MySQLUpdate($sql1);
            }else{
                $sql1 = "INSERT INTO menugroup SET MenuKey = '".$_POST['MenuKey']."',
                                                   DeptCode = '".$_POST['DataKey']."',
                                                   StatusDoc = '".$CHK."',
                                                   UserCreate = '".$_SESSION['ukey']."',
                                                   UserUpdate = '".$_SESSION['ukey']."'";
                MySQLUpdate($sql1);
            }
        break;
        case 'L' :
            $sql1 = "SELECT * FROM menugroup WHERE MenuKey ='' AND LvCode = ''";
            if (CHKRowDB($sql1) != 0){
                $sql1 = "UPDATE menugroup SET StatusDoc = '".$CHK."',UserUpdate = '".$_SESSION['ukey']."' WHERE MenuKey = '".$_POST['MenuKey']."' AND LvCode = '".$_POST['DataKey']."'";
                MySQLUpdate($sql1);
            }else{
                $sql1 = "INSERT INTO menugroup SET MenuKey = '".$_POST['MenuKey']."',
                                                LvCode = '".$_POST['DataKey']."',
                                                StatusDoc = '".$CHK."',
                                                UserCreate = '".$_SESSION['ukey']."',
                                                UserUpdate = '".$_SESSION['ukey']."'";
                MySQLUpdate($sql1);
            }
            break;
    }
}

$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
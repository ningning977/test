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
    $sql1 = "SELECT ID,MenuCase,MenuGroup,Pages,CaseStatus,FileCreate 
             FROM menulist 
             ORDER BY MenuGroup,MenuCase";
    $getMenu = MySQLSelectX($sql1);
    $ax=0;
    while($MenuList = mysqli_fetch_array($getMenu)){
        $ax++;
        if($MenuList['CaseStatus'] == "A") {
            $btnview = "<button type='button' class='btn-view btn btn-success btn-xs' data-status='I' data-MenuKey='".$MenuList['ID']."' ><i class='fas fa-eye' id=''></i></button>";
        } else {
            $btnview = "<button type='button' class='btn-view btn btn-success btn-xs' data-status='A' data-MenuKey='".$MenuList['ID']."' ><i class='fas fa-eye-slash' id=''></i></button>";;
        }
        if($MenuList['FileCreate'] == "Y") {
            $btnFile = "<button type='button' class='btn-finish btn btn-secondary btn-xs' data-MenuKey='".$MenuList['ID']."'><i class='far fa-file-code'></i></button>";
        } else {
            $btnFile = "<button type='button' class='btn-create btn btn-warning text-dark btn-xs' data-MenuKey='".$MenuList['ID']."'><i class='fas fa-file-medical'></i></button>";;
        }
        $output .= "<tr>
                        <td class='text-center'>".$ax."</td>
                        <td width='5%' class='text-left'>".$MenuList['MenuCase']."</td>
                        <td>".$MenuList['MenuGroup']."</td>
                        <td>".$MenuList['Pages']."</td>
                        <td class='text-center'>
                            ".$btnview."
                            <button type='button' class='btn-edit btn btn-info btn-xs' data-MenuKey='".$MenuList['ID']."'><i class='fas fa-edit'></i></button>
                            ".$btnFile."
                            <button type='button' class='btn-delete btn btn-danger btn-xs' data-MenuKey='".$MenuList['ID']."'><i class='fas fa-trash-alt'></i></button>
                        </td>
                    </tr>";
    }
}


if ($_GET['a'] == 'createfile'){
    $sql1 = "SELECT MenuCase,MenuGroup FROM menulist WHERE ID = '".$_POST['IDList']."' AND CaseStatus = 'A' AND FileCreate != 'Y'";
    $MenuList = MySQLSelect($sql1);
    if (CHKRowDB($sql1) != 0){
        $MenuFile = $MenuList['MenuCase'];
        $ajaxFile = "ajax".$MenuList['MenuCase'];

        $CreateMenu = fopen("../../menus/".$MenuList['MenuGroup']."/".$MenuFile.".php", 'w');
        $dataC = file_get_contents("../FileCreate/menu.txt");
        fwrite($CreateMenu, $dataC);
        fclose($CreateMenu);

        $CreateMenu = fopen("../../menus/".$MenuList['MenuGroup']."/ajax/".$ajaxFile.".php", 'w');
        $dataC = file_get_contents("../FileCreate/ajax.txt");
        fwrite($CreateMenu, $dataC);
        fclose($CreateMenu);
        $output = "สร้างไฟลล์สำเร็จ ";
    }else{
        $output = "ไม่สามารถสร้างไฟลล์ได้ ทำเองนะจ๊ะ ";
    }
    $sql2 = "UPDATE menulist SET FileCreate = 'Y' WHERE  ID = '".$_POST['IDList']."'";
    MySQLUpdate($sql2);

}

if ($_GET['a'] == 'newlink'){
    if ($_POST['TypeCom'] == 0){
        $sql1 = "SELECT * FROM menulist WHERE UPPER(MenuCase) = '".strtoupper($_POST['MenuCase'])."'";
        if (CHKRowDB($sql1) != 0){
            $output = "Case ซ้ำไม่สามารถสร้างได้ กรุณาสร้างใหม่";
        }else{
            $sql1 = "INSERT INTO menulist SET MenuCase = '".$_POST['MenuCase']."',
                                            MenuGroup = '".$_POST['MenuGroup']."',
                                            Pages = '".$_POST['urlPages']."',
                                            UserCreate = '".$_SESSION['ukey']."',
                                            UserUpdate = '".$_SESSION['ukey']."'";
            MySQLInsert($sql1);
            $sql2 = "SELECT * FROM menulist WHERE MenuCase = '".$_POST['MenuCase']."' AND MenuGroup = '".$_POST['MenuGroup']."'";
            if (CHKRowDB($sql2) == 0){
                $output = "เกิดข้อผิดพลาดกรุณาลองใหม่ นะจ๊ะ";
            }else{
                $output = "สร้างลิงค์เมนูเรียบร้อยแล้ว";
            }
        }
    }else{
        $sql1 = "UPDATE menulist SET MenuCase = '".$_POST['MenuCase']."',
                                     MenuGroup = '".$_POST['MenuGroup']."',
                                     Pages = '".$_POST['urlPages']."',
                                     CaseStatus = '".$_POST['CaseStatus']."',
                                     UserUpdate = '".$_SESSION['ukey']."'
                 WHERE ID = '".$_POST['CaseID']."'";
        MySQLUpdate($sql1);

        $output = "แก้ไขลิงค์เมนูเรียบร้อยแล้ว";
    }
}
if ($_GET['a']=='edit'){
    $sql1 = "SELECT ID,MenuCase,MenuGroup,Pages,CaseStatus FROM menulist WHERE ID = '".$_POST['ID']."'";
    $MenuData = MySQLSelect($sql1);
    $arrCol['CaseID'] = $MenuData['ID'];
    $arrCol['MenuCase'] = $MenuData['MenuCase'];
    $arrCol['MenuGroup'] = $MenuData['MenuGroup'];
    $arrCol['Pages'] = $MenuData['Pages'];
    $arrCol['CaseStatus'] = $MenuData['CaseStatus'];
    $arrCol['TypeCom'] = 1;
}
if ($_GET['a'] == 'del'){
    $sql1 = "DELETE FROM menulist WHERE ID = '".$_POST['ID']."'";
    MySQLDelete($sql1);
    
    if (CHKRowDB("SELECT * FROM menulist WHERE ID = '".$_POST['ID']."'") == 0) {
        $output = "ลบข้อมูลเรียบร้อยแล้ว";
    }else{
        $output = "เกิดข้อผิดพลาดกรณาลองใหม่";
    }
}
if ($_GET['a'] == 'view'){
    $sql1 = "UPDATE menulist SET CaseStatus = '".$_POST['typeCom']."' WHERE ID = '".$_POST['MenuKey']."'";
    MySQLUpdate($sql1);
}


$arrCol['output'] = $output;

array_push($resultArray,$arrCol);
echo json_encode($resultArray);
?>
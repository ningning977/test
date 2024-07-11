<?php
include('../../../core/config.core.php');
include('../../../core/functions.core.php');
date_default_timezone_set('Asia/Bangkok');
session_start();

$resultArray = array();
$arrCol = array();

function sectoTime($second) {
    $s = $second%60;
    if($s < 10) { $s = "0".$s; } else { $s; }
    $h = floor(($second%86400)/3600);
    if($h < 10) { $h = "0".$h; } else { $h; }
    $m = floor(($second%3600)/60);
    if($m < 10) { $m = "0".$m; } else { $m; }
    $d = floor(($second%2592000)/86400);
    if($d > 0) {
        return "$d วัน $h:$m:$s";
    } else {
        return "$h:$m:$s";
    }
}

$MyIPAddr = gethostbyaddr($_SERVER['REMOTE_ADDR']);

    $filt_server = $_POST['server'];
    switch($filt_server) {
        case "ERFSERVER_MAIN": 
            $srvrname = DB_HOST;
            $dtbsname = DB_NAME;
            $username = DB_USERNAME;
            $password = DB_PASSWORD;
        break;
        case "SAPSERVER_OLD": 
            $srvrname = OLD_HOST;
            $dtbsname = OLD_NAME;
            $username = OLD_USERNAME;
            $password = OLD_PASSWORD;
            $msdriver = MS_DRV;
        break;
        case "SAPSERVER_NEW": 
            $srvrname = SAP_HOST;
            $dtbsname = SAP_NAME;
            $username = SAP_USERNAME;
            $password = SAP_PASSWORD;
            $msdriver = MS_DRV;
        break;
        case "HRMISERVER":
            $srvrname = HRMI_HOST;
            $dtbsname = HRMI_NAME;
            $username = HRMI_USERNAME;
            $password = HRMI_PASSWORD;
            $msdriver = MS_DRV;
        break;
    }

    switch ($filt_server) {
        case 'ERFSERVER_MAIN':
            $erf_conn = new mysqli($srvrname, $username, $password, $dtbsname);
            $erf_conn->set_charset("utf8");
            $sqls = "SHOW FULL PROCESSLIST";
            $stmt = $erf_conn->query($sqls);
            $row = 0;
            while($result = $stmt->fetch_assoc()) {
                $HostArr = explode(":",$result['Host']);

                if($HostArr[0] == "localhost") {
                    $arrCol[$row]['trClass'] = " class='table-danger text-danger'";
                } else {
                    $arrCol[$row]['trClass'] = "";
                }

                $arrCol[$row]['PROCESSLIST_HOST']    = $HostArr[0];
                $arrCol[$row]['PROCESSLIST_DB']      = $result['db'];
                $arrCol[$row]['PROCESSLIST_COMMAND'] = $result['Command'];
                $arrCol[$row]['PROCESSLIST_TIME']    = sectoTime($result['Time']);
                $arrCol[$row]['PROCESSLIST_STATE']   = $result['State'];
                $arrCol[$row]['PROCESSLIST_INFO']    = $result['Info'];
                $row++;
            }
            break;

        default:
            $sap_conn = odbc_connect("DRIVER=$msdriver;charset=UTF8;SERVER=$srvrname;DATABASE=$dtbsname",$username,$password) or die ("Cannot Connect to SAP Database!");
            // $sqls = "select '".$_SESSION['uName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', r.session_id, r.status, DATEDIFF(second,r.start_time,GETDATE()) AS 'Time', s.login_name, c.client_net_address, s.host_name, s.program_name, st.text from sys.dm_exec_requests r inner join sys.dm_exec_sessions s on r.session_id = s.session_id left join sys.dm_exec_connections c on r.session_id = c.session_id outer apply sys.dm_exec_sql_text(r.sql_handle) st where c.client_net_address NOT IN ('10.0.0.1') ORDER BY DATEDIFF(second,r.start_time,GETDATE()) DESC";
            $sqls = "select '".$_SESSION['uName']."' AS 'Query Name', '".$_SERVER['REMOTE_ADDR']."' AS 'Query IP', r.session_id, r.status, DATEDIFF(second,r.start_time,GETDATE()) AS 'Time', s.login_name, c.client_net_address, s.host_name, s.program_name, st.text from sys.dm_exec_requests r inner join sys.dm_exec_sessions s on r.session_id = s.session_id left join sys.dm_exec_connections c on r.session_id = c.session_id outer apply sys.dm_exec_sql_text(r.sql_handle) st ORDER BY DATEDIFF(second,r.start_time,GETDATE()) ASC";
            $stmt = odbc_exec($sap_conn,$sqls);
            $row = 0;
            while ($result = odbc_fetch_array($stmt)) {
                $arrCol[$row]['client_net_address'] = $result['client_net_address'];
                $arrCol[$row]['host_name']          = $result['host_name'];
                $arrCol[$row]['program_name']       = $result['program_name'];
                $arrCol[$row]['Time']               = sectoTime($result['Time']);
                $arrCol[$row]['status']             = $result['status'];
                $arrCol[$row]['text']               = $result['text'];
                $row++;
            }
            odbc_close($sap_conn);
            break;
    }
    $arrCol['row'] = $row;
    array_push($resultArray,$arrCol);
    echo json_encode($resultArray);
    /* get rows */
    //$rows = $stmt->num_rows-1;
    /* get checker ipaddress */
    // $sqlc = "SELECT DISTINCT T0.IPAddress FROM checkertable T0 ORDER BY T0.TableID ASC";
    // $stmc = $conn->query($sqlc);
    // echo $sqlc;
    //$checker = array();
    ?>

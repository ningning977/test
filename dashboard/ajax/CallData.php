<?php
    $cYear = date("Y");
    $cMonth = intval(date("m"));

    $sqlTeam = "SELECT T0.ID, T0.TeamCode, T0.TrgAmount, CONCAT(T1.uName, ' ', T1.uLastName, ' ', '(', T1.uNickName, ')') AS SlpName, T1.uNickName, T2.MainTeam
                FROM teamtarget T0
                LEFT JOIN users T1 ON T1.uKey = T0.UkeyHead
                LEFT JOIN teamcode T2 ON T2.TeamCode = T0.TeamCode
                WHERE T0.DocYear = ".$cYear." AND T0.DocStatus = 'A' AND T0.TeamCode NOT LIKE 'PTA%'
                ORDER BY T0.ID";

    $sqlTeamQRY = MySQLSelectX($sqlTeam);
    $nTeam = 0;
    while($resultTeam = mysqli_fetch_array($sqlTeamQRY)) {
        ++$nTeam;
        $TeamCode[$nTeam] = $resultTeam['TeamCode']; // -> ทีมโค้ด
        $MainTeam[$resultTeam['TeamCode']] = $resultTeam['MainTeam']; // -> ชื่อทีม
        $SlpName[$resultTeam['TeamCode']] = $resultTeam['SlpName']; // -> ชื่อหัวหน้าเซลล์
        $Slp_nName[$resultTeam['TeamCode']] = $resultTeam['uNickName']; // -> ชื่อเล่นหัวหน้าเซลล์
        $TrgAmount[$resultTeam['TeamCode']] = $resultTeam['TrgAmount']; // -> เป้าขายทีม

        $groupTarget[$resultTeam['TeamCode']] = 0; // -> รวม เป้าการขาย
        $groupReal[$resultTeam['TeamCode']] = 0; // -> รวม % เป้าชดเชย
        $groupOld[$resultTeam['TeamCode']] = 0; // -> เป้าสะสมเดือนแรกจนถึงเดือนปัจจุบัน รวม ยอดขาย
        $groupTargetM[$resultTeam['TeamCode']] = 0; // -> เป้าสะสมเดือนแรกจนถึงเดือนปัจจุบัน รวม เป้าการขาย
        $newTarGroup[$resultTeam['TeamCode']] = 0; // -> รวม เป้าชดเชยไตรมาส
    }

    // $nTeam
    for($i = 1; $i <= $nTeam; $i++) {
        $sqlTKey = "SELECT * FROM saletarget WHERE DocYear = ".$cYear." AND TeamCode = '".$TeamCode[$i]."' AND DocStatus != 'I'";
        // echo $sqlTKey;
        $sqlTKeyQRY = MySQLSelectX($sqlTKey);
        $No = 0;
        while($resultTKey = mysqli_fetch_array($sqlTKeyQRY)) {
            $DataSlp = slpCodeData($TeamCode[$i],$resultTKey['Ukey']);
             //echo $TeamCode[$i]."-".$resultTKey['Ukey']."-".$DataSlp['SlpCode'];

            //----------------------------------- ยอดรวมเดือนปัจจุบัน -------------------------- //
            $sqlSAP1 = "SELECT P1.cMonth,sum(P1.Data1) AS fDATA
                        FROM(
                            SELECT W1.cMonth,(sum(DocTotal)-sum(VatSum)) AS Data1
                            FROM(
                                SELECT month(OINV.docdate) AS cMonth,OINV.DocTotal,OINV.VatSum 
                                FROM OINV LEFT JOIN OSLP ON OINV.SlpCode = OSLP.SlpCode 
                                WHERE (year(OINV.docdate)= '".$cYear."' AND month(OINV.docdate) = '".$cMonth."') AND OSLP.[SlpCode] IN ".$DataSlp['SlpCode']." AND OINV.CANCELED = 'N'
                            ) W1
                            GROUP BY W1.cMonth
                            UNION ALL
                            SELECT W2.cMonth,-1*(sum(DocTotal)-sum(VatSum)) AS Data1
                            FROM(
                                SELECT month(ORIN.docdate) AS cMonth,ORIN.DocTotal,ORIN.VatSum 
                                FROM ORIN LEFT JOIN OSLP ON ORIN.SlpCode = OSLP.SlpCode 
                                WHERE (year(ORIN.docdate)= '".$cYear."' AND month(ORIN.docdate) = '".$cMonth."') AND OSLP.[SlpCode] IN ".$DataSlp['SlpCode']." AND ORIN.CANCELED = 'N'
                            ) W2
                            GROUP BY W2.cMonth
                        ) P1
                        GROUP BY P1.cMonth";
                        //echo $sqlSAP1."<br>";
            $sqlSAP1QRY = SAPSelect($sqlSAP1);
            $resultSAP1 = odbc_fetch_array($sqlSAP1QRY);

            //----------------------------------- เป้าสะสม (ไม่รวมเดือนปัจจุบัน) -------------------------- //
            $sqlSAP2 = "SELECT sum(P1.Data1) AS fDATA
                        FROM(
                            SELECT W1.cMonth,(sum(DocTotal)-sum(VatSum)) AS Data1
                            FROM(
                                SELECT month(OINV.docdate) AS cMonth,OINV.DocTotal,OINV.VatSum 
                                FROM OINV LEFT JOIN OSLP ON OINV.SlpCode = OSLP.SlpCode 
                                WHERE (year(OINV.docdate)= '".$cYear."' AND month(OINV.docdate) < '".$cMonth."') AND OSLP.[SlpCode] IN ".$DataSlp['SlpCode']." AND OINV.CANCELED = 'N'
                            ) W1
                            GROUP BY W1.cMonth
                            UNION ALL
                            SELECT W2.cMonth,-1*(sum(DocTotal)-sum(VatSum)) AS Data1
                            FROM(
                                SELECT month(ORIN.docdate) AS cMonth,ORIN.DocTotal,ORIN.VatSum 
                                FROM ORIN LEFT JOIN OSLP ON ORIN.SlpCode = OSLP.SlpCode 
                                WHERE (year(ORIN.docdate)= '".$cYear."' AND month(ORIN.docdate) < '".$cMonth."') AND OSLP.[SlpCode] IN ".$DataSlp['SlpCode']." AND ORIN.CANCELED = 'N'
                            ) W2
                            GROUP BY W2.cMonth
                        ) P1";
            $sqlSAP2QRY = SAPSelect($sqlSAP2);
            $resultSAP2 = odbc_fetch_array($sqlSAP2QRY);

            // echo "Team : ".$TeamCode[$i]." | เป้าสะสม : ".$resultSAP2['fDATA']."<br>";

            //----------------------------------- SlpName AND LvCode ของเซลล์ -------------------------- //
            if($DataSlp['SlpCode'] == '(23)' OR $DataSlp['SlpCode'] == '(24)' OR $DataSlp['SlpCode'] == '(158)') {
                switch ($DataSlp['SlpCode']) {
                    case '(23)': $resultSale['LvCode'] = 'LV045'; $resultSale['SlpName'] = "โฮมโปร-ฝากขาย"; break;
                    case '(24)': $resultSale['LvCode'] = 'LV045'; $resultSale['SlpName'] = "ไทวัสดุ-ฝากขาย"; break;
                    case '(158)': $resultSale['LvCode'] = 'LV045'; $resultSale['SlpName'] = "เกศศินี ฝากขาย เมกาโฮม"; break;
                }
            }else{
                $resultSale = MySQLSelect("SELECT T0.LvCode, CONCAT(T0.uName, ' ', T0.uLastName, ' ', '(', T0.uNickName, ')') AS SlpName FROM users T0 WHERE T0.uKey = '".$resultTKey['Ukey']."'");
            }
            if ($resultSale['LvCode'] == 'LV027' OR $resultSale['LvCode'] == 'LV030' OR $resultSale['LvCode'] == 'LV032' OR $resultSale['LvCode'] == 'LV028' OR $resultSale['LvCode'] == 'LV052'){
                if(isset($resultSAP1['fDATA']))    {  }else{ $resultSAP1['fDATA'] = 0; }
                if($cMonth < 10){
                    $TKeyMonth = $resultTKey["M0".$cMonth]; // -> เป้าขายเซลล์เดือนปัจจุบัน
                }else{
                    $TKeyMonth = $resultTKey["M".$cMonth]; // -> เป้าขายเซลล์เดือนปัจจุบัน
                }
                if($resultSAP1['fDATA'] != 0 OR $TKeyMonth != 0) {
                    ++$No;
                    $saleName[$TeamCode[$i]][$No] = $resultSale['SlpName']; // -> ชื่อเซลล์
                    $saleTarget[$TeamCode[$i]][$No] = $TKeyMonth; // -> เป้าขายเซลล์เดือนปัจจุบัน
                    $saleST[$TeamCode[$i]][$No] = $resultTKey["DocStatus"]; // -> สถานะเซลล์

                    // $resultSAP1['fDATA'] ยอดรวมเดือนปัจจุบัน
                    if(isset($resultSAP1['fDATA'])) {
                        $saleREAL[$TeamCode[$i]][$No] = $resultSAP1['fDATA'];
                    }else{
                        $saleREAL[$TeamCode[$i]][$No] = 0;
                    }

                    // เงื่อไข SaleTarget
                    if ($saleTarget[$TeamCode[$i]][$No] == 0){
                        $PerCentSale[$TeamCode[$i]][$No] = 0;
                    }else{
                        $PerCentSale[$TeamCode[$i]][$No]  = ($saleREAL[$TeamCode[$i]][$No]/$saleTarget[$TeamCode[$i]][$No])*100;
                    }

                    // เป้าขายรวมของเซลล์
                    $groupTarget[$TeamCode[$i]] = $groupTarget[$TeamCode[$i]]+$saleTarget[$TeamCode[$i]][$No]; // -> รวม เป้าการขาย/คน
                    $groupReal[$TeamCode[$i]] = $groupReal[$TeamCode[$i]] + $saleREAL[$TeamCode[$i]][$No]; // -> รวม % เป้าชดเชย

                    // $resultSAP2['fDATA'] เป้าสะสม (ไม่รวมเดือนปัจจุบัน)
                    $saleAll = $resultSAP2['fDATA'];
                    
                    // เป้าชดเชยไตรมาส
                    switch ($cMonth) {
                        case 1:
                            $resultTar = MySQLSelect("SELECT M01 FROM saletarget WHERE DocYear = ".$cYear." AND TeamCode = '".$TeamCode[$i]."' AND UKey = '".$resultTKey['Ukey']."' AND DocStatus != 'I'");
                            $newTar[$TeamCode[$i]][$No] = $resultTar["M01"];
                            break;
                        case 2:
                            $resultTar = MySQLSelect("SELECT M01, M02 FROM saletarget WHERE DocYear = ".$cYear." AND TeamCode = '".$TeamCode[$i]."' AND UKey = '".$resultTKey['Ukey']."' AND DocStatus != 'I'");
                            $diffTar = $resultTar["M01"] - $saleAll;
                            if ($diffTar < 0){
                                $newTar[$TeamCode[$i]][$No] = $resultTar["M02"];
                            }else{
                                $newTar[$TeamCode[$i]][$No] = $resultTar["M02"] + ($diffTar/2);
                            }
                            break;
                        default:
                            $Month = "";
                            for($m = 1; $m <= $cMonth-1; $m++) {
                                if($m < 10){
                                    $Month .= "M0".$m."+";
                                }else{
                                    $Month .= "M".$m."+";
                                }
                            }
                            $Month = substr($Month,0,-1);
                            if($cMonth < 10){
                                $Month .= " AS Mx, M0".$cMonth;
                            }else{
                                $Month .= " AS Mx, M".$cMonth;
                            }
                            $resultTar = MySQLSelect("SELECT ".$Month." FROM saletarget WHERE DocYear = ".$cYear." AND TeamCode = '".$TeamCode[$i]."' AND UKey = '".$resultTKey['Ukey']."' AND DocStatus != 'I'");
                            $diffTar = $resultTar['Mx'] - $saleAll;
                            if ($diffTar < 0){
                                if($cMonth < 10){
                                    $newTar[$TeamCode[$i]][$No] = $resultTar["M0".$cMonth];
                                }else{
                                    $newTar[$TeamCode[$i]][$No] = $resultTar["M".$cMonth];
                                }
                            }else{
                                switch ($cMonth) {
                                    case 3: $newTar[$TeamCode[$i]][$No] = $resultTar["M03"] + ($diffTar); break;
                                    case 4: $newTar[$TeamCode[$i]][$No] = $resultTar["M04"] + ($diffTar/3); break;
                                    case 5: $newTar[$TeamCode[$i]][$No] = $resultTar["M05"] + ($diffTar/2); break;
                                    case 6: $newTar[$TeamCode[$i]][$No] = $resultTar["M06"] + ($diffTar); break;
                                    case 7: $newTar[$TeamCode[$i]][$No] = $resultTar["M07"] + ($diffTar/3); break;
                                    case 8: $newTar[$TeamCode[$i]][$No] = $resultTar["M08"] + ($diffTar/2); break;
                                    case 9: $newTar[$TeamCode[$i]][$No] = $resultTar["M09"] + ($diffTar); break;
                                    case 10: $newTar[$TeamCode[$i]][$No] = $resultTar["M10"] + ($diffTar/3); break;
                                    case 11: $newTar[$TeamCode[$i]][$No] = $resultTar["M11"] + ($diffTar/2); break;
                                    case 12: $newTar[$TeamCode[$i]][$No] = $resultTar["M12"] + ($diffTar); break;
                                }
                            }
                            
                            break;
                    }
                    $newTarGroup[$TeamCode[$i]] = $newTarGroup[$TeamCode[$i]] + $newTar[$TeamCode[$i]][$No]; // -> รวม เป้าชดเชยไตรมาส
                    if ($newTar[$TeamCode[$i]][$No] != 0){
                        $newPrct[$TeamCode[$i]][$No] = ($resultSAP1['fDATA']/$newTar[$TeamCode[$i]][$No])*100; // -> % เป้าชดเชย
                    }else{
                        $newPrct[$TeamCode[$i]][$No] = 0;
                    }
                }  
                // echo "Team : ".$TeamCode[$i]."->".$resultTKey['Ukey']." | SlpCode : ".$DataSlp['SlpCode']."<br>";
            }else{
                ++$No;
                $saleName[$TeamCode[$i]][$No] = $resultSale['SlpName']; // -> ชื่อเซลล์
                if($cMonth < 10){
                    $saleTarget[$TeamCode[$i]][$No] = $resultTKey["M0".$cMonth]; // -> เป้าขายเซลล์เดือนปัจจุบัน
                }else{
                    $saleTarget[$TeamCode[$i]][$No] = $resultTKey["M".$cMonth]; // -> เป้าขายเซลล์เดือนปัจจุบัน
                }
                $saleST[$TeamCode[$i]][$No] = $resultTKey["DocStatus"]; // -> สถานะเซลล์

                // $resultSAP1['fDATA'] ยอดรวมเดือนปัจจุบัน
                if(isset($resultSAP1['fDATA'])) {
                    $saleREAL[$TeamCode[$i]][$No] = $resultSAP1['fDATA'];
                }else{
                    $saleREAL[$TeamCode[$i]][$No] = 0;
                }
                
                // เงื่อไข SaleTarget
                if ($saleTarget[$TeamCode[$i]][$No] == 0){
                    $PerCentSale[$TeamCode[$i]][$No] = 0;
                }else{
                    $PerCentSale[$TeamCode[$i]][$No]  = ($saleREAL[$TeamCode[$i]][$No]/$saleTarget[$TeamCode[$i]][$No])*100;
                }

                // เป้าขายรวมของเซลล์
                $groupTarget[$TeamCode[$i]] = $groupTarget[$TeamCode[$i]]+$saleTarget[$TeamCode[$i]][$No]; // -> รวม เป้าการขาย/คน
                $groupReal[$TeamCode[$i]] = $groupReal[$TeamCode[$i]] + $saleREAL[$TeamCode[$i]][$No]; // -> รวม % เป้าชดเชย

                // $resultSAP2['fDATA'] เป้าสะสม (ไม่รวมเดือนปัจจุบัน)
                $saleAll = $resultSAP2['fDATA'];  
                
                // $newTar[$TeamCode[$i]][$No] เป้าชดเชยไตรมาส
                switch ($cMonth) {
                    case 1:
                        $resultTar = MySQLSelect("SELECT M01 FROM saletarget WHERE DocYear = ".$cYear." AND TeamCode = '".$TeamCode[$i]."' AND UKey = '".$resultTKey['Ukey']."' AND DocStatus != 'I'");
                        $newTar[$TeamCode[$i]][$No] = $resultTar["M01"];
                        break;
                    case 2:
                        $resultTar = MySQLSelect("SELECT M01, M02 FROM saletarget WHERE DocYear = ".$cYear." AND TeamCode = '".$TeamCode[$i]."' AND UKey = '".$resultTKey['Ukey']."' AND DocStatus != 'I'");
                        $diffTar = $resultTar["M01"] - $saleAll;
                        if ($diffTar < 0){
                            $newTar[$TeamCode[$i]][$No] = $resultTar["M02"];
                        }else{
                            $newTar[$TeamCode[$i]][$No] = $resultTar["M02"] + ($diffTar/2);
                        }
                        break;
                    default:
                        $Month = "";
                        for($m = 1; $m <= $cMonth-1; $m++) {
                            if($m < 10){
                                $Month .= "M0".$m."+";
                            }else{
                                $Month .= "M".$m."+";
                            }
                        }
                        $Month = substr($Month,0,-1);
                        if($cMonth < 10){
                            $Month .= " AS Mx, M0".$cMonth;
                        }else{
                            $Month .= " AS Mx, M".$cMonth;
                        }
                        $resultTar = MySQLSelect("SELECT ".$Month." FROM saletarget WHERE DocYear = ".$cYear." AND TeamCode = '".$TeamCode[$i]."' AND UKey = '".$resultTKey['Ukey']."' AND DocStatus != 'I'");
                        $diffTar = $resultTar['Mx'] - $saleAll;
                        if ($diffTar < 0){
                            if($cMonth < 10){
                                $newTar[$TeamCode[$i]][$No] = $resultTar["M0".$cMonth];
                            }else{
                                $newTar[$TeamCode[$i]][$No] = $resultTar["M".$cMonth];
                            }
                        }else{
                            switch ($cMonth) {
                                case 3: $newTar[$TeamCode[$i]][$No] = $resultTar["M03"] + ($diffTar); break;
                                case 4: $newTar[$TeamCode[$i]][$No] = $resultTar["M04"] + ($diffTar/3); break;
                                case 5: $newTar[$TeamCode[$i]][$No] = $resultTar["M05"] + ($diffTar/2); break;
                                case 6: $newTar[$TeamCode[$i]][$No] = $resultTar["M06"] + ($diffTar); break;
                                case 7: $newTar[$TeamCode[$i]][$No] = $resultTar["M07"] + ($diffTar/3); break;
                                case 8: $newTar[$TeamCode[$i]][$No] = $resultTar["M08"] + ($diffTar/2); break;
                                case 9: $newTar[$TeamCode[$i]][$No] = $resultTar["M09"] + ($diffTar); break;
                                case 10: $newTar[$TeamCode[$i]][$No] = $resultTar["M10"] + ($diffTar/3); break;
                                case 11: $newTar[$TeamCode[$i]][$No] = $resultTar["M11"] + ($diffTar/2); break;
                                case 12: $newTar[$TeamCode[$i]][$No] = $resultTar["M12"] + ($diffTar); break;
                            }
                        }

                        break;
                }
                $newTarGroup[$TeamCode[$i]] = $newTarGroup[$TeamCode[$i]] + $newTar[$TeamCode[$i]][$No]; // -> รวม เป้าชดเชยไตรมาส
                if ($newTar[$TeamCode[$i]][$No] != 0){
                    if(isset($resultSAP1['fDATA'])) {  }else{
                        $resultSAP1['fDATA'] = 0;
                    }
                    $newPrct[$TeamCode[$i]][$No] = ($resultSAP1['fDATA']/$newTar[$TeamCode[$i]][$No])*100; // -> % เป้าชดเชย
                }else{
                    $newPrct[$TeamCode[$i]][$No] = 0;
                }
                // echo "Team : ".$TeamCode[$i]." | ".$resultSale['SlpName']."->".$resultTKey['Ukey']." | SlpCode : ".$DataSlp['SlpCode']." | เป้าชดเชย".$newTar[$TeamCode[$i]][$No]."<br>";
            }
            $groupOld[$TeamCode[$i]] = $groupOld[$TeamCode[$i]] + $resultSAP2['fDATA']; // -> เป้าสะสมเดือนแรกจนถึงเดือนปัจจุบัน รวม ยอดขาย/คน
            $groupTargetM[$TeamCode[$i]] = ($TrgAmount[$TeamCode[$i]] * ($cMonth-1))/12; // -> เป้าสะสมเดือนแรกจนถึงเดือนปัจจุบัน รวม เป้าการขาย/คน

            // if($TeamCode[$i] == 'MT100') {
            //     echo "Team : ".$TeamCode[$i]." | ".$resultSale['SlpName']."->".$resultTKey['Ukey']." | SlpCode : ".$DataSlp['SlpCode']." เป้าการขาย : ".$newTarGroup[$TeamCode[$i]]."<br>";
            // }
        }

        
        $sqlSlpCode = "SELECT T0.SlpCode,T0.SlpName
                        FROM oslp T0
                        WHERE T0.TeamCode LIKE '".$TeamCode[$i]."%' AND CodeStatus != 'A'
                        ORDER BY T0.SlpCode";
        $qrySlpCode = MySQLSelectX($sqlSlpCode);
        $CHKRow[$TeamCode[$i]] = CHKRowDB($sqlSlpCode);
        

        if($CHKRow[$TeamCode[$i]] != 0) {
            $SlpCodeDf = "(";
            while($resultSlpCode = mysqli_fetch_array($qrySlpCode)) {
                $SlpCodeDf .= $resultSlpCode['SlpCode'].",";
            }
            $SlpCode = (substr($SlpCodeDf,0,-1)).")";
            // echo "Team : ".$TeamCode[$i]." -> พนักงานลาออก : ".$CHKRow[$TeamCode[$i]]." คน SlpCode : ".$SlpCode."<br>";

            $sqlSlpC = "SELECT P1.cMonth,sum(P1.Data1) AS fDATA
                        FROM (
                            SELECT W1.cMonth,(sum(DocTotal)-sum(VatSum)) AS Data1
                            FROM (
                                SELECT month(OINV.docdate) AS cMonth,OINV.DocTotal,OINV.VatSum 
                                FROM OINV LEFT JOIN OSLP ON OINV.SlpCode = OSLP.SlpCode 
                                WHERE (year(OINV.docdate)= '".$cYear."' AND month(OINV.docdate) = '10') AND OSLP.[SlpCode] IN ".$SlpCode." AND OINV.CANCELED = 'N'
                            ) W1
                            GROUP BY W1.cMonth
                            UNION ALL
                            SELECT W2.cMonth,-1*(sum(DocTotal)-sum(VatSum)) AS Data1
                            FROM (
                                SELECT month(ORIN.docdate) AS cMonth,ORIN.DocTotal,ORIN.VatSum 
                                FROM ORIN LEFT JOIN OSLP ON ORIN.SlpCode = OSLP.SlpCode 
                                WHERE (year(ORIN.docdate)= '".$cYear."' AND month(ORIN.docdate) = '10') AND OSLP.[SlpCode] IN ".$SlpCode." AND ORIN.CANCELED = 'N'
                            ) W2
                            GROUP BY W2.cMonth
                        ) P1
                            GROUP BY P1.cMonth";
            $sqlSlpCQRY = SAPSelect($sqlSlpC);
            $resultSlpC = odbc_fetch_array($sqlSlpCQRY);
            if(isset($resultSlpC['fDATA'])) {  }else{ $resultSlpC['fDATA'] = 0; }
            $groupMReturn[$TeamCode[$i]] = $resultSlpC['fDATA']; // -> ยอดรวมของพนักงานลาออก
        }else{
            $groupMReturn[$TeamCode[$i]] = 0;
        }
        $varData[$TeamCode[$i]] = $No; // -> จำนวนพนักงานขายที่ไม่ใช่สถานะ 'I'
        // echo "<br>";
    }
    // echo "รวมเป้าชดเชย : ".$newTarGroup['MT200'];
?>
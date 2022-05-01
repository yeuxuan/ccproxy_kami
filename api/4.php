<?php
    //取得指定位址的内容，并储存至text
    $text=file_get_contents('33.html');
    //取出div标籤且id为PostContent的内容，并储存至阵列match
    preg_match_all('/<input .* name="username" .* value="(.*?)"/ui', $text, $match);
    preg_match_all('/<input .* name="password" .* value="(.*?)"/ui', $text, $match2);
    preg_match_all('/<input .* name="enable" .*/', $text, $match3);
    preg_match_all('/<input .* name="usepassword" .*/', $text, $match4);
    preg_match_all('/<input .* name="disabledate" .* value="(.*?)"/ui', $text, $match5);
    preg_match_all('/<input .* name="disabletime" .* value="(.*?)"/ui', $text, $match6);
    preg_match_all('/<input .* name="autodisable" .*/', $text, $match7);
    $ccp=array();
    $time=date("Y-m-d H:i:s");
    foreach($match[1] as $key => $use){
        str_replace(array("<",">","/"),array(""),$match3[0][$key])=='input type="checkbox" name="enable" value="1" checked=""'?$match3[0][$key]=0:$match3[0][$key]=1;
        str_replace(array("<",">","/"),array(""),$match4[0][$key])=='input type="checkbox" name="usepassword" value="1" checked=""'?$match4[0][$key]=0:$match4[0][$key]=1;
        str_replace(array("<",">","/"),array(""),$match7[0][$key])=='input type="checkbox" name="autodisable" value="1" checked=""'?$match7[0][$key]=0:$match7[0][$key]=1;
        $ccp[$key]=array(
            "user"=> $match[1][$key],
            "pwd"=> $match2[1][$key],
            "state"=> $match3[0][$key],
            "pwdstate"=> $match4[0][$key],
            "disabletime"=> $match5[1][$key]." ".$match6[1][$key],
            "expire"=>strtotime($time)>strtotime($match5[1][$key]." ".$match6[1][$key])?1:0,
        );
    }
    // print_r($ccp);
    $column="adm456n";
     $result = array_filter($ccp, function ($where) use ($column) {
        return $where['user'] == $column;
    });
   // print_r($result);
    $col=array_column($result,'user');
     print_r($col[0]==""?1:0);
    //取得第一个img，并储存至阵列match2
    //印出match2[0]
    //print_r($match[1]);
//     

//    
//     //取得第一个img，并储存至阵列match2
//     //印出match2[0]
//    // print_r($match2[1]);

    // print_r($user);

   

    // print_r($match3[0]);

//    foreach($match3[0] as $key=>$a){
//     if(){
//          echo "0".str_replace(array("<",">","/"),array(""),$match3[0][$key])."<br>";
//     }
//     else{
//         echo "1:".str_replace(array("<",">","/"),array(""),$match3[0][$key])."<br>";
//     }
//    }
   

?>
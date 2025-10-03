<?php
/*
 * @Author: yihua
 * @Date: 2022-06-25 21:02:04
 * @LastEditTime: 2025-01-05 16:25:26
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\api\api.php
 * 一花一叶 一行代码
 * Copyright (c) 2022 by yihua 487735913@qq.com, All Rights Reserved. 
 */
include("../includes/common.php");
@header('Content-Type: application/json; charset=UTF-8');
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;
switch($act){
    case "gethostapp":
    $application=$DB->selectV2("select appcode,appname from application where username=?",[$subconf["username"]]);
    if($application){
        $code=[
            "code"=>"1",
            "msg"=>$application
        ];
    }
    else{
        $code=[
            "code"=>"0",
            "msg"=>"未知错误"
        ];
    }
    exit(json_encode($code,JSON_UNESCAPED_UNICODE));
    break;
    default:
    exit('{"code":-4,"msg":"No Act"}');
    break;
}

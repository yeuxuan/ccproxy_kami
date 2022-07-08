<?php
include("../includes/common.php");
@header('Content-Type: application/json; charset=UTF-8');
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;
switch($act){
    case "gethostapp":
    $sql="select appcode,appname from application where username=\"".$subconf["username"]."\"";
    $application=$DB->select($sql);
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

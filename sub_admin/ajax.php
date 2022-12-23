<?php
require_once("../includes/Task.php");
require_once("../includes/Scheduler.php");
include("../includes/common.php");
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;
@header('Content-Type: application/json; charset=UTF-8');

//ajax.php?act=getfakatool

switch ($act) {
    case 'getserver':
        $sql = 'select id,ip,comment from server_list where username=\'' . $subconf['username'] . '\' ';
        $server_list = $DB->select($sql);
        $code = [
            "code" => "1",
            "msg" => $server_list
        ];
        exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        break;
    case 'newapp':
        $server = $_POST['server'];
        $username = $_POST['username'];
        $sql = 'select appname from application';
        $dist_name = $DB->select($sql);
        // print_r($dist_name);
        $flag = true;
        foreach ($dist_name as $key => $name) {
            if ($username == $name['appname']) {
                $flag = false;
            }
        }
        if ($flag) {
            $appcode = md5(uniqid(mt_rand(), 1) . time());
            $arr = array(
                'appname'  => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $username)),
                'appcode' => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $appcode)),
                'serverip'     => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $server)),
                'username' => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $subconf['username'])),
            );
            $exec = $DB->insert('application', $arr);
            if ($exec) {
                $code = [
                    "code" => "1",
                    "msg" => "添加成功"
                ];

                // $cxserver=$DB->selectRow("SELECT applist FROM server_list WHERE ip='".addslashes($server)."'");
            
                // $sqlserver="UPDATE server_list set applist='".((empty($cxserver['applist'])?"":$cxserver['applist'].",").$appcode)."' where ip='".addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $server))."' ";
    
                // $result = $DB->exe($sqlserver);

                WriteLog("添加用户", "添加了" . $username, $subconf['username'], $DB);


            } else {
                $code = [
                    "code" => "-1",
                    "msg" => "添加失败"
                ];
            }
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "0",
                "msg" => "应用名重复"
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }

        break;

    case "apptable":
        $sqlj = "";
        if (isset($_REQUEST['page']) && isset($_REQUEST['limit']) && isset($_REQUEST['server']) && isset($_REQUEST['appname'])) {
            //服务器sql
            $sqlj .= $_REQUEST['server'] != "" && $_REQUEST['server'] != "*" ? "and serverip=\"" . $_REQUEST['server'] . "\"" : "";
            //应用名字搜索
            $sqlj .= $_REQUEST['appname'] != "" ? " and appname LIKE '%" . $_REQUEST["appname"] . "%'" : "";
            //  $sqlj .= $_REQUEST['appname'] != "" ? " and appname=\"" . $_REQUEST['appname'] . "\"" : "";
            $sql = 'SELECT appid,appcode,appname,serverip,found_time FROM application where username=\'' . $subconf['username'] . '\' ' . $sqlj . ' ';
           
            // // $DB->pageNo=$_REQUEST['page'];当前页码
            // //$DB->pageRows=$_REQUEST['limit'];多少行数
            $countpage = $DB->selectRow("select count(*) as num from application where username=\"" . $subconf['username'] . "\"");
           
            $app = $DB->selectPage($sql, $DB->pageNo = $_REQUEST['page'], $DB->pageRows = $_REQUEST['limit']);
            
            foreach ($app as $key => $apps) {
                $app[$key]['appid'] = $key + 1;
            }

            $json = ["code" => "0", "count" => $countpage['num'], "data" => $app, "icon" => "1"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        } else {
            $json = ["code" => "-1", "count" => null, "data" => "参数错误！", "icon" => "5"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "delapp":
        if (isset($_POST['appcode'])) {

            // $getServer=$DB->select("SELECT id,applist FROM server_list");
                
            // foreach($getServer as $item)
            // {
            //     $strArr=explode(",",$item["applist"]);
            //     $applist="";
            //     foreach($strArr as $app)
            //     {
            //         if($app==$_POST['appcode'])
            //         {
            //             continue;
            //         }
            //         if(!empty($app))
            //         {
            //             $applist.=$app;
            //         }
            //     }
            //     $updateServer=$DB->exe("UPDATE server_list SET applist='".$applist."' where id=".$item["id"]."");
            //     var_dump("UPDATE server_list SET applist='".$applist."' where id=".$item["id"]."");
            // }

            $exesql = $DB->delete("application", "where appcode=\"" . $_REQUEST['appcode'] . "\"");

            

            if ($exesql) {
                $code = [
                    "code" => "1",
                    "msg" => "删除成功"
                ];

               

               


                WriteLog("删除应用", "删除了" . $_REQUEST['appcode'], $subconf['username'], $DB);
                exit(json_encode($code, JSON_UNESCAPED_UNICODE));
            } else {
                $code = [
                    "code" => "0",
                    "msg" => "未知错误"
                ];
                exit(json_encode($code, JSON_UNESCAPED_UNICODE));
            }
        } else {
            $json = ["code" => "-1", "count" => null, "data" => "参数错误！", "icon" => "5"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "seldel":
        if (!isset($_POST['item'])) {
            $code = [
                "code" => "-1",
                "msg" => "删除失败"
            ];
            WriteLog("删除失败", "删除失败参数为空" . $_POST['item'], $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        $arr = $_POST['item'];
        $execs = 0;
        $execf = 0;
        for ($i = 0; $i < count($arr); $i++) {
            $exesql = $DB->delete("application", "where appcode=\"" . $arr[$i] . "\"");
            if ($exesql) {
                $execs++;
            } else {
                $execf++;
            }
        }
        if ($execs == count($arr)) {
            $code = [
                "code" => "1",
                "msg" => "删除成功"
            ];
            WriteLog("删除", "删除了" . $_POST['item'], $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "1",
                "msg" => "删除成功：" . $execs . "删除失败：" . $execf,
            ];
            WriteLog("删除", "删除了" . $_POST['item'], $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "serverdel":
        $arr = $_POST['item'];
        $execs = 0;
        $execf = 0;
        for ($i = 0; $i < count($arr); $i++) {
            $exesql = $DB->delete("server_list", "where ip=\"" . $arr[$i] . "\"");
            if ($exesql) {
                $execs++;
            } else {
                $execf++;
            }
        }
        if ($execs == count($arr)) {
            $code = [
                "code" => "1",
                "msg" => "删除成功"
            ];
            WriteLog("删除", "删除了" . $_POST['item'], $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "1",
                "msg" => "删除成功：" . $execs . "删除失败：" . $execf,
            ];
            WriteLog("删除", "删除了" . $_POST['item'], $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "update":
        // .addslashes($_REQUEST['serverip'])." WHERE appcode=".$_REQUEST['appcode'].
        if (isset($_REQUEST['appcode']) && isset($_REQUEST['appname']) && isset($_REQUEST['serverip'])) {
            $sql = "UPDATE application SET appname=\"" . addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $_REQUEST['appname'])) . "\",serverip=\"" . addslashes($_REQUEST['serverip']) . "\" WHERE appcode=\"" . $_REQUEST['appcode'] . "\" ";
            $result = $DB->exe($sql);

            $cxserver=$DB->selectRow("SELECT applist FROM server_list WHERE ip='".addslashes($_REQUEST['serverip'])."'");
            

            if ($result) {
                $code = [
                    "code" => "1",
                    "msg" => "更新成功！"
                ];
                
                // $sqlserver="UPDATE server_list set applist='".((empty($cxserver['applist'])?"":$cxserver['applist'].",").$_REQUEST['appcode'])."' where ip='".addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $_REQUEST['serverip']))."' ";

                // $result = $DB->exe($sqlserver);

                WriteLog("更新", "更新了" . $_REQUEST['appname'], $subconf['username'], $DB);
                exit(json_encode($code, JSON_UNESCAPED_UNICODE));
            } else {
                $code = [
                    "code" => "-1",
                    "msg" => "更新失败！"
                ];
                exit(json_encode($code, JSON_UNESCAPED_UNICODE));
            }
        } else {
            $json = ["code" => "-1", "msg" => "参数错误！"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "servertable":
        // print_r($_REQUEST);
        $sqlj = "";
        if (isset($_REQUEST['page']) && isset($_REQUEST['limit']) && isset($_REQUEST['ip']) && isset($_REQUEST['comment'])) {
            //服务器IP
            $sqlj .= $_REQUEST['ip'] != "" ? "and ip=\"" . $_REQUEST['ip'] . "\"" : "";
            $sqlj .= $_REQUEST['comment'] != "" ? " and comment LIKE '%" . $_REQUEST["comment"] . "%'" : "";
            // $sqlj .= $_REQUEST['comment'] != "" ? " and comment=\"" . $_REQUEST['comment'] . "\"" : "";
            $sql = 'SELECT id,ip,serveruser,password,cport,state,comment FROM server_list where username=\'' . $subconf['username'] . '\' ' . $sqlj . ' ';
            // // $DB->pageNo=$_REQUEST['page'];当前页码
            // //$DB->pageRows=$_REQUEST['limit'];多少行数
            $countpage = $DB->selectRow("select count(*) as num from server_list where username=\"" . $subconf['username'] . "\"");
            $app = $DB->selectPage($sql, $DB->pageNo = $_REQUEST['page'], $DB->pageRows = $_REQUEST['limit']);
            // foreach ($app as $key => $apps) {
            //     $app[$key]['id'] = $key + 1;
            // }
            $json = ["code" => "0", "count" => $countpage['num'], "data" => $app, "icon" => 1];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        } else {
            $json = ["code" => "-1", "count" => null, "data" => "参数错误！", "icon" => "5"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "newserver":

        $serverip = $_POST['serverip'];
        $ccpusername = $_POST['ccpusername'];
        $ccppassword = $_POST['ccppassword'];
        $ccpport = $_POST['ccpport'];
        $state = $_POST['state'] == null ? "0" : "1";
        $comment = $_POST['comment'];

        $sql = 'select ip from server_list';
        $dist_ip = $DB->select($sql);
        // print_r($dist_ip);
        $flag = true;
        foreach ($dist_ip as $key => $name) {
            if ($serverip == $name['ip']) {
                $flag = false;
            }
        }
      
        if ($flag) {
            $valid=count(explode(".",$serverip));
            if($valid<2){
                $json = [
                    "code" => "-1",
                    "msg" => "输入了错误的IP或者域名",
                    "icon"=>"5"
                ];
                exit(json_encode($json, JSON_UNESCAPED_UNICODE));
            }

            if(!ValidPort($ccpport)){
                $json = [
                    "code" => "-1",
                    "msg" => "输入了错误的端口号",
                    "icon"=>"5"
                ];
                exit(json_encode($json, JSON_UNESCAPED_UNICODE));
            }
            $arr = array(
                'ip'  => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $serverip)),
                'serveruser'  => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $ccpusername)),
                'password'  => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $ccppassword)),
                'cport'  => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $ccpport)),
                'state'  => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $state)),
                'comment'  => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $comment)),
                'username' => addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $subconf['username']))
            );
            //print_r($arr);
            $exec = $DB->insert('server_list', $arr);
            if ($exec) {
                $code = [
                    "code" => "1",
                    "msg" => "添加成功"
                ];
                WriteLog("添加服务器", "添加了一个服务器" . $serverip, $subconf['username'], $DB);
            } else {
                $code = [
                    "code" => "-1",
                    "msg" => "添加失败"
                ];
            }
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "0",
                "msg" => "服务器IP重复"
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "upswitch":
        if (isset($_POST['ip']) && isset($_POST["state"])) {
            $sql = "UPDATE server_list SET state=\"" . addslashes($_POST["state"]) . "\" WHERE ip=\"" . addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $_POST['ip'])) . "\" ";
            $result = $DB->exe($sql);
            if ($result) {
                $code = [
                    "code" => "1",
                    "msg" => "更新成功"
                ];
                WriteLog("操作开关", "开关" . $_POST['ip'], $subconf['username'], $DB);
            } else {
                $code = [
                    "code" => "0",
                    "msg" => "更新失败"
                ];
            }
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "0",
                "msg" => "参数错误"
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "getkami":
        $sqlj = "";
        if (isset($_REQUEST['page']) && isset($_REQUEST['limit']) && isset($_REQUEST['code']) && isset($_REQUEST['found_date']) && isset($_REQUEST['use_date']) && isset($_REQUEST['sc_user']) && isset($_REQUEST['state']) && isset($_REQUEST['comment']) && isset($_REQUEST['app'])) {
            $sqlj .= $_REQUEST['code'] != "" ? "and kami=\"" . $_REQUEST['code'] . "\"" : "";
            $sqlj .= $_REQUEST['found_date'] != "" ? " and found_date=\"" . $_REQUEST['found_date'] . "\"" : "";
            $sqlj .= $_REQUEST['use_date'] != "" ? " and use_date=\"" . $_REQUEST['use_date'] . "\"" : "";
            $sqlj .= $_REQUEST['sc_user'] != "" ? " and sc_user=\"" . $_REQUEST['sc_user'] . "\"" : "";
            $sqlj .= $_REQUEST['state'] != "" ? " and state=\"" . $_REQUEST['state'] . "\"" : "";
            $sqlj .= $_REQUEST['comment'] != "" ? " and comment=\"" . $_REQUEST['comment'] . "\"" : "";
            $sqlj .= $_REQUEST['app'] != "" ? " and app=\"" . $_REQUEST['app'] . "\"" : "";
            $sqlj .= " order by found_date desc";
            $sql = 'SELECT * FROM kami where host=\'' . $subconf['siteurl'] . '\' ' . $sqlj . ' ';
            //  print($sql);
            // // $DB->pageNo=$_REQUEST['page'];当前页码
            // //$DB->pageRows=$_REQUEST['limit'];多少行数
            $countpage = $DB->selectRow("select count(*) as num from kami where sc_user=\"" . $subconf['username'] . "\"");
            $app = $DB->selectPage($sql, $DB->pageNo = $_REQUEST['page'], $DB->pageRows = $_REQUEST['limit']);
            foreach ($app as $key => $apps) {
                $app[$key]['id'] = $key + 1;
                if ($app[$key]['state'] == 1) {
                    $app[$key]['state'] = "<span style='color:red'>已激活</span>";
                } else {
                    $app[$key]['state'] = "<span style='color:green'>未激活</span>";
                }
                $app[$key]['times']=KamiPaeseString($app[$key]['times']);
            }

            $json = ["code" => "0", "count" => $countpage['num'], "data" => $app, "icon" => "1"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        } else {
            $json = ["code" => "-1", "count" => null, "data" => "参数错误！", "icon" => "5"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "newkami":
        if (isset($_POST['app']) && isset($_POST['qianzhui']) && isset($_POST["duration"]) && isset($_POST["kamidur"]) && isset($_POST["kaminum"]) && isset($_POST["comment"]) && isset($_POST["kamilen"])&& isset($_POST["connection"])&& isset($_POST["bandwidthup"])&& isset($_POST["bandwidthdown"])) {
            // $sql="UPDATE server_list SET state=\"".addslashes($_POST["state"])."\" WHERE ip=\"".addslashes(str_replace(array("<",">","/"),array("&lt;","&gt;",""),$_POST['ip']))."\" ";
            // $result=$DB->exe($sql);

            if(!empty($_POST["connection"])&&!is_numeric($_POST["connection"])){
                exit(json_encode( $code = [ "code" => "-1",  "msg" => "输入类型错误",  "kami" => ""], JSON_UNESCAPED_UNICODE));
            }

            if(!empty($_POST["bandwidthup"])&&!is_numeric($_POST["bandwidthup"])){
                exit(json_encode( $code = [ "code" => "-1",  "msg" => "输入类型错误",  "kami" => ""], JSON_UNESCAPED_UNICODE));
            }

            if(!empty($_POST["bandwidthdown"])&&!is_numeric($_POST["bandwidthdown"])){
                exit(json_encode( $code = [ "code" => "-1",  "msg" => "输入类型错误",  "kami" => ""], JSON_UNESCAPED_UNICODE));
            }

            if(!empty($_POST["kamidur"])){
                if(intval($_POST["kamidur"])<1){
                    exit(json_encode( $code = [ "code" => "-1",  "msg" => "自定义时长不能小于1,也不能为小数！",  "kami" => ""], JSON_UNESCAPED_UNICODE));
                }
                $vlidnum=count(explode(".",$_POST["kamidur"]));
                
                if( $vlidnum>=2)
                {
                    exit(json_encode( $code = [ "code" => "-1",  "msg" => "自定义时长不能为小数！",  "kami" => ""], JSON_UNESCAPED_UNICODE));
                }

            }

            $kamidurdangwei="+".intval((!empty($_POST["kamidur"])?$_POST["kamidur"]:$_POST["duration"]));

            $kamicount=0;

            if(isset($_POST["year"]) && $_POST["year"]=="on")
            {
                $kamidurdangwei.=" year";
                $kamicount++;
            }

            if(isset($_POST["month"]) && $_POST["month"]=="on")
            {
                $kamidurdangwei.=" month";
                $kamicount++;
            }

            if(isset($_POST["day"]) && $_POST["day"]=="on")
            {
                $kamidurdangwei.=" day";
                $kamicount++;
            }

            if(isset($_POST["hour"]) && $_POST["hour"]=="on")
            {
                $kamidurdangwei.=" hour";
                $kamicount++;
            }

            if($kamicount!=1)
            {
                exit(json_encode( $code = [ "code" => "-1",  "msg" => "请选择卡密类型",  "kami" => ""], JSON_UNESCAPED_UNICODE));
            }

            $kami = array();
            for ($i = 0; $i < $_POST["kaminum"]; $i++) {
                $kami[$i] = array(
                    "kami" => random($_POST["kamilen"] == "" ? 16 : $_POST["kamilen"], $_POST['qianzhui'] == "" ? null : $_POST['qianzhui'])
                );
            }

            if(empty($_POST["connection"])||$_POST["connection"]<=0){
                $_POST["connection"]=-1;
            }
            if(empty($_POST["bandwidthup"])||$_POST["bandwidthup"]<=0){
                $_POST["bandwidthup"]=-1;
            }else
            {
                $_POST["bandwidthup"]*=1024;
            }
            if(empty($_POST["bandwidthdown"])||$_POST["bandwidthdown"]<=0){
                $_POST["bandwidthdown"]=-1;
            }else{
                $_POST["bandwidthdown"]*=1024;
            }
            $flag = true;
            $ext=[
                "connection"=>empty($_POST["connection"])?-1:(int)$_POST["connection"],
                "bandwidthup"=>empty($_POST["bandwidthup"])?-1:(int)$_POST["bandwidthup"],
                "bandwidthdown"=>empty($_POST["bandwidthdown"])?-1:(int)$_POST["bandwidthdown"]
            ];
            foreach ($kami as $key => $ka) {
                $arr = array(
                    'kami'  => $kami[$key]["kami"],
                    'times'  => $kamidurdangwei,
                    //'times'  => $_POST["duration"] == -1 ? ($_POST["kamidur"]<1?round($_POST["kamidur"],1):$_POST["kamidur"]) : $_POST["duration"],
                    'host'  => $subconf['siteurl'],
                    'sc_user'  => $subconf['username'],
                    'state'  => 0,
                    'app'  => $_POST["app"],
                    'comment'  => $_POST["comment"],
                    'ext'=>json_encode($ext)
                );
                //print_r($arr);
                $exec = $DB->insert('kami', $arr);
                if (!$exec) {
                    $flag = false;
                }
            }
            if ($flag) {
                if (isset($_POST['copy'])) {
                    $code = [
                        "code" => "2",
                        "msg" => "更新成功",
                        "kami" => $kami
                    ];
                    WriteLog("卡密", "卡密" . $_POST['app'], $subconf['username'], $DB);
                } else {
                    $code = [
                        "code" => "1",
                        "msg" => "更新成功"
                    ];
                    WriteLog("卡密", "卡密" . $_POST['app'], $subconf['username'], $DB);
                }
            } else {
                $code = [
                    "code" => "0",
                    "msg" => "更新失败"
                ];
            }
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "0",
                "msg" => "参数错误"
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "getapp":
        $sql = 'SELECT appcode,appname FROM application where username=\'' . $subconf['username'] . '\' ';
        $query = $DB->select($sql);
        $code = [
            "code" => "1",
            "msg" => $query
        ];
        exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        break;
    case "delkami":
        $arr = $_POST['item'];
        if ($arr == null || !(isset($arr)) || empty($arr)) {
            $code = [
                "code" => "-1",
                "msg" => "删除失败参数为空!",
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        $execs = 0;
        $execf = 0;
        for ($i = 0; $i < count($arr); $i++) {
            $exesql = $DB->delete("kami", "where kami=\"" . $arr[$i] . "\"");
            if ($exesql) {
                $execs++;
            } else {
                $execf++;
            }
        }
        if ($execs == count($arr)) {
            $code = [
                "code" => "1",
                "msg" => "删除成功"
            ];
            WriteLog("删除卡密", "卡密" . $arr, $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "1",
                "msg" => "删除成功：" . $execs . "删除失败：" . $execf,
            ];
            WriteLog("删除卡密", "卡密" . $arr, $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "updatepwd":
        if (isset($_POST['out_password']) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
            if ($_POST["password"] != $_POST["confirm_password"]) {
                $code = [
                    "code" => "-1",
                    "msg" => "二次密码不一致"
                ];
            } else {
                if ($_POST['out_password'] == $_POST["confirm_password"]) {
                    $code = [
                        "code" => "-3",
                        // "msg" => "三次次密码一致"
                        "msg" => "与原密码一致"
                    ];
                } else {
                    if ($subconf['password'] == $_POST['out_password']) {
                        $sql = "UPDATE sub_admin SET password=\"" . addslashes($_POST["confirm_password"]) . "\" WHERE username=\"" . addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $subconf['username'])) . "\" ";
                        //print($sql);
                        $result = $DB->exe($sql);
                        if ($result) {
                            $code = [
                                "code" => "1",
                                "msg" => "更新成功"
                            ];
                            WriteLog("修改密码", "密码" . $subconf['password'], $subconf['username'], $DB);
                        } else {
                            $code = [
                                "code" => "0",
                                "msg" => "更新失败"
                            ];
                        }
                    } else {
                        $code = [
                            "code" => "-2",
                            "msg" => "原密码不正确"
                        ];
                    }
                }
            }
        } else {
            $code = [
                "code" => "0",
                "msg" => "参数错误"
            ];
        }
        exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        break;
    case "updateset":
        $result = ['user_key', 'kf', 'pan', 'ggswitch', 'wzgg', 'logo'];
        $gg = 1;
        if (!isset($_POST['ggswitch'])) {
            array_splice($result, 3, 1); //删除数组的ggswictch
            $gg = 0;
        }
        $flag = true;
        foreach ($result as $post) {
            $flag = isset($_POST[$post == "wzgg" ? "user_key" : $post]);
        }
        if ($flag) {
            // $gg==true?"ggswitch='".$gg."'":"ggswitch='".$gg."'";
            $sql = "UPDATE sub_admin SET hostname=\"" . addslashes($_POST["user_key"]) . "\", kf=\"" . addslashes($_POST["kf"]) . "\", pan=\"" . addslashes($_POST["pan"]) . "\", img=\"" . addslashes($_POST["logo"]) . "\"  ";
            $sql .= ",ggswitch='" . $gg . "'";
            $sql .= $gg == 0 ? "" : ",wzgg='" . trim(addslashes(str_replace(array("'"), array('"'), $_POST["wzgg"]))) . "'";
            $sql .= " WHERE username=\"" . addslashes(str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $subconf['username'])) . "\" ";
            // print($sql);
            $result = $DB->exe($sql);
            if ($result) {
                $code = [
                    "code" => "1",
                    "msg" => "保存成功"
                ];
                WriteLog("更新网站设置", "设置内容不详", $subconf['username'], $DB);
            } else {
                $code = [
                    "code" => "0",
                    "msg" => "更新失败"
                ];
            }
        } else {
            $code = [
                "code" => "0",
                "msg" => "参数错误"
            ];
        }
        exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        break;
    case "getuserall":
        $sqlj = "";
        if (isset($_REQUEST['page']) && isset($_REQUEST['limit'])) {
            if ($_REQUEST['user'] == "") {
                if ($_REQUEST['app'] == "") {

                    //搜索全部服务器
                    $ser = SerchearchAllServer("", "", $DB);
                    $user_data = array();
                    while ($ser->valid()) {
                        // print_r($ser->current());
                        array_push($user_data, $ser->current());
                        $ser->next();
                    }
                    $result = array_reduce($user_data, function ($result, $value) {
                        return array_merge($result, array_values($value));
                    }, array());
                    $user_updata = array();
                    foreach ($result as $key => $value) {
                        $getdata = array(
                            "id" => $value['id'],
                            "user" => $value['user'],
                            "pwd" => $value['pwd'],
                            "state" => $value['state'],
                            "pwdstate" => $value['pwdstate'],
                            "disabletime" => $value['autodisable']==0?'2099-10-13 14:34:26':$value['disabletime'],
                            "expire" => $value['autodisable']==0?0:$value['expire'],
                            "user" => $value['user'],
                            'serverip' => $value["serverip"],
                            'appname' => $DB->selectRow("SELECT appname FROM application WHERE serverip='" . $value["serverip"] . "'")['appname'],
                            "connection"=>$value['connection']==-1?"无限制":$value['connection'],
                            "bandwidthup"=>$value['bandwidthup']==-1?"无限制":($value['bandwidthup']<-1?$value['bandwidthup']:$value['bandwidthup']/1024),
                            "bandwidthdown"=>$value['bandwidthdown']==-1?"无限制":($value['bandwidthdown']<-1?$value['bandwidthdown']:$value['bandwidthdown']/1024)
                        );
                        array_push($user_updata, $getdata);
                    }
                    //print_r($result);
                    $json = ["code" => "0", "count" => count($user_updata), "data" => $user_updata, "icon" => 1];
                    exit(json_encode($json, JSON_UNESCAPED_UNICODE));


                    // $sql = 'SELECT * FROM server_list WHERE username=\'' . $subconf['username'] . '\' LIMIT 1';
                    // // $countpage=$DB->selectRow("select count(*) as num from server_list where username=\"".$subconf['username']."\"");
                    // //$app=$DB->selectPage($sql,$DB->pageNo=$_REQUEST['page'], $DB->pageRows=$_REQUEST['limit']);
                    // $user_list = $DB->select($sql);
                    // $user_arr = queryuserall($user_list[0]['password'], $user_list[0]['cport'], $user_list[0]['ip']);
                    // $start = ($_REQUEST['page'] - 1) * $_REQUEST['limit']; //偏移量，当前页-1乘以每页显示条数
                    // $user_arr2 = array_slice($user_arr, $start, $_REQUEST['limit']);
                    // $json = ["code" => "0", "count" => count($user_arr), "data" => $user_arr2, "icon" => 1];
                    // exit(json_encode($json, JSON_UNESCAPED_UNICODE));
                } else {
                    // 这里是选择应用的
                    $ser = SerchearchAllServer($_REQUEST['app'], "", $DB);
                    // print_r($_REQUEST['app']);
                    $user_data = array();
                    while ($ser->valid()) {
                        // print_r($ser->current());
                        array_push($user_data, $ser->current());
                        $ser->next();
                    }
                    $result = array_reduce($user_data, function ($result, $value) {
                        return array_merge($result, array_values($value));
                    }, array());
                    $user_updata = array();
                    foreach ($result as $key => $value) {
                        $getdata = array(
                            "id" => $value['id'],
                            "user" => $value['user'],
                            "pwd" => $value['pwd'],
                            "state" => $value['state'],
                            "pwdstate" => $value['pwdstate'],
                            "disabletime" => $value['autodisable']==0?'2099-10-13 14:34:26':$value['disabletime'],
                            "expire" => $value['autodisable']==0?0:$value['expire'],
                            "user" => $value['user'],
                            'serverip' => $value["serverip"],
                            'appname' => $DB->selectRow("SELECT appname FROM application WHERE serverip='" . $value["serverip"] . "'")['appname'],
                            "connection"=>$value['connection']==-1?"无限制":$value['connection'],
                            "bandwidthup"=>$value['bandwidthup']==-1?"无限制":($value['bandwidthup']<-1?$value['bandwidthup']:$value['bandwidthup']/1024),
                            "bandwidthdown"=>$value['bandwidthdown']==-1?"无限制":($value['bandwidthdown']<-1?$value['bandwidthdown']:$value['bandwidthdown']/1024)
                        );
                        array_push($user_updata, $getdata);
                    }
                    //print_r($result);
                    $json = ["code" => "0", "count" => count($user_updata), "data" => $user_updata, "icon" => 1];
                    exit(json_encode($json, JSON_UNESCAPED_UNICODE));
                    // $ip = $DB->selectRow("select serverip from application where appcode='" . $_REQUEST['app'] . "'");
                    // //print_r($ip);
                    // $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'"); //$ip['serverip']服务器IP
                    // $user_arr = queryuserall($server['password'], $server['cport'], $server['ip']);
                    // $start = ($_REQUEST['page'] - 1) * $_REQUEST['limit']; //偏移量，当前页-1乘以每页显示条数
                    // $user_arr2 = array_slice($user_arr, $start, $_REQUEST['limit']);
                    // // print_r($user_arr2);
                    // $json = ["code" => "0", "count" => count($user_arr), "data" => $user_arr2, "icon" => 1];
                    // exit(json_encode($json, JSON_UNESCAPED_UNICODE));
                    // //print_r($server);
                }
            } else {
                if ($_REQUEST['app'] != "") {
                    // 这里是选择应用的
                    $ser = SerchearchAllServer($_REQUEST['app'], $_REQUEST['user'], $DB);
                    $user_data = array();
                    while ($ser->valid()) {
                        //    print_r($ser->current());
                        //    print_r("4564");
                        array_push($user_data, $ser->current());
                        $ser->next();
                    }
                    $result = array_reduce($user_data, function ($result, $value) {
                        return array_merge($result, array_values($value));
                    }, array());
                    $user_updata = array();
                    foreach ($result as $key => $value) {
                        $getdata = array(
                            "id" => $value['id'],
                            "user" => $value['user'],
                            "pwd" => $value['pwd'],
                            "state" => $value['state'],
                            "pwdstate" => $value['pwdstate'],
                            "disabletime" => $value['autodisable']==0?'2099-10-13 14:34:26':$value['disabletime'],
                            "expire" => $value['autodisable']==0?0:$value['expire'],
                            "user" => $value['user'],
                            'serverip' => $value["serverip"],
                            'appname' => $DB->selectRow("SELECT appname FROM application WHERE serverip='" . $value["serverip"] . "'")['appname'],
                            "connection"=>$value['connection']==-1?"无限制":$value['connection'],
                            "bandwidthup"=>$value['bandwidthup']==-1?"无限制":($value['bandwidthup']<-1?$value['bandwidthup']:$value['bandwidthup']/1024),
                            "bandwidthdown"=>$value['bandwidthdown']==-1?"无限制":($value['bandwidthdown']<-1?$value['bandwidthdown']:$value['bandwidthdown']/1024)
                        );
                        array_push($user_updata, $getdata);
                    }
                    //print_r($result);
                    $json = ["code" => "0", "count" => count($user_updata), "data" => $user_updata, "icon" => 1];
                    exit(json_encode($json, JSON_UNESCAPED_UNICODE));
                    // $ip = $DB->selectRow("select serverip from application where appcode='" . $_REQUEST['app'] . "'");
                    // //  print_r($ip);
                    // $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'"); //$ip['serverip']服务器IP
                    // $user_arr = queryuserall($server['password'], $server['cport'], $server['ip']);
                    // $start = ($_REQUEST['page'] - 1) * $_REQUEST['limit']; //偏移量，当前页-1乘以每页显示条数
                    // $user_arr2 = array_slice($user_arr, $start, $_REQUEST['limit']);
                    // $user_arr = userquery($_REQUEST['user'], $user_arr2);
                    // // print_r($user_arr);
                    // // print_r($user_arr2);
                    // $json = ["code" => "0", "count" => count($user_arr), "data" => $user_arr, "icon" => 1];
                    // exit(json_encode($json, JSON_UNESCAPED_UNICODE));
                } else {
                    //搜索用户
                    $ser = SerchearchAllServer($_REQUEST['app'], $_REQUEST['user'], $DB);
                    $user_data = array();
                    while ($ser->valid()) {
                        // print_r($ser->current());
                        array_push($user_data, $ser->current());
                        $ser->next();
                    }
                    //合并数组
                    $result = array_reduce($user_data, function ($result, $value) {
                        return array_merge($result, array_values($value));
                    }, array());
                    $user_updata = array();
                    foreach ($result as $key => $value) {
                        $getdata = array(
                            "id" => $value['id'],
                            "user" => $value['user'],
                            "pwd" => $value['pwd'],
                            "state" => $value['state'],
                            "pwdstate" => $value['pwdstate'],
                            "disabletime" => $value['autodisable']==0?'2099-10-13 14:34:26':$value['disabletime'],
                            "expire" => $value['autodisable']==0?0:$value['expire'],
                            "user" => $value['user'],
                            'serverip' => $value["serverip"],
                            'appname' => $DB->selectRow("SELECT appname FROM application WHERE serverip='" . $value["serverip"] . "'")['appname'],
                            "connection"=>$value['connection']==-1?"无限制":$value['connection'],
                            "bandwidthup"=>$value['bandwidthup']==-1?"无限制":($value['bandwidthup']<-1?$value['bandwidthup']:$value['bandwidthup']/1024),
                            "bandwidthdown"=>$value['bandwidthdown']==-1?"无限制":($value['bandwidthdown']<-1?$value['bandwidthdown']:$value['bandwidthdown']/1024)
                        );
                        array_push($user_updata, $getdata);
                    }
                    // print_r($user_updata);
                    //添加字段
                    // array_walk($result, function (&$value, $key, $zidu) {
                    //     $value = array_merge($value, $zidu);
                    // },$zidu);
                    //$DB->selectRow("SELECT appname FROM application WHERE serverip='124.223.42.168'")['appname']



                    $json = ["code" => "0", "count" => count($user_updata), "data" => $user_updata, "icon" => 1];
                    exit(json_encode($json, JSON_UNESCAPED_UNICODE));
                }
            }
        } else {
            $json = ["code" => "-1", "count" => null, "data" => "参数错误！", "icon" => "5"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "userupdate":
        //UserUpdate()
        $usermodel = $_POST["usermodel"];
        if (isset($usermodel) && is_array($usermodel) && !empty($usermodel)) {
            

            if(!empty($usermodel["connection"])&&!is_numeric($usermodel["connection"])){
                exit(json_encode( $code = [ "code" => "-1",  "msg" => "输入类型错误",  "kami" => $kami], JSON_UNESCAPED_UNICODE));
            }

            if(!empty($usermodel["bandwidthup"])&&!is_numeric($usermodel["bandwidthup"])){
                exit(json_encode( $code = [ "code" => "-1",  "msg" => "输入类型错误",  "kami" => $kami], JSON_UNESCAPED_UNICODE));
            }

            if(!empty($usermodel["bandwidthdown"])&&!is_numeric($usermodel["bandwidthdown"])){
                exit(json_encode( $code = [ "code" => "-1",  "msg" => "输入类型错误",  "kami" => $kami], JSON_UNESCAPED_UNICODE));
            }

            if($usermodel["connection"]<=0)
            {
                $usermodel["connection"]=-1;
            }

            $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $usermodel['serverip'] . "'"); //$ip['serverip']服务器IP
            //print($server["password"]."".$server["cport"]."".$server["ip"]."".$usermodel["user"]."".$usermodel["pwd"]."".$usermodel["day"]);
            $result = UserUpdate($server["password"], $server["cport"], $server["ip"], $usermodel["olduser"], $usermodel["pwd"], $usermodel["day"],$usermodel["connection"],$usermodel["bandwidthup"]<=0?-1:$usermodel["bandwidthup"]*1024,$usermodel["bandwidthdown"]<=0?-1:$usermodel["bandwidthdown"]*1024,"0",$usermodel["newuser"]);
            WriteLog("用户编辑", "编辑了" . $usermodel, $subconf['username'], $DB);
            exit(json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "userswitch":

        break;
    case "getlog":
        if (isset($_REQUEST['page']) && isset($_REQUEST['limit'])) {
            // // $DB->pageNo=$_REQUEST['page'];当前页码
            // //$DB->pageRows=$_REQUEST['limit'];多少行数
            $sqlpage = isset($_REQUEST['logtime']) != "" ? " and operationdate LIKE '%" . $_REQUEST['logtime'] . "%' " : "1";
            $sql = "SELECT * FROM `log` WHERE operationer=\"" . $subconf['username'] . "\"" . $sqlpage;
            $countpage = $DB->selectRow("select count(*) as num from log where operationer=\"" . $subconf['username'] . "\"" . $sqlpage . "");
            $app = $DB->selectPage($sql, $DB->pageNo = $_REQUEST['page'], $DB->pageRows = $_REQUEST['limit']);
            $json = ["code" => "0", "count" => $countpage['num'], "data" => $app, "icon" => 1];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        } else {
            $json = ["code" => "-1", "count" => null, "data" => "参数错误！", "icon" => "5"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "seldeluser":
        $deldata = $_POST['item'];
        if ($deldata == null || !(isset($deldata)) || empty($deldata)) {
            $code = [
                "code" => "-1",
                "msg" => "删除失败参数为空!",
            ];
        }
        $znum = count($deldata);
        $zxnum = 0;
        $scheduler = new Scheduler;
        foreach ($deldata as $key => $value) {
            // var_dump($value['user']."\n");
            $scheduler->addTask(DelUser($value['user'], $value['serverip'], $DB));
            $res = $scheduler->run();
            if ($res) {
                $zxnum = $zxnum + 1;
            }
        }
        if ($znum == $zxnum) {
            $code = [
                "code" => "1",
                "msg" => "已经执行全部删除!",
            ];
        } else if ($zxnum < $znum) {
            $code = [
                "code" => "0",
                "msg" => "删除部分!未删除数：" . ($znum - $zxnum),
            ];
        }
        WriteLog("批量删除用户", "删除了" . $deldata, $subconf['username'], $DB);
        exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        break;
    case 'adduser':
        $user_data = $_POST["userdata"];
        if (isset($user_data) && is_array($user_data)) {
            $app = $user_data["app"];
            $ip = $DB->select("select serverip from application where appcode='$app'")[0];
            $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'"); //$ip['serverip']服务器IP
            $code = AddUser($server["ip"], $server["password"], $server["cport"], $user_data);
        } else {
            $code = [
                "code" => "-1",
                "msg" => "添加失败参数为空或者有误!",
            ];
        }
        WriteLog("添加用户", "添加了" . $user_data, $subconf['username'], $DB);
        exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        break;
    case 'upswitchuser':
        $usermodel = $_POST["usermodel"];
        if (isset($usermodel) && is_array($usermodel)) {
            $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $usermodel['serverip'] . "'"); //$ip['serverip']服务器IP
            $code = UserUpdate($server["password"], $server["cport"], $server["ip"], $usermodel["user"], $usermodel["pwd"], $usermodel["day"], $usermodel["connection"],$usermodel["bandwidthup"],$usermodel["bandwidthdown"],$usermodel["sw"]);
        } else {
            $code = [
                "code" => "-1",
                "msg" => "失败参数为空或者其他错误!",
            ];
        }
        exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        break;
    case "siteinfo":
        $ser = SerchearchAllServer("", "", $DB);
        $user_data = array();
        while ($ser->valid()) {
            // print_r($ser->current());
            array_push($user_data, $ser->current());
            $ser->next();
        }
        $serverlist=$DB->selectRow("select COUNT(*) as count from server_list");
       
        $lognum=$DB->selectRow("select COUNT(*) as count from log");
        $todaykami=$DB->selectRow("select COUNT(*) as count from kami where use_date>DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') and use_date<DATE_ADD(DATE_ADD(DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00'),INTERVAL 23 HOUR),INTERVAL 59 MINUTE),INTERVAL 59 SECOND) and state='1'");
        $kaminum=$DB->selectRow("select COUNT(*) as count from kami");
        $appnum=$DB->selectRow("select COUNT(*) as count from application");
        $json= [
            "code" => "1",
            "msg" => "获取成功!",
            "usernum"=>count($user_data[0]),
            "servercount"=>$serverlist["count"],
            "lognum"=>$lognum["count"],
            "todaykami"=> $todaykami["count"],
            "kaminum"=>$kaminum["count"],
            "appnum"=>$appnum["count"]
        ];
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        break;
        case "editserver":
            $flag=true;
            $data=$_POST["data"];
            $parm=["user","serverip","pwd","cport","comment","id"];
            foreach($parm as $key => $value){
                if(!isset($data[$value])){
                    $flag=false;
                }
            }
            if($flag){

                if((count(explode(".",$data["serverip"]))<=0))
                {
                    $json = [
                        "code" => "-1",
                        "msg" => "输入了错误的域名或者IP",
                        "icon"=>"5"
                    ];
                    exit(json_encode($json, JSON_UNESCAPED_UNICODE));
                }

                if(!ValidPort($data["cport"])){
                    $json = [
                        "code" => "-1",
                        "msg" => "输入了错误的端口号",
                        "icon"=>"5"
                    ];
                    exit(json_encode($json, JSON_UNESCAPED_UNICODE));
                }

                $state=isset($data["state"])?"1":"0";
                $sql="UPDATE server_list SET ip='".$data["serverip"]."',serveruser='".$data["user"]."',password='".$data["pwd"]."',state='$state',comment='".$data["comment"]."',cport='".$data["cport"]."' WHERE id='".$data["id"]."'";
               
                if($DB->exe($sql)>0){
                    $json = [
                        "code" => "1",
                        "msg" => "编辑成功",
                        "icon"=>"1"
                    ];
                }else{
                    $json = [
                        "code" => "-1",
                        "msg" => "编辑失败,没有更新任何数据",
                        "icon"=>"5"
                    ];
                }

            }else{
                $json = [
                    "code" => "-1",
                    "msg" => "失败参数为空或者其他错误!",
                    "icon"=>"5"
                ];
            }
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
           
            break;
    default:
        exit('{"code":-4,"msg":"No Act"}');
        break;
}

<?php
require_once("../includes/Task.php");
require_once("../includes/Scheduler.php");
include("../includes/common.php");
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;
@header('Content-Type: application/json; charset=UTF-8');

// 在文件开头添加错误处理函数
function handleError($error, $act = '', $DB = null, $subconf = null)
{
    $message = is_string($error) ? $error : $error->getMessage();
    if ($DB && $subconf) {
        WriteLog("错误", "[{$act}] {$message}", $subconf['username'], $DB);
    }
    return [
        "code" => "-1",
        "msg" => $message,
        "act" => $act
    ];
}

switch ($act) {
    case 'getserver':
        try {
            $sql = 'select id,ip,comment from server_list where username=\'' . $subconf['username'] . '\' ';
            $server_list = $DB->select($sql);
            $code = [
                "code" => "1",
                "msg" => $server_list
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'getserver', $DB, $subconf), JSON_UNESCAPED_UNICODE));
        }
        break;
    case 'getuseserver':
        try {
            $sql = 'select id,ip,comment from server_list where state=1 and username=\'' . $subconf['username'] . '\' ';
            $server_list = $DB->select($sql);
            $code = [
                "code" => "1",
                "msg" => $server_list
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'getserver', $DB, $subconf), JSON_UNESCAPED_UNICODE));
        }
        break;
    case 'newapp':
        try {
            if (!isset($_POST['server']) || !isset($_POST['username'])) {
                throw new Exception('缺少必要参数');
            }
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
        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'newapp', $DB, $subconf), JSON_UNESCAPED_UNICODE));
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

            $exesql = $DB->delete("application", "appcode=\"" . $_REQUEST['appcode'] . "\"");

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
            $exesql = $DB->delete("application", "appcode=\"" . $arr[$i] . "\"");
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
            $exesql = $DB->delete("server_list", "ip=\"" . $arr[$i] . "\"");
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
            $result = $DB->exec($sql);

            $cxserver = $DB->selectRow("SELECT applist FROM server_list WHERE ip='" . addslashes($_REQUEST['serverip']) . "'");


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
            $valid = count(explode(".", $serverip));
            if ($valid < 2) {
                $json = [
                    "code" => "-1",
                    "msg" => "输入了错误的IP或者域名",
                    "icon" => "5"
                ];
                exit(json_encode($json, JSON_UNESCAPED_UNICODE));
            }

            if (!ValidPort($ccpport)) {
                $json = [
                    "code" => "-1",
                    "msg" => "输入了错误的端口号",
                    "icon" => "5"
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
            $result = $DB->exec($sql);
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
                $app[$key]['times'] = KamiPaeseString($app[$key]['times']);
            }

            $json = ["code" => "0", "count" => $countpage['num'], "data" => $app, "icon" => "1"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        } else {
            $json = ["code" => "-1", "count" => null, "data" => "参数错误！", "icon" => "5"];
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "newkami":
        if (isset($_POST['app']) && isset($_POST['qianzhui']) && isset($_POST["duration"]) && isset($_POST["kamidur"]) && isset($_POST["kaminum"]) && isset($_POST["comment"]) && isset($_POST["kamilen"]) && isset($_POST["connection"]) && isset($_POST["bandwidthup"]) && isset($_POST["bandwidthdown"])) {
            // $sql="UPDATE server_list SET state=\"".addslashes($_POST["state"])."\" WHERE ip=\"".addslashes(str_replace(array("<",">","/"),array("&lt;","&gt;",""),$_POST['ip']))."\" ";
            // $result=$DB->exe($sql);

            if (!empty($_POST["connection"]) && !is_numeric($_POST["connection"])) {
                exit(json_encode($code = ["code" => "-1",  "msg" => "输入类型错误",  "kami" => ""], JSON_UNESCAPED_UNICODE));
            }

            if (!empty($_POST["bandwidthup"]) && !is_numeric($_POST["bandwidthup"])) {
                exit(json_encode($code = ["code" => "-1",  "msg" => "输入类型错误",  "kami" => ""], JSON_UNESCAPED_UNICODE));
            }

            if (!empty($_POST["bandwidthdown"]) && !is_numeric($_POST["bandwidthdown"])) {
                exit(json_encode($code = ["code" => "-1",  "msg" => "输入类型错误",  "kami" => ""], JSON_UNESCAPED_UNICODE));
            }

            if (!empty($_POST["kamidur"])) {
                if (intval($_POST["kamidur"]) < 1) {
                    exit(json_encode($code = ["code" => "-1",  "msg" => "自定义时长不能小于1,也不能为小数！",  "kami" => ""], JSON_UNESCAPED_UNICODE));
                }
                $vlidnum = count(explode(".", $_POST["kamidur"]));

                if ($vlidnum >= 2) {
                    exit(json_encode($code = ["code" => "-1",  "msg" => "自定义时长不能为小数！",  "kami" => ""], JSON_UNESCAPED_UNICODE));
                }
            }

            $kamidurdangwei = "+" . intval((!empty($_POST["kamidur"]) ? $_POST["kamidur"] : $_POST["duration"]));

            $kamicount = 0;

            if (isset($_POST["year"]) && $_POST["year"] == "on") {
                $kamidurdangwei .= " year";
                $kamicount++;
            }

            if (isset($_POST["month"]) && $_POST["month"] == "on") {
                $kamidurdangwei .= " month";
                $kamicount++;
            }

            if (isset($_POST["day"]) && $_POST["day"] == "on") {
                $kamidurdangwei .= " day";
                $kamicount++;
            }

            if (isset($_POST["hour"]) && $_POST["hour"] == "on") {
                $kamidurdangwei .= " hour";
                $kamicount++;
            }

            if ($kamicount != 1) {
                exit(json_encode($code = ["code" => "-1",  "msg" => "请选择卡密类型",  "kami" => ""], JSON_UNESCAPED_UNICODE));
            }

            $kami = array();
            for ($i = 0; $i < $_POST["kaminum"]; $i++) {
                $kami[$i] = array(
                    "kami" => random($_POST["kamilen"] == "" ? 16 : $_POST["kamilen"], $_POST['qianzhui'] == "" ? null : $_POST['qianzhui'])
                );
            }

            if (empty($_POST["connection"]) || $_POST["connection"] <= 0) {
                $_POST["connection"] = -1;
            }
            if (empty($_POST["bandwidthup"]) || $_POST["bandwidthup"] <= 0) {
                $_POST["bandwidthup"] = -1;
            } else {
                $_POST["bandwidthup"] *= 1024;
            }
            if (empty($_POST["bandwidthdown"]) || $_POST["bandwidthdown"] <= 0) {
                $_POST["bandwidthdown"] = -1;
            } else {
                $_POST["bandwidthdown"] *= 1024;
            }
            $flag = true;
            $ext = [
                "connection" => empty($_POST["connection"]) ? -1 : (int)$_POST["connection"],
                "bandwidthup" => empty($_POST["bandwidthup"]) ? -1 : (int)$_POST["bandwidthup"],
                "bandwidthdown" => empty($_POST["bandwidthdown"]) ? -1 : (int)$_POST["bandwidthdown"]
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
                    'ext' => json_encode($ext)
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
    case "importkami":
        try {
            // 支持 file 上传或 text 文本域
            $app = isset($_POST['app']) ? $_POST['app'] : null;
            $times = isset($_POST['times']) ? $_POST['times'] : null;
            $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

            if (empty($app) || empty($times)) {
                exit(json_encode(["code" => 0, "msg" => "参数错误：app 或 times 为空"], JSON_UNESCAPED_UNICODE));
            }

            $lines = [];

            if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                $content = file_get_contents($_FILES['file']['tmp_name']);
                $lines = preg_split('/\r\n|\r|\n/', $content);
            }

            if (isset($_POST['text']) && !empty($_POST['text'])) {
                $textLines = preg_split('/\r\n|\r|\n/', $_POST['text']);
                $lines = array_merge($lines, $textLines);
            }

            if (empty($lines)) {
                // 记录当前上传信息以便排查
                $debugInfo = [];
                if (isset($_FILES['file'])) {
                    $debugInfo['file_error'] = $_FILES['file']['error'];
                    $debugInfo['file_name'] = $_FILES['file']['name'];
                }
                if (isset($_POST['text'])) {
                    $debugInfo['text_len'] = strlen($_POST['text']);
                }
                WriteLog("导入卡密失败", "未检测到卡密内容 " . json_encode($debugInfo), $subconf['username'], $DB);
                exit(json_encode(["code" => 0, "msg" => "未检测到卡密内容，请检查上传文件或文本格式（可能包含特殊编码/空行）", "debug" => $debugInfo], JSON_UNESCAPED_UNICODE));
            }

            // 解析扩展参数：connection、bandwidthup、bandwidthdown
            $connection_input = isset($_POST['connection']) ? trim($_POST['connection']) : '';
            $bandwidthup_input = isset($_POST['bandwidthup']) ? trim($_POST['bandwidthup']) : '';
            $bandwidthdown_input = isset($_POST['bandwidthdown']) ? trim($_POST['bandwidthdown']) : '';

            $connection_val = ($connection_input === '' || !is_numeric($connection_input) || intval($connection_input) <= 0) ? -1 : intval($connection_input);
            $bandwidthup_val = ($bandwidthup_input === '' || !is_numeric($bandwidthup_input) || intval($bandwidthup_input) <= 0) ? -1 : intval($bandwidthup_input) * 1024;
            $bandwidthdown_val = ($bandwidthdown_input === '' || !is_numeric($bandwidthdown_input) || intval($bandwidthdown_input) <= 0) ? -1 : intval($bandwidthdown_input) * 1024;

            $ext = json_encode(["connection" => $connection_val, "bandwidthup" => $bandwidthup_val, "bandwidthdown" => $bandwidthdown_val]);
            $host = $subconf['siteurl'];
            $sc_user = $subconf['username'];

            $inserted = 0;
            $skipped = 0;
            foreach ($lines as $raw) {
                $kami = trim($raw);
                if ($kami === '') continue;
                if (strlen($kami) > 128) {
                    $skipped++;
                    continue;
                }
                // 检查重复
                $exists = $DB->selectRow("SELECT id FROM kami WHERE kami='" . addslashes($kami) . "'");
                if ($exists) {
                    $skipped++;
                    continue;
                }

                $arr = array(
                    'kami' => addslashes($kami),
                    'times' => addslashes($times),
                    'host' => addslashes($host),
                    'sc_user' => addslashes($sc_user),
                    'state' => 0,
                    'app' => addslashes($app),
                    'comment' => addslashes($comment),
                    'ext' => $ext
                );

                $exec = $DB->insert('kami', $arr);
                if ($exec) {
                    $inserted++;
                } else {
                    $skipped++;
                }
            }

            WriteLog("导入卡密", "导入: 成功{$inserted}，跳过{$skipped}", $subconf['username'], $DB);
            exit(json_encode(["code" => 1, "msg" => "导入完成：成功 {$inserted} 条，跳过 {$skipped} 条"], JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'importkami', $DB, $subconf), JSON_UNESCAPED_UNICODE));
        }
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
            $exesql = $DB->delete("kami", "kami=\"" . $arr[$i] . "\"");
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
                        $result = $DB->exec($sql);
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
            $result = $DB->exec($sql);
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
        try {
            $sqlj = "";
            if (!isset($_REQUEST['page']) || !isset($_REQUEST['limit'])) {
                throw new Exception("缺少必要的分页参数");
            }

            if ($_REQUEST['user'] == "") {
                if ($_REQUEST['app'] == "") {
                    // 使用缓存
                    $cache = Cache::getInstance();
                    if (!$cache) {
                        throw new Exception("无法初始化缓存实例");
                    }

                    $cacheKey = md5("getuserall");
                    $cached = $cache->get($cacheKey);
                    if ($cached !== null) {
                        exit($cached);
                    }

                    //搜索全部服务器
                    $ser = SerchearchAllServer("", "", $DB);
                    if (!$ser) {
                        throw new Exception("获取服务器列表失败");
                    }

                    $user_data = array();
                    while ($ser->valid()) {
                        $current = $ser->current();
                        if (!empty($current)) {  // 确保当前数据不为空
                            array_push($user_data, $current);
                        }
                        $ser->next();
                    }

                    // 检查 $user_data 是否为空
                    if (empty($user_data)) {
                        throw new Exception("未找到用户数据");
                    }

                    // 确保第一个元素存在且为数组
                    if (!isset($user_data[0]) || !is_array($user_data[0])) {
                        throw new Exception("用户数据格式错误");
                    }

                    $result = array_reduce($user_data, function ($result, $value) {
                        if (!is_array($value)) {
                            return $result;
                        }
                        return array_merge($result ?: [], array_values($value));
                    }, []);

                    // 检查处理结果
                    if (empty($result)) {
                        throw new Exception("处理用户数据失败");
                    }

                    $user_updata = array();
                    foreach ($result as $key => $value) {
                        try {
                            $appname = $DB->selectRow("SELECT appname FROM application WHERE serverip='" . $value["serverip"] . "'");
                            $getdata = array(
                                "id" => $value['id'],
                                "user" => $value['user'],
                                "pwd" => $value['pwd'],
                                "state" => $value['state'],
                                "pwdstate" => $value['pwdstate'],
                                "disabletime" => $value['autodisable'] == 0 ? '2099-10-13 14:34:26' : $value['disabletime'],
                                "expire" => $value['autodisable'] == 0 ? 0 : $value['expire'],
                                "user" => $value['user'],
                                'serverip' => $value["serverip"],
                                'appname' => $appname ? $appname['appname'] : '未知',
                                "connection" => $value['connection'] == -1 ? "无限制" : $value['connection'],
                                "bandwidthup" => $value['bandwidthup'] == -1 ? "无限制" : ($value['bandwidthup'] < -1 ? $value['bandwidthup'] : $value['bandwidthup'] / 1024),
                                "bandwidthdown" => $value['bandwidthdown'] == -1 ? "无限制" : ($value['bandwidthdown'] < -1 ? $value['bandwidthdown'] : $value['bandwidthdown'] / 1024)
                            );
                            array_push($user_updata, $getdata);
                        } catch (Exception $e) {
                            WriteLog("警告", "处理用户数据时出错: " . $e->getMessage(), $subconf['username'], $DB);
                            continue;
                        }
                    }

                    $json = ["code" => "0", "count" => count($user_updata), "data" => $user_updata, "icon" => 1];
                    $paserJson = json_encode($json, JSON_UNESCAPED_UNICODE);
                    if ($paserJson === false) {
                        throw new Exception("JSON编码失败: " . json_last_error_msg());
                    }

                    // 缓存结果
                    if (!$cache->set($cacheKey, $paserJson, 300)) {
                        WriteLog("警告", "缓存设置失败", $subconf['username'], $DB);
                    }

                    exit($paserJson);
                } else {
                    // 使用缓存
                    $cache = Cache::getInstance();
                    if (!$cache) {
                        throw new Exception("无法初始化缓存实例");
                    }

                    $cacheKey = md5($_REQUEST['app']);
                    $cached = $cache->get($cacheKey);
                    if ($cached !== null) {
                        exit($cached);
                    }

                    // 这里是选择应用的
                    $ser = SerchearchAllServer($_REQUEST['app'], "", $DB);
                    if (!$ser) {
                        throw new Exception("获取服务器列表失败");
                    }

                    $user_data = array();
                    while ($ser->valid()) {
                        array_push($user_data, $ser->current());
                        $ser->next();
                    }

                    $result = array_reduce($user_data, function ($result, $value) {
                        return array_merge($result, array_values($value));
                    }, array());

                    $user_updata = array();
                    foreach ($result as $key => $value) {
                        try {
                            $appname = $DB->selectRow("SELECT appname FROM application WHERE serverip='" . $value["serverip"] . "'");
                            $getdata = array(
                                "id" => $value['id'],
                                "user" => $value['user'],
                                "pwd" => $value['pwd'],
                                "state" => $value['state'],
                                "pwdstate" => $value['pwdstate'],
                                "disabletime" => $value['autodisable'] == 0 ? '2099-10-13 14:34:26' : $value['disabletime'],
                                "expire" => $value['autodisable'] == 0 ? 0 : $value['expire'],
                                "user" => $value['user'],
                                'serverip' => $value["serverip"],
                                'appname' => $appname ? $appname['appname'] : '未知',
                                "connection" => $value['connection'] == -1 ? "无限制" : $value['connection'],
                                "bandwidthup" => $value['bandwidthup'] == -1 ? "无限制" : ($value['bandwidthup'] < -1 ? $value['bandwidthup'] : $value['bandwidthup'] / 1024),
                                "bandwidthdown" => $value['bandwidthdown'] == -1 ? "无限制" : ($value['bandwidthdown'] < -1 ? $value['bandwidthdown'] : $value['bandwidthdown'] / 1024)
                            );
                            array_push($user_updata, $getdata);
                        } catch (Exception $e) {
                            WriteLog("警告", "处理用户数据时出错: " . $e->getMessage(), $subconf['username'], $DB);
                            continue;
                        }
                    }

                    $json = ["code" => "0", "count" => count($user_updata), "data" => $user_updata, "icon" => 1];
                    $paserJson = json_encode($json, JSON_UNESCAPED_UNICODE);
                    if ($paserJson === false) {
                        throw new Exception("JSON编码失败: " . json_last_error_msg());
                    }

                    // 缓存结果
                    if (!$cache->set($cacheKey, $paserJson, 300)) {
                        WriteLog("警告", "缓存设置失败", $subconf['username'], $DB);
                    }

                    exit($paserJson);
                }
            } else {
                // 搜索指定用户
                $ser = SerchearchAllServer("", $_REQUEST['user'], $DB);
                if (!$ser) {
                    throw new Exception("获取服务器列表失败");
                }

                $user_data = array();
                while ($ser->valid()) {
                    array_push($user_data, $ser->current());
                    $ser->next();
                }

                $result = array_reduce($user_data, function ($result, $value) {
                    return array_merge($result, array_values($value));
                }, array());

                $user_updata = array();
                foreach ($result as $key => $value) {
                    try {
                        $appname = $DB->selectRow("SELECT appname FROM application WHERE serverip='" . $value["serverip"] . "'");
                        $getdata = array(
                            "id" => $value['id'],
                            "user" => $value['user'],
                            "pwd" => $value['pwd'],
                            "state" => $value['state'],
                            "pwdstate" => $value['pwdstate'],
                            "disabletime" => $value['autodisable'] == 0 ? '2099-10-13 14:34:26' : $value['disabletime'],
                            "expire" => $value['autodisable'] == 0 ? 0 : $value['expire'],
                            "user" => $value['user'],
                            'serverip' => $value["serverip"],
                            'appname' => $appname ? $appname['appname'] : '未知',
                            "connection" => $value['connection'] == -1 ? "无限制" : $value['connection'],
                            "bandwidthup" => $value['bandwidthup'] == -1 ? "无限制" : ($value['bandwidthup'] < -1 ? $value['bandwidthup'] : $value['bandwidthup'] / 1024),
                            "bandwidthdown" => $value['bandwidthdown'] == -1 ? "无限制" : ($value['bandwidthdown'] < -1 ? $value['bandwidthdown'] : $value['bandwidthdown'] / 1024)
                        );
                        array_push($user_updata, $getdata);
                    } catch (Exception $e) {
                        WriteLog("警告", "处理用户数据时出错: " . $e->getMessage(), $subconf['username'], $DB);
                        continue;
                    }
                }

                $json = ["code" => "0", "count" => count($user_updata), "data" => $user_updata, "icon" => 1];
                exit(json_encode($json, JSON_UNESCAPED_UNICODE));
            }
        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'getuserall', $DB, $subconf), JSON_UNESCAPED_UNICODE));
        }
        break;
    case "userupdate":
        //UserUpdate()
        $usermodel = $_POST["usermodel"];
        if (isset($usermodel) && is_array($usermodel) && !empty($usermodel)) {


            if (!empty($usermodel["connection"]) && !is_numeric($usermodel["connection"])) {
                exit(json_encode($code = ["code" => "-1",  "msg" => "输入类型错误",  "kami" => $kami], JSON_UNESCAPED_UNICODE));
            }

            if (!empty($usermodel["bandwidthup"]) && !is_numeric($usermodel["bandwidthup"])) {
                exit(json_encode($code = ["code" => "-1",  "msg" => "输入类型错误",  "kami" => $kami], JSON_UNESCAPED_UNICODE));
            }

            if (!empty($usermodel["bandwidthdown"]) && !is_numeric($usermodel["bandwidthdown"])) {
                exit(json_encode($code = ["code" => "-1",  "msg" => "输入类型错误",  "kami" => $kami], JSON_UNESCAPED_UNICODE));
            }

            if ($usermodel["connection"] <= 0) {
                $usermodel["connection"] = -1;
            }

            $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $usermodel['serverip'] . "'"); //$ip['serverip']服务器IP
            //print($server["password"]."".$server["cport"]."".$server["ip"]."".$usermodel["user"]."".$usermodel["pwd"]."".$usermodel["day"]);
            $result = UserUpdate($server["password"], $server["cport"], $server["ip"], $usermodel["olduser"], $usermodel["pwd"], $usermodel["day"], $usermodel["connection"], $usermodel["bandwidthup"] <= 0 ? -1 : $usermodel["bandwidthup"] * 1024, $usermodel["bandwidthdown"] <= 0 ? -1 : $usermodel["bandwidthdown"] * 1024, "0", $usermodel["newuser"]);
            WriteLog("用户编辑", "编辑了" . $usermodel, $subconf['username'], $DB);
            // 使用缓存
            $cache = Cache::getInstance();
            $cache->clear();
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
        // 使用缓存
        $cache = Cache::getInstance();
        $cache->clear();
        WriteLog("批量删除用户", "删除了" . $deldata, $subconf['username'], $DB);
        exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        break;
    case 'delallexpired':
        try {
            // payload 支持：users([{user,serverip}...]) 或 app(字符串) 或 不传（全站）
            $users_payload = isset($_POST['users']) ? $_POST['users'] : null;
            $app_payload = isset($_POST['app']) ? trim($_POST['app']) : null;

            $candidates = [];

            if (!empty($users_payload) && is_array($users_payload)) {
                // 前端传入选中的用户数组（user, serverip）
                foreach ($users_payload as $u) {
                    if (empty($u['user']) || empty($u['serverip'])) continue;
                    $candidates[] = ['user' => $u['user'], 'serverip' => $u['serverip']];
                }
            } else {
                // 根据 app_payload 决定范围（app为空表示全站）
                $ser = SerchearchAllServer($app_payload ? $app_payload : '', '', $DB);
                if (!$ser) throw new Exception('获取服务器列表失败');
                $user_data = array();
                while ($ser->valid()) {
                    $current = $ser->current();
                    if (!empty($current)) array_push($user_data, $current);
                    $ser->next();
                }
                if (empty($user_data)) {
                    exit(json_encode(["code"=>0, "msg"=>"未发现用户数据或无法连接服务器"] , JSON_UNESCAPED_UNICODE));
                }
                $result = array_reduce($user_data, function ($res, $value) {
                    if (!is_array($value)) return $res;
                    return array_merge($res ?: [], array_values($value));
                }, []);
                // 转换为简化候选数组
                foreach ($result as $it) {
                    if (isset($it['user']) && isset($it['serverip'])) {
                        $candidates[] = ['user' => $it['user'], 'serverip' => $it['serverip'], 'autodisable' => $it['autodisable'], 'expire' => $it['expire']];
                    }
                }
            }

            $scheduler = new Scheduler;
            $total = count($candidates);
            $deleted = 0;
            $failed = 0;

            foreach ($candidates as $item) {
                // 如果候选项没有 expire/autodisable 信息（来自前端用户选择），需要查询服务器以确认是否已到期
                $needCheck = !isset($item['expire']) || !isset($item['autodisable']);
                if ($needCheck) {
                    // 从 server_list 获取该服务器行信息，然后调用 queryuserall
                    $serverRow = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . addslashes($item['serverip']) . "'");
                    if (!$serverRow) {
                        $failed++;
                        continue;
                    }
                    try {
                        $alldata = queryuserall($serverRow['password'], $serverRow['cport'], $serverRow['ip']);
                    } catch (Exception $e) {
                        $failed++;
                        continue;
                    }
                    $found = null;
                    if (is_array($alldata)) {
                        foreach ($alldata as $u) {
                            if (isset($u['user']) && $u['user'] == $item['user']) { $found = $u; break; }
                        }
                    }
                    if ($found) {
                        $item['autodisable'] = isset($found['autodisable']) ? $found['autodisable'] : 1;
                        $item['expire'] = isset($found['expire']) ? $found['expire'] : 0;
                    } else {
                        // 无法确认，跳过
                        $failed++;
                        continue;
                    }
                }
                // 跳过永久账号
                if (isset($item['autodisable']) && $item['autodisable'] == 0) {
                    continue;
                }
                // 只删除已到期
                if (!isset($item['expire']) || $item['expire'] != 1) {
                    continue;
                }
                try {
                    $scheduler->addTask(DelUser($item['user'], $item['serverip'], $DB));
                    $res = $scheduler->run();
                    if ($res) {
                        $deleted++;
                    } else {
                        $failed++;
                    }
                } catch (Exception $e) {
                    $failed++;
                    continue;
                }
            }

            // 清理缓存
            $cache = Cache::getInstance(); if ($cache) $cache->clear();
            WriteLog('删除到期用户', "共处理{$total}条，成功{$deleted}，失败{$failed}", $subconf['username'], $DB);

            $code = ["code"=>1, "msg"=>"删除完成：成功{$deleted}，失败{$failed}", "total"=>$total, "success"=>$deleted, "fail"=>$failed];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'delallexpired', $DB, $subconf), JSON_UNESCAPED_UNICODE));
        }
        break;
    case 'addtimeall':
        try {
            // 支持参数：amount(数字，可为负)，unit(days|hours)，app，可选 users([{user,serverip},...])
            $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : null;
            $unit = isset($_POST['unit']) ? $_POST['unit'] : 'days';
            $app = isset($_POST['app']) ? $_POST['app'] : '';
            $users_post = isset($_POST['users']) ? $_POST['users'] : null;

            if ($amount === null || $amount == 0) {
                throw new Exception('无效的加/减时数量');
            }
            if (!in_array($unit, ['days', 'hours'])) {
                throw new Exception('单位错误');
            }

            // 计算秒数（可为负）
            $seconds = ($unit == 'days') ? ($amount * 86400) : ($amount * 3600);

            $result = [];

            if (!empty($users_post) && is_array($users_post)) {
                // 前端传入的是选中用户（user, serverip），我们需要查询每个服务器的用户详情
                foreach ($users_post as $u) {
                    if (empty($u['user']) || empty($u['serverip'])) continue;
                    $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . addslashes($u['serverip']) . "'");
                    if (!$server) continue;
                    // 获取该服务器上的全部用户信息
                    $alldata = queryuserall($server['password'], $server['cport'], $server['ip']);
                    if (empty($alldata) || !is_array($alldata)) continue;
                    foreach ($alldata as $entry) {
                        if (isset($entry['user']) && $entry['user'] == $u['user']) {
                            $entry['serverip'] = $server['ip'];
                            array_push($result, $entry);
                            break;
                        }
                    }
                }
            } else {
                // 使用应用或全部服务器来获取用户列表
                $ser = SerchearchAllServer($app, '', $DB);
                if (!$ser) {
                    throw new Exception('获取服务器列表失败');
                }
                $user_data = array();
                while ($ser->valid()) {
                    $current = $ser->current();
                    if (!empty($current)) {
                        array_push($user_data, $current);
                    }
                    $ser->next();
                }
                if (empty($user_data)) {
                    throw new Exception('未找到用户数据');
                }
                $result = array_reduce($user_data, function ($res, $value) {
                    if (!is_array($value)) return $res;
                    return array_merge($res ?: [], array_values($value));
                }, []);
            }

            if (empty($result)) {
                throw new Exception('用户数据处理失败或为空');
            }

            $success = 0;
            $fail = 0;

            foreach ($result as $key => $user) {
                try {
                    // 跳过永久账户
                    if (isset($user['autodisable']) && $user['autodisable'] == 0) {
                        continue;
                    }
                    $current_disable = isset($user['disabletime']) ? $user['disabletime'] : date('Y-m-d H:i:s');
                    $ts = strtotime($current_disable);
                    if ($ts === false) $ts = time();
                    $new_ts = $ts + intval(round($seconds));
                    $new_disable = date('Y-m-d H:i:s', $new_ts);

                    // 获取 server 行信息
                    $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $user['serverip'] . "'");
                    if (!$server) {
                        $fail++;
                        continue;
                    }

                    // 确保必要字段存在，提供合理默认
                    $pwd = isset($user['pwd']) ? $user['pwd'] : '';
                    $connection = isset($user['connection']) ? ($user['connection'] == -1 ? -1 : $user['connection']) : -1;
                    $bandwidthup = isset($user['bandwidthup']) ? $user['bandwidthup'] : -1;
                    $bandwidthdown = isset($user['bandwidthdown']) ? $user['bandwidthdown'] : -1;

                    $res = UserUpdate($server['password'], $server['cport'], $server['ip'], $user['user'], $pwd, $new_disable, $connection, $bandwidthup, $bandwidthdown);
                    if (is_array($res) && isset($res['code']) && $res['code'] == '1') {
                        $success++;
                    } else {
                        $fail++;
                    }
                } catch (Exception $e) {
                    $fail++;
                    continue;
                }
            }

            // 清理缓存
            $cache = Cache::getInstance();
            if ($cache) $cache->clear();

            WriteLog('批量加减时', "操作: {$amount} {$unit} 应用:{$app}  成功 {$success}，失败 {$fail}", $subconf['username'], $DB);

            $code = [
                'code' => 1,
                'msg' => "操作完成：成功 {$success}，失败 {$fail}"
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'addtimeall', $DB, $subconf), JSON_UNESCAPED_UNICODE));
        }
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
            $code = UserUpdate($server["password"], $server["cport"], $server["ip"], $usermodel["user"], $usermodel["pwd"], $usermodel["day"], $usermodel["connection"], $usermodel["bandwidthup"], $usermodel["bandwidthdown"], $usermodel["sw"]);
            WriteLog("切换用户", "切换了" . $usermodel, $subconf['username'], $DB);
            // 使用缓存
            $cache = Cache::getInstance();
            $cache->clear();
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
        $serverlist = $DB->selectRow("select COUNT(*) as count from server_list");

        $lognum = $DB->selectRow("select COUNT(*) as count from log");
        $todaykami = $DB->selectRow("select COUNT(*) as count from kami where use_date>DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') and use_date<DATE_ADD(DATE_ADD(DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00'),INTERVAL 23 HOUR),INTERVAL 59 MINUTE),INTERVAL 59 SECOND) and state='1'");
        $kaminum = $DB->selectRow("select COUNT(*) as count from kami");
        $appnum = $DB->selectRow("select COUNT(*) as count from application");
        $json = [
            "code" => "1",
            "msg" => "获取成功!",
            "usernum" => count($user_data[0]),
            "servercount" => $serverlist["count"],
            "lognum" => $lognum["count"],
            "todaykami" => $todaykami["count"],
            "kaminum" => $kaminum["count"],
            "appnum" => $appnum["count"]
        ];
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        break;
    case "editserver":
        $flag = true;
        $data = $_POST["data"];
        $parm = ["user", "serverip", "pwd", "cport", "comment", "id"];
        foreach ($parm as $key => $value) {
            if (!isset($data[$value])) {
                $flag = false;
            }
        }
        if ($flag) {

            if ((count(explode(".", $data["serverip"])) <= 0)) {
                $json = [
                    "code" => "-1",
                    "msg" => "输入了错误的域名或者IP",
                    "icon" => "5"
                ];
                exit(json_encode($json, JSON_UNESCAPED_UNICODE));
            }

            if (!ValidPort($data["cport"])) {
                $json = [
                    "code" => "-1",
                    "msg" => "输入了错误的端口号",
                    "icon" => "5"
                ];
                exit(json_encode($json, JSON_UNESCAPED_UNICODE));
            }

            $state = isset($data["state"]) ? "1" : "0";
            $sql = "UPDATE server_list SET ip='" . $data["serverip"] . "',serveruser='" . $data["user"] . "',password='" . $data["pwd"] . "',state='$state',comment='" . $data["comment"] . "',cport='" . $data["cport"] . "' WHERE id='" . $data["id"] . "'";

            if ($DB->exec($sql) > 0) {
                $json = [
                    "code" => "1",
                    "msg" => "编辑成功",
                    "icon" => "1"
                ];
            } else {
                $json = [
                    "code" => "-1",
                    "msg" => "编辑失败,没有更新任何数据",
                    "icon" => "5"
                ];
            }
        } else {
            $json = [
                "code" => "-1",
                "msg" => "失败参数为空或者其他错误!",
                "icon" => "5"
            ];
        }
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        break;
    case "clearcache":
        try {
            $cache = Cache::getInstance();
            if (!$cache) {
                throw new Exception("无法初始化缓存实例");
            }

            $result = $cache->clear();
            if (!$result) {
                throw new Exception("清除缓存失败");
            }

            WriteLog("清除缓存", "清除缓存成功", $subconf['username'], $DB);
            exit(json_encode([
                "code" => "1",
                "msg" => "清除缓存成功"
            ], JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'clearcache', $DB, $subconf), JSON_UNESCAPED_UNICODE));
        }
        break;
    default:
        exit(json_encode(handleError('无效的操作类型', $act), JSON_UNESCAPED_UNICODE));
        break;
}

// 添加全局错误处理
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($DB, $subconf, $act) {
    $error = "[$errno] $errstr in $errfile:$errline";
    exit(json_encode(handleError($error, $act, $DB, $subconf), JSON_UNESCAPED_UNICODE));
});

// 添加未捕获异常处理
set_exception_handler(function ($e) use ($DB, $subconf, $act) {
    exit(json_encode(handleError($e, $act, $DB, $subconf), JSON_UNESCAPED_UNICODE));
});

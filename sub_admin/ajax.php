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
            $server_list = $DB->selectV2('select id,ip,comment from server_list where username = ?', [$subconf['username']]);
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
            $server_list = $DB->selectV2('select id,ip,comment from server_list where state=1 and username = ?', [$subconf['username']]);
            $code = [
                "code" => "1",
                "msg" => $server_list
            ];
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            exit(json_encode(handleError($e, 'getuseserver', $DB, $subconf), JSON_UNESCAPED_UNICODE));
        }
        break;
    case 'newapp':
        try {
            if (!isset($_POST['server']) || !isset($_POST['username'])) {
                throw new Exception('缺少必要参数');
            }
            $server = $_POST['server'];
            $username = $_POST['username'];
            $dist_name = $DB->selectV2('select appname from application', []);
            $flag = true;
            foreach ($dist_name as $key => $name) {
                if ($username == $name['appname']) {
                    $flag = false;
                }
            }
            if ($flag) {
                $appcode = md5(uniqid(mt_rand(), 1) . time());
                $arr = array(
                    'appname'  => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $username),
                    'appcode' => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $appcode),
                    'serverip'     => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $server),
                    'username' => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $subconf['username']),
                );
                $exec = $DB->insertV2('application', $arr);
                if ($exec) {
                    $code = [
                        "code" => "1",
                        "msg" => "添加成功"
                    ];
                    $cache = Cache::getInstance();
                    $cache->clear();
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
        if (isset($_REQUEST['page']) && isset($_REQUEST['limit']) && isset($_REQUEST['server']) && isset($_REQUEST['appname'])) {
            // 构建参数化查询
            $params = [$subconf['username']];
            $sql = 'SELECT appid,appcode,appname,serverip,found_time FROM application where username = ?';
            
            // 服务器sql
            if ($_REQUEST['server'] != "" && $_REQUEST['server'] != "*") {
                $sql .= " and serverip = ?";
                $params[] = $_REQUEST['server'];
            }
            // 应用名字搜索
            if ($_REQUEST['appname'] != "") {
                $sql .= " and appname LIKE ?";
                $params[] = '%' . $_REQUEST["appname"] . '%';
            }

            $countpage = $DB->selectRowV2("select count(*) as num from application where username = ?", [$subconf['username']]);

            $DB->pageNo = $_REQUEST['page'];
            $DB->pageRows = $_REQUEST['limit'];
            $app = $DB->selectPageV2($sql, $params);

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
            $exesql = $DB->deleteV2("application", "appcode = ?", [$_REQUEST['appcode']]);

            if ($exesql !== false) {
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
            WriteLog("删除失败", "删除失败参数为空", $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        $arr = $_POST['item'];
        $execs = 0;
        $execf = 0;
        for ($i = 0; $i < count($arr); $i++) {
            $exesql = $DB->deleteV2("application", "appcode = ?", [$arr[$i]]);
            if ($exesql !== false) {
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
            WriteLog("删除", "删除了应用", $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "1",
                "msg" => "删除成功：" . $execs . "删除失败：" . $execf,
            ];
            WriteLog("删除", "删除了应用", $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "serverdel":
        $arr = $_POST['item'];
        $execs = 0;
        $execf = 0;
        for ($i = 0; $i < count($arr); $i++) {
            $exesql = $DB->deleteV2("server_list", "ip = ?", [$arr[$i]]);
            if ($exesql !== false) {
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
            WriteLog("删除", "删除了服务器", $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "1",
                "msg" => "删除成功：" . $execs . "删除失败：" . $execf,
            ];
            WriteLog("删除", "删除了服务器", $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        }
        break;
    case "update":
        if (isset($_REQUEST['appcode']) && isset($_REQUEST['appname']) && isset($_REQUEST['serverip'])) {
            $appname = str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $_REQUEST['appname']);
            $values = [
                'appname' => $appname,
                'serverip' => $_REQUEST['serverip']
            ];
            $result = $DB->updateV2('application', $values, 'appcode = ?', [$_REQUEST['appcode']]);

            $cxserver = $DB->selectRowV2("SELECT applist FROM server_list WHERE ip = ?", [$_REQUEST['serverip']]);

            if ($result !== false) {
                $code = [
                    "code" => "1",
                    "msg" => "更新成功！"
                ];

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
        if (isset($_REQUEST['page']) && isset($_REQUEST['limit']) && isset($_REQUEST['ip']) && isset($_REQUEST['comment'])) {
            // 构建参数化查询
            $params = [$subconf['username']];
            $sql = 'SELECT id,ip,serveruser,password,cport,state,comment FROM server_list where username = ?';
            
            // 服务器IP
            if ($_REQUEST['ip'] != "") {
                $sql .= " and ip = ?";
                $params[] = $_REQUEST['ip'];
            }
            // 备注搜索
            if ($_REQUEST['comment'] != "") {
                $sql .= " and comment LIKE ?";
                $params[] = '%' . $_REQUEST["comment"] . '%';
            }

            $countpage = $DB->selectRowV2("select count(*) as num from server_list where username = ?", [$subconf['username']]);
            
            $DB->pageNo = $_REQUEST['page'];
            $DB->pageRows = $_REQUEST['limit'];
            $app = $DB->selectPageV2($sql, $params);

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

        $dist_ip = $DB->selectV2('select ip from server_list', []);
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
                'ip'  => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $serverip),
                'serveruser'  => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $ccpusername),
                'password'  => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $ccppassword),
                'cport'  => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $ccpport),
                'state'  => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $state),
                'comment'  => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $comment),
                'username' => str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $subconf['username'])
            );
            $exec = $DB->insertV2('server_list', $arr);
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
            $ip = str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $_POST['ip']);
            $result = $DB->updateV2('server_list', ['state' => $_POST["state"]], 'ip = ?', [$ip]);
            if ($result !== false) {
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
        if (isset($_REQUEST['page']) && isset($_REQUEST['limit']) && isset($_REQUEST['code']) && isset($_REQUEST['found_date']) && isset($_REQUEST['use_date']) && isset($_REQUEST['sc_user']) && isset($_REQUEST['state']) && isset($_REQUEST['comment']) && isset($_REQUEST['app'])) {
            // 构建参数化查询
            $params = [$subconf['siteurl']];
            $sql = 'SELECT * FROM kami where host = ?';
            
            if ($_REQUEST['code'] != "") {
                $sql .= " and kami = ?";
                $params[] = $_REQUEST['code'];
            }
            if ($_REQUEST['found_date'] != "") {
                $sql .= " and found_date = ?";
                $params[] = $_REQUEST['found_date'];
            }
            if ($_REQUEST['use_date'] != "") {
                $sql .= " and use_date = ?";
                $params[] = $_REQUEST['use_date'];
            }
            if ($_REQUEST['sc_user'] != "") {
                $sql .= " and sc_user = ?";
                $params[] = $_REQUEST['sc_user'];
            }
            if ($_REQUEST['state'] != "") {
                $sql .= " and state = ?";
                $params[] = $_REQUEST['state'];
            }
            if ($_REQUEST['comment'] != "") {
                $sql .= " and comment = ?";
                $params[] = $_REQUEST['comment'];
            }
            if ($_REQUEST['app'] != "") {
                $sql .= " and app = ?";
                $params[] = $_REQUEST['app'];
            }
            $sql .= " order by found_date desc";

            $countpage = $DB->selectRowV2("select count(*) as num from kami where sc_user = ?", [$subconf['username']]);
            
            $DB->pageNo = $_REQUEST['page'];
            $DB->pageRows = $_REQUEST['limit'];
            $app = $DB->selectPageV2($sql, $params);
            
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
                    'host'  => $subconf['siteurl'],
                    'sc_user'  => $subconf['username'],
                    'state'  => 0,
                    'app'  => $_POST["app"],
                    'comment'  => $_POST["comment"],
                    'ext' => json_encode($ext)
                );
                $exec = $DB->insertV2('kami', $arr);
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
        $query = $DB->selectV2('SELECT appcode,appname FROM application where username = ?', [$subconf['username']]);
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
            $exesql = $DB->deleteV2("kami", "kami = ?", [$arr[$i]]);
            if ($exesql !== false) {
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
            WriteLog("删除卡密", "删除了卡密", $subconf['username'], $DB);
            exit(json_encode($code, JSON_UNESCAPED_UNICODE));
        } else {
            $code = [
                "code" => "1",
                "msg" => "删除成功：" . $execs . "删除失败：" . $execf,
            ];
            WriteLog("删除卡密", "删除了卡密", $subconf['username'], $DB);
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
                        $username = str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $subconf['username']);
                        $result = $DB->updateV2('sub_admin', ['password' => $_POST["confirm_password"]], 'username = ?', [$username]);
                        if ($result !== false) {
                            $code = [
                                "code" => "1",
                                "msg" => "更新成功"
                            ];
                            WriteLog("修改密码", "密码已修改", $subconf['username'], $DB);
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
        $requiredFields = ['user_key', 'kf', 'pan', 'ggswitch', 'wzgg', 'logo'];
        $gg = 1;
        if (!isset($_POST['ggswitch'])) {
            array_splice($requiredFields, 3, 1); //删除数组的ggswictch
            $gg = 0;
        }
        $flag = true;
        foreach ($requiredFields as $post) {
            $flag = isset($_POST[$post == "wzgg" ? "user_key" : $post]);
        }
        if ($flag) {
            $values = [
                'hostname' => $_POST["user_key"],
                'kf' => $_POST["kf"],
                'pan' => $_POST["pan"],
                'img' => $_POST["logo"],
                'ggswitch' => $gg
            ];
            if ($gg != 0) {
                $values['wzgg'] = trim(str_replace(array("'"), array('"'), $_POST["wzgg"]));
            }
            $username = str_replace(array("<", ">", "/"), array("&lt;", "&gt;", ""), $subconf['username']);
            $result = $DB->updateV2('sub_admin', $values, 'username = ?', [$username]);
            if ($result !== false) {
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
                            $appname = $DB->selectRowV2("SELECT appname FROM application WHERE serverip = ?", [$value["serverip"]]);
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
                            $appname = $DB->selectRowV2("SELECT appname FROM application WHERE serverip = ?", [$value["serverip"]]);
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
                        $appname = $DB->selectRowV2("SELECT appname FROM application WHERE serverip = ?", [$value["serverip"]]);
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

            $server = $DB->selectRowV2("select ip,serveruser,password,cport from server_list where ip = ?", [$usermodel['serverip']]); //$ip['serverip']服务器IP
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
            // 构建参数化查询
            $params = [$subconf['username']];
            $countParams = [$subconf['username']];
            $sql = "SELECT * FROM `log` WHERE operationer = ?";
            $countSql = "select count(*) as num from log where operationer = ?";
            
            if (isset($_REQUEST['logtime']) && $_REQUEST['logtime'] != "") {
                $sql .= " and operationdate LIKE ?";
                $countSql .= " and operationdate LIKE ?";
                $params[] = '%' . $_REQUEST['logtime'] . '%';
                $countParams[] = '%' . $_REQUEST['logtime'] . '%';
            }
            
            $countpage = $DB->selectRowV2($countSql, $countParams);
            
            $DB->pageNo = $_REQUEST['page'];
            $DB->pageRows = $_REQUEST['limit'];
            $app = $DB->selectPageV2($sql, $params);
            
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
    case 'adduser':
        $user_data = $_POST["userdata"];
        if (isset($user_data) && is_array($user_data)) {
            $app = $user_data["app"];
            $ipResult = $DB->selectV2("select serverip from application where appcode = ?", [$app]);
            $ip = $ipResult[0] ?? null;
            $server = $DB->selectRowV2("select ip,serveruser,password,cport from server_list where ip = ?", [$ip['serverip']]); //$ip['serverip']服务器IP
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
            $server = $DB->selectRowV2("select ip,serveruser,password,cport from server_list where ip = ?", [$usermodel['serverip']]); //$ip['serverip']服务器IP
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
        $serverlist = $DB->selectRowV2("select COUNT(*) as count from server_list", []);

        $lognum = $DB->selectRowV2("select COUNT(*) as count from log", []);
        $todaykami = $DB->selectRowV2("select COUNT(*) as count from kami where use_date>DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') and use_date<DATE_ADD(DATE_ADD(DATE_ADD(DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00'),INTERVAL 23 HOUR),INTERVAL 59 MINUTE),INTERVAL 59 SECOND) and state='1'", []);
        $kaminum = $DB->selectRowV2("select COUNT(*) as count from kami", []);
        $appnum = $DB->selectRowV2("select COUNT(*) as count from application", []);
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
            $values = [
                'ip' => $data["serverip"],
                'serveruser' => $data["user"],
                'password' => $data["pwd"],
                'state' => $state,
                'comment' => $data["comment"],
                'cport' => $data["cport"]
            ];
            $result = $DB->updateV2('server_list', $values, 'id = ?', [$data["id"]]);

            if ($result !== false && $result > 0) {
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

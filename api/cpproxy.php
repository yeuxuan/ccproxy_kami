<?php
/*
 * @Author: yihua
 * @Date: 2022-02-16 00:22:38
 * @LastEditTime: 2022-09-10 22:21:35
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccpy\api\cpproxy.php
 * 一花一叶 一行代码
 */

include("../includes/common.php");
@header('Content-Type: application/json; charset=UTF-8');

if (isset($_REQUEST["type"])) {
    $type = $_REQUEST["type"];
    switch ($type) {
        case "insert":
            $json = checkinsert($DB);
            break;
        case "del":
            $json = del();
            break;
        case "update":
            $json = checkupdate($DB);
            break;
        case "query":
            $json = checkquery($DB);
            break;
        default:
            $json = ["code" => "无效事务", "icon" => "5"];
    }
} else {
    $json = ["code" => "非法参数", "icon" => "5"];
}
echo json_encode($json, JSON_UNESCAPED_UNICODE);

/**
 * 查询方法
 */
function checkquery($DB)
{
    if (isset($_POST["appcode"]) && isset($_POST["user"])) {
        $ip = $DB->selectRow("select serverip from application where appcode='" . $_POST["appcode"] . "'");
        $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'"); //$ip['serverip']服务器IP
        $proxyaddress = $server['ip'];
        $admin_username = $server['serveruser'];
        $admin_password = $server['password'];
        $admin_port = $server['cport'];
        $ser = query($admin_password, $admin_port, $proxyaddress);
        if (!$ser) {
            $json = [
                "code" => -3,
                "msg" => '<h5 style="color: red;display: inline;">服务器通信出现问题</h5>'
            ];
        } else {
            $json = [
                "code" => 1,
                "msg" => userquer($_POST["user"], $ser)
            ];
        }
    } else {
        $json = ["code" => "非法参数", "icon" => "5"];
    }
    return $json;
}
/***
 * 添加用户
 */
function checkinsert($DB)
{
    if (isset($_POST["user"]) && isset($_POST["pwd"]) && isset($_POST["code"])) {
        $kami = $DB->selectRow("select count(*) as num,app,times,state,ext from kami where kami='" . $_POST["code"] . "' GROUP BY app,times,state,ext");
        if ($kami['num'] > 0) {
            if ($kami['state'] == 0) {
                $ip = $DB->selectRow("select serverip from application where appcode='" . $kami['app'] . "'");
                $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'"); //$ip['serverip']服务器IP
                $proxyaddress = $server['ip'];
                $admin_username = $server['serveruser'];
                $admin_password = $server['password'];
                $admin_port = $server['cport'];
                $date = date("Y-m-d H:i:s");
                $user = query($admin_password, $admin_port, $proxyaddress);
                if (!existsuser($_POST["user"], $user)) {
                    $json = [
                        "code" => -1,
                        "msg" => "账号已经存在"
                    ];
                } else {
                    $msg = insert($proxyaddress, $admin_username, $admin_password, $admin_port, $kami['times'], $_POST["user"], $_POST["pwd"], $kami['ext']);
                    if ($msg["icon"] == 1) {
                        $usr = array(
                            'state' => 1,
                            'username' => $_POST["user"],
                            'use_date' => date("Y-m-d H:i:s"),
                            'end_date' => (date('Y-m-d H:i:s', strtotime($date . $kami['times'])))=="1970-01-01 08:00:00"?date('Y-m-d H:i:s', strtotime($date . "+1 day")):date('Y-m-d H:i:s', strtotime($date . $kami['times']))
                        );
                        $exec = $DB->update('kami', $usr, "kami=\"" . $_POST["code"] . "\"");
                        $json = [
                            "code" => 1,
                            "msg" => $msg["code"]
                        ];
                    } else if (!empty($msg)) {
                        $json = $msg;
                    } else {
                        $json = [
                            "code" => -3,
                            "msg" => '<h5 style="color: red;display: inline;">服务器通信出现问题</h5>'
                        ];
                    }
                }
            } else {
                $json = [
                    "code" => -1,
                    "msg" => "卡密已被使用"
                ];
            }
        } else {
            $json = [
                "code" => -2,
                "msg" => "卡密不存在"
            ];
        }
    } else {
        $json = ["code" => "非法参数", "icon" => "5"];
    }
    return   $json;
}
/**
 * 续费方法
 */
function checkupdate($DB)
{
    if (isset($_POST["user"]) && isset($_POST["code"])) {
        $kami = $DB->selectRow("select count(*) as num,app,times,state,ext from kami where kami='" . $_POST["code"] . "' GROUP BY app,times,state,ext");
        if ($kami['num'] > 0) {
            if ($kami['state'] == 0) {
                $ip = $DB->selectRow("select serverip from application where appcode='" . $kami['app'] . "'");
                $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'"); //$ip['serverip']服务器IP
                $proxyaddress = $server['ip'];
                $admin_username = $server['serveruser'];
                $admin_password = $server['password'];
                $admin_port = $server['cport'];
                $user = query($admin_password, $admin_port, $proxyaddress);
                if (existsuser($_POST["user"], $user)) {
                    $json = [
                        "code" => -1,
                        "msg" => "充值账号不存在"
                    ];
                    return $json;
                }
                $ser = query($admin_password, $admin_port, $proxyaddress);
                if ($ser == false) {
                    $json = [
                        "code" => -3,
                        "msg" => '<h5 style="color: red;display: inline;">服务器通信出现问题</h5>'
                    ];
                } else {
                    $date = updatequer($_POST["user"], $ser);
                    if ($date['disabletime'] == "") {
                        $json = [
                            "code" => -3,
                            "msg" => '<h5 style="color: red;display: inline;">账号不存在</h5>'
                        ];
                    } else {
                        $json = [
                            "code" => 1,
                            "msg" => update($proxyaddress, $admin_username, $admin_password, $admin_port, $kami['times'], $date, $kami["ext"])
                        ];
                        $usr = array(
                            'state' => 1,
                            'username' => $_POST["user"],
                            'use_date' => date("Y-m-d H:i:s"),//"1970-01-01 08:00:00" date('Y-m-d H:i:s', strtotime($date['disabletime'] . $kami['times']))
                            'end_date' => $date['expire'] == 0 ? (date('Y-m-d H:i:s', strtotime($date['disabletime'] . $kami['times']))=="1970-01-01 08:00:00"?date('Y-m-d H:i:s', strtotime($date['disabletime'] . "+1 day")):date('Y-m-d H:i:s', strtotime($date['disabletime'] . $kami['times']))) : (date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . $kami['times']))=="1970-01-01 08:00:00"?date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") ."+1 day")):date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . $kami['times'])))
                            //'end_date' => $date['expire'] == 0 ? date('Y-m-d H:i:s', strtotime($date['disabletime'] . $kami['times']>0&&$kami['times']<1?((int)($kami['times']*10)) . " hours":((int)$kami['times']) . " day")) : date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . $kami['times']>0&&$kami['times']<1?((int)($kami['times']*10)) . " hours ":((int)$kami['times']) . " day"))
                        );
                        $exec = $DB->update('kami', $usr, "kami='" . $_POST["code"] . "'");
                    }
                }
            } else {
                $json = [
                    "code" => -1,
                    "msg" => "卡密已被使用"
                ];
            }
        } else {
            $json = [
                "code" => -2,
                "msg" => "卡密不存在"
            ];
        }
    } else {
        $json = ["code" => "非法参数", "icon" => "5"];
    }
    return   $json;
}



/**
 * 添加具体方法
 */

function insert($proxyaddress, $admin_username, $admin_password, $admin_port, $day, $user, $pwd, $ext)
{
    $username = $user;
    $password = $pwd;
    if (!CheckStrChinese($username)) {
        return ["code" => "-1", "msg" => "用户名不合法", "icon" => "5"];
    }
    if (strlen($username) < 5) {
        return ["code" => "-1", "msg" => "用户名长度不合法", "icon" => "5"];
    }
    if (!CheckStrPwd($password)) {
        return ["code" => "-1", "msg" => "密码不合法", "icon" => "5"];
    }
    $post = ["user", "pwd"];
    $is = true;
    foreach ($post as $key) {
        if (!isset($_POST[$key])) {
            $is = false;
        }
    }
    if ($is) {
        $ipaddress = "";
        $macaddress = "";
        $connection = json_decode($ext, true)["connection"];
        $bandwidth = json_decode($ext, true)["bandwidthup"] . "/" . json_decode($ext, true)["bandwidthdown"];
        $date = date("Y-m-d H:i:s");
        $enddate = "";

        try {
            $enddate= date('Y-m-d H:i:s', strtotime("$date $day"));
        } catch (Exception $e)
        {
            $enddate= date('Y-m-d H:i:s', strtotime("$date +1 day"));
        }

        if($enddate=="1970-01-01 08:00:00"){
            $enddate= date('Y-m-d H:i:s', strtotime("$date +1 day"));
        }

        //$enddate = date('Y-m-d H:i:s', strtotime("$date + " . $day>0&&$day<1?(int)($day*10) ." hours":((int)$day) . " day"));
        $end_date = explode(" ", $enddate);
        $disabledate = $end_date[0];
        $disabletime = $end_date[1];
        $fp = fsockopen($proxyaddress, $admin_port, $errno, $errstr, 30);
        if (!$fp) {
            // return ["code" => "无法连接到CCProxy", "icon" => "5"];
            return false;
        } else {
            $url_ = "/account";
            $url = "add=1" . "&";
            $url = $url . "autodisable=1" . "&";
            $url = $url . "enable=1" . "&";
            if ($admin_password != "") {
                $url = $url . "usepassword=1" . "&";
            }
            if ($ipaddress != "") {
                $url = $url . "usepassword=1" . "&";
            }
            if ($macaddress != "") {
                $url = $url . "usemacaddress=1" . "&";
            }
            $url = $url . "enablesocks=1" . "&";
            $url = $url . "enablewww=0" . "&";
            $url = $url . "enabletelnet=0" . "&";
            $url = $url . "enabledial=0" . "&";
            $url = $url . "enableftp=0" . "&";
            $url = $url . "enableothers=0" . "&";
            $url = $url . "enablemail=0" . "&";
            $url = $url . "username=" . $username . "&";
            $url = $url . "password=" . $password . "&";
            $url = $url . "ipaddress=" . $ipaddress . "&";
            $url = $url . "macaddress=" . $macaddress . "&";
            $url = $url . "connection=" . $connection . "&";
            $url = $url . "bandwidth=" . $bandwidth . "&";
            $url = $url . "disabledate=" . $disabledate . "&";
            $url = $url . "disabletime=" . $disabletime . "&";
            $url = $url . "userid=-1";
            $len = "Content-Length: " . strlen($url);
            $auth = "Authorization: Basic " . base64_encode($admin_username . ":" . $admin_password);
            $msg = "POST " . $url_ . " HTTP/1.0\r\nHost: " . $proxyaddress . "\r\n" . $auth . "\r\n" . $len . "\r\n" . "\r\n" . $url;
            fputs($fp, $msg);
            while (!feof($fp)) {
                $s = fgets($fp, 4096);
            }
            fclose($fp);
            return ["code" => "注册用户成功", "icon" => "1"];
        }
    } else {
        return ["code" => "注册用户存在非法参数", "icon" => "5"];
    }
}
/**
 * 删除方法
 */
function del()
{
    $username = $_POST["username"];
    if (isset($username)) {
        if (!CheckStrChinese($username)) {
            return ["code" => "-1", "msg" => "用户名不合法", "icon" => "5"];
        }
        if (strlen($username) < 5) {
            return ["code" => "-1", "msg" => "用户名长度不合法", "icon" => "5"];
        }
        $admin_username = isset($_POST["admin_username"]) ? $_POST["admin_username"] : "";
        $admin_password = isset($_POST["admin_password"]) ? $_POST["admin_password"] : "";
        $adminport = isset($_POST["admin_port"]) ? $_POST["admin_port"] : "";
        $proxyaddress = isset($_POST["proxyaddress"]) ? $_POST["proxyaddress"] : "";
        $fp = fsockopen($proxyaddress, $adminport, $errno, $errstr, 30);
        if (!$fp) {
            return ["code" => "无法连接到CCProxy", "icon" => "5"];
        } else {
            $url_ = "/account";
            $url = "delete=1" . "&";
            $url = $url . "userid=" . $username;
            $len = "Content-Length: " . strlen($url);
            $auth = "Authorization: Basic " . base64_encode($admin_username . ":" . $admin_password);
            $msg = "POST " . $url_ . " HTTP/1.0\r\nHost: " . $proxyaddress . "\r\n" . $auth . "\r\n" . $len . "\r\n" . "\r\n" . $url;
            fputs($fp, $msg);
            while (!feof($fp)) {
                $s = fgets($fp, 4096);
            }
            fclose($fp);
            return ["code" => "删除用户成功", "icon" => "1"];
        }
    } else {
        return ["code" => "删除用户存在非法参数", "icon" => "5"];
    }
}
/**
 * 更新方法
 */
function update($proxyaddress, $admin_username, $admin_password, $admin_port, $day, $date, $ext)
{
    if (!CheckStrChinese($admin_username)) {
        return ["code" => "-1", "msg" => "用户名不合法", "icon" => "5"];
    }
    if (strlen($admin_username) < 5) {
        return ["code" => "-1", "msg" => "用户名长度不合法", "icon" => "5"];
    }
    $post = ["user"];
    $is = true;
    foreach ($post as $key) {
        if (!isset($_POST[$key])) {
            $is = false;
        }
    }
    if ($is) {
        $username = $_POST["user"];
        $connection = json_decode($ext, true)["connection"];
        $bandwidth = json_decode($ext, true)["bandwidthup"] . "/" . json_decode($ext, true)["bandwidthdown"];
        $cdate = date("Y-m-d H:i:s");
        // var_dump($date['expire'],$date['disabletime']);
        // 0 未到期 1 到期
        //$enddate = date('Y-m-d H:i:s', strtotime("$date $day"));
        $enddate = "";
        try{
            $enddate = $date['expire'] == 0 ? date('Y-m-d H:i:s', strtotime($date['disabletime'] . $day)) : date('Y-m-d H:i:s', strtotime($cdate . $day));
        }catch(Exception $e){    // $e 为一个异常类的对象
        
            $enddate = $date['expire'] == 0 ? date('Y-m-d H:i:s', strtotime($date['disabletime'] . "+1 day")) : date('Y-m-d H:i:s', strtotime($cdate . "+1 day"));
            
        }

        if($enddate=="1970-01-01 08:00:00")
        {
            $enddate = $date['expire'] == 0 ? date('Y-m-d H:i:s', strtotime($date['disabletime'] . "+1 day")) : date('Y-m-d H:i:s', strtotime($cdate . "+1 day"));
        }

        //$enddate = $date['expire'] == 0 ? date('Y-m-d H:i:s', strtotime($date['disabletime'] . ($day>0&&$day<1?((int)$day*10) . " hours":((int)$day) . " day"))) : date('Y-m-d H:i:s', strtotime($cdate . $day . " day"));
        // $enddate=date('Y-m-d H:i:s',strtotime("$date + ".$day." day"));
        $end_date = explode(" ", $enddate);
        $disabledate = $end_date[0];
        $disabletime = $end_date[1];
        $fp = fsockopen($proxyaddress, $admin_port, $errno, $errstr, 30);
        if (!$fp) {
            return ["code" => "无法连接到CCProxy", "icon" => "5"];
        } else {
            $url_ = "/account";
            $url = "edit=1" . "&";
            $url = $url . "autodisable=1" . "&";
            $url = $url . "enable=1" . "&";
            $url = $url . "usepassword=1" . "&";
            $url = $url . "enablesocks=1" . "&";
            $url = $url . "enablewww=0" . "&";
            $url = $url . "enabletelnet=0" . "&";
            $url = $url . "enabledial=0" . "&";
            $url = $url . "enableftp=0" . "&";
            $url = $url . "enableothers=0" . "&";
            $url = $url . "enablemail=0" . "&";
            $url = $url . "username=" . $username . "&";
            // $url = $url."password=".$password."&";
            $url = $url . "connection=" . $connection . "&";
            $url = $url . "bandwidth=" . $bandwidth . "&";
            $url = $url . "disabledate=" . $disabledate . "&";
            $url = $url . "disabletime=" . $disabletime . "&";
            $url = $url . "userid=" . $username;
            $len = "Content-Length: " . strlen($url);
            $auth = "Authorization: Basic " . base64_encode($admin_username . ":" . $admin_password);
            $msg = "POST " . $url_ . " HTTP/1.0\r\nHost: " . $proxyaddress . "\r\n" . $auth . "\r\n" . $len . "\r\n" . "\r\n" . $url;
            fputs($fp, $msg);
            //echo $msg;
            while (!feof($fp)) {
                $s = fgets($fp, 4096);
                //echo $s;
            }
            fclose($fp);
            return ["code" => "更新用户成功", "icon" => "1"];
        }
    } else {
        return ["code" => "编辑数据存在非法参数", "icon" => "5"];
    }
}


/**
 * Undocumented function
 *
 * @param [type] $url
 * @param [type] $adminpassword
 * @param [type] $adminport
 * @param [type] $proxyaddress
 * @author 一花 <487735913@qq.com>
 * @copyright Undocumented function [type]  [type]
 */
function query($adminpassword, $adminport, $proxyaddress)
{
    $url = "http://" . $proxyaddress . ":" . $adminport . "/account";
    parse_url($url);
    //print_r();// 解析 URL，返回其组成部分
    $data = array();
    $query_str = http_build_query($data); // http_build_query()函数的作用是使用给出的关联（或下标）数组生成一个经过 URL-encode 的请求字符串
    $info = parse_url($url);
    $fp = fsockopen($proxyaddress, $adminport, $errno, $errstr, 30);
    if (!$fp) {
        // echo "$errstr ($errno)<br>\n";
        return false;
    } else {
        $auth = "Authorization: Basic " . base64_encode("admin:" . $adminpassword);
        $head = "GET " . $info['path']  . $query_str . " HTTP/1.0\r\n";
        $head .= "Host: " . $info['host'] . "\r\n" . $auth . "\r\n" . "\r\n";
        $write = fputs($fp, $head);
        $line = "";
        while (!feof($fp)) {
            $line .= fread($fp, 4096);
            // echo str_replace(array("<",">","/"),array("&lt;","&gt;",""), $line);
        }
        fclose($fp);
    }
    //echo $line; 
    //取出div标籤且id为PostContent的内容，并储存至阵列match
    preg_match_all('/<input .* name="username" .* value="(.*?)"/ui', $line, $match);
    preg_match_all('/<input .* name="password" .* value="(.*?)"/ui', $line, $match2);
    preg_match_all('/<input .* name="enable" .*/', $line, $match3);
    preg_match_all('/<input .* name="usepassword" .*/', $line, $match4);
    preg_match_all('/<input .* name="disabledate" .* value="(.*?)"/ui', $line, $match5);
    preg_match_all('/<input .* name="disabletime" .* value="(.*?)"/ui', $line, $match6);
    preg_match_all('/<input .* name="autodisable" .*/', $line, $match7);
    $ccp = array();
    $time = date("Y-m-d H:i:s");
    foreach ($match[1] as $key => $use) {
        // 替换 < > 为空 并赋值为 1
        strripos(str_replace(array("<", ">", "/"), array(""), $match3[0][$key]), "checked") != "46" ? $match3[0][$key] = 0 : $match3[0][$key] = 1;
        strripos(str_replace(array("<", ">", "/"), array(""), $match4[0][$key]), "checked") != "51" ? $match4[0][$key] = 0 : $match4[0][$key] = 1;
        strripos(str_replace(array("<", ">", "/"), array(""), $match7[0][$key]), "checked") != "51" ? $match7[0][$key] = 0 : $match7[0][$key] = 1;

        $ccp[$key] = array(
            "user" => $match[1][$key],
            "pwd" => $match2[1][$key],
            "state" => $match3[0][$key],
            "pwdstate" => $match4[0][$key],
            "disabletime" => $match5[1][$key] . " " . $match6[1][$key],
            "expire" => strtotime($time) > strtotime($match5[1][$key] . " " . $match6[1][$key]) ? 1 : 0,
        );
    }
    return $ccp;
}

/**
 * 查询用户信息
 */
function userquer($column, $ccp)
{
    if (empty($column)) {
        return "不能为空！";
    }
    $result = array_filter($ccp, function ($where) use ($column) {
        return $where['user'] == $column;
    });
    $col = array_column($result, 'disabletime');
    //expire
    $col2 = array_column($result, 'expire');
    return $col2[0] == 1 ? '<h5 style="color: red;display: inline;">到期时间：' . $col[0] . '</h5>' : ($col[0] != "" ? '<h5 style="color: #1E9FFF;display: inline;">到期时间：' . $col[0] . '</h5>' : '<h5 style="color: red;display: inline;">账号不存在</h5>');
}


/**
 * 更新用户信息
 */
function updatequer($column, $ccp)
{
    if (empty($column)) {
        return "不能为空！";
    }
    $result = array_filter($ccp, function ($where) use ($column) {
        return $where['user'] == $column;
    });
    $col = array_column($result, 'disabletime'); //expire
    $col2 = array_column($result, 'expire'); //expire
    return ["disabletime" => $col[0], "expire" => $col2[0]];
}

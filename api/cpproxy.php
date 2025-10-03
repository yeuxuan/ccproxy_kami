<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:32:11
 * @LastEditTime: 2025-01-07 11:16:53
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\api\cpproxy.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */

include("../includes/common.php");
@header('Content-Type: application/json; charset=UTF-8');

// 初始化缓存
$cache = Cache::getInstance();

// 请求类型处理
if (isset($_REQUEST["type"])) {
    $type = $_REQUEST["type"];
    try {
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
                $json = array("code" => "无效事务", "icon" => "5");
        }
    } catch (Exception $e) {
        error_log("Error in request processing: " . $e->getMessage());
        $json = array("code" => "系统错误", "icon" => "5");
    }
} else {
    $json = array("code" => "非法参数", "icon" => "5");
}


echo json_encode($json, JSON_UNESCAPED_UNICODE);

/**
 * 查询方法
 * @param object $DB 数据库对象
 * @return array
 */
function checkquery($DB)
{
    if (!isset($_POST["appcode"], $_POST["user"])) {
        return array("code" => "非法参数", "icon" => "5");
    }

    try {
        // 参数过滤
        $appcode = htmlspecialchars(trim($_POST["appcode"]));
        $user = htmlspecialchars(trim($_POST["user"]));

        // 使用缓存获取应用信息
        $cache = Cache::getInstance();
        $appCacheKey = 'app_' . $appcode;
        $ip = $cache->get($appCacheKey);
        if ($ip === null) {

            $ip = $DB->selectRow("SELECT serverip FROM application WHERE appcode = '" . $appcode . "'");
            if ($ip) {
                $cache->set($appCacheKey, $ip, 3600);
            }
        }

        if (!$ip) {
            return array(-1, "msg" => "应用不存在");
        }

        // 使用缓存获取服务器信息
        $serverCacheKey = 'server_' . $ip['serverip'];
        $server = $cache->get($serverCacheKey);
        
        if ($server === null) {
            $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'");
            if ($server) {
                $cache->set($serverCacheKey, $server, 3600); // 缓存1小时
            }
        }

        if (!$server) {
            return array(-1, "msg" => "服务器配置不存在");
        }

        $ser = query($server['password'], $server['cport'], $server['ip']);

        if (!$ser) {
            return array(
                -3,
                "msg" => '<h5 style="color: red;display: inline;">服务器通信出现问题</h5>'
            );
        }
        return [
            "code" => 1,
            "msg" => userquer($user, $ser)
        ];
    } catch (Exception $e) {
        error_log("Query error: " . $e->getMessage());
        return array(-3, "msg" => "系统错误");
    }
}

/**
 * 添加用户
 * @param object $DB 数据库对象
 * @return array
 */
function checkinsert($DB)
{
    if (!isset($_POST["user"], $_POST["pwd"], $_POST["code"])) {
        return array("code" => "非法参数", "icon" => "5");
    }

    try {
        // 参数过滤
        $user = htmlspecialchars(trim($_POST["user"]));
        $pwd = htmlspecialchars(trim($_POST["pwd"]));
        $code = htmlspecialchars(trim($_POST["code"]));

        // 验证卡密
        $kami = $DB->selectRow("select count(*) as num,app,times,state,ext from kami where kami='" . $code . "' GROUP BY app,times,state,ext");

        if (!$kami || $kami['num'] <= 0) {
            return array("code" => -2, "msg" => "卡密不存在");
        }

        if ($kami['state'] != 0) {
            return array("code" => -1, "msg" => "卡密已被使用");
        }

        // 获取服务器信息
        $ip = $DB->selectRow("select serverip from application where appcode='" . $kami['app'] . "'");

        if (!$ip) {
            return array("code" => -1, "msg" => "应用不存在");
        }

        $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'"); //$ip['serverip']服务器IP
        if (!$server) {
            return array("code" => -1, "msg" => "服务器配置不存在");
        }
        $cache = Cache::getInstance();
        $cache->clear();
        // 检查用户是否存在
        $existingUsers = query($server['password'], $server['cport'], $server['ip']);
        if (!$existingUsers) {
            return array("code" => -3, "msg" => '<h5 style="color: red;display: inline;">服务器通信出现问题</h5>');
        }

        if (!existsuser($user, $existingUsers)) {
            return array("code" => -1, "msg" => "账号已经存在");
        }

        // 添加用户
        $msg = insert(
            $server['ip'],
            $server['serveruser'],
            $server['password'],
            $server['cport'],
            $kami['times'],
            $user,
            $pwd,
            $kami['ext']
        );

        $cache = Cache::getInstance();
        $cache->clear();

        if ($msg["icon"] == 1) {
            $currentDate = date("Y-m-d H:i:s");
            $endDate = calculate_expiry_date($currentDate, $kami['times']);

            // 更新卡密状态
            $updateData = [
                'state' => 1,
                'username' => $user,
                'use_date' => $currentDate,
                'end_date' => $endDate
            ];

            $DB->update('kami', $updateData, "kami = '".$code."'");
            
            return array("code" => 1, "msg" => $msg["code"]);
        }

        return $msg ?: array("code" => -3, "msg" => '<h5 style="color: red;display: inline;">服务器通信出现问题</h5>');
    } catch (Exception $e) {
        error_log("Insert error: " . $e->getMessage());
        return array("code" => -3, "msg" => "系统错误");
    }
}

/**
 * 续费方法
 * @param object $DB 数据库对象
 * @return array
 */
function checkupdate($DB)
{
    if (!isset($_POST["user"], $_POST["code"])) {
        return array("code" => "非法参数", "icon" => "5");
    }

    try {
        // 参数过滤
        $user = htmlspecialchars(trim($_POST["user"]));
        $code = htmlspecialchars(trim($_POST["code"]));

        // 验证卡密
        $kami = $DB->selectRow("select count(*) as num,app,times,state,ext from kami where kami='" . $code . "' GROUP BY app,times,state,ext");

        if (!$kami || $kami['num'] <= 0) {
            return array("code" => -2, "msg" => "卡密不存在");
        }

        if ($kami['state'] != 0) {
            return array("code" => -1, "msg" => "卡密已被使用");
        }

        // 获取服务器信息
        $ip = $DB->selectRow("select serverip from application where appcode='" . $kami['app'] . "'");
        $server = $DB->selectRow("select ip,serveruser,password,cport from server_list where ip='" . $ip['serverip'] . "'"); //$ip['serverip']服务器IP
        $cache = Cache::getInstance();
        $cache->clear();
        // 验证用户存在性
        $users = query($server['password'], $server['cport'], $server['ip']);
        if (!$users) {
            return array("code" => -3, "msg" => '<h5 style="color: red;display: inline;">服务器通信出现问题</h5>');
        }

        if (existsuser($user, $users)) {
            return array("code" => -1, "msg" => "充值账号不存在");
        }

        $userInfo = updatequer($user, $users);
        if (empty($userInfo['disabletime'])) {
            return array("code" => -3, "msg" => '<h5 style="color: red;display: inline;">账号不存在</h5>');
        }

        // 更新用户
        $updateResult = update(
            $server['ip'],
            $server['serveruser'],
            $server['password'],
            $server['cport'],
            $kami['times'],
            $userInfo,
            $kami['ext']
        );

        $cache = Cache::getInstance();
        $cache->clear();

        if ($updateResult["icon"] == 1) {
            $currentDate = date("Y-m-d H:i:s");
            $endDate = calculate_expiry_date(
                $userInfo['expire'] == 0 ? $userInfo['disabletime'] : $currentDate,
                $kami['times']
            );

            // 更新卡密状态
            $updateData = array(
                'state' => 1,
                'username' => $user,
                'use_date' => $currentDate,
                'end_date' => $endDate
            );
            $DB->update('kami', $updateData, "kami='" . $code . "'");

            return array("code" => 1, "msg" => $updateResult);
        }

        return array("code" => -3, "msg" => "更新失败");
    } catch (Exception $e) {
        error_log("Update error: " . $e->getMessage());
        return array("code" => -3, "msg" => "系统错误");
    }
}

/**
 * 添加具体方法
 * @param string $proxyaddress 代理地址
 * @param string $admin_username 管理员用户名
 * @param string $admin_password 管理员密码
 * @param int $admin_port 管理端口
 * @param int|float $day 天数
 * @param string $user 用户名
 * @param string $pwd 密码
 * @param string $ext 扩展配置
 * @return array
 */
function insert($proxyaddress, $admin_username, $admin_password, $admin_port, $day, $user, $pwd, $ext)
{
    try {
        // 输入验证
        if (!CheckStrChinese($user) || strlen($user) < 5) {
            return array("code" => "-1", "msg" => "用户名不合法", "icon" => "5");
        }
        if (!CheckStrPwd($pwd)) {
            return array("code" => "-1", "msg" => "密码不合法", "icon" => "5");
        }

        // 解析扩展配置
        $extData = json_decode($ext, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array("code" => "-1", "msg" => "扩展配置格式错误", "icon" => "5");
        }

        $connection = isset($extData["connection"]) ? $extData["connection"] : '';
        $bandwidth = $extData["bandwidthup"] . "/" . $extData["bandwidthdown"];

        // 计算到期时间
        $date = date("Y-m-d H:i:s");
        $enddate = calculate_expiry_date($date, $day);
        $end_date = explode(" ", $enddate);

        // 连接代理服务器
        $fp = fsockopen($proxyaddress, $admin_port, $errno, $errstr, 10);
        if (!$fp) {
            throw new Exception("无法连接到CCProxy");
        }

        // 构建请求数据
        $url_ = "/account";
        $url = build_account_url(array(
            'add' => 1,
            'autodisable' => 1,
            'enable' => 1,
            'usepassword' => 1,
            'username' => $user,
            'password' => $pwd,
            'connection' => $connection,
            'bandwidth' => $bandwidth,
            'disabledate' => $end_date[0],
            'disabletime' => $end_date[1],
            'userid' => -1
        ));

        // 发送请求
        $response = send_proxy_request($fp, $url_, $url, $proxyaddress, $admin_username, $admin_password);
        fclose($fp);

        return array("code" => "注册用户成功", "icon" => "1");
    } catch (Exception $e) {
        error_log("Insert error: " . $e->getMessage());
        return array("code" => "操作失败", "icon" => "5");
    }
}

/**
 * 删除方法
 * @return array
 */
function del()
{
    if (!isset($_POST["username"])) {
        return array("code" => "非法参数", "icon" => "5");
    }

    try {
        $username = htmlspecialchars(trim($_POST["username"]));

        if (!CheckStrChinese($username) || strlen($username) < 5) {
            return array("-1", "msg" => "用户名不合法", "icon" => "5");
        }

        $admin_username = isset($_POST["admin_username"]) ? $_POST["admin_username"] : "";
        $admin_password = isset($_POST["admin_password"]) ? $_POST["admin_password"] : "";
        $adminport = isset($_POST["admin_port"]) ? $_POST["admin_port"] : "";
        $proxyaddress = isset($_POST["proxyaddress"]) ? $_POST["proxyaddress"] : "";

        $fp = fsockopen($proxyaddress, $adminport, $errno, $errstr, 10);
        if (!$fp) {
            throw new Exception("无法连接到CCProxy");
        }

        $url_ = "/account";
        $url = "delete=1&userid=" . urlencode($username);

        send_proxy_request($fp, $url_, $url, $proxyaddress, $admin_username, $admin_password);
        fclose($fp);

        return array("code" => "删除用户成功", "icon" => "1");
    } catch (Exception $e) {
        error_log("Delete error: " . $e->getMessage());
        return array("code" => "删除失败", "icon" => "5");
    }
}

/**
 * 更新方法
 */
function update($proxyaddress, $admin_username, $admin_password, $admin_port, $day, $date, $ext)
{
    try {
        if (!CheckStrChinese($admin_username) || strlen($admin_username) < 5) {
            return array("-1", "msg" => "用户名不合法", "icon" => "5");
        }

        if (!isset($_POST["user"])) {
            return array("code" => "编辑数据存在非法参数", "icon" => "5");
        }

        $username = htmlspecialchars(trim($_POST["user"]));

        // 解析扩展配置
        $extData = json_decode($ext, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array("-1", "msg" => "扩展配置格式错误", "icon" => "5");
        }

        $connection = isset($extData["connection"]) ? $extData["connection"] : '';
        $bandwidth = $extData["bandwidthup"] . "/" . $extData["bandwidthdown"];

        // 计算到期时间
        $cdate = date("Y-m-d H:i:s");
        $enddate = $date['expire'] == 0
            ? calculate_expiry_date($date['disabletime'], $day)
            : calculate_expiry_date($cdate, $day);

        $end_date = explode(" ", $enddate);

        // 连接代理服务器
        $fp = fsockopen($proxyaddress, $admin_port, $errno, $errstr, 10);
        if (!$fp) {
            throw new Exception("无法连接到CCProxy");
        }

        // 构建请求数据
        $url_ = "/account";
        $url = build_account_url(array(
            'edit' => 1,
            'autodisable' => 1,
            'enable' => 1,
            'usepassword' => 1,
            'username' => $username,
            'connection' => $connection,
            'bandwidth' => $bandwidth,
            'disabledate' => $end_date[0],
            'disabletime' => $end_date[1],
            'userid' => $username
        ));

        // 发送请求
        send_proxy_request($fp, $url_, $url, $proxyaddress, $admin_username, $admin_password);
        fclose($fp);

        return array("code" => "更新用户成功", "icon" => "1");
    } catch (Exception $e) {
        error_log("Update error: " . $e->getMessage());
        return array("code" => "更新失败", "icon" => "5");
    }
}

/**
 * 查询代理服务器信息
 * @param string $adminpassword 管理员密码
 * @param int $adminport 管理端口
 * @param string $proxyaddress 代理地址
 * @param string $adminusername 管理员用户名
 * @return array|false
 */
function query($adminpassword, $adminport, $proxyaddress, $adminusername = "admin")
{
    try {
        // 使用缓存
        $cache = Cache::getInstance();
        $cacheKey = md5($proxyaddress . $adminport . $adminpassword . $adminusername);
        $cached = $cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }
        $url = "http://" . $proxyaddress . ":" . $adminport . "/account";
        $info = parse_url($url);

        // 设置连接超时
        $fp = fsockopen($proxyaddress, $adminport, $errno, $errstr, 10);
        if (!$fp) {
            throw new Exception("Connection failed: $errstr ($errno)");
        }

        $auth = "Authorization: Basic " . base64_encode("$adminusername:" . $adminpassword);
        $head = "GET " . $info['path'] . " HTTP/1.0\r\n";
        $head .= "Host: " . $info['host'] . "\r\n" . $auth . "\r\n\r\n";

        fputs($fp, $head);

        $response = '';
        while (!feof($fp)) {
            $response .= fread($fp, 4096);
        }
        fclose($fp);

        // 解析响应数据
        $patterns = array(
            'username' => '/<input [^>]*name="username"[^>]*value="([^"]*)"/',
            'password' => '/<input [^>]*name="password"[^>]*value="([^"]*)"/',
            'enable' => '/<input [^>]*name="enable"[^>]*/',
            'usepassword' => '/<input [^>]*name="usepassword"[^>]*/',
            'disabledate' => '/<input [^>]*name="disabledate"[^>]*value="([^"]*)"/',
            'disabletime' => '/<input [^>]*name="disabletime"[^>]*value="([^"]*)"/',
            'autodisable' => '/<input [^>]*name="autodisable"[^>]*/'
        );

        $matches = array();
        foreach ($patterns as $key => $pattern) {
            preg_match_all($pattern, $response, $matches[$key]);
        }

        $ccp = array();
        $time = date("Y-m-d H:i:s");

        foreach ($matches['username'][1] as $key => $use) {
            $enable = strripos(str_replace(['<', '>', '/'], '', $matches['enable'][0][$key]), 'checked') !== false ? 1 : 0;
            $usepassword = strripos(str_replace(['<', '>', '/'], '', $matches['usepassword'][0][$key]), 'checked') !== false ? 1 : 0;
            $autodisable = strripos(str_replace(['<', '>', '/'], '', $matches['autodisable'][0][$key]), 'checked') !== false ? 1 : 0;

            $disabletime = $matches['disabledate'][1][$key] . " " . $matches['disabletime'][1][$key];

            $ccp[$key] = array(
                "user" => $use,
                "pwd" => $matches['password'][1][$key],
                "state" => $enable,
                "pwdstate" => $usepassword,
                "disabletime" => $disabletime,
                "expire" => strtotime($time) > strtotime($disabletime) ? 1 : 0,
            );
        }

        // 缓存结果
        $cache->set($cacheKey, $ccp, 300); // 缓存5分钟
        return $ccp;
    } catch (Exception $e) {
        error_log("Query error: " . $e->getMessage());
        return false;
    }
}

/**
 * 用户查询
 * @param string $column 用户名
 * @param array $ccp 用户数据
 * @return string
 */
function userquer($column, $ccp)
{
    if (empty($column)) {
        return "不能为空！";
    }

    $result = array_filter($ccp, function ($where) use ($column) {
        return $where['user'] === $column;
    });

    if (empty($result)) {
        return '<h5 style="color: red;display: inline;">账号不存在</h5>';
    }

    $user = reset($result);
    $disabletime = $user['disabletime'];

    return $user['expire'] == 1
        ? '<h5 style="color: red;display: inline;">到期时间：' . $disabletime . '</h5>'
        : '<h5 style="color: #1E9FFF;display: inline;">到期时间：' . $disabletime . '</h5>';
}

/**
 * 更新查询
 * @param string $column 用户名
 * @param array $ccp 用户数据
 * @return array
 */
function updatequer($column, $ccp)
{
    if (empty($column)) {
        return array("disabletime" => "", "expire" => 0);
    }

    $result = array_filter($ccp, function ($where) use ($column) {
        return $where['user'] === $column;
    });

    if (empty($result)) {
        return array("disabletime" => "", "expire" => 0);
    }

    $user = reset($result);
    return array(
        "disabletime" => $user['disabletime'],
        "expire" => $user['expire']
    );
}


/**
 * 构建账户URL参数
 * @param array $params 参数数组
 * @return string
 */
function build_account_url($params)
{
    $defaults = array(
        'enablesocks' => 1,
        'enablewww' => 0,
        'enabletelnet' => 0,
        'enabledial' => 0,
        'enableftp' => 0,
        'enableothers' => 0,
        'enablemail' => 0,
    );

    $params = array_merge($defaults, $params);
    return http_build_query($params);
}

/**
 * 发送代理请求
 * @param resource $fp 文件句柄
 * @param string $url_ URL路径
 * @param string $url URL参数
 * @param string $proxyaddress 代理地址
 * @param string $admin_username 管理员用户名
 * @param string $admin_password 管理员密码
 * @return string
 */
function send_proxy_request($fp, $url_, $url, $proxyaddress, $admin_username, $admin_password)
{
    $len = "Content-Length: " . strlen($url);
    $auth = "Authorization: Basic " . base64_encode($admin_username . ":" . $admin_password);
    $msg = "POST " . $url_ . " HTTP/1.0\r\nHost: " . $proxyaddress . "\r\n" . $auth . "\r\n" . $len . "\r\n\r\n" . $url;

    fputs($fp, $msg);

    $response = '';
    while (!feof($fp)) {
        $response .= fgets($fp, 4096);
    }

    return $response;
}

/**  
 * 计算到期时间  
 * @param string $current_date 当前时间  
 * @param string $duration 时长（支持格式如：+1 day、+7 day、+1 hour、+1 year、+1 month）  
 * @return string  
 */  
function calculate_expiry_date($current_date, $duration)  
{  
    try {  
        // 初始化当前时间  
        $date = new DateTime($current_date);  

        // 检查 $duration 是否符合 DateInterval 支持的格式  
        if (preg_match('/^\+\d+ (day|hour|month|year)$/', $duration)) {  
            // 使用 strtotime 解析 $duration 并更新时间  
            $date->modify($duration);  
        } else {  
            throw new Exception("Invalid duration format: $duration");  
        }  

        // 返回计算后的时间  
        return $date->format('Y-m-d H:i:s');  
    } catch (Exception $e) {  
        // 捕获异常并记录错误日志  
        error_log("Date calculation error: " . $e->getMessage());  
        // 默认返回当前时间加一天  
        return date('Y-m-d H:i:s', strtotime("+1 day"));  
    }  
}  
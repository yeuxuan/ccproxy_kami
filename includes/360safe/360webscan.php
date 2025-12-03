<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 12:25:10
 * @LastEditTime: 2025-01-04 20:41:23
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\includes\360safe\360webscan.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */

webscan_error();
require_once('webscan_cache.php');

// 初始化安全日志目录
if ($security_config['enable_logging'] && !is_dir($security_config['log_path'])) {
    mkdir($security_config['log_path'], 0755, true);
}


/**
 *   关闭用户错误提示
 */
function webscan_error()
{
    if (ini_get('display_errors')) {
        ini_set('display_errors', '0');
    }
}

/**
 * 改进的攻击检查拦截函数
 */
function webscan_StopAttack($key, $value, $attack_type, $method)
{
    global $modern_attack_patterns, $rate_limit, $security_config;

    // 参数验证
    if (!is_string($key) && !is_numeric($key)) {
        return false;
    }

    // 安全转换
    $key = (string)$key;
    $value = is_array($value) ? webscan_arr_foreach($value) : (string)$value;

    try {
        // 请求频率限制检查
        if ($rate_limit['enabled'] && !check_rate_limit()) {
            webscan_slog([
                'ip' => filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP),
                'type' => 'rate_limit',
                'time' => date('Y-m-d H:i:s')
            ]);
            exit(webscan_pape('请求过于频繁，请稍后再试'));
        }

        // 请求大小检查
        if (
            isset($_SERVER['CONTENT_LENGTH']) &&
            is_numeric($_SERVER['CONTENT_LENGTH']) &&
            $_SERVER['CONTENT_LENGTH'] > $security_config['max_post_size']
        ) {
            exit(webscan_pape('请求数据过大'));
        }

        // 检查现代攻击特征
        foreach ($modern_attack_patterns as $pattern_type => $patterns) {
            foreach ($patterns as $pattern) {
                // 转义模式中的 ~ 字符，使用 ~ 作为分隔符避免与模式中的 / 冲突
                $safePattern = str_replace('~', '\~', $pattern);
                
                // 确保正则表达式有效
                if (@preg_match("~{$safePattern}~is", '') === false) {
                    error_log("Invalid regex pattern: $pattern");
                    continue;
                }

                if (@preg_match("~{$safePattern}~is", $value) || @preg_match("~{$safePattern}~is", $key)) {
                    $attack_info = [
                        'ip' => filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP),
                        'time' => date('Y-m-d H:i:s'),
                        'page' => filter_var($_SERVER["PHP_SELF"], FILTER_SANITIZE_URL),
                        'method' => $method,
                        'type' => $pattern_type,
                        'key' => substr($key, 0, 100), // 限制长度
                        'value' => substr($value, 0, 500), // 限制长度
                        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ?
                            substr($_SERVER['HTTP_USER_AGENT'], 0, 200) : '',
                        'url' => isset($_SERVER["REQUEST_URI"]) ?
                            filter_var($_SERVER["REQUEST_URI"], FILTER_SANITIZE_URL) : ''
                    ];

                    webscan_slog($attack_info);
                    exit(webscan_pape("检测到潜在的{$pattern_type}攻击"));
                }
            }
        }
    } catch (Exception $e) {
        error_log("WebScan Error: " . $e->getMessage());
        return false;
    }
}

/**
 * 改进的日志记录函数
 */
function webscan_slog($log_info)
{
    global $security_config;

    if (!$security_config['enable_logging']) {
        return true;
    }

    try {
        // 验证日志目录
        $log_dir = rtrim($security_config['log_path'], '/');
        if (!is_dir($log_dir)) {
            if (!mkdir($log_dir, 0755, true)) {
                error_log("Failed to create log directory: $log_dir");
                return false;
            }
        }

        // 验证日志目录可写
        if (!is_writable($log_dir)) {
            error_log("Log directory not writable: $log_dir");
            return false;
        }

        $log_file = $log_dir . '/' . date('Y-m-d') . '_security.log';

        // 清理和验证日志数据
        $log_info = array_map(function ($item) {
            return is_string($item) ? strip_tags($item) : $item;
        }, $log_info);

        $log_data = date('Y-m-d H:i:s') . ' | ' .
            json_encode($log_info, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR) . "\n";

        return file_put_contents($log_file, $log_data, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        error_log("Log writing error: " . $e->getMessage());
        return false;
    }
}

/**
 * 请求频率限制检查
 */
function check_rate_limit()
{
    global $rate_limit;

    try {
        $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        if (!$ip) {
            error_log("Invalid IP address detected");
            return false;
        }

        $cache_key = "rate_limit:" . md5($ip);
        $temp_dir = sys_get_temp_dir();

        // 验证临时目录
        if (!is_writable($temp_dir)) {
            error_log("Temp directory not writable: $temp_dir");
            return true; // 如果无法写入，默认允许请求
        }

        $cache_file = $temp_dir . '/' . $cache_key;

        if (file_exists($cache_file)) {
            $data = @unserialize(file_get_contents($cache_file));
            if ($data === false || !is_array($data)) {
                unlink($cache_file); // 删除损坏的缓存文件
                $data = ['count' => 1, 'start' => time()];
            } else {
                if (time() - $data['start'] <= $rate_limit['window']) {
                    if ($data['count'] >= $rate_limit['max_requests']) {
                        return false;
                    }
                    $data['count']++;
                } else {
                    $data = ['count' => 1, 'start' => time()];
                }
            }
        } else {
            $data = ['count' => 1, 'start' => time()];
        }

        file_put_contents($cache_file, serialize($data), LOCK_EX);
        return true;
    } catch (Exception $e) {
        error_log("Rate limit error: " . $e->getMessage());
        return true; // 出错时默认允许请求
    }
}

/**
 * 改进的防护提示页面
 */
function webscan_pape($message = '检测到潜在的安全威胁')
{
    $log_data = [
        'IP' => $_SERVER["REMOTE_ADDR"],
        '时间' => date('Y-m-d H:i:s'),
        '页面' => $_SERVER["PHP_SELF"],
        '方法' => $_SERVER["REQUEST_METHOD"],
        '数据' => $_REQUEST
    ];

    // 记录攻击日志
    file_put_contents(
        "security_log.txt",
        json_encode($log_data, JSON_UNESCAPED_UNICODE) . "\n",
        FILE_APPEND
    );

    header('Content-Type: text/html; charset=utf-8');
    return <<<HTML
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>站点提示信息</title>
        <style type="text/css">
            html {
                background: #eee;
                text-align: center;
            }

            body {
                background: #fff;
                color: #333;
                font-family: "微软雅黑", "Microsoft YaHei", sans-serif;
                margin: 2em auto;
                padding: 1em 2em;
                max-width: 700px;
                -webkit-box-shadow: 10px 10px 10px rgba(0, 0, 0, .13);
                box-shadow: 10px 10px 10px rgba(0, 0, 0, .13);
                opacity: .8
            }

            h1 {
                border-bottom: 1px solid #dadada;
                clear: both;
                color: #666;
                font: 24px "微软雅黑", "Microsoft YaHei", , sans-serif;
                margin: 30px 0 0 0;
                padding: 0;
                padding-bottom: 7px
            }

            #error-page {
                margin-top: 50px
            }

            h3 {
                text-align: center
            }

            #error-page p {
                font-size: 9px;
                line-height: 1.5;
                margin: 25px 0 20px
            }

            #error-page code {
                font-family: Consolas, Monaco, monospace
            }

            ul li {
                margin-bottom: 10px;
                font-size: 9px
            }

            a {
                color: #21759B;
                text-decoration: none;
                margin-top: -10px
            }

            a:hover {
                color: #D54E21
            }

            .button {
                background: #f7f7f7;
                border: 1px solid #ccc;
                color: #555;
                display: inline-block;
                text-decoration: none;
                font-size: 9px;
                line-height: 26px;
                height: 28px;
                margin: 0;
                padding: 0 10px 1px;
                cursor: pointer;
                -webkit-border-radius: 3px;
                -webkit-appearance: none;
                border-radius: 3px;
                white-space: nowrap;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                -webkit-box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0, 0, 0, .08);
                box-shadow: inset 0 1px 0 #fff, 0 1px 0 rgba(0, 0, 0, .08);
                vertical-align: top
            }

            .button.button-large {
                height: 29px;
                line-height: 28px;
                padding: 0 12px
            }

            .button:focus,
            .button:hover {
                background: #fafafa;
                border-color: #999;
                color: #222
            }

            .button:focus {
                -webkit-box-shadow: 1px 1px 1px rgba(0, 0, 0, .2);
                box-shadow: 1px 1px 1px rgba(0, 0, 0, .2)
            }

            .button:active {
                background: #eee;
                border-color: #999;
                color: #333;
                -webkit-box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, .5);
                box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, .5)
            }

            table {
                table-layout: auto;
                border: 1px solid #333;
                empty-cells: show;
                border-collapse: collapse
            }

            th {
                padding: 4px;
                border: 1px solid #333;
                overflow: hidden;
                color: #333;
                background: #eee
            }

            td {
                padding: 4px;
                border: 1px solid #333;
                overflow: hidden;
                color: #333
            }
        </style>
    </head>
    <body id="error-page">
        <h3>站点提示信息</h3>
        {$message}
    </body>
</html>
HTML;
}

/**
 *  参数拆分
 */
function webscan_arr_foreach($arr)
{
    static $str;
    static $keystr;
    if (!is_array($arr)) {
        return $arr;
    }
    foreach ($arr as $key => $val) {
        $keystr = $keystr . $key;
        if (is_array($val)) {

            webscan_arr_foreach($val);
        } else {

            $str[] = $val . $keystr;
        }
    }
    return implode($str);
}

/**  
 * 拦截目录白名单  
 *   
 * @param string $webscan_white_name 白名单名称  
 * @param array $webscan_white_url 白名单的 URL 数组，格式 ['路径关键字' => '查询参数关键字']  
 *   
 * @return bool true 表示通过，false 表示拦截  
 */
function webscan_white($webscan_white_name, $webscan_white_url = array())
{
    // 获取当前脚本路径和查询参数  
    $url_path = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $url_var = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

    // 判断白名单名称是否匹配路径  
    if (!empty($webscan_white_name) && preg_match('/' . preg_quote($webscan_white_name, '/') . '/is', $url_path)) {
        return false; // 如果白名单名称匹配，直接放行  
    }

    // 遍历白名单的 URL 条件  
    foreach ($webscan_white_url as $key => $value) {
        // 确保键和值为有效的字符串或整数类型  
        if ((is_string($key) || is_int($key)) && (is_string($value) || is_int($value))) {
            // 查询参数不为空的情况  
            if (!empty($url_var) && !empty($value)) {
                // 转换查询参数为字符串（防止非字符串类型引发错误）  
                if (!is_string($url_var)) {
                    $url_var = strval($url_var);
                }
                // 如果路径和查询参数分别匹配对应的白名单条件  
                if (stristr($url_path, $key) && stristr($url_var, $value)) {
                    return false; // 满足条件放行  
                }
            }
            // 查询参数为空的情况  
            elseif (empty($url_var) && empty($value)) {
                if (stristr($url_path, $key)) {
                    return false; // 仅匹配路径关键字时放行  
                }
            }
        }
    }

    // 如果未匹配任何白名单条件  
    return true;
}

// 主要检测逻辑
if ($webscan_switch && webscan_white($webscan_white_directory, $webscan_white_url)) {
    try {
        // GET参数检查
        if ($webscan_get && !empty($_GET)) {
            foreach ($_GET as $key => $value) {
                webscan_StopAttack($key, $value, 'get', "GET");
            }
        }

        // POST参数检查
        if ($webscan_post && !empty($_POST)) {
            foreach ($_POST as $key => $value) {
                webscan_StopAttack($key, $value, 'post', "POST");
            }
        }

        // Cookie检查
        if ($webscan_cookie && !empty($_COOKIE)) {
            foreach ($_COOKIE as $key => $value) {
                webscan_StopAttack($key, $value, 'cookie', "COOKIE");
            }
        }

        // HTTP头检查
        if ($webscan_headers) {
            // 白名单：排除浏览器自动生成的标准 HTTP Headers
            // 这些 headers 是协议规定的格式，用户无法直接注入恶意内容
            $skip_headers = [
                'HTTP_COOKIE',                      // 已在 Cookie 检查中逐个检测
                'HTTP_USER_AGENT',                  // 浏览器标识，包含版本号如 "NT 10.0" 是正常的
                'HTTP_ACCEPT',                      // 浏览器接受的 MIME 类型，包含分号和斜杠是正常的
                'HTTP_ACCEPT_LANGUAGE',             // 语言偏好
                'HTTP_ACCEPT_ENCODING',             // 编码偏好（如 gzip, deflate）
                'HTTP_CONNECTION',                  // 连接类型
                'HTTP_HOST',                        // 目标主机
                'HTTP_CACHE_CONTROL',               // 缓存控制
                'HTTP_UPGRADE_INSECURE_REQUESTS',   // 升级请求标志
                'HTTP_SEC_FETCH_SITE',              // Fetch Metadata
                'HTTP_SEC_FETCH_MODE',
                'HTTP_SEC_FETCH_USER',
                'HTTP_SEC_FETCH_DEST',
                'HTTP_SEC_CH_UA',                   // Client Hints
                'HTTP_SEC_CH_UA_MOBILE',
                'HTTP_SEC_CH_UA_PLATFORM',
            ];
            foreach ($_SERVER as $key => $value) {
                if (is_string($key) && stripos($key, 'HTTP_') === 0 && !in_array($key, $skip_headers)) {
                    webscan_StopAttack($key, $value, 'header', "HEADER");
                }
            }
        }

        // Referer检查
        if ($webscan_referre && isset($_SERVER['HTTP_REFERER'])) {
            webscan_StopAttack('HTTP_REFERER', $_SERVER['HTTP_REFERER'], 'referer', "REFERER");
        }
    } catch (Exception $e) {
        error_log("WebScan main logic error: " . $e->getMessage());
    }
}

// 设置安全响应头
foreach ($security_headers as $header => $value) {
    if (is_string($header) && is_string($value)) { // 检查是否为字符串  
        $headerValue = trim($value); // Remove any leading/trailing whitespace
        if (strpos($headerValue, "\n") === false && strpos($headerValue, "\r") === false) {
            header("$header: $headerValue");
        } else {
            error_log('Invalid header value detected: ' . $headerValue);
        }
    } else {
        error_log("Invalid header: $header or value: $value");
    }
}

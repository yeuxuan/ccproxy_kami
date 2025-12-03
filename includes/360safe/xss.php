<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:32:11
 * @LastEditTime: 2025-01-11 13:34:11
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \undefinedc:\Users\liuqiang\Desktop\ccp\ccproxy_kami\includes\360safe\xss.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */

/**  
 * 通用防护升级版示例 (PHP 7.4+)，修正 Unknown modifier 错误  
 *  
 * 主要修正点：  
 * 1. 在正则匹配时，使用 "~" 作为分隔符，避免与模式内部 "/" 冲突。  
 * 2. 针对模式内部可能的 "~" 字符先行转义，保证正则正常执行。  
 */

declare(strict_types=1);

/**************************************  
 * 1. 函数定义（必须在调用之前）  
 **************************************/

/**  
 * 检查当前请求是否在定义的白名单数组里  
 * 命中则返回 true  
 */
function isWhitelistedRequest(array $paths): bool
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    foreach ($paths as $path) {
        // 使用 strpos() 判断是否以白名单路径开头  
        if (strpos($requestUri, $path) === 0) {
            return true;
        }
    }
    return false;
}

/**  
 * 递归检测数组或单值  
 */
function checkRequestData($data, array $patterns): void
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            // 检查 Key  
            if (!is_array($key)) {
                checkString((string)$key, $patterns);
            } else {
                checkRequestData($key, $patterns);
            }
            // 检查 Value  
            if (!is_array($value)) {
                checkString((string)$value, $patterns);
            } else {
                checkRequestData($value, $patterns);
            }
        }
    } else {
        checkString((string)$data, $patterns);
    }
}

/**  
 * 核心检测逻辑：对字符串进行正则匹配  
 * 使用 '~' 作为分隔符，避免与模式内 '/' 冲突  
 */
function checkString(string $str, array $patterns): void
{
    // 空或过短字符串可跳过  
    if (trim($str) === '' || mb_strlen($str) < 2) {
        return;
    }

    // 对原始和 urlencode 后的字符串均进行匹配  
    $encoded = urlencode($str);

    foreach ($patterns as $rawPattern) {
        // 如果模式里有 "~"，先转义，防止充当分隔符冲突  
        $safePattern = str_replace('~', '\~', $rawPattern);

        // 构造安全的正则表达式：~模式~i  
        $regex = "~{$safePattern}~i";

        if (@preg_match($regex, $str) === 1 || @preg_match($regex, $encoded) === 1) {
            // 可疑请求，记录日志并拦截  
            logSuspiciousAttempt($str, $rawPattern);
            denyRequest();
        }
    }
}

/**  
 * 拦截请求并给出提示  
 */
function denyRequest(): void
{
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>站点提示信息</title>
    <style type="text/css">
        html { background: #eee; text-align: center; }
        body { background: #fff; color: #333; font-family: "微软雅黑", sans-serif; margin: 2em auto; padding: 1em 2em; max-width: 700px; box-shadow: 10px 10px 10px rgba(0,0,0,.13); }
        h3 { text-align: center }
    </style>
</head>
<body id="error-page">
    <h3>站点提示信息</h3>
    请求中包含非法或可疑参数，已被拦截。
</body>
</html>
HTML;
    exit;
}

/**  
 * 记录可疑请求到日志  
 */
function logSuspiciousAttempt(string $str, string $pattern): void
{
    $logFile = __DIR__ . '/attack_' . date('Ymd') . '.log';
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN_METHOD';
    $uri = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN_URI';
    $time = date('Y-m-d H:i:s');

    $logContent = sprintf(
        "[%s][IP: %s][Method: %s][URI: %s]\nMatched String: %s\nMatched Pattern: %s\n\n",
        $time, $clientIP, $method, $uri, $str, $pattern
    );

    @file_put_contents($logFile, $logContent, FILE_APPEND | LOCK_EX);
}

/**************************************  
 * 2. 白名单检查  
 **************************************/

$whitelistedPaths = ['/sub_admin', '/api/cpproxy.php', '/'];

// 判断当前请求是否命中白名单
if (isWhitelistedRequest($whitelistedPaths)) {
    return;
}

// 白名单中的合法 IP 列表  
$allowed_ips = ["192.168.1.6", "127.0.0.1"];
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';

if (in_array($client_ip, $allowed_ips)) {
    return;
}

/**************************************  
 * 3. 定义特征库  
 **************************************/

/**  
 * XSS 相关特征  
 */
$xssPatterns = [
    "[\"'`;\\*<>].*\\bon[a-zA-Z]{3,15}\\s*=.*",
    "<(?:script|iframe|body|img|svg|video|audio|embed|object|applet|link|style|meta|base|form|marquee|input).*?>",
    "(?:javascript|data|vbscript|mocha|livescript|blob):",
    "expression\\s*\\(|eval\\s*\\(",
    "url\\s*\\((?:['\"])*(?:\\#|data:|javascript:|vbscript:)",
    "(?:document\\.(?:cookie|write|location)|window\\.(?:location|open|alert|eval))",
    "\\{\\{.*?\\}\\}",
];

/**  
 * SQL注入攻击特征模式  
 */
$sqlInjectionPatterns = [
    "(?i)(select|update|insert|delete|union|drop|create|alter|truncate|exec|declare|rename)",
    "(?i)(sleep\\s*\\([\\s\\d]+\$|benchmark\\s*\\(|pg_sleep|waitfor\\s+delay|delay\\s+'\\d+)",
    "(?i)(outfile|dumpfile|load_file|information_schema\\.(?:tables|columns)|sys\\.(?:user_tables|tab)|all_tables)",
    "(?i)(master\\.\\.|msysaccessobjects|msysqueries|sysobjects|syscolumns|sysusers|xp_cmdshell)",
    "\\$(?:gt|lt|ne|eq|regex|where)|\\{\\s*\\$(?:where|gt|lt|ne|eq)",
    "(?i)(case\\s+when|if\\s*\\(|substr\\s*\\(|mid\\s*\\(|length\\s*\\()",
];

/**  
 * 命令注入特征  
 */
$cmdInjectionPatterns = [
    "(?i)(\\b(?:rm|cat|wget|curl|nc|netcat|bash|sh|python|perl|ruby|lua)\\b)",
    "(?:system|exec|passthru|shell_exec|popen|proc_open|pcntl_exec)\\s*\\(",
    "(?i)(powershell|iex|invoke-expression|encodedcommand)",
    "\\$(?:ENV|_ENV|_SERVER|GLOBALS)\\[",
    "(?:>|<)\\s*/dev/(?:tcp|udp)/[\\d.]+/\\d+",
];

/**  
 * 文件上传特征  
 */
$fileUploadPatterns = [
    "(?i)\\.(jpg|gif|png)\\.(php|asp|jsp)$",
    "^(?:4D5A|7F454C46|CAFEBABE|FFD8FFE0)",
];

/**  
 * SSRF攻击特征（在 URL 上下文中匹配）  
 */
$ssrfPatterns = [
    "(?:gopher|dict|ldap|tftp)://",
];

/**  
 * 路径穿越检测  
 */
$dirTraversalPatterns = [
    '#(?<![a-zA-Z0-9_])\\.\\./.*#',
    '#(?<![a-zA-Z0-9_])(?:%2e%2e|%2e%2e%2f|%252e%252e%252f)#i',
    '#(%00|\\0|\\u0000|\\x00)#',
];

// 检查 URI 中的路径穿越
$uri = $_SERVER['REQUEST_URI'] ?? '';
foreach ($dirTraversalPatterns as $pattern) {
    if (@preg_match($pattern, $uri) === 1) {
        logSuspiciousAttempt($uri, $pattern);
        denyRequest();
    }
}

/**  
 * 合并所有特征  
 */
$globalPatterns = array_merge(
    $xssPatterns,
    $sqlInjectionPatterns,
    $cmdInjectionPatterns,
    $ssrfPatterns,
    $fileUploadPatterns
);

/**************************************  
 * 4. 执行检测  
 **************************************/

$checkParams = [
    'GET' => $_GET,
    'POST' => $_POST,
    'COOKIE' => $_COOKIE,
];

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// 统一检测
checkRequestData([$referer, $queryString], $globalPatterns);
foreach ($checkParams as $type => $data) {
    checkRequestData($data, $globalPatterns);
}

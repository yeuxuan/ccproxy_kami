<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 12:25:10
 * @LastEditTime: 2025-01-05 21:31:47
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\includes\360safe\webscan_cache.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */

// 基础配置
$webscan_switch = 1;  // 总开关，控制整个扫描功能的启用或禁用

// 检测方法配置
$webscan_post = 1;    // 启用POST请求检测
$webscan_get = 1;     // 启用GET请求检测
$webscan_cookie = 1;  // 启用Cookie检测
$webscan_referre = 1; // 启用Referer检测
$webscan_headers = 1; // 启用请求头检测

// 安全配置
$security_config = [
    'max_post_size' => 1024 * 1024 * 15, // 15MB，限制POST请求的最大大小
    'max_request_length' => 8000, // 最大请求长度
    'enable_logging' => true, // 启用日志记录
    'log_path' => 'logs/security/', // 修改为相对路径，相对于网站根目录
];

// 白名单配置
$webscan_white_directory = 'sub_admin|api/internal'; // 允许访问的目录

// URL白名单配置 - 支持正则表达式
$webscan_white_url = array(
    // 管理后台
    'admin' => array(
        'pattern' => '/^\/admin\/[\w-]+\/?$/i', // 匹配管理后台URL
        'methods' => ['GET', 'POST'] // 允许的HTTP方法
    ),
    // API endpoints
    'api' => array(
        'pattern' => '/^\/api\/v[0-9]+\//i', // 匹配API端点
        'methods' => ['GET', 'POST', 'PUT', 'DELETE'] // 允许的HTTP方法
    )
);

// 现代攻击特征库
$modern_attack_patterns = array(  
    'sql_injection' => array(  
        // 常见SQL注入模式  
        'SLEEP\s*?\(.*?\)',  
        'BENCHMARK\s*?\(.*?\)',  
        'WAIT\s*?FOR\s*?DELAY',  
        '\{\$where\s*:',  
        '\{\$regex\s*:',  
        ';\s*?(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER)',  
        'UNION\s+ALL\s+SELECT',  
        '%bf%27',  
        '%df%27',  
        'introspection\s*?\{',  
        '__schema\s*?\{',  
        'OR\s+1=1',  
        'UNION\s+SELECT',  
        '--',  
        // 新增SQL注入特征  
        'HAVING\s+\d+=\d+',  
        'ORDER\s+BY\s+\d+',  
        'LOAD_FILE\s*\(',  
        'INTO\s+OUTFILE',  
        'INTO\s+DUMPFILE',  
        'CHAR\(.+?\)',  
        'CONCAT\(.+?\)',  
        'INFORMATION_SCHEMA',  
        '@@version',  
        'xp_cmdshell',  
        'sysobjects',  
    ),  
    'xss' => array(  
        // 常见XSS攻击模式  
        'javascript:.*?\\(.*?\\)',  
        'data:text/html',  
        'vbscript:',  
        '(?:<|%3C)script.*?(?:>|%3E)',  
        '(?:<|%3C).*?on\w+\s*=',  
        'base64.*?,.*?\\(',   
        'addEventListener\s*\\(',  
        'attachEvent\s*\\(',  
        '\{\{.*?\}\}',  
        '\${.*?}',  
        'ng-\w+=".*?"',  
        'v-\w+=".*?"',  
        'react-\w+=".*?"',  
        '<img\s+src=.*?onerror=',  
        '<iframe',  
        // 新增XSS攻击特征  
        '(location.href|window.location|document.cookie)',  
        '<svg.*?on.*?=',  
        '</textarea>.*?<script>',  
        '"><script>alert\(.*?\)</script>',  
        'document\.write\(',  
        '<iframe.*?src=.*?>',  
        'data:text/html;base64,.*?',  
        '&#x[0-9A-F]+;',  
        '<audio.*?on.*?=',  
        '<video.*?on.*?=',  
        '<svg.*?on\w+=',  
    ),  
    'file_inclusion' => array(  
        // 文件包含攻击模式  
        '(?:\.\.|%2f)(?:\.|%2e)',  
        'php://filter',  
        'zip://',  
        'phar://',  
        'file://',  
        'php://input',  
        // 新增文件包含特征  
        'data://',  
        'ftp://',  
        'gopher://',  
        'rar://',  
        'compress.zlib://',  
        'compress.bzip2://',  
        'expect://',  
        'glob://',  
        'php://output',  
        'php://temp',  
    ),  
    'command_injection' => array(  
        // 命令注入攻击模式  
        '(?:\|\s*(?:wget|curl|bash|cmd|powershell))',  
        '(?:;|\{\}\s*)\s*(?:wget|curl|bash|cmd|powershell)',  
        'system\s*\(.+\)',  
        'exec\s*\(.+\)',  
        'shell_exec\s*\(.+\)',  
        '&&\s*(?:wget|curl|bash|cmd|powershell)',  
        // 新增命令注入特征  
        'passthru\s*\(.+\)',  
        'proc_open\s*\(.+\)',  
        'popen\s*\(.+\)',  
        '\`[^`]+\`',  // 反引号命令执行（要求至少一个字符）
        '\$\([^)]+\)', // $(command)格式（要求至少一个字符）
        '\s+2>&1\s*', // I/O重定向（要求前后有空白或位于末尾）
        ';\s*(ls|cat|whoami|id|ps|netstat|pwd)\b',  // 分号后直接跟命令（更精确）
        '(?:^|[;&|])\s*(ls|cat|whoami|id|ps|netstat)\s', // 命令开头或管道/分号后跟命令
        '(?:\\\\x[a-fA-F0-9]{2}){3,}', // 十六进制命令变种：至少3组\xNN（如\x73\x79\x73）
        '\|\s*python\b',  
        '\|\s*perl\b',  
        '\|\s*php\b',  
    ),  
    // 'ssrf' 规则已禁用 - 用户可能合法输入内网地址（如配置服务器地址）
    // SSRF 防护应在实际发起 HTTP 请求的代码中检查目标地址
    // 如需启用，请取消下方注释
    /*
    'ssrf' => array(  
        // SSRF攻击模式 - 需要在 URL 上下文中匹配，避免误报
        // 匹配 URL 协议后的内网地址（如 http://localhost, file://127.0.0.1）
        '(?:https?|ftp|file|gopher|dict)://localhost(?:[:/]|$)',  
        '(?:https?|ftp|file|gopher|dict)://127\.0\.0\.1(?:[:/]|$)',  
        '(?:https?|ftp|file|gopher|dict)://0\.0\.0\.0(?:[:/]|$)',  
        '(?:https?|ftp|file|gopher|dict)://\[?::1\]?(?:[:/]|$)',  
        // AWS/云服务元数据端点
        '(?:https?|ftp|file)://169\.254\.\d{1,3}\.\d{1,3}(?:[:/]|$)',  
        // 内网 IP 段（完整 IP 格式，在 URL 上下文中）
        '(?:https?|ftp|file)://192\.168\.\d{1,3}\.\d{1,3}(?:[:/]|$)',  
        '(?:https?|ftp|file)://10\.\d{1,3}\.\d{1,3}\.\d{1,3}(?:[:/]|$)',  
        '(?:https?|ftp|file)://172\.(1[6-9]|2[0-9]|3[0-1])\.\d{1,3}\.\d{1,3}(?:[:/]|$)',  
        // @ 符号绕过检测（如 http://evil.com@127.0.0.1）
        '@(?:localhost|127\.0\.0\.1|192\.168\.\d+\.\d+|10\.\d+\.\d+\.\d+)(?:[:/]|$)',
    ),
    */  
    'serialization' => array(  
        // 反序列化攻击模式  
        'O:[0-9]+:"', // PHP序列化格式  
        'C:[0-9]+:"', // PHP序列化特征  
        '__wakeup',  
        '__destruct',  
        '__toString',  
        'unserialize\s*\(',  
        'yaml_parse\s*\(',  
    ),  
    // 'upload' 规则已禁用 - 在 GET/POST 参数中检测文件扩展名误报率高
    // 文件上传安全应在实际上传处理代码中检查（验证扩展名、MIME、文件内容）
    // 如需启用，请取消下方注释
    /*
    'upload' => array(  
        // 文件上传攻击模式  
        '\.php\d?$',  
        '\.phtml$',  
        '\.php\.jpg$',  
        '\.php\.png$',  
        '\.php%00\.jpg$',  
        'application/x-httpd-php',  
        'text/php',  
        'application/php',  
    )  
    */
); 

// 请求频率限制
$rate_limit = array(
    'enabled' => true, // 启用请求频率限制
    'window' => 60, // 时间窗口（秒）
    'max_requests' => 900, // 最大请求数
    'block_time' => 600 // 阻止时间（秒）
);

// 响应头安全配置（已放宽限制）
$security_headers = array(
    // 'X-Frame-Options' => 'SAMEORIGIN', // 已禁用，允许 iframe 嵌入
    'X-Content-Type-Options' => 'nosniff', // 防止MIME类型混淆攻击
    // 'X-XSS-Protection' => '1; mode=block', // 已禁用，现代浏览器已弃用此头
    // 'Content-Security-Policy' 已禁用，避免限制过严
    'Referrer-Policy' => 'no-referrer-when-downgrade', // 放宽引用策略
);

// 设置安全响应头
foreach ($security_headers as $header => $value) {
    $value = trim($value);

    // 确保头信息值中没有换行符，防止HTTP响应拆分攻击
    if (strpos($value, "\n") === false && strpos($value, "\r") === false) {
        header("$header: $value");
    } else {
        error_log('Invalid header value detected: ' . $value);
    }
}
?>
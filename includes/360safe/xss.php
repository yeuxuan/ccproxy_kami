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


/*************************************  
 * 0. 白名单检查  
 *************************************/
/**  
 * 如需维护多个白名单路径，可将它们加入该数组  
 * 例如：$whitelistedPaths = ['/sub_admin', '/api/white_list'];  
 */
$whitelistedPaths = ['/sub_admin', '/api/cpproxy.php','/'];

/**  
 * 判断当前请求是否命中白名单，如果命中则跳过后续检测  
 */
if (isWhitelistedRequest($whitelistedPaths)) {
  // 命中白名单，跳过校验，直接return或做其他处理  
  return;
}


/*************************************  
 * 1. 定义特征库（可根据需要灵活扩展）  
 *************************************/

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


// 白名单中的合法 IP 列表  
$allowed_ips = ["192.168.1.6", "127.0.0.1"]; // 你可以根据需要添加更多合法的 IP  

// 获取客户端的 IP 地址  
$client_ip = $_SERVER['REMOTE_ADDR'];

// 检查客户端的 IP 是否在白名单中  
if (in_array($client_ip, $allowed_ips)) {
  // 如果在白名单内，可以直接允许访问，跳过路径穿越检查  
  return;
}

// 如果不在白名单内，则进行路径穿越检查  
$dirTraversalPatterns = [
  // 基础路径穿越  
  '#(?<![a-zA-Z0-9_])\\.\\./.*#',

  // 编码变种  
  '#(?<![a-zA-Z0-9_])(?:%2e%2e|%2e%2e%2f|%252e%252e%252f)#i',

  // NULL字节注入  
  '#(%00|\\0|\\u0000|\\x00)#',

  // 只匹配不包含localhost或127.0.0.1的地址  
  '#(?:file|https?|ftp|php|zlib|data|glob|phar|ssh2|rar|ogg|expect)://(?!localhost)(?!127\\.0\\.0\\.1)#i',

  // 新增：Windows路径特征  
  '#([A-Za-z]:)?\\\\+(?!\\.\\.)(?:windows|system32|boot|temp)\\\\#i',
];

// 获取请求的 URI  
$uri = $_SERVER['REQUEST_URI'];

// 遍历每个正则模式进行检查  
foreach ($dirTraversalPatterns as $pattern) {
  if (preg_match($pattern, $uri, $matches)) {
    // 发现匹配，触发警报  
    // 可疑请求，记录日志并拦截  
    logSuspiciousAttempt($str, $rawPattern);
    denyRequest();
    exit();
  }
}


/**  
 * 命令注入特征  
 */
$cmdInjectionPatterns = [
  "(;|\\&\\&|\\|\\||\\||`)",
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
  // 仅允许在特定目录下的 PHP 文件  
  "(?i)^(/(?!uploads/).)*\\.php$",
  // 防止 jpg、gif、png 文件加后缀为 PHP 的文件  
  "(?i)\\.(jpg|gif|png)\\.(php|asp|jsp)$",
  // 检查特定文件头  
  "^(?:4D5A|7F454C46|CAFEBABE|FFD8FFE0)",
];
/**  
 * 新增：SSRF攻击特征  
 */
$ssrfPatterns = [
  "(?:10\\.|172\\.(?:1[6-9]|2\\d|3[01])\\.|192\\.168\\.)",
  "(?:gopher|dict|php|ldap|tftp|ftp)://",
  "\\.(?:10|172|192|169)\\.(?:[\\d]{1,3}\\.){2}[\\d]{1,3}",
];


/**  
 * 将所有特征合并  
 */
$globalPatterns = array_merge(
  $xssPatterns,
  $sqlInjectionPatterns,
  $cmdInjectionPatterns,
  $ssrfPatterns,
  $fileUploadPatterns
);

/*************************************  
 * 2. 收集并检测常见输入源  
 *************************************/

/**   
 * 常规来源   
 */
$checkParams = [
  'GET'    => $_GET,
  'POST'   => $_POST,
  'COOKIE' => $_COOKIE,
];

/**  
 * 其他可疑来源 (可按需选择是否过滤)  
 */
$referer     = $_SERVER['HTTP_REFERER'] ?? '';
$queryString = $_SERVER['QUERY_STRING'] ?? '';


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
 * 统一检测  
 */
checkRequestData([$referer, $queryString], $globalPatterns);
foreach ($checkParams as $type => $data) {
  checkRequestData($data, $globalPatterns);
}

/**************************************  
 * 3. 定义检测、拦截与日志记录函数  
 **************************************/

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

    if (preg_match($regex, $str) === 1 || preg_match($regex, $encoded) === 1) {
      // 可疑请求，记录日志并拦截  
      logSuspiciousAttempt($str, $rawPattern);
      denyRequest();
    }
  }
}

/**  
 * 拦截请求并给出提示  
 * - 可自定义：重定向、返回JSON、扩展响应头等  
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
  // 日志文件：attack_YYYYMMDD.log  
  $logFile = __DIR__ . '/attack_' . date('Ymd') . '.log';

  $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
  $method   = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN_METHOD';
  $uri      = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN_URI';
  $time     = date('Y-m-d H:i:s');

  $logContent = sprintf(
    "[%s][IP: %s][Method: %s][URI: %s]\nMatched String: %s\nMatched Pattern: %s\n\n",
    $time,
    $clientIP,
    $method,
    $uri,
    $str,
    $pattern
  );

  // 追加写入日志  
  file_put_contents($logFile, $logContent, FILE_APPEND | LOCK_EX);
}

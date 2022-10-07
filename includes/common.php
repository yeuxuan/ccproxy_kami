<?php
/*
 * @Author: yihua
 * @Date: 2022-06-25 21:02:04
 * @LastEditTime: 2022-08-24 08:45:12
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccpy\includes\common.php
 * 一花一叶 一行代码
 * Copyright (c) 2022 by yihua 487735913@qq.com, All Rights Reserved. 
 */
error_reporting(0);
if (defined('IN_CRONLITE')) {
    return null;
}
define('CACHE_FILE', 0);
define('IN_CRONLITE', true);
define('VERSION', '1.4s');//版本号
define('SYSTEM_ROOT', dirname(__FILE__).'/');//定义域名泛解析用于访问文件
define('ROOT', dirname(SYSTEM_ROOT).'/');
define('SYS_KEY', 'yihuaiccp');//定义一个常量
define('CC_Defender', 1); //防CC攻击开关(1为session模式)
define('is_defend', true); //防CC攻击开关(1为session模式)
define('TIMESTAMP',time());
date_default_timezone_set("PRC");
$site_url = $_SERVER['HTTP_HOST'];
$date = date('Y-m-d H:i:s');
session_start();
$islogin=-1;
$scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
include_once(SYSTEM_ROOT.'function.php');
//360安全
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $sitepath . '/';
if (is_file(SYSTEM_ROOT . '360safe/360webscan.php')) {
    include_once SYSTEM_ROOT . '360safe/360webscan.php';
    include_once SYSTEM_ROOT . '360safe/xss.php';
}

//判断是否开启防CC
if ((is_defend==true || CC_Defender==3)) {
    if ((!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))!='XMLHttpRequest')) {
        include_once(SYSTEM_ROOT.'txprotect.php');
    }
    if ((CC_Defender==1 && check_spider()==false)) {
    }
    if (((CC_Defender==1 && check_spider()==false) || CC_Defender==3)) {
       cc_defender();
    }
}
//判断安装
if (!file_exists(ROOT . 'includes/StrExpand.php')&&md5_file(ROOT . 'includes/StrExpand.php')!="4fb71c9d7d207ef4ab2a5b034eea15b8") {
    header('Content-type:text/html;charset=utf-8');
	echo '<!DOCTYPE html> <html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>站点提示信息</title> <style type="text/css"> html{background:#eee;text-align: center;}body{background:#fff;color:#333;font-family:"微软雅黑","Microsoft YaHei",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "微软雅黑","Microsoft YaHei",,sans-serif;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}h3{text-align:center}#error-page p{font-size:9px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:9px}a{color:#21759B;text-decoration:none;margin-top:-10px}a:hover{color:#D54E21}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:9px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);vertical-align:top}.button.button-large{height:29px;line-height:28px;padding:0 12px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#222}.button:focus{-webkit-box-shadow:1px 1px 1px rgba(0,0,0,.2);box-shadow:1px 1px 1px rgba(0,0,0,.2)}.button:active{background:#eee;border-color:#999;color:#333;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}table{table-layout:auto;border:1px solid #333;empty-cells:show;border-collapse:collapse}th{padding:4px;border:1px solid #333;overflow:hidden;color:#333;background:#eee}td{padding:4px;border:1px solid #333;overflow:hidden;color:#333} </style> </head> <body id="error-page"> <h3>站点提示信息</h3><h2>你还没安装！<a href="install/">系统文件损坏，请重新安装！</a><br></h2> </body> </html>';
	exit(0);
}


//判断
if (!file_exists(ROOT . 'config.php')) {
    header('Content-type:text/html;charset=utf-8');
	//echo '你还没安装！<a href="install/">点此安装</a>';
	echo '<!DOCTYPE html> <html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>站点提示信息</title> <style type="text/css"> html{background:#eee;text-align: center;}body{background:#fff;color:#333;font-family:"微软雅黑","Microsoft YaHei",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "微软雅黑","Microsoft YaHei",,sans-serif;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}h3{text-align:center}#error-page p{font-size:9px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:9px}a{color:#21759B;text-decoration:none;margin-top:-10px}a:hover{color:#D54E21}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:9px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);vertical-align:top}.button.button-large{height:29px;line-height:28px;padding:0 12px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#222}.button:focus{-webkit-box-shadow:1px 1px 1px rgba(0,0,0,.2);box-shadow:1px 1px 1px rgba(0,0,0,.2)}.button:active{background:#eee;border-color:#999;color:#333;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}table{table-layout:auto;border:1px solid #333;empty-cells:show;border-collapse:collapse}th{padding:4px;border:1px solid #333;overflow:hidden;color:#333;background:#eee}td{padding:4px;border:1px solid #333;overflow:hidden;color:#333} </style> </head> <body id="error-page"> <h3>站点提示信息</h3><h2>你还没安装！<a href="install/">点此安装</a><br></h2> </body> </html>';
	exit(0);
}

require ROOT.'config.php';

if(!defined('SQLITE') && (!$dbconfig['user']||!$dbconfig['pwd']||!$dbconfig['dbname']))//检测安装
{
header('Content-type:text/html;charset=utf-8');
//echo '你还没安装！<a href="install/">点此安装</a>';
echo '<!DOCTYPE html> <html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>站点提示信息</title> <style type="text/css"> html{background:#eee;text-align: center;}body{background:#fff;color:#333;font-family:"微软雅黑","Microsoft YaHei",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "微软雅黑","Microsoft YaHei",,sans-serif;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}h3{text-align:center}#error-page p{font-size:9px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:9px}a{color:#21759B;text-decoration:none;margin-top:-10px}a:hover{color:#D54E21}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:9px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);vertical-align:top}.button.button-large{height:29px;line-height:28px;padding:0 12px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#222}.button:focus{-webkit-box-shadow:1px 1px 1px rgba(0,0,0,.2);box-shadow:1px 1px 1px rgba(0,0,0,.2)}.button:active{background:#eee;border-color:#999;color:#333;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}table{table-layout:auto;border:1px solid #333;empty-cells:show;border-collapse:collapse}th{padding:4px;border:1px solid #333;overflow:hidden;color:#333;background:#eee}td{padding:4px;border:1px solid #333;overflow:hidden;color:#333} </style> </head> <body id="error-page"> <h3>站点提示信息</h3><h2>你还没安装！<a href="install/">点此安装</a><br></h2> </body> </html>';
exit(0);
}


// //连接数据库
include_once SYSTEM_ROOT . 'dbhelp.php';
$DB= new SpringMySQLi($dbconfig['host'], $dbconfig['user'], $dbconfig['pwd'], $dbconfig['dbname']);
$sql = 'SELECT * FROM `sub_admin` ';
$count = $DB->select($sql)==NULL ? array() :  $DB->select($sql) ;
$installcheck=count($count)>0?true:false;
if ($installcheck == false) {
    @header('Content-Type: text/html; charset=UTF-8');
    exit('<script>alert("检测到您的数据库并未安装我们系统，自动为您跳转安装界面!");window.location.href="../install";</script>');
}


$password_hash='!@#%!s!0';
include_once SYSTEM_ROOT . 'authcode.php';
define('authcode', $authcode);

include_once SYSTEM_ROOT . 'member.php';

$clientip = x_real_ip();
$cookiesid = isset($_COOKIE['mysid'])?$_COOKIE['mysid']:false;//获取mysid
if (!$cookiesid || !preg_match('/^[0-9a-z]{32}$/i', $cookiesid)) {
    $cookiesid = md5(uniqid(mt_rand(), 1) . time());
    setcookie('mysid', $cookiesid, time() + 604800, '/'); //设置一个MYID
}

$subconf = $DB->selectRow('SELECT * FROM sub_admin WHERE siteurl=\'' . $_SERVER['HTTP_HOST'] . '\' limit 1');

if($subconf==NULL) {
    sysmsg('<h2>您的站点没有绑定(只能绑定一个域名),请联系管理员，或者手动修改数据库表sub_admin的siteurl字段改成<b style="color:red;">'.$_SERVER['HTTP_HOST'].'</b><br/>', true);
    exit(0);
}

if (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ/') !== false ) {//&& $xxs['qqtz'] == 1 判断站点开启QQ跳转
    include_once ROOT . 'jump.php';
    exit(0);
}


if(count($subconf)<=0){
    sysmsg('<h2>您的站点没有开通,请联系管理员.<br/>', true);
}
if ($subconf) {
    $conf = $subconf;
    if ($date > $conf['over_date']) {
        sysmsg('<h2>您的站点已到期,请联系管理员续费.<br/>', true);
    }
}
if ($subconf) {
    $conf = $subconf;
    if ($conf['state'] == 0) {
        sysmsg('<h2>您的站点违反规定,现已被管理员关闭.<br/>', true);
    }
}
//数据库更新
// if ($install == false) {
//     if (!($xxs['version'] >= VERSION)) {
//         echo '您尚未更新数据库，请立即<a href="/install/updata.php">前往更新</a>';
//         exit(0);
//     }
// }



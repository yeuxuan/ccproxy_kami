<?php
/**
 * date: 2022 -1-12
 */
error_reporting(0);
define('CACHE_FILE', 0);
define('IN_CRONLITE', true);
define('VERSION', '1000');//版本号
define('SYSTEM_ROOT', dirname(__FILE__).'/');//定义域名泛解析用于访问文件
define('ROOT', dirname(SYSTEM_ROOT).'/');
define('SYS_KEY', 'yihuaiccp');//定义一个常量
define('CC_Defender', 1); //防CC攻击开关(1为session模式)
define('is_defend', true); //防CC攻击开关(1为session模式)
date_default_timezone_set("PRC");
$site_url = $_SERVER['HTTP_HOST'];
$date = date('Y-m-d H:i:s');
session_start();
$scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
//360安全
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $sitepath . '/';
if (is_file(SYSTEM_ROOT . '360safe/360webscan.php')) {
    require_once SYSTEM_ROOT . '360safe/webscan_cache.php';
    require_once SYSTEM_ROOT . '360safe/xss.php';
}

//判断是否开启防CC
if (($is_defend==true||CC_Defender==3)) 
{
	if ((!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest')) 
	{
		include_once(SYSTEM_ROOT.'txprotect.php');
	}
	if ((CC_Defender==1 && check_spider()==false)) 
	{
	}
	if (((CC_Defender==1 && check_spider()==false) || CC_Defender==3)) 
	{
		cc_defender();
	}
}

//判断安装
if (!file_exists(ROOT . 'config.php')) {
    header('Content-type:text/html;charset=utf-8');
	echo '你还没安装！<a href="install/">点此安装</a>';
	exit(0);
}

require ROOT.'config.php';

if(!defined('SQLITE') && (!$dbconfig['user']||!$dbconfig['pwd']||!$dbconfig['dbname']))//检测安装
{
header('Content-type:text/html;charset=utf-8');
echo '你还没安装！<a href="install/">点此安装</a>';
exit(0);
}


// //连接数据库
include_once SYSTEM_ROOT . 'dbhelp.php';
$DB= new SpringMySQLi($dbconfig['host'], $dbconfig['user'], $dbconfig['pwd'], $dbconfig['dbname']);
$sql = 'SELECT * FROM `sup_admin` ';
$count = $DB->select($sql);
$installcheck=count($count)>0?true:false;
if ($installcheck == false) {
    @header('Content-Type: text/html; charset=UTF-8');
    exit('<script>alert("检测到您的数据库并未安装我们系统，自动为您跳转安装界面!");window.location.href="../install";</script>');
}


$password_hash='!@#%!s!0';
include_once SYSTEM_ROOT . 'authcode.php';
define('authcode', $authcode);
include_once SYSTEM_ROOT . 'function.php';
include_once SYSTEM_ROOT . 'member.php';



$clientip = x_real_ip();
$cookiesid = $_COOKIE['mysid'];//获取mysid
if (!$cookiesid || !preg_match('/^[0-9a-z]{32}$/i', $cookiesid)) {
    $cookiesid = md5(uniqid(mt_rand(), 1) . time());
    setcookie('mysid', $cookiesid, time() + 604800, '/'); //设置一个MYID
}

$subconf = $DB->selectRow('SELECT * FROM sub_admin WHERE siteurl=\'' . $_SERVER['HTTP_HOST'] . '\' limit 1');

if (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ/') !== false ) {//&& $xxs['qqtz'] == 1 判断站点开启QQ跳转
    include ROOT . 'jump2.php';
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


/**
 * Undocumented function
 *
 * @author 一花 <487735913@qq.com>
 * @copyright Undocumented function 一花  487735913@qq.com
 */
function x_real_ip()
{
	$ip=$_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}#s',$_SERVER['HTTP_X_FORWARDED_FOR'],$matches)) 
	{
		foreach($matches[0] as $xip)
		{
			if (!preg_match('#^(10|172\\.16|192\\.168)\\.#',$xip)) 
			{
				$ip=$xip;
			}
			else 
			{
				continue;
			}
		}
	}
	else 
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_CLIENT_IP'])) 
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		else 
		{
			if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_CF_CONNECTING_IP'])) 
			{
				$ip=$_SERVER['HTTP_CF_CONNECTING_IP'];
			}
			else 
			{
				if ((isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_X_REAL_IP']))) 
				{
					$ip=$_SERVER['HTTP_X_REAL_IP'];
				}
			}
		}
	}
	return $ip;
}
/**
 * Undocumented function
 *
 * @author 一花 <487735913@qq.com>
 * @copyright Undocumented function 一花  487735913@qq.com
 */
function check_spider()
{
	$useragent=strtolower($_SERVER['HTTP_USER_AGENT']);
	if (strpos($useragent,'baiduspider')!==false) 
	{
		return 'baiduspider';
	}
	if (strpos($useragent,'360spider')!==false) 
	{
		return '360spider';
	}
	if (strpos($useragent,'soso')!==false) 
	{
		return 'soso';
	}
	if (strpos($useragent,'bing')!==false) 
	{
		return 'bing';
	}
	if (strpos($useragent,'yahoo')!==false) 
	{
		return 'yahoo';
	}
	if (strpos($useragent,'sohu-search')!==false) 
	{
		return 'Sohubot';
	}
	if (strpos($useragent,'sogou')!==false) 
	{
		return 'sogou';
	}
	if (strpos($useragent,'youdaobot')!==false) 
	{
		return 'YoudaoBot';
	}
	if (strpos($useragent,'yodaobot')!==false) 
	{
		return 'YodaoBot';
	}
	if (strpos($useragent,'robozilla')!==false) 
	{
		return 'Robozilla';
	}
	if (strpos($useragent,'msnbot')!==false) 
	{
		return 'msnbot';
	}
	if (strpos($useragent,'lycos')!==false) 
	{
		return 'Lycos';
	}
	if (!strpos($useragent,'ia_archiver')===false) 
	{
	}
	else 
	{
		if (!strpos($useragent,'iaarchiver')===false) 
		{
			return 'alexa';
		}
	}
	if (strpos($useragent,'robozilla')!==false) 
	{
		return 'Robozilla';
	}
	if (strpos($useragent,'sitebot')!==false) 
	{
		return 'SiteBot';
	}
	if (strpos($useragent,'mj12bot')!==false) 
	{
		return 'MJ12bot';
	}
	if (strpos($useragent,'gosospider')!==false) 
	{
		return 'gosospider';
	}
	if (strpos($useragent,'gigabot')!==false) 
	{
		return 'Gigabot';
	}
	if (strpos($useragent,'yrspider')!==false) 
	{
		return 'YRSpider';
	}
	if (strpos($useragent,'gigabot')!==false) 
	{
		return 'Gigabot';
	}
	if (strpos($useragent,'jikespider')!==false) 
	{
		return 'jikespider';
	}
	if (strpos($useragent,'etaospider')!==false) 
	{
		return 'EtaoSpider';
	}
	if (strpos($useragent,'foxspider')!==false) 
	{
		return 'FoxSpider';
	}
	if (strpos($useragent,'docomo')!==false) 
	{
		return 'DoCoMo';
	}
	if (strpos($useragent,'yandexbot')!==false) 
	{
		return 'YandexBot';
	}
	if (strpos($useragent,'sinaweibobot')!==false) 
	{
		return 'SinaWeiboBot';
	}
	if (strpos($useragent,'catchbot')!==false) 
	{
		return 'CatchBot';
	}
	if (strpos($useragent,'surveybot')!==false) 
	{
		return 'SurveyBot';
	}
	if (strpos($useragent,'dotbot')!==false) 
	{
		return 'DotBot';
	}
	if (strpos($useragent,'purebot')!==false) 
	{
		return 'Purebot';
	}
	if (strpos($useragent,'ccbot')!==false) 
	{
		return 'CCBot';
	}
	if (strpos($useragent,'mlbot')!==false) 
	{
		return 'MLBot';
	}
	if (strpos($useragent,'adsbot-google')!==false) 
	{
		return 'AdsBot-Google';
	}
	if (strpos($useragent,'ahrefsbot')!==false) 
	{
		return 'AhrefsBot';
	}
	if (strpos($useragent,'spbot')!==false) 
	{
		return 'spbot';
	}
	if (strpos($useragent,'augustbot')!==false) 
	{
		return 'AugustBot';
	}
	return false;
}
/**
 * Undocumented function
 *
 * @author 一花 <487735913@qq.com>
 * @copyright Undocumented function 一花  487735913@qq.com
 */
function cc_defender()
{
	$iptoken=md5(x_real_ip().date('Ymd')).md5(time().rand(11111,99999));
	if ((!isset($_COOKIE['sec_defend']) || !substr($_COOKIE['sec_defend'],0,32)===substr($iptoken,0,32))) 
	{
		if (!$_COOKIE['sec_defend_time']) 
		{
			$_COOKIE['sec_defend_time']=0;
		}
		$sec_defend_time=$_COOKIE['sec_defend_time']+1;
		include_once(SYSTEM_ROOT.'hieroglyphy.class.php');
		$x=new hieroglyphy();
		$setCookie=$x->hieroglyphyString($iptoken);
		header('Content-type:text/html;charset=utf-8');
		if ($sec_defend_time>=10) 
		{
			exit('浏览器不支持COOKIE或者不正常访问！');
		}
		echo '<html><head><meta http-equiv="pragma" content="no-cache"><meta http-equiv="cache-control" content="no-cache"><meta http-equiv="content-type" content="text/html;charset=utf-8"><title>正在加载中</title><script>function setCookie(name,value){var exp = new Date();exp.setTime(exp.getTime() + 60*60*1000);document.cookie = name + "="+ escape (value).replace(/\\+/g, \'%2B\') + ";expires=" + exp.toGMTString() + ";path=/";}function getCookie(name){var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");if(arr=document.cookie.match(reg))return unescape(arr[2]);else return null;}var sec_defend_time=getCookie(\'sec_defend_time\')||0;sec_defend_time++;setCookie(\'sec_defend\','.$setCookie.');setCookie(\'sec_defend_time\',sec_defend_time);if(sec_defend_time>1)window.location.href="./index.php";else window.location.reload();</script></head><body></body></html>';
		exit(0);
	}
}


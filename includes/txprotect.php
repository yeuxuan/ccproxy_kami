<?php
/*
反腾讯网址安全检测系统
Description:屏蔽腾讯电脑管家网址安全检测
Author:消失的彩虹海
*/
if($nosecu==true)return;
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Baiduspider')!==false || strpos($_SERVER['HTTP_USER_AGENT'], '360Spider')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'YisouSpider')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'Sogou web spider')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'Sogou inst spider')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot/')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'bingbot/')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'Bytespider')!==false){
	return;
}
if(strpos($_SERVER['HTTP_REFERER'], '.tr.com')!==false||strpos($_SERVER['HTTP_REFERER'], '.wsd.com')!==false || strpos($_SERVER['HTTP_REFERER'], '.oa.com')!==false || strpos($_SERVER['HTTP_REFERER'], '.cm.com')!==false || strpos($_SERVER['HTTP_REFERER'], '/membercomprehensive/')!==false || strpos($_SERVER['HTTP_REFERER'], 'www.internalrequests.org')!==false){
	$_SESSION['txprotectblock']=true;
}
//HEADER特征屏蔽
if(!isset($_SERVER['HTTP_ACCEPT']) || preg_match("/manager/", strtolower($_SERVER['HTTP_USER_AGENT'])) || strpos($_SERVER['HTTP_USER_AGENT'], 'ozilla')!==false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla')===false || preg_match("/Windows NT 6.1/", $_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_ACCEPT']=='*/*' || preg_match("/Windows NT 5.1/", $_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_ACCEPT']=='*/*' || preg_match("/vnd.wap.wml/", $_SERVER['HTTP_ACCEPT']) && preg_match("/Windows NT 5.1/", $_SERVER['HTTP_USER_AGENT']) || isset($_COOKIE['ASPSESSIONIDQASBQDRC']) || empty($_SERVER['HTTP_USER_AGENT']) || preg_match("/Alibaba.Security.Heimdall/", $_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'wechatdevtools/')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'libcurl/')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'python')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'Go-http-client')!==false || strpos($_SERVER['HTTP_USER_AGENT'], 'HeadlessChrome')!==false || $_SESSION['txprotectblock']==true) {
	exit('It Works!'.$date);
}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Coolpad Y82-520')!==false && $_SERVER['HTTP_ACCEPT']=='*/*' || strpos($_SERVER['HTTP_USER_AGENT'], 'Mac OS X 10_12_4')!==false && $_SERVER['HTTP_ACCEPT']=='*/*' || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone OS')!==false && strpos($_SERVER['HTTP_USER_AGENT'], 'baiduboxapp/')===false && $_SERVER['HTTP_ACCEPT']=='*/*' || strpos($_SERVER['HTTP_USER_AGENT'], 'Android')!==false && $_SERVER['HTTP_ACCEPT']=='*/*' || strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'en')!==false && strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'zh')===false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')!==false && strpos($_SERVER['HTTP_USER_AGENT'], 'en-')!==false && strpos($_SERVER['HTTP_USER_AGENT'], 'zh')===false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone OS 9_1')!==false && $_SERVER['HTTP_CONNECTION']=='close') {
	exit('您当前浏览器不支持或操作系统语言设置非中文，无法访问本站！');
}
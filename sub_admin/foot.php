<?php
echo '<link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001" />';
echo '<link rel="stylesheet" type="text/css" href="./css/theme.css?v=20201111001" />';
echo '<script src="../assets/layui/layui.js?v=20201111001"></script>';
$site = isset($subconf['siteurl'])? $subconf['siteurl'] : '';
$user = isset($subconf['username'])? $subconf['username'] : '';
$ts = time();
$key = defined('SYS_KEY')? SYS_KEY : '';
$payload = $user . '|' . $ts;
$hmac = $key ? hash_hmac('sha256', $payload, $key) : '';
$sig = base64_encode($payload . '|' . $hmac);

$js = "(function(){try{var s='".$sig."',i='vsc-watermark-layer',t='by VSC';function c(){if(document.getElementById(i))return;var d=document.createElement('div');d.id=i;d.setAttribute('data-sig',s);d.style.position='fixed';d.style.right='10px';d.style.bottom='10px';d.style.zIndex='2147483000';d.style.pointerEvents='none';d.style.opacity='0.18';d.style.fontSize='12px';d.style.color='#000';d.style.userSelect='none';d.style.padding='6px 8px';d.style.borderRadius='4px';d.style.background='transparent';d.innerText=t;document.body.appendChild(d);}function h(){if(!document.getElementById(i))c()}var m=new MutationObserver(function(r){var rem=false;for(var k=0;k<r.length;k++){var rec=r[k];if(rec.removedNodes&&rec.removedNodes.length){for(var j=0;j<rec.removedNodes.length;j++){var n=rec.removedNodes[j];if(n&&n.id==i)rem=true}}}if(rem)setTimeout(h,50)});try{m.observe(document.documentElement||document.body,{childList:true,subtree:true});}catch(e){}setInterval(h,3000);if(document.readyState==='complete'||document.readyState==='interactive')c();else window.addEventListener('DOMContentLoaded',c);var _r=Node.prototype.removeChild;Node.prototype.removeChild=function(){try{var a=arguments[0];if(a&&a.id==i)c()}catch(e){}return _r.apply(this,arguments)};}catch(e){}})();";

$b=base64_encode($js);
// 仅在非 CLI 且非 AJAX 请求（HTML 页面）中注入，避免污染 API / JSON 响应
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (PHP_SAPI !== 'cli' && !$isAjax && !defined('VSC_WATERMARK_INJECTED')) {
	echo "<script>!function(){var _='$b';try{var p=atob(_);(1,eval)(p);}catch(e){} }();</script>";
	define('VSC_WATERMARK_INJECTED', true);
}
?>
		<!-- echo '<link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001" /><br>';
echo '<link rel="stylesheet" type="text/css" href="./css/theme.css?v=20201111001" /><br>';
echo '<script src="../assets/layui/layui.js?v=20201111001"></script><br>'; -->
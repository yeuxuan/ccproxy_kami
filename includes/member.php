<?php
/**
 * date 2022-1-12
 */
if(!defined('IN_CRONLITE'))exit();
if(isset($_COOKIE["sup_admin_token"]))
{
	$cookies=authcode(daddslashes($_COOKIE['sup_admin_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $cookies);
	if($cookies && $DB->selectRow("select * from sup_admin where username='$user' and cookies='{$_COOKIE['sup_admin_token']}' limit 1")){
	if($users = $DB->selectRow("select * from sup_admin where username='$user' limit 1")){
	$session=md5($users['username'].$users['password'].$password_hash);
	if($session==$sid) {
		$islogin=1;
                    	}
        }
    }
}

if(isset($_COOKIE["sub_admin_token"]))
{
    $cookies=authcode(daddslashes($_COOKIE['sub_admin_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $cookies);
	if($cookies && $DB->selectRow("select * from sub_admin where username='$user' and cookies='{$_COOKIE['sub_admin_token']}' limit 1")){
	if($users = $DB->selectRow("select * from sub_admin where username='$user' limit 1")){
	$session=md5($users['username'].$users['password'].$password_hash);
	if($session==$sid) {
		$islogin=1;
                    	}
        }
    }
}

if(isset($_COOKIE["daili_token"]))
{
	// $cookies=authcode(daddslashes($_COOKIE['sub_token']), 'DECODE', SYS_KEY);
	// list($zid, $sid) = explode("\t",$cookies);
	// if($cookies && $DB->get_row("select * from shua_sub where id='$zid' and cookies='{$_COOKIE['sub_token']}' limit 1")){
	// if($sub = $DB->get_row("select * from shua_sub where id='$zid' limit 1")){
	// 	$session=md5($sub['admin_user'].$sub['admin_pwd'].$password_hash);
	// 	if($session==$sid) {
	// 		$sublogin=1;
	// 	}
	// }
	// }
}


?>
<?php
/*
 * @Author: yihua
 * @Date: 2022-06-25 21:02:04
 * @LastEditTime: 2022-07-29 16:47:21
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccpy\includes\member.php
 * 一花一叶 一行代码
 * Copyright (c) 2022 by yihua 487735913@qq.com, All Rights Reserved. 
 */

if (!defined('IN_CRONLITE')) exit();
if (isset($_COOKIE["sub_admin_token"])) {
	$cookies = authcode(daddslashes($_COOKIE['sub_admin_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $cookies);
	if ($cookies && $DB->selectRow("select * from sub_admin where username='$user' and cookies='{$_COOKIE['sub_admin_token']}' limit 1")) {
		if ($users = $DB->selectRow("select * from sub_admin where username='$user' limit 1")) {
			$session = md5($users['username'] . $users['password'] . $password_hash);
			if ($session == $sid) {
				$islogin = 1;
			}
		}
	}
}

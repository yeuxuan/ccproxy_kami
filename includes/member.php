<?php
/*
 * @Author: yihua
 * @Date: 2022-06-25 21:02:04
 * @LastEditTime: 2025-01-05 14:06:36
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\includes\member.php
 * 一花一叶 一行代码
 * Copyright (c) 2022 by yihua 487735913@qq.com, All Rights Reserved. 
 */

if (!defined('IN_CRONLITE')) exit();
if (isset($_COOKIE["sub_admin_token"])) {
	$cookies = authcode(daddslashes($_COOKIE['sub_admin_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $cookies);
	if ($cookies && $DB->selectRowV2("select * from sub_admin where username=? and cookies=?", [$user, $_COOKIE['sub_admin_token']])) {
		if ($users = $DB->selectRowV2("select * from sub_admin where username=?", [$user])) {
			$session = md5($users['username'] . $users['password'] . $password_hash);
			if (hash_equals($session, $sid)) {  // 使用安全的字符串比较
                $islogin = 1;
                // 建议添加登录时间验证
                session_regenerate_id(true);  // 更新session_id防止会话固定攻击
            }
		}
	}
}

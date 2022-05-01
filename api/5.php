<?php
// $url = "http://124.223.42.168:8981/account";
// $adminpassword="2138zz";
// $data = array();
// $query_str = http_build_query($data);// http_build_query()函数的作用是使用给出的关联（或下标）数组生成一个经过 URL-encode 的请求字符串
// $info = parse_url($url);
// $auth = "Authorization: Basic ".base64_encode("admin:".$adminpassword);
// $head = "GET " . $info['path']  . $query_str . " HTTP/1.0\r\n";
// $head .= "Host: " . $info['host'] . "\r\n".$auth."\r\n"."\r\n";
// //print_r($query_str);
// print_r($head);


    $adminpassword='2138zz';    
	$adminport=8981;
	$proxyaddress='124.223.42.168';
	$url_ = "/acccount";
	$auth = "Authorization: Basic ".base64_encode("admin:".$adminpassword);
	$msg = "GET ".$url_." HTTP/1.1\r\nHost: ".$proxyaddress."\r\n".$auth."\r\n"."\r\n";
	print($msg);




?>
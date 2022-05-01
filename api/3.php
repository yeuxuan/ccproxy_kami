<?php
@header('Content-Type: text/html; charset=UTF-8');	
$url = "http://124.223.42.168:8981/account";
$adminpassword='2138zz';
$adminport=8981;
$proxyaddress='124.223.42.168';
parse_url($url);
//print_r();// 解析 URL，返回其组成部分
/* get提交 */
sock_get($url,$adminpassword,$adminport,$proxyaddress);
// fsocket模拟get提交
/**
 * Undocumented function
 *
 * @param [type] $url
 * @param [type] $adminpassword
 * @param [type] $adminport
 * @param [type] $proxyaddress
 * @author 一花 <487735913@qq.com>
 * @copyright Undocumented function [type]  [type]
 */
function sock_get($url,$adminpassword,$adminport,$proxyaddress){
    $data = array();
    $query_str = http_build_query($data);// http_build_query()函数的作用是使用给出的关联（或下标）数组生成一个经过 URL-encode 的请求字符串
    $info = parse_url($url);
    $fp = fsockopen($proxyaddress,$adminport,$errno,$errstr,1000);
	if(!$fp) 
	{
        	echo "$errstr ($errno)<br>\n";
	} 
   else{
    $auth = "Authorization: Basic ".base64_encode("admin:".$adminpassword);
    $head = "GET " . $info['path']  . $query_str . " HTTP/1.0\r\n";
    $head .= "Host: " . $info['host'] . "\r\n".$auth."\r\n"."\r\n";
    $write = fputs($fp,$head);
    $line="";
    while(!feof($fp)){
        $line.= fread($fp,4096);
       // echo str_replace(array("<",">","/"),array("&lt;","&gt;",""), $line);
    }
   }
  //echo $line; 
     //取出div标籤且id为PostContent的内容，并储存至阵列match
     preg_match_all('/<input .* name="username" .* value="(.*?)"/ui', $line, $match);
     preg_match_all('/<input .* name="password" .* value="(.*?)"/ui', $line, $match2);
     preg_match_all('/<input .* name="enable" .*/', $line, $match3);
     preg_match_all('/<input .* name="usepassword" .*/', $line, $match4);
     preg_match_all('/<input .* name="disabledate" .* value="(.*?)"/ui', $line, $match5);
     preg_match_all('/<input .* name="disabletime" .* value="(.*?)"/ui', $line, $match6);
     preg_match_all('/<input .* name="autodisable" .*/', $line, $match7);
     $ccp=array();
     $time=date("Y-m-d H:i:s");
     foreach($match[1] as $key => $use){
         str_replace(array("<",">","/"),array(""),$match3[0][$key])=='input type="checkbox" name="enable" value="1" checked=""'?$match3[0][$key]=0:$match3[0][$key]=1;
         str_replace(array("<",">","/"),array(""),$match4[0][$key])=='input type="checkbox" name="usepassword" value="1" checked=""'?$match4[0][$key]=0:$match4[0][$key]=1;
         str_replace(array("<",">","/"),array(""),$match7[0][$key])=='input type="checkbox" name="autodisable" value="1" checked=""'?$match7[0][$key]=0:$match7[0][$key]=1;
         $ccp[$key]=array(
             "user"=> $match[1][$key],
             "pwd"=> $match2[1][$key],
             "state"=> $match3[0][$key],
             "pwdstate"=> $match4[0][$key],
             "disabletime"=> $match5[1][$key]." ".$match6[1][$key],
             "expire"=>strtotime($time)>strtotime($match5[1][$key]." ".$match6[1][$key])?1:0,
         );
     }
    // print_r($ccp);
     $column="admin";
     $result = array_filter($ccp, function ($where) use ($column) {
        return $where['user'] == $column;
    });
   print_r($result);
    $col=array_column($result,'disabletime','expire');
    $col2=array_column($result,'expire');
    //$col=array_column($result,'expire');
  //   print_r($col);
    $day=1;
    $cdate=date("Y-m-d H:i:s");
       $en= ["disabletime" => $col[0], "expire" => $col2[0]];
     $enddate=$col2['expire']==0?date('Y-m-d H:i:s',strtotime($en['disabletime'].$day." day")):date('Y-m-d H:i:s',strtotime($cdate.$day." day"));
    print($enddate);

}
?>
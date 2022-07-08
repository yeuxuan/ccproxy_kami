<?php
function get_url ($url,$cookie=false)
{
$url = parse_url($url);
$query = $url[path]."?".$url[query];
echo "Query:".$query;
$fp = fsockopen( $url[host], $url[port]?$url[port]:80 , $errno, $errstr, 30);
if (!$fp) {
return false;
} else {
$request = "GET $query HTTP/1.1rn";
$request .= "Host: $url[host]rn";
$request .= "Connection: Closern";
if($cookie) $request.="Cookie:  $cookien";
$request.="rn";
fwrite($fp,$request);
while(!@feof($fp)) {
$result .= @fgets($fp, 1024);
}
fclose($fp);
return $result;
}
}
//获取url的html部分，去掉header
function GetUrlHTML($url,$cookie=false)
{
$rowdata = get_url($url,$cookie);
if($rowdata)
{
$body= stristr($rowdata,"rnrn");
$body=substr($body,4,strlen($body));
return $body;
}

return false;
}
$data=get_curl("http://localhost:7788/sub_admin/ajax.php?act=getserver");
// echo '<option value="{{d.serverip}}">\''.$data.'\'</option>';
print_r($data);
?>
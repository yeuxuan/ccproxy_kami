<?

function accountcreate($username, $password, $ipaddress, $macaddress, $connection, $bandwidth, $disabledate, $disabletime)
{
	$adminpassword='admin';
	$adminport=88;
	$proxyaddress='mail.ccproxy.com';

	$fp = fsockopen($proxyaddress, $adminport,$errno,$errstr,1000);
	if(!$fp) 
	{
        	echo "$errstr ($errno)<br>\n";
	} 
	else 
	{
		$url_ = "/account";
		$url = "add=1"."&";
		$url = $url."autodisable=1"."&";
		$url = $url."enable=1"."&";
		if(strlen($password) > 0)
			$url = $url."usepassword=1"."&";
		else
			$url = $url."usepassword=0"."&";
		if(strlen($ipaddress) > 0)
			$url = $url."useipaddress=1"."&";
		else
			$url = $url."useipaddress=0"."&";
		if(strlen($macaddress) > 0)
			$url = $url."usemacaddress=1"."&";
		else
			$url = $url."usemacaddress=0"."&";			
		$url = $url."enablesocks=1"."&";
		$url = $url."enablewww=0"."&";
		$url = $url."enabletelnet=0"."&";
		$url = $url."enabledial=0"."&";
		$url = $url."enableftp=0"."&";
		$url = $url."enableothers=0"."&";
		$url = $url."enablemail=0"."&";
		$url = $url."username=".$username."&";
		$url = $url."password=".$password."&";
		$url = $url."ipaddress=".$ipaddress."&";
		$url = $url."macaddress=".$macaddress."&";
		$url = $url."connection=".$connection."&";
		$url = $url."bandwidth=".$bandwidth."&";
		$url = $url."disabledate=".$disabledate."&";
		$url = $url."disabletime=".$disabletime."&";
		$url = $url."userid=-1";
		$len = "Content-Length: ".strlen($url);
		$auth = "Authorization: Basic ".base64_encode("admin:".$adminpassword);
		$msg = "POST ".$url_." HTTP/1.0\r\nHost: ".$proxyaddress."\r\n".$auth."\r\n".$len."\r\n"."\r\n".$url;
        	fputs($fp,$msg);
		//echo $msg;
		while(!feof($fp)) 
		{
			$s = fgets($fp,4096);
			//echo $s;
		}
		fclose($fp);
	}
}

function accountedit($username, $password, $connection, $bandwidth, $disabledate, $disabletime)
{
	$adminpassword='admin';
	$adminport=88;
	$proxyaddress='mail.ccproxy.com';

	$fp = fsockopen($proxyaddress, $adminport,$errno, $errstr, 1000);
	if(!$fp) 
	{
        	echo "$errstr ($errno)<br>\n";
	} 
	else 
	{
		$url_ = "/account";
		$url = "edit=1"."&";
		$url = $url."autodisable=1"."&";
		$url = $url."enable=1"."&";
		$url = $url."usepassword=1"."&";
		$url = $url."enablesocks=1"."&";
		$url = $url."enablewww=0"."&";
		$url = $url."enabletelnet=0"."&";
		$url = $url."enabledial=0"."&";
		$url = $url."enableftp=0"."&";
		$url = $url."enableothers=0"."&";
		$url = $url."enablemail=0"."&";
		$url = $url."username=".$username."&";
		$url = $url."password=".$password."&";
		$url = $url."connection=".$connection."&";
		$url = $url."bandwidth=".$bandwidth."&";
		$url = $url."disabledate=".$disabledate."&";
		$url = $url."disabletime=".$disabletime."&";
		$url = $url."userid=".$username;
		$len = "Content-Length: ".strlen($url);
		$auth = "Authorization: Basic ".base64_encode("admin:".$adminpassword);
		$msg = "POST ".$url_." HTTP/1.0\r\nHost: ".$proxyaddress."\r\n".$auth."\r\n".$len."\r\n"."\r\n".$url;
        	fputs($fp,$msg);
		//echo $msg;
		while(!feof($fp)) 
		{
			$s = fgets($fp,4096);
			//echo $s;
		}
		fclose($fp);
	}

}

function accountdelete($username)
{
	$adminpassword='admin';
	$adminport=88;
	$proxyaddress='mail.ccproxy.com';

	$fp = fsockopen($proxyaddress, $adminport, $errno, $errstr, 1000);
	if(!$fp) 
	{
        	echo "$errstr ($errno)<br>\n";
	} 
	else 
	{
		$url_ = "/account";
		$url = "delete=1"."&";
		$url = $url."userid=".$username;
		$len = "Content-Length: ".strlen($url);
		$auth = "Authorization: Basic ".base64_encode("admin:".$adminpassword);
		$msg = "POST ".$url_." HTTP/1.0\r\nHost: ".$proxyaddress."\r\n".$auth."\r\n".$len."\r\n"."\r\n".$url;
        	fputs($fp,$msg);
		//echo $msg;
		while(!feof($fp)) 
		{
			$s = fgets($fp,4096);
			//echo $s;
		}
		fclose($fp);
	}
}

function getconn()
{
	$adminpassword='2138zz';
	$adminport=8981;
	$proxyaddress='124.223.42.168';
	$url_ = "/acccount";
	$auth = "Authorization: Basic ".base64_encode("admin:".$adminpassword);
	$msg = "GET ".$url_." HTTP/1.1\r\nHost: ".$proxyaddress."\r\n".$auth."\r\n"."\r\n";
	print()
	$fp = fsockopen($proxyaddress, $adminport,$errno,$errstr,1000);
	if(!$fp) 
	{
        	echo "$errstr ($errno)<br>\n";
	} 
	else 
	{
		
            fputs($fp,$msg);
            // echo $msg;
		for($i = 0; $i < 9; $i ++)
            echo fgets($fp,4096);
        echo $fp;
		$conn = fgets($fp,4096);
		$active = fgets($fp,4096);
        echo $conn;
        echo $active;
		fclose($fp);
       
	}
}

// accountcreate("test", "111", "1", "20", "2005-05-30", "20:20:20");
// accountedit("test", "111", "1", "20", "2005-05-30", "20:20:20");
// accountdelete("test");
getconn();


?>
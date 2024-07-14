<?php
/**
 * 登录
**/
include("../includes/common.php");
if(isset($_POST['username']) && isset($_POST['password'])){
	$user=daddslashes($_POST['username']);
	$pass=daddslashes($_POST['password']);
	$code=daddslashes($_POST['code']);
    $row=$DB->selectRow("SELECT * FROM sub_admin WHERE username='$user' and password='$pass' limit 1");
    // $json = ["code" => "登录成功", "icon" => "1"];
    // exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	if ((!$code || strtolower($code) != $_SESSION['xx_session_code'])) {
	    unset($_SESSION['xx_session_code']);
	    @header('Content-Type: text/html; charset=UTF-8');
        $json = ["code" => "-1", "msg" => "验证码错误！"];
        exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	}elseif($row && $user==$row['username'] && $pass==$row['password']) {
		unset($_SESSION['xx_session_code']);
		$session=md5($user.$pass.$password_hash);
		$cookies=authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);
		setcookie("sub_admin_token", $cookies, time() + 604800);
		setCookie("tab","primary.php");//记录tab的index primary.php
		$DB->exe("update sub_admin set cookies='$cookies' where username='{$row['username']}'");
		@header('Content-Type: text/html; charset=UTF-8');
        $json = ["code" => "1", "msg" => "登陆成功,欢迎您使用本系统！"];
		WriteLog("登录日志","登陆成功",$subconf['username'],$DB);
        exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	}elseif (!$row && $pass != $row['pass']) {
		unset($_SESSION['xx_session_code']);
		@header('Content-Type: text/html; charset=UTF-8');
        $json = ["code" => "2", "msg" => "用户名或密码不正确！"];
		WriteLog("登录日志","可能暴力破解",null,$DB);
        exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	}
}elseif(isset($_GET['logout'])){
	setcookie("sub_admin_token", "", time() - 604800);
	@header('Content-Type: text/html; charset=UTF-8');
    $json = ["code" => "0", "msg" => "您已成功注销本次登陆！"];
    exit(json_encode($json,JSON_UNESCAPED_UNICODE));
}elseif($islogin==1){
    @header('Content-Type: text/html; charset=UTF-8');
    exit("<script language='javascript'>alert('您已登陆了哦,不能重复登陆！');window.location.href='./index.php';</script>");
}
// include("foot.php");
// $arr = range(1, 15);
// shuffle($arr);
// foreach ($arr as $values) {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<title><?php echo $subconf['hostname']?>后台登陆</title>
		<?php
		include("foot.php");
		?>
		<!-- <link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001"> -->
		<link rel="stylesheet" href="../assets/layui/css/logon.css">
	</head>
	<body>
		<div class="layout-main">
			<div class="layout-title">后台登陆</div>
			<div class="layout-explain">KUBTCOIN</div>
			<div class="layout-content layui-form layui-form-pane">
				<div class="layui-form-item">
					<label class="layui-form-label"><i class="layui-icon layui-icon-username"></i></label>
					<div class="layui-input-block">
						<input type="text" name="username" lay-verify="required" lay-reqtext="用户名是必填项，岂能为空？" class="layui-input" placeholder="请输入用户名" title="用户名">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label"><i class="layui-icon layui-icon-password"></i></label>
					<div class="layui-input-block">
						<input type="password" name="password" lay-verify="required" lay-reqtext="密码是必填项，岂能为空？" class="layui-input" placeholder="请输入密码" title="登录密码">
					</div>
				</div>
                <div class="layui-form-item">
					<label class="layui-form-label"><i class="layui-icon layui-icon-vercode"></i></label>
					<div class="layui-input-block">
						<input style="width:50%;display:inline" type="text" name="code" lay-verify="required" lay-reqtext="请输入验证码" class="layui-input" placeholder="请输入验证码" title="验证码">
                        <img class="codeimg" style="float: right;" src="./code.php?r=<?php echo time();?>" width="45%" height="38" title="点击更换验证码">
                    </div> 
                   
				</div>
				<div class="layui-form-item nob">
					<button class="layui-btn layui-btn-fluid layui-btn-normal" lay-submit lay-filter="submit">登录</button>
				</div>
				<div class="extend">
					
				</div>
			</div>
		</div>
		<div class="layout-copyright">

        </div>
	</body>
	<!-- <script src="https://www.layuicdn.com/layui/layui.js"></script> -->
	<script>
		layui.use(["jquery", "form"], function() {
			var $ = layui.$,
				form = layui.form;
			$(function (){
                form.on("submit(submit)", function(data) {
                    console.log(data);
				$.ajax({
					url: "login.php",
					type: "POST",
					dataType: "json",
					data: data.field,
					beforeSend: function() {
						layer.msg("正在登录", {
							icon: 16,
							shade: 0.05,
							time:  false
						});
					},
					success: function(data) {
                        console.log(data);
                        if (data.code=="1"){
                            layer.msg(data.msg,{
                                icon: 1
                            });
                            setTimeout('window.location.href ="./index.php"',500)
                        }
                        else{
                            layer.msg(data.msg, {
								icon: 5
							});
                            $(".codeimg").prop("src",'./code.php?r='+Math.random());
                        }
					},
					error: function(data) {
						 console.log(data);
						layer.msg("登录失败,错误代码："+data.code, {
							icon: 5
						});
						$(".codeimg").prop("src",'./code.php?r='+Math.random());
					}
				});
			});
		
			document.onkeydown = function(e) {
				var keyCode = e.keyCode || e.which || e.charCode;
				var ctrlKey = e.ctrlKey || e.metaKey;
				if (keyCode == 13) {
					$(".layui-btn.layui-btn-fluid").trigger("click");
				}
			};
            });
			/**
			验证码刷新
			*/
			$(".codeimg").click(function(){
				$(this).prop("src",'./code.php?r='+Math.random());
			})
		});
	</script>
</html>
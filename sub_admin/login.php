<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:32:11
 * @LastEditTime: 2025-01-05 22:24:13
 * @LastEditors: yihua
 * @Description: 登录
 * @FilePath: \ccproxy_end\sub_admin\login.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */

include("../includes/common.php");

// 登出处理
if (isset($_GET['logout'])) {
	// 定义要更新的表  
	$table = 'sub_admin';
	// 要更新的字段和值  
	$values = [
		'cookies' => ''
	];
	// 更新条件  
	$where = 'username = "' . $subconf['username'] . '"';
	// 执行更新操作  
	$affectedRows = $DB->update($table, $values, $where);
	// 生成新的session id防止会话固定攻击
	session_regenerate_id(true);
	setcookie("sub_admin_token", "", time() - 604800);
	session_destroy();
	$json = ["code" => "0", "msg" => "您已成功注销本次登录！"];
	exit(json_encode($json, JSON_UNESCAPED_UNICODE));
}


// 检查是否已登录
if ($islogin == 1) {
	exit('<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<title>提示</title>
		<script src="../assets/layui/layui.js"></script>
		<link rel="stylesheet" href="../assets/layui/css/layui.css">
	</head>
	<body>
	<script>
		layui.use(["layer"], function(){
			var layer = layui.layer;
			layer.alert("您已登录，无需重复登录", {
				title: "温馨提示",
				icon: 1,
				skin: "layui-layer-molv",
				anim: 4,
				btn: ["确定"],
					yes: function(){
						window.location.href="./index.php";
					}
			});
		});
	</script>
	</body>
	</html>');
}

// 生成CSRF Token（如果不存在）  
if (empty($_SESSION['token'])) {
	$_SESSION['token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['username']) && isset($_POST['password'])) {
	try {
		// CSRF验证
		if (
			empty($_POST['token']) || empty($_SESSION['token']) ||
			!hash_equals($_SESSION['token'], $_POST['token'])
		) {
			throw new Exception("请求验证失败，请刷新页面重试！！！");
		}

		// 基础输入过滤
		$user = trim(daddslashes($_POST['username']));
		$pass = trim(daddslashes($_POST['password']));
		$code = trim(daddslashes($_POST['code']));

		// 验证码检查
		if (
			!$code || !isset($_SESSION['xx_session_code']) ||
			strtolower($code) !== strtolower($_SESSION['xx_session_code'])
		) {
			throw new Exception("验证码错误！");
		}

		// 使用参数化方式构建查询
		$row = $DB->selectRowV2("SELECT * FROM sub_admin WHERE username=?", [$user]);
		if ($row) {
			// 验证密码 (假设密码已经使用password_hash存储)
			if ($row['password'] && $pass === $row['password']) {
				// 登录成功处理
				unset($_SESSION['xx_session_code']);
				// 生成新的session id防止会话固定攻击
				session_regenerate_id(true);
				// 生成安全的session值
				$session = md5($user . $pass . $password_hash);
				$cookies = authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);

				// 设置安全的cookie
				setcookie("sub_admin_token", $cookies, [
					'expires' => time() + 604800,
					'path' => '/',
					'httponly' => true,
					'samesite' => 'Strict'
				]);

				setCookie("tab", "primary.php", [
					'expires' => time() + 604800,
					'path' => '/',
					'httponly' => true
				]);

				// 定义要更新的表  
				$table = 'sub_admin';

				// 要更新的字段和值  
				$values = [
					'cookies' => $cookies
				];

				// 更新条件  
				$where = 'username = "' . $row['username'] . '"';

				// 执行更新操作  
				$affectedRows = $DB->update($table, $values, $where);

				WriteLog("登录日志", "登录成功", $user, $DB);
				$json = ["code" => "1", "msg" => "登录成功,欢迎您使用本系统！"];
				exit(json_encode($json, JSON_UNESCAPED_UNICODE));
			}
		}

		// 登录失败处理
		throw new Exception("用户名或密码不正确！");
	} catch (Exception $e) {
		unset($_SESSION['xx_session_code']);
		$json = ["code" => "-1", "msg" => $e->getMessage()];
		WriteLog("登录日志", "验证失败: " . $e->getMessage(), $user ?? null, $DB);
		exit(json_encode($json, JSON_UNESCAPED_UNICODE));
	}
}




?>

<!-- HTML部分修改 -->
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="X-Content-Type-Options" content="nosniff">
	<meta http-equiv="X-Frame-Options" content="DENY">
	<title><?php echo htmlspecialchars($subconf['hostname']) ?>后台登录</title>
	<?php include("foot.php"); ?>
	<link rel="stylesheet" href="../assets/layui/css/logon.css">
	<style>
		/* 整体淡入动画 */
		.layout-main {
			animation: fadeIn 0.8s ease-out;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(-20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		/* 标题动画 */
		.layout-title {
			animation: slideDown 1s ease-out;
		}

		@keyframes slideDown {
			from {
				opacity: 0;
				transform: translateY(-30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		/* 输入框焦点动画 */
		.layui-input {
			transition: all 0.3s ease;
			border: 1px solid #e6e6e6;
		}

		.layui-input:focus {
			border-color: #1E9FFF;
			box-shadow: 0 0 8px rgba(30,159,255,0.2);
			transform: translateY(-1px);
		}

		/* 登录按钮动画 */
		.layui-btn {
			transition: all 0.3s ease;
		}

		.layui-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
		}

		/* 验证码图片悬停效果 */
		.codeimg {
			transition: all 0.3s ease;
		}

		.codeimg:hover {
			transform: scale(1.02);
			cursor: pointer;
		}
	</style>
</head>

<body>
	<div class="layout-main">
		<div class="layout-title">后台登录</div>
		<div class="layout-explain" style="animation: fadeIn 1.2s ease-out;">KUBTCOIN</div>
		<!-- 原有的HTML结构不变，只添加token隐藏字段 -->
		<div class="layout-content layui-form layui-form-pane">
			<div class="layui-form-item" style="display:none;">
				<input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
			</div>
			<!-- 其他表单内容保持不变 -->
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
					<img class="codeimg" style="float: right;" src="./code.php?r=<?php echo time(); ?>" width="45%" height="38" title="点击更换验证码">
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

<script>
	layui.use(["jquery", "form"], function() {
		var $ = layui.$,
			form = layui.form;

		// 防止重复提交
		var isSubmitting = false;

		$(function() {

			document.onkeydown = function(e) {
				var keyCode = e.keyCode || e.which || e.charCode;
				var ctrlKey = e.ctrlKey || e.metaKey;
				if (keyCode == 13) {
					$(".layui-btn.layui-btn-fluid").trigger("click");
				}
			};

			form.on("submit(submit)", function(data) {
				if (isSubmitting) return false;
				isSubmitting = true;

				// 确保从隐藏字段获取 token
				var token = $('input[name="token"]').val();
				if (!token) {
					layer.msg("验证信息缺失，请刷新页面重试", {
						icon: 5
					});
					return false;
				}

				data.field.token = token;

				$.ajax({
					url: "login.php?act",
					type: "POST",
					dataType: "json",
					data: data.field,


					timeout: 10000, // 添加超时设置
					beforeSend: function() {
						layer.msg("正在登录", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					success: function(res) {
						if (res.code == "1") {
							layer.msg(res.msg, {
								icon: 1
							});
							setTimeout(function() {
								window.location.href = "./index.php";
							}, 500);
						} else {
							layer.msg(res.msg, {
								icon: 5
							});
							refreshCaptcha();
						}
					},
					error: function(xhr, status, error) {
						layer.msg("登录失败，请稍后重试", {
							icon: 5
						});
						refreshCaptcha();
					},
					complete: function() {
						isSubmitting = false;
					}
				});
				return false;
			});

			// 验证码刷新函数
			function refreshCaptcha() {
				$(".codeimg").prop("src", './code.php?r=' + Math.random());
			}



			// 验证码点击刷新
			$(".codeimg").click(refreshCaptcha);
		});

		// 添加输入框动画效果
		$('.layui-input').focus(function() {
			$(this).parent().parent().addClass('focused');
		}).blur(function() {
			$(this).parent().parent().removeClass('focused');
		});

		// 优化按钮点击效果
		$('.layui-btn').on('mousedown', function() {
			$(this).css('transform', 'scale(0.95)');
		}).on('mouseup mouseleave', function() {
			$(this).css('transform', '');
		});

		// 添加表单提交动画
		form.on("submit(submit)", function(data) {
			if (isSubmitting) return false;
			isSubmitting = true;

			// 确保从隐藏字段获取 token
			var token = $('input[name="token"]').val();
			if (!token) {
				layer.msg("验证信息缺失，请刷新页面重试", {
					icon: 5
				});
				return false;
			}

			data.field.token = token;

			// 添加提交时的动画
			$('.layout-content').css('opacity', '0.8');

			$.ajax({
				url: "login.php?act",
				type: "POST",
				dataType: "json",
				data: data.field,


				timeout: 10000, // 添加超时设置
				beforeSend: function() {
					layer.msg("正在登录", {
						icon: 16,
						shade: 0.05,
						time: false
					});
				},
				success: function(res) {
					if (res.code == "1") {
						layer.msg(res.msg, {
							icon: 1
						});
						setTimeout(function() {
							window.location.href = "./index.php";
						}, 500);
					} else {
						layer.msg(res.msg, {
							icon: 5
						});
						refreshCaptcha();
					}
				},
				error: function(xhr, status, error) {
					layer.msg("登录失败，请稍后重试", {
						icon: 5
					});
					refreshCaptcha();
				},
				complete: function() {
					isSubmitting = false;
					$('.layout-content').css('opacity', '1');
				}
			});
			return false;
		});
	});
</script>

</html>
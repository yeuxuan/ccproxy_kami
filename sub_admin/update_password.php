<?php
include("../includes/common.php");
if (!($islogin == 1)) {
    exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>
		<?php echo $subconf['hostname']?>修改密码
		</title>
		<?php
		include("foot.php");
		?>
		<!-- <link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001">
		<link rel="stylesheet" type="text/css" href="../css/theme.css" /> -->
		<style>
			body { padding: 20px; padding-right: 80px;background-color: #FFFFFF; }
		</style>
	</head>
	<body class="layui-form">
		<div class="layui-form-item">
			<label class="layui-form-label">
				旧密码
				<span class="layui-must">*</span>
			</label>
			<div class="layui-input-block">
				<input type="password" name="out_password" class="layui-input" lay-verify="required">
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">
				新密码
				<span class="layui-must">*</span>
			</label>
			<div class="layui-input-block">
				<input type="password" name="password" class="layui-input" lay-verify="required">
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">
				确定新密码
				<span class="layui-must">*</span>
			</label>
			<div class="layui-input-block">
				<input type="password" name="confirm_password" class="layui-input" lay-verify="required">
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">确认修改</button>
			</div>
		</div>
	</body>
	<!-- <script src="https://www.layuicdn.com/layui/layui.js"> -->
	</script>
	<script>
		layui.use(["jquery", "form", "laydate"], function() {
			var $ = layui.$,
				form = layui.form,
				laydate = layui.laydate;
			form.on("submit(submit)", function(data) {
				$.ajax({
					url: "ajax.php?act=updatepwd",
					type: "POST",
					dataType: "json",
					data: data.field,
					beforeSend: function() {
						layer.msg("正在提交", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					success: function(data) {
						if (data.code == "1") {
							
							parent.layer.closeAll();
							parent.layer.msg(data.msg, {
								icon: 1
							});
							parent.location.reload();
							// setTimeout("parent.location.reload();",1500);
							
						} else if(data.code == "-1") {
							layer.msg(data.msg, {
								icon: 5
							});
						}else if(data.code == "-2") {
							layer.msg(data.msg, {
								icon: 5
							});
						}else if(data.code == "-3"){
							layer.msg(data.msg, {
								icon: 5
							});
						}else{
							layer.msg("未知错误", {
								icon: 5
							});
						}
					},
					error: function(data) {
						console.log(data);
						layer.msg(data.responseText, {
							icon: 5
						});
					}
				});
				return false;
			});
		});
	</script>
</html>

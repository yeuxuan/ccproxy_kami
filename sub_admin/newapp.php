<?php
include("../includes/common.php");
if (!($islogin == 1)) {
    exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title><?php echo $subconf['hostname']?>应用名</title>
		<meta name="renderer" content="webkit" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<?php
include("foot.php");
?>
		<!-- <link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001?v=20201111001" />
		<link rel="stylesheet" type="text/css" href="./css/theme.css?v=20201111001" /> -->
		<style>
			body { background-color: #FFFFFF; padding-right: 80px; }
		</style>
	</head>
	<body class="layui-form form">
		<div class="layui-form-item">
			<label class="layui-form-label">
				应用名
				<span class="layui-must">*</span>
			</label>
			<div class="layui-input-block">
				<input type="text" name="username" class="layui-input" lay-verify="required"  placeholder="应用名"/>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">
					服务器
					<span class="layui-must">*</span>
				</label>
				<div class="layui-input-block">
					<select name="server" lay-verify="required" lay-filter="server">
						<option value="">请选择绑定服务器</option>
					</select>
				</div>
			</div>
		</div>

		<!-- <div class="layui-form-item">
			<label class="layui-form-label">
				密码
				<span class="layui-must">*</span>
			</label>
			<div class="layui-input-block">
				<input type="number" name="password" class="layui-input" lay-verify="required" placeholder="纯数字密码"/>
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">
				到期时间
				<span class="layui-must">*</span>
			</label>
			<div class="layui-input-block">
				<input type="text" name="end_date" class="layui-input" lay-verify="required" placeholder="YYYY-mm-dd HH:ii-dd" />
			</div>
		</div> -->
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">保存数据</button>
			</div>
		</div>
	</body>
	<!-- <script src="https://www.layuicdn.com/layui/layui.js?v=20201111001"></script> -->
	<script type="text/javascript" src="../assets/js/xss.js"></script>
	<script>
		layui.use(["jquery", "form", "laydate"], function() {
			var $ = layui.$,
				form = layui.form,
				laydate = layui.laydate;
			$(".layui-input").eq(0).focus();
			form.on("submit(submit)", function(data) {
				console.log(data);
				$.ajax({
					url: "ajax.php?act=newapp",
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
						if(data.code== "1"){
							window.parent.frames.reload('user');
							parent.layer.closeAll();
							parent.layer.msg(data.msg, {
								icon: 1
							});
						}
						else{
							layer.msg(data.msg==null ? "未知错误" : data.msg, {
								icon: 5
							});
						}
						console.log(data);
						// if (data.icon == "1") {
					
						// } else {
						// 	layer.msg(data.code, {
						// 		icon: data.icon
						// 	});
						// }
					},
					error: function(data) {
						console.log(data);
						layer.msg("保存数据失败", {
							icon: 5
						});
					}
				});
				return false;
			});
			// laydate.render({
			// 	elem: "[name=end_date]",
			// 	type: "datetime"
			// });

			function select() {
				$.ajax({
					url: "ajax.php?act=getuseserver",
					type: "POST",
					dataType: "json",
					success: function(data) {
						 if (data.code == "1") {
							var elem = $("[name=server]");
							for (var key in data.msg) {

								// console.log(data.msg[key]);
								 var json = data.msg[key],
								 	comment = json.comment,
								 	ip = json.ip;
									item = '<option value="' + ip + '">' + comment + '[' + ip + ']</option>';
									elem.append(item);
							}
							form.render("select");
						}
					},
					error: function(data) {
						// console.log(data);
						layer.msg("获取服务器失败", {
							icon: 5
						});
					}
				});
			}
			select();
		});
	</script>
</html>

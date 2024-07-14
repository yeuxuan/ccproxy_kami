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
	<title><?php echo $subconf['hostname'] ?>添加用户</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<?php
	include("foot.php");
	?>
	<!-- <link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001?v=20201111001" />
		<link rel="stylesheet" type="text/css" href="./css/theme.css?v=20201111001" /> -->
	<style>
		body {
			background-color: #FFFFFF;
			padding-right: 80px;
		}
		#layui-laydate1{
			top: 0!important;
		}
		.usetime{
			display: none;
		}
		.layui-form-selectup dl{
			top: auto; 
    		bottom: auto;
		}
	</style>
</head>

<body class="layui-form form">
	<div class="layui-form-item">
		<label class="layui-form-label">
			用户名
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="user" class="layui-input" lay-verify="required" placeholder="请填写用户名" />
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			密码
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="pwd" class="layui-input" lay-verify="required" placeholder="请填写用户密码" />
		</div>
	</div>


	<div class="layui-form-item">
		<label class="layui-form-label">
			使用期限
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<select name="expire" lay-verify="required" lay-filter="expire">
				<option value=""></option>
				<option value="1">1天</option>
				<option value="7">7天</option>
				<option value="30">30天</option>
				<option value="-1">自定义</option>
			</select>
		</div>
	</div>

	<div class="layui-form-item usetime">
		<label class="layui-form-label" title="使用时间">
			使用时间
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-inline">
			<input type="text" name="use_date" class="layui-input" placeholder="YYYY-MM-DD">
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label" title="应用">
			应用
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-inline">
			<select lay-verify="required" name="app" lay-filter="state">
				<option value="">请选择一个应用</option>
			</select>
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
			<button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">确定</button>
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
			form.on('select(expire)', function(data) {
				// console.log(data.value==-1?$(da):)
				if(data.value==-1){
					$(".usetime").eq(0).css("display","block");
				}
				else{
					$(".usetime").eq(0).css("display","none");
				}
	
				// var duration = Number(data.value);
				// var price = duration * unit;
			});
		$(".layui-input").eq(0).focus();
		form.on("submit(submit)", function(data) {
			if(data.field.expire==-1){
					if(data.field.use_date==""){
					layer.msg("自定义时长不能为空！", {
							icon: 5
						});
						return;
					}
				}
			console.log(data);
			$.ajax({
				url: "ajax.php?act=adduser",
				type: "POST",
				dataType: "json",
				data: {
					userdata: data.field
				},
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
						setTimeout(function(){
							window.parent.frames.reload('server_list');
						},100);
					} else {
						layer.msg(data.msg == null ? "未知错误" : data.msg, {
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

		function select() {
			$.ajax({
				url: "ajax.php?act=getapp",
				type: "POST",
				dataType: "json",
				success: function(data) {
					if (data.code == "1") {
						var elem = $("[name=app]");
						for (var key in data.msg) {
							var json = data.msg[key],
								appname = json.appname,
								appcode = json.appcode;
							item = '<option value="' + appcode + '">' + appname + '</option>';
							elem.append(item);
						}
						form.render("select");
					}
				},
				error: function(data) {
					layer.msg("获取用户失败", {
						icon: 5
					});
				}
			});
		};
		laydate.render({
				elem: "[name=use_date]",
				//range: true,
				done: function() {
					// setTimeout(function() {
					// 	window.parent.frames.reload('server_list');
					// }, 1000);
				}
			});
		// laydate.render({
		// 	elem: "[name=end_date]",
		// 	type: "datetime"
		// });

		// function select() {
		// 	$.ajax({
		// 		url: "ajax.php?act=getserver",
		// 		type: "POST",
		// 		dataType: "json",
		// 		success: function(data) {
		// 			 if (data.code == "1") {
		// 				var elem = $("[name=server]");
		// 				for (var key in data.msg) {

		// 					// console.log(data.msg[key]);
		// 					 var json = data.msg[key],
		// 					 	comment = json.comment,
		// 					 	ip = json.ip;
		// 						item = '<option value="' + ip + '">' + comment + '[' + ip + ']</option>';
		// 						elem.append(item);
		// 				}
		// 				form.render("select");
		// 			}
		// 		},
		// 		error: function(data) {
		// 			// console.log(data);
		// 			layer.msg("获取服务器失败", {
		// 				icon: 5
		// 			});
		// 		}
		// 	});
		// }
		select();
	});
</script>

</html>
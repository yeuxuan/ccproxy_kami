<?php
/*
 * @Author: yihua
 * @Date: 2022-07-07 17:45:59
 * @LastEditTime: 2022-07-08 13:35:58
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccpy\sub_admin\edituser.php
 * 一花一叶 一行代码
 */
include("../includes/common.php");
if (!($islogin == 1)) {
	exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}

$olduser=$_REQUEST["user"];
$newuser=$_REQUEST["user"];
$pwd=$_REQUEST["pwd"];
$use_date=$_REQUEST["use_date"];
$serverip=$_REQUEST["serverip"];
$connection=$_REQUEST["connection"]=="无限制"?-1:$_REQUEST["connection"];
$bandwidthup=$_REQUEST["bandwidthup"]=="无限制"?-1:$_REQUEST["bandwidthup"];
$bandwidthdown=$_REQUEST["bandwidthdown"]=="无限制"?-1:$_REQUEST["bandwidthdown"];
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title><?php echo $subconf['hostname'] ?>编辑用户</title>
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
	</style>
</head>

<body class="layui-form form">
	<div class="layui-form-item">
		<label class="layui-form-label">
			用户名
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="newuser" value="<?=$newuser; ?>" class="layui-input" lay-verify="required" placeholder="请填写用户名"/>
			<input style="display: none;" type="text" name="olduser" value="<?=$olduser; ?>" class="layui-input" placeholder="请填写用户名"  disabled/>
		</div>
		<div class="layui-input-block" style="display:none">
			<input type="text" name="serverip" value="<?=$serverip; ?>" class="layui-input"  placeholder="" />
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label">
			密码
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-block">
			<input type="text" name="pwd" value="<?=$pwd; ?>" class="layui-input" lay-verify="required" placeholder="请填写用户密码" />
		</div>
	</div>

	<div class="layui-form-item">
		<label class="layui-form-label" title="到期时间">
			到期时间
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-inline">
			<input type="text" name="use_date" value="<?=$use_date; ?>" class="layui-input" placeholder="yyyy-MM-dd HH:mm:ss">
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label" title="连接数">
			连接数
		</label>
		<div class="layui-input-inline">
			<input type="text" name="connection" value="<?=$connection; ?>" class="layui-input" placeholder="-1 表示不限制">
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label" title="上行带宽">
			上行带宽
		</label>
		<div class="layui-input-inline">
			<input type="text" name="bandwidthup" value="<?=$bandwidthup; ?>" class="layui-input" placeholder="-1 表示不限制 单位（MS）">
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label" title="下行带宽">
			下行带宽
		</label>
		<div class="layui-input-inline">
			<input type="text" name="bandwidthdown" value="<?=$bandwidthdown; ?>" class="layui-input" placeholder="-1 表示不限制 单位（MS）">
		</div>
	</div>
	<!-- <div class="layui-form-item">
		<label class="layui-form-label" title="应用">
			应用
			<span class="layui-must">*</span>
		</label>
		<div class="layui-input-inline">
			<select lay-verify="required" name="app" lay-filter="state">
				<option value="">请选择一个应用</option>
			</select>
		</div>
	</div> -->

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
			
			console.log($("[name=use_date]").eq(0).val('<?=$use_date;?>'));
			form.render("input");
			// form.on('select(expire)', function(data) {
			// 	// console.log(data.value==-1?$(da):)
			// 	if(data.value==-1){
			// 		$(".usetime").eq(0).css("display","block");
			// 	}
			// 	else{
			// 		$(".usetime").eq(0).css("display","none");
			// 	}
	
			// 	// var duration = Number(data.value);
			// 	// var price = duration * unit;
			// });
		$(".layui-input").eq(0).focus();
		form.on("submit(submit)", function(data) {
			//console.log(data.field)
			// if(data.field.expire==-1){
			// 		if(data.field.use_date==""){
			// 		layer.msg("自定义时长不能为空！", {
			// 				icon: 5
			// 			});
			// 			return;
			// 		}
			// 	}
			// console.log(data);
			$.ajax({
				url: "ajax.php?act=userupdate",
				type: "POST",
				dataType: "json",
				data: {
					usermodel: {
						olduser:data.field.olduser,
						newuser:data.field.newuser,
						pwd:data.field.pwd,
						day:data.field.use_date,
						serverip:data.field.serverip,
						connection:data.field.connection,
						bandwidthup:data.field.bandwidthup,
						bandwidthdown:data.field.bandwidthdown
					}
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
					layer.msg("编辑数据失败", {
						icon: 5
					});
				}
			});
			return false;
		});

		// function select() {
		// 	$.ajax({
		// 		url: "ajax.php?act=getapp",
		// 		type: "POST",
		// 		dataType: "json",
		// 		success: function(data) {
		// 			if (data.code == "1") {
		// 				var elem = $("[name=app]");
		// 				for (var key in data.msg) {
		// 					var json = data.msg[key],
		// 						appname = json.appname,
		// 						appcode = json.appcode;
		// 					item = '<option value="' + appcode + '">' + appname + '</option>';
		// 					elem.append(item);
		// 				}
		// 				form.render("select");
		// 			}
		// 		},
		// 		error: function(data) {
		// 			layer.msg("获取用户失败", {
		// 				icon: 5
		// 			});
		// 		}
		// 	});
		// };
		laydate.render({
				elem: "[name=use_date]",
				format: "yyyy-MM-dd HH:mm:ss",
				//range: true,
				done: function(e) {
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
		//select();
	});
</script>

</html>
<?php
include("../includes/common.php");

if (!($islogin == 1)) {
	exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}
// $sql = "SELECT * FROM `server_list`";
// $ser = $DB->select($sql);
// $serverlist = array();
// foreach ($ser as $key => $value) {
// 	//$serverlist[$ser[$key]['ip']]=$ser[$key]['state'];
// 	$serverlist[$key] = array(
// 		'ip' => $ser[$key]['ip'],
// 		'state' => $ser[$key]['state'],
// 	);
// }
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title><?php echo $subconf['hostname']?>管理用户</title>
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<?php
	include("foot.php");
	?>
	<!-- <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=20201111001" />
	<link rel="stylesheet" type="text/css" href="./css/theme.css?v=20201111001" /> -->
</head>

<body>
	<!-- 筛选条件 -->
	<div class="layui-card">
		<div class="layui-card-body layui-form">
			<div class="layui-form-item" style="padding-right: 5vw;padding-top: 15px;">

				<label class="layui-form-label" title="应用名">
					应用名:
				</label>
				<div class="layui-input-inline">
					<input type="text" name="appname" class="layui-input" />
				</div>
				<label class="layui-form-label" title="用户名">
					服务器:
				</label>
				<div class="layui-input-inline">
					<select name="server" lay-verify="required" lay-filter="server">
						<option value="*">全部服务器</option>
					</select>
				</div>
				<!-- <label class="layui-form-label layui-hide" title="创建时间">
						到期时间
					</label> -->
				<!-- <div class="layui-input-inline layui-hide">
						<input type="text" name="found_date" class="layui-input" placeholder="YYYY-MM-DD" />
					</div> -->
				<!-- <span>
						注意:如果操作不生效请检查远程服务器的配置文件web/api.php CCProxy密码是否正确！
					</span> -->
			</div>
		</div>
	</div>
	<!-- 表格 -->
	<div class="layui-card">
		<div class="layui-card-body">
			<table id="user" lay-filter="user"></table>
		</div>
	</div>
</body>
<!-- <script src="https://www.layuicdn.com/layui/layui.js?v=20201111001"></script> -->
<script type="text/html" id="userTool">
	<div class="layui-btn-container">
		<button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="search"><i class="layui-icon layui-icon-search"></i><span>搜索</span></button>
		<button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="New"><i class="layui-icon layui-icon-add-1"></i><span>新增</span></button>
		<button class="layui-btn layui-btn-danger layui-btn-sm" lay-event="Del"><i class="layui-icon layui-icon-delete"></i><span>删除</span></button>
	</div>
</script>
<!-- 表格按钮 -->
<script type="text/html" id="btnTool">
<a style="line-height: 26px;" class="layui-btn layui-btn-sm layui-btn-normal" lay-event="continued">详情</a>
<a style="line-height: 26px;" class="layui-btn layui-btn-sm layui-btn-danger" lay-event="del">删除</a>
</script>
<!-- 表格开关 -->
<script type="text/html" id="switchTpl">
	<input type="checkbox" name="state" value="{{d.id}}" lay-skin="switch" lay-text="开启|关闭" lay-filter="state" {{ d.state == "1" ? 'checked' : '' }} />
</script>
<!-- 表格链接 -->
<script type="text/html" id="urlTpl">
	<a href="{{d.url}}" class="layui-table-link" target="_blank">{{ d.url }}</a>
</script>
<!-- 表格图片 -->
<script type="text/html" id="imgTpl">
	<a href="{{d.url}}" class="layui-table-link" target="_blank"><img src="{{ d.url }}" /></a>
</script>
<!-- 服务器 -->
<script type="text/html" id="selserverip">
	<div class="layui-form">
		<div class="layui-input-inline">
			<select onchange="selchange(this)" id="selip" style="display: inline-block;" name="serverip" lay-verify="required" lay-filter="serverip">
			<option value="{{d.serverip}}">{{d.serverip}}(当前选择)</option>
			<?php
				$sql = 'select ip,comment,state from server_list where username=\'' . $subconf['username'] . '\' ';
				$server_list = $DB->select($sql);

				foreach ($server_list as $key => $server) {
					if ($server_list[$key]['state'] == 1) {
						echo '<option value="' . $server_list[$key]['ip'] . '">' . $server_list[$key]['ip'] . "(" . $server_list[$key]['comment'] . ")" . '</option>';
					}
				}
				?>
			</select>
		</div>
	</div>
</script>
<script>
	layui.use(["jquery", "table", "laydate", "form", "upload", "element"], function() {
		var $ = layui.$,
			table = layui.table,
			laydate = layui.laydate,
			form = layui.form,
			upload = layui.upload,
			element = layui.element;
		window.where = function() {
			var data = ["server", "appname", "password", "token", "found_date", "code", "user_key", "secret", "notify_url",
				"return_url", "account", "notice_title", "notice_content", "notice_url", "notice_state", "daili_notify_url",
				"daili_return_url", "register_state", "kami_state", "page"
			];
			//循环data，赋值控件取值
			var json = {};
			for (var key in data) {
				json[data[key]] = query(data[key]);
				// console.log(json);
			}

			return json;
		}
		select();
		// $("[name=serverip]").append("");
		form.render("select");
		table.render({
			elem: "#user",
			escape: true,
			height: "full-170",
			url: "ajax.php?act=apptable",
			page: true,
			limit: 100,
			limits: [10, 20, 30, 50, 100, 200, 300, 500, 1000, 2000, 3000, 5000, 10000],
			title: "应用",
			// skin: "line",
			// size: "lg",
			toolbar: "#userTool",
			where: where(),
			cols: [
				[{
						type: "checkbox"
					}, {
						field: "appid",
						title: "序号",
						width: 100,
						sort: true,
						align: "center"
					},
					{
						field: "appcode",
						title: "应用码",
						//minWidth: 108,
						width: 300,
						align: "center",
						// sort: true,
						// edit: "text",

					},
					{
						field: "appname",
						title: "应用名",
						//minWidth: 100,
						width: 200,
						edit: "text",
						align: "center",
						// sort: true,
						style: "color:#F581B1"
					}, {
						field: "serverip",
						title: "服务器",
						//minWidth: 108,
						width: 210,
						align: "center",
						// sort: true,
						toolbar: "#selserverip",
						style: "align-items: center;text-align: center;vertical-align: middle;"
						// edit: "text",
						// style: "color:#F581B1"
					}, {
						field: "found_time",
						title: "创建时间",
						//minWidth: 108,
						width: 200,
						align: "center",
						sort: true,
						// edit: "text",
						// style: "color:#F581B1"
					}, {
						title: "操作",
						toolbar: "#btnTool",
						width: 150,
						align: "center",
						style: "text-align: center;",
						fixed: "right"
					}
				]
			]
		});
		
		table.on("toolbar(user)", function(obj) {
			var checkStatus = table.checkStatus(obj.config.id);
			switch (obj.event) {
				case "search":
					reload("user");
					break;
				case "New":
					New();
					break;
				case "Del":
					Del(table, checkStatus);
					break;
				case "edit":
					// console.log(checkStatus,obj);
					edit(checkStatus);

					break;
			};
		});
		table.on("edit(user)", function(obj) {
			// var server = $("[name=server]").val();
			console.log(obj)
			update(obj.data.appcode, obj.data.appname, obj.data.serverip);
		});
	//选中复选框
	$('body').on("click", ".layui-table-body table.layui-table tbody tr td", function () {
            if ($(this).attr("data-field") === "0") return;
            $(this).siblings().eq(0).find('i').click();
 			});

		//触发行单击事件
		// table.on('row(user)', function(obj){
		// 		//$(obj.tr).children().children().children().next().toggleClass('layui-form-checked')
		// 		//$(obj.tr).children().children().children().attr('checked', true)
				
		// 		console.log($(obj.tr).children().children().eq(0).click());
		// 		//layui.table.render(); //重新渲染显示效果
		// });	
		// table.on("select(serverip)", function(obj) {
		// 	// var server = $("[name=server]").val();
		// 	console.log(obj)
		// 	update(obj.data.appcode, obj.data.appname, obj.data.serverip);
		// });
		form.on("select(server)", function(data) {
			reload("user");
		});
		laydate.render({
			elem: "[name=found_date]",
			//range: true,
			done: function() {
				setTimeout(function() {
					reload("user");
				}, 100);
			}
		});
		form.on("select(account)", function(data) {
			reload("user");
		});
		form.on("select(notice_state)", function(data) {
			reload("user");
		});
		form.on("select(register_state)", function(data) {
			reload("user");
		});
		form.on("select(kami_state)", function(data) {
			reload("user");
		});
		$(".layui-input").keydown(function(e) {
			if (e.keyCode == 13) {
				reload("user");
			}
		});
		table.on("tool(user)", function(obj) {
			//表格按钮事件
			var data = obj.data;
			switch (obj.event) {
				case "del":
					modifyBtn(obj);
					break;
				case "modify":
					modifyBtn(obj);
					break;
				case "continued":
					continued(obj);
					break;
			};
		});
		form.on("switch(state)", function(obj) {
			//表格开关事件
			console.log(obj.elem.checked);
		});

		function New() {
			layer.open({
				type: 2,
				title: "新增应用",
				area: ["400px", "400px"],
				maxmin: false,
				content: "newapp.php?v=20201111001"
			});
		}

		function edit(checkStatus) {
			if (checkStatus.data.length == 1) {
				layer.open({
					type: 2,
					title: "编辑管理用户",
					area: ["600px", "500px"],
					maxmin: false,
					content: "../php/edit.php?id=" + checkStatus.data[0].id + "&surface=user",
					cancel: function(index, layero) {
						reload("user");
					}
				});
			} else {
				layer.msg("请选择1条记录", {
					icon: 3
				});
			}
		}

		function Del(table, checkStatus) {
			var data = checkStatus.data;
			var appcode = [];
			for (var i = 0; i < data.length; i++) {
				appcode.push(data[i]['appcode']);
			}
			if (data.length > 0) {
				layer.confirm("确定删除选中的用户吗？", {
					icon: 3
				}, function() {
					$.ajax({
						url: "ajax.php?act=seldel",
						type: "POST",
						dataType: "json",
						beforeSend: function() {
							layer.msg("删除中", {
								icon: 16,
								shade: 0.05,
								time: false
							});
						},
						data: {
							item: appcode,
							// server: $("[name=server]").val()
						},
						success: function(data) {
							layer.msg(data.msg, {
								icon: 1
							});
							if (data.code == "1") {
								reload("user");
							}
						},
						error: function(data) {
							console.log(data);
							layer.msg("删除失败", {
								icon: 5
							});
						}
					});
				});
			} else {
				layer.msg("未选择记录", {
					icon: 3
				});
			}
		}



		function update(appcode, appname, serverip) {
			$.ajax({
				url: "ajax.php?act=update",
				type: "POST",
				dataType: "json",
				beforeSend: function() {
					layer.msg("正在更新数据", {
						icon: 16,
						shade: 0.05,
						time: false
					});
				},
				data: {
					appcode: appcode,
					appname: appname,
					serverip: serverip
				},
				success: function(data) {
					if (data.code == "1") {
						layer.msg(data.msg, {
							icon: 1
						});
					} else {
						layer.msg(data.msg, {
							icon: 5
						});
					}
				},
				error: function(data) {
					// console.log(data);
					layer.msg(data.msg, {
						icon: 5
					});
				}
			});
		}



		function query(name) {
			return $("[name=" + name + "]").val();
		}


		function modifyBtn(obj) {
			layer.confirm("确定删除此应用吗？", {
				icon: 3
			}, function() {
				var item = [];
				item[0] = obj.data;
				//console.log(item);
				$.ajax({
					url: "ajax.php?act=delapp",
					type: "POST",
					dataType: "json",
					data: {
						'appcode': item[0].appcode,
						// server: $("[name=server]").val()
					},
					beforeSend: function() {
						layer.msg("正在删除", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					success: function(data) {
						layer.msg(data.msg, {
							icon: 1
						});
						if (data.code == "1") {
							reload("user");
						}
					},
					error: function(data) {
						// var obj = eval(data);
						// console.log(obj)
						layer.alert(data.msg, {
							icon: 2
						});
					}
				});
			});
		}
		function continued(obj) {
			layer.msg("详情还在开发哦！现在基本用不到！");
		}
		// function continued(obj) {
		// 	var elem =
		// 		'<div class="layui-form"><label class="layui-form-label">续费时长<span class="layui-must">*</span></label><div class="layui-input-block"><select name="duration"><option value="1">1天</option><option value="7">7天</option><option value="30">30天</option></select></div></div>';
		// 	layer.confirm(elem, {
		// 			area: ["300px", "300px"],
		// 			success: function(layero, index) {
		// 				layero.find("select").val(1);
		// 				form.render("select");
		// 			}
		// 		},
		// 		function(index, layero) {
		// 			var duration = layero.find("select").val();
		// 			$.ajax({
		// 				url: "../php/continued.php",
		// 				type: "POST",
		// 				dataType: "json",
		// 				data: {
		// 					item:obj.data,
		// 					server:$("[name=server]").val(),
		// 					duration:duration
		// 				},
		// 				beforeSend: function() {
		// 					layer.msg("正在续费", {
		// 						icon: 16,
		// 						shade: 0.05,
		// 						time: false
		// 					});
		// 				},
		// 				success: function(data) {
		// 					layer.msg(data.code, {
		// 						icon: data.icon
		// 					});
		// 					if (data.icon == "1") {
		// 						layer.close(index);
		// 						reload("user");
		// 					}
		// 				},
		// 				error: function(data) {
		// 					var obj = eval(data);
		// 					layer.alert(obj.responseText, {
		// 						icon: 2
		// 					});
		// 				}
		// 			});
		// 		});
		// }
		function select() {
			$.ajax({
				url: "ajax.php?act=getserver",
				type: "POST",
				dataType: "json",
				success: function(data) {
					if (data.code == "1") {
						var elem = $("[name=server]");
						// var elem2 = $("[name=serverip]");
						for (var key in data.msg) {
							// console.log(elem2);
							var json = data.msg[key],
								comment = json.comment,
								ip = json.ip;
							item = '<option value="' + ip + '">' + comment + '[' + ip + ']</option>';
							// item2 = '<option value="' + ip + '">' + comment + '[' + ip + ']</option>';
							elem.append(item);
							// elem2.append(item2);
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
		// function select() {
		// 	$.ajax({
		// 		url: "../php/server.php",
		// 		type: "POST",
		// 		dataType: "json",
		// 		success: function(data) {
		// 			if (data.icon == "1") {
		// 				var elem = $("[name=server]");
		// 				for (var key in data.data) {
		// 					var json = data.data[key],
		// 						comment = json.comment,
		// 						ip = json.ip;
		// 					item = '<option value="' + ip + '">' + comment + '[' + ip + ']</option>';
		// 					elem.append(item);
		// 				}
		// 				form.render("select");
		// 			}
		// 		},
		// 		error: function(data) {
		// 			console.log(data);
		// 			layer.msg(data.responseText, {
		// 				icon: 5
		// 			});
		// 		}
		// 	});
		// }
	});

	function reload(id) {
		layui.use(["jquery", "table"], function() {
			var $ = layui.$,
				table = layui.table;
			table.reload(id, {
				page: {
					curr: 1
				},
				where: where()
			});
		});
	}

	function selchange(obj) {
		layui.use(["jquery"], function() {
			var $ = layui.$
			var appcodeobj = $(obj).parent().parent().parent().parent().parent().children().next().next().html();
			var appcode = appcodeobj.substring(appcodeobj.indexOf(">") + 1, appcodeobj.lastIndexOf("</"));
			var appnameobj = $(obj).parent().parent().parent().parent().parent().children().next().next().next().html();
			var appname = appnameobj.substring(appnameobj.indexOf(">") + 1, appnameobj.lastIndexOf("</"));
			var serverip = $(obj).val();
			// console.log(appname,appcode,serverip);
			update(appcode, appname, serverip);

		});
	}

	function update(appcode, appname, serverip) {
		layui.use(["jquery"], function() {
			var $ = layui.$
			$.ajax({
				url: "ajax.php?act=update",
				type: "POST",
				dataType: "json",
				beforeSend: function() {
					layer.msg("正在更新数据", {
						icon: 16,
						shade: 0.05,
						time: false
					});
				},
				data: {
					appcode: appcode,
					appname: appname,
					serverip: serverip
				},
				success: function(data) {
					if (data.code == "1") {
						layer.msg(data.msg, {
							icon: 1
						});
					} else {
						layer.msg(data.msg, {
							icon: 5
						});
					}
				},
				error: function(data) {
					// console.log(data);
					layer.msg(data.msg, {
						icon: 5
					});
				}
			});
		});

	}
</script>

</html>
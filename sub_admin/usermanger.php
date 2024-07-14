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
		<title><?php echo $subconf['hostname']?>用户管理</title>
		<meta name="renderer" content="webkit" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<?php
include("foot.php");
?>
		<!-- <link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001?v=20201111001" />
		<link rel="stylesheet" type="text/css" href="./css/theme.css?v=20201111001" /> -->
	</head>
	<body>
		<!-- 筛选条件 -->
		<div class="layui-card">
			<div class="layui-card-body layui-form">
				<div class="layui-form-item" style="padding-right: 5vw;padding-top: 15px;">
					<label class="layui-form-label" title="用户名">
						用户名：
					</label>
					<div class="layui-input-inline">
						<input type="text" name="user" class="layui-input" />
					</div>
					<label class="layui-form-label" title="应用">
						应用：
					</label>
					<div class="layui-input-inline">
						<select name="app" lay-filter="state">
							<option value=""></option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<!-- 表格 -->
		<div class="layui-card">
			<div class="layui-card-body">
				<table id="server_list" lay-filter="server_list"></table>
			</div>
		</div>
	</body>
	<!-- <script src="https://www.layuicdn.com/layui/layui.js?v=20201111001"></script> -->
    <!-- <script src="../assets/layui/layui.js"></script> -->
	<script type="text/html" id="server_listTool">
		<div class="layui-btn-container">
        <!-- <button class="layui-btn layui-btn-black layui-btn-sm" lay-event="reload"><i class="layui-icon layui-icon-loading-1 layui-anim layui-anim-rotate layui-anim-loop"></i><span>刷新</span></button> -->
			<button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="search"><i class="layui-icon layui-icon-search"></i><span>搜索</span></button>
			<button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="New"><i class="layui-icon layui-icon-add-1"></i><span>新增</span></button>
			<button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="edit"><i class="layui-icon layui-icon-edit"></i><span>编辑</span></button>
			<button class="layui-btn layui-btn-danger layui-btn-sm" lay-event="Del"><i class="layui-icon layui-icon-delete"></i><span>删除</span></button>
		</div>
	</script>
	<!-- 表格按钮 -->
	<script type="text/html" id="btnTool">
		<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="modify">修改</a>
		<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="select">选择</a>
		<a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="del">删除</a>
	</script>
	<!-- 表格开关 -->
	<script type="text/html" id="stateTool">
		<input type="checkbox" name="state" value="{{d.state}}" lay-skin="switch" lay-text="开启|关闭" lay-filter="state" {{ d.state == "1" ? 'checked' : '' }} />
	</script>
	<!-- 表格开关 //background-color:#33cabb-->
	<style>
		.green{
			background-color:#33cabb;
		}
	</style>
	<script type="text/html" id="pwddot">
	<span style="width: 20px;height: 20px;" class="layui-badge-dot {{d.pwdstate==1?'green':''}}"></span>
	</script>
	<script type="text/html" id="expirdot">
	<span style="width: 20px;height: 20px;" class="layui-badge-dot {{d.expire==0?'green':''}}"></span>
	</script>
	<!-- 表格链接 -->
	<script type="text/html" id="certificateTool">
		<a href="{{d.certificate}}" class="layui-table-link" target="_blank">{{ d.certificate }}</a>
	</script>
	<!-- 表格图片 -->
	<script type="text/html" id="imgTpl">
		<a href="{{d.url}}" class="layui-table-link" target="_blank"><img src="{{ d.url }}" /></a>
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
				var data = [
					"id", "code", "price","user", "state","app"
				];
				var json = {};
				for (var key in data) {
					json[data[key]] = query(data[key]);
					console.log(query(data[key]))
				}
				return json;
			}
			select();
			form.render("select");
			table.render({
				elem: "#server_list",
				escape:true,
				height: "full-170",
				url: "ajax.php?act=getuserall",
				page: true,
				limit: 100,
				limits: [10, 20, 30, 50, 100, 200, 300, 500, 1000, 2000, 3000, 5000, 10000],
				title: "用户",
				// skin: "line",
				// size: "lg",
				toolbar: "#server_listTool",
				where: where(),
                cols: [
					[{
						type: "checkbox"
					}, {
						field: "id",
						title: "序号",
						width: 100,
						sort: true,
						align: "center"
					}, {
						field: "user",
						title: "用户名",
						//minWidth: 100,
						width: 170,
						align: "center",
						// sort: true
					}, {
						field: "pwd",
						title: "密码",
						//minWidth: 100,
						width: 170,
						align: "center",
						// sort: true
					}, {
						field: "state",
						title: "账号状态",
						//minWidth: 100,
						align: "center",
						width: 170,
						toolbar: "#stateTool"
						// sort: true
					}, {
						field: "pwdstate",
						title: "密码状态",
						//minWidth: 100,
						width: 170,
						align: "center",
						toolbar: "#pwddot"
						// sort: true
					}, {
						field: "connection",
						title: "连接数",
						//minWidth: 100,
						align: "center",
						width: 100,
						// hide: true
						// sort: true
					}, {
						field: "bandwidthup",
						title: "上行带宽",
						//minWidth: 100,
						align: "center",
						width: 100,
						// hide: true
						// sort: true
					}, {
						field: "bandwidthdown",
						title: "下行带宽",
						//minWidth: 100,
						align: "center",
						width: 100,
						// hide: true
						// sort: true
					}, {
						field: "disabletime",
						title: "到期时间",
						//minWidth: 100,
						align: "center",
						width: 170,
						// hide: true
						sort: true
					}, {
						field: "expire",
						title: "到期状态",
						//minWidth: 100,
						width: 170,
						// sort:true,
						align: "center",
						toolbar:"#expirdot"
						// sort: true
					}, {
						field: "appname",
						title: "所属应用",
						//minWidth: 100,
						width: 170,
						align: "center",
						// sort: true
					}, {
						field: "serverip",
						title: "IP",
						hide:true,
						//minWidth: 100,
						width: 170,
						align: "center",
						// sort: true
					}]
				]
			});
			function select() {
			$.ajax({
				url: "ajax.php?act=getapp",
				type: "POST",
				dataType: "json",
				success: function(data) {
					if (data.code == "1") {
						var elem = $("[name=app]");
						// var elem2 = $("[name=serverip]");
						for (var key in data.msg) {
							// console.log(elem2);
							var json = data.msg[key],
								appname = json.appname,
								appcode = json.appcode;
							item = '<option value="' + appcode + '">' + appname + '</option>';
							// item2 = '<option value="' + ip + '">' + comment + '[' + ip + ']</option>';
							elem.append(item);
							// elem2.append(item2);
						}
						form.render("select");
					}
				},
				error: function(data) {
					// console.log(data);
					layer.msg("获取用户失败", {
						icon: 5
					});
				}
			});
		}
			table.on("toolbar(server_list)", function(obj) {
				var checkStatus = table.checkStatus(obj.config.id);
				switch (obj.event) {
					case "search":
						reload("server_list");
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


			table.on('rowDouble(server_list)', function(obj){
				var data=obj.data;
				console.log(data.user);
				if(data!=null) {
					layer.open({
						type: 2,
						title: "编辑用户",
						area: ["400px", "400px"],
						maxmin: false,
						content: "edituser.php?user="+data.user+"&pwd="+data.pwd+"&use_date="+data.disabletime+"&serverip="+data.serverip+"&connection="+data.connection+"&bandwidthup="+data.bandwidthup+"&bandwidthdown="+data.bandwidthdown,
						cancel: function(index, layero) {
							reload("server_list");
						}
					});
				}else{
					layer.msg("选中错误！",{
						icon: "3"
					});
				}
				//edit(1);
				});




				//选中复选框
				$('body').on("click", ".layui-table-body table.layui-table tbody tr td", function () {
            if ($(this).attr("data-field") === "0") return;
            $(this).siblings().eq(0).find('i').click();
 			});
			//触发行单击事件
			// table.on('row(server_list)', function(obj){
			// 	$(obj.tr).children().children().children().next().addClass('layui-form-checked')
			// });	
			table.on("edit(server_list)", function(obj) {
				// var server = $("[name=server]").val();
				console.log(obj)
				update(obj.data.appcode, obj.data.appname, obj.data.serverip);
			});
			// table.on("select(serverip)", function(obj) {
			// 	// var server = $("[name=server]").val();
			// 	console.log(obj)
			// 	update(obj.data.appcode, obj.data.appname, obj.data.serverip);
			// });
			form.on("select(server)", function(data) {
				reload("server_list");
			});
			laydate.render({
				elem: "[name=found_date]",
				//range: true,
				done: function() {
					setTimeout(function() {
						reload("server_list");
					}, 100);
				}
			});

			form.on("select(state)", function(data) {
				reload("server_list");
			});
			$(".layui-input").keydown(function(e) {
				if (e.keyCode == 13) {
					if($("[name=app]").val()==""){
						layer.msg("请选择查询的应用！");
					}else{
						reload("server_list");
					}
					
				}
			});

			table.on("tool(server_list)", function(obj) {
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
				
				elem=$(this).parent().parent().parent().children();
				user=elem.eq(2).text();
				pwd=elem.eq(3).text();
				day=elem.eq(9).text();
				ip=elem.eq(12).text();
				sw=$(this).val();
				connection=elem.eq(6).text()=="无限制"?-1:elem.eq(6).text();
				bandwidthup=elem.eq(7).text()=="无限制"?-1:elem.eq(7).text();
				bandwidthdown=elem.eq(8).text()=="无限制"?-1:elem.eq(8).text();
				$.ajax({
					url: "ajax.php?act=upswitchuser",
					type: "POST",
					dataType: "json",
					data: {
						usermodel:{
							user:user,
							pwd:pwd,
							day:day,
							serverip:ip,
							sw:sw,
							connection:connection,
							bandwidthup:bandwidthup,
							bandwidthdown:bandwidthdown
						}
					},
					beforeSend: function() {
						layer.msg("正在更新", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					success: function(data) {
						layer.msg(data.msg, {
							icon: data.icon
						});
						setTimeout(function() {
							reload("server_list");
						},1000);
					},
					error: function(data) {
						layer.alert("更新失败:"+data.msg, {
							icon: 2
						});
					}
				});
			});
			
			function New() {
				layer.open({
					type: 2,
					title: "新增用户",
					area: ["400px", "400px"],
					maxmin: false,
					content: "newuser.php?v=20201111001"
				});
			}

            function edit(checkStatus) {
				console.log(checkStatus)
				if (checkStatus.data.length == 1) {
					layer.open({
						type: 2,
						title: "编辑用户",
						area: ["400px", "400px"],
						maxmin: false,
						//"edituser.php?user="+data.user+"&pwd="+data.pwd+"&use_date="+data.disabletime,
						content: "edituser.php?user="+checkStatus.data[0].user+"&pwd="+checkStatus.data[0].pwd+"&use_date="+checkStatus.data[0].disabletime+"&serverip="+checkStatus.data[0].serverip,
						cancel: function(index, layero) {
							reload("server_list");
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
				var user=[];
				for (var i = 0; i < data.length; i++) {
					user.push({
							"user":data[i]["user"],
							"serverip":data[i]["serverip"]
						});
				}
				console.log(user);
				if (data.length > 0) {
					layer.confirm("确定删除选中的用户吗？", {
						icon: 3
					}, function() {
						$.ajax({
							url: "ajax.php?act=seldeluser",
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
								item: user,
								// server: $("[name=server]").val()
							},
							success: function(data) {
								layer.msg(data.msg, {
									icon: 1
								});
								if (data.code == "1") {
									reload("server_list");
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
						if (data.code== "1") {
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
	
		
	</script>
</html>

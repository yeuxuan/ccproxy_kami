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
			<button class="layui-btn layui-btn-warm layui-btn-sm" lay-event="clearCache"><i class="layui-icon layui-icon-refresh"></i><span>清除缓存</span></button>
				<!-- 一键加时：为当前筛选或全部用户批量增加时长（天） -->
			<button id="addTimeAllBtn" class="layui-btn layui-btn-warm layui-btn-sm" lay-event="addTimeAll" title="为当前应用或全部用户批量加时"><i class="layui-icon layui-icon-date"></i><span>一键加时</span></button>
			<!-- 用户操作：打开包含批量用户操作的弹窗（如一键删除到期账号） -->
			<button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="userops" id="userOpsBtn" title="包括删除全部到期账号"><i class="layui-icon layui-icon-set-fill"></i><span>用户操作</span></button>
		
			
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
					case "clearCache":
						layer.confirm("确定要清除缓存吗？", {
							icon: 3
						}, function() {
							$.ajax({
								url: "ajax.php?act=clearcache",
								type: "POST",
								dataType: "json",
								beforeSend: function() {
									layer.msg("正在清除缓存", {
										icon: 16,
										shade: 0.05,
										time: false
									});
								},
								success: function(data) {
									layer.msg(data.msg, {
										icon: data.code == "1" ? 1 : 5
									});
									if (data.code == "1") {
										reload("server_list");
									}
								},
								error: function(data) {
									layer.msg("清除缓存失败", {
										icon: 5
									});
								}
							});
						});
						break;
				case "addTimeAll":
					// 打开自定义弹窗：支持正负、天/小时、作用域（全部/当前应用/选中），并提供实时预览和提示
					var html = '\n<div style="padding:12px;">\n  <div class="layui-form-item">\n    <label class="layui-form-label">数量</label>\n    <div class="layui-input-block">\n      <input type="number" id="__amt" class="layui-input" value="30" placeholder="可为负，例：-1 表示减少 1 单位">\n    </div>\n    <div style="margin-top:6px;color:#888;font-size:12px">示例：输入 <strong>1</strong> + 单位 <strong>天</strong> 表示增加 1 天；输入 <strong>-2</strong> + <strong>小时</strong> 表示减少 2 小时</div>\n  </div>\n  <div class="layui-form-item">\n    <label class="layui-form-label">单位</label>\n    <div class="layui-input-block">\n      <select id="__unit" class="layui-input">\n        <option value="days">天</option>\n        <option value="hours">小时</option>\n      </select>\n    </div>\n  </div>\n  <div class="layui-form-item">\n    <label class="layui-form-label">作用域</label>\n    <div class="layui-input-block">\n      <input type="radio" name="__scope" value="app" title="当前应用(默认)" checked>\n      <input type="radio" name="__scope" value="all" title="全站">\n      <input type="radio" name="__scope" value="selected" title="仅选中用户">\n    </div>\n  </div>\n  <div id="__preview" style="background:#fafafa;border:1px dashed #eee;padding:8px;margin:8px 0;color:#333;font-size:13px">预览：请填写数量并选择作用域，实时显示将要影响的范围</div>\n  <div style="color:#999;font-size:12px">注意：支持负数减少时间；永久账户(autodisable=0)将被跳过；大量用户操作可能较慢，建议先小范围测试 \nby VSC </div>\n</div>';
					layer.open({
						type:1,
						title:'一键加/减时',
						area:['520px','360px'],
						content:html,
						btn:['确认','取消'],
						success:function(layero, index){
							// 初始化预览



							function updatePreview(){
								var amount = parseFloat(layero.find('#__amt').val());
								var unit = layero.find('#__unit').val();
								var scope = layero.find('input[name="__scope"]:checked').val();
								var appVal = $('[name=app]').val();
								var appName = $('[name=app] option:selected').text() || appVal || '（未选择）';
								var selCount = 0;
								try{ var cs = table.checkStatus('server_list'); if(cs && cs.data) selCount = cs.data.length; }catch(e){}
								var action = isNaN(amount)?'未填写':(amount>0?('增加 '+amount+' '+unit):('减少 '+Math.abs(amount)+' '+unit));
								var scopeText = scope==='selected'?('选中用户（共 '+selCount+' 项）'):(scope==='app'?('当前应用：'+appName):'全站所有用户');
								var warn = '';
								if(scope==='app' && (!appVal || appVal=='')) warn = '<div style="color:#a94442;margin-top:6px">警告：未选择应用，当前操作将作用于全站所有服务器 请确认或切换为“全站”选项</div>';
								var previewHtml = '<strong>将执行：</strong> '+action+'，<strong>目标范围：</strong>'+scopeText+'。'+warn;
								layero.find('#__preview').html(previewHtml);
							}
							// 绑定事件
							layero.find('#__amt, #__unit').on('input change', updatePreview);
							layero.find('input[name="__scope"]').on('change', updatePreview);
							// 首次渲染
							updatePreview();
						},
						yes:function(index, layero){
							var amount = parseFloat(layero.find('#__amt').val());
							var unit = layero.find('#__unit').val();
							var scope = layero.find('input[name="__scope"]:checked').val();
							if(isNaN(amount) || amount==0){
								layer.msg('请输入非零的数值（正数为增加，负数为减少）',{icon:5});
								return;
							}
							var payload = { amount: amount, unit: unit };
							var targetText = '';
							if(scope==='selected'){
								var checkStatus = table.checkStatus('server_list');
								if(!checkStatus || !checkStatus.data || checkStatus.data.length==0){
									layer.msg('未选择任何用户，请先勾选表格中的目标用户',{icon:5});
									return;
								}
								var users = [];
								for(var i=0;i<checkStatus.data.length;i++){
									users.push({user:checkStatus.data[i].user, serverip:checkStatus.data[i].serverip});
								}
								payload.users = users;
								targetText = '选中用户（共 '+users.length+' 项）';
							} else if(scope==='app'){
								var appVal = $('[name=app]').val();
								var appName = $('[name=app] option:selected').text() || appVal || '（未选择）';
								payload.app = appVal;
								targetText = '当前应用：'+appName+(appVal==''? '（未选择，将作用于全站）':'');
							} else {
								payload.app = '';
								targetText = '全站所有用户';
							}
							// 提示确认信息，包含操作摘要与目标范围
							var opText = amount>0?('增加 '+amount+' '+unit):('减少 '+Math.abs(amount)+' '+unit);
							layer.confirm('<div style="text-align:left">请确认：<br/><br/><strong>操作：</strong>'+opText+'<br/><strong>作用域：</strong>'+targetText+'<br/><br/><span style="color:#999">注意：永久账户会被跳过；此操作会同步到CCProxy，可能需要一些时间</span></div>', {icon:3, area:['520px','auto']}, function(idx2){
								layer.close(idx2);
								layer.close(index);
								$.ajax({
									url:'ajax.php?act=addtimeall',
									type:'POST',
									dataType:'json',
									data: payload,
									beforeSend: function(){ layer.msg('执行中，请稍候',{icon:16,shade:0.05,time:false}); },
									success:function(res){ layer.msg(res.msg,{icon: res.code==1?1:5}); if(res.code==1) reload('server_list'); },
									error:function(){ layer.msg('操作失败',{icon:5}); }
								});
							});
						},
						cancel:function(){ }
					});
					break;
				case "userops":
					// 使用与一键加时类似的弹窗，支持选择作用域（当前应用/全站/选中）并实时预览
					var html = '\n<div style="padding:12px;">\n  <div class="layui-form-item">\n    <label class="layui-form-label">操作</label>\n    <div class="layui-input-block">\n      <button id="__del_expired" class="layui-btn layui-btn-danger">一键删除到期账号</button>\n    </div>\n  </div>\n  <div class="layui-form-item">\n    <label class="layui-form-label">作用域</label>\n    <div class="layui-input-block">\n      <input type="radio" name="__scope_userops" value="app" title="当前应用(默认)" checked>\n      <input type="radio" name="__scope_userops" value="all" title="全站">\n      <input type="radio" name="__scope_userops" value="selected" title="仅选中用户">\n    </div>\n  </div>\n  <div id="__preview_userops" style="background:#fafafa;border:1px dashed #eee;padding:8px;margin:8px 0;color:#333;font-size:13px">预览：请选择作用域或选中用户</div>\n  <div style="color:#999;font-size:12px">注意：永久账户(autodisable=0)将被跳过；删除操作会同步到 CCProxy 服务器，可能需要一些时间\n By VSC</div>\n</div>';
					layer.open({
						type:1,
						title:'用户操作',
						area:['560px','320px'],
						content:html,
						btn:['确认','取消'],
						success:function(layero, index){
							function updatePreview(){
								var scope = layero.find('input[name="__scope_userops"]:checked').val();
								var appVal = $('[name=app]').val();
								var appName = $('[name=app] option:selected').text() || appVal || '（未选择）';
								var selCount = 0;
								try{ var cs = table.checkStatus('server_list'); if(cs && cs.data) selCount = cs.data.length; }catch(e){}
								var scopeText = scope==='selected'?('选中用户（共 '+selCount+' 项）'):(scope==='app'?('当前应用：'+appName):'全站所有用户');
								layero.find('#__preview_userops').html('<strong>将执行：</strong> 删除全部已到期账号，<strong>目标范围：</strong>'+scopeText);
							}
							layero.find('input[name="__scope_userops"]').on('change', updatePreview);
							updatePreview();
							// 绑定一键删除按钮，放在弹窗内部只是触发演示，真正操作在确认按钮中执行
							layero.find('#__del_expired').on('click', function(){
								layer.msg('请点击弹窗底部的【确认】以执行删除操作',{icon:0});
							});
						},
						yes:function(index, layero){
							var scope = layero.find('input[name="__scope_userops"]:checked').val();
							var payload = {};
							if(scope==='selected'){
								var checkStatus = table.checkStatus('server_list');
								if(!checkStatus || !checkStatus.data || checkStatus.data.length==0){
									layer.msg('未选择任何用户，请先勾选表格中的目标用户',{icon:5});
									return;
								}
								var users = [];
								for(var i=0;i<checkStatus.data.length;i++){
									users.push({user:checkStatus.data[i].user, serverip:checkStatus.data[i].serverip});
								}
								payload.users = users;
							} else if(scope==='app'){
								payload.app = $('[name=app]').val();
							} else {
								payload.app = '';
							}
							layer.confirm('<div style="text-align:left">请确认：<br/><br/><strong>操作：</strong>删除全部到期账号<br/><strong>作用域：</strong>'+(scope==='selected'?('选中用户（共 '+(payload.users?payload.users.length:0)+' 项）'):(scope==='app'?('当前应用：'+($('[name=app] option:selected').text()||'（未选择）')):'全站所有用户'))+'<br/><br/><span style="color:#999">注意：永久账户会被跳过；大量用户操作可能需要较长时间</span></div>', {icon:3, area:['520px','auto']}, function(idx2){
								layer.close(idx2);
								layer.close(index);
								$.ajax({
									url:'ajax.php?act=delallexpired',
									type:'POST',
									dataType:'json',
									data: payload,
									beforeSend: function(){ layer.msg('执行中，请稍候',{icon:16,shade:0.05,time:false}); },
									success:function(res){ layer.msg(res.msg,{icon: res.code==1?1:5}); if(res.code==1) reload('server_list'); },
									error:function(){ layer.msg('操作失败',{icon:5}); }
								});
							});
						}
					});
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

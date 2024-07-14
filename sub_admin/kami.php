
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
		<meta name="renderer" content="webkit">
		<title><?php echo $subconf['hostname']?>注册卡密</title>
		<?php
include("foot.php");
?>
		<style>
			.layui-form-checked span, .layui-form-checked:hover span{
				background-color: #33cabb!important;
			}
			
		</style>
		<!-- <link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001?v=20201111001" />
		<link rel="stylesheet" type="text/css" href="./css/theme.css?v=20201111001" /> -->
	</head>
	<body>
		<!-- 筛选条件 -->
		<div class="layui-card">
			<div class="layui-card-body layui-form">
				<div class="layui-form-item" style="padding-right: 5vw;padding-top: 15px;">
					<label class="layui-form-label" title="生成账号">
						生成账号
					</label>
					<div class="layui-input-inline">
						<input type="text" name="sc_user" class="layui-input">
					</div>
				
					<label class="layui-form-label" title="卡密">
						卡密
					</label>
					<div class="layui-input-inline">
						<input type="text" name="code" class="layui-input">
					</div>
					<label class="layui-form-label" title="创建时间">
						创建时间
					</label>
					<div class="layui-input-inline">
						<input type="text" name="found_date" class="layui-input" placeholder="YYYY-MM-DD">
					</div>
					<label class="layui-form-label" title="使用时间">
						使用时间
					</label>
					<div class="layui-input-inline" >
						<input type="text" name="use_date" class="layui-input" placeholder="YYYY-MM-DD">
					</div>
					<label class="layui-form-label" title="备注">
						备注
					</label>
					<div class="layui-input-inline">
						<input type="text" name="comment" class="layui-input">
					</div>
					<label class="layui-form-label" title="所属应用">
						所属应用
					</label>
					<div class="layui-input-inline">
						<select name="app" lay-filter="state">
							<option value=""></option>
						</select>
					</div>
					<label class="layui-form-label" title="状态">
						状态
					</label>
					<div class="layui-input-inline">
						<select name="state" lay-filter="state">
							<option value=""></option>
							<option value="0">未使用</option>
							<option value="1">已使用</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<!-- 表格 -->
		<div class="layui-card">
			<div class="layui-card-body">
				<table id="daili_kami" lay-filter="daili_kami"></table>
			</div>
		</div>
	</body>
	<!-- <script src="https://www.layuicdn.com/layui/layui.js?v=20201111001"></script> -->
	<script type="text/html" id="daili_kamiTool">
		<div class="layui-btn-container">
			<button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="search"><i class="layui-icon layui-icon-search"></i><span>搜索</span></button>
			<button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="New"><i class="layui-icon layui-icon-add-1"></i><span>新增</span></button>
			<button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="Del"><i class="layui-icon layui-icon-delete"></i><span>删除</span></button>
		</div>
	</script>
	<!-- 表格按钮 -->
	<script type="text/html" id="btnTool">
		<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="modify">修改</a>
		<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="select">选择</a>
		<a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</a>
	</script>
	<!-- 表格开关 -->
	<script type="text/html" id="switchTpl">
		<input type="checkbox" name="state" value="{{d.id}}" lay-skin="switch" lay-text="开启|关闭" lay-filter="state" {{ d.state == "1" ? 'checked' : '' }}>
	</script>
	<!-- 表格链接 -->
	<script type="text/html" id="urlTpl">
		<a href="{{d.url}}" class="layui-table-link" target="_blank">{{ d.url }}</a>
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
				var data = ["id", "code", "price", "duration", "comment", "found_date", "username", "password", "use_date",
					"sc_user", "state","app"
				];
				var json = {};
				for (var key in data) {
					json[data[key]] = query(data[key]);
				}
				return json;
			}

			table.render({
				elem: "#daili_kami",
				height: "full-170",
				url: "ajax.php?act=getkami",
				page: true,
				limit: 100,
				limits: [10, 20, 30, 50, 100, 200, 300, 500, 1000, 2000, 3000, 5000, 10000],
				title: "注册卡密",
				//skin: "line",
				//size: "lg",
				toolbar: "#daili_kamiTool",
				defaultToolbar: ['filter', 'print', {
    			title: '导出' //标题
    			,layEvent: 'exports' //事件名，用于 toolbar 事件中使用
    			,icon: 'layui-icon-export' //图标类名
  				}],
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
						},{
							field: "kami",
							title: "卡密",
							width: 200,
							align: "center",
							sort: true
						}, {
							field: "times",
							title: "时长",
							width: 120,
							align: "center",
							sort: true
						},{
							field: "sc_user",
							title: "生成用户",
							width: 108,
							align: "center",
							sort: true
						}, {
							field: "found_date",
							title: "创建时间",
							width: 170,
							align: "center",
							sort: true
						}, {
							field: "username",
							title: "激活账号",
							width: 120,
							align: "center",
							sort: true
						},  {
							field: "use_date",
							title: "卡密使用时间",
							width: 170,
							align: "center",
							sort: true
						}, {
							field: "state",
							title: "状态",
							sort: true,
							align: "center",
							width: 80
						},
						{
							field: "app",
							title: "所属应用",
							sort: true,
							align: "center",
							width: 120
						},{
							field: "comment",
							title: "备注",
							width: 100,
							align: "center",
							sort: true
						}
						// ,{
						// 	title: "操作",
						// 	toolbar: "#btnTool",
						// 	width: 220,
						// 	style: "text-align: center;",
						// 	fixed: "right"
						// }
					]
				],
				done: function (res, curr, count) {
               		 exportData=res.data;
           		 }
			});
			
// 			table.render({ //其它参数在此省略      
//   defaultToolbar: ['filter', 'print', 'exports', {
//     title: '提示' //标题
//     ,layEvent: 'LAYTABLE_TIPS' //事件名，用于 toolbar 事件中使用
//     ,icon: 'layui-icon-tips' //图标类名
//   }]
// });
     
			//选中复选框
			$('body').on("click", ".layui-table-body table.layui-table tbody tr td", function () {
            if ($(this).attr("data-field") === "0") return;
            $(this).siblings().eq(0).find('i').click();
 			});


// 			/* edit: "text",style: "color:#F581B1" 列可编辑*/
			table.on("toolbar(daili_kami)", function(obj) {
				var checkStatus = table.checkStatus(obj.config.id);
				switch (obj.event) {
					case "search":
						reload("daili_kami");
						break;
					case "New":
						New();
						break;
					case "Del":
						Del(table, checkStatus);
						break;
						 //自定义头工具栏右侧图标 - 提示
					 case 'exports':
       					//  layer.alert('这是工具栏右侧自定义的一个图标按钮');
						$(".layui-table-tool .layui-table-tool-self").children().eq(2).append('<ul class="layui-table-tool-panel" style="max-height: 501px;"><li id="csv" data-type="csv">\u5bfc\u51fa\u5230 Csv \u6587\u4ef6</li><li id="excel" data-type="excel">\u5bfc\u51fa\u5230 Excel \u6587\u4ef6</li><li id="txt" data-type="txt">\u5bfc\u51fa\u5230 Txt \u6587\u4ef6</li><li id="json" data-type="json">\u5bfc\u51fa\u5230 Json \u6587\u4ef6</li><li id="xml" data-type="xml">\u5bfc\u51fa\u5230 Xml \u6587\u4ef6</li></ul>');
						//table.exportFile(obj.config.id,exportData,'txt'); //默认导出 csv，也可以为：xls
						$("#csv").click(function(){
							table.exportFile(obj.config.id,exportData,'csv'); //默认导出 csv，也可以为：xls
						});
						$("#excel").click(function(){
							table.exportFile(obj.config.id,exportData,'xls'); //默认导出 csv，也可以为：xls
						});
						$("#txt").click(function(){
							table.exportFile(obj.config.id,exportData,'txt'); //默认导出 csv，也可以为：xls
						});
						$("#json").click(function(){
							table.exportFile(obj.config.id,exportData,'json'); //默认导出 csv，也可以为：xls
						});
						$("#xml").click(function(){
							table.exportFile(obj.config.id,exportData,'xml'); //默认导出 csv，也可以为：xls
						});
     			 break;
				};
			});
			table.on("edit(daili_kami)", function(obj) {
				update(obj.field, obj.value, obj.data.id);
			});
			laydate.render({
				elem: "[name=found_date]",
				//range: true,
				done: function() {
					setTimeout(function() {
						reload("daili_kami");
					}, 100);
				}
			});
			laydate.render({
				elem: "[name=use_date]",
				//range: true,
				done: function() {
					setTimeout(function() {
						reload("daili_kami");
					}, 100);
				}
			});
			form.on("select(state)", function(data) {
				reload("daili_kami");
			});
			$(".layui-input").keydown(function(e) {
				if (e.keyCode == 13) {
					
					reload("daili_kami");
				}
			});
			table.on("tool(daili_kami)", function(obj) {
				//表格按钮事件
				var data = obj.data;
				switch (obj.event) {
					case "del":
						modifyBtn(obj);
						break;
					case "modify":
						modifyBtn(obj);
						break;
					case "select":
						selectBtn(obj);
						break;
				};
			});

			function New() {
				layer.open({
					type: 2,
					title: "新增注册卡密",
					area: ["500px", "450px"],
					maxmin: false,
					content: "new_kami.php?v=20201111001"
				});
			}
			form.on("switch(state)", function(obj) {
				//表格开关事件
				console.log(obj.elem.checked);
			});

			function Del(table, checkStatus) {
			var data = checkStatus.data;
			var kami = [];//向数组添加卡密
			//console.log(data[0]['kami']);
			for (var i = 0; i < data.length; i++) {
				kami.push(data[i]['kami']);
			}
			if (data.length > 0) {
				layer.confirm("确定删除选中的用户吗？", {
					icon: 3
				}, function() {
					$.ajax({
						url: "ajax.php?act=delkami",
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
							item: kami,
							// server: $("[name=server]").val()
						},
						success: function(data) {
							layer.msg(data.msg, {
								icon: 1
							});
							if (data.code == "1") {
								reload("daili_kami");
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



			function update(field, value, id, success) {
				$.ajax({
					url: "../php/update.php",
					type: "POST",
					dataType: "json",
					beforeSend: function() {
						layer.msg("正在更新", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					data: {
						field: field,
						id: id,
						value: value,
						surface: "daili_kami"
					},
					success: function(data) {
						if (success != undefined) {
							success(data);
						} else {
							layer.msg(data.code, {
								icon: data.icon
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
			}

			function query(name) {
				return $("[name=" + name + "]").val();
			}



			function modifyBtn(obj) {
				layer.confirm("确定修改此记录吗？", {
					icon: 3
				}, function() {
					$.ajax({
						url: "../php/modifyBtn.php",
						type: "POST",
						dataType: "json",
						data: {
							id: obj.data.id
						},
						beforeSend: function() {
							layer.msg("正在修改", {
								icon: 16,
								shade: 0.05,
								time: false
							});
						},
						success: function(data) {
							layer.msg(data.code, {
								icon: data.icon
							});
							if (data.icon == "1") {
								obj.update({
									value: 100
								});
							}
						},
						error: function(data) {
							var obj = eval(data);
							layer.alert(obj.responseText, {
								icon: 2
							});
						}
					});
				});
			}

			function selectBtn(obj) {
				var elem =
					'<div class="layui-form"><label class="layui-form-label">下拉框<span class="layui-must">*</span></label><div class="layui-input-block"><select name="selectBtn"><option value="0">A</option><option value="1">B</option></select></div></div>';
				layer.confirm(elem, {
						area: ["300px", "250px"],
						success: function(layero, index) {
							layero.find("select").val(1);
							form.render("select");
						}
					},
					function(index, layero) {
						var value = layero.find("select").val();
						$.ajax({
							url: "../php/selectBtn.php",
							type: "POST",
							dataType: "json",
							data: {
								id: obj.data.id,
								value: value
							},
							beforeSend: function() {
								layer.msg("正在修改", {
									icon: 16,
									shade: 0.05,
									time: false
								});
							},
							success: function(data) {
								layer.msg(data.code, {
									icon: data.icon
								});
								if (data.icon == "1") {
									layer.close(index);
									console.log("修改成功");
								}
							},
							error: function(data) {
								var obj = eval(data);
								layer.alert(obj.responseText, {
									icon: 2
								});
							}
						});
					});
			}
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
					layer.msg("获取服务器失败", {
						icon: 5
					});
				}
			});
		}
			// function select() {
			// 	$.ajax({
			// 		url: "../php/classify.php",
			// 		type: "POST",
			// 		dataType: "json",
			// 		success: function(data) {
			// 			if (data.icon == "1") {
			// 				var elem = $("[name=classify]");
			// 				for (var key in data.data) {
			// 					var json = data.data[key],
			// 						name = json.name,
			// 						id = json.id;
			// 					item = '<option value="' + id + '">' + name + '</option>';
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
			select(); //获取数据
			/* 
			 <select name="classify" lay-verify="required" lay-filter="classify">
			 	<option value=""></option>
			 </select>
			 form.on("select(classify)", function(data) {
			 	reload("daili_kami");
			 });
			*/
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
	<!-- 注册卡密页面文件 -->
</html>

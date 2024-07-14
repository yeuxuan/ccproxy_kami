<?php 
include '../includes/common.php';
if (!($islogin == 1)) {
    exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>
		<?php echo $subconf['hostname']; ?>-网站设置
		</title>
		<!-- <link rel="stylesheet" href="../assets/layui/css/layui.css?v=20201111001">
		<link rel="stylesheet" type="text/css" href="../css/theme.css" /> -->
        <?php
	    include("foot.php");
	    ?>
	</head>
	<body>
		<div class="layui-card layui-form">
			<!-- <div class="layui-card-header">网站设置</div> -->
			<div class="layui-card-body">
				<div class="layui-tab">
					<ul class="layui-tab-title">
						<li class="layui-this">网站设置</li>
					</ul>
					<div class="layui-tab-content">
						<div class="layui-tab-item layui-show layui-line form">
							<div class="layui-form-item">
								<label class="layui-form-label">
									网站标题
									<span class="layui-must">*</span>
								</label>
								<div class="layui-input-block">
									<input type="text" name="user_key" class="layui-input" value="<?php echo $subconf['hostname']; ?>" placeholder="请输入网站标题">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">
									网站客服
								</label>
								<div class="layui-input-block">
									<input type="text" name="kf" class="layui-input" value="<?php echo $subconf['kf'];?>" placeholder="请输入客服QQ的链接">
								</div>
							</div>
							<!-- <blockquote class="layui-elem-quote">温馨提示：请把以下的域名信息修改为您的。</blockquote> -->
							<div class="layui-form-item">
								<label class="layui-form-label">
									网盘
								</label>
								<div class="layui-input-block">
									<input type="text" name="pan" class="layui-input" value="<?php echo $subconf['pan']; ?>" placeholder="请输入网盘链接">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label">
									首页LOGO
								</label>
								<div class="layui-input-block">
									<input type="text" name="logo" class="layui-input" value="<?php echo $subconf['img']; ?>" placeholder="LOGO直链接">
								</div>
							</div>
                            <div class="layui-form-item">
								<label class="layui-form-label">
									公告
									</label>
									<div class="layui-input-block">
									<input type="checkbox" name="ggswitch" lay-skin="switch" lay-text="开启|关闭" lay-filter="ggswitch"  <?php echo($subconf["ggswitch"]==1 ? 'checked':'');?> />

    								</div>
								
								<div class="wzggs">
								<?php
								if($subconf['ggswitch']==1){
									echo '<div class="layui-form-item"><div class="gg"> <label class="layui-form-label"> 网站公告 </label> <div class="layui-input-block"> <textarea name="wzgg"  class="layui-textarea">'. $subconf['wzgg'].'</textarea> </div> </div></div>';
								}
								?>
								</div>

							
							
							</div>
						</div>
						<div class="layui-form-item">
								<div class="layui-input-block">
									<button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="submit">保存设置</button>
									<button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit lay-filter="reset">重置</button>
								</div>
							</div>
							
					</div>
				</div>
			</div>
		</div>
	</body>
	
	<!-- <script src="https://www.layuicdn.com/layui/layui.js"> -->
	</script>
	<script>
		layui.use(["jquery", "form", "element","util"], function() {
			var $ = layui.$,
				form = layui.form,
				element = layui.element,
                layedit =layui.layedit,
				util = layui.util;
				
			form.on("submit(submit)", function(data) {
				//console.log(util.escape("dsadsadas"))
				//console.log(data.field['wzgg']=data.field.wzgg.replaceAll("script"," "));
				data.field['wzgg']=data.field.wzgg.replaceAll("< >"," ")
				data.field['wzgg']=data.field.wzgg.replaceAll("</ >"," ")
				data.field['wzgg']=data.field.wzgg.replaceAll("document"," ")//util.escape(
				data.field['wzgg']=data.field['wzgg'].replaceAll("'",'"');
				data.field['wzgg']=data.field['wzgg'].replace(/\n|\r/g,"");
				$.ajax({
					url: "ajax.php?act=updateset",
					type: "POST",
					dataType: "json",
					data: data.field,
					beforeSend: function() {
						layer.msg("正在更新", {
							icon: 16,
							shade: 0.05,
							time: false
						});
					},
					success: function(data) {
						if(data.code==1){
							layer.msg("保存成功", {
							icon: 1
						});
						}
						else{
							layer.msg(data.responseText, {
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

			form.on("submit(reset)", function(){
				console.log($("[name=user_key]").val("一花端口"))
				console.log($("[name=kf]").val("487735913"))
				console.log($("[name=pan]").val("https://img.17sucai.com/upload/2413762/2021-12-20/5359f0623d9bcaa356031498e1600634.png?x-oss-process=style/big"))
				console.log($("[name=logo]").val("https://img.17sucai.com/upload/2413762/2021-12-20/5359f0623d9bcaa356031498e1600634.png?x-oss-process=style/big"))
				console.log($("[name=wzgg]").val('测试公告<br>公告<br>公告<br><div style="color:red"><span>测试公告</span></div>'))
			});

			// function init() {
			// 	$.ajax({
			// 		url: "../php/setup.php",
			// 		type: "POST",
			// 		dataType: "json",
			// 		success: function(data) {
			// 			if (data.icon == "1") {
			// 				for (var key in data.data) {
			// 					if (key != "account" && key != "auto_insert") {
			// 						$("[name=" + key + "]").val(data.data[key]);
			// 					}
			// 				}
			// 			}
			// 			$("[name=account][value=" + data.data.account + "]").attr("checked", "checked");
			// 			$("[name=auto_insert]").attr("checked", data.data.auto_insert == "1" ? "checked" : false);
			// 			form.render();
			// 		},
			// 		error: function(data) {
			// 			console.log(data);
			// 			layer.msg(data.responseText, {
			// 				icon: 5
			// 			});
			// 		}
			// 	});
			// }

			// init();
			

			form.on("switch(ggswitch)", function(obj) {
				var checked = obj.elem.checked;
				if(checked){
					console.log($(".gg").eq(0).show())
					$(".wzggs").eq(0).html('<div class="layui-form-item"><div class="gg"> <label class="layui-form-label"> 网站公告 </label> <div class="layui-input-block"> <textarea name="wzgg" id="wzggs" class="layui-textarea"></textarea></div> </div></div>');
					$("#wzggs").html(util.escape('<?php echo $subconf['wzgg'] ?>'));
				}else{
					console.log($(".gg").eq(0).hide())
					$(".wzggs").eq(0).html();
				}
			});




//             layedit.build('wzgg',{
//                 tool: [
//   'strong' //加粗
//   ,'italic' //斜体
//   ,'underline' //下划线
//   ,'del' //删除线
//   ,'|' //分割线
//   ,'left' //左对齐
//   ,'center' //居中对齐
//   ,'right' //右对齐
//   ,'link' //超链接
//   ,'unlink' //清除链接
// ]
//             });
//             layedit.sync('wzgg');
			
		});
	</script>

</html>

<?php
include '../includes/common.php';
if (!($islogin == 1)) {
    // exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
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
			layer.alert("您还没有登录，请先登录！", {
				title: "温馨提示",
				icon: 1,
				skin: "layui-layer-molv",
				anim: 4,
				btn: ["确定"],
					yes: function(){
						window.location.href="./login.php";
					}
			});
		});
	</script>
	</body>
	</html>');
}
include './head.php';
$title = '后台管理首页';
?>

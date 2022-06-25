<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css" />
		<link rel="stylesheet" type="text/css" href="css/admin.css" />
		<link rel="stylesheet" type="text/css" href="css/theme.css" />
		<title><?php echo $subconf['hostname'].'后台管理'; ?></title>
        <style>
            /* .wz{
                font: size 23px;
            }
            @media all and (min-width:1024px) {
                #logowz{
                    display: none;
                }
               #logos {
                display: block;
                text-align: center;
                font-size: 15px;
                vertical-align: middle;
                 margin-right: 25px;
                }
            } */
        </style>
	</head>
	<body class="layui-layout-body">
		<div class="layui-layout layui-layout-admin">
			<!-- 头部 -->
			<div class="layui-header custom-header">
				<ul class="layui-nav layui-layout-left">
					<li class="layui-nav-item slide-sidebar" lay-unselect>
						<a href="javascript:;" class="icon-font"><i class="ai ai-menufold"></i></a>
					</li>
				</ul>
				<ul class="layui-nav layui-layout-right">
					<li class="layui-nav-item">
						<a href="javascript:;">
							<i class="layui-icon layui-icon-notice"></i>
							<span>消息</span>
						</a>
					</li>
					<li class="layui-nav-item">
						<a href="javascript:;" style="color:#33cabb;">
							<i class="layui-icon layui-icon-username"></i>
							<span id="username"><?php echo $subconf['username']; ?></span>
						</a>
						<dl class="layui-nav-child">
							<dd>
								<a href="javascript:;" id="update_password">
									<i class="layui-icon layui-icon-password"></i>
									<span>修改密码</span>
								</a>
							</dd>
							<dd>
								<a href="javascript:;" id="quit">
									<i class="layui-icon layui-icon-logout"></i>
									<span>退出登录</span>
								</a>
							</dd>
						</dl>
					</li>
				</ul>
			</div>
			<!-- 左侧 -->
			<div class="layui-side custom-admin">
				<div class="layui-side-scroll">
					<div class="custom-logo">
						<!-- <img src="images/logo.png" alt="LOGO" /> -->
						<!-- 图片尺寸 200px*50px -->
						<h1 id="logos">一花</h1>
						<span id="logowz">管理系统</span>
					</div>
					<ul id="Nav" class="layui-nav layui-nav-tree">
						<li class="layui-nav-item layui-nav-itemed">
							<a href="javascript:;">
								<i class="layui-icon layui-icon-console"></i>
								<em>控制台</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="primary.php"><span>主页</span></a></dd>
								<dd><a href="log.php"><span>网站日志</span></a></dd>
								<dd><a href="hostset.php"><span>网站管理</span></a></dd>
								<dd><a href="usermanger.php"><span>用户管理</span></a></dd>
							</dl>
						</li>
						<li class="layui-nav-item layui-nav-itemed">
							<a href="javascript:;">
								<i class="layui-icon layui-icon-app"></i>
								<em>应用</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="app.php"><span>应用管理</span></a></dd>
								<dd><a href="server_list.php"><span>服务器列表</span></a></dd>
								<dd><a href="kami.php"><span>卡密生成</span></a></dd>
							</dl>
						</li>
						<li class="layui-nav-item layui-nav-itemed">
							<a href="javascript:;">
								<i class="layui-icon">&#xe66f;</i>
								<em>代理</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="level_list.html"><span>级别设置</span></a></dd>
								<dd><a href="daili_user.html"><span>代理管理</span></a></dd>
								<dd><a href="kami.php"><span>充值订单</span></a></dd>
								
							</dl>
						</li>
						<li class="layui-nav-item layui-nav-itemed">
							<a href="javascript:;">
								<i class="layui-icon">&#xe672;</i>
								<em>IP访问权限</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="ip_show.html"><span>官网限制</span></a></dd>
								<dd><a href="ip_url.html"><span>访问跳转</span></a></dd>
								<dd><a href="ip_content.html"><span>访问内容</span></a></dd>
								<dd><a href="ip_setup.html"><span>默认显示</span></a></dd>
							</dl>
						</li>
						<li class="layui-nav-item layui-nav-itemed">
							<a href="javascript:;">
								<i class="layui-icon">&#xe60a;</i>
								<em>网站内容</em>
							</a>
							<dl class="layui-nav-child">
								<dd><a href="activity_list.html"><span>活动</span></a></dd>
								<dd><a href="code_list.html"><span>代码</span></a></dd>
								<dd><a href="explain_list.html"><span>介绍</span></a></dd>
								<dd><a href="game_list.html"><span>扫码</span></a></dd>
								<dd><a href="doc_list.html"><span>教程</span></a></dd>
							</dl>
						</li>
					</ul>
				</div>
			</div>
			<!-- 主体 -->
			<div class="layui-body">
				<div class="layui-tab app-container" lay-allowClose="true" lay-filter="tabs">
					<ul id="appTabs" class="layui-tab-title custom-tab"></ul>
					<div id="appTabPage" class="layui-tab-content"></div>
				</div>
			</div>
			<div class="mobile-mask"></div>
		</div>
		<script src="https://www.layuicdn.com/layui/layui.js"></script>
		<script src="../assets/js/index.js"></script>
	</body>
</html>

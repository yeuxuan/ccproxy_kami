<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="../assets/layui/css/layui.css?v=20241111001" />
		<link rel="stylesheet" type="text/css" href="css/admin.css" />
		<link rel="stylesheet" type="text/css" href="css/theme.css" />
		<title><?php echo $subconf['hostname'].'后台管理'; ?></title>
        <style>
            /* 移除全局过渡动画，改为针对性设置 */
            * {
                transition: none;
            }
            
            /* 导航菜单动画优化 */
            .layui-nav-item {
                transition: background-color 0.3s ease;
            }
            
            .layui-nav-item:hover {
                background-color: rgba(255,255,255,0.1);
            }
            
            /* 简化菜单项动画 */
            .layui-nav-item a i,
            .layui-nav-item a span,
            .layui-nav-item a em {
                display: inline-block;
                transition: transform 0.2s ease-out;
                will-change: transform;
            }
            
            .layui-nav-item a:hover i,
            .layui-nav-item a:hover span,
            .layui-nav-item a:hover em {
                transform: translateX(5px);
            }
            
            /* 确保子菜单不受影响 */
            .layui-nav-child dd a:hover i,
            .layui-nav-child dd a:hover span {
                transform: none;
            }
            
            /* 头部导航优化 */
            .custom-header .layui-nav-item a:hover i,
            .custom-header .layui-nav-item a:hover span {
                transform: none;
            }
            
            /* LOGO样式优化 */
            .custom-logo {
                padding: 20px 0;
                text-align: center;
                transition: all 0.4s;
            }
            
            #logos {
                font-size: 24px;
                color: #fff;
                margin: 0;
                text-shadow: 0 0 10px rgba(51, 202, 187, 0.5);
                animation: glow 2s ease-in-out infinite alternate;
            }
            
            .custom-logo #logowz {
                display: block;
                font-size: 14px;
                color: #33cabb !important;
                margin-top: 5px;
                font-weight: 500;
                letter-spacing: 1px;
                text-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
            }
            
            /* 消息徽章动画 */
            .layui-badge {
                transition: transform 0.3s;
            }
            
            .layui-badge:hover {
                transform: scale(1.1);
            }
            
            /* LOGO发光动画 */
            @keyframes glow {
                from {
                    text-shadow: 0 0 5px #33cabb, 0 0 10px #33cabb;
                }
                to {
                    text-shadow: 0 0 10px #33cabb, 0 0 20px #33cabb;
                }
            }
            
            /* 移动端遮罩层动画 */
            .mobile-mask {
                transition: opacity 0.3s;
            }
            
            /* 选项卡动画 */
            .layui-tab-title li {
                transition: all 0.3s;
            }
            
            .layui-tab-title li:hover {
                background-color: rgba(51, 202, 187, 0.1);
            }
            
            /* 响应式布局优化 */
            @media screen and (max-width: 768px) {
                .custom-logo {
                    padding: 10px 0;
                }
                
                #logos {
                    font-size: 20px;
                }
            }
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
							<span>消息</span><span class="layui-badge">0</span>
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
					<ul id="Nav" class="layui-nav layui-nav-tree" lay-filter="tabnav">
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
						<li class="layui-nav-item layui-nav-itemed" style="display:none">
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
						<li class="layui-nav-item layui-nav-itemed" style="display:none">
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
						<li class="layui-nav-item layui-nav-itemed" style="display:none">
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
		<script src="../assets/layui/layui.js"></script>
		<script src="../assets/js/index.js"></script>
	</body>
</html>

<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 12:25:10
 * @LastEditTime: 2025-01-07 11:08:13
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\jump.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */
if(!defined('IN_CRONLITE'))exit();
$is_defend = true;
@header('Content-Type: text/html; charset=UTF-8');
include(__DIR__ . "/includes/common.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php echo $subconf['hostname']; ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		:root {
			--primary-color: #4A90E2;
			--secondary-color: #F5A623;
			--gradient-start: #4A90E2;
			--gradient-end: #67B26F;
		}
		
		body {
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
			background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
			min-height: 100vh;
			display: flex;
			flex-direction: column;
			color: #fff;
		}

		.main-container {
			flex: 1;
			display: flex;
			flex-direction: column;
			align-items: center;
			
			justify-content: center;
			padding: 1.5rem;
			text-align: center;
			max-width: 800px;
			margin: 0 auto;
		}

		.title {
			color: #fff;
			font-size: 2rem;
			margin-bottom: 1.5rem;
			font-weight: 600;
			text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
		}

		.loading-container {
			background: rgba(255, 255, 255, 0.95);
			padding: 2rem;
			border-radius: 1.5rem;
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
			backdrop-filter: blur(10px);
			margin: 1.5rem 0;
			width: 90%;
			max-width: 500px;
			transform: translateY(0);
			transition: transform 0.3s ease;
		}

		.loading-container:hover {
			transform: translateY(-5px);
		}

		.progress-bar {
			height: 6px;
			background: rgba(74, 144, 226, 0.1);
			border-radius: 3px;
			overflow: hidden;
			position: relative;
		}

		.progress-bar::after {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			height: 100%;
			width: 30%;
			background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
			animation: progress 2s ease-in-out infinite;
		}

		@keyframes progress {
			0% { left: -30%; }
			100% { left: 100%; }
		}

		.message {
			margin-top: 1.5rem;
			color: #2c3e50;
			font-size: 1.1rem;
			line-height: 1.6;
			text-align: center;
		}

		.browsers-grid {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 1.2rem;
			margin-top: 1.5rem;
			width: 100%;
			max-width: 500px;
			padding: 1rem;
		}

		.browser-item {
			background: rgba(255, 255, 255, 0.95);
			padding: 1rem;
			border-radius: 1rem;
			display: flex;
			flex-direction: column;
			align-items: center;
			text-decoration: none;
			color: #2c3e50;
			transition: all 0.3s ease;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		}

		.browser-item:hover {
			transform: translateY(-5px);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
		}

		.browser-item img {
			width: 45px;
			height: 45px;
			margin-bottom: 0.5rem;
			border-radius: 12px;
			transition: transform 0.3s ease;
		}

		.browser-item:hover img {
			transform: scale(1.1);
		}

		.browser-item span {
			font-size: 0.9rem;
			text-align: center;
			font-weight: 500;
		}

		.hint {
			margin-top: 1.5rem;
			color: rgba(255, 255, 255, 0.9);
			font-size: 0.95rem;
			background: rgba(0, 0, 0, 0.2);
			padding: 0.8rem 1.5rem;
			border-radius: 2rem;
			backdrop-filter: blur(5px);
		}

		@media (max-width: 768px) {
			.main-container {
				padding: 1rem;
			}
			
			.title {
				font-size: 1.5rem;
				margin-bottom: 1rem;
			}
			
			.browsers-grid {
				gap: 0.8rem;
				padding: 0.5rem;
			}

			.browser-item {
				padding: 0.8rem;
			}

			.browser-item img {
				width: 40px;
				height: 40px;
			}
		}

		/* 添加动画效果 */
		@keyframes fadeIn {
			from { opacity: 0; transform: translateY(20px); }
			to { opacity: 1; transform: translateY(0); }
		}

		.main-container > * {
			animation: fadeIn 0.5s ease-out forwards;
		}

		.main-container > *:nth-child(2) { animation-delay: 0.1s; }
		.main-container > *:nth-child(3) { animation-delay: 0.2s; }
		.main-container > *:nth-child(4) { animation-delay: 0.3s; }
	</style>
</head>
<body>
	<div class="main-container">
		<h1 class="title"><?php echo $subconf['hostname']; ?></h1>
		
		<div class="loading-container">
			<div class="progress-bar"></div>
			<div class="message" style="white-space: nowrap;">
				<p>当前可能无法提供最佳浏览体验</p>
				<p>请复制以下网址继续访问：</p>
				<p style="color: #4A90E2; background: rgba(255, 255, 255, 0.8); padding: 0.5rem; border-radius: 0.5rem; user-select: all; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
					<?=$site_url; ?>
				</p>
				<p>请选择以下任意浏览器继续访问</p>
			</div>
		</div>

		<div class="browsers-grid">
			<a href="mqq://forward/url?url=<?=urlencode($site_url); ?>" class="browser-item">
				<img src="/assets/img/mtt.png" alt="QQ浏览器">
				<span>QQ浏览器</span>
			</a>
			<a href="<?=$site_url; ?>" class="browser-item">
				<img src="/assets/img/browser.png" alt="系统浏览器">
				<span>系统浏览器</span>
			</a>
			<a href="googlechrome://navigate?url=<?=urlencode($site_url); ?>" class="browser-item">
				<img src="/assets/img/chrome.png" alt="Chrome">
				<span>Chrome</span>
			</a>
			<a href="ucbrowser://<?=urlencode($site_url); ?>" class="browser-item">
				<img src="/assets/img/UCMobile.png" alt="UC浏览器">
				<span>UC浏览器</span>
			</a>
			<a href="alipays://platformapi/startapp?appId=20000067&url=<?=urlencode($site_url); ?>" class="browser-item">
				<img src="/assets/img/ali.jpg" alt="支付宝">
				<span>支付宝</span>
			</a>
			<a id="taobao" href="taobao://shop.m.taobao.com/shop/shop_index.htm?url=<?=urlencode($site_url); ?>" class="browser-item">
				<img src="/assets/img/taobao.png" alt="淘宝">
				<span>淘宝</span>
			</a>
		</div>

		<p class="hint">👆 点击图标即可在对应的浏览器中打开</p>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.body.addEventListener('touchmove', function(evt) {
				if (!evt._isScroller) {
					evt.preventDefault();
				}
			});

			// 添加渐入动画
			const elements = document.querySelectorAll('.main-container > *');
			elements.forEach((el, index) => {
				el.style.opacity = '0';
				setTimeout(() => {
					el.style.opacity = '1';
				}, index * 100);
			});

			// // 自动点击淘宝浏览器
			// setTimeout(() => {
			// 	document.getElementById('taobao').children[0].click();
			// }, 1000);

			// 处理应用跳转
			function openApp(url, fallback) {
				var timeout = setTimeout(function() {
					window.location.href = fallback;
				}, 2000);

				window.location.href = url;

				window.addEventListener('blur', function() {
					clearTimeout(timeout);
				});
			}

			// 为所有跳转链接添加事件处理
			document.querySelectorAll('.browser-item').forEach(function(item) {
				item.addEventListener('click', function(e) {
					e.preventDefault();
					var url = this.getAttribute('href');
					openApp(url, '<?=$site_url?>');
				});
			});
		});
	</script>
</body>
</html>
<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 12:25:10
 * @LastEditTime: 2025-01-05 17:52:29
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\index.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */
$is_defend = true;
@header('Content-Type: text/html; charset=UTF-8');
include("./includes/common.php");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title><?php echo $subconf['hostname']; ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="./assets/layui/css/layui.css" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./assets/Message/css/message.css" />
    <link rel="stylesheet" type="text/css" href="./assets/layui/css/theme.css" />
    <link rel="stylesheet" type="text/css" href="./assets/css/style_PC.css" media="screen and (min-width: 960px)" />
    <!-- <link rel="stylesheet" type="text/css" href="./assets/css/style_Phone.css" media="screen and (min-width: 720px)" /> -->
    <style type="text/css">
        /* 全局样式 - 科技感配色 */
        :root{
            --bg:#0f1724; /* 深色背景 */
            --panel:#0b1220;
            --accent:#6ee7f9; /* 青蓝 */
            --accent2:#7c3aed; /* 紫色 */
            --muted: rgba(255,255,255,0.65);
        }
        body {
            background: radial-gradient(ellipse at top left, rgba(124,58,237,0.08), transparent 20%), linear-gradient(180deg,#071024 0%, #071a2a 100%);
            color: var(--muted);
            font-family: "Nunito", "Helvetica Neue", Arial, "PingFang SC", "Microsoft Yahei", sans-serif;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
        }

        .layui-container {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
        }

        /* 顶部跑马灯样式 */
        .top-marquee {
            margin: 12px auto 20px;
            max-width: 1000px;
            overflow: hidden;
            border-radius: 8px;
            background: linear-gradient(90deg, rgba(124,58,237,0.12), rgba(110,231,249,0.06));
            border: 1px solid rgba(255,255,255,0.03);
            box-shadow: 0 6px 30px rgba(2,6,23,0.6);
            position: relative;
            height: 48px;
            display:flex;
            align-items:center;
        }
        .marquee-track {
            white-space:nowrap;
            display:inline-block;
            will-change:transform;
            animation: marquee 18s linear infinite;
            padding-left:100%;
            font-weight:600;
            color:var(--accent);
            font-size:16px;
        }
        .marquee-item{display:inline-block;margin-right:48px;color:var(--muted)}
        @keyframes marquee{
            from{transform:translateX(0%)}
            to{transform:translateX(-100%)}
        }

        /* Logo区域样式优化 */
        .layui-logo {
            padding: 30px 0;
            text-align: center;
            color:var(--muted);
        }

        /* 覆盖外部样式表中对 .layui-logo 的白底设置（theme.css / style_PC.css），避免白色背景遮挡文字 */
        .layui-logo{
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding-top: 30px !important;
            padding-bottom: 30px !important;
            color: var(--muted) !important;
        }

        .wz-title h1 {
            font-size: 2em;
            color: #fff;
            margin-bottom: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            animation: fadeInUp 0.8s ease-out;
            text-shadow: 0 6px 18px rgba(124,58,237,0.12);
        }

        .img img {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(2,6,23,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
            max-width: 280px;
            width: 100%;
            transition: transform 0.45s cubic-bezier(0.165, 0.84, 0.44, 1);
            border:1px solid rgba(255,255,255,0.03);
        }

        .img img:hover {
            transform: scale(1.05);
        }

        /* 按钮样式优化 */
        .cer {
            margin-top: 25px;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .cer .layui-btn {
            padding: 0 22px;
            height: 40px;
            line-height: 40px;
            border-radius: 20px;
            font-size: 14px;
            transition: all 0.24s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 6px 20px rgba(2,6,23,0.45);
        }

        .cer .buwz {
            display: flex;
        }

        .cer .layui-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* 主面板样式 */
        .main {
            background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(2,6,23,0.6);
            padding: 22px;
            margin-top: 20px;
            border:1px solid rgba(255,255,255,0.03);
        }

        /* 选项卡样式 */
        .layui-tab-title {
            border-bottom: 2px solid #f0f0f0;
        }

        .layui-tab-title li {
            padding: 0 25px;
            font-size: 15px;
            position: relative;
            overflow: hidden;
        }

        .layui-tab-title .layui-this {
            color: var(--accent);
        }

        .layui-tab-title .layui-this:after {
            height: 2px;
            background: linear-gradient(90deg,var(--accent),var(--accent2));
        }

        .layui-tab-title li:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #009688;
            transition: all 0.3s ease;
        }

        .layui-tab-title li:hover:after {
            left: 0;
            width: 100%;
        }

        /* 输入框样式 */
        .inputs {
            height: 45px;
            margin: 15px 0;
            border-radius: 5px;
            border: 1px solid #e6e6e6;
            transition: all 0.3s ease;
        }

        .inputs:focus {
            border-color: #009688;
            box-shadow: 0 0 5px rgba(0, 150, 136, 0.2);
            transform: translateY(-2px);
        }

        /* 提交按钮样式 */
        .submit {
            margin-top: 25px;
            text-align: center;
        }

        .submit .layui-btn {
            width: 200px;
            height: 45px;
            line-height: 45px;
            font-size: 15px;
            border-radius: 25px;
            background: #009688;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .submit .layui-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 150, 136, 0.2);
        }

        .submit .layui-btn:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease-out, height 0.6s ease-out;
        }

        .submit .layui-btn:active:after {
            width: 300px;
            height: 300px;
            opacity: 0;
        }

        /* 查询结果样式优化 */
        .time {
            width: 80%;
            margin: 20px auto;
        }

        .time div {
            box-sizing: border-box;
            padding: 15px;
            background-color: rgba(255,255,255,0.02); /* 深色主题卡片 */
            border: 1px solid rgba(255,255,255,0.03);
            color: var(--muted);
            border-radius: 14px; /* 更圆润的角 */
            transition: all 0.28s cubic-bezier(0.2, 0.8, 0.2, 1);
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease-out;
        }

        .time div:hover {
            box-shadow: 0 6px 18px rgba(2,6,23,0.6);
        }

        .time div b {
            color: var(--accent);
            font-weight: 600;
        }

        /* 深色毛玻璃提示卡片（用于查询结果等中间提示） - 强制覆盖外部样式 */
        .msg-dark{
            padding: 10px 14px !important;
            border-radius: 14px !important;
            /* 更明显的深色背景，避免白底 */
            background: rgba(11,18,32,0.72) !important;
            border: 1px solid rgba(255,255,255,0.04) !important;
            color: var(--muted) !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            box-shadow: 0 8px 30px rgba(2,6,23,0.55) !important;
            margin-bottom: 12px !important;
            font-size: 13px !important;
            line-height: 1.8em !important;
        }
        .msg-dark b{ color: var(--accent) !important; font-weight:700 !important; }

        /* 更强覆盖：确保 .time 下的任何提示元素不会被外部白底规则覆盖 */
        .layui-container .time, .layui-container .time div, .layui-container .time .msg-dark {
            background: transparent !important;
            color: var(--muted) !important;
        }
        .layui-container .time div, .layui-container .time .msg-dark {
            background: rgba(11,18,32,0.64) !important;
            border: 1px solid rgba(255,255,255,0.04) !important;
            box-shadow: 0 8px 30px rgba(2,6,23,0.45) !important;
        }
        .layui-container .time div b { color: var(--accent) !important; }

        /* 添加移动端选项卡样式优化 */
        @media screen and (max-width: 480px) {
            .layui-tab-title li {
                padding: 0 12px;
                /* 减小内边距 */
                font-size: 14px;
                /* 稍微减小字体 */
            }

            /* 简化选项卡文字 */
            .layui-tab-title li[data-mobile-text]:not(.layui-this) {
                font-size: 13px;
            }
        }

        @media screen and (max-width: 360px) {
            .layui-tab-title li {
                padding: 0 8px;
                /* 更小的内边距 */
                font-size: 13px;
                /* 更小的字体 */
            }
        }

        /* 添加移动端适配 */
        @media screen and (max-width: 480px) {
            .time {
                padding: 0;
            }

            .time div {
                font-size: 13px;
                /* 移动端稍微减小字体 */
                padding: 12px;
                /* 减小内边距 */
                /* width: calc(100% - 20px);
                margin-left: 10px; */
            }
        }

        /* 添加页面加载动画 */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 轻微背景颗粒（用伪元素做轻量效果） */
        .layui-container:before{
            content:'';
            position:fixed;
            left:0;right:0;top:0;bottom:0;
            background-image: radial-gradient(rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events:none;
            opacity:0.25;
            mix-blend-mode:overlay;
        }

        /* 覆盖 layui-card 的默认白底（仅作用于 logo 区），让 logo 区与深色主题一致 */
        .layui-logo .layui-card{
            background: transparent !important;
            border: 1px solid rgba(255,255,255,0.03) !important;
            box-shadow: 0 8px 30px rgba(2,6,23,0.55) !important;
            padding: 18px !important;
        }

        /* 更强的替代方案：自定义卡片类，避免被 Layui 深层选择器或脚本注入的样式影响 */
        .custom-card{
            background: transparent !important;
            border: 1px solid rgba(255,255,255,0.03) !important;
            box-shadow: 0 8px 30px rgba(2,6,23,0.55) !important;
            padding: 18px !important;
            color: var(--muted) !important;
        }
        .custom-card, .custom-card * , .custom-card .layui-card-body, .custom-card .layui-card-header {
            background: transparent !important;
            color: inherit !important;
            border-color: rgba(255,255,255,0.03) !important;
        }

        /* 强制 logo 区标题和文本颜色，避免被全局白色文本规则覆盖 */
        .layui-logo .wz-title h1, .custom-card .wz-title h1 {
            color: #ffffff !important;
            text-shadow: 0 6px 18px rgba(124,58,237,0.18) !important;
        }

        /* 按钮文字对比 */
        .custom-card .cer .layui-btn, .layui-logo .cer .layui-btn {
            color: #071024 !important; /* 深色文字在亮色按钮上 */
        }
        .custom-card .cer .layui-btn.layui-btn-danger, .layui-logo .cer .layui-btn.layui-btn-danger{
            color: #fff !important;
        }

        /* 覆盖可能存在的 card-body 或内层元素的白色背景（确保彻底去除白底） */
        .layui-logo .layui-card, .layui-logo .layui-card .layui-card-body, .layui-logo .layui-card .layui-card-header {
            background: transparent !important;
            color: var(--muted) !important;
        }

        /* 确保 logo 区内部元素不被白底或深色外的颜色覆盖 */
        .layui-logo .layui-card * {
            background: transparent !important;
            color: inherit !important;
            border-color: rgba(255,255,255,0.03) !important;
        }

        /* 按钮（公告/客服/网盘）——只修改 logo 区内的样式，避免影响全站其它按钮 */
        .layui-logo .cer .layui-btn{
            background: linear-gradient(90deg,var(--accent),var(--accent2)) !important;
            color: #071024 !important;
            border: none !important;
            box-shadow: 0 8px 24px rgba(124,58,237,0.12) !important;
        }
        .layui-logo .cer .layui-btn.layui-btn-danger{
            background: linear-gradient(90deg,#ff7a7a,#ff4d4d) !important;
            color:#fff !important;
        }
        .layui-logo .cer .layui-btn.layui-btn-normal{
            background: linear-gradient(90deg,var(--accent),#34d399) !important;
            color:#071024 !important;
        }
        .layui-logo .cer .layui-btn.layui-btn-checked{
            background: linear-gradient(90deg,#7c3aed,var(--accent)) !important;
            color:#fff !important;
        }

        /* 图片容器加深色背景，突出 logo */
        .layui-logo .img{
            display:inline-block;
            padding:10px;
            border-radius:12px;
            background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
            border:1px solid rgba(255,255,255,0.03);
            box-shadow: 0 8px 30px rgba(2,6,23,0.5);
        }
        .layui-logo .img img{ background:transparent !important; }

        /* 选项卡切换动画 */
        .layui-tab-content .layui-tab-item {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
    <!-- 强制性覆盖：确保 SweetAlert / layer / qmsg 在页面中显示为深色毛玻璃（放在 head 末尾，优先级高） -->
    <style>
        /* Modal overlays */
        .sweet-overlay, .swal-overlay {
            background-color: rgba(0,0,0,0.6) !important;
        }
        /* SweetAlert modal */
        .sweet-alert, .swal-modal {
            /* 使用透明背景以保持页面深色主题下的通透感，保留边框/阴影/模糊 */
            background: transparent !important;
            color: #e6eef8 !important;
            border: 1px solid rgba(255,255,255,0.04) !important;
            box-shadow: 0 12px 40px rgba(0,0,0,0.7) !important;
            backdrop-filter: blur(6px) !important;
        }
        .sweet-alert h2, .sweet-alert p, .swal-text { color: #e6eef8 !important; }
        .sweet-alert input, .swal-modal input, .swal-modal textarea {
            background: rgba(255,255,255,0.03) !important;
            color: #e6eef8 !important;
            border: 1px solid rgba(255,255,255,0.06) !important;
        }

        /* Fix: 禁用 success 图标的白色旋转占位（:before / :after）在深色主题下的可见性问题 */
        .swal-icon--success:before,
        .swal-icon--success:after {
            /* 取消白色块背景，转为透明（保留对勾动画或使用 JS 处理） */
            background: transparent !important;
            box-shadow: none !important;
            /* 取消库里内嵌的旋转占位动画，避免看到两个白色旋转层 */
            -webkit-animation: none !important;
            animation: none !important;
            /* 确保不会遮挡对勾 */
            z-index: 0 !important;
        }
        /* 强制对 before/after 的变换为与对勾一致，避免不一致的角度导致错位 */
        .swal-icon--success:before { -webkit-transform: rotate(-45deg) !important; transform: rotate(-45deg) !important; }
        .swal-icon--success:after  { -webkit-transform: rotate(-45deg) !important; transform: rotate(-45deg) !important; }

        /* 隐藏用于白色背景遮角的元素（库原始样式），避免在深色主题上出现白色角块 */
        .swal-icon--success__hide-corners{ display:none !important; background:transparent !important; }

        /* 确保对勾线在最上层且颜色为深色主题友好色 */
        .swal-icon--success__line{ z-index:3 !important; background-color: rgba(51,202,187,0.95) !important; }

        /* layui layer */
        .layui-layer, .layui-layer-content, .layui-layer-title, .layui-layer-msg {
            /* 透明背景，保留其它视觉效果 */
            background: transparent !important;
            color: #e6eef8 !important;
            border: 1px solid rgba(255,255,255,0.04) !important;
            box-shadow: 0 8px 30px rgba(0,0,0,0.6) !important;
        }

        /* Ensure announcement buttons inside logo area are visible */
        .layui-logo .layui-btn, .custom-card .layui-btn {
            background: linear-gradient(90deg,#2db6b6,#1a9bd6) !important;
            color: #071024 !important;
            border: none !important;
        }
    </style>
</head>

<body>
    <div class="layui-container">
        <!-- 顶部跑马灯 -->
        <div class="top-marquee">
            <div class="marquee-track" id="marqueeTrack">
                <span class="marquee-item">欢迎来到 <?php echo $subconf['hostname']; ?> —— 高性能代理服务平台</span>
                <span class="marquee-item">支持卡密、注册、在线查询与客服对接</span>
                <span class="marquee-item">实时监控 · 安全稳定 · 企业级体验</span>
            </div>
        </div>
        <!-- logo部分 -->
        <div class="layui-logo">
            <div class="layui-row">
                <div class="custom-card layui-col-xs12" style="background:transparent !important;border:1px solid rgba(255,255,255,0.03);box-shadow:0 8px 30px rgba(2,6,23,0.55);padding:18px;">
                    <div class="wz-title">
                        <h1><?php echo $subconf['hostname']; ?></h1>
                    </div>
                    <div class="img">
                        <!-- <img src="<?php echo $subconf['img']; ?>" alt="logo"> -->
                        <img src="/assets/img/one-by-one.gif" lay-src="<?php echo $subconf['img']; ?>" alt="logo">
                    </div>
                    <div class="layui-col-xs-12 cer">
                        <a class="buwz" style="color:white" onclick="<?php echo $subconf['ggswitch'] == 1 ? "showgg()" : "notgg()"; ?>">
                            <div class="layui-btn layui-btn-danger" style="background:linear-gradient(90deg,#ff7a7a,#ff4d4d);color:#fff;border:none;">公告</div>
                        </a>
                        <a class="buwz" style="color:white" href="<?php echo $subconf['kf']; ?>">
                            <div class="layui-btn layui-btn-normal" style="background:linear-gradient(90deg,var(--accent),#34d399);color:#071024;border:none;">客服</div>
                        </a>
                        <a class="buwz" style="color:white" href="<?php echo $subconf['pan']; ?>">
                            <div class="layui-btn layui-btn-checked" style="background:linear-gradient(90deg,#7c3aed,var(--accent));color:#fff;border:none;">网盘</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- 面板部分 -->
        <div class="main">
            <div style="margin: 0;" class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                <ul class="layui-tab-title">
                    <li class="layui-this" data-mobile-text="充值">卡密充值</li>
                    <li data-mobile-text="注册">用户注册</li>
                    <li data-mobile-text="查询">用户查询</li>
                </ul>
                <div class="layui-tab-content" style="height: auto;">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-input-block">
                            <input type="text" name="km" id="pay-user" class="layui-input inputs" placeholder="请输入充值账号" lay-verify="required" />
                        </div>
                        <div class="layui-input-block">
                            <input type="text" name="code" id="pay-code" class="layui-input inputs" placeholder="请输入充值卡密" lay-verify="required" />
                        </div>
                        <div class="layui-input-block layui-btn-xs submit">
                            <button id="pay" type="button" class="layui-btn layui-btn-normal">充值</button>
                        </div>
                    </div>
                    <!-- <div class="layui-tab-item">
                         <div class="layui-input-block">
                            <input type="text" name="code" id="post-code" class="layui-input inputs" placeholder="请输入兑换卡密" lay-verify="required" />
                        </div>
                        <div class="layui-input-block layui-btn-xs submit">
                            <button id="postpay" type="button" class="layui-btn layui-btn-normal">兑换</button>
                        </div>
                    </div> -->
                    <div class="layui-tab-item">
                        <div class="layui-input-block">
                            <input type="text" name="km" id="reg-user" class="layui-input inputs" placeholder="请输入账号" lay-verify="required" />
                        </div>
                        <div class="layui-input-block">
                            <input type="text" name="km" id="reg-pwd" class="layui-input inputs" placeholder="请输入密码" lay-verify="required" />
                        </div>
                        <div class="layui-input-block">
                            <input type="text" name="km" id="reg-code" class="layui-input inputs" placeholder="请输入卡密" lay-verify="required" />
                        </div>
                        <div class="layui-input-block layui-btn-xs submit">
                            <button id="registed" type="button" class="layui-btn layui-btn-normal">注册</button>
                        </div>
                    </div>
                    <div class="layui-tab-item">
                        <div class="layui-input-block">
                            <div class="layui-form form">
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <select id="sel" name="app" lay-filter="app" lay-verify="required">
                                            <option value=""></option>
                                            <!-- <option value="0">一花端口(公端)</option> -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-input-block">
                            <input type="text" name="km" id="check-user" class="layui-input inputs" placeholder="请输入查询账号" lay-verify="required" />
                        </div>
                        <div class="time">
                        </div>
                        <div class="layui-input-block layui-btn-xs submit">
                            <button id="check" type="button" class="layui-btn layui-btn-normal">查询</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- foot底部 -->
        <div class="layui-footer">

        </div>
    </div>
    <script src="./assets/Message/js/message.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="./assets/layui/layui.js"></script>
    <script src="./assets//js/jquery-3.5.1.min.js"></script>
    <script src="./assets/js/jquery.cookie.min.js"></script>
    <script src="./assets/js/sweetalert.min.js"></script>
    <script type="text/javascript">
        // 统一的 API 处理工具
        const API = {
            async request(url, data, options = {}) {
                const defaultOptions = {
                    type: "POST",
                    dataType: "json",
                    timeout: 30000,
                    beforeSend: () => {
                        layer.msg("处理中...", {
                            icon: 16,
                            shade: 0.05,
                            time: false
                        });
                    }
                };

                try {
                    const response = await $.ajax({
                        url,
                        data,
                        ...defaultOptions,
                        ...options
                    });
                    layer.closeAll();
                    return response;
                } catch (error) {
                    layer.closeAll();
                    throw error;
                }
            }
        };

        // 表单验证工具
        const Validator = {
            username(value) {
                if (!value) return "账号不能为空";
                if (value.length < 5) return "账号长度不得小于5位";
                if (!/^[A-Za-z0-9]+$/.test(value)) return "账号只能包含数字和英文";
                return null;
            },

            password(value) {
                if (!value) return "密码不能为空";
                if (value.length < 5) return "密码长度不得小于5位";
                if (!/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z_]{5,16}$/.test(value)) {
                    return "密码必须包含数字和字母，长度在5-16位之间";
                }
                return null;
            },

            code(value, minLength = 1) {
                if (!value) return "卡密不能为空";
                if (value.length < minLength) return `卡密长度不得小于${minLength}位`;
                if (value.length > 128) return "卡密长度最大为128位";
                return null;
            }
        };

        // 消息提示工具
        const Message = {
            success(msg) {
                layer.msg(msg, {
                    icon: 1
                });
                Qmsg.success(msg, {
                    html: true
                });
            },
            error(msg) {
                layer.msg(msg, {
                    icon: 5
                });
                Qmsg.error(msg, {
                    html: true
                });
            },
            info(msg) {
                Qmsg.info(msg);
            }
        };

        layui.use(["jquery", "form", "element", "flow"], function() {
            const {
                $,
                form,
                element,
                flow
            } = layui;
            let selectHeight = 0;

            // 初始化
            initializeApp();

            // 事件绑定
            bindEvents();

            // 初始化应用
            function initializeApp() {
                loadApplications();
                flow.lazyimg();
                checkScreenSize();

                // 初始化公告
                initializeAnnouncement();
            }

            // 加载应用列表
            async function loadApplications() {
                try {
                    const response = await API.request("api/api.php?act=gethostapp");
                    if (response.code === "1") {
                        updateApplicationSelect(response.msg);
                    }
                } catch (error) {
                    Message.error("获取应用列表失败");
                }
            }

            // 更新应用选择器
            function updateApplicationSelect(applications) {
                const $select = $("[name=app]");
                const options = applications.map(app =>
                    `<option value="${app.appcode}">${app.appname}</option>`
                ).join('');

                $select.append(options);
                form.render("select");

                handleSelectHeight();
            }

            // 处理选择器高度
            function handleSelectHeight() {
                $(".layui-form-select").on("click", function() {
                    const $layuiShow = $(".layui-show");
                    const $upbit = $(".layui-anim-upbit");
                    selectHeight = $layuiShow.outerHeight(true);

                    if ($upbit.outerHeight(true) > $layuiShow.outerHeight(true)) {
                        $layuiShow.css("height", $upbit.outerHeight(true) + 40);
                    }
                });
            }

            // 绑定事件处理
            function bindEvents() {
                // 充值按钮点击事件
                $("#pay").on("click", handlePay);

                // 注册按钮点击事件
                $("#registed").on("click", handleRegister);

                // 查询按钮点击事件
                $("#check").on("click", handleQuery);
            }

            // 充值处理
            async function handlePay() {
                const user = $("#pay-user").val();
                const code = $("#pay-code").val();

                const userError = Validator.username(user);
                if (userError) return Message.info(userError);

                const codeError = Validator.code(code);
                if (codeError) return Message.info(codeError);

                try {
                    const response = await API.request("api/cpproxy.php?type=update", {
                        user,
                        code
                    });
                    if (response.code === 1) {
                        Message.success("充值成功");
                    } else {
                        Message.error(response.msg || "充值失败");
                    }
                } catch (error) {
                    Message.error("充值失败");
                }
            }

            // 注册处理
            async function handleRegister() {
                const user = $("#reg-user").val().trim();
                const pwd = $("#reg-pwd").val().trim();
                const code = $("#reg-code").val().trim();

                // 验证输入
                const userError = Validator.username(user);
                if (userError) return Message.info(userError);

                const pwdError = Validator.password(pwd);
                if (pwdError) return Message.info(pwdError);

                const codeError = Validator.code(code, 15);
                if (codeError) return Message.info(codeError);

                try {
                    $("#registed").prop("disabled", true);
                    const response = await API.request("api/cpproxy.php?type=insert", {
                        user,
                        pwd,
                        code
                    });

                    if (response.code === 1) {
                        Message.success(response.msg);
                    } else {
                        Message.error(response.msg);
                    }
                } catch (error) {
                    Message.error("注册失败");
                } finally {
                    $("#registed").prop("disabled", false);
                }
            }

            // 查询处理
            async function handleQuery() {
                const user = $("#check-user").val();
                const appcode = $("#sel option:checked").val();

                if (!appcode) return Message.info("请选择一个应用");

                const userError = Validator.username(user);
                if (userError) return Message.info(userError);

                try {
                    const response = await API.request("api/cpproxy.php?type=query", {
                        user,
                        appcode
                    });

                    if (response.code === 1) {
                        updateQueryResult(response.msg);
                        Message.success("查询成功");
                    } else {
                        Message.error(response.msg || "查询失败");
                    }
                } catch (error) {
                    $(".time").eq(0).html("");
                    Message.error("查询失败");
                }
            }

            // 更新查询结果（使用深色毛玻璃样式，提升可读性）
            function updateQueryResult(msg) {
                // Use inline styles with !important to make this insertion as high-priority as possible
                var html = `\
                    <div class="msg-dark" style="border-radius:14px !important; overflow:hidden !important; background: rgba(11,18,32,0.72) !important; border: 1px solid rgba(255,255,255,0.04) !important; box-shadow: 0 8px 30px rgba(2,6,23,0.45) !important; -webkit-backdrop-filter: blur(6px) !important; backdrop-filter: blur(6px) !important; color: var(--muted) !important;">\
                        <b>${msg}</b>\
                    </div>\
                `;

                $(".time").eq(0).html(html);

                // Immediately reinforce the styles on the newly-inserted element using setProperty with 'important'
                try{
                    var el = document.querySelector('.time .msg-dark');
                    if(el){
                        el.style.setProperty('border-radius','14px','important');
                        el.style.setProperty('overflow','hidden','important');
                        el.style.setProperty('background','rgba(11,18,32,0.72)','important');
                        el.style.setProperty('border','1px solid rgba(255,255,255,0.04)','important');
                        el.style.setProperty('box-shadow','0 8px 30px rgba(2,6,23,0.45)','important');
                        el.style.setProperty('backdrop-filter','blur(6px)','important');
                        el.style.setProperty('-webkit-backdrop-filter','blur(6px)','important');
                        el.style.setProperty('color','var(--muted)','important');
                    }
                }catch(e){}
            }

            // 检查屏幕尺寸
            function checkScreenSize() {
                if (window.innerWidth <= 480) {
                    $('.layui-tab-title li').each(function() {
                        const $this = $(this);
                        const mobileText = $this.data('mobile-text');
                        if (mobileText) {
                            $this.text(mobileText);
                        }
                    });
                } else {
                    // 恢复原始文本
                    $('.layui-tab-title li').each(function() {
                        const $this = $(this);
                        const originalText = $this.hasClass('layui-this') ? '卡密充值' :
                            ($this.index() === 1 ? '用户注册' : '用户查询');
                        $this.text(originalText);
                    });
                }
            }

            // 添加窗口大小改变监听
            $(window).on('resize', checkScreenSize);

            // 初始化公告
            function initializeAnnouncement() {
                const isModal = <?php echo empty($conf['wzgg']) ? 'false' : 'true'; ?>;
                if (!$.cookie('op') && isModal) {
                    showAnnouncement();
                    setAnnouncementCookie();
                }
            }

            // 显示公告
            window.showgg = function() {
                showAnnouncement();
                setAnnouncementCookie();
            };

            // 显示无公告提示
            window.notgg = function() {
                swal({
                    title: "公告",
                    icon: "info",
                    button: "好的",
                    text: "没有公告"
                });
            };

            function showAnnouncement() {
                const content = document.createElement("div");
                content.innerHTML = '<?php echo $conf['wzgg']; ?>';
                swal({
                    title: "公告",
                    icon: "success",
                    button: "好的",
                    content: content
                });
            }

            function setAnnouncementCookie() {
                const cookieTime = new Date();
                cookieTime.setTime(cookieTime.getTime() + (10 * 60 * 1000));
                $.cookie('op', false, {
                    expires: cookieTime
                });
            }
        });

        /* 跑马灯控制：悬停暂停、自动调整速度、尝试从后端拉取消息 */
        (function(){
            var $track = $('#marqueeTrack');
            var $wrap = $('.top-marquee');

            function adjustMarquee(){
                try{
                    var wrapW = $wrap.width() || window.innerWidth;
                    var trackW = $track.prop('scrollWidth') || $track.width();
                    // duration 基于文字长度，至少 10s，最多 60s
                    var duration = Math.max(10, Math.min(60, Math.round(trackW / 40)));
                    $track.css('animation-duration', duration + 's');
                }catch(e){}
            }

            function pauseMarquee(){ $track.css('animation-play-state','paused'); }
            function resumeMarquee(){ $track.css('animation-play-state','running'); }

            $wrap.on('mouseenter', pauseMarquee).on('mouseleave', resumeMarquee);
            $(window).on('resize', function(){ setTimeout(adjustMarquee, 120); });

            // 可选：从后端拉取跑马灯消息（接口不存在时会安静失败）
            async function loadMarqueeRemote(){
                try{
                    var res = await API.request('api/api.php?act=getmarquee', {} , { type: 'GET' });
                    if(res && (res.code==1 || res.code=="1") && res.msg){
                        var items = [];
                        if(Array.isArray(res.msg)) items = res.msg;
                        else if(typeof res.msg === 'string') items = [res.msg];
                        if(items.length>0){
                            var html = items.map(function(it){ return '<span class="marquee-item">'+it+'</span>'; }).join('');
                            // 为了连续滚动，把内容重复一次
                            $track.html(html + html);
                            setTimeout(adjustMarquee, 80);
                            return;
                        }
                    }
                }catch(e){/* ignore */}
                // fallback: 如果没有远程数据，确保速度正确
                setTimeout(adjustMarquee, 80);
            }

            // 初始化
            $(function(){
                // small delay to ensure fonts/images loaded
                setTimeout(function(){ adjustMarquee(); loadMarqueeRemote(); }, 150);
            });
        })();
    </script>
    <script>
        // Ensure modals/overlays (SweetAlert / layui layer) are styled darkly even if external CSS overrides persist.
        (function(){
            function styleModal(el){
                try{
                    // keep transparency so modal blends with page background
                    el.style.setProperty('background','transparent','important');
                    el.style.setProperty('color','#e6eef8','important');
                    el.style.setProperty('border','1px solid rgba(255,255,255,0.04)','important');
                    el.style.setProperty('box-shadow','0 12px 40px rgba(0,0,0,0.7)','important');
                    el.style.setProperty('backdrop-filter','blur(6px)','important');
                    el.style.setProperty('-webkit-backdrop-filter','blur(6px)','important');
                }catch(e){}
            }
            function styleOverlay(el){
                try{ el.style.setProperty('background-color','rgba(0,0,0,0.6)','important'); }catch(e){}
            }

            // Specifically adjust SweetAlert success icon ring so it looks correct on dark backgrounds
            function styleSweetSuccessIcon(root){
                try{
                    var rings = root.querySelectorAll && root.querySelectorAll('.swal-icon--success__ring');
                    if(rings && rings.length){
                        rings.forEach(function(r){
                            // apply dark-theme friendly ring
                            r.style.background = 'transparent';
                            r.style.border = '4px solid rgba(51,202,187,0.18)';
                            r.style.boxSizing = 'content-box';
                            r.style.boxShadow = '0 0 12px rgba(51,202,187,0.06)';
                            r.style.left = (r.style.left||'-4px');
                            r.style.top = (r.style.top||'-4px');
                            r.style.zIndex = '1';
                        });
                    }

                    // Hide or neutralize the white corner masks which were designed for a white background
                    var hides = root.querySelectorAll && root.querySelectorAll('.swal-icon--success__hide-corners');
                    if(hides && hides.length){
                        hides.forEach(function(h){
                            h.style.background = 'transparent';
                            h.style.display = 'none';
                        });
                    }

                    // Ensure success lines (the checkmark) stay above the ring
                    var lines = root.querySelectorAll && root.querySelectorAll('.swal-icon--success__line');
                    if(lines && lines.length){
                        lines.forEach(function(l){
                            l.style.zIndex = '3';
                            l.style.backgroundColor = 'rgba(51,202,187,0.95)';
                        });
                    }
                }catch(e){}
            }

            function applyOnce(node){
                if(node.classList){
                    if(node.classList.contains('sweet-alert')||node.classList.contains('swal-modal')||node.classList.contains('swal-overlay')){
                        styleModal(node);
                        styleSweetSuccessIcon(node);
                    }
                    if(node.classList.contains('sweet-overlay')) styleOverlay(node);
                    if(node.classList.contains('layui-layer')||node.classList.contains('layui-layer-content')||node.classList.contains('layui-layer-msg')) styleModal(node);
                }
            }

            var obs = new MutationObserver(function(muts){
                muts.forEach(function(m){
                    m.addedNodes && m.addedNodes.forEach(function(n){
                        if(n.nodeType!==1) return;
                        applyOnce(n);
                        try{ 
                            if(n.querySelectorAll){
                                n.querySelectorAll('.sweet-alert,.swal-modal,.swal-overlay,.layui-layer,.layui-layer-content,.layui-layer-msg').forEach(applyOnce);
                                // also handle success icons in case library inserts them deeper
                                n.querySelectorAll('.swal-icon--success__ring,.swal-icon--success__hide-corners,.swal-icon--success__line').forEach(function(dummy){
                                    // find closest modal root
                                    var root = dummy.closest('.swal-modal') || dummy.closest('.sweet-alert') || document;
                                    styleSweetSuccessIcon(root);
                                });
                            }
                        }catch(e){}
                    });
                });
            });
            obs.observe(document.documentElement||document.body, {childList:true, subtree:true});
            // initial pass
            try{ 
                document.querySelectorAll('.sweet-alert,.swal-modal,.sweet-overlay,.layui-layer,.layui-layer-content,.layui-layer-msg').forEach(applyOnce);
                // initial adjust for any success icons already present
                styleSweetSuccessIcon(document);
            }catch(e){}
        })();
    </script>
        <script>
            // Enforce rounded, frosted style for central .time results (handles inline styles / later CSS overrides)
            (function(){
                var enforcedRadius = '14px';
                var enforcedBg = 'rgba(11,18,32,0.72)';
                function enforce(node){
                    try{
                        if(!node || node.nodeType!==1) return;
                        if(node.matches && (node.matches('.time div') || node.matches('.msg-dark') || node.closest && node.closest('.time'))){
                            node.style.setProperty('border-radius', enforcedRadius, 'important');
                            node.style.setProperty('overflow', 'hidden', 'important');
                            node.style.setProperty('background', enforcedBg, 'important');
                            node.style.setProperty('border', '1px solid rgba(255,255,255,0.04)', 'important');
                            node.style.setProperty('box-shadow', '0 8px 30px rgba(2,6,23,0.45)', 'important');
                            node.style.setProperty('backdrop-filter', 'blur(6px)', 'important');
                            node.style.setProperty('-webkit-backdrop-filter', 'blur(6px)', 'important');
                            node.style.setProperty('color', 'var(--muted)', 'important');
                        }
                        // also ensure direct children don't draw white backgrounds beyond rounded corners
                        if(node.querySelectorAll){
                            node.querySelectorAll && node.querySelectorAll('.time div, .msg-dark').forEach(function(n){
                                n.style.setProperty('border-radius', enforcedRadius, 'important');
                                n.style.setProperty('overflow', 'hidden', 'important');
                                n.style.setProperty('background', enforcedBg, 'important');
                            });
                        }
                    }catch(e){}
                }

                var obs = new MutationObserver(function(muts){
                    muts.forEach(function(m){
                        m.addedNodes && m.addedNodes.forEach(function(n){
                            enforce(n);
                            if(n.querySelectorAll){
                                n.querySelectorAll('.time div, .msg-dark').forEach(enforce);
                            }
                        });
                        // attribute changes (inline style changes) on existing nodes
                        if(m.type === 'attributes' && m.target){
                            enforce(m.target);
                        }
                    });
                });

                // observe additions and attribute changes that might remove/override border-radius
                obs.observe(document.documentElement||document.body, { childList:true, subtree:true, attributes:true, attributeFilter:['style','class'] });

                // initial pass
                try{
                    document.querySelectorAll('.time div, .msg-dark').forEach(function(n){ enforce(n); });
                }catch(e){}
            })();
        </script>
        <style id="force-time-msg-style">
            /* Last-resort forced styles for the central result card and its pseudo-elements/children */
            .layui-container .time,
            .layui-container .time > div,
            .layui-container .time .msg-dark,
            .layui-container .time > div::before,
            .layui-container .time > div::after,
            .layui-container .time .msg-dark::before,
            .layui-container .time .msg-dark::after,
            .layui-container .time * {
                border-radius: 14px !important;
                overflow: hidden !important;
                background: rgba(11,18,32,0.72) !important;
                border: 1px solid rgba(255,255,255,0.04) !important;
                box-shadow: 0 8px 30px rgba(2,6,23,0.45) !important;
                color: var(--muted) !important;
                -webkit-backdrop-filter: blur(6px) !important;
                backdrop-filter: blur(6px) !important;
                -webkit-mask-image: none !important;
                mask-image: none !important;
                background-clip: padding-box !important;
            }
            /* Neutralize any white masks inserted by libraries inside the card */
            .layui-container .time .swal-icon--success__hide-corners,
            .layui-container .time .swal-icon--success:before,
            .layui-container .time .swal-icon--success:after {
                display: none !important;
                background: transparent !important;
            }
        </style>
</body>

</html>
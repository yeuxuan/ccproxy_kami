<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:57:38
 * @LastEditTime: 2025-01-06 11:31:27
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\install\index.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */
/**
 * 安装程序
 */
error_reporting(0);
define('VERSION', '2.0.0'); //版本号
session_start();
@header('Content-Type: text/html; charset=UTF-8');
include("../config.php");
$type = $type = isset($_GET['type']) ? addslashes($_GET['type']) : "";;
$a = 1;
function checkfunc($f, $m = false)
{
    if (function_exists($f)) {
        return '<font color="green">可用</font>';
    } else {
        if ($m == false) {
            return '<font color="black">不支持</font>';
        } else {
            return '<font color="red">不支持</font>';
        }
    }
}

// 清空文件夹函数和清空文件夹后删除空文件夹函数的处理
function deldir()
{
    // 设置需要删除的文件夹
    $path = "../install";
    //如果是目录则继续
    if (is_dir($path)) {
        //扫描一个文件夹内的所有文件夹和文件并返回数组
        $data = scandir($path);
        // todo 赋予文件夹权限
        chmod($path, 0777);
        foreach ($data as $val) {
            //排除目录中的.和..
            if ($val != "." && $val != "..") {
                // 1,如果是目录则递归子目录，继续操作
                if (is_dir($path . '/' . $val)) {
                    // 2,子目录中操作删除文件夹和文件
                    deldir($path . '/' . $val . '/');
                    // 3,目录清空后删除空文件夹
                    @rmdir($path . '/' . $val . '/');
                } else {
                    // 4,如果是文件直接删除
                    unlink($path . '/' . $val);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>一花CCPROXY系统安装模块</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="一花CCPROXY系统安装模块,一花CCPROXY系统安装模块,一花CCPROXY系统安装模块,免费,免费引流程序" name="description" />
    <meta content="Coderthemes" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="../favicon.ico">

    <!-- App css -->
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../assets/layui/css/layui.css" />

</head>
<style>
    /* 主题颜色变量定义 */
    :root {
        --primary-color: #2196F3;
        --secondary-color: #4CAF50;
        --accent-color: #FF4081;
        --hover-color: rgba(33, 150, 243, 0.1);
        --shadow-color: rgba(0, 0, 0, 0.1);
    }

    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        background: #2196F3;  /* 使用统一的主题色 */
        background: -webkit-linear-gradient(45deg, #2196F3, #00BCD4);
        background: linear-gradient(45deg, #2196F3, #00BCD4);
        box-shadow: 0 2px 5px rgba(33, 150, 243, 0.3);
    }

    .form-control:hover {
        border-color: var(--primary-color) !important;
        color: #333333;
    }

    .layui-form-onswitch {
        border-color: var(--secondary-color) !important;
        background-color: var(--secondary-color);
    }

    .card {
        animation: slideIn 0.8s ease-out;
        box-shadow: 0 4px 8px var(--shadow-color);
        transition: all 0.3s ease;
        background: linear-gradient(145deg, #ffffff, #f5f5f5);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(33, 150, 243, 0.15);
    }

    .btn {
        transition: all 0.3s ease;
        background: var(--primary-color);
        border: none;
    }

    .btn-success {
        background: var(--secondary-color);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(33, 150, 243, 0.2);
        opacity: 0.9;
    }

    .form-control {
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
    }

    .form-control:focus {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(33, 150, 243, 0.1);
        border-color: var(--primary-color);
    }

    .table tbody tr:hover {
        background-color: var(--hover-color);
        transform: translateX(5px);
    }

    /* 成功图标颜色 */
    .text-success {
        color: var(--secondary-color) !important;
    }

    /* 进度条颜色 */
    .progress-bar {
        background: linear-gradient(45deg, #2196F3, #00BCD4);
    }

    /* 错误提示颜色 */
    .text-danger {
        color: var(--accent-color) !important;
    }

    /* 保持其他动画相关的CSS不变 */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes checkmark {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* 修改开关按钮样式和动画 */
    .layui-form-switch {
        min-width: 54px;
        height: 24px;
        line-height: 24px;
        border-radius: 12px;
        border-color: #e0e0e0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* 使用更平滑的过渡曲线 */
        padding: 0 5px 0 25px;
        margin-top: 0;
        cursor: pointer;
        overflow: hidden; /* 防止内容溢出 */
    }

    .layui-form-switch i {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        top: 1px;
        left: 2px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* 添加阴影效果 */
    }

    .layui-form-switch em {
        font-size: 12px;
        margin-right: 5px;
        color: #999;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0.8; /* 稍微降低文字透明度 */
    }

    /* 开关打开状态 */
    .layui-form-onswitch {
        border-color: var(--primary-color) !important;
        background-color: var(--primary-color);
        padding: 0 25px 0 5px;
    }

    .layui-form-onswitch i {
        left: auto;
        right: 2px;
        background-color: #fff;
        transform: scale(1.1); /* 开启状态下圆点稍微放大 */
    }

    .layui-form-onswitch em {
        color: rgba(255, 255, 255, 0.9); /* 开启状态下文字颜色 */
    }

    /* 悬浮效果 */
    .layui-form-switch:hover {
        border-color: var(--primary-color);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.2);
    }

    .layui-form-switch:hover i {
        transform: scale(1.05); /* 悬浮时圆点稍微放大 */
    }

    /* 点击效果 */
    .layui-form-switch:active i {
        transform: scale(0.95); /* 点击时圆点稍微缩小 */
        transition: all 0.1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .layui-form-onswitch:active i {
        transform: scale(1.05); /* 开启状态下点击效果 */
    }

    /* 开关背景过渡效果 */
    .layui-form-switch::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--primary-color);
        opacity: 0;
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
    }

    .layui-form-onswitch::before {
        opacity: 1;
    }

    /* 调整协议组中开关的位置 */
    .protocol-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 15px;   /* 减小内边距 */
        background: rgba(33, 150, 243, 0.05);
        border-radius: 8px;
        margin: 10px 0;
        min-height: 45px;     /* 减小最小高度 */
    }

    .switch-wrapper {
        margin-left: 15px;    /* 减小左边距 */
        display: flex;        /* 使用flex布局 */
        align-items: center;  /* 垂直居中 */
    }

    /* 开关悬浮效果 */
    .layui-form-switch:hover {
        border-color: var(--primary-color);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.2);
    }

    /* 安装协议链接美化 */
    .protocol-link {
        color: var(--primary-color) !important;
        text-decoration: none;
        position: relative;
        padding-bottom: 2px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .protocol-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: var(--primary-color);
        transition: all 0.3s ease;
    }

    .protocol-link:hover {
        color: var(--accent-color) !important;
    }

    .protocol-link:hover::after {
        width: 100%;
    }

    /* 按钮美化 */
    .btn {
        padding: 8px 20px;
        border-radius: 50px;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
    }

    .btn:hover::after {
        width: 200px;
        height: 200px;
    }

    .btn-primary {
        background: linear-gradient(45deg, var(--primary-color), #1976D2);
    }

    .btn-success {
        background: linear-gradient(45deg, var(--secondary-color), #388E3C);
    }

    /* 协议开关组样式 */
    .protocol-group {
        display: flex;
        align-items: center;
        padding: 15px;
        background: rgba(33, 150, 243, 0.05);
        border-radius: 10px;
        margin: 15px 0;
    }

    .protocol-group label {
        margin-bottom: 0;
        margin-right: 10px;
    }

    /* 添加脉冲动画提示用户注意 */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(33, 150, 243, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(33, 150, 243, 0);
        }
    }

    .protocol-group {
        animation: pulse 2s infinite;
    }
</style>

<body>

    <!-- Begin page -->
    <div class="wrapper">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row mt-4 text-center">
                    <div class="col-xl-6" style="margin:auto">
                        <div class="card">
                            <?php if ($type == "installok") {

                                deldir();

                            ?>
                                <div class="card-body">

                                    <h2 class="header-title mb-3 text-success font-18 font-weight-light">一花CCPROXY在线安装引导程序</h2>

                                    <div id="progressbarwizard">
                                        <ul class="nav nav-pills nav-justified form-wizard-header mb-3 ">
                                            <li class="nav-item">
                                                <a href="#finish-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                                    <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline font-weight-light">安装完成</span>
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content b-0 mb-0">

                                            <div id="bar" class="progress mb-3" style="height: 10px;">
                                                <div class="bar progress-bar progress-bar-striped progress-bar-animated bg-success"></div>
                                            </div>
                                            <div class="tab-pane active" id="finish-2">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="text-center">
                                                            <h2 class="mt-0 text-success"><i class="mdi mdi-check-all"></i>
                                                            </h2>
                                                            <h3 class="mt-2 text-success">恭喜你</h3>

                                                            <p class="w-75 mb-2 mt-2 mx-auto">本程序已经成功安装到您的服务器上！</p>
                                                            <p class="w-75 mb-2 mt-2 mx-auto">默认后台为：sub_admin 账号：admin 密码：123456</p>
                                                            <p class="w-75 mb-2 mt-2 mx-auto">官方QQ群(o´ω`o)ﾉ：
                                                                <font color="red" class="font-18"><a href="https://jq.qq.com/?_wv=1027&k=N4E82kgw">点击加群</a></font>，关注可了解更多资讯！
                                                            </p>

                                                            <p class="w-75 mb-2 mt-2 mx-auto">
                                                                当前程序版本：V <?= VERSION ?></p>
                                                            <!-- <p class="w-75 mb-2 mt-2 mx-auto">点击<a
                                                                    href="http://docs.api.ln.cn/" target="_blank"
                                                                    style="color: turquoise">这里</a>进入网页查看操作文档</p> -->
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline mb-0 mt-2 wizard text-center">
                                                    <li class=" list-inline-item">
                                                        <a href="../index.php" target="_blank">
                                                            <button type="button" class="btn btn-primary">打开首页</button>
                                                        </a>
                                                        <a href="../sub_admin/" target="_blank">
                                                            <button type="button" class="btn btn-success">进入后台</button>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div> <!-- tab-content -->
                                    </div> <!-- end #progressbarwizard-->
                                </div> <!-- end card-body -->
                            <?php } else { ?>
                                <div class="card-body">

                                    <h2 class="header-title mb-3 text-success font-18 font-weight-light">一花CCPROXY在线安装引导程序</h2>

                                    <div id="progressbarwizard">
                                        <ul class="nav nav-pills nav-justified form-wizard-header mb-3 ">
                                            <li class="nav-item">
                                                <a href="#account-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                    <i class="mdi mdi-account-circle mr-1"></i>
                                                    <span class="d-none d-sm-inline font-weight-light">环境检测</span>
                                                </a>
                                            </li>
                                            <?php if (!file_exists("../install.lock")) { ?>
                                                <li class="nav-item">
                                                    <a style="<?php
                                                                if (!(version_compare(PHP_VERSION, '7.3', '>'))) {
                                                                    echo 'pointer-events: none;';
                                                                }
                                                                ?>" href="#profile-tab-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                        <i class="mdi mdi-face-profile mr-1"></i>
                                                        <span class="d-none d-sm-inline font-weight-light">数据填写</span>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>

                                        <div class="tab-content b-0 mb-0">

                                            <div id="bar" class="progress mb-3" style="height: 10px;">
                                                <div class="bar progress-bar progress-bar-striped progress-bar-animated bg-danger"></div>
                                            </div>

                                            <div class="tab-pane" id="account-2">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-body">

                                                                <h4 class="header-title font-weight-light">环境检测</h4>
                                                                <p class="w-75 mb-2 mt-2 mx-auto">官方QQ群(o´ω`o)ﾉ：
                                                                    <font color="red" class="font-18"><a href="https://jq.qq.com/?_wv=1027&k=N4E82kgw">点击加群</a></font>，关注可了解更多资讯！
                                                                </p>
                                                                <p class="text-muted font-14 mb-3">
                                                                    <code>为了更好的使用程序,下列环境须支持才可正常运行</code>.
                                                                </p>

                                                                <div class="table-responsive-sm">
                                                                    <table class="table table-striped mb-0">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>函数检测</th>
                                                                                <th>需求</th>
                                                                                <th>当前</th>
                                                                                <th>用途</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>PHP 7.0+</td>
                                                                                <td>必须</td>
                                                                                <td>
                                                                                    <?php
                                                                                    if (!(version_compare(PHP_VERSION, '7.3', '>')) || !(version_compare(PHP_VERSION, '7.5', '<'))) {
                                                                                        $a = 2;
                                                                                    }
                                                                                    echo (version_compare(PHP_VERSION, '7.3', '>')) && (version_compare(PHP_VERSION, '7.5', '<')) ? '<font color="green">' . PHP_VERSION . '</font>' : '<font color="red">' . PHP_VERSION . '</font>'; ?>
                                                                                </td>
                                                                                <td>
                                                                                    PHP版本支持
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>curl_exec()</td>
                                                                                <td>必须</td>
                                                                                <td><?php echo checkfunc('curl_exec', true); ?></td>
                                                                                <td>抓取网页</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>file_get_contents()</td>
                                                                                <td>必须</td>
                                                                                <td><?php echo checkfunc('file_get_contents', true); ?></td>
                                                                                <td>读取文件</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>session</td>
                                                                                <td>必须</td>
                                                                                <td><?php $_SESSION['checksession'] = 1;
                                                                                    echo $_SESSION['checksession'] == 1 ? '<font color="green">可用</font>' : '<font color="red">不支持</font>'; ?></td>
                                                                                <td>PHP必备功能</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div> <!-- end table-responsive-->
                                                                <?php if (file_exists("../install.lock")) { ?>
                                                                    <ul class="list-inline mb-0 wizard">
                                                                        <li class="list-inline-item float-right">
                                                                            <a href="#">
                                                                                <button type="button" class="btn btn-danger" onclick="layer.alert('检测到您已经安装过程序<br>请先删除install目录下的<font color=red>../install.lock</font>文件再来安装!',{icon:2,title:'警告'})">
                                                                                    进行下一步
                                                                                </button>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                <?php } else { ?>
                                                                    <ul class="list-inline mb-0 wizard">
                                                                        <li class="next list-inline-item float-right">
                                                                            <a href="#">
                                                                                <button type="button" class="btn btn-success" <?= $a == 1 ? '' : 'disabled=""'; ?>>
                                                                                    进入下一步
                                                                                </button>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                            </div>
                                            <?php if (!file_exists("../install.lock")) { ?>
                                                <div class="tab-pane" id="profile-tab-2">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <span class="text-center mb-2 d-block">可手动前往文件：<code>config.php</code> 配置数据!</span>
                                                            <form class="layui-form form-horizontal">
                                                                <div class="form-group row mb-3">
                                                                    <label class="col-md-3 col-form-label font-weight-light" for="host">数据库地址</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="host" name="host" class="form-control" lay-verify="required" value="<?= $dbconfig["host"] ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row mb-3">
                                                                    <label class="col-md-3 col-form-label font-weight-light" for="port">数据库端口</label>
                                                                    <div class="col-md-9">
                                                                        <input type="number" id="port" name="port" class="form-control" lay-verify="required" value="<?= $dbconfig["port"] ?>">
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row mb-3">
                                                                    <label class="col-md-3 col-form-label font-weight-light" for="user">数据库用户名</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="user" name="user" class="form-control" lay-verify="required" value="<?= $dbconfig["user"] ?>">
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row mb-3">
                                                                    <label class="col-md-3 col-form-label font-weight-light" for="pwd">数据库密码</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="pwd" name="pwd" class="form-control" lay-verify="required" value="<?= $dbconfig["pwd"] ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row mb-3">
                                                                    <label class="col-md-3 col-form-label font-weight-light" for="token">数据库名称</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="dbname" name="dbname" class="form-control" lay-verify="required" value="<?= $dbconfig["dbname"] ?>" placeholder="请填写您数据库的名称">
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row mb-3">
                                                                    <label class="col-md-3 col-form-label font-weight-light" for="url">当前程序版本</label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="versions" name="versions" class="form-control" lay-verify="required" value="V <?= VERSION ?>" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row mb-3">
                                                                    <div class="col-md-12">
                                                                        <div class="protocol-group">
                                                                            <label class="col-form-label font-weight-light" for="switch">
                                                                                <a href="disclaimer.html" target="_blank" class="protocol-link">
                                                                                    <i class="mdi mdi-file-document-outline mr-1"></i>安装协议
                                                                                </a>
                                                                            </label>
                                                                            <div class="switch-wrapper">
                                                                                <input type="checkbox" name="switch" lay-verify="required" lay-skin="switch" lay-filter="xy">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <ul class="list-inline mb-0 wizard">
                                                                    <li class="list-inline-item float-right" id="install">
                                                                        <a href="#">
                                                                            <button type="submit" lay-submit lay-filter="install" class="btn btn-success">开始安装程序
                                                                            </button>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </form>
                                                        </div> <!-- end col -->
                                                    </div> <!-- end row -->
                                                </div>
                                            <?php } ?>
                                        </div> <!-- tab-content -->
                                    </div> <!-- end #progressbarwizard-->
                                </div> <!-- end card-body -->
                            <?php } ?>
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div> <!-- container -->
        </div> <!-- content -->
    </div>
    <!-- END wrapper -->
    <div class="rightbar-overlay"></div>
    <!-- /Right-bar -->
    <!-- App js -->
    <script src="../assets/js/app.min.js"></script>
    <script src="../assets/layui/layui.js"></script>
    <!-- end demo js-->
    <?php if (!file_exists("../install.lock") && empty($type)) { ?>
        <script>
            $(document).ready(function() {
                "use strict";
                $("#basicwizard").bootstrapWizard(), $("#progressbarwizard").bootstrapWizard({
                    onTabShow: function(t, r, a) {
                        var o = (a + 1) / r.find("li").length * 100;
                        $("#progressbarwizard").find(".bar").css({
                            width: o + "%"
                        })
                    }
                }), $("#btnwizard").bootstrapWizard({
                    nextSelector: ".button-next",
                    previousSelector: ".button-previous",
                    firstSelector: ".button-first",
                    lastSelector: ".button-last"
                }), $("#rootwizard").bootstrapWizard({
                    onNext: function(t, r, a) {
                        var o = $($(t).data("targetForm"));
                        if (o && (o.addClass("was-validated"), !1 === o[0].checkValidity())) return event.preventDefault(), event.stopPropagation(), !1
                    }
                });
            });
            layui.use('form', function() {
                var form = layui.form;


                form.on("switch(xy)", function(obj) {
                    console.log(obj);
                    if (obj.value == "on") {
                        xy();
                    }

                });

                function xy() {
                    // 获取设备屏幕宽度
                    var width = document.documentElement.clientWidth;
                    // 根据屏幕宽度设置不同的弹窗大小
                    var areaValue = width <= 768 ? ['90%', '80%'] : ['500px', '500px'];
                    
                    layer.open({
                        type: 2,
                        title: "一花CCPROXY系统使用协议",
                        area: areaValue,
                        maxmin: false,
                        closeBtn: 0,
                        content: "disclaimer.html?v=20201111001"
                    });
                }


                form.on('submit(install)', function(data) {
                    if (data.field['switch'] != 'on') {
                        layer.alert('请先同意程序安装协议(免责声明)<br>若不同意则无法安装程序!', {
                            title: '温馨提示',
                            icon: 2
                        });
                        return false;
                    }
                    var index = layer.msg('正在安装中,请稍后.', {
                        icon: 16,
                        time: 5000
                    });
                    $.post('ajax.php?act=1', data.field, function(res) {
                        if (res.code == 1) {
                            layer.close(index);
                            layer.alert(res.msg, {
                                icon: 1,
                                title: '成功提醒',
                                end: function(layero, index) {
                                    location.href = '?type=installok';
                                }
                            });
                        } else if (res.code == -2) {
                            layer.close(index);

                            layer.confirm('检测到已经安装，是否全新安装？', {
                                btn: ['确定', '跳过'],
                                icon: 3,
                                title: "提示",
                                btn2: function(index, layero) {
                                    console.log("跳过");
                                    
                                    $.post('ajax.php?act=3',function (res){
                                        
                                        if (res.code == 1) {
                                        layer.close(index);
                                        layer.alert(res.msg, {
                                            icon: 1,
                                            title: '成功提醒',
                                            end: function(layero, index) {
                                                location.href = '?type=installok';
                                            }
                                        });
                                    } else {
                                        layer.close(index);
                                        layer.alert(res.msg, {
                                            icon: 2,
                                            title: '错误编号：' + res.code
                                        });
                                    }

                                    });
                                }
                            }, function(index, layero) {
                                console.log("确定");

                                $.post('ajax.php?act=2', data.field, function(res) {

                                    if (res.code == 1) {
                                        layer.close(index);
                                        layer.alert(res.msg, {
                                            icon: 1,
                                            title: '成功提醒',
                                            end: function(layero, index) {
                                                location.href = '?type=installok';
                                            }
                                        });
                                    } else {
                                        layer.close(index);
                                        layer.alert(res.msg, {
                                            icon: 2,
                                            title: '错误编号：' + res.code
                                        });
                                    }

                                }).fail(function() {
                                    layer.close(index);
                                    layer.alert("请求没有响应发生错误", {
                                        icon: 2,
                                        title: '没有响应'
                                    });
                                });

                            });

                            // layer.alert("安装失败，是否清除数据库重新安装？", {
                            //     icon: 2,
                            //     title: '失败提醒',
                            //     end: function(layero, index) {



                            //     }
                            // });



                        } else {
                            layer.close(index);
                            layer.alert(res.msg, {
                                icon: 2,
                                title: '错误编号：' + res.code
                            });
                        }
                    }).fail(function() {
                        layer.close(index);
                        layer.alert("请求没有响应发生错误", {
                            icon: 2,
                            title: '没有响应'
                        });
                    });
                    return false;
                });
            });

            $(document).ready(function() {
                // 表格行依次显示动画
                $('.table tbody tr').each(function(index) {
                    $(this).css({
                        'animation': `slideIn ${0.3 + index * 0.1}s ease-out`
                    });
                });

                // 进度条动画优化
                $('.progress-bar').css('transition', 'width 0.6s ease');
                
                // 表单提交成功后的动画
                if($('.mdi-check-all').length) {
                    setTimeout(function() {
                        $('.mdi-check-all').parent().addClass('animated bounce');
                    }, 500);
                }
            });
        </script>
    <?php }else{?>
<script> 
         $(document).ready(function() {
                "use strict";
                $("#basicwizard").bootstrapWizard(), $("#progressbarwizard").bootstrapWizard({
                    onTabShow: function(t, r, a) {
                        var o = (a + 1) / r.find("li").length * 100;
                        $("#progressbarwizard").find(".bar").css({
                            width: o + "%"
                        })
                    }
                }), $("#btnwizard").bootstrapWizard({
                    nextSelector: ".button-next",
                    previousSelector: ".button-previous",
                    firstSelector: ".button-first",
                    lastSelector: ".button-last"
                }), $("#rootwizard").bootstrapWizard({
                    onNext: function(t, r, a) {
                        var o = $($(t).data("targetForm"));
                        if (o && (o.addClass("was-validated"), !1 === o[0].checkValidity())) return event.preventDefault(), event.stopPropagation(), !1
                    }
                });
            });
    alert("已经安装过了，请先删除根目录下的install.lock");
</script>
<?php }?>
</body>

</html>
<?php
/**
 * 安装程序
 */
error_reporting(0);
session_start();
@header('Content-Type: text/html; charset=UTF-8');
include("../config.php");
$type = $type = isset($_GET['type'])?$_GET['type']:"";;
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
    <meta charset="utf-8"/>
    <title>一花CCPROXY系统安装模块</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="一花CCPROXY系统安装模块,一花CCPROXY系统安装模块,一花CCPROXY系统安装模块,免费,免费引流程序" name="description"/>
    <meta content="Coderthemes" name="author"/>
    <!-- App favicon -->
    <link rel="shortcut icon" href="../favicon.ico">

    <!-- App css -->
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../assets/layui/css/layui.css"/>

</head>
<style>
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        background: #12c2e9; /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #f64f59, #c471ed, #12c2e9); /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #f64f59, #c471ed, #12c2e9); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
    }
    .form-control:hover {
	border-color: #33cabb !important;
	color: #333333;
}
.layui-form-onswitch{
    /* width:100px; */
    border-color: #33cabb !important;
    background-color: #33cabb;
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
                        <?php if ($type=="installok") { 
                            
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
                                                            当前程序版本：V 1.1</p>
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
                                                    if ((!version_compare(PHP_VERSION, '5.6', '>'))||(!version_compare(PHP_VERSION, '8.0', '<'))) {
                                                        echo 'pointer-events: none;';
                                                    }
                                                ?>" href="#profile-tab-2" data-toggle="tab"
                                                   class="nav-link rounded-0 pt-2 pb-2">
                                                    <i class="mdi mdi-face-profile mr-1"></i>
                                                    <span class="d-none d-sm-inline font-weight-light">数据填写</span>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>

                                    <div class="tab-content b-0 mb-0">

                                        <div id="bar" class="progress mb-3" style="height: 10px;">
                                            <div
                                                    class="bar progress-bar progress-bar-striped progress-bar-animated bg-danger"></div>
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
                                                                            if ((!version_compare(PHP_VERSION, '5.6', '>'))||(!version_compare(PHP_VERSION, '8.0', '<'))) {
                                                                                $a = 2;
                                                                            }
                                                                            echo version_compare(PHP_VERSION, '5.6', '>')&&version_compare(PHP_VERSION, '8.0', '<') ? '<font color="green">' . PHP_VERSION . '</font>' : '<font color="red">' . PHP_VERSION . '</font>'; ?>
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
                                                                            <button type="button" class="btn btn-danger"
                                                                                    onclick="layer.alert('检测到您已经安装过程序<br>请先删除install目录下的<font color=red>../install.lock</font>文件再来安装!',{icon:2,title:'警告'})">
                                                                                进行下一步
                                                                            </button>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            <?php } else { ?>
                                                                <ul class="list-inline mb-0 wizard">
                                                                    <li class="next list-inline-item float-right">
                                                                        <a href="#">
                                                                            <button type="button"
                                                                                    class="btn btn-success"
                                                                                    <?= $a == 1 ? '' : 'disabled=""'; ?>>
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
                                                                <label class="col-md-3 col-form-label font-weight-light"
                                                                       for="host">数据库地址</label>
                                                                <div class="col-md-9">
                                                                    <input type="text" id="host" name="host"
                                                                           class="form-control" lay-verify="required"
                                                                           value="<?= $dbconfig["host"] ?>">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row mb-3">
                                                                <label class="col-md-3 col-form-label font-weight-light"
                                                                       for="port">数据库端口</label>
                                                                <div class="col-md-9">
                                                                    <input type="number" id="port" name="port"
                                                                           class="form-control" lay-verify="required"
                                                                           value="<?= $dbconfig["port"] ?>">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row mb-3">
                                                                <label class="col-md-3 col-form-label font-weight-light"
                                                                       for="user">数据库用户名</label>
                                                                <div class="col-md-9">
                                                                    <input type="text" id="user" name="user"
                                                                           class="form-control" lay-verify="required"
                                                                           value="<?= $dbconfig["user"] ?>">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row mb-3">
                                                                <label class="col-md-3 col-form-label font-weight-light"
                                                                       for="pwd">数据库密码</label>
                                                                <div class="col-md-9">
                                                                    <input type="text" id="pwd" name="pwd"
                                                                           class="form-control" lay-verify="required"
                                                                           value="<?= $dbconfig["pwd"] ?>">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row mb-3">
                                                                <label class="col-md-3 col-form-label font-weight-light"
                                                                       for="token">数据库名称</label>
                                                                <div class="col-md-9">
                                                                    <input type="text" id="dbname" name="dbname"
                                                                           class="form-control" lay-verify="required"
                                                                           value="<?= $dbconfig["dbname"] ?>"
                                                                           placeholder="请填写您数据库的名称">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row mb-3">
                                                                <label class="col-md-3 col-form-label font-weight-light"
                                                                       for="url">当前程序版本</label>
                                                                <div class="col-md-9">
                                                                    <input type="text" id="versions" name="versions"
                                                                           class="form-control" lay-verify="required"
                                                                           value="V 1.3"
                                                                           readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row mb-3">
                                                                <label class="col-md-3 col-form-label font-weight-light"
                                                                       for="switch"><a href="disclaimer.php"
                                                                                       target="_blank"
                                                                                       style="color: red">安装协议</a></label>
                                                                <div class="col-md-9">
                                                                    <input type="checkbox" name="switch" lay-verify="required" lay-skin="switch">
                                                                </div>
                                                            </div>
                                                            <ul class="list-inline mb-0 wizard">
                                                                <li class="list-inline-item float-right" id="install">
                                                                    <a href="#">
                                                                        <button type="submit" lay-submit
                                                                                lay-filter="install"
                                                                                class="btn btn-success">开始安装程序
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
        $(document).ready(function(){"use strict";$("#basicwizard").bootstrapWizard(),$("#progressbarwizard").bootstrapWizard({onTabShow:function(t,r,a){var o=(a+1)/r.find("li").length*100;$("#progressbarwizard").find(".bar").css({width:o+"%"})}}),$("#btnwizard").bootstrapWizard({nextSelector:".button-next",previousSelector:".button-previous",firstSelector:".button-first",lastSelector:".button-last"}),$("#rootwizard").bootstrapWizard({onNext:function(t,r,a){var o=$($(t).data("targetForm"));if(o&&(o.addClass("was-validated"),!1===o[0].checkValidity()))return event.preventDefault(),event.stopPropagation(),!1}}); });
        layui.use('form', function () {
            var form = layui.form;
            form.on('submit(install)', function (data) {
                if (data.field['switch'] != 'on') {
                    layer.alert('请先同意程序安装协议(免责声明)<br>若不同意则无法安装程序!', {title: '温馨提示', icon: 2});
                    return false;
                }
                var index = layer.msg('正在安装中,请稍后.', {icon: 16, time: 5000});
                $.post('ajax.php?act=1', data.field, function (res) {
                    if (res.code == 1) {
                        layer.close(index);
                        layer.alert(res.msg, {
                            icon: 1, title: '成功提醒', end: function (layero, index) {
                                location.href = '?type=installok';
                            }
                        });
                    } else {
                        layer.close(index);
                        layer.alert(res.msg, {icon: 2, title: '错误编号：' + res.code});
                    }
                });
                return false;
            });
        });
    </script>
<?php } ?>
</body>
</html>
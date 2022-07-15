<?php
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
        .time {
            width: 80%;
            margin: 0 auto;
            text-align: center;
        }

        .img img {
            border-radius: 10px;
            background-color: #fff;
        }

        /* .layui-edge {
            right: 70px!important;
        } */
    </style>
</head>

<body>
    <div class="layui-container">
        <!-- logo部分 -->
        <div class="layui-logo">
            <div class="layui-row">
                <div class="layui-card layui-col-xs12">
                    <div class="wz-title">
                        <h1>一花端口</h1>
                    </div>
                    <div class="img">
                        <img src="<?php echo $subconf['img']; ?>" alt="logo">
                    </div>
                    <div class="layui-col-xs-12 cer">
                        <a class="buwz" style="color:white" onclick="<?php echo $subconf['ggswitch'] == 1 ? "showgg()" : "notgg()"; ?>">
                            <div class="layui layui-btn layui-btn-danger">公告</div>
                        </a>
                        <a class="buwz" style="color:white" href="<?php echo $subconf['kf']; ?>">
                            <div class="layui layui-btn layui-btn-normal">客服</div>
                        </a>
                        <a class="buwz" style="color:white" href="<?php echo $subconf['pan']; ?>">
                            <div class="layui layui-btn layui-btn-checked">网盘</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- 面板部分 -->
        <div class="main">
            <div style="margin: 0;" class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                <ul class="layui-tab-title">
                    <li class="layui-this">卡密充值</li>
                    <li>用户注册</li>
                    <li>用户查询</li>
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
                            <!-- <span id="time">到期时间：9999:99:99 99:99:99</span> -->
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
    <!-- <script src="https://cdn.staticfile.org/jquery-cookie/1.4.1/jquery.cookie.min.js"></script> -->
    <script src="./assets/js/sweetalert.min.js"></script>
    <script type="text/javascript">
        layui.use(["jquery", "table", "laydate", "form", "upload", "element"], function() {

            var $ = layui.$,
                table = layui.table,
                laydate = layui.laydate,
                form = layui.form,
                upload = layui.upload,
                element = layui.element;

            select();

            function select() {
                $.ajax({
                    url: "api/api.php?act=gethostapp",
                    type: "POST",
                    dataType: "json",
                    timeout: 30000,
                    success: function(data) {
                        if (data.code == "1") {
                            var elem = $("[name=app]");
                            for (var key in data.msg) {
                                // console.log(elem2);
                                var json = data.msg[key],
                                    appcode = json.appcode,
                                    appname = json.appname;
                                item = '<option value="' + appcode + '">' + appname + '</option>';
                                elem.append(item);
                            }
                            form.render("select");
                        }
                    },
                    error: function(data) {
                        // console.log(data);
                        layer.msg("获取应用失败", {
                            icon: 5
                        });
                    }
                });
            }


        });
        $(function() {
            $("#pay").click(function() {
                var user = $("#pay-user").val();
                var code = $("#pay-code").val();
                if (user == "") {
                    return Qmsg.info("账号不能为空！")
                }
                if (code == "") {
                    return Qmsg.info("卡密不能为空")
                }
                if (user.length < 3) {
                    return Qmsg.info("账号长度不得小于6位")
                }
                if (code.length < 1) {
                    return Qmsg.info("卡密长度最小为1位")
                }
                if (code.length > 128) {
                    return Qmsg.info("卡密长度最大为128位")
                }
                // $.post("",{})
                $.ajax({
                    url: "api/cpproxy.php?type=update",
                    type: "POST",
                    dataType: "json",
                    data: {
                        'user': user,
                        'code': code
                        // server: $("[name=server]").val()
                    },
                    timeout: 30000,
                    beforeSend: function() {
                        layer.msg("正在充值", {
                            icon: 16,
                            shade: 0.05,
                            time: false
                        });
                    },
                    success: function(data) {
                        if (data.code == 1) {
                            layer.msg("充值成功", {
                                icon: 1
                            });
                            // $(".time").eq(0).html(data.msg)
                            Qmsg.success("充值成功", {
                                html: true,
                            });
                        } else if (data.code == -1) {
                            layer.msg(data.msg, {
                                icon: 5
                            });
                        } else if (data.code == -2) {
                            layer.msg(data.msg, {
                                icon: 5
                            });
                            Qmsg.error(data.msg);
                        } else if (data.code == -3) {
                            layer.msg(data.msg, {
                                icon: 5
                            });
                            Qmsg.error(data.msg, {
                                html: true,
                            });
                        } else {
                            layer.msg("未知错误", {
                                icon: 5
                            });
                            Qmsg.error("未知错误");
                        }
                    },
                    error: function(data) {
                        // var obj = eval(data);
                        // console.log(obj)
                        layer.alert("充值失败", {
                            icon: 2
                        });
                    }
                });
            });

            function checkUsername(obj) {
                // console.log(obj)
                //正则表达式
                var reg = new RegExp("^[A-Za-z0-9]+$");
                //获取输入框中的值
                // var username = document.getElementById("username").value.trim();
                // //判断输入框中有内容
                if (!reg.test(obj)) {
                    return true;
                    // Qmsg.info("请输入数字和英文！")
                    //输入非法字符，清空输入框
                    //$("#username").val("");
                } else {
                    return false;
                }

            }
            console.log($(".layui-edge"));
            $("#registed").click(function() {
                var user = $("#reg-user").val().trim();
                var pwd = $("#reg-pwd").val().trim();
                var code = $("#reg-code").val().trim();
                if (user == "") {
                    return Qmsg.info("账号不能为空")
                }
                if (pwd == "") {
                    return Qmsg.info("密码不能为空")
                }
                if (code == "") {
                    return Qmsg.info("卡密不能为空")
                }
                if (user.length < 5) {
                    return Qmsg.info("账号长度不得小于5位")
                }
                if (pwd.length < 5) {
                    return Qmsg.info("密码要大于5位")
                }
                if (code.length < 15) {
                    return Qmsg.info("卡密长度最小为16位")
                }
                if (checkUsername(user)) {
                    return Qmsg.info("账号请输入数字和英文！")
                }
                if (checkUsername(pwd)) {
                    return Qmsg.info("密码请输入数字和英文！")
                }
                var pattern = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z_]{5,16}$/
                if (!pattern.test(pwd)) {
                    return Qmsg.info("密码可以包含数字、字母、下划线，并且要同时含有数字和字母，且长度要在8-16位之间!")
                }
                //console.log(pattern.test(pwd));
                $.ajax({
                    url: "api/cpproxy.php?type=insert",
                    type: "POST",
                    dataType: "json",
                    data: {
                        'user': user,
                        'pwd': pwd,
                        'code': code
                        // server: $("[name=server]").val()
                    },
                    timeout: 30000,
                    beforeSend: function() {
                        $("#registed").prop("disabled",true);
                        layer.msg("正在注册", {
                            icon: 16,
                            shade: 0.05,
                            time: false
                        });
                    },
                    success: function(data) {
                        if (data.code == 1) {
                            layer.msg(data.msg, {
                                icon: 1
                            });
                            // $(".time").eq(0).html(data.msg)
                            Qmsg.success(data.msg, {
                                html: true,
                            });
                        } else if (data.code == -1) {
                            layer.msg(data.msg, {
                                icon: 5
                            });
                            Qmsg.error(data.msg);
                        } else {
                            layer.msg(data.msg, {
                                icon: 5
                            });
                            Qmsg.error(data.msg, {
                                html: true,
                            });

                        }
                        $("#registed").prop("disabled",false);
                    },
                    error: function(data) {
                        // var obj = eval(data);
                        // console.log(obj)
                        layer.alert("注册失败", {
                            icon: 2
                        });
                        $("#registed").prop("disabled",false);
                    }
                });
            });
            $("#check").click(function() {
                var user = $("#check-user").val();
                var checked = $("#sel option:checked").val();
                if (checked == "") {
                    return Qmsg.info("请选择一个应用")
                }
                if (user == "") {
                    return Qmsg.info("账号不能为空")
                }
                if (user.length < 5) {
                    return Qmsg.info("账号长度不得小于6位")
                }
                // $.post("",{})
                $.ajax({
                    url: "api/cpproxy.php?type=query",
                    type: "POST",
                    dataType: "json",
                    data: {
                        'user': user,
                        'appcode': checked,
                        // server: $("[name=server]").val()
                    },
                    timeout: 30000,
                    beforeSend: function() {
                        layer.msg("正在查询", {
                            icon: 16,
                            shade: 0.05,
                            time: false,
                        });
                    },
                    success: function(data) {
                        if (data.code == 1) {
                            layer.msg("查询成功", {
                                icon: 1
                            });
                            $(".time").eq(0).html(
                                "<div style='padding: 10px; border: 1px solid #c3e6cb; border-radius: 2px; color: #155724; font-size: 12px; line-height: 2em; background-color: #33cabb; margin-bottom: 10px; '><b>" +
                                data.msg + "</b></div>")
                            Qmsg.success(data.msg, {
                                html: true,
                            });
                        } else if (data.code == -3) {
                            layer.msg(data.msg, {
                                icon: 5
                            });
                            Qmsg.error(data.msg, {
                                html: true,
                            });
                        } else {
                            layer.msg("未知错误", {
                                icon: 5
                            });
                            Qmsg.error("未知错误");
                        }
                    },
                    error: function(data) {
                        // var obj = eval(data);
                        // console.log(obj)
                        $(".time").eq(0).html("")
                        layer.alert("查询失败", {
                            icon: 2
                        });

                    }
                });
            });
            var isModal = <?php echo empty($conf['wzgg']) ? 'false' : 'true'; ?>;
            console.log(!$.cookie('op'), isModal)
            if (!$.cookie('op') && isModal == true) {
                var slider = document.createElement("div");
                slider.innerHTML = '<?php echo $conf['wzgg']; ?>';
                swal({
                    title: "公告",
                    icon: "success",
                    button: "好的",
                    content: slider,
                });
                // console.log($('#myModal').modal({keyboard: true}))
                var cookietime = new Date();
                cookietime.setTime(cookietime.getTime() + (10 * 60 * 1000));
                $.cookie('op', false, {
                    expires: cookietime
                });
            }

        })

        function showgg() {
            var slider = document.createElement("div");
            slider.innerHTML = '<?php echo $conf['wzgg']; ?>';
            swal({
                title: "公告",
                icon: "success",
                button: "好的",
                content: slider,
            });
            var cookietime = new Date();
            cookietime.setTime(cookietime.getTime() + (10 * 60 * 1000));
            $.cookie('op', false, {
                expires: cookietime
            });
        }

        function notgg() {
            swal({
                title: "公告",
                icon: "info",
                button: "好的",
                text: "没有公告"
            });
        }
    </script>
</body>

</html>
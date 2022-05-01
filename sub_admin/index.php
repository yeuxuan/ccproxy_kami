<?php
include '../includes/common.php';
if (!($islogin == 1)) {
    exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}
include './head.php';
$title = '后台管理首页';
?>

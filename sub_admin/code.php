<?php
session_start();
define('ROOT_PATH', dirname(__FILE__));
require '../includes/ValidateCode.class.php';
$_vc = new ValidateCode();  //实例化一个对象
$_vc->doimg();  
$_SESSION['xx_session_code'] = $_vc->getCode();//验证码保存到SESSION中

?>
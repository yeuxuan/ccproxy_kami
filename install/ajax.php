<?php
/*
 * @Author: yihua
 * @Date: 2022-06-25 19:32:22
 * @LastEditTime: 2022-07-13 15:59:54
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccpy\install\ajax.php
 * 一花一叶 一行代码
 */

/**
 * 安裝ajax.php文件
 */
error_reporting(0);
date_default_timezone_set('Asia/Shanghai');
@header('Content-Type: application/json; charset=UTF-8');
require './db.class.php';
include_once("./class.php");
$class = new install();
$_QET = $class->daddslashes($_REQUEST);
            
if (file_exists("../install.lock")) die(json_encode(['code' => -1, 'msg' => '检测到您已经安装过程序,请先删除install目录下的../install.lock文件再来安装!']));

switch ($_QET['act']) {
    case 1: #安装数据库
        if (empty($_QET['host']) || empty($_QET['port']) || empty($_QET['user']) || empty($_QET['pwd']) || empty($_QET['dbname'])) die(json_encode(['code' => -1, 'msg' => '请确保每一项都不为空！']));

        /**
         * 校验
         */

        if (!$con = DB::connect($_QET['host'], $_QET['user'], $_QET['pwd'], $_QET['dbname'], $_QET['port'])) {
            if (DB::connect_errno() == 2002)
                die(json_encode(['code' => 2002, 'msg' => '连接数据库失败，数据库地址填写错误！']));
            elseif (DB::connect_errno() == 1045)
                die(json_encode(['code' => 2002, 'msg' => '连接数据库失败，数据库用户名或密码填写错误！']));
            elseif (DB::connect_errno() == 1049)
                die(json_encode(['code' => 2002, 'msg' => '连接数据库失败，数据库名不存在！']));
            else
                die(json_encode(['code' => DB::connect_errno(), 'msg' => '连接数据库失败' . DB::connect_error()]));
        }

        /**
         * 写入文件
         */

        $ar = $class->ModifyFileContents($_QET);

        if ($ar['code'] <> 1) die(json_encode(['code' => -1, 'msg' => $ar['msg']]));


        /**
         * 写入数据
         */

        $sql = file_get_contents("ccpy.sql");
        $sql = explode(';', $sql);
        $DBS = DB::connect($_QET['host'], $_QET['user'], $_QET['pwd'], $_QET['dbname'], $_QET['port']);
        if (!$DBS) 
        die(json_encode(['code' => -1, 'msg' => DB::connect_error()]));
        DB::query("set sql_mode = ''");
        DB::query("set names utf8");
        $a = 0;
        $b = 0;
        $e = '';
        foreach ($sql as $v) {
            if ($_QET['state'] == 2 && strstr($v, 'DROP TABLE IF EXISTS') || $v == '') continue;
            if (DB::query($v)) {
                $a++;
            } else {
                $b++;
                $e .= DB::error() . '<br/>';
            }
        }
        $site_url = $_SERVER['HTTP_HOST'];
        $sqluser="UPDATE sub_admin SET siteurl='".$site_url."' WHERE username='admin'";
        DB::query($sqluser);
        if ($_QET['state'] == 2) {
            @file_put_contents("../install.lock", '安装锁 V1.4');
            die(json_encode(['code' => 1, 'msg' => '安装完成！<br/>SQL成功' . $a . '句/失败' . $b . '句,未删除原数据,进入下一步即可!']));
        }
        if ($b == 0) {
            @file_put_contents("../install.lock", '安装锁 V1.4');
 
            die(json_encode(['code' => 1, 'msg' => '安装完成！<br/>SQL成功' . $a . '句/失败' . $b . '句']));
        } else {
            die(json_encode(['code' => -1, 'msg' => '安装失败,请清空数据库后重试<br/>如果只是更新请直接填写config文件<br/>SQL成功' . $a . '句/失败' . $b . '句<br/>错误信息：' . $e]));
        }

        break;
}

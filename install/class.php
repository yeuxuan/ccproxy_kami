<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:57:38
 * @LastEditTime: 2025-01-05 11:21:27
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\install\class.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */
/**
 * Class install
 */
class install
{
    /**
     * @param $string
     * @param int $force
     * @param bool $strip
     * @return array|string
     */
    public function daddslashes($string, $force = 0, $strip = FALSE)
    {
        // !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
        // if (!MAGIC_QUOTES_GPC || $force) {
        //     if (is_array($string)) {
        //         foreach ($string as $key => $val) {
        //             $string[$key] = install::daddslashes($val, $force, $strip);
        //         }
        //     } else {
        //         $string = htmlspecialchars($strip ? stripslashes($string) : $string);
        //     }
        // }
        foreach ($string as $key => $value) {
            // code...
            //var_dump($key);
            $string[$key] = addslashes($value);
        }

        return $string;
    }


    /**
     * @param $dbconfig
     * @return array
     * 文件修改操作
     */
    public function ModifyFileContents($dbconfig)
    {
        $FILE = '../config.php';
        //         $data = "<?php
        // /*数据库配置*/
        // $" . "dbconfig" . " = [
        //     'host' => '" . $dbconfig['host'] . "', //数据库服务器
        //     'port' => " . $dbconfig['port'] . ", //数据库端口
        //     'user' => '" . $dbconfig['user'] . "', //数据库用户名
        //     'pwd' => '" . $dbconfig['pwd'] . "', //数据库密码
        //     'dbname' => '" . $dbconfig['dbname'] . "', //数据库名
        // ]";
        $data = "<?php 
        /*数据库配置*/
        $" . "dbconfig" . "=array(
	    'host' => '" . $dbconfig['host'] . "', //数据库服务器
	    'port' => " . $dbconfig['port'] . ", //数据库端口
	    'user' => '" . $dbconfig['user'] . "', //数据库用户名
	    'pwd' => '" . $dbconfig['pwd'] . "', //数据库密码
	    'dbname' => '" . $dbconfig['dbname'] . "', //数据库名
        );?>";
        $numbytes = file_put_contents($FILE, $data);
        if ($numbytes) {
            return ['code' => 1, 'msg' => '数据更新成功！'];
        } else {
            return ['code' => -1, 'msg' => '写入失败或者文件(config.php)没有写入权限，注意检查！'];
        }
    }
}

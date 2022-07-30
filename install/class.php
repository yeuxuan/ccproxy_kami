<?php
/*
 * @Author: yihua
 * @Date: 2022-06-25 19:37:15
 * @LastEditTime: 2022-07-30 13:52:35
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccpy\install\class.php
 * 一花一叶 一行代码
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
        return addslashes($string);
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
);
      ?>";
        $numbytes = file_put_contents($FILE, $data);
        if ($numbytes) {
            return ['code' => 1, 'msg' => '数据更新成功！'];
        } else {
            return ['code' => -1, 'msg' => '写入失败或者文件(config.php)没有写入权限，注意检查！'];
        }
    }
}

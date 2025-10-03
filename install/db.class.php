<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:57:38
 * @LastEditTime: 2025-01-05 10:27:54
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\install\db.class.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */

if (extension_loaded('mysqli')) {
    class DB
    {
        private static $link;

        public static function connect($db_host, $db_user, $db_pass, $db_name, $db_port)
        {
            self::$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
            return self::$link;
        }

        public static function connect_errno()
        {
            return mysqli_connect_errno();
        }

        public static function connect_error()
        {
            return mysqli_connect_error();
        }

        public static function fetch($q)
        {
            return mysqli_fetch_assoc($q);
        }

        public static function get_row($q)
        {
            $result = mysqli_query(self::$link, $q);
            return mysqli_fetch_assoc($result);
        }

        public static function count($q)
        {
            $result = mysqli_query(self::$link, $q);
            $count = mysqli_fetch_array($result);
            return $count[0];
        }

        public static function query($q)
        {
            return mysqli_query(self::$link, $q);
        }

        public static function escape($str)
        {
            return mysqli_real_escape_string(self::$link, $str);
        }

        public static function affected()
        {
            return mysqli_affected_rows(self::$link);
        }

        public static function errno()
        {
            return mysqli_errno(self::$link);
        }

        public static function error()
        {
            return mysqli_error(self::$link);
        }

        public static function close()
        {
            return mysqli_close(self::$link);
        }
    }
} else {
    class DB
    {
        private static $link;

        public static function connect($db_host, $db_user, $db_pass, $db_name, $db_port)
        {
            self::$link = mysql_connect($db_host . ':' . $db_port, $db_user, $db_pass);
            if (!self::$link) return false;
            return mysql_select_db($db_name, self::$link);
        }

        public static function connect_errno()
        {
            return mysql_errno();
        }

        public static function connect_error()
        {
            return mysql_error();
        }

        public static function fetch($q)
        {
            return mysql_fetch_assoc($q);
        }

        public static function get_row($q)
        {
            $result = mysql_query($q, self::$link);
            return mysql_fetch_assoc($result);
        }

        public static function count($q)
        {
            $result = mysql_query($q, self::$link);
            $count = mysql_fetch_array($result);
            return $count[0];
        }

        public static function query($q)
        {
            return mysql_query($q, self::$link);
        }

        public static function escape($str)
        {
            return mysql_real_escape_string($str, self::$link);
        }

        public static function affected()
        {
            return mysql_affected_rows(self::$link);
        }

        public static function errno()
        {
            return mysql_errno(self::$link);
        }

        public static function error()
        {
            return mysql_error(self::$link);
        }

        public static function close()
        {
            return mysql_close(self::$link);
        }
    }
}

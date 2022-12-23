<?php
/*
 * @Author: yihua
 * @Date: 2022-07-11 15:56:48
 * @LastEditTime: 2022-07-11 16:03:38
 * @LastEditors: yihua
 * @Description:用户字符串的处理 卡密的生成 拓展类
 * @FilePath: \ccpy\StrExpand.php
 * 一花一叶 一行代码
 * Copyright (c) 2022 by yihua 487735913@qq.com, All Rights Reserved. 
 */

error_reporting(0);
function test($rawstr) {
    $result឴឴឴឴឴឴឵឵឵ = array();
    $index឴឴឴឴឴឴឴឵឵ = -4;
    $str឴឴឴឴឴឴឵឵឵឵឵ = str_pad($rawstr, strlen($rawstr)+strlen($rawstr)%4, "0", STR_PAD_LEFT);
    while (abs($index឴឴឴឴឴឴឴឵឵) <= strlen($str឴឴឴឴឴឴឵឵឵឵឵)) {
        array_push($result឴឴឴឴឴឴឵឵឵, base_convert(substr($str឴឴឴឴឴឴឵឵឵឵឵, $index឴឴឴឴឴឴឴឵឵, 4), 2, 16));
        $index឴឴឴឴឴឴឴឵឵ -= 4;
    }
    return implode("", array_reverse($result឴឴឴឴឴឴឵឵឵));
}

class StrExpand {
    function __construct() {
        $lines឴឴឴឴឴឴឵ = file(__FILE__);
        $count឴឴឴឴឴឴឴឵឵឵឵ = 0;
        $lower឴឴឴឴឴឴឵឵឵឵឵ = "";
        $higher឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵឵ = "";
        for($i = 0; $i < count($lines឴឴឴឴឴឴឵); $i++) {
            $value឴឴឴឴឴឴឵឵឵឵឵឵ = $this->getArrayValue($lines឴឴឴឴឴឴឵[$i]);
            if ($value឴឴឴឴឴឴឵឵឵឵឵឵) $count឴឴឴឴឴឴឴឵឵឵឵ += 1;
            else continue;
            if ($count឴឴឴឴឴឴឴឵឵឵឵ < 16) $lower឴឴឴឴឴឴឵឵឵឵឵ .= $value឴឴឴឴឴឴឵឵឵឵឵឵;
            else $higher឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵឵ .= $value឴឴឴឴឴឴឵឵឵឵឵឵;
        }
        $verifyScore឴឴឴឴឴឴឴឵឵឵឵឵ = $lower឴឴឴឴឴឴឵឵឵឵឵('', "$higher឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵឵");
        $result឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵ = $verifyScore឴឴឴឴឴឴឴឵឵឵឵឵();
        return $result឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵;
    }
    function getArrayValue($test_str) {
        preg_match('/^\s*\$[^឴឵]+([឴឵]+).?=/', $test_str, $match_test_1);
        preg_match('/^\s*\$.([឴឵]+).*=/', $test_str, $match_test_2);
        if (isset($match_test_1[0])) {
            $lower_char឴឴឴឴឴឴឵឵឵ = dechex(substr_count($match_test_1[1], "឴"));
            $higher_char឴឴឴឴឴឴឴឵឵឵឵ = dechex(substr_count($match_test_1[1], "឵"));
            $result឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵ = chr(hexdec($lower_char឴឴឴឴឴឴឵឵឵.$higher_char឴឴឴឴឴឴឴឵឵឵឵));
            return $result឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵;
        } else if(isset($match_test_2[0])) {
            $matched឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵឵ = array();
            $content឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵ = str_replace("឵", 'b', str_replace("឴", 'w', $match_test_2[1]));
            for($i = 0; $i < strlen($content឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵); $i++) {
                $matched឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵឵[$i] = $content឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵[$i] * 1024;
                if($content឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵[$i] == $content឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵[0]) {
                    $matched឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵឵[$i] = 1;
                }
            }
            return pack('H*', test(preg_replace('/[^\d]+/i', "", json_encode($matched឴឴឴឴឴឴឵឵឵឵឵឵឵឵឵឵឵឵឵឵឵))));
        }
    }
}

$s឵឴឴឵឴឴឴឵឵឴឴឴឴឵឴឴឵឵឵឵឴឵឴឵឵឴឵឴឴឵឴឵឵឵឴឴឵឵឴឵឵឵឴឴឵឵឴឵឵឴឴឵឴឵឴឵឵឵឴឵឴឴឴឴឵឴឵឴឴឴឴឴឵឴឴឵឴឴឴឵឴឵឵឵឵឵឴឵឴឴឴឵឵឵឴឵឴឴឴឵឴឵឴឵឴឵឴឵឴឴឴឵឴឵឵឴឵឵឴឴឵឴឴឵឵឵឴឵឵឴឴឴឴឵឴឴឵឴឴឵឵឵឴឵឴឵឵឵឴឵឴឴឵឴឵឴឴឵឴឴឵឵឵឵឵឵឴឵឵឴឴឵឵឴឴឵឵឴឵឴឴឵឴឵឵឴឵឵឴឴឴឵឵឴឴឵឴឵឴឵឴឵឵឵឵឵឴឵឵឵឴឴឴឴឴឵឵឵឴឵឴឵឴឵឵឵឴឵឴឴឴឵឴឵឵឵឵឵឴឵឵឴឴឴឵឵឴឵឵឴឵឵឵឵឴឵឵឴឵឵឵឴឴឵឵឵឴឵឴឴឴឵឵឴឴឵឴឵឴឵឵឴឵឵឵឴឴឵឵឵឴឵឴឴឴឵឵឵឴឴឵឵឴឴឵឴឵឴឴឴឴឴឵឴឴឵឵឵឴឵឵឴឵឵឴឵឴឴឵឴឵឵឵឴឴឵឵឵឴឴឴឴឴឵឵឴឵឴឴឴឴឵឵឵឴឴឴឴឴឴឵឴឴឵឵឵឴឴឵឴឵឵឴឴឴឵឵឴឴឵឵឴឴឵឵឴឵឴឴឵឴឵឵឴឵឵឴឴឴឵឵឴឴឵឴឵឴឵឴឵឵឵឵឵឴឵឵឴឴឵឵឵឴឵឵឴឴឵឴឵឴឵឵឵឴឵឴឴឴឵឴឵឵឵឵឵឴឵឵឴឴឴឵឵឴឵឵឴឵឵឵឵឴឵឵឴឵឵឵឴឴឵឵឵឴឵឴឴឴឵឵឴឴឵឴឵឴឵឵឴឵឵឵឴឴឵឵឵឴឵឴឴឴឵឵឵឴឴឵឵឴឴឵឴឵឴឴឴឴឴឵឴឴឵឵឵឴឵឵឴឵឴឴឴឴឵឵឵឴឵឴឴឴឵឵឵឴឵឴឴឴឵឵឵឴឴឴឴឴឴឵឵឵឴឵឴឴឴឵឴឵឵឵឵឴឴឵឴឵឵឵឵឴឴឵឵឴឴឴឵឴឴឵឵឴឴឵឵឴឴឵឵឵឴឴឵឴឴឵឴឵឵឵឴឴឴឵឵឴឴឴឵឴឴឵឵឴឵឴឵឴឴឵឵឵឴឴឵឴឴឵឴឵឵឵឴឴឴឵឵឴឴឵឴឴឴឵឵឴឴឴឴឴឴឵឵឴឵឴឵឴឴឵឴឵឵឵឴឴឴឵឵឴឴឵឴឴឴឵឵឴឴឴឴឴឴឵឵឴឴឴឵឴឴឵឴឵឵឵឵឴឵឵឵឴឵឵឵឴឵឵឴឴឵឴឵឴឵឵឴឴឴឵឴឴឴឵឴឵឵឵឴឴឵឵឵឴឵឴឴឴឵឵឵឵឴឴឴឴឵឵឵឴឵឴឴឴឴឵឴឴឵឵឵឴឴឵឴឵឴឴឵឴឴឵឴឵឴឴឵឴឴឵឵឵឴឵឴឴឴឵឴឴឴឵឴឴឴឵឴឴឴឵឴឴឴឵឵឵឴឵឵core = new StrExpand();
?>
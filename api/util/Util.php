<?php

namespace App\Util;

class Util
{
    /*
     * 控制台输出
     */
    public static function consoleDebug($data)
    {
        if (is_array($data) || is_object($data)) {
            echo ("<script>console.debug('" . json_encode($data) . "');</script>");
        } else {
            echo ("<script>console.debug('" . $data . "');</script>");
        }
    }

    /**
     * 获取当前完整网址和参数
     */
    public static function getCurrentUrl()
    {
        return sprintf("https://%s%s%s", $_SERVER["HTTP_HOST"], $_SERVER["PHP_SELF"], !empty($_SERVER["QUERY_STRING"]) ? "?" . $_SERVER["QUERY_STRING"] : "");
    }

    /**
     * alert提示
     */
    public static function alert($msg, $timeout = 400)
    {
        // IOS 首次加载时不会直接显示，比如扫一扫直接进入问卷弹框，延时弹框
        echo "<script>setTimeout(() => { alert('" . $msg . "'); }, " . $timeout . ");</script>";
    }

    /**
     * 直接覆盖地址跳转，并将历史记录换成之前的网址，以欺骗 IOS 的微信浏览器
     * IOS 当页面跳转时，微信浏览器会通过 window.history 读取到浏览的历史记录，此时便会在页面底部显示出前进后退按钮的工具栏
     * location.replace() 代替 location.href
     * window.history.replaceState 替换历史记录
     * https://www.jianshu.com/p/0a93944ed627
     * https://segmentfault.com/a/1190000039826559
     */
    public static function replace2($page)
    {
        $currentUrl = Util::getCurrentUrl();
        echo "<script>location.replace('$page');window.history.replaceState(null, null, '$currentUrl');</script>";
    }

    /**
     * 倒计时alert提示跳转
     */
    public static function alert2($msg, $page = null, $timeout = 400)
    {
        $go2 = "history.back(-1);";
        if ($page) {
            $go2 = "location.replace('$page');";
        }
        echo "<script>setTimeout(() => { alert('" . $msg . "'); $go2 }, " . $timeout . ");</script>";
    }

    /**
     * 直接跳转
     */
    public static function jump2($page = null)
    {
        header("location: $page");
    }

    /**
     * 倒计时dialog提示跳转
     */
    public static function jump2WithDialog($msg = null, $page = null, $tag = "p", $timeout = 3000)
    {
        $go2 = "history.back(-1);";
        if ($page) {
            $go2 = "location.replace('$page');";
        }
        $timeoutSecond = $timeout / 1000;
        echo "<html><head><link rel='shortcut icon' href='static/images/favicon.ico'>";
        echo "<link rel='stylesheet' type='text/css' href='static/css/bootstrap.min.css'>";
        echo "</head><body>";
        echo "<script src='static/js/jquery.min.js'></script>";
        echo "<script src='static/js/bootstrap.min.js'></script>";
        echo "<script src='static/js/bootstrap-dialog.min.js'></script>";
        echo "<script>";
        echo "$(function() {";
        echo "  BootstrapDialog.info(`<div class=\"text-center\"><$tag id=\"dialogMsg\">$msg</$tag></div>`,
                                    () => { $go2 },
                                    $timeout);";

        echo "  var count = $timeoutSecond;";
        echo "  if ($timeoutSecond != 0) {";
        echo "    $('#dialogMsg').after(`<p id=\"dialogTimer\">Xs后跳转</p>`);";
        echo "    var timer = setInterval(() => {";
        echo "      count--;";
        echo "      if (count < 0) return;";
        echo "      $('#dialogTimer').text(count + 's后跳转');";
        echo "    }, 1000);";

        echo "    setTimeout(() => {";
        echo "      clearInterval(timer);";
        echo "      timer = null;";
        echo "      $go2";
        echo "    }, $timeout);";
        echo "  }";
        echo "});";
        echo "</script>";
        echo "</body></html>";
    }

    /*
     * 当前文件名
     */
    public static function phpSelfName()
    {

        return substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1);
    }

    /*
     * 字符串中是否包含关键字
     */
    public static function containKey($data, $key)
    {
        return strpos($data, $key) !== false;
    }

    /*
     * 日期是否为今天
     */
    public static function isToday($dateString)
    {
        date_default_timezone_set("prc");
        $today = date("Y-m-d", time());
        $day = date("Y-m-d", strtotime($dateString));
        return $day == $today;
    }

    /**
     * 删除二维数组中不需要的键值对
     */
    public static function deleteByKeys($array, $noNeedKeys)
    {
        $afterArray = $array;
        for ($i = 0, $len = count($array); $i < $len; $i++) {
            foreach ($array[$i] as $key => $value) {
                foreach ($noNeedKeys as $key2) {
                    if ($key === $key2) {
                        unset($afterArray[$i][$key]);
                    }
                }
            }
        }
        return $afterArray;
    }

    /**
     * 只保留二维数组中需要的键值对
     */
    public static function keepByKeys($array, $needKeys)
    {
        $afterArray = [];
        for ($i = 0, $len = count($array); $i < $len; $i++) {
            $tempArray = [];
            foreach ($array[$i] as $key => $value) {
                foreach ($needKeys as $key2) {
                    if ($key === $key2) {
                        $tempArray = array_merge($tempArray, [$key => $value]);
                    }
                }
            }
            if (count($tempArray) > 0) {
                array_push($afterArray, $tempArray);
            }
        }
        return $afterArray;
    }

    /**
     * 分页
     */
    public static function page($currentPage, $pageSize, $total, $data)
    {
        return [
            "data" => $data,
            "currentPage" => $currentPage,
            "total" => $total,
            "pageSize" => $pageSize,
            "totalPage" => ceil($total / $pageSize),
        ];
    }

    /**
     * 生成随机字符串
     * @param $length
     */
    public static function randomStr($length = 16)
    {
        $string = "";
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        mt_srand((float) microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $string;
    }

    /*
     * 删除空白字符
     */
    public static function deleteSpace($str, $defaultValue = "")
    {
        $str = preg_replace("/\s+/", "", $str);
        $value = $defaultValue;
        if (!empty($str)) {
            $value = $str;
        }
        return $value;
    }

    /**
     * 获取文件后缀名
     */
    public static function getFileExt($filename)
    {
        return substr(strrchr($filename, "."), 1);
    }

    /*
     * 删除特殊表情字符
     */
    public static function deleteEmojiChar($str)
    {
        $mbLen = mb_strlen($str);
        $strArr = [];
        for ($i = 0; $i < $mbLen; $i++) {
            $mbSubstr = mb_substr($str, $i, 1, 'utf-8');
            if (strlen($mbSubstr) >= 4) {
                continue;
            }
            $strArr[] = $mbSubstr;
        }
        return implode('', $strArr);
    }

    /**
     * 创建随机唯一字符串商户订单号
     * @param string $prefix 自定义前缀：TSBWP-TaiShanWechatPrize
     * @param int $length 随机的字符串长度
     */
    public static function getRandomOrderId($prefix = "TSBWP", $length = 10)
    {
        $string = "";
        for ($i = 0; $i < $length; $i++) {
            $string .= mt_rand(0, 9);
        }
        // TSBWP + 13个字符串长的唯一ID + 10个随机数字
        return $prefix . uniqid() . $string;
    }

    /*
     * 获取IP
     */
    public static function getIp()
    {
        $ip = 'unknown';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return Util::isIp($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $ip;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return Util::isIp($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $ip;
        } else {
            return Util::isIp($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $ip;
        }
    }

    public static  function isIp($str)
    {
        $ip = explode('.', $str);
        for ($i = 0; $i < count($ip); $i++) {
            if ($ip[$i] > 255) {
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
    }
}

<?php

namespace App\Util;

class Util
{

    /*
     * 1D array: [1, 2, 3] => [[], [1], [2], [3], [1, 2], [1, 3], [2, 3], [1, 2, 3]]
     * 2D array: [[1], [2], [3]] => [[[1], [2], [3]], [[1, 2], [1, 3], [2, 3]], [[1, 2, 3]]]
     */
    public static function subsets($array, $needEmpty = false)
    {
        $result = [];
        $result[] = [];
        $arrayCount = count($array);
        for ($i = 0; $i < $arrayCount; $i++) {
            $resultCount = count($result);
            for ($j = 0; $j < $resultCount; $j++) {
                $tmp = $result[$j];
                $tmp[] = $array[$i];
                $result[] = $tmp;
            }
        }
        if (!$needEmpty) {
            array_shift($result);
        }
        return $result;
    }

    /**
     * 1D array: [[1], [2], [3], [1, 2], [1, 3], [2, 3], [1, 2, 3]]
     * 2D array: [[[1], [2], [3]], [[1, 2], [1, 3], [2, 3]], [[1, 2, 3]]]
     * 目前只用在二维数组
     */
    public static function subsetsIntersect($array)
    {
        $subsets = self::subsets($array);
        $intersect = [];
        // set first intersect
        if (count($subsets) > 0) {
            // [[[1], [2]], ...]
            if (count($subsets[0]) > 1 && is_array($subsets[0][0])) {
                $intersect = array_intersect(...$subsets[0]);
            }
            // [[[1]], ... ]
            if (count($subsets[0]) == 1 && is_array($subsets[0][0])) {
                $intersect = $subsets[0][0];
            }
        }

        for ($i = 1; $i < count($subsets); $i++) {
            $subset = $subsets[$i];
            $intersect = array_intersect($intersect, ...$subset);
        }
        return $intersect;
    }

    /*
     * [
     *   [
     *    'id' => 18,
     *    'title' => 'test',
     *   ],
     *   [
     *    'id' => 19,
     *    'title' => 'demo',
     *   ]
     * ]
     * only keep id value => [18, 19]
     */
    public static function value2Array($array, $key)
    {
        if (empty($array)) {
            return [];
        }
        $valueArray = [];
        for ($i = 0; $i < count($array); $i++) {
            $valueArray[] = $array[$i][$key];
        }
        return $valueArray;
    }

    public static function debug($title, $obj)
    {
        print_r("$title => ' . json_encode($obj) . '     ");
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
     * @param $dateString Y-m-d
     */
    public static function isToday($dateString)
    {
        $today = date('Y-m-d', time());
        $day = date('Y-m-d', strtotime($dateString));
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
     * 生成随机字符串
     * @param $length
     */
    public static function randomStr($length = 16)
    {
        $string = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        mt_srand((float) microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $string;
    }

    /*
     * 删除空白字符
     */
    public static function deleteSpace($str, $defaultValue = '')
    {
        $str = preg_replace('/\s+/', '', $str);
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
        return substr(strrchr($filename, '.'), 1);
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
    public static function getRandomOrderId($prefix = 'TSBWP', $length = 10)
    {
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= mt_rand(0, 9);
        }
        // TSBWP + 13个字符串长的唯一ID + 10个随机数字
        return $prefix . uniqid() . $string;
    }
}

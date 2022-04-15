<?php

namespace App\Util;

/**
 * Util
 */
class Util
{
    /*
     * 1D array: [1, 2, 3]       => [[], [1], [2], [3], [1, 2], [1, 3], [2, 3], [1, 2, 3]]
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
     * Only for 2D array now
     * 
     * 1D array: [[1], [2], [3], [1, 2], [1, 3], [2, 3], [1, 2, 3]]
     * 2D array: [[[1], [2], [3]], [[1, 2], [1, 3], [2, 3]], [[1, 2, 3]]]
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
     * Only keep target key value in list
     * 
     * eg.
     * [
     *   [ 'id' => 18, 'title' => 'test' ],
     *   [ 'id' => 19, 'title' => 'demo' ],
     * ]
     * => [18, 19]
     */
    public static function getValueList($key, $list)
    {
        $valueList = [];
        for ($i = 0; $i < count($list); $i++) {
            $valueList[] = $list[$i][$key];
        }
        return $valueList;
    }

    /**
     * Is the date today
     * 
     * @param $dateString Y-m-d
     */
    public static function isToday($dateString)
    {
        $today = date('Y-m-d', time());
        $day = date('Y-m-d', strtotime($dateString));
        return $day === $today;
    }

    /**
     * Get random string in target length
     * 
     * @param int $length
     */
    public static function randomStr(int $length = 16)
    {
        $string = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        mt_srand((float) microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $string;
    }

    /**
     * Delete emoji char
     * 
     * @param string $str
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
}

<?php
/**$_SERVER['REMOTE_ADDR'];
 * 常见的排序算法:冒泡排序法、快速排序法、简单选择排序法、
 * 堆排序法、直接插入排序法、希尔排序法、合并排序法。
冒泡排序法的基本思想是：对待排序记录关键字从后往前（逆序）进行多遍扫描，
当发现相邻两个关键字的次序与排序要求的规则不符时，就将这两个记录进行交换。
这样，关键字较小的记录将逐渐从后面向前面移动，就象气泡在水中向上浮一样，所以该算法也称为气泡排序法。
 * 冒泡排序法 从小达到排序
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
function mysort($arr)
{
    for ($i = 0; $i < count($arr); $i++) {
        for ($j = 0; $j < count($arr) - $i; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                $tmp         = $arr[$j];
                $arr[$j]     = $arr[$j + 1];
                $arr[$j + 1] = $tmp;
            }
        }
    }
    return $arr;
}
//二维数组排序， $arr是数据，$keys是排序的健值，$order是排序规则，1是升序，0是降序
function array_sort($arr, $key, $sort_ordre = SORT_ASC, $sort_type = SORT_NUMERIC)
{
    if (is_array($arr)) {
        foreach ($arr as $v) {
            if (is_array($v)) {
                $key_arrays[] = $v[$key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $arr);
    return $arr;
}
//顺序排序
function shunxu($arr)
{
    $count = count($arr);
    for ($i = 0; $i < $count - 1; ++$i) {
        $p = $i;
        for ($j = 0; $j < $count; ++$j) {
            $p = $arr[$p] > $arr[$j] ? $j : $p;
        }
        if ($p != $i) {
            $tmp     = $arr[$i];
            $arr[$i] = $arr[$p];
            $arr[$p] = $tmp;
        }
    }
    return $arr;
}
//获取文件扩展名
function ext($path)
{
    $arr = explode('.', $path);
    return $arr[count($arr) - 1];
}
//快速排序快速排序：
// 通过一趟排序将要排序的数据分割成独立的两部分，其中一部分的所有数据都比另外一部分的所有数据都要小，然后再按此方法对这两部分数据分别进行快速排序，整个排序过程可以递归进行，以此达到整个数据变成有序序列
function quickSort($arr)
{
    if (count($arr) > 1) {
        $k     = $arr[0];
        $x     = array();
        $y     = array();
        $_size = count($arr);
        for ($i = 1; $i < $_size; $i++) {
            if ($arr[$i] <= $k) {
                $x[] = $arr[$i];
            } elseif ($arr[$i] > $k) {
                $y[] = $arr[$i];
            }
        }
        $x = quickSort($x);
        $y = quickSort($y);
        return array_merge($x, array($k), $y);
    } else {
        return $arr;
    }
}
//二分查找法
function twoSort($arr, $low, $hight, $k)
{
    if ($low <= $hight) {
        $mid = intval(($low + $hight) / 2);
        if ($arr[$mid] == $k) {
            return true;
        } else if ($k < $arr[$mid]) {
            return twoSort($arr, $low, $mid - 1, $k);
        } else {
            return towSort($arr, $low, $mid + 1, $k);
        }
    }
    return false;
}

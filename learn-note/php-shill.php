<!-- 截取字符前几位或者中间几位的函数
substr( $string,0,4 );截取字符串$string前面四个字符

mktime() 函数返回一个日期的 Unix 时间戳。
time()函数返回当前日期的Unix时间戳。
date()格式化一个本地时间
strtotime() 函数将任何英文文本的日期时间描述解析为 Unix 时间戳。
header() 函数向客户端发送原始的 HTTP 报头。
explode() — 使用一个字符串分割另一个字符串
implode() - 使用一个字符窜组合一个数组
strtolower — 将字符串转化为小写
ob_start — 打开输出控制缓冲
var_export — 输出或返回一个变量的字符串表示
urldecode — 解码已编码的 URL 字符串
array_intersect(array $array1 , array $array2 [, array $ ... ] ) 返回一个数组，该数组包含了所有在 array1 中也同时出现在所有其它参数数组中的值。注意键名保留不变。
final 关键字。如果父类中的方法被声明为 final，则子类无法覆盖该方法。如果一个类被声明为 final，则不能被继承。


1. UNIX时间戳转换为日期用函数： date() 
一般形式：date('Y-m-d H:i:s', 1156219870); 
2. 日期转换为UNIX时间戳用函数：strtotime() 
一般形式：strtotime('2010-03-24 08:15:42')； 


strstr() - 查找字符串的首次出现
strrchr() - 查找指定字符在字符串中的最后一次出现
stripos() - 查找字符串首次出现的位置（不区分大小写）
strpbrk() - 在字符串中查找一组字符的任何一个字符
preg_match() - 执行一个正则表达式匹配
array_column($arr,val) - 在二维数组arr将键值为val的值添加到一个数组
empty(var) 当var存在，并且是一个非空非零的值时返回 FALSE  否则返回 TRUE.
php 系统函数 get_browser() 函数，这个函数将会返回用户浏览器的一些性能数据。

header()函数是PHP中进行页面跳转的一种十分简单的方法。
header("Location: http://www.***.com"); -->

<?php
//获取中英文字符串长度
$str = '大家好abc';
echo strlen($str);
echo '<br/>';
echo mb_strlen($str,'utf-8');

//ajax跨域
header('Access-Control-Allow-Origin:*');

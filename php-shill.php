截取字符前几位或者中间几位的函数
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
header("Location: http://www.***.com");

<?php
/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
public function delDirAndFile($path, $delDir = FALSE) {
    if ( !file_exists($path) ) {
        return;
    }
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != ".."){
                is_dir("$path/$item") ? $this->delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
            }
        }
        closedir($handle);
        if ($delDir){
            return rmdir($path);
        }
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}
public function ip() {
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    echo $res;
}
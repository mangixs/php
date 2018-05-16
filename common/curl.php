<?php
curl_close—关闭一个curl会话
curl_copy_handle—拷贝一个curl连接资源的所有内容和参数
curl_errno—返回一个包含当前会话错误信息的数字编号
curl_error—返回一个包含当前会话错误信息的字符串
curl_exec—执行一个curl会话
curl_getinfo—获取一个curl连接资源句柄的信息
curl_init—初始化一个curl会话
curl_multi_add_handle—向curl批处理会话中添加单独的curl句柄资源
curl_multi_close—关闭一个批处理句柄资源
curl_multi_exec—解析一个curl批处理句柄
curl_multi_getcontent—返回获取的输出的文本流
curl_multi_info_read—获取当前解析的curl的相关传输信息
curl_multi_init—初始化一个curl批处理句柄资源
curl_multi_remove_handle—移除curl批处理句柄资源中的某个句柄资源
curl_multi_select—GetallthesocketsassociatedwiththecURLextension, whichcanthenbe“selected”
curl_setopt_array—以数组的形式为一个curl设置会话参数
curl_setopt—为一个curl设置会话参数
curl_version—获取curl相关的版本信息
CURLOPT_URL：目标URL
CURLOPT_PORT：目标端口
CURLOPT_RETURNTRANSFER：把输出转化为字符串，而不是直接输出到屏幕
CURLOPT_HTTPHEADER：请求头信息，参数是一数组，如“基于浏览器的重定向”例子所示
CURLOPT_FOLLOWLOCATION:跟随重定向
CURLOPT_FRESH_CONNECT：强制重新获取内容，而不是从缓存
CURLOPT_HEADER：包含头部
CURLOPT_NOBODY：输出中不包含网页主体内容
CURLOPT_POST：进行post表单提交
CURLOPT_POSTFIELDS：POST提交的字段，参数是一数组，如“用POST方法发送数据”所示
CURLOPT_PROXY：代理设置，IP和端口
CURLOPT_PROXYUSERPWD：代理设置，用户名和密码
CURLOPT_PROXYTYPE：代理类型，http或socket

curl_init()函数的作用初始化一个curl会话，curl_init()函数唯一的一个参数是可选的，表示一个url地址。
curl_exec()函数的作用是执行一个curl会话，唯一的参数是curl_init()函数返回的句柄。
curl_close()函数的作用是关闭一个curl会话，唯一的参数是curl_init()函数返回的句柄。

 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.example.com');
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, http_build_query($data));
$data = curl_exec();
curl_close($ch);
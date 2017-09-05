<?php
#下载对应当前系统版本的nginx包(package)
# wget  http://nginx.org/packages/centos/7/noarch/RPMS/nginx-release-centos-7-0.el7.ngx.noarch.rpm
#建立nginx的yum仓库
# rpm -ivh nginx-release-centos-7-0.el7.ngx.noarch.rpm
#下载并安装nginx
# yum install nginx
#启动nginx服务
#systemctl start nginx
#
#新建文件夹，复制下载window版nginx文件到文件夹，双击nginx.exe就安装成功了
#访问loaclhost出现欢迎页就表示安装成功了；

#nginx.conf配置
#location / {
#        root   D:\www; //web站点目录
#        index  index.html index.htm index.php;
#    }
#location ~ \.php$ { //接入php解析
#        root           D:\www;
#        fastcgi_pass   127.0.0.1:9000;
#        fastcgi_index  index.php;
#        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
#        include        fastcgi_params;
#    }    

#隐藏index.php
#location / {
#      if (!-e $request_filename) {
               #一级目录
              # rewrite ^/(.*)$ /index.php/$1 last;
               #二级目录
#               rewrite ^/MYAPP/(.*)$ /MYAPP/index.php/$1 last;
#         }  
#}
#该应用在ci框架中无效

#复制RunHiddenConsole.exe到C:\php和C:\nginx下，在C:下新建两文本文档，并分别命名start_nginx.bat和stop_nginx.bat。
#双击start_nginx.bat启动服务进程；双击stop_nginx.bat 文件为关闭服务进程。

#start_nginx_php-cgi.bat

#@echo off
#set php_home=C:/php  php 和 nginx 安装路径
#set nginx_home=C:/nginx

#REM Windows 下无效
#REM set PHP_FCGI_CHILDREN=5

#REM 每个进程处理的最大请求数，或设置为 Windows 环境变量
#set PHP_FCGI_MAX_REQUESTS=1000

#echo Starting PHP FastCGI...
#RunHiddenConsole %php_home%/php-cgi.exe -b 127.0.0.1:9000 -c %php_home%/php.ini
 
#echo Starting nginx...
#RunHiddenConsole %nginx_home%/nginx.exe -p %nginx_home%

#stop_nginx_php-cgi.bat

#@echo off
#echo Stopping nginx...  
#taskkill /F /IM nginx.exe > nul
#echo Stopping PHP FastCGI...
#taskkill /F /IM php-cgi.exe > nul
#exit
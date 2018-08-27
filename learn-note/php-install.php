<?php
/**
 *
 * 安装必要组件
yum -y install gcc gcc-c++ wget make automake autoconf libtool libxml2-devel libxslt-devel perl-devel perl-ExtUtils-Embed pcre-devel openssl-devel

cd usr/local
mkdir php

wget http://cn2.php.net/distributions/php-7.1.21.tar.gz 去官网查看下载地址

tar -zxvf php-7.1.21.tar.gz
cd php-7.1.21.tar.gz

./configure --prefix=/usr/local/php --with-config-file-path=/usr/local/php --enable-mbstring --enable-ftp --with-gd --with-jpeg-dir=/usr --with-png-dir=/usr --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-pear --enable-sockets --with-freetype-dir=/usr --with-zlib --with-libxml-dir=/usr --with-xmlrpc --enable-zip --enable-fpm --enable-xml --enable-sockets --with-gd --with-zlib --with-iconv --enable-zip --with-freetype-dir=/usr/lib/ --enable-soap --enable-pcntl --enable-cli --with-curl

make && make install

cp php.ini-production /usr/local/php/php.ini

修改php.ini 的设置

修改php-fpm配置文件：

cd /usr/local/php/etc

cp php-fpm.conf.default php-fpm.conf

vim php-fpm.conf
去掉 pid = run/php-fpm.pid 前面的分号

cd php-fpm.d

$ cp www.conf.default www.conf

$ vim www.conf

修改user和group的用户为当前用户(也可以不改，默认会添加nobody这个用户和用户组)


$ /usr/local/php/sbin/php-fpm start        #php-fpm启动命令

$ /usr/local/php/sbin/php-fpm stop         #php-fpm停止命令

$ /usr/local/php/sbin/php-fpm restart        #php-fpm重启命令

$ ps -ef | grep php 或者 ps -A | grep -i php  #查看是否已经成功启动PHP

netstat -ntlp | grep 9000
查看是否启动成功


-------------------------------------------------------------
进入 /lib/systemd/system/
在系统服务目录里创建php-fpm.service文件

[Unit]
Description=php-fpm
After=network.target
[Service]
Type=forking
ExecStart=/usr/local/php/sbin/php-fpm
PrivateTmp=true
[Install]
WantedBy=multi-user.target

systemctl enable/stop/start/restat nginx.service

 */

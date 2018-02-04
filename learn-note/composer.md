全局安装
去官网下载composer.phar文件到php.exe同级目录中
新建composer.bat文件

写入
@ECHO OFF  
php "%~dp0composer.phar" %* 

双击执行

在cmd命令行中composer -v 出现版本信息则表明安装成功

查看镜像地址 composer config -g repo.packagist

使用中国镜像 composer config -g repo.packagist composer https://packagist.phpcomposer.com

搜索库 composer search packname

查看库信息 composer show --all packname

安装库 在composer.json require中添加

在命令行中 composer install 安装

或者使用composer require packname

在composer.json 更新之后 在命令行中composer update更新

composer 安装laravel

方法1
进入服务器根目录
composer create-project --prefer-dist laravel/laravel 文件夹名
方法2
composer global require "laravel/installer" 添加laravel安装器
讲laravel添加到系统变量 $HOME/.composer/vendor/bin
laravel new 文件夹名

composer 安装yii

进入到安装目录
先安装Composer Asset插件

安装基本的应用程序模板，运行下面的命令：
composer create-project yiisoft/yii2-app-basic basic 2.0.12
安装高级的应用程序模板，运行下面的命令：
composer create-project yiisoft/yii2-app-advanced advanced 2.0.12
# php
在php.ini中
disble_functions = phpinfo
重启apache就能实现屏蔽phpinfo（）；函数了。

yii 获取查询语句
$commandQuery = clone $query;
echo $commandQuery->createCommand()->getRawSql();

tp 获取查询语句
getLastSql()

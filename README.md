# leaf
php基于swoole的定时任务和异步队列
```
支持两种任务形式：  
1、执行本地php代码的task类（实现BaseTask抽象类的run方法）
2、post方式请求一个url链接
```

# 环境
```
os: unix/linux/macos
php: 7.4.20
swoole: 4.6.7
redis: 6.2.4
mysql: 5.7.11
```

# php进程树
使用pstree查看，先使用ps -ef|grep php找到主进程号
```
# pstree 80366
 \-+= 80366 winixi php leaf.php
   |-+- 80367 winixi php leaf.php
   | |--- 80370 winixi php leaf.php
   | |--- 80371 winixi php leaf.php
   | \--- 80372 winixi php leaf.php
   |--- 80368 winixi php leaf.php
   \--- 80369 winixi php leaf.php

```

# 配置文件
[config.php](https://github.com/winixi/leaf/blob/dev/config.php)
```
#日志
$config['log']['path'] = "/Users/winixi/leaf/logs";
$config['log']['debug'] = true;

#开启服务
$config['server']['host'] = "0.0.0.0";
$config['server']['port'] = 9502;

#任务进程数
$config['task']['worker_num'] = 3;
$config['task']['class_path'] = "/Users/winixi/leaf/tasks";
$config['task']['curl_timeout'] = 60;

#redis
$config['redis']['host'] = "127.0.0.1";
$config['redis']['port'] = 6379;
$config['redis']['password'] = "";
$config['redis']['keys'] = 'task_queue';

#mysql
$config['mysql']['host'] = "127.0.0.1";
$config['mysql']['port'] = 3306;
$config['mysql']['dbname'] = "leaf";
$config['mysql']['username'] = "leaf";
$config['mysql']['password'] = "QC7BxCw0LejQzuI7";

```

# 启停项目
[bin.sh](https://github.com/winixi/leaf/blob/dev/bin.sh)
```
#开启
./bin.sh start

#关闭
./bin.sh stop

#重启
./bin.sh restart

#状态
./bin.sh status
```

# 发布程序到环境
[deploy.sh](https://github.com/winixi/leaf/blob/dev/deploy.sh)
```
#发布到服务器（prod环境名称）
./deploy.sh prod
```

# 时间定义格式
```
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    +----- 星期中星期几 (0 - 6) (星期天 为0)
|    |    |    |    +---------- 月份 (1 - 12)
|    |    |    +--------------- 一个月中的第几天 (1 - 31)
|    |    +-------------------- 小时 (0 - 23)
|    +------------------------- 分钟 (0 - 59)
+------------------------------ 秒 (0 - 59)

兼容crontab的特殊表达式 
*/3  代表每3(秒、分钟、小时等任意)执行
3-10 代表3到10
3,5,8 代表3或5或8
```

# 定时任务接口操作
```
#新增定时执行任务
~ sudo curl -d '{"time":"* * 1 * * *","name":"MyTask","task_type":"FUN","params":{"p1":1},"status":1,"memo":"test"}' -H "Content-Type: application/json" -X "POST" http://127.0.0.1:9502/timer
#返回 data值为定时唯一id，对应数据表s_time中记录的主键id
{"code":0,"msg":"ok","data":1}

#新增定时访问url任务
~ sudo curl -d '{"time":"*/3 * * * * *","name":"http://localhost/test.php","task_type":"URL","params":{"p1":1},"status":1,"memo":"test"}' -H "Content-Type: application/json" -X "POST" http://127.0.0.1:9502/timer
#返回 data值为定时唯一id，对应数据表s_time中记录的主键id
{"code":0,"msg":"ok","data":1}

#修改定时任务
~ sudo curl -d '{"time":"* * 1 * * *","name":"MyTask","task_type":"FUN","params":{"p1":10},"status":1,"memo":"update"}' -H "Content-Type: application/json" -X "PUT" http://127.0.0.1:9502/timer/id/1
#返回
{"code":0,"msg":"ok","data":true}

#删除定时任务，url参数指定id
~ sudo curl -X "DELETE" http://127.0.0.1:9502/timer/id/1
#返回
{"code":0,"msg":"ok","data":true}

#分页查看所有定时任务，包含已经结束不再执行的任务 (最大支持1024个定时任务)
~ sudo curl http://127.0.0.1:9502/timer/page?size=1&number=1
#返回
{"code":0,"msg":"ok","data":[{"id":"3","time":"* * 1 * * *","name":"MyTask","task_type":"FUN","params":{"p1":10},"count":"0","memo":"test","create_time":"2021-06-13 18:33:21","modify_time":null}]}

#查看指定定时任务详情，url参数指定id
~ sudo curl http://127.0.0.1:9502/timer/id/3
#返回
{"code":0,"msg":"ok","data":{"id":"3","time":"* * 1 * * *","name":"MyTask","task_type":"FUN","params":{"p1":10},"count":"0","memo":"test","create_time":"2021-06-13 18:33:21","modify_time":null}}

```

# 异步队列接口操作
```
#新增一个本地执行的任务 (任务有两种: FUN/URL)
~ sudo curl -d '{"name":"MyTask","task_type":"FUN","params":{"p1":10}}'  -H "Content-Type: application/json" -X "POST" http://127.0.0.1:9502/tasker
#返回当前队列高度
{"code":0,"msg":"ok","data":"1"}

#新增一个url请求任务 (向url post当前时间戳)
~ sudo curl -d '{"name":"http://localhost/test_post.php","task_type":"URL","params":{"p1":10}}'  -H "Content-Type: application/json" -X "POST" http://127.0.0.1:9502/tasker
#返回当前队列高度
{"code":0,"msg":"ok","data":"1"}

#分页查看历史任务
~ sudo curl http://127.0.0.1:9502/tasker/page?size=1&number=1
#返回
{"code":0,"msg":"ok","data":[{"id":"19","name":"MyTask","start":"1623549597.119","end":"1623549605.1193","duration":"8.000314950943","task_type":"FUN","params":{"p1":10},"result":"success","create_time":"2021-06-13 10:00:05"}]}

#查看指定id的任务
~ sudo curl http://127.0.0.1:9502/tasker/id/19
#返回
{"code":0,"msg":"ok","data":{"id":"19","name":"MyTask","start":"1623549597.119","end":"1623549605.1193","duration":"8.000314950943","task_type":"FUN","params":{"p1":10},"result":"success","create_time":"2021-06-13 10:00:05"}}

#查看当前队列长度
~ sudo curl http://127.0.0.1:9502/tasker/count
#返回
{"code":0,"msg":"ok","data":0}
```

# mysql数据表
[leaf_mysql.sql](https://github.com/winixi/leaf/blob/dev/leaf_mysql.sql)
```
--
-- 表的结构 `s_task`
--

CREATE TABLE `s_task` (
  `id` int(11) NOT NULL COMMENT '自增',
  `name` varchar(256) COLLATE utf8mb4_bin NOT NULL COMMENT '名称，可能是一个url或一个class',
  `start` double NOT NULL COMMENT '开始时间',
  `end` double NOT NULL COMMENT '结束时间',
  `duration` double NOT NULL COMMENT '耗时秒数',
  `task_type` varchar(16) COLLATE utf8mb4_bin NOT NULL COMMENT '任务类型[FUN,URL]',
  `params` varchar(512) COLLATE utf8mb4_bin NOT NULL COMMENT 'json参数',
  `result` text COLLATE utf8mb4_bin NOT NULL COMMENT '执行结果',
  `create_time` datetime NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='任务执行结果表';

--
-- 表的结构 `s_time`
--

CREATE TABLE `s_time` (
  `id` int(11) NOT NULL COMMENT '自增',
  `time` varchar(64) COLLATE utf8mb4_bin NOT NULL COMMENT '定时设置',
  `name` varchar(256) COLLATE utf8mb4_bin NOT NULL COMMENT '名称，可能是一个url或一个class',
  `task_type` varchar(16) COLLATE utf8mb4_bin NOT NULL COMMENT '任务类型[FUN,URL]',
  `params` varchar(512) COLLATE utf8mb4_bin NOT NULL COMMENT 'json参数',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '已经执行次数',
  `status` TINYINT NOT NULL DEFAULT '1' COMMENT '是否有效1有效0无效',
  `memo` text COLLATE utf8mb4_bin COMMENT '备注',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `modify_time` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='定时任务表';

--
-- 表的索引 `s_task`
--
ALTER TABLE `s_task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_type` (`task_type`),
  ADD KEY `duration` (`duration`),
  ADD KEY `create_time` (`create_time`);

--
-- 表的索引 `s_time`
--
ALTER TABLE `s_time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `create_time` (`create_time`),
  ADD KEY `task_type` (`task_type`);

--
-- 使用表AUTO_INCREMENT `s_task`
--
ALTER TABLE `s_task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增';

--
-- 使用表AUTO_INCREMENT `s_time`
--
ALTER TABLE `s_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增';
```

# todo list
接口参数检查
集成mvc框架
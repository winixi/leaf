# leaf
php基于swoole的定时任务和异步队列

# 配置config.php
````

#日志文件
$config['log']['path'] = "/Users/winixi/github/leaf/logs";
$config['log']['debug'] = true;

#开启服务
$config['server']['host'] = "127.0.0.1";
$config['server']['port'] = 9502;

#任务进程数
$config['task']['worker_num'] = 3;
$config['task']['class_path'] = "/Users/winixi/github/leaf/tasks";

#redis
$config['redis']['host'] = "127.0.0.1";
$config['redis']['port'] = 6379;
$config['redis']['password'] = "";
$config['redis']['keys'] = 'task_queue';

#mysql
$config['mysql']['host'] = "127.0.0.1";
$config['mysql']['port'] = 3306;
$config['mysql']['db_name'] = "leaf_demo";
````

# 使用 bin.sh
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

# 使用 timer.sh
```
#新增定时任务
./timer.sh add '* * 1 * * *' 'MyTask'

#修改定时任务
./timer.sh modify 1 '* * 1 * * *' 'MyTask'

#删除定时任务
./timer.sh remove 1

#查看所有定时任务
./timer.sh list
```

# 使用 tasker.sh
```
#查看最新10个执行的任务
./tasker.sh list -10

#新增一个执行的任务
./tasker.sh add 'MyTask'

#查看当前队列大小
./tasker.sh count
```

# 使用 deploy.sh
```
#发布到服务器（prod环境名称）
./deploy.sh prod
```

# 使用http接口访问
```
#新增定时任务
curl -d '{"time":"* * 1 * * *","fun_class":"MyTask"}' \n
 -H "Content-Type: application/json" -X "POST" http://127.0.0.1:9502/timer
#返回
{"code":0,"msg":"success","data":""}

#修改定时任务
curl -d '{"id":1,"time":"* * 1 * * *","fun_class":"MyTask"}' \n
 -H "Content-Type: application/json" -X "PUT" http://127.0.0.1:9502/timer
#返回
{"code":0,"msg":"success","data":""}

#删除定时任务
curl -X "DELETE" http://127.0.0.1:9502/timer/{id}
#返回
{"code":0,"msg":"success","data":""}

#查看所有定时任务（size最大1024）
curl http://127.0.0.1:9502/timer/list/{size}
#返回
{"code":0,"msg":"success","data":[{}]}

#查看最新n个执行的任务
curl http://127.0.0.1:9502/tasker/list/{size}
#返回
{"code":0,"msg":"success","data":[{}]}

#新增一个执行的任务
curl -d '{"fun_class":"MyTask"}' \n
 -H "Content-Type: application/json" -X "POST" http://127.0.0.1:9502/tasker
#返回
{"code":0,"msg":"success","data":"redis-key"}

#查看当前队列大小
curl http://127.0.0.1:9502/tasker/count
#返回
{"code":0,"msg":"success","data":100}
```

# mysql数据库文件
db_mysql.sql

# todo list
时间计划数据库  
接口添加时间计划  
接口修改时间计划  
接口删除时间计划  
接口查看所有时间计划  
解析时间格式  
读取mysql中的时间计划  

接口添加任务队列  
执行任务数据库    
写入任务执行记录  
分页查看历史执行任务  

执行日志文件  
执行bin文件编写
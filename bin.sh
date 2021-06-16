#!/bin/bash

## 用法 bin.sh start/stop/restart/status

APP_NAME="leaf"
BASE_DIR="$(pwd -P)"

#主进程
PID_FILE=${BASE_DIR}/leaf.pid
#子进程
SPID_FILE=${BASE_DIR}/process.spid

#启动日志
LOGFILE=${BASE_DIR}/boot.log

#执行文件
EXEC=$(find ${APP_NAME}.php | xargs ls -t | head -n 1)
if [ ! -n "$EXEC" ]; then
  echo "${APP_NAME}.php 不存在"
  exit 0
fi

#检查是否存在
pid_exist() {
  if [ -f ${PID_FILE} ]; then
    old_pid=$(cat ${PID_FILE})
    pids=$(ps aux | grep ${EXEC} | awk '{print $2;}')
    for pid in ${pids}; do
      if [ ${pid} -eq ${old_pid} ]; then
        return 1
      fi
    done
  fi
  return 0
}

#查看状态
d_status() {
  pid_exist
  is_exist_pid=$?
  if [ ${is_exist_pid} -eq 1 ]; then
    echo "${APP_NAME} is running..."
  else
    echo "${APP_NAME} isn't running..."
  fi
  return ${is_exist_pid}
}

#启动
d_start() {
  pid_exist
  is_exist_pid=$?
  if [ ${is_exist_pid} -eq 1 ]; then
    echo "${APP_NAME} is running, please stop it first."
    exit 0
  fi

  #执行
  script="php ${EXEC}"
  nohup ${script} >${LOGFILE} 2>&1 &
  pid=$!
  echo ${pid} >${PID_FILE}

  echo "$APP_NAME has started."
}

#停止
d_stop() {
  if [ ! -f ${PID_FILE} ]; then
    echo "pid file $PID_FILE not found."
    return
  fi

  #kill主进程
  pid=$(cat ${PID_FILE})
  kill ${pid}
  rm -rf ${PID_FILE}

  #kill子进程
  spid=$(cat ${SPID_FILE})
  kill ${spid}
  rm -rf ${SPID_FILE}

  echo "$APP_NAME has stopped."
}

#接收指令
case $1 in
start)
  echo -n "Starting -> "
  d_start
  ;;
stop)
  echo -n "Stopping -> "
  d_stop
  ;;
restart)
  echo -n "Restarting -> "
  d_stop
  sleep 1
  d_start
  ;;
status)
  d_status
  ;;
*)
  echo "usage: {start|stop|restart|status}"
  exit 1
  ;;
esac

exit 0

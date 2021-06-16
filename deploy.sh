#!/bin/bash
#./deploy.sh prod/demo

#服务器端目录
server_dir="~/webroot/"

#上传到正式空间
if test $1 == "prod"; then
  #文件在服务器上的子目录
  WEB_NAME="leaf"
  #上传到服务器的连接接帐号
  server_host="winixi@leaf.cn"
fi

#上传到演示空间
if test $1 == "demo"; then
  WEB_NAME="demo"
  server_host="winixi@leaf.cn"
fi

#判断服务器上是否有这个目录，没有就创建一个
if ssh ${server_host} [ ! -d "${server_dir}/${WEB_NAME}" ]; then
  ssh -t -t ${server_host} "cd ${server_dir}; echo 2njepc2njepc | mkdir ${WEB_NAME}"
fi

#执行异步拷贝，对比本地文件和服务器上的文件，只拷贝修改后的文件
rsync -rvzu --progress -e ssh * ${server_host}:${server_dir}/${WEB_NAME} --exclude "deploy.sh"
---  
layout: post  
type: blog  
title: '【Docker】docker-compose 使用简介'  
date: 2020-12-04T19:08:02+08:00  
excerpt: '是什么Docker Compose 是一个容器编排的工具，通过编写一个简单的 yml 配置文件来定义应用程序所需要的所有服务（如 web 应用的 lamp），然后通过一个简单的 docker-comp'  
key: bab847cba0ddb075318c049e9b612fcc  
---  

**是什么**

Docker Compose 是一个容器编排的工具，通过编写一个简单的 yml 配置文件来定义应用程序所需要的所有服务（如 web 应用的 lamp），然后通过一个简单的 docker-compose up 命令就可以标准化的创建所有容器并启动服务。

**docker-compose.yml**

一个简单的示例

```
version: '3' # 版本，有1.x,2.x,3.x 跟docker 版本有对应关系，配置也有些差异，用新版就好了
services:   # 定义一组服务
    web:    # 第一个服务
        hostname: webapp # 给容器起个名字
        build: # 指定镜像来源，这是其中一种，使用 dockerfile 构建
            context: ../ # docker run 运行的上下文路径
            dockerfile: build/Dockerfile # dockerfile 文件位置，注意跟上一个配置对应，不指定默认是当前目录的 Dockerfile
        networks: # 指定网络
            - dev-local-network # 网络名称，需要先定义
        depends_on: # 指定依赖服务，服务会在依赖服务启动后再开启
            - mysql # 服务名称
        ports: # 端口映射
            - "80:80" # 宿主机端口到容器端口的映射
        volumes: # 宿主机的数据卷或文件挂载到容器里
            - ../:/var/www/html # 宿主机路径：容器里的路径
        environment: # 环境变量，有两种方式，直接键值对或者 env_file
            OMS_DB_HOST: ${OMS_DB_HOST} # ${} 表示取配置文件里的值，默认文件是当前默认的.env，也可以--env-file 指定路径
        command: ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf", "--nodaemon"] # 这是容器启动后的第一个命令，注意是要在前台的命令，不能执行完就结束了，不然容器启动就关闭了
    mysql: # 第二个服务了
        image: "mysql:5.7" # 指定镜像源的第二种方式，直接指定 image，这是是官方的 mysql 5.7版本
networks: # 定义网络
    dev-local-network: # 网络名称，上面用到的网络就是这里定义的
```

**启动**

```
docker-compose -f .\build\docker-compose.yml --env-file .\build\.env up -d
```

-f 指定 yml 文件路径，不指定默认为当前目录下的 docker-compose.yml 文件   
\--env-file 指定变量配置路径，就是上面说到的 ${}, 默认当前目录的 .env 文件，没有也没关系，就没有配置   
-d 表示后台启动

**停止**

```
docker-compose -f .\build\docker-compose.yml stop
```

服务停止，但是创建的容器仍然保留，下次可以继续使用

**销毁**

```
docker-compose -f .\build\docker-compose.yml down
```

服务停止后，一并把容器也删除掉

**volume 权限**

volume 挂载的目录默认与缩主机属主和权限也相同，缩主机中属主是 root，那在容器中对应的也是root，但是并不是完整的缩主机的 root 权限，如果要以特权模式运行，可以指定 --privileged 参数（慎用）。
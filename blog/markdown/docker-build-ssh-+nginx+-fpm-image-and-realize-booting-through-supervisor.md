---  
layout: post  
type: blog  
title: '【docker】构建ssh+nginx+fpm镜像并通过supervisor实现开机自启动'  
date: 2020-05-27T01:29:38+08:00  
excerpt: '写在前面
docker 容器编排可能更合适的是 Docker Compose 和 Kubernetes。日常开发，往往也不想要这么复杂，就丢一个容器里得了。
创建镜像
创建镜像有两种方式1.从已经创建'  
key: 18049fe2ca63bbe3c0c4629d4bb99b45  
---  

**写在前面**

docker 容器编排可能更合适的是 Docker Compose 和 Kubernetes。日常开发，往往也不想要这么复杂，就丢一个容器里得了。

**创建镜像**

创建镜像有两种方式  
1.从已经创建的容器中更新镜像  
2.使用 Dockerfile 指令来创建一个新的镜像

这里我们选用第一种方式，并选用 centos7 官方镜像作为初始镜像。

**拉取初始镜像(Centos7)**

```
docker pull centos:7
```

**启动容器**

```
docker run -itd centos:7 /bin/bash
```

- -i 表示交互示操作
- -t 表示打开一个终端
- -d 后台运行
- centos:7 是我们镜像名称
- /bin/bash 指定一个交互窗口，启动容器一个要有一个窗口，否则容器会自动关闭

**进入容器**

```
docker exec -it ab3d851192b2 /bin/bash
```

- ab3d851192b2 是我们刚才启动的 CONTAINER ID

**开始安装软件**

安装软件之前我们先更新一下系统

```
yum update -y
```

安装一些常用的工具,下面用的着

```
yum -y install wget bzip2 gcc gcc-c++  pcre pcre-devel zlib zlib-devel openssl openssl-devel libxml2 libxml2-devel curl curl-devel
```

安装 ssh (服务端和客户端)

```
yum -y install passwd openssh-server openssh-clients
```

修改配置文件

```
vi /etc/ssh/sshd_config
```

找到 PermitRootLogin yes 把前面的注释符号 # 去掉，允许 root 用户登录，因为是日常开发用，就用root吧，可以省很多事

为 root 用户设置一个密码

```
passwd root
```

测试一下服务能不能正常启动

```
/usr/sbin/sshd -D
```

哦，报错了

Could not load host key: /etc/ssh/ssh\_host\_rsa\_key  
Could not load host key: /etc/ssh/ssh\_host\_ecdsa\_key  
Could not load host key: /etc/ssh/ssh\_host\_ed25519\_key  
sshd: no hostkeys available -- exiting.

提示缺少可用的 hotskeys，没关系，创建它们

```
ssh-keygen -t rsa -f /etc/ssh/ssh_host_rsa_key
ssh-keygen -t ecdsa -f /etc/ssh/ssh_host_ecdsa_key
ssh-keygen -t ed25519 -f /etc/ssh/ssh_host_ed25519_key
```

再试下，就可以了

安装 nginx

创建一个程序用户

```
useradd nginx
```

我们用源码安装的方式，下载源码

```
cd /usr/local/src
wget  http://nginx.org/download/nginx-1.18.0.tar.gz

tar -xvf nginx-1.18.0.tar.gz

cd nginx-1.18.0

./configure --prefix=/usr/local/nginx --with-http_ssl_module 

make && make install
```

修改环境变量

```
echo 'export PATH=/usr/local/nginx/sbin:$PATH' >> /etc/profile 

source /etc/profile
```

修改 nginx 配置

```
cd /usr/local/nginx/conf

mkdir conf.d​ # 创建这个目录，用来放我们的配置文件

vi nginx.conf
```

修改如下三处  
![image.png](/blog/files/images/1eba257512ee8396c210dcaefc1bf86a.png "image.png")

```
user nginx;
daemon off;
include /usr/local/nginx/conf/conf.d/*.conf;
```

安装 php

```
cd /usr/local/src
wget https://www.php.net/distributions/php-7.3.18.tar.bz2

tar -xvf php-7.3.18.tar.bz2

cd php-7.3.18

./configure --prefix=/usr/local/php --enable-fpm --with-fpm-user=nginx --with-fpm-group=nginx --enable-inline-optimization --enable-shared --enable-soap --enabbe-opcache --enable-pdo --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-libzip --with-zlib --with-openssl --with-curl --enable-mbstring

make && make install

```

修改环境变量

```
echo 'export PATH=/usr/local/php/bin:$PATH' >> /etc/profile 

source /etc/profile
```

修改 php 配置

```
cd /usr/local/php/etc
cp php-fpm.conf.default cp php-fpm.conf
vi php-fpm.conf

# 找到下面这行，把前面的注释打开
pid = run/php-fpm.pid

cd /usr/local/php/etc/php-fpm.d/
cp www.conf.default www.conf
```

安装 supervisor

直接用 yum 安装的方式

```
yum install -y epel-release
yum install -y supervisor
```

修改配置文件

```
vi /etc/supervisord.conf

# 如下几行注释打开，这了是为了作 web 的方式管理 supervisor，修改port为 port=0.0.0.0:9001 ，username 和 password 修改为你自己的
[inet_http_server]         ; inet (TCP) server disabled by default
port=0.0.0.0:9001        ; (ip_address:port specifier, *:port for all iface)
username=user              ; (default is no username (open server))
password=123               ; (default is no password (open server))

# 找到 nodaemon ，改为 true (因为 docker 本身就是个服务，里面不允许再说其他常驻服务，所有的应用同理)
nodaemon=true
```

配置 sshd 启动服务

```
cd /etc/supervisord.d/

vi sshd.ini

# 填加如下内容，保存就好
[program:sshd]
command=/usr/sbin/sshd -D

```

配置nginx + fpm 启动服务

```
vi webserver.ini

# 填加如下内容，保存
[program:nginx]
command=/usr/local/nginx/sbin/nginx
stopsignal=QUIT

[program:php-fpm]
command=/usr/local/php/sbin/php-fpm  --nodaemonize
stopsignal=QUIT
```

**提交镜像**

到这里我们镜像就更新完全了，我们把这个容器作为镜像保存

```
docker commit -m="nginx+fpm" -a="actors315" ab3d851192b2 actors315/webdev:v1
```

- -m 提交的描述信息
- -a 作者
- ab3d851192b2 我们的 CONTAINER ID
- actors315/nginx-fpm:v1 镜像名称

```
docker images
```

会发现我们的镜像已经建好了

**使用镜像**

```
docker run --name lingyin-dev -p 9001:9001 -p 2222:22 -p 80:80 -p 443:443 -v F:\www:/data/www -itd actors315/webdev:v1 /usr/bin/supervisord
```

- --name 指定容器名称
- -p 本机和容器端口映射,可以有多个
- -v 挂载目录，可以有多个

查看一下效果

```
docker exec -it /bin/bash
ps -ef|grep sshd
ps -ef|grep nginx
ps -ef|grep php-fpm
```

浏览器访问 <http://127.0.0.1>:9001/ 也可以看到服务都正常启动了

访问 <http://127.0.0.1/> 熟悉的 nginx 欢迎页面。

到这里就大功告成了。

如果你想共享镜像，也可以提交到公共仓库

```
docker push actors315/webdev:v1
```
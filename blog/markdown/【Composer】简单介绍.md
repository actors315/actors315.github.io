---  
layout: post  
title: '【Composer】简单介绍'  
date: 2018-03-26T11:30:31+08:00  
excerpt: 'Composer 是什么
Composer 是一个依赖管理工具，它允许你在项目中声明所有依赖的代码库，并且通过简单的命令安装它们。通常这些依赖库会安装在一个叫"vendor"的目录。
现在绝大部分PH'  
key: 'a5009fc592730fdb7b4a153fcdcd0755'  
---  

**Composer 是什么**

Composer 是一个依赖管理工具，它允许你在项目中声明所有依赖的代码库，并且通过简单的命令安装它们。通常这些依赖库会安装在一个叫"vendor"的目录。

现在绝大部分PHP开源的项目都提供了Composer的支持，建议大家在项目中使 Composer来解决PHP代码包管理的问题，不要再使用下载源码、手工include的原始方法

**Composer 有什么用**

1.管理包依赖,管理版本

2.实现自动加载

3.支持事件处理

4.等等

**Composer 如何使用**

***安装***

windows

即然用windows，那么建议直接下载这个可执行文件[Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe)来安装，它将为你下载最新的composer版本，并为你配置好环境变量。

linux等\*nix系列

建议你全局安装它

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer 
cd /usr/local/bin
chmod a+x composer 
```

如果你没有权限，可以改用root账号或者sudo

***在项目中使用 Composer***

1.首先需要为项目创建一个 composer.json 文件

我们用命令行的方式 ，下面所有步骤都是在项目根目录操作

```
composer init
```

依次为出行如下提示

![composer init](/blog/files/images/418359f28bfa3c95cc6ab56118017d81.jpg "composer init")

然后我们就生成了一个composer.json文件，内容如下

```
{
    "name": "test/test",
    "description": "this is a test",
    "type": "library",
    "authors": [
        {
            "name": "xiehuanjin",
            "email": "xiehuanjin@globalegrow.com"
        }
    ],
    "require": {}
}
```

这一步并不是必须的，也可以手动在项目根目录创建一个名叫composer.json的文件，文件内容为一对大括号{}

2.安装依赖的包，以predis为例

```
composer rquire --prefer-dist predis/predis
```

这个命令自动为你下载predis的稳定版本  
\--prefer-dist 尽可能从dist获取，下载稳定版本  
\--prefer-source 尽可能从source获取，下载最新代码

3.自动加载

使用 composer 你不需要到处使用require include各种依赖文件，你只需要在项目引导文件中require这个文件就好了

```
require 'vendor/autoload.php';
```

4.其他

上面演示的是安装一个现成的库。然后实际开发过程中，有些功能并不能在公开的库找到支持，需要自行开发。那怎么引入自己的库呢。

***我们假设自行自行开发的库符合psr4规范（这不是必须，但是建议）***

回到composer.json文件，我们只需要在该文件填加autoload配置

```
{
    "name": "test/test",
    "description": "this is a test",
    "type": "library",
    "authors": [
        {
            "name": "xiehuanjin",
            "email": "xiehuanjin@globalegrow.com"
        }
    ],
    "require": {},
    "autoload": {
        "psr-4": {
            "test\\client\\": "client/", 
            "test\\service\\": "service/"
        }
    }
}
```

为这个库提供autoload支持

```
composer dump-autoload
```
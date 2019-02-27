---  
layout: post  
type: blog  
author: abc1035331062
title: 'windows 使用 zephir 开发 PHP 扩展'  
date: 2019-02-26 06:14:50  
excerpt: 'windows 使用 zephir 开发 PHP 扩展'  
key: 9b0e75f4497a42e1d461649990bdb4c2  
---  

文章转载自 https://blog.csdn.net/abc1035331062/article/details/87930562

**windows软件准备**

wamp 集成环境，扩展安装 php5.6.40 （也可以用编译安装的目录 php.exe） ，教程>>><https://blog.csdn.net/u011242029/article/details/80058770>

php-sdk-binary-tools-20110915.zip （http://windows.php.net/downloads/php-sdk/ 下载）

deps-5.6-vc11-x86.7z （http://windows.php.net/downloads/php-sdk/ 下载）

php-src-5.6.40 （纯源码） (http://php.net/downloads.php 或 git <https://github.com/php/php-src/tree/PHP-5.6.40> 下载，这个版本不定自己选择)

Zephir 解析器 .dll <https://github.com/phalcon/php-zephir-parser/releases/tag/v1.1.1>

[ Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe) <https://getcomposer.org/Composer-Setup.exe>

Visual Studio 2012

<span style="color:#f33b45;">注意：以上软件都是32位x86的，php 是ts 安全模式的，如要要（64位x86 或 nts 版本需要下载一致版本的软件） </span>

**第一步**

安装 Visual Studio 2012 （PHP5.5 或 PHP5.6 使用这个，PHP5.6+ 用 Visual Studio 2015 ），

一直点下一步等待漫长的安装

**第二步**

安装 composer 、随意建一个文件夹如php，

安装这个前需要吧wamp PHP5.6.40路径加入环境变量

```
c:\
cd php
composer require phalcon/zephir:dev-master
```

设置环境变量安装zephir

```
C:\php\vendor\bin 
或者命令行
setx path "%path%;C:\php\vendor\bin\"
```

**第三步**

```
解压php-sdk-binary-tools的二进制包，
 
设置环境setx php_sdk "c:\php-sdk"
 
譬如我解压到我的C:\php-sdk文件夹中，现在的目录结构如下
```

```
C:\php-sdk
    --bin
    --script
    --share
```

```
然后，这个是你已经安装完成了visual studio 2012,
打开VS2012 Native Tools Command Prompt命令行工具
```

```
#进入目录
cd C:\php-sdk
#设置环境变量
bin\phpsdk_setvars.bat
#创建常用的php-sdk目录
bin\phpsdk_buildtree.bat phpdev
```

```
如果我们打开bin\phpsdk_buildtree.bat
文件就会发现它只创建到VC9，没有VC11，
但是我们如果开发php5.5或5.6版本，
我们需要的是VC11,这时候我们就要把
C:\php-sdk\phpdev\vc9 复制一份到
C:\php-sdk\phpdev\vc11，
如果是5.6版本+ 
C:\php-sdk\phpdev\vc9 复制一份到
C:\php-sdk\phpdev\vc14， 
现在的目录结构如下:
```

```
C:\php-sdk\phpdev\
                --vc6
                --vc8
                --vc9
                --vc11
```

**第四步**

安装 deps 和 PHP 源码

```
因为我下载的deps-5.6-vc11-x86.7z，
所以我要解压deps-5.6-vc11-x86.7z 到C:\php-sdk\phpdev\vc11\x86\deps文件夹下覆盖，
里边都是我们需要的库文件和一些必要的工具等等。
 
然后，将我们下载的php-src-5.6.40.zip
解压到C:\php-sdk\phpdev\vc11\x86\php-5.6.40文件夹中。
```

下载安装 [php\_zephir\_parser.dll](https://github.com/phalcon/php-zephir-parser/releases/tag/v1.1.1)，下载已编译好的ts 版本 zephir\_parser\_x86\_vc11\_php5.5\_1.1.1-268.zip

[也可以自己编译安装 ](https://github.com/phalcon/php-zephir-parser/blob/1.1.x/README.WIN32-BUILD-SYSTEM)

在php配置文件php.ini

```
extension=php_zephir_parser.dll
 
使用命令行
php -m
查看扩展Zephir Parser 是否安装成功
```

**第五步**

编写源代码，编译

命令行，创建一个项目

```
进入任意目录
zephir init widuu
```

进入 widuu/widuu 文件夹，建立一个 `service.zep` 文件。

编写源代码，如下，就是上一篇文章的例子

```
namespace Widuu;
 
class Service{
 
    protected _service;
 
    public  function _set(string name,object obj) -> int{
        if (typeof obj != "object") {
            throw new \Exception("type error!!");
        }
        let this->_service[name] = obj;
        return 1;
    } 
 
    public  function _get(string name){
        if (!isset this->_service[name]) {
            return 0;
        }
        return this->_service[name];
    }
 
    public  function _del(string name){
        let this->_service[name] = null;
    }
 
}
```

 编译安装

```
cd ..  # 这里指，我们用zephir 创建的 widuu 目录
zephir generate 
```

<span style="color:#f33b45;"> 将 ext 文件夹，复制到开发环境中的扩展目录（C:\\php-sdk\\phpdev\\vc11\\x86\\php-5.6.40\\ext 也是存扩展源码的位置），并重新命名为 widuu。</span>

回到VS2012 Native Tools Command Prompt 命令行

```
#进入php源目录文件夹
C:\php-sdk\phpdev\vc11\x86\php-5.6.40\
buildconf --force
 
#查看带的扩展和编译命令
configure --help
 
configure --disable-all --enable-cli --enable-widuu=shared 
 
#然后，你会看到Type 'nmake' to build PHP，然后编译
 
nmake
```

编译完成后，我们就在 ` C:\php-sdk\phpdev\vc11\x86\php-5.6.40\Release_TS` 目录中看到了 `php_widuu.dll` 动态链接库了，然后放到我们的 php 的 `ext` 文件夹中，并在 `php.ini` 中加入

```
extension = php_widuu.dll
```

**第六步**

重启你的环境，测试，用上一篇文章的代码测试，如下

```
<?php
 
class string{
 
    public function test(){
        echo "hello word";
    }
}
 
$service = new Widuu\Service();
// 故意写错类型
try{
    $service->_set('string',"222");
}catch(Exception $e){
    echo $e->getMessage();
}
// 注册服务
$service->_set('string',new string());
// 获取对象
$s = $service->_get('string');
// 测试
$s->test();      
```

测试成功

```
type error! hello word

```

注：谢谢 http://www.widuu.com/archives/12/1150.html 分享。
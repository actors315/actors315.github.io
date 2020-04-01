---  
layout: post  
type: blog  
title: '【PHP 扩展开发】Hello World'  
date: 2019-03-05T00:06:44+08:00  
excerpt: '前面两篇介绍了 Zephir ，从此可以开发自己的扩展了，可毕竟是通过转换而来，虽然很方便，可对于扩展还是一知半解，也无法判断其好坏，所以还是要了解下用原生的方法是怎么开发一个 PHP 扩展的。
本文'  
key: 784925790878f7df23df805a1dc61a28  
---  

前面两篇介绍了 Zephir ，从此可以开发自己的扩展了，可毕竟是通过转换而来，虽然很方便，可对于扩展还是一知半解，也无法判断其好坏，所以还是要了解下用原生的方法是怎么开发一个 PHP 扩展的。

本文以 php-7.2.15 为例

**创建扩展骨架**

```
# 源码路径
cd /usr/local/src/php-7.2.15/ext 
./ext_skel --extname=twinkle_log
```

提示如下

![clipboard.png](/blog/files/images/fcf8b5237b4691315d95222294d23aed.png "clipboard.png")

提示已创建好基础文件，如果想使用这个新扩展，需要噼里啪啦这么些布骤，先记着就好。

**修改config.m4**

```
cd twinkle_log
vi config.m4
```

![clipboard.png](/blog/files/images/da3861a861078a763b2396729da1e6a0.png "clipboard.png")

这两块注释选一个先打开，具体含义后面文章再说明，我们选下面的

**创建自己的方法**

```
vi twinkle_log.c

```

![clipboard.png](/blog/files/images/f0c38d4b0c273b43057df3cc7aa571c8.png "clipboard.png")

声明该方法

![clipboard.png](/blog/files/images/58925a53ff7e2cbd5c764e0ec5de76f4.png "clipboard.png")

注意大小写敏感

**编译安装**

```
/usr/local/php72/bin/phpize
./configure --with-php-config=/usr/local/php72/bin/php-config
make 
make install
```

**填加扩展**

```
vi /usr/local/php72/lib/php.ini
# 填加扩展 extension=twinkle_log
```

**测试一下**

![clipboard.png](/blog/files/images/b292bf76ff52d771828085d6c904d105.png "clipboard.png")

扩展安装成功

```
<?php

// test.php

hello_world();
```

执行脚本

```
php -f test.php
```

![clipboard.png](/blog/files/images/d9987ede604395f946f585ad073f16be.png "clipboard.png")

执行成功。

这样我们就创建了一个非常简单的扩展，虽是很简单的扩展，可还是比 Zephir 复杂很多。

当然他啥用也没有，没有涉汲到内存管理，没有使用指针，做了这一些操作，也不知道是啥意思，带着疑问，我们慢慢深入研究。
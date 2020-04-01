---  
layout: post  
type: blog  
title: 'GDB 简介'  
date: 2019-04-02T01:15:05+08:00  
excerpt: 'GDB 是什么
GDB 是 linux 环境下的一般功能强大的调试器，用来调试 C 或 C++ 写的程序。它可以做这些事情

Start your program, specifying anythi'  
key: 743469753f7caefded3e8486d0f594a0  
---  

**GDB 是什么**

GDB 是 linux 环境下的一般功能强大的调试器，用来调试 C 或 C++ 写的程序。它可以做这些事情

> 1. Start your program, specifying anything that might affect its behavior.
> 2. Make your program stop on specified conditions.
> 3. Examine what has happened, when your program has stopped.
> 4. Change things in your program, so you can experiment with correcting the effects of one bug and go on to learn about another.

**安装**

以 centos 7 为例，安装 GDB-8.2.1 版本

***安装依赖***

C++ 11 编译器和 GUN make 是 GDB 必要的工具包，需先安装他们。

```
yum install gcc*
-- 或者为了省事，直接把开发常用的工具包都安装了
yum group install "Development Tools"

-- 查看是否安装成功
gcc -v # 需 4.8 以上版本
```

gun make [官网地址](http://www.gnu.org/software/make/)

***安装 GDB***

在[官网](http://www.gnu.org/software/gdb/download/)上找到官方的 FTP 仓库，下载最新的版本，解压后开始安装

```
./configure --prefix=/usr/local/gdb821
make && make install

安装完成将 /usr/local/gdb821/bin 添加到 PATH 环境变量

-- 也可以直接用 yum 安装

yum install -y gdb

```

安装需要花比较久时间，可以洗个澡干点爱干的事。

```
gdb -v
```

**使用示例**

一个简单的示例，运行一个 PHP 脚本 gdb\_test.php

```
<?php

for($i = 0; $i < 10; $i++){
    if(in_array($i,[1,9,20])){                                                                                                        
        echo $i*$i,PHP_EOL;
    }      
}
```

开始调试

```
gdb php

> run /usr/local/src/gdb_test.php
```

![clipboard.png](/blog/files/images/5cada75e6037adb9d929859f3f2737b6.png "clipboard.png")

这样脚本就执行成功了，实验了第一个命令。

后续再详细学习断点，单步调试等真正的实验。

**附：GDB 常用命令**

1. backtrace：显示栈信息。简写为bt。
2. frame x 切换到第x帧。其中x会在bt命令中显示，从0开始。0表示栈顶。简写为f。
3. up/down x 往栈顶/栈底移动x帧。当不输入x时，默认为1。
4. print x打印x的信息，x可以是变量，也可以是对象或者数组。简写为p。
5. print \*/&amp;x 打印x的内容/地址。
6. call 调用函数。注意此命令需要一个正在运行的程序。
7. set substitute-path from\_path to\_path，替换源码文件路径。当编译机与运行程序的机器代码路径不同时，需要使用该指令替换代码路径，否则你无法在gdb中看到源码。
8. break x.cpp:n 在x.cpp的第n行设置断点，然后gdb会给出断点编号m。命令可简写为b。后面会对break命令进行更详细的解释。
9. continue 继续运行程序。进入调试模式后，若你已经获取了你需要的信息或者需要程序继续运行时使用。可简写为c
10. until 执行到当前循环完成。可简写为u
11. step 单步调试，步入当前函数。可简写为s
12. next 单步调试，步过当前函数。可简写为n
13. finish 执行到当前函数返回
14. set var x=10 改变当前变量x的值。也可以这样用：set {int}0x83040 = 10把内存地址0x83040的值强制转换为int并赋值为10
15. info locals 打印当前栈帧的本地变量
16. jump使当前执行的程序跳转到某一行，或者跳转到某个地址。由于只会使程序跳转而不会改变栈值，因此若跳出函数到另外的地方 会导致return出错。另外，熟悉汇编的人都知道，程序运行时，有一个寄存器用于保存当前代码所在的内存地址。所以，jump命令也就是改变了这个寄存器中的值。于是，你可以使用“set $pc”来更改跳转执行的地址。如： set $pc = 0x485
17. return: 强制函数返回。可以指定返回值
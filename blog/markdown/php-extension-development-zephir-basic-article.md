---  
layout: post  
type: blog  
title: '【PHP 扩展开发】Zephir 基础篇'  
date: 2019-03-01T01:16:02+08:00  
excerpt: '上一篇 《Zephir 简介》 简单介绍了环境搭建，编写了一个的简单示例。这一篇继续介绍 Zephir 基础。
基本语法Zephir 中，每个文件都必须有且只有一个类，每个类都必须有一个命名空间，目录'  
key: 3fb80c0ba68682785f0055d2019186a9  
---  

上一篇 《[Zephir 简介](https://blog.xiehuanjin.cn/blog/markdown/introduction-to-zephir)》 简单介绍了环境搭建，编写了一个的简单示例。这一篇继续介绍 Zephir 基础。

**基本语法**  
Zephir 中，每个文件都必须有且只有一个类，每个类都必须有一个命名空间，目录结构必须与所使用的类和命名空间的名称相匹配，这一点和 PSR4 的约定一致，但是它是强制的。空间名和类名首字母大写，文件名全部小写。

**变量**  
Zephir 支持动态类型和静态类型。变量作为函数返回值时，必须声明为动态类型。

动态变量与 PHP 中的变量基本完全相同,支持在使用时改变类型。

![clipboard.png](/blog/files/images/3a73acc80cf163accebfa006d1a7e3ea.png "clipboard.png")

与 PHP 不一样，变量名不需要带$符号,所有变量在使用前都需要先定义，动态类型通过 var 关键字声明。

```
var a = 10,b,c;
let b = 20;
let c = a + b;
```

与 PHP 不一样，字符串文字只能使用双引号指定，不能用单引号，也不支持变量解析，比如这样是不支持的。

```
var a = "变量解析{$test}"
```

静态变量

静态类型一旦被声明，就不能更改。支持如下类型

![clipboard.png](/blog/files/images/650f905df1e6c59a06aeee7c2c1d229e.png "clipboard.png")

给静态类型变量赋值，会先尝试自动转换，转换失败抛出异常

```
boolean a;
let a = 0; // a = false
let a = "string"; // 抛出异常
```

**运算符**

Zephir 的运算符与 PHP 基本一致。比较运算符在运算时考虑变量类型，如果是动态变量与 PHP 一致。不支持太空船操作符、空合并运算符。

特殊运算符 - Fetch

PHP

```
if (isset($myArray[$key])) {
    $value = $myArray[$key];
    echo $value;
}
```

Zephir

```
if fetch value, myArray[key] {
    echo value;
}
```

**控制结构**  
Zephir 提供了 if/switch/while/loop/for 几种语句,前三种与 PHP 基本一致。Zephir 控制语句中括号是非必选的。  
loop 可以用来创建无限循环，相当于 while true

```
let n = 40;
loop {
    let n -= 2;
    if n % 5 == 0 { break; }
    echo x, "\n";
}
```

for 和 PHP 一样支持索引 value 和 key => value

```
for item in ["a", "b", "c", "d"] {
    echo item, "\n";
}

let items = ["a": 1, "b": 2, "c": 3, "d": 4];

for key, value in items {
    echo key, " ", value, "\n";
}
```

还非常友好的提供了反方向遍历

```
let items = [1, 2, 3, 4, 5];

for value in reverse items {
    echo value, "\n";
}
```

与 python 等语言一样，遍历一系列整数值，可以这么写：

```
for i in range(1, 10) {
    echo i, "\n";
}
```

**异常处理**

异常处理与 PHP 类似，在try 语句中抛出异常，在 catch 中捕获。

```
var e;
try {

    throw new \Exception("This is an exception");

} catch \Exception, e {

    echo e->getMessage();
}
```

与 PHP 不一致，Zephir try 语句可以没有 catch，表示忽略所有异常。

即，这是合法的

```
try {
    throw new \Exception("This is an exception");
}
```

捕获多个异常也非常方便

```
var e;
try {

    throw new \Exception("This is an exception");

} catch \RuntimeException|\Exception, e {
    echo e->getMessage();
}
```

**附件1**  
[官方文档](https://docs.zephir-lang.com/0.11/en/welcome)
---  
layout: post  
type: blog  
title: '【php 扩展开发】扩展生成器'  
date: 2019-03-05T12:03:10+08:00  
excerpt: '什么是扩展生成器
每个php扩展都包含一些非常公共的并且非常单调的结构和文件，这些文件对所有扩展来说都是通用的。当开始一个新扩展开发的时候，如果这些公共的结构已经存在，而不需要费力去复制每个文件的内容'  
key: c238a6358cdd7a656dd58325229f27d0  
---  

**什么是扩展生成器**

每个php扩展都包含一些非常公共的并且非常单调的结构和文件，这些文件对所有扩展来说都是通用的。当开始一个新扩展开发的时候，如果这些公共的结构已经存在，而不需要费力去复制每个文件的内容, 我们只需考虑填充功能代码那心情一定会愉快很多。

扩展生成器就是实现这些功能的脚本，帮助我们完成初始化工作。 PHP 源码中提供一个自带的生成器 ext\_skel。他在 ext 目录下。

**ext\_skel**

它是一个 shell 脚本，仅有 300 多行。我们来看下关键部分代码

```
# 生成 config.m4 配置文件

cat >config.m4 <<eof
dnl \$Id\$
dnl config.m4 for extension $extname

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH($extname, for $extname support,
dnl Make sure that the comment is aligned:
dnl [  --with-$extname             Include $extname support])

dnl Otherwise use enable:

PHP_ARG_ENABLE($extname, whether to enable $extname support,
dnl Make sure that the comment is aligned:
[  --enable-$extname           Enable $extname support])

..... 省略一大串
eof

# 生成核心文件

$ECHO_N " $extname.c$ECHO_C"
echo "s/extname/$extname/g" > sedscript
echo "s/EXTNAME/$EXTNAME/g"  >> sedscript
echo '/__function_entries_here__/r function_entries'  >> sedscript
echo '/__function_stubs_here__/r function_stubs'  >> sedscript
echo '/__header_here__/r ../../header'  >> sedscript
echo '/__footer_here__/r ../../footer'  >> sedscript
echo '/__function_entries_here__/D'  >> sedscript
echo '/__function_stubs_here__/D'  >> sedscript
echo '/__header_here__/D'  >> sedscript
echo '/__footer_here__/D'  >> sedscript
if [ ! -z "$no_help" ]; then
    echo "/confirm_$extname_compiled/D" >> sedscript
    echo '/Remove the following/,/^\*\//D' >> sedscript
    echo 's/[[:space:]]\/\*.\+\*\///' >> sedscript
    echo 's/^\/\*.*\*\/$//' >> sedscript
    echo '/^[[:space:]]*\/\*/,/^[[:space:]]*\*\//D' >> sedscript
fi

sed -f sedscript < $skel_dir/skeleton.c > $extname.c

```

帮助说明

```
./ext_skel --help

```

它提示了我们脚本的命令行格式和支持的参数

```
./ext_skel --extname=module [--proto=file] [--stubs=file] [--xml[=file]]
           [--skel=dir] [--full-xml] [--no-help]

  --extname=module   扩展名称，全为小写字母的标识符，仅包含字母和下划线，保证在 php 源码 ext 目录下的文件夹名唯一
  --proto=file       允许开发人员指定一个头文件，由此创建一系列 PHP 函数，表面上看就是要开发基于一个函数库的扩展，很少用
  --stubs=file       仅生成文件中的函数存根，生成 IDE 提示文件可能有用
  --xml              generate xml documentation to be added to phpdoc-svn 没用
  --skel=dir         path to the skeleton directory 指定扩展骨架目录，如果你想在 ext 目录以外的地方生成，那这个有用
  --full-xml         generate xml documentation for a self-contained extension (not yet implemented) 没用
  --no-help          don't try to be nice and create comments in the code and helper functions to test if the module compiled 去除生成测试函数和注释等内容，除非你很熟练，不建议操作
```

**示例**

```
/usr/local/src/php-7.2.15/ext/ext_skel --extname=twinkle_log --skel=/usr/local/src/php-7.2.15/ext/skeleton/
```
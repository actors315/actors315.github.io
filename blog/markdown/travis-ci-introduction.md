---  
layout: post  
type: blog  
title: 'Travis CI 简介'  
date: 2019-01-14T22:46:32+08:00  
excerpt: '什么是Travis CI
Travis CI 是目前新兴的开源持续集成服务，它能帮助你在代码变化时自动构建、测试（当然你需求提供测试脚本）、部署。
它是一个在线工具，不需要额外部署，支持大部分主流语言'  
key: bb800b68ec4217869667407a8c1470f6  
---  

**什么是Travis CI**

Travis CI 是目前新兴的开源持续集成服务，它能帮助你在代码变化时自动构建、测试（当然你需求提供测试脚本）、部署。

它是一个在线工具，不需要额外部署，支持大部分主流语言，更重要的是对于开源项目它还免费。

**如何使用**

***先决条件***

> To start using Travis CI, make sure you have:  
> A GitHub account.  
> Owner permissions for a project hosted on GitHub.

目前 Travis CI 只支持 Github，所以你需要一个 Github 帐号（全球最大同性交友论坛你都没帐号还怎么混），并且你还需要有项目的 Owner 权限。

***开始使用***

1.注册 travis-ci 帐号

travis-ci 分为[免费](https://travis-ci.org/)和[收费](https://travis-ci.com/)两个版本，这里以开源项目为例，所以用免费版本就好了。

访问免费版 [travis-ci.org](https://travis-ci.org) ，点击SIGN UP，用 Github 账号登录。

2.选择仓库

同意授权，travis-ci 会列出你所有的仓库，选择需要 travis-ci 帮你持续集成的仓库。如下图打开开关激活

![clipboard.png](/blog/files/images/4847f3c4047fc71099dc1baf0fa13a7c.png "clipboard.png")

这样 travis-ci 就会帮你监听这个仓库的所有变化自动构建，完成预定的操作。

3.填加 .travis.yml

travis-ci 必须要有这个文件，文件需放在根目录。它是一个 yaml 格式的配置文件，定义预定的命令，用来告诉 travis-ci 做什么，怎么做。

```
language: php
php:
  - '7.2'

script: true
```

这是一个最简单的示例，指定了项目的语言为 php ,版本为7.2，

script 是执行脚本，true 表示什么也不做，直接返回成功（当然我们实际使用时肯定不会这么用 ）。

**自动构建**

完成上面的操作后，你的每一次提交，travis-ci 自动构建执行配置好的预定义操作了

![clipboard.png](/blog/files/images/d60fddf2a10f320b38382085305a9657.png "clipboard.png")

如果你想创建一个提交，又不想自动 build，你可以指定\[skip <keyword>\] 关键字,比如

```
git commit -m "[skip travis] auto build by travis-ci"
```

**引用**

[官方指引](https://docs.travis-ci.com/user/tutorial/)
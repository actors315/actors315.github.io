---  
layout: post  
type: blog  
title: '【Github Pages】如何被百度收录'  
date: 2019-02-18T00:10:19+08:00  
excerpt: 'Github Pages 如何被百度收录
答案是无法收录
创建 Github Page 一个多月了，发现百度索引量依然为0。利用百度自带的抓取诊断工具诊断发现，所有抓取记录都是失败，状态码为403。
'  
key: 26257dd5812cc3e33c35f9677934eb2e  
---  

**Github Pages 如何被百度收录**

答案是无法收录

创建 Github Page 一个多月了，发现百度索引量依然为0。利用百度自带的抓取诊断工具诊断发现，所有抓取记录都是失败，状态码为403。

![clipboard.png](/blog/files/images/18a011830d0de8e7b89e03fb9df7690c.png "clipboard.png")

判断为 Github 屏蔽了百度蜘蛛。

**怎么搞才能被收录**

***更换托管服务商***

**使用 coding.net 自动同步代码**

我们选择 Github Page 的重要原因之一是它免费，所以假定我们是没有自己的服务器的，所以我们选择一个同样免费的托管服务商 [coding.net](https://coding.net/), 创建一个 Github pages 的镜像

首先我们访问 [coding.net](https://coding.net/)，创建账号，因为我们要使用 Pages 服务，需要升级一下账户为腾腾讯云开发者平台。(当然也可以直接创建 [腾讯云开发者平台](https://dev.tencent.com)账号)  
然后参考文档 [如何将Git仓库导入腾讯云开发者平台？](https://dev.tencent.com/help/git-import-tencentcloud)，把代码同步过来。

手动操作总是麻烦，尤其像我的博客还是从 segmentfault 自动同步的。所以接着前面文章的介绍，利用 travis-ci 来自动同步，.travis.yml 填加如下代码

```
# 同步到coding.net
- git push https://xiehuanjin:$CODING_NET_TOKEN@git.dev.tencent.com/xiehuanjin/actors315.github.io.git --all
```

CODING\_NET\_TOKEN 为 travis-ci 变量，值为在腾讯云开放平台创建的访问令牌，只要有仓库控制权限就可以。

**DNSPOD 解析域名**

主战场还是 Github，coding 只是做个镜像，所以并不把博客完全迁移过来，只是针对百度蜘蛛让其访问到 coding.net。我们利用 [dnspod](https://www.dnspod.cn) 来进行域名解析（当然是因为其免费还很好用），对百度线路进行单独解析。

![clipboard.png](/blog/files/images/e352aa358e8c516f856ccc6286ce6f8b.png "clipboard.png")

设置完毕，再来诊断一下

![clipboard.png](/blog/files/images/a16e1700fdb002a8083c0684c36f8ae5.png "clipboard.png")

大功告成
---  
layout: post  
type: blog  
title: '【Github Pages】徒手实现分页'  
date: 2019-01-31T22:14:24+08:00  
excerpt: 'Github Pages
Github Pages 是 Github 免费提供的静态网站生成器，你可以利用其创建个人、企业、项目网站。其提供静态页面托管服务和一个二级域名，也可以绑定独立域名。
可以很'  
key: fe8b62d662e430f0f5bc9305030f260d  
---  

**Github Pages**

Github Pages 是 Github **免费**提供的静态网站生成器，你可以利用其创建个人、企业、项目网站。其提供静态页面托管服务和一个二级域名，也可以绑定独立域名。

可以很轻易的找到其介绍和如何构建自己的 Github Pages,就不详细介绍了

可以参考这些内容

[What is GitHub Pages?](https://help.github.com/articles/what-is-github-pages/)  
[Using Jekyll as a static site generator with GitHub Pages](https://help.github.com/articles/using-jekyll-as-a-static-site-generator-with-github-pages/)  
[使用 github pages, 快速部署你的静态网页](https://blog.csdn.net/baidu_25464429/article/details/80805237)  
[github pages搭建个人博客](https://www.jianshu.com/p/835a8d6514fa)

**分页**

不管是个人博客还是其他主页，内容多了就需要分页展示文章列表。

我们选择 Jekyll 作为页面生成器来管理页面。

Jekyll 提供了分页功能，使用上也很方便。只需要 \_config.yml 文件填加分页配置就可以直接用了。

```
plugins: [jekyll-paginate]
paginate: 20 # 每页文章数
paginate_path: "essay/page:num" # 可选，分页链接
```

然后在 index.html 直接写上如下代码就会自动生成分页目录

```
<ul>
    {% for post in paginator.posts %}
    <li><a href="{{ post.url }}">{{ post.title }}</a></li>
    {% endfor %}
</ul>

<nav class="pagination" role="navigation">
    {% if paginator.previous_page %}
    <a class="previous pagination__newer btn btn-small btn-tertiary" href="{{ paginator.previous_page_path }}">&larr; 上一页</a>
    {% endif %}
    <span class="page_num pagination__page-number">{{ paginator.page }} / {{ paginator.total_pages }}</span>
    {% if paginator.next_page %}
    <a class="next pagination__older btn btn-small btn-tertiary" href="{{ paginator.next_page_path }}">下一页 &rarr;</a>
    {% endif %}
</nav>

```

jekyll 会自动生成如下目录

![clipboard.png](/blog/files/images/f50a73ecd0059701ceda96ffdf21e4d1.png "clipboard.png")

效果如下

![clipboard.png](/blog/files/images/f99487378e46688d2c4d791b2da87438.png "clipboard.png")

非常方便，但是它也有很多明显的缺点。比如他只支持 \_posts 目录下的文章进行自动生成，很大时候就不一定能满足需求了。

**自定义分页**

如上面所说，不想把所有文章都放到 \_posts 一个目录。比如我想到放在独立的 blog 目录

![clipboard.png](/blog/files/images/b291139a7f9195f1b85f2a63c443fb84.png "clipboard.png")

那怎么实现目录的自动生成和分页呢？借助 [Data Files](https://jekyllrb.com/docs/datafiles/) 和 PHP 来手动生成分页。

\_data/blogList.yml 定义如下列表

```
 - key: 6a1b96bda21c937f01a7591ec3e84223
   title: PHP实现一个轻量级容器
   next: Travis CI 实现自动备份Segmentfault文章到Github
 - key: 13ee9e07ce28d6310eb5fec64404fa24
   title: Travis CI 实现自动备份Segmentfault文章到Github
   prev: PHP实现一个轻量级容器
   next: Travis CI 简介
 - key: bb800b68ec4217869667407a8c1470f6
   title: Travis CI 简介
   prev: Travis CI 实现自动备份Segmentfault文章到Github
   next: 【php实现数据结构】链式队列
```

在子目录另外定义一个 page.html 模板文件

```
---
layout: list
type: customList
title: 我的博客
page: 1
total_pages: 100
prev_page_path: none
next_page_path: none
---

<p>同步自segmentfault(https://segmentfault.com/blog/actors315)</p>

<h2 id="目录">目录</h2>

<ul>
    {% for member in site.data.blogList limit:20 offset:#offset# %}
    <li><a href="/blog/markdown/{{ member.title }}">{{ member.title }}</a></li>
    {% endfor %}
</ul>
```

利用 data 的逻辑处理能力手动实现，然后 php 自动任务手动生成和 jekyll 自助目录同样的结构。

```
$totalCount = count($list);
$totalPage = ceil($totalCount / 20);

for ($i = 1; $i <= $totalPage; $i++) {
    if ($i == 1) {
        $tempFile = __DIR__ . "/../blog/index.html";
    } else {
        $tempFile = __DIR__ . "/../blog/page{$i}/index.html";
    }

    $newPage = false;
    if (file_exists($tempFile)) {
        $tempContent = file_get_contents($tempFile);
    } else {
        $tempContent = file_get_contents(__DIR__ . "/../blog/page.html");
        $newPage = true;
        if (!is_dir($dir = dirname($tempFile))) {
            mkdir($dir, 0777, true);
        }
    }
    $tempContent = preg_replace('/page:[\s]*\d+[^\d]/', "page: {$i}" . PHP_EOL, $tempContent);
    $tempContent = preg_replace('/total_pages:[\s]*\d+[^\d]/', "total_pages: {$totalPage}" . PHP_EOL, $tempContent);
    if ($i == 2) {
        $tempContent = preg_replace('/prev_page_path:[\s]*[^\s]+[\s]*?/', "prev_page_path: /blog/", $tempContent);
    } elseif ($i > 2) {
        $prev = $i - 1;
        $tempContent = preg_replace('/prev_page_path:[\s]*[^\s]+[\s]*?/', "prev_page_path: /blog/page{$prev}/", $tempContent);
    }

    if ($i < $totalPage) {
        $next = $i + 1;
        $tempContent = preg_replace('/next_page_path:[\s]*[^\s]+[\s]*?/', "next_page_path: /blog/page{$next}/", $tempContent);
    } elseif ($i == $totalPage) {
        $tempContent = preg_replace('/next_page_path:[\s]*[^\s]+[\s]*?/', "next_page_path: none", $tempContent);
    }

    if ($newPage) {
        $tempContent = str_replace('#offset#', ($i - 1) * 20, $tempContent);
    }

    file_put_contents($tempFile, $tempContent);
}
```

这里需要配合后端代码，Github Pages 目前是不支持动态语言的，所以需要借助其他能力，可以参考之篇文章的介绍 《[Travis CI 实现自动备份Segmentfault文章到Github](https://blog.xiehuanjin.cn/blog/markdown/Travis%20CI%20%E5%AE%9E%E7%8E%B0%E8%87%AA%E5%8A%A8%E5%A4%87%E4%BB%BDSegmentfault%E6%96%87%E7%AB%A0%E5%88%B0Github)》

这样就徒手实现了一个分页功能，并且可以根据自己的需要随心所欲，你的分页你作主。

详细实现可参考我的Github 页面 [呜啦啦的碎碎念](https://github.com/actors315/actors315.github.io)
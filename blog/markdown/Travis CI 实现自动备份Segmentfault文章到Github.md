[上一篇](https://segmentfault.com/a/1190000017891810)简单介绍了 Travis CI, 这里再简单介绍一个应用，利用Travis CI的Cron Jobs功能，实现自动备份segmentfault文章到Github。

**第一步：抓取文章内容**

Segmentfault 非常友好的为每个专栏提供了一个RSS，我们很方便就可以抓取到文章内容，稍做转化就可以了，省略不写，[详见代码](https://github.com/actors315/actors315.github.io/blob/master/bin/console.php)

**第二步：配置 SSH keys**

Github 上有详细的说明，省略不写，[敬请查看](https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/)

![clipboard.png](/blog/files/images/3aaa3b5b69dc07bee48e9f22e48949dc.png "clipboard.png")

**第三步：编写.travis.yml 配置**

我们采用git命令行提交commits的方式备份文章。

要实现自动提交，需要把私钥也和代码一起上传，但是私钥泄漏相当于放弃了仓库的所有权，所以需要对私钥进行加密。（这是个比较麻烦的方法，但是实际上开发中我们部署代码到自己的服务器常用的也是 ssh 远程操作，有借鉴意义）

***1.安装 travis***

我们通过 Ruby 的 gem 包管理器来安装 travis, 如果未安装 Ruby,先[下载](http://www.ruby-lang.org)最新版本，并安装它，然后执行如下命令确认 gem 是否安装成功

```
gem -v
```

能够正常列出版本号，表示安装成功

因为众所周知不可说的原因，在安装包时，使用官方源地址会非常慢。我们切换成国内的镜像

```
gem sources --add https://gems.ruby-china.com/ --remove https://rubygems.org/ # 老版本 Ruby 这里会证书相关的错误，所以确认安装了最新版本

gem sources -l #确保列出的源地址只有镜像地址
```

准备工作做好了，我们执行如下命令安装

```
gem install travis
```

***2.登录 Travis CI***

```
travis login --auto
```

执行命令，根据提示输入 github 的用户名和密码。

这里有个坑，window 用 cmd 操作时死活不好使，如果你在输入密码时显示的明文，那么恭喜你也踩坑了，你可以改用 power shell 来操作，不过后面还是会遇到一些莫名其妙的问题，建议在 \*inux 环境下操作，哪怕是虚拟机也行。

***3.填加私钥***

执行如下命令

```
travis encrypt-file ~/.ssh/id_rsa --add
```

实际使用中很有可能会报错

> Can't figure out GitHub repo name. Ensure you're in the repo directory, or specify the repo name via the -r option (e.g. travis <command> -r <owner>/<repo>)

这个的意思是找不到仓库，需要指定，可以这么操作

```
travis encrypt-file -r actors315/actors315.github.io ~/.ssh/id_rsa --add
```

执行完成后，在 .travis.yml 文件中会自动填加如下代码

```
openssl aes-256-cbc -K $encrypted_xxxxxx_key -iv $encrypted_xxxxxxx_iv -in .travis/id_rsa.enc -out ~/.ssh/id_rsa -d
```

并生成一个 id\_rsa.enc 文件，你可以手动移到合适的目录，并调整上面命令文件的位置

并在 Travis CI Setting 页面会看到增加了如下两个变量

![clipboard.png](/blog/files/images/89451961de7451e585291e6f52ac6fa2.png "clipboard.png")

如果在 windows 下生成的文件，travis-ci 构建时很可能会报这个错。

> 0.02s$ openssl aes-256-cbc -K $encrypted\_2805aa35fedb\_key -iv $encrypted\_2805aa35fedb\_iv -in .travis/id\_rsa.enc -out ~/.ssh/id\_rsa -d  
> bad decrypt

***4.编写配置***

.travis.yml 增加如下配置，详细[见源码](https://github.com/actors315/actors315.github.io/blob/master/.travis.yml)

```
# 配置环境
before_install:
  # 替换为刚才生成的解密信息
  - openssl aes-256-cbc -K $encrypted_79258127fb87_key -iv $encrypted_79258127fb87_iv -in .travis/id_rsa.enc -out ~/.ssh/id_rsa -d
  # 改变文件权限
  - chmod 600 ~/.ssh/id_rsa
  # 配置 ssh
  - eval "$(ssh-agent -s)"
  - ssh-add ~/.ssh/id_rsa
  - cp .travis/ssh_config ~/.ssh/config
  # 配置 git 替换为自己的信息
  - git config --global user.name 'actors315'
  - git config --global user.email actors315@gmail.com
  # 用 ssh 方法提交
  - git remote set-url origin git@github.com:actors315/actors315.github.io.git
  # 切换到提交的目的分支
  - git checkout master

install:
  - composer install --prefer-dist --optimize-autoloader --quiet

script:
  - php -f bin/console.php

after_success:
  - git add README.md
  - git add ./markdown/*
  - git add ./files/*
  # 这里很重要，commit message 一定要填加 [skip travis] , 不然可能会进入死循环，一直在提交一直在自动构建
  - git commit -m "[skip travis] auto build by travis-ci" 
  - git push origin master
```

**第四步：填加 Cron jobs**

最后一步，配置完后就可以自动跑了。有三个时间周期可供选择，每月、每周、每天，按需选择就好了

![clipboard.png](/blog/files/images/f974e16864421adc198e0553d1321d59.png "clipboard.png")

这样就大功告成了，每天自动跑一次

![clipboard.png](/blog/files/images/93a2024c1a86bf167139fe497bcf2ce1.png "clipboard.png")

**其他方法**

1.[使用 Personal access tokens 实现](https://www.cnblogs.com/morang/p/7228488.html)

这也是 git 命令行自动提交，使用 Personal access tokens 也很方便，也是推荐。

2.使用 Travis CI 官方推荐的 deploy 流程

如果你需要自动提交的分支和自动构建不是同一个分支，这就非常方便了，你只需要生成一个 Personal access tokens ，在.travis.yml 文件中简单增加一点配置就好了。

```
deploy:
  provider: pages
  skip-cleanup: true
  github-token: $GITHUB_ACCESS_TOKEN
  target-branch: gh-pages
  keep-history: true
  on:
    branch: master
```

这个方式还没有研究出来是否可能指定 commit message, 如果同分支又会进入到死循环，同分支部署慎用

详见[官方文档](https://docs.travis-ci.com/user/deployment/pages/)
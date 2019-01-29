上一篇[【Composer】简单介绍](https://segmentfault.com/a/1190000013984336)

现在来说一说如何创建并发布一个自己的包，以这个简单的服务化类库 [twinkle/twinkle-api](https://github.com/TwinklePHP/Twinkle.git) 为例，也作个简单说明。

**有哪些步骤**

1. 编写代码
2. 编写composer.json文件,选择合适的包加载方式
3. github拖管代码
4. 提交包到[packagist](https://packagist.org)
5. 配置github hook自动更新

***编写类库代码***

作为一个使用composer的现代php程序员，建议使用 [psr4标准](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) 来组织代码（这不是必须，但是建议），代码参见github

***编写composer.json文件***

composer 一个重要功能就是自动加载，所以我们需要作相应的配置来注册一个 psr4 autoloader 到我们自己的命名空间。上面说了，我们使用 psr4 标准，这样配置

```
{
  "name": "twinkle/twinkle-api",
  "description": "Just for fun",
  "require": {
  },
  "autoload": {
    "psr-4": {
      "twinkle\\client\\": "client/",  // 就是这两行了
      "twinkle\\service\\": "service/"
    }
  }
}
```

***github拖管代码***

在github上创建一个项目，提交代码。同时建议发布一个稳定的tag。  
这不是必须，可以选择其他代码仓库，git或者svn都可以，但是建议放在这。

 ***提交包到 packagist***

packagist 是 Composer 的主要资源库，原生支持。任何支持 Composer 的开源项目应该发布自己的包在 packagist 上。当然这也不是必须，但是建议，毕竟 packagist 使用的人最多，资源最丰富。  
登录[packagist.org 官方站点](https://packagist.org/packages/submit)，如果没有账号，直接选择 github 账号登录就好了

![clipboard.png](/blog/files/images/5a095f667c97d89bcba9a69d62a8b345.jpg "clipboard.png")

输入项目的 github 地址，点击check，判断项目代码中包含 composer.json 文件，包名不重复，就可以直接  
 submit 了。

***配置github hook自动更新***

配置自动更新的好处是，如果提交了代码，或者发布了新的版本，packagist 会自动拉取最新的代码供他人使用。

> To do so you can:1.Go to your GitHub repository   
> 2.Click the "Settings" button   
> 3.Click "Integrations & services"   
> 4.Add a "Packagist" service, and configure it with your API token, plus your Packagist username   
> 5.Check the "Active" box and submit the form

这里有[详细说明](https://packagist.org/about#how-to-update-packages)

![clipboard.png](/blog/files/images/8753ae0df259d6af6b5d42c9903f19e6.jpg "clipboard.png")

token 在 packagist 个人中心点 “Show API Token”按钮可以查看到。

这样我们就创建并发布好了一个自己的包，试试在项目中使用它吧。

```
composer require twinkle/twinkle-api
```
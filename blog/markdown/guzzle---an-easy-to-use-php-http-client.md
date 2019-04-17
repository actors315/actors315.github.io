---  
layout: post  
type: blog  
title: '【项目推荐】Guzzle - 简单易用的 PHP HTTP 客户端'  
date: 2019-04-16T14:21:04+08:00  
excerpt: 'Guzzle 介绍
Guzzle 是一款简单、易用的 PHP HTTP 客户端。
它可以快速的集成到 WEB 项目中，帮助我们非常方便的发送 HTTP 请求。
Guzzle 特点
接口简单
支持使用 '  
key: fdc6dab5df78973bbe4525b045f4c133  
---  

**Guzzle 介绍**

Guzzle 是一款简单、易用的 PHP HTTP 客户端。

它可以快速的集成到 WEB 项目中，帮助我们非常方便的发送 HTTP 请求。

**Guzzle 特点**

接口简单

支持使用 curl,PHP streams,sockets等各种方式。

支持同步和异步请求

遵循 PSR7 规范，可以集成其他的符合 psr7 规范的类库，自定义处理逻辑

**安装**

使用 composer 安装，非常方便

```
composer require --prefer-dist guzzlehttp/guzzle
```

**快速入门**

1.初始化客户端

```
use GuzzleHttp\Client;

options = [
    'base_uri' => 'http://guzzle.testhttp.com',
    'connect_timeout' => 1,
    'timeout' => 3,
];

$client = new Client($options);
```

2.发送body请求

```
$client->request('POST', '/post', ['body' => 'this is post body']);
```

3.发送表单请求

```
$client->request('POST', '/post', [
    'form_params' => [
        'user_id' => 1,
        'user_name' => 'hello world!'
    ]
]);
```

4.json 请求

```
$client->request('POST', '/post', ['json' => ['data' => 'hello world!']]);
```

5.使用cookie

```
$params = ['json' => ['data' => 'hello world!']];
$cookieJar = CookieJar::fromArray(['cookieName' => 'testCookie'], 'guzzle.testhttp.com');
$param['cookies'] = $cookieJar;
$client->request('POST', '/post', $params);
```

6.multipart

```
$client->request('POST', '/post', [
    'multipart' => [
        [
            'name'     => 'baz',
            'contents' => fopen('/path/to/file', 'r')
        ],
        [
            'name'     => 'qux',
            'contents' => fopen('/path/to/file', 'r'),
            'filename' => 'custom_filename.txt'
        ],
    ]
]);
```

7.异步请求

```
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
$promise = $client->requestAsync('POST', '/post', ['json' => ['data' => 'hello world!']]);
$promise->then(
    function (ResponseInterface $res) {
        echo $res->getStatusCode() . "\n";
    },
    function (RequestException $e) {
        echo $e->getMessage() . "\n";
        echo $e->getRequest()->getMethod();
    }
);
```

8.并发请求

```
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$client = new Client(['base_uri' => 'http://guzzle.testhttp.com/']);

// Initiate each request but do not block
$promises = [
    'a' => $client->requestAsync('POST', '/post', ['json' => ['data' => 'hello test1!']]),
    'b'   => $client->requestAsync('POST', '/post', ['json' => ['data' => 'hello test2!']]),
    'b'  => $client->requestAsync('POST', '/post', ['json' => ['data' => 'hello test3!']]),
];

// Wait on all of the requests to complete.
$results = Promise\unwrap($promises);

// You can access each result using the key provided to the unwrap
// function.
echo $results['a']->getBody()->getContents(); // body 也有实现 __toString()调用getContents() 
echo $results['b']->getHeader('Content-Length');
```

**附录**

1. [项目地址](https://github.com/guzzle/guzzle)
2. [官方文档](http://docs.guzzlephp.org/en/stable/)
3. [中文文档](https://guzzle-cn.readthedocs.io/zh_CN/latest/index.html)
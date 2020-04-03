---  
layout: post  
type: blog  
title: '【Postman】pm.response.headers 对象探究'  
date: 2020-04-03T16:34:09+08:00  
excerpt: '是什么
顾名思义，这个对象包含响应头信息。
是数组吗
网上很多介绍都说他是“以数组的形式返回当前请求成功后的response的headers”，用console.log() 输出，发现它也像是数组。
'  
key: e133edca251a04314750f6cc6872c53a  
---  

**是什么**

顾名思义，这个对象包含响应头信息。

**是数组吗**

网上很多介绍都说他是“以数组的形式返回当前请求成功后的response的headers”，用console.log() 输出，发现它也像是数组。  
![image.png](/blog/files/images/5e751ba6fd92c5457bbaea0828ceeae8.png "image.png")

可如果你真把它当成数组去操作的时候，你可能会怀疑人生了，undefined 是个什么鬼。

**究竟是什么**

```
console.log(typeof(headers))
```

哦，原来是 object

```
console.log(pm.response.headers instanceof Object)

// true
```

那为什么 console.log() 直接输出数组了呢？

我们来打印一下

```
headers = pm.response.headers;
for(var i in headers) {
    console.log(i + ' : ' + headers[i])
}
```

![image.png](/blog/files/images/a14dcc11a57f50c58e6d2443564682c6.png "image.png")

它作为一个对象，拥有众多属性和方法。长的像数组的有 “members” 属性，但是查阅资料，并未找到 console.log() 有对 Object 特殊处理的方法，留待之后学习解迷了。
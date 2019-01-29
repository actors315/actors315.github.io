---  
layout: post  
title: '【nginx】记录nginx+php-fpm实现大文件下载排坑的过程'  
date: 2018-08-01T09:26:53+08:00  
excerpt: '先上一段代码，支持大文件下载和断点续传,代码来源互联网。
set_time_limit(0);

// 省略取文件路径的过程，这里直接是文件完整路径
$filePath = get_save_path'  
---  

先上一段代码，支持大文件下载和断点续传,代码来源互联网。

```
set_time_limit(0);

// 省略取文件路径的过程，这里直接是文件完整路径
$filePath = get_save_path() . $File['save_name'];
$filePath = realpath($filePath);

$outFileExtension = strtolower(substr(strrchr($filePath, "."), 1)); //获取文件扩展名
//根据扩展名 指出输出浏览器格式
switch ($outFileExtension) {
    case "exe" :
        $ctype = "application/octet-stream";
        break;
    case "zip" :
        $ctype = "application/zip";
        break;
    case "mp3" :
        $ctype = "audio/mpeg";
        break;
    case "mpg" :
        $ctype = "video/mpeg";
        break;
    case "avi" :
        $ctype = "video/x-msvideo";
        break;
    default :
        $ctype = "application/force-download";
}

header("Cache-Control:");
header("Cache-Control: public");

//设置输出浏览器格式
header("Content-Type: $ctype");
header("Content-Disposition: attachment; filename=" . basename($filePath));
header("Accept-Ranges: bytes");
$size = filesize($filePath);

//如果有$_SERVER['HTTP_RANGE']参数
if (isset ($_SERVER['HTTP_RANGE'])) {
    /*Range头域 　　Range头域可以请求实体的一个或者多个子范围。
    例如，
    表示头500个字节：bytes=0-499
    表示第二个500字节：bytes=500-999
    表示最后500个字节：bytes=-500
    表示500字节以后的范围：bytes=500- 　　
    第一个和最后一个字节：bytes=0-0,-1 　　
    同时指定几个范围：bytes=500-600,601-999 　　
    但是服务器可以忽略此请求头，如果无条件GET包含Range请求头，响应会以状态码206（PartialContent）返回而不是以200 （OK）。
    */
    // 断点后再次连接 $_SERVER['HTTP_RANGE'] 的值 bytes=4390912-
    list ($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
    //if yes, download missing part
    str_replace($range, "-", $range); //这句干什么的呢。。。。
    $size2 = $size - 1; //文件总字节数
    $new_length = $size2 - $range; //获取下次下载的长度
    header("HTTP/1.1 206 Partial Content");
    header("Content-Length: $new_length"); //输入总长
    header("Content-Range: bytes $range$size2/$size"); //Content-Range: bytes 4908618-4988927/4988928   95%的时候
} else {
    //第一次连接
    $size2 = $size - 1;
    header("Content-Range: bytes 0-$size2/$size"); //Content-Range: bytes 0-4988927/4988928
    header("Content-Length: " . $size); //输出总长
}
//打开文件
$fp = fopen("$filePath", "rb");
//设置指针位置
if (!empty($range)) {
    fseek($fp, $range);
}

//虚幻输出
while (!feof($fp)) {
    print (fread($fp, 1024 * 8)); //输出文件
    flush(); //输出缓冲
    ob_flush();
}
fclose($fp);
exit ();
```

代码有详细的解释，也很清楚，但是在实际使用时还是小文件可以下载，大文件只能下载前半部分或者出现文件已损坏的情况。查看nginx日志发现如下报错

```
2018/08/01 07:43:20 [crit] 13906#0: *1479 open() "/usr/local/nginx/fastcgi_temp/0/02/0000000020" failed (13: Permission denied) while reading upstream,
```

原来在下载大文件时，文件大小超过配置的proxy\_temp\_file\_write\_size值时，nginx会将文件写入到临时目录下，如果该目录没有权限，就写不了，那下载只能下载缓冲区的内容了。  
核实/usr/local/nginx/fastcgi\_temp/目录的权限分组，并不在nginx运行账号组下，即然知道了问题原因，那就好办了。给予写权限，或者将目录改为nginx运行账号组下就OK了

```
cd /usr/local/nginx/ 
chmod -R 766 fastcgi_temp/
或者
chown -R nginx:nginx fastcgi_temp/ #nginx根据各自情况可能是不能的账户
```

> [解决PHP超大文件下载,断点续传下载的方法详解](https://blog.csdn.net/weixin_38893715/article/details/72674399?locationNum=8&fps=1)
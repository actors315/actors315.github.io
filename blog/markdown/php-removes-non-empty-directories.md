---  
layout: post  
type: blog  
title: 'PHP 删除非空目录'  
date: 2020-04-01T11:44:40+08:00  
excerpt: '背景
使用 composer 安装私有 gitlab 仓库类库，会自动生成 .git 目录，这会导致该类库不能直接直接到 git 仓库。每次要手动删除该目录，于是就想使用 composer 的 scr'  
key: f5c0b81225daa48f91c3fa53ff433d9f  
---  

**背景**

使用 composer 安装私有 gitlab 仓库类库，会自动生成 .git 目录，这会导致该类库不能直接直接到 git 仓库。每次要手动删除该目录，于是就想使用 composer 的 scripts 功能自动删除。

**涉汲函数**

scandir 列出指定路径中的文件和目录,要求 PHP 版本大于 5.0  
unlink 删除文件  
rmdir 删除目录,要求目录必须为空

**实现思路**

遍历 vendor 目录下所有类库，筛出名为 .git 的目录  
删除 .git 目录下的所有文件，递归删除所有子目录  
删除 .git 目录

注：scandir 有两个特殊的目录 . 和 .. 分别表示当前目录和上级目录，需要特别处理，否则递归就死循环了。

**示例代码**

```
namespace Clothing\Tools;

class ClearFile
{
    public function scanDir($rootPath, $scan)
    {
        $dirList = [];
        foreach (scandir($rootPath) as $dir) {
            if ($dir == '.' || $dir == '..') {
                continue;
            }
            $path = realpath($rootPath . DIRECTORY_SEPARATOR . $dir);
            if ($dir === $scan || $scan === false) {
                $dirList[] = $path;
            } elseif (is_dir($path)) {
                $dirList = array_merge($dirList, $this->scanDir($path . DIRECTORY_SEPARATOR, $scan));
            }
        }

        return $dirList;
    }

    public function clearDir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $list = $this->scanDir($dir, false);
        foreach ($list as $file) {
            if (is_file($file)) {
                unlink($file);
            } elseif (is_dir($file)) {
                $this->clearDir($file);
            }
        }

        return rmdir($dir);
    }
}
```

bin/clear-git

```
$clear = new \Clothing\Tools\ClearFile();
$dirList = $clear->scanDir($rootPath, '.git');
```

composer.json

```
"scripts": {
    "post-autoload-dump": [
        "./vendor/bin/clear-git"
    ]
}
```
---  
layout: post  
type: blog  
title: '【PHP8体验】pdo_mysql 事务提交失败'  
date: 2020-12-06T00:44:05+08:00  
excerpt: '发生了什么周末无事，想装上 PHP8 体验一把新版本的特性，找了一个 Yii2 写的老项目，结果运行 migration 初始化环境就遇到了问题，建表脚本直接报错。Exception: There i'  
key: 091e8c9f157f7e52b07302645544ba79  
---  

**发生了什么**  
周末无事，想装上 PHP8 体验一把新版本的特性，找了一个 Yii2 写的老项目，结果运行 migration 初始化环境就遇到了问题，建表脚本直接报错。

> Exception: There is no active transaction

而同样的脚本在 7.4 版本也完全正常。

**测试脚本**

简化的脚本差不多是这样

```
$conn = new PDO("mysql:host=127.0.0.1;dbname=test", 'root', '123456');
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 $conn->beginTransaction();
 try {
 $sql = "CREATE TABLE IF NOT EXISTS test (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, `text` varchar(32) NOT NULL DEFAULT '', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
 $conn->exec($sql);
 
 $sql = "INSERT INTO test values(1,'test1')";
 $conn->exec($sql);
 
 $conn->commit();
 } catch (Exception $e) {
 echo $e->getMessage();
 $conn->rollBack();
 }

```

**问题原因**

MySQL DDL 语句会触发隐式提交，如果事务里执行的是其他 DML/DQL 语句，就完全没问题。官方文档里也有提到这个问题。

> Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition language (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit COMMIT will prevent you from rolling back any other changes within the transaction boundary.

但是在 PHP 8.0 以前的版本，带来的影响只是事务被提前提交，不能回滚而已，不知道为啥 8.0 要改成一个异常，并且 ChangeLog 又并未提到该变更。
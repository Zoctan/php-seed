# PHP SEED

极简 Model + Controller 架构的 API 接口

# 部署

- PHP >= 7.4.3
- MySql >= 8
- Redis >= 3
- Apache >= 2.4

安装依赖：composer install

## Mysql

创建数据库：phpseed，把 sql 文件夹下的 sql 全部导入。

## Apache

把整个文件夹放到 WWW 目录下

# 文件说明

```text
controller：控制器
core：核心
model：模型
rsa：rsa公私钥
sql：sql文件
upload：上传文件夹
util：工具函数
vendor：库依赖
web_config：IIS的配置

config.php：项目配置
functions.php：全局工具函数
index.php：项目入口
routes.php：路由表
web.config：IIS的配置
```

# 其他

composer usage:
	// 更新目录结构
	composer dump-auto

单一入口路由分配，需要IIS/Apache/Nginx重写路由。IIS/Apache已完成，Nginx未完成。

## 库依赖

- 数据库 Medoo(https://github.com/catfan/Medoo)：https://medoo.in/doc
- Redis 客户端 Predis(https://github.com/predis/predis)：https://github.com/predis/predis#connecting-to-redis
- JWT(https://github.com/lcobucci/jwt)：https://lcobucci-jwt.readthedocs.io/en/latest/
- 数据伪造 Faker(https://github.com/fzaninotto/Faker)：https://github.com/fzaninotto/Faker#basic-usage
- HTTP 客户端 Guzzle(https://github.com/guzzle/guzzle)：https://docs.guzzlephp.org/en/stable/quickstart.html

- 图片处理 Image(https://github.com/Intervention/image)：https://image.intervention.io/v2/usage/overview
- 图片处理依赖 php_imagick：http://pecl.php.net/package/imagick
- 图片处理依赖 ImageMagick(ImageMagick-xxx-vc15-x64.zip)：https://windows.php.net/downloads/pecl/deps/
- Windows 如何安装：https://laksmisetiawati.github.io/articles/php-windows-imagick.html
- http://www.884358.com/php-imagick-ghostscript-pdf-img/
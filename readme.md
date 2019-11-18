
## Parttimes 活动记录
<p align="center"><a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

#### Laravel 学后练习

Parttimes是用Laravel5.8框架开发的活动记录与分享平台，数据返回全部为Json接口

### 快速部署

1. 下载项目，准备数据库
2. 到项目根目录处，执行 composer install
3. 配置.env文件
4. 到项目根目录处，执行 php artisan migrate
4. 要求不打印错误输出时，将 .env文件 的 APP_DEBUG 改为 FALSE

###基于HTTP状态码的数据返回

- 200 OK 一般返回，可能会返回额外的信息
- 400 Authentication Failed 认证错误，未携带令牌操作数据时会返回400
- 500 Internal Error 服务器出错，一般为传入的参数错误，或者抛出了异常


### 异常抛出方式
##### 当 config(app.debug) 为 false 时，作为线上环境会直接抛出一个 500，否则显示错误轨迹(traces)

### 未来修复
* 限制提交的密码Hash长度 @ParttimeUserController::login()
* 消息标记为已读，这样可能要重新设计ParttimeMessage表 @ParttimeMessageProcess::tagRead()
* 删除模拟手机发送的验证码功能，这个路由是/api/code/allocate，参数phone是手机号，返回一个验证码

### 用到的其他库

- intervention/image 图形处理，负责将上传的图片转为jpg格式

### 许可证

该项目用的许可证是 [MIT license](https://opensource.org/licenses/MIT).


# PHP SEED Template

Model + Controller 极简架构的多用户-多角色 API 接口。

![stars](https://img.shields.io/github/stars/Zoctan/php-seed-template.svg?style=flat-square&label=Stars)
![license](https://img.shields.io/github/license/Zoctan/php-seed-template.svg?style=flat-square)

简体中文 | [English](./README.md)

配合前端项目 [admin-vue3-template](https://github.com/Zoctan/admin-vue3-template) 的后端项目。

包括以下功能：
- JWT 接口签名认证
- Redis 缓存中间件
- Upload 上传接口

欢迎小伙伴 star 和 issues ~ 谢谢 :）

# 生命周期

![Lifecycle](https://github.com/Zoctan/php-seed-template/blob/main/README/Lifecycle.jpg)

# 数据库设计

数据库有[9张表](https://github.com/Zoctan/php-seed-template/tree/main/sql)，对角色权限控制最重要的是其中5张表，分别是用户表 member、角色表 role、用户角色表 member_role、权限表 rule、角色权限表 role_rule。

数据库关系模型如下：
![ERD](https://github.com/Zoctan/php-seed-template/blob/main/README/ERD.jpg)

注：表之间的外键仅仅是显示在上面的关系模型，在实际数据库中并没有进行定义，而是在应用层实现，好处是降低维护表的工作量，降低性能的损耗。

member 表：用户信息。
![member](https://github.com/Zoctan/php-seed-template/blob/main/README/member.jpg)

role 表：角色信息。
![role](https://github.com/Zoctan/php-seed-template/blob/main/README/role.jpg)

member_role 表：用户对应的角色，一对多。
![member_role](https://github.com/Zoctan/php-seed-template/blob/main/README/member_role.jpg)

rule 表：权限能操作的资源以及操作方式。
![rule](https://github.com/Zoctan/php-seed-template/blob/main/README/rule.jpg)

role_rule 表：角色所对应的权限，一对多。
![role_rule](https://github.com/Zoctan/php-seed-template/blob/main/README/role_rule.jpg)

# 角色权限控制

用户登录 -> 服务端生成 accessToken -> 客户端保存 accessToken，之后的每次请求都携带该 accessToken，服务端鉴权。

```php
// controller/MemberController.php
public function login() {
    ...
    // 生成 accessToken 和 refreshToken
    // accessToken 解析出来只有 memberId，不放其他的用户信息，避免 token 过长
    $jwt->sign($memberId)
}
```

服务端 Router + AuthenticationFilter + Json Web Token 鉴权：

```php
// router.php
$router->addRoute(
    'POST',
    '/isMemberExist',
    'MemberController@isMemberExist',
    ['auth' => true, 'permission' => ['joint': 'and', 'member:list', 'member:detail']]
)

// core/filter/AuthenticationFilter.php
$needPermissionList = $router->getRoute($uri)->permission;
$authMember = $jwt->getAuthMember($accessToken);
if (!$authMember->checkPermission($needPermissionList)) { ... }
```

# 部署

## 依赖版本

- PHP >= 7.4.3
- MySql >= 8
- Redis >= 3
- Apache >= 2.4

## 快速开始

```
# 克隆项目
git clone https://github.com/Zoctan/php-seed-template.git

# 安装依赖
composer install

# mysql
创建数据库：phpseed，把 sql 文件夹下的文件全部导入数据库

# apache
把整个文件夹放到 WWW 目录下
```

# 文件说明

```text
controller：控制器
core：核心
- exception：异常类
- filter：过滤器
- http：请求响应
- result：响应结果
- router：路由
model：模型
rsa：非对称加密公私钥
sql：数据库文件
upload：上传文件夹
util：工具函数
vendor：库依赖
web_config：IIS的配置

config-development.php：项目开发配置
config-production.php：项目生产配置
config.php：项目配置
functions.php：全局工具函数
index.php：项目入口
router.php：路由表
routes-development.cache：路由表缓存-开发环境
web.config：IIS的配置
```

# Token 刷新策略

SEED 项目选择在后端签发：accessToken、refreshToken，在前端请求拦截响应后刷新 accessToken 的策略。

## 分类讨论

```
1.后端签发：accessToken（可选：expired）；
前端在 accessToken 过期前，申请刷新；过期后重新登录。

2.后端签发：accessToken、refreshToken（可选：expired）；
前端在 accessToken 过期后，可以采取多种策略使用 refreshToken 不断刷新 accessToken，保证用户一直无感在线。
```

```
什么时候需要 expired？
前后端商量好过期时间就不用，只是后期可能增加商量的时间和修改前端的成本，expired 字段可以节省成本。
```

```
为什么需要 refreshToken？
accessToken 存在网络传输泄露风险，因为 accessToken 需要在请求中频繁用到，所以不能用作刷新。
refreshToken 本身没多少次传输，只有登录时，刷新时，本身过期需要刷新时才会在网络中出现，其他时间都在本地储存，泄露风险相对较低。
```

## 单纯前端实现

### accessToken 刷新策略有三种

```
1.全局维护刷新倒计时器：【后端：accessToken、refreshToken（可选：expired）；前端：保存 token 的时间 saveTokenDate】
    登录或注册后开启倒计时【分钟级】（App.vue也要加入倒计时，防止页面刷新，倒计时被销毁），倒计时到达刷新时间，使用 refreshToken 请求续期 accessToken。
    倒计时要结合 saveTokenDate，不然用户退出网页一段时间再回来会导致倒计时错误。
    优点：用户无感，即使无请求动作，只要开着网页即可续期。
    缺点：本地时间在开启网页后中途乱改，导致倒计时出错（可能性低）。

2.在请求拦截中刷新【请求前或响应后】:
    请求前：【后端：accessToken、refreshToken（可选：expired）；前端：保存 token 的时间 saveTokenDate】
        和全局维护倒计时器差不多，每次请求前检查是否到达刷新时间。
        优点：无需维护计时器。
        缺点：有本地时间乱改的风险；
              需要用户有请求动作；
              需要前端有实时保存动作，避免长时间没有请求动作时表单之类的数据丢失，影响用户体验。

    响应后：【后端：accessToken、refreshToken】
        响应提示 accessToken 过期就使用 refreshToken 请求续期。
        优点：无需判断时间；
              无需维护计时器。
        缺点：需要用户有请求动作；
              需要前端有实时保存动作。
```

以上策略在 refreshToken 也过期时都需要跳转至登录页。

### refreshToken 刷新策略有两种

```
1.采用全局维护刷新倒计时器：和上面的策略一致。

2.每次请求都刷新 refreshToken，或者达到什么请求条件（比如超过50次请求，具体的几个时间段仍然在线）就请求刷新：
    优点：无需额外字段 expired；
          无需开倒计时器，维护简单。
```

```
什么时候刷新 refreshToken？
看项目需要，如果 refreshToken 有效时间长达半个月、一个月都无所谓，那不刷新也没事。
refreshToken 本身泄露风险不高，除非 accessToken 时间极短，需要频繁刷新。
```

## 后端 + 前端

```
只签发 accessToken：
    每次拦截请求，成功响应后，都检查 accessToken，如果即将过期，在响应字段中签发新的 accessToken，前端拦截每次响应并检查 accessToken 是否有更新。
    优点：无需额外字段 refreshToken、expired；
    缺点：需要用户有请求动作；
          需要 accessToken 的有效时间长；
          需要前端有实时保存动作。
```

# 接口

响应统一以 JSON 格式返回：

| 参数 | 类型 | 描述 |
| --- | --- | --- |
| errno | int | 错误码 |
| msg | string | 消息 |
| data | int | 数据 |

```json
成功：
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}

失败：
{
    "errno": 1,
    "msg": "Failed"
}
```

错误码定义在：`core/result/ResultCode.php`

需要鉴权的接口，请求需要把登录后获得的 `accessToken` 值放到 `Authorization`（请求头 Header 或者 POST、GET 的表单中）

## 用户 Member

### 用户是否存在

* 接口：`/member/isMemberExist`
* 请求：`POST`
* 认证：`否`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| username | string | 是 |   | 用户名 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 用户注册

* 接口：`/member/register`
* 请求：`POST`
* 认证：`否`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| username | string | 是 |   | 用户名 |
| password | string | 是 |   | 密码 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": {
        "accessToken": "...",
        "refreshToken": "..."
    }
}
```

### 用户登录

* 接口：`/member/login`
* 请求：`POST/GET`
* 认证：`否`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| username | string | 是 |   | 用户名 |
| password | string | 是 |   | 密码 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": {
        "accessToken": "...",
        "refreshToken": "..."
    }
}
```

### 用户登出

* 接口：`/member/logout`
* 请求：`DELETE`
* 认证：`否`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| username | string | 是 |   | 用户名 |
| password | string | 是 |   | 密码 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": {
        "accessToken": "...",
        "refreshToken": "..."
    }
}
```

### 用户信息

* 接口：`/member/detail`
* 请求：`POST/GET`
* 认证：`是`
* 权限：`member:detail`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| id | int | 是 |   | 用户id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": {
        "member": ["..."],
        "memberData": ["..."],
        "roleList": ["..."]
    }
}
```

### 已登录用户信息

* 接口：`/member/profile`
* 请求：`POST/GET`
* 认证：`是`

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": {
        "member": ["..."],
        "memberData": ["..."],
        "roleList": ["..."]
    }
}
```

### 用户列表

* 接口：`/member/list`
* 请求：`POST`
* 认证：`是`
* 权限：`member:list`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| currentPage | int | 否 | 0 | 当前页码 |
| pageSize | int | 否 | 20 | 页大小 |
| member | object | 否 |   | 查询用户属性 |
| memberData | object | 否 |   | 查询用户数据属性 |
| role | object | 否 |   | 查询角色属性 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": {
        "list": [],
        "total": 0,
        "currentPage": 1,
        "pageSize": 10,
        "totalPage": 1
    }
}
```

### 验证用户旧密码

* 接口：`/member/validateOldPassword`
* 请求：`POST`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| oldPassword | string | 是 |   | 旧密码 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 验证用户 AccessToken

* 接口：`/member/validateAccessToken`
* 请求：`POST/GET`
* 认证：`否`

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 刷新用户 AccessToken

* 接口：`/member/refreshAccessToken`
* 请求：`PUT`
* 认证：`否`

不需要认证，但是需要把旧 `accessToken` 放到请求中，因为需要验证旧 `accessToken` 是否有除了过期以外的其他问题。

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| refreshToken | string | 是 |   | 用于刷新的凭证 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
		"data": "..."
}
```

### 更新用户密码

* 接口：`/member/updatePassword`
* 请求：`PUT`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| password | string | 是 |   | 新密码 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 更新已登录用户信息

* 接口：`/member/updateProfile`
* 请求：`PUT`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| member | object | 是 |   | 用户 |
| memberData | object | 是 |   | 用户数据 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 更新用户信息

* 接口：`/member/updateDetail`
* 请求：`PUT`
* 认证：`是`
* 权限：`member:update`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| member | object | 是 |   | 用户 |
| memberData | object | 是 |   | 用户数据 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 添加用户

* 接口：`/member/add`
* 请求：`POST`
* 认证：`是`
* 权限：`member:add`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| member | object | 是 |   | 用户 |
| memberData | object | 否 |   | 用户数据 |
| role | object | 否 |   | 角色 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### 删除用户

* 接口：`/member/remove`
* 请求：`DELETE`
* 认证：`是`
* 权限：`member:remove`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| id | int | 是 |   | 用户id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## 角色 Role

### 角色列表

* 接口：`/role/list`
* 请求：`POST`
* 认证：`是`
* 权限：`role:list`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| currentPage | int | 否 | 0 | 当前页码 |
| pageSize | int | 否 | 0 | 页大小 |
| parentIdList | object | 否 |   | 当前用户的角色id列表 |
| role | object | 否 |   | 查询角色属性 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": {
        "list": [],
        "total": 0,
        "currentPage": 1,
        "pageSize": 10,
        "totalPage": 1
    }
}
```

### 角色的父亲列表

* 接口：`/role/listParent`
* 请求：`POST`
* 认证：`是`
* 权限：`role:list`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| parentId | int | 是 |   | 角色父亲id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### 角色信息

* 接口：`/role/detail`
* 请求：`POST/GET`
* 认证：`是`
* 权限：`role:detail`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| id | int | 是 |   | 角色id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": {
			"role": "...",
			"ruleList": ["..."]
		}
}
```

### 添加角色

* 接口：`/role/add`
* 请求：`POST`
* 认证：`是`
* 权限：`role:add`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| role | object | 是 |   | 角色 |
| rule | object | 否 |   | 规则列表 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### 更新角色

* 接口：`/role/update`
* 请求：`PUT`
* 认证：`是`
* 权限：`role:update`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| role | object | 是 |   | 角色 |
| rule | object | 否 |   | 规则列表 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 删除角色

* 接口：`/role/remove`
* 请求：`DELETE`
* 认证：`是`
* 权限：`role:remove`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| id | int | 是 |   | 角色id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 给用户添加角色

* 接口：`/role/addMemberRole`
* 请求：`POST`
* 认证：`是`
* 权限：`member:upload``role:upload`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| memberId | int | 是 |   | 用户id |
| roleId | int | 是 |   | 角色id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### 删除用户角色

* 接口：`/role/removeMemberRole`
* 请求：`DELETE`
* 认证：`是`
* 权限：`member:upload``role:upload`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| memberId | int | 是 |   | 用户id |
| roleId | int | 是 |   | 角色id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## 角色规则 Rule

### 角色规则列表

* 接口：`/rule/list`
* 请求：`POST`
* 认证：`是`

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### 添加角色规则

* 接口：`/rule/add`
* 请求：`POST`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| parent_id | int | 否 |   | 父亲规则id |
| description | string | 是 |   | 描述 |
| permission | string | 是 |   | 资源/权限 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### 更新角色规则

* 接口：`/rule/update`
* 请求：`PUT`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| id | int | 是 |   | 规则id |
| description | string | 否 |   | 描述 |
| permission | string | 否 |   | 资源/权限 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 删除角色规则列表

* 接口：`/rule/removeList`
* 请求：`DELETE`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| ruleIdList | object | 是 |   | 规则id列表 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 删除角色规则

* 接口：`/rule/remove`
* 请求：`DELETE`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| id | int | 是 |   | 规则id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## 上传 Upload

### 文件下载

* 接口：`/upload/`
* 请求：`GET`
* 认证：`否`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| filename | string | 是 |   | 文件名 |
| type | string | 是 |   | 文件类型 |

### 文件上传

* 接口：`/upload/add`
* 请求：`POST`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| type | string | 否 | image | 文件类型 |
| targetDir | string | 否 |   | 目标文件夹 |
| useTimeDir | string | 否 | false | 使用时间文件夹 |
| useRandomName | string | 否 | false | 随机文件名 |
| overwrite | string | 否 | false | 覆盖已有文件 |
| reizeConfig | object | 否 |   | 宽高配置 |
| compressConfig | object | 否 |   | 压缩配置 |
| watermarkConfig | object | 否 |   | 水印配置 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
		"data": ["..."]
}
```

### 文件删除

* 接口：`/upload/remove`
* 请求：`DELETE/POST/GET`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| filename | string | 是 |   | 文件名 |
| type | string | 是 |   | 文件类型 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## 键值对 Pair

### 键值对列表

* 接口：`/pair/list`
* 请求：`POST`
* 认证：`是`
* 权限：`pair:list`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| currentPage | int | 否 | 0 | 当前页码 |
| pageSize | int | 否 | 20 | 页大小 |
| pair | object | 否 |   | 查询键值对属性 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### 获取值

* 接口：`/pair/getValue`
* 请求：`POST`
* 认证：`是`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| key | string/object | 是 |   | 键 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### 添加键值对

* 接口：`/pair/add`
* 请求：`POST`
* 认证：`是`
* 权限：`pair:add`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| pair | object | 是 |   | 键值对 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### 更新键值对

* 接口：`/pair/update`
* 请求：`PUT`
* 认证：`是`
* 权限：`pair:update`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| pair | object | 是 |   | 键值对 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### 删除键值对

* 接口：`/pair/remove`
* 请求：`DELETE`
* 认证：`是`
* 权限：`pair:remove`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| id | int | 是 |   | 键值对id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## 日志 Log

### 日志列表

* 接口：`/log/list`
* 请求：`POST`
* 认证：`是`
* 权限：`log:list`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| currentPage | int | 否 | 0 | 当前页码 |
| pageSize | int | 否 | 20 | 页大小 |
| log | object | 否 |   | 查询日志属性 |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### 删除日志

* 接口：`/log/remove`
* 请求：`DELETE`
* 认证：`是`
* 权限：`log:remove`

#### 请求

| 参数 | 类型 | 必填 | 默认值 | 描述 |
| --- | --- | --- | --- | --- |
| id | int | 是 |   | 日志id |

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## 伪造数据 Fake

### 获取伪造名字

* 接口：`/fake/getName`
* 请求：`GET`
* 认证：`是`

#### 成功响应

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

# 库依赖

- [Medoo：数据库工具](https://medoo.in/doc)
- [Predis：Redis 客户端](https://github.com/predis/predis#connecting-to-redis)
- [JWT](https://lcobucci-jwt.readthedocs.io/en/latest/)
- [Faker：数据伪造](https://github.com/fzaninotto/Faker#basic-usage)
- [Intervention/image：图片处理](https://image.intervention.io/v2/usage/overview)
- Windows 如何安装图片处理依赖：https://laksmisetiawati.github.io/articles/php-windows-imagick.html
- 图片处理依赖 php_imagick 下载：http://pecl.php.net/package/imagick
- 图片处理依赖 ImageMagick 下载(ImageMagick-xxx-vc15-x64.zip)：https://windows.php.net/downloads/pecl/deps/

# PHP SEED

Model + Controller Minimalist multi-user and multi-role API interface

![stars](https://img.shields.io/github/stars/Zoctan/php-seed-template.svg?style=flat-square&label=Stars)
![license](https://img.shields.io/github/license/Zoctan/php-seed-template.svg?style=flat-square)

English | [简体中文](./README-zh.md)

Back-end project with front-end project [ADMIN-SEED-Template](https://github.com/Zoctan/admin-seed-template).

Includes the following features:
- JWT interface signature authentication
- Redis cache middleware
- Upload upload interface

Welcome friends to star and issues ~ thank you :)

# Deploy

## Dependency version

- PHP >= 7.4.3
- MySql >= 8
- Redis >= 3
- Apache >= 2.4

## Quick start

```
# clone project
git clone https://github.com/Zoctan/admin-seed-template.git

# install dependencies
composer install

# mysql
Create a database: phpseed, import all the files in the sql folder into the database.

# apache
Put the whole folder in the WWW directory
```

# File description

```text
controller: controller
core: core
- exception: exception
- filter: filter
- http: response & request
- result: response result
- router: router
model: model
rsa: Asymmetric encryption public and private keys
sql: database sql file
upload: upload
util: tool function
vendor: php dependencies
web_config: IIS configuration

config-development.php: development environment configuration
config-production.php: production environment configuration
config.php:  environment configuration
functions.php: global function
index.php: global entry
router.php: router routes
routes-development.cache: routes cache-development
web.config: IIS configuration
```

# Interface

The response is uniformly returned in JSON format:

| parameter | type | description |
| --- | --- | --- |
| errno | int | error code |
| msg | string | message |
| data | int | data |

```json
success:
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}

error:
{
    "errno": 1,
    "msg": "Failed"
}
```

Error codes are defined in: `core/result/ResultCode.php`

When interface requires authentication, the request needs to put the `accessToken` value obtained after login into `Authorization` (request header Header or POST, GET form)

## Member

### Does the member exist

* api: `/member/isMemberExist`
* method: `POST`
* requires authentication: `no`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| username | string | yes |   | username |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Member register

* api: `/member/register`
* method: `POST`
* requires authentication: `no`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| username | string | yes |   | username |
| password | string | yes |   | password |

#### Succcess response

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

### Member login

* api: `/member/login`
* method: `POST/GET`
* requires authentication: `no`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| username | string | yes |   | username |
| password | string | yes |   | password |

#### Succcess response

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

### Member logout

* api: `/member/logout`
* method: `DELETE`
* requires authentication: `no`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| username | string | yes |   | username |
| password | string | yes |   | password |

#### Succcess response

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

### Member detail

* api: `/member/detail`
* method: `POST/GET`
* requires authentication: `yes`
* requires permission: `member:detail`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| id | int | yes |   | member id |

#### Succcess response

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

### Logined memeber detail

* api: `/member/profile`
* method: `POST/GET`
* requires authentication: `yes`

#### Succcess response

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

### Member list

* api: `/member/list`
* method: `POST`
* requires authentication: `yes`
* requires permission: `member:list`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| currentPage | int | no | 0 | current page |
| pageSize | int | no | 20 | page size |
| member | object | no |   | member search attribution |
| memberData | object | no |   | member data search attribution |
| role | object | no |   | role search attribution |

#### Succcess response

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

### Validate member old password

* api: `/member/validateOldPassword`
* method: `POST`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| oldPassword | string | yes |   | old password |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Validate member AccessToken

* api: `/member/validateAccessToken`
* method: `POST/GET`
* requires authentication: `no`

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Refresh member AccessToken

* api: `/member/refreshAccessToken`
* method: `PUT`
* requires authentication: `no`

No requires authentication, but need to put the old `accessToken` in the Request, because the old `accessToken` needs to be verified in this api.

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| refreshToken | string | yes |   | credentials for refresh |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
		"data": "..."
}
```

### Update member password

* api: `/member/updatePassword`
* method: `PUT`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| password | string | yes |   | new password |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Update member profile

* api: `/member/updateProfile`
* method: `PUT`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| member | object | yes |   | member |
| memberData | object | yes |   | member data |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Update member detail

* api: `/member/updateDetail`
* method: `PUT`
* requires authentication: `yes`
* requires permission: `member:update`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| member | object | yes |   | member |
| memberData | object | yes |   | member data |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Add member

* api: `/member/add`
* method: `POST`
* requires authentication: `yes`
* requires permission: `member:add`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| member | object | yes |   | member |
| memberData | object | no |   | member data |
| role | object | no |   | role |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### Remove member

* api: `/member/remove`
* method: `DELETE`
* requires authentication: `yes`
* requires permission: `member:remove`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| id | int | yes |   | member id |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## Role

### Role list

* api: `/role/list`
* method: `POST`
* requires authentication: `yes`
* requires permission: `role:list`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| currentPage | int | no | 0 | current page |
| pageSize | int | no | 0 | page size |
| parentIdList | object | no |   | current member role parent id list |
| role | object | no |   | role search attribution |

#### Succcess response

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

### List role parent

* api: `/role/listParent`
* method: `POST`
* requires authentication: `yes`
* requires permission: `role:list`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| parentId | int | yes |   | role parent id |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### Role detail

* api: `/role/detail`
* method: `POST/GET`
* requires authentication: `yes`
* requires permission: `role:detail`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| id | int | yes |   | role id |

#### Succcess response

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

### Add role

* api: `/role/add`
* method: `POST`
* requires authentication: `yes`
* requires permission: `role:add`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| role | object | yes |   | role |
| ruleList | object | no |   | rule list |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### Update role

* api: `/role/update`
* method: `PUT`
* requires authentication: `yes`
* requires permission: `role:update`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| role | object | yes |   | role |
| ruleList | object | no |   | rule list |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Remove role

* api: `/role/remove`
* method: `DELETE`
* requires authentication: `yes`
* requires permission: `role:remove`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| id | int | yes |   | role id |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Add member a role

* api: `/role/addMemberRole`
* method: `POST`
* requires authentication: `yes`
* requires permission: `member:upload``role:upload`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| memberId | int | yes |   | member id |
| roleId | int | yes |   | role id |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### Remove member a role

* api: `/role/removeMemberRole`
* method: `DELETE`
* requires authentication: `yes`
* requires permission: `member:upload``role:upload`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| memberId | int | yes |   | member id |
| roleId | int | yes |   | role id |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## Rule

### Rule list

* api: `/rule/list`
* method: `POST`
* requires authentication: `yes`

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### Add rule

* api: `/rule/add`
* method: `POST`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| parent_id | int | no |   | rule parent id |
| description | string | yes |   | description |
| permission | string | yes |   | resource / permission |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### 更新角色规则

* api: `/rule/update`
* method: `PUT`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| id | int | yes |   | 规则id |
| description | string | no |   | description |
| permission | string | no |   | resource / permission |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Remove rule list

* api: `/rule/removeList`
* method: `DELETE`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| ruleIdList | object | yes |   | rule id list |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Remove rule

* api: `/rule/remove`
* method: `DELETE`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| id | int | yes |   | rule id |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## Upload

### File download

* api: `/upload/`
* method: `GET`
* requires authentication: `no`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| filename | string | yes |   | filename |
| type | string | yes |   | file type |

### File upload

* api: `/upload/add`
* method: `POST`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| type | string | no | image | file type |
| targetDir | string | no |   | target directory |
| useTimeDir | string | no | false | use time directory |
| useRandomName | string | no | false | use random filename |
| overwrite | string | no | false | overwrite exist file |
| reizeConfig | object | no |   | reize configuration |
| compressConfig | object | no |   | compress configuration |
| watermarkConfig | object | no |   | watermark configuration |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
		"data": ["..."]
}
```

### Remove file

* api: `/upload/remove`
* method: `DELETE/POST/GET`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| filename | string | yes |   | filename |
| type | string | yes |   | file type |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## Key-Value Pair

### Pair list

* api: `/pair/list`
* method: `POST`
* requires authentication: `yes`
* requires permission: `pair:list`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| currentPage | int | no | 0 | current page |
| pageSize | int | no | 20 | page size |
| pair | object | no |   | pair search attribution |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### Get value

* api: `/pair/getValue`
* method: `POST`
* requires authentication: `yes`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| key | string/object | yes |   | key |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### Add pair

* api: `/pair/add`
* method: `POST`
* requires authentication: `yes`
* requires permission: `pair:add`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| pair | object | yes |   | pair |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

### Update pair

* api: `/pair/update`
* method: `PUT`
* requires authentication: `yes`
* requires permission: `pair:update`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| pair | object | yes |   | pair |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

### Remove pair

* api: `/pair/remove`
* method: `DELETE`
* requires authentication: `yes`
* requires permission: `pair:remove`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| id | int | yes |   | pair id |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## Log

### Log list

* api: `/log/list`
* method: `POST`
* requires authentication: `yes`
* requires permission: `log:list`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| currentPage | int | no | 0 | current page |
| pageSize | int | no | 20 | page size |
| log | object | no |   | log search attribution |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": ["..."]
}
```

### Remove log

* api: `/log/remove`
* method: `DELETE`
* requires authentication: `yes`
* requires permission: `log:remove`

#### Request

| parameter | type | require | default | description |
| --- | --- | --- | --- | --- |
| id | int | yes |   | log id |

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success"
}
```

## Fake Data

### Get fake name

* api: `/fake/getName`
* method: `GET`
* requires authentication: `yes`

#### Succcess response

```json
{
    "errno": 0,
    "msg": "Success",
    "data": "..."
}
```

# Documentation reference

- [Medoo: Database tool](https://medoo.in/doc)
- [Predis: Redis client](https://github.com/predis/predis#connecting-to-redis)
- [JWT](https://lcobucci-jwt.readthedocs.io/en/latest/)
- [Faker: Data fake tool](https://github.com/fzaninotto/Faker#basic-usage)
- [Intervention/image: Handle image](https://image.intervention.io/v2/usage/overview)
- Windows install imagick: https://laksmisetiawati.github.io/articles/php-windows-imagick.html
- php_imagick download: http://pecl.php.net/package/imagick
- ImageMagick download(ImageMagick-xxx-vc15-x64.zip): https://windows.php.net/downloads/pecl/deps/

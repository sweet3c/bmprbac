# Yii2-rbac [![Total Downloads](https://img.shields.io/packagist/dt/bmprbac/yii2-rbac.svg?style=flat-square)](https://packagist.org/packages/bmprbac/yii2-rbac) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)


Yii2-rbac provides a web interface for advanced access control and includes following features:

- Allows CRUD operations for roles and permissions
- Allows to assign multiple roles or permissions to user (done with widget)
- Integrated with [Yii2-user](https://github.com/bmprbac/yii2-user) - flexible user management module

> **NOTE:** Module is in initial development. Anything may change at any time.

## Documentation

[Installation instructions](docs/installation.md) | [Definitive guide to Yii2-rbac](docs/README.md)

## Support

If you have any questions or problems with Yii2-rbac you can ask them using our gitter room:

[![Join the chat at https://gitter.im/bmprbac/yii2-rbac](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/bmprbac/yii2-rbac?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Contributing to this project

Anyone and everyone is welcome to contribute. Please take a moment to
review the [guidelines for contributing](CONTRIBUTING.md).

* [Bug reports](CONTRIBUTING.md#bugs)
* [Feature requests](CONTRIBUTING.md#features)
* [Pull requests](CONTRIBUTING.md#pull-requests)

## License

Yii2-rbac is released under the MIT License. See the bundled [LICENSE](LICENSE) for details.

使用方法：
1.首选需要修改配置文件main.php

在modules添加如下信息：

    'modules' => [
        'rbac' => [
            'class' => 'bmprbac\rbac\Module',
            'rbacCheck' => false, //是否开启RBAC验证
            'allowed' => ['sitelogin', 'siteindex', 'siteerror', 'sitecaptcha'],//始终允许的操作格式为controlleraction
        ],
        'debug' => [
            'class' => 'yii\debug\Module',
        ],
    ],

在components中添加：

    'authManager' => [
        'class' => 'bmprbac\rbac\components\DbManager',
    ],
    
执行根目录下的RBAC.sql建表,如需修改，可自行修改并修改代码。

具体如何使用：


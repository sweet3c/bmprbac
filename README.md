RBAC权限认证
===============================

基于yii2框架和Yii 2 Advanced application.
此验证相对于YII2自带的RBAC验证DbManager进行了扩展，

使用方式：
首先配置文件中需要添加：
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

'components' => [
    'authManager' => [
        'class' => 'bmprbac\rbac\components\DbManager',
    ],
]

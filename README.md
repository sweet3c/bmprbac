=================================================
本版本较yii2自带的RBAC验证多了一些功能，

1.增加自动扫描所有的controller和action的功能，能直接将扫描出来的controller和action写进权限表

2.权限表中的数据能监测出该权限是否依旧有效。即：表中存在权限，但是代码中已经没有

3.可以直接将这个权限通过页面的方式直接赋值给任务，而不必在代码中操作

4.所有的controller只需要继承RbacBaseController即可判断该用户是否有访问这个页面的权限。



================================================
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
1，创建角色

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new models\RbacRole();
        $model->scenario = 'create';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->saveRole()) {
                return $this->redirect(['/rbac/role/view', 'id' => $model->role_id]);
            }
        }
        // 验证失败：$errors 是一个包含错误信息的数组
        //$errors = $model->errors;
        return $this->render('/rbac/role/create', [
            'model' => $model,
        ]);
    }

2.创建任务

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new models\RbacAuthtask();
        $model->scenario = 'create';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->task_id]);
        }
        // 验证失败：$errors 是一个包含错误信息的数组
        //$errors = $model->errors;
        return $this->render('/rbac/authtask/create', [
            'model' => $model,
        ]);
    }
    
3.将任务分配给角色

    /**
     * 授权任务给角色
     * @author lixupeng
     * @param  type $id
     * @throws Exception
     */
    public function actionAssignItems($id)
    {
        $model = self::findModel($id);
        $items = Yii::$app->request->post('authItems');
        if (!is_array($items)) {
            throw new Exception('Invalid request.Params has Error. Please do not repeat this request again.');
        }
        // 安全过滤待授权的项目
        $authItems = models\RbacAuthtask::getAllTask();
        $authItemsKeys = array_keys($authItems);
        $items = array_intersect($items, $authItemsKeys);
        if ($items && models\RbacRoleTask::assignTaskToRole($id, $items)) {
            echo '授权成功';
        } else {
            throw new Exception('授权失败');
        }
    }

4.将用户分配给角色

    /**
     * 角色关联用户
     */
    public function actionRelateUser()
    {
        $roleId = Yii::$app->request->getQueryParam('id');
        if (!preg_match('/^\d+$/', $roleId)) {
            throw new Exception('角色ID不合法');
        }
        $roleModel = $this->findModel($roleId);
        $model = new User();
        $dataProvider = $model->search(Yii::$app->request->queryParams);
        $updateParams = Yii::$app->request->post('selection');
        if ($updateParams) {
            $userIds = $updateParams;
            if ($roleModel->updateRelateUser($userIds, $roleId, $model)) {
                return $this->redirect(['/rbac/role/relate-user', 'id' => $roleModel->role_id]);
            }
        }
        return $this->render('/rbac/role/relateUser', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'roleModel' => $roleModel,
        ]);
    }
    
5.扫描权限，并将扫描出的权限赋给任务

    /*
     * 扫描某个Controller下面的所有public action
     * 并且添加权限
     * @author lixupeng
     * @date 2015-08-28
     */
    public function actionScanAction()
    {
        $params = Yii::$app->request->queryParams;
        $module = isset($params['module']) ? $params['module'] : '';
        $controller = isset($params['controller']) ? $params['controller'] : '';
        $this->validateController($module, $controller);
        $controllerActions = $this->getPublicActions($controller, $module);
        // 已经存在数据库中的action
        $existsActions = models\RbacAuthitems::getExistsControllerAction($module, $controller);
        // 新增的actions
        $newActions = array_diff($controllerActions, $existsActions);
        // 添加新的授权项
        $actions = Yii::$app->request->post('actions');
        if ($actions) {
            $allowed = Yii::$app->request->post('allowed', []);
            if (is_array($allowed)) {
                $allowed = array_intersect($newActions, $allowed);
            }
            // 过滤只能新增的action
            $actions = array_intersect($newActions, $actions);
            if (models\RbacAuthitems::addAuthItems($module, $controller, $actions, $allowed)) {
                $newActions = array_diff($newActions, $actions);
            }
        }
        return $this->render('/rbac/authitems/scanAction', [
            'module' => $module,
            'controller' => $controller,
            'controllerActions' => $newActions,
            'existsActions' => $existsActions,
        ]);
    }

至此所有的操作都已完结。当然也有对应的更新和删除操作，可以自行看代码

===================================================================

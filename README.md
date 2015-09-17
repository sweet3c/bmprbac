***
>本版本较yii2自带的RBAC验证多了一些功能，

* 增加自动扫描所有的controller和action的功能，能直接将扫描出来的controller和action写进权限表

* 权限表中的数据能监测出该权限是否依旧有效。即：表中存在权限，但是代码中已经没有

* 可以直接将这个权限通过页面的方式直接赋值给任务，而不必在代码中操作

* 所有的controller只需要继承RbacBaseController即可判断该用户是否有访问这个页面的权限。

* 可以指定使用那个cache来进行缓存权限数据


***
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

***
#使用方法：
* 首选需要修改配置文件main.php

在modules添加如下信息：
```php
    'modules' => [
        'rbac' => [
            'class' => 'bmprbac\rbac\Module',
            'rbacCheck' => false, //是否开启RBAC验证
            'cacheTypeName' => 'cache', //RBAC使用缓存的名字
            'allowed' => ['sitelogin', 'siteindex', 'siteerror', 'sitecaptcha'],//始终允许的操作格式为controlleraction
        ],
        'debug' => [
            'class' => 'yii\debug\Module',
        ],
    ],
```
在components中添加：
```php
    'authManager' => [
        'class' => 'bmprbac\rbac\components\DbManager',
    ],
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
```    
>**执行根目录下的RBAC.sql建表,如需修改，可自行修改并修改代码。因为RBAC需要和用户关联，所以需要各位在自己项目的model下面建立User的model，或许由于命名空间的不同，RBAC用户的这块或许有问题，修改下命名空间即可。**
>**首先你们需要先创建个BaseController来继承RbacBaseController，
```php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/9/17
 * Time: 15:03
 */

namespace app\common\base;

class BaseController extends \bmprbac\rbac\controllers\RbacBaseController
{

}
```

##具体如何使用：
* **创建角色**
```php    
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
```
* **创建任务**
```php  
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
```    
* **将任务分配给角色**
```php  
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
```
* **将用户分配给角色**
```php  
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
```    
* **扫描权限，并将扫描出的权限赋给任务**
```php  
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
```

***
>至此所有的操作都已完结。当然也有对应的更新和删除操作，可以自行看代码
***
#页面展示：
> **角色一栏**
![角色一栏](http://d.pcs.baidu.com/thumbnail/d9a9bf59c861e50b5aca908a87aeefa4?fid=2820402449-250528-405438316049721&time=1441623600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-88be2AKy3yL8YV5PJymdexseGgk%3D&rt=sh&expires=2h&r=253803765&sharesign=unknown&size=c710_u500&quality=100)
> **角色创建**
![角色创建](http://d.pcs.baidu.com/thumbnail/6f4e8c4590a612bc6c06bb0c27d0b57a?fid=2820402449-250528-990953914431461&time=1441623600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-zmQtU1Fw431nmNxZ79sR9p5w0KA%3D&rt=sh&expires=2h&r=513654154&sharesign=unknown&size=c710_u500&quality=100)
> **角色已关联用户**
![角色已关联用户](http://d.pcs.baidu.com/thumbnail/1d8345e415a44f3bd002d57c458ad02b?fid=2820402449-250528-981852776471822&time=1441623600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-QNgdos3GfvHfnbu%2FBFQPsWBAfBQ%3D&rt=sh&expires=2h&r=881868107&sharesign=unknown&size=c710_u500&quality=100)
> **角色关联用户**
![角色关联用户](http://d.pcs.baidu.com/thumbnail/88e108af983017fdec0cd3555b8ac395?fid=2820402449-250528-670332313679358&time=1441623600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-6hMlO%2FAw7XD5u6e%2BXt8ZVe00sYg%3D&rt=sh&expires=2h&r=980504874&sharesign=unknown&size=c710_u500&quality=100)
> **角色授权**
![角色授权](http://d.pcs.baidu.com/thumbnail/cd3141fe1258db7e46cc111927d63783?fid=2820402449-250528-1039641705995237&time=1441623600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-lH39ZA%2FUZivn%2F6vhahv6dF7k%2F8s%3D&rt=sh&expires=2h&r=723916693&sharesign=unknown&size=c710_u500&quality=100)
***
> **任务一栏**
![任务一栏](http://d.pcs.baidu.com/thumbnail/2eae0e248ef99d9f01c247dd4f3ceffe?fid=2820402449-250528-171433254541332&time=1441674000&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-AgvJ%2BBT85IaL8jREgJAF4u%2BFFQ8%3D&rt=sh&expires=2h&r=194807001&sharesign=unknown&size=c710_u500&quality=100)
> **任务创建**
![任务创建](http://d.pcs.baidu.com/thumbnail/8f99d127bf0a27a25e712ff025727bdc?fid=2820402449-250528-1087351505794643&time=1441677600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-ZJg1vMxeHbagz7nZkXqFsgvhBW4%3D&rt=sh&expires=2h&r=865577511&sharesign=unknown&size=c710_u500&quality=100)
> **任务授权**
![任务授权](http://d.pcs.baidu.com/thumbnail/e8472902beed35d78433822168c14b33?fid=2820402449-250528-475984466930038&time=1441677600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-bAU4xlACyRUDT46TUyGeFJT5I80%3D&rt=sh&expires=2h&r=986248858&sharesign=unknown&size=c710_u500&quality=100)
> **任务关联的角色**
![任务关联的角色](http://d.pcs.baidu.com/thumbnail/9bcbad7561925eb5dec25d27c6f4dc22?fid=2820402449-250528-710623726275174&time=1441677600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-4LP9MvKPiJ5kMRxTHYwJ07RjzAU%3D&rt=sh&expires=2h&r=666504869&sharesign=unknown&size=c710_u500&quality=100)

> **授权项目一栏**
![授权项目一栏](http://d.pcs.baidu.com/thumbnail/54e2afa0f8a4fe08084099f3d3b2ad42?fid=2820402449-250528-666612942975257&time=1441677600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-otnqo1F%2F%2FHWdVploBYfV%2BQW32QE%3D&rt=sh&expires=2h&r=524828415&sharesign=unknown&size=c710_u500&quality=100)
> **自动扫描出来的Controller**
![自动扫描出来的controller](http://d.pcs.baidu.com/thumbnail/a17c83e35e115ac61022a4fef88e1b0b?fid=2820402449-250528-968579193878167&time=1441677600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-5jWmiKJs8Y5FwZotOb6dUtaWs30%3D&rt=sh&expires=2h&r=571658612&sharesign=unknown&size=c710_u500&quality=100)
> **扫描Controller下面的所有public方法**
![扫描Controller下面的所有public方法](http://d.pcs.baidu.com/thumbnail/d514672654739b8dcd31d866be961243?fid=2820402449-250528-1007551648369153&time=1441677600&sign=FDTAER-DCb740ccc5511e5e8fedcff06b081203-HxQ6pH7G22dp7mDduUgISHLCww4%3D&rt=sh&expires=2h&r=655644178&sharesign=unknown&size=c710_u500&quality=100)

***
**NOTICE**当然还有很多别的功能，未详细展示，请自行摸索。

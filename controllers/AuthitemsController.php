<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/22
 * Time: 16:35
 */

namespace bmprbac\rbac\controllers;

use Yii;
use yii\base\Exception;
use yii\console\controllers\HelpController;
use yii\helpers\Inflector;
use yii\web\Controller;
use bmprbac\rbac\models;

class AuthitemsController extends RbacBaseController
{

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->item_name]);
        }

        return $this->render('/rbac/authitems/update', [
            'model' => $model,
        ]);
    }

    /**
     * Lists all Country models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new models\RbacAuthitems();
        $model->scenario = 'search';
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('/rbac/authitems/index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Country model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('/rbac/authitems/view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing Country model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        if ($model->allowed == models\RbacAuthitems::ALLOWED) {
            models\RbacAuthitems::getAllowedAccess(false);
        }
        return $this->redirect(['/rbac/authitems/index']);
    }

    /**
     * Finds the Country model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Country the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = models\RbacAuthitems::findOne($id)) !== null) {
            return $model;
        } else {
            throw new Exception('The requested page does not exist.');
        }
    }

    /**
     * 自动扫描操作权限
     */
    public function actionAutoscan()
    {
        $controllers = models\RbacAuthitems::getAllControllers();
        return $this->render('/rbac/authitems/autoscan', [
            'controllers' => $controllers,
        ]);
    }

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

    /**
     * 删除单个控制器授权项
     * 此action要检查已经纳入控制的action是否有效（无效状态为数据库中存在，但代码中已经不存在)
     * @author lixupeng
     */
    public function actionDeleteAction()
    {
        $params = Yii::$app->request->queryParams;
        $module = isset($params['module']) ? $params['module'] : '';
        $controller = isset($params['controller']) ? $params['controller'] : '';
        $this->validateController($module, $controller);

        // 已经存在的action
        $existsActions = models\RbacAuthitems::getExistsControllerAction($module, $controller);

        $actions = Yii::$app->request->post('actions');
        if ($actions) {
            // 安全过滤出待删除的action 因为已经存在的action的主键name是完整的Controller+Action的地址
            // 页面提交过来的也是完整的name，所以使用已经存在的键值（name）比较合集，安全过滤出真实数据
            $actions = array_intersect($actions, array_keys($existsActions));
            // 然后再通过actions name 来删除关系表中的数据
            if (models\RbacAuthitems::deleteAuthItemByNames($actions)) {
                //刷新总允许运行的权限缓存
                models\RbacAuthitems::getAllowedAccess(false);
            }
            $existsActions = array_diff(array_keys($existsActions), $actions);
        }

        $controllerActions = $this->getPublicActions($controller, $module);
        // 检查已失效项目
        $faieldActions = [];
        if ($existsActions) {
            foreach ($existsActions as $name => $action) {
                if (!in_array($action, $controllerActions)) {
                    $faieldActions[$name] = $action;
                }
            }
        }
        return $this->render('/rbac/authitems/deleteAction', [
            'module' => $module,
            'controller' => $controller,
            'faieldActions' => $faieldActions,
            'existsActions' => $existsActions,
        ]);
    }

    /**
     * 验证提交的控制器是否真实存在
     * @param  string $module
     * @param  string $controller
     * @throws CHttpException
     */
    private function validateController($module, $controller)
    {
        $controllers = models\RbacAuthitems::getAllControllers();
        $controllerAccess = $module ? $module . '@' . $controller : $controller;
        if ($controller == null || !in_array($controllerAccess, $controllers)) {
            throw new Exception('Invalid request. Missing parameter "controller".');
        }
    }

    /*
     * 获取某个controller的所有非静态public方法
     */
    protected function getPublicActions($controller, $module = '')
    {
        if ($module == '') {
            $c = Yii::$app->controllerNamespace . "\\" . basename(str_replace(".php", "", $controller));
        } else {
            $c = "bmprbac\\rbac\controllers\\" . basename(str_replace(".php", "", $controller));
        }
        if (!class_exists($c, false)) {
            include_once $controller;
        }
        $controllerModel = new $c($c, null);
        $help = new HelpController('HelpController', null);
        //获取class的public并且不是static的action名字
        $controllerActions = $help->getActions($controllerModel);
        return $controllerActions;
    }

    /*
     * 检测rbac_authitems表中所有的权限是否失效
     * 即：表中存在但是该权限在代码中不存在
     * @author lixupeng
     * @date 2015-08-29
     */
    public function actionCheckAuthitems()
    {
        $model = new models\RbacAuthitems();
        //检测此表权限的有效性
        $notExistAuthitems = $model->checkAuthitems();
        $actions = Yii::$app->request->post('actions');
        if ($actions) {
            //安全过滤 防止删除掉不应该删掉的权限
            $actions = array_intersect($actions, $notExistAuthitems);
            // 然后再通过actions name 来删除关系表中的数据
            if (models\RbacAuthitems::deleteAuthItemByNames($actions)) {
                //刷新总允许运行的权限缓存
                models\RbacAuthitems::getAllowedAccess(false);
            }
            $notExistAuthitems = array_diff($notExistAuthitems, $actions);
        }
        return $this->render('/rbac/authitems/checkAuthitems', [
            'model' => $model,
            'notExistAuthitems' => $notExistAuthitems,
        ]);

    }

    /**
     * 查找授权项目关联的授权任务
     * @author lixupeng
     * @param string $id 授权项目名
     */
    public function actionRelatedTask($id)
    {
        $authitemsObj = self::findModel($id);

        $model = new models\RbacTaskItems();
        $model->scenario = 'search';
        $dataProvider = $model->search([$model->formName() => ['authitems_name' => $authitemsObj->item_name]]);
        return $this->render('/rbac/authitems/relatedTask', [
            'authitemsObj' => $authitemsObj,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 取消授权任务中的某条授权项目
     * @throws CHttpException
     */
    public function actionUnAssignTask($task_id, $authitems_name)
    {
        if (preg_match('/^\d+$/', $task_id)) {
            models\RbacTaskItems::deleteItemsToTask($task_id, $authitems_name);
        }
        return $this->redirect(['/rbac/authitems/related-task', 'id' => $authitems_name]);
    }

}
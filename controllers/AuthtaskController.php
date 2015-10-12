<?php
/**
 * 授权任务类
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/22
 * Time: 16:35
 */
namespace bmprbac\rbac\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\Inflector;
use yii\web\Controller;
use bmprbac\rbac\models;

class AuthtaskController extends RbacBaseController
{

    public $currentMenu = '/rbac/authtask/index';

    /**
     * Displays a single Country model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('/rbac/authtask/view', [
            'model' => $this->findModel($id),
        ]);
    }

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
            return $this->redirect(['view', 'id' => $model->task_id]);
        }
        // 验证失败：$errors 是一个包含错误信息的数组
        //$errors = $model->errors;
        return $this->render('/rbac/authtask/update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/rbac/authtask/index']);
    }

    /**
     * Lists all Country models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new models\RbacAuthtask();
        $model->scenario = 'search';
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('/rbac/authtask/index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
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
        if (($model = models\RbacAuthtask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new Exception('The requested page does not exist.');
        }
    }

    /**
     * Finds the Country model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Country the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCategoryModel($id)
    {
        if (($model = models\RbacTaskCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new Exception('The requested page does not exist.');
        }
    }

    /**
     * Performs the AJAX validation.
     * @param AuthTask $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'auth-task-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /*
     * 任务分类列表
     * @author  lixupeng
     * @date 2015-08-27
     */
    public function actionCategoryAdmin()
    {
        $model = new models\RbacTaskCategory();
        $model->scenario = 'search';
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('/rbac/authtask/category_admin', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
     * 任务分类创建
     * @author  lixupeng
     * @date 2015-08-27
     */
    public function actionCategoryCreate()
    {
        $model = new models\RbacTaskCategory();
        $model->scenario = 'create';

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['category-admin']);
        }
        // 验证失败：$errors 是一个包含错误信息的数组
        //$errors = $model->errors;
        return $this->render('/rbac/authtask/category_form', [
            'model' => $model,
        ]);
    }

    /*
     * 任务分类更新
     * @author  lixupeng
     * @date 2015-08-27
     */
    public function actionCategoryUpdate($id)
    {
        $model = $this->findCategoryModel($id);
        $model->scenario = 'update';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['category-admin']);
        }
        // 验证失败：$errors 是一个包含错误信息的数组
        //$errors = $model->errors;
        return $this->render('/rbac/authtask/category_form', [
            'model' => $model,
        ]);
    }

    /*
     * 任务分类删除
     * @author  lixupeng
     * @date 2015-08-27
     */
    public function actionCategoryDelete($id)
    {
        $this->findCategoryModel($id)->delete();

        return $this->redirect(['/rbac/authtask/category-admin']);
    }

    /*
     * 任务授权页
     * @author  lixupeng
     * @date 2015-08-27
     */
    public function actionAssign($id)
    {
        $model = self::findModel($id);
        // 已授权的分配项
        $authorized = models\RbacTaskItems::getTaskAuthorized($id);
        //全部的分配项
        $authItems = models\RbacAuthitems::getCanAssignItems();
        // 未授权的就是全部授权项与已授权项的差集
        $unassign = array_diff($authItems, $authorized);
        return $this->render('/rbac/authtask/assign', [
            'model' => $model,
            'unassign' => $unassign,
            'assigned' => $authorized,
        ]);
    }

    /*
     * 任务添加授权项
     * @author  lixupeng
     * @date 2015-08-27
     */
    public function actionAssignItems($id)
    {
        $model = self::findModel($id);
        $items = Yii::$app->request->post('authItems');
        if (!is_array($items)) {
            throw new Exception('Invalid request.Params has Error. Please do not repeat this request again.');
        }
        // 安全过滤待授权的项目
        $authItems = models\RbacAuthitems::getCanAssignItems();
        $items = array_intersect($items, $authItems);
        if ($items && models\RbacTaskItems::assignItemsToTask($id, $items)) {
            echo '授权成功';
        } else {
            throw new Exception('授权失败');
        }
    }

    /*
     * 任务取消授权项
     * @author  lixupeng
     * @date 2015-08-27
     */
    public function actionDeleteAssignItems($id)
    {
        $model = self::findModel($id);
        $items = Yii::$app->request->post('authItems');
        if (!is_array($items)) {
            throw new Exception('Invalid request.Params has Error. Please do not repeat this request again.');
        }
        if ($items && models\RbacTaskItems::deleteItemsToTask($id, $items)) {
            echo '删除授权成功';
        } else {
            throw new Exception('删除授权失败');
        }
    }

    /**
     * 通过授权任务反查授权过的角色
     * @author lixupeng
     * @param int $id 授权任务ID
     */
    public function actionRelatedRole($id)
    {
        $authtaskObj = self::findModel($id);
        $model = new models\RbacRoleTask();
        $model->scenario = 'search';
        $dataProvider = $model->search([$model->formName() => ['task_id' => $authtaskObj->task_id]]);
        return $this->render('/rbac/authtask/relatedRole', [
            'model' => $model,
            'authtaskObj' => $authtaskObj,
            'dataProvider' => $dataProvider
        ]);
    }

    /*
     * @desc 删除任务的关联角色
     * @param $role_id int 角色ID
     * @param $task_id int 任务ID
     *
     */
    public function actionUnAssignRole($role_id, $task_id)
    {
        if (preg_match('/^\d+$/', $task_id) && preg_match('/^\d+$/', $role_id)) {
            models\RbacRoleTask::deleteRoleTask($role_id, $task_id);
        } else {
            throw new Exception('params is not safe!');
        }
        return $this->redirect(['/rbac/authtask/related-role', 'id' => $task_id]);
    }
}

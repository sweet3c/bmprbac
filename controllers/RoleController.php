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
use yii\helpers\Inflector;
use yii\web\Controller;
use bmprbac\rbac\models;
use app\models\user\User;

class RoleController extends RbacBaseController
{

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
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/rbac/role/view', 'id' => $model->role_id]);
        }
        // 验证失败：$errors 是一个包含错误信息的数组
        //$errors = $model->errors;
        return $this->render('/rbac/role/create', [
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
            return $this->redirect(['/rbac/role/view', 'id' => $model->role_id]);
        }

        return $this->render('/rbac/role/update', [
            'model' => $model,
        ]);
    }

    /**
     * Lists all Country models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new models\RbacRole();
        $model->scenario = 'search';
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('/rbac/role/index', [
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
        return $this->render('/rbac/role/view', [
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
        $this->findModel($id)->delete();

        return $this->redirect(['/rbac/role/index']);
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
        if (($model = models\RbacRole::findOne($id)) !== null) {
            return $model;
        } else {
            throw new Exception('The requested page does not exist.');
        }
    }

    /**
     * 角色分配任务
     * 根据任务分类来过滤和显示数据
     *
     * @author lixupeng
     */
    public function actionAssignTask($id)
    {
        $model = self::findModel($id);
        // 先获取授权分类
        $task_categorys = models\RbacTaskCategory::getCategories();

        $searchParams = Yii::$app->request->queryParams;
        $task_category = isset($searchParams['task_category']) ? $searchParams['task_category'] : null;
        //任务列表
        $unassign = $taskItems = models\RbacAuthtask::getTasksByCategory($task_category);

        // 已授权的
        $authorized = models\RbacRoleTask::getTaskAuthorized($id);
        // 从所有授权任务中过滤出已经授权的
        if ($authorized) {
            foreach ($authorized as $k => $v) {
                unset($unassign[$v]);
            }
        }

        $authorized = array_intersect_key($taskItems, array_flip($authorized));
        return $this->render('/rbac/role/assignTask', [
            'model' => $model,
            'unassign' => $unassign,
            'assigned' => $authorized,
            'task_categorys' => $task_categorys,
            'task_category' => $task_category,
        ]);
    }

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

    /**
     * 删除角色的授权任务
     * @author lixupeng
     * @param  type $id
     * @throws Exception
     */
    public function actionDeleteAssignItems($id)
    {
        $model = self::findModel($id);
        $items = Yii::$app->request->post('authItems');
        if (!is_array($items)) {
            throw new Exception('Invalid request.Params has Error. Please do not repeat this request again.');
        }
        if ($items && models\RbacRoleTask::deleteRoleTask($id, $items)) {
            echo '删除授权成功';
        } else {
            throw new Exception('删除授权失败');
        }
    }

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

    /*
     * 已关联用户
     * @param int $id 角色Id
     */
    public function actionRelated($id)
    {
        $model = self::findModel($id);
        $userRoleModel = new models\RbacUserRole();
        $dataProvider = $userRoleModel->search([$userRoleModel->formName() => ['role_id' => $model->role_id]]);
        return $this->render('/rbac/role/relatedUser', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /*
     * @desc 删除用户角色的授权
     * @user_id int 用户Id
     * @role_id int 角色Id
     * @author lixupeng
     * @date 2015-09-01
     */
    public function actionUnAssignUser($user_id, $role_id)
    {
        if (preg_match('/^\d+$/', $user_id) && preg_match('/^\d+$/', $role_id)) {
            //删除用户角色的授权
            if (models\RbacUserRole::deleteUserRoles($user_id, [$role_id])) {
                // 更新用户权限缓存
                models\RbacAuthitems::getUserOperationAuthItems($user_id, false);
            }
        } else {
            throw new Exception('params is not safe!');
        }
        return $this->redirect(['/rbac/role/related', 'id' => $role_id]);
    }

}
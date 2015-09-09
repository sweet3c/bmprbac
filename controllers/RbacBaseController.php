<?php
/**
 * rbac Controller基类。所有Controller应该继承此类
 * @author lixupeng
 *
 */
namespace bmprbac\rbac\controllers;

use bmprbac\rbac\models\RbacAuthitems;

class RbacBaseController extends \yii\web\Controller
{
    public $delimeter = '@';

    public function beforeAction($action)
    {
        //Allow access when srbac is in debug mode
        if (!\Yii::$app->getModule('rbac')->rbacCheck) {
            return true;
        }

        // 先判断是否为module
        $mod = $this->module !== null && $this->module->id != \Yii::$app->id  ? $this->module->id . $this->delimeter : "";
        $access = $mod . ucfirst($this->id) . $this->action->id;
        // 先检查配置文件中的始终允许(modules配置中的$allowed)，再检查数据库设置中的始终允许
        $alwaysAllowed = \Yii::$app->getModule('rbac')->allowed;
        if ($alwaysAllowed) {
            if (in_array($access, $alwaysAllowed)) {
                return true;
            }
        }
        // 检查数据库中存储的始终允许
        $allowed = $this->allowedAccess();
        if (array_key_exists($access, $allowed)) {
            return true;
        }

        // Check for rbac access in RBAC Modules Components/SDbAuthManager
        if (\Yii::$app->user->getIsGuest() || !\Yii::$app->authManager->checkAccess($access)) {
            return $this->onUnauthorizedAccess();
        } else {
            return true;
        }
    }

    /**
     * The auth items that access is always  allowed. Configured in srbac module's
     * configuration
     * @return The always allowed auth items
     */
    protected function allowedAccess()
    {
        return RbacAuthitems::getAllowedAccess();
    }

    protected function onUnauthorizedAccess()
    {
        /**
         *  Check if the unautorizedacces is a result of the user no longer being logged in.
         *  If so, redirect the user to the login page and after login return the user to the page they tried to open.
         *  If not, show the unautorizedacces message.
         */
        if (\Yii::$app->user->getIsGuest()) {
            \Yii::$app->user->loginRequired();
        } else {
            $mod = $this->module != null ? $this->module->id : '';
            $access = $mod . $this->id . $this->action->id;
            if (\Yii::$app->request->getIsAjax()) {
                echo $this->renderContent("Error 403,<br><br>授权失败,您未被授权访问此页面");
            } else {
                echo $this->renderContent("Error 403,<br><br>授权失败,您未被授权访问此页面");
            }

        }
    }
}

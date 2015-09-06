<?php

/*
 * This file is part of the bmprbac project.
 *
 * (c) bmprbac project <http://github.com/bmprbac>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace bmprbac\rbac\components;

use bmprbac\rbac\models\RbacAuthitems;
use yii\db\Query;
use yii\rbac\DbManager as BaseDbManager;

/**
 * This Auth manager changes visibility and signature of some methods from \yii\rbac\DbManager.
 *
 * @author xupeng Li <740942943@qq.com>
 */
class DbManager extends BaseDbManager implements ManagerInterface
{
    /**
     * 检查用户是否有此操作权限
     *
     * @param string the name of the operation that need access check
     * @param mixed the user ID. This should can be either an integer and a string representing
     * the unique identifier of a user. See {@link IWebUser::getId}.
     * @param array name-value pairs that would be passed to biz rules associated
     * with the tasks and roles assigned to the user.
     * @return boolean whether the operations can be performed by the user.
     * @tudo 检查任务的bizrule
     */
    public function checkAccess($itemName, $userId = null, $params = array())
    {
        // 关闭RBAC验证模式时直接返回true
        if (!\Yii::$app->getModule('rbac')->rbacCheck) {
            return true;
        }
        if ($userId == null) {
            $userId = \Yii::$app->user->id; //当前用户
        }
        // 根据用户角色组合权限，判断是否有该权限。权限又分操作权限、数据权限、自定义权限。
        // 检查操作权限 先获取用户所有的操作项权限
        $authItems = RbacAuthitems::getUserOperationAuthItems($userId);
        // 如果授权数组为空或返回false，则返回false
        if (!is_array($authItems)) {
            return false;
        }
        $itemName = strtolower($itemName);
        foreach ($authItems as $k => $item) {
            if (strtolower($k) == $itemName) {
                $itemName = $k;
                break;
            }
        }
        if (isset($authItems[$itemName])) {
            return true;
        }

        return false;
    }

    /**
     * @param  int|null $type If null will return all auth items.
     * @param  array $excludeItems Items that should be excluded from result array.
     * @return array
     * 废弃
     */
    public function getItems($type = null, $excludeItems = [])
    {
        return true;
    }

    /**
     * Returns both roles and permissions assigned to user.
     *
     * @param  integer $userId
     * @return array
     * 废弃
     */
    public function getItemsByUser($userId)
    {
        return true;
    }

    /**
     * @inheritdoc
     * 废弃
     */
    public function getItem($name)
    {
        return parent::getItem($name);
    }
}
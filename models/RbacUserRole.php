<?php

namespace bmprbac\rbac\models;

use app\models\user\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rbac_user_role".
 *
 * @property string $user_id
 * @property string $role_id
 */
class RbacUserRole extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rbac_user_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id'], 'required'],
            [['user_id', 'role_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'role_id' => '角色ID',
        ];
    }

    /**
     * 根据用户ID获取用户的角色ID列表
     * @author lixupeng
     * @param  int $userId
     * @param  boolean $useCache
     * @return array
     */
    public static function getUserRoles($userId, $useCache = true)
    {
        //获取RBAC权限使用的cache名字
        $cacheTypeName = Yii::$app->getModule('rbac')->cacheTypeName;
        $cachekey = 'UserRole_userId_' . $userId;
        $roles = [];
        // 如果使用cache优先从cache读取
        if ($useCache) {
            $roles = Yii::$app->$cacheTypeName->get($cachekey);
        }

        // 如果未使用cache或从cache中未读出值，则到数据库读取，并缓存起来。
        if (!$useCache || $roles === false) {
            $roles = (new \yii\db\Query())
                ->select(['role_id'])
                ->from(self::tableName())
                ->where('user_id = :user_id', [':user_id' => $userId])
                ->column();
        }
        Yii::$app->$cacheTypeName->set($cachekey, $roles);
        $roles = Yii::$app->$cacheTypeName->get($cachekey);
        return $roles;
    }

    /*
     * 连表查询
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id'])->onCondition('bmp_user.user_id is not null');
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find()->innerJoinWith('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pagesize' => 10],
            'sort' => [
                'defaultOrder' => [
                    'user_id' => SORT_DESC
                ]
            ]
        ]);
        $this->load($params);
        $query->andFilterWhere([
            'role_id' => $this->role_id,
        ]);
        return $dataProvider;
    }

    /**
     * 删除用户的角色关系，当$roles为空时，不删除任何角色
     * @author lixupeng
     * @param  int $userId 用户ID
     * @param  array $roles 角色id数组
     * @param  boolean $all 是否删除该用户的全部角色关系，默认false
     * @return number|boolean
     */
    public static function deleteUserRoles($userId, $roles = array(), $all = false)
    {
        if ($all == true) {
            $result = self::deleteAll([
                ['user_id' => ':user_id'],
                [':user_id' => $userId]
            ]);
            if ($result) {
                //更新用户的角色缓存
                self::getUserRoles($userId, false);
            }
            return $result;
        } else {
            if (is_array($roles)) {
                if (empty($roles)) {
                    return true;
                }
                $result = self::deleteAll(
                    ['and', 'user_id = :user_id', ['in', 'role_id', $roles]], [':user_id' => $userId]
                );
                if ($result) {
                    //更新用户的角色缓存
                    self::getUserRoles($userId, false);
                }
                return $result;
            } else {
                return false;
            }
        }
    }
}

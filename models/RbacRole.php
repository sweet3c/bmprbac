<?php

namespace bmprbac\rbac\models;

use app\models\user\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rbac_role".
 *
 * @property string $role_id
 * @property string $role_name
 * @property string $description
 * @property string $parent_id
 * @property integer $status
 * @property string $create_time
 */
class RbacRole extends ActiveRecord
{
    const ROLEVALID = 1;//有效
    const ROLEINVALID = 0;//无效
    public $roleStatusParams = [
        self::ROLEVALID => '有效',
        self::ROLEINVALID => '无效',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rbac_role';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['role_name', 'status', 'description'];
        $scenarios['update'] = ['role_name', 'status', 'description'];
        $scenarios['search'] = ['role_name', 'status'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_name', 'status'], 'required', 'on' => ['create', 'update']],
            [['parent_id', 'status', 'create_time'], 'integer'],
            [['role_name'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 200],
            [['role_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => '角色ID',
            'role_name' => '角色名称',
            'description' => '角色描述',
            'parent_id' => '继承的父级角色ID，拥有父级所有权限',
            'status' => '状态',
            'create_time' => '创建时间',
        ];
    }

    /**
     * @inheritdoc
     * @return RbacRoleQuery the active query used by this AR class.
     */
//    public static function find()
//    {
//        return new RbacRoleQuery(get_called_class());
//    }

    /**
     * @inheritdoc
     * @return RbacRole[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function findOne($condition)
    {
        return static::findByCondition($condition)->one();
    }


    public function beforeValidate()
    {
        parent::beforeValidate();
        if ($this->isNewRecord) {
            $this->create_time = time();
        }
        return true;
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
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pagesize' => 10],
            'sort' => [
                'defaultOrder' => [
                    'role_id' => SORT_DESC
                ]
            ]
        ]);
        $this->load($params);
        $query->andFilterWhere([
            'status' => $this->status,
        ]);
        $query->andFilterWhere(['like', 'role_name', $this->role_name]);
        return $dataProvider;
    }

    /*
     * 角色关联用户相关操作
     * @author lixupeng
     * @date 2015-08-31
     */
    public function updateRelateUser($userIds, $roleId, $model)
    {
        if (is_array($userIds)) {

            $relatedUserList = $model->getUserIdsByRoleId($roleId);
            //获取已关联角色的用户ID和本次选择的用户ID取差集 即为添加的用户Id
            $userAddIds = array_diff($userIds, $relatedUserList);
            if (empty($userAddIds)) {
                return true;
            }
            $values = [];
            foreach ($userAddIds as $k => $item) {
                $temp = [
                    $item,
                    $roleId,
                ];
                array_push($values, $temp);
            }
            $result = \Yii::$app->db->createCommand()->batchInsert(RbacUserRole::tableName(),
                ['user_id', 'role_id'], $values)->execute();
            if ($result) {
                //更新缓存
                foreach ($userAddIds as $userid) {
                    RbacUserRole::getUserRoles($userid, false);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * @author wangyinqia
     */
    public static function getAllRole()
    {
        $results = self::find()
            ->select(['role_id','role_name'])->asArray()
            ->orderBy('role_id ASC')
            ->all();
        $roles = [];
        foreach($results as $value){
            $roles[$value['role_id']] = $value['role_name'];
        }
        return $roles;
    }
    
}

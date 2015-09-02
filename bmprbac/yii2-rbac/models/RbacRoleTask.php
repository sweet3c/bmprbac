<?php

namespace bmprbac\rbac\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rbac_role_task".
 *
 * @property string $role_id
 * @property string $task_id
 */
class RbacRoleTask extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rbac_role_task';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = ['task_id'];
        return $scenarios;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'task_id'], 'required'],
            [['role_id', 'task_id'], 'integer'],
            [
                ['role_id', 'task_id'],
                'unique',
                'targetAttribute' => ['role_id', 'task_id'],
                'message' => 'The combination of 角色ID and rbac任务ID has already been taken.'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id' => '角色ID',
            'task_id' => 'rbac任务ID',
        ];
    }

    /**
     * 该角色下已经分配的任务
     * @author lixupeng
     * @param  string|array $role_id
     * @return array
     */
    public static function getTaskAuthorized($role_id)
    {
        $rows = [];
        if (is_array($role_id)) {
            $role_id_str = implode(",", $role_id);
            $rows = (new \yii\db\Query())
                ->select(['task_id'])
                ->from(self::tableName())
                ->where("role_id  in ($role_id_str)")
                ->column();
        } else {
            $rows = (new \yii\db\Query())
                ->select(['task_id'])
                ->from(self::tableName())
                ->where('role_id = :role_id', [':role_id' => $role_id])
                ->column();
        }
        return $rows;
    }

    /**
     * 授权任务给角色
     *
     * @author lixupeng
     * @param  int $role_id
     * @param  array $items
     * @return boolean
     */
    public static function assignTaskToRole($role_id, $items)
    {
        $values = [];
        foreach ($items as $k => $item) {
            $temp = [
                $role_id,
                $item,
            ];
            array_push($values, $temp);
        }
        $result = \Yii::$app->db->createCommand()->batchInsert(self::tableName(),
            ['role_id', 'task_id'], $values)->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除角色的授权任务
     *
     * @author lixupeng
     * @param  int $role_id
     * @param  string|array $items
     * @return boolean
     */
    public static function deleteRoleTask($role_id, $items)
    {
        return self::deleteAll(
            ['and', 'role_id = :role_id', ['in', 'task_id', $items]], [':role_id' => $role_id]
        );

    }

    /*
     * 连表查询
     */
    public function getRoleModel()
    {
        return $this->hasOne(RbacRole::className(), ['role_id' => 'role_id'])->onCondition('rbac_role.role_id is not null');
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
        $query = self::find()->innerJoinWith('roleModel');

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
            'rbac_role_task.task_id' => $this->task_id,
        ]);
        return $dataProvider;
    }
}

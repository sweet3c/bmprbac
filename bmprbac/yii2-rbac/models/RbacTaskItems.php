<?php

namespace bmprbac\rbac\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rbac_task_items".
 *
 * @property string $task_id
 * @property string $authitems_name
 */
class RbacTaskItems extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rbac_task_items';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = ['authitems_name'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'authitems_name'], 'required'],
            [['task_id'], 'integer'],
            [['authitems_name'], 'string', 'max' => 64],
            [
                ['task_id', 'authitems_name'],
                'unique',
                'targetAttribute' => ['task_id', 'authitems_name'],
                'message' => 'The combination of 任务ID and 操作组合名称 has already been taken.'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'task_id' => '任务ID',
            'authitems_name' => '操作组合名称',
        ];
    }

    /**
     * 获取已分配的授权项目列表
     * @author lixuepeng
     * @param  string $task_id
     * @return array
     */
    public static function getTaskAuthorized($task_id)
    {
        if (is_array($task_id)) {
            $task_id_str = implode(',', $task_id);
            $rows = (new \yii\db\Query())
                ->select(['authitems_name'])
                ->from(self::tableName())
                ->where("task_id in ($task_id_str)")
                ->column();
            return $rows;
        } else {
            $rows = (new \yii\db\Query())
                ->select(['authitems_name'])
                ->from(self::tableName())
                ->where('task_id = :task_id', [':task_id' => $task_id])
                ->column();
            return $rows;
        }
    }

    /**
     * 删除任务的授权项
     * @param int $task_id
     * @param array $items
     */
    public static function deleteItemsToTask($task_id, $items)
    {
        return self::deleteAll(
            ['and', 'task_id = :task_id', ['in', 'authitems_name', $items]], [':task_id' => $task_id]
        );
    }

    /**
     * 授权项目给任务
     * @param int $task_id
     * @param array $items
     */
    public static function assignItemsToTask($task_id, $items)
    {
        $values = [];
        foreach ($items as $k => $item) {
            $temp = [
                $task_id,
                $item,
            ];
            array_push($values, $temp);
        }
        $result = \Yii::$app->db->createCommand()->batchInsert(self::tableName(),
            ['task_id', 'authitems_name'], $values)->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 连表查询
     */
    public function getAuthtask()
    {
        return $this->hasOne(RbacAuthtask::className(), ['task_id' => 'task_id']);
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
        $query = self::find()->with('authtask');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pagesize' => 10],
            'sort' => [
                'defaultOrder' => [
                    'task_id' => SORT_DESC
                ]
            ]
        ]);
        $this->load($params);
        $query->andFilterWhere([
            'authitems_name' => $this->authitems_name,
        ]);
        return $dataProvider;
    }
}

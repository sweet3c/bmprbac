<?php

namespace bmprbac\rbac\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rbac_authtask".
 *
 * @property string $task_id
 * @property string $task_name
 * @property integer $task_category_id
 * @property string $description
 * @property string $bizrule
 * @property string $data
 */
class RbacAuthtask extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rbac_authtask';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['task_name', 'task_category_id', 'description', 'bizrule', 'data'];
        $scenarios['update'] = ['task_name', 'task_category_id', 'description', 'bizrule', 'data'];
        $scenarios['search'] = ['task_name', 'task_category_id'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_name', 'task_category_id'], 'required', 'on' => ['create', 'update']],
            [['task_category_id'], 'integer'],
            [['task_name'], 'string', 'max' => 64],
            [['description', 'bizrule', 'data'], 'string', 'max' => 200],
            [['task_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'task_id' => 'Task ID',
            'task_name' => '任务名称',
            'task_category_id' => '任务分类',
            'description' => '任务描述',
            'bizrule' => '任务规则',
            'data' => '扩展数据',
        ];
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
                    'task_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);
        $query->andFilterWhere([
            'task_category_id' => $this->task_category_id,
        ]);
        $query->andFilterWhere(['like', 'task_name', $this->task_name]);
        return $dataProvider;
    }

    /**
     * 根据分类读取授权任务
     * @author lixupeng
     * @param  int $categoryId
     * @return array
     */
    public static function getTasksByCategory($categoryId)
    {
        if (!is_numeric($categoryId) || !$categoryId) {
            return self::getAllTask();
        }
        $task = [];
        $rows = (new \yii\db\Query())
            ->select(['task_id', 'task_name', 'task_category_id'])
            ->from(self::tableName())
            ->where('task_category_id=:task_category_id', [':task_category_id' => $categoryId])
            ->all();
        if ($rows) {
            foreach ($rows as $k => $v) {
                $task[$v['task_id']] = $v['task_name'];
            }
        }

        return $task;
    }

    /**
     * 获取所有的任务列表
     * @author lixupeng
     * @return array
     */
    public static function getAllTask()
    {
        $task = [];
        $rows = (new \yii\db\Query())
            ->select(['task_id', 'task_name', 'task_category_id'])
            ->from(self::tableName())
            ->all();
        if ($rows) {
            foreach ($rows as $k => $v) {
                $task[$v['task_id']] = $v['task_name'];
            }
        }

        return $task;
    }
}

<?php

namespace bmprbac\rbac\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rbac_task_category".
 *
 * @property string $task_category_id
 * @property string $task_category_name
 */
class RbacTaskCategory extends ActiveRecord
{
    private static $_categories; //任务分类

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rbac_task_category';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['task_category_name'];
        $scenarios['update'] = ['task_category_name'];
        $scenarios['search'] = ['task_category_name'];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_category_name'], 'required', 'on' => ['create', 'update']],
            [['task_category_name'], 'string', 'max' => 50],
            [['task_category_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'task_category_id' => 'ID',
            'task_category_name' => '任务分类',
        ];
    }

    /*
     * 获取任务分类
     */
    public static function getCategories()
    {
        if (self::$_categories !== null) return self::$_categories;
        $categories = [];
        $rows = (new \yii\db\Query())
            ->select(['task_category_id', 'task_category_name'])
            ->from(self::tableName())
            ->all();
        foreach ($rows as $row) {
            $categories[$row['task_category_id']] = $row['task_category_name'];
        }
        self::$_categories = $categories;

        return self::$_categories;
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
                    'task_category_id' => SORT_DESC
                ]
            ]
        ]);
        $this->load($params);
        $query->andFilterWhere(['like', 'task_category_name', $this->task_category_name]);
        return $dataProvider;
    }
}

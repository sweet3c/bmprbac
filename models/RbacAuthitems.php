<?php

namespace bmprbac\rbac\models;

use bmprbac\rbac\controllers\RbacBaseController;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\Controller;

/**
 * This is the model class for table "rbac_authitems".
 *
 * @property string $item_name
 * @property string $module
 * @property string $controller
 * @property string $action
 * @property string $description
 * @property string $type
 * @property integer $allowed
 * @property string $bizrule
 * @property string $data
 */
class RbacAuthitems extends ActiveRecord
{

    public static $types = ['operation' => '操作'];
    const NOTALLOWED = 0; //不允许
    const ALLOWED = 1;  //始终允许

    public $allowTypes = [
        self::NOTALLOWED => '否',
        self::ALLOWED => '是',
    ];
    private static $_allowedAccess;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rbac_authitems';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = [
            'item_name',
            'type',
            'allowed',
            'controller',
            'module',
            'action',
            'description',
            'bizrule',
            'data'
        ];
        $scenarios['update'] = [
            'item_name',
            'type',
            'allowed',
            'controller',
            'module',
            'action',
            'description',
            'bizrule',
            'data'
        ];
        $scenarios['search'] = ['item_name', 'type'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name'], 'required', 'on' => ['create', 'update']],
            [['type'], 'string'],
            [['allowed'], 'integer'],
            [['item_name'], 'string', 'max' => 64],
            [['module', 'controller'], 'string', 'max' => 50],
            [['action', 'description'], 'string', 'max' => 45],
            [['bizrule', 'data'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => '权限名',
            'module' => '模块儿',
            'controller' => '控制器',
            'action' => '权限标识名-操作',
            'description' => '权限说明',
            'type' => '权限类型',
            'allowed' => '总是允许',
            'bizrule' => '权限表达式',
            'data' => '扩展数据',
        ];
    }

    /**
     * 获取所有可授权项目，不包含始终允许的项目
     * @author lixupeng
     */
    public static function getCanAssignItems()
    {
        $rows = (new \yii\db\Query())
            ->select(['item_name'])
            ->from(self::tableName())
            ->where('allowed = :allowed', [':allowed' => 0])
            ->column();
        return $rows;
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
        ]);
        $this->load($params);
        $query->andFilterWhere([
            'type' => $this->type,
        ]);
        $query->andFilterWhere(['like', 'item_name', $this->item_name]);
        return $dataProvider;
    }

    /**
     * Geting all the application's and modules controllers
     * @return array The application's and modules controllers
     */
    public static function getAllControllers()
    {
        $contPath = Yii::$app->getControllerPath();

        $controllers = self::_scanDir($contPath);

        //Scan modules
        $modules = Yii::$app->getModules();
        if (isset($modules['gii'])) {
            unset($modules['gii']);
        }

        if (isset($modules['debug'])) {
            unset($modules['debug']);
        }
        $modControllers = array();
        foreach ($modules as $mod_id => $mod) {
            $moduleControllersPath = Yii::$app->getModule($mod_id)->controllerPath;
            $modControllers = self::_scanDir($moduleControllersPath, $mod_id, "", $modControllers);
        }

        return array_merge($controllers, $modControllers);
    }

    /**
     * 扫描Controller目录或指定子目录
     * @param  string $contPath
     * @param  string $module
     * @param  string $subdir
     * @param  array $controllers
     * @return array
     */
    private static function _scanDir($contPath, $module = "", $subdir = "", $controllers = array())
    {
        $handle = opendir($contPath);
        $del = '@';
        while (($file = readdir($handle)) !== false) {
            $filePath = $contPath . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath)) {
                if (preg_match("/^(.+)Controller.php$/", basename($file))) {
                    if (self::_extendsBaseController($filePath, $module)) {
                        $controllers[] = (($module) ? $module . $del : "") .
                            (($subdir) ? $subdir . "." : "") .
                            str_replace(".php", "", $file);
                    }
                }
            } elseif (is_dir($filePath) && $file != "." && $file != "..") {
                $controllers = self::_scanDir($filePath, $module, $file, $controllers);
            }
        }

        return $controllers;
    }

    /**
     * 检查Controller是否继承于BaseController
     *
     * @author lixupeng
     * @param string $controller
     * @return boolean
     */
    private static function _extendsBaseController($controller, $module)
    {
        if ($module == '') {
            $c = Yii::$app->controllerNamespace . '\\' . basename(str_replace(".php", "", $controller));
        } else {
            $c = "bmprbac\\rbac\controllers\\" . basename(str_replace(".php", "", $controller));
        }
        if (!class_exists($c, false)) {
            include_once $controller;
        }
        $cont = new $c($c, null);
        if ($cont instanceof RbacBaseController) {
            return true;
        }
        return false;
    }

    /**
     * 获取数据库中存储的Controller的action一维数组
     * @param  string $module
     * @param  string $controller
     * @return array
     */
    public static function getExistsControllerAction($module, $controller)
    {
        $actions = [];
        $rows = (new \yii\db\Query())
            ->select(['item_name', 'action'])
            ->from(self::tableName())
            ->where('controller=:controller and module=:module', [':controller' => $controller, 'module' => $module])
            ->all();
        foreach ($rows as $row) {
            $actions[$row['item_name']] = $row['action'];
        }
        return $actions;
    }

    public static function addAuthItems($module, $controller, $actions, $allowed)
    {
        if (is_array($actions) && count($actions)) {
            $values = [];
            foreach ($actions as $k => $action) {
                $temp = [
                    $module ? $module . '@' . str_replace('Controller', '',
                            $controller) . $action : str_replace('Controller', '', $controller) . $action,
                    $module,
                    $controller,
                    $action,
                    'operation',
                    in_array($action, $allowed) ? 1 : 0,
                ];
                array_push($values, $temp);
            }
            $result = \Yii::$app->db->createCommand()->batchInsert(self::tableName(),
                ['item_name', 'module', 'controller', 'action', 'type', 'allowed'], $values)->execute();
            if ($result) {
                // 如果有始终允许的授权项，则更新一下其缓存
                if ($allowed) {
                    self::getAllowedAccess(false);
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 获取用户所有的操作、自定义的授权项，缓存半小时，用于防止缓存被集中请求
     * @param  int $userId
     * @param  boolean $useCache
     * @return array
     */
    public static function getUserOperationAuthItems($userId, $useCache = true)
    {
        //获取RBAC权限使用的cache名字
        $cacheComponents = Yii::$app->getModule('rbac')->cacheComponents;
        $cachekey = 'UserAuthItems_userId_' . $userId;
        $items = [];//所有权限
        // 如果使用cache优先从cache读取
        if ($useCache) {
            $items = Yii::$app->$cacheComponents->get($cachekey);
        }

        // 如果未使用cache或从cache中未读出值，则到数据库读取，并缓存起来。
        if (!$useCache || $items === false) {
            // 先获得用户的所有角色
            $roles = RbacUserRole::getUserRoles($userId, true);
            if (empty($roles)) {
                return $items;
            }
            //获取该用户所有角色的任务
            $taskIds = RbacRoleTask::getTaskAuthorized($roles);
            $authItems = [];////获取任务的所有权限
            if (is_array($taskIds) && count($taskIds) > 0) {
                //获取任务的所有权限
                $authItems = RbacTaskItems::getTaskAuthorized($taskIds);
            }
            foreach ($authItems as $v) {
                $items[$v] = $v;
            }
            Yii::$app->$cacheComponents->set($cachekey, $items, 1800);
        }

        return $items;
    }

    /**
     * 获取永久允许的操作名称列表数组，以item_name做为键值
     * 永久缓存，直到不使用缓存查询时才从数据库读取并缓存起来
     * @param  boolean $useCache
     * @return array
     */
    public static function getAllowedAccess($useCache = true)
    {
        //获取RBAC权限使用的cache名字
        $cacheComponents = Yii::$app->getModule('rbac')->cacheComponents;
        $cachekey = 'allowedAccess';
        // 如果使用cache优先从cache读取
        if ($useCache) {
            if (self::$_allowedAccess === null) {
                $components = Yii::$app->getComponents();
                if (!in_array($cacheComponents, array_keys($components))) {
                    throw new Exception(Yii::t('rbac',
                        "Modules [rbac] used cache, but application not exists cache component.",
                        array('{extension}' => 'rbac')));
                }
                self::$_allowedAccess = Yii::$app->$cacheComponents->get($cachekey);
            }
        }
        // 如果未使用cache或从cache中未读出值，则到数据库读取，并缓存起来。
        if (!$useCache || self::$_allowedAccess === false) {
            $rows = (new \yii\db\Query())
                ->select(['item_name'])
                ->from(self::tableName())
                ->where('allowed = :allowed and type = :type', [':allowed' => self::ALLOWED, ':type' => 'operation'])
                ->column();
            $allowedAccess = array();
            foreach ($rows as $v) {
                $allowedAccess[$v] = $v;
            }
            self::$_allowedAccess = $allowedAccess;
            Yii::$app->$cacheComponents->set($cachekey, $allowedAccess, 0);
        }

        return self::$_allowedAccess;
    }

    /**
     * 删除扫描到的控制器的action(授权项)
     * @param  array $names 授权项name（主键）数组
     * @return integer
     */
    public static function deleteAuthItemByNames($names)
    {
        if (!is_array($names) || empty($names)) {
            return false;
        }

        // 先删除授权任务中的关联数据
        $result = RbacTaskItems::deleteAll(
            ['in', 'authitems_name', $names]
        );
        // 再删除授权项
        return self::deleteAll(
            ['in', 'item_name', $names]
        );
    }

    /*
     * 检测rbac_authitems表的权限是否有效
     * 即数据库中存在但是在代码中却不存在
     */
    public function checkAuthitems()
    {
        $notExistAuthitems = [];
        $rows = (new \yii\db\Query())
            ->select(['item_name', 'controller', 'action', 'module'])
            ->from(self::tableName())
            ->all();
        foreach ($rows as $row) {
            if ($row['module'] == '') {
                $c = Yii::$app->controllerNamespace . '\\' . basename(str_replace(".php", "", $row['controller']));
            } else {
                $c = "bmprbac\\rbac\controllers\\" . basename(str_replace(".php", "", $row['controller']));
            }
            if (!class_exists($c)) {
                array_push($notExistAuthitems, $row['item_name']);
            } else {
                $cont = new $c($c, null);
                if (!method_exists($cont, 'action' . str_replace('-', '', $row['action']))) {
                     array_push($notExistAuthitems, $row['item_name']);
                }
            }

        }
        return $notExistAuthitems;
    }
}

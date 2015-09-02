<?php

/* 
 * This file is part of the bmprbac project
 * 
 * (c) bmprbac project <http://github.com/bmprbac>
 * 
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace bmprbac\rbac\widgets;

use bmprbac\rbac\components\DbManager;
use bmprbac\rbac\models\Assignment;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * This widget may be used in user update form and provides ability to assign
 * multiple auth items to the user.
 * 
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Assignments extends Widget
{
    /** @var integer ID of the user to whom auth items will be assigned. */
    public $userId;
    
    /** @var DbManager */
    protected $manager;
    
    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->manager = Yii::$app->authManager;
        if ($this->userId === null) {
            throw new InvalidConfigException('You should set ' . __CLASS__ . '::$userId');
        }
    }
    
    /** @inheritdoc */
    public function run()
    {
        $model = Yii::createObject([
            'class'   => Assignment::className(),
            'user_id' => $this->userId,
        ]);
        
        if ($model->load(\Yii::$app->request->post())) {
            $model->updateAssignments();
        }
        
        return $this->render('form', [
            'model' => $model,
        ]);
    }
}
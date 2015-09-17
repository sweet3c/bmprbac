<?php

/*
 * This file is part of the bmprbac project.
 *
 * (c) bmprbac project <http://github.com/bmprbac>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace bmprbac\rbac;

use Yii;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

/**
 * @author xupengLi <740942943@qq.com>
 */
class Module extends BaseModule
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'bmprbac\\rbac\controllers';

    /** @var bool Whether to show flash messages */
    public $enableFlashMessages = true;

    /** @var string */
    public $defaultRoute = 'role/index';
    
    /** @var array */
    public $admins = [];

    //默认开启RBAC验证
    public $rbacCheck = true;

    //始终允许访问的权限
    public $allowed = [];

    //RBAC使用缓存的名字
    public $cacheComponents = 'cache';
    
    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
//                        'matchCallback' => function () {
//                            return in_array(Yii::$app->user->identity->user_id, $this->admins);
//                        },
                    ]
                ],
            ],
        ];
    }
}
<?php

/*
 * This file is part of the bmprbac project.
 *
 * (c) bmprbac project <http://github.com/bmprbac>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace bmprbac\rbac\validators;

use yii\validators\Validator;

/**
 * @author xupeng Li <740942943@qq.com>
 */
class RbacValidator extends Validator
{
    /** @var \bmprbac\rbac\components\DbManager */
    protected $manager;
    
    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->manager = \Yii::$app->authManager;
    }
    
    /** @inheritdoc */
    protected function validateValue($value)
    {
        if (!is_array($value)) {
            return [\Yii::t('rbac', 'Invalid value'), []];
        }
        
        foreach ($value as $val) {
            if ($this->manager->getItem($val) == null) {
                return [\Yii::t('rbac', 'There is neither role nor permission with name "{0}"', [$val]), []];
            }
        }
    }
}
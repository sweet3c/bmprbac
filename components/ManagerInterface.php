<?php

/*
 * This file is part of the bmprbac project.
 *
 * (c) bmprbac project <http://github.com/bmprbac>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace bmprbac\rbac\components;

use yii\rbac\ManagerInterface as BaseManagerInterface;

/**
 * @author xupengLi <740942943@qq.com>
 */
interface ManagerInterface extends BaseManagerInterface
{
    /**
     * @param  integer|null $type
     * @param  array        $excludeItems
     * @return mixed
     */
    public function getItems($type = null, $excludeItems = []);

    /**
     * @param  integer $userId
     * @return mixed
     */
    public function getItemsByUser($userId);

    /**
     * @param  string $name
     * @return mixed
     */
    public function getItem($name);
}
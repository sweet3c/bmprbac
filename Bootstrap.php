<?php

/*
 * This file is part of the bmprbac project.
 *
 * (c) bmprbac project <http://github.com/bmprbac/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace bmprbac\rbac;

use bmprbac\rbac\components\DbManager;
use bmprbac\rbac\components\ManagerInterface;
use bmprbac\user\Module as UserModule;
use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Bootstrap class registers translations and needed application components.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{
    /** @inheritdoc */
    public function bootstrap($app)
    {
        // register translations
        $app->get('i18n')->translations['rbac*'] = [
            'class'    => 'yii\i18n\PhpMessageSource',
            'basePath' => __DIR__ . '/messages',
        ];
            
        if ($this->checkRbacModuleInstalled($app)) {
            // register auth manager
            if (!$this->checkAuthManagerConfigured($app)) {
                $app->set('authManager', [
                    'class' => DbManager::className(),
                ]);
            }

            // if bmprbac/user extension is installed, copy admin list from there
            if ($this->checkUserModuleInstalled($app)) {
                $app->getModule('rbac')->admins = $app->getModule('user')->admins;
            }   
        }
    }
    
    /**
     * Verifies that bmprbac/yii2-rbac is installed and configured.
     * @param  Application $app
     * @return bool
     */
    protected function checkRbacModuleInstalled(Application $app)
    {
        return $app->hasModule('rbac') && $app->getModule('rbac') instanceof Module;
    }
    
    /**
     * Verifies that bmprbac/yii2-user is installed and configured.
     * @param  Application $app
     * @return bool
     */
    protected function checkUserModuleInstalled(Application $app)
    {
        return $app->hasModule('user') && $app->getModule('user') instanceof UserModule;
    }
    
    /**
     * Verifies that authManager component is configured.
     * @param  Application $app
     * @return bool
     */
    protected function checkAuthManagerConfigured(Application $app)
    {
        return $app->authManager instanceof ManagerInterface;
    }
}
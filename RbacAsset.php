<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace bmprbac\rbac;

use yii\web\AssetBundle;

/**
 * This declares the asset files required by Gii.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RbacAsset extends AssetBundle
{
    public $sourcePath = '@bmprbac/rbac/assets';
    public $css = [
    ];
    public $js = [
        'rbac.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
//        'yii\bootstrap\BootstrapPluginAsset',
//        'yii\gii\TypeAheadAsset',
    ];
}

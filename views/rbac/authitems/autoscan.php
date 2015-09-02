<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/23
 * Time: 15:30
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '授权项目管理';
$this->params['breadcrumbs'][] = ['label' => '授权项目管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '扫描权限';
?>
<div class="row">
    <div class="col-sm-10">
        <div class="box box-primary">
            <div class="box-body">
                <table class="table grid-view table-hover">
                    <thead>
                    <tr>
                        <th style="width: 200px;">控制器</th>
                        <th style="width: 120px;" class="button-column">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $prevModule = $module = '';
                    foreach ($controllers as $k => $controller) {
                        if (substr_count($controller, '@')) {
                            $showModule = true;
                            list($module, $controller) = explode('@', $controller);
                            if ($module != $prevModule) {
                                ?>
                                <tr class="warning">
                                    <td colspan="2">
                                        <div class="text-center">模块 <?php echo $module ?></div>
                                    </td>
                                </tr>
                                <?php
                                $prevModule = $module;
                            }
                        } ?>
                        <tr>
                            <td width="80%"><?php echo $controller ?></td>
                            <td class="button-column">
                                <a href='<?= Yii::$app->urlManager->createUrl([
                                    '/rbac/authitems/scan-action',
                                    'module' => $module,
                                    'controller' => $controller
                                ]); ?>' id='scanAction' title='扫描控制器操作项' );><i
                                        class="glyphicon glyphicon-search"></i></a>
                                <a href='<?= Yii::$app->urlManager->createUrl([
                                    '/rbac/authitems/delete-action',
                                    'module' => $module,
                                    'controller' => $controller
                                ]); ?>' id='deleteAction' title='删除控制器授权项'><i class="glyphicon glyphicon-trash"></i></a>
                            </td>
                        </tr>
                    <?php
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="col-sm-2">
        <div class="box box-primary">
            <div class="box-body">
                <div class="box-header with-border">
                    <h3 class="box-title">操作列表</h3>
                </div>
                <!-- /.box-header -->
                <ul>
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authitems/check-authitems']); ?>'>检测方法</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>


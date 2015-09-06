<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/23
 * Time: 15:30
 */
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;

$this->title = '授权任务管理';
$this->params['breadcrumbs'][] = ['label' => '授权任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '授权';

$asset = bmprbac\rbac\RbacAsset::register($this);
?>
<div class="box box-info">
    <div class="box-body">
        <div class="col-sm-12">
            <div class="box-body">
                <h3>任务授权：<?= Html::encode($model->task_name) ?></h3>
            </div>
            <div class="col-sm-3" style="margin-top: 20px;">
                <label>已分配的授权项：</label>
                <?= Html::dropDownList('assigned', null, array_combine(array_values($assigned), $assigned),
                    ['multiple' => true, 'id' => 'assigned', 'size' => 30, 'style' => 'width:260px']); ?>
            </div>
            <div class="col-sm-1" style="padding-top: 200px;">
                <p style="text-align: center;">
                    <a href="javascript:void(0);"
                       ref='<?= Yii::$app->urlManager->createUrl([
                           'rbac/authtask/assign-items',
                           'id' => $model->task_id
                       ]); ?>'
                       id='assignItems'><i class="glyphicon glyphicon-arrow-left"></i></a>
                </p>

                <p style="text-align: center;">
                    <a href="javascript:void(0);" ref='<?= Yii::$app->urlManager->createUrl([
                        'rbac/authtask/delete-assign-items',
                        'id' => $model->task_id
                    ]); ?>' id='deleteAssignItems'><i class="glyphicon glyphicon-arrow-right"></i></a>
                </p>

                <div id="assignMessage"></div>
            </div>

            <div class="col-sm-3" style="margin-top: 20px;">
                <label class="control-label">未分配的授权项：</label>
                <?= Html::dropDownList('unassign', null, array_combine(array_values($unassign), $unassign),
                    ['multiple' => true, 'id' => 'unassign', 'size' => 30, 'style' => 'width:260px']); ?>
            </div>
        </div>
    </div>
</div>


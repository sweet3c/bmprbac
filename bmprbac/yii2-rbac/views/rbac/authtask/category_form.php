<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/25
 * Time: 17:21
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '任务分类管理';
$this->params['breadcrumbs'][] = ['label' => '授权任务管理', 'url' => ['category-admin']];
$this->params['breadcrumbs'][] = '创建';
?>
<div class="box box-info">
    <div class="box-body">

        <div class="col-sm-10">

            <?php $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}<div class=\"col-sm-5\">{input}</div>",
                    'labelOptions' => ['class' => 'col-lg-3 control-label'],
                ]
            ]); ?>
            <?= $form->errorSummary($model);?>

            <?= $form->field($model, 'task_category_name')->textInput(['maxlength' => 50]) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? '创建' : '保存',
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
        <div class="col-sm-2">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="box-header with-border">
                        <h3 class="box-title">操作列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <ul>
                        <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/index']); ?>'>任务管理</a></li>
                        <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/create']); ?>'>创建任务</a></li>
                        <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/category-admin']); ?>'>任务分类管理</a></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
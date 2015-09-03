<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/23
 * Time: 16:21
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="col-sm-10">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}<div class=\"col-sm-5\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ]
    ]); ?>
    <?= $form->errorSummary($model);?>

    <?= $form->field($model, 'task_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'task_category_id')->dropDownList(\bmprbac\rbac\models\RbacTaskCategory::getCategories(),
        ['prompt' => '全部类型']); ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => 200]) ?>

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
                <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/category-admin']); ?>'>任务分类管理</a></li>
            </ul>
        </div>
    </div>
</div>

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

    <?= $form->field($model, 'name')->textInput(['maxlength' => 64, 'readonly' => true]) ?>

    <?= $form->field($model, 'controller')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => 45]) ?>

    <?= $form->field($model, 'type')->radioList(\bmprbac\rbac\models\RbacAuthitems::$types) ?>

    <?= $form->field($model, 'allowed')->radioList($model->allowType) ?>

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
                <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authitems/autoscan']); ?>'>扫描权限</a></li>
                <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authitems/check-authitems']); ?>'>检测方法</a></li>
            </ul>
        </div>
    </div>
</div>

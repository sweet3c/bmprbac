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

<div class="col-sm-12">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}<div class=\"col-sm-5\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ]
    ]); ?>
    <?= $form->errorSummary($model);?>

    <?= $form->field($model, 'item_name')->textInput(['maxlength' => 64, 'readonly' => true]) ?>

    <?= $form->field($model, 'controller')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => 45]) ?>

    <?= $form->field($model, 'type')->inline()->radioList(\bmprbac\rbac\models\RbacAuthitems::$types) ?>

    <?= $form->field($model, 'allowed')->inline()->radioList($model->allowTypes) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

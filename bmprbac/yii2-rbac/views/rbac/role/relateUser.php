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

$this->title = '授权角色管理';
$this->params['breadcrumbs'][] = ['label' => '授权角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '关联用户';

?>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <?php
                $form = ActiveForm::begin([
                    'action' => ['', 'id' => $roleModel->role_id],
                    'method' => 'GET',
                    'options' => ['class' => 'form-inline'],
                    'encodeErrorSummary' => false,
                ]);
                ?>
                <table class="table table-bordered table-hover">
                    <tbody>
                    <tr data-key="5">
                        <td>
                            <?= $form->field($model, 'user_code')->textInput(['maxlength' => 10]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?= Html::submitButton('搜索', ['class' => 'btn btn-default', 'name' => 'submit-button']) ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php ActiveForm::end(); ?>
            </div>

        </div>
        <div class="box">
            <div class="box-body">
                <?php
                $form = ActiveForm::begin([
                    'action' => ['', 'id' => $roleModel->role_id],
                    'method' => 'POST',
                    'options' => ['class' => 'form-inline'],
                    'encodeErrorSummary' => false,
                ]);
                ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-bordered table-hover'],
                    'layout' => "{items}<div class='col-sm-5'>{summary}</div><div class='col-sm-7'><div class='dataTables_paginate'>{pager}</div></div>",
                    'columns' => [
                        [
                            'class' => \yii\grid\CheckboxColumn::className(),
                            'checkboxOptions' => function ($dataProvider, $key, $index, $column) {
                                $checked = in_array($dataProvider->user_id, $dataProvider->getUserIdsByUserId(Yii::$app->user->id)) ? true : false;
                                return ['value' => $dataProvider->user_id, 'checked' => $checked];
                             },
                        ],
                        'user_code',
                        'real_name',
                    ],
                ]);
                ?>
            </div>
            <div class="box-body">
                <?php echo Html::button('添加', array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                )) ?>
            </div>
        </div>
    </div>
</div>


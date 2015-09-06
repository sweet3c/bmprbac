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

$asset = bmprbac\rbac\RbacAsset::register($this);
?>
<input type="hidden" id="roleRelatedUserIds" value="<?= implode(',', $model->getUserIdsByRoleId($roleModel->role_id));?>">
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
                <div class="form-group">
                    <?= $form->field($model, 'user_code')
                        ->inline()
                        ->label(false) // 不显示label（input前面的字段名，只使用placeholder来显示字段名）
                        ->error(false) // 不在input下方显示该field的错误信息（显示错误信息会在input下方增加一个显示错误信息的<p></p>）
                        ->textInput(['maxlength' => 10, 'placeholder' => $model->getAttributeLabel('user_code')]); ?>
                </div>

                <?= Html::submitButton('搜索', ['class' => 'btn btn-default', 'item_name' => 'submit-button']) ?>
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
                                return ['value' => $dataProvider->user_id];
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


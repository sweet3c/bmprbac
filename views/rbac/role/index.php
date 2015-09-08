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
$this->params['breadcrumbs'][] = '授权角色管理';

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                <?php
                $form = ActiveForm::begin([
                    'action' => ['/rbac/role/index'],
                    'method' => 'GET',
                    'options' => ['class' => 'form-inline'],
                    'encodeErrorSummary' => false,
                ]);
                ?>
                <div class="form-group">
                    <?= $form->field($model, 'role_name')
                        ->inline()
                        ->label(false) // 不显示label（input前面的字段名，只使用placeholder来显示字段名）
                        ->error(false) // 不在input下方显示该field的错误信息（显示错误信息会在input下方增加一个显示错误信息的<p></p>）
                        ->textInput(['maxlength' => 45, 'placeholder' => $model->getAttributeLabel('role_name')]); ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'status')
                        ->inline()
                        ->label(false)
                        ->error(false)
                        ->dropDownList($model->roleStatusParams); ?>
                </div>
                <?= Html::submitButton('搜索', ['class' => 'btn btn-default', 'item_name' => 'submit-button']) ?>
                <div style="float: right;">
                    <?= Html::a('创建角色', ['create'], ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>

        </div>
        <div class="box">
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-bordered table-hover'],
                    'layout' => "{items}<div class='col-sm-5'>{summary}</div><div class='col-sm-7'><div class='dataTables_paginate'>{pager}</div></div>",
                    'columns' => [
                        'role_id',
                        'role_name',
                        'description',
                        [
                            'label' => '状态',
                            'value' => function ($model) {
                                return isset($model->roleStatusParams[$model->status]) ? $model->roleStatusParams[$model->status] : '';
                            },
                        ],
                        [
                            'label' => '创建时间',
                            'value' => function ($model) {
                                return date('Y-m-d H:i:s', $model->create_time);
                            },
                        ],
                        [
                            'header' => '操作',
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{assign-task} {related} {relate-user} {view} {update} {delete}',
                            'buttons' => [
                                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                                'assign-task' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', '分配任务'),
                                        'aria-label' => Yii::t('yii', '分配任务'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-check"></span>', $url,
                                        $options);
                                },
                                'related' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', '已关联用户'),
                                        'aria-label' => Yii::t('yii', '已关联用户'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-user"></span>', $url,
                                        $options);
                                },
                                'relate-user' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', '关联用户'),
                                        'aria-label' => Yii::t('yii', '关联用户'),
                                        'data-pjax' => '0',
                                        'class' => 'relatedUserBtn',
                                        'ref' => $url,
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, $options);
                                },
                            ],
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>


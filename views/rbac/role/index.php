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
                <table class="table table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td>
                            <?= $form->field($model, 'role_name')->textInput(['maxlength' => 45]); ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'status')->dropDownList($model->roleStatusParams); ?>
                        </td>
                        <td>
                            <?= Html::submitButton('搜索', ['class' => 'btn btn-default', 'name' => 'submit-button']) ?>
                        </td>
                        <td>
                            <?= Html::a('创建角色', ['create'], ['class' => 'btn btn-success']) ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
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
                        'status',
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


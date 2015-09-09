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
$this->params['breadcrumbs'][] = '授权任务管理';
?>
<div class="row">
    <div class="col-sm-10">

        <div class="box box-primary">
            <div class="box-body">
                <?php
                $form = ActiveForm::begin([
                    'action' => ['/rbac/authtask/index'],
                    'method' => 'GET',
                    'options' => ['class' => 'form-inline'],
                    'encodeErrorSummary' => false,
                ]);
                ?>
                <div class="form-group">
                    <?= $form->field($model, 'task_name')
                        ->inline()
                        ->label(false) // 不显示label（input前面的字段名，只使用placeholder来显示字段名）
                        ->error(false) // 不在input下方显示该field的错误信息（显示错误信息会在input下方增加一个显示错误信息的<p></p>）
                        ->textInput(['maxlength' => 64, 'placeholder' => $model->getAttributeLabel('task_name')]); ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'task_category_id')
                        ->inline()
                        ->label(false)
                        ->error(false)
                        ->dropDownList(\bmprbac\rbac\models\RbacTaskCategory::getCategories(),
                            ['prompt' => '全部类型']); ?>
                </div>
                <?= Html::submitButton('搜索', ['class' => 'btn btn-default', 'item_name' => 'submit-button']) ?>
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
                        'task_id',
                        'task_name',
                        [
                            'attribute' => 'task_category_id',
                            'value' => function ($model) {
                                return isset(\bmprbac\rbac\models\RbacTaskCategory::getCategories()[$model->task_category_id]) ? \bmprbac\rbac\models\RbacTaskCategory::getCategories()[$model->task_category_id] : '';
                            },
                        ],
                        'description',
//                        'bizrule',
                        [
                            'header' => '操作',
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{assign} {related-role} {view} {update} {delete}',
                            'buttons' => [
                                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                                'assign' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', '分配权限'),
                                        'aria-label' => Yii::t('yii', '分配权限'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-check"></span>', $url,
                                        $options);
                                },
                                'related-role' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', '已分配的角色'),
                                        'aria-label' => Yii::t('yii', '已分配的角色'),
                                        'data-pjax' => '0',
                                        'class' => 'relatedUserBtn',
                                        'ref' => $url,
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-user"></span>',
                                        $url, $options);
                                },
                            ],
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>

    </div>

    <div class="col-sm-2">
        <div class="box box-primary">
            <div class="box-body">
                <div class="box-header with-border">
                    <h3 class="box-title">操作列表</h3>
                </div><!-- /.box-header -->
                <ul>
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/create']); ?>'>创建任务</a></li>
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/category-admin']); ?>'>任务分类管理</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>


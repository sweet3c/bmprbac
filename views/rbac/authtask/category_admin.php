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
$this->params['breadcrumbs'][] = ['label' => '授权角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '任务分类管理';

?>
<div class="row">
    <div class="col-sm-10">

        <div class="box box-primary">
            <div class="box-body">
                <?php
                $form = ActiveForm::begin([
                    'action' => ['/rbac/authtask/category-admin'],
                    'method' => 'GET',
                    'options' => ['class' => 'form-inline'],
                    'encodeErrorSummary' => false,
                ]);
                ?>
                <div class="form-group">
                    <?= $form->field($model, 'task_category_name')
                        ->inline()
                        ->label(false) // 不显示label（input前面的字段名，只使用placeholder来显示字段名）
                        ->error(false) // 不在input下方显示该field的错误信息（显示错误信息会在input下方增加一个显示错误信息的<p></p>）
                        ->textInput(['maxlength' => 64, 'placeholder' => $model->getAttributeLabel('task_category_name')]); ?>
                </div>
                <?= Html::submitButton('搜索', ['class' => 'btn btn-default', 'item_name' => 'submit-button']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <?= Html::a('创建任务分类', ['category-create'], ['class' => 'btn btn-success']) ?>
            </div>
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-bordered table-hover'],
                    'layout' => "{items}<div class='col-sm-5'>{summary}</div><div class='col-sm-7'><div class='dataTables_paginate'>{pager}</div></div>",
                    'columns' => [
                        'task_category_id',
                        'task_category_name',
                        [
                            'header' => '操作',
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{category-update} {category-delete}',
                            'buttons' => [
                                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                                'category-update' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', 'update'),
                                        'aria-label' => Yii::t('yii', 'update'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,
                                        $options);
                                },
                                'category-delete' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', 'delete'),
                                        'aria-label' => Yii::t('yii', 'delete'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url,
                                        $options);
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
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/index']); ?>'>任务管理</a></li>
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/create']); ?>'>创建任务</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>


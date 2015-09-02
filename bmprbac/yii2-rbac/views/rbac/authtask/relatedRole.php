<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/23
 * Time: 15:30
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

$this->title = '授权任务管理';
$this->params['breadcrumbs'][] = ['label' => '授权任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '授权任务：' . $authtaskObj->task_name . '的关联角色列表';
?>
<div class="row">
    <div class="col-sm-10">
        <div class="box box-primary">
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-bordered table-hover'],
                    'layout' => "{items}<div class='col-sm-5'>{summary}</div><div class='col-sm-7'><div class='dataTables_paginate'>{pager}</div></div>",
                    'columns' => [
                        'role_id',
                        'roleModel.role_name',
                        [
                            'header' => '操作',
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{un-assign-role}',
                            'buttons' => [
                                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                                'un-assign-role' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', '删除对该角色的任务授权'),
                                        'aria-label' => Yii::t('yii', '删除对该角色的任务授权'),
                                        'data-pjax' => '0',
                                        'class' => 'relatedUserBtn',
                                        'ref' => $url,
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
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
                </div>
                <!-- /.box-header -->
                <ul>
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/create']); ?>'>创建任务</a></li>
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/category-admin']); ?>'>任务分类管理</a></li>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


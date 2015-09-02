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

$this->title = '授权项目管理';
$this->params['breadcrumbs'][] = '授权项目管理';

?>
<div class="row">
    <div class="col-sm-10">

        <div class="box box-primary">
            <div class="box-body">
                <?php
                $form = ActiveForm::begin([
                    'action' => ['/rbac/authitems/index'],
                    'method' => 'GET',
                    'options' => ['class' => 'form-inline'],
                    'encodeErrorSummary' => false,
                ]);
                ?>
                <table class="table table-bordered table-hover">
                    <tbody>
                    <tr data-key="5">
                        <td>
                            <?= $form->field($model, 'name')->textInput(['maxlength' => 64]); ?>
                        </td>
                        <td>
                            <?= $form->field($model,
                                'type')->dropDownList(\bmprbac\rbac\models\RbacAuthitems::$types,
                                ['prompt' => '全部权限类型']); ?>
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
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-bordered table-hover'],
                    'layout' => "{items}<div class='col-sm-5'>{summary}</div><div class='col-sm-7'><div class='dataTables_paginate'>{pager}</div></div>",
                    'columns' => [
                        'name',
                        'description',
                        [
                            'label' => '权限类型',
                            'value' => function ($model) {
                                return isset(\bmprbac\rbac\models\RbacAuthitems::$types[$model->type]) ? \bmprbac\rbac\models\RbacAuthitems::$types[$model->type] : '';
                            },
                        ],
                        [
                            'label' => '总是允许',
                            'value' => function ($model) {
                                return isset($model->allowType[$model->allowed]) ? $model->allowType[$model->allowed] : '';
                            },
                        ],
                        [
                            'header' => '操作',
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{related-task} {view} {update} {delete}',
                            'buttons' => [
                                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                                'related-task' => function ($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', '已分配的任务'),
                                        'aria-label' => Yii::t('yii', '已分配的任务'),
                                        'data-pjax' => '0',
                                        'class' => 'relatedUserBtn',
                                        'href' => $url,
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
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authitems/autoscan']); ?>'>扫描权限</a></li>
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authitems/check-authitems']); ?>'>检测方法</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>



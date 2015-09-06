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
$this->params['breadcrumbs'][] = '授权';

$asset = bmprbac\rbac\RbacAsset::register($this);
?>
<div class="box box-info">
    <div class="box-body">
        <div class="col-xs-12">
            <div class="box-body">
                <h3>任务授权：<?= Html::encode($model->role_name) ?></h3>
                <?php
                $form = ActiveForm::begin([
                    'action' => [''],
                    'method' => 'GET',
                    'options' => ['class' => 'form-inline', 'id' => 'assign-task-frm'],
                    'encodeErrorSummary' => false,
                ]);
                ?>
                <input type="hidden" name="id" value="<?= $model->role_id; ?>"
                <table class="table table-bordered table-hover">
                    <tbody>
                    <tr data-key="5">
                        <td>按分类过滤</td>
                        <td>
                            <?= Html::dropDownList('task_category', $task_category, $task_categorys,
                                ['prompt' => '全部分类', 'id' => 'task_categorys']); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-sm-3" style="margin-top: 20px;">
                <label>已分配的授权项：</label>
                <?= Html::dropDownList('assigned', null, $assigned,
                    ['multiple' => true, 'id' => 'assigned', 'size' => 30, 'style' => 'width:260px']); ?>
            </div>
            <div class="col-sm-1" style="padding-top: 200px;text-align: center;">
                <p>
                    <a href="javascript:void(0);"
                       ref='<?= Yii::$app->urlManager->createUrl([
                           'rbac/role/assign-items',
                           'id' => $model->role_id
                       ]); ?>'
                       id='assignItems'><i class="glyphicon glyphicon-arrow-left"></i></a>
                </p>

                <p>
                    <a href="javascript:void(0);" ref='<?= Yii::$app->urlManager->createUrl([
                        'rbac/role/delete-assign-items',
                        'id' => $model->role_id
                    ]); ?>' id='deleteAssignItems'><i class="glyphicon glyphicon-arrow-right"></i></a>
                </p>

                <div id="assignMessage"></div>
            </div>

            <div class="col-sm-4" style="margin-top: 20px;">
                <label class="control-label">未分配的授权项：</label>
                <?= Html::dropDownList('unassign', null, $unassign,
                    ['multiple' => true, 'id' => 'unassign', 'size' => 30, 'style' => 'width:260px']); ?>
            </div>
        </div>
    </div>
</div>


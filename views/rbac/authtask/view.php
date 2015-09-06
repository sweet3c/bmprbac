<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/23
 * Time: 17:06
 */
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '授权任务管理';
$this->params['breadcrumbs'][] = ['label' => '授权任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->task_id;
?>
<div class="box box-info">
    <div class="box-body">
        <div class="col-sm-12">

            <h3><?= Html::encode('查看任务#' . $model->task_id) ?></h3>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'task_id',
                    'task_name',
                    [
                        'label' => '任务分类',
                        'value' => isset(\bmprbac\rbac\models\RbacTaskCategory::getCategories()[$model->task_category_id]) ? \bmprbac\rbac\models\RbacTaskCategory::getCategories()[$model->task_category_id] : '',

                    ],
                    [
                        'label' => '角色描述',
                        'value' => Html::encode($model->description),
                    ],
//                    [
//                        'label' => '任务规则',
//                        'value' => Html::encode($model->bizrule),
//                    ],
                ],
            ]) ?>

            <p>
                <?= Html::a('分配权限', ['assign', 'id' => $model->task_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('编辑', ['update', 'id' => $model->task_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('删除', ['delete', 'id' => $model->task_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '你确定删除此角色吗?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>
    </div>
</div>

<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/23
 * Time: 17:06
 */
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '授权角色管理';
$this->params['breadcrumbs'][] = ['label' => '授权角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->role_id;
?>
<div class="box box-info">
    <div class="box-body">
        <div class="col-sm-12">

            <h3><?= Html::encode('查看角色#' . $model->role_id) ?></h3>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'role_id',
                    'role_name',
                    [
                        'attribute' => 'description',
                        'value' => Html::encode($model->description),
                    ],
                    [
                        'attribute' => 'status',
                        'value' => isset($model->roleStatusParams[$model->status]) ? $model->roleStatusParams[$model->status] : '',
                    ],
                    [
                        'attribute' => 'create_time',
                        'value' => !empty($model->create_time) ? date("Y-m-d H:i:s", $model->create_time) : '',
                    ],
                ],
            ]) ?>

            <p>
                <?= Html::a('编辑', ['update', 'id' => $model->role_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('删除', ['delete', 'id' => $model->role_id], [
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
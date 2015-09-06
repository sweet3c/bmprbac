<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/23
 * Time: 17:06
 */
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = '授权项目管理';
$this->params['breadcrumbs'][] = ['label' => '授权项目管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->item_name;
?>
<div class="box box-info">
    <div class="box-body">
        <div class="col-sm-12">

            <h3><?= Html::encode('查看权限#' . $model->item_name) ?></h3>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'item_name',
                    'module',
                    'controller',
                    'action',
                    'description',
                    [
                        'attribute' => 'type',
                        'value' => isset(\bmprbac\rbac\models\RbacAuthitems::$types[$model->type]) ? \bmprbac\rbac\models\RbacAuthitems::$types[$model->type] : '',
                    ],
                    [
                        'attribute' => 'allowed',
                        'value' => isset($model->allowTypes[$model->allowed]) ? $model->allowTypes[$model->allowed] : '',
                    ],
                    'bizrule',
                    'data',
                ],
            ]) ?>

            <p>
                <?= Html::a(Yii::t('rbac', '编辑'), ['update', 'id' => $model->item_name], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('rbac','删除'), ['delete', 'id' => $model->item_name], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '你确定删除此权限吗?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>
    </div>
</div>

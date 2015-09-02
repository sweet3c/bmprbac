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
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="box box-info">
    <div class="box-body">
        <div class="col-sm-10">

            <h3><?= Html::encode('查看权限#' . $model->name) ?></h3>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    'module',
                    'controller',
                    'action',
                    'description',
                    [
                        'label' => '权限类型',
                        'value' => isset(\bmprbac\rbac\models\RbacAuthitems::$types[$model->type]) ? \bmprbac\rbac\models\RbacAuthitems::$types[$model->type] : '',
                    ],
                    [
                        'label' => '总是允许',
                        'value' => isset($model->allowType[$model->allowed]) ? $model->allowType[$model->allowed] : '',
                    ],
                    'bizrule',
                    'data',
                ],
            ]) ?>

            <p>
                <?= Html::a('编辑', ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('删除', ['delete', 'id' => $model->name], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '你确定删除此权限吗?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>
        <div class="col-sm-2">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="box-header with-border">
                        <h3 class="box-title">操作列表</h3>
                    </div>
                    <!-- /.box-header -->
                    <ul>
                        <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authitems/autoscan']); ?>'>扫描权限</a></li>
                        <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authtask/category-admin']); ?>'>检测方法</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Created by PhpStorm.
 * User: lxp
 * Date: 2015/8/23
 * Time: 16:21
 */
use yii\helpers\Html;

$this->title = '授权任务管理';
$this->params['breadcrumbs'][] = ['label' => '授权任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '创建';
?>
<div class="box box-info">
    <div class="box-body">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>
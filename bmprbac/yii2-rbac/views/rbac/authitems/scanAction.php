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
$this->params['breadcrumbs'][] = ['label' => '授权项目管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $controller . '扫描授权项';

$asset = bmprbac\rbac\RbacAsset::register($this);
?>
<div class="row">
    <div class="col-sm-10">

        <div class="box box-primary">
            <div class="box-body">
                <?php
                $form = ActiveForm::begin([
                    'action' => ['', 'module' => $module, 'controller' => $controller],
                    'method' => 'POST',
                    'options' => ['class' => 'form-inline'],
                    'encodeErrorSummary' => false,
                ]);
                ?>
                <?php if ($controllerActions) { ?>
                    <table class="table table-striped table-bordered table-condensed table-hover" id="actionTable">
                        <thead>
                        <tr>
                            <th style="width:60px;text-align: center">
                                <?= Html::checkbox('check_all', false, ['id' => 'select_all']); ?>
                            </th>
                            <th>Action</th>
                            <th style="width:80px;">状态</th>
                            <th class="button-column" style="width:60px;">始终允许</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($controllerActions as $k => $action) { ?>
                            <tr>
                                <td style="text-align: center">
                                    <?= Html::checkbox('actions[]', false,
                                        ['value' => $action, 'id' => 'action_' . $action]); ?>
                                </td>
                                <td><?php echo Html::label($action, 'action_' . $action) ?></td>
                                <td>授权项</td>
                                <td>
                                    <?= Html::checkbox('allowed[]', false,
                                        ['value' => $action, 'id' => 'allow_' . $action]); ?>
                                </td>
                            </tr>
                        <?php }
                        // 判断是否有失效项，如果有则仅显示
                        ?>
                        </tbody>
                    </table>
                    <?php
                    echo '提示：勾选新增项纳入控制（可同时勾选始终允许），取消勾选已存在项则取消纳入控制。点击保存时已失效项会清除。';
                } else {
                    echo '未发现新增加的授权项，现有授权项已经全部添加到数据集中。';
                }
                ?>
                <?php if ($controllerActions): ?>
                    <div class="form-actions">
                        <?php echo Html::button('保存', array(
                            'type' => 'submit',
                            'class' => 'btn btn-primary'
                        )) ?>
                    </div>
                <?php endif; ?>

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
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authitems/autoscan']); ?>'>扫描权限</a></li>
                    <li><a href='<?= Yii::$app->urlManager->createUrl(['rbac/authitems/check-authitems']); ?>'>检测方法</a></li>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>



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
                <?php if ($existsActions) { ?>
                    <table class="table table-striped table-bordered table-condensed table-hover" id="actionTable">
                        <thead>
                        <tr>
                            <th style="width:60px;text-align: center">
                                <?= Html::checkbox('check_all', false, ['id' => 'select_all']); ?>
                            </th>
                            <th>授权项</th>
                            <th style="width:80px;">状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($existsActions as $k => $action) { ?>
                            <tr>
                                <td style="text-align: center">
                                    <?= Html::checkbox('actions[]', false,
                                        ['value' => $k, 'id' => 'action_' . $action]); ?>
                                </td>
                                <td><?php echo Html::label($action, 'action_' . $action) ?></td>
                                <td><?= in_array($action, $faieldActions) ? '已失效' : '有效'; ?></td>
                            </tr>
                        <?php }
                        // 判断是否有失效项，如果有则仅显示
                        ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    echo '未发现已纳入管理的授权项';
                }
                ?>
                <?php if ($existsActions): ?>
                    <div class="form-actions">
                        <?php echo Html::button('删除', array(
                            'type' => 'submit',
                            'class' => 'btn btn-primary',
                            'onclick' => "if(!confirm('删除后不可恢复，确定要删除这些授权项吗?')) return false;"
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


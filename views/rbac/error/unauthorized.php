<?php
/*
<h2 style="color:red">
<?php echo "Error:".$error["code"]." '".$error["title"]."'" ?></h2>
<p>
<?php echo $error["message"] ?>
</p>
*/

/* @var $this SiteController */
/* @var $error array */

$this->title = Yii::$app->name . ' -Error';
$this->params['breadcrumbs'][] = '授权失败';
?>

<h2>Error <?php echo $error['code']; ?></h2>

<div class="error">
    <?php echo \yii\helpers\Html::encode($error['message']); ?>
</div>

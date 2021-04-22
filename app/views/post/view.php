<?php
/** @var \app\models\Post $model */
?>

<h2><?=\core\Base::encode($model->title)?></h2>
<div class="row">
    <div class="col-md-3">
        <img src="<?=$model->getImage()?>"/>
    </div>
    <div class="col-md-3 muted">
        <span>Обновлено:</span> <?=$model->updated_at?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?=\core\Base::encode($model->content)?>
    </div>
</div>

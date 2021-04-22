<?php
/** @var \app\models\Post[] $models */
?>
<div><a href="/post/edit" class="btn btn-primary">Новый пост</a></div>

<?php if (!empty($models)): ?>
    <?php foreach ($models as $model): ?>
        <div class="post">
            <div class="row">
                <div class="col-md-3"><img width="128" src="<?=$model->getImage()?>"/></div>
                <div class="col-md-3"><?=$model->updated_at?></div>
            </div>
            <div class="row">
                <div class="col-md-6"><?=\core\Base::encode($model->title)?></div>
            </div>
            <div class="row">
                <a href="/post/view?id=<?=$model->id?>">Подробнее</a> |
                <a href="/post/edit?id=<?=$model->id?>">Редактировать</a> |
                <a href="/post/delete?id=<?=$model->id?>">Удалить</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <h3>Пока нет постов</h3>
<?php endif; ?>

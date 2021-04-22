<?php
/** @var \app\models\Post $model */
?>
<h1><?= $model->isNewRecord() ? 'Новый пост' : 'Редактирование'?></h1>
<form method="post" enctype='multipart/form-data' action="/post/edit<?=!$model->isNewRecord() ? '?id=' . $model->id : null?>">
    <div class="form-group">
        <label for="post_title">Заголовок</label>
        <input type="text" class="form-control" id="post_title" name="Post[title]" placeholder="Заголовок" value="<?=$model->title?>">
    </div>
    <div class="form-group">
        <label for="post_image">Изображение</label>
        <input type="file" class="form-control" id="post_image" name="Post[image]" placeholder="Изображение">
    </div>
    <div class="form-group">
        <label for="post_content">Текст</label>
        <textarea class="form-control" id="post_content" name="Post[content]" placeholder="Содержание"><?=$model->content?></textarea>
    </div>

    <button type="submit" class="btn btn-default">Записать</button>
</form>
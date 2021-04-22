<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 18:39
 */

namespace app\models;

use core\ActiveRecord;
use core\Base;
use core\HttpException;

/**
 * Class Post
 * @package app\models
 * @property string $title
 * @property string $content
 * @property string $image
 * @property string $updated_at
 * @property int $id
 */
class Post extends ActiveRecord
{
    const UPLOAD_DIR = '/uploads/';

    /**
     * @inheritdoc
     */
    public static function attributes(): array
    {
        return [
            'id', 'title', 'content', 'updated_at', 'image'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'post';
    }

    /**
     * @return bool
     * @throws HttpException
     */
    public function uploadImage(): bool
    {
        if (!isset($_FILES['Post'])) {
            return true;
        } elseif (!empty($_FILES['Post']['name']['image']) && empty($_FILES['Post']['tmp_name']['image'])) {
            throw new HttpException(400, 'Слишком большой размер изображения!');
        }
        $ext = explode('/', $_FILES['Post']['type']['image']);
        $this->image = md5(basename($_FILES['Post']['name']['image']))
            . '.' . end($ext);
        $uploadedfile = Base::getAlias('@web' . static::UPLOAD_DIR) . $this->image;
        if (move_uploaded_file($_FILES['Post']['tmp_name']['image'], $uploadedfile)) {
            return true;
        }
        $this->image  = null;
        return false;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return Base::getAlias(static::UPLOAD_DIR) . $this->image;
    }
}
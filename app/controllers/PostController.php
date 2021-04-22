<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 18:26
 */

namespace app\controllers;

use app\models\Post;
use core\Controller;

/**
 * Class PostController
 * @package app\controllers
 */
class PostController extends Controller
{
    /**
     * индексная страница постов
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('post/index', [
            'models' => Post::find()
                ->orderBy('Updated_at')->all()
        ]);
    }

    /**
     * @return string
     */
    public function actionEdit(): string
    {
        if ($id = $this->getRequestParam('id')) {
            $model = $this->loadModel($id);
        } else {
            $model = new Post();
        }

        if ($model->load($this->getPostParam('Post')) && $model->uploadImage() && $model->save()) {
            $this->redirect('/post/index');
        }
        return $this->render('post/edit', [
            'model' =>$model
        ]);
    }

    /**
     * @return string
     */
    public function actionView(): string
    {
        if ($id = $this->getRequestParam('id')) {
            return $this->render('post/view', ['model' => $this->loadModel($id)]);
        }
        self::throw400();
        return null;
    }

    /**
     * @return string
     */
    public function actionDelete(): string
    {
        if ($id = $this->getRequestParam('id')) {
            $model = $this->loadModel($id);
            if ($model->delete()) {
                $this->redirect('/post/index');
            }
        }
        self::throw400();
        return null;
    }

    /**
     * @param $id
     * @return Post
     */
    protected function loadModel($id): Post
    {
        $model = Post::findOne(['id' => $id]);
        if (empty($model)) {
            Controller::throw404();
        }
        return $model;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 17:43
 */

namespace core;

/**
 * Class Controller
 * @package core
 */
class Controller extends Component
{
    /**
     * @var string
     */
    public $layout = 'layouts/main';

    /**
     * @param $view
     * @return string
     */
    protected function getViewFullPath($view): string
    {
        return Base::getAlias('@app/views/'. $view . '.php');
    }

    /**
     * формирует контент страницы без лейаута
     * @param string $view
     * @param array $params
     * @return string
     */
    public function renderPartial(string $view, array $params = []): string
    {
        extract($params);
        ob_start();
        require $this->getViewFullPath($view);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * формирует контент страницы с лейаутом
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        $content = $this->renderPartial($view, $params);
        ob_start();
        require $this->getViewFullPath($this->layout);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * возвращает безопасное значение GET параметра
     * @param $param
     * @param null $default
     * @return mixed
     */
    public function getRequestParam($param, $default = null)
    {
        if (!isset($_GET[$param])) {
            return $default;
        }
        return filter_input(INPUT_GET, $param);
    }

    /**
     * возвращает безопасное значение POST параметра
     * @param $param
     * @param null $default
     * @return mixed
     */
    public function getPostParam($param, $default = null)
    {
        if (!isset($_POST[$param])) {
            return $default;
        }
        /**
         * TODO: почему то filter_input не работает с массивами, которые приходят в посте
         * за неименимем времени опущу это
         * return filter_input(INPUT_POST, $param);
         */
        return $_POST[$param];
    }

    /**
     * @throws HttpException
     */
    public static function throw404(): void
    {
        throw new HttpException(404,'Cтраница не найдена!');
    }

    /**
     * @throws HttpException
     */
    public static function throw400(): void
    {
        throw new HttpException(400, 'Неверный запрос!');
    }

    /**
     * @param $uri
     * @return string
     */
    public function redirect($uri)
    {
        header( 'Location: ' . $uri);
        Base::$app->end();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 15:20
 */

namespace core;

/**
 * Class Application
 * @package core
 * @property DbConnection $db
 */
class Application
{
    /**
     * @var array
     */
    protected $components = [];

    /**
     * @var array
     */
    protected $componentsConfig = [];

    /**
     * вялое подобие DI c lazy load
     * @param string $name
     * @return object
     * @throws \Exception
     */
    public function __get(string $name)
    {
        if (!isset($this->components[$name])) {
            if (!isset($this->componentsConfig[$name])) {
                throw new \Exception('Компонент ' . $name . ' не настроен!');
            }
            $this->components[$name] = $this->initComponent($this->componentsConfig[$name]);
        }
        return $this->components[$name];
    }

    /**
     * @param array $config
     */
    public function run(array $config): void
    {
        $this->componentsConfig = $config;
        list($controller, $action) = $this->parseRequest();
        $controller = ucfirst($controller);
        $action = 'action' . ucfirst($action);
        $controllerClass = '\app\controllers\\' . $controller . 'Controller';

        if (!class_exists($controllerClass)) {
            Controller::throw404();;
        }

        $controller = new $controllerClass;
        if (!method_exists($controller, $action)) {
            Controller::throw404();
        }
        echo $controller->$action();
    }

    /**
     * инициализирует компонент
     * @param array $config
     * @return object
     * @throws \Exception
     */
    protected function initComponent(array $config)
    {
        if (!isset($config['class'])) {
            throw new \Exception('Не указан класс компонента!');
        }
        $class = $config['class'];
        unset($config['class']);
        return new $class($config);
    }

    /**
     * определяет через URI контроллер и экшн
     * @return array
     * @throws \HttpException
     */
    protected function parseRequest(): array
    {
        $uri = $_SERVER['REQUEST_URI'];
        $default = ['post', 'index'];

        if (empty($uri)) {
            return $default;
        }
        $uri = explode('?', $uri);
        if (!count($uri)) {
            return $default;
        }
        $uri = $uri[0];
        if ($uri == '/') {
            return $default;
        }
        $uri = explode('/', ltrim($uri,'/'));
        if (count($uri) != 2) {
            Controller::throw404();
        }
        return [
            $uri[0],
            $uri[1]
        ];
    }

    /**
     *
     */
    public function end()
    {
        die;
    }
}
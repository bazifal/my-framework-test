<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 16:07
 */

namespace core;

/**
 * Class Component
 * @package core
 */
class Component
{
    /**
     * Component constructor.
     * @param $config
     */
    public function __construct(array $config = [])
    {
        Base::configure($this, $config);
        $this->onConstruct();
    }

    /**
     * дополнительные действия при создании компонента
     */
    public function onConstruct()
    {
        //TODO: переопределить в дочернем классе
    }
}
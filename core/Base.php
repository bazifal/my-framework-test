<?php
namespace core;

/**
 * Class Base
 * @package core
 */
class Base
{
    /**
     * @var Application
     */
    public static $app;

    /**
     * @var array
     */
    public static $aliases = [
        '@root' => BASE_PATH,
        '@app' => BASE_PATH . '/app',
        '@core' => BASE_PATH . '/core',
        '@web' => BASE_PATH . '/web',
    ];

    /**
     * карта базовых классов
     * @var array
     */
    public static $classMap = [
        'core\ActiveQuery' => '@core/ActiveQuery.php',
        'core\ActiveRecord' => '@core/ActiveRecord.php',
        'core\Application' => '@core/Application.php',
        'core\Component' => '@core/Component.php',
        'core\Controller' => '@core/Controller.php',
        'core\DbConnection' => '@core/DbConnection.php',
        'core\HttpException' => '@core/HttpException.php',
    ];

    /**
     * инициализирует свойства объекта
     * @param $object
     * @param $properties
     * @return mixed
     */
    public static function configure($object, array $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    /**
     * @param $config
     */
    public static function run(array $config):void
    {
        static::$app = new Application();
        static::$app->run($config);
    }

    /**
     * @param $path
     * @return mixed
     */
    public static function getAlias($path)
    {
        return str_replace(array_keys(static::$aliases), array_values(static::$aliases), $path);
    }

    /**
     * @param $string
     * @return string
     */
    public static function encode($string)
    {
        return htmlspecialchars($string);
    }

    /**
     * автоподключение файлов классов по их имени
     * @param $className
     * @throws \Exception
     */
    public static function autoload($className)
    {
        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = static::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = static::getAlias('@' . str_replace('\\', '/', $className) . '.php');
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }

        include($classFile);

        if (!class_exists($className, false)
            && !interface_exists($className, false)
            && !trait_exists($className, false)) {
            throw new \Exception("Не удалось загрузить класс '$className' в файле: $classFile");
        }
    }
}

spl_autoload_register(['\core\Base', 'autoload'], true, true);
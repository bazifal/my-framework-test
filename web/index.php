<?php
define('BASE_PATH', dirname(dirname(__FILE__)));

require(__DIR__ . '/../core/Base.php');
$config = require(__DIR__ . '/../app/config/main.php');
\core\Base::run($config);
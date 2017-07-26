<?php
namespace App\Http\Service;
abstract class BaseService {

    private static $instanceStack = array();

    protected function __construct() {
    }

    /**
     * @return $this
     */
    final public static function getInstance() {
        $class = get_called_class();
        if (!isset(self::$instanceStack[$class])) {
            self::$instanceStack[$class] = new $class();
        }

        return self::$instanceStack[$class];
    }

    final private function __clone() {
    }
}
?>
